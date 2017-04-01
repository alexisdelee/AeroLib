function MomentUtils() {
  if(typeof MomentUtils.initialized === "undefined") {
    MomentUtils.prototype.localeToUTC = (_date, zone) => {
      let france = moment.tz(moment(_date), zone);
      let london = france.clone().tz("Europe/London");

      if(london.format() === "Invalid date") {
        return null;
      } else {
        return london._d;
      }
    }

    MomentUtils.prototype.UTCToLocale = (_date, zone) => {
      let london = moment.tz(moment(_date), zone);
      let france = london.clone().tz("Europe/London");

      if(france.format() === "Invalid date") {
        return null;
      } else {
        return france._d;
      }
    }

    MomentUtils.prototype.timestamp = (_date) => {
      return parseInt(_date.getTime() / 1000);
    }

    MomentUtils.prototype.limiter = (_date, limit, higher) => {
      if(higher) {
        if(this.timestamp(limit) < this.timestamp(_date)) {
          return true;
        }
      } else {
        if(this.timestamp(limit) > this.timestamp(_date)) {
          return true;
        }
      }

      return false;
    }

    MomentUtils.prototype.getType = (_date) => {
      let timestamp = this.timestamp(_date);

      for(let day of moment.utc().getFerieList(_date.getFullYear())){
        let input = day.date._d.toString();
        input = (moment(input).unix());

        if(timestamp >= input && timestamp < input + 24 * 3600){
          return "public holiday";
        }
      }

      if(_date.getDay() >= 1 && _date.getDay() <= 5) {
        return "week";
      } else {
        return "week-end";
      }
    }
  }
}

Date.prototype.timestamp = function() {
  return parseInt(this.getTime() / 1000);
}

Date.prototype.localeToUTC = function(zone) {
  let geo = moment.tz(this, zone);
  let london = geo.clone().tz("Europe/London");

  if(london.format() === "Invalid date") {
    return null;
  } else {
    return london._d;
  }
}

Date.prototype.UTCToLocale = function(zone) {
  let london = moment.tz(this, "Europe/London");
  let geo = london.clone().tz(zone);

  if(geo.format() === "Invalid date") {
    return null;
  } else {
    return geo._d;
  }
}

Date.prototype.getType = function() {
  let timestamp = this.timestamp();

  for(let day of moment.utc().getFerieList(this.getFullYear())){
    let input = day.date._d.toString();
    input = (moment(input).unix());

    if(timestamp >= input && timestamp < input + 24 * 3600){
      return "public holiday";
    }
  }

  if(this.getDay() >= 1 && this.getDay() <= 5) {
    return "week";
  } else {
    return "week-end";
  }
}