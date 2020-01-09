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

	// Test Mode !!!!!!!!!!!! <<< ================================================ REMOVE WHEN LIVE !!!!!!!!!!!! =====================================
    //$GLOBALS['jaws_exec_live'] = false;

	// Ignore User Abort & Output Buffering
	ignore_user_abort(true);
	ob_start();

	// Load stuff
	load_module("ui");
	load_module("user");
	load_module("course");
	load_module("user_enrollment");
	load_library('email');

	// Check Course
	if (!isset($_REQUEST['course'])) {
        ui_render_msg_front(array(
            "type" => "error",
			"title" => "A problem ran into you :(",
			"header" => "Oops !",
			"text" => "Sorry, but this link seems to be invalid!<br /><br />Please contact our support team for assistance. (Code: 1)",
			"footer" => ["phone" => "+91-90193-17000", 'website' => 'iot']
		));
		exit();
	}

	// Prep
	$name; $email; $login_email; $phone; $password; $psk;

	// Get Course
	$course_info = db_query("SELECT c.*, m.category FROM course AS c INNER JOIN course_meta AS m ON m.course_id = c.course_id WHERE c.sp_code = ".db_sanitize("V".$_POST["course"])." ORDER BY c.course_id ASC;");
	if (empty($course_info)) {
		ui_render_msg_front(array(
            "type" => "error",
			"title" => "A problem ran into you :(",
			"header" => "Oops !",
			"text" => "Sorry, but this link seems to be invalid!<br /><br />Please contact our support team for assistance. (Code: 0)",
			"footer" => ["phone" => "+91-90193-17000", 'website' => 'iot']
		));
		exit();
	}

	$course_info = $course_info[0];
	$course = $course_info["course_id"];
	$category = explode(";", $course_info["category"]);
	if (in_array("iot", $category)) {

		$category = "iot";
		$GLOBALS["content"]["footer"]["phone"] = "+91-90193-17000";

	}
	else {

		$category = "analytics";
		$GLOBALS["content"]["footer"]["phone"] = "+91-90192-17000";

	}

	// duration for afb course is 5 days, for others it is 15.
	// afb course id 1
	$durations = [
		1 => 5,
		48 => 30,
		152 => 7,
		289 => 30
	];
	// if( $course == 1 ){
	// 	$duration = 5;
	// }
	// else if
	// else if ($course == 48) {
	// 	$duration = 15;
	// }
	$duration = $durations[$course];

	// Check Mode : Social
	if (!isset($_POST['corp'])) {
        auth_session_init();

		/*if (empty($_REQUEST["soc"])) {
            ui_render_msg_front(array(
                "type" => "error",
				"title" => "A problem ran into you :(",
				"header" => "Oops !",
				"text" => "Sorry, but this link seems to be invalid!<br /><br />Please contact our support team for assistance. (Code: 2)",
				"footer" => ["phone" => "+91-90193-17000", 'website' => 'iot']
			));
			exit();
		}*/

		$user = user_get_by_email($_REQUEST["email"]);

		$name = $user['name'];
		$email = $user['email'];
		$phone = $user['phone'];

		if (empty($_POST["soc"])) {

			if (!empty($user["soc_gp"])) {
				$_POST["soc"] = "gp";
			}
			else if (!empty($user["soc_fb"])) {
				$_POST["soc"] = "fb";
			}
			else if (!empty($user["soc_li"])) {
				$_POST["soc"] = "li";
			}

		}

		$_REQUEST["soc"] = $_POST["soc"];

		if (!isset($_POST["soc"])) {
            ui_render_msg_front(array(
                "type" => "error",
				"title" => "A problem ran into you :(",
				"header" => "Oops !",
				"text" => "Sorry, but this link seems to be invalid!<br /><br />Please contact our support team for assistance. (Code: 3)",
				"footer" => ["phone" => "+91-90193-17000", 'website' => 'iot']
			));
			exit();
		}

		$login_email = $user["soc_".strtolower($_POST["soc"])];

	}

	// Mode : Corp
	else {
		if ((!isset($_POST['name'])) || (!isset($_POST['email'])) || (!isset($_POST['phone'])) || (!isset($_POST['password']))) {
			ui_render_msg_front(array(
				"type" => "error",
				"title" => "A problem ran into you :(",
				"header" => "Oops !",
				"text" => "Sorry, but this link seems to be invalid!<br /><br />Please contact our support team for assistance. (Code: 4)",
				"footer" => ["phone" => "+91-90193-17000", 'website' => 'iot']
			));
			exit();
		}

		$name = $_POST['name'];
		$email = $_POST['email'];
		$phone = $_POST['phone'];
		$password = $_POST['password'];
		$login_email = $_POST["email"];
        $city = $_POST["city"] ?? '';

        if (empty($password)) {
        	// $password = bin2hex(random_bytes(4));
        	$emailComponents = explode("@", $email);
        	$password = $emailComponents[0];
        	if (strlen($password) < 6) {
        		$password .= "12345";
        	}

        }

        $meta = array( 'city' => $city );

		// create lead with utm parameters if met with condition
		if( !empty($_POST["ldc"]) && $_POST["ldc"] == true ){

			$source = $_POST["source"] ?? "";
			if (!empty($source)) {
				$source .= "-".$_POST["course"];
			}

			// Insert into leads
			$query="INSERT INTO
						user_leads_basic (
							name,
							email,
							phone,
							utm_source,
							utm_campaign,
							utm_term,
							utm_medium,
							utm_segment,
							utm_content,
							utm_numvisits,
							referer,
							ip,
							ad_lp,
							ad_url,
							create_date,
							capture_trigger,
							capture_type,
                            meta
						)
					VALUES
						(
							".db_sanitize($_POST['name']).",
							".db_sanitize($_POST['email']).",
							".db_sanitize($_POST['phone']).",
							".db_sanitize($source).",
							".db_sanitize($_POST['campaign'] ?? '').",
							".db_sanitize($_POST['term'] ?? '').",
							".db_sanitize($_POST['medium'] ?? '').",
							".db_sanitize($_POST['segment'] ?? '').",
							".db_sanitize($_POST['content'] ?? '').",
							".db_sanitize($_POST['numvisits'] ?? '').",
							".db_sanitize($_POST['referer'] ?? '').",
							".db_sanitize($_POST['ip'] ?? '').",
							".db_sanitize($_POST['lp'] ?? '').",
							".db_sanitize($_POST['url'] ?? '').",
							".db_sanitize(strval(date("Y-m-d H:i:s"))).",
							'formsubmit',
                            'url',
                            ".db_sanitize(json_encode($meta))."
						);";

			db_exec($query);
			// $lead_id = db_get_last_insert_id();
		}

	}

	// Check if any old enrollment is present; if there is any, get the soc and overwrite $_GET["soc"]. It will be used if the login method is soc, otherwise not.
	$enr_old_act = db_query("SELECT act.content FROM system_activity AS act INNER JOIN system_activity AS sa ON act.context_id = sa.act_id WHERE act.act_type='sis.import.automation.free' AND act.activity='success' AND act.context_type='system_activity' AND sa.act_type='jlc.free' AND sa.activity='setup' AND sa.content=".db_sanitize($email)." ORDER BY sa.act_id ASC LIMIT 1;");
	if (isset($enr_old_act[0])) {
		$content = json_decode($enr_old_act[0]["content"], true);
		$password = $_POST[$content["lms_auth_key"]] = $content["lms_auth_value"];
	}

	// Create Tracking Activity
	$enract = db_query('SELECT * FROM system_activity WHERE act_type="jlc.free" AND activity="setup" AND content='.db_sanitize($email).' AND entity_type="course" AND entity_id='.$course_info["course_id"].';');

	$regged = false;

	// Registered for this course and access is ready!
	if (isset($enract[0]['status']) && ($enract[0]['status'] == 'executed')) {

		send_email_with_attachment('lms.free.setup.success', ['to' => $email], [
	    		'fname' => substr($name, 0, ((strpos($name, " ") !== false) ? strpos($name, " ") : strlen($name))),
	    		'course' => [
	    				'name' => $course_info['name'],
	    				'category' => $category
	    				],
	    		'access' => [
	    				'duration' => $duration,
	    				'end_date' => date('dS M Y', strtotime("+" . $duration . " days")),
	    				'account' => [
	    					'mode' => (!isset($_REQUEST["corp"])) ? 'soc' : 'corp',
	    					'provider' => (!isset($_REQUEST["corp"])) ? ($_REQUEST["soc"] == 'fb' ? 'Facebook' : ($_REQUEST["soc"] == 'gp' ? 'Google+' : 'LinkedIn')) : '',
	    					'username' => $email,
	    					'password' => $password ?? ''
	    					]
	    				]
	    	], ["media/misc/attachments/Terms_and_Conditions.pdf"]);

		ui_render_msg_front(array(
			"type" => "info",
			"title" => "Already Signed Up",
			"header" => "Free Learning",
			"text" => "Hey ".substr($name, 0, ((strpos($name, " ") !== false) ? strpos($name, " ") : strlen($name))).",<br/>It seems you are already registered with us.<br/><br/>Kindly visit <a href='https://freelearning.jigsawacademy.net'><b style='text-decoration: none!important;'>FreeLearning.JigsawAcademy.net</b></a> and use the login details we sent to you by email.",
			"footer" => ["phone" => "+91-90193-17000", 'website' => 'iot']
		));
		exit();

	}
	else {

		// Registered for this course but access is pending
		if (isset($enract[0]['status'])) {
			$psk = psk_get('jlc.free', $enract[0]['act_id'], 'setup');
		}
		// Not registered for this course
		else {
			$act_id = activity_create('high','jlc.free','setup','course',$course_info["course_id"],'','',$email,'pending');
			$psk = psk_generate('jlc.free', $act_id, 'setup');

			// Additionally check if registered for any course at all !!
			$enract = db_query('SELECT * FROM system_activity WHERE act_type="jlc.free" AND activity="setup" AND content='.db_sanitize($email).' ORDER BY act_id ASC LIMIT 1;');

			// Registered for some other course
			if (isset($enract[0]['status']) && ($enract[0]['status'] == 'executed')) {
				$regged = json_decode((db_query('SELECT content FROM system_activity WHERE act_type="sis.import.automation.free" AND context_id='.db_sanitize($enract[0]['act_id']).' LIMIT 1;'))[0]['content'], true);

				$_POST['email'] = $regged['email'];
				$email = $regged['email'];

				if ($regged['lms_auth_key'] == 'soc') {
					$_POST['soc'] = $regged['lms_auth_value'];
					unset($_POST['corp']);
				}
				else {
					$_POST['corp'] = true;
					unset($_POST['soc']);
				}
			}
		}
	}

	// Proceed with rendering the UI
	ui_render_head_front(array(
		"title" => ("Free Learning - Jigsaw Academy"),
		//"scripts" => array(1 => "app/templates/jaws/frontend/modal.js"),
		"styles" => array(1 => "app/templates/jaws/frontend/modal.css")
	));

	?>

	<!-- Google Code for Conversion Page -->
	<script type="text/javascript">
	       /* <![CDATA[ */
	       var google_conversion_id = 987804683;
	       var google_conversion_language = "en";
	       var google_conversion_format = "3";
	       var google_conversion_color = "ffffff";
	       var google_conversion_label = "oV-KCLmRklgQi-iC1wM";
	       var google_remarketing_only = false;
	       /* ]]> */
	</script><script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js"></script>
	<noscript><div style="display:inline;"><img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/987804683/?label=oV-KCLmRklgQi-iC1wM&amp;guid=ON&amp;script=0"/></div></noscript>
	<!-- Google Code for Conversion Page ends here -->

		<script type='text/javascript'>
			var wcount=0,ready=false,perma_fail=false;

			$(document).ready(function(){

				// Init
				var qcurr=1,ecurr=1,qtol=$('div.wait-quote').length;
				$('div.wait-quote:first-child').addClass('active');

				// Wait Quotes
				var t_q = setInterval(function() {
					if (qcurr == qtol ) qcurr = 1;
					else qcurr++;

					$('div.wait-quote').removeClass('active');
					if (ready || perma_fail) {
						try {
							clearInterval(t_q);
							clearInterval(t_e);
						} catch(r) {}

						$('div.page.wait').removeClass('active');
						if (ready) $('div.page.success').addClass('active');
						else $('div.page.fail').addClass('active');
						if (!perma_fail) $('#btn-freejlc').addClass('active');
						$('.progress').css('width','100%');
						$('.progress').addClass('ready');

						// Redirect to JLC in 5 Seconds
						setTimeout(function(){
							//window.location.href = $('#txt-jlc-free-url').val();
						}, 10000);
					}
					else {
						$('div.wait-quote:nth-child('+(qcurr)+')').addClass('active');
						$('.progress').css('width',(wcount * 1.5)+'%');
						poll();
					}
				}, 7500);

				// Wait Elipsis
				var t_e = setInterval(function() {
					if (wcount > 100) $('#wait-longer').html('This is taking longer than expected');
					else wcount++;

					ecurr++;
					if (ecurr>3) ecurr = 1;

					var txt;
					if (ecurr == 1) txt = 'Please Wait';
					else if (ecurr == 2) txt = 'Please Wait.';
					else txt = 'Please Wait..';

					$('#wait-ellipsis').html(txt);
				}, 500);

				// API Poll for status
				function poll() {
					$.ajax({
						url: $('#txt-jaws-status-url').val(),
						type: 'POST',
						data: {}
					})
					.done(function(data) {
						var d = jQuery.parseJSON(data);
						if (d.status === true) ready = true;
						else if (d.perma_fail === true) perma_fail=true;
					});
				}

				// Goto Free JLC
				$('#btn-freejlc').click(function() {
					window.location.href = $('#txt-jlc-free-url').val();
				});

			});
		</script>

		<div id="bkg-img"> </div>
		<div id="bkg-overlay"> </div>

		<div class="modal">

			<div class="page bkg active wait">
				<div class="header" id='wait-longer'>Setting up your free account</div>
				<div class='sub-header' id='wait-ellipsis'>Please Wait</div>

				<div class="text">

					<style scoped>
						div.wait-quote-container {
							display: block;
							position: relative;
							top: 50px;
						}

						div.wait-quote-container > div.wait-quote {
							display: block;
							position: absolute;
							opacity: 0;
							top: 50px;

							-webkit-transition: opacity 0.75s ease-in, top 0.75s ease-in;
							-moz-transition: opacity 0.75s ease-in, top 0.75s ease-in;
							-o-transition: opacity 0.75s ease-in, top 0.75s ease-in;
							-ms-transition: opacity 0.75s ease-in, top 0.75s ease-in;
							transition: opacity 0.75s ease-in, top 0.75s ease-in;
						}

						div.wait-quote-container > div.wait-quote.active {
							opacity: 1;
							top: 0px;
						}

						div.wait-quote-container > div.wait-quote > i.fa {
							position: relative;
							margin:  0 10px;
						}
						div.wait-quote-container > div.wait-quote > i.fa-quote-left { top: -15px; }
						div.wait-quote-container > div.wait-quote > i.fa-quote-right { top: 10px; }

						div.wait-quote-container > div.wait-quote > span.wait-quote-text {
							line-height: 150%;
						}

						div.wait-quote-container > div.wait-quote > span.wait-quote-person {
							position: relative;

							font-family: 'Montserrat', sans-serif;
							font-size: 15px;
							font-weight:  bold;
							opacity: 0.85;
							text-transform: uppercase;
						}

						div.wait-quote-container > div.wait-quote > span.wait-quote-job {
							position: relative;
							top: -5px;

							font-family: 'Montserrat', sans-serif;
							font-size: 12px;
							opacity: 0.6;
							text-transform: capitalize;
						}


					</style>

					<div class='wait-quote-container'>

					<?php if (in_array($course, [48])) { ?>

						<div class='wait-quote'>
							<i class='fa fa-quote-left fa-fw fa-lg'></i><span class='wait-quote-text'>IoT will be bigger than anything that's ever been done in high tech. It will change the way people live, work and play.</span><i class='fa fa-quote-right fa-fw fa-lg'></i><br/><br/>
							<span class='wait-quote-person'>John Chambers</span><br/>
							<span class='wait-quote-job'>CEO, Cisco Systems Inc.</span>
						</div>

						<div class='wait-quote'>
							<i class='fa fa-quote-left fa-fw fa-lg'></i><span class='wait-quote-text'>If you think that the internet has changed your life, think again. The IoT is about to change it all over again!</span><i class='fa fa-quote-right fa-fw fa-lg'></i><br/><br/>
							<span class='wait-quote-person'>Brendan O’Brien</span><br/>
							<span class='wait-quote-job'>Co-Founder, Aria Systems</span>
						</div>

						<div class='wait-quote'>
							<i class='fa fa-quote-left fa-fw fa-lg'></i><span class='wait-quote-text'>Do not put your faith in what statistics say, until you have carefully considered what they do not say.</span><i class='fa fa-quote-right fa-fw fa-lg'></i><br/><br/>
							<span class='wait-quote-person'>William W Watt</span>
						</div>

					<?php } else { ?>

						<div class='wait-quote'>
							<i class='fa fa-quote-left fa-fw fa-lg'></i><span class='wait-quote-text'>The world is now awash in data and we can see consumers in a lot clearer ways.</span><i class='fa fa-quote-right fa-fw fa-lg'></i><br/><br/>
							<span class='wait-quote-person'>Max Levchin</span><br/>
							<span class='wait-quote-job'>PayPal ​C​o-founder</span>
						</div>

						<div class='wait-quote'>
							<i class='fa fa-quote-left fa-fw fa-lg'></i><span class='wait-quote-text'>Data is a precious thing and will last longer than the systems themselves.</span><i class='fa fa-quote-right fa-fw fa-lg'></i><br/><br/>
							<span class='wait-quote-person'>Tim Berners-Lee</span><br/>
							<span class='wait-quote-job'>Inventor of the World Wide Web</span>
						</div>

						<div class='wait-quote'>
							<i class='fa fa-quote-left fa-fw fa-lg'></i><span class='wait-quote-text'>Data really powers everything that we do.</span><i class='fa fa-quote-right fa-fw fa-lg'></i><br/><br/>
							<span class='wait-quote-person'>Jeff Weiner</span><br/>
							<span class='wait-quote-job'>CEO LinkedIn</span>
						</div>

						<div class='wait-quote'>
							<i class='fa fa-quote-left fa-fw fa-lg'></i><span class='wait-quote-text'>The most valuable commodity I know of is information.</span><i class='fa fa-quote-right fa-fw fa-lg'></i><br/><br/>
							<span class='wait-quote-person'>Gordon Gekko</span><br/>
							<span class='wait-quote-job'>Wall Street</span>
						</div>

						<div class='wait-quote'>
							<i class='fa fa-quote-left fa-fw fa-lg'></i><span class='wait-quote-text'>Making analytics cool is, to a certain extent, enabled by having cool people working with it.</span><i class='fa fa-quote-right fa-fw fa-lg'></i><br/><br/>
							<span class='wait-quote-person'>David Rhee</span><br/>
							<span class='wait-quote-job'>Adidas Group</span>
						</div>


					<?php } ?>

					</div>
				</div>
			</div>

			<div class="page bkg success">
				<div class="header">You're All Set</div>
				<div class="sub-header">Success</div>

				<div class="text">
					<?php echo ($regged === false ? "Your free account is ready." : "You already have an account with us."); ?> Here are your login credentials:<br/><br/>

					URL: <a href='https://freelearning.jigsawacademy.net/login/free_trial?token=<?php echo $psk; ?>&email=<?php echo urlencode($email) ?>&course=<?php echo $course_info["sis_id"] ?>'>freelearning.jigsawacademy.net</a><br/>
					<?php if (!isset($_REQUEST['corp'])) { ?>
						Social Sign-On: <?php echo ($_REQUEST["soc"] == 'fb' ? 'Facebook' : ($_REQUEST["soc"] == 'gp' ? 'Google+' : 'LinkedIn')); ?><br/>
					<?php } else { ?>
						Email Id: <?php echo $_POST['email']; ?><br/>
						Password: **** (Please check your E-mail) <?php //echo $_POST['password']; ?><br/>
					<?php } ?>

					<br/><br/>
					We have emailed you a copy of the above (Remember to check your spam folder also). Click 'Take me there now' to go to your free account.

				</div>
			</div>

			<div class="page bkg fail">
				<div class="header">Something went wrong :(</div>
				<div class="sub-header">Oops</div>

				<div class="text">
					We were unable to create your free account due to some error. Please write to us at <a href="mailto:support@jigsawacademy.com">support@jigsawacademy.com</a>.<br/><br/>Sorry for the inconvenience.
				</div>
			</div>

			<div class="nav">
				<div class="panel left">
					<div style='display:none' class="link-button active" id="btn-iot">Back to website</div>
				</div>

				<div class="panel right" >
					<div class="link-button" id="btn-freejlc">Take me there now</div>
				</div>
			</div>

			<div class="progress" style='max-width: 100%; width: 10%'>
				<div class="RL"></div>
			</div>

		</div>

		<div style="visibility: hidden; display: none;">
			<input type="hidden" value="https://www.jigsawacademy.com/jaws/lmsapi/jlc.free.setup.status?token=<?php echo $psk; ?>&email=<?php echo urlencode($email); ?>&course=<?php echo $course; ?>" id="txt-jaws-status-url" style="visibility: hidden; display: none;" />
			<input type="hidden" value="https://freelearning.jigsawacademy.net/login/free_trial?token=<?php echo $psk; ?>&email=<?php echo urlencode($email)."&course=".$course_info["sis_id"]; ?>" id="txt-jlc-free-url" style="visibility: hidden; display: none;" />
		</div>

<?php
	// Get output size
	$ob_length = ob_get_length();

	// No Compression
	@apache_setenv('no-gzip', 1);

	// Send Headers
	header("Content-Type: text/html;");
	header("Content-Length: ".$ob_length.";");
	header($_SERVER["SERVER_PROTOCOL"] . " 200 Accepted;");
	header("Status: 200 Accepted;");
	header("Connection: close;");

	// Flush Buffer
	ob_end_flush();
	ob_flush();
	flush();
	ob_end_clean();

	// Close & Destroy Session
	session_write_close();
	// session_destroy();

	// Try Create Enrollment
	if (enrollment_free_create($login_email, $name, (!isset($_POST["corp"])) ? "soc" : "pass", (!isset($_POST["corp"])) ? $_POST["soc"] : $password, $course)) {

		// Update the activity
		db_exec("UPDATE system_activity SET status='executed' WHERE act_type='jlc.free' AND activity='setup' AND content=".db_sanitize($email)." AND entity_type='course' AND entity_id=".$course.";");

		// Trigger Welcome Email
    	send_email_with_attachment(
    		'lms.free.setup.success',
    		['to' => $email],
    		[
	    		'fname' => substr($name, 0, ((strpos($name, " ") !== false) ? strpos($name, " ") : strlen($name))),
	    		'course' => [
	    				'name' => $course_info['name'],
	    				'category' => $category
	    				],
	    		'access' => [
	    				'duration' => $duration,
	    				'end_date' => date('dS M Y', strtotime("+" . $duration . " days")),
	    				'account' => [
	    					'mode' => (!isset($_REQUEST["corp"])) ? 'soc' : 'corp',
	    					'provider' => (!isset($_REQUEST["corp"])) ? ($_REQUEST["soc"] == 'fb' ? 'Facebook' : ($_REQUEST["soc"] == 'gp' ? 'Google+' : 'LinkedIn')) : '',
	    					'username' => $email,
	    					'password' => $password ?? ''
	    					]
	    				]
	    	],
	    	["media/misc/attachments/Terms_and_Conditions.pdf"]
    	);

	}
	else {
		// Update the activity with failed status
		db_exec("UPDATE system_activity SET status='fail' WHERE act_type='jlc.free' AND activity='setup' AND content=".db_sanitize($email)." AND entity_type='course' AND entity_id=".$course.";");
	}


?>
