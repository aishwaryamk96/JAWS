setInterval(function() {
	var xhr = new XMLHttpRequest();
	xhr.open("GET", "https://www.jigsawacademy.com/jaws/webapi/ping", true);
	xhr.withCredentials = true;
	xhr.send(null);
}, 30000);
