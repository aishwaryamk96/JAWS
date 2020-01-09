(function(w) {
	var d = w.document;
	var x, wd, ht, swd, sht;
	var le = null;
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
		d.p = w.location.pathname;
		var l=atob("aHR0cHM6Ly93d3cuamlnc2F3YWNhZGVteS5jb20vamF3cy91YXRhcGkvY2FwdHVyZQ==");
		p(l, {}, d, function(r){});
	}
	// Random string generator
	var r = function() {
		return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
	}
	// Visible part of page's Y calculator
	var py = function(e) {
		return {y0:e.pageY - e.clientY, y1:e.pageY + (ht - e.clientY)};
	}
	// Mouse events wrapper
	var m = {
		c:function(e) {
			var t = e.target;
			var m = "";
			if (t.tagName=="A") {
				m = t.href;
			}
			else if (t.tagName=="IMG") {
				m = t.src;
			}
			var p = t.parentElement;
			while (p.id=='' && p.tagName!='BODY') {
				if (p.tagName=='A') {
					break;
				}
				p = p.parentElement;
			}
			if (m=='' && p.tagName=='A') {
				m = p.href;
			}
			var pi = p.id;
			if (p.tagName=='BODY') {
				pi = 'body';
			}
			var y = py(e);
			l({e:'click', x:e.pageX, y:e.pageY, cx:e.clientX, cy:e.clientY, tt:t.tagName, ti:t.id, tc:t.className, tm:m, y0:y.y0, y1:y.y1, pi:pi});
		},
		o:function(e) {
			var s = (e.timeStamp)/1000;
			if (le !== null && le.target != e.target && s - le.timeStamp >= 5.0) {
				var y = py(le);
				var m = "";
				if (le.target.tagName == "A") {
					m = le.target.href;
				}
				l({e:'attention', x:e.pageX, y:e.pageY, tt:le.target.tagName, ti:le.target.id, tm:m, y0:y.y0, y1:y.y1});
			}
			le = e;
		}
	}
	w.onload = function() {
		wd = w.innerWidth || d.documentElement.clientWidth || d.body.clientWidth;
		ht = w.innerHeight || d.documentElement.clientHeight || d.body.clientHeight;
		swd = w.screen.width || -1;
		sht = w.screen.height|| -1;
		l({e:'pageload'});
	}
	w.onclick = m.c;
	w.onmouseover = m.o;
	w.onmousewheel = m.o;
})(window);