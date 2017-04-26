// debug
("load".split(" ")).forEach((e) => {
  window.addEventListener(e, () => {
    let calendar = new Calendar(3, 2017);
    calendar.load();
  }, false);
});
// debug

/* const http = require("http");
const mysql = require("mysql");

var connection = mysql.createConnection({
  host: "localhost",
  user: "root",
  password: "",
  database: "aerodrome"
});

connection.connect();

connection.query("SELECT `type`, `use` FROM `privateplane`", (err, results, fields) => {
  if(err) throw err;

  for(let result of results) {
    http.get("http://localhost/aerodrome/services/getStatusProperties.php?type=" + result.type + "&start=1493071200&end=1493157599", (res) => {
      const { statusCode } = res;
      const contentType = res.headers["content-type"];

      if(statusCode != 200) {
        res.setEncoding("utf8");
        let rawData = "";
        res.on("data", (chunk) => { rawData += chunk; });
        res.on("end", () => {
          try {
            const parsedData = JSON.parse(rawData);

            for(let _reserve of parsedData.reserve) {
              console.log("[" + parsedData.type + "] " + timestampToDate(_reserve.dateStart) + "|" + timestampToDate(_reserve.dateEnd));
            }
          } catch(e) {
            console.error(e.message);
          }
        });
      }

      res.resume(); // libère de la mémoire
    }).on("error", (e) => {
      console.error("Got error: " + e.message);
    });
  }
});

connection.end();

function timestampToDate(unix_timestamp) {
  let date = new Date(unix_timestamp * 1000);
  let hours = date.getHours();
  let minutes = "0" + date.getMinutes();
  let secondes = "0" + date.getSeconds();

  return hours + ":" + minutes.substr(-2) + ":" + secondes.substr(-2);
} */