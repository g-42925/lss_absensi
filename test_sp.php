<?php
$mysqli = new mysqli("localhost", "root", "", "lss_absensi");
if ($mysqli->connect_error) { die("Connection failed: " . $mysqli->connect_error); }
$res = $mysqli->query("SELECT id, company_name, sp_deduction_policy FROM companies");
while($row = $res->fetch_assoc()) { print_r($row); }
$res = $mysqli->query("SELECT id, employeeId, penalty, date FROM warning");
while($row = $res->fetch_assoc()) { print_r($row); }
