<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Edit Data</h5>
    </div>
    <div class="card">
      <form class="card-body" action="<?=base_url('patterns_work/edit_proses/'.$edit['pola_kerja_id']);?>" method="POST">
        <?=$this->session->flashdata('message');?>
        <div class="row g-3">
          <div class="col-xl-6 col-md-6 col-sm-6">
            <label class="form-label">Nama Pola<i class="text-danger">*</i></label>
            <input type="text" class="form-control" name="nama" value="<?=$edit['nama_pola'];?>" autocomplete="off" placeholder="..." required />
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6">
            <label class="form-label">Toleransi Keterlambatan</label>
            <div class="input-group">
              <input type="text" class="form-control" name="tolet" value="<?=$edit['toleransi_terlambat'];?>" placeholder="0">
              <span class="input-group-text">Menit</span>
            </div>
          </div>
          <div class="col-xl-12 col-md-12">
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr class="v-align-middle">
                    <td class="text-center ft-14" colspan="2">
                      Jumlah Hari ( Dalam Siklus )<span id="alertxjumlah"></span><i class="text-danger">*</i>
                    </td>
                    <th class="text-center" colspan="4">
                      <input type="text" class="form-control" name="jumlahhari" id="jumlahhari" value="<?=$edit['jumlah_hari_siklus'];?>" autocomplete="off" placeholder="7" required  onkeyup="checkPolaHari(this.value)" />
                    </th>
                  </tr>
                </thead>
                <tbody id="siklus_pola_kerja_xm"></tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="pt-5 text-end">
          <a href="javascript:window.history.back();" class="btn btn-label-secondary me-sm-3 me-1">Batal</a>
          <button type="submit" class="btn btn-primary">Simpan Data</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- / Content -->
<script type="text/javascript">

  var jumlah_siklus = '<?=$edit['jumlah_hari_siklus'];?>';

  $(document).ready(function () {
    checkPolaHari(jumlah_siklus);
  });
  function checkPolaHari(a){
    if (a>365) {
      $('#alertxjumlah').html('<br/><span class="text-danger">Maksimal 365 Hari</span>');
      $('#jumlahhari').val(jumlah_siklus);
      checkPolaHari(jumlah_siklus);
    }else{
      var atext = '';
      <?php $no=1; foreach ($edit_pola as $row) : ?>
      if ('<?=$no;?>'<=parseInt(a)) {
        atext += `
          <tr id="xtrpolaset<?=$no;?>">
            <td>Day&nbsp;<?=$no;?></td>
            <td>
              <select class="form-control" name="work[]" required="">
                <option value="y" <?php if ($row['is_work']=='y') echo 'selected'; ?>>Hari Kerja</option>
                <option value="n" <?php if ($row['is_work']=='n') echo 'selected'; ?>>Libur</option>
              </select>
            </td>
            <td>
              <select class="form-control" name="sistemkerja[]" required="">
                <option value="1" <?php if ($row['is_polkat']=='1') echo 'selected'; ?>>WFO</option>
                <option value="2" <?php if ($row['is_polkat']=='2') echo 'selected'; ?>>WFH</option>
              </select>
            </td>
            <td width="140">
              <input type="text" class="form-control flatpickr-input text-center active" placeholder="hh:mm" id="flatpickr-time-work-m<?=$no;?>" value="<?=$row['jam_masuk'];?>" readonly="readonly" name="masuk[]">
            </td>
            <td width="120" align="center">Sampai</td>
            <td width="140">
              <input type="text" class="form-control flatpickr-input text-center active" placeholder="hh:mm" id="flatpickr-time-work-p<?=$no;?>" value="<?=$row['jam_pulang'];?>" readonly="readonly" name="pulang[]">
            </td>
          </tr>
        `;
      }
      <?php $no++; endforeach; ?>
      for (var i = 1+parseInt(jumlah_siklus); i <= a; i++) {
        atext += `
          <tr>
            <td>Day&nbsp;`+i+`</td>
            <td>
              <select class="form-control" name="work[]" required="">
                <option value="y">Hari Kerja</option>
                <option value="n">Libur</option>
              </select>
            </td>
            <td>
              <select class="form-control" name="sistemkerja[]" required="">
                <option value="1">WFO</option>
                <option value="2">WFH</option>
              </select>
            </td>
            <td width="140">
              <input type="text" class="form-control flatpickr-input text-center active" placeholder="hh:mm" value="08:00" id="flatpickr-time-work-m`+i+`" readonly="readonly" name="masuk[]">
            </td>
            <td width="120" align="center">Sampai</td>
            <td width="140">
              <input type="text" class="form-control flatpickr-input text-center active" placeholder="hh:mm" value="17:00" id="flatpickr-time-work-p`+i+`" readonly="readonly" name="pulang[]">
            </td>
          </tr>
        `;
      }
      $('#siklus_pola_kerja_xm').html(atext);
      for (var i = 1; i <= a; i++) {
        const twMm = document.querySelector('#flatpickr-time-work-m'+i),
        twPp = document.querySelector('#flatpickr-time-work-p'+i);
        twMm.flatpickr({ enableTime: true, noCalendar: true, time_24hr: true });
        twPp.flatpickr({ enableTime: true, noCalendar: true, time_24hr: true });
      }
    }
  }
</script>