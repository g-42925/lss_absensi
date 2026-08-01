<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Edit Data</h5>
    </div>
    <div class="card">
      <form class="card-body" action="<?=base_url('locations/edit_proses/'.$edit['lokasi_id']);?>" method="POST">
        <?php if ($failed): ?>
          <?=$this->session->flashdata('message');?>
        <?php endif; ?>
        <div class="row g-3">
          <div class="col-xl-6 col-md-6 col-sm-6">
            <div class="mb-3">
              <input id="addressmaploc" type="text" class="form-control" placeholder="Cari lokasi disini..." />
            </div>
            <div id="search-result" class="list-group"></div>
            <div id="map" style="width:100%;height:400px;"></div>
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6">
            <div class="row g-3">
              <div class="col-xl-12 col-md-12">
                <label class="form-label">Alamat<i class="text-danger">*</i></label>
                <textarea type="text" class="form-control btn-light" name="alamat" id="alamat_locglgb" placeholder="..." required="" rows="4"><?=$edit['alamat_lokasi'];?></textarea>
              </div>
              <div class="col-xl-12 col-md-12">
                <label class="form-label">Garis Lintang<i class="text-danger">*</i></label>
                <input type="text" class="form-control btn-light" name="gl" placeholder="..." required="" value="<?=$edit['garis_lintang'];?>" id="latitude_gl" />
              </div>
              <div class="col-xl-12 col-md-12">
                <label class="form-label">Garis Bujur<i class="text-danger">*</i></label>
                <input type="text" class="form-control btn-light" name="gb" placeholder="..." required="" value="<?=$edit['garis_bujur'];?>" id="longitude_gb" />
              </div>
              <div class="col-xl-12 col-md-12">
                <label class="form-label">Nama Lokasi<i class="text-danger">*</i></label>
                <input type="text" class="form-control" name="nama" placeholder="..." value="<?=$edit['nama_lokasi'];?>" required="" />
              </div>
              <div class="col-xl-12 col-md-12">
                <label class="form-label" for="multicol-country">Radius<i class="text-danger">*</i></label>
                <select class="select2 form-select" name="radius" id="radiusgbgl" required onchange="changeradius()">
                  <option value="0" <?php if ($edit['jangkauan_radius']=='0') echo 'selected'; ?>>0 meter</option>
                  <option value="5" <?php if ($edit['jangkauan_radius']=='5') echo 'selected'; ?>>5 meter</option>
                  <option value="10" <?php if ($edit['jangkauan_radius']=='10') echo 'selected'; ?>>10 meter</option>
                  <option value="15" <?php if ($edit['jangkauan_radius']=='15') echo 'selected'; ?>>15 meter</option>
                  <option value="20" <?php if ($edit['jangkauan_radius']=='20') echo 'selected'; ?>>20 meter</option>
                  <option value="25" <?php if ($edit['jangkauan_radius']=='25') echo 'selected'; ?>>25 meter</option>
                  <option value="30" <?php if ($edit['jangkauan_radius']=='30') echo 'selected'; ?>>30 meter</option>
                  <option value="35" <?php if ($edit['jangkauan_radius']=='35') echo 'selected'; ?>>35 meter</option>
                  <option value="40" <?php if ($edit['jangkauan_radius']=='40') echo 'selected'; ?>>40 meter</option>
                  <option value="45" <?php if ($edit['jangkauan_radius']=='45') echo 'selected'; ?>>45 meter</option>
                  <option value="50" <?php if ($edit['jangkauan_radius']=='50') echo 'selected'; ?>>50 meter</option>
                  <option value="100" <?php if ($edit['jangkauan_radius']=='100') echo 'selected'; ?>>100 meter</option>
                </select>
              </div>
              <div class="col-xl-12 col-md-12">
                <label class="form-label">Lokasi Utama</label>
                <select class="form-select" name="lokasi_utama" id="lokasi_utama" required>
                  <option value="1" <?php if ($edit['main_location']=='1') echo 'selected'; ?>>Ya</option>
                  <option value="0" <?php if ($edit['main_location']=='0') echo 'selected'; ?>>Tidak</option>
                </select>
              </div>
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

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
  var attribute = '&copy; OpenStreetMap contributors';
  var map = L.map('map').setView([51.505, -0.09], 13);
 
  L.tileLayer('https://tiles.locationiq.com/v3/streets/r/{z}/{x}/{y}.png?key=pk.d06328c7edafb1675ef1d1914ec2acd4',{attribution: attribute}).addTo(map);

  map.on('click', function(e) {
    var lat = e.latlng.lat;
    var lng = e.latlng.lng;
    document.getElementById('latitude_gl').value = lat;
    document.getElementById('longitude_gb').value = lng;
  });

  const searchInput = document.getElementById("addressmaploc");
  const resultBox = document.getElementById("search-result");

  searchInput.addEventListener("keyup", async function(){
    const keyword = this.value;

    if(keyword.length < 3){
      resultBox.innerHTML = "";
      return;
    }

    const res = await fetch(
      `https://us1.locationiq.com/v1/search?key=pk.d06328c7edafb1675ef1d1914ec2acd4&q=${encodeURIComponent(keyword)}&format=json&countrycodes=id`
    );

    const data = await res.json();

    resultBox.innerHTML = "";

    data.forEach(item=>{

        resultBox.innerHTML += `
            <a
                class="list-group-item list-group-item-action"
                data-lat="${item.lat}"
                data-lon="${item.lon}"
                data-address="${item.display_name}"
            >
                ${item.display_name}
            </a>
        `;

    });

    resultBox.addEventListener("click",function(e){
      if(!e.target.dataset.lat) return;

      document.getElementById("latitude_gl").value = e.target.dataset.lat;
      document.getElementById("longitude_gb").value = e.target.dataset.lon;
      document.getElementById("alamat_locglgb").value = e.target.dataset.address;

      map.setView([e.target.dataset.lat, e.target.dataset.lon], 15);
      L.marker([e.target.dataset.lat, e.target.dataset.lon]).addTo(map);
      resultBox.innerHTML = ''
    })
  })
</script>