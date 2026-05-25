<?php 

$nama_file = 'rekap_kehadiran-'.$user['nama_pegawai'].'-periode-'.$tgl_awal.'-sd-'.$tgl_akhir;

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=".$nama_file.".xls");

?>

<p>&nbsp;</p>

<p style="font-weight: bold;">
Rekap Kehadiran : <?=indolengkap($tgl_awal);?> s/d <?=indolengkap($tgl_akhir);?>
</p>
<p style="font-weight: bold;">
Nama Pegawai : <?=$user['nama_pegawai'];?>
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
        <th style="text-align: center;background: #418AD4; color: #FFF;">No</th>
        <th style="text-align: center;background: #418AD4; color: #FFF;">Tanggal</th>
        <th style="text-align: center;background: #418AD4; color: #FFF;">Jam Masuk & Keluar</th>
        <th style="text-align: center;background: #418AD4; color: #FFF;">Status Kehadiran</th>
        <th style="text-align: center;background: #418AD4; color: #FFF;">Absen Masuk</th>
        <th style="text-align: center;background: #418AD4; color: #FFF;">Istirahat</th>
        <th style="text-align: center;background: #418AD4; color: #FFF;">Selesai Istirahat</th>
        <th style="text-align: center;background: #418AD4; color: #FFF;">Absen Keluar</th>
        <th style="text-align: center;background: #418AD4; color: #FFF;">Total Kerja</th>
        <!-- <th style="text-align: center;background: #418AD4; color: #FFF;">Lembur</th> -->
    </tr>
</thead>
<tbody>
        <?php 
            $statuscount1 = 0;
            $statuscount2 = 0;
            $statuscount3 = 0;
            $statuscount4 = 0;
            $statuscount5 = 0;
            $statuscount6 = 0;
            $statuscount8 = 0;
            $statuscount9 = 0;
            $statuscount7 = 0;
            $statuscount10 = 0;
            $statuscount11 = 0;
            $statuscount12 = 0;
            $no=1; foreach ($all_data as $row) : 

            if ($row['is_status']=='ts') {
                $statuscount1 +=1;
              $bgs = '<span class="btn btn-label-secondary btn-sm ft-11">Belum ada status</span>';
            }else if ($row['is_status']=='th') {
                $statuscount2 +=1;
              $bgs = '<span class="btn btn-label-danger btn-sm ft-11">Tidak hadir</span>';
            }else if ($row['is_status']=='hhk') {
                $statuscount3 +=1;
              $bgs = '<span class="btn btn-label-success btn-sm ft-11">Hadir</span>';
            }else if ($row['is_status']=='hbhk') {
                $statuscount4 +=1;
              $bgs = '<span class="btn btn-label-info btn-sm ft-11">Hadir dihari libur</span>';
            }else if ($row['is_status']=='s') {
                $statuscount5 +=1;
              $bgs = '<span class="btn btn-label-warning btn-sm ft-11">Sakit</span>';
            }else if ($row['is_status']=='i') {
                $statuscount6 +=1;
              $bgs = '<span class="btn btn-label-dark btn-sm ft-11">Izin</span>';
            }else if ($row['is_status']=='c') {
                $statuscount7 +=1;
              $bgs = '<span class="btn btn-label-dark btn-sm ft-11">Cuti</span>';
            }else if ($row['is_status']=='cb') {
                $statuscount8 +=1;
              $bgs = '<span class="btn btn-label-dark btn-sm ft-11">Cuti Bersama</span>';
            }else if ($row['is_status']=='csh') {
                $statuscount9 +=1;
              $bgs = '<span class="btn btn-label-dark btn-sm ft-11">Cuti Setengah Hari</span>';
            }else if ($row['is_status']=='ct') {
                $statuscount10 +=1;
              $bgs = '<span class="btn btn-label-dark btn-sm ft-11">Cuti Tahunan</span>';
            }else if ($row['is_status']=='l') {
                $statuscount11 +=1;
              $bgs = '<span class="btn btn-label-dark btn-sm ft-11">Libur</span>';
            }else if ($row['is_status']=='tl') {
                $statuscount12 +=1;
              $bgs = '<span class="btn btn-label-primary btn-sm ft-11">Tugas Luar</span>';
            }else{
                $statuscount1 +=1;
              $bgs = '<span class="btn btn-label-secondary btn-sm ft-11">Belum ada status</span>';
            }
            $jmasuk = date('H:i', strtotime($row['j_masuk']. ' +'.$row['j_toleransi'].' minutes'));

            if ($row['shift_jam_mulai']!='') {
              $shift = '<div class="btn btn-label-dark btn-sm ft-11">Shift</div>';
            }else{
              $shift = '';
            }


            if ($row['jam_masuk']!='' && $row['jam_keluar']!='') {
              $waktu_awal  = strtotime($row['jam_masuk']);
              $waktu_akhir = strtotime($row['jam_keluar']);
              $diff        = $waktu_akhir - $waktu_awal;
              $totaljam    = floor($diff / (60 * 60));
              $menit       = $diff - $totaljam * (60 * 60);
              $totaljam    = $totaljam."j ". floor( $menit / 60 )."m";
            }else{
              $totaljam = '';
            }

            $clrjmasuklbl = '';
            if ($row['jam_masuk']>$jmasuk) {
              if ($row['j_masuk']!='' && ($row['is_status']=='hhk' || $row['is_status']=='csh')) {
                $clrjmasuk = 'text-danger';
                $clrjmasuklbl = '<br/><span class="small">Terlambat</span>';
              }else{
                $clrjmasuk = '';
              }
            }else{
              $clrjmasuk = '';
            }

            $clrjkeluarlbl = '';
            if ($row['j_pulang']>$row['jam_keluar']) {
              if ($row['jam_keluar']!='') {
                if ($row['j_pulang']!='' && ($row['is_status']=='hhk' || $row['is_status']=='csh')) {
                  $clrjkeluar = 'text-danger';
                  $clrjkeluarlbl = '<br/><span class="small">Lebih awal</span>';
                }else{
                  $clrjkeluar = '';
                }
              }else{
                $clrjkeluar = '';
              }
            }else{
              $clrjkeluar = '';
            }
          ?>
          <tr>
          <td class="w-s-n" align="center"><?=$no;?></td>
            <td class="w-s-n" align="center"><?=indo($row['tanggal']);?></td>
            <td align="center" class="font-weight-500 w-s-n">
              <?php if ($row['j_masuk']!='' && $row['j_pulang']!='') { ?>
                <span class="text-success"><?=$row['j_masuk'];?> s/d <?=$row['j_pulang'];?></span><br/>
                <?php if ($row['j_toleransi']!=0) { ?>
                <span class="small text-primary" data-toggle="tooltip" title="Toleransi Terlambat">
                  Toleransi <?=$row['j_toleransi'];?> Menit
                </span>
                <?php } ?>
              <?php }else{ ?>
                <?php if ($row['mulai_berlaku_tanggal']>$row['tanggal'] && $row['jam_masuk']!='') { ?>
                  <span class="small text-info">Pola Diset mulai<br/><?=$row['mulai_berlaku_tanggal'];?>.</span>
                <?php }else if ($row['mulai_berlaku_tanggal']=='' && $row['jam_masuk']!='') { ?>
                  <span class="small">Pola belum diset</span>
                <?php }else if ($row['mulai_berlaku_tanggal']!='' && $row['jam_masuk']!='') { ?>
                  <span class="small">Saat absen<br/>pola belum diset</span>
                <?php }else{ ?>
                  <span class="small">-</span>
                <?php } ?>
              <?php } ?>
              <?=$shift;?>
            </td>
            <td align="center">
              <?=$bgs;?>
            </td>
            <td class="<?=$clrjmasuk;?>" align="center">
              <?=$row['jam_masuk'];?>
              <?=$clrjmasuklbl;?>
            </td>
            <td align="center"><?=$row['jam_istirahat'];?></td>
            <td align="center"><?=$row['jam_sistirahat'];?></td>
            <td align="center" class="<?=$clrjkeluar;?>">
              <?php if ($row['acc_keluar']=='y') { ?>
              <?=$row['jam_keluar'];?>
              <?=$clrjkeluarlbl;?>
              <?php }else if ($row['acc_keluar']=='t') { ?>
              <?=$row['jam_keluar'];?>
              <div class="ft-11">
                Ditolak diluar jangkauan, 
                <a href="<?=base_url('attendance_approval/index/'.$row['tanggal']);?>"> lihat </a>
              </div>
              <?php }else if ($row['acc_keluar']=='n') { ?>
              <?=$row['jam_keluar'];?>
              <div class="ft-11">Waiting</div>
              <?php } ?>
            </td>
            <td align="center"><?=$totaljam;?></td>
            <!-- <td align="center" style="white-space: nowrap;">
              <?php if ($row['tanggal_lembur']!='') { ?>
              Set : <?=$row['masuk_lembur'];?> s/d <?=$row['keluar_lembur'];?>
              <br/>
              Masuk : <?=$row['absen_masuk'];?> Keluar : <?=$row['absen_keluar'];?>
              <?php } ?>
            </td> -->
          </tr>
        <?php $no++; endforeach; ?>
</tbody>
</table>
<p style="font-weight: bold;">
Belum ada status : <?=$statuscount1;?>
<br>
Libur : <?=$statuscount11;?>
<br>
Tidak hadir : <?=$statuscount2;?>
<br>
Hadir : <?=$statuscount3;?>
<br>
Hadir dihari libur : <?=$statuscount5;?>
<br>
Sakit : <?=$statuscount5;?>
<br>
Izin : <?=$statuscount6;?>
<br>
Cuti : <?=$statuscount7;?>
<br>
Cuti Bersama : <?=$statuscount8;?>
<br>
Cuti Setengah Hari : <?=$statuscount9;?>
<br>
Cuti Tahunan : <?=$statuscount10;?>
<br>
Tugas Luar : <?=$statuscount12;?>
</p>