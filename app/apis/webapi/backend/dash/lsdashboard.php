<?php

// Prevent exclusive access
//if (!defined("JAWS")) {
//        header("HTTP/1.1 401 Unauthorized");
//        die();
//}

// Init Session
//auth_session_init();
//
//// Auth Check - Expecting Session Only !
//if ((!auth_session_is_logged()) || (!auth_session_is_allowed("dash"))) {
//        header("HTTP/1.1 401 Unauthorized");
//        die();
//}

$leadStatus = $_GET['leadStatus'];
$leadTable = $_GET['leadTable'];
$leadList = $_GET['leadList'];

if(empty($leadTable) || !is_numeric($leadStatus)){ 
    header("HTTP/1.1 400 Bad Request");
    die;
}

if(((isset($leadStatus) &&  ($leadStatus < 0)) ) || (!($leadTable) &&  !is_string($leadTable))){
    
   header("HTTP/1.1 400 Bad Request");
   die;
}
//get offset and limit
//$pageResult = getPaginationDetails('GET');

$arrLeadData = getLeadData($leadTable, $leadStatus, $leadList);

sendResponse($arrLeadData, $leadStatus);



function sendResponse($arrLeadData, $leadStatus){
    
    $response['leadStatus'] = $leadStatus;
    $response['data'] = $arrLeadData;
    
    	// Send Headers
	header('Access-Control-Allow-Credentials: true');
	header("Content-Type: application/json");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Pragma-directive: no-cache");
	header("Cache-directive: no-cache");
	header("Cache-Control: private, must-revalidate, max-age=0, no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
	header("Pragma: no-cache");
        
        die(json_encode($response));      
        exit();
}

function getLeadData($leadTable, $leadStatus, $leadList){
    
    switch($leadTable){
        case 'compiled':
            $strTableName = 'user_leads_basic_compiled as l';
            break;;
        default :
            $strTableName = 'user_leads_basic as l';
    }
    
    $arrLeadList = [];
    $arrLeadList = db_query("SELECT l.lead_id as leadId, l.user_id as userId,l.email as leadEmail, l.name as leadName, l.phone as leadPhone, l.create_date as leadDate FROM ".$strTableName. " WHERE l.status = ".$leadStatus." AND l.create_date BETWEEN ( NOW() - INTERVAL 30 DAY) AND NOW() ORDER BY l.create_date DESC");
    
    //echo ("SELECT l.lead_id as leadId, l.user_id as userId,l.email as leadEmail, l.name as leadName, l.phone as leadPhone, l.create_date as leadDate FROM ".$strTableName. " WHERE l.status = ".$leadStatus." AND l.create_date BETWEEN ( NOW() - INTERVAL 30 DAYS) AND NOW() ORDER BY l.create_date DESC");die;
//            ." ORDER BY l.create_date DESC");die;
    
       
    $arrLeadResult=[];
    switch($leadList){
        case 1:            
        case 2: 
            $arrLeadResult['count'] = count($arrLeadList);            
            $arrLeadResult['list'] = array_values($arrLeadList);
            break;
        case 0: //case only count to be returned
        default:
            $arrLeadResult['count'] = count($arrLeadList);
            
            break;
    }
    
    return $arrLeadResult;
}





