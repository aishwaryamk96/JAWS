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

<!-- blue-band start -->
<div class="wrapper section-padding-top section-padding-bottom">
  <div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hidden-xs">
	  <div class="beginner_internet_back">
		<div class="row">
		  <div class="col-lg-2 col-md-2 col-sm-2"> </div>
		  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-7">
			<p class="beginner_internet_text">Become an IOT Expert with the most comprehensive specialization in the IOT.</p>
		  </div>
		  <div class="col-lg-3 col-md-3 col-sm-4 col-xs-5 footer-padding">
			<div class=""><a href="specializations" class="skew-blue banner-link-blue pull-right"><span>LEARN MORE</span></a></div>
		  </div>
		  <div class="col-lg-1 col-md-1"> </div>
		</div>
	  </div>
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hidden-lg hidden-md hidden-sm">
	  <div class="beginner_internet_back<?php echo (isset($GLOBALS['content']['blueband']['style-alt']) ? ' text-center-xs' : ''); ?>">
		<div class="row">
		  <div class="col-xs-12">
			<p class="beginner_internet_text">Become an IOT Expert with the most comprehensive specialization in the IOT.</p>
		  </div>
		  <div class="<?php echo (isset($GLOBALS['content']['blueband']['style-alt']) ? 'col-xs-12' : 'col-lg-3 col-md-3 col-sm-4 col-xs-5'); ?> footer-padding text-center-xs">
			<div class=""><a href="specializations" class="skew-blue banner-link-blue"><span>LEARN MORE</span></a></div>
		  </div>
		</div>
	  </div>
	</div>
  </div>
</div>
<!-- blue-band end --> 