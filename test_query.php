<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
$mysqli = new mysqli('localhost', 'root', '', 'db_erp');
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
$empId = 104;
$bulan = '08';
$tahun = 2026;
$res = $mysqli->query("SELECT SUM(penalty) as amt FROM warning WHERE employeeId = $empId AND MONTH(date) = $bulan AND YEAR(date) = $tahun");
print_r($res->fetch_assoc());
