<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDXS1EEABl1XxIcZEK6wyzOFHzveWzpoRs"></script>

<script>
  var myCenter = new google.maps.LatLng(24.4455918, 45.6810391);
  var marker;
  var map;
  var markers = {result};
  function initialize()
  {
//    $('#googleMap').css({
//      'width': $(window).width() - 100,
//      'height': $(window).height() - 100
//    });
    var mapProp = {
      center: myCenter,
      zoom: 2,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    var map = new google.maps.Map(document.getElementById("googleMap"), mapProp);
    for (var i in markers)
    {
      prop = {};
      prop.center = new google.maps.LatLng(markers[i].latitude, markers[i].longitude);
//      prop.icon = markers[i].image;
      prop.description = markers[i].address + '<br>' + markers[i].email + '<br>' + markers[i].number;
      createMarker(prop, map);
    }
  }

  google.maps.event.addDomListener(window, 'load', initialize);

  function createMarker(prop, map)
  {
    var marker = new google.maps.Marker({
      position: prop.center,
//      icon: '{url_image}/' + prop.icon,
    });
    marker.setMap(map);
    google.maps.event.addListener(marker, "mouseover", function () {
      infowindow = new google.maps.InfoWindow({content: prop.description});
      infowindow.open(map, marker);
    });
    google.maps.event.addListener(marker, "mouseout", function () {
      infowindow.close();
    });
  }
</script>

<div id="googleMap" style="width:100%;height:650px;"></div>
