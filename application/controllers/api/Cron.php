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
    }

    function index() {
      return 'API is running';
    }
}
?>