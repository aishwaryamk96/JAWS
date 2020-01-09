<?php

	if (!empty($_POST["m"])) {

		$_SESSION["track"][] = ["u" => $_POST["m"], "t" => time()];
		$m = strtolower($_POST["m"]);

		ignore_user_abort(true);
		set_time_limit(0);

		header('Connection: close');
		header('Content-Length: 0');
		flush();

		load_module("chat");
		$_SESSION["r"] = process_request($m, $_SESSION["user"]["user_id"]);

	}

?>