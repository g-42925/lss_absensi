<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title"><?=$namalabel;?></h5>
      <div class="text-start">
        <a href="<?=base_url('req_permission/add');?>" class="btn btn-secondary btn-primary btn-sm"><i class="ti ti-plus me-md-1"></i> Tambah Data</a>
      </div>
      <div class="row mt-3">
        <div class="col-xl-3 col-lg-4 col-md-5 col-sm-6 col-xs-5">
          <label class="form-label">Tanggal Mulai</label>
            <input type="text" class="form-control filtertglrkp" placeholder="YYYY-MM-DD" value="<?=$tglawal;?>" id="flatpickr-date" />
        </div>
        <div class="col-xl-4 col-lg-4 col-md-5 col-sm-6 col-xs-7">
          <label class="form-label">Akhir</label>
          <div class="input-group">
            <input type="text" class="form-control filtertglrkp2" placeholder="YYYY-MM-DD" id="flatpickr-date2" value="<?=$tglakhir;?>" />
            <a href="javascript:filtertglRkp();" class="input-group-text btn btn-outline-primary">Terapkan</a>
          </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-2 col-sm-12">
          <label class="form-label">&nbsp;</label>
          <a href="<?=base_url('req_permission/download_laporan/'.$tglawal.'/'.$tglakhir);?>" class="form-control btn btn-outline-primary">Download</a>
        </div>
        <div class="col-xl-12 col-lg-12 pt-2">
          <small>
            *Set.Masuk dan Set.Keluar berlaku untuk kategori cuti s/hari dan tugas luar.
          </small>
        </div>
      </div>
    </div>
    <div class="card-datatable table-responsive">
      <table class="table border-top" id="dataTableatt">
        <thead>
          <tr>
            <th class="w-s-n">Date</th>
            <th class="w-s-n">Requested By</th>
            <th>Category</th>
            <th>Photo</th>
            <th>Status</th>
            <th class="text-end">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php 
            $no=1; foreach ($datas as $row) : 
          ?>
          <tr>
            <td class="w-s-n">
              <?= $row['tanggal_request'] ?> - <?= $row['tanggal_request_end'] ?>
            </td>
            <td class="w-s-n">
              <?= $row['nama_pegawai'] ?> (<?= $row['pegawai_id']; ?>)
            </td>
            <td class="w-s-n">
              <?= $row['tipe_request'] ?>
            </td>
            <td class="text-capitalize">
              <a target="_blank" href="<?= $row['image'] == "-" ? "":$row['image'] ?>"><i class="ti ti-photo"></i></a>
            </td>  
            <td class="w-s-n">
              <?= $row['is_status'] ?>
            </td>                   
            <td align="right">
              <a href="<?=base_url('req_permission/edit/'.$row['request_izin_id']);?>" class="btn p-1">
                <i class="ti ti-edit"></i>
              </a>
              <a href="#" class="btn p-1" data-bs-toggle="modal" data-bs-target="#delRow<?=$row['request_izin_id'];?>">
                <i class="ti ti-trash"></i>
              </a>
              <a class="<?= $row['tipe_request'] == "s" ? "":"hidden"  ?> btnp-1" href="<?= base_url('req_permission/cut/').$row['pegawai_id'].'/'.$row['tanggal_request'].'/'.$row['tanggal_request_end'] ?>" title="payroll">
                <i class="ti ti-scissors"></i>
              </a>
              <a href="<?=base_url('req_permission/print/'.$row['request_izin_id']);?>" class="btn p-1">
                <i class="ti ti-file"></i>
              </a>
              <div class="modal fade" id="delRow<?=$row['request_izin_id'];?>" tabindex="-1" aria-hidden="true">
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
                        <a href="<?=base_url('req_permission/hapus/'.$row['request_izin_id']);?>" class="btn btn-danger">Ya, Hapus</a>
                      </div>
                  </div>
                </div>
              </div>
            </td>
            <!-- <td><?= $row['status'];?></td>
            <td align="right">
              <a href="<?=base_url('req_permission/edit/'.$row['id']);?>" class="btn p-1">
                <i class="ti ti-edit"></i>
              </a>
              <a href="#" class="btn p-1" data-bs-toggle="modal" data-bs-target="#delRow<?=$row['id'];?>">
                <i class="ti ti-trash"></i>
              </a>
              <div class="modal fade" id="delRow<?=$row['id'];?>" tabindex="-1" aria-hidden="true">
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
                        <a href="<?=base_url('req_permission/hapus/'.$row['id']);?>" class="btn btn-danger">Ya, Hapus</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </td> -->
          </tr>
          <?php $no++; endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<!-- / Content -->

<!-- Modal -->
<div class="modal fade" id="optiondataModalPermitReq" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
    <div class="modal-content p-2 p-md-3" id="content_option_modal_permitrqe"></div>
  </div>
</div>

<script type="text/javascript">
  function filtertglRkp(){
    var valx = $('.filtertglrkp').val();
    var valx2 = $('.filtertglrkp2').val();
    window.location.href='<?=base_url('req_permission/index/');?>'+valx+'/'+valx2;
  }

  /*

  $(document).ready(function () {
    $('#flatpickr-date2').flatpickr({
      maxDate: "<?=$today;?>"
    });
    $('#flatpickr-date').flatpickr({
      maxDate: "<?=$today;?>"
    });
  });

  */

  function action_permit_req(a,b){
    $('#optiondataModalPermitReq').modal('toggle');
    $('#content_option_modal_permitrqe').html('Loading...');
    $.get('<?=base_url('req_permission/action/');?>'+a+'/'+b, function(data) {
      $('#content_option_modal_permitrqe').html(data);
    });
  }
</script>