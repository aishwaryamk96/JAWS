angular.module('jaws')
	.service('feed', ['$http', '$timeout', function($http, $timeout) {

		var link = {};
		var feeds = {};
		var self = this;

		this.status = false;
		this.message = "unavailable";

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

		this.connect = function() {
			if (self.status) return;

			// Prep query
			self.message = "connecting";
			var _feeds = [];

			for(var feed in feeds) {
				if (!feeds.hasOwnProperty(feed)) continue;
				_feeds[feed] = {
					counter: feeds[feed].counter,
					data: feeds[feed].data
				}
			}

			// Establish connection
			link = new EventSource(_JAWS_PATH_WEB + "/webapi/feed/subscribe?nocache=" + Date.now() + '&' + serialize(_feeds, 'feeds'), { withCredentials: true });

			// Open notify
			link.onopen = function(e) {
				self.status = true;
				self.message = "connected";

				// Set Feed Handlers
				for(var feed in feeds) {
					if (!feeds.hasOwnProperty(feed)) continue;
					link.addEventListener("activity", function(e) {
						try { feeds[feed].counter = feeds[feed].handler(e); }
						catch (err) {}
					}, false);
				}
			};

			// Set Error Handler for EventSource
			link.onerror = function(e) {
				self.disconnect("reconnecting");
				$timeout(function() {self.connect();}, 5000);
			};

			// Set Close Handler for EventSource
			link.onclose = function(e) {
				self.disconnect();
			};
		};

		this.disconnect = function(msg = "unavailable") {
			try { link.close(); }
			catch(err) {}
			self.status = false;
			self.message = msg;
		};

		this.subscribe = function(name, counter, data, handler) {
			self.disconnect();

			feeds[name] = {
				counter: counter,
				data: data,
				handler: handler
			};

			self.connect();
		};

		this.unsubscribe = function(name) {
			self.disconnect();
			try { delete feeds[name]; }
			catch (err) {}
			self.connect();
		};

		return this;

}]);
