const http = require("http");
const mysql = require("mysql");

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
  let connection = mysql.createConnection({
    // host: "192.168.91.141",
    host: "localhost",
    user: "root",
    password: "",
    database: "aerodrome"
  });

  connection.connect();

  connection.query("SELECT `idPrivatePlane`, `type`, `use` FROM `privateplane`", (err, results, fields) => {
    if(err) {
      alert(err);
      return;
    }

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
        type: result.type,
        use: result.use,
        status: true
      });

      /* gestion des évènements */

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

  connection.end();
}

function planning(start, end) {
  let timer = setTimeout(() => {
    notificationContainer.innerHTML = "<div id=\"notfound\"></div>"; // on vide le container
  }, 200);
  statusContainer = false;

  for(let plane of planes) {
    if(!plane.status) continue; // si l'avion est désactivé, on passe au suivant

    http.get("http://localhost/aerodrome/services/getStatusProperties.php?type=" + plane.type + "&start=" + start + "&end=" + end, (res) => {
      const { statusCode } = res;
      const contentType = res.headers["content-type"];

      if(statusCode == 200) {
        if(statusContainer == false) {
          clearTimeout(timer);
          notificationContainer.innerHTML = "";
          statusContainer = true;
        }

        res.setEncoding("utf8");
        let rawData = "";
        res.on("data", (chunk) => { rawData += chunk; });
        res.on("end", () => {
          try {
            const parsedData = JSON.parse(rawData);

            for(let _reserve = 0, n = parsedData.reserve.length; _reserve < n; _reserve++) {
              _create_notification(plane.type, plane.use, timestampToDate(parsedData.reserve[_reserve].dateStart), timestampToDate(parsedData.reserve[_reserve].dateEnd));
            }
          } catch(e) {
            alert(e.message);
          }
        });
      }

      res.resume(); // libère de la mémoire
    }).on("error", (e) => {
      alert("Got error: " + e.message);
    });
  }
}

function _create_notification(type, use, start, end) {
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
  time.innerHTML = "<span>" + start + "</span><span>" + end + "</span>";

  id.classList.add("ids");
  name.classList.add("names");
  time.classList.add("times");

  let container = document.createElement("div");
  container.classList.add("notifications");
  container.style.cursor = "pointer";
  container.setAttribute("title", "Cliquez sur une réservation pour la modifier");

  container.appendChild(id);
  container.appendChild(name);
  container.appendChild(time);

  notificationContainer.style.opacity = "0";
  notificationContainer.appendChild(container);
  Velocity(notificationContainer, {opacity: "1"}, {duration: 500});

  /* clic droit */
  container.addEventListener("click", () => {
    alert("ok");
  });
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