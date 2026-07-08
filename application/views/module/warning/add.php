<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header border-bottom d-flex align-items-center justify-content-between">
      <h5 class="card-title mb-0">Tambah Surat Peringatan</h5>
      <a href="<?= base_url('warning'); ?>" class="btn btn-sm btn-label-secondary">
        <i class="ti ti-arrow-left me-1"></i> Kembali
      </a>
    </div>
    <div class="card">
      <form class="card-body" action="<?= base_url('warning/add_proses'); ?>" method="POST" id="warningForm">
        <?= $this->session->flashdata('message'); ?>

        <div class="row g-3">

          <div class="flex flex-col gap-1">
            <label class="form-label" for="title">
              Tugaskan kepada <i class="text-danger">*</i>
            </label>
            <input name="nik" onKeyUp="onKeyChg(this)" id="target2" list="employees" placeholder="card id or name" type="text" class="p-3 border-2 border-black rounded-md" placeholder=""/>
            <datalist id="employees">
            </datalist>
          </div>

          <!-- Nomor SP (auto-generate, read-only) -->
          <div class="col-xl-6 col-md-6 col-sm-12">
            <label class="form-label" for="sp_number">
              Nomor SP <i class="text-danger">*</i>
            </label>
            <input
              type="text"
              class="form-control bg-light"
              id="sp_number"
              name="sp_number"
              value="<?= htmlspecialchars($sp_number); ?>"
              readonly
            />
            <small class="text-muted">Nomor otomatis berdasarkan urutan.</small>
          </div>

          <!-- Level SP -->
          <div class="col-xl-6 col-md-6 col-sm-12">
            <label class="form-label" for="level">
              Level / Tingkat SP <i class="text-danger">*</i>
            </label>
            <select class="form-select" id="level" name="level" required>
              <option value="" disabled selected>-- Pilih Level --</option>
              <option value="1">SP 1 – Peringatan Pertama</option>
              <option value="2">SP 2 – Peringatan Kedua</option>
              <option value="3">SP 3 – Peringatan Ketiga / Terakhir</option>
            </select>
          </div>

          <!-- Judul -->
          <div class="col-12">
            <label class="form-label" for="title">
              Judul Surat Peringatan <i class="text-danger">*</i>
            </label>
            <input
              type="text"
              class="form-control"
              id="title"
              name="title"
              placeholder="Contoh: Pelanggaran Kedisiplinan Kehadiran"
              required
              autocomplete="off"
            />
          </div>

          <!-- Violation / Deskripsi Kejadian -->
          <div class="col-12">
            <label class="form-label" for="violation">
              Deskripsi Kejadian / Pelanggaran <i class="text-danger">*</i>
            </label>
            <textarea
              class="form-control"
              id="violation"
              name="violation"
              rows="5"
              placeholder="Uraikan secara detail kronologi dan pelanggaran yang dilakukan karyawan..."
              required
            ></textarea>
          </div>

          <!-- Tanggal Kejadian -->
          <div class="col-xl-6 col-md-6 col-sm-12">
            <label class="form-label" for="date">
              Tanggal Kejadian <i class="text-danger">*</i>
            </label>
            <input
              type="text"
              class="form-control"
              id="date"
              name="date"
              placeholder="YYYY-MM-DD"
              required
            />
            <small class="text-muted">Tanggal terjadinya pelanggaran.</small>
          </div>

        </div><!-- /.row -->

        <div class="pt-5 text-end">
          <a href="javascript:window.history.back();" class="btn btn-label-secondary me-sm-3 me-1">Batal</a>
          <button type="submit" class="btn btn-primary" id="btnSubmit">
            <i class="ti ti-device-floppy me-1"></i> Simpan SP
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function () {
    // Flatpickr for deadline
    $('#date').flatpickr({
      minDate: 'today',
      dateFormat: 'Y-m-d'
    });
  });
</script>


<script type="text/javascript">
  const BASE_URL = "<?= base_url(); ?>";

  function parse(data){
    const employees = document.getElementById("employees")
    employees.innerHTML = ""
    data.forEach(e => {
      const option = document.createElement("option")
      option.value = `${e.nama_pegawai} (${e.pegawai_id})`;
      option.text = e.nama_pegawai
      employees.appendChild(option)
    })
  }

  function onKeyChg(e){
    const employees = document.getElementById("e")
    const value = target2.value
    
    fetch(BASE_URL + "karyawan/data/all?divId=" + "Any" + "&key=" + value)
      .then(response => response.json())
      .then(data => parse(data))
  }
</script>
