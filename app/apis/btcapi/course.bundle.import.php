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

    JIGSAW ACADEMY WORKFLOW SYSTEM v1
    ---------------------------------
*/


  	// Prevent exclusive access
  	

  	register_shutdown_function(function() {
  		if (!empty($errors = error_get_last())) {
  			log_activity("course.bundle.import.error", $errors);
  		}
  	});

	// if (!auth_api("course.bundle.import"))
	// 	die ("You do not have the required priviledges to use this feature.");

	// Auth Check - Expecting Session Only !
	if ((!auth_session_is_logged()) || (!auth_session_is_allowed("batcave"))) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	if (!isset($_POST["bundle"]))
	{
		echo json_encode(false);
		exit();
	}
	$bundle = $_POST["bundle"];
	$method = $_POST["method"]? $_POST["method"]:"ADD";//IT SHOULD BE ADD OR UPDATE by default add

	$bundle_jaws = validateBatchDetails($bundle,$method);
	$bundle_jaws['meta']=db_sanitize(json_encode($bundle_jaws));

	activity_debug_start();

	$return = bootcamp_add_batch_batcave($bundle['bundle_id'],$bundle_jaws,$method);

	if($return != false) {
		if($method == "ADD")
			die(json_encode(["message" => "Batch Details added successfully."]));
		else
			die(json_encode(["message" => "Batch Details updated successfully."]));
	}
	die(json_encode(["message"=>"Something went wrong."]));


	//functions
	function validateBatchDetails($batch_info,$method){
		$batch_data =array();
		if(($method == "ADD")||($method == "UPDATE")) {
			if($method =="UPDATE")
				$batch_data["id"] =checkIspresent("Bathch Id",$batch_info["bacth_id"]);
			$batch_data['name'] = checkIspresent('Name',$batch_info['name']);
			$batch_data['code'] = checkIspresent('Code',$batch_info['code']);
			$batch_data['start_date'] = checkIspresent('Start Date',$batch_info['batch_start_date']);
			$batch_data['end_date'] = checkIspresent('End Date',$batch_info['batch_end_date']);
			$batch_data['price_inr'] = checkIspresent('Price',$batch_info['price']);
			$batch_data['price_usd'] = checkIspresent('Price in Usd',$batch_info['price_usd']);
			$batch_data['visible'] = $batch_info['visible']?$batch_info['visible']:1;// by default set it to 1
		}else{
			die(json_encode(["message"=>"invalid method."]));
		}
		return $batch_data;
	}
	function checkIspresent($key,$value){
		if(isset($value))
			return $value;
		else
			die(json_encode(["message"=>"$key is required."]));

	}

?>
