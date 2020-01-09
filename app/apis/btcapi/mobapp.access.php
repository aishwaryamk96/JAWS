<?php

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	// Init Session
	// auth_session_init();

	// // Auth Check - Expecting Session Only !
	// if ((!auth_session_is_logged()) || (!auth_session_is_allowed("batcave"))) {
	// 	header("HTTP/1.1 401 Unauthorized");
	// 	die();
	// }

	// Send Headers
	header('Access-Control-Allow-Credentials: true');
	header("Content-Type: application/json");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Pragma-directive: no-cache");
	header("Cache-directive: no-cache");
	header("Cache-Control: private, must-revalidate, max-age=0, no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
	header("Pragma: no-cache");

	if (count($_POST) == 0) {
		$_POST = json_decode(file_get_contents('php://input'), true);
	}

	if (isset($_POST["user_id"])) {

  		load_plugin("mobile_app");
		$mobile = new MobileApp;

  		$jig_id;
  		$course_codes = [];
  		$course_names = [];
  		$enrs = db_query("SELECT enr.sis_id, course.sis_id AS course_code, course.name FROM user_enrollment AS enr INNER JOIN course ON course.course_id = enr.course_id WHERE enr.status = 'active' AND enr.user_id = ".db_sanitize($_POST["user_id"]));
  		foreach ($enrs as $enr) {

  			$jig_id = $enr["sis_id"];
  			if (strtolower($enr["course_code"]) != "skipthiscourse") {

	  			$course_codes[] = $enr["course_code"];
	  			$course_names[$enr["course_code"]] = $enr["name"];

  			}

  		}

  		$course_topics = $mobile->getCoursesTopics($course_codes, $jig_id);

  		$locked = false;
  		$problem = [];
  		// if (empty($course_topics)) {
  		// 	$locked = true;
  		// }

  		$course_topics = $course_topics["t"];
  		foreach ($course_topics as $course => $topics) {

  			if (($res = videos_unlocked_check($topics)) !== true) {

  				if (is_string($res)) {
  					$problem[] = [$course, $res];
  				}

  				$locked = true;
  				break;

  			}

  		}

  		die(json_encode(["msg" => $locked, "problem" => $problem]));

  	}

  	function videos_unlocked_check($course) {

		foreach ($course as $topic_id => $topic_body) {

			foreach ($topic_body["v"] as $video) {

				if (empty($video["vi"])) {

					if (!isset($video["u"])) {
						return $topic_body["n"];
					}

					if (empty($video["u"])) {
						return false;
					}

				}

			}

		}

		return true;

	}

?>