<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Rest_get extends CI_Controller {

    public $upload;
    public $email;
    public $session;
    public $form_validation;
    public $pagination;
    public $get;

    function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Origin, Content-Type, Authorization, Accept, X-Requested-With, x-xsrf-token");
        header("Content-Type: application/json; charset=utf-8");
        $this->load->model('user/api/get_model', 'get');

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

    function cron(){
        return 'cron job is running';
    }

    function company_data() {
        $postjson = json_decode(file_get_contents('php://input'), true);
        $data = $this->get->company_data($postjson);
        echo $data;
    }

    function karyawan_data() {
        $postjson = json_decode(file_get_contents('php://input'), true);
        $data = $this->get->karyawan_data($postjson);
        echo $data;
    }

    function attendance_data() {
        $postjson = json_decode(file_get_contents('php://input'), true);
        $data = $this->get->attendance_data($postjson);
        echo $data;
    }

    function location_office() {
        $postjson = json_decode(file_get_contents('php://input'), true);
        $data = $this->get->location_office($postjson);
        echo $data;
    }

    function req_data() {
        $postjson = json_decode(file_get_contents('php://input'), true);
        $data = $this->get->req_data($postjson);
        echo $data;
    } 

    function req_data_id() {
        $postjson = json_decode(file_get_contents('php://input'), true);
        $data = $this->get->req_data_id($postjson);
        echo $data;
    } 

    function lembur_data() {
        $postjson = json_decode(file_get_contents('php://input'), true);
        $data = $this->get->terkini($postjson);
        echo $data;
    }

    function terkini() {
        $postjson = json_decode(file_get_contents('php://input'), true);
        $data = $this->get->terkini($postjson);
        echo $data;
    }

    //Masukan function selanjutnya disini
}
?>