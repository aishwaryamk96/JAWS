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

    	$index = 0;


?>

  <!-- HTML HEAD -->
  <?php
	$GLOBALS["content"]["title"] = "IoT for Beginners and an Introduction to IoT";
	$GLOBALS["content"]["meta_description"] = "Start IoT for Beginners course by Jigsaw Academy and be ready to move into the futuristic world of the Internet of Things (IoT). Know more key terminologies, history, applications, success stories etc with free IoT beginners course online.";
	load_template("iot", "v2/head");
  ?>
  <!-- HTML HEAD ENDS -->

  <!-- HEADER MENU -->
  <?php load_template("iot", "v2/header"); ?>
  <!-- HEADER MENU ENDS -->
<!-- Banner Start -->
		<div class="beginners-benner section sTop header-padding-bottom header-padding-top" id="side_nav_item_id_0">
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
						<a class="last" href="https://www.jigsawacademy.com/iot/iot-beginners-course" itemprop="url">
							<span itemprop="title">IoT for Beginners</span>
						</a>
					</span>
				</li>
			</ol>
		</section>
			<div class="wrapper ">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 v-center-text">
						<span>
							<h1 class="innerpage-banner-title white-text">IoT for Beginners</h1>
							<div class="rating" itemscope="itemscope" itemtype="http://data-vocabulary.org/Review-aggregate">
								<img src="https://www.jigsawacademy.com/wp-content/uploads/2016/08/four_five_star.png" alt="4.6 Star Rating: Very Good" width="79" height="15" title="4.6">
								<span>4.6 Ratings </span>
								<meta itemprop="itemreviewed" content="IoT for Beginners">
								<meta itemprop="rating" content="4.6">
								<meta itemprop="votes" content="24">
							</div>
							<p class="banner-contain white-text">Get started with the exciting world of the Internet of Things</p>
							<div class="margin-30"></div>
							<div class="row">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <!--<div class="pull-left">
              <h3 class="cancel-prize"> <strike><i class="fa fa-inr" aria-hidden="true"></i>7,500</strike> </h3>
              <h1 class="prize padding-top-7">FREE<span class="taxes-text"> </span> </h1>
          </div>-->
          <div class="pull-left">
          	<div class="text-center-xs actionbtn skew-btn"> <a target="_blank" <?php iot_a_part('https://www.jigsawacademy.com/jaws/free.jlc?course='.$GLOBALS['iot_courses']['courses'][0]["course_id"].(auth_session_is_logged() ? "&soc=".$_SESSION["user"]["jlc.free.soc"] : ""),'#loginmodal','#profile-popup1'); ?> ><span class="sign-up-top uppercase">Start your free course now</span></a> </div>
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
<div class="nav-scrolling site-header hidden-xs">
	<div class="wrapper ">
		<nav class="navbar navbar-default navbar-custom ">
			<ul class="nav navbar-nav pull-left">
				<li><a href="#about-section" class="cool-link">about this course</a></li>
				<!-- <li><a href="#key-feature-section" class="cool-link">key features</a></li>-->
				<li><a href="#curriculum-section" class="cool-link">curriculum</a></li>
       <!-- <li><a href="#you-get-section" class="cool-link">What you get</a></li>
       <li><a href="#learning-outcomes" class="cool-link">Learning Outcomes</a></li>-->
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
					<div class="col-lg-8 col-md-8 col-sm-8 padding-left-none">
						<nav class="navbar navbar-default navbar-custom">
							<ul class="nav navbar-nav pull-left">
								<li><a href="#about-section" class="cool-link">about this course</a></li>
								<!--<li><a href="#key-feature-section" class="cool-link">key features</a></li>-->
								<li><a href="#curriculum-section" class="cool-link">curriculum</a></li>
								<!--<li><a href="#you-get-section" class="cool-link">What you get</a></li>-->
								<!--<li><a href="#learning-outcomes" class="cool-link">Learning Outcomes</a></li>-->
							</ul>
						</nav>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-4">
						<!--<div class="pull-left"><a href="#" class="uppercase signup-btn skew-btn innerpage-btn margin-top-8 a-hover">Start now for free</a></div>-->
						<div class="text-center-xs actionbtn skew-btn  margin-top-8 a-hover"> <a href="#"><span class="fixedtop-tab-nav-btn uppercase">Start your free course now</span></a> </div>
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
			<h2 class="innerpage-title text-center">About this course</h2>
			<div class="gary-strip center-block"></div>
		</div>
	</div>
	<div class="conatin-margin-top hidden-lg hidden-md hidden-sm"></div>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12">


		<div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"></div>

		<div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
			<p>Step into the fantastic world of the future with this introductory course on the Internet of Things. Understand key terminologies, explore its history and watch stories of how IoT inventors are creating a brave new world with their path-breaking devices.</p>
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
					<li><p class="font16"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/prerequisites.png"> &nbsp; <span class="capital f-color-blue">prerequisites : </span> None</p></li>
					<li class="hidden-xs">|</li>
					<li><p class="font16"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Monitor.png"> &nbsp; <span class="capital f-color-blue">platform : </span> None</p></li>
					<li class="hidden-xs">|</li>
					<li><p class="font16"><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/Calendar.png"> &nbsp; <span class="capital f-color-blue">duration : </span> 60 Minutes</p></li>
				</ul>
			</div>
		</div>
		<div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"></div>
	</div>
</div>

</div>
<!-- about section end -->

<!--Curriculum Section start-->

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
								<img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/timeline-1.png" alt="IoT Introduction for Beginners" class="img-responsive center-block padding-top-15">
							</div>

							<div class="timeline-label">
								<h2>Introduction</h2>

								<div id="accordion">
									<div class="target-timeline">
										<div class="timeline-mini-icon">
											<h4>1</h4>
										</div>
										<h3 class="f-color-blue">What is IoT?</h3>
									</div>

									<div class="target-timeline">
										<div class="timeline-mini-icon bg-yellow">
											<h4>2</h4>
										</div>
										<h3 class="f-color-blue">Into the IoT Time machine</h3>
									</div>
								</div>
							</div>
						</div>
					</article>

					<article class="timeline-entry">
						<div class="timeline-entry-inner">
							<div class="timeline-icon text-center">
								<img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/timeline-2.png" alt="Working with IoT" class="img-responsive center-block padding-top-15">
							</div>

							<div class="timeline-label">
								<h2>Working with IoT</h2>

								<div class="timeline-mini-icon">
									<h4>1</h4>
								</div>
								<h3 class="f-color-blue">How does IoT work?</h3>

								<div class="timeline-mini-icon bg-yellow">
									<h4>2</h4>
								</div>
								<h3 class="f-color-blue">Building IoT</h3>

								<div class="more-content">
									<div class="timeline-mini-icon">
										<h4>3</h4>
									</div>
									<h3 class="f-color-blue">What are the words I need to know?</h3>

									<div class="timeline-mini-icon bg-yellow">
										<h4>4</h4>
									</div>
									<h3 class="f-color-blue">Challenges in IoT</h3>

									<div class="timeline-mini-icon">
										<h4>5</h4>
									</div>
									<h3 class="f-color-blue">What is the Connection? - IoT, Analytics & Big Data</h3>
								</div>
								<p><a class="readmore" href="#">Show More (+)</a></p>
							</div>
						</div>

					</article>
					<article class="timeline-entry">
						<div class="timeline-entry-inner">
							<div class="timeline-icon text-center">
								<img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/timeline-3.png" alt="Get Started with IoT" class="img-responsive center-block padding-top-15">
							</div>

							<div class="timeline-label">
								<h2>Getting Started with IoT</h2>

								<div class="timeline-mini-icon">
									<h4>1</h4>
								</div>

								<h3 class="f-color-blue">Careers in IoT</h3>

								<div class="timeline-mini-icon bg-yellow">
									<h4>2</h4>
								</div>
								<h3 class="f-color-blue">IoT Success Stories</h3>

								<div class="more-content">
									<div class="timeline-mini-icon">
										<h4>3</h4>
									</div>
									<h3 class="f-color-blue">So, how do I get started?</h3>

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
<!--Curriculum Section end-->

<!-- gray-band start -->
<div class="wrapper section-padding-top section-padding-bottom">
	<div class="row spe-gray-band">
		<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
			<h3 class="spe-gray-band-text f-color-blue font-19">4 courses. 1 certification. Rs 14,000 in savings. Become a certified IoT professional.</h3>
		</span> </div>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<div class="text-center-xs actionbtn skew-btn spe-btn-padding uppercase">
				<!-- <a href="#" data-toggle="modal" data-target="#loginmodal" data-ru="https://www.jigsawacademy.com/checkout/?c=10758&p=path" data-verify="true"><span>Enroll Now</span>
				</a> -->

				<a target="_blank" <?php iot_a_part('https://www.jigsawacademy.com/checkout-iot/?c='.$GLOBALS['iot_courses']['bundles'][0]["wp_id"].'&p=path','#loginmodal','#profile-popup1'); ?> ><span>Enroll Now</span>
				</a>
			</div>
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
		var txt = $(this).parent("p").prev(".more-content").is(':visible') ? 'Show More (+)' : 'Less (–)';
		$(this).parent().prev(".more-content").toggleClass("visible");
		$(this).html(txt);
		event.preventDefault();
	});
</script>
<script>
	$('#accordion').tsReadMore({
		itemSelector: 'div.panel.panel-default',
		readMoreSelector: '#read-more-btn',
		openItemsNo: 6
	});
</script>

<script>
	var menu = document.querySelector('.menu');
	function toggleMenu () {
		menu.classList.toggle('open');
	}
	menu.addEventListener('click', toggleMenu);
</script>
<!-- FOOTER -->
	<?php load_template("iot", "v2/foot"); ?>
<!-- FOOTER ENDS -->