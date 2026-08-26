<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Except extends MY_Controller {
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
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/req_permission_model', 'rp');
    }

    public function index() {
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Data Request Izin';
        $data['title']      = 'Pengecualian';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');


        $data['data'] = $this->db->query("select * from exception e join m_pegawai mp on e.employee_id = mp.pegawai_id where mp.company_id = ? and mp.is_del != 'y' order by e.created_at desc",[$companyId])->result_array();
        $data['divisions'] = $this->db->query("select * from divisions where company_id = ?",[$companyId])->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/exception/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);


    }

    public function filter(){
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Data Request Izin';
        $data['title']      = 'Pengecualian';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $div = $this->input->get('divisionId');
        $status = $this->input->get('status');
        $start = $this->input->get('start');
        $until = $this->input->get('until');

        $companyId = $this->session->userdata('company_id');
       
        $data['divisions'] = $this->db->query("select * from divisions where company_id = ?",[$companyId])->result_array();  
        
        $data['div'] = $div == 'Any' ? '' : $div;
        $data['status'] = $status == 'all' ? '' : $status;
        $data['tglawal'] = $start ?: date('Y-m-01');
        $data['tglakhir'] = $until ?: date('Y-m-d');
        $data['keyword'] = $this->input->get('keyword');


        $data['status'] = $status;
        $data['divisions'] = $this->db->query("select * from divisions where company_id = ?",[$companyId])->result_array();
        $data['data'] = $this->rp->exceptionFilter($data['tglawal'],$data['tglakhir'],$data);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/exception/filter', $data);
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

    #[SkipPermission]
    public function edit_proses($id){
      $data = [
        'date' => $this->input->post('date'),
        'status' => $this->input->post('status'),
        'htu' => false,
        'actTime' => date('H:i')
      ];

      $this->db->trans_begin(); // to start db transaction

      $status = $this->input->post('status');

      
      $exception = $this->db->query("select * from exception where id = ?",[$id])->row_array();
      $employee = $this->db->query("select * from m_pegawai where pegawai_id = ?",[$exception['employee_id']])->row_array();

      $leaveStatus = $this->db->query("select * from employee_leave_balance where employee_id = ? order by id desc",[$exception['employee_id']])->row_array();

      if($status == "1" && $exception['is_csh']){
        if($employee['jumlah_cuti'] >= 0.5){
          $this->db->set(['used' => $leaveStatus['used'] - 0.5]);
          $this->db->where('pegawai_id', $employee['pegawai_id']);
          $this->db->update('employee_leave_balance');
        }
        else{
          if (date('m') === '02') {
            $oneDaySalary = $employee['salary'] / 24;
          }
          else{
            $oneDaySalary = $employee['salary'] / 26;
          }
          
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
        if($exception['is_csh']){
          redirect('req_permission');
        }
        else{
          redirect('except');
        }
      }
    }

}
