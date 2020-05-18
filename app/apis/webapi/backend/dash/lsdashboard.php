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
$dateFilter['from'] = $_GET['from_date'];
$dateFilter['to'] = $_GET['to_date'];

if(empty($leadTable) || !is_numeric($leadStatus)){ 
    header("HTTP/1.1 400 Bad Request");
    die;
}

if(((isset($leadStatus) &&  ($leadStatus < 0)) ) || (!($leadTable) &&  !is_string($leadTable))){
    
   header("HTTP/1.1 400 Bad Request");
   die;
}

//get offset and limit
$pageResult = getPaginationDetails('GET');
$arrLeadData = getLeadData($leadTable, $leadStatus, $leadList,$dateFilter, $pageResult);

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

function getLeadData($leadTable, $leadStatus, $leadList, $dateFilter =[], $pageResult){
    
    switch($leadTable){
        case 'compiled':
            $strTableName = 'user_leads_basic_compiled as l';
            break;;
        default :
            $strTableName = 'user_leads_basic as l';
    }
    
    $arrLeadList = [];
    $datePeriodQuery = " DATE(l.create_date) BETWEEN ( NOW() - INTERVAL 30 DAY) AND NOW() ";
    if(!empty($dateFilter['from']) && !empty($dateFilter['to']) ){
        //need to change the date format to "YY:MM:DD HH:MM:SS"
        $dateFilter['from'] = date('Y-m-d H:i:s', strtotime($dateFilter['from']));
        $dateFilter['to'] = date('Y-m-d H:i:s', strtotime('+1 day',strtotime($dateFilter['to'])));
        $datePeriodQuery = " DATE(l.create_date) BETWEEN '".$dateFilter['from']."' AND '".$dateFilter['to']."'";
    }
    $leadListGetQuery = "SELECT l.lead_id as leadId, l.user_id as userId,l.email as leadEmail, l.name as leadName ";
    $leadListGetQuery .= " , l.phone as leadPhone, l.create_date as leadDate FROM ".$strTableName;
    $leadListGetQuery .= " WHERE l.status = ".$leadStatus." AND  ".$datePeriodQuery;
    $leadListGetQuery .= " ORDER BY l.create_date DESC";
    
    $arrLeadCount = db_count($leadListGetQuery);
    
    
    $arrLeadList = db_select_query($leadListGetQuery, $pageResult);
    
    $arrLeadResult=[];
    switch($leadList){
        case 1:            
        case 2: 
            $arrLeadResult['count'] = $arrLeadCount;            
            $arrLeadResult['list'] = array_values($arrLeadList);
            break;
        case 0: //case only count to be returned
        default:
            $arrLeadResult['count'] = $arrLeadCount;
            
            break;
    }
        
    $arrLeadResult['totalPages'] = ceil($arrLeadCount / ITEMS_PER_PAGE);
    $arrLeadResult['page'] = $pageResult['page'];
    $arrLeadResult['totalRecords'] = $arrLeadCount;
    $arrLeadResult['counter'] = $pageResult['offset']+1;
    
    return $arrLeadResult;
}





