'use strict';

/* Controllers */

angular.module('fabric')
    .controller('topicCtrl', ['$scope', function($scope) {

    	$scope.app.name = "Topic - Jigsaw Learning Centre";
		$scope.env.header.ext.default = false;
		$scope.env.header.ext.allow = true;

    }]);
