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
============================
	JIGSAW ACADEMY
============================
<?php echo $GLOBALS["content"]["emailer"]["header"]; ?>
------------------------------------------------------------------
Hello <?php echo ucfirst(strtolower($GLOBALS["content"]["emailer"]["fname"])); ?>! <?php echo $GLOBALS["content"]["emailer"]["sub-header"]; ?>

+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

<?php echo $GLOBALS['content']['emailer']['mail_data']['name']; ?>
<?php if(!empty($GLOBALS['content']['emailer']['mail_data']['opportunity_text'])){ ?>
    <?php echo $GLOBALS['content']['emailer']['mail_data']['opportunity_text']; ?>
    .................................................
    <?php foreach( $GLOBALS['content']['emailer']['mail_data']['opportunity_icons'] as $icons ){?>
        <?php echo $icons['oppr_title'] . " "; ?>
    <?php } ?>
    .................................................
<?php } ?>

-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_
<?php echo $GLOBALS['content']['emailer']['mail_data']['earn_text']; ?>

<?php echo $GLOBALS['content']['emailer']['mail_data']['earn_description']; ?> ==> <?php echo $GLOBALS['content']['emailer']['mail_data']['salary_text_indian']; ?>

<?php echo $GLOBALS['content']['emailer']['mail_data']['earn_description_usd']; ?> ==> <?php echo $GLOBALS['content']['emailer']['mail_data']['salary_text_us']; ?>
-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_

-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_
<?php echo $GLOBALS['content']['emailer']['mail_data']['work_text']; ?>

<?php foreach($$GLOBALS['content']['emailer']['mail_data']['work_description'] as $work){ ?>
    <?php echo $work['thumbnail']; ?>
<?php } ?>
-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_

<?php if(!empty($GLOBALS['content']['emailer']['mail_data']['university_text'])){ ?>
    <?php echo $GLOBALS['content']['emailer']['mail_data']['university_text']; ?>
    .................................................
    <?php foreach( $GLOBALS['content']['emailer']['mail_data']['university_icons'] as $icons ){ ?>
        <?php echo $icons['oppr_title'] . " "; ?>
    <?php } ?>
    .................................................
<?php } ?>

<?php echo $GLOBALS['content']['emailer']['mail_data']['footer_text']; ?>

<?php echo $GLOBALS['content']['emailer']['mail_data']['footer_description']; ?>

View Details ==> [<?php echo $GLOBALS['content']['emailer']['mail_data']['post_url']; ?>]

+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

Happy Learning!

Contact: +91 90192-17000
Email: info@jigsawacademy.com
Jigsaw Academy Education Pvt. Ltd.

=================================================
This e-mail and any attachments with it, are for the sole use of the intended recipient(s) and may contain confidential and privileged information. Unauthorized access to this e-mail (or attachments) and disclosure or copying of its contents or any action taken in reliance on this e-mail is strictly prohibited and may be unlawful. Unintended recipients must notify the sender immediately by e-mail/phone & delete it from their system without making any copies or disclosing it to a third person. Before opening any attachments please check them for viruses and defects.