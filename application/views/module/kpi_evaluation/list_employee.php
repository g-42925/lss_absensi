<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header border-bottom flex flex-row gap-3">
      <form method="get" action="<?= base_url('kpi_evaluation') ?>" class="flex flex-row w-full gap-3 m-0">
        <select data-value="<?= $div == '' ? 'all':$div  ?>" name="divisionId" class="w-full p-2 rounded-md border-2 border-black appearance-none">
          <option value="all">Semua Divisi</option>
          <?php foreach ($divisions as $row): ?>
            <option <?= $div == $row['id'] ? 'selected':'' ?> value="<?= $row['id']; ?>">
              <?= $row['division_name']; ?>
            </option>
          <?php endforeach; ?>
        </select>
        <div class="flex flex-col gap-1">
          <input value="<?= $nik ?>" name="nik" placeholder="NIK atau Nama" type="text" class="p-2 border-2 border-black rounded-md" />
        </div>
        <button type="submit" class="bg-black text-white p-2 rounded-md">Filter</button>
      </form>
    </div>    
    <div class="card-datatable table-responsive">
      <table class="table border-top" id="dataTable">
        <thead>
          <tr class="text-center">
            <th class="w-s-n">Nik</th>
            <th class="w-s-n">Divisi</th>
            <th class="w-s-n ">Nama Lengkap</th>
            <th class="w-s-n">No WhatsApp</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php $no=1; foreach ($datas as $row) : ?>
          <tr class="text-center">
            <td><?= $row['nik'];?></td>
            <td><?= $row['divisi'];?></td>
            <td>
              <span class="<?= $row['signed_in'] ? '':'' ?>"><?= $row['nama_pegawai'];?></span>
            </td>
            <td><?= $row['nomor_pegawai'];?></td>
            <td>
              <a href="<?= base_url('kpi_evaluation/index/').$row['pegawai_id'] ?>" class="btn p-1 text-warning" title="Evaluasi KPI">
                <i class="ti ti-star"></i>
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<!-- / Content -->
