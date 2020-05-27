<?php

// Prevent exclusive access
if (!defined("JAWS")) {
    header("HTTP/1.1 401 Unauthorized");
    die();
}

// Init Session
auth_session_init();

// Auth Check - Expecting Session Only !
if ((!auth_session_is_logged()) || (!auth_session_is_allowed("dash"))) {
    header("HTTP/1.1 401 Unauthorized");
    die();
}

//Loggedin USer
$loginUserId = $_SESSION["user"]["user_id"];


if (count($_POST) == 0) {
    $_POST = json_decode(file_get_contents('php://input'), true);
}

$currentInstlDataArr = [];

$validationStatus = validateEditInstallment($_POST);

if (count($validationStatus) > 1 && $validationStatus['status'] === TRUE) {

    updateData($validationStatus);
} else {
    die(json_encode(array("status" => false, "message" => "There was error processing data. Please try again after sometime!")));
}

function validateEditInstallment($apiInput) {

    $errMsg = '';
    if (empty($apiInput) || !is_array($apiInput)) {
        $errMsg = "Invalid request";
    }

    //check for empty
    if (empty($apiInput['subs_id']) || empty($apiInput['pay_id']) || empty($apiInput['user_id'])) {
        $errMsg = "Invalid data";
    }

    //Check the data exist for combination of user+subs_id+pay_id

    $currentInstlDataArr = db_query("SELECT * FROM payment_instl WHERE subs_id=" . db_sanitize($apiInput['subs_id']) . " AND pay_id=" . db_sanitize($apiInput['pay_id']) . " and user_id=" . db_sanitize($apiInput['user_id']));

    if ($currentInstlDataArr == false) {
        $errMsg = "There was some data error.";
    } elseif (count($currentInstlDataArr) == 0) {
        $errMsg = 'Invalid data';
    }

    //@TODO if needed installment validation by pay_isntl_id
    //return
    if ($errMsg != '') {
        die(json_encode(array("status" => FALSE, "message" => $errMsg)));
    }

    //case of successfull db record found , return data
    return ['status' => TRUE, 'dbInstlData' => $currentInstlDataArr, 'apiInput' => $apiInput];
}

function updateData($instlData) {

    $dbInstlData = $instlData['dbInstlData'];
    $apiInput = $instlData['apiInput'];
    $newInstlData = $apiInput['newInst'];
    unset($newInstlData[0]); // Remove the 0th element since its empty
    //Totoal installments
    $newInstlCntr = count($newInstlData);

    //populate all the processing errors into one common array
    $errorMsg = [];
    //UPDATING INSTALLMENT STARTS
    $updateInstlQuery = " UPDATE payment_instl SET ";

    $instlEditedCol = " instl_edited = ( CASE ";
    $instlEditedCase = '';
    //For install amount
    $amntCol = " ,sum = ( CASE ";
    $amntCase = '';
    //For install date
    $dateCol = " ,due_date = ( CASE ";
    $dateCase = '';
    $dueDaysCol = " ,due_days = (CASE ";
    $dueDaysCase = '';

    $instlIdList = [];
    $delDiscInstlList = [];

    foreach ($dbInstlData as $idx => $oldInstl) {

        $instlIdx = $oldInstl['instl_count'];
        $instlActionId = $newInstlData[$instlIdx]['edited']; //case 0 , no changes
        //For installement action
        switch ($instlActionId) {
            case 1: // installment is updated
                if ($oldInstl['status'] != 'paid') {
                    $instlIdList[] = $oldInstl['instl_id'];
                    $instlEditedCase .= " WHEN instl_id = " . $oldInstl['instl_id'] . " THEN " . db_sanitize($instlActionId);
                    //For install amount
                    // $amntCol = " sum CASE ";
                    //$amntCase = '';
                    if ($newInstlData[$instlIdx]['new_amnt'] >= 0 && $newInstlData[$instlIdx]['new_amnt'] != "") {
                        $amntCase .= " WHEN instl_id = " . $oldInstl['instl_id'] . " THEN " . db_sanitize($newInstlData[$instlIdx]['new_amnt']);
                    }
//                            if($amntCase != '' ){
//                                $amntCase.= " END ";
//                                $updateInstlQuery.= $amntCase;
//                            }
                    //For install date
//                            $dateCol = " due_date CASE ";
//                            $dateCase = '';
                    if ($newInstlData[$instlIdx]['new_date'] != '') {
                        $newDueDate = date("Y-m-d H:i:s", strtotime($newInstlData[$instlIdx]['new_date']));
                        $dateCase .= " WHEN instl_id = " . $oldInstl['instl_id'] . " THEN " . db_sanitize($newDueDate);
                    }
//                            if($dateCase != '' ){
//                                $dateCase.= " END ";
//                                if($amntCase != ''){
//                                    $updateInstlQuery.= " , ";
//                                }
//                                $updateInstlQuery.= $dateCase;
//                            }
                    //For install due-days
//                            $dueDaysCol = " due_days CASE ";
//                            $dueDaysCase = '';
                    if ($newInstlData[$instlIdx]['new_duedays'] != '') {
                        $dueDaysCase .= " WHEN instl_id = " . $oldInstl['instl_id'] . " THEN " . db_sanitize($newInstlData[$instlIdx]['new_duedays']);
                    }
                }
                break;
            case 2:// installment is deleted
                if ($oldInstl['status'] != 'paid') {
                    $instlIdList[] = $delDiscInstlList[] = $oldInstl['instl_id'];
                    $instlEditedCase .= " WHEN instl_id = " . $oldInstl['instl_id'] . " THEN " . db_sanitize($instlActionId);
                }
                break;
            case 3:// installment is discounted
                if ($oldInstl['status'] != 'paid') {
                    $instlIdList[] = $delDiscInstlList[] = $oldInstl['instl_id'];
                    $instlEditedCase .= " WHEN instl_id = " . $oldInstl['instl_id'] . " THEN " . db_sanitize($instlActionId);
                }
                break;
        }
    }
    //Only if there are any isntallments to be updated ,
    //enter the loop to process the data
    if ($instlEditedCase != '') {
        $updateInstlQuery .= $instlEditedCol . $instlEditedCase . " END )";
        if ($amntCase != '') {//Case of edited Amount of any installment
            $amntCase .= " END )";
            $updateInstlQuery .= $amntCol . $amntCase;
        }
        if ($dateCase != '') { //Case of edited DueDate of any installment
            $dateCase .= " END )";
            $updateInstlQuery .= $dateCol . $dateCase;
        }
        if ($dueDaysCase != '') {//Case of edited DueDays of any installment
            $dueDaysCase .= " END )";
            $updateInstlQuery .= $dueDaysCol . $dueDaysCase;
        }

        //Prepare the complete UPDATE query
        $updateAllInstlQuery = " WHERE instl_id IN (" . implode(",", $instlIdList) . ") ";
        $updateInstlQuery .= $updateAllInstlQuery;
        //execute the UPDATE query
        $updateStatus = db_update_exec($updateInstlQuery);

        if ($updateStatus === false) {// Stop the execution if existing installments update failed
            die(json_encode(array("status" => false, "message" => "Update failed!.")));
        } else {   //if  existing installments update was success
            //disable payment links for all DELETED/DISCOUNTED installments
            $disableLinkStatus = '';
            if (!empty($delDiscInstlList) && count($delDiscInstlList) > 0) {
                $disableLinkStatus = db_update_exec("UPDATE payment_link set status = 'disabled' WHERE instl_id IN (" . implode(",", $delDiscInstlList) . ") ");
                //If existing payment-links were not disabled
                if ($disableLinkStatus === false) {
                    $errorMsg[] = "Package Installments updated but Payment Links update failed!.";
                }
                //else payment-links were disabled successfully.
            }
            if ($updateStatus == 0) {
                $errorMsg[] = "There was no changes to existing installment data!";
            }
        }
    }

    // Existing Installments are not edited, but news ones are added.
    $updateTotalInstlQuery = db_update_exec(" UPDATE payment_instl SET updated_by = " . db_sanitize($_SESSION["user"]["user_id"]) . " , instl_total = " . db_sanitize($newInstlCntr) . " WHERE subs_id=" . db_sanitize($apiInput['subs_id']) . " AND pay_id=" . db_sanitize($apiInput['pay_id']) . " and user_id=" . db_sanitize($apiInput['user_id']));



    //Update of existing installments are processed.
    //If any new installments, process them
    $createStatus = creatInstallments($apiInput);

    if (is_array($createStatus) || !empty($errorMsg)) {

        $updateError = "There are few errors :" . implode("<br>\n", $errorMsg) . implode("<br>\n", $createStatus);
        die(json_encode(array("status" => false, "message" => $updateError)));
    }

    //@TODO send email function
    $mailStatus = true; //sendUpdatedInstallmentEmail();
    if ($mailStatus == true) {
        die(json_encode(array("status" => true, "message" => "Success : All installments updated. Email sent.")));
    } else {
        die(json_encode(array("status" => false, "message" => "Email was not sent. Success : All installments updated.")));
    }
}

/**
 *
 * @param type $newInstlData
 * @param type $errorMsg
 * @return boolean|string
 */
function creatInstallments($newInstlData, $errorMsg = []) {

    $createInstlQuery = " INSERT INTO payment_instl ( instl_edited ,pay_id, subs_id, user_id, instl_count, instl_total, sum, currency, due_days, due_date , pay_mode, status, updated_by) VALUES ";

    $instlEdited = 4; // Indicates this installment was added in edit screen
    $payId = $newInstlData['pay_id'];
    $subsId = $newInstlData['subs_id'];
    $userId = $newInstlData['user_id'];
    $payMode = "external";
    $status = "enabled";

    $newInstlArr = [];
    $newCnt = 0;
    $valueQuery = '';
    unset($newInstlData['newInst'][0]);
    $latestTotalInstallments = count($newInstlData['newInst']);
    //BATCH INSERT of new installments is implemented.
    foreach ($newInstlData['newInst'] as $idx => $instlData) {

        if ($instlData['added'] == true) {

            if ($instlData['added'] == true & $instlData['discounted'] == true) {
                $instlEdited = 5; // New installment but discounted
            }

            $newInstlArr[$newCnt]['instl_edited'] = db_sanitize($instlEdited);
            $newInstlArr[$newCnt]['pay_id'] = db_sanitize($payId);
            $newInstlArr[$newCnt]['subs_id'] = db_sanitize($subsId);
            $newInstlArr[$newCnt]['user_id'] = db_sanitize($userId);
            $newInstlArr[$newCnt]['instl_count'] = db_sanitize($idx);
            $newInstlArr[$newCnt]['instl_total'] = db_sanitize($latestTotalInstallments);
            $newInstlArr[$newCnt]['sum'] = db_sanitize($instlData['new_amnt']);
            $newInstlArr[$newCnt]['currency'] = db_sanitize('inr');
            $newInstlArr[$newCnt]['due_days'] = $instlData['new_duedays']?db_sanitize($instlData['new_duedays']):'null';
            $newInstlArr[$newCnt]['due_date'] = db_sanitize((new DateTime(($instlData['new_date'])))->format('Y-m-d H:i:s'));
            $newInstlArr[$newCnt]['pay_mode'] = db_sanitize($payMode);
            $newInstlArr[$newCnt]['status'] = db_sanitize($status);
            $newInstlArr[$newCnt]['updated_by'] = db_sanitize($_SESSION["user"]["user_id"]);

            // print_r($newInstlArr);die;
            if (!empty($valueQuery)) {
                $valueQuery .= " , ";
            }
            $valueQuery .= "(" . implode(",", array_values(array_values($newInstlArr[$newCnt]))) . " )";
            $newCnt++;
        }
    }

    if ($newCnt > 0) {
        //prepare the query
        $createInstlQuery .= $valueQuery;
//                echo $createInstlQuery;die;
        $createStatus = db_update_exec($createInstlQuery, true);

        if ($createStatus === false) {
            $errorMsg[] = "Creating new installment failed!.";
        } else {

            // All new installments are created succesfully ,
            // Further process for new isntallment's payment-link
            // Since BATCH-INSERT was done for new installments, we have to retrienve them back to create the payment links
            //Pick all the new installments which added  or added+discounted
            $newInstlIdArr = db_query("SELECT instl_id, instl_count FROM payment_instl WHERE pay_id =" . db_sanitize($payId) . " AND subs_id=" . db_sanitize($subsId) . " AND user_id=" . db_sanitize($userId) . " AND instl_edited IN ( 4,5) "); //
            //BATCH-INSERT of payment links
            //Create payment links section
            $payLinkQuery = " INSERT INTO payment_link ( web_id ,pay_id, subs_id, user_id, instl_id, create_date, status, updated_by) VALUES ";
            $payLinkArr = [];
            $payLinkStatus = "enabled";
            $lnkCnt = 0;
            $payLinkValQuery = '';
            foreach ($newInstlIdArr as $idx => $instl) {
                $webId = md5(strval($payId) . strval($instl['instl_id']) . strval($userId) . strval($subsId) . strval($instl['instl_count']) . strval(date('Y-m-d H:i:s')));
                $payLinkArr[$lnkCnt]['web_id'] = db_sanitize($webId);
                $payLinkArr[$lnkCnt]['pay_id'] = db_sanitize($payId);
                $payLinkArr[$lnkCnt]['subs_id'] = db_sanitize($subsId);
                $payLinkArr[$lnkCnt]['user_id'] = db_sanitize($userId);
                $payLinkArr[$lnkCnt]['instl_id'] = db_sanitize($instl['instl_id']);
                $payLinkArr[$lnkCnt]['create_date'] = db_sanitize(strval(date('Y-m-d H:i:s')));
                $payLinkArr[$lnkCnt]['status'] = db_sanitize($payLinkStatus);
                $payLinkArr[$lnkCnt]['updated_by'] = db_sanitize($_SESSION["user"]["user_id"]);

                if (!empty($payLinkValQuery)) {
                    $payLinkValQuery .= " , ";
                }
                $payLinkValQuery .= "(" . implode(",", array_values(array_values($payLinkArr[$lnkCnt]))) . " )";
                $lnkCnt++;
            }

            if ($lnkCnt > 0) {
                //prepare the query
                $payLinkQuery .= $payLinkValQuery;

                $createPayLinkStatus = db_update_exec($payLinkQuery, true);

                if ($createPayLinkStatus === false) {
                    $errorMsg[] = "New installments are created but payment links creation failed!";
                }
            }
        }
    }

    if (count($errorMsg) > 0) { // If any error , return the error
        return $errorMsg;
    }
    return true;
}

/**
 *
 * @param type $packageDataArr
 * @return boolean
 */
function sendUpdatedInstallmentEmail($packageDataArr = []) {

    return true;
}
