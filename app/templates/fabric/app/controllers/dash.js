'use strict';

/* Controllers */

angular.module('fabric')
    .controller('dashCtrl', ['$scope', function($scope) {

    	$scope.app.name = "Dashboard - Jigsaw Learning Centre";
		$scope.env.header.ext.default = true;
		$scope.env.header.ext.allow = true;

    }]);
