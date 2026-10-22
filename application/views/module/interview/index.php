<div class="container-xxl flex-grow-1 container-p-y m-3">
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title">Interview</h5>
      <div class="text-start">
        <a href="<?=base_url('interview/add?failed=false');?>" class="btn btn-secondary btn-primary btn-sm"><i class="ti ti-plus me-md-1"></i> Tambah Data</a>
      </div>
    </div>
    <div class="card-datatable table-responsive">
      <table class="table border-top" id="dataTable">
        <thead class="text-center">
          <tr>
            <th class="w-s-n">Interview Id</th>
            <th class="w-s-n">Candidate Id</th>
            <th class="w-s-n">Candidate Name</th>
            <th class="w-s-n">Position</th>
            <th class="w-s-n">Date</th>
            <th class="w-s-n">Action</th>

          </tr>
        </thead>
        <tbody class="text-center">
          <?php $no=1; foreach ($data as $r) : ?>
          <tr>
            <td><?= $r['interview_id'];?></td>
            <td><?= $r['candidate_id'];?></td>
            <td><?= $r['candidate_name'];?></td>
            <td><?= $r['name'];?></td>
            <td><?= $r['date'];?></td>

            <td>
              <a href="<?=base_url('interview/edit/'.$r['interview_id']).'?failed=false';?>" class="btn p-1" title="Edit Pengajuan">
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