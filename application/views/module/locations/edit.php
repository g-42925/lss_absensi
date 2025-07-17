<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Edit Data</h5>
    </div>
    <div class="card">
      <form class="card-body" action="<?=base_url('locations/edit_proses/'.$edit['lokasi_id']);?>" method="POST">
        <?=$this->session->flashdata('message');?>
        <div class="row g-3">
          <div class="col-xl-6 col-md-6 col-sm-6">
            <div class="mb-3">
              <input id="addressmaploc" type="text" class="form-control" placeholder="Cari lokasi disini..." />
            </div>
            <div id="googleMap" style="width:100%;height:400px;"></div>
          </div>
          <div class="col-xl-6 col-md-6 col-sm-6">
            <div class="row g-3">
              <div class="col-xl-12 col-md-12">
                <label class="form-label">Alamat<i class="text-danger">*</i></label>
                <textarea type="text" class="form-control btn-light" name="alamat" id="alamat_locglgb" placeholder="..." required="" rows="4" readonly=""><?=$edit['alamat_lokasi'];?></textarea>
              </div>
              <div class="col-xl-12 col-md-12">
                <label class="form-label">Garis Lintang<i class="text-danger">*</i></label>
                <input type="text" class="form-control btn-light" name="gl" placeholder="..." required="" readonly="" value="<?=$edit['garis_lintang'];?>" id="latitude_gl" />
              </div>
              <div class="col-xl-12 col-md-12">
                <label class="form-label">Garis Bujur<i class="text-danger">*</i></label>
                <input type="text" class="form-control btn-light" name="gb" placeholder="..." required="" readonly="" value="<?=$edit['garis_bujur'];?>" id="longitude_gb" />
              </div>
              <div class="col-xl-12 col-md-12">
                <label class="form-label">Nama Lokasi<i class="text-danger">*</i></label>
                <input type="text" class="form-control" name="nama" placeholder="..." value="<?=$edit['nama_lokasi'];?>" required="" />
              </div>
              <div class="col-xl-12 col-md-12">
                <label class="form-label" for="multicol-country">Radius<i class="text-danger">*</i></label>
                <select class="select2 form-select" name="radius" id="radiusgbgl" required onchange="changeradius()">
                  <option value="10" <?php if ($edit['jangkauan_radius']=='10') echo 'selected'; ?>>10 Meter</option>
                  <option value="20" <?php if ($edit['jangkauan_radius']=='20') echo 'selected'; ?>>20 Meter</option>
                  <option value="30" <?php if ($edit['jangkauan_radius']=='30') echo 'selected'; ?>>30 Meter</option>
                  <option value="40" <?php if ($edit['jangkauan_radius']=='40') echo 'selected'; ?>>40 Meter</option>
                  <option value="50" <?php if ($edit['jangkauan_radius']=='50') echo 'selected'; ?>>50 Meter</option>
                  <option value="100" <?php if ($edit['jangkauan_radius']=='100') echo 'selected'; ?>>100 Meter</option>
                  <option value="150" <?php if ($edit['jangkauan_radius']=='150') echo 'selected'; ?>>150 Meter</option>
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

<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&key=AIzaSyDeY_0v4-MA7fDR8mf9Ssw6_skjyTFGbE0&libraries=places"></script>
 
<script>
  var marker;
  var circle;
  var lat = document.getElementById("latitude_gl").value;
  var lng = document.getElementById("longitude_gb").value;
  var latlng;

  var mapProp= {
    center: new google.maps.LatLng(lat,lng),
    zoom:15,
    fullscreenControl: false,
    streetViewControl: false
  };
  var map = new google.maps.Map(document.getElementById("googleMap"),mapProp);
  var geocoder = new google.maps.Geocoder();

  latlng = map.center;
  initialize_sec();

  google.maps.event.addDomListener(window, 'load', initialize);

  function initialize() {

    var input = document.getElementById('addressmaploc');
    var autocomplete = new google.maps.places.Autocomplete(input);
    autocomplete.addListener('place_changed', function () {
      var place = autocomplete.getPlace();

      latlng = place.geometry['location'];
      lat = place.geometry['location'].lat();
      lng = place.geometry['location'].lng();

      if (place.geometry.viewport) {
        map.fitBounds(place.geometry.viewport);
      } else {
        map.setCenter(place.geometry.location);
        map.setZoom(15);
      }

      initialize_sec();

    });

    google.maps.event.addListener(map, 'click', function(event) {
      
      latlng = event.latLng;
      lat = event.latLng.lat();
      lng = event.latLng.lng();

      initialize_sec();

    });
    
  }

  function initialize_sec() {
    if (latlng!='') {

      if (typeof marker !== 'undefined'){ marker.setMap(); }
      if (typeof circle !== 'undefined'){ circle.setMap(); }

      document.getElementById("latitude_gl").value = lat;
      document.getElementById("longitude_gb").value = lng;

      marker = new google.maps.Marker({position: latlng, map: map});

      geocoder.geocode({ 'latLng': latlng }, function (results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
          if (results[1]) {
            document.getElementById("alamat_locglgb").value = results[1].formatted_address;
          } else {
            alert('No results found, please try again.');
          }
        } else {
          alert('Geocoder failed due to: ' + status);
        }
      });

      circle = new google.maps.Circle({
        center: latlng,
        map: map,
        radius: Number(document.getElementById("radiusgbgl").value), // in meters.
        fillColor: '#FF6600',
        fillOpacity: 0.3,
        strokeColor: "#FFF",
        strokeWeight: 0
      });
    }
  }

  function changeradius() {
    
    if (typeof marker !== 'undefined'){ marker.setMap(); }
    if (typeof circle !== 'undefined'){ circle.setMap(); }

    marker = new google.maps.Marker({position: latlng, map: map});

    circle = new google.maps.Circle({
      center: latlng,
      map: map,
      radius: Number(document.getElementById("radiusgbgl").value), // in meters.
      fillColor: '#FF6600',
      fillOpacity: 0.3,
      strokeColor: "#FFF",
      strokeWeight: 0
    });
  }
</script>