<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Post_model extends CI_Model {

    protected $today;
    protected $todayy;
    protected $time;
    public function __construct() {
        parent::__construct();
        $this->today = date('Y-m-d');
        $this->todayy = date('Y/m/d');
        $this->time = date('H:i');
    }

    public function signin($postjson) {

        if (!isset($postjson)) exit();

        $c_data = $this->db->query("SELECT * FROM m_pegawai WHERE email_pegawai='$postjson[email]'");

        if ($c_data->num_rows() > 0) {
            $result = $c_data->row_array();
            if (password_verify($postjson['password'], $result['password_pegawai'])) {
                if($result['is_status']=='y'){
                    return json_encode(array('status'=>true, 'result'=>$result, 'msg'=>'Login berhasil.'));
                }else if($result['is_active']=='n'){
                    return json_encode(array('status'=>false, 'msg'=>'Akun ini tidak aktif, hubungi kontak support untuk informasi lebih lanjut.'));
                }else{
                    return json_encode(array('status'=>false, 'msg'=>'Login gagal, status akun null.'));
                }
            }else{
                return json_encode(array('status'=>false, 'msg'=>'Email atau password tidak sesuai.'));
            }
        }else{
            return json_encode(array('status'=>false, 'msg'=>'Login gagal, akun tidak terdaftar.'));
        }
    }

    public function post_attendance($postjson) {

        if (!isset($postjson)) exit();

        $data = array();
        $datakar = authKaryawan($postjson['id']);

        if ($datakar) {

            $tgl = date('Y-m-d');
            $check_tgl = $this->db->query("SELECT * FROM tx_tanggal WHERE tanggal='$tgl'")->num_rows();
            if ($check_tgl==0) {
                $datatgl = [
                    'tanggal'       => $tgl
                ];
                $this->db->insert('tx_tanggal', $datatgl);
            }            

            $query = "SELECT a.pegawai_id as pid, a.nama_pegawai, b.*, c.mulai_berlaku_tanggal, c.dari_hari_ke, c.pola_kerja_id FROM m_pegawai a
                LEFT JOIN tx_absensi b ON a.pegawai_id=b.pegawai_id AND b.tanggal_absen='$this->today'
                LEFT JOIN m_pegawai_pola c ON a.pegawai_id=c.pegawai_id AND c.is_selected='y'
                WHERE a.pegawai_id='$postjson[id]' AND a.is_del='n'";

            $result = $this->db->query($query)->result_array();

            foreach ($result as $row) {

                if (isset($row['mulai_berlaku_tanggal'])) {
                    if ($this->today >= $row['mulai_berlaku_tanggal']) {

                        $tgl1 = strtotime($row['mulai_berlaku_tanggal']); 
                        $tgl2 = strtotime($this->today); 

                        $jarak = $tgl2 - $tgl1;
                        $hari = $jarak / 60 / 60 / 24;
                        $checkJumlahPola = checkJumlahPola($row['pola_kerja_id'],$hari+($row['dari_hari_ke']));
                        $q = $this->db->query("SELECT a.*, b.toleransi_terlambat FROM m_pola_kerja_det a 
                            JOIN m_pola_kerja b ON a.pola_kerja_id=b.pola_kerja_id 
                            WHERE a.pola_kerja_id='$row[pola_kerja_id]' AND a.is_day='$checkJumlahPola'")->row_array();

                        if (isset($q['is_work']) && $q['is_work']=='n') { $st = 'l'; }else{ $st = 'ts'; }

                        $q2 = $this->db->query("SELECT * FROM tx_absensi WHERE tanggal_absen='$this->today' AND pegawai_id='$row[pid]'")->num_rows();
                        if ($q2==0) {
                            $datains = [
                                'tanggal_absen'       => $this->today,
                                'pegawai_id'          => $row['pid'],
                                'j_masuk'             => $q['jam_masuk'],
                                'j_pulang'            => $q['jam_pulang'],
                                'j_toleransi'         => $q['toleransi_terlambat'],
                                'is_status'           => $st
                            ];
                            $this->db->insert('tx_absensi', $datains);
                        }
                    }
                }
            }

            $result = $this->db->query($query)->row_array();

            $lembur = $this->db->query("SELECT a.lembur_id, 
            a.tanggal_lembur, a.masuk_lembur, a.keluar_lembur, 
            a.is_status, a.created_at, b.* FROM tx_lembur a JOIN tx_lembur_pegawai b ON a.lembur_id=b.lembur_id
            WHERE b.pegawai_id='$postjson[id]'
            AND a.tanggal_lembur='$this->today' ORDER BY a.lembur_id DESC
            ")->row_array();

            $statusizinrequesttxt = '';
            $txt_lblabsen = '';
            $unlockabsen = 'y';
            $txtizx = '';
            $statusizinrequest = $this->db->query("SELECT a.tipe_request, a.is_status FROM tx_request_izin a JOIN tx_request_izin_pegawai b ON a.request_izin_id=b.request_izin_id WHERE b.pegawai_id='$postjson[id]' AND a.tanggal_request<='$this->today' AND a.tanggal_request_end>='$this->today'")->row_array();

            if($statusizinrequest){
                if($statusizinrequest['is_status']==1){
                    $unlockabsen = 'n';
                    if($statusizinrequest['tipe_request']=='csh'){
                        $statusizinrequesttxt = 'Absen Dikunci Status = Cuti Setengah Hari ';
                    }else if($statusizinrequest['tipe_request']=='i'){
                        $statusizinrequesttxt = 'Absen Dikunci Status = Izin Cuti '.$txtizx;
                    }else if($statusizinrequest['tipe_request']=='s'){
                        $statusizinrequesttxt = 'Absen Dikunci Status = Izin Sakit '.$txtizx;
                    }else if($statusizinrequest['tipe_request']=='ct'){
                        $statusizinrequesttxt = 'Absen Dikunci Status = Cuti Tahunan '.$txtizx;
                    }else if($statusizinrequest['tipe_request']=='tl'){
                        $unlockabsen = 'y';
                    }
                }else if($statusizinrequest['is_status']==0 || $statusizinrequest['is_status']==2){
                    if ($statusizinrequest['is_status']==0) {
                        $txtizx = 'masih menunggu persetujuan.';
                    }else{
                        $txtizx = 'telah ditolak.';
                    }
                    if($statusizinrequest['tipe_request']=='csh'){
                        $statusizinrequesttxt = 'Request Cuti Setengah Hari '.$txtizx;
                    }else if($statusizinrequest['tipe_request']=='i'){
                        $statusizinrequesttxt = 'Request Izin Cuti '.$txtizx;
                    }else if($statusizinrequest['tipe_request']=='s'){
                        $statusizinrequesttxt = 'Request Izin Sakit '.$txtizx;
                    }else if($statusizinrequest['tipe_request']=='ct'){
                        $statusizinrequesttxt = 'Request Cuti Tahunan '.$txtizx;
                    }else if($statusizinrequest['tipe_request']=='tl'){
                        $statusizinrequesttxt = 'Request Tugas Luar '.$txtizx;
                    }
                }
            }

            if ($result) {

                $res2 = $this->db->query("SELECT a.tipe_request FROM tx_request_izin a JOIN tx_request_izin_pegawai b ON a.request_izin_id=b.request_izin_id WHERE b.pegawai_id='$postjson[id]' AND a.tanggal_request<='$this->today' AND a.tanggal_request_end>='$this->today' AND a.is_status=1")->row_array();

                if(isset($res2['tipe_request'])) $result['is_status'] = $res2['tipe_request'];

                if($result['is_status']=='' || $result['is_status']=='ts'){
                  $txt_swork = 'Hari Kerja';
                }else if($result['is_status']=='th'){
                  $txt_swork = 'Tidak Hadir';
                }else if($result['is_status']=='l'){
                  $txt_swork = 'Libur Hari Ini';
                  $txt_lblabsen = 'Status kamu sedang '.$txt_swork.', tapi jika kamu masuk kerja silahkan melakukan absen.';
                }else if($result['is_status']=='hhk'){
                  $txt_swork = 'Masuk Kerja';
                }else if($result['is_status']=='hbhk'){
                  $txt_swork = 'Masuk Dihari Kerja';
                }else if($result['is_status']=='s'){
                  $txt_swork = 'Sakit';
                  $txt_lblabsen = 'Status kamu sedang '.$txt_swork.', tapi jika kamu masuk kerja silahkan melakukan absen.';
                }else if($result['is_status']=='i'){
                  $txt_swork = 'Izin';
                  $txt_lblabsen = 'Status kamu sedang '.$txt_swork.', tapi jika kamu masuk kerja silahkan melakukan absen.';
                }else if($result['is_status']=='c'){
                  $txt_swork = 'Cuti';
                  $txt_lblabsen = 'Status kamu sedang '.$txt_swork.', tapi jika kamu masuk kerja silahkan melakukan absen.';
                }else if($result['is_status']=='cb'){
                  $txt_swork = 'Cuti Bersama';
                  $txt_lblabsen = 'Status kamu sedang '.$txt_swork.', tapi jika kamu masuk kerja silahkan melakukan absen.';
                }else if($result['is_status']=='ct'){
                  $txt_swork = 'Cuti Tahunan';
                  $txt_lblabsen = 'Status kamu sedang '.$txt_swork.', tapi jika kamu masuk kerja silahkan melakukan absen.';
                }else if($result['is_status']=='csh'){
                  $txt_swork = 'Cuti Setengah Hari';
                  $txt_lblabsen = 'Status kamu sedang '.$txt_swork.', tapi jika kamu masuk kerja silahkan melakukan absen.';
                }else if($result['is_status']=='tl'){
                  $txt_swork = 'Tugas Luar';
                }else{
                  $txt_swork = '';
                }

                $datashift = $this->db->query("SELECT * FROM tx_shift WHERE pegawai_id='$postjson[id]' AND tanggal_shift='$this->today'")->row_array();

                if (!$datashift) {
                    $datashift = '';
                }

                $txt_pending = '';
                if($result['is_pending']=='y'){
                    $txt_pending = 'Status absen menunggu persetujuan, kamu melakukan absen diluar jangkauan atau radius lokasi yang ditetapkan, tapi tenang kamu masih bisa melakukan absen hingga selesai.';
                }

                $txt_accmasuk = '';
                if($result['acc_masuk']=='t'){
                    $txt_accmasuk = 'Absen masuk ditolak oleh HRD atau admin, silahkan hubungi pihak terkait untuk informasi lebih lanjut.';
                }

                $data = array(
                    'nama_pegawai'          => $result['nama_pegawai'],
                    'tanggal_ini_display'   => $this->todayy,
                    'tanggal_absen'         => $result['tanggal_absen'],
                    'is_status'             => $result['is_status'],
                    'txt_swork'             => $txt_swork,
                    'jam_masuk'             => $result['jam_masuk'],
                    'jam_istirahat'         => $result['jam_istirahat'],
                    'jam_sistirahat'        => $result['jam_sistirahat'],
                    'jam_keluar'            => $result['jam_keluar'],
                    'catatan_masuk'         => $result['catatan_masuk'],
                    'catatan_keluar'        => $result['catatan_keluar'],
                    'acc_masuk'             => $result['acc_masuk'],
                    'acc_keluar'            => $result['acc_keluar'],
                    'is_pending'            => $result['is_pending'],
                    'txt_pending'           => $txt_pending,
                    'lembur'                => $lembur,
                    'txt_accmasuk'          => $txt_accmasuk,
                    'statusizinrequesttxt'  => $statusizinrequesttxt,
                    'txt_lblabsen'          => $txt_lblabsen,
                    'unlockabsen'           => $unlockabsen,
                    'datashift'             => $datashift
                );
            }

            return json_encode(array('status'=>true, 'result'=>$data));

        }else{
            return json_encode(array('status'=>false, 'msg'=>'Proses dihentikan, id unknown, silahkan logout dan login kembali.'));
        }
    }

    public function attendance($postjson) {

        if (!isset($postjson)) exit();

        $msg = '';
        $res = false;
        if ($postjson['status']=='in') {
            $field = 'jam_masuk'; 
            $msg = 'Absen masuk berhasil pada jam '.$this->time;
        }else if ($postjson['status']=='out') {
            $field = 'jam_keluar'; 
            $msg = 'Absen keluar berhasil.';
        }else if ($postjson['status']=='iin') {
            $field = 'jam_istirahat'; 
            $msg = 'Absen istirahat berhasil, waktu istirahat telah dimulai.';
        }else if ($postjson['status']=='oout') {
            $field = 'jam_sistirahat'; 
            $msg = 'Absen selesai istirahat berhasil, waktu nya kembali bekerja.';
        }else{
            return json_encode(array('status'=>false, 'msg'=>'Proses dihentikan, status unknown.'));
            exit();
        }

        $datakar = authKaryawan($postjson['id']);

        if ($datakar) {

            $qlokasi = $this->db->query("SELECT * FROM m_pegawai_lokasi a
                JOIN m_lokasi b ON a.lokasi_id=b.lokasi_id
                WHERE a.pegawai_id='$postjson[id]'")->row_array();

            $lat_point = '';
            $lng_point = '';
            if($qlokasi){
                $lat_point = $qlokasi['garis_lintang'];
                $lng_point = $qlokasi['garis_bujur'];
            }else {
                $qlokasi = $this->db->query("SELECT * FROM m_lokasi")->row_array();
                if($qlokasi){
                    $lat_point = $qlokasi['garis_lintang'];
                    $lng_point = $qlokasi['garis_bujur'];
                }
            }

            $randname = date('Ymdhis').$postjson['id'];

            $q = $this->db->query("SELECT * FROM tx_absensi WHERE pegawai_id='$postjson[id]' AND tanggal_absen='$this->today'");
            $result = $q->row_array();

            if(!isset($result['is_status'])) $result['is_status'] = '';
            if(!isset($result['jam_masuk'])) $result['jam_masuk'] = '';
            if(!isset($result['jam_keluar'])) $result['jam_keluar'] = '';
            if(!isset($result['jam_istirahat'])) $result['jam_istirahat'] = '';
            if(!isset($result['jam_sistirahat'])) $result['jam_sistirahat'] = '';
            
            if ($this->time > $result['jam_masuk'] && $result['jam_masuk']=='' && $result['is_status']=='th') {
                return json_encode(array('status'=>false, 'msg'=>'Proses dihentikan, action telah ditutup.'));
                exit();
            }

            if ($postjson['status']=='in' && $result['jam_masuk']!='') {
                return json_encode(array('status'=>false, 'msg'=>'Proses dihentikan, absen masuk telah dilakukan pada jam '.$result['jam_masuk']));
            }

            if ($postjson['status']=='out' && $result['jam_keluar']!='') {
                return json_encode(array('status'=>false, 'msg'=>'Proses dihentikan, absen keluar telah dilakukan pada jam '.$result['jam_keluar']));
            }

            if ($postjson['status']=='iin' && $result['jam_istirahat']!='') {
                return json_encode(array('status'=>false, 'msg'=>'Proses dihentikan, waktu mulai istirahat telah dilakukan.'));
            }

            if ($postjson['status']=='oout' && $result['jam_sistirahat']!='') {
                return json_encode(array('status'=>false, 'msg'=>'Proses dihentikan, waktu selesai istirahat telah dilakukan.'));
            }

            if ($q->num_rows()==0) {
                if (($postjson['status']=='out' || $postjson['status']=='oout' || $postjson['status']=='iin') && $result['jam_masuk']=='') {
                    return json_encode(array('status'=>false, 'msg'=>'Proses dihentikan, absen masuk belum dilakukan.'));
                    exit();
                }else{

                    if ($postjson['poinradius']=='y') {
                        $ispending = 'n';
                    }else{
                        $ispending = 'y';
                    }

                    if ($postjson['image_path']!='') {
                        $ext = 'jpeg';
                        $imgstring = $postjson['image_path'];
                        $imgstring = trim(str_replace('data:image/'.$ext.';base64,', "", $imgstring));
                        $imgstring = str_replace(' ', '+', $imgstring);
                        $data = base64_decode($imgstring);
                        $directory = "assets/uploaded/users/";
                        $absenfoto  = $directory."absen_masuk_".$postjson['id']."_".$randname.".jpg";  
                        file_put_contents($absenfoto, $data);
                    }else{
                        $absenfoto = '';
                    }

                    $data = [
                        'tanggal_absen'       => $this->today,
                        'pegawai_id'          => $postjson['id'],
                        'is_status'           => 'hhk',
                        'foto_absen_masuk'    => $absenfoto,
                        'latitude_masuk'      => $postjson['latitude'],
                        'longitude_masuk'     => $postjson['longitude'],
                        'point_latitude'      => $lat_point,
                        'point_longitude'     => $lng_point,
                        'catatan_masuk'       => $postjson['catatan'],
                        'is_point_masuk'      => $postjson['poinradius'],
                        'is_pending'          => $ispending,
                        $field   => $this->time
                    ];
                    $res = $this->db->insert('tx_absensi', $data);
                }
            }else{
                if (($postjson['status']=='out' || $postjson['status']=='oout' || $postjson['status']=='iin') && $result['jam_masuk']=='') {
                    return json_encode(array('status'=>false, 'msg'=>'Proses dihentikan, absen masuk belum dilakukan.'));
                    exit();
                }else{

                    if ($result['is_status']=='ts' || $result['is_status']=='th') {
                        $status_ab = 'hhk';
                    }else if ($result['is_status']=='l') {
                        $status_ab = 'hbhk';
                    }else if ($result['is_status']=='s' || $result['is_status']=='i' || $result['is_status']=='c' || $result['is_status']=='cb' || $result['is_status']=='ct') {
                        $status_ab = 'hhk';
                    }else{
                        $status_ab = $result['is_status'];
                    }

                    if ($postjson['status']=='out' && $result['jam_istirahat']=='') {
                        $this->db->set([
                            'jam_istirahat'     => '12:30',
                            'jam_sistirahat'    => '14:00',
                            'is_status'         => $status_ab,
                            $field              => $this->time
                        ]);   
                    }else if ($postjson['status']=='out' && $result['jam_sistirahat']=='') {
                        $newTime = date('H:i', strtotime('+90 minutes', strtotime($result['jam_istirahat'])));
                        $this->db->set([
                            'jam_sistirahat'    => $newTime,
                            'is_status'         => $status_ab,
                            $field              => $this->time
                        ]);
                    }else if ($postjson['status']=='oout' && $result['jam_istirahat']=='') {
                        // $newTime = date('H:i', strtotime('-1 hour', strtotime($this->time)));
                        $this->db->set([
                            'jam_istirahat'     => '12:30',
                            'jam_sistirahat'    => '14:00',
                            'is_status'         => $status_ab
                        ]);
                    }else{
                        // if ($postjson['status']=='oout' && $this->time>'14:00') { $this->time = '14:00'; }
                        // if ($postjson['status']=='iin' && $this->time>'12:30') { $this->time = '12:30'; }
                        $this->db->set([
                            'point_latitude'    => $lat_point,
                            'point_longitude'   => $lng_point,
                            'is_status'         => $status_ab,
                            $field              => $this->time
                        ]);
                    }
                    $this->db->where('absen_id', $result['absen_id']);
                    $res = $this->db->update('tx_absensi');

                    if ($postjson['status']=='in') {

                        if ($postjson['poinradius']=='y') {
                            $ispending = 'n';
                        }else{
                            $ispending = 'y';
                        }

                        if ($postjson['image_path']!='') {
                            $ext = 'jpeg';
                            $imgstring = $postjson['image_path'];
                            $imgstring = trim(str_replace('data:image/'.$ext.';base64,', "", $imgstring));
                            $imgstring = str_replace(' ', '+', $imgstring);
                            $data = base64_decode($imgstring);
                            $directory = "assets/uploaded/users/";
                            $absenfoto  = $directory."absen_masuk_".$postjson['id']."_".$randname.".jpg";  
                            file_put_contents($absenfoto, $data);
                        }else{
                            $absenfoto = '';
                        }

                        $this->db->set([
                            'foto_absen_masuk'    => $absenfoto,
                            'latitude_masuk'      => $postjson['latitude'],
                            'longitude_masuk'     => $postjson['longitude'],
                            'catatan_masuk'       => $postjson['catatan'],
                            'is_point_masuk'      => $postjson['poinradius'],
                            'is_pending'          => $ispending,
                            $field                => $this->time
                        ]);
                        $this->db->where('absen_id', $result['absen_id']);
                        $res = $this->db->update('tx_absensi');

                    }else if ($postjson['status']=='out') {

                        if ($postjson['image_path']!='') {
                            $ext = 'jpeg';
                            $imgstring = $postjson['image_path'];
                            $imgstring = trim(str_replace('data:image/'.$ext.';base64,', "", $imgstring));
                            $imgstring = str_replace(' ', '+', $imgstring);
                            $data = base64_decode($imgstring);
                            $directory = "assets/uploaded/users/";
                            $absenfoto  = $directory."absen_keluar_".$postjson['id']."_".$randname.".jpg";  
                            file_put_contents($absenfoto, $data);
                        }else{
                            $absenfoto = '';
                        }

                        $this->db->set([
                            'foto_absen_keluar'    => $absenfoto,
                            'latitude_keluar'      => $postjson['latitude'],
                            'longitude_keluar'     => $postjson['longitude'],
                            'catatan_keluar'       => $postjson['catatan'],
                            'is_point_keluar'      => $postjson['poinradius'],
                            'acc_keluar'           => $postjson['poinradius'],
                            $field                 => $this->time
                        ]);
                        $this->db->where('absen_id', $result['absen_id']);
                        $res = $this->db->update('tx_absensi');
                    }
                }
            }

            return json_encode(array('status'=>true, 'msg'=>$msg));

        }else{
            return json_encode(array('status'=>false, 'msg'=>'Proses dihentikan, id unknown, silahkan logout dan login kembali.'));
        }

    }

    public function attendance_lembur($postjson) {

        if (!isset($postjson)) exit();
        $msg = '';
        if ($postjson['status']=='overtime' || $postjson['status']=='sovertime') {

            $randname = date('Ymdhis').$postjson['id'];

            if ($postjson['image_path']!='') {
                $ext = 'jpeg';
                $imgstring = $postjson['image_path'];
                $imgstring = trim(str_replace('data:image/'.$ext.';base64,', "", $imgstring));
                $imgstring = str_replace(' ', '+', $imgstring);
                $data = base64_decode($imgstring);
                $directory = "assets/uploaded/users/";
                $absenfoto  = $directory."lembur_".$postjson['id']."_".$randname.".jpg";  
                file_put_contents($absenfoto, $data);
            }else{
                $absenfoto = '';
            }

            if ($postjson['status']=='overtime') {

                $qlokasi = $this->db->query("SELECT * FROM m_pegawai_lokasi a
                JOIN m_lokasi b ON a.lokasi_id=b.lokasi_id
                WHERE a.pegawai_id='$postjson[id]'")->row_array();

                $lat_point = '';
                $lng_point = '';
                if($qlokasi){
                    $lat_point = $qlokasi['garis_lintang'];
                    $lng_point = $qlokasi['garis_bujur'];
                }else {
                    $qlokasi = $this->db->query("SELECT * FROM m_lokasi")->row_array();
                    if($qlokasi){
                        $lat_point = $qlokasi['garis_lintang'];
                        $lng_point = $qlokasi['garis_bujur'];
                    }
                }

                $this->db->set([
                    'absen_masuk'         => $this->time,
                    'foto_absen_masuk'    => $absenfoto,
                    'latitude_masuk'      => $postjson['latitude'],
                    'longitude_masuk'     => $postjson['longitude'],
                    'point_latitude'      => $lat_point,
                    'point_longitude'     => $lng_point
                ]);
                $msg = 'Absen lembur berhasil.';
            }else{
                $this->db->set([
                    'absen_keluar'         => $this->time,
                    'foto_absen_keluar'    => $absenfoto,
                    'latitude_keluar'      => $postjson['latitude'],
                    'longitude_keluar'     => $postjson['longitude'],
                    'catatan_hasil_lembur' => $postjson['catatan']
                ]);
                $msg = 'Absen lembur keluar berhasil.';
            }
            $this->db->where('pegawai_id', $postjson['id']);
            $this->db->where('tanggal_lembur', $this->today);
            $res = $this->db->update('tx_lembur_pegawai');
            if ($res==true) {
                return json_encode(array('status'=>true, 'msg'=>$msg));
            }else{
                return json_encode(array('status'=>false, 'msg'=>'Proses dihentikan, absen lembur gagal.'));
            }
        }else{
            return json_encode(array('status'=>false, 'msg'=>'Unknown in/out.'));
        }

    }

    public function add_lembur($postjson) {

        if (!isset($postjson)) exit();

        $c_data = $this->db->query("SELECT * FROM m_pegawai WHERE pegawai_id='$postjson[id]'")->num_rows();

        if ($c_data > 0) {

            $c_data = $this->db->query("SELECT * FROM tx_lembur_pegawai WHERE pegawai_id='$postjson[id]' AND tanggal_lembur='$postjson[tanggal]'")->num_rows();
            if ($c_data==0) {
                $datains = [
                    'tanggal_lembur'      => $postjson['tanggal'],
                    'masuk_lembur'        => $postjson['jmulai'],
                    'keluar_lembur'       => $postjson['jakhir'],
                    'is_status'           => 0,
                    'created_at'          => date('Y-m-d H:i:s')
                ];
                $res = $this->db->insert('tx_lembur', $datains);
                $idnya = $this->db->insert_id();

                $datainss = [
                    'lembur_id'             => $idnya,
                    'pegawai_id'            => $postjson['id'],
                    'tanggal_lembur'        => $postjson['tanggal'],
                    'catatan_lembur'        => $postjson['catatan']
                ];
                $res = $this->db->insert('tx_lembur_pegawai', $datainss);

                if($res==true){
                    return json_encode(array('status'=>true, 'msg'=>'Request lembur telah dikirim.'));
                }else{
                    return json_encode(array('status'=>false, 'msg'=>'Proses dihentikan, request lembur gagal.'));
                }
            }else{
                return json_encode(array('status'=>false, 'msg'=>'Proses dihentikan, request lembur pada tanggal ini sudah ada.'));
            }
        }else{
            return json_encode(array('status'=>false, 'msg'=>'Proses dihentikan, ID unknown silahkan logout dan login kembali..'));
        }
    }

    public function add_izin($postjson) {

        if (!isset($postjson)) exit();

        $c_data = $this->db->query("SELECT * FROM m_pegawai WHERE pegawai_id='$postjson[id]'")->num_rows();

        if ($c_data > 0) {

            $tanggal = date("Y-m-d", strtotime($postjson['tanggal']));
            $totanggal = date("Y-m-d", strtotime($postjson['totanggal']));

            if ($postjson['totanggal']=='') {
                $totanggal = $tanggal;
            }else{
                $totanggal = $totanggal;
            }
            
            if($tanggal<$this->today){
                return json_encode(array('status'=>false, 'msg'=>'Proses dihentikan, minimal tanggal yang di isi adalah tanggal hari ini.'));
                exit();
            }

            if($tanggal>$totanggal){
                return json_encode(array('status'=>false, 'msg'=>'Proses dihentikan, tanggal berakhir tidak boleh lebih rendah dari tanggal mulai.'));
                exit();
            }

            $c_data = $this->db->query("SELECT * FROM tx_request_izin a JOIN tx_request_izin_pegawai b ON a.request_izin_id=b.request_izin_id WHERE b.pegawai_id='$postjson[id]' AND a.tipe_request='$postjson[kategori]' AND a.is_status='0' AND a.tanggal_request<='$tanggal' AND a.tanggal_request_end>='$tanggal'")->num_rows();
            if ($c_data==0) {

                $randname = date('Ymdhis').$postjson['id'];

                if ($postjson['totanggal']=='') {
                    $tglakh = $tanggal;
                }else{
                    $tglakh = $totanggal;
                }

                if ($tglakh<$tanggal) {
                    $tglakh = $tanggal;
                }

                $tgl1 = strtotime($tanggal); 
                $tgl2 = strtotime($tglakh); 
                $jarak = $tgl2 - $tgl1;
                $jumlahhari = $jarak / 60 / 60 / 24;

                if(!isset($postjson['doc_file'])) $postjson['doc_file'] = '';
                if(!isset($postjson['doc_type'])) $postjson['doc_type'] = 'image/jpeg';
                if ($postjson['doc_file']!='') {
                    $imgstring = $postjson['doc_file'];
                    $imgstring = trim(str_replace('data:'.$postjson['doc_type'].';base64,', "", $imgstring));
                    $imgstring = str_replace(' ', '+', $imgstring);
                    $data = base64_decode($imgstring);
                    $extension = '.jpg';
                    if ($postjson['doc_type'] == 'application/pdf') {
                        $extension = '.pdf';
                    }
                    $directory = "assets/uploaded/users/";
                    $filedoc  = $directory."files_".$postjson['id']."_".$randname.$extension;  
                    file_put_contents($filedoc, $data);
                }else{
                    $filedoc = '';
                }

                $data = [
                    'tipe_request'          => $postjson['kategori'],
                    'tanggal_request'       => $tanggal,
                    'tanggal_request_end'   => $tglakh,
                    'r_jam_masuk'           => $postjson['jmulai'],
                    'r_jam_keluar'          => $postjson['jakhir'],
                    'catatan_awal'          => $postjson['catatan'],
                    'jumlah_cuti'           => $jumlahhari+1,
                    'file_dokumen'          => $filedoc,
                    'is_status'             => 0, // 0 pending, 1 acc, 2 tolak
                    'created_at'            => date('Y-m-d H:i:s')
                ];
                $res = $this->db->insert('tx_request_izin', $data);
                $idnya = $this->db->insert_id();

                $data = [
                    'request_izin_id'   => $idnya,
                    'pegawai_id'        => $postjson['id'],
                    'tanggal_request'       => $tanggal,
                    'tanggal_request_end'   => $tglakh,
                    'catatan_awal'          => $postjson['catatan'],
                    'jumlah_cuti'           => $jumlahhari+1
                ];
                $res = $this->db->insert('tx_request_izin_pegawai', $data);

                if($res==true){
                    return json_encode(array('status'=>true, 'msg'=>'Request izin telah dikirim.'));
                }else{
                    return json_encode(array('status'=>false, 'msg'=>'Proses dihentikan, request gagal.'));
                }
            }else{
                return json_encode(array('status'=>false, 'msg'=>'Proses dihentikan, request diantara tanggal ini sudah ada.'));
            }
        }else{
            return json_encode(array('status'=>false, 'msg'=>'Proses dihentikan, ID unknown silahkan logout dan login kembali..'));
        }
    }

    public function edit_izin($postjson) {

        if (!isset($postjson)) exit();

        $c_data = $this->db->query("SELECT * FROM m_pegawai WHERE pegawai_id='$postjson[id]'")->num_rows();

        if ($c_data > 0) {

            $tanggal = date("Y-m-d", strtotime($postjson['tanggal']));
            $totanggal = date("Y-m-d", strtotime($postjson['totanggal']));

            if ($postjson['totanggal']=='') {
                $totanggal = $tanggal;
            }else{
                $totanggal = $totanggal;
            }
            
            if($tanggal<$this->today){
                return json_encode(array('status'=>false, 'msg'=>'Proses dihentikan, minimal tanggal yang di isi adalah tanggal hari ini.'));
                exit();
            }

            if($tanggal>$totanggal){
                return json_encode(array('status'=>false, 'msg'=>'Proses dihentikan, tanggal berakhir tidak boleh lebih rendah dari tanggal mulai.'));
                exit();
            }

            $c_data = $this->db->query("SELECT * FROM tx_request_izin a JOIN tx_request_izin_pegawai b ON a.request_izin_id=b.request_izin_id WHERE b.pegawai_id='$postjson[id]' AND a.tipe_request='$postjson[kategori]' AND a.is_status='0' AND a.tanggal_request<='$tanggal' AND a.tanggal_request_end>='$tanggal' AND a.request_izin_id!='$postjson[idizin]'")->num_rows();
            if ($c_data==0) {

                $randname = date('Ymdhis').$postjson['id'];

                if ($postjson['totanggal']=='') {
                    $tglakh = $tanggal;
                }else{
                    $tglakh = $totanggal;
                }

                if ($tglakh<$tanggal) {
                    $tglakh = $tanggal;
                }

                $tgl1 = strtotime($tanggal); 
                $tgl2 = strtotime($tglakh); 
                $jarak = $tgl2 - $tgl1;
                $jumlahhari = $jarak / 60 / 60 / 24;

                $cc_data = $this->db->query("SELECT file_dokumen FROM tx_request_izin WHERE request_izin_id='$postjson[idizin]'")->row_array();

                if(!isset($postjson['doc_file'])) $postjson['doc_file'] = '';
                if(!isset($postjson['doc_type'])) $postjson['doc_type'] = 'image/jpeg';
                if ($postjson['doc_file']!='') {
                    $imgstring = $postjson['doc_file'];
                    $imgstring = trim(str_replace('data:'.$postjson['doc_type'].';base64,', "", $imgstring));
                    $imgstring = str_replace(' ', '+', $imgstring);
                    $data = base64_decode($imgstring);
                    $extension = '.jpg';
                    if ($postjson['doc_type'] == 'application/pdf') {
                        $extension = '.pdf';
                    }
                    $directory = "assets/uploaded/users/";
                    $filedoc  = $directory."files_".$postjson['id']."_".$randname.$extension;  
                    file_put_contents($filedoc, $data);
                    if($cc_data['file_dokumen']!=''){
                        if (file_exists(FCPATH.$cc_data['file_dokumen'])){ unlink(FCPATH.$cc_data['file_dokumen']); }
                    }
                }else{
                    $filedoc = $cc_data['file_dokumen'];
                }

                $this->db->set([
                    'tipe_request'          => $postjson['kategori'],
                    'tanggal_request'       => $tanggal,
                    'tanggal_request_end'   => $tglakh,
                    'r_jam_masuk'           => $postjson['jmulai'],
                    'r_jam_keluar'          => $postjson['jakhir'],
                    'catatan_awal'          => $postjson['catatan'],
                    'jumlah_cuti'           => $jumlahhari+1,
                    'file_dokumen'          => $filedoc,
                    'is_status'             => 0 // 0 pending, 1 acc, 2 tolak
                ]);
                $this->db->where('request_izin_id', $postjson['idizin']);
                $res = $this->db->update('tx_request_izin');

                $this->db->set([
                    'tanggal_request'       => $tanggal,
                    'tanggal_request_end'   => $tglakh,
                    'catatan_awal'          => $postjson['catatan'],
                    'jumlah_cuti'           => $jumlahhari+1
                ]);
                $this->db->where('pegawai_id', $postjson['id']);
                $this->db->where('request_izin_id', $postjson['idizin']);
                $res = $this->db->update('tx_request_izin_pegawai');

                if($res==true){
                    return json_encode(array('status'=>true, 'msg'=>'Request izin berhasil diperbarui.'));
                }else{
                    return json_encode(array('status'=>false, 'msg'=>'Proses dihentikan, edit request gagal.'));
                }
            }else{
                return json_encode(array('status'=>false, 'msg'=>'Proses dihentikan, request pada diantara tanggal ini sudah ada.'));
            }
        }else{
            return json_encode(array('status'=>false, 'msg'=>'Proses dihentikan, ID unknown silahkan logout dan login kembali..'));
        }
    }

    public function del_izin($postjson) {

        if (!isset($postjson)) exit();

        $c_data = $this->db->query("SELECT * FROM tx_request_izin a JOIN tx_request_izin_pegawai b ON a.request_izin_id=b.request_izin_id WHERE a.request_izin_id='$postjson[idizin]' AND b.pegawai_id='$postjson[id]'")->row_array();

        if ($c_data) {
            $res = $this->db->delete('tx_request_izin', ['request_izin_id' => $postjson['idizin']]);
            if($res==true){
                if($c_data['file_dokumen']!=''){
                    if (file_exists(FCPATH.$c_data['file_dokumen'])){ unlink(FCPATH.$c_data['file_dokumen']); }
                }
                return json_encode(array('status'=>true, 'msg'=>'Data berhasil dihapus.'));
            }else{
                return json_encode(array('status'=>false, 'msg'=>'Proses dihentikan, data gagal dihapus.'));
            }
        }else{
            return json_encode(array('status'=>false, 'msg'=>'Proses dihentikan, data tidak ditemukan.'));
        }
    }

    public function del_lembur($postjson) {

        if (!isset($postjson)) exit();

        $c_data = $this->db->query("SELECT * FROM tx_lembur a JOIN tx_lembur_pegawai b ON a.lembur_id=b.lembur_id WHERE a.lembur_id='$postjson[idizin]' AND b.pegawai_id='$postjson[id]'")->row_array();

        if ($c_data) {
            $res = $this->db->delete('tx_lembur_pegawai', ['lembur_id' => $postjson['idizin']]);
            
            $count = $this->db->query("SELECT * FROM tx_lembur_pegawai WHERE lembur_id='$postjson[idizin]'")->num_rows();
            if($count==0){
                $res = $this->db->delete('tx_lembur', ['lembur_id' => $postjson['idizin']]);
            }

            if($res==true){
                return json_encode(array('status'=>true, 'msg'=>'Data berhasil dihapus.'));
            }else{
                return json_encode(array('status'=>false, 'msg'=>'Proses dihentikan, data gagal dihapus.'));
            }
        }else{
            return json_encode(array('status'=>false, 'msg'=>'Proses dihentikan, data tidak ditemukan.'));
        }
    }

    public function reset_absen($postjson) {

        if (!isset($postjson)) exit();

        $c_data = $this->db->query("SELECT * FROM m_pegawai WHERE pegawai_id='$postjson[id]'")->num_rows();
        $c_absen = $this->db->query("SELECT * FROM tx_absensi WHERE pegawai_id='$postjson[id]' AND tanggal_absen='$this->today'")->row_array();

        if ($c_data > 0) {
            $res = $this->db->delete('tx_absensi', ['pegawai_id' => $postjson['id'], 'tanggal_absen' => $this->today]);
            if($res==true){
                if($c_absen['foto_absen_masuk']!=''){
                    if (file_exists(FCPATH.$c_absen['foto_absen_masuk'])){ unlink(FCPATH.$c_absen['foto_absen_masuk']); }
                }
                if($c_absen['foto_absen_keluar']!=''){
                    if (file_exists(FCPATH.$c_absen['foto_absen_keluar'])){ unlink(FCPATH.$c_absen['foto_absen_keluar']); }
                }
                return json_encode(array('status'=>true, 'msg'=>'Data berhasil dihapus.'));
            }else{
                return json_encode(array('status'=>false, 'msg'=>'Proses dihentikan, data gagal dihapus.'));
            }
        }else{
            return json_encode(array('status'=>false, 'msg'=>'Proses dihentikan, ID unknown silahkan logout dan login kembali..'));
        }
    }

    public function terkini($postjson) {

        if (!isset($postjson)) exit();

        $msg = 'Data berhasil disimpan.';
        $res = false;

        $datakar = authKaryawan($postjson['id']);

        if ($datakar) {

            $randname = date('Ymdhis').$postjson['id'];

            if ($postjson['image_path']!='') {
                $ext = 'jpeg';
                $imgstring = $postjson['image_path'];
                $imgstring = trim(str_replace('data:image/'.$ext.';base64,', "", $imgstring));
                $imgstring = str_replace(' ', '+', $imgstring);
                $data = base64_decode($imgstring);
                $directory = "assets/uploaded/users/";
                $absenfoto  = $directory."terkini_".$postjson['id']."_".$randname.".jpg";  
                file_put_contents($absenfoto, $data);
            }else{
                $absenfoto = '';
            }

            $data = [
                'pegawai_id'        => $postjson['id'],
                'tanggal'           => $this->today,
                'jam'               => $this->time,
                'catatan'           => $postjson['catatan'],
                'foto'              => $absenfoto,
                'latitude_lt'       => $postjson['latitude'],
                'longitude_lt'      => $postjson['longitude'],
                'alamat_lengkap'    => $postjson['alamatlengkap'],
                'is_read'           => 'n',
                'created_at'        => date('Y-m-d H:i:s')
            ];
            $res = $this->db->insert('tx_lokasi_terkini', $data);

            return json_encode(array('status'=>true, 'msg'=>$msg));

        }else{
            return json_encode(array('status'=>false, 'msg'=>'Proses dihentikan, id unknown, silahkan logout dan login kembali.'));
        }

    }

}