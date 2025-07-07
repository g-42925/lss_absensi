<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Edit Data</h5>
    </div>
    <div class="card">
      <form class="card-body" action="<?=base_url('req_permission/edit_proses/'.$edit['request_izin_id']);?>" method="POST" enctype="multipart/form-data">
        <?=$this->session->flashdata('message');?>
        <div class="row g-3">
          <div class="col-xl-12 col-md-12">
            <label class="form-label" for="multicol-country">Karyawan<i class="text-danger">*</i></label>
            <div class="pb-2" style="position: absolute; top:15px; right:30px;">
              <button type="button" class="btn btn-outline-primary btn-sm ft-11" onclick="selectAll()">Select All</button>
              <button type="button" class="btn btn-outline-warning btn-sm ft-11" onclick="deselectAll()">
                <i class="ti ti-trash ft-12"></i>&nbsp;&nbsp;All</button>
            </div>
            <select class="select2 select2s form-select" id="my-select-kary" name="idp[]" required multiple>
              <?php foreach ($karyawan as $row) : ?>
              <option value="<?=$row['pegawai_id'];?>" data-foo="<?=$row['id_pegawai'];?>" <?php if ($row['pegawai_id']==$row['pid']) echo 'selected'; ?>><?=$row['nama_pegawai'];?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label" for="multicol-country">Kategori<i class="text-danger">*</i></label>
            <select class="select2 form-select" name="kat" required="" onchange="selectKategori(this.value)">
              <option value="">-- Pilih Kategori --</option>
              <option value="s" <?php if ($edit['tipe_request']=='s') echo 'selected'; ?>>Sakit [S]</option>
              <option value="i" <?php if ($edit['tipe_request']=='i') echo 'selected'; ?>>Izin [I]</option>
              <option value="c" <?php if ($edit['tipe_request']=='c') echo 'selected'; ?>>Cuti [C]</option>
              <option value="cb" <?php if ($edit['tipe_request']=='cb') echo 'selected'; ?>>Cuti Bersama [CB]</option>
              <option value="ct" <?php if ($edit['tipe_request']=='ct') echo 'selected'; ?>>Cuti Tahunan [CT]</option>
              <option value="csh" <?php if ($edit['tipe_request']=='csh') echo 'selected'; ?>>Cuti Setengah Hari [CSH]</option>
              <option value="tl" <?php if ($edit['tipe_request']=='tl') echo 'selected'; ?>>Tugas Luar [TL]</option>
            </select>
          </div>
          <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
            <label class="form-label">Tanggal<i class="text-danger">*</i></label>
            <input type="text" class="form-control" name="tgl1" placeholder="YYYY-MM-DD" value="<?=$edit['tanggal_request'];?>" id="flatpickr-date" required />
          </div>
          <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 d-none" id="sttgl2">
            <label class="form-label">Sampai Tanggal<i class="text-danger">*</i></label>
            <input type="text" class="form-control" name="tgl2" placeholder="YYYY-MM-DD" value="<?=$edit['tanggal_request_end'];?>" id="flatpickr-date2" />
          </div>
          <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 d-none" id="jmsuk">
            <label class="form-label">Jam Masuk<i class="text-danger">*</i></label>
            <input type="text" class="form-control flatpickr-input active" name="jmasuk" value="<?=$edit['r_jam_masuk'];?>" placeholder="hh:mm" id="flatpickr-time" readonly="readonly">
          </div>
          <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 d-none" id="jkuar">
            <label class="form-label">Jam Keluar<i class="text-danger">*</i></label>
            <input type="text" class="form-control flatpickr-input active" name="jkeluar" value="<?=$edit['r_jam_keluar'];?>" placeholder="hh:mm" id="flatpickr-time2" readonly="readonly">
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6">
            <label class="form-label" for="multicol-country">Status<i class="text-danger">*</i></label>
            <select class="select2 form-select" name="status" required="">
              <option value="0" <?php if ($edit['is_status']==0) echo 'selected'; ?>>Pending</option>
              <option value="1" <?php if ($edit['is_status']==1) echo 'selected'; ?>>Approved</option>
              <option value="2" <?php if ($edit['is_status']==2) echo 'selected'; ?>>Reject</option>
            </select>
          </div>
          <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <label class="form-label">Catatan</label>
            <textarea type="text" class="form-control" name="catatanl" placeholder="..."><?=$edit['catatan_awal'];?></textarea>
          </div>
          <div class="col-xl-12 col-lg-12">
            <div class="">Optional</div>
          </div>
          <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
            <label class="form-label">File Dokumen (jpg/png/pdf)</label>
            <input type="file" class="form-control" name="imgpdf">
            <?php if ($edit['file_dokumen']!='') { ?>
            <div class="mt-2 small">
              <a href="<?=base_url($edit['file_dokumen']);?>" target="_blank">Lihat File...</a>
            </div>
            <?php } ?>
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
  $(document).ready(function () {

    selectKategori('<?=$edit['tipe_request'];?>');

    $("#my-select-kary").select2();

    $('#flatpickr-date').flatpickr({
      minDate: "today",
      maxDate: '<?=$thismonth;?>'
    });

    $('#flatpickr-date2').flatpickr({
      minDate: "today",
      maxDate: '<?=$thismonth;?>'
    });

    $(function(){
      $(".select2s").select2({
          matcher: matchCustom,
          templateResult: formatCustom
      });
    });

  });

  function selectKategori(a){
    if (a=='csh') {
      $('#sttgl2').addClass('d-none');
      $('#flatpickr-date2').val('');
    }else{
      $('#sttgl2').removeClass('d-none');
    }

    if (a=='lm' || a=='csh' || a=='tl') {
      $('#jmsuk').removeClass('d-none');
      $('#jkuar').removeClass('d-none');
    }else{
      $('#jmsuk').addClass('d-none');
      $('#jkuar').addClass('d-none');
      $('#flatpickr-time').val('');
      $('#flatpickr-time2').val('');
    }
  }

  function selectAll() {
      $("#my-select-kary > option").prop("selected", true);
      $("#my-select-kary").trigger("change");
  }

  function deselectAll() {
      $("#my-select-kary > option").prop("selected", false);
      $("#my-select-kary").trigger("change");
  }
</script>

