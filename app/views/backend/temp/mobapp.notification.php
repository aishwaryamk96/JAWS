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
    load_module("ui");
    load_module("course");
    load_module("webinar");
	load_module("activity");

    // Init Session
    auth_session_init();

    // Prep
    $login_params["return_url"] = JAWS_PATH_WEB."/mobnotify";

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
    if (!auth_session_is_allowed("mobile.notification")) {
        ui_render_msg_front(array(
                "type" => "error",
                "title" => "Jigsaw Academy",
                "header" => "No Tresspassing",
                "text" => "Sorry, but you do not have permission to access this page.<br/>Contact Jigsaw Support team for assistance."
                ));
        exit();
    }

    load_library("mobile.notification");
    load_module("user");

    if (isset($_POST["operation"]) && isset($_POST["notification_type"]))
    {
    	$gcm_ids = array();
		$user_id_arr = array();
    	switch($_POST["operation"])
    	{
    		case "calendar":
    			// Get the list of students enrolled in this section
    			if ($_POST["course_id"] == "All") {
    				break;
    			}
    			$url = "https://jigsawacademy.net/app/gs.php";
				$data = array("section" => $_POST["section_id"], "course" => $_POST["course_id"]);
				$options = array(
		    		"http" => array(
		        		"header"  => "Content-type: application/x-www-form-urlencoded\r\n",
		        		"method"  => "POST",
		        		"content" => http_build_query($data),
		    			),
					);
				$context  = stream_context_create($options);
				$result = file_get_contents($url, false, $context);
				$ret_data = json_decode($result, true);

				//activity_debug_start();

				// Get the GCM IDs associated with each Jig ID that was received from LMS
				foreach ($ret_data as $jig_id)
    			{
    				activity_debug_log($jig_id);
    				$user_id = db_query("SELECT user_id FROM user_enrollment WHERE sis_id=".db_sanitize($jig_id)." LIMIT 1");
    				if (!$user_id)
    					continue;
    				$reg_gcm_ids = user_content_get($user_id[0]["user_id"], "gcm_id");
    				if (!$reg_gcm_ids)
    					continue;
    				if (strlen($reg_gcm_ids) == 0)
    					continue;
    				$reg_gcm_ids = json_decode($reg_gcm_ids, true);
    				foreach ($reg_gcm_ids as $reg_gcm_id)
    					$gcm_ids[] = $reg_gcm_id;
					$user_id_arr[] = $user_id[0]["user_id"];
    			}
    			// If the notification is for class cancellation, the message will be slightly different from other cases
				if ($_POST["notification_type"] == "2")
					$message = array('is_student' => 'Y', 'title' => $_POST["title"], 'message' => $_POST["description"], 'type' => $_POST['notification_type'], 'type_id' => $_POST['event_id']);
				else
					$message = array('is_student' => 'Y', 'title' => $_POST["title"], 'message' => $_POST["description"], 'type' => $_POST['notification_type'], 'type_id' => $_POST['section_id']);
				break;

			case "misc":
				switch($_POST["category"])
				{
					case "1":
						if ($_POST["course"] == "0" || $_POST["course"] == "All") {
							$user_ids = db_query("SELECT DISTINCT user_id FROM user_enrollment WHERE status='active'");
							// $user_ids = [["user_id" => 18], ["user_id" => 16767], ["user_id" => 13683], ["user_id" => 4108]];
						}
						else
							$user_ids = db_query("SELECT DISTINCT user_id FROM user_enrollment INNER JOIN course ON course.course_id = user_enrollment.course_id WHERE course.sis_id=".db_sanitize($_POST["course"]).";");
						foreach ($user_ids as $user_id)
						{
							$reg_gcm_ids = user_content_get($user_id["user_id"], "gcm_id");
							if (!$reg_gcm_ids)
								continue;
							if (strlen($reg_gcm_ids) == 0)
								continue;
							$reg_gcm_ids = json_decode($reg_gcm_ids, true);
							foreach ($reg_gcm_ids as $reg_gcm_id)
    							$gcm_ids[] = $reg_gcm_id;
							$user_id_arr[] = $user_id["user_id"];
						}
						$message = array('is_student' => 'Y', 'title' => $_POST["title"], 'message' => $_POST["description"], 'type' => $_POST['notification_type'], "type_id" => 1);
						break;

					case "2":
						$user_ids = db_query("SELECT user_id FROM user WHERE user_id NOT IN (SELECT DISTINCT user_id FROM user_enrollment)");
						foreach ($user_ids as $user_id)
						{
							$reg_gcm_ids = user_content_get($user_id["user_id"], "gcm_id");
    						if (strlen($reg_gcm_ids) == 0)
    							continue;
    						$reg_gcm_ids = json_decode($reg_gcm_ids, true);
    						foreach ($reg_gcm_ids as $reg_gcm_id)
    							$gcm_ids[] = $reg_gcm_id;
							$user_id_arr[] = $user_id["user_id"];
						}
						$message = array('is_student' => 'N', 'title' => $_POST["title"], 'message' => $_POST["description"], 'type' => $_POST['notification_type'], "type_id" => 2);
						break;
				}
				break;

			case "webinar":
				$user_ids = db_query("SELECT user_id FROM webinar_reg WHERE webinar_session_id=".$_POST["webinar_id"].";");
				foreach ($user_ids as $user_id)
				{
					$reg_gcm_ids = user_content_get($user_id["user_id"], "gcm_id");
					if (strlen($reg_gcm_ids) == 0)
						continue;
					$reg_gcm_ids = json_decode($reg_gcm_ids, true);
					foreach ($reg_gcm_ids as $reg_gcm_id)
						$gcm_ids[] = $reg_gcm_id;
					$user_id_arr[] = $user_id["user_id"];
				}
				$message = array('is_student' => 'N', 'title' => $_POST["title"], 'message' => $_POST["description"], 'type' => $_POST['notification_type'], "type_id" => $_POST["webinar_id"]);
				break;

			case "coupon":
				$user_ids = db_query("SELECT context_id FROM system_activity WHERE act_type='app.coupon.avail' AND act_date > DATE_SUB(CURDATE(), INTERVAL 15 DAY)");
				foreach ($user_ids as $user_id)
				{
					$reg_gcm_ids = user_content_get($user_id["user_id"], "gcm_id");
					if (strlen($reg_gcm_ids) == 0)
						continue;
					$reg_gcm_ids = json_decode($reg_gcm_ids);
					foreach ($reg_gcm_ids as $reg_gcm_id)
						$gcm_ids[] = $reg_gcm_id;
					$user_id_arr[] = $user_id["user_id"];
				}
				$message = array('title' => $_POST["title"], 'message' => $_POST["description"], 'type' => $_POST['notification_type'], "type_id" => 1);
				if ($_POST["category"] == "1")
					$message["is_student"] = "Y";
				else
					$message["is_student"] = "N";
				break;
    	}

    	// There are no Users for the notification to be sent to
    	if (count($gcm_ids) == 0)
		{
			$message["uids"] = "NIL";
			activity_create("ignore", "mobapp.notify", "mobapp.notify.send.log", "", "", "", "", json_encode($message), "logged");
    		echo "No registered GCM IDs found";
		}
    	else
    	{
			$message["uids"] = json_encode($user_id_arr);
			activity_create("ignore", "mobapp.notify", "mobapp.notify.send.log", "", "", "", "", json_encode($message), "logged");
			unset($message["uids"]);
			echo "Notification sent to ".count($user_id_arr)." users. Check logs for details.<br />";
    		echo "<br />Google says: ";
    		echo "<pre>";
    		$chunks = array_chunk($gcm_ids, 1000);
    		foreach ($chunks as $chunk) {
    			notification_send($chunk, $message);
    		}
    		// notification_send($gcm_ids, $message);
    	}
    	exit();
    }

    $courses = db_query("SELECT * FROM course WHERE status != 'disabled'");
    array_unshift($courses, ["sis_id" => "All", "name" => "All"]);
    $webinars = webinar_session_get_upcoming();
?>

<html>
<head>
    <title>Mobile Application Calendar Notification Dashboard - JAWS</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
    <script>
    	$(document).ready(function() {
    		$("#user_type").change(function() {
    			if($("#user_type :selected").val() == "1") {
    				$("#notification_type").html("<option value='0'>Select</option><option value='1'>Class Rescheduled</option><option value='2'>Class Cancelled</option><option value='3'>New Class Added</option><option value='4'>Course Delivery Type Changed</option><option value='6'>Miscellaneous</option><option value='8'>Coupon Reminder</option>");
    				$("#student").css("display", "block");
					$("#non-misc").css("display", "block");
    				$("#coupon").css("display", "none");
    				$("#webinar").css("display", "none");
    				$("#user").css("display", "none");
    				$("#title").val("");
    				$("#description").val("");
					$("#title").attr("readonly", "readonly");
					$("#description").attr("disabled", "disabled");
					$("#notification_send").attr("disabled", "disabled");
    			}
    			else if ($("#user_type :selected").val() == "2") {
    				$("#notification_type").html("<option value='0'>Select</option><option value='7'>Webinar Reminder</option><option value='8'>Coupon Reminder</option><option value='6'>Miscellaneous</option>");
    				$("#student").css("display", "none");
    				$("#user").css("display", "none");
    				$("#title").val("");
    				$("#description").val("");
					$("#title").attr("readonly", "readonly");
					$("#description").attr("disabled", "disabled");
					$("#notification_send").attr("disabled", "disabled");
    			}
    			else {
    				$("#notification_type").html("<option value='0'>Select</option>");
    				$("#student").css("display", "none");
    				$("#coupon").css("display", "none");
    				$("#webinar").css("display", "none");
    				$("#user").css("display", "none");
    				$("#title").val("");
    				$("#description").val("");
    				$("#title").removeAttr("readonly");
					$("#description").removeAttr("disabled");
					$("#notification_send").removeAttr("disabled");
    			}
    		});

    		$("#notification_type").change(function() {
				if ($("#notification_type").val() <= "4")
					$("#non-misc").css("display", "block");
    			if($("#notification_type").val() == "7") {
    				$("#webinar").css("display", "block");
    				$("#coupon").css("display", "none");
    			}
    			else if($("#notification_type").val() == "8") {
    				$("#student").css("display", "none");
    				$("#coupon").css("display", "block");
    				$("#webinar").css("display", "none");
    			}
    			else if($("#notification_type").val() == "6") {
					if ($("#user_type :selected").val() == "1") {
	    				$("#student").css("display", "block");
						$("#non-misc").css("display", "none");
					}
					else
						$("#student").css("display", "none");
    				$("#coupon").css("display", "none");
    				$("#webinar").css("display", "none");
    				$("#title").val("");
    				$("#description").val("");
    				$("#title").removeAttr("readonly");
					$("#description").removeAttr("disabled");
					$("#notification_send").removeAttr("disabled");
    			}
    		});

    		$("#course").change(function() {
				if($("#course :selected").val() != "0") {
					$.post("https://jigsawacademy.net/app/rc.php", { course : $("#course").val() }, function(data, status) {
						$("#section").html(data);
					});
	                $("#calendar_message").val('');
	                $("#user_id").val('');
				}
			});

			$("#section").change(function() {
				if($("#section :selected").val() != "0") {
					if($("#section :selected").val() == "-1")
						$.post("https://jigsawacademy.net/app/rc.php", { section : $('#section').val(), course_only : $("#course").val() }, function(data, status) {
						$("#calendar").html(data);
					});
					else
						$.post("https://jigsawacademy.net/app/rc.php", { section : $('#section').val() }, function(data, status) {
						$("#calendar").html(data);
					});
				}
			});

			$("#calendar").change(function() {
				if($("#calendar :selected").val() != "0") {
					$("#title").val($("#calendar :selected").text());
					$("#title").removeAttr("readonly");
					$("#description").removeAttr("disabled");
					$("#notification_send").removeAttr("disabled");
				}
				else
				{
					$("#title").val("");
					$("#description").val("");
					$("#title").attr("readonly", "readonly");
					$("#description").attr("disabled", "disabled");
					$("#notification_send").attr("disabled", "disabled");
				}
			});

			$("#webinar").change(function() {
				if($("#webinar :selected").val() != "0") {
					$("#title").val($("#webinar :selected").text());
					$("#description").val($("#"+$("#webinar :selected").val()).data("date"));
					$("#title").removeAttr("readonly");
					$("#description").removeAttr("disabled");
					$("#notification_send").removeAttr("disabled");
				}
				else
				{
					$("#title").val("");
					$("#description").val("");
					$("#title").attr("readonly", "readonly");
					$("#description").attr("disabled", "disabled");
					$("#notification_send").attr("disabled", "disabled");
				}
			});

			$("#coupon").change(function() {
				if($("#coupon :selected").val() != "0") {
					$("#title").val("Jigsaw Mobile App Coupon");
					$("#description").val("Rs. 500 off");
					$("#title").removeAttr("readonly");
					$("#description").removeAttr("disabled");
					$("#notification_send").removeAttr("disabled");
				}
				else {
					$("#title").val("");
					$("#description").val("");
					$("#title").attr("readonly", "readonly");
					$("#description").attr("disabled", "disabled");
					$("#notification_send").attr("disabled", "disabled");
				}
			});

			// Have to add checks for empty fields==============================//////////////////////////////////////////////
			$("#notification_send").click(function() {
				switch($("#notification_type").val()) {
					case "1":
					case "2":
					case "3":
					case "4":
						$.post(window.location.href, {
								operation : "calendar",
								notification_type : $("#notification_type :selected").val(),
								course_id : $("#course :selected").val(),
								section_id : $("#section :selected").val(),
								event_id : $("#calendar :selected").val(),
								title : $("#title").val(),
								description : $("#description").val()
							}, function(data, status) { $("#user_id").html(data); });
						break;
					case "6":
						$.post(window.location.href, {
								operation : "misc",
								notification_type : $("#notification_type :selected").val(),
								category: $("#user_type :selected").val(),
								course: ($("#user_type :selected").val() == "1" ? $("#course :selected").val() : undefined),
								title : $("#title").val(),
								description : $("#description").val()
							}, function(data, status) { $("#user_id").html(data); });
						break;
					case "7":
						$.post(window.location.href, {
								operation : "webinar",
								notification_type : $("#notification_type :selected").val(),
								webinar_id : $("#webinar :selected").val(),
								title : $("#title").val(),
								description : $("#description").val()
							}, function(data, status) { $("#user_id").html(data); });
						break;
					case "8":
						$.post(window.location.href, {
								operation : "coupon",
								notification_type : $("#notification_type :selected").val(),
								coupon_id : $("#coupon :selected").val(),
								title : $("#title").val(),
								description : $("#description").val(),
								category : $("#user_type :selected").val()
							}, function(data, status) { $("#user_id").html(data); });
						break;
				}
			});
    	});
    </script>
</head>
<body>
	<div>
        <center>
            You are logged in as: <?php echo $_SESSION["user"]["name"]; ?> <a href="<?php echo JAWS_PATH_WEB."/logout"; ?>">Logout</a>
        </center>
    </div><br />
    <div id="container">
    	User type:
    	<select id="user_type">
    		<option value="0">Select</option>
    		<option value="1">Student</option>
    		<option value="2">Non-Student</option>
    	</select><br /><br />
        Notification Type:
        <select id="notification_type">
            <option value="0">Select</option>
        </select><br /><br />
        <div id="student" style="display:none">
	        Course:
	        <select id="course">
	        	<option value="0">Select</option>
	        	<?php foreach ($courses as $course)
	        	{ ?>
	        		<option value="<?php echo $course["sis_id"] ?>"><?php echo $course["name"] ?></option> <?php
	        	} ?>
	        </select><br /><br />
			<div id="non-misc">
	        	Section:
		        <select id="section">
		        	<option value="0">Select</option>
	    	    </select><br /><br />
				Calendar Event:
			    <select id="calendar">
			      	<option value="0">Select</option>
	    		</select><br /><br />
			</div>
	    </div>
	    <div id="webinar" style="display:none">
	        Webinar:
	        <div id="webinar_date" style="display:none">
	        <?php foreach ($webinars as $webinar)
	        { ?>
	        	<div id="<?php echo $webinar["webinar_session"]["webinar_session_id"] ?>" data-date="<?php echo $webinar["webinar_session"]["start_date"] ?>"></div> <?php
	        } ?>
	        </div>
	        <select id="webinar">
	        	<option value="0">Select</option>
	        	<?php foreach ($webinars as $webinar)
	        	{ ?>
	        		<option value="<?php echo $webinar["webinar_session"]["webinar_session_id"] ?>"><?php echo $webinar["desc"] ?></option> <?php
	        	} ?>
	        </select><br /><br />
	    </div>
	    <div id="coupon" style="display:none">
	        Coupon:
	        <select id="coupon">
	        	<option value="0">Select</option>
	        	<option value="1">JAAPP500</option>
	        </select><br /><br />
	    </div>
	    <div id="user" style="display:none">
	    	Email ID:
	    	<input id="email" type="email" placeholder="of the user" /><br /><br />
	    	OR<br /><br />
	    	GCM ID:
	    	<input id="gcm" type="text" placeholder="of the user" /><br /><br />
	    </div>
	    <div id="notification_msg">
	    	Title:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	    	<input type="text" id="title" placeholder="Notification Title" size="50" readonly/><br />
	    	Description:
	    	<textarea id="description" rows="4" cols="50" disabled></textarea><br /><br />
	    	<button id="notification_send" disabled>Send Notification</button>
	    </div>
	    <div id="user_id">
	    </div>
    </div>
</body>
</html>
