
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
  position:myCenter,
         // icon:'{img}',
  });
  marker.setMap(map);

  
  map.center(myCenter)
  }

  google.maps.event.addDomListener(window, 'load', initialize);

</script>

<div id="googleMap" style="width:100%;"></div>
