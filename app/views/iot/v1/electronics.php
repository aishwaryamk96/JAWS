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
	$GLOBALS["content"]["title"] = "Electronics";
	load_template("iot", "v1/head");
  ?>
  <!-- HTML HEAD ENDS -->

  <!-- HEADER MENU -->
  <?php load_template("iot", "v1/header"); ?>
  <!-- HEADER MENU ENDS -->

<!-- Banner Start -->
<div class="gray-bg" id="side_nav_item_id_0">
  <div class="wrapper header-padding-top">
	<div class="row">
	  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<h1 class="innerpage-banner-title f-color-blue">IoT from the Ground Up Using Arduino</h1>
		<p class="banner-contain">Build IoT projects with microcontrollers and sensors and connect them to cloud. </p>
		<div class="margin-30"></div>
		<div class="row">
		  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="pull-left">
			  <h3 class="cancel-prize"> <strike><i class="fa fa-inr" aria-hidden="true"></i>35,000</strike> </h3>
			  <h1 class="prize"><i class="fa fa-inr" aria-hidden="true"></i>25,000<span class="taxes-text">+ taxes </span> </h1>
			</div>
			<div class="innerpage-btn-margin pull-left"><a href="#" class="text-capitalize skew-fill-color innerpage-btn">Enroll Now</a></div>
		  </div>
		</div>
	  </div>
	  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<div class="margin-30 hidden-lg hidden-md hidden-sm"></div>
		<div class="header-img-margin hidden-xs"> <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/electronics-header.jpg" class="img-responsive"> </div>
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
		<li><a href="#about-section" class="cool-link">ABOUT THIS SPECIALIZATION</a></li>
		<li><a href="#key-feature-section" class="cool-link">key features</a></li>
		<li><a href="#curriculum-section" class="cool-link">curriculum</a></li>
		<li><a href="#you-get-section" class="cool-link">WHAT YOU will GET</a></li>
	  </ul>
	</nav>
  </div>
</div>

<!-- submenu end --> 

<!-- about section start -->
<div class="wrapper section-padding-top section-padding-bottom" id="about-section">
  <div class="row hidden-lg hidden-md hidden-sm">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	  <h2 class="innerpage-title">About this Specialization</h2>
	  <div class="gary-strip"></div>
	</div>
  </div>
  <div class="conatin-margin-top hidden-lg hidden-md hidden-sm"></div>
  <div class="row">
	<div class="col-lg-8 col-md-8 col-sm-8 padding-right-30">
	  <p>This course deals with basic electronics, microcontroller architectures, sensors, human-machine interfaces (HMI) and basic networking. We use the Arduino platform to teach these concepts.</p>
	  <br>
	  <p>After doing this course you should be able to put together IOT projects by combining micro controllers and sensors and connect them to the cloud with mobile applications. This course equips you to make complete projects based on microcontrollers.</p>
	</div>
	<div class="col-lg-4 col-md-4 col-sm-4 border-left-side padding-left-40">
	  <p><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/prerequisites.png"> &nbsp; <span class="capital f-color-blue">prerequisites:</span> Basic</p>
	  <p><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Monitor.png"> &nbsp; <span class="capital f-color-blue">platform:</span> 5</p>
	  <p><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Calendar.png"> &nbsp; <span class="capital f-color-blue">duration:</span> 7 Hrs.</p>
	  <p><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/capstone-projects.png"> &nbsp; <span class="capital f-color-blue">capstone-projects:</span> 1</p>
	</div>
  </div>
</div>

<!-- about section end --> 

<!-- keyfeature section start -->
<div class="border-top">
  <div class="wrapper section-padding-top section-padding-bottom" id="key-feature-section">
	<div class="row">
	  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<h2 class="innerpage-title">Key Features</h2>
		<div class="gary-strip"></div>
	  </div>
	</div>
	<div class="row conatin-margin-top">
	  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<p><span><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/check-icn.png"></span> &nbsp;&nbsp; Most comprehensive course in IOT to date</p>
		<p><span><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/check-icn.png"></span> &nbsp;&nbsp; Learning by doing -  Very high emphasis on practical learning</p>
		<p><span><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/check-icn.png"></span> &nbsp;&nbsp; Only Specialization integrating Analytics and Big Data with IoT concepts</p>
		<p><span><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/check-icn.png"></span> &nbsp;&nbsp; IoT Hardware Kit as a part of the course</p>
		<p><span><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/check-icn.png"></span> &nbsp;&nbsp; Course designed by IoT practitioners</p>
		<p><span><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/check-icn.png"></span> &nbsp;&nbsp; Makes you IOT ready professional</p>
		<p><span><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/check-icn.png"></span> &nbsp;&nbsp; Suitable for both hardware and software professional</p>
	  </div>
	</div>
  </div>
</div>
<!-- keyfeature section start --> 

<!-- curriculum section start -->
<div class="gray-bg" id="curriculum-section">
  <div class="wrapper section-padding-top section-padding-bottom relative">
	<div class="curriculum-gray-design hidden-xs"> <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/curriculum-gray-design.jpg"> </div>
	<div class="row">
	  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<h2 class="innerpage-title">Curriculum</h2>
		<div class="gary-strip"></div>
	  </div>
	</div>
	<div class="margin-30"></div>
	<div class="row">
	  <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
		<div class="demo">
		  <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
			<div class="panel panel-default">
			  <div class="panel-heading" role="tab" id="heading1">
				<h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse1" aria-expanded="true" aria-controls="collapse1">Introduction to IOT </a> </h4>
			  </div>
			  <div id="collapse1" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading1">
				<div class="panel-body">
				  <ul>
					<li>Intro to Arduino</li>
					<li>What is Arduino</li>
					<li>Installation Steps</li>
					<li>Programming the attiny</li>
					<li>POV example</li>
				  </ul>
				</div>
			  </div>
			</div>
			<div class="panel panel-default">
			  <div class="panel-heading" role="tab" id="heading2">
				<h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse2" aria-expanded="true" aria-controls="collapse2">Arduino Code Language </a> </h4>
			  </div>
			  <div id="collapse2" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading2">
				<div class="panel-body">
				  <ul>
					<li>Variables, Types, Operators and Statements</li>
					<li>Control Statements</li>
					<li>Functions</li>
					<li>Variable Scope</li>
					<li>Arrays and Pointers</li>
					<li>Dynamic Memory</li>
					<li>Bitwise Functions</li>
					<li>Fixed Point Arithmetic</li>
				  </ul>
				</div>
			  </div>
			</div>
			<div class="panel panel-default">
			  <div class="panel-heading" role="tab" id="heading3">
				<h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse3" aria-expanded="true" aria-controls="collapse3">Inputs and App Control </a> </h4>
			  </div>
			  <div id="collapse3" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading3">
				<div class="panel-body">
				  <ul>
					<li>Reading a button input</li>
					<li>Busy-wait vs. Interrupt</li>
					<li>Intro to Blynk</li>
					<li>Virtual pins</li>
					<li>Automated light</li>
				  </ul>
				</div>
			  </div>
			</div>
			<div class="panel panel-default">
			  <div class="panel-heading" role="tab" id="heading4">
				<h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse4" aria-expanded="true" aria-controls="collapse4">Arduino Simulation Environment </a> </h4>
			  </div>
			  <div id="collapse4" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading4">
				<div class="panel-body">
				  <ul>
					<li>What is simulation</li>
					<li>Intro to 123Circuits</li>
					<li>Advantages/Disadvantages of simulation</li>
					<li>Example circuit</li>
					<li>Choose "Platform" and make "Connections"</li>
					<li>Redo LED and switch example</li>
					<li>Why are we talking about simulation: will get better, easier to share projects, schematics, other such resources</li>
				  </ul>
				</div>
			  </div>
			</div>
			<div class="panel panel-default">
			  <div class="panel-heading" role="tab" id="heading5">
				<h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse5" aria-expanded="true" aria-controls="collapse5">Analog Sensors </a> </h4>
			  </div>
			  <div id="collapse5" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading5">
				<div class="panel-body">
				  <ul>
					<li>LDR</li>
					<li>Blynk demo</li>
					<li>Issues: Calibration, Noise, Settling time</li>
					<li>ADC specifications (settling time)</li>
				  </ul>
				</div>
			  </div>
			</div>
			<div class="panel panel-default">
			  <div class="panel-heading" role="tab" id="heading6">
				<h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse6" aria-expanded="true" aria-controls="collapse6">Basic electronics refresher </a> </h4>
			  </div>
			  <div id="collapse6" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading6">
				<div class="panel-body">
				  <ul>
					<li>Resisters, Capacitors and Inductors</li>
					<li>Diodes, transistors and OpAmps</li>
					<li>ADCs and DACs</li>
					<li>Voltage domains</li>
				  </ul>
				</div>
			  </div>
			</div>
			<div class="panel panel-default">
			  <div class="panel-heading" role="tab" id="heading7">
				<h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse7" aria-expanded="true" aria-controls="collapse7">Digital Sensors </a> </h4>
			  </div>
			  <div id="collapse7" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading7">
				<div class="panel-body">
				  <ul>
					<li>DHT11</li>
					<li>How to read temp/humidity</li>
					<li>Blynk demo</li>
				  </ul>
				</div>
			  </div>
			</div>
			<div class="panel panel-default">
			  <div class="panel-heading" role="tab" id="heading8">
				<h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse8" aria-expanded="true" aria-controls="collapse8">Digital Signals </a> </h4>
			  </div>
			  <div id="collapse8" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading8">
				<div class="panel-body">
				  <ul>
					<li>Serial vs Parallel</li>
					<li>1-, 2- and 3-wire formats</li>
					<li>Timing</li>
					<li>Bit-banging vs. dedicated peripheral</li>
					<li>Demo</li>
					<li>Serial flash</li>
				  </ul>
				</div>
			  </div>
			</div>
			<div class="panel panel-default">
			  <div class="panel-heading" role="tab" id="heading9">
				<h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse9" aria-expanded="true" aria-controls="collapse9">Sensors overview </a> </h4>
			  </div>
			  <div id="collapse9" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading9">
				<div class="panel-body">
				  <ul>
					<li>Overview of commonly available sensors and their uses</li>
				  </ul>
				</div>
			  </div>
			</div>
			<div class="panel panel-default">
			  <div class="panel-heading" role="tab" id="heading10">
				<h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse10" aria-expanded="true" aria-controls="collapse10">User Interface: Buttons and displays </a> </h4>
			  </div>
			  <div id="collapse10" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading10">
				<div class="panel-body">
				  <ul>
					<li>OLED display</li>
					<li>Example showing temp/humidity/LDR on display</li>
					<li>Other input devices</li>
					<li>Potentiometer</li>
					<li>Joystick</li>
					<li>Rotary encoder</li>
				  </ul>
				</div>
			  </div>
			</div>
			<div class="panel panel-default">
			  <div class="panel-heading" role="tab" id="heading11">
				<h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse11" aria-expanded="true" aria-controls="collapse11">uC concepts 1 - memory map, IO peripherals </a> </h4>
			  </div>
			  <div id="collapse11" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading11">
				<div class="panel-body">
				  <ul>
					<li>Program vs Data memory</li>
					<li>Bss vs Stack vs Heap</li>
					<li>Memory mapped peripherals</li>
					<li>Stack overflow demo</li>
				  </ul>
				</div>
			  </div>
			</div>
			<div class="panel panel-default">
			  <div class="panel-heading" role="tab" id="heading12">
				<h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse12" aria-expanded="true" aria-controls="collapse12">Arduino h/w overview</a> </h4>
			  </div>
			  <div id="collapse12" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading12">
				<div class="panel-body">
				  <ul>
					<li>Overview of all the major "official" arduinos</li>
					<li>Some examples of un-official boards that support Arduino</li>
				  </ul>
				</div>
			  </div>
			</div>
			<div class="panel panel-default">
			  <div class="panel-heading" role="tab" id="heading13">
				<h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse13" aria-expanded="true" aria-controls="collapse13">Intro to nodemcu</a> </h4>
			  </div>
			  <div id="collapse13" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading13">
				<div class="panel-body">
				  <ul>
					<li>Walkthrough nodemcu</li>
					<li>Arduino install and hello world</li>
					<li>Blynk example (LED blink)</li>
				  </ul>
				</div>
			  </div>
			</div>
			<div class="panel panel-default">
			  <div class="panel-heading" role="tab" id="heading14">
				<h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse14" aria-expanded="true" aria-controls="collapse14">Basic Wireless Networking</a> </h4>
			  </div>
			  <div id="collapse14" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading14">
				<div class="panel-body">
				  <ul>
					<li>Standards b/g/n</li>
					<li>Security PEK,WPA,WPA2, WEP</li>
					<li>Pairing problem</li>
					<li>Hardcoding SSID and password on nodemcu</li>
					<li>WPS</li>
					<li>Hotspot</li>
					<li>Smartconfig</li>
				  </ul>
				</div>
			  </div>
			</div>
			<div class="panel panel-default">
			  <div class="panel-heading" role="tab" id="heading15">
				<h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse15" aria-expanded="true" aria-controls="collapse15">Sensor Log</a> </h4>
			  </div>
			  <div id="collapse15" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading15">
				<div class="panel-body">
				  <ul>
					<li>Connect DHT-11, LDR to nodemcu</li>
					<li>Log to thingspeak</li>
					<li>Show graphs</li>
				  </ul>
				</div>
			  </div>
			</div>
			<div class="panel panel-default">
			  <div class="panel-heading" role="tab" id="heading16">
				<h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse16" aria-expanded="true" aria-controls="collapse16">HTTP and REST API</a> </h4>
			  </div>
			  <div id="collapse16" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading16">
				<div class="panel-body">
				  <ul>
					<li>Use steps from prev example using Postman</li>
					<li>HTTP verbs; semantics</li>
					<li>Headers</li>
					<li>Payload and encoding</li>
				  </ul>
				</div>
			  </div>
			</div>
			<div class="panel panel-default">
			  <div class="panel-heading" role="tab" id="heading17">
				<h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse17" aria-expanded="true" aria-controls="collapse17">LED blink using MQTT</a> </h4>
			  </div>
			  <div id="collapse17" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading17">
				<div class="panel-body">
				  <ul>
					<li>Connect LED and button</li>
					<li>Control via MQTT and physically</li>
				  </ul>
				</div>
			  </div>
			</div>
			<div class="panel panel-default">
			  <div class="panel-heading" role="tab" id="heading18">
				<h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse18" aria-expanded="true" aria-controls="collapse18">Messaging and MQTT</a> </h4>
			  </div>
			  <div id="collapse18" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading18">
				<div class="panel-body">
				  <ul>
					<li>Why messaging (disadvantages of rest)</li>
					<li>Pub/Sub pattern</li>
					<li>Broker, Client</li>
					<li>QoS</li>
					<li>LWT</li>
				  </ul>
				</div>
			  </div>
			</div>
			<div class="panel panel-default">
			  <div class="panel-heading" role="tab" id="heading19">
				<h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse19" aria-expanded="true" aria-controls="collapse19">uC concepts 2 -- interrupts, timers, callbacks, re-entrancy, watchdog (15 mins)</a> </h4>
			  </div>
			  <div id="collapse19" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading19">
				<div class="panel-body">
				  <ul>
					<li>NA</li>
				  </ul>
				</div>
			  </div>
			</div>
			<div class="panel panel-default">
			  <div class="panel-heading" role="tab" id="heading20">
				<h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse20" aria-expanded="true" aria-controls="collapse20">nodemcu discovery</a> </h4>
			  </div>
			  <div id="collapse20" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading20">
				<div class="panel-body">
				  <ul>
					<li>NA</li>
				  </ul>
				</div>
			  </div>
			</div>
			<div class="panel panel-default">
			  <div class="panel-heading" role="tab" id="heading21">
				<h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse21" aria-expanded="true" aria-controls="collapse21">nodemcu OTA</a> </h4>
			  </div>
			  <div id="collapse21" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading21">
				<div class="panel-body">
				  <ul>
					<li>NA</li>
				  </ul>
				</div>
			  </div>
			</div>
			<div class="panel panel-default">
			  <div class="panel-heading" role="tab" id="heading22">
				<h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse22" aria-expanded="true" aria-controls="collapse22">Capstone Project</a> </h4>
			  </div>
			  <div id="collapse22" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading22">
				<div class="panel-body">
				  <ul>
					<li>At the end of this course you will work on a standalone project named Desk buddy. Deskbuddy is an IOT project with which students can monitor the work environment.</li>
				  </ul>
				</div>
			  </div>
			</div>
		  </div>
		</div>
	  </div>
	  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12"> </div>
	</div>
  </div>
</div>
<!-- courses section end --> 

<!-- youget section start -->
<div class="wrapper section-padding-top section-padding-bottom" id="you-get-section">
  <div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	  <h2 class="innerpage-title center">What Will You Get?</h2>
	  <div class="gary-strip center-block"></div>
	</div>
  </div>
  <div class="row section-padding-top">
	<div class="col-lg-1 col-md-1 col-sm-12 col-xs-12"> </div>
	<div class="col-lg-5 col-md-5 col-sm-6 col-xs-12">
	  <div class="row">
		<div class="col-lg-2 col-md-2 col-sm-3 col-xs-2">
		  <div class="dark-blue-bg specialization-small-circle"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/hardwarekit-icn.png"></div>
		</div>
		<div class="col-lg-10 col-md-10 col-sm-9 col-xs-10">
		  <p><b>IoT Hardware Kit</b></p>
		  <p class="feature-contain">Get Arduino and Raspberry Pi along with WiFi Dongle, Webcam, Sensor modules and other accessories</p>
		</div>
	  </div>
	</div>
	<div class="col-lg-5 col-md-5 col-sm-6 col-xs-12">
	  <div class="conatin-margin-top hidden-lg hidden-md hidden-sm"></div>
	  <div class="row">
		<div class="col-lg-2 col-md-2 col-sm-3 col-xs-2">
		  <div class="light-orange-bg specialization-small-circle"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/pre-recorded-icn.png"></div>
		</div>
		<div class="col-lg-10 col-md-10 col-sm-9 col-xs-10">
		  <p><b>Pre-recorded Lectures</b></p>
		  <p class="feature-contain">View 30 hours of video lectures, as often as you want</p>
		</div>
	  </div>
	</div>
	<div class="col-lg-1 col-md-1 col-sm-12 col-xs-1"> </div>
  </div>
  <div class="row conatin-margin-top">
	<div class="col-lg-1 col-md-1 col-sm-12 col-xs-12"> </div>
	<div class="col-lg-5 col-md-5 col-sm-6 col-xs-12">
	  <div class="row">
		<div class="col-lg-2 col-md-2 col-sm-3 col-xs-2">
		  <div class="light-green-bg specialization-small-circle"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/learning-centre-icn.png"></div>
		</div>
		<div class="col-lg-10 col-md-10 col-sm-9 col-xs-10">
		  <p><b>Jigsaw Learning Centre</b></p>
		  <p class="feature-contain">Gain free access to a variety of supplemental resources like handouts, reference material, guides, lecture transcripts and student forums</p>
		</div>
	  </div>
	</div>
	<div class="col-lg-5 col-md-5 col-sm-6 col-xs-12">
	  <div class="conatin-margin-top hidden-lg hidden-md hidden-sm"></div>
	  <div class="row">
		<div class="col-lg-2 col-md-2 col-sm-3 col-xs-2">
		  <div class="light-blue-bg specialization-small-circle"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/faculty-support-icn.png"></div>
		</div>
		<div class="col-lg-10 col-md-10 col-sm-9 col-xs-10">
		  <p><b>Faculty Support</b></p>
		  <p class="feature-contain">Get your doubts solved by the Jigsaw Faculty via email, phone or chat</p>
		</div>
	  </div>
	</div>
	<div class="col-lg-1 col-md-1 col-sm-12 col-xs-12"> </div>
  </div>
</div>
<!-- youget section end --> 

	<!-- BLUE BAND -->
	<?php load_template("iot", "v1/blueband"); ?>
	<!-- BLUE BAND ENDS -->

	<!-- FOOTER -->
	<?php load_template("iot", "v1/footer"); ?>
	<!-- FOOTER ENDS -->

	<!-- LOGIN MODAL -->
	<?php load_template("iot", "v1/login"); ?>
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
 
	  autoPlay: 3000, //Set AutoPlay to 3 seconds
 
	  items : 3,
	  itemsDesktop : [1199,3],
	  itemsDesktopSmall : [979,2]
 
  });
 
});
</script>
</body>
</html>