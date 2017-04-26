let getNumberOfDays = Symbol(); // on peut simuler une méthode privée avec un symbol
let getListOfDays = Symbol();
let createMonthsSelector = Symbol();

class Calendar {
  constructor(month, year) {
    this.month = month;
    this.year = year;

    this[getNumberOfDays] = function(month, year) {
      var isLeap = ((year % 4) == 0 && ((year % 100) != 0 || (year % 400) == 0));
      return [31, (isLeap ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month];
    }

    this.months = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];

    this[getListOfDays] = function() {
      let firstday = (new Date(this.year, this.month, 1)).getDay();
      firstday = ((firstday + 6) % 7); // on replace dans le bon sens les jours (0 pour lundi et 6 pour dimanche)
      let parent = document.querySelector("form");

      for(let index = 0, monthLen = this[getNumberOfDays](this.month, this.year) + firstday; index < monthLen; index++) {
        let day = index - firstday + 1;

        let span = document.createElement("span");
        span.textContent = day;

        let label = document.createElement("label");
        label.setAttribute("data-day", index);

        label.classList.add("day");
        if(day < 1) label.classList.add("invalid");

        label.appendChild(span);
        parent.appendChild(label);

        document.querySelector(".calendar h1").textContent = this.months[this.month] + " " + this.year;
      }
    }

    this[createMonthsSelector] = function() {
      let parent = document.querySelector("#months");

      document.querySelector(".js-value").textContent = this.months[this.month];

      for(let month of this.months) {
        let input = document.createElement("input");
        input.setAttribute("type", "radio");
        input.setAttribute("id", month.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase());
        input.setAttribute("name", "dropp");
        input.setAttribute("class", "_month");
        input.setAttribute("value", month);

        let label = document.createElement("label");
        label.setAttribute("for", month.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase());
        if(this.months[this.month] == month) label.classList.add("js-open");

        label.innerHTML = month
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

      let labels = document.querySelectorAll("label");
      for(let label of labels) {
        label.addEventListener("click", () => {
          for(let _label of labels) {
            _label.classList.remove("js-open");
          }

          label.classList.add("js-open");
          monthContainer.classList.remove("js-open");
          monthsContainer.classList.remove("js-open");
        });
      }

      let _months = document.querySelectorAll("._month");
      for(let month of _months) {
        month.addEventListener("change", () => {
          let value = document.querySelector("input:checked").value;
          document.querySelector(".js-value").textContent = value;
        });
      }
    }
  }

  load() {
    this[createMonthsSelector]();
    this[getListOfDays]();
  }
}