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

    // Load stuff
    load_plugin("phpexcel");
    load_module("ui");

    // Init Session
    auth_session_init();

    // Prep
    $login_params["return_url"] = JAWS_PATH_WEB."/lform";

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
    if (!auth_session_is_allowed("leads.get")) {
        ui_render_msg_front(array(
                "type" => "error",
                "title" => "Jigsaw Academy",
                "header" => "No Tresspassing",
                "text" => "Sorry, but you do not have permission to access this page.<br/>Contact Jigsaw Support team for assistance."
                ));
        exit();
    }

    //Check if Date is set
    if ((isset($_POST["fromdate"])) && (isset($_POST["todate"]))) {

    	ini_set("memory_limit", "512M");

		// Prep
		$lqueryres_arr = array(
		        0 => array(
		            "title" => "Users Registered",

		            "data" => db_query("SELECT
		            				main.user_id AS 'Lead ID',
		            				main.name AS 'Name',
		            				main.phone AS 'Phone',
		            				main.email AS 'Email',
		            				main.soc_fb AS 'FB',
		            				main.soc_gp AS 'GP',
		            				main.soc_li AS 'LI',
		            				main.photo_url AS 'Photo Link',
		            				meta.reg_date  AS 'Account Created on'
		            			FROM
		            				user AS main
		            			INNER JOIN
		            				user_meta AS meta
		            				ON main.user_id = meta.user_id
		            			WHERE
		            				`status`='active' AND
		            				meta.reg_date >= '".$_POST["fromdate"]."' AND
		            				meta.reg_date <= '".$_POST["todate"]."'
		            			ORDER BY
		            				meta.reg_date ASC;
		            			")
		            ),

		        1 => array(
		            "title" => "Web Activity",

		            "data" => db_query("SELECT
		            				`lead_id` AS 'SNo.',
		            				`name` AS 'Name',
		            				`email` AS 'Email',
		            				`phone` AS 'Phone',
		            				`xuid`,
		            				`utm_source`,
		            				`utm_campaign`,
		            				`utm_term`,
		            				`utm_medium`,
		            				`utm_content`,
		            				`utm_segment`,
		            				`utm_numvisits`,
		            				`gcl_id`,
		            				`global_id_perm`,
		            				`global_id_session`,
		            				`referer` AS 'Landing Referer URL',
		            				`landing_url` AS 'Landing URL',
		            				`page_url` AS 'Activity/CTA URL',
		            				`event` AS 'Activity Performed',
		            				`create_date` AS 'Date',
		            				`ip` AS 'IP'
		            			FROM
		            				`user_leads_basic_compiled`
		            			WHERE
		            				(create_date >= '".$_POST["fromdate"]."') AND
		            				(create_date <= '".$_POST["todate"]."')
		            			ORDER BY
		            				`email` ASC, `create_date` ASC;")
		            ),

		        2 => array(
		            "title" => "Forms Submitted",

		            "data" => db_query("SELECT
		            				`user_id` AS 'Lead ID',
		            				`name` AS 'Name',
		            				`email` AS 'Email',
		            				`phone` AS 'Phone',
		            				`xuid`,
		            				`utm_source`,
		            				`utm_campaign`,
		            				`utm_term`,
		            				`utm_medium`,
		            				`utm_content`,
		            				`utm_segment`,
		            				`utm_numvisits`,
		            				`gcl_id`,
		            				`global_id_perm`,
		            				`global_id_session`,
		            				`referer` AS 'Referer',
		            				`ip` AS 'IP',
		            				`ad_lp` AS 'Landing Page',
		            				`ad_url`,
		            				`create_date` AS 'Submitted on',
		            				`capture_type`
		            			FROM
		            				`user_leads_basic`
		            			WHERE
		            				((`name` IS NOT NULL) OR (`email` IS NOT NULL) OR (`phone` IS NOT NULL)) AND
		            				(`capture_trigger` = 'formsubmit') AND
		            				(create_date >= '".$_POST["fromdate"]."') AND
		            				(create_date <= '".$_POST["todate"]."')
		            			ORDER BY
		            				`create_date` ASC;")
		            ),

		        3 => array(
		            "title" => "Rest",

		            "data" => db_query("SELECT
		            				user.user_id AS 'Lead ID',
		            				user.name AS 'Name',
		            				user.email AS 'Email',
		            				user.phone AS 'Phone',
		            				leads.xuid AS 'XUID',
		            				leads.utm_source,
		            				leads.utm_campaign,
		            				leads.utm_term,
		            				leads.utm_medium,
		            				leads.utm_content,
		            				leads.utm_segment,
		            				leads.utm_numvisits,
		            				leads.gcl_id,
		            				leads.global_id_perm,
		            				leads.global_id_session,
		            				leads.referer AS 'Referer',
		            				leads.ip AS 'IP',
		            				leads.ad_lp AS 'Landing Page',
		            				leads.ad_url,
		            				leads.create_date AS 'Created On',
		            				leads.capture_trigger,
		            				leads.capture_type
		            			FROM
		            				`user_leads_basic` AS leads
		            			INNER JOIN user
		            				ON user.user_id = leads.user_id
		            			WHERE
		            				(leads.user_id IS NOT NULL) AND
		            				(leads.create_date >= '".$_POST['fromdate']."') AND
		            				(leads.create_date <= '".$_POST['todate']."')
		            			ORDER BY
		            				leads.create_date ASC
		            			;")
		            )
		);

		$prop = array(
		        "title" => "Leads (".date("F j, Y").")",
		        "category" => "payments sales marketing"
		);

		// Download Leads
		//echo json_encode($lqueryres_arr);
		try {
			phpexcel_write($lqueryres_arr, $prop, "Leads (".date("F j, Y").").xls");
		}
		catch(Exception $e) {
			var_dump($e);
		}
		exit();

    }

    else {

    	?>
    	<html>
		<head>
			<title>JAWS - L-Form for Leads (Temp)</title>
		</head>
		<body>
			<center>
			<b>Leads Report Download</b>&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: gray;">Logged in as <?php echo $_SESSION["user"]["name"]; ?>!</span>&nbsp;<a href="https://www.jigsawacademy.com/jaws/logout">Logout</a>
			<hr>
			<form method="POST" action="https://www.jigsawacademy.com/jaws/lform" style="text-align: left; width: 50vw; margin: 0 auto;">
				From Date : <input type="date" required name="fromdate" max="<?php echo date("d-m-Y"); ?>" value="<?php echo date("d-m-Y"); ?>"><br/>
				To Date : <input type="date" required name="todate" max="<?php echo date("d-m-Y"); ?>" value="<?php echo date("d-m-Y"); ?>"><br/><br/>
				<input type="submit" value="Download Report"><br/><br/>
				<span style="color: gray;">Warning : Selecting a large range may not function properly. Try to keep the range within 20 days.</span>
			</form>
			</center>
		</body>
    	</html>
    	<?php

    }
?>
