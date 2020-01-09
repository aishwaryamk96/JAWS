/* ============================================================
 * File: main.js
 * Main Controller to set global scope variables.
 * ============================================================ */

angular.module('fabric')
	.controller('rootCtrl', ['$scope', '$rootScope', '$state', function($scope, $rootScope, $state) {

		// App globals
		$scope.app = {
			name: 'Jigsaw Learning Centre',
			jawsPath: _JAWS_PATH,
			jawsPathTemplates: _JAWS_PATH_TEMPLATES,
			jawsPathViews: _JAWS_PATH_VIEWS,
			jawsPathAPI: _JAWS_PATH_API,
			jawsPathWeb: _JAWS_PATH_WEB,
			jawsDevAppend: _JAWS_DEV_APPEND
		}

		// Environment globals
		$scope.env = {
			header: {
				ext: {
					allow: true,
					default: true
				}
			}
		}

		// User Globals
		$scope.user = _JAWS_USER;

		// Checks if the given state is the current state
		$scope.is = function(name) {
			return $state.is(name);
		};

		// Checks if the given state/child states are present
		$scope.includes = function(name) {
			return $state.includes(name);
		};


	}]);


angular.module('fabric')
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
