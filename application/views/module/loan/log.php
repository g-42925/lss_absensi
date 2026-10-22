<div class="container-xxl flex-grow-1 container-p-y m-3">
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title">Payment log</h5>
      <div class="text-start">
        <a onclick="window.print()" href="#" class="btn btn-secondary btn-primary btn-sm"><i class="ti ti-printer me-md-1"></i>Print</a>
      </div>
    </div>
    <div class="card-datatable table-responsive">
      <table class="table border-top" id="dataTable">
        <thead class="text-center">
          <tr>
            <th class="w-s-n">Date</th>
            <th class="w-s-n">Log Id</th>
            <th class="w-s-n">Amount</th>
          </tr>
        </thead>
        <tbody class="text-center">
          <?php $no=1; foreach ($data as $r) : ?>
          <tr>
            <td><?= $r['date'];?></td>            
            <td><?= $r['payment_log_id'];?></td>
            <td><?= number_format($r['amount'],2) ?></td>
          </tr>
          <?php $no++; endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>