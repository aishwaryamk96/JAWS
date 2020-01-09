<?php

	function products_all() {

		$categories = bundles_get();

		$categories["courses"] = courses_get();

		return $categories;

	}

	function bundles_get() {

		$bundles = [];

		$res = db_query(
			"SELECT
				b.bundle_id AS id,
				b.bundle_type AS type,
				b.name,
				b.combo,
				b.price_inr,
				b.price_usd,
				b.subs_duration_length AS duration,
				b.subs_duration_unit AS duration_unit,
				b.receipt_type,
				b.iot_kit,
				m.slug,
				m.desc,
				m.category,
				m.content
			FROM
				course_bundle AS b
			LEFT JOIN
				course_bundle_meta AS m
				ON m.bundle_id = b.bundle_id
			WHERE
				b.bundle_type NOT IN ('offer', 'combo')
				AND b.status IN ('upcoming', 'enabled', 'offline')
				AND b.combo != ''
			ORDER BY b.position;"
		);
		foreach ($res as $bundle) {

			$bundle = bundle_normalize($bundle, "type");

			if ($bundle["type"] != "specialization") {
				$bundles[$bundle["type"]][] = $bundle;
			}

		}

		return $bundles;

	}

	function courses_get() {

		$res = db_query(
			"SELECT
				c.course_id AS id,
				c.name,
				c.sis_id,
				c.sp_price_inr AS price_inr,
				c.sp_price_usd AS price_usd,
				c.duration_length AS duration,
				c.duration_unit,
				'course' AS type,
				m.slug,
				m.desc,
				m.category,
				m.content
			FROM
				course AS c
			LEFT JOIN
				course_meta AS m
				ON m.course_id = c.course_id
			WHERE
				c.no_show = 0
			ORDER BY
				c.position DESC,
				c.course_id ASC;");
		foreach ($res as $course) {

			$course = course_normalize($course, true);

			if (empty($course["sis_id"])) {
				$noCode++;
			}

			$courses[] = $course;

		}

		return $courses;

	}

?>