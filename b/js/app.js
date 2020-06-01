// var layoutOverlay = {};
var layoutOverlay = {
	show: function(loader = false, fullScreen = false) {
		if (fullScreen) {
			$(".layout-overlay").css("z-index", 10000);
		}
		$(".layout-overlay").addClass("show");
		if (loader) {
			$(".loader").removeClass("hidden");
		}
	},
	hide: function() {
		$(".layout-overlay").css("z-index", 100);
		$(".layout-overlay").removeClass("show");
		$(".loader").addClass("hidden");
	},
	toggle: function() {
		if ($(".layout-overlay").hasClass("show")) {
			$(".layout-overlay").removeClass("show");
			$(".loader").addClass("hidden");
		}
		else {
			$(".layout-overlay").addClass("show");
		}
	}
}
var batBar = {
	add: function(props = {}) {
		if (!props.content) {
			return;
		}
		if (!props.delay || !Number.isInteger(props.delay)) {
			props.delay = 3000;
		}
		var bb = $(".batbar.d-none").clone(true, true);
		bb.removeClass("d-none").find(".content").html(props.content);
		bb.find(".btn-close").bind("click", batBar.remove);
		if (props.controls) {
			props.controls.forEach(function(e) {
				var btn = $(document.createElement("BUTTON"));
				btn.addClass("btn").addClass(e.class).html(e.text).bind("click", e.click);
				bb.find(".control").find(".d-flex").append(btn);
			});
		}
		bb.appendTo(".batbar-container");
		setTimeout(function() {
			bb.addClass("show");
		}, 100);
		setTimeout(function() {
			bb.removeClass("show");
			setTimeout(function() {
				bb.remove();
			}, 300);
		}, props.delay);
	},
	remove: function(e) {
		var b = $(e.target);
		var p = b.parents(".batbar");
		p.removeClass("show");
		setTimeout(function() {
			p.remove();
		}, 300);
	}
}
$(document).ready(function() {
	$("input[type='search']").focus();
	$("input[type='search']").on("focus", function() {
		this.select();
	});
	$("#btnClearSearch").click(function() {
		$("input[type='search']").focus();
	});
	$(".layout-overlay").click(function(e) {
		if (e.target == this) {
			// drawer.hide();
		}
	});
});
angular.module("batcave", ["ngRoute"])
	.config(function($locationProvider, $routeProvider) {
		$locationProvider.html5Mode(true);
		$routeProvider
			.when("/", {
				templateUrl: VIEWS + "/home.html",
				controller: "homeCtrl"
			})
			.when("/search", {
				templateUrl: VIEWS + "/search.html",
				controller: "searchCtrl",
				resolve: {
					results: function(searchService) {
						return searchService();
					}
				}
			})
			.when("/users/:userId", {
				templateUrl: VIEWS + "/user.html",
				controller: "userCtrl",
				resolve: {
					response: function(userService) {
						return userService();
					}
				}
			})
			.when("/careers", {
				templateUrl: VIEWS + "/jobs.html",
				controller: "jobsCtrl",
				resolve: {
					response: function(jobsService) {
						return jobsService();
					}
				}
			})
			.when("/404", {
				templateUrl: VIEWS + "/error.html",
				controller: "errorCtrl",
			})
			.when("/error", {
				templateUrl: VIEWS + "/error.html",
				controller: "errorCtrl"
			})
			.when("/courses", {
				templateUrl: VIEWS + "/courses.html",
				controller: "coursesCtrl",
				resolve: {
					response: function(coursesService) {
						return coursesService.all();
					}
				}
			})
			.when("/courses/:id", {
				templateUrl: VIEWS + "/course.html",
				controller: "courseCtrl",
				resolve: {
					response: function(coursesService) {
						return coursesService.id();
					}
				}
			})
			.when("/programs", {
				templateUrl: VIEWS + "/programs.html",
				controller: "programsCtrl",
				resolve: {
					response: function(programsService) {
						return programsService.all();
					}
				}
			})
			.when("/programs/:id", {
				templateUrl: VIEWS + "/program.html",
				controller: "programCtrl",
				resolve: {
					response: function(programsService) {
						return programsService.id();
					}
				}
			})
			.when("/notifications/:id?", {
				templateUrl: VIEWS + "/notifications.html",
				controller: "notificationsCtrl",
				resolve: {
					response: function(notificationsService) {
						return notificationsService.segregated();
					}
				}
			})
			.when("/settings", {
				templateUrl: VIEWS + "/settings.html",
				controller: "settingsCtrl"
			})
			.when("/students/:context?/:id?", {
				templateUrl: VIEWS + "/students.html",
				controller: "studentsCtrl",
				resolve: {
					response: function(studentsService) {
						return studentsService.all();
					}
				}
			})
			.when("/mobile", {
				templateUrl: VIEWS + "/mobile.html",
				controller: "mobileCtrl",
				resolve: {
					stats: function(mobileService) {
						return mobileService.stats();
					},
					form: function(mobileService) {
						return mobileService.form();
					}
				}
			})
			.when("/leads/:email", {
				templateUrl: VIEWS + "/lead.html",
				controller: "leadCtrl",
				resolve: {
					response: function(leadService) {
						return leadService();
					}
				}
			})
			.when("/bootcamp_batches/:id", {
				templateUrl: VIEWS + "/bootcamp_batch.html",
				controller: "bootcampBatchCtrl",
				resolve: {
					response: function(bootcampBatchService) {
						return bootcampBatchService();
					}
				}
			})
			.when("/sales/new", {
				templateUrl: VIEWS + "/sales/new.html",
				controller: "salesNewCtrl",
				resolve: {
					response: function(salesNewService) {
						return salesNewService();
					}
				}
			})
			.when("/orders", {
				templateUrl: VIEWS + "/orders.html",
				controller: "ordersCtrl",
				resolve: {
					response: function(ordersService) {
						return ordersService.all();
					}
				}
			})
			.when("/orders/:id", {
				templateUrl: VIEWS + "/order.html",
				controller: "orderCtrl",
				resolve: {
					response: function(ordersService) {
						return ordersService.fetch();
					}
				}
			})
			.when("/payments", {
				templateUrl: VIEWS + "/payments.html",
				controller: "paymentsCtrl"
			})
			.when("/payments/export", {
				templateUrl: VIEWS + "/payments/export.html",
				controller: "paymentExportCtrl"
			})
			.when("/payments/applications", {
				templateUrl: VIEWS + "/payments/applications/track.html",
				controller: "applicationsCtrl",
				resolve: {
					response: function(applicationsService) {
						return applicationsService.all();
					}
				}
			})
			.when("/labs", {
				templateUrl: VIEWS + "/labs.html",
				controller: "labsCtrl",
				resolve: {
					response: function(labsService) {
						return labsService.all();
					}
				}
			})
			.otherwise("/404")
	})
	.service("Session", function() {
		this.create = function(user) {
			sessionStorage.setItem("user", JSON.stringify(user));
			sessionStorage.setItem("auth", JSON.stringify(user.roles.feature_keys));
			sessionStorage.setItem("preferences", JSON.stringify(user.batcave_pref));
		}
		this.initialized = function() {
			return sessionStorage.getItem("user");
		}
		this.destroy = function() {
			sessionStorage.clear();
		}
		this.user = function(set = "") {
			if (!!set) {
				return sessionStorage.setItem("user", JSON.stringify(set));
			}
			return JSON.parse(sessionStorage.getItem("user"));
		}
		this.auth = function() {
			return JSON.parse(sessionStorage.getItem("auth"));
		}
		this.preferences = function(set = "") {
			if (!!set) {
				return sessionStorage.setItem("preferences", JSON.stringify(set));
			}
			return JSON.parse(sessionStorage.getItem("preferences"));
		}
	})
	.factory("AuthService", function($http, Session, $window, $location) {
		return {
			authenticate: function(forced = false, success = null) {
				if (Session.initialized() && !forced) {
					return;
				}
				var url = API + "/identity/authentication";
				if (forced) {
					url += "?forced=true";
				}
				return $http
					.get(url, {withCredentials: true})
					.then(function (res) {
						Session.create(res.data.user);
						if (!!success) {
							success();
						}
					}, function(res) {
						if (res.status == 400) {
							$window.location.href = "https://accounts.jigsawacademy.com/?ru=" + encodeURI($window.location.href);
						}
						else if (res.status == 401) {
							$window.location.href = "https://www.jigsawacademy.com";
						}
					});
			},
			authenticated: function() {
				return Session.initialized();
			},
			authorized: function(scope, privilege) {
				return Session.auth()[scope][privilege];
			},
			can: function() {
				return function(verb, object) {
					var edit = false;
					if (verb == "view") {
						edit = Session.auth()["batcave.edit."+object]
					}
					return Session.auth()["batcave."+verb+"."+object] == 1 || edit;
				}
			},
			logout: function() {
				Session.destroy();
			},
			user: function(set = "") {
				if (!!set) {
					return Session.user(set);
				}
				return Session.user();
			},
			preferences: function(set = "") {
				if (!!set) {
					return Session.preferences(set);
				}
				return Session.preferences();
			}
		};
	})
	.factory("searchService", function($http, $route) {
		return function() {
			return $http.get(API + "/search?q=" + $route.current.params.q);
		}
	})
	.factory("userService", function($http, $route) {
		return function() {
			return $http.get(API + "/user.get?user="+$route.current.params.userId);
		}
	})
	.factory("jobsService", function($http) {
		return function() {
			return $http.get(API + "/careers");
		}
	})
	.factory("coursesService", function($http, $route) {
		return {
			all: function() {
				return $http.get(API + "/catalogue/courses");
			},
			id: function() {
				return $http.get(API + "/catalogue/courses?id=" + $route.current.params.id);
			}
		}
	})
	.factory("programsService", function($http, $route) {
		return {
			all: function() {
				return $http.get(API + "/catalogue/programs");
			},
			id: function() {
				return $http.get(API + "/catalogue/programs?id=" + $route.current.params.id);
			}
		}
	})
	.factory("notificationsService", function($http) {
		return {
			segregated: function() {
				return $http.get(API + "/notifications");
			},
			all: function() {
				return $http.get(API + "/notifications?all=1");
			}
		}
	})
	.factory("studentsService", function($http, $route) {
		return {
			all: function() {
				if ($route.current.params.context) {
					if (!$route.current.params.id) {
						return $http.get(API + "/students");
					}
					var context = "s";
					if ($route.current.params.context == "courses") {
						context = "c";
					}
					context += $route.current.params.id;
					return $http.post(API + "/students", {criteria: {
						from_start_date: false,
						to_start_date: false,
						from_end_date: false,
						to_end_date: false,
						as: false,
						we: false,
						iot: false,
						catalogue: context,
						batch: "n0",
						no_save: true
					}});
				}
				else {
					return $http.get(API + "/students");
				}
			}
		}
	})
	.factory("mobileService", function($http) {
		return {
			stats: function() {
				return $http.get(API + "/mobile/stats");
			},
			form: function() {
				return $http.get(API + "/mobile/form");
			}
		}
	})
	.factory("leadService", function($http, $route) {
		return function() {
			return $http.get(API + "/leads/get?email="+$route.current.params.email);
		}
	})
	.factory("bootcampBatchService", function($http, $route) {
		return function() {
			return $http.get(API + "/bootcamps/batch?id="+$route.current.params.id);
		}
	})
	.factory("ordersNewService", function($http, $route) {
		return function() {
			return $http.get(API + "/catalogue/products");
		}
	})
	.factory("ordersService", function($http, $route) {
		return {
			all: function() {
				return $http.get(API + "/orders");
			},
			fetch: function() {
				return $http.get(API + "/orders?id="+$route.current.params.id);
			}
		}
	})
	.factory("paymentsService", function($http) {
		return {
			overdue: function() {
				return $http.get(API + "/payments/overdue");
			},
			export: function(data = {}) {
				return $http.post(API + "/payments/export", data);
			}
		}
	})
	.factory("labsService", function($http, $route) {
		return {
			all: function() {
				return $http.get(API + "/labs");
			},
			fetch: function() {
				return $http.get(API + "/labs?id="+$route.current.params.id);
			},
			create: {
				lab: function(lab) {
					return $http.post(API + "/labs/new", {lab: lab});
				},
				courseLab: function(courseLab) {
					return $http.post(API + "/labs/course_labs/new", {courseLab: courseLab});
				}
			}
		}
	})
	.factory("applicationsService", function($http) {
		return {
			all: function(currentPage) { 
				var config = {
					params: {
						page: (currentPage ? currentPage: 1)
					}
				};
				return $http.get(API + "/payments/tracking/applications",config);
			},
			find: function($email) {
				return $http.post(API + "/payments/tracking/applications", {email: email});
			},
			save: function(application) {
				return $http.post(API + "/payments/tracking/application.edit", {application: application});
			}
		}
	})
	.directive("formatDate", function() {
		let normalize = function(value) {
			return value < 10 ? "0" + value : value;
		}
		return {
			require: 'ngModel',
			link: function(scope, elem, attr, modelCtrl) {
				modelCtrl.$formatters.push(function(modelValue) {
					if (modelValue) {
						return new Date(modelValue);
					}
					else {
						return modelValue;
					}
				});
				modelCtrl.$parsers.push(function(modelValue) {
					if (modelValue) {
						return modelValue.getFullYear() + "-" + normalize(modelValue.getMonth() + 1) + "-" + normalize(modelValue.getDate());
					}
					else {
						return modelValue;
					}
				});
			}
		}
	})
	.directive("formatNumber", function() {
		return {
			require: 'ngModel',
			link: function(scope, elem, attr, modelCtrl) {
				modelCtrl.$formatters.push(function(modelValue) {
					if (modelValue) {
						return parseInt(modelValue);
					}
					else {
						modelValue;
					}
				});
				modelCtrl.$parsers.push(function(modelValue) {
					if (modelValue) {
						return parseInt(modelValue);
					}
					else {
						return modelValue;
					}
				});
			}
		}
	})
	.directive("inverseBoolean", function() {
		return {
			require: 'ngModel',
			link: function(scope, elem, attr, modelCtrl) {
				modelCtrl.$formatters.push(function(modelValue) {
					if (modelValue != undefined && modelValue != null) {
						return !modelValue;
					}
					else {
						modelValue;
					}
				});
				modelCtrl.$parsers.push(function(modelValue) {
					if (modelValue != undefined && modelValue != null) {
						return !modelValue;
					}
					else {
						return modelValue;
					}
				});
			}
		}
	})
	.directive("elipsize", function() {
		return {
			scope: {
				str: "="
			},
			template: "<div>{{str}}</div>",
			link: function(scope, elem, attrs) {
				var str = scope.str ? scope.str : '';
				var length = attrs.length ? attrs.length : 100;
				str = str.toLowerCase();
				scope.str = str.length > length ? str.substring(0, length - 3) + "..." : str;
			}
		}
	})
	.directive("rendered", function($parse) {
		return {
			link: function(scope, elem, attrs) {
				if (!attrs.rendered) {
					return;
				}
				elem.ready(function() {
					scope.$apply(function() {
						var func = $parse(attrs.rendered);
						func(scope);
					});
				});
			}
		}
	})
	.directive('pagingControl',function(){
            return {
              templateUrl: VIEWS + "/pagination.html",
         }
        })
	.directive("select2", function($timeout, $parse) {
		return {
		  restrict: 'AC',
		  require: 'ngModel',
		  link: function(scope, element, attrs) {
			$timeout(function() {
			  element.select2();
			  element.select2Initialized = true;
			});
	  
			var refreshSelect = function() {
			  if (!element.select2Initialized) return;
			  $timeout(function() {
				element.trigger('change');
			  });
			};
			
			var recreateSelect = function () {
			  if (!element.select2Initialized) return;
			  $timeout(function() {
				element.select2('destroy');
				element.select2();
			  });
			};
	  
			scope.$watch(attrs.ngModel, refreshSelect);
	  
			if (attrs.ngOptions) {
			  var list = attrs.ngOptions.match(/ in ([^ ]*)/)[1];
			  // watch for option list change
			  scope.$watch(list, recreateSelect);
			}
	  
			if (attrs.ngDisabled) {
			  scope.$watch(attrs.ngDisabled, refreshSelect);
			}
		  }
		};
	})
	.run(['$rootScope', '$window', '$location', 'AuthService', function($rootScope, $window, $location, AuthService) {
		$rootScope.initialized = false;
		AuthService.authenticate(true, function() {
			angular.element(".bm-overlay").removeClass("show");
			$rootScope.initialized = true;
		});
		$rootScope.$on('$routeChangeStart', function(e, n, c) {
			$rootScope.title = "Home";
			if ($window.layoutOverlay.show) {
				$window.layoutOverlay.show(true);
			}
		});
		$rootScope.$on('$routeChangeSuccess', function(e, c, p) {
			AuthService.authenticate();
			$rootScope.title = c.$$route.title;
			$window.layoutOverlay.hide();
		});
		$rootScope.$on("$routeChangeError", function(e, c, p, ex) {
			if (ex.status == 401) {
				$window.location.href = "https://accounts.jigsawacademy.com/?ru=" + encodeURI($window.location.href);
			}
			else {
				$window.layoutOverlay.hide();
				$rootScope.errorRef = ex.status;
				$location.url("/error");
			}
		});
	}])
	.controller("rootCtrl", function($scope, $window, $location, $http, AuthService, notificationsService) {
		$scope.user = AuthService.user();
		$scope.user_can = AuthService.can();
		$scope.preferences = AuthService.preferences();
		notificationsService.all().then(function(response) {
			$scope.notifications = response.data;
		});
		$scope.sidebarLock = function() {
			var drawer = angular.element(".drawer");
			if (drawer.hasClass("fixed")) {
				drawer.removeClass("fixed");
			}
			else {
				drawer.addClass("fixed");
			}
		}
		$scope.search = function() {
			$location.url("/search?q=" + $scope.searchText);
		}
		$scope.reloadAccount = function() {
			$window.layoutOverlay.show(true);
			AuthService.authenticate(true, function() {
				$window.layoutOverlay.hide();
			});
		}
		$scope.logout = function() {
			$http.get(API + "/identity/logout")
				.then(function(response) {
					AuthService.logout();
					$window.location.href = "https://accounts.jigsawacademy.com/?ru=" + encodeURI($window.location.href);
				}, function(response) {
					AuthService.logout();
					$window.location.href = "https://accounts.jigsawacademy.com/?ru=" + encodeURI($window.location.href);
				});
		}
	})
	.controller("parentCtrl", function($scope, $http, $location, $rootScope) {
		$rootScope.title = "Home";
		$scope.chatOn = false;
		$scope.$on("startChat", function(e, d) {
			angular.element("#chatWindow").attr("src", "https://chat.jigsawacademy.com/users/"+d).removeClass("hidden")
			$scope.chatOn = true;
		});
		$scope.search = function() {
			if ($scope.primarySearch != "") {
				$location.url("/search/"+encodeURIComponent($scope.primarySearch));
			}
		}
		$scope.toggleChat = function() {
			$scope.chatOpen = !$scope.chatOpen;
		}
	})
	.controller("homeCtrl", function($rootScope, $scope, $http, $location, AuthService) {
		$rootScope.title = "Home";
		$scope.user = AuthService.user();
		$scope.goto = function(location) {
			$location.url("/" + location);
		}
	})
	.controller("errorCtrl", function($scope, $rootScope, $location, $routeParams) {
		if ($location.url() == "/404") {
			$rootScope.errorRef = 404;
		}
		if (!$rootScope.errorRef) {
			$location.url("/404");
			return;
		}
		$scope.errorRef = $rootScope.errorRef;
		$scope.title = {
			403: "Unauthorized...",
			404: "404",
			500: "Something went wrong...",
		};
		$rootScope.title = $scope.title[$scope.errorRef];
		$scope.msg1 = {
			403: "Sorry, you are not sufficiently authorized to access that page.",
			404: "Page not found",
			500: "Oops! The last request did not work out. Please try again after some time."
		}
		$scope.msg2 = {
			403: "Please contact your TL who will contact their manager, who in turn will contact the CTO, who will let one of the developers know that you should have access to this page."
		}
		$scope.msg3 = {
			403: "That, my friend, is the only way..."
		}
	})
	.controller("searchCtrl", function($scope, $rootScope, $routeParams, $location, results) {
		$scope.iconMap = {
			'user': 'person',
			'lead': 'monetization_on',
			'course': 'book',
			'program': 'school',
			'bundle': 'school',
			'bootcamp': 'school'
		}
		$scope.rttr = {
			'user': 'users',
			'course': 'courses',
			'bundle': 'programs',
			'bootcamp': 'programs',
			'program': 'programs',
			'lead': 'leads'
		};
		// $scope.$emit("homePageChanged", false);
		$rootScope.title = "Search: " + $routeParams.q;
		$scope.query = $routeParams.q;
		results = results.data;
		$scope.results = results.results;
		$scope.usersCount = results.usersCount;
		$scope.coursesCount = results.coursesCount;
		$scope.bundlesCount = results.bundlesCount;
		$scope.leadsCount = results.leadsCount;
		$scope.shouldShowFilter = $scope.coursesCount + $scope.bundlesCount + $scope.leadsCount;
		$scope.time = results.time;
		if ($scope.results.length == 1) {
			var res = $scope.results[0];
			if (res.type != "lead") {
				$location.url("/" + $scope.rttr[res.type] + "/" + res.id);
				return;
			}
		}
		$scope.filter = "3";
		$scope.resultsPerPage = "10";
		$scope.currentPage = 1;
		$scope.filterChanged = function() {
			if ($scope.filter == "3") {
				$scope.totalPages = Math.ceil($scope.results.length / $scope.resultsPerPage);
			}
			else if ($scope.filter == "0") {
				$scope.totalPages = Math.ceil(($scope.coursesCount) / $scope.resultsPerPage);
			}
			else if ($scope.filter == "1") {
				$scope.totalPages = Math.ceil(($scope.bundlesCount) / $scope.resultsPerPage);
			}
			else if ($scope.filter == "2") {
				$scope.totalPages = Math.ceil(($scope.coursesCount + $scope.bundlesCount) / $scope.resultsPerPage);
			}
			else if ($scope.filter == "4") {
				$scope.totalPages = Math.ceil(($scope.leadsCount) / $scope.resultsPerPage);
			}
		}
		$scope.resultsPerPageChange = function() {
			$scope.filterChanged();
		}
		$scope.resultsPerPageChange();
		$scope.resultsByRange = function(val) {
			var index;
			if ($scope.filter == "0") {
				if (val.type != "course") {
					return false;
				}
				index = $scope.results.indexOf(val) - $scope.usersCount;
			}
			else if ($scope.filter == "1") {
				if (val.type != "bundle") {
					return false;
				}
				index = $scope.results.indexOf(val) - $scope.usersCount - $scope.coursesCount;
			}
			else if ($scope.filter == "2") {
				if (val.type == "user") {
					return false;
				}
				index = $scope.results.indexOf(val) - $scope.usersCount;
			}
			else if ($scope.filter == "4") {
				if (val.type != "lead") {
					return false;
				}
				index = $scope.results.indexOf(val) - $scope.usersCount - $scope.coursesCount - $scope.bundlesCount;
			}
			else {
				index = $scope.results.indexOf(val);
			}
			return (index >= ($scope.currentPage - 1) * $scope.resultsPerPage && index < $scope.currentPage * $scope.resultsPerPage);
		}
		$scope.pagesByRange = function(val) {
			var currPage = $scope.currentPage;
			var totalPages = $scope.totalPages;
			if (currPage < 6) {
				return (val <= 10 ? true : false);
			}
			if (currPage >= 6 && currPage <= totalPages) {
				return ((currPage - val <= 5 && val - currPage <= 5) ? true : false);
			}
			if (currPage > totalPages - 6) {
				return (val >= totalPages - 10 ? true : false);
			}
		}
		$scope.range = function(min, max) {
			var arr = [];
			max = Math.ceil(max);
			for (var i = min; i <= max; i++)
				arr.push(i);
			return arr;
		}
		$scope.pageChange = function(pageNum) {
			$scope.currentPage = pageNum;
		}
	})
	.controller("userCtrl", function($scope, $rootScope, $http, $window, response, AuthService) {
		$scope.user_can = AuthService.can();
		$scope.appAccess = "phone_iphone";
		response = response.data;
		$scope.user = response.user;
		$scope.appAccessTitle = "Check " + $scope.user.name + "'s Mobile app Access";
		$scope.notifications = response.notifications;
		$scope.user.labs = response.user.labs;
		$scope.bundles = response.bundles;
		$scope.courses = response.courses;
		$scope.sisUrl = API + "/sis?sis=";
		$scope.batches = {
			"type": "none",
			"l": {},
			"i": 0
		};
		$scope.jlc = {
			status: function() {
				if (!$scope.user.jlc_status) {
					return "";
				}
				if ($scope.user.jlc_status.user == "registered") {
					if ($scope.user.jlc_status.pseudonym == "active") {
						return "";
					}
				}
				return "header-inactive";
			}
		}
		$scope.location = [];
		if ($scope.user.city) {
			$scope.location.push($scope.user.city);
		}
		if ($scope.user.state) {
			$scope.location.push($scope.user.state);
		}
		if ($scope.user.country) {
			$scope.location.push($scope.user.country);
		}
		$scope.location = $scope.location.join(", ");
		if (!!$scope.user.survey_date) {
			$scope.user.survey_data = $.parseJSON($scope.user.survey_data);
			$scope.user.leads_media_src = $scope.user.leads_media_src.split(",");
			$scope.user.survey_data.why = $scope.user.survey_data.why.split(",");
			$scope.user.survey_data.enquiry = $scope.user.survey_data.enquiry.split(",");
			$scope.user.survey_data.sales = $scope.user.survey_data.sales.split(",");
		}
		$scope.logs = response.logs;
		$scope.logClasses = [
			"bg-info text-white",
			"bg-primary text-white",
			"bg-secondary text-white",
			"bg-warning text-white",
			"bg-success text-white"
		];
		$scope.bundleTypeClasses = {
			"specialization": "bg-primary text-white",
			"bootcamps": "bg-info text-white",
			"custom": "bg-success text-white",
			"programs": "bg-pgpdm text-white"
		};
		$scope.why = {
			"Passion": "I am passionate about analytics",
			"Job Raise": "It will help me to move ahead in my current company",
			"Role requirement": "My current role requires knowledge in analytics",
			"Problem Solving": "I want to use more analytics in my current role",
			"Job Search": "I want to get a job in the field of analytics"
		};
		$scope.how = {
			"Google":"Google",
			"Bing":"Bing",
			"Facebook":"Facebook",
			"LinkedIn":"LinkdIn",
			"Twitter":"Twitter",
			"Google ads":"Google ads",
			"Banner ads":"Banner ads",
			"Newspaper":"Newspaper",
			"PRWEB":"PRWEB",
			"Alumni referral":"Alumni ref.",
			"College Magazine":"Magazine",
			"Other":"Other"
		};
		$scope.know = {
			"Website Form":"Filled a form on website",
			"Phone":"Called",
			"Chat":"Did a chat session",
			"Social Media":"Social media",
			"Office":"Office walk-in",
			"Other":"Other"
		};
		$scope.enrollWhy = {
			"Online Demo":"Attending the online demo session",
			"Students":"Talking to other students",
			"Website":"Based on information on the website",
			"Jigsaw Team":"Communicating with Jigsaw team on email",
			"Free Class":"Attending the 1st class for free",
			"Testimonials or Reviews":"Reading online testimonials and reviews",
			"Other":"Other"
		};
		$rootScope.title = $scope.user.name + "'s profile";
		$scope.startChat = function() {
			$scope.$emit("startChat", $scope.user.jig_id);
		}
		$scope.initiateCall = function() {
			if (!confirm("Are you sure you want to connect to "+$scope.user.phone+"?")) {
				return;
			}
			$http.get(API + "/user.call?user=" + $scope.user.user_id)
				.then(
					function success(response) {
						alert(response.data.msg);
					},
					function error(response) {
						if (response.status == 401) {
							$window.location.reload();
						}
						else {
							$.snackbar({content: "Something went wrong...\nPlease contact IT team for the issue."});
						}
					}
				);
		}
		$scope.showProfile = function() {
			$scope.tempUser = {
				email: $scope.user.email,
				name: $scope.user.name,
				phone: $scope.user.phone,
				email_2: $scope.user.email_2,
				soc_fb: $scope.user.soc_fb,
				soc_gp: $scope.user.soc_gp,
				soc_li: $scope.user.soc_li,
				lms_soc: $scope.user.lms_soc,
				freeze: $scope.user.freeze,
				jlc: {
					canRevoke: function() {
						if (!!$scope.user.jig_id) {
							if ($scope.user.jlc_status.user == "registered") {
								return ($scope.user.jlc_status.pseudonym == "active") + false;
							}
							return 0;
						}
						return -1;
					},
					grant: function() {
						$http.post(API + "/user/grant", {user_id: $scope.user.user_id})
							.then(function(res) {
								if (res.status) {
									alert("Access to JLC has been restored!");
									$window.location.reload();
								}
								else {
									alert("Something went wrong...");
								}
							}, function(res) {
								alert("Something went wrong...");
							});
					},
					revoke: function() {
						$http.post(API + "/user/revoke", {user_id: $scope.user.user_id})
							.then(function(res) {
								if (res.status) {
									alert("Access to JLC has been revoked!");
									$window.location.reload();
								}
								else {
									alert("Something went wrong...");
								}
							}, function(res) {
								alert("Something went wrong...");
							});
					}
				}
			};
			angular.element("#profile").modal("show");
		}
		$scope.textCopy = function(text) {
			angular.element("#hidden-input").removeClass("hidden").val(text).select();
			document.execCommand("Copy");
			angular.element("#hidden-input").addClass("hidden");
		}
		$scope.textCopyFromElement = function(e) {
			console.log(e);
			console.log(angular.element(e));
			angular.element(e).select();
			document.execCommand("Copy");
			angular.element(e).blur();
		}
		$scope.showAssignments = function(courseCode, courseName) {
			$scope.assignments = $scope.user.assignments[courseCode];
			angular.element("#assignments").modal("show");
		}
		$scope.subsEdit = function(status, i) {
			$window.layoutOverlay.show(true, true);
			$scope.editableSubs = JSON.parse(JSON.stringify($scope.user.subs[status][i]));
			$scope.editableSubs.editable = true;
			$scope.editableSubs.originalBundleId = $scope.editableSubs.bundle_id;
			$scope.editableSubs.originalBatch = $scope.editableSubs.batch;
			$scope.courseBatches = $scope.setMonthlyBatches();
			// $scope.setBatches($scope.editableSubs.bundle_type, $scope.editableSubs.bundle_type == "bootcamps" ? $scope.editableSubs.bundle_id : 0);
			$scope.setBatches($scope.editableSubs.bundle_id, $scope.editableSubs.bundle_type);
			$scope.editableSubs.newEnrs = [];
			$scope.editableSubs.removedEnrs = [];
			$scope.editableSubs.newDurations = [];
			$scope.editableSubs.newEnrsAdded = 0;
			$scope.editableSubs.accessDatesChanged = 0;
			$scope.editableSubs.pay.newInstl = [];
			$scope.editableSubs.statusNew = false;
			angular.element("#editSubs").on("shown.bs.modal", function() {
				$window.layoutOverlay.hide();
			}).modal("show");
		}
		$scope.setBatches = function(id, type) {
			if (type == "specialization" && $scope.batches.type != type) {
				if (Object.keys($scope.bundles.specializations[id].batches).length) {
					$scope.batches.l = $scope.bundles.specializations[id].batches;
					$scope.batches.type = "bootcamps";
				}
				else {
					$scope.batches.l = $scope.setMonthlyBatches();
					$scope.batches.type = "specialization";
				}
			}
			else if (type == "bootcamps" && $scope.batches.i != id) {
				$scope.batches.type = "bootcamps";
				$scope.batches.l = $scope.bundles.bootcamps[id].batches;
				$scope.batches.l[0] = "Please select one";
				$scope.i = id;
			}
			else if (type == "custom" || id == "0") {
				$scope.batches.l = $scope.setMonthlyBatches();
			}
			else {
				if (Object.keys($scope.bundles.programs[id].batches).length) {
					$scope.batches.l = $scope.bundles.programs[id].batches;
					$scope.batches.type = "bootcamps";
				}
				else {
					$scope.batches.l = $scope.setMonthlyBatches();
					$scope.batches.type = "programs";
				}
			}
		}
		$scope.setMonthlyBatches = function() {
			var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September",  "October",  "November",  "December"];
			var years = [2015, 2016, 2017, 2018, 2019, 2020, 2021, 2022];
			$batches = {"0": "Please select one"};
			years.forEach(function(e, i) {
				months.forEach(function(f, j) {
					$batches[100*(i + 1) + j] = f + ", " + e;
				});
			});
			return $batches;
		}
		$scope.subsView = function(status, i) {
			$scope.editableSubs = $scope.user.subs[status][i];
			$scope.editableSubs.editable = false;
			angular.element("#editSubs").modal("show");
		}
		$scope.bundleChange = function() {
			if ($scope.editableSubs.bundle_id != $scope.editableSubs.originalBundleId) {
				$scope.editableSubs.batch = "0";
				if ($scope.editableSubs.enr.length>0) {
					$scope.editableSubsEnrs = $scope.editableSubs.enr;
					$scope.editableSubs.enr = [];
				}
				$type = "specialization";
				if ($scope.bundles.specializations[$scope.editableSubs.bundle_id]) {
					$type = "specialization";
				}
				else if ($scope.bundles.bootcamps[$scope.editableSubs.bundle_id]) {
					$type = "bootcamps";
				}
				else {
					$type = "programs";
				}
				$scope.setBatches($scope.editableSubs.bundle_id, $type);
				$scope.bundleChanged = true;
			}
			else if ($scope.editableSubs.bundle_id == $scope.editableSubs.originalBundleId) {
				$scope.editableSubs.enr = $scope.editableSubsEnrs;
				$scope.editableSubsEnrs = undefined;
				if ($scope.bundles.specializations[$scope.editableSubs.bundle_id]) {
					$scope.setBatches("specialization");
				}
				else if ($scope.bundles.bootcamps[$scope.editableSubs.bundle_id]) {
					$scope.setBatches("bootcamps", $scope.editableSubs.bundle_id);
				}
				else {
					$scope.setBatches("programs");
				}
				$scope.editableSubs.batch = $scope.editableSubs.originalBatch;
				$scope.bundleChanged = false;
			}
		}
		$scope.batchChange = function() {
			if ($scope.editableSubs.batch != $scope.editableSubs.originalBatch) {
				$scope.batchChanged = true;
			}
			else if ($scope.editableSubs.batch == $scope.editableSubs.originalBatch) {
				$scope.batchChanged = false;
			}
		}
		$scope.enrToggle = function(enr) {
			if (enr.new) {
				$scope.editableSubs.enr.splice($scope.editableSubs.enr.indexOf(enr), 1);
				// $scope.editableSubs.newEnrsAdded = --$scope.editableSubs.newEnrsAdded < 0 ? 0 : $scope.editableSubs.newEnrsAdded;
				return;
			}
			if (enr.status=='deleted') {
				// $scope.editableSubs.removedEnrs.splice($scope.editableSubs.removedEnrs.indexOf(enr), 1);
				enr.status = 'active';
			}
			else {
				// $scope.editableSubs.removedEnrs.push(enr);
				enr.status = 'deleted';
			}
		}
		$scope.newCourseAdd = function(enr) {
			if (enr.course_id != 0) {
				$scope.editableSubs.newEnrsAdded++;
			}
			else {
				$scope.editableSubs.newEnrsAdded = --$scope.editableSubs.newEnrsAdded < 0 ? 0 : $scope.editableSubs.newEnrsAdded;
			}
		}
		$scope.accessDateChange = function(e, i) {
			var dates = ['start_date', 'end_date'];
			if (!e.ad[dates[i]]) {
				$scope.editableSubs.accessDatesChanged--;
				return;
			}
			if (angular.element("#ad-" + e.$id).data("orig") != e.ad[dates[i]]) {
				$scope.editableSubs.accessDatesChanged++;
			}
			else {
				$scope.editableSubs.accessDatesChanged--;
			}
		}
		$scope.newAccessDateChange = function(i) {
			if (!!$scope.editableSubs.newDurations[i].start_date && !!$scope.editableSubs.newDurations[i].end_date) {
				var s = new Date($scope.editableSubs.newDurations[i].start_date);
				var e = new Date($scope.editableSubs.newDurations[i].end_date);
				if (s < e) {
					$scope.editableSubs.accessDatesChanged++;
					$scope.editableSubs.newDurations[i][3] = 1;
				}
				else if ($scope.editableSubs.newDurations[i][3] == 1) {
					$scope.editableSubs.accessDatesChanged--;
					$scope.editableSubs.newDurations[i][3] = undefined;
				}
			}
			else {
				$scope.editableSubs.accessDatesChanged = --$scope.editableSubs.accessDatesChanged < 0 ? 0 : $scope.editableSubs.accessDatesChanged;
			}
		}
		$scope.saveSubs = function() {
			$http.post(API + "/subs/edit", {subs: $scope.editableSubs})
				.then(function(response) {
					alert("Subscription has been updated!");
					$window.location.reload();
				}, function(response) {
					alert(response.data.errors.join("\n"));
				});
		}
		$scope.checkAppAccess = function() {
			if ($scope.appAccess == "phone_iphone") {
				$http.post(API + "/mobapp.access", { user_id: $scope.user.user_id })
					.then(
						function(response) {
							if (!response.data.msg) {
								$scope.appAccess = "mobile_friendly";
								$scope.appAccessTitle = $scope.user.name + " has complete access on mobile app";
								// $.snackbar({content: $scope.user.name + " has complete access on mobile app"});
								$window.batBar.add({content: $scope.user.name + " has complete access on mobile app"});
							}
							else {
								$scope.appAccess = "mobile_off";
								$scope.appAccessTitle = $scope.user.name + " does not have complete access on mobile app";
								// $.snackbar({content: $scope.user.name + " does not have complete access on mobile app"});
								$window.batBar.add({content: $scope.user.name + " does not have complete access on mobile app", delay: 7000, controls: [{
									click: $scope.grantAccess, class: "btn-danger", text: "Grant access"
								}]});
							}
						}
					)
			}
			/*else if ($scope.appAccess == "mobile_off") {
			}*/
			else {
				$http.post(API + "/app/subs", {user_id: $scope.user.user_id})
					.then(
						function(response) {
							$scope.appDebug = JSON.stringify(response.data, undefined, 4);
							angular.element("#appDebug").modal("show");
						}, function(response) {
							$.snackbar({content: "Something went wrong... Please contact IT team for the issue."});
						}
					)
			}
		}
		$scope.grantAccess = function() {
			if (!$scope.user_can('edit', 'subs')) {
				// return $.snackbar({content: $scope.user.name + " does not have complete access on mobile app"});
				$window.batBar.add({content: $scope.user.name + " does not have complete access on mobile app", delay: 7000});
				return;
			}
			$http.post(API + "/mobapp.access.grant", { user_id: $scope.user.user_id })
				.then(
					function(response) {
						if (response.data.status) {
							$scope.appAccess = "mobile_friendly";
							// $.snackbar({content: "Complete access has been given to the student!"});
							$window.batBar.add({content: "Complete access has been given to the student!"});
						}
						else if (!!response.data.msg) {
							// $.snackbar({content: response.data.msg});
							$window.batBar.add({content: response.data.msg});
						}
						else {
							// $.snackbar({content: "Something went wrong... Please contact IT team for the issue."});
							$window.batBar.add({content: "Something went wrong... Please contact IT team for the issue."});
						}
					}, function error(response) {
						if (response.status == 401) {
							$window.location.reload();
						}
						else {
							// $.snackbar({content: "Something went wrong... Please contact IT team for the issue."});
							$window.batBar.add({content: "Something went wrong... Please contact IT team for the issue."});
						}
					}
				)
		}
		$scope.platformExport = function(status, i) {
			$scope.exportRequest = $scope.user.subs[status][i].export_request;
			$scope.exportResponse = $scope.user.subs[status][i].export_response;
			$scope.exportedAt = $scope.user.subs[status][i].exported_at;
			if ($scope.user.subs[status][i].platform_id != 2) {
				$scope.exportRequest = JSON.stringify($scope.exportRequest, undefined, 4);
				$scope.exportResponse = JSON.stringify($scope.exportResponse, undefined, 4);
			}
			angular.element("#exportInfo").modal("show");
		}
	})
	.controller("jobsCtrl", function($rootScope, $scope, $http, response) {
		$rootScope.title = "Careers";
		response = response.data;
		$scope.canDownload = response.canDownload;
		$scope.canCreate = response.canCreate;
		$scope.careers = response.careers;
		$scope.courses = response.courses;
		$scope.tools = [];
		$scope.insert = "";
		$scope.toolsAdd = function() {
			if ($scope.insert != '' && $scope.tools.indexOf($scope.insert) === -1) {
				$scope.tools.push($scope.insert);
				$scope.insert = "";
			}
		}
		$scope.toolsRemove = function(text) {
			$scope.tools = $scope.tools.filter(function(t) {
				return text != t;
			});
		}
		$scope.jobSave = function() {
			var job = {
				title: $scope.value("#jobTitle"),
				company: $scope.value("#jobCompany"),
				location: $scope.value("#jobLocation"),
				role: $scope.value("#jobRole"),
				experience: $scope.value("#jobExperience"),
				tools: $scope.tools,
				description: $scope.value("#jobDescription"),
				code: $scope.value("#jobCode"),
				vacancies: $scope.value("#jobVacancies"),
				submit_by: $scope.value("#jobSubmitBy"),
				courses: $scope.value("#jobCourses")
			};
			$http.post(API + "/careers/new", {job: job})
				.then(function(response) {
					angular.element("#newCareer").modal("hide");
					job = response.data;
					$scope.careers.push(job);
					$.snackbar({content: 'New career added!', timeout: 3000}).snackbar("show");
				}, function() {

				})
		}
		$scope.jobDelete = function(jobId) {
			$http.delete(API + "/careers/delete?id=" + jobId)
				.then(function(response) {
					$scope.careers = $scope.careers.filter(function(c) {
						return c.id != jobId;
					});
					$.snackbar({content: 'Career deleted!', timeout: 3000}).snackbar("show");
				}, function() {

				})
		}
		$scope.jobView = function(jobId) {
			var job = $scope.careers.filter(function(c) {
				return c.id == jobId;
			})[0];
			$scope.element("#vJobTitle").val(job.title);
			$scope.element("#vJobHtml").html(job.html);
			$scope.element("#vJobPreview").html(job.preview);
			$scope.element("#viewCareer").modal("show");
		}
		$scope.value = function(selector, value = null) {
			var e = $scope.element(selector);
			var tagName = e[0].tagName;
			if (tagName == "INPUT" || tagName == "SELECT") {
				if (value === null) {
					return e.val();
				}
				return e.val(value);
			}
			if (value === null) {
				return e.html();
			}
			return e.html(value);
		}
		$scope.element = function(selector) {
			return angular.element(selector);
		}
		$scope.copy = function(selector) {
			var text = $scope.value(selector);
			var copyTA = $scope.value("#copyDest", text).removeClass("hidden");
			copyTA.select();
			document.execCommand("copy");
			$scope.element("#copyDest").addClass("hidden");
		}
	})
	.controller("notificationsCtrl", function($scope, $rootScope, $routeParams, response) {
		$rootScope.title = "Notifications";
		$scope.sc = response.data.sc;
		$scope.scp = response.data.scp;
		$scope.in = response.data.in;
		$scope.inp = response.data.inp;
		$scope.editingN = "";
		$scope.scIds = response.data.scIds;
		$scope.inIds = response.data.inIds;
		$scope.activeTab = 0;
		$scope.vm = $scope;
		$scope.contentLoaded = function() {
			var id = $routeParams.id;
			if (id) {
				if ($scope.inIds.indexOf(id) >= 0) {
					$scope.activeTab = 1;
					angular.element("#n-" + id).addClass("hover-anchor").click().removeClass("hover-anchor");
				}
				else if ($scope.scIds.indexOf(id) >= 0) {
					angular.element("#n-" + id).addClass("hover-anchor").click().removeClass("hover-anchor");
				}
			}
		};
		$scope.showNotification = function(n) {
			$scope.editingN = n;
			$scope.editableN = JSON.parse(JSON.stringify(n));
			$scope.editableN.saveState = 0;
			angular.element("#showNotification").modal("show");
		}
		$scope.saveNotification = function() {
			if ($scope.editableN.saveState == 0) {
				$scope.editableN.saveState = 1;
			}
			else if ($scope.editableN.saveState == 1) {
				$scope.editableN.saveState = undefined;
				angular.element("#showNotification").modal("hide");
			}
		}
	})
	.controller("coursesCtrl", function($rootScope, $scope, $http, $location, response) {
		$rootScope.title = "Courses";
		$scope.courses = response.data.courses;
		$scope.noCodeCount = response.data.noCodeCount;
		$scope.resultsPerPage = "10";
		$scope.currentPage = 1;
		($scope.resultsPerPageChange = function() {
			$scope.totalPages = Math.ceil($scope.courses.length / $scope.resultsPerPage);
		})();
		$scope.noCode = false;
		$scope.newCourse = {
			invalid: 0,
			duration_unit: "months",
			no_show: 0
		};
		$scope.resultsByRange = function(val) {
			var index = $scope.courses.indexOf(val);
			return (index >= ($scope.currentPage - 1) * $scope.resultsPerPage && index < $scope.currentPage * $scope.resultsPerPage)  && !$scope.noCode || ($scope.noCode && !val.sis_id);
		}
		$scope.pagesByRange = function(val) {
			var currPage = $scope.currentPage;
			var totalPages = $scope.totalPages;
			if (currPage < 6) {
				return (val <= 10 ? true : false);
			}
			if (currPage >= 6 && currPage <= totalPages) {
				return ((currPage - val <= 5 && val - currPage <= 5) ? true : false);
			}
			if (currPage > totalPages - 6) {
				return (val >= totalPages - 10 ? true : false);
			}
		}
		$scope.range = function(min, max) {
			var arr = [];
			max = Math.ceil(max);
			for (var i = min; i <= max; i++)
				arr.push(i);
			return arr;
		}
		$scope.pageChange = function(pageNum) {
			$scope.currentPage = pageNum;
		}
		$scope.noCodeToggle = function() {
			$scope.totalPages = Math.ceil(($scope.noCode ? $scope.noCodeCount : $scope.courses.length) / $scope.resultsPerPage);
		}
		$scope.courseCodeChanged = function() {
			if ($scope.newCourse.sis_id) {
				layoutOverlay.show(true, true);
				$scope.newCourse.sis_id_tip_class = undefined;
				$http.get(API + "/catalogue/courses?jlc=true&sis_id=" + $scope.newCourse.sis_id)
					.then(function(response) {
						layoutOverlay.hide();
						$scope.newCourse.name = response.data.course.name;
						if (response.data.course_id) {
							$scope.newCourse.sis_id_tip = "Course with this code already exists in the system";
							$scope.newCourse.sis_id_tip_class = "red";
							$scope.newCourse.invalid = 1;
						}
						else {
							$scope.newCourse.sis_id_tip = "Course with this code found in JLC";
						}
					}, function(response) {
						$scope.newCourse.name = "";
						layoutOverlay.hide();
						$scope.newCourse.sis_id_tip = "Course does not exist in JLC";
					})
			}
		}
		$scope.saveCourse = function() {
			$http.post(API + "/catalogue/courses/new", {course: $scope.newCourse})
				.then(function(response) {
					$scope.courses.push(response.data.course);
					angular.element("#newCourse").modal("hide");
					$.snackbar({content: response.data.course.name + " has been created!"});
					$location.url("/courses/" + response.data.course.course_id);
				}, function(response) {

				});
		}
	})
	.controller("courseCtrl", function($rootScope, $scope, $http, response) {
		$scope.course = response.data.course;
		$scope.edit = false;
		$rootScope.title = $scope.course.name;
		$http.get(API + "/catalogue/courses/sections?id=" + $scope.course.course_id)
			.then(function(response) {
				$scope.sections = response.data.sections;
				angular.element("#componentLoader").removeClass("show");
			}, function(response) {

			});
		$scope.showSaveCourse = function() {
			angular.element('#saveCourse').modal('show');
		}
		$scope.saveCourse = function() {
			// console.log($scope.course);return;
			$http.post(API + "/catalogue/courses/edit", {course: $scope.course})
				.then(function(response) {
					$scope.course = response.data.course;
					$.snackbar({content: $scope.course.name + " updated!"});
					angular.element("#saveCourse").modal("hide");
					$scope.edit = false;
				});
		}
	})
	.controller("programsCtrl", function($rootScope, $scope, $http, $location, response) {
		$rootScope.title = "Programs";
		$scope.programs = response.data.programs;
		$scope.count = response.data.count;
		$scope.resultsPerPage = "10";
		$scope.currentPage = 1;
		($scope.resultsPerPageChange = function() {
			$scope.totalPages = Math.ceil($scope.programs.length / $scope.resultsPerPage);
		})();
		$scope.noCode = false;
		$scope.newProgram = {
			invalid: 0,
			subs_duration_unit: "months",
			bundle_type: "0"
		};
		$scope.resultsByRange = function(val) {
			var index = $scope.programs.indexOf(val);
			return (index >= ($scope.currentPage - 1) * $scope.resultsPerPage && index < $scope.currentPage * $scope.resultsPerPage);
		}
		$scope.pagesByRange = function(val) {
			var currPage = $scope.currentPage;
			var totalPages = $scope.totalPages;
			if (currPage < 6) {
				return (val <= 10 ? true : false);
			}
			if (currPage >= 6 && currPage <= totalPages) {
				return ((currPage - val <= 5 && val - currPage <= 5) ? true : false);
			}
			if (currPage > totalPages - 6) {
				return (val >= totalPages - 10 ? true : false);
			}
		}
		$scope.range = function(min, max) {
			var arr = [];
			max = Math.ceil(max);
			for (var i = min; i <= max; i++)
				arr.push(i);
			return arr;
		}
		$scope.pageChange = function(pageNum) {
			$scope.currentPage = pageNum;
		}
		$scope.saveProgram = function() {
			$http.post(API + "/catalogue/programs/new", {program: $scope.newProgram})
				.then(function(response) {
					// $scope.programs[response.data.program.bundle_type].push(response.data.program);
					angular.element("#newProgram").modal("hide");
					$.snackbar({content: response.data.program.name + " has been created!"});
					$location.url("/programs/" + response.data.program.bundle_id);
				}, function(response) {

				});
		}
	})
	.controller("programCtrl", function($rootScope, $scope, $http, response,$filter) {
		$scope.program = response.data.program;
		$scope.edit = false;
		$rootScope.title = $scope.program.name;
		/*****Start Add Batches *******/
		$scope.batch_name='';
		$scope.batch_code='';
		$scope.batch_start_date='';
		$scope.batch_end_date='';
		$scope.price_inr='';
		$scope.price_usd='';

		$scope.batchDetails='';
		/*****End Add Batches   *****/
		$http.get(API + "/catalogue/courses?components=true")
			.then(function(response) {
				$scope.courses = response.data.courses;
				angular.element("#componentLoader").removeClass("show");
			}, function(response) {

			});
		$http.get(API + "/catalogue/programs/batches?id="+$scope.program.bundle_id)
			.then(function(response) {
				$scope.batches = response.data.batches;
				angular.element("#batchLoader").removeClass("show");
			}, function(response) {

			});
		$scope.showSaveProgram = function() {
			angular.element('#saveProgram').modal('show');
		}
		$scope.saveProgram = function() {
			// console.log($scope.program);
			$http.post(API + "/catalogue/programs/edit", {program: $scope.program})
				.then(function(response) {
					$scope.program = response.data.program;
					$.snackbar({content: $scope.program.name + " updated!"});
					angular.element("#saveProgram").modal("hide");
					$scope.edit = false;
				});
		}

		/*****Start Add Batches *******/
		$scope.showAddBatches =  function() {
		  
			angular.element('#showAddBatches').modal('show');
		}
		$scope.saveBatches =  function() { 
				  var formatedDate = $scope.batch_start_date;
				  formatedDate.setDate(formatedDate.getDate());
				  var startDate = $filter('date')(formatedDate, "yyyy-MM-dd");
				  var formatedEndDate = $scope.batch_end_date;
				  formatedEndDate.setDate(formatedEndDate.getDate());
				  var endDate = $filter('date')(formatedEndDate, "yyyy-MM-dd");
			      var bundle = {};
				   bundle = {
					'name':$scope.batch_name,
					'code':$scope.batch_code,
					'price':$scope.price_inr,
					'price_usd':$scope.price_usd,
					'batch_start_date':startDate,
					'batch_end_date':endDate,
					'bundle_id':$scope.program.bundle_id,
				};
				
				$http.post(API+"/course.bundle.import", {bundle})
				.then(function(response) { console.log(response.data.message);
				    $.snackbar({content: response.data.message});
					 angular.element("#showAddBatches").modal("hide");
				});
		}
	   /*****End Add Batches *******/
	   
	   /*****Start List Batches *******/
		$http.get(API + "/bcBatch.list?bundle_id="+$scope.program.bundle_id)
			.then(function(response) {
				$scope.batchDetails = response.data.data;
			}, function(response) {
		});
	  /*****End List Batches *******/
	})
	.controller("settingsCtrl", function($rootScope, $scope, $http, $window, AuthService) {
		$rootScope.title = "Settings";
		$rootScope.user = AuthService.user();
		$rootScope.preferences = AuthService.preferences();
		$scope.dataChanged = function() {
			$http.post(API + "/identity/profile", {user: $scope.user, preferences: $scope.preferences})
				.then(function(response) {
					AuthService.user($scope.user);
					AuthService.preferences($scope.preferences);
					$window.batBar.add({content: "Changes saved!"});
				});
		}
	})
	.controller("studentsCtrl", function($rootScope, $scope, $http, response) {
		$rootScope.title = "Students";
		if (angular.element("#sideNav").hasClass("fixed")) {
			angular.element("#bottomPanel").css("padding-left", "220px");
		}
		$scope.api = API;
		$scope.order = false;
		$scope.sortDate = function(student) {
			return new Date(student.start_date);
		}
		$scope.setMonthlyBatches = function() {
			var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September",  "October",  "November",  "December"];
			var years = [2015, 2016, 2017, 2018, 2019, 2020, 2021, 2022];
			$batches = [];
			years.forEach(function(e, i) {
				months.forEach(function(f, j) {
					$batches.push({id: "n" + (100*(i + 1) + j), name: f + ", " + e});
				});
			});
			return $batches;
		}
		$scope.students = response.data.students;
		$scope.bundles = response.data.bundles;
		$scope.courses = response.data.courses;
		$scope.criteria = response.data.criteria;
		$scope.resultsPerPage = "10";
		$scope.currentPage = 1;
		($scope.resultsPerPageChange = function() {
			$scope.totalPages = Math.ceil($scope.students.length / $scope.resultsPerPage);
		})();
		$scope.resultsByRange = function(val) {
			var index = $scope.students.indexOf(val);
			if ($scope.order) {
				index = $scope.students.length - index;
			}
			return (index >= ($scope.currentPage - 1) * $scope.resultsPerPage && index < $scope.currentPage * $scope.resultsPerPage);
		}
		$scope.pagesByRange = function(val) {
			var currPage = $scope.currentPage;
			var totalPages = $scope.totalPages;
			if (currPage < 6) {
				return (val <= 10 ? true : false);
			}
			if (currPage >= 6 && currPage <= totalPages) {
				return ((currPage - val <= 5 && val - currPage <= 5) ? true : false);
			}
			if (currPage > totalPages - 6) {
				return (val >= totalPages - 10 ? true : false);
			}
		}
		$scope.range = function(min, max) {
			var arr = [];
			max = Math.ceil(max);
			for (var i = min; i <= max; i++)
				arr.push(i);
			return arr;
		}
		$scope.pageChange = function(pageNum) {
			$scope.currentPage = pageNum;
		}
		$scope.batches = [];
		$scope.toggleBottomPanel = function() {
			var bp = angular.element("#bottomPanel");
			var oo = angular.element("#optionsOverlay");
			if (bp.hasClass("lowered")) {
				bp.removeClass("lowered");
				bp.find("i.material-icons").html("expand_more");
				oo.addClass("show");
			}
			else {
				bp.addClass("lowered");
				bp.find("i.material-icons").html("expand_less");
				oo.removeClass("show");
			}
		}
		$scope.applyCriteria = function() {
			$http.post(API + "/students", {criteria: $scope.criteria})
				.then(function(response) {
					$scope.students = response.data.students;
					$scope.resultsPerPageChange();
					$scope.toggleBottomPanel();
				}, function(response) {
					$.snackbar({content: "Something went wrong... Please try again later."});
				});
		}
		$scope.resetCriteria = function() {
			$scope.criteria = {
				from_start_date: false,
				to_start_date: false,
				from_end_date: false,
				to_end_date: false,
				as: false,
				we: false,
				iot: false,
				catalogue: "0",
				batch: "n0"
			}
			$http.post(API + "/students", {criteria: $scope.criteria})
				.then(function(response) {
					$scope.students = response.data.students;
					$scope.resultsPerPageChange();
					$scope.toggleBottomPanel();
				}, function(response) {
					$.snackbar({content: "Something went wrong... Please try again later."});
				});
		}
		$scope.catalogueChange = function() {
			if ($scope.criteria.catalogue[0] == "b") {
				$scope.batches = $scope.bundles.bootcamps[$scope.criteria.catalogue.substr(1)].batches;
			}
			else {
				$scope.batches = $scope.setMonthlyBatches();
			}
		}
	})
	.controller("mobileCtrl", function($rootScope, $scope, $http, stats, form) {
		$rootScope.title = "Mobile";
		$scope.stats = stats.data;
		$scope.forms = [form.data.notifications];
		// $scope.forms = form.data.forms;
	})
	.controller("leadCtrl", function($rootScope, $scope, $http, response) {
		$scope.lead = response.data.lead;
		$rootScope.title = "Lead - " + $scope.lead.name;
		$scope.records = response.data.records;
		$scope.activities = response.data.activities;
		$scope.crm = response.data.crm;
	})
	.controller("salesNewCtrl", function($rootScope, $scope, $http, response) {
		$rootScope.title = "New Order";
		$scope.categories = response.data.categories;
		$scope.order = {
			components: [],
			price: 0.00,
			originalPrice: 0.00,
			free: 0.00,
			inr: true,
			discount: 0.00,
			instlDiff: 0.00,
			installments: [
				{
					sum: 0.00,
					due: 0,
					sumPreserved: 0.00
				}
			]
		};
		$scope.addComponent = function(component) {
			if (component.chosen) {
				component.order_price = component["price_" + ($scope.order.inr ? "inr" : "usd")];
				$scope.order.components.push(component);
				$scope.order.originalPrice += component.order_price;
			}
			else {
				$scope.order.components.splice($scope.order.components.indexOf(component), 1);
				$scope.order.originalPrice -= component.order_price;
				component.order_price = undefined;
			}
			$scope.updateCartPrice();
		}
		$scope.updateCart = function(component = false) {
			if (component !== false) {
				if (component.free) {
					$scope.order.free += component.order_price;
					component.order_price = 0;
				}
				else {
					component.order_price = component["price_" + ($scope.order.inr ? "inr" : "usd")];
					$scope.order.free -= component.order_price;
				}
				$scope.updateCartPrice();
				return;
			}
			var toRemove = [];
			$scope.order.price = 0;
			$scope.order.originalPrice = 0;
			$scope.order.free = 0;
			$scope.order.installments = [
				{
					sum: 0.00,
					due: 0,
					sumPreserved: 0.00
				}
			];
			$scope.order.components.forEach(function(e) {
				if ($scope.order.inr) {
					if (!e.price_inr) {
						e.chosen = false;
						e.order_price = undefined;
						toRemove.push(e);
					}
					else {
						if (e.free) {
							$scope.order.free += e.price_inr;
						}
						else {
							e.order_price = e.price_inr;
						}
						$scope.order.originalPrice += e.price_inr;
					}
				}
				else {
					if (!e.price_usd) {
						e.chosen = false;
						e.order_price = undefined;
						toRemove.push(e);
					}
					else {
						if (e.free) {
							$scope.order.free += e.price_usd;
						}
						else {
							e.order_price = e.price_usd;
						}
						$scope.order.originalPrice += e.price_usd;
					}
				}
			});
			if (toRemove.length > 0) {
				$scope.order.components = $scope.order.components.filter(function(e) {
					if (toRemove.indexOf(e) < 0) {
						return e;
					}
				});
			}
			$scope.order.discount = 0;
			$scope.updateCartPrice();
		}
		$scope.updateCartPrice = function() {
			var difference = $scope.order.price - $scope.order.originalPrice + $scope.order.discount + $scope.order.free;
			$scope.order.price = $scope.order.originalPrice - $scope.order.discount - $scope.order.free;
			var noOfInstls = $scope.order.installments.length;
			var i = 2;
			var lastInstallment = $scope.order.installments[noOfInstls - 1];
			while (lastInstallment.sum <= 0 && noOfInstls > 1) {
				lastInstallment = $scope.order.installments[noOfInstls - i];
				i++;
			}
			lastInstallment.sum -= difference;
			lastInstallment.sumPreserved -= difference;
		}
		$scope.updateInstallmentChange = function(index, instl) {
			var difference = instl.sum - instl.sumPreserved;
			instl.sumPreserved = instl.sum;
			var noOfInstls = $scope.order.installments.length;
			var offset = noOfInstls;
			if (index == $scope.order.installments.length - 1) {
				if (index == 0) {
					instl.sumPreserved = instl.sum;
					$scope.order.instlDiff = Math.abs(difference);
					return;
				}
				offset = index;
			}
			var i = 2;
			var lastInstallment = $scope.order.installments[offset - 1];
			if (lastInstallment.sum == 0) {
				lastInstallment.sum = Math.abs(difference);
			}
			else {
				lastInstallment.sum -= difference;
			}
			lastInstallment.sumPreserved = lastInstallment.sum;
		}
	})
	.controller("bootcampBatchCtrl", function($rootScope, $scope, response) {
		$scope.batch = response.data.batch;
	})
	.controller("ordersCtrl", function($rootScope, $scope, response) {
		$rootScope.title = "Orders";
		$scope.orders = response.data;
		$scope.statusClass = {
			unpaid: 'text-danger',
			paid: 'text-success',
			partial: 'text-muted'
		}
	})
	.controller("orderCtrl", function($rootScope, $scope, $http, response) {
		$scope.order = response.data;
		if ($scope.order.id == 0) {
			$rootScope.title = "New Order";
		}
		else {
			$rootScope.title = "View Order";
		}
		$scope.order.errors = {
			payment: false,
			installments: {due_date: false},
			email: false
		};
		$scope.order.payment.installments.forEach(function(e) {
			if (e.due_by) {
				e.due_by = new Date(e.due_by);
			}
		})
		$scope.email = $scope.order.user.email;
		$scope.recalculateInstl = function(instl) {
			if (instl.amount <= 0 || isNaN(instl.amount)) {
				instl.amount = 0;
			}
			if (instl.discount <= 0 || isNaN(instl.discount)) {
				instl.discount = 0;
			}
			if (instl.instl_fees <= 0 || isNaN(instl.instl_fees)) {
				instl.instl_fees = 0;
			}
			var total = instl.total;
			instl.total = instl.amount - instl.discount + instl.instl_fees;
			if (instl.tax > 0) {
				instl.total += instl.total * (instl.tax) / 100;
			}
			instl.total = Number(instl.total.toFixed(2));
			// instl.total += instl.instl_fees;
			$scope.order.payment.total += instl.total - total;
			$scope.order.errors.payment = false;
		}
		$scope.fetchUser = function() {
			if ($scope.email == $scope.order.user.email) {
				return;
			}
			$http.get(API + "/users?email=" + encodeURI($scope.order.user.email))
				.then(function(res) {
					$scope.order.user.name = res.data.name;
					$scope.order.user.phone = res.data.phone;
					$scope.order.errors.email = false;
				}, function(res) {
					$scope.order.errors.email = false;
				});
		}
		$scope.toggleCurrency = function() {
			if ($scope.order.payment.currency == "inr") {
				$scope.order.payment.currency = "usd";
			}
			else {
				$scope.order.payment.currency = "inr";
			}
		}
		$scope.saveOrder = function() {
			if (!$scope.order.user.email) {
				$scope.order.errors.email = true;
				return;
			}
			if (!$scope.validateOrder()) {
				return;
			}
			$http.post(API + "/orders?id=" + $scope.order.id, {order: $scope.order})
				.then(function(res) {
					$scope.order = res.data;
					angular.element("#orderSummary").modal("show");
				}, function(res) {

				});
		}
		$scope.validateOrder = function() {
			var res = true;
			var instls = 0;
			$scope.order.payment.installments.forEach(function(e) {
				if (e.total > 0) {
					if (!e.due_by) {
						$scope.order.errors.installments.due_date = true;
						res = false;
					}
					instls++;
				}
			});
			if (instls == 0) {
				$scope.order.errors.payment = true;
			}
			return res;
		}
		$scope.copyLink = function() {
			angular.element("#orderToken").select();
			document.execCommand("Copy");
		}
		$scope.viewComments = function(comments) {
			$scope.comments = comments;
			angular.element("#viewComments").modal("show");
		}
		$scope.viewPayment = function(instl) {
			$scope.paymentMeta = JSON.stringify(instl.channel_meta, undefined, 4);
			angular.element("#viewPaymentInfo").modal("show");
		}
	})
	.controller("paymentsCtrl", function($rootScope, $scope, $http) {
		$rootScope.title = "Payments";
		$scope.pay = [];
		$scope.format = {
			decoration: ['th', 'st', 'nd', 'rd'],
			number: function(n) {
				if (n > 3) {
					n = 0;
				}
				return this.decoration[n];
			}
		}
		$scope.jlc = {
			color: function(status) {
				if (status.user == "registered") {
					if (status.pseudonym == "active") {
						return "success";
					}
					return "warning";
				}
				else {
					return "danger";
				}
			},
			status: function(status) {
				if (status.user == "registered") {
					return status.pseudonym;
				}
				return status.user;
			}
		}
		$scope.overdue = function() {
			$http.get(API + "/payments/overdue")
				.then(function(res) {
					$scope.pay = res.data;
				}, function(res) {

				});
		}
	})
	.controller("labsCtrl", function($rootScope, $scope, response, labsService) {
		$rootScope.title = "Labs";
		$scope.courseLabs = response.data.courseLabs;
		$scope.labs = response.data.labs;
		$scope.canEdit = response.data.edit;
		$scope.defaults = response.data.defaults;
		angular.element("#newAmi").on("modal-hide", function() {
			$scope.lab.reset();
		});
		angular.element("#newCourseLab").on("modal-hide", function() {
			$scope.courseLab.reset();
		});
		$scope.lab = {
			obj: {},
			saving: false,
			reset: function() {
				$scope.lab.define({ami_id: $scope.defaults.AWS_IMAGE_ID, meta: $scope.defaults, type: 'Linux'});
			},
			show: function(lab) {
				$scope.lab.define(lab);
				angular.element("#newAmi").modal("show");
			},
			define: function(lab) {
				$scope.lab.obj = {
					ami_id: lab.ami_id,
					type: lab.type,
					meta: {
						AWS_KEY_NAME: lab.meta.AWS_KEY_NAME,
						AWS_SUBNETID: lab.meta.AWS_SUBNETID,
						AWS_INSTANCE_TYPE: lab.meta.AWS_INSTANCE_TYPE,
						AWS_SECURITY_GROUP_ID: lab.meta.AWS_SECURITY_GROUP_ID,
						AWS_INSTANCE_MIN_COUNT: lab.meta.AWS_INSTANCE_MIN_COUNT,
						AWS_INSTANCE_MAX_COUNT: lab.meta.AWS_INSTANCE_MAX_COUNT
					}
				}
				if (!!lab.id) {
					$scope.lab.id = lab.id;
				}
			},
			invalid: function() {
				if (!$scope.lab.obj.ami_id.length) {
					return true;
				}
				for (var key in $scope.lab.obj.meta) {
					if (key == "AWS_INSTANCE_MIN_COUNT" || "AWS_INSTANCE_MAX_COUNT") {
						if ($scope.lab.obj.meta[key] <= 0) {
							return true;
						}
					}
					else if (!$scope.lab.obj.meta[key].length) {
						return true;
					}
				}
			},
			save: function() {
				$scope.lab.saving = true;
				labsService.create.lab($scope.lab.obj)
					.then(function(res) {
						$scope.lab.saving = false;
						$scope.labs[res.data.id] = res.data;
						angular.element("#newAmi").modal("hide");
					}, function(res) {
						$scope.lab.saving = false;
					});
			}
		}
		$scope.lab.reset();
		$scope.courseLab = {
			obj: {},
			saving: false,
			reset: function() {
				$scope.courseLab.define({code: "", name: "", lab_id: 0, lifespan: 10800});
			},
			show: function(courseLab) {
				$scope.courseLab.define(courseLab);
				angular.element("#newCourseLab").modal("show");
			},
			define: function(courseLab) {
				$scope.courseLab.obj = {
					code: courseLab.code,
					name: courseLab.name,
					lab_id: courseLab.lab_id,
					lifespan: courseLab.lifespan
				}
				if (!!courseLab.id) {
					$scope.courseLab.id = courseLab.id;
				}
			},
			invalid: function() {
				for (var key in $scope.courseLab.obj.meta) {
					if (key == "lifespan") {
						if ($scope.courseLab.obj.meta[key] <= 0) {
							return true;
						}
					}
					else if (!$scope.courseLab.obj.meta[key].length) {
						return true;
					}
				}
			},
			save: function() {
				$scope.courseLab.saving = true;
				labsService.create.courseLab($scope.courseLab.obj)
					.then(function(res) {
						$scope.courseLab.saving = false;
						if (!$scope.courseLab.obj.id) {
							$scope.courseLabs.push(res.data);
						}
						angular.element("#newCourseLab").modal("hide");
					}, function(res) {
						$scope.courseLab.saving = false;
					});
			}
		}
		$scope.courseLab.reset();
	})
	.controller("paymentExportCtrl", function($rootScope, $scope, paymentsService) {
		$rootScope.title = "Payments Export";
		$scope.export = {
			from: "",
			to: new Date,
			saving: false,
			submit: function(enr = true) {
				if ($scope.export.from > $scope.export.to) {
					$scope.export.error = true;
				}
				angular.element("#fromFinal").val($scope.date($scope.export.from));
				angular.element("#toFinal").val($scope.date($scope.export.to));
				angular.element("#exportFormHidden").attr("action", API + "/payments/exports/" + (enr ? "enr" : "app")).submit();
			},
			disable: function() {
				if ($scope.export.from > $scope.export.to) {
					return true;
				}
				return $scope.export.saving;
			}
		}
		$scope.date = function(date) {
			var format = n => {
				return n < 10 ? "0" + n : n;
			}
			return date.getFullYear() + "-" + format(date.getMonth() + 1) + "-" + format(date.getDate());
		}
	})
	.controller("applicationsCtrl", function($rootScope, $scope, response, applicationsService,$timeout,$window) {
		$rootScope.title = "Applications"; 
		$scope.filter = {
			text: "",
			form_name: "all",
			lead_status: "all",
			enroll_status: "all",
			filter: a => {
				let text = $scope.filter.text == "" ? true : (a.email.indexOf($scope.filter.text) > -1 || a.name.indexOf($scope.filter.text) > -1);
				let form_name = $scope.filter.form_name == "all" ? true : a.form_name == $scope.filter.form_name;
				let lead_status = $scope.filter.lead_status == "all" ? true : a.lead_status == $scope.filter.lead_status;
				let enroll_status = $scope.filter.enroll_status == "all" ? true : a.main_payment.length + "" == $scope.filter.enroll_status;
				return text && form_name && lead_status && enroll_status;
			}
		};
		$scope.setPaginationParams = function(response){
			$scope.totalPages = response.data.totalPages;
			$scope.applications = response.data.data;
			$scope.currentPage = parseInt(response.data.page);
			$scope.totalRecords = response.data.totalRecords;
			$scope.counter = response.data.counter;
		}


		if (!!response.data.error) {
			alert("Something went wrong... Retrying after a minute.");
		}
		else {
			$scope.setPaginationParams(response);
		}
		
		/****************Pagination******************/
		$scope.resultsPerPage = $scope.applications.length;
		($scope.resultsPerPageChange = function() {
			$scope.totalPages = $scope.totalPages;
		})();
		$scope.itemsPerPage = 100;
		$scope.pagesByRange = function(val) {
			var currPage = $scope.currentPage;
			var totalPages = $scope.totalPages;
			if (currPage < 6) {
				return (val <= 10 ? true : false);
			}
			if (currPage >= 6 && currPage <= totalPages) {
				return ((currPage - val <= 5 && val - currPage <= 5) ? true : false);
			}
			if (currPage > totalPages - 6) {
				return (val >= totalPages - 10 ? true : false);
			}
		}
		$scope.range = function(min, max) {
			var arr = [];
			for (var i = min; i <= max; i++)
				arr.push(i);
			return arr;
		}
		$scope.pageChange = function(pageNum) {  
			$window.layoutOverlay.show(true);
			$scope.currentPage = pageNum;
			$scope.apiCall($scope.currentPage)
			.then(function(response) { console.log($scope.currentPage);
				$window.layoutOverlay.hide();
				$scope.setPaginationParams(response);
				}, function(err) {
					alert("Something went wrong... Retrying after a minute.");
					$window.location.reload();
				});			
		}	

		$scope.apiCall = function(pageNum){
			return applicationsService.all(pageNum);
		}


		/********************************* */
		
		
		$scope.showApplication = app => {
			$scope.application = app;
			$scope.error = "";
			angular.element("#application").modal("show");
		}
		$scope.saveApplication = e => {
			e.preventDefault();
			applicationsService.save($scope.application)
				.then(res => {
					$scope.application.id = res.data.id;
					$scope.application.agent_name = res.data.agent_name;
					angular.element("#application").modal("hide");
				}, res => {
					$scope.error = "Something went wrong...";
					if (!!res.data.error) {
						$scope.error = res.data.error;
					}
				});
		}
	});