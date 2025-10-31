<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Benefit extends CI_Controller {
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
        $data['nmenu']      = 'Hutang & Potongan';
        $data['title']      = 'Potongan';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');

        $data['data'] = $this->db->query("select * from benefit where company_id = ?",[$companyId])->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/benefit/index',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add(){
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Hutang & Potongan';
        $data['title']      = 'Potongan';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/benefit/add',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit($id){
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Hutang & Potongan';
        $data['title']      = 'Potongan';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['id'] = $id;

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

        $data['data'] = $this->db->query("select * from benefit where benefit_id = ?",[$id])->row_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/benefit/edit',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit_proses($id){
      $data = ['benefit_name' => $this->input->post('benefit_name')];
      
      $this->db->set(
        $data
      );
      $this->db->where(
        'benefit_id', 
        $id
      );
      $q = $this->db->update(
        'benefit'
      );


      if(!$q){
        $this->session->set_flashdata(
          'message','<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
        );
        redirect(
          'benefit/edit/'.$id.'?failed=true'
        );
      }
      else{
        redirect(
          'benefit'
        );
      }
    }

    public function add_proses(){
      $companyId = $this->session->userdata('company_id');
      $data = ['benefit_name' => $this->input->post('benefit_name')];
      $data = [...$data,'benefit_id' => uniqid(),'company_id' => $companyId];

      $q = $this->db->insert(
        'benefit',
        $data
      );

      if(!$q){
        $this->session->set_flashdata(
          'message','<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
        );
        redirect(
          'benefit/add?failed=true'
        );
      }
      else{
        redirect(
          'benefit/'
        );
      }
    }

    public function config_process($id){
      $this->db->trans_begin(); // db transaction is started from here

      $this->db->where('employee_id',$id)->delete('employee_benefit');

      foreach($this->input->post('benefit') as $index => $a){
        $value = $this->input->post('values')[$index];

        $data = [
          'employee_benefit_id' => uniqid(),
          'employee_id' => $id,
          'benefit_id' => $a,
          'value' => $value
        ];

        $this->db->insert(
          'employee_benefit',
          $data
        );
      }

      if($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        $this->session->set_flashdata('message','<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>');
        redirect('benefit/config/' . $id . '?failed=true');
      } 
      else {
        $this->db->trans_commit();
        redirect('karyawan/data');
      }
    }
    

    public function config($id){
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Karyawan';
        $data['title']      = 'Data Karyawan';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['id'] = $id;

        $companyId = $this->session->userdata('company_id');

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

        $data['data'] = $this->db->query("select * from benefit where company_id = ?",[$companyId])->result_array();

        foreach($data['data'] as $index => $a){
          $benefitId = $a['benefit_id'];
          $q = $this->db->query("select * from employee_benefit where benefit_id = ? and employee_id = ?",[$benefitId,$id])->row_array();

          if($q){
            $data['data'][$index]['value'] = $q['value'];
          }
          else{
            $data['data'][$index]['value'] = 0;
          }
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/benefit/config',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }    
}
