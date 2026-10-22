<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// 1. Fungsi untuk MEMBUAT OTP (Dikirim ke Email)
if (!function_exists('generate_otp')) {
    function get_otp($userId) {
        $CI =& get_instance();
        $CI->load->database();
        
        $token = random_int(100000, 999999);

        $CI->db->where('user_id', $userId);
        $CI->db->update('m_user', ['secret_key' => password_hash($token, PASSWORD_DEFAULT)]);

        return $token;
    }
}

// 2. Fungsi untuk MEMVERIFIKASI OTP (Saat User Input)
if (!function_exists('verify_otp')) {
    function verify_otp($user_id, $input_otp) {
        $CI =& get_instance();
        $CI->load->database();
        
        $user = $CI->db->where('user_id', $user_id)->get('m_user')->row_array();

        if (password_verify($input_otp, $user['secret_key'])) {
          return true;
        }
        else{
          return false;
        }
    }
}