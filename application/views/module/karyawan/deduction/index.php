<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom flex flex-col gap-3">
      <form method="get" action="<?= base_url().'/karyawan/deduction/index' ?>" class="flex flex-row w-full gap-3">
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
            <th>...</th>
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
            <td>    
            <a href="#" class="btn p-1" data-bs-toggle="modal" data-bs-target="#delRow<?=$row['deduction_id'];?>" title="Hapus">
              <i class="ti ti-trash"></i>
            </a>        
            <div class="modal fade" id="delRow<?=$row['deduction_id'];?>" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-simple modal-enable-otp modal-dialog-centered">
                  <div class="modal-content p-3 p-md-5">
                    <div class="modal-body">
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      <div class="text-center mb-4">
                        <h3 class="mb-2">Konfirmasi</h3>
                        <p>Yakin ingin menghapus data ini ?</p>
                      </div>
                      <div class="col-12 text-center pt-3">
                        <button
                          type="button"
                          class="btn btn-label-secondary me-sm-3 me-1"
                          data-bs-dismiss="modal"
                          aria-label="Close">
                          Batal
                        </button>
                        <a href="<?=base_url('karyawan/deduction/delete/'.$row['deduction_id']);?>" class="btn btn-danger">Ya, Hapus</a>
                      </div>
                    </div>
                  </div>
              </div>
            </div>   
            </td>  
          </tr>
          <?php $no++; endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<!-- / Content -->