<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-datatable table-responsive">
       <table class="table border-top" id="dataTable">
        <thead>
          <tr class="text-center">
            <th>Claim By</th>
            <th>Name</th>
            <th>Date</th>
            <th>Value</th>
            <th>Photo</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php $no=1; foreach ($data as $r) : ?>
          <tr class="text-center">
            <td class="text-capitalize"><?= $r['nama_pegawai'] ?> (<?= $r['employee_id'] ?>)</td>
            <td class="text-capitalize"><?= $r['reimburse_name'];?></td>
            <td class="text-capitalize"><?= $r['date'];?></td>
            <td class="text-capitalize"><?= number_format($r['value'],2);?></td>
            <td class="text-capitalize">
              <a target="_blank" href="<?= $r['photo'] ?>"><i class="ti ti-photo"></i></a>
            </td>            
            <td class="text-capitalize"><?= $r['status'] ?></td>
            <td>
              <a href="<?= base_url().'reimburse/claim_edit/'.$r['reimburse_id'].'/'.$r['reimburse_claim_id'].'?failed=false' ?>" class="btn p-1">
                <i class="ti ti-settings"></i>
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