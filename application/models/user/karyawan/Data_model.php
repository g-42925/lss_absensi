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

    public function add_proses($companyId) {
        $totalEmployee = $this->db->query("select * from m_pegawai where company_id = ?",[$companyId])->num_rows();
        $company = $this->db->query("select * from companies where id = ?",[$companyId])->row_array();
        $idPegawai = str_pad($totalEmployee,3,'0',STR_PAD_LEFT);

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
            'nama_pegawai'  	  => $this->input->post('nama'),
            'email_pegawai'  	  => $this->input->post('email'),
            'nomor_pegawai'       => $this->input->post('nom'),
            'jenis_kelamin'       => $this->input->post('jeniskelamin'),
            'tanggal_mulai_kerja' => $this->input->post('tglmulai'),
            'jumlah_cuti'         => $this->input->post('jumlahCuti'),
            'salary'              => $this->input->post('salary'),
            'division_id'         => $this->input->post('division'),
            'password_pegawai'    => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
            'foto_pegawai'        => 'assets/uploaded/users/default-logo.png',
            'is_status'  		  => $this->input->post('status'),
            'status_pegawai'      => $this->input->post('statusPegawai'),
            'position_id'         => $this->input->post('position'),
            'created_at'  		  => date('Y-m-d H:i:s'),
            'nik'                 => $this->input->post('nik'),
            'contract_start_date' => $this->input->post('contract_start_date'),
            'contract_end_date'   => $this->input->post('contract_end_date'),
            'on_training'         => $this->input->post('on_training')
        ];
        
        return $this->db->insert(
            'm_pegawai', 
            $data
        );

        // if($res==true){
        //     $postData = [
        //         'first_name'    => $this->input->post('nama'),
        //         'designation'   => 1,
        //         'phone'         => $this->input->post('nom'),
        //         'email'         => $this->input->post('email'),
        //         'id_sync'       => $idsync,
        //         'id_pegawai'       => $this->input->post('idkar'),
        //         'password_pegawai' => password_hash($this->input->post('password_pegawai'), PASSWORD_DEFAULT)
        //     ];
        //     $this->db->insert('employee_history', $postData);
        // }
    }

    public function edit_proses($id) {
        $pega = authKaryawan($id);
        $pass = $this->input->post('password');
        if ($pass=='') {
            $passnya = $pega['password_pegawai'];
        }else{
            $passnya = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
        }

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
            'on_training'         => $this->input->post('on_training')

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
        }

        return $res;
    }

}