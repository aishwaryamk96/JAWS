<?php

	if (empty($_SERVER["argv"][2])) {
		die("USAGE: <table_name>:<column_name> <where_column>:<is_equal_to_value>\nExample: user:email user_id:12345");
	}

	list($table, $column) = explode(":", $_SERVER["argv"][2]);
	list($where, $equal) = explode(":", $_SERVER["argv"][3]);

	$res = db_query("SELECT $column FROM $table WHERE $where = ".db_sanitize($equal));
	if (empty($res)) {
		var_dump($res);
	}
	else if (count($res) == 1) {
		var_dump($res[0][$column]);
	}
	else {
		var_dump($res);
	}

?>