<?php
$dsn = 'mysql:host=localhost;dbname=db_erp';
$user = 'root';
$password = '';
try {
    $dbh = new PDO($dsn, $user, $password);
    
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

    $checkJumlahPola = checkJumlahPolaLocal($dbh, 32, 2374);
    echo "checkJumlahPola for 32, 2374: $checkJumlahPola\n";

} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
