<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <div class="row mt-3">
        <form method="get" action="<?= base_url().'req_permission/filter' ?>" class="flex flex-row w-full gap-3">
          <select data-value="<?= $div ?>" id="target1" onChange="onDivChg(this)" name="divisionId" class="w-full p-3 rounded-md border-2 border-black appearance-none">
            <option value="Any">Any</option>            
            <?php foreach ($divisions as $row): ?>
              <option <?= $row['id'] == $div ? 'selected':'' ?> value="<?= $row['id']; ?>">
                <?= $row['division_name']; ?>
              </option>
            <?php endforeach; ?>
          </select>
          <select name="status" class="w-full p-3 rounded-md border-2 border-black appearance-none">
            <option <?= $status == 'all' ? 'selected':'' ?> value="all">All</option>
            <option <?= $status == 's' ? 'selected':'' ?> value="s">Sakit</option>
            <option <?= $status == 'i' ? 'selected':'' ?> value="i">Izin</option>
            <option <?= $status == 'c' ? 'selected':'' ?> value="c">Cuti</option>
            <option <?= $status == 'csh' ? 'selected':'' ?> value="csh">Cuti Setengah Hari</option>
          </select>
          <input id="target2" onKeyUp="onKeyChg(this)" list="employees" value="<?= $keyword ?>" name="keyword" type="text" placeholder="card id or name" class="w-full p-3 rounded-md border-2 border-black"/>
          <datalist id="employees"></datalist>
          <input value="<?= $tglawal ?>" name="start" type="date" class="w-full p-3 rounded-md border-2 border-black"/>
          <input value="<?= $tglakhir ?>" name="until" type="date" class="w-full p-3 rounded-md border-2 border-black"/>
          <button class="bg-black text-white p-3 rounded-md">search</button>
        </form>
      </div>
    </div>
    <div class="card-datatable table-responsive">
      <table class="table border-top" id="dataTableatt text-center">
        <thead>
          <tr>
            <th class="w-s-n">Date</th>
            <th class="w-s-n">Requested By</th>
            <th class="w-s-n">Requested At</th>
            <th>Category</th>
            <th>Photo</th>
            <th>Status</th>
            <th>Remain</th>
            <th class="text-end w-s-n">...</th>
          </tr>
        </thead>
        <tbody>
          <?php 
            $no=1; foreach ($datas as $row) : 
          ?>
          <tr>
            <td class="w-s-n">
              <?php if($status == "csh") { ?>
                <?= $row['date'] ?>
              <?php } else { ?>
              <?= $row['tanggal_request']?> -
              <?= $row['tanggal_request_end']?>
              (<?= ((new DateTime($row['tanggal_request']))->diff(new DateTime($row['tanggal_request_end']))->days + 1) ?> hari)
              <?php } ?>
            </td>
            <td class="w-s-n">
              <?= $row['created_at']?>
            </td>
            <td class="w-s-n">
              <?= $row['nama_pegawai'] ?> (<?= $row['pegawai_id']; ?>)
            </td>
            <td class="w-s-n">
              <?= $row['tipe_request'] ?>
            </td>
            <td class="text-capitalize">
              <a target="_blank" href="<?= $row['image'] == "-" ? "" : base_url('image/index').'/'.basename($row['image']) ?>"><i class="ti ti-photo"></i></a>
            </td>  
            <td class="w-s-n">
              <?= $row['is_status'] ?>
            </td>
            <td class="w-s-n">
            <td><?= $row['remain'] == '0' ? '0':$row['remain'] ?></td>
            </td>
            <?php if ($status == "csh"): ?>
              <td align="right">
                <a href="<?=base_url('except/edit/'.$row['id']).'?failed=false';?>" class="btn p-1" title="Edit Pengajuan">
                  <i class="ti ti-edit"></i>
                </a>
              </td>
            <?php else: ?>
              <td align="right">
                <a href="<?=base_url('req_permission/edit/'.$row['request_izin_id']);?>" class="btn p-1">
                  <i class="ti ti-edit"></i>
                </a>
                <a href="#" class="btn p-1" data-bs-toggle="modal" data-bs-target="#delRow<?=$row['request_izin_id'];?>">
                  <i class="ti ti-trash"></i>
                </a>
                <a href="#" class="<?= $row['tipe_request'] == "s" ? "":"hidden"  ?> btn p-1" data-bs-toggle="modal" data-bs-target="#cutRow<?=$row['request_izin_id'];?>" title="payroll">
                  <i class="ti ti-scissors"></i>
                </a>
                <a href="<?=base_url('req_permission/print/'.$row['request_izin_id']);?>" class="btn p-1">
                  <i class="ti ti-file"></i>
                </a>

                <div class="modal fade" id="cutRow<?=$row['request_izin_id'];?>" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-simple modal-enable-otp modal-dialog-centered">
                    <div class="modal-content p-3 p-md-5">
                      <div class="modal-body">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="text-center mb-4">
                          <h3 class="mb-2">Konfirmasi</h3>
                          <p>Yakin ingin memotong jumlah cuti karyawan ini ?</p>
                        </div>
                        <div class="col-12 text-center pt-3">
                          <button
                            type="button"
                            class="btn btn-label-secondary me-sm-3 me-1"
                            data-bs-dismiss="modal"
                            aria-label="Close">
                            Batal
                          </button>
                          <a href="<?=base_url('req_permission/cut/'.$row['pegawai_id']).'/'.$row['tanggal_request'].'/'.$row['tanggal_request_end'] ?>" class="btn btn-danger">Ya, Potong!</a>
                        </div>
                    </div>
                  </div>
                </div>
              </td>
              <td>
                <div class="modal fade" id="delRow<?=$row['request_izin_id'];?>" tabindex="-1" aria-hidden="true">
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
                          <a href="<?=base_url('req_permission/hapus/'.$row['request_izin_id']);?>" class="btn btn-danger">Ya, Hapus!</a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </td>
            <?php endif; ?>               
          </tr>
          <?php $no++; endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<!-- / Content -->

<!-- Modal -->
<div class="modal fade" id="optiondataModalPermitReq" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
    <div class="modal-content p-2 p-md-3" id="content_option_modal_permitrqe"></div>
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
  function filtertglRkp() {
    var valx = $('.filtertglrkp').val();
    var valx2 = $('.filtertglrkp2').val();
    window.location.href = '<?= base_url('req_permission / index / '); ?>' + valx + '/' + valx2;
  }

  function filtertglRkp(){
    var valx = $('.filtertglrkp').val();
    var valx2 = $('.filtertglrkp2').val();
    window.location.href='<?=base_url('req_permission/index/');?>'+valx+'/'+valx2;
  }

  /*

  $(document).ready(function () {
    $('#flatpickr-date2').flatpickr({
      maxDate: "<?=$today;?>"
    });
    $('#flatpickr-date').flatpickr({
      maxDate: "<?=$today;?>"
    });
  });

  */

  function action_permit_req(a,b){
    $('#optiondataModalPermitReq').modal('toggle');
    $('#content_option_modal_permitrqe').html('Loading...');
    $.get('<?=base_url('req_permission/action/');?>'+a+'/'+b, function(data) {
      $('#content_option_modal_permitrqe').html(data);
    });
  }
</script>