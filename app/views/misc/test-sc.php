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

	// Start Output Buffering - Optional
	ob_start();

?>

	<html>
	<head>
		<title>JAWS - Test Page</title>
		<style>
			body {
				display:block;

				background-color: black;
				color: white;
				font-family: "consolas", "sans-serif";
				font-size: 90%;

				margin: 0;
				padding: 20px;

				overflow-x: hidden;
				overflow-y: scroll;
			}
		</style>
	</head>
	<body>

<?php

	error_reporting(E_ALL);

	echo "JAWS v".JAWS_VERSION, "<br/>Jigsaw Academy Workflow System", "<br/>Running PHP v".phpversion()." on ".$_SERVER['SERVER_SOFTWARE'], "<br/>&copy; Jigsaw Academy Education Pvt. Ltd.<br/>";
	echo "<br/>";

	echo "JAWS Test Routine - Testing Auto Loaders";
	echo " <br />";

	echo "Loading auto loaders..";
	echo " &#10004;<br /><br />";

	echo "JAWS Test Routine - Testing Manual Loaders";
	echo " <br />";

	echo "Loading ui..";
	load_module("ui");
	echo " &#10004;<br />";

	echo "Loading user..";
	load_module("user");
	echo " &#10004;<br />";

	echo "Loading course..";
	load_module("course");
	echo " &#10004;<br />";

	echo "Loading subs..";
	load_module("subs");
	echo " &#10004;<br />";

	echo "Loading leads..";
	load_module("leads");
	echo " &#10004;<br />";

	echo "Loading payment lib..";
	load_library("payment");
	echo " &#10004;<br />";

	echo "Loading email lib..";
	load_library("email");
	echo " &#10004;<br />";

	echo "<br />";
	echo "Test Routine Successfull - Testing Custom Code";
	echo "<br /><br />";

	// ----- CUSTOM CODE ---------------------------------------------------------
	// --------------------------------------------------------------------------------

	//activity_create("critical", "IT error log test", "successful","","","","", "Some test content");
	//activity_log("[Soumik]: [[All iz well.]] IT error feed should be functional now! Existing msgs tagged as critical will show up live.", ["it"], ["c" => "complete"]);

?>

	<br /><br />
	Custom code execution complete - Ciao!
	</body>
