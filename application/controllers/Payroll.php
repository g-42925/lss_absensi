<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Payroll extends CI_Controller {
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
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/req_permission_model', 'rp');
    }

    public function filter($id) {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Karyawan';
        $data['title']      = 'Data Karyawan';
        $data['namalabel']  = "Filter Payroll";
        $data['auth']       = authUser();

        $data['id'] = $id;

        $companyId = $this->session->userdata('company_id');

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/payroll/filter',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function check($empId){
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Karyawan';
        $data['title']      = 'Data Karyawan';
        $data['namalabel']  = "Filter Payroll";
        $data['auth']       = authUser();


        $dateX = $this->input->post('start');
        $dateY = $this->input->post('end');

        $data['id'] = $empId;

        $data['from'] = $dateX;
        $data['to'] = $dateY;

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
        $attendance = $this->db->query("select * from tx_absensi where tanggal_absen between ? and ? and pegawai_id = ?",[$dateX,$dateY,$empId])->result_array();

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
    
        $emp['salary'] = (int) ($emp['salary'] / 26) * count($attendance) - ((int) ($emp['salary'] / 26) * $offDays);
    
        $alphaPenalty = $this->db->query("select sum(amount) as amt from salary_deduction where employee_id = ? and date between ? and ? and deduction_type = 'alpha-2'",[$empId,$dateX,$dateY])->row_array();

        $deductions = $this->db->query("select * from salary_deduction where employee_id = ? and date between ? and ?",[$empId,$dateX,$dateY])->result_array();

        foreach($deductions as $d){
          if($d['deduction_type'] == "late penalty" || $d['deduction_type'] == "after break late" || $d['deduction_type'] == "clockout late penalty" || $d['deduction_type'] == "clockout forget"){
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
          if((int) $deductionValue > 0){
            $penalty[] = [
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
              'name' => 'reimburse',
              'value' => (int) $qReimburse['val']
            ];
          }

          $qPosition = $this->db->query("select * from position where id = ?",[$emp['position_id']])->row_array();

          $emp['position'] = $qPosition['name'];

          $companyId = $this->session->userdata('company_id');

          $company = $this->db->query("select * from companies where id = ?",[$companyId])->row_array();

          $data['final'] =  [
            'emp' => $emp,
            'company' => $company,
            'salary' => $emp['salary'],
            'allowance' => $income,
            'benefit' => $benefit,
            'penalty' => $penalty,
            'totalIncome' => $emp['salary'] + $totalAllowance + $totalOverwork + $qReimburse['val'],
            'totalBenefit' => (int) $totalBenefit + $alphaPenalty['amt'] + $deductionValue,
            'thp' => $salary,
          ];

          $this->load->view('templates/header', $data);
          $this->load->view('templates/sidemenu', $data);
          $this->load->view('templates/sidenav', $data);
          $this->load->view('module/payroll/result',$data);
          $this->load->view('templates/footer', $data);
          $this->load->view('templates/fscript-html-end', $data);
  }

    public function accumulationV2(){
        cek_menu_access();
    	$data['htmlpagejs'] = 'none';
    	$data['nmenu']      = 'Karyawan';
    	$data['title']      = 'Data Karyawan';
    	$data['namalabel']  = "Total Take Home Pay";
    	$data['auth']       = authUser();

    	$dateX = $this->input->post('start');
    	$dateY = $this->input->post('end');

		$data['dateX'] = $dateX;
		$data['dateY'] = $dateY;

    	$companyId = $this->session->userdata('company_id');

    	$divisions = $this->db->query("select * from divisions where company_id = ?",[$companyId])->result_array();

    	foreach($divisions as $divIndex => $div){
        	$employees = $this->db->query("select * from m_pegawai where division_id = ? and is_del='n' order by salary desc",[$div['id']])->result_array();

        	foreach($employees as $empIndex => $e){
            	$empId = $e['pegawai_id'];
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

        		$attendance = $this->db->query("select * from tx_absensi where tanggal_absen between ? and ? and pegawai_id = ?",[$dateX,$dateY,$empId])->result_array();

            
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
    
        $e['salary'] = (int) ($e['salary'] / 26) * count($attendance) - ((int) ($e['salary'] / 26) * $offDays);

			      $employees[$empIndex]['salaryx'] = (int) ($e['salary'] / 26) * count($attendance) - ((int) ($e['salary'] / 26) * $offDays);

    
        		$alphaPenalty = $this->db->query("select sum(amount) as amt from salary_deduction where employee_id = ? and date between ? and ? and deduction_type = 'alpha-2'",[$empId,$dateX,$dateY])->row_array();
  
     

        		$deductions = $this->db->query("select * from salary_deduction where employee_id = ? and date between ? and ?",[$empId,$dateX,$dateY])->result_array();

        		foreach($deductions as $d){
          			if($d['deduction_type'] == "late penalty" || $d['deduction_type'] == "after break late" || $d['deduction_type'] == "clockout late penalty" || $d['deduction_type'] == "clockout forget"){
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

          		$salary = ($e['salary'] - ($alphaPenalty['amt'] + $deductionValue) + $totalAllowance + $totalOverwork + $qReimburse['val']) - $totalBenefit;

          		if((int) $alphaPenalty['amt'] > 0){
            		$penalty[] = [
              			'name' => 'alpha',
              			'value' => (int) $alphaPenalty['amt']
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

          		$qPosition = $this->db->query("select * from position where id = ?",[$e['position_id']])->row_array();

          		$e['position'] = $qPosition['name'];

          		$companyId = $this->session->userdata('company_id');

          		$company = $this->db->query("select * from companies where id = ?",[$companyId])->row_array();

		  		$employees[$empIndex]['salary'] = $e['salary'];
		  		$employees[$empIndex]['thp'] = $salary;
			    $employees[$empIndex]['totalAllowance'] = $totalAllowance + $totalOverwork + $qReimburse['val'];
				$employees[$empIndex]['totalBenefit'] = (int) $totalBenefit + $alphaPenalty['amt'] + $deductionValue;

      		}

			usort($employees,function($a,$b){
			  return $b['thp'] <=> $a['thp']; // ascending
			});

			
			$divisions[$divIndex]['employees'] = $employees;

			
        }

		$data['divisions'] = $divisions;
        $summary = 0;

        foreach($data['divisions'] as $d){
          foreach($d['employees'] as $_emp){
            $summary += $_emp['thp'];
		  }
        };

        $data['summary'] = $summary;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/payroll/accumulation',$data);
        $this->load->view('templates/footer', $data);
    	$this->load->view('templates/fscript-html-end', $data);
    }

	public function toCsv($dateX,$dateY){
 		$filename = "data_payroll_" . date('Ymd_His') . ".csv";

		header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

		$output = fopen('php://output', 'w');

    	fputcsv($output, ['Employee', 'Division', 'Salary', 'Addtional', 'Minus', 'Take Home Pay']);

    	$companyId = $this->session->userdata('company_id');

    	$divisions = $this->db->query("select * from divisions where company_id = ?",[$companyId])->result_array();

    	foreach($divisions as $divIndex => $div){
        	$employees = $this->db->query("select * from m_pegawai where division_id = ? and is_del='n' order by salary desc",[$div['id']])->result_array();

        	foreach($employees as $empIndex => $e){
            	$empId = $e['pegawai_id'];
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

        		$attendance = $this->db->query("select * from tx_absensi where tanggal_absen between ? and ? and pegawai_id = ?",[$dateX,$dateY,$empId])->result_array();
    
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
    
        $emp['salary'] = (int) ($emp['salary'] / 26) * count($attendance) - ((int) ($emp['salary'] / 26) * $offDays);


			    $employees[$empIndex]['salaryx'] = (int) ($e['salary'] / 26) * count($attendance) - ((int) ($emp['salary'] / 26) * $offDays);

    
        		$alphaPenalty = $this->db->query("select sum(amount) as amt from salary_deduction where employee_id = ? and date between ? and ? and deduction_type = 'alpha-2'",[$empId,$dateX,$dateY])->row_array();
  
        		

        		$deductions = $this->db->query("select * from salary_deduction where employee_id = ? and date between ? and ?",[$empId,$dateX,$dateY])->result_array();

        		foreach($deductions as $d){
          			if($d['deduction_type'] == "late penalty" || $d['deduction_type'] == "after break late" || $d['deduction_type'] == "clockout late penalty"){
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

          		$qPosition = $this->db->query("select * from position where id = ?",[$e['position_id']])->row_array();

          		$e['position'] = $qPosition['name'];

          		$companyId = $this->session->userdata('company_id');

          		$company = $this->db->query("select * from companies where id = ?",[$companyId])->row_array();

		  		$employees[$empIndex]['salary'] = $emp['salary'];
		  		$employees[$empIndex]['thp'] = $salary;
			    $employees[$empIndex]['totalAllowance'] = $totalAllowance + $totalOverwork + $qReimburse['val'];
				$employees[$empIndex]['totalBenefit'] = (int) $totalBenefit + $alphaPenalty['amt'] + $deductionValue;

      		}

			usort($employees,function($a,$b){
			  return $b['thp'] <=> $a['thp']; // ascending
			});

			
			$divisions[$divIndex]['employees'] = $employees;
        }

    	foreach ($divisions as $div) {
          foreach($div['employees'] as $e){
			$row = [
			  'Employee' => $e['nama_pegawai'],
			  'Division' => $div['division_name'],
			  'Salary' => $emp['salary'],
			  'Additional' => $e['totalAllowance'],
			  'Minus' => $e['totalBenefit'],
			  'Take Home Pay' => $e['thp']
			];

		    fputcsv($output, $row);
		  }
    	}

    	fclose($output);
    	exit; // pastikan tidak ada output lain
	}

    public function accumulation(){
      cek_menu_access();
      $data['htmlpagejs'] = 'none';
      $data['nmenu']      = 'Karyawan';
      $data['title']      = 'Data Karyawan';
      $data['namalabel']  = "Total Take Home Pay";
      $data['auth']       = authUser();

      $dateX = $this->input->post('start');
      $dateY = $this->input->post('end');

      $companyId = $this->session->userdata('company_id');

      $employees = $this->db->query("select * from m_pegawai where company_id = ? and is_del='n'",[$companyId])->result_array();

      foreach($employees as $index => $e){
        $empId = $e['pegawai_id'];

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

        $attendance = $this->db->query("select * from tx_absensi where tanggal_absen between ? and ? and pegawai_id = ?",[$dateX,$dateY,$empId])->result_array();
    
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
    
        $emp['salary'] = (int) ($emp['salary'] / 26) * count($attendance) - ((int) ($emp['salary'] / 26) * $offDays);
    
        $alphaPenalty = $this->db->query("select sum(amount) as amt from salary_deduction where employee_id = ? and date between ? and ? and deduction_type = 'alpha-2'",[$empId,$dateX,$dateY])->row_array();

    
        $deductions = $this->db->query("select * from salary_deduction where employee_id = ? and date between ? and ?",[$empId,$dateX,$dateY])->result_array();

        foreach($deductions as $d){
          if($d['deduction_type'] == "late penalty" || $d['deduction_type'] == "after break late" || $d['deduction_type'] == "clockout late penalty"){
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

          $qPosition = $this->db->query("select * from position where id = ?",[$e['position_id']])->row_array();

          $e['position'] = $qPosition['name'];

          $companyId = $this->session->userdata('company_id');

          $company = $this->db->query("select * from companies where id = ?",[$companyId])->row_array();

		  $employees[$index]['thp'] = $salary;

      }

	  $data['data'] = $employees;
      $summary = 0;

      foreach($data['data'] as $d){
        $summary += $d['thp'];
      };

      $data['summary'] = $summary;

      $this->load->view('templates/header', $data);
      $this->load->view('templates/sidemenu', $data);
      $this->load->view('templates/sidenav', $data);
      $this->load->view('module/payroll/accumulation',$data);
      $this->load->view('templates/footer', $data);
      $this->load->view('templates/fscript-html-end', $data);
  }
}
