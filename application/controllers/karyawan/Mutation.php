<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mutation extends CI_Controller {

    public $email;
    public $session;
    public $form;
    public $upload;
    public $pagination;
    public $other;
    public $menu;
    public $data;
    public $patterns;
    public $tw;
    public $form_validation;
    


    public function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/karyawan/data_model', 'data');
        $this->load->model('user/patterns_model', 'patterns');
        $this->load->model('user/karyawan/timework_model', 'tw');
    }
    public function index() {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Karyawan';
        $data['title']      = 'Mutation';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

        $data['companies'] = $this->db->query("select * from companies")->result_array();

        $companyId = $this->session->userdata('company_id');

        $data['employees'] = $this->db->query("select * from m_pegawai where company_id = ? and is_del = 'n'",[$companyId])->result_array();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/karyawan/mutation/index',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function proses(){
      $this->db->trans_begin();

      $target = $this->input->post('target');
     
      foreach($this->input->post('idp[]') as $e){
        $data = [
          'premutation_id' => uniqid(),
          'company_id' => $target,
          'employee_id' =>  $e
        ];

        $this->db->set(['is_del' => 'y']);
        $this->db->where('pegawai_id',$e);
        $this->db->update('m_pegawai');

        $this->db->insert(
          'premutation',
          $data
        );
      }


      if($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        $this->session->set_flashdata(
            'message',
            '<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
        );
        redirect('karyawan/mutation?failed=true');
      } 
      else {
        $this->db->trans_commit();
        redirect('karyawan/data');
      }      
    }

    
}
