<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Attendance_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

	public function get_data($tgl,$statt,$isRun) {
        $data = array();
        $today = Date('N');
        $serverDate = new DateTime();

        if ($tgl==date('Y-m-d')) {
            $check_tgl = $this->db->query("SELECT * FROM tx_tanggal WHERE tanggal='$tgl'")->num_rows();
            if ($check_tgl==0) {
                $datatgl = [
                    'tanggal'       => $tgl
                ];
                if($isRun){
                    $this->db->insert('tx_tanggal', $datatgl);
                }
            }            
        }

        $companyId = $this->session->userdata('company_id');

        $query = "SELECT a.company_id, a.pegawai_id as pid, a.division_id as division_id, a.nama_pegawai, a.tanggal_mulai_kerja, b.*, c.mulai_berlaku_tanggal, c.dari_hari_ke, c.is_day, c.pola_kerja_id FROM m_pegawai a
            LEFT JOIN tx_absensi b ON a.pegawai_id=b.pegawai_id AND b.tanggal_absen='$tgl' AND b.is_pending='$statt'
            LEFT JOIN m_pegawai_pola c ON a.pegawai_id=c.pegawai_id AND c.is_selected='y'
            WHERE a.is_del='n' and a.company_id =$companyId";

        $result = $this->db->query($query)->result_array();

        foreach ($result as $row) {
            $cH = $this->db->query("select * from company_holidays where company_id='$row[company_id]' and tanggal='$tgl'")->num_rows();
            $globalHolidays = $this->db->query("select * from global_holidays where tanggal='$tgl'")->num_rows();

            $q2 = $this->db->query("SELECT * FROM tx_absensi WHERE tanggal_absen='$tgl' AND pegawai_id='$row[pid]'")->num_rows();



            


            if (isset($row['mulai_berlaku_tanggal'])) {
                if ($tgl>=$row['mulai_berlaku_tanggal']) {

                    $tgl1 = strtotime($row['mulai_berlaku_tanggal']); 
                    $tgl2 = strtotime($tgl); 

                    $jarak = $tgl2 - $tgl1;
                    $hari = $jarak / 60 / 60 / 24;
                    // $checkJumlahPola = checkJumlahPola($row['pola_kerja_id'],$hari+($row['dari_hari_ke']-1));
                    $checkJumlahPola = checkJumlahPola($row['pola_kerja_id'],$hari+($row['dari_hari_ke']));
                    $q = $this->db->query("SELECT a.*, b.toleransi_terlambat FROM m_pola_kerja_det a 
                        JOIN m_pola_kerja b ON a.pola_kerja_id=b.pola_kerja_id 
                        WHERE a.pola_kerja_id='$row[pola_kerja_id]' AND a.is_day='$checkJumlahPola'")->row_array();

                    if (!isset($q['jam_masuk'])) { $q['jam_masuk'] = ''; }
                    if (!isset($q['jam_pulang'])) { $q['jam_pulang'] = ''; }
                    if (!isset($q['toleransi_terlambat'])) { $q['toleransi_terlambat'] = ''; }

                    if($cH > 0 || $globalHolidays > 0){
                        $st = 'cb';
                    }
                    else{
                        if(isset($q['is_work']) && $q['is_work']=='n'){ 
                            $st = 'l'; 
                        }
                        else{ 
                            $st = 'ts'; 
                        }

                    }
                    
                    
                    if($q2==0){
                        $datains = [
                            'tanggal_absen'       => $tgl,
                            'pegawai_id'          => $row['pid'],
                            'j_masuk'             => $q['jam_masuk'],
                            'j_pulang'            => $q['jam_pulang'],
                            'j_toleransi'         => $q['toleransi_terlambat'],
                            'is_status'           => $st
                        ];

                        if($isRun){
                            $this->db->insert('tx_absensi', $datains);
                        }
                    }
                }
            }
        }

        $result = $this->db->query($query)->result_array();

        foreach ($result as $row) {
            $division = $this->db->query("select * from divisions where id = ?",[$row['division_id']])->row_array();

            $lastDefaultStatus = $this->db->query("SELECT * FROM tx_absensi where pegawai_id = ? ORDER BY tanggal_absen DESC LIMIT 1",[$row['pid']])->row_array();

            $workSystem = explode("-",$division['work_system']);

            if($workSystem[0] == "s"){
                $shift = $this->db->query("select * from employee_shift es join shift_detail sd on es.shift_detail_id = sd.shift_detail_id where employee_id = ?",[$row['pid']])->row_array();
                $base = new DateTime($lastDefaultStatus['tanggal_absen'].' 00:00:00');
                
                if (!$shift || empty($shift['clock_in'])) {
                    $row['tolerance'] = clone $base;
                    $row['limit'] = clone $base;
                }
                else{
                    $dateTime1 = new DateTime($lastDefaultStatus['tanggal_absen']. ' ' . $shift['clock_in']);
                    $row['tolerance'] = (clone $dateTime1)->modify("+{$shift['tardiness_tolerance']} minutes");
                    $row['limit'] = (clone $row['tolerance'])->modify("+{$division['restriction']} minutes");
                }
            }
            else{
                $pattern = $this->db->query("select * from m_pola_kerja mpk join m_pola_kerja_det mpkd on mpk.pola_kerja_id = mpkd.pola_kerja_id where mpk.pola_kerja_id = ? and is_day = ?",[$workSystem[1],$today])->row_array();
                $dateTime1 = new DateTime($lastDefaultStatus['tanggal_absen']. ' ' . $pattern['jam_masuk']);
                $row['tolerance'] = (clone $dateTime1)->modify("+{$pattern['toleransi_terlambat']} minutes");
                $row['limit'] = (clone $row['tolerance'])->modify("+{$division['restriction']} minutes");
            }

            if ($tgl>=$row['tanggal_mulai_kerja']) {
                

            $q3 = $this->db->query("SELECT * FROM tx_request_izin a JOIN tx_request_izin_pegawai b ON a.request_izin_id=b.request_izin_id WHERE b.pegawai_id='$row[pid]' AND a.is_status=1 AND (a.tanggal_request='$tgl' OR (date(a.tanggal_request) BETWEEN a.tanggal_request AND '$tgl' AND date(a.tanggal_request_end) BETWEEN '$tgl' AND a.tanggal_request_end AND a.tanggal_request_end!=''))")->row_array();

            if(isset($q3['tipe_request'])) $row['is_status'] = $q3['tipe_request'];
            if(isset($q3['request_izin_id'])) $row['is_request'] = $q3['request_izin_id'];

            $data[] = array(
                'absen_id'              => $row['absen_id'],
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
                'acc_keluar'            => $row['acc_keluar'],
                'foto_absen_masuk'      => $row['foto_absen_masuk'],
                'point_latitude'        => $row['point_latitude'],
                'point_longitude'       => $row['point_longitude'],
                'foto_absen_keluar'     => $row['foto_absen_keluar'],
                'latitude_keluar'       => $row['latitude_keluar'],
                'longitude_keluar'      => $row['longitude_keluar'],
                's_istirahat_photo'     => $row['s_istirahat_photo'],
                's_istirahat_latitude'  => $row['s_istirahat_latitude'],
                's_istirahat_longitude' => $row['s_istirahat_longitude'],
                'isLate'                => $row['isLate'],
                'tolerance'             => $row['tolerance'],
                'limit'                 => $row['limit']
            );
            }
        }
        return $data;
    }

    public function getByFilter($tgl,$statt,$isRun,$status){
        $data = array();
        $today = Date('N');
        $serverDate = new DateTime();

        if ($tgl==date('Y-m-d')) {
            $check_tgl = $this->db->query("SELECT * FROM tx_tanggal WHERE tanggal='$tgl'")->num_rows();
            if ($check_tgl==0) {
                $datatgl = [
                    'tanggal'       => $tgl
                ];
                if($isRun){
                    $this->db->insert('tx_tanggal', $datatgl);
                }
            }            
        }

        $query = "SELECT a.company_id, a.pegawai_id as pid, a.division_id as division_id, a.nama_pegawai, a.tanggal_mulai_kerja, b.*, c.mulai_berlaku_tanggal, c.dari_hari_ke, c.is_day, c.pola_kerja_id FROM m_pegawai a
            LEFT JOIN tx_absensi b ON a.pegawai_id=b.pegawai_id AND b.tanggal_absen='$tgl' AND b.is_pending='$statt'
            LEFT JOIN m_pegawai_pola c ON a.pegawai_id=c.pegawai_id AND c.is_selected='y'
            WHERE a.is_del='n' and b.is_status='$status'";

        $result = $this->db->query($query)->result_array();

        foreach ($result as $row) {
            $cH = $this->db->query("select * from company_holidays where company_id='$row[company_id]' and tanggal='$tgl'")->num_rows();
            $globalHolidays = $this->db->query("select * from global_holidays where tanggal='$tgl'")->num_rows();

            $q2 = $this->db->query("SELECT * FROM tx_absensi WHERE tanggal_absen='$tgl' AND pegawai_id='$row[pid]'")->num_rows();



            


            if (isset($row['mulai_berlaku_tanggal'])) {
                if ($tgl>=$row['mulai_berlaku_tanggal']) {

                    $tgl1 = strtotime($row['mulai_berlaku_tanggal']); 
                    $tgl2 = strtotime($tgl); 

                    $jarak = $tgl2 - $tgl1;
                    $hari = $jarak / 60 / 60 / 24;
                    // $checkJumlahPola = checkJumlahPola($row['pola_kerja_id'],$hari+($row['dari_hari_ke']-1));
                    $checkJumlahPola = checkJumlahPola($row['pola_kerja_id'],$hari+($row['dari_hari_ke']));
                    $q = $this->db->query("SELECT a.*, b.toleransi_terlambat FROM m_pola_kerja_det a 
                        JOIN m_pola_kerja b ON a.pola_kerja_id=b.pola_kerja_id 
                        WHERE a.pola_kerja_id='$row[pola_kerja_id]' AND a.is_day='$checkJumlahPola'")->row_array();

                    if (!isset($q['jam_masuk'])) { $q['jam_masuk'] = ''; }
                    if (!isset($q['jam_pulang'])) { $q['jam_pulang'] = ''; }
                    if (!isset($q['toleransi_terlambat'])) { $q['toleransi_terlambat'] = ''; }

                    if($cH > 0 || $globalHolidays > 0){
                        $st = 'cb';
                    }
                    else{
                        if(isset($q['is_work']) && $q['is_work']=='n'){ 
                            $st = 'l'; 
                        }
                        else{ 
                            $st = 'ts'; 
                        }

                    }
                    
                    
                    if($q2==0){
                        $datains = [
                            'tanggal_absen'       => $tgl,
                            'pegawai_id'          => $row['pid'],
                            'j_masuk'             => $q['jam_masuk'],
                            'j_pulang'            => $q['jam_pulang'],
                            'j_toleransi'         => $q['toleransi_terlambat'],
                            'is_status'           => $st
                        ];

                        if($isRun){
                            $this->db->insert('tx_absensi', $datains);
                        }
                    }
                }
            }
        }

        $result = $this->db->query($query)->result_array();

        foreach ($result as $row) {
            $division = $this->db->query("select * from divisions where id = ?",[$row['division_id']])->row_array();

            $lastDefaultStatus = $this->db->query("SELECT * FROM tx_absensi where pegawai_id = ? ORDER BY tanggal_absen DESC LIMIT 1",[$row['pid']])->row_array();

            $workSystem = explode("-",$division['work_system']);

            if($workSystem[0] == "s"){
                $shift = $this->db->query("select * from employee_shift es join shift_detail sd on es.shift_detail_id = sd.shift_detail_id where employee_id = ?",[$row['pid']])->row_array();
                $dateTime1 = new DateTime($lastDefaultStatus['tanggal_absen']. ' ' . $shift['clock_in']);
                $row['tolerance'] = (clone $dateTime1)->modify("+{$shift['tardiness_tolerance']} minutes");
                $row['limit'] = (clone $row['tolerance'])->modify("+{$division['restriction']} minutes");

            }
            else{
                $pattern = $this->db->query("select * from m_pola_kerja mpk join m_pola_kerja_det mpkd on mpk.pola_kerja_id = mpkd.pola_kerja_id where mpk.pola_kerja_id = ? and is_day = ?",[$workSystem[1],$today])->row_array();
                $dateTime1 = new DateTime($lastDefaultStatus['tanggal_absen']. ' ' . $pattern['jam_masuk']);
                $row['tolerance'] = (clone $dateTime1)->modify("+{$pattern['toleransi_terlambat']} minutes");
                $row['limit'] = (clone $row['tolerance'])->modify("+{$division['restriction']} minutes");


            }

            if ($tgl>=$row['tanggal_mulai_kerja']) {
                

            $q3 = $this->db->query("SELECT * FROM tx_request_izin a JOIN tx_request_izin_pegawai b ON a.request_izin_id=b.request_izin_id WHERE b.pegawai_id='$row[pid]' AND a.is_status=1 AND (a.tanggal_request='$tgl' OR (date(a.tanggal_request) BETWEEN a.tanggal_request AND '$tgl' AND date(a.tanggal_request_end) BETWEEN '$tgl' AND a.tanggal_request_end AND a.tanggal_request_end!=''))")->row_array();

            if(isset($q3['tipe_request'])) $row['is_status'] = $q3['tipe_request'];
            if(isset($q3['request_izin_id'])) $row['is_request'] = $q3['request_izin_id'];

            $data[] = array(
                'absen_id'              => $row['absen_id'],
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
                'acc_keluar'            => $row['acc_keluar'],
                'foto_absen_masuk'      => $row['foto_absen_masuk'],
                'point_latitude'        => $row['point_latitude'],
                'point_longitude'       => $row['point_longitude'],
                'foto_absen_keluar'     => $row['foto_absen_keluar'],
                'latitude_keluar'       => $row['latitude_keluar'],
                'longitude_keluar'      => $row['longitude_keluar'],
                's_istirahat_photo'     => $row['s_istirahat_photo'],
                's_istirahat_latitude'  => $row['s_istirahat_latitude'],
                's_istirahat_longitude' => $row['s_istirahat_longitude'],
                'tolerance'             => $row['tolerance'],
                'limit'                 => $row['limit']
            );
            }
        }
        return $data;
    }

    public function get_alpha($tgl,$statt,$isRun) {
        $data = array();
        $today = Date('N');
        $serverDate = new DateTime();

        if ($tgl==date('Y-m-d')) {
            $check_tgl = $this->db->query("SELECT * FROM tx_tanggal WHERE tanggal='$tgl'")->num_rows();
            if ($check_tgl==0) {
                $datatgl = [
                    'tanggal'       => $tgl
                ];
                if($isRun){
                    $this->db->insert('tx_tanggal', $datatgl);
                }
            }            
        }

        $query = "SELECT a.company_id, a.pegawai_id as pid, a.division_id as division_id, a.nama_pegawai, a.tanggal_mulai_kerja, b.*, c.mulai_berlaku_tanggal, c.dari_hari_ke, c.is_day, c.pola_kerja_id FROM m_pegawai a
            LEFT JOIN tx_absensi b ON a.pegawai_id=b.pegawai_id AND b.tanggal_absen='$tgl' AND b.is_pending='$statt'
            LEFT JOIN m_pegawai_pola c ON a.pegawai_id=c.pegawai_id AND c.is_selected='y'
            WHERE a.is_del='n' and b.is_status='alpha-1' or b.is_status='alpha-2'";

        $result = $this->db->query($query)->result_array();

        foreach ($result as $row) {
            $cH = $this->db->query("select * from company_holidays where company_id='$row[company_id]' and tanggal='$tgl'")->num_rows();
            $globalHolidays = $this->db->query("select * from global_holidays where tanggal='$tgl'")->num_rows();

            $q2 = $this->db->query("SELECT * FROM tx_absensi WHERE tanggal_absen='$tgl' AND pegawai_id='$row[pid]'")->num_rows();



            


            if (isset($row['mulai_berlaku_tanggal'])) {
                if ($tgl>=$row['mulai_berlaku_tanggal']) {

                    $tgl1 = strtotime($row['mulai_berlaku_tanggal']); 
                    $tgl2 = strtotime($tgl); 

                    $jarak = $tgl2 - $tgl1;
                    $hari = $jarak / 60 / 60 / 24;
                    // $checkJumlahPola = checkJumlahPola($row['pola_kerja_id'],$hari+($row['dari_hari_ke']-1));
                    $checkJumlahPola = checkJumlahPola($row['pola_kerja_id'],$hari+($row['dari_hari_ke']));
                    $q = $this->db->query("SELECT a.*, b.toleransi_terlambat FROM m_pola_kerja_det a 
                        JOIN m_pola_kerja b ON a.pola_kerja_id=b.pola_kerja_id 
                        WHERE a.pola_kerja_id='$row[pola_kerja_id]' AND a.is_day='$checkJumlahPola'")->row_array();

                    if (!isset($q['jam_masuk'])) { $q['jam_masuk'] = ''; }
                    if (!isset($q['jam_pulang'])) { $q['jam_pulang'] = ''; }
                    if (!isset($q['toleransi_terlambat'])) { $q['toleransi_terlambat'] = ''; }

                    if($cH > 0 || $globalHolidays > 0){
                        $st = 'cb';
                    }
                    else{
                        if(isset($q['is_work']) && $q['is_work']=='n'){ 
                            $st = 'l'; 
                        }
                        else{ 
                            $st = 'ts'; 
                        }

                    }
                    
                    
                    if($q2==0){
                        $datains = [
                            'tanggal_absen'       => $tgl,
                            'pegawai_id'          => $row['pid'],
                            'j_masuk'             => $q['jam_masuk'],
                            'j_pulang'            => $q['jam_pulang'],
                            'j_toleransi'         => $q['toleransi_terlambat'],
                            'is_status'           => $st
                        ];

                        if($isRun){
                            $this->db->insert('tx_absensi', $datains);
                        }
                    }
                }
            }
        }

        $result = $this->db->query($query)->result_array();

        foreach ($result as $row) {
            $division = $this->db->query("select * from divisions where id = ?",[$row['division_id']])->row_array();

            $lastDefaultStatus = $this->db->query("SELECT * FROM tx_absensi where pegawai_id = ? ORDER BY tanggal_absen DESC LIMIT 1",[$row['pid']])->row_array();

            $workSystem = explode("-",$division['work_system']);

            if($workSystem[0] == "s"){
                $shift = $this->db->query("select * from employee_shift es join shift_detail sd on es.shift_detail_id = sd.shift_detail_id where employee_id = ?",[$row['pid']])->row_array();
                $dateTime1 = new DateTime($lastDefaultStatus['tanggal_absen']. ' ' . $shift['clock_in']);
                $row['tolerance'] = (clone $dateTime1)->modify("+{$shift['tardiness_tolerance']} minutes");
                $row['limit'] = (clone $row['tolerance'])->modify("+{$division['restriction']} minutes");

            }
            else{
                $pattern = $this->db->query("select * from m_pola_kerja mpk join m_pola_kerja_det mpkd on mpk.pola_kerja_id = mpkd.pola_kerja_id where mpk.pola_kerja_id = ? and is_day = ?",[$workSystem[1],$today])->row_array();
                $dateTime1 = new DateTime($lastDefaultStatus['tanggal_absen']. ' ' . $pattern['jam_masuk']);
                $row['tolerance'] = (clone $dateTime1)->modify("+{$pattern['toleransi_terlambat']} minutes");
                $row['limit'] = (clone $row['tolerance'])->modify("+{$division['restriction']} minutes");


            }

            if ($tgl>=$row['tanggal_mulai_kerja']) {
                

            $q3 = $this->db->query("SELECT * FROM tx_request_izin a JOIN tx_request_izin_pegawai b ON a.request_izin_id=b.request_izin_id WHERE b.pegawai_id='$row[pid]' AND a.is_status=1 AND (a.tanggal_request='$tgl' OR (date(a.tanggal_request) BETWEEN a.tanggal_request AND '$tgl' AND date(a.tanggal_request_end) BETWEEN '$tgl' AND a.tanggal_request_end AND a.tanggal_request_end!=''))")->row_array();

            if(isset($q3['tipe_request'])) $row['is_status'] = $q3['tipe_request'];
            if(isset($q3['request_izin_id'])) $row['is_request'] = $q3['request_izin_id'];

            $data[] = array(
                'absen_id'              => $row['absen_id'],
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
                'acc_keluar'            => $row['acc_keluar'],
                'foto_absen_masuk'      => $row['foto_absen_masuk'],
                'point_latitude'        => $row['point_latitude'],
                'point_longitude'       => $row['point_longitude'],
                'foto_absen_keluar'     => $row['foto_absen_keluar'],
                'latitude_keluar'       => $row['latitude_keluar'],
                'longitude_keluar'      => $row['longitude_keluar'],
                's_istirahat_photo'     => $row['s_istirahat_photo'],
                's_istirahat_latitude'  => $row['s_istirahat_latitude'],
                's_istirahat_longitude' => $row['s_istirahat_longitude'],
                'tolerance'             => $row['tolerance'],
                'limit'                 => $row['limit']
            );
            }
        }
        return $data;
    }

    public function absensi_proses($tgl,$idp,$value,$tipe = 'is_status') {
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

        $q = $this->db->query("SELECT * FROM tx_absensi WHERE tanggal_absen='$tgl' AND pegawai_id='$idp'");
        $result = $q->row_array();

        if ($q->num_rows()==0) {
            $data = [
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
            $this->db->where('absen_id', $result['absen_id']);
            $res = $this->db->update('tx_absensi');
        }


        if($res==true){
            if ($tgl==date('Y-m-d')) {

                $check_tgl = $this->db->query("SELECT * FROM tx_tanggal WHERE tanggal='$tgl'")->num_rows();
                if ($check_tgl==0) {
                    $data = [
                        'tanggal'       => $tgl
                    ];
                    $res = $this->db->insert('tx_tanggal', $data);
                }            
            }
        }
        return $res;
    }

    

    public function req_cancel($idp,$tgl) {
        $res = false;
        $q3 = $this->db->query("SELECT * FROM tx_request_izin a JOIN tx_request_izin_pegawai b ON a.request_izin_id=b.request_izin_id WHERE b.pegawai_id='$idp' AND a.is_status=1 AND (a.tanggal_request='$tgl' OR (date(a.tanggal_request) BETWEEN a.tanggal_request AND '$tgl' AND date(a.tanggal_request_end) BETWEEN '$tgl' AND a.tanggal_request_end AND a.tanggal_request_end!=''))")->row_array();

        if ($q3) {
            $res = $this->db->delete('tx_request_izin_pegawai', ['request_izin_peg_id' => $q3['request_izin_peg_id'], 'pegawai_id' => $idp]);
            $res = $this->db->delete('tx_absensi', ['is_request' => $q3['request_izin_id'], 'pegawai_id' => $idp]);
        }

        return $res;
    }

}