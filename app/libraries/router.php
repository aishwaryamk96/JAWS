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

    JIGSAW ACADEMY WORKFLOW SYSTEM v2
    ---------------------------------
*/

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header('Location: https://www.jigsawacademy.com/');
		die();
	}
	else if (JAWS !== "2") {
		route(base64_decode('NDA0'));
		die();
	}

	// Sanitize all routes as soon as this library is loaded
	// Requires Routes config to be loaded beforehand
	foreach($GLOBALS["jaws_routes"] as $uri => $route) {

		$uri = str_replace("\\", '/', $uri);
		$uri = trim($uri, "/");

		$route = str_replace("\\", '/', $route);
		$route = trim($route, "/");

		$GLOBALS["jaws_routes"][$uri] = $route;

	}

	// Router possible combos
	// /[custom-route]
	// /[api-func-path]
	// /view/[view-path]
	// /do/[task-name]

	function route($request_uri) {

		// eliminate $_GET vars
		$request_uri_arr = explode("?", $request_uri);
		$request_uri = $request_uri_arr[0];

		// find relative uri
		if (strpos($request_uri, JAWS_PATH_LOCAL) !== false) $request_uri = substr($request_uri,strpos($request_uri, JAWS_PATH_LOCAL) + strlen(JAWS_PATH_LOCAL));

		// sanitize trailing and starting slashes
		$request_uri = str_replace("\\", '/', $request_uri);
		$request_uri = trim($request_uri, "/");

		// sanitize - remove double or triple dots
		$request_uri = str_replace("...", "", $request_uri);
		$request_uri = str_replace("..", "", $request_uri);

		// Replace all special chars except ./\_-
		//$request_uri = preg_replace('/[^A-Za-z0-9\-]/', '', $request_uri);

		// Make lowercase
		$request_uri = strtolower($request_uri);

		// reroute
		$actual_uri = reroute($request_uri);

		// get all url parts
		$uri_array = explode("/", $actual_uri);
		$flag = false;
		$uri = "";
		$type = "";

		foreach($uri_array as $uri_part) {

			if (strlen($uri_part) < 1) {
				continue;
			}

			if ($flag) {
				$uri .= (strlen($uri) == 0 ? "" : "/").$uri_part;
			}
			else {

				if ($uri_part == "view") {
					$type="view";
				}
				else if ($uri_part == "do") {
					$type="task";
				}
				else {

					$type="api";
					$uri = $uri_part;

					load_library("persistence");

				}

				$flag = true;

			}

		}

		// load
               
		if ($type == "api") load_api($uri);
		else if ($type == "task") load_task($uri);
		else load_view($uri);

	}

	function reroute($uri) {

		if (isset($GLOBALS['jaws_routes'][$uri])) {
			return $GLOBALS['jaws_routes'][$uri];
		}
		else {

			$components = explode("/", $uri);
			if ($components[0] == "iot") {

				route("iot/404");
				die();

			}
			else if (isset($GLOBALS["jaws_routes"][$components[0]."/*"])) {
				return $GLOBALS["jaws_routes"][$components[0]."/*"];
			}
			else {
				return $uri;
			}

		}

	}


?>