<?php

	function process_request($text, $user_id) {

		if (($response = process_known($text, $user_id)) !== false) {
			return $response;
		}
		else {
			return "I am sorry, I did not understand your query.";
		}

	}

	function process_known($text, $user_id) {

		if ($text == "freeze my access") {

			$_SESSION["context"] = "freeze";
			return process_freeze($user_id);

		}
		else if ($text == "extend my access") {

			$_SESSION["context"] = "access";
			return process_access($user_id);

		}
		else if ($_SESSION["context"] == "freeze") {

			if (($response = process_freeze($user_id, $text)) !== false) {
				return $response;
			}

		}

		return false;

	}

	function process_freeze($user_id, $text = "") {

		if (empty($text)) {

			$freeze = db_query("SELECT * FROM freeze WHERE user_id = $user_id;");
			if (empty($freeze)) {
				return "Sure, please note that we only allow freeze for one month. Please give me the start and end dates for your freeze duration in 'dd/mm/yyyy' format.";
			}
			else {
				return "I see that you have already opted for access freeze previously";
			}

		}
		else {

		}

	}

?>