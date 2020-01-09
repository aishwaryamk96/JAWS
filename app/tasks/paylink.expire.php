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

  	// This task is executed to expire payment links that have a expiry date set.
  	// This is a taken up by a daily CRON job

  	// Load Stuff
  	load_module("subs");
	// Add instl disabler too !!!!
  	// Expire all payment links which are pending and installment count is 1 and expiry date is set.
  	/*db_exec("
		UPDATE
			payment_link AS link
		INNER JOIN
			payment_instl AS instl
			ON instl.paylink_id = link.paylink_id
		SET
			link.status = 'expired'
		WHERE
			link.expire_date > NOW()
			AND link.status = 'enabled'
			AND instl.instl_count = 1
			AND instl.status != 'paid';");*/

	activity_debug_start();
	activity_debug_log("paylink.expire - ".json_encode(db_query("
		SELECT
		 	*
		FROM
			payment_link AS link
		INNER JOIN
			payment_instl AS instl
			ON instl.paylink_id = link.paylink_id
		WHERE
			link.expire_date > NOW()
			AND link.status = 'enabled'
			AND instl.instl_count = 1
			AND instl.status != 'paid';
	")));

  	// All done
  	die();

?>
