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

	// $unfreeze = db_query("SELECT enr.sis_id, user.email FROM subs INNER JOIN user_enrollment AS enr ON enr.subs_id = subs.subs_id INNER JOIN user ON user.user_id = subs.user_id WHERE DATE(subs.unfreeze_date) = CURDATE() GROUP BY subs.subs_id;");

	$unfreeze = db_query(
		"SELECT
			enr.sis_id, user.email
		FROM
			freeze
		INNER JOIN
			user
			ON user.user_id = freeze.user_id
		INNER JOIN
			user_enrollment AS enr
			ON enr.user_id = user.user_id
		WHERE
			DATE(freeze.end_date) = CURRENT_DATE
		GROUP BY enr.subs_id;"
	);

	$students = [];
	if (!empty($unfreeze)) {

		foreach ($unfreeze as $student) {
			$students[] = ["jig_id" => $student["sis_id"], "email" => $student["email"]];
		}

		load_library("email");
		send_email("unfreeze.notify", [], ["students" => $students]);

	}

?>