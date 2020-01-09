app.controller("courseCtrl", function($scope, $http, $routeParams, $compile, $window, $timeout) {
	$scope.custom_body_class = "custom-course";
	$scope.processing = true;
	$scope.batchId = -1;
	$scope.batchName = "";
	$http.get(JAWS_PATH_API + "/course" + (!!$routeParams.query ? "?id=" + $routeParams.query : ""))
		.then(
			function success(response) {
				$scope.course = response.data;
				$scope.custom_body_css = true;
				$scope.processing = false;
				$scope.statusSelected = "";
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
	$scope.courseParentClick = function(e) {
		var t = e.target;
		while(t != angular.element(".course-parent")[0]) {
			if (t.className.indexOf("batch") == 0) {
				return;
			}
			t = t.parentElement;
		}
		angular.element(".options").addClass("hidden");
	}
	$scope.showOptions = function() {
		angular.element(".options").removeClass("hidden");
	}
	$scope.batchSelected = function(batchId, batchName) {
		$scope.batchId = batchId;
		$scope.batchName = batchName;
		angular.element(".options").addClass("hidden");
		angular.element(".btn").removeClass("btn-disabled");
		angular.element(".btn").attr("disabled", false);
	}
	$scope.gotoSection = function() {
		console.log($scope.batchId);
		$window.location = "https://www.jigsawacademy.com/jaws/batcave#!/section/" + $scope.batchId;
	}
});