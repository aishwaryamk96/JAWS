<?php $template_content = $GLOBALS["content"]['emailer']; ?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo ($template_content['title'] ?? "") ?></title>
</head>
<body style="font-family: Verdana, Helvetica, Arial; font-size: 12px; color: #000000;background: rgb(250, 250, 250) none repeat scroll 0% 0%">
<!--[if mso]>
<style type="text/css">
body, table, td {font-family: Verdana, Helvetica, Arial !important;}
</style>
<![endif]-->
<div style="width: auto; background: #FAFAFA;">
 <table width="100%">
  <tbody>
   <tr>
    <td>
	<a href="https://www.jigsawacademy.com" title="Jigsaw Academy">
		<img src="https://www.jigsawacademy.com/wp-content/themes/jigsaw/images/jigsaw_horizontal_logo.png" alt="Jigsaw Academy" style="margin: 10px; border: none;" />
	</a>
	<table width="100%">
		<thead>
		<tr>
			<td style="padding: 7px;">
				<h2 style="background:#0676EE; padding: 8px;; color: #FFFFFF; text-align: center;font-size: 15pt;">Your Account is Ready!</h2>
			</td>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td style="font-size: 12px; text-align: left; padding: 7px; color: #222222;">
				Hello <?php echo $template_content['name']; ?>,
			</td>
		</tr>
		<tr>
			<td style="font-size: 12px; text-align: left; padding: 7px; color: #222222;">
				Congratulations! You are now set up to access the<?php echo (!empty($template_content["bundle_name"]) ? "&nbsp;<b>".$template_content["bundle_name"]."</b> ".($template_content["full_stack"] != 0 ? "program" : "specialization")." which includes" : "") ?> following <?php echo ($template_content["full_stack"] != 0 ? "modules" : "course(s)") ?> via the Jigsaw Learning Center (JLC):
			</td>
		</tr>
		<tr>
		<td style="font-size: 12px;	text-align: left; padding: 7px;">
			<table cellspacing="0" cellpadding="0" style="font-size: 12px;font-family: Verdana, Helvetica, Arial;">
			<?php foreach ($template_content["enr"] as $course) { ?>
				<tr>
					<td width="20" align="center" valign="top">&bull;</td>
					<td align="left" valign="top"><?php echo $course["name"] . /*($course["learn_mode"] == "Catalyst" ? "" : " (" . $course["learn_mode"] . ")").*/($course["complimentary"] ? " (Complimentary)" : ""); ?></td>
				</tr>
			<?php } ?>
			</table>
		<!--
		<table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD;">
			<thead>
			<tr>
				<td width="50%" style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: center; padding: 7px; color: #222222;">Course</td>
				<td width="50%" style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: center; padding: 7px; color: #222222;">Mode</td>
			</tr>
			</thead>
			<tbody width="100%">
			<?php //foreach( $template_content["enr"] as $course){ ?>
			<tr>
				<td width="50%" style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: center; padding: 7px;"><?php //echo $course["name"]; ?>Data Science with SAS</td>
				<td width="50%" style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: center; padding: 7px;"><?php //echo $course["learn_mode"]; ?>Regular</td>
			</tr>
			<?php //} ?>
			</tbody>
		</table> -->
		</td>
		</tr>
		<tr>
			<td style="font-size: 12px;	text-align: left; padding: 7px;">
				<?php if (count($template_content["enr"]) > 1 ) { ?> We recommend that following the given sequence of <?php echo ($template_content["full_stack"] != 0 ? "modules" : "courses") ?> will help you in easy understanding of the concepts.<?php } ?> Your access to the course content will be for <?php echo $template_content["duration"] ?> months and will be available till <b><?php echo $template_content['end_date']; ?></b>.
			</td>
		</tr>
		<?php if ($template_content["is_bootcamp"]) { ?>
			<tr>
				<td style="font-size: 12px;	text-align: left; padding: 7px;">
					Your training schedule will be for 2.5 months, starting from <?php echo $template_content["bootcamp_batch"] ?>.
				</td>
			</tr>
		<?php } ?>
		<tr>
			<td style="font-size: 12px;	text-align: left; padding: 7px;">
				<b>Accessing your account on the JLC:</b>
			</td>
		</tr>
		<tr>
			<td style="font-size: 12px;	text-align: left; padding: 7px;">
				All course content is accessed via this link <a href="https://www.jigsawacademy.net"><b>(www.jigsawacademy.net)</b></a>. We recommend bookmarking this link.
			</td>
		</tr>
		<tr>
			<td style="font-size: 12px;	text-align: left; padding: 7px;">
				Please use <?php echo empty($template_content["lms_pass"]) ? "the same social login selected while setting up your access" : "your registerred email ID as username and ".$template_content["lms_pass"]." as password to login"; ?>. To help you get started we have attached a file containing the login guidelines.
			</td>
		</tr>
		<?php if (count($template_content["lab"]) > 0 && $template_content["full_stack"] != 76) { ?>
			<tr>
				<td style="font-size: 12px;	text-align: left; padding: 7px;">
					<b>Lab access details</b>
				</td>
			</tr>
			<?php if (!$template_content["suppress_lab_info"]) { ?>
				<tr>
					<td style="font-size: 12px;	text-align: left; padding: 7px;">
						<b>Lab User ID : </b><?php echo $template_content['lab_user']; ?><br/>
						<b>Password : </b> <?php echo $template_content['lab_pass']; ?><br/>
						<?php if ($template_content["full_stack"] == 68 || $template_content["full_stack"] == 70 || $template_content["full_stack"] == 72) {
							echo 'Please go to "Jigsaw Lab" tab in JLC and click on "LAB" button and follow the instructions.';
						} ?>
					</td>
				</tr>
				<?php if ($template_content["full_stack"] == 0) {
					foreach ($template_content["lab"] as $ip => $lab) {
						$lab = implode(", ", $lab); ?>
						<tr>
							<td style="font-size: 12px;	text-align: left; padding: 7px;">
								<?php echo "<b>".$lab.":</b> ".($ip == "dataserver1.jigsawacademy.in" ? "Please go to \"Jigsaw Lab\" tab in JLC and click on \"LAB\" button and follow the instructions." : $ip); ?>
							</td>
						</tr>
					<?php }
				} ?>
				<tr>
					<td style="font-size: 12px;text-align: left; padding: 7px; color: #222222;"><b>Please note:</b> The user id and password are case sensitive. Your access to the Jigsaw Lab will be activated within 24 hours.</td>
				</tr>
			<?php }
			else { ?>
				<tr>
					<td>We encourage you to download required tools (R, Python and Tableau) in your laptop. The data set and code for practice as well as graded assignments will be shared with you. You may work on them from your own device.</td>
				</tr>
				<tr>
					<td>
						For R, download the latest version of R tool from: https://cran.r-project.org/bin/windows/base/<br>as well as R studio (Open Source License) from: https://www.rstudio.com/products/rstudio/download/
					</td>
				</tr>
				<tr>
					<td>For Python, please download the latest version from: https://www.anaconda.com/download/</td>
				</tr>
				<tr>
					<td>For Tableau, please download Tableau Public fom https://public.tableau.com/en-us/s/</td>
				</tr>
				<tr>
					<td>We will share access to the Jigsaw Lab a week before the capstone project starts. Details will be shared with you over email.</td>
				</tr>
			<?php }
		} ?>
		<tr>
			<td style="font-size: 12px;text-align: left; padding: 7px; color: #222222;"><b>Support:</b></td>
		</tr>
		<tr>
			<?php if (!$template_content["is_bootcamp"]) { ?>
				<td style="font-size: 12px;text-align: left; padding: 7px; color: #222222;">We are happy to help! Please use the "Support" tab on the JLC to raise a ticket for any help required.</td>
			<?php }
			else { ?>
				<td style="font-size: 12px;text-align: left; padding: 7px; color: #222222;">We are happy to help! Please raise all your queries once your classroom sessions start.</td>
			<?php } ?>
		</tr>
		<?php if (!$template_content["full_stack"]) { ?>
		<tr>
			<td style="font-size: 12px;text-align: left; padding: 7px; color: #222222;"><b>Kick-off Session: </b></td>
		</tr>
		<tr>
			<td style="font-size: 12px;text-align: left; padding: 7px; color: #222222;">Invite to New Student Course Kick-Off Session-Excellent opportunity for you to understand the complete gamut of resources at your disposal to make full utilization of the course. Please check calendar in JLC for the class link.</td>
		</tr>
		<?php } ?>
		<?php if (!empty($template_content["iot_nokit"])) { ?>
			<tr>
				<td style="font-size: 12px;text-align: left; padding: 7px; color: #222222;"><b>IOT hardware kit: </b></td>
			</tr>
			<tr>
				<td style="font-size: 12px;text-align: left; padding: 7px; color: #222222;">You have opted to enroll into the <?php echo $template_content["iot_nokit_bundle"] ?> without the hardware kit. Hence, please find attached the list of components that are required to complete the course practicals, we suggest you to buy these components.</td>
			</tr>
		<?php } ?>
		<?php if (!empty($template_content["iot_nokit_usd"])) { ?>
			<tr>
				<td style="font-size: 12px;text-align: left; padding: 7px; color: #222222;"><b>IOT hardware kit: </b></td>
			</tr>
			<tr>
				<td style="font-size: 12px;text-align: left; padding: 7px; color: #222222;">You have enrolled from a country other than India hence your fees does not include the IoT hardware kit. Please find attached the list of components that are required to complete the course practicals, we suggest you to buy these components.</td>
			</tr>
		<?php } ?>
		</tbody>
		<tfoot>
			<?php if ($template_content["instl_total"] > 1) { ?>
				<tr>
					<td style="font-size: 12px;	text-align: left; padding: 7px;">
						You have opted to finance your enrollment in installments so you will have a partial access to the learning material. We will provide complete access after full payment is done.
					</td>
				</tr>
			<?php } ?>
			<tr>
				<td style="font-size: 12px;	text-align: left; padding: 7px;">
					<br/>
					<br/>
					Happy Learning!
					<br/>
					<br/>
					Regards,
					<br/>
					Jigsaw Team
				</td>
			</tr>
			<tr>
				<td style="font-size: 12px;	text-align: left; padding: 7px;">
					P.S. You can find your program calendar, for planning your class schedules, in JLC Calendar section.
				</td>
			</tr>
		</tfoot>
	</table>
	</td>
   </tr>
  </tbody>
 </table>
</div>
</body>
</html>
