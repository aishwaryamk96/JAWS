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
		header('Location: https://www.jigsawacademy.com');
		die();
	}

    load_module("ui");

	// Init Session
    auth_session_init();

    if (!auth_session_is_logged()) {
        ui_render_login_front(array(
            "mode" => "create",
            "return_url" => JAWS_PATH_WEB . '/social',
            "text" => "Please login to continue."
        ));
        exit;
    }

    $saved = false;
    $user = db_query("SELECT c.* FROM corp_users AS c INNER JOIN user AS u ON u.email = c.email WHERE u.user_id = " . $_SESSION["user"]["user_id"] . ";");
    // if (!empty($user)) {

    //     auth_session_logout();
    //     die;

    // }

	$msg = "";
	$status = false;
	if ($user[0]["status"] == 1) {

		$msg = "Your email is already updated!";
		$status = true;

	}
    if (!empty($_POST['email'])) {

		$data = db_query("SELECT * FROM corp_users WHERE email = " . db_sanitize(trim($_POST['email'])) . ";");

        if (empty($data)) {
            $msg = "Email not found. Please contact support.";
        }
        else if (!empty($data) && $data[0]['status'] == 1) {

            $msg = "Email already updated.";
            $status = true;

        }
        else {

            db_query("UPDATE corp_users SET status = 1 WHERE email = ".db_sanitize($_POST["email"]).";");
			db_exec("UPDATE user SET email = ".db_sanitize($_POST["email"])." WHERE user_id = ".$_SESSION["user"]["user_id"].";");
			$msg = "Updated.";
			$status = true;

        }

    }

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="Jigsaw Academy - The Online School of Analytics">
		<title>Jigsaw Academy - Link Social Account for Corporate</title>
		<link rel="icon" type="image/png" href="https://www.jigsawacademy.com/jaws/media/jaws/frontend/images/favicon.png">
		<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
		<link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
		<link href='https://fonts.googleapis.com/css?family=Lato:400,300' rel='stylesheet' type='text/css'>
		<style> body { margin:0; padding:0; font-family: 'Lato', sans-serif; } .overlay { width: 100vw; height: 100vh; position: absolute; z-index: -1; filter: blur(3px); background-color:rgba(200,205,205,0.9); background-image: url('https://www.jigsawacademy.com/jaws/media/jaws/frontend/images/bkg.png'); }
		.container { z-index: 2; height: 100vh; width: 100vw; position: relative; }
		.content { width: auto; max-width: 300px; text-align: center; position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); background-color: white; padding: 25px; box-shadow: 0px 0px 30px rgba(245,245,245,0.5); }
		.title{ font-size: 18px; font-family: 'Lato', sans-serif; margin-bottom: 10px; }
		.razorpay-payment-button { padding: 10px;  font-size: 18px;  }
		.pg-each img { width: 150px; cursor: pointer; } .pg-each{ padding: 10px; } .pg-each.ebs img{ width: 125px; height: 125px; padding: 0; }
		</style>
	</head>
<body>
	<div class="overlay"></div>
	<div class="container">
		<div class="content">
			<div class="title">Hi <?= ucwords(strtolower($_SESSION["user"]["name"])); ?>,</div>
			<?php if (!$status) { ?>
				<form method="post" id="pg-select">
					<div class="pg-each hdfc">
						Please provide your Corporate Email:
					</div>
					<div class="pg-each ebs">
						<input type="text" name="email" placeholder="Email" >
					</div>
					<button type="button" id="save-corp">Save</button>
				</form>
            <?php } ?>
			<div class="pg-each"><?php echo $msg; ?></div>
		</div>
	</div>
<script>$(function(){ $(document).on('click',"#save-corp",function(){ $("#pg-select").submit(); })})</script>
</body>
</html>