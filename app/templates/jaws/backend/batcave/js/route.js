var app = angular.module("batcave", ["ngRoute", "ngAnimate", "ngSanitize"]);

app.filter('prettify', function () {
	function syntaxHighlight(json) {
		json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
		return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
			var cls = 'number';
			if (/^"/.test(match)) {
				if (/:$/.test(match)) {
					cls = 'key';
				}
				else {
					cls = 'string';
				}
			}
			else if (/true|false/.test(match)) {
				cls = 'boolean';
			}
			else if (/null/.test(match)) {
				cls = 'null';
			}
			return '<span class="' + cls + '">' + match + '</span>';
		});
	}
	return syntaxHighlight;
});

app.config(function($routeProvider) {
	$routeProvider
		.when("/track", {
			templateUrl : JAWS_PATH_VIEWS + "/track.html",
			controller : "trackCtrl"
		})
		.when("/edit", {
			templateUrl : JAWS_PATH_VIEWS + "/edit.html",
			controller : "editCtrl"
		})
		.when("/search/:query", {
			templateUrl : JAWS_PATH_VIEWS + "/search.html",
			controller : "searchCtrl"
		})
		.when("/user/:user_id", {
			templateUrl : JAWS_PATH_VIEWS + "/user.html",
			controller : "userCtrl"
		})
		.when("/refer/:query?", {
			templateUrl : JAWS_PATH_VIEWS + "/refer.html",
			controller : "referCtrl"
		})
		.when("/labs", {
			templateUrl : JAWS_PATH_VIEWS + "/lab.html",
			controller : "labCtrl"
		})
		.when("/enrollment/:query", {
			templateUrl : JAWS_PATH_VIEWS + "/enrollment.html",
			controller : "enrollmentCtrl"
		})
		.when("/course/:query", {
			templateUrl : JAWS_PATH_VIEWS + "/course.html",
			controller : "courseCtrl"
		})
		.when("/section/:query", {
			templateUrl : JAWS_PATH_VIEWS + "/section.html",
			controller : "sectionCtrl"
		})
		.when("/mobile/notifications", {
			templateUrl : JAWS_PATH_VIEWS + "/notifications.html",
			controller : "notificationsCtrl"
		})
		.when("/mobile", {
			redirectTo : "/mobile/notifications"
		})
		.when("/students/:query?", {
			templateUrl : JAWS_PATH_VIEWS + "/students.html",
			controller : "studentsCtrl"
		})
		.when("/careers", {
			templateUrl : JAWS_PATH_VIEWS + "/careers.html",
			controller : "careersCtrl"
		})
		.otherwise({
			redirectTo : "/"
		});
});