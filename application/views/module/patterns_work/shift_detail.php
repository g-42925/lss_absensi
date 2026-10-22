<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title">Jadwal Shift <?= $shift['name']; ?></h5>
      <div class="flex flex-row gap-3 items-center">
        <div class="text-start">
          <a href="<?= base_url().'patterns_work/shift_detail_add/'.$shiftId.'?failed=false' ?>" class="btn btn-secondary btn-primary btn-sm"><i class="ti ti-plus me-md-1"></i>Tambah Jadwal</a>
        </div>
        <div class="text-start">
          <a href="<?= base_url().'patterns_work/shift_off_set/'.$shiftId.'?failed=false' ?>" class="btn btn-secondary btn-primary btn-sm">Atur Hari Libur</a>
        </div>
      </div>
    </div>
    <div class="card-datatable table-responsive">
      <table class="table border-top" id="dataTable">
        <thead>
          <tr>
            <th>Name</th>
            <th>Work Hour</th>
            <th>Break Hour</th>
            <th>Tardiness Tolerance</th>
          </tr>
        </thead>
        <tbody>
          <?php $no=1; foreach ($datas as $row) : ?>
          <tr>
            <td class="text-capitalize"><?= $row['name'];?></td>
            <td><?= date("H:i", strtotime($row['clock_in'])) ?> - <?= date("H:i", strtotime($row['clock_out'])) ?></td>
            <td><?= date("H:i", strtotime($row['break'])) ?> - <?= date("H:i", strtotime($row['after_break'])) ?></td>
            <td><?= $row['tardiness_tolerance'];?> Minutes</td>
            <td align="right">
              <a href="<?= base_url().'patterns_work/shift_detail_edit/'.$shiftId.'/'.$row['shift_detail_id'] ?>" class="btn p-1">
                <i class="ti ti-edit"></i>
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