<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Division extends CI_Controller {

    public $email;
    public $session;
    public $form;
    public $upload;
    public $pagination;
    public $other;
    public $menu;
    public $data;
    public $patterns;
    public $tw;
    public $form_validation;
    


    public function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/karyawan/data_model', 'data');
        $this->load->model('user/patterns_model', 'patterns');
        $this->load->model('user/karyawan/timework_model', 'tw');
    }

    public function edit($id){
      cek_menu_access();
      $data['htmlpagejs'] = 'none';
      $data['nmenu']      = 'Karyawan';
      $data['title']      = 'Divisi';
      $data['namalabel']  = 'Pengaturan '.$data['title'].' Divisi';
      $data['auth']       = authUser();

      $companyId = $this->session->userdata('company_id');

      $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

      $data['weekly'] = $this->db->query("select * from m_pola_kerja where company_id = ?",[$companyId])->result_array();
      $data['shift'] = $this->db->query("select * from shift where company_id = ?",[$companyId])->result_array();

      $data['current'] = $this->db->query("select * from divisions where id = ?",[$id])->row_array();

      $this->load->view('templates/header', $data);
      $this->load->view('templates/sidemenu', $data);
      $this->load->view('templates/sidenav', $data);
      $this->load->view('module/karyawan/division/edit', $data);
      $this->load->view('templates/footer', $data);
      $this->load->view('templates/fscript-html-end', $data);

    }

    public function edit_proses($id){
      $this->form_validation->set_rules('divisionName', 'Division Name', 'required');
      $this->form_validation->set_rules('latePenalty', 'Late Penalty', 'required');
      $this->form_validation->set_rules('penaltyNominal', 'Penalty Nominal', 'required');
      $this->form_validation->set_rules('pattern', 'Pattern', 'required');
      $this->form_validation->set_rules('clockoutPenalty', 'Clockout Penalty', 'required');
      $this->form_validation->set_rules('alphaPenalty', 'Alpha Penalty', 'required');
      $this->form_validation->set_rules('alphaPenaltyValue', 'Alpha Penalty', 'required');
      $this->form_validation->set_rules('overworkFee', 'OverWork', 'trim|required|xss_clean|htmlspecialchars');

      if($this->form_validation->run() == false){
        $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
        redirect('karyawan/division/add?failed=true');
      }
      else{
        $data = [
          'id'                    => $id,
          'company_id'            => $this->session->userdata('company_id'),
          'division_name'  	      => $this->input->post('divisionName'),
          'late_penalty'  	      => $this->input->post('latePenalty'),
          'work_system'           => $this->input->post('pattern'),
          'clockout_penalty '     => $this->input->post('clockoutPenalty'),
          'penalty_nominal'       => $this->input->post('penaltyNominal'),
          'alpha_penalty'         => $this->input->post('alphaPenalty'),
          'alpha_penalty_value'   => $this->input->post('alphaPenaltyValue'),
          'alpha_penalty_type'    => $this->input->post('alphaPenaltyType'),
          'restriction'           => $this->input->post('restriction'),
          'clockout_restriction'  => $this->input->post('clockoutRestriction'),
          'alpha_consequence'     => $this->input->post('alphaConsequence'),
          'overwork_fee'          => $this->input->post('overworkFee'),
          'alpha_penalty_on_holiday_date' => $this->input->post('alphaPenaltyOnHolidayDate'),
          'after_break_late_penalty' => $this->input->post('afterBreakLatePenalty'),
          'after_break_late_penalty_type' => $this->input->post('afterBreakLatePenaltyType'),
          'after_break_late_penalty_value' => $this->input->post('afterBreakLatePenaltyValue'),
          'ffo_check_in_allowed' => $this->input->post('ffocia'),
          'ffo_check_out_allowed' => $this->input->post('ffocoa')

        ];

        $this->db->set(
          $data
        );
        $this->db->where(
          'id', 
          $id
        );
        $q = $this->db->update(
          'divisions'
        );

        if($q){
          redirect('karyawan/division');
        }
        else{
          $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">proses gagal, silahkan coba lagi</div>');
          redirect('karyawan/division/add?failed=true');
        }
      }
    }

    public function index() {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Karyawan';
        $data['title']      = 'Divisi';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');

        $data['datas'] = $this->db->query("select * from divisions where company_id = ?",[$companyId])->result_array();


        foreach($data['datas'] as $index => $d){
          $workSystem = explode("-", $d['work_system']);
          $data['datas'][$index]['alpha_penalty_value'] = (int) $data['datas'][$index]['alpha_penalty_value']; 
          
          if($workSystem[0] == "wd"){ 
            $q = $this->db->query("select * from m_pola_kerja where pola_kerja_id = ?",[$workSystem[1]])->row_array();
            $data['datas'][$index]['work_system'] = "Work Day (".$q['nama_pola'].")";
          }
          else{
            $q = $this->db->query("select * from shift where id = ?",[$workSystem[1]])->row_array();
            $data['datas'][$index]['work_system'] = "Shift (".$q['name'].")";
          }

          if($d['alpha_penalty_type'] == "percent"){
            $data['datas'][$index]['alpha_penalty_value'] = $d['alpha_penalty_value']."% Of Salary ";
          }

          if($d['after_break_late_penalty_type'] == "minute"){
            $data['datas'][$index]['after_break_late_penalty_value'] = "Rp.".number_format($d['after_break_late_penalty_value'],2)."/minute";
          }
          if($d['after_break_late_penalty_type'] == "fixed"){
            $data['datas'][$index]['after_break_late_penalty_value'] = "Rp.".number_format($d['after_break_late_penalty_value'],2);            
          }

           if($d['alpha_consequence'] == "1"){
            $data['datas'][$index]['alpha_consequence'] = "Salary Deduction";
          }
          if($d['alpha_consequence'] == "2"){
            $data['datas'][$index]['alpha_consequence'] = "Offdays Deduction";            
          }
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/karyawan/division/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add_proses(){
        $this->form_validation->set_rules('divisionName', 'Division Name', 'required');
        $this->form_validation->set_rules('penaltyNominal', 'Penalty Nominal', 'required');
        $this->form_validation->set_rules('alphaPenaltyValue', 'Alpha Penalty', 'required');
        $this->form_validation->set_rules('restriction','Restriction','required');
        $this->form_validation->set_rules('clockoutRestriction','Clockout Restriction','required');
        $this->form_validation->set_rules('pattern','Work System','required');
        $this->form_validation->set_rules('overworkFee', 'OverWork', 'trim|required|xss_clean|htmlspecialchars');


        if($this->form_validation->run() == false){
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
            redirect('karyawan/division/add?failed=true');
        }
        else{
          $data = [
            'id'                    => time(),
            'company_id'            => $this->session->userdata('company_id'),
            'division_name'  	      => $this->input->post('divisionName'),
            'late_penalty'  	      => $this->input->post('latePenalty'),
            'work_system'           => $this->input->post('pattern'),
            'clockout_penalty '     => $this->input->post('clockoutPenalty'),
            'penalty_nominal'       => $this->input->post('penaltyNominal'),
            'alpha_penalty'         => $this->input->post('alphaPenalty'),
            'alpha_penalty_value'   => $this->input->post('alphaPenaltyValue'),
            'alpha_penalty_type'    => $this->input->post('alphaPenaltyType'),
            'restriction'           => $this->input->post('restriction'),
            'clockout_restriction'  => $this->input->post('clockoutRestriction'),
            'alpha_consequence'     => $this->input->post('alphaConsequence'),
            'overwork_fee'          => $this->input->post('overworkFee'),
            'alpha_penalty_on_holiday_date' => $this->input->post('alphaPenaltyOnHolidayDate'),
            'after_break_late_penalty' => $this->input->post('afterBreakLatePenalty'),
            'after_break_late_penalty_type' => $this->input->post('afterBreakLatePenaltyType'),
            'after_break_late_penalty_value' => $this->input->post('afterBreakLatePenaltyValue'),
            'ffo_check_in_allowed' => $this->input->post('ffocia'),
            'ffo_check_out_allowed' => $this->input->post('ffocoa')
          ];
        
          $q = $this->db->insert(
            'divisions',
            $data
          );

          if($q){
            redirect('karyawan/division');
          }
          else{
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">proses gagal, silahkan coba lagi</div>');
            redirect('karyawan/division/add?failed=true');
          }
        }

    }

      public function add() {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Karyawan';
        $data['title']      = 'Divisi';
        $data['namalabel']  = 'Pengaturan '.$data['title'].' Karyawan';
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

        $data['weekly'] = $this->db->query("select * from m_pola_kerja where company_id = ?",[$companyId])->result_array();
        $data['shift'] = $this->db->query("select * from shift where company_id = ?",[$companyId])->result_array();


        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/karyawan/division/add', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

}
