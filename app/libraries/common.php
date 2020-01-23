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
            if(count($queryParams) > 0 && isset($queryParams['page']) && is_int($queryParams['page'])){
                $pageResult['page'] = $queryParams['page'];
            }
            $pageResult['offset'] = ( $pageResult['page'] - 1) * 100;
            
            return $pageResult;
            break;
    }
}
