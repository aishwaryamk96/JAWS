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
    $login_params["return_url"] = JAWS_PATH_WEB."/leads.export";

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

    	$fromdate = db_sanitize($_POST["fromdate"]);
    	$todate = db_sanitize($_POST["todate"]);

    	$leads = db_query(
    		"SELECT
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
				`ip` AS 'IP',
				meta
			FROM
				`user_leads_basic_compiled`
			WHERE
				(create_date >= $fromdate) AND
				(create_date <= $todate)
			ORDER BY
				`create_date` ASC;"
		);

    	$data = [];
		foreach ($leads as $lead) {

			$lead["meta"] = implode(",", json_decode($lead["meta"] ?: "[]", true));
			$data[] = $lead;

		}

		// Prep
		$lqueryres_arr = [
		        [
		            "title" => "Web Activity",
		            "data" => $data
		        ]
		    ];

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
			<form method="POST" action="https://www.jigsawacademy.com/jaws/leads.export" style="text-align: left; width: 50vw; margin: 0 auto;">
				From Date : <input type="date" required name="fromdate" max="<?= date("Y-m-d"); ?>"><br/>
				To Date : <input type="date" required name="todate" max="<?= date("Y-m-d"); ?>" value="<?= date("Y-m-d"); ?>"><br/><br/>
				<input type="submit" value="Download Report"><br/><br/>
				<span style="color: gray;">Warning : Selecting a large range may not function properly. Try to keep the range within 20 days.</span>
			</form>
			</center>
		</body>
    	</html>
    	<?php

    }
?>
