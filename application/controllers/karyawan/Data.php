<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Data extends CI_Controller {

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

        $data['datas']      = $this->data->get_data();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/karyawan/data/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add() {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Karyawan';
        $data['title']      = 'Data Karyawan';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();
        
        if($data['auth']['tambah']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('karyawan/data/');
        }

        $data['roles']      = $this->other->get_roles();
        $data['permission'] = $this->other->get_permission();

        $data['mindate'] = date("Y-m-d", strtotime(date('Y-m-d')." -7 day"));
        $data['maxdate'] = date("Y-m-d", strtotime(date('Y-m-d')." +7 day"));

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

        $this->form_validation->set_rules('idkar', 'ID Karyawan', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('nama', 'Nama', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('nom', 'No WhatsApp', 'trim|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('jeniskelamin', 'Jenis Kelamin', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('tglmulai', 'Tanggal Mulai', 'trim|required|xss_clean|htmlspecialchars|min_length[6]');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|htmlspecialchars|min_length[4]');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
            redirect('karyawan/data/add');
        } else {
            $query = $this->db->get_where('m_pegawai', ['id_pegawai' => $unama, 'is_del' => 'n'])->num_rows();
            if ($query < 1) {
                $res = $this->data->add_proses();
                if ($res==true) {
                    $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil disimpan.</div></div>');
                    redirect('karyawan/data');
                }else{
                    $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                    redirect('karyawan/data/add');
                }
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-warning p-cg" role="alert">Proses gagal, id karyawan <b>"'.$unama.'"</b> ini sudah digunakan.</div>');
                redirect('karyawan/data/add');
            }
        }
    }

    public function edit($id = null) {
        cek_menu_access();
        if ($id==null) { redirect('karyawan/data'); }
        $check = $this->db->get_where('m_pegawai', ['pegawai_id' => $id]);
        if ($check->num_rows()==0) { 
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
            redirect('karyawan/data'); 
        }

        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Karyawan';
        $data['title']      = 'Data Karyawan';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();
        
        if($data['auth']['edit']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('karyawan/data/');
        }

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
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
                redirect('karyawan/data'); 
            }

            $this->form_validation->set_rules('idkar', 'ID Karyawan', 'trim|required|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('nama', 'Nama', 'trim|required|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('nom', 'No WhatsApp', 'trim|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('jeniskelamin', 'Jenis Kelamin', 'trim|required|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('tglmulai', 'Tanggal Mulai', 'trim|required|xss_clean|htmlspecialchars|min_length[6]');
            $this->form_validation->set_rules('password', 'Password', 'trim|xss_clean|htmlspecialchars|min_length[4]');

            if ($this->form_validation->run() == false) {
                $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
                redirect('karyawan/data/edit/'.$id);
            } else {
                $query = $this->db->get_where('m_pegawai', ['id_pegawai' => $unama, 'is_del' => 'n', 'pegawai_id!=' => $id])->num_rows();
                if ($query < 1) {
                    $res = $this->data->edit_proses($id);
                    if ($res==true) {
                        $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil disimpan.</div></div>');
                        redirect('karyawan/data');
                    }else{
                        $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                        redirect('karyawan/data/edit/'.$id);
                    }
                } else {
                    $this->session->set_flashdata('message', '<div class="alert alert-warning p-cg" role="alert">Proses gagal, id karyawan <b>"'.$unama.'"</b> ini sudah digunakan.</div>');
                    redirect('karyawan/data/edit/'.$id);
                }
            }

    }

    public function hapus($id){
        cek_menu_access();
        
        $data['auth']       = authUser();
        if($data['auth']['hapus']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('karyawan/data/');
        }
            
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
