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

                <?php load_template("email","common/front/header-ipba"); ?>

                <tr>
                    <td>
                        <table border="0" cellpadding="30" cellspacing="0" style="width: 100%; border-color: transparent;" bgcolor="#0c8dc9" color="#ffffff">
                            <tr>
                                <td style="text-align: center;">
                                    <span style="color: white; font-size: 150%;">Application Received</span><br /><br />
                                    <span style="color: #DAEEF7; font-size: 85%;">Thank you for your application to IIM Indore's Integrated Program in Business Analytics (IPBA)</span><br/><br/>
                                    <?php if(!empty($GLOBALS["content"]["emailer"]["application_number"])){ ?>
                                    <span style="color: #DAEEF7; font-size: 85%;">Your Application Number : <b><?php echo $GLOBALS["content"]["emailer"]["application_number"]; ?></b> </span>
                                    <?php } ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td>
                        <table border="0" cellpadding="30" cellspacing="0" style="width: 100%; border-color: transparent;" bgcolor="#f4f6f6" color="#000000">
                            <tr>
                                <td style="text-align: left;">
                                    <span style="color: #252525; font-size: 85%;">Hi <?php echo $GLOBALS["content"]["emailer"]["fname"]; ?>!<br/><br/>Thank you for your application to the Integrated Program in Business Analytics (IPBA) with IIM Indore and Jigsaw Academy! We have received your payment.<br/><br/>You will be assigned a dedicated admissions counsellor. The counsellor will get in touch with you regarding the status of your application and on admission updates. <br/><br/>Best wishes,<br/>IPBA Admissions<br/><br/><b>Phone:</b> +91 90192 17000 (9AM – 8PM, Monday to Saturday)<br/><b>Email:</b> <a href='mailto:ipba@iimidr.ac.in'>ipba@iimidr.ac.in</a>
									</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td>
                        <table border="0" cellpadding="30" cellspacing="0" style="width: 100%; border-color: transparent;" bgcolor="#0c8dc9" color="#ffffff">
                            <tr>
                                <td style="text-align: center;">
                                    <span style="font-size: 70%; color: #A9D7EC; line-height: 200%; text-transform: uppercase;">Contact</span><br />
                                    <span style="font-size: 80%; color: #F3F9FC;"><?php echo $GLOBALS['content']['footer']['phone'] ?? '+91-90192-17000'; ?></span>
                                </td>
                                <td style="text-align: center;">
                                    <span style="font-size: 70%; color: #A9D7EC; line-height: 200%; text-transform: uppercase;">Email</span><br />
                                    <a style="text-decoration: none;" href="mailto:ipba@iimidr.ac.in?Subject=Regarding%20My%20Enrollment" target="_top"><span style="font-size: 80%; color: #F3F9FC;">ipba@iimidr.ac.in</span></a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

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
