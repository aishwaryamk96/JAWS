<?php

	function installments_get($id, $type = "id") {

		$id = db_sanitize($id);

		if ($type == "id") {

			$instl = db_query("SELECT * FROM installments WHERE id = $id;");
			if (empty($instl)) {
				return false;
			}

			return installment_normalize($instl[0]);

		}
		else {

			$installments = [];
			$type = $type."_id";
			$res = db_query("SELECT * FROM installments WHERE $type = $id ORDER BY position ASC;");
			foreach ($res as $instl) {
				$installments[] = installment_normalize($instl);
			}

			return $installments;

		}

		return false;

	}

	function installment_normalize($instl) {

		$instl["id"] = intval($instl["id"]);
		$instl["payment_id"] = intval($instl["payment_id"]);
		$instl["position"] = intval($instl["position"]);
		$instl["created_by"] = intval($instl["created_by"]);

		$instl["amount"] = floatval($instl["amount"]);
		$instl["discount"] = floatval($instl["discount"]);
		$instl["tax"] = floatval($instl["tax"]);
		$instl["instl_fees"] = floatval($instl["instl_fees"]);
		$instl["late_fees"] = floatval($instl["late_fees"]);
		$instl["total"] = floatval($instl["total"]);

		$comments = json_decode($instl["comments"] ?? "{}");
		$instl["comments"] = [];
		foreach ($comments as $user => $text) {

			$instl["comments"][] = [
				"creator" => user_get($user),
				"text" => $text
			];

		}

		$instl["channel_meta"] = json_decode($instl["channel_meta"] ?? "{}");

		return $instl;

	}

	// This function expects the entire payments object so
	// that several independent params are not required
	function installments_create($payment) {

		$prepared_instls = [];

		$installments = $payment["installments"];
		foreach ($installments as $i => $instl) {

			if (!empty($instl["total"])) {
				$prepared_instls[] = installment_create($instl, $i, $payment);
			}

		}

		return $prepared_instls;

	}

	function installments_edit($payment) {

		$prepared_instls = [];

		$installments = $payment["installments"];
		foreach ($installments as $i => $instl) {

			if (!empty($instl["total"])) {
				$prepared_instls[] = installment_edit($instl, $i, $payment);
			}

		}

		return $prepared_instls;

	}

	function installment_prepare_amounts($installment) {

		$installment["total"] = 0;
		if (empty($installment["amount"])) {
			return $installment;
		}

		$installment["amount"] = floatval($installment["amount"] ?? 0);

		if (empty($installment["discount"])) {
			$installment["discount"] = 0;
		}
		else {
			$installment["discount"] = floatval($installment["discount"]);
		}
		$installment["total"] = $installment["amount"] - $installment["discount"];

		if (empty($installment["instl_fees"])) {
			$installment["instl_fees"] = 0;
		}
		else {
			$installment["instl_fees"] = floatval($installment["instl_fees"]);
		}
		$installment["total"] += $installment["instl_fees"];

		if (empty($installment["tax"])) {
			$installment["tax"] = 0;
		}
		else {
			$installment["tax"] = floatval($installment["tax"]);
		}
		$installment["tax_amount"] = round($installment["total"] * $installment["tax"] / 100);
		$installment["total"] += $installment["tax_amount"];
		$installment["total"] = round($installment["total"]);

		if (empty($installment["late_fees"])) {
			$installment["late_fees"] = 0;
		}
		else {
			$installment["late_fees"] = floatval($installment["late_fees"]);
		}

		return $installment;

	}

	function installment_create($installment, $i, $payment) {

		$installment["payment_id"] = $payment["id"];
		$installment["position"] = $i;

		if (empty($installment["created_by"])) {
			$installment["created_by"] = $payment["created_by"];
		}

		if (!empty($installment["comments"]) && !is_array($installment["comments"])) {
			$installment["comments"] = [$payment["created_by"] => $installment["comments"]];
		}

		list($cols, $values) = installment_sanitize($installment);
		if (!empty($cols)) {

			$cols = implode(", ", $cols);
			$values = implode(", ", $values);

			db_exec("INSERT INTO installments ($cols) VALUES ($values);");
			$installment["id"] = db_get_last_insert_id();

		}

		return $installment;

	}

	function installment_edit($installment, $i, $payment) {

		if ($installment["status"] == "paid") {
			return installment;
		}

		$installment["payment_id"] = $payment["id"];
		$installment["position"] = $i;

		if (empty($installment["created_by"])) {
			$installment["created_by"] = $payment["created_by"];
		}

		if (!empty($installment["comments"]) && !is_array($installment["comments"])) {
			$installment["comments"] = [$payment["created_by"] => $installment["comments"]];
		}

		$set = installment_sanitize($installment, false);
		if (!empty($set)) {

			$set = implode(", ", $set);
			$id = db_sanitize($installment["id"]);

			db_exec("UPDATE installments SET $set WHERE id = $id;");

		}

		return $installment;

	}

	function installment_sanitize($installment, $create = true) {

		$attributes = ["payment_id", "position", "due_days", "due_by", "amount", "discount", "tax", "tax_amount", "instl_fees", "late_fees", "total",
			"channel", "channel_meta", "comments", "created_by", "receipt", "status"
		];

		$cols = [];
		$values = [];

		$set = [];

		foreach ($attributes as $attr) {

			if (isset($installment[$attr])) {

				if ($attr == "status") {

					if (!in_array($installment[$attr], ["unpaid", "paid", "disabled"])) {
						return $create ? ["", ""] : false;
					}

				}

				$value = $installment[$attr];
				if (is_array($installment[$attr])) {
					$value = json_encode($value);
				}

				if ($create) {

					$cols[] = $attr;
					$values[] = db_sanitize($value);

				}
				else {
					$set[] = $attr." = ".db_sanitize($value);
				}

			}

		}

		if ($create) {
			return [$cols, $values];
		}

		return $set;

	}

?>