<?php 

defined('BASEPATH') or exit('No direct script access allowed');

class Privacy extends MY_Controller {
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

    #[SkipPermission]
    public function index() {
        $this->load->view('templates/privacy-policy');
    }
}

?>