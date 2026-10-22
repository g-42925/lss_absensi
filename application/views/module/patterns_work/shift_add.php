<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Tambah Shift</h5>
    </div>
    <div class="card">
      <form class="card-body" action="<?=base_url('patterns_work/shift_add_proses');?>" method="POST">
        <?php if ($failed): ?>
          <?=$this->session->flashdata('message');?>
        <?php endif; ?>
        <div class="row g-3">
          <div class="col-xl-6 col-md-6 col-sm-6">
            <label class="form-label">Id Shift<i class="text-danger">*</i></label>
            <input id="shiftId" readonly type="text" class="form-control" name="id" autocomplete="off" placeholder="..." required />
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6">
            <label class="form-label">Nama Shift<i class="text-danger">*</i></label>
            <input type="text" class="form-control" name="name" autocomplete="off" required />
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
    const idField = document.getElementById('shiftId');
    
    idField.value = Date.now()
  });


</script>