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

	// Globals
	global $jaws_feed_subscribed;
	global $jaws_feed_context;
	global $jaws_feed_flush;

	// This is used to subscribe to feeds
	function feed_subscribe($feeds) {

		// Prep
		set_time_limit(0);

		//Send Headers & re-connection time
		ob_start();
		header('Content-Type: text/event-stream');
		header("Cache-Control: no-cache");
		header("Pragma: no-cache");
		header('Connection: keep-alive');
		header('Access-Control-Allow-Credentials: true');
    	header('Access-Control-Expose-Headers: X-Events');
		ob_end_flush();
		ob_flush();
		flush();

		ob_start();
		echo "retry: 5000\n\n";
		ob_end_flush();
		ob_flush();
		flush();

		// Prep Feeds
		$GLOBALS['jaws_feed_subscribed'] = [];
		foreach($feeds as $name => $detail) {
			if (isset($GLOBALS["jaws_feed_map"][$name])) {

				// Map
				$map = $GLOBALS["jaws_feed_map"][$name];

				// Load dependencies
				if (!empty($map['module'])) {
					try {
						load_module($map['module']);
					}
					catch (Exception $e) {
						// Log error here
						continue;
					}
				}

				// Subscribe
				$GLOBALS['jaws_feed_subscribed'][] = [
					'feed' => $name,
					'handler' => $map['handler'],
					'interval' => $map['interval'] ?? 10,
					'counter' => $detail['counter'] ?? -1,
					'data' => $detail['data'] ?? []
				];

			}
		}

		// Get Feeds
		feed_sync();
	}

    // This is used to trigger a live feed update on all feeds subscribed to
    function feed_sync() {

		// Prep
		$elapsed = 0;
		$GLOBALS['jaws_feed_flush'] = false;

		// Start handling each feeds update
		while(!connection_aborted()) {

			// Start Output Buffering
			ob_start();

			// Ping every 30 seconds
			if ($elapsed % 30 == 0) {
				echo ":)\n\n";
				$GLOBALS['jaws_feed_flush'] = true;
			}

			// Start handling each feeds update
			for($count = 0; $count < count($GLOBALS['jaws_feed_subscribed']); $count++) {

				// Interval check
				if ($elapsed % $GLOBALS['jaws_feed_subscribed'][$count]['interval'] != 0) continue;

				// Prep
				$GLOBALS['jaws_feed_context'] = $GLOBALS['jaws_feed_subscribed'][$count]['feed'];
				$handler = $GLOBALS['jaws_feed_subscribed'][$count]['handler'];

				// Handle
				try {
					ob_start();
					$GLOBALS['jaws_feed_subscribed'][$count]['counter'] = $handler($GLOBALS['jaws_feed_subscribed'][$count]['counter'], $GLOBALS['jaws_feed_subscribed'][$count]['data']);
					ob_end_flush();
				}
				catch (Exception $e) {
					// Log error here
					ob_end_clean();
					continue;
				}
			}

			// Flush Output
			if ($GLOBALS['jaws_feed_flush']) { // if no data to be send do not send anything
				$GLOBALS['jaws_feed_flush'] = false;
				ob_end_flush();
				ob_flush();
				flush();
			}
			else ob_end_clean();

			// Sleep
			$elapsed = $elapsed > 600 ? 0 : $elapsed + 1;
			sleep(1);
		}

    }

	// This is used to log data to a particular feed
	function feed_log($data) {
		echo "event: ".$GLOBALS['jaws_feed_context']."\n";
		echo "data: ".$data."\n\n";
		$GLOBALS['jaws_feed_flush'] = true;
	}

?>
