<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Tambah Data</h5>
    </div>
    <div class="card">
      <form class="card-body" action="<?=base_url('sliders/add_proses');?>" method="POST" enctype="multipart/form-data">
        <?=$this->session->flashdata('message');?>
        <div class="row g-3">
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label">Tipe<i class="text-danger">*</i></label>
            <select class="select2 form-select" name="tipe" required onchange="checkTipe(this.value)">
              <option value="1">Embed IG Post</option>
              <option value="2">Gambar</option>
            </select>
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label">Status<i class="text-danger">*</i></label>
            <select class="select2 form-select" name="status" required>
              <option value="y">Aktif</option>
              <option value="n">Tidak Aktif</option>
            </select>
          </div>
          <div class="col-xl-12 col-md-12 col-sm-12 mb-2" id="igTipe">
            <label class="form-label">Link IG Post<i class="text-danger">*</i></label>
            <input type="text" class="form-control" name="ilink" placeholder="https://www.instagram.com/p/C-Y9HLwySrq/" />
          </div>
          <div class="col-xl-12 col-md-12 col-sm-12 mb-2 d-none" id="gambarTipe">
            <label class="form-label">Cover<i class="text-danger">*</i></label>
            <input type="file" class="form-control" name="gambar" onChange="showImgfile(this);" />
            <div class="mt-2 small">Format (jpg/png) maksimal 2mb.</div>
            <div class="mt-2">
              <div id="targetfileimg"></div>
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

<script>
  function checkTipe(a){
    if(a==1){
      $('#gambarTipe').addClass('d-none');
      $('#igTipe').removeClass('d-none');
    }else{
      $('#gambarTipe').removeClass('d-none');
      $('#igTipe').addClass('d-none');
    }
  }
</script>