<div class="container-xxl flex-grow-1 container-p-y m-3">
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title">Pengecualian</h5>
    </div>
    <div class="card-datatable table-responsive">
      <table class="table border-top" id="dataTable">
        <thead>
          <tr>
            <th class="w-s-n">Date</th>
            <th class="w-s-n">Requested By</th>
            <th class="w-s-n">Reason</th>
            <th class="w-s-n">Category</th>
            <th class="w-s-n">Photo</th>
            <th class="w-s-n">Status</th>
          </tr>
        </thead>"
        <tbody>
          <?php $no=1; foreach ($data as $r) : ?>
          <tr>
            <td><?= $r['date'];?></td>
            <td><?= $r['nama_pegawai'] ?> (<?= $r['employee_id'];?>)</td>
            <td><?= $r['reason'];?></td>
            <td><?= $r['type'];?></td>
            <td class="text-capitalize">
              <a target="_blank" href="<?= $r['image'] ?>"><i class="ti ti-photo"></i></a>
            </td>  
            <td>
              <?php
                echo match($r['status']) {
                  '0' => 'pending',
                  '1' => 'approved',
                  '2' => 'rejected',
                  default => 'rejected',
                };
              ?>
            </td>
            <td align="right">
              <a href="<?=base_url('except/edit/'.$r['id']).'?failed=false';?>" class="btn p-1" title="Edit Pengajuan">
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