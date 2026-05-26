<div class="container-xxl flex-grow-1 container-p-y m-3">
  <div class="card">
    <div class="card-header border-bottom">
        <form method="get" action="<?= base_url() . 'except/filter'?>" class="flex flex-row w-full gap-3">
          <select data-value="Any" id="target1" onChange="onDivChg(this)" name="divisionId" class="w-full p-3 rounded-md border-2 border-black appearance-none">
            <option value="Any">Any</option>
            <?php foreach ($divisions as $row): ?>
              <option value="<?= $row['id']; ?>">
                <?= $row['division_name']; ?>
              </option>
            <?php endforeach; ?>
          </select>
          <select name="status" class="w-full p-3 rounded-md border-2 border-black appearance-none">
            <option value="all">All</option>
            <option value="Absen masuk">Absen masuk</option>
            <option value="Absen pulang">Absen pulang</option>
            <option value="Lupa absen">Lupa absen</option>
            <option value="Lupa absen">Cuti pulang</option>

            <option value="Cuti setengah hari">Cuti setengah hari</option>
          </select>
          <input id="target2" onKeyUp="onKeyChg(this)" list="employees" value="" name="keyword" type="text" placeholder="card id or name" class="w-full p-3 rounded-md border-2 border-black" />
          <datalist id="employees"></datalist>
          <input value="<?= date('Y-m-01')?>" name="start" type="date" class="w-full p-3 rounded-md border-2 border-black" />
          <input value="<?= date('Y-m-t')?>" name="until" type="date" class="w-full p-3 rounded-md border-2 border-black" />
          <button class="bg-black text-white p-3 rounded-md">search</button>
        </form>
    </div>
    <div class="card-datatable table-responsive">
      <table class="table border-top" id="dataTable">
        <thead>
          <tr>
            <th class="w-s-n">Date</th>
            <th class="w-s-n">Requested By</th>
            <th class="w-s-n">Requested At</th>
            <th class="w-s-n">Reason</th>
            <th class="w-s-n">Category</th>
            <th class="w-s-n">Photo</th>
            <th class="w-s-n">No fee</th>
            <th class="w-s-n">Status</th>
            <th class="w-s-n">Handled at</th>
            <th class="w-s-n">...</th>
          </tr>
        </thead>
        <tbody>
          <?php $no=1; foreach ($data as $r) : ?>
          <tr>
            <td><?= $r['date'];?></td>
            <td><?= $r['nama_pegawai'] ?> (<?= $r['employee_id'];?>)</td>
            <td><?= $r['requested_at'];?></td>
            <td><?= $r['reason'];?></td>
            <td><?= $r['type'];?></td>
            <td class="text-capitalize">
              <a target="_blank" href="<?= base_url('image/index').'/'.basename($r['image']) ?>"><i class="ti ti-photo"></i></a>
            </td> 
            <td><?= $r['htu'] ? 'yes':'no' ?></td>

            <td>
              <?php
                echo match($r['status']) {
                  '0' => 'pending',
                  '1' => 'approved',
                  '2' => 'rejected',
                  default => 'rejected',
                };
              ?>
            </td>
            <td><?= $r['actTime'];?></td>
            <td align="right">
              <a href="<?=base_url('except/edit/'.$r['id']).'?failed=false';?>" class="btn p-1" title="Edit Pengajuan">
                <i class="ti ti-edit"></i>
              </a>
            </td>
          </tr>
          <?php $no++; endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script type="text/javascript">
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