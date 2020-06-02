<?php
//delete a batch

// Init Session
auth_session_init();

// Auth Check - Expecting Session Only !
if ((!auth_session_is_logged()) || (!auth_session_is_allowed("batcave"))) {
    header("HTTP/1.1 401 Unauthorized");
    die();
}
$batch_id = db_sanitize($_GET["batch_id"]);

if(isset($batch_id)) {
    $isPresent = db_query("SELECT bcb.id 
                      FROM bootcamp_batches as bcb 
                      WHERE bcb.id = " . $batch_id);

    if (!empty($isPresent)) {
        db_exec("DELETE bcb FROM bootcamp_batches as bcb WHERE bcb.id = " . $batch_id);
        die(json_encode(["message" => "Batch deleted successfully", "status" => "success"]));
    }
}
die(json_encode(["message" => "No Record found", "status"=>"error"]));
?>