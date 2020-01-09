<?php

	$user_not_found = [];
	$subs_not_found = [];
	$payment_not_found = [];
	$payment_found = [];

	$lines = file("external/temp/pgpdm.list.csv");
	foreach ($lines as $line) {

		$i = explode(",", $line);

		$email = trim($i[1]);
		$user = user_get_by_email($email);
		if (empty($user)) {

			// Create the user
			$user_not_found[] = $email;
			continue;

		}

		$sis_id = db_sanitize(trim($i[0]));
		$enr = db_query("SELECT * FROM user_enrollment WHERE sis_id = $sis_id AND course_id = 62 AND status = 'active';");
		if (!empty($enr)) {
			$subs_id = $enr[0]["subs_id"];
		}
		else {

			// Create the subs
			$subs_not_found[] = [$email, $user["user_id"]];
			continue;

		}

		$pay_id = "";
		$subs_id = db_sanitize($subs_id);
		$pay = db_query("SELECT * FROM payment WHERE subs_id = $subs_id;");
		if (empty($pay)) {

			// Create the payment record
			$payment_not_found[] = [$email, $subs_id];
			continue;

		}
		else {

			$payment_found[] = [$email, $pay[0]["pay_id"]];
			continue;

		}

	}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Blah</title>
</head>
<body>
	<center>
		<h2>Users not found</h2>
		<?php foreach ($user_not_found as $user) { ?>
			<div><?= $user ?></div>
		<?php } ?>
		<h2>Subs not found</h2>
		<?php foreach ($subs_not_found as $user) { ?>
			<div><?= $user[0]." ".$user[1] ?></div>
		<?php } ?>
		<h2>Payment not found</h2>
		<?php foreach ($payment_not_found as $pay) { ?>
			<div><?= $pay[0]." ".$pay[1] ?></div>
		<?php } ?>
		<h2>Payment found</h2>
		<?php foreach ($payment_found as $pay) { ?>
			<div><?= $pay[0]." ".$pay[1] ?></div>
		<?php } ?>
	</center>
</body>
</html>