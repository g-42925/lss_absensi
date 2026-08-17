<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header border-bottom d-flex align-items-center gap-2 py-3">
          <i class="ti ti-building-plus text-primary fs-5"></i>
          <h5 class="card-title mb-0">Tambah Divisi</h5>
        </div>

        <form class="card-body" action="<?= base_url().'karyawan/division/add_proses' ?>" method="post">
          <?php if ($failed == 1): ?>
            <div class="alert alert-danger alert-dismissible" role="alert">
              <i class="ti ti-alert-circle me-2"></i>
              <?= $this->session->flashdata('message'); ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>

          <!-- Informasi Dasar -->
          <div class="mb-4 card-body">
            <h6 class="text-uppercase text-muted fw-semibold small mb-3 border-bottom pb-2">
              <i class="ti ti-info-circle me-1"></i>Informasi Dasar
            </h6>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-medium">Nama Divisi <span class="text-danger">*</span></label>
                <input type="text" name="divisionName" id="division-name"
                  class="form-control" placeholder="Masukkan nama divisi">
              </div>
            </div>
          </div>

          <!-- Pola Pekerjaan -->
          <div class="mb-4 card-body">
            <h6 class="text-uppercase text-muted fw-semibold small mb-3 border-bottom pb-2">
              <i class="ti ti-calendar me-1"></i>Pola Pekerjaan
            </h6>
            <div class="row g-3">
              <div class="col-md-12">
                <label class="form-label fw-medium">Pola Pekerjaan <span class="text-danger">*</span></label>
                <div class="d-flex gap-4">
                  <div class="form-check">
                    <input type="radio" name="opt" id="opt-workday" onclick="showWeeklyList()" class="form-check-input">
                    <label class="form-check-label" for="opt-workday">Work Day</label>
                  </div>
                  <div class="form-check">
                    <input type="radio" name="opt" id="opt-shift" onclick="showShiftList()" class="form-check-input">
                    <label class="form-check-label" for="opt-shift">Shift Day</label>
                  </div>
                </div>
              </div>

              <div class="col-md-6 d-none" id="weeklyList">
                <label class="form-label fw-medium">Pilih Jadwal Mingguan <span class="text-danger">*</span></label>
                <select class="form-select" onchange="test(this.value)">
                  <option>-- Pilih --</option>
                  <?php foreach ($weekly as $w): ?>
                    <option value="wd-<?= $w['pola_kerja_id'] ?>"><?= $w['nama_pola'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-6 d-none" id="shiftList">
                <label class="form-label fw-medium">Pilih Shift <span class="text-danger">*</span></label>
                <select class="form-select" onchange="test(this.value)">
                  <option>-- Pilih --</option>
                  <?php foreach ($shift as $s): ?>
                    <option value="s-<?= $s['id'] ?>"><?= $s['name'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Hidden field untuk menyimpan nilai pola kerja -->
              <input type="text" id="pattern" name="pattern" class="d-none">
            </div>
          </div>

          <!-- Pengaturan Absensi -->
          <div class="mb-4 card-body">
            <h6 class="text-uppercase text-muted fw-semibold small mb-3 border-bottom pb-2">
              <i class="ti ti-clock me-1"></i>Pengaturan Absensi
            </h6>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-medium">Restriksi Absen Masuk <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input placeholder="0" type="number" min="0" value="0" name="restriction" class="form-control">
                  <span class="input-group-text">menit</span>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-medium">Restriksi Absen Pulang <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input type="number" min="0" placeholder="0" value="0" name="clockoutRestriction" class="form-control">
                  <span class="input-group-text">menit</span>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-medium">Absen Masuk di Luar Kantor <span class="text-danger">*</span></label>
                <div class="d-flex gap-4 mt-1">
                  <div class="form-check">
                    <input type="radio" name="ffocia" value="1" id="ffocia-yes" class="form-check-input">
                    <label class="form-check-label" for="ffocia-yes">Ya</label>
                  </div>
                  <div class="form-check">
                    <input checked type="radio" name="ffocia" value="0" id="ffocia-no" class="form-check-input">
                    <label class="form-check-label" for="ffocia-no">Tidak</label>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-medium">Absen Pulang di Luar Kantor <span class="text-danger">*</span></label>
                <div class="d-flex gap-4 mt-1">
                  <div class="form-check">
                    <input type="radio" name="ffocoa" value="1" id="ffocoa-yes" class="form-check-input">
                    <label class="form-check-label" for="ffocoa-yes">Ya</label>
                  </div>
                  <div class="form-check">
                    <input checked type="radio" name="ffocoa" value="0" id="ffocoa-no" class="form-check-input">
                    <label class="form-check-label" for="ffocoa-no">Tidak</label>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Pengaturan Penalty -->
          <div class="mb-4 card-body">
            <h6 class="text-uppercase text-muted fw-semibold small mb-3 border-bottom pb-2">
              <i class="ti ti-alert-triangle me-1"></i>Pengaturan Penalty
            </h6>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-medium">Nominal Penalty Terlambat <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text">Rp</span>
                  <input type="number" value="0" min="0" name="penaltyNominal" class="form-control">
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-medium">Menerapkan Penalty Terlambat <span class="text-danger">*</span></label>
                <div class="d-flex gap-4 mt-1">
                  <div class="form-check">
                    <input type="radio" name="latePenalty" value="1" id="latePenalty-yes" class="form-check-input">
                    <label class="form-check-label" for="latePenalty-yes">Ya</label>
                  </div>
                  <div class="form-check">
                    <input checked type="radio" name="latePenalty" value="0" id="latePenalty-no" class="form-check-input">
                    <label class="form-check-label" for="latePenalty-no">Tidak</label>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-medium">Menerapkan Penalty Lupa Absen Pulang <span class="text-danger">*</span></label>
                <div class="d-flex gap-4 mt-1">
                  <div class="form-check">
                    <input type="radio" name="clockoutPenalty" value="1" id="clockoutPenalty-yes" class="form-check-input">
                    <label class="form-check-label" for="clockoutPenalty-yes">Ya</label>
                  </div>
                  <div class="form-check">
                    <input checked type="radio" name="clockoutPenalty" value="0" id="clockoutPenalty-no" class="form-check-input">
                    <label class="form-check-label" for="clockoutPenalty-no">Tidak</label>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-medium">Upah Lembur <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text">Rp</span>
                  <input type="number" value="0" min="0" name="overworkFee" class="form-control">
                </div>
              </div>
            </div>
          </div>

          <!-- Pengaturan Penalty Alpha -->
          <div class="mb-4 card-body">
            <h6 class="text-uppercase text-muted fw-semibold small mb-3 border-bottom pb-2">
              <i class="ti ti-user-x me-1"></i>Pengaturan Penalty Alpha
            </h6>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-medium">Nominal Penalty Alpha <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text">Rp</span>
                  <input type="number" value="0" min="0" name="alphaPenaltyValue" class="form-control">
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-medium">Menerapkan Penalty Alpha <span class="text-danger">*</span></label>
                <div class="d-flex gap-4 mt-1">
                  <div class="form-check">
                    <input type="radio" name="alphaPenalty" value="1" id="alphaPenalty-yes" class="form-check-input">
                    <label class="form-check-label" for="alphaPenalty-yes">Ya</label>
                  </div>
                  <div class="form-check">
                    <input checked type="radio" name="alphaPenalty" value="0" id="alphaPenalty-no" class="form-check-input">
                    <label class="form-check-label" for="alphaPenalty-no">Tidak</label>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-medium">Type Potongan Alpha <span class="text-danger">*</span></label>
                <div class="d-flex gap-4 mt-1">
                  <div class="form-check">
                    <input type="radio" name="alphaPenaltyType" value="custom" id="apt-custom" class="form-check-input">
                    <label class="form-check-label" for="apt-custom">Custom</label>
                  </div>
                  <div class="form-check">
                    <input type="radio" name="alphaPenaltyType" value="percent" id="apt-percent" class="form-check-input">
                    <label class="form-check-label" for="apt-percent">Percent</label>
                  </div>
                  <div class="form-check">
                    <input checked type="radio" name="alphaPenaltyType" value="no" id="apt-no" class="form-check-input">
                    <label class="form-check-label" for="apt-no">No</label>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-medium">Konsekuensi Alpha <span class="text-danger">*</span></label>
                <div class="d-flex gap-4 mt-1">
                  <div class="form-check">
                    <input type="radio" name="alphaConsequence" value="1" id="ac-salary" class="form-check-input">
                    <label class="form-check-label" for="ac-salary">Salary Deduction</label>
                  </div>
                  <div class="form-check">
                    <input checked type="radio" name="alphaConsequence" value="2" id="ac-offdays" class="form-check-input">
                    <label class="form-check-label" for="ac-offdays">Offdays Deduction</label>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Pengaturan Penalty Istirahat -->
          <div class="mb-4 card-body">
            <h6 class="text-uppercase text-muted fw-semibold small mb-3 border-bottom pb-2">
              <i class="ti ti-coffee me-1"></i>Pengaturan Penalty Istirahat
            </h6>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-medium">Nominal Penalty Istirahat <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text">Rp</span>
                  <input type="number" value="0" min="0" name="afterBreakLatePenaltyValue" class="form-control">
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-medium">Menerapkan Potongan Istirahat <span class="text-danger">*</span></label>
                <div class="d-flex gap-4 mt-1">
                  <div class="form-check">
                    <input type="radio" name="afterBreakLatePenalty" value="1" id="ablp-yes" class="form-check-input">
                    <label class="form-check-label" for="ablp-yes">Ya</label>
                  </div>
                  <div class="form-check">
                    <input checked type="radio" name="afterBreakLatePenalty" value="0" id="ablp-no" class="form-check-input">
                    <label class="form-check-label" for="ablp-no">Tidak</label>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-medium">Type Potongan Istirahat <span class="text-danger">*</span></label>
                <div class="d-flex gap-4 mt-1">
                  <div class="form-check">
                    <input type="radio" name="afterBreakLatePenaltyType" value="fixed" id="ablt-fixed" class="form-check-input">
                    <label class="form-check-label" for="ablt-fixed">Fixed</label>
                  </div>
                  <div class="form-check">
                    <input type="radio" name="afterBreakLatePenaltyType" value="minute" id="ablt-minute" class="form-check-input">
                    <label class="form-check-label" for="ablt-minute">Minute</label>
                  </div>
                  <div class="form-check">
                    <input type="radio" name="afterBreakLatePenaltyType" value="no" id="ablt-no" class="form-check-input">
                    <label class="form-check-label" for="ablt-no">No</label>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer Buttons -->
          <div class="d-flex justify-content-end gap-2 pt-3 border-top">
            <a href="<?= base_url('karyawan/division') ?>" class="btn btn-outline-secondary">
              <i class="ti ti-x me-1"></i>Batal
            </a>
            <button type="submit" class="btn btn-primary">
              <i class="ti ti-device-floppy me-1"></i>Simpan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
    function showWeeklyList(){
      document.getElementById("weeklyList").classList.remove("d-none");
      document.getElementById("shiftList").classList.add("d-none");
    }

    function showShiftList(){
      document.getElementById("weeklyList").classList.add("d-none");
      document.getElementById("shiftList").classList.remove("d-none");
    }

    function test(id){
      document.getElementById("pattern").value = id;
    }
</script>