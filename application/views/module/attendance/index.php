<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0"><?=$namalabel;?><br/>
        <small><?=indo($today);?></small>
      </h5>
    </div>
    <div class="card-body">
      <div class="pt-3">
        <div class="row">
          <div class="col-xl-3 col-lg-4 col-md-5 mb-4">
            <label for="flatpickr-date" class="form-label">Filter</label>
            <div class="input-group">
              <input type="text" class="form-control filtertglabsensi" placeholder="YYYY-MM-DD" value="<?=$today;?>" id="flatpickr-date" />
              <a href="javascript:filtertglAbsensi();" class="input-group-text btn btn-outline-primary">Terapkan</a>
            </div>
          </div>
        </div>
        <!-- <div class="btn btn-primary w-100 p-3">
          Untuk menampilkan filter tanggal.

          <?php $day = date('D', strtotime($today));
          $dayList = array('Sun' => 'Minggu', 'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu', 'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu');
          ?>
        </div> -->
      </div>
    </div>
    <div class="card-datatable table-responsive">
      <?=$this->session->flashdata('message');?>
      <table class="table border-top" id="dataTableatt">
        <thead>
          <tr>
            <th>Nama</th>
            <th width="210">Status</th>
            <th class="w-s-n">Jam Masuk</th>
            <th>Istirahat</th>
            <th>S.Istirahat</th>
            <th class="w-s-n">Jam Keluar</th>
          </tr>
        </thead>
        <tbody>
          <?php 
            $no=1; foreach ($datas as $row) : 
            if ($row['is_status']=='hhk') {
              $bgs = 'btn-label-primary';
            }else if ($row['is_status']=='hbhk') {
              $bgs = 'btn-label-success';
            }else if ($row['is_status']=='s') {
              $bgs = 'btn-label-secondary';
            }else if ($row['is_status']=='i') {
              $bgs = 'btn-label-warning';
            }else if ($row['is_status']=='c' || $row['is_status']=='cb' || $row['is_status']=='ct' || $row['is_status']=='csh') {
              $bgs = 'btn-label-dark';
            }else if ($row['is_status']=='l' || $row['is_status']=='th') {
              $bgs = 'btn-label-danger';
            }else{
              $bgs = '';
            }
          ?>
          <tr>
            <td class="w-s-n"><?=$row['nama_pegawai'];?></td>
            <td class="v-a-t">
              <select class="form-control <?=$bgs;?>" name="status" id="status<?=$row['pid'];?>" required="" onchange="updateStatus('status','<?=$row['pid'];?>',this.value)" <?php if ($row['is_request']>0) { echo 'disabled'; } ?>>
                <option value="ts" <?php if ($row['is_status']=='ts') echo 'selected'; ?>>Belum ada status [TS]</option>
                <option value="th" <?php if ($row['is_status']=='th') echo 'selected'; ?>>Tidak hadir [TH]</option>
                <option value="hhk" <?php if ($row['is_status']=='hhk') echo 'selected'; ?>>Hadir dihari kerja [HHK]</option>
                <option value="hbhk" <?php if ($row['is_status']=='hbhk') echo 'selected'; ?>>Hadir bukan dihari kerja [HBHK]</option>
                <option value="s" <?php if ($row['is_status']=='s') echo 'selected'; ?>>Sakit [S]</option>
                <option value="i" <?php if ($row['is_status']=='i') echo 'selected'; ?>>Izin [I]</option>
                <option value="c" <?php if ($row['is_status']=='c') echo 'selected'; ?>>Cuti [C]</option>
                <option value="cb" <?php if ($row['is_status']=='cb') echo 'selected'; ?>>Cuti bersama [CB]</option>
                <option value="ct" <?php if ($row['is_status']=='ct') echo 'selected'; ?>>Cuti tahunan [CT]</option>
                <option value="csh" <?php if ($row['is_status']=='csh') echo 'selected'; ?>>Cuti setengah hari [CSH]</option>
                <option value="l" <?php if ($row['is_status']=='l') echo 'selected'; ?>>Libur [L]</option>
                <option value="tl" <?php if ($row['is_status']=='tl') echo 'selected'; ?>>Tugas Luar [TL]</option>
              </select>
              <?php if ($row['is_request']>0) { ?>
                <div class="ft-12 mt-2">Status berdasarkan request yang telah disetujui, 
                  <a href="<?=base_url('attendance/req_cancel/'.$row['pid'].'/'.$today);?>">klik untuk membatalkan.</a></div>
              <?php } ?>
            </td>
            <td class="v-a-t">
              <input type="text" class="form-control flatpickr-input text-center active" placeholder="hh:mm" id="flatpickr-time-work-a<?=$row['pid'];?>" value="<?=$row['jam_masuk'];?>" readonly="readonly" onchange="checkStatus<?=$row['pid'];?>('<?=$row['is_status'];?>');updateStatus('jmasuk','<?=$row['pid'];?>',this.value)" <?php if ($row['is_request']>0) { echo 'disabled'; } ?>>
              <?php if ($row['jam_masuk']!='') { ?>
              <div class="text-center">
                <a href="javascript:;" onclick="action_data_att('in','<?=$row['absen_id'];?>','map');" class="btn p-1" data-toggle="tooltip" title="Lihat Map">
                  <i class="ti ti-map-pin ft-13"></i>
                </a>
                <a href="javascript:;" onclick="action_data_att('in','<?=$row['absen_id'];?>','photo');"class="btn p-1" data-toggle="tooltip" title="Lihat Foto Absen">
                  <i class="ti ti-photo ft-13"></i>
                </a>
                <a href="javascript:;" onclick="action_data_att('in','<?=$row['absen_id'];?>','catatan');" class="btn p-1" data-toggle="tooltip" title="Lihat Catatan">
                  <i class="ti ti-note ft-13"></i>
                </a>
              </div>
              <?php } ?>
            </td>
            <td class="v-a-t">
              <input type="text" class="form-control flatpickr-input text-center active" placeholder="hh:mm" id="flatpickr-time-work-b<?=$row['pid'];?>" value="<?=$row['jam_istirahat'];?>" readonly="readonly" onchange="updateStatus('jisti','<?=$row['pid'];?>',this.value)" <?php if ($row['is_request']>0) { echo 'disabled'; } ?>>
            </td>
            <td class="v-a-t">
              <input type="text" class="form-control flatpickr-input text-center active" placeholder="hh:mm" id="flatpickr-time-work-c<?=$row['pid'];?>" value="<?=$row['jam_sistirahat'];?>" readonly="readonly" onchange="updateStatus('jsisti','<?=$row['pid'];?>',this.value)" <?php if ($row['is_request']>0) { echo 'disabled'; } ?>>
            </td>
            <td class="v-a-t">
              <?php if ($row['acc_keluar']=='y') { ?>
              <input type="text" class="form-control flatpickr-input text-center active" placeholder="hh:mm" id="flatpickr-time-work-d<?=$row['pid'];?>" value="<?=$row['jam_keluar'];?>" readonly="readonly" onchange="updateStatus('jkeluar','<?=$row['pid'];?>',this.value)" <?php if ($row['is_request']>0) { echo 'disabled'; } ?>>
              <?php }else if ($row['acc_keluar']=='n') { ?>
              <?=$row['jam_keluar'];?>
              <div class="ft-11">Waiting,
                <a href="<?=base_url('attendance_approval/index/'.$today);?>"> lihat </a>
              </div>
              <?php }else if ($row['acc_keluar']=='t') { ?>
              <?=$row['jam_keluar'];?>
              <div class="ft-11">Ditolak diluar jangkauan, 
                <a href="<?=base_url('attendance_approval/index/'.$today);?>"> lihat </a>
              </div>
              <?php }else{ ?>
              <input type="text" class="form-control flatpickr-input text-center active" placeholder="hh:mm" id="flatpickr-time-work-d<?=$row['pid'];?>" value="<?=$row['jam_keluar'];?>" readonly="readonly" onchange="updateStatus('jkeluar','<?=$row['pid'];?>',this.value)" <?php if ($row['is_request']>0) { echo 'disabled'; } ?>>
              <?php } ?>
              <?php if ($row['acc_keluar']!='n' && $row['acc_keluar']!='t' && $row['jam_keluar']!='') { ?>
              <div class="text-center">
                <a href="javascript:;" onclick="action_data_att('out','<?=$row['absen_id'];?>','map');" class="btn p-1" data-toggle="tooltip" title="Lihat Map">
                  <i class="ti ti-map-pin ft-13"></i>
                </a>
                <a href="javascript:;" onclick="action_data_att('out','<?=$row['absen_id'];?>','photo');"class="btn p-1" data-toggle="tooltip" title="Lihat Foto Absen">
                  <i class="ti ti-photo ft-13"></i>
                </a>
                <a href="javascript:;" onclick="action_data_att('out','<?=$row['absen_id'];?>','catatan');" class="btn p-1" data-toggle="tooltip" title="Lihat Catatan">
                  <i class="ti ti-note ft-13"></i>
                </a>
              </div>
              <?php } ?>
            </td>
          </tr>
          <script type="text/javascript">
            function checkStatus<?=$row['pid'];?>(a){
              if (a=='') {
                a = $('#status<?=$row['pid'];?>').val();
              }
              if (a=='l') {
                updateStatus('status','<?=$row['pid'];?>','hbhk');
                $('#status<?=$row['pid'];?>').val('hbhk');
                $('#status<?=$row['pid'];?>').removeClass('btn-label-danger');
                $('#status<?=$row['pid'];?>').addClass('btn-label-success');
              }else{
                if (a=='ts' || a=='th') {
                  updateStatus('status','<?=$row['pid'];?>','hhk');
                  $('#status<?=$row['pid'];?>').val('hhk');
                  $('#status<?=$row['pid'];?>').addClass('btn-label-primary');
                }
              }
            }
            $(document).ready(function () {
              const twAa = document.querySelector('#flatpickr-time-work-a<?=$row['pid'];?>'),
              twBb = document.querySelector('#flatpickr-time-work-b<?=$row['pid'];?>'),
              twCc = document.querySelector('#flatpickr-time-work-c<?=$row['pid'];?>'),
              twDd = document.querySelector('#flatpickr-time-work-d<?=$row['pid'];?>');
              twAa.flatpickr({ enableTime: true, noCalendar: true, time_24hr: true });
              twBb.flatpickr({ enableTime: true, noCalendar: true, time_24hr: true });
              twCc.flatpickr({ enableTime: true, noCalendar: true, time_24hr: true });
              twDd.flatpickr({ enableTime: true, noCalendar: true, time_24hr: true });
            });
          </script>
          <?php $no++; endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<!-- / Content -->

<script type="text/javascript">
  function filtertglAbsensi(){
    var valx = $('.filtertglabsensi').val();
    window.location.href='<?=base_url('attendance/index/');?>'+valx;
  }

  function updateStatus(a,b,c){
    if (c=='ts' || c=='th' || c=='l' || c=='l' || c=='ct' || c=='cb' || c=='l' || c=='c' || c=='i' || c=='s') {
      $('#flatpickr-time-work-a'+b).val('');
      $('#flatpickr-time-work-b'+b).val('');
      $('#flatpickr-time-work-c'+b).val('');
      $('#flatpickr-time-work-d'+b).val('');
    }
    $.get('<?=base_url('attendance/absensi/'.$today.'/')?>'+a+'/'+b+'/'+c, function(data) {
    });
  }

  $(document).ready(function () {
    $('#flatpickr-date').flatpickr({
      maxDate: "<?=$maxdate;?>"
    });
  });
</script>