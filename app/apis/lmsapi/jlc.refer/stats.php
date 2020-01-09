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
	load_module("refer");
	load_module("ui");
	load_library("setting");

	function get_fname($name) {

		$tokens = explode(" ", $name);
		$i = 0;
		while ($i < count($tokens)) {

			if (strlen($tokens[$i]) <= 2) {
				$i++;
			}
			else {

				if (ctype_alpha($tokens[$i])) {
					break;
				}
				else {
					$i++;
				}

			}

		}
		if ($i == count($tokens)) {
			$i = 0;
		}

		return $tokens[$i];

	}

	function camel_case($string) {
		return ucwords(str_replace("_", " ", $string));
	}

	function validate_payment_for_claim($user_id) {

		$date = new DateTime("now");
		$paid_flag = false;

		$pay_info = payment_get_info_by_user_id($user_id);
		if ($pay_info === false) {
			return true;
		}

		foreach ($pay_info as $payment) {

			if (strcmp($payment["status"], "paid") != 0) {
				$paid_flag = $paid_flag || false;
			}
			else {

				foreach ($payment["instl"] as $instl) {

					if (strcmp($instl["status"], "paid") != 0) {

						$paid_flag = false;
						break 2;

					}
					else {
						$paid_flag = true;
					}

				}

			}

		}

		if ($paid_flag) {

			// Check if the referrer is still in the 7 days refund period; if yes, the user cannot claim the referral reward
			$subs = db_query("SELECT start_date, combo FROM subs WHERE user_id=".$user_id." ORDER BY start_date DESC LIMIT 1;");
			$start_date = date_create_from_format("Y-m-d H:i:s", $subs[0]["start_date"])->add(new DateInterval("P7D"));
			if ($date < $start_date) {
				$paid_flag = false;
			}
			else {

				$combo = explode(";", $subs[0]["combo"]);
				if (count($combo) == 1) {

					$course_id = explode(",", $combo[0]);
					$course = db_query("SELECT * FROM course WHERE course_id = ".$course_id[0]);
					if (!empty($course[0]["after_sales"])) {

						$course_after_sales = json_decode($course[0]["after_sales"], true);
						if (isset($course_after_sales["jlc"]) && empty($course_after_sales["jlc"])) {
							$paid_flag = false;
						}

					}

				}

			}

		}

		return $paid_flag;

	}

	$user;
	$user_src = "user";

	$privileged_user = 0;

	if (!isset($_REQUEST["token"])) {

		// Init Session
		auth_session_init();

		// Prep
		$login_params["return_url"] = JAWS_PATH_WEB."/refer";

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
		if (!auth_session_is_allowed("refer")) {
			ui_render_msg_front(array(
					"type" => "error",
					"title" => "Jigsaw Academy",
					"header" => "No Tresspassing",
					"text" => "Sorry, but you do not have permission to access this page.<br/>Contact Jigsaw Support team for assistance."
					));
			exit();
		}

		$privileged_user = 1;
		$user = $_SESSION["user"];
		$user_src = "user";

	}
	else {

		$psk_info = psk_info_get($_REQUEST["token"]);
		if ($psk_info["entity_type"] == "user") {
			$user = user_get_by_id($psk_info["entity_id"]);
		}
		else {

			$res_act = db_query("SELECT * FROM system_activity WHERE act_id=".$psk_info["entity_id"]);
			$user = json_decode($res_act[0]["content"], true);
			$user["user_id"] = $res_act[0]["act_id"];
			$user_src = "system_activity";

		}

	}


	// Delete this token // after jlc get token from jaws when refer a friend page is loaded
	psk_expire($user_src, $user["user_id"], "jlc.refer.get");

	// Delete any old referral tokens
	psk_expire($user_src, $user["user_id"], "jlc.refer");

	// Token for the referral submission form
	$token = psk_generate($user_src, $user["user_id"], "jlc.refer");
	$courses_list= array();
	$programs = [
		"programs" => [],
		"specialization" => [],
		"bootcamps" => []
	];
	$idm_only = false;
	$bundles = db_query("SELECT bundle.bundle_id, bundle.name, meta.content, bundle.bundle_type FROM course_bundle AS bundle INNER JOIN course_bundle_meta AS meta ON bundle.bundle_id = meta.bundle_id WHERE bundle.status='enabled' AND (bundle.bundle_type = 'bootcamps' OR bundle.bundle_type='specialization' OR bundle.bundle_type = 'programs')".($idm_only ? " AND meta.category='idm'" : "")." ORDER BY position DESC;");
	foreach ($bundles as $bundle) {

		$content = json_decode($bundle["content"], true);
		// $courses_list[] = array("id" => "b".$bundle["bundle_id"], "name" => $bundle["name"], "url" => $content["url_web"]);
		$programs[$bundle["bundle_type"]][] = array("id" => "b".$bundle["bundle_id"], "name" => $bundle["name"], "url" => $content["url_web"]);

	}
	$programs = array_merge($programs["programs"], $programs["bootcamps"], $programs["specialization"]);

	$courses = db_query("SELECT course.course_id, course.name, meta.content FROM course INNER JOIN course_meta AS meta ON course.course_id = meta.course_id WHERE course.no_show = 0 AND course.status='enabled'".($idm_only ? " AND meta.category='idm'" : "").";");
	foreach ($courses as $course) {

		$content = json_decode($course["content"], true);
		$courses_list[] = array("id" => "c".$course["course_id"], "name" => $course["name"], "url" => $content["url_web"]);

	}

	$nope = 1;

	$date = new DateTime("now");

	$claimable = $user["user_id"] == 18 ? true : validate_payment_for_claim($user["user_id"]);

	$num_invite_expired = 0;
	$num_no_action = 0;
	$num_registerred = 0;
	$num_enrolled = 0;
	$num_claimable = 0;
	$num_claimed = 0;
	$num_awaiting_approval = 0;
	$num_awarded = 0;

	$result = array();
	$referrals_total = 0;
	$referrals = refer_get_by_referrer($user_src, $user["user_id"]);
	if(isset($referrals) && !empty($referrals)) {
		$referrals_total = count($referrals);
	}

	if ($referrals !== false) {

		foreach ($referrals as $referral) {

			$write_back = false;

			if ($referral["status"] == "no_action" || $referral["status"] == "registered" || $referral["status"] == "enrolled") {

				$ref_user = user_get_by_email($referral["email"]);
				if ($ref_user !== false) {

					if ($referral["status"] == "no_action") {

						$referral["status"] = "registered";
						$write_back = true;

					}

					$ref_enr = db_query("SELECT * FROM subs WHERE (status='active' OR status='pending') AND user_id=".$ref_user["user_id"]." ORDER BY start_date ASC;");
					if (isset($ref_enr[0])) {

						if ($referral["status"] == "registered") {

							$referral["status"] = "enrolled";
							$write_back = true;

						}
						$ref_enr = $ref_enr[0];
						$enr_date = date_create_from_format("Y-m-d H:i:s", $ref_enr["start_date"]);
						$referral["enr_date"] = $enr_date;

						//$referral["claimable"] = $claimable;
						if ($claimable && validate_payment_for_claim($ref_user["user_id"])) {

							$referral["status"] = "claim_reward";
							$num_claimable++;
							$referral["claim_token"] = psk_generate("refer", $referral["id"], "jlc.refer.claim");
							$write_back = true;

						}
						$num_enrolled++;

					}
					else {
						$num_registerred++;
					}

				}
				else {

					$create_date = date_create_from_format("Y-m-d H:i:s", $referral["create_date"]);
					if ($date > $create_date->add(new DateInterval("P30D"))) {

						$referral["status"] = "invite_expired";
						$num_invite_expired++;
						$write_back = true;
					}
					else {

						$referral["resend_token"] = psk_generate("refer", $referral["id"], "jlc.refer.resend");
						$referral["days_left"] = $create_date->diff($date)->format("%a");
						$num_no_action++;

					}

				}

			}
			else {

				if ($referral["status"] == "claimed") {

					$num_claimed++;
					$num_enrolled++;

				}
				else if ($referral["status"] == "awaiting_approval") {

					$num_awaiting_approval++;
					$num_enrolled++;

				}
				else if ($referral["status"] == "voucher_awarded") {

					$num_awarded++;
					$num_enrolled++;

				}
				else if ($referral["status"] == "claim_reward") {

					$num_claimable++;
					$referral["claim_token"] = psk_generate("refer", $referral["id"], "jlc.refer.claim");
					$num_enrolled++;

				}
				else if ($referral["status"] == "invite_expired") {
					$num_invite_expired++;
				}

			}

			$consult_arr = array();
			$bundles = str_replace(";", ",", $referral["course_bundles"]);
			if (strlen($bundles) > 0) {

				$bundle = db_query("SELECT name FROM course_bundle WHERE bundle_id IN (".$bundles.");");
				foreach ($bundle as $b) {
					$consult_arr[] = $b['name'];
				}

			}

			$courses = str_replace(";", ",", $referral["courses"]);
			if (strlen($courses) > 0) {

				$courses = db_query("SELECT name FROM course WHERE course_id IN (".$courses.");");
				foreach ($courses as $c) {
					$consult_arr[] = $c['name'];
				}

			}

			$referral["consults"] = implode(", ", $consult_arr);

			$result[] = $referral;
			if ($write_back) {
				refer_edit($referral);
			}

		}

		$resend_max = setting_get("jlc.refer.resend.max_count", 2);

	}

?>
<!DOCTYPE html>
<html lang="en" >
	<head>
		<title>Refer a friend</title>
		<meta name="author" content="Dashboard">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

		<link rel="stylesheet" href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL ?>/common/fa/css/chosen.min.css">
		<link rel="stylesheet" href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL ?>/common/fa/css/font-awesome.min.css">
		<link rel="stylesheet" href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL ?>/common/font-awesome-animation/faa.min.css">
		<link rel="stylesheet/less" type="text/css" href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL."/app/templates/jaws/backend/dashtemp/theme.light.less" ?>" />
		<link rel="stylesheet/less" type="text/css" href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL."/app/templates/jaws/backend/dashtemp/layout.less"; ?>" />

		<script src="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL ?>/common/jquery/jquery-2.2.3.min.js"></script>
		<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.13.0/jquery.validate.js"></script>
		<script src="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL ?>/common/less.js/less.min.js" data-env="development"></script>

		<script src="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL ?>/common/jquery/jquery-ui.js"></script>
		<script src="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL ?>/common/jquery/chosen.jquery.min.js" ></script>

		<link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
		<script>
			$(document).ready(function() {
				$(".chosen-select").chosen({
					width: "100%",
					max_selected_options: <?php echo ($idm_only ? "1" : "2") ?>
				});
				$(".chosen-select").bind("chosen:maxselected", function() {
					$(".error1").html('You can only select two courses');
				});
			});
		</script>
	</head>
	<body data-tourian="false" >
		<div class="section-boxed">
			<div class="refer-heading">
				<!--HAVE AN <span>"AMAZ-ON"</span> HOLIDAY WITH GREAT GIVEAWAYS!-->
				<strong> AN <span>AMAZON</span> TREAT AWAITS YOU</strong><p></p>
				<!--FIND YOUR STUDY BUDDY-->
			</div>
			<div class="tagline">
				<?php if($idm_only){ ?>
					<div class="tag-1">Special offer for you!</div>
					<div class="tag-2">Get an Amazon <span><a href="#terms_cond">*</a></span>voucher of  <i class="fa fa-inr" aria-hidden="true"></i> 25,000 for every friend that enrolls in IDM program<br/> (Integrated Program in Data Science and Machine Learning)<br/><b>Course fees: <i class="fa fa-inr" aria-hidden="true"></i> 3,35,000 + taxes ( = <i class="fa fa-inr" aria-hidden="true"></i> 3,85,250)</b></div>
					<div class="banner" style="margin:10px 0 10px 0;"><a target="_blank" href="https://www.jigsawacademy.com/idm/"><img src="<?php echo JAWS_PATH_WEB ?>/media/jaws/frontend/images/FB_Amazon_1000X130px.png" /></a></div>
				<?php } else { ?>
						<div class="tag-1" style="font-size:18px;">Introduce friends to Jigsaw Academy. Get them enrolled. Get rewarded.</div>
						<!-- <div class="tag-2">Enroll 2 friends by January 31st, 2018 and get an Amazon<span><a href="#terms_cond">*</a></span> voucher for  <i class="fa fa-inr" aria-hidden="true"></i>3000 and Jigsaw Goody. -->
						<!--<div class="tag-2">Get an Amazon<span><a href="#terms_cond">*</a></span> voucher of  <i class="fa fa-inr" aria-hidden="true"></i> <b>1000</b> for every friend that enrolls. -->

						<div class="tag-2" style="font-size:18px;">Get an Amazon<span><a href="#terms_cond">*</a></span> voucher upto  <i class="fa fa-inr" aria-hidden="true"></i> <b>18,000</b> for every friend that enrolls.

						<!--5000 for Three friends that enrolls in SEPTEMBER 2017 </div>-->

						<!--<div class="tag-2">Get an Amazon<span><a href="#terms_cond">*</a></span> voucher of  <i class="fa fa-inr" aria-hidden="true"></i> 1500 for every Women friend that enrolls in March 2018.</div>-->

						<!-- <div class="banner" style="margin:10px 0 10px 0;"><img src="<?php echo JAWS_PATH_WEB ?>/media/jaws/frontend/images/refer-friend_JLC-140x1000-2.png" /></a></div> -->

						<p></p>
						<div class="banner" style="margin:10px 0 10px 0;"><img src="<?php echo JAWS_PATH_WEB ?>/media/jaws/frontend/images/referral-process-top-image.png" /></a></div>
						<!--<div class="banner" style="margin:10px 0 10px 0;"><img src="<?php echo JAWS_PATH_WEB ?>/media/jaws/frontend/images/Womens-day--friend_1000-w-x-130-h.png" /></a></div>-->
				<?php } ?>
			</div>

			<?php if (count($result) == 0 || (count($result) % 5) != 0) {

				$disabled = "";
				$display = 'style="display:none"';

			}
			else {

				$disabled = "disabled";
				$display = 'style="display:block"';

			} ?>
			<div class="refer-form" align="center">
				<form id="refer-form" method="post" action="" >
					<div class="form-inputs">
						<div class="refer-input">
							<input <?php echo $disabled; ?> id="friend-name" required="true" title="You have only <?php echo 5 - $referrals_total % 5 ?> invites to send out" type="text" name="friend_name" placeholder="Friend's name" data-referral-count="<?php echo 5 - $referrals_total % 5 ?>">
						</div>

						<div class="refer-input">
							<!-- <div class="error"></div> -->
							<input <?php echo $disabled; ?> id="friend_email"  required="true" type="email" name="friend_email" placeholder="Email address">
						</div>

						<div class="refer-input">
							<!-- <div class="error"></div> -->
							<input <?php echo $disabled; ?>  type="text" name="friend_phone" id="friend_phone" placeholder="Mobile number" >

						</div>

						<input type="hidden" name="token" id="token" value="<?php echo $token ?>">

						<div class="refer-input">
							<div class="err error1"></div>
							<select <?php echo $disabled; ?> name="recommended_course" id="recommended_course" data-placeholder="Select courses" class="chosen-select" multiple="multiselect" >
								<optgroup label="Programs"></optgroup>
								<?php foreach ($programs as $course) { ?>
									<option value="<?php echo $course["id"] ?>" data-url="<?php echo $course["url"] ?>"><?php echo $course["name"] ?></option>
								<?php } ?>
								<optgroup label="Courses"></optgroup>
								<?php foreach ($courses_list as $course) { ?>
									<option value="<?php echo $course["id"] ?>" data-url="<?php echo $course["url"] ?>"><?php echo $course["name"] ?></option>
								<?php } ?>
							</select>

						</div>
						<div class="preview">
							<?php  if (count($result) == 0 || (count($result) % 5) != 0) { ?>
								<input id="preview"  type="submit" name="preview" value="Preview invite" >
							<?php }
							else { ?>
								<input id="preview" style="background: #ccc; pointer-events: none;"  type="submit" name="preview" value="Preview invite" >
							<?php } ?>

							<div <?php echo $display; ?> class="error-div unlock">
								<div class="err error-locked">
									You have consumed all 5 invites. Want to refer more? Click to unlock 5 more invites.
									<span  onclick="unlock_form()">Click to unlock</span>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>

			<div class="stats" <?php echo ((count($result) == 0) ? "style='display:none;'" : "") ?>>
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
								<td  id="referral_count"><?php echo count($result) ?></td>
								<td><?php echo $num_enrolled ?></td>
								<td><?php echo $num_awaiting_approval ?></td>
								<td><?php echo $num_awarded ?></td>
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
							<?php foreach ($result as $referral)
								{ ?>
									<tr>
									<td><?php echo $referral["email"] ?></td>
									<td><?php echo $referral["name"] ?></td>
									<td><?php echo date_create_from_format("Y-m-d H:i:s", $referral["create_date"])->format("d-M-Y"); ?></td>
									<td><?php echo $referral["consults"] ?></td>
									<td><?php echo $referral["coupon_code"] ?></td>
									<td id="ref_<?php echo $referral["id"] ?>">
										<?php if ($referral["status"] == "claim_reward") { ?>
											<a href='javascript:void(0);' onclick="claim('<?php echo $referral["claim_token"] ?>');"><?php echo camel_case($referral["status"]) ?></a>
										<?php }
										else {

											if ($referral["status"] == "enrolled") { ?>
												<a href='javascript:void(0);' style='color: grey;cursor: not-allowed;'>Claim reward</a><br><span style='margin-top:5px;font-size: 9px;cursor: not-allowed;'>In fulfillment period</span>
											<?php }
											else {
												echo camel_case($referral["status"]);

												if ($referral["status"] == "no_action") {

													if ($referral["resent_count"] == 0) { ?>
														<br/><a id="<?php echo $referral['id'] ?>" onclick="resend_link('<?php echo $referral["resend_token"] ?>')" class="resend_link">Resend invite</a> -->
													<?php }
													else if ($referral["resent_count"] < $resend_max) { ?>
														<br/><a id="<?php echo $referral["id"] ?>" onclick="resend_link('<?php echo $referral["resend_token"] ?>')" class="resend_link">Resend invite <span>(Resent invite --> <?php// echo $referral["resent_count"]; ?> <!-- times)</span></a> -->
													 <?php }
													else { ?>
														<br/><a class="resend_link"> <span>(Resent invite <?php echo $referral["resent_count"]; ?> times)</span></a> -->
													<?php }

												}
												else if ($referral["status"] == "invite_expired" && $referral["resent_count"] > 0) { ?>
													<br/><a class="resend_link"> <span>(Resent invite <?php echo $referral["resent_count"]; ?> times)</span></a> -->
												<?php }

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
				<?php if ($idm_only) { ?>
				<ul>
					<li>
						<div class="question"> How can I refer my friends? <i class="fa fa-caret-down" aria-hidden="true"></i></div>
						<ul>
							<li>Fill up your friend's details and send the invitation email. You can send invites to only 5 friends. We would recommend sending it only to friends who are serious about a career in analytics. We would hate to spam people!</li>
						</ul>
					</li>
					<li>
						<div class="question"> When do I get the reward? <i class="fa fa-caret-down" aria-hidden="true"></i></div>
						<ul>
							<li>For every friend who enrols for the IDM course, you get an Amazon gift voucher worth INR 25,000.</li>
							<li>There is no upper limit to the number of friends who can join. 4 friends can make you a Lakhpati!</li>
							<li>Your friend should enrol within 30th June 2017 so that both of you get the benefit.</li>
							<li>Your friend must not have already expressed interest in the IDM program.</li>
							<li>You can claim your Amazon voucher, once your friend has purchased the course, completed the payment process and crossed the 7 calendar days money back guarantee period.</li>
						</ul>
					</li>
					<li>
						<div class="question">Why should my friend enroll through my referral? <i class="fa fa-caret-down" aria-hidden="true"></i></div>
						<ul>
							<li>Your friend gets an exclusive discount of an additional 10% only from this referral scheme.</li>
						</ul>
					</li>
					<li>
						<div class="question">How can I know if my friend has enrolled?<i class="fa fa-caret-down" aria-hidden="true"></i></div>
						<ul>
							<li>You can see the statistics for all your invites on the ‘Refer a Friend’ page on your JLC.</li>
							<li>The status of each invite sent through JLC is updated on this screen.</li>
						</ul>
					</li>
					<!-- <li>
						<div class="question"> Is there a minimum course purchase requirement for my friend? <i class="fa fa-caret-down" aria-hidden="true"></i></div>
						<ul>
							<li>No, your friend can enroll for any course offered at Jigsaw Academy. You will get an INR 25000 Amazon gift card for his first purchase with Jigsaw, irrespective of the cost of the course.</li>
						</ul>
					</li> -->
					<li>
						<div class="question">Is the Amazon voucher based on courses? <i class="fa fa-caret-down" aria-hidden="true"></i></div>
						<ul>
							<li>Yes, when your friend enrolls for IDM program, you get Amazon voucher worth INR 25000 and you friend gets 10% discount on IDM course purchase. Whereas if your friend enrolls into any other course/s then you get the regular referral benifit of INR 1000 and you friend gets an exclusive discount of 5% on his first payment value.
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
							<li><b>"Invite expired":  </b>Your referral invite is valid till 30th June 2017. Referral invite was sent before 30th June 2017 and hence has expired.</li>
							<li><b>"Claim reward (In fulfillment period)":  </b>Your friend has enrolled. One or both of you are in the fulfilment period. Fulfillment is considered completed when the complete payment is done and seven days money back period is expired. In case of installment payment, all the installments need to be paid.</li>
							<li><b>"Claim reward":  </b>Your friend has enrolled. You both have passed the fulfilment period. You can now click on this link to claim for your reward.</li>
							<li><b>"Awaiting voucher approval":  </b>Your amazon voucher reward request is received and is in progress. Team will do the required checks before sending out the reward.</li>
							<li><b>"Amazon voucher awarded":  </b>Amazon voucher has been sent to your registered email id.</li>
						</ul>
					</li>

				</ul>
			<?php } else { ?>
				<ul>
					<li>
						<div class="question"> How can I refer my friends? <i class="fa fa-caret-down" aria-hidden="true"></i></div>
						<ul>
							<li>Fill up your friend's details, recommend appropriate course/s by selecting it from the dropdown list in the form and send the invitation email. </li>
							<li>You can send invites to only 5 friends. We would recommend sending it only to friends who are serious about a career in analytics. We would hate to spam people!
							</li>
						</ul>
					</li>
					<li>
						<div class="question"> When and how much reward do I get? <i class="fa fa-caret-down" aria-hidden="true"></i></div>
						<ul>
							<!-- <li>For every friend who enrols with Jigsaw Academy for the first time, you get an Amazon gift voucher worth INR 1000.</li> -->
							<!--<li>If 2 of your friends had enrolled from 5th December 2017 to 31st January 2018 then you get additional amazon voucher worth INR 1000 and a merchandized goody from Jigsaw.</li>-->
							<!--<li>We are supporting and encouraging women who want to choose analytics as their career of choice. For more info <a target="_blank" href="https://www.jigsawacademy.com/jigsaw_offers_indian/">click here</a>. When your women friend enrolls in the month of March, you get INR 1500 amazon voucher. However, the reward remains the same i.e. INR 1000 for a male friend getting enrolled. This offer is valid for the enrollments happening only in the month of March 2018.</li>-->
							<!--<li>Women's special offer for March 2018 is expired. If your women friend was invited and enrolled in the month of March 2018 then you can get an Amazon voucher worth INR 1500. However, the reward remains the same i.e. INR 1000 for a male friend getting enrolled.
							</li>-->
							<li>For every friend who enrols with Jigsaw Academy for the first time, you can get an Amazon gift voucher worth upto <b>INR 18000</b>.</li>
							<!--<li>For every friend who enrols with Jigsaw Academy for the first time, you can get an Amazon gift voucher worth upto <b>INR 20,000</b>.</li>-->
							<li>Your friend can enrol for any course offered at Jigsaw Academy. The reward amount will be 5% of the course fees received (not inclusive of GST) from the referred student after any applied discounts / scholarships / fee waivers. The instalment charges will also not be included in the fees for the calculation of referral amount.</li>
							<!--<li><b>Limited period offer: If your friend enrolls in PGPDM course, you get flat INR 20,000 Amazon Voucher.</b></li>-->
							<li>The offer as communicated by the sales team will be final.</li>
							<!--<li>If 3 of your friends enroll within a month then you get additional amazon voucher worth INR 2000 and a merchandized goody from Jigsaw.</li>
							<li>If your friends enroll in PGPDM program then you get an amazon voucher worth INR 16000. Contact pgpdm@jigsawacademy.com for more information. Your friend should enrol within 14 calendar days of your referral so that you get the benefit.</li>
							<li>There is no other offer available.</li>-->
							<li>For PGPDM and IPBA Programs, your friend should enrol within 14 calendar days of your referral so that you get the benefit.</li>
							<li>This offer cannot be combined with any other offer.</li>
							<li>For any course other than PGPDM and IPBA, your friend should enrol within 30 calendar days of your referral and with the coupon code you shared, so that both of you get the benefit.</li>
							<li>Your friend must be a new lead / student. He / she should not be an existing student or lead with us.</li>
							<li>You can claim your Amazon voucher, once your friend has purchased the course, completed the payment process and crossed the 7 calendardays money back guarantee period. In case of installment payment, all the installments need to be paid.</li>
							<li>Referral reward can be given as an Amazon voucher or an adjustment in the final instalment of your fees due subject to management decision.</li>

						</ul>
					</li>
					<li>
						<div class="question">Why should my friend enroll through my referral? <i class="fa fa-caret-down" aria-hidden="true"></i></div>
						<ul>
							<!--<li>Your friend gets an exclusive discount of an additional 5% only from this referral scheme. </li>-->
							<li>Your friend may get an exclusive discount from this referral scheme. </li>
							<!--<li>For March 2018, your women friend can avail total 20% discount on any course they enroll into. For more info <a target="_blank" href="https://www.jigsawacademy.com/jigsaw_offers_indian/">click here</a>.</li>-->
							<li>This discount is not applicable if your friend enrolls into PGPDM or IPBA program.</li>
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
							<li>No, your friend can enroll for any course offered at Jigsaw Academy. You will get an Amazon gift card for his first purchase with Jigsaw, irrespective of the cost of the course.</li>
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
							<li> Yes, you can exchange your Amazon gift card for a discount coupon on your next course purchase at Jigsaw Academy. Talk to your student counsellor for availing your discount. Exchange decision needs to be confirmed before you claim for your reward. Referral gift cards are non-transferrable, and cannot be exchanged for cash.
							</li>
						</ul>
					</li>
					<li>
						<div class="question">What does the ‘Status’ mean? <i class="fa fa-caret-down" aria-hidden="true"></i></div>
						<ul>
							<!--<li><b>"X days left | Waiting for action":  </b>Number of days since the invite was sent. And, friend has not subscribed on Jigsaw Academy website yet.
							</li>
							<li><b>"X days left | In progress":  </b>Number of days since the invite was sent. And, your friend has subscribed on Jigsaw Academy website but has not yet enrolled into any course.</li>-->
							<li><b>"No Action":  </b>Your friend has not subscribed on Jigsaw Academy website yet.</li>
							<li><b>"Registered":  </b>Your friend has signed-in on Jigsaw Academy website but has not yet enrolled into any course.</li>
							<li><b>"Invite expired":  </b>Your referral invite is valid for 30 days. Referral invite was sent before 30 days and hence has expired.</li>
							<li><b>"Claim reward (In fulfillment period)":  </b>Your friend has enrolled. One or both of you are in the fulfilment period. Fulfillment is considered completed when the complete payment is done and seven days money back period is expired. In case of installment payment, all the installments need to be paid.</li>
							<li><b>"Claim reward":  </b>Your friend has enrolled. You both have passed the fulfilment period. You can now click on this link to claim for your reward.</li>
							<li><b>"Awaiting approval":  </b>Your amazon voucher reward request is received and is in progress. Team will do the required checks before sending out the reward.</li>
							<li><b>"Voucher awarded":  </b>Amazon voucher has been sent to your registered email id.</li>
							<li><b>"Voucher rejected": </b>Your voucher claim is rejected by the management during the verification process. Possible reasons could be because your referral already existed in our system. Management reserves the rights to accept or reject the claim request. For more information please contact support@jigsawacademy.com.</li>
						</ul>
					</li>
					<li><div class="question">I am a corporate students, can I refer my friends?<i class="fa fa-caret-down" aria-hidden="true"></i></div>
						<ul>
							<li>Referral program for corporates will be handled separately. Please contact us at support@jigsawacademy.com for more details.
							</li>
						</ul>
					</li>
				</ul>
			<?php } ?>
			</div>
		</div>

		<div class="modal-container" id="preview-modal">
			<div class="overlay close"></div>

	  		<div class="modal">
	   			<div class="close"><i class="fa fa-close"></i></div>
		   		<?php if($idm_only){ ?>
		   			<div class="content">
			   			<p>Subject: <?php echo $user["name"] ?> wants to boost your career</p>
					    	<p>Dear <span id="referral-name"></span>,</p>
					    	<p>Your friend <?php echo $user["name"] ?> is learning analytics with us and recommends a great course to give your career a boost. <?php echo get_fname($user["name"]) ?>
					    	  believes that the <a target="_blank" href="https://www.jigsawacademy.com/idm/">Integrated Program in Data Science and Machine Learning (IDM)</a> by the University of Chicago Graham School and Jigsaw Academy is ideal for you!</p>

					    	<p>Enclosed is the brochure and schedule for your reference.</p>

					    	<p>The IDM focuses on advanced data science and machine learning with R, Big Data with Spark, as well as project management, visualization and the storytelling with data. The program is a hybrid course (in-person and online) with 96 hours (12 days) on in-person classes in hotel Hilton Bangalore, 26 online sessions as well as over 100 hours of pre-recorded content during a period of 9 months. The faculty will be a mix of lecturers from University of Chicago, industry experts and Jigsaw faculty</p>

					    	<p><b>Enrol within June 2017 and use coupon code {COUPON CODE} to get a 10% discount on the course fees. </b></p>

					    	<p>Best Wishes<br/>
					    	IDM Admissions <br/>
					    	Website: <a href="https://www.jigsawacademy.com/idm">https://www.jigsawacademy.com/idm</a>
					    	</p>
					    	<p>Contact: +91 90199 87000 (10AM - 6PM, Monday to Saturday)</p>

					    	<div class="invite_button">
					    		<div class="cc_me"><input id="cc-me" type="checkbox" name="cc_me"><span> CC me</span></div>
					    		<div id="loading-image">Sending invite please wait.....</div>
					    		<div class="send_invite"><input id="send-invite" type="button" name="send_invite" value="Send Invite"></div>
					    	</div>
		    			</div>
	  			<?php }else{ ?>
	  					<div class="content">
			   			<p>Subject: <?php echo $user["name"] ?> wants to boost your career</p>
					    	<p>Hey <span id="referral-name"></span>,</p>
					    	<p>Your friend <?php echo $user["name"] ?> is learning analytics with us and finds it a great career boost. <?php echo get_fname($user["name"]) ?>
					    	 has recommended the courses,<span id="course-1">Course 1 </span> <span id="and" style="display:none;">and</span> <span id="course-2" style="display:none;">Course 2</span> for you as <span id="grammer">these are </span>suitable for your profile.</p>

					    	<p id="enroll-line">Enroll within the next 30 days using this coupon code {COUPON CODE} and get a further discount of 5% on the courses.</p>

					    	<p id="desc-line">Jigsaw Academy, their specializations, student counselors and services are exactly of the standard that will support your career and take it places. If you'd like to explore other courses at Jigsaw, visit <a target="_blank" href='https://www.jigsawacademy.com/online-analytics-training/'>Jigsaw Academy Courses page</a>.</p>

					    	<p>Happy Learning,<br/>
					    	Team Jigsaw
					    	</p>

					    	<div class="invite_button">
					    		<div class="cc_me"><input id="cc-me" type="checkbox" name="cc_me"><span> CC me</span></div>
					    		<div id="loading-image">Sending invite please wait.....</div>
					    		<div class="send_invite"><input id="send-invite" type="button" name="send_invite" value="Send Invite"></div>
					    	</div>
		    			</div>
	  			<?php } ?>
	  		</div>
		</div>
<script>
	$(document).ready(function () {

		/** ----------- tooltip --------- **/
		$(document).tooltip( {
			position: {
				my: "center bottom-20",
				at: "center top",
				using: function(position, feedback) {
					$(this).css(position);
					$("<div>")
					.addClass("arrow")
					.addClass(feedback.vertical)
					.addClass(feedback.horizontal)
					.appendTo(this);
				}
			}
		});
		/** ----------- tooltip  ends--------- **/

		$("div.modal-container div.overlay.close").click(function(){
			$("body > div.wrapper").removeClass('blur');
			$(this).closest("div.modal-container").removeClass("active");
        });

        $("div.modal-container div.close > i.fa").click(function() {
			$("body > div.wrapper").removeClass('blur');
			$(this).closest("div.modal-container").removeClass("active");
       	});

		/** added method to check name only contains spaces and alphabets **/
		$.validator.addMethod("lettersonly", function(value, element) {
			return this.optional(element) || /^[a-z ]+$/i.test(value);
		}, "Letters only please");

		/** Added to check if email id is registered or referred **/
		var message=''; result = false;
		$.validator.addMethod('emailalreadyexists', function(value, element) {
			$.ajax({
				url: "<?php echo JAWS_PATH_WEB ?>/lmsapi/jlc.referral.validate",
				type: "post",
				data: {
					criterion: 'email',
					text: function() {
						return $( "#friend_email" ).val();
					}
				},
				success: function(data){
					data = JSON.parse(data);
					if (data.error === 0) {
						result = false;
						message = data.error_desc;
					}
					else {
						result = true;
					}
				}
			});
			$.validator.messages["emailalreadyexists"] = message;
			return result;
		}, '' );

		/** Added to check if phone number is already registered or referred **/
		var msg= ''; res = false;
		$.validator.addMethod('numberalreadyexist', function(value, element) {
			$.ajax({
				url: "<?php echo JAWS_PATH_WEB ?>/lmsapi/jlc.referral.validate",
				type: "post",
				data: {
					criterion: 'phone',
					text: function() {
						return $( "#friend_phone" ).val();
					}
				},
				success: function(data){
					data = JSON.parse(data);
					if (data.error === 0) {
						res = false;
						msg = data.error_desc;
					}
					else {
						res = true;
					}
				}
			});
			$.validator.messages["numberalreadyexist"] = msg;
			return res;
		}, '' );

		$("#refer-form").validate({
			rules: {
				friend_name: {
					required: true,
					lettersonly: true
				},
				friend_email: {
					required: true,
					email: true,
					emailalreadyexists: true
				},
				friend_phone: {
					required: true,
					number: true,
					minlength: 10,
					maxlength: 10,
					numberalreadyexist: true
				},
				recommended_course: {
					 required: true,
				}
			},
			ignore: ':hidden:not("#recommended_course")',
			//onkeyup: false,
			messages:{
		 		friend_name: {
					required: "Name is required",
					lettersonly: "Letters and spaces only"
				},
				friend_email:{
					required:"Email is required",
					email: "Please enter a valid email address",
				},
				friend_phone: {
					required: "Phone number is required",
					number: "Please enter valid phone",
					minlength: "Please enter 10 digit phone number",
					maxlength: "Please enter 10 digit phone number"
				},
				recommended_course: {
					required: "Please select atleast one course",
					//needsSelection:  "Please select atleast one course",
				}
			},
			errorPlacement: function(error, element) {
				error.insertBefore(element);
			},
			submitHandler: function (form) { // for demo
				$(".error1").html("");
				$("#referral-name").html($("#refer-form input[name=friend_name]").val());
				var select = $("#refer-form select");
				var i = 1;
				var pgpdm = false;
				$("option:selected", select).each(function() {
					if ((this).value == "b75") {
						$("#enroll-line").html("Enroll within the next 14 days using this coupon code {COUPUN CODE} as the seats are limited and admissions are closing.");
						$("#desc-line").html("Please email us at pgpdm@jigsawacademy.com and suggest a suitable time for a counsellor to call you and explain the course details.");
					}
					$("#course-" + i).html("<a target='_blank' href='" + $(this).data("url") + "'>" + $(this).html() + "</a>");
					if (i == 2) {
						$("#and").css("display", "");
						$("#course-" + i).css("display", "");
						$("#grammer").html("these are ");
					}
					else {
						$("#and").css("display", "none");
						$("#course-" + (i + 1)).css("display", "none");
						$("#grammer").html("this is ");
					}
					i++;
				});
				$("#preview-modal").addClass("active");
				$("body > div.wrapper").addClass('blur');
			}
		});

   		$("#send-invite").click(function() {
			$("#loading-image").css('display',"inline-block");
			$("#send-invite").attr("disabled","true");
			var arr = {}; arr["referral"] = {};
			arr["referral"]["email"] = $("#refer-form input[name=friend_email]").val();
			arr["referral"]["name"] = $("#refer-form input[name=friend_name]").val();
			arr["referral"]["phone"] = $("#refer-form input[name=friend_phone]").val();
			var select = $("#refer-form select").val();
			arr["referral"]["consult"] = select.join(";");
			if ($("#cc-me").is(":checked")) {
				arr["referral"]["cc"] = "1";
			}
			$.post("<?php echo JAWS_PATH_WEB ?>/lmsapi/jlc.refer/new", { referral : arr["referral"], token : $("#token").val(), idm_only : <?php echo ($idm_only ? "1" : "0") ?>, privileged_user : <?php echo $privileged_user ?> }, function (data) {
				response = JSON.parse(data);
				$("#token").val(response.token);
				$(".stats").css('display','block');
				$("#loading-image").css('display',"none");
				$("#send-invite").prop("disabled",false);
				$("#preview-modal").removeClass("active");
				$("body > div.wrapper").removeClass('blur');
				var referrer_count = $("#referral_count").html();
				referrer_count = parseInt(referrer_count) + 1;
				if (response.response.n == 1) {
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
					'</tr>'
				);
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

	function claim(token) {
		$.post("<?php echo JAWS_PATH_WEB ?>/lmsapi/jlc.refer/claim", { token: token} , function(data){
			data = $.parseJSON(data);
			if (data.success == 1) {
				alert('Your claim request has been received successfully. Your Amazon Voucher is under approval. You will hear from us soon.');
				$("#ref_"+data.id).html("Awaiting Approval");
			}
			else {
				alert("Something went wrong!");
			}
		});
	}

	/** function to resend referral mail **/
	function resend_link(user_resend_token) {
		$.post("<?php echo JAWS_PATH_WEB ?>/lmsapi/jlc.refer/resend", { token: user_resend_token }, function (data) {
			var result = $.parseJSON(data);
			if(result["rc"]==2) {
				$("#"+result['id']).text('');
				$("#"+result['id']).html('<span>(Resent invite '+result["rc"]+' times)</span>');
			}
			else if(result["rc"]==1) {
				$("#"+result['id']).text('');
				$("#"+result['id']).html('Resend invite <span>(Resent invite '+result["rc"]+' times)</span>');
			}
		});
	}

	// unlock after every 5 referrals
	function unlock_form() {
		$("#refer-form input[name=friend_email]").prop("disabled",false);
		$("#refer-form input[name=friend_name]").prop("disabled",false);
		$("#refer-form input[name=friend_phone]").prop("disabled",false);
		$(".chosen-select").prop("disabled",false).trigger("chosen:updated");
		$(".unlock").css('display','none');
		$("#preview").css("background","#009cd9");
		$("#preview").css("pointer-events","auto");
	}
	</script>
	<style>.refer-input select + .chosen-container-multi ul li input.default { width: auto !important; }</style>
</body>
</html>