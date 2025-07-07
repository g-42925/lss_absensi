<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Edit Data</h5>
    </div>
    <div class="card">
      <form class="card-body" action="<?=base_url('company/roles/edit_proses/'.$edit['role_id']);?>" method="POST">
        <?=$this->session->flashdata('message');?>
        <div class="row g-3">
          <div class="col-xl-8 col-md-8 col-sm-7 col-xs-7">
            <label class="form-label">Nama Jabatan</label>
            <input type="text" class="form-control" name="nama" value="<?=$edit['nama_role'];?>" placeholder="..." required />
          </div>
          <div class="col-xl-4 col-md-4 col-sm-5 col-xs-5">
            <label class="form-label" for="multicol-country">Status</label>
            <select class="select2 form-select" name="status" required>
              <option value="y" <?php if ($edit['is_status']=='y') echo 'selected'; ?>>Aktif</option>
              <option value="n" <?php if ($edit['is_status']=='n') echo 'selected'; ?>>Tidak Aktif</option>
            </select>
          </div>
        </div>
        <hr class="my-4 mx-n4" />
        <h6>Akses Menu</h6>
        <div class="row g-3">
          <div class="col-xl-12 col-lg-12">
            <div class="row">
              <?php foreach ($menu as $m) : ?>
              <div class="col-xl-3 col-lg-3 col-md-3 col-sm-4 col-xs-6 mb-3">
                <?php if ($m['tipe']==1) { ?>
                  <div class="mb-2"><?=$m['nama_menu'];?></div>
                  <?php $subMenu = $this->menu->getSubMenu($edit['role_id'],$m['menu_id']); ?>
                  <?php foreach ($subMenu as $sm) : ?>
                  <?php if ($sm['tipe']==1) { ?>
                  <div class="pe-4 ms-4 pt-1">
                    <div class="mb-2"><?=$sm['nama_menu'];?></div>
                    <?php $subMenu = $this->menu->getSubMenu($edit['role_id'],$sm['menu_id']); ?>
                    <?php foreach ($subMenu as $smx) : ?>
                    <div class="form-check form-check-primary">
                      <label class="form-check-label">
                        <input class="form-check-input" type="checkbox" name="roles[]" value="<?=$sm['menu_id']."~".$smx['menu_id']?>" <?php if($smx['id_menu']<>''){ echo "checked";} ?> />
                        <?=$smx['nama_menu']?>
                      </label>
                    </div>
                    <?php endforeach; ?>
                  </div>
                  <?php } else { ?>
                  <div class="form-check form-check-primary">
                    <label class="form-check-label">
                      <input class="form-check-input" type="checkbox" name="roles[]" value="<?=$m['menu_id']."~".$sm['menu_id']?>" <?php if($sm['id_menu']<>''){ echo "checked";} ?> />
                      <?=$sm['nama_menu']?>
                    </label>
                  </div>
                  <?php } ?>
                  <?php endforeach; ?>
                <?php } else if ($m['tipe']==2) { ?>
                  <?php $checkms = $this->menu->getSubMenurow($edit['role_id'],$m['menu_id']); ?>
                  <div class="form-check form-check-primary">
                    <label class="form-check-label">
                      <input class="form-check-input" type="checkbox" name="roles[]" value="<?=$m['menu_id']."~".$m['menu_id']?>" <?php if($checkms>0){ echo "checked";} ?> />
                      <?=$m['nama_menu'];?>
                    </label>
                  </div>
                <?php } ?>
              </div>
              <?php endforeach; ?>
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