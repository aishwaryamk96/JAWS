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

    // Load stuff
    load_plugin(JAWS_SITE_TOUR_MAIN);
    load_module('ui');

    // Init Session
    auth_session_init();

    // Prep
    $login_params["return_url"] = JAWS_PATH_WEB."/tourian/list";

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
    if (!auth_session_is_allowed("tourian")) {
        ui_render_msg_front(array(
                "type" => "error",
                "title" => "Jigsaw Academy",
                "header" => "No Tresspassing",
                "text" => "Sorry, but you do not have permission to access this page.<br/>Contact Jigsaw Support team for assistance."
                ));

        exit();
    } 

	$tourian = New Tourian();
	
	//Render Tourian's Form
	$url_list = $tourian->getDetails('all','');
	if(!empty($_GET['get']) && $_GET['get'] === 'tours' ){
		$tour_list = $tourian->getDetails('tourian.tours','');
		$url_list = '';
	}
?>
<!DOCTYPE html>
<html>
<head>
<title>Tourian Connecting Form</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<style> html { font-size: 100%; } body { font-family: Lato, sans-serif; font-weight: 400; line-height: 1.5; vertical-align: baseline; } h1{ font-weight: bold; color:#ff853d; } hr { border-style: dotted; color:#a2a2a2; } label { font-weight:bold; color: #333; font-size:14px; } textarea { display:inline-block; vertical-align: middle; } input[type="text" i] { width:190px; height: 24px; } select { width:200px; height:24px;margin-left:20px; } th  , #view , #view tr td { border: 1px solid #a4a4a4; text-align: left; color: #000; font-weight: normal; font-size: 14px;  } th{ padding:5px; } table { border-top:1px dotted #a4a4a4; margin:10px; } table tr td { text-align: left; padding: 10px; } table th{ font-weight:bold; text-align:center;} table tr td a { color:#ff853d; } b{ font-size:12px; } .clear_div { clear:both; } .main{ margin:10px; border: 2px solid #a4a4a4; box-shadow: 3px 1px 9px #a4a4a4; height:auto; text-align: center; } .sub_divs { text-align: left; padding:10px; } .reset { text-align: center; padding:10px; } .reset input , .addButton{    background-color: #ff853d; padding: 5px; color: #000; text-decoration:none; border: none; margin-right:10px;} </style>
<script type="text/javascript" src="../common/jquery-1.12.2.min.js"></script>
</head>
<body>
<div class="main">
    <h1>View Your Tours Here!</h1>
    <div class="sub_divs">
		<a class="addButton" href="<?php echo JAWS_PATH_WEB.'/tourian/url'; ?>">Add New URL</a>
		<a class="addButton" href="<?php echo JAWS_PATH_WEB.'/tourian/add'; ?>">Add New Tour</a>
		<a class="addButton" href="<?php echo JAWS_PATH_WEB.'/tourian/list'; ?>">List URL</a>
		<a class="addButton" href="<?php echo JAWS_PATH_WEB.'/tourian/list?get=tours'; ?>">List Tour</a>
	</div>
    <table cellspacing="0" cellpadding="0" id="view"  width="98%">
    	<thead>
    		<tr>
				<th id="bbb">S.No.</th>
				<th><?php echo (!empty($tour_list)) ? 'Tour Name' : 'Page URL';?></th>
				<th><?php echo (!empty($tour_list)) ? 'Steps' : 'Connected tours';?></th>
				<th>Action</th>
			</tr>
    	</thead>
    	<tbody>
		<?php if(!empty($url_list) && empty($tour_list)){ ?>
		<?php $count = 1; foreach($url_list as &$url){ ?>
    		<tr>
    			<td style="text-align:center;"><?php echo $count; ?></td>
    			<td><?php echo $url['url']; ?></td>
    			<td>
					<?php if(!empty($url['tours'])){ ?>
					<?php foreach($url['tours'] as &$t){ ?>
					<?php echo $t['name'].', '; ?>
					<?php } unset($t); ?>
					<?php } else { ?>
					No tour is associated with this url.
					<?php } ?>
				</td>
    			<td style="text-align:center;"><a href="<?php echo 'url?content_id='.$url['content_id']; ?>">[Edit]</a> &nbsp;<a href="<?php echo 'url?c='.base64_encode($url['content_id']).'&t=r'; ?>" onclick="return confirm('Are you sure you want to remove the url?');">[Delete]</a></td>
    		</tr>
		<?php $count++; } unset($url); ?>	
		<?php } else if( empty($url_list) && empty($tour_list) ) { ?>	
			<tr>
    			<td style="text-align:center;" colspan="4">No URLs Found.</td>
    		</tr>
		<?php } else if( empty($url_list) && !empty($tour_list) ) { ?>
		<?php $count = 1; foreach($tour_list as &$tour){ ?>
    		<tr>
    			<td style="text-align:center;"><?php echo $count; ?></td>
    			<td><?php echo $tour['tour']['tour_name']; ?></td>
    			<td style="text-align:center;">
					<?php echo count($tour['tour']['steps']); ?>
				</td>
    			<td style="text-align:center;"><a href="<?php echo 'add?content_id='.$tour['id']; ?>">[Edit]</a> &nbsp;<a href="<?php echo 'add?c='.base64_encode($tour['id']).'&t=r'; ?>" onclick="return confirm('Are you sure you want to remove the tour?');">[Delete]</a></td>
    		</tr>
		<?php $count++; } unset($tour); ?>	
		<?php } else { ?>
			<tr>
    			<td style="text-align:center;" colspan="4">No tours Found.</td>
    		</tr>
		<?php } ?>
    	</tbody>
    </table>    	
</div>
<script>
$(document).on('click','#bbb',function(){
	console.log('disabled');
/* $.ajax({
  url: "https://www.jigsawacademy.com/jaws/tourian/get",
  method: "POST",
  data: { url : 'https://localhost:3000/courses/{}/discussion_topics' }	
}).done( function( response ) {
  response = JSON.parse(response);
  var tour_length = response.length;
  var tour_start = 0;
  if( tour_length > 0 ){
	for( tour_start; tour_start < tour_length; tour_start++ ){
		var steps_length = response[tour_start].steps.length;
		var steps_start = 0;
		if( steps_length >= 1 ){
			var steps = response[tour_start].steps;
			for( steps_start; steps_start < steps_length; steps_start++ ){
				// for tour type
				if( steps[steps_start].user_interaction == 1 ){
					var type = 'interactive';
				} else {
					var type = 'static';
				}
				// for tour selector
				if(steps[steps_start].tour_selector){
					var selector = steps[steps_start].tour_selector;
				} else {
					var selector = '';
				}
				// for tour message
				if(steps[steps_start].tour_message){
					var message = steps[steps_start].tour_message;
				} else {
					var message = '[No message Set]';
				}
				// for tour event type as user action
				if( steps[steps_start].interaction_type == 'event' && steps[steps_start].step_trigger){
					var action = steps[steps_start].step_trigger;
					var timeout = 0;
				} else {
					var action = '';
					var timeout = 0;
				}
				// for tour event type as timeout
				if(steps[steps_start].interaction_type == 'timeout' && steps[steps_start].tour_timeout){
					var action = '';
					var timeout = steps[steps_start].tour_timeout * 1000;
				} else {
					var action = '';
					var timeout = 0;
				}
				// console.log(tourian_step_create(type,selector,message,action,timeout));
				console.log(type,selector,message,action,timeout);
			}
		}
	}
  }
}); */
});
</script>
</body>
</html>