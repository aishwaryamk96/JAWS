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
		header('Location: https://www.jigsawacademy.com/index.php');
		die();
	}

	// Shuriken Event Queue execution and Storage
    	// This contains functions to Implement the event queue execution and storage
	// -------------------

	// This function will fetch active event kue overhead for the given tokens in the request and cookie
  	// This function also implements a security check for append-mode.
  	// It checks if the given tokens are valid and the page is available for more event chaining.
  	function shuriken_event_kue_get() {

		// Prep page token
    		$_id = new MongoDB\BSON\ObjectID($_REQUEST["iii"]);

    		// Prep Query & Projection
    		$filter = [
	    		'_id' => &$_id,
			'ip' => $_SERVER['REMOTE_ADDR']
		];

		$projection = [
			'_id' => 0,
			'url' => 1,
			't' => 1
		];

    		// Prep user and session token
    		$_i; 
    		$_ii;

    		// Get tokens from cookie
    		if ((isset($_COOKIE["_shuri"])) && (isset($_COOKIE["_shurii"]))) {
    			$_i = new MongoDB\BSON\ObjectID($_COOKIE["_shuri"]);
    			$_ii = new MongoDB\BSON\ObjectID($_COOKIE["_shurii"]);

    			if ($GLOBALS['shuriken']['security']['token_check']) {
    				$filter['_i'] = &$_i;
    				$filter['_ii'] = &$_ii;
    			}
    		}

    		// Get tokens from stored document
    		else {
    			// Security check
    			if ($GLOBALS['shuriken']['security']['token_check']) return false;
    			
    			// Add to projection
    			$projection['_i'] = 1;
    			$projection['_ii'] = 1;
    		}

    		// Out Append Check?
    		if ($GLOBALS['shuriken']['security']['append_check']) $filter['out'] = [ '$exists' => false ];

    		// Parameter IV check?
    		if (isset($_REQUEST['iv'])) $filter['_v'] = ['$nin' => [$_REQUEST['iv']]];

    		// Execute Retreive
    		$mdbcol = (new MongoDB\Client())->jaws->{$GLOBALS['shuriken']['storage']['collection_events']};
		$doc = $mdbcol->findOne($filter, $projection);
		if ($doc == null) return false;

		// Set IV Parameter
		if (isset($_REQUEST['iv'])) $mdbcol->updateOne(['_id' => &$_id], ['$addToSet' => ['_v' => $_REQUEST['iv']]]);		

		// Prep user and session token - again
		if ((!isset($_COOKIE["_shuri"])) || (!isset($_COOKIE["_shurii"]))) {
			$_i = new MongoDB\BSON\ObjectID($doc['_i']);
    			$_ii = new MongoDB\BSON\ObjectID($doc['_ii']);
		}

		// Return Kue
    		return [
    			'_id' => &$_id,
			'_i' => &$_i,
			'_ii' => &$_ii,
			'url' => $GLOBALS['shuriken']['temp']['url'] ?? url_template_expand($doc['url']),
			't' => $doc['t'],
			'ip' => $_SERVER['REMOTE_ADDR'],
			'v' => [],
			'mode' => 'append'
    		];

	}

	// This function create a new event kue
	function shuriken_event_kue_create() {

		$seed = (microtime(true)).$_SERVER['REMOTE_ADDR'];
		$GLOBALS['shuriken']['temp']['date'] = new MongoDB\BSON\UTCDateTime(time()*1000);
			
		// Page token
		$_id = new MongoDB\BSON\ObjectID();

		// Perm token already set - set sess token
		if (isset($_COOKIE["_shuri"])) {
			$_i = new MongoDB\BSON\ObjectID($_COOKIE["_shuri"]);

			if (isset($_COOKIE["_shurii"])) $_ii = new MongoDB\BSON\ObjectID($_COOKIE["_shurii"]);
			else {
				$_ii = new MongoDB\BSON\ObjectID();
				shuriken_output_cookie("_shurii", (string) $_ii, 0, "/");
			}
		}

		// Perm token not set
		else {
			$_i = new MongoDB\BSON\ObjectID();
			$_ii = new MongoDB\BSON\ObjectID();
			shuriken_output_cookie("_shurii", (string) $_ii, 0, "/");
		}

		// Refresh  / set perm token
		shuriken_output_cookie("_shuri", (string) $_i, time() + (86400 * 365), "/");

		// Create the kue
		return [
    			'_id' => &$_id,
			'_i' => &$_i,
			'_ii' => &$_ii,
			'url' => $GLOBALS['shuriken']['temp']['url'] ?? url_template_from_string($_SERVER['HTTP_REFERER']),
			'ip' => $_SERVER['REMOTE_ADDR'],
			't' => &$GLOBALS['shuriken']['temp']['date'],
			'v' => [],
			'mode' => 'create'
    		];	

	}

	// This function will parse the passed event queue and prep the events for execution
	// This can parse the events as properties for the parent document, using the parse_as_prop = true (used for single fire endpoints like GIF)
	// This can also add the propped parsed data into an event of name as given in $propped_event
	// Propped events are an event representing addition of data to the parent kue / doc. These events are not stored by default.
	function shuriken_event_kue_parse(&$kue, $parse_as_prop = false, $propped_event_name = null) {

		// Prep
		$GLOBALS['shuriken']['temp']['date'] = new MongoDB\BSON\UTCDateTime(time()*1000);

		// Propped event ?
		$propped_event_data;
		$propped_event_callback = '';

		// Parse each event
		$i = 'a';
		while(true) {

			// Event available
			if (!isset($_REQUEST['e'.$i])) break;

			// Parse as Prop
			if ($parse_as_prop) {
				if (strlen($_REQUEST['e'.$i]) == 0) continue;
				$kue[$_REQUEST['e'.$i]] = $_REQUEST['d'.$i] ?? '';
				if (isset($propped_event_name)) {
					$propped_event_data[$_REQUEST['e'.$i]] = $_REQUEST['d'.$i] ?? '';
					$propped_event_callback = $_REQUEST['c'.$i] ?? $propped_event_callback;
				}
			}

			// Parse as event
			else {

				// Add properties to parent doc
				if ((strlen($_REQUEST['e'.$i]) == 0) && (isset($_REQUEST['d'.$i]))) {
					foreach($_REQUEST['d'.$i] as $key => $val) {
						$kue[$key] = $val;
						if (isset($propped_event_name)) {
							$propped_event_data[$_REQUEST['e'.$i]] = $_REQUEST['d'.$i] ?? '';
							$propped_event_callback = $_REQUEST['c'.$i] ?? $propped_event_callback;
						}
					}
				}

				// Add to [v]
				elseif (strlen($_REQUEST['e'.$i]) > 0) {
					$event = [
						'e' => $_REQUEST['e'.$i],
						'allow_output' => $GLOBALS['shuriken']['security']['allow_output'],
						'allow_process' => $GLOBALS['shuriken']['security']['allow_process'],
						'allow_storage' => $GLOBALS['shuriken']['security']['allow_storage'],
						'allow_end' => $GLOBALS['shuriken']['security']['allow_end'],
						'storage_mode' => 'add'
					];

					if (isset($_REQUEST['d'.$i])) $event['d'] = $_REQUEST['d'.$i];
					if (isset($_REQUEST['c'.$i])) $event['c'] = $_REQUEST['c'.$i];
					if (isset($_REQUEST['t'.$i])) $event['t'] = (strlen($_REQUEST['t'.$i]) > 0) ? ((intval($_REQUEST['t'.$i]) > 999999) ? (new MongoDB\BSON\UTCDateTime(intval($_REQUEST['t'.$i]))) : intval($_REQUEST['t'.$i])) : $GLOBALS['shuriken']['temp']['date'];

					// Add the event to the kue
					$kue['v'][] = $event;
				}

				// e and d both are blank - cannot be processed
				else continue;
			}			

			// Next Event
			$i++;

		}

		// Propped event finalize
		if ((($parse_as_prop) && (isset($propped_event_name))) || ((isset($propped_event_name)) && (isset($propped_event_data)))) {
			$event = [
				'e' => $propped_event_name,
				'allow_output' => $GLOBALS['shuriken']['security']['allow_output'],
				'allow_process' => $GLOBALS['shuriken']['security']['allow_process'],
				'allow_storage' => false,
				'allow_end' => $GLOBALS['shuriken']['security']['allow_end']
			];
			if (isset($propped_event_data)) $event['d'] = $propped_event_data;
			if (strlen($propped_event_callback) > 0) $event['c'] = $propped_event_callback;
			
			// Push to kueue
			array_push($kue['v'], $event);
		}

		// All done !!
	}

	// This will execute event-wide and kue-wide stage on the event kue
	// This calls events handlers for the given stage on each event and on the entire kue
	// Kue is passed by reference and updated
	function shuriken_event_kue_execute(&$kue, $stage) {

		// Execute Kue-Wide stage	
    		handle_strict('shuriken_kue_'.$stage, $kue);

		// Execute event-wide stage on kue
    		switch ($stage) {

    			// Output Stage
    			case 'output':    				
    				for($i=0;$i<count($kue['v']);$i++) {
    					if ((isset($kue['v'][$i])) && ($kue['v'][$i]['allow_output'])) {
    						$hook = 'shuriken_event_output_'.$kue['v'][$i]['e'];

    						if (((!isset($GLOBALS["jaws_hooks"][$hook])) || (count($GLOBALS["jaws_hooks"][$hook]) == 0)) && ((isset($kue['v'][$i]['c'])) && (strlen($kue['v'][$i]['c']) > 0))) echo $kue['v'][$i]['c'].'();'; 
    						else handle_strict($hook, $kue, $i);
    					}
    				}
    				break;

    			// Process Stage
    			case 'process':    				
    				for($i=0;$i<count($kue['v']);$i++) {
    					if ((isset($kue['v'][$i])) && ($kue['v'][$i]['allow_process'])) {
    						$hook = 'shuriken_event_process_'.$kue['v'][$i]['e'];

    						if (!$GLOBALS['shuriken']['security']['allow_storage']) {
    							if ($GLOBALS['shuriken']['security']['allow_storage_on_hook']) {
    								if (isset($GLOBALS["jaws_hooks"][$hook])) $kue['v'][$i]['allow_storage'] = true;
    							}
    						}
    						
    						handle_strict($hook, $kue, $i);
    					}
    				}
    				break;

    			// End Stage
    			case 'end':    				
    				for($i=0;$i<count($kue['v']);$i++) {
    					if ((isset($kue['v'][$i])) && ($kue['v'][$i]['allow_end'])) {
    						$hook = 'shuriken_event_end_'.$kue['v'][$i]['e'];
    						handle_strict($hook, $kue, $i);
    					}
    				}
    				break;
    		} 	
	}

	// This function will store an event queue
	// This will either create a new document or embed new documents into an existing document according to the mode.
	function shuriken_event_kue_store($kue) {

		// Prep Mongo
		$mdbcol = (new MongoDB\Client())->jaws->{$GLOBALS['shuriken']['storage']['collection_events']};

		// Create new document ?
		if ($kue['mode'] == 'create') {

			// Prep Kue
			for($i=0;$i<count($kue['v']);$i++) {
				if (isset($kue['v'][$i])) {
					if (!($kue['v'][$i]['allow_storage'] === true)) unset($kue['v'][$i]);
					else unset($kue['v'][$i]['c'], $kue['v'][$i]['allow_output'], $kue['v'][$i]['allow_process'], $kue['v'][$i]['allow_storage'], $kue['v'][$i]['allow_end'], $kue['v'][$i]['storage_mode']);	
				}	
			}
			$kue['v'] = array_values($kue['v']);		

			// Prep more
			$kue['url'] = url_template_compact($kue['url']);
			if ($GLOBALS['shuriken']['storage']['pre_alloc'] > 0) $kue['_alloc'] = str_repeat('-', $GLOBALS['shuriken']['storage']['pre_alloc']);
			unset($kue['mode']);
	
			// Store in DB
			$mdbcol->insertOne($kue);

		}

		// Append to existing embedded document
		elseif ($kue['mode'] == 'append') {

			// Log Errors
			function logerr(&$doc, &$fltr, &$upd)
			{
				if ($doc->getMatchedCount() == 0) {
					ob_start();
					echo 'RES - ';
					var_dump($doc);
					echo "\nFLTR - ";
					var_dump($fltr);
					echo "\nUPD - ";
					var_dump($upd);
					$resdump = ob_get_clean();
					
					activity_create('critical','shuriken.event.storage','append.fail','','','','',$resdump);
				}
			}

			// Prep the filter
			$filter = ['_id' => &$kue['_id']	];

			// Prep Kue
			$update;
			$v_add = [];
			$v_remove = [];
			$v_replace_add_pos;

			for($i=0;$i<count($kue['v']);$i++) {
				if (isset($kue['v'][$i])) {
					if ($kue['v'][$i]['allow_storage'] === true) {

						$store_mode = strtolower($kue['v'][$i]['storage_mode']);
						unset($kue['v'][$i]['c'], $kue['v'][$i]['allow_output'], $kue['v'][$i]['allow_process'], $kue['v'][$i]['allow_storage'], $kue['v'][$i]['allow_end'], $kue['v'][$i]['storage_mode']);	

						if ($store_mode=='remove') {
							if (!in_array($kue['v'][$i]['e'], $v_remove)) $v_remove[] = $kue['v'][$i]['e'];
						}

						elseif ($store_mode=='replace') {
							if (isset($v_replace_add_pos[$kue['v'][$i]['e']])) $v_add[$v_replace_add_pos[$kue['v'][$i]['e']]] = $kue['v'][$i];	
							else {
								if (!in_array($kue['v'][$i]['e'], $v_remove)) $v_remove[] = $kue['v'][$i]['e'];
								$v_replace_add_pos[$kue['v'][$i]['e']] = count($v_add);
								$v_add[] = $kue['v'][$i];
							}
						}

						else $v_add[] = $kue['v'][$i];						
					}
				}	
			}

			// Defer push for pull operation ??
			$defer_push = ((count($v_add) > 1) && (count($v_remove) > 1));

			// Prep the $pull
			if ($defer_push) $update['$pull'] = ['v' => ['e' => ['$in' => $v_remove]]];

			// Prep the $push
			if (!$defer_push) $update['$push'] = ['v' => ['$each' => $v_add]];

			// Set Total Time
			$kue['tt'] = time() - ((string) $kue['t'] / 1000);

			// Prep the $set
			unset($kue['mode'], $kue['url'], $kue['ip'], $kue['_id'], $kue['_i'], $kue['_ii'], $kue['t'],$kue['v']);
			$update['$set'] = $kue;

			// Prep $unset padding
			$update['$unset'] = ["_alloc" => ""];

			// Unset IV if OUT is being set and Append is gated via OUT
			if (($GLOBALS['shuriken']['security']['append_check']) && (isset($kue['out']))) $update['$unset']['_v'] = "";

			// Store in DB
			$resdoc = $mdbcol->updateOne($filter, $update);
			logerr($resdoc, $filter, $update);

			// Push pending ??
			if ($defer_push) {
				// Prep the $push
				unset($update);
				$update['$push'] = ['v' => ['$each' => $v_add]];

				// Store in DB
				$resdoc = $mdbcol->updateOne($filter, $update);
				logerr($resdoc, $filter, $update);
			}
		}
	}

  ?>