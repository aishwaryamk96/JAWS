<?php

	authorize_api_call("", true);

	if (empty($_POST)) {
		$_POST = json_decode(file_get_contents("php://input"), true);
	}

	if (empty($_POST["course"]) || empty($_POST["course"]["course_id"])) {

		header("HTTP/1.1 422");
		die;

	}

	die(json_encode(["course" => course_edit($_POST["course"])]));

?>