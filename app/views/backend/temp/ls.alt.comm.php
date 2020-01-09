<?php

	if (!empty($_POST["e"]) || !empty($_POST["p"])) {

		$where = [];
		if (!empty($_POST["e"])) {
			$where[] = "email = ".db_sanitize($_POST["e"]);
		}
		if (!empty($_POST["p"])) {
			$where[] = "phone = ".db_sanitize($_POST["p"]);
		}
		$where = implode(" AND ", $where);

		$original_lead = db_query("SELECT * FROM ls_leads WHERE $where;");
		if (empty($original_lead)) {
			die(json_encode(["status" => 0, "msg" => "Lead not found for the given criteria"]));
		}
		$original_lead = $original_lead[0];

		$email_1;
		$phone_1;
		$email_2;
		$phone_2;

		$msg = [];
		if (!empty($_POST["a1"][0])) {

			$email_1 = db_sanitize($_POST["a1"][0]);
			$lead = db_query("SELECT * FROM ls_leads WHERE (email LIKE $email_1 OR alt_email_1 LIKE $email_1 OR alt_email_2 LIKE $email_1) AND $where;");
			if (!empty($lead)) {
				$msg[] = "Email '".$_POST["a1"][0]."' belongs to lead with email ID '".$lead[0]["email"]."'";
			}


		}
		if (!empty($_POST["a1"][1])) {

			$phone_1 = db_sanitize($_POST["a1"][1]);
			$lead = db_query("SELECT * FROM ls_leads WHERE (phone LIKE $phone_1 OR alt_phone_1 LIKE $phone_1 OR alt_phone_2 LIKE $phone_1) AND $where;");
			if (!empty($lead)) {
				$msg[] = "Email '".$_POST["a1"][1]."' belongs to lead with email ID '".$lead[0]["email"]."'";
			}

		}
		if (!empty($_POST["a1"][0])) {

			$email_2 = db_sanitize($_POST["a1"][0]);
			$lead = db_query("SELECT * FROM ls_leads WHERE (email LIKE $email_2 OR alt_email_1 LIKE $email_2 OR alt_email_2 LIKE $email_2) AND $where;");
			if (!empty($lead)) {
				$msg[] = "Email '".$_POST["a2"][0]."' belongs to lead with email ID '".$lead[0]["email"]."'";
			}


		}
		if (!empty($_POST["a1"][1])) {

			$phone_2 = db_sanitize($_POST["a1"][1]);
			$lead = db_query("SELECT * FROM ls_leads WHERE (phone LIKE $phone_2 OR alt_phone_1 LIKE $phone_2 OR alt_phone_2 LIKE $phone_2) AND $where;");
			if (!empty($lead)) {
				$msg[] = "Email '".$_POST["a2"][1]."' belongs to lead with email ID '".$lead[0]["email"]."'";
			}

		}

		if (!empty($msg)) {
			die(json_encode(["status" => 1, "msg" => implode(", ", $msg)]));
		}

		$set = [];
		$lead_info = ["lead_id" => $original_lead["lead_id"]];
		if (!empty($email_1)) {

			$set[] = "alt_email_1 = ".$email_1;
			$lead_info["alt_email_1"] = $_POST["a1"][0];

		}
		if (!empty($phone_1)) {

			$set[] = "alt_phone_1 = ".$phone_1;
			$lead_info["alt_phone_1"] = $_POST["a1"][1];

		}
		if (!empty($email_2)) {

			$set[] = "alt_email_2 = ".$email_2;
			$lead_info["alt_email_2"] = $_POST["a2"][0];

		}
		if (!empty($phone_2)) {

			$set[] = "alt_phone_2 = ".$phone_2;
			$lead_info["alt_phone_2"] = $_POST["a2"][1];

		}
		if (empty($set)) {
			die(json_encode(["status" => 1, "msg" => "Nothing to update..."]));
		}
		$set = implode(", ", $set);

		db_exec("UPDATE ls_leads SET $set WHERE id = ".$original_lead["id"]);

		load_plugin("leadsquared");
		ls_lead_capture($lead_info, false);

	}

?>
<html>
<head>
	<style>
		body {
			margin: 0px;
		}
		.container {
			background-color: rgba(120, 175, 255, 0.8);
			display: flex;
			height: 100%;
			width: 100%;
		}
		.modal {
			background-color: white;
			display: flex;
			flex-direction: column;
			margin: auto;
			min-width: 400px;
		}
		.head {
			border-bottom: 2px solid #999;
			font-size: 18px;
			font-weight: bold;
			padding: 20px;
		}
		.content {
			border-bottom: 2px solid #999;
			display: flex;
			flex-direction: column;
			justify-content: space-between;
			padding: 20px;
		}
		.control {
			display: flex;
			justify-content: space-between;
			padding: 20px 40px;
		}
		.section {
			display: flex;
			flex-direction: column;
			padding: 10px 10px;
		}
		.floating {
			color: #aaa;
			position: relative;
			top: -25px;
			transition: 0.2s ease all;
		}
		.section input {
			border: none;
			border-bottom: 1px solid #aaa;
			padding: 5px;
		}
		.section input:focus {
			outline: none;
			border-bottom: #5599ff;
			box-shadow: 0px 5px 10px -4px #5599ff;
		}
		.section input:valid, .section input:focus ~ .floating {
			font-size: 90%;
			top: -40px;
		}
		.named-section {
			border: 1px solid #aaa;
			margin-top: 10px;
		}
		.named-section:before {
			background-color: white;
			content: "Alternate";
			font-weight: bold;
			margin-left: 10px;
			padding: 0px 5px;
			position: relative;
			top: -11px;
		}
		.or {
			display: flex;
			justify-content: space-around;
		}
		.separator {
			border-bottom: 1px solid #aaa;
			margin: 5px 0px 10px;
		}
		.btn {
			background-color: #efefef;
			border: none;
			box-shadow: 0 1px 5px 0 rgba(0, 0, 0, 0.26);
			cursor: pointer;
			min-width: 130px;
			padding: 10px 30px;
			text-transform: uppercase;
		}
		.btn-save {
			background-color: rgba(55,200,90,0.8);
			color: white;
		}
		.btn-save:hover {
			background-color: rgba(55,200,90,1);
		}
		.btn-save:active {
			background-color: rgba(55,190,90,1);
			box-shadow: none;
			outline: none;
		}
		.btn-cancel {
			background-color: rgba(250,50,50,0.9);
			color: white;
		}
		.btn-cancel:hover {
			background-color: rgba(255,0,0,1);
		}
		.btn-cancel:active {
			background-color: rgba(230,50,50,1);
			box-shadow: none;
			outline: none;
		}
		.btn-disabled {
			background-color: #efefef!important;
			color: darkgray!important;
		}
		.btn-disabled:active {
			outline: none;
		}
		.required:after {
			color: red;
			content:" *";
		}
	</style>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script>
		var jq = $.noConflict();
		jq(document).ready(function() {
			jq("#email").change(inputChanged);
			jq("#phone").change(inputChanged);
			jq("#btn-save").click(function() {
				if (this.className.indexOf("btn-disabled") > -1) {
					return;
				}
				var e = jq("#email").val();
				var p = jq("#phone").val();
				if (e.length == 0 && p.length == 0) {
					alert("Either email or phone should be provided");
					return;
				}
				var a1 = [jq("#alt-email-1").val(), jq("#alt-phone-1").val()];
				var a2 = [jq("#alt-email-2").val(), jq("#alt-phone-2").val()];
				jq.post("", {e: e, p: p, a1: a1, a2: a2}, function(data, status) {
					alert(data);
				});
			});
		});
		var inputChanged = function() {
			if (jq(this).val().length > 0) {
				jq("#btn-save").removeClass("btn-disabled");
			}
			else if (jq("#phone").val().length == 0 && jq("#email").val().length == 0) {
				jq("#btn-save").addClass("btn-disabled");
			}
		}
	</script>
</head>
<body>
	<div class="container">
		<div class="modal">
			<div class="head">
				<label>Alternate Communication</label>
			</div>
			<div class="content">
				<div class="section">
					<input type="email" name="email" id="email" required="true">
					<label for="email" class="required floating">Primary Email</label>
				</div>
				<div class="or">Or</div>
				<div class="section">
					<input type="number" name="phone" id="phone" required="true">
					<label for="phone" class="required floating">Primary Phone</label>
				</div>
				<!-- <div class="separator"></div> -->
				<div class="named-section">
					<div class="section">
						<input type="email" name="alt_email[]" id="alt-email-1" required="true">
						<label for="alt-email-1" class="floating">Email 1</label>
					</div>
					<div class="section">
						<input type="number" name="alt_phone[]" id="alt-phone-1" required="true">
						<label for="alt-phone-1" class="floating">Phone 1</label>
					</div>
					<div class="separator"></div>
					<div class="section">
						<input type="email" name="alt_email[]" id="alt-email-2" required="true">
						<label for="alt-email-2" class="floating">Email 2</label>
					</div>
					<div class="section">
						<input type="number" name="alt_phone[]" id="alt-phone-2" required="true">
						<label for="alt-phone-2" class="floating">Phone 2</label>
					</div>
				</div>
			</div>
			<div class="control">
				<button class="btn btn-save btn-disabled" id="btn-save">Save</button>
				<button class="btn btn-cancel" id="btn-cancel">Cancel</button>
			</div>
		</div>
	</div>
</body>
</html>