<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title"><?=$namalabel;?></h5>
      <div class="text-start">
        <a href="#" onclick="action_data('baru');" class="btn btn-secondary btn-primary btn-sm"><i class="ti ti-plus me-md-1"></i> Tambah Data</a>
      </div>
    </div>
    <div class="card-datatable table-responsive">
      <?=$this->session->flashdata('message');?>
      <table class="table border-top" id="dataTable">
        <thead>
          <tr>
            <th>Label</th>
            <th>Tambah</th>
            <th>Edit</th>
            <th>Hapus</th>
            <th>Status</th>
            <th class="text-end">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php $no=1; foreach ($datas as $row) : ?>
          <tr>
            <td><?= $row['nama_permission'];?></td>
            <td>
              <?php if ($row['tambah']=='y') { ?>
              <span class="badge bg-label-success"><i class="ti ti-check small"></i></span>
              <?php } else { ?>
              <span class="badge bg-label-danger"><i class="ti ti-x small"></i></span>
              <?php } ?>
            </td>
            <td>
              <?php if ($row['edit']=='y') { ?>
              <span class="badge bg-label-success"><i class="ti ti-check small"></i></span>
              <?php } else { ?>
              <span class="badge bg-label-danger"><i class="ti ti-x small"></i></span>
              <?php } ?>
            </td>
            <td>
              <?php if ($row['hapus']=='y') { ?>
              <span class="badge bg-label-success"><i class="ti ti-check small"></i></span>
              <?php } else { ?>
              <span class="badge bg-label-danger"><i class="ti ti-x small"></i></span>
              <?php } ?>
            </td>
            <td>
              <?php if ($row['is_status']=='y') { ?>
              <span class="badge bg-label-success">Aktif</span>
              <?php } else if ($row['is_status']=='n') { ?>
              <span class="badge bg-label-danger">Tidak Aktif</span>
              <?php } else { ?>
              <span class="badge bg-label-secondary">Unknown</span>
              <?php } ?>
            </td>
            <td align="right">
              <?php if ($row['permission_id']!=1) { ?>
              <a href="javascript:;" onclick="action_data('<?=$row['permission_id'];?>');" class="btn p-1">
                <i class="ti ti-edit"></i>
              </a>
              <a href="#" class="btn p-1" data-bs-toggle="modal" data-bs-target="#delRow<?=$row['permission_id'];?>">
                <i class="ti ti-trash"></i>
              </a>
              <!-- Konfirmasi Hapus -->
              <div class="modal fade" id="delRow<?=$row['permission_id'];?>" tabindex="-1" aria-hidden="true">
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
                        <a href="<?=base_url('company/permission/hapus/'.$row['permission_id']);?>" class="btn btn-danger">Ya, Hapus</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <?php } else { ?>
                Default
              <?php } ?>
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
  <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
    <div class="modal-content p-3 p-md-5" id="content_option_modal"></div>
  </div>
</div>

<script type="text/javascript">
  function action_data(a){
    $('#optiondataModal').modal('toggle');
    $('#content_option_modal').html('Loading...');
    $.get('<?=base_url('company/permission/action/');?>'+a, function(data) {
      $('#content_option_modal').html(data);
    });
  }
</script>