<?php

	if (!empty($_POST["i"])) {

		$_SESSION["r"] = "Hi, ".$_SESSION["user"]["name"]."!<br>How can I help you today?";
		$_SESSION["o"] = [
			"Extend my access",
			"Freeze my access",
			"Issue with lab access"
		];

		respond();

	}

?>