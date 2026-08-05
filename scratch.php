<?php
$mysqli = new mysqli("localhost", "root", "", "db_erp");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
$result = $mysqli->query("DESCRIBE tx_kpi_absensi");
while($row = $result->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
$mysqli->close();
?>
