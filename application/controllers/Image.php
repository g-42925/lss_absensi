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

      $ch = curl_init($fileUrl);
      curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => false,
      ]);
     
      $content = curl_exec($ch);
      $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);

      if ($content === false || $httpCode !== 200) {
        show_404();
        return;
      }

      header("Content-Type: image/jpeg");
      echo $content;
  }
}
