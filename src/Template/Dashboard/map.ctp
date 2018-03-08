
<section class="content">
  <div id="main-wrapper">
    <div class="row"><!-- Start Row2 -->
      <div class="col-xs-12">
        <h3>Maps</h3>
        <div id="map"></div>
        <p><b>Address</b>: <span id="address"></span></p>
        <p id="error"></p>
      </div>
    </div>
  </div>
</section>

<?php $this->start('mapScript'); ?>
<style>
   #map {
    height: 400px;
    width: 100%;
   }
</style>
<script async defer
src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAknpAArC-q0gxMWJ5S90yfiLgZjT_WtWY">
</script>
<script>
  function initGym(map) {
    var gyms = <?php echo $maps; ?>;
     /*var france = {lat: 45.9203815, lng: 4.0402203};
     var map = new google.maps.Map(document.getElementById('map'), {
       zoom: 4,
       center: france
     });*/
     for(var i=0; i < gyms.length; i++) {
       var marker1 = new google.maps.Marker(Object.assign({}, gyms[i], {map: map}));
     }
   }

function writeAddressName(latLng) {
   var geocoder = new google.maps.Geocoder();

   geocoder.geocode({
    "location": latLng
  },
  function(results, status) {
    if (status == google.maps.GeocoderStatus.OK)
      document.getElementById("address").innerHTML = results[0].formatted_address;
    else
      document.getElementById("error").innerHTML += "Unable to retrieve your address" + "<br />";
  });
}

function geolocationSuccess(position) {
  var userLatLng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
  // Write the formatted address
  writeAddressName(userLatLng);

  var myOptions = {
    zoom : 16,
    center : userLatLng,
    mapTypeId : google.maps.MapTypeId.ROADMAP
  };
  // Draw the map
  var mapObject = new google.maps.Map(document.getElementById("map"), myOptions);
  /** ADD THE GYMS SALLES **/
  initGym(mapObject);
  // Place the marker
  new google.maps.Marker({
    map: mapObject,
    position: userLatLng
  });
  // Draw a circle around the user position to have an idea of the current localization accuracy
  var circle = new google.maps.Circle({
    center: userLatLng,
    radius: position.coords.accuracy,
    map: mapObject,
    fillColor: '#0000FF',
    fillOpacity: 0.5,
    strokeColor: '#0000FF',
    strokeOpacity: 1.0
  });
  mapObject.fitBounds(circle.getBounds());
}

function geolocationError(positionError) {
  document.getElementById("error").innerHTML += "Error: " + positionError.message + "<br />";
}

function geolocateUser() {
  // If the browser supports the Geolocation API
  if (navigator.geolocation)
  {
    var positionOptions = {
      enableHighAccuracy: true,
      timeout: 10 * 1000 // 10 seconds
    };
    navigator.geolocation.getCurrentPosition(geolocationSuccess, geolocationError, positionOptions);
  }
  else
    document.getElementById("error").innerHTML += "Your browser doesn't support the Geolocation API";
}

window.onload = geolocateUser;
</script>
<?php $this->end(); ?>
