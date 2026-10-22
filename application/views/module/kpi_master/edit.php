<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header border-bottom d-flex align-items-center justify-content-between">
      <div>
        <h5 class="card-title mb-0">Edit KPI Master</h5>
        <small class="text-muted">Perbarui data KPI: <strong><?= htmlspecialchars($kpi['nama_kpi']); ?></strong></small>
      </div>
      <a href="<?= base_url('kpi_master'); ?>" class="btn btn-sm btn-label-secondary">
        <i class="ti ti-arrow-left me-1"></i> Kembali
      </a>
    </div>
    <div class="card">
      <form class="card-body" action="<?= base_url('kpi_master/edit_proses/' . $kpi['id']); ?>" method="POST" id="kpiForm">
        <?= $this->session->flashdata('message'); ?>

        <div class="row g-3">

          <!-- Nama KPI -->
          <div class="col-12">
            <label class="form-label" for="nama_kpi">
              Nama KPI <i class="text-danger">*</i>
            </label>
            <input
              type="text"
              class="form-control"
              id="nama_kpi"
              name="nama_kpi"
              value="<?= htmlspecialchars($kpi['nama_kpi']); ?>"
              placeholder="Contoh: Tingkat Kehadiran, Produktivitas Target, Kualitas Kerja..."
              required
              autocomplete="off"
              maxlength="150"
            />
            <small class="text-muted">Nama indikator yang akan digunakan sebagai tolak ukur penilaian.</small>
          </div>

          <!-- Kategori & Satuan -->
          <div class="col-xl-6 col-md-6 col-12">
            <label class="form-label" for="kategori">
              Kategori <i class="text-danger">*</i>
            </label>
            <select class="form-select" id="kategori" name="kategori" required>
              <option value="" disabled>-- Pilih Kategori --</option>
              <?php
                $kategoriOptions = ['Produktivitas', 'Kualitas', 'Kehadiran', 'Perilaku', 'Inovasi', 'Keuangan', 'Lainnya'];
                $kategoriLabels  = [
                  'Produktivitas' => 'Produktivitas',
                  'Kualitas'      => 'Kualitas',
                  'Kehadiran'     => 'Kehadiran',
                  'Perilaku'      => 'Perilaku &amp; Kedisiplinan',
                  'Inovasi'       => 'Inovasi &amp; Kreativitas',
                  'Keuangan'      => 'Keuangan',
                  'Lainnya'       => 'Lainnya',
                ];
                foreach ($kategoriOptions as $k):
                  $selected = ($kpi['kategori'] === $k) ? 'selected' : '';
              ?>
              <option value="<?= $k; ?>" <?= $selected; ?>><?= $kategoriLabels[$k]; ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-xl-6 col-md-6 col-12">
            <label class="form-label" for="satuan">
              Satuan Pengukuran <i class="text-danger">*</i>
            </label>
            <select class="form-select" id="satuan" name="satuan" required>
              <option value="" disabled>-- Pilih Satuan --</option>
              <?php
                $satuanOptions = [
                  'Persen (%)', 'Nilai (1-10)', 'Nilai (1-100)',
                  'Hari', 'Jam', 'Rupiah (Rp)', 'Unit', 'Proyek', 'Kali', 'Lainnya'
                ];
                foreach ($satuanOptions as $s):
                  $selected = ($kpi['satuan'] === $s) ? 'selected' : '';
              ?>
              <option value="<?= htmlspecialchars($s); ?>" <?= $selected; ?>><?= htmlspecialchars($s); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Bobot -->
          <div class="col-xl-4 col-md-4 col-12">
            <label class="form-label" for="bobot">
              Bobot (%) <i class="text-danger">*</i>
            </label>
            <div class="input-group">
              <input
                type="number"
                class="form-control"
                id="bobot"
                name="bobot"
                min="0"
                max="100"
                step="0.01"
                value="<?= htmlspecialchars($kpi['bobot']); ?>"
                required
              />
              <span class="input-group-text">%</span>
            </div>
          </div>

          <!-- Nilai Min & Max -->
          <div class="col-xl-4 col-md-4 col-12">
            <label class="form-label" for="nilai_min">
              Nilai Minimum <i class="text-danger">*</i>
            </label>
            <input
              type="number"
              class="form-control"
              id="nilai_min"
              name="nilai_min"
              min="0"
              step="0.01"
              value="<?= htmlspecialchars($kpi['nilai_min']); ?>"
              required
            />
          </div>

          <div class="col-xl-4 col-md-4 col-12">
            <label class="form-label" for="nilai_max">
              Nilai Maksimum <i class="text-danger">*</i>
            </label>
            <input
              type="number"
              class="form-control"
              id="nilai_max"
              name="nilai_max"
              min="0"
              step="0.01"
              value="<?= htmlspecialchars($kpi['nilai_max']); ?>"
              required
            />
          </div>

          <!-- Deskripsi -->
          <div class="col-12">
            <label class="form-label" for="deskripsi">
              Deskripsi / Panduan Penilaian
            </label>
            <textarea
              class="form-control"
              id="deskripsi"
              name="deskripsi"
              rows="4"
              maxlength="500"
              placeholder="Jelaskan cara mengukur dan menilai KPI ini..."
            ><?= htmlspecialchars($kpi['deskripsi']); ?></textarea>
          </div>

          <!-- Status Aktif -->
          <div class="col-12">
            <div class="form-check form-switch">
              <input
                class="form-check-input"
                type="checkbox"
                id="is_aktif"
                name="is_aktif"
                value="1"
                <?= $kpi['is_aktif'] === 'y' ? 'checked' : ''; ?>
              >
              <label class="form-check-label" for="is_aktif">
                KPI Aktif &amp; Dapat Digunakan
              </label>
            </div>
            <small class="text-muted">KPI yang tidak aktif tidak akan muncul dalam daftar penilaian karyawan.</small>
          </div>

        </div><!-- /.row -->

        <!-- Info created_at -->
        <div class="mt-3">
          <small class="text-muted">
            <i class="ti ti-clock me-1"></i>
            Dibuat: <?= date('d M Y H:i', strtotime($kpi['created_at'])); ?>
            <?php if (!empty($kpi['updated_at'])): ?>
            &nbsp;|&nbsp; Terakhir diperbarui: <?= date('d M Y H:i', strtotime($kpi['updated_at'])); ?>
            <?php endif; ?>
          </small>
        </div>

        <div class="pt-4 d-flex justify-content-end gap-2">
          <a href="javascript:window.history.back();" class="btn btn-label-secondary">
            <i class="ti ti-x me-1"></i> Batal
          </a>
          <button type="submit" class="btn btn-primary" id="btnSubmit">
            <i class="ti ti-device-floppy me-1"></i> Simpan Perubahan
          </button>
        </div>

      </form>
    </div>
  </div>
</div>
<!-- / Content -->

<script type="text/javascript">
  $(document).ready(function () {
    $('#kpiForm').on('submit', function () {
      const nilMin = parseFloat($('#nilai_min').val());
      const nilMax = parseFloat($('#nilai_max').val());
      const bobot  = parseFloat($('#bobot').val());

      if (nilMax <= nilMin) {
        alert('Nilai Maksimum harus lebih besar dari Nilai Minimum.');
        return false;
      }
      if (bobot < 0 || bobot > 100) {
        alert('Bobot harus berada di antara 0 dan 100.');
        return false;
      }
      $('#btnSubmit').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');
    });
  });
</script>
