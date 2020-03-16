<?php

	function program_get_all() {

		$bs = db_query("SELECT * FROM course_bundle AS b WHERE b.bundle_type = 'programs' AND b.status != 'disabled' AND b.combo != '' AND (b.expire_date IS NULL OR DATE(b.expire_date) >= CURRENT_DATE) ORDER BY b.position ASC, b.bundle_id DESC;");

		if (empty($bs)) {
			return [];
		}

		$today = new DateTime;

		$bootcamps = [];
		foreach ($bs as $b) {

			$batches = db_query("SELECT * FROM bootcamp_batches WHERE bundle_id = ".$b["bundle_id"]);

			foreach ($batches as $batch) {

				$start_date = date_create_from_format("Y-m-d", $batch["start_date"]);
				if (!$batch["visible"]) {
					$batch["no_show"] = true;
				}
				if ($today->diff($start_date)->format("%r%a") < -BATCHES_PERIOD) {
					 $batch["no_show"] = true;
				}
				$batch["meta"] = json_decode($batch["meta"], true);

				$b["batches"][] = $batch;

			}

			$bootcamps[] = $b;

		}

		return $bootcamps;

	}

?>