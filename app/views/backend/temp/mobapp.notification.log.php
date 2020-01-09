<?php

load_module("ui");

 // Init Session
auth_session_init();

// Prep
$login_params["return_url"] = JAWS_PATH_WEB."/search";

// Login Check
if (!auth_session_is_logged()) {
	ui_render_login_front(array(
        	"mode" => "login",
        	"return_url" => $login_params["return_url"],
        	"text" => "Please login to access this page."
    	));
	exit();
}

if (!auth_session_is_allowed("mobapp.notify.log.get")) {
   	ui_render_msg_front(array(
           	"type" => "error",
           	"title" => "Jigsaw Academy",
           	"header" => "No Tresspassing",
           	"text" => "Sorry, but you do not have permission to access this page.<br/>Contact Jigsaw Support team for assistance."
       	));
   	exit();
}

if (isset($_POST["start_date"]))
{
	$res_logs = db_query("SELECT * FROM system_activity WHERE act_date>=".db_sanitize($start_date->format("Y-m-d H:i:s"))." AND act_date <".db_sanitize($end_date->format("Y-m-d H:i:s")).";");

	if(isset($res_logs[0]))
	{
		echo "no results found";
		exit();
	}

	$string = "Type,Title,Description,Is Student?\r\n";
	foreach ($res_logs as $log)
	{
		$content = json_decode($row["content"], true);
		$string .= $content["type"].",".$content["title"].",".$content["message"].",".$content["is_student"]."\r\n";
		if (strcmp($content["uids"], "NIL") != 0)
		{
			$uids = json_decode($content["uids"], true);
			foreach($uids as $uid)
				$string .= $uid[0]["user_id"]."\r\n";
		}
	}

	$file = fopen("external/temp/mobnotifylog.csv", "w");
	fwrite($file, $string);
	fclose($file);

	header('Pragma: public');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Cache-Control: private', false);
	header('Content-Type: text/csv');
	header('Content-Disposition: attachment; filename="'. basename("mobnotifylog.csv") . '"');
	header('Content-Transfer-Encoding: binary');
	header('Content-Length: ' . filesize("mobnotifylog.csv"));
	ob_clean();
	readfile("mobnotifylog.csv");
	exit();
}

?>

<html>
<head>
<title></title>
</head>	
 <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js" type="text/javascript"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"
            type="text/javascript"></script>
    <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/start/jquery-ui.css"
          rel="Stylesheet" type="text/css" />
    <script type="text/javascript">
        $(function () {
            $("#txtFrom").datepicker({
                numberOfMonths: 1,
                onSelect: function (selected) {
                    var dt = new Date(selected);
                    dt.setDate(dt.getDate() + 1);
                    $("#txtTo").datepicker("option", "minDate", dt);
                }
            });
            $("#txtTo").datepicker({
                numberOfMonths: 1,
                onSelect: function (selected) {
                    var dt = new Date(selected);
                    dt.setDate(dt.getDate() - 1);
                    $("#txtFrom").datepicker("option", "maxDate", dt);
                }
            });
        });
    </script>
<body>
<div class="container">
<div class="main">
<form  method="post" action="index.php" >
<label>START_DATE :</label>
<td>
<input type="text" id="txtFrom" name = "start_date" />

<br><br>      
<label> END_DATE :</label>
 
<td><input type="text" id="txtTo" name = "end_date"/></td>
<br><br>
<input type = "submit" name = "submit" id="inputid" value = "Submit"> 
</form>
</div>
</div>
</form>
</body>
</html>