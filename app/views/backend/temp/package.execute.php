<?php

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header('Location: http://www.jigsawacademy.com');
		die();
	}

	load_module("ui");

	// Init Session
	auth_session_init();

	// Prep
	$login_params["return_url"] = JAWS_PATH_WEB."/view/backend/temp/package.execute";

	// Login Check
	if (!auth_session_is_logged()) {

		ui_render_login_front(array(
			"mode" => "login",
			"return_url" => $login_params["return_url"],
			"text" => "Please login to access this page."
		));
		exit();

	}

	if ($_SESSION["user"]["user_id"] != 18 && $_SESSION["user"]["user_id"] != 16767 && $_SESSION["user"]["user_id"] != 13683 && $_SESSION["user"]["user_id"] != 7822) {
		die("You do not have required priviledges to use this feature, lol.");
	}

	$msg = "";

	if (!empty($_POST["package_id"])) {

		$package = db_query("SELECT * FROM package WHERE package_id=".$_POST["package_id"]);
		if (!isset($package[0])) {
			$msg = "Package not found";
		}
		else {

			$package = $package[0];
			load_module("subs");
			if ($package["status"] == "pending" && $package["status_approval_sm"] == "approved" && $package["status_approval_pm"] == "approved") {

				package_exec($package["package_id"], false);
				$msg = "Done!";

			}
			else {
				$msg = "Package is either not ready for execution or already executed.";
			}

		}

	}

?>
<html>
<head>
</head>
<body>
	<center>
		<form method="post">
			<input type="text" name="package_id"><br>
			<input type="submit" value="Execute">
		</form>
		<?php echo $msg ?>
	</center>
</body>
</html>