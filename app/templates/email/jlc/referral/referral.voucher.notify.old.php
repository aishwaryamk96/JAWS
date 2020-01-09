<?php 
https://www.facebook.com/sharer.php?s=100&p[title]=Baap+of+Big+Data&p[url]=https%3A%2F%2Fwww.facebook.com%2Fjigsawacademy&
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
                        <span style="color: white; font-size: 100%;">Confirmation on your Amazon Voucher</span>
                    </td>
                </tr>               

                <!-- SUBS ////////////////////////////////////////////////////////////////////////////////// -->
                
                <tr> 
                    <td width="800" bgcolor="#f4f6f6">  
                        <center>

                            <table border="0" cellpadding="0" cellspacing="15" bgcolor="#f4f6f6" color="#000000" style="border-color: #e2e4e4; border-style: solid; border-width: 0 0 1px 0;">
                                <tr height="20" bgcolor="#f4f6f6">
                                    <td>Hey <?php echo $GLOBALS["content"]["emailer"]["referrer_name"]; ?>,</td>
                                </tr>
                                <tr height="20" bgcolor="#f4f6f6">
                                    <td colspan="2">Congratulations on winning the AMAZON voucher worth INR 1000!</td>
                                </tr>

                                <tr height="20" bgcolor="#f4f6f6">
                                    <td colspan="2">Amazon voucher details have been mailed to you on your registered email id with us. Hope you have received it. Please check your spam and promotions folder as well.</td>
                                </tr>

                               <tr height="20" bgcolor="#f4f6f6">
                                    <td colspan="2">This voucher is valid for 365 days. So, happy shopping!</td>
                                </tr>

                                <tr height="20" bgcolor="#f4f6f6">
                                    <td colspan="2">It would be great if you can share your experience on JLC forum discussion and encourage your peers.</td>
                                </tr>

                                <tr height="20" bgcolor="#f4f6f6">
                                    <td>Regards,</td>
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
