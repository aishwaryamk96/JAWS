<?php

	load_module("user_enrollment");

	register_shutdown_function(function() {
		if (!empty($errors = error_get_last())) {
			$errors = db_sanitize(var_export($errors, true));
			db_exec("INSERT INTO system_log (source, value) VALUES ('platforms.task', $errors);");
		}
	});

	$loaded_plugins = [];

        //JA-14 changes starts
        $wherePlatformQry = " AND b.platform_id NOT IN (1)";
        //JA-14 changes ends
        
	$subscriptions = db_query(
		"SELECT
			s.*, m.*,
			b.name AS bundle_name,
			bb.code AS batch_code,
			bb.meta AS batch_meta,
			bb.exported AS batch_exported,b.platform_id
			p.plugin
		FROM
			subs AS s
		INNER JOIN
			subs_meta AS m
			ON m.subs_id = s.subs_id
		INNER JOIN
			course_bundle AS b
			ON b.bundle_id = m.bundle_id
		INNER JOIN
			bootcamp_batches AS bb
			ON bb.id = m.batch_id
		INNER JOIN
			platform AS p
			ON p.id = b.platform_id
		WHERE
			s.status = 'pending'
			".$wherePlatformQry //JA-14 changes ends
	);

	foreach ($subscriptions as $subs) {

		$class = ucwords($subs["plugin"]);
		if (!in_array($class, $loaded_plugins)) {

			load_plugin($subs["plugin"]);
			$loaded_plugins[] = $class;

		}

		if (!class_exists($class)) {
			continue;
		}

		create_enrollments($subs);
                
                //JA-14 changes starts
                if($subs['platform_id'] != 2){ 
                    $plugin = new $class;
                    $plugin->export_subs($subs);
                }
                //JA-14 changes ends

	}

?>