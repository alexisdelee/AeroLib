// debug
const mysql = new Mysql();
const win = require("nw.gui").Window.get();

win.on("close", () => {
  mysql.free();
  win.close(true);
});
// debug

function getQueryVariable(_param) { // pour récupérer les paramètres passés dans l'url
  let url = window.location.href;

  _param = _param.replace(/[\[\]]/g, "\\$&");
  let regex = new RegExp("[?&]" + _param + "(=([^&#]*)|&|#|$)"),
      results = regex.exec(url);

  if(!results) return null;
  else if(!results[2]) return "";
  else return decodeURIComponent(results[2].replace(/\+/g, " "));
}

const root = getQueryVariable("root") == "true" ? true : false;
if(root) document.title += " [administrateur]";

function popup(request) {
  let popupContainer = document.querySelector("#popup");

  let header = document.createElement("header");
  popupContainer.appendChild(header);

  let _container = document.createElement("div");
  _container.classList.add("container");

  popupContainer.appendChild(_container);

  header.style.opacity = 0;
  _container.style.opacity = 0;

  Velocity(header, {opacity: "0.6"}, {duration: 500});
  Velocity(_container, {opacity: "1"}, {duration: 500});

  header.addEventListener("click", () => {
    Velocity(header, {opacity: "0"}, {duration: 500, progress: (els, complete) => {
      if(complete == 1) popupContainer.innerHTML = "";
    }});
    Velocity(_container, {opacity: "0"}, {duration: 500, progress: (els, complete) => {
      if(complete == 1) popupContainer.innerHTML = "";
    }});
  });

  /* on récupère les informations de la notification ici */

  mysql.exec("SELECT service.idService, aeroclub.idAeroclub, user.name, activity.title, service.dateStart, service.dateEnd, privateplane.idPrivatePlane FROM `receipt` "
           + "LEFT JOIN `service` ON receipt.idReceipt = service.idReceipt "
           + "LEFT JOIN `user` ON receipt.idUser = user.idUser "
           + "LEFT JOIN `aeroclub` ON service.idAeroclub = aeroclub.idAeroclub "
           + "LEFT JOIN `activity` ON aeroclub.idActivity = activity.idActivity "
           + "LEFT JOIN `privateplane` ON aeroclub.idPrivatePlane = privateplane.idPrivatePlane "
           + "WHERE privateplane.type = ? "
           + "AND service.dateStart = ? "
           + "AND service.dateEnd = ? "
           + "GROUP BY service.idService", [request.type, request.start, request.end], (results, fields) => {

    let result = results[0];
    let values = [
      {title: "Nom", value: result.name},
      {title: "Prestation", value: result.title},
      {title: "Date de début", value: result.dateStart},
      {title: "Date de fin", value: result.dateEnd},
      {title: "Avion utilisé", value: result.idPrivatePlane}
    ];
    let table = document.createElement("table");
    let dates_changed = [];

    let tr_header = document.createElement("tr");
    let tr_article = document.createElement("tr");

    for(let index = 0; index < 5; index++) {
      let th = document.createElement("th");
      let td = document.createElement("td");

      th.textContent = values[index].title;

      if(index == 4) {
        let select = document.createElement("select");

        for(let i = 0, n = request.planes.length; i < n; i++) {
          let option = document.createElement("option");

          option.textContent = request.planes[i].type;
          option.setAttribute("data-idplane", request.planes[i].id);

          if(i + 1 == request.id_plane) {
            option.setAttribute("selected", "selected");
          }

          select.appendChild(option);
        }

        td.appendChild(select);
      } else if(index == 2 || index == 3) {
        let _date = moment(new Date(values[index].value * 1000));
        td.textContent = _date.format("DD/MM/YYYY HH:mm:ss");
        td.setAttribute("contentEditable", "true");

        dates_changed[index - 2] = (new Date(_date._d)).getTime() / 1000;

        td.addEventListener("keydown", (e) => {
          if(e.keyCode == 13) {
            e.preventDefault();

            let date = moment(e.target.textContent, "DD/MM/YYYY HH:mm:ss")._d;

            if(date == "Invalid Date") {
              dates_changed[index - 2] = "null";
            } else {
              dates_changed[index - 2] = date.getTime() / 1000;
            }

            e.target.style.background = "#FFF";
            e.target.style.color = "#666B85";
          } else if(!~[48, 49, 50, 51, 52, 53, 54, 55, 56, 57, // numeric keys
                     96, 97, 98, 99, 100, 101, 102, 103, 104, 105, // numpad
                     58, // :
                     111, // divide
                     37, 38, 39, 49, // arrow
                     32, // space
                     8, 46] // backspace && delete
                    .indexOf(e.keyCode)) {
            e.preventDefault();
          } else {
            e.target.style.background = "#A61835";
            e.target.style.color = "#FFF";
          }
        });
      } else {
        td.textContent = values[index].value;
      }

      tr_header.appendChild(th);
      tr_article.appendChild(td);
    }

    table.appendChild(tr_header);
    table.appendChild(tr_article);
    _container.appendChild(table);

    table.style.marginLeft = -(table.offsetWidth / 2) + "px";

    // création et gestion des boutons modifier/supprimer

    let optionsContainer = document.createElement("span");
    optionsContainer.setAttribute("id", "options");
    optionsContainer.innerHTML = "<span class=\"left\">OK</span><span class=\"right\">Suppr</span>";
    _container.appendChild(optionsContainer);

    let submit = document.querySelector("#popup .container #options .left");
    submit.addEventListener("click", () => {
      let selected_plane = document.querySelector("#popup .container select");
      modify(request, {service: result.idService, plane: selected_plane.options[selected_plane.selectedIndex].dataset.idplane}, dates_changed, selected_plane.options[selected_plane.selectedIndex].text, request.use);
    });

    let clear = document.querySelector("#popup .container #options .right");
    clear.addEventListener("click", () => {
      let selected_plane = document.querySelector("#popup .container select");
      remove(request, {service: result.idService, plane: selected_plane.options[selected_plane.selectedIndex].dataset.idplane}, dates_changed, selected_plane.options[selected_plane.selectedIndex].text, request.use);
    });
  });
}

function modify(options, ids, dates, type, use) {
  request.post({
    headers: {"Content-type": "application/x-www-form-urlencoded"},
    url: "http://localhost/aerodrome/services/getStatusProperties.php",
    form: {"type": type, "start": dates[0], "end": dates[1]}
  }, (err, response, body) => {
    if(response.statusCode == 200) {
      mysql.exec("UPDATE `service` "
               + "LEFT JOIN `aeroclub` ON service.idAeroclub = aeroclub.idAeroclub "
               + "SET service.dateStart = ?, service.dateEnd = ?, aeroclub.idPrivatePlane = ? "
               + "WHERE service.idService = ? ", [dates[0], dates[1], ids.plane, ids.service]);

      close_popup(options);
    } else {
      alert("Plage horraire déjà occupée");
    } 
  });
}

function remove(options, ids, dates, type, use) {
  mysql.exec("DELETE FROM `service` WHERE idService = ?", [ids.service], () => {
    close_popup(options);
  });
}

function close_popup(options) {
  let popupContainer = document.querySelector("#popup");
  let header = popupContainer.querySelector("header");
  let _container = popupContainer.querySelector(".container");

  Velocity(header, {opacity: "0"}, {duration: 500, progress: (els, complete) => {
    if(complete == 1) popupContainer.innerHTML = "";
  }});

  Velocity(_container, {opacity: "0"}, {duration: 500, progress: (els, complete) => {
    if(complete == 1) {
      popupContainer.innerHTML = "";

      planning(options.start, options.end);
    }
  }});
}