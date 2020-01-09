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
					Congratulations! You are now set up to access the <b><?= $template_content["bundle_name"]; ?> program by <b>Manipal Academy of Higher Education</b> and <b>Jigsaw Academy</b> via the Jigsaw Learning Center (JLC)
				</td>
			</tr>
			<tr>
				<td style="font-size: 12px;	text-align: left; padding: 7px;">
					<b>Accessing your account on EduNxt:</b>
				</td>
			</tr>
			<tr>
				<td style="font-size: 12px;	text-align: left; padding: 7px;">
					All course content is accessed via the link: <a href="https://jigsaw.manipalprolearn.com">(https://jigsaw.manipalprolearn.com)</a>. We recommend bookmarking this link.
				</td>
			</tr>
			<tr>
				<td style="font-size: 12px;	text-align: left; padding: 7px;">
					Please use the following login credentials:
				</td>
			</tr>
			<tr>
				<td style="font-size: 12px;	text-align: left; padding: 7px;">
					<b>Username : </b><?php echo $template_content['email']; ?><br/>
					<b>Password : </b> <?php echo $template_content['password']; ?><br/>
				</td>
			</tr>
			<tr>
				<td style="font-size: 12px;text-align: left; padding: 7px; color: #222222;"><b>Please note:</b> The user id and password are case sensitive.</td>
			</tr>
			<tr>
				<td style="font-size: 12px;	text-align: left; padding: 7px;">
					Your access to the EduNxt course content is for the entire duration of the program. We are pleased to offer an additional 12 months of access after the program is over. Your access end date is <?= $end_date; ?>.
				</td>
			</tr>
			<tr>
				<td style="font-size: 12px;text-align: left; padding: 7px; color: #222222;"><b>Support:</b></td>
			</tr>
			<tr>
				<td style="font-size: 12px;text-align: left; padding: 7px; color: #222222;">We are happy to help! Please use the "Support" tab on the EduNxt and select "PGCPCC - All Queries" to raise a ticket for any help required.</td>
			</tr>
			<tr>
				<td style="font-size: 12px;text-align: left; padding: 7px; color: #222222;"><b>Next Steps:</b></td>
			</tr>
			<tr>
				<td style="font-size: 12px;text-align: left; padding: 7px; color: #222222;">
					1. Check your EduNxt access and confirm that you have access to the program.<br/>
					2. Review the attached schedule and make note of all the class dates.
				</td>
			</tr>
		</tbody>
		<tfoot>
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
		</tfoot>
	</table>
	</td>
   </tr>
  </tbody>
 </table>
</div>
</body>
</html>
