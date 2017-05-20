function Request() {
  this.request = null;

  if(typeof Request.initialized === "undefined") {
    Request.prototype.send = (callback) => {
      this.request = new XMLHttpRequest();

      this.request.onreadystatechange = () => {
        if(this.request.readyState == 4 && this.request.status == 200) {
          callback(this.request.responseText);
        }
      };
    }

    Request.prototype.post = (file, params, callback, asynchronous = true) => {
      this.send(callback);

      this.request.open("POST", file, asynchronous);
      this.request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      this.request.send(params);
    }

    Request.prototype.get = (file, params, callback, asynchronous = true) => {
      this.send(callback);

      this.request.open("GET", file + (file != undefined ? "?" + params : ""), asynchronous);
      this.request.send(send);
    }
  }
}