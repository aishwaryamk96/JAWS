<?php

	function batcave_init() {

		if ($_SERVER["HTTP_HOST"] == "refer.jigsawacademy.com") {

			define("REFER", "https://refer.jigsawacademy.com");
			define("API_ROOT", "/refapi");

		}
		else {

			//define("BATCAVE", "http://batcave.jigsawacademydev.com");
                        define("BATCAVE", BATCAVE_URL);
			define("API_ROOT", BATCAVE_API);

		}

		session_set_cookie_params(0, "/", ".jigsawacademy.com", true, true);
		auth_session_init();

		load_module("batcave");
		batcave_load_components();

	}

	function batcave_is_api_call() {

		if (strpos($_SERVER["REQUEST_URI"], API_ROOT) === 0) {

			// Send Headers
			header('Access-Control-Allow-Credentials: true');
			header("Content-Type: application/json");
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header("Pragma-directive: no-cache");
			header("Cache-directive: no-cache");
			header("Cache-Control: private, must-revalidate, max-age=0, no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
			header("Pragma: no-cache");

			if ($_SERVER["REQUEST_METHOD"] != "GET") {

				if (empty($_POST)) {
					$_POST = json_decode(file_get_contents("php://input"), true);
				}

			}

			route($_SERVER["REQUEST_URI"]);

			return true;

		}

		return false;

	}

	function authenticate_api_call($die = false) {

		if (!auth_session_is_logged()){

			header("HTTP/1.1 401 Unauthorized");

			if ($die) {
				die();
			}

			return false;

		}

		return true;

	}

	function authorize_api_call($permission = "batcave", $die = false, $header = true) {

		if (is_array($permission)) {

			$status = false;
			foreach ($permission as $perm) {
				$status = $status || authorize_api_call($perm, false, false);
			}

			if (!$status) {

				if ($header) {
					header("HTTP/1.1 403");
				}
				if ($die) {
					die(json_encode(["status" => false, "error" => "Are you trying to hack...? Coz, its not gonna work."]));
				}

			}

			return $status;

		}

		$permission = $permission ?: "batcave";

		if (authenticate_api_call($die)) {

			if (!auth_session_is_allowed($permission)) {

				if ($header) {
					header("HTTP/1.1 403");
				}

				if ($die) {
					die(json_encode(["status" => false, "error" => "Are you trying to hack...? Coz, its not gonna work."]));
				}

				return false;

			}

		}

		return true;

	}

	function batcave_serve() {

		if (batcave_is_api_call()) {
			return;
		}

		if (defined("BATCAVE")) {
			$assets = BATCAVE."/b";
		}
		else {
			$assets = REFER."/b";
		}
		$js = $assets."/js";
		$css = $assets."/css";
		$views = $assets."/views";
		if ($_SERVER["HTTP_HOST"] == ACCOUNTS_HOST) {

			$ru = $_GET["ru"] ?? WEBSITE_URL;
			if (isset($_SESSION["user"])) {
				header("Location: ".$ru);
			}
			else {
				require_once "b/views/accounts.php";
			}

		}
		else if ($_SERVER["HTTP_HOST"] == "refer.jigsawacademy.com") {
			require_once "b/views/refer.php";
		}
		else {
			if (!isset($_SESSION["user"])) {
//				header("Location: ".($_GET["ru"] ?? "http://accounts.jigsawacademydev.com/?ru=".urlencode("http://batcave.jigsawacademydev.com".$_SESSION["REQUEST_URI"])));
                           header("Location: ".($_GET["ru"] ?? ACCOUNTS_URL."/?ru=".urlencode(BATCAVE_URL.$_SESSION["REQUEST_URI"])));
			}
			else {
				require_once "b/views/batcave.php";
			}

		}

	}

?>
