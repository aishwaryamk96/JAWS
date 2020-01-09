<?php

	load_library("jwt");
	if (!empty($_POST["secret"])) {
		die(json_encode(explode(".", jwt_encode(["email" => "himanshu@jigsawacademy.com", "phone" => "8618593578"], $_POST["secret"]))));
	}

?>
<!DOCTYPE html>
<html>
<head>
	<title>JWT Test</title>
	<style>
		body {
			margin: 0px;
		}
		.container {
			display: flex;
			height: 100%;
		}
		.modal {
			display: flex;
			margin: auto;
		}
		.panel {
			display: flex;
			flex-direction: column;
			justify-content: stretch;
			margin: 10px;
		}
		.title {
			font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
			font-size: 28px;
			font-weight: bold;
			text-transform: capitalize;
		}
		.token {
			border: 1px solid #ccc;
			border-radius: 3px;
			font-family: Monaco;
			font-size: 18px;
			height: 458px;
			letter-spacing: 0.5px;
			line-height: 1.5;
			padding: 10px;
			outline: none;
			width: 418px;
			word-wrap: break-word;
		}
		.panel-content {
			border: 1px solid #ccc;
			border-radius: 3px;
			display: flex;
			flex-direction: column;
		}
		.section-title {
			border-top: 1px solid #ccc;
			border-bottom: 1px solid #ccc;
			color: gray;
			margin-bottom: 5px;
			padding: 5px;
			text-transform: uppercase;
		}
		.section-title.first {
			border-top: none!important;
		}
		.content {
			padding: 10px 50px;
		}
		pre {
			font-family: 'Roboto Mono',Menlo;
			line-height: 1.5;
			margin: 0px;
		}
		#secret {
			margin-left: 29px;
		}
		#header-encoded {
			color: #fb015b;
		}
		#payload-encoded {
			color: #d63aff;
		}
		#hash-str {
			color: #00b9f1;
		}
	</style>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script>
		$(document).ready(function() {
			getToken();
			$("#secret").keypress(function() {
				getToken();
			});
		});
		function getToken() {
			$.post("", {secret: $("#secret").val()}, function(data) {
				data = $.parseJSON(data);
				$("#header-encoded").html(data[0]);
				$("#payload-encoded").html(data[1]);
				$("#hash-str").html(data[2]);
			});
		}
	</script>
</head>
<body>
	<div class="container">
		<div class="modal">
			<div class="panel left">
				<div class="title">
					<label>Encoded</label>
				</div>
				<div class="token" id="token">
					<span id="header-encoded"></span>
					<span>.</span>
					<span id="payload-encoded"></span>
					<span>.</span>
					<span id="hash-str"></span>
				</div>
			</div>
			<div class="panel right">
				<div class="title">
					<label>Decoded</label>
				</div>
				<div class="panel-content">
					<div class="header">
						<div class="section-title first">
							<label>Header</label>
						</div>
						<div class="content">
							<pre>{</pre>
							<pre>	"alg": "HS256",</pre>
							<pre>	"typ": "JWT"</pre>
							<pre>}</pre>
						</div>
					</div>
					<div class="payload">
						<div class="section-title">
							<label>payload</label>
						</div>
						<div class="content">
							<pre>{</pre>
							<pre>	"email": "himanshu@jigsawacademy.com",</pre>
							<pre>	"phone": "8618593578"</pre>
							<pre>}</pre>
						</div>
					</div>
					<div class="hash">
						<div class="section-title">
							<label>signature</label>
						</div>
						<div class="content">
							<pre>hash_hmac(</pre>
							<pre>	base64_json_encode(HEADER) + "." +</pre>
							<pre>	base64_json_encode(PAYLOAD),</pre>
							<input type="text" id="secret" value="SECRET">
							<pre>)</pre>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>