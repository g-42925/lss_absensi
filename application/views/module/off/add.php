<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title"><?=$namalabel;?></h5>
    </div>
  </div>
  <form method="post" action="<?php echo site_url('off/add_proses'); ?>" class="row mt-3 gap-x-3">
    <div class="col-xl-3 col-lg-4 col-md-5 col-sm-6 col-xs-5 input-group">
      <input type="text" name="tanggal" class="form-control" placeholder="YYYY-MM-DD" id="flatpickr-date" />
    </div>
    <div class="col-xl-3 col-lg-4 col-md-5 col-sm-6 col-xs-5 input-group">
      <input type="text" name="keterangan" class="form-control" placeholder="Keterangan" />
    </div>
    <div class="col-xl-3 col-lg-4 col-md-5 col-sm-6 col-xs-5 input-group">
      <button type="submit" class="input-group-text btn btn-outline-primary">Tambah</button>
    </div>
  </form>
    
</div>
