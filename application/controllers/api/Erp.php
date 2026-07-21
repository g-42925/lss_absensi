<?php

class Erp extends CI_Controller{
  public $email;
  public $session;
  public $form_validation;
  public $upload;
  public $pagination;

  function __construct() {
    parent::__construct();
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Origin, Content-Type, Authorization, Accept, X-Requested-With, x-xsrf-token");
    header("Content-Type: application/json; charset=utf-8");
  }

  function emp($erpId){
    $query = "select * from companies c join m_pegawai mp on c.id = mp.company_id where c.erpId = ? and mp.is_del = 'n'";
    $employeeList = $this->db->query($query,[$erpId])->result_array();
    echo json_encode($employeeList);
  }
  
  function payroll($erpId){
    $data['filter'] = date('m');

    $thpGrandTotal = 0;

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
    
    $company = $this->db->query('select * from companies where erpId = ?',[$erpId])->row_array();

    $employees = $this->db->query("select * from m_pegawai where company_id = ? and is_del = 'n'",[$company['id']])->result_array();

    foreach ($employees as $index => $emp) {
      $awalBulan = date('Y-m-01');
      $akhirBulan = date('Y-m-t');
      $deduction = $this->db->query("select * from salary_deduction where employee_id = $emp[pegawai_id] and date between '$awalBulan' and '$akhirBulan'")->result_array();
      $employees[$index]['deduction'] = $deduction;
    }

    foreach ($employees as $index => $emp) {
      $clockoutLatePenalty = 0;
      $clockoutForget = 0;
      $alpha2 = 0;
      $latePenalty = 0;
      $sick = 0;

      $deduction = [];

      foreach ($emp['deduction'] as $idx => $d) {
        if ($d['deduction_type'] == 'clockout late penalty') {
          $clockoutLatePenalty += $d['amount'];
        }
        if ($d['deduction_type'] == 'clockout forget') {
          $clockoutForget += $d['amount'];
        }
        if ($d['deduction_type'] == 'alpha-2') {
          $alpha2 += $d['amount'];
        }
        if ($d['deduction_type'] == 'late penalty') {
          $latePenalty += $d['amount'];
        }
        if ($d['deduction_type'] == 'mmc') {
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


    foreach ($employees as $index => $emp) {
      foreach ($this->db->query("select * from benefit b join employee_benefit eb on b.benefit_id = eb.benefit_id where b.company_id = ? and eb.employee_id = ?", [$company['id'], $emp['pegawai_id']])->result_array() as $idx => $b) {
        if ($b['value'] > 0) {
          $employees[$index]['minus'][] = ['name' => $b['benefit_name'], 'value' => $b['value']];
        }
      }
    }

    foreach ($employees as $index => $emp) {
      $awalBulan = date('Y-m-01');
      $akhirBulan = date('Y-m-t');
      $recap = $this->db->query("select * from recap where employee_id = ? and date between ? and ? and required = ?", [$emp['pegawai_id'], $awalBulan, $akhirBulan, true])->result_array();
      $absences = $this->db->query("select * from tx_absensi where pegawai_id = ? and tanggal_absen between ? and ?", [$emp['pegawai_id'], $awalBulan, $akhirBulan])->result_array();
      foreach ($this->db->query("select * from allowance a join employee_allowance ea on a.allowance_id = ea.allowance_id where a.company_id = ? and ea.employee_id = ?", [$company['id'], $emp['pegawai_id']])->result_array() as $idx => $a) {
        if ($a['value'] > 0) {
          if ($a['period'] == 'monthly') {
            if ($a['foa']) {
              if (count(array_filter($recap, fn($r) => $r['isAlpha'] == 1)) < 1) {
                if ($a['fol']) {
                  $hasil = array_filter($absences, function ($item) {
                    return $item['isLate'] == 1;
                  });
                  if (count($hasil) < 1) {
                    $employees[$index]['plus'][] = ['name' => $a['name'], 'value' => $a['value']];
                  }
                } else {
                  $employees[$index]['plus'][] = ['name' => $a['name'], 'value' => $a['value']];
                }
              }
            }
            if ($a['fol']) {
              $hasil = array_filter($absences, function ($item) {
                return $item['isLate'] == 1;
              });
              if (!$a['foa'] && count($hasil) < 1) {
                $employees[$index]['plus'][] = ['name' => $a['name'], 'value' => $a['value']];
              }
            }

            if (!$a['foa'] && !$a['fol']) {
              $employees[$index]['plus'][] = ['name' => $a['name'], 'value' => $a['value']];
            }
          }
          if ($a['period'] == 'daily') {
            if ($a['boa']) {
              $alphaFilter = count(array_filter($recap, fn($r) => $r['isAlpha'] == 1));
              $employees[$index]['plus'][] = ['name' => $a['name'], 'value' => $a['value'] * (count($recap) - $alphaFilter)];
            } else {
              $employees[$index]['plus'][] = ['name' => $a['name'], 'value' => $a['value'] * count($recap)];
            }
          }
        }
      }
    }

    foreach ($employees as $index => $emp) {
      foreach ($this->db->query("select * from reimburse_claim where employee_id = ? and Date(date) between ? and ? and status = ?", [$emp['pegawai_id'], date('Y-m-1'), date('Y-m-t'), 'approved'])->result_array() as $idx => $rmb) {
        $reimburse = $this->db->query("select * from reimburse where reimburse_id = ?", [$rmb['reimburse_id']])->row_array();
        $employees[$index]['plus'][] = ['name' => $reimburse['reimburse_name'], 'value' => $rmb['value']];
      }
    }

    foreach ($employees as $index => $emp) {
      if (!isset($employees[$index]['plus'])) {
        $employees[$index]['plus'] = [];
      }
      if (!isset($employees[$index]['minus'])) {
        $employees[$index]['minus'] = [];
      }
    }

    foreach ($employees as $index => $emp) {
      $bulan = date('n'); // 1-12
      $tahun = date('Y');
      $isFebruari = $bulan == 2;
      $isKabisat = checkdate(2, 29, $tahun);
      $awalBulan = date('Y-m-01');
      $akhirBulan = date('Y-m-t');
      $recap = $this->db->query("select * from recap where employee_id = ? and date between ? and ? and required = ?", [$emp['pegawai_id'], $awalBulan, $akhirBulan, true])->num_rows();
      $basicIncome = $isFebruari ? ($isKabisat ? $emp['salary'] / 24 : $emp['salary'] / 25) : $emp['salary'] / 26;

      $employees[$index]['totalPlus'] = array_sum(array_column($emp['plus'] ?? [], 'value'));
      $employees[$index]['totalMinus'] = array_sum(array_column($emp['minus'] ?? [], 'value'));

      $employees[$index]['income'] = ($recap * $basicIncome) + array_sum(array_column($emp['plus'] ?? [], 'value'));
      $employees[$index]['thp'] = $employees[$index]['income'] - $employees[$index]['totalMinus'];

      $thpGrandTotal += $employees[$index]['thp'];
    }


    $data['employees'] = $employees;
    $data['thpGrandTotal'] = $thpGrandTotal;
    
    echo json_encode($data);
  }
}
