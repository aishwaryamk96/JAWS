<?php

// Prevent exclusive access
if (!defined("JAWS")) {
    header('Location: '.WEBSITE_URL);
    die();
}
load_library("email");

$todaysDate = date('Y-m-d H:i:s');
$fromDate = date('Y-m-d H:i:s', strtotime('-5 day',strtotime($todaysDate)));//from date can be vary according to the requirement

$fileHandler = fopen(FLR_FILE_NAME,"w+");
fputcsv($fileHandler, FLR_CSV_HEADERS);

$basic_lead_failed_count = processTheLeadToCsv($fileHandler,"user_leads_basic",BASIC_FAILURE,$fromDate,$todaysDate);
$compiled_lead_failed_count = processTheLeadToCsv($fileHandler,'user_leads_basic_compiled',COMPILED_FAILURE,$fromDate,$todaysDate);
$totalCount = $basic_lead_failed_count + $compiled_lead_failed_count;

fclose($fileHandler);

$content["todate"] = date('d-M-Y H:i');
$content["fromdate"] = date('d-M-Y H:i', strtotime('-1 day',strtotime($todaysDate)));
if($totalCount>0)
    $content["leadCountMessage"] = "Number of leads failed : ".$totalCount.".";
else
    $content["leadCountMessage"] = "There is no failed leads";

//need to add an attachment in mail only when leads present
$attachment = $totalCount>0?FLR_FILE_NAME:"";
send_email_with_attachment("lead.fail.re",array(),$content,$attachment);
//delete the file
unlink(FLR_FILE_NAME);
exit(0);

function processTheLeadToCsv($fileHandler,$table,$status,$fromDate,$todaysDate){
    $leadQuery = "SELECT  l.lead_id as id, l.name as name, l.email as email,l.phone as phone ,l.create_date as createDate";
    $leadQuery .= " FROM ".$table." as l ";
    $leadQuery .= " WHERE l.status = ".$status." AND  ";
    $leadQuery .= " DATE(l.create_date) BETWEEN '".$fromDate."' AND '".$todaysDate."'";
    $leadQuery .= " ORDER BY l.create_date DESC";
    $leeds = db_query($leadQuery);
    $count = db_count($leadQuery);
    foreach ($leeds as $leed) {
        $line =array();
        foreach ($leed as $key=>$val) {
            $line[] = ($val!=NULL)?$val:"NA";
        }
        fputcsv($fileHandler,$line);
    }
    return $count;
}
