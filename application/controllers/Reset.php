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
    $this->load->library('session');
    $this->load->helper('otp');
  }

  public function index() {
    $data['htmlpagejs'] = '';
    $data['htmlclasstemp'] = 'customizer-hide';
    $data['title'] = 'Reset Username dan Password';
    
    $this->load->view('templates/header', $data);
    $this->load->view('module/reset/index', $data);
    $this->load->view('templates/fscript-html-end', $data);
  }
  
  public function send_otp() {
    $username = $this->input->post('username');
    
    if (empty($username)) {
        echo json_encode(['status' => 'error', 'message' => 'Username (Email) wajib diisi.']);
        return;
    }
    
    $user = $this->db->get_where('m_user', ['email_address' => $username])->row_array();
    
    if ($user) {
        $token = get_otp($user['user_id']);
        $email = $user['email_address'];
        
        // Setup email (bisa disesuaikan dengan konfigurasi SMTP sesungguhnya)
        $this->email->from('no-reply@leryn.com', 'Leryn Absensi');
        $this->email->to($email);
        $this->email->subject('OTP Reset Username & Password');
        $this->email->message('Kode OTP Anda untuk reset akun adalah: ' . $token);
        $this->email->send();

        if (ob_get_length()) ob_clean(); // Ensure no PHP warnings/notices break JSON
        echo json_encode(['status' => 'success', 'message' => 'Permintaan OTP telah dikirim ke email terdaftar (' . $email . ').']);
    } else {
        if (ob_get_length()) ob_clean();
        echo json_encode(['status' => 'error', 'message' => 'Username tidak ditemukan di sistem.']);
    }
  }

  public function proccess(){
    $username = $this->input->post('username');
    $otp_code = $this->input->post('otp');
    $new_username = $this->input->post('new_username');
    $new_password = $this->input->post('new_password');

    if (empty($username) || empty($otp_code)) {
        $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Username dan OTP wajib diisi.</div>');
        redirect('reset');
        return;
    }

    $user = $this->db->get_where('m_user', ['email_address' => $username])->row_array();

    if (!$user) {
        $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Username tidak ditemukan.</div>');
        redirect('reset');
        return;
    }

    if (verify_otp($user['user_id'], $otp_code)){
        $update_data = ['secret_key' => NULL];
        
        if (!empty($new_password)) {
            $update_data['password'] = password_hash($new_password, PASSWORD_DEFAULT);
        }
        
        if (!empty($new_username) && $new_username != $user['email_address']) {
            $update_data['email_address'] = $new_username;
        }
        
        if (!empty($update_data)) {
            $this->db->where('user_id', $user['user_id']);
            $this->db->update('m_user', $update_data);
        }
        
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Username dan Password berhasil diperbarui. Silakan login.</div>');
        redirect('auth');
    }
    else {
        $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">OTP salah atau kadaluarsa.</div>');
        redirect('reset');
    }
  }   
}
