<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Tambah Data</h5>
    </div>
    <div class="card">
      <form class="card-body" enctype="multipart/form-data" id="formTarget" action="<?=base_url('candidate/edit_proccess/').$id ?>" method="POST">
        <?php if($failed): ?>
          <?=$this->session->flashdata('message');?>
        <?php endif; ?>
        <div class="row g-3">
          <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12">
            <label class="form-label">Nik<i class="text-danger">*</i></label>
            <input value="<?= $candidate['nik'] ?>"type="text" class="form-control" name="nik" required />
            <input type="hidden" id="changeMarker" class="form-control" name="changeMarker" value="0" />

          </div>
          <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12">
            <label class="form-label">Name<i class="text-danger">*</i></label>
            <input value="<?= $candidate['candidate_name'] ?>"t type="text" class="form-control" name="name" required />
          </div>
          <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12">
            <label class="form-label">Nomor Ponsel<i class="text-danger">*</i></label>
            <input value="<?= $candidate['phone_number'] ?>"t type="text" class="form-control" name="phone_number" required />
          </div>
          <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12">
            <label class="form-label">Email<i class="text-danger">*</i></label>
            <input value="<?= $candidate['email'] ?>"t type="text" class="form-control" name="email" required />
          </div>
          <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12">
            <label class="form-label" for="multicol-country">Jenis kelamin<i class="text-danger">*</i></label>
            <select class="select2 form-select" name="sex" required>
                <option <?= $candidate['sex'] == 'l' ? 'selected':'' ?> value="l">Laki-laki</option>
                <option <?= $candidate['sex'] == 'p' ? 'selected':'' ?> value="p">Perempuan</option>
            </select>
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6 col-xs-6">
            <label class="form-label" for="multicol-country">Posisi yang dituju<i class="text-danger">*</i></label>
            <select class="select2 form-select" name="position_id" required>
              <?php foreach($position as $p): ?>
                <option <?= $candidate['position_id'] == $p['id'] ? 'selected':'' ?> value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-xl-12 col-md-12 col-sm-12 mb-2">
            <label class="form-label">Foto kandidat</label>
            <input type="file" class="form-control" name="photo" id="photo" />
            <div class="mt-2 small">Rekomendasi 1:1 ratio, format (jpg/png) maksimal 2mb.</div>
            <div class="mt-2">
                <img id="previewTarget" src="<?= $candidate['candidate_picture'] ?>" width="120" class="rounded">
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

<script type="text/javascript">
  $(document).ready(function () {
    const idField = document.getElementById('candidateId');
    const previewTarget = document.getElementById("previewTarget");
    const formTarget = document.getElementById("formTarget");
    const photo = document.getElementById("photo");
    const candidateId = document.getElementById('candidateId');

    let currentObjectURL = null;
    
    photo.addEventListener('change', (e) => {
      const file = e.target.files && e.target.files[0];
      if (!file) return;

      // validasi sederhana
      if (!file.type.startsWith('image/')) {
        alert('Pilih file gambar!');
        fileInput.value = '';
        return;
      }
      if (file.size > 5 * 1024 * 1024) { // batas 5MB
        alert('Ukuran maksimal 5MB');
        fileInput.value = '';
        return;
      }

      currentFile = file;

      // Cara cepat: gunakan URL.createObjectURL untuk preview lokal
      if (currentObjectURL) URL.revokeObjectURL(currentObjectURL);
      
      currentObjectURL = URL.createObjectURL(file)
      previewTarget.src = currentObjectURL
      changeMarker.value = 1;

    });

    candidateId.value = Date.now()

  });


</script>