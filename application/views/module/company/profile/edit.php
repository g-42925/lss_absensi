<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Edit Data</h5>
    </div>
    <div class="card">
      <form class="card-body" action="<?=base_url('company/profile/edit_proses');?>" method="POST" enctype="multipart/form-data">
        <?=$this->session->flashdata('message');?>
        <div class="row g-3">
          <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12">
            <label class="form-label">Nama Perusahaan<i class="text-danger">*</i></label>
            <input type="text" class="form-control" name="nama" value="<?=$edit['nama_perusahaan'];?>" placeholder="..." required />
          </div>
          <div class="col-xl-12 col-lg-12 col-md-12">
            <label class="form-label">Alamat Perusahaan<i class="text-danger">*</i></label>
            <textarea type="text" class="form-control" name="alamat" required=""><?=$edit['alamat_perusahaan'];?></textarea>
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label">Nomor Telepon<i class="text-danger">*</i></label>
            <input type="text" class="form-control" name="nomor" value="<?=$edit['nomor_perusahaan'];?>" placeholder="..." required />
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label">Email Perusahaan<i class="text-danger">*</i></label>
            <input type="text" class="form-control" name="email" value="<?=$edit['email_perusahaan'];?>" placeholder="..." required />
          </div>
          <div class="col-xl-12 col-md-12 col-sm-12 mb-2">
            <label class="form-label">Logo Perusahaan</label>
            <input type="file" class="form-control" name="gambar" onChange="showImgfile(this);" />
            <div class="mt-2 small">Rekomendasi 1:1 ratio, format (jpg/png) maksimal 2mb.</div>
            <div class="mt-2">
              <?php if ($edit['logo_perusahaan']!='') { ?>
                <div id="targetfileimg">
                  <img src="<?=base_url($edit['logo_perusahaan']);?>" width="120" class="rounded">
                </div>
              <?php } else { ?>
                <div id="targetfileimg"></div>
              <?php } ?>
            </div>
          </div>
          <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12">
            <label class="form-label">Label Sambutan (max 85 karakter)</label>
            <input type="text" class="form-control" name="label_sambutan" value="<?=$edit['label_informasi_app'];?>" placeholder="..." />
          </div>
          <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12">
            <label class="form-label">Label Informasi</label>
            <input type="text" class="form-control" name="label_info" value="<?=$edit['text_informasi_app'];?>" placeholder="..." />
          </div>
        </div>
        <hr class="my-4 mx-n4" />
        <div class="pt-5 text-end">
          <a href="javascript:window.history.back();" class="btn btn-label-secondary me-sm-3 me-1">Batal</a>
          <button type="submit" class="btn btn-primary">Simpan Data</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- / Content -->