<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
define("ERRLOG_PATH","'");
//New Error Logging library
load_library("errormanager");

/**
 * This function checks if cront-tracker file exists in tmp folder
 * @param type $cronTracker
 * @return boolean
 */
function checkCronStatus($cronTracker){   
    
    $filename = '/tmp/'.$cronTracker;
 
    if (file_exists($filename)) {
        return TRUE;
    }
    
    return FALSE;
}

function createCronStarter($cronTracker, $logFile){   
    
    $content = "Cron started @ ".date("Y-m-d H:i:s");
    $filename = '/tmp/'.$cronTracker;
    try{
        $fp = fopen($filename,"x+");
        if($fp == FALSE) { return FALSE ;}
        fwrite($fp,$content);
        fclose($fp);
        
        $errMsg = "Sucessfully Started Cron . Datetime :" . date(" Y-m-d H:i:s");
        logErrors($logFile, "startedcron", $errMsg, [error_get_last()]);
            
        return TRUE;
    }catch(Exception $e){
        $errMsg = "Create Cron Start File . Datetime :" . date(" Y-m-d H:i:s");        
        logErrors($logFile, "createCronStartMarker", $errMsg, [$e->getMessage()]);
        return FALSE;
    }
}

/**
 * This function delete the crontracker file
 * @param type $cronTracker
 * @param type $logFile
 * @return boolean
 */
function stopCron($cronTracker, $logFile){   
    
    $filename = '/tmp/'.$cronTracker;
    try{        
        $stopCronFlag = unlink($filename);        
        return $stopCronFlag;
        
    }catch(Exception $e){
        $errMsg = "Failed to Stop Cron . Datetime :" . date(" Y-m-d H:i:s");        
        logErrors($logFile, "stopCron", $errMsg, [$e->getMessage()]);
        return FALSE;
    }
}
/**
 * This function checks if there was any lead picked from basic table for processing
 * 
 * @return boolean
 */
function checkBasicLeadProcessing($compiledFlag = '',$logFile) {

    register_shutdown_function("leadscronfailure");
    
    $table = getLeadTable($compiledFlag);
    //query
    $getProcessingLeadQry = " SELECT * FROM " . $table . " WHERE status = " . LEAD_PROCESSING;

    $leadResult = db_query($getProcessingLeadQry);

    if ($leadResult == false) {
        $errMsg = "Check Cron Status " . $table . " : DB error. Datetime :" . date(" Y-m-d H:i:s");
        $errData = $GLOBALS["jaws_db"]["error"];
        logErrors($logFile, "checkBasicLeadProcessing", $errMsg, [$errData]);

        return false;
    }

    return $leadResult;
}

/**
 * This is common function that returns lead table name based on input param
 * $compiledFlag = 1 , returns "user_leads_basic_compiled"
 * By default, it will return "user_leads_basic"
 * @param type $compiledFlag
 * @return string
 */
function getLeadTable($compiledFlag = 0) {

    switch ($compiledFlag) {
        case 1 :
            $tblName = "user_leads_basic_compiled";
            break;
        case 0 :
        default:
            $tblName = "user_leads_basic";
            break;
    }

    return $tblName;
}

/**
 * This is common function that updates status of lead based on inputs
 * 
 * @param type $leadId
 * @param type $status
 * @param type $compiledFlag
 * @return boolean
 */
function updateStatus($leadId, $status = 0, $compiledFlag = 0) {

    $table = getLeadTable($compiledFlag);

    $updateFlag = db_exec(" UPDATE " . $table . " SET status = " . db_sanitize($status) . " WHERE lead_id = " . $leadId . ";");

    // if $updateFlag == false, there was db exception and is stored in $GLOBALS variable.
    // Log the DB Error
    if ($updateFlag == false) {
        
        $logFile = BASIC_LEAD_LOG;
        if($compiledFlag == 1){
            $logFile = COMPILED_LEAD_LOG;
        }
        $errMsg = " Update Status: DB error. Datetime :" . date(" Y-m-d H:i:s");
        $errData = $GLOBALS["jaws_db"]["error"];
        logErrors($logFile, "updateStatus", $errMsg, [$errData]);

        return false;
    }

    return $updateFlag;
}

/**
 * This function fetches 1 Record of user_lead_basic table
 * @return boolean
 */
function getBasicLeads() {

    //leadId condition is WHERE clause removed 
    // select criteria of "capture_trigger" and select criteria of user info is retained - no changes done.
    // add new conditon : leadStatus must be new 
    $getBasicLeadQry = " SELECT * FROM user_leads_basic as lb ";
    $getBasicLeadQry.= " WHERE  lb.status = " . BASIC_NEW;
    $getBasicLeadQry.= " AND lb.lead_id > 5661993 AND (lb.user_id IS NOT NULL OR (lb.email IS NOT NULL AND lb.phone IS NOT NULL AND lb.name IS NOT NULL)) AND (lb.capture_trigger IN ('formsubmit', 'form-submit', 'reg', 'login', 'ws-gateway', 'cart', 'phoneupdate', 'reg.android', 'clickthrough')) ";
    $getBasicLeadQry.= " ORDER BY lb.lead_id ASC LIMIT 1";

    
    $leadResult = db_query($getBasicLeadQry);

    if ($leadResult === false) {
        $errMsg = "Basic Lead : There was some DB error. Datetime :" . date(" Y-m-d H:i:s");
        $errMsg.= "\n Query :\n ".$getBasicLeadQry;
        $errData = $GLOBALS["jaws_db"]["error"];
        logErrors(BASIC_LEAD_LOG, "getBasicLeads", $errMsg, [$errData]);

        return false;
    }

    if (!empty($leadResult)) {
        //Mark the record as processed
        $updateFlag = updateStatus($leadResult[0]['lead_id'], LEAD_PROCESSING);

        if ($updateFlag == FALSE) {
            $errMsg = " Failed Basic Lead Update Status: Datetime :" . date(" Y-m-d H:i:s");
            $errData = $GLOBALS["jaws_db"]["error"];
            logErrors(BASIC_LEAD_LOG, "getBasicLeads", $errMsg, [$errData]);

            return false;
        }
    }

    return $leadResult;
}

/**
 * This function fetches 1 Record of user_lead_basic table
 * @return boolean
 */
function getCompiledLeads() {

    //leadId condition is WHERE clause removed 
    // select criteria of "capture_trigger" and select criteria of user info is retained - no changes done.
    // add new conditon : leadStatus must be new 
    $getCompiledLeadQry = " SELECT * FROM user_leads_basic_compiled as lc ";
    $getCompiledLeadQry.= " WHERE  lc.status = " . db_sanitize(COMPILED_NEW);
    $getCompiledLeadQry.= " AND lc.lead_id > 435688 ORDER BY lc.lead_id ASC LIMIT 1";

    
    $leadResult = db_query($getCompiledLeadQry);

    if ($leadResult === false) {
        $errMsg = "Compiled Lead : There was some DB error. Datetime :" . date(" Y-m-d H:i:s");
        $errMsg.= "\n Query :\n ".$getCompiledLeadQry;
        $errData = $GLOBALS["jaws_db"]["error"];
        logErrors(COMPILED_LEAD_LOG, "getCompiledLeads", $errMsg, [$errData, error_get_last()]);
      
        return FALSE;
    }

    if (!empty($leadResult)) {
        //Mark the record as processed
        $updateFlag = updateStatus($leadResult[0]['lead_id'], LEAD_PROCESSING, 1);

        if ($updateFlag == FALSE) {
            $errMsg = " Failed Compiled Lead Update Status - LEAD_PROCESSING: Datetime :" . date(" Y-m-d H:i:s");
            $errData = $GLOBALS["jaws_db"]["error"];
            logErrors(COMPILED_LEAD_LOG, "getCompiledLeads", $errMsg, [$errData,error_get_last()]);

            return FALSE;
        }
    }

    return $leadResult;
}

/**
 * This function takes the array input Lead
 * process it to form the as needed to user_lead_basic_compiled table
 * @param array $leadData
 * @return array $parsedLead
 */
function createCompiledLead($leadData) {

    $parsedLead = [];
    foreach ($leadData as $lead) {
        //JA-113 changes
        updateLeadStatus($lead['lead_id'], BASIC_PROCESSED);
        //JA-113 ends
        $rec = array();
        $rec["lead_id"] = $lead['lead_id']; //JA-113 prod issue fix , lead id not passed

        $rec["user_id"] = "";
        $rec["meta"] = $lead["meta"];
        $rec["__tr"] = $lead["__tr"];
        $rec["cookies"] = $lead["cookies"];

        //changes done for JA-140
        $rec["name"] = trim($lead["name"]);
        $rec["email"] = trim($lead["email"]);
        $rec["phone"] = trim($lead["phone"]);
        //changes done for JA-140
        if (strlen($lead["user_id"]) == 0 || $lead["user_id"] === NULL) {
            $user = user_get_by_email(trim($lead["email"]));
            if ($user != false) {
                $rec["user_id"] = $user["user_id"];
            } 
        } else {
            $user = user_get_by_id($lead["user_id"]);
            $rec["user_id"] = $user["user_id"];
            $rec["name"] = $user["name"];
            $rec["email"] = $user["email"];
            ((strlen($user["phone"]) == 0) ? ($rec["phone"] = $lead["phone"]) : ($rec["phone"] = $user["phone"]));
        }

        // Check if the lead record is a duplicate (if it arrived within 2 secs)
        if (isset($leads_data[$rec["email"]])) {
            $date = date_create_from_format("Y-m-d H:i:s", $leads_data[$rec["email"]]);
            $date->add(new DateInterval("PT2S"));
            $create_date = date_create_from_format("Y-m-d H:i:s", $lead["create_date"]);
            if ($create_date < $date)
                continue;
        }

        $rec["ip"] = $lead["ip"];
        $rec["create_date"] = $lead["create_date"];
        $rec["event"] = $lead["capture_trigger"];
        $rec["referer"] = $lead["referer"];

        if (strcmp($lead["capture_trigger"], "formsubmit") == 0) {

            if (strcmp(trim($lead["ad_lp"]), 'www.jigsawacademy.com') == 0) {

                $query = "SELECT * FROM user_leads_basic WHERE (capture_trigger='pageload' OR capture_trigger='formload') AND (email=" . db_sanitize($rec["email"]);
                if (strlen($rec["user_id"]) > 0)
                    $query .= " OR user_id=" . $rec["user_id"];
                $query .= ") AND create_date<" . db_sanitize($lead["create_date"]) . " ORDER BY create_date DESC LIMIT 1;";
                $res_load_event = db_query($query);
                //if (!isset($res_load_event[0])) continue;

                $rec["page_url"] = $res_load_event[0]["referer"] ?? $lead["ad_url"] ?? '';
                $rec["landing_url"] = $res_load_event[0]["ad_url"] ?? $lead["ad_url"] ?? '';

                $rec["utm_source"] = $res_load_event[0]["utm_source"] ?? $lead["utm_source"] ?? '';
                $rec["utm_campaign"] = $res_load_event[0]["utm_campaign"] ?? $lead["utm_campaign"] ?? '';
                $rec["utm_term"] = $res_load_event[0]["utm_term"] ?? $lead["utm_term"] ?? '';
                $rec["utm_medium"] = $res_load_event[0]["utm_medium"] ?? $lead["utm_medium"] ?? '';
                $rec["utm_content"] = $res_load_event[0]["utm_content"] ?? $lead["utm_content"] ?? '';
                $rec["utm_segment"] = $res_load_event[0]["utm_segment"] ?? $lead["utm_segment"] ?? '';

                $rec["utm_numvisits"] = $res_load_event[0]["utm_numvisits"] ?? $lead["utm_numvisits"] ?? '';
                $rec["gcl_id"] = $lead["gcl_id"] ?? '';
                $rec["global_id_perm"] = $lead["global_id_perm"] ?? '';
                $rec["global_id_session"] = $lead["global_id_session"] ?? '';
                $rec["xuid"] = $res_load_event[0]["xuid"] ?? $lead["xuid"] ?? '';

                if (empty($rec["__tr"])) {

                    if (!empty($res_load_event[0]["__tr"])) {
                        $rec["__tr"] = $res_load_event[0]["__tr"];
                    }
                }
            } else {

                $rec["page_url"] = $lead["ad_url"] ?? '';
                $rec["landing_url"] = $lead["ad_url"] ?? '';

                if (trim($lead["ad_lp"]) == "naukri-lp") {
                    $rec["page_url"] = "naukri-lp";
                }

                $rec["utm_source"] = $lead["utm_source"] ?? '';
                $rec["utm_campaign"] = $lead["utm_campaign"] ?? '';
                $rec["utm_term"] = $lead["utm_term"] ?? '';
                $rec["utm_medium"] = $lead["utm_medium"] ?? '';
                $rec["utm_content"] = $lead["utm_content"] ?? '';
                $rec["utm_segment"] = $lead["utm_segment"] ?? '';

                $rec["utm_numvisits"] = $lead["utm_numvisits"] ?? '';
                $rec["gcl_id"] = $lead["gcl_id"] ?? '';
                $rec["global_id_perm"] = $lead["global_id_perm"] ?? '';
                $rec["global_id_session"] = $lead["global_id_session"] ?? '';
                $rec["xuid"] = $lead["xuid"] ?? '';
            }

            if (empty($rec["__tr"])) {
                $rec["__tr"] = $lead["__tr"];
            }
        } elseif (strcmp($lead["capture_trigger"], "reg") == 0) {

            // URL decode the ru portion first!!
            $rec["page_url"] = urldecode(explode("ru=", $lead["ad_url"])[1]);

            $query = "SELECT * FROM user_leads_basic WHERE (capture_trigger='pageload' OR capture_trigger='formload') AND (email=" . db_sanitize($rec["email"]);
            if (strlen($rec["user_id"]) > 0) {
                $query .= " OR user_id=" . $rec["user_id"];
            }
            $query .= ") AND capture_type='url' AND create_date<" . db_sanitize($lead["create_date"]) . " ORDER BY create_date DESC LIMIT 1;";
            $res_load_event = db_query($query);

            if (!isset($res_load_event[0])) {

                $rec["landing_url"] = "";
                $rec["utm_source"] = "";
                $rec["utm_campaign"] = "";
                $rec["utm_term"] = "";
                $rec["utm_medium"] = "";
                $rec["utm_content"] = "";
                $rec["utm_segment"] = "";
            } else {

                $load_event = $res_load_event[0];
                $rec["landing_url"] = $load_event["ad_url"];

                $rec["utm_source"] = $load_event["utm_source"];
                $rec["utm_campaign"] = $load_event["utm_campaign"];
                $rec["utm_term"] = $load_event["utm_term"];
                $rec["utm_medium"] = $load_event["utm_medium"];
                $rec["utm_content"] = $load_event["utm_content"];
                $rec["utm_segment"] = $load_event["utm_segment"];
                $rec["xuid"] = $load_event["xuid"] ?? $lead["xuid"] ?? '';

                if (empty($rec["__tr"]) && !empty($load_event["__tr"])) {
                    $rec["__tr"] = $load_event["__tr"];
                }
            }

            $rec["gcl_id"] = $lead["gcl_id"];
            $rec["global_id_perm"] = $lead["global_id_perm"];
            $rec["global_id_session"] = $lead["global_id_session"];
            $rec["utm_numvisits"] = $lead["utm_numvisits"];

            if (empty($rec["__tr"])) {
                $rec["__tr"] = $lead["__tr"];
            }
        } elseif (strcmp($lead["capture_trigger"], "ws-gateway") == 0) {

            $query = "SELECT * FROM user_leads_basic WHERE (capture_trigger='pageload' OR capture_trigger='formload') AND (email=" . db_sanitize($rec["email"]);
            if (strlen($rec["user_id"]) > 0) {
                $query .= " OR user_id=" . $rec["user_id"];
            }
            $query .= ") AND create_date<" . db_sanitize($lead["create_date"]) . " ORDER BY create_date DESC LIMIT 1;";
            $res_load_event = db_query($query);

            $rec["page_url"] = $res_load_event[0]["referer"] ?? $lead["ad_url"] ?? '';
            $rec["landing_url"] = $res_load_event[0]["ad_url"] ?? $lead["ad_url"] ?? '';

            $rec["utm_source"] = $res_load_event[0]["utm_source"] ?? $lead["utm_source"] ?? '';
            $rec["utm_campaign"] = $res_load_event[0]["utm_campaign"] ?? $lead["utm_campaign"] ?? '';
            $rec["utm_term"] = $res_load_event[0]["utm_term"] ?? $lead["utm_term"] ?? '';
            $rec["utm_medium"] = $res_load_event[0]["utm_medium"] ?? $lead["utm_medium"] ?? '';
            $rec["utm_content"] = $res_load_event[0]["utm_content"] ?? $lead["utm_content"] ?? '';
            $rec["utm_segment"] = $res_load_event[0]["utm_segment"] ?? $lead["utm_segment"] ?? '';

            $rec["utm_numvisits"] = $res_load_event[0]["utm_numvisits"] ?? $lead["utm_numvisits"] ?? '';
            $rec["gcl_id"] = $lead["gcl_id"] ?? '';
            $rec["global_id_perm"] = $lead["global_id_perm"] ?? '';
            $rec["global_id_session"] = $lead["global_id_session"] ?? '';
            $rec["xuid"] = $res_load_event[0]["xuid"] ?? $lead["xuid"] ?? '';

            if (empty($rec["__tr"])) {

                if (!empty($res_load_event[0]["__tr"])) {
                    $rec["__tr"] = $res_load_event[0]["__tr"];
                } else {
                    $rec["__tr"] = $lead["__tr"];
                }
            }
        } elseif (strcmp($lead["capture_trigger"], "login") == 0) {

            if (stripos($lead["ad_url"], 'checkout') !== false) {

                $rec["event"] = 'checkout';

                // URL decode the ru portion first!!
                $rec["page_url"] = urldecode(explode("ru=", $lead["ad_url"])[1]);

                $query = "SELECT * FROM user_leads_basic WHERE (capture_trigger='pageload' OR capture_trigger='formload') AND (email=" . db_sanitize($rec["email"]);
                if (strlen($rec["user_id"]) > 0) {
                    $query .= " OR user_id=" . $rec["user_id"];
                }
                $query .= ") AND capture_type='url' AND create_date < " . db_sanitize($lead["create_date"]) . " ORDER BY create_date DESC LIMIT 1;";
                $res_load_event = db_query($query);

                if (!isset($res_load_event[0])) {

                    $rec["landing_url"] = "";
                    $rec["utm_source"] = "";
                    $rec["utm_campaign"] = "";
                    $rec["utm_term"] = "";
                    $rec["utm_medium"] = "";
                    $rec["utm_content"] = "";
                    $rec["utm_segment"] = "";
                } else {

                    $load_event = $res_load_event[0];
                    $rec["landing_url"] = $load_event["ad_url"];

                    $rec["utm_source"] = $load_event["utm_source"];
                    $rec["utm_campaign"] = $load_event["utm_campaign"];
                    $rec["utm_term"] = $load_event["utm_term"];
                    $rec["utm_medium"] = $load_event["utm_medium"];
                    $rec["utm_content"] = $load_event["utm_content"];
                    $rec["utm_segment"] = $load_event["utm_segment"];
                    $rec["xuid"] = $load_event["xuid"] ?? $lead["xuid"] ?? '';

                    if (empty($rec["__tr"]) && !empty($lead["__tr"])) {
                        $rec["__tr"] = $load_event["__tr"];
                    }
                }

                $rec["gcl_id"] = $lead["gcl_id"];
                $rec["global_id_perm"] = $lead["global_id_perm"];
                $rec["global_id_session"] = $lead["global_id_session"];
                $rec["utm_numvisits"] = $lead["utm_numvisits"];

                if (empty($rec["__tr"])) {
                    $rec["__tr"] = $lead["__tr"];
                }
            } else {
                continue;
            }
        } elseif (strcmp($lead["capture_trigger"], "cart") == 0) {

            $query = "SELECT * FROM user_leads_basic WHERE (capture_trigger='pageload' OR capture_trigger='formload') AND (email=" . db_sanitize($rec["email"]);
            if (strlen($rec["user_id"]) > 0) {
                $query .= " OR user_id=" . $rec["user_id"];
            }
            $query .= ") AND create_date<" . db_sanitize($lead["create_date"]) . " ORDER BY create_date DESC LIMIT 1;";
            $res_load_event = db_query($query);

            $rec["page_url"] = $lead["ad_url"] ?? '';
            $rec["landing_url"] = $res_load_event[0]["ad_url"] ?? $lead["ad_url"] ?? '';

            $rec["utm_source"] = $res_load_event[0]["utm_source"] ?? $lead["utm_source"] ?? '';
            $rec["utm_campaign"] = $res_load_event[0]["utm_campaign"] ?? $lead["utm_campaign"] ?? '';
            $rec["utm_term"] = $res_load_event[0]["utm_term"] ?? $lead["utm_term"] ?? '';
            $rec["utm_medium"] = $res_load_event[0]["utm_medium"] ?? $lead["utm_medium"] ?? '';
            $rec["utm_content"] = $res_load_event[0]["utm_content"] ?? $lead["utm_content"] ?? '';
            $rec["utm_segment"] = $res_load_event[0]["utm_segment"] ?? $lead["utm_segment"] ?? '';

            $rec["utm_numvisits"] = $res_load_event[0]["utm_numvisits"] ?? $lead["utm_numvisits"] ?? '';
            $rec["gcl_id"] = $lead["gcl_id"] ?? '';
            $rec["global_id_perm"] = $lead["global_id_perm"] ?? '';
            $rec["global_id_session"] = $lead["global_id_session"] ?? '';
            $rec["xuid"] = $res_load_event[0]["xuid"] ?? $lead["xuid"] ?? '';

            if (empty($rec["__tr"])) {

                if (!empty($res_load_event[0]["__tr"])) {
                    $rec["__tr"] = $res_load_event[0]["__tr"];
                } else {
                    $rec["__tr"] = $lead["__tr"];
                }
            }
        } else if (strcmp($lead["capture_trigger"], "phoneupdate") == 0) {

            $reg = db_query("SELECT * FROM user_leads_basic_compiled WHERE event='reg' AND email=" . db_sanitize($lead["email"]) . " LIMIT 1;");
            if (!empty($reg[0])) {
                $reg = $reg[0];
            }

            $rec["phone"] = $lead["phone"];

            $rec["utm_source"] = "";
            $rec["utm_campaign"] = "";
            $rec["utm_term"] = "";
            $rec["utm_medium"] = "";
            $rec["utm_content"] = "";
            $rec["utm_segment"] = "";

            $rec["utm_numvisits"] = "";
            $rec["gcl_id"] = "";
            $rec["global_id_perm"] = "";
            $rec["global_id_session"] = "";
            $rec["xuid"] = "";

            $rec["page_url"] = $reg["page_url"] ?? "";
            $rec["landing_url"] = "";

            $rec["referrer"] = $reg["referrer"] ?? "";

            activity_debug_start();
            activity_debug_log("phoneupdate => " . json_encode($rec));
        } else if (strcmp($lead["capture_trigger"], "reg.android") == 0) {

            $rec["utm_source"] = "";
            $rec["utm_campaign"] = "";
            $rec["utm_term"] = "";
            $rec["utm_medium"] = "";
            $rec["utm_content"] = "";
            $rec["utm_segment"] = "";

            $rec["utm_numvisits"] = "";
            $rec["gcl_id"] = "";
            $rec["global_id_perm"] = "";
            $rec["global_id_session"] = "";
            $rec["xuid"] = "";

            $rec["page_url"] = "";
            $rec["landing_url"] = "";
        } else if (strcmp($lead["capture_trigger"], "clickthrough") == 0) {

            if ($lead["ad_lp"] == "referral_email") {

                $rec["page_url"] = $lead["ad_url"] ?? '';
                $rec["landing_url"] = $lead["ad_url"] ?? '';

                $rec["utm_source"] = $lead["utm_source"] ?? '';
                $rec["utm_campaign"] = $lead["utm_campaign"] ?? '';
                $rec["utm_term"] = $lead["utm_term"] ?? '';
                $rec["utm_medium"] = $lead["utm_medium"] ?? '';
                $rec["utm_content"] = $lead["utm_content"] ?? '';
                $rec["utm_segment"] = $lead["utm_segment"] ?? '';

                $rec["utm_numvisits"] = $lead["utm_numvisits"] ?? '';
                $rec["gcl_id"] = $lead["gcl_id"] ?? '';
                $rec["global_id_perm"] = $lead["global_id_perm"] ?? '';
                $rec["global_id_session"] = $lead["global_id_session"] ?? '';
                $rec["xuid"] = $lead["xuid"] ?? '';

                $rec["event"] = "referral";
            }
        }

        // Save the data before sanitizing it
        $data = $rec;
        //commented during LS Dashboard JA-113 production issu- status not changed
        //$leads_arr[] = $data; 

        $leads_data[$rec["email"]] = $lead["create_date"];

        // IOT Edit : Overwrite utm source if coming from IOT pages
        $rec["utm_source"] = ((stripos($rec["page_url"], '/iot') !== false) ||
                (stripos($rec["landing_url"], '/iot') !== false) ||
                (stripos($rec["referer"], '/iot') !== false)) ? 'iot' : $rec["utm_source"];

        // Sanitize the data
        $rec["name"] = db_sanitize($rec["name"]);
        $rec["email"] = db_sanitize($rec["email"]);
        $rec["phone"] = db_sanitize($rec["phone"]);
        $rec["utm_source"] = db_sanitize($rec["utm_source"]);
        $rec["utm_campaign"] = db_sanitize($rec["utm_campaign"]);
        $rec["utm_term"] = db_sanitize($rec["utm_term"]);
        $rec["utm_medium"] = db_sanitize($rec["utm_medium"]);
        $rec["utm_content"] = db_sanitize($rec["utm_content"]);
        $rec["utm_segment"] = db_sanitize($rec["utm_segment"]);
        $rec["utm_numvisits"] = db_sanitize($rec["utm_numvisits"]);
        $rec["gcl_id"] = db_sanitize($rec["gcl_id"]);
        $rec["global_id_perm"] = db_sanitize($rec["global_id_perm"]);
        $rec["global_id_session"] = db_sanitize($rec["global_id_session"]);
        $rec["xuid"] = db_sanitize($rec["xuid"]);
        $rec["page_url"] = db_sanitize($rec["page_url"]);
        $rec["landing_url"] = db_sanitize($rec["landing_url"]);
        $rec["referer"] = db_sanitize($rec["referer"]);
        $rec["ip"] = db_sanitize($rec["ip"]);
        $rec["create_date"] = db_sanitize($rec["create_date"]);
        $rec["event"] = db_sanitize($rec["event"]);
        $rec["meta"] = db_sanitize($rec["meta"]);
        $rec["cookies"] = db_sanitize($rec["cookies"]);
        if (!empty($rec["__tr"])) {
            $rec["__tr"] = db_sanitize($rec["__tr"]);
        } else {
            $rec["__tr"] = "NULL";
        }

        $insert = "INSERT INTO user_leads_basic_compiled (" .
                ((strlen($rec["user_id"]) == 0) ? "" : "user_id,") .
                "basic_lead_id, name,
								email,
								phone,
								utm_source,
								utm_campaign,
								utm_term,
								utm_medium,
								utm_content,
								utm_segment,
								utm_numvisits,
								gcl_id,
								global_id_perm,
								global_id_session,
								xuid,
								page_url,
								landing_url,
								referer,
								ip,
								create_date,
								event,
								meta,
								cookies,
								__tr
								)
							VALUES (" .
                ((strlen($rec["user_id"]) == 0) ? "" : $rec["user_id"] . ", ") .
                $rec["lead_id"] . ", " .
                $rec["name"] . ", " .
                $rec["email"] . ", " .
                $rec["phone"] . ", " .
                $rec["utm_source"] . ", " .
                $rec["utm_campaign"] . ", " .
                $rec["utm_term"] . "," .
                $rec["utm_medium"] . ", " .
                $rec["utm_content"] . ", " .
                $rec["utm_segment"] . ", " .
                $rec["utm_numvisits"] . ", " .
                $rec["gcl_id"] . ", " .
                $rec["global_id_perm"] . ", " .
                $rec["global_id_session"] . ", " .
                $rec["xuid"] . ", " .
                $rec["page_url"] . ", " .
                $rec["landing_url"] . ", " .
                $rec["referer"] . ", " .
                $rec["ip"] . ", " .
                $rec["create_date"] . ", " .
                $rec["event"] . ", " .
                $rec["meta"] . ", " .
                $rec["cookies"] . ", " .
                $rec["__tr"] . ");";
        $stat = db_exec($insert);
        
        if($stat == false){
            
            $errMsg = "Create Compiled data failed : DB error. Lead Id.".$lead['lead_id'] ." Datetime :" . date(" Y-m-d H:i:s");
            $errData = $GLOBALS["jaws_db"]["error"];
            logErrors(BASIC_LEAD_LOG, "createCompiledLead", $errMsg, [$errData]);
            $stopCronFlag = stopCron('leadBasicCron.txt', BASIC_LEAD_LOG);
            error_clear_last();
            exit();
        }

        $compiledLeadId = db_get_last_insert_id();

        //JA-113 changes-prod issue
        $data['compiledLeadId'] = $compiledLeadId;
        echo "\n Compiled Lead id".$compiledLeadId."\n";
        $parsedLead = $data;
    }
    
    return $parsedLead;
}

/**
 * This function saves parsed-lead-data into compiled data
 * 
 * @param type $basicLeadData
 * @param 
 */
function newLsCRMActivity($leads) {

    //function ls_lead_capture($leads, $new = true, $return = false) {

    load_library("url");
    
    //$leads = $leads[0];
    
    foreach ($leads as $lead) {

        $api_url = "LeadManagement.svc/Lead.Capture";

        $key_mapping = LS_KEY_MAPPING;
        
        
        if (isset($lead["phone"]) && strlen($lead["phone"]) < 10) {
            unset($lead["phone"]);
        } else {
            $lead["phone"] = str_replace("+91-", "", $lead["phone"]);
        }

        
        //Checking ls_api table if email record exists - This to be removed
        // @TODO discuss with Kiran
        $old_lead = was_lead_prime_data_sent_before($lead["email"]);

        if (!$old_lead) {
            if (empty($lead["utm_source"])) {

                $lead["utm_source"] = "Social login";
                // $lead["category"] = "Online";
            } else if (strpos($lead["utm_source"], "FREECOURSE") === 0) {

                $free_course_info = explode("-", $lead["utm_source"]);

                $lead["utm_source"] = "FREECOURSE";
                $lead["channel"] = "Free Trial";
            }
        }

        if ($lead["page_url"] == "coupon") {
            $lead["category"] = "Mobile App";
        }

        $meta = [];
        if (!empty($lead["meta"])) {
            $meta = json_decode($lead["meta"], true) ?: [];
        }

        if (!empty($meta["MXCProspectId"])) {
            $lead["lead_id"] = $meta["MXCProspectId"];
        }

        if (!empty($meta["form_name"])) {

            $lead["utm_term"] = $meta["form_name"];
            if ($meta["form_name"] == "resource-download" && ($meta["embed_url"] ?? "") == "www.jigsawacademy.com/online-analytics-training/") {
                $lead["utm_term"] = "IPBA Brochure";
            }
        }
        if (!empty($meta["city"])) {

            $lead["mx_City"] = $meta["city"];
            $lead["mx_Location"] = $meta["city"];
        }
        if (!empty($meta["course"])) {
            $lead["course"] = $meta["course"];
        }
        if (!empty($meta["time_to_call"])) {
            $lead["mx_Preferred_date"] = $meta["time_to_call"];
        }

        //JA-172
        if (!empty($meta["Qualification"])) {
            
            $lead["mx_Qualification"] = $meta["Qualification"];
        }
        
        if (!empty($meta["Experience"])) {
            $lead["mx_Total_Experience"] = $meta["Experience"];
        }
        //JA-172
        $page_url = trim($lead["page_url"], "/");
        if ($page_url == "wp-admin/admin-ajax.php") {
            $page_url = trim($lead["referer"], "/");
            $lead["page_url"] = $page_url;
        }

        //@TODO remove hardcoded URL
        if (strpos($page_url, "http") !== 0) {
            if (strpos($page_url, "www.jigsawacademy.com") !== 0) {
                $page_url = "www.jigsawacademy.com/" . trim($page_url, "/");
            }
            $page_url = "https://" . $page_url;
        }

        $url_components = parse_url($page_url);
        $built_url = trim($url_components["path"] ?? "", "/");

        if (strpos($built_url, "cloud") !== false) {
            // $lead["category"] = "Cloud-Computing";
        } elseif (strpos($built_url, "analytics-courses-trial") !== false) {
            $lead["channel"] = "Free Trial";
        } else if (stripos($built_url, "corporate") !== false || stripos($built_url, "about-us/careers") !== false) {

            if (!$old_lead) {
                $lead["source_2"] = "Corporate";
            } else {
                $lead["source_2_2"] = "Corporate";
            }

            $lead["company"] = $meta["company"];
            $lead["comments"] = $meta["outcomes"];
        } else if (strpos($built_url, "iot") !== false) {

            if ($built_url == "iot-beginners-course") {
                $lead["channel"] = "IOT_FreeTrial";
            }
        } else if ($lead["event"] == "referral") {

            if (!$old_lead) {
                $lead["source_2"] = "Referrals";
            } else {
                $lead["source_2_2"] = "Referrals";
            }
        } else {

            if (isset($lead["category"]) && $lead["category"] ?? "" != "Mobile App") {
                $lead["category"] = "Online";
            }
        }

        if (!empty($meta["form_name"])) {

            if ($meta["form_name"] == "pathfinder") {

                if (!$old_lead) {
                    $lead["source_2"] = "Organic";
                } else {
                    $lead["source_2_2"] = "Organic";
                }

                $lead["category"] = "Online";
                $lead["channel"] = "Pathfinder";
                $lead["path_finder_thingy"] = $meta["path_name"];
            }
        }

//        if (($lead["source_2"] ?? "") == "Corporate") {
//
//            // Corporate keys
//            define("AccessKey", "u\$r98364e50e96ad22f9ce3f40f2f2b3597");
//            define("SecretKey", "75dfc985eefeb4c92a6eaeebb517155d049e840a");
//        } else {
//
//            define("AccessKey", "u\$r6daf2e31c28ab58d15cb696d4e0f6a43");
//            define("SecretKey", "95c34e5756021c31bc0ded96a0fafd70320cd9a2");
//        }

        if ($old_lead) {

            if ($old_lead == 1) {
                $lead["page_url_2"] = $lead["page_url"];
            } else {
                $lead["page_url_3"] = $lead["page_url"];
            }

            unset($lead["page_url"]);
            unset($lead["source_2"]);
            unset($lead["channel"]);
            unset($lead["category"]);
        }

        $payload = [];
        foreach ($lead as $key => $value) {

            if (empty(trim($value))) {
                continue;
            }

            if (isset($key_mapping[$key])) {
                $payload[] = ["Attribute" => $key_mapping[$key], "Value" => $value];
            } elseif (strpos($key, "mx_") === 0) {
                $payload[] = ["Attribute" => $key, "Value" => $value];
            }
        }

        return $payload;

//        if (($response = json_decode(ls_api($api_url, $payload, $lead["email"], [], $lead), true)) === false) {
//            //return false;
//            //JA-113 LS API gave no response
//            //JA-113 - update lead status in compiled table to 5
//            updateLeadStatus($lead['compiledLeadId'], COMPILED_NO_RESPONSE, 1);
//            //JA-113 ends
//        }
//        if (empty($response["Status"]) || $response["Status"] != "Success" || empty($response["Message"]["Id"])) {
//            //return false;
//            //JA-113 there was error in LS response
//            //JA-113 - update lead status in compiled table to 4
//            updateLeadStatus($lead['compiledLeadId'], COMPILED_FAILURE, 1);
//            //JA-113 ends
//        }
//        //JA-113 changes
//        if (isset($response["Status"]) && $response["Status"] == "Success") {
//            //return false;
//            //success LS API 
//            //update lead status in compiled table to 4
//            updateLeadStatus($lead['compiledLeadId'], COMPILED_SUCCESS, 1);
//        }
//        //JA-113 ends
//
//
//        $leadIdCrm = $response["Message"]["RelatedId"];
//
//        //case of new Account functionality
//        newLsAccountUpdates($lead, $new);
//
//        if ($new) {
//            update_lead_info($lead["email"], $lead["phone"] ?? "", $leadIdCrm, []);
//        }
//
//        if ($return) {
//            return $leadIdCrm;
//        }
    }
}

function logLSApiResponse($apiResponse, $compiledLead){
    
    $content = "************************************************\n";
    $content.= "Compiled Lead Id : ".$compiledLead[0]['lead_id']." - Date : ".date("Y-m-d H:i:s")."\n";
    $content.= "-----------------------------------------------------\n";
    $content.= json_encode($apiResponse);
    $content.= "************************************************\n";
    
    $filename = LS_API_LOG;
    try{
        $fp = fopen($filename,"a+");
        if($fp == FALSE) { return FALSE ;}
        fwrite($fp,$content);
        fclose($fp);
        
        return TRUE;
    }catch(Exception $e){
        $errMsg = "Create Cron Start File . Datetime :" . date(" Y-m-d H:i:s");        
        logErrors(COMPILED_LEAD_LOG, "createCronStartMarker", $errMsg, [$e->getMessage()]);
        return FALSE;
    }
}

function saveLSApiResponse($apiResponse, $apiRequest, $compiledLead){
    
    error_clear_last();
    $updateFlag = db_exec(" UPDATE user_leads_basic_compiled SET ls_response = " . db_sanitize($apiResponse) . " , ls_request = ". db_sanitize(json_encode($apiRequest))." WHERE lead_id = " . db_sanitize($compiledLead[0]['lead_id']));

    // if $updateFlag == false, there was db exception and is stored in $GLOBALS variable.
    // Log the DB Error
    if ($updateFlag == false) {
        
        $logFile = COMPILED_LEAD_LOG;
        $errMsg = " Update Status: DB error. Datetime :" . date(" Y-m-d H:i:s");
        $errData = $GLOBALS["jaws_db"]["error"];
        logErrors($logFile, "updateStatus", $errMsg, [$errData]);

    }
}
/**
 * This function triggers the LS API
 * and captures the response to log file
 * @param type $payload
 * @param type $lead
 */
function getLSApi($payload, $lead){
    
        
        updateStatus($lead[0]['lead_id'], COMPILED_API, 1);
         
        $apiResponse = lsApi(LS_CAPTURE_API, $payload, $lead[0]["email"], [], $lead, TRUE);
        
        //print_r($apiResponse);
        if($apiResponse === FALSE){
            $errMsg = "Curl Failure. Lead-Id :".$lead[0]['lead_id']." Datetime :" . date(" Y-m-d H:i:s");              logErrors(COMPILED_LEAD_LOG, "getLSApi", $errMsg,[error_get_last()]);            
            stopCron("leadCompiledCron.txt", COMPILED_LEAD_LOG);
            exit();
        }
        
        //Dump response to log file.
        $logResp = logLSApiResponse($apiResponse, $lead);
        if($logResp == FALSE){
            $errMsg = "Failed to Log LS API Response. Lead-Id :".$lead[0]['lead_id']." Datetime :" . date(" Y-m-d H:i:s");        
            logErrors(COMPILED_LEAD_LOG, "createCronStartMarker", $errMsg, error_get_last());
        }
        
        //Save the response to db table column
        // the data is dumped to response column of the user_leads_basic_compiled table;
        saveLSApiResponse($apiResponse, $payload, $lead);
        
        $response = json_decode($apiResponse, true);
        if ( $response === false) {
            //return false;
            //JA-113 LS API gave no response
            //JA-113 - update lead status in compiled table to 5
            updateStatus($lead[0]['lead_id'], COMPILED_NO_RESPONSE, 1);
            //JA-113 ends
        }
        
        if (empty($response["Status"]) || $response["Status"] != "Success" || empty($response["Message"]["Id"])) {
            //return false;
            //JA-113 there was error in LS response
            //JA-113 - update lead status in compiled table to 4
            updateStatus($lead[0]['lead_id'], COMPILED_FAILURE, 1);
            //JA-113 ends
        }
        //JA-113 changes
        if (isset($response["Status"]) && $response["Status"] == "Success") {
            //return false;
            //success LS API 
            //update lead status in compiled table to 4
            updateStatus($lead[0]['lead_id'], COMPILED_SUCCESS, 1);
        }
        //JA-113 ends
}
function newLsAccountUpdates($lead, $new) {

    $newApiurl = 'LeadManagement.svc/Lead.Capture';

    $payload = [];
    $keyMapping = LS_KEY_MAPPING;
    foreach ($lead as $key => $value) {

        if (empty(trim($value))) {
            continue;
        }

        if (isset($keyMapping[$key])) {
            $payload[] = ["Attribute" => $keyMapping[$key], "Value" => $value];
        } elseif (strpos($key, "mx_") === 0) {
            $payload[] = ["Attribute" => $key, "Value" => $value];
        }
    }
    if (($response = json_decode(lsApi(LS_CAPTURE_API, $payload, $lead['email'], [], $lead, true), true)) === false) {
        //return false;
    }
    if (empty($response["Status"]) || $response["Status"] != "Success" || empty($response["Message"]["Id"])) {
        //return false;
    }

    $leadIdCrm = $response["Message"]["RelatedId"];

    if ($new) {
        update_lead_info_new($lead["email"], $lead["phone"] ?? "", $leadIdCrm, []);
    }
}

function find_lead($email, $lead_id = false) {

    $lead = db_query("SELECT * FROM ls_leads WHERE email = " . db_sanitize($email) . (!empty($lead_id) ? " OR lead_id = " . db_sanitize($lead_id) : "") . ";");
    if (empty($lead)) {
        return false;
    }

    return $lead;
}

function find_lead_new($email, $lead_id = false) {

    $lead = db_query("SELECT * FROM ls_leads_new WHERE email = " . db_sanitize($email) . (!empty($lead_id) ? " OR lead_id = " . db_sanitize($lead_id) : "") . ";");
    if (empty($lead)) {
        return false;
    }

    return $lead;
}

function update_lead_info_new($email, $phone, $lead_id, $lead_data = []) {

    if (!empty(find_lead($email, $lead_id))) {
        return;
    }

    $email = db_sanitize($email);
    $phone = db_sanitize($phone);
    $lead_id = db_sanitize($lead_id);
    $lead_data = db_sanitize(json_encode($lead_data));

    $newLsLeadFlag = db_exec("INSERT INTO ls_leads_new (email, phone, lead_id, lead_data) VALUES (" . $email . ", " . $phone . ", " . $lead_id . ", " . $lead_data . ");");
    if ($newLsLeadFlag == false) {
        activity_create("critical", "lead.cron.status.update", "fail", "", "", "", "", $lead_id, "logged");
    }
}

function lsApi($api_url, $data, $id, $params = [], $lead = [], $newConfig = FALSE) {

    //JA-113 - update lead status in compiled table to 2
//    if ($newConfig == FALSE & !empty($lead)) {
//        updateLeadStatus($lead[0]['lead_id'], COMPILED_API, 1);
//    }
    //JA-113 -ends
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, api_url_construct($api_url, $params, $newConfig));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    if (!empty($data)) {

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    try{
        $response = curl_exec($ch);
    } catch (Exception $e){
        $errMsg = "Curl Request Error. Datetime :" . date(" Y-m-d H:i:s");        
        logErrors(COMPILED_LEAD_LOG, "ls_api", $errMsg, [$e->getMessage(), error_get_last()]);
        error_clear_last();
        return FALSE;
    }
    //Check for CURL errors
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);        
        $errMsg = "Curl Request Error. Datetime :" . date(" Y-m-d H:i:s");        
        logErrors(COMPILED_LEAD_LOG, "ls_api", $errMsg, [$error_msg], error_get_last());
        error_clear_last();
        return FALSE;
        
    }
    curl_close($ch);

//    if ($newConfig === TRUE) {
//        db_exec("INSERT INTO ls_new_api (email, request, response) VALUES (" . db_sanitize($id) . ", " . db_sanitize(json_encode($data)) . ", " . db_sanitize(json_encode($response)) . ");");
//    } else {
//        db_exec("INSERT INTO ls_api (email, request, response) VALUES (" . db_sanitize($id) . ", " . db_sanitize(json_encode($data)) . ", " . db_sanitize(json_encode($response)) . ");");
//    }
    return $response;
}

function api_url_construct($api_url, $params = [], $apiNewConfig = false) {

    if ($apiNewConfig == true) {
        $url = LS_DOMAIN . $api_url . "?accessKey=" . LS_ACCOUNT_RETAIL_NEW_ACCESS . "&secretKey=" . LS_ACCOUNT_RETAIL_NEW_SECRET;
    } else {
        $url = LS_DOMAIN .$api_url. "?accessKey=" . LS_ACCOUNT_RETAIL_NEW_ACCESS . "&secretKey=" . LS_ACCOUNT_RETAIL_NEW_SECRET;
    }
    
    $extra_params = [];
    foreach ($params as $key => $value) {
        $extra_params[] = $key . "=" . $value;
    }

    if (!empty($extra_params)) {
        $extra_params = "&" . implode("&", $extra_params);
    } else {
        $extra_params = "";
    }

    return $url . $extra_params;
}
