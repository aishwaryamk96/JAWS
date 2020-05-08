<!doctype html>
<html lang="en" ng-app="batcave" class="h-100">
<head>
	<title ng-bind="title + ' - Batcave'">Home - Batcave</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<base href="/">
	<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
	<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.7.1/css/all.css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	<link rel="stylesheet" href="https://unpkg.com/bootstrap-material-design@4.1.1/dist/css/bootstrap-material-design.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" href="/b/css/select2.min.css">
	<script src="/b/js/select2.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/arrive/2.4.1/arrive.min.js"></script>
	<script src="https://unpkg.com/popper.js@1.12.6/dist/umd/popper.js" integrity="sha384-fA23ZRQ3G/J53mElWqVJEGJzU0sTs+SvzG8fXVWP+kJQ1lwFAOkcUOysnlKJC33U" crossorigin="anonymous"></script>
	<script src="https://cdn.rawgit.com/FezVrasta/snackbarjs/1.1.0/dist/snackbar.min.js"></script>
	<script src="https://unpkg.com/bootstrap-material-design@4.1.1/dist/js/bootstrap-material-design.js" integrity="sha384-CauSuKpEqAFajSpkdjv3z9t8E7RlpJ1UP0lKM/+NdtSarroVKu069AlsRPKkFBz9" crossorigin="anonymous"></script>
	<script>$(document).ready(function() { $('body').bootstrapMaterialDesign(); });</script>
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.7.4/angular.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular-route.js"></script>
	<script type="text/javascript">
		const VIEWS = "<?= $views ?>";
		const WEB = "<?= BATCAVE ?>";
		const API = "<?= BATCAVE ?>/btcapi";
	</script>
	<script src="<?= $js ?>/jq.pm.js"></script>
	<script src="<?= $js ?>/app.js"></script>
	<link href="<?= $css ?>/app.css" rel="stylesheet">
	<!-- <script src="https://chat.jigsawacademy.com/c/js/embed.js"></script> -->
</head>
<body ng-controller="rootCtrl">
	<div class="bm-overlay show">
		<div class="bm-loader"></div>
	</div>
	<nav class="navbar fixed-top navbar-light bg-white" ng-show="initialized">
		<a class="navbar-brand" href="/">
		     <!-- JA-161 Start -->
			<img src="https://batcave.jigsawacademy.com/media/jaws/frontend/images/jigsaw-logo-manipal.png" width="30" height="30" class="d-inline-block align-top" alt="">
			 <!-- JA-161 End -->
			Batcave
		</a>
		 <form class="form-inline my-2 my-lg-0 position-relative" ng-submit="search()">
			<input class="form-control text-center" type="search" placeholder="Search" aria-label="Search" style="width:500px;" ng-model="searchText" autofocus="true">
			<button type="button" class="close position-absolute" aria-label="Close" style="right:0;" ng-click="searchText=''" id="btnClearSearch">
				<span aria-hidden="true">&times;</span>
			</button>
		</form>
		<div class="d-flex">
			<div class="dropdown mr-2">
				<button class="btn btn-primary bmd-btn-icon {{notifications.t>0 ? 'btn-raised' : ''}}" href="/notifications" data-toggle="dropdown">
					<i class="material-icons ng-binding">notifications_none</i>
				</button>
				<div class="dropdown-menu dropdown-menu-right" style="left:-315px;min-width:350px;">
					<div class="list-group-item text-muted d-flex justify-content-center bg-light">{{notifications.t==0 ? 'No' : notifications.t}} notifications</div>
					<ul class="list-group py-0" style="max-height:calc(100vh - 11rem);overflow: auto;">
						<a href="/notifications/{{n.id}}" class="d-flex justify-content-between border-bottom border-muted p-3 text-dark notification-link position-relative" ng-repeat="n in notifications.all" style="min-height:120px;" ng-if="!n.resolved_at">
							<img src="{{n.photo_url}}" class="rounded-circle" style="max-width:50px;max-height:50px;width:auto;height:auto;">
							<div class="w-80 d-flex flex-column">
								<div class="d-flex justify-content-between mb-2">
									<elipsize class="font-weight-bold" length="50" str="n.name"></elipsize>
									<div class="text-info" style="font-size: 90%">{{n.days}} days</div>
								</div>
								<elipsize class="list-group-item-heading text-capitalize text-muted mb-0" length="70" str="n.desc" style="font-size: 90%"></elipsize>
							</div>
						</a>
					</ul>
					<div class="dropdown-divider"></div>
					<a class="btn btn-primary d-flex justify-content-center mb-0" href="/notifications">All notifications</a>
				</div>
			</div>
			<div class="dropdown ml-2">
				<button class="btn btn-primary bmd-btn-icon" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<!-- <i class="material-icons">more_vert</i> -->
					<img src="{{user.photo_url ? user.photo_url : '/b/images/male.png'}}" width="30" height="30" class="rounded-circle" alt="">
				</button>
				<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton" style="left:-123px;">
					<!-- <a class="dropdown-item" href="#">
						<i class="material-icons mr-3">person</i>Profile
					</a> -->
					<a class="dropdown-item" href="/settings">
						<i class="material-icons mr-3">settings</i>Settings
					</a>
					<a class="dropdown-item" href="#" ng-click="reloadAccount()">
						<i class="material-icons mr-3">refresh</i>Reload
					</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item text-danger" href="#" ng-click="logout()">
						<i class="material-icons mr-3">power_settings_new</i>Logout
					</a>
				</div>
			</div>
		</div>
	</nav>
	<div class="drawer d-flex flex-column justify-content-between" id="sideNav" ng-if="initialized" ng-class="{fixed: preferences.settings.drawer.fixed}">
		<ul class="list-group">
			<span ng-if="user_can('view', 'enrollment')">
				<div class="list-group-item px-0">Subscriptions</div>
				<a class="list-group-item px-0 justify-content-between" href="/students">
					Students
					<i class="fas fa-users mr-0"></i>
				</a>
				<div class="dropdown-divider"></div>
			</span>
			<span ng-if="user_can('view', 'catalogue')">
				<div class="list-group-item px-0">Catalogue</div>
				<a class="list-group-item px-0 justify-content-between" href="/courses">
					Courses
					<i class="fas fa-book-open mr-0"></i>
				</a>
				<a class="list-group-item px-0 justify-content-between" href="/programs">
					Programs
					<i class="fas fa-graduation-cap mr-0"></i>
				</a>
				<div class="dropdown-divider"></div>
			</span>
			<span ng-if="user_can('view', 'career') || user_can('view', 'refer') || user_can('view', 'mobile')">
				<div class="list-group-item px-0">Miscellaneous</div>
				<a class="list-group-item px-0 justify-content-between" href="/careers" ng-if="user_can('view', 'career')">
					Careers
					<i class="fas fa-briefcase mr-0"></i>
				</a>
				<a class="list-group-item px-0 justify-content-between" href="/refer" ng-if="user_can('view', 'refer')">
					Referrals
					<i class="fas fa-user-friends mr-0"></i>
				</a>
				<a class="list-group-item px-0 justify-content-between" href="/mobile" ng-if="user_can('view', 'mobile')">
					Mobile
					<i class="fas fa-mobile-alt mr-1" style="font-size: 18px;"></i>
				</a>
				<a class="list-group-item px-0 justify-content-between" href="/labs" ng-if="user_can('view', 'labs')">
					Labs
					<i class="fas fa-microscope mr-1" style="font-size: 18px;"></i>
				</a>
			</span>
			<span ng-if="user_can('create', 'order') || user_can('view', 'overdue')">
				<div class="dropdown-divider"></div>
				<div class="list-group-item px-0">Sales</div>
				<a class="list-group-item px-0 justify-content-between" href="/orders" ng-if="user_can('create', 'order')">
					Orders
					<i class="fas fa-shopping-cart mr-0"></i>
				</a>
				<a class="list-group-item px-0 justify-content-between" href="/payments" ng-if="user_can('view', 'overdue')">
					Payments
					<i class="fas fa-rupee-sign mr-0"></i>
				</a>
				<a class="list-group-item px-0 justify-content-between" href="/payments/export" ng-if="user_can('view', 'overdue')">
					Exports
					<i class="fas fa-rupee-sign mr-0"></i>
				</a>
				<a class="list-group-item px-0 justify-content-between" href="/payments/applications" ng-if="user_can('view', 'applications')">
					Applications
					<i class="fas fa-rupee-sign mr-0"></i>
				</a>
			</span>
			<span ng-if="user_can('edit', 'himanshu')">
				<a class="list-group-item px-0 justify-content-between" href="/sales/new" ng-if="user_can('edit', 'himanshu')">
					New
					<i class="fas fa-shopping-cart mr-0"></i>
				</a>
			</span>
		</ul>
	</div>
	<main ng-show="initialized">
		<div ng-view>
		</div>
	</main>
	<div class="layout-overlay">
		<div class="loader hidden">
			<!-- <svg class="circular" viewBox="25 25 50 50">
				<circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="5" stroke-miterlimit="10"/>
			</svg> -->
		</div>
	</div>
	<div class="batbar-container">
		<div class="batbar d-none">
			<div class="content"></div>
			<div class="control">
				<div class="d-flex flex-row-reverse"></div>
				<button class="btn btn-primary btn-close">Close</button>
			</div>
		</div>
	</div>
	<!-- <iframe src="" height="400" width="400" id="chatWindow" class="position-fixed hidden border" style="z-index:100;"></iframe> -->
</body>
</html>