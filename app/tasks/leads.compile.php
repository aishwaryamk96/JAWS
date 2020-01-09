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

	load_module("leads");
	load_library("setting");

	$leads_compile_lock = setting_get("leads.compile.task.is_running");
	if (strcasecmp($leads_compile_lock, "false") == 0)
	{

		setting_set("leads.compile.task.is_running", "true");
		//$time_start = microtime(true);

		$done = false;

		register_shutdown_function(function() use ($done) {
			if (!empty($error = error_get_last())) {
				$data = db_sanitize(json_encode(["done" => var_export($done, true), "error" => $error]));
				db_exec("INSERT INTO system_log (source, data) VALUES ('leads.compile.check', $data);");
			}
		});

		leads_basic_compile();
		//$time_end = microtime(true);
		//$runtime = round(($time_end - $time_start));
		setting_set("leads.compile.task.is_running", "false");
		$done = true;
		//activity_create("ignore", "debug", "leads.compile.runtime", "", "", "", "", $runtime);
	}
	else
		activity_create("critical", "debug", "leads.compile.runtime", "", "", "", "", "overlap");

?>