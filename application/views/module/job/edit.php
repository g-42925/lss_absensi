<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Tambah Data</h5>
    </div>
    <div class="card">
      <form class="card-body" action="<?= base_url('job/edit_proccess/').$jobId ?>" method="POST">
        <?php if ($failed == 1): ?>
          <?=$this->session->flashdata('message');?>
        <?php endif; ?>
        <div class="row g-3">
          <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12">
            <label class="form-label" for="multicol-country">Posisi<i class="text-danger">*</i></label>
            <select class="select2 form-select" name="position" required>
              <?php foreach($positions as $c): ?>
                <option <?= $job['position_id'] == $c['id'] ? 'selected':'' ?> value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label">Accepted for<i class="text-danger">*</i></label>
            <input type="text" class="form-control" name="amt" value="<?= $job['accepted_for'] ?>" required />
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label" for="multicol-country">Closed<i class="text-danger">*</i></label>
            <select class="select2 form-select" name="closed" required>
              <option <?= $job['closed'] == 0 ? 'selected':'' ?> value="0">False</option>
              <option <?= $job['closed'] == 1 ? 'selected':'' ?> value="1">True</option>
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