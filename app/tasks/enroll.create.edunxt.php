<?php

	load_module("user_enrollment");
	load_plugin("edunxt");

	$subscriptions = db_query("SELECT s.*, m.* FROM subs AS s INNER JOIN subs_meta AS m ON m.subs_id = s.subs_id WHERE s.status = 'pending' AND m.bundle_id = 132;");
	foreach ($subscriptions as $subs) {

		create_enrollments($subs);
		export_subs($subs);

	}

?>