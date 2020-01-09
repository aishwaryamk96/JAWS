app.controller("profileCtrl", function($scope, $http) {
	$http.post(JAWS_PATH_API + "/me.get")
		.then(function(response) {
			if (response.status == 401) {
				$scope.error = "Something went wrong";
			}
			else if (response.status == 200) {
				$scope.me = response.data;
			}
		});
});