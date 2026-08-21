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
        <div class="row g-3">
          <div class="col-xl-12 col-lg-12">
            <div class="row">
              <?php foreach ($actions as $act) : ?>
                <?php foreach ($act as $x) : ?>
                  <div class="col-xl-3 col-lg-3 col-md-3 col-sm-4 col-xs-6 mb-3">
                       <div class="form-check form-check-primary">
                        <label class="form-check-label">
                          <input onChange="onChg(this)" class="form-check-input" type="checkbox" name="roles[]" value="<?=$x['id']."~".$x['id']?>" />
                          <?=$x['description']?>
                        </label>
                      </div>
                  </div>
                <?php endforeach; ?>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
        <div class="pt-5 text-end">
          <a href="javascript:window.history.back();" class="btn btn-label-secondary me-sm-3 me-1">Batal</a>
          <button type="submit" class="btn btn-primary">Simpan Data</button>
        </div>
        <input name="uncheck" type="hidden" class="p-3 border-2 border-black rounded-md" id="target">
      </form>
    </div>
  </div>
</div>

<script>
function onChg(checkbox){
  var target = document.getElementById('target');
  var value = target.value
    if (!checkbox.checked) {
      if(target.value == ''){
        target.value = checkbox.value.split('~')[1];
      }
      else{ 
        target.value = `${target.value}/${checkbox.value.split('~')[1]}`;
      }
    }
    else{
        if(target.value !== ''){
          if(target.value.includes(`/${checkbox.value}`)){
            target.value = target.value.replace(`/${checkbox.value.split('~')[1]}`, '');
          }
          else{
            target.value = target.value.replace(`${checkbox.value.split('~')[1]}`, '');
            target.value = target.value.replace(`/`, '');
          }
        }                                                                                                                                                                                       
    }
}
</script>
