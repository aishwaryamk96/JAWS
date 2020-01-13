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
		header('Location: '.WEB_URL.'/index.php');
		die();
	}

	// Auth module handles session-based, user-based and API-key based Authentication
	// JAWS can authenticate the caller of the script in any one of these three methods
	// Views - usually based on session
	// APIs - can be based on any of the three
	// Tasks - tasks do not use any authentication. They can only be executed from local system as CRONs
	// Ideal feature-key nomenclature example = apiname.subcategory.functionname (webapi.testers.func)
	// Structure of any API Call = POST as JSON Obj {"[param-name]" => "[value]", .., "persistence" => { "[param-name]" =>{ "layer" => "[layer-name]", "type" => "[entity-type]" } }, "auth" => { "type" => "[type]", "[cred_1]" => "[value]", .. } }

  	// This will keep track of if session has been started;
  	$GLOBALS["auth"]["session"]["status"] = false;

	// This will get all allow/deny feature keys for the given roles array
  	function auth_get_roles($roles_str_delim) {

  		$roles_str_arr = explode(";",$roles_str_delim);
  		$role_arr;
  		$role_featurekey_arr = array();
  		$role_id = 0;

  		$obj = db_prepare("SELECT name, allow, deny FROM system_role WHERE role_id=:role_id LIMIT 1;");
  		$obj->bindParam(':role_id', $role_id);

  		foreach ($roles_str_arr as $role_str) {

  			$role_id = intval($role_str);
  			$res = db_query_prepared($obj);
        	if (!isset($res[0])) continue;

  			$role_arr[$role_str] = $res[0]["name"];
  			$allow_str = $res[0]["allow"];
  			$deny_str = $res[0]["deny"];
  			$allow_arr = explode(";",$allow_str);
  			$deny_arr = explode(";",$deny_str);

  			foreach($allow_arr as $key) { if (!isset($role_featurekey_arr[$key])) $role_featurekey_arr[$key] = 1; }
  			foreach($deny_arr as $key) $role_featurekey_arr[$key] = 0;

  		}

  		$role_arr["feature_keys"] = $role_featurekey_arr;
  		return $role_arr;

  	}

  	// This will check if a particular feature key is in the allowed list of the feature key lists but not in the deny list
  	function auth_feature_is_allowed($key, $feature_arr) {

  		if (!isset($feature_arr[$key])) return false;
  		return $feature_arr[$key];

  	}

  	// This will check if the current request is coming from LAN-side or WAN-side
  	function auth_is_cli() {
  		return (!isset($_SERVER['SERVER_SOFTWARE']) && (php_sapi_name() == 'cli' || (is_numeric($_SERVER['argc']) && $_SERVER['argc'] > 0)));
  	}


  	// --------------------------------------------------------------------------------------------------------------
  	// The following functions perform operations on users using sessions
  	// --------------------------------------------------------------------------------------------------------------

  	// This function starts session tracking - call when rendering views or using session auth for API
  	function auth_session_init() {

      if ($GLOBALS["auth"]["session"]["status"]) return true;

    	if (ini_set('session.use_only_cookies', 1) === FALSE) return false; // Forces sessions to only use cookies.
  		if (ini_set('session.cookie_httponly', 1) === FALSE) return false; // This stops scripts being able to access the session id. (Anti XSS-Attacks)

      	//$secure = SECURE;  // set to SECURE for SSL Only
      	//$httponly = true;  // This stops scripts being able to access the session id. (Anti XSS-Attacks)
      	//$cookieParams = session_get_cookie_params();
      	//session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $cookieParams["secure"], $cookieParams["httponly"]);

        if (defined("BATCAVE_APP")) {
          session_name("batcave");
        }
        else {
        	session_name("jaws");
        }
      	if (!session_start()) return false;
        $GLOBALS["auth"]["session"]["status"] = true;
      	return true;

  	}

    // This will stop the current session without destroying it
    function auth_session_deinit() {
      session_destroy();
      $GLOBALS["auth"]["session"]["status"] = false;
    }

  	// This checks if anyone is logged in
  	function auth_session_is_logged() {

  		auth_session_init();
  		return isset($_SESSION["user"]);

  	}

  	// This will login an user based on his credentials and start his session
  	function auth_session_login($email, $pass) {

        if (strlen($email) == 0) return false;

  		auth_session_init();
  		auth_session_logout();

  		$pass_md5 = db_sanitize(md5($pass));
  		$email = db_sanitize($email);

  		$res = db_query("SELECT * FROM user WHERE email=".$email." AND pass=".$pass_md5." AND status='active' LIMIT 1;");
  		if (!isset($res[0])) return false;

  		session_regenerate_id(true);
  		$_SESSION["user"] = $res[0];
        $_SESSION["user"]["session"] = session_id();
  		auth_session_load_roles();
  		db_exec("UPDATE user SET session=".db_sanitize($_SESSION["user"]["session"])." WHERE user_id=".$res[0]["user_id"].";");

  		return true;

  	}

  	// This will login an user - without testing for password
  	function auth_session_login_forced($email) {

        if (strlen($email) == 0) return false;

  		auth_session_init();
  		auth_session_logout();

  		$email = db_sanitize($email);

  		$res = db_query("SELECT * FROM user WHERE email=".$email." AND status='active' LIMIT 1;");
  		if (!isset($res[0])) return false;

  		session_regenerate_id(true);
  		$_SESSION["user"] = $res[0];
        $_SESSION["user"]["session"] = session_id();
  		auth_session_load_roles();
  		db_exec("UPDATE user SET session=".db_sanitize($_SESSION["user"]["session"])." WHERE user_id=".$res[0]["user_id"].";");

  		return true;

  	}

    // This will login/reg an user based on the given provider
    // Mode = login : allow login only - no assoc/reg
    // Mode = reg : allow reg only - no login/assoc (Note: user will be logged in upon account creation)
    // Mode = assoc : allow assoc - no login/reg (Note: requires user to be logged in to JAWS for soc association)
    // Mode = create : login or reg (Note: user will be logged in upon account creation)
    function auth_session_soc($provider, $mode = "login", $reauth = false) {

        // Load required stuff
        load_module("user");
        load_plugin("hybridauth");

        // get the email ID from this provider
        $soc_info = soc_get_info($provider, false, $reauth);
        if ($soc_info === false) return false;

        //Prep
        $user = user_get_by_email($soc_info["email"]);
        auth_session_init();

        // Start
        switch($mode) {

            case "login": // Mode is login only
                if ($user === false) return false;
                else {
                    if ($user["soc_".$provider] == "") user_update($user["user_id"], array("soc_".$provider => $soc_info["email"]));
                    return auth_session_login_forced($user["email"]);
                }
                break;

            case "reg": // Mode is reg only
                if ($user === false) {

                    $user = user_create($soc_info["email"], substr(str_shuffle($soc_info["name"].str_replace("@", "0", str_replace(".", "", $soc_info["email"]))), 0, 10), $soc_info["name"], "", true);
                    user_update($user["user_id"], array(
                        "soc_".$soc_info["soc"] => $soc_info["email"],
                        "photo_url" => $soc_info["photo_url"]
                        ));

                }
                else return false;
                break;

            case "assoc": // Mode is assoc only
                if (!auth_session_is_logged()) return false;
                else {
                    if (!$user) user_update($_SESSION["user"]["user_id"], array("soc_".$soc_info["soc"] => $soc_info["email"]));

                    else {
                        if (strcmp($user["user_id"], $_SESSION["user"]["user_id"]) != 0) return false;
                        else user_update($_SESSION["user"]["user_id"], array("soc_".$soc_info["soc"] => $soc_info["email"]));
                    }
                }
                break;

            case "create": // Mode is login or create
                if ($user === false) {
                    $user = user_create($soc_info["email"], substr(str_shuffle($soc_info["name"].str_replace("@", "0", str_replace(".", "", $soc_info["email"]))), 0, 10), $soc_info["name"], "", true);
                    user_update($user["user_id"], array(
                        "soc_".$soc_info["soc"] => $soc_info["email"],
                        "photo_url" => $soc_info["photo_url"]
                        ));
                }

                else {
                      if ($user["soc_".$provider] == "") user_update($user["user_id"], array("soc_".$provider => $soc_info["email"]));
                }

                return auth_session_login_forced($user["email"]);
                break;

            default: return false;

        }

        // All done!
        return true;

    }

    // This will login/reg an user based on email - password combination (corp login)
    // Mode = login : allow login only - no assoc/reg
    // Mode = reg : allow reg only - no login/assoc (Note: user will be logged in upon account creation)
    // Mode = assoc : allow assoc - no login/reg (Note: requires user to be logged in to JAWS for soc association)
    // Mode = create : login or reg (Note: user will be logged in upon account creation)
    function auth_session_corp($email, $pass, $mode = "login") {

        // Load required stuff
        load_module("user");

        //Prep
        $user = user_get_by_email($soc_info["email"]);
        auth_session_init();

        // Start
        switch($mode) {

            case "login": // Mode is login only
                if ($user === false) return false;
                else {
                    if ($user["soc_".$provider] == "") user_update($user["user_id"], array("soc_".$provider => $soc_info["email"]));
                    return auth_session_login_forced($user["email"]);
                }
                break;

            case "reg": // Mode is reg only
                if ($user === false) {

                    $user = user_create($soc_info["email"], substr(str_shuffle($soc_info["name"].str_replace("@", "0", str_replace(".", "", $soc_info["email"]))), 0, 10), $soc_info["name"], "", true);
                    user_update($user["user_id"], array(
                        "soc_".$soc_info["soc"] => $soc_info["email"],
                        "photo_url" => $soc_info["photo_url"]
                        ));

                }
                else return false;
                break;

            case "assoc": // Mode is assoc only
                if (!auth_session_is_logged()) return false;
                else {
                    if (!$user) user_update($_SESSION["user"]["user_id"], array("soc_".$soc_info["soc"] => $soc_info["email"]));

                    else {
                        if (strcmp($user["user_id"], $_SESSION["user"]["user_id"]) != 0) return false;
                        else user_update($_SESSION["user"]["user_id"], array("soc_".$soc_info["soc"] => $soc_info["email"]));
                    }
                }
                break;

            case "create": // Mode is login or create
                if ($user === false) {
                    $user = user_create($soc_info["email"], substr(str_shuffle($soc_info["name"].str_replace("@", "0", str_replace(".", "", $soc_info["email"]))), 0, 10), $soc_info["name"], "", true);
                    user_update($user["user_id"], array(
                        "soc_".$soc_info["soc"] => $soc_info["email"],
                        "photo_url" => $soc_info["photo_url"]
                        ));
                }

                else {
                      if ($user["soc_".$provider] == "") user_update($user["user_id"], array("soc_".$provider => $soc_info["email"]));
                }

                return auth_session_login_forced($user["email"]);
                break;

            default: return false;

        }

        // All done!
        return true;

    }

  	// This will get all roles that have been assigned to the currently logged in user, and store it in the current session
  	function auth_session_load_roles() {
  		$roles_str_delim = $_SESSION["user"]["roles"];
  		unset($_SESSION["user"]["roles"]);
  		$_SESSION["user"]["roles"] = auth_get_roles($roles_str_delim);
  	}

  	// This will check if a particular feature key is in the allowed list of the session feature key list but not in the deny list
  	function auth_session_is_allowed($key) {
  		return auth_feature_is_allowed($key, $_SESSION["user"]["roles"]["feature_keys"]);
  	}

  	// This will logout the currently logged in user
  	function auth_session_logout() {

  		try {
            unset($_SESSION["user"]);
  			session_unset();
  			session_destroy();
  		}
  		catch(exception $e) { }

      	$GLOBALS["auth"]["session"]["status"] = false;
  		auth_session_init();

  	}

  	// --------------------------------------------------------------------------------------------------------------
  	// The following functions perform operations on users WITHOUT using sessions
  	// --------------------------------------------------------------------------------------------------------------

  	// This will authenticate an user (see if his creds are valid and if he exists)...but NOT create a session for him
  	function auth_user_login($email, $pass) {

  		$pass_md5 = db_sanitize(md5($pass));
  		$email = db_sanitize($email);

  		$res = db_query("SELECT * FROM user WHERE email=".$email." AND pass=".$pass_md5." AND status!='blocked' LIMIT 1;");
  		if (!isset($res[0])) return false;
  		else return $res[0];

  	}

  	// This will authenticate an API based on its feature-key and the authentication method requested
  	function auth_api($feature_key) {

  		$auth_type = "session";
  		$cred_1 = "";
  		$cred_2 = "";
          $feature_keys;

  		if (isset($_POST["auth"])) {
  			if (isset($_POST["auth"]["type"])) {

  				$auth_type = $_POST["auth"]["type"];

  				if (strcmp($auth_type,"user") == 0) {

  					$cred_1 = $_POST["auth"]["user"];
  					$cred_2 = $_POST["auth"]["pass"];

  					if (isset($_POST["persistence"]["auth"])) {

  						$entity["layer"] = $_POST["persistence"]["auth"]["layer"];
  						$entity["id"] = $cred_1;
  						$entity["type"] = $_POST["persistence"]["auth"]["type"];

  						$cred_1 = get_native_id($entity)["id"];

  					}

  					$user = auth_user_login($cred_1, $cred_2);
  					if (!$user) return false;

  					$roles = auth_get_roles($user["roles"]);
  					$feature_keys = $roles["feature_keys"];
  					$GLOBALS["temp"]["user"] = $user;
  					$GLOBALS["temp"]["user"]["roles"] = $roles;

  				}
  				else if (strcmp($auth_type,"api") == 0) {

  					$cred_1 = db_sanitize($_POST["auth"]["dev"]);
  					$cred_2 = db_sanitize($_POST["auth"]["key"]);

  					$res = db_query("SELECT * FROM system_api WHERE dev_name=".$cred_1." AND dev_key=".$cred_2." AND status=1 LIMIT 1;");
  					if (!isset($res[0]["dev_id"])) return false;

  					$roles = auth_get_roles($res[0]["roles"]);
  					$feature_keys = $roles["feature_keys"];
  					$GLOBALS["temp"]["dev"] = $res[0];
  					$GLOBALS["temp"]["dev"]["roles"] = $roles;

  				}
  				else if (strcmp($auth_type,"session") == 0) {

  					auth_session_init();
            		if (!auth_session_is_logged()) return false;
  					$feature_keys = $_SESSION["user"]["roles"]["feature_keys"];

  				}
  				else return false;
  			}

  			else {

  				auth_session_init();
          		if (!auth_session_is_logged()) return false;
  				$feature_keys = $_SESSION["user"]["roles"]["feature_keys"];

  			}

  		}
  		else {

  			auth_session_init();
        	if (!auth_session_is_logged()) return false;
  			$feature_keys = $_SESSION["user"]["roles"]["feature_keys"];

  		}

  		$GLOBALS["temp"]["api"]["feature_keys"] = $feature_keys;
  		return auth_feature_is_allowed($feature_key, $feature_keys);

  	}



?>