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

	function section_get_for_date_create($course_id, $start_date, $learn_mode, $sis_id = "", $forced_lm = false, $bootcamp_batch_id = false, $admin = "", $users = "") {

		if (!empty($bootcamp_batch_id)) {
			$learn_mode = 2;
		}

		$res = section_get_for_date($course_id, $start_date, $learn_mode, $forced_lm, $bootcamp_batch_id);
		if (!$res) {
			$res = section_create($course_id, $start_date, $learn_mode, $sis_id, $admin, $users, $forced_lm, $bootcamp_batch_id);
		}
		return $res;

	}

	function section_get_for_date($course_id, $date, $learn_mode, $forced_lm = false, $bootcamp_batch_id = false) {

		if (!empty($bootcamp_batch_id)) {
			$learn_mode = 2;
		}

		list($start_date, $ml_effective) = start_date_get($date, $forced_lm, $learn_mode, $bootcamp_batch_id);
		if ($learn_mode != 4) {
			$learn_mode = $ml_effective ? 3 : $learn_mode;
		}

		$res = db_query("SELECT * FROM course_section WHERE course_id=".$course_id." AND start_date=".db_sanitize($start_date->format("Y-m-d H:i:s"))." AND learn_mode=".$learn_mode.(!empty($bootcamp_batch_id) ? " AND bootcamp_batch_id = ".$bootcamp_batch_id : ""));
		if (!isset($res[0])) {
			return false;
		}
		return $res[0];

	}

	function section_get_by_id($id) {

		$res = db_query("SELECT * FROM course_section WHERE id=".$id);
		if (!isset($res[0])) {
			return false;
		}
		return $res[0];

	}

	function section_get_by_sis_id($sis_id) {

		$res = db_query("SELECT * FROM course_section WHERE sis_id=".db_sanitize($sis_id));
		if (!isset($res[0])) {
			return false;
		}
		return $res[0];

	}

	function section_create($course_id, $start_date, $learn_mode = 2, $sis_id = "", $admin = "", $users = "", $forced_lm = false, $bootcamp_batch_id = false) {

		if (!empty($bootcamp_batch_id)) {
			$learn_mode = 2;
		}

		$res = section_get_for_date($course_id, $start_date, $learn_mode, $forced_lm, $bootcamp_batch_id);
		if ($res) {
			return $res;
		}

		list($start_date, $ml_effective) = start_date_get($start_date, $forced_lm, $learn_mode, $bootcamp_batch_id);
		if (!is_numeric($learn_mode)) {
			$learn_mode = (strtolower($learn_mode) == "sp" ? 2 : (strtolower($learn_mode) == "il" ? 1 : 3));
		}
		if ($learn_mode != 4) {
			$learn_mode = $ml_effective ? 3 : $learn_mode;
		}

		if (strlen($sis_id) == 0) {

			$course = db_query("SELECT sis_id FROM course WHERE course_id=".$course_id);
			$sis_id = (!empty($bootcamp_batch_id) ? "B".$bootcamp_batch_id."_" : "").$course[0]["sis_id"].$start_date->format("M").($learn_mode == 4 ? "ML" : ($learn_mode == 3 ? "ML" : ($learn_mode == 2 ? "SP" : "IL"))).$start_date->format("y");

		}
		if (is_array($admin)) {
			$admin = implode(";", $admin);
		}
		if (is_array($users)) {
			$users = implode(";", $users);
		}

		$admin = strlen($admin) > 0 ? db_sanitize($admin) : "NULL";
		$users = strlen($users) > 0 ? db_sanitize($users) : "NULL";

		if (empty($bootcamp_batch_id)) {
			$bootcamp_batch_id = "NULL";
		}

		db_exec("INSERT INTO course_section (course_id, start_date, learn_mode, bootcamp_batch_id, sis_id, admin, users) VALUES (".$course_id.",".db_sanitize($start_date->format("Y-m-d H:i:s")).",".db_sanitize($learn_mode).",".$bootcamp_batch_id.",".db_sanitize($sis_id).",".$admin.",".$users.");");
		$id = db_get_last_insert_id();

		return ["id" => $id, "course_id" => $course_id, "learn_mode" => $learn_mode, "start_date" => $start_date, "bootcamp_batch_id" => $bootcamp_batch_id, "sis_id" => $sis_id, "admin" => $admin, "users" => $users];

	}

	function section_update($section) {

		if (!isset($section["id"])) {
			return;
		}

		$start_date = strlen($section["start_date"]) > 0 ? start_date_get($section["start_date"]) : "NULL";
		$bootcamp_batch_id = empty($section["bootcamp_batch_id"]) ? "NULL" : db_sanitize($section["bootcamp_batch_id"]);
		$admin = strlen($section["admin"]) > 0 ? db_sanitize($section["admin"]) : "NULL";
		$users = strlen($section["users"]) > 0 ? db_sanitize($section["users"]) : "NULL";

		db_exec("UPDATE course_section SET course_id=".$section["course_id"].", learn_mode=".db_sanitize(strtolower($section["learn_mode"])).", bootcamp_batch_id=".$bootcamp_batch_id.", sis_id=".db_sanitize($section["sis_id"]).", start_date=".$start_date.", admin=".$admin.", users=".$users." WHERE id=".$section["id"]);

	}

	function start_date_get($date, $forced_lm = false, $lm = 3, $bootcamp_batch_id = false) {

		if ($bootcamp_batch_id !== false) {

			$batch = db_query("SELECT start_date FROM bootcamp_batches WHERE id = ".$bootcamp_batch_id);
			$ret_date = date_create_from_format("Y-m-d", $batch[0]["start_date"]);

			return [$ret_date->setTime(0, 0, 0), false];

		}

		if (!is_a($date, "DateTime")) {

			if (strlen($date) == 0) {
				return false;
			}
			$date = date_create_from_format("Y-m-d H:i:s", $date);
			if ($date === false) {
				return false;
			}

		}
		$ret_date = clone $date;

		$ml_effective = false;
		if (!isset($GLOBALS["ml_effective"])) {

			if (intval($ret_date->format("Y")) > 2017) {
				$ml_effective = true;
			}
			else if (intval($ret_date->format("Y")) == 2017 && intval($ret_date->format("m")) >= 11) {
				$ml_effective = true;
			}

		}
		else {
			$ml_effective = $GLOBALS["ml_effective"];
		}

		if ($forced_lm) {
			$ml_effective = ($lm == 3);
		}

		if ($ml_effective) {

			if (intval($ret_date->format("d")) < 16) {
				$ret_date->setDate(intval($date->format("Y")), intval($date->format("m")), 15);
			}
			else {
				$ret_date->setDate(intval($date->format("Y")), intval($date->format("m")) + 1, 15);
			}

		}
		else {
			$ret_date->setDate(intval($date->format("Y")), intval($date->format("m")), 1);
		}

		return [$ret_date->setTime(0, 0, 0), $ml_effective];

	}

	function section_sis_import($section) {

		$jlc = new JLC();

		$sis_path = setting_get("sis.file.save_path")."sections/".$section["sis_id"].".csv";

		$line = "id,course_id,name,status,start_date,end_date\r\n";
		$course = course_get_info_by_id($section["course_id"]);
		$start_date = start_date_get($section["start_date"]);
		$section_name = section_name_get($section, $course);
		$line .= $section["sis_id"].",".$course["course_id"].",".$section_name.",active,".$start_date->format("Y-m-d H:i:s").",\r\n";

		$file = fopen($sis_path, "w");
		fwrite($file, $line);
		fclose($file);

		$response = false;
		$retry = 0;
		while (!$response && $retry < 3) {

			if ($response === false) {
				$response = $jlc->sisImport($file);
			}
			else {
				$response = $jlc->sisImportStatus($response["id"]);
			}

			if ($response !== false) {

				$response = json_decode($response, true);

				if ($response["workflow_state"] == "imported") {
					return;
				}

			}

			$retry++;

		}

		if ($response === false) {
			activity_create("critical", "sis.section.import", "fail", "", "", "", "", "Unable to connect to JLC", "pending");
		}
		else {
			activity_create("critical", "sis.section.import", "fail", "course_section", $section["id"], "sis_batch", $response["id"], json_encode($response), "pending");
		}

	}

	function sections_sis_import($sections) {

		$jlc = new JLC();
		$date = new DateTime();

		$sis_path = setting_get("sis.file.save_path")."sections/".$date->format("Y-m-d.H.i.s").".csv";

		$line = "id,course_id,name,status,start_date,end_date\r\n";

		foreach ($sections as $section) {

			$course = course_get_info_by_id($section["course_id"]);
			$start_date = start_date_get($section["start_date"]);
			$section_name = section_name_get($section, $course);
			$line .= $section["sis_id"].",".$course["course_id"].",".$section_name.",active,".$start_date->format("Y-m-d H:i:s").",\r\n";

		}

		$file = fopen($sis_path, "w");
		fwrite($file, $line);
		fclose($file);

	}

	function section_name_get($section, $course = null) {

		if ($course === null) {
			$course = course_get_info_by_id($section["course_id"]);
		}
		$start_date = start_date_get($section["start_date"]);
		return $start_date->format("F Y")/*." - ".($section["learn_mode"] == "3" ? "Mentor Led" : ($section["learn_mode"] == 2 ? "Regular" : "Premium"))*/;

	}

?>