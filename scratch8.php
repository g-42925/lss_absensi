<?php
$dsn = 'mysql:host=localhost;dbname=db_erp';
$user = 'root';
$password = '';
try {
    $dbh = new PDO($dsn, $user, $password);
    
    // Find pegawai_id for Nazly Ade Nurhedy
    $stmt = $dbh->query("SELECT pegawai_id, nama_pegawai FROM m_pegawai WHERE nama_pegawai LIKE '%Nazly%'");
    $pegawai = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r($pegawai);

    if ($pegawai) {
        $pid = $pegawai['pegawai_id'];
        
        $stmt2 = $dbh->query("SELECT * FROM m_pegawai_pola WHERE pegawai_id = $pid");
        $polas = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        echo "\nm_pegawai_pola:\n";
        print_r($polas);

        $stmt3 = $dbh->query("SELECT * FROM m_pola_kerja");
        $polakerja = $stmt3->fetchAll(PDO::FETCH_ASSOC);
        echo "\nm_pola_kerja:\n";
        print_r($polakerja);
    }
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
