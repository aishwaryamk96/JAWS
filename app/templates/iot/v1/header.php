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

<!-- Menu Start -->
	<div class="wrapper">
	  <div class="row">
	    <div class="logo-margin">
	      <div class="navbar navbar-default">
	        <div class="navbar-header">
	          <div class="menu navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
	            <div class="bit-1"></div>
	            <div class="bit-2"></div>
	            <div class="bit-3"></div>
	          </div>
	          <a href="home" class="navbar-brand"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/logo.png" class="hidden-xs"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/ja_logo.jpg" class="hidden-lg hidden-md hidden-sm"></a> </div>
	        <div class="collapse navbar-collapse">
	          <ul class="nav navbar-nav">
	            <li><a href="specializations" class="cool-link">IOT SPecialization</a></li>
	            <li><a href="#" class="cool-link">Why IOT?</a></li>
	            <li><a href="#" class="cool-link">IOT Blog</a></li>
	            <?php if (!auth_session_is_logged()) { ?>
	            	<li class="signup-btn skew-btn text-capitalize"><a href="#" data-toggle="modal" data-target="#loginmodal"><span>Sign Up</span></a></li>
	            <?php } else { ?>
	            	<li style="pointer-events: none; opacity: 0.5; text-transform: capitalize;"><a href="#" class="cool-link">Hello, <?php echo substr($_SESSION["user"]["name"], 0, ((strpos($_SESSION["user"]["name"], " ") !== false) ? strpos($_SESSION["user"]["name"], " ") : strlen($_SESSION["user"]["name"]))); ?></a></li>
	            <?php } ?>
	          </ul>
	        </div>
	      </div>
	    </div>
	  </div>
	</div>
	<!-- Menu End -->