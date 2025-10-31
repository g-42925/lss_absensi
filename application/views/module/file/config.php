<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Edit Allowance</h5>
    </div>
    <div class="card">
      <form class="card-body" enctype="multipart/form-data" action="<?= $candidate ? base_url('file/c_config_process/').$id : base_url('file/config_process/').$id ?>" method="POST">
        <?php if ($failed): ?>
          <?=$this->session->flashdata('message');?>
        <?php endif; ?>
        <div class="row g-3">
          <?php foreach($data as $a): ?>
            <div class="col-xl-6 col-md-6 col-sm-6">
              <label class="form-label">File Name<i class="text-danger">*</i></label>
              <input value="<?= $a['file_id'] ?>" type="hidden" name="file[]" class="form-control" />
              <input value="<?= $a['title'] ?>" readonly type="text" class="form-control" />
            </div>
            <div class="col-xl-6 col-md-6 col-sm-6">
              <label class="form-label">Source<i class="text-danger">*</i></label>
              <div class="flex flex-row gap-3 items-start">
                <input data-current="<?= $a['file_id'] ?>" name="photo[]" type="file" onchange="onFileChange(event)" class="form-control" />
                <a target="_blank" href="<?= $a['source'] == '-' ? $placeholder:$a['source'] ?>">
                  <img id="<?= 'preview-'.$a['file_id'] ?>" src="<?= $a['source'] == '-' ? $placeholder : $a['source'] ?>" class="w-16 h-16"/>
                </a>
              </div>
            </div>
            <div class="col-xl-4 col-md-4 col-sm-4 hidden">
              <label class="form-label">Current<i class="text-danger">*</i></label>
              <div class="flex flex-row gap-3">
                <input name="current[]" id="<?= 'current-'.$a['file_id'] ?>" value="<?= $a['source'] ?>" type="text" class="form-control" />
              </div>
            </div>
            
          <?php endforeach; ?>
        </div>
        <div class="pt-5 text-end">
          <a href="javascript:window.history.back();" class="btn btn-label-secondary me-sm-3 me-1">Batal</a>
          <button type="submit" class="btn btn-primary">Simpan Data</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
   
  function onFileChange(e){
    var id = e.target.getAttribute("data-current")
    var current = document.getElementById(`current-${id}`);
    const file = e.target.files && e.target.files[0];
    const previewTarget = document.getElementById(`preview-${id}`);

    let currentObjectURL = null;

    if(!file.type.startsWith('image/')) {
      alert('Pilih file gambar!');
      fileInput.value = '';
      return;
    }
    if(file.size > 5 * 1024 * 1024) { // batas 5MB
      alert('Ukuran maksimal 5MB');
      fileInput.value = '';
      return;
    }

    if (currentObjectURL) URL.revokeObjectURL(currentObjectURL);
      
    currentObjectURL = URL.createObjectURL(file)
    previewTarget.src = currentObjectURL

    current.value = ''
  }

  function preview(src){
    if(src == '' || src == '-'){
      alert('preview tidak tersedia')
    }
    else{
      window.open(src, '_blank');
    }
  }
</script>