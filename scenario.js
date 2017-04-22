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

let send = document.querySelectorAll(".send");
if(send != null) {
  for(let button of send) {
    button.addEventListener("click", (e) => {
      let data = "";

      if(e.target.dataset.prestation === "reservoir") {
        let container = document.querySelector(".reservoir").parentNode;

        data = "&product=" + encodeURIComponent(container.querySelector("select").selectedOptions[0].innerHTML);
        data += "&quantite=" + (isNaN(parseInt(container.querySelector("input").value)) ? 0 : container.querySelector("input").value);
      } else if(e.target.dataset.prestation === "nettoyage") {
        data = "";
      } else if(e.target.dataset.prestation === "stationnement") {
        let container = document.querySelector(".area").parentNode;
        let manager = new MomentUtils();

        data = "&zone=" + encodeURIComponent(container.querySelector("select").selectedOptions[0].value);
        data += "&timetable=" + encodeURIComponent(container.querySelector("select").selectedOptions[0].innerHTML);
        data += "&duration=" + container.querySelector("input").value;
      }

      window.location.href = e.target.dataset.href + data;
    });
  }
}

let valid = document.querySelector("#accept");
if(valid !== null) {
  valid.addEventListener("click", () => {
    let manager = new MomentUtils();
    let data = decodeURIComponent(window.location.search.substring(1));
    data += "&action=" + manager.timestamp(moment(document.querySelector("#date").value, "DD/MM/YYYY HH:mm")._d);

    let request = new Request();
    request.post("prestations.php", data, (response) => {
      response = JSON.parse(response);
      if(response.status == 200) {
        window.location.href = "escale.php";
      } else {
        popup.manager.open("<span style=\"color: #A61835\">" + decodeURIComponent(response.message) + "</span>");
      }
    });
  });
}