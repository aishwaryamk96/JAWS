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

	// SMS Gateway - Default
	define("JAWS_SMS_GATEWAY","exotel");

	// SMS API - EXOTEL
	define("JAWS_SMS_EXOTEL_SENDER","JIGSAW");
	define("JAWS_SMS_EXOTEL_SID","jigsawacademy");
	define("JAWS_SMS_EXOTEL_KEY","8fa2d8a191e5ad114b6770dd82543ef8ee10dc7c");
	define("JAWS_SMS_EXOTEL_URL","https://".JAWS_SMS_EXOTEL_SID.":".JAWS_SMS_EXOTEL_KEY."@twilix.exotel.in/v1/Accounts/".JAWS_SMS_EXOTEL_SID."/Sms/send");

	// CALL API - EXOTEL
	define("JAWS_CALL_EXOTEL_URL","https://".JAWS_SMS_EXOTEL_SID.":".JAWS_SMS_EXOTEL_KEY."@twilix.exotel.in/v1/Accounts/".JAWS_SMS_EXOTEL_SID."/Calls/connect");

	// SMS API - VALUE LEAF
	define("JAWS_SMS_VALUELEAF_SID","JIGSAW");
	define("JAWS_SMS_VALUELEAF_KEY","A1ffbd402c2d364b37c6b11494f128da4");
	define("JAWS_SMS_VALUELEAF_URL","http://alerts.valueleaf.com/api/v3/");

	// OTP API - MSG91
	define("JAWS_OTP_MSG91_SID","JAWS");
	define("JAWS_OTP_MSG91_KEY","7F7DXQ5Ig-R5z4WRMUklj5pr85w6pbqH5anuKPsFFdHyiIk9b8-606JJ9ZPPPbTRLTx1mulh9ZN5Rqnj_EFwy-YCgMLbfnFyHRL6Rbc_kHgLVin-Ju1BCi_mmI7-cPvqAt7Toq1cFQbVxmXZtKBFMw==");
	define("JAWS_OTP_MSG91_URL","https://sendotp.msg91.com/api");

	
?>
