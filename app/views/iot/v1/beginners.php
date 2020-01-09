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

    	// Load stuff
    	load_module("iot");

    	// Init View
    	iot_view_init();
  
?>

  <!-- HTML HEAD -->
  <?php 
	$GLOBALS["content"]["title"] = "Beginner";
	load_template("iot", "v1/head");
  ?>
  <!-- HTML HEAD ENDS -->

  <!-- HEADER MENU -->
  <?php load_template("iot", "v1/header"); ?>
  <!-- HEADER MENU ENDS -->

<!-- Banner Start -->
<div class="gray-bg" id="side_nav_item_id_0">
  <div class="wrapper header-padding-top">
	<div class="row">
	  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<h1 class="innerpage-banner-title f-color-blue ">IoT for Beginners</h1>
		<p class="banner-contain">Get an overview of the phenomenon called the internet of Things.</p>
		<div class="margin-30"></div>
		<div class="row site-header">
		  <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4">
			<h3 class="cancel-prize"> <strike><i class="fa fa-inr" aria-hidden="true"></i>7,500</strike> </h3>
			<h1 class="prize">FREE</h1>
		  </div>
		  <div class="col-lg-9 col-md-8 col-sm-8 col-xs-8">
			<div class="pull-left"><a href="#" class="text-capitalize skew-fill-color innerpage-btn">Enroll Now</a></div>
		  </div>
		</div>
	  </div>
	  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<div class="margin-30 hidden-lg hidden-md hidden-sm"></div>
		<div class="header-img-margin hidden-xs"> <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/beginners-header.jpg" class="img-responsive"> </div>
	  </div>
	</div>
  </div>
</div>
<!-- Banner End --> 

<!-- section1 start -->
<div class="nav-scrolling site-header hidden-xs">
  <div class="wrapper ">
	<nav class="navbar navbar-default navbar-custom ">
	  <ul class="nav navbar-nav pull-left">
		<li><a href="#about-section" class="cool-link">about this course</a></li>
		<li><a href="#video-section" class="cool-link">course video</a></li>
	  </ul>
	</nav>
  </div>
</div>
<div class="clearfix"></div>
<div class="wrapper wrapper-top-bottom-50" id="about-section">
  <div class="row hidden-lg hidden-md hidden-sm">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	  <h2 class="innerpage-title">About this course</h2>
	  <div class="gary-strip"></div>
	</div>
  </div>
  <div class="conatin-margin-top hidden-lg hidden-md hidden-sm"></div>
  <div class="row">
	<div class="col-lg-8 col-md-8 col-sm-8 padding-right-30">
	  <p>Step into the fantastic world of the future with this introductory course on the internet of Things. Understand the key terminologies, explore its history, and watch stories of how IOT inventors are creating a brave new world eith their path-breaking devices.</p>
	</div>
	<div class="col-lg-4 col-md-4 col-sm-4 border-left-side padding-left-40">
	  <p><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/prerequisites.png"> &nbsp; <span class="capital f-color-blue">prerequisites:</span> None</p>
	  <p><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Monitor.png"> &nbsp; <span class="capital f-color-blue">platform:</span> None</p>
	  <p><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Calendar.png"> &nbsp; <span class="capital f-color-blue">duration:</span> 60 Min</p>
	</div>
  </div>
</div>
<!-- section1 end --> 

<!-- section2 start -->
<div class="gray-bg relative" id="video-section">
  <div class="wrapper section-padding-top">
	<div class="row">
	  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<h2 class="course-title center">Course Video</h2>
		<div class="gary-strip center-block"></div>
	  </div>
	</div>
	<div class="clearfix"></div>
	<div class="row">
	  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="video-beginners-section"> <a href="#" data-toggle="modal" data-target="#loginmodal"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/beginners-video.jpg" class="img-responsive center-block"></a> </div>
	  </div>
	</div>
  </div>
</div>

	<!-- BLUE BAND -->
	<?php 
		$GLOBALS['content']['blueband']['style-alt'] = true;
		load_template("iot", "v1/blueband"); 
	?>
	<!-- BLUE BAND ENDS -->

	<!-- FOOTER -->
	<?php load_template("iot", "v1/footer"); ?>
	<!-- FOOTER ENDS -->

	<!-- LOGIN MODAL -->
	<?php load_template("iot", "v1/login"); ?>
	<!-- LOGIN MODAL ENDS -->

<script async type="text/javascript" src="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL; ?>/app/templates/iot/v1/js/waypoints.min.js"></script> 
<script async type="text/javascript" src="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL; ?>/app/templates/iot/v1/js/waypoints-sticky.min.js"></script> 

<script>
var menu = document.querySelector('.menu');
function toggleMenu () {
  menu.classList.toggle('open');
}
menu.addEventListener('click', toggleMenu);
</script> 

</body>
</html>