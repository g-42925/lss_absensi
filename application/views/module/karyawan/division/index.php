<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header d-flex align-items-center justify-content-between border-bottom py-3">
      <h5 class="card-title mb-0">
        <i class="ti ti-building me-2 text-primary"></i>Data Divisi
      </h5>
      <a href="<?=base_url('karyawan/division/add?failed=false');?>" class="btn btn-primary btn-sm">
        <i class="ti ti-plus me-1"></i> Tambah Divisi
      </a>
    </div>
    <div class="card-datatable table-responsive">
      <table class="table table-hover table-bordered align-middle" id="divisionTable">
        <thead class="table-light">
          <tr>
            <th class="text-center" style="width: 40px;">#</th>
            <th>Nama Divisi</th>
            <th>Pola Kerja</th>
            <th class="text-end">Penalty Terlambat</th>
            <th class="text-end">Penalty Alpha</th>
            <th class="text-end">Penalty Istirahat</th>
            <th class="text-center">Restriksi Masuk</th>
            <th class="text-center">Restriksi Pulang</th>
            <th>Konsekuensi Alpha</th>
            <th class="text-end">Upah Lembur</th>
            <th class="text-center" style="width: 80px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($datas)): ?>
          <tr>
            <td colspan="11" class="text-center py-4 text-muted">
              <i class="ti ti-database-off fs-3 d-block mb-2"></i>
              Belum ada data divisi
            </td>
          </tr>
          <?php else: ?>
          <?php foreach ($datas as $i => $row): ?>
          <tr>
            <td class="text-center"><?= $i + 1 ?></td>
            <td>
              <strong><?= htmlspecialchars($row['division_name']) ?></strong>
            </td>
            <td>
              <?php
                $ws = $row['work_system'];
                $prefix = explode('-', $ws)[0] ?? '';
                $badge = $prefix === 'wd' ? 'bg-label-success' : ($prefix === 's' ? 'bg-label-info' : 'bg-label-secondary');
                $label = $prefix === 'wd' ? 'Work Day' : ($prefix === 's' ? 'Shift' : $ws);
              ?>
              <span class="badge <?= $badge ?>"><?= $label ?></span>
            </td>
            <td class="text-end">Rp <?= number_format($row['penalty_nominal'], 0, ',', '.') ?></td>
            <td class="text-end">
              <?php if (is_numeric($row['alpha_penalty_value'])): ?>
                Rp <?= number_format($row['alpha_penalty_value'], 0, ',', '.') ?>
              <?php else: ?>
                <?= htmlspecialchars($row['alpha_penalty_value']) ?>
              <?php endif; ?>
            </td>
            <td class="text-end">
              <?php if (is_numeric($row['after_break_late_penalty_value'])): ?>
                Rp <?= number_format($row['after_break_late_penalty_value'], 0, ',', '.') ?>
              <?php else: ?>
                <?= htmlspecialchars($row['after_break_late_penalty_value']) ?>
              <?php endif; ?>
            </td>
            <td class="text-center">
              <span class="badge bg-label-warning"><?= $row['restriction'] ?> menit</span>
            </td>
            <td class="text-center">
              <span class="badge bg-label-warning"><?= $row['clockout_restriction'] ?> menit</span>
            </td>
            <td>
              <?php
                $ac = $row['alpha_consequence'];
                $acLabel = $ac == '1' ? 'Salary Deduction' : 'Offdays Deduction';
                $acBadge = $ac == '1' ? 'bg-label-danger' : 'bg-label-primary';
              ?>
              <span class="badge <?= $acBadge ?>"><?= $acLabel ?></span>
            </td>
            <td class="text-end">Rp <?= number_format($row['overwork_fee'], 0, ',', '.') ?></td>
            <td class="text-center">
              <a href="<?= base_url() ?>karyawan/division/edit/<?= $row['id'] ?>?failed=false"
                 class="btn btn-icon btn-sm btn-outline-primary"
                 title="Edit Divisi">
                <i class="ti ti-edit"></i>
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