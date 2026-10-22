<?php


defined('BASEPATH') or exit('No direct script access allowed');


class Filebase extends MY_Controller{
    public $att;
    public $email;
    public $session;
    public $form_validation;
    public $upload;
    public $pagination;
    public $s3;


    public function __construct(){
        parent::__construct();

        $this->load->model('s3_model','s3');
    }

    #[SkipPermission]
    public function upload($fileName,$id,$dir){
        try {
            $bin = file_get_contents($_FILES['file']['tmp_name']);

            echo $this->s3->upload(
                $fileName,
                $id,
                $dir,
                $bin,
                $_FILES['file']['type']
            );
        } 
        catch (Exception $e) {
            show_error($e->getMessage(), 500);
        }
    }

    #[SkipPermission]
    public function unknown($fileName, $id){
        try {
            $bin = file_get_contents($_FILES['file']['tmp_name']);


            echo $this->s3->upload(
                $fileName,
                $id,
                'unknown',
                $bin,
                $_FILES['file']['type']
            );
        } 
        catch (Exception $e) {
            show_error($e->getMessage(), 500);
        }
    }

    #[SkipPermission]
    public function exception($fileName, $id){
        try {
            $bin = file_get_contents($_FILES['file']['tmp_name']);

            echo $this->s3->upload(
                $fileName,
                $id,
                'exception',
                $bin,
                $_FILES['file']['type']
            );
        } 
        catch (Exception $e) {
            show_error($e->getMessage(), 500);
        }
    }

    #[SkipPermission]
    public function task($fileName, $id){
        try {

            $bin = file_get_contents($_FILES['file']['tmp_name']);

            echo $this->s3->upload(
                $fileName,
                $id,
                'task',
                $bin,
                $_FILES['file']['type']
            );
        } 
        catch (Exception $e) {
            show_error($e->getMessage(), 500);
        }
    }

    #[SkipPermission]
    public function attendance($fileName, $id){
        try {
            $bin = file_get_contents($_FILES['file']['tmp_name']);
            
            echo $this->s3->upload(
                $fileName,
                $id,
                'attendance',
                $bin,
                $_FILES['file']['type']
            );
        } 
        catch (Exception $e) {
            show_error($e->getMessage(), 500);
        }
    }
}
