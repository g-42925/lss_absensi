<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title"><?=$namalabel;?></h5>
      <div class="text-start">
        <a href="<?=base_url('overtime/add');?>" class="btn btn-secondary btn-primary btn-sm"><i class="ti ti-plus me-md-1"></i> Tambah Data</a>
      </div>
    </div>
    <div class="card-datatable table-responsive">
      <?=$this->session->flashdata('message');?>
      <table class="table border-top" id="dataTableatt">
        <thead>
          <tr>
            <th class="w-s-n">Tanggal</th>
            <th>J.Masuk</th>
            <th>J.Keluar</th>
            <th>Status</th>
            <th class="w-s-n">Absen&nbsp;Masuk</th>
            <th class="w-s-n">Absen&nbsp;Keluar</th>
            <th class="text-end">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php 
            $no=1; foreach ($datas as $row) : 
          ?>
          <tr>
            <td class="w-s-n">
              <?= indo($row['tanggal_lembur']);?>
            </td>
            <td><?= $row['masuk_lembur'];?></td>
            <td><?= $row['keluar_lembur'];?></td>
            <td>
              <?php if ($row['is_status']==0) { ?>
              <span class="badge bg-label-warning">Pending</span>
              <?php } else if ($row['is_status']==1) { ?>
              <span class="badge bg-label-success">Approved</span>
              <?php } else if ($row['is_status']==2) { ?>
              <span class="badge bg-label-danger">Reject</span>
              <?php }  else { ?>
              <span class="badge bg-label-secondary">Unknown</span>
              <?php } ?>
            </td>
            <td><?= $row['absen_masuk'];?></td>
            <td><?= $row['absen_keluar'];?></td>
            <td align="right">
              <a href="javascript:;" onclick="action_data('<?=$row['lembur_id'];?>');" class="btn p-1">
                <i class="ti ti-users"></i>
              </a>
              <a href="<?=base_url('overtime/edit/'.$row['lembur_id']);?>" class="btn p-1">
                <i class="ti ti-edit"></i>
              </a>
              <a href="#" class="btn p-1" data-bs-toggle="modal" data-bs-target="#delRow<?=$row['lembur_id'];?>">
                <i class="ti ti-trash"></i>
              </a>
              <!-- Konfirmasi Hapus -->
              <div class="modal fade" id="delRow<?=$row['lembur_id'];?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-simple modal-enable-otp modal-dialog-centered">
                  <div class="modal-content p-3 p-md-5">
                    <div class="modal-body">
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      <div class="text-center mb-4">
                        <h3 class="mb-2">Konfirmasi</h3>
                        <p>Yakin ingin menghapus data ini ?</p>
                      </div>
                      <div class="col-12 text-center pt-3">
                        <button
                          type="button"
                          class="btn btn-label-secondary me-sm-3 me-1"
                          data-bs-dismiss="modal"
                          aria-label="Close">
                          Batal
                        </button>
                        <a href="<?=base_url('overtime/hapus/'.$row['lembur_id']);?>" class="btn btn-danger">Ya, Hapus</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </td>
          </tr>
          <?php $no++; endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<!-- / Content -->

<!-- Modal -->
<div class="modal fade" id="optiondataModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-add-new-role">
    <div class="modal-content p-3 p-md-5" id="content_option_modal"></div>
  </div>
</div>

<script type="text/javascript">
  function action_data(a){
    $('#optiondataModal').modal('toggle');
    $('#content_option_modal').html('Loading...');
    $.get('<?=base_url('overtime/action/');?>'+a, function(data) {
      $('#content_option_modal').html(data);
    });
  }
</script>