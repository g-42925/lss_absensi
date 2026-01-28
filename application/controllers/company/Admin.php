<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller {
    public $email;
    public $session;
    public $form_validation;
    public $upload;
    public $pagination;
    public $other;
    public $menu;
    public $admin;

    public function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/perusahaan/admin_model', 'admin');
    }

    public function index() {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Perusahaan';
        $data['title']      = 'Admin';
        $data['namalabel']  = 'Data '.$data['title'];
        $data['auth']       = authUser();

        $data['datas']      = $this->admin->get_data();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/company/admin/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add($failed) {
        cek_menu_access();
        isCreatable();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Perusahaan';
        $data['title']      = 'Admin';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();
        

        $data['company_id'] = $this->session->userdata('company_id');
        $data['roles']      = $this->other->get_roles($data['company_id']);
        $data['permission'] = $this->other->get_permission();
        $data['failed'] = $failed;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/company/admin/add', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add_proses() {
        cek_menu_access();
        $unama  = $this->input->post('email');
        $companyId = $this->session->userdata('company_id');
        $this->form_validation->set_rules('nama', 'Nama', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('roles', 'Role/Jabatan', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('izin', 'Permission/Izin', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|htmlspecialchars|min_length[4]');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
            redirect('company/admin/add/1');
        } else {
            $query = $this->db->get_where('m_user', ['email_address' => $unama, 'is_del' => 'n'])->num_rows();
            if ($query < 1) {
                $res = $this->admin->add_proses($companyId);
                if ($res==true) {
                    $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil disimpan.</div></div>');
                    redirect('company/admin');
                }
                else{
                    $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                    redirect('company/admin/add/1');
                }
            } 
            else {
                $this->session->set_flashdata('message', '<div class="alert alert-warning p-cg" role="alert">Proses gagal, email <b>"'.$unama.'"</b> ini sudah digunakan.</div>');
                redirect('company/admin/add/1');
            }
        }
    }

    public function edit($id = null) {
        cek_menu_access();
        isEditable();
        if ($id==null) { redirect('company/admin'); }
        $check = $this->db->get_where('m_user', ['user_id' => $id]);
        if ($check->num_rows()==0) { 
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
            redirect('company/admin'); 
        }

        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Perusahaan';
        $data['title']      = 'Admin';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');
        $data['failed'] = filter_var($this->input->get('failed'), FILTER_VALIDATE_BOOLEAN);

        $data['edit']       = $check->row_array();
        $data['roles']      = $this->other->get_roles($companyId);
        $data['permission'] = $this->other->get_permission();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/company/admin/edit', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit_proses($id = null) {
        cek_menu_access();
        $unama  = $this->input->post('email');

        if($id==1 && $this->session->userdata('u_id')!=1){
            redirect('company/admin');
        }
        else{
            if ($id==null) { redirect('company/admin'); }
            $check = $this->db->get_where('m_user', ['user_id' => $id]);
            $rowcheck = $check->row_array();
            if ($check->num_rows()==0) {
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
                redirect('company/admin'); 
            }

            $this->form_validation->set_rules('nama', 'Nama', 'trim|required|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('roles', 'Role/Jabatan', 'trim|required|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('izin', 'Permission/Izin', 'trim|required|xss_clean|htmlspecialchars');

            if ($this->form_validation->run() == false) {
                $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
                redirect('company/admin/edit/'.$id.'?failed=true');
            } 
            else {
                $query = $this->db->get_where('m_user', ['email_address' => $unama, 'is_del' => 'n', 'user_id!=' => $id])->num_rows();
                if ($query < 1) {
                    $res = $this->admin->edit_proses($id,$rowcheck['password']);
                    if($res==true) {
                        redirect('company/admin');
                    }
                    else{
                        $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                        redirect('company/admin/edit/'.$id.'?failed=true');
                    }
                } else {
                    $this->session->set_flashdata('message', '<div class="alert alert-warning p-cg" role="alert">Proses gagal, email <b>"'.$unama.'"</b> ini sudah digunakan.</div>');
                    redirect('company/admin/edit/'.$id.'?failed=true');
                }
            }
        }
    }

    public function hapus($id){
        cek_menu_access();

        if($id==1){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Akun Admin ini tidak bisa dihapus ya.</div></div>');
            redirect('company/admin');
        }
        else{
            $data['auth'] = authUser();

            $res = $this->other->hapus_data('m_user','user_id',$id);
            if ($res==true) {
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil dihapus.</div></div>');
                redirect('company/admin');
            }
            else{
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div></div>');
                redirect('company/admin');
            }
        }
    }

}
