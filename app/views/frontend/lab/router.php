<?php

	auth_session_init();

	$GLOBALS["templates"] = [
		"404" => __DIR__."/404.php",
		"login" => __DIR__."/login.php",
		"start" => __DIR__."/start.php",
	];

	$GLOBALS["css"] = JAWS_PATH_WEB."/common/css/lab/";
	$GLOBALS["js"] = JAWS_PATH_WEB."/common/lab/js/";

	$components = explode("/", trim($_SERVER["REQUEST_URI"], "/"));

	load_plugin("aws_lab");

	if (!empty($_SESSION["lab"]["route"]) && $_SESSION["lab"]["route"] == $components[1]) {

		log_in();
		die;

	}

	$_SESSION = [];

	$res_lab = db_query("SELECT * FROM aws_labs WHERE status = 1 AND route = ".db_sanitize($components[1]).";");
	if (empty($res_lab)) {

		require_once $GLOBALS["templates"]["404"];
		die;

	}

	$res_lab = $res_lab[0];
	$GLOBALS["title"] = $res_lab["name"];

	if (empty($_GET["token"])) {

		if (!empty($_POST["username"]) && !empty($_POST["password"])) {

			$user = db_query(
				"SELECT DISTINCT
					user.user_id AS id,
					user.name
				FROM
					user
				INNER JOIN
					subs
					ON subs.user_id = user.user_id
				LEFT JOIN
					(
						SELECT
							subs_id,
							MAX(end_date) AS end_date
						FROM
							access_duration
						GROUP BY
							subs_id
					) AS ad
					ON ad.subs_id = subs.subs_id
				INNER JOIN
					user_enrollment AS enr
					ON enr.subs_id = subs.subs_id
				INNER JOIN
					course_lab AS lab
					ON lab.lab_ip = enr.lab_ip
				INNER JOIN
					aws_labs
					ON aws_labs.domain = lab.domain
				WHERE
					aws_labs.status = 1
					AND
					enr.lab_ip IS NOT NULL
					AND
					(
						ad.end_date IS NOT NULL
						AND
						DATE(ad.end_date) > CURRENT_DATE
						OR
						DATE(subs.end_date) > CURRENT_DATE
					)
					AND
					subs.status = 'active'
					AND
					enr.status='active'
					AND
					enr.lab_user = ".db_sanitize($_POST["username"])."
					AND
					enr.lab_pass = ".db_sanitize($_POST["password"])."
					AND
					aws_labs.route = ".db_sanitize($res_lab["route"]).";"
			);

			if (!empty($user)) {

				log_in($user[0]["name"], $res_lab, "user", $user[0]["id"]);
				die;

			}

			$user = db_query(
				"SELECT DISTINCT
					lcd.id,
					lcd.name,
				FROM
					lab_credentials_dummy AS lcd
				INNER JOIN
					aws_lab AS lab
					ON lab.domain = lcd.domain
				WHERE
					DATE(lcd.start_date) < CURRENT_DATE
					AND
					DATE(lcd.end_date) > CURRENT_DATE
					AND
					lcd.status = 'active'
					AND
					lab.status = 1
					AND
					lcd.username = ".db_sanitize($_POST["username"])."
					AND
					lcd.password = ".db_sanitize($_POST["password"])."
					AND
					lab.route = ".db_sanitize($res_lab["route"]).";"
			);

			if (!empty($user)) {

				log_in($user[0]["name"], $res_lab, "lcd", $user[0]["id"]);
				die;

			}

			$error_msg = "Invalid username/password";

		}

		require_once $GLOBALS["templates"]["login"];
		die;

	}
	else {

		$user = db_query(
			"SELECT DISTINCT
				user.user_id AS user_id,
				user.name AS user_name,
				lcd.id AS lcd_id,
				lcd.name AS lcd_name
			FROM
				system_psk AS psk
			LEFT JOIN
				user
				ON user.user_id = psk.entity_id AND psk.entity_type = 'user'
			LEFT JOIN
				lab_credentials_dummy AS lcd
				ON lcd.id = psk.entity_id AND psk.entity_type = 'lcd'
			WHERE
				psk.entity_type = 'user' AND
				psk.action = 'lab.login' AND
				psk.token = ".db_sanitize($_GET["token"]).";"
		);

		if (empty($user)) {

			require_once $GLOBALS["templates"]["404"];
			die;

		}

		$user = $user[0];

		$context_type = empty($user["user_name"]) ? "lcd" : "user";
		$context_id = empty($user["user_name"]) ? $user["lcd_id"] : $user["user_id"];
		log_in(empty($user["user_name"]) ? $user["lcd_name"] : $user["user_name"], $res_lab, $context_type, $context_id);
		die;

	}

	function log_in($user_name = null, $lab = null, $context_type = null, $context_id = null) {

		if ((is_null($user_name) && empty($_SESSION["user_name"])) || !empty($_POST["logout"])) {

			$_SESSION = [];
			require_once $GLOBALS["templates"]["login"];
			die;

		}

		if (!is_null($user_name)) {

			$_SESSION["user_name"] = $user_name;
			$_SESSION["lab"] = $lab;
			$_SESSION["username"] = $_POST["username"];
			$_SESSION["password"] = $_POST["password"];
			$_SESSION["context_type"] = $context_type;
			$_SESSION["context_id"] = $context_id;

		}
		else if (isset($_POST["launch"])) {
			download_file();
		}
		else if (!empty($_SESSION["instance"])) {
			return launch_lab();
		}

		start_page();

	}

	function start_page() {

		if (isset($_POST["start"])) {
			initialize_instance();
		}
		else {

			if (($instance = AwsLab::instanceExists($_SESSION["context_type"], $_SESSION["context_id"])) === false) {

				$_SESSION["instance"] = $instance;
				$GLOBALS["launch"] = true;
				require_once $GLOBALS["templates"]["start"];
				return;

			}

			require_once $GLOBALS["templates"]["start"];

		}

	}

	function initialize_instance() {

		$awsLab = new AwsLab();
		$_SESSION["instance"] = $awsLab->createInstance($_SESSION["lab"], $_SESSION["username"], $_POST["password"], $_SESSION["context_type"], $_SESSION["context_id"]);

		echo json_encode($_SESSION["instance"]);

	}

	function launch_lab() {

		$GLOBALS["launch"] = true;
		require_once $GLOBALS["templates"]["start"];

	}

	function download_file() {

		$awsLab = new AwsLab();
		if (empty($_SESSION["instance"])) {
			die("Something went wrong...");
		}
		$instance = $awsLab->getInstanceInfo($_SESSION["instance"]["instance_id"]);

		$content = "mstsc /v:".$instance[1];
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.$_SESSION["username"].'.bat"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . strlen($content));
		echo $content;
		die;

	}

	function test123() {

		$awsLab = new AwsLab();
		if (empty($_SESSION["instance"])) {
			die("Something went wrong...");
		}
		$instance = $awsLab->getInstanceInfo($_SESSION["instance"]["instance_id"]);

		var_dump($_SESSION);
		var_dump($instance);

	}

?>