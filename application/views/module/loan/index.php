<div class="container-xxl flex-grow-1 container-p-y m-3">
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title">Hutang</h5>
      <div class="text-start">
        <a href="<?=base_url('loan/add?failed=false');?>" class="btn btn-secondary btn-primary btn-sm"><i class="ti ti-plus me-md-1"></i> Tambah Data</a>
      </div>
    </div>
    <div class="card-datatable table-responsive">
      <table class="table border-top" id="dataTable">
        <thead class="text-center">
          <tr>
            <th class="w-s-n">Date</th>
            <th class="w-s-n">Loan Id</th>
            <th class="w-s-n">Borrowed By</th>
            <th class="w-s-n">Amount</th>
            <th class="w-s-n">Paid Amount</th>
            <th class="w-s-n">Remaining</th>            
            <th class="w-s-n">Action</th>
          </tr>
        </thead>
        <tbody class="text-center">
          <?php $no=1; foreach ($data as $r) : ?>
          <tr>
            <td><?= $r['date'];?></td>            
            <td><?= $r['loan_id'];?></td>
            <td><?= $r['nama_pegawai'] ?> (<?= $r['id_pegawai']?>)</td>
            <td><?= number_format($r['amount'],2);?></td>
            <td class=<?= $r['amount'] == $r['paid_amount'] ? 'text-red-900':'' ?> ><?= number_format($r['paid_amount'],2);?></td>
            <td><?= number_format( $r['amount'] - $r['paid_amount'],2) ?></td>          
            <td>
              <a href="<?=base_url('loan/edit/'.$r['loan_id']).'?failed=false';?>" class="btn p-1" title="Edit Pengajuan">
                <i class="ti ti-edit"></i>
              </a>
              <a href="<?=base_url('loan/log/'.$r['loan_id']).'?failed=false';?>" class="btn p-1" title="Edit Pengajuan">
                <i class="ti ti-note"></i>
              </a>
            </td>
          </tr>
          
          <?php $no++; endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>