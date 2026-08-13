<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
$mysqli = new mysqli('localhost', 'root', '', 'db_erp');
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
$res = $mysqli->query("SELECT pegawai_id, nama_pegawai FROM m_pegawai LIMIT 5");
while($row = $res->fetch_assoc()) { print_r($row); }
