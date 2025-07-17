<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Locations_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

	public function get_data() {
        $data = array();
        $query = $this->db->query("SELECT * FROM m_lokasi WHERE is_del='n'");
        $num_rows = $query->num_rows();

        $no = 1;
        foreach ($query->result_array() as $row) {

            $cekkaryawan = $this->db->query("SELECT * FROM m_pegawai_lokasi a
            JOIN m_pegawai b ON a.pegawai_id=b.pegawai_id WHERE b.is_del='n' AND a.lokasi_id='$row[lokasi_id]'")->num_rows();
            
            if ($num_rows==1) {
                $cekkaryawanall = $this->db->query("SELECT * FROM m_pegawai WHERE is_del='n'")->num_rows();
                if ($cekkaryawan==0) {
                    $totalkaryawan = 'Semua Karyawan';
                }else{
                    $totalkaryawan = $cekkaryawan;
                }

            }else{
                $totalkaryawan = $cekkaryawan;
            }

            $data[] = array(
                'lokasi_id'     => $row['lokasi_id'],
                'nama_lokasi'   => $row['nama_lokasi'],
                'alamat_lokasi' => $row['alamat_lokasi'],
                'total'         => $totalkaryawan
            );
        $no++;
        }
        return $data;
    }

    public function get_assign($id) {
        $query = $this->db->query("SELECT * FROM m_pegawai a
            JOIN m_pegawai_lokasi b ON a.pegawai_id=b.pegawai_id
            WHERE a.is_del='n' AND b.lokasi_id='$id'")->result_array();
        return $query;
    }

    public function get_karyawan($id) {
        $data = array();
        $query = $this->db->query("SELECT a.*, b.pegawai_id as pxid FROM m_pegawai a
            LEFT JOIN m_pegawai_lokasi b ON a.pegawai_id=b.pegawai_id
            WHERE a.is_del='n'")->result_array();

        foreach ($query as $row) {
            if ($row['pegawai_id']!=$row['pxid']) {
                $data[] = array(
                    'pegawai_id'    => $row['pegawai_id'],
                    'nama_pegawai'  =>$row['nama_pegawai']
                );
            }
        }
        return $data;
    }

    public function add_proses() {
        $data = [
            'nama_lokasi'  	        => $this->input->post('nama'),
            'alamat_lokasi'         => $this->input->post('alamat'),
            'garis_lintang'  	    => $this->input->post('gl'),
            'garis_bujur'           => $this->input->post('gb'),
            'jangkauan_radius'      => $this->input->post('radius'),
            'created_at'  		    => date('Y-m-d H:i:s')
        ];
        $res = $this->db->insert('m_lokasi', $data);
        return $res;
    }

    public function edit_proses($id) {
        $this->db->set([
            'nama_lokasi'           => $this->input->post('nama'),
            'alamat_lokasi'         => $this->input->post('alamat'),
            'garis_lintang'         => $this->input->post('gl'),
            'garis_bujur'           => $this->input->post('gb'),
            'jangkauan_radius'      => $this->input->post('radius')
        ]);
        $this->db->where('lokasi_id', $id);
        $res = $this->db->update('m_lokasi');
        return $res;
    }

    public function assign_proses($id) {
        $res = false;
        $idp = $this->input->post('idp');
        if ($idp!='') {
            foreach ($idp as $key => $value) {
                if($value!=""){
                    $query = $this->db->query("SELECT * FROM m_pegawai_lokasi WHERE lokasi_id='$id' AND pegawai_id='$value'");
                    $cekrow = $query->num_rows();
                    if($cekrow==0){
                        $data = [
                            'lokasi_id'     => $id,
                            'pegawai_id'    => $value
                        ];
                        $res = $this->db->insert('m_pegawai_lokasi', $data);
                    }
                }
            }
        }

        return $res;
    }

}