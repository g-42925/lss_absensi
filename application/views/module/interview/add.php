<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Tambah Data</h5>
    </div>
    <div class="card">
      <form class="card-body" action="<?=base_url('interview/add_proccess');?>" method="POST">
        <?php if ($failed == 1): ?>
          <?=$this->session->flashdata('message');?>
        <?php endif; ?>
        <div class="row g-3">
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label" for="multicol-country">Posisi<i class="text-danger">*</i></label>
            <select class="select2 form-select" name="job_id" required>
              <?php foreach($job as $p): ?>
                <option value="<?= $p['job_id'] ?>"><?= $p['name'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label" for="multicol-country">Kandidat<i class="text-danger">*</i></label>
            <select class="select2 form-select" name="candidate_id" required>
              <?php foreach($candidate as $c): ?>
                <option value="<?= $c['candidate_id'] ?>"><?= $c['candidate_name'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <label class="form-label">Tanggal Interview<i class="text-danger">*</i></label>
            <input type="hidden" class="form-control" name="date" placeholder="YYYY-MM-DD" id="flatpickr-date" required />
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
    $('#flatpickr-date').flatpickr({
      minDate: "<?=$mindate;?>",
      maxDate: "<?=$maxdate;?>"
    });

    const idField = document.getElementById('idkar');
    
    idField.value = Date.now()
  });


</script>