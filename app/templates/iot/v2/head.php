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

    JIGSAW ACADEMY WORKFLOW SYSTEM v2
    ---------------------------------
*/

	// Prevent exclusive access
    	if (!defined("JAWS")) {
        		header('Location: https://www.jigsawacademy.com');
        		die();
    	}

?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
		<meta name="description" content="<?php echo (isset($GLOBALS["content"]["meta_description"]) ? $GLOBALS["content"]["meta_description"] : "Internet of Things (IoT) in India")." - Jigsaw Academy"; ?>">
		<meta name="author" content="BadGuppy">
		<title><?php echo (isset($GLOBALS["content"]["title"]) ? $GLOBALS["content"]["title"] : "Internet of Things (IoT) in India")." - Jigsaw Academy"; ?></title>
		<link rel="icon" type="image/png" href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL.'/media/jaws/frontend/images/favicon.png'; ?>">

		<link type="text/css" rel="stylesheet" href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL; ?>/common/iot/v2/bootstrap.min.css"/>
		<link type="text/css" rel="stylesheet" href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL; ?>/common/iot/v2/style.css"/>
		<link type="text/css" rel="stylesheet" href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL; ?>/common/iot/v2/iot.popup.css"/>
		<link type="text/css" rel="stylesheet" href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL; ?>/common/iot/v2/media.css">
		<!--<link type="text/css" rel="stylesheet" href="<?php// echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL; ?>/common/iot/v2/custom.css">-->
		<link rel="stylesheet" type="text/css" href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL; ?>/common/iot/v2/examples.css" />
		<link rel="stylesheet" href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL.'/common/fa/css/font-awesome.css'; ?>">

		<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
		<script type="text/javascript" src="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL; ?>/app/templates/iot/v2/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL; ?>/app/templates/iot/v2/js/jquery.fullPage.js"></script>
		<script type="text/javascript" src="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL; ?>/app/templates/iot/v2/js/examples.js"></script>  	
		<script type="text/javascript" src="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL; ?>/app/templates/iot/v2/js/waypoints.min.js"></script> 
    	<script type="text/javascript" src="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL; ?>/app/templates/iot/v2/js/waypoints-sticky.min.js"></script>
		<script type="text/javascript" src="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL; ?>/app/templates/iot/v2/js/owl.carousel.js"></script>  
		<script type="text/javascript" src="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL; ?>/app/templates/iot/v2/js/smoothscroll.js"></script> 
		<script type="text/javascript" src="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL; ?>/app/templates/iot/v2/js/login.js"></script>

		<link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
		<link href='https://fonts.googleapis.com/css?family=Lato:400,300' rel='stylesheet' type='text/css'>
		<!-- Google analytics tracking -->
		<script>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');ga('create', 'UA-31889158-1', 'auto');ga('send', 'pageview');</script>
		<!-- Google analytics tracking -->
		<meta name="google-site-verification" content="gSjq92tY8jL46yMnT0Z-POIPEK68u97DuBcUnHTe87M" />
	</head>
	<body>