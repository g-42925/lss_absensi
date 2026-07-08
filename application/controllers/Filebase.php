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
        $path = "https://sg.storage.bunnycdn.com/leryn/absensi_{$rootDir}_unknown/{$fileName}.jpg";
        
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
            echo "https://leryn.b-cdn.net/absensi_{$rootDir}_unknown/{$fileName}.jpg";
        } 
        else {
            show_error($response, $httpCode);
        }
    }
    
    public function exception($fileName,$id){
        $company = $this->db->query("select * from companies where id = ?",[$id])->row_array();
        $rootDir = explode('@', $company['email'])[0];
        $path = "https://sg.storage.bunnycdn.com/leryn/absensi_{$rootDir}_exception/{$fileName}.jpg";
        
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
            echo "https://leryn.b-cdn.net/absensi_{$rootDir}_exception/{$fileName}.jpg";
        } 
        else {
            show_error($response, $httpCode);
        }
    }    
    
    public function task($fileName,$id){
        $company = $this->db->query("select * from companies where id = ?",[$id])->row_array();
        $rootDir = explode('@', $company['email'])[0];
        $path = "https://sg.storage.bunnycdn.com/leryn/absensi_{$rootDir}_task/{$fileName}.jpg";
        
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
            echo "https://leryn.b-cdn.net/absensi_{$rootDir}_task/{$fileName}.jpg";
        } 
        else {
            show_error($response, $httpCode);
        }
    }
    
    public function attendance($fileName,$id){
        $company = $this->db->query("select * from companies where id = ?",[$id])->row_array();
        $rootDir = explode('@', $company['email'])[0];
        $path = "https://sg.storage.bunnycdn.com/leryn/absensi_{$rootDir}_attendance/{$fileName}.jpg";
        
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
            echo "https://leryn.b-cdn.net/absensi_{$rootDir}_attendance/{$fileName}.jpg";
        } 
        else {
            show_error($response, $httpCode);
        }
    }
    
    public function upload($fileName,$id){
        $company = $this->db->query("select * from companies where id = ?",[$id])->row_array();
        $rootDir = explode('@', $company['email'])[0];
        $path = "https://sg.storage.bunnycdn.com/leryn/absensi_{$rootDir}_unknown/{$fileName}.jpg";
        
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
            echo "https://leryn.b-cdn.net/absensi_{$rootDir}_unknown/{$fileName}.jpg";
        } 
        else {
            show_error($response, $httpCode);
        }
    }
}