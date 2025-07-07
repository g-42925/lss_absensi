<button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="modal-body">
    <div class="text-center mb-4">
        <h3 class="role-title mb-2"><?=indo($datal['tanggal_lembur']);?></h3>
        <p class="text-muted">Set <?=$datal['masuk_lembur'];?> s/d <?=$datal['keluar_lembur'];?></p>
    </div>

    <div class="col-12">
      <div class="table-responsive">
        <table class="table border-top" id="dataTableatt">
          <thead>
            <tr>
              <th class="w-s-n">Nama Pegawai</th>
              <th>Absen Lembur</th>
              <th>Absen S.Lembur</th>
              <th>Catatan</th>
            </tr>
          </thead>
          <tbody>
            <?php 
              $no=1; foreach ($result as $row) : 
            ?>
            <tr>
              <td><?= $row['nama_pegawai'];?></td>
              <td><?= $row['absen_masuk'];?></td>
              <td><?= $row['absen_keluar'];?></td>
              <td>
                Catatan Lembur : <?= $row['catatan_lembur'];?><br/>
                S.Lembur : <?= $row['catatan_hasil_lembur'];?></td>
            </tr>
            <?php $no++; endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php if($datal['is_acc_updated']!=0){ ?>
    <div class="col-12 mt-2">
      Terakhir di update pada 
      <?php if($datal['updated_at']!=''){ ?>
      <?=indo($datal['updated_at']);?> <?=substr($datal['updated_at'],11,5);?> 
      <?php } ?>
      oleh 
      <?php if($userupd!=''){ ?>
      <?=$userupd['nama_lengkap'];?> (<?=$userupd['email_address'];?>)
      <?php } ?>
    </div>
    <?php } ?>
    <div class="col-12 text-center mt-4">
      <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">
        Tutup
      </button>
    </div>
</div>

