        <!-- Layout container -->
        <div class="layout-page">

          <?php
            $count_pending = count_pending();
            $count_lembur = count_lembur();
            $count_izin = count_izin();
          ?>

          <!-- Navbar -->
          <nav
            class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
            id="layout-navbar">
            <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
              <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                <i class="ti ti-menu-2 ti-sm"></i>
              </a>
            </div>

            <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
              <!-- Search -->
              <div class="navbar-nav align-items-center">
                <div class="nav-item navbar-search-wrapper mb-0">
                  <span class="d-none d-md-inline-block">Selamat Datang, <?=$auth['nama_lengkap'];?></span>
                </div>
              </div>
              <!-- /Search -->

              <ul class="navbar-nav flex-row align-items-center ms-auto">
                <!-- Language -->
                <li class="nav-item dropdown-language dropdown me-2 me-xl-0">
                  <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <i class="fi fi-id fis rounded-circle me-1 fs-3"></i>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a class="dropdown-item" href="javascript:void(0);" data-language="id">
                        <i class="fi fi-id fis rounded-circle me-1 fs-3"></i>
                        <span class="align-middle">Indonesia</span>
                      </a>
                    </li>
                    <li>
                      <a class="dropdown-item" href="javascript:void(0);" data-language="en">
                        <i class="fi fi-us fis rounded-circle me-1 fs-3"></i>
                        <span class="align-middle">English</span>
                      </a>
                    </li>
                  </ul>
                </li>
                <!--/ Language -->

                <!-- Waiting Absen -->
                <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
                  <ul class="dropdown-menu dropdown-menu-end py-0">
                    <li class="dropdown-menu-header border-bottom">
                      <div class="dropdown-header d-flex align-items-center py-3">
                        <h5 class="text-body mb-0 me-auto">Menunggu Persetujuan</h5>
                        <a
                          href="javascript:void(0)"
                          class="dropdown-notifications-all text-body"
                          data-bs-toggle="tooltip"
                          data-bs-placement="top"
                          title="Mark all as read"
                          ><i class="ti ti-hourglass-high fs-4"></i
                        ></a>
                      </div>
                    </li>
                    <li class="dropdown-notifications-list scrollable-container">
                      <ul class="list-group list-group-flush">
                        <?php $no=1; foreach ($count_pending['result'] as $row) : ?>
                        <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                          <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                              <div class="avatar">
                                <span class="avatar-initial rounded-circle bg-label-warning"
                                  ><i class="ti ti-map-pin"></i
                                ></span>
                              </div>
                            </div>
                            <a href="<?=base_url('attendance_approval/index/'.$row['tanggal_absen']);?>">
                              <div class="flex-grow-1">
                                <h6 class="mb-1"><?=$row['nama_pegawai'];?></h6>
                                <p class="mb-0 ft-12 text-muted">Menunggu persetujuan, diluar jangkauan.</p>
                                <small class="text-muted"><?=$row['tanggal_absen'];?></small>
                              </div>
                            </a>
                          </div>
                        </li>
                        <?php $no++; endforeach; ?>
                      </ul>
                    </li>
                    <li class="dropdown-menu-footer border-top">
                      <a
                        href="<?=base_url('attendance_approval/');?>"
                        class="dropdown-item d-flex justify-content-center text-primary p-2 h-px-40 mb-1 align-items-center">
                        View all data
                      </a>
                    </li>
                  </ul>
                </li>
                <!--/ Waiting Absen -->

                <!--/ Request Izin -->
                <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
                  <a
                    class="nav-link dropdown-toggle hide-arrow"
                    href="javascript:void(0);"
                    data-bs-toggle="dropdown"
                    data-bs-auto-close="outside"
                    aria-expanded="false">
                    <i class="ti ti-calendar-plus ti-md"></i>
                    <span class="badge bg-danger rounded-pill badge-notifications"><?=$count_izin['num'];?></span>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end py-0">
                    <li class="dropdown-menu-header border-bottom">
                      <div class="dropdown-header d-flex align-items-center py-3">
                        <h5 class="text-body mb-0 me-auto">Request Data Izin</h5>
                        <a
                          href="javascript:void(0)"
                          class="dropdown-notifications-all text-body"
                          data-bs-toggle="tooltip"
                          data-bs-placement="top"
                          title="Mark all as read"
                          ><i class="ti ti-calendar-plus fs-4"></i
                        ></a>
                      </div>
                    </li>
                    <li class="dropdown-notifications-list scrollable-container">
                      <ul class="list-group list-group-flush">
                        <?php $no=1; foreach ($count_izin['result'] as $row) : ?>
                        <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                          <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                              <div class="avatar">
                                <span class="avatar-initial rounded-circle bg-label-success"
                                  ><i class="ti ti-user"></i
                                ></span>
                              </div>
                            </div>
                            <a href="<?=base_url('req_permission/edit/'.$row['request_izin_id']);?>">
                              <div class="flex-grow-1">
                                <h6 class="mb-1"><?=$row['nama_pegawai'];?></h6>
                                <p class="mb-0 ft-12 text-muted">Menunggu persetujuan.</p>
                                <small class="text-muted"><?=$row['tanggal_request'];?> s/d <?=$row['tanggal_request_end'];?></small>
                              </div>
                            </a>
                          </div>
                        </li>
                        <?php $no++; endforeach; ?>
                      </ul>
                    </li>
                    <li class="dropdown-menu-footer border-top">
                      <a
                        href="<?=base_url('req_permission/');?>"
                        class="dropdown-item d-flex justify-content-center text-primary p-2 h-px-40 mb-1 align-items-center">
                        View all data
                      </a>
                    </li>
                  </ul>
                </li>
                <!--/ Request Izin -->

                <!--/ Data Lembur -->
                <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1 d-none">
                  <a
                    class="nav-link dropdown-toggle hide-arrow"
                    href="javascript:void(0);"
                    data-bs-toggle="dropdown"
                    data-bs-auto-close="outside"
                    aria-expanded="false">
                    <i class="ti ti-clock-2 ti-md"></i>
                    <span class="badge bg-danger rounded-pill badge-notifications"><?=$count_lembur['num'];?></span>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end py-0">
                    <li class="dropdown-menu-header border-bottom">
                      <div class="dropdown-header d-flex align-items-center py-3">
                        <h5 class="text-body mb-0 me-auto">Request Data Lembur</h5>
                        <a
                          href="javascript:void(0)"
                          class="dropdown-notifications-all text-body"
                          data-bs-toggle="tooltip"
                          data-bs-placement="top"
                          title="Mark all as read"
                          ><i class="ti ti-clock-2 fs-4"></i
                        ></a>
                      </div>
                    </li>
                    <li class="dropdown-notifications-list scrollable-container">
                      <ul class="list-group list-group-flush">
                        <?php $no=1; foreach ($count_lembur['result'] as $row) : ?>
                        <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                          <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                              <div class="avatar">
                                <span class="avatar-initial rounded-circle bg-label-success"
                                  ><i class="ti ti-user"></i
                                ></span>
                              </div>
                            </div>
                            <a href="<?=base_url('overtime/edit/'.$row['lembur_id']);?>">
                              <div class="flex-grow-1">
                                <p class="mb-0 ft-12 text-muted">Menunggu persetujuan lembur.</p>
                                <small class="text-muted"><?=$row['tanggal_lembur'];?></small>
                              </div>
                            </a>
                          </div>
                        </li>
                        <?php $no++; endforeach; ?>
                      </ul>
                    </li>
                    <li class="dropdown-menu-footer border-top">
                      <a
                        href="<?=base_url('overtime/');?>"
                        class="dropdown-item d-flex justify-content-center text-primary p-2 h-px-40 mb-1 align-items-center">
                        View all data
                      </a>
                    </li>
                  </ul>
                </li>
                <!--/ Data Lembur -->

                <!-- Style Switcher -->
                <li class="nav-item me-2 me-xl-0">
                  <a class="nav-link style-switcher-toggle hide-arrow" href="javascript:void(0);">
                    <i class="ti ti-md"></i>
                  </a>
                </li>
                <!--/ Style Switcher -->

                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                  <a
                    class="nav-link dropdown-toggle hide-arrow"
                    href="javascript:void(0);"
                    data-bs-toggle="dropdown"
                    data-bs-auto-close="outside"
                    aria-expanded="false">
                    <i class="ti ti-user-circle ti-md"></i>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <div class="dropdown-item">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar avatar-online">
                              <img src="<?=base_url('assets/temp/');?>assets/img/avatars/1.png" alt class="h-auto rounded-circle" />
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <span class="fw-semibold d-block"><?=$auth['nama_lengkap'];?></span>
                            <small class="text-muted"><?=$auth['nama_role'];?></small>
                          </div>
                        </div>
                      </div>
                    </li>
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <!-- <li>
                      <a class="dropdown-item" href="#">
                        <i class="ti ti-user-check me-2 ti-sm"></i>
                        <span class="align-middle">Informasi Akun</span>
                      </a>
                    </li>
                    <li>
                      <a class="dropdown-item" href="#">
                        <i class="ti ti-settings me-2 ti-sm"></i>
                        <span class="align-middle">Pengaturan</span>
                      </a>
                    </li>
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <a class="dropdown-item" href="#">
                        <i class="ti ti-lifebuoy me-2 ti-sm"></i>
                        <span class="align-middle">Bantuan</span>
                      </a>
                    </li>
                    <li>
                      <a class="dropdown-item" href="#">
                        <i class="ti ti-help me-2 ti-sm"></i>
                        <span class="align-middle">FAQ</span>
                      </a>
                    </li>
                    <li>
                      <div class="dropdown-divider"></div>
                    </li> -->
                    <li>
                      <a class="dropdown-item" href="<?=base_url('auth/logout');?>">
                        <i class="ti ti-logout me-2 ti-sm"></i>
                        <span class="align-middle">Keluar</span>
                      </a>
                    </li>
                  </ul>
                </li>
                <!--/ User -->
              </ul>
            </div>

            <!-- Search Small Screens -->
            <div class="navbar-search-wrapper search-input-wrapper d-none">
              <input
                type="text"
                class="form-control search-input container-xxl border-0"
                placeholder="Search..."
                aria-label="Search..." />
              <i class="ti ti-x ti-sm search-toggler cursor-pointer"></i>
            </div>
          </nav>
          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            
            <!-- Content -->
