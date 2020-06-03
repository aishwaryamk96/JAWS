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

	$programs = [/*["id" => 0, "name" => "Select", "type" => "none", "category" => "none"]*/];
	$program_ids = [];

	$programs_first_course = [];

	$courses = db_query("SELECT course_id AS id, name, sis_id FROM course WHERE sis_id IS NOT NULL ORDER BY course_id;");
	// /*$courses = */array_unshift($courses, ["id" => 0, "name" => "Select", "sis_id" => ""]);
	$bundles = db_query("SELECT b.bundle_id AS id, b.combo, b.name, b.bundle_type, m.category FROM course_bundle AS b INNER JOIN course_bundle_meta AS m ON m.bundle_id = b.bundle_id WHERE (bundle_type='programs' OR bundle_type='bootcamps' OR bundle_type='specialization') AND status != 'draft' ORDER BY b.position;");
	foreach ($bundles as $bundle) {

		if ($bundle["bundle_type"] == "specialization") {

			if (strpos($bundle["category"], "full-stack") !== false) {
				$bundle["bundle_type"] = "full_stack";
			}

		}
		else if ($bundle["bundle_type"] == "bootcamps") {

			$bundle["batches"] = [];

			$batches = db_query("SELECT * FROM bootcamp_batches WHERE bundle_id = ".$bundle["id"].";");
			foreach ($batches as $batch) {

				$batch["id"] = "b".$batch["id"];
				$batch["meta"] = json_decode($batch["meta"], true);
				$batch["name"] = $batch["meta"]["name"];
				$bundle["batches"][] = $batch;

			}

		}

		$bundle["bundle_id"] = intval($bundle["bundle_id"]);

		$combo = explode(";", $bundle["combo"]);
		$programs_first_course[$bundle["id"]] = explode(",", $combo[0])[0];

		if (empty($_GET["old"])) {

			if (!isset($programs[$bundle["bundle_type"]])) {
				$programs[$bundle["bundle_type"]] = [];
			}
			$programs[$bundle["bundle_type"]][$bundle["id"]] = $bundle;

		}
		else {
			$programs[] = $bundle;
		}

		$program_ids[$bundle["bundle_id"]] = $bundle["bundle_type"];

	}

	$criteria = [
		"from_start_date" => false,
		"to_start_date" => false,
		"from_end_date" => false,
		"to_end_date" => false,
		"as" => false,
		"we" => false,
		"iot" => false,
		"catalogue" => "0",
		"batch" => "n0"
	];

	$pref_found = false;
	$pref = db_query("SELECT * FROM user_content WHERE `key` = 'batcave.pref' AND user_id = ".$_SESSION["user"]["user_id"]);
	if (!empty($pref)) {

		$pref = json_decode($pref[0]["value"], true);
		if (!empty($pref["stud.criteria"])) {
			$criteria =  $pref["stud.criteria"];
		}

		$pref_found = true;

	}

	$where = ["s.status != 'inactive'"];

	$from_start_date = "DATE_SUB(NOW(), INTERVAL 30 DAY)";
	$to_start_date = "NOW()";
	$from_end_date = false;
	$to_end_date = false;
	$as = false;
	$we = false;
	$program = false;
	$course = false;
	$batch = false;
	$iot = false;

	if (!defined("BATCAVE")) {

		if (!empty($_GET["from"])) {
			$from_start_date = db_sanitize($_GET["from"]);
		}
		if (!empty($_GET["to"])) {
			$to_start_date = db_sanitize($_GET["to"]);
		}
		if (!empty($_GET["as"])) {
			$as = "u.lms_soc IS NULL";
		}
		if (!empty($_GET["we"])) {
			$we = "(em.subs_id IS NULL OR em.email_sent_at IS NULL) AND s.status = 'active'";
		}
		if (!empty($_GET["program"])) {
			$program = $_GET["program"];
		}
		if (!empty($_GET["course"])) {
			$course = $_GET["course"];
		}
		if (!empty($_GET["iot"])) {
			$iot = "b.iot_kit = 1";
		}

	}
	else {

		if (empty($_POST)) {
			$_POST = json_decode(file_get_contents("php://input"), true);
		}

		$opt = $_POST["criteria"] ?? $criteria;

		if (!empty($opt["from_start_date"])) {
			$from_start_date = db_sanitize($opt["from_start_date"]);
		}
		else {
			$from_start_date = "";
		}
		if (!empty($opt["to_start_date"])) {
			$to_start_date = db_sanitize($opt["to_start_date"]);
		}

		$from_end_date = $opt["from_end_date"];
		$to_end_date = $opt["to_end_date"];
		$as = $opt["as"];
		$we = $opt["we"];
		if ($opt["catalogue"][0] == "c") {
			$course = substr($opt["catalogue"], 1);
		}
		else {
			$program = substr($opt["catalogue"], 1);
		}
		$iot = $opt["iot"];
		$batch = $opt["batch"];

		if (!empty($_POST["criteria"]) && empty($_POST["criteria"]["no_save"])) {

			$pref["stud.criteria"] = $_POST["criteria"];
			$pref = db_sanitize(json_encode($pref));
			if ($pref_found) {
				db_exec("UPDATE user_content SET `value` = $pref WHERE `key` = 'batcave.pref' AND user_id = ".$_SESSION["user"]["user_id"]);
			}
			else {
				db_exec("INSERT INTO user_content (user_id, `key`, `value`) VALUES (".$_SESSION["user"]["user_id"].", 'batcave.pref', $pref);");
			}

		}

	}

	if (!empty($from_end_date)) {
		$where[] = "DATE(a.end_date) >= ".db_sanitize($from_end_date);
	}
	if (!empty($to_end_date)) {
		$where[] = "DATE(a.end_date) <= ".db_sanitize($to_end_date);
	}
	if (!empty($as)) {
		$where[] = "u.lms_soc IS NULL";
	}
	if (!empty($we)) {
		$where[] = "(em.subs_id IS NULL OR em.email_sent_at IS NULL) AND s.status = 'active'";
	}
	if (!empty($program)) {
		$where[] = "m.bundle_id = ".db_sanitize($program);
	}
	if (!empty($course)) {
		$where[] = "e.course_id = ".db_sanitize($course);
	}
	if (!empty($batch) && $batch != "n0") {

		if ($batch[0] == "b") {
			$where[] = "m.batch_id = ".db_sanitize(substr($batch, 1));
		}
		else {

			$batch_date = db_sanitize(section_get_date_from_number(substr($batch, 1)));
			$batch_clause = "";
			//JA- 159 START
			if (!empty($course)) {
				$batch_clause = "cs.course_id = ".db_sanitize($course)." AND cs.start_date like ".$batch_date;
			}
			else if (!empty($program)) {
				$batch_clause = "cs.course_id = ".db_sanitize($programs_first_course[$program])." AND cs.start_date like ".$batch_date;
			}
            //JA-159 END
			if (!empty($batch_clause)) {
				$where[] = $batch_clause;
			}

		}

	}
	if (!empty($iot)) {
		$where[] = "b.iot_kit = 1";
	}

	if (empty($where) || !empty($from_start_date)) {
		$where[] = "DATE(s.start_date) >= ".$from_start_date;
	}
	$where[] = "DATE(s.start_date) <= ".$to_start_date;

	$where = implode(" AND ", $where);
	// die($where);

	if (!empty($_GET["download"])) {

		load_plugin("phpexcel");

		$res = db_query(
			"SELECT
				u.name AS Name,
				u.email AS Email,
				u.phone AS Phone,
				um.city AS City,
				u.lms_soc AS 'Social Account',
				(
					CASE
						WHEN u.lms_soc = 'fb' THEN u.soc_fb
						WHEN u.lms_soc = 'gp' THEN u.soc_gp
						WHEN u.lms_soc = 'li' THEN u.soc_li
					END
				) AS 'Social Email',
				IF (a.id IS NOT NULL, MIN(DATE(a.start_date)), DATE(s.start_date)) AS 'start_date',
				IF (a.id IS NOT NULL, MIN(DATE(a.end_date)), DATE(s.end_date)) AS 'End Date',
				IF (a.id IS NOT NULL,
					PERIOD_DIFF(EXTRACT(YEAR_MONTH FROM MIN(DATE(a.end_date))), EXTRACT(YEAR_MONTH FROM MIN(DATE(a.start_date)))),
					PERIOD_DIFF(EXTRACT(YEAR_MONTH FROM MIN(DATE(s.end_date))), EXTRACT(YEAR_MONTH FROM MIN(DATE(s.start_date))))) AS 'Duration',
				IF (m.bundle_id IS NOT NULL, b.name, 'Custom') AS 'Specialization',
				GROUP_CONCAT(CONCAT(course.course_id, '=', course.name) SEPARATOR '+') AS Courses,
				e.sis_id AS 'Jig ID',
				e.lab_pass AS 'Password',
				GROUP_CONCAT(lab.lab_ip SEPARATOR '+') AS 'Lab',
				s.combo,
				s.combo_free AS 'Free',
				IF (freeze.id IS NOT NULL, GROUP_CONCAT(DISTINCT CONCAT(DATE(freeze.start_date), ' to ', DATE(freeze.end_date)) SEPARATOR ', '), '') AS 'Freezes',
				IF (a.id IS NOT NULL, GROUP_CONCAT(DISTINCT CONCAT(DATE(a.start_date), ' to ', DATE(a.end_date)) SEPARATOR ', '), '') AS 'Extns'
			FROM
				subs AS s
			INNER JOIN
				subs_meta AS m
				ON m.subs_id = s.subs_id
			INNER JOIN
				user AS u
				ON u.user_id = s.user_id
			INNER JOIN
				user_meta AS um ON um.user_id = s.user_id
			INNER JOIN
				user_enrollment AS e ON e.subs_id = s.subs_id AND e.status = 'active'
			INNER JOIN
				course ON course.course_id = e.course_id
			INNER JOIN
				course_section AS cs
				ON e.section_id = cs.id
			LEFT JOIN
				course_bundle AS b
				ON b.bundle_id = m.bundle_id
			LEFT JOIN
				access_duration AS a
				ON a.subs_id = s.subs_id
			LEFT JOIN
				freeze
				ON freeze.user_id = s.user_id
			LEFT JOIN
				(
					SELECT
						DISTINCT lab_ip,
						subs_id
					FROM
						user_enrollment
					WHERE
						lab_ip IS NOT NULL
						AND
						lab_ip != ''
				) AS lab
				ON lab.subs_id = s.subs_id
			WHERE
				s.status='active' AND
				$where
			GROUP BY s.subs_id
			ORDER BY s.start_date ASC;"
		);

		$data = [];
  		foreach ($res as $row) {

  			$courses_map = [];
			$courses = explode("+", $row["Courses"]);
			foreach ($courses as $course) {

				$course = explode("=", $course);
				$courses_map[$course[0].""] = $course[1];

			}

			$normal = [];
			$complimentary = [];
			$complimentary_ids = [];

  			if (!empty($row["Free"])) {

  				$combo_free = explode(";", $row["Free"]);
  				foreach ($combo_free as $course) {

  					$course_id = explode(",", $course)[0];
  					$complimentary[] = $courses_map[$course_id.""];
  					$complimentary_ids[] = $course_id;

  				}

  			}

  			foreach ($courses_map as $id => $course) {

  				if (!in_array($id, $complimentary_ids)) {
  					$normal[] = $course;
  				}

  			}

  			$row["Courses"] = implode("+", $normal);
  			$row["Free"] = implode("+", $complimentary);

  			unset($row["combo"]);

  			$labs = [];
  			$urls = explode("+", $row["Lab"]);
  			foreach ($urls as $url) {

  				if (!in_array($url, $labs)) {
  					$labs[] = $url;
  				}

  			}
  			$row["Lab"] = implode("+", $labs);

  			$row["Extns"] = explode(", ", $row["Extns"]);
  			unset($row["Extns"][0]);
  			$row["Extns"] = implode(", ", $row["Extns"]);

  			$data[] = $row;

  		}

		phpexcel_write([
				0 => [
					"title" => "Enrollments",
					"cols" => ["Name", "Email", "Phone", "City", "Social Account", "Social Email", "Start Date", "End Date", "Duration", "Specialization", "Courses", "Jig ID", "Password", "Lab", "Complimentary", "Freezes", "Extns"],
					"data" => $data
				]
			],

			["title" => "Enrollments (".date("F j, Y").")"],

			"Enrollments (".date("F j, Y").").xls"
		);

		exit();

	}

	$res = db_query(
		"SELECT
			u.name,
			u.user_id,
			u.email,
			b.bundle_id,
			b.name AS program,
			s.combo,
			s.combo_free,
			s.start_date,
			s.end_date,
			IF (u.lms_soc IS NULL AND s.status = 'pending', 'N', 'Y') AS acs,
			IF (
				em.subs_id IS NOT NULL,
				IF (em.email_sent_at IS NOT NULL,
					IF (
						DATE(em.email_sent_at) = CURRENT_DATE,
						CONCAT('Today ', DATE_FORMAT(em.email_sent_at, '%h:%i %p')),
						DATE_FORMAT(em.email_sent_at, '%e %b %y, %h:%i %p')
					),
					'N'
				),
				'N'
			) AS email_at,
			IF (
				em.subs_id IS NOT NULL,
				IF (em.iot_email_sent_at IS NOT NULL,
					IF (
						DATE(em.iot_email_sent_at) = CURRENT_DATE,
						CONCAT('Today ', DATE_FORMAT(em.iot_email_sent_at, '%h:%i %p')),
						DATE_FORMAT(em.iot_email_sent_at, '%e %b %y, %h:%i %p')
					),
					'N'
				),
				'NA'
			) AS iot_email_at
		FROM
			user AS u
		INNER JOIN
			subs AS s
			ON s.user_id = u.user_id
		INNER JOIN
			subs_meta AS m
			ON m.subs_id = s.subs_id
		LEFT JOIN
			course_bundle AS b
			ON b.bundle_id = m.bundle_id
		LEFT JOIN
			user_enrollment AS e
			ON e.subs_id = s.subs_id
		LEFT JOIN
			course_section AS cs
			ON cs.id = e.section_id
		INNER JOIN
			(
				SELECT
					s.subs_id,
					IF (a.id IS NOT NULL, MAX(a.end_date), s.end_date) AS end_date
				FROM
					subs AS s
				LEFT JOIN
					access_duration AS a
					ON a.subs_id = s.subs_id
				GROUP BY
					s.subs_id
			) AS a
			ON a.subs_id = s.subs_id
		LEFT JOIN
			user_enr_meta AS em
			ON em.subs_id = s.subs_id
		WHERE
			$where
		GROUP BY
			s.subs_id
		ORDER BY
			s.start_date;"
	);

	$students = [];
	foreach ($res as $student) {

		if (empty($student["bundle_id"])) {

			if (!empty($student["combo_free"])) {
				$student["combo"] .= ";".$student["combo_free"];
			}

			$count = count(explode(";", $student["combo"]));
			$student["program"] = $count." course".($count > 1 ? "s" : "");

		}

		$bundle_type = $program_ids[$student["bundle_id"]] ?? "specialization";
		$student["bundle_type"] = $bundle_type;

		unset($student["combo"]);
		unset($student["combo_free"]);

		$students[] = $student;

	}

	$response = ["bundles" => $programs, "students" => $students, "courses" => $courses, "criteria" => $criteria];
	if (!empty($_GET["old"])) {
		$response["programs"] = $programs;
	}

	die(json_encode($response));

?>