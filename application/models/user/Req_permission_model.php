<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Req_permission_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

	public function get_data($tglawal,$tglakhr) {
        $data = array();
        $query = $this->db->query("SELECT * FROM tx_request_izin WHERE date(tanggal_request) BETWEEN '$tglawal' AND '$tglakhr' ORDER BY request_izin_id DESC")->result_array();

        foreach ($query as $row) {

            $query2 = $this->db->query("SELECT * FROM tx_request_izin_pegawai a LEFT JOIN m_pegawai b ON a.pegawai_id=b.pegawai_id WHERE a.request_izin_id='$row[request_izin_id]' AND b.is_del='n' ")->result_array();

            if ($row['tipe_request']=='s') {
              $bgs = 'Sakit';
            }else if ($row['tipe_request']=='i') {
              $bgs = 'Izin';
            }else if ($row['tipe_request']=='c') {
              $bgs = 'Cuti';
            }else if ($row['tipe_request']=='cb') {
              $bgs = 'Cuti Bersama';
            }else if ($row['tipe_request']=='ct') {
              $bgs = 'Cuti Tahunan';
            }else if ($row['tipe_request']=='csh') {
              $bgs = 'Cuti Setengah Hari';
            }else if ($row['tipe_request']=='tl') {
              $bgs = 'Tugas Luar';
            }else if ($row['tipe_request']=='lm') {
              $bgs = 'Lembur';
            }else{
              $bgs = '';
            }

            if ($row['is_status']==0) {
                $status = 'Pending';
            }else if ($row['is_status']==1) {
              $status = 'Approved';
            }else if ($row['is_status']==2) {
              $status = 'Reject';
            }else{
              $status = 'Unknown';
            }

            $totgl = '';
            if ($row['tanggal_request_end']!='') {
                $totgl = ' s/d '.indo($row['tanggal_request_end']);
            }

            $data[] = array(
                'id'                  => $row['request_izin_id'],
                'tanggal'             => indo($row['tanggal_request']).$totgl,
                'kategori'            => $bgs,
                'j_masuk'             => $row['r_jam_masuk'],
                'j_keluar'            => $row['r_jam_keluar'],
                'pegawai'             => $query2,
                'status'              => $status,
                'tipe'                => $row['tipe_request']
            );
        }

        return $data;

    }

    public function get_karyawan($id) {
        $query = $this->db->query("SELECT a.*, b.pegawai_id as pid FROM m_pegawai a LEFT JOIN tx_request_izin_pegawai b ON a.pegawai_id=b.pegawai_id AND b.request_izin_id='$id' WHERE a.is_status='y' AND a.is_del='n' ORDER BY id_pegawai ASC")->result_array();
        return $query;
    }

    public function add_proses($cekimgpdf,$upload) {

        if ($cekimgpdf=='') { 
            $filex = ''; 
        }else{ 
            $filex = $upload['path'].$upload['file']['file_name']; 
        }

        if ($this->input->post('tgl2')=='') {
            $tglakh = $this->input->post('tgl1');
        }else{
            $tglakh = $this->input->post('tgl2');
        }

        $tgl1 = strtotime($this->input->post('tgl1')); 
        $tgl2 = strtotime($tglakh); 
        $jarak = $tgl2 - $tgl1;
        $jumlahhari = $jarak / 60 / 60 / 24;

        $data = [
            'tipe_request'          => $this->input->post('kat'),
            'tanggal_request'       => $this->input->post('tgl1'),
            'tanggal_request_end'   => $tglakh,
            'r_jam_masuk'           => $this->input->post('jmasuk'),
            'r_jam_keluar'          => $this->input->post('jkeluar'),
            'catatan_awal'          => $this->input->post('catatanl'),
            'jumlah_cuti'           => $jumlahhari+1,
            'file_dokumen'          => $filex,
            'is_status'             => 1, // 0 pending, 1 acc, 2 tolak
            'created_at'            => date('Y-m-d H:i:s')
        ];
        $res = $this->db->insert('tx_request_izin', $data);
        $idnya = $this->db->insert_id();

        $idp = $this->input->post('idp');
        if ($idp!='') {
            foreach ($idp as $key => $value) {
                if($value!=""){
                    $data = [
                        'request_izin_id'   => $idnya,
                        'pegawai_id'        => $value,
                        'tanggal_request'       => $this->input->post('tgl1'),
                        'tanggal_request_end'   => $tglakh,
                        'r_absen_masuk'         => $this->input->post('amasuk'),
                        'r_absen_keluar'        => $this->input->post('akeluar'),
                        'catatan_awal'          => $this->input->post('catatanl'),
                        'jumlah_cuti'           => $jumlahhari+1
                    ];
                    $this->db->insert('tx_request_izin_pegawai', $data);
                }
            }
        }
        return $res;
    }

    public function edit_proses($id,$cekimgpdf,$upload,$oldimg) {

        if ($cekimgpdf=='') { 
            $filex = $oldimg; 
        }else{ 
            $filex = $upload['path'].$upload['file']['file_name']; 
        }

        if ($this->input->post('tgl2')=='') {
            $tglakh = $this->input->post('tgl1');
        }else{
            $tglakh = $this->input->post('tgl2');
        }

        $tgl1 = strtotime($this->input->post('tgl1')); 
        $tgl2 = strtotime($tglakh); 
        $jarak = $tgl2 - $tgl1;
        $jumlahhari = $jarak / 60 / 60 / 24;

        $cek = $this->db->query("SELECT * FROM tx_request_izin WHERE request_izin_id='$id'")->row_array();

        $this->db->set([
            'tipe_request'          => $this->input->post('kat'),
            'tanggal_request'       => $this->input->post('tgl1'),
            'tanggal_request_end'   => $tglakh,
            'r_jam_masuk'           => $this->input->post('jmasuk'),
            'r_jam_keluar'          => $this->input->post('jkeluar'),
            'catatan_awal'          => $this->input->post('catatanl'),
            'is_status'             => $this->input->post('status'),
            'jumlah_cuti'           => $jumlahhari+1,
            'file_dokumen'          => $filex
        ]);
        $this->db->where('request_izin_id', $id);
        $res = $this->db->update('tx_request_izin');


        $buff = $this->db->query("SELECT * FROM tx_request_izin_pegawai WHERE request_izin_id='$id'")->result_array();
        $idp = $this->input->post('idp');

        $existing_ids = $this->db->select('pegawai_id')
            ->from('tx_request_izin_pegawai')
            ->where('request_izin_id', $id)
            ->get()->result_array();
        $existing_ids = array_column($existing_ids, 'pegawai_id');

        if (!empty($idp)) {
            // hapus pegawai yang tidak ada dalam array idp[]
            $ids_to_delete = array_diff($existing_ids, $idp);
            if (!empty($ids_to_delete)) {
                $this->db->where('request_izin_id', $id);
                $this->db->where_in('pegawai_id', $ids_to_delete);
                $this->db->delete('tx_request_izin_pegawai');
            }

            // Lakukan proses insert atau update seperti biasa
            foreach ($idp as $value) {
                if ($value != '') {
                    $checkdata = $query = $this->db->query("SELECT * FROM tx_request_izin_pegawai WHERE pegawai_id='$value' AND request_izin_id='$id' ORDER BY request_izin_peg_id DESC")->row_array();

                    if(!isset($checkdata['catatan_awal'])) $checkdata['catatan_awal'] = '';
                    if(!isset($checkdata['r_absen_masuk'])) $checkdata['r_absen_masuk'] = '';
                    if(!isset($checkdata['r_absen_keluar'])) $checkdata['r_absen_keluar'] = '';

                    $this->db->delete('tx_request_izin_pegawai', ['pegawai_id' => $value, 'request_izin_id' => $id]);

                    if($cek['catatan_awal']==$this->input->post('catatanl')){
                        $cttnnya = $checkdata['catatan_awal'];
                    }else{
                        $cttnnya = $this->input->post('catatanl');
                    }

                    if($this->input->post('amasuk')==''){
                        $amasuk = $checkdata['r_absen_masuk'];
                    }else{
                        $amasuk = $this->input->post('amasuk');
                    }

                    if($this->input->post('akeluar')==''){
                        $akeluar = $checkdata['r_absen_keluar'];
                    }else{
                        $akeluar = $this->input->post('akeluar');
                    }

                    $data = [
                        'request_izin_id'   => $id,
                        'pegawai_id'        => $value,
                        'tanggal_request'       => $this->input->post('tgl1'),
                        'tanggal_request_end'   => $tglakh,
                        'r_absen_masuk'         => $amasuk,
                        'r_absen_keluar'        => $akeluar,
                        'catatan_awal'          => $cttnnya,
                        'jumlah_cuti'           => $jumlahhari+1
                    ];
                    $this->db->insert('tx_request_izin_pegawai', $data);
                }
            }
        } else {
            $this->db->delete('tx_request_izin_pegawai', ['request_izin_id' => $id]);
        }

        foreach ($buff as $row) {
            $resbuff = $this->db->query("SELECT * FROM tx_request_izin_pegawai WHERE request_izin_id='$id' AND pegawai_id='$row[pegawai_id]'")->num_rows();

            if ($resbuff==0) {
                $this->db->delete('tx_absensi', ['pegawai_id' => $row['pegawai_id'], 'is_request' => $id]);
            }
        }
        
        return $res;
    }

    public function get_data_tl($id,$idp) {
        $query = $this->db->query("SELECT * FROM tx_absensi a JOIN tx_request_izin_pegawai b ON a.pegawai_id=b.pegawai_id 
        WHERE date(a.tanggal_absen) BETWEEN b.tanggal_request AND b.tanggal_request_end AND b.request_izin_id='$id' AND b.pegawai_id='$idp'")->result_array();
        return $query;

    }

}