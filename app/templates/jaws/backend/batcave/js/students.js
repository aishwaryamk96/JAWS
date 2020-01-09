app.controller("studentsCtrl", function($scope, $http, $routeParams, $filter) {
	$scope.processing = true;
	$scope.custom_body_css = true;
	$scope.accessSetupNA = 0;
	$scope.params = {};
	$scope.apiCall = function() {
		$scope.processing = true;
		var url = JAWS_PATH_API + "/students";
		var params = ["old=true"];
		for (var k in $scope.params) {
			if (k != '' && $scope.params[k] != '') {
				params.push(k + "=" + $scope.params[k]);
			}
		}
		if (params.length) {
			url += "?" + params.join("&");
		}
		$http.get(url)
			.then(function(response) {
				$scope.programs = response.data.programs;
				$scope.program = $scope.programs[0];
				$scope.students = response.data.students;
				$scope.processing = false;
			}, function(response) {
				alert(response.status.toString() + ": " + response.statusText);
				$scope.processing = false;
			});
	}
	$scope.apiCall();
	$scope.programChange = function() {
		$scope.params.program = $scope.program.bundle_id;
		$scope.apiCall();
	}
	$scope.dateChange = function(end = 0) {
		if (end) {
			if (!$scope.endDate) {
				$scope.params.to = "";
			}
			else {
				$scope.params.to = $filter('date')($scope.endDate, "yyyy/MM/dd");
			}
			$scope.apiCall();
		}
		else {
			if (!$scope.startDate) {
				$scope.params.from = "";
			}
			else {
				$scope.params.from = $filter('date')($scope.startDate, "yyyy/MM/dd");
			}
			$scope.apiCall();
		}
	}
	$scope.accessSetupChange = function () {
		$scope.params.as = $scope.accessSetupNA = $scope.accessSetupNA == 1 ? 0 : 1;
		$scope.apiCall();
	}
	$scope.welcomeEmailChange = function () {
		$scope.params.we = $scope.welcomeEmailNA = $scope.welcomeEmailNA == 1 ? 0 : 1;
		$scope.apiCall();
	}
	$scope.iotKitChange = function () {
		$scope.params.iot = $scope.iotKitMailNA = $scope.iotKitMailNA == 1 ? 0 : 1;
		$scope.apiCall();
	}
});