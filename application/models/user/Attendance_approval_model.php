<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Attendance_approval_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

	public function get_data($tgl,$statt) {
        $data = array();
        $query = "SELECT * FROM tx_absensi a
            JOIN m_pegawai b ON a.pegawai_id=b.pegawai_id
            WHERE b.is_status='y' AND b.is_del='n' AND a.tanggal_absen='$tgl' AND a.is_pending!='n'";
        $result = $this->db->query($query)->result_array();

        foreach ($result as $row) {

            if ($row['is_pending']=='t') {
                $stt = 'Ditolak';
                $clr = 'danger';
            }else if ($row['is_pending']=='y') {
                $stt = 'Waiting';
                $clr = 'secondary';
            }else{
                $stt = 'Disetujui';
                $clr = 'success';
            }

            $data[] = array(
                'absen_id'            => $row['absen_id'],
                'pegawai_id'          => $row['pegawai_id'],
                'nama_pegawai'        => $row['nama_pegawai'],
                'tipe'                => 'in',
                'tipe_label'          => 'Absen Masuk',
                'jam'                 => $row['jam_masuk'],
                'foto_absen'          => $row['foto_absen_masuk'],
                'latitude'            => $row['latitude_masuk'],
                'longitude'           => $row['longitude_masuk'],
                'action_acc'          => $row['is_pending'],
                'status'              => $stt,
                'statusclr'           => $clr
            );
        }

        $query2 = "SELECT * FROM tx_absensi a
            JOIN m_pegawai b ON a.pegawai_id=b.pegawai_id
            WHERE b.is_status='y' AND b.is_del='n' AND a.acc_keluar!='y' AND a.tanggal_absen='$tgl'";
        $result2 = $this->db->query($query2)->result_array();

        foreach ($result2 as $row) {

            if ($row['acc_keluar']=='t') {
                $stt = 'Ditolak';
                $clr = 'danger';
            }else if ($row['acc_keluar']=='n') {
                $stt = 'Waiting';
                $clr = 'secondary';
            }else{
                $stt = 'Disetujui';
                $clr = 'success';
            }

            $data[] = array(
                'absen_id'            => $row['absen_id'],
                'pegawai_id'          => $row['pegawai_id'],
                'nama_pegawai'        => $row['nama_pegawai'],
                'tipe'                => 'out',
                'tipe_label'          => 'Absen Keluar',
                'jam'                 => $row['jam_keluar'],
                'foto_absen'          => $row['foto_absen_keluar'],
                'latitude'            => $row['latitude_keluar'],
                'longitude'           => $row['longitude_keluar'],
                'action_acc'          => $row['acc_keluar'],
                'status'              => $stt,
                'statusclr'           => $clr
            );
        }
        return $data;
    }

    public function update_att($st,$tipe,$id) {
        $res = false;
        if ($tipe=='in') {

            if ($st=='acc') {
                $pen = 'n';
                $pent = 'y';
            }else{
                $pen = 't';
                $pent = 't';
            }

            $this->db->set([
                'is_pending'        => $pen,
                'acc_masuk'         => $pent
            ]);
        }else{
            if ($st=='acc') {
                $pen = 'y';
            }else{
                $pen = 't';
            }

            $this->db->set([
                'acc_keluar'         => $pen
            ]);
        }
        $this->db->where('absen_id', $id);
        $res = $this->db->update('tx_absensi');
        return $res;
    }

}