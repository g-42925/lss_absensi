<?php


defined('BASEPATH') or exit('No direct script access allowed');

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
        function makePathAndResult($rootDir,$fileName,$year,$month){
            return [
              "path" => "https://storage.bunnycdn.com/leryn-ljm-3/absensi_{$rootDir}_unknown_{$year}_{$month}/{$fileName}.jpg",
              "result" => "https://leryn-ljm-3.b-cdn.net/absensi_{$rootDir}_unknown_{$year}_{$month}/{$fileName}.jpg"
            ];
        }           
        
        
        $company = $this->db->query("select * from companies where id = ?",[$id])->row_array();
        $rootDir = explode('@', $company['email'])[0];
        $path = makePathAndResult($rootDir,$fileName,date('Y'),date('m'))['path'];
        
        $fp = fopen($_FILES['file']['tmp_name'], 'r');
        
        $ch = curl_init($path);
            curl_setopt_array($ch, [
                CURLOPT_UPLOAD => true,
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_INFILE => $fp,
                CURLOPT_INFILESIZE => filesize($_FILES['file']['tmp_name']),
                CURLOPT_HTTPHEADER => [
                'AccessKey: 625c8418-f512-4642-98885cb3a927-3b8b-442a',
                'Content-Type: ' . $_FILES['file']['type']
            ],
            CURLOPT_RETURNTRANSFER => true,
        ]);
         
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) throw new Exception(curl_error($ch));
        
        curl_close($ch);
        fclose($fp);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            echo makePathAndResult($rootDir,$fileName,date('Y'),date('m'))['result'];
        } 
        else {
            show_error($response, $httpCode);
        }
    }
    
    public function exception($fileName,$id){
        function makePathAndResult($rootDir,$fileName,$year,$month){
            return [
              "path" => "https://storage.bunnycdn.com/leryn-ljm-3/absensi_{$rootDir}_exception_{$year}_{$month}/{$fileName}.jpg",
              "result" => "https://leryn-ljm-3.b-cdn.net/absensi_{$rootDir}_exception_{$year}_{$month}/{$fileName}.jpg"
            ];
        }             
        
        $company = $this->db->query("select * from companies where id = ?",[$id])->row_array();
        $rootDir = explode('@', $company['email'])[0];
        $path = makePathAndResult($rootDir,$fileName,date('Y'),date('m'))['path'];
        
        $fp = fopen($_FILES['file']['tmp_name'], 'r');
        $ch = curl_init($path);
            curl_setopt_array($ch, [
                CURLOPT_UPLOAD => true,
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_INFILE => $fp,
                CURLOPT_INFILESIZE => filesize($_FILES['file']['tmp_name']),
                CURLOPT_HTTPHEADER => [
                'AccessKey: 625c8418-f512-4642-98885cb3a927-3b8b-442a',
                'Content-Type: ' . $_FILES['file']['type']
            ],
            CURLOPT_RETURNTRANSFER => true,
        ]);
         
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) throw new Exception(curl_error($ch));
        
        curl_close($ch);
        fclose($fp);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            echo makePathAndResult($rootDir,$fileName,date('Y'),date('m'))['result'];
        } 
        else {
            show_error($response, $httpCode);
        }
    }

    public function task($fileName,$id){
        function makePathAndResult($rootDir,$fileName,$year,$month){
            return [
              "path" => "https://storage.bunnycdn.com/leryn-ljm-3/absensi_{$rootDir}_task_{$year}_{$month}/{$fileName}.jpg",
              "result" => "https://leryn-ljm-3.b-cdn.net/absensi_{$rootDir}_task_{$year}_{$month}/{$fileName}.jpg"
            ];
        }        
        
        $company = $this->db->query("select * from companies where id = ?",[$id])->row_array();
        $rootDir = explode('@', $company['email'])[0];
        $path    = makePathAndResult($rootDir,$fileName,date('Y'),date('m'))['path'];
        
        
        //$path = "https://storage.bunnycdn.com/leryn-ljm/absensi_{$rootDir}_task/{$fileName}.jpg";
        
        $fp = fopen($_FILES['file']['tmp_name'], 'r');
        $ch = curl_init($path);
            curl_setopt_array($ch, [
                CURLOPT_UPLOAD => true,
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_INFILE => $fp,
                CURLOPT_INFILESIZE => filesize($_FILES['file']['tmp_name']),
                CURLOPT_HTTPHEADER => [
                'AccessKey: 625c8418-f512-4642-98885cb3a927-3b8b-442a',
                'Content-Type: ' . $_FILES['file']['type']
            ],
            CURLOPT_RETURNTRANSFER => true,
        ]);
         
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) throw new Exception(curl_error($ch));
        
        curl_close($ch);
        fclose($fp);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            echo makePathAndResult($rootDir,$fileName,date('Y'),date('m'))['result'];
        } 
        else {
            show_error($response, $httpCode);
        }
    }
    
    public function attendance($fileName,$id){
        function makePathAndResult($rootDir,$fileName,$year,$month){
            return [
              "path" => "https://storage.bunnycdn.com/leryn-ljm-3/absensi_{$rootDir}_attendance_{$year}_{$month}/{$fileName}.jpg",
              "result" => "https://leryn-ljm-3.b-cdn.net/absensi_{$rootDir}_attendance_{$year}_{$month}/{$fileName}.jpg"
            ];
        }
        

        $company = $this->db->query("select * from companies where id = ?",[$id])->row_array();
        $rootDir = explode('@', $company['email'])[0];
        $path    = makePathAndResult($rootDir,$fileName,date('Y'),date('m'))['path'];

        $fp = fopen($_FILES['file']['tmp_name'], 'r');
        $ch = curl_init($path);
            curl_setopt_array($ch, [
                CURLOPT_UPLOAD => true,
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_INFILE => $fp,
                CURLOPT_INFILESIZE => filesize($_FILES['file']['tmp_name']),
                CURLOPT_HTTPHEADER => [
                'AccessKey: 625c8418-f512-4642-98885cb3a927-3b8b-442a',
                'Content-Type: ' . $_FILES['file']['type']
            ],
            CURLOPT_RETURNTRANSFER => true,
        ]);
         
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) throw new Exception(curl_error($ch));
        
        curl_close($ch);
        fclose($fp);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            echo makePathAndResult($rootDir,$fileName,date('Y'),date('m'))['result'];
        } 
        else {
            show_error($response, $httpCode);
        }
    }
    
    public function upload($fileName,$id){
        $company = $this->db->query("select * from companies where id = ?",[$id])->row_array();
        $rootDir = explode('@', $company['email'])[0];
        $path = "https://storage.bunnycdn.com/leryn-ljm/absensi_{$rootDir}_unknown/{$fileName}.jpg";
        
        $fp = fopen($_FILES['file']['tmp_name'], 'r');
        $ch = curl_init($path);
            curl_setopt_array($ch, [
                CURLOPT_UPLOAD => true,
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_INFILE => $fp,
                CURLOPT_INFILESIZE => filesize($_FILES['file']['tmp_name']),
                CURLOPT_HTTPHEADER => [
                'AccessKey: 87444b62-a846-4c08-90ae4cd1779b-dc96-407a',
                'Content-Type: ' . $_FILES['file']['type']
            ],
            CURLOPT_RETURNTRANSFER => true,
        ]);
         
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) throw new Exception(curl_error($ch));
        
        curl_close($ch);
        fclose($fp);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            echo "https://leryn-ljm.b-cdn.net/absensi_{$rootDir}_unknown/{$fileName}.jpg";
        } 
        else {
            show_error($response, $httpCode);
        }
    }
}