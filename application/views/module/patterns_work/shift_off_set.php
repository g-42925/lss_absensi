<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Pengaturan hari libur</h5>
    </div>
    <div class="card">
      <form class="card-body" action="<?= base_url('patterns_work/shift_off_set_proses/').$id ?>" method="POST">
        <?php if ($failed == 1): ?>
          <?=$this->session->flashdata('message');?>
        <?php endif; ?>
        
        <div class="row g-3">
          <?php foreach($employees as $e) : ?>

            <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
              <label class="form-label">Jadwal<i class="text-danger">*</i></label>
              <input type="hidden" class="form-control" name="employees[]" value="<?= $e['pegawai_id'] ?>"/>
              <input type="text" class="form-control" value="<?= $e['nama_pegawai'] ?> (<?= $e['pegawai_id'] ?>)" readonly />
            </div>

            <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
              <label class="form-label" for="multicol-country">Hari<i class="text-danger">*</i></label>
                <select name="day[]" class=" form-select" name="day" required>
                  <option <?= $e['off'] == '0' ? 'selected' :''  ?> value="0">-- Choose --</option>
                  <option <?= $e['off'] == '1' ? 'selected' :''  ?> value="1">Senin</option>
                  <option <?= $e['off'] == '2' ? 'selected' :''  ?> value="2">Selasa</option>
                  <option <?= $e['off'] == '3' ? 'selected' :''  ?> value="3">Rabu</option>
                  <option <?= $e['off'] == '4' ? 'selected' :''  ?> value="4">Kamis</option>
                  <option <?= $e['off'] == '5' ? 'selected' :''  ?> value="5">Jumat</option>
                  <option <?= $e['off'] == '6' ? 'selected' :''  ?> value="6">Sabtu</option>
                  <option <?= $e['off'] == '7' ? 'selected' :''  ?> value="7">Minggu</option>
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