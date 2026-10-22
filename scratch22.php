<?php
$dsn = 'mysql:host=localhost;dbname=db_erp';
$user = 'root';
$password = '';
try {
    $dbh = new PDO($dsn, $user, $password);
    
    $stmt = $dbh->query("SELECT tanggal_absen, is_status, jam_masuk, jam_keluar, j_masuk, j_pulang FROM tx_absensi WHERE pegawai_id=82 AND tanggal_absen LIKE '2026-07-%' ORDER BY tanggal_absen ASC LIMIT 5");
    $absensi = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($absensi);

} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
