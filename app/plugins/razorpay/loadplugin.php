<?php

	## Razorpay : Account email: santosh@jigsawacademy.com
	## Razorpay : Account password: sant@3737
	## Razorpay : PHP Library : https://github.com/razorpay/razorpay-php
	## Razorpay : PHP Library : 2.2.0
	## Razorpay : Amount is always in paise so multiply rupees amount by 100

	include 'razorpay/Razorpay.php';

	use Razorpay\Api\Api;

	function response_parse($paylink_info) {

		$response = $_POST;

		$api_key = constant('JAWS_PAYMENT_GATEWAY_RZPY_KEY_'.($GLOBALS['jaws_exec_live'] ? "LIVE" : "TEST"));
		$api_secret = constant('JAWS_PAYMENT_GATEWAY_RZPY_SECRET_'.($GLOBALS['jaws_exec_live'] ? "LIVE" : "TEST"));

		$api = new Api($api_key, $api_secret);
		
		$payment = $api->payment->fetch($response['razorpay_payment_id']);

		$status = false;

		$result = $payment->toArray();

		if ($result["status"] == "authorized") {
            try {
                $result = $payment->capture(array(
                    'currency' => strtoupper($paylink_info['currency']), 
                    'amount' => $paylink_info['sum'] * 100
                    ))->toArray();
                $status = true;
            }
            catch (Exception $e) {
                // amount tampering 
                activity_create("critical", "razorpay", "amount.tamper-->", "paylink_id", $paylink_info["paylink_id"], "user_id", $paylink_info["user_id"], "RZPY payment_response_parse capture Failed -> ".json_encode($result)."<-->". $e->getMessage(), "logged");
            }
		}
		else if ($result["status"] != "captured") {				
			// Amount tampering, may be...this section shouldnot get executed.
			activity_create("critical", "razorpay", "not.captured", "paylink_id", $paylink_info["paylink_id"], "user_id", $paylink_info["user_id"], "RZPY this should not get executed", "logged");
		} else {
			// already captured. this section shouldnot get executed either.
			activity_create("critical", "razorpay", "already.captured", "paylink_id", $paylink_info["paylink_id"], "user_id", $paylink_info["user_id"], "RZPY this should not get executed but came here anyways", "logged");
		}

		$response = array(
			'status' => $status,
			'reference_id' => $response['razorpay_payment_id'],
			'channel_type' => 'RZPY',
			'channel_info' => 'Razorpay',
			'meta' => json_encode($result)
		);

		return $response;

    }
    
	function response_parse_neft($paylink_info) {
        echo 'neft not doing anything'; 
        exit;
	}

    function createOrder($data = array()){

        // $GLOBALS['jaws_exec_live'] = false;

        $api_key = constant('JAWS_PAYMENT_GATEWAY_RZPY_KEY_'.($GLOBALS['jaws_exec_live'] ? "LIVE" : "TEST"));
		$api_secret = constant('JAWS_PAYMENT_GATEWAY_RZPY_SECRET_'.($GLOBALS['jaws_exec_live'] ? "LIVE" : "TEST"));

        $api = new Api($api_key, $api_secret);

        try {
            $order = $api->order->create(
                array(
                    'receipt' => $data['receipt'], 
                    'amount' => $data['amount'], 
                    'currency' => $data['currency']
                )
            ); 
        }
        catch (Exception $e) {
            return array(
                "status" => false,
                "message" => "Some error occured. Please contect support@jigsawacademy.com",
                "response" => $e->getMessage(),
            );
        }

        $order = $order->toArray();

        return $order;
    }

?>