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
		$GLOBALS["content"]["title"] = "Internet of Things (IoT)";
		load_template("iot", "v2/head");
	?>
	<!-- HTML HEAD ENDS -->

	<!-- HEADER MENU -->
	<?php load_template("iot", "v2/header"); ?>
	<!-- HEADER MENU ENDS -->

	<div id="fullpage"> 

	  <!-- Banner Start -->
	  <div class="home-banner_1 section" id="firstpage">
		<section class="wrapper breadcrumb-start" style="display: none;">
			<ol class="bread-crumbs">
				<li>
					<span itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
						<a href="https://www.jigsawacademy.com/iot/" itemprop="url" class="last">
							<span itemprop="title">IoT Home</span>
						</a>
					</span>
				</li>
			</ol>
		</section>
	    <div class="wrapper section-padding-top section-padding-bottom">
	      <div class="row">
	        <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12">
 				<h1 class="banner-title white-text">The Future is Here</h1>
          		<p class="banner-contain banner-contain-hm white-text">Transform your career with an IoT certification.</p>
	          <div class="margin-30"></div>
	          <!--<div class="skew-border banner-link"><a href="#" data-toggle="modal" data-target="#myModal">START YOUR IOT JOURNEY FOR FREE</a></div>-->
	           <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
	          <div class="text-center-xs actionbtn skew-btn uppercase"><a target="_blank" <?php iot_a_part('https://www.jigsawacademy.com/jaws/free.jlc?course='.$GLOBALS['iot_courses']['courses'][0]["course_id"].(auth_session_is_logged() ? "&soc=".$_SESSION["user"]["jlc.free.soc"] : ""),'#loginmodal','#profile-popup1'); ?> ><span class="sign-up-top uppercase">Start your free course now</span></a></div>
	          </div>
	        </div>
	        <div class="col-lg-7 col-md-7 col-sm-6 col-xs-12">
	          <div class="margin-30 hidden-lg hidden-md hidden-sm"></div>
	         <!--  <a href="#" data-toggle="modal" data-target="<?php //echo (auth_session_is_logged()) ? '#myModal' : '#loginmodal'; ?>" data-ru="<?php //echo JAWS_PATH_WEB."/view/frontend/redir/wp.login?redir=".urlencode('https://www.jigsawacademy.com/iot?utm_source=iot&utm_campaign=iot&utm_term=iot'); ?>" ><img src="<?php //echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/video.png" class="img-responsive"></a>  -->
	         <a <?php iot_a_part(JAWS_PATH_WEB."/iot", "#loginmodal", "#profile-popup1", "#myModal"); ?>><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/video.png" class="img-responsive"></a>
	          </div>
	      </div>
	    </div>
	  </div>
	  <!-- Banner End --> 
	  
	  <!-- section1 start -->
	  <div class="wrapper section-padding-top section-padding-bottom relative" id="secondpage">
	<a href="#secondpage"><div class="circle-click"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/down-arrow.png"></div></a>
	<div class="margin-15"></div>
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <h2 class="section-title center">Why IoT?</h2>
        <div class="gary-strip center-block"></div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"></div>
      <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12 ">
        <p class="center conatin-margin-top">The Internet of Things has the potential to change the world. It’s poised to make an even greater impact than the internet ever has.<br class="hidden-xs"> By 2020, IoT will have doubled its reach and made its presence relevant across the smartphone, <br class="hidden-xs">PC, tablet and the wearable market combined.</p>
      </div>
      <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"></div>
    </div>
    <div class="padding-bottom-10"></div>
    <div class="row section-padding-top conatin-margin-top">
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-right-none">
        <div class="box1 box relative">
          <div class="circle light-blue-bg center">
            <h2 class="white-text font-size-25"><b><i class="fa fa-usd" aria-hidden="true"></i>14.2</b></br>
              <span class="circle-contain">Trillion</span></h2>
          </div>
          <p class="center">Is what the world economy will make from IoT Domain by 2030.</p>
          <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/box-icn1.jpg" class="center-block"> </div>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-right-none">
        <div class="box-img-margin hidden-lg hidden-md hidden-sm"></div>
        <div class="box2 box relative">
          <div class="circle light-green-bg center">
            <h2 class="white-text font-size-25"><b><i class="fa fa-usd" aria-hidden="true"></i>6</b></br>
              <span class="circle-contain">Trillion</span></h2>
          </div>
          <p class="center">IoT budget projection for the next 5 years</p>
          <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/box-icn2.jpg" class="center-block"> </div>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-right-none">
        <div class="box-img-margin hidden-lg hidden-md hidden-sm"></div>
        <div class="box3 box relative">
          <div class="circle light-blue-bg center">
            <h2 class="white-text font-size-25"><b>50</b></br>
              <span class="circle-contain">billion</span></h2>
          </div>
          <p class="center">Devices will be connected to the Internet by 2020.</p>
          <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/box-icn3.jpg" class="center-block"> </div>
      </div>
    </div>
  </div>
	  <!-- section1 end --> 
	  
	  <!-- section2 start -->
	    <div class="gray-bg relative" id="thirdpage">
    <div class="wrapper " id="forthpage">
      <div class="gray-bg relative section-padding-top section-padding-bottom">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h2 class="section-title center">IoT –  A Connected World</h2>
            <div class="gary-strip center-block"></div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"></div>
          <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
            <p class="center conatin-margin-top">While the fixed broadband connects 1 billion users via PCs and fixed wireless connects 2 billion users via smartphones <br class="hidden-sm hidden-xs">(on its way to 6 billion), IoT is expected to connect 50 billion ‘things’ to the Internet by 2020.</p>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="section-padding-top padding-bottom-10"> <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/IoT_home_6.jpg" class="img-responsive center-block"> </div>
          </div>
        </div>
      </div>
    </div>
  </div>
	  <!-- section2 end --> 
	  
	  <!-- section3 start -->
	    <div class="relative border-bottom" id="thirdpage">
    <div class="wrapper" id="forthpage">
      <div class="relative section-padding-top section-padding-bottom">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h2 class="section-title center">Real World Applications of IoT</h2>
            <div class="gary-strip center-block"></div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"></div>
          <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
            <p class="center conatin-margin-top">Examples of the Internet of Things extend from smart connected homes to wearables and healthcare.<br class="hidden-xs">
              It is not wrong to suggest that IoT is now becoming a part of every aspect of our lives.  </p>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="section-padding-top padding-bottom-10"> <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Connected-Smart-Cities.jpg" class="img-responsive center-block"> </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12">
            <h4 class="text-center smart-cities-heading padding-bottom-10">Connected Smart Cities</h4>
          </div>
        </div>
      </div>
    </div>
  </div>
	  <!-- section3 end --> 
	  
	  <!-- section4 start -->
	 <div class="wrapper section-padding-top section-padding-bottom">
    <div class="row">
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12"></div>
		<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
			<div class="carousel slide" data-ride="carousel" id="quote-carousel">
				<!-- Bottom Carousel Indicators -->
				<ol class="carousel-indicators">
				  <li data-target="#quote-carousel" data-slide-to="0" class="active"></li>
				  <li data-target="#quote-carousel" data-slide-to="1"></li>
				  <li data-target="#quote-carousel" data-slide-to="2"></li>
				  <li data-target="#quote-carousel" data-slide-to="3"></li>
				</ol>       
				<!-- Carousel Slides / Quotes -->
				<div class="carousel-inner">
					<!-- Quote 1 -->
					<div class="item active">
					  <div class="row">
						<div class="col-sm-12">
							<p class="testimonial-contain"><i>"Developers who have the right mix of Internet of Things skills and experience will soon command big bucks, but the time to start acquiring those skill is now."</i></p>
							<div class="margin-30"></div>
							<img class="img-responsive center-block" src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/testimonial02.jpg" alt="">
						</div>
					  </div>
					</div>

					<!-- Quote 2 -->
					<div class="item">
					  <div class="row">
						<div class="col-sm-12">
							<p class="testimonial-contain"><i>"A lack of employee skills and knowledge was the biggest obstacle for organisations wanting to make use of the Internet of Things."</i></p>
							<div class="margin-30"></div>
							<img class="img-responsive center-block" src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/testimonial03.jpg" alt="">
						</div>
					  </div>
					</div>

					<!-- Quote 3 -->
					<div class="item">
					  <div class="row">
						<div class="col-sm-12">
							<p class="testimonial-contain"><i>"The Internet of Things promises to be a job bonanza for developers, and coders can expect plenty of work at very good pay."</i></p>
							<div class="margin-30"></div>
							<img class="img-responsive center-block" src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/testimonial04.jpg" alt="">
						</div>
					  </div>
					</div>
					
					<!-- Quote 4 -->
					<div class="item">
					  <div class="row">
						<div class="col-sm-12">
							<p class="testimonial-contain"><i>"I think the next ten years because of the Internet of Things, data will be more important than anything else. Data scientists, who are already in great demand, will be in even more demand in the next ten years."</i></p>
							<div class="margin-30"></div>
							<img class="img-responsive center-block" src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/testimonial05.jpg" alt="">
						</div>
					  </div>
					</div>
					
				</div>
			</div>    	
		</div>
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12"></div>
    </div>
  </div>
	  <!-- section4 end --> 
	  
	  <!-- section5 start -->
<div class="wrapper section-padding-bottom padding-left-none-xs padding-right-none-xs" id="forthpage">
    <div class="gray-bg relative section-padding-top section-padding-bottom padding-right-15-xs padding-left-15-xs">
      <div class="specialization-gray-design hidden-xs"> <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/specialization-gray-design.png"> </div>
      <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <h2 class="section-title center">Start learning about IoT</h2>
          <div class="gary-strip center-block"></div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"></div>
        <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
          <p class="center conatin-margin-top">Would you like to find your place in an exciting, new frontier that’s powered by IoT? Right from the domain of electronics<br/>  to computer science, there’s plenty of scope to grow and thrive in IoT. With the right skills and hands-on experience, you could easily land the best jobs in IoT. A great place to begin your foray into IoT is through the distinctive, detailed and transformational IoT and analytics courses from Jigsaw Academy.</p>
          <br>
          <p class="text-center"></p>
        </div>
        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"></div>
      </div>
      <div class="row">
        <div class="margin-30"></div>
        <div class="text-center">
          <div class="col-lg-5 col-md-5 col-sm-4 col-xs-3"> </div>
          <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
            <div class="text-center-xs center-block actionbtn skew-btn uppercase"><a href="https://www.jigsawacademy.com/iot/blog/" target='_blank'><span>Know more</span></a></div>
          </div>
          <div class="col-lg-5 col-md-5 col-sm-4 col-xs-3"> </div>
        </div>
      </div>
    </div>
  </div>

  <div class="margin-30"></div>
	<!-- section5 end --> 

	<!-- FOOTER -->
	<?php load_template("iot", "v2/footer"); ?>
	<!-- FOOTER ENDS -->

<!-- modal start-->
<div class="modal fade" id="myModal" role="dialog">
  <div class="modal-dialog"> 
    <!-- Modal content-->
    <div class="modal-content model-margin">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <!--<iframe width="100%" height="400" src="https://www.youtube.com/embed/F7VJopzuZzo" frameborder="0" allowfullscreen></iframe>-->
		<iframe width="100%" height="400" src="https://www.youtube.com/embed/OVxqC59G_Zk" frameborder="0" allowfullscreen></iframe>
      </div>
    </div>
  </div>
</div>
<!-- modal end--> 

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
    function makeSticky() {
      var myWindow = $( window ),
        myHeader = $( ".site-header" );

      myWindow.scroll( function() {
        if ( myWindow.scrollTop() == 0 ) {
          myHeader.removeClass( "sticky-nav" );
        } else {
          myHeader.addClass( "sticky-nav" );
        }
      } );
    }

    $( function() {
      // makeSticky();
       
      $( ".site-header" ).waypoint( 'sticky' );
    } );
  </script> 

<script>
$(document).ready(function() {
  //carousel options
  $('#quote-carousel').carousel({
    pause: true, interval: 10000,
  });
});
</script>

<!-- FOOTER -->
	<?php load_template("iot", "v2/foot"); ?>
<!-- FOOTER ENDS -->