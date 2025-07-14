<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Attendance_record_model extends CI_Model {

    protected $firstdate;
    protected $today;
    public function __construct() {
        parent::__construct();
        $this->firstdate = date('2023-01-01');
        $this->today = date('Y-m-d');
    }

	public function get_data($tglawal,$tglakhr) {
        $data = array();
        $query = $this->db->query("SELECT * FROM m_pegawai WHERE is_del='n'")->result_array();

        foreach ($query as $row) {

            // belum lengkap (contoh belum absen pulang dihari sebelumnya) 
            $bl = $this->db->query("SELECT count(jam_keluar) as total FROM tx_absensi WHERE jam_masuk!='' AND jam_keluar='' AND pegawai_id='$row[pegawai_id]' AND tanggal_absen!='$this->today' AND date(tanggal_absen) BETWEEN '$tglawal' AND '$tglakhr' AND is_pending='n'")->row_array();
            $bl2 = $this->db->query("SELECT count(absen_id) as total FROM tx_absensi WHERE jam_keluar!='' AND pegawai_id='$row[pegawai_id]' AND tanggal_absen!='$this->today' AND date(tanggal_absen) BETWEEN '$tglawal' AND '$tglakhr' AND is_pending='n' AND acc_keluar='n'")->row_array();
            // hadir di hari kerja
            $hhk = $this->db->query("SELECT count(is_status) as total FROM tx_absensi WHERE is_status='hhk' AND pegawai_id='$row[pegawai_id]' AND date(tanggal_absen) BETWEEN '$tglawal' AND '$tglakhr' AND is_pending='n'")->row_array();
            // hadir bukan di hari kerja
            $hbhk = $this->db->query("SELECT count(is_status) as total FROM tx_absensi WHERE is_status='hbhk' AND pegawai_id='$row[pegawai_id]' AND date(tanggal_absen) BETWEEN '$tglawal' AND '$tglakhr' AND is_pending='n'")->row_array();

            // tugas luar
            $tl = checkStatusAbsen('tl',$row['pegawai_id'],$tglawal,$tglakhr)-1;
            // sakit
            $sakit = checkStatusAbsen('s',$row['pegawai_id'],$tglawal,$tglakhr)-1;
            // izin
            $i = checkStatusAbsen('i',$row['pegawai_id'],$tglawal,$tglakhr) - 1;
            // cuti
            $c = checkStatusAbsen('c',$row['pegawai_id'],$tglawal,$tglakhr);
            // cuti setengah hari
            $csh = checkStatusAbsen('csh',$row['pegawai_id'],$tglawal,$tglakhr)-1;
            // cuti bersama
            $cb = checkStatusAbsen('cb',$row['pegawai_id'],$tglawal,$tglakhr);
            // cuti tahunan
            $ct = checkStatusAbsen('ct',$row['pegawai_id'],$tglawal,$tglakhr)-1;

            if($sakit > 0){
              $cutiSakit = $sakit;
            }
            else{
                $cutiSakit = 0;
            }

            if($tl > 0){
              $tugasLuar = $tl;
            }
            else{
               $tugasLuar = 0;
            }

            if($csh > 0){
              $cutiSetengahHari = $csh;
            }
            else{
                $cutiSetengahHari;
            }

            if($ct > 0){
                $cutiTahunan = $ct;
            }
            else{
                $cutiTahunan = 0;
            }


            $tidakHadir = checkStatusAbsen('ts',$row['pegawai_id'],$tglawal,$tglakhr);
            
            // alfa (th) dan blm ada status (ts)
            $th = $this->db->query("SELECT count(is_status) as total FROM tx_absensi WHERE is_status='th' AND pegawai_id='$row[pegawai_id]' AND date(tanggal_absen) BETWEEN '$tglawal' AND '$tglakhr' AND is_pending='n'")->row_array();

            $qtgl = $this->db->query("SELECT * FROM tx_tanggal WHERE date(tanggal) BETWEEN '$tglawal' AND '$tglakhr'")->result_array();
            $totalts = 0;
            $nots = 0;
            
            foreach ($qtgl as $rowx) {
                if ($rowx['tanggal']>=$row['tanggal_mulai_kerja']) {
                    // (is_status!='ts' AND is_status!='th')
                    $ts = $this->db->query("SELECT absen_id FROM tx_absensi WHERE is_status!='ts' AND tanggal_absen='$rowx[tanggal]' AND pegawai_id='$row[pegawai_id]' AND is_pending='n'")->row_array();
                    if ($ts) { $totalts++; }
                $nots++; }
            }

            if (!isset($th['total'])) { $th['total'] = 0; }
            if (!isset($bl['total'])) { $bl['total'] = 0; }
            if (!isset($bl2['total'])) { $bl2['total'] = 0; }
            if (!isset($hhk['total'])) { $hhk['total'] = 0; }
            if (!isset($hbhk['total'])) { $hbhk['total'] = 0; }


            $data[] = array(
                'pegawai_id'              => $row['pegawai_id'],
                'nama_pegawai'            => $row['nama_pegawai'],
                'hhk'                     => $hhk['total']+$hbhk['total'],
                'bl'                      => $bl['total']+$bl2['total'],
                'tl'                      => $tugasLuar,
                's'                       => $cutiSakit,
                'c'                       => $c+$i,
                'csh'                     => $cutiSetengahHari,
                'cb'                      => $cb,
                'ct'                      => $cutiTahunan,
                'th'                      => $tidakHadir
            );
        }
        return $data;
    }

    public function get_detail($id,$tglawal,$tglakhr) {

        $data = array();
        $query = $this->db->query("SELECT * FROM tx_tanggal WHERE date(tanggal) BETWEEN '$tglawal' AND '$tglakhr'")->result_array();

        $kary = $this->db->query("SELECT tanggal_mulai_kerja FROM m_pegawai WHERE pegawai_id='$id'")->row_array();

        if ($kary) {
            foreach ($query as $row) {

                // ini v1 jadi walau udh set lembur kalo blm absen/msk kerja utamanya lembur tak tampil
                // $res = $this->db->query("SELECT a.*, b.tanggal_lembur, b.masuk_lembur, b.keluar_lembur, b.absen_masuk, b.absen_keluar, c.mulai_berlaku_tanggal FROM tx_absensi a 
                // LEFT JOIN tx_lembur b ON a.tanggal_absen=b.tanggal_lembur 
                // AND a.pegawai_id=b.pegawai_id 
                // AND b.is_status=1
                // LEFT JOIN m_pegawai_pola c ON a.pegawai_id=c.pegawai_id
                // WHERE a.pegawai_id='$id'
                // AND a.tanggal_absen='$row[tanggal]'
                // AND a.is_pending='n'
                // ")->row_array();

                $res = $this->db->query("SELECT a.*, c.mulai_berlaku_tanggal FROM tx_absensi a 
                LEFT JOIN m_pegawai_pola c ON a.pegawai_id=c.pegawai_id
                WHERE a.pegawai_id='$id'
                AND a.tanggal_absen='$row[tanggal]'
                AND a.is_pending='n'
                ")->row_array();

                $lembur = $this->db->query("SELECT a.lembur_id, 
                a.tanggal_lembur, a.masuk_lembur, a.keluar_lembur, 
                a.is_status, a.created_at, b.* FROM tx_lembur a JOIN tx_lembur_pegawai b ON a.lembur_id=b.lembur_id
                WHERE b.pegawai_id='$id'
                AND a.tanggal_lembur='$row[tanggal]'
                AND a.is_status=1 ORDER BY a.lembur_id DESC
                ")->row_array();

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
                if(!isset($res['catatan_masuk'])) $res['catatan_masuk'] = '';
                if(!isset($res['catatan_keluar'])) $res['catatan_keluar'] = '';
                if(!isset($res['mulai_berlaku_tanggal'])) $res['mulai_berlaku_tanggal'] = '';

                if ($row['tanggal']>=$kary['tanggal_mulai_kerja']) {

                    $res2 = $this->db->query("SELECT a.tipe_request FROM tx_request_izin a JOIN tx_request_izin_pegawai b ON a.request_izin_id=b.request_izin_id WHERE b.pegawai_id='$id' AND a.tanggal_request<='$row[tanggal]' AND a.tanggal_request_end>='$row[tanggal]' AND a.is_status=1")->row_array();

                    $datashift = $this->db->query("SELECT * FROM tx_shift WHERE pegawai_id='$id' AND tanggal_shift='$row[tanggal]'")->row_array();

                    $datashiftm = '';
                    $datashiftk = '';
                    if(isset($res2['tipe_request'])) $res['is_status'] = $res2['tipe_request'];
                    if(isset($datashift['shift_jam_mulai'])) $datashiftm = $datashift['shift_jam_mulai'];
                    if(isset($datashift['shift_jam_selesai'])) $datashiftk = $datashift['shift_jam_selesai'];

                    if ($datashift) {
                        $res['j_masuk'] = $datashiftm;
                        $res['j_pulang'] = $datashiftk;
                        $res['j_toleransi'] = 0;
                    }

                    $data[] = array(
                        'tanggal'  => $row['tanggal'],
                        'tanggal_mulai_kerja'  => $kary['tanggal_mulai_kerja'],
                        'mulai_berlaku_tanggal'  => $res['mulai_berlaku_tanggal'],
                        'is_status'  => $res['is_status'],
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
                        'catatan_masuk'  => $res['catatan_masuk'],
                        'catatan_keluar'  => $res['catatan_keluar'],
                        'acc_keluar'  => $res['acc_keluar'],
                        'shift_jam_mulai'  => $datashiftm,
                        'shift_jam_selesai'  => $datashiftk
                    );
                }

            }
        }

        return $data;
    }

}