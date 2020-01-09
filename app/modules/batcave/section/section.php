<?php

	function section_get($id) {

		$id = db_sanitize($id);
		$section = db_query("SELECT * FROM course_section WHERE id = $id;");
		if (empty($section)) {
			return false;
		}

		return $section[0];

	}

	function section_get_date_from_number($number, $month_start = 1) {

		list($year, $month) = section_get_month_and_year_from_number($number);

		if ($month_start == "1") {
			$month_start = "01";
		}
		if ($month < 10) {
			$month = "0".$month;
		}

		return $year."-".$month."-".$month_start." 00:00:00";

	}

	function section_get_batch_from_number($number) {

		list($year, $month) = section_get_month_and_year_from_number($number);

		$months = ["January", "February", "March", "April", "May", "June", "July", "August", "September",  "October",  "November",  "December"];

		return $months[$month - 1]." ".$year;

	}

	function section_get_month_and_year_from_number($number) {
		return [intval($number / 100) + 2014, $number % 100 + 1];
	}

	function section_get_for_course($course_id, $bootcamp_batch_id = false) {

		$where = "course_id = ".db_sanitize($course_id);
		if (!empty($bootcamp_batch_id)) {
			$where .= " AND bootcamp_batch_id = ".db_sanitize($bootcamp_batch_id);
		}

		return db_query("SELECT *, DATE_FORMAT(start_date, '%M, %Y') AS name FROM course_section WHERE $where ORDER BY start_date DESC;");

	}

	function section_get_number_for_start_date($section) {

		$section_id = false;
		$start_date = false;
		if (is_numeric($section)) {
			$section_id = $section;
		}
		else if (is_array($section)) {

			$start_date = $section["start_date"] ?? false;
			if (empty($start_date)) {
				$section_id = $section["id"];
			}

		}

		$number = false;
		if (!empty($start_date)) {
			$number = construct_number_from_date($start_date);
		}

		if (empty($number)) {

			if (empty(($section = section_get($section_id)))) {
				return false;
			}

			$number = construct_number_from_date($section["start_date"]);

		}

		return $number;

	}

	function construct_number_from_date($date) {

		if (!is_a($date, DateTime::class)) {

			$date = date_create_from_format("Y-m-d H:i:s", $date);
			if (empty($date)) {
				$date = date_create_from_format("Y-m-d", $date);
			}

		}

		if (empty($date)) {
			return false;
		}

		return ($date->format("Y") - 2014) * 100 + ($date->format("n") - 1);

	}

?>