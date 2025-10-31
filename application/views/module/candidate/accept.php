<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Tambah Data</h5>
    </div>
    <div class="card">
      <form class="card-body" action="<?= base_url('candidate/accept_proccess/').$candidateId ?>" method="POST">
        <?php if ($failed): ?>
          <?=$this->session->flashdata('message');?>
        <?php endif; ?>
        <div class="row g-3">
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label">ID Karyawan<i class="text-danger">*</i></label>
            <input type="text" class="form-control" name="id_pegawai" value="<?= $candidate['candidate_id'] ?>" readonly/>
            <input type="hidden" value="<?= $candidate['candidate_picture'] ?>" name="foto_pegawai">
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label">Nama Lengkap<i class="text-danger">*</i></label>
            <input value="<?= $candidate['candidate_name'] ?>" type="text" class="form-control" name="nama_pegawai" readonly />
          </div>
          <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
            <label class="form-label">Email<i class="text-danger">*</i></label>
            <input value="<?= $candidate['email'] ?>" type="email" class="form-control" name="email_pegawai" readonly />
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label">No WhatsApp</label>
            <input value="<?= $candidate['phone_number'] ?>" type="text" class="form-control" name="nomor_pegawai" readonly />
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label">No Induk Kependudukan</label>
            <input value="<?= $candidate['nik'] ?>" type="text" class="form-control" name="nik" readonly />
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label">Password<i class="text-danger">*</i></label>
            <input type="password" class="form-control" name="password" required placeholder="**********" />
          </div>
          <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
            <label class="form-label">Tanggal Mulai Kerja<i class="text-danger">*</i></label>
            <input type="hidden" class="form-control" name="tglmulai" placeholder="YYYY-MM-DD" id="flatpickr-date" required />
            <div class="small mt-1">Ini menentukan tanggal awal mulai rekap, jika diset dibawah tanggal hari ini maka status kehadiran otomatis dinyatakan tidak hadir.</div>
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label" for="multicol-country">Jenis Kelamin<i class="text-danger">*</i></label>
            <select class="select2 form-select" name="jeniskelamin" required>
              <option <?= $candidate['sex'] == 'l' ? 'selected':'' ?> value="l">Laki-laki</option>
              <option <?= $candidate['sex'] == 'p' ? 'selected':'' ?> value="p">Perempuan</option>
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
                <option <?= $candidate['position_id'] == $p['id'] ? 'selected':'' ?> value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label">Jumlah Cuti</label>
            <input type="text" class="form-control" name="jumlahCuti" placeholder="..." />
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label" for="multicol-country">Status pegawai<i class="text-danger">*</i></label>
            <select class="select2 form-select" name="statusPegawai" required>
              <option value="contract">Contract</option>
              <option value="permanent">Permananet</option>
            </select>
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label">Gaji</label>
            <input type="text" class="form-control" name="salary" required />
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
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
    $('#flatpickr-date').flatpickr({
      minDate: "<?=$mindate;?>",
      maxDate: "<?=$maxdate;?>"
    });

    const idField = document.getElementById('idkar');
    
    idField.value = Date.now()
  });


</script>