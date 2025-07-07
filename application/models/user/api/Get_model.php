<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Get_model extends CI_Model {

    protected $compid;
    protected $today;
    public function __construct() {
        parent::__construct();
        $this->compid = '';
        $this->today = date('Y-m-d');
    }

    public function location_office($postjson) {

        if (!isset($postjson)) exit();

        $datakar = authKaryawan($postjson['id']);
        $sistem = pengaturanSistem();

        if ($datakar) {
            $result = $this->db->query("SELECT * FROM m_pegawai_lokasi a
                JOIN m_lokasi b ON a.lokasi_id=b.lokasi_id
                WHERE a.pegawai_id='$postjson[id]'")->row_array();

            if($result){
                return json_encode(array('status'=>true, 'loc'=>'y', 'result'=>$result, 'sistem'=>$sistem));
            }else {
                $result = $this->db->query("SELECT * FROM m_lokasi WHERE is_del='n'")->row_array();
                if($result){
                    return json_encode(array('status'=>true, 'loc'=>'y', 'result'=>$result, 'sistem'=>$sistem));
                }else{
                    return json_encode(array('status'=>false, 'loc'=>'n', 'msg'=>'Koordinat atau lokasi kantor belum ditetapkan, tapi kamu masih bisa melakukan absen.', 'sistem'=>$sistem));
                }
            }
        }else{
            return json_encode(array('status'=>false, 'loc'=>'x', 'msg'=>'ID unknown, silahkan logout dan login kembali.', 'sistem'=>$sistem));
        }
    }

    public function karyawan_data($postjson) {

        if (!isset($postjson)) exit();

        $res = $this->db->query("SELECT * FROM m_pegawai WHERE pegawai_id='$postjson[id]'")->row_array();

        $pola = array();
        $query = "SELECT 
        a.pegawai_id as pid, a.tanggal_mulai_kerja, 
        c.mulai_berlaku_tanggal, c.dari_hari_ke, c.pola_kerja_id 
        FROM m_pegawai a
        JOIN m_pegawai_pola c ON a.pegawai_id=c.pegawai_id AND c.is_selected='y'
        JOIN m_pola_kerja_det d ON c.pola_kerja_id=d.pola_kerja_id
        WHERE a.pegawai_id='$postjson[id]' AND a.is_del='n'";

        $result = $this->db->query($query)->result_array();

        $no = 0;
        $msgpola = 'y';
        foreach ($result as $row) {

            if (isset($row['mulai_berlaku_tanggal'])) {
                // jika pola kerja yang ditetapkan sudah berlaku
                if ($this->today>=$row['mulai_berlaku_tanggal']) {

                    $tgl = date("Y-m-d", strtotime($this->today." +".$no++." day"));

                    $tgl1 = strtotime($row['mulai_berlaku_tanggal']); 
                    $tgl2 = strtotime($tgl); 

                    $jarak = $tgl2 - $tgl1;
                    $hari = $jarak / 60 / 60 / 24;
                    $checkJumlahPola = checkJumlahPola($row['pola_kerja_id'],$hari+($row['dari_hari_ke']));

                    $q = $this->db->query("SELECT a.*, b.toleransi_terlambat FROM m_pola_kerja_det a 
                        JOIN m_pola_kerja b ON a.pola_kerja_id=b.pola_kerja_id 
                        WHERE a.pola_kerja_id='$row[pola_kerja_id]' AND a.is_day='$checkJumlahPola'")->row_array();

                    if (!isset($q['jam_masuk'])) { $q['jam_masuk'] = ''; }
                    if (!isset($q['jam_pulang'])) { $q['jam_pulang'] = ''; }
                    if (!isset($q['toleransi_terlambat'])) { $q['toleransi_terlambat'] = ''; }

                    if (isset($q['is_work']) && $q['is_work']=='n') { $st = 'l'; }else{ $st = 'ts'; }

                    if ($st=='l') {
                        $status= 'Libur';
                        $clr= 'bg-danger';
                    }else{
                        $status= 'Hari Kerja';
                        $clr= 'bg-white';
                    }

                    $pola[] = array(
                        'tanggal'             => indo($tgl),
                        'is_status'           => $st,
                        'status'              => $status,
                        'color'               => $clr,
                        'j_masuk'             => substr($q['jam_masuk'],0,5),
                        'j_pulang'            => substr($q['jam_pulang'],0,5),
                        'j_toleransi'         => $q['toleransi_terlambat']." Menit",
                    );
                }else{
                    $msgpola = 'Pola kerja akan tersedia mulai pada tanggal '.$row['mulai_berlaku_tanggal']. ', tapi kamu masih bisa melakukan absen jika itu diperlukan.';
                }
            }else{                    
                $msgpola = 'Belum setting pola kerja.';
            }
        }

        if (!$result) {
            $msgpola = 'Belum setting pola kerja.';
        }

        if ($res) {
            return json_encode(array('status'=>true, 'result'=>$res, 'pola'=>$pola, 'msgpola'=>$msgpola));
        }else{
            return json_encode(array('status'=>false, 'msg'=>'ID unknown, silahkan logout dan login kembali.'));
        }
    }

    public function company_data($postjson) {

        if (!isset($postjson)) exit();

        $res = $this->db->query("SELECT * FROM m_setting WHERE setting_id='1'")->row_array();
        return json_encode(array('status'=>true, 'result'=>$res));
    }

    public function attendance_data($postjson) {

        if (!isset($postjson)) exit();

        $data = array();

        if ($postjson['tlimit']=='limit') {
            $limit = " LIMIT $postjson[start],$postjson[limit] ";
        }else{
            $limit = " ";
        }

        $query = $this->db->query("SELECT * FROM tx_tanggal ORDER BY tanggal DESC $limit ");
        $resq = $query->result_array();
        $loadnya = $query->num_rows();

        $kary = $this->db->query("SELECT tanggal_mulai_kerja FROM m_pegawai WHERE pegawai_id='$postjson[id]'")->row_array();

        if ($kary) {
            foreach ($resq as $row) {

                $res = $this->db->query("SELECT a.*, c.mulai_berlaku_tanggal FROM tx_absensi a 
                LEFT JOIN m_pegawai_pola c ON a.pegawai_id=c.pegawai_id
                WHERE a.pegawai_id='$postjson[id]'
                AND a.tanggal_absen='$row[tanggal]'
                ")->row_array();

                $lembur = $this->db->query("SELECT a.lembur_id, 
                a.tanggal_lembur, a.masuk_lembur, a.keluar_lembur, 
                a.is_status, a.created_at, b.* FROM tx_lembur a JOIN tx_lembur_pegawai b ON a.lembur_id=b.lembur_id
                WHERE b.pegawai_id='$postjson[id]'
                AND a.tanggal_lembur='$row[tanggal]' ORDER BY a.lembur_id DESC
                ")->row_array();

                if(!isset($res['is_pending'])) $res['is_pending'] = '';
                if(!isset($res['acc_keluar'])) $res['acc_keluar'] = '';
                if(!isset($res['is_status'])) $res['is_status'] = '';
                if(!isset($res['j_masuk'])) $res['j_masuk'] = '';
                if(!isset($res['j_pulang'])) $res['j_pulang'] = '';
                if(!isset($res['j_toleransi'])) $res['j_toleransi'] = '';
                if(!isset($res['jam_masuk'])) $res['jam_masuk'] = '';
                if(!isset($res['jam_keluar'])) $res['jam_keluar'] = '';
                if(!isset($res['jam_istirahat'])) $res['jam_istirahat'] = '';
                if(!isset($res['jam_sistirahat'])) $res['jam_sistirahat'] = '';
                if(!isset($lembur['tanggal_lembur'])) $lembur['tanggal_lembur'] = '';
                if(!isset($lembur['masuk_lembur'])) $lembur['masuk_lembur'] = '';
                if(!isset($lembur['keluar_lembur'])) $lembur['keluar_lembur'] = '';
                if(!isset($lembur['absen_masuk'])) $lembur['absen_masuk'] = '';
                if(!isset($lembur['absen_keluar'])) $lembur['absen_keluar'] = '';
                if(!isset($lembur['is_status'])) $lembur['is_status'] = '';
                if(!isset($res['catatan_masuk'])) $res['catatan_masuk'] = '';
                if(!isset($res['catatan_keluar'])) $res['catatan_keluar'] = '';
                if(!isset($res['mulai_berlaku_tanggal'])) $res['mulai_berlaku_tanggal'] = '';

                if ($this->today>=$row['tanggal']) {

                    if ($row['tanggal']>=$kary['tanggal_mulai_kerja']) {

                        $res2 = $this->db->query("SELECT a.tipe_request FROM tx_request_izin a JOIN tx_request_izin_pegawai b ON a.request_izin_id=b.request_izin_id WHERE b.pegawai_id='$postjson[id]' AND a.tanggal_request<='$row[tanggal]' AND a.tanggal_request_end>='$row[tanggal]' AND a.is_status=1")->row_array();

                        if(isset($res2['tipe_request'])) $res['is_status'] = $res2['tipe_request'];
                        $txt_color = 'color-dark';
                        if($res['is_status']=='' || $res['is_status']=='ts'){
                          $txt_swork = 'Tidak Ada Status';
                        }else if($res['is_status']=='th'){
                          $txt_swork = 'Tidak Hadir / Alfa';
                        }else if($res['is_status']=='l'){
                          $txt_swork = 'Libur';
                          $txt_color = 'color-danger';
                        }else if($res['is_status']=='hhk'){
                          $txt_swork = 'Masuk Kerja';
                        }else if($res['is_status']=='hbhk'){
                          $txt_swork = 'Masuk Dihari Kerja';
                        }else if($res['is_status']=='s'){
                          $txt_swork = 'Sakit';
                        }else if($res['is_status']=='i'){
                          $txt_swork = 'Izin';
                        }else if($res['is_status']=='c'){
                          $txt_swork = 'Cuti';
                        }else if($res['is_status']=='cb'){
                          $txt_swork = 'Cuti Bersama';
                        }else if($res['is_status']=='ct'){
                          $txt_swork = 'Cuti Tahunan';
                        }else if($res['is_status']=='csh'){
                          $txt_swork = 'Cuti Setengah Hari';
                        }else if($res['is_status']=='tl'){
                          $txt_swork = 'Tugas Luar';
                        }else{
                          $txt_swork = '';
                        }

                        if ($res['is_pending']=='y') {
                            $txt_swork = 'Menunggu Persetujuan';
                        }

                        $data[] = array(
                            'tanggal'  => indo($row['tanggal']),
                            'tanggal_mulai_kerja'  => $kary['tanggal_mulai_kerja'],
                            'mulai_berlaku_tanggal'  => $res['mulai_berlaku_tanggal'],
                            'is_status'  => $res['is_status'],
                            'txt_swork'  => $txt_swork,
                            'txt_color'  => $txt_color,
                            'j_masuk'  => $res['j_masuk'],
                            'j_pulang'  => $res['j_pulang'],
                            'j_toleransi'  => $res['j_toleransi'],
                            'jam_masuk'  => $res['jam_masuk'],
                            'jam_keluar'  => $res['jam_keluar'],
                            'jam_istirahat'  => $res['jam_istirahat'],
                            'jam_sistirahat'  => $res['jam_sistirahat'],
                            'tanggal_lembur'  => $lembur['tanggal_lembur'],
                            'masuk_lembur'  => $lembur['masuk_lembur'],
                            'keluar_lembur'  => $lembur['keluar_lembur'],
                            'absen_masuk'  => $lembur['absen_masuk'],
                            'absen_keluar'  => $lembur['absen_keluar'],
                            'status_lembur'  => $lembur['is_status'],
                            'catatan_masuk'  => $res['catatan_masuk'],
                            'catatan_keluar'  => $res['catatan_keluar'],
                            'acc_keluar'  => $res['acc_keluar'],
                            'is_pending'  => $res['is_pending'],
                        );
                    }
                }

            }
        }

        return json_encode(array('status'=>true, 'result'=>$data, 'getloadnya'=>$loadnya));

    }

    public function req_data($postjson) {

        if (!isset($postjson)) exit();
        $data = array();

        $result = $this->db->query("SELECT distinct(b.pegawai_id)as pegawai_id, a.* FROM tx_request_izin a
            JOIN tx_request_izin_pegawai b ON a.request_izin_id=b.request_izin_id
            WHERE b.pegawai_id='$postjson[id]' ORDER BY a.request_izin_id DESC")->result_array();

        foreach ($result as $row) {

            if ($row['tanggal_request']==$row['tanggal_request_end']) {
                $tgl = indo($row['tanggal_request']);
            }else{
                $tgl = indo($row['tanggal_request'])." s/d ".indo($row['tanggal_request_end']);
            }

            if ($row['is_status']==0) {
                $status = 'Menunggu Persetujuan';
            }else if ($row['is_status']==1) {
                $status = 'Disetujui';
            }else if ($row['is_status']==2) {
                $status = 'Ditolak';
            }else{
                $status = 'Unknnown';
            }

            if ($row['tipe_request']=='s') {
                $txtstatus = 'Izin Sakit';
            }else if ($row['tipe_request']=='i') {
                $txtstatus = 'Izin Lainnya';
            }else if ($row['tipe_request']=='c') {
                $txtstatus = 'Izin Cuti';
            }else if ($row['tipe_request']=='cb') {
                $txtstatus = 'Cuti Bersama';
            }else if ($row['tipe_request']=='ct') {
                $txtstatus = 'Cuti Tahunan';
            }else if ($row['tipe_request']=='csh') {
                $txtstatus = 'Cuti s/ Hari';
            }else if ($row['tipe_request']=='tl') {
                $txtstatus = 'Tugas Luar';
            }else{
                $txtstatus = 'Unknnown';
            }

            if($row['catatan_awal']==''){
                $ccttn = 'Tidak ada catatan';
            }else{
                $ccttn = $row['catatan_awal'];
            }

            $data[] = array(
                'request_izin_id'   => $row['request_izin_id'],
                'tanggal'           => $tgl,
                'catatan'           => $ccttn,
                'is_status'         => $row['is_status'],
                'status'            => $status,
                'txtstatus'         => $txtstatus,
                'jam_masuk'         => $row['r_jam_masuk'],
                'jam_keluar'        => $row['r_jam_keluar'],
                'lamahari'          => $row['jumlah_cuti']." Hari"
            );
        }

        return json_encode(array('status'=>true, 'result'=>$data));
    }

    public function req_data_id($postjson) {

        if (!isset($postjson)) exit();

        $result = $this->db->query("SELECT * FROM tx_request_izin WHERE request_izin_id='$postjson[idizin]'")->row_array();

        return json_encode(array('status'=>true, 'result'=>$result));
    }

    public function lembur_data($postjson) {

        if (!isset($postjson)) exit();
        $data = array();

        $result = $this->db->query("SELECT a.lembur_id, 
        a.tanggal_lembur, a.masuk_lembur, a.keluar_lembur, 
        a.is_status, a.created_at, b.* FROM tx_lembur a JOIN tx_lembur_pegawai b ON a.lembur_id=b.lembur_id 
        WHERE b.pegawai_id='$postjson[id]' ORDER BY a.lembur_id DESC")->result_array();

        foreach ($result as $row) {

            if ($row['is_status']==0) {
                $status = 'Menunggu Persetujuan';
            }else if ($row['is_status']==1) {
                $status = 'Disetujui';
            }else if ($row['is_status']==2) {
                $status = 'Ditolak';
            }else{
                $status = 'Unknnown';
            }

            $data[] = array(
                'lembur_id'         => $row['lembur_id'],
                'tanggal'           => indo($row['tanggal_lembur']),
                'status'            => $status,
                'is_status'         => $row['is_status'],
                'catatan_awal'      => $row['catatan_lembur'],
                'catatan_akhir'     => $row['catatan_hasil_lembur'],
                'jam_masuk'         => $row['masuk_lembur'],
                'jam_keluar'        => $row['keluar_lembur'],
                'absen_masuk'       => $row['absen_masuk'],
                'absen_keluar'      => $row['absen_keluar']
            );
        }

        return json_encode(array('status'=>true, 'result'=>$data));
    }

    public function terkini($postjson) {

        if (!isset($postjson)) exit();
        $data = array();

        $result = $this->db->query("SELECT * FROM tx_lokasi_terkini
            WHERE pegawai_id='$postjson[id]' ORDER BY lt_id DESC")->result_array();

        foreach ($result as $row) {

            $data[] = array(
                'id'                => $row['lt_id'],
                'tanggal'           => indo($row['tanggal']),
                'jam'               => $row['jam'],
                'catatan'           => $row['catatan'],
                'foto'              => $row['foto'],
                'latitude'          => $row['latitude_lt'],
                'longitude'         => $row['longitude_lt'],
                'alamat'            => $row['alamat_lengkap']
            );
        }

        return json_encode(array('status'=>true, 'result'=>$data));
    }

}