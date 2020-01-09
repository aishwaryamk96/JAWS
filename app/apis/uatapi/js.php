(function(w) {
	var d = w.document;
	var x, wd, ht, swd, sht;
	// Initialize XMLHttpRequest object
	if (w.XMLHttpRequest) {
		x = new XMLHttpRequest();
	}
	else {
		x = new ActiveXObject("Microsoft.XMLHTTP");
	}
	// GET wrapper
	var g = function(u,h,c) {
		x.onreadystatechange = function() {
			if (this.readyState < 4) {
				return;
			}
			c({status: this.status, data: this.responseText});
		}
		x.open("GET", u, true);
		for (var k in h) {
			x.setRequestHeader(k, h[k]);
		}
		x.send();
	}
	// POST wrapper
	var p = function(u, h, d, c) {
		x.onreadystatechange = function() {
			if (this.readyState < 4) {
				return;
			}
			c({status: this.status, data: this.responseText});
		}
		x.open("POST", u, true);
		for (var k in h) {
			x.setRequestHeader(k, h[k]);
		}
		var da = [];
		for (var k in d) {
			da.push(k+"="+d[k]);
		}
		x.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		x.send(da.join("&"));
	}
	// Cookie wrapper
	var c = {
		// Get cookie wrapper
		g: function(n) {
			n += "=";
			var dc = decodeURIComponent(d.cookie).split(";");
			for (var i = 0; i < dc.length; i++) {
				var c = dc[i].trim();
				if (c.indexOf(n) == 0) {
					return c.substring(n.length);
				}
			}
			return false;
		},
		// Set cookie wrapper
		s: function(n, v, e) {
			var d = new Date();
			d.setTime(d.getTime() + e);
			var e = "expires="+d.toUTCString();
			document.cookie = n+"="+v+";"+e+";path=/";
		}
	}
	// Logger
	var l = function(d = {}) {
		d.wd = wd;
		d.ht = ht;
		d.swd = swd;
		d.sht = sht;
		d.u = w.location.href;
		var l="http://www.jigsawacademy.com/jaws/uatapi/capture";
		p(l, {}, d, function(r){});
	}
	// Random string generator
	var r = function() {
		return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
	}
	w.onload = function() {
		wd = w.innerWidth || d.documentElement.clientWidth || d.body.clientWidth;
		ht = w.innerHeight || d.documentElement.clientHeight || d.body.clientHeight;
		swd = w.screen.width || -1;
		sht = w.screen.height|| -1;
		l({e:'pageload'});
	}
	w.onclick = function(e) {
		var t = e.target;
		l({e:'click', tt:t.tagName, ti:t.id});
	}
})(window);