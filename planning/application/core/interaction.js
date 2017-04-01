let active = -1;

window.addEventListener("keydown", (e) => {
  if(e.keyCode == 27){
    window.close();
  } else if(e.keyCode == 82){
    window.location.reload();
  } else if(e.keyCode == 32){
    if(active){
      Velocity(document.querySelector("header"), {height: "25%"}, {duration: "250"});
      Velocity(document.querySelectorAll("header h3, header h1"), {opacity: "0.8"}, {duration: "250"});
      document.querySelector("header h3").textContent = "planning: avions et ULM";
      Velocity(document.querySelector(".score"), {fontSize: "40px", opacity: "0.6", color: "#1F2834"}, {duration: "250"});
    } else {
      Velocity(document.querySelector("header"), {height: "100%"}, {duration: "250"});
      Velocity(document.querySelectorAll("header h3, header h1"), {opacity: "0"}, {duration: "250"});
      Velocity(document.querySelector(".score"), {fontSize: "80px", opacity: "0.7", color: "#B63B4D"}, {duration: "250"});
    }

    active = ~active;
  }
});