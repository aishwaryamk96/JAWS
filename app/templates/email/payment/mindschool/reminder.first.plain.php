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
============================....
	JIGSAW ACADEMY
============================....

Hello <?php echo ucfirst(strtolower($GLOBALS["content"]["emailer"]["fname"])); ?>,

Hope you're enjoying learning with Jigsaw Academy!

This is a gentle reminder regarding your outstanding payment of
<?php echo ((strcmp(strtolower($GLOBALS["content"]["emailer"]["currency"]), "inr") == 0) ? 'INR ' : 'USD ' ).number_format(intval($GLOBALS["content"]["emailer"]["sum"])); ?>

which is due on
<?php echo date('F jS, Y', strtotime($GLOBALS["content"]["emailer"]["due_date"])); ?>

<?php if (intval($GLOBALS["content"]["emailer"]["payment"]["instl_total"]) > 1) { ?>
You had opted to finance your enrollment in installments. The following is a summary of the status of your installment scheme.

<?php $count = 1;
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
    if (strcmp($GLOBALS["content"]["emailer"]["instl"][$count]["status"], 'paid') == 0) { ?>
:::::::::::::::::::::::::::::::::::::::::::::::
   <?php echo $instl_count_text_arr[$count]; ?>
   <?php echo ((strcmp(strtolower($GLOBALS["content"]["emailer"]["currency"]), "inr") == 0) ? 'INR ' : 'USD ' ); ?><?php echo number_format(intval($GLOBALS["content"]["emailer"]["payment"]["instl"][$count]["sum"])); ?>
   Paid 
:::::::::::::::::::::::::::::::::::::::::::::::
    <?php } else {
        $due_date = $GLOBALS["content"]["emailer"]["payment"]["instl"][$count]["due_date"];
        $due_date = date('M jS, Y', strtotime($due_date));
        ?>
:::::::::::::::::::::::::::::::::::::::::::::::
   <?php echo $instl_count_text_arr[$count]; ?>
   <?php echo ((strcmp(strtolower($GLOBALS["content"]["emailer"]["currency"]), "inr") == 0) ? 'INR ' : 'USD ' ); ?><?php echo number_format(intval($GLOBALS["content"]["emailer"]["payment"]["instl"][$count]["sum"])); ?>
   <?php echo $due_date; ?> 
:::::::::::::::::::::::::::::::::::::::::::::::
    <?php } $count++; } ?>
<?php } ?>

To make the payment, please copy and paste the link below in your browser window.

<?php echo JAWS_PATH_WEB.'/pay?pay='.$GLOBALS["content"]["emailer"]["paylink_id"]; ?>

We urge you to make the payment to have uninterrupted access to your enrolled program and the Jigsaw Learning Centre.

Please ignore this email if you have already paid.

<?php /* if(!empty($GLOBALS["content"]["emailer"]["receipt_type"]) && $GLOBALS["content"]["emailer"]["receipt_type"] === "pgpdm") { ?>
IMPORTANT for PGPDM Students! You can get INR 20,000 off your last instalment! All you have to do is ensure that your instalment payments are regularly paid at least 1 day prior to the due date. Don't miss out!
<?php } */ ?>

If you have any queries or need any assistance, please reach out to the Jigsaw Payments Team.

Happy Learning!

Contact: <?php echo $GLOBALS['content']['footer']['phone'] ?? '+91-90193-17000'; ?>
Email: payments@jigsawacademy.com
Jigsaw Academy Pvt. Ltd. <?php echo date("Y"); ?>

=================================================
By proceeding you have accepted our Terms and Conditions [ https://mind-global.com/terms-and-conditions/ ].This e-mail and any attachments with it, are for the sole use of the intended recipient(s) and may contain confidential and privileged information. Unauthorized access to this e-mail (or attachments) and disclosure or copying of its contents or any action taken in reliance on this e-mail is strictly prohibited and may be unlawful. Unintended recipients must notify the sender immediately by e-mail/phone & delete it from their system without making any copies or disclosing it to a third person. Before opening any attachments please check them for viruses and defects.