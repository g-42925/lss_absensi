<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom flex flex-row gap-3 items-center">
      <h5 class="m-0 flex-grow-1">Active Login Sessions</h5>
      <?php if (!empty($datas)): ?>
      <button class="bg-red-600 text-white p-3 rounded-md ms-auto hover:bg-red-700" data-bs-toggle="modal" data-bs-target="#kickAllModal">
        <i class="ti ti-user-x"></i> Kick All
      </button>
      <?php endif; ?>
      <a class="bg-gray-600 text-white p-3 rounded-md hover:bg-gray-700" href="<?=base_url('karyawan/data');?>">Back</a>
    </div>    
    <div class="card-datatable table-responsive">
      <?php if($this->session->flashdata('message')): ?>
        <div class="px-4 py-3">
          <?= $this->session->flashdata('message') ?>
        </div>
      <?php endif; ?>
      <table class="table border-top" id="dataTable">
        <thead>
          <tr class="text-center">
            <th class="w-s-n">Nik</th>
            <th class="w-s-n">Divisi</th>
            <th class="w-s-n ">Nama Lengkap</th>
            <th class="w-s-n">No WhatsApp</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php $no=1; foreach ($datas as $row) : ?>
          <tr class="text-center">
            <td><?= $row['nik'];?></td>
            <td><?= $row['divisi'];?></td>
            <td><?= $row['nama_pegawai'];?></td>
            <td><?= $row['nomor_pegawai'];?></td>
            <td>
              <a href="<?= base_url('karyawan/data/kick/').$row['pegawai_id'] ?>" class="btn p-1 text-red-600 hover:text-red-800" title="kick">
                <i class="ti ti-user-x"></i> Kick
              </a>
            </td>
          </tr>
          <?php $no++; endforeach; ?>
          <?php if (empty($datas)): ?>
          <tr>
            <td colspan="5" class="text-center py-4 text-gray-500">Tidak ada karyawan yang sedang login/aktif.</td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Kick All -->
<div class="modal fade" id="kickAllModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3 class="mb-2">Konfirmasi</h3>
          <p>Yakin ingin men-kick (logout paksa) <b>seluruh</b> karyawan yang aktif?</p>
        </div>
        <div class="col-12 text-center pt-3 flex flex-row justify-center gap-3">
          <button type="button" class="bg-gray-300 text-black px-4 py-2 rounded-md" data-bs-dismiss="modal">
            Batal
          </button>
          <a href="<?=base_url('karyawan/data/kick_all');?>" class="bg-red-600 text-white px-4 py-2 rounded-md">Ya, Kick Semua</a>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- / Content -->
