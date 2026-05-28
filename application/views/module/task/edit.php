<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header border-bottom d-flex align-items-center justify-content-between">
      <h5 class="card-title mb-0">Edit Task</h5>
      <a href="<?= base_url('task/list'); ?>" class="btn btn-sm btn-label-secondary">
        <i class="ti ti-arrow-left me-1"></i> Kembali
      </a>
    </div>
    <div class="card">
      <form class="card-body" action="<?= base_url('task/edit_proses/' . $task['office_task_id']); ?>" method="POST" id="taskEditForm">
        <?= $this->session->flashdata('message'); ?>

        <div class="row g-3">

          <!-- Assigned To (AJAX Select2, pre-filled) -->
          <div class="flex flex-col gap-1">
            <label class="form-label" for="title">
              Tugaskan kepada <i class="text-danger">*</i>
            </label>
            <input value="<?=  $emp['nama_pegawai'].' ('.$task['assigned_to'].')' ?>" name="nik" onKeyUp="onKeyChg(this)" id="target2" list="employees" placeholder="card id or name" type="text" class="p-3 border-2 border-black rounded-md" placeholder=""/>
            <datalist id="employees">
            </datalist>
          </div>

          <!-- Title -->
          <div class="col-xl-12 col-md-12">
            <label class="form-label" for="title">
              Judul Task <i class="text-danger">*</i>
            </label>
            <input type="text" class="form-control" id="title" name="task"
                   value="<?= htmlspecialchars($task['task']); ?>"
                   placeholder="Masukkan judul task..." required autocomplete="off" />
          </div>

          <!-- Description -->
          <div class="col-xl-12 col-md-12">
            <label class="form-label" for="description">Deskripsi</label>
            <textarea class="form-control" id="description" name="description"
                      rows="4" placeholder="Detail pekerjaan yang harus diselesaikan..."><?= htmlspecialchars($task['description']); ?></textarea>
          </div>

          <!-- Deadline -->
          <div class="col-xl-6 col-md-6 col-sm-12">
            <label class="form-label" for="deadline">
              Deadline <i class="text-danger">*</i>
            </label>
            <input type="text" class="form-control" id="deadline" name="deadline"
                   value="<?= $task['deadline']; ?>"
                   placeholder="YYYY-MM-DD" required readonly />
            <small class="text-muted">Tanggal batas pengerjaan</small>
          </div>

          <!-- Solved toggle -->
          <div class="col-xl-6 col-md-6 col-sm-12">
            <label class="form-label d-block">Status Penyelesaian</label>
            <div class="form-check form-switch mt-2">
              <input type="hidden" name="is_solved" value="0" />
              <input class="form-check-input" type="checkbox" id="is_solved" name="is_solved" value="1"
                     <?= $task['solved'] ? 'checked' : ''; ?> />
              <label class="form-check-label" for="is_solved">
                Tandai sebagai <strong>Selesai</strong>
              </label>
            </div>
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
<!-- / Content -->

<script type="text/javascript">
$(document).ready(function () {
  // Flatpickr for deadline
  $('#deadline').flatpickr({
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