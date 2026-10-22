<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card p-3">
    <div class="card-header border-bottom">
      <h5 class="card-title"><?=$namalabel;?></h5>
      <div class="text-start">
        <a href="<?=base_url('job');?>" class="btn btn-secondary btn-primary btn-sm">Kelola Job</a>
        <a href="<?=base_url('candidate');?>" class="btn btn-secondary btn-primary btn-sm">Kelola Candidate</a>
        <a href="<?=base_url('interview');?>" class="btn btn-secondary btn-primary btn-sm">Kelola interview </a>
      </div>
    </div>
    <div class="card-body pt-3">
      <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#opened-position" type="button" role="tab" aria-selected="true">Opened Position</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" data-bs-toggle="tab" data-bs-target="#upcoming-interview" type="button" role="tab" aria-selected="false">Upcoming Interview</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" data-bs-toggle="tab" data-bs-target="#candidate" type="button" role="tab" aria-selected="false">Candidate</button>
        </li>
      </ul>

      <div class="tab-content">
        <div class="tab-pane fade show active" id="opened-position" role="tabpanel">
          <div class="table-responsive">
            <table class="table border-top table-striped table-hover" id="dataTable1">
              <thead class="text-center">
                <tr>
                  <th class="w-s-n">Job Id</th>
                  <th class="w-s-n">Position</th>
                  <th class="w-s-n">Accepted For</th>
                  <th class="w-s-n">Closed</th>
                </tr>
              </thead>
              <tbody class="text-center">
                <?php $no=1; foreach ($job as $r) : ?>
                <tr>
                  <td><?= $r['job_id'];?></td>
                  <td><?= $r['name'];?></td>
                  <td><?= $r['accepted_for'];?></td>
                  <td><?= $r['closed']  == 1 ? 'yes':'no' ?></td>
                </tr>
                <?php $no++; endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="tab-pane fade" id="upcoming-interview" role="tabpanel">
          <div class="table-responsive">
            <table class="table border-top table-striped table-hover" id="dataTable2">
              <thead class="text-center">
                <tr>
                  <th class="w-s-n">Interview Id</th>
                  <th class="w-s-n">Candidate Id</th>
                  <th class="w-s-n">Name</th>
                  <th class="w-s-n">Position</th>
                  <th class="w-s-n">Date</th>
                </tr>
              </thead>
              <tbody class="text-center">
                <?php $no=1; foreach ($interview as $r) : ?>
                <tr>
                  <td><?= $r['interview_id'];?></td>
                  <td><?= $r['candidate_id'];?></td>
                  <td><?= $r['candidate_name'];?></td> 
                  <td><?= $r['name'];?></td> 
                  <td><?= $r['date'];?></td> 
                </tr>
                <?php $no++; endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="tab-pane fade" id="candidate" role="tabpanel">
          <div class="table-responsive">
            <table class="table border-top table-striped table-hover" id="dataTable3">
              <thead class="text-center">
                <tr>
                  <th class="w-s-n">Candidate Id</th>
                  <th class="w-s-n">Nik</th>
                  <th class="w-s-n">Name</th>
                  <th class="w-s-n">No. Telefon</th>
                  <th class="w-s-n">Jenis kelamin</th>
                  <th class="w-s-n">Posisi</th>
                </tr>
              </thead>
              <tbody class="text-center">
                <?php $no=1; foreach ($candidate as $r) : ?>
                <tr>
                  <td><?= $r['candidate_id'];?></td>
                  <td><?= $r['nik'];?></td>
                  <td><?= $r['candidate_name'];?></td> 
                  <td><?= $r['phone_number'];?></td> 
                  <td><?= $r['sex'] == 'l' ? 'laki-laki':'perempuan' ?></td> 
                  <td><?= $r['name'];?></td> 
                </tr>
                <?php $no++; endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- / Content -->