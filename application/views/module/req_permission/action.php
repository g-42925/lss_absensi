<button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="modal-body p-1">
  <div class="text-center mb-4">
    <h4 class="role-title mb-2 text-capitalize"><?=$datap['nama_pegawai'];?></h4>
    <?php
        $totgl = '';
        if ($datap['tanggal_request_end']!='') {
            $totgl = ' s/d '.indo($datap['tanggal_request_end']);
        }
    ?>
    <?=indo($datap['tanggal_request']).$totgl;?>
    <?php
      if ($datar['tipe_request']=='s') {
        $bgs = 'Sakit';
      }else if ($datar['tipe_request']=='i') {
        $bgs = 'Izin';
      }else if ($datar['tipe_request']=='c') {
        $bgs = 'Cuti';
      }else if ($datar['tipe_request']=='cb') {
        $bgs = 'Cuti Bersama';
      }else if ($datar['tipe_request']=='ct') {
        $bgs = 'Cuti Tahunan';
      }else if ($datar['tipe_request']=='csh') {
        $bgs = 'Cuti Setengah Hari';
      }else if ($datar['tipe_request']=='tl') {
        $bgs = 'Tugas Luar';
      }else if ($datar['tipe_request']=='lm') {
        $bgs = 'Lembur';
      }
    ?>
    <div class="mt-2">
        <span class="btn btn-label-dark btn-sm ft-11 ms-1 me-1">
            <?=$bgs;?>
        </span>
        <a href="<?=base_url('req_permission/download_perid/'.$datap['request_izin_id'].'/'.$datap['pegawai_id']);?>" class="btn btn-label-primary btn-sm ft-11 ms-1 me-1">
            Download Excel
        </a>
    </div>
  </div>
  <?php if($datar['tipe_request']=='tl' || $datar['tipe_request']=='csh') { ?>
  <div class="">
    <div class="col-12 mb-1 text-center">
    <div class="card-datatable table-responsive">
    <table class="table border-top">
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Set.Masuk</th>
            <th>Set.Keluar</th>
            <th>Absen.Masuk</th>
            <th>Absen.Keluar</th>
          </tr>
        </thead>
        <tbody>
          <?php $nos=1; foreach ($datatl as $row2) : ?>
          <tr>
            <td><?= $row2['tanggal_absen'];?></td>
            <td><?= $datar['r_jam_masuk'];?></td>
            <td><?= $datar['r_jam_keluar'];?></td>
            <td>
            <?php if($row2['is_pending']=='t'){ ?>
                Ditolak diluar jangkauan.
              <?php }else{ ?>
                <?= $row2['jam_masuk'];?>
              <?php } ?>
            </td>
            <td>
            <?php if($row2['is_pending']=='t'){ ?>
                Ditolak diluar jangkauan.
              <?php }else{ ?>
                <?php if($row2['acc_keluar']=='t'){ ?>
                  Ditolak diluar jangkauan.
                <?php }else{ ?>
                  <?= $row2['jam_keluar'];?>
                <?php } ?>
              <?php } ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      </div>
    </div>
  </div>
  <?php } ?>
</div>