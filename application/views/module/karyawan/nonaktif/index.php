<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-datatable table-responsive">
      <?php if ($failed): ?>
        <?=$this->session->flashdata('message');?>
      <?php endif; ?>
      <table class="table border-top" id="dataTable">
        <thead>
          <tr class="text-center">
            <th class="w-s-n">Nik</th>
            <th class="w-s-n">Divisi</th>
            <th class="w-s-n">Nama Lengkap</th>
            <th class="w-s-n">No WhatsApp</th>
            <th>Email</th>
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
            <td><?= $row['email_pegawai'];?></td>
            <td>
              <a href="<?= base_url('karyawan/nonaktif/undo/').$row['pegawai_id'] ?>" class="btn p-1">
                <i class="ti ti-arrow-back-up"></i>
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