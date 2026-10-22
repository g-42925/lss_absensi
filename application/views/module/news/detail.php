<body>
    <!-- Layout container -->
    <div class="layout-page" style="display:block;">

      <!-- Navbar -->
      <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">

        <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
          <div class="navbar-nav align-items-center">
            <div class="nav-item navbar-search-wrapper mb-0">
              <img src="<?=base_url('assets/temp/assets/logo/client/logo.png');?>" width="70">
              <span class="d-none d-md-inline-block"><b>&nbsp;&nbsp;Mentari Islamic School</b></span>
            </div>
          </div>

          <ul class="navbar-nav flex-row align-items-center ms-auto">
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
              <a class="nav-link"
                href="javascript:void(0);"
                data-bs-toggle="modal"
                data-bs-target="#addNewLogin"
                aria-expanded="false">
                <i class="ti ti-user-circle ti-md"></i>&nbsp;&nbsp;Login
              </a>
            </li>
          </ul>
        </div>
      </nav>
      <!-- / Navbar -->

      <!-- Content wrapper -->
      <div class="content-wrapper">
            
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">

            <?=$this->session->flashdata('message');?>

            <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">
                <a href="<?=base_url('auth');?>" class="text-muted"> Kembali </a> .. /</span> <?=$datas['nama_berita'];?>
            </h4>

            <div class="row mb-5 justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-7">
                  <div class="card mb-3">
                    <img class="card-img-top" src="<?=base_url($datas['gambar_berita']);?>" alt="" />
                    <div class="card-body">
                      <h5 class="card-title"><?=$datas['nama_berita'];?></h5>
                      <div class="card-text inner-html-det">
                        <?=$datas['isi_berita'];?>
                      </div>
                      <p class="card-text">
                        <small class="text-muted ft-14"><?=indo($datas['created_at']);?></small>
                      </p>
                    </div>
                  </div>
                </div>
            </div>
        </div>
        <!-- / Content -->

      </div>
    </div>