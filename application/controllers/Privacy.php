<?php 

defined('BASEPATH') or exit('No direct script access allowed');

class Privacy extends CI_Controller {
    public $email;
    public $session;
    public $form_validation;
    public $upload;
    public $pagination;
    public $other;
    public $menu;
    public $rp;

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->load->view('templates/privacy-policy');
    }
}

?>