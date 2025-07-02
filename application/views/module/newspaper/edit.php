<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Edit Data</h5>
    </div>
    <div class="card">
      <form class="card-body" action="<?=base_url('newspaper/edit_proses/'.$edit['berita_id']);?>" method="POST" enctype="multipart/form-data">
        <?=$this->session->flashdata('message');?>
        <div class="row g-3">
          <div class="col-xl-8 col-md-8 col-sm-7 col-xs-7">
            <label class="form-label">Judul Berita<i class="text-danger">*</i></label>
            <input type="text" class="form-control" name="nama" value="<?=$edit['nama_berita'];?>" placeholder="..." required />
          </div>
          <div class="col-xl-4 col-md-4 col-sm-5 col-xs-5">
            <label class="form-label" for="multicol-country">Status<i class="text-danger">*</i></label>
            <select class="select2 form-select" name="status" required>
              <option value="y" <?php if ($edit['is_status']=='y') echo 'selected'; ?>>Aktif</option>
              <option value="n" <?php if ($edit['is_status']=='n') echo 'selected'; ?>>Tidak Aktif</option>
            </select>
          </div>
          <div class="col-xl-12 col-md-12 col-sm-12 mb-2">
            <div class="form-group editoronly">
              <label class="form-label">Deskripsi<i class="text-danger">*</i></label>
              <textarea name="berita" class="form-control"><?=$edit['isi_berita'];?></textarea>
            </div>
          </div>
          <div class="col-xl-12 col-md-12 col-sm-12 mb-2">
            <label class="form-label">Cover (optional)</label>
            <input type="file" class="form-control" name="gambar" onChange="showImgfile(this);" />
            <div class="mt-2 small">Format (jpg/png) maksimal 2mb.</div>
            <div class="mt-2">
              <?php if ($edit['gambar_berita']!='') { ?>
                <div id="targetfileimg">
                  <img src="<?=base_url($edit['gambar_berita']);?>" width="120" class="rounded">
                </div>
              <?php } else { ?>
                <div id="targetfileimg"></div>
              <?php } ?>
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