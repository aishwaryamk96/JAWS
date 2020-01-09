<?php

	// Initialize
	define("JAWS", "2");
	date_default_timezone_set("Asia/Kolkata");
	define("AILAB", "https://chat.jigsawacademy.com");

	// Load stuff
	require_once "app/libraries/loader.php";
	load_config();
	load_library("chat");

?>
<!doctype html>
<html lang="en">
<head>
	<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	<link rel="stylesheet" href="https://unpkg.com/bootstrap-material-design@4.1.1/dist/css/bootstrap-material-design.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://unpkg.com/popper.js@1.12.6/dist/umd/popper.js" integrity="sha384-fA23ZRQ3G/J53mElWqVJEGJzU0sTs+SvzG8fXVWP+kJQ1lwFAOkcUOysnlKJC33U" crossorigin="anonymous"></script>
	<script src="https://unpkg.com/bootstrap-material-design@4.1.1/dist/js/bootstrap-material-design.js" integrity="sha384-CauSuKpEqAFajSpkdjv3z9t8E7RlpJ1UP0lKM/+NdtSarroVKu069AlsRPKkFBz9" crossorigin="anonymous"></script>
	<script src="/c/js/jq.pm.js"></script>
	<script src="/c/js/app.js"></script>
	<link rel="stylesheet" href="/c/css/app.css">
</head>
<body>
	<div class="d-flex justify-content-between py-2 border-bottom bg-info fixed-top w-100">
		<div class="my-auto ml-4">
			<label class="text-white">Jigsaw Chat</label>
		</div>
		<button class="btn btn-sm btn-primary mr-2 my-sm-0 text-white" id="chatToggle">
			<i class="material-icons">keyboard_arrow_down</i>
		</button>
	</div>
	<div class="chat-body d-flex flex-column flex-auto">
		<div id="msgList" class="d-flex flex-column" style="overflow:scroll;">
		</div>
	</div>
	<div class="border-top d-flex position-fixed w-100" style="bottom:0px">
		<span class="bmd-form-group pt-0 w-100 position-relative" style="height:56px;">
			<textarea id="userInput" class="border-0 w-100 h-100"></textarea>
			<button id="send" class="btn btn-primary my-0 bmd-btn-fab position-absolute fixed-right" style="top:0px">
				<i class="material-icons">send</i>
			</button>
		</span>
	</div>
	<div id="msg" class="hidden fade d-flex flex-column py-2 px-2">
		<div id="msgContainer" class="d-flex justify-content-end">
			<div class="d-flex flex-column">
				<div class="p-2 border mw-100 bg-white">
					<label id="text" class="wrap-text"></label>
				</div>
				<div id="status" class="pt-1 my-0 d-flex justify-content-end">
					<label style="font-size:85%;"></label>
				</div>
			</div>
			<div id="photoUrl" class="d-flex ml-2">
				<img src="<?= $_SESSION["user"]["photo_url"] ?>" width="50" height="50" class="rounded-circle">
			</div>
		</div>
		<div id="options" class="hidden d-flex flex-column">
		</div>
	</div>
	<div id="option" class="hidden my-1 d-flex justify-content-center">
		<button class="btn btn-primary btn-opt"></button>
	</div>
</body>
</html>