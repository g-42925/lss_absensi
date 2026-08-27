<?php

defined('BASEPATH') or exit('No direct script access allowed');

use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Aws\Exception\AwsException;

class S3_model extends CI_Model {

    private $bucket = 'leryn-ljm-5';
    private $endpoint = 'https://de-s3.storage.bunnycdn.com';
    private $cdn = 'https://leryn-ljm-5.b-cdn.net/';

    private $accessKey = 'leryn-ljm-5';

    private $secretKey = 'c51a0e05-4133-42cc-8607544b884e-4ecf-4320';  


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