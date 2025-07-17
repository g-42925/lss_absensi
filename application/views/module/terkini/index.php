<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-2"><?=$namalabel;?></h5>
      <div class="row">
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
      </div>
    </div>
    <div class="card-datatable table-responsive">
      <?=$this->session->flashdata('message');?>
      <table class="table border-top" id="dataTableatt">
        <thead>
          <tr>
            <th>Nama</th>
            <th>Tanggal</th>
            <th>Jam</th>
            <th>Lat & Lng</th>
            <th class="text-right">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php 
            $no=1; foreach ($datas as $row) : 
          ?>
          <tr>
            <td class="w-s-n"><?=$row['nama_pegawai'];?></td>
            <td class="w-s-n"><?=indo($row['tanggal']);?></td>
            <td class="w-s-n"><?=$row['jam'];?></td>
            <td>
              <small><?=$row['latitude_lt'];?>, <?=$row['longitude_lt'];?><br>
              <?=$row['alamat_lengkap'];?>
              </small>
            </td>
            <td align="right">
              <a href="javascript:;" onclick="action_data_terkini('<?=$row['lt_id'];?>','map');" class="btn p-1" data-toggle="tooltip" title="Lihat Map">
                <i class="ti ti-map-pin"></i>
              </a>
              <a href="javascript:;" onclick="action_data_terkini('<?=$row['lt_id'];?>','photo');"class="btn p-1" data-toggle="tooltip" title="Lihat Foto Absen">
                <i class="ti ti-photo"></i>
              </a>
              <a href="javascript:;" onclick="action_data_terkini('<?=$row['lt_id'];?>','catatan');" class="btn p-1" data-toggle="tooltip" title="Lihat Catatan">
                <i class="ti ti-note"></i>
              </a>
              <a href="#" class="btn p-1" data-bs-toggle="modal" data-bs-target="#delRow<?=$row['lt_id'];?>">
                <i class="ti ti-trash"></i>
              </a>
              <!-- Konfirmasi Hapus -->
              <div class="modal fade" id="delRow<?=$row['lt_id'];?>" tabindex="-1" aria-hidden="true">
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
                        <a href="<?=base_url('terkini/hapus/'.$row['lt_id']);?>" class="btn btn-danger">Ya, Hapus</a>
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
<div class="modal fade" id="optiondataModalTerkini" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
    <div class="modal-content p-2 p-md-3" id="content_option_modal_terkini"></div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function () {
    $('#flatpickr-date2').flatpickr({
      maxDate: "<?=$today;?>"
    });
    $('#flatpickr-date').flatpickr({
      maxDate: "<?=$today;?>"
    });
  });

  function filtertglRkp(){
    var valx = $('.filtertglrkp').val();
    var valx2 = $('.filtertglrkp2').val();
    window.location.href='<?=base_url('terkini/index/');?>'+valx+'/'+valx2;
  }

  function action_data_terkini(a,b,c){
    $('#optiondataModalTerkini').modal('toggle');
    $('#content_option_modal_terkini').html('Loading...');
    $.get('<?=base_url('terkini/action/');?>'+a+'/'+b+'/'+c, function(data) {
      $('#content_option_modal_terkini').html(data);
    });
  }
</script>

