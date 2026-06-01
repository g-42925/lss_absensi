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
        
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Rekap Gaji';
        $data['title']      = 'Rekap Gaji';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['filter'] = date('m');

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
          $sick = 0;

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
            if($d['deduction_type'] == 'mmc'){
              $sick += $d['amount'];
            }
          }

          $deduction['clockout_late'] = $clockoutLatePenalty;
          $deduction['clockout_forget'] = $clockoutForget;
          $deduction['alpha-2'] = $alpha2;
          $deduction['late_penalty'] = $latePenalty;
          $deduction['sick'] = $sick;

          unset($employees[$index]['deduction']);
          
          $employees[$index]['minus'][] = ['name' => 'Clockout Late Penalty', 'value' => $clockoutLatePenalty];
          $employees[$index]['minus'][] = ['name' => 'Clockout Forget', 'value' => $clockoutForget];
          $employees[$index]['minus'][] = ['name' => 'Alpha-2', 'value' => $alpha2];
          $employees[$index]['minus'][] = ['name' => 'Late Penalty', 'value' => $latePenalty];
          $employees[$index]['minus'][] = ['name' => 'Denda sakit', 'value' => $sick];
        }
        

        foreach($employees as $index => $emp){          
          foreach($this->db->query("select * from benefit b join employee_benefit eb on b.benefit_id = eb.benefit_id where b.company_id = ? and eb.employee_id = ?",[$company, $emp['pegawai_id']])->result_array() as $idx => $b){
            if($b['value'] > 0){
              $employees[$index]['minus'][] = ['name' => $b['benefit_name'], 'value' => $b['value']];
            }
          }
        }

        foreach($employees as $index => $emp){   
            $awalBulan = date('Y-m-01');
            $akhirBulan = date('Y-m-t');       
            $recap = $this->db->query("select * from recap where employee_id = ? and date between ? and ? and required = ?",[$emp['pegawai_id'],$awalBulan,$akhirBulan,true])->result_array();
            $absences = $this->db->query("select * from tx_absensi where pegawai_id = ? and tanggal_absen between ? and ?",[$emp['pegawai_id'],$awalBulan,$akhirBulan])->result_array();
        foreach($this->db->query("select * from allowance a join employee_allowance ea on a.allowance_id = ea.allowance_id where a.company_id = ? and ea.employee_id = ?",[$company, $emp['pegawai_id']])->result_array() as $idx => $a){
          if($a['value'] > 0){
            if($a['period'] == 'monthly'){
                if($a['foa']){
                  if(count(array_filter($recap, fn($r) => $r['isAlpha'] == 1)) < 1){
                    if($a['fol']){
                      $hasil = array_filter($absences, function($item) {
                        return $item['isLate'] == 1;
                      });
                      if(count($hasil) < 1){
                        $employees[$index]['plus'][] = ['name' => $a['name'], 'value' => $a['value']];
                      }
                    }
                    else{
                      $employees[$index]['plus'][] = ['name' => $a['name'], 'value' => $a['value']];
                    }
                  }
                }
                if($a['fol']){
                  $hasil = array_filter($absences, function($item) {
                    return $item['isLate'] == 1;
                  });
                  if(!$a['foa'] && count($hasil) < 1){
                    $employees[$index]['plus'][] = ['name' => $a['name'], 'value' => $a['value']];
                  }
                }

                if(!$a['foa'] && !$a['fol']){
                  $employees[$index]['plus'][] = ['name' => $a['name'], 'value' => $a['value']];
                }
            }
            if($a['period'] == 'daily'){
              if($a['boa']){
                $alphaFilter = count(array_filter($recap, fn($r) => $r['isAlpha'] == 1));
                $employees[$index]['plus'][] = ['name' => $a['name'], 'value' => $a['value'] * (count($recap) - $alphaFilter)];
              }
              else{
                $employees[$index]['plus'][] = ['name' => $a['name'], 'value' => $a['value'] * count($recap)];
              }
            }
          }
        }
        }

        foreach($employees as $index => $emp){
          foreach($this->db->query("select * from reimburse_claim where employee_id = ? and Date(date) between ? and ? and status = ?",[$emp['pegawai_id'],date('Y-m-1'),date('Y-m-t'),'approved'])->result_array() as $idx => $rmb){
            $reimburse = $this->db->query("select * from reimburse where reimburse_id = ?",[$rmb['reimburse_id']])->row_array();
            $employees[$index]['plus'][] = ['name' => $reimburse['reimburse_name'], 'value' => $rmb['value']];
          }
        }

        foreach($employees as $index => $emp){
          if(!isset($employees[$index]['plus'])){
            $employees[$index]['plus'] = [];
          }
          if(!isset($employees[$index]['minus'])){
            $employees[$index]['minus'] = [];
          }
        }

        foreach($employees as $index => $emp){
          $bulan = date('n'); // 1-12
          $tahun = date('Y');
          $isFebruari = $bulan == 2;
          $isKabisat = checkdate(2, 29, $tahun);
          $awalBulan = date('Y-m-01');
          $akhirBulan = date('Y-m-t');
          $recap = $this->db->query("select * from recap where employee_id = ? and date between ? and ? and required = ?",[$emp['pegawai_id'],$awalBulan,$akhirBulan,true])->num_rows();
          $basicIncome = $isFebruari ? ($isKabisat ? $emp['salary'] / 24 : $emp['salary'] / 25) : $emp['salary'] / 26;
          
          $employees[$index]['totalMinus'] = array_sum(array_column($emp['minus'] ?? [], 'value'));

          $employees[$index]['income'] = ($recap * $basicIncome) + array_sum(array_column($emp['plus'] ?? [], 'value'));
          $employees[$index]['thp'] = $employees[$index]['income'] - $employees[$index]['totalMinus'];
        }


        $data['employees'] = $employees;
        

        if ($this->input->get('export') == 'excel') {
            $this->load->view('module/salary_record/excel', $data);
            return;
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/salary_record/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function filter($month) {
        
      $data['htmlpagejs'] = 'none';
      $data['nmenu']      = 'Rekap Gaji';
      $data['title']      = 'Rekap Gaji';
      $data['namalabel']  = $data['title'];
      $data['auth']       = authUser();

      $data['filter'] = $month;

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

      $company = $this->session->userdata('company_id');

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
        $sick = 0;
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
          if($d['deduction_type'] == 'mmc'){
            $sick += $d['amount'];
          }
        }

        $deduction['clockout_late'] = $clockoutLatePenalty;
        $deduction['clockout_forget'] = $clockoutForget;
        $deduction['alpha-2'] = $alpha2;
        $deduction['late_penalty'] = $latePenalty;
        $deduction['sick'] = $sick;

        unset($employees[$index]['deduction']);
        $employees[$index]['minus'][] = ['name' => 'Clockout Late Penalty', 'value' => $clockoutLatePenalty];
        $employees[$index]['minus'][] = ['name' => 'Clockout Forget', 'value' => $clockoutForget];
        $employees[$index]['minus'][] = ['name' => 'Alpha-2', 'value' => $alpha2];
        $employees[$index]['minus'][] = ['name' => 'Late Penalty', 'value' => $latePenalty];
        $employees[$index]['minus'][] = ['name' => 'Denda sakit', 'value' => $sick];
      }

      foreach($employees as $index => $emp){          
        foreach($this->db->query("select * from benefit b join employee_benefit eb on b.benefit_id = eb.benefit_id where b.company_id = ? and eb.employee_id = ?",[$company, $emp['pegawai_id']])->result_array() as $idx => $b){
          if($b['value'] > 0){
            $employees[$index]['minus'][] = ['name' => $b['benefit_name'], 'value' => $b['value']];
          }
        }
      }

        foreach($employees as $index => $emp){   
            $awalBulan = date('Y-m-01');
            $akhirBulan = date('Y-m-t');       
            $recap = $this->db->query("select * from recap where employee_id = ? and date between ? and ? and required = ?",[$emp['pegawai_id'],$awalBulan,$akhirBulan,true])->result_array();
            $absences = $this->db->query("select * from tx_absensi where pegawai_id = ? and tanggal_absen between ? and ?",[$emp['pegawai_id'],$awalBulan,$akhirBulan])->result_array();
        foreach($this->db->query("select * from allowance a join employee_allowance ea on a.allowance_id = ea.allowance_id where a.company_id = ? and ea.employee_id = ?",[$company, $emp['pegawai_id']])->result_array() as $idx => $a){
          if($a['value'] > 0){
            if($a['period'] == 'monthly'){
                if($a['foa']){
                  if(count(array_filter($recap, fn($r) => $r['isAlpha'] == 1)) < 1){
                    if($a['fol']){
                      $hasil = array_filter($absences, function($item) {
                        return $item['isLate'] == 1;
                      });
                      if(count($hasil) < 1){
                        $employees[$index]['plus'][] = ['name' => $a['name'], 'value' => $a['value']];
                      }
                    }
                    else{
                      $employees[$index]['plus'][] = ['name' => $a['name'], 'value' => $a['value']];
                    }
                  }
                }
                if($a['fol']){
                  $hasil = array_filter($absences, function($item) {
                    return $item['isLate'] == 1;
                  });
                  if(!$a['foa'] && count($hasil) < 1){
                    $employees[$index]['plus'][] = ['name' => $a['name'], 'value' => $a['value']];
                  }
                }

                if(!$a['foa'] && !$a['fol']){
                  $employees[$index]['plus'][] = ['name' => $a['name'], 'value' => $a['value']];
                }
            }
            if($a['period'] == 'daily'){
              if($a['boa']){
                $alphaFilter = count(array_filter($recap, fn($r) => $r['isAlpha'] == 1));
                $employees[$index]['plus'][] = ['name' => $a['name'], 'value' => $a['value'] * (count($recap) - $alphaFilter)];
              }
              else{
                $employees[$index]['plus'][] = ['name' => $a['name'], 'value' => $a['value'] * count($recap)];
              }
            }
          }
        }
        }

      foreach($employees as $index => $emp){
        foreach($this->db->query("select * from reimburse_claim where employee_id = ? and Date(date) between ? and ? and status = ?",[$emp['pegawai_id'],date('Y-'.$month.'-1'),date('Y-'.$month.'-t'),'approved'])->result_array() as $idx => $rmb){
          $reimburse = $this->db->query("select * from reimburse where reimburse_id = ?",[$rmb['reimburse_id']])->row_array();
          $employees[$index]['plus'][] = ['name' => $reimburse['reimburse_name'], 'value' => $rmb['value']];
        }
      }

      

      foreach($employees as $index => $emp){
        if(!isset($employees[$index]['plus'])){
          $employees[$index]['plus'] = [];
        }
        if(!isset($employees[$index]['minus'])){
          $employees[$index]['minus'] = [];
        }
      }

      foreach($employees as $index => $emp){
        $bulan = date('n'); // 1-12
        $tahun = date('Y');
        $isFebruari = $bulan == 2;
        $isKabisat = checkdate(2, 29, $tahun);
        $awalBulan = date('Y-'.$month.'-01');
        $akhirBulan = date('Y-'.$month.'-t');
        $recap = $this->db->query("select * from recap where employee_id = ? and date between ? and ? and required = ?",[$emp['pegawai_id'],$awalBulan,$akhirBulan,true])->num_rows();
        $basicIncome = $isFebruari ? ($isKabisat ? $emp['salary'] / 24 : $emp['salary'] / 25) : $emp['salary'] / 26;

        $employees[$index]['totalMinus'] = array_sum(array_column($emp['minus'] ?? [], 'value'));
        $employees[$index]['income'] = ($recap * $basicIncome) + array_sum(array_column($emp['plus'] ?? [], 'value'));
        $employees[$index]['thp'] = $employees[$index]['income'] - $employees[$index]['totalMinus'];
      }


      $data['employees'] = $employees;
        
      if ($this->input->get('export') == 'excel') {
          $this->load->view('module/salary_record/excel', $data);
          return;
      }

      $this->load->view('templates/header', $data);
      $this->load->view('templates/sidemenu', $data);
      $this->load->view('templates/sidenav', $data);
      $this->load->view('module/salary_record/index', $data);
      $this->load->view('templates/footer', $data);
      $this->load->view('templates/fscript-html-end', $data);
    }

    public function slip($month, $empId){
        
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Rekap Gaji';
        $data['title']      = 'Rekap Gaji';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $company = $this->session->userdata('company_id');

        $monthList = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

        $employee = $this->db->query("select * from m_pegawai where company_id = $company and is_del = 'n' and pegawai_id = $empId")->row_array();
    
        $awalBulan = date('Y-'.$month.'-01');
        $akhirBulan = date('Y-'.$month.'-t');
        
        $deduction = $this->db->query("select * from salary_deduction where employee_id = $employee[pegawai_id] and date between '$awalBulan' and '$akhirBulan'")->result_array();
        
        $employee['deduction'] = $deduction;

        $clockoutLatePenalty = 0;
        $clockoutForget = 0;
        $alpha2 = 0;
        $latePenalty = 0;
        $sick = 0;

        $deduction = [];

        foreach($employee['deduction'] as $idx => $d){
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
          if($d['deduction_type'] == 'mmc'){
            $sick += $d['amount'];
          }
        }

        $deduction['clockout_late'] = $clockoutLatePenalty;
        $deduction['clockout_forget'] = $clockoutForget;
        $deduction['alpha-2'] = $alpha2;
        $deduction['late_penalty'] = $latePenalty;
        $deduction['sick'] = $sick;

        unset($employee['deduction']);
        
        if($clockoutLatePenalty > 0){
          $employee['minus'][] = ['name' => 'Lupa absen pulang', 'value' => $clockoutLatePenalty];
        }
        if($clockoutForget > 0){
          $employee['minus'][] = ['name' => 'Lupa absen pulang', 'value' => $clockoutForget];
        }
        if($alpha2 > 0){
          $employee['minus'][] = ['name' => 'Alpha', 'value' => $alpha2];
        }
        if($latePenalty > 0){
          $employee['minus'][] = ['name' => 'Terlambat', 'value' => $latePenalty];
        }
        if($sick > 0){
          $employee['minus'][] = ['name' => 'Denda sakit', 'value' => $sick];
        }

        foreach($this->db->query("select * from benefit b join employee_benefit eb on b.benefit_id = eb.benefit_id where b.company_id = ? and eb.employee_id = ?",[$company, $employee['pegawai_id']])->result_array() as $idx => $b){
          if($b['value'] > 0){
            $employee['minus'][] = ['name' => $b['benefit_name'], 'value' => $b['value']];
          }
        }

        $awalBulan = date('Y-m-01');
        $akhirBulan = date('Y-m-t');       
        $recap = $this->db->query("select * from recap where employee_id = ? and date between ? and ? and required = ?",[$employee['pegawai_id'],$awalBulan,$akhirBulan,true])->result_array();
        $absences = $this->db->query("select * from tx_absensi where pegawai_id = ? and tanggal_absen between ? and ?",[$employee['pegawai_id'],$awalBulan,$akhirBulan])->result_array();
        foreach($this->db->query("select * from allowance a join employee_allowance ea on a.allowance_id = ea.allowance_id where a.company_id = ? and ea.employee_id = ?",[$company, $employee['pegawai_id']])->result_array() as $idx => $a){
          if($a['value'] > 0){
            if($a['period'] == 'monthly'){
                if($a['foa']){
                  if(count(array_filter($recap, fn($r) => $r['isAlpha'] == 1)) < 1){
                    if($a['fol']){
                      $hasil = array_filter($absences, function($item) {
                        return $item['isLate'] == 1;
                      });
                      if(count($hasil) < 1){
                        $employee['plus'][] = ['name' => $a['name'], 'value' => $a['value']];
                      }
                    }
                    else{
                      $employee['plus'][] = ['name' => $a['name'], 'value' => $a['value']];
                    }
                  }
                }
                if($a['fol']){
                  $hasil = array_filter($absences, function($item) {
                    return $item['isLate'] == 1;
                  });
                  if(!$a['foa'] && count($hasil) < 1){
                    $employee['plus'][] = ['name' => $a['name'], 'value' => $a['value']];
                  }
                }

                if(!$a['foa'] && !$a['fol']){
                  $employee['plus'][] = ['name' => $a['name'], 'value' => $a['value']];
                }
            }
            if($a['period'] == 'daily'){
              if($a['boa']){
                $alphaFilter = count(array_filter($recap, fn($r) => $r['isAlpha'] == 1));
                $employee['plus'][] = ['name' => $a['name'], 'value' => $a['value'] * (count($recap) - $alphaFilter)];
              }
              else{
                $employee['plus'][] = ['name' => $a['name'], 'value' => $a['value'] * count($recap)];
              }
            }
          }
        }

        foreach($this->db->query("select * from reimburse_claim where employee_id = ? and date between ? and ? and status = ?",[$employee['pegawai_id'],date('Y-'.$month.'-1'),date('Y-'.$month.'-t'),'approved'])->result_array() as $idx => $rmb){
          $reimburse = $this->db->query("select * from reimburse where reimburse_id = ?",[$rmb['reimburse_id']])->row_array();
          $employee['plus'][] = ['name' => $reimburse['reimburse_name'], 'value' => $rmb['value']];
        }

        if(!isset($employee['plus'])){
          $employee['plus'] = [];
        }
        if(!isset($employee['minus'])){
          $employee['minus'] = [];
        }

        $bulan = date('n'); // 1-12
        $tahun = date('Y');
        $isFebruari = $bulan == 2;
        $isKabisat = checkdate(2, 29, $tahun);
        $awalBulan = date('Y-m-01');
        $akhirBulan = date('Y-m-t');
        $recap = $this->db->query("select * from recap where employee_id = ? and date between ? and ? and required = ?",[$employee['pegawai_id'],$awalBulan,$akhirBulan,true])->num_rows();
        $basicIncome = $isFebruari ? ($isKabisat ? $employee['salary'] / 24 : $employee['salary'] / 25) : $employee['salary'] / 26;
        $income = $recap * $basicIncome;

        $employee['income'] = $income;


        $employee['totalPlus'] = array_sum(array_column($employee['plus'] ?? [], 'value')) + $income;
        $employee['totalMinus'] = array_sum(array_column($employee['minus'] ?? [], 'value'));
        

        $employee['totalIncome'] = $income + array_sum(array_column($employee['plus'] ?? [], 'value'));
        $employee['thp'] = ($income + array_sum(array_column($employee['plus'] ?? [], 'value'))) - array_sum(array_column($employee['minus'] ?? [], 'value'));

        $data['emp'] = $employee;

        $data['cmp'] = $this->db->query("select * from companies where id = ?",[$company])->row_array();
        $data['pst'] = $this->db->query("select * from position where id = ?",[$employee['position_id']])->row_array();

        $data['periode'] = $monthList[$month-1].' '.$tahun;
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/salary_record/slip', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }


}
