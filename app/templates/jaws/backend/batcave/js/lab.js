app.controller("labCtrl", function($scope, $http, $routeParams, $compile, $window, $timeout) {
	$scope.processing = true;
	$scope.custom_body_css = true;
	$scope.labs = [];
	$scope.labIds = [];
	$http.get(JAWS_PATH_API + "/labs.get" + (!!$routeParams.query ? "?user=" + $routeParams.query : ""))
		.then(
			function success(response) {
				$scope.labs = response.data.labs;
				$scope.labIds = Object.keys($scope.labs);
				$scope.edit = response.data.edit;
				$scope.defaults = response.data.defaults;
				$scope.processing = false;
			}
		);
	$scope.modalClickEdit = function($e) {
		if ($e.target == angular.element("#modal-container-lab")[0] || $e.target == angular.element("#modal-box-lab")[0] || $e.target == angular.element("#btn-close-lab")[0] || $e.target == angular.element("#btn-cancel-lab")[0]) {
			angular.element("#modal-container-lab").addClass("hidden");
		}
	}
	$scope.toggleLab = function(lab) {
		var status = lab.status;
		lab.status = status == 1 ? 0 : 1;
		if (!$scope.saveLab(lab)) {
			lab.status = status;
		}
	}
	$scope.showEditLab = function(lab = false, edit = false) {
		if (edit && lab == false) {
			$scope.labNewOrEditOrAboutTitle = "New";
			$scope.editModalEdit = true;
			$scope.lab = {name: '', route: '', config: {}, lifespan: 12600};
			$scope.lab.config = $scope.defaults;
		}
		else if (edit) {
			$scope.labNewOrEditOrAboutTitle = "Edit";
			$scope.editModalEdit = true;
			$scope.lab = lab;
		}
		else {
			$scope.labNewOrEditOrAboutTitle = "About";
			$scope.editModalEdit = false;
			$scope.lab = lab;
		}
		angular.element("#modal-container-lab").removeClass("hidden");
	}
	$scope.deleteLab = function(lab) {
		if (confirm("Are you sure you want to delete the lab?")) {
			var status = lab.status;
			lab.status = -1;
			if (!$scope.saveLab(lab)) {
				lab.status = status;
			}
		}
	}
	$scope.copyLab = function(lab) {
		var newLab = JSON.parse(JSON.stringify(lab));
		newLab.id = -1;
		$scope.showEditLab(newLab, true);
	}
	$scope.saveLab = function(lab = false) {
		if (lab == false) {
			lab = $scope.lab;
		}
		var lab = JSON.parse(JSON.stringify(lab));
		$http.post(JAWS_PATH_API + "/lab.edit", {lab: lab})
			.then(
				function success(response) {
					if (response.data.status == true) {
						alert("Changes saved!");
						if ($scope.labIds.indexOf(response.data.lab.id) == -1) {
							$scope.labIds.push(response.data.lab.id);
						}
						$scope.labs[response.data.lab.id] = response.data.lab;
					}
					else {
						console.log(response.data);
						return false;
					}
				}, function error(response) {
					if (response.status == 401) {
						$window.location.reload();
					}
					else {
						alert("Something went wrong...\nPlease contact IT team for the issue.");
						$scope.processing = false;
					}
				}
			);
	}
});