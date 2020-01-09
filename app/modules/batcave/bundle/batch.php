<?php

	function batches_get_for_bootcamp($bootcamp_id) {

		$batches = [];

		$bootcamp_id = db_sanitize($bootcamp_id);
		$res_batches = db_query("SELECT * FROM bootcamp_batches WHERE bundle_id = $bootcamp_id;");
		foreach ($res_batches as $batch) {
			$batches[] = batch_normalize($batch);
		}

		return $batches;

	}

	function batch_get($id) {

		$id = db_sanitize($id);
		$batch = db_query("SELECT * FROM bootcamp_batches WHERE id = $id;");
		if (empty($batch)) {
			return false;
		}

		return batch_normalize($batch);

	}

	function batch_normalize($batch) {

		$batch["meta"] = json_decode($batch["meta"], true);
		return $batch;

	}

?>