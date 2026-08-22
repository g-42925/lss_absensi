<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cron extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('user/attendance_model', 'att');
    }

    public $att;
    public $email;
    public $session;
    public $form_validation;
    public $upload;
    public $pagination;

    // This function is called by the cron job to update attendance data
    
    #[SkipPermission]
    public function default() {
      $this->db->query("SET time_zone = '+07:00'");
      $companies = $this->db->query("select * from companies where active = ?",[1])->result_array();
      foreach($companies as $company){
        $companyHolidays = $this->db->query("select * from company_holidays where company_id = ? and curdate() between tanggal and sampai_tanggal",[$company['id']])->result_array();
        $globalHolidays = $this->db->query("select * from global_holidays where company_id = ? and curdate() between tanggal and sampai_tanggal",[$company['id']])->result_array();
        $employees = $this->db->query("select * from m_pegawai where company_id = ? and is_del != 'y'",[$company['id']])->result_array();

        foreach($employees as $e){
          $division = $this->db->query("select * from divisions where id = ?",[$e['division_id']])->row_array();

          $permission = $this->db->query("select * from tx_request_izin x join tx_request_izin_pegawai y on x.request_izin_id = y.request_izin_id where x.is_status = ? and y.pegawai_id = ? and curdate() between x.tanggal_request and x.tanggal_request_end",[1,$e['pegawai_id']])->result_array();
          $workSystem = explode("-", $division['work_system']);
          $workSystemType = $workSystem[0];
          $workSystemId = $workSystem[1];
          

          $qDetail1 = "select * from m_pola_kerja_det where pola_kerja_id = ? and is_day = ?";
          $qDetail2 = "select * from employee_shift es join shift_detail sd on es.shift_detail_id = sd.shift_detail_id where es.employee_id = ?";
          $qDetailParams = $workSystemType === 'wd' ? [$workSystemId,date('N')]:[$e['pegawai_id']];
          $pDetail = $workSystemType === 'wd' ? $this->db->query($qDetail1,$qDetailParams)->row_array():$this->db->query($qDetail2,$qDetailParams)->row_array();
          $pPattern = $workSystemType === 'wd' ? $this->db->query("select * from m_pola_kerja where pola_kerja_id = ?",[$workSystemId])->row_array() : $pDetail;
          $pTolerance = $workSystemType === 'wd' ? new DateTime($pDetail['jam_masuk']) : new DateTime($pPattern['clock_in']);
          $tolerance = $workSystemType === 'wd' ? $pPattern['toleransi_terlambat']:$pDetail['tardiness_tolerance'];
          $pTolerance->modify("+{$tolerance} minutes");
          
          $jMasuk = $workSystemType === 'wd' ? $pDetail['jam_masuk'] : $pPattern['clock_in'];
          $jKeluar = $workSystemType === 'wd' ? $pDetail['jam_pulang'] : $pPattern['clock_out'];


          $data1 = [
            'absen_id' => uniqid(),
            'company_id' => $company['id'],
            'pegawai_id' => $e['pegawai_id'],
            'tanggal_absen' => date('Y-m-d'),
            'is_status' => 'free',
            'jam_masuk' => '00:00',
            'jam_istirahat' => '00:00',
            'jam_sistirahat' => '00:00',
            'jam_keluar' => '00:00',
            'catatan_masuk' => '...',
            'catatan_keluar' => '...',
            'j_masuk' => $jMasuk,
            'j_pulang' => $jKeluar,
            'j_toleransi' => $pTolerance->format("H:i"),
            's_istirahat_photo' => 'no',
            's_istirahat_latitude' => 0,
            's_istirahat_longitude' => 0,
            'foto_absen_masuk' => 'no',
            'foto_absen_keluar' => 'no',
            'point_latitude' => 0,
            'point_longitude' => 0,
            'latitude_masuk' => 0,
            'longitude_masuk' => 0,
            'latitude_keluar' => 0,
            'longitude_keluar' => 0,
            'htu' => 0
          ];

          $data2 = [
            ...$data1,
            'is_status'=>'alpha-1'
          ];

          $data3 = [
            ...$data1,
            'is_status'=>'alpha-2'
          ];

      
          if(count($companyHolidays) > 0 || count($globalHolidays) > 0){
            if(count($globalHolidays) > 0){
              if($e['jumlah_cuti'] > 0){
                $sisa = $e['jumlah_cuti'] - 1;
                $update = ['jumlah_cuti' => $sisa];
                $target = ['pegawai_id' => $e['pegawai_id']];
                
                $this->db->update(
                  'm_pegawai',
                  $update,
                  $target
                );
              }


              $this->db->insert(
                'tx_absensi',
                $data1
              );
            }

            if(count($companyHolidays) > 0 && count($globalHolidays) <  1){
              $this->db->insert(
                'tx_absensi',
                $data1
              );
            }

          }

          if(count($companyHolidays) < 1 && count($globalHolidays) < 1){
            $workSystem = explode("-",$division['work_system'])[0];
            $workSysId = explode("-",$division['work_system'])[1];

            if($workSystem == "s"){
              $off = $this->db->query("select * from shift_off where employee_id = ? and day = ?",[$e['pegawai_id'],Date('N')])->row_array();
              $onDuty = $this->db->query("select * from assignment where employee_id  = ? and curdate() between start_from and until",[$e['pegawai_id']])->row_array();

              if(!$off){
                if(count($permission) > 0){
                  $permitted = [
                    ...$data1,
                    'is_status' => $permission[0]['tipe_request']
                  ];

                  $this->db->insert(
                    'tx_absensi',
                    $permitted
                  );
                }
                else{
                  if($onDuty){
                    $duty = [
                      ...$data1,
                      'is_status' => 'alpha-2'
                    ];

                    $this->db->insert(
                      'tx_absensi',
                      $duty
                    );
                  }
                  else{
                    if($division["alpha_penalty"] == 1){
                      if($division['alpha_consequence'] == "1"){
                        $this->db->insert(
                          'tx_absensi',
                          $data3
                        );
                      }
                      if($division['alpha_consequence'] == "2"){
                        if($e['jumlah_cuti'] > 0){
                          $this->db->insert(
                            'tx_absensi',
                           $data2
                          );
                        }
                        else{
                          $this->db->insert(
                            'tx_absensi',
                            $data3
                          );
                        }
                      }
                    }
                    else{
                      $data = [
                        ...$data1,
                        'is_status' => 'alpha-0'
                      ];

                      $this->db->insert(
                        'tx_absensi',
                        $data
                      );
                    }
                  }
                }
              }
              else{
                $offData = [
                  ...$data1,
                  'is_status' => 'off'
                ];

                $this->db->insert(
                  'tx_absensi',
                  $offData
                );
              }
            }
            else{
              $off = $this->db->query("select * from m_pola_kerja mpk join m_pola_kerja_det mpkd on mpk.pola_kerja_id = mpkd.pola_kerja_id where mpk.pola_kerja_id = ? and is_day = ? and is_work='n'",[$workSysId,Date('N')])->row_array();
              $onDuty = $this->db->query("select * from assignment where employee_id  = ? and curdate() between start_from and until",[$e['pegawai_id']])->row_array();


              if(!$off){
                if(count($permission) > 0){
                  $permitted = [
                     ...$data1,
                     'is_status' => $permission[0]['tipe_request']
                    ];

                  $this->db->insert(
                    'tx_absensi',
                    $permitted
                  );
                }
                else{
                  if($onDuty){
                    $duty = [
                      ...$data1,
                      'is_status' => 'alpha-2'
                    ];

                    $this->db->insert(
                      'tx_absensi',
                      $duty
                    );
                  }
                  else{
                    if($division["alpha_penalty"] == 1){
                      if($division['alpha_consequence'] == "1"){
                        $this->db->insert(
                          'tx_absensi',
                          $data3
                        );
                      }
                      if($division['alpha_consequence'] == "2"){
                        if($e['jumlah_cuti'] > 0){
                          $this->db->insert(
                            'tx_absensi',
                           $data2
                          );
                        }
                        else{
                          $this->db->insert(
                            'tx_absensi',
                            $data3
                          );
                        }
                      }
                    }
                    else{
                      $data = [
                        ...$data1,
                        'is_status' => 'alpha-0'
                      ];

                      $this->db->insert(
                        'tx_absensi',
                        $data
                      );
                    }
                  }
                }
              }
              else{
                $offData = [
                  ...$data1,
                  'is_status' => 'off'
                ];

                $this->db->insert(
                  'tx_absensi',
                  $offData
                );
              }
            }
          }
        } 
      }
    }

    #[SkipPermission]
    public function recap(){
      $companies = $this->db->query("select * from companies")->result_array();

      foreach($companies as $company){
        $data = $this->db->query("select * from tx_absensi where company_id = ? and tanggal_absen = curdate()",[$company['id']])->result_array();
      
        foreach($data as $d){
          if($d["is_status"] == "alpha-2"){
            $start= date('Y-m-01');
            $until = date('Y-m-d');
            $e = $this->db->query("select * from m_pegawai where pegawai_id = ?",[$d['pegawai_id']])->row_array();
            $recap = $this->db->query("select * from recap where employee_id = ? and date between ? and ?",[$d['pegawai_id'],$start,$until])->num_rows();
            $required = $recap < 26 ? true : false;
            
            $recapData = [
              'recap_id' => uniqid(),
              'date' => date('Y-m-d'),
              'employee_id' => $e['pegawai_id'],
              'isAlpha' => true,
              'required' => $required
            ];

            $this->db->insert(
              'recap',
              $recapData
            );
          }

          if($d['is_status'] != 'alpha-2' && $d['is_status'] != 'off'){
            $e = $this->db->query("select * from m_pegawai where pegawai_id = ?",[$d['pegawai_id']])->row_array();
            $start= date('Y-m-01');
            $until = date('Y-m-d');
            $recap = $this->db->query("select * from recap where employee_id = ? and date between ? and ?",[$d['pegawai_id'],$start,$until])->num_rows();
            $required = $recap < 26 ? true : false;
            $recapData = [
              'recap_id' => uniqid(),
              'date' => date('Y-m-d'),
              'employee_id' => $e['pegawai_id'],
              'isAlpha' => false,
              'required' => $required
            ];

            $this->db->insert(
              'recap',
              $recapData
            ); 
          }
        }
      }
    }
    
    #[SkipPermission]
    public function deduction(){
      $this->db->query("SET time_zone = '+07:00'");
      $companies = $this->db->query("select * from companies")->result_array();

      foreach($companies as $company){
        $data = $this->db->query("select * from tx_absensi where company_id = ? and tanggal_absen = curdate()",[$company['id']])->result_array();
      
        foreach($data as $d){
          if($d["is_status"] == "alpha-1"){
            $this->db->set('jumlah_cuti', 'jumlah_cuti - 1', FALSE);
            $this->db->where('pegawai_id', $d['pegawai_id']);
            $this->db->update('m_pegawai');
          }

          if($d["is_status"] == "alpha-2"){
            $e = $this->db->query("select * from m_pegawai where pegawai_id = ?",[$d['pegawai_id']])->row_array();
            $div = $this->db->query("select * from divisions where id = ?",[$e['division_id']])->row_array();

            if($div['alpha_penalty_type'] == "percent"){
              $penaltyValue = $div['alpha_penalty_value'] / 100;
              if (date('m') === '02') {
                $deductionValue = ($e['salary'] / 24) * $penaltyValue;
              }
              else{
                $deductionValue = ($e['salary'] / 26) * $penaltyValue;
              }
              

              $data = [
                'deduction_id' => uniqid(),
                'employee_id' => $e['pegawai_id'],
                'deduction_type' => $d['is_status'],
                'date' => date('Y-m-d'),
                'amount' => $deductionValue,
                'note' => '...'
              ];

              $this->db->insert(
                'salary_deduction',
                $data
              );
            }
            if($div['alpha_penalty_type'] == "custom"){
              $penaltyValue = $div['alpha_penalty_value'];

              $data = [
                'deduction_id' => uniqid(),
                'employee_id' => $e['pegawai_id'],
                'deduction_type' => $d['is_status'],
                'date' => date('Y-m-d'),
                'amount' => $penaltyValue,
                'note' => '...'
              ];

              $this->db->insert(
                'salary_deduction',
                $data
              );
            }
          }
          
          if($d['is_status'] == 'hhk' && $d['jam_keluar'] == '00:00'){
            $e = $this->db->query("select * from m_pegawai where pegawai_id = ?",[$d['pegawai_id']])->row_array();
            $div = $this->db->query("select * from divisions where id = ?",[$e['division_id']])->row_array();

            $penaltyValue = $div['penalty_nominal'];
              
            $data = [
              'deduction_id' => uniqid(),
              'employee_id' => $e['pegawai_id'],
              'deduction_type' => 'clockout forget',
              'date' => date('Y-m-d'),
              'amount' => $penaltyValue,
              'note' => '...'
            ];

            $this->db->insert(
              'salary_deduction',
              $data
            );
          }

          if($d['is_status'] == 'i'){
            $e = $this->db->query("select * from m_pegawai where pegawai_id = ?",[$d['pegawai_id']])->row_array();
            $div = $this->db->query("select * from divisions where id = ?",[$e['division_id']])->row_array();

            $offDaysAmount = $e['jumlah_cuti'];
            
            if($div['alpha_consequence'] == "2"){
              if($e['jumlah_cuti'] > 0){
                $offDaysAmount = $offDaysAmount - 1;
                $data =  ['jumlah_cuti' => $offDaysAmount];
                $this->db->where('pegawai_id',$e['pegawai_id']);
                $this->db->update('m_pegawai',$data);
              }
              else{
                $data = [
                  'deduction_id' => uniqid(),
                  'employee_id' => $e['pegawai_id'],
                  'deduction_type' => $d['is_status'],
                  'date' => date('Y-m-d'),
                  'amount' => $penaltyValue,
                  'note' => '...'
                ];

                $this->db->insert(
                  'salary_deduction',
                  $data
                );
              }
            }
            else{
              $data = [
                'id' => uniqid(),
                'employee_id' => $e['pegawai_id'],
                'deduction_type' => $d['is_status'],
                'date' => date('Y-m-d'),
                'amount' => $penaltyValue,
                'note' => '...'
              ];

              $this->db->insert(
                'salary_deduction',
                $data
              );
            }
          }

        }
      }
    }
}
