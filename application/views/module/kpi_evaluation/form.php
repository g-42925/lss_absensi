<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  
  <div class="card mb-4">
    <div class="card-header border-bottom d-flex align-items-center justify-content-between">
      <div>
        <?php
          $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
          ];
          $periode_str = $months[$bulan] . ' ' . $tahun;
        ?>
        <h5 class="card-title mb-0">Form Penilaian KPI - <?= $periode_str; ?></h5>
        <small class="text-muted">Karyawan: <strong><?= htmlspecialchars($pegawai['nama_pegawai']); ?></strong> (<?= htmlspecialchars($pegawai['nik']); ?>)</small>
      </div>
      <a href="<?= base_url('kpi_evaluation/index/' . $pegawai['pegawai_id']); ?>" class="btn btn-sm btn-label-secondary">
        <i class="ti ti-arrow-left me-1"></i> Batal
      </a>
    </div>
  </div>

  <?= $this->session->flashdata('message'); ?>

  <form action="<?= base_url('kpi_evaluation/save/' . $pegawai['pegawai_id'] . '/' . $bulan . '/' . $tahun); ?>" method="POST" id="evalForm">
    
    <?php 
      $kategori_sebelumnya = '';
      $total_bobot = 0;
    ?>

    <?php if (empty($kpi_list)): ?>
      <div class="card mb-4">
        <div class="card-body text-center py-5">
          <i class="ti ti-alert-circle ti-xl text-warning mb-2 d-block"></i>
          <h5>Tidak ada Data KPI Master Aktif</h5>
          <p class="text-muted mb-0">Silakan tambahkan dan aktifkan kriteria penilaian di menu <strong>KPI Master</strong> terlebih dahulu sebelum melakukan evaluasi.</p>
        </div>
      </div>
    <?php else: ?>

      <div class="row g-4">
        
        <div class="col-xl-9 col-lg-8 col-12">
          <!-- Looping KPI List based on Kategori -->
          <?php foreach ($kpi_list as $kpi): ?>
            
            <?php if ($kategori_sebelumnya != $kpi['kategori']): ?>
              <?php if ($kategori_sebelumnya != ''): ?>
                </div> </div> <!-- Close previous card -->
              <?php endif; ?>
              
              <div class="card mb-4 shadow-sm">
                <div class="card-header bg-label-dark py-3">
                  <h6 class="mb-0 fw-bold text-uppercase"><i class="ti ti-category me-2"></i> Kategori: <?= htmlspecialchars($kpi['kategori']); ?></h6>
                </div>
                <div class="card-body p-0">
            <?php 
              $kategori_sebelumnya = $kpi['kategori']; 
            endif; 
            ?>

            <?php $total_bobot += floatval($kpi['bobot']); ?>

            <!-- Item KPI -->
            <div class="border-bottom p-4">
              <div class="row align-items-start">
                <div class="col-md-5 mb-3 mb-md-0">
                  <h6 class="fw-bold mb-1"><?= htmlspecialchars($kpi['nama_kpi']); ?></h6>
                  <span class="badge bg-label-secondary mb-2">Bobot: <?= number_format($kpi['bobot'], 1); ?>%</span>
                  <span class="badge bg-label-info mb-2">Satuan: <?= htmlspecialchars($kpi['satuan']); ?></span>
                  
                  <?php if (!empty($kpi['deskripsi'])): ?>
                    <p class="text-muted small mb-0 mt-1 fst-italic">
                      "<?= nl2br(htmlspecialchars($kpi['deskripsi'])); ?>"
                    </p>
                  <?php endif; ?>
                </div>

                <div class="col-md-3 mb-3 mb-md-0">
                  <label class="form-label fw-semibold text-primary">Nilai Aktual <i class="text-danger">*</i></label>
                  <div class="input-group">
                    <input 
                      type="number" 
                      name="nilai_kpi[<?= $kpi['id']; ?>]" 
                      class="form-control kpi-input" 
                      placeholder="Min: <?= number_format($kpi['nilai_min'],0); ?>"
                      min="<?= $kpi['nilai_min']; ?>"
                      max="<?= $kpi['nilai_max']; ?>"
                      step="0.01"
                      value="<?= isset($kpi['nilai_aktual']) ? $kpi['nilai_aktual'] : ''; ?>"
                      data-bobot="<?= $kpi['bobot']; ?>"
                      data-max="<?= $kpi['nilai_max']; ?>"
                      required
                    >
                    <span class="input-group-text bg-light text-muted">/ <?= number_format($kpi['nilai_max'],0); ?></span>
                  </div>
                  <small class="text-danger error-msg d-none mt-1">Melebihi batas maksimal!</small>
                </div>

                <div class="col-md-4">
                  <label class="form-label text-muted">Catatan (Opsional)</label>
                  <textarea 
                    name="catatan_kpi[<?= $kpi['id']; ?>]" 
                    class="form-control form-control-sm" 
                    rows="2" 
                    placeholder="Alasan / Keterangan nilai..."
                  ><?= isset($kpi['catatan_kpi']) ? htmlspecialchars($kpi['catatan_kpi']) : ''; ?></textarea>
                </div>
              </div>
            </div>

          <?php endforeach; ?>
          
          <?php if ($kategori_sebelumnya != ''): ?>
            </div> </div> <!-- Close last card -->
          <?php endif; ?>
        </div> <!-- End Col 8 -->

        <!-- Right Sidebar Panel -->
        <div class="col-xl-3 col-lg-4 col-12">
          
          <div class="card shadow-sm sticky-top" style="top: 80px;">
            <div class="card-header border-bottom">
              <h6 class="card-title mb-0">Ringkasan Penilaian</h6>
            </div>
            <div class="card-body pt-3">
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Total Bobot Master:</span>
                <strong class="<?= $total_bobot == 100 ? 'text-success' : 'text-warning'; ?>"><?= $total_bobot; ?>%</strong>
              </div>
              
              <hr>

              <div class="text-center mb-4">
                <span class="text-muted d-block mb-1">Total Nilai Akhir (Kalkulasi)</span>
                <h1 class="display-4 fw-bold text-primary mb-0" id="totalScoreDisplay">
                  <?= isset($eval['total_nilai']) ? number_format($eval['total_nilai'], 2) : '0.00'; ?>
                </h1>
                <small class="text-muted">Skor Maksimal: 100</small>
              </div>

              <div class="mb-4">
                <label class="form-label fw-bold">Catatan Keseluruhan</label>
                <textarea 
                  name="catatan_umum" 
                  class="form-control" 
                  rows="4" 
                  placeholder="Review umum untuk kinerja karyawan di periode ini..."
                ><?= isset($eval['catatan']) ? htmlspecialchars($eval['catatan']) : ''; ?></textarea>
              </div>

              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg" id="btnSubmit">
                  <i class="ti ti-device-floppy me-2"></i> Simpan Penilaian
                </button>
              </div>
              
              <?php if (isset($eval['id'])): ?>
                <div class="mt-3 text-center text-muted small">
                  <i class="ti ti-info-circle"></i> Mengupdate data evaluasi yang sudah ada.
                </div>
              <?php endif; ?>

            </div>
          </div>

        </div> <!-- End Sidebar -->

      </div> <!-- End Row -->
    <?php endif; ?>

  </form>
</div>
<!-- / Content -->

<script type="text/javascript">
  $(document).ready(function () {
    
    // Fungsi untuk menghitung total skor secara live
    function calculateTotal() {
      let total = 0;
      let hasError = false;

      $('.kpi-input').each(function () {
        const val = parseFloat($(this).val());
        const bobot = parseFloat($(this).data('bobot'));
        const max = parseFloat($(this).data('max')) || 1; // Cegah bagi nol

        if (!isNaN(val)) {
          if (val > max) {
            $(this).addClass('is-invalid');
            $(this).parent().next('.error-msg').removeClass('d-none');
            hasError = true;
          } else {
            $(this).removeClass('is-invalid');
            $(this).parent().next('.error-msg').addClass('d-none');
            
            // Rumus: (Nilai Aktual / Max) * Bobot
            let score = (val / max) * bobot;
            total += score;
          }
        } else {
            $(this).removeClass('is-invalid');
            $(this).parent().next('.error-msg').addClass('d-none');
        }
      });

      $('#totalScoreDisplay').text(total.toFixed(2));
      
      // Update warna indikator
      if (total < 50) $('#totalScoreDisplay').removeClass('text-primary text-warning text-success').addClass('text-danger');
      else if (total < 75) $('#totalScoreDisplay').removeClass('text-primary text-danger text-success').addClass('text-warning');
      else $('#totalScoreDisplay').removeClass('text-primary text-danger text-warning').addClass('text-success');

      return hasError;
    }

    // Trigger kalkulasi saat input berubah
    $('.kpi-input').on('input change', function () {
      calculateTotal();
    });

    // Validasi saat submit
    $('#evalForm').on('submit', function (e) {
      if (calculateTotal()) {
        e.preventDefault();
        alert('Terdapat nilai yang melebihi batas maksimal. Silakan periksa kembali form Anda.');
        
        // Scroll ke element error pertama
        $('html, body').animate({
          scrollTop: $('.is-invalid').first().offset().top - 100
        }, 500);
        return false;
      }
      
      $('#btnSubmit').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...');
    });
  });
</script>
