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
============================...
	JIGSAW ACADEMY
============================...

Hello <?php echo ucfirst(strtolower($GLOBALS["content"]["emailer"]["fname"])); ?>,

Don't stall your learning curve just yet!

Pay now for continued access to your course and these amazing additional benefits:
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

JLC Access
Learning Materials
Industry Connect through Webinars & Competitions
Access to Industry & Academic Experts
Career Assistance

+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

To make the payment, please copy and paste the link below in your browser window.

<?php echo JAWS_PATH_WEB.'/pay?pay='.$GLOBALS["content"]["emailer"]["paylink_id"]; ?>

<?php /* if(!empty($GLOBALS["content"]["emailer"]["receipt_type"]) && $GLOBALS["content"]["emailer"]["receipt_type"] === "pgpdm") { ?>
    IMPORTANT for PGPDM Students! You can get INR 20,000 off your last instalment! All you have to do is ensure that your instalment payments are regularly paid at least 1 day prior to the due date. Don't miss out!
<?php } */ ?>

Please reach out to Jigsaw Support team in case you have any queries or require any assistance.

Happy Learning!

Contact: <?php echo $GLOBALS['content']['footer']['phone'] ?? '+91-90193-17000'; ?>
Email: know@mind-global.com
Jigsaw Academy Pvt. Ltd. <?php echo date("Y"); ?>

=================================================
By proceeding you have accepted our Terms and Conditions [ https://mind-global.com/terms-and-conditions/ ].This e-mail and any attachments with it, are for the sole use of the intended recipient(s) and may contain confidential and privileged information. Unauthorized access to this e-mail (or attachments) and disclosure or copying of its contents or any action taken in reliance on this e-mail is strictly prohibited and may be unlawful. Unintended recipients must notify the sender immediately by e-mail/phone & delete it from their system without making any copies or disclosing it to a third person. Before opening any attachments please check them for viruses and defects.