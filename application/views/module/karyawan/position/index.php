<div class="container-xxl flex-grow-1 container-p-y m-3">
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title">Data Posisi</h5>
      <div class="text-start">
        <a href="<?=base_url('karyawan/position/add?failed=false');?>" class="btn btn-secondary btn-primary btn-sm"><i class="ti ti-plus me-md-1"></i> Tambah Posisi</a>
      </div>
    </div>
    <div class="card-datatable table-responsive">
      <table class="table border-top" id="dataTable">
        <thead>
          <tr>
            <th class="w-s-n">Nama</th>
            <th class="w-s-n">Hirarki</th>
          </tr>
        </thead>
        <tbody>
          <?php $no=1; foreach ($position as $row) : ?>
          <tr>
            <td><?= $row['name'];?></td>
            <td><?= $row['hirarki'];?></td>
          </tr>
          <?php $no++; endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>