<?php

	load_module("user");

	$students = [];
	$not_found = [];

	$lines = file("external/temp/bootcamp.csv");
	foreach ($lines as $email) {

		$user = user_get_by_email(trim($email));
		$subs = db_query("SELECT subs.subs_id, subs.combo_free, DATE(subs.end_date) AS end_date, bb.id, bb.start_date, bb.end_date AS bb_end_date FROM subs INNER JOIN subs_meta AS m ON m.subs_id = subs.subs_id INNER JOIN bootcamp_batches AS bb ON bb.id = m.batch_id WHERE subs.start_date IS NOT NULL AND m.batch_id IS NOT NULL AND subs.user_id = ".$user["user_id"]);

		if (empty($subs)) {

			$not_found[] = $email;
			continue;

		}

		$subs = $subs[0];
		$students[] = ["email" => $email, "subs_id" => $subs["subs_id"], "end_date" => $subs["end_date"], "bb_start_date" => $subs["start_date"], "bb_end_date" => $subs["bb_end_date"], "free" => $subs["combo_free"], "bb_id" => $subs["id"]];

	}

?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style>
		.container {
			display: flex;
			flex-direction: column;
		}
		.row {
			display: flex;
			padding: 5px 0px;
			width: 100%;
		}
		.row:nth-child(odd) {
			background-color: #eee;
		}
		.header {
			font-weight: bold;
		}
		.column {
			display: flex;
			width: 20%;
		}
		.column:not(:first-child) {
			justify-content: center;
		}
	</style>
</head>
<body>
	<div class="container">
		<div class="row header">
			<div class="column">Email</div>
			<div class="column">Subs ID</div>
			<div class="column">Complimentary</div>
			<div class="column">End Date</div>
			<div class="column">End Date Correct?</div>
			<div class="column">Batch ID</div>
			<div class="column">Batch Duration</div>
		</div>
		<?php foreach ($students as $student) { ?>
			<div class="row">
				<div class="column"><?= $student["email"] ?></div>
				<div class="column"><?= $student["subs_id"] ?></div>
				<div class="column"><?= $student["free"] ?></div>
				<div class="column"><?= $student["end_date"] ?></div>
				<div class="column">
					<?php if (!empty($student["free"])) {
						echo $student["end_date"] == "2019-02-06" ? "Y" : "N";
					}
					else {
						echo $student["end_date"] == $student["bb_end_date"] ? "Y" : "N";
					} ?>
				</div>
				<div class="column"><?= $student["bb_id"] ?></div>
				<div class="column"><?= $student["bb_start_date"]." === ".$student["bb_end_date"] ?></div>
			</div>
		<?php }
		foreach ($not_found as $user) { ?>
			<div class="row">
				<div class="column">Not Found</div>
				<div class="column"><?= $user ?></div>
				<div class="column"></div>
				<div class="column"></div>
				<div class="column"></div>
			</div>
		<?php } ?>
	</div>
</body>
</html>