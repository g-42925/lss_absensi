<?php
$dsn = 'mysql:host=localhost;dbname=db_erp';
$user = 'root';
$password = '';
try {
    $dbh = new PDO($dsn, $user, $password);
    
    $stmt = $dbh->query("SELECT tanggal_absen, j_masuk, j_pulang, j_toleransi FROM tx_absensi WHERE pegawai_id=79 ORDER BY tanggal_absen ASC LIMIT 31");
    $absensi = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($absensi);

} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
