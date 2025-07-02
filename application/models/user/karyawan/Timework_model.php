<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Timework_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

	public function get_data() {
        $query = $this->db->query("SELECT a.*, b.pegawai_id as pid, b.mulai_berlaku_tanggal, c.jumlah_hari_siklus, c.nama_pola, c.pola_kerja_id 
            FROM m_pegawai a 
            LEFT JOIN m_pegawai_pola b ON a.pegawai_id=b.pegawai_id AND b.is_del='n' AND b.is_selected='y'
            LEFT JOIN m_pola_kerja c ON b.pola_kerja_id=c.pola_kerja_id AND c.is_del='n'
        	WHERE a.is_del='n'")->result_array();
        return $query;
    }

    public function add_proses($id) {

        $this->db->set(['is_selected' => 'n']);
        $this->db->where('pegawai_id', $id);
        $this->db->update('m_pegawai_pola');

        $data = [
            'pegawai_id'              => $id,
            'pola_kerja_id'  	      => $this->input->post('pola'),
            'mulai_berlaku_tanggal'   => $this->input->post('tglmulai'),
            'dari_hari_ke'            => $this->input->post('harike'),
            'created_at'  		      => date('Y-m-d H:i:s')
        ];
        $res = $this->db->insert('m_pegawai_pola', $data);
        return $res;
    }

    public function edit_proses($id) {
        $this->db->set([
            'pola_kerja_id'           => $this->input->post('pola'),
            'mulai_berlaku_tanggal'   => $this->input->post('tglmulai'),
            'dari_hari_ke'            => $this->input->post('harike')
        ]);
        $this->db->where('pegawai_pola_id', $id);
        $res = $this->db->update('m_pegawai_pola');
        return $res;
    }

    public function get_record($id) {
        $data = array();
        $query = $this->db->query("SELECT * FROM m_pegawai_pola a JOIN m_pola_kerja b ON a.pola_kerja_id=b.pola_kerja_id
            WHERE a.is_del='n' AND b.is_del='n' AND a.pegawai_id='$id' ORDER BY a.pegawai_pola_id DESC")->result_array();
        return $query;
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