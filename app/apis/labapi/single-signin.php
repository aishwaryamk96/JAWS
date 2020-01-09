<?php
$headers = getallheaders();
if (isset($headers["Authorization"])) {
		if ($headers["Authorization"] != "Bearer 7F21611ED3FB647EA6101A7F5E07AFF1C330396BB53033D1B420F24F18E687E1") {
			header("HTTP/1.1 401");
			die();
    }
  }
  load_module("user");
  register_shutdown_function(function() {
    // var_dump(error_get_last());
  });

    /**
     *to load user module
    */
    $user = user_get_by_email($_POST["email"]);
    if ($user === false) {
      die(json_encode(["token" => false]));
    }else{

      $a=db_query("SELECT e.lab_user, e.lab_pass, e.lab_ip FROM user_enrollment AS e INNER JOIN course AS c ON c.course_id = e.course_id WHERE e.status = 'active' AND e.user_id = ".db_sanitize($user["user_id"])." AND c.sis_id = ".db_sanitize($_POST["course_id"])." GROUP BY e.course_id LIMIT 1");
      $ip = $a[0]["lab_ip"];
      $isDyanmicIp=(int)substr($ip,0,strpos($ip,'.'));
      $isDyanmicIp = $isDyanmicIp!==0;
      // die(json_encode($a));
      include('new.php');
      // echo 0xg;
    }
?>
