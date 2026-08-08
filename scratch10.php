<?php
$dsn = 'mysql:host=localhost;dbname=db_erp';
$user = 'root';
$password = '';
try {
    $dbh = new PDO($dsn, $user, $password);
    
    // Check m_pegawai columns
    $stmt = $dbh->query("DESCRIBE m_pegawai");
    $emp_cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "m_pegawai columns: " . implode(", ", $emp_cols) . "\n\n";

    // Check divisions columns
    $stmt2 = $dbh->query("DESCRIBE divisions");
    $div_cols = $stmt2->fetchAll(PDO::FETCH_COLUMN);
    echo "divisions columns: " . implode(", ", $div_cols) . "\n\n";

    // Check m_pola_kerja columns
    $stmt3 = $dbh->query("DESCRIBE m_pola_kerja");
    $pola_cols = $stmt3->fetchAll(PDO::FETCH_COLUMN);
    echo "m_pola_kerja columns: " . implode(", ", $pola_cols) . "\n\n";
    
    // Let's get Nazly's division
    $stmt4 = $dbh->query("SELECT p.pegawai_id, p.division_id, d.division_name FROM m_pegawai p LEFT JOIN divisions d ON p.division_id=d.id WHERE p.pegawai_id=79");
    $nazly = $stmt4->fetch(PDO::FETCH_ASSOC);
    echo "Nazly: "; print_r($nazly);
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
