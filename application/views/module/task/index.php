<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-datatable table-responsive p-3">
          <div class="col-xl-3 col-lg-4 col-md-5 mb-4">
            <label for="flatpickr-date" class="form-label">Filter by assignment date</label>
            <div class="input-group">
              <input type="text" class="form-control filtertglabsensi" placeholder="YYYY-MM-DD" id="flatpickr-date" />
              <a href="javascript:filtertglAbsensi();" class="input-group-text btn btn-outline-primary">Terapkan</a>
            </div>
          </div>
       <table class="table border-top" id="dataTable">
        <thead>
          <tr class="text-center">
            <th>Employee</th>
            <th>Date</th>
            <th>Description</th>
            <th>Start Photo</th>
            <th>Start Time</th>
            <th>Start Location</th>
            <th>Finish Photo</th>
            <th>Finish Time</th>
            <th>Finish Location</th>
          </tr>
        </thead>
        <tbody>
          <?php $no=1; foreach ($data as $r) : ?>
          <tr class="text-center">
            <td class="text-capitalize"><?= $r['nama_pegawai'] ?> (<?= $r['employee_id'] ?>)</td>
            <td class="text-capitalize"><?= $r['date'];?></td>
            <td class="text-capitalize"><?= $r['description'];?></td>
            <td class="text-capitalize">
              <a target="_blank" href="<?= $r['start_photo'] ?>"><i class="ti ti-photo"></i></a>
            </td>  
            <td class="text-capitalize"><?= $r['start_time'];?></td>
            <td class="text-capitalize">
                
              <a href="https://www.google.com/maps?q=<?= explode("/",$r['start_location'])[0] ?>,<?= explode("/",$r['start_location'])[1] ?>" target="_blank"><i class="ti ti-map-pin"></i></a>
            </td>
            <td class="text-capitalize">
              <a target="_blank" href="<?= $r['finish_photo'] ?>"><i class="ti ti-photo"></i></a>
            </td>  
            <td class="text-capitalize"><?= $r['finish_time'];?></td>
            <td class="text-capitalize">
              <a href="https://www.google.com/maps?q=<?= explode("/",$r['finish_location'])[0] ?>,<?= explode("/",$r['finish_location'])[1] ?>" target="_blank"><i class="ti ti-map-pin"></i></a>
            </td>          
          </tr>
          <?php $no++; endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script type="text/javascript">
  function filtertglAbsensi(){
    var valx = $('.filtertglabsensi').val();
    window.location.href='<?=base_url('task/index/');?>'+valx;
  }
</script>