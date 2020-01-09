app.controller("userCtrl", function($scope, $http, $routeParams, $compile, $window, $timeout) {
	$scope.processing = true;
	$scope.months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
	$scope.newCoursesCount = 1;
	$scope.selectCourse = [];
	$scope.batchAuto = [];
	$scope.batchMonth = [];
	$scope.batchYear = [];
	$scope.isComplimentary = [];
	$scope.sisDo = [];
	$scope.subs = {id : -1, remove : [], add : []};
	$scope.removedSoc = {fb: false, gp: false, li: false};
	$scope.noCopied = true;
	$scope.assignments = [];
	$scope.courseName = "";
	$scope.mobBlocked = false;
	$scope.mobAccess = [];
	$scope.cleanSubs = function() {
		$scope.newCourseSelected = 0;
		$scope.subs.id = -1;
		$scope.subs.remove = [];
		$scope.subs.add = [];
		for (var i = 0; i < 10; i++) {
			$scope.selectCourse[i] = "";
			$scope.batchAuto[i] = true;
			$scope.isComplimentary[i] = true;
			$scope.sisDo[i] = true;
			$scope.batchMonth[i] = "";
			$scope.batchYear[i] = "";
		}
	}
	$scope.cleanSubs();
	/* Get user profile start */
	$http.get(JAWS_PATH_API + "/user.get?user=" + $routeParams.user_id)
		.then(
			function success(response) {
				$scope.editAccess = response.data.edit == 1;
				$scope.custom_body_css = true;
				$scope.user = response.data.user;
				$scope.hasSubs = false;
				if (Object.keys($scope.user.subs).length > 0) {
					$scope.hasSubs = true;
				}
				$scope.hasProgress = false;
				if (Object.keys($scope.user.progress).length > 0) {
					$scope.hasProgress = true;
				}
				$scope.jigId = response.data.user.jig_id;
				$scope.lmsPass = response.data.user.lms_pass;
				$scope.processing = false;
				$scope.statusShow = false;
				$scope.electives = response.data.electives;
				$scope.courses = response.data.courses;
				$scope.logs = response.data.logs;
				$scope.editPayment = response.data.edit_payment;
				$scope.canCall = response.data.can_call;
			},
			function error(response) {
				if (response.status == 401) {
					$window.location.reload();
				}
				else {
					alert("Something went wrong...");
				}
			}
		);
	/* Get user profile end */
	/* Toggle "Copy phone number text change" start */
	$scope.toggleCopyText = function() {
		$scope.textCopy($scope.user.phone);
		$scope.noCopied = false;
		$timeout(function() {
			$scope.noCopied = true;
		}, 1000);
	}
	/* Toggle "Copy phone number text change" end */
	/* Call the user start */
	$scope.call = function() {
		if (!confirm("Are you sure you want to connect to "+$scope.user.phone+"?")) {
			return;
		}
		$http.get(JAWS_PATH_API + "/user.call?user=" + $scope.user.user_id)
			.then(
				function success(response) {
					alert(response.data.msg);
				},
				function error(response) {
					if (response.status == 401) {
						$window.location.reload();
					}
					else {
						alert("Something went wrong...\nPlease contact IT team for the issue.");
					}
				}
			);
	}
	/* Call the user end */
	/* Subs status accordion start */
	$scope.subsStatusToggle = function(status) {
		var tbody = angular.element("#subs-" + status);
		var arrow_icon = angular.element("#arrow-" + status);
		if (tbody.css("display") == "none") {
			tbody.removeClass("ng-hide");
			arrow_icon.removeClass("fa-chevron-right");
			arrow_icon.addClass("fa-chevron-down");
		}
		else {
			tbody.addClass("ng-hide");
			arrow_icon.removeClass("fa-chevron-down");
			arrow_icon.addClass("fa-chevron-right");
		}
	}
	/* Subs status accordion end */
	$scope.textCopy = function(text, returnText = "") {
		var input = angular.element("#txt-copy");
		input.val(text);
		input.css("display", "inline");
		input.select();
		document.execCommand("Copy");
		input.css("display", "none");
		if (returnText != "") {
			return returnText;
		}
	}
	/* Elipsize subs bundle name start */
	$scope.elipsiseText = function(text, length) {
		if (text.length > length) {
			return text.substr(0, length - 3) + "...";
		}
		return text;
	}
	/* Elipsize subs bundle name end */
	/* Progress & certification start */
	$scope.progress = function(course_code) {
		if (!$scope.user.progress[course_code]) {
			return "N/A";
		}
		return $scope.user.progress[course_code].p + "% completed";
	}
	$scope.progressInt = function(course_code) {
		if (!$scope.user.progress[course_code]) {
			return 0;
		}
		return $scope.user.progress[course_code].p;
	}
	$scope.certificate = function(course_code) {
		if (!$scope.user.progress[course_code]) {
			return "0";
		}
		return $scope.user.progress[course_code].c;
	}
	/* Progress & certification end */
	$scope.range = function(i, j, k = 1) {
		var l = [];
		for (var n = i; n < j; n += k) {
			l.push(n);
		}
		return l;
	}
	/* Modal click handlers start */
	// Present edit profile modal
	$scope.modalClick = function($e) {
		if ($e.target == angular.element("#modal-container")[0] || $e.target == angular.element("#modal-box")[0] || $e.target == angular.element("#btn-close")[0] || $e.target == angular.element("#btn-cancel")[0]) {
			$scope.dialogVisible = 0;
		}
	}
	// Add new courses to subs modal
	$scope.modalClick2 = function($e) {
		if ($e.target == angular.element("#modal-container-2")[0] || $e.target == angular.element("#modal-box-2")[0] || $e.target == angular.element("#btn-close-2")[0] || $e.target == angular.element("#btn-cancel-2")[0]) {
			angular.element("#modal-container-2").addClass("hidden");
		}
	}
	// Access duration modification modal
	$scope.modalClickAD = function($e) {
		if ($e.target == angular.element("#modal-container-ad")[0] || $e.target == angular.element("#modal-box-ad")[0] || $e.target == angular.element("#btn-close-ad")[0] || $e.target == angular.element("#btn-cancel-ad")[0]) {
			angular.element("#modal-container-ad").addClass("hidden");
		}
	}
	// New profile edit modal
	$scope.modalClickSP = function($e) {
		if ($e.target == angular.element("#modal-container-sp")[0] || $e.target == angular.element("#modal-box-sp")[0] || $e.target == angular.element("#btn-close-sp")[0] || $e.target == angular.element("#btn-cancel-sp")[0]) {
			angular.element("#modal-container-sp").addClass("hidden");
		}
	}
	// Report an issue modal
	$scope.modalClickIssue = function($e) {
		if ($e.target == angular.element("#modal-container-issue")[0] || $e.target == angular.element("#modal-box-issue")[0] || $e.target == angular.element("#btn-close-issue")[0] || $e.target == angular.element("#btn-cancel-issue")[0]) {
			angular.element("#modal-container-issue").addClass("hidden");
		}
	}
	// Assignments & progress modal
	$scope.modalClickAssignments = function($e) {
		if ($e.target == angular.element("#modal-container-assignments")[0] || $e.target == angular.element("#modal-box-assignments")[0] || $e.target == angular.element("#btn-close-assignments")[0] || $e.target == angular.element("#btn-cancel-assignments")[0]) {
			angular.element("#modal-container-assignments").addClass("hidden");
			$scope.assignments = [];
		}
	}
	/* Modal click handlers end */
	/* Show present/new profile edit modal start */
	$scope.showProfileEdit = function() {
		//if ($scope.himanshu) {
			$scope.userName = $scope.user.name;
			$scope.userEmail = $scope.user.email;
			$scope.userEmail2 = $scope.user.email_2 ? $scope.user.email_2 : "";
			$scope.addAltEmail = false;
			$scope.userPhone = $scope.user.phone;
			$scope.userLmsSoc = $scope.user.lms_soc;
			$scope.userSocFb = $scope.user.soc_fb;
			$scope.userSocGp = $scope.user.soc_gp;
			$scope.userSocLi = $scope.user.soc_li;
			$scope.userJigId = $scope.user.jig_id;
			$scope.userLabUserUpdate = false;
			angular.element("#modal-container-sp").removeClass("hidden");
			return;
		// }
		// $scope.freezeDate = null;
		// $scope.unfreezeDate = null;
		// $scope.endDateExt = null;
		// $scope.extendRange = null;
		// $scope.dialogVisible = 1;
	}
	/* Present edit profile access date pickers start */
	angular.element("#freeze-date").datepicker({
		dateFormat: "d MM, yy",
	});
	angular.element("#unfreeze-date").datepicker({
		dateFormat: "d MM, yy",
	});
	angular.element("#extend-date").datepicker({
		dateFormat: "d MM, yy"
	});
	/* Present edit profile access date pickers end */
	/* New edit profile access date pickers start */
	angular.element("#freeze-date-sp").datepicker({
		dateFormat: "d MM, yy",
	});
	angular.element("#unfreeze-date-sp").datepicker({
		dateFormat: "d MM, yy",
	});
	/* New edit profile access date pickers end */
	/* Show present/new profile edit modal end */
	/* Save access from present edit profile modal start */
	$scope.saveAccessEdit = function() {
		if ((($scope.freezeDate != null && $scope.freezeDate.length > 0) && ($scope.unfreezeDate != null && $scope.unfreezeDate.length > 0)) || (($scope.endDateExt.length > 0 || $scope.extendRange != null || $scope.extendRange != undefined) && $scope.extCombo != null)) {
			$scope.processing = true;
			$scope.toggleFormElementsAccess();
			var data = {};
			if (($scope.freezeDate != null && $scope.freezeDate.length) > 0 && ($scope.unfreezeDate != null && $scope.unfreezeDate.length > 0)) {
				data.freezeDate = $scope.freezeDate;
				data.unfreezeDate = $scope.unfreezeDate;
			}
			else if (($scope.endDateExt.length > 0 || $scope.extendRange != null || $scope.extendRange != undefined) && $scope.extCombo != null) {
				data.subs_id = $scope.extCombo.subs_id;
				data.end_date_ext = $scope.endDateExt;
				data.days = $scope.extendRange;
			}
			data.user_id = $scope.user.user_id;
			$http.post(JAWS_PATH_API + "/user.update", data)
				.then(
					function(response) {
						if (response.data.success == true) {
							if (response.data.freeze) {
								$scope.user.freeze.push({id: response.data.id, start_date: response.data.freeze, end_date: response.data.unfreeze});
							}
							$scope.dialogVisible = 0;
							$scope.toggleFormElementsAccess(false);
							$scope.freezeDate = null;
							$scope.unfreezeDate = null;
							$scope.endDateExt = null;
							$scope.extendRange = null;
							$scope.processing = false;
							alert("Changes saved!");
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
	}
	/* Save access from present edit profile modal end */
	/* Save profile from new edit profile modal start */
	$scope.saveProfile = function() {
		var profile = {userId: $scope.user.user_id, remove_soc: $scope.removedSoc, lab_user_update: $scope.userLabUserUpdate};
		if ($scope.userName != "" && $scope.userName != $scope.user.name) {
			profile.name = $scope.userName;
		}
		if ($scope.userEmail != "" && $scope.userEmail != $scope.user.email) {
			profile.email = $scope.userEmail;
		}
		if ($scope.userEmail2 != $scope.user.email_2) {
			profile.email_2 = $scope.userEmail2;
		}
		if ($scope.userPhone != "" && $scope.userPhone != $scope.user.phone) {
			profile.phone = $scope.userPhone;
		}
		if ($scope.userLmsSoc != "" && $scope.userLmsSoc != $scope.user.lms_soc) {
			if ($scope.removedSoc[$scope.userLmsSoc]) {
				alert("Login channel cannot be one of the removed social logins. Please change the Login channel");
				return;
			}
			profile.lms_soc = $scope.userLmsSoc;
		}
		if ($scope.userSocFb != "" && $scope.userSocFb != $scope.user.soc_fb) {
			profile.soc_fb = $scope.userSocFb;
		}
		if ($scope.userSocGp != "" && $scope.userSocGp != $scope.user.soc_gp) {
			profile.soc_gp = $scope.userSocGp;
		}
		if ($scope.userSocLi != "" && $scope.userSocLi != $scope.user.soc_li) {
			profile.soc_li = $scope.userSocLi;
		}
		if ($scope.userJigId != "" && $scope.userJigId != $scope.user.jig_id) {
			profile.jig_id = $scope.userJigId;
		}
		if (($scope.freezeDate != null && $scope.freezeDate.length > 0) && ($scope.unfreezeDate != null && $scope.unfreezeDate.length > 0)) {
			profile.freeze_date = $scope.freezeDate;
			profile.unfreeze_date = $scope.unfreezeDate;
		}
		if ($scope.removedSoc[$scope.user.lms_soc]) {
			alert("Login channel cannot be one of the removed social logins. Please change the Login channel");
			return;
		}
		$http.post(JAWS_PATH_API + "/user.edit", profile)
			.then(
				function success(response) {
					if (response.data.status) {
						if (!!response.data.msg) {
							alert(response.data.msg);
						}
						else {
							alert("Profile updated!");
						}
						$window.location.reload();
					}
					else {
						alert(response.data.msg);
					}
				}, function error(response) {
					if (response.status == 401) {
						$window.location.reload();
					}
					else {
						alert("Something went wrong...\nPlease contact IT team for the issue.");
					}
				}
			)
	}
	$scope.removeSoc = function(soc, remove = true) {
		$scope.removedSoc[soc] = remove;
	}
	/* Save profile from new edit profile modal end */
	/* Toggle Okay and cancel button availability on first 2 modals start */
	$scope.toggleFormElementsAccess = function(disable = true, modal = "1") {
		angular.element("#btn-cancel"+(modal == "1" ? "" : "-2")).addClass("disabled");
		angular.element("#btn-save"+(modal == "1" ? "" : "-2")).removeClass("enabled");
	}
	/* Toggle Okay and cancel button availability on first 2 modals end */
	/* Edit subs start */
	// Start editing subs
	$scope.editSubs = function(subsId) {
		$scope.cleanSubs();
		angular.element(".remove-course-"+subsId).removeClass("hidden");
		angular.element("#edit-subs-"+subsId).addClass("hidden");
		angular.element("#save-subs-"+subsId).removeClass("hidden");
		angular.element("#cancel-subs-"+subsId).removeClass("hidden");
		angular.element(".add-course-"+subsId).removeClass("hidden");
		angular.element(".lab-"+subsId).removeClass("hidden");
	}
	// Cancel editing subs
	$scope.cancelSubs = function(subsId) {
		$scope.cleanSubs();
		angular.element(".remove-course-"+subsId).addClass("hidden");
		angular.element(".addback-course-"+subsId).addClass("hidden");
		angular.element(".subs-enr-row-"+subsId).removeClass("disabled");
		angular.element("#edit-subs-"+subsId).removeClass("hidden");
		angular.element("#save-subs-"+subsId).addClass("hidden");
		angular.element("#cancel-subs-"+subsId).addClass("hidden");
		angular.element(".add-course-"+subsId).addClass("hidden");
		angular.element(".lab-"+subsId).addClass("hidden");
	}
	// Remove a course
	$scope.removeCourse = function(subsId, enrId) {
		if ($scope.subs.id != subsId) {
			$scope.cleanSubs();
		}
		$scope.subs.id = subsId;
		$scope.subs.remove.push(enrId);
		angular.element("#enr-ab-"+enrId).removeClass("hidden");
		angular.element("#enr-r-"+enrId).addClass("hidden");
		angular.element(".enr-row-"+enrId).addClass("disabled");
	}
	// Add back a removed course
	$scope.addBackCourse = function(subsId, enrId) {
		$scope.subs.remove.splice($scope.subs.remove.indexOf(enrId), 1);
		angular.element("#enr-ab-"+enrId).addClass("hidden");
		angular.element("#enr-r-"+enrId).removeClass("hidden");
		angular.element(".enr-row-"+enrId).removeClass("disabled");
	}
	// Show add new courses modal
	$scope.newCourses = function(subsId) {
		if ($scope.subs.id != subsId) {
			$scope.cleanSubs();
			$scope.subs.id = subsId;
		}
		angular.element("#modal-container-2").removeClass("hidden");
	}
	// Selected a course from drop down
	$scope.selectCourseChange = function(courseIndex) {
		if ($scope.selectCourse[courseIndex] != "") {
			if ($scope.batchAuto || ($scope.batchMonth != "" && $scope.batchYear != "")) {
				$scope.newCourseSelected++;
			}
		}
		else {
			$scope.newCourseSelected--;
		}
	}
	// Add a new row for another course
	$scope.addCourseRow = function() {
		angular.element("#new-course-"+$scope.newCoursesCount).removeClass("hidden");
		$scope.newCoursesCount++;
	}
	// Add the new courses and close the modal
	$scope.addNewCourses = function() {
		var courseInfo;
		for (var i = 0; i < 10; i++) {
			if ($scope.selectCourse[i] && $scope.selectCourse[i] != "") {
				courseInfo = {id: $scope.selectCourse[i], batch: {auto: $scope.batchAuto[i], month: -1, year: -1}, isComplimentary: $scope.isComplimentary[i], sis: $scope.sisDo[i]};
				if (!$scope.batchAuto[i]) {
					courseInfo.batch.month = $scope.batchMonth[i];
					courseInfo.batch.year = $scope.batchYear[i];
				}
				$scope.subs.add.push(courseInfo);
			}
		}
		angular.element("#modal-container-2").addClass("hidden");
	}
	// Show edit access duration (new) modal
	$scope.editAccessDuration = function(subsId, startDate, endDate) {
		if (!$scope.editAccess) {
			return;
		}
		angular.element("#inp-subs-id").val(subsId);
		$scope.subsStartDate = startDate;
		$scope.subsEndDate = endDate;
		$scope.accessExtnStartDate = true;
		$scope.accessExtnEndDate = true;
		var startDate = new Date($scope.user.subs_dates[angular.element("#inp-subs-id").val()].start_date);
		var endDate = new Date($scope.user.subs_dates[angular.element("#inp-subs-id").val()].end_date);
		// Date pickers for start and end date of subs
		angular.element("#subs-start-date").datepicker({
			dateFormat: "d MM, yy",
			defaultDate: startDate
		});
		angular.element("#subs-end-date").datepicker({
			dateFormat: "d MM, yy",
			defaultDate: endDate
		});
		angular.element("#modal-container-ad").removeClass("hidden");
	}
	// Save the access duration in subs
	$scope.updateAccessDuration = function() {
		var subsId = angular.element("#inp-subs-id").val();
		var access = { subs_id: subsId };
		var modified = false;
		if ($scope.subsStartDate != "") {
			access.start_date = $scope.subsStartDate;
			modified = true;
		}
		if ($scope.subsEndDate != "") {
			access.end_date = $scope.subsEndDate;
			modified = true;
		}
		access.extn_start_date = $scope.accessExtnStartDate;
		access.extn_end_date = $scope.accessExtnEndDate;
		access.paid_extn = $scope.extnPaid;
		if (modified) {
			$http.post(JAWS_PATH_API+"/subs.access.edit", access)
				.then(
					function success(response) {
						if (response.data.status == false) {
							alert(response.data.error);
						}
						else {
							if (response.data.update) {
								alert("Access has been updated!");
								$window.location.reload();
							}
						}
					}, function error(response){
						console.log(response.data);
					}
				)
		}
	}
	// Show edit lab info edit modal
	$scope.editLabInfo = function(subsId, enrId) {
		if (!$scope.editAccess) {
			return;
		}
	}
	// Save the changes in subs
	$scope.saveSubs = function(subsId) {
		if ($scope.subs.id == -1) {
			alert("Nothing to save...");
			return;
		}
		if ($scope.subs.id != subsId) {
			alert("Something went wrong...");
			return;
		}
		if (!confirm("Are you sure you want to save the changes?")) {
			return;
		}
		$http.post(JAWS_PATH_API+"/subs.edit", $scope.subs)
			.then(
				function success(response) {
					if (response.data.status == false) {
						alert(response.data.error);
					}
					else {
						alert("Changes have been saved!");
						$window.location.reload();
					}
				}, function error(response) {
					if (response.status == 401) {
						$window.location.reload();
					}
					else {
						alert("Something went wrong...\nPlease contact IT team for the issue.");
						console.log(response.data);
						console.log($scope.subs);
					}
				}
			)
	}
	$scope.refundSubs = function(subsId) {
		if (!confirm("Are you sure you want to refund this subscription?")) {
			return;
		}
		$scope.cleanSubs();
		$http.post(JAWS_PATH_API+"/subs.refund", subsId)
			.then(
				function success(response) {
					if (response.data.status == false) {
						alert(response.data.error);
					}
					else {
						alert("Subscription has been refunded!");
						$window.location.reload();
					}
				}, function error(response) {
					if (response.status == 401) {
						$window.location.reload();
					}
					else {
						alert("Something went wrong...\nPlease contact IT team for the issue.");
						console.log(response.data);
						console.log($scope.subs);
					}
				}
			)
	}
	/* Edit subs end */
	/* Mobile app access start */
	$scope.mobileAccess = function() {
		$http.post(JAWS_PATH_API + "/mobapp.access", { user_id: $scope.user.user_id })
			.then(
				function(response) {
					$scope.mobAccess = [];
					if (!response.data.msg) {
						$scope.mobAccess.push("Videos are unlocked! Yay!!");
						$scope.mobAccessStatus = "green";
					}
					else {
						if (response.data.problem.length > 0) {
							response.data.problem.forEach(function(e, i) {
								$scope.mobAccess.push((i + 1) + ". Course '"+e[0]+"', topic '"+e[1]+"' has attachment in pre_class_videos.");
							});
							$scope.mobAccess.push("Please talk to Deepak, he knows.");
							$scope.mobAccessStatus = "red";
						}
						else {
							$scope.mobAccess.push("Videos are locked... :(");
							$scope.mobAccessStatus = "red";
							$scope.mobBlocked = true;
						}
					}
				}
			)
	}
	$scope.grantCompleteAccess = function() {
		$http.post(JAWS_PATH_API + "/mobapp.access.grant", { user_id: $scope.user.user_id })
			.then(
				function(response) {
					if (response.data.status) {
						alert("Complete access has been given to the student!");
					}
					else if (!!response.data.msg) {
						alert(response.data.msg);
					}
					else {
						alert("Something went wrong...\nPlease contact IT team for the issue.");
					}
				}, function error(response) {
					if (response.status == 401) {
						$window.location.reload();
					}
					else {
						alert("Something went wrong...\nPlease contact IT team for the issue.");
					}
				}
			)
	}
	/* Mobile app access end */
	/* Report an issue start */
	// Show modal
	$scope.showReport = function() {
		angular.element("#modal-container-issue").removeClass("hidden");
	}
	// Save issue
	$scope.report = function() {
		$http.post(JAWS_PATH_API + "/report", { user_id: $scope.user.user_id, category: $scope.reportCategory, desc: $scope.reportDesc })
			.then(
				function success(response) {
					if (response.data.status) {
						alert("Issue has been reported. Thank you!");
					}
					else {
						alert("Something went wrong...\nPlease contact IT team for the issue.");
					}
				}, function error(response) {
					if (response.status == 401) {
						$window.location.reload();
					}
					else {
						alert("Something went wrong...\nPlease contact IT team for the issue.");
					}
				}
			)
	}
	/* Report an issue end */
	/* Assignments & progress start */
	$scope.showAssignments = function(courseCode, courseName) {
		if ($scope.user.assignments.hasOwnProperty(courseCode)) {
			$scope.assignments = $scope.user.assignments[courseCode];
			$scope.courseName = courseName;
			angular.element("#modal-container-assignments").removeClass("hidden");
		}
	}
	/* Assignments & progress end */
});