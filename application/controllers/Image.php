<?php

use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Aws\Exception\AwsException;

defined('BASEPATH') or exit('No direct script access allowed');

class Image extends MY_Controller {
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
  
  

    #[SkipPermission]
    function view($path,$fileName){
      $companyId = $this->session->userdata('company_id');
      $company = $this->db->query("select * from companies where id = ?",[$companyId])->row_array();
      $rootDir = explode('@', $company['email'])[0];

      $s3 = new S3Client([
          'version'     => 'latest',
          'region'      => 'us-east-1',
          'endpoint'    => 'https://o3-rc3.akave.xyz',
          'use_path_style_endpoint' => false,
          'credentials' => [
              'key'    => 'O3_0F49YS93DCVX51CM0',
              'secret' => 'Wp4izs5aiR73rJFyOX6rWNnVmWDae1rqAf1NUFeR'
          ]
      ]);
      

      $cmd = $s3->getCommand('GetObject',[
        'Bucket' => 'project-alpha',
        'Key' => 'absensi_'.$rootDir.'_'.$path.'/'.$fileName
      ]);

      
      $request = $s3->createPresignedRequest($cmd, '+1 day');
      
      $url = (string) $request->getUri();
      
      $ch = curl_init($url);
     
      curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => false,
      ]);
     
      $content = curl_exec($ch);
      $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
      curl_close($ch);

      if ($content === false || $httpCode !== 200) {
        header("HTTP/1.1 404 Not Found");
        exit;
      }

      header("Content-Type: " . ($contentType ? $contentType : "image/jpeg"));
      echo $content;

  }
}
