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

	$index = 6;

?>

<!-- HTML HEAD -->
<?php
	$GLOBALS["content"]["title"] = "Advance IoT Analytics";
	load_template("iot", "v2/head");
?>
<!-- HTML HEAD ENDS -->

<!-- HEADER MENU -->
<?php load_template("iot", "v2/header"); ?>
<!-- HEADER MENU ENDS -->

<!-- Banner Start -->
<div class="advance-analytics-banner" id="side_nav_item_id_0">
  <div class="wrapper header-padding-top header-padding-bottom">
    <div class="row">
      <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
        <h1 class="innerpage-banner-title white-text arduino-title-width">Advanced IoT Analytics</h1>
        <p class="banner-contain white-text">Get an in-depth understanding of key data analytics concepts as applicable for data streams arising out of the Internet of Things. A key component of this course will be developing a first view of IoT use cases across all verticals.</p>
        <div class="margin-30"></div>
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="pull-left" style="display: inline-block !important;vertical-align: middle; float: none !important;">
              <h1 class="prize white-text"><i class="fa fa-inr" aria-hidden="true"></i> <?php echo number_format($GLOBALS['iot_courses']['courses'][$index]["sp_price_inr"]) ?><span class="taxes-text white-text">+ taxes </span> </h1>
            </div>
            <div class="pull-left"  style="display: inline-block !important;vertical-align: middle;  float: none !important;">
              <div class="text-center-xs actionbtn skew-btn sign-custom-btn">
                <a target="_blank" <?php iot_a_part('https://www.jigsawacademy.com/checkout-iot/?c='.$GLOBALS['iot_courses']['courses'][$index]["wp_id"].'&p=course','#loginmodal','#profile-popup1'); ?> ><span>Enroll Now</span>
               </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Banner End -->

<!-- submenu start -->
<div class="nav-scrolling hidden-xs">
  <div class="wrapper">
    <nav class="navbar navbar-default navbar-custom ">
      <ul class="nav navbar-nav pull-left">
        <li><a href="#about-section" class="cool-link">ABOUT THIS COURSE</a></li>
        <li><a href="#key-feature-section" class="cool-link">key features</a></li>
        <li><a href="#curriculum-section" class="cool-link">curriculum</a></li>
    <li class="hide"><a href="#capstone-project-section" class="cool-link">capstone project</a></li>
        <li><a href="#you-get-section" class="cool-link">What you Get</a></li>
      </ul>
    </nav>
  </div>
</div>
<!-- submenu end -->

<!-- fixed top tab nav start -->
<div class="" id="mybutton">
  <div class="nav-scrolling hidden-xs course-top-nav">
    <div class="row">
      <div class="wrapper">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-left-none">
          <div class="col-lg-10 col-md-10 col-sm-10 padding-left-none">
            <nav class="navbar navbar-default navbar-custom">
              <ul class="nav navbar-nav pull-left">
                <li><a href="#about-section" class="cool-link">ABOUT THIS COURSE</a></li>
                <li><a href="#key-feature-section" class="cool-link">key features</a></li>
                <li><a href="#curriculum-section" class="cool-link">curriculum</a></li>
        <li class="hide"><a href="#capstone-project-section" class="cool-link">capstone project</a></li>
                <li><a href="#you-get-section" class="cool-link">What you Get</a></li>
              </ul>
            </nav>
          </div>
          <div class="col-lg-2 col-md-2 col-sm-2">
      <div class="text-center-xs actionbtn skew-btn  margin-top-8 a-hover"> <a href="#" data-toggle="modal" data-target="#loginmodal"><span class="fixedtop-tab-nav-btn uppercase">Enroll Now</span></a> </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<!-- fixed top tab nav end -->

<!-- about section start -->
<div class="wrapper section-padding-top section-padding-bottom" id="about-section">
  <div class="row hidden-lg hidden-md hidden-sm">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <h2 class="innerpage-title text-center">About this Specialization</h2>
      <div class="gary-strip center-block"></div>
    </div>
  </div>
  <div class="conatin-margin-top hidden-lg hidden-md hidden-sm"></div>


  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">


      <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"></div>

      <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
      <p>To make a successful practitioner of analytics on the data generated in IoT, IoT analytics project personnel need to get an overarching perspective of the techniques, enterprise adoption considerations, and an end-to-end perspective of the business use case. This course provides that last mile finishing touch for making analytics practitioners aware of the most relevant techniques, the product spectrum, enterprise desiderata like visualization, security etc.  This course module explores diverse use cases of IoT for similar projects across verticals including BFSI, manufacturing, retail, energy and healthcare etc.</p>
	  <br/>
	  <p>After doing this course, students will be able to take up any IoT analytics project, architect the end-to-end needs for such a project in any vertical, and develop end-to-end solutions for IoT analytics.</p>
	  <br/>
	  <p></p>
	  <br><br>
    </div>

      <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"></div>
  </div>
   </div>
    <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">

      <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"></div>

      <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
     <div class="three-list">
    <ul class="list-inline text-center xs-text-align-left">
      <li><p class="font16"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/prerequisites.png"> &nbsp; <span class="capital f-color-blue">prerequisites : </span> IoT understanding and basic data handling.</p></li>
      <li class="hidden-xs">|</li>
      <li><p class="font16"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Monitor.png"> &nbsp; <span class="capital f-color-blue">platform : </span>R and MOA</p></li>
      <li class="hidden-xs">|</li>
      <li><p class="font16"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Calendar.png"> &nbsp; <span class="capital f-color-blue">duration : </span> 10 hours</p></li>
    </ul>
    </div>
  </div>
      <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"></div>
    </div>
  </div>

</div>

<!-- about section end -->

<!-- keyfeature section start -->
<div class="border-top">
  <div class="wrapper section-padding-top section-padding-bottom" id="key-feature-section">
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <h2 class="innerpage-title text-center">Key Features</h2>
        <div class="gary-strip center-block"></div>
      </div>
    </div>
    <div class="row conatin-margin-top">
      <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"></div>
      <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
        <div class="row">
          <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
            <div class="dark-blue-bg sp-key-circle"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/key1.png"></div>
          </div>
          <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11 paddinglft25">
            <p>IoT Hardware kit containing Arduino and Raspberry Pi</p>
          </div>
        </div>
        <div class="margin-10"></div>
        <div class="row">
          <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
            <div class="light-orange-bg sp-key-circle pull-left"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/key2.png"></div>
          </div>
          <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11 paddinglft25">
            <p>Practical learning that’s high on learning by doing </p>
          </div>
        </div>
        <div class="margin-10"></div>
        <div class="row">
          <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
            <div class="light-blue-bg sp-key-circle pull-left"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/key4.png"></div>
          </div>
          <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11 paddinglft25">
            <p>Most comprehensive IoT course designed by IoT practitioners</p>
          </div>
        </div>
        <div class="margin-10"></div>
      </div>
      <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
        <div class="row">
          <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
            <div class="light-blue-bg sp-key-circle pull-left"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/key5.png"></div>
          </div>
          <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11 paddinglft25">
            <p>Ideal leap into being an IoT-ready professional </p>
          </div>
        </div>
    <div class="margin-10"></div>
        <div class="row">
          <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
            <div class="light-green-bg sp-key-circle pull-left"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/key3.png"></div>
          </div>
          <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11 paddinglft25">
            <p>Suitable for both hardware and software professionals </p>
          </div>
        </div>
        <div class="margin-10"></div>
        <div class="row">
          <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
            <div class="light-orange-bg sp-key-circle pull-left"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/key6.png"></div>
          </div>
          <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11 paddinglft25">
            <p>Only specialization in the market that blends Analytics and Big Data with IoT concepts</p>
          </div>
        </div>
        <div class="margin-10"></div>
      </div>
      <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"></div>
    </div>
  </div>
</div>
<!-- keyfeature section start -->

<!-- curriculum section start -->
<div class="gray-bg" id="curriculum-section">
  <div class="wrapper section-padding-top section-padding-bottom">
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <h2 class="innerpage-title text-center">Curriculum</h2>
        <div class="gary-strip center-block"></div>
      </div>
    </div>
    <div class="margin-30"></div>

 <div class="row">
  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12">
  </div>
    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
    <div class="timeline-centered">

        <article class="timeline-entry">
            <div class="timeline-entry-inner">
                <div class="timeline-icon text-center">
                    <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/timeline-1.png" class="img-responsive center-block padding-top-15">
                </div>

                <div class="timeline-label">
                    <h2>Vertical Use Cases</h2>

                 <div id="accordion">
                      <div class="target-timeline">
                        <div class="timeline-mini-icon">
                          <h4>a</h4>
                        </div>
                        <h3 class="f-color-blue">Exploring Verticals</h3>
                        <p>This component illustrates several use cases of IoT across diverse verticals. The verticals showcased include: Aerospace Defence and Airlines, Automotive Vertical, Banking Payments and Financial Services, Insurance, Retail and Consumer Packaged Goods, Education, Healthcare, Manufacturing, Oil and Gas, Public Sector and Government, Resources - Agriculture, Mining and Chemicals, Utilities, Hospitality and Travel, Logistics and Transportation and Media and Entertainment.</p>
                      </div>
               </div>
              </div>
            </div>
        </article>

        <article class="timeline-entry">
            <div class="timeline-entry-inner">
                <div class="timeline-icon text-center">
                    <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/timeline-4.png" class="img-responsive center-block padding-top-15">
                </div>

                <div class="timeline-label">
                    <h2> IoT Data Clustering </h2>

					<div class="timeline-mini-icon"><h4>a</h4></div>
					<h3 class="f-color-blue">Clustering and Distance Measures</h3>

                    <p>It covers an overview of clustering as applied on IoT data, and the distance measures used in IoT analytics.</p>

					<div class="timeline-mini-icon bg-yellow"><h4>b</h4></div>
					<h3 class="f-color-blue">Streaming Clustering Algorithms Overview</h3>
                    <p>It looks at the specific needs for IoT data clustering techniques, and discusses the summary structures as needed for streaming clustering.</p>

					<div class="timeline-mini-icon"><h4>c</h4></div>
					<h3 class="f-color-blue">Common Streaming Clustering Techniques</h3>
                    <p>It explores in depth the diverse streaming clustering techniques including DenStream, CluStream and StreamKM++</p>

					<div class="timeline-mini-icon bg-yellow"><h4>d</h4></div>
					<h3 class="f-color-blue">Demo</h3>
                    <p>A demo of popular streaming clustering techniques is provided.</p>
                   </div>
                </div>

        </article>

        <article class="timeline-entry">
            <div class="timeline-entry-inner">
                <div class="timeline-icon text-center">
                    <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Icons_Inferential_Data_Analytics_for_IoT.png" class="img-responsive center-block padding-top-15">
                </div>

                <div class="timeline-label">
                    <h2>Advanced IoT Data Anomaly Detection</h2>
					<div class="timeline-mini-icon"><h4>a</h4></div>
                    <h3 class="f-color-blue">Spectrum of Techniques</h3>
                    <p>It covers the overarching spectrum of anomaly detection techniques.</p>

					<div class="timeline-mini-icon bg-yellow"><h4>b</h4></div>
                    <h3 class="f-color-blue">Nearest Neighbour Methods</h3>
                    <p>A popular lazy method of detecting anomalies in data is considered and explained.</p>

					<div class="timeline-mini-icon"><h4>c</h4></div>
                    <h3 class="f-color-blue">Classification Method</h3>
                    <p>A popular technique using machine learning based classification technique is demonstrated for anomaly detection along with a demo. Also illustrated is the concept of disproportionate sampling needed for anomaly detection. Demo is provided in R.</p>
                </div>
                </div>

        </article>

        <article class="timeline-entry">
            <div class="timeline-entry-inner">
                <div class="timeline-icon text-center">
                    <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Icons_Prescriptive_IoT_Data_Analytics_for_IoT.png" class="img-responsive center-block padding-top-15">
                </div>

                <div class="timeline-label">
                    <h2>Complex Event Processing</h2>

                    <div class="timeline-mini-icon"> <h4>a</h4> </div>
					<h3 class="f-color-blue">Basic concepts of CEP</h3>
                    <p>Covers the concepts in CEP, which predates IoT tackling similar scenarios. </p>
					<div class="timeline-mini-icon bg-yellow"> <h4>b</h4> </div>
					<h3 class="f-color-blue">CEP Functions</h3>
                    <p>It covers a broad spectrum of CEP functions ranging from root cause analysis, to event correlation.</p>
					<div class="timeline-mini-icon"> <h4>c</h4> </div>
					<h3 class="f-color-blue">CEP Tools</h3>
                    <p>A broad range of CEP tools explored in context of IoT using their event stream handling capabilities.</p>
					<div class="timeline-mini-icon bg-yellow"> <h4>d</h4> </div>
					<h3 class="f-color-blue">Use Case</h3>
                    <p>A detailed analysis of Apache Flink CEP and WS02 CEP capabilities as required for IoT. A use case of streaming SQL based approach is explored.</p>

                </div>
                </div>

        </article>

        <article class="timeline-entry">
            <div class="timeline-entry-inner">
                <div class="timeline-icon text-center">
                    <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/timeline-3.png" class="img-responsive center-block padding-top-15">
                </div>
                <div class="timeline-label">
                    <h2>IoT Product Ecosystem</h2>
                    <div class="timeline-mini-icon">
                      <h4>a</h4>
                    </div>
                    <h3 class="f-color-blue">IoT Analytics Products and Platforms</h3>
                    <p> It covers a broad spectrum of characteristics of IoT analytics products and comparison metrics. </p>

                    <div class="timeline-mini-icon bg-yellow"> <h4>b</h4> </div>
					<h3 class="f-color-blue">Case Study for IoT Temperature and Humidity Data Streams</h3>
                    <p>It covers the following platforms in depth in terms of the functions and APIs.
                        <ul>
                            <li style="font-family: lato;font-size: 17px;">Spark Streaming</li>
                            <li style="font-family: lato;font-size: 17px;">Amazon Kinesis</li>
                            <li style="font-family: lato;font-size: 17px;">IBM Watson IoT</li>
                            <li style="font-family: lato;font-size: 17px;">R - Stream Package</li>
                        </ul>
                    </p> 
                </div>
                </div>
        </article>

        <article class="timeline-entry">
            <div class="timeline-entry-inner">
                <div class="timeline-icon text-center">
                    <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Icons_Predictive_IoT_analytics_via_IoT_Data_Classification.png" class="img-responsive center-block padding-top-15">
                </div>

                <div class="timeline-label">
                    <h2>Time Series and IoT</h2>

                    <div class="timeline-mini-icon"> <h4>a</h4> </div>
					<h3 class="f-color-blue">Time Series</h3>
                    <p>It covers the basics of Time Series Modelling for continuous stream of data.</p>

					<div class="timeline-mini-icon bg-yellow"> <h4>b</h4> </div>
					<h3 class="f-color-blue ">ARIMA Modelling Technique for Forecasting</h3>
                    <p>It covers the popular ARIMA modelling technique for forecasting streaming data in time series form.</p>

					<!-- <div class="timeline-mini-icon"> <h4>c</h4> </div>
					<h3 class="f-color-blue">Case Study for IoT Temperature and Humidity Data Streams</h3>
                    <p>An end-to-end demo using R based time series for an IoT data stream from temperature and humidity sensors.</p> -->
                </div>
                </div>

        </article>

        <article class="timeline-entry">
            <div class="timeline-entry-inner">
                <div class="timeline-icon text-center">
                    <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/timeline-2.png" class="img-responsive center-block padding-top-15">
                </div>

                <div class="timeline-label">
                    <h2>Industrial Internet Use Cases</h2>

                    <div class="timeline-mini-icon">
                      <h4>a</h4>
                    </div>
                    <h3 class="f-color-blue">Basics</h3>
                    <p>Covers basic idea of Industrial Internet, and role of Industrial Internet Consortium.</p>
                    <div class="timeline-mini-icon bg-yellow">
                      <h4>b</h4>
                    </div>
                    <h3 class="f-color-blue">Broad Business Applications</h3>
                    <p>Explores IIoT across verticals including asset management, preventive maintenance, asset monitoring, asset tracking, straight through processing.</p>
                    <div class="timeline-mini-icon">
                        <h4>c</h4>
                    </div>
                    <h3 class="f-color-blue">Machine Learning Based Classification</h3>
                    <p>Detailed use case using machine learning based classification technique for a use case of manufacturing of steel involving the relevant parameters to help predict optimal combinations for annealing.</p>
                </div>
               </div>

        </article>

        <article class="timeline-entry">
            <div class="timeline-entry-inner">
                <div class="timeline-icon text-center">
                    <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Icons_IoT_Case_Study_Air_Quality_Data.png" class="img-responsive center-block padding-top-15">
                </div>

                <div class="timeline-label">
                    <h2>Data Visualization for IoT</h2>

                    <div class="timeline-mini-icon"> <h4>a</h4> </div>
					<h3 class="f-color-blue">Basic Data Visualization</h3>
                    <p>Illustrated for diverse use cases of IoT data.</p>
					<div class="timeline-mini-icon bg-yellow"> <h4>b</h4> </div>
					<h3 class="f-color-blue">Univariate Data Analysis</h3>
                    <p>Illustrations of different univariate analytics for IoT data stream with example in R.</p>
					<div class="timeline-mini-icon"> <h4>c</h4> </div>
					<h3 class="f-color-blue">Bivariate Data Analysis</h3>
                    <p>Illustrations of different bivariate analytics for IoT data stream with example in R.</p>
					<div class="timeline-mini-icon bg-yellow"> <h4>d</h4> </div>
					<h3 class="f-color-blue">Multivariate Data Analysis</h3>
                    <p>Illustration of specific use cases of IoT data beyond two dimensions.</p>
                </div>
                </div>

        </article>

         <article class="timeline-entry">
            <div class="timeline-entry-inner">
                <div class="timeline-icon text-center">
                    <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/timeline-5.png" class="img-responsive center-block padding-top-15">
                </div>

                <div class="timeline-label">
                    <h2>Security for IoT</h2>

                    <div class="timeline-mini-icon "> <h4>a</h4> </div>
					<h3 class="f-color-blue">Basic Security Requirements</h3>
                    <p>Covers basic CIA (confidentiality, integrity and availability) needs.</p>

					<div class="timeline-mini-icon bg-yellow"> <h4>b</h4> </div>
					<h3 class="f-color-blue">Security Technologies:</h3>
                    <p>Covers a range of security technologies used in enterprises. </p>

					<div class="timeline-mini-icon"> <h4>c</h4> </div>
					<h3 class="f-color-blue">Specific Security Needs of IoT</h3>
                    <p>Focus on precise security needs for IoT and IoT data. </p>

					<div class="timeline-mini-icon bg-yellow"> <h4>d</h4> </div>
					<h3 class="f-color-blue"> Layered IoT Security and IoT Device Security</h3>
                    <p>A layer wise analysis of IoT security needs. </p>

					<div class="timeline-mini-icon"> <h4>e</h4> </div>
					<h3 class="f-color-blue">IoT Security</h3>
                    <p>Covers Storage, Communication and Application levels. </p>

					<div class="timeline-mini-icon bg-yellow"> <h4>f</h4> </div>
					<h3 class="f-color-blue">IoT Data and API Security</h3>
                    <p>Covers security needs of IoT data, analytics and application interface layers. </p>

                </div>
                </div>

        </article>

		<article class="timeline-entry">
            <div class="timeline-entry-inner">
                <div class="timeline-icon text-center">
                    <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Icons_IoT_Case_Study_Air_Quality_Data.png" class="img-responsive center-block padding-top-15">
                </div>

                <div class="timeline-label">
                    <h2>Frontier Topics in IoT analytics</h2>

					<div class="timeline-mini-icon"> <h4>a</h4> </div>
					<h3 class="f-color-blue">Sensor Fusion</h3>
                    <p>A coverage of scenarios where there is a need to merge data from multiple IoT data streams to derive meaningful analytics.</p>

					<div class="timeline-mini-icon bg-yellow"> <h4>b</h4> </div>
					<h3 class="f-color-blue">Concept Drift</h3>
                    <p>A notion popular in IoT data stream where underlying data characteristics changes over time and there is a need to capture the changing dynamics of analytics thereof.</p>

                    <div class="timeline-mini-icon"> <h4>c</h4> </div>
					<h3 class="f-color-blue">Deep Learning for IoT</h3>
                    <p>Gives an introduction to the potential use of deep learning for IoT use cases.</p>
                </div>
                </div>

        </article>
    </div>
    </div>
  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12">
  </div>
    </div>
  </div>
</div>
<!-- courses section end -->

<!-- Capstone Project start -->
<div class="wrapper section-padding-top section-padding-bottom hide" id="capstone-project-section">
  <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <h2 class="innerpage-title text-center">Capstone Project</h2>
        <div class="gary-strip center-block"></div>
      </div>
    </div>
    <div class="margin-30"></div>
  <div class="row">
    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12">
    </div>
    <div class="col-lg-10 col-md-8 col-sm-10 col-xs-12 padding-left-none padding-left-15-xs">
      <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/capstone-project-icon.png" class="center-block padding-bottom-10">
      <p>Workplace Buddy is your very own personal IoT companion: a gadget that sits on your desk and tracks your work conditions as well as your work habits. It can control lighting and temperature at your desk. It also acts as a timekeeper and keep track of your working hours automatically.</p>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12">
    </div>
  </div>
</div>
<!-- Capstone Project end -->

<!-- second tabing start -->
<div class="gray-bg" id="you-get-section">
  <div class="wrapper section-padding-top section-padding-bottom spe-padding">
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <ul class="nav nav-tabs custom-tabs electronics-tab">
          <li class="active"><a data-toggle="tab" href="#menu1">WHAT YOU GET</a></li>
          <li><a data-toggle="tab" href="#menu2">THE EXPERTS</a></li>
          <li><a data-toggle="tab" href="#menu3">FAQs</a></li>
        </ul>
        <div class="tab-content tab-contain-body">
          <div id="menu1" class="tab-pane fade in active">
            <div class="row">
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="row" style="margin-bottom: 40px;">
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2"> <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/spe-IOT-Hardware-Kit.png"> </div>
                  <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
          <div class="vxs-padding-left-15">
            <h3 class="spe-tabbing2-title">IoT Hardware Kit</h3>
            <p>Get Arduino and Raspberry Pi along with WiFi Dongle, Webcam, Sensor modules and Accessories.</p>
          </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="row">
          <div class="conatin-margin-top hidden-lg hidden-md hidden-sm"></div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2"> <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Catalyst.png"> </div>
                  <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
                    <div class="vxs-padding-left-15">
                      <h3 class="spe-tabbing2-title">The Catalyst Approach</h3>
                      <p>Learn through video lectures, live question and answer sessions conducted by the faculty, assignments, interactive case study workshops with senior faculty and industry mentors.<br><a href="https://www.jigsawacademy.com/how-online-training-works/" target="_blank">Know more.</a></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row conatin-margin-top">
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="row">
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2"> <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/spe-Jigsaw-Learning-Center.png"> </div>
                  <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
          <div class="vxs-padding-left-15">
            <h3 class="spe-tabbing2-title">Jigsaw Learning Center</h3>
            <p>Gain free access to a variety of supplemental resources like handouts, reference material, guides, lecture transcripts and student forums.</p>
          </div>
          </div>
                </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="row">
          <!-- <div class="conatin-margin-top"></div> -->
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2"> <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/spe-Faculty-Support.png"> </div>
                  <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
          <div class="vxs-padding-left-15">
            <h3 class="spe-tabbing2-title">Faculty Support</h3>
            <p>Get your doubts solved by the Jigsaw Faculty via email, phone or chat.</p>
          </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div id="menu2" class="tab-pane fade">
            <div class="row">
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-center">
        <img class="img-circle img-responsive center-block" src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/expert-harikrishna.jpg" alt="">
                <h3 class="f-color-blue expert-title">Harikrishna R.</h3>
                <p>He is a co-founder and director of Klar Systems, where he designs and builds cool new gadgets, applications and platforms for the IoT era.</p>
        <a href="https://www.linkedin.com/in/harikrishnar" target="_blank"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/linkedin_icon.jpg" class="img-responsive center-block padding-top-7"></a>
        </div>
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-center">
        <div class="margin-30 hidden-lg hidden-md hidden-sm"></div>
        <img class="img-circle img-responsive center-block" src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/expert-urmil.jpg" alt="">
                <h3 class="f-color-blue expert-title">Urmil Parikh</h3>
                <p>He is a co-founder of Klar Systems, an emerging IoT start-up.He carries extensive experience in embedded systems, connectivity and application development.</p>
                <a href="https://www.linkedin.com/in/parikhurmil" target="_blank"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/linkedin_icon.jpg" class="img-responsive center-block padding-top-7"></a>
              </div>
            </div>
          </div>
          <div id="menu3" class="tab-pane fade">
            <div class="row">
              <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2">
                <div class="dark-blue-bg sp-key-circle">
                  <p class="white-text">01</p>
                </div>
              </div>
              <div class="col-lg-11 col-md-11 col-sm-11 col-xs-10 paddinglft0 padding-left-15-xs">
                <h3 class="spe-tabbing2-title">What will be the impact of IoT on our careers?</h3>
                <p>IoT is the next big revolution in tech space and it has already started having a huge impact on both businesses and consumers. Every fifth developer today is working on IoT related projects...</p>
              </div>
            </div>
            <div class="row conatin-margin-top">
              <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2">
                <div class="light-orange-bg sp-key-circle">
                  <p class="white-text">02</p>
                </div>
              </div>
              <div class="col-lg-11 col-md-11 col-sm-11 col-xs-10 paddinglft0 padding-left-15-xs">
                <h3 class="spe-tabbing2-title">How are IoT and Analytics connected?</h3>
                <p>EMC estimates that IoT will account for 4.4 trillion GB of the data in the digital universe by 2020. However, the real value lies in the intersection of gathering data and leveraging it...</p>
              </div>
            </div>
            <div class="row conatin-margin-top">
        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
          <div class="text-center-xs actionbtn skew-btn spe-btn-padding uppercase"><a href="faqs"><span>View All</span></a></div>
        </div>
      </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<!-- second tabing end -->

<!-- gray-band start -->
<div class="wrapper section-padding-top section-padding-bottom">
  <div class="row spe-gray-band">
    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
      <h3 class="spe-gray-band-text f-color-blue font-19">4 courses. 1 certification. Rs 14,000 in savings. Become a certified IoT professional.</h3>
      </span> </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
      <div class="text-center-xs actionbtn skew-btn spe-btn-padding uppercase"><a target="_blank" <?php iot_a_part('https://www.jigsawacademy.com/checkout-iot/?c='.$GLOBALS['iot_courses']['bundles'][0]["wp_id"].'&p=path','#loginmodal','#profile-popup1'); ?> ><span>Enroll Now</span>
        </a></div>
    </div>
  </div>
</div>
<!-- gray-band end -->

  <!-- FOOTER -->
  <?php load_template("iot", "v2/footer"); ?>
  <!-- FOOTER ENDS -->

  <!-- LOGIN MODAL -->
  <?php load_template("iot", "v2/login"); ?>
  <!-- LOGIN MODAL ENDS -->

<script>
  $(".readmore").on('click touchstart', function(event) {
        var txt = $(this).parent().prev(".more-content").is(':visible') ? 'Show More (+)' : 'Less (–)';
        $(this).parent().prev(".more-content").toggleClass("visible");
        $(this).html(txt);
        event.preventDefault();
    });
</script>
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