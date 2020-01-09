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

      $index = 3;

?>

  <!-- HTML HEAD -->
  <?php
  $GLOBALS["content"]["title"] = "Cloud Computing using IoT";
  $GLOBALS["content"]["meta_description"] = "Explore Cloud Computing and the IoT. Examine real-world IoT applications such as wearables, home automation and smart cities.";
	load_template("iot", "v2/head");
  ?>
  <!-- HTML HEAD ENDS -->

  <!-- HEADER MENU -->
  <?php load_template("iot", "v2/header"); ?>
  <!-- HEADER MENU ENDS -->

<!-- Banner Start -->
<div class="iot-cloud-banner" id="side_nav_item_id_0">
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
					<a class="last" href="https://www.jigsawacademy.com/iot/iot-cloud" itemprop="url">
						<span itemprop="title">IoT Cloud</span>
					</a>
				</span>
			</li>
		</ol>
	</section>
  <div class="wrapper header-padding-top header-padding-bottom">
    <div class="row">
      <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12 v-center-text xs-display-block">
    <span>
        <h1 class="innerpage-banner-title white-text">IoT and the Cloud</h1>
		<div class="rating" itemscope="itemscope" itemtype="http://data-vocabulary.org/Review-aggregate">
			<img src="https://www.jigsawacademy.com/wp-content/uploads/2016/08/three_nine_star.png" alt="4 Star Rating: Very Good" width="79" height="15" title="4">
			<span>4 Ratings </span>
			<meta itemprop="itemreviewed" content="IoT and the Cloud">
			<meta itemprop="rating" content="4">
			<meta itemprop="votes" content="20">
		</div>
        <p class="banner-contain white-text">Explore Cloud Computing and the IoT.</p>
        <div class="margin-30"></div>
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="pull-left">
              <!--<h3 class="cancel-prize"> <strike><i class="fa fa-inr" aria-hidden="true"></i>35,000</strike> </h3>-->
              <h1 class="prize white-text"><i class="fa fa-inr" aria-hidden="true"></i> <?php echo number_format($GLOBALS['iot_courses']['courses'][$index]["sp_price_inr"]) ?><span class="taxes-text white-text">+ taxes </span> </h1>
            </div>
            <div class="pull-left xs-banner-btn-margin">
              <div class="text-center-xs actionbtn skew-btn sign-custom-btn">
                <!-- <a href="#" data-toggle="modal" data-target="#leadmodal" data-course-id="<?php //echo $GLOBALS['iot_courses']['courses'][$index]['course_id']; ?>" data-course-name="<?php //echo $GLOBALS['iot_courses']['courses'][$index]['name']; ?>" ><span class="sign-up-custom uppercase">Enroll Now</span>
                </a>  -->
                <a target="_blank" <?php iot_a_part('https://www.jigsawacademy.com/checkout-iot/?c='.$GLOBALS['iot_courses']['courses'][$index]["wp_id"].'&p=course','#loginmodal','#profile-popup1'); ?> ><span>Enroll Now</span></a>
              </div>
            </div>
          </div>
        </div>
    </span>
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
    <!--<li><a href="#capstone-project-section" class="cool-link">capstone project</a></li>-->
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
          <div class="col-lg-9 col-md-9 col-sm-9 padding-left-none">
            <nav class="navbar navbar-default navbar-custom">
              <ul class="nav navbar-nav pull-left">
                <li><a href="#about-section" class="cool-link">ABOUT THIS COURSE</a></li>
                <li><a href="#key-feature-section" class="cool-link">key features</a></li>
                <li><a href="#curriculum-section" class="cool-link">curriculum</a></li>
        <!--<li><a href="#capstone-project-section" class="cool-link">capstone project</a></li>-->
                <li><a href="#you-get-section" class="cool-link">What you Get</a></li>
              </ul>
            </nav>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-3">
            <!--<div class="pull-left"><a href="#" class="uppercase signup-btn skew-btn innerpage-btn margin-top-8 a-hover">Enroll Now</a></div>-->
      <div class="text-center-xs actionbtn skew-btn  margin-top-8 a-hover"> <a href="#"><span class="fixedtop-tab-nav-btn uppercase">Notify me when available</span></a> </div>
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
      <p>This course deals with the rapidly evolving field of cloud computing.  We will examine cloud offerings from all the leading providers, including Amazon, Google and Microsoft, and learn to design and deploy solutions to them. As with previous modules, we will continue to emphasize hands-on learning, but this course will also have several case studies, where we examine real-world IoT applications such as wearables, home automation and smart cities.</p>
      <br>
      <br>
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
      <li><p class="font16"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/prerequisites.png"> &nbsp; <span class="capital f-color-blue">prerequisites : </span> Basic programming skills</p></li>
      <li class="hidden-xs">|</li>
      <li><p class="font16"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Monitor.png"> &nbsp; <span class="capital f-color-blue">platform : </span> Node.js, Docker, AWS, Heroku</p></li>
      <li class="hidden-xs">|</li>
      <li><p class="font16"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Calendar.png"> &nbsp; <span class="capital f-color-blue">duration : </span> 4 hours</p></li>
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
        <!--<div class="row">
          <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
            <div class="dark-blue-bg sp-key-circle"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/key7.png"></div>
          </div>
          <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11 paddinglft25">
            <p>Suitable for both hardware and software professional</p>
          </div>
        </div>-->
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
                    <h2>Deploying to the Cloud</h2>

                 <div id="accordion">
                      <div class="target-timeline">
                        <div class="timeline-mini-icon">
                          <h4>1</h4>
                        </div>
                        <h3 class="f-color-blue">Express.js on Raspberry Pi</h3>
                        <p>We learn to run a restful service on our Raspberry Pi using Express.js - a Javascript-based framework for building web applications. Our webserver will receive data from local IoT device such as a nodemcu which provides a dashboard for examining the data.</p>
                      </div>

                     <div class="timeline-mini-icon bg-yellow">
                      <h4>2</h4>
                    </div>
                    <h3 class="f-color-blue">Heroku Quickstart</h3>
                    <p>We now take our Express.js server and run it on the cloud using a "platform-as-a-service" provider. This lets us examine the power and limitations of the PaaS approach.</p>


                    <!-- <div class="target-timeline">
                         <div class="timeline-mini-icon bg-yellow">
                          <h4>2</h4>
                         </div>
                        <h3 class="f-color-blue">IoT Gateway Architecture</h3>
                        <p>This video explains how the previous demo works and how it is different from when we were running `node-red` on the Raspberry Pi.</p>
                    </div> -->


              <div class="more-content">
                    <div class="timeline-mini-icon">
                    <h4>3</h4>
                    </div>
                    <h3 class="f-color-blue">SaaS, PaaS and IaaS</h3>
                    <p>A more in-depth examination of cloud services and the buckets under which they are classified.</p>
                 <!-- <div class="target-timeline">
                    <div class="timeline-mini-icon">
                      <h4>3</h4>
                     </div>
                    <h3 class="f-color-blue">Intro to Javascript (Part 1)</h3>
                    <p>Since we'll be doing a lot of coding using Javascript, this video provides a quick introduction to the language. We'll look at why it is our language of choice for coding back-end logic and we'll cover the basics.</p>
                 </div> -->

                 <!-- <div class="target-timeline">
                    <div class="timeline-mini-icon bg-yellow">
                      <h4>4</h4>
                     </div>
                    <h3 class="f-color-blue">Intro to Javascript (Part 2)</h3>
                    <p>This video covers more advanced aspects of Javascript. It looks at how its usage differs when used at the front-end and back-end.</p>
                 </div> -->

                </div>
              <p> <a class="readmore" href="#">Show More (+)</a></p>
               </div>
              </div>
            </div>
        </article>


        <!--  <article class="timeline-entry">
            <div class="timeline-entry-inner"> -->
                <!-- <div class="timeline-icon text-center">
                    <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/timeline-2.png" class="img-responsive center-block padding-top-15">
                </div> -->

                <!-- <div class="timeline-label">
                    <h2>Platform-as-a-Service</h2>

                    <div class="timeline-mini-icon">
                      <h4>1</h4>
                    </div>
                    <h3 class="f-color-blue">Heroku Quickstart</h3>
                    <p>We now take our ExpressJS server and run it on the cloud using a "platform-as-a-service" provider. This lets us examine the power and limitations of the PaaS approach.</p>

                    <div class="timeline-mini-icon bg-yellow">
                    <h4>2</h4>
                    </div>
                    <h3 class="f-color-blue">SaaS, PaaS and IaaS</h3>
                    <p>A more in-depth examination of cloud services and the buckets under which they are classified.</p>


                </div> -->
              <!--  </div>

        </article> -->
      <article class="timeline-entry">
            <div class="timeline-entry-inner">
                <div class="timeline-icon text-center">
                    <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/timeline-3.png" class="img-responsive center-block padding-top-15">
                </div>

                <div class="timeline-label">
                    <h2>Infrastructure-as-a-Service</h2>

                    <div class="timeline-mini-icon">
                      <h4>1</h4>
                    </div>

                    <h3 class="f-color-blue">AWS Quickstart</h3>
                    <p>We move to IaaS with Amazon's cloud offering - AWS. This means creating your own server instance in the cloud, choosing and configuring its OS, and running your service - the Express.js server - on top of it.</p>

                     <div class="timeline-mini-icon bg-yellow">
                      <h4>2</h4>
                     </div>
                    <h3 class="f-color-blue">IaaS Comparison</h3>
                    <p>The number and variety of cloud services providers has grown in the last few years. This video provides an overview of all the main players in the IaaS space. </p>

                    <div class="more-content">
                    <div class="timeline-mini-icon">
                    <h4>3</h4>
                    </div>
                    <h3 class="f-color-blue">Case Study on Home Automation</h3>
                    <p>In this first of our case studies, we examine what an ideal home automation system will look like and how it will be organized. We'll talk about the nodes, the gateway and the cloud services that such a system would need.
          </p>
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
                    <h2>Microservices using Docker</h2>

                    <div class="timeline-mini-icon">
                      <h4>1</h4>
                    </div>

                    <h3 class="f-color-blue">Running your own Services</h3>
                   <!--  <p>MQTT, MongoDB: A modern backend requires more than just a webserver. In this video, we expand our backend to include a database and a message queue both on our PaaS and IaaS platforms.</p>  -->
                   <p><b>MQTT, Redis:</b> A modern backend requires more than just a webserver. In this video, we expand our backend to include a database and a message queue on our IaaS platform.</p>

                     <div class="timeline-mini-icon bg-yellow">
                      <h4>2</h4>
                     </div>
                    <h3 class="f-color-blue">Docker Intro</h3>
                    <p>A quick start to using docker - a virtual container for services targeting distributed systems.</p>

                    <div class="more-content">
                        <div class="timeline-mini-icon">
                        <h4>3</h4>
                        </div>
                        <h3 class="f-color-blue">Virtualization</h3>
                        <p>Hypervision vs. Containerization: A deeper look at virtualization - why we need it, how it is done, and the reasons it has become so important.</p>
                    	<!-- <div class="timeline-mini-icon bg-yellow">
                        <h4>4</h4>
                      </div>
                        <h3 class="f-color-blue">Docker Micro-Services</h3>
            <p>We look at the overall architecture of our backend, assuming we use docker to organize and deploy it.</p>
            <div class="timeline-mini-icon">
                        <h4>5</h4>
                        </div>
                        <h3 class="f-color-blue">Authentication and Authorization</h3>
            <p>Our backend is exposed to the big bad internet. This video looks at all the steps we need to take to secure it.</p>
            <div class="timeline-mini-icon bg-yellow">
                        <h4>6</h4>
                        </div>
                        <h3 class="f-color-blue">Docker Swarm and Compose</h3>
            <p>An introduction to advanced docker topics, this video looks at how it is used in practice.</p> -->
          </div>
                       <!--  <p><a class="readmore" href="#">Show More (+)</a></p> -->
                   </div>
                </div>

        </article>

        <article class="timeline-entry">
            <div class="timeline-entry-inner">
                <div class="timeline-icon text-center">
                    <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/timeline-5.png" class="img-responsive center-block padding-top-15">
                </div>

                <div class="timeline-label">
                    <h2>Connectivity beyond Wi-Fi</h2>

                    <div class="timeline-mini-icon">
                      <h4>1</h4>
                    </div>

                    <h3 class="f-color-blue">LoRa</h3>
                    <p>So far, we have looked at Wi-Fi as the sole connectivity option. We now expand our gaze to include cellular connectivity and LoRa.</p>

                     <div class="timeline-mini-icon bg-yellow">
                      <h4>2</h4>
                     </div>
                    <h3 class="f-color-blue">Case Study on Smart City</h3>
                    <p>IoT technology is relevant for more than just consumer goods. This video looks at how we might go about building smart city solutions.</p>

                    <div class="more-content">
                        <div class="timeline-mini-icon">
                        <h4>3</h4>
                        </div>
                        <h3 class="f-color-blue">BT and NFC</h3>
                        <p>Connectivity at ultra-short ranges can be just as important as those at longer distance when it comes to certain IoT applications. We look at bluetooth, NFC and other radio frequency alternatives.</p>

                        <div class="timeline-mini-icon bg-yellow">
                        <h4>4</h4>
                        </div>
                        <h3 class="f-color-blue">Case Study on Brick and Mortar Retail</h3>
                        <p>This case study imagines a large retail grocery chain and builds a system of how they might benefit from IoT technology.</p>
                      </div>
                         <p><a class="readmore" href="#">Show More (+)</a></p>
                </div>
                </div>

        </article>

         <article class="timeline-entry">
            <div class="timeline-entry-inner">
                <div class="timeline-icon text-center">
                    <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/timeline-5.png" class="img-responsive center-block padding-top-15">
                </div>

                <div class="timeline-label">
                    <h2>Final Project</h2>

                  <!--   <div class="timeline-mini-icon">
                      <h4>1</h4>
                    </div> -->

                   <!--  <h3 class="f-color-blue">LoRa</h3> -->
                    <p>Our final project builds a complete IoT cloud application using everything we have learnt so for.  Sensor data from an IoT device is logged into a time series database.  We then build a dashboard to visualize this data.  We also build a rules engine to monitor this data and take specific automatic actions when certain preset conditions are triggered.</p>

                    <!--  <div class="timeline-mini-icon bg-yellow">
                      <h4>2</h4>
                     </div>
                    <h3 class="f-color-blue">Case Study on Smart City</h3>
                    <p>IoT technology is relevant for more than just consumer goods. This video looks at how we might go about building smart city solutions.</p>
                     -->
                    <!-- <div class="more-content">
                        <div class="timeline-mini-icon">
                        <h4>3</h4>
                        </div>
                        <h3 class="f-color-blue">BT and NFC</h3>
                        <p>Connectivity at ultra-short ranges can be just as important as those at longer distance when it comes to certain IoT applications. We look at bluetooth, NFC and other radio frequency alternatives.</p>

                        <div class="timeline-mini-icon bg-yellow">
                        <h4>4</h4>
                        </div>
                        <h3 class="f-color-blue">Case Study on Brick and Mortar Retail</h3>
                        <p>This case study imagines a large retail grocery chain and builds a system of how they might benefit from IoT technology.</p>
                      </div> -->
                         <!-- <p><a class="readmore" href="#">Show More (+)</a></p> -->
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

<!-- second tabing start -->
<div  id="you-get-section">
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
                <div class="row">
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
          <div class="conatin-margin-top"></div>
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
                <p>He is co-founder of Klar Systems, an emerging IoT start-up.He carries extensive experience in embedded systems, connectivity and application development.</p>
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