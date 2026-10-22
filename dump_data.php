<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
$mysqli = new mysqli('localhost', 'root', '', 'db_erp');
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
$res = $mysqli->query("SELECT id, company_name, sp_deduction_policy FROM companies");
while($row = $res->fetch_assoc()) { print_r($row); }
$res = $mysqli->query("SELECT id, employeeId, penalty, date FROM warning");
while($row = $res->fetch_assoc()) { print_r($row); }
