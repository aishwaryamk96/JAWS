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
    $login_params["return_url"] = JAWS_PATH_WEB."/moblog";

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
    if (!auth_session_is_allowed("mobapi.log.get")) {
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
	            "title" => "Users Registered",

	            "data" => db_query("SELECT
	            				log.activity AS 'API',
	            				log.entity_type AS 'Associated Entity',
	            				user.email AS 'User Email',
	            				user.phone AS 'User Phone',
	            				log.content AS 'Request JSON',
	            				log.act_date AS 'Date'
	            			FROM
	            				system_activity AS log
	            			LEFT JOIN
	            				user AS user
	            				ON log.entity_id = user.user_id
	            				AND log.entity_type = 'user' 
	            			WHERE	            				
	            				log.act_date >= '".$_POST["fromdate"]."' AND
	            				log.act_date <= '".$_POST["todate"]."' AND
	            				log.act_type = 'mobapi'
	            			ORDER BY
	            				log.act_date ASC;
	            			")
	           ));	

	$prop = array(
	        "title" => "MobAPI Log (".date("F j, Y").")",
	        "category" => "mobile app"
	);

	// Download Leads
	phpexcel_write($lqueryres_arr, $prop, "MobAPI Log (".date("F j, Y").").xls");
	exit();

    }

    else {

    	?>
    	<html>
		<head>
			<title>JAWS - Mobile App API Log (Temp)</title>
		</head>
		<body>
			<center>
			<b>Mobile API Log</b>&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: gray;">Logged in as <?php echo $_SESSION["user"]["name"]; ?>!</span>&nbsp;<a href="https://www.jigsawacademy.com/jaws/logout">Logout</a>
			<hr>
			<form method="POST" action="https://www.jigsawacademy.com/jaws/moblog" style="text-align: left; width: 50vw; margin: 0 auto;">
				From Date : <input type="date" required name="fromdate" max="<?php echo date("d-m-Y"); ?>" value="<?php echo date("d-m-Y"); ?>"><br/>
				To Date : <input type="date" required name="todate" max="<?php echo date("d-m-Y"); ?>" value="<?php echo date("d-m-Y"); ?>"><br/><br/>
				<input type="submit" value="Download Report"><br/><br/>
				<span style="color: gray;">Warning : Selecting a large range may not function properly. Try to keep the range within 15 days.</span>
			</form>
			</center>
		</body>
    	</html>
    	<?php

    }
?>
