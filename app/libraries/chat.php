<?php

	session_start();
	if (empty($_SESSION["user"])) {

		if (empty($_COOKIE["__chat_u"])) {

			$matches = [];
			if (!preg_match("/\/users\/(.+)\/?.*/", $_SERVER["REQUEST_URI"], $matches)) {

				header("HTTP/1.1 400");
				die;

			}

			$user_id = $matches[1];
			setcookie("__chat_u", $user_id);

		}
		else {
			$user_id = $_COOKIE["__chat_u"];
		}

		$user_id = db_sanitize($user_id);
		$user = db_query("SELECT u.* FROM user AS u INNER JOIN user_enrollment AS e ON e.user_id = u.user_id WHERE e.sis_id LIKE $user_id;");
		if (empty($user)) {
			// Pull info from JLC
		}
		else {
			$user = $user[0];
		}

		$_SESSION["user"] = $user;
		$_SESSION["msg"] = [];

	}
	else if (strpos($_SERVER["REQUEST_URI"], "/api/") === 0) {

		if (empty($_POST)) {
			$_POST = json_decode(file_get_contents("php://input"), true);
		}

		if (empty($_POST)) {

			header("HTTP/1.1 422");
			die;

		}

		$_SESSION["w"] = false;
		$_SESSION["o"] = [];

		route($_SERVER["REQUEST_URI"]);
		die;

	}

	function respond() {

		$r = $_SESSION["r"];
		$_SESSION["r"] = "";

		if (!empty($r)) {
			$_SESSION["track"][] = ["c" => $r, "t" => time()];
		}

		die(json_encode(["r" => $r, "o" => $_SESSION["o"], "w" => $_SESSION["w"]]));

	}

?>