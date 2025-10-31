<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Edit Allowance</h5>
    </div>
    <div class="card">
      <form class="card-body" action="<?=base_url('allowance/config_process/').$id ?>" method="POST">
        <?php if ($failed): ?>
          <?=$this->session->flashdata('message');?>
        <?php endif; ?>
        <div class="row g-3">
          <?php foreach($data as $a): ?>
            <div class="col-xl-6 col-md-6 col-sm-6">
              <label class="form-label">Allowance Name<i class="text-danger">*</i></label>
              <input  value="<?= $a['allowance_id'] ?>" type="hidden" name="allowance[]" class="form-control" />
              <input  value="<?= $a['name'] ?>" readonly type="text" class="form-control" />
            </div>
            <div class="col-xl-6 col-md-6 col-sm-6">
              <label class="form-label">Allowance Value<i class="text-danger">*</i></label>
              <input name="values[]" value="<?= $a['value'] ?>" type="text" class="form-control" required />
            </div>
          <?php endforeach; ?>
        </div>
        <div class="pt-5 text-end">
          <a href="javascript:window.history.back();" class="btn btn-label-secondary me-sm-3 me-1">Batal</a>
          <button type="submit" class="btn btn-primary">Simpan Data</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- / Content -->