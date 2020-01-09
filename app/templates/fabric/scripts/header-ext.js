$(document).ready(function() {

	// Header collapse and extend
	var cbpAnimatedHeader = (function() {
		var docElem = document.querySelector("#page"),
			header = $('header'),
			didScroll = false,
			changeHeaderOn = 175;

		function init() {
			docElem.addEventListener('scroll', function(event) {
				if (!didScroll) {
					didScroll = true;
					setTimeout(scrollPage, 50);
				}
			}, false);
		}

		function scrollPage() {
			var sy = scrollY();
			if (sy >= changeHeaderOn) header.removeClass('extend');
			else header.addClass('extend');
			didScroll = false;
		}

		function scrollY() { return docElem.scrollTop; }

		init();

	})();

});
