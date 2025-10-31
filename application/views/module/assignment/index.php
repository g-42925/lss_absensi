<div class="container-xxl flex-grow-1 container-p-y m-3">
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title">Penugasan</h5>
      <div class="text-start flex flex-row gap-3">
        <a href="<?=base_url('assignment/add?failed=false');?>" class="btn btn-secondary btn-primary btn-sm"><i class="ti ti-plus me-md-1"></i> Tambahkan penugasan</a>
      </div>
    </div>
    <div class="card-datatable table-responsive">
      <table class="table border-top" id="dataTable">
        <thead>
          <tr>
            <th class="w-s-n">Assignment Id</th>
            <th class="w-s-n">Employee Id</th>
            <th class="w-s-n">Start</th>
            <th class="w-s-n">Until</th>
            <th class="w-s-n">Description</th>
            <th class="w-s-n">Action</th>
          </tr>
        </thead>"
        <tbody>
          <?php $no=1; foreach ($data as $r) : ?>
          <tr>
            <td><?= $r['assignment_id'];?></td>
            <td><?= $r['nama_pegawai'] ?> (<?= $r['employee_id'];?>)</td>
            <td><?= $r['start_from'];?></td>
            <td><?= $r['until'];?></td>
            <td><?= $r['description'];?></td>
            <td>
              <a href="<?=base_url('assignment/edit/'.$r['assignment_id']).'?failed=false';?>" class="btn p-1" title="Edit Pengajuan">
                <i class="ti ti-edit"></i>
              </a>
              <a href="<?= site_url('assignment/delete/'.$r['assignment_id']) ?>" class="btn p-1">
                <i class="ti ti-trash"></i>
              </a>              
            </td>
          </tr>
          <?php $no++; endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>