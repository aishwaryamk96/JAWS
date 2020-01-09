<?php

	authorize_api_call("", true);

	$user = false;
	if (!empty($_GET["email"])) {

		load_module("user");
		$user = user_get_by_email($_GET["email"]);

	}

	if (empty($user)) {
		header("HTTP/1.1 404");
	}
	else {
		die(json_encode($user));
	}

?>