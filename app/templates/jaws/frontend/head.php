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

?>


<!DOCTYPE html>
<html lang="en">
	<head>
	
		<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
		<meta name="description" content="The Online School of Analytics">
		<meta name="author" content="BadGuppy">
		<title>Jigsaw Academy<?php echo (isset($GLOBALS["content"]["title"]) ? " - ".$GLOBALS["content"]["title"] : ""); ?></title>
		<link rel="icon" type="image/png" href="<?php echo JAWS_URL.'/media/jaws/frontend/images/favicon.png'; ?>">
		
		<script   src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular.min.js"></script>
		<?php if (isset($GLOBALS["content"]["scripts"])) { foreach($GLOBALS["content"]["scripts"] as $script_url) {?><script src="<?php echo JAWS_URL."/".$script_url; ?>"></script> <?php } } ?>

		<!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">-->
		<link rel="stylesheet" href="<?php echo JAWS_URL.'/common/fa/css/font-awesome.css'; ?>">
		<?php if (isset($GLOBALS["content"]["styles"])) { foreach($GLOBALS["content"]["styles"] as $style_url) { ?><link rel="stylesheet" href="<?php echo JAWS_URL."/".$style_url; ?>"> <?php } } ?>			

		<link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
		<link href='https://fonts.googleapis.com/css?family=Lato:400,300' rel='stylesheet' type='text/css'>

		<?php echo (isset($GLOBALS["content"]["head"]) ? " - ".$GLOBALS["content"]["head"] : ""); ?>

	</head>
	<body>

