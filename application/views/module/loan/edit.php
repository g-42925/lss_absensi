<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Tambah Data</h5>
    </div>
    <div class="card">
      <form class="card-body" action="<?=base_url('loan/edit_proses/').$id ?>" method="POST">
        <?php if($failed): ?>
          <?=$this->session->flashdata('message');?>
        <?php endif; ?>
        <div class="row g-3">
          <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12">
            <label class="form-label">Loan Id<i class="text-danger">*</i></label>
            <input type="text" value="<?= $data['loan_id'] ?>" class="form-control" name="loan_id" readonly />
          </div>
          <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12">
            <label class="form-label" for="multicol-country">Employee<i class="text-danger">*</i></label>
            <input type="text" value="<?= $data['employee_id'] ?>" class="form-control" name="employee_id" readonly />
          </div>
          <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12">
            <label class="form-label">Amount<i class="text-danger">*</i></label>
            <input type="text" class="form-control" name="amount" value="<?= $data['amount'] ?>" readonly />
          </div>
          <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12">
            <label class="form-label">Paid Amount<i class="text-danger">*</i></label>
            <input type="text" class="form-control" name="paid_amount" value="<?= $data['paid_amount'] ?>" />
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
