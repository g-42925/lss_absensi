<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title"><?=$namalabel;?> :  <?= number_format($summary,2) ?></h5>
      <div class="text-start">
        <a href="<?= base_url('payroll/toCsv/').$dateX."/".$dateY ?>" class="btn btn-secondary btn-primary btn-sm"><i class="ti ti-plus me-md-1"></i>To Excel</a>
      </div>
    </div>
    <div class="card-datatable table-responsive">
      <table class="table border-top" id="dataTable">
        <thead>
          <tr class="text-center">
            <th class="w-s-n">Employee Name</th>
            <th class="w-s-n">Division Name</th>
            <th class="w-s-n">Salary</th>
            <th class="w-s-n">Additional</th>
            <th class="w-s-n">Minus</th>
            <th class="w-s-n">Take Home Pay</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($divisions as $row) : ?>
            <?php foreach($row['employees'] as $e) : ?>
              <tr class="text-center">
                <td><?= $e['nama_pegawai'] ?>
                <td><?= $row['division_name'] ?></td>
                <td><?= number_format($e['salary'],2) ?></td>
                <td><?= number_format($e['totalAllowance'],2) ?></td>                            
                <td><?= number_format($e['totalBenefit'],2) ?></td>
                <td><?= number_format($e['thp'],2) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
