<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">

  <!-- Header Stats Row -->
  <?php
    $total     = count($data);
    $aktif     = count(array_filter($data, fn($r) => $r['is_aktif'] === 'y'));
    $nonaktif  = $total - $aktif;
    $kategori  = count(array_unique(array_column($data, 'kategori')));
  ?>
  <div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
      <div class="card border-0 shadow-sm">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="avatar avatar-md flex-shrink-0">
            <span class="avatar-initial rounded-circle bg-label-primary">
              <i class="ti ti-chart-bar ti-md"></i>
            </span>
          </div>
          <div>
            <small class="text-muted d-block">Total KPI</small>
            <h4 class="mb-0 fw-bold"><?= $total; ?></h4>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-sm-6">
      <div class="card border-0 shadow-sm">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="avatar avatar-md flex-shrink-0">
            <span class="avatar-initial rounded-circle bg-label-success">
              <i class="ti ti-circle-check ti-md"></i>
            </span>
          </div>
          <div>
            <small class="text-muted d-block">KPI Aktif</small>
            <h4 class="mb-0 fw-bold"><?= $aktif; ?></h4>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-sm-6">
      <div class="card border-0 shadow-sm">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="avatar avatar-md flex-shrink-0">
            <span class="avatar-initial rounded-circle bg-label-secondary">
              <i class="ti ti-circle-off ti-md"></i>
            </span>
          </div>
          <div>
            <small class="text-muted d-block">KPI Nonaktif</small>
            <h4 class="mb-0 fw-bold"><?= $nonaktif; ?></h4>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-sm-6">
      <div class="card border-0 shadow-sm">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="avatar avatar-md flex-shrink-0">
            <span class="avatar-initial rounded-circle bg-label-warning">
              <i class="ti ti-category ti-md"></i>
            </span>
          </div>
          <div>
            <small class="text-muted d-block">Kategori</small>
            <h4 class="mb-0 fw-bold"><?= $kategori; ?></h4>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Table Card -->
  <div class="card">
    <div class="card-header border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
      <div>
        <h5 class="card-title mb-0">Daftar KPI Master</h5>
        <small class="text-muted">Kelola jenis-jenis KPI yang dapat dinilai terhadap karyawan</small>
      </div>
      <a href="<?= base_url('kpi_master/add'); ?>" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> Tambah KPI
      </a>
    </div>

    <?= $this->session->flashdata('message'); ?>

    <div class="card-datatable table-responsive p-3">
      <table class="table border-top" id="dataTable">
        <thead>
          <tr class="text-center">
            <th>#</th>
            <th class="text-start">Nama KPI</th>
            <th>Kategori</th>
            <th>Satuan</th>
            <th>Bobot (%)</th>
            <th>Nilai Min</th>
            <th>Nilai Max</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1; foreach ($data as $r): ?>
          <tr class="text-center align-middle">
            <td><?= $no++; ?></td>
            <td class="text-start">
              <strong><?= htmlspecialchars($r['nama_kpi']); ?></strong>
              <?php if (!empty($r['deskripsi'])): ?>
              <br><small class="text-muted" style="max-width:200px; display:inline-block; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?= htmlspecialchars($r['deskripsi']); ?>">
                <?= htmlspecialchars($r['deskripsi']); ?>
              </small>
              <?php endif; ?>
            </td>
            <td>
              <?php
                $kategoriColors = [
                  'Produktivitas'  => 'bg-label-primary',
                  'Kualitas'       => 'bg-label-info',
                  'Kehadiran'      => 'bg-label-success',
                  'Perilaku'       => 'bg-label-warning',
                  'Inovasi'        => 'bg-label-danger',
                  'Keuangan'       => 'bg-label-dark',
                ];
                $kat = htmlspecialchars($r['kategori']);
                $katClass = $kategoriColors[$kat] ?? 'bg-label-secondary';
              ?>
              <span class="badge <?= $katClass; ?>"><?= $kat; ?></span>
            </td>
            <td><span class="badge bg-label-secondary"><?= htmlspecialchars($r['satuan']); ?></span></td>
            <td>
              <strong><?= number_format($r['bobot'], 1); ?>%</strong>
            </td>
            <td><?= number_format($r['nilai_min'], 0); ?></td>
            <td><?= number_format($r['nilai_max'], 0); ?></td>
            <td>
              <div class="form-check form-switch d-flex justify-content-center">
                <input
                  class="form-check-input toggle-aktif"
                  type="checkbox"
                  role="switch"
                  data-id="<?= $r['id']; ?>"
                  <?= $r['is_aktif'] === 'y' ? 'checked' : ''; ?>
                  style="cursor:pointer;"
                >
              </div>
            </td>
            <td>
              <div class="d-flex gap-1 justify-content-center">
                <a href="<?= base_url('kpi_master/edit/' . $r['id']); ?>"
                   class="btn btn-sm btn-icon btn-outline-primary" title="Edit">
                  <i class="ti ti-pencil"></i>
                </a>
                <a href="<?= base_url('kpi_master/delete/' . $r['id']); ?>"
                   class="btn btn-sm btn-icon btn-outline-danger" title="Hapus"
                   onclick="return confirm('Yakin ingin menghapus KPI \'<?= addslashes($r['nama_kpi']); ?>\'?');">
                  <i class="ti ti-trash"></i>
                </a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($data)): ?>
          <tr>
            <td colspan="9" class="text-center text-muted py-5">
              <i class="ti ti-chart-bar ti-xl d-block mb-2 opacity-50"></i>
              Belum ada KPI yang ditambahkan.
              <a href="<?= base_url('kpi_master/add'); ?>">Tambah sekarang</a>
            </td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<!-- / Content -->

<script type="text/javascript">
  const BASE_URL = "<?= base_url(); ?>";

  $(document).ready(function () {
    $('#dataTable').DataTable({
      order: [[1, 'asc']],
      columnDefs: [{ orderable: false, targets: [7, 8] }]
    });

    // Toggle status aktif via AJAX
    $(document).on('change', '.toggle-aktif', function () {
      const id  = $(this).data('id');
      const chk = $(this);
      $.get(BASE_URL + 'kpi_master/toggle_aktif/' + id, function (res) {
        if (res.status !== 'ok') {
          chk.prop('checked', !chk.prop('checked'));
          alert('Gagal mengubah status.');
        }
      }, 'json').fail(function () {
        chk.prop('checked', !chk.prop('checked'));
        alert('Terjadi kesalahan jaringan.');
      });
    });
  });
</script>
