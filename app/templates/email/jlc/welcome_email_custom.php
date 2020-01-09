<?php $template_content = $GLOBALS["content"]['emailer']; ?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo $template_content['title']; ?></title>
</head>
<body style="font-family: Verdana, Helvetica, Arial; font-size: 12px; color: #000000;background: rgb(250, 250, 250) none repeat scroll 0% 0%; margin: 0 auto;width: 100%; max-width: 700px;">
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
				<h2 style="background:#0676EE; padding: 15px;; color: #FFFFFF; text-align: center;font-size: 12pt;font-weight: normal;">Your Account is Ready!</h2>
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
				You are now set up to access the Data Science Bootcamp at Jigsaw Academy.
			</td>
		</tr>
    	<tr>
            <td style="font-size: 12px;	text-align: left; padding: 7px;">
                <b>Your account details:</b>
            </td>
        </tr>
        <tr>
            <td style="font-size: 12px;	text-align: left; padding: 7px;">
                URL: https://www.jigsawacademy.net<br/>
                JLC User ID : <?php echo $template_content['lab_user']; ?><br/>
                Password : <?php echo $template_content['lab_pass']; ?>
            </td>
        </tr>
		<tr>
			<td style="font-size: 12px;text-align: left; padding: 7px; color: #222222;"><b>Note: Please click on  "Corporate user? Click here" at the bottom of the screen to login.</b></td>
		</tr>
		<tr>
            <td style="font-size: 12px;text-align: left; padding: 7px; color: #222222;">Be sure to check out the attached Getting Started Guide for tips on how to get the most out of JLC.</td>
		</tr>
		<tr>
			<td style="font-size: 12px;text-align: left; padding: 7px; color: #222222;">Ready with your first course? Get started with your learning right now.</td>
		</tr>
		</tbody>
		<tfoot>
			<tr>
				<td style="font-size: 12px;	text-align: left; padding: 7px;">
					<br/>
					<br/>
					Regards,
					<br/>
					Jigsaw Corporate Support
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
