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
		header('Location: https://www.jigsawacademy.com');
		die();
	}

	load_library("email");

	$res_sis_ids = db_query(
		"SELECT
			DISTINCT enr.sis_id
		FROM
			user_enrollment AS enr
		INNER JOIN
			subs
			ON subs.subs_id=enr.subs_id
		INNER JOIN
			subs_meta AS m
			ON m.subs_id = subs.subs_id
		WHERE
			DATE_ADD(DATE(subs.start_date), INTERVAL 7 DAY) = CURDATE()
			AND m.bundle_id NOT IN (126, 127);"
	);

	$res_ipba = db_query("SELECT e.sis_id FROM user_enrollment AS e INNER JOIN subs AS s ON s.subs_id = e.subs_id INNER JOIN subs_meta AS m ON m.subs_id = s.subs_id INNER JOIN bootcamp_batches AS bb ON bb.id = m.batch_id WHERE s.status = 'active' AND m.bundle_id IN (126, 127) AND DATE_SUB(DATE(bb.start_date), INTERVAL 1 DAY) = CURDATE() GROUP BY s.subs_id;");

	$res_sis_ids = array_merge($res_sis_ids, $res_ipba);

	if ($res_sis_ids === false) {
		exit();
	}

	load_plugin("jlc");
	$jlc = new JLC;

	$post_data = [];
	foreach ($res_sis_ids as $sis_id) {
		$post_data[] = $sis_id["sis_id"];
	}

	$response = false;
	$retry = 0;

	while ($response === false) {

		$response = $jlc->apiNew("users/permissions_add", ["data" => http_build_query(["sis_id" => implode(";", $post_data)]), "content_type" => "application/x-www-form-urlencoded"]);

		if ($response === false) {
			$retry++;
		}
		else {

			$response = json_decode($response, true);
			send_email("jlc.permissions.notify", [], ["success" => true, "sis_ids" => $post_data, "skipped" => $response["skipped"]]);
			break;

		}

		if ($retry >= 2) {

			send_email("jlc.permissions.notify", [], ["success" => false, "sis_ids" => $post_data]);
			break;

		}

	}

?>