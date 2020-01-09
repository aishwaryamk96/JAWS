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
		header('Location: ../index.php');
		die();
	}

	load_module("ui");

	// Init Session
	auth_session_init();

	// Login Check
	if (!auth_session_is_logged()) {
		ui_render_login_front(array(
			"mode" => "login",
			"return_url" => "https://www.jigsawacademy.com/jaws/view/backend/temp/welcome.email.send",
			"text" => "Please login to access this page."
		));
		exit();
	}

	if ($_SESSION["user"]["user_id"] != 18 && $_SESSION["user"]["user_id"] != 16767 && $_SESSION["user"]["user_id"] != 13683 && $_SESSION["user"]["user_id"] != 4108) {
		die("You do not have required priviledges to use this feature, lol.");
	}

	if (!empty($_GET["sis_file"])) {

		load_module("user_enrollment");

		$res_validate = db_query("SELECT * FROM user_enrollment WHERE sis_file=".db_sanitize($_GET["sis_file"]));

		if (!isset($res_validate[0])) {
			$result = "SIS File is invalid.";
		}
		else {
			welcome_email_send($_GET["sis_file"]);
			db_exec("UPDATE user_enrollment SET sis_status='ul', shall_notify=0 WHERE sis_file=".db_sanitize($_GET["sis_file"]));
			$result = "Done";
		}

	}

?>
<html>
<head>
	<title>Enrollment creation page for Himanshu Malpande only</title>
</head>
<body>
	<center>
		<form>
			Sis File: <input name="sis_file"><br>
			<input type="submit" value="Try">
		</form>
	</center>
	<br><br>
	<?php if (isset($result)) { ?>
	<center>
		<?php echo $result; ?>
	</center>
	<?php } ?>
</body>
</html>