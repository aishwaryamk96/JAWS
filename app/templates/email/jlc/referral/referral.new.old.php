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
                        <span style="color: white; font-size: 100%;"><?php echo $GLOBALS["content"]["emailer"]["referrer"]["name"]; ?> wants to boost your career</span>
                    </td>
                </tr>               

                <!-- SUBS ////////////////////////////////////////////////////////////////////////////////// -->
                
                <tr> 
                    <td width="800" bgcolor="#f4f6f6">  
                        <center>

                            <table border="0" cellpadding="0" cellspacing="15" bgcolor="#f4f6f6" color="#000000" style="border-color: #e2e4e4; border-style: solid; border-width: 0 0 1px 0;">
                                <tr height="20" bgcolor="#f4f6f6">
                                    <td>Hey <?php echo $GLOBALS["content"]["emailer"]["referred"]["name"]; ?>,</td>
                                </tr>
                                <tr height="20" bgcolor="#f4f6f6">
                                    <td colspan="2">Your friend <?php echo $GLOBALS["content"]["emailer"]["referrer"]["name"]; ?> is learning analytics with us and finds it a great career boost. <?php echo $GLOBALS["content"]["emailer"]["referrer"]["fname"]; ?> has recommended the courses, <?php echo $GLOBALS["content"]["emailer"]["courses_str"]; ?> for you as <?php echo ($GLOBALS["content"]["emailer"]["courses_count"] > 1 ? "these are" : "this is") ?> suitable for your profile.</td>
                                </tr>

                                <tr height="20" bgcolor="#f4f6f6">
                                    <td colspan="2">Enroll within the next 30 days using this coupon code <b><?php echo $GLOBALS["content"]["emailer"]["coupon_code"]; ?></b> and get a further discount of 5% on the courses.</td>
                                </tr>

                                <tr height="20" bgcolor="#f4f6f6">
                                    <td colspan="2">Jigsaw Academy, their specializations, career counselors and services are exactly of the standard that will support your career and take it places. If you'd like to explore other courses at Jigsaw, visit <a href="https://www.jigsawacademy.com/online-analytics-training/">Jigsaw Academy Courses page</a>.</td>
                                </tr>

                                <tr height="20" bgcolor="#f4f6f6">
                                    <td>Happy Learning,</td>
                                </tr>
                                <tr height="20" bgcolor="#f4f6f6">
                                    <td>Team Jigsaw</td>
                                </tr>

                                <tr height="245" bgcolor="#f4f6f6"> 
                                
                                <?php     
                                    $count = 1;
                                    foreach($GLOBALS["content"]["emailer"]["courses"] as $course) {  

                                    if (((($count - 1) % 3) == 0)  && ($count > 1)) {
                                        ?>
                                            </tr>
                                            <tr height="245" bgcolor="#f4f6f6">
                                        <?php
                                    }

                                ?>                                
    
                                    <td bgcolor="" width="50%" style="">
                                        <center>
                                            <table style="border: 1px solid #d8d9d9;" border="0" cellpadding="0" cellspacing="0">
                                                <tr height="122">
                                                    <td width="245">
                                                        <a href="<?php echo $course["url"]; ?>" class="img" style="text-decoration: none;"><img src="<?php echo $course["img"]; ?>" alt="" height="122" width="246" /></a>
                                                    </td>
                                                </tr>
                                                <tr height="123">
                                                    <td width="245" bgcolor="#ffffff">
                                                        <table style="" border="0" cellpadding="10" cellspacing="0">
                                                            <tr>
                                                                <td>
                                                                    <a href="<?php echo $course["url"]; ?>" class="crs" style="text-decoration: none;"><span style="font-size: 90%; color: #0c8dc9; text-transform: uppercase; line-height: 145%;"><?php echo $course["name"]; ?></span></a><br />
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>                                                            
                                                                    <span style="font-size: 75%; color: #787878;"><?php echo $course["desc"]; ?></span>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </center>
                                    </td>

                                    <?php 
                                            $count ++;

                                        }
                                    ?> 

                                </tr>
                            
                            </table>
                        </center>

                    </td>   
                </tr>
<?php /*
                <?php 

                if (intval($GLOBALS["content"]["emailer"]["payment"]["instl_total"]) > 1) { 

                ?>
                <!-- MORE TEXT ////////////////////////////////////////////////////////////////////////////// -->

                <tr> 
                    <td>        
                        <table border="0" cellpadding="30" cellspacing="0" style="width: 100%; border-color: transparent;" bgcolor="#f4f6f6" color="#000000">
                            <tr>                                
                                <td style="text-align: center;">
                                    <span style="color: #252525; font-size: 85%;">You have opted to finance your enrollment in installments. We would like to confirm your installment scheme as below. We will email you a reminder of your payment details a week before each due date.</span>
                                </td>
                            </tr>
                        </table>
                    </td>   
                </tr>

                <!-- INSTL //////////////////////////////////////////////////////////////////////////////// -->         

                <tr> 
                    <td width="800" bgcolor="#f4f6f6">  
                        <center>

                            <table border="0" cellpadding="0" cellspacing="15" bgcolor="#f4f6f6" color="#000000" style="border-color: #e2e4e4; border-style: solid; border-width: 0 0 1px 0;">
                                <tr height="180" bgcolor="#f4f6f6"> 

                                    <td bgcolor="#fafdfd" width="245">
                                        <table style="border: 1px solid #e0e1e1;" border="0" cellpadding="0" cellspacing="0">
                                            <tr height="100">
                                                <td width="245" style="text-align: center;">
                                                    <span style="font-size: 80%; color: #e1e4e4;">1<sup style="font-size: 70%;">st</sup> Installment</span><br />
                                                    <span style="font-size: 200%; color: #e1e4e4;"><?php echo ((strcmp(strtolower($GLOBALS["content"]["emailer"]["currency"]), "inr") == 0) ? '&#8377;' : '&#36;' ); ?></span>&nbsp;<span style="font-size: 275%; color: #bcbebe;"><?php echo number_format(intval($GLOBALS["content"]["emailer"]["sum"])); ?></span>
                                                </td>
                                            </tr>
                                            <tr height="80">
                                                <td width="245">
                                                    <table border="0" cellpadding="10" cellspacing="0" style="border-color: #e0e1e1; border-style: solid; border-width: 1px 0 0 0;">
                                                        <tr>
                                                            <td width="245" style="text-align: center;">
                                                                <span style="font-size: 150%; color: #bce3be; text-transform: capitalize; line-height: 200%;">&#x2714;&nbsp;Paid</span><br />
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>

                                    <?php 

                                    $count = 2;
                                    $instl_count_text_arr = array(
                                        1 => '1<sup style="font-size: 70%;">st</sup> Installment',
                                        2 => '2<sup style="font-size: 70%;">nd</sup> Installment',
                                        3 => '3<sup style="font-size: 70%;">rd</sup> Installment',
                                        4 => '4<sup style="font-size: 70%;">th</sup> Installment',
                                        5 => '5<sup style="font-size: 70%;">th</sup> Installment',
                                        6 => '6<sup style="font-size: 70%;">th</sup> Installment',
                                        7 => '7<sup style="font-size: 70%;">th</sup> Installment',
                                        8 => '8<sup style="font-size: 70%;">th</sup> Installment',
                                        9 => '9<sup style="font-size: 70%;">th</sup> Installment',
                                    );

                                    while($count <= intval($GLOBALS["content"]["emailer"]["payment"]["instl_total"])) {

                                        if ((($count - 1) % 3) == 0) {
                                        ?>
                                            </tr>
                                            <tr height="180" bgcolor="#f4f6f6">
                                        <?php
                                        }

                                        // Format the due date
                                        $due_date = $GLOBALS["content"]["emailer"]["payment"]["instl"][$count]["due_date"];
                                        $due_date = strtoupper(strval(date('M j-S, Y', strtotime($due_date))));
                                        $due_date_arr = explode("-", $due_date);
                                        $due_date_arr_sub = explode(",", $due_date_arr[1]);
                                        $due_date_arr_sub[0] = '<sup style="font-size: 60%; text-transform: none;">'.strtolower($due_date_arr_sub[0]).'</sup>';
                                        $due_date = $due_date_arr[0].$due_date_arr_sub[0].",".$due_date_arr_sub[1];

                                    ?>

                                    <td bgcolor="#ffffff" width="245" style="">
                                        <table style="border: 1px solid #e0e1e1;" border="0" cellpadding="0" cellspacing="0">
                                            <tr height="100">
                                                <td width="245" style="text-align: center;">
                                                    <span style="font-size: 80%; color: #808080;"><?php echo $instl_count_text_arr[$count]; ?></span><br />
                                                    <span style="font-size: 200%; color: #808080;">&#8377;</span>&nbsp;<span style="font-size: 275%; color: black"><?php echo number_format(intval($GLOBALS["content"]["emailer"]["payment"]["instl"][$count]["sum"])); ?></span>
                                                </td>
                                            </tr>
                                            <tr height="80">
                                                <td width="245">
                                                    <table border="0" cellpadding="10" cellspacing="0" style="border-color: #e0e1e1; border-style: solid; border-width: 1px 0 0 0;">
                                                        <tr>
                                                            <td width="245" style="text-align: center;">
                                                                <span style="font-size: 80%; color: #808080; text-transform: capitalize; line-height: 145%;">Due date</span><br />
                                                                <span style="font-size: 125%; color: black; line-height: 145%; text-transform: uppercase;"><?php echo $due_date; ?></span>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>

                                    <?php

                                        $count ++;

                                    }

                                    ?>

                                </tr>
                            </table>

                        </center>
                    </td>
                </tr>

                <?php } ?>

                <!-- MORE TEXT ////////////////////////////////////////////////////////////////////////////// -->

                <?php if ($GLOBALS["content"]["emailer"]["allow_setup"]) { ?>

                <tr> 
                    <td>        
                        <table border="0" cellpadding="30" cellspacing="0" style="width: 100%; border-color: transparent;" bgcolor="#f4f6f6" color="#000000">
                            <tr>                                
                                <td style="text-align: center;">
                                    <span style="color: #252525; font-size: 85%;">If by any chance you haven’t been able to set up your access to our Learning Center, please click the button below to finish the process. Make sure to use your <b>own</b> social ID to login.</span><br/><br /><br/>                               
                                    <center>
                                        <a bgcolor="#f59533" color="#ffffff" class="btn" style="color: white; text-decoration: none; text-transform: capitalize; font-size: 95%; text-align: center; margin: 0px auto; padding: 25px; display: block; min-width: 30%; max-width: 50%; width: 200px; border: 2px solid #FE761B; background-color: #FE761B; background: -webkit-linear-gradient(left, #FE761B 0%, #FF9326 56%, #FF9326 100%); background: -moz-linear-gradient(right, #FE761B 0%, #FF9326 56%, #FF9326 100%); background: -o-linear-gradient(right, #FE761B 0%, #FF9326 56%, #FF9326 100%); background: linear-gradient(to right, #FE761B 0%, #FF9326 56%, #FF9326 100%); border-radius: 0px; text-shadow: 0px 0px 8px black;" href="<?php echo JAWS_PATH_WEB.'/setupaccess?user='.$GLOBALS["content"]["emailer"]["user_webid"]; ?>">
                                            <span style="">Set up your access&nbsp;&nbsp;<b>&rarr;</b></span>
                                        </a>
                                    </center><br />
                                    <span style="color: #252525; font-size: 85%;">If you're unable to click on the button, please copy and paste the below link in your browser window.<br/><?php echo JAWS_PATH_WEB.'/setupaccess?user='.$GLOBALS["content"]["emailer"]["user_webid"]; ?><br/><br/>Please remember that we will be able to grant you access to our Learning Centre only after you've completed the setup! We request you to reach out to the Jigsaw support team in case you have any queries or require any assistance. Thank you!</span>
                                </td>
                            </tr>
                        </table>
                    </td>   
                </tr>  

                <?php } else { ?> 

                  <tr> 
                    <td>        
                        <table border="0" cellpadding="30" cellspacing="0" style="width: 100%; border-color: transparent;" bgcolor="#f4f6f6" color="#000000">
                            <tr>                                
                                <td style="text-align: center;">
                                    <span style="color: #252525; font-size: 85%;">
                                        Thank you for the payment. We require a little time (upto 24hrs) to get your course materials on the Learning Center ready. Keep checking your email for the access details - it should be with you very soon!<br /><br/> 
                                        If you’d like to get started though, we recommend reading up on the latest in the world of analytics and Big Data on the official Jigsaw blog at <a href="http://analyticstraining.com">AnalyticsTraining.com</a>.
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </td>   
                </tr>

                <?php } ?>
*/ ?>
                <!-- OUTRO ///////////////////////////////////////////////////////////////////////////////// -->

                <?php load_template("email", "common/front/footer"); ?>
        
            </table>
        </center>

    </body>
</html>
