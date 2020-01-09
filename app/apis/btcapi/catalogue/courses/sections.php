<?php

	// Send Headers
	header('Access-Control-Allow-Credentials: true');
	header("Content-Type: application/json");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Pragma-directive: no-cache");
	header("Cache-directive: no-cache");
	header("Cache-Control: private, must-revalidate, max-age=0, no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
	header("Pragma: no-cache");

	if (!auth_session_is_allowed("batcave")) {

		header("HTTP/1.1 403");
		die(json_encode(["status" => false, "error" => "Are you trying to hack...? Coz, its not gonna work."]));

	}

	if (!empty($_GET["id"])) {
		die(json_encode(["sections" => section_get_for_course($_GET["id"])]));
	}

	header("HTTP/1.1 404");

?>