<?php

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	// Init Session
	auth_session_init();

	// Auth Check - Expecting Session Only !
	if ((!auth_session_is_logged()) || (!auth_session_is_allowed("batcave") && (!auth_session_is_allowed("lab.view") || !auth_session_is_allowed("lab.edit")))) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	$course_labs = db_query("SELECT id, name, code, lifespan, status, lab_id, DATE_FORMAT(created_at, '%e %M %Y, %l:%i %p') AS created_at, DATE_FORMAT(updated_at, '%e %M %Y, %l:%i %p') AS updated_at FROM course_labs ORDER BY id DESC;");

	$labs = [];
	$res_labs = db_query("SELECT l.id, l.ami_id, l.meta, l.type, DATE_FORMAT(l.created_at, '%e %M %Y, %l:%i %p') AS created_at, DATE_FORMAT(l.updated_at, '%e %M %Y, %l:%i %p') AS updated_at, l.created_by, u.name FROM labs AS l INNER JOIN user AS u ON u.user_id = l.created_by ORDER BY l.id ASC;");
	foreach ($res_labs as $lab) {

		$lab["meta"] = json_decode($lab["meta"], true);
		$labs[$lab["id"]] = $lab;

	}

	die(json_encode(["courseLabs" => $course_labs, "labs" => $labs, "edit" => auth_session_is_allowed("lab.edit"), "defaults" => json_decode(setting_get("aws.ami.defaults"))]));

?>