<?php

	// Initialize
	define("JAWS", "2");
	if (version_compare(PHP_VERSION, '5.3.10', '<')) {
		die('Your host needs to use PHP 5.3.10 or higher to run JAWS!');
	}

	date_default_timezone_set("Asia/Kolkata");
	error_reporting(0);

	define("BATCAVE_APP", 1);

	// Load stuff
	require_once "app/libraries/loader.php";
	load_config();

	load_library("batcave");

	batcave_init();
	batcave_serve();

?>