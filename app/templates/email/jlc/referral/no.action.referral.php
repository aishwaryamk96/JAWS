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
                        <span style="color: white; font-size: 100%;">Hurry up! Coupon shared by <?php echo $GLOBALS["content"]["emailer"]["referrer_name"]; ?> is about to expire </span>
                    </td>
                </tr>               

                <!-- SUBS ////////////////////////////////////////////////////////////////////////////////// -->
                
                <tr> 
                    <td width="800" bgcolor="#f4f6f6">  
                        <center>

                            <table border="0" cellpadding="0" cellspacing="15" bgcolor="#f4f6f6" color="#000000" style="border-color: #e2e4e4; border-style: solid; border-width: 0 0 1px 0;">
                                <tr height="20" bgcolor="#f4f6f6">
                                    <td>Dear <?php echo $GLOBALS["content"]["emailer"]["referral_name"]?>,</td>
                                </tr>
                                <tr height="20" bgcolor="#f4f6f6">
                                    <td colspan="2">Your friend <?php echo $GLOBALS["content"]["emailer"]["referrer_name"]; ?> had recommended Jigsaw Academy courses to you on <?php echo date("d M Y", strtotime($GLOBALS["content"]["emailer"]["date"])); ?>. Enroll soon to avail an exclusive 5% discount. Your coupon code <?php echo $GLOBALS["content"]["emailer"]["coupon_code"]; ?> is valid only for next 5 days.</td>
                                </tr>

                                <tr height="20" bgcolor="#f4f6f6">
                                    <td colspan="2">To get assistance, call a career counsellor now @ +91 92435-22277 for Analytics courses or +91 90193-17000 for courses on Internet of Things.</td>
                                </tr>

                                <tr height="10" bgcolor="#f4f6f6">
                                    <td>Happy Learning,</td>
                                </tr>
                                <tr height="20" bgcolor="#f4f6f6">
                                    <td>Team Jigsaw</td>
                                </tr>
                            </table>
                        </center>

                    </td>   
                </tr>

                <!-- OUTRO ///////////////////////////////////////////////////////////////////////////////// -->

                <?php load_template("email", "common/front/footer"); ?>
        
            </table>
        </center>

    </body>
</html>
