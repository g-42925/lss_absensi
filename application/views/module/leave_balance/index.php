<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header border-bottom">
      <div class="d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0"><?=$namalabel;?></h5>
      </div>
      <form method="get" action="<?= site_url('leave_balance') ?>" class="row mt-3 g-2">
        <div class="col-xl-3 col-lg-3 col-md-4 col-sm-12">
            <select name="divisionId" class="form-select select2">
                <option value="all">Semua Divisi</option>
                <?php foreach ($divisions as $row): ?>
                  <option <?= (isset($div) && $div == $row['id']) ? 'selected':'' ?> value="<?= $row['id']; ?>">
                    <?= $row['division_name']; ?>
                  </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-4 col-sm-12">
            <input type="text" name="name" value="<?= isset($name) ? $name : '' ?>" class="form-control" placeholder="Nama Karyawan" />
        </div>
        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
            <button class="btn btn-secondary me-2">Cari</button>
            <a href="<?= site_url('leave_balance/add') ?>" class="btn btn-primary">Tambah</a>
        </div>
      </form>
    </div>
  </div>
   <div class="card-datatable table-responsive mt-3">
      <table class="table border-top" id="dataTableatt">
        <thead>
          <tr>
            <th class="w-s-n">Karyawan</th>
            <th class="w-s-n">Divisi</th>
            <th class="w-s-n">Berlaku Dari</th>
            <th class="w-s-n">Berlaku Sampai</th>
            <th class="w-s-n">Quota</th>
            <th class="w-s-n">Terpakai</th>
            <th>Sisa</th>
            <th class="w-s-n">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($balances as $row) : ?>
          <tr>
            <td class="w-s-n">
              <?= $row['nama_pegawai'] ?>
            </td>
            <td class="w-s-n">
              <?= $row['divisi'] ? $row['divisi'] : '-' ?>
            </td>
            <td class="w-s-n">
              <?= $row['from'];?>
            </td>
            <td class="w-s-n">
              <?= $row['to'] ?>
            </td>
            <td class="w-s-n">
              <?= $row['quota']?>
            </td>
            <td class="w-s-n">
              <?= $row['used']?>
            </td>
            <td class="w-s-n">
              <?= $row['quota'] - $row['used']?>
            </td>
            <td class="w-s-n input-group">
              <a href="<?= site_url('leave_balance/edit/'.$row['id']) ?>" class="btn p-1">
                <i class="ti ti-edit"></i>
              </a>
              <a href="<?= site_url('leave_balance/delete/'.$row['id']) ?>" class="btn p-1" onclick="return confirm('Hapus data ini?');">
                <i class="ti ti-trash"></i>
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
</div>
