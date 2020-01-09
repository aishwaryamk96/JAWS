app.controller("sectionCtrl", function($scope, $http, $routeParams, $compile, $window, $timeout) {
	$scope.custom_body_class = "custom-section";
	$scope.processing = true;
	$http.get(JAWS_PATH_API + "/section?id=" + $routeParams.query)
		.then(
			function success(response) {
				$scope.section = response.data;
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
	$scope.expandTopic = function(topicId) {
		if (angular.element('#topic-'+topicId).val() == 1) {
			angular.element('#topic-'+topicId).val("");
		}
		else {
			angular.element('#topic-'+topicId).val(1);
		}
	}
});