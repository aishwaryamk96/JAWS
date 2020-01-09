app.controller("referCtrl", function($scope, $http, $routeParams, $compile, $window, $timeout) {
	$scope.processing = true;
	$scope.count = { enrolled: 0, total: 0 };
	$scope.profileUrl = JAWS_PATH_WEB+"/batcave/#!/user/";
	$http.get(JAWS_PATH_API + "/refer/all" + (!!$routeParams.query ? "?user=" + $routeParams.query : ""))
		.then(
			function success(response) {
				$scope.referrals = response.data.refer;
				$scope.count = response.data.count;
				$scope.count.total = $scope.referrals.length;
				$scope.custom_body_css = true;
				$scope.processing = false;
				$scope.statusSelected = "";
				$scope.expirySelected = "";
				$scope.collapsed = true;
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
	$scope.export = function() {
		$window.open(JAWS_PATH_API + "/refer/all?export=1" + (!!$routeParams.query ? "&user=" + $routeParams.query : ""), "_blank");
	}
	$scope.expand = function(referId) {
		if (angular.element("#expander-"+referId).hasClass("expanded")) {
			angular.element("#expander-"+referId).removeClass("expanded");
			angular.element("#dates-"+referId).removeClass("no-collapse");
		}
		else {
			angular.element("#expander-"+referId).addClass("expanded");
			angular.element("#dates-"+referId).addClass("no-collapse");
		}
	}
});