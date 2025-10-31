<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Tambah Shift</h5>
    </div>
    <div class="card">
      <form class="card-body" action="<?=base_url('patterns_work/shift_detail_add_proses');?>" method="POST">
        <?php if ($failed): ?>
          <?=$this->session->flashdata('message');?>
        <?php endif; ?>
        <div class="row g-3">
          <div class="col-xl-6 col-md-6 col-sm-6">
            <label class="form-label">Shift Id<i class="text-danger">*</i></label>
            <input readonly type="text" class="form-control" name="shiftId" value="<?= $shiftId ?>" required />
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6">
            <label class="form-label">Jadwal Id<i class="text-danger">*</i></label>
            <input id="jadwalId" readonly type="text" class="form-control" name="jadwalId" autocomplete="off" placeholder="..." required />
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6">
            <label class="form-label">Nama Jadwal Shift<i class="text-danger">*</i></label>
            <input type="text" class="form-control" name="name" autocomplete="off" required />
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6">
            <label class="form-label">Clock In<i class="text-danger">*</i></label>
            <input id="clockin" type="text" class="form-control" name="clockin" required />
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6">
            <label class="form-label">Clock out<i class="text-danger">*</i></label>
            <input id="clockout" type="text" class="form-control" name="clockout" required />
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6">
            <label class="form-label">Break<i class="text-danger">*</i></label>
            <input id="break" type="text" class="form-control" name="break" required />
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6">
            <label class="form-label">After break<i class="text-danger">*</i></label>
            <input id="afterbreak" type="text" class="form-control" name="afterbreak" required />
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6">
            <label class="form-label">Tardiness Tolerance<i class="text-danger">*</i></label>
            <input id="tt" type="text" class="form-control" name="tt" required />
          </div>
        </div>
        <div class="pt-5 text-end">
          <a href="javascript:window.history.back();" class="btn btn-label-secondary me-sm-3 me-1">Batal</a>
          <button type="submit" class="btn btn-primary">Simpan Data</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- / Content -->

<script type="text/javascript">
  $(document).ready(function () {
    document.getElementById('jadwalId').value = Date.now()

    flatpickr("#clockin", {
      enableTime: true,
      noCalendar: true,
      dateFormat: "H:i",
      time_24hr: true
    });
    flatpickr("#clockout", {
      enableTime: true,
      noCalendar: true,
      dateFormat: "H:i",
      time_24hr: true
    });
    flatpickr("#break", {
      enableTime: true,
      noCalendar: true,
      dateFormat: "H:i",
      time_24hr: true
    });
    flatpickr("#afterbreak", {
      enableTime: true,
      noCalendar: true,
      dateFormat: "H:i",
      time_24hr: true
    });
  });
</script>