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
	load_module("ui");

	// Prep
	$course_name = 'Machine Learning in Python';
	$course_price = 'INR 2,950';
	$course_price_usd = 'USD 40';

	// Following block disabled as page is for single course only !
	/*if (strpos(strtolower($_SERVER['REQUEST_URI']), 'dsb-fsas') !== false) {
		$course_name = 'Data Science in R and Data Visualization';
		$course_price = '28,750 INR';
	}*/

	// Proceed with rendering the UI
	ui_render_head_front(array(
		"title" => "Nokia",
		"scripts" => ["app/templates/jaws/frontend/nokia.js"],
		"styles" => [
			"app/templates/jaws/frontend/modal.css",
			"app/templates/jaws/frontend/wns.css"
		]
	));

?>
		<div id="bkg-img"> </div>
		<div id="bkg-overlay"> </div>

		<div id="wns-form" class="border">
			<!-- <div style='height: 82px; width: 106px; background-image: url("https://www.jigsawacademy.com/jaws/media/jaws/frontend/images/jigsaw-logo.jpg"); vertical-align: middle; position: absolute; right: 60px;'></div> -->
			<div class="header">Jigsaw Academy <span class="pull-right"><img src="https://www.jigsawacademy.com/jaws/media/jaws/frontend/images/jigsaw-logo.jpg" alt="Jigsaw Academy"></span></div>
			<div class="sub-header"><?php echo $course_name; ?></div>
			<div class="intro">
				Please use the link given below to pay for your one month extension to the course.
			</div>
		<br/>
			<div class="title"><span>Personal Info</span></div>
			<div id="details" class="border">

				<div class="panel">
					<label for="txt-name">Full Name</label>
					<input type="text" id="txt-name" name="name" class="field" value="" placeholder="Firstname Lastname" tabindex="1"/>
					<label class="error" for="txt-name"></label>

					<!--<label for="txt-emp-id">Employee ID</label>-->
					<input type="hidden" id="txt-emp-id" name="emp-id" class="field" value="NIL" tabindex="5" />
					<!--<label class="error" for="txt-emp-id"></label>-->

					<!-- <label for="txt-country">Country</label>
					<input type="text" id="txt-country" name="country" class="field" value="" tabindex="7"/>
					<label class="error" for="txt-country"></label> -->

				</div>

				<div class="panel">
					<label for="txt-email" title="Email ID must be under the domain nokia.com"><i class="fa fa-info-circle fa-lg" title="Email ID must be under the domain nokia.com"></i>Official Email Id</label>
					<input type="email" id="txt-email" name="email" class="field" value="" placeholder="email@nokia.com" tabindex="3"/>
					<label class="error" for="txt-email"></label>

					<!-- <label for="txt-phone">Phone</label>
					<input type="tel" id="txt-phone" name="phone" class="field" value="" tabindex="2" />
					<label class="error" for="txt-phone"></label>

					<label for="txt-email-alt">Alternate Email</label>
					<input type="email-alt" id="txt-email-alt" name="email" class="field" value="" placeholder="email@example.com" tabindex="4"/>
					<label class="error" for="txt-email-alt"></label> -->

					<!--<label for="txt-office">Office Location</label>-->
					<input type="hidden" id="txt-office" name="office" class="field" value="NIL" tabindex="6" />
					<!--<label class="error" for="txt-office"></label>-->

					<!-- <label for="txt-emp-city">City</label>
					<input type="text" id="txt-city" name="city" class="field" value="" tabindex="8" />
					<label class="error" for="txt-city"></label> -->
				</div>

			<div class="title"><span>Payment Information</span></div>
			<div id="paymode" class="border">
					<br/>
					<form>
						<input type="radio" name="opt-mode" value="inr" id="iopt-inr" checked="checked"/><label for="iopt-inr" selected><span><i class="fa fa-fw fa-lg fa-check"></i></span><?php echo $course_price; ?> (Incl. of all taxes)</label><br/>
						<!-- <input type="radio" name="opt-mode" value="usd" id="iopt-usd" /><label for="iopt-usd" selected><span><i class="fa fa-fw fa-lg fa-check"></i></span><?php echo $course_price_usd; ?> (Incl. of all taxes)</label><br/> -->
			</form>
			</div>

			<br/><br/>
				<div class="title"><span>Terms &amp; Conditions</span></div><br/>
				<form>
					<input type="checkbox" name="chk-tc" value="tc" id="ichk-tc"/><label for="ichk-tc"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;I agree to Jigsaw Academy's <a href="https://www.jigsawacademy.com/terms-conditions/" target="_blank" tabindex="7">Terms &amp; Conditions</a>.</label><br/>

					<input type="checkbox" name="chk-pp" value="pp" id="ichk-pp" checked/><label for="ichk-pp"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;I agree to Jigsaw Academy's <a href="https://www.jigsawacademy.com/privacy-policy/" target="_blank" tabindex="8">Privacy Policy</a>.</label><br/>
				</form>

			</div>

			<center>
			<a class="button skewed" id="btn-dsb" style="margin-bottom: -7px;" tabindex="9">
				<span class="button-main-text">Proceed to Payment</span>
				<span class="button-main-arrow-image">
					<img class="image-icon" src="<?php echo JAWS_PATH_WEB.'/media/jaws/frontend/images/long-arrow-orange.png'; ?>">
				</span>
			</a>
			</center>

		</div>

		<div style="visibility: hidden; display: none;">
			<input type="hidden" value="<?php echo JAWS_PATH_WEB; ?>" id="txt-jaws-url" style="visibility: hidden; display: none;" />
			<!-- <input type="hidden" value="vdsr" id="txt-course-code" style="visibility: hidden; display: none;" /> -->
		</div>

	</body>
</html>