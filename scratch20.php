<?php
define('BASEPATH', TRUE);
$dsn = 'mysql:host=localhost;dbname=db_erp';
$user = 'root';
$password = '';
try {
    $dbh = new PDO($dsn, $user, $password);

    $pegawai_id = 82;

    $stmt = $dbh->query("SELECT * FROM m_pegawai_pola WHERE pegawai_id=$pegawai_id");
    $pola = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($pola);

} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
