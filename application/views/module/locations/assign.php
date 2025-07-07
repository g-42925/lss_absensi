<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title"><?=$namalabel;?></h5>
      <div class="text-start">
        <a href="<?=base_url('locations');?>" class="btn btn-label-secondary btn-sm me-1"><i class="ti ti-chevron-left me-md-1"></i> Kembali</a>
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
            <th class="text-end">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php $no=1; foreach ($datas as $row) : ?>
          <tr>
            <td><?= $row['id_pegawai'];?></td>
            <td><?= $row['nama_pegawai'];?></td>
            <td align="right">
              <a href="#" class="btn p-1" data-bs-toggle="modal" data-bs-target="#delRow<?=$row['pegawai_lokasi_id'];?>">
                <i class="ti ti-trash"></i>
              </a>
              <!-- Konfirmasi Hapus -->
              <div class="modal fade" id="delRow<?=$row['pegawai_lokasi_id'];?>" tabindex="-1" aria-hidden="true">
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
                        <a href="<?=base_url('locations/hapus_assign/'.$id.'/'.$row['pegawai_lokasi_id']);?>" class="btn btn-danger">Ya, Hapus</a>
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
  <div class="modal-dialog modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <form class="card-body" action="<?=base_url('locations/assign_proses/'.$id);?>" method="POST">
          <div class="mb-4">
            <div class="">
              <label class="form-label ft-14">
                Karyawan
              </label>
              <div class="pb-2">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="selectAll()">Select All</button>
                <button type="button" class="btn btn-outline-warning btn-sm" onclick="deselectAll()">Deselect All</button>
              </div>
              <select class="select2 form-select" id="my-select-kary" name="idp[]" required multiple>
                <?php foreach ($karyawan as $row) : ?>
                <option value="<?=$row['pegawai_id'];?>"><?=$row['nama_pegawai'];?></option>
                <?php endforeach; ?>
              </select>
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
  });

  function selectAll() {
      $("#my-select-kary > option").prop("selected", true);
      $("#my-select-kary").trigger("change");
  }

  function deselectAll() {
      $("#my-select-kary > option").prop("selected", false);
      $("#my-select-kary").trigger("change");
  }
</script>