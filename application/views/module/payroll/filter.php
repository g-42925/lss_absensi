<div class="container-xxl flex-grow-1 container-p-y">
  <form method="post" action="<?php echo site_url('payroll/check/').$id ?>" class="row mt-3 g-3">
    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
      <label class="form-label">Tanggal<i class="text-danger">*</i></label>
      <input type="text" class="form-control" name="start" placeholder="YYYY-MM-DD" id="flatpickr-date" required />
    </div>
    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
      <label class="form-label">Sampai Tanggal<i class="text-danger">*</i></label>
      <input type="text" class="form-control" name="end" placeholder="YYYY-MM-DD" id="flatpickr-date2" required />
    </div>
    <div class="flex flex-row gap-3 ">
    <div class="flex-1 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6 input-group">
      <button type="submit" class="input-group-text btn btn-outline-primary w-full">Periksa</button>
    </div>
    <div class="flex-1 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6 input-group">
      <button type="submit" formaction="<?= site_url('payroll/accumulationV2/').$id ?>" class="input-group-text btn btn-outline-primary w-full">Ringkasan</button>
    </div>    
    </div>
  </form>
</div>
