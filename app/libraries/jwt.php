<?php

	$GLOBALS["jwt_fwd_hashing"] = [
		"sha256" => "HS256",
	];

	$GLOBALS["jwt_rvs_hashing"] = [
		"HS256" => "sha256",
	];

	function base64_json_decode($str) {
		return json_decode(base64_url_decode($str), true);
	}

	function base64_json_encode($arr) {
		return base64_url_encode(json_encode($arr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
	}

	function base64_url_decode($str) {
		return base64_decode(str_pad(strtr($str, '-_', '+/'), strlen($str) % 4, '=', STR_PAD_RIGHT));
	}

	function base64_url_encode($str) {
		return str_replace("=", "", strtr(base64_encode($str), '+/', '-_'));
	}

	function jwt_decode($token, $secret) {

		$components = explode(".", $token);
		if (count($components) != 3) {

			trigger_error("Invalid JWT token", E_USER_NOTICE);
			return false;

		}

		$header_encoded = $components[0];
		$payload_encoded = $components[1];
		$hash_str = $components[2];

		$header = base64_json_decode($header_encoded);
		if (empty($header["typ"]) || $header["typ"] != "JWT") {

			trigger_error("Token does not contain valid header", E_USER_NOTICE);
			return false;

		}
		if (empty($header["alg"]) || empty($GLOBALS["jwt_rvs_hashing"][$header["alg"]])) {

			trigger_error("Unrecognized hashing method '".$header["alg"]."' found in JWT header", E_USER_NOTICE);
			return false;

		}

		$data = $header_encoded.".".$payload_encoded;
		$calculated_hash = base64_url_encode(hash_hmac($GLOBALS["jwt_rvs_hashing"][$header["alg"]], $data, $secret, true));
		if ($calculated_hash != $hash_str) {

			trigger_error("JWT payload validation failed", E_USER_NOTICE);
			return false;

		}

		return base64_json_decode($payload_encoded);

	}

	function jwt_encode($payload, $secret, $hash = "sha256") {

		if (empty($GLOBALS["jwt_fwd_hashing"][$hash])) {

			trigger_error("Unrecognized hashing method '$hash'.", E_USER_NOTICE);
			return false;

		}

		$header = [
			"alg" => $GLOBALS["jwt_fwd_hashing"][$hash],
			"typ" => "JWT"
		];

		$header_encoded = base64_json_encode($header);
		$payload_encoded = base64_json_encode($payload);

		$data = $header_encoded.".".$payload_encoded;

		$hash_str = hash_hmac($hash, $data, $secret, true);

		return $data.".".base64_url_encode($hash_str);

	}

?>