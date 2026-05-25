<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Validator{
    public function __construct() {
        parent::__construct();
    }
    
    public function TEST() {
        $now = time();
        $CI =& get_instance();
        $connected = @fsockopen("www.google.com", 80);
        if(!$connected) {
            $CI->load->view('templates/warning');
            exit;
        }
        else{
            $url="https://timeapi.io/api/Time/current/zone?timeZone=Asia/Jakarta";
            $response = file_get_contents($url);
            $data = json_decode($response, true);
            $tglInternet = strtotime($data['date']);
            if($tglInternet != $now ) {
                $CI->load->view('templates/warning');
                exit;
            }
        }
    }
}
