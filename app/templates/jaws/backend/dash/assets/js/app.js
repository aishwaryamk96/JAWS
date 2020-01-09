/* ============================================================
 * File: app.js
 * Configure global module dependencies. Page specific modules
 * will be loaded on demand using ocLazyLoad
 * ============================================================ */

'use strict';

angular.module('jaws', [
    'ui.router',
    'ui.utils',
    'oc.lazyLoad',
	'ngAnimate'
]);
