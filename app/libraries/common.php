<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function getQueryParams(){
    
    $queryParams = filter_input_array(INPUT_GET);
    return $queryParams;
}

function getPostParams(){
    
}

function getPaginationDetails($method = "GET"){
    
    //default page offset = 0 and limits
    
    $pageResult['page'] = 1;
    $pageResult['limit'] = ITEMS_PER_PAGE;
    switch ($method){
        case "POST":
            break;
        case "GET":
        default:
            $queryParams = getQueryParams();
           
            if(count($queryParams) > 0 && isset($queryParams['page']) && is_numeric($queryParams['page'])){
                $pageResult['page'] = $queryParams['page'];
            }
            $pageResult['offset'] = ( $pageResult['page'] - 1) * 100;
            
            return $pageResult;
            break;
    }
}

function getUniqueApplicationId($subsId,$format,$checkWebId = false){
    
        if (empty($format)) {
            return false;
        }
        
        if (empty($subsId)) {
            throw new Exception("Subs Id missing", 404);
        }
        
        try{
        // find last application number created.
            $whereQuery = " subs_id = ".$subsId;
            if($checkWebId){
                $whereQuery = " pay_id =".$subsId;
            }
        $lastPayId = db_query( "SELECT `pay_id` FROM `payment`  WHERE ".$whereQuery);
        $lastPayId= intval($lastPayId[0]['pay_id']);
        
        $applicationNumber = $format. date('md'). $lastPayId;

        
        }catch(Exception $e){
            throw new Exception("There was database error");
        }
        
        return $applicationNumber;
}

function updateApplicationNumber($applicationNumber, $subsId){
    
    try{
        // save application number in payment table
        db_exec("UPDATE payment SET app_num = " . db_sanitize($applicationNumber) . " WHERE subs_id = " . $subsId . ";");
        }catch(Exception $e){
            throw new Exception("There was database error");
        }
    
       
}
function getCourseApplicationFormat($courseId){
    
    $courseFormat = db_query("SELECT app_num_format FROM course WHERE course_id=" . db_sanitize($courseId) . ";");
    
    if(!empty($courseFormat)){
        return $courseFormat[0]['app_num_format'];
    }
    
    return false;
}