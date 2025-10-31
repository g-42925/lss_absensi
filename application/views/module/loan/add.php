<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Tambah Data</h5>
    </div>
    <div class="card">
      <form class="card-body" action="<?=base_url('loan/add_proses/') ?>" method="POST">
        <?php if($failed): ?>
          <?=$this->session->flashdata('message');?>
        <?php endif; ?>
        <div class="row g-3">
          <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12">
            <label class="form-label">Loan Id<i class="text-danger">*</i></label>
            <input type="text" id="loanId" class="form-control" name="loan_id" readonly />
          </div>
          <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12">
            <label class="form-label" for="multicol-country">Employee<i class="text-danger">*</i></label>
            <select class="select2 form-select" name="employee_id" required>
              <?php foreach ($data as $e): ?>
                <option value="<?= $e['pegawai_id'] ?>"><?= $e['nama_pegawai'] ?> (<?= $e['pegawai_id'] ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12">
            <label class="form-label">Amount<i class="text-danger">*</i></label>
            <input type="text" class="form-control" name="amount" value="0" />
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

<script type="text/javascript">
  $(document).ready(function () {
    const idField = document.getElementById('loanId');
    
    loanId.value = Date.now()
  });


</script>