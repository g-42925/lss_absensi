<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Edit Data</h5>
    </div>
    <div class="card">
      <form class="card-body" action="<?=base_url('except/edit_proses/').$id ?>" method="POST">
        <?php if($failed): ?>
          <?=$this->session->flashdata('message');?>
        <?php endif; ?>
        <div class="row g-3">
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label">Exception Id<i class="text-danger">*</i></label>
            <input type="text" class="form-control" name="exception_id" readonly value="<?=$data['id'];?>" placeholder="..." required />
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label">Employee Id<i class="text-danger">*</i></label>
            <input type="text" class="form-control" name="employee_id" readonly value="<?=$data['employee_id'];?>" placeholder="..." required />
          </div>
          <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
            <label class="form-label">Tanggal Pengajuan<i class="text-danger">*</i></label>
            <input type="text" class="form-control" name="date" value="<?=$data['date'] ?>" placeholder="YYYY-MM-DD" id="flatpickr-date" required />
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label">Reason</label>
            <input type="text" class="form-control" name="reason" readonly value="<?=$data['reason'];?>" readonly />
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label" for="multicol-country">Status<i class="text-danger">*</i></label>
            <select class="select2 form-select" name="status" required>
              <option value="0" <?= $data['status'] == "0" ? 'selected':'' ?>>Pending</option> 
              <option value="1" <?= $data['status'] == "1" ? 'selected':'' ?>>Approved</option>
              <option value="2" <?= $data['status'] == "2" ? 'selected':'' ?>>Rejected</option>
            </select>
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label" for="multicol-country">Considered As Alpha<i class="text-danger">*</i></label>
            <select class="select2 form-select" name="htu" required>
              <option value="0" <?= $data['htu'] == "0" ? 'selected':'' ?>>No</option> 
              <option value="1" <?= $data['htu'] == "1" ? 'selected':'' ?>>Yes</option>
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