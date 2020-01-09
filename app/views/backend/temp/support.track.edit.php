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

	// Prep
	$login_params["return_url"] = JAWS_PATH_WEB."/track";

	// Login Check
    if (!auth_session_is_logged()) {
        ui_render_login_front(array(
                    "mode" => "login",
                    "return_url" => $login_params["return_url"],
                    "text" => "Please login to access this page."
                ));
        exit();
    }

    if (!auth_session_is_allowed("enrollment.get") && !auth_session_is_allowed("enrollment.get.adv")) {
        ui_render_msg_front(array(
                "type" => "error",
                "title" => "Jigsaw Academy",
                "header" => "No Tresspassing",
                "text" => "Sorry, but you do not have permission to access this page.<br/>Contact Jigsaw Support team for assistance."
                ));
        exit();
    }

	$date = new DateTime("now");
	if (!empty($_POST["subs_id"]) && !empty($_POST["rep"])) {

		$res_subs = db_query("SELECT * FROM user_enr_meta WHERE subs_id=".$_POST["subs_id"]);
		if (!isset($res_subs[0])) {
			db_exec("INSERT INTO user_enr_meta (subs_id, assigned_to) VALUES (".$_POST["subs_id"].", ".$_POST["rep"].");");
		}
		else {
			db_exec("UPDATE user_enr_meta SET assigned_to=".$_POST["rep"]." WHERE subs_id=".$_POST["subs_id"]);
		}

		die(json_encode(["id" => $_POST["subs_id"]]));

	}

	$res_subs = db_query("SELECT
								enr.sis_id AS sis_id,
								subs.subs_id,
								user.name AS name,
								user.email AS email,
								user.phone AS phone,
								um.city AS city,
								DATE_FORMAT(subs.start_date, '%e %b %Y, %h:%i %p') AS start_date,
								DATE_FORMAT(subs.end_date, '%e %b %Y, %h:%i %p') AS end_Date,
								GROUP_CONCAT(CONCAT(enr.enr_id, '=', course.name, '=', IF(enr.lab_ip IS NOT NULL, enr.lab_ip, ''), '=', enr.lab_status) separator ';') AS courses,
								DATE_FORMAT(support.email_sent_at, '%e %b %Y, %h:%i %p') AS email_sent_at,
								support.assigned_to,
								bundle.name AS bundle
							FROM
								subs
							LEFT JOIN
								subs_meta ON subs_meta.subs_id = subs.subs_id
							INNER JOIN
								user ON user.user_id = subs.user_id
							INNER JOIN
								user_meta AS um ON um.user_id = subs.user_id
							INNER JOIN
								user_enrollment AS enr ON enr.subs_id = subs.subs_id
							LEFT JOIN
								course ON course.course_id = enr.course_id
							LEFT JOIN
								user_enr_meta AS support ON support.subs_id = subs.subs_id
							INNER JOIN
								course_bundle AS bundle ON bundle.bundle_id = subs_meta.bundle_id AND subs_meta.bundle_id IS NOT NULL
							WHERE
								support.call_status = 'pending'
								AND
								subs.status='active'
								AND
								enr.status='active'
								AND
								subs.status='active'
							GROUP BY subs.subs_id
							ORDER BY subs.start_date DESC");

	$res_team = db_query("SELECT user_id, name FROM user WHERE allow='support.rep';");

?>
<html>
<head>
	<title>Welcome Calls Assignment - JAWS</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
	<script>
		$(document).ready(function() {
			$(".enr-info").click(function() {
				var id = "#tr2" + this.id.substr(3);
				$(".courses-shown").each(function() {
					if (this != $(id)[0]) {
						$(this).addClass("courses-hidden").removeClass("courses-shown");
					}
				});
				$(id).toggleClass("courses-hidden");
				$(id).toggleClass("courses-shown");
			});
			$(".rep-assign").change(function () {
				if (this.value != "-1") {
					$.post("https://www.jigsawacademy.com/jaws/track/edit", { subs_id : this.id, rep : this.value }, function(data, status) {
						var ret_obj = $.parseJSON(data);
						if (ret_obj.err) {
							alert(ret_obj.err);
						}
					});
				}
				return false;
			});
			$(".rep-assign").click(function() {
				return false;
			});
		});
	</script>
	<script src="https://use.fontawesome.com/fc49ce4973.js"></script>
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
	<style>
		thead th {
			background-color: rgba(0, 0, 0, 0.095);
			text-align:center;
		}
		tr:nth-child(odd) {
			background-color: rgba(0, 0, 0, 0.075);
			text-align:center;
		}
		tr:nth-child(even) {
			background: #FFF;
			text-align:center;
		}
		.courses-hidden {
			display: none;
		}
		.courses-shown {

		}
	</style>
</head>
<body>
	<div>
        <center>
            <b>Assign Welcome Calls</B> (You are logged in as: <?php echo $_SESSION["user"]["name"]; ?> <a href="<?php echo JAWS_PATH_WEB."/logout"; ?>">Logout</a>)
            <?php if (isset($msg)) echo "<br/>".$msg; ?>
        </center>
    </div><hr>
	<center>
		<a href="<?php echo JAWS_PATH_WEB ?>/track">Welcome Calls Tracker</a>
		<br />
		<table border="0" cellpadding="10" cellspacing="2" style="font-size: 95%;" id="enr">
			<thead>
				<tr class="header">
					<th>Jig ID</th>
					<th>Name</th>
					<th>Email</th>
					<th>Phone</th>
					<th>Courses</th>
					<th>Start Date</th>
					<th>Welcome Email Sent At</th>
					<th>Assign to</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($res_subs as $user) { ?>
					<tr id="tr1-<?php echo $user["subs_id"] ?>" class="enr-info">
						<td><a href="<?php echo JAWS_PATH_WEB ?>/search?criterion=sis_id&search_text=<?php echo $user["sis_id"] ?>" target="_blank"><?php echo $user["sis_id"] ?></a></td>
						<td><?php echo $user["name"] ?></td>
						<td><a href="<?php echo JAWS_PATH_WEB ?>/search?criterion=sis_id&search_text=<?php echo $user["sis_id"] ?>" target="_blank"><?php echo $user["email"] ?></a></td>
						<td align="center"><?php echo $user["phone"]; ?></td>
						<td><?php echo (!empty($user["bundle"]) ? $user["bundle"] : $user["courses"]) ?></td>
						<td><?php echo $user["start_date"] ?></td>
						<td><?php echo $user["email_sent_at"] ?></td>
						<td>
							<select id="<?php echo $user["subs_id"] ?>" class="rep-assign">
								<option value="-1">Select</option>
								<?php foreach ($res_team as $rep) { ?>
									<option value="<?php echo $rep["user_id"] ?>" <?php echo ($rep["user_id"] == $user["assigned_to"] ? "selected" : "") ?>><?php echo $rep["name"] ?></option>
								<?php } ?>
							</select>
						</td>
					</tr>
					<tr id="tr2-<?php echo $user["subs_id"] ?>" class="courses-hidden">
						<td colspan="2" align="left">
							<?php
							$labs = false;
							$courses = explode(";", $user["courses"]);
							foreach ($courses as $course) {

								$details = explode("=", $course);
								if (empty($details[2])) { ?>
									<input class="lab-info" id="<?php echo $detail[0] ?>" type='checkbox' value="<?php echo $details[0] ?>" style="margin-right: 10px;" disabled><?php echo $details[1] ?><br>
								<?php }

							} ?>
						</td>
						<?php if ($labs) { ?>
							<td colspan="1" align="left">
								<?php
								foreach ($courses as $course) {

									$details = explode("=", $course);
									if (empty($details[2])) {
										echo "<br>";
									}
									else {
										echo $details[2]."<br>";
									}

								} ?>
							</td>
							<td>
							</td>
							<td colspan="5">
								<?php if (!empty($user["comments"])) {
									echo "Comment: ".$user["comments"];
								} ?>
							</td>
						<?php } ?>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</center>
</body>
</html>