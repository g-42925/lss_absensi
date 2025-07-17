<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Roles extends CI_Controller {
    public $email;
    public $session;
    public $form_validation;
    public $upload;
    public $pagination;
    public $other;
    public $menu;
    public $roles;

    public function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/perusahaan/roles_model', 'roles');
    }

    public function index() {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Perusahaan';
        $data['nmenusub']   = 'Jabatan & Izin';
        $data['title']      = 'Jabatan';
        $data['namalabel']  = $data['title'];

        $data['auth']       = authUser();
        $data['datas'] = $this->roles->get_data();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/company/roles/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add() {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Perusahaan';
        $data['nmenusub']   = 'Jabatan & Izin';
        $data['title']      = 'Jabatan';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();
        
        if($data['auth']['tambah']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('company/roles/');
        }
        
        $data['menu'] = $this->menu->getMenu();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/company/roles/add', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add_proses() {
        cek_menu_access();
        $unama  = $this->input->post('nama');

        $this->form_validation->set_rules('nama', 'Nama', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('roles[]', 'Menu', 'trim|xss_clean|htmlspecialchars');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
            redirect('company/roles/add');
        } else {
            $query = $this->db->get_where('m_role', ['nama_role' => $unama, 'is_del' => 'n'])->num_rows();
            if ($query < 1) {
                $res = $this->roles->add_proses();
                if ($res==true) {
                    $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil disimpan.</div></div>');
                    redirect('company/roles');
                }else{
                    $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                    redirect('company/roles/add');
                }
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-warning p-cg" role="alert">Proses gagal, nama <b>"'.$unama.'"</b> ini sudah digunakan.</div>');
                redirect('company/roles/add');
            }
        }
    }

    public function edit($id = null) {
        cek_menu_access();
        if ($id==null) { redirect('user/company/roles'); }
        $check = $this->db->get_where('m_role', ['role_id' => $id]);
        if ($check->num_rows()==0) { 
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
            redirect('company/roles'); 
        }

        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Perusahaan';
        $data['nmenusub']   = 'Jabatan & Izin';
        $data['title']      = 'Jabatan';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();
        
        if($data['auth']['edit']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('company/roles/');
        }

        $data['menu'] = $this->menu->getMenu();
        $data['edit'] = $check->row_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/company/roles/edit', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit_proses($id = null) {
        cek_menu_access();

        if($id==1){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Jabatan Super Admin ini tidak bisa diedit ya.</div></div>');
            redirect('company/roles');
        }else{
            if ($id==null) { redirect('company/roles'); }
            $check = $this->db->get_where('m_role', ['role_id' => $id]);
            if ($check->num_rows()==0) { 
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
                redirect('company/roles'); 
            }
            $unama  = $this->input->post('nama');

            $this->form_validation->set_rules('nama', 'Nama', 'trim|required|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('roles[]', 'Menu', 'trim|xss_clean|htmlspecialchars');

            if ($this->form_validation->run() == false) {
                $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
                redirect('company/roles/edit/'.$id);
            } else {
                $query = $this->db->get_where('m_role', ['nama_role' => $unama, 'is_del' => 'n', 'role_id!=' => $id])->num_rows();
                if ($query < 1) {
                    $res = $this->roles->edit_proses($id);
                    if ($res==true) {
                        $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil disimpan.</div></div>');
                        redirect('company/roles');
                    }else{
                        $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                        redirect('company/roles/edit/'.$id);
                    }
                } else {
                    $this->session->set_flashdata('message', '<div class="alert alert-warning p-cg" role="alert">Proses gagal, nama <b>"'.$this->input->post('nama').'"</b> ini sudah digunakan.</div>');
                    redirect('company/roles/edit/'.$id);
                }
            }
        }
    }

    public function hapus($id){
        cek_menu_access();
        
        if($id==1){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Jabatan Super Admin ini tidak bisa dihapus ya.</div></div>');
            redirect('company/roles');
        }else{
            $data['auth'] = authUser();
            if($data['auth']['hapus']!='y'){
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
                redirect('company/roles/');
            }

            if($id==1){
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">[DEFAULT] - Jabatan ini tidak bisa dihapus.</div></div>');
                redirect('company/roles/');
            }
            
            $res = $this->other->hapus_data('m_role','role_id',$id);
            if ($res==true) {
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil dihapus.</div></div>');
                redirect('company/roles');
            }else{
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div></div>');
                redirect('company/roles');
            }
        }
    }

}
