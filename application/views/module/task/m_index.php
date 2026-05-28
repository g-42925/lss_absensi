<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
      <h5 class="card-title mb-0">Task Management</h5>
      <a href="<?= base_url('task/add'); ?>" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> Tambah Task
      </a>
    </div>

    <?= $this->session->flashdata('message'); ?>

    <div class="card-datatable table-responsive p-3">
      <table class="table border-top" id="dataTable">
        <thead>
          <tr class="text-center">
            <th>#</th>
            <th>Karyawan</th>
            <th>Tugas</th>
            <th>Deskripsi</th>
            <th>Deadline</th>
            <th>Status</th>
            <th>Aksi</th>
            <th>...</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1; foreach ($data as $r): ?>
          <tr class="text-center align-middle">
            <td><?= $no++; ?></td>
            <td class="text-capitalize text-start">
              <strong><?= htmlspecialchars($r['nama_pegawai']); ?></strong><br>
              <small class="text-muted"><?= htmlspecialchars($r['id_pegawai']); ?></small>
            </td>
            <td class="text-start"><?= htmlspecialchars($r['task']); ?></td>
            <td class="text-start" style="max-width:200px;">
              <span class="text-truncate d-block" style="max-width:180px;" title="<?= htmlspecialchars($r['description']); ?>">
                <?= htmlspecialchars($r['description']); ?>
              </span>
            </td>
            <td>
              <?php
                $today    = date('Y-m-d');
                $deadline = $r['deadline'];
                $badgeClass = 'bg-label-secondary';
                if ($r['solved']) {
                    $badgeClass = 'bg-label-success';
                } 
                elseif ($deadline < $today) {
                    $badgeClass = 'bg-label-danger';
                } 
                elseif ($deadline == $today) {
                    $badgeClass = 'bg-label-warning';
                } 
                else {
                    $badgeClass = 'bg-label-info';
                }
              ?>
              <span class="badge <?= $badgeClass; ?>"><?= $deadline; ?></span>
            </td>
            <td>
              <?php if ($r['solved']): ?>
                <span class="badge bg-label-success"><i class="ti ti-circle-check me-1"></i>Selesai</span>
              <?php else: ?>
                <span class="badge bg-label-warning"><i class="ti ti-clock me-1"></i>Pending</span>
              <?php endif; ?>
            </td>
            <td>
              <small><?= date('d M Y', strtotime($r['created_at'])); ?></small>
            </td>
            <td>
              <div class="d-flex gap-1 justify-content-center">
                <a href="<?= base_url('task/edit/' . $r['office_task_id']); ?>" class="btn btn-sm btn-icon btn-outline-primary" title="Edit">
                  <i class="ti ti-pencil"></i>
                </a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($data)): ?>
          <tr>
            <td colspan="8" class="text-center text-muted py-4">Belum ada task. <a href="<?= base_url('task/add'); ?>">Tambah sekarang</a></td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<!-- / Content -->

<script type="text/javascript">
  $(document).ready(function () {
    $('#dataTable').DataTable({
      order: [[4, 'asc']],
      columnDefs: [{ orderable: false, targets: [7] }]
    });
  });
</script>
