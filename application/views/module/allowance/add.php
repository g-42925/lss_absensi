<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Tambah Allowance</h5>
    </div>
    <div class="card">
      <form class="card-body" action="<?=base_url('allowance/daily_add_proses/');?>" method="POST">
        <?php if ($failed): ?>
          <?=$this->session->flashdata('message');?>
        <?php endif; ?>
        <div class="row g-3">
          <div class="col-xl-12 col-md-12 col-sm-12">
            <label class="form-label">Allowance Id<i class="text-danger">*</i></label>
            <input id="allowanceId" readonly type="text" class="form-control" name="id" autocomplete="off" placeholder="..." required />
          </div>
          <div class="col-xl-12 col-md-12 col-sm-12">
            <label class="form-label">Allowance Name<i class="text-danger">*</i></label>
            <input type="text" class="form-control" name="name" autocomplete="off" required />
          </div>
          <div class="flex flex-col gap-3">
            <label class="block text-gray-600 font-medium md:w-1/3">
              Based on attendance <span class="text-red-500">*</span>
            </label>
            <div class="mt-1 md:mt-0 md:w-2/3 space-x-6">
              <label class="inline-flex items-center">
                <input type="radio" name="boa" value="1" class="form-radio text-blue-600">
                <span class="ml-2 text-gray-700">Yes</span>
              </label>
              <label class="inline-flex items-center">
                <input checked type="radio" name="boa" value="0" class="form-radio text-blue-600">
                <span class="ml-2 text-gray-700">No</span>
              </label>
             </div>
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
    const idField = document.getElementById('allowanceId');
    
    idField.value = Date.now()
  });


</script>