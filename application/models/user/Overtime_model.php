<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Overtime_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

	public function get_data() {
        $query = $this->db->query("SELECT * FROM tx_lembur ORDER BY lembur_id DESC")->result_array();
        return $query;
    }

    public function get_karyawan($id) {
        $query = $this->db->query("SELECT a.*, b.pegawai_id as pid FROM m_pegawai a LEFT JOIN tx_lembur_pegawai b ON a.pegawai_id=b.pegawai_id AND b.lembur_id='$id' WHERE a.is_status='y' AND a.is_del='n' ORDER BY id_pegawai ASC")->result_array();
        return $query;
    }

    public function detail_user_lembur($id) {
        $query = $this->db->query("SELECT a.id_pegawai, a.nama_pegawai, b.* 
        FROM m_pegawai a JOIN tx_lembur_pegawai b ON a.pegawai_id=b.pegawai_id WHERE b.lembur_id='$id' AND a.is_status='y' AND a.is_del='n' ORDER BY id_pegawai ASC")->result_array();
        return $query;
    }

    public function add_proses() {

        $data = [
            'tanggal_lembur'        => $this->input->post('tgl1'),
            'masuk_lembur'          => $this->input->post('jmasuk'),
            'keluar_lembur'         => $this->input->post('jkeluar'),
            'is_status'             => 1, // 0 pending, 1 acc, 2 tolak
            'created_at'            => date('Y-m-d H:i:s')
        ];
        $res = $this->db->insert('tx_lembur', $data);
        $idnya = $this->db->insert_id();

        $idp = $this->input->post('idp');
        if ($idp!='') {
            foreach ($idp as $key => $value) {
                if($value!=""){
                    $data = [
                        'lembur_id'   => $idnya,
                        'pegawai_id'  => $value,
                        'tanggal_lembur'        => $this->input->post('tgl1'),
                        'absen_masuk'           => $this->input->post('amasuk'),
                        'absen_keluar'          => $this->input->post('akeluar'),
                        'catatan_hasil_lembur'  => $this->input->post('catatanhl')
                    ];
                    $this->db->insert('tx_lembur_pegawai', $data);
                }
            }
        }
        return $res;
    }

    public function edit_proses($id) {

        $this->db->set([
            'tanggal_lembur'        => $this->input->post('tgl1'),
            'masuk_lembur'          => $this->input->post('jmasuk'),
            'keluar_lembur'         => $this->input->post('jkeluar'),
            'is_status'             => $this->input->post('status'),
            'is_acc_updated'        => $this->session->userdata('u_id'),
            'updated_at'            => date('Y-m-d H:i:s')
        ]);
        $this->db->where('lembur_id', $id);
        $res = $this->db->update('tx_lembur');        

        $this->db->delete('tx_lembur_pegawai', ['lembur_id' => $id]);
        $idp = $this->input->post('idp');
        if ($idp!='') {
            foreach ($idp as $key => $value) {

                if($value!=''){
                    $data = [
                        'lembur_id'   => $id,
                        'pegawai_id'  => $value,
                        'tanggal_lembur'        => $this->input->post('tgl1'),
                        'absen_masuk'           => $this->input->post('amasuk'),
                        'absen_keluar'          => $this->input->post('akeluar'),
                        'catatan_hasil_lembur'  => $this->input->post('catatanhl')
                    ];
                    $this->db->insert('tx_lembur_pegawai', $data);
                }
            }
        }

        return $res;
    }

}