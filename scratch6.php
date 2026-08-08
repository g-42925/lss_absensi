<?php
$dsn = 'mysql:host=localhost;dbname=db_erp';
$user = 'root';
$password = '';

try {
    $dbh = new PDO($dsn, $user, $password);
    $stmt = $dbh->query('SELECT * FROM m_pola_kerja_det LIMIT 10');
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($result);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>
