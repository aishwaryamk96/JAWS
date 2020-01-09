<?php

	$headers = getallheaders();
	if (empty($headers["Authorization"]) || $headers["Authorization"] != "Bearer Kq4C9bLMpvvCuXsVJ8NcYRqnQuvLbvNepZqSjb9QgmgSf2m6vjnbG9Z92PzCvmEg") {

		header("HTTP/1.1 401 Unauthorized");
		die();

	}

	if (empty($_POST["from"]) || empty($_POST["to"]) || empty($_POST["subject"]) || empty($_POST["body"])) {

		header("HTTP/1.1 401 Unauthorized");
		die();

	}

	load_library("email");

	$header = [
		"to" => $_POST["to"],
		"subject" => base64_decode($_POST["subject"]),
		"from" => $_POST["from"]
	];

	if (!empty($_POST["cc"])) {
		$header["cc"] = $_POST["cc"];
	}
	if (!empty($_POST["bcc"])) {
		$header["bcc"] = $_POST["bcc"];
	}

	send_email("rubric.empty.template", $header, ["body" => base64_decode($_POST["body"])]);

	die(json_encode(["dhinchyak" => true]));

?>