<script
  src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDXS1EEABl1XxIcZEK6wyzOFHzveWzpoRs&language=ar&region=EG">
</script>

<script>
  var marker;
  var map;
  var myCenter = new google.maps.LatLng(28.07198030177986,30.9814453125);

  function initialize()
  {
    var mapProp = {
      center: myCenter,
      zoom: 6,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    map = new google.maps.Map(document.getElementById("sample"), mapProp);

    google.maps.event.addListener(map, 'click', function (event) {
      placeMarker(event.latLng);
    });
  }
  function placeMarker(location) {

    if (typeof marker === 'object') {
      marker.setPosition(location);
    } else {
      marker = new google.maps.Marker({
        position: location,
        map: map,
      });
    }
//var latlng = new google.maps.LatLng(location.lat(), location.lng());
//
//  var infowindow = new google.maps.InfoWindow({
//    content: 'Latitude: ' + location.lat() + '<br>Longitude: ' + location.lng()
//  });
//  infowindow.open(map,marker);
    $('#inputs').html('<input type="hidden" name="lat" value="' + location.lat() + '" /> ' +
            '<input type="hidden" name="lng" value="' + location.lng() + '" />' +
            '<input type="hidden" name="zoom" value="' + map.zoom + '" /> ');

  }

  google.maps.event.addDomListener(window, 'load', initialize);
</script>
<div id="sample" style="width:100%;height:380px;"></div>
<div id="inputs"></div>