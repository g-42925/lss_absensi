<?php

function is_logged_in() {
    $CI = get_instance();
    if (!$CI->session->userdata('u_id')) {
        redirect('auth');
    }
}

function cek_menu_access() {
    $CI = get_instance();
    if ($CI->session->userdata('role_id')!=1) {
        if ($CI->uri->segment(1)!='') {
            if ($CI->uri->segment(2)!='') {
                if ($CI->uri->segment(3)!='') {
                    if ($CI->uri->segment(4)!='') {
                        $segment = $CI->uri->segment(1).'/'.$CI->uri->segment(2).'/'.$CI->uri->segment(3).'/';
                    }else{
                        $segment = $CI->uri->segment(1).'/'.$CI->uri->segment(2).'/'.$CI->uri->segment(3).'/';
                    }
                }else{
                    $segment = $CI->uri->segment(1).'/'.$CI->uri->segment(2).'/';
                }
            }else{
            $segment = $CI->uri->segment(1).'/';
            }
        }else{
            $segment = '';
        }

        $query = $CI->db->query("SELECT * FROM m_role_access a JOIN m_menu b ON a.id_menu=b.menu_id JOIN m_role c ON a.id_role=c.role_id WHERE b.link_url='$segment' AND c.is_status='y' AND c.is_del='n' AND a.id_role=".$CI->session->userdata('role_id'))->num_rows();

        if ($query<1) {
            redirect('dashboard');
        }
    }
}

function pengaturanSistem(){
    $CI = get_instance();
    $query = $CI->db->query("SELECT * FROM m_setting WHERE setting_id='1'")->row_array();
    return ($query);
}

function authUser(){
    $CI = get_instance();
    $pid = $CI->session->userdata('u_id');
    $query = $CI->db->query("SELECT * FROM m_user a 
        JOIN m_role b ON a.role_id=b.role_id 
        JOIN m_permission c ON a.permission_id=c.permission_id 
        WHERE a.user_id='$pid' AND a.is_del='n'
    ")->row_array();
    return ($query);
}

function dataUser($id){
    $CI = get_instance();
    $query = $CI->db->query("SELECT * FROM m_user a 
        JOIN m_role b ON a.role_id=b.role_id 
        JOIN m_permission c ON a.permission_id=c.permission_id 
        WHERE a.user_id='$id' AND a.is_del='n'
    ")->row_array();
    return ($query);
}

function authKaryawan($id = null){
    $CI = get_instance();
    $query = $CI->db->query("SELECT * FROM m_pegawai
        WHERE pegawai_id='$id' AND is_del='n'
    ")->row_array();
    return ($query);
}

function dataKaryawan(){
    $CI = get_instance();
    $query = $CI->db->query("SELECT * FROM m_pegawai WHERE is_status='y' AND is_del='n'
        ")->result_array();
    return ($query);
}

function checkJumlahPola($id,$jhari){
    $CI = get_instance();
    $query = $CI->db->query("SELECT jumlah_hari_siklus FROM m_pola_kerja WHERE pola_kerja_id='$id'")->row_array();
    if (!isset($query['jumlah_hari_siklus'])) { $query['jumlah_hari_siklus'] = ''; }

    if($query['jumlah_hari_siklus']!=''){
        for ($i=0; $i < 10; $i++) { 
            if ($jhari>$query['jumlah_hari_siklus']) {
                $jhari = checkJumlahPola($id,$jhari-$query['jumlah_hari_siklus']);
                break;
            }
        }
    }
    return $jhari+0;

}

function checkStatusAbsen($st = '',$idp = '',$tglawal = '',$tglakhr = ''){
    $CI = get_instance();
    $query = $CI->db->query("SELECT count(is_status) as total FROM tx_absensi WHERE is_status='$st' AND pegawai_id='$idp' AND date(tanggal_absen) BETWEEN '$tglawal' AND '$tglakhr' AND is_pending='n' AND is_request=0")->row_array();

    if ($tglawal==$tglakhr) {
        $query2 = $CI->db->query("SELECT a.jumlah_cuti as total, a.tanggal_request_end FROM tx_request_izin a JOIN tx_request_izin_pegawai b ON a.request_izin_id=b.request_izin_id WHERE a.tipe_request='$st' AND b.pegawai_id='$idp' AND a.tanggal_request_end>='$tglawal' AND a.is_status=1")->row_array();
        if ($query2) {
            $query2['total'] = 1;
        }else{
            $query2['total'] = 0;
        }
    }else{
        $query2 = $CI->db->query("SELECT a.jumlah_cuti as total, a.tanggal_request_end FROM tx_request_izin a JOIN tx_request_izin_pegawai b ON a.request_izin_id=b.request_izin_id WHERE a.tipe_request='$st' AND b.pegawai_id='$idp' AND (date(a.tanggal_request) BETWEEN '$tglawal' AND '$tglakhr') AND a.is_status=1")->row_array();

        if ($query2) {
            if ($tglakhr<=$query2['tanggal_request_end']) {
                $tgl2 = strtotime($query2['tanggal_request_end']); 
                $tgl1 = strtotime($tglakhr); 
                $jarak = $tgl2 - $tgl1;
                $hari = $jarak / 60 / 60 / 24;
                $query2['total'] = $query2['total']-($hari);
            }
        }else{
            $query2 = $CI->db->query("SELECT a.jumlah_cuti as total, a.tanggal_request_end FROM tx_request_izin a JOIN tx_request_izin_pegawai b ON a.request_izin_id=b.request_izin_id WHERE a.tipe_request='$st' AND b.pegawai_id='$idp' AND a.tanggal_request_end>='$tglawal' AND a.tanggal_request_end<='$tglakhr' AND a.is_status=1")->row_array();
            if ($query2) {
                if ($tglakhr<=$query2['tanggal_request_end']) {
                    $tgl1 = strtotime($tglawal); 
                    $tgl2 = strtotime($query2['tanggal_request_end']); 
                    $jarak = $tgl2 - $tgl1;
                    $hari = $jarak / 60 / 60 / 24;
                    $query2['total'] = $query2['total']-($hari);
                }else{
                    $tgl1 = strtotime($tglawal); 
                    $tgl2 = strtotime($query2['tanggal_request_end']); 
                    $jarak = $tgl2 - $tgl1;
                    $hari = $jarak / 60 / 60 / 24;
                    $query2['total'] = $query2['total']-($hari);
                }
            }
        }
    }

    if (!isset($query['total'])) { $query['total'] = 0; }
    if (!isset($query2['total'])) { $query2['total'] = 0; }
    return $query['total']+$query2['total'];
}

function count_pending() {
    $CI = get_instance();
    $result = $CI->db->query("SELECT * FROM tx_absensi a
        JOIN m_pegawai b ON a.pegawai_id=b.pegawai_id
        WHERE b.is_status='y' AND b.is_del='n' AND (a.is_pending='y' OR a.acc_keluar='n')");

    return array('num'=>$result->num_rows(), 'result'=>$result->result_array());
}

function count_izin() {
    $CI = get_instance();
    $result = $CI->db->query("SELECT * FROM tx_request_izin a
        JOIN tx_request_izin_pegawai b ON a.request_izin_id=b.request_izin_id
        JOIN m_pegawai c ON b.pegawai_id=c.pegawai_id
        WHERE c.is_status='y' AND c.is_del='n' AND a.is_status=0");

    return array('num'=>$result->num_rows(), 'result'=>$result->result_array());
}

function count_lembur() {
    $CI = get_instance();
    $result = $CI->db->query("SELECT * FROM tx_lembur WHERE is_status=0");

    return array('num'=>$result->num_rows(), 'result'=>$result->result_array());
}

function indo($tgl = null){
    if ($tgl!=null) {
        $date = substr($tgl,0,10);
        $BulanIndo = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
        $pecahkan = explode('-', $date);
        $tgl = isset($pecahkan[2]) ? $pecahkan[2] : '';
        $bln = isset($pecahkan[1]) ? $pecahkan[1] : '';
        $thn = isset($pecahkan[0]) ? $pecahkan[0] : '';
        return $tgl . ' ' . $BulanIndo[ (int)$bln-1] . ' ' . $thn;
    }else{
        return '';
    }
}

function indolengkap($tgl = null){
    if ($tgl!=null) {
        $date = substr($tgl,0,10);
        $BulanIndo = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $pecahkan = explode('-', $date);
        $tgl = isset($pecahkan[2]) ? $pecahkan[2] : '';
        $bln = isset($pecahkan[1]) ? $pecahkan[1] : '';
        $thn = isset($pecahkan[0]) ? $pecahkan[0] : '';
        return $tgl . ' ' . $BulanIndo[ (int)$bln-1] . ' ' . $thn;
    }else{
        return '';
    }
}

function validateDate($date = null){
    return strtotime($date) !== false;
}

function hari_ini(){
    $hari = date ("D");
    switch($hari){
        case 'Sun':
            $hari_ini = "Minggu";
        break;
 
        case 'Mon':         
            $hari_ini = "Senin";
        break;
 
        case 'Tue':
            $hari_ini = "Selasa";
        break;
 
        case 'Wed':
            $hari_ini = "Rabu";
        break;
 
        case 'Thu':
            $hari_ini = "Kamis";
        break;
 
        case 'Fri':
            $hari_ini = "Jumat";
        break;
 
        case 'Sat':
            $hari_ini = "Sabtu";
        break;
        
        default:
            $hari_ini = "Unknown";     
        break;
    }
 
    return $hari_ini;
 
}

function urutId($table,$field){
    $CI = get_instance();
    $query = $CI->db->query("SELECT max($field) as id FROM $table")->row_array();
    $hasilid = $query['id']+1;
    return ($hasilid);
}

function urutIdwhere($table,$field,$field2,$where){
    $CI = get_instance();
    $query = $CI->db->query("SELECT max($field) as id FROM $table WHERE $field2='$where'")->row_array();
    $hasilid = $query['id']+1;
    return ($hasilid);
}

function formatRupiah($jumlah = 0){
    $conv = "Rp ".number_format($jumlah,0,',','.');
    return($conv);
}

function formatRupiahnorp($jumlah = 0,$kutip = 0){
    $conv = number_format($jumlah,$kutip,',','.');
    return($conv);
}

function randCode($panjang){
    $karakter= 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789';
    $string = '';
    for ($i = 0; $i < $panjang; $i++) {
        $pos = rand(0, strlen($karakter)-1);
        $string .= $karakter[$pos];
    }
    
    return $string;
}

function randNumb($panjang){
    $karakter= '123456789';
    $string = '';
    for ($i = 0; $i < $panjang; $i++) {
        $pos = rand(0, strlen($karakter)-1);
        $string .= $karakter[$pos];
    }
    
    return $string;
}

function str_replace_html($txt = null){
    if ($txt!=null) {
      $find = array("<?php","?>","<?","<?=","<script>","<script","</script>","<a>","<a","</a>","<button>","<button","</button>","<ul>","<ul","</ul>","<li>","<li","</li>","<ol>","<ol","</ol>");
      $replace = "-";
      return str_replace($find,$replace,$txt);
    }else{
      return $txt;
    }
}

function str_replace_html_noa($txt = null){
  if ($txt!=null) {
    $find = array("<?php","?>","<?","<?=","<script>","<script","</script>","<button>","<button","</button>","<ul>","<ul","</ul>","<li>","<li","</li>","<ol>","<ol","</ol>");
    $replace = "-";
    return str_replace($find,$replace,$txt);
  }else{
    return $txt;
  }
}

function str_replace_kutip($txt = null){
  if ($txt!=null) {
    $find = array('"','`');
    $replace = "'";
    return str_replace($find,$replace,$txt);
  }else{
    return $txt;
  }
}

function str_replace_kutipx($txt = null){
  if ($txt!=null) {
    $find = array("'");
    $replace = "-";
    return str_replace($find,$replace,$txt);
  }else{
    return $txt;
  }
}

function hitungBulan($tgl1,$tgl2){ 
    //convert
    $timeStart = strtotime($tgl1);
    $timeEnd = strtotime($tgl2);
    // Menambah bulan ini + semua bulan pada tahun sebelumnya
    $numBulan = 1 + (date("Y",$timeEnd)-date("Y",$timeStart))*12;
    // hitung selisih bulan
    $numBulan += date("m",$timeEnd)-date("m",$timeStart);
    return $numBulan;
}

function convertedTime($tambah,$date = null){ 
    if ($date==null) {
        $startTime = date("Y-m-d H:i:s");
    }else{
        $startTime = $date;
    }
    //add time
    $cenvertedTime = date('Y-m-d H:i:s',strtotime($tambah,strtotime($startTime)));
    //display the converted time
    return $cenvertedTime;
}

function url_replace($string) {
    $c = array (' ');
    $d = array ('/','\\',',','.','#',':',';','\'','"','[',']','{','}',')','(','|','`','~','!','@','%','$','^','*','?','&','=','+','°');
    $string = str_replace($d, '', $string); // Hilangkan karakter yang telah disebutkan di array $d
    $string = strtolower(str_replace($c, '-', $string)); // Ganti spasi dengan tanda - dan ubah hurufnya menjadi kecil semua
    return $string;
}

// ----

function check_access($role_id, $menu_id) {
    $CI = get_instance();
    $CI->db->where('role_id', $role_id);
    $CI->db->where('menu_id', $menu_id);
    $result = $CI->db->get('m_role_access');
    if ($result->num_rows() > 0) {
        return "checked='checked'";
    }
}

function resizeImagev2($resourceType,$image_width,$image_height,$resizeWidth,$resizeHeight) {
    // $resizeWidth = 100;
    // $resizeHeight = 100;
    $imageLayer = imagecreatetruecolor($resizeWidth,$resizeHeight);
    imagecopyresampled($imageLayer,$resourceType,0,0,0,0,$resizeWidth,$resizeHeight, $image_width,$image_height);
    return $imageLayer;
}

function resizeImgv2($source_image, $dir){
    list( $width, $height ) = getimagesize($source_image);

    $width_size = $width*50/100; // compress 50%
    $k = $width / $width_size;
    $new_width = $width / $k;
    $new_height = $height / $k;

    $fileName = $source_image;
    $sourceProperties = getimagesize($fileName);
    $uploadPath = $dir;
    $uploadImageType = $sourceProperties[2];
    $sourceImageWidth = $sourceProperties[0];
    $sourceImageHeight = $sourceProperties[1];
    switch ($uploadImageType) {
        case IMAGETYPE_JPEG:
            $resourceType = imagecreatefromjpeg($fileName);
            $imageLayer = resizeImagev2($resourceType,$sourceImageWidth,$sourceImageHeight,$new_width,$new_height);
            imagejpeg($imageLayer,$uploadPath);
            break;

        case IMAGETYPE_GIF:
            $resourceType = imagecreatefromgif($fileName);
            $imageLayer = resizeImagev2($resourceType,$sourceImageWidth,$sourceImageHeight,$new_width,$new_height);
            imagegif($imageLayer,$uploadPath);
            break;

        case IMAGETYPE_PNG:
            $resourceType = imagecreatefrompng($fileName);
            $imageLayer = resizeImagev2($resourceType,$sourceImageWidth,$sourceImageHeight,$new_width,$new_height);
            imagepng($imageLayer,$uploadPath);
            break;

        default:
            break;
    }
}

?>