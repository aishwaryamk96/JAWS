<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function logErrors($logFile, $errModule, $errMsg, $otherData =[],$logType = ''){
    
    switch($logType){
        case 1:
            sendErr();//@TODO complete this function
            break;
        default : //write to application's error log file defined in vhost
            fileErrLog($logFile, $errModule, $errMsg, $otherData);
            break;
    }
}

function fileErrLog($logFile,$errModule, $errMsg, $otherData){
    
    $filename = trim($logFile);//;
    
    try{
        
        if (!file_exists(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
            $fp = fopen($filename,"a+");
            fclose($fp);
        }

        //ini_set('error_log', '/var/log/apache2/leadcron/lead.log');
        $log = "\n----------".$errModule."----------\n";
        $log .= $errMsg."\n";
        $log .= json_encode($otherData);
        $log .= "\n--------------------\n";

        error_log($log,3,$filename);
    }catch(Exception $e){
        throw new Exception("There was error!! Its not logged");
    }
}