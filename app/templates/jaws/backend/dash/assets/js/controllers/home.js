'use strict';

/* Controllers */

angular.module('jaws')
    .controller('HomeCtrl', ['$scope', function($scope) {

    	$scope.app.name = "JAWS - Dashboard";
		$scope.user = _JAWS_USER;
		$scope.fname = ($scope.user.name.split(" "))[0];

    }]);
