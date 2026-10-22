<?php
$dsn = 'mysql:host=localhost;dbname=db_erp';
$user = 'root';
$password = '';

try {
    $dbh = new PDO($dsn, $user, $password);
    $dbh->exec("ALTER TABLE tx_kpi_absensi ADD COLUMN jumlah_sp INT(11) DEFAULT 0 AFTER total_jam_kerja;");
    $dbh->exec("ALTER TABLE tx_kpi_absensi ADD COLUMN kpi_score DECIMAL(5,2) DEFAULT 0.00 AFTER jumlah_sp;");
    echo "Columns added.";
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>
