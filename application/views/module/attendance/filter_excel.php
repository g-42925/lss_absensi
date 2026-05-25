<?php 
$date = $date ?? '';
$until = $until ?? '';
$datas = $datas ?? [];

$nama_file = 'rekap_kehadiran-tanggal-'.$date.($until ? '-sd-'.$until : '');
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=".$nama_file.".xls");
header("Pragma: no-cache");
header("Expires: 0");

$judul_tanggal = (function_exists('indolengkap') && $date) ? indolengkap($date) : $date;
if ($until) {
    if(function_exists('indolengkap')){
        $judul_tanggal .= ' s/d '.indolengkap($until);
    } else {
        $judul_tanggal .= ' s/d '.$until;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Export Excel</title>
    <style type="text/css">
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid black;
            padding: 5px;
        }
        thead th {
            line-height: 25px; 
            height: 25px;
            vertical-align: middle;
            background-color: #418AD4 !important;
            color: #FFFFFF !important;
            font-weight: bold;
        }
        tbody td {
            line-height: 30px; 
            height: 30px;
            vertical-align: middle;
        }
    </style>
</head>
<body>

<p>&nbsp;</p>

<p style="font-weight: bold; font-size: 14pt;">
    Rekap Kehadiran Filter : <?= $judul_tanggal ?>
</p>

<table border="1" style="border-collapse: collapse; width: 100%;">
    <thead>
        <tr>
            <th style="text-align: center; background-color: #418AD4; color: #FFFFFF; font-weight: bold; border: 1px solid black;">Tanggal</th>
            <th style="text-align: center; background-color: #418AD4; color: #FFFFFF; font-weight: bold; border: 1px solid black;">Nama</th>
            <th style="text-align: center; background-color: #418AD4; color: #FFFFFF; font-weight: bold; border: 1px solid black;">Status</th>
            <th style="text-align: center; background-color: #418AD4; color: #FFFFFF; font-weight: bold; border: 1px solid black;">Jam Masuk</th>
            <th style="text-align: center; background-color: #418AD4; color: #FFFFFF; font-weight: bold; border: 1px solid black;">Istirahat</th>
            <th style="text-align: center; background-color: #418AD4; color: #FFFFFF; font-weight: bold; border: 1px solid black;">S.Istirahat</th>
            <th style="text-align: center; background-color: #418AD4; color: #FFFFFF; font-weight: bold; border: 1px solid black;">Jam Keluar</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($datas as $row) : 
            $status_text = '';
            if ($row['is_status']=='ts') $status_text = 'Belum ada status [TS]';
            else if ($row['is_status']=='on duty') $status_text = 'Bertugas di luar';
            else if ($row['is_status']=='alpha-2') $status_text = 'Alpha-2';
            else if ($row['is_status']=='alpha-1') $status_text = 'Alpha-1';
            else if ($row['is_status']=='alpha-0') $status_text = 'Alpha-0';
            else if ($row['is_status']=='off') $status_text = 'Off';
            else if ($row['is_status']=='free') $status_text = 'Off';
            else if ($row['is_status']=='th') $status_text = 'Tidak hadir [TH]';
            else if ($row['is_status']=='hhk') $status_text = 'Hadir';
            else if ($row['is_status']=='hbhk') $status_text = 'Hadir bukan dihari kerja [HBHK]';
            else if ($row['is_status']=='s') $status_text = 'Sakit [S]';
            else if ($row['is_status']=='i') $status_text = 'Izin [I]';
            else if ($row['is_status']=='c') $status_text = 'Cuti [C]';
            else if ($row['is_status']=='cb') $status_text = 'Cuti bersama [CB]';
            else if ($row['is_status']=='ct') $status_text = 'Cuti tahunan [CT]';
            else if ($row['is_status']=='csh') $status_text = 'Cuti setengah hari [CSH]';
            else if ($row['is_status']=='l') $status_text = 'Libur [L]';
            else if ($row['is_status']=='tl') $status_text = 'Tugas Luar [TL]';
            else $status_text = $row['is_status'];
        ?>
        <tr>
            <td align="center" style="border: 1px solid black;"><?= $row['tanggal_absen'] ?></td>
            <td align="center" style="border: 1px solid black; <?= $row['isLate'] == '1' ? 'color: red;' : '' ?>"><?= $row['nama_pegawai'] ?></td>
            <td align="center" style="border: 1px solid black;"><?= $status_text ?></td>
            <td align="center" style="border: 1px solid black; mso-number-format:'\@';"><?= $row['jam_masuk'] ?></td>
            <td align="center" style="border: 1px solid black; mso-number-format:'\@';"><?= $row['jam_istirahat'] ?></td>
            <td align="center" style="border: 1px solid black; mso-number-format:'\@';"><?= $row['jam_sistirahat'] ?></td>
            <td align="center" style="border: 1px solid black; mso-number-format:'\@';"><?= $row['jam_keluar'] ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
