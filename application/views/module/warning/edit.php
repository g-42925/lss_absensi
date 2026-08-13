<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header border-bottom d-flex align-items-center justify-content-between">
      <h5 class="card-title mb-0">Edit Surat Peringatan</h5>
      <a href="<?= base_url('warning'); ?>" class="btn btn-sm btn-label-secondary">
        <i class="ti ti-arrow-left me-1"></i> Kembali
      </a>
    </div>
    <div class="card">
      <form class="card-body" action="<?= base_url('warning/edit_proses/' . $warning['id']); ?>" method="POST" id="warningForm">
        <?= $this->session->flashdata('message'); ?>

        <div class="row g-3">

          <!-- Pilih Karyawan -->
          <div class="flex flex-col gap-1">
            <label class="form-label" for="nik_display">
              Karyawan <i class="text-danger">*</i>
            </label>
            <input
              name="nik"
              onKeyUp="onKeyChg(this)"
              id="target2"
              list="employees"
              placeholder="Ketik nama atau NIK karyawan..."
              type="text"
              class="p-3 border-2 border-black rounded-md"
              value="<?= htmlspecialchars($warning['nama_pegawai'] . ' (' . $warning['emp_id'] . ')'); ?>"
            />
            <datalist id="employees">
            </datalist>
          </div>

          <!-- Nomor SP (read-only, tidak berubah saat edit) -->
          <div class="col-xl-6 col-md-6 col-sm-12">
            <label class="form-label" for="sp_number">Nomor SP</label>
            <input
              type="text"
              class="form-control bg-light"
              id="sp_number"
              value="<?= htmlspecialchars($warning['sp_number']); ?>"
              readonly
            />
            <small class="text-muted">Nomor SP tidak dapat diubah.</small>
          </div>

          <!-- Level SP -->
          <div class="col-xl-6 col-md-6 col-sm-12">
            <label class="form-label" for="level">
              Level / Tingkat SP <i class="text-danger">*</i>
            </label>
            <select class="form-select" id="level" name="level" required>
              <option value="" disabled>-- Pilih Level --</option>
              <option value="1" <?= $warning['level'] == 1 ? 'selected' : ''; ?>>SP 1 – Peringatan Pertama</option>
              <option value="2" <?= $warning['level'] == 2 ? 'selected' : ''; ?>>SP 2 – Peringatan Kedua</option>
              <option value="3" <?= $warning['level'] == 3 ? 'selected' : ''; ?>>SP 3 – Peringatan Ketiga / Terakhir</option>
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
              value="<?= htmlspecialchars($warning['title']); ?>"
              placeholder="Contoh: Pelanggaran Kedisiplinan Kehadiran"
              required
              autocomplete="off"
            />
          </div>

          <!-- Violation -->
          <div class="col-12">
            <label class="form-label" for="violation">
              Deskripsi Kejadian / Pelanggaran <i class="text-danger">*</i>
            </label>
            <textarea
              class="form-control"
              id="violation"
              name="violation"
              rows="5"
              maxlength="333"
              placeholder="Uraikan secara detail kronologi dan pelanggaran yang dilakukan karyawan..."
              required
            ><?= htmlspecialchars($warning['violation']); ?></textarea>
          </div>

          <!-- Tanggal Kejadian -->
          <div class="col-4">
            <label class="form-label" for="date">
              Tanggal Kejadian <i class="text-danger">*</i>
            </label>
            <input
              type="text"
              class="form-control"
              id="date"
              name="date"
              value="<?= htmlspecialchars($warning['date']); ?>"
              placeholder="YYYY-MM-DD"
              required
            />
            <small class="text-muted">Tanggal terjadinya pelanggaran.</small>
          </div>

          <!-- masa berlaku -->
          <div class="col-4">
            <label class="form-label" for="expired">
              Masa Berlaku <i class="text-danger">*</i>
            </label>
            <input
              type="text"
              class="form-control"
              id="expired"
              name="expired"
              value="<?= htmlspecialchars($warning['expired']); ?>"
              placeholder="YYYY-MM-DD"
              required
            />
            <small class="text-muted">Tanggal berakhirnya masa berlaku.</small>
          </div>

          <!--  penalty nominal -->
          <div class="col-4">
            <label class="form-label" for="penalty_nominal">
              Penalty Nominal
            </label>
            <input
              value="<?= htmlspecialchars($warning['penalty']); ?>"
              type="number"
              class="form-control"
              id="penalty_nominal"
              name="penalty"
              placeholder="Contoh: 100000"
              autocomplete="off"
            />
          </div>


          <div class="flex flex-col gap-1">
            <label class="form-label" for="title">
              Dikeluarkan oleh <i class="text-danger">*</i>
            </label>
            <input 
              name="issuedBy" 
              value="<?= htmlspecialchars($issuedBy['nama_pegawai'] . ' (' . $issuedBy['pegawai_id'] . ')'); ?>"
              onKeyUp="onKeyChg2(this)" 
              id="target3" 
              list="employees" 
              placeholder="card id or name" 
              type="text" 
              class="p-3 border-2 border-black rounded-md" 
              placeholder=""
            />
            <datalist id="x">
            </datalist>
          </div>        

         
        </div><!-- /.row -->

        <div class="pt-5 text-end">
          <a href="javascript:window.history.back();" class="btn btn-label-secondary me-sm-3 me-1">Batal</a>
          <button type="submit" class="btn btn-primary">
            <i class="ti ti-device-floppy me-1"></i> Simpan Perubahan
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function () {
    $('#date').flatpickr({
      dateFormat: 'Y-m-d',
      defaultDate: '<?= $warning['date']; ?>'
    });

    $('#expired').flatpickr({
      dateFormat: 'Y-m-d',
      defaultDate: '<?= $warning['expired']; ?>'
    });
  });
</script>

<script type="text/javascript">
  const BASE_URL = "<?= base_url(); ?>";

  function parse(data){
    const employees = document.getElementById("employees");
    employees.innerHTML = "";
    
    data.forEach(e => {
      const option = document.createElement("option");
      option.value = `${e.nama_pegawai} (${e.pegawai_id})`;
      option.text  = e.nama_pegawai;
      employees.appendChild(option);
    });
  }

  function onKeyChg2(e){
    const employees = document.getElementById("x")
    const value = target3.value
    
    fetch(BASE_URL + "karyawan/data/all?divId=" + "Any" + "&key=" + value).then(response => response.json()).then(
      data => parse(data)
    )
  }

  function onKeyChg(e){
    const value = target2.value;
    fetch(BASE_URL + "karyawan/data/all?divId=Any&key=" + encodeURIComponent(value)).then(response => response.json()).then(
      data => parse(data)
    );
  }


</script>
