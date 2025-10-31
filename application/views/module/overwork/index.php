<div class="container-xxl flex-grow-1 container-p-y m-3">
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title">Lembur</h5>
    </div>
    <div class="card-datatable table-responsive">
      <table class="table border-top" id="dataTable">
        <thead class="text-center">
          <tr>
            <th class="w-s-n">Requested By</th>
            <th class="w-s-n">Requested At</th>
            <th class="w-s-n">Start from</th>
            <th class="w-s-n">Until</th>
            <th class="w-s-n">Action</th>
          </tr>
        </thead>
        <tbody class="text-center">
          <?php $no=1; foreach ($data as $r) : ?>
          <tr>
            <td><?= $r['nama_pegawai'] ?> (<?= $r['employee_id'];?>)</td>
            <td><?= $r['date'];?></td>
            <td><?= $r['start_from'];?></td>
            <td><?= $r['until'];?></td>
            <td><?= $r['approved'] == 1 ? 'yes':'no' ?></td>

            <td>
              <a href="<?=base_url('overwork/edit/'.$r['employee_overwork_id']).'?failed=false';?>" class="btn p-1" title="Edit Pengajuan">
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