/* login */
const request = require("request");

let login = new Autotab();
login.listen(document.querySelector("#login .code_input"), 1, (keys, els) => {
  request.post({
    headers: {"Content-type": "application/x-www-form-urlencoded"},
    url: "aen.fr:8080/login.php",
    form: {"email": "root@aen.fr", "password": keys}
  }, (err, response, body) => {
    if(response.statusCode == 200) {
      if(body == "true") {
        Velocity(document.body, {opacity: "0"}, {duration: 800, progress: (el, complete) => {
          if(complete == 1) window.location.href = "calendar.html?root=true";
        }});
      } else {
        alert("Exception: error password");
      }
    }
  });


  login.clear(els);
});