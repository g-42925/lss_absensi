<button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="modal-body p-1">
  <?php if ($tipe=='photo') { ?>
  <div class="text-center mb-4">
    <h4 class="role-title mb-2 text-capitalize">Foto</h4>
  </div>
  <div class="">
    <div class="col-12 mb-1 text-center">
        <?php if ($datas['foto']!='') { ?>
        <img class="img-fluid rounded" src="<?=base_url($datas['foto']);?>" width="300" />
        <?php }else{ ?>
          Tidak ada foto.
        <?php } ?>
    </div>
  </div>
  <?php } ?>
  <?php if ($tipe=='catatan') { ?>
  <div class="text-center mb-4">
    <h4 class="role-title mb-2 text-capitalize">Catatan</h4>
  </div>
  <div class="">
    <div class="col-12 mb-1 text-center ft-16">
      <?php 
        if ($datas['catatan']!='') {
          echo $datas['catatan'];
        }else{
          echo 'Tidak ada catatan.';
        }
      ?>
    </div>
  </div>
  <?php } ?>
  <?php if ($tipe=='map') { ?>
  <div class="text-center mb-4">
    <h4 class="role-title mb-2 text-capitalize">Koordinat ( <?=$datas['latitude_lt'];?>, <?=$datas['longitude_lt'];?> )</h4>
  </div>
  <?php 
      $slat = $datas['latitude_lt'];
      $slng = $datas['longitude_lt'];
  ?>
  <div class="">
    <div class="col-12 mb-1 text-center ft-16">
      <?php if ($slat!='' && $slng!='') { ?>
        <div id="googleMap" style="width:100%;height:400px;"></div>
      <?php }else{ ?>
        Tidak diketahui
      <?php } ?>
    </div>
  </div>
  
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDeY_0v4-MA7fDR8mf9Ssw6_skjyTFGbE0&callback=initMap&v=weekly" defer ></script>
 
  <script type="text/javascript">
    var p_lat = Number('<?=$slat;?>'); 
    var p_lng = Number('<?=$slng;?>'); 
    console.log(p_lat);
    console.log(p_lng);

    function initMap() {
    const map = new google.maps.Map(document.getElementById("googleMap"), {
        zoom: 15,
        center: { lat: p_lat, lng: p_lng },
        disableDefaultUI: true
    });

    // Tambahkan marker dengan ikon pada lokasi
    const marker = new google.maps.Marker({
        position: { lat: p_lat, lng: p_lng },
        map: map,
        icon: {
        url: "https://maps.google.com/mapfiles/ms/icons/red-dot.png", // Ganti URL_ICON dengan path ikon yang diinginkan
        scaledSize: new google.maps.Size(30, 30) // Ukuran ikon
        }
    });
    }

    window.initMap = initMap;
  </script>
  <?php } ?>
</div>