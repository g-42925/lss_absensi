<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title"><?=$namalabel;?></h5>
      <div class="text-start">
        <a href="<?=base_url('karyawan/timework');?>" class="btn btn-label-secondary btn-sm me-1"><i class="ti ti-chevron-left me-md-1"></i> Kembali</a>
        <a href="<?=base_url('karyawan/timework/add/'.$id);?>" class="btn btn-primary btn-sm"><i class="ti ti-plus me-md-1"></i> Tambah Data</a>
      </div>
    </div>
    <div class="card-datatable table-responsive">
      <?=$this->session->flashdata('message');?>
      <table class="table border-top" id="dataTable">
        <thead>
          <tr>
            <th>Nama Pola</th>
            <th>Mulai Berlaku</th>
            <th>Jumlah Hari</th>
            <th>Kerja</th>
            <th>Libur</th>
            <th>Status</th>
            <th class="text-end">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php $no=1; foreach ($datas as $row) : ?>
          <tr>
            <td><?= $row['nama_pola'];?></td>
            <td><?= indo($row['mulai_berlaku_tanggal']);?></td>
            <td><?= $row['jumlah_hari_siklus'];?> Hari</td>
            <td><?= $row['jumlah_kerja'];?> Hari</td>
            <td><?= $row['jumlah_libur'];?> Hari</td>
            <td>
              <?php if ($no==1) { ?>
              <span class="btn btn-label-primary btn-sm ft-11">Aktif</span>
              <?php } ?>
            </td>
            <td align="right">
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
                        <a href="<?=base_url('karyawan/timework/hapus_record/'.$id.'/'.$row['pegawai_pola_id']);?>" class="btn btn-danger">Ya, Hapus</a>
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
