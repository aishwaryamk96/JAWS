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

	// Check Auth
	if (!auth_api("jobs.get")) {
		die("You do not have the required priviledges to use this feature.");
	}

	// Load prerequisites
	load_library("misc");

	$jobs_arr = content_get("mobile.jobs");
	if (!$jobs_arr) {
		die(json_encode(array("Items" => false, "Count" => 0, "ScannedCount" => 0)));
	}

	$jobs_arr = array_reverse(json_decode($jobs_arr, true));
	$jobs = array();
	foreach ($jobs_arr as $job) {

		$jobs[] = array(
			"jobid" => $job["id"],
			"title" => $job["t"],
			"description" => $job["d"],
			"company" => $job["co"],
			"city" => $job["ci"],
			"vacancy" => (empty($job["v"]) ? "N/A" : $job["v"]),
			"status" => $job["s"]
		);

	}

	die(json_encode(array("Items" => $jobs, "Count" => count($jobs), "ScannedCount" => count($jobs))));

?>