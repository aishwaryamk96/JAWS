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

  	// Init Session
    auth_session_init();

    // Prep
    $login_params["return_url"] = JAWS_PATH_WEB."/referrals";
	
    // Login Check
    if (!auth_session_is_logged()) {
       	ui_render_login_front(array(
                   	"mode" => "login",
                   	"return_url" => $login_params["return_url"],
                   	"text" => "Please login to access this page."
               	));
       	exit();
    }

	if (!auth_session_is_allowed("jlc.referral")) {
		ui_render_msg_front(array(
                	"type" => "error",
                	"title" => "Jigsaw Academy",
                	"header" => "No Tresspassing",
                	"text" => "Sorry, but you do not have permission to access this page.<br/>Contact Jigsaw Support team for assistance."
                	));
        	exit();
	}

	$voucher_award = false;
	if (auth_session_is_allowed("jlc.referral.voucher.award"))
		$voucher_award = true;

	$res_referral_act = db_query("SELECT * FROM system_activity WHERE act_type='jlc.referral' AND activity='referral' ORDER BY act_id DESC;");
	$refer = array();
	$export_list = "Referrer Email,Referrer Name,Email,Name,Phone,Courses\r\n";

	$i = 0;
	$no_action = 0;
	$reg = 0;
	$enrolled = 0;

	foreach ($res_referral_act as $referral)
	{
		if ($referral["context_type"] == "user")
			$user = user_get_by_id($referral["context_id"]);
		else
			$user = json_decode(db_query("SELECT content FROM system_activity WHERE act_id=".$referral["context_id"])[0]["content"], true);
		$user["user_src"] = $referral["context_type"];

		$referrals = json_decode($referral["content"], true)["r"];
		$ref_info = [];
		$referrals = array_reverse($referrals);
		foreach ($referrals as $referred)
		{
			$referred["user_id"] = $user["user_id"];
			$ref_user = user_get_by_email($referred["e"]);
			$referred["status"] = "No action";
			if ($ref_user !== false)
			{
				$referred["user_id"] = $ref_user["user_id"];
				$referred["reg"] = 1;
				$referred["status"] = "Registered";
				if (isset(db_query("SELECT * FROM subs WHERE status='active' AND user_id=".$ref_user["user_id"])[0]))
				{
					$referred["status"] = "Enrolled";
					$enrolled++;
				}
				else
					$reg++;
			}
			else
			{
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
			$ref_info[] = $referred;
		}
		$i += count($ref_info);

		$refer[] = ["user" => $user, "r" => $ref_info];
	}

	// echo "<pre>";
	// print_r($refer); exit;
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

<HTML>
<HEAD>
	<TITLE><?php echo substr($domain_name, 0, -3) ?>Referral List</TITLE>
	<STYLE>
		table td {
			background-color: rgba(0, 0, 0, 0.075);
			text-align:center;
		}
		table th {
			background-color: rgba(0, 0, 0, 0.075);
			text-align:center;
		}
		a {
			font-size: 75%;
		}
		.inner-table {
			font-size: 100%;
			width: 100%;
		}
		.inner-table td {
			background-color: rgba(0, 0, 0, 0.0);
			text-align:center;
		}
		.header {
			position: fixed;
			width: 100%;
			background: white;
			top: 0px;
			padding-top: 10px;
		}
		form {
			margin-bottom: 0px;
		}
	</STYLE>
</HEAD>
<BODY style="font-family: sans-serif; font-size: 90%;">
	<DIV class="header">
    	<CENTER>
	        <B>JLC Referrals</B> (You are logged in as: <?php echo $_SESSION["user"]["name"]; ?> <A href="<?php echo JAWS_PATH_WEB."/logout"; ?>">Logout</A>)
        	<?php if (isset($msg)) echo "<br/>".$msg; ?>
	    	<HR>
	    	<TABLE border="0" cellpadding="10" cellspacing="2" style="font-size: 95%;">
				<THEAD>
					<TR>
						<TH>Total</TH>
						<TH>No Action</TH>
						<TH>Registered</TH>
						<TH>Enrolled</TH>
					</TR>
				</THEAD>
				<TBODY>
					<TR>
						<TD><?php echo $i ?></TD>
						<TD>
							<FORM method="POST">
								<INPUT type="submit" name="export" value="<?php echo $no_action ?>" />
							</FORM>
						</TD>
						<TD><?php echo $reg ?></TD>
						<TD><?php echo $enrolled ?></TD>
					</TR>
				</TBODY>
			</TABLE>
			<HR>
		</CENTER>
	</DIV>
	<CENTER>
		<TABLE border="0" cellpadding="10" cellspacing="2" style="font-size: 95%; margin-top: 133px;">
			<THEAD>
				<TR>
					<TH>Referrer Name</TH>
					<TH>Referrer Email</TH>
					<TH>#</TH>
					<TH>Referral Email</TH>
					<TH>Referral Name</TH>
					<TH>Referral Phone</TH>
					<TH>Referral Date</TH>
					<TH>Status</TH>
				</TR>
			</THEAD>
			<?php foreach ($refer as $referral)
			{ ?>
				<TR>
					<TD><?php echo $referral["user"]["name"] ?></TD>
					<TD><?php echo $referral["user"]["email"] ?><BR /><A HREF="<?php echo JAWS_PATH_WEB ?>/search?search_text=<?php echo urlencode($referral["user"]["email"]) ?>&criterion=email" TARGET="_blank">Search</A></TD>
					<TD>
						<TABLE class="inner-table">
							<?php foreach ($referral["r"] as $referred)
							{ ?>
								<TR>
									<TD><?php echo $i; $i-- ?></TD>
								</TR>
							<?php } ?>
						</TABLE>
					</TD>
					<TD>
						<TABLE class="inner-table">
							<?php foreach ($referral["r"] as $referred)
							{ ?>
								<TR>
									<TD><?php echo $referred["e"].(isset($referred["reg"]) ? "<BR /><A HREF='".JAWS_PATH_WEB."/search?search_text=".urlencode($referred["e"])."&criterion=email' TARGET='_blank'>Search</A>" : "") ?></TD>
								</TR>
							<?php } ?>
						</TABLE>
					</TD>
					<TD>
						<TABLE class="inner-table">
							<?php foreach ($referral["r"] as $referred)
							{ ?>
								<TR>
									<TD><?php echo $referred["n"] ?></TD>
								</TR>
							<?php } ?>
						</TABLE>
					</TD>
					<TD>
						<TABLE class="inner-table">
							<?php foreach ($referral["r"] as $referred)
							{ ?>
								<TR>
									<TD><?php echo $referred["p"] ?></TD>
								</TR>
							<?php } ?>
						</TABLE>
					</TD>
					<TD>
						<TABLE class="inner-table">
							<?php foreach ($referral["r"] as $referred)
							{ ?>
								<TR>
									<TD><?php echo date_create_from_format("Y-m-d H:i:s", $referred["d"])->format("d M Y") ?></TD>
								</TR>
							<?php } ?>
						</TABLE>
					</TD>
					<TD>
						<TABLE class="inner-table">
							<?php foreach ($referral["r"] as $referred)
							{ 
							?>
								<TR>
									<TD><?php echo isset($referred["x"]) ? ($referred["x"] == 1 ? "Awaiting voucher approval".($voucher_award ? "<BR/><a onclick='voucher_notification(".$referred['user_id'].",".$referral['user']['name'].",".$referral['user']['email'].",".$referral['user']['user_id'].",".$referral['user']['user_src'].");''>Send Voucher Notification</a>" : "") : "Voucher awarded") : $referred["status"] ?>
									</TD>
								</TR>
							<?php } ?>
						</TABLE>
					</TD>
				</TR>
			<?php } ?>
		</TABLE>
	</CENTER>

<script>
	function voucher_notification(userId, referrer_name, referrer_email,referrer_id,src)
	{
		$.post("<?php echo JAWS_PATH_WEB ?>/lmsapi/jlc.voucher.notification.php?referral_id="+userId+"&referrer_name="+referrer_name+"&referrer_email="+referrer_email+"&referrer_src="+src+"&referrer_id="+referrer_id, function(data){
			alert(data);
		});
	}
</script>
</BODY>
</HTML>