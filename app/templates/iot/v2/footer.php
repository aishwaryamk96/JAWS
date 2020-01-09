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

    ?>

    <!-- footer1 start -->
    <div class="footer-bg" id="fifthpage" style='margin-bottom: -30px;'>
    	<div class="wrapper section-padding-top section-padding-bottom">
    		<div class="row">
    			<div class="col-lg-5 col-md-5 col-sm-6 col-xs-10">
    				<p class="footer-contain">Jigsaw Academy is a global award-winning online analytics and Big Data training provider.</p>
    				<div class="margin-30"></div>
    				<p class="footer-contain">Jigsaw's extensive background and credentials in analytics and Big Data led to creating courses in the cutting-edge, information-based domain of the Internet of Things (IoT).</p>
    				<div class="margin-30"></div>
    				<ul class="list-inline footer-contact-info">
    					<li><img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/call-out-icn.png"></li>
    					<li>+91 90193-17000 </li>
    					<li><i class="fa fa-envelope-o" aria-hidden="true"></i></li>
    					<li><a href="mailto:info@jigsawacademy.com">info@jigsawacademy.com</a></li>
    				</ul>
                                    <div class="margin-30"></div>
                                    <p class="footer-contain">
                                            <span style="padding-right: 10px;">
                                                <a href="https://twitter.com/jigsawacademy" target="_blank" class="hvr-flip">
                                                    <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/twitter-icn.png" alt="Jigsaw Academy Twitter Icon">
                                                </a>
                                            </span>
                                            <span style="padding-right: 10px;">
                                                <a href="https://www.linkedin.com/company/jigsaw-academy" target="_blank" class="hvr-flip">
                                                    <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/linkedin-icn.png" alt="Jigsaw Academy Linkedin Icon" >
                                                </a>
                                            </span>
                                             <span style="padding-right: 10px;">
                                                <a href="https://www.youtube.com/user/jigsawAcademy" target="_blank" class="hvr-flip">
                                                    <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/youtube-icn.png" alt="Jigsaw Academy Youtube Icon">
                                                </a>
                                            </span>
                                             <span>
                                                    <a href="https://www.facebook.com/jigsawacademy/" target="_blank" class="hvr-flip">
                                                        <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/fb-icn.png" alt="Jigsaw Academy Facebook Icon">
                                                    </a>
                                            </span>
                                    </p>
    			</div>
    			<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
    				<div class="margin-30 hidden-lg hidden-md hidden-sm"></div>
    				<ul class="list-unstyled footer-menu pull-right xs-pull-left">
    					<li class="footer-menu-title"><a href="iot-courses"><b>IoT Courses</b></a></li>
                                            <?php foreach($GLOBALS["iot_courses"]['courses'] as $iot_course){
                                                        $slug = json_decode($iot_course['content'], true);
                                                        $slug = $slug['slug'];
                                                    if( $iot_course['status']=='upcoming'){ ?>
                                                        <li><a href="#" data-toggle="modal" data-target="#leadmodal" data-course-name="<?php echo $iot_course['name']; ?>" data-course-id="<?php echo $iot_course['course_id']?>"> <?php echo $iot_course['name']; ?><span style="color:#fe7f27;"> (Coming Soon)</span></a></li>
                                                    <?php }
                                                    else
                                                    { ?>
                                                        <li><a  href="<?php echo $slug; ?>"><?php echo $iot_course['name']; ?></a></li>
                                                    <?php }

                                             } ?>
    					<!-- <li><a href="iot-beginners-course">IoT for Beginners</a></li>
    					<li><a href="iot-using-arduino">IoT from the Ground Up – Using Arduino</a></li>
    					<li><a href="iot-using-raspberry-pi">Powering the IoT – Using Raspberry Pi</a></li>
    					<li><a href="iot-cloud">IoT and the Cloud</a></li>
            				<li><span style="color: #6d6e71; font-size: 15px; font-family: 'Lato', sans-serif;">Introduction to IoT Analytics <span style="color: #f99d1c">(Coming Soon)</span></span></li>
            				<li><span style="color: #6d6e71; font-size: 15px; font-family: 'Lato', sans-serif;">Data Science for IoT <span style="color: #f99d1c">(Coming Soon)</span></span></li>
            				<li><span style="color: #6d6e71; font-size: 15px; font-family: 'Lato', sans-serif;">Advanced IoT Analytics <span style="color: #f99d1c">(Coming Soon)</span></span></li> -->
            				<li><a href="faqs" target="_blank">FAQs</a></li>
			</ul>
		</div>
		<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
			<div class="margin-30 hidden-lg hidden-md hidden-sm"></div>
                                <ul class="list-unstyled footer-menu pull-right xs-pull-left">
                                    <li class="footer-menu-title"><a href="#"><b>Company</b></a></li>
                                    <li><a href="https://www.jigsawacademy.com/about-us/" >About Jigsaw</a></li>
                                    <li><a href="https://www.jigsawacademy.com/contact/" >Contact Details</a></li>
                                    <li><a href="https://www.jigsawacademy.com/legal/" >Legal</a></li>
                                    <li><a href="https://www.jigsawacademy.com/terms-conditions/" >Terms</a></li>
                                    <li><a href="https://www.jigsawacademy.com/privacy-policy/" >Privacy Policy</a></li>
                                </ul>
                          </div>
                  </div>
</div>
</div>
<br/><br/><br/>

<!-- footer1 end -->

<!-- footer2 start -->
<div style='display:block;position: fixed;bottom:0px;left:0px;z-index:9999;background-color:white;border-top:1px solid #cdcdcd;width:100vw;'>
<div class="wrapper footer-padding" style='padding: 10px 0px;'>
	<div class="row">
        <!--<div class="col-lg-1 col-md-1 col-sm-1 col-xs-2"><a href="https://www.jigsawacademy.com/" ></a></div>-->
       <!--  <div class="col-lg-2 col-md-2 col-sm-3 col-xs-5 social" style="float: right; width: 50%;">
            <span style="float: right;">
                <a href="https://www.facebook.com/jigsawacademy/" target="_blank" class="hvr-flip">
                    <img src="<?php //echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/fb-icn.png">
                </a>
            </span>
            <span style="float: right; padding-right: 10px;">
                <a href="https://www.youtube.com/user/jigsawAcademy" target="_blank" class="hvr-flip">
                    <img src="<?php //echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/youtube-icn.png">
                </a>
            </span>
            <span style="float: right; padding-right: 10px;">
                <a href="https://www.linkedin.com/company/jigsaw-academy" target="_blank" class="hvr-flip">
                    <img src="<?php //echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/linkedin-icn.png">
                </a>
            </span>
            <span style="float: right; padding-right: 10px;">
                <a href="https://twitter.com/jigsawacademy" target="_blank" class="hvr-flip">
                    <img src="<?php //echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/twitter-icn.png">
                </a>
            </span>
        </div> -->
        <div class="col-lg-10 col-md-10 col-sm-9 col-xs-7" style="width: 100%; padding-right: 0px;">
            <a class="copyright" href="https://www.jigsawacademy.com"  style="font-family: 'Lato', sans-serif; float: left;">
                <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/footer-logo.png" style="padding-right: 13px; width: auto; height: 28px;" alt="IoT Footer logo"></a>
                 <a class="copyright" href="https://www.jigsawacademy.com"  style="font-family: 'Lato', sans-serif; float: left; position:relative; vertical-align:middle;">&copy; Jigsaw Academy Education Pvt Ltd</a>
            </div>
    </div>
</div>
</div>
<!-- footer2 end -->