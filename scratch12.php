<?php
$dsn = 'mysql:host=localhost;dbname=db_erp';
$user = 'root';
$password = '';
try {
    $dbh = new PDO($dsn, $user, $password);
    
    // Find Yovi
    $stmt = $dbh->query("SELECT p.pegawai_id, p.division_id, d.division_name, p.tanggal_mulai_kerja, p.company_id FROM m_pegawai p LEFT JOIN divisions d ON p.division_id=d.id WHERE p.nama_pegawai LIKE '%Yovi%'");
    $yovi = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r($yovi);

    if ($yovi) {
        $pegawai_id = $yovi['pegawai_id'];
        $tanggal_absen = '2026-07-01';

        $stmt2 = $dbh->prepare("SELECT pk.pola_kerja_id, p.tanggal_mulai_kerja
                 FROM m_pegawai p
                 JOIN divisions d ON p.division_id = d.id
                 JOIN m_pola_kerja pk ON LOWER(pk.nama_pola) = LOWER(d.division_name) AND pk.company_id = p.company_id
                 WHERE p.pegawai_id = ? AND pk.is_del = 'n' LIMIT 1");
        $stmt2->execute([$pegawai_id]);
        $fallback = $stmt2->fetch(PDO::FETCH_ASSOC);
        echo "Fallback query result:\n";
        print_r($fallback);
    }
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
