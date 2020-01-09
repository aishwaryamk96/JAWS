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

<?php 
// LEAD MODAL
 load_template("iot", "v2/leadform");
// LEAD MODAL ENDS 
 
if(auth_session_is_logged())
	include('profile.php'); 
?>
<!-- Menu Start -->
<div class="site-header border-bottom">
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
	          <a href="home" class="navbar-brand" style='position:relative; left:15px;'>
	          	<img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/IoT-logo-full1.png" class="hidden-xs" style="padding-top: 3px;" alt="IoT Logo">
	          	<img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/IoT-logo-small.png" class="hidden-lg hidden-md hidden-sm" style='top: 15px; position: relative;' alt="IoT Logo Small">
	          </a>
	          </div>
	        <div class="collapse navbar-collapse">
	          <ul class="nav navbar-nav">
	            <li><a href="https://www.jigsawacademy.com/iot/iot-courses" class="cool-link">IoT COURSES</a></li>
	            <li><a href="https://www.jigsawacademy.com/iot/iot-career" class="cool-link">IoT CAREERS</a></li>
	            <li><a href="https://www.jigsawacademy.com/iot/blog/" target='_blank' class="cool-link">EXPLORE IOT</a></li>
	            <?php// if (!auth_session_is_logged()) { ?>
	            <!--	<li class="signup-btn skew-btn text-capitalize"><a href="#" data-toggle="modal" data-target="#loginmodal"><span>Sign Up</span></a></li>
	            <?php// } else { ?>
	            	<li style="pointer-events: none; opacity: 0.5; text-transform: capitalize;"><a href="#" class="cool-link">Hello, <?php// echo substr($_SESSION["user"]["name"], 0, ((strpos($_SESSION["user"]["name"], " ") !== false) ? strpos($_SESSION["user"]["name"], " ") : strlen($_SESSION["user"]["name"]))); ?></a></li>-->
	            <?php// } ?>
			<?php if (auth_session_is_logged()) { ?>
	              	 <li><a class="cool-link" href="#profile-popup1" data-toggle="modal" >Profile</a></li>
	              	 <?php } ?>
	              	 <li class="signup-btn skew-btn"><a href="iot-beginners-course"><span>Free Course</span></a></li>
	          </ul>
	        </div>
	      </div>
	    </div>
	  </div>
	</div>
</div>
	<!-- Menu End -->