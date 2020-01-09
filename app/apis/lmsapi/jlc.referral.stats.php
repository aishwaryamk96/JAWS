<?php
die("This page is under maintenance. Please try again after sometime");
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

    	JIGSAW ACADEMY WORKFLOW SYSTEM v2
    	---------------------------------
*/

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header('Location: https://www.jigsawacademy.com');
		die();
	}

	load_module("user");
	load_module("subs");

	function get_fname($name)
	{
		$tokens = explode(" ", $name);
		$i = 0;
		while ($i < count($tokens))
		{
			if (strlen($tokens[$i]) <= 2)
				$i++;
			else
			{
				if (ctype_alpha($tokens[$i]))
					break;
				else
				$i++;
			}
		}
		if ($i == count($tokens))
			$i = 0;
		return $tokens[$i];
	}

	$user;
	$user_src = "user";

	if (!isset($_REQUEST["token"]))
		$user = user_get_by_id(18);
	else
	{
		$psk_info = psk_info_get($_REQUEST["token"]);
		if ($psk_info["entity_type"] == "user")
			$user = user_get_by_id($psk_info["entity_id"]);
		else
		{
			$res_act = db_query("SELECT * FROM system_activity WHERE act_id=".$psk_info["entity_id"]);
			$user = json_decode($res_act[0]["content"], true);
			$user["user_id"] = $res_act[0]["act_id"];
			$user_src = "system_activity";
		}
	}

	// Delete this token
	psk_expire($user_src, $user["user_id"], "jlc.referral.get");

	// Delete any old referral tokens
	psk_expire($user_src, $user["user_id"], "jlc.referral");

	$date = new DateTime("now");

	// Check system_activity for the student
	$res_act = db_query("SELECT * FROM system_activity WHERE act_type='jlc.referral' AND activity='referral' AND context_type=".db_sanitize($user_src)." AND context_id=".$user["user_id"]." ORDER BY act_id DESC;");

	// Lists the status of each referral
	$stats = array();
	// Holds the total number of referrals ever made
	$referrals_total = 0;
	// Holds the count of referrals done until now, resets if any referral enrolls
	$referral_flag = 0;
	// Holds the count of referrals enrolled
	$enrollments_count = 0;
	// Globally controls if the user can claim referral reward or not
	$paid_flag = false;
	// Total reward accumulated until now
	$reward_accrued = 0;
	// Total reward claimed until now
	$reward_claimed = 0;
	// Reward pending confirmation
	$reward_pending_confirmation = 0;

	// If any referrals found, process them
	if (isset($res_act[0]))
	{
		$user_resend_token = psk_generate($user_src, $user["user_id"], "jlc.referral.resend");
		// Check if the user is eligible to claim the referral reward
		// Criteria is the user should have paid all the installments and should have completed the 7 days of refund period
		$pay_info = payment_get_info_by_user_id($user["user_id"]);
		// Check if JAWS has payment history for the student
		if ($pay_info !== false)
		{
			foreach ($pay_info as $payment)
			{
				if (strcmp($payment["status"], "paid") != 0) {
					$paid_flag = $paid_flag || false;
				}
				else
				{
					foreach ($payment["instl"] as $instl)
					{
						if (strcmp($instl["status"], "paid") != 0)
						{
							$paid_flag = false;
							break 2;
						}
						else {
							$paid_flag = true;
						}
					}
				}
			}
			// Check if the referrer is still in the 7 days refund period; if yes, the user cannot claim the referral reward
			if ($paid_flag)
			{
				$subs = db_query("SELECT start_date FROM subs WHERE user_id=".$user["user_id"]." ORDER BY start_date DESC LIMIT 1;");
				$start_date = date_create_from_format("Y-m-d H:i:s", $subs[0]["start_date"])->add(new DateInterval("P7D"));
				if ($date < $start_date)
					$paid_flag = false;
			}
		}
		else {
			$paid_flag = true;
		}
		if ($user["user_id"] == 18)
			$paid_flag = true;

		// Start building the stats
		foreach ($res_act as $activity)
		{
			$content = json_decode($activity["content"], true);
			$referral_flag = $content["n"];
			$content = $content["r"];
			$putback = array();
			$edit = false;
			// Process each referral record
			foreach ($content as $referral)
			{
				$referrals_total++;

				// Name, Email and referral Date
				$stats[$i]["email"] = $referral["e"];
				$stats[$i]["name"] = $referral["n"];
				$referral_date = date_create_from_format("Y-m-d H:i:s", $referral["d"]);
				$stats[$i]["date"] = $referral_date->format("d-M Y");
				$referral_date = $referral_date->setTime(0, 0, 0);

				// Days left (max 30) for the referral to expire if the referral has not paid
				//$days_left = floor(abs($date->format("U") - $referral_date->format("U")) / 86400);
				$days_left = date_diff($date, $referral_date, true);

				// Courses or bundles the referral was referred for or has enrolled in
				$consult = false;

				// If the referral process is complete and bonus has been claimed
				if (isset($referral["x"]) && ($referral["x"] == "1" || $referral["x"] == "2"))
				{
					// So that resend invite button does not show up
					$stats[$i]["x"] = $referral["x"];

					if ($referral["x"] == "1")
						$stats[$i]["status"] = "Awaiting voucher approval";
					else
						$stats[$i]["status"] = "Amazon voucher awarded";

					$referral_user = user_get_by_email($referral["e"]);

					// Check if the referral has been sent any payment link or has attempted any payment
					$subs = db_query("SELECT subs.*, subs_meta.bundle_id FROM subs INNER JOIN subs_meta ON subs.subs_id = subs_meta.subs_id WHERE status != 'inactive' AND user_id=".$referral_user["user_id"]." ORDER BY subs_id DESC LIMIT 1;")[0];

					// Fetch the name of the bundle or courses for which the referral wished to or enrolled
					if (isset($subs["bundle_id"]) && strlen($subs["bundle_id"]) > 0)
						$stats[$i]["courses"] = db_query("SELECT name FROM course_bundle WHERE bundle_id=".$subs["meta"]["bundle_id"])[0]["name"];
					else
					{
						$courses = explode(";", trim($subs["combo"].";".$subs["combo_free"], ";"));
						$courses_str = array();
						foreach ($courses as $course)
							$courses_str[] = db_query("SELECT name FROM course WHERE course_id=".explode(",", $course)[0])[0]["name"];

						$stats[$i]["courses"] = implode(", ", $courses_str);
					}
					$enrollments_count++;
					if ($referral["x"] == "1")
						$reward_pending_confirmation += 1000;
					else
						$reward_claimed += 1000;

					$stats[$i]["claim"]["coupon"] = $referral["cc"];

					$i++;
					$putback[] = $referral;
					continue;
				}
				else if (isset($referral["x"]) && $referral["x"] == "-1")
				{
					$stats[$i]["status"] = "Invite expired";
					$courses_str = "";
					foreach ($referral["c"] as $consults)
					{
						if (substr($consults, 0, 1) == "c")
							$courses_str .= db_query("SELECT name FROM course WHERE course_id=".substr($consults, 1))[0]["name"].", ";
						else
							$courses_str .= db_query("SELECT name FROM course_bundle WHERE bundle_id=".substr($consults, 1))[0]["name"].", ";
					}
					$stats[$i]["courses"] = substr($courses_str, 0, -2);
					$stats[$i]["claim"] = array("coupon" => $referral["cc"], "eligible" => 0);

					$i++;
					$putback[] = $referral;
					continue;
				}

				$days_left_str;

				// Check if the referral is more than 30 days old
				if ($days_left->format("%a") > 30)
				{
					$status = "Invite expired";
					$referral["x"] = -1;
					$edit = true;
				}
				else
					$days_left_str = (30 - $days_left->format("%a"))." days left";

				// Check if referral has registerred
				$referral_user = user_get_by_email($referral["e"]);
				$enrolled = false;
				if ($referral_user)
				{
					if ($referral["x"] != -1)
						$status = $days_left_str." | In progress";

					// Check if the referral has been sent any payment link or has attempted any payment
					$subs = db_query("SELECT subs.*, subs_meta.bundle_id FROM subs INNER JOIN subs_meta ON subs.subs_id = subs_meta.subs_id WHERE status != 'inactive' AND user_id=".$referral_user["user_id"]." ORDER BY subs_id DESC LIMIT 1;");

					if (isset($subs[0]))
					{
						$subs = $subs[0];
						$edit = false;
						$enrollments_count++;

						$status = "Claim reward (In fulfillment period)";

						$stats[$i]["claim"] = $paid_flag;
						// If the referer is eligible to claim reward and no coupon code has been already created for the referer, create one now
						if ($paid_flag)
						{
							// Check if the user is eligible to claim the referral reward for this referral
							$pay_info = payment_get_info_by_user_id($referral_user["user_id"]);
							foreach ($pay_info as $payment)
							{
								if (strcmp($payment["status"], "paid") != 0)
									$paid_flag = false;
								else
								{
									foreach ($payment["instl"] as $instl)
									{
										if (strcmp($instl["status"], "paid") != 0)
										{
											$stats[$i]["claim"] = false;
											break 2;
										}
									}
								}
							}
							// Check if the referral is still in the 7 days refund period; if yes, the referrer cannot claim the referral reward
							if ($stats[$i]["claim"])
							{
								$start_date = date_create_from_format("Y-m-d H:i:s", $subs["start_date"])->add(new DateInterval("P7D"));
								if ($date < $start_date)
									$stats[$i]["claim"] = false;
							}
							$reward_accrued += 1000;
						}

						// Fetch the name of the bundle or courses for which the referral wished to or enrolled
						if (isset($subs["bundle_id"]) && strlen($subs["bundle_id"]) > 0)
							$stats[$i]["courses"] = db_query("SELECT name FROM course_bundle WHERE bundle_id=".$subs["bundle_id"])[0]["name"];
						else
						{
							$courses = explode(";", trim($subs["combo"].";".$subs["combo_free"], ";"));
							$courses_str = array();
							foreach ($courses as $course)
								$courses_str[] = db_query("SELECT name FROM course WHERE course_id=".explode(",", $course)[0])[0]["name"];

							$stats[$i]["courses"] = implode(", ", $courses_str);
						}

						$enrolled = true;
					}
					else
						$consult = true;
				}
				else
				{
					$status = $days_left_str." | Waiting for action";
					$consult = true;
				}
				// If the referrer can claim reward, pass a token for claim API and the coupon code
				if ($stats[$i]["claim"])
				{
					$stats[$i]["claim"] = array("user_id" => $referral_user["user_id"], "token" => psk_generate($user_src, $user["user_id"], "jlc.referral.claim"));
					$status = "Claim reward";
				}

				if ($enrolled)
					$stats[$i]["claim"]["no_claim"] = 1;
					$stats[$i]["claim"]["coupon"] = $referral["cc"];

				if ($consult)
				{
					$courses_str = "";
					foreach ($referral["c"] as $consults)
					{
						if (substr($consults, 0, 1) == "c")
							$courses_str .= db_query("SELECT name FROM course WHERE course_id=".substr($consults, 1))[0]["name"].", ";
						else
							$courses_str .= db_query("SELECT name FROM course_bundle WHERE bundle_id=".substr($consults, 1))[0]["name"].", ";
					}
					$stats[$i]["courses"] = substr($courses_str, 0, -2);
				}

				$stats[$i]["status"] = $status;
				$i++;

				$putback[] = $referral;
			}

			if (($referrals_total % ($enrollments_count + 1)) == 5)
			{
				if ($referral_flag == 0)
					$edit = true;
				$referral_flag = 1;
			}

			// Might need to update the record...
			if ($edit)
			{
				db_exec("UPDATE system_activity SET content=".db_sanitize(json_encode(array("r" => $putback, "n" => $referral_flag)))." WHERE act_id=".$activity["act_id"]);
			}
		} // End of foreach($res_act as $activity)
	} // End of if (isset($res_act[0]))

	$courses_list = array();
	$token;

	$stats = array_reverse($stats);

	// If referral count is less than 5, the referral form will be rendered
	if (/**$referral_flag == 0**/1)
	{
		// Token for the referral submission form
		$token = psk_generate($user_src, $user["user_id"], "jlc.referral");
		// Gather bundles and courses to be shown on referral form
		$bundles = db_query("SELECT bundle.bundle_id, bundle.name, meta.content FROM course_bundle AS bundle INNER JOIN course_bundle_meta AS meta ON bundle.bundle_id = meta.bundle_id WHERE bundle.status='enabled' AND bundle.bundle_type='specialization';");
		foreach ($bundles as $bundle)
		{
			$content = json_decode($bundle["content"], true);
			$courses_list[] = array("id" => "b".$bundle["bundle_id"], "name" => $bundle["name"], "url" => $content["url_web"]);
		}
		$courses = db_query("SELECT course.course_id, course.name, meta.content FROM course INNER JOIN course_meta AS meta ON course.course_id = meta.course_id WHERE course.status='enabled';");
		foreach ($courses as $course)
		{
			$content = json_decode($course["content"], true);
			$courses_list[] = array("id" => "c".$course["course_id"], "name" => $course["name"], "url" => $content["url_web"]);
		}
	}

?>


<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Refer a friend</title>
		<meta name="author" content="Dashboard">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

		<link rel="stylesheet" href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL ?>/common/fa/css/chosen.min.css">
		<link rel="stylesheet" href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL ?>/common/fa/css/font-awesome.min.css">
		<link rel="stylesheet" href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL ?>/common/font-awesome-animation/faa.min.css">
		<link rel="stylesheet/less" type="text/css" href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL."/app/templates/jaws/backend/dashtemp/theme.light.less" ?>" />
		<link rel="stylesheet/less" type="text/css" href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL."/app/templates/jaws/backend/dashtemp/layout.less"; ?>" />
		<!--<link rel="stylesheet" type="text/css" href="app/styles/compiled.css" />-->

		<script src="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL ?>/common/jquery/jquery-2.2.3.min.js"></script>
		<script src="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL ?>/common/less.js/less.min.js" data-env="development"></script>

		<!-- <script src="app/js/tourian.js"></script>
		<script src="app/js/dash.js"></script> -->
		<script src="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL ?>/common/jquery/jquery-ui.js"></script>
		<script src="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL ?>/common/jquery/chosen.jquery.min.js" ></script>
		<link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
		<script>
			$(document).ready(function() {
				// $(".chosen-select").chosen({ width: '23%',
				// 	max_selected_options: 2});
				// $(".chosen-select").bind("chosen:maxselected", function () {
				// 	$(".error").html('You can only select two courses');
				// });

				$(".chosen-select").chosen({
				width: "100%",
				max_selected_options: 2
				});
				$(".chosen-select").bind("chosen:maxselected",
				function() {
				$(".error").html('You can only select two courses');
				});
			});
		</script>
	</head>
	<body data-tourian="false">
		<!-- <div class="wrapper">
			<div id="main-container" class="">
				<div class="wrapper" style="min-height: 100vh;"> -->
					<div class="section-boxed">
						<div class="refer-heading"><!-- <span>5</span> for you <span>5</span> for me -->
							AN <span>AMAZON</span> TREAT AWAITS YOU
						</div>
						<div class="tagline">
							<div class="tag-1">Introduce friends to Jigsaw Academy. Get them enrolled. Get rewarded.</div>
							<div class="tag-2">Get an Amazon <span><a href="#terms_cond">*</a></span>voucher of  <i class="fa fa-inr" aria-hidden="true"></i> 1000 for every friend of your that enrolls.</div>
						</div>

						 <?php if ($referral_flag == 0)
						 {
						 	$disabled = "";
						 	$display = 'style="display:none"';
						 }
						 else
						 {
						 	$disabled = "disabled";
						 	$display = 'style="display:block"';
						 }
						 	?>
							<div class="refer-form" align="center">
								<form id="refer-form" method="post" action="" onsumbit="return false;">
									<div class="form-inputs">
										<div class="refer-input">
											<input <?php echo $disabled; ?> id="friend-name" required="true" title="You have only <?php echo 5 - $referrals_total % 5 ?> invites to send out" type="text" name="friend_name" placeholder="Friend's name" data-referral-count="<?php echo 5 - $referrals_total % 5 ?>">
											<div class="err error-name"></div>
										</div>

										<div class="refer-input">
											<input <?php echo $disabled; ?>  required="true" type="email" name="friend_email" placeholder="Email address">
											<div class="err error-email"></div>
										</div>

										<div class="refer-input">
											<input <?php echo $disabled; ?>  required="true" type="text" name="friend_phone" placeholder="Mobile number" minlength="10" maxlength="10">
											<div class="err error-phone"></div>
										</div>


										<input type="hidden" name="token" id="token" value="<?php echo $token ?>">

										<div class="refer-input">
											<select <?php echo $disabled; ?>   name="recommended_course[]" data-placeholder="Select 1-2 recommended courses" class="chosen-select" multiple="true" >
												<?php foreach ($courses_list as $course)
												{ ?>
													<option value="<?php echo $course["id"] ?>" data-url="<?php echo $course["url"] ?>"><?php echo $course["name"] ?></option>
												<?php } ?>
											</select>
											<div class="err error"></div>
										</div>

										<?php //if ($referral_flag != 0) { ?>
										<!-- <div <?php //echo $display; ?> class="error-div unlock">
											<div class="err error-locked">You can invite more after at least one of your friends enroll</div>
										</div> -->
										<?php //} ?>

										<div class="preview">
											<?php if ($referral_flag != 0)
												{ ?>
													<input id="preview" style="pointer-events: none; background:#ccc;" type="button" name="preview" value="Preview invite">
												<?php } else{ ?>
													<input id="preview" type="button" name="preview" value="Preview invite">
													<?php } ?>
													<div <?php echo $display; ?> class="error-div unlock">
														<div class="err error-locked">
															You have consumed all 5 invites.Want to refer more? Click to unlock 5 more invites.
															<span  onclick="unlock_form()">Click to unlock</span>
														</div>
													</div>
											<!-- <div class="share">
												<span>Share: </span><img src="media/share-buttons.png">
											</div> -->
										</div>
									</div>
								</form>
							</div>
						<?php //} ?>
						<div class="stats" <?php echo (($referrals_total == 0) ? "style='display:none;'" : "") ?>>
							<div class="stats-heading">Stats</div>
							<div class="stats-data">
								<table>
									<thead>
										<tr>
											<td>Referred</td>
											<td>Enrolled</td>
											<td>Awaiting approval</td>
											<td>Awarded so far</td>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td  id="referral_count"><?php echo $referrals_total ?></td>
											<td><?php echo $enrollments_count ?></td>
											<td><?php echo $reward_pending_confirmation ?></td>
											<td><?php echo $reward_claimed ?></td>
										</tr>
									</tbody>
								</table>
							</div>
							<div class="referral-details">
								Referral Details
							</div>
							<div class="referral-detail-table">
								<table id="referral-details">
									<thead>
										<th>Friend referred</th>
										<th>Friend's name</th>
										<th>Invited on</th>
										<th>Courses recommneded / enrolled</th>
										<th>Coupon code</th>
										<th>Status</th>
									</thead>
									<tbody>
										<?php foreach ($stats as $referrals)
										{ ?>
											<tr>
												<td><?php echo $referrals["email"] ?></td>
												<td><?php echo $referrals["name"] ?></td>
												<td><?php echo $referrals["date"] ?></td>
												<td><?php echo $referrals["courses"] ?></td>
												<td><?php echo $referrals["claim"]["coupon"] ?></td>
												<!-- <td id="<?php //echo $referrals["claim"]["token"] ?>"><?php //echo (isset($referrals["claim"]["token"]) ? "<a href='javascript:void(0);' onclick=\"claim('".$referrals["claim"]["token"]."', '".$referrals["claim"]["user_id"]."', '".urlencode($user["email"])."');\">".$referrals["status"]."</a>" : (isset($referrals["claim"]["no_claim"]) ? "<a href='javascript:void(0);' style='color: grey;cursor: not-allowed;'>Claim reward</a><br><span style='margin-top:5px;font-size: 9px;cursor: not-allowed;'>In fulfillment period</span>" : $referrals["status"])) ?><!-- /td> -->
												<td>
													<?php if (isset($referrals["claim"]["token"]))
													{ ?>
														<a href='javascript:void(0);' onclick="claim('<?php echo $referrals["claim"]["token"] ?>', '<?php echo $referrals["claim"]["user_id"] ?>', '<?php echo urlencode($user["email"]) ?>');"><?php echo $referrals["status"] ?></a>
													<?php }
													else
													{
														if (isset($referrals["claim"]["no_claim"]))
														{ ?>
														<a href='javascript:void(0);' style='color: grey;cursor: not-allowed;'>Claim reward</a>
														<br><span style='margin-top:5px;font-size: 9px;cursor: not-allowed;'>In fulfillment period
														</span>
														<?php }
														else
														{
															echo $referrals["status"];
														}
													} ?>
												</td>
											</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
						</div>
						<div id="terms_cond" class="terms_condition"><span>*</span> Terms and FAQs:</div>
						<div class="note">
							<ul>
								<li>
									<div class="question"> How can I refer my friends? <i class="fa fa-caret-down" aria-hidden="true"></i></div>
									<ul>
										<li>
										Fill up your friend's details and send the invitation email. You can send invites to only 5 friends. We would recommend sending it only to friends who are serious about a career in analytics. We would hate to spam people!
										</li>
									</ul>
								</li>
								<li>
									<div class="question"> When do I get the reward? <i class="fa fa-caret-down" aria-hidden="true"></i></div>
									<ul>
										<li>For every friend who enrols with Jigsaw Academy for the first time, you get an Amazon gift voucher worth INR 1000.</li>
										<li>Your friend should enrol within 30 calendar days of your referral and with the coupon code you shared, so that both of you get the benefit.</li>
										<li>Your friend must be a new student, and cannot be a returning or an existing Jigsaw student.</li>
										<li>You can claim your Amazon voucher, once your friend has purchased the course, completed the payment process and crossed the 7 calendardays money back guarantee period. In case of installment payment, all the installments need to be paid.</li>
									</ul>
								</li>
								<li>
									<div class="question">Why should my friend enroll through my referral? <i class="fa fa-caret-down" aria-hidden="true"></i></div>
									<ul>
										<li>Your friend gets an exclusive discount of an additional 5% only from this referral scheme.</li>
									</ul>
								</li>
								<li>
									<div class="question">How can I know if my friend has enrolled?<i class="fa fa-caret-down" aria-hidden="true"></i></div>
									<ul>
										<li>You can see the statistics for all your invites on the ‘Refer a Friend’ page on your JLC.</li>
										<li>The status of each invite sent through JLC is updated on this screen.</li>
									</ul>
								</li>
								<li>
									<div class="question"> Is there a minimum course purchase requirement for my friend? <i class="fa fa-caret-down" aria-hidden="true"></i></div>
									<ul>
										<li>No, your friend can enroll for any course offered at Jigsaw Academy. You will get an INR 1000 Amazon gift card for his first purchase with Jigsaw, irrespective of the cost of the course.</li>
									</ul>
								</li>
								<li>
									<div class="question">Is the Amazon voucher based on courses? <i class="fa fa-caret-down" aria-hidden="true"></i></div>
									<ul>
										<li> We expect you to suggest only relevant courses to your friends. However, we will understand if your friend chooses to take any other course. Both of you will still get the same benefits.
										</li>
									</ul>
								</li>
								<li>
									<div class="question">Can I get something else in place of the Amazon gift card? <i class="fa fa-caret-down" aria-hidden="true"></i></div>
									<ul>
										<li> Yes, you can exchange your Amazon gift card for a discount coupon on your next course purchase at Jigsaw Academy. Talk to your student counsellor for availing your discount. Referral gift cards are non-transferrable, and cannot be exchanged for cash.
										</li>
									</ul>
								</li>
								<li>
									<div class="question">What does the ‘Status’ mean? <i class="fa fa-caret-down" aria-hidden="true"></i></div>
									<ul>
										<li><b>"X days left | Waiting for action":  </b>Number of days since the invite was sent. And, friend has not subscribed on Jigsaw Academy website yet.
										</li>
										<li><b>"X days left | In progress":  </b>Number of days since the invite was sent. And, your friend has subscribed on Jigsaw Academy website but has not yet enrolled into any course.</li>
										<li><b>"Invite expired":  </b>Your referral invite is valid for 30 days. Referral invite was sent before 30 days and hence has expired.</li>
										<li><b>"Claim reward (In fulfillment period)":  </b>Your friend has enrolled. One or both of you are in the fulfilment period. Fulfillment is considered completed when the complete payment is done and seven days money back period is expired. In case of installment payment, all the installments need to be paid.</li>
										<li><b>"Claim reward":  </b>Your friend has enrolled. You both have passed the fulfilment period. You can now click on this link to claim for your reward.</li>
										<li><b>"Awaiting voucher approval":  </b>Your amazon voucher reward request is received and is in progress. Team will do the required checks before sending out the reward.</li>
										<li><b>"Amazon voucher awarded":  </b>Amazon voucher has been sent to your registered email id.</li>
									</ul>
								</li>

							</ul>
						</div>
					</div>

		<div class="modal-container" id="preview-modal">
			<div class="overlay close"></div>

	  		<div class="modal">
	   			<div class="close"><i class="fa fa-close"></i></div>
		   		<div class="content">
		   			<p>Subject: <?php echo $user["name"] ?> wants to boost your career</p>
				    	<p>Hey <span id="referral-name"></span>,</p>
				    	<p>Your friend <?php echo $user["name"] ?> is learning analytics with us and finds it a great career boost.
				    	 <?php echo get_fname($user["name"]) ?> has recommended the courses, <span id="course-1">Course 1 </span> <span id="and" style="display:none;">and</span> <span id="course-2" style="display:none;">Course 2</span> for you as <span id="grammer">these are </span>suitable for your profile.</p>

				    	<p>Enroll within the next 30 days using this coupon code {COUPON CODE} and get a further discount of 5% on the courses.</p>

				    	<p>Jigsaw Academy, their specializations, student counselors and services are exactly of the standard that will support your career and take it places. If you'd like to explore other courses at Jigsaw, visit <a target="_blank" href='https://www.jigsawacademy.com/online-analytics-training/'>Jigsaw Academy Courses page</a>.</p>

				    	<p>Happy Learning,<br/>
				    	Team Jigsaw
				    	</p>


				    	<div class="invite_button">
				    		<div class="cc_me"><input id="cc-me" type="checkbox" name="cc_me"><span> CC me</span></div>
				    		<div id="loading-image">Sending invite please wait.....</div>
				    		<div class="send_invite"><input id="send-invite" type="button" name="send_invite" value="Send Invite"></div>
				    	</div>
		    		</div>

	  		</div>
		</div>

		<!-- Claim Click Popup -->
		<!-- <div class="modal-container" id="claim-modal">
			<div class="overlay close"></div>
	  		<div class="modal">
	   			<div class="close"><i class="fa fa-close"></i></div>
		   		<div class="content">
		   			<p>Your Amazon Voucher Claim has been requested successfully.</p>
				    	<p>You will recieve you voucher within 2-3 days on your registered email id.</p>
		    		</div>

	  		</div>
		</div> -->

	<script>
		$(document).ready(function(){
			$( document ).tooltip({
			      position: {
			        my: "center bottom-20",
			        at: "center top",
			        using: function( position, feedback ) {
			          $( this ).css( position );
			          $( "<div>" )
			            .addClass( "arrow" )
			            .addClass( feedback.vertical )
			            .addClass( feedback.horizontal )
			            .appendTo( this );
			        }
			      }
    			});

			$("div.modal-container div.overlay.close").click(function(){
				$("body > div.wrapper").removeClass('blur');
				$(this).closest("div.modal-container").removeClass("active");
        			});

        			$("div.modal-container div.close > i.fa").click(function() {
				$("body > div.wrapper").removeClass('blur');
				$(this).closest("div.modal-container").removeClass("active");
       			});
			// Preview invite
			$("#preview").click(function(e) {
				var email = $("#refer-form input[name=friend_email]").val();
				var name = $("#refer-form input[name=friend_name]").val();
				var phone = $("#refer-form input[name=friend_phone]").val();

				var validate = true;

				 // friend name //
			         if ($.trim(name).length == 0) {
			         	$(".error-name").html("Please enter friend's name");
			           	e.preventDefault();
			           	validate = false;
			         }
			         else if(validateName(name)){
			         	$(".error-name").html("");
			         }
			         else{
			         	$(".error-name").html("Please enter a valid name");
			           	e.preventDefault();
			           	validate = false;
			         }

			        if ($.trim(email).length == 0) {
			            $(".error-email").html("Please enter email.");
			            e.preventDefault();
			            validate = false;
			        }
			       else if (validateEmail(email)) {
			       		var email_valid = true;
				       	$.post("<?php echo JAWS_PATH_WEB ?>/lmsapi/jlc.referral.validate", { criterion : "email", text : email }, function(data) {
							var response = $.parseJSON(data);
							if (response.error == 1)
							{
								$(".error-email").html(response.error_desc);
								validate = false;
								email_valid = false;
							}
						});
						if (email_valid)
			            	$(".error-email").html("");
			        }
			        else {
			            $(".error-email").html("Please enter valid email address.");
			            e.preventDefault();
			            validate = false;
			        }

			        // phone nmuber //
			        if ($.trim(phone).length == 0) {
			         $(".error-phone").html("Please enter phone number");
			           e.preventDefault();
			           validate = false;
			        }
			        else if(validatePhone(phone)){
			        	var phone_valid = true;
			        	$.post("<?php echo JAWS_PATH_WEB ?>/lmsapi/jlc.referral.validate", { criterion : "phone", text : phone }, function(data) {
							var response = $.parseJSON(data);
							if (response.error == 1)
							{
								$(".error-phone").html(response.error_desc);
								validate = false;
								phone_valid = false;
							}
						});
						if (phone_valid)
			         		$(".error-phone").html("");
			        }
			        else {
			           $(".error-phone").html("Please enter valid phone number");
			           e.preventDefault();
			           validate = false;
			        }
			          if($(".chosen-select").val() == null)
			        {
			        		$(".error").html("Please select atleast one course");
			            	e.preventDefault();
			            	validate = false;
			        }
			         else if($(".chosen-select :selected").length > 2)
			        {
			        		$(".error").html("You can only select two courses");
			            	e.preventDefault();
			            	validate = false;
			        }
			        else
			        {
			        	$(".error").html('');
			        }
				setTimeout(function () {
					if (validate) {
						$("#referral-name").html($("#refer-form input[name=friend_name]").val());
						var select = $("#refer-form select");
						var i = 1;
						$("option:selected", select).each(function() {
							$("#course-" + i).html("<a target='_blank' href='" + $(this).data("url") + "'>" + $(this).html() + "</a>");
							if (i == 2)
							{
								$("#and").css("display", "");
								$("#course-" + i).css("display", "");
								$("#grammer").html("these are ");
							}
							else
							{
								$("#and").css("display", "none");
								$("#course-" + (i + 1)).css("display", "none");
								$("#grammer").html("this is ");
							}
							i++;
						});
						$("#preview-modal").addClass("active");
						$("body > div.wrapper").addClass('blur');
					}
				}, 1000);
			});

			// disabled-preview click - when referred 5 friends but none of the friends enrolled- in that case it will disable the form
			// $("#disabled-preview").click(function(e) {
			// 	alert("To unlock 5 more invites atleast one of your friend should be enrolled");
			// });

			$("#send-invite").click(function() {
				$("#loading-image").css('display',"inline-block");
				$("#send-invite").attr("disabled","true");
				var arr = {}; arr["referral"] = {};
				arr["referral"]["email"] = $("#refer-form input[name=friend_email]").val();
				arr["referral"]["name"] = $("#refer-form input[name=friend_name]").val();
				arr["referral"]["phone"] = $("#refer-form input[name=friend_phone]").val();
				var select = $("#refer-form select").val();
				arr["referral"]["consult"] = select.join(";");
				if ($("#cc-me").is(":checked")) arr["referral"]["cc"] = "1";
				$.post("<?php echo JAWS_PATH_WEB ?>/lmsapi/jlc.referral.new", { referral : arr["referral"], token : $("#token").val(), email : "<?php echo $user["email"] ?>" }, function (data) {
					response = $.parseJSON(data);
					$("#token").val(response.token);
					$(".stats").css('display','block');
					//console.log(response);
					//alert("Hao chal, ghar jaa");
					$("#loading-image").css('display',"none");
					$("#send-invite").prop("disabled",false);
					$("#preview-modal").removeClass("active");
					$("body > div.wrapper").removeClass('blur');
					var referrer_count = $("#referral_count").html();
					//console.log(referrer_count);
					referrer_count = parseInt(referrer_count)+1;
					if (response.response.n == 1)
					{
						//$("#refer-form").css("display", "none");
						$("#refer-form input[name=friend_email]").attr("disabled","true");
						$("#refer-form input[name=friend_name]").attr("disabled","true");
						$("#refer-form input[name=friend_phone]").attr("disabled","true");
						$("#refer-form select").attr("disabled","true");
						$(".unlock").css('display','block');
						$("#preview").css("background","#ccc","pointer-events","none");
						$("#preview").css("pointer-events","none");
					}

					$("#referral_count").html(referrer_count);
					$('#referral-details tbody').prepend('<tr>'+
										'<td>'+response.response.email+'</td>'+
										'<td>'+response.response.name+'</td>'+
										'<td>'+response.response.date+'</td>'+
										'<td>'+response.response.ref+'</td>'+
										'<td>'+response.response.coupon+'</td>'+
										'<td>'+response.response.claim+'</td>'+
									'</tr>');
					$("#refer-form").trigger('reset');
					var referral_count = parseInt($("#friend-name").data("referral-count")) - 1;
					$("#friend-name").data("referral-count", referral_count);
					$("#friend-name").attr("title", "You have only " + referral_count + " invites to send out")
					$(".chosen-select").trigger('chosen:updated');
				});
			});

			$("div.question").click(function() {
				$("div.question").next().css("display","none");
				$("div.question i").removeClass('fa-caret-up');
				$("div.question i").addClass('fa-caret-down');
				$header = $(this);
				$content = $(this).next();
				$content.slideToggle(100);
				$header.find('i').toggleClass('fa-caret-up fa-caret-down');
			});
		});

		/** function to resend referral mail **/
		function resend_link(id,referral_email, user_resend_token)
		{

			$("#"+id).text('Sendind please wait.....');
			$.post("<?php echo JAWS_PATH_WEB ?>/lmsapi/jlc.referral.resend", {referral_email: referral_email, token: user_resend_token }, function (data) {
				var result = $.parseJSON(data);
				$("#"+id).text('');
				if(result["rc"]==2)
				{
					$("#"+id).html('<span>(Resent invite '+result["rc"]+' times)</span>');
				}
				else if(result["rc"]==1)
				{
					$("#"+id).html('Resend invite <span>(Resent invite '+result["rc"]+' times)</span>');
				}
				// else
				// {
				// 	$("#"+id).html('<span>(Resent '+result["rc"]+' times)</span>');
				// }

				//console.log(result);
			});
		}

		function validateEmail(email)
	 	{
			var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
			if (filter.test(email)) {
			    return true;
			}
			else {
			    return false;
			}
		}

		function validatePhone(txtPhone) {
		    var filter = /([0-9]{10})|(\([0-9]{3}\)\s+[0-9]{3}\-[0-9]{4})/;
		    if (filter.test(txtPhone)) {
		        return true;
		    }
		    else {
		        return false;
		    }
		}

		function validateName(name)
		{
			var filter = /^[a-zA-Z. ]+$/;  // allow only characters, '.' and space // full name
			if (filter.test(name))
				return true
			else
				return false;
		}

		function claim(token, referral_user_id, email)
		{
			$.get("<?php echo JAWS_PATH_WEB ?>/lmsapi/jlc.referral.claim?token="+token+"&referral_user_id="+referral_user_id+"&email="+email, function(data){
				console.log(data);
				data = $.parseJSON(data);
				if (data.success == 1) {
					alert('Your claim request has been received successfully. Your Amazon Voucher is under approval. You will hear from us soon.');
					$("#" + token).html("Awaiting voucher approval");
				}
				else
					alert("Something went wrong!");
			}).fail(function(){
				alert("Something went wrong!");
  // Handle error here
});
		}
		// unlock after every 5 referrals
		function unlock_form()
		{
			$("#refer-form input[name=friend_email]").prop("disabled",false);
			$("#refer-form input[name=friend_name]").prop("disabled",false);
			$("#refer-form input[name=friend_phone]").prop("disabled",false);
			$(".chosen-select").prop("disabled",false).trigger("chosen:updated");
			$(".unlock").css('display','none');
			$("#preview").css("background","#009cd9");
			$("#preview").css("pointer-events","auto");
		}
	</script>
	</body>
</html>