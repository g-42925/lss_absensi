<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Rekap_Gaji_".date('Y-m-d').".xls");
header("Pragma: no-cache");
header("Expires: 0");

$all_plus = [];
$all_minus = [];

foreach($employees as $emp){
    $plus_list = isset($emp['plus']) ? $emp['plus'] : [];
    foreach($plus_list as $p){
        if(!in_array($p['name'], $all_plus)){
            $all_plus[] = $p['name'];
        }
    }
    
    $minus_list = isset($emp['minus']) ? $emp['minus'] : [];
    foreach($minus_list as $m){
        if(!in_array($m['name'], $all_minus)){
            $all_minus[] = $m['name'];
        }
    }
}

$monthName = "Bulan ".$filter;
if(isset($months)) {
    foreach($months as $m) {
        if($m['key'] == $filter) {
            $monthName = ucfirst($m['month']);
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        table { border-collapse: collapse; width: 100%; font-family: sans-serif; }
        th, td { border: 1px solid black; padding: 5px; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; }
        .text { mso-number-format:"\@"; }
        .num { mso-number-format:"\#\,\#\#0"; }
    </style>
</head>
<body>
    <h3>Data Gaji Karyawan - Periode <?= $monthName ?> <?= date('Y') ?> (GrandTotal : <?= $thpGrandTotal ?>)</h3>
    <table>
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Nama Karyawan</th>
                <th rowspan="2">ID Pegawai</th>
                <th rowspan="2">Status</th>
                <th rowspan="2">Nik</th>
                <th rowspan="2">Alamat</th>
                <th rowspan="2">Mulai Kontrak</th>
                <th rowspan="2">Selesai Kontrak</th>
                <th rowspan="2">Gaji Pokok</th>
                <th colspan="<?= count($all_plus) + 1 ?>">Pendapatan</th>
                <th rowspan="2">Total Pendapatan</th>
                <?php if(count($all_minus) > 0): ?>
                    <th colspan="<?= count($all_minus) ?>">Potongan</th>
                <?php endif; ?>
                <th rowspan="2">Total Potongan</th>
                <th rowspan="2">THP (Take Home Pay)</th>
            </tr>
            <tr>
                <th>Kehadiran</th>
                <?php foreach($all_plus as $plus_name): ?>
                    <th><?= $plus_name ?></th>
                <?php endforeach; ?>
                <?php foreach($all_minus as $minus_name): ?>
                    <th><?= $minus_name ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach($employees as $index => $emp): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= $emp['nama_pegawai'] ?></td>
                    <td class="text"><?= $emp['id_pegawai'] ?></td>
                    <td><?= $emp['married'] ?></td>
                    <td class="text"><?= $emp['nik'] ?></td>
                    <td><?= $emp['address'] ?></td>
                    <?php if($emp['status_pegawai'] == 'contract'): ?>
                        <td><?= $emp['contract_start_date'] ?></td>
                        <td><?= $emp['contract_end_date'] ?></td>
                    <?php else: ?>
                        <td>-</td>
                        <td>-</td>
                    <?php endif; ?>
                    <td class="num"><?= $emp['salary'] ?></td>
                    <td class="num"><?= isset($emp['income']) ? $emp['income'] : 0 ?></td>
                    <?php 
                        foreach($all_plus as $plus_name){
                            $val = 0;
                            $plus_list = isset($emp['plus']) ? $emp['plus'] : [];
                            foreach($plus_list as $p){
                                if($p['name'] == $plus_name){
                                    $val = $p['value'];
                                    break;
                                }
                            }
                            echo "<td class=\"num\">{$val}</td>";
                        }
                    ?>
                    <td class="num"><?= $emp['totalPlus'] ?></td>

                    <?php 
                        foreach($all_minus as $minus_name){
                            $val = 0;
                            $minus_list = isset($emp['minus']) ? $emp['minus'] : [];
                            foreach($minus_list as $m){
                                if($m['name'] == $minus_name){
                                    $val = $m['value'];
                                    break;
                                }
                            }
                            echo "<td class=\"num\">{$val}</td>";
                        }
                    ?>
                    <td class="num"><?= $emp['totalMinus'] ?></td>
                    <td class="num"><?= round($emp['thp'], 0) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
