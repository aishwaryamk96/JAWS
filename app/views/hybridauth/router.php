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
		header('Location: https://www.jigsawacademy.com');
		die();
	}

    // This view will re-route return URLs for HybridAuth authentication plugin
    //---------------

    /*$request_uri = $_SERVER["REQUEST_URI"];
    $request_uri = substr($request_uri,strpos($request_uri, JAWS_PATH_LOCAL) + strlen(JAWS_PATH_LOCAL));
    $request_uri = str_replace("\\", '/', $request_uri);
    $request_uri = trim($request_uri, "/");
    $actual_uri = "app/plugins/hybridauth/".$request_uri;

    //echo $actual_uri."<br/>".$_SERVER["REQUEST_URI"]."<br/>".$request_uri;
    //die();

    $str = substr($_SERVER["REQUEST_URI"], 0, strpos($_SERVER["REQUEST_URI"], "?"));
    $_str = substr($str, -3);

    if (strcmp($_str, "php")) require_once ($actual_uri);
	else*/

    require_once ("app/plugins/hybridauth/hybridauth/index.php");

?>
