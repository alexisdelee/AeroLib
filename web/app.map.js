function initMap(contentString) {
  var styles = [
    {
      stylers: [
        { hue: "#4E7CAD" },
        { saturation: 20 }
      ]
    },{
      featureType: "road",
      elementType: "geometry",
      stylers: [
        { lightness: 100 },
        { visibility: "simplified" }
      ]
    },{
      featureType: "road",
      elementType: "labels",
      stylers: [
        { visibility: "off" }
      ]
    }
  ];

  var styledMap = new google.maps.StyledMapType(styles, {name: "Styled Map"});

  var map = new google.maps.Map(document.getElementById("map"), {
    center: {lat: 48.8992771, lng: 1.2409076999999797},
    zoom: 7,
    scrollwheel: false,
    disableDefaultUI: true,
    mapTypeControlOptions: {
      mapTypeIds: [google.maps.MapTypeId.ROADMAP, "map_style"]
    }
  });

  map.mapTypes.set("map_style", styledMap);
  map.setMapTypeId("map_style");

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