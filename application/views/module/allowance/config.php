<?php
  $pending = $pending ?? [];
  $conflicts = $conflicts ?? [];
  $message = $message ?? null;
  $data = $data ?? [];
?>

<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Edit Allowance</h5>
    </div>

    <form
      class="card-body p-3"
      action="<?= base_url('allowance/config_process/' . $id) ?>"
      method="POST"
    >
      <?php if ($message): ?>
        <?= $message ?>
      <?php endif; ?>

      <?php if ($conflicts): ?>
        <div class="alert alert-warning" role="alert">
          <ul class="mb-0">
            <?php foreach ($conflicts as $conflict): ?>
              <li>
                <strong><?= html_escape($conflict['name']) ?></strong>
                — Periode <?= html_escape($conflict['payroll_period']) ?>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <input
        type="hidden"
        name="confirm_overwrite"
        value="<?= $conflicts ? '1' : '0' ?>"
      />

      <?php foreach ($data as $index => $a): ?>
        <?php
          $value = $pending['values'][$index] ?? $a['value'];
          $period = $pending['dates'][$index] ?? $a['payroll_period'];
          $overwrite = (string) ($pending['overwrite'][$index] ?? '0');

          $historyUrl = base_url(
            'allowance/history/' .
            rawurlencode((string) $id) . '/' .
            rawurlencode((string) $a['allowance_id'])
          );
        ?>

        <div class="row g-3 align-items-end mb-4">
          <input
            type="hidden"
            name="allowance[]"
            value="<?= html_escape($a['allowance_id']) ?>"
          />

          <!-- Allowance Name -->
          <div class="col-xl-3 col-md-6">
            <label class="form-label">Allowance Name</label>
            <input
              type="text"
              class="form-control"
              value="<?= html_escape($a['name']) ?>"
              readonly
            />
          </div>

          <!-- Allowance Value -->
          <div class="col-xl-3 col-md-6">
            <label class="form-label">
              Allowance Value <span class="text-danger">*</span>
            </label>
            <input
              type="number"
              name="values[]"
              class="form-control"
              value="<?= html_escape($value) ?>"
              step="any"
              required
            />
          </div>

          <!-- Effective Date -->
          <div class="col-xl-2 col-md-6">
            <label class="form-label">Effective Date</label>
            <input
              type="date"
              name="dates[]"
              class="form-control"
              value="<?= html_escape($period) ?>"
            />
          </div>

          <!-- Overwrite -->
          <div class="col-xl-2 col-md-6">
            <label class="form-label">Overwrite</label>
            <select name="overwrite[]" class="form-select">
              <option value="1" <?= $overwrite === '1' ? 'selected' : '' ?>>
                Yes
              </option>
              <option value="0" <?= $overwrite === '0' ? 'selected' : '' ?>>
                No
              </option>
            </select>
          </div>

          <!-- Riwayat -->
          <div class="col-xl-2 col-md-6">
            <label class="form-label">Riwayat</label>
            <button
              type="button"
              class="btn btn-outline-info w-100 btn-history"
              data-url="<?= html_escape($historyUrl) ?>"
            >
              <i class="bx bx-history"></i>
              Lihat Riwayat
            </button>
          </div>
        </div>
      <?php endforeach; ?>

      <div class="pt-4 border-top text-end">
        <a
          href="<?= base_url('karyawan/data') ?>"
          class="btn btn-label-secondary me-2"
        >
          Batal
        </a>

        <button type="submit" class="btn btn-primary">
          <?= $conflicts ? 'Ya, Timpa Record' : 'Simpan Data' ?>
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Allowance History Modal -->
<div
  class="modal fade"
  id="allowanceHistoryModal"
  tabindex="-1"
  aria-labelledby="allowanceHistoryTitle"
  aria-hidden="true"
>
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="allowanceHistoryTitle">
          Riwayat Allowance
        </h5>

        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Tutup"
        ></button>
      </div>

      <div class="modal-body">
        <div id="allowanceHistoryContent">
          <div class="text-center py-4">
            Memuat riwayat...
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button
          type="button"
          class="btn btn-secondary"
          data-bs-dismiss="modal"
        >
          Tutup
        </button>
      </div>
    </div>
  </div>
</div>
<!-- / Content -->

<script>
document.addEventListener('DOMContentLoaded', () => {
  const modalElement = document.getElementById('allowanceHistoryModal');
  const content = document.getElementById('allowanceHistoryContent');
  const title = document.getElementById('allowanceHistoryTitle');

  if (!modalElement || !content || !window.bootstrap?.Modal) {
    console.error('Modal atau Bootstrap JS belum tersedia.');
    return;
  }

  const historyModal = bootstrap.Modal.getOrCreateInstance(modalElement);

  document.addEventListener('click', async event => {
    const button = event.target.closest('.btn-history');
    if (!button) return;

    content.textContent = 'Memuat riwayat...';
    title.textContent = 'Riwayat Allowance';
    historyModal.show();

    try {
      const response = await fetch(button.dataset.url, {
        headers: { Accept: 'application/json' }
      });

      const result = await response.json();

      if (!response.ok || !result.success) {
        throw new Error(result.message || `HTTP ${response.status}`);
      }

      title.textContent = `Riwayat: ${result.allowance}`;

      if (!Array.isArray(result.data) || !result.data.length) {
        content.replaceChildren();

        const alert = document.createElement('div');
        alert.className = 'alert alert-info mb-0';
        alert.textContent = 'Belum ada riwayat allowance.';
        content.appendChild(alert);
        return;
      }

      const table = document.createElement('table');
      table.className = 'table table-bordered table-striped mb-0';

      const thead = document.createElement('thead');
      const headerRow = document.createElement('tr');

      const periodHeader = document.createElement('th');
      periodHeader.textContent = 'Periode';

      const valueHeader = document.createElement('th');
      valueHeader.textContent = 'Nilai';
      valueHeader.className = 'text-end';

      headerRow.append(periodHeader, valueHeader);
      thead.appendChild(headerRow);

      const tbody = document.createElement('tbody');

      result.data.forEach(item => {
        const row = document.createElement('tr');

        const period = document.createElement('td');
        period.textContent = item.payroll_period || '-';

        const value = document.createElement('td');
        const numericValue = Number(item.value);

        value.textContent = Number.isFinite(numericValue)
          ? numericValue.toLocaleString('id-ID')
          : '-';

        value.className = 'text-end';

        row.append(period, value);
        tbody.appendChild(row);
      });

      table.append(thead, tbody);

      const wrapper = document.createElement('div');
      wrapper.className = 'table-responsive';
      wrapper.appendChild(table);

      content.replaceChildren(wrapper);
    } catch (error) {
      content.replaceChildren();

      const alert = document.createElement('div');
      alert.className = 'alert alert-danger mb-0';
      alert.textContent = `Gagal memuat riwayat: ${error.message}`;
      content.appendChild(alert);

      console.error('Allowance history error:', error);
    }
  });
});
</script>
