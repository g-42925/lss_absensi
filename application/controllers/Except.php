<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Except extends CI_Controller {
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
        $data['nmenu']      = 'Data Request Izin';
        $data['title']      = 'Pengecualian';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');

        $data['data'] = $this->db->query("select * from exception e join m_pegawai mp on e.employee_id = mp.pegawai_id where mp.company_id = ? and mp.is_del != 'y' order by e.created_at desc",[$companyId])->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/exception/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit($id){
        isEditable();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Data Request Izin';
        $data['title']      = 'Pengecualian';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();
        
        $data['id'] = $id;

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);
        $data['data'] = $this->db->query("select * from exception where id = ?",[$id])->row_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/exception/edit', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit_proses($id){
      cek_menu_access();

      $data = [
        'date' => $this->input->post('date'),
        'status' => $this->input->post('status'),
        'htu' => false
      ];

      $this->db->trans_begin(); // to start db transaction

      $status = $this->input->post('status');
      
      $exception = $this->db->query("select * from exception where id = ?",[$id])->row_array();
      $employee = $this->db->query("select * from m_pegawai where pegawai_id = ?",[$exception['employee_id']])->row_array();

      if($status == "1" && $exception['is_csh']){
        if($employee['jumlah_cuti'] >= 0.5){
          $this->db->set(['jumlah_cuti' => $employee['jumlah_cuti'] - 0.5]);
          $this->db->where(['pegawai_id' => $employee['pegawai_id']]);
          $this->db->update('m_pegawai');
        }
        else{
          $oneDaySalary = $employee['salary'] / 26;
          $halfOfOneDaySalary = $oneDaySalary / 2;
          
          $cshData = [
            'deduction_id' => uniqid(),
            'employee_id' => $exception['employee_id'],
            'deduction_type' => 'late penalty',
            'date' => date('Y-m-d'),
            'amount' => $halfOfOneDaySalary,
            'note' => '...'     
          ];
          
          $this->db->insert('salary_deduction',$cshData);

        }
      }

      $this->db->set(
        $data
      );
      $this->db->where(
        'id', 
        $id
      );
      $this->db->update(
        'exception'
      );

      if($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">proses gagal, silahkan coba lagi</div>');
        redirect('except/edit/'.$id.'?failed=true');
      } 
      else {
        $this->db->trans_commit();
        redirect('except');
      }
    }

}
