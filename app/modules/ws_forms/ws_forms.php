<?php

	function ws_forms_log($form, $submission, $tracking) {

		if (empty($form = ws_form_get($form))) {
			return false;
		}

		$form_id = db_sanitize($form["id"]);

		$name = "NULL";
		if (!empty($submission["name"])) {
			$name = db_sanitize($submission["name"]);
		}

		$email = "NULL";
		if (!empty($submission["email"])) {
			$email = db_sanitize($submission["email"]);
		}

		$phone = "NULL";
		if (!empty($submission["phone"])) {
			$phone = db_sanitize($submission["phone"]);
		}

		$country_code = "NULL";
		if (!empty($submission["country_code"])) {
			$country_code = db_sanitize($submission["country_code"]);
		}

		$submission = db_sanitize(json_encode($submission));

		$__tr = db_sanitize($tracking["__tr"]);
		$__se = db_sanitize($tracking["__se"]);

		db_exec("INSERT INTO ws_form_submissions (ws_form_id, name, email, phone, country_code, meta, __tr, __se) VALUES ($form_id, $name, $email, $phone, $country_code, $submission, $__tr, $__se);");

	}

	function ws_form_get($slug) {

		$slug = db_sanitize($slug);
		$form = db_query("SELECT * FROM ws_forms WHERE slug = $slug;");
		if (empty($form)) {
			return false;
		}

		return $form[0];

	}

?>