<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Tambah Data</h5>
    </div>
    <div class="card">
      <form class="card-body" action="<?=base_url('karyawan/position/edit_proccess').'/'.$id ?>" method="POST">
        <?php if ($failed == 1): ?>
          <?=$this->session->flashdata('message');?>
        <?php endif; ?>
        <div class="row g-3">

        <div class="row g-3">
          <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12">
            <label class="form-label">Nama<i class="text-danger">*</i></label>
            <input type="text" value="<?= $current["name"] ?>" class="form-control" name="name" placeholder="..." required />
          </div>
          <div class="w-full">
            <label class="form-label" for="multicol-country">Parent<i class="text-danger">*</i></label>
            <select class="form-select" name="parent">
                <option value="0" <?= $current['parent'] == "0" ? 'selected':'' ?> >No Parent</option>
                <?php foreach ($position as $p): ?>
                  <option <?= $p['parent'] == $current['parent'] ? 'selected':'' ?> value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
                <?php endforeach; ?>
              </select>
          </div>
        </div>

        <div>
          <div class="row g-3" id="parent"></div>
        </div>

        </div>

        
        <div class="pt-5 text-end">
          <a href="javascript:window.history.back();" class="btn btn-label-secondary me-sm-3 me-1">Batal</a>
          <button type="submit" class="btn btn-primary">Simpan Data</button>
        </div>
      </form>

      <div class="row g-3 hidden" id="target">
        <div class="w-1/2">
          <label class="form-label" for="multicol-country">Lokasi<i class="text-danger">*</i></label>
          <select class="form-select" name="location[]">
            <option value="">-- Choose --</option>
            <?php foreach ($location as $l): ?>
              <option value="<?= $l['lokasi_id'] ?>"><?= $l['nama_lokasi'] ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="w-1/2">
          <label class="form-label">Target (%)<i class="text-danger">*</i></label>
          <input type="text" class="form-control" name="target[]" placeholder="..." required />
        </div>
      </div>
    </div>
  </div>
</div>