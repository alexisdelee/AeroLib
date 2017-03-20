function Keypad() {
  this.active = false;
  this.target = null;
  this.cancel = null;
  this.confirm = null;

  if(typeof Keypad.initialized == "undefined") {
    Keypad.prototype.start = (container) => {
      if(this.active) {
        this.destroy();
      }

      this.target = container;
      this.active = true;

      this.initialize();
    }

    Keypad.prototype.initialize = () => {
      let parent = this.createParent();

      let digitals = new Array(16);
      digitals = digitals.fill(null)
      .map((value, index) => {
        return index;
      });

      for(let digital = 0; digital < 16; digital++) {
        let link = document.createElement("img");

        link.setAttribute("src", "res/case/null.png");
        link.setAttribute("data-digital", digital);

        this.stylize(link);
        parent.appendChild(link);

        if((digital + 1) % 4 == 0) {
          let br = document.createElement("br");
          parent.appendChild(br);
        }
      }

      for(let digital = 0; digital < 12; digital++) {
        let randomNb = this.randomCase(0, digitals.length - 1);
        let randomCase = digitals.splice(randomNb, 1);

        let target = document.querySelector("img[data-digital=\"" + randomCase + "\"]");
        target.style.cursor = "pointer";

        if(digital <= 9) {
          target.setAttribute("src", "res/case/" + digital + ".png");
          target.textContent = digital;

          target.addEventListener("click", (e) => {
            this.listen(e.target.textContent);
          });
        } else if(digital == 10) {
          target.setAttribute("src", "res/case/reset.png");

          target.addEventListener("click", (e) => {
            if(this.cancel !== null)
              this.cancel();
          });
        } else {
          target.setAttribute("src", "res/case/ok.png");

          target.addEventListener("click", (e) => {
            if(this.confirm !== null)
              this.confirm();
          });
        }
      }
    }

    Keypad.prototype.createParent = () => {
      let parent = document.createElement("div");

      parent.setAttribute("class", "keypad");
      parent.style.left = (this.target.offsetLeft + 13) + "px";
      parent.style.top = (this.target.offsetTop + 60) + "px";

      document.querySelector("body").appendChild(parent);

      return document.querySelector(".keypad");
    }

    Keypad.prototype.stylize = (tag) => {
      tag.style.position = "relative";
      tag.style.bottom = "-2px";
      tag.style.display = "inline-block";
      tag.style.height = "50px";
      tag.style.width = "50px";
      tag.style.margin = "2px";
      tag.style.textDecoration = "none";
      tag.style.fontSize = "20px";
      tag.style.fontWeight = "bold";
      tag.style.textAlign = "center";
      tag.style.lineHeight = "50px";
      tag.style.borderRadius = "3px";
      tag.style.background = "#1B1E24";
      tag.style.color = "#4E7CAD";
    }

    Keypad.prototype.randomCase = (min, max) => {
      return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    Keypad.prototype.listen = (_case, callback) => {
      this.target.value += parseInt(_case);
      // if(this.callback != null) this.callback();
    }

    Keypad.prototype.destroy = () => {
      let target = document.querySelector(".keypad");
      target.parentNode.removeChild(target);

      this.active = false;
    }
  }
}