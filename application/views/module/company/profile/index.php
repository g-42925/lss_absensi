<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 order-1 order-md-0">
      <!-- User Card -->
      <div class="card mb-4">
        <div class="card-body">
          <div class="user-avatar-section">
            <div class="d-flex align-items-center flex-column">
              <img
                class="img-fluid rounded mb-3 pt-1"
                src="<?=base_url($company['logo_perusahaan']);?>"
                height="100"
                width="100"
                alt="Logo Company" />
              <div class="user-info text-center">
                <h4 class="mb-0"><?=$company['nama_perusahaan'];?></h4>
              </div>
              <?=$this->session->flashdata('message');?>
            </div>
          </div>
          <div class="pb-4 border-bottom"></div>
          <p class="mt-4 small text-uppercase text-muted">Details</p>
          <div class="info-container">
            <ul class="list-unstyled">
              <li class="mb-2">
                <div class="fw-semibold me-1">Nama Perusahaan</div>
                <span><?=$company['nama_perusahaan'];?></span>
              </li>
              <li class="mb-2">
                <div class="fw-semibold me-1">Alamat Perusahaan</div>
                <span><?=$company['alamat_perusahaan'];?></span>
              </li>
              <li class="mb-2 pt-1">
                <div class="fw-semibold me-1">Nomor Telepon</div>
                <span><?=$company['nomor_perusahaan'];?></span>
              </li>
              <?php if ($company['email_perusahaan']!='') { ?>
              <li class="mb-2 pt-1">
                <div class="fw-semibold me-1">Email Perusahaan</div>
                <span><?=$company['email_perusahaan'];?></span>
              </li>
              <?php } ?>
              <?php if ($company['label_informasi_app']!='') { ?>
              <li class="mb-2 pt-1">
                <div class="fw-semibold me-1">Label Sambutan App</div>
                <span><?=$company['label_informasi_app'];?></span>
              </li>
              <?php } ?>
              <?php if ($company['text_informasi_app']!='') { ?>
              <li class="mb-2 pt-1">
                <div class="fw-semibold me-1">Label Informasi App</div>
                <span><?=$company['text_informasi_app'];?></span>
              </li>
              <?php } ?>
            </ul>
            <div class="d-flex justify-content-center">
              <a href="<?=base_url('company/profile/edit');?>" class="btn btn-primary">Edit Data</a>
            </div>
          </div>
      </div>
      <!-- /User Card -->
    </div>
    <!-- / Content -->
  </div>
</div>