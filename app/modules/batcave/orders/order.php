<?php

	function orders_all($asc = true) {

		$orders = [];

		$order = $asc ? "ASC" : "DESC";

		$res = db_query("SELECT * FROM orders ORDER BY id $order;");
		foreach ($res as $order) {
			$orders[] = order_normalize($order);
		}

		return $orders;

	}

	function order_get($id, $type = "id") {

		$id = db_sanitize($id);

		if ($type == "id") {

			$order = db_query("SELECT * FROM orders WHERE id = $id;");
			if (empty($order)) {
				return false;
			}

			return order_normalize($order[0]);

		}
		else {

			$orders = [];
			$type = $type."_id";
			$res = db_query("SELECT * FROM orders WHERE $type = $id ORDER BY id ASC;");
			foreach ($res as $order) {
				$orders[] = order_normalize($order);
			}

			return $orders;

		}

		return false;

	}

	function order_normalize($order) {

		$order["id"] = intval($order["id"]);
		$order["user_id"] = intval($order["user_id"]);
		$order["created_by"] = intval($order["created_by"]);

		$order["payment"] = payments_get($order["id"], "order")[0];

		$order["user"] = user_get($order["user_id"]);
		$order["creator"] = user_get($order["created_by"]);

		return $order;

	}

	// This function expects the payment information associated with the order, as well
	function order_process_new($order) {

		$user = user_find_or_create($order["user"]);

		$order["user_id"] = $user["user_id"];
		$order["created_by"] = $_SESSION["user"]["user_id"];

		$order = order_create($order);
		$order["payment"] = payments_create($order["payment"], $order);
		if (empty($order["payment"])) {

			db_exec("DELETE FROM orders WHERE id = ".$order["id"]);
			$order = false;

		}

		if (!empty($order["notify"])) {
			order_notify_user($order);
		}

		return $order;

	}

	function order_process($order) {

		$order = order_edit($order);
		$order["payment"] = payments_edit($order["payment"], $order);
		if (empty($order["payment"])) {

			db_exec("DELETE FROM orders WHERE id = ".$order["id"]);
			$order = false;

		}

		return $order;

	}

	function order_create($order) {

		$order["user_type"] = "old";
		list($cols, $values) = order_sanitize($order);

		if (!empty($cols)) {

			$cols = implode(", ", $cols);
			$values = implode(", ", $values);

			db_exec("INSERT INTO orders ($cols) VALUES ($values);");
			$order["id"] = db_get_last_insert_id();

		}

		return $order;

	}

	function order_edit($order) {

		$order["user_type"] = "old";
		$set = order_sanitize($order, false);
		if (!empty($set)) {

			$set = implode(", ", $set);
			$id = db_sanitize($order["id"]);

			db_exec("UPDATE order SET $set WHERE id = $id;");

		}

		return $order;

	}

	function order_sanitize($order, $create = true) {

		$attributes = ["user_id", "user_type", "created_by", "description"];

		$cols = [];
		$values = [];

		$set = [];

		foreach ($attributes as $attr) {

			if (!empty($order[$attr])) {

				if ($create) {

					$cols[] = $attr;
					$values[] = db_sanitize($order[$attr]);

				}
				else {
					$set[] = $attr." = ".db_sanitize($order[$attr]);
				}

			}

		}

		if ($create) {
			return [$cols, $values];
		}

		return $set;

	}

	function order_notify_user($order) {

	}

?>