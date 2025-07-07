<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Tambah Data</h5>
    </div>
    <div class="card">
      <form class="card-body" action="<?=base_url('karyawan/timework/add_proses/'.$karyawan['pegawai_id']);?>" method="POST">
        <?=$this->session->flashdata('message');?>
        <div class="row g-3">
          <div class="col-xl-6 col-md-6 col-sm-6">
            <label class="form-label">Nama Lengkap<i class="text-danger">*</i></label>
            <input type="text" class="form-control btn-light" value="<?=$karyawan['nama_pegawai'];?>" placeholder="..." readonly="" />
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6">
            <label class="form-label w-100" for="multicol-country">
              Pola Kerja<i class="text-danger">*</i>
              <span class="float-right">
                <a href="<?=base_url('patterns_work');?>">Tambah&nbsp;</a>
              </span>
            </label>
            <select class="select2 form-select" name="pola" required="" onchange="checkPola(this.value)">
              <option value="">-- Pilih Pola --</option>
              <?php foreach ($pola as $row) : ?>
              <option value="<?=$row['pola_kerja_id'];?>"><?=$row['nama_pola'];?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6 d-none" id="mberlaku">
            <label class="form-label">Mulai Berlaku<i class="text-danger">*</i></label>
            <input type="text" class="form-control" name="tglmulai" autocomplete="off" placeholder="YYYY-MM-DD" onchange="checkharipola()" id="flatpickr-date" required />
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6 d-none" id="mhberlaku">
            <label class="form-label">Hari ( dari pola kerja )<i class="text-danger">*</i></label>
            <input type="text" class="form-control" name="harike" id="harikeid" value="1" placeholder="..." required="" onkeyup="checkharipola()" />
          </div>
          <div class="col-xl-12 col-md-12">
            <div class="card-datatable table-responsive" id="res_pola_set"></div>
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
  function checkPola(a){
    $('#res_pola_set').html('Loading....');
    $.get('<?=base_url('karyawan/timework/pola/')?>'+a, function(data) {
      $('#mberlaku').removeClass('d-none');
      $('#mhberlaku').removeClass('d-none');
      $('#res_pola_set').html(data);
    });
  }

  $(document).ready(function () {
    $('#flatpickr-date').flatpickr({
      minDate: "today"
    });
  });

</script>