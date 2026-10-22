<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title">Lembur</h5>
    </div>
    <div class="card-datatable table-responsive">
      <table class="table border-top" id="dataTable">
        <thead class="text-center">
          <tr>
            <th class="w-s-n">Requested By</th>
            <th class="w-s-n">Requested At</th>
            <th class="w-s-n">Start from</th>
            <th class="w-s-n">Until</th>
            <th class="w-s-n">Start photo</th>
            <th class="w-s-n">Start location</th>
            <th class="w-s-n">Finish photo</th>
            <th class="w-s-n">Finish location</th>
            <th class="w-s-n">Action</th>
          </tr>
        </thead>
        <tbody class="text-center">
          <?php $no=1; foreach ($data as $r) : ?>
          <tr>
            <td><?= $r['nama_pegawai'] ?> (<?= $r['employee_id'];?>)</td>
            <td><?= $r['date'];?></td>
            <td><?= $r['start_from'];?></td>
            <td><?= $r['until'];?></td>
            <td class="text-capitalize">
              <a href="javascript:void(0);" onclick="showPhotoPopup('<?= base_url('image/index').'/'.basename($r['start_photo']) ?>', 'Foto Mulai Lembur')"><i class="ti ti-photo"></i></a>
            </td>  
            <td>
                <?php if (!empty($r['start_location'])): ?>
                  <a href="https://www.google.com/maps?q=<?= explode("/",$r['start_location'])[0] ?>,<?= explode("/",$r['start_location'])[1] ?>" target="_blank">
                    <i class="ti ti-map-pin <?= $r['mocked_in'] == '1' ? 'text-red-900':'' ?>"></i>
                  </a>
                <?php endif; ?>
                <?php if (empty($r['start_location'])): ?>
                  <a href="" target="_blank">
                    -
                  </a>
                <?php endif; ?>
            </td>
            <td class="text-capitalize">
              <a href="javascript:void(0);" onclick="showPhotoPopup('<?= base_url('image/index').'/'.basename($r['finish_photo']) ?>', 'Foto Selesai Lembur')"><i class="ti ti-photo"></i></a>
            </td>  
            <td>
                <?php if (!empty($r['finish_location'])): ?>
                  <a href="https://www.google.com/maps?q=<?= explode("/",$r['finish_location'])[0] ?>,<?= explode("/",$r['finish_location'])[1] ?>" target="_blank">
                    <i class="ti ti-map-pin text <?= $r['mocked_in'] == '1' ? 'text-red-900':'' ?>"></i>
                  </a>
                <?php endif; ?>
                <?php if (empty($r['finish_location'])): ?>
                  <a href="" target="_blank">
                    -
                  </a>
                <?php endif; ?>
            </td>
            <td>
              <a href="<?=base_url('overwork/edit/'.$r['employee_overwork_id']).'?failed=false';?>" class="btn p-1" title="Edit Pengajuan">
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