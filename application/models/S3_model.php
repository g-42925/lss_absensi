<?php

defined('BASEPATH') or exit('No direct script access allowed');

use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Aws\Exception\AwsException;

class S3_model extends CI_Model {

    private $bucket = 'leryn-ljm-3';
    private $endpoint = 'https://de-s3.storage.bunnycdn.com';
    private $cdn = 'https://leryn-ljm-3.b-cdn.net/';

    private $accessKey = 'leryn-ljm-3';

    private $secretKey = '625c8418-f512-4642-98885cb3a927-3b8b-442a';  


    public function __construct() {
        parent::__construct();
    }

    private function getS3(){
        return new S3Client([
            'version' => 'latest',
            'endpoint' => $this->endpoint,
            'region' => 'de',
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key' => $this->accessKey,
                'secret' => $this->secretKey,
            ],
        ]);
    }

    


    private function getRootDir($id){
        $company = $this->db->query("SELECT * FROM companies WHERE id = ?",[$id])->row_array();
        return explode('@', $company['email'])[0];
    }

    public function upload($fileName, $id, $type,$file,$contentType){
        $rootDir = $this->getRootDir($id);

        $year = date('Y');
        $month = date('m');

        $key = "absensi_{$rootDir}_{$type}_{$year}_{$month}/{$fileName}";


        $result = $this->getS3()->putObject([
            'Bucket' => $this->bucket,
            'Key' => $key,
            'SourceFile' => $file,
            'ContentType' => $contentType,
        ]);

        return $this->cdn . $key;
    }
}