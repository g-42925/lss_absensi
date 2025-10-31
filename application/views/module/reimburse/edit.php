<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Edit reimburse</h5>
    </div>
    <div class="card">
      <form class="card-body" action="<?=base_url('reimburse/edit_proccess/').$id ?>" method="POST">
        <?php if ($failed): ?>
          <?=$this->session->flashdata('message');?>
        <?php endif; ?>
        <div class="row g-3">
          <div class="col-xl-12 col-md-12 col-sm-12">
            <label class="form-label">Reimburse Id<i class="text-danger">*</i></label>
            <input value="<?= $data['reimburse_id'] ?>" id="reimburseId" readonly type="text" class="form-control" name="reimburseId" autocomplete="off" />
          </div>
          <div class="col-xl-12 col-md-12 col-sm-12">
            <label class="form-label">Reimburse Name<i class="text-danger">*</i></label>
            <input value="<?= $data['reimburse_name'] ?>" type="text" class="form-control" name="reimburseName" autocomplete="off" required />
          </div>
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