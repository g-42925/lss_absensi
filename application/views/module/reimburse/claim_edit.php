<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Edit Claim</h5>
    </div>
    <div class="card">
      <form class="card-body" action="<?=base_url('reimburse/claim_edit_proccess/').$reimburseId.'/'.$claimId ?>" method="POST">
        <?php if ($failed): ?>
          <?=$this->session->flashdata('message');?>
        <?php endif; ?>
        <div class="row g-3">
          <div class="col-xl-12 col-md-12 col-sm-12">
            <label class="form-label">Reimburse Claim Id<i class="text-danger">*</i></label>
            <input value="<?= $data['reimburse_claim_id'] ?>" id="reimburseClaimId" readonly type="text" class="form-control" name="reimburseClaimId" autocomplete="off" />
          </div>

          <div class="col-xl-12 col-md-12 col-sm-12">
            <label class="form-label">Claim By<i class="text-danger">*</i></label>
            <input value="<?= $data['employee_id'] ?>" readonly type="text" class="form-control" name="employeeId" autocomplete="off" />
          </div>

          <div class="col-xl-12 col-md-12 col-sm-12">
            <label class="form-label">Reimburse Name<i class="text-danger">*</i></label>
            <input value="<?= $data['reimburse_name'] ?>" readonly type="text" class="form-control" name="reimburseName" autocomplete="off" />
          </div>

          <div class="col-xl-12 col-md-12 col-sm-12">
            <label class="form-label">Reimburse Name<i class="text-danger">*</i></label>
            <input value="<?= $data['value'] ?>" readonly type="text" class="form-control" name="value" autocomplete="off" />
          </div>

          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label" for="multicol-country">Status<i class="text-danger">*</i></label>
            <select class="select2 form-select" name="status" required>
              <option value="pending" <?= $data['status']  == 'pending' ? 'selected':''?>>Pending</option>
              <option value="approved" <?= $data['status']  == 'approved' ? 'selected':''?>>Approved</option>
              <option value="rejected" <?= $data['status']  == 'rejected' ? 'selected':''?>>Rejected</option>
            </select>
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