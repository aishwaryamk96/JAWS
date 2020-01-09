<?php

/* 
           8 8888       .8. `8.`888b                 ,8' d888888o.   
           8 8888      .888. `8.`888b               ,8'.`8888:' `88. 
           8 8888     :88888. `8.`888b             ,8' 8.`8888.   Y8 
           8 8888    . `88888. `8.`888b     .b    ,8'  `8.`8888.     
           8 8888   .8. `88888. `8.`888b    88b  ,8'    `8.`8888.    
           8 8888  .8`8. `88888. `8.`888b .`888b,8'      `8.`8888.   
88.        8 8888 .8' `8. `88888. `8.`888b8.`8888'        `8.`8888.  
`88.       8 888'.8'   `8. `88888. `8.`888`8.`88'     8b   `8.`8888. 
  `88o.    8 88'.888888888. `88888. `8.`8' `8,`'      `8b.  ;8.`8888 
    `Y888888 ' .8'       `8. `88888. `8.`   `8'        `Y8888P ,88P' 

    JIGSAW ACADEMY WORKFLOW SYSTEM v1
    ---------------------------------
*/
    
	// Prevent exclusive access
	if (!defined("JAWS")) {
		header('Location: http://www.jigsawacademy.com');
		die();
	}

	$csv = "Name,Email,Phone,Sum,Date,Courses<br />";

	$res_pay = db_query("SELECT user_id, subs_id, sum_total, create_date FROM payment ORDER BY create_date DESC");
	foreach ($res_pay as $pay)
	{
		$res_user = db_query("SELECT name, email, phone FROM user WHERE user_id=".$pay["user_id"]);
		$res_user = $res_user[0];
		$res_subs = db_query("SELECT combo, combo_free FROM subs WHERE subs_id=".$pay["subs_id"]);
		$res_subs = $res_subs[0];
		$line = $res_user["name"].",".$res_user["email"].",".$res_user["phone"].",".$pay["sum_total"].",".$pay["create_date"].",";
		$courses = $res_subs["combo"].";".$res_subs["combo_free"];
		$courses = trim($courses, ";");
		$courses = explode(";", $courses);
		foreach ($courses as $course)
		{
			$detail = explode(",", $course);
			$name = db_query("SELECT name FROM course WHERE course_id=".$detail[0])[0]["name"];
			$line .= $name." + ".($detail == "2" ? "Regular" : "Premium").",";
		}
		$line = substr($line, 0, -1);
		$line .= "<br />";

		$csv .= $line;
	}

	echo $csv;

?>
