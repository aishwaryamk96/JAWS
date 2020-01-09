<?php

	load_module("ui");

	// Init Session
	auth_session_init();

	// Login Check
	if (!auth_session_is_logged() || strpos($_SESSION["user"]["email"], "jigsawacademy") === false) {

		ui_render_login_front(array(
			"mode" => "login",
			"return_url" => JAWS_PATH_WEB."/numbers",
			"text" => "Please login to access this page."
		));
		exit();

	}

	if (!empty($_POST["numbers"])) {

		$url = "https://jigsawacademy:8fa2d8a191e5ad114b6770dd82543ef8ee10dc7c@api.exotel.com/v1/Accounts/jigsawacademy/Numbers/";

		libxml_use_internal_errors();

		$data = [];
		$data[] = "Number,Type,DND,Circle,State,Operator";
		$lines = explode(PHP_EOL, $_POST["numbers"]);
		foreach ($lines as $number) {

			$number = trim($number);

			$response = file_get_contents($url.$number);
			if (($xml = simplexml_load_string($response)) === false) {
				continue;
			}

			$data[] = $number.",".$xml->Numbers->Type.",".$xml->Numbers->DND.",".$xml->Numbers->Circle.",".$xml->Numbers->CircleName.",".$xml->Numbers->OperatorName;

		}

		$filename = "external/temp/exotel.csv";
		$file = fopen($filename, "w");
		fwrite($file, implode(PHP_EOL, $data));
		fclose($file);

		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header("Content-disposition: attachment; filename='Numbers.csv'");
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');

		header("Content-length: ".filesize($filename));
		readfile($filename);

		exit;

	}

?>
<html>
<head>
	<title>Phone Number Info Page</title>
</head>
<body>
	<center>
		<form method="post">
			Please input the numbers in the textbox below, one number per line:<br>
			<textarea name="numbers" style="height: 250px"></textarea><br>
			<input type="submit" value="Go!">
		</form>
	</center>
</body>
</html>