const mysql_manager = require("mysql");

class Mysql {
  constructor() {
    this.connection = null;
  }

  getSharedInstance() {
    this.connection = mysql_manager.createConnection({
      // host: "192.168.80.129",
      host: "localhost",
      user: "root",
      password: "",
      database: "aerodrome"
    });

    this.connection.connect();
  }

  test() {
    return this.connection;
  }

  exec(request, params, callback = null) {
    if(this.connection == null) {
      this.getSharedInstance();
    }

    this.connection.query(request, params, (err, results, fields) => {
      if(err) {
        alert(err);
        return;
      }

      if(callback != null) {
        callback(results, fields);
      }
    });
  }

  free() {
    if(this.connection != null) {
      this.connection.end();
    }
  }
}