<?php

	load_plugin("mobile_app");

	if (empty($GLOBALS["mobileObject"])) {

		load_module("user");

		$mobile = new MobileApp;
		$GLOBALS["mobileObject"] = $mobile;

		$headers = getallheaders();
		if (!$mobile->authorizeRequest($headers["Authorization"])) {

			header("HTTP/1.1 401 Unauthorized");
			die();

		}

		$user = user_get_by_id($_POST["user_id"]);

		$res_subs = db_query("SELECT * FROM subs WHERE (status='active' OR status='pending') AND user_id=".$_POST["user_id"].";");
		if (empty($res_subs)) {
			die(json_encode(menu_sequence_get()));
		}
		else {
			die(json_encode(menu_sequence_get(true)));
		}

	}

	function menu_sequence_get($student = false) {

		if ($student) {
			return ["My Courses", "Refer a Friend", /*"Notifications", "Job Openings", "Exclusive Offers", */"Explore Jigsaw", /*"Upcoming Webinars", "Visit our Website",*/ "FAQ", "Logout"];
		}
		else {
			return ["Home", /*"Jigsaw Courses", "Upcoming Webinars", "Exclusive Offers", */"Notifications", "Job Openings"/*, "Visit our Website"*/, "FAQ", "Logout"];
		}

	}

	/*

	Student
	1. My Courses
	2. Refer a Friend
	3. Notifications
	4. Job Openings
	5. Exclusive Offers 	<-- NOT WANTED RIGHT NOW! (2018-09-06)
	6. Explore Jigsaw
	7. Upcoming Webinars
	8. Visit our Website
	9. FAQ
	10. Logout


	Non Student
	1. Home
	2. Jigsaw Courses
	3. Upcoming Webinars
	4. ​Exclusive Offers 	<-- NOT WANTED RIGHT NOW! (2018-09-06)
	5. Notifications
	6. Job Openings
	7. Visit our Website
	8. FAQ
	9. Logout

	*/

?>