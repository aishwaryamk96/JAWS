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
    	header('Location: ../index.php');
    	die();
  	}

  	header('Access-Control-Allow-Origin: *');

  	load_module("ui");

  	 // Init Session
    auth_session_init();

    // Prep
    $login_params["return_url"] = JAWS_PATH_WEB."/mobjobs";

    // Login Check
    if (!auth_session_is_logged()) {
        if (isset($_POST["jobs"]))
        {
            echo "Please login before saving.";
            exit();
        }
        ui_render_login_front(array(
                    "mode" => "login",
                    "return_url" => $login_params["return_url"],
                    "text" => "Please login to access this page."
                ));
        exit();
    }

    // Priviledge Check
    if (!auth_session_is_allowed("mobapp.jobs")) {
        ui_render_msg_front(array(
                "type" => "error",
                "title" => "Jigsaw Academy",
                "header" => "No Tresspassing",
                "text" => "Sorry, but you do not have permission to access this page.<br/>Contact Jigsaw Support team for assistance."
                ));
        exit();
    }

    load_library ("misc");

    if (isset($_POST["jobs"]))
    {
        $jobs = array();
        content_set("mobile.jobs", 0, "empty");
        foreach ($_POST["jobs"] as $job)
            $jobs[] = array(
                "id" => $job["id"],
                "t" => $job["t"], /* Title */
                "d" => $job["d"], /* Description */
                "co" => $job["co"], /* Company */
                "ci" => $job["ci"], /* City */
                "v" => $job["v"], /* Vacancies */
                "s" => $job["s"] /* Status */
            );
        content_set("mobile.jobs", 0, json_encode($jobs));
        echo count($jobs)." jobs saved successfully!";
        exit();
    }

    $jobs = json_decode(content_get("mobile.jobs"), true);

?>

<html>
<head>
    <title>Jobs Portal - JAWS</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
    <script type="text/javascript">
        var job_count = <?php echo count($jobs) + 1 ?>;
        $(document).ready(function() {
            $("body").on("click", ".job_delete", function() {
                var id = $(this).attr("id").substr("job_delete_".length);
                var div = $("#job_" + id);
                div.find("div").css("opacity", "0.2");
                $(this).attr("disabled", true);
                $("#job_undelete_" + id).removeAttr("style");
            });

            $("body").on("click", ".job_undelete", function() {
                var id = $(this).attr("id").substr("job_undelete_".length);
                var div = $("#job_" + id);
                div.find("div").css("opacity", "1");
                $("#job_delete_" + id).removeAttr("disabled");
                $(this).css("display", "none");
            });

            $("#add").click(function() {
            	var div = $("#job_new").clone();
            	div.attr("id", "job_" + job_count);
            	div.find("#inner_").attr("id", div.find("#inner_").attr("id") + job_count);
            	div.find("#job_id").append("Job " + job_count + ":");
            	div.find("#job_id").removeAttr("id");
            	div.find("#job_delete_").attr("id", div.find("#job_delete_").attr("id") + job_count);
            	div.find("#job_undelete_").attr("id", div.find("#job_undelete_").attr("id") + job_count);
            	div.css("display", "block");
                $("#jobs_list").append(div);
                $("#jobs_list").append("<br /><br />");
                job_count++;
            });

            $("#save").click(function() {
            	var post_job = [];
            	$("#jobs_list").find("div").each(function() {
            		var inner_div = $(this).find("#inner_" + $(this).attr("id").substr("job_".length));
            		if (inner_div.css("opacity") == "1")
            		{
            			var job = { 
            				id : inner_div.attr("id").substr("inner_".length),
            				t : inner_div.find("#job_title").val(),
            				d : inner_div.find("#job_desc").val(),
            				co : inner_div.find("#job_company").val(),
            				ci : inner_div.find("#job_city").val(),
            				v : inner_div.find("#job_vacancy").val(),
            				s : inner_div.find("#job_status").val()
            			}
            			post_job.push(job);
            		}
            	});
            	$.post(window.location.href, { jobs : post_job }, function(data, status) { alert(data); });
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
    <div id="jobs_list">
        <?php foreach ($jobs as $job) { ?>
            <div id="job_<?php echo $job["id"] ?>">
                <div id="inner_<?php echo $job["id"] ?>">
                    <label>Job <?php echo $job["id"] ?>:</label><br />
                    <label for="job_title">Title:</label><input id="job_title" type="input" value="<?php echo $job["t"] ?>" /><br />
                    <label for="job_desc">Description:</label><textarea id="job_desc" rows="3" cols="80"><?php echo $job["d"] ?></textarea><br />
                    <label for="job_company">Company:</label><input id="job_company" type="input" value="<?php echo $job["co"] ?>" /><br />
                    <label for="job_city">City:</label><input id="job_city" type="input" value="<?php echo $job['ci'] ?>" /><br />
                    <label for="job_vacancy">Vacancies:</label><input id="job_vacancy" type="number" value="<?php echo $job["v"] ?>" /><br />
                    <label for="job_status">Status:</label>
                    <select id="job_status">
                    	<option value="0" <?php echo ($job["s"] == 0 ? "selected" : "") ?>>Inactive</option>
                    	<option value="1" <?php echo ($job["s"] == 1 ? "selected" : "") ?>>Active</option>
                    </select><br />
                </div>
                <button class="job_delete" id="job_delete_<?php echo $job["id"] ?>">Delete</button> <button class="job_undelete" style="display:none" id="job_undelete_<?php echo $job["id"] ?>">Undelete</button>
            </div><br /><br />
        <?php } ?>
    </div>
    <button id="add">Add New</button> <button id="save">Save</button>
    <div style="display:none" id="job_new">
        <div id="inner_">
            <label id="job_id"></label><br />
            <label for="job_title">Title:</label><input id="job_title" type="input" value="" /><br />
            <label for="job_desc">Description:</label><textarea id="job_desc" rows="3" cols="80"></textarea><br />
            <label for="job_company">Company:</label><input id="job_company" type="input" value="" /><br />
            <label for="job_city">City:</label><input id="job_city" type="input" value="" /><br />
            <label for="job_vacancy">Vacancies:</label><input id="job_vacancy" type="number" value="" /><br />
            <label for="job_status">Status:</label>
            <select id="job_status">
            	<option value="0" selected>Inactive</option>
            	<option value="1">Active</option>
            </select><br />
    	</div>
    	<button class="job_delete" id="job_delete_">Delete</button> <button class="job_undelete" style="display:none" id="job_undelete_">Undelete</button>
    </div>
    <div id="temp">
    </div>
</body>
</html>
