  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->

        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
          <div class="app-brand demo">
            <a href="<?=base_url('dashboard');?>" class="app-brand-link">
              <img src="<?=base_url('assets/temp/assets/logo/client/leryn_lss_abseni.png');?>" width="160">
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
              <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
              <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
            </a>
          </div>

          <div class="menu-inner-shadow"></div>

          <ul class="menu-inner py-1">
            <!-- Dashboards -->
            <?php if ($title=='Dashboard') { $ac = 'active'; }else{ $ac = ''; } ?>
            <li class="menu-item <?=$ac;?>">
              <a href="<?=base_url('dashboard');?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-layout-sidebar"></i>
                <div data-i18n="Dashboard">Dashboard</div>
              </a>
            </li>

            <?php if(!isset($nmenusub)) $nmenusub = $title; ?>

            <?php $menu = $this->menu->showMenu($auth['role_id']); if ($menu!='no') { ?>

            <?php foreach ($menu as $m) :
              if ($nmenu == $m['nama_menu']) {
                if ($m['tipe']==2) { $nmenusc = 'active'; }else{ $nmenusc = 'active open'; }
              } else { 
                $nmenusc = '';
              } 
            ?>

            <?php if ($m['tipe']==2) { ?>
            <li class="menu-item <?=$nmenusc;?>">
              <a href="<?=base_url($m['link_url']);?>" class="menu-link">
                <i class="menu-icon tf-icons <?=$m['icon']; ?>"></i>
                <div data-i18n="<?=$m['nama_menu'];?>"><?=$m['nama_menu'];?></div>
              </a>
            </li>
            <?php } else if ($m['tipe']==1) { ?>
            <li class="menu-item <?=$nmenusc;?>">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons <?=$m['icon']; ?>"></i>
                <div data-i18n="<?=$m['nama_menu']; ?>"><?=$m['nama_menu']; ?></div>
              </a>
              <ul class="menu-sub">
                <?php $subMenu = $this->menu->showSubMenu($auth['role_id'],$m['menu_id']); ?>
                <?php foreach ($subMenu as $sm) : ?>
                <?php if ($sm['tipe']==1) { ?>
                <?php 
                  if ($nmenusub == $sm['nama_menu']){
                    $nmenusubea = 'active open';
                  }else{
                    $nmenusubea = '';
                  }
                ?>
                <li class="menu-item <?=$nmenusubea;?>">
                  <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <div data-i18n="<?=$sm['nama_menu'];?>"><?=$sm['nama_menu'];?></div>
                  </a>
                  <ul class="menu-sub">
                    <?php $subMenu = $this->menu->showSubMenu($auth['role_id'],$sm['menu_id']); ?>
                    <?php foreach ($subMenu as $smx) : 
                      if ($title == $smx['nama_menu']){
                        $nmenusubx = 'active';
                      } else {
                        $nmenusubx = ' ';
                      }
                    ?>
                    <li class="menu-item <?=$nmenusubx;?>">
                      <a href="<?=base_url($smx['link_url']);?>" class="menu-link">
                        <div data-i18n="<?=$smx['nama_menu'];?>"><?=$smx['nama_menu'];?></div>
                      </a>
                    </li>
                    <?php endforeach; ?>
                  </ul>
                </li>
                <?php } else { ?>
                <?php 
                  if ($nmenusub == $sm['nama_menu']){
                    $nmenusuba = 'active';
                  }else{
                    $nmenusuba = '';
                  }
                ?>
                <li class="menu-item <?=$nmenusuba;?>">
                  <a href="<?=base_url($sm['link_url']);?>" class="menu-link">
                    <div data-i18n="<?=$sm['nama_menu'];?>"><?=$sm['nama_menu'];?></div>
                  </a>
                </li>
                <?php } ?>
                <?php endforeach; ?>
              </ul>
            </li>
            <?php } ?>
            <?php endforeach; ?>
            <?php } ?>
            <!-- Dashboards -->
            <!-- <li class="menu-item active">
              <a href="<?=base_url('dashboard');?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-layout-sidebar"></i>
                <div data-i18n="Dashboard">Dashboard</div>
              </a>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-building"></i>
                <div data-i18n="Perusahaan">Perusahaan</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="#" class="menu-link">
                    <div data-i18n="Profil">Profil</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="#" class="menu-link">
                    <div data-i18n="Admin">Admin</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <div data-i18n="Jabatan & Izin">Jabatan & Izin</div>
                  </a>
                  <ul class="menu-sub">
                    <li class="menu-item">
                      <a href="<?=base_url('company/roles/');?>" class="menu-link">
                        <div data-i18n="Jabatan">Jabatan</div>
                      </a>
                    </li>
                    <li class="menu-item">
                      <a href="<?=base_url('company/permission/');?>" class="menu-link">
                        <div data-i18n="Izin">Izin</div>
                      </a>
                    </li>
                  </ul>
                </li>
              </ul>
            </li>
            <li class="menu-item">
              <a href="#" class="menu-link">
                <i class="menu-icon tf-icons ti ti-map-pin"></i>
                <div data-i18n="Lokasi Kehadiran">Lokasi Kehadiran</div>
              </a>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-users"></i>
                <div data-i18n="Karyawan">Karyawan</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="#" class="menu-link">
                    <div data-i18n="Data Karyawan">Data Karyawan</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="#" class="menu-link">
                    <div data-i18n="Grup">Grup</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="#" class="menu-link">
                    <div data-i18n="Waktu Kerja">Waktu Kerja</div>
                  </a>
                </li>
              </ul>
            </li>
            <li class="menu-item">
              <a href="#" class="menu-link">
                <i class="menu-icon tf-icons ti ti-clock"></i>
                <div data-i18n="Kehadiran Harian">Kehadiran Harian</div>
              </a>
            </li>
            <li class="menu-item">
              <a href="#" class="menu-link">
                <i class="menu-icon tf-icons ti ti-clock-off"></i>
                <div data-i18n="Menunggu Persetujuan">Menunggu Persetujuan</div>
              </a>
            </li>
            <li class="menu-item">
              <a href="#" class="menu-link">
                <i class="menu-icon tf-icons ti ti-checklist"></i>
                <div data-i18n="Rekap Kehadiran">Rekap Kehadiran</div>
              </a>
            </li> -->
          </ul>
        </aside>
        <!-- / Menu -->