var table = null;

function choosePrestation(child) {
  let parent = document.querySelectorAll(".prestation");

  for(let p = 0; p < parent.length; p++) {
    parent[p].style.display = "none";
  }

  child.style.display = "";
  document.querySelector("button").style.display = "";

  table = child.id; // stock id
}

document.querySelector("select").addEventListener("change", (e) => {
  let target = e.target;
  let table = target.selectedOptions[0].dataset.nexttable
  let type = target.value;
  let id = target.selectedOptions[0].dataset.id

  if(type !== "default") {
    db("load", "table=" + table + "&column=" + type + "&id=" + id, "1 = 1", (response) => {
      createOptions(table, response);
    });
  }
});

function db(type, data, where, callback = null) {
  let request = new XMLHttpRequest();
  request.onreadystatechange = function(){
    if(request.readyState == 4 && request.status == 200){
      if(callback !== null) callback(request.responseText);
    }
  }

  request.open("POST", "loadTable.php");
  request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  request.send("type=" + type + "&" + data + "&where=" + where);
}

function createOptions(table, content) {
  let options = content.split("\n");
  options.splice(options.length - 1, 1); // supprimer la case vide à la fin du tableau
  
  let selectDom = document.querySelector("#category select");
  let selectDomChildren = selectDom.options;
  for(let o = 0, selectDomLength = selectDomChildren.length; o < selectDomLength; o++) {
    selectDom.removeChild(selectDomChildren[0]); // supprimer les anciens options
  }

  let optionDom = document.createElement("option");
  optionDom.setAttribute("value", "default");
  optionDom.textContent = "Quelle période";
  selectDom.appendChild(optionDom);

  for(let option of options) {
    let values = option.split(":");
    let optionDom = document.createElement("option");

    optionDom.setAttribute("value", "default");
    optionDom.setAttribute("data-id", values[0]);
    optionDom.setAttribute("data-nexttable", "acoustic");
    optionDom.textContent = values[1];

    selectDom.appendChild(optionDom);
  }
}

function getValues() {
  if(table === "reservoir") {
    let option = document.querySelector("#" + table + " select").selectedOptions[0];
    let column = option.value;
    let value = option.dataset.id;

    // let inputs = document.querySelectorAll("#" + table + " .row input");
    let input = document.querySelector("#" + table + " .row input");

    if(column !== "default" && value !== undefined && !isNaN(parseInt(input.value, 10)) && parseInt(input.value, 10) > 0) {
      db("update", "table=plane" + "&" + column + "=" + value, "idPlane = 1", (response) => {
        if(table === "reservoir") {
          create_receipt("Avitaillement", "Achat de " + input.value + " litres de " + option.textContent, 1, parseInt(input.value), ["reservoir", value]);
        }
      });
    }
  }
}

function create_receipt(prestation, explication, idPlane, quantite, linkToTable) {
  let user;
  let date = new Date();

  db_query("SELECT idUser FROM `user` WHERE email = \"$email\"", (response) => {
    user = response;
    
    if(prestation.toLowerCase() === "avitaillement") {
      db_query("SELECT cost" + linkToTable[0].capitalizeFirstLetter() + ", tva" + linkToTable[0].capitalizeFirstLetter() + " FROM " + linkToTable[0] + " WHERE id" + linkToTable[0].capitalizeFirstLetter() + " = " + linkToTable[1], (response) => {
        if(response != "null") {
          let prices = response.split(":");

          db_query("SELECT idPlane FROM `receipt` WHERE idUser = " + user + " ORDER BY idPlane DESC LIMIT 1", (response) => {
            if(response != "null") {
              db("insert", "table=receipt&prestation=" + prestation + "&explication=" + explication + "&dateOfDay=" + (date.getTime() / 1000) + "&idUser=" + user + "&idPlane=" + response + "&costReceipt=" + (quantite * parseFloat(prices[0])) + "&tvaReceipt=" + (quantite * parseFloat(prices[1])), null, () => {
                let span = document.createElement("span");

                span.innerHTML = "Vous avez été débité de " + (quantite * parseFloat(prices[0]) + quantite * parseFloat(prices[1])) + " euro(s).";
                popup.manager.open(span);
              });
            }
          });
        }
      });
    }
  });
}

function db_query(query, callback) {
  let request = new XMLHttpRequest();
  request.onreadystatechange = function(){
    if(request.readyState == 4 && request.status == 200){
      if(callback !== null) callback(request.responseText);
    }
  }

  request.open("POST", "loadTable.php");
  request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  request.send("type=query" + "&query=" + query);
}

String.prototype.capitalizeFirstLetter = function() {
  return this.charAt(0).toUpperCase() + this.slice(1);
}