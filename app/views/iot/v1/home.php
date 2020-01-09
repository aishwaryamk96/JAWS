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
		$GLOBALS["content"]["title"] = "The Next Big Revolution";
		load_template("iot", "v1/head");
	?>
	<!-- HTML HEAD ENDS -->

	<!-- HEADER MENU -->
	<?php load_template("iot", "v1/header"); ?>
	<!-- HEADER MENU ENDS -->

	<div id="fullpage"> 
	  <!-- Banner Start -->
	  <div class="gray-bg section" id="firstpage">
	    <div class="wrapper section-padding-top section-padding-bottom">
	      <div class="row">
	        <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12">
	          <h1 class="banner-title">Get Yourself Future Ready</h1>
	          <p class="banner-contain">The world is getting ready for the next big thing or, to be exact, the Internet of Things. Are you?</p>
	          <div class="margin-30"></div>
	          <!--<div class="skew-border banner-link"><a href="#" data-toggle="modal" data-target="#myModal">START YOUR IOT JOURNEY FOR FREE</a></div>-->
	          <div class="text-center-xs"><a href="#" data-toggle="modal" data-target="<?php echo (auth_session_is_logged()) ? '#myModal' : '#loginmodal'; ?>" class="skew-border banner-link"><span>START YOUR IOT JOURNEY FOR FREE<span></a></div>
	        </div>
	        <div class="col-lg-7 col-md-7 col-sm-6 col-xs-12">
	          <div class="margin-30 hidden-lg hidden-md hidden-sm"></div>
	          <a href="#" data-toggle="modal" data-target="<?php echo (auth_session_is_logged()) ? '#myModal' : '#loginmodal'; ?>"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/video.png" class="img-responsive"></a> </div>
	      </div>
	    </div>
	  </div>
	  <!-- Banner End --> 
	  
	  <!-- section1 start -->
	  <div class="wrapper section-padding-top section-padding-bottom" id="secondpage">
	    <div class="row">
	      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	        <h2 class="section-title center">IoT – The Next Big Revolution</h2>
	        <div class="gary-strip center-block"></div>
	      </div>
	    </div>
	    <div class="row">
	      <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12"></div>
	      <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
	        <p class="center conatin-margin-top">The Internet of Things has the potential to change the world, just as the Internet did. Maybe even more so. It’s the world where you don’t worry about things but the things do the thinking for yous.</p>
	      </div>
	      <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12"></div>
	    </div>
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
	          <p class="center">Internet of Things budget projection for the next 5 years.</p>
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
	    <div class="career-gray-design hidden-xs"> <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/career-gray-design.png"> </div>
	    <div class="wrapper section-padding-top">
	      <div class="row">
	        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12"> <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/career-img1.jpg" class="img-responsive career-img-margin"> </div>
	        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 section-padding-bottom">
	          <div class="margin-30 hidden-lg hidden-md hidden-sm"></div>
	          <div class="row">
	            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	              <h2 class="section-title">Careers in IoT</h2>
	              <div class="gary-strip"></div>
	            </div>
	          </div>
	          <div class="row">
	            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	              <p class="career-margin">IoT will create a huge opportunity for organizations and individuals to grow in the tech industry.</p>
	            </div>
	          </div>
	          <div class="row career-margin">
	            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2"> <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/career-icn1.png"> </div>
	            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
	              <p class="career-contain">Jobs in IT industry projected to grow by 50 percent before 2020 due to Internet of Things.</p>
	            </div>
	          </div>
	          <div class="row career-margin">
	            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2"> <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/career-icn2.png"> </div>
	            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
	              <p class="career-contain">New roles like IoT managers and IoT Architects are being created which never existed earlier.</p>
	            </div>
	          </div>
	          <div class="row career-margin">
	            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2"> <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/career-icn3.png"> </div>
	            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
	              <p class="career-contain">Global demand for IOT developers stand at 4.5 million by the year 2020.</p>
	            </div>
	          </div>
	        </div>
	      </div>
	    </div>
	  </div>
	  <!-- section2 end --> 
	  
	  <!-- section3 start -->
	  <div class="wrapper section-padding-top section-padding-bottom">
	    <div class="row">
	      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" data-wow-delay="0.2s">
	        <div class="carousel slide" data-ride="carousel" id="quote-carousel"> 
	          <!-- Bottom Carousel Indicators -->
	          <ol class="carousel-indicators">
	            <li data-target="#quote-carousel" data-slide-to="0" class="active"><img class="img-responsive " src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/testimonial1.jpg" alt=""></li>
	            <li data-target="#quote-carousel" data-slide-to="1"><img class="img-responsive" src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/testimonial2.jpg" alt=""></li>
	            <li data-target="#quote-carousel" data-slide-to="2"><img class="img-responsive" src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/testimonial3.jpg" alt=""> </li>
	          </ol>
	          <!-- Carousel Slides / Quotes -->
	          <div class="carousel-inner text-center"> 
	            <!-- Quote 1 -->
	            <div class="item active">
	              <blockquote>
	                <div class="row">
	                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12"></div>
	                  <div class="col-lg-8 col-md-8 col-sm-8 col-sm-8">
	                    <p class="testimonial-contain"><i>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliquaUt enim ad minim veniam."</i></p>
	                  </div>
	                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12"></div>
	                </div>
	              </blockquote>
	            </div>
	            <!-- Quote 2 -->
	            <div class="item">
	              <blockquote>
	                <div class="row">
	                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12"></div>
	                  <div class="col-lg-8 col-md-8 col-sm-8 col-sm-8">
	                    <p class="testimonial-contain"><i>“The demand for IoT Champions is growing by day. These professionals make an average of $178,000 per year.”</i></p>
	                  </div>
	                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12"></div>
	                </div>
	              </blockquote>
	            </div>
	            <!-- Quote 3 -->
	            <div class="item">
	              <blockquote>
	                <div class="row">
	                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12"></div>
	                  <div class="col-lg-8 col-md-8 col-sm-8 col-sm-8">
	                    <p class="testimonial-contain"><i>"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."</i></p>
	                  </div>
	                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12"></div>
	                </div>
	              </blockquote>
	            </div>
	          </div>
	        </div>
	      </div>
	    </div>
	  </div>
	  <!-- section3 end --> 
	  
	  <!-- section4 start -->
	  <div class="wrapper padding-left-none-xs padding-right-none-xs" id="forthpage">
	    <div class="gray-bg relative section-padding-top padding-right-15-xs padding-keft-15-xs">
	      <div class="specialization-gray-design hidden-xs"> <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/specialization-gray-design.png"> </div>
	      <div class="row">
	        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	          <h2 class="section-title center">IoT Specialization</h2>
	          <div class="gary-strip center-block"></div>
	        </div>
	      </div>
	      <div class="row">
	        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"></div>
	        <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
	          <p class="center conatin-margin-top">Get a comprehensive understanding of IoT with the only IoT specialization that gives you a thorough understanding of the various technologies associated with this field.</p>
	        </div>
	        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"></div>
	      </div>
	      <div class="row">
	        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/specialization-header.jpg" class="center-block specialization-img-margin img-responsive"> </div>
	      </div>
	    </div>
	  </div>
	  <!-- section4 end --> 
	  
	  <!-- section5 start -->
	  <div class="wrapper section-padding-bottom padding-left-none-xs padding-right-none-xs">
	    <div class="dark-gary-bg padding-right-15-xs padding-keft-15-xs">
	      <div class="row graybg-padding">
	        <div class="col-lg-8 col-md-8 col-sm-6 col-xs-12 text-center-xs">
	          <p class="spe-offer-contain">Become an IOT Wiz with the most comprehensive IOT Specialization.</p>
	        </div>
	        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-9"> 
	          <!--<div class="skew-border enroll-link"><a href="specializations.html">learn more</a></div>-->
	          <div class=""><a href="specializations" class="skew-border banner-link pull-right"><span>Learn more</span></a></div>
	        </div>
	      </div>
	    </div>
	  </div>
	</div>
	<!-- section5 end --> 

	<!-- FOOTER -->
	<?php load_template("iot", "v1/footer"); ?>
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
	        <iframe width="100%" height="400" src="https://www.youtube.com/embed/F7VJopzuZzo" frameborder="0" allowfullscreen></iframe>
	      </div>
	    </div>
	  </div>
	</div>
	<!-- modal end--> 

	<!-- LOGIN MODAL -->
	<?php load_template("iot", "v1/login"); ?>
	<!-- LOGIN MODAL ENDS -->
	
	</body>
</html>