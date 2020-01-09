<?php

	function payments_get($id, $type = "id") {

		$id = db_sanitize($id);

		if ($type == "id") {

			$payment = db_query("SELECT * FROM payments WHERE id = $id;");
			if (empty($payment)) {
				return false;
			}

			return payment_normalize($payment[0]);

		}
		else {

			$payments = [];
			$type = $type."_id";
			$res = db_query("SELECT * FROM payments WHERE $type = $id ORDER BY id ASC;");
			foreach ($res as $payment) {
				$payments[] = payment_normalize($payment);
			}

			return $payments;

		}

		return false;

	}

	function payment_normalize($payment) {

		$payment["id"] = intval($payment["id"]);
		$payment["user_id"] = intval($payment["user_id"]);
		$payment["order_id"] = intval($payment["order_id"]);
		$payment["created_by"] = intval($payment["created_by"]);

		$payment["total"] = floatval($payment["total"]);

		$payment["installments"] = installments_get($payment["id"], "payment");

		return $payment;

	}

	function payments_create($payment, $order) {

		$payment["user_id"] = $order["user_id"];
		$payment["user_type"] = $order["user_type"];
		$payment["order_id"] = $order["id"];
		$payment["created_by"] = $order["created_by"];

		$payment["token"] = payments_generate_token($payment);
		$payment = payments_deduce_from_installments($payment);
		if (empty($payment["total"])) {

			$data = db_sanitize(json_encode($payment));
			db_exec("INSERT INTO system_log (source, data) VALUES ('pay.total.0', $data);");

			return false;

		}

		list($cols, $values) = payments_sanitize($payment);
		if (!empty($cols)) {

			$cols = implode(", ", $cols);
			$values = implode(", ", $values);

			db_exec("INSERT INTO payments ($cols) VALUES ($values);");
			$payment["id"] = db_get_last_insert_id();

			$payment["installments"] = installments_create($payment);

			return $payment;

		}
		else {

			$data = db_sanitize(json_encode(["cols" => $cols, "values" => $values]));
			db_exec("INSERT INTO system_log (source, data) VALUES ('pay.total.0', $data);");

		}

		return false;

	}

	function payments_edit($payment, $order) {

		$payment["user_id"] = $order["user_id"];
		$payment["user_type"] = $order["user_type"];
		$payment["order_id"] = $order["id"];
		$payment["created_by"] = $order["created_by"];

		$payment = payments_deduce_from_installments($payment);
		if (empty($payment["total"])) {

			$data = db_sanitize(json_encode($payment));
			db_exec("INSERT INTO system_log (source, data) VALUES ('pay.total.0', $data);");

			return false;

		}

		$set = payments_sanitize($payment, false);
		if (!empty($set)) {

			$set = implode(", ", $set);
			$id = db_sanitize($payment["id"]);

			db_exec("UPDATE payments SET $set WHERE id = $id;");

			$payment["installments"] = installments_edit($payment);

			return $payment;

		}
		else {

			$data = db_sanitize(json_encode(["set" => $set]));
			db_exec("INSERT INTO system_log (source, data) VALUES ('pay.total.1', $data);");

		}

		return false;

	}

	function payments_sanitize($payment, $create = true) {

		$attributes = ["user_id", "user_type", "order_id", "total", "currency", "token", "channel", "link_status", "status", "expires_at", "created_by"];

		$cols = [];
		$values = [];

		$set = [];
		foreach ($attributes as $attr) {

			if (isset($payment[$attr])) {

				if ($attr == "channel") {

					if (!in_array($payment[$attr], ["nb", "others", "all"])) {
						return $create ? ["", ""] : false;
					}

				}
				elseif ($attr == "status") {

					if (!in_array($payment[$attr], ["unpaid", "partial", "paid"])) {
						return $create ? ["", ""] : false;
					}

				}

				if ($create) {

					$cols[] = $attr;
					$values[] = db_sanitize($payment[$attr]);

				}
				else {
					$set[] = $attr." = ".db_sanitize($payment[$attr]);
				}

			}

		}

		if ($create) {
			return [$cols, $values];
		}

		return $set;

	}

	function payments_generate_token($payment) {

		$secure_random_string = bin2hex(openssl_random_pseudo_bytes(16));

		$patterned_random_string = $payment["user_id"].$payment["order_id"].time();

		$salt = bin2hex(openssl_random_pseudo_bytes(8));

		return hash_hmac("sha256", $patterned_random_string."@#$%^&".$secure_random_string, $salt);

	}

	function payments_deduce_from_installments($payment) {

		$total = 0;
		$expires_at = false;
		$installments_prepared = [];

		if (!empty($payment["installments"])) {

			foreach ($payment["installments"] as $instl) {

				if (!empty($instl["due_by"]) && empty($expires_at)) {
					$expires_at = $instl["due_by"];
				}

				$instl = installment_prepare_amounts($instl);
				$total += $instl["total"];

				if (!empty($instl["total"])) {
					$installments_prepared[] = $instl;
				}

			}

		}

		$payment["installments"] = $installments_prepared;

		if (!empty($expires_at)) {
			$payment["expires_at"] = $expires_at;
		}

		$payment["total"] = $total;

		return $payment;

	}

?>