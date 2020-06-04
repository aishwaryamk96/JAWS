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

	define ("EOL", "\r\n");
	define ("SECTION_FILE", "batch-");
	define ("USER_FILE", "user-");
	define ("ENROLL_FILE", "enroll-");
	define ("ZIP_FILE", "sis-");
	define ("ZIP_FILE_FREE", "free-");

	function initialize() {
		define ("SIS_PATH", setting_get("sis.file.save_path"));
	}

	function sis_import($subs_id = false) {

		initialize();

		if (empty($subs_id)) {

			// Old SIS import status check
			sis_import_previous_status_check();

		}

		if (($filename_timestamp = sis_records_process($subs_id)) === false) {
			return;
		}

		$result = sis_import_do($filename_timestamp, $subs_id);
		if (empty($subs_id)) {

			if ($result !== true) {

				activity_create("critical", "sis.import.automation", "fail", "", "", "", "", $result." - ".$filename_timestamp, "pending");
				notify_sis_import_failure($filename_timestamp);

			}

		}
		else {
			return $result;
		}

	}

	function sis_import_previous_status_check($sis_batch_id_pending = false) {

		// Start with checking the status of any old SIS import batches
		$free = true;
		if (empty($sis_batch_id_pending)) {

			$sis_batch_id_pending = setting_get("sis.user.pending_id", "nil");
			$free = false;

		}
		if (strcmp($sis_batch_id_pending, "nil") != 0) {

			// Initialize the plugin
			$jlc = new JLC();

			// Get the SIS record
			$sis = sis_get_by_sis_batch_id($sis_batch_id_pending);

			// Send a request to the LMS with the SIS Batch ID to get it's status
			$result = $jlc->sisImportStatus($sis_batch_id_pending);
			$result = json_decode($result, true);
			$sis_file_last = setting_get("sis.file.last");
			if (strcmp($result["workflow_state"], "imported") == 0)/* || strcmp($result["workflow_state"], "imported_with_messages") == 0)*/ {

				// If the SIS import was successful, update sis_status to 'ul' for the corresponding records
				db_exec("UPDATE user_enrollment SET sis_status='ul' WHERE sis_file=".db_sanitize($sis_file_last));

				$sis["response_at"] = new DateTime("now");
				$sis["response"] = json_encode($result);
				$sis["status"] = "completed";
				sis_update($sis);

				if (!$free) {
					welcome_email_send($sis_file_last);
				}
				else {
					return 1;
				}

			}
			else if ($free && $result["workflow_state"] == "created") {
				return 0;
			}
			else {

				// Log the error and send some notification
				activity_create("critical", "sis.import.automation", "fail", "", "", "", "", "Import of sis-".$sis_file_last.".zip failed on LMS", "pending");

				$sis["response"] = json_encode($result);
				$sis["response_at"] = new DateTime("now");
				$sis["status"] = "completed";
				sis_update($sis);

				$result["filename"] = $sis_file_last;
				notify_sis_import_failure($result);

				if ($free) {
					return -1;
				}

			}

			if (!$free) {
				setting_set("sis.user.pending_id", "nil");
			}

		}

	}

	function sis_records_process($subs_id = false) {

		// Get the records awaiting SIS import
		if (empty($subs_id)) {
			$query = "SELECT enr_id, user_id, subs_id, course_id, section_id, learn_mode, sis_id, lms_pass, status FROM user_enrollment WHERE sis_status='na';";
		}
		else {
			$query = "SELECT enr_id, user_id, subs_id, course_id, section_id, learn_mode, sis_id, lms_pass, status FROM user_enrollment WHERE subs_id = $subs_id AND sis_status='na';";
		}
		$res_enrs = db_query($query);

		if (!isset($res_enrs[0])) {
			return false;
		}

		// Batch SIS file header
		$section_lines = "section_id,course_id,name,status,is_mentor_led,has_custom_topic,has_online_videos_topic".EOL;
		// User SIS file header
		$user_lines = "user_id,login_id,authentication_provider_id,password,first_name,last_name,short_name,email,status".EOL;
		// Enroll SIS file header
		$enroll_lines = "course_id,user_id,role,section_id,status,program_id,program_duration".EOL;

		// Create a file name format
		$date = new DateTime("now");
		$filename_timestamp = $date->format("Y-m-d.H.i.s");

		$sections_done = [];
		$users_done = [];
		// Start processing them one by one
		foreach ($res_enrs as $enr) {

			if (!in_array($enr["section_id"], $sections_done) && ($enr["status"] == 'active' || $enr["status"] == 'alumni')) {

				$section_lines .= section_line_get($enr["section_id"]).EOL;
				$batch[] = $enr["section_id"];
				$sections_done[] = $enr["section_id"];

			}

			if (!in_array($enr["user_id"], $users_done)) {

				$user_lines .= user_line_get($enr["user_id"], $enr["subs_id"], $enr["sis_id"], $enr["lms_pass"]).EOL;
				$users_done[] = $enr["user_id"];

			}

			$enroll_lines .= enroll_line_get($enr["user_id"], $enr["course_id"], $enr["sis_id"], $enr["section_id"], $enr["subs_id"], $enr["status"]).EOL;

			db_exec("UPDATE user_enrollment SET sis_status='dl', sis_file=".db_sanitize($filename_timestamp)." WHERE enr_id=".$enr["enr_id"]);

		}

		if (($write_status = file_write($filename_timestamp, $user_lines, $enroll_lines, $section_lines, $subs_id)) !== true) {

			activity_create("critical", "sis.file.creation", "fail", "", "", "", "", $write_status." ".$filename_timestamp, "pending");
			//activity_log("SIS Import failed","","",["it","for_"."3"],["c" => "danger"]);
			db_exec("UPDATE user_enrollment SET sis_status='na' WHERE sis_file=".db_sanitize($filename_timestamp));
			notify_sis_write_failure($write_status." ".$filename_timestamp);

			return false;

		}

		if (empty($subs_id)) {

			// Save this file name timestamp in DB for later use
			setting_set("sis.file.last", $filename_timestamp);

			// Send a mail to notify the SIS file creation
			notify_sis_creation($filename_timestamp);

		}

		return $filename_timestamp;

	}

	function section_line_get($section_id) {

		$section = db_query("SELECT s.sis_id, CONCAT(MONTHNAME(s.start_date), ' ', YEAR(s.start_date)) AS name, s.learn_mode, c.learn_modes, c.sis_id AS course_code, c.section_meta FROM course_section AS s INNER JOIN course AS c ON c.course_id = s.course_id WHERE s.id = ".$section_id)[0];
		if ($section["learn_modes"] == 4) {
			$section["learn_mode"] = 4;
		}
		$has_custom_topic = false;
		if (!empty($section["section_meta"])) {

			$section_meta = json_decode($section["section_meta"], true);
			$has_custom_topic = !empty($section_meta["ibm_content"]);

		}

		return $section["sis_id"].",".$section["course_code"].",".$section["name"].",active,,,"/*.($section["learn_mode"] == 3 ? "true," : "false,").($has_custom_topic ? "true" : "false").",".($section["learn_mode"] == 4 ? "true" : "false")*/;

	}

	function user_line_get($user_id, $subs_id, $sis_id, $lms_pass) {

		$user_line = $sis_id.",";

		// Get the user info
		$user = user_get_by_id($user_id);
		$email = $user["email"];

		// If the login method is corp, user will require password to login
		if ($user["lms_soc"] == "corp") {
			$user_line .= $user["email"].",,".$lms_pass.",";
		}
		// If the login method is social, user will require a social service
		else {

			$soc = "linkedin";
			if ($user["lms_soc"] == "fb") {
				$soc = "facebook";
			}
			else if ($user["lms_soc"] == "gp") {
				$soc = "google";
			}

			$email = $user["soc_".$user["lms_soc"]];
			$user_line .= $email.",".$soc.",,";

		}

		// Default the secondary email to the login email
		$user_line .= str_replace(",", "-", $user["name"]).",,".str_replace(",", "-", $user["name"]).",".$email.",";

		// Default status for the user
		$status = "active";
		// Get the subs info and gather the correct status for the user
		$subs = db_query("SELECT end_date, end_date_ext, status FROM subs WHERE subs_id=".$subs_id)[0];
		if ($subs["status"] == "expired") {
			$status = "deleted";
		}
		else if ($subs["status"] == "frozen" || $subs["status"] == "blocked") {
			$status = "inactive";
		}else if($subs["status"]== "alumni"){
            $status= "alumni";
        }

		// Complete the line and return
		$user_line .= $status;
		return $user_line;

	}

	function enroll_line_get($user_id, $course_id, $sis_id, $section_id, $subs_id, $enr_status) {

		// Get the course SIS Id
		$course = db_query("SELECT sis_id FROM course WHERE course_id=".$course_id)[0];

		$prog_name = "";
		$prog_duration  = "";

		$bundle_info = db_query("SELECT bundle.code AS code, bundle.subs_duration_length, bundle_meta.category, meta.batch_id, CEIL(DATEDIFF(DATE(subs.end_date), DATE(subs.start_date)) / 30) AS program_duration FROM course_bundle AS bundle INNER JOIN course_bundle_meta AS bundle_meta ON bundle_meta.bundle_id = bundle.bundle_id INNER JOIN subs_meta AS meta ON meta.bundle_id = bundle.bundle_id INNER JOIN subs ON subs.subs_id = meta.subs_id WHERE bundle.is_bootcamp = 0 AND meta.subs_id = ".$subs_id);
		if (!empty($bundle_info)) {

			$bundle_info = $bundle_info[0];

			if (empty($bundle_info["batch_id"])) {

				if (in_array("full-stack", explode(";", $bundle_info["category"])) && !empty($bundle_info["code"])) {

					$prog_name = $bundle_info["code"];
					$prog_duration = $bundle_info["subs_duration_length"];

				}

			}
			else {

				$prog_name = $bundle_info["code"];
				$prog_duration = $bundle_info["program_duration"];

			}

		}

		$enroll_line = $course["sis_id"].",".$sis_id.",";

		$role = "student";
		$status = "active";
		// Get the subs info for the role
		$subs = db_query("SELECT status FROM subs WHERE subs_id=".$subs_id)[0];
		if ($subs["status"] == "alumni") {
			$role = "alumni";
		}
		else if ($status == "expired") {
			$status = "deleted";
		}

		if ($enr_status != "active") {
			$status = "deleted";
		}

		$enroll_line .= $role.",";
		$section = section_get_by_id($section_id);
		$enroll_line .= $section["sis_id"].",".$status.",".$prog_name.",".$prog_duration;
		return $enroll_line;

	}

	function file_write($filename_format, $user_text, $enroll_text, $section_text, $subs_id = false) {

		// Set the file names
		$zip_filename = SIS_PATH.(empty($subs_id) ? ZIP_FILE : ZIP_FILE_FREE).$filename_format.".zip";
		$section_filename = SIS_PATH.SECTION_FILE.$filename_format.".csv";
		$user_filename = SIS_PATH.USER_FILE.$filename_format.".csv";
		$enroll_filename = SIS_PATH.ENROLL_FILE.$filename_format.".csv";

		// Create the zip file
		$zip = new ZipArchive();
		if (($zip_status = $zip->open($zip_filename, ZipArchive::CREATE)) !== true) {
			return "Zip creation failed: ".zip_archive_error_string_get($zip_status);
		}

		// Create the section file only if there is something to be written
		if (strlen($section_text) > 0) {

			// Write the user file
			if (($section_file = fopen($section_filename, "a")) === false) {
				return "Section file creation failed";
			}
			if (fwrite($section_file, $section_text) === false) {

				fclose($section_file);
				return "Section file write failed";

			}
			fclose($section_file);

			// Add the file to zip
			if ($zip->addFile($section_filename, "section.csv") === false) {
				return "Could not add section file to zip";
			}

		}

		// Create the user file only if there is something to be written
		if (strlen($user_text) > 0) {

			// Write the user file
			if (($user_file = fopen($user_filename, "a")) === false) {
				return "User file creation failed";
			}
			if (fwrite($user_file, $user_text) === false) {

				fclose($user_file);
				return "User file write failed";

			}
			fclose($user_file);

			// Add the file to zip
			if ($zip->addFile($user_filename, "user.csv") === false) {
				return "Could not add user file to zip";
			}

		}

		// Create the enrollments file only if there is something to be written
 		if (strlen($enroll_text) > 0) {

			// Write the enrollments file
			if (($enroll_file = fopen($enroll_filename, "a")) === false) {
				return "Enrollment file creation failed";
			}
			if (fwrite($enroll_file, $enroll_text) === false) {

				fclose($enroll_file);
				return "Enroll file write failed";

			}
			fclose($enroll_file);

			// Add the file to zip
			if ($zip->addFile($enroll_filename, "enroll.csv") === false) {
				return "Could not add enrollment file to the zip";
			}

		}

		// Close the file
		$zip->close();

		// Delete the original files
		if (strlen($section_text) > 0) {
			unlink($section_filename);
		}
		if (strlen($user_text) > 0) {
			unlink($user_filename);
		}
		if (strlen($enroll_text) > 0) {
			unlink($enroll_filename);
		}

		return true;

	}

	function sis_import_do($filename_timestamp, $subs_id = false) {

		$full_filename = SIS_PATH.(empty($subs_id) ? ZIP_FILE : ZIP_FILE_FREE).$filename_timestamp.".zip";

		if (($fzip = fopen($full_filename, "rb")) === false) {
			return "Zip file access error";
		}
		if (($zip_content = fread($fzip, filesize($full_filename))) === false) {
			return "Zip file read error";
		}

		// Initialize JLC plugin
		$jlc = new JLC();

		$result = false;
		$retry = 0;

		$sis_data = ["filename" => $filename_timestamp, "full_filename" => $full_filename, "imported_at" => (new DateTime("now"))];
		if (!empty($subs_id)) {
			$sis_data["free_subs"] = $subs_id;
		}

		$sis = sis_update($sis_data);

		while ($result === false) {

			// Send the zip file to LMS
			$result = $jlc->sisImport($zip_content);
			$retry++;
			if (!$result) {

				if ($retry > 2) {

					db_exec("UPDATE user_enrollment SET sis_status='na' WHERE sis_file=".db_sanitize($filename_timestamp).";");

					$sis["status"] = "error";
					sis_update($sis);

					return "SIS import for file ".$filename_timestamp." failed thrice. CUrl seems to have failed.";

				}

			}
			else {

				$result = json_decode($result, true);
				// All went well
				if (strcmp($result["workflow_state"], "imported") == 0) {

					// Update the sis_status of the present record to 'ul'
					db_exec("UPDATE course_enrollment SET sis_status='ul' WHERE sis_file=".db_sanitize($filename_timestamp));
					activity_create("low", "sis.import.automation", "success", "", "", "", "", "SIS Import successful at ".$filename_timestamp.". ".$count." records imported.");

					$sis["response"] = json_encode($result);
					$sis["response_at"] = new DateTime("now");
					$sis["status"] = "completed";
					$sis["sis_batch_id"] = $result["id"];
					sis_update($sis);

					if (empty($subs_id)) {
						welcome_email_send($filename_timestamp);
					}

				}
				// Import is taking time
				else if (strcmp($result["workflow_state"], "created") == 0) {

					if (empty($subs_id)) {

						// Save the import ID to check the status later and break the loop
						setting_set("sis.user.pending_id", $result["id"]);

					}

					$sis["sis_batch_id"] = $result["id"];
					$sis["status"] = "sent";
					sis_update($sis);

				}
				else {

					//activity_create("critical", "sis.import.automation", "fail", "", "", "", "", "SIS import for file ".$filename_timestamp." failed on JLC. Reason received: ".json_encode($result), "pending");
					$result["filename"] = $filename_timestamp;
					//notify_sis_import_failure($result);
					db_exec("UPDATE user_enrollment SET sis_status='na' WHERE sis_file=".db_sanitize($filename_timestamp).";");

					$sis["response_at"] = new DateTime("now");
					$sis["status"] = "failed";
					$sis["response"] = json_encode($result);
					$sis["sis_batch_id"] = $result["id"];
					sis_update($sis);

					return $result;

				}

				break;

			}

		}

		return true;

	}

	function sis_update($sis) {

		$filename = db_sanitize($sis["filename"]);
		$full_filename = db_sanitize($sis["full_filename"]);
		$imported_at = (is_a($sis["imported_at"], DateTime::class) ? $sis["imported_at"]->format("Y-m-d H:i:s") : $sis["imported_at"]);
		$imported_at = db_sanitize($imported_at);
		$free_subs = false;
		if (!empty($sis["response_at"])) {

			$response_at = (is_a($sis["response_at"], DateTime::class) ? $sis["response_at"]->format("Y-m-d H:i:s") : $sis["response_at"]);
			$response_at = db_sanitize($response_at);

		}
		if (!empty($sis["sis_batch_id"])) {
			$sis_batch_id = $sis["sis_batch_id"];
		}
		if (!empty($sis["response"])) {
			$response = db_sanitize($sis["response"]);
		}
		if (!empty($sis["status"])) {
			$status = db_sanitize($sis["status"]);
		}
		if (!empty($sis["free_subs"])) {
			$free_subs = db_sanitize($sis["free_subs"]);
		}

		if (empty($sis["id"])) {

			db_exec("INSERT INTO sis (filename, full_filename, imported_at".(!empty($response_at) ? ", response_at" : "").(!empty($sis_batch_id) ? ", sis_batch_id" : "").(!empty($response) ? ", response" : "").(!empty($status) ? ", status" : "").(!empty($free_subs) ? ", free_subs" : "").") VALUES (".$filename.", ".$full_filename.", ".$imported_at.(!empty($response_at) ? ", ".$response_at : "").(!empty($sis_batch_id) ? ", ".$sis_batch_id : "").(!empty($response) ? ", ".$response : "").(!empty($status) ? ", ".$response : "").(!empty($free_subs) ? ", ".$free_subs : "").");");
			$sis["id"] = db_get_last_insert_id();

		}
		else {
			db_exec("UPDATE sis SET filename=".$filename.", full_filename=".$full_filename.", imported_at=".$imported_at.(!empty($response_at) ? ", response_at=".$response_at : "").(!empty($sis_batch_id) ? ", sis_batch_id=".$sis_batch_id : "").(!empty($response) ? ", response=".$response : "").(!empty($status) ? ", status=".$status : "")." WHERE id=".$sis["id"]);
		}

		return $sis;

	}

	function sis_get_by_sis_batch_id($sis_batch_id) {

		$res = db_query("SELECT * FROM sis WHERE sis_batch_id=".$sis_batch_id);
		if (!isset($res[0])) {
			return false;
		}

		return $res[0];

	}

	function sis_get_by_free_subs($free_subs) {

		$res = db_query("SELECT * FROM sis WHERE free_subs=".$free_subs);
		if (!isset($res[0])) {
			return false;
		}

		return $res[0];

	}

?>