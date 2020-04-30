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

	// Payment Gateway - Settings
	define("JAWS_PAYMENT_GATEWAY_CURRENT","ebs");
	define("JAWS_PAYMENT_GATEWAY_FALLBACK","atom");

	// Payment Gateway - ATOM
	define("JAWS_PAYMENT_GATEWAY_ATOM_SID","18092"); // Test = 160, Live = 18092
	define("JAWS_PAYMENT_GATEWAY_ATOM_KEY","JIGSAW@123"); // Test = Test@123, Live = JIGSAW@123
	define("JAWS_PAYMENT_GATEWAY_ATOM_PRODID","JIGSAW"); // Test = NSE, Live = JIGSAW
	define("JAWS_PAYMENT_GATEWAY_ATOM_URL","https://payment.atomtech.in/paynetz/epi/fts"); // Live = https://payment.atomtech.in/paynetz/epi/fts, Test = https://paynetzuat.atomtech.in/paynetz/epi/fts

	define("JAWS_PAYMENT_GATEWAY_ATOM_SID_TEST","160"); // Test = 160, Live = 18092
	define("JAWS_PAYMENT_GATEWAY_ATOM_KEY_TEST","Test@123"); // Test = Test@123, Live = JIGSAW@123
	define("JAWS_PAYMENT_GATEWAY_ATOM_PRODID_TEST","NSE"); // Test = NSE, Live = JIGSAW
	define("JAWS_PAYMENT_GATEWAY_ATOM_URL_TEST","https://paynetzuat.atomtech.in/paynetz/epi/fts"); // Live = https://payment.atomtech.in/paynetz/epi/fts, Test = https://paynetzuat.atomtech.in/paynetz/epi/fts

	// Payment Gateway - EBS
	define("JAWS_PAYMENT_GATEWAY_EBS_MODE","LIVE"); // LIVE, TEST
	define("JAWS_PAYMENT_GATEWAY_EBS_PAYOPT","1211"); // Live = supposed to be '1211', but not working!
	define("JAWS_PAYMENT_GATEWAY_EBS_SID","10474"); // Test = 5880, Live = 10474
	define("JAWS_PAYMENT_GATEWAY_EBS_KEY","db522fadbd8481f40d813f0cb712f87f"); // Test = ebskey, Live = db522fadbd8481f40d813f0cb712f87f
	define("JAWS_PAYMENT_GATEWAY_EBS_URL_INR","https://secure.ebs.in/pg/ma/payment/request"); // Test = https://testing.secure.ebs.in/pg/ma/sale/pay, Live = https://secure.ebs.in/pg/ma/sale/pay
	define("JAWS_PAYMENT_GATEWAY_EBS_URL_USD","https://secure.ebs.in/pg/ma/sale/pay"); // Test = https://testing.secure.ebs.in/pg/ma/sale/pay, Live = https://secure.ebs.in/pg/ma/sale/pay

	define("JAWS_PAYMENT_GATEWAY_EBS_MODE_TEST","TEST"); // LIVE, TEST
	define("JAWS_PAYMENT_GATEWAY_EBS_PAYOPT_TEST","1211"); // Live = supposed to be '1211', but not working!
	define("JAWS_PAYMENT_GATEWAY_EBS_SID_TEST","5880"); // Test = 5880, Live = 10474
	define("JAWS_PAYMENT_GATEWAY_EBS_KEY_TEST","ebskey"); // Test = ebskey, Live = db522fadbd8481f40d813f0cb712f87f
	define("JAWS_PAYMENT_GATEWAY_EBS_URL_INR_TEST","https://testing.secure.ebs.in/pg/ma/sale/pay"); // Test = https://testing.secure.ebs.in/pg/ma/sale/pay, Live = https://secure.ebs.in/pg/ma/sale/pay
	define("JAWS_PAYMENT_GATEWAY_EBS_URL_USD_TEST","https://testing.secure.ebs.in/pg/ma/sale/pay"); // Test = https://testing.secure.ebs.in/pg/ma/sale/pay, Live = https://secure.ebs.in/pg/ma/sale/pay

	define("JAWS_PAYMENT_GATEWAY_PAYU_URL", "https://secure.payu.in/_payment");
	define("JAWS_PAYMENT_GATEWAY_PAYU_KEY", "rJYkqy");
	define("JAWS_PAYMENT_GATEWAY_PAYU_SALT", "AIxl2JqS");

	// Payment Gateway - Razorpay
	define('JAWS_PAYMENT_GATEWAY_RZPY_KEY_TEST', "rzp_test_XJifhHCjSqTT5V");
	define('JAWS_PAYMENT_GATEWAY_RZPY_SECRET_TEST', "yzivGYMijMUAsLRuUBrGXSdq");

	define('JAWS_PAYMENT_GATEWAY_RZPY_KEY_LIVE', "rzp_live_wTWocAbhjhdnTI");
    define('JAWS_PAYMENT_GATEWAY_RZPY_SECRET_LIVE', "Q9S1zR3iwZBjaaxry5UZGoif");
    
	// Payment Gateway - Razorpay NEFT
	define('JAWS_PAYMENT_GATEWAY_RZPY_NEFT_KEY_TEST', "rzp_test_ksffkkmI6ACZ53");
	define('JAWS_PAYMENT_GATEWAY_RZPY_NEFT_SECRET_TEST', "tsPjGdgJcxWQta56sckoXrrz");

	define('JAWS_PAYMENT_GATEWAY_RZPY_NEFT_KEY_LIVE', "rzp_live_1lHBXz53kDchQP");
	define('JAWS_PAYMENT_GATEWAY_RZPY_NEFT_SECRET_LIVE', "EOvxiHXWZUFvEtQv4AOls91J");
        
        //JA-120 NEw Razorpay Gateway Integration
        define('RZPY_NEW_ACC_KEY_LIVE', "rzp_test_J6UF87B4Neydpz");
	define('RZPY_NEW_ACC_SECRET_LIVE', "t0N8ygzi9RLAUHus8QqF6wTI");
        
        define('RZPY_NEW_ACC_KEY_TEST', "rzp_test_J6UF87B4Neydpz");
	define('RZPY_NEW_ACC_SECRET_TEST', "t0N8ygzi9RLAUHus8QqF6wTI");
        //JA-120 ends
?>