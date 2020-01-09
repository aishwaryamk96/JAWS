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

	register_shutdown_function(function() {
		if (!empty($errors = error_get_last())) {
			var_dump($errors);
		}
	});

	load_module("ui");

	// register_shutdown_function(function() {
	// 	var_dump(error_get_last());
	// });

	// Init Session
	auth_session_init();

	// Prep
	$login_params["return_url"] = JAWS_PATH_WEB."/view/backend/temp/enroll.create";

	// Login Check
	if (!auth_session_is_logged()) {

		ui_render_login_front(array(
			"mode" => "login",
			"return_url" => $login_params["return_url"],
			"text" => "Please login to access this page."
		));
		exit();

	}

    $allowed_users = [
        18,
        16767,
        13683,
        17993,
        7822,
        4108,
        6273,
        470432,
        457115
    ];
	if ( !in_array($_SESSION["user"]["user_id"], $allowed_users) ) {
		die("You do not have required priviledges to use this feature, lol.");
	}

	function validate_subs($subs_id) {

		$res_subs = db_query("SELECT combo, combo_free FROM subs WHERE (status='active' OR status='pending') AND subs_id=".$subs_id);

		if (!isset($res_subs[0])) {
			return -2;
		}

		$res_subs = $res_subs[0];
		$combo = $res_subs["combo"];
		if (strlen($res_subs["combo_free"]) != 0) {
			$combo .= ";".$res_subs["combo_free"];
		}

		$combo_arr = explode(";", $combo);

		$res_enr = db_query("SELECT * FROM user_enrollment WHERE status='active' AND subs_id=".$subs_id);

		if (!isset($res_enr[0])) {
			return 0;
		}

		if (count($res_enr) < count($combo_arr)) {

			$pending_enrs = array();
			$available_enrs = array();
			foreach ($res_enr as $enr) {
				$available_enrs[] = $enr["course_id"].",".($enr["learn_mode"] == "il" ? "1" : "2");
			}
			foreach ($combo_arr as $combo) {

				if (!in_array($combo, $available_enrs)) {
					$pending_enrs[] = explode(",", $combo)[0];
				}

			}
			return $pending_enrs;

		}

		if (count($res_enr) == count($combo_arr)) {
			return 1;
		}

	}

	if (!empty($_GET["subs"])) {

		load_module("user_enrollment");

		$subs_id = $_GET["subs"];
		$sis = !empty($_GET["sis"]) ? false : true;

		$situation = validate_subs($subs_id);

		if (!is_array($situation) && $situation == 0) {

			enr_create($subs_id);
			if ($sis) {
				db_exec("UPDATE user_enrollment SET sis_status='ul', shall_notify=0 WHERE subs_id=".$subs_id);
			}

			db_exec("UPDATE user_enrollment SET shall_notify=0 WHERE subs_id=".$subs_id);

		}
		else if (is_array($situation)) {

			$res_subs = db_query("SELECT start_date FROM subs WHERE subs_id = ".$_GET["subs"]);
			$res_subs_meta = db_query("SELECT batch_id FROM subs_meta WHERE subs_id = ".$_GET["subs"]);
			$bootcamp_batch_id = false;
			if (!empty($res_subs_meta) && !empty($res_subs_meta[0]["batch_id"])) {
				$bootcamp_batch_id = $res_subs_meta[0]["batch_id"];
			}

			$date = date_create_from_format("Y-m-d H:i:s", $res_subs[0]["start_date"]);

			$lm = 2;

			$ml_date = date_create_from_format("Y-m-d H:i:s", "2017-09-01 00:00:00");
			if ($date > $ml_date) {
				$lm = 3;
			}

			$enr = db_query("SELECT * FROM user_enrollment WHERE subs_id=".$subs_id." LIMIT 1;");
			$enr = $enr[0];

			foreach ($situation as $course_id) {

				//$section = db_query("SELECT sis_id FROM course WHERE course_id=".$course_id);
				$section = section_get_for_date_create($course_id, $date, $lm, "", false, $bootcamp_batch_id);
				//$section = $section[0]["sis_id"].$date->format("M")."SP".$date->format("y");

				db_exec("INSERT INTO user_enrollment (user_id, subs_id, course_id, learn_mode, sis_id, lms_pass, section_id, lab_user, lab_pass, lab_status".($sis ? ", sis_status" : "" ).", shall_notify) VALUES (".$enr["user_id"].",".$subs_id.",".$course_id.",'".($lm == 2 ? "sp" : "ml")."','".$enr["sis_id"]."','".$enr["lms_pass"]."','".$section["id"]."','".$enr["lab_user"]."','".$enr["lab_pass"]."','ul'".($sis ? ", 'ul'" : "").", 0);");

			}

		}
		if ($situation != 1) {
			$result = validate_subs($subs_id);
		}

	}

?>
<html>
<head>
	<title>Enrollment creation page for Himanshu Malpande only</title>
</head>
<body>
	<center>
		<form>
			Subs ID: <input type="number" name="subs"><br>
			SIS import? <input type="checkbox" name="sis"><br>
			<input type="submit" value="Try">
		</form>
	</center>
	<br><br>
	<?php if (isset($result) || isset($situation)) { ?>
	<center>
		<?php if (!isset($result) && $situation == 1) {
			echo "Everything is fine, nothing had to be done.";
		}
		else if (is_array($result) || $result == 0) {
			echo "Enrollment creation failed. Please try again.";
			if (is_array($result)) {
				var_dump($result);
			}
		}
		else if ($result == -2) {
			echo "Check the subs_id macha... It does not exist.";
		}
		else if ($result == 1) {
			echo "Done. Now remember to make neccessary changes like learn_mode and lab_ip in the enrollments.";
		}
		?>
	</center>
	<?php } ?>
</body>
</html>