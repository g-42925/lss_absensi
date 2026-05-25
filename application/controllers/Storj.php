<?php

use Aws\S3\S3Client;
use Aws\Credentials\Credentials;

defined('BASEPATH') or exit('No direct script access allowed');

class Storj extends CI_Controller {
    public function __construct() {
        parent::__construct();
    }

    public $att;
    public $email;
    public $session;
    public $form_validation;
    public $upload;
    public $pagination;

    public function upload($fileName) {
      $accKey = 'jxoanx7assawl2rzstglt5zm3zia';
      $secretKey = 'jyl5ai53cbtxk4burfc7ishe7zqzjbq6van2nrr6uiwsbxbk2rhk6';

      $client = new S3Client([
        'version' => 'latest',
        'region' => 'us-east-1',
        'endpoint' => 'https://gateway.storjshare.io',
        'credentials' => new Credentials($accKey, $secretKey),
        'use_path_style_endpoint' => true,
      ]);

      $cmd = $client->getCommand('PutObject', [
        'Bucket' => 'absensi',
        'Key' => $fileName,        
      ]);

      // URL berlaku 15 menit
      $request = $client->createPresignedRequest($cmd, '+1440 minutes');

      // Return URL ke mobile
      echo rawurldecode((string)$request->getUri());
  }

  public function download($fileName){
    $accKey = 'jxoanx7assawl2rzstglt5zm3zia';
    $secretKey = 'jyl5ai53cbtxk4burfc7ishe7zqzjbq6van2nrr6uiwsbxbk2rhk6';

    $client = new S3Client([
      'version' => 'latest',
      'region' => 'us-east-1',
      'endpoint' => 'https://gateway.storjshare.io',
      'credentials' => new Credentials($accKey, $secretKey),
      'use_path_style_endpoint' => true,
    ]);

    $cmd = $client->getCommand('GetObject', [
      'Bucket' => 'absensi',
      'Key' => $fileName,
    ]);

      // URL berlaku 15 menit
    $request = $client->createPresignedRequest($cmd, '+10080 minutes');

      // Return URL ke mobile
    echo (string)$request->getUri();
  }


}
