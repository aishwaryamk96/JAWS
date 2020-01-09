<?php

// Init Session
auth_session_init();

load_module("ui");

// Login Check
if (!auth_session_is_logged()) {
	ui_render_login_front(array(
			"mode" => "login",
			"return_url" => $return_url,
			"text" => "Please login to access this page."
	));
	exit();
}

if (!auth_session_is_allowed("enrollment.get.adv")) {
	ui_render_msg_front(array(
			"type" => "error",
			"title" => "Jigsaw Academy",
			"header" => "No Tresspassing",
			"text" => "Sorry, but you do not have permission to access this page.<br/>Contact Jigsaw Support team for assistance."
	));
	exit();
}

if (!empty($_POST["date"])) {

	$res_sis_ids = db_query("SELECT DISTINCT enr.sis_id FROM user_enrollment AS enr INNER JOIN subs ON subs.subs_id=enr.subs_id WHERE DATE(subs.start_date) = ".db_sanitize($_POST["date"]));

	if ($res_sis_ids === false) {
		exit();
	}

	//$GLOBALS["jaws_exec_live"] = false;
	load_plugin("jlc");
	$jlc = new JLC;

	load_library("email");

	$post_data = [];
	foreach ($res_sis_ids as $sis_id) {
		$post_data[] = $sis_id["sis_id"];
	}

	$response = false;
	$retry = 0;

	while ($response === false) {

		$response = $jlc->apiNew("users/permissions_add", ["data" => http_build_query(["sis_id" => implode(";", $post_data)]), "content_type" => "application/x-www-form-urlencoded"]);

		if ($response === false) {
			$retry++;
		}
		else {

			$response = json_decode($response, true);
			send_email("jlc.permissions.notify", [], ["success" => true, "sis_ids" => $post_data, "skipped" => $response["skipped"]]);
			break;

		}

		if ($retry >= 2) {

			send_email("jlc.permissions.notify", [], ["success" => false, "sis_ids" => $post_data]);
			break;

		}

	}

}

?>
<html>
<head>
	<title>JLC Permissions Tester</title>
</head>
<body>
	<div>
		<center>
			JLC Permissions Tester (You are logged in as: <?php echo $_SESSION["user"]["name"]; ?> <a href="<?php echo JAWS_PATH_WEB."/logout"; ?>">Logout</a>)
			<?php if (isset($msg)) echo "<br/>".$msg; ?>
		</center>
	</div><br />
	<center>
		<form enctype="multipart/form-data" method="post">
			<input type="date" name="date" /><br />
			<input type="submit" name="submit" value="Test" />
		</form>
	</center>
</body>
</html>