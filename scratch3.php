<?php
$dsn = 'mysql:host=localhost;dbname=db_erp';
$user = 'root';
$password = '';

try {
    $dbh = new PDO($dsn, $user, $password);
    $stmt = $dbh->query('DESCRIBE tx_kpi_absensi');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>
