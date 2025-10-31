<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Pengaturan jadwal shift</h5>
    </div>
    <div class="card">
      <form class="card-body" action="<?= base_url('patterns_work/shift_set_proses').'/'.$id; ?>" method="POST">
        <?php if ($failed == 1): ?>
          <?=$this->session->flashdata('message');?>
        <?php endif; ?>
        
        <div class="row g-3">
          <?php foreach($schedule as $s) : ?>

            <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
              <label class="form-label">Jadwal<i class="text-danger">*</i></label>
              <input type="hidden" class="form-control" name="schedules[]" value="<?= $s['shift_detail_id'] ?>"/>
              <input type="text" class="form-control" value="<?= $s['name'] ?>" readonly />
            </div>

            <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
              <label class="form-label" for="multicol-country">Pegawai<i class="text-danger">*</i></label>
                <select class="select2 select2s form-select" name="<?= $s['shift_detail_id'] ?>-employees[]" multiple>
                  <?php foreach($employees as $emp): ?>
                    <option <?= in_array($emp['pegawai_id'],$s['employees']) ? 'selected':'' ?> value="<?= $emp['pegawai_id'] ?>" data-foo=""><?= $emp['nama_pegawai'] ?></option>
                  <?php endforeach; ?>
                </select>
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