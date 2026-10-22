<?php
define('BASEPATH', TRUE);
$dsn = 'mysql:host=localhost;dbname=db_erp';
$user = 'root';
$password = '';
try {
    $dbh = new PDO($dsn, $user, $password);
    
    // Calculate Abbas (82), for July (7), 2026.
    // I will write a mock of _get_valid_schedule for this context.
    
    $pegawai_id = 82;
    $tanggal_absen = '2026-07-01';
    
    $pola = $dbh->query("SELECT mulai_berlaku_tanggal, dari_hari_ke, pola_kerja_id 
             FROM m_pegawai_pola 
             WHERE pegawai_id=$pegawai_id AND is_selected='y'")->fetch(PDO::FETCH_ASSOC);
             
    $pola_kerja_id = null;
    $hari_ke = 1;
    
    if ($pola && $tanggal_absen >= $pola['mulai_berlaku_tanggal']) {
        $hari = (strtotime($tanggal_absen) - strtotime($pola['mulai_berlaku_tanggal'])) / (60 * 60 * 24);
        $hari_ke = round($hari) + $pola['dari_hari_ke'];
        $pola_kerja_id = $pola['pola_kerja_id'];
    } else {
        $fallback = $dbh->query("SELECT pk.pola_kerja_id, p.tanggal_mulai_kerja
                 FROM m_pegawai p
                 JOIN divisions d ON p.division_id = d.id
                 JOIN m_pola_kerja pk ON LOWER(pk.nama_pola) = LOWER(d.division_name) AND pk.company_id = p.company_id
                 WHERE p.pegawai_id = $pegawai_id AND pk.is_del = 'n' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
                 
        if ($fallback) {
            $pola_kerja_id = $fallback['pola_kerja_id'];
            $tgl_mulai = !empty($fallback['tanggal_mulai_kerja']) ? $fallback['tanggal_mulai_kerja'] : '2020-01-01';
            if ($tanggal_absen >= $tgl_mulai) {
                $hari = (strtotime($tanggal_absen) - strtotime($tgl_mulai)) / (60 * 60 * 24);
                $hari_ke = round($hari) + 1;
            }
        }
    }
    
    echo "pola_kerja_id: $pola_kerja_id\n";
    echo "hari_ke: $hari_ke\n";

} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
