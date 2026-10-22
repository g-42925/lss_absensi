<?php
define('BASEPATH', TRUE);
$dsn = 'mysql:host=localhost;dbname=db_erp';
$user = 'root';
$password = '';
try {
    $dbh = new PDO($dsn, $user, $password);

    $pegawai_id = 82;

    $stmt = $dbh->prepare("SELECT pk.pola_kerja_id, p.tanggal_mulai_kerja, p.nama_pegawai, d.division_name
                 FROM m_pegawai p
                 JOIN divisions d ON p.division_id = d.id
                 JOIN m_pola_kerja pk ON LOWER(pk.nama_pola) = LOWER(d.division_name) AND pk.company_id = p.company_id
                 WHERE p.pegawai_id = ? AND pk.is_del = 'n' LIMIT 1");
    $stmt->execute([$pegawai_id]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r($res);
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
