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
    $login_params["return_url"] = JAWS_PATH_WEB."/slkform";

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
    if (!auth_session_is_allowed("payment.get")) {
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
	            "title" => "Links Report",

	            "data" => db_query("SELECT
	            				user.name as 'Student Name',
	            				user.phone as 'Student Phone',
	            				user.email as 'Student Email',
	            				pay.sum_basic as 'Sum (w/o Tax)',
	            				pay.sum_total as 'Sum (w/ Tax)',	   
	            				pay.instl_total as 'Total Installments',
	            				instl.instl_count as 'Link-Installment No.',
	            				instl.sum as ' Installment Amount',
	            				link.status as 'Link Status',
	            				instl.pay_date as 'Link Paid On',
	            				instl.pay_mode as 'Payment Mode',
	            				instl.gateway_name as 'Payment Gateway',
	            				instl.gateway_reference as 'Transaction Reference No.',
	            				link.create_date as 'Link Created On',
	            				user2.name as 'Link Created By',
	            				link.web_id as 'Link Web_ID (www.jigsawacademy.com/jaws/pay=web_id)',
	            				instl.notify_count as 'No. of Reminder Emails'
	            			FROM
	            				payment_link AS link
	            			INNER JOIN
	            				user AS user
	            				ON user.user_id = link.user_id
	            			INNER JOIN
	            				user as user2
	            				ON user2.user_id = link.create_entity_id
	            			INNER JOIN
	            				payment AS pay
	            				ON pay.pay_id = link.pay_id
	            			INNER JOIN
	            				payment_instl AS instl
	            				ON instl.instl_id = link.instl_id
	            			WHERE
	            				link.create_entity_type = 'user' AND
	            				link.create_date >= '".$_POST["fromdate"]."' AND
	            				link.create_date <= '".$_POST["todate"]."'
	            			ORDER BY
	            				link.user_id ASC, link.create_date ASC;
	            			")
	            )	            
	);

	$prop = array(
	        "title" => "Paylinks (".date("F j, Y").")",
	        "category" => "payments sales"
	);

	// Download Leads
	phpexcel_write($lqueryres_arr, $prop, "Paylink (".date("F j, Y").").xls");
	exit();

    }

    else {

    	?>
    	<html>
		<head>
			<title>JAWS - Paylinks Report (Temp)</title>
		</head>
		<body>
			<center>
			<b>Paylinks Report Download</b>&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: gray;">Logged in as <?php echo $_SESSION["user"]["name"]; ?>!</span>&nbsp;<a href="https://www.jigsawacademy.com/jaws/logout">Logout</a>
			<hr>
			<form method="POST" action="https://www.jigsawacademy.com/jaws/slkform" style="text-align: left; width: 50vw; margin: 0 auto;">
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