let now = new Date();
let calendar = new Calendar(now.getMonth(), now.getFullYear());
calendar.load();

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

/* gestion bouton générer fichier excel */

let generate = document.querySelector("#generate");
let monthContainer = document.querySelector(".dropp-header__title");
let yearContainer = document.querySelector(".spinner input");
let status = false;

generate.addEventListener("click", () => {
  status = !status; // on passe de false à true ou de true à false

  if(status) {
    let index = calendar.months.indexOf(monthContainer.textContent);

    if(~index) {
      index = index < 10 ? "0" + (index + 1) : (index + 1);
    }

    let request = new Request();
    request.post("generateExcel.php", "month=" + index + "&year=" + yearContainer.value, (response) => {
      response = JSON.parse(response);
      
      if(response.status == 200) {
        generate.innerHTML = "<a style=\"text-decoration: none; color: #FFF;\" href=\"" + response.message + "\" download=\"facture\">Télécharger la facture</a>";
        generate.style.background = "#61B2A7";
      }
    });
  } else {
    generate.innerHTML = "Générer un fichier excel";
    generate.style.background = "#4E7CAD";
  }
});