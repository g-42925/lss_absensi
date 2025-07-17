<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Profile extends CI_Controller {
    public $email;
    public $session;
    public $form_validation;
    public $upload;
    public $pagination;
    public $other;
    public $menu;
    public $profile;

    public function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/perusahaan/profile_model', 'profile');
    }

    public function index() {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Perusahaan';
        $data['title']      = 'Profil';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();
        $data['company']    = pengaturanSistem();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/company/profile/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit() {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Perusahaan';
        $data['title']      = 'Profil';
        $data['auth']       = authUser();
        $data['edit']       = pengaturanSistem();
        
        if($data['auth']['edit']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('company/profile/');
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/company/profile/edit', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit_proses() {
        cek_menu_access();
        $company = pengaturanSistem();
        $this->form_validation->set_rules('nama', 'Nama', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('alamat', 'Alamat', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('nomor', 'Nomor Telepon', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('label_sambutan', 'Label Sambutan', 'trim|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('label_info', 'Label Info', 'trim|xss_clean|htmlspecialchars');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
            redirect('company/profile/edit/');
        } else {
            $ceklogo = $_FILES['gambar']['name'];
            $upload = $this->other->upload_gambar('gambar',$company['logo_perusahaan'],'logo','logo_');
            if($upload['result'] == "success" || $ceklogo==''){
                $res = $this->profile->edit_proses($ceklogo,$upload);
                if ($res==true) {
                    $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil diperbarui.</div></div>');
                    redirect('company/profile');
                }else{
                    unlink(FCPATH.$upload['path'].$upload['file']['file_name']);
                    $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                        redirect('company/profile/edit/');
                }
            }else{
                $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.$upload['error'].'</div>');
                redirect('company/profile/edit/');
            } 
        }
    }

}
