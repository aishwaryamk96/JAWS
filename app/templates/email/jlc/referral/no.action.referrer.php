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
<?php  $date = date("Y-m-d"); ?>
        <center>
            <table width="800" border="0" cellpadding="0" cellspacing="0" style="font-family: verdana; font-size:100%; margin: 5px auto; max-width: 800px; padding: 0; width: 100vw; border: none;">
            
                <!-- HEADER AND LOGO //////////////////////////////////////////////////////////////////////// -->

                <?php load_template("email","common/front/header"); ?>

                <!-- SUBJECT AND INTRO ////////////////////////////////////////////////////////////////////// -->

                <tr>
                    <td style="text-align: center; padding: 12px" bgcolor="#009cd9">
                        <span style="color: white; font-size: 100%;">Can't wait to give you Amazon voucher</span>
                    </td>
                </tr>               

                <!-- SUBS ////////////////////////////////////////////////////////////////////////////////// -->
                
                <tr> 
                    <td width="800" bgcolor="#f4f6f6">  
                        <center>

                            <table border="0" cellpadding="0" cellspacing="15" bgcolor="#f4f6f6" color="#000000" style="border-color: #e2e4e4; border-style: solid; border-width: 0 0 1px 0;">
                           
                                <tr height="20" bgcolor="#f4f6f6">
                                    <td>Dear <?php echo $GLOBALS["content"]["emailer"]["content"][0]["referrer_name"]; ?>,</td>
                                </tr>
                                 <?php if(count($GLOBALS["content"]["emailer"]["content"])> 1 ) {
                                            $referral_name = array();
                                            foreach($GLOBALS["content"]["emailer"]["content"] as $referral){
                                                     $referral_name[] = $referral["referral_name"];
                                                     $invite_date =  $referral["date"];
                                            } 
                                                $last  = array_slice($referral_name, -1);
                                                $first = join(', ', array_slice($referral_name, 0, -1));
                                                $both  = array_filter(array_merge(array($first), $last), 'strlen');
                                ?>

                                <tr height="20" bgcolor="#f4f6f6">
                                    <td colspan="2">You had recommended Jigsaw courses to <b><?php  echo join(' and ', $both); ?></b> on <b><?php echo date("d M Y", strtotime($invite_date)); ?></b>. Your friends have not yet enrolled. Your reward of Amazon voucher worth INR 1000 for each friend is waiting to be sent.</td>
                                </tr>

                                <tr height="10" bgcolor="#f4f6f6">
                                    <td colspan="2">You can connect with your friends and find out if any help is required. Our career counsellors will be glad to assist.</td>
                                </tr>
                                <?php } else { ?>
                                    <tr height="20" bgcolor="#f4f6f6">
                                        <td colspan="2">You had recommended Jigsaw courses to <?php echo $GLOBALS["content"]["emailer"]["content"][0]["referral_name"] ?> on <?php echo date("d M Y", strtotime($GLOBALS["content"]["emailer"]["content"][0]["date"])); ?>. Your friend has not yet enrolled. Your reward of Amazon voucher worth INR 1000 is waiting to be sent.
                                        </td>
                                    </tr>

                                    <tr height="10" bgcolor="#f4f6f6">
                                        <td colspan="2">You can connect with your friend and find out if any help is required. Our career counsellors will be glad to assist.</td>
                                    </tr>
                                <?php } ?>
                                <tr height="20" bgcolor="#f4f6f6">
                                    <td>Thanks</td>
                                </tr>
                                <tr height="10" bgcolor="#f4f6f6">
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
