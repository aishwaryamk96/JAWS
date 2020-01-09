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

	// Start Output Buffering - Important to avoid same headers with duplicate values !
	ob_start();

	// Load JAWS Session
	session_name('jaws');
	session_start();

	load_module("redirector");

	// Check if logged in
	if (isset($_SESSION['user'])) {

		if (isset($_GET["pre"]) && function_exists("wp_login\\".$_GET["pre"]))
		{
			$func = "wp_login\\".$_GET["pre"];
			$func(isset($_GET["pre_param"]) ? $_GET["pre_param"] : "");
		}

		// Copy JAWS Session Values
		$temp_session = $_SESSION['user'];

		// Write session changes
		session_commit();
		// Abort JAWS Session
		session_abort();

		if (isset($_GET["in"]) && function_exists("wp_login\\".$_GET["in"]))
			"wp_login\\".$_GET["in"](isset($_GET["in_param"]) ? $_GET["in_param"] : "");

		// Create WP Session from copy
		ini_set('session.name','PHPSESSID');
		session_start();
		session_regenerate_id();
		$_SESSION["jaws"]["user"]["user_id"] = $temp_session["user_id"];
		$_SESSION["jaws"]["user"]["web_id"] = $temp_session["web_id"];
		$_SESSION["jaws"]["user"]["email"] = $temp_session["email"];
		$_SESSION["jaws"]["user"]["name"] = $temp_session["name"];
		$_SESSION["jaws"]["user"]["phone"] = $temp_session["phone"];
		$_SESSION["jaws"]["user"]["photo_url"] = $temp_session["photo_url"];
		$_SESSION["jaws"]["user"]["soc_fb"] = $temp_session["soc_fb"];
		$_SESSION["jaws"]["user"]["soc_gp"] = $temp_session["soc_gp"];
		$_SESSION["jaws"]["user"]["soc_li"] = $temp_session["soc_li"];
		$_SESSION["jaws"]["refresh_timestamp"] = new DateTime("now");
		session_commit();

		if (isset($_GET["post"]) && function_exists("wp_login\\".$_GET["post"]))
			"wp_login\\".$_GET["post"](isset($_GET["post_param"]) ? $_GET["post_param"] : "");

	}

	// Redirect if can
	if (isset($_GET['redir'])) {
		?>
			<script type='text/javascript'>
				window.location.href = decodeURI('<?php echo $_GET["redir"]; ?>');
			</script>
		<?php
	}

	// Done
	ob_end_flush();

?>