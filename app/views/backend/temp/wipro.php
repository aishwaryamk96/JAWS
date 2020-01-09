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
    $login_params["return_url"] = JAWS_PATH_WEB."/corpform";

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
    if (!auth_session_is_allowed("corp.get")) {
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

	// Prep
	$lqueryres_arr = array(
	        0 => array(
	            "title" => "Wipro Corp Sign-Up",

	            "data" => db_query("SELECT
	            				common_schema.extract_json_value(content, '/name') AS 'Name',
								common_schema.extract_json_value(content, '/email') AS 'Email',
								common_schema.extract_json_value(content, '/email_alt') AS 'Alternate Email',
								common_schema.extract_json_value(content, '/phone') AS 'Phone',
								common_schema.extract_json_value(content, '/city') AS 'City',
								common_schema.extract_json_value(content, '/country') AS 'Country',
								common_schema.extract_json_value(content, '/office') AS 'Office',
								common_schema.extract_json_value(content, '/empid') AS 'Employee ID',
								common_schema.extract_json_value(content, '/approval') AS 'Approval',
	            				act_date  AS 'Date',
								CASE WHEN status = 'executed' THEN 'Paid' ELSE 'Not Paid' END AS 'Payment Status'
	            			FROM
	            				system_activity
	            			WHERE
								act_type = 'wipro.paylink' AND
	            				act_date >= '".$_POST["fromdate"]."' AND
	            				act_date <= '".$_POST["todate"]."'
	            			ORDER BY
	            				act_date ASC;
	            			")
	            )
	);

	$prop = array(
	        "title" => "Wipro (".date("F j, Y").")",
	        "category" => "payments sales marketing corporate"
	);

	// Download Corp Report
	//echo json_encode($lqueryres_arr);
	try {
		phpexcel_write($lqueryres_arr, $prop, "Wipro (".date("F j, Y").").xls");
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
			<title>JAWS - Wipro Sign-Up Report (Temp)</title>
		</head>
		<body>
			<center>
			<b>Wipro Report Download</b>&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: gray;">Logged in as <?php echo $_SESSION["user"]["name"]; ?>!</span>&nbsp;<a href="https://www.jigsawacademy.com/jaws/logout">Logout</a>
			<hr>
			<form method="POST" action="https://www.jigsawacademy.com/jaws/corpform" style="text-align: left; width: 50vw; margin: 0 auto;">
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
