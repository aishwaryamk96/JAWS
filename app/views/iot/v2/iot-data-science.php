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

	$index = 5;

?>

<!-- HTML HEAD -->
<?php
  $GLOBALS["content"]["title"] = "Data Science for IOT";
  $GLOBALS["content"]["meta_description"] = "Become an expert on processing and analysing IoT data with advanced analytics methods. Get detailed understanding of the role data analytics plays in deriving business value out of Internet of Things.";
	load_template("iot", "v2/head");
?>
<!-- HTML HEAD ENDS -->

<!-- HEADER MENU -->
<?php load_template("iot", "v2/header"); ?>
<!-- HEADER MENU ENDS -->

<!-- Banner Start -->
<div class="datascience-banner" id="side_nav_item_id_0">
	<section class="wrapper breadcrumb-start">
		<ol class="bread-crumbs">
			<li>
				<span itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
					<a href="https://www.jigsawacademy.com/iot/" itemprop="url">
						<span itemprop="title">IoT Home</span>
					</a>
				</span>
			</li>
			<li>
				<span itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
					<a class="last" href="https://www.jigsawacademy.com/iot/data-science-for-iot" itemprop="url">
						<span itemprop="title">Data Science for IoT</span>
					</a>
				</span>
			</li>
		</ol>
	</section>
  <div class="wrapper header-padding-top header-padding-bottom">
    <div class="row">
      <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
        <h1 class="innerpage-banner-title white-text arduino-title-width">Data Science for IoT</h1>
		<div class="rating" itemscope="itemscope" itemtype="http://data-vocabulary.org/Review-aggregate">
			<img src="https://www.jigsawacademy.com/wp-content/uploads/2016/08/three_nine_star.png" alt="3.9 Star Rating: Very Good" width="79" height="15" title="3.9">
			<span>3.9 Ratings </span>
			<meta itemprop="itemreviewed" content="Data Science for IoT">
			<meta itemprop="rating" content="3.9">
			<meta itemprop="votes" content="20">
		</div>
        <p class="banner-contain white-text">Become an expert on processing and analysing IoT data with advanced analytics methods.At the same time understand the data lifecycle as applied to IoT data. A key component of this course will be an understanding of stream processing algorithms which are necessary for handling IoT data.</p>
        <div class="margin-30"></div>
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="pull-left" style="display: inline-block !important;vertical-align: middle; float: none !important;">
              <!--<h3 class="cancel-prize"> <strike><i class="fa fa-inr" aria-hidden="true"></i>35,000</strike> </h3>-->
              <h1 class="prize white-text"><i class="fa fa-inr" aria-hidden="true"></i> <?php echo number_format($GLOBALS['iot_courses']['courses'][$index]["sp_price_inr"]) ?><span class="taxes-text white-text">+ taxes </span> </h1>
            </div>
            <div class="pull-left"  style="display: inline-block !important;vertical-align: middle;  float: none !important;">
              <!--<div class="text-center-xs actionbtn skew-btn sign-custom-btn"> <a href="#" data-toggle="modal" data-target="#loginmodal"><span class="sign-up-custom uppercase">Enroll Now</span></a> </div>-->
              <div class="text-center-xs actionbtn skew-btn sign-custom-btn">
                <a target="_blank" <?php iot_a_part('https://www.jigsawacademy.com/checkout-iot/?c='.$GLOBALS['iot_courses']['courses'][$index]["wp_id"].'&p=course','#loginmodal','#profile-popup1'); ?> ><span>Enroll Now</span>
                <?php //echo "<pre>"; print_r($GLOBALS['iot_courses']); die(); ?>
               </a>
              <!-- <a href="#" data-toggle="modal" data-target="#leadmodal" data-course-id="<?php// echo $GLOBALS['iot_courses']['courses'][$index]['course_id']; ?>" data-course-name="<?php //echo $GLOBALS['iot_courses']['courses'][$index]['name']; ?>" ><span class="sign-up-custom uppercase">Notify Me</span></a> -->
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
    <!-- <li><a href="#capstone-project-section" class="cool-link">capstone project</a></li> -->
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
        <!-- <li><a href="#capstone-project-section" class="cool-link">capstone project</a></li> -->
                <li><a href="#you-get-section" class="cool-link">What you Get</a></li>
              </ul>
            </nav>
          </div>
          <div class="col-lg-2 col-md-2 col-sm-2">
            <!--<div class="pull-left"><a href="#" class="uppercase signup-btn skew-btn innerpage-btn margin-top-8 a-hover">Enroll Now</a></div>-->
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
      <p>Get a detailed understanding of the role data analytics plays in deriving business value out of Internet of Things.  A key component of this course will be developing an understanding of the different kinds of IoT use cases across verticals, a brief overview of the IoT ecosystem, communication needs for IoT, the overarching architecture and an understanding of the data architecture. Alongside topics that will be illustrated include different analytics as applied to IoT, different key performance indicators (KPIs) in IoT analytics, along with a preliminary understanding of some basic algorithms for IoT analytics.</p>
      <!--<br>
      <p>After doing this course you should be able to put together IoT projects driven by the Arduino and connect them to cloud with mobile applications. </p>--><br><br>
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
      <li><p class="font16"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/prerequisites.png"> &nbsp; <span class="capital f-color-blue">prerequisites : </span> IOT understanding and basic data handling.</p></li>
      <li class="hidden-xs">|</li>
      <li><p class="font16"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Monitor.png"> &nbsp; <span class="capital f-color-blue">platform : </span>Python and MOA</p></li>
      <li class="hidden-xs">|</li>
      <li><p class="font16"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Calendar.png"> &nbsp; <span class="capital f-color-blue">duration : </span> 6 hours</p></li>
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
                    <h2>Module Overview</h2>

                 <div id="accordion">
                      <div class="target-timeline">
                        <div class="timeline-mini-icon">
                          <h4>1</h4>
                        </div>
                        <h3 class="f-color-blue">IoT Basics</h3>
                        <p>A commentary on the overarching structure of this module of IoT analytics training – “Data Science for IoT”.</p>
                      </div>

                    <!-- <div class="target-timeline">
                         <div class="timeline-mini-icon bg-yellow">
                            <h4>2</h4>
                         </div>
                        <h3 class="f-color-blue">IoT reference model</h3>
                        <p>A basic coverage of key concepts in IoT and how they relate to each other. This includes a coverage of the wide varieties of sensors, and actuators.</p>
                    </div> -->


              <!-- <div class="more-content"> -->

                <!--  <div class="target-timeline">
                    <div class="timeline-mini-icon">
                        <h4>3</h4>
                     </div>
                    <h3 class="f-color-blue">IoT reference architecture</h3>
                    <p>This covers the overall IT stack needed to deal with IoT systems including devices, communication, and data.</p>
                 </div> -->

                <!--  <div class="target-timeline">
                    <div class="timeline-mini-icon bg-yellow">
                        <h4>4</h4>
                     </div>
                    <h3 class="f-color-blue">IoT APIs and Web of Things</h3>
                    <p>A detailed coverage of the wide variety of communication infrastructure of IoT, protocols and interfaces for interacting with IoT systems. Web of things is a focused approach using Internet to interact with IoT systems.</p>
                 </div> -->

                 <!-- <div class="target-timeline">
                    <div class="timeline-mini-icon bg-yellow">
                        <h4>4</h4>
                     </div>
                    <h3 class="f-color-blue">Analytics for IoT</h3>
                    <p>Covers a description of the five kinds of analytics - Descriptive, Inferential, Exploratory, Predictive and Prescriptive analytics and a reference model for IoT.</p>
                 </div> -->

               <!--  </div> -->
             <!--  <p> <a class="readmore" href="#">Show More (+)</a></p> -->
               </div>
              </div>
            </div>
        </article>


         <article class="timeline-entry">
            <div class="timeline-entry-inner">
                <div class="timeline-icon text-center">
                    <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/timeline-2.png" class="img-responsive center-block padding-top-15">
                </div>

                <div class="timeline-label">
                    <h2>Special IoT Applications</h2>

                    <div class="timeline-mini-icon">
                      <h4>1</h4>
                    </div>
                    <h3 class="f-color-blue">IoT in Retail</h3>
                    <p><b>Overview of RFID:</b> Examines the overview of RFID as a key early IoT technology for retail.</p>
                    <p><b>RFID Use Cases:</b> Examines the various use cases of RFID technology across verticals dominated by retail.</p>
                    <p><b>Introduction to iBeacon:</b> Explores use of iBeacon as a recent enabling IoT technology for retail.</p>
                    <div class="timeline-mini-icon bg-yellow">
                      <h4>2</h4>
                    </div>
                    <h3 class="f-color-blue">Industrial IoT</h3>
                    <p><b>Introduction to Industrial Internet:</b> Covers the diverse spectrum of issues, framework and enabling technologies for IoT as applied in industry settings like manufacturing, also termed as Industrial Internet of Things (IIoT).</p>
                    <p><b>IIoT Use Cases:</b> Covers a wide range of use cases of IIoT in verticals like manufacturing, transportation and others with focus on preventive maintenance.</p>
                    <p><b>Industrial Internet Consortium and its Role:</b> Examines the role of IIC consortium in formulating standards, use cases and evangelism of IIoT.</p>
                 <!--  <div class="more-content">
                    <div class="timeline-mini-icon">
                        <h4>3</h4>
                    </div>
                    <h3 class="f-color-blue">IoT in Smart Environments</h3>
                    <p>Covers in detail the various facets of smart environments including smart cities and smart buildings.</p>
                  </div> -->
                     <!-- <p><a class="readmore" href="#">Show More (+)</a></p> -->
                </div>
               </div>

        </article>
      <article class="timeline-entry">
            <div class="timeline-entry-inner">
                <div class="timeline-icon text-center">
                    <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/timeline-3.png" class="img-responsive center-block padding-top-15">
                </div>

                <div class="timeline-label">
                    <h2>IoT Analytics Data Science LifeCycle</h2>

                    <div class="timeline-mini-icon">
                      <h4>1</h4>
                    </div>

                    <h3 class="f-color-blue"></h3>
                    <p><b>Overview of IoT Data Science Lifecycle:</b> It presents a customized view of the popular data science process CRISP-DM as specialized for IoT data.</p>
                    <p><b>Stages of IoT Data Science Lifecycle:</b> Covers in brief the needs for IoT data as per each stage of the CRISP methodology.</p>
                    <p><b>Technique and Tool View:</b> Covers a spectrum of tools and techniques for each IoT data science process stage.</p>
                     <div class="timeline-mini-icon bg-yellow">
                      <h4>2</h4>
                     </div>
                    <h3 class="f-color-blue">Data Acquisition for IoT</h3>
                    <p><b>Overview of Data Ingestion Requirements:</b> Covers the wide ranging needs for IoT data use cases from a data acquisition and ingestion perspective including reliable messaging.</p>
                    <p><b>IoT Data Ingestion Frameworks:</b> Covers the breadth of product features of various open source and commercial data ingestion frameworks.</p>
                    <p><b>Ingestion with Kafka:</b> Includes details of data ingestion capabilities of Apache Kafka.</p>
                    <p><b>Apache Storm for Data Ingestion:</b> Includes details of data ingestion capabilities of Apache Storm.</p>
                    <p><b>Amazon Kinesis, Apache Flume and Apache Samza:</b> Coverage of these three frameworks for Data Ingestion.</p>

                    <div class="more-content">
                    <div class="timeline-mini-icon">
                    <h4>3</h4>
                    </div>
                    <h3 class="f-color-blue">IoT Data Cleaning</h3>
                    <p><b>Overview of IoT Data Cleaning :</b> Covers the needs for data cleaning and types of data cleaning as required of IoT data.</p>
                    <p><b>Missing Data:</b> In focus analysis of missing data in IoT streams.</p>
                    <p><b>Data Imputation:</b> Covers various approaches for Living with missing data via imputing of the missing data.</p>
                    <p><b>KNN Based Data Imputation:</b> A focused approach to data imputation for IoT data with K – Nearest Neighbour Algorithm.</p>
                    <p><b>Demo of Imputation Using Mean and Median :</b> Demo of the data imputation approach using global mean and median.</p>
                  </div>
                     <p><a class="readmore" href="#">Show More (+)</a></p>
                </div>
                </div>

        </article>

       <article class="timeline-entry">
            <div class="timeline-entry-inner">
                <div class="timeline-icon text-center">
                    <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/timeline-4.png" class="img-responsive center-block padding-top-15">
                </div>

                <div class="timeline-label">
                    <h2>Descriptive IoT Data Analytics</h2>

                   <!--  <div class="timeline-mini-icon">
                      <h4>1</h4>
                    </div>

                    <h3 class="f-color-blue">Overview</h3> -->
                    <p><b>Basic Descriptive IoT Analytics: </b>A coverage of basic measures of central tendency and measures of dispersion.</p>
                    <p><b>Code Walkthrough of Basic Descriptive IoT Analytics: </b>A code walkthrough of basic measures of central tendency and measures of dispersion.</p>
                    <p><b>Sliding Window Based Descriptive Analytics: </b>Sliding window version of basic measures of central tendency and measures of dispersion for IoT data.</p>
                    <p><b>Code Walkthrough:</b> Sliding Window Based Descriptive Analytics for IoT data.</p>
                    <p><b>Additional Descriptive IoT Analytics:</b> Covers more measures like kurtosis, and correlation across multiple IoT streams.</p>
                    <p><b>Code Walkthrough of Additional Descriptive IoT Analytics :</b> Demo of more measures like kurtosis, and correlation across multiple IoT streams.</p>
                    <p><b>Streaming SQL: </b> Feature offered in SQL like language for some Stream processing engines to compute measures of central tendency/dispersion on a window in a stream.</p>
                   <!--   <div class="timeline-mini-icon bg-yellow">
                      <h4>2</h4>
                     </div> -->
                    <!-- <h3 class="f-color-blue">Anomaly Detection</h3>
                    <p>A detailed analysis of anomaly detection in IoT business scenarios.</p> -->

                    <!-- <div class="more-content">
                        <div class="timeline-mini-icon">
                        <h4>3</h4>
                        </div>
                        <h3 class="f-color-blue">Walkthrough of Anomaly Detection</h3>
                        <p>A code walkthrough of a detailed anomaly detection algorithm for a smart environment case study.</p>
                      </div> -->
                         <!-- <p><a class="readmore" href="#">Show More (+)</a></p> -->
                   </div>
                </div>

        </article>

        <article class="timeline-entry">
            <div class="timeline-entry-inner">
                <div class="timeline-icon text-center">
                    <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Icons_Inferential_Data_Analytics_for_IoT.png" class="img-responsive center-block padding-top-15">
                </div>

                <div class="timeline-label">
                    <h2>Inferential Data Analytics for IoT</h2>

                  <!--   <div class="timeline-mini-icon">
                      <h4>1</h4>
                    </div>

                    <h3 class="f-color-blue"></h3> -->
                    <p><b>Overview of Inferential Analytics:</b> Covers some broad techniques for handling summaries of data as in IoT data including sampling and sketching.</p>
                    <p><b>Reservoir Sampling:</b> A popular concise technique for sampling streams of IoT data.</p>
                    <p><b>Sketching and Hashing :</b> Covers popular hashing and sketching techniques for streaming data including Min-Count Sketching and Reservoir Sampling.</p>
                </div>
                </div>

        </article>
        <!-- added by nikita -->
         <article class="timeline-entry">
            <div class="timeline-entry-inner">
                <div class="timeline-icon text-center">
                    <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Icons_Predictive_IoT_analytics_via_IoT_Data_Classification.png" class="img-responsive center-block padding-top-15">
                </div>

                <div class="timeline-label">
                    <h2>Predictive IoT analytics via IoT Data Classification</h2>

                   <!--  <div class="timeline-mini-icon">
                      <h4>1</h4>
                    </div>

                    <h3 class="f-color-blue"></h3> -->
                    <p><b>Supervised Learning and Classification: </b>Covers a brief of classification approaches as used in IoT data.</p>
                    <p><b>Streaming Classification with Decision Trees:</b> A detailed analysis of streaming decision trees based classification technique for streaming IoT data.</p>
                    <p><b>Demo using MOA: </b>Demo of streaming classification using MOA toolkit.</p>
                </div>
                </div>

        </article>

         <article class="timeline-entry">
            <div class="timeline-entry-inner">
                <div class="timeline-icon text-center">
                    <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/timeline-5.png" class="img-responsive center-block padding-top-15">
                </div>

                <div class="timeline-label">
                    <h2>Exploratory IoT Data Analytics via Clustering</h2>

                   <!--  <div class="timeline-mini-icon">
                      <h4>1</h4>
                    </div>

                    <h3 class="f-color-blue"></h3> -->
                    <p><b>Exploratory Data Analytics with Clustering for Streaming Data :</b> A thorough analysis of clustering needs for streaming IoT data.</p>
                    <p><b>K-Means Clustering:</b> A coverage of basics of K-means clustering the basis for all cluster analysis.</p>
                    <p><b>Streaming Clustering Algorithms:</b> A breadth of different clustering algorithms analysed.</p>
                    <p><b>Streaming K-Means Clustering Algorithm :</b> An in-depth analysis of streaming K-Means clustering algorithm.</p>
                </div>
                </div>

        </article>

         <article class="timeline-entry">
            <div class="timeline-entry-inner">
                <div class="timeline-icon text-center">
                    <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Icons_Prescriptive_IoT_Data_Analytics_for_IoT.png" class="img-responsive center-block padding-top-15">
                </div>

                <div class="timeline-label">
                    <h2>Prescriptive IoT Data Analytics for IoT</h2>

                    <!-- <div class="timeline-mini-icon">
                      <h4>1</h4>
                    </div>

                    <h3 class="f-color-blue"></h3> -->
                    <p><b>Need for Prescriptive Analytics :</b> A ground level exploration of the different needs for prescriptive analytics for IoT.</p>
                    <p><b>Additional Use Cases of Prescriptive Analytics :</b> An in-depth analysis of some IoT analytics use cases requiring Prescriptive Analytics.</p>
                    <p><b>Prescriptive Analytics Techniques:</b> Some popular techniques for prescriptive analytics explored.</p>
                    <p><b>Prioritization Techniques: </b>An indepth analysis of some key prioritization techniques, key for prescriptive IoT analytics.</p>
                </div>
                </div>

        </article>

         <article class="timeline-entry">
            <div class="timeline-entry-inner">
                <div class="timeline-icon text-center">
                    <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Icons_IoT_Case_Study_Air_Quality_Data.png" class="img-responsive center-block padding-top-15">
                </div>

                <div class="timeline-label">
                    <h2>IoT Case Study: Air Quality Data</h2>

                   <!--  <div class="timeline-mini-icon">
                      <h4>1</h4>
                    </div>

                    <h3 class="f-color-blue"></h3> -->
                    <p><b>Descriptive Analytics:</b> A code walkthrough of descriptive analytics for IoT air quality data.</p>
                    <p><b>Inferential and Exploratory Analytics:</b> A code walkthrough of of sampling and clustering of IoT air quality data.</p>
                    <p><b>Predictive Analytics with Classification:</b> A decision tree based predictive analytics demo of IoT air quality data.</p>
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
<div class="wrapper section-padding-top section-padding-bottom" id="capstone-project-section" style="display:none; ">
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
          <li class="active"><a data-toggle="tab" data-target="#menu1" href="javascript:;">WHAT YOU GET</a></li>
          <li><a data-toggle="tab" data-target="#menu2" href="javascript:;">THE EXPERTS</a></li>
          <li><a data-toggle="tab" data-target="#menu3" href="javascript:;">FAQs</a></li>
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
            <p>Get Arduino and Raspberry Pi along with a webcam, sensor modules and other accessories. This exclusive kit is available for the IOT Professional (not applicable for only IoT for Beginners) and Full Stack IoT Analytics course students only.</p>
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