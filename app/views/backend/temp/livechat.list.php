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

	// Login Check
	if (!auth_session_is_logged()) {
		ui_render_login_front([
			"mode" => "login",
			"return_url" => JAWS_PATH_WEB."/livechat",
			"text" => "Please login to access this page."
		]);
		exit();
	}

	// Priviledge Check
	if (!auth_session_is_allowed("leads.get")) {
		ui_render_msg_front([
			"type" => "error",
			"title" => "Jigsaw Academy",
			"header" => "No Tresspassing",
			"text" => "Sorry, but you do not have permission to access this page.<br/>Contact Jigsaw Support team for assistance."
		]);
		exit();
	}

	$msg = "";
	$fromdate = date("d-m-Y");
	$todate = date("d-m-Y");

	if (isset($_POST["fromdate"])) {

		$fromdate = $_POST["fromdate"];
		$todate;
		if (empty($_POST["todate"])) {
			$todate = (new DateTime)->format("Y-m-d");
		}
		else {
			$todate = $_POST["todate"];
		}

		$chats = [];
		$info = [];
		$pages = $page = 1;
		$transcript = [];
		while ($page <= $pages) {

			$url = "https://api.livechatinc.com/chats?date_from=".$fromdate."&date_to=".$todate."&page=".$page;

			$curl = curl_init();
			curl_setopt_array($curl, [
				CURLOPT_URL => $url,
				CURLOPT_HTTPHEADER => ["X-API-Version:2"],
				CURLOPT_USERPWD => "chat@jigsawacademy.com:a023110919cb4c2e52335aec9ec81aed",
				CURLOPT_RETURNTRANSFER => true
			]);

			$response = curl_exec($curl);
			curl_close($curl);

			$all = json_decode($response, true);
			if ($page == 1) {

				$pages = $all["pages"];
				// if ($pages > 10) {

				// 	$msg = [$pages, $all["total"]];
				// 	break;

				// }

			}
			$page++;

			foreach ($all["chats"] as $chat) {

				$agents_name = [];
				$agents_email = [];
				foreach ($chat["agents"] as $agent) {

					$agents_name[] = $agent["display_name"];
					$agents_email[] = $agent["email"];

				}

				$phone = "N/A";
				if (!empty($chat["prechat_survey"])) {

					foreach ($chat["prechat_survey"] as $prechat_survey) {

						if ($prechat_survey["key"] == "Phone Number") {
							$phone = $prechat_survey["value"];
						}

					}

				}

				$events = [];
				foreach ($chat["events"] as $event) {

					$each = [];

					$each[$event["user_type"]] = $event["text"];
					if ($event["type"] == "attachment") {
						$each["attachment"] = $event["files"];
					}

					$events[] = $each;

				}

				$info[] = [
					"Chat ID" => $chat["id"],
					"ID" => $chat["visitor_id"],
					"Name" => $chat["visitor_name"],
					"Email" => $chat["visitor"]["email"] ?? "N/A",
					"Phone" => $phone,
					"IP" => $chat["visitor_ip"],
					"City" => $chat["visitor"]["city"],
					"Region" => $chat["visitor"]["region"],
					"Country" => $chat["visitor"]["country"],
					"Agent Name" => implode(";",$agents_name),
					"Agent Email" => implode(";",$agents_email),
					"Chat Start Url" => $chat["chat_start_url"],
					"Referrer" => $chat["referrer"],
					"Started At" => $chat["started"],
					"Ended At" => $chat["ended"],
					"Transcript" => json_encode($events)
				];

			}

		}

		if (empty($msg)) {

			try {
				phpexcel_write([["title" => "Chats Basic", "data" => $info]], ["title" => "Livechat List"], "Livechat (".date("F j, Y").").xls");
			}
			catch (Exception $e) {
				var_dump($e);
			}
			die;

		}

	}

?>
<html>
<head>
	<title>Livechat List</title>
</head>
<body>
	<center>
		<b>Livechat List</b>&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: gray;">Logged in as <?php echo $_SESSION["user"]["name"]; ?>!</span>&nbsp;<a href="https://www.jigsawacademy.com/jaws/logout">Logout</a>
		<hr>
		<?php if (!empty($msg)) { ?>
			<b style="color: red;">There are <?php echo $msg[0] ?> pages, amounting to <?php echo $msg[1] ?> chats, for the selected date range. Please try reducing the date range.</b>
			<hr>
		<?php } ?>
		<form method="POST" action="https://www.jigsawacademy.com/jaws/livechat" style="text-align: left; width: 50vw; margin: 0 auto;">
			From Date : <input type="date" required name="fromdate" max="<?php echo date("d-m-Y"); ?>" value="<?php echo $fromdate; ?>"><br/>
			To Date : <input type="date" name="todate" max="<?php echo date("d-m-Y"); ?>" value="<?php echo $todate; ?>"><br/><br/>
			<input type="submit" value="Download Report"><br/><br/>
			<span style="color: gray;">Warning : Selecting a large range may not function properly. Try to keep the range within 20 days.</span>
		</form>
	</center>
</body>
</html>