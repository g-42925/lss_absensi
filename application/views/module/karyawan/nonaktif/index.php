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
              <button
                type="button"
                class="btn btn-sm btn-warning btn-set-end-working"
                data-bs-toggle="modal"
                data-bs-target="#modalSetEndWorking"
                data-pegawai-id="<?= $row['pegawai_id']; ?>"
                data-nama="<?= htmlspecialchars($row['nama_pegawai']); ?>"
                data-end-working-at="<?= $row['endWorkingAt'] ?? ''; ?>"
                title="Atur Tanggal Berhenti Kerja">
                <i class="bx bx-calendar-x me-1"></i> Atur Tgl. Berhenti
              </button>
            </td>
          </tr>
          <?php $no++; endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<!-- / Content -->

<!-- Modal Set End Working At -->
<div class="modal fade" id="modalSetEndWorking" tabindex="-1" aria-labelledby="modalSetEndWorkingLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalSetEndWorkingLabel">
          <i class="bx bx-calendar-x me-2 text-warning"></i>Atur Tanggal Berhenti Kerja
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formSetEndWorking" method="POST" action="">
        <div class="modal-body">
          <p class="mb-3">
            Karyawan: <strong id="modalNamaPegawai"></strong>
          </p>
          <div class="mb-3">
            <label for="inputEndWorkingAt" class="form-label">
              Tanggal Berhenti Kerja <span class="text-danger">*</span>
            </label>
            <input
              type="date"
              class="form-control"
              id="inputEndWorkingAt"
              name="end_working_at"
              required
            />
            <div class="form-text text-muted">
              Kosongkan untuk menghapus tanggal berhenti kerja.
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning">
            <i class="bx bx-save me-1"></i> Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- / Modal Set End Working At -->

<script>
document.addEventListener('DOMContentLoaded', function () {
  const modalEl = document.getElementById('modalSetEndWorking');
  const formEl  = document.getElementById('formSetEndWorking');
  const namaEl  = document.getElementById('modalNamaPegawai');
  const inputEl = document.getElementById('inputEndWorkingAt');
  const baseUrl = '<?= base_url('karyawan/nonaktif/set_end_working/'); ?>';

  modalEl.addEventListener('show.bs.modal', function (event) {
    const btn         = event.relatedTarget;
    const pegawaiId   = btn.getAttribute('data-pegawai-id');
    const nama        = btn.getAttribute('data-nama');
    const endWorking  = btn.getAttribute('data-end-working-at');

    namaEl.textContent    = nama;
    inputEl.value         = endWorking || '';
    inputEl.required      = false;
    formEl.action         = baseUrl + pegawaiId;
  });
});
</script>