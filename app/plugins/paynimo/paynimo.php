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

    class Paynimo {

        protected $api_url = 'https://www.paynimo.com/api/paynimoV2.req';
        protected $merchantId = 'T454293';

        function __construct($apiKey, $apiSecret)
        {
            $this->apiKey = $apiKey;
            $this->apiSecret = $apiSecret;
        }

        public function confirm($currency, $date, $token){
            $request = array(
                "merchant" => array(
                    "identifier" => $this->merchantId,
                ),
                "transaction" => array(
                    "deviceIdentifier" => "S",
                    "currency" => strtoupper($currency),
                    "dateTime" => $date,
                    "token" => $token,
                    "requestType" => "S"
                )
            );

            return json_decode($this->api(json_encode($request)), true);
        }

        private function api($data) {
            $headers = ["Content-Type: application/json"];

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $this->api_url);
            curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl);
            if (!$response) {
                $this->curlError = curl_error($curl);
            }
            return $response;
        }
    }

?>