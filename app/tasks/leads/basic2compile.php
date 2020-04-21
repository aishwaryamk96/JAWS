<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined("JAWS")) {
    header('Location: ' . WEBSITE_URL);
    die();
}

register_shutdown_function("basicLeadCronfailure", 1);

//New Error Logging library
load_library("errormanager");
//load leads module
load_module("leads");
//cronstatustracker file name
$cronTracker = "leadBasicCron.txt";

try {

    //Check the eny lead basic cron running
    
    $cronFlag = checkCronStatus($cronTracker);

    if ($cronFlag == TRUE) {

        $errMsg = "Already Lead Basic Cron is running . Datetime :" . date(" Y-m-d H:i:s");
        logErrors(BASIC_LEAD_LOG, "checkCronRunning", $errMsg);
        return false;
    }

    $startCronFlag = '';
    $startCronFlag = createCronStarter($cronTracker, BASIC_LEAD_LOG);
    
    if ($startCronFlag === FALSE || $startCronFlag == '') {
        logErrors(BASIC_LEAD_LOG, "startCronFlag", "Start Cron has exited");
        exit();
    }
     
    if($startCronFlag === TRUE) {
        
        $basicLead = TRUE;//Intialise it to true
        
        // Process Lead Data unitl getBasicLeads returns false
        // Gets single lead record, process it
        while (($basicLead = getBasicLeads()) != false) {
            //send the data to compiled table
            echo "\nLead is " . $basicLead[0]['lead_id'];
            $compiledLead = createCompiledLead($basicLead);
        }

        //Stop the cron;
        $stopCronFlag = stopCron($cronTracker, BASIC_LEAD_LOG);
        if ($stopCronFlag === FALSE) {
            $errMsg = "Failed to Stop Cron . Datetime :" . date(" Y-m-d H:i:s");
            logErrors(BASIC_LEAD_LOG, "failedStopCron", $errMsg);
            return FALSE;
            exit();
        }
        
        //For reference logging the Cron stopped time.
        if ($stopCronFlag == TRUE) {
            $errMsg = "Sucessfully Stopped Cron . Datetime :" . date(" Y-m-d H:i:s");
            logErrors(BASIC_LEAD_LOG, "stopCron", $errMsg);
            exit();
        }
    }
    //Get single lead record
} catch (Exception $e) {
    
    //register_shutdown_function("leadscronfailure",1);
    $errMsg = "Create  Cron Start Marker . Datetime :" . date(" Y-m-d H:i:s");
    logErrors(BASIC_LEAD_LOG, "createCronStartMarker", $errMsg, [$e->getMessage()]);
    exit();
    return false;
}

/**
 * This function logs all the php errors to the log file
 * and exits
 * @param type $errorFlag
 * @return boolean
 */
function basicLeadCronfailure($errorFlag = '') {
    
    if (!empty(error_get_last())) {
        
        $errMsg = "PHP error. Datetime :" . date(" Y-m-d H:i:s");
        logErrors(BASIC_LEAD_LOG, "genericPHPError", $errMsg, [error_get_last()]);
        
        $stopCronFlag = stopCron($cronTracker, BASIC_LEAD_LOG);
        exit();
    }
}
