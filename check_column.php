<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
$mysqli = new mysqli('localhost', 'root', '', 'db_erp');
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
$result = $mysqli->query("SHOW COLUMNS FROM companies LIKE 'sp_deduction_policy'");
if ($result->num_rows > 0) {
    echo "COLUMN_EXISTS";
} else {
    echo "COLUMN_MISSING";
    $mysqli->query("ALTER TABLE companies ADD COLUMN sp_deduction_policy VARCHAR(50) DEFAULT 'tiap_bulan'");
    echo " COLUMN_ADDED";
}
