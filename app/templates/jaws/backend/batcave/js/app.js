app.controller("homeCtrl", function($scope, $http, $location, $window) {
	angular.element('#search-text').focus();
	$scope.focus = true;
	$scope.profile_menu = 0;
	$scope.logout = function() {
		$window.location.href = JAWS_PATH_WEB + "/logout";
	}
	$scope.search = function() {
		$location.url("search/" + encodeURIComponent($scope.query));
	}
	$scope.searchClear = function() {
		angular.element('#search-text').focus();
		$scope.query='';
	}
	$scope.openLink = function(anchor) {
		$location.url(anchor);
	}
});