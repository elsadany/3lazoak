
<script
  src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDXS1EEABl1XxIcZEK6wyzOFHzveWzpoRs">
</script>

<script>
  var myCenter = new google.maps.LatLng({{$lat}}, {{$lng}});
  var marker;
  var map;
  function initialize()
  {
  var mapProp = {
  center:myCenter,
          zoom:{{$zoom}},
          mapTypeId:google.maps.MapTypeId.ROADMAP
  };
  var map = new google.maps.Map(document.getElementById("googleMap"), mapProp);
  var marker = new google.maps.Marker({
  position:myCenter
         // icon:'{img}',
  });
  marker.setMap(map);
  
  google.maps.event.addListener(marker, "mouseout", function() {
  infowindow.close();
  });
  google.maps.event.addListener(map, 'click', function(event) {
  marker.setPosition(event.latLng);
  $('#inputs').html('<input type="hidden" name="lat" value="' + event.latLng.lat() + '" /> ' +
          '<input type="hidden" name="lng" value="' + event.latLng.lng() + '" />' +
          '<input type="hidden" name="zoom" value="' + map.zoom + '" /> ');
  
  });
  map.center(myCenter)
  }


  google.maps.event.addDomListener(window, 'load', initialize);

</script>

<div id="googleMap" style="width:100%;height:380px;"></div>
<div id="inputs">
    <input type="hidden" name="lat" value="{{$lat}}" />
    <input type="hidden" name="lng" value="{{$lng}}" />
    <input type="hidden" name="zoom" value="{{$zoom}}" />
</div>
