/* ============================================================
 * File: config.js
 * Configure routing
 * ============================================================ */

angular.module('fabric')
	.config(['$stateProvider', '$urlRouterProvider', '$ocLazyLoadProvider',

		function($stateProvider, $urlRouterProvider, $ocLazyLoadProvider) {
			$urlRouterProvider
				.otherwise('/env/dash');

			$stateProvider
				.state('env', {
					abstract: true,
					url: "/env",
					templateUrl: _JAWS_PATH_VIEWS + "pages/env" + _JAWS_DEV_APPEND,
					controller: 'envCtrl',
					resolve: {
						deps: ['$ocLazyLoad', function($ocLazyLoad) {
							return $ocLazyLoad.load([], {
									insertBefore: '#lazyload_placeholder'
								})
								.then(function() {
									return $ocLazyLoad.load([
										_JAWS_PATH_TEMPLATES + 'app/controllers/env.js' + _JAWS_DEV_APPEND
									]);
								});
						}]
					}
				})

				.state('env.dash', {
					url: "/dash",
					views: {
						'ext' : {
							templateUrl: _JAWS_PATH_VIEWS + "sections/header-ext-dash" + _JAWS_DEV_APPEND,
							controller: 'dashExtCtrl'
						},
						'page' : {
							templateUrl: _JAWS_PATH_VIEWS + "pages/dash" + _JAWS_DEV_APPEND,
							controller: 'dashCtrl'
						}
					},
					resolve: {
						deps: ['$ocLazyLoad', function($ocLazyLoad) {
							return $ocLazyLoad.load([], {
									insertBefore: '#lazyload_placeholder'
								})
								.then(function() {
									return $ocLazyLoad.load([
										_JAWS_PATH_TEMPLATES + 'app/controllers/dash.js' + _JAWS_DEV_APPEND,
										_JAWS_PATH_TEMPLATES + 'app/controllers/header-ext-dash.js' + _JAWS_DEV_APPEND
									]);
								});
						}]
					}
				})

				.state('env.course', {
					url: "/course",
					views: {
						'ext' : {
							templateUrl: _JAWS_PATH_VIEWS + "sections/header-ext-course" + _JAWS_DEV_APPEND,
							controller: 'courseExtCtrl'
						},
						'page' : {
							templateUrl: _JAWS_PATH_VIEWS + "pages/course" + _JAWS_DEV_APPEND,
							controller: 'courseCtrl'
						}
					},
					resolve: {
						deps: ['$ocLazyLoad', function($ocLazyLoad) {
							return $ocLazyLoad.load([], {
									insertBefore: '#lazyload_placeholder'
								})
								.then(function() {
									return $ocLazyLoad.load([
										_JAWS_PATH_TEMPLATES + 'app/controllers/course.js' + _JAWS_DEV_APPEND,
										_JAWS_PATH_TEMPLATES + 'app/controllers/header-ext-course.js' + _JAWS_DEV_APPEND
									]);
								});
						}]
					}
				})

				.state('env.topic', {
					url: "/topic",
					views: {
						'ext' : {
							templateUrl: _JAWS_PATH_VIEWS + "sections/header-ext-course" + _JAWS_DEV_APPEND,
							controller: 'courseExtCtrl'
						},
						'topic' : {
							templateUrl: _JAWS_PATH_VIEWS + "pages/topic" + _JAWS_DEV_APPEND,
							controller: 'topicCtrl'
						}
					},
					resolve: {
						deps: ['$ocLazyLoad', function($ocLazyLoad) {
							return $ocLazyLoad.load([], {
									insertBefore: '#lazyload_placeholder'
								})
								.then(function() {
									return $ocLazyLoad.load([
										_JAWS_PATH_TEMPLATES + 'app/controllers/topic.js' + _JAWS_DEV_APPEND,
										_JAWS_PATH_TEMPLATES + 'app/controllers/header-ext-course.js' + _JAWS_DEV_APPEND
									]);
								});
						}]
					}
				});
		}
	]);
