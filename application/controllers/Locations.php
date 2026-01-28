<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Locations extends CI_Controller {
    public $email;
    public $session;
    public $validation;
    public $upload;
    public $pagination;
    public $other;
    public $menu;
    public $lokasi;
    public $form_validation;

    public function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/locations_model', 'lokasi');
    }

    public function index() {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Lokasi Kehadiran';
        $data['title']      = 'Lokasi Kehadiran';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['datas']      = $this->lokasi->get_data(
            $this->session->userdata('company_id')
        );

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/locations/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add() {
        cek_menu_access();
        isCreatable();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Lokasi Kehadiran';
        $data['title']      = 'Lokasi Kehadiran';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['failed'] = filter_var($this->input->get('failed'), FILTER_VALIDATE_BOOLEAN);
        

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/locations/add', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add_proses() {
        cek_menu_access();
        $unama  = $this->input->post('nama');

        $this->form_validation->set_rules('alamat', 'Alamat', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('gl', 'Garis Lintang', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('gb', 'Garis Bujur', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('nama', 'Nama', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('radius', 'Radius', 'trim|required|xss_clean|htmlspecialchars');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
            redirect('locations/add?failed=true');
        } 
        else {
            $query = $this->db->get_where('m_lokasi', ['nama_lokasi' => $unama, 'is_del' => 'n'])->num_rows();
            if ($query < 1) {
                $res = $this->lokasi->add_proses(
                  $this->session->userdata('company_id')
                );
                if ($res==true) {
                    $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil disimpan.</div></div>');
                    redirect('locations');
                }
                else{
                    $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                    redirect('locations/add?failed=true');
                }
            } 
            else {
                $this->session->set_flashdata('message', '<div class="alert alert-warning p-cg" role="alert">Proses gagal, nama lokasi <b>"'.$unama.'"</b> ini sudah digunakan.</div>');
                redirect('locations/add?failed=true');
            }
        }
    }

    public function edit($id = null) {
        cek_menu_access();
        isEditable();
        if ($id==null) { redirect('locations'); }
        $check = $this->db->get_where('m_lokasi', ['lokasi_id' => $id]);
        if ($check->num_rows()==0) { 
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
            redirect('locations'); 
        }

        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Lokasi Kehadiran';
        $data['title']      = 'Lokasi Kehadiran';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['edit']       = $check->row_array();
        $data['failed'] = filter_var($this->input->get('failed'), FILTER_VALIDATE_BOOLEAN);


        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/locations/edit', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit_proses($id = null) {
        cek_menu_access();
        $unama  = $this->input->post('nama');

        if ($id==null) { redirect('locations'); }
        $check = $this->db->get_where('m_lokasi', ['lokasi_id' => $id]);
        $rowcheck = $check->row_array();
        if ($check->num_rows()==0) {
            redirect('locations'); 
        }

        $this->form_validation->set_rules('alamat', 'Alamat', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('gl', 'Garis Lintang', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('gb', 'Garis Bujur', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('nama', 'Nama', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('radius', 'Radius', 'trim|required|xss_clean|htmlspecialchars');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
            redirect('locations/edit/'.$id.'?failed=false');
        } 
        else {
            $query = $this->db->get_where('m_lokasi', ['nama_lokasi' => $unama, 'is_del' => 'n', 'lokasi_id!=' => $id])->num_rows();
            if ($query < 1) {
                $res = $this->lokasi->edit_proses($id);
                if ($res==true) {
                    redirect('locations');
                }else{
                    $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                    redirect('locations/edit/'.$id.'?failed=false');
                }
            } 
            else {
                $this->session->set_flashdata('message', '<div class="alert alert-warning p-cg" role="alert">Proses gagal, nama lokasi <b>"'.$unama.'"</b> ini sudah digunakan.</div>');
                redirect('locations/edit/'.$id.'?failed=true');
            }
        }
    }

    public function hapus($id){
        cek_menu_access();

        $data['auth']       = authUser();

        $num = $this->db->query("SELECT * FROM m_pegawai_lokasi a JOIN m_pegawai b ON a.pegawai_id=b.pegawai_id WHERE a.lokasi_id='$id' AND b.is_del='n'")->num_rows();
        if ($num<=0) {
            $res = $this->other->hapus_data('m_lokasi','lokasi_id',$id);
            if ($res==true) {
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil dihapus.</div></div>');
            }
            else{
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div></div>');
            }
        }
        else{
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-warning p-cg" role="alert">Data tidak bisa dihapus, karna masih ada '.$num.' data karyawan didalamnya.</div></div>');
        }
        redirect('locations');

    }

    public function assign($id = null) {
        cek_menu_access();

        if ($id==null) { redirect('locations'); }
        $check = $this->db->get_where('m_lokasi', ['lokasi_id' => $id]);
        if ($check->num_rows()==0) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
            redirect('locations'); 
        }

        $lokasi = $check->row_array();

        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Lokasi Kehadiran';
        $data['title']      = 'Lokasi Kehadiran';
        $data['namalabel']  = 'Terapkan '.$lokasi['nama_lokasi'];
        $data['auth']       = authUser();
        $data['id']         = $id;

        $data['datas']      = $this->lokasi->get_assign($id);
        $data['karyawan']   = $this->lokasi->get_karyawan($id);
        $data['failed'] = filter_var($this->input->get('failed'), FILTER_VALIDATE_BOOLEAN);


        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/locations/assign', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function assign_proses($id = null) {
        cek_menu_access();
        
        $data['auth']       = authUser();

        if ($id==null) { redirect('locations'); }
        $check = $this->db->get_where('m_lokasi', ['lokasi_id' => $id]);
        if ($check->num_rows()==0) {
            redirect('locations'); 
        }

        $this->form_validation->set_rules('idp[]', 'Karyawan', 'trim|required|xss_clean|htmlspecialchars');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div></div>');
            redirect('locations/assign/'.$id.'?failed=true');
        } 
        else {
            $res = $this->lokasi->assign_proses($id);
            if ($res==true) {
                redirect('locations/assign/'.$id);
            }
            else{
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div></div>');
                redirect('locations/assign/'.$id.'?failed=true');
            }
        }
    }

    public function hapus_assign($id = null, $idx = null){
        cek_menu_access();
        
        $data['auth']       = authUser();

        if ($id==null || $idx==null) { redirect('locations/assign/'.$id); }
        $check = $this->db->get_where('m_pegawai_lokasi', ['pegawai_lokasi_id' => $idx]);
        if ($check->num_rows()==0) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
            redirect('locations/assign/'.$id); 
        }

        $res = $this->db->delete('m_pegawai_lokasi', ['pegawai_lokasi_id' => $idx]);
        if ($res==true) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil dihapus.</div></div>');
            redirect('locations/assign/'.$id);
        }else{
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div></div>');
            redirect('locations/assign/'.$id);
        }
    }

}
