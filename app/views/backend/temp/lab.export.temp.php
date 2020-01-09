<?php

$res = db_query("SELECT
					enr.user_id,
					course.name,
					enr.lab_ip,
					enr.lab_user,
					enr.lab_pass,
					subs.start_date,
					subs.end_date
				FROM
					user_enrollment AS enr
				INNER JOIN
					subs ON subs.subs_id = enr.subs_id
				INNER JOIN
					course ON course.course_id = enr.course_id
				WHERE
					enr.lab_ip IS NOT NULL
					AND
					enr.lab_ip != ''
					AND
					enr.lab_ip != 'dataserver1.jigsawacademy.in'
				ORDER BY
					subs.start_date ASC;
		");

$users = [];
foreach ($res as $result) {

	if (!in_array($result["lab_user"], $users[$result["user_id"]]["lab_user"])) {
		$users[$result["user_id"]]["lab_user"][] = $result["lab_user"];
	}
	if (!in_array($result["lab_pass"], $users[$result["user_id"]]["lab_pass"])) {
		$users[$result["user_id"]]["lab_pass"][] = $result["lab_pass"];
	}
	$users[$result["user_id"]]["lab"][] = ["course" => $result["name"], "lab_ip" => $result["lab_ip"]];

}

$header = "Course,Lab IP,Usernames,Passwords,Start Date,End Date\r\n";
$lines = [];
foreach ($users as $user_id => $data) {

	$foreach ($data["lab"] as $lab) {
		# code...
	}

}

?>