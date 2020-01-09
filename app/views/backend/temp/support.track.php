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

	load_module("user");
	load_module("course");

	load_library("misc");

	load_plugin("exotel");

	$date = new DateTime("now");
	$call_statuses = ["pending", "done", "did_not_pick", "unreachable", "busy", "call_back", "invalid_number"];

	if (!empty($_POST["phone"]) && !empty($_POST["id"])) {

		$_POST["phone"] = trim($_POST["phone"]);

		//connect_call("08618593578", "09686630258", "09243522277");
		die(json_encode(["msg" => "Connecting to ".$_POST["phone"], "id" => $_POST["id"]]));

	}

	if (!empty($_POST["call_status"]) && !empty($_POST["id"])) {

		$subs_id = trim($_POST["id"], "s-");
		if (!in_array($_POST["call_status"], $call_statuses)) {
			die(json_encode(["err" => "Invalid Status"]));
		}
		$res_subs = db_query("SELECT * FROM user_enr_meta WHERE subs_id=".$subs_id);
		if (!isset($res_subs[0])) {
			db_exec("INSERT INTO user_enr_meta (subs_id, called_at, called_by, call_status) VALUES (".$subs_id.", ".db_sanitize($date->format("Y-m-d H:i:s")).", ".$_SESSION["user"]["user_id"].", ".db_sanitize($_POST["call_status"]).");");
		}
		else {
			db_exec("UPDATE user_enr_meta SET call_status=".db_sanitize($_POST["call_status"]).", called_by=".$_SESSION["user"]["user_id"].", called_at=".db_sanitize($date->format("Y-m-d H:i:s"))." WHERE subs_id=".$subs_id);
		}

		db_exec("INSERT INTO user_enr_meta_history (subs_id, channel, rep_id, status, `timestamp`) VALUES (".$subs_id.", 'call', ".$_SESSION["user"]["user_id"].", ".db_sanitize($_POST["call_status"]).", ".db_sanitize($date->format("Y-m-d H:i:s")).");");

		die(json_encode(["msg" => "<b>".$_SESSION["user"]["name"]."</b><br/>".$date->format("d M, Y H:i:s")." <span ".($_POST["call_status"] != "done" ? "style='color: red'" : "").">(".ucwords(str_replace("_", " ", $_POST["call_status"])).")</span>", "id" => $subs_id, "call_status" => $_POST["call_status"]]));

	}

	if (!empty($_POST["sms"]) && !empty($_POST["sms_id"]) && !empty($_POST["subs_id"])) {

		//send SMS with the text

		$res_subs = db_query("SELECT * FROM user_enr_meta WHERE subs_id=".$subs_id);
		if (!isset($res_subs[0])) {
			db_exec("INSERT INTO user_enr_meta (subs_id, sms_sent_by, sms_sent_at) VALUES (".$_POST["subs_id"].", ".$_SESSION["user"]["user_id"].", ".db_sanitize($edate->format("Y-m-d H:i:s")).");");
		}
		else {
			db_exec("UPDATE user_enr_meta SET sms_sent_at=".db_sanitize($date->format("Y-m-d H:i:s")).", sms_sent_by=".$_SESSION["user"]["user_id"]." WHERE subs_id=".$_POST["subs_id"]);
		}

		db_exec("INSERT INTO user_enr_meta_history (subs_id, channel, rep_id, status, `timestamp`) VALUES (".$subs_id.", 'sms', ".$_SESSION["user"]["user_id"].", 'done', ".db_sanitize($date->format("Y-m-d H:i:s")).");");

		if ($_POST["sms_id"] == "-1") {

			$res_sms = db_query("SELECT content FROM system_content WHERE context_type='welcome.sms';");
			$content = json_decode($res_sms[0]["content"], true);
			$content[] = $_POST["sms"];

			db_exec("UPDATE system_content SET content=".json_encode($content)." WHERE context_type='welcome.sms';");

		}

		die(json_encode(["msg" => "SMS sent to ".$_POST["phone"]]));

	}

	$duration = "30";
	if (isset($_POST["duration"])) {
		$duration = $_POST["duration"];
	}

	$res_subs = db_query("SELECT
								enr.sis_id AS sis_id,
								enr.lab_pass AS lab_pass,
								subs.subs_id,
								user.name AS name,
								user.email AS email,
								user.phone AS phone,
								IF(support.assigned_to IS NOT NULL, assignee.name, 'Unassigned') AS assigned_to,
								um.city AS city,
								IF(DATE(subs.start_date) = CURDATE(), DATE_FORMAT(subs.start_date, '%h:%i %p Today'), DATE_FORMAT(subs.start_date, '%e %b %Y, %h:%i %p')) AS start_date,
								IF(DATE(subs.end_date) = CURDATE(), DATE_FORMAT(subs.end_date, '%h:%i %p Today'), DATE_FORMAT(subs.end_date, '%e %b %Y, %h:%i %p')) AS end_Date,
								GROUP_CONCAT(CONCAT(enr.enr_id, '=', course.name, '=', IF(enr.lab_ip IS NOT NULL, enr.lab_ip, ''), '=', enr.lab_status) separator ';') AS courses,
								IF(DATE(support.email_sent_at) = CURDATE(), DATE_FORMAT(support.email_sent_at, '%h:%i %p Today'), DATE_FORMAT(support.email_sent_at, '%e %b %Y, %h:%i %p')) AS email_sent_at,
								IF(DATE(support.called_at) = CURDATE(), DATE_FORMAT(support.called_at, '%h:%i %p Today'), DATE_FORMAT(support.called_at, '%e %b %Y, %h:%i %p')) AS called_at,
								agent_caller.name AS caller,
								support.call_status,
								agent_smser.name AS smser,
								IF(DATE(support.sms_sent_at) = CURDATE(), DATE_FORMAT(support.sms_sent_at, '%h:%i %p Today'), DATE_FORMAT(support.sms_sent_at, '%e %b %Y, %h:%i %p')) AS sms_sent_at,
								support.comments,
								bundle.name AS bundle,
								(SELECT
										GROUP_CONCAT(
											CONCAT(
												history.subs_id, '=', history.channel, '=', historical_rep.name, '=', history.status, '=', DATE_FORMAT(history.timestamp, '%e %b %Y, %h:%i %p'), '=', history.comments
											) SEPARATOR ';'
										)
									FROM
										user_enr_meta_history AS history
									INNER JOIN
										user AS historical_rep ON historical_rep.user_id = history.rep_id AND history.rep_id IS NOT NULL
									WHERE
										history.subs_id = subs.subs_id
									ORDER BY `timestamp`
								) AS historical
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
							INNER JOIN
								course ON course.course_id = enr.course_id
							LEFT JOIN
								user_enr_meta AS support ON support.subs_id = subs.subs_id
							LEFT JOIN
								user AS agent_caller ON agent_caller.user_id = support.called_by AND support.called_by IS NOT NULL
							LEFT JOIN
								user AS agent_smser ON agent_smser.user_id = support.sms_sent_by AND support.sms_sent_by IS NOT NULL
							INNER JOIN
								course_bundle AS bundle ON bundle.bundle_id = subs_meta.bundle_id AND subs_meta.bundle_id IS NOT NULL
							LEFT JOIN
								user AS assignee ON assignee.user_id = support.assigned_to
							WHERE
								subs.status='active'
								AND
								enr.status='active'
								AND
								subs.start_date>DATE_SUB(CURDATE(), INTERVAL ".$duration." DAY)
							GROUP BY subs.subs_id
							ORDER BY subs.start_date DESC");

	$res_sms = content_get("welcome.sms");
	if ($res_sms !== false) {
		$res_sms = json_decode($res_sms, true);
	}

?>
<html>
<head>
	<title>New Students - JAWS</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
	<script src="https://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
	<script>
		$(document).ready(function() {
			$("#datepicker1").datepicker();
			$("#datepicker2").datepicker();
			$("#datepicker3").datepicker();
			$("#datepicker4").datepicker();
			$(".a-phone").click(function() {
				$.post("https://www.jigsawacademy.com/jaws/track", { phone : this.childNodes[1].data.trim(), id : this.id }, function(data, status) {
					var ret_obj = $.parseJSON(data);
					alert(ret_obj.msg);
					$("#s-" + ret_obj.id).css("display", "block");
					$("#cc-" + ret_obj.id).css("display", "block");
				});
				return false;
			});
			$(".call-status").change(function() {
				if (this.value != "done") {
					$("#b-" + this.id.substr(2)).css("display", "block");
				}
				$.post("https://www.jigsawacademy.com/jaws/track", { call_status : this.value, id : this.id }, function(data, status) {
					var ret_obj = $.parseJSON(data);
					if (ret_obj.err) {
						alert(ret_obj.err);
					}
					else {
						$("#c-" + ret_obj.id).html(ret_obj.msg);
						if (ret_obj.call_status == "done") {
							$("#c-" + ret_obj.id).addClass("caller-id-done");
							$("#b-" + ret_obj.id).css("display", "none");
						}
						else {
							$("#c-" + ret_obj.id).removeClass("caller-id-done");
						}
					}
				});
				return false;
			});
			$(".call-status").click(function() {
				return false;
			});
			$(".call-comment").click(function() {
				$("#comment-dialog").css("display", "block");
				return false;
			});
			$(".history-expand").click(function() {
				$("#history-body").html("");
				var td = $("#h-" + this.id.substr(3));
				if (td.children().length == 0) {
					return false;
				}
				var innerHtml = "";
				td.children("span").each(function() {
					innerHtml += "<tr><td>";
					if ($(this).data("channel") == "sms") {
						innerHtml += "SMS sent by " + $(this).data("rep") + " at " + $(this).data("timestamp");
					}
					else {
						innerHtml += $(this).data("rep");
						if ($(this).data("status") == "done") {
							innerHtml += " called at " + $(this).data("timestamp");
						}
						else if ($(this).data("status") == "call_back") {
							innerHtml += " called at " + $(this).data("timestamp") + ", but student asked to call back later.";
						}
						else if ($(this).data("status") == "busy") {
							innerHtml += " tried calling at " + $(this).data("timestamp") + ", but the number was busy.";
						}
						else if ($(this).data("status") == "invalid_number") {
							innerHtml += " tried calling at " + $(this).data("timestamp") + ", but the number is invalid.";
						}
						else if ($(this).data("status") == "did_not_pick") {
							innerHtml += " tried calling at " + $(this).data("timestamp") + ", but the call was unanswered.";
						}
						if ($(this).data("comments").length > 0) {
							innerHtml += " Comments: " + $(this).data("comments");
						}
					}
					innerHtml += "</td></tr>";
				});
				$("#history-body").html(innerHtml);
				$("#history-dialog").css("display", "block");
				return false;
			});
			$(".btn-sms").click(function() {
				$("#sms-dialog").css("display", "block");
				$("#subs-id").val(this.id.substr(2));
				return false;
			});
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
			$(".sms").change(function() {
				$("#txt-sms").val($(this).find("option:selected").text());
			});
			$("#btn-sms-send").click(function() {
				$.post("https://www.jigsawacademy.com/jaws/track", { sms : $("#txt-sms").val(), sms_id : $("#sms-id").val(), subs_id :  $("#subs-id").val() }, function(data, status) {
					var ret_obj = $.parseJSON(data);
					if (ret_obj.err) {
						alert(ret_obj.err);
					}
					else {
						alert(ret_obj.msg);
					}
				});
			});
		});
	</script>
	<script src="https://use.fontawesome.com/fc49ce4973.js"></script>
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<style>
		#search {
			margin: 10px;
			padding-left: 10px;
			padding-right: 10px;
			border: 1px solid rgba(0, 0, 0, 0.07);
		}
		#search-form {
			margin-top: 15px;
			width: 100%;
		}
		.col {
			float: left;
			width: 25%;
			padding-right: 10px;
			margin-bottom: 20px;
		}
		.form-div {
			border: 1px solid rgba(0, 0, 0, 0.07);
			padding: 5px 10px 5px 10px;
			text-transform: uppercase;
			font-weight: bold;
			font-size: 80%;
		}
		.form-div:active {
			border: 1px solid rgba(0, 0, 0, 0.1);
		}
		label {
			width: 100%;
		}
		.form-input {
			width: 100%;
			border: none;
			outline: 0;
			margin-top: 5px;
		}
		.form-sub-input {
			width: 45%;
			border: none;
			outline: 0;
		}
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
		.student-info {
			text-align: left;
			text-transform: capitalize;
		}
		.search-link {
			text-transform: lowercase;
			font-size: 90%;
		}
		.call-unassigned {
			color: red;
		}
		.caller-id-width {
			padding: 0px;
		}
		.caller-id-done {
			background-color: rgb(120, 255, 120);
		}
		.history-expand {
			padding: 2px;
			float: right;
		}
		.courses-hidden {
			display: none;
		}
		.courses-shown {

		}
		textarea, .sms {
			-webkit-margin-after: 1em;
			width: 99%;
		}
		.dlg-btn {
			-webkit-margin-after: 1em;
		}
		.tooltip {
			position: relative;
			display: inline-block;
		}
		.tooltip .tooltiptext {
			visibility: hidden;
			width: 120px;
			background-color: black;
			color: #fff;
			text-align: center;
			border-radius: 6px;
			padding: 5px 0;

			/* Position the tooltip */
			position: absolute;
			z-index: 1;
		}
		.tooltip:hover .tooltiptext {
			visibility: visible;
		}
	</style>
</head>
<body>
	<div>
        <center>
            <b>New Students</B> (You are logged in as: <?php echo $_SESSION["user"]["name"]; ?> <a href="<?php echo JAWS_PATH_WEB."/logout"; ?>">Logout</a>)
            <?php if (isset($msg)) echo "<br/>".$msg; ?>
        </center>
    </div><hr>
	<div id="search">
		<b>SEARCH</b>
		<form id="search-form" method="post">
			<div class="criteria">
				<div class="col">
					<div class="form-div">
						<label>student</label>
						<input type="text" class="form-input" name="user_search" placeholder="Email, Name, Jig ID or Phone">
					</div>
				</div>
				<div class="col">
					<div class="form-div">
						<label>start date</label>
						<div class="form-input">
							<input type="text" name="start_from_date" class="form-sub-input" id="datepicker1" placeholder="DD/MM/YYYY">
							<span style="width: 20%; margin-left: 3px; margin-right: 3px; padding-left: 2px; padding-right: 2px">to</span>
							<input type="text" name="start_to_date" class="form-sub-input" id="datepicker2" placeholder="DD/MM/YYYY">
						</div>
					</div>
				</div>
				<div class="col">
					<div class="form-div">
						<label>end date</label>
						<div class="form-input">
							<input type="text" name="end_from_date" class="form-sub-input" id="datepicker3" placeholder="DD/MM/YYYY">
							<span style="width: 20%; margin-left: 3px; margin-right: 3px; padding-left: 2px; padding-right: 2px">to</span>
							<input type="text" name="end_to_date" class="form-sub-input" id="datepicker4" placeholder="DD/MM/YYYY">
						</div>
					</div>
				</div>
				<div class="col">
					<div class="form-div">
						<label>support</label>
						<input type="text" class="form-input" name="user_search" placeholder="Email or Name">
					</div>
				</div>
			</div>
			<div style="margin-top: 15px">
				<input type="submit" value="SEARCH" />
			</div>
		</form>
	</div>
	<center>
		<a href="<?php echo JAWS_PATH_WEB ?>/track/edit">Assign Welcome Calls</a>
		<br />
		<table border="0" cellpadding="10" cellspacing="2" style="font-size: 95%;" id="enr">
			<thead>
				<tr class="header">
					<th>Student</th>
					<th>Phone</th>
					<th>Courses</th>
					<th>Start Date</th>
					<th>Welcome Email Sent At</th>
					<th>Welcome Call</th>
					<th>SMS</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($res_subs as $user) { ?>
					<tr id="tr1-<?php echo $user["subs_id"] ?>" class="enr-info">
						<td>
							<p class="student-info">
								<b><?php echo $user["name"] ?></b><br>
								<a href="<?php echo JAWS_PATH_WEB ?>/search?criterion=sis_id&search_text=<?php echo $user["sis_id"] ?>" class="search-link" target="_blank"><?php echo $user["email"] ?></a><br>
								<div style="margin-top: 5px"><b><span><?php echo $user["sis_id"] ?></span><span style="margin-left: 5em"><?php echo $user["lab_pass"] ?></span></b></div>
							</p>
						</td>
						<td id="td-<?php echo $user["subs_id"] ?>" align="center">
							<?php if ($user["call_status"] != "done" && empty($user["smser"])) { ?>
								<a href="#" id="<?php echo $user["subs_id"] ?>" class="a-phone" style="text-decoration: none"><i class="fa fa-phone" aria-hidden="true"></i><?php echo " ".$user["phone"] ?></a>
								<br><span class="<?php echo $user["assigned_to"] == "Unassigned" ? "call-unassigned" : "" ?>">(<?php echo $user["assigned_to"] ?>)</span>
								<select id="s-<?php echo $user["subs_id"] ?>" class="call-status" style="display: none">
									<?php foreach ($call_statuses as $status) { ?>
										<option value="<?php echo $status ?>" <?php echo ($user["call_status"] == $status ? "selected" : "") ?>><?php echo ucwords(str_replace("_", " ", $status)) ?></option>
									<?php } ?>
								</select><button id="cc-<?php echo $user["subs_id"] ?>" class="call-comment tooltip" style="display: none"><i class="fa fa-comment-o" aria-hidden="true"></i><span class="tooltiptext">Add a comment</span></button>
								<button id="b-<?php echo $user["subs_id"] ?>" class="btn-sms" style="display: none">Send SMS</button>
							<?php }
							else {
								echo $user["phone"]."<br><span class=".($user["assigned_to"] == "Unassigned" ? "call-unassigned" : "").">(".$user["assigned_to"].")</span>";
							} ?>
						</td>
						<td><?php echo (!empty($user["bundle"]) ? $user["bundle"] : $user["courses"]) ?></td>
						<td><?php echo $user["start_date"] ?></td>
						<td><?php echo $user["email_sent_at"] ?></td>
						<td id="c-<?php echo $user["subs_id"] ?>" class="caller-id <?php echo $user["call_status"] == "done" ? "caller-id-done" : "" ?> caller-id-width">
							<?php echo (!empty($user["caller"]) ? "<b>".$user["caller"]."</b><br/>".$user["called_at"]." <span ".($user["call_status"] != "done" ? "style='color: red" : "")."''>(".ucwords(str_replace("_", " ", $user["call_status"])).")</span>" : "Pending") ?>
							<?php if (!empty($user["call_status"]) && $user["call_status"] != "pending") { ?><button id="he-<?php echo $user["subs_id"] ?>" class="history-expand tooltip"><i class="fa fa-plus-square-o" aria-hidden="true"></i><span class="tooltiptext">View Communication History</span></button><?php } ?>
						</td>
						<td><?php echo (!empty($user["smser"]) ? $user["smser"]." @ ".$user["sms_sent_at"]." (".$user["sms_sent_at"].")" : "--") ?></td>
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
								else { ?>
									<input class="lab-info" id="<?php echo $detail[0] ?>" type='checkbox' value="<?php echo $details[0] ?>" style="margin-right: 10px" <?php echo ($details[3] == 'ul' ? "checked disabled" : "") ?>><?php echo $details[1] ?><br>
									<?php $labs = true;
								}

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
								<button id="" class="">Save</button>
							</td>
							<td colspan="5">
								<?php if (!empty($user["comments"])) {
									echo "Comment: ".$user["comments"];
								} ?>
							</td>
							<?php if (!empty($user["historical"])) {
								$historical = explode(";", $user["historical"]); ?>
								<td style="display: none" id="h-<?php echo $user["subs_id"] ?>">
									<?php foreach ($historical as $history) {
										$info = explode("=", $history); ?>
										<span data-channel="<?php echo $info[1] ?>" data-rep="<?php echo $info[2] ?>" data-status="<?php echo $info[3] ?>" data-timestamp="<?php echo $info[4] ?>" data-comments="<?php echo $info[5] ?>"></span>
									<?php } ?>
								</td>
							<?php } ?>
						<?php } ?>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</center>

	<div id="sms-dialog" class="w3-modal">
		<div class="w3-modal-content">
			<div class="w3-container">
				<span onclick="document.getElementById('sms-dialog').style.display='none'" class="w3-button w3-display-topright">&times;</span>
				<p>Choose a saved SMS or enter a new one:</p>
				<select class="sms" size="<?php echo ($res_sms[0] !== false ? 1 : (count($res_sms) > 5 ? 5 : count($res_sms))) ?>">
					<?php if ($res_sms !== false) {
						$sms_count = 0; ?>
						<option value="-1">Select</option>
						<?php foreach ($res_sms as $sms) { ?>
							<option value="<?php echo $sms_count++ ?>"><?php echo $sms ?></option>
						<?php } ?>
					<?php }
					else { ?>
						<option value="-1">No saved templates</option>
					<?php } ?>
				</select>
				<textarea id="txt-sms" placeholder="Please enter your message here"></textarea>
				<input type="text" id="subs-id" style="display: none">
				<input type="text" id="sms-id" style="display: none" value="-1">
				<center>
					<button id="btn-sms-save-send" class="sms-send dlg-btn">Save & Send</button>
					<button id="btn-sms-send" class="sms-send dlg-btn">Send</button>
				</center>
			</div>
		</div>
	</div>

	<div id="comment-dialog" class="w3-modal">
		<div class="w3-modal-content">
			<div class="w3-container">
				<span onclick="document.getElementById('comment-dialog').style.display='none'" class="w3-button w3-display-topright">&times;</span>
				<p>Enter a comment:</p>
				<textarea id="txt-sms" placeholder="Please enter your message here"></textarea>
				<input type="text" id="subs-id" style="display: none">
				<center>
					<button id="btn-comment-save" class="comment-save dlg-btn">Save</button>
				</center>
			</div>
		</div>
	</div>

	<div id="history-dialog" class="w3-modal">
		<div class="w3-modal-content">
			<div class="w3-container">
				<span onclick="document.getElementById('history-dialog').style.display='none'" class="w3-button w3-display-topright">&times;</span>
				<p>Communication history:</p>
				<table id="tbl-history" style="-webkit-margin-after: 1em; width: 100%">
					<thead>
						<tr>
							<th>Details</th>
						</tr>
					</thead>
					<tbody id="history-body">
					</tbody>
				</table>
				<center>
					<button onclick="document.getElementById('history-dialog').style.display='none'" class="dlg-btn">Okay</button>
				</center>
			</div>
		</div>
	</div>

</body>
</html>