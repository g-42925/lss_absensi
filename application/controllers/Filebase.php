<?php

use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Aws\Exception\AwsException;

defined('BASEPATH') or exit('No direct script access allowed');

class Filebase extends CI_Controller {
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
        $s3 = new S3Client([
            'version'     => 'latest',
            'region'      => 'us-east-1',
            'endpoint'    => 'https://s3.filebase.com',
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key'    => 'B8F0135956143AE0685E',
                'secret' => 'gKrbIZJnzLWBXZ0VGQvnlAumvngpBH35PsXN5zUp'
            ],
            'Metadata' => [
              'cid' => 'true'
            ],
            'ContentType' => mime_content_type($file),
        ]);

        // file dari form android
        $file = $_FILES['file']['tmp_name'];

        $result = $s3->putObject([
          'Bucket' => 'leryn-storage',
          'Key'    => $fileName,
          'SourceFile' => $file,
          'ContentType' => 'image/png',
        ]);

        $cid = $result['@metadata']['headers']['x-amz-meta-cid']; 

        echo "https://wooden-plum-woodpecker.myfilebase.com/ipfs/".$cid;

    }

}