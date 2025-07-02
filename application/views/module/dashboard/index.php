<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row">
    <!-- Website Analytics -->
    <div class="col-xl-8 col-lg-7 col-md-7 mb-4">
      <div class="row">
        <div class="col-xl-4 col-md-6 col-sm-6 col-xs-6 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="badge p-2 bg-label-primary mb-2 rounded">
                <i class="ti ti-users ti-md"></i>
              </div>
              <h5 class="card-title mb-1 pt-2">Karyawan</h5>
              <p class="mb-2 mt-1 ft-16"><?=$t_pegawai+0;?></p>
              <div class="pt-0">
                <a href="<?=base_url('karyawan/data');?>" class="badge bg-label-secondary float-right">Lihat</a>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-4 col-md-6 col-sm-6 col-xs-6 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="badge p-2 bg-label-primary mb-2 rounded">
                <i class="ti ti-calendar ti-md"></i>
              </div>
              <h5 class="card-title mb-1 pt-2">Request Izin</h5>
              <p class="mb-2 mt-1 ft-16"><?= $t_izin ?></p>
              <div class="pt-0">
                <a href="<?=base_url('req_permission');?>" class="badge bg-label-secondary float-right">Lihat</a>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-4 col-md-6 col-sm-6 col-xs-6 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="badge p-2 bg-label-primary mb-2 rounded">
                <i class="ti ti-map-pin ti-md"></i>
              </div>
              <h5 class="card-title mb-1 pt-2">Absen Pending</h5>
              <p class="mb-2 mt-1 ft-16"><?=$t_pending['num']+0;?></p>
              <div class="pt-0">
                <a href="<?=base_url('attendance_approval');?>" class="badge bg-label-secondary float-right">Lihat</a>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-12 col-md-6 col-sm-6 col-xs-6 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="badge p-2 bg-label-primary mb-2 rounded">
                <i class="ti ti-compass ti-md"></i>
              </div>
              <h5 class="card-title mb-1 pt-2">Data Terkini Terbaru</h5>
              <p class="mb-2 mt-1 ft-16"><?=$t_terkini+0;?></p>
              <div class="pt-0">
                <a href="<?=base_url('terkini');?>" class="badge bg-label-secondary float-right">Lihat</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--/ Website Analytics -->

    <!-- Sales Overview -->
    <div class="col-xl-4 col-lg-5 col-md-5 mb-4">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header pb-3">
              <div class="d-flex justify-content-between">
                <h6 class="d-block mb-1">Kehadiran - <?=indo(date('Y-m-d'));?></h6>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-6 mb-2">
                  <div class="d-flex gap-2 align-items-center mb-2">
                    <p class="mb-0">Masuk&nbsp;Kerja</p>
                  </div>
                  <h5 class="mb-0 pt-1 text-nowrap"><?=$a_masuk+0;?></h5>
                </div>
                <div class="col-6 mb-2 text-end">
                  <div class="d-flex gap-2 justify-content-end align-items-center mb-2">
                    <p class="mb-0">Sakit</p>
                  </div>
                  <h5 class="mb-0 pt-1 text-nowrap ms-lg-n3 ms-xl-0"><?=$a_sakit+0;?></h5>
                </div>
                <div class="col-6 mb-2">
                  <div class="d-flex gap-2 align-items-center mb-2">
                    <p class="mb-0">Izin & Cuti</p>
                  </div>
                  <h5 class="mb-0 pt-1 text-nowrap"><?=$a_izin+0;?></h5>
                </div>
                <div class="col-6 mb-2 text-end">
                  <div class="d-flex gap-2 justify-content-end align-items-center mb-2">
                    <p class="mb-0">Tugas Luar</p>
                  </div>
                  <h5 class="mb-0 pt-1 text-nowrap ms-lg-n3 ms-xl-0"><?=$a_tl+0;?></h5>
                </div>
                <div class="col-12 mb-2">
                  <div class="d-flex gap-2 align-items-center mb-2">
                    <p class="mb-0">Belum Ada Status / Tidak Hadir</p>
                  </div>
                  <h5 class="mb-0 pt-1 text-nowrap"><?=$a_ts+0;?></h5>
                </div>
                <div class="col-12 mb-2">
                  <div class="d-flex gap-2 align-items-center mb-2">
                    <p class="mb-0">Libur</p>
                  </div>
                  <h5 class="mb-0 pt-1 text-nowrap"><?=$a_l+0;?></h5>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--/ Sales Overview -->
  </div>
</div>
<!-- / Content -->