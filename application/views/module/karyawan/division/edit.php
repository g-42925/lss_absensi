<!-- =========================================================
     CONTENT
========================================================= -->
<div class="container-xxl flex-grow-1 container-p-y">

  <div class="row justify-content-center">
    <div class="col-12 col-xl-10">

      <div class="card border-0 shadow-sm overflow-hidden">

        <!-- =====================================================
             HEADER
        ====================================================== -->
        <div class="card-header bg-body border-bottom px-4 px-md-5 py-4">

          <div class="d-flex align-items-center gap-3">

            <div class="page-icon bg-primary-subtle text-primary">
              <i class="ti ti-building"></i>
            </div>

            <div class="min-w-0">

              <div class="d-flex flex-wrap align-items-center gap-2 mb-1">

                <h5 class="mb-0 fw-semibold">
                  Edit Divisi
                </h5>

                <span class="badge bg-primary-subtle text-primary fw-medium">
                  <?= htmlspecialchars($current['division_name']) ?>
                </span>

              </div>

              <div class="text-muted small">
                Perbarui pola kerja, pengaturan absensi, dan kebijakan divisi.
              </div>

            </div>

          </div>

        </div>


        <!-- =====================================================
             FORM
        ====================================================== -->
        <form
          action="<?= base_url().'karyawan/division/edit_proses/'.$current['id'] ?>"
          method="post"
        >

          <div class="card-body px-4 px-md-5 py-4 py-md-5">

            <!-- =================================================
                 ALERT
            ================================================== -->
            <?php if ($failed == 1): ?>

              <div
                class="alert alert-danger d-flex align-items-start gap-3 mb-4"
                role="alert"
              >

                <i class="ti ti-alert-circle fs-5 flex-shrink-0"></i>

                <div class="flex-grow-1">
                  <?= $this->session->flashdata('message'); ?>
                </div>

                <button
                  type="button"
                  class="btn-close"
                  data-bs-dismiss="alert"
                  aria-label="Close"
                ></button>

              </div>

            <?php endif; ?>


            <!-- =================================================
                 INFORMASI DIVISI
            ================================================== -->
            <section class="config-section">

              <div class="section-heading">

                <div class="section-icon bg-primary-subtle text-primary">
                  <i class="ti ti-info-circle"></i>
                </div>

                <div>
                  <h6 class="section-title">
                    Informasi Divisi
                  </h6>

                  <p class="section-description">
                    Informasi dasar mengenai divisi.
                  </p>
                </div>

              </div>


              <div class="section-content">

                <div class="row g-4">

                  <div class="col-12 col-md-6">

                    <label
                      for="division-name"
                      class="form-label"
                    >
                      Nama Divisi
                      <span class="text-danger">*</span>
                    </label>

                    <input
                      type="text"
                      value="<?= htmlspecialchars($current['division_name']) ?>"
                      name="divisionName"
                      id="division-name"
                      class="form-control"
                      placeholder="Masukkan nama divisi"
                      required
                    >

                  </div>

                </div>

              </div>

            </section>


            <!-- =================================================
                 POLA KERJA
            ================================================== -->
            <section class="config-section">

              <div class="section-heading">

                <div class="section-icon bg-primary-subtle text-primary">
                  <i class="ti ti-calendar"></i>
                </div>

                <div>
                  <h6 class="section-title">
                    Pola Kerja
                  </h6>

                  <p class="section-description">
                    Tentukan sistem kerja yang digunakan divisi.
                  </p>
                </div>

              </div>


              <div class="section-content">

                <div class="row g-4">

                  <!-- Work System -->
                  <div class="col-12">

                    <label class="form-label">
                      Pola Pekerjaan
                      <span class="text-danger">*</span>
                    </label>

                    <div class="choice-grid">

                      <input
                        type="radio"
                        name="opt"
                        value="workday"
                        id="opt-workday"
                        class="btn-check"
                        <?= explode('-', $current['work_system'])[0] == 'wd' ? 'checked' : '' ?>
                        onchange="showWeeklyList()"
                      >

                      <label
                        for="opt-workday"
                        class="choice-card"
                      >

                        <i class="ti ti-calendar-week"></i>

                        <span>
                          <strong>Work Day</strong>
                          <small>Menggunakan pola kerja mingguan.</small>
                        </span>

                      </label>


                      <input
                        type="radio"
                        name="opt"
                        value="shift"
                        id="opt-shift"
                        class="btn-check"
                        <?= explode('-', $current['work_system'])[0] == 's' ? 'checked' : '' ?>
                        onchange="showShiftList()"
                      >

                      <label
                        for="opt-shift"
                        class="choice-card"
                      >

                        <i class="ti ti-clock-hour-4"></i>

                        <span>
                          <strong>Shift Day</strong>
                          <small>Menggunakan jadwal kerja berbasis shift.</small>
                        </span>

                      </label>

                    </div>

                  </div>


                  <!-- Weekly -->
                  <div
                    class="col-12 col-md-6 <?= explode('-', $current['work_system'])[0] == 'wd' ? '' : 'd-none' ?>"
                    id="weeklyList"
                  >

                    <label
                      for="weeklyPattern"
                      class="form-label"
                    >
                      Jadwal Mingguan
                      <span class="text-danger">*</span>
                    </label>

                    <select
                      class="form-select"
                      name="weeklyPattern"
                      id="weeklyPattern"
                      onchange="setPattern(this.value)"
                    >

                      <option value="">
                        -- Pilih Jadwal --
                      </option>

                      <?php foreach ($weekly as $w): ?>

                        <option
                          value="wd-<?= $w['pola_kerja_id'] ?>"
                          <?= $current['work_system'] == 'wd-'.$w['pola_kerja_id'] ? 'selected' : '' ?>
                        >
                          <?= htmlspecialchars($w['nama_pola']) ?>
                        </option>

                      <?php endforeach; ?>

                    </select>

                  </div>


                  <!-- Shift -->
                  <div
                    class="col-12 col-md-6 <?= explode('-', $current['work_system'])[0] == 's' ? '' : 'd-none' ?>"
                    id="shiftList"
                  >

                    <label
                      for="shiftPattern"
                      class="form-label"
                    >
                      Shift
                      <span class="text-danger">*</span>
                    </label>

                    <select
                      class="form-select"
                      name="shiftPattern"
                      id="shiftPattern"
                      onchange="setPattern(this.value)"
                    >

                      <option value="">
                        -- Pilih Shift --
                      </option>

                      <?php foreach ($shift as $s): ?>

                        <option
                          value="s-<?= $s['id'] ?>"
                          <?= $current['work_system'] == 's-'.$s['id'] ? 'selected' : '' ?>
                        >
                          <?= htmlspecialchars($s['name']) ?>
                        </option>

                      <?php endforeach; ?>

                    </select>

                  </div>


                  <!-- Hidden Pattern -->
                  <input
                    type="hidden"
                    id="pattern"
                    value="<?= htmlspecialchars($current['work_system']) ?>"
                    name="pattern"
                  >

                </div>

              </div>

            </section>


            <!-- =================================================
                 PENGATURAN ABSENSI
            ================================================== -->
            <section class="config-section">

              <div class="section-heading">

                <div class="section-icon bg-info-subtle text-info">
                  <i class="ti ti-clock"></i>
                </div>

                <div>
                  <h6 class="section-title">
                    Pengaturan Absensi
                  </h6>

                  <p class="section-description">
                    Atur batas waktu dan lokasi absensi divisi.
                  </p>
                </div>

              </div>


              <div class="section-content">

                <div class="row g-4">

                  <!-- Restriction -->
                  <div class="col-12 col-md-6">

                    <label
                      for="restriction"
                      class="form-label"
                    >
                      Restriksi Absen Masuk
                      <span class="text-danger">*</span>
                    </label>

                    <div class="input-group">

                      <input
                        type="number"
                        min="0"
                        value="<?= htmlspecialchars($current['restriction']) ?>"
                        name="restriction"
                        id="restriction"
                        class="form-control"
                        required
                      >

                      <span class="input-group-text">
                        menit
                      </span>

                    </div>

                  </div>


                  <!-- Clockout Restriction -->
                  <div class="col-12 col-md-6">

                    <label
                      for="clockoutRestriction"
                      class="form-label"
                    >
                      Restriksi Absen Pulang
                      <span class="text-danger">*</span>
                    </label>

                    <div class="input-group">

                      <input
                        type="number"
                        min="0"
                        value="<?= htmlspecialchars($current['clockout_restriction']) ?>"
                        name="clockoutRestriction"
                        id="clockoutRestriction"
                        class="form-control"
                        required
                      >

                      <span class="input-group-text">
                        menit
                      </span>

                    </div>

                  </div>


                  <!-- Check In Outside -->
                  <div class="col-12 col-md-6">

                    <div class="simple-setting">

                      <div>

                        <div class="fw-semibold">
                          Absen Masuk di Luar Kantor
                        </div>

                        <div class="text-muted small">
                          Izinkan karyawan melakukan absensi masuk di luar kantor.
                        </div>

                      </div>


                      <div class="compact-choice">

                        <input
                          type="radio"
                          name="ffocia"
                          value="1"
                          id="ffocia-yes"
                          class="btn-check"
                          <?= $current['ffo_check_in_allowed'] == 1 ? 'checked' : '' ?>
                        >

                        <label for="ffocia-yes">
                          Ya
                        </label>


                        <input
                          type="radio"
                          name="ffocia"
                          value="0"
                          id="ffocia-no"
                          class="btn-check"
                          <?= $current['ffo_check_in_allowed'] == 0 ? 'checked' : '' ?>
                        >

                        <label for="ffocia-no">
                          Tidak
                        </label>

                      </div>

                    </div>

                  </div>


                  <!-- Check Out Outside -->
                  <div class="col-12 col-md-6">

                    <div class="simple-setting">

                      <div>

                        <div class="fw-semibold">
                          Absen Pulang di Luar Kantor
                        </div>

                        <div class="text-muted small">
                          Izinkan karyawan melakukan absensi pulang di luar kantor.
                        </div>

                      </div>


                      <div class="compact-choice">

                        <input
                          type="radio"
                          name="ffocoa"
                          value="1"
                          id="ffocoa-yes"
                          class="btn-check"
                          <?= $current['ffo_check_out_allowed'] == 1 ? 'checked' : '' ?>
                        >

                        <label for="ffocoa-yes">
                          Ya
                        </label>


                        <input
                          type="radio"
                          name="ffocoa"
                          value="0"
                          id="ffocoa-no"
                          class="btn-check"
                          <?= $current['ffo_check_out_allowed'] == 0 ? 'checked' : '' ?>
                        >

                        <label for="ffocoa-no">
                          Tidak
                        </label>

                      </div>

                    </div>

                  </div>

                </div>

              </div>

            </section>


            <!-- =================================================
                 PENALTY KETERLAMBATAN
            ================================================== -->
            <section class="config-section">

              <div class="section-heading">

                <div class="section-icon bg-warning-subtle text-warning">
                  <i class="ti ti-clock-exclamation"></i>
                </div>

                <div>
                  <h6 class="section-title">
                    Penalty Keterlambatan
                  </h6>

                  <p class="section-description">
                    Atur potongan keterlambatan, absen pulang, dan upah lembur.
                  </p>

                </div>

              </div>


              <div class="settings-list">

                <!-- Penalty Terlambat -->
                <div class="setting-row">

                  <div class="setting-info">

                    <div class="setting-icon text-warning">
                      <i class="ti ti-clock-exclamation"></i>
                    </div>

                    <div>

                      <div class="fw-semibold">
                        Penalty Terlambat
                      </div>

                      <div class="text-muted small">
                        Potongan ketika karyawan terlambat masuk.
                      </div>

                    </div>

                  </div>


                  <div class="setting-control">

                    <div class="compact-choice">

                      <input
                        type="radio"
                        name="latePenalty"
                        value="1"
                        id="latePenalty-yes"
                        class="btn-check"
                        <?= $current['late_penalty'] == 1 ? 'checked' : '' ?>
                      >

                      <label for="latePenalty-yes">
                        Ya
                      </label>


                      <input
                        type="radio"
                        name="latePenalty"
                        value="0"
                        id="latePenalty-no"
                        class="btn-check"
                        <?= $current['late_penalty'] == 0 ? 'checked' : '' ?>
                      >

                      <label for="latePenalty-no">
                        Tidak
                      </label>

                    </div>


                    <div class="dynamic-control">

                      <div class="input-group">

                        <span class="input-group-text">
                          Rp
                        </span>

                        <input
                          type="number"
                          value="<?= htmlspecialchars($current['penalty_nominal']) ?>"
                          min="0"
                          name="penaltyNominal"
                          class="form-control"
                        >

                      </div>

                    </div>

                  </div>

                </div>


                <!-- Clockout -->
                <div class="setting-row">

                  <div class="setting-info">

                    <div class="setting-icon text-secondary">
                      <i class="ti ti-logout-2"></i>
                    </div>

                    <div>

                      <div class="fw-semibold">
                        Penalty Absen Pulang
                      </div>

                      <div class="text-muted small">
                        Terapkan penalty ketika karyawan tidak melakukan absen pulang.
                      </div>

                    </div>

                  </div>


                  <div class="setting-control">

                    <div class="compact-choice">

                      <input
                        type="radio"
                        name="clockoutPenalty"
                        value="1"
                        id="clockoutPenalty-yes"
                        class="btn-check"
                        <?= $current['clockout_penalty'] == 1 ? 'checked' : '' ?>
                      >

                      <label for="clockoutPenalty-yes">
                        Ya
                      </label>


                      <input
                        type="radio"
                        name="clockoutPenalty"
                        value="0"
                        id="clockoutPenalty-no"
                        class="btn-check"
                        <?= $current['clockout_penalty'] == 0 ? 'checked' : '' ?>
                      >

                      <label for="clockoutPenalty-no">
                        Tidak
                      </label>

                    </div>

                  </div>

                </div>


                <!-- Overtime -->
                <div class="setting-row">

                  <div class="setting-info">

                    <div class="setting-icon text-success">
                      <i class="ti ti-businessplan"></i>
                    </div>

                    <div>

                      <div class="fw-semibold">
                        Upah Lembur
                      </div>

                      <div class="text-muted small">
                        Nominal dasar yang digunakan untuk perhitungan lembur.
                      </div>

                    </div>

                  </div>


                  <div class="setting-control">

                    <div class="dynamic-control">

                      <div class="input-group">

                        <span class="input-group-text">
                          Rp
                        </span>

                        <input
                          type="number"
                          value="<?= htmlspecialchars($current['overwork_fee']) ?>"
                          min="0"
                          name="overworkFee"
                          class="form-control"
                          required
                        >

                      </div>

                    </div>

                  </div>

                </div>

              </div>

            </section>


            <!-- =================================================
                 PENALTY ALPHA
            ================================================== -->
            <section class="config-section">

              <div class="section-heading">

                <div class="section-icon bg-danger-subtle text-danger">
                  <i class="ti ti-user-x"></i>
                </div>

                <div>

                  <h6 class="section-title">
                    Penalty Alpha
                  </h6>

                  <p class="section-description">
                    Atur konsekuensi ketika karyawan tidak hadir tanpa keterangan.
                  </p>

                </div>

              </div>


              <!-- =================================================
                   ALPHA CONFIGURATION
              ================================================== -->
              <div class="complex-setting">

                <!-- Main -->
                <div class="complex-setting-main">

                  <div class="setting-icon text-danger">
                    <i class="ti ti-user-x"></i>
                  </div>

                  <div>

                    <div class="fw-semibold">
                      Menerapkan Penalty Alpha
                    </div>

                    <div class="text-muted small">
                      Tentukan konsekuensi dan nilai penalty alpha.
                    </div>

                  </div>

                </div>


                <!-- Controls -->
                <div class="complex-setting-controls">

                  <!-- Alpha Toggle -->
                  <div class="compact-choice">

                    <input
                      type="radio"
                      name="alphaPenalty"
                      value="1"
                      id="alphaPenalty-yes"
                      class="btn-check"
                      <?= $current['alpha_penalty'] == 1 ? 'checked' : '' ?>
                      onchange="toggleAlphaPenalty()"
                    >

                    <label for="alphaPenalty-yes">
                      Ya
                    </label>


                    <input
                      type="radio"
                      name="alphaPenalty"
                      value="0"
                      id="alphaPenalty-no"
                      class="btn-check"
                      <?= $current['alpha_penalty'] == 0 ? 'checked' : '' ?>
                      onchange="toggleAlphaPenalty()"
                    >

                    <label for="alphaPenalty-no">
                      Tidak
                    </label>

                  </div>


                  <!-- Alpha Fields -->
                  <div
                    id="alphaPenaltyFields"
                    class="alpha-fields <?= $current['alpha_penalty'] == 1 ? '' : 'd-none' ?>"
                  >

                    <!-- Type -->
                    <div id="alphaPenaltyType">

                      <label class="form-label small">
                        Type
                      </label>

                      <div class="compact-choice">

                        <input
                          type="radio"
                          name="alphaPenaltyType"
                          value="custom"
                          id="apt-custom"
                          class="btn-check"
                          <?= $current['alpha_penalty_type'] == 'custom' ? 'checked' : '' ?>
                          onchange="toggleAlphaPenaltyType()"
                        >

                        <label for="apt-custom">
                          Rp.
                        </label>


                        <input
                          type="radio"
                          name="alphaPenaltyType"
                          value="percent"
                          id="apt-percent"
                          class="btn-check"
                          <?= $current['alpha_penalty_type'] == 'percent' ? 'checked' : '' ?>
                          onchange="toggleAlphaPenaltyType()"
                        >

                        <label for="apt-percent">
                          %
                        </label>

                      </div>

                    </div>


                    <!-- Consequence -->
                    <div>

                      <label class="form-label small">
                        Konsekuensi
                      </label>

                      <div class="compact-choice">

                        <input
                          type="radio"
                          name="alphaConsequence"
                          value="1"
                          id="ac-salary"
                          class="btn-check"
                          <?= $current['alpha_consequence'] == '1' ? 'checked' : '' ?>
                          onchange="toggleAlphaConsequence()"
                        >

                        <label for="ac-salary">
                          Salary
                        </label>


                        <input
                          type="radio"
                          name="alphaConsequence"
                          value="2"
                          id="ac-offdays"
                          class="btn-check"
                          <?= $current['alpha_consequence'] == '2' ? 'checked' : '' ?>
                          onchange="toggleAlphaConsequence()"
                        >

                        <label for="ac-offdays">
                          Cuti
                        </label>

                      </div>

                    </div>


                    <!-- Value -->
                    <div id="alphaPenaltyValue">

                      <label
                        for="alphaPenaltyValueInput"
                        class="form-label small"
                      >
                        Nilai
                      </label>

                      <div class="input-group">

                        <span
                          class="input-group-text"
                          id="alphaPenaltyUnit"
                        >
                          Rp
                        </span>

                        <input
                          type="number"
                          value="<?= htmlspecialchars($current['alpha_penalty_value']) ?>"
                          min="0"
                          name="alphaPenaltyValue"
                          id="alphaPenaltyValueInput"
                          class="form-control"
                        >

                      </div>

                    </div>

                  </div>

                </div>

              </div>


              <!-- =================================================
                   ALPHA WARNING
              ================================================== -->
              <div
                id="alphaOffdaysWarning"
                class="alpha-warning <?= $current['alpha_consequence'] == '2' && $current['alpha_penalty'] == 1 ? '' : 'd-none' ?>"
              >

                <div class="alpha-warning-icon">
                  <i class="ti ti-alert-triangle"></i>
                </div>

                <div class="alpha-warning-content">

                  <div class="fw-semibold">
                    Perhatian
                  </div>

                  <div>
                    Jika karyawan tidak memiliki saldo cuti yang tersedia,
                    konsekuensi alpha akan dialihkan menjadi
                    <strong>potongan 100% gaji</strong>.
                  </div>

                </div>

              </div>

            </section>


            <!-- =================================================
                 PENALTY ISTIRAHAT
            ================================================== -->
            <section class="config-section mb-0">

              <div class="section-heading">

                <div class="section-icon bg-info-subtle text-info">
                  <i class="ti ti-coffee"></i>
                </div>

                <div>

                  <h6 class="section-title">
                    Penalty Istirahat
                  </h6>

                  <p class="section-description">
                    Atur konsekuensi ketika karyawan terlambat kembali dari istirahat.
                  </p>

                </div>

              </div>


              <div class="complex-setting">

                <!-- Main -->
                <div class="complex-setting-main">

                  <div class="setting-icon text-info">
                    <i class="ti ti-coffee"></i>
                  </div>

                  <div>

                    <div class="fw-semibold">
                      Menerapkan Penalty Istirahat
                    </div>

                    <div class="text-muted small">
                      Tentukan jenis dan nilai potongan istirahat.
                    </div>

                  </div>

                </div>


                <!-- Controls -->
                <div class="complex-setting-controls">

                  <!-- Enable -->
                  <div class="compact-choice">

                    <input
                      type="radio"
                      name="afterBreakLatePenalty"
                      value="1"
                      id="ablp-yes"
                      class="btn-check"
                      <?= $current['after_break_late_penalty'] == 1 ? 'checked' : '' ?>
                      onchange="toggleBreakPenalty()"
                    >

                    <label for="ablp-yes">
                      Ya
                    </label>


                    <input
                      type="radio"
                      name="afterBreakLatePenalty"
                      value="0"
                      id="ablp-no"
                      class="btn-check"
                      <?= $current['after_break_late_penalty'] == 0 ? 'checked' : '' ?>
                      onchange="toggleBreakPenalty()"
                    >

                    <label for="ablp-no">
                      Tidak
                    </label>

                  </div>


                  <!-- Break Fields -->
                  <div
                    id="breakPenaltyFields"
                    class="break-fields <?= $current['after_break_late_penalty'] == 1 ? '' : 'd-none' ?>"
                  >

                    <!-- Type -->
                    <div>

                      <label class="form-label small">
                        Type
                      </label>

                      <div class="compact-choice">

                        <input
                          type="radio"
                          name="afterBreakLatePenaltyType"
                          value="fixed"
                          id="ablt-fixed"
                          class="btn-check"
                          <?= $current['after_break_late_penalty_type'] == 'fixed' ? 'checked' : '' ?>
                          onchange="toggleBreakPenaltyType()"
                        >

                        <label for="ablt-fixed">
                          Fixed
                        </label>


                        <input
                          type="radio"
                          name="afterBreakLatePenaltyType"
                          value="minute"
                          id="ablt-minute"
                          class="btn-check"
                          <?= $current['after_break_late_penalty_type'] == 'minute' ? 'checked' : '' ?>
                          onchange="toggleBreakPenaltyType()"
                        >

                        <label for="ablt-minute">
                          Minute
                        </label>


                        <input
                          type="radio"
                          name="afterBreakLatePenaltyType"
                          value="no"
                          id="ablt-no"
                          class="btn-check"
                          <?= $current['after_break_late_penalty_type'] == 'no' ? 'checked' : '' ?>
                          onchange="toggleBreakPenaltyType()"
                        >

                        <label for="ablt-no">
                          No
                        </label>

                      </div>

                    </div>


                    <!-- Value -->
                    <div>

                      <label
                        for="afterBreakLatePenaltyValueInput"
                        class="form-label small"
                      >
                        Nilai
                      </label>

                      <div class="input-group">

                        <span
                          class="input-group-text"
                          id="afterBreakLatePenaltyUnit"
                        >
                          Rp
                        </span>

                        <input
                          type="number"
                          value="<?= htmlspecialchars($current['after_break_late_penalty_value']) ?>"
                          min="0"
                          name="afterBreakLatePenaltyValue"
                          id="afterBreakLatePenaltyValueInput"
                          class="form-control"
                        >

                      </div>

                      <div
                        class="form-text"
                        id="afterBreakLatePenaltyHelp"
                      >
                        Nominal potongan tetap.
                      </div>

                    </div>

                  </div>

                </div>

              </div>

            </section>

          </div>


          <!-- =====================================================
               FOOTER
          ====================================================== -->
          <div class="card-footer bg-body border-top px-4 px-md-5 py-3">

            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">

              <div class="text-muted small">
                <span class="text-danger">*</span>
                Wajib diisi
              </div>

              <div class="d-flex gap-2">

                <a
                  href="<?= base_url('karyawan/division') ?>"
                  class="btn btn-outline-secondary"
                >
                  <i class="ti ti-arrow-left me-1"></i>
                  Batal
                </a>

                <button
                  type="submit"
                  class="btn btn-primary px-4"
                >
                  <i class="ti ti-device-floppy me-1"></i>
                  Simpan Perubahan
                </button>

              </div>

            </div>

          </div>

        </form>

      </div>

    </div>
  </div>

</div>


<!-- =========================================================
     STYLE
========================================================= -->
<style>

/* =========================================================
   PAGE ICON
========================================================= */

.page-icon {
  width: 48px;
  height: 48px;
  flex-shrink: 0;

  display: flex;
  align-items: center;
  justify-content: center;

  border-radius: .75rem;
  font-size: 1.5rem;
}


/* =========================================================
   SECTION
========================================================= */

.config-section {
  padding-bottom: 2.5rem;
  margin-bottom: 2.5rem;

  border-bottom: 1px solid var(--bs-border-color);
}

.config-section:last-child {
  padding-bottom: 0;
  margin-bottom: 0;
  border-bottom: 0;
}


/* =========================================================
   SECTION HEADING
========================================================= */

.section-heading {
  display: flex;
  align-items: center;
  gap: .875rem;

  margin-bottom: 1.5rem;
}

.section-icon {
  width: 38px;
  height: 38px;
  flex-shrink: 0;

  display: flex;
  align-items: center;
  justify-content: center;

  border-radius: .65rem;
  font-size: 1.15rem;
}

.section-title {
  margin: 0 0 .2rem;

  font-size: .95rem;
  font-weight: 600;
}

.section-description {
  margin: 0;

  color: var(--bs-secondary-color);
  font-size: .8rem;
}

.section-content {
  padding-left: calc(38px + .875rem);
}


/* =========================================================
   FORM
========================================================= */

.form-label {
  margin-bottom: .5rem;

  font-size: .8125rem;
  font-weight: 500;
}

.form-control,
.form-select {
  min-height: 40px;
}


/* =========================================================
   WORK PATTERN
========================================================= */

.choice-grid {
  display: grid;

  grid-template-columns:
    repeat(2, minmax(0, 1fr));

  gap: .75rem;
}

.choice-card {
  display: flex;
  align-items: center;

  gap: .75rem;

  min-height: 64px;
  padding: .75rem 1rem;

  border: 1px solid var(--bs-border-color);
  border-radius: .65rem;

  cursor: pointer;

  transition: .15s ease;
}

.choice-card > i {
  font-size: 1.35rem;
  color: var(--bs-primary);
}

.choice-card span {
  display: flex;
  flex-direction: column;
  gap: .1rem;
}

.choice-card strong {
  font-size: .875rem;
}

.choice-card small {
  color: var(--bs-secondary-color);
  font-size: .75rem;
}

.btn-check:checked + .choice-card {
  border-color: var(--bs-primary);

  background: var(--bs-primary-bg-subtle);
  color: var(--bs-primary-text-emphasis);
}


/* =========================================================
   SIMPLE SETTING
========================================================= */

.simple-setting {
  display: flex;
  align-items: center;
  justify-content: space-between;

  gap: 1rem;

  min-height: 64px;
  padding: 1rem;

  border: 1px solid var(--bs-border-color);
  border-radius: .65rem;
}


/* =========================================================
   COMPACT CHOICE
========================================================= */

.compact-choice {
  display: inline-grid;

  grid-template-columns:
    repeat(2, minmax(62px, 1fr));

  min-width: 126px;
  flex-shrink: 0;

  border: 1px solid var(--bs-border-color);
  border-radius: .55rem;

  overflow: hidden;
}

.compact-choice label {
  margin: 0;
  padding: .45rem .65rem;

  text-align: center;

  font-size: .75rem;
  font-weight: 500;

  color: var(--bs-secondary-color);
  background: var(--bs-body-bg);

  cursor: pointer;

  transition: .15s ease;
}

.compact-choice label + label {
  border-left: 1px solid var(--bs-border-color);
}

.compact-choice .btn-check:checked + label {
  color: var(--bs-primary);
  background: var(--bs-primary-bg-subtle);
}


/* =========================================================
   SETTINGS LIST
========================================================= */

.settings-list {
  border: 1px solid var(--bs-border-color);
  border-radius: .75rem;

  overflow: hidden;
}

.setting-row {
  display: grid;

  grid-template-columns:
    minmax(0, 1fr)
    minmax(300px, 430px);

  align-items: center;

  gap: 2rem;

  padding: 1.25rem;
}

.setting-row + .setting-row {
  border-top: 1px solid var(--bs-border-color);
}

.setting-info {
  display: flex;
  align-items: flex-start;

  gap: .875rem;

  min-width: 0;
}

.setting-icon {
  width: 34px;
  flex-shrink: 0;

  font-size: 1.35rem;
  text-align: center;
}

.setting-control {
  display: flex;
  align-items: center;
  justify-content: flex-end;

  gap: .75rem;

  min-width: 0;
}

.dynamic-control {
  width: 210px;
}


/* =========================================================
   COMPLEX SETTING
========================================================= */

.complex-setting {
  display: grid;

  grid-template-columns:
    minmax(260px, .8fr)
    minmax(0, 1.8fr);

  align-items: center;

  gap: 2rem;

  padding: 1.25rem;

  border: 1px solid var(--bs-border-color);
  border-radius: .75rem;
}

.complex-setting-main {
  display: flex;
  align-items: flex-start;

  gap: .875rem;

  min-width: 0;
}

.complex-setting-controls {
  display: flex;
  align-items: center;
  justify-content: flex-end;

  gap: .75rem;

  min-width: 0;
}


/* =========================================================
   ALPHA FIELDS
========================================================= */

.alpha-fields {
  display: grid;

  grid-template-columns:
    minmax(120px, 1fr)
    minmax(150px, 1.2fr)
    minmax(130px, 160px);

  align-items: start;

  gap: .75rem;

  min-width: 0;
  flex: 1;
}

.alpha-fields > div {
  min-width: 0;
}

.alpha-fields .compact-choice {
  width: 100%;
}


/* =========================================================
   ALPHA WARNING
   WARNING DI LUAR BORDER COMPLEX SETTING
========================================================= */

.alpha-warning {
  display: flex;
  align-items: flex-start;

  gap: .75rem;

  margin-top: .75rem;
  padding: .75rem .875rem;

  border: 1px solid var(--bs-warning-border-subtle);
  border-radius: .65rem;

  background: var(--bs-warning-bg-subtle);
  color: var(--bs-warning-text-emphasis);

  font-size: .75rem;
  line-height: 1.5;
}

.alpha-warning-icon {
  width: 30px;
  height: 30px;
  flex-shrink: 0;

  display: flex;
  align-items: center;
  justify-content: center;

  border-radius: .5rem;

  background: var(--bs-warning-bg-subtle);

  font-size: 1rem;
}

.alpha-warning-content {
  min-width: 0;
}

.alpha-warning-content .fw-semibold {
  margin-bottom: .15rem;
}


/* =========================================================
   BREAK FIELDS
========================================================= */

.break-fields {
  display: grid;

  grid-template-columns:
    minmax(140px, 180px)
    minmax(180px, 230px);

  align-items: start;

  gap: .75rem;

  min-width: 0;
  flex: 1;
}

.break-fields > div {
  min-width: 0;
}

.break-fields .compact-choice {
  width: 100%;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 1199.98px) {

  .complex-setting {
    grid-template-columns:
      minmax(220px, .7fr)
      minmax(0, 1.8fr);
  }

  .alpha-fields {
    grid-template-columns:
      repeat(2, minmax(0, 1fr));
  }

}


@media (max-width: 991.98px) {

  .section-content {
    padding-left: 0;
  }

  .setting-row {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .setting-control {
    justify-content: flex-start;
  }

  .complex-setting {
    grid-template-columns: 1fr;
    gap: 1.25rem;
  }

  .complex-setting-controls {
    justify-content: flex-start;
    flex-wrap: wrap;
  }

  .alpha-fields,
  .break-fields {
    flex: 1;
  }

}


@media (max-width: 767.98px) {

  .choice-grid {
    grid-template-columns: 1fr;
  }

  .simple-setting {
    align-items: flex-start;
    flex-direction: column;
  }

  .simple-setting .compact-choice {
    width: 100%;
  }

  .setting-control {
    align-items: stretch;
    flex-direction: column;
  }

  .setting-control > .compact-choice,
  .dynamic-control {
    width: 100%;
  }

  .complex-setting-controls {
    align-items: stretch;
    flex-direction: column;
  }

  .complex-setting-controls > .compact-choice {
    width: 100%;
  }

  .alpha-fields,
  .break-fields {
    width: 100%;
    grid-template-columns: 1fr;
  }

}


@media (max-width: 575.98px) {

  .card-body,
  .card-header,
  .card-footer {
    padding-left: 1rem !important;
    padding-right: 1rem !important;
  }

  .config-section {
    padding-bottom: 2rem;
    margin-bottom: 2rem;
  }

  .section-heading {
    align-items: flex-start;
  }

  .section-icon {
    width: 34px;
    height: 34px;

    font-size: 1rem;
  }

  .setting-row,
  .complex-setting {
    padding: 1rem;
  }

}


/* =========================================================
   HIDDEN
========================================================= */

.d-none {
  display: none !important;
}

</style>


<!-- =========================================================
     SCRIPT
========================================================= -->
<script type="text/javascript">

/* =========================================================
   POLA KERJA
========================================================= */

function showWeeklyList() {

  document
    .getElementById('weeklyList')
    .classList.remove('d-none');

  document
    .getElementById('shiftList')
    .classList.add('d-none');

  setPattern(
    document.getElementById('weeklyPattern').value
  );

}


function showShiftList() {

  document
    .getElementById('weeklyList')
    .classList.add('d-none');

  document
    .getElementById('shiftList')
    .classList.remove('d-none');

  setPattern(
    document.getElementById('shiftPattern').value
  );

}


function setPattern(value) {

  document.getElementById('pattern').value = value;

}


/* =========================================================
   PENALTY ALPHA
========================================================= */

function toggleAlphaPenalty() {

  const enabled =
    document.querySelector(
      'input[name="alphaPenalty"]:checked'
    )?.value === '1';


  const fields =
    document.getElementById(
      'alphaPenaltyFields'
    );

  const warning =
    document.getElementById(
      'alphaOffdaysWarning'
    );


  fields.classList.toggle(
    'd-none',
    !enabled
  );


  warning.classList.add('d-none');


  document.querySelectorAll(
    'input[name="alphaPenaltyType"], input[name="alphaConsequence"]'
  ).forEach(input => {

    input.required = enabled;

  });


  const value =
    document.getElementById(
      'alphaPenaltyValueInput'
    );

  value.required = enabled;


  if (!enabled) {
    return;
  }


  toggleAlphaConsequence();

}


/* =========================================================
   ALPHA CONSEQUENCE
========================================================= */

function toggleAlphaConsequence() {

  const consequence =
    document.querySelector(
      'input[name="alphaConsequence"]:checked'
    )?.value;


  const type =
    document.getElementById(
      'alphaPenaltyType'
    );

  const valueContainer =
    document.getElementById(
      'alphaPenaltyValue'
    );

  const warning =
    document.getElementById(
      'alphaOffdaysWarning'
    );

  const custom =
    document.getElementById(
      'apt-custom'
    );

  const percent =
    document.getElementById(
      'apt-percent'
    );

  const value =
    document.getElementById(
      'alphaPenaltyValueInput'
    );

  const unit =
    document.getElementById(
      'alphaPenaltyUnit'
    );


  const isOffdays =
    consequence === '2';


  /* =======================================================
     CUTI
  ====================================================== */

  if (isOffdays) {

    warning.classList.remove('d-none');

    percent.checked = true;
    custom.checked = false;

    custom.disabled = true;

    type.classList.add('d-none');
    valueContainer.classList.add('d-none');

    value.value = 100;
    value.readOnly = true;
    value.required = false;
    value.max = 100;

    unit.textContent = '%';

    return;

  }


  /* =======================================================
     SALARY
  ====================================================== */

  warning.classList.add('d-none');

  type.classList.remove('d-none');
  valueContainer.classList.remove('d-none');

  custom.disabled = false;

  value.readOnly = false;
  value.required = true;

  value.removeAttribute('max');

  toggleAlphaPenaltyType();

}


/* =========================================================
   ALPHA TYPE
========================================================= */

function toggleAlphaPenaltyType() {

  const type =
    document.querySelector(
      'input[name="alphaPenaltyType"]:checked'
    )?.value;


  const unit =
    document.getElementById(
      'alphaPenaltyUnit'
    );

  const value =
    document.getElementById(
      'alphaPenaltyValueInput'
    );


  if (type === 'percent') {

    unit.textContent = '%';

    value.max = 100;

    return;

  }


  unit.textContent = 'Rp';

  value.removeAttribute('max');

}


/* =========================================================
   PENALTY ISTIRAHAT
========================================================= */

function toggleBreakPenalty() {

  const enabled =
    document.querySelector(
      'input[name="afterBreakLatePenalty"]:checked'
    )?.value === '1';


  const fields =
    document.getElementById(
      'breakPenaltyFields'
    );


  fields.classList.toggle(
    'd-none',
    !enabled
  );


  document.querySelectorAll(
    'input[name="afterBreakLatePenaltyType"]'
  ).forEach(input => {

    input.required = enabled;

  });


  const value =
    document.getElementById(
      'afterBreakLatePenaltyValueInput'
    );


  value.required = enabled;


  if (!enabled) {
    return;
  }


  toggleBreakPenaltyType();

}


/* =========================================================
   PENALTY ISTIRAHAT TYPE
========================================================= */

function toggleBreakPenaltyType() {

  const type =
    document.querySelector(
      'input[name="afterBreakLatePenaltyType"]:checked'
    )?.value;


  const value =
    document.getElementById(
      'afterBreakLatePenaltyValueInput'
    );

  const unit =
    document.getElementById(
      'afterBreakLatePenaltyUnit'
    );

  const help =
    document.getElementById(
      'afterBreakLatePenaltyHelp'
    );


  if (type === 'minute') {

    unit.textContent = 'menit';

    help.textContent =
      'Potongan dihitung berdasarkan jumlah menit keterlambatan istirahat.';

    return;

  }


  if (type === 'fixed') {

    unit.textContent = 'Rp';

    help.textContent =
      'Nominal potongan tetap ketika karyawan melewati batas istirahat.';

    return;

  }


  unit.textContent = 'Rp';

  help.textContent =
    'Penalty istirahat tidak diterapkan.';

}


/* =========================================================
   INITIAL STATE
========================================================= */

document.addEventListener(
  'DOMContentLoaded',
  function () {

    toggleAlphaPenalty();
    toggleBreakPenalty();

  }
);

</script>
