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

	// Load Stuff
	//load_module("fabric");

	// Authenticate user
	//fabric_handle_auth();

	// Proceed - Prep
	$jaws_path = 'https://'.$_SERVER['SERVER_NAME'].'/'.JAWS_PATH_LOCAL.'/';
	$jaws_path_templates = $jaws_path."app/templates/fabric/";
	$jaws_path_views = $jaws_path."view/fabric/";
	$jaws_path_api = $jaws_path."webapi/fabric/";

	// Render
?>

<!DOCTYPE html>
<html lang="en" data-ng-app="fabric" ng-controller="rootCtrl">

	<head>
		<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
		<meta charset="utf-8" />
		<title>{{app.name}}</title>
		<meta content="Jigsaw Learning Center" name="description" />
		<meta content="Jigsaw Academy" name="author" />
		<link rel="icon" href="https://www.jigsawacademy.com/jaws/media/jaws/frontend/images/favicon.png" type="image/x-icon">

		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-touch-fullscreen" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="default">

		<!-- JS GLOBALS -->
		<script type='text/javascript'>
			<?php
				$jaws_fabric_dev = true;
				$jaws_dev_append = $jaws_fabric_dev ? '?nocache='.(time()) : '';
			?>
			var _JAWS_PATH = '<?php echo $jaws_path; ?>',
			_JAWS_PATH_TEMPLATES = '<?php echo $jaws_path_templates; ?>',
			_JAWS_PATH_VIEWS = '<?php echo $jaws_path_views; ?>',
			_JAWS_PATH_API = '<?php echo $jaws_path_api; ?>',
			_JAWS_PATH_WEB = '<?php echo JAWS_PATH_WEB; ?>',
			_JAWS_USER = JSON.parse(`<?php echo json_encode($_SESSION["user"]); ?>`);

			var _JAWS_DEV_APPEND = (<?php echo $jaws_dash_dev ? 'true' : 'false'; ?>) ? ('?nocache=' + (new Date).getTime().toString()) : '';
		</script>

		<!-- PACE PAGE LOAD PROGRESS -->
		<link href="<?php echo $jaws_path_templates; ?>plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" />
		<script src="<?php echo $jaws_path_templates; ?>plugins/pace/pace.min.js" type="text/javascript"></script>

		<!-- VENDOR STYLES -->
		<link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700|Source+Sans+Pro:400,500,700" rel="stylesheet">
		<link href="<?php echo $jaws_path_templates; ?>plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo $jaws_path_templates; ?>plugins/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo $jaws_path_templates; ?>plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo $jaws_path_templates; ?>plugins/ionicons/css/ionicons.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo $jaws_path_templates; ?>plugins/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo $jaws_path_templates; ?>plugins/jquery-ui/jquery-ui.structure.min.css" rel="stylesheet" type="text/css" />

		<!-- FABRIC STYLES-->
		<link href="<?php echo $jaws_path_templates; ?>styles/styles.less<?php echo $jaws_dev_append; ?>" rel="stylesheet/less" type="text/css"/>

		<!-- LAZY LOAD PLACEHOLDER -->
		<link id="lazyload_placeholder">

	</head>
	<body>
		<!-- ENV PLACEHOLDER -->
		<div style="height: 100vh; width: 100vw; overflow-x: hidden; overflow-y: auto; padding: 0px; margin: 0px;" ui-view></div>

		<!-- LESS -->
		<script src="<?php echo $jaws_path_templates; ?>plugins/lessjs/less.min.js" type="text/javascript" data-env="development"></script>

		<!-- VENDOR JS -->
		<script src="<?php echo $jaws_path_templates; ?>plugins/jquery/jquery-3.2.1.min.js" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_templates; ?>plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_templates; ?>plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>

		<!-- ANGULAR -->
		<script src="<?php echo $jaws_path_templates; ?>plugins/angular/angular.js" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_templates; ?>plugins/angular-ui-router/angular-ui-router.min.js" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_templates; ?>plugins/angular-ui-util/ui-utils.min.js" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_templates; ?>plugins/angular-sanitize/angular-sanitize.min.js" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_templates; ?>plugins/angular-oc-lazyload/ocLazyLoad.min.js" type="text/javascript"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular-animate.js" type="text/javascript"></script>

		<!-- FRAMEWORK JS -->
		<script src="<?php echo $jaws_path_templates; ?>app/app.js<?php echo $jaws_dev_append; ?>" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_templates; ?>app/config/config.js<?php echo $jaws_dev_append; ?>" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_templates; ?>app/config/config.lazyload.js" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_templates; ?>app/controllers/root.js<?php echo $jaws_dev_append; ?>" type="text/javascript"></script>

		<!-- FABRIC SCRIPTS -->
		<script src="<?php echo $jaws_path_templates; ?>scripts/ping.js<?php echo $jaws_dev_append; ?>" type="text/javascript"></script>
		<!--<script src="<?php //echo $jaws_path_templates; ?>scripts/header-ext.js<?php //echo $jaws_dev_append; ?>" type="text/javascript"></script>-->
	</body>
</html>
