<?php

/* 
           8 8888       .8. `8.`888b                 ,8' d888888o.   
           8 8888      .888. `8.`888b               ,8'.`8888:' `88. 
           8 8888     :88888. `8.`888b             ,8' 8.`8888.   Y8 
           8 8888    . `88888. `8.`888b     .b    ,8'  `8.`8888.     
           8 8888   .8. `88888. `8.`888b    88b  ,8'    `8.`8888.    
           8 8888  .8`8. `88888. `8.`888b .`888b,8'      `8.`8888.   
88.        8 8888 .8' `8. `88888. `8.`888b8.`8888'        `8.`8888.  
`88.       8 888'.8'   `8. `88888. `8.`888`8.`88'     8b   `8.`8888. 
  `88o.    8 88'.888888888. `88888. `8.`8' `8,`'      `8b.  ;8.`8888 
    `Y888888 ' .8'       `8. `88888. `8.`   `8'        `Y8888P ,88P' 

      JIGSAW ACADEMY WORKFLOW SYSTEM v2       
      ---------------------------------
*/
      $data= file_get_contents("php://input");
      $refer_data = json_decode($data,true);
      
  // Prevent exclusive access
  if (!defined("JAWS")) {
    header('Location: https://www.jigsawacademy.com');
    die();
  }

  load_library("email");

  // Init Session
  auth_session_init();

  if (!auth_session_is_logged() || !auth_session_is_allowed("jlc.referral"))
    die ("You do not have required priviledges to use this feature.");

  load_module("user");
  // Set the referral record to completed
  $res_act = db_query("SELECT * FROM system_activity WHERE act_type='jlc.referral' AND activity='referral' AND context_type=".db_sanitize($refer_data['src'])." AND context_id=".$refer_data["referrer_id"].";");

  $referral_user;

  foreach ($res_act as $activity)
  {
    $content = json_decode($activity["content"], true);
    $content_new = array();
    $write_back = false;
    foreach ($content["r"] as $referral)
    {
      $ref = user_get_by_id($refer_data["referred_id"]);
      if ($ref["email"] == $referral["e"])
      {
        $referral["x"] = "2";
        $write_back = true;
        $referral_user = $ref;
      }

      $content_new[] = $referral;
    }
    //print_r($content_new); die();
    if ($write_back)
      db_exec("UPDATE system_activity SET content=".db_sanitize(json_encode(array("r" => $content_new, "n" => $content["n"])))." WHERE act_id=".$activity["act_id"]);
  }
  // db query to update status when voucher sent to user //

  // send mail of voucher confirmation

  send_email("referral.voucher.notify", array("to" => (($GLOBALS["jaws_exec_live"]) ? $refer_data["email"] : "nikita@jigsawacademy.com"), "cc"=> "jagruti@jigsawacademy.com", "subject" => "Confirmation on your Amazon Voucher"), array('referrer_name' => $refer_data['name']));

  exit();