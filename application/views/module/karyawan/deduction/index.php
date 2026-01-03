<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom flex flex-col gap-3">
      <form method="get" action="<?= base_url().'/karyawan/deduction/all' ?>" class="flex flex-row w-full gap-3">
        <select name="employeeId" class="w-full p-3 rounded-md border-2 border-black appearance-none">
          <option>select an employee</option>
            <?php foreach ($employees as $row): ?>
                <option <?= $empId == $row['pegawai_id'] ? 'selected':'' ?> value="<?= $row['pegawai_id']; ?>">
                    <?= $row['nama_pegawai']; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input value="<?= $from ?>" name="from" type="date" class="w-full p-3 rounded-md border-2 border-black" placeholder="from"/>
        <input value="<?= $to ?>" name="to" type="date" class="w-full p-3 rounded-md border-2 border-black"/>
        <button class="bg-black text-white p-3 rounded-md">search</button>
      </form>
    </div>
    <div class="card-datatable table-responsive">
      <table class="table border-top" id="dataTable">
        <thead>
          <tr class="text-center">
            <th class="w-s-n">Date</th>
            <th class="w-s-n">Employee Id</th>
            <th class="w-s-n">Id Pegawai</th>
            <th class="w-s-n">Nama Pegawai</th>
            <th class="w-s-n">Tipe Potongan</th>
            <th>Jumlah</th>
          </tr>
        </thead>
        <tbody>
          <?php $no=1; foreach ($deductions as $row) : ?>
          <tr class="text-center">
            <td><?= $row['date'];?></td>
            <td><?= $row['employee_id'];?></td>
            <td><?= $row['pegawai_id'];?></td>
            <td><?= $row['nama_pegawai'];?></td>
            <td><?= $row['deduction_type'];?></td>
            <td><?= $row['amount'];?></td>
          </tr>
          <?php $no++; endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<!-- / Content -->