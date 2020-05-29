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

	load_module ("course");
	load_library("persistence");
	 
	/*
	 * A bundle can be of 3 types:
	 * 1. Specialization: These are pre-defined bundles of courses which do not have any date of expiry.
	 * 2. Offers: Offers have a date of expiry.
	 * 3. Combo: They do not seem to have any date of expiry.
	*/

	if (!isset($_POST["bundle"]))
	{
		echo json_encode(false);
		exit();
	}

	// Get the type of the bundle
	$type = $_POST["bundle"]["type"];
	$bundle = $_POST["bundle"];
	
	$bundle_jaws['name'] = $bundle['name'];
	$bundle_jaws['code'] = $bundle['code'];
	$bundle_jaws['start_date'] = db_sanitize($bundle['batch_start_date']);
	$bundle_jaws['end_date'] = db_sanitize($bundle['batch_end_date']);
	$bundle_jaws['price_inr'] = db_sanitize($bundle['price']);
	$bundle_jaws['price_usd'] = db_sanitize($bundle['price_usd']);
	//$bundle_jaws['bundle_type'] = $type;
	$bundle_jaws['visible']='';
	$bundle_jaws['meta']=db_sanitize(json_encode($bundle_jaws));
	$bundal_all["batches"] = $bundle_jaws;
	activity_debug_start();
	$return = bootcamp_add_batch_batcave($bundle['bundle_id'], $bundal_all["batches"]);
	if($return==1){
		$data['message']= "Batch Details added sucessfully";
		echo json_encode($data);
	}else{
		$data['message']= "Something went wrong.";
		echo json_encode($data);
	}

	// echo json_encode(true);

?>
