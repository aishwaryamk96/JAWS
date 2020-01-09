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
		header('Location: ../index.php');
		die();
	}

?>

   <html>
    <head>
        <title>Jigsaw Academy - New Enrollment</title>

        <style>
            a.btn:hover { background-color: #FF9326!important; }
            a.btn:active { background-color: #BE4600!important; }   
        </style>

    </head> 
    <body>

        <center>
            <table width="800" border="0" cellpadding="0" cellspacing="0" style="font-family: verdana; font-size:100%; margin: 5px auto; max-width: 800px; padding: 0; width: 100vw; border: none;">
            
                <!-- HEADER AND LOGO //////////////////////////////////////////////////////////////////////// -->

                <tr> 
                    <td bgcolor="#f4f6f6" color="#FFFFFF">      
                        <table border="0" cellpadding="0" cellspacing="0" style="width: 100%; border: none;">
                            <tr height="200">
                                <td width="120"><img align="right" style="display: block; margin: 0;" src="<?php echo JAWS_PATH_WEB.'/media/jaws/frontend/images/favicon.png'; ?>" alt="" width="75" height="75"/></td>
                                <td width="400" style="padding-left: 25px; font-size: 150%; text-transform: uppercase;">Jigsaw Academy<br><span style="font-size: 61%; color: #969696; text-transform: capitalize;">The Online School of Anaytics</span></td>
                                <td width="300"><img align="right" style="display: block; margin: 0;" src="<?php echo JAWS_PATH_WEB.'/media/jaws/frontend/images/gradhat.png'; ?>" alt="" width="280" height="150"/></td>
                            </tr>
                        </table>
                    </td>   
                </tr>

                <!-- SUBJECT AND INTRO ////////////////////////////////////////////////////////////////////// -->

                <tr> 
                    <td>        
                        <table border="0" cellpadding="30" cellspacing="0" style="width: 100%; border-color: transparent;" bgcolor="#0c8dc9" color="#ffffff">
                            <tr>                                
                                <td style="text-align: center;">
                                    <span style="color: white; font-size: 150%;">Welcome to Jigsaw Academy</span><br /><br />
                                    <span style="color: #DAEEF7; font-size: 85%;">Congratulations on your enrolment! We have recieved your payment of &#8377;25,000. You are now successfully enrolled in the following courses</span>
                                </td>
                            </tr>
                        </table>
                    </td>   
                </tr>               

                <!-- SUBS ////////////////////////////////////////////////////////////////////////////////// -->
                
                <tr> 
                    <td width="800" bgcolor="#f4f6f6">  
                        <center>

                            <table border="0" cellpadding="0" cellspacing="15" bgcolor="#f4f6f6" color="#000000" style="border-color: #e2e4e4; border-style: solid; border-width: 0 0 1px 0;">
                                <tr height="245" bgcolor="#f4f6f6"> 
    
                                    <td bgcolor="#ffffff" width="245" style="">
                                        <table style="border: 1px solid #d8d9d9;" border="0" cellpadding="0" cellspacing="0">
                                            <tr height="122">
                                                <td width="245">
                                                    <a href="#" class="img" style="text-decoration: none;"><img src="<?php echo JAWS_PATH_WEB.'/media/jaws/frontend/images/course-1.jpg'; ?>" alt="" height="122" width="246" /></a>
                                                </td>
                                            </tr>
                                            <tr height="123">
                                                <td width="245">
                                                    <table style="" border="0" cellpadding="10" cellspacing="0">
                                                        <tr>
                                                            <td>
                                                                <a href="" class="crs" style="text-decoration: none;"><span style="font-size: 90%; color: #0c8dc9; text-transform: uppercase; line-height: 145%;">Analytics for Beginners</span></a><br />
                                                                <span style="font-size: 55%; color: #989898; line-height: 145%; text-transform: uppercase;">Normal</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>                                                            
                                                                <span style="font-size: 75%; color: #787878;">Get an insight of the field that's defining business strategy.</span>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>

                                    <td bgcolor="#ffffff" width="245" style="">
                                        <table style="border: 1px solid #d8d9d9;" border="0" cellpadding="0" cellspacing="0">
                                            <tr height="122">
                                                <td width="245">
                                                    <a href="#" class="img" style="text-decoration: none;"><img src="<?php echo JAWS_PATH_WEB.'/media/jaws/frontend/images/course-2.jpg'; ?>" alt="" height="122" width="246" /></a>
                                                </td>
                                            </tr>
                                            <tr height="123">
                                                <td width="245">
                                                    <table style="" border="0" cellpadding="10" cellspacing="0">
                                                        <tr>
                                                            <td>
                                                                <a href="" class="crs" style="text-decoration: none;"><span style="font-size: 90%; color: #0c8dc9; text-transform: uppercase; line-height: 145%;">Big Data for Beginners</span></a><br />
                                                                <span style="font-size: 55%; color: #FE761B; line-height: 145%; text-transform: uppercase;">Premium</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>                                                            
                                                                <span style="font-size: 75%; color: #787878;">Get an insight of the field that's defining business strategy.</span>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>

                                    <td bgcolor="#ffffff" width="245" style="">
                                        <table style="border: 1px solid #d8d9d9;" border="0" cellpadding="0" cellspacing="0">
                                            <tr height="122">
                                                <td width="245">
                                                    <a href="#" class="img" style="text-decoration: none;"><img src="<?php echo JAWS_PATH_WEB.'/media/jaws/frontend/images/course-3.jpg'; ?>" alt="" height="122" width="246" /></a>
                                                </td>
                                            </tr>
                                            <tr height="123">
                                                <td width="245">
                                                    <table style="" border="0" cellpadding="10" cellspacing="0">
                                                        <tr>
                                                            <td>
                                                                <a href="" class="crs" style="text-decoration: none;"><span style="font-size: 90%; color: #0c8dc9; text-transform: uppercase; line-height: 145%;">Analytics for Leaders</span></a><br />
                                                                <span style="font-size: 55%; color: #FE761B; line-height: 145%; text-transform: uppercase;">Premium</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>                                                            
                                                                <span style="font-size: 75%; color: #787878;">Get an insight of the field that's defining business strategy.</span>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
    
                                    
    
    
    
                                </tr>
                            
                            </table>
                        </center>

                    </td>   
                </tr>

                <!-- MORE TEXT ////////////////////////////////////////////////////////////////////////////// -->

                <tr> 
                    <td>        
                        <table border="0" cellpadding="30" cellspacing="0" style="width: 100%; border-color: transparent;" bgcolor="#f4f6f6" color="#000000">
                            <tr>                                
                                <td style="text-align: center;">
                                    <span style="color: #252525; font-size: 85%;">You have opted to finance your enrolment in installments. We would like to confirm your installment scheme as below. We will email you the link to make your payment a week before each due date.</span>
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

                                    <td bgcolor="#fafdfd" width="245" style="">
                                        <table style="border: 1px solid #e0e1e1;" border="0" cellpadding="0" cellspacing="0">
                                            <tr height="100">
                                                <td width="245" style="text-align: center;">
                                                    <span style="font-size: 80%; color: #e1e4e4;">1<sup style="font-size: 70%;">st</sup> Installment</span><br />
                                                    <span style="font-size: 200%; color: #e1e4e4;">&#8377;</span>&nbsp;<span style="font-size: 275%; color: #bcbebe;">25,000</span>
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

                                    <td bgcolor="#ffffff" width="245" style="">
                                        <table style="border: 1px solid #e0e1e1;" border="0" cellpadding="0" cellspacing="0">
                                            <tr height="100">
                                                <td width="245" style="text-align: center;">
                                                    <span style="font-size: 80%; color: #808080;">2<sup style="font-size: 70%;">nd</sup> Installment</span><br />
                                                    <span style="font-size: 200%; color: #808080;">&#8377;</span>&nbsp;<span style="font-size: 275%; color: black">25,000</span>
                                                </td>
                                            </tr>
                                            <tr height="80">
                                                <td width="245">
                                                    <table border="0" cellpadding="10" cellspacing="0" style="border-color: #e0e1e1; border-style: solid; border-width: 1px 0 0 0;">
                                                        <tr>
                                                            <td width="245" style="text-align: center;">
                                                                <span style="font-size: 80%; color: #808080; text-transform: capitalize; line-height: 145%;">Due date</span><br />
                                                                <span style="font-size: 125%; color: black; line-height: 145%; text-transform: uppercase;">APR 15<sup style="font-size: 60%; text-transform: none;">th</sup>, 2016</span>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>

                                </tr>
                            </table>

                        </center>
                    </td>
                </tr>

                <!-- MORE TEXT ////////////////////////////////////////////////////////////////////////////// -->

                <tr> 
                    <td>        
                        <table border="0" cellpadding="30" cellspacing="0" style="width: 100%; border-color: transparent;" bgcolor="#f4f6f6" color="#000000">
                            <tr>                                
                                <td style="text-align: center;">
                                    <span style="color: #252525; font-size: 85%;">If by any chance you haven’t been able to setup your access to our Learning Center, please click the button below to finish the process.</span><br/><br /><br/>                               
                                    <center>
                                        <a bgcolor="#f59533" color="#ffffff" class="btn" style="color: white; text-decoration: none; text-transform: capitalize; font-size: 95%; text-align: center; margin: 0px auto; padding: 25px; display: block; min-width: 30%; max-width: 50%; width: 200px; border: 2px solid #FE761B; background-color: #FE761B; background: -webkit-linear-gradient(left, #FE761B 0%, #FF9326 56%, #FF9326 100%); background: -moz-linear-gradient(right, #FE761B 0%, #FF9326 56%, #FF9326 100%); background: -o-linear-gradient(right, #FE761B 0%, #FF9326 56%, #FF9326 100%); background: linear-gradient(to right, #FE761B 0%, #FF9326 56%, #FF9326 100%); border-radius: 0px; text-shadow: 0px 0px 8px black;" href="<?php echo JAWS_PATH_WEB.'/setupaccess?user='.$user_id; ?>">

                                            <span style="">Setup your access&nbsp;&nbsp;<b>&rarr;</b></span>
                                        </a>
                                    </center><br />
                                    <span style="color: #252525; font-size: 85%;">Do remember that we will be able to grant you access to our Learning Centre only after you've completed the setup! Please reach out to the Jigsaw support team in case you have any queries or require any assistance. We are eager to help you!</span>
                                </td>
                            </tr>
                        </table>
                    </td>   
                </tr>   

                <!-- OUTRO ///////////////////////////////////////////////////////////////////////////////// -->

                <tr> 
                    <td>
                        <table border="0" cellpadding="30" cellspacing="0" style="width: 100%; border-color: transparent;" bgcolor="#0c8dc9" color="#ffffff">                           
                            <tr>                                
                                <td style="text-align: center;">
                                    <span style="font-size: 70%; color: #A9D7EC; line-height: 200%; text-transform: uppercase;">Contact</span><br />
                                    <span style="font-size: 80%; color: #F3F9FC;">+91-90192-17000</span>
                                </td>
                                <td style="text-align: center;">
                                    <span style="font-size: 70%; color: #A9D7EC; line-height: 200%; text-transform: uppercase;">Email</span><br />
                                    <a style="text-decoration: none;" href="mailto:support@jigsawacademy.com?Subject=Regarding%20My%20Enrollment" target="_top"><span style="font-size: 80%; color: #F3F9FC;">support@jigsawacademy.com</span></a>
                                </td>
                                <td style="text-align: center;">
                                    <span style="font-size: 70%; color: #A9D7EC; line-height: 200%; text-transform: uppercase;">Facebook</span><br />
                                    <a style="text-decoration: none;" href="https://www.facebook.com/jigsawacademy"><span style="font-size: 80%; color: #F3F9FC;">fb.com/jigsawacademy</span></a>
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
                                    <span style="color: #989898; font-size: 55%;">&copy;&nbsp;Jigsaw Academy Pvt. Ltd.</span>
                                </td>
                            </tr>
                        </table>
                    </td>   
                </tr>               
        
            </table>
        </center>

    </body>
</html>
