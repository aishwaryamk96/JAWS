<?php
// get a batch details to edit.
$batch_id = db_sanitize($_GET["batch_id"]);

$res_batches = db_query(
    "SELECT
            bcb.id as id,
            bcb.bundle_id as bundle_id,
            bcb.code as code,
            bcb.start_date as start_date,
            bcb.end_date as end_date,
            bcb.price as price,
            bcb.price_usd as price_usd,            
            bcb.visible as visible
		FROM
			bootcamp_batches as bcb		
		WHERE
			bcb.id = ".$batch_id);


if (!empty($res_batches)) {
    $res_batches = $res_batches[0];
    die(json_encode(["data" => $res_batches, "status"=>"success"] ));
}
die(json_encode(["data" => "No Record found", "status"=>"success"]));
?>