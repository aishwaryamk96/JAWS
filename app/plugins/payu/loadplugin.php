<?php

	function transact($transaction_info, $validatetoken) {

		// Construct return URL
    	$return_url = $transaction_info['return_url'].(strpos($transaction_info['return_url'], "?") ? "&" : "?")."validate=".$validatetoken."&pg=payu";

		// HASH = sha512(key|txnid|amount|productinfo|firstname|email|||||||||||SALT);
		$hash_str = JAWS_PAYMENT_GATEWAY_PAYU_KEY."|".$transaction_info["invoice_id"]."|".floatval($transaction_info["sum"])."|".$transaction_info['extra']['desc']."|".$transaction_info["name"]."|".$transaction_info["email"]."|||||||||||".JAWS_PAYMENT_GATEWAY_PAYU_SALT;
		$secure_hash = hash("sha512", $hash_str);

		// Render the form
    	?>
    	<html>
    		<head>
  			<title>Jigsaw Academy</title>
    		</head>
    		<body>

	    		<div style="margin: 45vh auto; display: block; text-align: center;">
	    			<b>Please wait while we process your request.<br/></b>
	    			Do not press back button or refresh the page!
	    		</div>

				<form action="<?php echo ((strcmp($transaction_info['currency'], "usd") == 0) ? JAWS_PAYMENT_GATEWAY_PAYU_URL : JAWS_PAYMENT_GATEWAY_PAYU_URL); ?>" name="payment" method="POST">

					<input type="hidden" name="firstname" value="<?php echo $transaction_info["name"]; ?>"/>
					<input type="hidden" name="lastname" value=""/>
					<input type="hidden" name="phone" value="<?php echo $transaction_info["phone"]; ?>"/>
					<input type="hidden" name="email" value="<?php echo $transaction_info["email"]; ?>"/>

					<input type="hidden" name="key" value="<?php echo JAWS_PAYMENT_GATEWAY_PAYU_KEY; ?>"/>
					<input type="hidden" name="hash" value="<?php echo $secure_hash; ?>"/>
					<input type="hidden" name="surl" value="<?php echo $return_url; ?>"/>
					<input type="hidden" name="furl" value="<?php echo $return_url; ?>"/>

					<input type="hidden" name="amount" value="<?php echo floatval($transaction_info['sum']); ?>"/>
					<input type="hidden" name="productinfo" value="<?php echo $transaction_info['extra']['desc']; ?>"/>
					<input type="hidden" name="txnid" value="<?php echo $transaction_info['invoice_id'];?>"/>
					<!-- <input type="hidden" name="currency" value="<?php //echo $transaction_info['currency'];?>"/> -->

					<!--<button onclick="document.payment.submit();"> SUBMIT </button>-->
				</form>

				<script>
					(function() {
						document.payment.submit();
					})();
	    		</script>
			</body>
		</html>
		<?php

	}

	function response_parse() {

		$response = $_POST;

		$response["reference_id"] = $response["mihpayid"];
		$hash_str = JAWS_PAYMENT_GATEWAY_PAYU_SALT."|".$response["status"]."||||||".$response["udf5"]."|".$response["udf4"]."|".$response["udf3"]."|".$response["udf2"]."|".$response["udf1"]."|".$response["email"]."|".$response["firstname"]."|".$response["productinfo"]."|".$response["amount"]."|".$response["txnid"]."|".JAWS_PAYMENT_GATEWAY_PAYU_KEY;
		$hash = hash("sha512", $hash_str);
		if ($hash != $response["hash"]) {
			$response["status"] = false;
		}
		else {
			$response["status"] = ($response["status"] == "success" ? true : false);
		}

		$response["channel_type"] = $response["payment_source"];
		$response["channel_info"] = $response["PG_TYPE"];

		return $response;

	}

	function get_client_ip() {
    	$ipaddress = '';
    	if (isset($_SERVER['HTTP_CLIENT_IP']))
        	$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    	else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        	$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    	else if(isset($_SERVER['HTTP_X_FORWARDED']))
        	$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    	else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        	$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    	else if(isset($_SERVER['HTTP_FORWARDED']))
        	$ipaddress = $_SERVER['HTTP_FORWARDED'];
    	else if(isset($_SERVER['REMOTE_ADDR']))
        	$ipaddress = $_SERVER['REMOTE_ADDR'];
    	else
        	$ipaddress = 'UNKNOWN';
    	return $ipaddress;
	}

?>