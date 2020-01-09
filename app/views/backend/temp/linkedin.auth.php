<?php

	session_start();

	$client_id = "81wzatdlzo1p9v";
	$client_secret = "CNqZwlgimOmkSMkI";

	if (empty($_SESSION["access_token"])) {

		if (isset($_GET["error"])) {

			echo "<b>Error received from LinkedIn:</b><br>";
			echo "<b>Error code:</b> ".$_GET["error"]."<br>";
			echo "<b>Error description:</b> ".$_GET["error_description"];

		}

		if (empty($_GET["code"])) { ?>
			<a href="https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id=<?php echo $client_id ?>&redirect_uri=<?php echo urlencode("https://www.jigsawacademy.com/jaws/auth/linkedin") ?>&state=987654321&scope=r_ads_leadgen_automation">LinkedIn!</a>
		<?php }
		else {

			$post_data = [
				"grant_type" => "authorization_code",
				"code" => $_GET["code"],
				"redirect_uri" => "https://www.jigsawacademy.com/jaws/auth/linkedin",
				"client_id" => $client_id,
				"client_secret" => $client_secret
			];

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $this->lmsUrl."api/v1/accounts/1/".$api);
			curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER, ["Content-type: application/x-www-form-urlencoded"]);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post_data));
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

			$response = curl_exec($curl);
			if (!$response) {
				$this->curlError = curl_error($curl);
			}

			var_dump(json_decode($response));

		}

	}
	else {
		echo "Yo!";
	}

?>