<?php

/*
           8 8888       .8. `8.`888b                 ,8' d888888o.
           8 8888      .888. `8.`888b               ,8'.`8888:' `88.
           8 8888     :88888. `8.`888b             ,8' 8.`8888.   Y8
           8 8888    . `88888. `8.`888b     .b    ,8'  `8.`8888.
           8 8888   .8. `88888. `8.`888b    88b  ,8'    `8.`8888.
           8 8888  .8`8. `88888. `8.`888b .`888b,8'      `8.`8888.
88.        8 8888 .8' `8. `88888. `8.`888b8.`8888'        `8.`8888.
`88.       8 888'.8'   `8. `88888. `8.`888`8.`88'     8b   `8.`8888.
  `88o.    8 88'.888888888. `88888. `8.`8' `8,`'      `8b.  ;8.`8888
    `Y888888 ' .8'       `8. `88888. `8.`   `8'        `Y8888P ,88P'

    JIGSAW ACADEMY WORKFLOW SYSTEM v2
    ---------------------------------
*/

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header('Location: https://www.jigsawacademy.com');
		die();
	}

	// Load stuff
	load_plugin("mongodb");
	load_plugin('predis');

	// Feed Handler
	function activity_feed_get($counter, $data) {

		// Prep options
		$option = [
			'projection' => [
				'_id' => false,
				'm' => true,
				'w' => true,
				'd' => true,
				't' => true
			],
			'sort' => ['t' => 1],
			'limit' => 5
		];

		// Build Filter
		$filter = [];
		$w = [];

		foreach($data as $val) {
			if (is_array($val)) foreach($val as $dkey => $dval) $filter['d.'.$dkey] = $dval;
			else $w[] = $val;
		}

		// Process tags, check cache
		if (count($w) > 0) {
			$flag = false;
			foreach($w as $tag) {
				$t = ($GLOBALS["jaws_redis"]["db"])->get("activity.log.tag.".$tag);

				if (is_null($t)) {
					$flag = true;
					break;
				}
				else if (floatval($t) > floatval($counter)) {
					$flag = true;
					break;
				}
			}

			if ($flag) $filter['w'] = ['$in' => $w];
			else return $counter;
		}

		// Load initial
		if ($counter == -1) {
			$option['limit'] = 50;
			$option['sort'] = ['t' => -1];
		}

		// Load updates
		else $filter['t'] = ['$gt' => new MongoDB\BSON\UTCDateTime($counter)];

		// Retrieve
		$cursor = (new MongoDB\Client())->jaws->system_log->find($filter, $option);
		//$cursor->setTypeMap(['root' => 'array']);
		$docs = $cursor->toArray();
		$ret = [];

		// Proccess
		foreach($docs as $doc) {
			$ret[] = [
				'epoch' => (string) $doc['t'],
				'tags' => $doc['w'],
				'messages' => $doc['m'],
				'data' => $doc['d']
			];
		}

		// More process
		if ($counter == -1) $ret = array_reverse($ret);

		// Output to feed
		if (count($ret) > 0) feed_log(json_encode($ret));

		// Return Counter
		return (count($ret) == 0) ? $counter : ($ret[(count($ret) - 1)]['epoch']);
	}

	// Log activity
	function activity_log() {

		// Helper
		function is_assoc($obj) {
			if (!is_array($obj)) return false;
			else return count(array_filter(array_keys($obj), 'is_string')) > 0;
		}

		// Parse parameters
		$m = [];
		$w = [];
		$d = [];
		$params = func_get_args();
		$index = count($params) - 1;

		// Parse 'd' field
		if (is_assoc($params[$index])) $d = $params[$index];

		// Parse 'w'
		if (count($d) > 0) $index --;
		if (is_array($params[$index])) $w = array_values($params[$index]);
		else $w[] = $params[$index];

		// Parse 'm'
		$index --;
		for($count = 0; $count <= $index; $count++) {
			if (is_assoc($params[$count])) $m[] = $params[$count];
			else if (is_array($params[$count])) $m[] = array_values($params[$count]);
			else $m[] = $params[$count];
		}

		// Add timestamp
		$_t = time() * 1000;
		$t = new MongoDB\BSON\UTCDateTime($_t);

		// Cache updates
		foreach($w as $tag) ($GLOBALS["jaws_redis"]["db"])->set("activity.log.tag.".$tag, strval($_t));

		// Store
		return (new MongoDB\Client())->jaws->system_log->insertOne([
			'm' => $m,
			'w' => $w,
			'd' => $d,
			't' => $t
		]);
	}

?>
