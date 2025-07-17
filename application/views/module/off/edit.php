<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title"><?=$namalabel;?></h5>
    </div>
  </div>
  <form method="post" action="<?php echo site_url('off/edit_proses'); ?>" class="row mt-3 gap-x-3">
    <div class="col-xl-3 col-lg-4 col-md-5 col-sm-6 col-xs-5 input-group">
      <input value="<?=$data['tanggal'];?>" type="text" name="tanggal" class="form-control" placeholder="YYYY-MM-DD" id="flatpickr-date" />
    </div>
    <div class="col-xl-3 col-lg-4 col-md-5 col-sm-6 col-xs-5 input-group">
      <input value="<?=$data['keterangan'];?>" type="text" name="keterangan" class="form-control" placeholder="Keterangan" />
      <input type="hidden" name="id" value="<?=$data['id'];?>" />
    </div>
    <div class="col-xl-3 col-lg-4 col-md-5 col-sm-6 col-xs-5 input-group">
      <button type="submit" class="input-group-text btn btn-outline-primary">Edit</button>
    </div>
  </form>
    
</div>
