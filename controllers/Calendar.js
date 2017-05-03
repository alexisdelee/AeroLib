let getNumberOfDays = Symbol(); // on peut simuler une méthode privée avec un symbol
let getListOfDays = Symbol();
let createMonthsSelector = Symbol();

class Calendar {
  constructor(month, year) {
    this.month = month;
    this.year = year;

    this[getNumberOfDays] = function(month, year) {
      let isLeap = ((year % 4) == 0 && ((year % 100) != 0 || (year % 400) == 0));
      return [31, (isLeap ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month];
    }

    this.months = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];

    this[getListOfDays] = function() {
      let firstday = (new Date(this.year, this.month, 1)).getDay();
      firstday = ((firstday + 6) % 7); // on replace dans le bon sens les jours (0 pour lundi et 6 pour dimanche)
      
      let index = 0;
      let monthLen = this[getNumberOfDays](this.month, this.year) + firstday - 1;

      document.querySelector(".spinner input[type=\"number\"]").setAttribute("value", this.year);
    }

    this[createMonthsSelector] = function() {
      let parent = document.querySelector("#months");

      document.querySelector(".js-value").textContent = this.months[this.month];

      for(let month = 0, n = this.months.length; month < n; month++) {
        let input = document.createElement("input");
        input.setAttribute("type", "radio");
        input.setAttribute("id", this.months[month].normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase());
        input.setAttribute("name", "dropp");
        input.setAttribute("class", "_month");
        input.setAttribute("value", this.months[month]);
        input.setAttribute("data-id", month);

        let label = document.createElement("label");
        label.setAttribute("for", this.months[month].normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase());
        if(this.months[this.month] == this.months[month]) label.classList.add("js-open");

        label.innerHTML = this.months[month];
        label.appendChild(input);

        parent.appendChild(label);
      }

      let monthsContainer = document.querySelector("#months");
      let monthContainer = document.querySelector("#month");
      monthContainer.addEventListener("click", (e) => {
        e.preventDefault();
        monthContainer.classList.add("js-open");
        monthsContainer.classList.add("js-open");
      });

      let labels = document.querySelectorAll(".dropp label");
      for(let label = 0, n = labels.length; label < n; label++) {
        labels[label].addEventListener("click", () => {
          for(let _label = 0; _label < n; _label++) {
            labels[_label].classList.remove("js-open");
          }

          labels[label].classList.add("js-open");
          monthContainer.classList.remove("js-open");
          monthsContainer.classList.remove("js-open");
        });
      }

      let _months = document.querySelectorAll("._month");
      for(let month = 0, n = _months.length; month < n; month++) {
        _months[month].addEventListener("change", () => {
          let input = document.querySelector("input:checked");
          document.querySelector(".js-value").textContent = input.value;

          let id = parseInt(input.dataset.id);
          if(id != this.month) {
            this.month = id;
            this.load(false);
          }
        });
      }
    }
  }

  load(uniq = true) {
    if(uniq) this[createMonthsSelector]();
    this[getListOfDays]();
  }
}