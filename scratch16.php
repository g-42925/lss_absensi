<?php
$dsn = 'mysql:host=localhost;dbname=db_erp';
$user = 'root';
$password = '';
try {
    $dbh = new PDO($dsn, $user, $password);
    
    $stmt = $dbh->query("SELECT * FROM m_pola_kerja_det WHERE pola_kerja_id = 32 ORDER BY is_day ASC");
    $det = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($det);

} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
