<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title"><?=$namalabel;?></h5>
    </div>
  </div>
  <div class="card">
  <form method="post" action="<?php echo site_url('assignment/edit_proccess/').$id ?>" class="card-body row mt-3 g-3">
    <select class="select2 select2s form-select" name="employeeId" required>
      <?php foreach ($employees as $row) : ?>
        <option <?= $row['pegawai_id'] == $data['employee_id'] ? 'selected':'' ?> value="<?=$row['pegawai_id'];?>" data-foo="<?=$row['id_pegawai'];?>"><?=$row['nama_pegawai'];?></option>
      <?php endforeach; ?>
    </select>
    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
      <label class="form-label">Tanggal<i class="text-danger">*</i></label>
      <input value="<?= $data['start_from'] ?>" type="text" class="form-control" name="startFrom" placeholder="YYYY-MM-DD" id="flatpickr-date" required />
    </div>
    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
      <label class="form-label">Sampai Tanggal<i class="text-danger">*</i></label>
      <input value="<?= $data['until'] ?>" type="text" class="form-control" name="until" placeholder="YYYY-MM-DD" id="flatpickr-date2" required />
    </div>
    <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
      <label class="form-label">Deskripsi penugasan<i class="text-danger">*</i></label>
      <input value="<?= $data['description'] ?>" type="text" class="form-control" name="description" placeholder="..." required />
    </div>
    <div class="col-xl-3 col-lg-4 col-md-5 col-sm-6 col-xs-5 input-group">
      <button type="submit" class="input-group-text btn btn-outline-primary">Update</button>
    </div>
  </form>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function () {
    $('#flatpickr-date').flatpickr({});
    $('#flatpickr-date2').flatpickr({});
  });

</script>
