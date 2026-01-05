<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Deduction extends CI_Controller{
    public $email;
    public $session;
    public $form_validation;
    public $upload;
    public $pagination;
    public $other;
    public $menu;
    public $data;
    public $att;

    public function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/karyawan/data_model', 'data');
        $this->load->model('user/attendance_model', 'att');
    }
    
    public function index(){
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Karyawan';
        $data['title']      = 'Data Karyawan';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();
        
        $empId = $this->input->get('employeeId');
        $companyId = $this->session->userdata('company_id');

        $data['empId'] = $empId;

        $from = $this->input->get('from') == '' ? date('Y').'-'.date('m').'-01' : $this->input->get('from');
        $to = $this->input->get('to') == '' ? date('Y').'-'.date('m').'-31' : $this->input->get('to');
        $q1 = $this->db->query("select * from salary_deduction sd join m_pegawai mp on mp.pegawai_id = sd.employee_id join companies c on mp.company_id = c.id where sd.date between ? and ? and c.id = ?",[$from,$to,$companyId])->result_array();
        $q2 = $this->db->query("select * from salary_deduction sd join m_pegawai mp on mp.pegawai_id = sd.employee_id join companies c on mp.company_id = c.id where sd.date between ? and ? and mp.pegawai_id = ?",[$from,$to,$empId])->result_array();
        $data['deductions'] = $empId == '' ? $q1 : $q2;
        $data['employees'] = $this->db->query("select * from m_pegawai where company_id  = ?",[$companyId])->result_array();

        $data['from'] = $from;
        $data['to'] = $to;


        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/karyawan/deduction/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function delete($deductionId){
      $this->db->query(
        "DELETE FROM salary_deduction WHERE deduction_id = ?",
        [$deductionId]
      );

      if ($this->db->affected_rows() > 0) {
        redirect('karyawan/deduction?deleted=true');
    	} 
			else {
        redirect('karyawan/deduction?deleted=false');
    	}
    }
}