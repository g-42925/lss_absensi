<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=db_erp', 'root', '');
    $stmt = $db->query("SELECT pegawai_id, tanggal_absen, is_status, jam_masuk, jam_keluar, j_masuk, j_pulang, j_toleransi, isLate FROM tx_absensi WHERE is_status IN ('hhk', 'hbhk') ORDER BY tanggal_absen DESC LIMIT 10");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
