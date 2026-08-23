<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Deduction extends MY_Controller{
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
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/karyawan/data_model', 'data');
        $this->load->model('user/attendance_model', 'att');
    }

    #[SkipPermission]
    public function filter(){
        
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Karyawan';
        $data['title']      = 'Potongan';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();
        
        $empId = $this->input->get('employeeId');
        $companyId = $this->session->userdata('company_id');
        $divisions = $this->db->query("select * from divisions where company_id = ?",[$companyId])->result_array();
        $from = $this->input->get('from') == '' ? date('Y').'-'.date('m').'-01' : $this->input->get('from');
        $to = $this->input->get('to') == '' ? date('Y').'-'.date('m').'-31' : $this->input->get('to');
        $div = $this->input->get('divisionId');
        $keyword = $this->input->get('keyword');
        
        $data['from'] = $from;
        $data['to'] = $to;
        $data['divisions'] = $divisions;
        
        $query = null;

        $data['div'] = $div;
        $data['keyword'] = $keyword;
        
        if ($div == 'Any') {
            if ($keyword == '') {
              $query = $this->db->query("select * from salary_deduction sd join m_pegawai mp on mp.pegawai_id = sd.employee_id join companies c on mp.company_id = c.id where sd.date between ? and ? and c.id = ?",[$from,$to,$companyId])->result_array();
            }
            else {
              $query = $this->db->query("select * from salary_deduction sd join m_pegawai mp on mp.pegawai_id = sd.employee_id join companies c on mp.company_id = c.id where date between ? and ? and (mp.pegawai_id = ? or mp.nama_pegawai like ?) and c.id = ?",[$from,$to,$empId,"%$keyword%",$companyId])->result_array();
            }
        }
        else {
            if ($keyword == '') {
              $query = $this->db->query("select * from salary_deduction sd join m_pegawai mp on mp.pegawai_id = sd.employee_id join companies c on mp.company_id = c.id where sd.date between ? and ? and c.id = ? and mp.division_id = ?",[$from,$to,$companyId,$div])->result_array();
            }
            else {
              $query = $this->db->query("select * from salary_deduction sd join m_pegawai mp on mp.pegawai_id = sd.employee_id join companies c on mp.company_id = c.id where sd.date between ? and ? and c.id = ? and mp.division_id = ? and (mp.pegawai_id = ? or mp.nama_pegawai like ?)",[$from,$to,$companyId,$div,$empId,"%$keyword%"])->result_array();
            }
        }


        $data['deductions'] = $query;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/karyawan/deduction/filter', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }
    
    public function index(){
        
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Karyawan';
        $data['title']      = 'Potongan';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();
        
        $empId = $this->input->get('employeeId');
        $companyId = $this->session->userdata('company_id');

        $data['empId'] = $empId;

        $divisions = $this->db->query("select * from divisions where company_id = ?",[$companyId])->result_array();
        $data['divisions'] = $divisions;

        $from = $this->input->get('from') == '' ? date('Y').'-'.date('m').'-01' : $this->input->get('from');
        $to = $this->input->get('to') == '' ? date('Y').'-'.date('m').'-31' : $this->input->get('to');
        $q1 = $this->db->query("select * from salary_deduction sd join m_pegawai mp on mp.pegawai_id = sd.employee_id join companies c on mp.company_id = c.id where sd.date between ? and ? and c.id = ?",[$from,$to,$companyId])->result_array();
        $q2 = $this->db->query("select * from salary_deduction sd join m_pegawai mp on mp.pegawai_id = sd.employee_id join companies c on mp.company_id = c.id where sd.date between ? and ? and mp.pegawai_id = ? and c.id = ?",[$from,$to,$empId,$companyId])->result_array();
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
      isEditable();

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