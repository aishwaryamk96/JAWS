app.controller("notificationsCtrl", function($scope, $http) {
	$scope.processing = true;
	$scope.custom_body_css = true;
	$scope.showSettings = false;
	$scope.formRows = [];
	$scope.selectedRow = [];
	$scope.liveMode = 0;
	$scope.data = {};
	$scope.notificationTitle = "";
	$scope.response = "";
	$scope.showResponse = false;
	$http.get(JAWS_PATH_API + "/notifications.get")
		.then(function(response) {
			$scope.formRows.push(response.data.notifications);
			$scope.forms = response.data.forms;
			$scope.processing = false;
		}, function(response) {
			alert(response.status.toString() + ": " + response.statusText);
			$scope.processing = false;
		});
	$scope.toggleMode = function() {
		$scope.liveMode = $scope.liveMode == 0 ? 1 : 0;
	}
	$scope.prepareData = function(parent, option) {
		if (parent.staticId) {
			$scope.data[parent.staticId] = option.id;
		}
		else {
			$scope.data[parent.id] = option.id;
		}
	}
	$scope.selectChange = function(level) {
		while($scope.formRows.length > level + 1) {
			$scope.formRows.pop();
		}
		$scope.parseChild(level);
	}
	$scope.parseChild = function(level) {
		var option = $scope.formRows[level].value;
		var parent = $scope.formRows[0];
		if (level > 0) {
			parent = $scope.formRows[level - 1].value;
		}
		$scope.parseOption(parent, option);
	}
	$scope.parseOption = function(parent, option) {
		if (option.type == "form") {
			$scope.prepareData(parent, option);
			$scope.formRows.push(option);
		}
		else if (option.source) {
			$scope.processing = true;
			var url = $scope.getUrl(option.source);
			$http.get(JAWS_PATH_API + url)
				.then(function(response) {
					$scope.prepareData(parent, option);
					option.options = response.data;
					$scope.formRows.push(option);
					$scope.processing = false;
				}, function(response) {
					alert(response.status.toString() + ": " + response.statusText);
					$scope.processing = false;
				});
		}
		else if (parent && parent.sub) {
			if (parent.sub.source) {
				$scope.processing = true;
				var url = $scope.getUrl(parent.sub.source, option.id);
				$http.get(JAWS_PATH_API + url)
					.then(function(response) {
						$scope.prepareData(parent, option);
						option.staticId = parent.sub.id;
						option.name = parent.sub.name;
						option.type = parent.sub.type;
						option.options = response.data;
						if (parent.sub.sub) {
							option.sub = parent.sub.sub;
							option.sub.staticId = parent.sub.sub.id;
						}
						$scope.formRows.push(option);
						$scope.processing = false;
					}, function() {
						alert(response.status.toString() + ": " + response.statusText);
						$scope.processing = false;
					});
			}
			else if (parent.sub.options.length > 0) {
				$scope.prepareData(parent, option);
				option.staticId = parent.sub.id;
				option.name = parent.sub.name;
				option.type = parent.sub.type;
				option.options = parent.sub.options;
				$scope.formRows.push(option);
			}
		}
	}
	$scope.getUrl = function(source, value = false) {
		var url = source.url;
		if (source.get) {
			url += "?" + source.get + "=" + value;
		}
		return url;
	}
	$scope.showSettingsModal = function(show = true, form = false) {
		$scope.showSettings = show;
		$scope.formTitle = "";
		if (show) {
			$scope.formTitle = form.title;
		}
	}
	$scope.saveNotification = function() {
		$scope.processing = true;
		if ($scope.notificationTitle == "") {
			$scope.data["notificationTitle"] = $scope.formTitle;
		}
		else {
			$scope.data["notificationTitle"] = $scope.notificationTitle;
		}
		if ($scope.sendAt == "") {
			$scope.data["sendAt"] = "now";
		}
		else {
			$scope.data["sendAt"] = $scope.sendAt;
		}
		$scope.data["live"] = $scope.liveMode;
		$http.post(JAWS_PATH_API + "/notifications/send", $scope.data)
			.then(function(response) {
				$scope.showSettingsModal(false);
				$scope.response = JSON.stringify(response.data, undefined, 4);
				$scope.processing = false;
				$scope.showResponseModal();
			}, function(response) {
				$scope.processing = false;
				alert(response.status.toString() + ": " + response.statusText);
			});
	}
	$scope.showResponseModal = function(show = true) {
		$scope.showResponse = show;
		if (!show) {
			$scope.response = "";
		}
	}
});