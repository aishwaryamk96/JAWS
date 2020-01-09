app.controller("mainCtrl", function($scope, $http) {
	$http.post(JAWS_PATH_API + "/students.fetch")
		.then(function(response) {
			if (response.status == 401) {
				$scope.error = "Something went wrong";
			}
			else if (response.status == 200) {
				$scope.students = response.data;
			}
		});
	$scope.call_connect = function(event) {
		var subs_id = event.currentTarget.id.substr(3);
		var phone = event.currentTarget.children[1].innerHTML.trim();
		$http.post(JAWS_PATH_API + "/student.call", {subs_id : subs_id})
			.then(function(response) {
				if (response.status == 401) {
					//showDialog("Error!", "Something went wrong...", "Ok");
					alert("Something went wrong...");
				}
				else if (response.status == 200) {
					/*showDialog("Info!", "Connecting to " + phone, "Ok", function() {
						showTabDialog("communication.html");
					})*/
					alert("Connecting to " + phone);
				}
			});
		phone.trim();
	}
});