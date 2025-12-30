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
          <div class="col-xl-3 col-lg-4 col-md-5 mb-4 mt-6">
            <div class="input-group">
              <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
                <select class="select2 form-select filter" name="filter" required>
                  <option value="hhk">Hadir</option>
                  <option value="alpha-2">Alpha-2</option>
                  <option value="alpha-1">Alpha-1</option>
                  <option value="s">Sakit</option>
                  <option value="i">Izin</option>
                  <option value="csh">Cuti setengah hari</option>
                  <option value="csh">Cuti</option>
                </select>
              </div>
              <a href="javascript:filterWithState();" class="input-group-text btn btn-outline-primary">Terapkan</a>
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
            <td class="w-s-n <?= $row['isLate'] ? 'text-red-900':'' ?>"><?= $row['nama_pegawai'] ?></td> <!-- nama -->
            <td class="v-a-t"> <!-- status -->
              <select class="form-control <?=$bgs;?>" name="status" id="status<?=$row['pid'];?>" required="" onchange="updateStatus('status','<?=$row['pid'];?>',this.value)" <?php if ($row['is_request']>0) { echo 'disabled'; } ?>>               
                <option value="ts" <?= $row['is_status'] == 'ts' ? 'selected':'' ?>>Belum ada status [TS]</option>
                <option value="on duty" <?= $row['is_status'] == 'on duty' ? 'selected':'' ?>>Bertugas di luar</option>
                <option value="alpha-2" <?= $row['is_status'] == 'alpha-2' ? 'selected':'' ?>>Alpha-2</option>                                
                <option value="alpha-1" <?= $row['is_status'] == 'alpha-1' ? 'selected':'' ?>>Alpha-1</option>                
                <option value="alpha-0" <?= $row['is_status'] == 'alpha-0' ? 'selected':'' ?>>Alpha-0</option>                
                <option value="free" <?= $row['is_status'] == 'off' ? 'selected':'' ?>>Off</option>                                                             
                <option value="free" <?= $row['is_status'] == 'free' ? 'selected':'' ?>>Free</option>                                              
                <option value="th" <?php if ($row['is_status']=='th') echo 'selected'; ?>>Tidak hadir [TH]</option>
                <option value="free" <?= $row['is_status'] == 'off' ? 'selected':'' ?>>Off</option>                                                             
                <option value="free" <?= $row['is_status'] == 'hhk' ? 'selected':'' ?>>Hadir</option>                                                             z                
                <option value="hbhk" <?php if ($row['is_status']=='hbhk') echo 'selected'; ?>>Hadir bukan dihari kerja [HBHK]</option>
                <option value="s" <?php if ($row['is_status']=='s') echo 'selected'; ?>>Sakit [S]</option>
                <option value="i" <?php if ($row['is_status']=='i') echo 'selected'; ?>>Izin [I]</option>
                <option value="c" <?php if ($row['is_status']=='c') echo 'selected'; ?>>Cuti [C]</option>
                <option value="cb" <?php if ($row['is_status']=='cb') echo 'selected'; ?>>Cuti bersama [CB]</option>
                <option value="ct" <?php if ($row['is_status']=='ct') echo 'selected'; ?>>Cuti tahunan [CT]</option>
                <option value="csh" <?php if ($row['is_status']=='csh') echo 'selected'; ?>>Cuti setengah hari [CSH]</option>
                <option value="l" <?php if ($row['is_status']=='l') echo 'selected'; ?>>Libur [L]</option>
                <option value="tl" <?php if ($row['is_status']=='tl') echo 'selected'; ?>>Tugas Luar [TL]</option>
                <option value="free" <?= $row['is_status'] == 'htu' ? 'selected':'' ?>>Hadir Tanpa upah</option>                                                             z                
              </select>
            </td>
            <td class="v-a-t"> <!-- jam masuk -->
              <input type="text" class="form-control flatpickr-input text-center active" placeholder="hh:mm" id="flatpickr-time-work-a<?=$row['pegawai_id'];?>" value="<?=$row['jam_masuk'];?>" readonly="readonly" onchange="checkStatus<?=$row['pegawai_id'];?>('<?=$row['is_status'];?>');updateStatus('jmasuk','<?=$row['pegawai_id'];?>',this.value)" <?php if ($row['is_request']>0) { echo 'disabled'; } ?>>
              <?php if ($row['jam_masuk']!='') { ?>
              <div class="text-center">
                <a target="_blank" href="https://www.google.com/maps?q=<?= $row['point_latitude'] ?>,<?= $row['point_longitude'] ?>" class="btn p-1" data-toggle="tooltip" title="Lihat Map">
                  <i class="ti ti-map-pin ft-13"></i>
                </a>
                <a target="_blank" href="<?= base_url('image/index').'/'.basename($row['foto_absen_masuk']) ?>" class="btn p-1" data-toggle="tooltip" title="Lihat Foto Absen">
                  <i class="ti ti-photo ft-13"></i>
                </a>
              </div>
              <?php } ?>
            </td>
            <td class="v-a-t"> <!-- istirahat -->
              <input type="text" class="form-control flatpickr-input text-center active" placeholder="hh:mm" id="flatpickr-time-work-b<?=$row['pid'];?>" value="<?=$row['jam_istirahat'];?>" readonly="readonly" onchange="updateStatus('jisti','<?=$row['pid'];?>',this.value)" <?php if ($row['is_request']>0) { echo 'disabled'; } ?>>
            </td>
            <td class="v-a-t"> <!-- s.istirahat -->
              <input type="text" class="form-control flatpickr-input text-center active" placeholder="hh:mm" id="flatpickr-time-work-c<?=$row['pid'];?>" value="<?=$row['jam_sistirahat'];?>" readonly="readonly" onchange="updateStatus('jsisti','<?=$row['pid'];?>',this.value)" <?php if ($row['is_request']>0) { echo 'disabled'; } ?>>
              <div class="text-center">
                <a target="_blank" href="https://www.google.com/maps?q=<?= $row['s_istirahat_latitude'] ?>,<?= $row['s_istirahat_longitude'] ?>" class="btn p-1" data-toggle="tooltip" title="Lihat Map">
                  <i class="ti ti-map-pin ft-13"></i>
                </a>
                <a target="_blank" href="<?= $row['s_istirahat_photo'] ?>" class="btn p-1" data-toggle="tooltip" title="Lihat Foto Absen">
                  <i class="ti ti-photo ft-13"></i>
                </a>
              </div>
            </td>
            <td class="v-a-t"> <!-- jam keluar -->
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
                <a target="_blank" href="https://www.google.com/maps?q=<?= $row['latitude_keluar'] ?>,<?= $row['longitude_keluar'] ?>" class="btn p-1" data-toggle="tooltip" title="Lihat Map">
                  <i class="ti ti-map-pin ft-13"></i>
                </a>
                <a target="_blank" href="<?= base_url('image/index').'/'.basename($row['foto_absen_keluar']) ?>" class="btn p-1" data-toggle="tooltip" title="Lihat Foto Absen">
                  <i class="ti ti-photo ft-13"></i>
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
    window.location.href='<?=base_url('attendance/date/');?>'+valx;
  }

  function filterWithState(){
    var valx = $('.filter').val();
    window.location.href='<?=base_url('attendance/filter/');?>'+valx;
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