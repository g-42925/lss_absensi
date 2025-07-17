<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title"><?=$namalabel;?></h5>
      <div class="text-start mb-2">
        <a href="javascript:window.history.back();" class="btn btn-label-secondary btn-sm me-1"><i class="ti ti-chevron-left me-md-1"></i> Kembali</a>
      </div>
      <div class="row">
        <div class="col-xl-3 col-lg-4 col-md-5 col-sm-6 col-xs-5">
          <label class="form-label">Tanggal Mulai</label>
            <input type="text" class="form-control filtertglrkp" placeholder="YYYY-MM-DD" value="<?=$tglawal;?>" id="flatpickr-date" />
        </div>
        <div class="col-xl-4 col-lg-4 col-md-5 col-sm-6 col-xs-7">
          <label class="form-label">Akhir</label>
          <div class="input-group">
            <input type="text" class="form-control filtertglrkp2" placeholder="YYYY-MM-DD" id="flatpickr-date2" value="<?=$tglakhir;?>" />
            <a href="javascript:filtertglRkp();" class="input-group-text btn btn-outline-primary">Terapkan</a>
          </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-2 col-sm-12">
          <label class="form-label">&nbsp;</label>
          <a href="<?=base_url('attendance_record/download_laporan_detail/'.$id.'/'.$tglawal.'/'.$tglakhir);?>" class="form-control btn btn-outline-primary">Download</a>
        </div>
      </div>
    </div>
    <div class="card-datatable table-responsive">
      <?=$this->session->flashdata('message');?>
      <table class="table border-top" id="dataTableatt">
        <thead>
          <tr>
            <th>Tanggal</th>
            <th class="w-s-n">J.Masuk & Keluar</th>
            <th class="w-s-n">Status Kehadiran</th>
            <th class="w-s-n">Absen Masuk</th>
            <th>Istirahat</th>
            <th>S.Istirahat</th>
            <th class="w-s-n">Absen Keluar</th>
            <th>T.Kerja</th>
            <!-- <th>Lembur</th> -->
          </tr>
        </thead>
        <tbody>
          <?php 
            $no=1; foreach ($datas as $row) : 

            if ($row['is_status']=='ts') {
              $bgs = '<span class="btn btn-label-secondary btn-sm ft-11">Belum ada status</span>';
            }else if ($row['is_status']=='th') {
              $bgs = '<span class="btn btn-label-danger btn-sm ft-11">Tidak hadir</span>';
            }else if ($row['is_status']=='hhk') {
              $bgs = '<span class="btn btn-label-success btn-sm ft-11">Hadir</span>';
            }else if ($row['is_status']=='hbhk') {
              $bgs = '<span class="btn btn-label-info btn-sm ft-11">Hadir dihari libur</span>';
            }else if ($row['is_status']=='s') {
              $bgs = '<span class="btn btn-label-warning btn-sm ft-11">Sakit</span>';
            }else if ($row['is_status']=='i') {
              $bgs = '<span class="btn btn-label-dark btn-sm ft-11">Izin</span>';
            }else if ($row['is_status']=='c') {
              $bgs = '<span class="btn btn-label-dark btn-sm ft-11">Cuti</span>';
            }else if ($row['is_status']=='cb') {
              $bgs = '<span class="btn btn-label-dark btn-sm ft-11">Cuti Bersama</span>';
            }else if ($row['is_status']=='csh') {
              $bgs = '<span class="btn btn-label-dark btn-sm ft-11">Cuti Setengah Hari</span>';
            }else if ($row['is_status']=='ct') {
              $bgs = '<span class="btn btn-label-dark btn-sm ft-11">Cuti Tahunan</span>';
            }else if ($row['is_status']=='l') {
              $bgs = '<span class="btn btn-label-dark btn-sm ft-11">Libur</span>';
            }else if ($row['is_status']=='tl') {
              $bgs = '<span class="btn btn-label-primary btn-sm ft-11">Tugas Luar</span>';
            }else{
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
            <td class="w-s-n"><?=indo($row['tanggal']);?></td>
            <td class="font-weight-500 w-s-n">
              <?php if ($row['j_masuk']!='' && $row['j_pulang']!='') { ?>
                <span class="text-success"><?=$row['j_masuk'];?> s/d <?=$row['j_pulang'];?></span><br/>
                <?php if ($row['j_toleransi']!=0) { ?>
                <span class="small text-primary" data-toggle="tooltip" title="Toleransi Terlambat">
                  <?=$row['j_toleransi'];?> Menit
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
            <td>
              <?=$bgs;?>
            </td>
            <td class="<?=$clrjmasuk;?>">
              <?=$row['jam_masuk'];?>
              <?=$clrjmasuklbl;?>
            </td>
            <td><?=$row['jam_istirahat'];?></td>
            <td><?=$row['jam_sistirahat'];?></td>
            <td class="<?=$clrjkeluar;?>">
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
            <td><?=$totaljam;?></td>
            <!-- <td style="white-space: nowrap;">
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
    </div>
  </div>
</div>
<!-- / Content -->

<script type="text/javascript">
  function filtertglRkp(){
    var valx = $('.filtertglrkp').val();
    var valx2 = $('.filtertglrkp2').val();
    window.location.href='<?=base_url('attendance_record/detail/'.$id.'/');?>'+valx+'/'+valx2;
  }

  $(document).ready(function () {
    $('#flatpickr-date2').flatpickr({
      maxDate: "<?=$today;?>"
    });
    $('#flatpickr-date').flatpickr({
      maxDate: "<?=$today;?>"
    });
  });
</script>