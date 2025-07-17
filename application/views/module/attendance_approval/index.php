<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0"><?=$namalabel;?><br/>
        <small><?=indo($today);?></small>
      </h5>
    </div>
    <div class="card-body">
      <div class="pt-3">
        <div class="row">
          <div class="col-xl-3 col-lg-4 col-md-5 mb-4">
            <label for="flatpickr-date" class="form-label">Filter</label>
            <div class="input-group">
              <input type="text" class="form-control filtertglabsensi" placeholder="YYYY-MM-DD" value="<?=$today;?>" id="flatpickr-date" />
              <a href="javascript:filtertglAbsensi();" class="input-group-text btn btn-outline-primary">Terapkan</a>
            </div>
          </div>
        </div>
        <!-- <div class="btn btn-primary w-100 p-3">
          Untuk menampilkan filter tanggal.

          <?php $day = date('D', strtotime($today));
          $dayList = array('Sun' => 'Minggu', 'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu', 'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu');
          ?>
        </div> -->
      </div>
    </div>
    <div class="card-datatable table-responsive">
      <?=$this->session->flashdata('message');?>
      <table class="table border-top" id="dataTableatt">
        <thead>
          <tr>
            <th>Nama</th>
            <th>Tipe</th>
            <th>Jam</th>
            <th>Status</th>
            <th class="text-right">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php 
            $no=1; foreach ($datas as $row) : 
          ?>
          <tr>
            <td class="w-s-n"><?=$row['nama_pegawai'];?></td>
            <td class="w-s-n"><?=$row['tipe_label'];?></td>
            <td class="w-s-n"><?=$row['jam'];?></td>
            <td class="w-s-n">
              <span class="btn btn-label-<?=$row['statusclr'];?> btn-sm ft-11">
                <?=$row['status'];?>
              </span>
            </td>
            <td align="right">
              <a href="javascript:;" onclick="action_data_att('<?=$row['tipe'];?>','<?=$row['absen_id'];?>','map');" class="btn p-1" data-toggle="tooltip" title="Lihat Map">
                <i class="ti ti-map-pin"></i>
              </a>
              <a href="javascript:;" onclick="action_data_att('<?=$row['tipe'];?>','<?=$row['absen_id'];?>','photo');"class="btn p-1" data-toggle="tooltip" title="Lihat Foto Absen">
                <i class="ti ti-photo"></i>
              </a>
              <a href="javascript:;" onclick="action_data_att('<?=$row['tipe'];?>','<?=$row['absen_id'];?>','catatan');" class="btn p-1" data-toggle="tooltip" title="Lihat Catatan">
                <i class="ti ti-note"></i>
              </a>
              <a href="<?=base_url('attendance_approval/acc/'.$row['tipe'].'/'.$row['absen_id']);?>" class="btn p-1" data-toggle="tooltip" title="Setujui">
                <i class="ti ti-check"></i>
              </a>
              <?php if ($row['action_acc']!='t') { ?>
              <a href="<?=base_url('attendance_approval/reject/'.$row['tipe'].'/'.$row['absen_id']);?>" class="btn p-1" data-toggle="tooltip" title="Tolak">
                <i class="ti ti-x"></i>
              </a>
              <?php } ?>
            </td>
          </tr>
          <?php $no++; endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<!-- / Content -->

<script type="text/javascript">
  $(document).ready(function () {
    $('#flatpickr-date').flatpickr({
      maxDate: "<?=$maxdate;?>"
    });
  });

  function filtertglAbsensi(){
    var valx = $('.filtertglabsensi').val();
    window.location.href='<?=base_url('attendance_approval/index/');?>'+valx;
  }
</script>

