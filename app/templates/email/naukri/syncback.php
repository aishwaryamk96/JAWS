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
						<table border="0" cellpadding="30" cellspacing="0" style="width: 100%; border-color: transparent;" bgcolor="<?php echo ($GLOBALS["content"]["emailer"]["severity"] ? "#ff0000" : "#0c8dc9") ?>" color="#ffffff">
							<tr>
								<td style="text-align: center;">
									<span style="color: white; font-size: 150%;">Leads received until now</span><br /><br />
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
									<span style="color: #252525; font-size: 85%;">
										Following are the leads that have been received since the last sync:<br/>
										<?php foreach ($GLOBALS["content"]["emailer"]["leads"] as $lead) {
											echo $lead["name"]." - ".$lead["email"]." @ ".$lead["create_date"]."<br />";
										} ?>
									</span>
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