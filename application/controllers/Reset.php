<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Reset extends CI_Controller {

  public $db;
  public $email;
  public $session;
  public $form_validation;
  public $upload;
  public $pagination;

  public function __construct() {
    parent::__construct();
    $this->load->database();
    $this->load->library('email');
  }

  public function index() {
    $data['htmlclasstemp'] = 'customizer-hide';
    $data['title'] = 'Reset Password';
    $this->load->view('templates/header', $data);
    $this->load->view('module/reset/index', $data);
    $this->load->view('templates/fscript-html-end', $data);
  }
  

  public function proccess($otp_code){
    $this->load->helper('otp');
    $this->load->library('session');
    
    $user_id = $this->session->userdata('u_id');

    if (verify_otp($user_id, $otp_code)){
      echo "OTP Benar";
    }
    else{
      echo "OTP Salah";
    }
  }   
}
