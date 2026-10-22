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
          <div class="col-xl-3 col-md-6">
            <label class="form-label">Effective Date</label>
            <input
              type="date"
              name="dates[]"
              class="form-control"
              value="<?= html_escape($period) ?>"
            />
          </div>

          <!-- Overwrite -->


          <!-- Riwayat -->
          <div class="col-xl-3 col-md-6">
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

  const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
  let currentUrl = '';

  const showMessage = (message, type = 'info') => {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} mb-0`;
    alert.textContent = message;
    content.replaceChildren(alert);
  };

  const loadHistory = async () => {
    content.replaceChildren();

    const loading = document.createElement('div');
    loading.className = 'text-center py-4';
    loading.textContent = 'Memuat riwayat...';
    content.appendChild(loading);

    try {
      const response = await fetch(currentUrl, {
        headers: { Accept: 'application/json' }
      });

      const result = await response.json();

      if (!response.ok || !result.success) {
        throw new Error(result.message || `HTTP ${response.status}`);
      }

      title.textContent = `Riwayat: ${result.allowance}`;

      if (!Array.isArray(result.data) || !result.data.length) {
        showMessage('Belum ada riwayat allowance.');
        return;
      }

      const table = document.createElement('table');
      table.className = 'table table-bordered table-striped mb-0';

      const thead = document.createElement('thead');
      const headerRow = document.createElement('tr');

      ['Periode', 'Nilai', 'Aksi'].forEach((label, index) => {
        const th = document.createElement('th');
        th.textContent = label;

        if (index > 0) th.className = 'text-end';
        if (index === 2) th.className = 'text-center';

        headerRow.appendChild(th);
      });

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

        const action = document.createElement('td');
        action.className = 'text-center';

        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'btn btn-sm btn-outline-danger btn-delete-history';
        button.dataset.id = item.employee_allowance_id;
        button.textContent = 'Hapus';

        action.appendChild(button);
        row.append(period, value, action);
        tbody.appendChild(row);
      });

      table.append(thead, tbody);

      const wrapper = document.createElement('div');
      wrapper.className = 'table-responsive';
      wrapper.appendChild(table);

      content.replaceChildren(wrapper);
    } catch (error) {
      showMessage(`Gagal memuat riwayat: ${error.message}`, 'danger');
      console.error('Allowance history error:', error);
    }
  };

  document.addEventListener('click', async event => {
    const historyButton = event.target.closest('.btn-history');

    if (historyButton) {
      currentUrl = historyButton.dataset.url;
      title.textContent = 'Riwayat Allowance';
      modal.show();
      await loadHistory();
      return;
    }

    const deleteButton = event.target.closest('.btn-delete-history');
    if (!deleteButton) return;

    const recordId = deleteButton.dataset.id;

    if (!recordId) {
      showMessage('ID entry allowance tidak ditemukan.', 'danger');
      return;
    }

    if (!confirm('Hapus entry allowance pada periode ini?')) return;

    deleteButton.disabled = true;
    deleteButton.textContent = 'Menghapus...';

    try {
      const response = await fetch(
        `${currentUrl.replace(/\/$/, '')}/delete/${encodeURIComponent(recordId)}`,
        {
          method: 'POST',
          headers: { Accept: 'application/json' }
        }
      );

      const result = await response.json();

      if (!response.ok || !result.success) {
        throw new Error(result.message || `HTTP ${response.status}`);
      }

      await loadHistory();
    } catch (error) {
      deleteButton.disabled = false;
      deleteButton.textContent = 'Hapus';
      showMessage(`Gagal menghapus entry: ${error.message}`, 'danger');
      console.error('Delete allowance history error:', error);
    }
  });
});

</script>
