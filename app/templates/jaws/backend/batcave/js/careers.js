app.controller("careersCtrl", function($scope, $http, $routeParams, $compile, $window, $timeout) {
	$scope.custom_body_class = "custom-course";
	$scope.processing = true;
	$scope.canDownload = false;
	$scope.canCreate = false;
	$http.get(JAWS_PATH_API + "/careers")
		.then(
			function success(response) {
				$scope.canDownload = response.data.canDownload;
				$scope.canCreate = response.data.canCreate;
				$scope.careers = response.data.careers;
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
	$scope.openModal = function(modalId) {
		angular.element("#"+modalId).addClass("show");
	}
	$scope.closeModal = function(modalId) {
		angular.element("#"+modalId).removeClass("show");
	}
});