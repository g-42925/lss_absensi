<?php 

$nama_file = 'rekap_kehadiran-seluruh-pegawai-periode-'.$tgl_awal.'-sd-'.$tgl_akhir;

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=".$nama_file.".xls");

?>

<p>&nbsp;</p>

<p style="font-weight: bold;">
Rekap Kehadiran : <?=indolengkap($tgl_awal);?> s/d <?=indolengkap($tgl_akhir);?>
</p>
<p style="font-weight: bold;">
Rekap Seluruh Pegawai
</p>

<style type="text/css">
thead th {
    line-height: 25px !important; height: 25px !important;
    vertical-align: middle !important;
}

tfoot td {
    line-height: 25px !important; height: 25px !important;
    vertical-align: middle !important;
}

tbody td {
    line-height: 30px !important; height: 30px !important;
    vertical-align: middle !important;
}
</style>

<table class="table" width="100%" border="1">
<thead>			                
    <tr>
        <th style="text-align: center;background: #418AD4; color: #FFF;">Nama Karyawan</th>
        <th style="text-align: center;background: #418AD4; color: #FFF;">Hari Kehadiran</th>
        <th style="text-align: center;background: #418AD4; color: #FFF;">Tidak Hadir</th>
        <th style="text-align: center;background: #418AD4; color: #FFF;">Tugas Luar Kantor</th>
        <th style="text-align: center;background: #418AD4; color: #FFF;">Cuti & Lainnya</th>
        <th style="text-align: center;background: #418AD4; color: #FFF;">Cuti Sakit</th>
        <th style="text-align: center;background: #418AD4; color: #FFF;">Cuti Setengah Hari</th>
        <th style="text-align: center;background: #418AD4; color: #FFF;">Cuti Bersama</th>
        <th style="text-align: center;background: #418AD4; color: #FFF;">Cuti Tahunan</th>
    </tr>
</thead>
<tbody>
          <?php $no=1; foreach ($all_data as $row) : ?>
          <tr>
            <td align="center"><?=$row['nama_pegawai'];?></td>
            <td align="center"><?=$row['hhk'];?></td>
            <td align="center"><?=$row['th'];?></td>
            <td align="center"><?=$row['tl'];?></td>
            <td align="center"><?=$row['c'];?></td>
            <td align="center"><?=$row['s'];?></td>
            <td align="center"><?=$row['csh'];?></td>
            <td align="center"><?=$row['cb'];?></td>
            <td align="center"><?=$row['ct'];?></td>
          </tr>
          <?php $no++; endforeach; ?>
        </tbody>
</table>