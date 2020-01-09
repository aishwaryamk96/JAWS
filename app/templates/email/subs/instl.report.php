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

    JIGSAW ACADEMY WORKFLOW SYSTEM v1                   
    ---------------------------------
*/
    
    // Prevent exclusive access
    if (!defined("JAWS")) {
        header('Location: https://www.jigsawacademy.com');
        die();
    }

?>

<html>
	<head>
		<title>Jigsaw Academy - Installment Payment Report</title>

		<style>
			a:hover { background-color: rgba(225,110,10,1)!important; }
			a:active { background-color: rgba(190,70,0,1)!important; }		
		</style>

	</head>	
	<body>

		<center>
			<table border="1" cellpadding="20" cellspacing="0" style="font-family: verdana; font-size:90%; margin: 5px auto; max-width: 1000px; padding: 0px; width: 100vw; border-color: transparent;">
			
				<tr style="border-width: 1px 1px 0px 1px; border-color: rgba(0,0,0,0.1);"> 
					<td bgcolor="#f3f3f3" color="#ffffff">		
						<center>
							<img align="middle" style="display: block; margin: 0 auto;" src='https://www.jigsawacademy.com/wp-content/themes/jigsaw/images/jigsaw_horizontal_logo.png' alt=""/>
						</center>
					</td>	
				</tr>

				<tr style="background-color: rgba(0,160,220,1); color: white; text-transform: uppercase; text-align: center;">
					<td bgcolor="#00A0DC">
						Installment Payment Report
					</td>	
				</tr>

				<tr style="border-width: 0 1px 0 1px; border-color: rgba(0,0,0,0.1);">
					<td>
						Concerned,
						<br/><br/>
								
						This is an automated pending installments report generated on <?php echo strtoupper(strval(date('M jS, Y'))); ?>.<br/><br/>

						The following installments are to be paid within the next two days.<br/><br/>
				
						<center>
							<table border="0" cellpadding="10" cellspacing="2" style="font-size: 90%; font-family: verdana; background-color: white; width: 75%; margin: 20px auto;">

								<tr>
									<td bgcolor='#f3f3f3'>Name</td>
									<td bgcolor='#f3f3f3'>Phone</td>
									<td bgcolor='#f3f3f3'>Email</td>
									<td bgcolor='#f3f3f3'>Course(s)</td>
									<td bgcolor='#f3f3f3'>Installment</td>
									<td bgcolor='#f3f3f3'>Amount</td>
								</tr>

								<?php
									foreach($GLOBALS["content"]["emailer"]["2"] as $enr) echo "<tr><td bgcolor='#f3f3f3'>".$enr["name"]."</td><td bgcolor='#f3f3f3'>".$enr["phone"]."</td><td bgcolor='#f3f3f3'>".$enr["email"]."</td><td bgcolor='#f3f3f3'>".$enr["coursestr"]."</td><td bgcolor='#f3f3f3'>".$enr["instl_count"]." / ".$enr["instl_total"]."</td><td bgcolor='#f3f3f3'><a href='https://www.jigsawacademy.com/jaws/pay?pay=".$enr["paylink_id"]."' target='_blank' title='Pay Link'>".((strcmp(strtolower($enr["currency"]), "inr") == 0) ? '&#8377;' : '&#36;' )." ".$enr["sum"]."</a></td></tr>";
								?>

							</table>
						</center>
						<br/>
				
						The following installments are to be paid by today.<br/><br/>
				
						<center>
							<table border="0" cellpadding="10" cellspacing="2" style="font-size: 90%; font-family: verdana; background-color: white; width: 75%; margin: 20px auto;">

								<tr>
									<td bgcolor='#f3f3f3'>Name</td>
									<td bgcolor='#f3f3f3'>Phone</td>
									<td bgcolor='#f3f3f3'>Email</td>
									<td bgcolor='#f3f3f3'>Course(s)</td>
									<td bgcolor='#f3f3f3'>Installment</td>
									<td bgcolor='#f3f3f3'>Amount</td>
								</tr>

								<?php
									foreach($GLOBALS["content"]["emailer"]["0"] as $enr) echo "<tr><td bgcolor='#f3f3f3'>".$enr["name"]."</td><td bgcolor='#f3f3f3'>".$enr["phone"]."</td><td bgcolor='#f3f3f3'>".$enr["email"]."</td><td bgcolor='#f3f3f3'>".$enr["coursestr"]."</td><td bgcolor='#f3f3f3'>".$enr["instl_count"]." / ".$enr["instl_total"]."</td><td bgcolor='#f3f3f3'><a href='https://www.jigsawacademy.com/jaws/pay?pay=".$enr["paylink_id"]."' target='_blank' title='Pay Link'>".((strcmp(strtolower($enr["currency"]), "inr") == 0) ? '&#8377;' : '&#36;' )." ".$enr["sum"]."</a></td></tr>";
								?>

							</table>
						</center>
						<br/>
					
						The last dates for the following installments had lapsed two days back. Rules require that access to LMS and Lab be blocked for these enrollments. Payment link will however remain active for the next 20 Days.<br/><br/>
				
						<center>
							<table border="0" cellpadding="10" cellspacing="2" style="font-size: 90%; font-family: verdana; background-color: white; width: 75%; margin: 20px auto;">

								<tr>
									<td bgcolor='#f3f3f3'>Name</td>
									<td bgcolor='#f3f3f3'>Phone</td>
									<td bgcolor='#f3f3f3'>Email</td>
									<td bgcolor='#f3f3f3'>Course(s)</td>
									<td bgcolor='#f3f3f3'>Installment</td>
									<td bgcolor='#f3f3f3'>Amount</td>
								</tr>

								<?php
									foreach($GLOBALS["content"]["emailer"]["-2"] as $enr) echo "<tr><td bgcolor='#f3f3f3'>".$enr["name"]."</td><td bgcolor='#f3f3f3'>".$enr["phone"]."</td><td bgcolor='#f3f3f3'>".$enr["email"]."</td><td bgcolor='#f3f3f3'>".$enr["coursestr"]."</td><td bgcolor='#f3f3f3'>".$enr["instl_count"]." / ".$enr["instl_total"]."</td><td bgcolor='#f3f3f3'><a href='https://www.jigsawacademy.com/jaws/pay?pay=".$enr["paylink_id"]."' target='_blank' title='Pay Link'>".((strcmp(strtolower($enr["currency"]), "inr") == 0) ? '&#8377;' : '&#36;' )." ".$enr["sum"]."</a></td></tr>";
								?>

							</table>
						</center>
						<br/>
								
						Regards,<br/>
						JAWS CRM
					</td>	
				</tr>
	
				<tr style="background-color: rgba(0,160,220,1)!important; color: white; text-transform: uppercase; text-align: center;">
					<td bgcolor="#00A0DC" color="#ffffff">
						<center>
							This email is automatically generated - please check the validity of the information contained.
						</center>
					</td>	
				</tr>
		
			</table>
		</center>

	</body>
</html>