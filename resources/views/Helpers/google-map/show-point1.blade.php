
<script
src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDXS1EEABl1XxIcZEK6wyzOFHzveWzpoRs">
</script>

<script>
var myCenter=new google.maps.LatLng({{$lat}},{{$lng}});
var marker;

function initialize()
{
var mapProp = {
  center:myCenter,
  zoom:6,
  mapTypeId:google.maps.MapTypeId.ROADMAP
  };

var map=new google.maps.Map(document.getElementById("googleMap"),mapProp);

var marker=new google.maps.Marker({
  position:myCenter,
  animation:google.maps.Animation.BOUNCE
  });

marker.setMap(map);
map.center(  myCenter);
}

    google.maps.event.addDomListener(window, 'load', initialize);

</script>

<div id="googleMap" style="width:100%;height:380px;"></div>
