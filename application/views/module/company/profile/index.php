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
                src="<?= $profile['logo'] ?>"
                height="100"
                width="100"
                alt="Logo Company" />
              <div class="user-info text-center">
                <h4 class="mb-0"><?=$profile['company_name'];?></h4>
              </div>
            </div>
          </div>
          <div class="pb-4 border-bottom"></div>
          <p class="mt-4 small text-uppercase text-muted">Details</p>
          <div class="info-container">
            <ul class="list-unstyled">
              <li class="mb-2">
                <div class="fw-semibold me-1">Alamat Perusahaan</div>
                <span><?=$profile['address'];?></span>
              </li>
              <li class="mb-2 pt-1">
                <div class="fw-semibold me-1">Nomor Telepon</div>
                <span><?=$profile['phone'];?></span>
              </li>
              <?php if ($company['email_perusahaan']!='') { ?>
              <li class="mb-2 pt-1">
                <div class="fw-semibold me-1">Email Perusahaan</div>
                <span><?=$profile['email'];?></span>
              </li>
              <?php } ?>
            </ul>
            <div class="d-flex justify-content-center">
              <a href="<?=base_url('company/profile/edit/0');?>" class="btn btn-primary">Edit Data</a>
            </div>
          </div>
      </div>
      <!-- /User Card -->
    </div>
    <!-- / Content -->
  </div>
</div>