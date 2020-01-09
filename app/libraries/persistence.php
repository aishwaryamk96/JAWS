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
    if (!defined("JAWS")) {
      	header('Location: ../index.php');
      	die();
    }


    // Array structure for entity requiring persistence
    // ["layer"] => name of persistence layer. can also be blank, in which case no persistence is required.
    // ["type"] => entity type. courses/user/etc...
    // ["id"] => entity id to be converted
    // ["current"] => native/external - is set by the function for read-only use.
    // Ideal layer name nomenclature example = layerpl
    // Ideal entity type nomeclature example = column.entitytype.column (uid.user.email)

    // Errors for conversion failures are stored in $GLOBALS["jaws_persistence"]["error"]
    $GLOBALS["jaws_persistence"]["error"] = "";

    // Checks if an entity is persisted on a layer
    function is_persistent($entity, $origin = "external") {

    	$layer = db_sanitize($entity["layer"]);
    	$type = db_sanitize($entity["type"]);
    	$id = db_sanitize($entity["id"]);

    	$res;
        if (strcmp($origin, "external") == 0) $res = db_query("SELECT COUNT(map_id) FROM system_persistence_map WHERE layer=".$layer." AND entity_type=".$type." AND ext_id=".$id.";");
    	else $res = db_query("SELECT COUNT(map_id) FROM system_persistence_map WHERE layer=".$layer." AND entity_type=".$type." AND native_id=".$id.";");

        if (isset($res[0]["COUNT(map_id)"])) {
    		if (intval($res[0]["COUNT(map_id)"]) > 0) return true;
    		else return false;
    	}
    	else return false;

    }

    // Converts external entity to native entity
    function get_native_id($entity) {

    	if (isset($entity["layer"])) {
    		if (strlen($entity["layer"]) > 0) {

    			$layer = db_sanitize($entity["layer"]);
    			$type = db_sanitize($entity["type"]);
    			$ext_id = db_sanitize($entity["id"]);

    			$res=  db_query("SELECT native_id FROM system_persistence_map WHERE layer=".$layer." AND entity_type=".$type." AND ext_id=".$ext_id." LIMIT 1;");
    			if (isset($res[0]["native_id"])) $entity["id"] = $res[0]["native_id"];
    			else $GLOBALS["jaws_persistence"]["error"] .= nl2br("\nWarning: Could not find persistent NATIVE equivalent of ".$ext_id." (".$type.") on '".$layer."' layer.");

    		}
    	}

    	$entity["state"] = "native";
    	return $entity;
    }

     // Converts native entity to external entity
    function get_external_id($entity) {

    	if (isset($entity["layer"])) {
    		if (strlen($entity["layer"]) > 0) {

    			$layer = db_sanitize($entity["layer"]);
    			$type = db_sanitize($entity["type"]);
    			$native_id = db_sanitize($entity["id"]);

    			$res = db_query("SELECT ext_id FROM system_persistence_map WHERE layer=".$layer." AND entity_type=".$type." AND native_id=".$native_id." LIMIT 1;");
    			if (isset($res[0]["ext_id"])) $entity["id"] = $res[0]["ext_id"];
    			else $GLOBALS["jaws_persistence"]["error"] .= nl2br("\nWarning: Could not find persistent EXTERNAL equivalent of ".$native_id." (".$type.") on '".$layer."' layer.");

    		}
    	}

    	$entity["state"] = "external";
    	return $entity;
    }


    // Converts a parameter list array to native entities
    function get_native_arr($arr) {

    	$GLOBALS["jaws_persistence"]["error"] = "";
    	$arr_native = array();

    	$obj = db_prepare("SELECT native_id FROM system_persistence_map WHERE layer=:layer AND entity_type=:type AND ext_id=:ext_id LIMIT 1;");

    	$layer = "";
    	$type = "";
    	$ext_id = "";

    	$obj->bindParam(':layer', $layer);
		$obj->bindParam(':type', $type);
		$obj->bindParam(':ext_id', $ext_id);

    	foreach($arr as $entity) {

    		$layer = $entity["layer"];
    		$type = $entity["type"];
    		$ext_id = $entity["id"];

    		$res = db_query_prepared($obj);

    		if (isset($res[0]["native_id"])) $entity["id"] = $res[0]["native_id"];
    		else $GLOBALS["jaws_persistence"]["error"] .= nl2br("\nWarning: Could not find persistent NATIVE equivalent of ".$ext_id." (".$type.") on '".$layer."' layer.");

    		array_push($arr_native, $entity);
    		$entity["state"] = "native";

    	}

    	return $arr_native;

    }

    // Converts a parameter list array to external entities
    function get_external_arr($arr) {

    	$GLOBALS["jaws_persistence"]["error"] = "";
    	$arr_ext = array();

    	$obj = db_prepare("SELECT ext_id FROM system_persistence_map WHERE layer=:layer AND entity_type=:type AND native_id=:native_id LIMIT 1;");

    	$layer = "";
    	$type = "";
    	$native_id = "";

    	$obj->bindParam(':layer', $layer);
		$obj->bindParam(':type', $type);
		$obj->bindParam(':native_id', $native_id);

    	foreach($arr as $entity) {

    		$layer = $entity["layer"];
    		$type = $entity["type"];
    		$native_id = $entity["id"];

    		$res = db_query_prepared($obj);

    		if (isset($res[0]["ext_id"])) $entity["id"] = $res[0]["ext_id"];
    		else $GLOBALS["jaws_persistence"]["error"] .= nl2br("\nWarning: Could not find persistent EXTERNAL equivalent of ".$native_id." (".$type.") on '".$layer."' layer.");

    		array_push($arr_ext, $entity);
    		$entity["state"] = "external";

    	}

    	return $arr_ext;

    }

    // This will persist an external entity
    function persist($layer, $type, $native_id, $ext_id, $check_persistence = true) {

    	$is_persistent = false;
    	if ($check_persistence) {

    		$entity["layer"] = $layer;
    		$entity["type"] = $type;
    		$entity["id"] = $ext_id;

    		$is_persistent = is_persistent($entity);

    	}

    	if (!$is_persistent) {

    		$layer = db_sanitize($layer);
    		$type = db_sanitize($type);
    		$native_id = db_sanitize($native_id);
    		$ext_id = db_sanitize($ext_id);

    		db_exec("INSERT INTO system_persistence_map (layer, entity_type, native_id, ext_id) VALUES (".$layer.",".$type.",".$native_id.",".$ext_id.");");

    	}

    }

    // Type specific functions to get persisted data

    function get_native_course_id($ext_course_id, $layer) { return get_native_id(array("layer" => $layer, "type" => "course", "id" => $ext_course_id)); }
    function get_ext_course_id($native_course_id, $layer) { return get_external_id(array("layer" => $layer, "type" => "course", "id" => $native_course_id)); }

    function get_native_user_id($ext_user_id, $layer) { return get_native_id(array("layer" => $layer, "type" => "user", "id" => $ext_user_id)); }
    function get_ext_user_id($native_user_id, $layer) { return get_external_id(array("layer" => $layer, "type" => "user", "id" => $native_user_id)); }


?>