<?php

	$headers = getallheaders();
	if (empty($headers["Authorization"])) {

		header("HTTP/1.1 403");
		die;

	}

	$auth = strtolower(trim(substr($headers["Authorization"], strlen("bearer"))));
	if ($auth != "rcavqs6310vmh6voveamcp74z8ihkc5pl6uu9284e2jdgijsvvt9k8n95vo3te6i") {

		header("HTTP/1.1 403");
		die;

	}

	if (!isset($_POST["email"]) || !isset($_POST["sis_id"])) {

		header("HTTP/1.1 422");
		die;

	}

	$email = trim($_POST["email"]);
	$sis_id = trim($_POST["sis_id"]);

	if (empty($email) || empty($sis_id)) {

		header("HTTP/1.1 422");
		die;

	}

	$course = db_query("SELECT * FROM course WHERE sis_id = ".db_sanitize($sis_id).";");
	if (empty($course)) {

		header("HTTP/1.1 404");
		die;

	}
	$course = $course[0];

	$user["email"] = $email;
	$user["name"] = trim($_POST["name"] ?? "");
	$context = [
		"id" => $course["course_id"],
		"type" => "course"
	];

	$payment["paid"] = false;
	$payment["currency"] = "inr";
	$payment["amount"] = $course["sp_price_inr"];

	load_plugin("future_skills");

	$_POST["data"]["user"]["email"] = $email;
	$log_id = log_action($_POST);
	if (($subs = enroll_user($log_id, $user, $context, $payment, true)) === false) {

		header("HTTP/1.1 401");
		die;

	}

	log_action($log_id, "success");

	$pay = $subs["web_id"];
	$token = $subs["token"];

	header("Content-type: application/json");
	die(json_encode(["ru" => "https://www.jigsawacademy.com/jaws/pay?pay=$pay&token=$token"]));

?>