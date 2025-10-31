<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title"><?=$namalabel;?></h5>
    </div>
  </div>
  <form method="post" action="<?php echo site_url('holiday/add_proses'); ?>" class="row mt-3 g-3">
    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
      <label class="form-label">Tanggal<i class="text-danger">*</i></label>
      <input type="text" class="form-control" name="start" placeholder="YYYY-MM-DD" id="flatpickr-date" required />
    </div>
    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
      <label class="form-label">Sampai Tanggal<i class="text-danger">*</i></label>
      <input type="text" class="form-control" name="end" placeholder="YYYY-MM-DD" id="flatpickr-date2" required />
    </div>
    <div class="col-xl-3 col-lg-4 col-md-5 col-sm-6 col-xs-5 input-group">
      <input type="text" name="note" class="form-control" placeholder="Keterangan" />
    </div>
    <div class="col-xl-3 col-lg-4 col-md-5 col-sm-6 col-xs-5 input-group">
      <button type="submit" class="input-group-text btn btn-outline-primary">Tambah</button>
    </div>
  </form>
</div>

<script type="text/javascript">
  $(document).ready(function () {
    $('#flatpickr-date').flatpickr({});
    $('#flatpickr-date2').flatpickr({});
  });

</script>
