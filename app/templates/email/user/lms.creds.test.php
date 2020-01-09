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

		<style>
			a.btn:hover { background-color: #FF9326!important; }
			a.btn:active { background-color: #BE4600!important; }	
		</style>

	</head>	
	<body>

		<center>
			<table width="800" border="0" cellpadding="0" cellspacing="0" style="font-family: verdana; font-size:100%; margin: 5px auto; max-width: 800px; padding: 0; width: 100vw; border: none;">
			
				<!-- HEADER AND LOGO //////////////////////////////////////////////////////////////////////// -->

				<?php load_template("email","common/front/header"); ?>

				<!-- SUBJECT AND INTRO ////////////////////////////////////////////////////////////////////// -->

				<tr> 
					<td>		
						<table border="0" cellpadding="10" cellspacing="0" style="width: 100%; border-color: transparent;" bgcolor="#0c8dc9" color="#ffffff">
							<tr>								
								<td style="text-align: center;">
									<span style="color: white; font-size: 120%;">Your Account is Ready!</span><br />
								</td>
							</tr>
						</table>
					</td>	
				</tr>

				<!-- MORE TEXT ////////////////////////////////////////////////////////////////////////////// -->				

				<tr> 
					<td>		
						<table border="0" cellpadding="30" cellspacing="0" style="width: 100%; border-color: transparent;" bgcolor="#f4f6f6" color="#000000">
							<tr>								
								<td style="text-align: left;">
									<span style="color: #252525; font-size: 95%; line-height: 150%;">
									Hi [first-name],<br/>
									Congratulations! You are now set up to access ­[course_name] course at Jigsaw Academy.<br/><br/>
										Please use the same social login that you selected while setting up your access.<br/>
To help you get started we have attached a file containing all your login guidelines. <br/><br/>

After logging in, if you need any further assistance please feel free to visit the ‘Support’ tab in your Jigsaw Learning Center account.  <br/>
An important reminder: Our Learning Centre will host your access to the course for 6 months. Your access gateway is secure and will be available till <b>December 20, 2016</b>, after which it will lapse.<br/><br/>
 
 Visit www.jigsawacademy.net, log into your account and start learning.<br/><br/>
 
Your Lab Access Details:<br/>
Lab user ID - [XXXX]<br/>
Password -    [XXXX]<br/>
IP address -  [XXXX]<br/><br/>
 
Please Note: The user ID and password are case sensitive. Your access to the Jigsaw Lab will be activated within 24 hours.<br/><br/>
 
Happy Learning!<br/>
Regards,<br/>
Jigsaw Support Team
									</span>
								</td>
							</tr>
						</table>
					</td>	
				</tr>	

				<!-- OUTRO ///////////////////////////////////////////////////////////////////////////////// -->

				<?php //load_template("email","common/front/footer"); ?>
		
			</table>
		</center>

	</body>
</html>