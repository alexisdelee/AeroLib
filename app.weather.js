function standardization(details){
  switch(details[0]){
    case "date":
      details[0] = "<strong>dernière actualisation à ";
      details[1] += "</strong><br>";
      break;
    case "temp":
      details[0] = "Température actuelle : ";
      details[1] += "°C";
      break;
    case "pressure":
      details[0] = "Pression : ";
      details[1] += "hPa";
      break;
    case "humidity":
      details[0] = "Humidité : ";
      details[1] += "%";
      break;
    case "temp_min":
      details[0] = "Température minimale : ";
      details[1] += "°C";
      break;
    case "temp_max":
      details[0] = "Température maximale : ";
      details[1] += "°C";
      break;
    case "visibility":
      details[0] = "visibilité : ";
      details[1] += "m";
      break;
    case "speed":
      details[0] = "Vent : ";
      details[1] += "km/h";
      break;
    case "sunrise":
      details[0] = "Lever du soleil : ";
      break;
    case "sunset":
      details[0] = "Coucher du soleil : ";
      break;
    case "undefined":
      details[0] = "Le service météo est actuellement indisponible.";
      break;
    default:
      details[0] = details[0].charAt(0).toUpperCase() + details[0].substring(1).toLowerCase() + " : ";
      details[1] = details[1].toLowerCase();
  }

  return details;
}

function getWeather(){
  var request = new XMLHttpRequest();
  var weather = "";

  request.onreadystatechange = function(){
    if(request.readyState == 4 && request.status == 200){
      if(request.responseText == "undefined"){
        weather = standardization(["undefined", ""]).join("") + "<br>";
      } else {
        var response = request.responseText.split(":").filter(function(els){
          return els.length != 0;
        });

        for(var res of response){
          var details = res.split("-");

          if(details[0] == "date" || details[0] == "sunrise" || details[0] == "sunset"){
            var date = new Date(details[1] * 1000); // convert timestamp to date

            var hours = date.getHours();
            var minutes = "0" + date.getMinutes();
            var seconds = "0" + date.getSeconds();

            details[1] = hours + ":" + minutes.substr(-2) + ":" + seconds.substr(-2);
          }

          details = standardization(details);
          weather += details[0] + details[1] + "<br>";
        }
      }

      initMap(weather);
    }
  }

  request.open('GET', 'getWeather.php');
  request.send();
}