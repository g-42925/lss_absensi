<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title"><?=$namalabel;?></h5>
    </div>
  </div>
  <form method="post" action="<?php echo site_url('overwork/edit_proccess/').$employeeOverworkId ?>" class="row mt-3 g-3">
    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
      <label class="form-label">Overwork id<i class="text-danger">*</i></label>
      <input type="text" value="<?= $data['employee_overwork_id'] ?>" class="form-control" name="employee_overwork_id" readonly />
    </div>
    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
      <label class="form-label">Employee_id<i class="text-danger">*</i></label>
      <input type="text" value="<?= $data['employee_id'] ?>" class="form-control" name="id" placeholder="employee_id" readonly />
    </div>
    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
      <label class="form-label">Mulai Dari<i class="text-danger">*</i></label>
      <input type="text" value="<?= $data['start_from'] ?>" class="form-control" name="start_from" readonly />
    </div>
    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
      <label class="form-label">Sanpai<i class="text-danger">*</i></label>
      <input type="text" value="<?= $data['until'] ?>" class="form-control" name="until" readonly />
    </div>
    <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
      <label class="form-label" for="multicol-country">Approved<i class="text-danger">*</i></label>
      <select class="select2 form-select" name="approved" required>
        <option <?= $data['approved'] == 1 ? 'selected':'' ?> value="1">Yes</option>
        <option <?= $data['approved'] == 0 ? 'selected':'' ?> value="2">No</option>
      </select>
    </div>
    <div class="col-xl-3 col-lg-4 col-md-5 col-sm-6 col-xs-5 input-group">
      <button type="submit" class="input-group-text btn btn-outline-primary">Edit</button>
    </div>
  </form>
</div>

<script type="text/javascript">
  $(document).ready(function () {
    $('#flatpickr-date').flatpickr({});
    $('#flatpickr-date2').flatpickr({});
  });
</script>
