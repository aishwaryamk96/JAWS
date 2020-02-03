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

    // Load essential stuff
    // Do not load config files here - All config files are loaded automatically !

    load_library("common");
    load_library("db");
    load_library("hook");
    load_library("router");
    load_library("misc");

    load_module("auth");
    load_module("activity");

    // CRM & Sales-Marketing Autoload
    if ((strcmp(JAWS_CRM_CURRENT, "none") != 0) && (strcmp(JAWS_CRM_CURRENT, "") != 0)) load_plugin(JAWS_CRM_CURRENT);
    if ((strcmp(JAWS_SMARKET_CURRENT, "none") != 0) && (strcmp(JAWS_SMARKET_CURRENT, "") != 0)) load_plugin(JAWS_SMARKET_CURRENT);

?>
