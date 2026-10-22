<div class="container-xxl flex-grow container-p-y">
	<div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Edit Data</h5>
    </div>
  </div>
	<div class="card">
		<?php if ($failed): ?>
       <?= $this->session->flashdata('message'); ?>
    <?php endif; ?>
    <form method="post" class="card-body" action="<?= base_url('karyawan/timework/edit_proses/'.$edit['pegawai_id']) ?>">
			<div class="row g-3">
        <div class="col-xl-8 col-md-8 col-sm-7 col-xs-7">
          <label class="form-label">Id Karyawan<i class="text-danger">*</i></label>
          <input readonly type="text" class="form-control" value="<?= $edit['pegawai_id'] ?>" />
				  <input name="selected_pattern" class="form-control" id="selected_pattern" value="<?= $edit['pola_kerja_id'] ?>" />
				</div>
				<div class="col-xl-6 col-md-6 col-sm-6">
					<label class="form-label w-100">
						 Pola kerja<i class="text-danger">*</i>
          </label>
					<select name="pola" onchange="setSelectedPattern(this)" class="select2 form-select">
            <?php foreach ($patterns as $p) : ?>
              <option value="<?= $p['pola_kerja_id'] ?>">
								<?=$p['nama_pola'];?>
							</option>
            <?php endforeach; ?>
					</select>
        </div>
				<div class="col-xl-3 col-lg-4 col-md-5 col-sm-6 col-xs-5">
          <label class="form-label">Tanggal Mulai Berlaku</label>
          <input name="tglmulai" value="<?= $edit['mulai_berlaku_tanggal'] ?>" type="text" class="form-control filtertglrkp" placeholder="YYYY-MM-DD" id="flatpickr-date" />
        </div>
      </div>
			<div class="col-xl-8 col-md-8 col-sm-7 col-xs-7">
        <label class="form-label">Dari Hari ke<i class="text-danger">*</i></label>
        <input name="harike" type="text" class="form-control" value="<?= $edit['dari_hari_ke'] ?>" />
			</div>
      <div class="pt-5 text-end">
        <a href="javascript:window.history.back();" class="btn btn-label-secondary me-sm-3 me-1 waves-effect">Batal</a>
        <button type="submit" class="btn btn-primary waves-effect waves-light">Simpan Data</button>
      </div>
    </form>
  </div>
</div>

<script>
	function setSelectedPattern(t){
		var selectedPatternField = document.getElementById('selected_pattern')
		selectedPatternField.value = t.value
	}
</script>