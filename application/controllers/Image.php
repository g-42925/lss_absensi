<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Image extends CI_Controller {
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
  
	  public function index($cid) {
      $fileUrl = "https://wooden-plum-woodpecker.myfilebase.com/ipfs/" . $cid;

      $content = file_get_contents($fileUrl);

      if (!$content) show_404();

      header("Content-Type: image/jpeg");

      echo $content;
  }
}
