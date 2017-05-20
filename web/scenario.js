let selectPlane = document.querySelector("#matricules");
if(selectPlane !== null) {
  selectPlane.addEventListener("change", (e) => {
    let matricule = e.target.selectedOptions[0].textContent;
    if(matricule.length == 8) {
      // ajout d'un paramètre GET dans l'url
      let key = encodeURIComponent("matricule");
      let value = encodeURIComponent(matricule);

      let s = document.location.search; // simulaire à PHP_URL_QUERY
      let kvp = key + "=" + value;
      let regexp = new RegExp("(&|\\?)" + key + "=[^\&]*");

      s = s.replace(regexp, "$1" + kvp);
      if(!RegExp.$1) {s += (s.length > 0 ? "&" : "?") + kvp;};

      window.location.href = document.location.pathname + s;
    }
  });
}

let options = document.querySelectorAll("input[type=\"radio\"");
for(let option of options) {
  option.addEventListener("click", (e) => {
    if(e.target.id == "register") {
      document.querySelector("#subscribe").style.display = "block";
    } else {
      document.querySelector("#subscribe").style.display = "none";
    }
  });
}

function changeMatricule() {
  let url = window.location.href.split("?");
  if(url.length >= 2) {
    let prefix = encodeURIComponent("matricule") + "=";
    let params = url[1].split(/[&;]/g);

    for(let index = params.length - 1; index >= 0; index--) {
      if(params[index].lastIndexOf(prefix, 0) !== -1) {
        params.splice(index, 1);
      }
    }

    window.location.href = url[0] + (params.length > 0 ? "?" + params.join("&") : "");
  } else {
    window.location.href = url;
  }
}

let new_plane = document.querySelector("#newplane");
if(new_plane !== null) {
  new_plane.addEventListener("click", (e) => {
    let children = Array.prototype.slice.call(e.target.parentNode.parentNode.childNodes).filter((els) => {
      return els.nodeName === "TD";
    }).map((els) => {
      if(els.childNodes.length == 1) {
        return els.childNodes[0].value;
      } else {
        return els.childNodes[1].selectedOptions[0].innerHTML;
      }
    });


    children.pop(); // on supprime le bouton de validation

    let _id = ["matricule", "length", "width", "mass", "model", "acoustic"];
    let plane = children.reduce((result, item, index) => {
      result[_id[index]] = item;
      return result;
    }, {}); // on convertit le tableau en objet avec id

    let request = new Request();
    request.post("addPlane.php", "plane=" + JSON.stringify(plane), (response) => {
      if(response === "ok") {
        window.location.reload();
      } else {
        popup.manager.open("<span style=\"color: #A61835\">" + response + "</span>");
      }
    });
  });
}

let prestations = document.querySelectorAll(".tabs");
for(let prestation of prestations) {
  let link = prestation.querySelector("a");
  link.addEventListener("click", (e) => {
    e.preventDefault();

    let old = document.querySelector(".open");
    if(old !== null) {
      old.classList.remove("open");
    }

    e.target.parentNode.parentNode.classList.add("open");
  });
}

let confirm = document.querySelector("#confirm");
if(confirm != null) {
  confirm.addEventListener("click", (e) => {
    window.location.href = "escale.php?prestation=atterrissage&type=confirmation";
  });
}


let send = document.querySelectorAll(".send");
if(send != null) {
  for(let button of send) {
    button.addEventListener("click", (e) => {
      let data = "";

      if(e.target.dataset.prestation === "avitaillement") {
        let container = document.querySelector(".avitaillement").parentNode;

        data = "&product=" + encodeURIComponent(container.querySelector("select").selectedOptions[0].value);
        data += "&quantite=" + (isNaN(parseInt(container.querySelector("input").value)) ? 0 : container.querySelector("input").value);
      } else if(e.target.dataset.prestation === "nettoyage") {
        data = "";
      } else if(e.target.dataset.prestation === "atterrissage") {
        let container = document.querySelector(".landing").parentNode;
        let manager = new MomentUtils();

        data = "&zone=" + encodeURIComponent(container.querySelectorAll("select")[1].selectedOptions[0].value);
        data += "&timetable=" + encodeURIComponent(container.querySelectorAll("select")[0].selectedOptions[0].innerHTML);
        data += "&timetable_area=" + encodeURIComponent(container.querySelectorAll("select")[1].selectedOptions[0].innerHTML);
        data += "&duration=" + (isNaN(parseInt(container.querySelector("input").value)) ? 0 : container.querySelector("input").value);
      }

      window.location.href = e.target.dataset.href + data;
    });
  }
}

function getQueryVariable(_param) { // pour récupérer les paramètres passés dans l'url
  let url = window.location.href;

  _param = _param.replace(/[\[\]]/g, "\\$&");
  let regex = new RegExp("[?&]" + _param + "(=([^&#]*)|&|#|$)"),
      results = regex.exec(url);

  if(!results) return null;
  else if(!results[2]) return "";
  else return decodeURIComponent(results[2].replace(/\+/g, " "));
}

let valid = document.querySelector("#accept");
if(valid !== null) {
  valid.addEventListener("click", () => {
    let action = document.querySelector("#date");
    action = (action == null || action.value == "" ? "01/01/1970 01:00" : action.value);

    let pattern = new RegExp("([0-9]{2}/){2}[0-9]{4} [0-9]{2}:[0-9]{2}", "g");
    if(!pattern.test(action)) {
      popup.manager.open("<span style=\"color: #A61835\">Veuillez rentrer une date valide</span>");
      return;
    }

    let manager = new MomentUtils();
    let data = decodeURIComponent(window.location.search.substring(1));
    let prestation_date = moment(action, "DD/MM/YYYY HH:mm")._d;
    data += "&action=" + manager.timestamp(prestation_date);

    if(prestation_date == "Invalid Date") {
      popup.manager.open("<span style=\"color: #A61835\">Veuillez rentrer une date valide</span>");
      return;
    }

    let now = new Date();

    if(getQueryVariable("timetable") == "Semaine") {
      if(prestation_date.getDay() == 0 || prestation_date.getDay() == 6) {
        data = data.replace(/(timetable=).*?(&)/, "$1" + "Week-end/JF" + "$2");
      } else if(moment(moment(prestation_date).format("DD-MM-YYYY"), "DD-MM-YYYY").isFerie()) {
        data = data.replace(/(timetable=).*?(&)/, "$1" + "Week-end/JF" + "$2");
      }
    }

    if(getQueryVariable("prestation") == "atterrissage") {
      if(getQueryVariable("type") != null) {
        sendPrestation(data, (response) => {
          response = JSON.parse(response);
          if(response.status == 200) {
            popup.manager.open("<span>" + decodeURIComponent(response.message) + "</span>");
          } else {
            popup.manager.open("<span style=\"color: #A61835\">" + decodeURIComponent(response.message) + "</span>");
          }
        });
      } else {
        sendPrestation(data, (response) => {
          response = JSON.parse(response);
          if(response.status == 200) {
            data = data.replace(/prestation=atterrissage/g, "prestation=stationnement");
            sendPrestation(data, (response) => {
              response = JSON.parse(response);
              if(response.status == 200) {
                window.location.href = "escale.php";
              } else {
                popup.manager.open("<span style=\"color: #A61835\">" + decodeURIComponent(response.message) + "</span>");
              }
            });
          } else {
            popup.manager.open("<span style=\"color: #A61835\">" + decodeURIComponent(response.message) + "</span>");
          }
        });
      }
    } else {
      sendPrestation(data, (response) => {
        response = JSON.parse(response);
        if(response.status == 200) {
          window.location.href = "escale.php";
        } else {
          popup.manager.open("<span style=\"color: #A61835\">" + decodeURIComponent(response.message) + "</span>");
        }
      });
    }
  });
}

function sendPrestation(data, callback) {
  let request = new Request();
  request.post("prestations.php", data, (response) => {
    if(callback != undefined) callback(response);
  });
}