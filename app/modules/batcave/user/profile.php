<?php

	// Expects keys: 'email', 'name', 'phone'
	function user_new($user) {

		if (empty($user["email"]) || empty($user["name"])) {
			return false;
		}

		if (empty($user["phone"])) {
			$user["phone"] = "";
		}

		$password = substr(str_shuffle($name.str_replace("@", "0", str_replace(".", "", $user["email"]))), 0, 10);

		$user = user_create($user["email"], $password, $name, $user["phone"]);

	}

	function user_get($id) {

		load_module("user");
		if (is_numeric($id)) {
			return user_get_by_id($id);
		}

		return user_get_by_email($id);

	}

	function user_find_or_create($user) {

		if (!empty($res = user_get($user["email"]))) {
			return $res;
		}

		return user_new($user);

	}

?>