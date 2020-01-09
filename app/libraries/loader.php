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

	// Config Loader
	function load_config() {

		$dir = "app/config/";
		$file_list = array_diff(scandir($dir), array('..', '.'));
		foreach($file_list as $file_name) if (!($file_name == "autoload.php")) require_once "app/config/".$file_name;

		// Load autoloader files AFTER all other configs are loaded to satisfy dependencies.
		require_once "app/config/autoload.php";

	}

	// Library Loader
	function load_library($library_name) { require_once "app/libraries/".$library_name.".php"; }

	// Module Loader - loads ALL files inside a module's directory
	function load_module($module_name) {

		try {

			$dir = "app/modules/".$module_name;
			$file_list = array_diff(scandir($dir), array('..', '.'));
			foreach($file_list as $file_name) {

				if (!is_dir("app/modules/".$module_name."/".$file_name)) {
					require_once "app/modules/".$module_name."/".$file_name;
				}

			}

		}
		catch(Exception $e) {
			jaws_load_fail('module', $module_name, $e);
		}

	}

	// API Loader
	function load_api($api_func) {

		try {
			try {
				// DEBUG log for every mobile API call ------ REMOVE WHEN APP GOES LIVE!!!
				$api = substr($api_func, 0, strpos($api_func, "/"));
			  	// if ((strcmp($api, "mobapi") == 0) || (strcmp($api, "crmapi") == 0)) {

				if ((strcmp($api, "mobapi") == 0)) {
					$_POST["api"] = $api_func;
					$_POST["ip"] = $_SERVER["REMOTE_ADDR"];
					if (!isset($_POST["jigid"])) {
					  	$id = $_POST['socialid'] ?? $_POST['emailid'] ?? '';
					  	if ($id != '') {
							load_module('user');
							$user = user_get_by_email($id);
							if ($user !== false) mobapp_log($api_func, $user["user_id"], json_encode($_POST));
							else mobapp_log($api_func,  0, json_encode($_POST));
						}
					 	else mobapp_log($api_func, 0, json_encode($_POST));
					}
					else mobapp_log($api_func, $_POST["jigid"], json_encode($_POST));
				}
		  	}
		  	catch(Exception $e) {}

			if (!file_exists("app/apis/".$api_func.".php")) die("You do not have the required priviledges to use this feature.!");
			else require_once "app/apis/".$api_func.".php";

		}
		catch(Exception $e) {
			jaws_load_fail('api', $api_func, $e);
		}
	}

	// View Loader
	function load_view($view_name) {
		try {
			if (!file_exists("app/views/".$view_name.".php")) route("404");
			else require "app/views/".$view_name.".php";
		}
		catch(Exception $e) {
			jaws_load_fail('view', $view_name, $e, true);
		}
	}

	// Template Loader
	function load_template($template_type, $template_name) { require "app/templates/".$template_type."/".$template_name.".php"; }

	// Task Loader
	function load_task($task_name) {
		try {
			if (!auth_is_cli()) die("Error - Tasks can only be run from CLI!");
			else {
				if (!file_exists("app/tasks/".$task_name.".php")) die("JAWS could not locate the specified task - ".$task_name);
				else require_once "app/tasks/".$task_name.".php";
			}
		}
		catch(Exception $e) {
			jaws_load_fail('task', $task_name, $e);
		}
	}

	// Plugin Loader
	function load_plugin($name) {
		try {
			require_once "app/plugins/".$name."/loadplugin.php";
		}
		catch(Exception $e) {
			jaws_load_fail('plugin', $name, $e);
		}
	}

	// Loader Error Handler
	function jaws_load_fail($component_type, $component_name, $e, $output = false) {
		$info = json_encode([
			'msg' => $e->getMessage(),
			'file' => $e->getFile(),
			'line' => $e->getLine(),
			'trace' => $e->getTraceAsString()
		]);

		db_exec('INSERT INTO system_activity (priority, act_type, activity, content, act_date, status) VALUES ("critical", '.db_sanitize($component_type.'.load.fail').', '.db_sanitize($component_name).', '.db_sanitize($info).', '.db_sanitize(strval(date("Y-m-d H:i:s"))).',"logged");');
		die($output ? ('JAWS FATAL ERROR - Loader failed to load component ('.$component_type.") ".$component_name."<br/><br/>".$info) : '');
	}

	function mobapp_log($api, $user_id, $http_post) {
		db_exec("INSERT INTO mob_app_logs (api, user_id, http_post) VALUES (".db_sanitize($api).",".db_sanitize($user_id).",".db_sanitize($http_post).");");
	}

?>
