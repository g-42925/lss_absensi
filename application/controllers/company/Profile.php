<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Profile extends MY_Controller {
    public $email;
    public $session;
    public $form_validation;
    public $upload;
    public $pagination;
    public $other;
    public $menu;
    public $profile;

    public $s3;

    public function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/perusahaan/profile_model', 'profile');
        $this->load->library('upload');
        $this->load->model('S3_model','s3');
    }

    public function index() {
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Perusahaan';
        $data['title']      = 'Profil';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();
        $data['company']    = pengaturanSistem();
        
        $params = array($this->session->userdata('company_id'));

        $data['profile'] = $this->db->query("select * from companies where id = ?",$params)->row_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/company/profile/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit($failed) {
        
        isEditable();
        
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Perusahaan';
        $data['title']      = 'Profil';
        $data['auth']       = authUser();
        $data['edit']       = pengaturanSistem();
        $data['failed']     = $failed;
        $params = array($this->session->userdata('company_id'));
        $data['profile'] = $this->db->query("select * from companies where id = ?",$params)->row_array();
        $data['status'] = $this->session->flashdata('success');

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/company/profile/edit', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit_proses() {
        
        $company = pengaturanSistem();

        $companyId = $this->session->userdata('company_id');
				$params = array($this->session->userdata('company_id'));
        $profile = $this->db->query("select * from companies where id = ?",$params)->row_array();

        
				$this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('address', 'Address', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('active', 'Status', 'trim|required|xss_clean|htmlspecialchars');
        
        if(isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK){
          $name = $_FILES['logo']['name'];
          $tmpName = $_FILES['logo']['tmp_name'];
          $type = $_FILES['logo']['type'];
          $fileName = time() . '_' . basename($name);

          $result = $this->s3->upload(
            $fileName,
            $companyId,
            'logo',
            $tmpName,
            $type
          );
          
          $this->db->set([
            'id' => $companyId,
            'company_name' => $this->input->post('name'),
            'address' => $this->input->post('address'),
            'phone' => $this->input->post('phone'),
            'email' => $this->input->post('email'),
            'active' => $this->input->post('active'),
            'logo' => $result,
          ]);

          $this->db->where('id',$companyId);

          $this->db->update('companies');

					$this->session->set_flashdata('success','yes');

          redirect('company/profile');

        }
        else{
          $this->db->set([
            'id' => $companyId,
            'company_name' => $this->input->post('name'),
            'address' => $this->input->post('address'),
            'phone' => $this->input->post('phone'),
            'email' => $this->input->post('email'),
            'active' => $this->input->post('active'),
            'logo' => $profile['logo'],
          ]);

					$this->db->where('id',$companyId);

          $this->db->update('companies');

					$this->session->set_flashdata('success','yes');

          redirect('company/profile');
        }
    }

}
