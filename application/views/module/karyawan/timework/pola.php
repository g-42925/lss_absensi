<table class="table border-top">
  <thead>
    <tr>
      <th>Hari</th>
      <th>Status</th>
      <th>Jam Masuk</th>
      <th>Jam Keluar</th>
      <th>Sistem</th>
    </tr>
  </thead>
  <tbody>
    <?php $no=0; foreach ($datas as $row) : ?>
    <tr>
      <td>Hari&nbsp;<?= $row['is_day'];?> <span id="timework_day<?=$no;?>"></span></td>
      <td>
        <?php if ($row['is_work']=='y') { ?>
          <span class="badge bg-label-primary">Hari Kerja</span>
        <?php }else{ ?>
          <span class="badge bg-label-danger">Hari Libur</span>
        <?php } ?>
      </td>
      <td><?=substr($row['jam_masuk'],0,5);?></td>
      <td><?=substr($row['jam_pulang'],0,5);?></td>
      <td><?php if ($row['is_polkat']==1) echo 'WFO'; else echo 'WFH'; ?></td>
    </tr>
    <?php $no++; endforeach; ?>
  </tbody>
</table>

<script type="text/javascript">
  function checkharipola(){
    var a = $('#flatpickr-date').val();
    var b = $('#harikeid').val();

    var totalday = '<?=$no;?>';
    totalday = parseInt(totalday);

    if (b>31) {
      alert('Hari ( dari pola kerja ) Maksimal 31 hari.');
      return false;
    }else{
      if (b>totalday) {
        alert('Maksimal '+totalday+' hari.');
        return false;
      }
    }


    var bb = parseInt(b)+parseInt(1);
    if (b==1 || b==8 || b==15 || b==22 || b==29) {
      var days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    }else if (b==2 || b==9 || b==16 || b==23 || b==30) {
      var days = ['Sabtu', 'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
    }else if (b==3 || b==10 || b==17 || b==24 || b==31) {
      var days = ['Jumat', 'Sabtu', 'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis'];
    }else if (b==4 || b==11 || b==18 || b==25) {
      var days = ['Kamis', 'Jumat', 'Sabtu', 'Minggu', 'Senin', 'Selasa', 'Rabu'];
    }else if (b==5 || b==12 || b==19 || b==26) {
      var days = ['Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu', 'Senin', 'Selasa'];
    }else if (b==6 || b==13 || b==20 || b==27) {
      var days = ['Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu', 'Senin'];
    }else if (b==7 || b==14 || b==21 || b==28) {
      var days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
    }else{
      var days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    }
    <?php $no=0; foreach ($datas as $row) : ?>
    var plus = '<?=$no;?>';
    var d = new Date(a);
    d.setDate(d.getDate() + Number(plus));
    var dayName = days[d.getDay()];
    $('#timework_day<?=$no;?>').html('('+dayName+') <input type="hidden" class="form-control" name="days[]" value="'+dayName+'" required="" />');
    <?php $no++; endforeach; ?>
  }
</script>