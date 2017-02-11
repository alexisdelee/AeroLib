var mysql = require("mysql");
var connection = mysql.createConnection({
  port: 3306,
  host: "192.168.10.139",
  user: "staff",
  password: "staff",
  database: "aerodrome"
});

connection.connect();

connection.query("SELECT * from meteo;", (err, results, fields) => {
  if(err) throw err;
  console.log(results);
});

connection.end();