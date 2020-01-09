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

    JIGSAW ACADEMY WORKFLOW SYSTEM v2
    ---------------------------------
*/

  	// Prevent exclusive access
    if (!defined("JAWS")) {
    	header('Location: https://www.jigsawacademy.com');
    	die();
    }

    // Load stuff
    load_module("iot");

    // Init View
    iot_view_init();

    ?>

    <!-- HTML HEAD -->
    <?php 
    $GLOBALS["content"]["title"] = "Terms & Conditions";
    load_template("iot", "v2/head");
    ?>
    <!-- HTML HEAD ENDS -->

    <div style='padding: 40px;'>
		<h3>Return policy</h3><br/>

		Our replacement policy allows you to request a replacement device at no additional cost if the device received by you is defective or is not as ordered.<br/><br/>

		Under this policy:
		<ul>
			<li>
				In case you receive damaged or defective or incomplete products, please report the same to our Customer Service team. This should be reported within 7 days of receiving the damaged/malfunctioning products.
			</li>

			<li>
				In case you feel that the product received is not as shown on the site or as per your expectations, you must bring it to the notice of our customer service within 24 hours of receiving the product. The Customer Service Team after considering your complaint will take an appropriate decision.
			</li>

			<li>
				In case of complaints regarding products that come with a warranty from manufacturers, please refer the issue to them.
			</li>
		</ul><br/><br/>

		<h3>Refund Policy</h3><br/>

		To request a refund, simply contact us with your purchase details within seven days of your purchase. We&#39;ll send you a RMA number after which you can ship back the product(s) to us. Please include your order number (sent to you via email after ordering) and optionally tell us why you’re requesting a refund – we take customer feedback very seriously and use it to constantly improve our products and quality of service. Refunds are not being provided for services delivered in full such as installation service and provided knowledge base services. Refunds are being processed within 21 days’ period.<br/><br/>

		Please note that this policy only covers replacements for eligible products that are defective, or are not shipped as ordered. It will not cover routine product wear and tear, damage incurred during use or any other forms of damage and will not, in any event, entitle you to a refund, whether partial or otherwise.<br/><br/>

		In case of any other issues, you can contact the Customer Service Team.
		</div>

<!-- FOOTER -->
	<?php load_template("iot", "v2/foot"); ?>
<!-- FOOTER ENDS -->