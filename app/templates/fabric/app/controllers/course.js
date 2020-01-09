'use strict';

/* Controllers */

angular.module('fabric')
    .controller('courseCtrl', ['$scope', function($scope) {

		// Environment
    	$scope.app.name = "Course - Jigsaw Learning Centre";
		$scope.env.header.ext.default = true;
		$scope.env.header.ext.allow = true;

		// Header collapse and extend
		var cbpAnimatedHeader = (function() {
			var docElem = document.querySelector("#page"),
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
				var sy = scrollY(),
				header = $('header');
				if (sy >= changeHeaderOn) header.removeClass('extend');
				else header.addClass('extend');
				didScroll = false;
			}

			function scrollY() { return docElem.scrollTop; }

			init();

		})();

    }]);
