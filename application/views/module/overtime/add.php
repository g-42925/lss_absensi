<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Tambah Data</h5>
    </div>
    <div class="card">
      <form class="card-body" action="<?=base_url('overtime/add_proses');?>" method="POST">
        <?=$this->session->flashdata('message');?>
        <div class="row g-3">
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
            <label class="form-label" for="multicol-country">Karyawan<i class="text-danger">*</i></label>
            <div class="pb-2" style="position: absolute; top:15px; right:30px;">
              <button type="button" class="btn btn-outline-primary btn-sm ft-11" onclick="selectAll()">Select All</button>
              <button type="button" class="btn btn-outline-warning btn-sm ft-11" onclick="deselectAll()">
                <i class="ti ti-trash ft-12"></i>&nbsp;&nbsp;All</button>
            </div>
            <select class="select2 select2s form-select" id="my-select-kary" name="idp[]" required multiple>
              <?php foreach ($karyawan as $row) : ?>
              <option value="<?=$row['pegawai_id'];?>" data-foo="<?=$row['id_pegawai'];?>"><?=$row['nama_pegawai'];?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
            <label class="form-label">Tanggal<i class="text-danger">*</i></label>
            <input type="text" class="form-control" name="tgl1" placeholder="YYYY-MM-DD" id="flatpickr-date" required />
          </div>
          <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
            <label class="form-label">Jam Masuk<i class="text-danger">*</i></label>
            <input type="text" class="form-control flatpickr-input active" name="jmasuk" placeholder="hh:mm" id="flatpickr-time" readonly="readonly">
          </div>
          <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6" >
            <label class="form-label">Jam Keluar<i class="text-danger">*</i></label>
            <input type="text" class="form-control flatpickr-input active" name="jkeluar" placeholder="hh:mm" id="flatpickr-time2" readonly="readonly">
          </div>
          <div class="col-xl-12 col-lg-12">
            <div class="">Optional</div>
            <div class="ft-13">Jika kolom ini diisi maka semua pegawai yang dipilih untuk lembur, maka otomatis absen lembur, selesai lembur dan catatan akhir akan diisi mengikuti ini.</div>
          </div>
          <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
            <label class="form-label">Absen Masuk</label>
            <input type="text" class="form-control flatpickr-input active" name="amasuk" placeholder="hh:mm" id="flatpickr-time3" readonly="readonly">
          </div>
          <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
            <label class="form-label">Absen Keluar</label>
            <input type="text" class="form-control flatpickr-input active" name="akeluar" placeholder="hh:mm" id="flatpickr-time4" readonly="readonly">
          </div>
          <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <label class="form-label">Catatan Lembur</label>
            <textarea type="text" class="form-control" name="catatanhl" placeholder="..."></textarea>
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

    $('#flatpickr-date').flatpickr({
      minDate: "today",
      maxDate: '<?=$thismonth;?>'
    });

    $(function(){
      $(".select2s").select2({
          matcher: matchCustom,
          templateResult: formatCustom
      });
    });

  });

  function selectAll() {
      $("#my-select-kary > option").prop("selected", true);
      $("#my-select-kary").trigger("change");
  }

  function deselectAll() {
      $("#my-select-kary > option").prop("selected", false);
      $("#my-select-kary").trigger("change");
  }
</script>