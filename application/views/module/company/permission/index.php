<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-datatable table-responsive">
      <table class="table border-top" id="dataTable">
        <thead>
          <tr>
            <th>Label</th>
            <th>Tambah</th>
            <th>Edit</th>
            <th>Hapus</th>
          </tr>
        </thead>
        <tbody>
          <?php $no=1; foreach ($datas as $row) : ?>
          <tr>
            <td><?= $row['nama_permission'];?></td>
            <td>
              <?php if ($row['tambah']=='y') { ?>
              <span class="badge bg-label-success"><i class="ti ti-check small"></i></span>
              <?php } else { ?>
              <span class="badge bg-label-danger"><i class="ti ti-x small"></i></span>
              <?php } ?>
            </td>
            <td>
              <?php if ($row['edit']=='y') { ?>
              <span class="badge bg-label-success"><i class="ti ti-check small"></i></span>
              <?php } else { ?>
              <span class="badge bg-label-danger"><i class="ti ti-x small"></i></span>
              <?php } ?>
            </td>
            <td>
              <?php if ($row['hapus']=='y') { ?>
              <span class="badge bg-label-success"><i class="ti ti-check small"></i></span>
              <?php } else { ?>
              <span class="badge bg-label-danger"><i class="ti ti-x small"></i></span>
              <?php } ?>
            </td>
          </tr>
          <?php $no++; endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<!-- / Content -->

<!-- Modal -->
<div class="modal fade" id="optiondataModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
    <div class="modal-content p-3 p-md-5" id="content_option_modal"></div>
  </div>
</div>

<script type="text/javascript">
  function action_data(a){
    $('#optiondataModal').modal('toggle');
    $('#content_option_modal').html('Loading...');
    $.get('<?=base_url('company/permission/action/');?>'+a, function(data) {
      $('#content_option_modal').html(data);
    });
  }
</script>