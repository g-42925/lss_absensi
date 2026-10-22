<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Tambah Data</h5>
    </div>
    <div class="card">
      <form class="card-body" action="<?=base_url('karyawan/mutation/proses');?>" method="POST">
        <?php if ($failed == 1): ?>
          <?=$this->session->flashdata('message');?>
        <?php endif; ?>
        <div class="row g-3">
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label" for="multicol-country">Perusahaan<i class="text-danger">*</i></label>
            <select class="select2 form-select" name="target" required>
              <?php foreach($companies as $c): ?>
                <option value="<?= $c['id'] ?>"><?= $c['company_name'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label" for="multicol-country">Karyawans<i class="text-danger">*</i></label>
             <select class="select2 select2s form-select" id="my-select-kary" name="idp[]" required multiple>
              <?php foreach ($employees as $row) : ?>
              <option value="<?=$row['pegawai_id'];?>" data-foo="<?=$row['id_pegawai'];?>"><?=$row['nama_pegawai'];?></option>
              <?php endforeach; ?>
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

<script type="text/javascript">
  $(document).ready(function () {

    $("#my-select-kary").select2();

  });

 
</script>