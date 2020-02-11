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

    JIGSAW ACADEMY WORKFLOW SYSTEM v1
    ---------------------------------
*/

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header('Location: https://www.jigsawacademy.com');
		die();
	}

	$GLOBALS["activity"]["debug"]["log"] = "";
    $GLOBALS["activity"]["debug"]["error_get_last"] = false;

    // This will create a new activity
    //-------------------

    function activity_create($priority, $act_type, $activity, $entity_type = "", $entity_id = "", $context_type="", $context_id = "", $content = "", $status = "logged") {

		// Feed log
		try { if ($priority == "critical") activity_log("[JAWS:] Encountered critical error - ".$act_type." [[".$activity."]]", $content, ["it", "error"], ["c" => "danger"]); }
		catch(Exception $e) {}

        // Prep
        $priority = db_sanitize($priority);
        $act_type = db_sanitize($act_type);
        $activity = db_sanitize($activity);
        $entity_type = (strlen($entity_type) > 0) ? db_sanitize($entity_type) : 'NULL';
        $entity_id = (strlen($entity_id) > 0) ? db_sanitize($entity_id) : 'NULL';
        $context_type = (strlen($context_type) > 0) ? db_sanitize($context_type) : 'NULL';
        $context_id = (strlen($context_id) > 0) ? db_sanitize($context_id) : 'NULL';
        $content = (strlen($content) > 0) ? db_sanitize($content) : 'NULL';
        $act_date = db_sanitize(strval(date("Y-m-d H:i:s")));
        $status = db_sanitize($status);

        // Create
        if (db_exec("INSERT INTO system_activity (priority, act_type, activity, entity_type, entity_id, context_type, context_id, content, act_date, status) VALUES (".$priority.",".$act_type.",".$activity.",".$entity_type.",".$entity_id.",".$context_type.",".$context_id.",".$content.",".$act_date.",".$status.");")) return db_get_last_insert_id();
        else return false;

    }

    function activity_debug_start($mode = "log") {
    	if (strcmp($mode, "log") == 0) register_shutdown_function("activity_debug_write");
    	else register_shutdown_function("activity_debug_echo");
    }

    function activity_debug_write() {
    	activity_create("low", "debug", "php.script.end", "", "", "", "", ($GLOBALS["activity"]["debug"]["error_get_last"] ? var_export(error_get_last())." - " : "").$GLOBALS["activity"]["debug"]["log"]." => ".json_encode(debug_backtrace()));
    }

    function activity_debug_echo() {
    	echo ($GLOBALS["activity"]["debug"]["error_get_last"] ? var_export(error_get_last())." - " : "").$GLOBALS["activity"]["debug"]["log"];
    }

    function activity_debug_log($info, $clear = false) {
        if ($clear) $GLOBALS["activity"]["debug"]["log"] = $info;
        else $GLOBALS["activity"]["debug"]["log"] .= "\n".$info;
    }

	// Feed Handler
	function activity_feed_get($counter, $data) {

		// Load stuff
		load_plugin("mongodb");
		load_plugin("predis");

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
            
            if(APP_ENV == "dev" || APP_ENV == "devuat"){
                return true;
            }
		// Load stuff
		load_plugin("mongodb");
		load_plugin("predis");

		// Helper
		$is_assoc = function ($obj) {
			if (!is_array($obj)) return false;
			else return count(array_filter(array_keys($obj), 'is_string')) > 0;
		};

		// Parse parameters
		$m = [];
		$w = [];
		$d = [];
		$params = func_get_args();
		$index = count($params) - 1;

		// Parse 'd' field
		if ($is_assoc($params[$index])) $d = $params[$index];

		// Parse 'w'
		if (count($d) > 0) $index --;
		if (is_array($params[$index])) $w = array_values($params[$index]);
		else $w[] = $params[$index];

		// Parse 'm'
		$index --;
		for($count = 0; $count <= $index; $count++) {
			if ($is_assoc($params[$count])) $m[] = $params[$count];
			else if (is_array($params[$count])) $m[] = array_values($params[$count]);
			else $m[] = $params[$count];
		}

		// Add timestamp
		$_t = floatval(substr(str_replace(".","",microtime(true)), 0, 13));
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

	function log_activity($source, $data) {

		if (!is_string($data)) {
			$data = json_encode($data);
		}

		$source = db_sanitize($source);
		$data = db_sanitize($data);

		db_exec("INSERT INTO system_log (source, data) VALUES ($source, $data);");

		return db_get_last_insert_id();

	}

?>
