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
============================.
	JIGSAW ACADEMY  -   Init
============================.

Hello <?php echo ucfirst(strtolower($GLOBALS["content"]["emailer"]["fname"])); ?>!

Thank you for signing up for the Creative Leader's Program at MIND.

+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
<?php if(!empty($GLOBALS['content']['emailer']['bundle_details'])){ ?>
You have enrolled in :

<?php echo $GLOBALS['content']['emailer']['bundle_details']['name']; ?>
<?php if(!empty($GLOBALS['content']['emailer']['individual_course'])){foreach($GLOBALS['content']['emailer']['free_course'] as $idx => $fCourse){ ?>

----------------------------------------------
<?php echo $fCourse['course_name'];?>
<?php }} ?>
<?php if(!empty($GLOBALS['content']['emailer']['individual_course'])){foreach($GLOBALS['content']['emailer']['individual_course'] as $idx => $iCourse){ ?>

----------------------------------------------
<?php echo $iCourse['course_name'];?>
<?php } } ?>
<?php } else { ?>
Below is your list of courses:

<?php foreach($GLOBALS["content"]["emailer"]["courses"] as $course) {  ?>  
----------------------------------------------
<?php echo ucwords(strtolower($course["name"])); ?> 
----------------------------------------------
<?php } ?> 
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

Please ensure your banking transaction limit is above the payable amount.

Please reach out to the Jigsaw support team in case you have any queries or require any assistance.

Thank you

Contact: +91-90193-17000
Email: know@mind-global.com

=================================================
By proceeding you have accepted our Terms and Conditions [ https://mind-global.com/terms-and-conditions/ ].This e-mail and any attachments with it, are for the sole use of the intended recipient(s) and may contain confidential and privileged information. Unauthorized access to this e-mail (or attachments) and disclosure or copying of its contents or any action taken in reliance on this e-mail is strictly prohibited and may be unlawful. Unintended recipients must notify the sender immediately by e-mail/phone & delete it from their system without making any copies or disclosing it to a third person. Before opening any attachments please check them for viruses and defects.