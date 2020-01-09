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
                    <td style="text-align: center; padding: 12px" bgcolor="#009cd9">
                        <span style="color: white; font-size: 100%;">New referral from <?php echo $GLOBALS["content"]["emailer"]["name"]; ?>!</span>
                    </td>
                </tr>

                <!-- SUBS ////////////////////////////////////////////////////////////////////////////////// -->

                <tr>
                    <td width="800" bgcolor="#f4f6f6">
                        <center>

                            <table border="0" cellpadding="0" cellspacing="15" bgcolor="#f4f6f6" color="#000000" style="border-color: #e2e4e4; border-style: solid; border-width: 0 0 1px 0;">
                                <tr height="20" bgcolor="#f4f6f6">
                                    <td>Hi,</td>
                                </tr>
                                <tr height="20" bgcolor="#f4f6f6">
                                    <td colspan="2">Glad to tell you that our student <?= $GLOBALS["content"]["emailer"]["name"]; ?> (<?= $GLOBALS["content"]["emailer"]["email"]; ?>) has referred his/her friend <?= $GLOBALS["content"]["emailer"]["refer"]["name"]; ?>, <?= $GLOBALS["content"]["emailer"]["refer"]["email"]; ?>, <?= $GLOBALS["content"]["emailer"]["refer"]["phone"]; ?> through <b><?= $GLOBALS["content"]["emailer"]["source"]; ?></b> and has recommended <?= $GLOBALS["content"]["emailer"]["refered"]; ?>.</td>
                                </tr>

                                <tr height="20" bgcolor="#f4f6f6">
                                    <td colspan="2">You can contact the referred person to consult and help select the right set of courses.</td>
                                </tr>

                                <tr height="20" bgcolor="#f4f6f6">
                                    <td>Thank you,</td>
                                </tr>
                                <tr height="20" bgcolor="#f4f6f6">
                                    <td>Jigsaw Referral</td>
                                </tr>

                            </table>
                        </center>

                    </td>
                </tr>

                <!-- OUTRO ///////////////////////////////////////////////////////////////////////////////// -->

                <tr>
                    <td>
                        <table border="0" cellpadding="30" cellspacing="0" style="width: 100%; border-color: transparent;" bgcolor="#0c8dc9" color="#ffffff">
                            <tr>
                                <td style="text-align: center;">
                                    <span style="font-size: 70%; color: #A9D7EC; line-height: 200%; text-transform: uppercase;">Contact</span><br />
                                    <span style="font-size: 80%; color: #F3F9FC;"><?php echo $GLOBALS['content']['emailer']['pgpdm'] ? "+91-90192-17000" : '+91-90192-17000'; ?></span>
                                </td>
                                <td style="text-align: center;">
                                    <span style="font-size: 70%; color: #A9D7EC; line-height: 200%; text-transform: uppercase;">Email</span><br />
                                    <a style="text-decoration: none;" href="mailto:<?php echo $GLOBALS['content']['emailer']['pgpdm'] ? "pgpdm" : "support" ?>@jigsawacademy.com?Subject=Regarding%20My%20Enrollment" target="_top"><span style="font-size: 80%; color: #F3F9FC;"><?php echo $GLOBALS['content']['emailer']['pgpdm'] ? "pgpdm" : "support" ?>@jigsawacademy.com</span></a>
                                </td>
                                <td style="text-align: center;">
                                    <span style="font-size: 70%; color: #A9D7EC; line-height: 200%; text-transform: uppercase;"><?php echo $GLOBALS['content']['emailer']['pgpdm'] ? "Website" : "Facebook" ?></span><br />
                                    <a style="text-decoration: none;" href="<?php echo $GLOBALS['content']['emailer']['pgpdm'] ? "https://www.jigsawacademy.com/pgpdm" : "https://www.facebook.com/jigsawacademy" ?>"><span style="font-size: 80%; color: #F3F9FC;"><?php echo $GLOBALS['content']['emailer']['pgpdm'] ? "https://www.jigsawacademy.com/pgpdm" : "fb.com/jigsawacademy" ?></span></a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- FOOTER /////////////////////////////////////////////////////////////////////////////// -->

                <tr>
                    <td>
                        <table border="0" cellpadding="10" cellspacing="0" style="width: 100%; border-color: transparent;" bgcolor="#ffffff" color="#000000">
                            <tr>
                                <td style="text-align: left;">
                                    <span style="color: #989898; font-size: 55%;">This email is confidential and is intended to be read by the recipient only.</span>
                                </td>
                                <td style="text-align: right;">
                                    <span style="color: #989898; font-size: 55%;">&copy;&nbsp;Jigsaw Academy Education Pvt. Ltd.</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

            </table>
        </center>

    </body>
</html>
