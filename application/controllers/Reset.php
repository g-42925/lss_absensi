<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Reset extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('user/attendance_model', 'att');
    }

    public $att;
    public $email;
    public $session;
    public $form_validation;
    public $upload;
    public $pagination;

    // This function is called by the cron job to update attendance data
    
    public function index() {
      $this->load->view('module/reset/index');
    }

    public function proccess(){

        $password = $this->input->post('password');
        $id = $this->input->post('id');
        $q = $this->db->query("select * from companies where master_account_id = ?",[$id])->row_array();

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

        if($q){
          $this->db->where('company_id',$q['id']);
          
          $data = ['password' => password_hash($password,PASSWORD_BCRYPT)];

          $q2 = $this->db->update('m_user',$data);

          if($q2){
            redirect('auth');
          }
          else{
            $this->session->set_flashdata(
              'message','<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
            );
            redirect(
              'reset?failed=true'
            );
          }
        }
        else{
            $this->session->set_flashdata(
              'message','<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
            );
            redirect(
              'redirect?failed=true'
            );
        }
        
    }   
}
