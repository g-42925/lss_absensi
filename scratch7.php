<?php
define('BASEPATH', TRUE);
$dsn = 'mysql:host=localhost;dbname=db_erp';
$user = 'root';
$password = '';
try {
    $dbh = new PDO($dsn, $user, $password);
    
    // Simulate checkJumlahPola
    function checkJumlahPolaLocal($dbh, $id, $jHari) {
        $stmt = $dbh->query("SELECT jumlah_hari_siklus FROM m_pola_kerja WHERE pola_kerja_id='$id'");
        $query = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$query) return $jHari;
        $siklus = $query['jumlah_hari_siklus'] ?: 0;
        if ($jHari > $siklus && $siklus > 0) {
            return checkJumlahPolaLocal($dbh, $id, $jHari - $siklus);
        }
        return $jHari;
    }

    $pegawai_id = 78;
    $tanggal_absen = '2025-11-01';

    $stmt = $dbh->prepare("SELECT mulai_berlaku_tanggal, dari_hari_ke, pola_kerja_id FROM m_pegawai_pola WHERE pegawai_id=? AND is_selected='y'");
    $stmt->execute([$pegawai_id]);
    $pola = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r($pola);

    if ($pola && $tanggal_absen >= $pola['mulai_berlaku_tanggal']) {
        $hari = (strtotime($tanggal_absen) - strtotime($pola['mulai_berlaku_tanggal'])) / (60 * 60 * 24);
        $checkJumlahPola = checkJumlahPolaLocal($dbh, $pola['pola_kerja_id'], $hari + $pola['dari_hari_ke']);
        echo "Check Jumlah Pola for day offset $hari + " . $pola['dari_hari_ke'] . " = $checkJumlahPola\n";

        $stmt2 = $dbh->prepare("SELECT a.jam_masuk, a.jam_pulang, b.toleransi_terlambat 
                                FROM m_pola_kerja_det a 
                                JOIN m_pola_kerja b ON a.pola_kerja_id=b.pola_kerja_id 
                                WHERE a.pola_kerja_id=? AND a.is_day=?");
        $stmt2->execute([$pola['pola_kerja_id'], $checkJumlahPola]);
        $det = $stmt2->fetch(PDO::FETCH_ASSOC);
        print_r($det);
    }
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
