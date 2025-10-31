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
    <div class="card-datatable table-responsive flex flex-col gap-1">
      <table class="table border-top" id="dataTable">
        <span class="text-lg m-3 text-center">
          Opened position
        </span>
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
      <table class="table border-top" id="dataTable">
        <span class="text-lg m-3 text-center">
          Upcoming Interview
        </span>
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
      <table class="table border-top" id="dataTable">
        <span class="text-lg m-3 text-center">
          Candidate
        </span>
        <thead class="text-center">
          <tr>
            <th class="w-s-n">Candidate Id</th>
            <th class="w-s-n">Nik</th>
            <th class="w-s-n">Name</th>
            <th class="w-s-n">No. Telefon</th>
            <th class="w-s-n">Email</th>
            <th class="w-s-n">Jenis kelamin</th>

          </tr>
        </thead>
        <tbody class="text-center">
          <?php $no=1; foreach ($candidate as $r) : ?>
          <tr>
            <td><?= $r['candidate_id'];?></td>
            <td><?= $r['nik'];?></td>
            <td><?= $r['candidate_name'];?></td> 
            <td><?= $r['phone_number'];?></td> 
            <td><?= $r['email'];?></td> 
            <td><?= $r['sex'] == 'l' ? 'laki-laki':'perempuan' ?></td> 


          </tr>
          
          <?php $no++; endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<!-- / Content -->