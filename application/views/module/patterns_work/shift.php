<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title">Daftar Shift</h5>
      <div class="text-start">
        <a href="<?=base_url('patterns_work/shift_add?failed=false');?>" class="btn btn-secondary btn-primary btn-sm"><i class="ti ti-plus me-md-1"></i> Tambah Shift</a>
      </div>
    </div>
    <div class="card-datatable table-responsive">
       <table class="table border-top" id="dataTable">
        <thead>
          <tr>
            <th>Shift</th>
            <th>Jumlah Jadwal</th>
            <th class="text-end">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php $no=1; foreach ($datas as $row) : ?>
          <tr>
            <td class="text-capitalize"><?= $row['name'];?></td>
            <td class="text-capitalize"><?= $row['jumlahJadwal'];?></td>

            <td align="right">
              <a href="<?= base_url().'patterns_work/shift_set/'.$row['id'].'?failed=false' ?>" class="btn p-1">
                <i class="ti ti-settings"></i>
              </a>
              <a href="<?= base_url().'patterns_work/shift_edit/'.$row['id'] ?>" class="btn p-1">
                <i class="ti ti-edit"></i>
              </a>
              <a href="<?= base_url().'patterns_work/shift_detail/'.$row['id'] ?>" class="btn p-1">
                <i class="ti ti-eye"></i>
              </a>
            </td>
          </tr>
          <?php $no++; endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<!-- / Content -->