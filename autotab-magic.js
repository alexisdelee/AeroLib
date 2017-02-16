var Autotab = {
  blocLen: 0,
  count: 0,
  keys: null,
  listen: function(bloc, callback) {
    Autotab.blocLen = bloc.length;
    Autotab.keys = new Array(length);

    for(var input of bloc) {
      input.addEventListener("keyup", function(e) {
        var value = parseInt(e.target.getAttribute("class"));

        if(!(e.keyCode ^ 37) && (value > 2)) {
          document.getElementsByClassName(value >> 1)[0].focus();
        } else if(!(e.keyCode ^ 39) && (value < (2 << (Autotab.blocLen - 1)))) {
          document.getElementsByClassName(value << 1)[0].focus();
        } else {
          if(e.target.value.length) {
            if((value << 1) ^ (2 << Autotab.blocLen)) {
              document.getElementsByClassName(value << 1)[0].focus();
            } else {
              e.target.blur();
            }

            if(!(Autotab.count & value)) {
              Autotab.count |= value;
            }

            Autotab.keys[Autotab.ulog2(value) - 1] = e.target.value;
          }
        }

        if(!(Autotab.count ^ ((2 << Autotab.blocLen) - 2))) {
          callback(Autotab.keys.join(""));
        }
      });
    }
  },
  ulog2: function(number) {
    var base = 0;

    while(number >>= 1) base++;
    return base;
  },
  clear: function(bloc) {
    Autotab.count = 0;
    for(var input of bloc) {
      input.value = "";
    }
  }
};