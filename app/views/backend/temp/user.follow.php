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

	// Load stuff
	load_module("ui");
	load_module('user');

	// Init Session
	auth_session_init();

	// Prep
	$login_params["return_url"] = JAWS_PATH_WEB."/follow";

	// Login Check
	if (!auth_session_is_logged()) {
		ui_render_login_front(array(
					"mode" => "login",
					"return_url" => $login_params["return_url"],
					"text" => "Please login to access this page."
				));
		exit();
	}

	// Priviledge Check
	if (!auth_session_is_allowed("user.get")) {
		ui_render_msg_front(array(
				"type" => "error",
				"title" => "Jigsaw Academy",
				"header" => "No Tresspassing",
				"text" => "Sorry, but you do not have permission to access this page.<br/>Contact Jigsaw Support team for assistance."
				));
		exit();
	}

	// RENDER
?>

<html>
	<head>
		<title>JAWS - User Follow</title>
	</head>
	<body style='font-family: sans-serif; font-size: 90%;'>
		<style scoped>
			table td {
				background-color: rgba(0,0,0,0.075);
			}
			table tr:first-child{
				font-weight: bold;
				text-transform: uppercase;
				font-size: 80%;
			}

		</style>

		<center>
			<b>User Follow</b>&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: gray;">Logged in as <?php echo $_SESSION["user"]["name"]; ?>!</span>&nbsp;<a href="https://www.jigsawacademy.com/jaws/logout">Logout</a>
			<hr>

			<br/>
			<form method='GET'>
				<input type='text' name='email' placeholder='User Email' value='<?php echo $_GET["email"] ?? "";?>'/>
				<input type='submit' value='Follow'></input>
			</form>
			<br/>
			<hr>
			<br/><br/>

			<?php
				// FOLLOW ??
				$query;
				$user = false;

				if ((isset($_GET['email']) && (strlen($_GET['email']) > 0)) || (isset($_GET['id']) && (strlen($_GET['id']) > 0))) {

					if (isset($_GET['email'])) {
						$user = user_get_by_email($_GET['email'], true);
						if ($user === false) die('<b>User Not Found !</b>');
					}
					elseif (isset($_GET['id'])) {
						$user = user_get_by_id($_GET['id']);
						if ($user === false) die('<b>User Not Found !</b>');
					}

					?>
					<script type='text/javascript'>
						document.title = "<?php echo $user['name'].' - Follow JAWS User';?>";
					</script>
					<?php

					$query = 'SELECT *
						FROM
							system_activity AS activity
						WHERE
							( act_type = "paylink.parse.expired" OR
							act_type = "paylink.parse" OR
							act_type = "paylink.confirm" OR
							act_type = "paylink.send" OR
							act_type = "'.((auth_session_is_allowed("activity.get.lms.setup") || auth_session_is_allowed("activity.get.all")) ? "lms.setup" : "blah").'" ) AND
							((
							context_type = "user_id" AND
							context_id = '.$user['user_id'].'
							) OR
							(
							entity_type = "user_id" AND
							entity_id = '.$user['user_id'].'
							))
						ORDER BY
							act_id DESC;
					';
					?>
					<div id="profile">
					<center><img id="profile_img" src="<?php echo $user["photo_url"] ?>"></center><br />
					<table id="profile_desc" border="0" cellspacing='2' cellpadding='10'>
						<tr>
							<td>Name</td>
							<td>Communication Email</td>
							<td>Phone</td>
							<?php echo ((auth_session_is_allowed("activity.get.lms.setup") || auth_session_is_allowed("activity.get.all")) ? '<td>Access Setup + Survey</td><td>JLC Social Provider</td>' : ''); ?>
							<?php if ((auth_session_is_allowed("activity.get.lms.setup") || auth_session_is_allowed("activity.get.all"))) { ?>
								<td>Facebook</td>
								<td>Google+</td>
								<td>LinkedIn</td>
								<td>Account Status</td>
							<?php } ?>
						</tr>
						<tr>
							<td><?php echo $user["name"] ?></td>
							<td><?php echo $user["email"] ?></td>
							<td><?php echo $user["phone"] ?></td>
							<?php echo ((auth_session_is_allowed("activity.get.lms.setup") || auth_session_is_allowed("activity.get.all")) ? '<td>'.(strlen($user["lms_soc"]) > 0 ? "Completed" : "Waiting on user!<br/>Personalized link: www.jigsawacademy.com/jaws/setupaccess?user=".$user["web_id"]).'</td><td>'.(strlen($user["lms_soc"]) > 0 ? $user["lms_soc"] : "").'</td>' : ''); ?>
							<?php if ((auth_session_is_allowed("activity.get.lms.setup") || auth_session_is_allowed("activity.get.all"))) { ?>
								<td><?php echo $user["soc_fb"] ?></td>
								<td><?php echo $user["soc_gp"] ?></td>
								<td><?php echo $user["soc_li"] ?></td>
								<td><?php echo ((strcmp($user["status"], "active") == 0) ? "Full" : "Partial") ?></td>
							<?php } ?>
						</tr>
					</table><br />

				</div>
				<br/><br/>
				<hr>
				<br/><br/>

				<?php
				}
				else {

					if (!auth_session_is_allowed("activity.get.all")) die();

					$query = 'SELECT
							activity.act_date AS "act_date",
							activity.content AS "content",
							user.name AS "name",
							user.email AS "email",
							user.phone AS "phone",
							user.user_id AS "user_id",
							instl.sum AS "sum",
							instl.currency AS "currency",
							(CASE WHEN link.create_entity_type = "user" THEN agent.name ELSE "(Website)" END) AS "agent-name"
						FROM
							system_activity AS activity
						INNER JOIN
							user AS user
							ON user.user_id = activity.context_id
						INNER JOIN
							payment_link AS link
							ON activity.entity_id = link.paylink_id
						INNER JOIN
							payment_instl as instl
							ON link.instl_id = instl.instl_id
						LEFT JOIN
							user AS agent
							ON link.create_entity_id = agent.user_id
						WHERE
							( activity.act_type = "paylink.parse" OR
							activity.act_type = "paylink.confirm" OR
							activity.act_type = "paylink.parse.expired" OR
							activity.act_type = "paylink.send" )
						ORDER BY
							activity.act_id DESC
						LIMIT 500;
					';
				}

				// Get Activity
				$activity = db_query($query);
				?>

				<div>
					<table border="0" cellspacing='2' cellpadding='10' style='font-size: 95%;'>
						<tr>
							<td style='text-align:center;'>Time (Newest To Oldest)</td>
							<td style='text-align:center;'>Name</td>
							<td style='text-align:center;'>Email</td>
							<td style='text-align:center;'>Phone</td>
							<td style='text-align:center;'>Activity</td>
							<?php if ($user === false) { ?>
								<td style='text-align:center;'>Amount</td>
								<td style='text-align:center;'>Agent</td>
							<?php } ?>
						</tr>

						<?php
							foreach($activity as $act) {

								$name	;
								$phone;
								$email;
								$id;

								if ($user!==false) {
									$name = $user['name'];
									$phone = $user['phone'];
									$email = $user['email'];
									$id = $user['user_id'];
								}
								else {
									$name = $act['name'];
									$phone = $act['phone'];
									$email = $act['email'];
									$id = $act['user_id'];
								}

								$detail='';
								if ($act['context_type'] == 'paylink_id') {

								}

								if ($act["agent-name"] != "(Website)" || $act["agent-name"] == "(Website)" && auth_session_is_allowed("activity.get.website")) {
									?>
									<tr>
										<td><?php echo date('g:i A, dS M Y', strtotime($act['act_date'])); ?></td>
										<td><?php echo $name; ?><?php if (auth_session_is_allowed("activity.get.lms.setup") || auth_session_is_allowed("activity.get.all")) { ?>&nbsp;<a style='opacity: 0.75; font-size: 70%;' href='https://www.jigsawacademy.com/jaws/follow?email=<?php echo urlencode($email); ?>' target='_blank'>Follow</a>&nbsp;<a style='opacity: 0.75; font-size: 70%;' href='https://www.jigsawacademy.com/jaws/search?criterion=user_id&search_text=<?php echo urlencode($id); ?>' target='_blank'>Search</a><?php } ?></td>
										<td><?php echo $email; ?></td>
										<td><?php echo $phone; ?></td>
										<td><?php echo $act['content']; ?></td>
										<?php if ($user === false) { ?>
											<td><?php echo strtoupper($act['currency']).' '.number_format($act['sum']); ?></td>
											<td><?php echo $act['agent-name']; ?></td>
										<?php } ?>
									</tr>
								<?php
								}
							}
						?>
					</table>
				</div>
		</center>
	</body>
</html>
