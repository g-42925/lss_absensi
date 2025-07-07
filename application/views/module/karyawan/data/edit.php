<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Edit Data</h5>
    </div>
    <div class="card">
      <form class="card-body" action="<?=base_url('karyawan/data/edit_proses/'.$edit['pegawai_id']);?>" method="POST">
        <?=$this->session->flashdata('message');?>
        <div class="row g-3">
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label">ID Karyawan<i class="text-danger">*</i></label>
            <input type="text" class="form-control" name="idkar" value="<?=$edit['id_pegawai'];?>" placeholder="..." required />
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label">Nama Lengkap<i class="text-danger">*</i></label>
            <input type="text" class="form-control" name="nama" value="<?=$edit['nama_pegawai'];?>" placeholder="..." required />
          </div>
          <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
            <label class="form-label">Email<i class="text-danger">*</i></label>
            <input type="email" class="form-control" name="email" value="<?=$edit['email_pegawai'];?>" placeholder="..." required />
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label">No WhatsApp</label>
            <input type="text" class="form-control" name="nom" value="<?=$edit['nomor_pegawai'];?>" placeholder="..." />
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label">Password</label>
            <input type="password" class="form-control" name="password" placeholder="**********" />
          </div>
          <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
            <label class="form-label">Tanggal Mulai Kerja<i class="text-danger">*</i></label>
            <input type="text" class="form-control" name="tglmulai" value="<?=$edit['tanggal_mulai_kerja'];?>" placeholder="YYYY-MM-DD" id="flatpickr-date" required />
            <div class="small mt-1">Ini menentukan tanggal awal mulai rekap, jika diset dibawah tanggal hari ini maka status kehadiran otomatis dinyatakan tidak hadir.</div>
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label" for="multicol-country">Jenis Kelamin<i class="text-danger">*</i></label>
            <select class="select2 form-select" name="jeniskelamin" required>
              <option value="l" <?php if ($edit['jenis_kelamin']=='l') echo 'selected'; ?>>Laki-laki</option>
              <option value="p" <?php if ($edit['jenis_kelamin']=='p') echo 'selected'; ?>>Perempuan</option>
            </select>
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label" for="multicol-country">Status<i class="text-danger">*</i></label>
            <select class="select2 form-select" name="status" required>
              <option value="y" <?php if ($edit['is_status']=='y') echo 'selected'; ?>>Aktif</option>
              <option value="n" <?php if ($edit['is_status']=='n') echo 'selected'; ?>>Tidak Aktif</option>
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
  });
</script>