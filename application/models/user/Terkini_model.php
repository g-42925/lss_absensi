<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Terkini_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

	public function get_data($tglawal, $tglakhr) {

        $this->db->set(['is_read' => 'y']);
        // $this->db->where('lt_id', $id);
        $this->db->update('tx_lokasi_terkini');

        $query = "SELECT * FROM tx_lokasi_terkini a
            JOIN m_pegawai b ON a.pegawai_id=b.pegawai_id
            WHERE b.is_status='y' AND b.is_del='n' AND a.tanggal BETWEEN '$tglawal' AND '$tglakhr' ORDER BY lt_id DESC";
        return $this->db->query($query)->result_array();
    }

}