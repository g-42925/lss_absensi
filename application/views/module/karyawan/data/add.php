<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Tambah Data</h5>
    </div>
    <div class="card">
      <form class="card-body" action="<?=base_url('karyawan/data/add_proses');?>" method="POST">
        <?php if ($failed == 1): ?>
          <?=$this->session->flashdata('message');?>
        <?php endif; ?>
        <div class="row g-3">
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label">ID Karyawan<i class="text-danger">*</i></label>
            <input readonly type="text" id="idkar" class="form-control" name="idkar" placeholder="..." required />
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label">Nama Lengkap<i class="text-danger">*</i></label>
            <input type="text" class="form-control" name="nama" placeholder="..." required />
          </div>
          <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
            <label class="form-label">Email<i class="text-danger">*</i></label>
            <input type="email" class="form-control" name="email" placeholder="..." required />
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label">No WhatsApp</label>
            <input type="text" class="form-control" name="nom" placeholder="..." />
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label">No Induk Kependudukan</label>
            <input type="text" class="form-control" name="nik" />
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label">Password<i class="text-danger">*</i></label>
            <input type="password" class="form-control" name="password" required placeholder="**********" />
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label" for="multicol-country">Jenis Kelamin<i class="text-danger">*</i></label>
            <select class="select2 form-select" name="jeniskelamin" required>
              <option value="l">Laki-laki</option>
              <option value="p">Perempuan</option>
            </select>
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label" for="multicol-country">Divisi<i class="text-danger">*</i></label>
            <select class="select2 form-select" name="division" required>
              <?php foreach($divisions as $d): ?>
                <option value="<?= $d['id'] ?>"><?= $d['division_name'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label" for="multicol-country">Posisi<i class="text-danger">*</i></label>
            <select class="select2 form-select" name="position" required>
              <?php foreach($position as $p): ?>
                <option value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label">Jumlah Cuti</label>
            <input type="text" class="form-control" name="jumlahCuti" placeholder="..." />
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label" for="multicol-country">Status pegawai<i class="text-danger">*</i></label>
            <select class="select2 form-select" name="statusPegawai" onchange="onStatusChange(this.value)" required>
              <option value="contract">Contract</option>
              <option value="permanent" selected>Permanent</option>
            </select>
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label">Gaji</label>
            <input type="text" class="form-control" name="salary" placeholder="..." />
          </div>
          <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12">
            <label class="form-label" for="multicol-country">On Training <i class="text-danger">*</i></label>
            <select class="select2 form-select" name="on_training">
              <option value="1">Yes</option>
              <option value="0">No</option>
            </select>
          </div>
          <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 hidden" id="csd">
            <label class="form-label">Contract start date<i class="text-danger">*</i></label>
            <input class="form-control" name="contract_start_date" placeholder="YYYY-MM-DD" id="flatpickr-date" required />
          </div>
          <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 hidden" id="ced">
            <label class="form-label">Contract end date<i class="text-danger">*</i></label>
            <input class="form-control" name="contract_end_date" placeholder="YYYY-MM-DD" id="flatpickr-date-2" required />
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6 hidden">
            <label class="form-label" for="multicol-country">Status<i class="text-danger">*</i></label>
            <select class="select2 form-select" name="status" required>
              <option value="y">Aktif</option>
              <option value="n">Tidak Aktif</option>
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
    $('#flatpickr-date').flatpickr({});
    $('#flatpickr-date-2').flatpickr({});

    const idField = document.getElementById('idkar');
    
    idField.value = Date.now()
  });

  function onStatusChange(newStatus){
    if(newStatus == "contract"){
      var csd = document.getElementById("csd");
      var ced = document.getElementById("ced")
      csd.classList.remove("hidden")
      ced.classList.remove("hidden")
    }
    if(newStatus == "permanent"){
      var csd = document.getElementById("csd");
      var ced = document.getElementById("ced")
      csd.classList.add("hidden")
      ced.classList.add("hidden")
    }
  }


</script>