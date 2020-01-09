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


    // ATOM Payment Gateway
    // ---------------------

    // The following functions are mandatory to all plugins that implement a payment gateway
    // They are called by the payment library and must be present for the plugin to work as a gateway
   	// ---------------

    // This will transact with the gateway - atom-ized function
    function transact($transaction_info, $validatetoken) {

    	// Prep first request - cURL
		$postFields = "&login=".JAWS_PAYMENT_GATEWAY_ATOM_SID;
		$postFields .= "&pass=".JAWS_PAYMENT_GATEWAY_ATOM_KEY;
		$postFields .= "&ttype=NBFundTransfer";
		$postFields .= "&prodid=".JAWS_PAYMENT_GATEWAY_ATOM_PRODID;
		$postFields .= "&amt=".$transaction_info['sum'].".00";
		$postFields .= "&txncurr=".strtoupper($transaction_info['currency']);
		$postFields .= "&txnscamt=0";
		$postFields .= "&clientcode=".urlencode(base64_encode($_SESSION["user"]["user_id"]));
		$postFields .= "&txnid=".$transaction_info['invoice_id'];
		$postFields .= "&date=".str_replace(" ", "%20", date("d/m/Y h:m:s")); // <===================================== remove h:m:s ???????
		$postFields .= "&custacc=1234567890";
		$postFields .= "&ru=".$transaction_info['return_url'].(strpos($transaction_info['return_url'], "?") ? "%26" : "?")."validate=".$validatetoken;
		if ((isset($_SESSION["user"]["name"])) && (strlen($_SESSION["user"]["name"]) > 0)) $postFields .= "&udf1=".str_replace(" ", "", $_SESSION["user"]["name"]);  
		$postFields .= "&udf2=".$_SESSION["user"]["email"];
		if ((isset($_SESSION["user"]["phone"])) && (strlen($_SESSION["user"]["phone"]) > 0)) $postFields .= "&udf3=".$_SESSION["user"]["phone"];  

		// Exec first request to obtain token
		$curl_obj = curl_init();
		curl_setopt($curl_obj, CURLOPT_URL, JAWS_PAYMENT_GATEWAY_ATOM_URL);
		curl_setopt($curl_obj, CURLOPT_HEADER, 0);
		curl_setopt($curl_obj, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_obj, CURLOPT_POST, 1);
		curl_setopt($curl_obj, CURLOPT_PORT , 443); 
		curl_setopt($curl_obj, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl_obj, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl_obj, CURLOPT_POSTFIELDS, $postFields);

		$returnData = curl_exec($curl_obj);
		curl_close($curl_obj);	

		// Parse first request for token
		$xmlObjArray = xmltoarray($returnData);

		// Check
		//if ((!isset($xmlObjArray['tempTxnId'])) || (strlen(strval($xmlObjArray['tempTxnId'])) == 0)) return false;

		// Prep second request
		$url = $xmlObjArray['url'];
		$postFields;
		$postFields .= "&ttype=NBFundTransfer";
		$postFields .= "&tempTxnId=".$xmlObjArray['tempTxnId'];
		$postFields .= "&token=".$xmlObjArray['token'];
		$postFields .= "&txnStage=1";

		// Exec final request
		$url = JAWS_PAYMENT_GATEWAY_ATOM_URL."?".$postFields;
		header("Location: ".$url);

    }

	// This will parse a transaction response and return the response in a standard format - atomi-zed
	function response_parse() {

		// Check if all POST fields are set in responce
		if ((!isset($_POST["f_code"])) || (!isset($_POST["mmp_txn"]))  || (!isset($_POST["discriminator"])) || (!isset($_POST["CardNumber"]))) return false;

		$response["status"] = (strcmp($_POST["f_code"], "Ok") == 0) ? true : false;
		$response["reference_id"] = $_POST["mmp_txn"];
		$response["channel_type"] = $_POST["discriminator"];
		$response["channel_info"] = $_POST["CardNumber"];

		return $response;

	}

	// The following functions are specific to the plugin for it's internal working
	// ---------------

	// This parses the XML query returned on the first request for sedning to the second request
    function xmltoarray($data){
		$parser = xml_parser_create('');
		xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); 
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parse_into_struct($parser, trim($data), $xml_values);
		xml_parser_free($parser);
		
		$returnArray = array();
		$returnArray['url'] = $xml_values[3]['value'];
		$returnArray['tempTxnId'] = $xml_values[5]['value'];
		$returnArray['token'] = $xml_values[6]['value'];

		return $returnArray;
	}


?>