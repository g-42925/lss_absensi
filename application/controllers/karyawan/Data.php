<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Data extends CI_Controller {

    public $email;
    public $session;
    public $form_validation;
    public $upload;
    public $pagination;
    public $other;
    public $menu;
    public $data;
    public $att;

    public function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/karyawan/data_model', 'data');
        $this->load->model('user/attendance_model', 'att');
    }
    

    public function index() {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Karyawan';
        $data['title']      = 'Data Karyawan';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();
        
        $companyId = $this->session->userdata('company_id');

        $data['datas'] = $this->data->get_data($companyId);

        $divisions = $this->db->query("select * from divisions where company_id = ?",[$companyId])->result_array();

        foreach($data['datas'] as $index => $d){
          $division = $this->db->query("select * from divisions where id = ?",[$d['division_id']])->row_array();
          $data['datas'][$index]['divisi'] = $division['division_name'];
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/karyawan/data/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add($failed) {
        cek_menu_access();
        isCreatable();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Karyawan';
        $data['title']      = 'Data Karyawan';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();
 
        $companyId = $this->session->userdata('company_id');
        $data['roles'] = $this->other->get_roles($companyId);
        $data['permission'] = $this->other->get_permission();
        $data['failed'] = $failed;
        $data['mindate'] = date("Y-m-d", strtotime(date('Y-m-d')." -7 day"));
        $data['maxdate'] = date("Y-m-d", strtotime(date('Y-m-d')." +7 day"));

        $data['divisions'] = $this->db->query("select * from divisions where company_id = ?",[$companyId])->result_array();

        $data['position'] = $this->db->query("select * from position where company_id = ?",[$companyId])->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/karyawan/data/add', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);

    }

    public function add_proses() {
        cek_menu_access();
        $unama  = $this->input->post('idkar');

        $companyId = $this->session->userdata('company_id');

        $this->form_validation->set_rules('idkar', 'ID Karyawan', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('nama', 'Nama', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('nom', 'No WhatsApp', 'trim|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('jeniskelamin', 'Jenis Kelamin', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|htmlspecialchars|min_length[4]');
        $this->form_validation->set_rules('nik','Nik','trim|required|xss_clean|htmlspecialchars|min_length[16]');


        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
            redirect('karyawan/data/add/1');
        } 
        else {
            $nik = $this->input->post('nik');
            $qByNik = $this->db->query("select * from m_pegawai where company_id = ? and nik = ?",[$companyId,$nik])->row_array();
             
            if(!$qByNik){
                $query  = $this->data->add_proses(
                    $this->session->userdata('company_id')
                );
                if($query){
                  redirect('karyawan/data');
                }
                else{
                  $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                  redirect('karyawan/data/add/1');
                }
            }
            else{
                $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Nik sudah digunakan. periksa daftar karyawan atau daftar nonaktif</div>');
                redirect('karyawan/data/add/1');
            }
        }
    }

    public function edit($id = null) {
        cek_menu_access();
        isEditable();
        if ($id==null) { redirect('karyawan/data'); }
        $check = $this->db->get_where('m_pegawai', ['pegawai_id' => $id]);
        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

        if ($check->num_rows()==0) { 
          redirect('karyawan/data'); 
        }

        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Karyawan';
        $data['title']      = 'Data Karyawan';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');

        $data['divisions'] = $this->db->query("select * from divisions where company_id = ?",[$companyId])->result_array();

        $data['position'] = $this->db->query("select * from position where company_id = ?",[$companyId])->result_array();

        $data['edit']       = $check->row_array();
        $data['mindate'] = date("Y-m-d", strtotime(date('Y-m-d')." -7 day"));
        $data['maxdate'] = date("Y-m-d", strtotime(date('Y-m-d')." +7 day"));

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/karyawan/data/edit', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit_proses($id = null) {
        cek_menu_access();
        $unama  = $this->input->post('idkar');

            if ($id==null) { redirect('karyawan/data'); }
            $check = $this->db->get_where('m_pegawai', ['pegawai_id' => $id]);
            $rowcheck = $check->row_array();
            if ($check->num_rows()==0) {
              redirect('karyawan/data'); 
            }

            $this->form_validation->set_rules('idkar', 'ID Karyawan', 'trim|required|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('nama', 'Nama', 'trim|required|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('nom', 'No WhatsApp', 'trim|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('jeniskelamin', 'Jenis Kelamin', 'trim|required|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('password', 'Password', 'trim|xss_clean|htmlspecialchars|min_length[4]');

            if($this->form_validation->run() == false) {
                $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
                redirect('karyawan/data/edit/'.$id.'?failed=true');
            } 
            else {
                $query = $this->db->get_where('m_pegawai', ['id_pegawai' => $unama, 'is_del' => 'n', 'pegawai_id!=' => $id])->num_rows();
                if ($query < 1) {
                    $res = $this->data->edit_proses($id);
                    if($res==true) {
                        redirect('karyawan/data');
                    }
                    else{
                        $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                        redirect('karyawan/data/edit/'.$id.'?failed=true');
                    }
                } else {
                    $this->session->set_flashdata('message', '<div class="alert alert-warning p-cg" role="alert">Proses gagal, id karyawan <b>"'.$unama.'"</b> ini sudah digunakan.</div>');
                    redirect('karyawan/data/edit/'.$id.'?failed=true');
                }
            }

    }

    public function hapus($id){
        cek_menu_access();
        
        $data['auth']       = authUser();
        
        $res = $this->other->hapus_data('m_pegawai','pegawai_id',$id);
        if ($res==true) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil dihapus.</div></div>');
            redirect('karyawan/data');
        }else{
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div></div>');
            redirect('karyawan/data');
        }

    }

}
