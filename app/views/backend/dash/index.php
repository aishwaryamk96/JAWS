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

	// Init Session
	auth_session_init();

	// Load stuff
	load_module("ui");

	// Prep
	$login_params["return_url"] = $_SERVER['REQUEST_URI'];

	// Login Check
	if (!auth_session_is_logged()) {
		ui_render_login_front(array(
					"mode" => "login",
					"return_url" => $login_params["return_url"],
					"text" => "Please login to access this page."
				));
		exit();
	}

	// Priviledge Check
	if (!auth_session_is_allowed("dash")) {
		ui_render_msg_front(array(
				"type" => "error",
				"title" => "Jigsaw Academy",
				"header" => "No Tresspassing",
				"text" => "Sorry, but you do not have permission to access this page.<br/>Contact Jigsaw Support team for assistance."
				));

		exit();
	}

	// Proceed - Load More Stuff
	load_module('course');

	// Proceed - Prep
	$jaws_path = 'https://'.$_SERVER['SERVER_NAME'].'/'.JAWS_PATH_LOCAL.'/';
	$jaws_path_tpl = $jaws_path."app/templates/jaws/backend/dash/";
	$jaws_path_api = $jaws_path."webapi/backend/dash/";
	$jaws_path_api_rest = $jaws_path."webapi/backend/dash/";

	// Render
?>

<!DOCTYPE html>
<html lang="en" data-ng-app="jaws" ng-controller="jawsCtrl">

	<head>
		<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
		<meta charset="utf-8" />
		<title>{{app.name}}</title>

		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<link rel="apple-touch-icon" href="<?php echo $jaws_path_tpl; ?>pages/ico/60.png">
		<link rel="apple-touch-icon" sizes="76x76" href="<?php echo $jaws_path_tpl; ?>pages/ico/76.png">
		<link rel="apple-touch-icon" sizes="120x120" href="<?php echo $jaws_path_tpl; ?>pages/ico/120.png">
		<link rel="apple-touch-icon" sizes="152x152" href="<?php echo $jaws_path_tpl; ?>pages/ico/152.png">

		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-touch-fullscreen" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="default">
		<meta content="{{app.description}}" name="description" />
		<meta content="{{app.author}}" name="author" />

		<script type='text/javascript'>
			<?php
				$jaws_dash_dev = true;
				$jaws_dev_append = $jaws_dash_dev ? '?nocache='.(time()) : '';
			?>
			var _JAWS_PATH = '<?php echo $jaws_path; ?>',
			_JAWS_PATH_TPL = '<?php echo $jaws_path_tpl; ?>',
			_JAWS_PATH_API = '<?php echo $jaws_path_api; ?>',
			_JAWS_PATH_API_REST = '<?php echo $jaws_path_api_rest; ?>',
			_JAWS_PATH_WEB = '<?php echo JAWS_PATH_WEB; ?>',
			_JAWS_USER = JSON.parse(`<?php echo json_encode($_SESSION["user"]); ?>`);

			var _JAWS_DEV_APPEND = (<?php echo $jaws_dash_dev ? 'true' : 'false'; ?>) ? ('?nocache=' + (new Date).getTime().toString()) : '';
		</script>

		<script type="text/javascript"> // why is this need?? Removed for now.
			/* setInterval(function() {
			    var xhr = new XMLHttpRequest();
			    xhr.open("GET", "https://www.jigsawacademy.com/jaws/webapi/ping", true);
			    xhr.withCredentials = true;
			    xhr.send(null);
			}, 30000); */
		</script>

		<!-- BEGIN Vendor CSS-->
		<link href="<?php echo $jaws_path_tpl; ?>assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo $jaws_path_tpl; ?>assets/plugins/bootstrapv3/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo $jaws_path_tpl; ?>assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo $jaws_path_tpl; ?>assets/plugins/jquery-scrollbar/jquery.scrollbar.css" rel="stylesheet" type="text/css" media="screen" />
		<link id="lazyload_placeholder">

		<!-- BEGIN Pages CSS-->
		<link href="<?php echo $jaws_path_tpl; ?>pages/css/pages-icons.css" rel="stylesheet" type="text/css">
		<link class="main-stylesheet" ng-href="{{app.layout.theme}}" rel="stylesheet" type="text/css" />
		<!--[if lte IE 9]>
			<link href="<?php echo $jaws_path_tpl; ?>pages/css/ie9.css" rel="stylesheet" type="text/css" />
		<![endif]-->
		<!--[if lt IE 9]>
			<link href="<?php echo $jaws_path_tpl; ?>assets/plugins/mapplic/css/mapplic-ie.css" rel="stylesheet" type="text/css" />
		<![endif]-->
		<script type="text/javascript">
			window.onload = function() {
				// fix for windows 8
				if (navigator.appVersion.indexOf("Windows NT 6.2") != -1)
					document.head.innerHTML += '<link rel="stylesheet" type="text/css" href="<?php echo $jaws_path_tpl; ?>pages/css/windows.chrome.fix.css" />'
			}
		</script>
	</head>

	<body class="fixed-header" ng-class="{
	'bg-master-lighter': is('app.extra.timeline'),
	'no-header': is('app.social') || is('app.calendar') || is('app.maps.vector') || is('app.maps.google'),
	'menu-pin' : app.layout.menuPin,
	'menu-behind' : app.layout.menuBehind
	 }">
		<div class="full-height" ui-view></div>
		<!-- BEGIN VENDOR JS -->

		<!-- JQUERY -->
		<script src="<?php echo $jaws_path_tpl; ?>assets/plugins/pace/pace.min.js" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_tpl; ?>assets/plugins/jquery/jquery-1.11.1.min.js" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_tpl; ?>assets/plugins/modernizr.custom.js" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_tpl; ?>assets/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_tpl; ?>assets/plugins/bootstrapv3/js/bootstrap.min.js" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_tpl; ?>assets/plugins/jquery/jquery-easy.js" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_tpl; ?>assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_tpl; ?>assets/plugins/jquery-bez/jquery.bez.min.js"></script>
		<script src="<?php echo $jaws_path_tpl; ?>assets/plugins/jquery-ios-list/jquery.ioslist.min.js" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_tpl; ?>assets/plugins/jquery-actual/jquery.actual.min.js"></script>
		<script src="<?php echo $jaws_path_tpl; ?>assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js"></script>
		<script type="text/javascript" src="<?php echo $jaws_path_tpl; ?>assets/plugins/classie/classie.js"></script>

		<!-- ANGULAR -->
		<script src="<?php echo $jaws_path_tpl; ?>assets/plugins/angular/angular.js" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_tpl; ?>assets/plugins/angular-ui-router/angular-ui-router.min.js" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_tpl; ?>assets/plugins/angular-ui-util/ui-utils.min.js" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_tpl; ?>assets/plugins/angular-sanitize/angular-sanitize.min.js" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_tpl; ?>assets/plugins/angular-oc-lazyload/ocLazyLoad.min.js" type="text/javascript"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular-animate.js" type="text/javascript"></script>
		<!-- END VENDOR JS -->

		<!-- BEGIN CORE TEMPLATE JS -->
		<script src="<?php echo $jaws_path_tpl; ?>pages/js/pages.js" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_tpl; ?>assets/js/app.js<?php echo $jaws_dev_append; ?>" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_tpl; ?>assets/js/config.js<?php echo $jaws_dev_append; ?>" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_tpl; ?>assets/js/config.lazyload.js" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_tpl; ?>assets/js/main.js<?php echo $jaws_dev_append; ?>" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_tpl; ?>assets/js/controllers/home.js<?php echo $jaws_dev_append; ?>" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_tpl; ?>assets/js/directives/pg-sidebar.js" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_tpl; ?>assets/js/directives/cs-select.js" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_tpl; ?>assets/js/directives/pg-dropdown.js" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_tpl; ?>assets/js/directives/pg-form-group.js" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_tpl; ?>assets/js/directives/pg-navigate.js" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_tpl; ?>assets/js/directives/pg-portlet.js" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_tpl; ?>assets/js/directives/pg-tab.js" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_tpl; ?>assets/js/directives/pg-search.js" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_tpl; ?>assets/js/directives/skycons.js" type="text/javascript"></script>
		<!--<script src="<?php echo $jaws_path_tpl; ?>assets/js/controllers/search.js<?php //echo $jaws_dev_append; ?>" type="text/javascript"></script>-->
		<script src="<?php echo $jaws_path_tpl; ?>assets/js/providers/api.svc.js" type="text/javascript"></script>
		<script src="<?php echo $jaws_path_tpl; ?>assets/js/providers/feed.js<?php echo $jaws_dev_append; ?>" type="text/javascript"></script>
		<!-- END CORE TEMPLATE JS -->

	</body>

</html>
