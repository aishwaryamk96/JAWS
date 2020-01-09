<?php

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header('Location: https://www.jigsawacademy.com');
		die();
	}

	// Load stuff
	load_plugin("phpexcel");
	load_module("ui");

	// Init Session
	auth_session_init();

	// Prep
	$login_params["return_url"] = JAWS_PATH_WEB."/paypal";

	// Login Check
	if (!auth_session_is_logged()) {
		ui_render_login_front(array(
			"mode" => "login",
			"return_url" => $login_params["return_url"],
			"text" => "Please login to access this page."
		));
		exit();
	}

	// Priviledge Check
	if (!auth_session_is_allowed("corp.get")) {
		ui_render_msg_front(array(
			"type" => "error",
			"title" => "Jigsaw Academy",
			"header" => "No Tresspassing",
			"text" => "Sorry, but you do not have permission to access this page.<br/>Contact Jigsaw Support team for assistance."
		));
		exit();
	}

	$msg = [
		"Kaeko chiye? Ni milegi...",
		"Ni dunga, jo ukhadana ho ukhad lo",
		"Kahi or jaake nikal lo",
		"Shakal dekhi hai mirror me? Paypal ki report chiye...",
		"Sharam to bech khayi hai na. Aa gaye muuh uthake report maangne",
		false
	];

	if (!isset($_SESSION["mess_with_user"])) {
		$_SESSION["mess_with_user"] = -1;
	}
	$btn_text = "Download Report";
	// if (!in_array($_SESSION["user"]["email"], ["sathya@jigsawacademy.com", "geetika@jigsawacademy.com", "jagruti@jigsawacademy.com"])) {

	// 	$btn_text = "Yay! Bhaagya khul gaye mere to!";

	// 	$retry = 0;
	// 	while(($rand = microtime(true) % count($msg)) == $_SESSION["mess_with_user"]) {

	// 		$retry++;
	// 		if ($retry > 5) {

	// 			$rand = 0;
	// 			break;

	// 		}

	// 	}

	// 	$_SESSION["mess_with_user"]++;

	// }

	//Check if Date is set
	if ((isset($_POST["submit"]))) {

		$data = [];
		$res = json_decode(file_get_contents("https://jigsawacademy.net/app/paypal.php?dedo_bhaiya=ASDqwe!@"), true);
		$courses = $res["courses"];
		$counts = [
			"No. of participants nominated for course",
			"No. of participants who took the preassessment test",
			"No. of participants who passed the preassessment test",
			"No. of participants who failed the preassessment test",
			"No. of partricipants who enrolled for the course",
			"No. of participants who completed the course",
			"No. of participants who took the postassessment test",
			"No. of participants who passed the postassessment test",
			"No. of participants who failed the postassessment test",
		];

		$courses_list = [];
		foreach ($counts as $count) {

			$count_row = ["Count" => $count];
			foreach ($courses as $course_id => $course) {
				$count_row[$course] = 0;
			}

			$summary[] = $count_row;

		}


		$students = $res["users"];
		foreach ($courses as $course_id => $course) {

			$courses_list = [];
			foreach ($students as $user) {

				$user_info = ["Email" => $user["unique_id"], "Name" => $user["name"]];
				if (!empty($user["courses"][$course_id])) {

					$summary[0][$courses[$course_id]]++;

					$user_info["Pre Test taken"] = "N";
					$user_info["Pre Test score"] = "NA";
					$user_info["Post Test taken"] = "N";
					$user_info["Post Test score"] = "NA";
					if (!empty($user["courses"][$course_id]["unlocked"])) {

						$user_info["Course enrolled"] = "Y";
						$summary[4][$courses[$course_id]]++;

					}
					else {
						$user_info["Course enrolled"] = "N";
					}

					if (!empty($user["courses"][$course_id]["pre_assessment"])) {

						$summary[1][$courses[$course_id]]++;
						$user_info["Pre Test taken"] = "Y";
						if ($user["courses"][$course_id]["pre_assessment"][3] == "NS") {
							$user_info["Pre Test score"] = "NS";
						}
						else {

							$user_info["Pre Test score"] = ceil($user["courses"][$course_id]["pre_assessment"][3]);
							if ($user["courses"][$course_id]["pre_assessment"][3] > 70) {
								$summary[2][$courses[$course_id]]++;
							}
							else {
								$summary[3][$courses[$course_id]]++;
							}

						}

					}

					if (!empty($user["courses"][$course_id]["%age"])) {
						$user_info["%age completed"] = ceil($user["courses"][$course_id]["%age"]);
					}
					else {
						$user_info["%age completed"] = 0;
					}

					if (!empty($user["courses"][$course_id]["post_assessment"])) {

						$summary[6][$courses[$course_id]]++;
						$user_info["Post Test taken"] = "Y";
						$user_info["Post Test score"] = ceil($user["courses"][$course_id]["post_assessment"][3]);
						if ($user["courses"][$course_id]["post_assessment"][3] > 70) {
							$summary[7][$courses[$course_id]]++;
						}
						else {
							$summary[8][$courses[$course_id]]++;
						}

					}

					if (!empty($user_info["%age completed"]) && $user_info["%age completed"] == 100 && $user_info["Post Test taken"] == "Y") {
						$summary[5][$courses[$course_id]]++;
					}


					$courses_list[] = $user_info;

				}

			}

			$data[] = ["title" => $course, "data" => $courses_list];

		}

		array_unshift($data, [
			"title" => "Summary",
			"data" => $summary
		]);

		phpexcel_write($data, [
			"title" => "Paypal progress"
			], "Paypal (".date("F j, Y").").xls"
		);

		die;

	}

?>
<html>
<head>
	<title>Paypal Progress Report</title>
	<style>
		body {
			margin: 0px;
		}
		.container {
			display: flex;
			height: 100%;
		}
		.modal {
			margin: auto;
		}
		.head {
			border-bottom: 1px solid #999;
			display: flex;
			justify-content: space-between;
			padding: 10px 20px;
		}
		form {
			display: flex;
			flex-direction: column;
			margin: 10px 20px;
			width: 40vw;
		}
		.row {
			display: flex;
			justify-content: center;
		}
		.row.padded {
			padding-top: 10px;
		}
		.row.right {
			justify-content: flex-end;
		}
	</style>
</head>
<body>
	<div class="container">
		<div class="modal">
			<div class="head">
				<b>Paypal Report</b>
				<a href="https://www.jigsawacademy.com/jaws/logout">Logout</a>
			</div>
			<form method="POST">
				<?php if ($_SESSION["mess_with_user"] == -1 || empty($msg[$_SESSION["mess_with_user"]])) { ?>
					<div class="row padded">
						<input type="submit" value="<?= $btn_text ?>" name="submit">
					</div>
				<?php }
				else { ?>
					<div class="row">
						<label><?= $msg[$_SESSION["mess_with_user"]] ?></label>
					</div>
					<div class="row right">
						<a href="">Report dedo bhaiya...</a>
					</div>
				<?php } ?>
			</form>
		</div>
	</div>
</body>
</html>