<?php

	// Proceed with rendering the UI
	ui_render_head_front(array(
		"title" => ("Free Learning - Jigsaw Academy"),
		//"scripts" => array(1 => "app/templates/jaws/frontend/modal.js"),
		"styles" => array(1 => "app/templates/jaws/frontend/modal.css")
	));

?>

<!-- Google Code for Conversion Page -->
<script type="text/javascript">
   /* <![CDATA[ */
   var google_conversion_id = 987804683;
   var google_conversion_language = "en";
   var google_conversion_format = "3";
   var google_conversion_color = "ffffff";
   var google_conversion_label = "oV-KCLmRklgQi-iC1wM";
   var google_remarketing_only = false;
   /* ]]> */
</script><script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js"></script>
<noscript><div style="display:inline;"><img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/987804683/?label=oV-KCLmRklgQi-iC1wM&amp;guid=ON&amp;script=0"/></div></noscript>
<!-- Google Code for Conversion Page ends here -->

<script type='text/javascript'>
	var wcount=0,ready=false,perma_fail=false;

	$(document).ready(function(){

		// Init
		var qcurr=1,ecurr=1,qtol=$('div.wait-quote').length;
		$('div.wait-quote:first-child').addClass('active');

		// Wait Quotes
		var t_q = setInterval(function() {
			if (qcurr == qtol ) qcurr = 1;
			else qcurr++;

			$('div.wait-quote').removeClass('active');
			if (ready || perma_fail) {
				try {
					clearInterval(t_q);
					clearInterval(t_e);
				} catch(r) {}

				$('div.page.wait').removeClass('active');
				if (ready) $('div.page.success').addClass('active');
				else $('div.page.fail').addClass('active');
				if (!perma_fail) $('#btn-freejlc').addClass('active');
				$('.progress').css('width','100%');
				$('.progress').addClass('ready');

				// Redirect to JLC in 5 Seconds
				setTimeout(function(){
					//window.location.href = $('#txt-jlc-free-url').val();
				}, 10000);
			}
			else {
				$('div.wait-quote:nth-child('+(qcurr)+')').addClass('active');
				$('.progress').css('width',(wcount * 1.5)+'%');
				poll();
			}
		}, 7500);

		// Wait Elipsis
		var t_e = setInterval(function() {
			if (wcount > 100) $('#wait-longer').html('This is taking longer than expected');
			else wcount++;

			ecurr++;
			if (ecurr>3) ecurr = 1;

			var txt;
			if (ecurr == 1) txt = 'Please Wait';
			else if (ecurr == 2) txt = 'Please Wait.';
			else txt = 'Please Wait..';

			$('#wait-ellipsis').html(txt);
		}, 500);

		// API Poll for status
		function poll() {
			$.ajax({
				url: $('#txt-jaws-status-url').val(),
				type: 'POST',
				data: {}
			})
			.done(function(data) {
				var d = jQuery.parseJSON(data);
				if (d.status === true) ready
			});
		}

		// Goto Free JLC
		$('#btn-freejlc').click(function() {
			window.location.href = $('#txt-jlc-free-url').val();
		});

	});
</script>

<div id="bkg-img"> </div>
<div id="bkg-overlay"> </div>

<div class="modal">

	<div class="page bkg active wait">
		<div class="header" id='wait-longer'>Setting up your free account</div>
		<div class='sub-header' id='wait-ellipsis'>Please Wait</div>

		<div class="text">

			<style scoped>
				div.wait-quote-container {
					display: block;
					position: relative;
					top: 50px;
				}

				div.wait-quote-container > div.wait-quote {
					display: block;
					position: absolute;
					opacity: 0;
					top: 50px;

					-webkit-transition: opacity 0.75s ease-in, top 0.75s ease-in;
					-moz-transition: opacity 0.75s ease-in, top 0.75s ease-in;
					-o-transition: opacity 0.75s ease-in, top 0.75s ease-in;
					-ms-transition: opacity 0.75s ease-in, top 0.75s ease-in;
					transition: opacity 0.75s ease-in, top 0.75s ease-in;
				}

				div.wait-quote-container > div.wait-quote.active {
					opacity: 1;
					top: 0px;
				}

				div.wait-quote-container > div.wait-quote > i.fa {
					position: relative;
					margin:  0 10px;
				}
				div.wait-quote-container > div.wait-quote > i.fa-quote-left { top: -15px; }
				div.wait-quote-container > div.wait-quote > i.fa-quote-right { top: 10px; }

				div.wait-quote-container > div.wait-quote > span.wait-quote-text {
					line-height: 150%;
				}

				div.wait-quote-container > div.wait-quote > span.wait-quote-person {
					position: relative;

					font-family: 'Montserrat', sans-serif;
					font-size: 15px;
					font-weight:  bold;
					opacity: 0.85;
					text-transform: uppercase;
				}

				div.wait-quote-container > div.wait-quote > span.wait-quote-job {
					position: relative;
					top: -5px;

					font-family: 'Montserrat', sans-serif;
					font-size: 12px;
					opacity: 0.6;
					text-transform: capitalize;
				}


			</style>

			<div class='wait-quote-container'>

			<?php if (in_array($course, [48])) { ?>

				<div class='wait-quote'>
					<i class='fa fa-quote-left fa-fw fa-lg'></i><span class='wait-quote-text'>IoT will be bigger than anything that's ever been done in high tech. It will change the way people live, work and play.</span><i class='fa fa-quote-right fa-fw fa-lg'></i><br/><br/>
					<span class='wait-quote-person'>John Chambers</span><br/>
					<span class='wait-quote-job'>CEO, Cisco Systems Inc.</span>
				</div>

				<div class='wait-quote'>
					<i class='fa fa-quote-left fa-fw fa-lg'></i><span class='wait-quote-text'>If you think that the internet has changed your life, think again. The IoT is about to change it all over again!</span><i class='fa fa-quote-right fa-fw fa-lg'></i><br/><br/>
					<span class='wait-quote-person'>Brendan O’Brien</span><br/>
					<span class='wait-quote-job'>Co-Founder, Aria Systems</span>
				</div>

				<div class='wait-quote'>
					<i class='fa fa-quote-left fa-fw fa-lg'></i><span class='wait-quote-text'>Do not put your faith in what statistics say, until you have carefully considered what they do not say.</span><i class='fa fa-quote-right fa-fw fa-lg'></i><br/><br/>
					<span class='wait-quote-person'>William W Watt</span>
				</div>

			<?php } else { ?>

				<div class='wait-quote'>
					<i class='fa fa-quote-left fa-fw fa-lg'></i><span class='wait-quote-text'>The world is now awash in data and we can see consumers in a lot clearer ways.</span><i class='fa fa-quote-right fa-fw fa-lg'></i><br/><br/>
					<span class='wait-quote-person'>Max Levchin</span><br/>
					<span class='wait-quote-job'>PayPal ​C​o-founder</span>
				</div>

				<div class='wait-quote'>
					<i class='fa fa-quote-left fa-fw fa-lg'></i><span class='wait-quote-text'>Data is a precious thing and will last longer than the systems themselves.</span><i class='fa fa-quote-right fa-fw fa-lg'></i><br/><br/>
					<span class='wait-quote-person'>Tim Berners-Lee</span><br/>
					<span class='wait-quote-job'>Inventor of the World Wide Web</span>
				</div>

				<div class='wait-quote'>
					<i class='fa fa-quote-left fa-fw fa-lg'></i><span class='wait-quote-text'>Data really powers everything that we do.</span><i class='fa fa-quote-right fa-fw fa-lg'></i><br/><br/>
					<span class='wait-quote-person'>Jeff Weiner</span><br/>
					<span class='wait-quote-job'>CEO LinkedIn</span>
				</div>

				<div class='wait-quote'>
					<i class='fa fa-quote-left fa-fw fa-lg'></i><span class='wait-quote-text'>The most valuable commodity I know of is information.</span><i class='fa fa-quote-right fa-fw fa-lg'></i><br/><br/>
					<span class='wait-quote-person'>Gordon Gekko</span><br/>
					<span class='wait-quote-job'>Wall Street</span>
				</div>

				<div class='wait-quote'>
					<i class='fa fa-quote-left fa-fw fa-lg'></i><span class='wait-quote-text'>Making analytics cool is, to a certain extent, enabled by having cool people working with it.</span><i class='fa fa-quote-right fa-fw fa-lg'></i><br/><br/>
					<span class='wait-quote-person'>David Rhee</span><br/>
					<span class='wait-quote-job'>Adidas Group</span>
				</div>


			<?php } ?>

			</div>
		</div>
	</div>

	<div class="page bkg success">
		<div class="header">You're All Set</div>
		<div class="sub-header">Success</div>

		<div class="text">
			<?php echo ($regged === false ? "Your free account is ready." : "You already have an account with us."); ?> Here are your login credentials:<br/><br/>

			URL: <a href='https://freelearning.jigsawacademy.net/login/free_trial?token=<?php echo $psk; ?>&email=<?php echo urlencode($email) ?>&course=<?php echo $course_info["sis_id"] ?>'>freelearning.jigsawacademy.net</a><br/>
			<?php if (!isset($_REQUEST['corp'])) { ?>
				Social Sign-On: <?php echo ($_REQUEST["soc"] == 'fb' ? 'Facebook' : ($_REQUEST["soc"] == 'gp' ? 'Google+' : 'LinkedIn')); ?><br/>
			<?php } else { ?>
				Email Id: <?php echo $_POST['email']; ?><br/>
				Password: **** (Please check your E-mail) <?php //echo $_POST['password']; ?><br/>
			<?php } ?>

			<br/><br/>
			We have emailed you a copy of the above (Remember to check your spam folder also). Click 'Take me there now' to go to your free account.

		</div>
	</div>

	<div class="page bkg fail">
		<div class="header">Something went wrong :(</div>
		<div class="sub-header">Oops</div>

		<div class="text">
			We were unable to create your free account due to some error. Please write to us at <a href="mailto:support@jigsawacademy.com">support@jigsawacademy.com</a>.<br/><br/>Sorry for the inconvenience.
		</div>
	</div>

	<div class="nav">
		<div class="panel left">
			<div style='display:none' class="link-button active" id="btn-iot">Back to website</div>
		</div>

		<div class="panel right" >
			<div class="link-button" id="btn-freejlc">Take me there now</div>
		</div>
	</div>

	<div class="progress" style='max-width: 100%; width: 10%'>
		<div class="RL"></div>
	</div>

</div>

<div style="visibility: hidden; display: none;">
	<input type="hidden" value="https://www.jigsawacademy.com/jaws/lmsapi/free/setup.status?token=<?php echo $psk; ?>" id="txt-jaws-status-url" style="visibility: hidden; display: none;" />
	<input type="hidden" value="https://freelearning.jigsawacademy.net/login/free_trial?token=<?php echo $psk; ?>&email=<?php echo urlencode($email)."&course=".$course_info["sis_id"]; ?>" id="txt-jlc-free-url" style="visibility: hidden; display: none;" />
</div>