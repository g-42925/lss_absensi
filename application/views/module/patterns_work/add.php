<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Tambah Data</h5>
    </div>
    <div class="card">
      <form class="card-body" action="<?=base_url('patterns_work/add_proses');?>" method="POST">
        <?php if ($failed): ?>
          <?=$this->session->flashdata('message');?>
        <?php endif; ?>
        <div class="row g-3">
          <div class="col-xl-4 col-md-4 col-sm-4">
            <label class="form-label">Nama Pola<i class="text-danger">*</i></label>
            <input type="text" class="form-control" name="nama" autocomplete="off" placeholder="..." required />
          </div>
          <div class="col-xl-4 col-md-4 col-sm-4">
            <label class="form-label">Toleransi Keterlambatan</label>
            <div class="input-group">
              <input type="text" class="form-control" name="tolet" placeholder="0">
              <span class="input-group-text">Menit</span>
            </div>
          </div>
          <div class="col-xl-4 col-md-4">
            <label class="form-label">Jumlah Hari<i class="text-danger">*</i></label>
            <input type="text" class="form-control" name="jumlahhari" id="jumlahhari" autocomplete="off" placeholder="7" onkeyup="checkPolaHari(this.value)" required />
            </div>
          </div>
          <div class="col-xl-12 col-md-12 mt-6">
            <div class="table-responsive">
              <table class="table">
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
  function checkPolaHari(a){
    if (a>365) {
      $('#alertxjumlah').html('<br/><span class="text-danger">Maksimal 365 Hari</span>');
      $('#jumlahhari').val(0);
      checkPolaHari(0);
    }else{
      var atext = '';
      for (var i = 1; i <= a; i++) {
        atext += `
          <tr>
            <td>Day&nbsp;`+i+`</td>
            <td>
              <select class="form-control w-28" name="work[]" required="">
                <option value="y">Hari Kerja</option>
                <option value="n">Libur</option>
              </select>
            </td>
            <td class="hidden">
              <select class="form-control w-14" name="sistemkerja[]" required="">
                <option value="1">WFO</option>
                <option value="2">WFH</option>
              </select>
            </td>
            <td width="140">
              <input type="text" class="form-control flatpickr-input text-center active" placeholder="hh:mm" value="08:00" id="flatpickr-time-work-m`+i+`" readonly="readonly" name="masuk[]">
            </td>
            <td class="120">
              <input type="text" class="outline-none w-14" value="Sampai">
            </td>
            <td width="w-fit">
              <input type="text" class="form-control flatpickr-input text-center active" placeholder="hh:mm" value="17:00" id="flatpickr-time-work-p`+i+`" readonly="readonly" name="pulang[]">
            </td>
            <td class="">
              <input type="text" class="outline-none [width:calc(1ch*7)]" value="Istirahat">
            </td>
            <td class="">
              <input id="breakStart-`+i+`" type="text" value="12:00" class="form-control flatpickr-input text-center active" name="break[]">
            </td>
            <td class="">
              <input type="text" class="outline-none w-14" value="Sampai">
            </td>
            <td class="">
              <input value="13:00" type="text" id="breakEnd-`+i+`" class="form-control flatpickr-input text-center active" name="breakEnd[]">
            </td>
          </tr>
        `;
      }
    
      $('#siklus_pola_kerja_xm').html(atext);
      for (var i = 1; i <= a; i++) {
        const twPp = document.querySelector('#flatpickr-time-work-p'+i)
        const twMm = document.querySelector('#flatpickr-time-work-m'+i)
        const start = document.querySelector('#breakStart-'+i)
        const end = document.querySelector('#breakEnd-'+i)
        twMm.flatpickr({ enableTime: true, noCalendar: true, time_24hr: true });
        twPp.flatpickr({ enableTime: true, noCalendar: true, time_24hr: true });
        start.flatpickr({ enableTime: true, noCalendar: true, time_24hr: true });
        end.flatpickr({ enableTime: true, noCalendar: true, time_24hr: true });
      }
    }
  }
</script>