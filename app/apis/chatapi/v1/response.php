<?php

	if (!empty($_POST["r"])) {

		// var_dump($_SESSION);

		if (empty($_SESSION["r"])) {

			if ($_POST["r"] == 1) {
				$_SESSION["r"] = "Please allow me some time to gather relevant data for your request...";
			}

			$_SESSION["w"] = true;

		}

		respond();

	}

?>