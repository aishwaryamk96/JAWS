<?php

	load_module("ui");

	// Init Session
    auth_session_init();
    load_library("email");

	// Prep
	$return_url = "https://www.jigsawacademy.com/secret-santa";
	// Login Check
	if (!auth_session_is_logged()) {

		ui_render_login_front(array(
					"mode" => "create",
					"return_url" => $return_url,
					"text" => "Please login to access this page."
				));
		exit();

	}

	$user = false;

	if (!empty($_SESSION["ss"])) {
		$user = $_SESSION["ss"];
	}
	else {

		$email = db_sanitize($_SESSION["user"]["soc_gp"]);
		$user = db_query("SELECT * FROM secret_santa WHERE email=$email;");

	}

	if (empty($user)) {

		ui_render_msg_front(array(
					"type" => "error",
					"title" => "Jigsaw Academy",
					"header" => "No Tresspassing",
					"text" => "Sorry, but we do not recognize this email ID.<br/>This page is only available for Jigsaw Academy employees, using their official email ID."
				));
		exit();

	}

	$user = $user[0];

	if (isset($_GET["download"]) && $_GET["download"] == "1" && $user["email"] == "melissa@jigsawacademy.com") {

		load_plugin("phpexcel");

		$data[] = [
			"title" => "Secret Santa",
			"data" => db_query(
				"SELECT
					s.name AS Santa,
					s.email AS 'Santa\'s Email',
					IF (s.is_playing = 1, 'Playing', 'Not playing') AS 'Is playing?',
					IF (s.child_ss_id IS NOT NULL, c.name, 'Not assigned yet') AS Child,
					IF (s.child_ss_id IS NOT NULL, c.email, 'Not assigned yet') AS 'Child\'s Email'
				FROM
					secret_santa AS s
				LEFT JOIN
					secret_santa AS c
					ON c.id = s.child_ss_id
				ORDER BY
					s.id ASC;"
			)
		];

		phpexcel_write($data, [], "Secret Santa.xls");
		die;

	}

	$child = null;
	$new_child = null;
	$no_children = false;

	// Check if the user has opted to play or not
	$playing = intval($user["is_playing"]);

	// Check if the child assigned to the user has opted to play or not; if opted out, assign a new child
	if (!empty($user["child_ss_id"])) {

		$child = db_query("SELECT * FROM secret_santa WHERE id=".$user["child_ss_id"]);
		$child = $child[0];
		if (intval($child["is_playing"]) == 0) {

			$new_child = get_new_child($user);
			if ($new_child !== false) {

				save_new_child($user, $new_child["id"]);
				$user["child_ss_id"] = $new_child["id"];

				$floor = $child["floor"];
				if ($floor == 1) {
					$floor = "1st floor";
				}
				else if ($floor == 2) {
					$floor = "2nd floor";
				}
				else if ($floor == 3) {
					$floor = "3rd floor";
				}
				else {
					$floor .= "th floor";
				}

				$email = $user["email"];
				if (!empty($_SESSION["ss"])) {
					$email = "himanshu@jigsawacademy.com";
				}

				send_email('secret-santa', ["to" => $email], ["child" => $child["name"]." ($floor)"]);

			}
			else {

				$no_children = true;
				// Trigger a mail to Melissa that no child is left to be assigned

			}

		}

	}

	// If the form has been submitted,
	if (isset($_POST["optin"])) {

		if (intval($_POST["optin"]) == 1) {

			if (empty($user["child_ss_id"])) {

				$child = get_new_child($user);
				if (!empty($child)) {

					save_new_child($user, $child["id"]);
					$user["child_ss_id"] = $child["id"];
					if (!empty($_SESSION["ss"])) {
						$_SESSION["ss"][0]["child_ss_id"] = $child["id"];
					}

					$floor = $child["floor"];
					if ($floor == 1) {
						$floor = "1st floor";
					}
					else if ($floor == 2) {
						$floor = "2nd floor";
					}
					else if ($floor == 3) {
						$floor = "3rd floor";
					}
					else {
						$floor .= "th floor";
					}

					$email = $user["email"];
					if (!empty($_SESSION["ss"])) {
						$email = "himanshu@jigsawacademy.com";
					}

					// Trigger mail to user to notify the child
                    send_email('secret-santa', array("to" => $email), ["child" => $child["name"]." ($floor)"]);

				}
				else {

					$no_children = true;
					// Trigger a mail to Melissa that no child is left to be assigned

				}

			}

			$playing = 1;

		}
		else {

			db_exec("UPDATE secret_santa SET is_playing = 0 WHERE id = ".$user["id"]);
			$santa = db_query("SELECT * FROM secret_santa WHERE child_ss_id = ".$user["id"]);
			if (!empty($santa)) {

				$santa = $santa[0];
				// Trigger mail to the santa to notify that child has opted out
                send_email('santa-optout', array("to" => $santa["email"]), array());

			}
			$playing = 0;

		}

	}

	function get_new_child($user) {

		$floor = $user["floor"];
		$dept = $user["dept"];
		$is_manager = intval($user["is_manager"]);

		$user_id = $user["id"];

		if (!empty($user["preferred"])) {
			$new_child = db_query("SELECT ss.* FROM secret_santa AS ss INNER JOIN secret_santa AS s1 ON s1.preferred = ss.id WHERE s1.id = $user_id;");
		}
		else {
			$new_child = db_query("SELECT * FROM secret_santa WHERE id NOT IN (SELECT child_ss_id FROM secret_santa WHERE child_ss_id IS NOT NULL) AND id NOT IN (SELECT preferred FROM secret_santa WHERE preferred IS NOT NULL) AND is_playing = 1 AND floor != $floor AND dept != $dept".($is_manager == 1 ? " AND is_manager != 1" : "").";");
		}

		if (empty($new_child)) {
			$new_child = db_query("SELECT * FROM secret_santa WHERE id NOT IN (SELECT child_ss_id FROM secret_santa WHERE child_ss_id IS NOT NULL) AND id NOT IN (SELECT preferred FROM secret_santa WHERE preferred IS NOT NULL) AND is_playing = 1".($is_manager == 1 ? " AND is_manager != 1" : "").";");
		}

		if (empty($new_child)) {
			return false;
		}
		if (count($new_child) == 1) {
			return $new_child[0];
		}
		else {
			return $new_child[random_int(0, count($new_child) - 1)];
		}

		return false;

	}

	function save_new_child($user, $new_child_id) {
		db_exec("UPDATE secret_santa SET is_playing = 1, child_ss_id = ".$new_child_id." WHERE id = ".$user["id"]);
	}

	$img_src = "";
	if ($playing) {

		if (empty($user["child_ss_id"])) {
			$img_src = JAWS_PATH_WEB."/media/ss/first.jpg";
		}
		else {
			$img_src = JAWS_PATH_WEB."/media/ss/yes.jpg";
		}

	}
	else {
		$img_src = JAWS_PATH_WEB."/media/ss/no.jpg";
	}

?>
<html>
<head>
	<title>Secret Santa - Jigsaw Academy</title>
	<style type="text/css">
		body {
			background-image: url(<?php echo $img_src ?>);
			background-repeat: round;
			background-attachment: fixed;
			font-family: Helvetica, sans-serif, Arial;
			margin: 0;
			padding: 0;
		}
		.parent {
			display: flex;
			flex-direction: row-reverse;
			width: 100%;
			height: 100%;
			background: inherit;
		}
		.middle {
			display: flex;
			padding-right: 100px;
			background: inherit;
		}
		.opt-container {
			box-shadow: 0 2px 7px 5px rgba(0,0,0,.14), 0 3px 5px 2px rgba(0,0,0,.2), 0 1px 10px 4px rgba(0,0,0,.12);
			background: inherit;
			padding: 20px 100px;
			color: black;
			margin: auto;
			max-width: 500px;
			overflow: hidden;
			position: relative;
			z-index: 10;
			transition: all .3s ease-out;
			opacity: 0;
			pointer-events: none;
		}
		.opt-container.show {
			opacity: 1;
			pointer-events: all;
		}
		.opt-container:before {
			content: "";
			background: inherit;
			filter: blur(40px);
			position: absolute;
			left: -80px;
			top: -80px;
			right: -80px;
			bottom: -80px;
			box-shadow: inset 0 0 0 200px rgba(255,255,255,0.3);
			z-index: -1;
		}
		.opt-container h2{
            text-align: center;
        }
		.opt-container form {
			margin: 0px;
			display: flex;
			flex-direction: column;
		}
		.form-elems {
			padding: 10px 0px;
		}
		.form-elems label {
			padding-left: 20px;
		}
		.form-elems button {
			margin: auto;
		}
		.options {
			margin-right: 10px;
			vertical-align: top;
		}
		#retry {
			font-weight: bold;
			text-decoration: underline;
			cursor: pointer;
		}
		#form-container {
			display: none;
		}
		#retry:active {

		}
	</style>
	<script>
		function renderComplete() {
			document.getElementById("optContainer").className += " show";
		}
	</script>
</head>
<body onload="renderComplete()">
	<div class="parent">
		<div class="middle">
			<div class="opt-container" id="optContainer">
				<?php if ($playing) {
					if ($no_children === true) { ?>
						<h3>Sorry, we seem to have run out of cute children for you to gift... Please contact <a href="mailto:melissa@jigsawacademy.com">Melissa</a>, head of our order of Santas for clarification.</h3>
					<?php }
					else {
						if (empty($user["child_ss_id"])) { ?>
							<h1>Welcome, <?php echo $user["name"] ?>!</h1>
							<form method="post">
								<label style="font-weight: bold; font-size: 120%">Thanks for choosing to be someone's Secret Santa!</label>
								<label>Please click below to receive your secret child.</label>
								<div class="form-elems" style="display: flex; margin-top: 10px">
									<input type="hidden" name="optin" value="1">
									<button type="submit">Let's Go</button>
								</div>
							</form>
						<?php }
						else { ?>
							<?php if (!empty($new_child)) { ?>
								<h2>Your child opted out of the Secret Santa event...</h2>
								<p>Your new secret child has been mailed to your email ID.</p>
							<?php }
							else { ?>
								<h2>Thanks for choosing to be someone's Secret Santa!</h2>
								<p>You will get to gift a Christmas present anonymously to a person picked for you from Jigsaw Academy.</p><p>The person chosen for you has been shared via an email.</p>The identity of the gift giver (that's you) is a secret not to be revealed to anybody.</p><p>Have fun, surprise and cherish one another and spread joy in your gift giving and celebration of the season.</p>
								<p><b>(The mail can be best viewed in the browser.)</b></p>
							<?php } ?>
						<?php }
					}
				}
				else { ?>
					<h2>Looks like you prefer Halloween...</h2>
					<label>Unfortunately, this time the head of our order has decided not to allow traitors to turn clean.</label>
					<!-- <form method="post">
						<label style="font-weight: bold;">Thinking of changing your mind? <span id="retry" onclick="document.getElementById('form-container').style.display='block'; document.getElementById('retry').style.display='none';">Click here</span></label>
						<div id="form-container">
							<div class="form-elems">
								<label><input class="options" type="radio" name="optin" value="1" required>Yes, I am in</label>
							</div>
							<div class="form-elems">
								<label><input class="options" type="radio" name="optin" value="0">No, I am out</label>
							</div>
							<div class="form-elems" style="display: flex">
								<button type="submit">Let's Go</button>
							</div>
						</div>
					</form> -->
				<?php } ?>
			</div>
		</div>
	</div>
	<?php if ($user["email"] == "melissa@jigsawacademy.com") { ?>
		<h6 style="color: white">
			Hi Melissa, the head of our order of Santas! <a href="?download=1">Click here</a> to download the list.
		</h6>
	<?php } ?>
</body>
</html>