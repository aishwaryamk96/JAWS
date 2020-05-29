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
//cronstatustracker file name
$cronTracker = "leadCompiledCron.txt";

register_shutdown_function("compiledLeadCronfailure", 1,$cronTracker);

//Nload leads module
load_module("leads");
// TK064
load_library("email"); 
// TK064
//cronstatustracker file name
$cronTracker = "leadCompiledCron.txt";

try {

    //Check the eny lead basic cron running
    
    $cronFlag = checkCronStatus($cronTracker);
	
    if ($cronFlag == TRUE) {

        $errMsg = "Already Cron is running . Datetime :" . date(" Y-m-d H:i:s");
        logErrors(COMPILED_LEAD_LOG, "checkCronRunning", $errMsg);
        return false;
    }

    $startCronFlag = '';
    $startCronFlag = createCronStarter($cronTracker, COMPILED_LEAD_LOG);
    
    if ($startCronFlag === FALSE || $startCronFlag == '') {
        logErrors(COMPILED_LEAD_LOG, "startCronFlag", "Start Cron has exited");
        exit();
    }
     
    if($startCronFlag === TRUE) {
        
        $compiledLead = TRUE;//Intialise it to true
        
        // Process Lead Data unitl getCompiledLeads returns false
        // Gets single lead record, process it
        while (($compiledLead = getCompiledLeads()) != FALSE) {
           
            //send the data to compiled table
            echo "\nLead is " . $compiledLead[0]['lead_id'];
			$apiPayload = newLsCRMActivity($compiledLead);
			
			// TK064 -Corporate lead send email
			$is_corporate_lead = $compiledLead[0]['referer'];
			if (strpos($is_corporate_lead, 'corporate') !== false) {
				sendCorporateLeadEmail($compiledLead[0]);
			}
			// TK064
			
            //Trigger LS API
            $lsApiResult = getLSApi($apiPayload, $compiledLead);
        }
        
        //Stop the cron;
        $stopCronFlag = stopCron($cronTracker, COMPILED_LEAD_LOG);
        if ($stopCronFlag === FALSE) {
            $errMsg = "Failed to Stop Cron . Datetime :" . date(" Y-m-d H:i:s");
            logErrors(COMPILED_LEAD_LOG, "failedStopCron", $errMsg);
            return FALSE;
            exit();
        }
        
        //For reference logging the Cron stopped time.
        if ($stopCronFlag == TRUE) {
            $errMsg = "Sucessfully Stopped Cron . Datetime :" . date(" Y-m-d H:i:s");
            logErrors(COMPILED_LEAD_LOG, "stopCron", $errMsg);
            exit();
        }
    }
    //Get single lead record
} catch (Exception $e) {
    
    //register_shutdown_function("leadscronfailure",1);
    $errMsg = "Create  Cron Start Marker . Datetime :" . date(" Y-m-d H:i:s");
    logErrors(COMPILED_LEAD_LOG, "createCronStartMarker", $errMsg, [$e->getMessage()]);
    exit();
    return false;
}

/**
 * This function logs all the php errors to the log file
 * and exits
 * @param type $errorFlag
 * @return boolean
 */
function compiledLeadCronfailure($errorFlag = '', $cronTracker) {
    
    if (!empty(error_get_last())) {
        
        $errMsg = "PHP error. Datetime :" . date(" Y-m-d H:i:s");
        logErrors(COMPILED_LEAD_LOG, "genericPHPError", $errMsg, [error_get_last()]);
        
        $stopCronFlag = stopCron($cronTracker, COMPILED_LEAD_LOG);
        
        error_clear_last();
        exit();
    }
}

/*
* This function is called for corporate lead email
*/
function sendCorporateLeadEmail($compiledLead)
{
	$compiledLead['title']='Corporate Lead';
	send_email("corporate.email.lead", [], $compiledLead);
}
