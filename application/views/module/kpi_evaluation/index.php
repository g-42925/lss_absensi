<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  
  <div class="card mb-4">
    <div class="card-header border-bottom d-flex align-items-center justify-content-between">
      <div>
        <h5 class="card-title mb-0">Evaluasi KPI Karyawan</h5>
        <small class="text-muted">Karyawan: <strong><?= htmlspecialchars($pegawai['nama_pegawai']); ?></strong> (<?= htmlspecialchars($pegawai['nik']); ?>)</small>
      </div>
      <a href="<?= base_url('karyawan/data'); ?>" class="btn btn-sm btn-label-secondary">
        <i class="ti ti-arrow-left me-1"></i> Kembali ke Data Karyawan
      </a>
    </div>
    <div class="card-body mt-3">
      <div class="row align-items-center">
        <div class="col-md-6 mb-3 mb-md-0">
          <div class="d-flex align-items-center gap-3">
            <div class="avatar avatar-lg bg-label-primary rounded-circle">
              <i class="ti ti-user ti-lg text-primary"></i>
            </div>
            <div>
              <h6 class="mb-0 fw-bold"><?= htmlspecialchars($pegawai['nama_pegawai']); ?></h6>
              <small class="text-muted d-block">
                <?= htmlspecialchars($pegawai['division_name']); ?> 
                <?= $pegawai['position_name'] ? ' - ' . htmlspecialchars($pegawai['position_name']) : ''; ?>
              </small>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <form action="<?= base_url('kpi_evaluation/form/' . $pegawai['pegawai_id']); ?>" method="POST" class="d-flex align-items-center gap-2 justify-content-md-end">
            <div>
              <select name="bulan" class="form-select" required>
                <option value="" disabled selected>Pilih Bulan</option>
                <?php
                  $months = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                  ];
                  $curr_month = date('n');
                  foreach ($months as $num => $name) {
                    $sel = ($num == $curr_month) ? 'selected' : '';
                    echo "<option value=\"$num\" $sel>$name</option>";
                  }
                ?>
              </select>
            </div>
            <div>
              <select name="tahun" class="form-select" required>
                <option value="" disabled selected>Tahun</option>
                <?php
                  $curr_year = date('Y');
                  for ($y = $curr_year; $y >= $curr_year - 5; $y--) {
                    echo "<option value=\"$y\">$y</option>";
                  }
                ?>
              </select>
            </div>
            <button type="submit" class="btn btn-primary text-nowrap">
              <i class="ti ti-plus me-1"></i> Buat / Lihat Penilaian
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Riwayat Penilaian (Histori)</h5>
    </div>

    <?= $this->session->flashdata('message'); ?>

    <div class="card-datatable table-responsive p-3">
      <table class="table border-top" id="dataTable">
        <thead>
          <tr class="text-center">
            <th>#</th>
            <th>Periode</th>
            <th>Total Nilai Akhir</th>
            <th>Catatan</th>
            <th>Tanggal Dibuat</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1; foreach ($history as $r): ?>
          <tr class="text-center align-middle">
            <td><?= $no++; ?></td>
            <td>
              <span class="badge bg-label-primary fw-bold">
                <?= $months[$r['periode_bulan']] . ' ' . $r['periode_tahun']; ?>
              </span>
            </td>
            <td>
              <?php
                $score = floatval($r['total_nilai']);
                $badge_class = 'bg-label-success';
                if ($score < 50) $badge_class = 'bg-label-danger';
                else if ($score < 75) $badge_class = 'bg-label-warning';
                else if ($score >= 90) $badge_class = 'bg-primary text-white';
              ?>
              <span class="badge <?= $badge_class; ?> fs-6">
                <?= number_format($score, 2); ?>
              </span>
            </td>
            <td>
              <small class="d-block text-truncate" title="<?= htmlspecialchars($r['catatan']); ?>">
                <?= $r['catatan'] ? htmlspecialchars($r['catatan']) : '-'; ?>
              </small>
            </td>
            <td>
              <small><?= date('d M Y H:i', strtotime($r['created_at'])); ?></small>
            </td>
            <td>
              <div class="d-flex gap-1 justify-content-center">
                <a href="<?= base_url('kpi_evaluation/form/' . $pegawai['pegawai_id'] . '/' . $r['periode_bulan'] . '/' . $r['periode_tahun']); ?>" class="btn btn-sm btn-icon btn-outline-info" title="Detail / Edit">
                  <i class="ti ti-eye"></i>
                </a>
                <a href="<?= base_url('kpi_evaluation/delete/' . $r['id'] . '/' . $pegawai['pegawai_id']); ?>" class="btn btn-sm btn-icon btn-outline-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus riwayat penilaian bulan ini?');">
                  <i class="ti ti-trash"></i>
                </a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($history)): ?>
          <tr>
            <td colspan="7" class="text-center text-muted py-5">
              <i class="ti ti-history ti-xl d-block mb-2 opacity-50"></i>
              Belum ada riwayat penilaian KPI untuk karyawan ini.
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
      order: [[1, 'desc']], // Urutkan berdasarkan kolom periode descending jika di-parsing, tapi karena teks, bisa kurang akurat. Idealnya data array disusun dari SQL.
      columnDefs: [{ orderable: false, targets: [6] }]
    });
  });
</script>
