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
  
  $qres = db_query('SELECT paylink_id, user_id FROM payment_link WHERE web_id="'.$GLOBALS["content"]["emailer"]["paylink_id"].'" LIMIT 1;');
  activity_create("ignore", "paylink.send", "sent", "paylink_id", $qres[0]["paylink_id"], "user_id", $qres[0]["user_id"], "Payment Link Sent", "logged");

?>
====================== 
	JIGSAW ACADEMY
======================

Hi <?php echo $GLOBALS["content"]["emailer"]["fname"]; ?>!

Thank you for choosing to enroll with us!

Below is your list of courses:

+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
<?php foreach($GLOBALS["content"]["emailer"]["courses"] as $course) {  ?>  
----------------------------------------------
Course: <?php echo $course["name"]; ?> [ <?php echo $course["url"]; ?> ]
Mode: <?php echo $course["learn_mode"]; ?> 
Description: <?php echo $course["desc"]; ?>

----------------------------------------------
<?php } ?> 
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

<?php  if (intval($GLOBALS["content"]["emailer"]["payment"]["instl_total"]) > 1) {  ?>
You have opted to finance your enrollment in installments. We would like to confirm your installment scheme as below. We will email you a reminder of your payment details a week before each due date.
	<?php 

	$count = 1;
	$due_days_tol = 0;
	$instl_count_text_arr = array(
		1 => '1st Installment',
		2 => '2nd Installment',
		3 => '3rd Installment',
		4 => '4th Installment',
		5 => '5th Installment',
		6 => '6th Installment',
		7 => '7th Installment',
		8 => '8th Installment',
		9 => '9th Installment',
	);

	while($count <= intval($GLOBALS["content"]["emailer"]["payment"]["instl_total"])) {

		// Due Days Calc
		if ($count > 1) $due_days_tol += intval($GLOBALS["content"]["emailer"]["payment"]["instl"][$count]["due_days"]);

	?>
:::::::::::::::::::::::::::::::::::::::::::::::
   <?php echo $instl_count_text_arr[$count]; ?>
   <?php echo number_format(intval($GLOBALS["content"]["emailer"]["payment"]["instl"][$count]["sum"])); ?>
   <?php if ($count > 1) { ?>
	Due on (days from initial)
	<?php echo strval($due_days_tol)." Days";  
   } else { ?>
	Initial
	<?php } ?> 
:::::::::::::::::::::::::::::::::::::::::::::::
<?php $count ++; }  } else { ?>
For this enrollment, you will be charged a one-time sum of <?php echo strtoupper($GLOBALS["content"]["emailer"]["currency"])." ".number_format(intval($GLOBALS["content"]["emailer"]["sum"])); ?>
<?php } ?>

Please copy and paste this link in your browser window.
<?php echo JAWS_PATH_WEB.'/pay?pay='.$GLOBALS["content"]["emailer"]["paylink_id"]; ?>

Please reach out to the Jigsaw support team in case you have any queries or require any assistance.

Thank you

=================================================
Contact: +91-90192-17000
Email: support@jigsawacademy.com