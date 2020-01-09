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

	// Define custom routes
	global $jaws_routes;
	$jaws_routes = [];

	// COMMON & SYSTEM VIEWS
	// -------------------------------

	if (!isset($_SERVER["HTTP_HOST"])) {
		$_SERVER["HTTP_HOST"] = "www.jigsawacademy.com";
	}

	if ($_SERVER["HTTP_HOST"] == "www.jigsawacademy.com" || $_SERVER["HTTP_HOST"] == "jigsawacademy.com") {
		// Common System Pages
		$jaws_routes[""] = "view/backend/dash/index";	// Default page / Home page - JAWS DASH
		$jaws_routes["404"] = "view/common/404";		// 404 page

		// Developement & Testing Pages - Disable when live
		$jaws_routes["test"] = "view/misc/test";
		$jaws_routes["test-h"] = "view/misc/test-h";
		$jaws_routes["test-sr"] = "view/misc/test-sr";

		// Hybrid Auth
		$jaws_routes["hybridauth"] = "view/hybridauth/router";
		$jaws_routes["socialauth"] = "view/hybridauth/auth";
		$jaws_routes["dologin"] = "view/hybridauth/soc.login.proc";
		$jaws_routes["doreg"] = "view/hybridauth/soc.reg.proc";
		$jaws_routes["doassoc"] = "view/hybridauth/soc.assoc.proc";
		$jaws_routes["docreate"] = "view/hybridauth/soc.create.proc";
		// Hybrid Auth - Test
		$jaws_routes["hybridauth-new"] = "view/hybridauth/router.new";

		// Batcave
		$jaws_routes["batcave/*"] = "view/backend/batcave/index";

		// EventAPI
		$jaws_routes["event"] = "eventapi/event";

		// NaukriAPI
		$jaws_routes["naukri.api"] = "webapi/naukri.leads";

		// Shuriken
		$jaws_routes["shuriken.js"] = "shuriken/shuriken.js";
		$jaws_routes["shuriken.gif"] = "shuriken/shuriken.gif";

		// Shuriken - Test
		$jaws_routes["shuriken.test.js"] = "shuriken/shuriken.test.js";
		$jaws_routes["shuriken.test.gif"] = "shuriken/shuriken.test.gif";

		// Dev Login
		$jaws_routes["login.dev"] = "view/backend/temp/user.login.dev";

		// FRONT END VIEWS
		// -------------------------------

		// Payment
		$jaws_routes["pay"] = "view/frontend/payment/paylink.parse";
		$jaws_routes["pay/success"] = "view/frontend/payment/paylink.confirm";

		// User
		$jaws_routes["setupaccess"] = "view/frontend/user/lms.setup";

		// Futureskill - Genpact integration setup page
		$jaws_routes["access_setup"] = "view/frontend/jlc/access_setup";

		// DSB Employees
		$jaws_routes["dsb"] = "view/frontend/dsb/dsb";
		$jaws_routes["dsb/pay"] = "view/frontend/dsb/dsb.paylink.parse";
		$jaws_routes["dsb/pay/success"] = "view/frontend/dsb/dsb.paylink.confirm";

		// HSBC Employees
		$jaws_routes["hsbc"] = "view/frontend/hsbc/hsbc";
		$jaws_routes["hsbc/pay"] = "view/frontend/hsbc/hsbc.paylink.parse";
		$jaws_routes["hsbc/pay/success"] = "view/frontend/hsbc/hsbc.paylink.confirm";

		// Nokia employees
		$jaws_routes["nokia"] = "view/frontend/nokia/nokia";
		$jaws_routes["nokia/pay"] = "view/frontend/nokia/nokia.paylink.parse";
		$jaws_routes["nokia/pay/success"] = "view/frontend/nokia/nokia.paylink.confirm";

		// WIPRO Employees
		$jaws_routes["wipro"] = "view/frontend/wipro/wipro";
		$jaws_routes["wipro/pay"] = "view/frontend/wipro/wipro.paylink.parse";
		$jaws_routes["wipro/pay/success"] = "view/frontend/wipro/wipro.paylink.confirm";

		// IPBA thingy
		$jaws_routes["ipba"] = "view/frontend/ipba/ipba";
		$jaws_routes["ipba/pay"] = "view/frontend/ipba/ipba.paylink.parse";
		$jaws_routes["ipba/pay/success"] = "view/frontend/ipba/ipba.paylink.confirm";

		// University of Chicago
		$jaws_routes["uc/pay"] = "view/frontend/uc/uc.paylink.parse";
		$jaws_routes["uc/pay/success"] = "view/frontend/uc/uc.paylink.confirm";

		// EPBA course apply form checkout
		$jaws_routes["epba/pay"] = "view/frontend/epba/epba.paylink.parse";
		$jaws_routes["epba/pay/success"] = "view/frontend/epba/epba.paylink.confirm";

		// WNS Employees
		$jaws_routes["wns-nominations"] = "view/frontend/wns/wns";
		$jaws_routes["wns/success"] = "view/frontend/wns/success";

		// Corp Social account creation and linking
		$jaws_routes["social"] = "view/frontend/corp/social";

		// GENPACT Employees
		/*$jaws_routes["genpact-adss"] = "view/frontend/genpact/genpact-nov16";
		$jaws_routes["genpact-fsas"] = "view/frontend/genpact/genpact-nov16";
		$jaws_routes["genpact/pay"] = "view/frontend/genpact/genpact-nov16.paylink.parse";
		$jaws_routes["genpact/pay/success"] = "view/frontend/genpact/genpact-nov16.paylink.confirm";
		$jaws_routes["genpact-enroll"] = "view/frontend/genpact/genpact";
		$jaws_routes["genpact-usd"] = "view/frontend/genpact/genpact-usd";*/

		// AFB Free Trial
		$jaws_routes["free.jlc"] = "view/frontend/jlc/free/setup";
		$jaws_routes["free-jlc"] = "view/frontend/jlc/free/setup.new";

		// JLC Referral
		$jaws_routes["jlc.referral.stats"] = "view/frontend/jlc/referral/stats";
		$jaws_routes["refer"] = "lmsapi/jlc.refer/stats";

		// URL Redirecters
		$jaws_routes["redir"] = "view/frontend/redir/ctr";
		$jaws_routes["phone"] = "view/frontend/redir/phone";
		$jaws_routes["phone.verify"] = "view/frontend/redir/phone.otp";

		// FABRIC
		// -------------------------------
		$jaws_routes["fabric"] = "view/fabric/index";

		// BACK END VIEWS
		// -------------------------------

		// Temp
		$jaws_routes["kform"] = "view/backend/temp/paylink.create";
		$jaws_routes["kformalt"] = "view/backend/temp/paylink.create.wsubs";
		$jaws_routes["lform"] = "view/backend/temp/leads.basic.get";
		$jaws_routes["leads.export"] = "view/backend/temp/leads.export";
		$jaws_routes["qform"] = "view/backend/temp/user.survey.get";
		$jaws_routes["slkform"] = "view/backend/temp/paylink.report.get";
		$jaws_routes["search"] = "view/backend/temp/user.get";
		$jaws_routes["logout"] = "view/backend/temp/user.logout";
		$jaws_routes["moblog"] = "view/backend/temp/mobapp.log.get";
		$jaws_routes["mobnotify"] = "view/backend/temp/mobapp.notification";
		$jaws_routes["mobjobs"] = "view/backend/temp/mobapp.jobs";
		$jaws_routes["enrform"] = "view/backend/temp/enrollment.report.get";
		$jaws_routes["follow"] = "view/backend/temp/user.follow";
		$jaws_routes["lab"] = "view/backend/temp/lab.users.get";
		$jaws_routes["sis"] = "view/backend/temp/sis.get";
		$jaws_routes["enrollments"] = "view/backend/temp/students.get";
		$jaws_routes["students"] = "view/backend/temp/enroll.list";
		$jaws_routes["referrals"] = "view/backend/temp/jlc.referral.list";
		$jaws_routes["referralslist"] = "view/backend/temp/refer_friend/angular.referral.list";
		$jaws_routes["corpform"] = "view/backend/temp/wipro";
		$jaws_routes["hsbcform"] = "view/backend/temp/hsbc";
		$jaws_routes["alumni"] = "view/backend/temp/alumni.get";
		$jaws_routes["permissions.test"] = "view/backend/temp/permissions.test";
		$jaws_routes["track"] = "view/backend/temp/support.track";
		$jaws_routes["track/edit"] = "view/backend/temp/support.track.edit";
		$jaws_routes["numbers"] = "view/backend/temp/exotel.number.info";
		$jaws_routes["livechat"] = "view/backend/temp/livechat.list";
		$jaws_routes["iot.free.trial"] = "view/backend/temp/iot.free.trial";
		$jaws_routes["paypal"] = "view/backend/temp/paypal";
		$jaws_routes["call.me"] = "view/backend/temp/call.me";

		// Dashtemp
		$jaws_routes["kform2"] = "view/backend/dashtemp/package.create";
		$jaws_routes["aform"] = "view/backend/dashtemp/package.approve";
		$jaws_routes["package.create.embed"] = "view/backend/dashtemp/package.create.embed";

		// Shuriken Tag Manager
		$jaws_routes["tagmanager"] = "view/backend/temp/shuriken/tagmgr/shuriken.tagmgr.container";
		$jaws_routes["shuriken/tagmgr"] = "view/backend/temp/shuriken/tagmgr/shuriken.tagmgr.container";
		$jaws_routes["shuriken/tagmgr/containers"] = "view/backend/temp/shuriken/tagmgr/shuriken.tagmgr.container";
		$jaws_routes["shuriken/tagmgr/tags"] = "view/backend/temp/shuriken/tagmgr/shuriken.tagmgr.tag";

		// Tourian Routes
		$jaws_routes['tourian/add'] = "view/backend/temp/tourian.create";
		$jaws_routes['tourian/url'] = "view/backend/temp/tourian.urlform";
		$jaws_routes['tourian/list'] = "view/backend/temp/tourian.list";
		$jaws_routes['tourian/get'] = "view/backend/temp/tourian.get";

		// IOT ROUTES
		// -------------------------------
		$jaws_routes["iot"] = "view/iot/v2/iot";
		$jaws_routes["iot/home"] = "view/iot/v2/iot";
		$jaws_routes["iot/iot"] = "view/iot/v2/iot";
		$jaws_routes["iot/iot-courses"] = "view/iot/v2/iot-courses";
		$jaws_routes["iot/iot-beginners-course"] = "view/iot/v2/iot-beginners-course";
		$jaws_routes["iot/iot-using-arduino"] = "view/iot/v2/iot-using-arduino";
		$jaws_routes["iot/iot-using-raspberry-pi"] = "view/iot/v2/iot-using-raspberry-pi";
		$jaws_routes["iot/iot-cloud"] = "view/iot/v2/iot-cloud";
		$jaws_routes["iot/iot-career"] = "view/iot/v2/iot-career";
		$jaws_routes["iot/thankyou"] = "view/iot/v2/thankyou";
		$jaws_routes["iot/faqs"] = "view/iot/v2/faqs";
		$jaws_routes["iot/iot-terms"] = "view/iot/v2/iot-terms";
		$jaws_routes["iot/introduction-to-iot-analytics"] = "view/iot/v2/iot-analytics";
		$jaws_routes["iot/data-science-for-iot"] = "view/iot/v2/iot-data-science";
		$jaws_routes["iot/advanced-iot-analytics"] = "view/iot/v2/iot-advance-analytics";
		$jaws_routes["iot/sitemap.xml"] = "view/iot/v2/sitemap";
		$jaws_routes["iot/images-sitemap.xml"] = "view/iot/v2/images-sitemap";
		$jaws_routes["iot/robots.txt"] = "view/iot/v2/robots";
		$jaws_routes["iot/google845ac1fc57312367.html"] = "view/iot/v2/google845ac1fc57312367.html";
		$jaws_routes["iot/iot-javascript"] = "view/iot/v2/iot-javascript";
		// IoT 404 page
		$jaws_routes["iot/404"] = "view/iot/v2/404";

		// LP Routes
		$jaws_routes["idm-thankyou"] = "view/lp/idm/thankyou";

		// Mobile App APIs
		$jaws_routes["api/v1/user/login"] = "mobapi/v1/user/login";
		$jaws_routes["api/v1/user/update"] = "mobapi/v1/user/update";
		$jaws_routes["api/v1/courses/catalogue"] = "mobapi/v1/courses/catalogue.get";
		$jaws_routes["api/v1/courses/topics/progress.update"] = "mobapi/v1/courses/topics/progress.update";
		$jaws_routes["api/v1/home/menu/sequence"] = "mobapi/v1/home/menu/sequence";
		$jaws_routes["api/v1/faq"] = "mobapi/v1/home/faq.get";
		$jaws_routes["api/v1/jobs/get"] = "mobapi/v1/jobs/get";
		$jaws_routes["api/v1/webinars"] = "mobapi/v1/webinars/index";
		$jaws_routes["api/v1/webinars/reg"] = "mobapi/v1/webinars/reg";
		$jaws_routes["api/v1/refer"] = "mobapi/v1/refer/index";
		// 2nd version APIs
		$jaws_routes["api/v1/user/auth"] = "mobapi/v1/user/auth";
		$jaws_routes["api/v1/subs"] = "mobapi/v1/subs/get";
		$jaws_routes["api/v1/courses/topics"] = "mobapi/v1/courses/topics/all";

		// Rubric routes
		$jaws_routes["api/v1/rubric/email.send"] = "rubric/email.send";

		// Secret Santa
		$jaws_routes["secret-santa"] = "view/backend/temp/secret.santa";

		$jaws_routes["leads/new"] = "aflapi/lead";
		// CRM APIs
		$jaws_routes["leads/capture"] = "crmapi/leadsquared/new";
		$jaws_routes["leads/reassign"] = "crmapi/leadsquared/reassign";
		$jaws_routes["leads/task"] = "crmapi/leadsquared/task";

		// LinkedIn OAuth
		$jaws_routes["auth/linkedin"] = "view/backend/temp/linkedin.auth";

		// Smart routes
		$jaws_routes["lab/*"] = "view/frontend/lab/router";

		// MCube Callback Capture Api
		$jaws_routes["mcube-response"] = "webapi/mcube.response";

	}
	else if ($_SERVER["HTTP_HOST"] == "batcave.jigsawacademy.com") {

	}
	else if ($_SERVER["HTTP_HOST"] == "chat.jigsawacademy.com") {

		$jaws_routes["api/v1/init"] = "chatapi/v1/init";
		$jaws_routes["api/v1/post"] = "chatapi/v1/post";
		$jaws_routes["api/v1/response"] = "chatapi/v1/response";

	}

?>
