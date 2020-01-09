app.controller("referCtrl", function($scope, $http, $routeParams, $compile, $window, $timeout) {
	$scope.processing = true;
	$http.get(JAWS_PATH_API + "/refer/all" + (!!$routeParams.query ? "?user=" + $routeParams.query : ""))
		.then(
			function success(response) {
				$scope.referrals = response.data.refer;
				$scope.custom_body_css = true;
				$scope.processing = false;
			},
			function error(response) {
				if (response.status == 401) {
					$window.location.reload();
				}
				else {
					alert("Something went wrong...");
					$scope.processing = false;
				}
			}
		);
});