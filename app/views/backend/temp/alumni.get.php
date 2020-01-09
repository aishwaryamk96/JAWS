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

	// Prep
	$return_url = JAWS_PATH_WEB."/alumni";

	// Login Check
	if (!auth_session_is_logged()) {

		ui_render_login_front(array(
			"mode" => "login",
			"return_url" => $return_url,
			"text" => "Please login to access this page."
			));
		exit();

	}

	if (!auth_session_is_allowed("sis.get")) {

		ui_render_msg_front(array(
				"type" => "error",
				"title" => "Jigsaw Academy",
				"header" => "No Tresspassing",
				"text" => "Sorry, but you do not have permission to access this page.<br/>Contact Jigsaw Support team for assistance."
			));
		exit();

	}

	if (isset($_GET["date_from"])) {

		$from;
		$to;
		if (!empty($_GET["date_from"])) {

			$from = db_sanitize($_GET["date_from"]);
			if (!empty($_GET["date_to"])) {
				$to = db_sanitize($_GET["date_to"]);
			}
			else {
				$to = "CURRENT_DATE";
			}

		}
		else {

			$from = "CURRENT_DATE";
			$to = "DATE_ADD(CURRENT_DATE, INTERVAL 1 DAY)";

		}

		$query = "SELECT
					subs.subs_id,
					course.sis_id AS course,
					enr.sis_id AS jig,
					section.sis_id AS section
				FROM
					`subs`
				INNER JOIN
					user_enrollment AS enr
					ON enr.subs_id = subs.subs_id
				INNER JOIN
					course
					ON course.course_id = enr.course_id
				INNER JOIN
					course_section AS section
					ON section.id = enr.section_id
				INNER JOIN
					(
						SELECT
							subs_id,
							MAX(end_date) AS end_date
						FROM
							`access_duration`
							GROUP BY subs_id
					) AS access
					ON access.subs_id = subs.subs_id
				WHERE
					DATE(access.end_date) BETWEEN $from AND $to;";

		$res_subs = db_query($query);

		$data = "course_id,user_id,role,section_id,status\r\n";
		foreach ($res_subs as $subs) {

			$line = $subs["course"].",".$subs["jig"].",student,".$subs["section"].",deleted\r\n";
			$line .= $subs["course"].",".$subs["jig"].",alumni,".$subs["section"].",active\r\n";
			$data .= $line;

		}

		header_download("text/csv", "alumni-".date("Y-m-d").".csv");
		$stream = fopen("php://output", "w");
		fwrite($stream, $data);
		fclose($stream);
		exit();

	}

?>
<html>
<head>
	<title>Alumni SIS File Download</title>
</head>
<body>
	<div>
        <center>
            <b>Download Alumni SIS file</b> (You are logged in as: <?php echo $_SESSION["user"]["name"]; ?> <a href="<?php echo JAWS_PATH_WEB."/logout"; ?>">Logout</a>)
            <?php if (isset($msg)) echo "<br/>".$msg; ?>
        </center>
    </div><HR>
    <center>
        <form method="get">
            From: <input type="date" name="date_from" /> (Leave empty for today's alumni list)<br>
            To: <input type="date" name="date_to" /> (Leave empty for alumnis till today)<br><br>
            <input type="submit" value="Download!" />
        </form>
    </center>
</body>
</html>