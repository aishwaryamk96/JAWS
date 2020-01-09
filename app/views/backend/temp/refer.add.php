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
	$login_params["return_url"] = JAWS_PATH_WEB."/view/backend/temp/refer.add";

	// Login Check
	if (!auth_session_is_logged()) {

		ui_render_login_front(array(
			"mode" => "login",
			"return_url" => $login_params["return_url"],
			"text" => "Please login to access this page."
		));
		exit();

	}

	if ($_SESSION["user"]["user_id"] != 18 && $_SESSION["user"]["user_id"] != 16767 && $_SESSION["user"]["user_id"] != 13683 && $_SESSION["user"]["user_id"] != 4108) {
		die("You do not have required priviledges to use this feature, lol.");
	}

	$err = "";
	$msg = "";

	if (!empty($_POST["referrer"] && !empty($_POST["referred"]))) {

		load_module("user");

		$referrer = user_get_by_email($_POST["referrer"]);
		if ($referrer === false) {

			$referrer = db_query("SELECT * FROM system_activity WHERE activity=".db_sanitize($_POST["referrer"]));
			if ($referrer === false) {

				db_exec("INSERT INTO system_activity (act_type, activity, content) VALUES ('jlc.user.not_found', ".db_sanitize($_POST["referrer"]).", ".db_sanitize(["email" => $_POST["referrer"], "name" => $_POST["referrer_name"]]).");");
				$referrer = ["user_id" => db_get_last_insert_id()];

			}
			else {
				$referrer["user_id"] = $referrer["act_id"];
			}

			$referrer["user_src"] = "system_activity";

		}
		else {
			$referrer["user_src"] = "user";
		}

		$referred = user_get_by_email($_POST["referred"]);
		if ($referred === false) {
			$err = "Referred user is not available with us...";
		}
		else {

			$subs = db_query("SELECT * FROM subs WHERE user_id = ".$referred["user_id"].";");
			if (!isset($subs[0])) {
				$err = "Referred user does not have any enrollments yet. Please use the referrer's 'Refer a Friend' page to refer the referred";
			}
			else {

				load_module("refer");
				refer_create($referrer["user_src"], $referrer["user_id"], $referred["email"], $referred["name"], $referred["phone"], "XYZ123A", "4;5");
				$msg = "Done!";

			}

		}

	}

?>
<html>
<head>
	<title>Referral info add karein!</title>
</head>
<body>
	<center>
		<?php if (!empty($err)) { ?>
			<h3 style="color: red"><?php echo $err; ?></h3>
		<?php } ?>
		<?php if (!empty($msg)) { ?>
			<h3 style="color: green"><?php echo $msg; ?></h3>
		<?php } ?>
		<form method="post">
			Referrer Email ID: <input type="text" name="referrer" value="<?php echo (!empty($_POST["referrer"]) ? $_POST["referrer"] : "") ?>"><br>
			Referrer Name: <input type="text" name="referrer_name" value="<?php echo (!empty($_POST["referrer_name"]) ? $_POST["referrer_name"] : "") ?>"><br><br>
			Referral Email ID: <input type="text" name="referred"><br>
			<input type="submit" value="Go!">
		</form>
	</center>
</body>
</html>