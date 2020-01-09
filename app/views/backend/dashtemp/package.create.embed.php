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
	
	//Load Stuff
	load_module("ui");
	load_module("user");
	load_module("course");
	load_module("subs");
	load_library("persistence");
	load_library("setting");
	
	// define package id
	$package_id = null;
	if(!empty($_GET["package"])){
		$package_id = $_GET["package"];
	}
	
	$confirm_payment_access = false;
	
 	//Check
	if ((isset($_GET["token"])) && (isset($_GET["email"]))) {
		//Auth Token Check
		$auth = psk_info_get($_GET["token"]);

		if (($auth === false) || (strcmp($auth["action"], "crm") != 0)) {
			ui_render_msg_front(array(
						"type" => "error",
						"title" => "Jigsaw Academy",
						"header" => "No Tresspassing",
						"text" => "Sorry, but you do not have permission to access this embedded page.<br/>Contact Jigsaw Development team for assistance."
					));
		
			exit();
		}
		if (is_persistent(array("layer" => "dynpepl", "type" => "package", "id" => $_GET["package"]))){
			$_GET["package"] = get_native_id(array("layer" => "dynpepl", "type" => "package", "id" => $_GET["package"]))["id"];
		}

	}
	else if (auth_session_is_logged()) {
        if (auth_session_is_allowed("package.view")) { 
			$auth["entity_id"] = $_SESSION["user"]["user_id"]; 
		}
		
        if (auth_session_is_allowed("payment.instl.confirm")){
			$confirm_payment_access = true;
		}
	}
	else {	
		ui_render_msg_front(array(
					"type" => "error",
					"title" => "Jigsaw Academy",
					"header" => "No Tresspassing",
					"text" => "Sorry, but you do not have permission to access this embedded page.<br/>Contact Jigsaw Development team for assistance."
		   ));

		exit();
	}

	//Prep
	setlocale(LC_MONETARY, 'en_IN');

	// Parse Lead email
	$user = user_get_by_email(urldecode($_GET["email"]));

	// Check User Details
	/* if ($user === false) {
		if ((!isset($_GET["name"])) || (!isset($_GET["phone"]))) {
		ui_render_msg_front(array(
					"type" => "error",
					"title" => "Jigsaw Academy",
					"header" => "Incomplete Information",
					"text" => "Please provide lead's name and phone.<br/>Contact Jigsaw Development team for assistance."
		   ));

		exit();
		}
	} */
	
	if ((!isset($_GET["email"])) || (!isset($_GET["name"])) || (!isset($_GET["phone"]))) {
		ui_render_msg_front(array(
					"type" => "error",
					"title" => "Jigsaw Academy",
					"header" => "Incomplete Information",
					"text" => "Please provide lead's name and phone.<br/>Contact Jigsaw Development team for assistance."
		   ));

		exit();
	}
	
	// agent discount allowed
	$agent_discount_max = user_content_get($auth["entity_id"],'discount_max');
	$agent_default_discount_max = setting_get('payment.discount_max.default');
	
	// get bundle details
	// 33 is business analyst - amrketing, not enabled till now.
	$sql = "SELECT * FROM course_bundle WHERE bundle_type = 'specialization' AND status <> 'disabled' AND combo <> '' AND bundle_id NOT IN ('33')";
	$bundles = db_query($sql);

	// Load package
	$package;
	if (isset($_GET["package"]))
		$package = package_get($_GET["package"]);

	// Package Creator Type Check
	if(!empty($package)){

		$package['instl'] = json_decode($package['instl'],true);
		
		$package_serialized = json_decode($package['serialized'],true);
		
		$package['data_courses_actual'] 	= $package_serialized['data_courses_actual'];
		$package['data_courses_combo'] 		= $package_serialized['data_courses_combo'];
		$package['data_courses_discount'] 	= $package_serialized['data_courses_discount'];
		$package['data_payment_discount'] 	= $package_serialized['data_payment_discount'];
		$package['data_tax_amount'] 		= $package_serialized['data_tax_amount'];
		$package['data_discount_amount']	= $package_serialized['data_discount_amount'];
		$package['data_offered_amount'] 	= $package_serialized['data_offered_amount'];
		$package['data_net_payable'] 		= $package_serialized['data_net_payable'];
		
		$package['data_edit_offered_price'] = $package_serialized['data_edit_offered_price'];
		$package['data_edit_discount_amount'] = $package_serialized['data_edit_discount_amount'];
		$package['data_edit_discount_percent'] = $package_serialized['data_edit_discount_percent'];
		$package['data_edit_tax_amount'] = $package_serialized['data_edit_tax_amount'];
		$package['data_bundle_price'] = $package_serialized['data_bundle_price'];
		$package['data_bundle_combo'] = $package_serialized['data_bundle_combo'];
		
		// remove bundled courses from combo
		if( !empty($package["bundle_id"]) ){
			$package['combo'] = str_replace($package['data_bundle_combo'], "", $package['combo']);
			$package['sum_basic'] = $package["sum_basic"] - $package['data_bundle_price'];
		}
		
		$package_comments = json_decode($package['creator_comment'],true);
		
		$package_courses = explode(';',$package['combo']);
		$package_complimentary = explode(';',$package['combo_free']);
		
		$instalment = $package_serialized['data_instalment_amount'];
		$instalment_inr = $package_serialized['data_instalment_fees_inr'];
		$instalment_usd = $package_serialized['data_instalment_fees_usd'];
		
		if($package['currency'] == 'usd'){
			setlocale(LC_MONETARY, 'en_US');
		}
		
		// errors in package
		$package_errors = json_decode($package['approval_require_comment'],true);
		
		// update the user
		$user["email"]		= $package['email'];
		$user["name"] 		= $package['name'];
		$user["phone"] 		= $package['phone'];
		
		$_GET["email"]		= $package['email'];
		$_GET["name"] 		= $package['name'];
		$_GET["phone"] 		= $package['phone'];
		
		// if package is executed then only payment section will be created.then only instalment section will be visible.
		// to get payment details, first get details from subs table using package and user_id. there will be payid as well.
		// from the payid get payment details for the specific package.
		// get subs details
		$sql = "SELECT * FROM subs WHERE package_id = '" . (int)$package["package_id"] . "'";
		$subs = db_query($sql);
		$package_instl = payment_get_info($subs[0]["pay_id"]);
		
	} else {
		$package_instl = array();
		$package_errors = array();
		$package_courses = array();
		$package_complimentary = array();
		$package_comments = array();
		$instalment = null;
		$instalment_inr = null;
		$instalment_usd = null;
	}
	// Package Status = draft, pending, approved, executed, rejected
	
	// prep default data
	$allowed_status = array('draft');
	$approved_sm = false;
	$approved_pm = false;	
	$instl_criteria = setting_get("payment.instl.criteria");
	$tax_rate = json_decode(setting_get("payment.tax.percentage"),true);
	$instalment_fees = json_decode(setting_get("payment.instl.fee"),true);
	$instalment_date = setting_get("payment.instl.due.days");
	
	// Load courses
	$courses = course_get_info_all();
	
	// order the courses
	/* $order = array(
		'Analytics for Beginners',
		'Web Analytics',
		'Text Mining with R'
	);
	
	$new = array();
	
	foreach( $order as &$ord ){
		foreach($courses as $key => $course){
			if($course['name'] == $ord){
				array_push($new,$course);
				unset($courses[$key]);
			}
		}
	} unset($ord);
	
	$courses_ordered = array_merge( (array)$new, (array)$courses ); */

?>
<!doctype html>
<html lang="en">
  	<head>
		<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
		<meta content="utf-8" http-equiv="encoding">
	    <title>JAWS - New Subscription</title>
		<meta name="author" content="BadGuppy">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<meta name="description" content="JAWS v2.0">
		<link rel="icon" type="image/png" href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL.'/media/jaws/frontend/images/favicon.png'; ?>">

	    	<!-- Stylesheets -->
		<link rel="stylesheet/less" type="text/css" href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL."/app/templates/jaws/backend/dashtemp/theme.light.less"; ?>" />
		<link rel="stylesheet/less" type="text/css" href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL."/app/templates/jaws/backend/dashtemp/layout.less"; ?>" />

		<!-- Fonts -->
		<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL.'/common/fa/css/font-awesome.css'; ?>">

		<!-- Libraries -->
		<script   src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
		<script src="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL.'/common/less.js/less.min.js'; ?>" data-env="development"></script>

		<!-- DashMIN -->
		<script src="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL."/app/templates/jaws/backend/dashtemp/dash.js"; ?>"></script>

		<!-- Page Specific -->
		<script src="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL."/app/templates/jaws/backend/dashtemp/subs.create.js"."?tme=".time(); ?>"></script>

  </head>
  <body data-tourian="false">
  
		<div class="wrapper">

			<div id="main-container" class="<?php echo (!empty($package) && !in_array($package['status'],$allowed_status)) ? "read-only" : ''; ?>">	<!-- SET CLASS to read-only to disable editing -->
				<div class="wrapper" style="padding: 25px;">

					<div class="section-boxed">
<!-- Users Panel: Start -->
						<div class="accordian-panel pin has-subpanel" id="user-info">

							<div class="panel-tab">
								<i class="fa fa-user fa-lg fa-fw"></i>
							</div>

							<div class="panel-main">
								<div class="content-min overflow" style="opacity: 1; pointer-events: auto; padding-right: 0px;">
									
									<div class="section-col" style='margin-right: 10px;'><div class="user-pic" style="background-image: url('<?php echo ($user !== false) ? $user["photo_url"] : $_SERVER["server_name"]."/".JAWS_PATH_LOCAL.'/media/jaws/backend/user-default.png'; ?>');"></div></div>
									<div class="section-col">
										<!-- <input style="width: 255px;" type="email" class="disable" placeholder="Email" id="txt-email" value="<?php //echo ($user !== false) ? $user["email"] : urldecode($_GET["email"]); ?>" required disabled="disabled"/> -->
										<input style="width: 255px;" type="email" class="disable" placeholder="Email" id="txt-email" value="<?php echo urldecode($_GET["email"]); ?>" required disabled="disabled"/>
									</div>
									<div class="section-col">
										<!-- <input style="width: 170px;" type="text" class="disable" required placeholder="Name" id="txt-name" value="<?php //echo ($user !== false) ? $user["name"] : $_GET["name"]; ?>" class="disable"disabled="disabled"/> -->
										<input style="width: 170px;" type="text" class="disable" required placeholder="Name" id="txt-name" value="<?php echo urldecode($_GET["name"]); ?>" class="disable"disabled="disabled"/>
									</div>
									<div class="section-col">
										<!-- <input style="width: 110px;" type="text" class="disable" required maxlength="15" placeholder="Phone" id="txt-phone" value="<?php //echo ($user !== false) ? $user["phone"] : $_GET["phone"]; ?>" class="disable"disabled="disabled"/> -->
										<input style="width: 110px;" type="text" class="disable" required maxlength="15" placeholder="Phone" id="txt-phone" value="<?php echo urldecode($_GET["phone"]); ?>" class="disable"disabled="disabled"/>
									</div>
								</div>
							</div>						

							<div class="panel-sub">
								<div class="content">
									Currency
									<div class="accordian-panel-select" onclick="javascript:updateCurrency();" style="cursor: pointer;" id="select-currency">
										<b class="selected currency"><?php echo ( !empty($package['currency']) && $package['currency'] == 'inr' ) ? 'Indian Rupees' : ( ( !empty($package['currency']) && $package['currency'] == 'usd') ? 'US Dollar' : 'Indian Rupees' ); ?></b>
										<i class="fa fa-fw fa-lg fa-exchange"></i>
									</div>
								</div>
							</div>	

						</div>
<!-- Users Panel: End -->
<!-- Specializations Panel: Start -->
						<div class="accordian-panel pin has-subpanel">

							<div class="panel-tab">
								<i class="fa fa-user fa-lg fa-fw"></i>
							</div>

							<div class="panel-main">
								<div class="content-min overflow" style="opacity: 1; pointer-events: auto; padding-right: 0px;">

									<div class="section-col" style="display:inline;">
										Specialization : 
										<select name="specialization" onchange="javascript:selectSpecialization();">
											<option data-courses="" data-price-inr="0" data-price-usd="0" value="">No Specialization Selected</option>
											<?php foreach($bundles as $bundle) { ?>
											<option data-courses="<?php echo $bundle["combo"]; ?>" data-price-inr="<?php echo $bundle["price_inr"]; ?>" data-price-usd="<?php echo $bundle["price_usd"]; ?>" value="<?php echo $bundle["bundle_id"]; ?>" <?php echo ( !empty($package["bundle_id"]) && $bundle["bundle_id"] == $package["bundle_id"] ) ? 'selected="selected"' : ''; ?> ><?php echo ucwords($bundle["name"]); ?></option>
											<?php } ?>
										</select>
										<div class="info-box-container spec-courses"></div>
									</div>

								</div>
							</div>						

							<div class="panel-sub">
								<div class="content">
									<div>Specialization Price<br/>
										<b id="specialization-price" data-specialization-price="<?php echo (!empty($package['data_bundle_price'])) ? $package['data_bundle_price'] : 0; ?>"><?php echo (!empty($package['data_bundle_price'])) ? money_format('%.0n',$package['data_bundle_price']) : money_format('%.0n',0); ?> </b>
									</div>
								</div>
							</div>	

						</div>
<!-- Specializations Panel: End -->
<!-- Courses Panel: Start -->
						<div class="accordian-panel min has-subpanel">

							<div class="panel-tab">
								<i class="fa fa-chevron-down fa-lg fa-fw"></i>
								<div class="panel-title">Courses</div>
							</div>

							<div class="panel-main">
								<div class="content-min">
									<div id="courses-info" class="info-box-container">	
										<div>Courses : <strong style="color:black;">None</strong></div>	
										
									</div>
								</div>
								<div class="content">						
									<div id="courses" class="course-container">
										<div class="course-row">
										<?php $i = 1;
											foreach($courses as &$course) {
												$meta = json_decode($course['meta']['content'],true);
												// il_price_inr = premium price in inr/ live mode
												// sp_price_inr = regular price in inr/ video mode
												// course combo = course_id,mode;course_id,mode;...
												// mode = 1 is premium, 2 is regular;
										?>
											<div class="course <?php if(!empty($package_courses) && (in_array($course['course_id'].',1',$package_courses) || in_array($course['course_id'].',2',$package_courses) )){ echo 'active '; if(in_array($course['course_id'].',1',$package_courses)){ echo 'live'; } else { echo 'video'; } } ?>" data-course-id="<?php echo $course['course_id']; ?>" data-combo_hierarchy="<?php echo $course['combo_hierarchy']; ?>">
												<div class="title" data-il-code="<?php echo $course['il_code']; ?>" data-sp-code="<?php echo $course['sp_code']; ?>">
													<p>
														<?php echo $course['name']; ?>
														<span class="desc">
															<br/>Pre-requisites: <?php echo mb_strimwidth($meta['prerequisite'], 0, 70, "..."); ?>
															<br/>Tools: <?php echo mb_strimwidth($meta['tools'], 0, 32, "..."); ?>
														</span>
													</p>
												</div>
												<?php if ( $course['il_status_inr'] == 1 || $course['il_status_usd'] == 1 ) { ?>
												<div class="mode live <?php if(!empty($package_courses) && in_array($course['course_id'].',1',$package_courses)){ echo 'active'; } ?>" data-mode="<?php echo $course['course_id'].','.'1'; ?>" data-il_price_inr="<?php echo $course['il_price_inr']; ?>" data-il_price_inr_alt="<?php echo $course['il_price_inr_alt']; ?>" data-il_price_usd="<?php echo $course['il_price_usd']; ?>" data-il_price_usd_alt="<?php echo $course['il_price_usd_alt']; ?>">
													<p>Premium<br/>
														<span class="price">
														<?php if($package['currency'] == 'usd'){
																echo money_format('%.0n', $course['il_price_usd']);
															} else {
																echo money_format('%.0n', $course['il_price_inr']);
															} ?>
														</span>
													</p>
												</div>
												<?php } ?>
												<?php if ( $course['sp_status_inr'] == 1 || $course['sp_status_usd'] == 1 ) { ?>
												<div class="mode video <?php if(!empty($package_courses) && in_array($course['course_id'].',2',$package_courses)){ echo 'active'; } ?>" data-mode="<?php echo $course['course_id'].','.'2'; ?>" data-sp_price_inr="<?php echo $course['sp_price_inr']; ?>" data-sp_price_inr_alt="<?php echo $course['sp_price_inr_alt']; ?>" data-sp_price_usd="<?php echo $course['sp_price_usd']; ?>" data-sp_price_usd_alt="<?php echo $course['sp_price_usd_alt']; ?>">
													<p>Regular<br/>
														<span class="price">
														<?php if($package['currency'] == 'usd'){
															echo money_format('%.0n', $course['sp_price_usd']); 
														} else {
															echo money_format('%.0n', $course['sp_price_inr']);
														}
														?></span>
													</p>
												</div>
												<?php } ?>
											</div>

										<?php 
										if( $i % 2 == 0 ){
											echo '</div><div class="course-row">';  
										}
										$i++;	} unset($course);
										?>
										</div>
									</div>

								</div>
							</div>							

							<div class="panel-sub">
								<div class="content-min">
									<div>Combo Price<br/>
										<b id="courses-combo-min" data-combo-min="<?php echo (!empty($package['sum_basic'])) ? $package['sum_basic'] : 0; ?>"><?php echo (!empty($package['sum_basic'])) ? money_format('%.0n',$package['sum_basic']) : money_format('%.0n',0); ?> </b>
									</div>
								</div>
								<div class="content">							
									<div>Actual Price<br/>
										<b id="courses-actual" data-price="<?php echo (!empty($package['data_courses_actual'])) ? $package['data_courses_actual'] : 0; ?>"><?php echo (!empty($package['data_courses_actual'])) ? money_format('%.0n',$package['data_courses_actual']) : money_format('%.0n',0); ?> </b>
									</div><br/>
									<div>Discount<br/>
										<b id="courses-discount" data-discount="<?php echo (!empty($package['data_courses_discount'])) ? $package['data_courses_discount'] : 0; ?>"><?php echo (!empty($package['data_courses_discount'])) ? money_format('%.0n',$package['data_courses_discount']) : money_format('%.0n',0); ?> </b>
									</div><br/>
									<div>Combo Price<br/>
										<b id="courses-combo" data-combo="<?php echo (!empty($package['sum_basic'])) ? $package['sum_basic'] : 0; ?>"><?php echo (!empty($package['sum_basic'])) ? money_format('%.0n',$package['sum_basic']) : money_format('%.0n',0); ?> </b>
									</div><br/>
								</div>
							</div>	

						</div>
<!-- Courses Panel: End -->
<!-- Complimentary Courses Panel: Start -->
						<div class="accordian-panel min has-subpanel">

							<div class="panel-tab">
								<i class="fa fa-chevron-down fa-lg fa-fw"></i>
								<div class="panel-title">Complimentary</div>
							</div>

							<div class="panel-main">
								<div class="content-min">
									<div id="complimentary-info" class="info-box-container">	
										<div>Complimentary : <strong style="color:black;">None</strong></div>
									</div>
									<?php if(!empty($package_comments["combo_free"])){ ?>
										<i class="fa fa-lg fa-fw red fa-exclamation-triangle" title="<?php echo $package_comments["combo_free"]; ?>"></i>
									<?php } ?>
								</div>
								<div class="content">
									<div id="complimentary" class="course-container">
										<div class="course-row">
										<?php $i = 1;
											foreach($courses as &$complimentary) {
												$meta = json_decode($complimentary['meta']['content'],true);
												// il_price_inr = premium price in inr/ live mode
												// sp_price_inr = regular price in inr/ video mode
												// course combo = course_id,mode;course_id,mode;...
												// mode = 1 is premium, 2 is regular;
												// no price related calculations for complimentary courses as they are free.
										?>
											<div class="course <?php if(!empty($package_complimentary) && (in_array($complimentary['course_id'].',1',$package_complimentary) || in_array($complimentary['course_id'].',2',$package_complimentary) )){ echo 'active '; if(in_array($complimentary['course_id'].',1',$package_complimentary)){ echo 'live'; } else { echo 'video'; } } ?>" data-course-id="<?php echo $complimentary['course_id']; ?>" data-combo_hierarchy="<?php echo $complimentary['combo_hierarchy']; ?>">
												<div class="title" data-il-code="<?php echo $complimentary['il_code']; ?>" data-sp-code="<?php echo $complimentary['sp_code']; ?>">
													<p><?php echo $complimentary['name']; ?>
													<span class="desc">
														<br/>Pre-requisites: <?php echo mb_strimwidth($meta['prerequisite'], 0, 70, "..."); ?>
														<br/>Tools: <?php echo mb_strimwidth($meta['tools'], 0, 32, "..."); ?>
													</span>
													</p>
												</div>
												<?php if ( $complimentary['il_status_inr'] == 1 || $complimentary['il_status_usd'] == 1 ) { ?>
													<div class="mode live <?php if(!empty($package_complimentary) && in_array($complimentary['course_id'].',2',$package_complimentary)){ echo 'active'; } ?>" data-mode="<?php echo $complimentary['course_id'].','.'1'; ?>" data-il_price_inr="<?php echo $complimentary['il_price_inr']; ?>" data-il_price_inr_alt="<?php echo $complimentary['il_price_inr_alt']; ?>" data-il_price_usd="<?php echo $complimentary['il_price_usd']; ?>" data-il_price_usd_alt="<?php echo $complimentary['il_price_usd_alt']; ?>">
														<p>Premium<br/>
															<span class="price">
															<?php if($package['currency'] == 'usd'){
																echo money_format('%.0n', $complimentary['il_price_usd']); 
															} else {
																echo money_format('%.0n', $complimentary['il_price_inr']); 
															}
															?></span>
														</p>
													</div>
												<?php } ?>
												<?php if ( $complimentary['sp_status_inr'] == 1 || $complimentary['sp_status_usd'] == 1 ) { ?>
												<div class="mode video <?php if(!empty($package_complimentary) && in_array($complimentary['course_id'].',2',$package_complimentary)){ echo 'active'; } ?>" data-mode="<?php echo $complimentary['course_id'].','.'2'; ?>" data-sp_price_inr="<?php echo $complimentary['sp_price_inr']; ?>" data-sp_price_inr_alt="<?php echo $complimentary['sp_price_inr_alt']; ?>" data-sp_price_usd="<?php echo $complimentary['sp_price_usd']; ?>" data-sp_price_usd_alt="<?php echo $complimentary['sp_price_usd_alt']; ?>">
													<p>Regular<br/>
														<span class="price">
														<?php if($package['currency'] == 'usd'){
															echo money_format('%.0n', $complimentary['sp_price_usd']);
														} else {
															echo money_format('%.0n', $complimentary['sp_price_inr']);
														} ?></span>
													</p>
												</div>
												<?php } ?>
											</div>
										<?php 
										if( $i % 2 == 0 ){
											echo '</div><div class="course-row">';  
										}
										$i++;	} unset($complimentary);
										?>
										</div>
										<!-- <div class="course-row">
											<div class="section-col">
												<textarea name="comments_combo_free" cols="50" placeholder="Please provie reason for giving complimentary courses."><?php //echo (!empty($package_comments["combo_free"])) ? $package_comments["combo_free"] :""; ?></textarea>
											</div>
										</div> -->
									</div>									
								</div>
							</div>							

							<div class="panel-sub">
								<div class="content-min">
									<div>Complimentary Price<br/><b>FREE</b></div>
								</div>
								<div class="content">
									<div>Actual Price<br/><b>FREE</b></div><br/>
									<div>Complimentary Price<br/><b>FREE</b></div><br/>
								</div>
							</div>	

						</div>
<!-- Complimentary Courses Panel: End -->
<!-- Complimentary Courses Comments Panel: Start -->
						<div class="accordian-panel pin has-subpanel">
							<div class="panel-tab">
								<i class="fa fa-user fa-lg fa-fw"></i>
							</div>
							<div class="panel-main">
								<div class="content-min overflow" style="opacity: 1; pointer-events: auto; padding-right: 0px;">

									<div class="section-col" style="display:inline;">
										Complimentary Course Comments: <br/>
										<textarea name="comments_combo_free" cols="45" rows="1" placeholder="Please provie reason for giving complimentary courses."><?php echo (!empty($package_comments["combo_free"])) ? $package_comments["combo_free"] :""; ?></textarea>
									</div>
								</div>
							</div>
						</div>
<!-- Complimentary Courses Comments Panel: End -->
<!-- Payments Panel: Start -->
						<div class="accordian-panel min has-subpanel">
							<div class="panel-tab">
								<i class="fa fa-chevron-down fa-lg fa-fw"></i>
								<div class="panel-title">Payment</div>
							</div>

							<div class="panel-main">
								<div class="content-min">
									<div>Payment Mode : <b id="payment_mode_min">Online</b>
									<?php if(!empty($package_errors['pay_mode'])){ ?>
									<i class="fa fa-lg fa-fw red fa-exclamation-triangle" title="<?php echo $package_errors['pay_mode']; ?>"></i>
									<?php } ?>
									<?php if(!empty($package_errors['data_discount_amount'])){ ?>
									<i class="fa fa-lg fa-fw red fa-exclamation-triangle" title="<?php echo $package_errors['data_discount_amount']; ?>"></i>
									<?php } ?>
									<?php if(!empty($package_comments["discount"])){ ?>
									<i class="fa fa-lg fa-fw red fa-exclamation-triangle" title="<?php echo $package_comments["discount"]; ?>"></i>
									<?php } ?>
									</div>
								</div>
								<div class="content" style="min-height: 180px;">
									<div class="section-col">
										Payment Mode :
										<div class="accordian-panel-select">
											<select name="payment_mode">
												<option value="online" <?php echo (!empty($package['pay_mode']) && $package['pay_mode'] == 'online')? 'selected' : 'selected' ;?> >Online (via Payment Gateway)</option>
												<!--
												<option value="cash" <?php echo (!empty($package['pay_mode']) && $package['pay_mode'] == 'cash')? 'selected' : '' ;?> >Cash (Already Paid)</option>
												<option value="cheque" <?php echo (!empty($package['pay_mode']) && $package['pay_mode'] == 'cheque')? 'selected' : '' ;?> >Cheque (Already Paid)</option>
												<option value="dd" <?php echo (!empty($package['pay_mode']) && $package['pay_mode'] == 'dd')? 'selected' : '' ;?> >DD (Already Paid)</option>
												<option value="other" <?php echo (!empty($package['pay_mode']) && $package['pay_mode'] == 'other')? 'selected' : '' ;?> >Other (Already Paid)</option>
												-->
											</select>
										</div>
										<span class="red"><?php if(!empty($package_errors['pay_mode'])){ echo $package_errors['pay_mode']; } ?></span>
									</div>
									<div class="section-col payment_comment" <?php echo (!empty($package_comments['misc'])) ? '' : 'style="display:none;"'; ?>>
										Comment : 
										<input type="text" placeholder="Payment Comment" name="payment_comment" value="<?php echo (!empty($package_comments['misc'])) ? $package_comments['misc'] : ''; ?>" />
									</div>
									<div class="section-col" style="display:none;">
									<!-- will be enabled later. -->
										Discount :
										<input type="number" placeholder="Discount" name="total_discount" value="<?php echo (!empty($package['data_payment_discount'])) ? $package['data_payment_discount'] : ''; ?>" min="0" max="20" />
										<span class="red"><?php if(!empty($package_errors['data_discount_amount'])){ echo $package_errors['data_discount_amount']; } ?></span>
									</div>
									<div class="section-col">
										<textarea name="comments_discount" cols="50" placeholder="Please provie reason for giving discount."><?php echo (!empty($package_comments["discount"])) ? $package_comments["discount"] :""; ?></textarea>
									</div>
								</div>
							</div>							

							<div class="panel-sub">
								<div class="content-min">
									<div>Final Price<br/>
										<b id="actual_offered_price_min"><?php echo (!empty($package['sum_offered'])) ? money_format('%.0n',$package['sum_offered']) : money_format('%.0n',0); ?></b>
										<i class="fa fa-fw fa-lg fa-edit offered"></i>
									</div>
								</div>
								<div class="content pymt-sidebar">
									<div>
										Offered Price<br/>
										<b id="actual_offered_price_without_tax" data-actual_offered_price_without_tax="<?php echo (!empty($package['data_edit_offered_price'])) ? $package['data_edit_offered_price'] : 0; ?>"><?php echo (!empty($package['data_edit_offered_price'])) ? money_format('%.0n',$package['data_edit_offered_price']) : money_format('%.0n',0); ?></b>
										<i class="fa fa-fw fa-lg fa-edit offered" onclick="javascript:editOfferedPrice();"></i>
										<span id="editOfferedPrice" data-editOfferedPrice="<?php echo (!empty($package['data_edit_offered_price'])) ? $package['data_edit_offered_price'] : 0; ?>" style="display:none;"><input type="text" value="<?php echo (!empty($package['data_edit_offered_price'])) ? $package['data_edit_offered_price'] : 0; ?>" placeholder="Please provide appropriate value!" pattern="/[^\d]+/" /></span>
									</div><br/>
									<div id="text-tx-rt">Tax @ <?php echo $tax_rate['inr']; ?>%<br/>
										<b id="tax_amount">
											<?php if( $package['sum_basic'] == $package['data_edit_offered_price'] ){ 
												echo money_format('%.0n',$package['data_tax_amount']); 
											} else if( $package['sum_basic'] != $package['data_edit_offered_price'] ) { 
												echo money_format('%.0n',$package['data_edit_tax_amount']); 
											} else { 
												echo money_format('%.0n',0); 
											} ?>
										</b>
									</div><br/>
									<div>Additional Discount<br/>
										<b id="total_discount" data-discount-amount="<?php echo (!empty($package['data_discount_amount'])) ? $package['data_discount_amount'] : 0; ?>"  data-discount-percent="<?php echo (!empty($package['data_edit_discount_percent'])) ? $package['data_edit_discount_percent'] : 0; ?>">- 
											<?php if(!empty($package['data_discount_amount'])){
												echo money_format('%.0n',$package['data_discount_amount']);
											} else if(!empty($package['data_edit_discount_amount'])) {
												echo money_format('%.0n',$package['data_edit_discount_amount']) . " " ."(" . $package['data_edit_discount_percent'] . "%)";
											} else {
												echo money_format('%.0n',0);
											} ?>
										</b>
									</div><br/>									
									<div>
										Final Price<br/>
										<b id="actual_offered_price" data-actual_offered_price="<?php echo (!empty($package['sum_offered'])) ? $package['sum_offered'] : 0; ?>"><?php echo (!empty($package['sum_offered'])) ? money_format('%.0n',$package['sum_offered']) : money_format('%.0n',0); ?></b>
									</div><br/>
								</div>
							</div>	

						</div>
<!-- Payments Panel: End -->
<!-- Installments Panel: Start -->
						<div class="accordian-panel min has-subpanel ovrflw">

							<div class="panel-tab">
								<i class="fa fa-chevron-down fa-lg fa-fw"></i>
								<div class="panel-title">Installment </div>
							</div>

							<div class="panel-main">
								<div class="content-min">
									<div id="instalments-info" class="info-box-container">	
										<div>Installments :<b> None</b>
										</div>
									</div>
									<?php if(!empty($package_errors['instl_total'])){ ?>
									<i class="fa fa-lg fa-fw red side fa-exclamation-triangle" title="<?php echo $package_errors['instl_total']; ?>"></i>
									<?php } ?>
									<?php if(!empty($package_comments["instl"])){ ?>
									<i class="fa fa-lg fa-fw red side fa-exclamation-triangle" title="<?php echo $package_comments["instl"]; ?>"></i>
									<?php } ?>
								</div>
								<div class="content">
									<div class="instl-container">
									<?php if(!empty($package['instl']) && $package['instl_total'] > 0 ) { $i = 1; for( $i; $i <= $package['instl_total']; $i++ ){ ?>
										<div id="box<?php echo $i; ?>" class="instl <?php echo ($i == 1) ? 'fst' : ''; ?> <?php echo (!empty($package_instl["instl"][$i])) ? strtolower($package_instl["instl"][$i]["status"]) : ""; ?>">
											<?php if( $i != 1 ){ ?>
											<i onclick="javascript:removeInstalment2($(this));" class="fa fa-fw fa-lg fa-close"></i>
											<?php } ?>
											<i onclick="javascript:editInstalment2($(this));" class="fa fa-fw fa-lg fa-edit sum"></i>
											<?php if( $i != 1 ){ ?>
											<i onclick="javascript:editDate($(this));" class="fa fa-fw fa-lg fa-edit date"></i>
											<?php } ?>

											<div class="separater top"></div>
											<div class="separater bottom"></div>

											<div class="count">
												<?php if( $i == 1 ){ ?>Down Payment
												<?php } elseif( $i == 2 ){ ?>2<sup>nd</sup> Instalment
												<?php } elseif( $i == 3 ){ ?>3<sup>rd</sup> Instalment
												<?php } else{ echo $i;?><sup>th</sup> Instalment
												<?php } ?>
											</div>
											<div class="sum" data-sum="<?php echo $package['instl'][$i]['sum']; ?>">
												<?php if( $package['currency'] == 'inr' ) { 
													echo money_format('%.0n',$package['instl'][$i]['sum']); 
												} else if ( $package['currency'] == 'usd') { 
													echo money_format('%.0n',$package['instl'][$i]['sum']);
												}else { 
													echo money_format('%.0n',0); 
												}; ?>
											</div>
											<div class="sum-desc">
												<span>
													<?php echo money_format('%.0n',( $package['instl'][$i]['sum'] - $instalment )); ?>
												</span>
												<input type="text" style="display:none;" placeholder="Please provide appropriate value!"  value="<?php echo ( $package['instl'][$i]['sum'] - $instalment ); ?>" />
											 + Instl Fee</div>
											<div class="date" data-date="<?php echo $package['instl'][$i]['due_days']; ?>"><?php echo $package['instl'][$i]['due_days']; ?></div>
											<div class="date-desc">
												<span>Days From Previous</span>
												<input type="text" style="display:none;" placeholder="Please provide appropriate value!" value="<?php echo $package['instl'][$i]['due_days']; ?>" />
											</div>
										</div>
									<?php } } else { ?>
										<div id="box1" class="instl fst">
											
											<i onclick="javascript:editInstalment2($(this));" class="fa fa-fw fa-lg fa-edit sum"></i>
											

											<div class="separater top"></div>
											<div class="separater bottom"></div>

											<div class="count">Down Payment</div>
											<div class="sum" data-sum="0">
												<?php echo money_format('%.0n',0); ?>
											</div>
											<div class="sum-desc">
												<span>
													<?php echo money_format('%.0n',0); ?>
												</span>
												<input type="text" style="display:none;" placeholder="Please provide appropriate value!" pattern="/[^\d]+/" value="0" />
											 + Instl Fee</div>
											<div class="date" data-date="0">0</div>
											<div class="date-desc">
												<span>Days From Previous</span>
												<input type="text" style="display:none;" placeholder="Please provide appropriate value!" value="0" />
											</div>
										</div>
									<?php } ?>
										<div class="instl add" data-count="<?php echo (!empty($package['instl'])) ? $package['instl_total'] : 1; ?>">
											<div onclick="javascript:addInstalmentBox2();"><i class="fa fa-fw fa-lg fa-plus"></i></div>
										</div>
									</div>
									<div class="section-col">
										<textarea name="comments_instl" cols="50" placeholder="Please provie reason for giving instalments."><?php echo (!empty($package_comments["instl"])) ? $package_comments["instl"] :""; ?></textarea>
									</div>
								</div>
							</div>							

							<div class="panel-sub">
								<div class="content-min">
									<div>
										Nett Payable<br/>
										<b id="net_payable_min"><?php echo (!empty($package['sum_total'])) ? money_format('%.0n',$package['sum_total']) : money_format('%.0n',0); ?></b>
									</div>
								</div>
								<div class="content">
									<div>
										Installment Fees<br/>
										<b id="instalment_fee" data-instalment_fee="<?php echo (!empty($package['instl_fees'])) ? $package['instl_fees'] : 0; ?>"><?php echo (!empty($package['instl_fees'])) ? money_format('%.0n',$package['instl_fees']) : money_format('%.0n',0); ?></b>
									</div><br/>
									<div>
										Nett Payable<br/>
										<b id="net_payable" data-net_payable="<?php echo (!empty($package['sum_total'])) ? $package['sum_total'] : 0; ?>"><?php echo (!empty($package['sum_total'])) ? money_format('%.0n',$package['sum_total']) : money_format('%.0n',0); ?></b>
									</div>
								</div>
							</div>	

						</div>
<!-- Installments Panel: End -->
					</div>
					<?php //!empty($package) && in_array($package['status'],$allowed_status))) && (!auth_session_is_logged() ?>
					<?php if( (empty($package)) || ( !empty($package) && in_array($package['status'],$allowed_status) && $confirm_payment_access == false ) ) { ?>
					<div class="section-boxed" style="text-align: right;">
						<div class="button ripple" onclick="javascript:createSubscription('save',this);">Save Package</div>
						<div class="button alt orange ripple" onclick="javascript:createSubscription('create',this);">Create Subscription<div class="corner-glow orange"></div></div>
					</div>
					<?php } else { ?>
<!-- Status Panel: Start -->
					<div class="section-boxed status">
					<div class="accordian-panel min has-subpanel">
						<div class="panel-tab">
								<i class="fa fa-chevron-down fa-lg fa-fw"></i>
								<div class="panel-title">Status </div>
						</div>						
						<div class="panel-main">
							<div class="content-min">
								<div class="info-box-container">	
									<div>Status :
									<b><?php
		if( $package['status'] == 'pending' ){
			echo 'Approved by -';
			if( $package['require_approval_sm'] == 1 && $package['status_approval_sm'] == 'approved' ){
				$approved_sm = true;
				echo ' Sales Manager';
			} else if( $package['require_approval_pm'] == 1 && $package['status_approval_pm'] == 'approved' ){
				$approved_pm = true;
				echo ' Payments Manager';
			} else {
				echo ' None';
			}
			echo '&emsp;&#44;&emsp;';
			if( $approved_sm == false || $approved_pm == false ){
				echo 'Waiting for approval from -';
				if( $package['require_approval_sm'] == 1 && $package['status_approval_sm'] == 'pending' ){
					echo ' Sales Manager';
				} else if( $package['require_approval_pm'] == 1 && $package['status_approval_pm'] == 'pending' ){
					echo ' Payments Manager';
				}
			}
		} else if( $package['status'] == 'rejected' ){
			echo 'Rejected by';
			if( $package['require_approval_sm'] == 1 && $package['status_approval_sm'] == 'rejected' ){
				echo ' Sales Manager';
			} else if( $package['require_approval_pm'] == 1 && $package['status_approval_pm'] == 'rejected' ){
				echo ' Payments Manager';
			}
		} else if( $package['status'] == 'executed' ){
			echo package_get_subs_status($package['package_id']);
		} else {
			echo ucwords($package['status']);
		} 
										?>
									</b></div>
								</div>
							</div>
							<div class="content">
								<div class="section-col">
								<?php if( $package['require_approval_sm'] == 1 ){
									echo 'Require Sales Manager Approval';
								} elseif( $package['require_approval_pm'] == 1 ){
									echo 'Require Payments Manager Approval';
								} else {
									echo ucwords($package['status']);
								} ?>
								</div>
								<?php if( $package['require_approval_sm'] == 1 ) { ?>
								<div class="section-col">
									Sales Manager Approval Status : <b><?php echo ucwords($package['status_approval_sm']); ?></b>
									<br/>
									<?php echo $package['approver_comment_sm']; ?>
								</div>
								<?php } ?>
								<?php if( $package['require_approval_pm'] == 1 ) { ?>
								<div class="section-col">
									Payments Manager Approval Status : <b><?php echo ucwords($package['status_approval_pm']); ?></b>
									<br/>
									<?php echo $package['approver_comment_pm']; ?>
								</div>
								<?php } ?>
								<div class="section-col" style="float: right;">
									<!-- <div class="button ripple" onclick="javascript:confirmPayment();">Confirm Payment</div>
									-->
									<?php if($package['status'] == 'executed'){ ?>
									<div class="button alt orange ripple" onclick="javascript:resendLink();">Resend Payment Link</div>
									<?php } ?>
								</div>
							</div>
						</div>
						
						<div class="panel-sub">
						</div>
					</div>
					</div>
<!-- Status Panel: End -->
					<?php } ?>
				</div>
			</div>			
			<div id='ripple-container'>
				<div id='ripple'></div>
			</div>

		</div>	

		<div style="display:none;" id="payment-data" 
			data-instalment-fees-inr="<?php echo (!empty($package) && !empty($instalment_inr) ) ? $instalment_inr : $instalment_fees['inr']; ?>" 
			data-instalment-fees-usd="<?php echo (!empty($package) && !empty($instalment_usd) ) ? $instalment_usd : $instalment_fees['usd']; ?>" 
			data-instalment-date="<?php echo $instalment_date; ?>" 
			data-tax-rate-inr="<?php echo (!empty($package['tax'])) ? $package['tax'] : $tax_rate['inr']; ?>" 
			data-tax-rate-usd="<?php echo (!empty($package['tax'])) ? $package['tax'] : $tax_rate['usd']; ?>" 
			data-max-discount="<?php echo (!empty($agent_discount_max)) ? $agent_discount_max : $agent_default_discount_max; ?>" 
			data-jaws-url="<?php echo JAWS_PATH_WEB; ?>" 
			data-pic-default="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL.'/media/jaws/backend/user-default.png'; ?>" 
			data-currency="<?php echo (!empty($package['currency'])) ? $package['currency'] : 'inr' ; ?>" 
			data-creator-token="<?php echo $_GET["token"]; ?>" 
			<?php if(!empty($package)){ ?>data-package-id="<?php echo $package_id; ?>" <?php } ?> 
			data-instalment-settings='<?php echo $instl_criteria; ?>' ></div>
<script>
$(document).ready(function(){ 
	<?php if(!empty($package)){ ?> 
		selectSpecialization("no_payment");
		populateInfoBox('courses'); 
		populateInfoBox('complimentary'); 
		populateInfoBox('instalments'); 
		disableComplimentary(); 
		$('#payment_mode_min').html($('.accordian-panel-select select[name="payment_mode"] option:selected').text());
	<?php } else { ?> 
	$('.instl-container .instl').hide();
	<?php } ?>
});
</script>
<style>.pymt-sidebar{line-height:16px !important;}.pymt-sidebar input[type="text"]{height:30px;width:auto;}.ovrflw{overflow:unset !important;}.paid{opacity:0.5;pointer-events:none;cursor:none;}.paid::after{content:"Paid";display:inline-block;font-size:30px;margin:-20px 0 0 30%;}</style>
<?php if( $confirm_payment_access == true ){ ?>
<div class="modal-container" id="modal-container">
	<div class="overlay close"></div>
	<div class="modal">
		<div class="header">Update Payment Details</div>
		<div class="close"><i class="fa fa-close"></i></div>
		<div class="content">
		<div class="section-col">
			<?php if(!empty($package['instl']) && $package['instl_total'] > 0 ) {
				$i = 1; 
				for( $i; $i <= $package['instl_total']; $i++ ){ ?>
					<input type="checkbox" name="payment_update[]" id="paid_instl<?php echo $i; ?>" data-sum="<?php echo $package['instl'][$i]['sum']; ?>" data-status="<?php echo (!empty($package_instl["instl"][$i])) ? strtolower($package_instl["instl"][$i]["status"]) : ""; ?>" <?php echo ( !empty($package_instl["instl"][$i]) && $package_instl["instl"][$i]["status"] == "paid" ) ? 'checked="checked"' : ""; ?> value="<?php echo $i; ?>" />
					<label for="paid_instl<?php echo $i; ?>">
						Paid 
						<?php if( $i == 1 ){
							echo 'Down Payment'; 
						} else if( $i == 2 ){ 
							echo '2<sup>nd</sup> Instalment'; 
						} elseif( $i == 3 ){ 
							echo '3<sup>rd</sup> Installment'; 
						} else { 
							echo $i.'<sup>th</sup> Installment'; 
						}
						echo ' having amount of ';
						echo money_format('%.0n',$package['instl'][$i]['sum']); ?>
					</label><br /><br />
				<?php } ?>
				<div class="button ripple" style="float:right;" onclick="javascript:updatePaymentDetails();">Update</div>
			<?php } else { ?>
				Payment Details not available. Please try later.
			<?php } ?>
		</div>
		</div>
	</div>
</div>
<?php } ?>
</body>
</html>