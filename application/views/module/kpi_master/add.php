<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header border-bottom d-flex align-items-center justify-content-between">
      <div>
        <h5 class="card-title mb-0">Tambah KPI Master</h5>
        <small class="text-muted">Tambahkan jenis KPI baru yang dapat dinilai pada karyawan</small>
      </div>
      <a href="<?= base_url('kpi_master'); ?>" class="btn btn-sm btn-label-secondary">
        <i class="ti ti-arrow-left me-1"></i> Kembali
      </a>
    </div>
    <div class="card">
      <form class="card-body" action="<?= base_url('kpi_master/add_proses'); ?>" method="POST" id="kpiForm">
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
              <option value="" disabled selected>-- Pilih Kategori --</option>
              <option value="Produktivitas">Produktivitas</option>
              <option value="Kualitas">Kualitas</option>
              <option value="Kehadiran">Kehadiran</option>
              <option value="Perilaku">Perilaku &amp; Kedisiplinan</option>
              <option value="Inovasi">Inovasi &amp; Kreativitas</option>
              <option value="Keuangan">Keuangan</option>
              <option value="Lainnya">Lainnya</option>
            </select>
            <small class="text-muted">Pengelompokan jenis KPI.</small>
          </div>

          <div class="col-xl-6 col-md-6 col-12">
            <label class="form-label" for="satuan">
              Satuan Pengukuran <i class="text-danger">*</i>
            </label>
            <select class="form-select" id="satuan" name="satuan" required>
              <option value="" disabled selected>-- Pilih Satuan --</option>
              <option value="Persen (%)">Persen (%)</option>
              <option value="Nilai (1-10)">Nilai (1-10)</option>
              <option value="Nilai (1-100)">Nilai (1-100)</option>
              <option value="Hari">Hari</option>
              <option value="Jam">Jam</option>
              <option value="Rupiah (Rp)">Rupiah (Rp)</option>
              <option value="Unit">Unit</option>
              <option value="Proyek">Proyek</option>
              <option value="Kali">Kali</option>
              <option value="Lainnya">Lainnya</option>
            </select>
            <small class="text-muted">Satuan yang digunakan dalam pengukuran KPI.</small>
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
                placeholder="Contoh: 20"
                required
                autocomplete="off"
              />
              <span class="input-group-text">%</span>
            </div>
            <small class="text-muted">Persentase bobot KPI terhadap total penilaian.</small>
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
              placeholder="Contoh: 0"
              value="0"
              required
            />
            <small class="text-muted">Batas nilai terendah yang dapat diberikan.</small>
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
              placeholder="Contoh: 100"
              value="100"
              required
            />
            <small class="text-muted">Batas nilai tertinggi yang dapat diberikan.</small>
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
              placeholder="Jelaskan cara mengukur dan menilai KPI ini. Contoh: Kehadiran diukur dari persentase hari hadir dibanding hari kerja efektif dalam sebulan..."
            ></textarea>
            <small class="text-muted">Panduan bagi penilai tentang cara mengukur indikator ini.</small>
          </div>

          <!-- Status Aktif -->
          <div class="col-12">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="is_aktif" name="is_aktif" value="1" checked>
              <label class="form-check-label" for="is_aktif">
                KPI Aktif &amp; Dapat Digunakan
              </label>
            </div>
            <small class="text-muted">KPI yang tidak aktif tidak akan muncul dalam daftar penilaian karyawan.</small>
          </div>

          <!-- Info Bobot -->
          <div class="col-12">
            <div class="alert alert-info d-flex align-items-start gap-2 p-3" role="alert">
              <i class="ti ti-info-circle mt-1 flex-shrink-0"></i>
              <div>
                <strong>Petunjuk Bobot:</strong> Total bobot semua KPI dalam satu periode penilaian sebaiknya berjumlah <strong>100%</strong>.
                Pastikan distribusi bobot sudah sesuai dengan prioritas perusahaan.
              </div>
            </div>
          </div>

        </div><!-- /.row -->

        <div class="pt-4 d-flex justify-content-end gap-2">
          <a href="javascript:window.history.back();" class="btn btn-label-secondary">
            <i class="ti ti-x me-1"></i> Batal
          </a>
          <button type="submit" class="btn btn-primary" id="btnSubmit">
            <i class="ti ti-device-floppy me-1"></i> Simpan KPI
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
