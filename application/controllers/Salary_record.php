<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Salary_record extends CI_Controller {

    public $email;
    public $session;
    public $form_validation;
    public $upload;
    public $pagination;
    public $other;
    public $menu;
    public $attr;

    public function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/attendance_record_model', 'attr');
    }

    public function index() {
        //cek_menu_access();

        $data['filter'] = 1;

        $data['months'] = [
          [
            'key' => 1,
            'month' => 'january'
          ],
          [
            'key' => 2,
            'month' => 'february'
          ],
          [
            'key' => 3,
            'month' => 'march'
          ],
          [
            'key' => 4,
            'month' => 'april'
          ],
          [
            'key' => 5,
            'month' => 'may'
          ],
          [
            'key' => 6,
            'month' => 'june'
          ],
          [
            'key' => 7,
            'month' => 'july'
          ],
          [
            'key' => 8,
            'month' => 'august'
          ],
          [
            'key' => 9,
            'month' => 'september'
          ],
          [
            'key' => 10,
            'month' => 'october'
          ],
          [
            'key' => 11,
            'month' => 'november'
          ],
          [
            'key' => 12,
            'month' => 'december'
          ],
        ];

        
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Rekap Gaji';
        $data['title']      = 'Rekap Gaji';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $company = $this->session->userdata('company_id');

        $employees = $this->db->query("select * from m_pegawai where company_id = $company and is_del = 'n'")->result_array();
    
        foreach($employees as $index => $emp){
          $awalBulan = date('Y-m-01');
          $akhirBulan = date('Y-m-t');
          $deduction = $this->db->query("select * from salary_deduction where employee_id = $emp[pegawai_id] and date between '$awalBulan' and '$akhirBulan'")->result_array();
          $employees[$index]['deduction'] = $deduction;
        }

        foreach($employees as $index => $emp){
          $clockoutLatePenalty = 0;
          $clockoutForget = 0;
          $alpha2 = 0;
          $latePenalty = 0;

          $deduction = [];

          foreach($emp['deduction'] as $idx => $d){
            if($d['deduction_type'] == 'clockout late penalty'){
              $clockoutLatePenalty += $d['amount'];
            }
            if($d['deduction_type'] == 'clockout forget'){
              $clockoutForget += $d['amount'];
            }
            if($d['deduction_type'] == 'alpha-2'){
              $alpha2 += $d['amount'];
            }
            if($d['deduction_type'] == 'late penalty'){
              $latePenalty += $d['amount'];
            }
          }

          $deduction['clockout_late'] = $clockoutLatePenalty;
          $deduction['clockout_forget'] = $clockoutForget;
          $deduction['alpha-2'] = $alpha2;
          $deduction['late_penalty'] = $latePenalty;

          unset($employees[$index]['deduction']);
          $employees[$index]['minus'] = $deduction;
        }

        $data['employees'] = $employees;

        foreach($employees as $index => $emp){          
          foreach($this->db->query("select * from benefit b join employee_benefit eb where b.company_id = ? and eb.employee_id = ?",[$company, $emp['pegawai_id']])->result_array() as $idx => $b){
            if($b['value'] > 0){
              $employees[$index]['minus'][$b['benefit_name']] = $b['value'];
            }
          }
        }

        foreach($employees as $index => $emp){          
            $recap = $this->db->query("select * from recap where employee_id = ? and date between ? and ? and required = ?",[$emp['pegawai_id'],$awalBulan,$akhirBulan,true])->result_array();
            foreach($this->db->query("select * from allowance a join employee_allowance ea where a.company_id = ? and ea.employee_id = ?",[$company, $emp['pegawai_id']])->result_array() as $idx => $a){
            if($a['value'] > 0){
              if($a['period'] == 'monthly'){
                if($a['foa']){
                  if(count(array_filter($recap, fn($r) => $r['isAlpha'] == 1)) < 1){
                    $employees[$index]['plus'][$a['name']] = $a['value'];
                  }
                }
                else{
                  $employees[$index]['plus'][$a['name']] = $a['value'];
                }
              }
              if($a['period'] == 'daily'){
                if($a['boa']){
                  $alphaFilter = count(array_filter($recap, fn($r) => $r['isAlpha'] == 1));
                  $employees[$index]['plus'][$a['name']] = $a['value'] * (count($recap) - $alphaFilter);
                }
                else{
                  $employees[$index]['plus'][$a['name']] = $a['value'] * count($recap);
                }
              }
            }
          }
        }

        foreach($employees as $index => $emp){
          $bulan = date('n'); // 1-12
          $tahun = date('Y');
          $isFebruari = $bulan == 2;
          $isKabisat = checkdate(2, 29, $tahun);
          $recap = $this->db->query("select * from recap where employee_id = ? and date between ? and ? and required = ?",[$emp['pegawai_id'],$awalBulan,$akhirBulan,true])->num_rows();
          $basicIncome = $isFebruari ? ($isKabisat ? $emp['salary'] / 24 : $emp['salary'] / 25) : $emp['salary'] / 26;
          $income = $recap * $basicIncome;
          
          
          $income = $income - array_sum($emp['minus'] ?? []);
          $income = $income + array_sum($emp['plus'] ?? []);
          $employees[$index]['thp'] = $income;
        }

        foreach($employees as $index => $emp){
          if(!isset($emp['minus'])){
              $employees[$index]['minus'] = [];
          }
          if(!isset($emp['plus'])){
            $employees[$index]['plus'] = [];
          }
        }
        
 
        $data['employees'] = $employees;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/salary_record/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function filter($month){
        //cek_menu_access();
        
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Rekap Gaji';
        $data['title']      = 'Rekap Gaji';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['filter'] = $month;

        $company = $this->session->userdata('company_id');


        $data['months'] = [
          [
            'key' => 1,
            'month' => 'january'
          ],
          [
            'key' => 2,
            'month' => 'february'
          ],
          [
            'key' => 3,
            'month' => 'march'
          ],
          [
            'key' => 4,
            'month' => 'april'
          ],
          [
            'key' => 5,
            'month' => 'may'
          ],
          [
            'key' => 6,
            'month' => 'june'
          ],
          [
            'key' => 7,
            'month' => 'july'
          ],
          [
            'key' => 8,
            'month' => 'august'
          ],
          [
            'key' => 9,
            'month' => 'september'
          ],
          [
            'key' => 10,
            'month' => 'october'
          ],
          [
            'key' => 11,
            'month' => 'november'
          ],
          [
            'key' => 12,
            'month' => 'december'
          ],
        ];

        $employees = $this->db->query("select * from m_pegawai where company_id = $company and is_del = 'n'")->result_array();
    
        foreach($employees as $index => $emp){
          $awalBulan = date('Y-'.$month.'-01');
          $akhirBulan = date('Y-'.$month.'-t');
          $deduction = $this->db->query("select * from salary_deduction where employee_id = $emp[pegawai_id] and date between '$awalBulan' and '$akhirBulan'")->result_array();
          $employees[$index]['deduction'] = $deduction;
        }

        foreach($employees as $index => $emp){
          $clockoutLatePenalty = 0;
          $clockoutForget = 0;
          $alpha2 = 0;
          $latePenalty = 0;

          $deduction = [];

          foreach($emp['deduction'] as $idx => $d){
            if($d['deduction_type'] == 'clockout late penalty'){
              $clockoutLatePenalty += $d['amount'];
            }
            if($d['deduction_type'] == 'clockout forget'){
              $clockoutForget += $d['amount'];
            }
            if($d['deduction_type'] == 'alpha-2'){
              $alpha2 += $d['amount'];
            }
            if($d['deduction_type'] == 'late penalty'){
              $latePenalty += $d['amount'];
            }
          }

          $deduction['clockout_late'] = $clockoutLatePenalty;
          $deduction['clockout_forget'] = $clockoutForget;
          $deduction['alpha-2'] = $alpha2;
          $deduction['late_penalty'] = $latePenalty;

          unset($employees[$index]['deduction']);
          $employees[$index]['minus'] = $deduction;
        }

        $data['employees'] = $employees;

        foreach($employees as $index => $emp){          
          foreach($this->db->query("select * from benefit b join employee_benefit eb where b.company_id = ? and eb.employee_id = ?",[$company, $emp['pegawai_id']])->result_array() as $idx => $b){
            if($b['value'] > 0){
              $employees[$index]['minus'][$b['benefit_name']] = $b['value'];
            }
          }
        }

        foreach($employees as $index => $emp){          
            $recap = $this->db->query("select * from recap where employee_id = ? and date between ? and ? and required = ?",[$emp['pegawai_id'],$awalBulan,$akhirBulan,true])->result_array();
            foreach($this->db->query("select * from allowance a join employee_allowance ea where a.company_id = ? and ea.employee_id = ?",[$company, $emp['pegawai_id']])->result_array() as $idx => $a){
            if($a['value'] > 0){
              if($a['period'] == 'monthly'){
                if($a['foa']){
                  if(count(array_filter($recap, fn($r) => $r['isAlpha'] == 1)) < 1){
                    $employees[$index]['plus'][$a['name']] = $a['value'];
                  }
                }
                else{
                  $employees[$index]['plus'][$a['name']] = $a['value'];
                }
              }
              if($a['period'] == 'daily'){
                if($a['boa']){
                  $alphaFilter = count(array_filter($recap, fn($r) => $r['isAlpha'] == 1));
                  $employees[$index]['plus'][$a['name']] = $a['value'] * (count($recap) - $alphaFilter);
                }
                else{
                  $employees[$index]['plus'][$a['name']] = $a['value'] * count($recap);
                }
              }
            }
          }
        }

        foreach($employees as $index => $emp){
          $bulan = date('n'); // 1-12
          $tahun = date('Y');
          $isFebruari = $bulan == 2;
          $isKabisat = checkdate(2, 29, $tahun);
          $recap = $this->db->query("select * from recap where employee_id = ? and date between ? and ? and required = ?",[$emp['pegawai_id'],$awalBulan,$akhirBulan,true])->num_rows();
          $basicIncome = $isFebruari ? ($isKabisat ? $emp['salary'] / 24 : $emp['salary'] / 25) : $emp['salary'] / 26;
          $income = $recap * $basicIncome;
          
          
          $income = $income - array_sum($emp['minus'] ?? []);
          $income = $income + array_sum($emp['plus'] ?? []);
          $employees[$index]['thp'] = $income;
        }

        foreach($employees as $index => $emp){
          if(!isset($emp['minus'])){
              $employees[$index]['minus'] = [];
          }
          if(!isset($emp['plus'])){
            $employees[$index]['plus'] = [];
          }
        }
        
 
        $data['employees'] = $employees;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/salary_record/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }
}
