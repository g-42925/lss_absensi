<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Allowance extends CI_Controller {
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
        $data['nmenu']      = 'Bonus & Tunjangan';
        $data['title']      = 'Harian';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');

        $data['data'] = $this->db->query("select * from allowance where company_id = ? and period = 'daily'",[$companyId])->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/allowance/index',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function monthly(){
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Bonus & Tunjangan';
        $data['title']      = 'Bulanan';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');

        $data['data'] = $this->db->query("select * from allowance where company_id = ? and period = 'monthly'",[$companyId])->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/allowance/monthly_index',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add(){
        cek_menu_access();
        isCreatable();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Bonus & Tunjangan';
        $data['title']      = 'Harian';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/allowance/add',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

     public function monthly_add(){
        cek_menu_access();
        isCreatable();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Bonus & Tunjangan';
        $data['title']      = 'Bulanan';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/allowance/monthly_add',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function monthly_edit($id){
        cek_menu_access();
        isEditable();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Bonus & Tunjangan';
        $data['title']      = 'Bulanan';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['id'] = $id;

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);        

        $data['data'] = $this->db->query("select * from allowance where allowance_id = ?",[$id])->row_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/allowance/monthly_edit',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit($id){
        cek_menu_access();
        isEditable();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Bonus & Tunjangan';
        $data['title']      = 'Harian';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['id'] = $id;

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);        

        $data['data'] = $this->db->query("select * from allowance where allowance_id = ?",[$id])->row_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/allowance/edit',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function daily_add_proses(){
      $data = [
        'allowance_id' => $this->input->post('id'),
        'company_id' => $this->session->userdata('company_id'),
        'name' => $this->input->post('name'),
        'period' => 'daily',
        'foa' => false,
        'boa' => $this->input->post('boa')
      ];

      $q = $this->db->insert(
        'allowance',
        $data
      );

      if(!$q){
        $this->session->set_flashdata(
          'message','<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
        );
        redirect(
          'allowance/add/?failed=true'
        );
      }
      else{
        redirect(
          'allowance/'
        );
      }
    }

     public function monthly_add_proses(){
      $data = [
        'allowance_id' => $this->input->post('id'),
        'company_id' => $this->session->userdata('company_id'),
        'name' => $this->input->post('name'),
        'period' => 'monthly',
        'foa' => $this->input->post('foa'),
        'boa' => false
      ];

      $q = $this->db->insert(
        'allowance',
        $data
      );

      if(!$q){
        $this->session->set_flashdata(
          'message','<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
        );
        redirect(
          'allowance/monthly_add/?failed=true'
        );
      }
      else{
        redirect(
          'allowance/monthly'
        );
      }
    }

    public function daily_edit_process($id){
      $data = [
        'name' => $this->input->post('name'),
        'boa' => $this->input->post('boa')
      ];

      $this->db->set(
        $data
      );
      $this->db->where(
        'allowance_id', 
        $id
      );
      $q = $this->db->update(
        'allowance'
      );

      if($q){
        redirect('allowance');
      }
      else{
        $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">proses gagal, silahkan coba lagi</div>');
        redirect('allowance/edit/'.$id.'?failed=true');
      }
    }

    public function monthly_edit_process($id){
      $data = [
        'name' => $this->input->post('name'),
        'foa' => $this->input->post('foa')
      ];

      $this->db->set(
        $data
      );
      $this->db->where(
        'allowance_id', 
        $id
      );
      $q = $this->db->update(
        'allowance'
      );

      if($q){
        redirect('allowance/monthly');
      }
      else{
        $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">proses gagal, silahkan coba lagi</div>');
        redirect('allowance/monthly_edit/'.$id.'?failed=true');
      }
    }

    public function config_process($id){
      $this->db->trans_begin(); // db transaction is started from here

      $this->db->where('employee_id',$id)->delete('employee_allowance');

      foreach($this->input->post('allowance') as $index => $a){
        $value = $this->input->post('values')[$index];

        $data = [
          'employee_allowance_id' => uniqid(),
          'employee_id' => $id,
          'allowance_id' => $a,
          'value' => $value
        ];

        $this->db->insert(
          'employee_allowance',
          $data
        );
      }

      if($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        $this->session->set_flashdata('message','<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>');
        redirect('allowance/config/' . $id . '?failed=true');
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

        $data['data'] = $this->db->query("select * from allowance where company_id = ?",[$companyId])->result_array();

        foreach($data['data'] as $index => $a){
          $allowanceId = $a['allowance_id'];
          $q = $this->db->query("select * from employee_allowance where allowance_id = ? and employee_id = ?",[$allowanceId,$id])->row_array();

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
        $this->load->view('module/allowance/config',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    function monthlyDelete($allowanceId){
      $this->db->trans_begin();  // to start db transaction
      $this->db->delete('employee_allowance',['allowance_id' => $allowanceUd]);
      $this->db->delete('allowance',['allowance_id' => $allowanceId]);
      if($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        $this->session->set_flashdata(
            'message',
            '<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
        );
        redirect('allowance/monthly?failed=true');
      } 
      else {
        $this->db->trans_commit();
        redirect('allowance/monthly');

      }      
    }

    function delete($allowanceId){
      $this->db->trans_begin();  // to start db transaction
      $this->db->delete('employee_allowance',['allowance_id' => $allowanceUd]);
      $this->db->delete('allowance',['allowance_id' => $allowanceId]);
      if($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        $this->session->set_flashdata(
            'message',
            '<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
        );
        redirect('allowance?failed=true');
      } 
      else {
        $this->db->trans_commit();
        redirect('allowance');

      }
    }
}
