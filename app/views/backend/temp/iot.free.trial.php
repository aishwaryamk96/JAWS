<?php

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
    $login_params["return_url"] = JAWS_PATH_WEB."/iot.free.trial";

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

		// Prep
		$lqueryres_arr = [
			[
				"title" => "Users Registered",

				"data" => db_query(
					"SELECT
						a.content, u.name, u.phone, a.act_date
					FROM
						system_activity AS a
					INNER JOIN
						user AS u
						ON u.email = a.content
					WHERE
						a.act_type = 'jlc.free'
						AND a.act_date >= ".db_sanitize($_POST["fromdate"])."
						AND a.act_date <= ".db_sanitize($_POST["todate"])."
					ORDER BY
						a.act_date ASC;"
				)
			]
		];

		$prop = array(
		        "title" => "Leads (".date("F j, Y").")",
		        "category" => "payments sales marketing"
		);

		// Download Leads
		try {
			phpexcel_write($lqueryres_arr, $prop, "Leads (".date("F j, Y").").xls");
		}
		catch(Exception $e) {
			var_dump($e);
		}

		die;

    }

?>
<html>
<head>
	<title>IoT Free Trial Leads</title>
</head>
<body>
	<center>
		<b>IoT Free Trial Leads Report Download</b>&nbsp;&nbsp;&nbsp;&nbsp;
		<span style="color: gray;">Logged in as <?php echo $_SESSION["user"]["name"]; ?>!</span>&nbsp;
		<a href="https://www.jigsawacademy.com/jaws/logout">Logout</a>
		<hr>
		<form method="POST" action="https://www.jigsawacademy.com/jaws/iot.free.trial" style="text-align: left; width: 50vw; margin: 0 auto;">
			From Date : <input type="date" required name="fromdate" max="<?php echo date("d-m-Y"); ?>" value="<?php echo date("d-m-Y"); ?>"><br/>
			To Date : <input type="date" required name="todate" max="<?php echo date("d-m-Y"); ?>" value="<?php echo date("d-m-Y"); ?>"><br/><br/>
			<input type="submit" value="Download Report"><br/><br/>
			<span style="color: gray;">Warning : Selecting a large range may not function properly. Try to keep the range within 20 days.</span>
		</form>
	</center>
</body>
</html>