<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Loan extends CI_Controller {
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
        $data['title']      = 'Hutang';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');

        $data['data'] = $this->db->query("select * from loan l join m_pegawai mp on l.employee_id = mp.pegawai_id join companies c on mp.company_id = c.id where c.id = ?",[$companyId])->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/loan/index',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add(){
      cek_menu_access();
      $data['htmlpagejs'] = 'none';
      $data['nmenu']      = 'Hutang & Potongan';
      $data['title']      = 'Hutang';
      $data['namalabel']  = $data['title'];
      $data['auth']       = authUser();

      $companyId = $this->session->userdata('company_id');
      
      $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

      $data['data'] = $this->db->query("select * from m_pegawai where company_id = ? and is_del = 'n'",[$companyId])->result_array();

      $this->load->view('templates/header', $data);
      $this->load->view('templates/sidemenu', $data);
      $this->load->view('templates/sidenav', $data);
      $this->load->view('module/loan/add',$data);
      $this->load->view('templates/footer', $data);
      $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit($id){
      cek_menu_access();
      $data['htmlpagejs'] = 'none';
      $data['nmenu']      = 'Hutang & Potongan';
      $data['title']      = 'Hutang';
      $data['namalabel']  = $data['title'];
      $data['auth']       = authUser();

      $data['id'] = $id;

      $companyId = $this->session->userdata('company_id');
      
      $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

      $data['data'] = $this->db->query("select * from loan where loan_id = ?",[$id])->row_array();

      $this->load->view('templates/header', $data);
      $this->load->view('templates/sidemenu', $data);
      $this->load->view('templates/sidenav', $data);
      $this->load->view('module/loan/edit',$data);
      $this->load->view('templates/footer', $data);
      $this->load->view('templates/fscript-html-end', $data);   
    }

    public function add_proses(){
      $data = [
        'loan_id' => uniqid(),
        'employee_id' => $this->input->post('employee_id'),
        'amount' => $this->input->post('amount'),
        'paid_amount' => 0,
        'date' => date('Y-m-d')
      ];

      $q = $this->db->insert(
        'loan',
        $data
      );

      if(!$q){
        $this->session->set_flashdata(
          'message','<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
        );
        redirect(
          'load/add?failed=true'
        );
      }
      else{
        redirect(
          'loan'
        );
      }
    }

    public function log($id){
      cek_menu_access();
      $data['htmlpagejs'] = 'none';
      $data['nmenu']      = 'Hutang & Potongan';
      $data['title']      = 'Hutang';
      $data['namalabel']  = $data['title'];
      $data['auth']       = authUser();

      $data['data'] = $this->db->query("select * from loan_pay_log where loan_id = ?",[$id])->result_array();

      $this->load->view('templates/header', $data);
      $this->load->view('templates/sidemenu', $data);
      $this->load->view('templates/sidenav', $data);
      $this->load->view('module/loan/log',$data);
      $this->load->view('templates/footer', $data);
      $this->load->view('templates/fscript-html-end', $data);   

    }

     public function edit_proses($id){
      $loan = $this->db->query("select * from loan where loan_id = ?",[$id])->row_array();
      $data = ['paid_amount' => $this->input->post('paid_amount')];
      
      $newPaidAmount =  $this->input->post('paid_amount') - $loan['paid_amount'];

      $data2 = [
        'payment_log_id' => uniqid(),
        'loan_id' => $id,
        'date' => date('Y-m-d'),
        'amount' => $newPaidAmount,
        'description' => ''
      ];

      $this->db->trans_begin();

      $this->db->set(
        $data
      );
      $this->db->where(
        'loan_id', 
        $id
      );
      $q = $this->db->update(
        'loan'
      );

      $this->db->insert(
        'loan_pay_log',
        $data2
      );

     if($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        $this->session->set_flashdata(
          'message','<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
        );
        redirect(
          'load/edit/'.$id.'?failed=true'
        );
      } 
      else {
        $this->db->trans_commit();
        redirect(
          'loan'
        );
      }
    }
}
