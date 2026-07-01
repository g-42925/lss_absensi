<?php

use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Aws\Exception\AwsException;

defined('BASEPATH') or exit('No direct script access allowed');

class Filebase extends CI_Controller {
    public $att;
    public $email;
    public $session;
    public $form_validation;
    public $upload;
    public $pagination;
    
    
    public function unknown($fileName,$id){
        $company = $this->db->query("select * from companies where id = ?",[$id])->row_array();
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
        

        $s3->putObject([
          'Bucket' => 'project-alpha',
          'Key' => 'absensi_'.$rootDir .'_unknown/'.$fileName,
          'SourceFile' =>  $_FILES['file']['tmp_name'],
          'ContentType' => 'image/png',
        ]);
        
        echo $fileName;

    }
    
    public function exception($fileName,$id){
        $company = $this->db->query("select * from companies where id = ?",[$id])->row_array();
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
        


        $s3->putObject([
          'Bucket' => 'project-alpha',
          'Key' => 'absensi_'.$rootDir .'_exception/'.$fileName,
          'SourceFile' => $_FILES['file']['tmp_name'],
          'ContentType' => 'image/png',
        ]);
        
        echo $fileName;
    }
    
    public function task($fileName,$id){
        $company = $this->db->query("select * from companies where id = ?",[$id])->row_array();
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

        $result = $s3->putObject([
          'Bucket' => 'project-alpha',
          'Key' => 'absensi_'.$rootDir .'_task/'.$fileName,
          'SourceFile' => $_FILES['file']['tmp_name'],
          'ContentType' => 'image/png',
        ]);
        
        echo $fileName;

        // $cmd = $s3->getCommand('GetObject', [
        //     'Bucket' => 'project-alpha',
        //     'Key'    => 'absensi_'.$rootDir.'_task/'.$fileName,
        // ]);
        
        // $request = $s3->createPresignedRequest($cmd, '+1 day');
        
        // $url = (string) $request->getUri();
        
        // echo $url;
        
        // die();


        // echo "https://wooden-plum-woodpecker.myfilebase.com/ipfs/".$cid;        
    }

    
    public function attendance($fileName,$id){
        $company = $this->db->query("select * from companies where id = ?",[$id])->row_array();
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
        

        $s3->putObject([
          'Bucket' => 'project-alpha',
          'Key' => 'absensi_'.$rootDir .'_task/'.$fileName,
          'SourceFile' =>  $_FILES['file']['tmp_name'],
          'ContentType' => 'image/png',
        ]);


        echo $fileName;
        
    }

    public function upload($fileName) {
        $company = $this->db->query("select * from companies where id = ?",[$id])->row_array();
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
        
        $s3->putObject([
          'Bucket' => 'project-alpha',
          'Key' => 'absensi_'.$rootDir .'_unknown/'.$fileName,
          'SourceFile' =>  $_FILES['file']['tmp_name'],
          'ContentType' => 'image/png',
        ]);

        echo $fileName;
    }

}