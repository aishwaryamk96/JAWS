<?php

	function careers_get() {

		return db_query(
			"SELECT
				c.*,
				DATE_FORMAT(c.created_at, '%D %b, %Y') AS created_at,
				u.name AS created_by
			FROM
				careers AS c
			LEFT JOIN
				user AS u
				ON u.user_id = c.created_by
			WHERE
				c.status = 'active'
			ORDER BY created_at DESC;"
		);

	}

	function career_get($id) {

		$id = db_sanitize($id);

		return db_query(
			"SELECT
				c.*,
				DATE_FORMAT(c.created_at, '%D %b, %Y') AS created_at,
				u.name AS created_by
			FROM
				careers AS c
			LEFT JOIN
				user AS u
				ON u.user_id = c.created_by
			WHERE
				c.id = $id
			ORDER BY created_at DESC;"
		);

	}

	function career_add($job) {

		$job = sanitize($job);
		if (empty($job["title"]) || $job["title"] == "NULL" || empty($job["company"]) || $job["company"] == "NULL") {
			return false;
		}

		$job["status"] = "'active'";

		$columns = implode(", ", array_keys($job));
		$values = implode(", ", array_values($job));

		db_exec("INSERT INTO careers ($columns) VALUES ($values);");
		return db_get_last_insert_id();

	}

	function career_remove($id) {
		return db_exec("UPDATE careers SET status = 'deleted' WHERE id = ".db_sanitize($id).";");
	}

	function sanitize($job) {

		$fields = [
			"title" => "NULL",
			"company" => "NULL",
			"location" => "NULL",
			"role" => "NULL",
			"tools" => "NULL",
			"description" => "NULL",
			"vacancies" => "NULL",
			"created_by" => "NULL",
			"code" => db_sanitize(hash("sha256", time())),
			"submit_by" => "NULL"
		];

		$sanitized = [];
		$unsanitized = [];
		foreach ($fields as $key => $value) {

			if (!empty($job[$key])) {

				if ($key == "tools") {
					$job[$key] = json_encode($job["tools"]);
				}

				$value = $job[$key];
				$unsanitized[$key] = $value;

			}

			$sanitized[$key] = db_sanitize($value);

		}

		if (($html = generate_html($unsanitized)) === false) {
			return false;
		}

		$sanitized["html"] = db_sanitize($html);

		return $sanitized;

	}

	function generate_html($fields) {

		$html = "";
		if (!empty($fields["company"])) {
			$html = "<p><strong>Company Name</strong> : ".$fields["company"]."</p>";
		}

		if (!empty($fields["role"])) {
			$html .= "<p><strong>Role</strong> : ".$fields["role"]."</p>";
		}

		if (!empty($fields["tools"])) {
			$html .= "<p><strong>Tools</strong> : ".implode(", ", json_decode($fields["tools"], true))."</p>";
		}

		if (!empty($fields["experience"])) {
			$html .= "<p><strong>Experience</strong> : ".$fields["experience"]."</p>";
		}

		if (!empty($fields["code"])) {
			$html .= "<p><strong>Job code</strong> : ".$fields["code"]."</p>";
		}

		if (!empty($fields["vacancies"])) {
			$html .= "<p><strong>Vacancies</strong> : ".$fields["vacancies"]."</p>";
		}

		if (!empty($fields["description"])) {
			$html .= "<p>".$fields["description"]."</p>";
		}

		$submit_by = "";
		if (!empty($fields["submit_by"])) {

			$submit_by = date_create_from_format("Y-m-d", $fields["submit_by"]);
			if ($submit_by === false) {
				return false;
			}

			$submit_by = " on or before <b>".$submit_by->format("jS M")."</b>";

		}

		$html .= "<p>Interested applicants are expected to share their updated <b>CV<b>$submit_by at <b>Placement Support<b>.</p>";
		$html .= "<p><em>Regards,</em><br>";
		$html .= "<b>Team Jigsaw</b></p>";

		return $html;

	}

?>