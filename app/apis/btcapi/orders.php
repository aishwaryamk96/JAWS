<?php

	authorize_api_call("", true);

	$order = [];
	if (empty($_GET)) {
		die(json_encode(orders_all(false)));
	}
	else if ($_GET["id"] == "new") {

		$order = [
			"id" => 0,
			"user" => [
				"email" => "",
				"name" => "",
				"phone" => ""
			],
			"description" => "",
			"notify" => true,
			"payment" => [
				"total" => 0,
				"currency" => "inr",
				"installments" => [],
				"status" => "unpaid",
				"channel" => "nb",
				"channel_options" => ["nb" => "NetBanking", "others" => "Online", "all" => "Any"]
			],
			"status" => "draft"
		];

	}
	else {

		if (empty($_POST["order"])) {
			$order = order_get($_GET["id"]);
		}
		else {

			if ($_GET["id"] == 0) {
				$order = order_process_new($_POST["order"]);
			}
			else {
				$order = order_process($_POST["order"]);
			}

			if (empty($order)) {
				die(header("HTTP/1.1 422"));
			}

		}

		$order["payment"]["channel_options"] = ["nb" => "NetBanking", "others" => "Online", "all" => "Any"];

	}

	if (empty($order)) {
		die(header("HTTP/1.1 404"));
	}

	die(json_encode($order));

?>