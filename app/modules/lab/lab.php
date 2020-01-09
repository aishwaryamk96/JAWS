<?php

	function lab_add($ami) {

		$ami_id = db_sanitize($ami["ami_id"]);
		$meta = db_sanitize(json_encode($ami["meta"]));
		$type = db_sanitize($ami["type"]);
		$created_by = db_sanitize($ami["created_by"]);

		if (!empty($ami["id"])) {
			$result = db_exec("UPDATE labs SET ami_id = $ami_id, meta = $meta, type = $type WHERE id = ".$ami["id"]);
		}
		else {
			$result = db_exec("INSERT INTO labs (ami_id, meta, type, created_by) VALUES ($ami_id, $meta, $type, $created_by);");
		}

		if (!$result) {
			return false;
		}

		$ami["id"] = db_get_last_insert_id();
		return $ami;

	}

	function labs_get() {
		return db_query("SELECT * FROM labs ORDER BY id;");
	}

?>