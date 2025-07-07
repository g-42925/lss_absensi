<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Tambah Data</h5>
    </div>
    <div class="card">
      <form class="card-body" action="<?=base_url('company/admin/add_proses');?>" method="POST">
        <?=$this->session->flashdata('message');?>
        <div class="row g-3">
          <div class="col-xl-8 col-md-8 col-sm-7 col-xs-7">
            <label class="form-label">Nama Lengkap<i class="text-danger">*</i></label>
            <input type="text" class="form-control" name="nama" placeholder="..." required />
          </div>
          <div class="col-xl-4 col-md-4 col-sm-5 col-xs-5">
            <label class="form-label" for="multicol-country">Status<i class="text-danger">*</i></label>
            <select class="select2 form-select" name="status" required>
              <option value="y">Aktif</option>
              <option value="n">Tidak Aktif</option>
            </select>
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6">
            <label class="form-label w-100">
              Role/Jabatan<i class="text-danger">*</i>
              <span class="float-right">
                <a href="<?=base_url('company/roles');?>">Tambah&nbsp;</a>
              </span>
            </label>
            <select class="select2 form-select" name="roles" required>
              <?php foreach ($roles as $row) : ?>
              <option value="<?=$row['role_id'];?>"><?=$row['nama_role'];?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6">
            <label class="form-label w-100">
              Permission/Izin<i class="text-danger">*</i>
              <span class="float-right">
                <a href="<?=base_url('company/permission');?>">Tambah&nbsp;</a>
              </span>
            </label>
            <select class="select2 form-select" name="izin" required>
              <?php foreach ($permission as $row) : ?>
              <option value="<?=$row['permission_id'];?>"><?=$row['nama_permission'];?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
            <label class="form-label">Email<i class="text-danger">*</i></label>
            <input type="email" class="form-control" name="email" placeholder="..." required />
          </div>
          <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
            <label class="form-label">Password<i class="text-danger">*</i></label>
            <input type="password" class="form-control" name="password" placeholder="***************" required />
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