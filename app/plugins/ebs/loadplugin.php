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

	// EBS Payment Gateway
	// ---------------------

	// The following functions are mandatory to all plugins that implement a payment gateway
	// They are called by the payment library and must be present for the plugin to work as a gateway
	// ---------------

	// This will transact with the gateway - EBS-ized function
	function transact($transaction_info, $validatetoken) {

	    	if ($GLOBALS['jaws_exec_live']) {

		    	// Calcuate return URL
		    	$return_url = $transaction_info['return_url'].(strpos($transaction_info['return_url'], "?") ? "&" : "?")."validate=".$validatetoken."&pg=ebs&DR={DR}";

		    	// Calculate secure hash  
		    	$hashstr = JAWS_PAYMENT_GATEWAY_EBS_KEY."|".urlencode(JAWS_PAYMENT_GATEWAY_EBS_SID)."|".urlencode($transaction_info['sum'].".00")."|".urlencode($transaction_info['invoice_id'])."|".$return_url."|".urlencode(JAWS_PAYMENT_GATEWAY_EBS_MODE);
		    	$secure_hash = md5($hashstr);

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

					<form action="<?php echo ((strcmp($transaction_info['currency'], "usd") == 0) ? JAWS_PAYMENT_GATEWAY_EBS_URL_USD : JAWS_PAYMENT_GATEWAY_EBS_URL_INR); ?>" name="payment" method="POST">
						
						<input type="hidden" value="<?php echo JAWS_PAYMENT_GATEWAY_EBS_SID;?>" name="account_id"/>
						<input type="hidden" value="<?php echo $secure_hash; ?>" name="secure_hash"/>
						<input type="hidden" value="<?php echo $return_url; ?>" name="return_url"/>
			
						<input type="hidden" value="<?php echo $transaction_info['sum'].".00";?>" name="amount"/>
						<input type="hidden" value="<?php echo substr($transaction_info['extra']['desc'].' ('.$transaction_info['extra']['web_id'].')', 0, 255); ?>" name="description"/>
						<input type="hidden" value="<?php echo $transaction_info['invoice_id'];?>" name="reference_no"/>
						
						<input type="hidden" value="0" name="channel"/>
						
						<?php if (strcmp($transaction_info['currency'], "usd") == 0) { ?>
							<input type="hidden" value="<?php echo JAWS_PAYMENT_GATEWAY_EBS_PAYOPT;?>" name="payment_option"/>
						<?php } ?>

						<input type="hidden" value="<?php echo JAWS_PAYMENT_GATEWAY_EBS_MODE;?>" name="mode"/>
			
						<input type="hidden" value="<?php echo strtoupper($transaction_info['currency']);?>" name="currency"/>
						<input type="hidden" value="<?php echo strtoupper($transaction_info['currency']);?>" name="display_currency"/>
						<input type="hidden" value="<?php echo "1";?>" name="display_currency_rates"/>
			
						<input type="hidden" value="<?php echo $_SESSION["user"]["email"];?>" name="email"/>
						<input type="hidden" value="<?php echo $_SESSION["user"]["name"];?>" name="name"/>		
						<input type="hidden" value="<?php echo $_SESSION["user"]["name"];?>" name="ship_name"/>
						
						<?php 
						if ((isset($_SESSION["user"]["phone"])) && (strlen($_SESSION["user"]["phone"]) > 5)) { ?>
								<input type="hidden" value="<?php echo $_SESSION["user"]["phone"];?>" name="phone"/>
								<input type="hidden" value="<?php echo $_SESSION["user"]["phone"];?>" name="ship_phone"/>
							<?php } else { ?>
								<input type="hidden" value="9999999999" name="phone"/>
								<input type="hidden" value="9999999999" name="ship_phone"/>
						<?php }
						?>
			
						<?php 
						if ((isset($_SESSION["user"]["city"])) && (strlen($_SESSION["user"]["city"]) > 0)) { ?>
								<input type="hidden" value="<?php echo $_SESSION["user"]["city"];?>" name="city"/>
								<input type="hidden" value="<?php echo $_SESSION["user"]["city"];?>" name="ship_city"/>
							<?php } else { ?>
								<input type="hidden" value="NIL" name="city"/>
								<input type="hidden" value="NIL" name="ship_city"/>
						<?php }
						?>
			
						<?php 
						if ((isset($_SESSION["user"]["state"])) && (strlen($_SESSION["user"]["state"]) > 0)) { ?>
								<input type="hidden" value="<?php echo $_SESSION["user"]["state"];?>" name="state"/>
								<input type="hidden" value="<?php echo $_SESSION["user"]["state"];?>" name="ship_state"/>
							<?php } else { ?>
								<input type="hidden" value="NIL" name="state"/>
								<input type="hidden" value="NIL" name="ship_state"/>
						<?php }
						?>
			
						<?php 
						if ((isset($_SESSION["user"]["country"])) && (strlen($_SESSION["user"]["country"]) > 0)) { ?>
								<input type="hidden" value="<?php echo strtoupper($_SESSION["user"]["country"]);?>" name="country"/>
								<input type="hidden" value="<?php echo strtoupper($_SESSION["user"]["country"]);?>" name="ship_country"/>
							<?php } else { ?>
								<input type="hidden" value="IND" name="country"/>
								<input type="hidden" value="IND" name="ship_country"/>
						<?php }
						?>
			
						<?php 
						if ((isset($_SESSION["user"]["zipcode"])) && (strlen($_SESSION["user"]["zipcode"]) > 0)) { ?>
								<input type="hidden" value="<?php echo $_SESSION["user"]["zipcode"];?>" name="postal_code"/>
								<input type="hidden" value="<?php echo $_SESSION["user"]["zipcode"];?>" name="ship_postal_code"/>
							<?php } else { ?>
								<input type="hidden" value="560075" name="postal_code"/>
								<input type="hidden" value="560075" name="ship_postal_code"/>
						<?php }
						?>
			
						<?php 
						if ((isset($_SESSION["user"]["address"])) && (strlen($_SESSION["user"]["address"]) > 0)) { ?>
								<input type="hidden" value="<?php echo $_SESSION["user"]["address"];?>" name="address"/>
								<input type="hidden" value="<?php echo $_SESSION["user"]["address"];?>" name="ship_address"/>
							<?php } else { ?>
								<input type="hidden" value="NIL" name="address"/>
								<input type="hidden" value="NIL" name="ship_address"/>
						<?php }
						?>
			
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

		// JAWS Test Mode Alternate Payment Transaction
		else {

			// Calcuate return URL
		    	$return_url = $transaction_info['return_url'].(strpos($transaction_info['return_url'], "?") ? "&" : "?")."validate=".$validatetoken."&DR={DR}";

		    	// Calculate secure hash  
		    	$hashstr = JAWS_PAYMENT_GATEWAY_EBS_KEY_TEST."|".urlencode(JAWS_PAYMENT_GATEWAY_EBS_SID_TEST)."|".urlencode($transaction_info['sum'].".00")."|".urlencode($transaction_info['invoice_id'])."|".$return_url."|".urlencode(JAWS_PAYMENT_GATEWAY_EBS_MODE_TEST);
		    	$secure_hash = md5($hashstr);

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

					<form action="<?php echo ((strcmp($transaction_info['currency'], "usd") == 0) ? JAWS_PAYMENT_GATEWAY_EBS_URL_USD_TEST : JAWS_PAYMENT_GATEWAY_EBS_URL_INR_TEST); ?>" name="payment" method="POST">
						
						<input type="hidden" value="<?php echo JAWS_PAYMENT_GATEWAY_EBS_SID_TEST;?>" name="account_id"/>
						<input type="hidden" value="<?php echo $secure_hash; ?>" name="secure_hash"/>
						<input type="hidden" value="<?php echo $return_url; ?>" name="return_url"/>
			
						<input type="hidden" value="<?php echo $transaction_info['sum'].".00";?>" name="amount"/>
						<input type="hidden" value="<?php echo substr($transaction_info['extra']['desc'].' ('.$transaction_info['extra']['web_id'].')', 0, 255); ?>" name="description"/>
						<input type="hidden" value="<?php echo $transaction_info['invoice_id'];?>" name="reference_no"/>
						
						<input type="hidden" value="0" name="channel"/>
						
						<?php if (strcmp($transaction_info['currency'], "usd") == 0) { ?>
							<input type="hidden" value="<?php echo JAWS_PAYMENT_GATEWAY_EBS_PAYOPT_TEST;?>" name="payment_option"/>
						<?php } ?>

						<input type="hidden" value="<?php echo JAWS_PAYMENT_GATEWAY_EBS_MODE_TEST;?>" name="mode"/>
			
						<input type="hidden" value="<?php echo strtoupper($transaction_info['currency']);?>" name="currency"/>
						<input type="hidden" value="<?php echo strtoupper($transaction_info['currency']);?>" name="display_currency"/>
						<input type="hidden" value="<?php echo "1";?>" name="display_currency_rates"/>
			
						<input type="hidden" value="<?php echo $_SESSION["user"]["email"];?>" name="email"/>
						<input type="hidden" value="<?php echo $_SESSION["user"]["name"];?>" name="name"/>		
						<input type="hidden" value="<?php echo $_SESSION["user"]["name"];?>" name="ship_name"/>
						
						<?php 
						if ((isset($_SESSION["user"]["phone"])) && (strlen($_SESSION["user"]["phone"]) > 5)) { ?>
								<input type="hidden" value="<?php echo $_SESSION["user"]["phone"];?>" name="phone"/>
								<input type="hidden" value="<?php echo $_SESSION["user"]["phone"];?>" name="ship_phone"/>
							<?php } else { ?>
								<input type="hidden" value="9999999999" name="phone"/>
								<input type="hidden" value="9999999999" name="ship_phone"/>
						<?php }
						?>
			
						<?php 
						if ((isset($_SESSION["user"]["city"])) && (strlen($_SESSION["user"]["city"]) > 0)) { ?>
								<input type="hidden" value="<?php echo $_SESSION["user"]["city"];?>" name="city"/>
								<input type="hidden" value="<?php echo $_SESSION["user"]["city"];?>" name="ship_city"/>
							<?php } else { ?>
								<input type="hidden" value="NIL" name="city"/>
								<input type="hidden" value="NIL" name="ship_city"/>
						<?php }
						?>
			
						<?php 
						if ((isset($_SESSION["user"]["state"])) && (strlen($_SESSION["user"]["state"]) > 0)) { ?>
								<input type="hidden" value="<?php echo $_SESSION["user"]["state"];?>" name="state"/>
								<input type="hidden" value="<?php echo $_SESSION["user"]["state"];?>" name="ship_state"/>
							<?php } else { ?>
								<input type="hidden" value="NIL" name="state"/>
								<input type="hidden" value="NIL" name="ship_state"/>
						<?php }
						?>
			
						<?php 
						if ((isset($_SESSION["user"]["country"])) && (strlen($_SESSION["user"]["country"]) > 0)) { ?>
								<input type="hidden" value="<?php echo strtoupper($_SESSION["user"]["country"]);?>" name="country"/>
								<input type="hidden" value="<?php echo strtoupper($_SESSION["user"]["country"]);?>" name="ship_country"/>
							<?php } else { ?>
								<input type="hidden" value="IND" name="country"/>
								<input type="hidden" value="IND" name="ship_country"/>
						<?php }
						?>
			
						<?php 
						if ((isset($_SESSION["user"]["zipcode"])) && (strlen($_SESSION["user"]["zipcode"]) > 0)) { ?>
								<input type="hidden" value="<?php echo $_SESSION["user"]["zipcode"];?>" name="postal_code"/>
								<input type="hidden" value="<?php echo $_SESSION["user"]["zipcode"];?>" name="ship_postal_code"/>
							<?php } else { ?>
								<input type="hidden" value="560075" name="postal_code"/>
								<input type="hidden" value="560075" name="ship_postal_code"/>
						<?php }
						?>
			
						<?php 
						if ((isset($_SESSION["user"]["address"])) && (strlen($_SESSION["user"]["address"]) > 0)) { ?>
								<input type="hidden" value="<?php echo $_SESSION["user"]["address"];?>" name="address"/>
								<input type="hidden" value="<?php echo $_SESSION["user"]["address"];?>" name="ship_address"/>
							<?php } else { ?>
								<input type="hidden" value="NIL" name="address"/>
								<input type="hidden" value="NIL" name="ship_address"/>
						<?php }
						?>
			
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

		exit();
	}

	// This will parse a transaction response and return the response in a standard format - EBS-ized
	// $paylink_info param not used but passed anyways.
	function response_parse($paylink_info) {

		// Check if DR field is set in response
		if ((!isset($_GET["DR"])) || (strlen($_GET["DR"]) == 0)) return false;

		// Decrypt
		load_plugin("rc4");

		$DR = preg_replace("/\s/","+",$_GET['DR']);
		$rc4 = new Crypt_RC4($GLOBALS["jaws_exec_live"] ? JAWS_PAYMENT_GATEWAY_EBS_KEY : JAWS_PAYMENT_GATEWAY_EBS_KEY_TEST);

		$QueryString = base64_decode($DR);
		$rc4->decrypt($QueryString);
		$QueryString = explode('&',$QueryString);

		$response = array();
		foreach($QueryString as $param){
			$param = explode('=',$param);
			$response[$param[0]] = urldecode($param[1]);
		}

		// Parse
		$response["status"] = (strcmp($response["ResponseCode"], "0") == 0) ? true : false;
		$response["reference_id"] = $response["PaymentID"];
		$response["channel_type"] = "";
		$response["channel_info"] = "";

		return $response;

	}

	// This is an internal function used to transact using a form POST
	function ebs_renderform($transaction_info, $return_url, $secure_hash) {

		?>
		<form action="<?php echo "http://188.166.222.75/jaws/view/misc/test"; ?>" name="payment" method="POST">
			<input type="hidden" value="<?php echo JAWS_PAYMENT_GATEWAY_EBS_SID;?>" name="account_id"/>
			<input type="hidden" value="<?php echo $transaction_info['sum'].".00";?>" name="amount"/>
			<input type="hidden" value="0" name="channel"/>
			<input type="hidden" value="<?php echo $transaction_info['extra']['web_id'];?>" name="description"/>
			<input type="hidden" value="<?php echo $transaction_info['invoice_id'];?>" name="reference_no"/>
			<input type="hidden" value="<?php echo $return_url; ?>" name="return_url"/>
			<input type="hidden" value="<?php echo JAWS_PAYMENT_GATEWAY_EBS_MODE;?>" name="mode"/>

			<input type="hidden" value="<?php echo "INR";?>" name="currency"/>
			<input type="hidden" value="<?php echo "INR";?>" name="display_currency"/>
			<input type="hidden" value="<?php echo "1";?>" name="display_currency_rates"/>

			<input type="hidden" value="<?php echo $_SESSION["user"]["email"];?>" name="email"/>
			<input type="hidden" value="<?php echo $_SESSION["user"]["name"];?>" name="name"/>		
			<input type="hidden" value="<?php echo $_SESSION["user"]["phone"];?>" name="phone"/>

			<input type="hidden" value="<?php echo "560075";?>" name="postal_code"/>
			<input type="hidden" value="NIL" name="address"/>
			<input type="hidden" value="<?php echo "NIL";?>" name="city"/>
			<input type="hidden" value="<?php echo "NIL";?>" name="state"/>
			<input type="hidden" value="<?php echo "IND";?>" name="country"/>
		
			<input type="hidden" value="<?php echo $_SESSION["user"]["name"];?>" name="ship_name"/>
			<input type="hidden" value="<?php echo $_SESSION["user"]["phone"];?>" name="ship_phone"/>

			<input type="hidden" value="<?php echo "NIL";?>" name="ship_address"/>
			<input type="hidden" value="<?php echo "NIL";?>" name="ship_city"/>
			<input type="hidden" value="<?php echo "IND";?>" name="ship_country"/>
			<input type="hidden" value="<?php echo "560075";?>" name="ship_postal_code"/>
			<input type="hidden" value="<?php echo "NIL";?>" name="ship_state"/>
			<input type="hidden" value="<?php echo $secure_hash; ?>" name="secure_hash"/>


			<button onclick="document.payment.submit();"> SUBMIT </button>
		</form>

<?php
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