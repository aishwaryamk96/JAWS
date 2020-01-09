<?php

	// register_shutdown_function("shutdown");

	load_plugin("mobile_app");
	load_module("user");

	header("Content-type: application/json");

	$mobile = new MobileApp;

	$headers = getallheaders();
	if (!$mobile->authorizeRequest($headers["Authorization"])) {

		header("HTTP/1.1 401 Unauthorized");
		die();

	}

	if (empty($_POST)) {
		$_POST = json_decode(file_get_contents("php://input"), true);
	}

	if (empty($_POST)) {

		header("HTTP/1.1 400");
		die;

	}

	$user;
	$new_user = false;
	$res_user = user_get_by_email($_POST["email"]);
	if ($res_user === false) {

		// Create user
		$name = $_POST["name"];

		// if (strcmp($_POST["soc"], "facebook") == 0) {
		// 	$soc = "fb";
		// }
		// else if (strcmp($_POST["soc"], "google") == 0) {
		// 	$soc = "gp";
		// }
		// else if (strcmp($_POST["soc"], "linkedin") == 0) {
		// 	$soc = "li";
		// }

		if (!in_array($_POST["soc"], ["fb", "gp", "li"])) {
			header("HTTP/1.1 422");
			die;
		}

		$soc = "soc_".$_POST["soc"];

		$res_user = user_create($_POST["email"], substr(str_shuffle($name.str_replace("@", "0", str_replace(".", "", $_POST["email"]))), 0, 10), $name, $_POST["phone"] ?? "", true);
		$update = [$soc => $_POST["email"]];
		if (!empty($_POST["photo_url"])) {
			$update["photo_url"] = $_POST["photo_url"];
		}
		user_update($user["user_id"], $update);

		$new_user = true;

	}

	$mobile->saveDeviceId($res_user["user_id"], $_POST["dev_id"], $_POST["dev_type"]);

	$user = [
				"i" => $res_user["user_id"],
				"e" => $res_user["email"],
				"n" => $res_user["name"],
				"p" => $res_user["phone"],
				"l" => ($res_user["lms_soc"] == "fb" ? 1 : ($res_user["lms_soc"] == "gp" ? 2 : 3))
			];

	if (!empty($res_user["soc_fb"])) {
		$user["f"] = $res_user["soc_fb"];
	}
	if (!empty($res_user["soc_gp"])) {
		$user["g"] = $res_user["soc_gp"];
	}
	if (!empty($res_user["soc_li"])) {
		$user["li"] = $res_user["soc_li"];
	}

	$GLOBALS["mobileObject"] = $mobile;
	require_once __DIR__."/../home/menu/sequence.php";

	$user_id = $res_user["user_id"];

	if (!$new_user) {

		$res_subs = db_query(
			"SELECT
				IF (a.id IS NOT NULL, a.start_date, subs.start_date) AS start_date,
				IF (a.id IS NOT NULL, a.end_date, IF (subs.end_date_ext IS NULL, subs.end_date, subs.end_date_ext)) AS end_date,
				bundle.name,
				GROUP_CONCAT(CONCAT(course.course_id, '+', IF (enr.learn_mode = 'sp', 2, IF (enr.learn_mode = 'il', 1, 3)), '+', section.sis_id) SEPARATOR ';') AS enrollments,
				GROUP_CONCAT(course.sis_id SEPARATOR ';') AS course_codes,
				GROUP_CONCAT(course.course_id SEPARATOR ';') AS course_ids,
				GROUP_CONCAT(enr.sis_id SEPARATOR ';') AS jig_id
			FROM
				subs
			LEFT JOIN
				(
					SELECT
						MIN(start_date) AS start_date,
						MAX(end_date) AS end_date,
						subs_id,
						id
					FROM
						access_duration
					GROUP BY
						subs_id
				) AS a
				ON a.subs_id = subs.subs_id
			INNER JOIN
				user_enrollment AS enr ON enr.subs_id = subs.subs_id
			INNER JOIN
				course ON course.course_id = enr.course_id
			LEFT JOIN
				subs_meta AS meta ON meta.subs_id = subs.subs_id
			LEFT JOIN
				course_bundle AS bundle ON bundle.bundle_id = meta.bundle_id
			INNER JOIN
				course_section AS section ON section.id = enr.section_id
			WHERE
				subs.status = 'active'
				AND
				enr.status = 'active'
				AND
				subs.user_id = $user_id
			GROUP BY
				subs.subs_id
			ORDER BY
				subs.start_date ASC,
				enr.enr_id ASC;"
		);

		if (!empty($res_subs)) {

			$res_freeze = db_query(
				"SELECT
					freeze.id,
					IF (DATE(freeze.start_date) = CURDATE(), 'Today', DATE_FORMAT(freeze.start_date, '%e %b, %Y')) AS start_date,
					IF (DATE(freeze.end_date) = CURDATE(), 'Today', DATE_FORMAT(freeze.end_date, '%e %b, %Y')) AS end_date,
					IF (DATE(freeze.start_date) <= CURDATE() AND DATE(freeze.end_date) > CURDATE(), true, false) AS frozen,
					freeze.is_free,
					requestor.name AS requested_by,
					approver.name AS approved_by,
					freeze.created_at,
					freeze.updated_at
				FROM
					freeze
				LEFT JOIN
					user AS requestor ON requestor.user_id = freeze.requested_by
				LEFT JOIN
					user AS approver ON approver.user_id = freeze.approved_by
				WHERE
					freeze.user_id=".$res_user["user_id"].";"
			);

			if (isset($res_freeze[0])) {

				$user["fa"] = false;
				foreach ($res_freeze as $freeze) {

					$user["af"][] = ["sd" => $freeze["start_date"], "ed" => $freeze["end_date"]];
					if ($freeze["frozen"] == 1) {
						$user["fa"] = true;
					}

				}

			}

			$courses = [];
			$res_course_meta = db_query("SELECT course.course_id, course.sis_id, course.name, meta.content FROM course INNER JOIN course_meta AS meta ON meta.course_id = course.course_id;");
			foreach ($res_course_meta as $course) {

				$content = json_decode($course["content"], true);
				$courses[$course["course_id"]] = ["sis_id" => $course["sis_id"], "img_small" => $content["img_main_small"], "name" => $course["name"]];

			}

			$jig_id = explode(";", $res_subs[0]["jig_id"])[0];

			$course_codes = [];
			$course_names = [];
			$course_codes_map = [];
			foreach ($res_subs as $sub) {

				$res_course_codes = explode(";", $sub["course_codes"]);
				foreach ($res_course_codes as $course_code) {

					// list($course_code, $course_name, $course_id) = explode("+", $course_code);
					if (!in_array($course_code, $course_codes)) {

						if ($course_code == "SKIPTHISCOURSE") {
							continue;
						}

						$course_codes[] = $course_code;

					}

					// $course_names[$course_id] = $course_name;
					// $course_codes_map[$course_id] = $course_code;

				}

			}

			$subs = [];
			foreach ($res_subs as $sub) {

				$enrs = [];
				$res_enrs = explode(";", $sub["enrollments"]);
				foreach ($res_enrs as $enr) {

					$enr_info = explode("+", $enr);

					$enrs[] = [
						"cid" => $enr_info[0],
						"c" => [
							"n" => $courses[$enr_info[0]]["name"],
							"i" => $courses[$enr_info[0]]["img_small"] ?? "http://www.jigsawacademy.com/jaws/media/app/app_default.png"
						],
						"l" => $enr_info[1],
						"b" => $enr_info[2],
					];

				}
				$subs[] = ["sd" => $sub["start_date"], "ed" => $sub["end_date"], "sp" => $sub["name"], "en" => $enrs];

			}

			$user["s"] = $subs;
			$user["ms"] = menu_sequence_get(true);

		}

	}
	if (empty($user["s"])) {

		require_once __DIR__."/../courses/catalogue.get.php";
		$user["c"] = course_catalogue_get();
		$user["ms"] = menu_sequence_get(false);

	}

	function shutdown() {
		echo json_encode(error_get_last());
	}

	die(json_encode($user));

?>