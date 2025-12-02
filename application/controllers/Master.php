<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Master extends CI_Controller {
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
      $this->load->view('module/master/index');
    }

    public function proccess(){

        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $id = $this->input->post('id');
        $q = $this->db->query("select * from companies where master_account_id = ?",[$id])->row_array();

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

        if($q){
          $data = [
            'company_id' => $q['id'],
            'role_id' => 1,
            'permission_id' => 1,
            'nama_lengkap' => $q['company_name'],
            'email_address' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'is_status' => 'y',
            'is_del' => 'n',
            'created_at' => date('Y-m-d H:i:s')
          ];

          $q2 = $this->db->insert(
            'm_user',$data
          );

          if($q2){
            redirect('auth');
          }
          else{
            $this->session->set_flashdata(
              'message','<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
            );
            redirect(
              'master?failed=true'
            );
          }
        }
        else{
            $this->session->set_flashdata(
              'message','<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
            );
            redirect(
              'master?failed=true'
            );
        }
        
    }   
}
