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
	$GLOBALS["content"]["title"] = "Career in IoT | IoT Careers";
	$GLOBALS["content"]["meta_description"] = "Career in IoT - Build your career by enrolling yourself in the Internet of Things courses and training programs. Explore the IoT career opportunities and enhance your skills for rising IoT market with Jigsaw Academy.";
	load_template("iot", "v2/head");
  ?>
  <!-- HTML HEAD ENDS -->

  <!-- HEADER MENU -->
  <?php load_template("iot", "v2/header"); ?>
  <!-- HEADER MENU ENDS -->

<!-- Banner Start -->
<div class="gray-bg section sTop" id="side_nav_item_id_0">
	<section class="wrapper breadcrumb-start">
		<ol class="bread-crumbs">
			<li>
				<span itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
					<a href="https://www.jigsawacademy.com/iot/" itemprop="url" class="blue-text">
						<span itemprop="title">IoT Home</span>
					</a>
				</span>
			</li>
			<li>
				<span itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
					<a href="https://www.jigsawacademy.com/iot/iot-career" itemprop="url" class="blue-text last">
						<span itemprop="title">IoT Career</span>
					</a>
				</span>
			</li>
		</ol>
	</section>
  <div class="wrapper header-padding-top header-padding-bottom">
    <div class="row">
      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 career-header">
       <span>
        <h1 class="innerpage-banner-title black-title"><span class="blue-text">Career in IoT - <br class="hidden-xs"> Why does it matter?</span></h1>
        <p class="banner-contain padding-bottom-10">The Internet of Things (IoT) is set to sweep the globe, and become a $300 billion industry by 2020. IoT is increasingly being hailed in the tech and other industries as the inevitable future of the internet and computing.</p>
        <div class="margin-30"></div>
        <div class="row">
           
           <div class="col-lg-8 col-md-8 col-sm-10 col-xs-12">
              <div class="text-center-xs actionbtn skew-btn uppercase"><a href="https://www.jigsawacademy.com/iot/blog/" target="_blank"><span>Explore IoT</span></a></div>
           </div>
           
        </div>
        <div class="margin-30"></div>
        </span>
        
      </div>
    
      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
          <iframe width="100%" height="335" src="https://www.youtube.com/embed/3C0sg2bjvtc" frameborder="0" allowfullscreen></iframe>
      </div>
    </div>
  </div>
</div>
<!-- Banner End --> 



<!-- courses section start -->
<div class="" id="courses-section">
  <div class="wrapper section-padding-top section-padding-bottom">
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <h2 class="innerpage-title text-center">The Future is here. Get on board!</h2>
        <div class="gary-strip center-block"></div>
      </div>
    </div>
    <div class="row section-padding-top">
      <div class="col-lg-12 col-md-12 col-sm-12">
           <p class="text-center">IoT will create a huge opportunity for organizations and individuals to grow in the tech industry. </p>
        <div id="owl-demo">
          <div class="owl-item">
            <div class="career-owl specialization-box relative">
              <p class="specialization-box-contain center">Jobs in IT industry projected to be up by 50 percent before 2020 due to Internet of Things</p>
              <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/cisco.png" class="img-responsive center-block padding-top-25">
            </div>
          </div>
          
          <div class="owl-item">
            <div class="career-owl-2 specialization-box relative">
              <p class="specialization-box-contain center padding-bottom-10">Global demand for IoT developers stand at 4.5 million by the year 2020. (Accenture) </p>
              <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/box-icn1.jpg" class="img-responsive center-block padding-top-25">
            </div>
          </div>
          
          <div class="owl-item">
            <div class="career-owl-3 specialization-box relative">
              <p class="specialization-box-contain center">Average annual salary for an IoT Architect is $179,000 Indeed Salary Search.</p>
               <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/indeed.jpg" class="img-responsive center-block padding-top-25">
               
            </div>
          </div>
          
          <!-- <div class="owl-item">
            <div class="career-owl specialization-box relative">
              <p class="specialization-box-contain center">New roles like IoT managers and IoT Architects are being created which never existed earlier.</p>
            </div>
          </div> -->
          
          <div class="owl-item">
            <div class="career-owl-2 specialization-box relative">
              <p class="specialization-box-contain center">One in five developers are targeting IoT for upcoming projects.</p>
        <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/testimonial04.jpg" class="img-responsive center-block padding-top-25" alt="IoT Projects Testimonial">
            </div>
          </div>
          
           <div class="owl-item">
            <div class="career-owl-3 specialization-box relative">
              <p class="specialization-box-contain center">IoT projects will take twice as long to complete as first predicted, and firms are finding it difficult to source long-term staff for the jobs.</p>
        <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/box-icn3.jpg" class="img-responsive center-block padding-top-25">
        
            </div>
          </div>
          
        </div>
       <!-- <div class="owl-buttons">
          <div class="owl-prev"></div>
          <div class="owl-next"></div>
        </div>  -->
      </div>    
    </div>  
    
        
  </div>
</div>
<!-- courses section end --> 

<!--tabing start -->
<div class="gray-bg">
  <div class="wrapper section-padding-top section-padding-bottom spe-padding">
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <ul class="nav nav-tabs custom-tabs">
                    <li class="active"><a data-toggle="tab" href="#menu1">Companies hiring IoT professionals</a></li>
          <li><a data-toggle="tab" href="#menu2">WHO NEEDS IoT PROFESSIONALS?</a></li>
          <li><a data-toggle="tab" href="#menu3">Job roles in IoT domain</a></li>
          
        </ul>

        <div class="tab-content tab-contain-body">
          <div id="menu2" class="tab-pane fade in ">
              
                          <div class="row">
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                      <div class="tab-circle-icon"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/health.png"></div>
                       <p class="text-center">Healthcare</p>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                       <div class="tab-circle-icon"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Manufacturing.png"></div>
                       <p class="text-center">Manufacturing</p>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                       <div class="tab-circle-icon"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Retail.png"></div>
                       <p class="text-center">Retail</p>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                       <div class="tab-circle-icon padding-top-25"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Transportation.png"></div>
                       <p class="text-center">Transportation</p>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                       <div class="tab-circle-icon"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Telecom.png"></div>
                       <p class="text-center">Telecommunications</p>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                       <div class="tab-circle-icon padding-top-25"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Software.png"></div>
                       <p class="text-center">Software</p>
                  </div>
                </div>
                
                <div class="margin-40 hidden-xs"></div>
                
                <div class="row">
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                      <div class="tab-circle-icon padding-top-15"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Systemdesign.png"></div>
                       <p class="text-center">System Design</p>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                       <div class="tab-circle-icon padding-top-15"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Semiconductor.png"></div>
                       <p class="text-center">Semiconductor & Components</p>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                       <div class="tab-circle-icon padding-top-15"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Publicsector.png"></div>
                       <p class="text-center">Public Sector</p>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                       <div class="tab-circle-icon"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Telematics.png"></div>
                       <p class="text-center">Telematics</p>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                       <div class="tab-circle-icon padding-top-25"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Utilities.png"></div>
                       <p class="text-center">Utilities</p>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                       
                  </div>
                </div>
            
      </div>
          
          <div id="menu3" class="tab-pane fade">
            <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <ul class="fa-ul list-font">
                              <li>IoT Developer</li>
                              <li>Senior IoT Developer</li>
                              <li>Embedded/IoT Software Engineer</li>
                            </ul>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <ul class="fa-ul list-font">
                              <li>IoT Solution Architect</li>
                              <li>IoT Data Scientist</li>
                              <li>Director IoT Products</li>
                            </ul>
                        </div>
                      </div>
          </div>
          
          <div id="menu1" class="tab-pane fade in active">
                          <ul class="list-inline text-center company-logo">
                             <li><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/ge.png" class="img-responsive"></li>
                             <li><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/google.png" class="img-responsive"></li>
                             <li><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/amazon.png" class="img-responsive"></li>
                             <li><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/intel.png" class="img-responsive"></li>
                             <li><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/hp.png" class="img-responsive"></li>
                             <li><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/redhat.png" class="img-responsive"></li>
                             <li><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Honeywell.png" class="img-responsive"></li>  
                             <li><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/ibm.png" class="img-responsive"></li>
                             <li><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/cisco.png" class="img-responsive"></li>
                             <li><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/dell.png" class="img-responsive"></li>
                             <li><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/booz.png" class="img-responsive"></li>
                             <li><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/informatica.png" class="img-responsive"></li>
                             <li><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/bosch.png" class="img-responsive"></li>
                             <li><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Accenture-2.png" class="img-responsive"></li>
                             <li><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/wipro.png" class="img-responsive"></li>
                             <li><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/career_cubical.jpg" class="img-responsive"></li>
                             <li><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/samsung.png" class="img-responsive"></li>
                             <li><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Siemens.png" class="img-responsive"></li>
                             <li><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/qualcomm.png" class="img-responsive"></li>
                             <li><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/career_altizon.jpg" class="img-responsive"></li>
                             <li><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/career_cariq.jpg" class="img-responsive"></li>
                             <li><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/career_chai_point.jpg" class="img-responsive"></li>
                             <li><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/career_oakter.jpg" class="img-responsive"></li>
                             <li><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/career_smartron.jpg" class="img-responsive"></li>
                          </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!--tabing end -->

<!--Landscape section start -->

    <div class="wrapper section-padding-top section-padding-bottom" id="you-get-section">
      <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 section-padding-top">
          <h2 class="innerpage-title text-center">The IoT Landscape In India</h2>
          <div class="gary-strip center-block"></div>
          <div class="margin-30"></div>          
          <p class="text-center">The government of India’s Department of Electronics and Information Technology (DeitY) released <br class="hidden-xs"> the first IoT policy framework as a part of the broader ‘Digital India’ vision and this is what it says:</p>
        </div>
      </div>
      
          <div class="row section-padding-top section-padding-bottom">
             <div class="col-lg-4 col-md-4 col-sm-4">
               <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/market-by-2020.png" class="img-responsive center-block">
               <p class="text-center landscape-custom">The IoT industry in India is expected to be a $15 billion market by 2020.</p>
             </div>
             <div class="col-lg-4 col-md-4 col-sm-4 xs-padding-top-15">
               <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/IoT_Career.png" class="img-responsive center-block">
               <p class="text-center landscape-custom-center">India would have a share of 5-6% of the global IoT industry.</p>
             </div>
             <div class="col-lg-4 col-md-4 col-sm-4 xs-padding-top-15">
                <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/3M-App-Developers.png" class="img-responsive center-block">
                <p class="text-center landscape-custom">India will have 3 million mobile app developers by 2017 and a majority of them will focus on IoT (Convergence Catalyst).</p>
             </div>
          
          </div>
    </div>

<!--Landscape section end -->


<!--Start learning about IoT section start-->

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
          <p class="center conatin-margin-top">IoT is an overall field where everyone right from the domain of electronics to mechanical and computer science can find a place.<br/> But there’s a catch: To get the best jobs, you’ll need the right skills and plenty of experience. </p>
          <br>
          <p class="text-center">Understand more about IoT and analytics through comprehensive, real-time, hands-on courses from Jigsaw Academy.</p>
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

<!--Start learning about IoT section end--> 

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
      autoPlay: 5000, //Set AutoPlay to 5 seconds 
      items : 3,
      itemsDesktop : [1199,3],
      itemsDesktopSmall : [979,2],
      navigation : false,
      pagination:  true,
       slideBy: 1
    }); 
  });
</script>
<!-- FOOTER -->
	<?php load_template("iot", "v2/foot"); ?>
<!-- FOOTER ENDS -->