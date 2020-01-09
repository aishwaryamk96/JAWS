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

// load_module("user");
// load_module("subs");

// $data = db_query("SELECT * FROM system_activity where act_type='jlc.referral' ");
// $i = 0;$arr = array();
// $query = "INSERT INTO refer (referrer_type, referrer_id, name, email, phone, referral_id, create_date, coupon_code, courses, course_bundles, status, claim_date, voucher_awarded_date, resent_count) VALUES ";
// $insert = [];
// $date = new DateTime("now");
// foreach($data as $key=>$value)
// {
// 	$userId =  $value['context_id'];
// 	$userType =  $value['context_type'];
// 	$content = json_decode($value['content'], true);

// 	foreach($content['r'] as $val)
// 	{	
// 		$claim_date = "NULL";
// 		$voucher_date = "NULL";
// 		//echo $userId ;
// 		$arr[$i]['referral_id'] = $userId;
// 		$arr[$i]['referral_type'] = $userType;
// 		$arr[$i]['name'] = $val['n'];
// 		$arr[$i]['email'] = $val['e'];
// 		$arr[$i]['phone'] = $val['p'];
// 		$arr[$i]['create_date'] = $val['d'];
// 		$arr[$i]['coupon_code'] = $val['cc'];
// 		$ref_id = "NULL";
// 		$status = "no_action";
// 		if (($user = user_get_by_email($val["e"])) !== false) {

// 			$ref_id = $user["user_id"];
		
// 			$status = "registered";

// 			if (subs_get_info_by_user_id($user["user_id"]) !== false) {
// 				$status = "enrolled";
// 			}
		
// 		}
// 		if (!empty($val["x"])) {
		
// 			if ($val["x"] == "-1") {
// 				$status = "invite_expired";
// 			}
// 			else if ($val["x"] == "1") {
// 				$status = "awaiting_approval";
// 				$claim_date = $date->format("Y-m-d H:i:s");
// 			}
// 			else if ($val["x"] == "2") {
// 				$status = "voucher_awarded";
// 				$claim_date = db_sanitize($date->format("Y-m-d H:i:s"));
// 				$voucher_date = db_sanitize($date->format("Y-m-d H:i:s"));
// 			}
// 			else if ($val["x"] == "0") {
// 				$status = "claim_reward";
// 				$claim_date = db_sanitize($date->format("Y-m-d H:i:s"));
// 			}
		
// 		}
// 		$arr[$i]['status'] = $status;
// 		$arr[$i]["x"] = $val["x"];

// 		$courses = [];
// 		$bundles = [];
// 		foreach($val['c']  as $course)
// 		{
// 			if (substr($course, 0, 1) == "c")
// 			{
// 				$courses[] = substr($course, 1);
// 			}
// 			else
// 			{
// 				$bundles[]= substr($course, 1);
// 			}
// 		}
// 		if (empty($val["rc"])) {
// 			$val["rc"] = "0";
// 		}

// 		$courses = implode(';', $courses);
// 		$bundles = implode(';', $bundles);
// 		$i++;
// 		$insert[] = "(".db_sanitize($userType).",".$userId.",".db_sanitize($val['n']).",".db_sanitize($val['e']).",".db_sanitize($val['p']).",".$ref_id.",".db_sanitize($val['d']).",".db_sanitize($val['cc']).",".db_sanitize($courses).",".db_sanitize($bundles).",".db_sanitize($status).",".$claim_date.",".$voucher_date.",".$val["rc"].")";
// 	}
// }

// $query .= implode(",", $insert);

// echo $query;

// echo "<pre>"; print_r($arr); die;

// Prevent exclusive access
	if (!defined("JAWS")) {
		header('Location: https://www.jigsawacademy.com');
		die();
	}
	// if (!auth_api("jlc.refer")) {
	// 	die ("You do not have required priviledges to use this feature.");
	// }

	load_module("user");
	load_module("subs");
	//load_module("leads");
	load_module("refer");
	load_library("setting");
	//load_module("subs");

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

		$pay_info = payment_get_info_by_user_id($user_id);
		if ($pay_info === false) {
			return true;
		}

		foreach ($pay_info as $payment) {

			if (strcmp($payment["status"], "paid") != 0) {
				return false;
			}
			else {

				foreach ($payment["instl"] as $instl) {
					if (strcmp($instl["status"], "paid") != 0) {
						return false;
					}
				}

			}

		}
		// Check if the referrer is still in the 7 days refund period; if yes, the user cannot claim the referral reward
		$subs = db_query("SELECT start_date FROM subs WHERE user_id=".$user_id." ORDER BY start_date DESC LIMIT 1;");
		$start_date = date_create_from_format("Y-m-d H:i:s", $subs[0]["start_date"])->add(new DateInterval("P7D"));
		if ($date < $start_date) {
			return false;
		}

		return true;

	}

	$user;
	$user_src = "user";

	if (!isset($_REQUEST["token"])) {
		$user = user_get_by_id(18);
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

	$bundles = db_query("SELECT bundle.bundle_id, bundle.name, meta.content FROM course_bundle AS bundle INNER JOIN course_bundle_meta AS meta ON bundle.bundle_id = meta.bundle_id WHERE bundle.status='enabled' AND bundle.bundle_type='specialization';");
	foreach ($bundles as $bundle) {

		$content = json_decode($bundle["content"], true);
		$courses_list[] = array("id" => "b".$bundle["bundle_id"], "name" => $bundle["name"], "url" => $content["url_web"]);

	}
	$courses = db_query("SELECT course.course_id, course.name, meta.content FROM course INNER JOIN course_meta AS meta ON course.course_id = meta.course_id WHERE course.status='enabled';");
	foreach ($courses as $course) {

		$content = json_decode($course["content"], true);
		$courses_list[] = array("id" => "c".$course["course_id"], "name" => $course["name"], "url" => $content["url_web"]);

	}

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

	$referrals = refer_get_by_referrer($user_src, $user["user_id"]);
	$referrals_total = count($referrals);

	if ($referrals !== false) {

		foreach ($referrals as $referral) {

			if ($referral["status"] == "no_action" || $referral["status"] == "registered" || $referral["status"] == "enrolled") {

				$ref_user = user_get_by_email($referral["email"]);
				if ($ref_user !== false) {

					$referral["status"] = "registered";

					$ref_enr = db_query("SELECT * FROM subs WHERE (status='active' OR status='pending') AND user_id=".$ref_user["user_id"]." ORDER BY start_date ASC;");
					if (isset($ref_enr[0])) {

						$referral["status"] = "enrolled";
						$ref_enr = $ref_enr[0];
						$enr_date = date_create_from_format("Y-m-d H:i:s", $ref_enr["start_date"]);
						$referral["enr_date"] = $enr_date;

						$referral["claimable"] = $claimable;
						if ($claimable && validate_payment_for_claim($ref_user["user_id"])) {
							$referral["status"] = "claim_reward";
						}

						if ($referral["status"] == "claim_reward") {

							$num_claimable++;
							$referral["claim_token"] = psk_generate("refer", $referral["id"], "jlc.refer.claim");

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
				}
				else if ($referral["status"] == "awaiting_approval") {
					$num_awaiting_approval++;
				}
				else if ($referral["status"] == "voucher_awarded") {
					$num_awarded++;
				}
				else if ($referral["status"] == "claim_reward") {
				
					$num_claimable++;
					$referral["claim_token"] = psk_generate("refer", $referral["id"], "jlc.refer.claim");
					$num_enrolled++;
				
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
			refer_edit($referral);

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
				max_selected_options: 2
				});
				$(".chosen-select").bind("chosen:maxselected",
				function() {
				$(".error1").html('You can only select two courses');
				});
			});
		</script>
	</head>
	<body data-tourian="false" >
		<div class="section-boxed">
			<div class="refer-heading">
				AN <span>AMAZON</span> TREAT AWAITS YOU
			</div>
			<div class="tagline">
				<div class="tag-1">Introduce friends to Jigsaw Academy. Get them enrolled. Get rewarded.</div>
				<div class="tag-2">Get an Amazon <span><a href="#terms_cond">*</a></span>voucher of  <i class="fa fa-inr" aria-hidden="true"></i> 1000 for every friend of your that enrolls.</div>
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
							<select <?php echo $disabled; ?> name="recommended_course" id="recommended_course" data-placeholder="Select 1-2 recommended courses" class="chosen-select" multiple="multiselect" >
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
									You have consumed all 5 invites.Want to refer more? Click to unlock 5 more invites.
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

												if ($referral["status"] == "no_action" || $referral["status"] == "registered") {

													if ($referral["resent_count"] == 0) { ?>
														<br/><a id="<?php echo $referral['id'] ?>" onclick="resend_link('<?php echo $referral["resend_token"] ?>')" class="resend_link">Resend invite</a>
													<?php }
													else if ($referral["resent_count"] < $resend_max) { ?>
														<br/><a id="<?php echo $referral["id"] ?>" onclick="resend_link('<?php echo $referral["resend_token"] ?>')" class="resend_link">Resend invite <span>(Resent invite <?php echo $referral["resent_count"]; ?> times)</span></a>
													<?php }
													else { ?>
														<br/><a class="resend_link"> <span>(Resent invite <?php echo $referral["resent_count"]; ?> times)</span></a>
													<?php }

												}
												else if ($referral["status"] == "invite_expired" && $referral["resent_count"] > 0) { ?>
													<br/><a class="resend_link"> <span>(Resent invite <?php echo $referral["resent_count"]; ?> times)</span></a>
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
				    	<p>Your friend <?php echo $user["name"] ?> is learning analytics with us and finds it a great career boost. <?php echo get_fname($user["name"]) ?>
				    	 has recommended the courses,<span id="course-1">Course 1 </span> <span id="and" style="display:none;">and</span> <span id="course-2" style="display:none;">Course 2</span> for you as <span id="grammer">these are </span>suitable for your profile.</p>

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
		//var dyErrorMsg = function () { return message; }
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
		   //return this.optional(element) || result;
		 }, '' );


		/** Added to check if phone number is already registered or referred **/
		var msg= ''; res = false;
		//var dynamicErrorMsg = function () { return msg; }
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
		   //return this.optional(element) || res;
		 }, '' );


		$("#refer-form").validate({
			//debug: true,
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
					 //needsSelection: true
				}
			},
			ignore: ':hidden:not("#recommended_course")',
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
    						// errorLabelContainer: '.error',

                    			submitHandler: function (form) { // for demo
                    				$(".error1").html("");
                    				// $("#preview").css('background','#009cd9');
                    				$("#referral-name").html($("#refer-form input[name=friend_name]").val());
					var select = $("#refer-form select");
					var i = 1;
					$("option:selected", select).each(function() {
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

				$.post("<?php echo JAWS_PATH_WEB ?>/lmsapi/jlc.refer/new", { referral : arr["referral"], token : $("#token").val() }, function (data) {
					response = JSON.parse(data);
					//console.log(response);
					$("#token").val(response.token);
					$(".stats").css('display','block');

					$("#loading-image").css('display',"none");
					$("#send-invite").prop("disabled",false);
					$("#preview-modal").removeClass("active");
					$("body > div.wrapper").removeClass('blur');

					var referrer_count = $("#referral_count").html();

					referrer_count = parseInt(referrer_count) + 1;
					if (response.response.n == 1) {
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
		//.fail(function(){
		// 	alert("Something went wrong!");
		// });

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
	</body>
</html>

