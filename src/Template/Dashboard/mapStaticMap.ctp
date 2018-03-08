
<section class="content">
  <div id="main-wrapper">
    <div class="row"><!-- Start Row2 -->
      <div class="col-xs-12">
        <h3>Maps</h3>
        <div id="map"></div>
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
src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAknpAArC-q0gxMWJ5S90yfiLgZjT_WtWY&callback=initMap">
</script>
<script>
  function initMap() {
    var gyms = <?php echo $maps; ?>;
    var france = {lat: 45.9203815, lng: 4.0402203};
    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 4,
      center: france
    });
    for(var i=0; i < gyms.length; i++) {
      var marker1 = new google.maps.Marker(Object.assign({}, gyms[i], {map: map}));
    }
  }
</script>
<?php $this->end(); ?>
