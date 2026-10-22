<?php
$dsn = 'mysql:host=localhost;dbname=db_erp';
$user = 'root';
$password = '';
try {
    $dbh = new PDO($dsn, $user, $password);
    
    // Simulate fallback logic
    $pegawai_id = 79;
    $tanggal_absen = '2025-11-01';

    $stmt = $dbh->prepare("SELECT pk.pola_kerja_id, p.tanggal_mulai_kerja
                 FROM m_pegawai p
                 JOIN divisions d ON p.division_id = d.id
                 JOIN m_pola_kerja pk ON LOWER(pk.nama_pola) = LOWER(d.division_name) AND pk.company_id = p.company_id
                 WHERE p.pegawai_id = ? AND pk.is_del = 'n' LIMIT 1");
    $stmt->execute([$pegawai_id]);
    $fallback = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r($fallback);

    if ($fallback) {
        $pola_kerja_id = $fallback['pola_kerja_id'];
        $tanggal_mulai = $fallback['tanggal_mulai_kerja'];
        if (empty($tanggal_mulai)) $tanggal_mulai = '2025-01-01';

        $hari = (strtotime($tanggal_absen) - strtotime($tanggal_mulai)) / (60 * 60 * 24);
        $hari_ke = $hari + 1; // Asumsi mulai dari hari ke 1

        function checkJumlahPolaLocal($dbh, $id, $jHari) {
            $stmt = $dbh->query("SELECT jumlah_hari_siklus FROM m_pola_kerja WHERE pola_kerja_id='$id'");
            $query = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$query) return $jHari;
            $siklus = $query['jumlah_hari_siklus'] ?: 0;
            if ($jHari > $siklus && $siklus > 0) {
                return checkJumlahPolaLocal($dbh, $id, $jHari - $siklus);
            }
            return $jHari;
        }

        $checkJumlahPola = checkJumlahPolaLocal($dbh, $pola_kerja_id, $hari_ke);
        echo "hari_ke: $hari_ke, checkJumlahPola: $checkJumlahPola\n";

        $stmt2 = $dbh->prepare("SELECT a.jam_masuk, a.jam_pulang, b.toleransi_terlambat 
                 FROM m_pola_kerja_det a 
                 JOIN m_pola_kerja b ON a.pola_kerja_id=b.pola_kerja_id 
                 WHERE a.pola_kerja_id=? AND a.is_day=?");
        $stmt2->execute([$pola_kerja_id, $checkJumlahPola]);
        $det = $stmt2->fetch(PDO::FETCH_ASSOC);
        print_r($det);
    }
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
