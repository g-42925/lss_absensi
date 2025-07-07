<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Attendance_model extends CI_Model {

    protected $compid;
    public function __construct() {
        parent::__construct();
        $this->compid = $this->session->userdata('perusahaan_id');
    }

	public function get_data($tgl,$statt) {
        $data = array();

        $query = "SELECT a.pegawai_id as pid, a.nama_pegawai, a.tanggal_mulai_kerja, b.*, c.mulai_berlaku_tanggal, c.dari_hari_ke, c.is_day, c.pola_kerja_id FROM m_pegawai a
            LEFT JOIN tx_absensi b ON a.pegawai_id=b.pegawai_id AND b.tanggal_absen='$tgl' AND b.is_pending='$statt'
            LEFT JOIN m_pegawai_pola c ON a.pegawai_id=c.pegawai_id AND c.is_selected='y'
            WHERE a.perusahaan_id='$this->compid' AND a.is_del='n'";

        $result = $this->db->query($query)->result_array();

        foreach ($result as $row) {

            $q2 = $this->db->query("SELECT * FROM tx_absensi WHERE perusahaan_id='$this->compid' AND tanggal_absen='$tgl' AND pegawai_id='$row[pid]'")->num_rows();
                    
            $q3 = $this->db->query("SELECT * FROM tx_request_izin a JOIN tx_request_izin_pegawai b ON a.request_izin_id=b.request_izin_id WHERE a.perusahaan_id='$this->compid' AND b.pegawai_id='$row[pid]' AND a.is_status=1 AND (a.tanggal_request='$tgl' OR (date(a.tanggal_request) BETWEEN a.tanggal_request AND '$tgl' AND date(a.tanggal_request_end) BETWEEN '$tgl' AND a.tanggal_request_end AND a.tanggal_request_end!=''))")->row_array();

            if (isset($row['mulai_berlaku_tanggal'])) {
                if ($tgl>=$row['mulai_berlaku_tanggal']) {

                    $tgl1 = strtotime($row['mulai_berlaku_tanggal']); 
                    $tgl2 = strtotime($tgl); 

                    $jarak = $tgl2 - $tgl1;
                    $hari = $jarak / 60 / 60 / 24;
                    $checkJumlahPola = checkJumlahPola($row['pola_kerja_id'],$hari+($row['dari_hari_ke']-1));
                    $q = $this->db->query("SELECT a.*, b.toleransi_terlambat FROM m_pola_kerja_det a 
                        JOIN m_pola_kerja b ON a.pola_kerja_id=b.pola_kerja_id 
                        WHERE a.perusahaan_id='$this->compid' AND a.pola_kerja_id='$row[pola_kerja_id]' AND a.is_day='$checkJumlahPola'")->row_array();

                    if (!isset($q['jam_masuk'])) { $q['jam_masuk'] = ''; }
                    if (!isset($q['jam_pulang'])) { $q['jam_pulang'] = ''; }
                    if (!isset($q['toleransi_terlambat'])) { $q['toleransi_terlambat'] = ''; }

                    if (isset($q['is_work']) && $q['is_work']=='n') { $st = 'l'; }else{ $st = 'ts'; }
                    
                    if ($q3) {
                        if ($q2==0) {
                            $datains = [
                                'perusahaan_id'       => $this->compid,
                                'tanggal_absen'       => $tgl,
                                'pegawai_id'          => $row['pid'],
                                'j_masuk'             => $q['jam_masuk'],
                                'j_pulang'            => $q['jam_pulang'],
                                'j_toleransi'         => $q['toleransi_terlambat'],
                                'is_status'           => $q3['tipe_request'],
                                'is_request'          => $q3['request_izin_id']
                            ];
                            $this->db->insert('tx_absensi', $datains);
                        }else{
                            $this->db->set([
                                'is_status'           => $q3['tipe_request'],
                                'is_request'          => $q3['request_izin_id']
                            ]);
                            $this->db->where('perusahaan_id', $this->compid);
                            $this->db->where('absen_id', $row['absen_id']);
                            $this->db->update('tx_absensi');
                        }
                    }else{
                        if ($q2==0) {
                            $datains = [
                                'perusahaan_id'       => $this->compid,
                                'tanggal_absen'       => $tgl,
                                'pegawai_id'          => $row['pid'],
                                'j_masuk'             => $q['jam_masuk'],
                                'j_pulang'            => $q['jam_pulang'],
                                'j_toleransi'         => $q['toleransi_terlambat'],
                                'is_status'           => $st
                            ];
                            $this->db->insert('tx_absensi', $datains);
                        }
                    }
                }else{
                    if ($q3) {
                        if ($q2==0) {
                            $datains = [
                                'perusahaan_id'       => $this->compid,
                                'tanggal_absen'       => $tgl,
                                'pegawai_id'          => $row['pid'],
                                'is_status'           => $q3['tipe_request'],
                                'is_request'          => $q3['request_izin_id']
                            ];
                            $this->db->insert('tx_absensi', $datains);
                        }else{
                            $this->db->set([
                                'is_status'           => $q3['tipe_request'],
                                'is_request'          => $q3['request_izin_id']
                            ]);
                            $this->db->where('perusahaan_id', $this->compid);
                            $this->db->where('absen_id', $row['absen_id']);
                            $this->db->update('tx_absensi');
                        }
                    }
                }
            }else{                    
                if ($q3) {
                    if ($q2==0) {
                        $datains = [
                            'perusahaan_id'       => $this->compid,
                            'tanggal_absen'       => $tgl,
                            'pegawai_id'          => $row['pid'],
                            'is_status'           => $q3['tipe_request'],
                            'is_request'          => $q3['request_izin_id']
                        ];
                        $this->db->insert('tx_absensi', $datains);
                    }else{
                        $this->db->set([
                            'is_status'           => $q3['tipe_request'],
                            'is_request'          => $q3['request_izin_id']
                        ]);
                        $this->db->where('perusahaan_id', $this->compid);
                        $this->db->where('absen_id', $row['absen_id']);
                        $this->db->update('tx_absensi');
                    }
                }
            }
        }

        $result = $this->db->query($query)->result_array();

        foreach ($result as $row) {
            if ($tgl>=$row['tanggal_mulai_kerja']) {
            $data[] = array(
                'absen_id'              => $row['absen_id'],
                'perusahaan_id'         => $row['perusahaan_id'],
                'pegawai_id'            => $row['pegawai_id'],
                'tanggal_absen'         => $row['tanggal_absen'],
                'is_status'             => $row['is_status'],
                'jam_masuk'             => $row['jam_masuk'],
                'jam_istirahat'         => $row['jam_istirahat'],
                'jam_sistirahat'        => $row['jam_sistirahat'],
                'jam_keluar'            => $row['jam_keluar'],
                'catatan_masuk'         => $row['catatan_masuk'],
                'catatan_keluar'        => $row['catatan_keluar'],
                'pid'                   => $row['pid'],
                'nama_pegawai'          => $row['nama_pegawai'],
                'mulai_berlaku_tanggal' => $row['mulai_berlaku_tanggal'],
                'dari_hari_ke'          => $row['dari_hari_ke'],
                'is_day'                => $row['is_day'],
                'is_request'            => $row['is_request'],
                'acc_keluar'            => $row['acc_keluar']
            );
            }
        }
        return $data;
    }

    public function absensi_proses($tgl,$tipe = 'is_status',$idp,$value) {
        $res = false;
        if ($tipe=='status') { 
            $tipe = 'is_status'; 
        }else if ($tipe=='jmasuk') {
            $tipe = 'jam_masuk'; 
        }else if ($tipe=='jisti') {
            $tipe = 'jam_istirahat'; 
        }else if ($tipe=='jsisti') {
            $tipe = 'jam_sistirahat'; 
        }else if ($tipe=='jkeluar') {
            $tipe = 'jam_keluar'; 
        }

        $q = $this->db->query("SELECT * FROM tx_absensi WHERE perusahaan_id='$this->compid' AND tanggal_absen='$tgl' AND pegawai_id='$idp'");
        $result = $q->row_array();

        if ($q->num_rows()==0) {
            $data = [
                'perusahaan_id'       => $this->session->userdata('perusahaan_id'),
                'tanggal_absen'       => $tgl,
                'pegawai_id'          => $idp,
                $tipe                 => $value
            ];
            $res = $this->db->insert('tx_absensi', $data);
        }else{

            if ($value=='ts' || $value=='th' || $value=='s' || $value=='i' || $value=='c' || $value=='cb' || $value=='ct' || $value=='l') {
                $this->db->set([
                    $tipe                 => $value,
                    'jam_masuk'           => '',
                    'jam_istirahat'       => '',
                    'jam_sistirahat'      => '',
                    'jam_keluar'          => ''
                ]);
            }else{
                $this->db->set([
                    $tipe                 => $value
                ]);
            }
            $this->db->where('perusahaan_id', $this->compid);
            $this->db->where('absen_id', $result['absen_id']);
            $res = $this->db->update('tx_absensi');
        }
        return $res;
    }

    public function req_cancel($idp,$tgl) {
        $res = false;
        $q3 = $this->db->query("SELECT * FROM tx_request_izin a JOIN tx_request_izin_pegawai b ON a.request_izin_id=b.request_izin_id WHERE a.perusahaan_id='$this->compid' AND b.pegawai_id='$idp' AND a.is_status=1 AND (a.tanggal_request='$tgl' OR (date(a.tanggal_request) BETWEEN a.tanggal_request AND '$tgl' AND date(a.tanggal_request_end) BETWEEN '$tgl' AND a.tanggal_request_end AND a.tanggal_request_end!=''))")->row_array();

        if ($q3) {
            $res = $this->db->delete('tx_request_izin_pegawai', ['request_izin_peg_id' => $q3['request_izin_peg_id'], 'pegawai_id' => $idp, 'perusahaan_id' => $this->compid]);
            $res = $this->db->delete('tx_absensi', ['is_request' => $q3['request_izin_id'], 'pegawai_id' => $idp, 'perusahaan_id' => $this->compid]);
        }

        return $res;
    }

}