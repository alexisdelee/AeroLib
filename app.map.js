function initMap(contentString) {
  var map = new google.maps.Map(document.getElementById("map"), {
    center: {lat: 48.8992771, lng: 1.2409076999999797},
    zoom: 7,
    disableDefaultUI: true,
  });

  var infoWindow = new google.maps.InfoWindow({
    content: contentString
  });

  var marker = new google.maps.Marker({
    position: {lat: 49.027013, lng: 1.151361},
    map: map,
    title: "Aéroport Evreux Normandie"
  });

  marker.addListener("click", function(){
    infoWindow.open(map, marker);
  });
}