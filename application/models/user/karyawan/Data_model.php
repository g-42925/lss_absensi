<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Data_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

	public function get_data($companyId) {
        $query = $this->db->query("SELECT * FROM m_pegawai
        	WHERE is_del='n' and company_id=$companyId")->result_array();
        return $query;
    }

    public function getWithFilter($companyId,$filter) {
        $query = null;
        $nik = $filter['nik'];
        $div = $filter['div'];

        if($filter['div'] == ''){
          if($filter['nik'] == ''){
            $query = $this->db->query("SELECT * FROM m_pegawai WHERE is_del='n' and company_id=$companyId ")->result_array();
          }
          else{
            $query = $this->db->query("SELECT * FROM m_pegawai WHERE is_del='n' and company_id=$companyId and (nik='$nik' or nama_pegawai like '%$nik%')")->result_array();
          }
        }
        else{
          if($filter['nik'] == ''){
            $query = $this->db->query("SELECT * FROM m_pegawai WHERE is_del='n' and company_id=$companyId and division_id='$div' ")->result_array();
          }
          else{
            $query = $this->db->query("SELECT * FROM m_pegawai WHERE is_del='n' and company_id=$companyId and division_id='$div' and (nik='$nik' or nama_pegawai like'%$nik%')")->result_array();
          }         
        }

        return $query;
    }

    public function add_proses($companyId) {
        $division = $this->db->query("select * from divisions where id = ?",[$this->input->post('division')])->row_array();
        $totalEmployee = $this->db->query("select * from m_pegawai where company_id = ?",[$companyId])->num_rows();
        $company = $this->db->query("select * from companies where id = ?",[$companyId])->row_array();
        $idPegawai = str_pad($totalEmployee,3,'0',STR_PAD_LEFT);
        $workSystemType = explode('-',$division['work_system'])[0];
        $workSystemId = explode('-',$division['work_system'])[1];
        
        $q1 = "select * from m_pola_kerja_det where pola_kerja_id = ? and is_day = ?";
        $pattern = $workSystemType === 'wd' ? $this->db->query($q1,[$workSystemId,date('N')])->row_array() : null;
        

        
        $nik = $this->input->post('nik');

        $initials = "";
        
        foreach(explode(" ", $company['company_name']) as $w){
          $initials = $initials . substr($w, 0, 1);
        };

        $idsync = date('Ymdhis').$this->input->post('nom');

        $data = [
            'id_sync'             => $idsync,
            'id_pegawai'          => $initials."-".$idPegawai."-".substr($nik, -3),
            'company_id'          => $companyId,
            'nama_pegawai'  	    => $this->input->post('nama'),
            'email_pegawai'  	    => $this->input->post('email'),
            'nomor_pegawai'       => $this->input->post('nom'),
            'jenis_kelamin'       => $this->input->post('jeniskelamin'),
            'tanggal_mulai_kerja' => $this->input->post('tglmulai'),
            'jumlah_cuti'         => $this->input->post('jumlahCuti'),
            'salary'              => $this->input->post('salary'),
            'division_id'         => $this->input->post('division'),
            'password_pegawai'    => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
            'foto_pegawai'        => 'assets/uploaded/users/default-logo.png',
            'is_status'  		      => $this->input->post('status'),
            'status_pegawai'      => $this->input->post('statusPegawai'),
            'position_id'         => $this->input->post('position'),
            'created_at'  		    => date('Y-m-d H:i:s'),
            'nik'                 => $this->input->post('nik'),
            'contract_start_date' => $this->input->post('contract_start_date'),
            'contract_end_date'   => $this->input->post('contract_end_date'),
            'on_training'         => $this->input->post('on_training'),
            'address'             => $this->input->post('alamat'),
            'married'             => $this->input->post('statusPernikahan')
        ];

        $this->db->trans_begin();
        
        $newEmployee = $this->db->insert(
            'm_pegawai', 
            $data
        );

        $newPegawaiId = $this->db->insert_id();

        
        $txAbsensi = [
          'absen_id' => uniqid(),
          'company_id' => $this->session->userdata('company_id'),
          'pegawai_id' => $newPegawaiId,
          'tanggal_absen' => date('Y-m-d'),
          'is_status' => 'alpha-2',
          'jam_masuk' => '00:00',
          'jam_istirahat' => '00:00',
          'jam_sistirahat' => '00:00',
          'jam_keluar' => '00:00',
          'catatan_masuk' => '...',
          'catatan_keluar' => '...',
          'j_masuk' => $pattern['jam_masuk'],
          'j_pulang' => $pattern['jam_pulang'],
          'j_toleransi' => '00:00',
          's_istirahat_photo' => '',
          'foto_absen_masuk' => 'no',
          'foto_absen_keluar' => 'no'
        ];



        // Jika divisi menggunakan pola kerja shift (work_system = 's-xxx'), simpan jadwal shift
        if ($workSystemType === 's' && $this->input->post('shift_detail_id')) {
            $employeeShiftData = [
                'employee_shift_id' => uniqid('es_'),
                'employee_id'       => $newPegawaiId,
                'shift_detail_id'   => $this->input->post('shift_detail_id'),
            ];

            $shiftDetail = $this->db->query("select * from shift_detail where shift_detail_id = ?",[$this->input->post('shift_detail_id')])->row_array();
           
            $txAbsensi['j_masuk'] = $shiftDetail['clock_in'];
            $txAbsensi['j_pulang'] = $shiftDetail['clock_out'];

            $this->db->insert(
              'tx_absensi', 
              $txAbsensi
            );

            $this->db->insert('employee_shift', $employeeShiftData);
        }
        else{
            $this->db->insert(
              'tx_absensi', 
              $txAbsensi
            );
        }


        if($this->db->trans_status() === FALSE) {
          $this->db->trans_rollback();
          return false;
        } 
        else {
          $this->db->trans_commit();
          return $newEmployee;
        }
    }

    public function edit_proses($id) {
        $pega = authKaryawan($id);
        $pass = $this->input->post('password');
        if ($pass=='') {
            $passnya = $pega['password_pegawai'];
        }else{
            $passnya = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
        }

        $division = $this->db->query("select * from divisions where id = ?",[$this->input->post('division')])->row_array();
        $workSystemType = $division ? explode('-', $division['work_system'])[0] : '';

        $this->db->set([
            'id_pegawai'          => $this->input->post('idkar'),
            'nama_pegawai'        => $this->input->post('nama'),
            'email_pegawai'       => $this->input->post('email'),
            'nomor_pegawai'       => $this->input->post('nom'),
            'jenis_kelamin'       => $this->input->post('jeniskelamin'),
            'tanggal_mulai_kerja' => $this->input->post('tglmulai'),
            'is_status'           => $this->input->post('status'),
            'jumlah_cuti'         => $this->input->post('jumlahCuti'),
            'salary'              => $this->input->post('salary'),
            'division_id'         => $this->input->post('division'),
            'status_pegawai'      => $this->input->post('statusPegawai'),
            'position_id'         => $this->input->post('position'),
            'password_pegawai'    => $passnya,
            'nik'                 => $this->input->post('nik'),
            'contract_start_date' => $this->input->post('contract_start_date'),
            'contract_end_date'   => $this->input->post('contract_end_date'),
            'on_training'         => $this->input->post('on_training'),
            'address'             => $this->input->post('alamat'),
            'married'             => $this->input->post('statusPernikahan')
        ]);
        $this->db->where('pegawai_id', $id);
        $res = $this->db->update('m_pegawai');

        if($res==true){
            $this->db->set([
                'first_name'    => $this->input->post('nama'),
                'last_name'    => '',
                'phone'         => $this->input->post('nom'),
                'email'         => $this->input->post('email'),
                'id_pegawai'       => $this->input->post('idkar'),
                'password_pegawai' => $passnya
            ]);
            $this->db->where('id_sync', $pega['id_sync']);
            $this->db->update('employee_history');

            // Update jadwal shift jika divisi menggunakan pola kerja shift
            if ($workSystemType === 's' && $this->input->post('shift_detail_id')) {
                $shiftDetailId = $this->input->post('shift_detail_id');
                $existingShift = $this->db->query(
                    "SELECT employee_shift_id FROM employee_shift WHERE employee_id = ?", [$id]
                )->row_array();

                if ($existingShift) {
                    // Update record yang sudah ada
                    $this->db->where('employee_id', $id);
                    $this->db->update('employee_shift', ['shift_detail_id' => $shiftDetailId]);
                } else {
                    // Insert record baru
                    $this->db->insert('employee_shift', [
                        'employee_shift_id' => uniqid('es_'),
                        'employee_id'       => $id,
                        'shift_detail_id'   => $shiftDetailId,
                    ]);
                }
            }
        }

        return $res;
    }

}