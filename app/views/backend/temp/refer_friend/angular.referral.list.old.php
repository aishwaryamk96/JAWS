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
	      	header('Location: http://www.jigsawacademy.com');
	   	die();
	}

	load_module("ui");
	load_module("user");
	load_module("subs");

  	// Init Session
	auth_session_init();

	// Prep
	$login_params["return_url"] = JAWS_PATH_WEB."/referralslist";
	
	// Login Check

	/*if (!auth_session_is_allowed("jlc.referral")) {
		ui_render_msg_front(array(
			"type" => "error",
			"title" => "Jigsaw Academy",
			"header" => "No Tresspassing",
			"text" => "Sorry, but you do not have permission to access this page.<br/>Contact Jigsaw Support team for assistance."
			));
		exit();
	}*/
	$date = new DateTime("now");

	$voucher_award = false;
	if (auth_session_is_allowed("jlc.referral.voucher.award"))
		$voucher_award = true;

	$res_referral_act = db_query("SELECT * FROM system_activity WHERE act_type='jlc.referral' AND activity='referral' ORDER BY act_id DESC;");

	//var_dump($res_referral_act); exit();

	$refer = array();
	$export_list = "Referrer Email,Referrer Name,Email,Name,Phone,Courses\r\n";

	$i = 0;
	$no_action = 0;
	$reg = 0;
	$enrolled = 0;
	$voucher_awarded = 0;
	$awaiting_voucher = 0;
	$invite_expired = 0;
	$can_claim = 0;
	$total_enrolled = 0;

	foreach ($res_referral_act as $referral)
	{
		$paid_flag = false;
		$write_back = false;
		$content_new = array();

		if ($referral["context_type"] == "user")
		{
			$user = user_get_by_id($referral["context_id"]);

			$pay_info = payment_get_info_by_user_id($user["user_id"]);
			// Check if JAWS has payment history for the student
			if ($pay_info !== false)
			{
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
			}
			else
				$paid_flag = true;
		}
		else
		{
			$user = json_decode(db_query("SELECT content FROM system_activity WHERE act_id=".$referral["context_id"])[0]["content"], true);
			$user["user_id"] = $referral["context_id"];
			$paid_flag = true;
		}
		$user["user_src"] = $referral["context_type"];

		$content = json_decode($referral["content"], true);
		$referrals = $content["r"];
		$ref_info = array();
		$referrals = array_reverse($referrals);
		foreach ($referrals as $referred)
		{
			$referred_write_back_temp = $referred;
			$ref_user = user_get_by_email($referred["e"]);

			$noaction_expired= date_create_from_format("Y-m-d H:i:s", $referred["d"])->add(new DateInterval("P30D"));
			if($date > $noaction_expired)
			{
				$referred["status"] = "No action/ Invite Expired";
				$referred["color"] = "#F0F0F0";
			}
			else
			{
				$referred["status"] = "No action";
				$referred["color"] = "#F0F0F0";
			}
			
			if ($ref_user !== false)
			{
				$referred['encoded_e'] = urlencode($referred["e"]);
				$referred["user_id"] = $ref_user["user_id"];
				$referred["reg"] = 1;

				$reg_expired= date_create_from_format("Y-m-d H:i:s", $referred["d"])->add(new DateInterval("P30D"));
				if($date > $reg_expired)
				{
					$referred["status"] = "Registered/ Invite Expired";
					$referred["color"] = "#FAA257";
				}
				else
				{
					$referred["status"] = "Registered";
					$referred["color"] = "#FAA257";
				}
				
				

				if (isset($referred['x']))
				{
					if ($referred["x"] == 1)
					{
						$referred['status'] = 'Awaiting voucher approval';
						$referred["color"] = "#FFFF00";
						$awaiting_voucher++;
						$total_enrolled++;

					}
					else if ($referred["x"] == 2)
					{
						$referred['status'] = 'Voucher awarded';
						$referred["color"] = "#75DB1B";
						$voucher_awarded++;
						$total_enrolled++;
					}
					else if ($referred["x"] == "-1")
					{
						$referred['status'] = 'Invite expired';
						$referred["color"] = "#ececec";
						$invite_expired++;
					}
					else if ($referred["x"] == "0")
					{
						$referred['status'] = 'Can Claim';
						$referred["color"] = "#49FFFF";
						$can_claim++;
						$total_enrolled++;
						//$enrolled++;
					}
				}
				else
				{
					// Check if the referral has been sent any payment link or has attempted any payment
					$subs = db_query("SELECT subs.*, subs_meta.bundle_id FROM subs INNER JOIN subs_meta ON subs.subs_id = subs_meta.subs_id WHERE status != 'inactive' AND user_id=".$ref_user["user_id"]." ORDER BY subs_id DESC LIMIT 1;");
					if (isset($subs[0]))
					{
						$subs = $subs[0];

						$referred["status"] = "Enrolled";
						$referred["color"] = "#9CFF88";
						$enrolled++;
						$total_enrolled++;

						// If the referer is eligible to claim reward and no coupon code has been already created for the referer, create one now
						if ($paid_flag)
						{
							// Check if the user is eligible to claim the referral reward for this referral
							$pay_info = payment_get_info_by_user_id($ref_user["user_id"]);
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
											$paid_flag = false;
											break 2;
										}
									}
								}
							}
							// Check if the referral is has exceeded 7 days refund period; if yes, the referrer can claim the referral reward
							if ($paid_flag)
							{
								$start_date = date_create_from_format("Y-m-d H:i:s", $subs["start_date"])->add(new DateInterval("P7D"));
								if ($date > $start_date)
								{
									$referred["status"] = "Can Claim";
									$referred["color"] = "#49FFFF";
									$can_claim++;
									//$total_enrolled--;
									$enrolled--;
									$referred_write_back_temp["x"] = "0";
									$write_back = true;
								}
							}
						}
					}
					else
						$reg++;
				}
			}
			else
			{
				$ref_date = date_create_from_format("Y-m-d H:i:s", $referred["d"])->add(new DateInterval("P30D"));
				if ($date < $ref_date)
				{
					$write_back = true;
					$referred_write_back_temp["x"] = "-1";
					$invite_expired++;
					$referred["status"] = "Invite expired";
				}
				else
					$no_action++;
				$export_list .= $user["email"].",".$user["name"].",".$referred["e"].",".$referred["n"].",".$referred["p"].",";
				$courses_str = array();
				foreach ($referred["c"] as $consults)
				{
					if (substr($consults, 0, 1) == "c")
						$courses_str[] = db_query("SELECT name FROM course WHERE course_id=".substr($consults, 1))[0]["name"];
					else
						$courses_str[] = db_query("SELECT name FROM course_bundle WHERE bundle_id=".substr($consults, 1))[0]["name"];
				}
				$export_list .= implode(",", $courses_str)."\r\n";
			}
			$refer[] = ["user" => ["user_id" => $user["user_id"], "name" => $user["name"], "email" => $user["email"], "src"=> $user['user_src'], "encoded_email"=> urlencode($user["email"])], "referral" => $referred];
			$ref_info[] = $referred;

			$content_new[] = $referred_write_back_temp;

			echo (empty($referred["x"]) ? "==" : $referred["x"])."<br>";
		}

		//echo "<pre>"; print_r($refer);die();

		$i += count($ref_info);
		// If the record has been updated, write the record back to the database
		if ($write_back)
			db_exec("UPDATE system_activity SET content=".json_encode(array("r" => $content_new, "n" => $content["n"]))." WHERE act_id=".$referral["act_id"].";");
	}

	if (isset($_REQUEST["export"]))
	{
		$filename = "external/temp/no_action_referrals.csv";
		$file = fopen($filename, "w");
		fwrite($file, $export_list);
		fclose($file);

		header('Pragma: public');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control: private', false);
		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename="'. basename($filename) . '"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . filesize($filename));
		readfile($filename);
		exit();
	}
?>
<html ng-app="referApp">
	<head>
		<title><?php echo substr($domain_name, 0, -3) ?>Referral List</title>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.17/angular.min.js"></script>

		<style>
		table td {
			background-color: rgba(0, 0, 0, 0.075);
			text-align:center;
			font-size: 13px;
		}
		table th {
			background-color: #63beeb;
			text-align:center;
			font-size: 13px;
			color:#fff;
		}
		table th a{
			color:#fff;
			text-decoration: none;
			font-size: 13px;
		}
		td.sno a{
			color:#000;
			font-size: 13px;
		}
		a {
			font-size: 75%;
		}
		
		.header {
			position: fixed;
			width: 100%;
			background: white;
			top: 0px;
			padding-top: 10px;
		}
		input{
			    height: 30px;
			    border: 1px solid #909090;
			   /* border-radius: 3px;*/
			    padding:3px;
		}
		form {
			margin-bottom: 0px;
		}
	</style>
		
	</head>
	<!-- Controller name goes here -->
	<body ng-controller="referController" style="font-family: sans-serif; font-size: 90%;">
		<div class="header">
			<div>
				<div style="display:inline-block; width:35%; vertical-align:middle;"><img src="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL.'/media/jaws/backend/jigsaw_horizontal_logo.png'; ?>">
				</div>
				<div  style="display:inline-block; width:55%; vertical-align:middle;">
					<span  style="font-weight:bold;font-size:16px;">Search: </span>
					<span >
					<input style="width:45%;" type="text" placeholder="Type your search text here..." ng-model="searchKeyword"/>
					</span>
				</div>

				<div style="display:inline-block;vertical-align:middle;"> <b style="font-size:18px;"><?php echo $_SESSION["user"]["name"]; ?></b> <br/>
				<a href="<?php echo JAWS_PATH_WEB."/logout"; ?>">Logout</a>
		        		<?php if (isset($msg)) echo "<br/>".$msg; ?></div>
			</div>
			<hr/>
		    	<center>
			       
			    	<!-- <hr> -->
			    	<table border="0" cellpadding="10" cellspacing="2" style="font-size: 95%;">
					<thead>
						<tr>
							<th>Total</TH>
							<th>No Action</th>
							<th>Registered</th>
							<th>Enrolled</th>
							<th>Awaiting Voucher</th>
							<th>Voucher Awarded</th>
							<th>Can Claim</th>
							<th>Invite Expired</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><?php echo $total_enrolled ."/".$i ?></td>
							<td>
								<form method="POST" action="<?php echo JAWS_PATH_WEB ?>/referralslist">
									<input style="cursor: pointer;" type="submit" name="export" value="<?php echo $no_action ?>" />
								</form>
							</td>
							<td style="background:#FAA257;"><?php echo $reg ?></td>
							<td style="background:#9CFF88;"><?php echo $enrolled ?></td>
							<td style="background:#FFFF00;"><?php echo $awaiting_voucher ?></td>
							<td style="background:#75DB1B;"><?php echo $voucher_awarded ?></td>
							<td style="background:#49ffff;"><?php echo $can_claim ?></td>
							<td><?php echo $invite_expired ?></td>
						</tr>
					</tbody>
				</table>
				<hr>
				<!-- <div style="padding:20px 0px 0px 0px;">
					<span  style="font-weight:bold">Search: </span>
					<span >
					<input type="text" placeholder="Type your search text here..." ng-model="searchKeyword"/>
					</span>
				</div> -->
			</center>
		</div>
		<center>
		<div style=" margin-top:15%;">
			<!-- <div style="text-align:left; font-weight:bold;width:90%;">NOTE: Click on the table header to sort</div> -->
			<table border="0" cellpadding="10" cellspacing="2" >
				<thead>
					<tr>
						<th>#</th>
						<th><a href="" ng-click="orderByField='user.name'; reverseSort = !reverseSort">Referrer Name</a></th>
						<th><a href="" ng-click="orderByField='user.email'; reverseSort = !reverseSort">Referrer Email</a></th>
						<th><a href="" ng-click="orderByField='referral.n'; reverseSort = !reverseSort">Referral Name</a></th>
						<th><a href="" ng-click="orderByField='referral.e'; reverseSort = !reverseSort">Referral Email</a></th>
						<th><a href="" ng-click="orderByField='referral.d'; reverseSort = !reverseSort">Referral Phone</a></th>
						<th><a href="" ng-click="orderByField='referral.d'; reverseSort = !reverseSort">Referral Date</a></th>
						<th><a href="" ng-click="orderByField='referral.status'; reverseSort = !reverseSort">Status</a></th>
					</tr>
				</thead>
				
					<tr ng-repeat="referral in referrals  | filter: searchKeyword | orderBy:orderByField:reverseSort">
						<td>{{$index + 1}}</td>
						<td class="sno"><a href="<?php echo JAWS_PATH_WEB ?>/search?search_text={{referral.user.encoded_email}}&criterion=email" target="_blank">{{referral.user.name}}</a>
						</td>
						
						<td>{{referral.user.email}}</td>
						
						<td >{{referral.referral.n}}
							<br/><a ng-if="referral.referral.encoded_e" href="<?php echo JAWS_PATH_WEB ?>/search?criterion=email&search_text={{referral.referral.encoded_e}}" target="_blank">Search</a>
						</td>
						
						<td>{{referral.referral.e}}</td>
						<td>{{referral.referral.p}}</td>
						<td>{{referral.referral.d}}</td>
						<td style="background:{{referral.referral.color}}">{{referral.referral.status}}
						<br/><a style="cursor:pointer; color:#009cd9;" ng-if="referral.referral.status=='Awaiting voucher approval' " confirmed-click="voucher_notification(referral.user.name,referral.user.email,referral.user.user_id,referral.user.src,referral.referral.user_id)" ng-confirm-click="Are you sure you want to send notification for voucher awarded?" >Send Notification</a>
						</td>
					</tr>
				
			</table>
		</div>
		</center>
		<script type="text/javascript">var referrer =<?php echo json_encode($refer); ?>;</script>
		
		<script src="common/refer/controller.js"></script>
	</body>
</html>
