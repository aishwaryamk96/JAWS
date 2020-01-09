<?php error_reporting(E_ALL);

// Prevent exclusive access
	if (!defined("JAWS")) {
		header('Location: https://www.jigsawacademy.com');
		die();
	}
	
	load_module("subs");
	load_module("course");
	load_module("user");
	load_module("ui");
	
	// Prep
	$login_params["return_url"] = JAWS_PATH_WEB."/view/backend/temp/aform.temp.test";

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
	// if (!auth_session_is_allowed("package.approve")) {
	if (!auth_session_is_allowed("paylink.create")) {
			ui_render_msg_front(array(
					"type" => "error",
					"title" => "Jigsaw Academy",
					"header" => "No Tresspassing",
					"text" => "Sorry, but you do not have permission to access this page.<br/>Contact Jigsaw Support team for assistance."
					));
	
			exit();
	}
	
	if (isset($_GET['page'])) {
		$page = $_GET['page'];
	} else {
		$page = 1;
	}
	
	if (isset($_GET['email'])) {
		$email = $_GET['email'];
	} else {
		$email = "";
	}
	
	$limit = 10;
	$start = ($page - 1) * $limit;
	
	$data = package_fetch($start,$limit);
	
	$total = package_fetch_count();
	
	$pagination_count = ceil($total / $limit);
	
	// Load courses
	$courses = course_get_info_all();
	
	$combo_details = array();
	foreach ( $courses as &$course ) {
		$combo_details[$course['course_id'].',1'] = array( 
			'name' => $course['name'],
			'code' => $course['il_code'],
			'price_usd' => $course['il_price_usd'],
			'price_inr' => $course['il_price_inr']
		);
		$combo_details[$course['course_id'].',2'] = array( 
			'name' => $course['name'],
			'code' => $course['sp_code'],
			'price_usd' => $course['sp_price_usd'],
			'price_inr' => $course['sp_price_inr']
		); 
	} unset($course);
	
	function fetchStatus($package){
		$status = '';
		if( $package['status'] == 'pending' ){
			$status .= 'Approved by -';
			if( $package['require_approval_sm'] == 1 && $package['status_approval_sm'] == 'approved' ){
				$approved_sm = true;
				$status .= ' Sales Manager';
			} else if( $package['require_approval_pm'] == 1 && $package['status_approval_pm'] == 'approved' ){
				$approved_pm = true;
				$status .= ' Payments Manager';
			} else {
				$status .= ' None';
			}
			$status .= '<br/>';
			if( $approved_sm == false || $approved_pm == false ){
				$status .= 'Waiting for approval from -';
				if( $package['require_approval_sm'] == 1 && $package['status_approval_sm'] == 'pending' ){
					$status .= ' Sales Manager';
				} else if( $package['require_approval_pm'] == 1 && $package['status_approval_pm'] == 'pending' ){
					$status .= ' Payments Manager';
				}
			}
		} else if( $package['status'] == 'rejected' ){
			$status .= 'Rejected by';
			if( $package['require_approval_sm'] == 1 && $package['status_approval_sm'] == 'rejected' ){
				$status .= ' Sales Manager';
			} else if( $package['require_approval_pm'] == 1 && $package['status_approval_pm'] == 'rejected' ){
				$status .= ' Payments Manager';
			}
		} else if( $package['status'] == 'executed' ){
			$status .= package_get_subs_status($package['package_id']);
		} else {
			$status .= ucwords($package['status']);
		}
		return $status;
	}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
	<meta content="utf-8" http-equiv="encoding">
	<title>JAWS - Package Approval</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<link rel="icon" type="image/png" href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL.'/media/jaws/frontend/images/favicon.png'; ?>">
		
    <!-- Stylesheets -->
	<link rel="stylesheet" href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL.'/common/fa/css/font-awesome.css'; ?>" />
	<link rel="stylesheet/less" type="text/css" href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL."/app/templates/jaws/backend/dashtemp/theme.light.less"; ?>" />
	<link rel="stylesheet/less" type="text/css" href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL."/app/templates/jaws/backend/dashtemp/layout.less"; ?>" />
	<link rel="stylesheet/less" type="text/css" href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL."/app/templates/jaws/backend/dashtemp/aform.temp.less"; ?>" />
	
	<!-- Fonts -->
	<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
	<link href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet" type="text/css" />
	<!-- Libraries -->
	<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script src="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL.'/common/less.js/less.min.js'; ?>" data-env="development"></script>
	<!-- app specific -->
	<script src="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL."/app/templates/jaws/backend/dashtemp/dash.js"; ?>"></script>

  </head>
  <body data-tourian="false">
  <script>
function viewPayment(id, email, name, phone){
	var url = '<?php echo JAWS_PATH_WEB; ?>/package.create.embed?package='+encodeURIComponent(id)+'&email='+encodeURIComponent(email)+'&name='+encodeURIComponent(name)+'&phone='+encodeURIComponent(phone);
	$('#payment_details').attr('src',url);   
	$("#modal-container").addClass("active");
	$("body > div.wrapper").addClass('blur');
}
var sendUrl = "<?php echo JAWS_PATH_WEB; ?>/webapi/backend/dashtemp/package.update";
function approvePackage(id){
	if(confirm('Are you sure you want to approve the package? It cannot be updated later.')){
		var save = { 
		'package': {
			'package_id': id,
			'status_approval_pm': 'approved'
			}
		};
		$.ajax({ method: "POST", url:sendUrl , dataType: 'json', cache: false,	data: save 
		}).done(function( response ) { 
			if(response.package_id){
				$('#section'+id).html('Accepted');
			}
		}).fail(function() { alert( "Oops! It seems that some error has occured. Please try refreshing the page or try after some time." );
		}).always(function( response ) { console.log(response); console.log('Completed Transaction.'); });
	} else {
		console.log('Cancelled Approval.');
		return false;
	}
}
function rejectPackage(id,obj){
	if(!$('#reject'+id).find('textarea[name="approver_comment_pm"]').val().trim()){
		alert('Please provide reason for rejection.');
		return false;
	}
	if(confirm('Are you sure you want to reject the package? It cannot be updated later.')){
		var save = { 
		'package': {
			'package_id': $('#reject'+id).find('input[name="package_id"]').val(),
			'status_approval_pm': 
				$('#reject'+id).find('input[name="status_approval_pm"]').val(),
			'approver_comment_pm': 
				$('#reject'+id).find('textarea[name="approver_comment_pm"]').val()
			}
		};
		$.ajax({ method: "POST", url:sendUrl , dataType: 'json', cache: false,	data: save 
		}).done(function( response ) { 
			if(response.package_id){
				$('#section'+id).html('Rejected');
			}
		}).fail(function() { alert( "Oops! It seems that some error has occured. Please try refreshing the page or try after some time." );
		}).always(function( response ) { console.log(response); console.log('Completed Transaction.'); });
	} else {
		console.log('Cancelled Rejection.');
		return false;
	}
}
</script>
		<div class="wrapper">

			<div id="main-container" class="">	
				<div class="wrapper">

					<div class="section-boxed details-section">
<!-- Search Panel: Start -->
					<div class="accordian-panel pin has-subpanel">
					<form id="search_form" method="GET" action="">
						<div class="panel-tab">
							<i class="fa fa-user fa-lg fa-fw"></i>
						</div>

						<div class="panel-main">
							<div class="content-min overflow" style="opacity: 1; pointer-events: auto;">
								<div class="section-col">
									<input type="email" placeholder="Email" name="email" value="<?php echo $email; ?>" required />
								</div>
							</div>
						</div>						

						<div class="panel-sub">
							<div class="content">
								<div class="accordian-panel-select">
									<button class="btn btn-submit" type="submit">
										<i class="fa fa-search"></i> Search
									</button>
									<!--button class="btn btn-warning" type="reset">
										<i class="fa fa-times"></i> Clear
									</button -->
								</div>
							</div>
						</div>	
					</form>
					</div>
<!-- Search Panel: End -->
<!-- Details Panel: Start -->
					<?php if(!empty($data)){ ?>
					<?php foreach($data as $package){ ?>
<?php if($package['currency'] == 'usd'){ setlocale(LC_MONETARY, 'en_US'); } 
	else { setlocale(LC_MONETARY, 'en_IN'); } ?>
<?php $combo_array = explode(';',$package['combo']); ?>
<?php $combo_free_array = explode(';',$package['combo_free']); ?>
						<div class="accordian-panel pin has-subpanel">

							<div class="panel-tab">
								<i class="fa fa-user fa-lg fa-fw"></i>
							</div>

							<div class="panel-main">
								<div class="content-min overflow" style="opacity: 1; pointer-events: auto;">
									<div class="section-col user_details">
										<?php echo $package['name']; ?><br/>
										<?php echo $package['email']; ?><br/>
										<?php echo $package['phone']; ?><br/>
									</div>
									<div class="section-col user-courses">
										<?php foreach( $combo_array as $combo ){ ?>	<span class="tooltip"><?php echo $combo_details[$combo]['code']; ?></span>
										<div class="tooltiptext" style="display:none;">
											Name : <?php echo $combo_details[$combo]['name']; ?><br/>
											Price: <?php echo ($package['currency'] == 'usd') ? money_format('%.0n', $combo_details[$combo]['price_usd']) : money_format('%.0n', $combo_details[$combo]['price_inr']); ?>
										</div>
										<?php } unset($combo); ?>
										<br/>
										<?php foreach( $combo_free_array as $combo ){ ?>
										<span class="tooltip">
											<?php echo $combo_details[$combo]['code']; ?>
										</span>
										<div class="tooltiptext" style="display:none;">
											Name : <?php echo $combo_details[$combo]['name']; ?><br/>
											Price: Complimentary Course
										</div>
										<?php } unset($combo); ?>
									</div>
									<div class="section-col user-status">
										<span class="tooltip">
											<?php echo ucwords($package['status']); ?>
										</span>
										<div class="tooltiptext" style="display:none;">
											<?php echo fetchStatus($package); ?><br/>
											Created: <?php echo date("l jS \of F Y h:i:s A", strtotime($package['create_date'])); ?>
										</div>
									</div>
									<div class="section-col user-package">
										<span class="tooltip">
											<?php echo money_format('%.0n',$package['sum_total']); ?>
										</span>
										<div class="tooltiptext" style="display:none;">
											Basic : <?php echo money_format('%.0n', $package['sum_basic']);  ?><br/>
											Offered : <?php echo money_format('%.0n',$package['sum_offered']);  ?><br/>
											Total : <?php echo money_format('%.0n',$package['sum_total']);  ?><br/>
										</div>
									</div>
									<div class="section-col agent-details" data-agent-id="<?php echo $package['creator_id']; ?>">
										<?php $agent = user_get_by_id($package['creator_id']); ?>
										<?php echo $agent['name']; ?>
									</div>
									<div class="section-col">
									<i class="fa fa-list" onclick="javascript:viewPayment('<?php echo $package['package_id']; ?>','<?php echo $package['email']; ?>','<?php echo $package['name']; ?>','<?php echo $package['phone']; ?>');"></i>
									</div>
								</div>
							</div>						

							<div class="panel-sub">
								<div class="content">
									<div class="accordian-panel-select" id="section<?php echo $package['package_id']; ?>">
									<?php if( $package['status'] == 'pending' || $package['status'] == 'draft' ){ ?>
										<i class="fa fa-check approve" onclick="javascript:approvePackage('<?php echo $package['package_id']; ?>');">Approve</i>
										<i class="fa fa-times tooltip reject">Reject</i>
										<div class="tooltiptext">
											<form id="reject<?php echo $package['package_id']; ?>" >
												<input type="hidden" name="status_approval_pm" value="rejected" />
												<input type="hidden" name="package_id" value="<?php echo $package['package_id']; ?>" />
												<textarea rows="4" cols="30" style="width:100%;" name="approver_comment_pm"></textarea>
												<br/>
												<input type="button" value="Reject" onclick="javascript:rejectPackage('<?php echo $package['package_id']; ?>',this);" />
											</form>
										</div>
									<?php } else { ?>
										<?php echo ucwords($package['status']); ?>
									<?php } ?>
									</div>
								</div>
							</div>	

						</div>
					<?php } ?>
					<?php } ?>
<!-- Details Panel: End -->
					</div>
<!-- Pagination -->
					<div class="section-boxed" style="text-align: center;">
						<?php echo pagination($total,$page,$limit); ?>
					</div>
<!-- Pagination -->

				</div>
			</div>

			<div id="nav-container" class="corner">
				<div class="overlay"></div>

				<div id="nav" class="">
					<i class="fa hotspot fa-fw fa-lg fa-bars"></i>

					<div class="menu" class="">	

						<div class="menu-item ripple animate" href="#">
							<i class="fa fa-fw fa-lg fa-search"></i><span class="desc">Search</span>
						</div>

						<div class="menu-item ripple animate" href="#">
							<i class="fa fa-fw fa-lg fa-area-chart"></i><span class="desc">Dashboard</span>
						</div>
						
						<div class="menu-item animate has-submenu" href="#" id="menu-subs">
							<i class="fa fa-fw fa-lg fa-group animate"></i><span class="desc">Subscriptions<span class="arrow fa fa-fw fa-caret-right"></span></span>

							<div class="submenu">
								<div class="submenu-item ripple"  href="#">Add New Subscription</div>
								<div class="submenu-item ripple"  href="#">Pending Subscriptions</div>
								<div class="submenu-item ripple"  href="#">Pending Installments</div>						
							</div>
						</div>						

					</div>
				</div>
				
			</div>

			<div id="user-container" class="corner">
				<div class="overlay"></div>

				<div id="user" style="background-image: url('<?php echo $_SESSION["user"]["photo_url"]; ?>');">
					<div class="header-box">
						<span id="user-name"><?php echo $_SESSION["user"]["name"]; ?></span><br/>
						<span id="user-badges" style="pointer-events: none; color: rgba(0,0,0,0.1);">Profile</span>
					</div>

					<div class="dp-border"></div>

					<div class="menu">
						<i class="fa fa-fw fa-lg fa-envelope ripple" href="#" style="pointer-events: none; color: rgba(0,0,0,0.1);"><span class="desc">Inbox</span></i>
						<i class="fa fa-fw fa-lg fa-cog ripple" href="#" style="pointer-events: none; color: rgba(0,0,0,0.1);"><span class="desc">Settings</span></i>
						<i class="fa fa-fw fa-lg fa-power-off ripple" href="<?php echo JAWS_PATH_WEB."/logout"; ?>"><span class="desc">Logout</span></i>
					</div>

					<div id="user-alert" style="display:none">3</div>

					<div class="hover-mask"></div>
				</div>
			</div>

			<div id="msg-container" class="corner" style="display: none">
				<div class="overlay"></div>

				<div id="msg" class="">
					<i class="fa fa-fw fa-lg fa-comment hotspot"></i>

					<div class="header-box">
						<span id="msg-title">Comm Centre</span><br/>
						<span id="msg-title-mini">3 New Messages</span>
					</div>

					<div class="menu">
						<i class="fa fa-fw fa-lg fa-comments-o ripple" href="#" id="tourian-trigger"><span class="desc">Chat</span></i>
						<i class="fa fa-fw fa-lg fa-info ripple" href="#"><span class="desc">Thread</span></i>
						<i class="fa fa-fw fa-lg fa-bullhorn ripple" href="#"><span class="desc">Broadcast</span></i>
					</div>

					<div class="hover-mask"></div>
				</div>
			</div>
			
			<div id="alert-container" class="corner">
				<div class="overlay"></div>
			</div>

			<div id="search-container" class="">

			</div>

			<div id="top-container" class="">
				<div id="logo" class="grayscale" style="background-image: url('<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL.'/media/jaws/backend/logo_small.png'; ?>');"></div>
				
				<div id="page-title"><span>Jigsaw Academy</span></div>
				
				<div id="bread-crumbs">
					<a href="#">JAWS</a>
					<i class="fa fa-fw fa-caret-right"></i>
					<a href="#">Package Approval</a>
				</div>

				<div class="progress min pin" data-progress="50">
					<div class="fill animate"></div>
					<div class="desc"></div>
				</div>
				
			</div>

			<div id='ripple-container'>
				<div id='ripple'></div>
			</div>
		</div>
<style>.user_details{width:235px;}.user-courses{width:220px;}.user-status{width:100px;}.user-package{width:90px;}.agent-details{width:110px;}.details-section div.accordian-panel div.content-min div { display: inline-block; }.btn{height: 30px;padding: 0;min-width: 0;margin: 0 5px;
box-shadow: unset; border: none; }.btn-warning{background:#FF0000;color:#FFFFFF;}.btn-submit{background:#90EE90;color:#FFFFFF;}</style>		
<div class="modal-container" id="modal-container">
	<div class="overlay close"></div>
	<div class="modal" style="max-width:95% !important; width: 100%; height: 95%;">
		<div class="header">View Package Details</div>
		<div class="close"><i class="fa fa-close"></i></div>
		<div class="content" style="overflow:hidden;">
			<iframe width="100%" height="100%" id="payment_details" src="" frameborder="0">
		</div>
	</div>
</div>
</body>
</html>