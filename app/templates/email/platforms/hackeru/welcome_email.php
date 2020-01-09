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
		<img src="https://www.jigsawacademy.com/emailer/images/cyber-header-mailer.png" alt="Jigsaw Academy" style="margin: 10px; border: none;" />
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
					Congratulations! You are now set up to access the <b><?= $template_content["bundle_name"]; ?> by HackerU, a Premier Cyber Training Institute of Israel Powered by Jigsaw Academy</b> via the Hackampus
				</td>
			</tr>
			<tr>
				<td style="font-size: 12px;	text-align: left; padding: 7px;">
					<b>Accessing your account on the Hackampus.com:</b>
				</td>
			</tr>
			<tr>
				<td style="font-size: 12px;	text-align: left; padding: 7px;">
					All course content is accessed via this link <a href="https://www.hackampus.com/"><b>(www.hackampus.com)</b></a>. We recommend bookmarking this link.
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
				<td style="font-size: 12px;text-align: left; padding: 7px; color: #222222;">Your access to the HackerU course content is for the entire duration of the phase you study. Your access end date is <b>same as your course end date.</b></td>
			</tr>
			<tr>
				<td style="font-size: 12px;text-align: left; padding: 7px; color: #222222;"><b>Important Note:</b></td>
			</tr>
			<tr>
				<td style="font-size: 12px;text-align: left; padding: 7px; color: #222222;">
					Access to Hackampus for Phase 1 & Phase 2 (Cyber Security 101- Foundation Phase) will be available for all, until the end of the sorting phase.<br/>
					Students who successfully qualify the Sorting Phase will continue to get access to Hackampus till the end of the main program.
				</td>
			</tr>
			<tr>
				<td style="font-size: 12px;text-align: left; padding: 7px; color: #222222;"><b>Support:</b></td>
			</tr>
			<tr>
				<td style="font-size: 12px;text-align: left; padding: 7px; color: #222222;">In case of any issues, we are happy to help! Please write your query on the <a href="mailto:support@hackampus.com?cc=RedTeamIndiaSupport@HackerU.com"><b>“Support@hackampus.com”</b></a> with a copy (CC) to <a href="mailto:support@hackampus.com?cc=RedTeamIndiaSupport@HackerU.com"><b>RedTeamIndiaSupport@HackerU.com</b></a> to raise a ticket for any help required.</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td style="font-size: 12px;	text-align: left; padding: 7px;">
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
