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

    Request.prototype.post = (file, params, callback) => {
      this.send(callback);

      this.request.open("POST", file);
      this.request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      this.request.send(params);
    }
  }
}