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
	
	setlocale(LC_MONETARY, 'en_IN');
    
	?>

    <!-- HTML HEAD -->
    <?php
    $GLOBALS["content"]["title"] = "Online IoT Courses, Training & Certification";
	$GLOBALS["content"]["meta_description"] = "Get best online IoT certification courses & training in India to become IoT analyst and expert. Jigsaw academy provides Internet of Things training at competitive prices.";
    load_template("iot", "v2/head");
    ?>
    <!-- HTML HEAD ENDS -->

    <!-- HEADER MENU -->
    <?php load_template("iot", "v2/header"); ?>
    <!-- HEADER MENU ENDS -->
    <style>
    	.tabcontent a{ text-decoration: none; }
    </style>
    <!-- different option start -->
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
					<a href="https://www.jigsawacademy.com/iot/iot-courses" itemprop="url" class="blue-text last">
						<span itemprop="title">Online IoT Courses, Training and Certification</span>
					</a>
				</span>
			</li>
		</ol>
	</section>
    <div class="wrapper section-padding-top section-padding-bottom">
		<div class="row  intro-section">
			<h1 class="center">Best IoT Online Courses &amp; Training in India</h1>
			<p class="sign-up-top">Get the best IoT training in India from Jigsaw Academy and become an IoT expert. Our online training includes learning pertaining to the basics of electronics, such as information on circuits, sensors, microcontrollers and knowledge of the latest programming languages such as Python, Java, C++, Javascript, Parasail and more. Our world class online training will give you in-depth insight to the promising world of IoT.</p>
			<p class="sign-up-top">Step into the world of IoT by choosing the Internet of Things courses from below:</p>
		</div>
    	<div class="row padding-top-xs-35">
    		<div class="col-lg-4 col-md-4 col-sm-4">
    			<a href="#tab-section" data-index="1" class="header-link">
    				<div class="text-center box-specialization">
    					<img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/specialization_professional.jpg" alt="Professional IoT Training" class="img-responsive center-block">
    					<div class="padding-l-r-25 padding-right-15-xs padding-left-15-xs">

    						<h3 class="lightblue padding-top-25 font-size-23">Become a <br/><span class="blue-text">Certified IoT Professional<span class="lightblue"></span></h3>
    						<p class="header-peragraph">This specialization will give you a solid understanding of how to develop and implement your own IoT solutions using Arduino and Raspberry Pi. It also covers the details of how IoT works with the cloud.</p>

    						<p class="padding-top-15 f-size-15"><strong><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/specialization-course-number.png" />No. of Courses: <?php echo count(explode(";",$GLOBALS['iot_courses']['bundles'][0]['combo'])); ?></strong></p>
    						<p class="padding-bottom-25 f-size-15"><strong><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/specialization-duration.png" />Program Duration: 4 months</strong></p>
    						<div class="text-center-xs actionbtn skew-btn spe-btn-padding uppercase xs-f-size-15">
			                            <a target="_blank" <?php iot_a_part('https://www.jigsawacademy.com/checkout-iot/?c='.$GLOBALS['iot_courses']['bundles'][0]["wp_id"].'&p=path','#loginmodal','#profile-popup1'); ?> >
			                                        <span><div style="text-decoration:line-through;display:inline-block;width:30%;">&#8377; 46,200</div> now at <?php echo money_format("%.0n",$GLOBALS['iot_courses']['bundles'][0]['price_inr']); ?></span>
			                            </a>
	                        </div>
    					</div>
    				</div>
    			</a>
    		</div>
    		<div class="col-lg-4 col-md-4 col-sm-4">
    			<a href="#tab-section" data-index="2" class="header-link">
    				<div class="text-center box-specialization">
    					<img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/specialization_analyst.jpg" alt="IoT Analyst Certification" class="img-responsive center-block">
    					<div class="padding-l-r-25 padding-right-15-xs padding-left-15-xs">

    						<h3 class="lightblue padding-top-25 font-size-23">Become a <br/><span class="blue-text">Certified IoT Analyst</span></h3>
    						<p class="header-peragraph">This specialization covers the various dimensions of dealing with data generated from IoT devices, sensors, tags and actuators. You can expand your career to include Data Science for IoT.</p>

    						<p class="padding-top-15 f-size-15"><strong><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/specialization-course-number.png" />No. of Courses: <?php echo count(explode(";",$GLOBALS['iot_courses']['bundles'][1]['combo'])); ?></strong></p>
    						<p class="padding-bottom-25 f-size-15"><strong><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/specialization-duration.png" />Program Duration: 4 months</strong></p>
    						<div class="text-center-xs actionbtn skew-btn spe-btn-padding uppercase xs-f-size-15">
    							<!-- <a href="#" data-toggle="modal" data-target="#leadmodal" data-course-id="<?php //echo $GLOBALS['iot_courses']['bundles'][1]['bundle_id']; ?>" data-course-name="<?php //echo $GLOBALS['iot_courses']['bundles'][1]['name']; ?>"><span>Notify Me</span>
    							</a> -->

    							<a target="_blank" <?php iot_a_part('https://www.jigsawacademy.com/checkout-iot/?c='.$GLOBALS['iot_courses']['bundles'][1]["wp_id"].'&p=path','#loginmodal','#profile-popup1'); ?> >
			                                        <span><div style="text-decoration:line-through;display:inline-block;width:30%;">&#8377; 49,500</div> now at <?php echo money_format("%.0n",$GLOBALS['iot_courses']['bundles'][1]['price_inr']); ?></span>
			                            	</a>
    						</div>
    					</div>
    				</div>
    			</a>
    		</div>
    		<div class="col-lg-4 col-md-4 col-sm-4">
    			<a href="#tab-section" data-index="3" class="header-link">
    				<div class="text-center box-specialization">
    					<img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/specialization_expert.jpg" alt="Advanced IoT Analytics Certification" class="img-responsive center-block">
    					<div class="padding-l-r-25 padding-right-15-xs padding-left-15-xs">

    						<h3 class="lightblue padding-top-25 font-size-23">Become a <br/><span class="blue-text">Full Stack IoT Expert</span></h3>
    						<p class="header-peragraph">This comprehensive specialization will help you build IoT devices using Arduino and Raspberry Pi platforms, understand how the IoT works with the cloud and analyze the data generated by IoT devices.</p>

    						<p class="padding-top-15 f-size-15"><strong><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/specialization-course-number.png" />No. of Courses: <?php echo count(explode(";",$GLOBALS['iot_courses']['bundles'][2]['combo'])); ?></strong></p>
    						<p class="padding-bottom-25 f-size-15"><strong><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/specialization-duration.png" />Program Duration: 6 months</strong></p>
    						<div class="text-center-xs actionbtn skew-btn spe-btn-padding uppercase xs-f-size-15">
    							<!-- <a href="#" data-toggle="modal" data-target="#leadmodal" data-course-id='<?php //echo $GLOBALS['iot_courses']['bundles'][2]['bundle_id']; ?>' data-course-name="<?php //echo $GLOBALS['iot_courses']['bundles'][2]['name']; ?>" ><span>Notify Me</span>
							</a> -->
							<a target="_blank" <?php iot_a_part('https://www.jigsawacademy.com/checkout-iot/?c='.$GLOBALS['iot_courses']['bundles'][2]["wp_id"].'&p=path','#loginmodal','#profile-popup1'); ?> >
			                                        <span><div style="text-decoration:line-through;display:inline-block;width:30%;">&#8377; 95,700</div> now at <?php echo money_format("%.0n",$GLOBALS['iot_courses']['bundles'][2]['price_inr']); ?></span>
			                            	</a>
						</div>
    					</div>
    				</div>
    			</a>
    		</div>
    	</div>

    </div>
    <!-- different option end -->
	<!-- <div class="row" style="position: absolute;left: -55em;"><h1 style="text-align: left;">Best IoT online courses & training in India</h1></div> -->

    <!-- vedio section open -->
    <div class="gray-bg">
    	<div class="wrapper section-padding-top section-padding-bottom spe-padding">
    		<div class="row">
    			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 job-for-you">
    				<span>
    					<h3 class="black-title">Is IoT the job for you?</h3>
    					<p class="padding-top-15 padding-bottom-10">IoT is an extensive field where anyone from electronics, computer science or mechanical domain can find an opportunity. As this industry grows, more software engineers, more data scientists; and more product managers will be required. The right time to be IoT-ready is now.</p>
    				</span>
    			</div>

    			<div class="col-lg-6 col-md-8 col-sm-8 col-xs-12">
    				<div class="margin-30 hidden-lg hidden-md hidden-sm"></div>
    				<iframe width="100%" height="315" src="https://www.youtube.com/embed/gj7mS_Vv9Bo" frameborder="0" allowfullscreen></iframe>
    			</div>
    		</div>
    	</div>
    </div>
    <!-- vedio section start -->

    <!-- first tabing start -->
    <div class="wrapper section-padding-top section-padding-bottom spe-padding" id="tab-section">
    	<div class="row">
    		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    			<ul class="tab spe-first-tab-custom">
    				<li><a href="javascript:void(0)" class="tablinks xs-tab-link" data-index="3">IoT PROFESSIONAL CERTIFICATION</a></li>
    				<li><a href="javascript:void(0)" class="tablinks xs-tab-link" data-index="2">IoT ANALYTICS CERTIFICATION</a></li>
    				<li><a href="javascript:void(0)" class="tablinks" data-index="1">FULL STACK IoT EXPERT CERTIFICATION</a></li>
    			</ul>

    			<div id="tab1" class="tabcontent">
    				<h3 class="black-title text-center">Full Stack IoT Expert Certification Courses</h3>
    				<h5 class="diff-course-subtitle text-center spe-course-tagline-color">The Full Stack IoT Expert Certification’ consists of 7 courses. </h5>
    				<div class="row tab-box-margin">
    					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12"> <a href="iot-beginners-course"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/spe-IOT-for-Beginners.jpg" alt="IoT for Beginners" class="img-responsive spe-course-img"></a> </div>
    					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
    						<a href="iot-beginners-course" target="_blank">
    							<div class="spe-nuber-bg sp-key-circle center-block left xs-num-margin">
    								<p class="white-text">01</p>
    							</div>
    							<h3 class="spe-course-title f-color-blue">IoT for Beginners</h3>
    						</a>
    						<p>Step into the fantastic world of the future with this introductory course on the Internet of Things. Understand the key terminologies, explore its history, and watch stories of how IoT inventors are creating a brave new world with their path-breaking devices.</p>
    					</div>
    				</div>
    				<div class="row tab-box-margin">
    					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12"> <a href="iot-using-arduino"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/spe-IOT-from-the-Ground-Up.jpg" alt="IoT Using Arduino" class="img-responsive spe-course-img"></a> </div>
    					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
    						<a href="iot-using-arduino" target="_blank">
    							<div class="spe-nuber-bg sp-key-circle center-block left xs-num-margin">
    								<p class="white-text">02</p>
    							</div>
    							<h3 class="spe-course-title f-color-blue">IoT from the Ground Up<br class="hidden-xs">
    								– Using Arduino</h3>
    							</a>
    							<p>This course deals with basic electronics, microcontroller architectures, sensors, human-machine interfaces (HMI) and basic networking. We use the Arduino platform to teach these concepts. After doing this course, you should be able to put together IoT projects by combining micro controllers and sensors and connect them to cloud with mobile applications. </p>
    						</div>
    					</div>
    					<div class="row tab-box-margin">
    						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12"> <a href="iot-using-raspberry-pi"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/spe-Powering-the-IOT.jpg" class="img-responsive spe-course-img"></a> </div>
    						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
    							<a href="iot-using-raspberry-pi" target="_blank">
    								<div class="spe-nuber-bg sp-key-circle center-block left xs-num-margin">
    									<p class="white-text">03</p>
    								</div>
    								<h3 class="spe-course-title f-color-blue">Powering IoT<br>
    									– Using The Raspberry Pi</h3>
    								</a>
    								<p>The course focuses on higher-level operating systems, advanced networking, user interfaces, multi-media and uses more compute-intensive IoT applications as examples. We use the Raspberry Pi running Linux as the platform of choice, while also exposing the student to other comparable platforms. It is about gateway devices, where one can achieve scaling in amount of processing.</p>
    							</div>
    						</div>
    						<!-- <div class="row tab-box-margin">
    							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12"> <a href="iot-javascript"><img src="<?php // echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/spe-Basics-of-Javascript.png" alt="Basics of Javascript Training" class="img-responsive spe-course-img"></a> </div>
    							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
    								<a href="iot-javascript" target="_blank">
    									<div class="spe-nuber-bg sp-key-circle center-block left xs-num-margin">
    										<p class="white-text">04</p>
    									</div>
    									<h3 class="spe-course-title f-color-blue">Basics of Javascript</h3>
    								</a>
    								<p>This is a short introduction to Javascript syntax and usage for programmers unfamiliar with the language. It covers the application of JS in both the front-end and back-end contexts and also touches upon advanced concepts like higher-order programming using JS.</p>
    							</div>
    						</div>-->
    						<div class="row tab-box-margin">
    							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12"> <a href="iot-cloud"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/spe-IOT-and-the-Cloud.jpg" alt="IoT and the Cloud Training" class="img-responsive spe-course-img"></a> </div>
    							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
    								<a href="iot-cloud" target="_blank">
    									<div class="spe-nuber-bg sp-key-circle center-block left xs-num-margin">
    										<p class="white-text">04</p>
    									</div>
    									<h3 class="spe-course-title f-color-blue">IoT and the Cloud</h3>
    								</a>
    								<p>In this course, we explore the rapidly evolving field of cloud computing and its relation to IoT. We will examine cloud offerings from all the leading providers, including Amazon, Google and Microsoft, and learn to design and deploy solutions to them. This course will also have several case studies, where we examine real-world problem areas such as wearables, home automation, and smart cities. </p>
    							</div>
    						</div>

    						<div class="row tab-box-margin">
    							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12"> <a href="https://www.jigsawacademy.com/iot/introduction-to-iot-analytics"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/spe-IOT-Analytics-1.jpg" alt="IoT Analyst Certification Courses" class="img-responsive spe-course-img"></a> </div>
    							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
    								<a href="https://www.jigsawacademy.com/iot/introduction-to-iot-analytics" target="_blank">
    									<div class="spe-nuber-bg sp-key-circle center-block left xs-num-margin">
    										<p class="white-text">05</p>
    									</div>
    									<h3 class="spe-course-title f-color-blue">Introduction to IoT Analytics</h3>
    								</a>
    								<p>This course provides an overview and insights into data generation, analysis, and usage from IoT systems, and uses multiple case studies to explain how Analytics is used in IoT scenarios to accomplish desired outcomes. </p>
    							</div>
    						</div>

    						<div class="row tab-box-margin">
    							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12"> <a href="https://www.jigsawacademy.com/iot/data-science-for-iot"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/spe-IOT-Analytics-2.jpg" alt="Data Science for IoT Training" class="img-responsive spe-course-img"></a> </div>
    							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
    								<a href="https://www.jigsawacademy.com/iot/data-science-for-iot" target="_blank">
    									<div class="spe-nuber-bg sp-key-circle center-block left xs-num-margin">
    										<p class="white-text">06</p>
    									</div>
    									<h3 class="spe-course-title f-color-blue">Data Science for IoT</h3>
    								</a>
    								<p>This course provides a comprehensive understanding of the IoT data analytics life cycle, with specific examples and case studies. It illustrates milestones and outcomes at each stage of the cycle, starting with data acquisition, through cleaning, exploratory analysis, preparation, and final analysis for achieving desired outcomes.</p>
    							</div>
    						</div>

    						<div class="row tab-box-margin">
    							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12"> <a href="https://www.jigsawacademy.com/iot/advanced-iot-analytics"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/specialization-page_471x277px.jpg" alt="Advanced IoT Analytics Courses" class="img-responsive spe-course-img"></a> </div>
    							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
    								<a href="https://www.jigsawacademy.com/iot/advanced-iot-analytics" target="_blank">
    									<div class="spe-nuber-bg sp-key-circle center-block left xs-num-margin">
    										<p class="white-text">07</p>
    									</div>
    									<h3 class="spe-course-title f-color-blue">Advanced IoT Analytics</h3>
    								</a>
    								<p>This course explains how to implement advanced analytics and machine learning algorithms to IoT data to build complex IoT solutions. After doing this course, a student will be able to choose and apply the appropriate ML algorithms including classification and segmentation on IoT datasets and streaming data.</p>
    							</div>
    						</div>

    						<div class="row tab-box-margin spe-gray-band">
    							<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
    								<h3 class="spe-gray-band-text f-color-blue">7 courses. The most complete IoT certification available.</h3>
    							</div>
    							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
    								<div class="text-center-xs actionbtn skew-btn spe-btn-padding uppercase">
    									<!-- <a class="font-size-14" data-toggle="modal" data-target="#leadmodal" data-course-id='<?php //echo $GLOBALS['iot_courses']['bundles'][2]['bundle_id']; ?>' data-course-name="<?php //echo $GLOBALS['iot_courses']['bundles'][2]['name']; ?>" > -->
    									<a class="font-size-14" target="_blank" <?php iot_a_part('https://www.jigsawacademy.com/checkout-iot/?c='.$GLOBALS['iot_courses']['bundles'][2]["wp_id"].'&p=path','#loginmodal','#profile-popup1'); ?>>
    										<!-- <span>Become a full stack IoT expert</span> -->
    										<span>BECOME A FULL STACK IOT EXPERT</span>
    									</a>
    								</div>
    							</div>
    						</div>
    						<!-- certificate start -->
    						<div class="row tab-box-margin">
    							<div class="row spe-padding">
    								<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12"> <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Certificate-Final-IOT.jpg" alt="IoT Certification" class="certificate img-responsive"> </div>
    								<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
    									<div class="margin-30 hidden-lg hidden-md hidden-sm"></div>
    									<h3 class="black-title">Certification</h3>
    									<p class="certificate-contain">Become a certified Full Stack Expert with end to end expertise in IoT. After completing this specialisation you’ll have a good understanding of the different pieces of an integrated IoT system, their dynamics and a strong grasp on the entire IoT stack, its architecture, implementation and analysis. </p>
    								</div>
    							</div>
    						</div>
    						<!-- certificate end -->
    					</div>

    					<div id="tab2" class="tabcontent">
    						<h3 class="black-title text-center">IoT Analyst Certification Courses</h3>
    						<h5 class="diff-course-subtitle text-center spe-course-tagline-color">The 'IoT Analyst Certification' consists of 3 courses.</h5>

    						<div class="row tab-box-margin">
    							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12"> <a href="https://www.jigsawacademy.com/iot/introduction-to-iot-analytics"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/spe-IOT-Analytics-1.jpg" class="img-responsive spe-course-img"></a> </div>
    							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
    								<a href="https://www.jigsawacademy.com/iot/introduction-to-iot-analytics" target="_blank">
    									<div class="spe-nuber-bg sp-key-circle center-block left xs-num-margin">
    										<p class="white-text">01</p>
    									</div>
    									<h3 class="spe-course-title f-color-blue">Introduction to IoT Analytics</h3>
    								</a>
    								<p>This course provides an overview and insights into data generation, analysis, and usage from IoT systems, and uses multiple case studies to explain how Analytics is used in IoT scenarios to accomplish desired outcomes. </p>
    							</div>
    						</div>

    						<div class="row tab-box-margin">
    							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12"> <a href="https://www.jigsawacademy.com/iot/data-science-for-iot"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/spe-IOT-Analytics-2.jpg" class="img-responsive spe-course-img"></a> </div>
    							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
    								<a href="https://www.jigsawacademy.com/iot/data-science-for-iot" target="_blank">
    									<div class="spe-nuber-bg sp-key-circle center-block left xs-num-margin">
    										<p class="white-text">02</p>
    									</div>
    									<h3 class="spe-course-title f-color-blue">Data Science for IoT</h3>
    								</a>
    								<p>This course provides a comprehensive understanding of the IoT data analytics life cycle, with specific examples and case studies. It illustrates milestones and outcomes at each stage of the cycle, starting with data acquisition, through cleaning, exploratory analysis, preparation, and final analysis for achieving desired outcomes.</p>
    							</div>
    						</div>

    						<div class="row tab-box-margin margin-bottom-60">
    							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12"> <a href="https://www.jigsawacademy.com/iot/advanced-iot-analytics" ><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/specialization-page_471x277px.jpg" alt="Advanced IoT Analytics Courses" class="img-responsive spe-course-img"></a> </div>
    							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
    								<a href="https://www.jigsawacademy.com/iot/advanced-iot-analytics" target="_blank">
    									<div class="spe-nuber-bg sp-key-circle center-block left xs-num-margin">
    										<p class="white-text">03</p>
    									</div>
    									<h3 class="spe-course-title f-color-blue">Advanced IoT Analytics</h3>
    								</a>
    								<p>This course explains how to implement advanced analytics and machine learning algorithms to IoT data to build complex IoT solutions. After doing this course, a student will be able to choose and apply the appropriate ML algorithms including classification and segmentation on IoT datasets and streaming data.</p>
    							</div>
    						</div>

    						<div class="custom-border"></div>

                                                    <!-- commented by Nikita as per the mail *-->
    						<!-- <div class="row tab-box-margin margin-bottom-60">
    							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 industry-leaders">
    								<span>
    									<h3 class="spe-course-title f-color-blue padding-left-none-xs">Industry Leaders Speak</h3>
    									<p>Facing exponential growth, IoT has become one of the critical components of success stories across industries and domains. Hear what the Industry stalwarts have to say on IoT and its huge potential.</p>
    								</span>
    							</div>

    							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
    								<div class="margin-15 hidden-lg hidden-md hidden-sm"></div>
    								<iframe width="100%" height="315" src="https://www.youtube.com/embed/AD3oESUzYLE" frameborder="0" allowfullscreen></iframe>
    							</div>

    						</div> -->

    						<div class="custom-border"></div>

    						<div class="row tab-box-margin">
    							<h3 class="black-title text-center">4 Reasons To Do the IoT Analyst Certification</h3>

    							<div class="col-lg-1 col-md-1 col-sm-12 col-xs-12"></div>
    							<div class="col-lg-10 col-md-10 col-sm-12 col-xs-12 padding-left-none padding-right-none padding-right-15-xs padding-left-15-xs">
    								<div class="row tab-box-margin">
    									<div class="col-lg-1 col-md-1 col-sm-1 col-xs-2">
    										<div class="dark-blue-bg sp-key-circle center-block ">
    											<p class="white-text">01</p>
    										</div>
    									</div>
    									<div class="col-lg-5 col-md-5 col-sm-5 col-xs-10 padding-left-none padding-left-15-xs">
    										<p class="margin-right-10 xs-margin-right-0">90% of all IoT data will be hosted on service provider platforms within the next five years with cloud computing. - <strong>IDC</strong></p>
    									</div>

    									<div class="col-lg-1 col-md-1 col-sm-1 col-xs-2 xs-margin-top-30">
    										<div class="light-orange-bg sp-key-circle center-block margin-left-10 xs-margin-left-0">
    											<p class="white-text">02</p>
    										</div>
    									</div>
    									<div class="col-lg-5 col-md-5 col-sm-5 col-xs-10 xs-margin-top-30 padding-left-none padding-left-15-xs">
    										<p class="margin-left-10 xs-margin-left-0">IoT will account for 4.4 trillion GB of the data in the digital universe by 2020. - <strong>EMC</strong></p>
    									</div>

    								</div>

    								<div class="row tab-box-margin">
    									<div class="col-lg-1 col-md-1 col-sm-1 col-xs-2">
    										<div class="light-green-bg sp-key-circle center-block ">
    											<p class="white-text">03</p>
    										</div>
    									</div>
    									<div class="col-lg-5 col-md-5 col-sm-5 col-xs-10 padding-left-none padding-left-15-xs">
    										<p class="margin-right-10 xs-margin-right-0">8% of businesses are fully capturing and analysing IoT data in a timely fashion. - <strong>Parstream</strong></p>
    									</div>


    									<div class="col-lg-1 col-md-1 col-sm-1 col-xs-2 xs-margin-top-30">
    										<div class="bg-primary sp-key-circle center-block margin-left-10 xs-margin-left-0">
    											<p class="white-text">04</p>
    										</div>
    									</div>
    									<div class="col-lg-5 col-md-5 col-sm-5 col-xs-10 xs-margin-top-30 padding-left-none padding-left-15-xs">
    										<p class="margin-left-10 xs-margin-left-0">Convergence of machines, data and analytics will become a $200 billion global industry over the next three years. - <strong>GE</strong></p>
    									</div>

    								</div>

    							</div>
    							<div class="col-lg-1 col-md-1 col-sm-12 col-xs-12"></div>
    						</div>


    						<div class="row tab-box-margin spe-gray-band">
    							<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
    								<h3 class="spe-gray-band-text f-color-blue">Master the connection between analytics & IoT with a 3 course specialization.
    									<br class="hidden-xs"></h3>
    								</span> </div>
    								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
    									<div class="text-center-xs actionbtn skew-btn spe-btn-padding uppercase">
    										<a class="font-size-14" target="_blank" <?php iot_a_part('https://www.jigsawacademy.com/checkout-iot/?c='.$GLOBALS['iot_courses']['bundles'][1]["wp_id"].'&p=path','#loginmodal','#profile-popup1'); ?> >
    											<!-- <span>Become a certified IoT analyst</span> -->
    											<span>BECOME A CERTIFIED IOT ANALYST</span>
    										</a>
    									</div>
    								</div>
    							</div>
    							<!-- certificate start -->
    							<div class="row tab-box-margin">
    								<div class="row spe-padding">
    									<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12"> <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Certificate-Final-IOT-Analyst.jpg" alt="IoT Certification" class="certificate img-responsive"> </div>
    									<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
    										<div class="margin-30 hidden-lg hidden-md hidden-sm"></div>
    										<h3 class="black-title">Certification</h3>
    										<p class="certificate-contain">Become a certified IoT Analyst and make the difference where it matters the most. Post completion of this specialization, you’ll learn to focus on the data generated through IoT devices and variety of analytics techniques deployed on this data to derive meaningful insights.</p>
    									</div>
    								</div>
    							</div>
    							<!-- certificate end -->
    						</div>

    						<div id="tab3" class="tabcontent">
    							<h3 class="black-title text-center">IoT Professional Certificate Courses</h3>
    							<h5 class="diff-course-subtitle text-center spe-course-tagline-color">The 'IoT Professional Certification' consists of 4 courses.</h5>
    							<div class="row tab-box-margin xs-padding-top-15">
    								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12"> <a href="iot-beginners-course"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/spe-IOT-for-Beginners.jpg" class="img-responsive spe-course-img"></a> </div>
    								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
    									<a href="iot-beginners-course" target="_blank">
    										<div class="spe-nuber-bg sp-key-circle center-block left xs-num-margin">
    											<p class="white-text">01</p>
    										</div>
    										<h3 class="spe-course-title f-color-blue">IoT for Beginners</h3>
    									</a>
    									<p>Step into the fantastic world of the future with this introductory course on the Internet of Things. Understand the key terminologies, explore its history, and watch stories of how IoT inventors are creating a brave new world with their path-breaking devices.</p>
    								</div>
    							</div>
    							<div class="row tab-box-margin ">
    								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12"> <a href="iot-using-arduino"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/spe-IOT-from-the-Ground-Up.jpg" class="img-responsive spe-course-img"></a> </div>
    								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
    									<a href="iot-using-arduino" target="_blank">
    										<div class="spe-nuber-bg sp-key-circle center-block left xs-num-margin">
    											<p class="white-text">02</p>
    										</div>
    										<h3 class="spe-course-title f-color-blue">IoT from the Ground Up<br>
    											– Using Arduino</h3>
    										</a>
    										<p>This course deals with basic electronics, microcontroller architectures, sensors, human-machine interfaces (HMI) and basic networking. We use the Arduino platform to teach these concepts. After doing this course, you should be able to put together IoT projects by combining micro controllers and sensors and connect them to cloud with mobile applications. </p>
    									</div>
    								</div>
    								<div class="row tab-box-margin">
    									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12"> <a href="iot-using-raspberry-pi"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/spe-Powering-the-IOT.jpg" alt="IoT Projects Using Raspberry Pi" class="img-responsive spe-course-img"></a> </div>
    									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
    										<a href="iot-using-raspberry-pi" target="_blank">
    											<div class="spe-nuber-bg sp-key-circle center-block left xs-num-margin">
    												<p class="white-text">03</p>
    											</div>
    											<h3 class="spe-course-title f-color-blue">Powering IoT<br>
    												– Using The Raspberry Pi</h3>
    											</a>
    											<p>The course focuses on higher-level operating systems, advanced networking, user interfaces, multi-media and uses more compute-intensive IoT applications as examples. We use the Raspberry Pi running Linux as the platform of choice, while also exposing the student to other comparable platforms. It is about gateway devices, where one can achieve scaling in amount of processing.</p>
    										</div>
    									</div>
    									<div class="row tab-box-margin margin-bottom-60">
    										<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12"> <a href="iot-cloud"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/spe-IOT-and-the-Cloud.jpg" class="img-responsive spe-course-img"></a> </div>
    										<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
    											<a href="iot-cloud" target="_blank">
    												<div class="spe-nuber-bg sp-key-circle center-block left xs-num-margin">
    													<p class="white-text">04</p>
    												</div>
    												<h3 class="spe-course-title f-color-blue">IoT and the Cloud</h3>
    											</a>
    											<p>In this course, we explore the rapidly evolving field of cloud computing and its relation to IoT. We will examine cloud offerings from all the leading providers, including Amazon, Google and Microsoft, and learn to design and deploy solutions to them. This course will also have several case studies, where we examine real-world problem areas such as wearables, home automation, and smart cities. </p>
    										</div>
    									</div>
    									<div class="custom-border"></div>

    									<div class="row tab-box-margin">
    										<h3 class="black-title text-center">4 reasons to do the IoT Professional Certification</h3>

    										<div class="col-lg-1 col-md-1 col-sm-12 col-xs-12"></div>
    										<div class="col-lg-10 col-md-10 col-sm-12 col-xs-12 padding-left-none padding-right-none padding-right-15-xs padding-left-15-xs">
    											<div class="row tab-box-margin">

    												<div class="col-lg-1 col-md-1 col-sm-1 col-xs-2">
    													<div class="dark-blue-bg sp-key-circle center-block">
    														<p class="white-text">01</p>
    													</div>
    												</div>
    												<div class="col-lg-5 col-md-5 col-sm-5 col-xs-10 padding-left-none padding-left-15-xs">
    													<p class="margin-right-10 xs-margin-right-0">Due to Internet of Things, jobs in the IT industry are projected to grow by 50 percent before 2020. - <strong>Cisco</strong></p>

    													<!-- <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/four_cisco.png" class="img-responsive center-block">-->
    												</div>

    												<div class="col-lg-1 col-md-1 col-sm-1 col-xs-2 xs-margin-top-30">
    													<div class="light-orange-bg sp-key-circle center-block margin-left-10 xs-margin-left-0">
    														<p class="white-text">02</p>
    													</div>
    												</div>
    												<div class="col-lg-5 col-md-5 col-sm-5 col-xs-10 xs-margin-top-30 padding-left-none padding-left-15-xs">
    													<p class="margin-left-10 xs-margin-left-0">One in five developers is targeting IoT for upcoming projects. - <strong>Evans Data</strong></p>
<!--              <br>
              <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/four_edc.png" class="img-responsive center-block">
          -->            </div>

      </div>

      <div class="row tab-box-margin">
      	<div class="col-lg-1 col-md-1 col-sm-1 col-xs-2">
      		<div class="light-green-bg sp-key-circle center-block">
      			<p class="white-text">03</p>
      		</div>
      	</div>
      	<div class="col-lg-5 col-md-5 col-sm-5 col-xs-10 padding-left-none padding-left-15-xs">
      		<p class="margin-right-10 xs-margin-right-0">IoT projects will take twice as long to complete and firms are finding it difficult to source long-term staff for the jobs. - <strong>Gartner</strong></p>
                  <!--<br>
                  <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/four_gartner.jpg" class="img-responsive center-block">-->
              </div>


              <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2 xs-margin-top-30">
              	<div class="bg-primary sp-key-circle center-block margin-left-10 xs-margin-left-0">
              		<p class="white-text">04</p>
              	</div>
              </div>
              <div class="col-lg-5 col-md-5 col-sm-5 col-xs-10 xs-margin-top-30 padding-left-none padding-left-15-xs">
              	<p class="margin-left-10 xs-margin-left-0">India will have 3 million mobile app developers by 2017 and most them will focus on IoT apps and innovations. - <strong>Convergence Catalyst</strong></p>
              <!--<br>
              <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/four_convergence.png" class="img-responsive center-block">-->
          </div>

      </div>

  </div>
  <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12"></div>
</div>


<div class="row tab-box-margin spe-gray-band">
	<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
		<h3 class="spe-gray-band-text f-color-blue">4 courses. 1 certification. &#8377; 11,000 in savings.</h3>
	</span> </div>
	<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
		<div class="text-center-xs actionbtn skew-btn spe-btn-padding uppercase"><a class="font-size-14" target="_blank" <?php iot_a_part('https://www.jigsawacademy.com/checkout-iot/?c='.$GLOBALS['iot_courses']['bundles'][0]["wp_id"].'&p=path','#loginmodal','#profile-popup1'); ?> ><span>Become a certified IoT professional</span></a></div>
	</div>
</div>
<!-- certificate start -->
<div class="row tab-box-margin">
	<div class="row spe-padding">
		<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12"> <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Certificate-Final-IOT-Professional.jpg" alt="IoT Certification for Professionals" class="certificate img-responsive"> </div>
		<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
			<div class="margin-30 hidden-lg hidden-md hidden-sm"></div>
			<h3 class="black-title">Certification</h3>
			<p class="certificate-contain">Become a certified IoT professional with thorough understanding of IoT concepts. Post completion of this specialization, you’ll gain a solid understanding of how to develop and implement your own IoT solutions and applications.</p>
		</div>
	</div>
</div>
<!-- certificate end -->
</div>
</div>
</div>
</div>
<!-- first tabing end -->

<!-- second tabing start -->
<div class="gray-bg">
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
									<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2"> <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/spe-IOT-Hardware-Kit.png" alt="IoT Hardware Kit" /> </div>
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
									<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2"> <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Catalyst.png" alt="IoT Pre-Recorded Lectures" /> </div>
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
									<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2"> <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/spe-Jigsaw-Learning-Center.png" alt="Jigsaw Academy's IoT Training" /> </div>
									<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
										<div class="vxs-padding-left-15">
											<h3 class="spe-tabbing2-title">Jigsaw Learning Center</h3>
											<p>Gain free access to a variety of supplemental resources like hand-outs, reference material, guides, lecture transcripts and student forums.</p>
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
							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 text-center">
								<img class="img-circle img-responsive center-block" src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/expert-harikrishna.jpg" alt="">
								<h3 class="f-color-blue expert-title">Harikrishna R.</h3>
								<p class="minhei150">He is the co-founder and director of Klar Systems, where he designs and builds cool new gadgets, applications and platforms for the IoT era.</p>
								<a href="https://www.linkedin.com/in/harikrishnar" target="_blank"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/linkedin_icon.jpg" class="img-responsive center-block padding-top-7"></a>
							</div>
							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 text-center">
								<div class="margin-30 hidden-lg hidden-md hidden-sm"></div>
								<img class="img-circle img-responsive center-block" src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/expert-srinivas.jpg" alt="">
								<h3 class="f-color-blue expert-title">Srinivas Padmanabhuni</h3>
								<p class="minhei150">He is the immediate past President of ACM India. Prior to co-founding Tarah Technologies, he was Associate Vice President heading research at Infosys till Oct. 2015.</p>
								<a href="https://www.linkedin.com/in/spadmanabhuni" target="_blank"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/linkedin_icon.jpg" class="img-responsive center-block padding-top-7"></a>
							</div>
							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 text-center">
								<div class="margin-30 hidden-lg hidden-md hidden-sm"></div>
								<img class="img-circle img-responsive center-block" src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/expert-urmil.jpg" alt="">
								<h3 class="f-color-blue expert-title">Urmil Parikh</h3>
								<p class="minhei150">He is co-founder of Klar Systems, an emerging IoT start-up.He carries extensive experience in embedded systems, connectivity and application development.</p>
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
<!-- <div class="wrapper section-padding-top section-padding-bottom">
	<div class="row spe-gray-band">
		<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
			<h3 class="spe-gray-band-text f-color-blue font-19">4 courses. 1 certification. Rs 12,000 in savings. Become a certified IoT professional</h3>
		</span> </div>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<div class="text-center-xs actionbtn skew-btn spe-btn-padding uppercase"><a target="_blank" <?php iot_a_part('https://www.jigsawacademy.com/checkout/?c='.$GLOBALS['iot_courses']['bundles'][0]["wp_id"].'&p=path','#loginmodal','#profile-popup1'); ?> ><span>Enroll Now</span></a></div>
		</div>
	</div>
</div> -->
<!-- gray-band end -->

<!-- FOOTER -->
<?php load_template("iot", "v2/footer"); ?>
<!-- FOOTER ENDS -->

<!-- LOGIN MODAL -->
<?php load_template("iot", "v2/login"); ?>
<!-- LOGIN MODAL ENDS -->

<script>
	$(document).ready(function() {
		$("#owl-demo").owlCarousel({
      autoPlay: 5000, //Set AutoPlay to 5 seconds
      items : 3,
      itemElement : 1,
      navigation : true,
      pagination: false
  });
  
		$('.tab>li>a.tablinks').click(function(e){
            e.preventDefault();
			$('.tab>li>a.tablinks').removeClass('active');
			$('.tabcontent').hide();
			$(this).addClass('active');
			$('.tabcontent').eq($(this).data('index') - 1).show();
		});

		$('.tab>li>a.tablinks').first().trigger('click');

		$('a.header-link').click(function(){
			$('.tab>li>a.tablinks').eq($(this).data('index') - 1).trigger('click');
		});
	});
</script>
<!-- FOOTER -->
	<?php load_template("iot", "v2/foot"); ?>
<!-- FOOTER ENDS -->