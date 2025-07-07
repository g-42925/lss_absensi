<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Permission extends CI_Controller {

    public function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/perusahaan/permission_model', 'izin');
    }

    public function index() {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Perusahaan';
        $data['nmenusub']   = 'Jabatan & Izin';
        $data['title']      = 'Izin';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['datas']      = $this->izin->get_data();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/company/permission/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function action($id) {
        cek_menu_access();
        if ($id=='baru') {
            $this->load->view('module/company/permission/add');
        }else{
            $data['edit'] = $this->db->get_where('m_permission', ['permission_id' => $id])->row_array();
            $this->load->view('module/company/permission/edit', $data);
        }
    }

    public function add_proses() {
        cek_menu_access();
        
        $data['auth'] = authUser();
        if($data['auth']['tambah']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('company/permission/');
        }
        
        $unama  = $this->input->post('nama');
        $this->form_validation->set_rules('nama', 'Nama', 'trim|required|xss_clean|htmlspecialchars');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
            redirect('company/permission/add');
        } else {
            $query = $this->db->get_where('m_permission', ['nama_permission' => $unama, 'is_del' => 'n'])->num_rows();
            if ($query < 1) {
                $res = $this->izin->add_proses();
                if ($res==true) {
                    $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil disimpan.</div></div>');
                    redirect('company/permission');
                }else{
                    $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                    redirect('company/permission');
                }
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-warning p-cg" role="alert">Proses gagal, nama <b>"'.$unama.'"</b> ini sudah digunakan.</div>');
                redirect('company/permission');
            }
        }
    }

    public function edit_proses($id = null) {
        cek_menu_access();
        
        if($id==1){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">ID Default tidak bisa diedit ya.</div></div>');
            redirect('company/permission');
        }else{
            $data['auth'] = authUser();
            if($data['auth']['edit']!='y'){
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
                redirect('company/permission/');
            }
            
            $unama  = $this->input->post('nama');
            $this->form_validation->set_rules('nama', 'Nama', 'trim|required|xss_clean|htmlspecialchars');

            if ($this->form_validation->run() == false) {
                $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
                redirect('company/permission');
            } else {
                $query = $this->db->get_where('m_permission', ['nama_permission' => $unama, 'is_del' => 'n', 'permission_id!=' => $id])->num_rows();
                if ($query < 1) {
                    $res = $this->izin->edit_proses($id);
                    if ($res==true) {
                        $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil disimpan.</div></div>');
                        redirect('company/permission');
                    }else{
                        $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                        redirect('company/permission');
                    }
                } else {
                    $this->session->set_flashdata('message', '<div class="alert alert-warning p-cg" role="alert">Proses gagal, nama <b>"'.$unama.'"</b> ini sudah digunakan.</div>');
                    redirect('company/permission');
                }
            }
        }
    }

    public function hapus($id){
        cek_menu_access();
        if($id==1){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">ID Default tidak bisa dihapus ya.</div></div>');
            redirect('company/permission');
        }else{
            $data['auth'] = authUser();
            if($data['auth']['hapus']!='y'){
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
                redirect('company/permission/');
            }

            if($id==1){
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">[DEFAULT] - Jabatan ini tidak bisa dihapus.</div></div>');
                redirect('company/permission/');
            }
            
            $res = $this->other->hapus_data('m_permission','permission_id',$id);
            if ($res==true) {
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil dihapus.</div></div>');
                redirect('company/permission');
            }else{
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div></div>');
                redirect('company/permission');
            }
        }
    }

}
