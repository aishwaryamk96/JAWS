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
						<table border="0" cellpadding="30" cellspacing="0" style="width: 100%; border-color: transparent;" bgcolor="#0c8dc9" color="#ffffff">
							<tr>								
								<td style="text-align: center;">
									<span style="color: white; font-size: 150%;">Setup your access</span><br /><br />
									<span style="color: #DAEEF7; font-size: 85%;">Hi <?php echo $GLOBALS["content"]["emailer"]["fname"]; ?>! Your course materials are waiting!</span>
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
								<td style="text-align: center;">
									<span style="color: #252525; font-size: 85%;">We wanted to gently remind you that you have not set-up your access to our Learning Center yet. This process is necessary for you to get started with your course materials! And it takes just a couple of minutes of your time.</span><br/><br /><br/>								
									<center>
										<a bgcolor="#f59533" color="#ffffff" class="btn" style="color: white; text-decoration: none; text-transform: capitalize; font-size: 95%; text-align: center; margin: 0px auto; padding: 25px; display: block; min-width: 30%; max-width: 50%; width: 200px; border: 2px solid #FE761B; background-color: #FE761B; background: -webkit-linear-gradient(left, #FE761B 0%, #FF9326 56%, #FF9326 100%); background: -moz-linear-gradient(right, #FE761B 0%, #FF9326 56%, #FF9326 100%); background: -o-linear-gradient(right, #FE761B 0%, #FF9326 56%, #FF9326 100%); background: linear-gradient(to right, #FE761B 0%, #FF9326 56%, #FF9326 100%); border-radius: 0px; text-shadow: 0px 0px 8px black;" href="<?php echo JAWS_PATH_WEB.'/setupaccess?user='.$GLOBALS["content"]["emailer"]["user_webid"]; ?>">

											<span style="">Set up your access now&nbsp;&nbsp;<b>&rarr;</b></span>
										</a>
									</center><br />
									<span style="color: #252525; font-size: 85%;">Do remember that we will be able to grant you access to our Learning Centre only after you've completed the setup! Please reach out to the Jigsaw support team in case you have any queries or require any assistance. We are eager to help you!</span>
								</td>
							</tr>
						</table>
					</td>	
				</tr>	

				<!-- OUTRO ///////////////////////////////////////////////////////////////////////////////// -->

				<?php load_template("email","common/front/footer"); ?>
		
			</table>
		</center>

	</body>
</html>