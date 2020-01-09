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

	/*if (!auth_api("jlc.referral"))
		die ("You do not have required priviledges to use this feature.");*/

	if (!isset($_REQUEST["token"]))
	{
		$user_id = 18;
		//die("You do not have the required priviledges to use this feature.");*/
	}
	else
	{
		$user_id = psk_info_get($_REQUEST["token"])["entity_id"];
		psk_expire("user", $user["user_id"], "jlc.referral.get");
	}

	// Student who's referral records have to be gathered
	$user = user_get_by_id($user_id);

	// Delete any old referral tokens
	psk_expire("user", $user["user_id"], "jlc.referral");

	// Check system_activity for the student
	$res_act = db_query("SELECT * FROM system_activity WHERE act_type='jlc.referral' AND activity='referral' AND context_type='user' AND context_id=".$user["user_id"]." ORDER BY act_id DESC;");

	// Lists the status of each referral
	$stats = array();
	// Holds the total number of referrals ever made
	$referrals_total = 0;
	// Holds the count of referrals done until now, resets if any referral enrolls
	$referral_flag = 0;
	// Holds the count of referrals enrolled
	$enrollments_count = 0;
	// Globally controls if the user can claim referral reward or not
	$paid_flag = true;
	// Total reward accumulated until now
	$reward_accrued = 0;
	// Total reward claimed until now
	$reward_claimed = 0;
	// Reward pending confirmation
	$reward_pending_confirmation = 0;

	$date = new DateTime("now");

	// If any referrals found, process them
	if (isset($res_act[0]))
	{
		// Check if the user is eligible to claim the referral reward
		// Criteria is the user should have paid all the installments and should have completed the 7 days of refund period
		$pay_info = payment_get_info_by_user_id($user["user_id"]);
		foreach ($pay_info as $payment)
		{
			foreach ($payment["instl"] as $instl)
			{
				if (strcmp($instl["status"], "paid") != 0)
				{
					$paid_flag = false;
					break 2;
				}
			}
		}
		// Check if the referrer is still in the 7 days refund period; if yes, the user cannot claim the referral reward
		if ($paid_flag)
		{
			$subs = db_query("SELECT start_date FROM subs WHERE user_id=".$user["user_id"]." ORDER BY start_date DESC LIMIT 1;");
			$start_date = date_create_from_format("Y-m-d H:i:s", $subs["start_date"])->add(new DateInterval("P7D"));
			if ($date < $start_date)
				$paid_flag = false;
		}

		// Start building the stats
		foreach ($res_act as $activity)
		{
			$content = json_decode($activity["content"], true);
			$referral_flag = $content["n"];
			$content = $content["r"];
			$putback = array();
			$edit = false;
			foreach ($content as $referral)
			{
				$referrals_total++;
				$stats[$i]["email"] = $referral["e"];
				$stats[$i]["name"] = $referral["n"];
				$referral_date = date_create_from_format("Y-m-d H:i:s", $referral["d"]);
				$stats[$i]["date"] = $referral_date->format("d-M Y");
				$consult = false;
				if (isset($referral["x"]) && $referral["x"] == "1")
				{
					$stats[$i]["status"] = "You have claimed this bonus";
					$subs = subs_get_info_by_user_id($referral["user_id"])[0];
					// Fetch the name of the bundle or courses for which the referral wished to or enrolled
					if (isset($subs["meta"]["bundle_id"]) && strlen($subs["meta"]["bundle_id"]) > 0)
						$stats[$i]["courses"] = db_query("SELECT name FROM course_bundle WHERE bundle_id=".$subs["meta"]["bundle_id"])[0]["name"];
					else
					{
						$courses = explode(";", trim($subs["combo"].";".$subs["combo_free"], ";"));
						$courses_str = "";
						foreach ($courses as $course)
							$courses_str .= db_query("SELECT name FROM course WHERE course_id=".explode(",", $course)[0])[0]["name"].",";

						$stats[$i]["courses"] = substr($courses_str, 0, -1);
					}
					$enrollments_count++;
					$reward_claimed += 1000;
					$i++;
					continue;
				}
				// Check if referral has registerred
				$referral_user = user_get_by_email($referral["e"]);
				$status = "No action";
				if ($referral_user)
				{
					$status = "In progress";
					// Check if the referral has been sent any payment link or has attempted any payment
					$pay = db_query("SELECT * FROM payment WHERE status='paid' AND user_id=".$referral["user_id"]." ORDER BY pay_id DESC LIMIT 1;");
					if (isset($pay[0]))
						$subs = subs_get_info($pay[0]["subs_id"]);
					else
						$subs = subs_get_info_by_user_id($referral_user["user_id"]);

					if ($subs)
					{
						$subs = $subs[0];
						$pay_amount = db_query("SELECT sum_total FROM payment WHERE subs_id=".$subs["subs_id"])[0]["sum_total"];
						$stats[$i]["amount"] = "";
						// Referral hasn't paid yet
						if ($subs["status"] == "inactive")
							$status = "Waiting for payment";
						else
						{
							$enrollments_count++;

							// Referral has paid but hasn't been granted access
							if ($subs["status"] == "pending")
								$status = "Enrolled, paid";
							// Referral has been granted access
							else if ($subs["status"] == "active")
								$status = "Referral has received access";

							$stats[$i]["claim"] = $paid_flag;
							// If the referer is eligible to claim reward and no coupon code has been already created for the referer, create one now
							if ($paid_flag && (!isset($referral["cc"]) || $referral["cc"] == ""))
							{
								// Check if the user is eligible to claim the referral reward for this referral
								$pay_info = payment_get_info_by_user_id($referral_user["user_id"]);
								foreach ($pay_info as $payment)
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
								// Check if the referral is still in the 7 days refund period; if yes, the referrer cannot claim the referral reward
								if ($stats[$i]["claim"])
								{
									$subs = db_query("SELECT start_date FROM subs WHERE user_id=".$referral_user["user_id"]." ORDER BY start_date DESC LIMIT 1;");
									$start_date = date_create_from_format("Y-m-d H:i:s", $subs["start_date"])->add("P7D");
									if ($date < $start_date)
										$stats[$i]["claim"] = false;
								}
								$reward_accrued += 1000;
							}
						}

						// Fetch the name of the bundle or courses for which the referral wished to or enrolled
						if (isset($subs["meta"]["bundle_id"]) && strlen($subs["meta"]["bundle_id"]) > 0)
							$stats[$i]["courses"] = db_query("SELECT name FROM course_bundle WHERE bundle_id=".$subs["meta"]["bundle_id"])[0]["name"];
						else
						{
							$courses = explode(";", trim($subs["combo"].";".$subs["combo_free"], ";"));
							$courses_str = "";
							foreach ($courses as $course)
								$courses_str .= db_query("SELECT name FROM course WHERE course_id=".explode(",", $course)[0])[0]["name"].", ";

							$stats[$i]["courses"] = substr($courses_str, 0, -2);
						}
					}
					else
						$consult = true;
				}
				else
					$consult = true;
				// If the referrer can claim reward, pass a token for claim API and the coupon code
				if ($stats[$i]["claim"])
				{
					$stats[$i]["claim"] = array("user_id" => $referral_user["user_id"], "token" => psk_generate("user", $user["user_id"], "jlc.referral.claim"), "coupon" => $referral["cc"], "amt" => "1000");
				}
				else
					$stats[$i]["claim"] = array("user_id" => $referral_user["user_id"], "coupon" => $referral["cc"]/*, "amt" => "1000"*/);

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
			// Might need to update the record...
			if ($edit)
			{
				db_exec("UPDATE system_activity SET content=".db_sanitize(json_encode(array("r" => $putback, "n" => "0")))." WHERE act_id=".$activity["act_id"]);
			}
		} // End of foreach($res_act as $activity)
	} // End of if (isset($res_act[0]))

	$response;
	// If the stats are not available, the response array does not have the element
	if (count($stats) > 0)
		$response["stats"] = $stats;

	$response["courses"] = array();

	// If referral count is less than 5, the referral form will be rendered
	if ($referral_flag == 0)
	{
		// Token for the referral submission form
		$response["token"] = psk_generate("user", $user["user_id"], "jlc.referral");
		// Gather courses and bundles to be shown on referral form
		$courses = db_query("SELECT course.course_id, course.name, meta.content FROM course INNER JOIN course_meta AS meta ON course.course_id = meta.course_id WHERE course.status='enabled';");
		foreach ($courses as $course)
		{
			$content = json_decode($course["content"], true);
			$response["courses"][] = array("id" => "c".$course["course_id"], "name" => $course["name"], "url" => $content["url_web"]);
		}
		$bundles = db_query("SELECT bundle.bundle_id, bundle.name, meta.content FROM course_bundle AS bundle INNER JOIN course_bundle_meta AS meta ON bundle.bundle_id = meta.bundle_id WHERE bundle.status='enabled' AND bundle.bundle_type='specialization';");
		foreach ($bundles as $bundle)
		{
			$content = json_decode($bundle["content"], true);
			$response["courses"][] = array("id" => "b".$bundle["bundle_id"], "name" => $bundle["name"], "url" => $content["url_web"]);
		}
	}

	// The total count of referrals made
	$response["referral_count"] = $referrals_total;
	// The count of referrals enrolled
	$response["enr_count"] = $enrollments_count;
	// The total reward claimed
	$response["reward_claimed"] = $reward_claimed;
	// The total reward accrued
	$response["reward_accrued"] = $reward_accrued;

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
		<link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
		
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
							<div class="tag-2">Get an Amazon <span><a href="#terms_cond">*</a></span>voucher of Rs.1000 for every friend of your that enrolls.</div>
						</div>
						
						<?php if ($referral_flag == 0)
						{ ?>
							<div class="refer-form" align="center">
								<form id="refer-form" method="post" action="" onsumbit="return false;">
									<div class="form-inputs">
										<input  required="true" title="You have only 5 invites to send out." type="text" name="friend_name" placeholder="Friend Name">
										<input required="true" type="email" name="friend_email" placeholder="Email Address">
										<input required="true" type="text" name="friend_phone" placeholder="Mobile Number" minlength="10" maxlength="15">
										<input type="hidden" name="token" id="token" value="<?php echo $response["token"] ?>">
										
										<select  name="recommended_course[]" data-placeholder="Select recommended course" class="chosen-select" multiple="true" >
											<?php foreach ($response["courses"] as $course)
											{ ?>
												<option value="<?php echo $course["id"] ?>" data-url="<?php echo $course["url"] ?>"><?php echo $course["name"] ?></option>
											<?php } ?>
										</select>

										<div class="error-div">
											<div class="err error-name"></div>
											<div class="err error-email"></div>
											<div class="err error-phone"></div>
											<div class="err error"></div>
										</div>

										<div class="preview">
											<input id="preview" type="button" name="preview" value="Preview Invite">
											<!-- <div class="share">
												<span>Share: </span><img src="media/share-buttons.png">
											</div> -->
										</div>
									</div>
								</form>
							</div>
						<?php } ?>
						
						<?php if ($response["referral_count"] >  0)
						{ ?>
							<div class="stats">
						<?php } else { ?><div class="stats" style="display:none;"> <?php }?>
								<div class="stats-heading">Stats</div>
								<div class="stats-data">
									<table>
										<thead>
											<tr>
												<td>Referred</td>
												<td>Enrolled</td>
												<td>You Earned So Far</td>
												<td>Awaiting Approval</td>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td  id="referral_count"><?php echo $response["referral_count"] ?></td>
												<td><?php echo $response["enr_count"] ?></td>
												<td><?php echo $response["reward_claimed"] ?></td>
												<td><?php echo $response["reward_accrued"] ?></td>
											</tr>
										</tbody>
									</table>
								</div>
								<div class="referral-details">
									Referral Details
								</div>
								<?php /*if (isset($response["stats"]))
								{ */?>
									<div class="referral-detail-table">
										<table id="referral-details">
											<thead>
												<th>Friend Referred</th>
												<th>Friend's Name</th>
												<th>Invited On</th>
												<th>Friend's Status</th>
												<th>Courses Enrolled</th>
												<th>Coupon Code</th>
												<th>Reward Status</th>
											</thead>
											<tbody>
												<?php foreach ($response["stats"] as $referrals)
												{ ?>
													<tr>
														<td><?php echo $referrals["email"] ?></td>
														<td><?php echo $referrals["name"] ?></td>
														<td><?php echo $referrals["date"] ?></td>
														<td><?php echo $referrals["status"] ?></td>
														<td><?php echo $referrals["courses"] ?></td>
														<td><?php echo $referrals["claim"]["coupon"] ?></td>
														<td><?php echo (isset($referrals["claim"]["token"]) ? "<a href='".JAWS_PATH_WEB."/lmsapi/jlc.referral.claim?token=".$referrals["claim"]["token"]."&referral_user_id=".$referrals["claim"]["user_id"]."&email=".urlencode($user["email"])."'>Claim</a>" : "Waiting for action") ?></td>
													</tr>
												<?php } ?>
											</tbody>
										</table>
									</div>
								<?php /*}*/ ?>
							</div>
						<?php /*}*/ ?>
						<div id="terms_cond" class="terms_condition"><span>*</span> Terms:</div>
						<div class="note">
							<ul>
								<li>
									<div class="question"> How can I refer my friends? <i class="fa fa-caret-down" aria-hidden="true"></i></div>
									<ul>
										<li>
										Fill up your friends details and send the invitation email. You can send invite to only 5 friends so send only those friends who will be interested to take up a course to boost their career. We do not send automatic invitations and spam them.
										</li>
									</ul>
								</li>
								<li>
									<div class="question"> When do I get the reward? <i class="fa fa-caret-down" aria-hidden="true"></i></div>
									<ul>
										<li>For every friend who enrolls with Jigsaw Academy for the first time, you get the Amazon gift voucher worth INR 1000.</li>
										<li>Your friend should enrol within 30 calendar days of your referral and with the coupon code you shared so that you both get the benefit.</li>
										<li>Your friend must be new and not a returning or existing Jigsaw student.</li>
										<li>Ones your friend has purchased the course, done the complete payment and crossed the seven calendar days money back guarantee, you can claim your Amazon voucher.</li>
									</ul>
								</li>
								<li>
									<div class="question">Why should my friend enrol through my referral? <i class="fa fa-caret-down" aria-hidden="true"></i></div>
									<ul>
										<li>Your friend gets and exclusive additional 5% discount only from this referral scheme.</li>
									</ul>
								</li>
								<li>
									<div class="question">How do I come to know when my friend enrolls? <i class="fa fa-caret-down" aria-hidden="true"></i></div>
									<ul>
										<li>You can see the statistics for all your invites in the ‘Refer a Friend’ page of your JLC.</li>
										<li>Status of each invite sent through JLC is updated on this screen.</li>
									</ul>
								</li>
								<li>
									<div class="question"> Is there a minimum course purchase requirement for my friend? <i class="fa fa-caret-down" aria-hidden="true"></i></div>
									<ul>
										<li>No, your friend can enrol into course of any cost. You get INR 1000 Amazon gift card for his first purchase with Jigsaw.</li>
									</ul>
								</li>
								<li>
									<div class="question">Can I get something else in place of the Amazon gift card <i class="fa fa-caret-down" aria-hidden="true"></i></div>
									<ul>
										<li> Yes, you can exchange your Amazon gift card with a discount coupon for your next course purchase at Jigsaw Academy. Talk to your education counsellor for getting your discount. Referral gift cards are non-transferrable, and cannot be exchanged for a cash.
										</li>
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
				    	<p>Give your career that much-needed boost with these highly recommended courses <span id="course-1">Course 1 </span> <span id="and" style="display:none;">and</span> <span id="course-2" style="display:none;">Course 2</span> from Jigsaw Academy. <?php echo $user["name"] ?> thinks that these courses would be ideal for your career.</p>

				    	<p>Enroll withing the next 30 days using this coupon code {COUPON CODE} and get a further discount of 5% on the courses.</p>

				    	<p>Jigsaw Academy, their specializations, student councilors and services are exactly of the standard that will support your career and take it places. If you'd like to explore other courses at Jigsaw, visit <a href='https://www.jigsawacademy.com/online-analytics-training/'>Jigsaw Academy Courses page</a>.</p>

				    	<div class="invite_button">
				    		<div class="cc_me"><input id="cc-me" type="checkbox" name="cc_me"><span> CC me</span></div>
				    		<div class="send_invite"><input id="send-invite" type="button" name="send_invite" value="Send Invite"></div>
				    	</div>
		    		</div>
		    		
	  		</div>
		</div>
	<script src="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL ?>/common/jquery/chosen.jquery.min.js" ></script>
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
			//$(".chosen-select").chosen();
			$(".chosen-select").chosen({max_selected_options: 2});
			$(".chosen-select").bind("chosen:maxselected", function () {
				//$(".error").css("display", "inline-block");
				$(".error").html('You can only select two courses');
				//setTimeout(function() { $(".error").hide(); }, 3000);
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
			         	$(".error-name").html("Please enter friend name");
			           	e.preventDefault();
			           	validate = false;
			         }
			         else{
			         	$(".error-name").html("");
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
							if (response.result == 0)
							{
								$(".error-email").html("Email address has already been referred.");
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
							if (response.result == 0)
							{
								$(".error-phone").html("Phone number has already been referred");
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
							$("#course-" + i).html("<a href='" + $(this).data("url") + "'>" + $(this).html() + "</a>");
							if (i == 2)
							{
								$("#and").css("display", "");
								$("#course-" + i).css("display", "");
							}
							else
							{
								$("#and").css("display", "none");
								$("#course-" + (i + 1)).css("display", "none");
							}
							i++;
						});
						$("#preview-modal").addClass("active");
						$("body > div.wrapper").addClass('blur');
					}
				}, 1000);
			});

			$("#send-invite").click(function() {
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
					console.log(response);
					//alert("Hao chal, ghar jaa");
					$("#preview-modal").removeClass("active");
					$("body > div.wrapper").removeClass('blur');
					var referrer_count = $("#referral_count").html();
					console.log(referrer_count);
					referrer_count = parseInt(referrer_count)+1;
					if (response.response.n == 1)
						$("#refer-form").css("display", "none");
					$("#referral_count").html(referrer_count);
					$('#referral-details tbody').append('<tr>'+
										'<td>'+response.response.email+'</td>'+
										'<td>'+response.response.name+'</td>'+
										'<td>'+response.response.date+'</td>'+
										'<td>'+response.response.status+'</td>'+
										'<td>'+response.response.ref+'</td>'+
										'<td>'+response.response.coupon+'</td>'+
										'<td>'+response.response.claim+'</td>'+
									'</tr>');
					$("#refer-form").trigger('reset');
					$(".chosen-select").trigger('chosen:updated');
				});
			});

			$("div.question").click(function() {
				$header = $(this);
				$content = $(this).next();
				$content.slideToggle(100);
				$header.find('i').toggleClass('fa-caret-up fa-caret-down');
			});
		});

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
	</script>
	</body>
</html>