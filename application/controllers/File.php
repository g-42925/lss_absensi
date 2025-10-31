<?php
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

    public function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/req_permission_model', 'rp');
    }

    public function index() {
        cek_menu_access();
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
        cek_menu_access();
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
        cek_menu_access();
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
      $this->db->trans_begin(); // db transaction is started from here

      $this->db->where('candidate_id',$id)->delete('candidate_file');

      $supabaseUrl = "https://vgbkdwivxidacojvcnbr.supabase.co";
      $supabaseKey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InZnYmtkd2l2eGlkYWNvanZjbmJyIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc1NDg4MDEwOCwiZXhwIjoyMDcwNDU2MTA4fQ.u4n62Z_I3mO7etIJAXpzL3ScTc9QhY04hx1_n-Tg4K4";


      foreach($this->input->post('file[]') as $index => $fileId){
        $current = $this->input->post('current[]')[$index];

        if($current == ''){
          $name = $_FILES['photo']['name'][$index];
          $tmpName = $_FILES['photo']['tmp_name'][$index];
          $fileName = time() . '_' . basename($name);
          $ch = curl_init($supabaseUrl . "/storage/v1/object/storage/" . $fileName);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt_array($ch, [
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $supabaseKey,
                'Content-Type: ' . mime_content_type($tmpName)
              ],
              CURLOPT_UPLOAD => true, // ✅ penting
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_INFILE => fopen($tmpName, 'r'),
              CURLOPT_INFILESIZE => filesize($tmpName)
          ]);
          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
          curl_setopt($ch, CURLOPT_INFILE, fopen($tmpName, 'r'));
          curl_setopt($ch, CURLOPT_INFILESIZE, filesize($tmpName));

          $response = curl_exec($ch);
          $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
          curl_close($ch);

          if($httpCode === 200 || $httpCode === 201) {
            $publicUrl = $supabaseUrl . "/storage/v1/object/public/storage/" . $fileName;
            
            $data = [
              'candidate_file_id' => uniqid(),
              'file_id' => $fileId,
              'candidate_id' => $id,
              'source' => $publicUrl,
            ];


            $this->db->insert(
              'candidate_file',
              $data
            );
          }
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
      $this->db->trans_begin(); // db transaction is started from here

      $this->db->where('employee_id',$id)->delete('employee_file');

      $supabaseUrl = "https://vgbkdwivxidacojvcnbr.supabase.co";
      $supabaseKey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InZnYmtkd2l2eGlkYWNvanZjbmJyIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc1NDg4MDEwOCwiZXhwIjoyMDcwNDU2MTA4fQ.u4n62Z_I3mO7etIJAXpzL3ScTc9QhY04hx1_n-Tg4K4";


      foreach($this->input->post('file[]') as $index => $fileId){
        $current = $this->input->post('current[]')[$index];

        if($current == ''){
          $name = $_FILES['photo']['name'][$index];
          $tmpName = $_FILES['photo']['tmp_name'][$index];
          $fileName = time() . '_' . basename($name);
          $ch = curl_init($supabaseUrl . "/storage/v1/object/storage/" . $fileName);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt_array($ch, [
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $supabaseKey,
                'Content-Type: ' . mime_content_type($tmpName)
              ],
              CURLOPT_UPLOAD => true, // ✅ penting
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_INFILE => fopen($tmpName, 'r'),
              CURLOPT_INFILESIZE => filesize($tmpName)
          ]);
          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
          curl_setopt($ch, CURLOPT_INFILE, fopen($tmpName, 'r'));
          curl_setopt($ch, CURLOPT_INFILESIZE, filesize($tmpName));

          $response = curl_exec($ch);
          $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
          curl_close($ch);

          if($httpCode === 200 || $httpCode === 201) {
            $publicUrl = $supabaseUrl . "/storage/v1/object/public/storage/" . $fileName;
            
            $data = [
              'employee_file_id' => uniqid(),
              'file_id' => $fileId,
              'employee_id' => $id,
              'source' => $publicUrl,
            ];


            $this->db->insert(
              'employee_file',
              $data
            );
          }
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
      cek_menu_access();
      $data['htmlpagejs'] = 'none';
      $data['nmenu']      = 'Karyawan';
      $data['title']      = 'Data Karyawan';
      $data['namalabel']  = $data['title'];
      $data['auth']       = authUser();

      $data['id'] = $id;

      $data['candidate'] = true;

      $companyId = $this->session->userdata('company_id');

      $data['placeholder'] = 'https://vgbkdwivxidacojvcnbr.supabase.co/storage/v1/object/public/storage/placeholder.png';

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
        cek_menu_access();
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
