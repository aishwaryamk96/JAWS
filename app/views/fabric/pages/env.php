<header class="container-fluid bg-primary text-read" ng-class="{'extend':env.header.ext.default, 'no-extend': !env.header.ext.allow}">

	<!-- HEADER MAIN --><!-- !! -->
	<div ng-include src="app.jawsPathViews + 'sections/header-main' + app.jawsDevAppend" include-replace></div>

	<!-- HEADER EXT --><!-- !! -->
	<div ui-view="ext"></div>

	<div class="shadow"></div>
	<div class="notification pad-15 v-align text-center bg-warning text-warning">
		<span>This is just a notification</span>
		<i class="ion-close pull-right pad-row-4"></i>
	</div>

	<!-- COURSE MENU--><!-- !! -->
	<div ng-include src="app.jawsPathViews + 'sections/course-menu' + app.jawsDevAppend" include-replace></div>

	<!-- NAV MENU--><!-- !! -->
	<div ng-include src="app.jawsPathViews + 'sections/nav-menu' + app.jawsDevAppend" include-replace></div>
</header>

<!-- PAGE --><!-- !! -->
<div ui-view="page"></div>

<!-- TOPIC --><!--!! -->
<div ui-view="topic"></div>

<!-- PIP --><!-- !! -->
