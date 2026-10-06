<?php

defined('BASEPATH') or exit('No direct script access allowed');

use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Aws\Exception\AwsException;

class S3_model extends CI_Model {

    private $cdn = 'https://cdn.lerynpest.com/';
    private $bucket = 'absensi-lerynsoftware-com';
    private $endpoint = 'https://a9b834b798483ca01f0f33ccfab2d31d.r2.cloudflarestorage.com';

    private $accessKey = '9d95d51b57c4f172b3c7fe54c850843e';

    private $secretKey = '5dee914ebc364f5a046164791017b558d512cd22b68ae0d36611373665bc443c';


    public function __construct(){
        parent::__construct();
    }

    private function getS3(){
        return new S3Client([
            'endpoint' => $this->endpoint,
            'region' => 'auto',
            'credentials' => [
                'key' => $this->accessKey,
                'secret' => $this->secretKey,
            ],
        ]);
    }

    private function getRootDir($id){
        $company = $this->db->query("SELECT * FROM companies WHERE id = ?", [$id])->row_array();
        return explode('@', $company['email'])[0];
    }

    public function upload($fileName, $id, $type, $file, $contentType){
        $rootDir = $this->getRootDir($id);

        $year = date('Y');
        $month = date('m');

        $ext = pathinfo($fileName, PATHINFO_EXTENSION);

        if (empty($ext)) {
            $mimeTypes = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp',
                'application/pdf' => 'pdf'
            ];

            if (isset($mimeTypes[$contentType])) {
                $fileName .= '.' . $mimeTypes[$contentType];
            }
        }

        $key = "absensi_{$rootDir}_{$type}_{$year}_{$month}/{$fileName}";

        

        $result = $this->getS3()->putObject([
            'Bucket' => $this->bucket,
            'Key' => $key,
            'Body' => $file,
            'ContentType' => $contentType
        ]);

        return $this->cdn . $key;
    }
}
