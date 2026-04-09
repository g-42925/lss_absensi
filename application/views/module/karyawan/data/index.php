<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom flex flex-row gap-3">
      <form method="get" action="<?= base_url().'karyawan/data/filter' ?>" class="flex flex-row w-full gap-3">
        <select data-value="<?= $div == '' ? 'Any':$div  ?>" id="target1" onChange="onDivChg(this)" name="divisionId" class="w-full p-3 rounded-md border-2 border-black appearance-none">
          <option value="all">Any</option>
          <?php foreach ($divisions as $row): ?>
            <option <?= $div == $row['id'] ? 'selected':'' ?> value="<?= $row['id']; ?>">
              <?= $row['division_name']; ?>
            </option>
          <?php endforeach; ?>
        </select>
        <div class="flex flex-col gap-1">
          <input value="<?= $nik ?>" name="nik" onKeyUp="onKeyChg(this)" id="target2" list="employees" placeholder="card id or name" type="text" class="p-3 border-2 border-black rounded-md" placeholder=""/>
          <datalist id="employees">
          </datalist>
        </div>
        <button class="bg-black text-white p-3 rounded-md">search</button>
      </form>
      <a class="bg-black text-white p-3 rounded-md" href="<?=base_url('karyawan/data/add/0');?>">New</a>
    </div>    
    <div class="card-datatable table-responsive">
      <table class="table border-top" id="dataTable">
        <thead>
          <tr class="text-center">
            <th class="w-s-n">Nik</th>
            <th class="w-s-n">Divisi</th>
            <th class="w-s-n">Nama Lengkap</th>
            <th class="w-s-n">No WhatsApp</th>
            <th class="w-s-n">Alamat</th>
            <th class="w-s-n">Tangggal Kontrak</th>
            <th>Gaji</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php $no=1; foreach ($datas as $row) : ?>
          <tr class="text-center">
            <td><?= $row['nik'];?></td>
            <td><?= $row['divisi'];?></td>
            <td><?= $row['nama_pegawai'];?></td>
            <td><?= $row['nomor_pegawai'];?></td>
            <td><?= $row['address'] == "" ? "-":$row['address']; ?></td>
            <td><?= $row['status_pegawai'] == 'contract' ? date('d M Y',strtotime($row['contract_start_date']))."-".date('d M Y',strtotime($row['contract_end_date'])):"-" ?></td>
            <td><?= number_format($row['salary'],2); ?></td>
            <td>
              <a href="<?=base_url('attendance_record/detail/'.$row['pegawai_id']);?>" class="btn p-1" title="Rekap Kehadiran">
                <i class="ti ti-checklist"></i>
              </a>
              <a href="<?= base_url('karyawan/data/edit/'.$row['pegawai_id']).'?failed=false' ;?>" class="btn p-1" title="edit">
                <i class="ti ti-edit"></i>
              </a>
              <a href="#" class="btn p-1" data-bs-toggle="modal" data-bs-target="#delRow<?=$row['pegawai_id'];?>" title="Hapus">
                <i class="ti ti-trash"></i>
              </a>
              <a href="<?= base_url('allowance/config/').$row['pegawai_id'] ?>" class="btn p-1" titke="bonus / tunjangan">
                <i class="ti ti-currency-dollar"></i>
              </a>
              <a href="<?= base_url('benefit/config/').$row['pegawai_id'] ?>" class="btn p-1" title="potongan">
                <i class="ti ti-notebook"></i>
              </a>
              <a href="<?= base_url('file/config/').$row['pegawai_id'] ?>" class="btn p-1" title="file">
                <i class="ti ti-file"></i>
              </a>
              <a href="<?= base_url('payroll/filter/').$row['pegawai_id'] ?>" class="btn p-1" title="payroll">
                <i class="ti ti-calendar"></i>
              </a>
              <!-- Konfirmasi Hapus -->
              <div class="modal fade" id="delRow<?=$row['pegawai_id'];?>" tabindex="-1" aria-hidden="true">
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
                        <a href="<?=base_url('karyawan/data/hapus/'.$row['pegawai_id']);?>" class="btn btn-danger">Ya, Hapus</a>
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

<script>
  const BASE_URL = "<?= base_url(); ?>";

  function parse(data){
    const employees = document.getElementById("employees")
    employees.innerHTML = ""
    data.forEach(e => {
      const option = document.createElement("option")
      option.value = e.nama_pegawai
      option.text = e.nama_pegawai
      employees.appendChild(option)
    })
  }

  function onKeyChg(e){
    const employees = document.getElementById("e")
    const target1 = document.getElementById("target1")
    const target2 = document.getElementById("target2")
    const division = target1.getAttribute("data-value")
    const value = target2.value
    
    fetch(BASE_URL + "karyawan/data/filterByDiv?divId=" + division + "&key=" + value)
      .then(response => response.json())
      .then(data => parse(data))
  }

  function onDivChg(e){
    const target = document.getElementById("target1")
    target.setAttribute("data-value",e.value)
  }
</script>