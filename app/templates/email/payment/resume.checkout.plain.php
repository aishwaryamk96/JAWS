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
============================......
	JIGSAW ACADEMY  -   Resume Checkout
============================......

Hello <?php echo ucfirst(strtolower($GLOBALS["content"]["emailer"]["fname"])); ?>,

The courses left in your cart have been reserved and are waiting for your return.

The courses are:

+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
<?php if(!empty($GLOBALS['content']['emailer']['bundle_details'])){ ?>
<?php echo $GLOBALS['content']['emailer']['bundle_details']['name']; ?>
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
You had opted to finance your enrollment in installments as below.</td>
<?php } else { ?>     
For this enrollment, you will be charged a one-time sum of <?php echo ((strcmp(strtolower($GLOBALS["content"]["emailer"]["currency"]), "inr") == 0) ? 'INR ' : 'USD ' ).number_format(intval($GLOBALS["content"]["emailer"]["sum"])); ?>
<?php } ?>  

<?php if (intval($GLOBALS["content"]["emailer"]["payment"]["instl_total"]) > 1) { ?>
<?php  $count = 1;
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
        if($count == 1){ ?>
:::::::::::::::::::::::::::::::::::::::::::::::
   1st Installment
   <?php echo ((strcmp(strtolower($GLOBALS["content"]["emailer"]["currency"]), "inr") == 0) ? 'INR ' : 'USD ' ); ?><?php echo number_format(intval($GLOBALS["content"]["emailer"]["sum"])); ?>
   Initial 
:::::::::::::::::::::::::::::::::::::::::::::::

	<?php 
	} else {
        $due_date = $GLOBALS["content"]["emailer"]["payment"]["instl"][$count]["due_date"];
        $due_date = date('M jS, Y', strtotime($due_date));
	?>:::::::::::::::::::::::::::::::::::::::::::::::
   <?php echo $instl_count_text_arr[$count]; ?>
   <?php echo ((strcmp(strtolower($GLOBALS["content"]["emailer"]["currency"]), "inr") == 0) ? 'INR ' : 'USD ' ); ?><?php echo number_format(intval($GLOBALS["content"]["emailer"]["payment"]["instl"][$count]["sum"])); ?>
   Due date - <?php echo $due_date; ?> 
:::::::::::::::::::::::::::::::::::::::::::::::

<?php } $count ++; }  } ?>

Would you like to resume from where you left?

Please copy and paste this link in your browser window.
<?php echo JAWS_PATH_WEB.'/pay?pay='.$GLOBALS["content"]["emailer"]["paylink_id"]; ?>

Queries or assistance? Please reach out to the Jigsaw Payments Team.

Happy Learning!

Contact: <?php echo $GLOBALS['content']['footer']['phone'] ?? '+91-90192-17000'; ?>
Email: payments@jigsawacademy.com
Jigsaw Academy Pvt. Ltd. <?php echo date("Y"); ?>

=================================================
By proceeding you have accepted our Terms and Conditions [ https://www.jigsawacademy.com/terms-conditions/ ].This e-mail and any attachments with it, are for the sole use of the intended recipient(s) and may contain confidential and privileged information. Unauthorized access to this e-mail (or attachments) and disclosure or copying of its contents or any action taken in reliance on this e-mail is strictly prohibited and may be unlawful. Unintended recipients must notify the sender immediately by e-mail/phone & delete it from their system without making any copies or disclosing it to a third person. Before opening any attachments please check them for viruses and defects.