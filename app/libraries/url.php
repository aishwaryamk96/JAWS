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

	// This library consists of function neededs to map actual URLs to templated URLs
	// ---------------------

	// Templating Wildcard Rules - 
	// Rule {} - In URL : Segment MUST be present with ANY value, In query string : param MUST be present with ANY value.
	// Rule {:} - In URL : Segment MIGHT be present with ANY value, In query string : not applicable.
	// Rule {!} - In URL : Segment CANNOT be present, In query string : param CANNOT be present.
	// Rule else/exact - In URL : not applicable, In query string : param MUST be present with EXACT value.
	// ---------------------

	function url_template_from_string($url) {
	
		$url_template;
	
		// Prep
		$url = str_replace('\\', '/', $url);
		
		// Protocol
		$tarr = explode('://', $url);
		if(count($tarr)>1) {
			if (strlen($tarr[0]) > 0) $url_template['protocol'] = strtolower($tarr[0]);
			$url = $tarr[1];
		}
	
		// Prep
		$url = str_replace('//', '/', $url); // Upto 2 consecutive slashes
		$url = str_replace('//', '/', $url); // Progressive upto 4 consecutive slashes, J.I.C.
	
		// Query String and Anchor
		$tarr = explode('?', $url);
		$query;
		if(count($tarr)>1) {
			$url = $tarr[0];
			if (strlen($tarr[1]) > 0) $query = $tarr[1];
	
			$tarr = explode('#', $url);
			if(count($tarr)>1) {
				$url = $tarr[0];
				if (strlen($tarr[1]) > 0) $url_template['fragment'] = $tarr[1];
			}
			else {
				if (isset($query)) {
					$tarr = explode('#', $query);
					if(count($tarr)>1) {
						$query = $tarr[0];
						if (strlen($tarr[1]) > 0) $url_template['fragment'] = $tarr[1];
					}
				}
			}
		} 
		else  {
			$tarr = explode('#', $url);
			if(count($tarr)>1) {
				$url = $tarr[0];
				if (strlen($tarr[1]) > 0) $url_template['fragment'] = $tarr[1];
			}
		}
	
		// Query string params
		$params;
		if (isset($query)) {
			$tarr = explode('&', $query);
			foreach($tarr as $param) {
				$tarr2 = explode('=', $param);
				if (count($tarr2) > 1) $params[$tarr2[0]] = $tarr2[1];
				else $params[$tarr2[0]] = '';
			}
	
			$url_template['params'] = $params;
		}
	
		// Prep
		$url = trim($url, '/');
		$segment_arr = explode('/', $url);
		$tdomain_arr = explode('.', array_shift($segment_arr));
		$tc = count($tdomain_arr);
	
		// Sub-domain
		if ($tc == 3) $url_template['subdomain'] = strtolower($tdomain_arr[0]);
	
		// Domain
		$url_template['domain'] = strtolower($tc > 1 ? $tdomain_arr[$tc - 2].'.'.$tdomain_arr[$tc - 1] : $tdomain_arr[0]);
	
		// Path
		$url_template['path'] = implode('/', $segment_arr);
	
		// Segments
		//$url_template['segment_count'] = count($segment_arr);
	
		return $url_template;
	}

	function url_template_compact($url_template) {
		$url_template_minimized;

		if (isset($url_template['protocol'])) $url_template_minimized['r'] = $url_template['protocol'];
		if (isset($url_template['domain'])) $url_template_minimized['d'] = $url_template['domain'];
		if (isset($url_template['subdomain'])) $url_template_minimized['s'] = $url_template['subdomain'];
		if (isset($url_template['path'])) $url_template_minimized['p'] = $url_template['path'];
		if (isset($url_template['params'])) $url_template_minimized['q'] = $url_template['params'];
		if (isset($url_template['fragment'])) $url_template_minimized['f'] = $url_template['fragment'];

		return $url_template_minimized;
	}

	function url_template_expand($url_template) {
		$url_template_minimized;

		if (isset($url_template['r'])) $url_template_minimized['protocol'] = $url_template['r'];
		if (isset($url_template['d'])) $url_template_minimized['domain'] = $url_template['d'];
		if (isset($url_template['s'])) $url_template_minimized['subdomain'] = $url_template['s'];
		if (isset($url_template['p'])) $url_template_minimized['path'] = $url_template['p'];
		if (isset($url_template['q'])) $url_template_minimized['params'] = $url_template['q'];
		if (isset($url_template['f'])) $url_template_minimized['fragment'] = $url_template['f'];

		return $url_template_minimized;
	}
	
	function url_template_sanitize($url_template) {
	
		$url_template_sanitized['status'] = true;
	
		// Protocol
		if (!isset($url_template['protocol'])) $url_template_sanitized['protocol'] = '{}';
		elseif (strpos($url_template['protocol'], '{}') !== false) $url_template_sanitized['protocol'] = '{}';
		elseif (strcmp($url_template['protocol'], 'http') == 0) $url_template_sanitized['protocol'] = 'http';
		elseif (strcmp($url_template['protocol'], 'https') == 0) $url_template_sanitized['protocol'] = 'https';
		else {
			$url_template_sanitized['protocol'] = '{}';
			$url_template_sanitized['status'] = false;
			$url_template_sanitized['error']['protocol'] = "Use 'http://' or 'https://' for Protocol. In case of both, use blank or '{}://'.";
		}
	
		// SubDomain
		$url_template_sanitized['subdomain'] = isset($url_template['subdomain']) ? $url_template['subdomain'] : 'www';
	
		// Domain
		$url_template_sanitized['domain'] = $url_template['domain'];
	
		// Path
		$url_template_sanitized['path'] = isset($url_template['path']) ? $url_template['path'] : '';
	
		// Query
		$params;
		if (isset($url_template['params'])) {
			foreach($url_template['params'] as $key => $value) {
				if (strpos($key, '{}') !== false) {
					$url_template_sanitized['status'] = false;
					$url_template_sanitized['error']['params'] = "Query string parameter name cannot contain '{}'. Parameter value can be either '{}' or the exact value.";
					continue;
				}
	
				if (strlen($value) == 0) $value = '{}';
				elseif (strpos($value, '{}') !== false) $value = '{}';
				elseif (strpos($value, '{!}') !== false) $value = '{!}';
				elseif (strpos($value, '{:}') !== false) $value = '{:}';
	
				$params[$key] = $value; 
			}
	
			// implement sort by key here....
		}
		$url_template_sanitized['params'] = $params;
	
		// Fragment
		if (isset($url_template['fragment'])) {
			if (strpos($url_template['fragment'], '{}') !== false) {
				$url_template_sanitized['status'] = false;
				$url_template_sanitized['error']['fragment'] = "URL fragment/anchor (#) cannot contain '{}'.";
			}
			else $url_template_sanitized['fragment'] = $url_template['fragment'];
		}
	
		// All done
		return $url_template_sanitized;
	
	}
	
	function url_template_to_string($sanitized_template) {
		$params = [];
		if (isset($sanitized_template['params'])) foreach ($sanitized_template['params'] as $key => $value) $params[] = $key.'='.$value;
	
		return $sanitized_template['protocol'].'://'.$sanitized_template['subdomain'].'.'.$sanitized_template['domain'].(strlen($sanitized_template['path']) > 0 ? '/'.$sanitized_template['path'] : '').(count($params) > 0 ? '?'.implode('&', $params) : '').(isset($sanitized_template['fragment']) ? '#'.$sanitized_template['fragment'] : '');
	}
	
	function url_template_create($sanitized_template, $context_type, $context_id) {
	
		$template_str = db_sanitize(url_template_to_string($sanitized_template));
	
		$url = str_replace('%', '\%', $sanitized_template['subdomain'].'.'.$sanitized_template['domain'].(strlen($sanitized_template['path']) > 0 ? '/'.$sanitized_template['path'] : ''));
		$url = str_replace('_', '\_', $url);
		$url = str_replace('{}', '_%', $url);
		$url = str_replace('{:}', '%', $url);
		$url = str_replace('{!}', '', $url);
		$url = db_sanitize($url);

		$protocol = db_sanitize(strcmp($sanitized_template['protocol'], '{}') == 0 ? 'http_or_https' : $sanitized_template['protocol']);
		$params = ((isset($sanitized_template['params'])) && (count($sanitized_template['params']) > 0)) ? db_sanitize(json_encode($sanitized_template['params'])) : 'NULL';
		$fragment = isset($sanitized_template['fragment']) ? db_sanitize($sanitized_template['fragment']) : 'NULL';
		$context_type = db_sanitize($context_type);
	
		db_exec('INSERT INTO system_url_template(template_str, protocol, url, params, fragment, context_type, context_id) VALUES ('.$template_str.','.$protocol.','.$url.','.$params.','.$fragment.','.$context_type.','.$context_id.');');
	
		return db_get_last_insert_id();

	}

	function url_template_update($sanitized_template, $template_id){

		$template_str = db_sanitize(url_template_to_string($sanitized_template));
	
		$url = str_replace('%', '\%', $sanitized_template['subdomain'].'.'.$sanitized_template['domain'].(strlen($sanitized_template['path']) > 0 ? '/'.$sanitized_template['path'] : ''));
		$url = str_replace('_', '\_', $url);
		$url = str_replace('{}', '_%', $url);
		$url = str_replace('{:}', '%', $url);
		$url = str_replace('{!}', '', $url);
		$url = db_sanitize($url);

		$protocol = db_sanitize(strcmp($sanitized_template['protocol'], '{}') == 0 ? 'http_or_https' : $sanitized_template['protocol']);
		$params = ((isset($sanitized_template['params'])) && (count($sanitized_template['params']) > 0)) ? db_sanitize(json_encode($sanitized_template['params'])) : 'NULL';
		$fragment = isset($sanitized_template['fragment']) ? db_sanitize($sanitized_template['fragment']) : 'NULL';
		$context_type = db_sanitize($context_type);

               	db_exec('UPDATE system_url_template SET template_str='.$template_str.' ,  protocol = '.$protocol.' , url = '.$url.' ,   params = '.$params.' , fragment = '.$fragment.' WHERE template_id = '.$template_id.' ');
               	return true;
       }
	
	function url_template_get($template_str, $context_type) {
		$res = db_query('SELECT * FROM system_url_template WHERE template_str='.db_sanitize($template_str).' AND context_type='.db_sanitize($context_type).';');
	
		if (isset($res[0])) return $res[0];
		else return false;
	}
	
	function url_template_match($url, $context_type, $is_templated = false) {
	
		// Prep
		$template;
		if ($is_templated) $template = $url;
		else $template = url_template_from_string($url);
		
		$protocol = (isset($template['protocol']) && (strlen($template['protocol']) > 0)) ? $template['protocol'] : 'http'; 
		$url = (isset($template['subdomain']) ? $template['subdomain'] : 'www').'.'.$template['domain'].((strlen($template['path']) > 0) ? '/'.$template['path'] : '');
	
		// Build Query
		$query = 'SELECT * FROM system_url_template WHERE 
			(context_type = '.db_sanitize($context_type).') AND
			(protocol IN (\'http_or_https\', '.db_sanitize($protocol).')) AND 
			('.db_sanitize($url).' LIKE url)';
	
		if (!isset($template['fragment'])) $query .= ' AND (fragment IS NULL)';
		$query .= ';';
	
		// Find
		$res = db_query($query);
	
		// Match Params
		$matched_1 = [];
		foreach($res as $matched) {
			// No param rules defined for this template
			if ((!isset($matched['params'])) || (strlen($matched['params'])==0)) array_push($matched_1, $matched);

			// Rules are defined
			else {
				$param_rules = json_decode($matched['params'], true);	
				$flag = true;

				// Rule-Front Based Matching
				foreach($param_rules as $key => $value) {

					// Rule {} - Param must exist, any value allowed !
					if ($value == '{}') {
						if (!isset($template['params'][$key])) {
							$flag = false;
							break;
						}
						else continue;
					}

					// Rule {!} - Param must NOT exist!
					elseif ($value == '{!}') {
						if (isset($template['params'][$key])) {
							$flag = false;
							break;
						}
						else continue;
					}

					// Rule {:} - Param may exist or may not exist, with any value!
					elseif ($value == '{:}') continue;

					// Rule Exact match - Param must exist only with a specific value!
					else {
						if ((!isset($template['params'][$key])) || ($template['params'][$key] != $value)) {
							$flag = false;
							break;
						}
						else continue;
					}
				}

				// Push if matched
				if ($flag) array_push($matched_1, $matched);
			}
		}
	
		// Match Fragment
		$matched_2 = [];
		if (isset($template['fragment'])) {
			foreach($matched_1 as $matched) {
				if (!isset($matched['fragment'])) array_push($matched_2, $matched);
				elseif (strcmp($matched['fragment'], $template['fragment']) == 0) array_push($matched_2, $matched);
			}
		}
		else $matched_2 = $matched_1;
	
		// All Done
		return $matched_2;
	
	}

	function url_template_match_best($url, $context_type) {
		
	}


?>