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

	load_library("setting");
	load_module("ui");

	// Init Session
	auth_session_init();

	// Prep
	$return_url = JAWS_PATH_WEB."/sis";

	$filename;

    if (isset($_GET["sis"]) || isset($_POST["user_id"]) || isset($_GET["course"])) {

		if (isset($_GET["sis"])) {

			if (!isset($_GET["free"])) {
				$filename = "external/sis/sis-".$_GET["sis"].".zip";
			}
			else {
				$filename = "external/sis/free-".$_GET["sis"].".zip";
			}

		}
		else if (isset($_POST["user_id"])) {

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

			// Set the header row for each file
			$user_lines = "user_id,login_id,authentication_provider_id,password,first_name,last_name,short_name,email,status\r\n";
			$enroll_lines = "course_id,user_id,role,section_id,status\r\n";

			// Get the student
			$res_user = db_query("SELECT name, email, lms_soc, soc_fb, soc_gp, soc_li FROM user WHERE user_id=".$_POST["user_id"]);
			$res_user = $res_user[0];

			if (strcmp($res_user["lms_soc"], "fb") == 0) {
				$soc = "facebook";
			}
			else if (strcmp($res_user["lms_soc"], "gp") == 0) {
				$soc = "google";
			}
			else if (strcmp($res_user["lms_soc"], "li") == 0) {
				$soc = "linkedin";
			}
			else {
				$soc = "";
			}

			if (strlen($soc) == 0) {
				$lms_soc = $res_user["email"];
			}
			else {
				$lms_soc = $res_user["soc_".$res_user["lms_soc"]];
			}

			// Get the enrollment records
			$res_sis = db_query("SELECT enr.enr_id, enr.subs_id, enr.course_id, enr.learn_mode, enr.sis_id, enr.lms_pass, section.sis_id AS section_id FROM user_enrollment AS enr INNER JOIN course_section AS section ON section.id = enr.section_id WHERE user_id=".$_POST["user_id"].";");

			if (!isset($res_sis[0])) {
				exit();
			}

			$user_lines .= $res_sis[0]["sis_id"].",".$lms_soc.",".$soc.",".$res_sis[0]["lms_pass"].",".$res_user["name"].",,".$res_user["name"].",".$res_user["email"].",active\r\n";

			$role = "student";

			foreach ($res_sis as $sis) {

				$res_course_sis_id = db_query("SELECT sis_id FROM course WHERE course_id=".$sis["course_id"]);
				$res_course_sis_id = $res_course_sis_id[0];

				$enroll_lines .= $res_course_sis_id["sis_id"].",".$sis["sis_id"].",".$role.",".$sis["section_id"].",active\r\n";

			}

			$filename = $res_sis[0]["sis_id"];

			// Get file save path
			$sis_path = setting_get("sis.file.save_path");
			// Write data to respective files
			$fuser = fopen($sis_path."user-".$filename.".csv", "w");
			if ($fuser === false) {

				var_dump(error_get_last());
				exit();

			}
			$fenroll = fopen($sis_path."enroll-".$filename.".csv", "w");
			fwrite($fuser, $user_lines);
			fwrite($fenroll, $enroll_lines);
			fclose($fuser);
			fclose($fenroll);

			// Zip the files
			$zip = new ZipArchive;
			$zip->open($sis_path."sis-".$filename.".zip", ZipArchive::CREATE);
			$zip->addFile($sis_path."user-".$filename.".csv", "user.csv");
			$zip->addFile($sis_path."enroll-".$filename.".csv", "enroll.csv");
			$zip->close();

			// Delete the original csv files
			//unlink($sis_path."user-".$filename.".csv");
			//unlink($sis_path."enroll-".$filename.".csv");

			$filename = $sis_path."sis-".$filename.".zip";

		}
		else if (isset($_GET["course"])) {

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

			// Set the header row for each file
			$enroll_lines = "course_id,user_id,role,section_id,status\r\n";

			$res_enr = db_query("SELECT enr.sis_id AS sis_id, section.sis_id AS section_id, course.sis_id AS course_code FROM user_enrollment AS enr INNER JOIN subs ON enr.subs_id = subs.subs_id INNER JOIN course ON course.course_id = enr.course_id INNER JOIN course_section AS section ON section.id = enr.section_id WHERE enr.course_id=".$_GET["course"]." AND subs.end_date>CURDATE()");

			foreach ($res_enr as $enr) {
				$enroll_lines .= $enr["course_code"].",".$enr["sis_id"].",student,".$enr["section_id"].",active\r\n";
			}

			// Get file save path
			$sis_path = setting_get("sis_file_save_path");

			$filename = $sis_path."enroll-".$res_enr[0]["course_code"].".csv";
			$fenroll = fopen($filename, "w");
			fwrite($fenroll, $enroll_lines);
			fclose($fenroll);
		}

		header('Pragma: public');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control: private', false);
		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename="'. basename($filename) . '"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . filesize($filename));
		readfile($filename);

		exit();
	}

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

	$courses = db_query("SELECT DISTINCT course_id, sis_id, name FROM course WHERE status='enabled' OR status='hidden' AND sis_id<>'';");

?>

<HTML>
<HEAD>
	<TITLE>SIS Files</TITLE>
</HEAD>
<BODY>
	<DIV>
		<CENTER>
			<B><?php echo substr($domain_name, 0, -3) ?> Download SIS file</B> (You are logged in as: <?php echo $_SESSION["user"]["name"]; ?> <A href="<?php echo JAWS_PATH_WEB."/logout"; ?>">Logout</A>)
			<?php if (isset($msg)) echo "<br/>".$msg; ?>
		</CENTER>
	</DIV><HR>
	<CENTER>
		<FORM method="get">
			<SELECT name="course">
				<OPTION value="select">Select a course to download the SIS file</OPTION>
				<?php foreach ($courses as $course) { ?>
					<OPTION value="<?php echo $course['course_id'] ?>"><?php echo $course["name"]." - ".$course["sis_id"] ?></OPTION>
				<?php } ?>
			</SELECT>
			<INPUT type="submit" value="Go!" />
		</FORM>
	</CENTER>
</BODY>
</HTML>