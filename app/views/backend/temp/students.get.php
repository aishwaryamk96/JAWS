<?php

/*
           8 8888       .8. `8.`888b                 ,8' d888888o.
           8 8888      .888. `8.`888b               ,8'.`8888:' `88.
           8 8888     :88888. `8.`888b             ,8' 8.`8888.   Y8
           8 8888    . `88888. `8.`888b     .b    ,8'  `8.`8888.
           8 8888   .8. `88888. `8.`888b    88b  ,8'    `8.`8888.
           8 8888  .8`8. `88888. `8.`888b .`888b,8'      `8.`8888.
88.        8 8888 .8' `8. `88888. `8.`888b8.`8888'        `8.`8888.
`88.       8 888'.8'   `8. `88888. `8.`888`8.`88'     8b   `8.`8888.
  `88o.    8 88'.888888888. `88888. `8.`8' `8,`'      `8b.  ;8.`8888
    `Y888888 ' .8'       `8. `88888. `8.`   `8'        `Y8888P ,88P'

    JIGSAW ACADEMY WORKFLOW SYSTEM v1
    ---------------------------------
*/

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header('Location: http://www.jigsawacademy.com');
		die();
	}

	load_module("ui");
	load_module("user_enrollment");
	load_plugin("phpexcel");

	// Init Session
	auth_session_init();

	// Prep
	$login_params["return_url"] = JAWS_PATH_WEB."/enrollments";

	// Login Check
	if (!auth_session_is_logged()) {
		ui_render_login_front(array(
				"mode" => "login",
				"return_url" => $login_params["return_url"],
				"text" => "Please login to access this page."
				));
		exit();
	}

	if (!auth_session_is_allowed("enrollment.get") && !auth_session_is_allowed("enrollment.get.adv")) {
		ui_render_msg_front(array(
				"type" => "error",
				"title" => "Jigsaw Academy",
				"header" => "No Tresspassing",
				"text" => "Sorry, but you do not have permission to access this page.<br/>Contact Jigsaw Support team for assistance."
				));
		exit();
	}

	if (!empty($_POST)) {

		$msg = false;

		$where = "";
		$date_where = "";
		if (!empty($_POST["start_from_date"])) {
    		$date_where .= " AND DATE(subs.start_date) >= ".db_sanitize($_POST["start_from_date"]);
		}
		if (!empty($_POST["start_to_date"])) {
			$date_where .= " AND DATE(subs.start_date) <= ".db_sanitize($_POST["start_to_date"]);
		}
		if (!empty($_POST["end_from_date"])) {
    		$date_where .= " AND DATE(subs.end_date) >= ".db_sanitize($_POST["end_from_date"]);
		}
		if (!empty($_POST["end_to_date"])) {
			$date_where .= " AND DATE(subs.end_date) <= ".db_sanitize($_POST["end_to_date"]);
		}
		if ($_POST["course_id"] != "-1") {

			$id = substr($_POST["course_id"], 1);

			$bundle = false;

			if ($_POST["course_id"][0] == "c") {
				$where .= " AND enr.course_id = ".$id;
			}
			else {

				$where .= " AND meta.bundle_id = ".$id;
				$bundle = true;

			}

			if (!empty($_POST["section"])) {

				if ($bundle) {
					die("Cannot select section for specialization. Please try changing the serarch criteria.");
				}

				$section_date = date_create_from_format("Y-m-d", $_POST["section"]);
				if ($section_date->format("Y") > 2017 || ($section_date->format("Y") == 2017 && $section_date->format("m") >= 9)) {
					$GLOBALS["ml_effective"] = true;
				}

				if (!$GLOBALS["ml_effective"] && $_POST["learn_mode"] == "-1") {
					$msg = "Learn mode not selected";
				}
				else {

					if (($section = section_get_for_date($id, $section_date, ($GLOBALS["ml_effective"] == true ? 3 : $_POST["learn_mode"]))) === false) {
						die("Section does not exist for the course. Please try changing the search criteria.");
					}
					$where = " AND enr.section_id = ".$section["id"];

				}

			}

		}

		if ($_POST["learn_mode"] != "-1") {
			$where .= " AND enr.learn_mode='".($_POST["learn_mode"] == "1" ? "il" : ($_POST["learn_mode"] == "2" ? "sp" : "ml"))."'";
		}

		// $csv = "JigID,Name,Email,Phone,City,Start Date,End Date,Courses\r\n";

		if (empty($msg)) {

			$res_subs = db_query("SELECT
									user.name AS Name,
									user.email AS Email,
									user.phone AS Phone,
									um.city AS City,
									user.lms_soc AS 'Social Account',
									(
										CASE
											WHEN user.lms_soc = 'fb' THEN user.soc_fb
											WHEN user.lms_soc = 'gp' THEN user.soc_gp
											WHEN user.lms_soc = 'li' THEN user.soc_li
										END
									) AS 'Social Email',
									IF (access.id IS NOT NULL, MIN(DATE(access.start_date)), DATE(subs.start_date)) AS 'start_date',
									IF (access.id IS NOT NULL, MIN(DATE(access.end_date)), DATE(subs.end_date)) AS 'End Date',
									IF (access.id IS NOT NULL,
										PERIOD_DIFF(EXTRACT(YEAR_MONTH FROM MIN(DATE(access.end_date))), EXTRACT(YEAR_MONTH FROM MIN(DATE(access.start_date)))),
										PERIOD_DIFF(EXTRACT(YEAR_MONTH FROM MIN(DATE(subs.end_date))), EXTRACT(YEAR_MONTH FROM MIN(DATE(subs.start_date))))) AS 'Duration',
									IF (meta.bundle_id IS NOT NULL, bundle.name, 'Custom') AS 'Specialization',
									GROUP_CONCAT(CONCAT(course.course_id, '=', course.name) SEPARATOR '+') AS Courses,
									enr.sis_id AS 'Jig ID',
									enr.lab_pass AS 'Password',
									GROUP_CONCAT(lab.lab_ip SEPARATOR '+') AS 'Lab',
									subs.combo,
									subs.combo_free AS 'Free',
									IF (freeze.id IS NOT NULL, GROUP_CONCAT(DISTINCT CONCAT(DATE(freeze.start_date), ' to ', DATE(freeze.end_date)) SEPARATOR ', '), '') AS 'Freezes',
									IF (access.id IS NOT NULL, GROUP_CONCAT(DISTINCT CONCAT(DATE(access.start_date), ' to ', DATE(access.end_date)) SEPARATOR ', '), '') AS 'Extns'
								FROM
									subs
								INNER JOIN
									subs_meta AS meta
									ON meta.subs_id = subs.subs_id
								INNER JOIN
									user ON user.user_id = subs.user_id
								INNER JOIN
									user_meta AS um ON um.user_id = subs.user_id
								INNER JOIN
									user_enrollment AS enr ON enr.subs_id = subs.subs_id AND enr.status = 'active'
								INNER JOIN
									course ON course.course_id = enr.course_id
								LEFT JOIN
									course_bundle AS bundle
									ON bundle.bundle_id = meta.bundle_id
								LEFT JOIN
									access_duration AS access
									ON access.subs_id = subs.subs_id
								LEFT JOIN
									freeze
									ON freeze.user_id = subs.user_id
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
									ON lab.subs_id = subs.subs_id
								WHERE
									subs.status='active'".
									$date_where.
									$where."
								GROUP BY subs.subs_id
								ORDER BY subs.start_date ASC;");

			if (!isset($res_subs[0])) {
				die("No enrollments found... Try changing the search criteria.");
			}
	/*
			foreach ($res_subs as $subs) {

				//$csv .= $subs["name"].",".$subs["email"].",".$subs["phone"].",".$subs["city"].",".$subs["start_date"].",".$subs["end_date"].",";
				$res_enr = db_query("SELECT course_id, section_id, sis_id FROM user_enrollment WHERE subs_id=".$subs["subs_id"]);

				if (isset($_POST["download"]) && $_POST["download"] == "1") {
					$csv .= $res_enr[0]["sis_id"].",".str_replace(",", "-", $subs["name"]).",".$subs["email"].",".$subs["phone"].",".str_replace(",", "-", $subs["city"]).",".$subs["start_date"].",".$subs["end_date"].",";
				}
				$courses = "";
				foreach ($res_enr as $enr) {

					$res_course = db_query("SELECT name FROM course WHERE course_id=".$enr["course_id"]);
					$courses .= $res_course[0]["name"]."+";

				}
				$csv .= substr($courses, 0, -1)."\r\n";

			}

			$filename = "external/temp/Enrollments.csv";
			$file = fopen($filename, "w");
			fwrite($file, $csv);
			fclose($file);

			header('Pragma: public');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Cache-Control: private', false);
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment; filename="'. basename($filename) . '"');
			header('Content-Transfer-Encoding: binary');
			header('Content-Length: ' . filesize($filename));
			ob_clean();
			readfile($filename);
			exit();
	*/

			$data = [];
	  		foreach ($res_subs as $row) {

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

	}

	$courses = db_query("SELECT course_id, name FROM course WHERE sis_id IS NOT NULL AND sis_id != '';");
	$specializations = db_query("SELECT bundle_id, name FROM course_bundle WHERE bundle_type = 'specialization' ORDER BY bundle_id DESC;");

?>
<HTML>
<HEAD>
	<TITLE>Enrollments List</TITLE>
</HEAD>
<BODY>
	<div>
		<center>
			<B>Download Enrollments List</B> (You are logged in as: <?php echo $_SESSION["user"]["name"]; ?> <A href="<?php echo JAWS_PATH_WEB."/logout"; ?>">Logout</A>)
		</center>
	</div>
	<hr>
	<center>
		<?php echo empty($msg) ? "" : "<h1 style='color:red'>".$msg."</h1><br>"; ?>
		<form method="post">
			Start Date:<br>
			From: <input type="date" name="start_from_date" />
			To: <input type="date" name="start_to_date" /><br>
			End Date:<br>
			From: <input type="date" name="end_from_date" />
			To: <input type="date" name="end_to_date" />
			<input type="hidden" name="download" value="1" /><br/>
			Course: <select name="course_id">
				<option value="-1">Select a course</option>
				<optgroup label="Specializations">
					<?php foreach ($specializations as $spec) { ?>
						<option value="s<?php echo $spec["bundle_id"] ?>"><?php echo $spec["name"] ?></option>
					<?php } ?>
				</optgroup>
				<optgroup label="Courses">
					<?php foreach ($courses as $course) { ?>
						<option value="c<?php echo $course["course_id"] ?>"><?php echo $course["name"] ?></option>
					<?php } ?>
				</optgroup>
			</select>
			<select name="learn_mode">
				<option value="-1">Select</option>
				<option value="1">Premium</option>
				<option value="2">Regular</option>
				<option value="3">Catalyst</option>
			</select><br />
			Section: <input type="date" name="section"><br>
			<input type="submit" value="Get me the list!" />
		</form>
</center>
</BODY>
</HTML>