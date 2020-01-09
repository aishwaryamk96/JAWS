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
	$GLOBALS["content"]["title"] = "IOT - Thank You";
	load_template("iot", "v2/head");
  ?>
  <!-- HTML HEAD ENDS -->

  <!-- HEADER MENU -->
  <?php load_template("iot", "v2/header"); ?>
  <!-- HEADER MENU ENDS -->

<!-- Banner Start -->
<div class="gray-bg" id="side_nav_item_id_0">
  <div class="wrapper padding-top-15">
	<div class="row">
	  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<div class="margin-30"></div>
		<h1 class="innerpage-banner-title f-color-blue">Thank You!</h1>
		<div class="margin-30"></div>
	  </div>
	</div>
  </div>
</div>
<!-- Banner End --> 

<!-- section2 start -->
<div class="wrapper section-padding-bottom">
  <div class="row conatin-margin-top">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-bottom-10">
	  <p>Thank you for registring to "IoT for Beginners" course. Click on following button to access the course video.</p>
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 section-padding-bottom">
	  <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
		<div class="skew-border orange-header-btn enroll-link pull-left"><a href="#">Click here</a></div>
	  </div>
	</div>
  </div>
</div>

	<!-- FOOTER -->
	<?php load_template("iot", "v2/footer"); ?>
	<!-- FOOTER ENDS -->

	<!-- LOGIN MODAL -->
	<?php load_template("iot", "v2/login"); ?>
	<!-- LOGIN MODAL ENDS -->

<script>
var menu = document.querySelector('.menu');
function toggleMenu () {
  menu.classList.toggle('open');
}
menu.addEventListener('click', toggleMenu);
</script> 
<script>
$(document).ready(function() {
 
  $("#owl-demo").owlCarousel({
 
      autoPlay: 3000, //Set AutoPlay to 3 seconds
 
      items : 3,
      itemsDesktop : [1199,3],
      itemsDesktopSmall : [979,2]
 
  });
 
});
</script>
<!-- FOOTER -->
	<?php load_template("iot", "v2/foot"); ?>
<!-- FOOTER ENDS -->