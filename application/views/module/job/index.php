<div class="container-xxl flex-grow-1 container-p-y m-3">
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title">Job</h5>
      <div class="text-start">
        <a href="<?=base_url('job/add?failed=false');?>" class="btn btn-secondary btn-primary btn-sm"><i class="ti ti-plus me-md-1"></i> Tambah Data</a>
      </div>
    </div>
    <div class="card-datatable table-responsive">
      <table class="table border-top" id="dataTable">
        <thead class="text-center">
          <tr>
            <th class="w-s-n">Position Name</th>
            <th class="w-s-n">Accepted For</th>
            <th class="w-s-n">Closed</th>
            <th class="w-s-n">Action</th>
          </tr>
        </thead>
        <tbody class="text-center">
          <?php $no=1; foreach ($data as $r) : ?>
          <tr>
            <td><?= $r['name'];?></td>
            <td><?= $r['accepted_for'];?></td>
            <td><?= $r['closed'] == 1 ? 'yes':'no' ?></td>
            <td>
              <a href="<?=base_url('job/edit/'.$r['job_id']).'?failed=false';?>" class="btn p-1" title="Edit Pengajuan">
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