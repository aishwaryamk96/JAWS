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

	$index = 1;

?>

  <!-- HTML HEAD -->
  <?php
  $GLOBALS["content"]["title"] = "IoT Training using Arduino";
  $GLOBALS["content"]["meta_description"] = "Learn about basic electronics, microcontroller architectures, sensors, human-machine interfaces (HMI) and basic networking using Arduino. Create IoT projects driven by the Arduino and connect them to cloud with mobile applications.";
	load_template("iot", "v2/head");
  ?>
  <!-- HTML HEAD ENDS -->

  <!-- HEADER MENU -->
  <?php load_template("iot", "v2/header"); ?>
  <!-- HEADER MENU ENDS -->

<!-- Banner Start -->
<div class="arduino-banner" id="side_nav_item_id_0">
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
					<a class="last" href="https://www.jigsawacademy.com/iot/iot-using-arduino" itemprop="url">
						<span itemprop="title">IoT using Arduino</span>
					</a>
				</span>
			</li>
		</ol>
	</section>
  <div class="wrapper header-padding-top header-padding-bottom">
    <div class="row">
      <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
        <h1 class="innerpage-banner-title white-text arduino-title-width">IoT from the Ground Up Using Arduino</h1>
		<div class="rating" itemscope="itemscope" itemtype="http://data-vocabulary.org/Review-aggregate">
			<img src="https://www.jigsawacademy.com/wp-content/uploads/2016/08/three_nine_star.png" alt="4 Star Rating: Very Good" width="79" height="15" title="4">
			<span>4 Ratings </span>
			<meta itemprop="itemreviewed" content="IoT from the Ground Up Using Arduino">
			<meta itemprop="rating" content="4">
			<meta itemprop="votes" content="20">
		</div>
        <p class="banner-contain white-text">Power IoT projects with the Arduino and connect them to the cloud.</p>
        <div class="margin-30"></div>
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="pull-left">
              <!--<h3 class="cancel-prize"> <strike><i class="fa fa-inr" aria-hidden="true"></i>35,000</strike> </h3>-->
              <h1 class="prize white-text"><i class="fa fa-inr" aria-hidden="true"></i> <?php echo number_format($GLOBALS['iot_courses']['courses'][$index]["sp_price_inr"]) ?><span class="taxes-text white-text">+ taxes </span> </h1>
            </div>
            <div class="pull-left">
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
    <li><a href="#capstone-project-section" class="cool-link">capstone project</a></li>
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
        <li><a href="#capstone-project-section" class="cool-link">capstone project</a></li>
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
      <p>This course deals with basic electronics, microcontroller architectures, sensors, human-machine interfaces (HMI) and basic networking. We use the Arduino platform to teach these concepts.</p>
      <br>
      <p>After doing this course you should be able to put together IoT projects driven by the Arduino and connect them to cloud with mobile applications. </p><br><br>
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
      <li><p class="font16"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Monitor.png"> &nbsp; <span class="capital f-color-blue">platform : </span> Arduino</p></li>
      <li class="hidden-xs">|</li>
      <li><p class="font16"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Calendar.png"> &nbsp; <span class="capital f-color-blue">duration : </span> 6 hours 30 minutes</p></li>
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
                    <h2>Getting Started with Arduino</h2>

                 <div id="accordion">
                      <div class="target-timeline">
                        <div class="timeline-mini-icon">
                          <h4>1</h4>
                        </div>
                        <h3 class="f-color-blue">Introduction to IoT</h3>
                        <p>We get you up and running with the Arduino platform. You will install the tools, write and upload your first piece of code to run on a microcontroller.</p>
                      </div>

                    <div class="target-timeline">
                         <div class="timeline-mini-icon bg-yellow">
                          <h4>2</h4>
                         </div>
                        <h3 class="f-color-blue">Arduino Code Language (C Refresher)</h3>
                        <p>In case your C code skills are rusty, this video provides a quick refresher. We also look briefly at the differences between plain C and the Arduino Code Language.</p>
                    </div>


              <div class="more-content">

                 <div class="target-timeline">
                    <div class="timeline-mini-icon">
                      <h4>3</h4>
                     </div>
                    <h3 class="f-color-blue">Inputs and App Control</h3>
                    <p>We build a slightly more complex program for a microcontroller. This one feature user inputs via button and a way to control the operation of the microcontroller from an app running on your smartphone.</p>
                 </div>

                 <div class="target-timeline">
                    <div class="timeline-mini-icon bg-yellow">
                      <h4>4</h4>
                     </div>
                    <h3 class="f-color-blue">Arduino Simulation Environment</h3>
                    <p>Is there a way to run microcontroller code without having one at your disposal? In this video, we explore simulation technologies that allow you to build rapid prototypes, giving you greater insight into the working of your code.</p>
                 </div>

                </div>
              <p> <a class="readmore" href="#">Show More (+)</a></p>
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
                    <h2>Sensors, Signals & Electronics</h2>

                    <div class="timeline-mini-icon">
                      <h4>1</h4>
                    </div>
                    <h3 class="f-color-blue">Analog Sensors</h3>
                    <p>We begin our exploration of sensors by learning how to read analog signals from them in our microcontroller code. We also look briefly at some issues while using analog sensors.</p>

                    <div class="timeline-mini-icon bg-yellow">
                    <h4>2</h4>
                    </div>
                    <h3 class="f-color-blue">Basic electronics refresher</h3>
                    <p>A quick overview of all the components you will need as you build your Arduino projects, their usage and functions.</p>

                  <div class="more-content">
                    <div class="timeline-mini-icon">
                    <h4>3</h4>
                    </div>
                    <h3 class="f-color-blue">Digital Sensors</h3>
                    <p>Learn to read ambient temperature and humidity from an integrated sensor by talking to it using digital signals.</p>
                    <div class="timeline-mini-icon bg-yellow">
                    <h4>4</h4>
                    </div>
                    <h3 class="f-color-blue">Digital Signals</h3>
                    <p>We delve into the representation of digital signals on the wire and how signals are represented and transmitted between sensors and controller.</p>
                    <div class="timeline-mini-icon">
                    <h4>5</h4>
                    </div>
                    <h3 class="f-color-blue">Sensors overview</h3>
                    <p>We covered a few sensors in detail in the past videos, but there a wide variety of sensors out there that measure all manner of real world phenomena. This video provides a broad overview of commonly available sensors and sensor modules that play well with the Arduino hardware.</p>
                  </div>
                     <p><a class="readmore" href="#">Show More (+)</a></p>
                </div>
               </div>

        </article>
      <article class="timeline-entry">
            <div class="timeline-entry-inner">
                <div class="timeline-icon text-center">
                    <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/timeline-3.png" class="img-responsive center-block padding-top-15">
                </div>

                <div class="timeline-label">
                    <h2>Microcontroller Concepts</h2>

                    <div class="timeline-mini-icon">
                      <h4>1</h4>
                    </div>

                    <h3 class="f-color-blue">User Interface</h3>
                    <p>Buttons and displays: IoT devices often require a "human-machine interface" and this is often hard to build due to microcontroller limitations. In this video, we look at buttons and display modules and explain how these are controlled from microcontroller code.</p>

                     <div class="timeline-mini-icon bg-yellow">
                      <h4>2</h4>
                     </div>
                    <h3 class="f-color-blue">Microcontroller Concepts - Memory Map, IO Peripherals</h3>
                    <p>We have been doing a lot of microcontroller coding. Now it is time to look under the hood and understand the internal architectures of these machines. We also look at how code and data are organized.</p>

                    <div class="more-content">
                    <div class="timeline-mini-icon">
                    <h4>3</h4>
                    </div>
                    <h3 class="f-color-blue">Arduino Hardware Overview</h3>
                    <p>We have been using one microcontroller platform - the Arduino Nano q- but there are several others out there. Here, we take a walk through the world of popular Arduino hardware - both official and community-supported.</p>
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
                    <h2>Basic Networking with nodemcu</h2>

                    <div class="timeline-mini-icon">
                      <h4>1</h4>
                    </div>

                    <h3 class="f-color-blue">Intro to nodemcu</h3>
                    <p>This is the first look at the second of our two microcontroller platforms - the nodemcu. This is a true IoT enabled microcontroller, since it has built in Wi-FI. We jump right in and write our very first program for it and control an LED from the cloud!</p>

                     <div class="timeline-mini-icon bg-yellow">
                      <h4>2</h4>
                     </div>
                    <h3 class="f-color-blue">Basic Wireless Networking</h3>
                    <p>Time to take a step back and look deeper into how Wi-Fi works. This video teaches you the basics of networking, Wi-Fi architecture and security.</p>

                    <div class="more-content">
                        <div class="timeline-mini-icon">
                        <h4>3</h4>
                        </div>
                        <h3 class="f-color-blue">Sensor Log</h3>
                        <p>Here are more advanced exercise using the nodemcu. We connect a few sensors to it and build a sort of mini weather station.</p>

                        <div class="timeline-mini-icon bg-yellow">
                        <h4>4</h4>
                        </div>
                        <h3 class="f-color-blue">HTTP and REST API</h3>
                        <p>Networks allow us to transport data from one point to another. But how is one device controlled by another? This video looks at the client-server architecture and in particularly at HTTP and RESTful interfaces.</p>
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
                    <h2>Advanced Microcontroller Concepts</h2>

                    <div class="timeline-mini-icon">
                      <h4>1</h4>
                    </div>

                    <h3 class="f-color-blue">LED blink using MQTT</h3>
                    <p>In this video, we introduce MQTT, a lightweight yet powerful protocol for messaging. We use it to build an example, which, while similar in functionality to an example we built before, is transparent in terms of its underlying architecture.</p>

                     <div class="timeline-mini-icon bg-yellow">
                      <h4>2</h4>
                     </div>
                    <h3 class="f-color-blue">Messaging and MQTT</h3>
                    <p>Here, we take a deeper look at messaging and the "pub-sub" pattern. We discuss how it is different from REST and why it is often preferred in the case of IoT devices.</p>

                    <div class="more-content">
                        <div class="timeline-mini-icon">
                        <h4>3</h4>
                        </div>
                        <h3 class="f-color-blue">Microcontrollers Concepts - Interrupts, Timers, Callbacks, Re-entrancy, Watchdogs</h3>
                        <p>In this video on microcontroller internals, we look at common design patterns used in building firmware. We also go over basic concepts like re-entrancy, mutexes and error handling. We also look at performance aspects - latencies, throughput and other parameters.</p>

                        <div class="timeline-mini-icon bg-yellow">
                        <h4>4</h4>
                        </div>
                        <h3 class="f-color-blue">Device Configuration, Discovery and OTA</h3>
                        <p>Time to take a leap from building a demo to building a product. What should you worry about as you put your IoT device into the end-user’s hand? How is the product deployed, discovered, secured and maintained?</p>
                      </div>
                         <p><a class="readmore" href="#">Show More (+)</a></p>
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
<div class="wrapper section-padding-top section-padding-bottom" id="capstone-project-section">
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