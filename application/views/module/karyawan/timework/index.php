<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0"><?=$namalabel;?></h5>
    </div>
    <div class="card-datatable table-responsive">
      <?=$this->session->flashdata('message');?>
      <table class="table border-top" id="dataTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama&nbsp;Lengkap</th>
            <th>Pola&nbsp;Kerja ( Jml Hari )</th>
            <th>Mulai Berlaku</th>
            <th class="text-end">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php $no=1; foreach ($datas as $row) : ?>
          <tr>
            <td><?= $row['id_pegawai'];?></td>
            <td><?= $row['nama_pegawai'];?></td>
            <td><?= $row['nama_pola'];?> ( <?=number_format($row['jumlah_hari_siklus']+0);?> Hari )</td>
            <td>
              <?=indo($row['mulai_berlaku_tanggal']);?>
            </td>
            <td align="right">
              <?php if ($row['pola_kerja_id']) { ?>
              <a href="<?=base_url('karyawan/timework/record/'.$row['pegawai_id']);?>" class="btn btn-primary p-1 ft-12">
                <i class="ti ti-eye"></i>&nbsp;&nbsp;Lihat Pola Kerja&nbsp;
              </a>
            <?php }else{ ?>
              <a href="<?=base_url('karyawan/timework/add/'.$row['pegawai_id']);?>" class="btn btn-outline-primary p-1 ft-12">
                <i class="ti ti-plus"></i>&nbsp;&nbsp;Set Pola Kerja&nbsp;
              </a>
            <?php } ?>
            </td>
          </tr>
          <?php $no++; endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<!-- / Content -->