/* ============================================================
 * File: main.js
 * Main Controller to set global scope variables.
 * ============================================================ */

angular.module('jaws')
	.controller('jawsCtrl', ['$scope', '$rootScope', '$state', 'apiSVC', function($scope, $rootScope, $state, apiSVC) {

		// App globals
		$scope.app = {
			name: 'JAWS - Home',
			description: 'Jigsaw Academy',
			layout: {
				menuPin: false,
				menuBehind: false,
				theme: _JAWS_PATH_TPL + 'pages/css/pages.css',
				editInstallment: _JAWS_PATH_TPL + 'assets/css/editInstallment.css'
			},
			author: 'BadGuppy',
			jawsPath: _JAWS_PATH,
			jawsPathTPL: _JAWS_PATH_TPL,
			jawsPathAPI: _JAWS_PATH_API,
			jawsPathAPIREST: _JAWS_PATH_API_REST,
			jawsDevAppend: _JAWS_DEV_APPEND
		}

		// User Globals
		$scope.user = _JAWS_USER;

		// Course Globals
		//$scope.course = _JAWS_COURSE;

		// Checks if the given state is the current state
		$scope.is = function(name) {
			return $state.is(name);
		}

		// Checks if the given state/child states are present
		$scope.includes = function(name) {
			return $state.includes(name);
		}

		// Broadcasts a message to pgSearch directive to toggle search overlay
		$scope.showSearchOverlay = function() {
			$scope.$broadcast('toggleSearchOverlay', {
				show: true
			})
		}

	}]);


angular.module('jaws')
	/*
		Use this directive together with ng-include to include a
		template file by replacing the placeholder element
	*/

	.directive('includeReplace', function() {
		return {
			require: 'ngInclude',
			restrict: 'A',
			link: function(scope, el, attrs) {
				el.replaceWith(el.children());
			}
		};
	})

	.directive('enterSubmit', function () {
    return function (scope, element, attrs) {
        element.bind("keydown keypress", function (event) {
            if(event.which === 13) {
                scope.$apply(function (){
                    scope.$eval(attrs.enterSubmit);
                });

                event.preventDefault();
            }
        });
    };
});
