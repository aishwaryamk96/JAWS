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

	$index = 2;

?>

  <!-- HTML HEAD -->
  <?php
	$GLOBALS["content"]["title"] = "IoT Projects using Raspberry Pi | IoT Training";
	$GLOBALS["content"]["meta_description"] = "Build IoT projects using Raspberry Pi with Jigsaw Academy. Once your training gets completed you'll be able to design and deploy Raspberry Pi to multiple IoT devices that could connect to the gateway.";
	load_template("iot", "v2/head");
  ?>
  <!-- HTML HEAD ENDS -->

  <!-- HEADER MENU -->
  <?php load_template("iot", "v2/header"); ?>
  <!-- HEADER MENU ENDS -->

<!-- Banner Start -->
<div class="raspberry-pi-banner" id="side_nav_item_id_0">
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
					<a class="last" href="https://www.jigsawacademy.com/iot/iot-using-raspberry-pi" itemprop="url">
						<span itemprop="title">IoT using Raspberry Pi</span>
					</a>
				</span>
			</li>
		</ol>
	</section>
  <div class="wrapper header-padding-top header-padding-bottom">
    <div class="row">
      <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
        <h1 class="innerpage-banner-title white-text ">Powering IoT <br class="hidden-xs"> Using the Raspberry Pi</h1>
		<div class="rating" itemscope="itemscope" itemtype="http://data-vocabulary.org/Review-aggregate">
			<img src="https://www.jigsawacademy.com/wp-content/uploads/2016/08/four_five_star.png" alt="4.2 Star Rating: Very Good" width="79" height="15" title="4.2">
			<span>4.2 Ratings </span>
			<meta itemprop="itemreviewed" content="Powering IoT Using the Raspberry Pi">
			<meta itemprop="rating" content="4.2">
			<meta itemprop="votes" content="21">
		</div>
        <p class="banner-contain white-text">Develop complex IoT applications with a high-level operating system.</p>
        <div class="margin-30"></div>
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="pull-left">
              <!--<h3 class="cancel-prize"> <strike><i class="fa fa-inr" aria-hidden="true"></i>35,000</strike> </h3>-->
              <span class="prize white-text" style="font-family: 'Montserrat', sans-serif;"><i class="fa fa-inr" aria-hidden="true"></i> <?php echo number_format($GLOBALS['iot_courses']['courses'][$index]["sp_price_inr"]) ?><span class="taxes-text white-text">+ taxes </span> </span>
            </div>
            <div class="pull-left xs-banner-btn-margin">
              <div class="text-center-xs actionbtn skew-btn sign-custom-btn">
                   <a target="_blank" <?php iot_a_part('https://www.jigsawacademy.com/checkout-iot/?c='.$GLOBALS['iot_courses']['courses'][2]["wp_id"].'&p=course','#loginmodal','#profile-popup1'); ?> ><span>Enroll Now</span>
                  </a>

                  <!-- <a href="#" data-toggle="modal" data-target="#leadmodal" data-course-id="<?php //echo $GLOBALS['iot_courses']['courses'][$index]['course_id']; ?>" data-course-name="<?php //echo $GLOBALS['iot_courses']['courses'][$index]['name']; ?>" ><span class="sign-up-custom uppercase">Notify Me</span>
                  </a> -->
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
          <div class="col-lg-9 col-md-9 col-sm-9 padding-left-none">
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
          <div class="col-lg-3 col-md-3 col-sm-3">
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
      <p>With the widespread use of wireless connectivity among mobile devices, IoT has become the talk of the town. But did you know that Raspberry Pi is a popular choice when it comes to developing IoT products or projects? It is a great platform to get into the world of the IoT and learn about the Internet of Things (IoT) deeply.</p>
      <br>
      <p>If you are one of those who love to automate things, drive data, and develop Internet of Things projects, you are indeed going to enjoy using Raspberry Pi. Want to get your hands on IoT projects using Raspberry Pi? Enroll for IoT Training today itself.</p>
      <br/>
      <p>The course focuses on higher-level operating systems, advanced networking, user interfaces, multimedia and uses more computing intensive IoT applications as examples. We use the Raspberry Pi running Linux as the platform of 
      choice, while also exposing the student to other comparable platforms.</p>
      <br/>
      <p>After doing this course one should be able to design and deploy multiple IoT devices that could connect to the gateway. A gateway is a full featured device where one can discover more features and new possibilities to expand their toolset. Though this approach is more complex, practitioners can derive significant performance gain compared to solutions reliant only on microcontrollers.</p>
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
      <li><p class="font16"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/prerequisites.png"> &nbsp; <span class="capital f-color-blue">prerequisites : </span> Basic programming skills </p></li>
      <li class="hidden-xs">|</li>
      <li><p class="font16"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Monitor.png"> &nbsp; <span class="capital f-color-blue">platform : </span> Raspberry Pi, OS: Raspbian</p></li>
      <li class="hidden-xs">|</li>
      <li><p class="font16"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Calendar.png"> &nbsp; <span class="capital f-color-blue">duration : </span> 7 hours 20 minutes</p></li>
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
                    <h2>Getting Started with Raspberry Pi</h2>

                 <div id="accordion">
                      <div class="target-timeline">
                        <div class="timeline-mini-icon">
                          <h4>1</h4>
                        </div>
                        <h3 class="f-color-blue">Introducing the Raspberry Pi</h3>
                        <p>Get up and running with the Raspberry Pi 3. Learn to make the connections, power it up and take a tour of its desktop environment.</p>
                      </div>

                    <div class="target-timeline">
                         <div class="timeline-mini-icon bg-yellow">
                          <h4>2</h4>
                         </div>
                        <h3 class="f-color-blue">Course Objectives and Course Map</h3>
                        <p>Understand the overall plan for this module. We provide a quick overview of what we’ll learn and of the major projects we’ll be doing.</p>
                    </div>


              <div class="more-content">

                 <div class="target-timeline">
                    <div class="timeline-mini-icon">
                      <h4>3</h4>
                     </div>
                    <h3 class="f-color-blue">Booting the Raspberry Pi 3</h3>
                    <p>A more fundamental look at booting the Raspberry Pi 3. Understand how to download an operating system, format an SD card and boot the OS. Also, learn the rudiments of the file system.</p>
                 </div>

                 <div class="target-timeline">
                    <div class="timeline-mini-icon bg-yellow">
                      <h4>4</h4>
                     </div>
                    <h3 class="f-color-blue">OS Options Overview</h3>
                    <p>While we’ll use the Raspbian image as the reference for the rest of this course, we spend some time in this video looking at what other OS options are available for the Raspberry Pi, and why you might want to check them out.</p>
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
                    <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/timeline-2.png" alt="Interfacing Hardware with the Raspberry Pi" class="img-responsive center-block padding-top-15">
                </div>

                <div class="timeline-label">
                    <h2>Interfacing Hardware with the Raspberry Pi</h2>

                    <div class="timeline-mini-icon">
                      <h4>1</h4>
                    </div>
                    <h3 class="f-color-blue">Raspberry Pi Remote Access</h3>
                    <p>Learn to operate the Raspberry Pi in “headless mode” by logging into it remotely. We’ll also discuss how to move ata (or files) from your PC to the Raspberry Pi and back.</p>

                    <div class="timeline-mini-icon bg-yellow">
                    <h4>2</h4>
                    </div>
                    <h3 class="f-color-blue">Bash Command-line</h3>
                    <p>A short primer on Linux terminal commands, or more specifically, a quick tour through the basic command set for the bash shell. Get comfortable operating you Raspberry Pi without needing a GUI interface.</p>

                  <div class="more-content">
                    <div class="timeline-mini-icon">
                    <h4>3</h4>
                    </div>
                    <h3 class="f-color-blue">Raspberry Pi LED Blink Example</h3>
                    <p>Time to bring out the electronic components. The Raspberry Pi's connector allows you extend its functionality using external hardware and in this video, we begin to explore how this is done.</p>
                    <div class="timeline-mini-icon bg-yellow">
                    <h4>4</h4>
                    </div>
                    <h3 class="f-color-blue">Linux Overview and Device Drivers</h3>
                    <p>High-level operating systems, such as Linux, require the use of a device driver for user programs to access hardware. We look at why this is, what a device driver does, and how to build one.</p>

                  </div>
                     <p><a class="readmore" href="#">Show More (+)</a></p>
                </div>
               </div>

        </article>
      <article class="timeline-entry">
            <div class="timeline-entry-inner">
                <div class="timeline-icon text-center">
                    <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/timeline-3.png" alt="Machine-to-Machine Communication with IoT" class="img-responsive center-block padding-top-15">
                </div>

                <div class="timeline-label">
                    <h2>Machine-to-Machine Communication</h2>

                    <div class="timeline-mini-icon">
                      <h4>1</h4>
                    </div>

                    <h3 class="f-color-blue">Sensors Integration</h3>
                    <p>We continue the journey we started in video 7 and interface yet more hardware to the Raspberry Pi. We learn how to deal with inputs and digital protocols from within user programs.</p>

                     <div class="timeline-mini-icon bg-yellow">
                      <h4>2</h4>
                     </div>
                    <h3 class="f-color-blue">Local Blynk Server</h3>
                    <p>Blynk provides a simple means to tie together smartphones and IoT gadgets. In this video, we control a NodeMCU from a smartphone via a Blynk server running on Raspberry Pi.</p>

                    <div class="more-content">
                    <div class="timeline-mini-icon">
                    <h4>3</h4>
                    </div>
                    <h3 class="f-color-blue">Node-red: M2M and Gateway</h3>
                    <p>We expand on the previous example and use it as a basis to discuss machine-to-machine communication, an IoT node vs gateway and related concepts.
          </p>
                  </div>
                     <p><a class="readmore" href="#">Show More (+)</a></p>
                </div>
            </div>

        </article>

       <article class="timeline-entry">
            <div class="timeline-entry-inner">
                <div class="timeline-icon text-center">
                    <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/timeline-4.png" alt="IoT Projects using Raspberry Pi" class="img-responsive center-block padding-top-15">
                </div>

                <div class="timeline-label">
                    <h2>Multimedia Concepts</h2>

                    <div class="timeline-mini-icon">
                      <h4>1</h4>
                    </div>

                    <h3 class="f-color-blue">Raspberry Pi Media Server</h3>
                    <p>This video introduces a high-performance application on the Raspberry Pi - a complete content streaming server.</p>

                     <div class="timeline-mini-icon bg-yellow">
                      <h4>2</h4>
                     </div>
                    <h3 class="f-color-blue">Running Services</h3>
                    <p>Learn about the Linux boot up sequence, run levels, and how to run programs automatically on boot up.</p>

                    <div class="more-content">
                        <div class="timeline-mini-icon">
                        <h4>3</h4>
                        </div>
                        <h3 class="f-color-blue">Media Server System Architecture</h3>
                        <p>A more detailed look at the last example that maps out the control and data paths. Learn to think about performance in terms of throughput and latency.</p>
                    </div>
                        <p><a class="readmore" href="#">Show More (+)</a></p>
                   </div>
                </div>

        </article>

        <article class="timeline-entry">
            <div class="timeline-entry-inner">
                <div class="timeline-icon text-center">
                    <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/timeline-5.png" alt="Speech Processing Concepts" class="img-responsive center-block padding-top-15">
                </div>

                <div class="timeline-label">
                    <h2>Speech Processing Concepts</h2>

                    <div class="timeline-mini-icon">
                      <h4>1</h4>
                    </div>

                    <h3 class="f-color-blue">Voice Control Application</h3>
                    <p>Explore speech processing on Raspberry Pi. Speech recognition is an important emerging interface for IoT devices and this example introduces the topic.</p>

                     <div class="timeline-mini-icon bg-yellow">
                      <h4>2</h4>
                     </div>
                    <h3 class="f-color-blue">Voice Control System Architecture</h3>
                    <p>A more detailed look at how the last example was put together. Once again, we look at performance, introduce metrics, and compare with video processing.</p>

                    <div class="more-content">
                        <div class="timeline-mini-icon">
                        <h4>3</h4>
                        </div>
                        <h3 class="f-color-blue">Speech Processing and NLP Concepts</h3>
                        <p>Understand how speech processing and natural language interfaces work.</p>

                        <div class="timeline-mini-icon bg-yellow">
                        <h4>4</h4>
                        </div>
                        <h3 class="f-color-blue">Raspberry Security Cam</h3>
                        <p>We've covered video playback and speech processing. Now, its time to learn video processing. In this video, we set up our example and demonstrate how it works.</p>
                      </div>
                         <p><a class="readmore" href="#">Show More (+)</a></p>
                </div>
                </div>

        </article>

    <article class="timeline-entry">
            <div class="timeline-entry-inner">
                <div class="timeline-icon text-center">
                    <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/timeline-6.png" alt="Image Processing Concepts" class="img-responsive center-block padding-top-15">
                </div>

                <div class="timeline-label">
                    <h2>Image Processing Concepts</h2>

                    <div class="timeline-mini-icon">
                      <h4>1</h4>
                    </div>

                    <h3 class="f-color-blue">Gstreamer (Alternate video framework)</h3>
                    <p>Once again, we look under-the-hood at how the demo is put together, the frameworks used and how each is configured.</p>

                     <div class="timeline-mini-icon bg-yellow">
                      <h4>2</h4>
                     </div>
                    <h3 class="f-color-blue">Image Processing Concepts</h3>
                    <p>Image processing can be very demanding on a processor, but it is increasingly becoming easier, even on platform like the Raspberry Pi. This video provides a quick run-down of the major ideas in this field.</p>

                    <div class="more-content">
                        <div class="timeline-mini-icon">
                        <h4>3</h4>
                        </div>
                        <h3 class="f-color-blue">Face Detection Demo</h3>
                        <p>In this video, we introduce machine learning in the context of image recognition, more specifically, face recognition.</p>
                        <div class="timeline-mini-icon bg-yellow">
                        <h4>4</h4>
                        </div>
                        <h3 class="f-color-blue">Image Recognition Concepts</h3>
                        <p>A quick overview of the algorithms used in the last demo and how they work.</p>
            <div class="timeline-mini-icon">
                        <h4>5</h4>
                        </div>
                        <h3 class="f-color-blue">Profiling, Performance, Optimization</h3>
                        <p>We revisit many of the topics we covered before and view it through the prism of performance. As we seek to use available hardware to the fullest, it is important to understand where we're spending our platform's resources, find out about those we're running out of and learn to optimize the system.</p>
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
      <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/capstone-project-icon.png" alt="Capstone Project using Raspberry Pi" class="center-block padding-bottom-10">
      <p>We build a "Smart Mirror" using concepts of multi-media, video capture and speech processing. This is a repurposed computer monitor that can detect your presence, respond to your voice commands and overlay you a host of relevant information besides acting like a regular mirror.</p>
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
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2"> <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/spe-IOT-Hardware-Kit.png" alt="IoT Hardware Kit"> </div>
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
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2"> <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Catalyst.png" alt="IoT Pre-Recorded Lectures"> </div>
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
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2"> <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/spe-Jigsaw-Learning-Center.png" alt="Jigsaw Academy Learning Center"> </div>
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
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2"> <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/spe-Faculty-Support.png" alt="IoT Faculty Support"> </div>
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
        <img class="img-circle img-responsive center-block" src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/expert-harikrishna.jpg" alt="Jigsaw Academy Linkedin Icon">
                <h3 class="f-color-blue expert-title">Harikrishna R.</h3>
                <p>He is a co-founder and director of Klar Systems, where he designs and builds cool new gadgets, applications and platforms for the IoT era.</p>
        <a href="https://www.linkedin.com/in/harikrishnar" target="_blank"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/linkedin_icon.jpg" class="img-responsive center-block padding-top-7" alt="Jigsaw Academy Linkedin Icon"></a>
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