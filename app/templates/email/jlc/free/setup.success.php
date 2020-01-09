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
									<span style="color: white; font-size: 150%;">Welcome to the Jigsaw Learning Center</span><br /><br />
									<span style="color: #DAEEF7; font-size: 95%;">Hi <?php echo $GLOBALS["content"]["emailer"]["fname"]; ?>! Your FREE account is ready!</span>
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
									<span style="color: #252525; font-size: 95%;">
										You are now set up to access the FREE <?= $GLOBALS['content']['emailer']['course']["category"] == "iot" ? "IoT" : "Analytics"; ?> course on the Jigsaw Learning Center!<br />
										Your course access is limited to <?php echo $GLOBALS['content']['emailer']['access']['duration']; ?> days and will expire on <b><?php echo $GLOBALS['content']['emailer']['access']['end_date']; ?></b>.<br/><br/>
										Visit <a href='https://freelearning.jigsawacademy.net/'>​FreeLearning.​JigsawAcademy.net</a>, ​and ​<?php
											echo ($GLOBALS['content']['emailer']['access']['account']['mode'] == 'corp') ? 'use the following log in details to get started:' : 'use the '.$GLOBALS['content']['emailer']['access']['account']['provider'].' social login to access your course materials.';
											if ($GLOBALS['content']['emailer']['access']['account']['mode'] == 'corp') {
												?>
													<br/><br/>
													Username: <b><?php echo $GLOBALS['content']['emailer']['access']['account']['username']; ?></b><br/>
													Password: <b><?php echo $GLOBALS['content']['emailer']['access']['account']['password']; ?></b><br/>
												<?php
											}
										?>
										<br/>
										After logging in, for any further assistance​,​ please visit the '​Help &amp; ​Support' tab in your Jigsaw Learning Center account.<br/><br/>
										A copy of the terms and conditions pertaining to your course material and access to the Jigsaw Learning Center is attached with this email for your reference. Please do reach out to us if you have any questions.<br/><br/>
										Happy Learning!<br/><br />
										Regards,<br/>
										Team Jigsaw
									</span>
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<!-- OUTRO ///////////////////////////////////////////////////////////////////////////////// -->

				<?php load_template("email","jlc/free/footer"); ?>

			</table>
		</center>
		

	</body>
</html>