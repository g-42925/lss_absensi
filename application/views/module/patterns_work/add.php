<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header border-bottom d-flex align-items-center justify-content-between">
      <h5 class="card-title mb-0">
        <i class="ti ti-edit me-2 text-primary"></i>Tambah Pola Kerja Mingguan
      </h5>
      <a href="<?=base_url('patterns_work');?>" class="btn btn-sm btn-label-secondary">
        <i class="ti ti-arrow-left me-1"></i>Kembali
      </a>
    </div>


    <form class="card-body" action="<?=base_url('patterns_work/add_proses');?>" method="POST">
      <?php if ($failed): ?>
        <?=$this->session->flashdata('message');?>
      <?php endif; ?>

      <!-- Info Pola Kerja -->
      <div class="row g-3 mb-4 card-body">
        <div class="col-xl-4 col-md-6 col-sm-12">
          <label class="form-label fw-semibold">Nama Pola <i class="text-danger">*</i></label>
          <input type="text" class="form-control" name="nama" autocomplete="off" placeholder="Nama pola kerja..." required />
        </div>
        <div class="col-xl-4 col-md-6 col-sm-12">
          <label class="form-label fw-semibold">Toleransi Keterlambatan</label>
          <div class="input-group">
            <input type="number" class="form-control" name="tolet" placeholder="0" min="0">
            <span class="input-group-text">Menit</span>
          </div>
        </div>
        <div class="col-xl-4 col-md-6 col-sm-12">
          <label class="form-label fw-semibold">Jumlah Hari <i class="text-danger">*</i></label>
          <div class="input-group">
            <span class="input-group-text"><i class="ti ti-calendar"></i></span>
            <input type="number" class="form-control" name="jumlahhari" id="jumlahhari"
              autocomplete="off" placeholder="Cth: 7" min="1" max="365"
              oninput="checkPolaHari(this.value)" required />
          </div>
          <small class="text-muted" id="alertxjumlah"></small>
        </div>
      </div>

      <!-- Divider -->
      <div class="d-flex align-items-center gap-3 mb-3" id="divider-siklus" style="display:none;">
        <hr class="flex-grow-1 m-0">
        <span class="badge bg-label-primary px-3 py-2 fs-6">
          <i class="ti ti-clock me-1"></i>Jadwal Kerja Per Hari
        </span>
        <hr class="flex-grow-1 m-0">
      </div>

      <!-- Jadwal Kerja Cards -->
      <div id="siklus_pola_kerja_xm" class="row g-3">
        <!-- Diisi oleh JavaScript -->
      </div>

      <div class="d-flex justify-content-end gap-2 mt-4 border-top pt-4">
        <a href="javascript:window.history.back();" class="btn btn-label-secondary">
          <i class="ti ti-x me-1"></i>Batal
        </a>
        <button type="submit" class="btn btn-primary">
          <i class="ti ti-device-floppy me-1"></i>Simpan Data
        </button>
      </div>
    </form>
  </div>
</div>
<!-- / Content -->

<style>
  .day-card {
    border: 1px solid #e7e7e7;
    border-radius: 10px;
    background: #fff;
    transition: box-shadow 0.2s, border-color 0.2s;
  }
  .day-card:hover {
    box-shadow: 0 4px 16px rgba(105,108,255,0.10);
    border-color: #696cff55;
  }
  .day-badge {
    min-width: 60px;
    text-align: center;
    font-weight: 600;
    font-size: 0.85rem;
    border-radius: 8px;
  }
  .time-group-label {
    font-size: 0.72rem;
    color: #8592a3;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
  }
  .time-separator {
    font-size: 0.8rem;
    color: #a0a0a0;
    padding: 0 4px;
    align-self: flex-end;
    margin-bottom: 6px;
  }
  .work-status-select {
    min-width: 130px;
  }
  .day-card .flatpickr-input {
    text-align: center;
    font-size: 0.92rem;
    font-weight: 500;
    min-width: 80px;
  }
</style>

<script type="text/javascript">
  var dayNames = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];

  function checkPolaHari(a) {
    a = parseInt(a);

    if (isNaN(a) || a < 1) {
      $('#siklus_pola_kerja_xm').html('');
      $('#divider-siklus').hide();
      return;
    }

    if (a > 365) {
      $('#alertxjumlah').html('<span class="text-danger">Maksimal 365 Hari</span>');
      $('#jumlahhari').val('');
      $('#siklus_pola_kerja_xm').html('');
      $('#divider-siklus').hide();
      return;
    }

    $('#alertxjumlah').html('');

    var container = $('#siklus_pola_kerja_xm');
    container.html('');
    $('#divider-siklus').show();

    for (var i = 1; i <= a; i++) {
      var dayLabel = 'Hari ' + i;
      if (a === 7 && dayNames[i - 1]) {
        dayLabel = dayNames[i - 1];
      }

      var html = `
        <div class="col-xl-12 col-md-12">
          <div class="day-card p-3" id="daycard-${i}">
            <div class="d-flex align-items-center gap-3 flex-wrap">

              <!-- Badge Hari -->
              <div class="d-flex flex-column align-items-center" style="min-width:56px;">
                <span class="badge bg-label-success day-badge day-badge-${i} py-2 px-3 mb-1">
                  ${dayLabel}
                </span>
                <small class="text-muted" style="font-size:0.7rem;">Hari ${i}</small>
              </div>

              <!-- Status Kerja -->
              <div style="min-width:130px;">
                <div class="time-group-label">Status</div>
                <select class="form-control work-status-select" name="work[]"
                  onchange="onWorkStatusChange(${i}, this.value)" required>
                  <option value="y">Hari Kerja</option>
                  <option value="n">Libur</option>
                </select>
              </div>

              <!-- Input sistem kerja (hidden) -->
              <div class="d-none">
                <select name="sistemkerja[]">
                  <option value="1">WFO</option>
                  <option value="2">WFH</option>
                </select>
              </div>

              <!-- Jam Kerja -->
              <div class="time-fields-${i} d-flex align-items-start gap-2 flex-wrap" id="timefields-${i}">

                <!-- Jam Masuk - Pulang -->
                <div>
                  <div class="time-group-label"><i class="ti ti-door-enter" style="font-size:10px;"></i> Jam Kerja</div>
                  <div class="d-flex align-items-center gap-1">
                    <input type="text"
                      class="form-control flatpickr-input text-center"
                      style="width:80px;"
                      placeholder="hh:mm"
                      value="08:00"
                      id="flatpickr-time-work-m${i}"
                      readonly="readonly"
                      name="masuk[]">
                    <span class="time-separator">—</span>
                    <input type="text"
                      class="form-control flatpickr-input text-center"
                      style="width:80px;"
                      placeholder="hh:mm"
                      value="17:00"
                      id="flatpickr-time-work-p${i}"
                      readonly="readonly"
                      name="pulang[]">
                  </div>
                </div>

                <div class="time-separator align-self-end pb-1 px-1">|</div>

                <!-- Istirahat -->
                <div>
                  <div class="time-group-label"><i class="ti ti-coffee" style="font-size:10px;"></i> Istirahat</div>
                  <div class="d-flex align-items-center gap-1">
                    <input type="text"
                      id="breakStart-${i}"
                      class="form-control flatpickr-input text-center"
                      style="width:80px;"
                      value="00:00"
                      name="break[]">
                    <span class="time-separator">—</span>
                    <input type="text"
                      id="breakEnd-${i}"
                      class="form-control flatpickr-input text-center"
                      style="width:80px;"
                      value="13:00"
                      name="breakEnd[]">
                  </div>
                </div>

                <div class="time-separator align-self-end pb-1 px-1">|</div>

                <!-- C1 (Batas Cuti Setengah Hari) -->
                <div>
                  <div class="time-group-label"><i class="ti ti-clock-half-2" style="font-size:10px;"></i> Batas C1</div>
                  <div class="d-flex align-items-center gap-1">
                    <input type="text"
                      id="c1-${i}"
                      class="form-control flatpickr-input text-center"
                      style="width:80px;"
                      value="00:00"
                      name="c1[]">
                  </div>
                </div>

              </div>

              <!-- Placeholder saat Libur -->
              <div id="libur-placeholder-${i}" style="display:none; align-items:center;">
                <span class="text-muted fst-italic" style="font-size:0.88rem;">
                  <i class="ti ti-moon me-1"></i>Hari libur — tidak ada jadwal kerja
                </span>
              </div>

            </div>
          </div>
        </div>
      `;

      container.append(html);
    }

    // Init flatpickr for all time inputs
    for (var j = 1; j <= a; j++) {
      var twMm    = document.querySelector('#flatpickr-time-work-m' + j);
      var twPp    = document.querySelector('#flatpickr-time-work-p' + j);
      var bsStart = document.querySelector('#breakStart-' + j);
      var bsEnd   = document.querySelector('#breakEnd-' + j);
      var c1      = document.querySelector('#c1-' + j);
      if (twMm)    twMm.flatpickr({ enableTime: true, noCalendar: true, time_24hr: true });
      if (twPp)    twPp.flatpickr({ enableTime: true, noCalendar: true, time_24hr: true });
      if (bsStart) bsStart.flatpickr({ enableTime: true, noCalendar: true, time_24hr: true });
      if (bsEnd)   bsEnd.flatpickr({ enableTime: true, noCalendar: true, time_24hr: true });
      if (c1)      c1.flatpickr({ enableTime: true, noCalendar: true, time_24hr: true });
    }
  }

  function onWorkStatusChange(dayNo, val) {
    var card             = $('#daycard-' + dayNo);
    var timeFields       = $('#timefields-' + dayNo);
    var liburPlaceholder = $('#libur-placeholder-' + dayNo);
    var badge            = $('.day-badge-' + dayNo);

    if (val === 'y') {
      card.removeClass('bg-light');
      badge.removeClass('bg-label-secondary').addClass('bg-label-success');
      timeFields.css({ opacity: '1', 'pointer-events': 'auto' });
      liburPlaceholder.hide();
    } else {
      card.addClass('bg-light');
      badge.removeClass('bg-label-success').addClass('bg-label-secondary');
      timeFields.css({ opacity: '0.4', 'pointer-events': 'none' });
      liburPlaceholder.show();
    }
  }
</script>