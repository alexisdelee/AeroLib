const http = require("http");
const request = require("request");

let planes = [];
let notificationContainer = document.querySelector("#notification");

function list(index) {
  let label = document.querySelector("label[data-day=\"" + index + "\"]");
  let day = parseInt(label.childNodes[0].textContent);

  let labels = document.querySelectorAll("#days label");
  for(let _label = 0, l = labels.length; _label < l; _label++) {
    labels[_label].querySelector("span").style.borderColor = "#61b2a7"; // on redonne l'ancien style
  }

  label.querySelector("span").style.borderColor = "#4E7CAD";

  let date = new Date(calendar.year, calendar.month, day);
  let startday = Math.floor(date.getTime() / 1000); // 00:00:00
  let endday = startday + 24 * 3600 - 1; // 23:59:59

  calendar.startday = startday;
  calendar.endday = endday;

  planning(startday, endday);
}

listOfPlanes();

function listOfPlanes() {
  mysql.exec("SELECT `idPrivatePlane`, `type`, `use` FROM `privateplane`", [], (results, fields) => {
    let parent = document.querySelector("#planes ul");

    for(let result of results) {
      let input = document.createElement("input");
      input.setAttribute("type", "checkbox");
      input.setAttribute("id", "plane_" + result.idPrivatePlane);
      input.setAttribute("checked", "checked");

      let label = document.createElement("label");
      label.setAttribute("class", "labels");
      label.setAttribute("for", "plane_" + result.idPrivatePlane);
      label.textContent = result.type;

      let li = document.createElement("li");

      li.appendChild(input);
      li.appendChild(label);

      parent.appendChild(li);

      planes.push({
        id: result.idPrivatePlane,
        type: result.type,
        use: result.use,
        status: true
      });

      // gestion des évènements

      input.addEventListener("click", (e) => {
        let nameOfPlane = result.type;

        for(let plane of planes) {
          if(plane.type == nameOfPlane) {
            if(plane.status) {
              plane.status = false;
            } else {
              plane.status = true;
            }

            planning(calendar.startday, calendar.endday); // on relance automatiquement l'affichage des notifications
          }
        }
      });
    }
  });
}

function planning(start, end) {
  let timer = setTimeout(() => {
    notificationContainer.innerHTML = "<div id=\"notfound\"></div>"; // on vide le container
  }, 200);
  statusContainer = false;

  for(let plane = 0, p = planes.length; plane < p; plane++) {
    if(!planes[plane].status) continue; // si l'avion est désactivé, on passe au suivant

    request.post({
      headers: {"Content-type": "application/x-www-form-urlencoded"},
      url: "http://aen.fr:8080/services/getStatusProperties.php",
      form: {"type": planes[plane].type, "start": start, "end": end}
    }, (err, response, body) => {
      if(response.statusCode == 406) { // on récupère uniquement les avions indisponibles
        if(!statusContainer) {
          clearTimeout(timer);
          notificationContainer.innerHTML = "";
          statusContainer = true;
        }

        const parsedData = JSON.parse(body);

        for(let _reserve = 0, n = parsedData.reserve.length; _reserve < n; _reserve++) {
          _create_notification(planes[plane].id, planes[plane].type, planes[plane].use, timestampToDate(parsedData.reserve[_reserve].dateStart), timestampToDate(parsedData.reserve[_reserve].dateEnd), [parsedData.reserve[_reserve].dateStart, parsedData.reserve[_reserve].dateEnd]);
        }
      }
    });
  }
}

function _create_notification(idPlane, type, use, start, end, options) {
  let id = document.createElement("div");
  let idColor = strToHex(type);
  id.setAttribute("title", use);
  if(use == "ecole") {
    id.style.background = idColor + " url(\"res/icon_study.png\") no-repeat center center";
    id.style.backgroundSize = "cover";
  } else if(use == "voyage") {
    id.style.background = idColor + " url(\"res/icon_trip.png\") no-repeat center center";
    id.style.backgroundSize = "cover";
  }

  let name = document.createElement("div");
  name.innerHTML = type;
  name.style.border = "2px solid " + idColor;
  name.style.color = idColor;

  let time = document.createElement("div");
  time.innerHTML = "<span class=\"dateStart\" data-time=\"" + start + "\">" + start + "</span><span class=\"dateStart\" data-time=\"" + start + "\">" + end + "</span>";

  id.classList.add("ids");
  name.classList.add("names");
  time.classList.add("times");

  let container = document.createElement("div");
  container.classList.add("notifications");

  container.appendChild(id);
  container.appendChild(name);
  container.appendChild(time);

  /* Event */
  if(root) {
    container.style.cursor = "pointer";

    container.addEventListener("click", () => {
      popup({type: type, start: options[0], end: options[1], planes: planes, id_plane: idPlane, use: use});
    });
  }

  notificationContainer.style.opacity = "0";
  notificationContainer.appendChild(container);
  Velocity(notificationContainer, {opacity: "1"}, {duration: 500});
}

function strToHex(str) {
  let hash = 0;

  for (let i = 0; i < str.length; i++) {
    hash = str.charCodeAt(i) + ((hash << 5) - hash);
  }

  let colour = '#';
  for (let i = 0; i < 3; i++) {
    let value = (hash >> (i * 8)) & 0xFF;
    colour += ('00' + value.toString(16)).substr(-2);
  }

  return colour;
}

function timestampToDate(unix_timestamp) {
  let date = new Date(unix_timestamp * 1000);
  let hours = date.getHours();
  let minutes = "0" + date.getMinutes();
  let secondes = "0" + date.getSeconds();

  return hours + ":" + minutes.substr(-2) + ":" + secondes.substr(-2);
}

/* gestion évènement number spinner */

let number = document.querySelector("input[type=\"number\"]");
let sub = document.querySelector(".sub");
let add = document.querySelector(".add");

sub.addEventListener("click", function() {
  if(parseInt(number.getAttribute("min")) < parseInt(number.value)) {
    number.value = parseInt(number.value) - 1;
    calendar.year -= 1;
    calendar.load(false);
  }
});

add.addEventListener("click", function() {
  if(parseInt(number.getAttribute("max")) > parseInt(number.value)) {
    number.value = parseInt(number.value) + 1;
    calendar.year += 1;
    calendar.load(false);
  }
});