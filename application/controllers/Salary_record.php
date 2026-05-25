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

        foreach($employees as $index => $emp){          
          foreach($this->db->query("select * from benefit b join employee_benefit eb where b.company_id = ? and eb.employee_id = ?",[$company, $emp['pegawai_id']])->result_array() as $idx => $b){
            if($b['value'] > 0){
              $employees[$index]['minus'][$b['benefit_name']] = $b['value'];
            }
          }
        }

        foreach($employees as $index => $emp){   
            $awalBulan = date('Y-m-01');
            $akhirBulan = date('Y-m-t');       
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
          $awalBulan = date('Y-m-01');
          $akhirBulan = date('Y-m-t');
          $recap = $this->db->query("select * from recap where employee_id = ? and date between ? and ? and required = ?",[$emp['pegawai_id'],$awalBulan,$akhirBulan,true])->num_rows();
          $basicIncome = $isFebruari ? ($isKabisat ? $emp['salary'] / 24 : $emp['salary'] / 25) : $emp['salary'] / 26;
          $income = $recap * $basicIncome;

          if(isset($emp['plus'])){
            $employees[$index]['plus'] = array_sum($emp['plus']) + $income; 
          }
          else{
            $employees[$index]['plus'] = $income;
          }
          
          if(isset($emp['minus'])){
            $employees[$index]['minus'] = array_sum($emp['minus']);
          }
          else{
            $employees[$index]['minus'] = 0;
          }
          
          $income = $income - array_sum($emp['minus'] ?? []);
          $income = $income + array_sum($emp['plus'] ?? []);

          $employees[$index]['thp'] = $income;

        }


        $data['employees'] = $employees;

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

      $emp = $this->db->query("select * from m_pegawai where company_id = $company and is_del = 'n' and pegawai_id = $empId")->row_array();
  
      $awalBulan = date('Y-'.$month.'-01');
      $akhirBulan = date('Y-'.$month.'-t');
      $deduction = $this->db->query("select * from salary_deduction where employee_id = $emp[pegawai_id] and date between '$awalBulan' and '$akhirBulan'")->result_array();
      $emp['deduction'] = $deduction;
     
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

      if($clockoutLatePenalty > 0){
        $emp['minus'][] = ['name' => 'clockout_late', 'value' => $clockoutLatePenalty];
      }
      if($clockoutForget > 0){
        $emp['minus'][] = ['name' => 'clockout_forget', 'value' => $clockoutForget];
      }
      if($alpha2 > 0){
        $emp['minus'][] = ['name' => 'alpha-2', 'value' => $alpha2];
      }
      if($latePenalty > 0){
        $emp['minus'][] = ['name' => 'late_penalty', 'value' => $latePenalty];
      }

      unset($emp['deduction']);
      
      //$emp['minus'] = $deduction;

      foreach($this->db->query("select * from benefit b join employee_benefit eb where b.company_id = ? and eb.employee_id = ?",[$company, $emp['pegawai_id']])->result_array() as $idx => $b){
        if($b['value'] > 0){
          $emp['minus'][] = ['name' => $b['benefit_name'], 'value' => $b['value']];
        }
      }
        
      $recap = $this->db->query("select * from recap where employee_id = ? and date between ? and ? and required = ?",[$emp['pegawai_id'],$awalBulan,$akhirBulan,true])->result_array();
      
      foreach($this->db->query("select * from employee_allowance ea join allowance a on ea.allowance_id = a.allowance_id where ea.employee_id = ?",[$emp['pegawai_id']])->result_array() as $idx => $a){
        if($a['value'] > 0){
          if($a['period'] == 'monthly'){
            if($a['foa']){
              if(count(array_filter($recap, fn($r) => $r['isAlpha'] == 1)) < 1){
                $emp['plus'][] = ['name' => $a['name'], 'value' => $a['value']];
              }
            }
            else{
              $emp['plus'][] = ['name' => $a['name'], 'value' => $a['value']];
            }
          }
          if($a['period'] == 'daily'){
            if($a['boa']){
              $alphaFilter = count(array_filter($recap, fn($r) => $r['isAlpha'] == 1));
              $emp['plus'][] = ['name' => $a['name'], 'value' => $a['value'] * (count($recap) - $alphaFilter)];
            }
            else{
              $emp['plus'][] = ['name' => $a['name'], 'value' => $a['value'] * count($recap)];
            }
          }
        }
      }

      $bulan = date('n'); // 1-12
      $tahun = date('Y');
      $isFebruari = $bulan == 2;
      $isKabisat = checkdate(2, 29, $tahun);
      $recap = $this->db->query("select * from recap where employee_id = ? and date between ? and ? and required = ?",[$emp['pegawai_id'],$awalBulan,$akhirBulan,true])->num_rows();
      $basicIncome = $isFebruari ? ($isKabisat ? $emp['salary'] / 24 : $emp['salary'] / 25) : $emp['salary'] / 26;
      $income = $recap * $basicIncome;

      $emp['income'] = $income;

      if(!isset($emp['minus'])){
        $emp['minus'] = [];
      }
      if(!isset($emp['plus'])){
        $emp['plus'] = [];
      }
      
      $emp['thp'] = $income + array_sum(array_column($emp['plus'], 'value') ?? []) - array_sum(array_column($emp['minus'], 'value') ?? []);

      $data['emp'] = $emp;

      $data['totalMinus'] = array_sum(array_column($emp['minus'], 'value') ?? []);
      
      $data['cmp'] = $this->db->query("select * from companies where id = ?",[$company])->row_array();

      $data['pst'] = $this->db->query("select * from position where id = ?",[$emp['position_id']])->row_array();

 
    
      $this->load->view('templates/header', $data);
      $this->load->view('templates/sidemenu', $data);
      $this->load->view('templates/sidenav', $data);
      $this->load->view('module/salary_record/slip', $data);
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

        foreach($employees as $index => $emp){          
          foreach($this->db->query("select * from benefit b join employee_benefit eb where b.company_id = ? and eb.employee_id = ?",[$company, $emp['pegawai_id']])->result_array() as $idx => $b){
            if($b['value'] > 0){
              $employees[$index]['minus'][$b['benefit_name']] = $b['value'];
            }
          }
        }

        foreach($employees as $index => $emp){  
          $awalBulan = date('Y-'.$month.'-01');
          $akhirBulan = date('Y-'.$month.'-t');        
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
          $awalBulan = date('Y-'.$month.'-01');
          $akhirBulan = date('Y-'.$month.'-t');
          $recap = $this->db->query("select * from recap where employee_id = ? and date between ? and ? and required = ?",[$emp['pegawai_id'],$awalBulan,$akhirBulan,true])->num_rows();
          $basicIncome = $isFebruari ? ($isKabisat ? $emp['salary'] / 24 : $emp['salary'] / 25) : $emp['salary'] / 26;
          $income = $recap * $basicIncome;

          if(isset($emp['plus'])){
            $employees[$index]['plus'] = array_sum($emp['plus']) + $income; 
          }
          else{
            $employees[$index]['plus'] = $income;
          }
          
          if(isset($emp['minus'])){
            $employees[$index]['minus'] = array_sum($emp['minus']);
          }
          else{
            $employees[$index]['minus'] = 0;
          }
          
          $income = $income - array_sum($emp['minus'] ?? []);
          $income = $income + array_sum($emp['plus'] ?? []);

          $employees[$index]['thp'] = $income;

        }


        $data['employees'] = $employees;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/salary_record/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    function toExcel(){
      /*
      |--------------------------------------------------------------------------
      | NAMA FILE
      |--------------------------------------------------------------------------
      */

      $filename = 'payroll-'.date('Y-m-d-H-i-s').'.xls';

      /*
      |--------------------------------------------------------------------------
      | HEADER EXCEL
      |--------------------------------------------------------------------------
      */

        header("Content-Type: application/vnd-ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=$filename");
        header("Pragma: no-cache");
        header("Expires: 0");

      /*
      |--------------------------------------------------------------------------
      | TEMPLATE STYLE
      |--------------------------------------------------------------------------
      */

      echo '
        <html>
        <head>
          <meta charset="UTF-8">

          <style>

                table{
                    border-collapse: collapse;
                    width: 100%;
                }

                th, td{
                    border:1px solid black;
                    padding:5px;
                    font-size:12px;
                }

                .center{
                    text-align:center;
                }

                .middle{
                    vertical-align:middle;
                }

                .bold{
                    font-weight:bold;
                }

                .header{
                    background:#d9d9d9;
                }

                .title{
                    font-size:20px;
                    font-weight:bold;
                    text-align:center;
                }

                .currency{
                    text-align:right;
                }

            </style>
        </head>

        <body>
        ';

        /*
        |--------------------------------------------------------------------------
        | DATA TEMPLATE
        |--------------------------------------------------------------------------
        */

        $employees = [

            [
                'id_staff' => 'STF001',
                'nama' => 'Iqbal',
                'status' => 'Tetap',
                'nik' => '1271160503030001',
                'alamat' => 'Medan',

                'mulai' => '2025-01-01',
                'berakhir' => '-',

                'gaji_pokok' => 5000000,

            ],

            [
                'id_staff' => 'STF002',
                'nama' => 'Andi',
                'status' => 'Kontrak',
                'nik' => '1271160503030002',
                'alamat' => 'Jakarta',

                'mulai' => '2025-02-01',
                'berakhir' => '2026-02-01',

                'gaji_pokok' => 4500000,

            ]

        ];

        /*
        |--------------------------------------------------------------------------
        | TABLE
        |--------------------------------------------------------------------------
        */

        echo '

        <table>

            <tr>
                <th colspan="19" class="title">
                    DATA GAJI KARYAWAN PT. LERYN JAYA MAS
                </th>
            </tr>

            <tr>
                <td colspan="19">
                    Periode : '.date('F Y').'
                </td>
            </tr>

            <tr></tr>

            <tr class="header">

                <th rowspan="2" class="center middle">
                    No.
                </th>

                <th rowspan="2" class="center middle">
                    ID Staff
                </th>

                <th rowspan="2" class="center middle">
                    Nama Sesuai KTP
                </th>

                <th rowspan="2" class="center middle">
                    Status
                </th>

                <th rowspan="2" class="center middle">
                    NIK
                </th>

                <th rowspan="2" class="center middle">
                    Alamat
                </th>

                <th colspan="2" class="center middle">
                    Masa Kerja
                </th>

                <th rowspan="2" class="center middle">
                    Gaji Pokok
                </th>

                <th rowspan="2" class="center middle">
                    Total Gaji
                </th>

            </tr>

            <tr class="header">
                <th class="center">
                    Mulai
                </th>

                <th class="center">
                    Berakhir
                </th>
            </tr>

        ';

        /*
        |--------------------------------------------------------------------------
        | LOOP DATA
        |--------------------------------------------------------------------------
        */

        $no = 1;

        foreach($employees as $e){

            $plus =
                $e['tunjangan'] +
                $e['uang_makan'] +
                $e['insentif'] +
                $e['bonus'];

            $minus =
                $e['bpjs'] +
                $e['absen'] +
                $e['telat'] +
                $e['pph21'];

            $total =
                $e['gaji_pokok'] +
                $plus -
                $minus;

            echo '

            <tr>

                <td class="center">
                    '.$no++.'
                </td>

                <td>
                    '.$e['id_staff'].'
                </td>

                <td>
                    '.$e['nama'].'
                </td>

                <td class="center">
                    '.$e['status'].'
                </td>

                <td>
                    '.$e['nik'].'
                </td>

                <td>
                    '.$e['alamat'].'
                </td>

                <td class="center">
                    '.$e['mulai'].'
                </td>

                <td class="center">
                    '.$e['berakhir'].'
                </td>

                <td class="currency">
                    '.number_format($e['gaji_pokok'],0,",",".").'
                </td>

                <td class="currency bold">
                    '.number_format($total,0,",",".").'
                </td>

            </tr>

            ';
        }

        echo '
        </table>
        </body>
        </html>
        ';
    
    }
}
