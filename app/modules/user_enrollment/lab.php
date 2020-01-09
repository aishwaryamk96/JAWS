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

	function lab_import() {

		// Get the list of enrollments that have lab_status as 'na'
		$res_lab = db_query("SELECT enr_id, user_id, subs_id, course_id, lab_ip, lab_user, lab_pass FROM subs WHERE lab_status='na'");

		if ($res_lab) {

			// Set the header row
			$lab_lines = "Server,FN,LN,UserName,Password,Group\r\n";

			// Get the latest undownloaded lab input file
			$filename = setting_get("lab.file.last");
			if (!$filename || strcmp($filename, "nil") == 0) {
				$filename = "lab-".$filename_timestamp.".csv";
			}

			foreach ($res_lab as $lab_record) {

				$res_user = db_query("SELECT name FROM user WHERE user_id=".$lab_record["user_id"]);
				$res_course_lab = db_query("SELECT dir FROM course_lab WHERE course_id=".$lab_record["course_id"]." AND lab_ip=".$lab_record["lab_ip"]);
				$lab_lines .= $lab_record["lab_ip"].",".$res_user[0]["name"].",,".$lab_record["lab_user"].",".$lab_record["lab_pass"].",".$res_course_lab[0]["dir"]."\r\n";

				db_exec("UPDATE course_enrollment SET lab_status='cr', lab_file='".$filename."' WHERE enr_id=".$lab_record["enr_id"]);

			}

			// Write the Lab file only if the debug is off
			if (!$debug_state) {

				// Get file save path
				$lab_path = setting_get("lab.file.save_path");
				// Write the data to file
				$flab = fopen($lab_path.$filename, "w");
				fwrite($flab, $lab_lines);
				fclose($flab);

			}

			// Put back the file name in system_setting
			setting_set("lab.file.last", $filename);

			// After this, the file link is to be given for downloading
		}

	}

?>