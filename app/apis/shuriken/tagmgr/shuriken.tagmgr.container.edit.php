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
	
	// Prevent exclusive access
	if (!defined("JAWS")) {
		header('Location: https://www.jigsawacademy.com');
		die();
	}

	// Check Auth
	if (!auth_api("shuriken")) die("You do not have the required priviledges to use this feature.");

	// Init
	load_plugin("shuriken");

	// update the container
	$update_container = shuriken_tagmgr_container_edit($_POST['form_ele']); 
	$tag_id_array = array();
	
	if(isset($_POST['tag_ids'])) {
		$i =1;
		foreach($_POST['tag_ids'] as $value)
		 {
			foreach($value as $key=>$val)
			{
				$tag_id_array[$i]['status'] = $val;
				$tag_id_array[$i]['tag_id'] = $key;
				 $i++;
			}
		 }
	}
	
	// update associated tag 
	$associate_tags = shuriken_tagmgr_assoc_bulk_anew($_POST['form_ele']['container_id'], $tag_id_array); 
	echo true;
?>