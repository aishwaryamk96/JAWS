<?php

	auth_session_init();
	load_module("ui");

	$message = "";

	// Prep
	$return_url = "https://www.jigsawacademy.com/secret-santa";
	// Login Check
	if (!auth_session_is_logged()) {

		ui_render_login_front(array(
					"mode" => "login",
					"return_url" => $return_url,
					"text" => "Please login to access this page."
				));
		exit();

	}

	if ($_SESSION["user"]["user_id"] == 13683) {

		if (!empty($_POST["user"])) {

			$_SESSION["ss"] = db_query("SELECT * FROM secret_santa WHERE id=".$_POST["user"].";");
			if (empty($_SESSION["ss"])) {

				$message = "Invalid User";
				unset($_SESSION["ss"]);

			}
			else {
				$message = "User ".$_SESSION["ss"][0]["name"]." defined.";
			}

		}

	}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Secret Santa Testing</title>
</head>
<body>
	<?= $message; ?>
	<br>
	<form method="POST">
		<input type="text" name="user">
		<input type="submit">
	</form>
</body>
</html>