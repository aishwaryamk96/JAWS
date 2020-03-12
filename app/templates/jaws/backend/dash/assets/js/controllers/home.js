'use strict';

/* Controllers */

angular.module('jaws')
    .controller('HomeCtrl', ['$scope','$http', function($scope,$http) {


        $scope.userMbl = '';
    	$scope.app.name = "JAWS - Dashboard";
		$scope.user = _JAWS_USER;
		$scope.fname = ($scope.user.name.split(" "))[0];


        $scope.initiateCall = function() {

			if (!confirm("Are you sure you want to connect to "+$scope.userMbl+"?")) {
				return;
			}
			$http.get(_JAWS_PATH + "webapi/user.call?userMobile=" + $scope.userMbl)
				.then(
					function success(response) {
						alert(response.data.msg);
					},
					function error(response) {
						if (response.status == 401) {
							$window.location.reload();
						}
						else {
							$.snackbar({content: "Something went wrong...\nPlease contact IT team for the issue."});
						}
					}
				);
		}

    }]);
