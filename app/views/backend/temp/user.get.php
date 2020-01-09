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
	if (isset($_SERVER["QUERY_STRING"])) {

		$return_url = array();
		foreach ($_REQUEST as $key => $value) {

			if ($key == "p") {
				continue;
			}
			$return_url[] = $key."=".urlencode($value);

		}
		$return_url = JAWS_PATH_WEB."/search?".implode("&", $return_url);

	}
	else {
		$return_url = JAWS_PATH_WEB."/search";
	}

	// Login Check
	if (!auth_session_is_logged()) {
    	ui_render_login_front(array(
                	"mode" => "login",
                	"return_url" => $return_url,
                	"text" => "Please login to access this page."
            	));
    	exit();
	}

	// Priviledge Check
	$get_enrollment = auth_session_is_allowed("enrollment.get");
	$get_user_basic = auth_session_is_allowed("user.get");
	$get_user_adv = auth_session_is_allowed("user.get.adv");
	$get_payment = auth_session_is_allowed("payment.get");
	$get_enrollment_adv = auth_session_is_allowed("enrollment.get.adv");

	if ((!$get_enrollment) && (!$get_user_basic) && (!$get_user_adv) && (!$get_payment)) {
    	ui_render_msg_front(array(
            	"type" => "error",
            	"title" => "Jigsaw Academy",
            	"header" => "No Tresspassing",
            	"text" => "Sorry, but you do not have permission to access this page.<br/>Contact Jigsaw Support team for assistance."
            	));
    	exit();
	}

  	load_module("user");
  	load_module("subs");
  	load_module("course");
  	load_module("user_enrollment");

  	function user_profile_prep($user_obj, $user_id = true) {

  		if ($user_id) {
  			$user_obj = user_get_by_id($user_obj);
  		}

  		$user_obj["sis_id"] = "N/A";
  		$subs = subs_get_info_by_user_id($user_obj["user_id"]);
  		$user_obj["payment"] = payment_get_info_by_user_id($user_obj["user_id"]);

  		if (!$subs) {
  			return $user_obj;
  		}

  		$enr_arr = array();
  		$subs_arr = array();
  		foreach ($subs as $sub) {

  			if (strcmp($sub["status"], "active") != 0) {
  				continue;
  			}

  			if (!empty($sub["meta"]["bundle_id"])) {

  				$bundle = db_query("SELECT name FROM course_bundle WHERE bundle_id = ".$sub["meta"]["bundle_id"]);
  				$sub["bundle"] = $bundle[0]["name"];

  			}

  			$enrs = enrollments_get_by_subs_id($sub["subs_id"]);

			$combo_free = [];
			if (!empty($sub["combo_free"])) {

				$subs_combo_free = explode(";", $sub["combo_free"]);
				foreach ($subs_combo_free as $course) {
					$combo_free[] = explode(",", $course)[0];
				}

			}

			foreach ($enrs as $enr) {

				$course = course_get_info_by_id($enr["course_id"]);
				$enr["course"] = $course["name"];
				if (in_array($enr["course_id"], $combo_free)) {
					$enr["course"] .= " (Complimentary)";
				}
				unset($enr["course_id"]);
				$enr["section_id"] = section_get_by_id($enr["section_id"])["sis_id"];
				$enr["start_date"] = date_create_from_format("Y-m-d H:i:s", $sub["start_date"])->format("j F, Y");
				$enr["end_date"] = date_create_from_format("Y-m-d H:i:s", $sub["end_date"])->format("j F, Y");
				if ($sub["end_date_ext"] !== null) {
					$enr["end_date"] = date_create_from_format("Y-m-d H:i:s", $sub["end_date_ext"])->format("j F, Y");
				}
				if ($sub["freeze_date"] !== null) {
					$enr["freeze_date"] = date_create_from_format("Y-m-d H:i:s", $sub["freeze_date"])->format("j F, Y");
				}
				if ($sub["unfreeze_date"] !== null) {
					$enr["unfreeze_date"] = date_create_from_format("Y-m-d H:i:s", $sub["unfreeze_date"])->format("j F, Y");
				}
				$enr["status"] = $sub["status"];
				$enr["learn_mode"] = (strcmp($enr["learn_mode"], "il") == 0 ? "Premium" : "Regular");
				$enr["sis_date"] = strlen($enr["sis_file"]) > 0 ? "<a href='".JAWS_PATH_WEB."/sis?sis=".$enr["sis_file"]."'>".date_create_from_format("Y-m-d.H.i.s", $enr["sis_file"])->format("Y-m-d H:i:s")."</a>" : "N/A";

				$enr_arr[] = $enr;
				$sub["enrs"][] = $enr;

			}

			$subs_arr[] = $sub;

  		}
  		if (count($enr_arr) > 0) {

			// $user_obj["enr"] = $enr_arr;
			$user_obj["subs"] = $subs_arr;
			$user_obj["sis_id"] = $enr_arr[0]["sis_id"];

		}
		return $user_obj;

  	}

  	if (!empty($_POST["resend_wm"]) && $_POST["resend_wm"] == "1" && !empty($_POST["user_id"])) {

  		welcome_email_send_by_user_id($_POST["user_id"]);

  		$_REQUEST["criterion"] = "user_id";
  		$_REQUEST["search_text"] = $_POST["user_id"];

  	}

  	if (isset($_REQUEST["criterion"]) && isset($_REQUEST["search_text"])) {

  		$user = false;
  		$criterion = "";
  		switch ($_REQUEST["criterion"]) {

  			case "sis_id":
  				$criterion = "Jig ID";
  				$enr = db_query("SELECT user_id FROM user_enrollment WHERE sis_id=".db_sanitize(trim($_REQUEST["search_text"]))." LIMIT 1");
  				if (!$enr)
  					break;
  				$user = user_profile_prep($enr[0]["user_id"]);
  				break;

  			case "email":
  				// If the user has selected to only search partial account, return false if the found account is not partial
  				if (isset($_REQUEST["search_partial"])) {

  					$user = user_get_by_email(trim($_REQUEST["search_text"]), true);
  					if (strcmp($user["status"], "pending") != 0) {
  						$user = false;
  					}

  				}
  				else {
  					$user = user_get_by_email(trim($_REQUEST["search_text"]));
  				}
  				$criterion = "Email ID";
  				if (!$user) {
  					break;
  				}
  				$user = user_profile_prep($user, false);
  				break;

  			case "name":
  				$criterion = "Name";
  				$res_user = db_query("SELECT user_id FROM user WHERE name=".db_sanitize(trim($_REQUEST["search_text"])).";");
  				if (!$res_user) {
  					break;
  				}
  				$user = user_profile_prep($res_user[0]["user_id"]);
  				break;

  			case "phone":
  				$criterion = "Phone";
  				$res_user = db_query("SELECT user_id FROM user WHERE phone=".db_sanitize(trim($_REQUEST["search_text"])).";");
  				if (!$res_user) {
  					break;
  				}
  				$user = user_profile_prep($res_user[0]["user_id"]);
  				break;

			case "user_id":
				$criterion = "User ID";
				$user = user_get_by_id(trim($_REQUEST["search_text"]));
				if (!$user) {
					break;
				}
				$user = user_profile_prep($user, false);
				break;

  		}

  	}
  	else if (isset($_REQUEST["extend-duration"])) {

  		if (!$get_enrollment_adv || !isset($_REQUEST["jigid"])) {
  			die ("You do not have required previledges to use this feature.");
  		}

  		$freeze_date = NULL;
  		$unfreeze_date = NULL;

  		if (strlen($_REQUEST["freeze-date"]) > 0) {

  			$freeze_date = date_create_from_format("Y-m-d", $_REQUEST["freeze-date"]);
  			$unfreeze_date = date_create_from_format("Y-m-d", $_REQUEST["freeze-date"]);
  			$duration = "P".$_REQUEST["extend-duration"]."D";
  			$unfreeze_date->add(new DateInterval($duration));

  		}

  		$user_id = db_query("SELECT user_id FROM user_enrollment WHERE sis_id=".db_sanitize(trim($_REQUEST["jigid"]))." LIMIT 1");
  		if (!isset($user_id)) {
  			die ("You do not have required previledges to use this feature.");
  		}
  		$user_id = $user_id[0]["user_id"];
  		$subs_all = db_query("SELECT * FROM subs WHERE status='active' AND user_id=".$user_id);
  		foreach ($subs_all as $subs) {

  			$end_date = date_create_from_format("Y-m-d H:i:s", $subs["end_date"]);
  			$interval = "P".$_REQUEST["extend-duration"]."D";
  			$end_date->add(new DateInterval($interval));
  			$query = "UPDATE subs SET end_date_ext=".db_sanitize($end_date->format("Y-m-d H:i:s"))."".($freeze_date !== NULL ? ", freeze_date=".db_sanitize($freeze_date->format("Y-m-d H:i:s")) : "").($unfreeze_date !== NULL ? ", unfreeze_date=".db_sanitize($unfreeze_date->format("Y-m-d H:i:s")) : "")." WHERE subs_id=".$subs["subs_id"].";";
  			db_exec($query);

  		}

  		echo "Data saved!";

  	}
  	else if (isset($_POST["user_id"])) {

  		load_plugin("mobile_app");
		$mobile = new MobileApp;

  		$enrs = db_query("SELECT enr.sis_id, course.sis_id AS course_code FROM user_enrollment AS enr INNER JOIN course ON course.course_id = enr.course_id WHERE enr.user_id = ".db_sanitize($_POST["user_id"]));
  		$jig_id;
  		$course_codes = [];
  		foreach ($enrs as $enr) {

  			$jig_id = $enr["sis_id"];
  			$course_codes[] = $enr["course_code"];

  		}

  		$course_topics = $mobile->getCoursesTopics($course_codes, $jig_id);

  		$locked = false;
  		foreach ($course_topics as $course => $topics) {

  			if (videos_unlocked_check($topics) === false) {

  				$locked = true;
  				break;

  			}

  		}

  		die(json_encode(["msg" => $locked]));

  	}

function videos_unlocked_check($course) {

	foreach ($course as $topic_id => $topic_body) {

		foreach ($topic_body["v"] as $video) {

			if (is_null($video["vi"])) {
				return false;
			}

		}

	}

	return true;

}


?>

<html>
<head>
	<title><?php if (isset($user) && $user !== false) echo $user["name"]."'s profile - " ?>JAWS Search</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
	<script>
		$(document).ready(function() {
			$("#txt-search").focus();
			$("#mobapp").click(function() {
				$(this).attr("disabled", true);
				$.post("/jaws/search", {user_id: $("#user-id").val()}, function(result) {
					result = $.parseJSON(result);
					var msg = (result.msg ? "Videos are locked... :(" : "Videos are unlocked! Yay!!");
					var color = result.msg ? "red" : "blue";
					$("#mobapp-access").html("<span style='color: "+color+"'>"+msg+"</span><br><br><br>");
					$("#mobapp").attr("disabled", false);
				});
			});
		});
	</script>
	<style>
		#payment-info {
			border: 1px solid black;
			padding: 2px;
		}
	</style>
</head>

<body>
	<div>
        <center>
	        User Search (You are logged in as: <?php echo $_SESSION["user"]["name"]; ?> <a href="<?php echo JAWS_PATH_WEB."/logout"; ?>">Logout</a>)
	        <?php if (isset($msg)) echo "<br/>".$msg; ?>
        </center>
    </div><br />
	<center>
		<div id="search_box">
			<form id="inner" method="get">
				<select name="criterion">
					<option value="sis_id">Jig ID</option>
					<option value="email" selected>Email ID</option>
					<option value="name">Name</option>
					<option value="phone">Phone</option>
					<option value="user_id">User ID</option>
				</select>
				<input id="txt-search" name="search_text" type="text" placeholder="Seach" />
				<input type="submit" value="Search" /><br />
				<input name="search_partial" type="checkbox" value="true">Search Partial Account Only
			</form>
		</div>
	</center>
	<br />

	<?php if (isset($user)) {
		if (isset($_REQUEST["search_partial"])) echo "<center>Searching partial account only!</center><br />";
		if (!$user) {
			echo "<center>No results found for ".$criterion.": \"".$_REQUEST["search_text"]."\" query.</center>";
			exit();
		 } ?>
		<div id="profile">
			<center><img id="profile_img" src="<?php echo $user["photo_url"] ?>"></center><br />
			<table id="profile_desc" border="1">
				<tr>
					<?php echo ($get_user_adv) ? "<th>User ID</th>" : "" ?>
					<th>Jig ID</th>
					<th>Name</th>
					<th>Communication Email</th>
					<th>Phone</th>
					<th>Access Setup + Survey</th>
					<th>JLC Social Provider</th>
					<th>Facebook</th>
					<th>Google+</th>
					<th>LinkedIn</th>
					<?php if ($get_user_adv) { ?><th>Account Status</th><?php } ?>
				</tr>
				<tr>
					<?php echo ($get_user_adv) ? "<td>".$user["user_id"]."</td>" : "" ?>
					<td><?php echo $user["sis_id"] ?></td>
					<td><?php echo $user["name"] ?></td>
					<td><?php echo $user["email"] ?></td>
					<td><?php echo $user["phone"] ?></td>
					<td><?php echo (strlen($user["lms_soc"]) > 0 ? "Completed" : "Waiting on user!<br/>Personalized link: www.jigsawacademy.com/jaws/setupaccess?user=".$user["web_id"]) ?></td>
					<td><?php if ($user["lms_soc"] == "fb") echo "Facebook";
							  else if ($user["lms_soc"] == "gp") echo "Google+";
							  else if ($user["lms_soc"] == "li") echo "LinkedIn";
							  else if ($user["lms_soc"] == "corp") echo "Corp";
							  else echo ""; ?></td>
					<td><?php echo $user["soc_fb"] ?></td>
					<td><?php echo $user["soc_gp"] ?></td>
					<td><?php echo $user["soc_li"] ?></td>
					<?php if ($get_user_adv) { ?><td><?php echo ((strcmp($user["status"], "active") == 0) ? "Full" : "Partial") ?></td><?php } ?>
				</tr>
			</table><br />

		</div><br />
		<?php if ($get_payment || $get_user_adv) { ?>
		<div id="payments">
			<?php if(!$user["payment"]) { ?>
				This user has no payment records.
			<?php }
			else {
				$i = 1;
				foreach ($user["payment"] as $pay) {
					if (strcmp($user["payment"]["status"], "disabled") == 0) continue; ?>
					<div id="payment-info">
						<?php echo "Payment ".$i.":"; ?>
						<table border="1">
							<tr>
								<th>Total Sum</th>
								<th>Currency</th>
								<th>No. of Installments</th>
								<th>Creator</th>
							</tr>
							<tr>
								<td><?php echo $pay["sum_total"] ?></td>
								<td><?php echo $pay["currency"] ?></td>
								<td><?php echo $pay["instl_total"] ?></td>
								<?php $creator_id = $pay["instl"][1]["paylink"]["create_entity_id"] ?? $pay["instl"][2]["paylink"]["create_id"] ?? -1;
								if ($creator_id == -1)
									echo "<td>Not known</td>";
								else if ($creator_id == 0)
									echo "<td>(Website)</td>";
								else
									echo "<td>".user_get_by_id($creator_id)["name"]."</td>"; ?>
							</tr>
						</table>
						Installment Info:
						<table border="1">
							<tr>
								<th>Installment Amount</th>
								<th>Payment Status</th>
								<th>Payment Date</th>
								<th>Payment Mode</th>
								<th>Due Date</th>
							</tr>
							<?php foreach ($pay["instl"] as $instl) { ?>
								<tr>
									<td><?php echo $instl["sum"] ?></td>
									<td><?php if (strcmp($instl["status"], "due") == 0) echo "Due";
										else if (strcmp($instl["status"], "paid") == 0) echo "Paid";
										else echo "Disabled" ?></td>
									<td><?php if (strcmp($instl["status"], "paid") == 0) echo $instl["pay_date"];
										else echo "N/A"; ?></td>
									<td><?php if (strcmp($instl["status"], "paid") == 0) echo $instl["pay_mode"];
										else echo "N/A"; ?></td>
									<td><?php if (strcmp($instl["status"], "due") == 0) echo $instl["due_date"];
										else echo "N/A"; ?></td>
									<?php if (strcmp($instl["status"], "due") == 0) echo "<td>https://www.jigsawacademy.com/jaws/pay?pay=".$pay["instl"][1]["paylink"]["web_id"]."</td>"; ?>
								</tr>
							<?php } ?>
						</table>
					</div><br />
					<?php $i++;
				}
			} ?>
		</div>
		<?php }
		if ($get_enrollment || $get_user_adv) { ?>
		<div id="enrollments">
			Enrollments:<br/>
			<?php if (!isset($user["subs"][0]["enrs"])) { ?>
				<div id="no_enrollments">
					The user does not have any enrollments
				</div>
			<?php }
			else { ?>
				<center>
					<input type="hidden" id="user-id" value="<?php echo $user["user_id"] ?>">
					<button id="mobapp">Check mobile app access</button><br><br>
					<div id="mobapp-access"></div>
				</center>
				<table id="tbl_enr" border="1">
					<thead>
						<tr>
							<th>Course</th>
							<th>Learn Mode</th>
							<th>Start Date</th>
							<th>End Date</th>
							<th>Batch</th>
							<th>Lab IP</th>
							<th>Lab User ID</th>
							<th>Lab Password</th>
							<th>Freeze Date</th>
							<th>Unfreeze Date</th>
							<?php if ($get_enrollment_adv) { ?><th>SIS Imported on</th><?php } ?>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($user["subs"] as $subs) {
							if (!empty($subs["bundle"])) { ?>
								<tr>
									<td colspan="11" style="font-weight: bold; text-align: center"><?php echo $subs["bundle"] ?></td>
								</tr>
							<?php }
							foreach ($subs["enrs"] as $enr) { ?>
								<tr>
									<td><?php echo $enr["course"] ?></td>
									<td><?php echo $enr["learn_mode"] ?></td>
									<td><?php echo $enr["start_date"] ?></td>
									<td><?php echo $enr["end_date"] ?></td>
									<td><?php echo $enr["section_id"] ?></td>
									<td><?php echo $enr["lab_ip"] ?></td>
									<td><?php echo $enr["lab_user"] ?></td>
									<td><?php echo $enr["lab_pass"] ?></td>
									<td><?php echo (isset($enr["freeze_date"]) ? $enr["freeze_date"] : "N/A" ) ?></td>
									<td><?php echo (isset($enr["unfreeze_date"]) ? $enr["unfreeze_date"] : "N/A" ) ?></td>
									<?php if ($get_enrollment_adv) { ?><td><?php echo $enr["sis_date"] ?></td><?php } ?>
								</tr>
							<?php } ?>
						<?php } ?>
					</tbody>
				</table><br /><br />
				<?php if ($_SESSION["user"]["email"] == "himanshu@jigsawacademy.com" || $_SESSION["user"]["email"] == "himanshu.malpande@gmail.com") { ?>
					<form method="post" style="display: flex;background: red;padding: 10px;">
						<input type="hidden" name="user_id" value="<?php echo $user["user_id"] ?>">
						<input type="hidden" name="resend_wm" value="1">
						<input type="submit" value="Resend Welcome Email" style="margin: auto">
					</form><br><br>
				<?php } ?>
				<form method="post" action="https://www.jigsawacademy.com/jaws/sis">
					<input type="hidden" name="user_id" value="<?php echo $user["user_id"] ?>" />
					<input type="submit" value="Regenerate SIS file" /> <b><u>NOTE:</u></b> If this student is already present in the JLC, please remove the user.csv from the zip before importing the SIS file.<br />
				</form>
				<?php if ($get_enrollment_adv && strcmp($user["sis_id"], "N/A") != 0) {
/*
					$enr = $user["enr"][count($user["enr"]) - 1]; ?>
					<div id="enr-access">
					<form method="post" action="https://www.jigsawacademy.com/jaws/search">
						<input type="hidden" name="jigid" value="<?php echo $user["sis_id"] ?>" />
						Start the freeze from (If it is only extenion, leave the freeze date blank):<br />
						<input type="date" name="freeze-date" min="<?php echo $enr["start_date"] ?>" />. For <input type="number" name="extend-duration" min="15" max="90" /> days.<br />
						<input type="submit" value="Save" />
					</form>
					</div>

				<?php */}

			} ?>
		</div>
	<?php }

	} ?>
</body>
</html>
