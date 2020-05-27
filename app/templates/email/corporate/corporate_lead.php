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
					Hello,
				</td>
			</tr>
			<tr>
				<td style="font-size: 12px;	text-align: left; padding: 7px;">
					Below are the corporate lead details:
				</td>
			</tr>
			<tr>
				<td style="font-size: 12px;	text-align: left; padding: 7px;">
					<b>Name : </b><?php echo $template_content['name']; ?><br/>
					<b>Email : </b> <?php echo $template_content['email']; ?><br/>
					<b>Phone : </b> <?php echo $template_content['phone']; ?><br/>
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
