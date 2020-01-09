app.controller("notificationsCtrl", function($scope, $http) {
	$scope.custom_body_css = true;
	$scope.forms = [];
	$scope.notifications = [];
	$scope.selected = [];
	$http.get(JAWS_PATH_API + "/notifications.get")
		.then(function(response) {
			if (response.status == 401) {
				$scope.error = "Something went wrong";
			}
			else if (response.status == 200) {
				$scope.notifications = response.data;
				$scope.forms.push(response.data);
				// $scope.selected = response.data.sub[0].options[0];
				// $scope.formOptionChange($scope.notifications, 0);
				// $scope.notificationCategory = $scope.notification[0];
			}
		});
	$scope.prepareOption = function(option, callback, inner_sub = false) {
		if (option.sub) {
			if (option.source) {
				var source = option.source;
				var url = source.url;
				if (source.get != "") {
					url += "?" + source.get + "=" + option.value.id;
				}
				$http.get(JAWS_PATH_API + url)
					.then(function(response) {
						if (response.status == 401) {

						}
						else if (response.status == 200) {
							var form = [];
							for (var e in response.data) {
								var options = response.data[e];
								options.sub = option.sub[e].inner_sub;
								option.sub[e].options = options;
								form.push(option.sub[e]);
							}
							callback(form);
						}
					});
			}
			callback(option);
		}
		else if (option.form) {
			callback(option.form);
		}
		else if (inner_sub != false) {
			option.sub = inner_sub;
			prepareOption(option, callback);
		}
	}
	$scope.formOptionChange = function(element, level) {
		$scope.selected = element;
		inner_sub = false;
		console.log(level, $scope.forms, element);
		while ($scope.forms.length > level + 1) {
			$scope.forms.pop();
		}
		$scope.prepareOption(element.value, function(form) {
			$scope.forms.push(form);
		}, inner_sub);
	}
	$scope.saveNotification = function() {

	}
	$scope.clearNotification = function() {

	}
});