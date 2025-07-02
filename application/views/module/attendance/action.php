<button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="modal-body p-1">
  <?php if ($tipe=='photo') { ?>
  <div class="text-center mb-4">
    <h4 class="role-title mb-2 text-capitalize">Foto Absensi ( <?=$inout;?> )</h4>
  </div>
  <div class="">
    <div class="col-12 mb-1 text-center">
      <?php if ($inout=='in') { ?>
        <?php if ($datas['foto_absen_masuk']!='') { ?>
        <img class="img-fluid rounded" src="<?=base_url($datas['foto_absen_masuk']);?>" width="300" />
        <?php }else{ ?>
          Tidak ada foto.
        <?php } ?>
      <?php }else if ($inout=='out') { ?>
        <?php if ($datas['foto_absen_keluar']!='') { ?>
        <img class="img-fluid rounded" src="<?=base_url($datas['foto_absen_keluar']);?>" width="300" />
        <?php }else{ ?>
          Tidak ada foto.
        <?php } ?>
      <?php } ?>
    </div>
  </div>
  <?php } ?>
  <?php if ($tipe=='catatan') { ?>
  <div class="text-center mb-4">
    <h4 class="role-title mb-2 text-capitalize">Catatan ( <?=$inout;?> )</h4>
  </div>
  <div class="">
    <div class="col-12 mb-1 text-center ft-16">
      <?php 
        if ($inout=='in') { 
          if ($datas['catatan_masuk']!='') {
            echo $datas['catatan_masuk'];
          }else{
            echo 'Tidak ada catatan.';
          }
        }else if ($inout=='out') {
          if ($datas['catatan_keluar']!='') {
            echo $datas['catatan_keluar'];
          }else{
            echo 'Tidak ada catatan.';
          }
        }
      ?>
    </div>
  </div>
  <?php } ?>
  <?php if ($tipe=='map') { ?>
  <div class="text-center mb-4">
    <h4 class="role-title mb-2 text-capitalize">Koordinat ( <?=$inout;?> )</h4>
  </div>
  <?php 
    if ($inout=='in') { 
      $slat = $datas['latitude_masuk'];
      $slng = $datas['longitude_masuk'];
    }else{
      $slat = $datas['latitude_keluar'];
      $slng = $datas['longitude_keluar'];
    }
  ?>
  <div class="">
    <div class="col-12 mb-1 text-center ft-16">
      <?php if ($slat!='' && $slng!='') { ?>
        <div id="googleMap" style="width:100%;height:400px;"></div>
      <?php }else{ ?>
        Manual
      <?php } ?>
    </div>
  </div>
  
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDeY_0v4-MA7fDR8mf9Ssw6_skjyTFGbE0&callback=initMap&v=weekly" defer ></script>
 
  <script type="text/javascript">
    var p_lat = Number('<?=$datas['point_latitude'];?>');
    var p_lng = Number('<?=$datas['point_longitude'];?>');
    var s_lat = Number('<?=$slat;?>');
    var s_lng = Number('<?=$slng;?>');
    console.log(p_lat);
    console.log(p_lng);
    function initMap() {
      const map = new google.maps.Map(document.getElementById("googleMap"), {
        zoom: 15,
        center: { lat: p_lat, lng: p_lng },
        disableDefaultUI: true
      });

      // Tambahkan marker dengan ikon pada titik pertama
      const marker1 = new google.maps.Marker({
        position: { lat: p_lat, lng: p_lng },
        map: map,
        icon: {
          url: "http://maps.google.com/mapfiles/ms/icons/red-dot.png", // Ikon marker pertama
          scaledSize: new google.maps.Size(30, 30) // Ukuran ikon
        }
      });

      // Tambahkan marker dengan ikon pada titik kedua
      const marker2 = new google.maps.Marker({
        position: { lat: s_lat, lng: s_lng },
        map: map,
        icon: {
          url: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png", // Ikon marker kedua
          scaledSize: new google.maps.Size(30, 30) // Ukuran ikon
        }
      });
      
      const flightPlanCoordinates = [
        { lat: p_lat, lng: p_lng },
        { lat: s_lat, lng: s_lng }
      ];
      const flightPath = new google.maps.Polyline({
        path: flightPlanCoordinates,
        geodesic: true,
        strokeColor: "#FF0000",
        strokeOpacity: 1.0,
        strokeWeight: 2,
      });

      flightPath.setMap(map);
    }

    window.initMap = initMap;
  </script>
  <?php } ?>
</div>