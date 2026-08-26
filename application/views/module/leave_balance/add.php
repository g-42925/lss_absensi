<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title"><?=$namalabel;?></h5>
    </div>
  </div>
  <form method="post" action="<?php echo site_url('leave_balance/add_proses'); ?>" class="row mt-3 g-3">
    
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-3">
      <label class="form-label">Filter Divisi</label>
      <select class="form-select select2" id="filter_divisi" onchange="window.location.href='<?= site_url('leave_balance/add?div=') ?>' + this.value">
        <option value="all">Semua Karyawan</option>
        <?php foreach ($divisions as $row): ?>
          <option <?= (isset($div) && $div == $row['id']) ? 'selected' : '' ?> value="<?= $row['id']; ?>"><?= $row['division_name']; ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
      <div class="d-flex justify-content-between align-items-center mb-1">
        <label class="form-label mb-0">Karyawan<i class="text-danger">*</i></label>
        <div>
          <button type="button" class="btn btn-outline-primary btn-sm py-1 px-2" style="font-size: 0.75rem;" onclick="selectAll()">Pilih Semua</button>
          <button type="button" class="btn btn-outline-warning btn-sm py-1 px-2" style="font-size: 0.75rem;" onclick="deselectAll()">Hapus Semua</button>
        </div>
      </div>
      <select class="form-select select2" id="employee_id_select" name="employee_id[]" multiple required>
        <?php foreach($employees as $emp): ?>
        <option value="<?= $emp['pegawai_id'] ?>"><?= $emp['nama_pegawai'] ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
      <label class="form-label">Berlaku Dari<i class="text-danger">*</i></label>
      <input type="text" class="form-control" name="from" placeholder="YYYY-MM-DD" id="flatpickr-date" required />
    </div>

    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
      <label class="form-label">Berlaku Sampai<i class="text-danger">*</i></label>
      <input type="text" class="form-control" name="to" placeholder="YYYY-MM-DD" id="flatpickr-date2" required />
    </div>

    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
      <label class="form-label">Quota Cuti<i class="text-danger">*</i></label>
      <input type="number" step="0.5" class="form-control" name="quota" placeholder="Contoh: 12" required />
    </div>

    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
      <label class="form-label">Sudah Terpakai</label>
      <input type="number" step="0.5" class="form-control" name="used" placeholder="Contoh: 0" value="0" />
    </div>

    <div class="col-12 mt-4">
      <button type="submit" class="btn btn-primary">Simpan</button>
      <a href="<?= site_url('leave_balance') ?>" class="btn btn-outline-secondary">Kembali</a>
    </div>
  </form>
</div>

<script type="text/javascript">
  $(document).ready(function () {
    $('#flatpickr-date').flatpickr({});
    $('#flatpickr-date2').flatpickr({});
    $('#employee_id_select').select2({
      placeholder: "Pilih Karyawan",
      allowClear: true
    });
    $('#filter_divisi').select2();
  });

  function selectAll() {
      $("#employee_id_select > option").prop("selected", true);
      $("#employee_id_select").trigger("change");
  }

  function deselectAll() {
      $("#employee_id_select > option").prop("selected", false);
      $("#employee_id_select").trigger("change");
  }
</script>
