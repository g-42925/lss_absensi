<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title"><?=$namalabel;?></h5>
    </div>
  </div>
      <div class="row mt-3 gap-1">
        <div class="col-xl-3 col-lg-4 col-md-5 col-sm-6 col-xs-5 input-group">
            <input type="text" class="form-control" placeholder="YYYY-MM-DD" id="flatpickr-date" />
            <a class="input-group-text btn btn-outline-primary">Terapkan</a>
            <a href="<?= site_url('off/add') ?>" class="input-group-text btn btn-outline-primary">Tambah</a>
        </div>
      </div>
   <div class="card-datatable table-responsive">
      <?=$this->session->flashdata('message');?>
      <table class="table border-top" id="dataTableatt">
        <thead>
          <tr>
            <th class="w-s-n">Tanggal</th>
            <th class="w-s-n">Keterangan</th>
            <th class="w-s-n">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($offdays as $row) : ?>
          <tr>
            <td class="w-s-n">
              <?= $row['tanggal'];?>
            </td>
            <td class="w-s-n">
              <?= $row['keterangan']?>
            </td>
            <td class="w-s-n input-group">
              <a href="<?= site_url('off/edit/'.$row['id']) ?>" class="btn p-1">
                <i class="ti ti-edit"></i>
              </a>
              <a href="<?= site_url('off/delete/'.$row['id']) ?>" class="btn p-1" data-bs-toggle="modal" data-bs-target="#delRow<?=$row['id'];?>">
                <i class="ti ti-trash"></i>
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
</div>
