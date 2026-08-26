<?php

class Mobile extends CI_Controller{
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

  function leave(){
    $json = file_get_contents('php://input');
    $post = json_decode($json,true);

    $data1 = array(
      "company_id" => $post["company_id"],
      "tipe_request" => $post['tipe_request'],
      "tanggal_request" => $post["tanggal_request"],
      "tanggal_request_end" => $post["tanggal_request_end"],
      "catatan_awal" => $post["catatan_awal"],
      "created_at" => date("Y-m-d H:i:s"),
      "is_status" => 0,
      'image' => $post['image']
    );

    $r1 = $this->db->insert('tx_request_izin',$data1);

    $requestIzinId = $this->db->insert_id();

    $data2 = array(
      "request_izin_id" => $requestIzinId,
      "pegawai_id" => $post["pegawai_id"],
      "tanggal_request" => $post["tanggal_request"],
      "tanggal_request_end" => $post["tanggal_request_end"],
      "catatan_awal" => $post["catatan_awal"],
    );
 
    $this->db->insert('tx_request_izin_pegawai',$data2);

    http_response_code(200);

    echo json_encode(
      [
        "success" => true,
      ]
    );
  }

  function loginadmin(){
    $json = file_get_contents('php://input');
    $json = json_decode($json,true);
		$r1 = $this->db->query("SELECT * FROM m_user WHERE email_address = ? and is_del!='y'", array($json['email_address']))->row_array();
    
    if($r1){
      $r2 = $this->db->query("SELECT * FROM companies WHERE id = ?", array($r1['company_id']))->row_array();


      $isVerified = password_verify($json['password'],$r1['password']);
      
      if($isVerified){
        $r1['company'] = $r2;

        http_response_code(200);

        echo json_encode(
          [
            "success" => true,
            "result" => $r1
          ]
        );
      }
      else{
        http_response_code(200);

        echo json_encode(
          [
            "success" => false,
            "message" => "wrong password",
            "result" => []
          ]
        );
      }
    }
    else{
      http_response_code(200);

      echo json_encode(
        [
          "success" => false,
          "message" => "user not found",
          "result" => []
        ]
      );
    }
  }
  
  function leavelist($pegawaiId){
    $status = $this->db->query("select * from employee_leave_balance where employee_id = ? order by id desc limit 1",[$pegawaiId])->row_array();
    $all = $this->db->query("SELECT * FROM tx_request_izin_pegawai x JOIN tx_request_izin y ON x.request_izin_id = y.request_izin_id WHERE x.pegawai_id = ? and (y.tipe_request = 'c' or y.tipe_request = 's') and tanggal_request between ? and ? order by y.created_at desc",[$pegawaiId,$status['from'], $status['to']])->result_array();
    
    http_response_code(200);

    $result = [
        "list"   => $all,
        "quota"  => (double) $status['quota'],
        "used"   => (double) $status['used'],
    ];

    echo json_encode([
        "success" => true,
        "result"  => $result
    ]);

  }

  function xleavelist($pegawaiId){
    $differencesPending = [];
    $differencesApproved = [];
    $status = $this->db->query("select * from employee_leave_balance where employee_id = ? order by id desc limit 1",[$pegawaiId])->row_array();
    $r1 = $this->db->query("SELECT * FROM m_pegawai WHERE pegawai_id = ?", array($pegawaiId))->row_array();
    $r2 = $this->db->query("SELECT * 
        FROM tx_request_izin_pegawai x 
        JOIN tx_request_izin y 
          ON x.request_izin_id = y.request_izin_id 
        WHERE x.pegawai_id = ? and (y.tipe_request = 'c' or y.tipe_request = 's') order by y.created_at desc",
        [$pegawaiId]
    )->result_array();

    // Filter pending (status 0)
    $filteredPending = array_filter($r2, function($row) {
        return $row['is_status'] == 0;
    });

    // Filter approved (status 1)
    $filteredApproved = array_filter($r2, function($row) {
        return $row['is_status'] == 1;
    });

    // Hitung hari pending
    foreach($filteredPending as $f){
        $tanggalAwal = new DateTime($f['tanggal_request']);
        $tanggalAkhir = new DateTime($f['tanggal_request_end']);
        $difference = $tanggalAwal->diff($tanggalAkhir)->days+1;
        $differencesPending[] = $difference;
    }

    // Hitung hari approved
    foreach($filteredApproved as $f){
        $tanggalAwal = new DateTime($f['tanggal_request']);
        $tanggalAkhir = new DateTime($f['tanggal_request_end']);
        $difference = $tanggalAwal->diff($tanggalAkhir)->days+1;
        $differencesApproved[] = $difference;
    }
    
    if($r1){
        http_response_code(200);

        $result = [
            "list"   => $r2,
            "quota"  => $r1['jumlah_cuti'] - array_sum($differencesPending) , // quota real dikurangi pending (terkunci)
            "used"   => array_sum($differencesApproved) + array_sum($differencesPending), // hanya approved
            "locked" => array_sum($differencesPending)   // pending (opsional, biar user tahu ada cuti terpending)
        ];

        echo json_encode([
            "success" => true,
            "result"  => $result
        ]);
    }
    else{
        http_response_code(200);

        echo json_encode([
            "success" => false,
        ]);
    }
}

function afterbreak(){
  $this->db->trans_begin();

  $now = $now = strtotime(date("Y-m-d H:i"));
  
  $input = file_get_contents('php://input');
  $post = json_decode($input,true);

  $pegawaiId = $post['pegawai_id'];

  $batas = strtotime(date("Y-m-d ").$post['batas']);

  $data1 = [
    'jam_sistirahat' => $post['jam_sistirahat'],
    's_istirahat_photo' => $post['photo'],
    's_istirahat_latitude' => $post['latitude'],
    's_istirahat_longitude' => $post['longitude']
  ];
  
  $emp = $this->db->query("select * from m_pegawai where pegawai_id = ?",[$pegawaiId])->row_array();
  $div = $this->db->query("select * from divisions where id = ?",[$emp['division_id']])->row_array();
  
  $data2 = [
    'deduction_id' => uniqid(),
    'employee_id' => $pegawaiId,
    'deduction_type' => 'after break late',
    'date' => date("Y-m-d"),
    'amount' => $div['after_break_late_penalty_value'],
    'note' => '...'
  ];

  if($now > $batas){
    if($div['after_break_late_penalty_value'] > 0){
      if($div['after_break_late_penalty_type'] == 'fixed'){
        $this->db->where('DATE(tanggal_absen)',date('Y-m-d'));
        $this->db->where('pegawai_id',$pegawaiId);
        $this->db->update('tx_absensi',$data1);
        $this->db->insert('salary_deduction',$data2);
        
        if($this->db->trans_status() === FALSE) {
          $this->db->trans_rollback();

          echo json_encode(
            [
              'success' => false
            ]
          );
        }  
        else {
          $this->db->trans_commit();
          
          echo json_encode(
            [
              'success' => true
            ]
          );
        }
      }
      if($div['after_break_late_penalty_type'] == 'minute'){
        $aBLPV = $div['after_break_late_penalty_value'];
        $diffMinutes = floor(($now - $batas) / 60);
        $deductionValue = $aBLPV * $diffMinutes;

        $data2 = [
          ...$data2,
          'amount' => $deductionValue
        ];

        $this->db->where('DATE(tanggal_absen)',date('Y-m-d'));
        $this->db->where('pegawai_id',$pegawaiId);
        $this->db->update('tx_absensi',$data1);
        $this->db->insert('salary_deduction',$data2);
 
        if($this->db->trans_status() === FALSE) {
          $this->db->trans_rollback();

          echo json_encode(
            [
              'success' => false
            ]
          );
        }  
        else {
          $this->db->trans_commit();
          
          echo json_encode(
            [
              'success' => true
            ]
          );
        }
      }
    }
    else{
      $this->db->where('DATE(tanggal_absen)',date('Y-m-d'));
      $this->db->where('pegawai_id',$pegawaiId);
      $this->db->update('tx_absensi',$data1);
      if($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();

        echo json_encode(
          [
            'success' => false
          ]
        );
      }  
      else {
        $this->db->trans_commit();
          
        echo json_encode(
          [
            'success' => true
          ]
        );
      }    
    }
  }
  else{
    $this->db->where('DATE(tanggal_absen)',date('Y-m-d'));
    $this->db->where('pegawai_id',$pegawaiId);
    $this->db->update('tx_absensi',$data1);
    
    if($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();

      echo json_encode(
        [
          'success' => false
        ]
      );
    }  
    else {
      $this->db->trans_commit();
          
      echo json_encode(
        [
          'success' => true
        ]
      );
    }    
  }
}

function break(){
  $input = file_get_contents('php://input');
  $post = json_decode($input,true);

  $data = ['jam_istirahat' => $post['jam_istirahat']];
  $this->db->where('DATE(tanggal_absen)',date('Y-m-d'));
  $this->db->where('pegawai_id',$post["pegawai_id"]);
  $q = $this->db->update('tx_absensi',$data);

  if($q){
    echo json_encode(
      [
        'success' => true
      ]
    );
  }
  else{
    echo json_encode(
      [
        'success' => false
      ]
    );    
  }
}

function loginv2(){
    $today = date('N');
    $nextDay = $today % 7 + 1;
    $input = file_get_contents('php://input');
    $post = json_decode($input,true);
    $qR1 = "select * from m_pegawai emp join companies c on emp.company_id = c.id join divisions divs on emp.division_id = divs.id where emp.email_pegawai = ? and emp.is_del != 'y'";  
    $r1 = $this->db->query($qR1,[$post['email']])->row_array();
    
    
    if($r1 && password_verify($post['pwd'],$r1['password_pegawai']) ){
        $rFile = $this->db->query(
          "SELECT * 
           FROM employee_file ef 
           JOIN file f ON ef.file_id = f.file_id 
           WHERE ef.employee_id = ? AND f.title = 'Photo'",
          [$r1['pegawai_id']]
        )->row_array();
        
        $r5 = $r1 ? $this->db->query("select * from m_lokasi where company_id = ? and is_del!='y'",array($r1['company_id']))->result_array() : [];
        $companyHolidays = $this->db->query("select * from company_holidays where company_id = ? and curdate() between tanggal and sampai_tanggal",[$r1['company_id']])->row_array();
        $globalHolidays = $this->db->query("select * from global_holidays where company_id = ? and curdate() between tanggal and sampai_tanggal",[$r1['company_id']])->row_array();
        $position = $this->db->query("select * from position where id = ?",[$r1['position_id']])->row_array();
        $workSystem = explode("-",$r1['work_system']);
        
        if ($rFile) $r1['foto_pegawai'] = $rFile['source'];
        
        if($workSystem[0] == "s"){
          $rW = $this->db->query("select * from warning where employeeId = ?",[$r1['pegawai_id']])->result_array();
          $r2 = $this->db->query("select * from shift_detail x join employee_shift y on x.shift_detail_id = y.shift_detail_id  where y.employee_id = ?",[$r1['pegawai_id']])->row_array();
          $r3 = $this->db->query("select * from shift_off where employee_id = ? and day = ?",[$r1['pegawai_id'],$today])->row_array();
        
          $result = [
            "holiday" => $companyHolidays || $globalHolidays,
            'workDay' => $r3 ? false : true,
            "workSystem" => "shift",
            "workSystemName" => $r2['name'],
            "position" => $position['name'],
            "ffocia" => $r1['ffo_check_in_allowed'],
            "ffocoa" => $r1['ffo_check_out_allowed'],
            "ci_limit" => $r1['restriction'],
            "co_limit" => $r1['clockout_restriction'],
            "tolerance" => $r2['tardiness_tolerance'],
            "locations" => $r5,
            "warningList" => $rW,
            ...$r1,
            ...$r2
          ];

          http_response_code(200);

          echo json_encode(
            [
              "success" => true,
              "result" => $result
            ]
          );
      }
      if($workSystem[0] == "wd"){
          $rW = $this->db->query("select * from warning where employeeId = ?",[$r1['pegawai_id']])->result_array();
          $r2 = $this->db->query("select * from m_pola_kerja mpk join m_pola_kerja_det mpkd on mpk.pola_kerja_id = mpkd.pola_kerja_id where mpk.pola_kerja_id = ? and is_day = ?",[$workSystem[1],$today])->row_array();
          $rNext = $this->db->query("select * from m_pola_kerja mpk join m_pola_kerja_det mpkd on mpk.pola_kerja_id = mpkd.pola_kerja_id where mpk.pola_kerja_id = ? and is_day = ?",[$workSystem[1],$nextDay])->row_array();
          
          $result = [
            "holiday" => $companyHolidays || $globalHolidays,
            "workDay" => $r2['is_work'] == "y" ? true : false,
            "workSystemName" => "",
            "workSystem" => "daily",
            "position" => $position['name'],
            "locations" => $r5,
            "next" => $rNext,
            "ffocia" => $r1['ffo_check_in_allowed'],
            "ffocoa" => $r1['ffo_check_out_allowed'],
            "ci_limit" => $r1['restriction'],
            "co_limit" => $r1['clockout_restriction'],
            "tolerance" => $r2['toleransi_terlambat'],
            "warningList" => $rW,
            ...$r1,
            ...$r2
          ];

          http_response_code(200);
          
          echo json_encode(
            [
              "success" => true,
              "result" => $result
            ]
          );
        }
    }
    if($r1 && !(password_verify($post['pwd'],$r1['password_pegawai']))){
      http_response_code(200);
      echo json_encode(["success" => false,"message" => 'user not found']);
    }
    if(!$r1){
      http_response_code(200);
      echo json_encode(["success" => false,"message" => 'user not found']);       
    }
}

function login(){
    $today = date('N');
    $differences1 = [];
    $differences2 = [];
	$json = file_get_contents('php://input');
    $json = json_decode($json,true);
	$r1 = $this->db->query("SELECT * FROM m_pegawai WHERE email_pegawai = ? and is_del!='y'", array($json['email']))->row_array();
	
    if (!$r1) {
      http_response_code(200);
      echo json_encode(["success" => false,"message" => "user not found"]);
      return;
    }
    
    if($r1){
      $id = $r1['pegawai_id'];
      $r2 = $this->db->query("select * from companies where id=?",array($r1['company_id']))->row_array();
      $r3 = $this->db->query("select * from m_pegawai_pola where pegawai_id = ?", array($r1['pegawai_id']))->row_array();
      $r4 = $this->db->query("select * from m_pola_kerja_det where pola_kerja_id = ? and is_day = ?",array($r3['pola_kerja_id'], $today))->row_array();
      $r5 = $this->db->query("select * from m_lokasi where company_id = ? and is_del!='y'",array($r1['company_id']))->result_array();
      $r6 = $this->db->query("select * from tx_request_izin_pegawai x join tx_request_izin y on x.request_izin_id = y.request_izin_id and x.pegawai_id = ? and y.tipe_request = 'c'",[$id]);
      $r6 = $r6->result_array();
      
      $filtered1 = array_filter($r6, function($row) {
        return $row['is_status'] == 0;
      });

	  $filtered1 = array_values($filtered1);

	  foreach($filtered1 as $f){
        $tanggalAwal = new DateTime($f['tanggal_request']);
        $tanggalAkhir = new DateTime($f['tanggal_request_end']);
        $difference = $tanggalAwal->diff($tanggalAkhir)->days+1;
        $differences1[] = $difference;
      }

      $filtered2 = array_filter($r6, function($row) {
        return $row['is_status'] == 2;
      });

	  $filtered2 = array_values($filtered2);

	  foreach($filtered2 as $f){
        $tanggalAwal = new DateTime($f['tanggal_request']);
        $tanggalAkhir = new DateTime($f['tanggal_request_end']);
        $difference = $tanggalAwal->diff($tanggalAkhir)->days+1;
        $differences2[] = $difference;
      }
     
      $isVerified = password_verify($json['pwd'],$r1['password_pegawai']);
      
      if($isVerified){
        http_response_code(200);

        $r1['company'] = $r2;
        $r1['pattern'] = $r4;
        $r1['locations'] = $r5;
        $r1['permission'] = 0;

        $r1['jumlah_cuti'] = ($r1['jumlah_cuti'] - array_sum($differences1)) + array_sum($differences2);
        $r1['cuti_terpakai'] = array_sum($differences1);

        echo json_encode(
          [
            "success" => true,
            "result" => $r1
          ]
        );
      }
      else{
        http_response_code(200);

        echo json_encode(
          [
            "success" => false,
            "message" => "user not found",
            "result" => []
          ]
        );
      }
    }
  }

  function signout(){
    $today = date('N');
    $tanggalHariIni = date('Y-m-d');
    $json = file_get_contents('php://input');
    $post = json_decode($json,true);
    
    $data = array(
      "jam_keluar" => $post["jam_keluar"],
      "foto_absen_keluar" => $post["foto_absen_keluar"],
      "latitude_keluar" => $post["latitude_keluar"],
      "longitude_keluar" => $post["longitude_keluar"],
      "mocked_out" => $post["is_mock"] ?? false
    );

    $emp = $this->db->query("select * from m_pegawai where pegawai_id = ?",[$post['pegawai_id']])->row_array();
    $division = $this->db->query("select * from divisions where id = ?",[$emp['division_id']])->row_array();
    $lastDefaultStatus = $this->db->query("SELECT * FROM tx_absensi where pegawai_id = ? ORDER BY tanggal_absen DESC LIMIT 1",[$post['pegawai_id']])->row_array();
    $exception = $this->db->query("select * from exception where employee_id = ? and status = ? and date = ? and type = ?",[$post['pegawai_id'],1,date('Y-m-d'),'Lupa absen'])->row_array();

    $workSystem = explode("-",$division['work_system']);

    if(!$emp['signed_in']){
      http_response_code(200);

      echo json_encode(
        [
          "success" => false,
          "message" => "user is not signed in",
          "result" => []
        ]
      );
      return;
    }
    else{
      if($workSystem[0] == "s"){
        $serverDate = new DateTime();
        $shift = $this->db->query("select * from employee_shift es join shift_detail sd on es.shift_detail_id = sd.shift_detail_id where employee_id = ?",[$post['pegawai_id']])->row_array();
        $dateTime1 = new DateTime($lastDefaultStatus['tanggal_absen']. ' ' . $shift['clock_out']);
        $tolerance = (clone $dateTime1)->modify("+{$shift['tardiness_tolerance']} minutes");
        $limit = $dateTime1->modify("+{$division['clockout_restriction']} minutes");
        $sDTDiff = $serverDate->diff($tolerance);
        $sDTDiffMinutes = ($sDTDiff->days * 24 * 60) + ($sDTDiff->h * 60) + $sDTDiff->i;

        if($serverDate > $limit){
          if($exception){
            $dataAbsensi = !$post['csh'] ? $data : [
              ...$data,
              'is_status' => 'csh'
            ];
            
            $this->db->where('pegawai_id',$post["pegawai_id"]);
            $this->db->where('absen_id',$lastDefaultStatus["absen_id"]);
            $q2 = $this->db->update('tx_absensi',$dataAbsensi);
            if($q2){
              $this->db->where('pegawai_id',$post['pegawai_id']);
              $this->db->update('m_pegawai',['signed_in' => false]);
              echo json_encode(
                [
                  "success" => true,
                  "late" => false
                ]
              );

              return;
            }
            else{
              echo json_encode(
                  [
                    "success" => false,
                    "message" => "gagal request absen pulang"
                  ]
              );
              return;
            }
          }
          else{
            echo json_encode(
              [
                "success" => false,
                "message" => "kamu sudah tidak bisa absen pulang"
              ]
            ); 
          }
        }
        
        if($serverDate < $limit && $serverDate > $tolerance){
          if($division['clockout_penalty']){
            $data1 = [
              'id' => uniqid(),
              'employee_id' => $post['pegawai_id'],
              'deduction_type' => 'clockout late penalty',
              'date' => date('Y-m-d'),
              'amount' => $division['penalty_nominal'],
              'note' => '...'
            ];

            $this->db->trans_begin();

            $this->db->where('absen_id',$lastDefaultStatus["absen_id"]);

            $dataAbsensi = !$post['csh'] ? $data : [
              ...$data,
              'is_status' => 'csh'
            ];
          
            $q2 = $this->db->update('tx_absensi',$dataAbsensi);

            
            if($q2){
              $this->db->where('pegawai_id',$post['pegawai_id']);
              $this->db->update('m_pegawai',['signed_in' => false]);
              
              $this->db->trans_commit();

              echo json_encode(
                [
                  "success" => true,
                  "late" => true,
                  "late_diff" => $sDTDiffMinutes
                ]
              );

              return;
            }
            else{
              $this->db->trans_rollback();
              echo json_encode(
                [
                  "success" => false,
                  "message" => "gagal request absen pulang"
                ]
              );

              return;
            }
          }
          else{
            $dataAbsensi = !$post['csh'] ? $data : [
              ...$data,
              'is_status' => 'csh'
            ];
          
            $this->db->where('absen_id',$lastDefaultStatus["absen_id"]);
            $q2 = $this->db->update('tx_absensi',$dataAbsensi);

            if($q2){
              $this->db->where('pegawai_id',$post['pegawai_id']);
              $this->db->update('m_pegawai',['signed_in' => false]);
              echo json_encode(
                [
                  "success" => true,
                  "late" => true,
                  "late_diff" => $sDTDiffMinutes
                ]
              );
            }
            else{
              echo json_encode(
                [
                  "success" => false,
                  "message" => "gagal request absen pulang"
                ]
              );

              return;
            }
          }
        }
        if($serverDate < $tolerance){
          if($serverDate < new DateTime(date('Y-m-d') . ' ' . $pattern['jam_pulang'])){
            if($post['pegawai_id'] != '107'){
              echo json_encode(
                [
                  "success" => false,
                  "message" => "kamu belum bisa absen pulang sekarang"
                ]
              );

              return;   
            } 
            else{
              $dataAbsensi = !$post['csh'] ? $data : [
                ...$data,
                'is_status' => 'csh'
              ];
        
              $this->db->where('pegawai_id',$post["pegawai_id"]);
              $this->db->where('tanggal_absen',$tanggalHariIni);
              $q = $this->db->update('tx_absensi',$Absensi);

              if($q){
                $this->db->where('pegawai_id',$post['pegawai_id']);
                $this->db->update('m_pegawai',['signed_in' => false]);
                echo json_encode(
                  [
                    "success" => true,
                    "late" => false
                  ]
                );

                return;
              }
              else{
                echo json_encode(
                  [
                    "success" => false,
                    "message" => "gagal request absen pulang"
                  ]
                );
                return;
              }            
            }      
          }
          else{
            $dataAbsensi = !$post['csh'] ? $data : [
              ...$data,
              'is_status' => 'csh'
            ];
        
            $this->db->where('pegawai_id',$post["pegawai_id"]);
            $this->db->where('tanggal_absen',$tanggalHariIni);
            $q = $this->db->update('tx_absensi',$Absensi);

            if($q){
              $this->db->where('pegawai_id',$post['pegawai_id']);
              $this->db->update('m_pegawai',['signed_in' => false]);
              echo json_encode(
                [
                  "success" => true,
                  "late" => false
                ]
              );

              return;
            }
            else{
              echo json_encode(
                [
                  "success" => false,
                  "message" => "gagal request absen pulang"
                ]
              );
              return;
            }
          }
        }
      }
      else{
        $serverDate = new DateTime();
        $pattern = $this->db->query("select * from m_pola_kerja mpk join m_pola_kerja_det mpkd on mpk.pola_kerja_id = mpkd.pola_kerja_id where mpk.pola_kerja_id = ? and is_day = ?",[$workSystem[1],$today])->row_array();
        $dateTime1 = new DateTime($lastDefaultStatus['tanggal_absen']. ' ' . $pattern['jam_pulang']);
        $tolerance = (clone $dateTime1)->modify("+{$pattern['toleransi_terlambat']} minutes");
        $limit = $dateTime1->modify("+{$division['clockout_restriction']} minutes");
        $sDTDiff = $serverDate->diff($tolerance);
        $sDTDiffMinutes = ($sDTDiff->days * 24 * 60) + ($sDTDiff->h * 60) + $sDTDiff->i;

        if($serverDate > $limit){
          if($exception){
            $dataAbsensi = !$post['csh'] ? $data : [
              ...$data,
              'is_status' => 'csh'
            ];
            
            $this->db->where('pegawai_id',$post["pegawai_id"]);
            $this->db->where('absen_id',$lastDefaultStatus["absen_id"]);
            $q2 = $this->db->update('tx_absensi',$dataAbsensi);
            if($q2){
              $this->db->where('pegawai_id',$post['pegawai_id']);
              $this->db->update('m_pegawai',['signed_in' => false]);
              echo json_encode(
                [
                  "success" => true,
                  "late" => false
                ]
              );

              return;
            }
            else{
              echo json_encode(
                  [
                    "success" => false,
                    "message" => "coba beberapa saat lagi"
                  ]
              );
              return;
            }
          }
          else{
            echo json_encode(
              [
                "success" => false,
                "message" => "kamu sudah tidak bisa absen pulang"
              ]
            ); 
          }
        }

        if($serverDate < $limit && $serverDate > $tolerance){
          if($division['clockout_penalty']){
            $data1 = [
              'id' => uniqid(),
              'employee_id' => $post['pegawai_id'],
              'deduction_type' => 'clockout late penalty',
              'date' => date('Y-m-d'),
              'amount' => $division['penalty_nominal'],
              'note' => '...'
            ];

            $this->db->trans_begin();

            $dataAbsensi = !$post['csh'] ? $data : [
              ...$data,
              'is_status' => 'csh'
            ];

            $this->db->where('absen_id',$lastDefaultStatus["absen_id"]);
          
            $q2 = $this->db->update('tx_absensi',$dataAbsensi);


            if($q2){
              $this->db->where('pegawai_id',$post['pegawai_id']);
              $this->db->update('m_pegawai',['signed_in' => false]);
              $this->db->trans_commit();

              echo json_encode(
                [
                  "success" => true,
                  "late" => true,
                  "late_diff" => $sDTDiffMinutes
                ]
              );

              return;
            }
            else{
              $this->db->trans_rollback();
              echo json_encode(
                [
                  "success" => false,
                  "message" => "coba beberapa saat lagi"
                ]
              );

              return;
            }
          }
          else{
            $dataAbsensi = !$post['csh'] ? $data : [
              ...$data,
              'is_status' => 'csh'
            ];

            $this->db->where('absen_id',$lastDefaultStatus["absen_id"]);
          
            $q2 = $this->db->update('tx_absensi',$dataAbsensi);

            if($q2){
              $this->db->where('pegawai_id',$post['pegawai_id']);
              $this->db->update('m_pegawai',['signed_in' => false]);
              echo json_encode(
                [
                  "success" => true,
                  "late" => true,
                  "late_diff" => $sDTDiffMinutes
                ]
              );

              return;
            }
            else{
              echo json_encode(
                [
                  "success" => false,
                  "message" => "coba beberapa saat lagi"
                ]
              );

              return;
            }
          }
        }
        if($serverDate < $tolerance){
          if($serverDate < new DateTime(date('Y-m-d') . ' ' . $pattern['jam_pulang'])){
            if($post['pegawai_id'] != '107'){
              echo json_encode(
                [
                  "success" => false,
                  "message" => "coba beberapa saat lagi ya"
                ]
              );
              return;  
            }
            else{
              $dataAbsensi = !$post['csh'] ? $data : [
                ...$data,
                'is_status' => 'csh'
              ];
            
              $this->db->where('pegawai_id',$post["pegawai_id"]);
              $this->db->where('absen_id',$lastDefaultStatus["absen_id"]);
              $q2 = $this->db->update('tx_absensi',$dataAbsensi);
              if($q2){
                $this->db->where('pegawai_id',$post['pegawai_id']);
                $this->db->update('m_pegawai',['signed_in' => false]);
                echo json_encode(
                  [
                    "success" => true,
                    "late" => false
                  ]
                );

                return;
              }
              else{
                echo json_encode(
                  [
                    "success" => false,
                    "message" => "coba beberapa saat lagi"
                  ]
                );
                return;
              }            
            }         
          }
          else{
            $dataAbsensi = !$post['csh'] ? $data : [
              ...$data,
              'is_status' => 'csh'
            ];
            
            $this->db->where('pegawai_id',$post["pegawai_id"]);
            $this->db->where('absen_id',$lastDefaultStatus["absen_id"]);
            $q2 = $this->db->update('tx_absensi',$dataAbsensi);
            if($q2){
              $this->db->where('pegawai_id',$post['pegawai_id']);
              $this->db->update('m_pegawai',['signed_in' => false]);
              echo json_encode(
                [
                  "success" => true,
                  "late" => false
                ]
              );

              return;
            }
            else{
              echo json_encode(
                  [
                    "success" => false,
                    "message" => "coba beberapa saat lagi"
                  ]
              );
              return;
            }
          }
        }
      }
    }
  }
  
  function signin(){
    $today = date('N');
    $tanggalHariIni = date('Y-m-d');
    $json = file_get_contents('php://input');
    $post = json_decode($json,true);
    

    $data2 = array(
      "is_status" => $post["is_status"],
      "jam_masuk" => $post["jam_masuk"],
      "foto_absen_masuk" => $post["foto_absen_masuk"],
      "point_latitude" => $post["point_latitude"],
      "point_longitude" => $post["point_longitude"],
      "latitude_masuk" => $post["latitude_masuk"],
      "longitude_masuk" => $post["longitude_masuk"],
      "mocked_in" => $post["is_mock"] ?? false
    );

    $emp = $this->db->query("select * from m_pegawai where pegawai_id = ?",[$post['pegawai_id']])->row_array();
    $division = $this->db->query("select * from divisions where id = ?",[$emp['division_id']])->row_array();
    $lastDefaultStatus = $this->db->query("SELECT * FROM tx_absensi where pegawai_id = ? ORDER BY tanggal_absen DESC LIMIT 1",[$post['pegawai_id']])->row_array();

    $workSystem = explode("-",$division['work_system']);
    
    

    if(!in_array($lastDefaultStatus['is_status'], ['free', 'c', 'i', 's','off'])){
        $serverDate = new DateTime();
        $dateTime1 = new DateTime($lastDefaultStatus['tanggal_absen']. ' ' . $lastDefaultStatus['jam_masuk']);
        $tolerance = new DateTime($lastDefaultStatus['tanggal_absen']. ' ' . $lastDefaultStatus['j_toleransi']);
        $limit = (clone $tolerance)->modify("+{$division['restriction']} minutes");
        if(!$emp['signed_in']){
            if($serverDate > $limit || $serverDate < $limit && $serverDate > $tolerance){
                $empId = $post['pegawai_id'];
                $params = [$empId,1,date('Y-m-d'),1];
                $data2 = [...$data2,'isLate' => false];
        
                $e1 = $this->db->query("select * from exception where employee_id = ? and status = ? and date = ? and is_csh = ?",$params);  
                $e2 = $this->db->query("select * from exception where employee_id = ? and status = ? and date = ? and type = ?",[$empId,1,date('Y-m-d'),'Terlambat'])->num_rows() > 0;
                
                
                if($serverDate > $limit){
                    if($e1->num_rows() > 0 || $e2){
                        if($e2){
                            if($division['late_penalty']){
                                $deduction = [
                                    'deduction_id' => uniqid(),
                                    'employee_id' => $empId,
                                    'deduction_type' => 'late penalty',
                                    'date' => date('Y-m-d'),
                                    'amount' => $division['penalty_nominal'],
                                    'note' => '...'
                                ];
                          
                                $this->db->insert(
                                    'salary_deduction',
                                    $deduction
                                );
                            }
                            $this->db->where('absen_id',$lastDefaultStatus["absen_id"]);
                            $this->db->update('tx_absensi',[...$data2,'isLate' => true]);
                            echo json_encode(["success" => true,"message" => ""]);
                            return;

                        }
                        
                        // works
                        
                        else{
                            $this->db->where('absen_id',$lastDefaultStatus["absen_id"]);
                            $this->db->update('tx_absensi',[...$data2,'is_status' => 'csh']);
                            $this->db->where('pegawai_id',$post['pegawai_id']);
                            $this->db->update('m_pegawai',['signed_in' => true]);
                            echo json_encode(["success" => true,"message" => ""]);
                        }
                        
                        // works

                  
                    }
                    else{
                        echo json_encode([
                          'success' => false,
                          'message' => 'restricted'
                        ]);
                    }
                }
                
                // works
                
                if($serverDate < $limit && $serverDate > $tolerance){
                    if($division['late_penalty']){
                        $deduction = [
                            'deduction_id' => uniqid(),
                            'employee_id' => $empId,
                            'deduction_type' => 'late penalty',
                            'date' => date('Y-m-d'),
                            'amount' => $division['penalty_nominal'],
                            'note' => '...'
                        ];
                  
                        $this->db->insert(
                            'salary_deduction',
                            $deduction
                        );
                    }
                    
                    $this->db->where('absen_id',$lastDefaultStatus["absen_id"]);
                    $this->db->update('tx_absensi',[...$data2,'isLate' => true]);
                    $this->db->where('pegawai_id',$post['pegawai_id']);
                    $this->db->update('m_pegawai',['signed_in' => true]);
                    echo json_encode(["success" => true,"message" => ""]); 
                    return;
                }
                
                // works
            }
            if($serverDate < $tolerance){
                if($serverDate < $dateTime1->modify('-1 hour')){
                    echo json_encode(
                        [
                            "success" => false,
                            "message" => "tidak bisa absen masuk sebelum waktunya"
                        ]
                    );
                  
                    return;
                }
                else{
                    $this->db->where('pegawai_id',$post["pegawai_id"]);
                    $this->db->where('tanggal_absen',$tanggalHariIni);
                    $q = $this->db->update('tx_absensi',$data2);
                    $this->db->where('pegawai_id',$post['pegawai_id']);
                    $this->db->update('m_pegawai',['signed_in' => true]);
                    echo json_encode(["success" => true,"message" => ""]);
                    return;
                }
              }
        }
        else{
          echo json_encode(
            [
              "success" => false,
              "message" => "contact administrator to end previous session",
            ]
          );
          
          return;
        }
    }
    else{
      http_response_code(200);

      echo json_encode(
        [
          "success" => false,
          "message" => "today is not a working day",
          "result" => []
        ]
      );
      
      return;
    }

  }

  function location(){
    $r = $this->db->query("select * from m_lokasi")->result_array();
    
    http_response_code(200);

    echo json_encode(
      [
        "result" => $r
      ]
    );
  }

  function gstpl($pegawaiId){
    $r = $this->db->query("SELECT * FROM tx_request_izin a join tx_request_izin_pegawai b on a.request_izin_id=b.request_izin_id where b.pegawai_id=$pegawaiId and a.tipe_request='csh'")->result_array();

    if($r){
      http_response_code(200);

      echo json_encode(
        [
          "success" => true,
          "result" => $r
        ]
      );
    }
    else{
      http_response_code(500);

      echo json_encode(
        [
          "success" => false,
          "result" => []
        ]
      );
    }
  }

  function gpl($pegawaiId){
    $r = $this->db->query("SELECT * FROM tx_request_izin a join tx_request_izin_pegawai b on a.request_izin_id=b.request_izin_id where b.pegawai_id=$pegawaiId and a.tipe_request!='csh'")->result_array();

    if($r){
      http_response_code(200);

      echo json_encode(
        [
          "success" => true,
          "result" => $r
        ]
      );
    }
    else{
      http_response_code(500);

      echo json_encode(
        [
          "success" => false,
          "result" => []
        ]
      );
    }
  }

  function setleave(){
    $json = file_get_contents('php://input');
    $post = json_decode($json,true);
    $data = array("r_absen_keluar" => $post["r_absen_keluar"]);
    $this->db->where('request_izin_id',$post["request_izin_id"]);
    $this->db->update('tx_request_izin',$data);
    $this->db->where('request_izin_id',$post["request_izin_id"]);
    $this->db->update('tx_request_izin_pegawai',$data);
  }

  function setcomeback(){
    $json = file_get_contents('php://input');
    $post = json_decode($json,true);
    $data = array("r_absen_masuk" => $post["r_absen_masuk"]);
    $this->db->where('request_izin_id',$post["request_izin_id"]);
    $this->db->update('tx_request_izin',$data);
    $this->db->where('request_izin_id',$post["request_izin_id"]);
    $this->db->update('tx_request_izin_pegawai',$data);
  }

  function afp(){
    $json = file_get_contents('php://input');
    $post = json_decode($json,true);

    $data1 = array(
      "company_id" => $post["company_id"],
      "tipe_request" => $post["tipe_request"],
      "tanggal_request" => $post["tanggal_request"],
      "tanggal_request_end" => $post["tanggal_request_end"],
      "catatan_awal" => $post["catatan_awal"],
      "created_at" => date("Y-m-d H:i:s"),
      "is_status" => 0,
      'image' => $post['image']
    );

    $r1 = $this->db->insert('tx_request_izin',$data1);

    $requestIzinId = $this->db->insert_id();

    $data2 = array(
      "request_izin_id" => $requestIzinId,
      "pegawai_id" => $post["pegawai_id"],
      "tanggal_request" => $post["tanggal_request"],
      "tanggal_request_end" => $post["tanggal_request_end"],
      "catatan_awal" => $post["catatan_awal"],
    );
 
    $this->db->insert('tx_request_izin_pegawai',$data2);

    http_response_code(200);

    echo json_encode(
      [
        "success" => true,
      ]
    );
  }

  function csh(){
    $json = file_get_contents('php://input');
    $post = json_decode($json,true);

    $data1 = array(
      "company_id" => $post["company_id"],
      "tipe_request" => "csh",
      "tanggal_request" => $post["tanggal_request"],
      "tanggal_request_end" => $post["tanggal_request_end"],
      "r_jam_masuk" => $post["r_jam_masuk"],
      "r_jam_keluar" => $post["r_jam_keluar"],
      "catatan_awal" => $post["catatan_awal"],
      "created_at" => date("Y-m-d H:i:s"),
      "is_status" => 0,
    );

    $r1 = $this->db->insert('tx_request_izin',$data1);
    
    $requestIzinId = $this->db->insert_id();

    $data2 = array(
      "request_izin_id" => $requestIzinId,
      "pegawai_id" => $post["pegawai_id"],
      "tanggal_request" => $post["tanggal_request"],
      "tanggal_request_end" => $post["tanggal_request_end"],
      "catatan_awal" => $post["catatan_awal"],
    );


    $this->db->insert('tx_request_izin_pegawai',$data2);

    http_response_code(200);

    echo json_encode(
      [
        "success" => true,
      ]
    );
  }


  
  function filteredTaskList($id,$filter){
    $q = $this->db->query("select * from task t join task_detail td on t.task_id = td.task_id where t.employee_id = ? and t.date = ? order by created_at desc",[$id,$filter])->result_array();

    if($q){
      echo json_encode(
        [
          'success' => true,
          'result' => $q
        ]
      );
    }
    else{
      echo json_encode(
        [
          'success' => false,
        ]
      );
    }
  }

  function taskList($id){
    $yesterday =  date('Y-m-d', strtotime('-1 day')); 
      
    $q = $this->db->query("select * from task where employee_id = ? and date between ? and ? order by created_at desc",[$id,$yesterday,date('Y-m-d')])->result_array();

    if($q){
      echo json_encode(
        [
          'success' => true,
          'result' => $q
        ]
      );
    }
    else{
      echo json_encode(
        [
          'success' => false,
        ]
      );
    }
  }
  
  function doneTaskList($id){
    $q = $this->db->query("select * from task t join task_detail td on t.task_id = td.task_id where t.employee_id = ? and t.date = ?  order by t.created_at desc",[$id,date('Y-m-d')])->result_array();

    if($q){
      echo json_encode(
        [
          'success' => true,
          'result' => $q
        ]
      );
    }
    else{
      echo json_encode(
        [
          'success' => false,
        ]
      );
    }
  }
  
  public function taskfinish(){
    $json = file_get_contents('php://input');
    $post = json_decode($json,true);
    
    $data = [  
      "finish_location" => $post['finish_location'],
      "finish_photo" => $post['finish_photo'],
      "finish_time" => Date("H:i"),
      "mocked_in" => $post["is_mock"] ?? false
    ];

    $this->db->where('task_id',$post['task_id']);
    $q = $this->db->update('task_detail',$data);
    
    if($q){
      echo json_encode(
        [
          "success" => true
        ]
      );
    }
    else{
      echo json_encode(
        [
          "success" => false,
          "message" => "coba beberapa saat lagi"
        ]
      );
    }     
  }
  
  public function taskstart(){
    $json = file_get_contents('php://input');
    $post = json_decode($json,true);
    
    $data = [
      "task_detail_id" => uniqid(),
      "task_id" => $post["task_id"],
      "start_photo" => $post["start_photo"],
      "start_location" => $post["start_location"],
      "start_time" => date('H:i'),
      "finish_location" => "0/0",
      "finish_photo" => "-",
      "finish_time" => "00:00",
      "mocked_in" => $post["is_mock"] ?? false
    ];

    $q = $this->db->insert(
      "task_detail",$data
    );

    if($q){
      echo json_encode(
        [
          "success" => true
        ]
      );
    }
    else{
      echo json_encode(
        [
          "success" => false,
          "message" => "coba beberapa saat lagi"
        ]
      );
    }     
  }

  function makeTask(){
    $json = file_get_contents('php://input');
    $post = json_decode($json,true);
     
     $data = array(
      "task_id" => uniqid(),
      "date" => $post['date'],
      "employee_id" => $post["employee_id"],
      "description" => $post["description"],
      "created_at" => date('Y-m-d H:i:s')
    );

    $q = $this->db->insert(
      'task',$data
    );

    if($q){
      echo json_encode(
        [
          "success" => true
        ]
      );
    }
    else{
      echo json_encode(
        [
          "success" => false,
          "message" => "coba beberapa saat lagi"
        ]
      );
    }     
  }


  function makeexception(){
     $json = file_get_contents('php://input');
     $post = json_decode($json,true);
     
     $data = array(
      "id" => uniqid(),
      "date" => date('Y-m-d'),
      "employee_id" => $post["employee_id"],
      "reason" => $post["reason"],
      "status" => 0,
      "created_at" => date('Y-m-d H:i:s'),
      "type" => $post["type"],
      "image" => $post['image'],
      "is_csh" => $post["isCsh"],
      "requested_at" => date('H:i')
    );

    $q = $this->db->insert(
      'exception',$data
    );

    if($q){
      echo json_encode(
        [
          "success" => true
        ]
      );
    }
    else{
      echo json_encode(
        [
          "success" => false,
          "message" => "coba beberapa saat lagi"
        ]
      );
    }     
  }

  public function makeclaim(){
     $json = file_get_contents('php://input');
     $post = json_decode($json,true);
     
     $data = array(
      "reimburse_claim_id" => uniqid(),
      "date" => date("Y-m-d H:i:s") ,
      "employee_id" => $post["employee_id"],
      "value" => $post["value"],
      "reimburse_id" => $post['reimburse_id'],
      "photo" => $post['photo'],
      "status" => "pending"
    );

    $q = $this->db->insert(
      'reimburse_claim',$data
    );

    if($q){
      echo json_encode(
        [
          "success" => true
        ]
      );
    }
    else{
      echo json_encode(
        [
          "success" => false,
          "message" => "coba beberapa saat lagi"
        ]
      );
    }     
  }

  function ovwList($id){
    $q = $this->db->query("select * from employee_overwork eo join employee_overwork_detail eod on eo.employee_overwork_id = eod.employee_overwork_id where eo.employee_id = ? order by eo.date desc",[$id])->result_array();

    if($q){
      echo json_encode(
        [
          'success' => true,
          'result' => $q
        ]
      );
    }
    else{
      echo json_encode(
        [
          'success' => false,
        ]
      );
    }
  }

  function excList($id){
    $q = $this->db->query("select * from exception where employee_id = ? and date = ? order by created_at desc",[$id,date('Y-m-d')])->result_array();

    if($q){
      echo json_encode(
        [
          'success' => true,
          'result' => $q
        ]
      );
    }
    else{
      echo json_encode(
        [
          'success' => false,
        ]
      );
    }
  }

  function exceptionEdit(){
    $json = file_get_contents('php://input');
    $post = json_decode($json,true);
    $exceptionId = $post['id'];

    $data = [
      'date' => $post['date'],
      'reason' => $post['reason'],
      'type' => $post['type'],
      'image' => $post['image'],
    ];

    $this->db->where('id',$exceptionId);
    $q = $this->db->update('exception',$data);

    if($q){
      echo json_encode(
        [
          'success' => true
        ]
      );
    }
    else{
      echo json_encode(
        [
          'success' => false
        ]
      );
    }
  }

  function calendar($empId,$date){
    $eventDate = explode("-",$date);
    $date = new DateTime($eventDate[0] . '-' . $eventDate[1] . '-' . $eventDate[2]);
    $oDB = (clone $date)->modify("-1 day")->format("Y-m-d");
    $oDA = (clone $date)->modify("+1 day")->format("Y-m-d");
    $date = $date->format('Y-m-d');

    $q1 = $this->db->query("select * from m_pegawai where pegawai_id = ?",[$empId])->row_array();
    $q2 = $this->db->query("select * from tx_request_izin_pegawai trip join tx_request_izin tri on tri.request_izin_id = trip.request_izin_id where ? between  tri.tanggal_request and tri.tanggal_request_end and trip.pegawai_id = ?",[$date,$empId])->row_array();
    //$q2 = $this->db->query("select * from tx_request_izin_pegawai trip join tx_request_izin tri on tri.request_izin_id = trip.request_izin_id where ((tri.tanggal_request between ? and ?) or (tri.tanggal_request_end = ?)) and trip.pegawai_id = ?",[$oDB,$oDA,$date,$empId])->result_array();
    $q3 = $this->db->query("select * from global_holidays where ? between tanggal and sampai_tanggal and company_id = ?",[$date,$q1['company_id']])->row_array();
    //$q3 = $this->db->query("select * from global_holidays gh where ((gh.tanggal between ? and ?) or (gh.sampai_tanggal = ?)) and company_id = ?",[$oDB,$oDA,$date,$q1['company_id']])->result_array();
    $q4 = $this->db->query("select * from company_holidays where ? between tanggal and sampai_tanggal and company_id = ?",[$date,$q1['company_id']])->row_array();
    //$q4 = $this->db->query("select * from company_holidays gh where ((gh.tanggal between ? and ?) or (gh.sampai_tanggal = ?)) and company_id = ?",[$oDB,$oDA,$date,$q1['company_id']])->result_array();


    // $q2 = $this->db->query("select * from tx_request_izin_pegawai trip join tx_request_izin tri on tri.request_izin_id = trip.request_izin_id where (tri.tanggal_request between ? and ?) or (tri.tanggal_request_end between ? and ?) and trip.pegawai_id = ?",[$oDB,$oDA,$oDB,$oDA,$empId])->result_array();
    // $q3 = $this->db->query("select * from global_holidays gh where (gh.tanggal between ? and ?) or (gh.sampai_tanggal between ? and ?) and company_id = ?",[$oDB,$oDA,$oDB,$oDA,$q1['company_id']])->result_array();
    // $q4 = $this->db->query("select * from company_holidays gh where (gh.tanggal between ? and ?) or (gh.sampai_tanggal between ? and ?) and company_id = ?",[$oDB,$oDA,$oDB,$oDA,$q1['company_id']])->result_array();
    
    $globalHolidays = $q3 == null ? [] : [$q3];
    $companyHolidays = $q4 == null ? [] : [$q4];
    $permission = $q2 == null ? [] : [$q2];
    

    echo json_encode(
      [
        'permission' => $permission,
        'globalHolidays' => $globalHolidays,
        'companyHolidays' => $companyHolidays
      ]
    );

  }
  
  function salary($empId,$dateX,$dateY){
    $alphaDeductionValue = 0;
    $deductionValue = 0;
    $attendanceCount = 0;
    $alphaCount = 0;
    $totalAllowance = 0;
    $totalBenefit = 0;
    $totalOverwork = 0;
    $offDays = 0;
    $income = [];
    $benefit = [];
    $penalty = [];
    
    $emp = $this->db->query("select * from m_pegawai where pegawai_id = ?",[$empId])->row_array();
    $company = $this->db->query("select * from companies where id = ?",[$emp['company_id']])->row_array();
    $attendance = $this->db->query("select * from tx_absensi where tanggal_absen between ? and ? and pegawai_id = ?",[$dateX,$dateY,$empId])->result_array();
    $recap = $this->db->query("SELECT * FROM recap WHERE date BETWEEN ? AND ? AND employee_id = ? and required= ?",[$dateX,$dateY,$empId,true])->num_rows();

    foreach($attendance as $a){
      if($a['is_status'] == 'alpha-2'){
        $alphaCount += 1;
      }
      if($a['is_status'] == "hhk"){
        $attendanceCount += 1;
      }
      if($a['is_status'] == "off"){
        $offDays += 1;
      }
    }

    if (date('m') === '02') {
      $emp['salary'] = $recap * $emp['salary'] / 24;
    }
    else{
      $emp['salary'] = $recap * $emp['salary'] / 26;
    }
    
    // if($emp['salary_config'] == 'full_month'){
    //   if(date('d') != $company['salary_date']){
    //     $emp['salary'] = $recap * $emp['salary'] / 26;
    //   }
    // }
    // else{
    //   $emp['salary'] = $recap * $emp['salary'] / 26;
    // }

    $illPenalty = $this->db->query("select sum(amount) as amt from salary_deduction where employee_id = ? and date between ? and ? and deduction_type = ?",[$empId,$dateX,$dateY,'denda sakit'])->row_array();
    $alphaPenalty = $this->db->query("select sum(amount) as amt from salary_deduction where employee_id = ? and date between ? and ? and deduction_type = 'alpha-2'",[$empId,$dateX,$dateY])->row_array();
   
    $deductions = $this->db->query("select * from salary_deduction where employee_id = ? and date between ? and ?",[$empId,$dateX,$dateY])->result_array();

    foreach($deductions as $d){
      if($d['deduction_type'] == "late penalty" || $d['deduction_type'] == "after break late" || $d['deduction_type'] == "clockout late penalty" || $d['deduction_type'] == "clockout forget" || $d['deduction_type'] == "denda sakit"){
        $deductionValue += $d['amount'];
      }
    }

    $allowances = $this->db->query("select * from employee_allowance ea join allowance a on ea.allowance_id = a.allowance_id where ea.employee_id = ?",[$empId])->result_array();
    $overworks = $this->db->query("select * from employee_overwork eo join m_pegawai mp on eo.employee_id = mp.pegawai_id join divisions d on mp.division_id = d.id where mp.pegawai_id = ? and date between ? and ?",[$empId,$dateX,$dateY])->result_array();
    $qReimburse = $this->db->query("select sum(value) as val from reimburse_claim where employee_id = ? and date between ? and ? and status = 'approved'",[$empId,$dateX,$dateY])->row_array();

    foreach($overworks as $overwork){
      $start = new DateTime($overwork['start_from']);
      $end = new DateTime($overwork['until']);
      $diff  = $start->diff($end);
      $hours = ($diff->days * 24) + $diff->h + ($diff->i / 60);
      $totalOverwork += $overwork['overwork_fee'] * $hours;
    }

    foreach($allowances as $allowance){
      if($allowance['period'] == 'monthly'){
        if($allowance['foa']){
          if($alphaCount == 0){
            if($allowance['value'] > 0){
              $totalAllowance  += $allowance['value'];
              $income[] = [
                'name' => $allowance['name'],
                'value' => (int) $allowance['value']
              ];
            }
          }
        }
        else{
          if($allowance['value'] > 0){
            $totalAllowance += $allowance['value'];
            $income[] = [
              'name' => $allowance['name'],
              'value' => (int) $allowance['value']
            ];
          }
        }
      }
      if($allowance['period'] == 'daily'){
        if($allowance['boa']){
          if($allowance['value'] > 0){
            $totalAllowance  += $allowance['value'] * (count($attendance) - $alphaCount);
            $income[] = [
              'name' => $allowance['name'],
              'value' => $allowance['value'] * (count($attendance) - $alphaCount)
            ];

          }
        }
        else{
          if($allowance['value'] > 0){
            $totalAllowance += $allowance['value'] * count($attendance);

            $income[] = [
              'name' => $allowance['name'],
              'value' => $allowance['value'] * count($attendance)
            ]; 
          }
        }
      }
    }
    
    $qBenefit = $this->db->query("select * from employee_benefit eb join benefit b on eb.benefit_id = b.benefit_id where eb.employee_id = ?",[$empId])->result_array();

    foreach($qBenefit as $b){
      $totalBenefit += $b['value'];
      
      $benefit[] = [
        'name' => $b['benefit_name'],
        'value' => (int) $b['value']
      ];
    }

    $salary = ($emp['salary'] - ($alphaPenalty['amt'] + $deductionValue) + $totalAllowance + $totalOverwork + $qReimburse['val']) - $totalBenefit;

    if((int) $alphaPenalty['amt'] > 0){
      $penalty[] = [
        'name' => 'alpha',
        'value' => (int) $alphaPenalty['amt']
      ];
    }
    if((int) $illPenalty['amt'] > 0){
      $penalty[] = [
        'name' => 'sakit',
        'value' => (int) $illPenalty['amt']
      ];
    }
    if((int) $deductionValue > 0){
      $income[] = [
        'name' => 'terlambat',
        'value' => (int) $deductionValue
      ];
    }
    if((int) $totalOverwork > 0){
      $income[] = [
        'name' => 'lembur',
        'value' => $totalOverwork
      ];
    }
    if((int) $qReimburse['val'] > 0){
      $income[] = [
        'name' => 'lembur',
        'value' => (int) $qReimburse['val']
      ];
    }

    echo json_encode(
      [
        'salary' => (int) number_format($emp['salary'],3,'.',''),
        'allowance' => $income,
        'benefit' => $benefit,
        'penalty' => $penalty,
        'totalIncome' => (int) number_format($emp['salary'] + $totalAllowance + $totalOverwork + $qReimburse['val'],3,'.',''),
        'totalBenefit' => (int) number_format(($totalBenefit + $alphaPenalty['amt'] + $deductionValue),3,'.',''),
        'thp' => (int) number_format($salary, 3, '.', '')
      ]
    );
  }

  public function claim($empId){
    $q = $this->db->query("select * from reimburse_claim rc join reimburse r on rc.reimburse_id = r.reimburse_id where employee_id = ? order by date desc",[$empId])->result_array();
    
    if($q){
      echo json_encode(
        [
          'success' => true,
          'result' => $q
        ]
      );
    }
    else{
      echo json_encode(
        [
          'success' => false,
        ]
      );
    }
  }

  public function alphaList($companyId){
    $result = [];
    $today = date('N');
    $date = date("Y-m-d");
    $employees = $this->db->query("select * from tx_absensi ta join m_pegawai mp on ta.pegawai_id = mp.pegawai_id where mp.company_id = ? and ta.is_status = 'alpha-2' and tanggal_absen = ?",[$companyId,$date])->result_array();
    
    foreach($employees as $index => $e){
      $position = $this->db->query("select * from position where id = ?",[$e['position_id']])->row_array();
      $division = $this->db->query("select * from divisions where id = ?",[$e['division_id']])->row_array();
      $workSystem = explode("-",$division['work_system']);
      if($workSystem[0] == "s"){
        $serverDate = new DateTime();
        $shift = $this->db->query("select * from employee_shift es join shift_detail sd on es.shift_detail_id = sd.shift_detail_id where employee_id = ?",[$e['pegawai_id']])->row_array();
        $dateTime1 = new DateTime($employees[$index]['tanggal_absen']. ' ' . $shift['clock_in']);
        $tolerance = (clone $dateTime1)->modify("+{$shift['tardiness_tolerance']} minutes");
        $limit = (clone $tolerance)->modify("+{$division['restriction']} minutes");
        
        if($serverDate > $limit){
          $result[] = [
            'name' => $e['nama_pegawai'],
            'position' => $position['name'],
            'workHour' => $shift['clock_in'].'-'.$shift['clock_out']
          ];
        }
      }
      else{
         $serverDate = new DateTime();
         $pattern = $this->db->query("select * from m_pola_kerja mpk join m_pola_kerja_det mpkd on mpk.pola_kerja_id = mpkd.pola_kerja_id where mpk.pola_kerja_id = ? and is_day = ?",[$workSystem[1],$today])->row_array();
         $dateTime1 = new DateTime($employees[$index]['tanggal_absen']. ' ' . $pattern['jam_masuk']);
         $tolerance = (clone $dateTime1)->modify("+{$pattern['toleransi_terlambat']} minutes");
         $limit = (clone $tolerance)->modify("+{$division['restriction']} minutes");

         if($serverDate > $limit){
          $result[] = [
            'name' => $e['nama_pegawai'],
            'position' => $position['name'],
            'workHour' => $pattern['jam_masuk'].'-'.$pattern['jam_pulang']
          ];
        }
      }
    }   

    echo json_encode(
      [
        'success' => true,
        'result' => $result
      ]
    );
  }

  public function overworkend(){
    $json = file_get_contents('php://input');
    $post = json_decode($json,true);
    
    $data = [
      "finish_photo" => $post["finish_photo"],
      "finish_location" => $post["finish_location"],
      "mocked_out" => $post["is_mock"] ?? false
    ];

    $this->db->trans_begin(); // to start db transaction

    $q = $this->db->query("select * from employee_overwork_detail where employee_overwork_detail_id = ?",[$post['employee_overwork_detail_id']])->row_array();
    
    $this->db->where('employee_overwork_detail_id',$post['employee_overwork_detail_id']);
    
    $this->db->update('employee_overwork_detail',$data);

    echo json_encode ([
      'success' => true,
    ]);
  }

  public function overworkstart(){
    $json = file_get_contents('php://input');
    $post = json_decode($json,true);
    
    $data = [
      "start_photo" => $post["start_photo"],
      "start_location" => $post["start_location"],
      "mocked_in" => $post["is_mock"] ?? false
    ];

    $data2 = ['start_from' => date('Y-m-d H:i:s')];

    $this->db->trans_begin(); // to start db transaction

    $q = $this->db->query("select * from employee_overwork_detail where employee_overwork_detail_id = ?",[$post['employee_overwork_detail_id']])->row_array();
    
    if($q['start_location'] == '-'){
      $this->db->where('employee_overwork_detail_id',$post['employee_overwork_detail_id']);
      $this->db->update('employee_overwork_detail',$data);
      $this->db->where('employee_overwork_id',$post['employee_overwork_id']);
      $this->db->update('employee_overwork',$data2);
    }
    else{
      echo json_encode([
        'success' => false,
      ]);
    }

    if($q['start_location'] == '-'){
      if($this->db->trans_status() === false) {
        $this->db->trans_rollback();
      
        echo json_encode ([
          'success' => false,
        ]);
      } 
      else {
        $this->db->trans_commit();
          echo json_encode ([
          'success' => true,
        ]);
      }
    }
  }

  public function taskSubmit(){
    $json = file_get_contents('php://input');
    $post = json_decode($json,true);

    $date = date('Y-m-d H:i:s');

    $data = [
      'task_id' => uniqid(),
      'date' => $date,
      'employee_id' => $post['employee_id'],
      'location' => $post['location'],
      'photo' => $post['photo'],
      'description' => ''
    ];

    $q = $this->db->insert(
      'task',$data
    );

    if($q){
      echo json_encode(
        [
          'success' => true
        ]
      );
    }
    else{
      echo json_encode(
        [
          'success' => false
        ]
      );
    }
  }

  public function makeoverwork(){
    $json = file_get_contents('php://input');
    $post = json_decode($json,true);

    $employeeOverWorkId = uniqid();

    $data1 = array(
      "employee_overwork_id" => $employeeOverWorkId,
      "employee_id" => $post["employee_id"],
      "date" => date('Y-m-d H:i:s'),
      "start_from" => $post["start_from"],
      "until" => $post['until'],
      "approved" => 0,
    );

    $data2 = array(
      'employee_overwork_detail_id' => uniqid(),
      'employee_overwork_id' => $employeeOverWorkId,
      'start_location' => '-',
      'start_photo' => '-',
      'finish_location' => '-',
      'finish_photo' => '-',
      'reason' => $post['reason']
    );

    $this->db->trans_begin(); // to start db transaction
    
    $this->db->insert(
      'employee_overwork',
      $data1
    );
    $this->db->insert(
      'employee_overwork_detail',
      $data2
    );
        
    if($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      
      echo json_encode ([
        'success' => false,
      ]);
    } 
    else {
      $this->db->trans_commit();
      echo json_encode ([
        'success' => true,
      ]);
    }
  }

  public function reimburseList($id){
    $q = $this->db->query("select * from reimburse where company_id = ?",[$id])->result_array();

    echo json_encode($q);
  }

  public function notificationList($id){
    $q = $this->db->query("select * from notification where employee_id = ? order by date desc",[$id])->result_array();
    
    echo json_encode($q);
  }

  public function activityList($id){
    $q = $this->db->query("select * from tx_absensi where pegawai_id = ? and tanggal_absen = ?",[$id,date('Y-m-d')])->row_array();
    $emp = $this->db->query("select * from m_pegawai where pegawai_id = ?",[$q['pegawai_id']])->row_array();
    $division = $this->db->query("select * from divisions where id = ?",[$emp['division_id']])->row_array();
    $lastDefaultStatus = $this->db->query("SELECT * FROM tx_absensi where pegawai_id = ? ORDER BY tanggal_absen DESC LIMIT 1",[$id])->row_array();

    $workSystem = explode("-",$division['work_system']);

    $today = date('N');
    $tanggalHariIni = date('Y-m-d');

    if($workSystem[0] == "s"){
      $activity = [];
      $serverDate = new DateTime(date('Y-m-d') . ' ' . $q['jam_masuk']);
      $serverDate2 = new DateTime(date('Y-m-d') . ' ' . $q['jam_keluar']);
      $shift = $this->db->query("select * from employee_shift es join shift_detail sd on es.shift_detail_id = sd.shift_detail_id where employee_id = ?",[$id])->row_array();
      $dateTime1 = new DateTime($lastDefaultStatus['tanggal_absen']. ' ' . $shift['clock_in']);
      $tolerance = (clone $dateTime1)->modify("+{$shift['tardiness_tolerance']} minutes");
      $limit = (clone $tolerance)->modify("+{$division['restriction']} minutes");

      if($q['jam_masuk'] != '00:00'){
        if($serverDate < $limit && $serverDate > $tolerance){
          $activity[] = [
            'type' => 'Check In',
            'time' => $q['jam_masuk'],
            'late' => true
          ];
        }
        
        if($serverDate > $limit){
          $activity[] = [
            'type' => 'Check In',
            'time' => $q['jam_masuk'],
            'late' => true
          ];
        }

        if($serverDate < $limit && $serverDate < $tolerance){
          $activity[] = [
            'type' => 'Check In',
            'time' => $q['jam_masuk'],
            'late' => false
          ];
        }
      }

      if($q['jam_keluar'] != '00:00'){
        if($serverDate2 < $limit && $serverDate2 > $tolerance){
          $activity[] = [
            'type' => 'Check Out',
            'time' => $q['jam_keluar'],
            'late' => true
          ];
        }

        if($serverDate2 > $limit){
          $activity[] = [
            'type' => 'Check Out',
            'time' => $q['jam_keluar'],
            'late' => true
          ];
        }

        if($serverDate2 < $limit && $serverDate2 < $tolerance){
          $activity[] = [
            'type' => 'Check Out',
            'time' => $q['jam_keluar'],
            'late' => false
          ];
        }
      }

      $qTask = $this->db->query("select * from task t join task_detail td on t.task_id = td.task_id where t.employee_id = ?",[$id])->result_array();

      foreach($qTask as $task){
        if($task['finish_time'] != "00:00"){
          $activity[] = [
            'type' => 'Assignment',
            'date' => $task['date'],
            'time' => $task['finish_time'],
          ];
        }
      }
      
      echo json_encode($activity);
    }
    else{
      $serverDate = new DateTime(date('Y-m-d') . ' ' . $q['jam_masuk']);
      $serverDate2 = new DateTime(date('Y-m-d') . ' ' . $q['jam_keluar']);
      $pattern = $this->db->query("select * from m_pola_kerja mpk join m_pola_kerja_det mpkd on mpk.pola_kerja_id = mpkd.pola_kerja_id where mpk.pola_kerja_id = ? and is_day = ?",[$workSystem[1],$today])->row_array();
      $dateTime1 = new DateTime($lastDefaultStatus['tanggal_absen']. ' ' . $pattern['jam_masuk']);
      $tolerance = (clone $dateTime1)->modify("+{$pattern['toleransi_terlambat']} minutes");
      $limit = (clone $tolerance)->modify("+{$division['restriction']} minutes");

      if($q['jam_masuk'] != '00:00'){
        if($serverDate < $limit && $serverDate > $tolerance){
          $activity[] = [
            'type' => 'Check In',
            'time' => $q['jam_masuk'],
            'late' => true
          ];
        }
        
        if($serverDate > $limit){
          $activity[] = [
            'type' => 'Check In',
            'time' => $q['jam_masuk'],
            'late' => true
          ];
        }

        if($serverDate < $limit && $serverDate < $tolerance){
          $activity[] = [
            'type' => 'Check In',
            'time' => $q['jam_masuk'],
            'late' => false
          ];
        }
      }

      if($q['jam_keluar'] != '00:00'){
        if($serverDate2 < $limit && $serverDate2 > $tolerance){
          $activity[] = [
            'type' => 'Check Out',
            'time' => $q['jam_keluar'],
            'late' => true
          ];
        }

        if($serverDate2 > $limit){
          $activity[] = [
            'type' => 'Check Out',
            'time' => $q['jam_keluar'],
            'late' => true
          ];
        }

        if($serverDate2 < $limit && $serverDate2 < $tolerance){
          $activity[] = [
            'type' => 'Check Out',
            'time' => $q['jam_keluar'],
            'late' => false
          ];
        }
      } 

      $qTask = $this->db->query("select * from task t join task_detail td on t.task_id = td.task_id where t.employee_id = ?",[$id])->result_array();

      foreach($qTask as $task){
        if($task['finish_time'] != "00:00"){
          $activity[] = [
            'type' => 'Assignment',
            'date' => $task['date'],
            'time' => $task['finish_time'],
          ];
        }
      }

      echo json_encode($activity);


    }

  } 

  public function schedule($employeeId,$date){
    $emp = $this->db->query("select * from m_pegawai mp join divisions d on mp.division_id = d.id  where pegawai_id = ?",[$employeeId])->row_array();
    

    $workSystem = explode("-",$emp['work_system']);

    $days = [
      'Sunday'    => 'Minggu',
      'Monday'    => 'Senin',
      'Tuesday'   => 'Selasa',
      'Wednesday' => 'Rabu',
      'Thursday'  => 'Kamis',
      'Friday'    => 'Jumat',
      'Saturday'  => 'Sabtu'
    ];

    $_days = [
      'Senin',
      'Selasa',
      'Rabu',
      'Kamis',
      'Jumat',
      'Sabtu',
      'Minggu'
    ];
    
    if($workSystem[0] == "s"){
      $shift = $this->db->query("select * from employee_shift es join shift_detail sd on es.shift_detail_id = sd.shift_detail_id where employee_id = ?",[$employeeId])->row_array();
      $shiftOff = $this->db->query("select * from shift_off where shift_id = ? and employee_id = ?",[$shift['shift_id'],$employeeId])->row_array();
      $todayInAWeek = date('N');
      $schedule = [];
      $startDate = "";
      $endDate = "";

      for($i = $todayInAWeek; $i >= 1; $i--){
        $shiftOffDayName = $_days[$shiftOff['day'] - 1];
        $tanggal = date('Y-m-d', strtotime("$date -$i days"));
        $todayName = $days[date('l', strtotime($tanggal))];
        $companyHolidays = $this->db->query("select * from company_holidays where company_id = ? and ? between tanggal and sampai_tanggal",[$emp['company_id'],$tanggal])->result_array();
        $globalHolidays = $this->db->query("select * from global_holidays where company_id = ? and ? between tanggal and sampai_tanggal",[$emp['company_id'],$tanggal])->result_array();
        $permission = $this->db->query("select * from tx_request_izin x join tx_request_izin_pegawai y on x.request_izin_id = y.request_izin_id where x.is_status = ? and y.pegawai_id = ? and ? between x.tanggal_request and x.tanggal_request_end",[1,$employeeId,$tanggal])->result_array();

        $today = $tanggal == $date ? true : false;

        if($shiftOffDayName == $todayName){
          $schedule[] = [
            'free' => true,
            'date' => $tanggal,
            'dayName' => $todayName,
            'today' => $today,
            'start' => '00:00',
            'finish' => '00:00',
          ];         
        }
        else{
          if(count($permission) > 0){
              $schedule[] = [
                'free' => true,
                'permitted' => true,
                'date' => $tanggal,
                'dayName' => $todayName,
                'today' => $today,
                'start' => '00:00',
                'finish' => '00:00',
              ];
            }
            else{
              if(count($companyHolidays) > 0 || count($globalHolidays) > 0){
                $schedule[] = [
                  'free' => true,
                  'date' => $tanggal,
                  'dayName' => $todayName,
                  'today' => $today,
                  'start' => '00:00',
                  'finish' => '00:00',
                ];
              }
              else{
                $schedule[] = [
                  'free' => false,
                  'date' => $tanggal,
                  'dayName' => $todayName,
                  'start' => date("H:i", strtotime($shift['clock_in'])),
                  'finish' => date("H:i", strtotime($shift['clock_out'])),
                  'today' => $today,
                ];
              }
          }
        }


        if($i == $todayInAWeek){
          $startDate = $tanggal;
        }
      }

      for($i = 0; $i < (7 - $todayInAWeek); $i++){    
        $shiftOffDayName = $_days[$shiftOff['day'] - 1];
        $tanggal = date('Y-m-d', strtotime("$date +$i days"));
        $today = $tanggal == $date ? true : false;
        $todayName = $days[date('l', strtotime($tanggal))];
        $companyHolidays = $this->db->query("select * from company_holidays where company_id = ? and ? between tanggal and sampai_tanggal",[$emp['company_id'],$tanggal])->result_array();
        $globalHolidays = $this->db->query("select * from global_holidays where company_id = ? and ? between tanggal and sampai_tanggal",[$emp['company_id'],$tanggal])->result_array();
        $permission = $this->db->query("select * from tx_request_izin x join tx_request_izin_pegawai y on x.request_izin_id = y.request_izin_id where x.is_status = ? and y.pegawai_id = ? and ? between x.tanggal_request and x.tanggal_request_end",[1,$employeeId,$tanggal])->result_array();


        if($shiftOffDayName == $todayName){
          $schedule[] = [
            'free' => true,
            'date' => $tanggal,
            'dayName' => $todayName,
            'today' => $today,
            'start' => '00:00',
            'finish' => '00:00'
          ];
        }
        else{
            if(count($permission) > 0){
              $schedule[] = [
                'free' => true,
                'permitted' => true,
                'date' => $tanggal,
                'dayName' => $todayName,
                'today' => $today,
                'start' => '00:00',
                'finish' => '00:00'
              ];
            }
            else{
              if(count($companyHolidays) > 0 || count($globalHolidays) > 0){
                $schedule[] = [
                  'free' => true,
                  'date' => $tanggal,
                  'dayName' => $todayName,
                  'today' => $today,
                  'start' => '00:00',
                  'finish' => '00:00'
                ];
              }
              else{
                $schedule[] = [
                  'free' => false,
                  'date' => $tanggal,
                  'dayName' => $todayName,
                  'start' => date("H:i", strtotime($shift['clock_in'])),
                  'finish' => date("H:i", strtotime($shift['clock_out'])),
                  'today' => $today
                ];
              }
            }
        }
        
        if($i == (7 - $todayInAWeek - 1)){
          $endDate = $tanggal;
        }
      };
      
      echo json_encode(
        [
          'schedule' => $schedule,
          'startDate' => $startDate,
          'endDate' => $endDate
        ]
      );
    }
    else{
      $pattern = $this->db->query("select * from m_pola_kerja mpk join m_pola_kerja_det mpkd on mpk.pola_kerja_id = mpkd.pola_kerja_id where mpk.pola_kerja_id = ?",[$workSystem[1]])->result_array();
      $todayInAWeek = date('N');
      $startDate = "";
      $endDate = "";
      $schedule = [];

      foreach($pattern as $index => $p){
        $day = $p['is_day'] - 1;
        $tanggal = date('Y-m-d', strtotime("$date +$day days"));
        $today = $tanggal == $date ? true : false;
        $todayName = $days[date('l', strtotime($tanggal))];
        $companyHolidays = $this->db->query("select * from company_holidays where company_id = ? and ? between tanggal and sampai_tanggal",[$emp['company_id'],$tanggal])->result_array();
        $globalHolidays = $this->db->query("select * from global_holidays where company_id = ? and ? between tanggal and sampai_tanggal",[$emp['company_id'],$tanggal])->result_array();
        $permission = $this->db->query("select * from tx_request_izin x join tx_request_izin_pegawai y on x.request_izin_id = y.request_izin_id where x.is_status = ? and y.pegawai_id = ? and ? between x.tanggal_request and x.tanggal_request_end",[1,$employeeId,$tanggal])->result_array();

        if($p['is_work'] == 'n'){
          $schedule[] = [
            'free' => true,
            'date' => $tanggal,
            'dayName' => $todayName,
            'today' => $today,
            'start' => '00:00',
            'finish' => '00:00'
          ];         
        }
        else{
          if(count($permission) > 0){
              $schedule[] = [
                'free' => true,
                'permitted' => true,
                'date' => $tanggal,
                'dayName' => $todayName,
                'today' => $today,
                'start' => '00:00',
                'finish' => '00:00'
              ];
          }
          else{
            if(count($companyHolidays) > 0 || count($globalHolidays) > 0){
              $schedule[] = [
                'free' => true,
                'date' => $tanggal,
                'dayName' => $todayName,
                'today' => $today,
                'start' => '00:00',
                'finish' => '00:00'
              ];
            }
            else{
              $schedule[] = [
                'free' => false,
                'date' => $tanggal,
                'dayName' => $todayName,
                'start' => date("H:i", strtotime($p['jam_masuk'])),
                'finish' => date("H:i", strtotime($p['jam_pulang'])),
                'today' => $today
              ];
            }
        }
      }

      if($index == 0){
        $startDate = $tanggal;
      }
      if($index == 6){
        $endDate = $tanggal;
      }
    }
    
    echo json_encode(
      [
        'schedule' => $schedule,
        'startDate' => $startDate,
        'endDate' => $endDate
      ]
    );
   
   }
  }

  public function employeeList($employeeId){
    $q = $this->db->query("select * from m_pegawai where pegawai_id = ?",[$employeeId])->row_array();
    $q2 = $this->db->query("select * from m_pegawai mp join position p on mp.position_id = p.id where mp.division_id = ? and is_del='n'",[$q['division_id']])->result_array();
    echo json_encode($q2);
  }

  public function taskEdit(){
    $json = file_get_contents('php://input');
    $post = json_decode($json,true);
    
    $data = [
      'date' => $post['date'],
      'description' => $post['description'],
    ];

    $this->db->where(
      'task_id',$post['task_id']
    );

    $q = $this->db->update(
      'task',$data
    );

    if($q){
      echo json_encode(
        [
          'success' => true
        ]
      );
    }
    else{
      echo json_encode(
        [
          'success' => false
        ]
      );
    }
  }

  public function time(){
    echo time();
  }

  public function exceptionDelete($id){
    $this->db->where('id', $id);
    $q = $this->db->delete('exception');
    
    if($q){
      echo json_encode(
        [
          'success' => true
        ]
      );
    }
    else{
      echo json_encode(
        [
          'success' => false
        ]
      );
    }
  }

  public function cshList($pegawaiId){
    $status = $this->db->query("select * from employee_leave_balance where employee_id = ? order by id desc limit 1",[$pegawaiId])->row_array();
    $r3 = $this->db->query("select * from exception where employee_id = ? and (type='Cuti pulang' or type='Cuti setengah hari') and date between ? and ? order by created_at desc ",[$pegawaiId,date('Y-m-d'),$status['from'],$status['to']])->result_array();

    http_response_code(200);

    $result = [
        "list"   => $r3,
        "quota"  => (float) $status['quota'], // quota real dikurangi pending (terkunci)
        "used"   => (float) $status['used'], // hanya approved
        "locked" => 0   // pending (opsional, biar user tahu ada cuti terpending)
    ];

    echo json_encode([
        "success" => true,
        "result"  => $result
    ]);
    
   
  }

  public function log($empId){
    $from = date('Y').'-'.date('m').'-'.'01';
    $to = date('Y').'-'.date('m').'-'.'31';
    $q = $this->db->query("select * from tx_absensi where tanggal_absen between ? and ? and pegawai_id = ?",[$from,$to,$empId])->result_array();
    echo json_encode(
      [
        'r' => $q
      ]
    );
  }


  function hasException($employeeId,$reason){
    $employee = $this->db->query("select * from m_pegawai where pegawai_id = ?",[$employeeId])->row_array();
    $division = $this->db->query("select * from divisions where division_id = ?",[$employee['division_id']])->row_array();
    $r = $this->db->query("select * from exception where employee_id = ? and status = ? and date = ? and type = ?",[$employeeId,1,date('Y-m-d'),urldecode($reason)])->num_rows() > 0;
    $r2 =  $this->db->query("select * from exception where employee_id = ? and status = ? and date = ? and type = ?",[$employeeId,1,date('Y-m-d'),'Terlambat'])->num_rows() > 0;
    
    $patternId = explode('-',$division['work_system'])[1];

    if(explode('-',$division['work_system'])[1] == 'wd'){
      $params = [$patternId,date('N')];
      $q = "select * from m_pola_kerja det where pola_kerja_id = ? and is_day = ?";
      $pattern = $this->db->query($q,$params)->row_array();
      $hasException = $r || $r2 ? true : false;
      $limit = $r ? $pattern['c1'] : '23:58:00';
      
      echo json_encode([
        'hasException' => $hasException,
        'reason' => $reason,
        'limit' => $limit
      ]);
    }
    else{
      $q1 = $this->db->query("select * from employee_shift where employee_id = ?",[$employeeId])->row_array();
      $q2 = $this->db->query("select * from shift_detail where shift_detail_id = ?",[$q1['shift_detail_id']])->row_array();
      $hasException = $r || $r2 ? true : false;
      $limit = $r ? $q2['c1'] : '23:58:00';
      
      echo json_encode([
        'hasException' => $hasException,
        'reason' => $reason,
        'limit' => $limit
      ]);
    }
   
  }
  
  function timenow(){
    $tz = new DateTimeZone("Asia/Jakarta");
    $now = new DateTime("now", $tz);

    // ambil microtime untuk millisecond
    $micro = microtime(true);
    $milliSeconds = (int)(($micro - floor($micro)) * 1000);

    $data = [
        "year" => (int)$now->format("Y"),
        "month" => (int)$now->format("n"),
        "day" => (int)$now->format("j"),
        "hour" => (int)$now->format("G"),
        "minute" => (int)$now->format("i"),
        "seconds" => (int)$now->format("s"),
        "milliSeconds" => $milliSeconds,
        "dateTime" => $now->format("Y-m-d\TH:i:s") . "." . str_pad($milliSeconds, 3, "0", STR_PAD_LEFT),
        "date" => $now->format("m/d/Y"),
        "time" => $now->format("H:i"),
        "timeZone" => "Asia/Jakarta",
        "dayOfWeek" => $now->format("l"),
        "dstActive" => $now->format("I") == "1"
    ];
    
    return $this->output->set_content_type('application/json')->set_output(json_encode($data));
  }
  
  function syncout(){
    $today = date('N');
    $tanggalHariIni = date('Y-m-d');
    $json = file_get_contents('php://input');
    $post = json_decode($json,true);
    $eParam = [$post['empId'],1,date('Y-m-d'),'Lupa absen'];
    $data = ['jam_keluar' => $post['captureTime'],'foto_absen_keluar' => $post['photo']];
    $emp = $this->db->query("select * from m_pegawai where pegawai_id = ?",[$post['empId']])->row_array();
    $division = $this->db->query("select * from divisions where id = ?",[$emp['division_id']])->row_array();
    $lastDefaultStatus = $this->db->query("SELECT * FROM tx_absensi where pegawai_id = ? ORDER BY tanggal_absen DESC LIMIT 1",[$post['empId']])->row_array();
    $e = $this->db->query("select * from exception where employee_id = ? and status = ? and date = ? and type = ?",$eParam)->row_array();
    $q = "select * from m_pola_kerja mpk join m_pola_kerja_det mpkd on mpk.pola_kerja_id = mpkd.pola_kerja_id where mpk.pola_kerja_id = ? and is_day = ?";
    $workSystem = explode("-",$division['work_system']);
    
    if(!$emp['signed_in']){
      http_response_code(200);
      echo json_encode(["success" => false]);
    }
    else{
        if($workSystem[0] == "s"){
          $captureTime =  new DateTime($lastDefaultStatus['tanggal_absen'] . ' ' . $post['captureTime']);
          $q = "select * from employee_shift es join shift_detail sd on es.shift_detail_id = sd.shift_detail_id where employee_id = ?";
          $shift = $this->db->query($q,[$post['empId']])->row_array();
          $dateTime1 = new DateTime($lastDefaultStatus['tanggal_absen']. ' ' . $shift['clock_out']); // y
          $tolerance = (clone $dateTime1)->modify("+{$shift['tardiness_tolerance']} minutes");
          $limit = $dateTime1->modify("+{$division['clockout_restriction']} minutes");
          $sDTDiff = $captureTime->diff($tolerance);
          $sDTDiffMinutes = ($sDTDiff->days * 24 * 60) + ($sDTDiff->h * 60) + $sDTDiff->i;
          if($captureTime > $limit){
            if($e){
                $this->db->where('pegawai_id',$post["empId"]);
                $this->db->where('absen_id',$lastDefaultStatus["absen_id"]);
                $q2 = $this->db->update('tx_absensi',$data);
               
                if($q2){
                  $this->db->where('pegawai_id',$post['empId']);
                  $this->db->update('m_pegawai',['signed_in' => false]);
                  echo json_encode(["success" => true]);
                }
                else{
                  echo json_encode(["success" => false]);
                }
            }
            else{
              echo json_encode(["success" => false]);
            }
          }
          else{
            $this->db->where('pegawai_id',$post["empId"]);
            $this->db->where('absen_id',$lastDefaultStatus["absen_id"]);
            $q2 = $this->db->update('tx_absensi',$data);
               
            if($q2){
              $this->db->where('pegawai_id',$post['empId']);
              $this->db->update('m_pegawai',['signed_in' => false]);
              echo json_encode(["success" => true]);
            }
            else{
              echo json_encode(["success" => false]);
            }
          }
        }
        else{
          $param = [$workSystem[1],$today];
          $captureTime =  new DateTime($lastDefaultStatus['tanggal_absen'] . ' ' . $post['captureTime']);
          $pattern = $this->db->query($q,$param)->row_array();
          $dateTime1 = new DateTime($lastDefaultStatus['tanggal_absen']. ' ' . $pattern['jam_pulang']);
          $tolerance = (clone $dateTime1)->modify("+{$pattern['toleransi_terlambat']} minutes");
          $limit = $dateTime1->modify("+{$division['clockout_restriction']} minutes");
          $sDTDiff = $captureTime->diff($tolerance);
          $sDTDiffMinutes = ($sDTDiff->days * 24 * 60) + ($sDTDiff->h * 60) + $sDTDiff->i;
          if($captureTime > $limit){
            if($e){
                $this->db->where('pegawai_id',$post["empId"]);
                $this->db->where('absen_id',$lastDefaultStatus["absen_id"]);
                $q2 = $this->db->update('tx_absensi',$data);
               
                if($q2){
                  $this->db->where('pegawai_id',$post['empId']);
                  $this->db->update('m_pegawai',['signed_in' => false]);
                  echo json_encode(["success" => true]);
                }
                else{
                  echo json_encode(["success" => false]);
                }
            }
            else{
              echo json_encode(["success" => false]);
            }
          }
          else{
            $this->db->where('pegawai_id',$post["empId"]);
            $this->db->where('absen_id',$lastDefaultStatus["absen_id"]);
            $q2 = $this->db->update('tx_absensi',$data);
               
            if($q2){
              $this->db->where('pegawai_id',$post['empId']);
              $this->db->update('m_pegawai',['signed_in' => false]);
              echo json_encode(["success" => true]);
            }
            else{
              echo json_encode(["success" => false]);
            }
          }          
        }
    }

  }
  
  function syncin(){
    $json = file_get_contents('php://input');
    $syncParam = json_decode($json, true);
    $emp = $this->db->query("select * from m_pegawai where pegawai_id = ?",[$syncParam['empId']])->row_array();
    $division = $this->db->query("select * from divisions where id = ?",[$emp['division_id']])->row_array();
    $lastDefaultStatus = $this->db->query("SELECT * FROM tx_absensi where pegawai_id = ? ORDER BY tanggal_absen DESC LIMIT 1",[$syncParam['empId']])->row_array();
    $workSystem = explode("-",$division['work_system']);
    if(!in_array($lastDefaultStatus['is_status'], ['free', 'off', 'c', 'i', 's'])){
      if($workSystem[0] == "s"){
        $param = [$workSystem[0],date('Y-m-d')];
        $captureTime = new DateTime($lastDefaultStatus['tanggal_absen'] . ' ' . $syncParam['captureTime']);
        $employee = $this->db->query("select * from m_pegawai where pegawai_id = ?",[$syncParam['empId']])->row_array();
        $shift = $this->db->query("select * from employee_shift es join shift_detail sd on es.shift_detail_id = sd.shift_detail_id where employee_id = ?",[$syncParam['empId']])->row_array(); // x
        $dateTime1 = new DateTime($lastDefaultStatus['tanggal_absen']. ' ' . $shift['clock_in']); // y
        $tolerance = (clone $dateTime1)->modify("+{$shift['tardiness_tolerance']} minutes");
        $limit = (clone $tolerance)->modify("+{$division['restriction']} minutes");
        $sDLDiff = $captureTime->diff($limit);
        $sDLDiffMinutes = ($sDLDiff->days * 24 * 60) + ($sDLDiff->h * 60) + $sDLDiff->i;
        $sDTDiff = $captureTime->diff($tolerance);
        $sDTDiffMinutes = ($sDTDiff->days * 24 * 60) + ($sDTDiff->h * 60) + $sDTDiff->i;

         
        if(!$emp['signed_in']){
            if($captureTime > $limit){
                $params = [$syncParam['empId'],1,date('Y-m-d'),1];
                $data = ['is_status' => 'hhk','jam_masuk' => $syncParam['captureTime'],'foto_absen_masuk' => $syncParam['photo'], 'isOfflineIn' => true];
                $e = $this->db->query("select * from exception where employee_id = ? and status = ? and date = ? and is_csh = ?",$params)->row_array();
                if($e){
                    $this->db->where('absen_id',$lastDefaultStatus["absen_id"]);
                    $this->db->update('tx_absensi',$data);
                    $this->db->where('pegawai_id',$syncParam['empId']);
                    $this->db->update('m_pegawai',['signed_in' => true]);
                    echo json_encode(["success" => true]);
                }
                else{
                    echo json_encode(["success" => false,'message' => 'err1']);   
                }
            }
            if($captureTime < $limit && $captureTime > $tolerance){
              $eParam = [$emp['pegawai_id'],date('Y-m-d')];
              $data = ['is_status' => 'hhk','jam_masuk' => $syncParam['captureTime'],'foto_absen_masuk' => $syncParam['photo'], 'isOfflineIn' => true];
              $e = $this->db->query("select * from exception where employee_id = ? and date = ? and status = 1 order by created_at desc limit 1",$eParam)->row_array();
              
              $exception = $e ? $e : ['is_csh' => false,'type' => ''];
              
              if($division['late_penalty']){
                $data1 = [
                    'deduction_id' => uniqid(),
                    'employee_id' => $syncParam['empId'],
                    'deduction_type' => 'late penalty',
                    'date' => date('Y-m-d'),
                    'amount' => $division['penalty_nominal'],
                    'note' => '...'
                ];
                $this->db->trans_begin();
                    
                if(!$exception['is_csh']){
                  $q1 = $this->db->insert('salary_deduction',$data1);
                }
                    
                $this->db->where('absen_id',$lastDefaultStatus["absen_id"]);
                    
                $dataAbsensi = $exception['is_csh'] ? $data : [
                    ...$data,'isLate' => true
                ];
                    
                $q2 = $this->db->update('tx_absensi',$dataAbsensi);
    
                if($q1 && $q2){
                    $this->db->where('pegawai_id',$syncParam['empId']);
                    $this->db->update('m_pegawai',['signed_in' => true]);
                    $this->db->trans_commit();
                    echo json_encode(["success" => true]);
                }
                else{
                    $this->db->trans_rollback();
                    echo json_encode(["success" => false, 'message' => 'err2']);
                }
              }
              else{
                  $data = ['is_status' => 'hhk','jam_masuk' => $syncParam['captureTime'],'foto_absen_masuk' => $syncParam['photo']];
                  $this->db->where('absen_id',$lastDefaultStatus["absen_id"]);
                  $dataAbsensi = $exception['is_csh'] ? $data : [...$data,'isLate' => 'true'];
                  $this->db->where('absen_id',$lastDefaultStatus["absen_id"]);
                  $q2 = $this->db->update('tx_absensi',$dataAbsensi);
                  if($q2){
                    $this->db->where('pegawai_id',$syncParam['empId']);
                    $this->db->update('m_pegawai',['signed_in' => true]);
                    echo json_encode(["success" => true]);
                  }
                  else{
                    echo json_encode(["success" => false, 'message' => 'err3']);
                  }
              }
            }
            if($captureTime < $tolerance){
                if($captureTime < $dateTime1->modify('-1 hour')){
                    echo json_encode(["success" => false]);
                }
                else{
                  $data = ['is_status' => 'hhk','jam_masuk' => $syncParam['captureTime'],'foto_absen_masuk' => $syncParam['photo'], 'isOfflineIn' => true];
                  $this->db->where('pegawai_id',$syncParam['empId']);
                  $this->db->where('tanggal_absen',date('Y-m-d'));
                  $q = $this->db->update('tx_absensi',$data);
                  if($q){
                    $this->db->where('pegawai_id',$syncParam['empId']);
                    $this->db->update('m_pegawai',['signed_in' => true]);
                    echo json_encode(["success" => true]);
                  }
                  else{
                    echo json_encode(["success" => false, 'message' => 'err4']);
                  }
                }
            }
        }  
      }
      else{
          $param = [$workSystem[1],date('Y-m-d')];
          $captureTime =  new DateTime($lastDefaultStatus['tanggal_absen'] . ' ' . $syncParam['captureTime']);
          $employee = $this->db->query("select * from m_pegawai where pegawai_id = ?",[$syncParam['empId']])->row_array();
          $pattern = $this->db->query("select * from m_pola_kerja mpk join m_pola_kerja_det mpkd on mpk.pola_kerja_id = mpkd.pola_kerja_id where mpk.pola_kerja_id = ? and is_day = ?",[$workSystem[1],date('N')])->row_array();
          $dateTime1 = new DateTime($lastDefaultStatus['tanggal_absen']. ' ' . $pattern['jam_masuk']);
          $tolerance = (clone $dateTime1)->modify("+{$pattern['toleransi_terlambat']} minutes");
          $limit = (clone $tolerance)->modify("+{$division['restriction']} minutes");
          $sDLDiff = $captureTime->diff($limit);
          $sDLDiffMinutes = ($sDLDiff->days * 24 * 60) + ($sDLDiff->h * 60) + $sDLDiff->i;
          $sDTDiff = $captureTime->diff($tolerance);
          $sDTDiffMinutes = ($sDTDiff->days * 24 * 60) + ($sDTDiff->h * 60) + $sDTDiff->i;     
          
          if(!$emp['signed_in']){
            if($captureTime > $limit){
                $params = [$syncParam['empId'],1,date('Y-m-d'),1];
                $data = ['is_status' => 'hhk','jam_masuk' => $syncParam['captureTime'],'foto_absen_masuk' => $syncParam['photo'], 'isOfflineIn' => true];
                $e = $this->db->query("select * from exception where employee_id = ? and status = ? and date = ? and is_csh = ?",$params)->row_array();
                if($e){
                    $this->db->where('absen_id',$lastDefaultStatus["absen_id"]);
                    $this->db->update('tx_absensi',$data);
                    $this->db->where('pegawai_id',$syncParam['empId']);
                    $this->db->update('m_pegawai',['signed_in' => true]);
                    echo json_encode(["success" => true]);
                }
                else{
                    echo json_encode(["success" => false,'message' => 'err1']);   
                }
            }
            if($captureTime < $limit && $captureTime > $tolerance){
              $eParam = [$emp['pegawai_id'],date('Y-m-d')];
              $data = ['is_status' => 'hhk','jam_masuk' => $syncParam['captureTime'],'foto_absen_masuk' => $syncParam['photo'], 'isOfflineIn' => true];
              $e = $this->db->query("select * from exception where employee_id = ? and date = ? and status = 1 order by created_at desc limit 1",$eParam)->row_array();
              $exception = $e ? $e : ['is_csh' => false,'type' => ''];
              
              if($division['late_penalty']){
                $data1 = [
                    'deduction_id' => uniqid(),
                    'employee_id' => $syncParam['empId'],
                    'deduction_type' => 'late penalty',
                    'date' => date('Y-m-d'),
                    'amount' => $division['penalty_nominal'],
                    'note' => '...'
                ];
                $this->db->trans_begin();
                    
                if(!$exception['is_csh']){
                  $q1 = $this->db->insert('salary_deduction',$data1);
                }
                    
                $this->db->where('absen_id',$lastDefaultStatus["absen_id"]);
                    
                $dataAbsensi = $exception['is_csh'] ? $data : [
                      ...$data,
                      'isLate' => true
                    ];
                    
                $q2 = $this->db->update('tx_absensi',$dataAbsensi);
    
                if($q1 && $q2){
                    $this->db->where('pegawai_id',$syncParam['empId']);
                    $this->db->update('m_pegawai',['signed_in' => true]);
                    $this->db->trans_commit();
                    echo json_encode(["success" => true]);
                }
                else{
                    $this->db->trans_rollback();
                    echo json_encode(["success" => false, 'message' => 'err2']);
                }
              }
              else{
                  $data = ['is_status' => 'hhk','jam_masuk' => $syncParam['captureTime'],'foto_absen_masuk' => $syncParam['photo']];
                  $this->db->where('absen_id',$lastDefaultStatus["absen_id"]);
                  $dataAbsensi = $exception['is_csh'] ? $data : [...$data,'isLate' => 'true'];
                  $this->db->where('absen_id',$lastDefaultStatus["absen_id"]);
                  $q2 = $this->db->update('tx_absensi',$dataAbsensi);
                  if($q2){
                    $this->db->where('pegawai_id',$syncParam['empId']);
                    $this->db->update('m_pegawai',['signed_in' => true]);
                    echo json_encode(["success" => true]);
                  }
                  else{
                    echo json_encode(["success" => false, 'message' => 'err3']);
                  }
              }
            }
            if($captureTime < $tolerance){
                if($captureTime < $dateTime1->modify('-1 hour')){
                    echo json_encode(["success" => false]);
                }
                else{
                  $data = ['is_status' => 'hhk','jam_masuk' => $syncParam['captureTime'],'foto_absen_masuk' => $syncParam['photo'], 'isOfflineIn' => true];
                  $this->db->where('pegawai_id',$syncParam['empId']);
                  $this->db->where('tanggal_absen',date('Y-m-d'));
                  $q = $this->db->update('tx_absensi',$data);
                  if($q){
                    $this->db->where('pegawai_id',$syncParam['empId']);
                    $this->db->update('m_pegawai',['signed_in' => true]);
                    echo json_encode(["success" => true]);
                  }
                  else{
                    echo json_encode(["success" => false, 'message' => 'err4']);
                  }
                }
            }
        }          
      }
    }
  }

  // fungsi untuk mengizinkan karyawan absen masuk atau absen pulang di luar (location exception)
  
  function exceptiontoday($employeeId,$type){
    $f = $type == "in" ? "Absen Masuk":"Absen Pulang";
    $parameter = [date('Y-m-d'),$f,$employeeId,1];
    $q = "select * from exception where date = ? and type = ? and employee_id = ? and status = ?";
    $r = $this->db->query($q,$parameter)->num_rows();
    echo $r > 0 ? json_encode(['exist' => 'yes']) : json_encode(['exist' => 'no']);
  }
}