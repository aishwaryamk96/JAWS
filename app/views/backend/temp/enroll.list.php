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
    	header('Location: ../index.php');
    	die();
  	}

  	load_module("ui");

	// Init Session
	auth_session_init();

	// Prep
	$login_params["return_url"] = JAWS_PATH_WEB."/students";

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

	if (!empty($_GET["export"])) {

		load_plugin("phpexcel");

  		$res = db_query("SELECT
					user.name,
					IF (user.email_2 IS NOT NULL, CONCAT(user.email, '+', user.email_2), user.email) AS email,
					user.phone,
					user.lms_soc AS 'Social Account',
					(
						CASE
							WHEN user.lms_soc = 'fb' THEN user.soc_fb
							WHEN user.lms_soc = 'gp' THEN user.soc_gp
							WHEN user.lms_soc = 'li' THEN user.soc_li
						END
					) AS 'Social Email',
					IF (access.id IS NOT NULL, MIN(DATE(access.start_date)), DATE(subs.start_date)) AS start_date,
					IF (access.id IS NOT NULL, MAX(DATE(access.end_date)), DATE(subs.end_date)) AS 'End Date',
					IF (access.id IS NOT NULL,
						PERIOD_DIFF(EXTRACT(YEAR_MONTH FROM MAX(DATE(access.end_date))), EXTRACT(YEAR_MONTH FROM MIN(DATE(access.start_date)))),
						PERIOD_DIFF(EXTRACT(YEAR_MONTH FROM MAX(DATE(subs.end_date))), EXTRACT(YEAR_MONTH FROM MIN(DATE(subs.start_date))))) AS 'Duration',
					IF (meta.bundle_id IS NOT NULL, bundle.name, 'Custom') AS 'Specialization',
					GROUP_CONCAT(CONCAT(course.course_id, '=', course.name) SEPARATOR '+') AS Courses,
					enr.sis_id AS 'Jig ID',
					enr.lab_pass AS 'Password',
					GROUP_CONCAT(lab.lab_ip SEPARATOR '+') AS 'Lab',
					subs.combo,
					subs.combo_free AS 'Free'
				FROM
					user
				INNER JOIN
					subs
					ON subs.user_id = user.user_id
				LEFT JOIN
					access_duration AS access
					ON access.subs_id = subs.subs_id
				INNER JOIN
					user_enrollment AS enr
					ON enr.subs_id = subs.subs_id
				INNER JOIN
					course
					ON course.course_id = enr.course_id
				INNER JOIN
					subs_meta AS meta
					ON meta.subs_id = subs.subs_id
				LEFT JOIN
					course_bundle AS bundle
					ON bundle.bundle_id = meta.bundle_id
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
					subs.status = 'active'
					AND
					enr.status = 'active'
					AND
					DATE(subs.start_date) >= ".db_sanitize($_GET["start_date"])."
				GROUP BY
					user.user_id
					subs.subs_id
				ORDER BY
					start_date ASC;");

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

  			$data[] = $row;

  		}

  		$students = [
  			[
  				"title" => "ASC",
  				"cols" => ["Name", "Email", "Phone", "Social Account", "Social Email", "Start Date", "End Date", "Duration", "Specialization", "Courses", "Jig ID", "Password", "Lab", "Complimentary"],
  				"data" => $data
  			]
  		];

  		$prop = [
	        "title" => "ASC (".date("F j, Y").")",
		];

		phpexcel_write($students, $prop, "ASC (".date("F j, Y").").xls");

		exit;

  	}

  	load_module("user");
  	load_module("course");

  	$duration = "30";
  	if (isset($_POST["duration"]))
  		$duration = $_POST["duration"];

  	$res_subs = db_query("SELECT
								enr.sis_id AS sis_id,
								user.name AS name,
								user.email AS email,
								user.phone AS phone,
								um.city AS city,
								DATE_FORMAT(subs.start_date, '%e %M %Y %h:%i %p') AS start_date,
								DATE_FORMAT(subs.end_date, '%e %M %Y %h:%i %p') AS end_Date,
								GROUP_CONCAT(course.name separator '<br>') AS courses
							FROM
								subs
							INNER JOIN
								user ON user.user_id = subs.user_id
							INNER JOIN
								user_meta AS um ON um.user_id = subs.user_id
							INNER JOIN
								user_enrollment AS enr ON enr.subs_id = subs.subs_id
							INNER JOIN
								course ON course.course_id = enr.course_id
							WHERE
								subs.status='active'
								AND
								enr.status='active'
								AND
								subs.start_date>DATE_SUB(CURDATE(), INTERVAL ".$duration." DAY)
								AND
								subs.status='active'
							GROUP BY subs.subs_id
							ORDER BY subs.start_date DESC");

?>
<html>
<head>
	<title>New Students - JAWS</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
	<script>
		$(document).ready(function() {
			$("#go").click(function() {
				$.post("https://jigsawacademy.com/jaws/view/backend/temp/student.dashboard", { duration : $("#duration :selected").val() }, function(data, status) {
						var obj = $.parseJSON(data);
						$(".enr_info").remove();
						$.each(obj, function(index, value) {
							$("#enr").append("<tr class='enr_info'><td>" + value.sis_id + "</td><td>" + value.name + "</td><td>" + value.email + "</td><td>" + value.phone + "</td><td>" + $.each(value.courses, function(index1, value1) { value1 + "<br />"; }) + "</td><td>" + value.start_date + "</td></tr>");
						});
				});
			});
		});
	</script>
	<style>
		thead th {
			background-color: rgba(0, 0, 0, 0.095);
			text-align:center;
		}
		tr:nth-child(odd) {
			background-color: rgba(0, 0, 0, 0.075);
			text-align:center;
		}
		tr:nth-child(even) {
			background: #FFF;
			text-align:center;
		}
	</style>
</head>
<body>
	<div>
        <center>
            <b>New Students</B> (You are logged in as: <?php echo $_SESSION["user"]["name"]; ?> <a href="<?php echo JAWS_PATH_WEB."/logout"; ?>">Logout</a>)
            <?php if (isset($msg)) echo "<br/>".$msg; ?>
        </center>
    </div><hr>
	<center>
		<!-- <div id="duration_selector">
			<form method="post">
				Fetch <select name="duration">
					<?php /*for ($i=1; $i < 366; $i++) { ?>
						<option value="<?php //echo $i ?>" <?php if ($i == $duration) echo "selected" ?>><?php echo $i ?></option><?php
					}*/ ?>
				</select> days old enrollments.
				<input type="submit" value="Go!" />
			</form>
		</div> -->
		<br />
		<label>Export ASC:</label>
		<form method="get" style="border: 1px solid #aaa; padding: 10px">
			Start date: <input type="date" name="start_date">
			<input type="hidden" value="1" name="export">
			<button type="submit">Export</button>
		</form>
		<?php /*
		<table border="0" cellpadding="10" cellspacing="2" style="font-size: 95%;" id="enr">
			<thead>
				<tr class="header">
					<th>Jig ID</th>
					<th>Name</th>
					<th>Email</th>
					<th>Phone</th>
					<th>Courses</th>
					<th>Start Date</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($res_subs as $user) { ?>
					<tr class="enr_info">
						<td><a href="<?php echo JAWS_PATH_WEB ?>/search?criterion=sis_id&search_text=<?php echo $user["sis_id"] ?>" target="_blank"><?php echo $user["sis_id"] ?></a></td>
						<td><?php echo $user["name"] ?></td>
						<td><a href="<?php echo JAWS_PATH_WEB ?>/search?criterion=sis_id&search_text=<?php echo $user["sis_id"] ?>" target="_blank"><?php echo $user["email"] ?></a></td>
						<td><?php echo $user["phone"] ?></td>
						<td><?php echo $user["courses"] ?></td>
						<td><?php echo $user["start_date"] ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		*/ ?>
	</center>
</body>
</html>