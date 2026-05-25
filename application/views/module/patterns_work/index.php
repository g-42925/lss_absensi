<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title"><?=$namalabel;?></h5>
      <div class="text-start">
        <a href="<?=base_url('patterns_work/add?failed=false');?>" class="btn btn-secondary btn-primary btn-sm"><i class="ti ti-plus me-md-1"></i> Tambah Data</a>
      </div>
    </div>
    <div class="card-datatable table-responsive">
      <table class="table border-top" id="dataTable">
        <thead>
          <tr class="text-center">
            <th>Nama&nbsp;Pola</th>
            <th>Jumlah Hari</th>
            <th>Hari Kerja</th>
            <th>Hari Libur</th>
            <th>Toleransi Terlambat</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php $no=1; foreach ($datas as $row) : ?>
          <tr class="text-center">
            <td class="text-capitalize"><?= $row['nama_pola'];?></td>
            <td><?= $row['jumlah_hari_siklus'];?> Hari</td>
            <td><?= $row['jumlah_kerja'];?> Hari</td>
            <td><?= $row['jumlah_libur'];?> Hari</td>
            <td><?= $row['toleransi_terlambat'];?> Menit</td>
            <td>
              <a href="<?=base_url('patterns_work/edit/'.$row['pola_kerja_id']).'?failed=false';?>" class="btn p-1">
                <i class="ti ti-edit"></i>
              </a>
              <!-- Konfirmasi Hapus -->
            </td>
          </tr>
          <?php $no++; endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<!-- / Content -->