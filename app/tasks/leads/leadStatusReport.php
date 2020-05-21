<?php

// Prevent exclusive access
if (!defined("JAWS")) {
    header('Location: '.WEBSITE_URL);
    die();
}
load_library("email");

const  LEADBASICCOMPILEDFAILURESTATUS =4;
const  LEADBASICFAILURESTATUS =2;

$csvHeaders= "LEAD_ID LEAD_NAME LEAD_EMAIL LEAD_PHONE LEAD_DATE \r\n";
$todaysDate = date('Y-m-d H:i:s');
$fromDate = date('Y-m-d H:i:s', strtotime('-1 day',strtotime($todaysDate)));//from date can be vary according to the requirement


$filename = "failed_leads".date('Y-m-d').".csv";
$fileHandler = fopen($filename,"w+");
fwrite($fileHandler, $csvHeaders);

$count = processTheLeadToCsv($fileHandler,"user_leads_basic",LEADBASICFAILURESTATUS,$fromDate,$todaysDate);
$count += processTheLeadToCsv($fileHandler,'user_leads_basic_compiled',LEADBASICCOMPILEDFAILURESTATUS,$fromDate,$todaysDate);
fclose($fileHandler);

$content["todate"] = date('d-M-Y H:i');
$content["fromdate"] = date('d-M-Y H:i', strtotime('-1 day',strtotime($todaysDate)));
$content["leadCount"] = $count;

//need to add an attachment in mail only when leads present
$attachment = $count>0?$filename:"";
send_email_with_attachment("lead.fail.re",array(),$content,$filename);
//delete the file
unlink($filename);
exit(0);

function processTheLeadToCsv($fileHandler,$table,$status,$fromDate,$todaysDate){
    $leadQuery = "SELECT  l.lead_id as id, l.name as name, l.email as email,l.phone as phone ,l.create_date as createDate";
    $leadQuery .= " FROM ".$table." as l ";
    $leadQuery .= " WHERE l.status = ".$status." AND  ";
    $leadQuery .= " DATE(l.create_date) BETWEEN '".$fromDate."' AND '".$todaysDate."'";
    $leadQuery .= " ORDER BY l.create_date DESC";
    $leeds = db_query($leadQuery);
    $count = db_count($leadQuery);
    $line="";
    foreach ($leeds as $leed) {
        foreach ($leed as $key=>$val) {
            $line .=preg_replace('/\s+/','',$val)." ";
        }
        $line .="\r\n";
        fwrite($fileHandler,$line);
    }
    return $count;
}
