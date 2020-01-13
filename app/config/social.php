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
		header('Location: '.WEB_URL);
		die();
	}

	// Developer Keys - Live Mode
	// define("JAWS_AUTH_SOCIAL_FB_SID","875536382554370");
	// define("JAWS_AUTH_SOCIAL_FB_KEY","7ae53f597cca36d833fcbed17cff54ad");

	define("JAWS_AUTH_SOCIAL_FB_SID","1277255255686445");
	define("JAWS_AUTH_SOCIAL_FB_KEY","57c0de7b8867464f1c59604943a0de79");

	define("JAWS_AUTH_SOCIAL_GP_ID","618216848458-7g6euu405dj6n1ji8t33mri4pme6nh6u.apps.googleusercontent.com");
	define("JAWS_AUTH_SOCIAL_GP_KEY","aAKqpStpvSGrP-f41_H49e3R");

	define("JAWS_AUTH_SOCIAL_LI_ID","75yneg96feb8ow");
	define("JAWS_AUTH_SOCIAL_LI_KEY","jj0vmoImtGklbsrO");

	// Developer Keys - Test Mode
	define("JAWS_AUTH_SOCIAL_FB_SID_TEST","471517916372207");
	define("JAWS_AUTH_SOCIAL_FB_KEY_TEST","812d845bf9517c99a0c7ff3b444f30ea");

	define("JAWS_AUTH_SOCIAL_GP_ID_TEST","618216848458-7g6euu405dj6n1ji8t33mri4pme6nh6u.apps.googleusercontent.com");
	define("JAWS_AUTH_SOCIAL_GP_KEY_TEST","aAKqpStpvSGrP-f41_H49e3R");

	define("JAWS_AUTH_SOCIAL_LI_ID_TEST","75yneg96feb8ow");
	define("JAWS_AUTH_SOCIAL_LI_KEY_TEST","jj0vmoImtGklbsrO");


?>