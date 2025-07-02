<?php 

$nama_file = 'rekap-data-request-izin-periode-'.$tgl_awal.'-sd-'.$tgl_akhir;

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=".$nama_file.".xls");

?>

<p style="font-weight: bold;">
Rekap Data Request Izin : <?=indolengkap($tgl_awal);?> s/d <?=indolengkap($tgl_akhir);?>
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
        <th style="text-align: center;background: #418AD4; color: #FFF;">Tanggal</th>
        <th style="text-align: center;background: #418AD4; color: #FFF;">Kategori</th>
        <th style="text-align: center;background: #418AD4; color: #FFF;">Nama Karyawan</th>
        <th style="text-align: center;background: #418AD4; color: #FFF;">J.Masuk</th>
        <th style="text-align: center;background: #418AD4; color: #FFF;">J.Keluar</th>
        <th style="text-align: center;background: #418AD4; color: #FFF;">Status</th>
    </tr>
</thead>
<tbody>
          <?php $no=1; foreach ($all_data as $row) : ?>
          <tr>
            <td align="center"><?=$row['tanggal'];?></td>
            <td align="center"><?=$row['kategori'];?></td>
            <td>
              <?php $nos=1; $pegawai = ''; foreach ($row['pegawai'] as $row2) :
                $jm = '';
                if ($row['tipe']=='csh' || $row['tipe']=='tl') {
                    if($row2['r_absen_masuk']!=''){
                        $jm = '<br><small>Absen '.$row2['r_absen_masuk']."</small>";
                    }
                    if($row2['r_absen_keluar']!=''){
                        $jm = '<br><small>Absen '.$row2['r_absen_masuk']."~".$row2['r_absen_keluar']."</small>";
                    }
                }
                $pegawai = $nos++.'. '.$row2['nama_pegawai'].$jm.'<br>';
              ?>
              <?=$pegawai;?>
              <?php endforeach; ?>
            </td>
            <td align="center"><?=$row['j_masuk'];?></td>
            <td align="center"><?=$row['j_keluar'];?></td>
            <td align="center"><?=$row['status'];?></td>
          </tr>
          <?php $no++; endforeach; ?>
        </tbody>
</table>