<?php

	load_module("user");

	$lines = file("external/temp/mlai.csv");
	foreach ($lines as $line) {

		$l = explode(",", $line);

		$user = user_get_by_email($l[0]);
		if (empty($user)) {
			continue;
		}
		echo $l[0]."<br>";

		$user_id = $user["user_id"];

		die("SELECT e.enr_id, c.name, s.start_date, e.learn_mode FROM user_enrollment AS e INNER JOIN course AS c ON c.course_id = e.course_id INNER JOIN course_section AS s ON s.id = e.section_id WHERE e.enr_id = (SELECT MIN(enr_id) FROM user_enrollment WHERE user_id = $user_id AND status = 'active' GROUP BY course_id HAVING COUNT(course_id) > 1) e.user_id = $user_id;");

		$res = db_query("SELECT e.enr_id, c.name, s.start_date, e.learn_mode FROM user_enrollment AS e INNER JOIN course AS c ON c.course_id = e.course_id INNER JOIN course_section AS s ON s.id = e.section_id WHERE e.enr_id = (SELECT MIN(enr_id) FROM user_enrollment WHERE user_id = $user_id AND status = 'active' GROUP BY course_id HAVING COUNT(course_id) > 1) e.user_id = $user_id;");
		foreach ($res as $enr) {
			echo $enr["enr_id"]." ".$enr["name"]." ".$enr["start_date"]." ".$enr["learn_mode"]."<br>";
		}

	}

?>