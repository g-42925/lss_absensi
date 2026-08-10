<?php
// ── Helper functions untuk view ──────────────────────────
$months = [
    1 => 'Januari',    2 => 'Februari', 3 => 'Maret',
    4 => 'April',      5 => 'Mei',       6 => 'Juni',
    7 => 'Juli',       8 => 'Agustus',   9 => 'September',
    10 => 'Oktober',  11 => 'November', 12 => 'Desember',
];

function kpi_badge_persen($val) {
    $v = floatval($val);
    if ($v >= 95) return ['bg-label-success',  'Sangat Baik'];
    if ($v >= 85) return ['bg-label-primary',  'Baik'];
    if ($v >= 75) return ['bg-label-info',     'Cukup'];
    if ($v >= 60) return ['bg-label-warning',  'Perlu Perhatian'];
    return                ['bg-label-danger',  'Buruk'];
}
?>

<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">

  <!-- Page Header -->
  <div class="row mb-4 align-items-center">
    <div class="col">
      <h4 class="fw-bold mb-1">
        <i class="ti ti-chart-bar me-2 text-primary"></i>KPI Karyawan Berbasis Absensi
      </h4>
      <p class="text-muted mb-0">
        Penilaian performa bulanan otomatis berdasarkan data absensi.
        <span class="badge bg-label-warning ms-1">
          <i class="ti ti-clock me-1"></i>Periode aktif: <strong><?= $nama_bulan . ' ' . $tahun; ?></strong>
        </span>
      </p>
    </div>
    <div class="col-auto d-flex gap-2">
      <!-- Export CSV -->
      <a href="<?= base_url('kpi_absensi/export/' . $bulan . '/' . $tahun); ?>"
         class="btn btn-sm btn-label-success" title="Download CSV">
        <i class="ti ti-download me-1"></i>Export CSV
      </a>
    </div>
  </div>

  <?= $this->session->flashdata('message'); ?>

  <!-- Filter Periode + Generate Bulk -->
  <div class="card mb-4 shadow-sm border-0">
    <div class="card-body py-3">
      <div class="row align-items-center g-3">
        <!-- Filter Form -->
        <div class="col-md-7">
          <form method="POST" action="<?= base_url('kpi_absensi'); ?>" class="d-flex align-items-center gap-2 flex-wrap">
            <label class="fw-semibold text-muted mb-0 me-1">Filter Periode:</label>
            <select name="bulan" class="form-select form-select-sm w-auto">
              <?php foreach ($months as $n => $nm): ?>
                <option value="<?= $n; ?>" <?= $n == $bulan ? 'selected' : ''; ?>><?= $nm; ?></option>
              <?php endforeach; ?>
            </select>
            <select name="tahun" class="form-select form-select-sm w-auto">
              <?php
                $curr_year = date('Y');
                for ($y = $curr_year; $y >= $curr_year - 4; $y--):
              ?>
                <option value="<?= $y; ?>" <?= $y == $tahun ? 'selected' : ''; ?>><?= $y; ?></option>
              <?php endfor; ?>
            </select>
            <button type="submit" class="btn btn-sm btn-primary">
              <i class="ti ti-filter me-1"></i>Tampilkan
            </button>
          </form>
        </div>

        <!-- Generate Bulk -->
        <div class="col-md-5 text-md-end">
          <div class="d-flex align-items-center justify-content-md-end gap-2 flex-wrap">
            <span class="text-muted small">
              <i class="ti ti-database me-1"></i>
              Tersimpan: <strong class="text-primary"><?= $total_generated; ?></strong> / <?= $total_pegawai; ?> karyawan
            </span>
            <form method="POST" action="<?= base_url('kpi_absensi/generate'); ?>" id="bulkGenerateForm">
              <input type="hidden" name="bulan" value="<?= $bulan; ?>">
              <input type="hidden" name="tahun" value="<?= $tahun; ?>">
              <button type="button" class="btn btn-sm btn-warning" onclick="confirmGenerate()"
                      id="btnGenerate" title="Hitung & simpan KPI semua karyawan periode ini">
                <i class="ti ti-refresh me-1"></i>Generate Semua
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Stat Summary Cards -->
  <div class="row g-3 mb-4">
    <?php
      $total_hadir   = array_sum(array_column(array_filter(array_column($datas, 'kpi')), 'hari_hadir'));
      $total_alpha   = array_sum(array_column(array_filter(array_column($datas, 'kpi')), 'hari_alpha'));
      $total_terlambat = array_sum(array_column(array_filter(array_column($datas, 'kpi')), 'jumlah_terlambat'));
      $avg_kehadiran = 0;
      $count_gen = 0;
      foreach ($datas as $d) {
          if ($d['kpi']) {
              $avg_kehadiran += floatval($d['kpi']['persen_kehadiran']);
              $count_gen++;
          }
      }
      $avg_kehadiran = $count_gen > 0 ? round($avg_kehadiran / $count_gen, 1) : 0;
    ?>
    <div class="col-sm-6 col-xl-3">
      <div class="card text-center h-100 border-0 shadow-sm">
        <div class="card-body">
          <div class="avatar avatar-lg bg-label-primary rounded-circle mx-auto mb-3">
            <i class="ti ti-users ti-lg text-primary"></i>
          </div>
          <h3 class="fw-bold mb-0 text-primary"><?= $total_pegawai; ?></h3>
          <small class="text-muted">Total Karyawan</small>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card text-center h-100 border-0 shadow-sm">
        <div class="card-body">
          <div class="avatar avatar-lg bg-label-success rounded-circle mx-auto mb-3">
            <i class="ti ti-chart-line ti-lg text-success"></i>
          </div>
          <h3 class="fw-bold mb-0 text-success"><?= $avg_kehadiran; ?>%</h3>
          <small class="text-muted">Rata-rata Kehadiran</small>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card text-center h-100 border-0 shadow-sm">
        <div class="card-body">
          <div class="avatar avatar-lg bg-label-danger rounded-circle mx-auto mb-3">
            <i class="ti ti-user-off ti-lg text-danger"></i>
          </div>
          <h3 class="fw-bold mb-0 text-danger"><?= $total_alpha; ?></h3>
          <small class="text-muted">Total Hari Alpha</small>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card text-center h-100 border-0 shadow-sm">
        <div class="card-body">
          <div class="avatar avatar-lg bg-label-warning rounded-circle mx-auto mb-3">
            <i class="ti ti-clock-exclamation ti-lg text-warning"></i>
          </div>
          <h3 class="fw-bold mb-0 text-warning"><?= $total_terlambat; ?></h3>
          <small class="text-muted">Total Keterlambatan</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Table -->
  <div class="card border-0 shadow-sm">
    <div class="card-header border-bottom d-flex align-items-center justify-content-between py-3">
      <h5 class="mb-0">
        <i class="ti ti-table me-1 text-primary"></i>
        KPI Karyawan – <?= $nama_bulan . ' ' . $tahun; ?>
      </h5>
      <small class="text-muted">* Klik "Detail" untuk melihat breakdown harian</small>
    </div>
    <div class="card-datatable table-responsive">
      <table class="table table-hover border-top" id="kpiTable">
        <thead class="table-light">
          <tr class="text-center text-nowrap">
            <th class="text-start">#</th>
            <th class="text-start">Karyawan</th>
            <th class="text-start">Statistik Kehadiran</th>
            <th class="text-start">Performa Waktu</th>
            <th>Final KPI</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1; foreach ($datas as $row): ?>
          <?php $kpi = $row['kpi']; ?>
          <tr class="align-middle <?= !$kpi ? 'table-secondary' : ''; ?>">
            <td><?= $no++; ?></td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="avatar avatar-sm bg-label-primary rounded-circle flex-shrink-0">
                  <i class="ti ti-user text-primary" style="font-size:14px;"></i>
                </div>
                <div>
                  <span class="fw-semibold d-block"><?= htmlspecialchars($row['nama_pegawai']); ?></span>
                  <small class="text-muted"><?= htmlspecialchars($row['nik']); ?>
                    <?php if ($row['division_name']): ?>
                      · <?= htmlspecialchars($row['division_name']); ?>
                    <?php endif; ?>
                  </small>
                </div>
              </div>
            </td>

            <?php if ($kpi): ?>
            <td>
              <div class="d-flex flex-column gap-1 text-start">
                <small class="text-muted mb-1">Hari Kerja Efektif: <strong><?= $kpi['hari_kerja_efektif']; ?></strong></small>
                <div class="d-flex gap-1 flex-wrap">
                  <span class="badge bg-label-success" title="Hadir">H: <?= $kpi['hari_hadir']; ?></span>
                  <span class="badge bg-label-info" title="Izin">I: <?= $kpi['hari_izin']; ?></span>
                  <span class="badge bg-label-warning" title="Sakit">S: <?= $kpi['hari_sakit']; ?></span>
                  <span class="badge bg-label-secondary" title="Cuti">C: <?= $kpi['hari_cuti']; ?></span>
                  <span class="badge <?= $kpi['hari_alpha'] > 0 ? 'bg-label-danger' : 'bg-label-success'; ?>" title="Alpha">
                    A: <?= $kpi['hari_alpha']; ?>
                  </span>
                </div>
              </div>
            </td>
            <td>
              <div class="d-flex flex-column gap-1 text-start" style="min-width: 160px;">
                <div class="d-flex justify-content-between">
                  <small class="text-muted">Tepat Masuk:</small>
                  <?php [$badge2] = kpi_badge_persen($kpi['persen_tepat_waktu_masuk']); ?>
                  <small class="fw-bold text-<?= str_replace('bg-label-', '', $badge2); ?>"><?= number_format($kpi['persen_tepat_waktu_masuk'], 1); ?>%</small>
                </div>
                <div class="d-flex justify-content-between mt-1 pt-1 border-top">
                  <small class="text-muted">Keterlambatan:</small>
                  <small class="fw-bold <?= $kpi['jumlah_terlambat'] > 0 ? 'text-danger' : 'text-success'; ?>">
                    <?= $kpi['jumlah_terlambat']; ?>x (<?= $kpi['rata_menit_terlambat'] > 0 ? number_format($kpi['rata_menit_terlambat'], 0) . 'm' : '-'; ?> avg)
                  </small>
                </div>
              </div>
            </td>
            <td>
              <div class="d-flex flex-column align-items-center gap-1">
                <?php [$badge_kpi, $label_kpi] = kpi_badge_persen($kpi['kpi_score']); ?>
                <span class="badge <?= $badge_kpi; ?> fs-6 mb-1" title="Nilai Akhir: <?= $label_kpi; ?>">
                  <?= number_format($kpi['kpi_score'], 1); ?>
                </span>
                <div class="d-flex gap-2">
                  <small class="text-muted" title="% Kehadiran"><i class="ti ti-chart-pie me-1"></i><?= number_format($kpi['persen_kehadiran'], 1); ?>%</small>
                  <small class="<?= $kpi['jumlah_sp'] > 0 ? 'text-danger fw-bold' : 'text-muted'; ?>" title="Surat Peringatan"><i class="ti ti-alert-triangle me-1"></i><?= $kpi['jumlah_sp']; ?></small>
                </div>
              </div>
            </td>
            <?php else: ?>
            <td colspan="4" class="text-center text-muted">
              <small><i class="ti ti-alert-circle me-1 text-warning"></i>Belum di-generate</small>
            </td>
            <?php endif; ?>

            <td class="text-center">
              <div class="d-flex gap-1 justify-content-center flex-nowrap">
                <a href="<?= base_url('kpi_absensi/detail/' . $row['pegawai_id'] . '/' . $bulan . '/' . $tahun); ?>"
                   class="btn btn-sm btn-icon btn-outline-info" title="Detail KPI">
                  <i class="ti ti-eye"></i>
                </a>
                <a href="<?= base_url('kpi_absensi/generate_one/' . $row['pegawai_id'] . '/' . $bulan . '/' . $tahun); ?>"
                   class="btn btn-sm btn-icon btn-outline-warning"
                   title="<?= $kpi ? 'Generate Ulang KPI' : 'Generate KPI'; ?>"
                   onclick="return confirm('Generate/simpan ulang KPI untuk <?= htmlspecialchars(addslashes($row['nama_pegawai'])); ?>?')">
                  <i class="ti ti-refresh"></i>
                </a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($datas)): ?>
          <tr>
            <td colspan="7" class="text-center text-muted py-5">
              <i class="ti ti-user-off ti-xl d-block mb-2 opacity-50"></i>
              Tidak ada data karyawan.
            </td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>
<!-- / Content -->

<script>
  function confirmGenerate() {
    if (confirm('Generate KPI untuk SEMUA karyawan periode <?= $nama_bulan . ' ' . $tahun; ?>?\n\nData yang sudah ada akan ditimpa.')) {
      document.getElementById('btnGenerate').innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Proses...';
      document.getElementById('btnGenerate').disabled = true;
      document.getElementById('bulkGenerateForm').submit();
    }
  }

  $(document).ready(function () {
    $('#kpiTable').DataTable({
      order: [[5, 'desc']], // Sort by KPI Score desc
      columnDefs: [
        { orderable: false, targets: [6] } // kolom aksi
      ],
      language: {
        search: 'Cari:',
        lengthMenu: 'Tampilkan _MENU_ data',
        info: 'Menampilkan _START_–_END_ dari _TOTAL_ karyawan',
        paginate: { previous: '«', next: '»' },
        emptyTable: 'Tidak ada data.',
        zeroRecords: 'Data tidak ditemukan.'
      }
    });
  });
</script>
