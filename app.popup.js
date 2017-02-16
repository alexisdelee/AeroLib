var popup = popup || {};

popup.manager = (function(self){
	/**
	 * param(msg): tag
	 * return {boolean}
	 */
	self.open = function(msg){
		var cd_popup = document.getElementsByClassName('cd-popup')[0];
		cd_popup.classList.add('is-visible');

		var container = document.getElementById('cd-popup-paragraph');
		container.innerHTML = msg.innerHTML;

		return false;
	};

	/**
	 * param(el): tag
	 * return {boolean}
	 */
	self.close = function(el){
		var cd_popup = document.getElementsByClassName('cd-popup')[0];

		if(el == cd_popup){
			cd_popup.classList.remove('is-visible');
		}

		return false;
	};

	return self;
})(popup || {});

document.addEventListener('keydown', function(e){
	if(e.keyCode == 27){
		popup.manager.close(document.getElementsByClassName('cd-popup')[0]);
	}
});