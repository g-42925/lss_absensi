<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">

  <!-- Header Card: Info Karyawan + Filter Periode -->
  <div class="card mb-4">
    <div class="card-header border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
      <div>
        <h5 class="card-title mb-0">KPI Summary</h5>
        <small class="text-muted">
          Karyawan: <strong><?= htmlspecialchars($pegawai['nama_pegawai']); ?></strong>
          (<?= htmlspecialchars($pegawai['nik']); ?>)
          &mdash; <?= htmlspecialchars($pegawai['division_name'] ?? '-'); ?>
          <?= $pegawai['position_name'] ? ' / ' . htmlspecialchars($pegawai['position_name']) : ''; ?>
        </small>
      </div>
      <a href="<?= base_url('karyawan/data'); ?>" class="btn btn-sm btn-label-secondary">
        <i class="ti ti-arrow-left me-1"></i> Kembali
      </a>
    </div>
    <div class="card-body pt-3">
      <form method="POST" action="<?= base_url('kpi_summary/index/' . $pegawai['pegawai_id']); ?>"
            class="d-flex align-items-center gap-2 flex-wrap">
        <label class="fw-semibold mb-0 text-nowrap">Bulan Awal:</label>
        <select name="bulan_awal" class="form-select" style="width:auto;" required>
          <?php
            $months = [
              1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
              5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
              9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
            ];
            foreach ($months as $num => $name) {
              $sel = ($num == $bulan_awal) ? 'selected' : '';
              echo "<option value=\"$num\" $sel>$name</option>";
            }
          ?>
        </select>
        
        <label class="fw-semibold mb-0 text-nowrap ms-2">Bulan Akhir:</label>
        <select name="bulan_akhir" class="form-select" style="width:auto;" required>
          <?php
            foreach ($months as $num => $name) {
              $sel = ($num == $bulan_akhir) ? 'selected' : '';
              echo "<option value=\"$num\" $sel>$name</option>";
            }
          ?>
        </select>
        
        <label class="fw-semibold mb-0 text-nowrap ms-2">Tahun:</label>
        <select name="tahun" class="form-select" style="width:auto;" required>
          <?php
            $curr_year = date('Y');
            for ($y = $curr_year; $y >= $curr_year - 5; $y--) {
              $sel = ($y == $tahun) ? 'selected' : '';
              echo "<option value=\"$y\" $sel>$y</option>";
            }
          ?>
        </select>
        <button type="submit" class="btn btn-primary">
          <i class="ti ti-search me-1"></i> Tampilkan
        </button>
        <a id="btnExportExcel"
           href="<?= base_url('kpi_summary/export_excel/' . $pegawai['pegawai_id']); ?>?bulan_awal=<?= $bulan_awal; ?>&bulan_akhir=<?= $bulan_akhir; ?>&tahun=<?= $tahun; ?>"
           class="btn btn-success ms-1"
           title="Export data periode ini ke Excel (CSV)">
          <i class="ti ti-file-spreadsheet me-1"></i> Export Excel
        </a>
      </form>
    </div>
  </div>

  <?= $this->session->flashdata('message'); ?>

  <!-- Period Badge -->
  <div class="mb-3">
    <?php if ($is_multi_month): ?>
      <h6 class="text-muted mb-0">
        Periode: <span class="badge bg-label-primary fs-6"><?= $nama_bulan . ' ' . $tahun; ?></span>
        <small class="ms-2 text-muted fst-italic">(<?= ($bulan_akhir - $bulan_awal + 1); ?> bulan, agregat)</small>
      </h6>
    <?php else: ?>
      <h6 class="text-muted mb-0">Periode: <span class="badge bg-label-primary fs-6"><?= $nama_bulan . ' ' . $tahun; ?></span></h6>
    <?php endif; ?>
  </div>

  <!-- Score Cards Row -->
  <div class="row g-4 mb-4">

    <!-- KPI Absensi -->
    <div class="col-md-4">
      <?php
        $abs_score  = $kpi_absensi_score;
        $abs_class  = 'success';
        $abs_label  = 'Sangat Baik';
        if ($abs_score < 50)      { $abs_class = 'danger';  $abs_label = 'Buruk'; }
        elseif ($abs_score < 70)  { $abs_class = 'warning'; $abs_label = 'Cukup'; }
        elseif ($abs_score < 85)  { $abs_class = 'info';    $abs_label = 'Baik'; }
      ?>
      <div class="card h-100 border-start border-4 border-<?= $abs_class ?>">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
              <p class="text-muted mb-1 fw-semibold" style="font-size:0.8rem; text-transform:uppercase; letter-spacing:0.05em;">KPI Absensi</p>
              <h2 class="fw-bold mb-0 text-<?= $abs_class ?>"><?= number_format($abs_score, 2); ?></h2>
            </div>
            <div class="avatar avatar-lg bg-label-<?= $abs_class ?> rounded-circle">
              <i class="ti ti-calendar-stats ti-lg text-<?= $abs_class ?>"></i>
            </div>
          </div>
          <span class="badge bg-label-<?= $abs_class ?>"><?= $abs_label ?></span>
          <?php if (!$kpi_absensi_snap): ?>
            <small class="d-block text-muted mt-2 fst-italic">* Nilai dihitung real-time, belum di-generate</small>
          <?php else: ?>
            <small class="d-block text-muted mt-2">Snapshot tersimpan</small>
          <?php endif; ?>
          <?php if ($bulan_awal == $bulan_akhir): ?>
          <a href="<?= base_url('kpi_absensi/detail/' . $pegawai['pegawai_id'] . '/' . $bulan . '/' . $tahun); ?>"
             class="btn btn-sm btn-outline-<?= $abs_class ?> mt-3 w-100">
            <i class="ti ti-eye me-1"></i> Lihat Detail Absensi
          </a>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- KPI Biasa (Evaluasi) -->
    <div class="col-md-4">
      <?php
        $ev_score = $kpi_evaluasi_score;
        $ev_class  = 'secondary';
        $ev_label  = 'Belum Dinilai';
        if ($ev_score !== null) {
          $ev_class = 'success';  $ev_label = 'Sangat Baik';
          if ($ev_score < 50)     { $ev_class = 'danger';  $ev_label = 'Buruk'; }
          elseif ($ev_score < 70) { $ev_class = 'warning'; $ev_label = 'Cukup'; }
          elseif ($ev_score < 85) { $ev_class = 'info';    $ev_label = 'Baik'; }
        }
      ?>
      <div class="card h-100 border-start border-4 border-<?= $ev_class ?>">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
              <p class="text-muted mb-1 fw-semibold" style="font-size:0.8rem; text-transform:uppercase; letter-spacing:0.05em;">KPI Evaluasi</p>
              <?php if ($ev_score !== null): ?>
                <h2 class="fw-bold mb-0 text-<?= $ev_class ?>"><?= number_format($ev_score, 2); ?></h2>
              <?php else: ?>
                <h2 class="fw-bold mb-0 text-<?= $ev_class ?>">-</h2>
              <?php endif; ?>
            </div>
            <div class="avatar avatar-lg bg-label-<?= $ev_class ?> rounded-circle">
              <i class="ti ti-star ti-lg text-<?= $ev_class ?>"></i>
            </div>
          </div>
          <span class="badge bg-label-<?= $ev_class ?>"><?= $ev_label ?></span>
          <?php if ($kpi_evaluasi): ?>
            <small class="d-block text-muted mt-2">
              Dinilai oleh: <?= htmlspecialchars($kpi_evaluasi['evaluator_name'] ?? '-'); ?>
            </small>
          <?php else: ?>
            <small class="d-block text-muted mt-2">Belum ada penilaian di periode ini.</small>
          <?php endif; ?>
          <a href="<?= base_url('kpi_evaluation/index/' . $pegawai['pegawai_id']); ?>"
             class="btn btn-sm btn-outline-<?= $ev_class ?> mt-3 w-100">
            <i class="ti ti-star me-1"></i> Kelola Evaluasi KPI
          </a>
        </div>
      </div>
    </div>

    <!-- Total KPI -->
    <div class="col-md-4">
      <?php
        $tot_class = 'success';
        $tot_label = 'Sangat Baik';
        if ($total_kpi < 50)     { $tot_class = 'danger';  $tot_label = 'Buruk'; }
        elseif ($total_kpi < 70) { $tot_class = 'warning'; $tot_label = 'Cukup'; }
        elseif ($total_kpi < 85) { $tot_class = 'info';    $tot_label = 'Baik'; }
      ?>
      <div class="card h-100 border-start border-4 border-<?= $tot_class ?> bg-light">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
              <p class="text-muted mb-1 fw-semibold" style="font-size:0.8rem; text-transform:uppercase; letter-spacing:0.05em;">Total KPI (Rata-rata)</p>
              <h2 class="fw-bold mb-0 text-<?= $tot_class ?>"><?= number_format($total_kpi, 2); ?></h2>
            </div>
            <div class="avatar avatar-lg bg-<?= $tot_class ?> rounded-circle">
              <i class="ti ti-chart-pie ti-lg text-white"></i>
            </div>
          </div>
          <span class="badge bg-<?= $tot_class ?> text-white px-3"><?= $tot_label ?></span>
          <?php if ($kpi_evaluasi_score === null): ?>
            <small class="d-block text-warning mt-2 fst-italic">
              <i class="ti ti-alert-triangle me-1"></i>
              Total hanya dari KPI Absensi (KPI Evaluasi belum diisi)
            </small>
          <?php else: ?>
            <small class="d-block text-muted mt-2">
              (<?= number_format($kpi_absensi_score, 2); ?> + <?= number_format($kpi_evaluasi_score, 2); ?>) / 2
            </small>
          <?php endif; ?>

          <!-- Mini Progress Bar -->
          <div class="mt-3">
            <div class="d-flex justify-content-between mb-1">
              <small class="text-muted">Score</small>
              <small class="fw-semibold"><?= number_format(min($total_kpi, 100), 1); ?>%</small>
            </div>
            <div class="progress" style="height: 10px; border-radius: 5px;">
              <div class="progress-bar bg-<?= $tot_class ?>"
                   role="progressbar"
                   style="width: <?= min($total_kpi, 100); ?>%"
                   aria-valuenow="<?= $total_kpi; ?>"
                   aria-valuemin="0" aria-valuemax="100">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Breakdown Detail KPI Absensi -->
  <div class="row g-4 mb-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header border-bottom">
          <h5 class="card-title mb-0">
            <i class="ti ti-calendar-stats me-2 text-primary"></i>
            Detail KPI Absensi – <?= $nama_bulan . ' ' . $tahun; ?>
          </h5>
        </div>
        <div class="card-body">
          <div class="row row-cols-2 row-cols-md-4 g-3">
            <?php
              $stats = [
                ['label' => 'Hari Kerja Efektif', 'value' => $kpi_absensi_calc['hari_kerja_efektif'],   'icon' => 'ti-calendar',       'color' => 'primary'],
                ['label' => 'Hari Hadir',           'value' => $kpi_absensi_calc['hari_hadir'],           'icon' => 'ti-check',          'color' => 'success'],
                ['label' => 'Hari Izin',            'value' => $kpi_absensi_calc['hari_izin'],            'icon' => 'ti-file-text',      'color' => 'info'],
                ['label' => 'Hari Sakit',           'value' => $kpi_absensi_calc['hari_sakit'],           'icon' => 'ti-medical-cross',  'color' => 'warning'],
                ['label' => 'Hari Cuti',            'value' => $kpi_absensi_calc['hari_cuti'],            'icon' => 'ti-beach',          'color' => 'secondary'],
                ['label' => 'Hari Alpha',           'value' => $kpi_absensi_calc['hari_alpha'],           'icon' => 'ti-x',              'color' => 'danger'],
                ['label' => '% Kehadiran',          'value' => number_format($kpi_absensi_calc['persen_kehadiran'], 1) . '%', 'icon' => 'ti-percentage', 'color' => 'primary'],
                ['label' => 'Jml Terlambat',        'value' => $kpi_absensi_calc['jumlah_terlambat'],    'icon' => 'ti-clock',          'color' => 'warning'],
                ['label' => 'Jumlah SP',            'value' => $kpi_absensi_calc['jumlah_sp'],            'icon' => 'ti-alert-triangle', 'color' => 'danger'],
              ];
              foreach ($stats as $s):
            ?>
            <div class="col">
              <div class="d-flex align-items-center gap-2 p-3 rounded bg-label-<?= $s['color'] ?>">
                <i class="ti <?= $s['icon'] ?> text-<?= $s['color'] ?>" style="font-size:1.4rem;"></i>
                <div>
                  <div class="fw-bold text-<?= $s['color'] ?>"><?= $s['value']; ?></div>
                  <div class="text-muted" style="font-size: 0.75rem;"><?= $s['label']; ?></div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php if ($is_multi_month && !empty($kpi_per_bulan)): ?>
  <!-- Breakdown KPI Per Bulan (hanya tampil saat multi-bulan) -->
  <div class="row g-4 mb-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header border-bottom d-flex align-items-center justify-content-between">
          <h5 class="card-title mb-0">
            <i class="ti ti-calendar-month me-2 text-primary"></i>
            Breakdown KPI Per Bulan &mdash; <?= $nama_bulan . ' ' . $tahun; ?>
          </h5>
          <span class="badge bg-label-info"><?= count($kpi_per_bulan); ?> bulan</span>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0" id="tblPerBulan">
              <thead class="table-dark">
                <tr>
                  <th class="text-center" style="width:130px;">Bulan</th>
                  <th class="text-center">Hari Efektif</th>
                  <th class="text-center">Hadir</th>
                  <th class="text-center">Alpha</th>
                  <th class="text-center">Izin</th>
                  <th class="text-center">Sakit</th>
                  <th class="text-center">Cuti</th>
                  <th class="text-center">% Hadir</th>
                  <th class="text-center">Terlambat</th>
                  <th class="text-center">SP</th>
                  <th class="text-center" style="width:105px;">KPI Absensi</th>
                  <th class="text-center" style="width:105px;">KPI Evaluasi</th>
                  <th class="text-center" style="width:105px;">Total KPI</th>
                  <th class="text-center" style="width:70px;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($kpi_per_bulan as $pb): ?>
                <?php
                  $pb_tot_class = 'success';
                  if ($pb['total'] < 50)     $pb_tot_class = 'danger';
                  elseif ($pb['total'] < 70) $pb_tot_class = 'warning';
                  elseif ($pb['total'] < 85) $pb_tot_class = 'info';

                  $pb_abs_class = 'success';
                  if ($pb['kpi_absensi'] < 50)     $pb_abs_class = 'danger';
                  elseif ($pb['kpi_absensi'] < 70) $pb_abs_class = 'warning';
                  elseif ($pb['kpi_absensi'] < 85) $pb_abs_class = 'info';
                ?>
                <tr>
                  <td class="text-center fw-semibold">
                    <span class="badge bg-label-secondary"><?= $pb['nama_bulan'] . ' ' . $pb['tahun']; ?></span>
                  </td>
                  <td class="text-center"><?= $pb['hari_kerja_efektif']; ?></td>
                  <td class="text-center text-success fw-semibold"><?= $pb['hari_hadir']; ?></td>
                  <td class="text-center <?= $pb['hari_alpha'] > 0 ? 'text-danger fw-semibold' : 'text-muted'; ?>"><?= $pb['hari_alpha']; ?></td>
                  <td class="text-center"><?= $pb['hari_izin']; ?></td>
                  <td class="text-center"><?= $pb['hari_sakit']; ?></td>
                  <td class="text-center"><?= $pb['hari_cuti']; ?></td>
                  <td class="text-center"><?= number_format($pb['persen_kehadiran'], 1); ?>%</td>
                  <td class="text-center <?= $pb['jumlah_terlambat'] > 0 ? 'text-warning fw-semibold' : 'text-muted'; ?>"><?= $pb['jumlah_terlambat']; ?>×</td>
                  <td class="text-center <?= $pb['jumlah_sp'] > 0 ? 'text-danger fw-bold' : 'text-muted'; ?>"><?= $pb['jumlah_sp']; ?></td>
                  <td class="text-center">
                    <span class="badge bg-label-<?= $pb_abs_class; ?>"><?= number_format($pb['kpi_absensi'], 2); ?></span>
                  </td>
                  <td class="text-center">
                    <?php if ($pb['kpi_evaluasi'] !== null): ?>
                      <span class="badge bg-label-warning"><?= number_format($pb['kpi_evaluasi'], 2); ?></span>
                    <?php else: ?>
                      <span class="text-muted small">-</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-center">
                    <span class="badge bg-<?= $pb_tot_class; ?> text-white fs-6"><?= number_format($pb['total'], 2); ?></span>
                  </td>
                  <td class="text-center">
                    <a href="<?= base_url('kpi_summary/index/' . $pegawai['pegawai_id']); ?>"
                       onclick="setFilter(<?= $pb['bulan']; ?>, <?= $pb['bulan']; ?>, <?= $pb['tahun']; ?>); return false;"
                       class="btn btn-sm btn-icon btn-outline-primary" title="Lihat bulan ini saja">
                      <i class="ti ti-eye"></i>
                    </a>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
              <tfoot class="table-light fw-semibold">
                <?php
                  $sum_efektif   = array_sum(array_column($kpi_per_bulan, 'hari_kerja_efektif'));
                  $sum_hadir     = array_sum(array_column($kpi_per_bulan, 'hari_hadir'));
                  $sum_alpha     = array_sum(array_column($kpi_per_bulan, 'hari_alpha'));
                  $sum_izin      = array_sum(array_column($kpi_per_bulan, 'hari_izin'));
                  $sum_sakit     = array_sum(array_column($kpi_per_bulan, 'hari_sakit'));
                  $sum_cuti      = array_sum(array_column($kpi_per_bulan, 'hari_cuti'));
                  $sum_terlambat = array_sum(array_column($kpi_per_bulan, 'jumlah_terlambat'));
                  $sum_sp        = array_sum(array_column($kpi_per_bulan, 'jumlah_sp'));
                  $avg_persen    = $sum_efektif > 0 ? ($sum_hadir / $sum_efektif * 100) : 0;
                ?>
                <tr>
                  <td class="text-center"><small class="text-muted">Total / Rata-rata</small></td>
                  <td class="text-center"><?= $sum_efektif; ?></td>
                  <td class="text-center text-success"><?= $sum_hadir; ?></td>
                  <td class="text-center <?= $sum_alpha > 0 ? 'text-danger' : ''; ?>"><?= $sum_alpha; ?></td>
                  <td class="text-center"><?= $sum_izin; ?></td>
                  <td class="text-center"><?= $sum_sakit; ?></td>
                  <td class="text-center"><?= $sum_cuti; ?></td>
                  <td class="text-center"><?= number_format($avg_persen, 1); ?>%</td>
                  <td class="text-center"><?= $sum_terlambat; ?>×</td>
                  <td class="text-center"><?= $sum_sp; ?></td>
                  <td class="text-center"><span class="badge bg-label-primary"><?= number_format($kpi_absensi_score, 2); ?></span></td>
                  <td class="text-center"><?= $kpi_evaluasi_score !== null ? '<span class="badge bg-label-warning">' . number_format($kpi_evaluasi_score, 2) . '</span>' : '<span class="text-muted">-</span>'; ?></td>
                  <td class="text-center">
                    <?php
                      $tf_class = 'success';
                      if ($total_kpi < 50)     $tf_class = 'danger';
                      elseif ($total_kpi < 70) $tf_class = 'warning';
                      elseif ($total_kpi < 85) $tf_class = 'info';
                    ?>
                    <span class="badge bg-<?= $tf_class; ?> text-white"><?= number_format($total_kpi, 2); ?></span>
                  </td>
                  <td></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Detail KPI Evaluasi -->
  <?php if ($kpi_evaluasi): ?>
  <div class="row g-4 mb-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header border-bottom">
          <h5 class="card-title mb-0">
            <i class="ti ti-star me-2 text-warning"></i>
            Detail KPI Evaluasi – <?= $nama_bulan . ' ' . $tahun; ?>
          </h5>
        </div>
        <div class="card-body">
          <?php if (!empty($kpi_eval_by_cat)): ?>
            <?php foreach ($kpi_eval_by_cat as $kategori => $items): ?>

            <h6 class="fw-semibold text-muted mt-3 mb-2 text-uppercase" style="font-size:0.8rem; letter-spacing:0.05em;">
              <?= htmlspecialchars($kategori ?: 'Umum'); ?>
            </h6>
            <div class="table-responsive mb-3">
              <table class="table table-sm table-bordered align-middle">
                <thead class="table-light">
                  <tr>
                    <th>Indikator KPI</th>
                    <th class="text-center" style="width:100px;">Bobot</th>
                    <th class="text-center" style="width:100px;">Maks.</th>
                    <th class="text-center" style="width:100px;">Aktual</th>
                    <th class="text-center" style="width:120px;">Nilai Bobot</th>
                    <th>Catatan</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($items as $item): ?>
                  <tr>
                    <td><?= htmlspecialchars($item['nama_kpi']); ?></td>
                    <td class="text-center"><?= number_format($item['bobot'], 1); ?></td>
                    <td class="text-center"><?= number_format($item['nilai_max'], 1); ?></td>
                    <td class="text-center"><?= number_format($item['nilai_aktual'], 1); ?></td>
                    <td class="text-center fw-semibold"><?= number_format($item['nilai_bobot'], 2); ?></td>
                    <td><small><?= htmlspecialchars($item['catatan_kpi'] ?? '-'); ?></small></td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <?php endforeach; ?>
            <div class="d-flex justify-content-end mt-2">
              <div class="p-3 bg-label-warning rounded text-center">
                <small class="text-muted d-block">Total Nilai KPI Evaluasi</small>
                <h4 class="fw-bold mb-0 text-warning"><?= number_format($kpi_evaluasi['total_nilai'], 2); ?></h4>
              </div>
            </div>
          <?php else: ?>
            <p class="text-muted text-center py-3">Tidak ada detail item penilaian.</p>
          <?php endif; ?>
          <?php if (!empty($kpi_evaluasi['catatan'])): ?>
          <div class="alert alert-light border mt-3 mb-0">
            <strong><i class="ti ti-notes me-1"></i>Catatan Evaluator:</strong>
            <p class="mb-0 mt-1"><?= htmlspecialchars($kpi_evaluasi['catatan']); ?></p>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Riwayat KPI Per Periode -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">
        <i class="ti ti-history me-2"></i>Riwayat KPI Summary
      </h5>
    </div>
    <div class="card-datatable table-responsive p-3">
      <table class="table border-top text-center" id="dataTable">
        <thead>
          <tr>
            <th>#</th>
            <th>Periode</th>
            <th>KPI Absensi</th>
            <th>KPI Evaluasi</th>
            <th>Total KPI</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($riwayat_map)): ?>
          <tr>
            <td colspan="6" class="text-center text-muted py-5">
              <i class="ti ti-history ti-xl d-block mb-2 opacity-50"></i>
              Belum ada riwayat KPI untuk karyawan ini.
            </td>
          </tr>
          <?php else: ?>
          <?php $no = 1; foreach ($riwayat_map as $r): ?>
          <?php
            $r_total   = $r['total'];
            $r_class   = 'success';
            if ($r_total < 50)     $r_class = 'danger';
            elseif ($r_total < 70) $r_class = 'warning';
            elseif ($r_total < 85) $r_class = 'info';

            $months2 = [
              1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',
              7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'
            ];
          ?>
          <tr class="align-middle <?php
            if ($is_multi_month) {
              // highlight jika bulan ini berada dalam rentang yang sedang ditampilkan
              echo ($r['bulan'] >= $bulan_awal && $r['bulan'] <= $bulan_akhir && $r['tahun'] == $tahun) ? 'table-active fw-semibold' : '';
            } else {
              echo ($r['bulan'] == $bulan && $r['tahun'] == $tahun) ? 'table-active fw-semibold' : '';
            }
          ?>">
            <td><?= $no++; ?></td>
            <td>
              <span class="badge bg-label-primary">
                <?= ($months2[$r['bulan']] ?? $r['bulan']) . ' ' . $r['tahun']; ?>
              </span>
            </td>
            <td>
              <?php if (isset($r['kpi_absensi'])): ?>
                <span class="badge bg-label-primary"><?= number_format($r['kpi_absensi'], 2); ?></span>
              <?php else: ?>
                <span class="text-muted">-</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if (isset($r['kpi_evaluasi'])): ?>
                <span class="badge bg-label-warning"><?= number_format($r['kpi_evaluasi'], 2); ?></span>
              <?php else: ?>
                <span class="text-muted">-</span>
              <?php endif; ?>
            </td>
            <td>
              <span class="badge bg-<?= $r_class ?> text-white fs-6"><?= number_format($r_total, 2); ?></span>
            </td>
            <td>
              <a href="<?= base_url('kpi_summary/index/' . $pegawai['pegawai_id']); ?>"
                 onclick="setFilter(<?= $r['bulan']; ?>, <?= $r['bulan']; ?>, <?= $r['tahun']; ?>); return false;"
                 class="btn btn-sm btn-icon btn-outline-primary" title="Lihat periode ini">
                <i class="ti ti-eye"></i>
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>
<!-- / Content -->

<script>
  function setFilter(bulan_awal, bulan_akhir, tahun) {
    const form = document.querySelector('form[action*="kpi_summary"]');
    if (form) {
      form.querySelector('[name="bulan_awal"]').value = bulan_awal;
      form.querySelector('[name="bulan_akhir"]').value = bulan_akhir;
      form.querySelector('[name="tahun"]').value = tahun;
      form.submit();
    }
  }

  // Sinkronkan URL tombol Export Excel dengan nilai dropdown yang dipilih
  function syncExportUrl() {
    const form  = document.querySelector('form[action*="kpi_summary"]');
    const btn   = document.getElementById('btnExportExcel');
    if (!form || !btn) return;

    const ba  = form.querySelector('[name="bulan_awal"]').value;
    const bk  = form.querySelector('[name="bulan_akhir"]').value;
    const thn = form.querySelector('[name="tahun"]').value;

    const baseHref = btn.href.split('?')[0];
    btn.href = baseHref + '?bulan_awal=' + ba + '&bulan_akhir=' + bk + '&tahun=' + thn;
  }

  $(document).ready(function () {
    // Inisialisasi DataTable
    $('#dataTable').DataTable({
      order: [[1, 'desc']],
      columnDefs: [{ orderable: false, targets: [5] }],
      pageLength: 12
    });

    // Sinkronkan saat dropdown berubah
    const form = document.querySelector('form[action*="kpi_summary"]');
    if (form) {
      form.querySelectorAll('select').forEach(function (sel) {
        sel.addEventListener('change', syncExportUrl);
      });
      syncExportUrl(); // jalankan sekali saat load
    }
  });
</script>
