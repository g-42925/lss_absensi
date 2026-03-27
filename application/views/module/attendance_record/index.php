<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom flex flex-col gap-3">
      <form method="get" action="<?= base_url().'/attendance_record/filter' ?>" class="flex flex-row w-full gap-3">
        <input value="<?= $from == '' ? date('Y-m-d') : $from ?>" name="from" value="<?= date('Y-m-01') ?>" name="date" type="date" class="w-full p-3 rounded-md border-2 border-black"/>
        <input value="<?= $to == '' ? date('Y-m-d') : $to ?>" name="to" value="<?= $to == '' ? date('Y-m-d') : $to ?>" name="date" type="date" class="w-full p-3 rounded-md border-2 border-black"/>
        <button class="bg-black text-white p-3 rounded-md">search</button>
      </form>
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
            <th>Cuti setengah hari</th> <!-- i/s -->
            <th>Izin/Sakit</th> <!-- i/s -->
            <th>Sisa Cuti</th> <!-- c -->
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
            <td><?=$row['csh'];?></td>
            <td><?=$row['lL'];?></td>
            <td><?=$row['jumlah_cuti'];?></td>
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