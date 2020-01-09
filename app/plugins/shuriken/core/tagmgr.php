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
		header('Location: https://www.jigsawacademy.com/index.php');
		die();
	}

	// Shuriken Tag Manager
    	// This contains functions to Implement the shuriken snippet injector and tag manager.
	// -------------------

	// Hooks in
	hook('shuriken_event_output_pageload', 'shuriken_event_output_pageload_tagmgr');

	//  Event (output) : pageload, Handler for tagmgr output
	// This function renders the tag containers for a URL on pageload event.
	function shuriken_event_output_pageload_tagmgr($kue, $i) {
		shuriken_tagmgr_output($kue['url'], true);
	}

	// This function will create a new container for the specified URL string.	
	// NOTE : Same container with same URLs are allowed for A-B testing.
	function shuriken_tagmgr_container_create($url_str, $name='', $category='') {

		// Prep
		$url_template_sanitized = url_template_sanitize(url_template_from_string($url_str));
		$name = (strlen($name) > 0) ? db_sanitize($name) : 'NULL';
		$category = (strlen($category) > 0) ? db_sanitize($category) : 'NULL';

		// Check errors
		if (!$url_template_sanitized['status']) return false;

		// Create container
		db_exec('INSERT INTO shuriken_tagmgr_container(name, category, url_template) VALUES ('.$name.', '.$category.', 0);');
		$container_id = db_get_last_insert_id();

		// Create URL template
		$template_id = url_template_create($url_template_sanitized, "shuriken.container", $container_id);

		// Update container with URL template ID
		db_exec('UPDATE shuriken_tagmgr_container SET url_template='.$template_id.' WHERE container_id='.$container_id.';');

		// Done
		return $container_id;
	}

	// This function will create a tag from code or from file.
	// NOTE : New file will not be written ! File must exist with prexistant code.
	// NOTE : When using file-based storage, type will be determined by file extension.
	// NOTE : When using DB based storage, type will be determined by presence of HTML Tags.
	// NOTE : Automatic type can be overrriden. This is handy for 'dynamic' type.
	// NOTE : When type=html, selector_type and selector MUST be specified for injection.
	function shuriken_tagmgr_tag_create($content, $selector_type='', $selector='', $name='', $parent_container=null, $is_file=false, $type=null) {
		
		// Prep
		$name = (strlen($name) > 0) ? db_sanitize($name) : 'NULL';
		$file = $is_file ? db_sanitize($content) : 'NULL';
		$code = (!$is_file) ? db_sanitize($content) : 'NULL';

		// IMPORTANT - Use escaping for quotes inside the code if type is HTML - required for injector to be able to parse
		// IMPORTANT - check if db_sanitize escapes quotes !
		// IMPORTANT - check what characters will be escaped by db_sanitize - will JS and HTML require different sanitizing ?

		$type = db_sanitize($is_file ? strtolower(pathinfo($content, PATHINFO_EXTENSION)) : ($type ?? shuriken_tagmgr_tag_type($content)));
		$parent_container = isset($parent_container) ? $parent_container : 'NULL';

		if (strlen($selector) == 0) {
			$selector = "head";
			$selector_type = "TagName";
		}
		$selector_type = db_sanitize((strlen($selector_type) > 0) ? $selector_type : 'Id');
		$selector = db_sanitize($selector);

		// Insert
		db_exec('INSERT INTO shuriken_tagmgr_tag(name, type, file, code, selector_type, selector, parent_container) VALUES ('.$name.', '.$type.', '.$file.', '.$code.', '.$selector_type.', '.$selector.', '.$parent_container.');');

		// Done
		return db_get_last_insert_id();
	}

	// This function determins the type of a tag from the code supplied.
	// It works by detecting presence of HTML tags inside the code.
	function shuriken_tagmgr_tag_type($code) {
		return ($code != strip_tags($code)) ? 'html' : 'js';
	}

	// This function will add one tag to a container
	// If the tag is to be newly assoced, if it will use the given sequence if it is not null, else use the next higher sequence for the container.
	// If this tag is already assoced with the container, it will update it's status and/or sequence.
	// WARNING ! It is recommended to NOT use this function to update sequencing. Use the Bulk Assoc function instead.
	function shuriken_tagmgr_assoc($tag_id, $container_id, $status='enabled', $sequence=null) {	

		// Prep
		$status = db_sanitize($status);	

		// Check
		$res = db_query("SELECT assoc_id FROM shuriken_tagmgr WHERE container_id=".$container_id." AND tag_id=".$tag_id." LIMIT 1;");

		// Update status and/or sequence
		if (isset($res[0])) {

			// Update sequence ?
			$sequence_query = '';
			if (isset($sequence)) $sequence_query = ', sequence='.$sequence;

			// Update
			db_exec('UPDATE shuriken_tagmgr SET status='.$status.$sequencequery.' WHERE assoc_id='.$res[0]['assoc_id'].';');
			return true;
		}

		// Create new
		else {		
			// Sequence
			if (!isset($sequence)) {
				$sequence = 1;
				$res = db_query('SELECT MAX(sequence) AS max_sequence FROM shuriken_tagmgr WHERE container_id='.$container_id.';');			
				if (isset($res[0]['max_sequence'])) $sequence += intval($res[0]['max_sequence']);
			}

			// Insert
			db_exec('INSERT INTO shuriken_tagmgr(container_id, tag_id, sequence, status) VALUES ('.$container_id.', '.$tag_id.', '.$sequence.', '.$status.');');
			return db_get_last_insert_id();
		}
	}

	// This function will associate multiple tags to multiple containers in the given order.
	// WARNING ! This function will clear existing associations for all containers in the array !
	// WARNING ! This function will REPLACE ALL ASSOCIATION for the containers given !
	// WARNING ! If the function fails to new assoc for a container, all old assocs will be lost for that container !
	// Structure of array = [{id=xx, optional status='status'}, {..}]
	function shuriken_tagmgr_assoc_bulk_anew($container_id, $tag_arr) {	

		// Clear the assocs for the container
		db_exec('DELETE FROM shuriken_tagmgr WHERE container_id='.$container_id.';');

		// Build query
		$query = 'INSERT INTO shuriken_tagmgr(container_id, tag_id, sequence, status) VALUES (';

		for($sequence = 1; $sequence <= count($tag_arr); $sequence++) {
			$status = db_sanitize(isset($tag_arr[$sequence]['status']) ? $tag_arr[$sequence]['status'] : 'enabled');
			$query .= (($sequence == 1) ? '' : '), (').$container_id.', '.$tag_arr[$sequence]['tag_id'].', '.$sequence.', '.$status;
		}

		$query .= ');';

		// Insert
		unset($GLOBALS["jaws_db"]["error"]);
		db_exec($query);

		// Error Check
		if (isset($GLOBALS["jaws_db"]["error"])) {
			activity_create("critical", "shuriken.tagmgr.assoc.bulk.fail", "fail", "",  "", "", "", $query."|".$GLOBALS["jaws_db"]["error"], "logged");
			return false;
		}
		else return true;
		
	}

	// This function will parse an actual URL and output all the tags (in sequence) assoced with the matched containers
	// YET TO IMPLEMENT : Container Sequencing Based on URL Template Match Heirarchy
	function shuriken_tagmgr_output($url, $is_templated = false) {
		
		// Get Matched templates
		$matched_templates_arr = url_template_match($url, 'shuriken.container', $is_templated);

		// Check if any templates matched
		if (count($matched_templates_arr) == 0) return;

		// Build Container ID Arr
		$container_id_arr = [];
		foreach($matched_templates_arr as $matched_template) array_push($container_id_arr, $matched_template['context_id']);

		// Build Tag Retreival Query
		$query = "SELECT 
				tag.name AS 'name',
				tag.type AS 'type', 
				tag.file AS 'file', 
				tag.code AS 'code',
				tag.selector_type AS 'selector_type',
				tag.selector AS 'selector'
			FROM 
				shuriken_tagmgr AS assoc 
			INNER JOIN 
				shuriken_tagmgr_tag AS tag 
				ON tag.tag_id = assoc.tag_id 
			INNER JOIN 
				shuriken_tagmgr_container AS container 
				ON container.container_id = assoc.container_id 
			WHERE 
				(assoc.status = 'enabled') AND 
				(tag.status = 'enabled') AND 
				(container.status = 'enabled') AND 
				(assoc.container_id IN (".implode(', ', $container_id_arr).")) 
			GROUP BY
				tag.tag_id 
			ORDER BY 
				assoc.container_id DESC, 
				assoc.sequence ASC;";

		// Get Tags
		$tag_arr = db_query($query);

		// Check if any tags were retreived
		if (count($tag_arr) == 0) return;

		// Output JS tags, prep HTML tags
		$tag_html_arr = [];
		$tag_html_tagname_head_str = '';
		$tag_html_tagname_body_str = '';

		foreach($tag_arr as $tag) {

			// Code
			$code='';
			if (isset($tag['file'])) {
				if (file_exists($GLOBALS['shuriken']['path'].'/tags/'.$tag['file'])) {
					$content = file_get_contents($GLOBALS['shuriken']['path'].'/tags/'.$tag['file']);

					if ($content === false) $code = $tag['code'] ?? '';
					else $code = $content;
				}
				else $code = $tag['code'] ?? '';
			}
			else $code = $tag['code'] ?? '';

			// For JS, output right away
			if ($tag['type'] == 'js') echo $code;

			// For dynamic tags, fire handler
			elseif ($tag['type'] == 'dyn') {
				$data = $is_templated ? $url : url_template_from_string($url);
				handle_strict('shuriken_tagmgr_tag_output_'.$code, $data);
			}

			// For HTML, queue for injection
			elseif ($tag['type'] == 'html') {

				// Empty Code check
				if (strlen($code) == 0) continue;

				// Prep
				$prepped_tag = [
					'selector' => $tag['selector'],
					'selector_type' => $tag['selector_type'],
					'code' => $code
				];

				// Place in proper array
				if ($tag['selector_type'] == 'TagName') {
					if ($tag['selector'] == 'head') $tag_html_tagname_head_str .= $code;
					elseif ($tag['selector'] == 'body') $tag_html_tagname_body_str .= $code;
					else array_push($tag_html_arr, $prepped_tag);
				}
				else array_push($tag_html_arr, $prepped_tag);
			}
		}

		// Output HTML tag injections - for TagName 'head'
		if (strlen($tag_html_tagname_head_str) > 0) shuriken_js_inject($tag_html_tagname_head_str, 'head', 'TagName');

		// Output DOM Ready
		shuriken_js_domready_open();

		// Output HTML tag injections - for TagName 'body'
		if (strlen($tag_html_tagname_body_str) > 0) shuriken_js_inject($tag_html_tagname_body_str, 'body', 'TagName');

		// Output rest of the tag injections
		foreach($tag_html_arr as $tag) shuriken_js_inject($tag['code'], $tag['selector'], $tag['selector_type']);

		// Done - DOMReady Close
		shuriken_js_compound_close();

	}

	function shuriken_tagmgr_demo($url) {
		
	}

	/** Fetch container with a specific id **/
	function shuriken_tagmgr_container_get($container_id) {
		$container_arr = db_query("SELECT * FROM shuriken_tagmgr_container as container 
						   INNER JOIN system_url_template as template 
						   ON container.url_template = template.template_id 
						    where container.container_id = '".$container_id."' ");

		$associated_tags = db_query("SELECT tagmgr.assoc_id,tagmgr.container_id,tagmgr.tag_id, tagmgr.sequence, tagmgr.status as assoc_status , tag.name, tag.type, tag.code, tag.selector_type, tag.selector, tag.parent_container, tag.status as tag_status FROM shuriken_tagmgr as tagmgr INNER JOIN shuriken_tagmgr_tag as tag on tagmgr.tag_id = tag.tag_id where tagmgr.container_id = '".$container_id."' ORDER BY tagmgr.sequence ASC ");

		$container_data = array_merge($container_arr,$associated_tags);
		return $container_data;
	}

	/** Fetch all the container **/
	function shuriken_tagmgr_container_get_all() {
		return db_query("SELECT * 
					FROM 
						shuriken_tagmgr_container as container 	   
					INNER JOIN 
						system_url_template as template 	   
					ON 
						container.url_template = template.template_id 
					ORDER BY 
						container.category ASC,
						container.name ASC,
						container.container_id ASC;");
	}

	/** Fetch all the tags **/
	function shuriken_tagmgr_tag_get_all() {
		$tag_arr = db_query("SELECT * FROM shuriken_tagmgr_tag;");
		return $tag_arr;
	}

	/** Fetch all the tags which are local to any container and which are global **/
	function shuriken_tagmgr_tag_get_all_by_parent($parent_id) {
		$tag_arr = db_query("SELECT * FROM shuriken_tagmgr_tag where (parent_container IS NULL) OR (parent_container = ".$parent_id.");");
		return $tag_arr;
	}

	// fetch one particular Tag
	function shuriken_tagmgr_tag_get($tag_id) {
		$res = db_query("SELECT * FROM shuriken_tagmgr_tag where tag_id = '".$tag_id."' LIMIT 1;");
		return $res[0] ?? false;
	}

	/** Edit the container **/
	function shuriken_tagmgr_container_edit($data) 
	{
		// Prep
		$url_template_sanitized = url_template_sanitize(url_template_from_string($data['edit_container_url']));
		$name = (strlen($data['edit_container_name']) > 0) ? db_sanitize($data['edit_container_name']) : 'NULL';
		$category = (strlen($data['edit_container_category']) > 0) ? db_sanitize($data['edit_container_category']) : 'NULL';
		$edit_status = $data['edit_status'];

		// Check errors
		if (!$url_template_sanitized['status']) return false;

		// Create container
		db_exec('UPDATE shuriken_tagmgr_container SET name = '.$name.' , category = '.$category.', status = "'.$edit_status.'"  WHERE  container_id = '.$data['container_id'].' ');

		// Create URL template
		$template = url_template_update($url_template_sanitized, $data['url_template']);

		// Done
		return true;
	}

	/** remove tag associated with any container **/
	function shuriken_tagmgr_assoc_remove($container_id, $tag_id)
	{
		db_exec('DELETE FROM shuriken_tagmgr WHERE container_id='.$container_id.' and tag_id='.$tag_id.' ;');
		return true;
	}

	/** update tag **/
	function shuriken_tagmgr_tag_edit($tag_id, $content, $selector_type='', $selector='', $name='',  $status, $parent_container=null, $is_file=false)
	{
		// Prep
		$name = (strlen($name) > 0) ? db_sanitize($name) : 'NULL';
		$file = $is_file ? db_sanitize($content) : 'NULL';
		$code = (!$is_file) ? db_sanitize($content) : 'NULL';
		$type = db_sanitize($is_file ? strtolower(pathinfo($content, PATHINFO_EXTENSION)) : shuriken_tagmgr_tag_type($content));
		$parent_container = isset($parent_container) ? $parent_container : 'NULL';

		if (strlen($selector) == 0) {
			$selector = "head";
			$selector_type = "TagName";
		}
		$selector_type = db_sanitize((strlen($selector_type) > 0) ? $selector_type : 'Id');
		$selector = db_sanitize($selector);

		// Insert
		db_exec("UPDATE shuriken_tagmgr_tag SET name = ".$name." , type = ".$type.", file = ".$file.", code = ".$code.", selector_type = ".$selector_type.", selector = ".$selector.", status = '".$status."', parent_container = ".$parent_container." WHERE tag_id = ".$tag_id." ");

		// Done
		return true;
	}



?>