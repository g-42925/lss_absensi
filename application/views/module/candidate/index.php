<div class="container-xxl flex-grow-1 container-p-y m-3">
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title">Candidate</h5>
      <div class="text-start">
        <a href="<?=base_url('candidate/add?failed=false');?>" class="btn btn-secondary btn-primary btn-sm"><i class="ti ti-plus me-md-1"></i> Tambah Data</a>
      </div>
    </div>
    <div class="card-datatable table-responsive">
      <table class="table border-top" id="dataTable">
        <thead class="text-center">
          <tr>
            <th class="w-s-n">Candidate Id</th>
            <th class="w-s-n">Nik</th>
            <th class="w-s-n">Nama</th>
            <th class="w-s-n">Jenis kelamin</th>
            <th class="w-s-n">Nomor Ponsel</th>
            <th class="w-s-n">Email</th>
            <th class="w-s-n">Action</th>
          </tr>
        </thead>
        <tbody class="text-center">
          <?php $no=1; foreach ($data as $r) : ?>
          <tr>
            <td><?= $r['candidate_id'];?></td>
            <td><?= $r['nik'];?></td>
            <td><?= $r['candidate_name'] ?></td>
            <td><?= $r['sex'] = 'l' ? 'laki-laki':'perempuan' ?></td>
            <td><?= $r['phone_number'] ?></td>
            <td><?= $r['email'] ?></td>
            <td>
              <a href="<?=base_url('candidate/edit/'.$r['candidate_id']).'?failed=false';?>" class="btn p-1" title="Edit Pengajuan">
                <i class="ti ti-edit"></i>
              </a>
              <a href="<?=base_url('candidate/accept/'.$r['candidate_id']).'?failed=false';?>" class="btn p-1" title="Setujui kandidat">
                <i class="ti ti-check"></i>
              </a>
            </td>
          </tr>
          
          <?php $no++; endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>