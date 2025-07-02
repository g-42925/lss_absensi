<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Patterns_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

	public function get_data() {
        $query = $this->db->query("SELECT * FROM m_pola_kerja
        	WHERE is_del='n'")->result_array();
        return $query;
    }

    public function add_proses() {
        $data = [
            'nama_pola'               => $this->input->post('nama'),
            'jumlah_hari_siklus'  	  => $this->input->post('jumlahhari'),
            'toleransi_terlambat'  	  => $this->input->post('tolet'),
            'created_at'  		      => date('Y-m-d H:i:s')
        ];
        $res = $this->db->insert('m_pola_kerja', $data);
        $idnya = $this->db->insert_id();

        if ($res==true) {
            $hari_kerja = 0;
            for ($i=0; $i < $this->input->post('jumlahhari') ; $i++) { 
                if ($this->input->post('work')[$i]=='y') { $hari_kerja += 1; }
                $pdet = [
                    'pola_kerja_id'     => $idnya,
                    'is_day'            => $i+1,
                    'is_work'           => $this->input->post('work')[$i],
                    'is_polkat'         => $this->input->post('sistemkerja')[$i],
                    'jam_masuk'         => $this->input->post('masuk')[$i],
                    'jam_pulang'        => $this->input->post('pulang')[$i]
                ];
                $res = $this->db->insert('m_pola_kerja_det', $pdet);
            }

            $this->db->set([
                'jumlah_kerja'        => $hari_kerja,
                'jumlah_libur'        => $this->input->post('jumlahhari')-$hari_kerja
            ]);
            $this->db->where('pola_kerja_id', $idnya);
            $res = $this->db->update('m_pola_kerja');
        }
        return $res;
    }

    public function edit_proses($id) {
        $this->db->set([
            'nama_pola'               => $this->input->post('nama'),
            'jumlah_hari_siklus'      => $this->input->post('jumlahhari'),
            'toleransi_terlambat'     => $this->input->post('tolet')
        ]);
        $this->db->where('pola_kerja_id', $id);
        $res = $this->db->update('m_pola_kerja');

        if ($res==true) {
            $this->db->delete('m_pola_kerja_det', ['pola_kerja_id' => $id]);
            $hari_kerja = 0;
            for ($i=0; $i < $this->input->post('jumlahhari') ; $i++) { 
                if ($this->input->post('work')[$i]=='y') { $hari_kerja += 1; }
                $pdet = [
                    'pola_kerja_id'     => $id,
                    'is_day'            => $i+1,
                    'is_work'           => $this->input->post('work')[$i],
                    'is_polkat'         => $this->input->post('sistemkerja')[$i],
                    'jam_masuk'         => $this->input->post('masuk')[$i],
                    'jam_pulang'        => $this->input->post('pulang')[$i]
                ];
                $res = $this->db->insert('m_pola_kerja_det', $pdet);
            }

            $this->db->set([
                'jumlah_kerja'        => $hari_kerja,
                'jumlah_libur'        => $this->input->post('jumlahhari')-$hari_kerja
            ]);
            $this->db->where('pola_kerja_id', $id);
            $res = $this->db->update('m_pola_kerja');
        }
        return $res;
    }

    public function get_assign($id) {
        $query = $this->db->query("SELECT a.*, max(b.pegawai_pola_id)as pegawai_pola_id, count(b.pegawai_id)as totalpola FROM m_pegawai a
            JOIN m_pegawai_pola b ON a.pegawai_id=b.pegawai_id
            WHERE a.is_del='n' AND b.pola_kerja_id='$id' GROUP BY a.pegawai_id")->result_array();
        return $query;
    }

    public function get_karyawan($id) {
        $data = array();
        $query = dataKaryawan();

        foreach ($query as $row) {
            $data[] = array(
                'pegawai_id'    => $row['pegawai_id'],
                'nama_pegawai'  => $row['nama_pegawai']
            );
        }
        return $data;
    }

    public function assign_proses($id) {
        $res = false;
        $idp = $this->input->post('idp');
        if ($idp!='') {
            foreach ($idp as $key => $value) {
                if($value!=""){
                    $query = $this->db->query("SELECT * FROM m_pegawai_pola WHERE pegawai_id='$value'");
                    $cekrow = $query->num_rows();
                    if($cekrow>0){
                        $this->db->set(['is_selected' => 'n']);
                        $this->db->where('pegawai_id', $value);
                        $this->db->update('m_pegawai_pola');
                    }
                    $data = [
                        'pegawai_id'              => $value,
                        'pola_kerja_id'           => $this->input->post('pola'),
                        'mulai_berlaku_tanggal'   => $this->input->post('tglmulai'),
                        'dari_hari_ke'            => $this->input->post('harike'),
                        'created_at'              => date('Y-m-d H:i:s')
                    ];
                    $res = $this->db->insert('m_pegawai_pola', $data);
                }
            }
        }

        return $res;
    }

}