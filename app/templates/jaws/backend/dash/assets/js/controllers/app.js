'use strict';

/* Controllers */

angular.module('jaws', ['ngSanitize'])
	.controller('AppCtrl', ['$scope', '$sce', '$http', '$window', '$timeout', '$interval', '$state', 'defaultSettings', 'feed', function($scope, $sce, $http, $window, $timeout, $interval, $state, defaultSettings, feed) {

	// User Globals
	$scope.user = _JAWS_USER;

	// Helpers
	function serialize(obj, prefix) {
		var str = [], p;
		for(p in obj) {
			if (obj.hasOwnProperty(p)) {
				var k = prefix ? prefix + "[" + p + "]" : p, v = obj[p];
				str.push((v !== null && typeof v === "object") ?
				serialize(v, k) :
				encodeURIComponent(k) + "=" + encodeURIComponent(v));
			}
		}
		return str.join("&");
	};

	// Quickview
	$scope.quickview = defaultSettings.hasOwnProperty("quickview") ? JSON.parse(defaultSettings.quickview) : false;

	$scope.quickview_toggle = function () {
		$scope.quickview = !$scope.quickview;

		$http.get(_JAWS_PATH_API + 'settings.set.dash?' + serialize({
			quickview: $scope.quickview
		}, 'settings')).then(function(response){});
	};

	// Feed
	$scope.feed_status = feed.status;
	$scope.feed_message = feed.message;

	$interval(function(scope) {
		$scope.feed_status = feed.status;
		$scope.feed_message = feed.message;
	}, 1000);

	// Activity Feed
	$scope.activities = [];

	function highlightActivity(str, cls = "info") {
		str = str.replace('[[' , '<b class="text-' + cls + '">');
		str = str.replace(']]', '</b>');
		str = str.replace('[', '<b>');
		return str.replace(']', '</b>');
	}

	function parseActivity(e) {
		var acts = JSON.parse(e.data);

		$scope.$apply(function() {
			acts.forEach(function(act, i) {
				try {

					var msgs = act.messages;
					var parsedAct = {
						messages: [],
						data: act.data,
						epoch: parseInt(act.epoch),
						tags: act.tags,
						cls: (typeof act.data.c !== 'undefined') ? act.data.c : 'info'
					};

					msgs.forEach(function(msg, i) {
						var parsedMsg = {};

						// String msg
						if(typeof msg === 'string' || msg instanceof String) {
							parsedMsg.message = $sce.trustAsHtml(highlightActivity(msg, parsedAct.cls));
							parsedMsg.type = "string";
						}

						// List msg
						else if (Object.prototype.toString.call(msg) === '[object Array]') {
							var marr = [];
							for(var count = 0; count < msg.length; count++) marr.push($sce.trustAsHtml(highlightActivity(msg[count], parsedAct.cls)));
							parsedMsg.message = marr;
							parsedMsg.type = "list";
						}

						// Tabular msg
						else {
							var parr = [];
							for (var key in msg) parr.push({
								key: $sce.trustAsHtml(highlightActivity(key, parsedAct.cls)),
								value: $sce.trustAsHtml(highlightActivity(msg[key], parsedAct.cls))
							});
							parsedMsg.message = parr;
							parsedMsg.type = "table";
						}

						// Add to Act.messages
						parsedAct.messages.push(parsedMsg);
					});

					// Add to Acts
					$scope.activities.push(parsedAct);

				}
				catch (err) {}
			});
		});

		// Desktop Notification
		/*if ("Notification" in window) {
  			if (Notification.permission === "granted") {
	        	var options = {
					title: "JAWS - Jigsaw Academy",
	                body: "This is the body of the notification",
	                icon: 'https://www.jigsawacademy.com/jaws/media/jaws/frontend/images/favicon.png',
	                dir : "ltr"
	             };

	          	var notification = new Notification("Hi there",options);
			}
		}*/

		// Return counter
		return $scope.activities[$scope.activities.length - 1].epoch;
	}

	// Subscribe to activity feed
	try {
		if (defaultSettings.hasOwnProperty("activity")) {
			if (defaultSettings.activity.length > 0) feed.subscribe('activity', -1, defaultSettings.activity, parseActivity);
		}
	}
	catch(err) {
		$('body').pgNotification({
			style: 'bar',
			message: 'Unable to connect to live feed - ' + e.message + '.',
			position: 'top',
			timeout: 0,
			type: 'danger'
		}).show();
	}

}]);
