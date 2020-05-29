<?php

//get the list of the bootcamp_batches for a specific user

load_module ("course");


$bundle_id = db_sanitize($_GET["bundle_id"]);

$res_batches = db_query(
    "SELECT
            bcb.id as id,
            bcb.bundle_id as bundle_id,
            bcb.code as code,
            bcb.visible as visible,
            bcb.start_date as start_date,
            bcb.end_date as end_date            
		FROM
			bootcamp_batches as bcb		
		WHERE
			bcb.bundle_id = $bundle_id
		ORDER BY
			bcb.start_date DESC;"
);

if (!empty($res_batches)) {
    die(json_encode(["data" => $res_batches, "status"=>"success"]));
}
die(json_encode(["data" => [], "status"=>"success"]));

?>