<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Rest_post extends CI_Controller {

    function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Origin, Content-Type, Authorization, Accept, X-Requested-With, x-xsrf-token");
        header("Content-Type: application/json; charset=utf-8");
        $this->load->model('user/api/post_model', 'post');

        require APPPATH.'libraries/phpmailer/src/Exception.php';
        require APPPATH.'libraries/phpmailer/src/PHPMailer.php';
        require APPPATH.'libraries/phpmailer/src/SMTP.php';
    }

    function index() {
        $config = array(
            'name'      => 'Carvellonic',
            'website'   => 'https://carvellonic.com'
        );
        echo json_encode($config);
    }

    function signin() {
        $postjson = json_decode(file_get_contents('php://input'), true);
        $data = $this->post->signin($postjson);
        echo $data;
    }

    function post_attendance() {
        $postjson = json_decode(file_get_contents('php://input'), true);
        $data = $this->post->post_attendance($postjson);
        echo $data;
    }

    function attendance() {
        $postjson = json_decode(file_get_contents('php://input'), true);
        $data = $this->post->attendance($postjson);
        echo $data;
    }

    function attendance_lembur() {
        $postjson = json_decode(file_get_contents('php://input'), true);
        $data = $this->post->attendance_lembur($postjson);
        echo $data;
    }

    function add_izin() {
        $postjson = json_decode(file_get_contents('php://input'), true);
        $data = $this->post->add_izin($postjson);
        echo $data;
    }

    function edit_izin() {
        $postjson = json_decode(file_get_contents('php://input'), true);
        $data = $this->post->edit_izin($postjson);
        echo $data;
    }

    function del_izin() {
        $postjson = json_decode(file_get_contents('php://input'), true);
        $data = $this->post->del_izin($postjson);
        echo $data;
    }

    function add_lembur() {
        $postjson = json_decode(file_get_contents('php://input'), true);
        $data = $this->post->add_lembur($postjson);
        echo $data;
    }

    function del_lembur() {
        $postjson = json_decode(file_get_contents('php://input'), true);
        $data = $this->post->del_lembur($postjson);
        echo $data;
    }

    function reset_absen() {
        $postjson = json_decode(file_get_contents('php://input'), true);
        $data = $this->post->reset_absen($postjson);
        echo $data;
    }

    function terkini() {
        $postjson = json_decode(file_get_contents('php://input'), true);
        $data = $this->post->terkini($postjson);
        echo $data;
    }

    //Masukan function selanjutnya disini
}
?>