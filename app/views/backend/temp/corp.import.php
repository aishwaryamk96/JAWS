<?php

	// load_module("user");

	// $data = file("external/temp/corp.csv");
	// foreach ($data as $line) {

	// 	$line = trim($line);
	// 	$components = explode(",", $line);

	// 	$name = trim(trim($components[3])." ".trim($components[4]));

	// 	$user = user_create(trim($components[1]), $name, $name);
	// 	db_exec("UPDATE user SET lms_soc='corp' WHERE user_id = ".$user["user_id"].";");

	// 	db_exec("INSERT INTO subs (user_id, combo, combo_free, corp, start_date, end_date) VALUES (".$user["user_id"].", '108,2;9,2', '', 'blp', '2017-11-30 00:00:00', '2018-09-30 23:59:59');");
	// 	$subs_id = db_get_last_insert_id();
	// 	db_exec("INSERT INTO subs_meta VALUES (".$subs_id.", NULL, CURRENT_TIMESTAMP, 13683);");

	// 	db_exec(
	// 		"INSERT INTO
	// 			user_enrollment
	// 				(user_id, subs_id, course_id, learn_mode, sis_id, lms_pass, section_id, sis_file, sis_status, lab_ip, lab_user, lab_pass)
	// 		VALUES
	// 			(".$user["user_id"].", ".$subs_id.", 108, 'ml', '".trim($components[0])."', '".trim($components[2])."', 1516, 'xyz', 'ul', 'dataserver1.jigsawacademy.in', '".trim($components[0])."', '".trim($components[2])."'),
	// 			(".$user["user_id"].", ".$subs_id.", 9, 'ml', '".trim($components[0])."', '".trim($components[2])."', 1328, 'xyz', 'ul', NULL, '".trim($components[0])."', '".trim($components[2])."');"
	// 	);

	// }

?>