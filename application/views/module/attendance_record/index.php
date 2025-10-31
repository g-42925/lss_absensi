<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-2"><?=$namalabel;?></h5>
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
          <a href="<?=base_url('attendance_record/download_laporan/'.$tglawal.'/'.$tglakhir);?>" class="form-control btn btn-outline-primary">Download</a>
        </div>
      </div>
    </div>
    <div class="card-datatable table-responsive">
      <table class="table border-top" id="dataTableatt2">
        <thead>
          <tr>
            <th>Nama&nbsp;Karyawan</th>
            <th>Hari Kehadiran</th> <!-- hhk -->
            <th>Tidak Hadir</th> <!-- alpha-1 / alpha-2 -->
            <th>Tugas&nbsp;Luar Kantor</th> <!-- on duty -->
            <th>Cuti</th> <!-- c -->
            <th>Izin/Sakit</th> <!-- i/s -->
            <!-- <th width="">&nbsp;Action&nbsp;</th> -->
          </tr>
        </thead>
        <tbody>
          <?php $no=1; foreach ($datas as $row) : ?>
          <tr>
            <td><?=$row['nama_pegawai'];?></td>
            <td><?=$row['hHK'];?></td>
            <td><?=$row['alpha'];?></td>
            <td><?=$row['onDuty'];?></td>
            <td><?=$row['c'];?></td>
            <td><?=$row['lL'];?></td>
            <!-- <td>
              <a href="<?=base_url('attendance_record/detail/'.$row['pegawai_id']);?>" class="btn p-1 text-primary">
                Lihat&nbsp;Detail
              </a>
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
    window.location.href='<?=base_url('attendance_record/index/');?>'+valx+'/'+valx2;
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