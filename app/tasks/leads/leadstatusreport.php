<?php

// Prevent exclusive access
if (!defined("JAWS")) {
    header('Location: '.WEBSITE_URL);
    die();
}
load_library("email");

$attachment="";
$todaysDate = date('Y-m-d H:i:s');
$fromDate = date('Y-m-d H:i:s', strtotime('-1 day',strtotime($todaysDate)));//from date can be vary according to the requirement

$fileHandler = fopen(FLR_FILE_NAME,"w+");
//fputcsv($fileHandler, FLR_CSV_HEADERS);

//$basic_lead_failed_count = processTheLeadToCsv($fileHandler,"user_leads_basic",LEAD_PROCESSING,$fromDate,$todaysDate);
$compiled_lead_failed_count = processTheLeadToCsv($fileHandler,'user_leads_basic_compiled',COMPILED_FAILURE,$fromDate,$todaysDate);
$totalCount = $compiled_lead_failed_count;

fclose($fileHandler);

$content["todate"] = date('d-M-Y');
$content["fromdate"] = date('d-M-Y', strtotime('-1 day',strtotime($todaysDate)));
if($totalCount>0){
    $content["leadCountMessage"] = "Number of leads failed : " . $totalCount;
    $attachment = FLR_FILE_NAME;//need to add an attachment in mail only when leads present
}
else
    $content["leadCountMessage"] = "There are no failed leads";

send_email_with_attachment("lead.fail.re",array(),$content,array($attachment));
//delete the file
unlink(FLR_FILE_NAME);
exit(0);

function processTheLeadToCsv($fileHandler,$table,$status,$fromDate,$todaysDate){
    $leadQuery = "SELECT  l.*";
    $leadQuery .= " FROM ".$table." as l ";
    $leadQuery .= " WHERE l.status = ".$status." AND  ";
    $leadQuery .= "( l.create_date >= '".$fromDate."' AND l.create_date <= '".$todaysDate."') ";
    $leadQuery .= " ORDER BY l.create_date DESC";
    $leeds = db_query($leadQuery);
    $count = db_count($leadQuery);
    $isNeedHeaderNeed =true;
    foreach ($leeds as $leed) {
        $keyLine=array();
        $line =array();
        foreach ($leed as $key=>$val) {
            if($isNeedHeaderNeed)
                $keyLine[] = $key;
            $line[] = ($val!=NULL)?$val:"NA";
        }
        if($isNeedHeaderNeed){
            fputcsv($fileHandler,$keyLine);
            $isNeedHeaderNeed =false;
        }
        fputcsv($fileHandler,$line);
    }
    return $count;
}
