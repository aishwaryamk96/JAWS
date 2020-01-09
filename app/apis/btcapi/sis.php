<?php

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header('Location: ../index.php');
		die();
	}

	load_library("setting");

    if (isset($_GET["sis"]) || isset($_POST["user_id"])) {

		$filename;
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

?>