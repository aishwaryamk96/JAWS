<?php
  // echo 'working';
  header("Access-Control-Allow-Origin: *");
  $headers = getallheaders();
  $expectedToken="Bearer expected-really-long-token";
  $authHeader=$headers["Authorization"];
  if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS'){
    header('Access-Control-Allow-Origin: *');
    die();
  }
  // echo json_encode($headers);
  if (isset($authHeader)||(!$authHeader)) {
		if ($headers["Authorization"] != $expectedToken) {
			header("HTTP/1.1 401");
			die('buzz off ');
    }
  }
  die(json_encode($_POST));
?>
