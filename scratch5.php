<?php
$dsn = 'mysql:host=localhost;dbname=db_erp';
$user = 'root';
$password = '';

try {
    $dbh = new PDO($dsn, $user, $password);
    $stmt = $dbh->query('SELECT tanggal_absen, pegawai_id, is_status, jam_masuk, jam_keluar, j_masuk, j_pulang, j_toleransi FROM tx_absensi LIMIT 10');
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($result);
} 
catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>
