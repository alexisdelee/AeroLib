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

      if(e.target.dataset.prestation === "simple_services") {
        let container = document.querySelector(".simple_services").parentNode;

        data = "&title=" + encodeURIComponent(container.querySelector("select").selectedOptions[0].textContent.toLowerCase());
        data += "&duration=" + (isNaN(parseInt(container.querySelector("input[type=\"text\"]").value)) ? 0 : container.querySelector("input[type=\"text\"]").value);
        data += "&goodWeight=" + (container.querySelector("input[type=\"checkbox\"]").checked ? 1 : 0);
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

    let manager = new MomentUtils();
    let data = decodeURIComponent(window.location.search.substring(1));
    data += "&action=" + manager.timestamp(moment(action, "DD/MM/YYYY HH:mm")._d);

    if(getQueryVariable("prestation") == "simple_service") {
      sendPrestation(data, (response) => {
        response = JSON.parse(response);

        if(response.status == 200) {
          window.location.href = "aeroclub.php";
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