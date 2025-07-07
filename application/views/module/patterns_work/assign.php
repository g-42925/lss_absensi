<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title"><?=$namalabel;?></h5>
      <div class="text-start">
        <a href="<?=base_url('patterns_work');?>" class="btn btn-label-secondary btn-sm me-1"><i class="ti ti-chevron-left me-md-1"></i> Kembali</a>
        <a href="#" data-bs-toggle="modal" data-bs-target="#addGroupLoc" class="btn btn-primary btn-sm"><i class="ti ti-plus me-md-1"></i> Tambah Data</a>
      </div>
    </div>
    <div class="card-datatable table-responsive">
      <?=$this->session->flashdata('message');?>
      <table class="table border-top" id="dataTable">
        <thead>
          <tr>
            <th>ID&nbsp;Karyawan</th>
            <th>Nama&nbsp;Karyawan</th>
            <th>Riwayat&nbsp;Pola</th>
            <th class="text-end">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php $no=1; foreach ($datas as $row) : ?>
          <tr>
            <td><?= $row['id_pegawai'];?></td>
            <td><?= $row['nama_pegawai'];?></td>
            <td><?= $row['totalpola'];?></td>
            <td align="right">
              <a href="<?=base_url('karyawan/timework/record/'.$row['pegawai_id']);?>" class="btn p-1">
                <i class="ti ti-eye"></i>
              </a>
              <a href="#" class="btn p-1" data-bs-toggle="modal" data-bs-target="#delRow<?=$row['pegawai_pola_id'];?>">
                <i class="ti ti-trash"></i>
              </a>
              <!-- Konfirmasi Hapus -->
              <div class="modal fade" id="delRow<?=$row['pegawai_pola_id'];?>" tabindex="-1" aria-hidden="true">
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
                        <a href="<?=base_url('patterns_work/hapus_assign/'.$id.'/'.$row['pegawai_pola_id']);?>" class="btn btn-danger">Ya, Hapus</a>
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

<div class="modal fade" id="addGroupLoc" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <form class="card-body" action="<?=base_url('patterns_work/assign_proses/'.$id);?>" method="POST">
          <div class="mb-4">
            <div class="mb-2">
              <input type="hidden" class="form-control" name="pola" value="<?=$id;?>" required />
              <label class="form-label ft-14">
                Karyawan
              </label>
              <div class="pb-3">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="selectAll()">Select All</button>
                <button type="button" class="btn btn-outline-warning btn-sm" onclick="deselectAll()">Deselect All</button>
              </div>
              <select class="select2 form-select" id="my-select-kary" name="idp[]" required multiple>
                <?php foreach ($karyawan as $row) : ?>
                <option value="<?=$row['pegawai_id'];?>"><?=$row['nama_pegawai'];?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="row">
              <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6" id="mberlaku">
                <label class="form-label">Mulai Berlaku<i class="text-danger">*</i></label>
                <input type="text" class="form-control" name="tglmulai" autocomplete="off" placeholder="YYYY-MM-DD" onchange="checkharipola()" id="flatpickr-date" required />
              </div>
              <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6" id="mhberlaku">
                <label class="form-label">Hari ( dari pola kerja )<i class="text-danger">*</i></label>
                <input type="text" class="form-control" name="harike" id="harikeid" value="1" placeholder="..." required="" onkeyup="checkharipola()" />
              </div>
              <div class="col-xl-12 col-md-12 mt-3">
                <div class="card-datatable table-responsive" id="res_pola_set"></div>
              </div>
            </div>
          </div>
          <div class="col-12 text-center pt-3">
            <button
              type="button"
              class="btn btn-label-secondary me-sm-3 me-1"
              data-bs-dismiss="modal"
              aria-label="Close">
              Batal
            </button>
            <button
              type="submit"
              class="btn btn-primary">
              Simpan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- / Content -->

<script type="text/javascript">
  $(document).ready(function() {
      $("#my-select-kary").select2();
      $('#flatpickr-date').flatpickr({
        minDate: "today"
      });
      checkPola();
  });

  function selectAll() {
      $("#my-select-kary > option").prop("selected", true);
      $("#my-select-kary").trigger("change");
  }

  function deselectAll() {
      $("#my-select-kary > option").prop("selected", false);
      $("#my-select-kary").trigger("change");
  }

  function checkPola(){
    $('#res_pola_set').html('Loading....');
    $.get('<?=base_url('karyawan/timework/pola/'.$id)?>', function(data) {
      $('#res_pola_set').html(data);
    });
  }

</script>