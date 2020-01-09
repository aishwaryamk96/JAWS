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
    $login_params["return_url"] = JAWS_PATH_WEB."/enrform";

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
	            "title" => "Enrollments Report",

	            "data" => db_query("SELECT
	            				user.name as 'Student Name',
	            				user.phone as 'Student Phone',
	            				user.email as 'Student Email',
	            				meta.city AS 'City',
	            				meta.state AS 'State',
	            				pay.sum_total as 'Sum (w/ Tax)',	
	            				course.name AS 'Course',
	            				(CASE WHEN enr.learn_mode = 'sp' THEN 'Regular' ELSE 'Premium' END) AS 'Mode'	            				
	            			FROM
	            				payment AS pay
	            			INNER JOIN
	            				user AS user
	            				ON user.user_id = pay.user_id
	            			INNER JOIN
	            				user_meta as meta
	            				ON meta.user_id = pay.user_id 
	            			INNER JOIN
	            				user_enrollment AS enr
	            				ON enr.subs_id = pay.subs_id
	            			INNER JOIN
	            				course
	            				ON course.course_id = enr.course_id
	            			WHERE
	            				pay.create_date >= '".$_POST["fromdate"]."' AND
	            				pay.create_date <= '".$_POST["todate"]."'
	            			ORDER BY
	            				pay.create_date ASC, pay.user_id ASC;
	            			")
	            )	            
	);

	$prop = array(
	        "title" => "Enrollments (".date("F j, Y").")",
	        "category" => "payments sales enrollments"
	);

	// Download Leads
	phpexcel_write($lqueryres_arr, $prop, "Enrollments (".date("F j, Y").").xls");
	exit();

    }

    else {

    	?>
    	<html>
		<head>
			<title>JAWS - Enrollments Report (Temp)</title>
		</head>
		<body>
			<center>
			<b>Enrollments Report Download</b>&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: gray;">Logged in as <?php echo $_SESSION["user"]["name"]; ?>!</span>&nbsp;<a href="https://www.jigsawacademy.com/jaws/logout">Logout</a>
			<hr>
			<form method="POST" action="https://www.jigsawacademy.com/jaws/enrform" style="text-align: left; width: 50vw; margin: 0 auto;">
				From Date : <input type="date" required name="fromdate" max="<?php echo date("d-m-Y"); ?>" value="<?php echo date("d-m-Y"); ?>"><br/>
				To Date : <input type="date" required name="todate" max="<?php echo date("d-m-Y"); ?>" value="<?php echo date("d-m-Y"); ?>"><br/><br/>
				<input type="submit" value="Download Report"><br/><br/>
				<span style="color: gray;">Warning : Selecting a large range may not function properly. Try to keep the range within 30 days.</span>
			</form>
			</center>
		</body>
    	</html>
    	<?php

    }
?>