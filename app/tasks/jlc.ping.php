<?php

	$ch = curl_init('http://www.example.com/');

	// Execute
	curl_exec($ch);

	// Check HTTP status code
	if (!curl_errno($ch)) {

		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($http_code != 200) {

		}

	}

	// Close handle
	curl_close($ch);

?>