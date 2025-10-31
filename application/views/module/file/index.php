<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title">Daftar Jenis File</h5>
      <div class="text-start flex flex-row gap-3">
        <a href="<?=base_url('file/add?failed=false');?>" class="btn btn-secondary btn-primary btn-sm"><i class="ti ti-plus me-md-1"></i> Tambah Jenis File</a>
      </div>
    </div>
    <div class="card-datatable table-responsive">
       <table class="table border-top" id="dataTable">
        <thead>
          <tr class="text-center">
            <th>Name</th>
          </tr>
        </thead>
        <tbody>
          <?php $no=1; foreach ($data as $r) : ?>
          <tr class="text-center">
            <td class="text-capitalize"><?= $r['title'];?></td>
            <td>
              <a href="<?= base_url().'file/edit/'.$r['file_id'].'?failed=false' ?>" class="btn p-1">
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