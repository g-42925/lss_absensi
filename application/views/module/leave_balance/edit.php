<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title"><?=$namalabel;?></h5>
    </div>
  </div>
  <form method="post" action="<?php echo site_url('leave_balance/edit_proses'); ?>" class="row mt-3 g-3">
    
    <input type="hidden" name="id" value="<?= $data['id'] ?>">

    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
      <label class="form-label">Karyawan<i class="text-danger">*</i></label>
      <select class="form-select select2" id="employee_id_select" disabled required>
        <?php foreach($employees as $emp): ?>
          <?php if($data['employee_id'] == $emp['pegawai_id']): ?>
            <option value="<?= $emp['pegawai_id'] ?>" selected><?= $emp['nama_pegawai'] ?> (<?= $emp['nik'] ?>)</option>
          <?php endif; ?>
        <?php endforeach; ?>
      </select>
      <input type="hidden" name="employee_id" value="<?= $data['employee_id'] ?>">
    </div>

    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
      <label class="form-label">Berlaku Dari<i class="text-danger">*</i></label>
      <input type="text" class="form-control" name="from" placeholder="YYYY-MM-DD" id="flatpickr-date" value="<?= $data['from'] ?>" required />
    </div>

    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
      <label class="form-label">Berlaku Sampai<i class="text-danger">*</i></label>
      <input type="text" class="form-control" name="to" placeholder="YYYY-MM-DD" id="flatpickr-date2" value="<?= $data['to'] ?>" required />
    </div>

    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
      <label class="form-label">Quota Cuti<i class="text-danger">*</i></label>
      <input type="number" step="0.5" class="form-control" name="quota" placeholder="Contoh: 12" value="<?= $data['quota'] ?>" required />
    </div>

    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
      <label class="form-label">Sudah Terpakai</label>
      <input type="number" step="0.5" class="form-control" name="used" placeholder="Contoh: 0" value="<?= $data['used'] ?>" />
    </div>

    <div class="col-12 mt-4">
      <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      <a href="<?= site_url('leave_balance') ?>" class="btn btn-outline-secondary">Kembali</a>
    </div>
  </form>
</div>

<script type="text/javascript">
  $(document).ready(function () {
    $('#flatpickr-date').flatpickr({});
    $('#flatpickr-date2').flatpickr({});
    $('#employee_id_select').select2();
  });
</script>
