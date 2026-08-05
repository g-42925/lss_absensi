<?php
$months = [
    1 => 'Januari',    2 => 'Februari', 3 => 'Maret',
    4 => 'April',      5 => 'Mei',       6 => 'Juni',
    7 => 'Juli',       8 => 'Agustus',   9 => 'September',
    10 => 'Oktober',  11 => 'November', 12 => 'Desember',
];

$status_label = [
    'hhk'     => ['Hadir',          'bg-label-success'],
    'hbhk'    => ['Hadir (non-HK)', 'bg-label-success'],
    'i'       => ['Izin',           'bg-label-info'],
    's'       => ['Sakit',          'bg-label-warning'],
    'c'       => ['Cuti',           'bg-label-secondary'],
    'alpha-2' => ['Alpha',          'bg-label-danger'],
    'alpha'   => ['Alpha',          'bg-label-danger'],
    'th'      => ['Alpha',          'bg-label-danger'],
    'on duty' => ['Tugas Luar',     'bg-label-primary'],
    'free'    => ['Bebas',          'bg-label-dark'],
    'l'       => ['Libur',          'bg-label-secondary'],
    'cb'      => ['Libur Bersama',  'bg-label-dark'],
    'ts'      => ['Belum Absen',    'bg-label-secondary'],
    ''        => ['-',              'bg-label-secondary'],
];

function getStatusLabel($status_label, $status) {
    $key = strtolower(trim($status));
    return $status_label[$key] ?? [$status, 'bg-label-secondary'];
}

function formatJam($menit) {
    if ($menit <= 0) return '-';
    $j = floor($menit / 60);
    $m = $menit % 60;
    return ($j > 0 ? $j . 'j ' : '') . $m . 'mnt';
}

function persen_color($val) {
    $v = floatval($val);
    if ($v >= 95) return 'text-success';
    if ($v >= 85) return 'text-primary';
    if ($v >= 75) return 'text-info';
    if ($v >= 60) return 'text-warning';
    return 'text-danger';
}
?>

<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">

  <!-- Back Header -->
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      <a href="<?= base_url('kpi_absensi'); ?>" class="btn btn-sm btn-label-secondary me-2">
        <i class="ti ti-arrow-left me-1"></i>Kembali
      </a>
      <span class="text-muted small">
        <i class="ti ti-chart-bar mx-1"></i>KPI Absensi /
        <strong><?= htmlspecialchars($pegawai['nama_pegawai']); ?></strong> /
        <?= $nama_bulan . ' ' . $tahun; ?>
      </span>
    </div>
    <a href="<?= base_url('kpi_absensi/generate_one/' . $pegawai['pegawai_id'] . '/' . $bulan . '/' . $tahun); ?>"
       class="btn btn-sm btn-warning"
       onclick="return confirm('Generate ulang dan simpan KPI ini?')">
      <i class="ti ti-refresh me-1"></i>
      <?= $snapshot ? 'Generate Ulang' : 'Simpan ke DB'; ?>
    </a>
  </div>

  <?= $this->session->flashdata('message'); ?>

  <!-- Employee Info Card -->
  <div class="card mb-4 border-0 shadow-sm">
    <div class="card-body">
      <div class="d-flex align-items-center gap-3">
        <div class="avatar avatar-xl bg-label-primary rounded-circle">
          <i class="ti ti-user ti-lg text-primary"></i>
        </div>
        <div>
          <h5 class="fw-bold mb-1"><?= htmlspecialchars($pegawai['nama_pegawai']); ?></h5>
          <div class="d-flex gap-3 flex-wrap">
            <small class="text-muted"><i class="ti ti-id me-1"></i><?= htmlspecialchars($pegawai['nik']); ?></small>
            <?php if (!empty($pegawai['division_name'])): ?>
            <small class="text-muted"><i class="ti ti-building me-1"></i><?= htmlspecialchars($pegawai['division_name']); ?></small>
            <?php endif; ?>
            <?php if (!empty($pegawai['position_name'])): ?>
            <small class="text-muted"><i class="ti ti-briefcase me-1"></i><?= htmlspecialchars($pegawai['position_name']); ?></small>
            <?php endif; ?>
          </div>
          <div class="mt-2">
            <span class="badge bg-label-warning">
              <i class="ti ti-calendar me-1"></i>Periode: <?= $nama_bulan . ' ' . $tahun; ?>
            </span>
            <?php if ($snapshot): ?>
            <span class="badge bg-label-success ms-1">
              <i class="ti ti-database me-1"></i>Tersimpan <?= date('d M Y H:i', strtotime($snapshot['generated_at'])); ?>
            </span>
            <?php else: ?>
            <span class="badge bg-label-secondary ms-1">
              <i class="ti ti-alert-circle me-1"></i>Belum disimpan ke DB
            </span>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- KPI Summary Stat Cards -->
  <div class="row g-3 mb-4">

    <!-- % Kehadiran & KPI Score -->
    <div class="col-md-4 col-xl-2">
      <div class="card text-center h-100 border-0 shadow-sm">
        <div class="card-body py-4">
          <i class="ti ti-award ti-lg mb-2 text-primary"></i>
          <h3 class="fw-bold <?= persen_color($kpi['kpi_score']); ?> mb-0">
            <?= number_format($kpi['kpi_score'], 1); ?>
          </h3>
          <small class="text-muted d-block">Nilai Akhir KPI</small>
          <hr class="my-2">
          <h5 class="fw-bold <?= persen_color($kpi['persen_kehadiran']); ?> mb-0">
            <?= number_format($kpi['persen_kehadiran'], 1); ?>%
          </h5>
          <small class="text-muted d-block">Kehadiran (SP: <?= $kpi['jumlah_sp']; ?>)</small>
        </div>
      </div>
    </div>

    <!-- Breakdown Status -->
    <div class="col-md-8 col-xl-4">
      <div class="card h-100 border-0 shadow-sm">
        <div class="card-body">
          <h6 class="text-muted mb-3 fw-semibold"><i class="ti ti-list me-1"></i>Rekap Status</h6>
          <div class="row g-2 text-center">
            <div class="col-4">
              <div class="p-2 rounded bg-label-success">
                <div class="fw-bold fs-5"><?= $kpi['hari_hadir']; ?></div>
                <small>Hadir</small>
              </div>
            </div>
            <div class="col-4">
              <div class="p-2 rounded bg-label-info">
                <div class="fw-bold fs-5"><?= $kpi['hari_izin']; ?></div>
                <small>Izin</small>
              </div>
            </div>
            <div class="col-4">
              <div class="p-2 rounded bg-label-warning">
                <div class="fw-bold fs-5"><?= $kpi['hari_sakit']; ?></div>
                <small>Sakit</small>
              </div>
            </div>
            <div class="col-4">
              <div class="p-2 rounded bg-label-secondary">
                <div class="fw-bold fs-5"><?= $kpi['hari_cuti']; ?></div>
                <small>Cuti</small>
              </div>
            </div>
            <div class="col-4">
              <div class="p-2 rounded bg-label-danger">
                <div class="fw-bold fs-5"><?= $kpi['hari_alpha']; ?></div>
                <small>Alpha</small>
              </div>
            </div>
            <div class="col-4">
              <div class="p-2 rounded" style="background: #f0f0f5;">
                <div class="fw-bold fs-5"><?= $kpi['hari_kerja_efektif']; ?></div>
                <small>HK Efektif</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Keterlambatan -->
    <div class="col-md-6 col-xl-3">
      <div class="card h-100 border-0 shadow-sm">
        <div class="card-body">
          <h6 class="text-muted mb-3 fw-semibold"><i class="ti ti-clock-exclamation me-1"></i>Keterlambatan</h6>
          <div class="d-flex flex-column gap-3">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
              <span class="text-muted small">Jumlah Terlambat</span>
              <span class="badge <?= $kpi['jumlah_terlambat'] > 0 ? 'bg-label-danger' : 'bg-label-success'; ?> fw-bold fs-6">
                <?= $kpi['jumlah_terlambat']; ?> kali
              </span>
            </div>
            <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
              <span class="text-muted small">Total Menit Terlambat</span>
              <strong><?= formatJam($kpi['total_menit_terlambat']); ?></strong>
            </div>
            <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
              <span class="text-muted small">Rata-rata / Kejadian</span>
              <strong><?= $kpi['rata_menit_terlambat'] > 0 ? number_format($kpi['rata_menit_terlambat'], 0) . ' mnt' : '-'; ?></strong>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-muted small">% Tepat Waktu Masuk</span>
              <strong class="<?= persen_color($kpi['persen_tepat_waktu_masuk']); ?>">
                <?= number_format($kpi['persen_tepat_waktu_masuk'], 1); ?>%
              </strong>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Jam Kerja -->
    <div class="col-md-6 col-xl-3">
      <div class="card h-100 border-0 shadow-sm">
        <div class="card-body">
          <h6 class="text-muted mb-3 fw-semibold"><i class="ti ti-clock me-1"></i>Jam Kerja & Pulang</h6>
          <div class="d-flex flex-column gap-3">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
              <span class="text-muted small">Total Jam Kerja</span>
              <strong class="text-primary fs-5"><?= number_format($kpi['total_jam_kerja'], 1); ?> jam</strong>
            </div>
            <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
              <span class="text-muted small">Total Menit Kerja</span>
              <span><?= number_format($kpi['total_menit_kerja']); ?> mnt</span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-muted small">% Tepat Waktu Pulang</span>
              <strong class="<?= persen_color($kpi['persen_tepat_waktu_pulang']); ?>">
                <?= number_format($kpi['persen_tepat_waktu_pulang'], 1); ?>%
              </strong>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <!-- Breakdown Harian Table -->
  <div class="card border-0 shadow-sm">
    <div class="card-header border-bottom py-3 d-flex align-items-center justify-content-between">
      <h5 class="mb-0"><i class="ti ti-calendar-stats me-1 text-primary"></i>Breakdown Harian – <?= $nama_bulan . ' ' . $tahun; ?></h5>
      <small class="text-muted"><?= count($kpi['breakdown']); ?> hari tercatat</small>
    </div>
    <div class="table-responsive">
      <table class="table table-sm table-hover border-top" id="breakdownTable">
        <thead class="table-light">
          <tr class="text-center">
            <th class="text-start">Tanggal</th>
            <th>Status</th>
            <th>Jadwal Masuk</th>
            <th>Jam Masuk</th>
            <th>Terlambat</th>
            <th>Jadwal Pulang</th>
            <th>Jam Pulang</th>
            <th>Tepat Pulang</th>
            <th>Jam Kerja</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($kpi['breakdown'] as $row): ?>
          <?php
            [$slabel, $sbadge] = getStatusLabel($status_label, $row['is_status']);
            $is_hadir = in_array(strtolower(trim($row['is_status'])), ['hhk', 'hbhk']);
          ?>
          <tr class="align-middle <?= $row['menit_terlambat'] > 0 ? 'table-warning' : ''; ?>">
            <td>
              <strong><?= date('D', strtotime($row['tanggal'])); ?></strong>
              <span class="ms-1"><?= date('d M Y', strtotime($row['tanggal'])); ?></span>
            </td>
            <td class="text-center">
              <span class="badge <?= $sbadge; ?>"><?= $slabel; ?></span>
            </td>
            <td class="text-center text-muted small">
              <?= $row['j_masuk']
                  ? $row['j_masuk'] . ($row['j_toleransi'] > 0 ? ' (+' . $row['j_toleransi'] . 'mnt)' : '')
                  : '-'; ?>
            </td>
            <td class="text-center">
              <?php if ($row['jam_masuk']): ?>
                <span class="<?= $row['menit_terlambat'] > 0 ? 'text-danger fw-semibold' : 'text-success'; ?>">
                  <?= $row['jam_masuk']; ?>
                </span>
              <?php else: ?>
                <span class="text-muted">-</span>
              <?php endif; ?>
            </td>
            <td class="text-center">
              <?php if ($row['menit_terlambat'] > 0): ?>
                <span class="badge bg-label-danger">
                  <i class="ti ti-alarm me-1"></i><?= $row['menit_terlambat']; ?> mnt
                </span>
              <?php elseif ($is_hadir && $row['jam_masuk']): ?>
                <span class="badge bg-label-success">Tepat</span>
              <?php else: ?>
                <span class="text-muted">-</span>
              <?php endif; ?>
            </td>
            <td class="text-center text-muted small"><?= $row['j_pulang'] ?: '-'; ?></td>
            <td class="text-center">
              <?php if ($row['jam_keluar']): ?>
                <span class="<?= $row['tepat_pulang'] ? 'text-success' : 'text-warning'; ?>">
                  <?= $row['jam_keluar']; ?>
                </span>
              <?php else: ?>
                <span class="text-muted">-</span>
              <?php endif; ?>
            </td>
            <td class="text-center">
              <?php if ($row['jam_keluar'] && $is_hadir): ?>
                <?php if ($row['tepat_pulang']): ?>
                  <span class="badge bg-label-success"><i class="ti ti-check"></i></span>
                <?php else: ?>
                  <span class="badge bg-label-warning" title="Pulang lebih awal"><i class="ti ti-x"></i></span>
                <?php endif; ?>
              <?php else: ?>
                <span class="text-muted">-</span>
              <?php endif; ?>
            </td>
            <td class="text-center">
              <?= $row['menit_kerja'] > 0 ? formatJam($row['menit_kerja']) : '-'; ?>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($kpi['breakdown'])): ?>
          <tr>
            <td colspan="9" class="text-center text-muted py-5">
              <i class="ti ti-calendar-off ti-xl d-block mb-2 opacity-50"></i>
              Tidak ada data absensi untuk periode ini.
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
  $(document).ready(function () {
    $('#breakdownTable').DataTable({
      order: [[0, 'asc']],
      pageLength: 31,
      language: {
        search: 'Cari:',
        info: 'Menampilkan _START_–_END_ dari _TOTAL_ hari',
        paginate: { previous: '«', next: '»' },
        emptyTable: 'Tidak ada data.',
      }
    });
  });
</script>
