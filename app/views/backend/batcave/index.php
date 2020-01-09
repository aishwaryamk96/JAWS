<?php

load_module("ui");

// Init Session
auth_session_init();

// Prep
$login_params["return_url"] = "https://www.jigsawacademy.com/batcave";

$components = explode("/", trim($_SERVER["REQUEST_URI"], "/"));
$index = array_search("batcave", $components);
if (!empty($components = array_slice($components, $index + 1))) {

	header("Location: /batcave#!/".implode("/", $components));
	die;

}

// Login Check
if (!auth_session_is_logged()) {
    ui_render_login_front(array(
                "mode" => "login",
                "return_url" => $login_params["return_url"],
                "text" => "Please login to access this page."
            ));
    exit();
}

if (!auth_session_is_allowed("batcave") && !auth_session_is_allowed("btc.mobile")) {
    ui_render_msg_front(array(
            "type" => "error",
            "title" => "Jigsaw Academy",
            "header" => "No Tresspassing",
            "text" => "Sorry, but you do not have permission to access this page.<br/>Contact Jigsaw Support team for assistance."
            ));
    exit();
}

$jaws_path_assets = JAWS_PATH_WEB."/app/templates/jaws/backend/batcave";
$jaws_path_js = $jaws_path_assets."/js";
$jaws_path_css = $jaws_path_assets."/css";
$jaws_path_views = $jaws_path_assets."/views";

?>
<html>
<head>
	<title>Batcave</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
	<script src="https://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular-route.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular-animate.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular-sanitize.js"></script>

	<script src="https://use.fontawesome.com/fc49ce4973.js"></script>
	<!-- <script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script> -->

	<script type="text/javascript">
		var JAWS_PATH_VIEWS = "<?php echo $jaws_path_views ?>";
		var JAWS_PATH_WEB = "<?php echo JAWS_PATH_WEB ?>";
		var JAWS_PATH_API = "<?php echo JAWS_PATH_WEB ?>/btcapi";
	</script>

	<script src="<?php echo $jaws_path_js ?>/route.js"></script>
	<script src="<?php echo $jaws_path_js ?>/app.js"></script>
	<script src="<?php echo $jaws_path_js ?>/search.js"></script>
	<script src="<?php echo $jaws_path_js ?>/user.js"></script>
	<script src="<?php echo $jaws_path_js ?>/refer.js"></script>
	<script src="<?php echo $jaws_path_js ?>/lab.js"></script>
	<script src="<?php echo $jaws_path_js ?>/enrollment.js"></script>
	<script src="<?php echo $jaws_path_js ?>/course.js"></script>
	<script src="<?php echo $jaws_path_js ?>/section.js"></script>
	<script src="<?php echo $jaws_path_js ?>/notifications.js"></script>
	<script src="<?php echo $jaws_path_js ?>/students.js"></script>
	<script src="<?php echo $jaws_path_js ?>/careers.js"></script>
	<script src="<?php echo $jaws_path_js ?>/main.js"></script>

	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" href="<?php echo $jaws_path_css ?>/app.css">
	<link rel="stylesheet" href="<?php echo $jaws_path_css ?>/search.css">
	<link rel="stylesheet" href="<?php echo $jaws_path_css ?>/user.css">
	<link rel="stylesheet" href="<?php echo $jaws_path_css ?>/refer.css">
	<link rel="stylesheet" href="<?php echo $jaws_path_css ?>/lab.css">
	<link rel="stylesheet" href="<?php echo $jaws_path_css ?>/enrollment.css">
	<link rel="stylesheet" href="<?php echo $jaws_path_css ?>/course.css">
	<link rel="stylesheet" href="<?php echo $jaws_path_css ?>/section.css">
	<link rel="stylesheet" href="<?php echo $jaws_path_css ?>/notifications.css">
	<link rel="stylesheet" href="<?php echo $jaws_path_css ?>/students.css">
	<link rel="stylesheet" href="<?php echo $jaws_path_css ?>/careers.css">
	<link rel="stylesheet" href="<?php echo $jaws_path_css ?>/main.css">
</head>
<body ng-app="batcave" ng-controller="homeCtrl">
	<div class="header">
		<div style="overflow: auto;">
			<a class="title header-hover" ng-click="openLink('/')">
				<img src="https://www.jigsawacademy.com/jaws/media/jaws/frontend/images/favicon.png" height="32" width="32" class="header-icon">
				<h5 class="title-text">Jigsaw academy</h5>
			</a>
			<form class="search-form" ng-class="{focused: focus}" ng-submit="search()">
				<i class="fa fa-search search-icon" aria-hidden="true"></i>
				<input id="search-text" name="searchText" class="search-query" ng-class="{focused: focus}" placeholder="SEARCH" ng-focus="focus=true" ng-blur="focus=false" ng-model="query">
				<i class="fa fa-times-circle clear-icon" aria-hidden="true" ng-click="searchClear()" ng-hide="!query.length"></i>
			</form>
			<a class="user header-hover" ng-class="{focused: profile_menu}" ng-mouseenter="profile_menu=1" ng-mouseleave="profile_menu=0">
				<img src="<?php echo $_SESSION["user"]["photo_url"] ?>" height="32" width="32" class="user-icon">
				<h5 class="user-text"><?php echo $_SESSION["user"]["name"] ?></h5>
			</a>
			<ul class="profile-menu" ng-show="profile_menu" ng-mouseenter="profile_menu=true" ng-mouseleave="profile_menu=false">
				<li class="menu-item not-last" ng-click="openLink('user/<?php echo $_SESSION["user"]["user_id"] ?>')"><a class="menu-item-text"><i class="fa fa-user menu-item-icon" aria-hidden="true"></i>Profile</a></li>
				<li class="menu-item logout" ng_click="logout()"><a class="menu-item-text"><i class="fa fa-sign-out menu-item-icon" aria-hidden="true"></i>Logout</a></li>
			</ul>
		</div>
	</div>
	<div ng-view class="body {{custom_body_class}}" ng-class="{'custom-body': custom_body_css}">
	</div>
</body>
</html>