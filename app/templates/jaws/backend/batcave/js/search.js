app.controller("searchCtrl", function($scope, $routeParams, $http, $document, $window) {
	$scope.searching = true;
	$scope.query = $routeParams.query;
	$http.get(JAWS_PATH_API + "/search?q=" + $routeParams.query)
		.then(function success(response) {
			$scope.results = response.data.results;
			$scope.exec_time = response.data.time;
			$scope.searching = false;
			$scope.resultsPerPage = 10;
			$scope.currentPage = 1;
			$scope.totalPages = Math.ceil($scope.results.length / $scope.resultsPerPage);
			$scope.settings_show = false;
		}, function error(response) {
			if (response.status == 401) {
				$window.location.reload();
			}
			else {
				alert("Something went wrong...");
			}
		});
	$scope.settingsShow = function($event) {
		$scope.settings_show = true;
		$event.stopPropagation();
		$document.on("click", $scope.settingsHide);
	}
	$scope.settingsHide = function() {
		$scope.settings_show = false;
		$scope.$apply();
		$document.off("click", $scope.settingsHide);
	}
	$scope.resultsByRange = function(val) {
		var index = $scope.results.indexOf(val);
		return (index >= ($scope.currentPage - 1) * $scope.resultsPerPage && index < $scope.currentPage * $scope.resultsPerPage);
	}
	$scope.pagesByRange = function(val) {
		var currPage = $scope.currentPage;
		var totalPages = $scope.totalPages;
		if (currPage < 6) {
			return (val <= 10 ? true : false);
		}
		if (currPage >= 6 && currPage <= totalPages) {
			return ((currPage - val <= 5 && val - currPage <= 5) ? true : false);
		}
		if (currPage > totalPages - 6) {
			return (val >= totalPages - 10 ? true : false);
		}
	}
	$scope.range = function(min, max) {
		var arr = [];
		max = Math.ceil(max);
		for (var i = min; i <= max; i++)
			arr.push(i);
		return arr;
	}
	$scope.pageChange = function(pageNum) {
		$scope.currentPage = pageNum;
	}
});