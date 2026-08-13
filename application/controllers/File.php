<?php

use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Aws\Exception\AwsException;

defined('BASEPATH') or exit('No direct script access allowed');

class File extends CI_Controller {
    public $email;
    public $session;
    public $form_validation;
    public $upload;
    public $pagination;
    public $other;
    public $menu;
    public $rp;

    public $s3;

    public function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/req_permission_model', 'rp');
        $this->load->model('S3_model','s3');
    }

    public function index() {
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'File';
        $data['title']      = '';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');

        $data['data'] = $this->db->query("select * from file where company_id = ?",[$companyId])->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/file/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add() {
        isEditable();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'File';
        $data['title']      = '';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/file/add', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit_proccess($id){
        $data = ['title' => $this->input->post('title')];
        $this->db->where('file_id',$id);
        $this->db->update('file',$data);
        redirect('file/index');

    }

    public function edit($id) {
        isEditable();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'File';
        $data['title']      = '';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['id'] = $id;
        

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

        $data['data'] = $this->db->query("select * from file where  file_id = ?",[$id])->row_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/file/edit', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function c_config_process($id){
      $candidate = $this->db->query("select * from candidate where candidate_id = ?",[$id])->row_array();
      $this->db->trans_begin(); // db transaction is started from here

      $this->db->where('candidate_id',$id)->delete('candidate_file');

      foreach($this->input->post('file[]') as $index => $fileId){
        $current = $this->input->post('current[]')[$index];

        if($current == ''){
          $name = $_FILES['photo']['name'][$index];
          $tmpName = $_FILES['photo']['tmp_name'][$index];
          $type = $_FILES['photo']['type'][$index];
          $fileName = time() . '_' . basename($name);
          
          $result = $this->s3->upload(
            $fileName,
            $candidate['company_id'],
            'document',
            $tmpName,
            $type
          );

          $data = [
            'candidate_file_id' => uniqid(),
            'file_id' => $fileId,
            'candidate_id' => $id,
            'source' => $result,
          ];     
          
          $this->db->insert('candidate_file',$data);
        }
        else{
          $data = [
            'candidate_file_id' => uniqid(),
            'file_id' => $fileId,
            'candidate_id' => $id,
            'source' => $current,
          ];

          $this->db->insert(
            'candidate_file',
            $data
          );          
        }
      }

      if($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        $this->session->set_flashdata(
          'message','<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
        );
        redirect('file/config/' . $id . '?failed=true');
      } 
      else {
        $this->db->trans_commit();
        redirect('karyawan/data');
      }
    }

    public function config_process($id){
      $emp = $this->db->query("select * from m_pegawai where pegawai_id = '$id'")->row_array();
      $this->db->trans_begin(); // db transaction is started from here
      $this->db->where('employee_id',$id)->delete('employee_file');

      
      foreach($this->input->post('file[]') as $index => $fileId){
        $current = $this->input->post('current[]')[$index];

        if($current == ''){
          $name = $_FILES['photo']['name'];
          $tmpName = $_FILES['photo']['tmp_name'];
          $type = $_FILES['photo']['type'];
          $fileName = time() . '_' . basename($name);


          $r = $this->s3->upload(
              $fileName,
              $emp['company_id'],
              'document',
              $tmpName,
              $type
          );
          
          $data = [
            'employee_file_id' => uniqid(),
            'file_id' => $fileId,
            'employee_id' => $id,
            'source' => $r,
          ];     
          
          $this->db->insert(
            'employee_file',
            $data
          ); 
        }
        else{
          $data = [
            'employee_file_id' => uniqid(),
            'file_id' => $fileId,
            'employee_id' => $id,
            'source' => $current,
          ];

          $this->db->insert(
            'employee_file',
            $data
          );
        }
      }

      if($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        $this->session->set_flashdata(
          'message','<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
        );
        redirect('file/config/' . $id . '?failed=true');
      } 
      else {
        $this->db->trans_commit();
        redirect('karyawan/data');
      }
    }

    public function c_config($id){
      isEditable();
      $data['htmlpagejs'] = 'none';
      $data['nmenu']      = 'Karyawan';
      $data['title']      = 'Data Karyawan';
      $data['namalabel']  = $data['title'];
      $data['auth']       = authUser();

      $data['id'] = $id;

      $data['candidate'] = true;

      $companyId = $this->session->userdata('company_id');

      $data['placeholder'] = 'https://wooden-plum-woodpecker.myfilebase.com/ipfs/QmaZ8pDRwt4WryRZkafPVi7ZLjBE67aptFNzH8GXFP7rm7';

      $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

      $data['data'] = $this->db->query("select * from file where company_id = ?",[$companyId])->result_array();

      foreach($data['data'] as $index => $a){
        $fileId = $a['file_id'];
        $q = $this->db->query("select * from candidate_file where file_id = ? and candidate_id = ?",[$fileId,$id])->row_array();

        if($q){
          $data['data'][$index]['source'] = $q['source'];
        }
        else{
          $data['data'][$index]['source'] = '-';
        }
      }

      $this->load->view('templates/header', $data);
      $this->load->view('templates/sidemenu', $data);
      $this->load->view('templates/sidenav', $data);
      $this->load->view('module/file/config',$data);
      $this->load->view('templates/footer', $data);
      $this->load->view('templates/fscript-html-end', $data);
    }

    public function config($id){
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Karyawan';
        $data['title']      = 'Data Karyawan';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['id'] = $id;

        $data['candidate'] = false;

        $companyId = $this->session->userdata('company_id');

        $data['placeholder'] = 'https://vgbkdwivxidacojvcnbr.supabase.co/storage/v1/object/public/storage/placeholder.png';

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

        $data['data'] = $this->db->query("select * from file where company_id = ?",[$companyId])->result_array();

        foreach($data['data'] as $index => $a){
          $fileId = $a['file_id'];
          $q = $this->db->query("select * from employee_file where file_id = ? and employee_id = ?",[$fileId,$id])->row_array();

          if($q){
            $data['data'][$index]['source'] = $q['source'];
          }
          else{
            $data['data'][$index]['source'] = '-';
          }
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/file/config',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add_proccess(){
        $companyId = $this->session->userdata('company_id');


        $data = [
            "file_id" => uniqid(),
            "company_id" => $companyId,
            "title" => $this->input->post("title")
        ];

        $q = $this->db->insert(
            'file',
            $data
        );

        if(!$q){
            $this->session->set_flashdata(
          'message','<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
        );
        redirect(
          'file/add?failed=true'
        );
      }
      else{
        redirect(
          'file'
        );
      }
    }

}
