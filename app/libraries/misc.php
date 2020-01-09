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

	// This library contains functions that ease miscelleneous tasks
    // -----------

    // This function sends download header to the browser for file downloads
    function header_download($content_type, $filename) {
		header('Content-Type: '.$content_type);
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');

		// If serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
    }

    // This function returns the content according to the context_type and context_id; context_id can be blank
	function content_get($context_type, $context_id = 0) {
		$query = "SELECT content FROM system_content WHERE context_type=".db_sanitize($context_type);
      	$query .= (($context_id == 0) ? ";" : " AND context_id=".$context_id.";");
      	$res = db_query($query);

      	if (!isset($res[0])) return false;
      	else return $res[0]["content"];
    }

    // Saves the content in the database. If the content for the context_type is already present, updates it.
    function content_set($context_type, $context_id, $content){

		$query = "SELECT * FROM system_content WHERE context_type=".db_sanitize($context_type);
		if ($context_id != 0) $query .= " AND context_id=".$context_id;
		$res_content = db_query($query);

		if (!isset($res_content[0])) {
			$query = "INSERT INTO system_content (context_type, ";
			$query .= (($context_id == 0) ? "content) VALUES (".db_sanitize($context_type).", ".db_sanitize($content).");" : "context_id, content) VALUES (".db_sanitize($context_type).", ".$context_id.", ".db_sanitize($content).");");
		}

		else {
			$query = "UPDATE system_content SET context_type=".db_sanitize($context_type).", ";
			$query .= (($context_id != 0) ? "context_id=".$context_id.", " : "");
			$query .= "content=".db_sanitize($content)." WHERE content_id=".$res_content[0]["content_id"];
		}

		db_exec($query);

    }

    // Includes all files in a directory using require_once
    // WARNING ! Non-PHP code will be included and executed as well !
    // WARNING ! Having non-PHP files inside the given directory will cause JAWS script to end.
    function require_dir_once($dir) {
		$file_list = array_diff(scandir($dir), array('..', '.'));
		foreach($file_list as $file_name) require_once $dir."/".$file_name;
    }


?>