<?php

	load_module("ui");

	// Init Session
	auth_session_init();

	// Prep
	$return_url = JAWS_PATH_WEB."/lab";

	// Login Check
	if (!auth_session_is_logged()) {

		ui_render_login_front(array(
				"mode" => "login",
				"return_url" => $return_url,
				"text" => "Please login to access this page."
			));
		exit();

	}

	$lab_get = auth_session_is_allowed("lab.get");
	$lab_get_adv = auth_session_is_allowed("lab.get.adv");
	$lab_get_custom = auth_session_is_allowed("lab.get.custom");

	if (!$lab_get && !$lab_get_adv && !$lab_get_custom) {

		ui_render_msg_front(array(
				"type" => "error",
				"title" => "Jigsaw Academy",
				"header" => "No Tresspassing",
				"text" => "Sorry, but you do not have permission to access this page.<br/>Contact Jigsaw Support team for assistance."
			));
		exit();

	}

	$res_lab = false;

	// Update done or undone or export
	if (isset($_REQUEST["operation"])) {

		if ($_REQUEST["operation"] == "u") {

			$domains = [];
			$domains[] = db_sanitize($_REQUEST["data"]["d"]."lab");
			if (strcasecmp($_REQUEST["data"]["d"], "python") == 0) {
				$domains[] = db_sanitize("rlab");
			}
			else if (strcasecmp($_REQUEST["data"]["d"], "r") == 0) {
				$domains[] = db_sanitize("pythonlab");
			}

			if (!empty($_REQUEST["data"]["c"])) {
				db_exec("UPDATE user_enrollment SET lab_status='ul' WHERE enr_id IN (".implode(",", $_REQUEST["data"]["c"]).") AND course_id IN (SELECT course_id FROM course_lab WHERE domain IN (".implode(",",$domains)."));");
			}
			if (!empty($_REQUEST["data"]["uc"])) {
				db_exec("UPDATE user_enrollment SET lab_status='na' WHERE enr_id IN (".implode(",", $_REQUEST["data"]["uc"]).") AND course_id IN (SELECT course_id FROM course_lab WHERE domain IN (".implode(",",$domains)."));");
			}
			die("All records have been updated");

		}

		// Export what was selected
		else if ($_REQUEST["operation"] == "d") {

			$res_enr = db_query("SELECT sis_id AS FN, '', sis_id, lab_pass FROM user_enrollment WHERE enr_id IN (".trim($_REQUEST["data"], ",").");");
			$lines = "FN,LN,Username,Password\r\n";
			foreach ($res_enr as $enr) {

				$line = "";
				foreach ($enr as $key => $value) {
					$line .= $value.",";
				}
				$lines .= trim($line, ",")."\r\n";

			}

			header_download("text/csv", "lab-".date("Y-m-d").".csv");
			$stream = fopen("php://output", "w");
			fwrite($stream, implode("\r\n", $lines));
			fclose($stream);
			exit();

		}

	}
	// A username was searched
	else if (!empty($_GET["sis_id"])) {
		$res_lab = db_query("SELECT DISTINCT enr.enr_id, enr.user_id AS user_id, enr.lab_user AS lab_user, enr.lab_pass AS lab_pass, enr.lab_status AS lab_status, subs.start_date AS start_date, user.email, user.name FROM user_enrollment AS enr INNER JOIN course_lab AS lab ON lab.course_id = enr.course_id INNER JOIN subs ON subs.subs_id = enr.subs_id INNER JOIN user ON user.user_id = enr.user_id WHERE enr.sis_id=".db_sanitize($_GET["sis_id"])." AND lab.domain=".db_sanitize($_GET["domain"]));
	}
	// A domain was selected
	else if (empty($_GET["domain"])) {
		$_GET["domain"] = "RLab";
	}

	// Set the query limit mostly for users who can view
	$limit = 0;
	if (!empty($_GET["page"])) {
		$limit = $_GET["page"] * 30 + 1;
	}

	// Where clause
	$lab_status = ($lab_get ? "" : "na");

	if ($res_lab === false) {

		if (!empty($_GET["show"])) {

			if ($_GET["show"] == "all") {
				$lab_status = "";
			}
			else if ($_GET["show"] == "pending") {
				$lab_status = "na";
			}
			else if ($_GET["show"] == "done") {
				$lab_status = "ul";
			}

		}

		$res_lab = db_query("SELECT DISTINCT enr.enr_id, enr.user_id AS user_id, enr.lab_user AS lab_user, enr.lab_pass AS lab_pass, enr.lab_status AS lab_status, subs.start_date AS start_date, user.email, user.name FROM user_enrollment AS enr INNER JOIN course_lab AS lab ON lab.course_id = enr.course_id INNER JOIN subs ON subs.subs_id = enr.subs_id INNER JOIN user ON user.user_id = enr.user_id WHERE lab.domain=".db_sanitize($_GET["domain"]).($lab_status == "" ? "" : " AND lab_status='".$lab_status."'")." ORDER BY enr.enr_id DESC ".($lab_get ? "LIMIT ".$limit.", ".($limit + 30) : "").";");
	}


	$domain_name = db_query("SELECT DISTINCT domain FROM course_lab WHERE domain=".db_sanitize($_GET["domain"]).";")[0]["domain"];
	$domain_name = substr($domain_name, 0, -3);

	$domains = db_query("SELECT DISTINCT domain FROM course_lab;");

	// Type of records we are showing presently
	$list_type = "pending";
	if ($lab_status == "") {
		$list_type = "all";
	}
	else if ($lab_status == "na") {
		$list_type = "pending";
	}
	else if ($lab_status == "ul") {
		$list_type = "done";
	}

?>
<html>
<head>
	<title><?php echo $domain_name ?> Lab Users</title>
	<style>
		thead th {
			background-color: rgba(0, 0, 0, 0.095);
			text-align:center;
		}
		tr:nth-child(odd) {
			background-color: rgba(0, 0, 0, 0.075);
			text-align:center;
		}
		tr:nth-child(even) {
			background: #FFF;
			text-align:center;
		}
	</style>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script>
$(document).ready(function(){
var c=[],u=[];
$(".btn-done").click(function(){a("u",{"uc":u,"c":c,"d":"<?php echo $domain_name ?>"},true);});
$(".lab-check").click(function(){if(!$(this).prop("checked")){if(c.indexOf(this.id)!=-1)c.splice(c.indexOf(this.id),1);u.push(this.id);}else{if(u.indexOf(this.id)!=-1)u.splice(u.indexOf(this.id),1);c.push(this.id);}/*a("n",this.id);*/});
$(".btn-copy").click(function(){var ct="";$(".line-copy").each(function(){if($(this).prop("checked"))ct+=$($($($(this).parent()[0]).parent()[0]).find(".txt-user")[0]).html()+"\t"+$($($($(this).parent()[0]).parent()[0]).find(".txt-pass")[0]).html()+"\r\n";});$("#copy-text").val(ct);$("#copy-text").css("display","block");$("#copy-text").select();document.execCommand('Copy');$("#copy-text").css("display","none");});
$(".btn-export").click(function(){var es="";$(".line-copy:checked").each(function(){es+=this.id+",";});a("d",es,true);});
$("#list-type").change(function(){window.location.href="<?php echo JAWS_PATH_WEB ?>/lab?show="+$("#list-type").val()+"&domain=<?php echo $domain_name ?>Lab";});
});
function a(p,i,r=true){if(r)$.post("<?php echo JAWS_PATH_WEB ?>/lab",{"operation":p,"data":i},function(d){alert(d);});else window.location.href="<?php echo JAWS_PATH_WEB ?>/lab?operation="+p+"&data="+i;}
	</script>
</head>
<body style="font-family: sans-serif; font-size: 90%;">
	<div>
    	<center>
	        <B><?php echo $domain_name ?> Lab Users</B> (You are logged in as: <?php echo $_SESSION["user"]["name"]; ?> <A href="<?php echo JAWS_PATH_WEB."/logout"; ?>">Logout</A>)
        	<?php if (isset($msg)) echo "<br/>".$msg; ?>
    	</center>
	</div><hr>
	<center>
		<?php foreach ($domains as $domain) {
			if (strlen($domain["domain"]) == 0) {
				continue;
			} ?>
			<a href="<?php echo JAWS_PATH_WEB ?>/lab?domain=<?php echo $domain["domain"] ?>"><?php echo $domain["domain"] ?></a>
		<?php } ?>
		<br><br>
		<form method="get">
			<input type="text" name="sis_id" placeholder="Search <?php echo (strlen($domain_name) == 1 ? "an " : "a ").$domain_name ?> Username">
			<input type="hidden" name="domain" value="<?php echo $domain_name ?>Lab">
			<input type="submit" value="Search">
		</form>
		<?php if (!isset($res_lab[0])) { ?>
			<h3>No results returned for <?php echo $_GET["sis_id"] ?></h3>
		<?php } ?>
		<table border="0" cellpadding="10" cellspacing="2" style="font-size: 95%;">
			<thead>
				<tr class="header">
					<?php if (!$lab_get) { ?><th><button class="btn-copy">COPY</button> / <button class="btn-export">EXPORT</button></th><?php } ?>
					<th>Date</th>
					<th>User ID</th>
					<th>Password</th>
					<th>Name</th>
					<?php if (!$lab_get_custom) { ?><TH>Email ID</th><?php } ?>
					<th>
						<?php if ($lab_get) { ?>Done<?php } else { ?><button class="btn-done">UPDATE</button><?php } ?>
						<select id="list-type">
							<option value="pending" <?php echo ($list_type == "pending" ? "selected" : "") ?>>Pending</option>
							<option value="done" <?php echo ($list_type == "done" ? "selected" : "") ?>>Done</option>
							<option value="all" <?php echo ($list_type == "all" ? "selected" : "") ?>>All</option>
						</select>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($res_lab as $lab) { ?>
					<tr>
						<?php if (!$lab_get) { ?><td><input type="checkbox" class="line-copy" id="<?php echo $lab["enr_id"] ?>"></td><?php } ?>
						<td><?php echo substr($lab["start_date"], 0, 10) ?></td>
						<td class="txt-user"><?php echo $lab["lab_user"] ?></td>
						<td class="txt-pass"><?php echo $lab["lab_pass"] ?></td>
						<td><?php echo $lab["name"] ?></td>
						<?php if (!$lab_get_custom) { ?><td><?php echo $lab["email"] ?></td><?php } ?>
						<td><input type="checkbox" class="lab-check" id="<?php echo $lab["enr_id"] ?>" <?php $disabled = ($lab_get ? true : false); $checked = ($lab["lab_status"] == "ul" ? true : false); echo ($disabled ? "disabled " : ""); echo ($checked ? "checked" : ""); ?> /></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</center>
	<textarea id="copy-text" style="display: none;"></textarea>
</body>
</html>