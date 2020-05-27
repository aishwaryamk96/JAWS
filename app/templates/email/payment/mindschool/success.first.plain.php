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
============================.......
	JIGSAW ACADEMY  -   Success First
============================.......

Hello <?php echo ucfirst(strtolower($GLOBALS["content"]["emailer"]["fname"])); ?>,

Welcome to Manipal Innovation and Design!

We have received your payment of <?php echo ((strcmp(strtolower($GLOBALS["content"]["emailer"]["currency"]), "inr") == 0) ? 'INR ' : 'USD ' ).number_format(intval($GLOBALS["content"]["emailer"]["sum"])); ?> for the course(s) listed below:

+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
<?php if(!empty($GLOBALS['content']['emailer']['bundle_details'])){ ?>
<?php echo $GLOBALS['content']['emailer']['bundle_details']['name']; ?>
<?php //JA-54 STARTS ?>
<?php if(!empty($GLOBALS["content"]["emailer"]["individual_course"])){ foreach($GLOBALS["content"]["emailer"]["individual_course"] as $idx => $iCourse) {  ?>  
----------------------------------------------
<?php echo ucwords(strtolower($iCourse["course_name"])); ?> 
----------------------------------------------
<?php }} ?> 
<?php if(!empty($GLOBALS["content"]["emailer"]["free_course"])){foreach($GLOBALS["content"]["emailer"]["free_course"] as  $idx => $fCourse) {  ?>  
----------------------------------------------
<?php echo ucwords(strtolower($fCourse["course_name"])); ?> 
----------------------------------------------
<?php //JA-54 ENDS ?>
<?php } }?> 
====================================================
<?php } else { ?>
<?php foreach($GLOBALS["content"]["emailer"]["courses"] as $course) {  ?>  
----------------------------------------------
<?php echo ucwords(strtolower($course["name"])); ?> 
----------------------------------------------
<?php } ?> 
<?php } ?> 
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

<?php if (intval($GLOBALS["content"]["emailer"]["payment"]["instl_total"]) > 1) { ?>
Since you have opted to finance your enrolment in instalments an email reminder with your payment details will be sent before each due date. We would like to confirm your installment scheme as below.

:::::::::::::::::::::::::::::::::::::::::::::::
   1st Installment
   <?php echo ((strcmp(strtolower($GLOBALS["content"]["emailer"]["currency"]), "inr") == 0) ? 'INR ' : 'USD ' ); ?><?php echo number_format(intval($GLOBALS["content"]["emailer"]["sum"])); ?>
   Paid 
:::::::::::::::::::::::::::::::::::::::::::::::

	<?php 
	$count = 2;
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
        $due_date = $GLOBALS["content"]["emailer"]["payment"]["instl"][$count]["due_date"];
        $due_date = date('M jS, Y', strtotime($due_date));
	?>:::::::::::::::::::::::::::::::::::::::::::::::
   <?php echo $instl_count_text_arr[$count]; ?>
   <?php echo ((strcmp(strtolower($GLOBALS["content"]["emailer"]["currency"]), "inr") == 0) ? 'INR ' : 'USD ' ); ?><?php echo number_format(intval($GLOBALS["content"]["emailer"]["payment"]["instl"][$count]["sum"])); ?>
   Due date - <?php echo $due_date; ?> 
:::::::::::::::::::::::::::::::::::::::::::::::

<?php $count ++; }  } ?>
<?php if ($GLOBALS["content"]["emailer"]["allow_setup"]) { ?>
<!--What's next?
Get access to the Jigsaw Learning Center (JLC).
Please copy and paste this link in your browser window.
<?php echo JAWS_PATH_WEB.'/setupaccess?user='.$GLOBALS["content"]["emailer"]["user_webid"]; ?>

The only way to access the Jigsaw Learning Center is through successful setup completion. Queries or assistance? Please reach out to the Jigsaw Support Team.-->

Happy Learning!
<?php } else { ?>
We require a little time to get your course materials on the Learning Center ready. Keep checking your email for the access details - it should be with you very soon!

<!--If you’d like to get started though, we recommend reading up on the latest in the world of analytics and Big Data on the official Jigsaw blog at http://analyticstraining.com-->

Happy Learning!
<?php } ?>

Contact: <?php echo $GLOBALS['content']['footer']['phone'] ?? '+91-90193-17000'; ?>
Email: know@mind-global.com
Jigsaw Academy Pvt. Ltd. <?php echo date("Y"); ?>

=================================================
By proceeding you have accepted our Terms and Conditions [ https://mind-global.com/terms-and-conditions/ ].This e-mail and any attachments with it, are for the sole use of the intended recipient(s) and may contain confidential and privileged information. Unauthorized access to this e-mail (or attachments) and disclosure or copying of its contents or any action taken in reliance on this e-mail is strictly prohibited and may be unlawful. Unintended recipients must notify the sender immediately by e-mail/phone & delete it from their system without making any copies or disclosing it to a third person. Before opening any attachments please check them for viruses and defects.