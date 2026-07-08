<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
      <h5 class="card-title mb-0">Daftar Surat Peringatan</h5>
      <a href="<?= base_url('warning/add'); ?>" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> Tambah SP
      </a>
    </div>

    <?= $this->session->flashdata('message'); ?>

    <div class="card-datatable table-responsive p-3">
      <table class="table border-top" id="dataTable">
        <thead>
          <tr class="text-center">
            <th>#</th>
            <th>Karyawan</th>
            <th>No. SP</th>
            <th>Level</th>
            <th>Judul</th>
            <th>Deskripsi Pelanggaran</th>
            <th>Tgl. Kejadian</th>
            <th>Dibuat</th>
            <th>...</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1; foreach ($data as $r): ?>
          <tr class="text-center align-middle">
            <td><?= $no++; ?></td>
            <td class="text-start">
              <strong><?= htmlspecialchars($r['nama_pegawai']); ?></strong><br>
              <small class="text-muted"><?= htmlspecialchars($r['id_pegawai']); ?></small>
            </td>
            <td>
              <span class="badge bg-label-secondary fw-semibold"><?= htmlspecialchars($r['sp_number']); ?></span>
            </td>
            <td>
              <?php
                $lvl = (int)$r['level'];
                $lvlClass = $lvl === 1 ? 'bg-label-warning' : ($lvl === 2 ? 'bg-label-danger' : 'bg-label-dark');
                $lvlLabel = 'SP ' . $lvl;
              ?>
              <span class="badge <?= $lvlClass; ?>"><?= $lvlLabel; ?></span>
            </td>
            <td class="text-start"><?= htmlspecialchars($r['title']); ?></td>
            <td class="text-start" style="max-width:200px;">
              <span class="text-truncate d-block" style="max-width:180px;" title="<?= htmlspecialchars($r['violation']); ?>">
                <?= htmlspecialchars($r['violation']); ?>
              </span>
            </td>
            <td><?= date('d M Y', strtotime($r['date'])); ?></td>
            <td><small><?= date('d M Y', strtotime($r['createdAt'])); ?></small></td>
            <td>
                <div class="d-flex gap-1 justify-content-center">
                    <a href="<?= base_url('warning/edit/' . $r['id']); ?>" class="btn btn-sm btn-icon btn-outline-primary" title="Edit">
                        <i class="ti ti-pencil"></i>
                    </a>
                    <a href="<?= base_url('warning/print/' . $r['id']); ?>" class="btn btn-sm btn-icon btn-primary" title="Cetak">
                        <i class="ti ti-printer"></i>
                    </a>
                </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($data)): ?>
          <tr>
            <td colspan="8" class="text-center text-muted py-4">
              Belum ada surat peringatan. <a href="<?= base_url('warning/add'); ?>">Tambah sekarang</a>
            </td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<!-- / Content -->

<script type="text/javascript">
  $(document).ready(function () {
    $('#dataTable').DataTable({
      order: [[6, 'desc']],
      columnDefs: [{ orderable: false, targets: [5] }]
    });
  });
</script>
