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
    $login_params["return_url"] = JAWS_PATH_WEB."/tourian/add";

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
    if (!auth_session_is_allowed("tourian.add")) {
        ui_render_msg_front(array(
                "type" => "error",
                "title" => "Jigsaw Academy",
                "header" => "No Tresspassing",
                "text" => "Sorry, but you do not have permission to access this page.<br/>Contact Jigsaw Support team for assistance."
                ));

        exit();
    }

	$tourian = New Tourian();
	
	// handle form post data
	if(!empty($_POST)){

		$step_num = array();
		foreach ($_POST['tour'] as $key => $row)
		{
		    $step_num[$key] = $row['step_no'];
		}
		array_multisort($step_num, SORT_ASC, $_POST['tour']);

		$_SESSION['message'] = $tourian->createTour($_POST);
	}

	
	// get details for edit action
	if(!empty($_GET['content_id'])){
		$details = $tourian->getDetails('tourian.tours',$_GET['content_id']);
	}

	
	// get details for delete action
	if( !empty($_GET['c']) && !empty($_GET['t']) && $_GET['t'] === 'r' ){
		$tourian->remove('tourian.tours',base64_decode($_GET['c']));
		header('Location: list?get=tours');
		exit;
	}
	
    //Render Tourian's Form

?>
<!DOCTYPE html>
<html>
<head>
<title>Tourian Form</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style> html { font-size: 100%; } body { font-family: Lato, sans-serif; font-weight: 400; line-height: 1.5; vertical-align: baseline; } h1{ font-weight: bold; color:#ff853d; } hr { border-style: dotted; color:#a2a2a2; } label { font-weight:bold; color: #333; font-size:14px; } textarea { display:inline-block; vertical-align: middle; } input[type="text" i] { width:190px; height: 24px; } select { width:200px; height:24px;margin-left:20px; } th  , #view , #view tr td { border: 1px solid #a4a4a4; text-align: left; color: #000; font-weight: normal; font-size: 14px;  } th{ padding:5px; } table { border-top:1px dotted #a4a4a4; margin:10px; } table tr td { text-align: left; padding: 10px; } table tr td a { color:#ff853d; } b{ font-size:12px; } .clear_div { clear:both; } .main{ margin:10px; border: 2px solid #a4a4a4; box-shadow: 3px 1px 9px #a4a4a4; height:auto; text-align: center; } .sub_divs { text-align: left; padding:10px; } .reset { text-align: center; padding:10px; } .reset input , .addButton{    background-color: #ff853d; padding: 5px; color: #000; font-weight: bold; border: none; margin-right:10px;} .add2 { text-decoration: none; font-weight: inherit; } </style>
<script type="text/javascript" src="../common/jquery-1.12.2.min.js"></script>
</head>
<body>
<div class="main">
    <h1>Design Your Tour Here!</h1>
	<div class="sub_divs">
		<a class="addButton add2" href="<?php echo JAWS_PATH_WEB.'/tourian/url'; ?>">Add New URL</a>
		<a class="addButton add2" href="<?php echo JAWS_PATH_WEB.'/tourian/add'; ?>">Add New Tour</a>
		<a class="addButton add2" href="<?php echo JAWS_PATH_WEB.'/tourian/list'; ?>">List URL</a>
		<a class="addButton add2" href="<?php echo JAWS_PATH_WEB.'/tourian/list?get=tours'; ?>">List Tour</a>
	</div>
	<?php if(!empty($_SESSION['message'])){ ?><h3><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></h3><?php } ?>
    <form id="tour" method="post" >
	<input type="hidden" name="content_id" value="<?php echo (!empty($_GET['content_id'])) ? $_GET['content_id'] : '' ;?>" />
    	<div class="sub_divs">
			<label>Name of tour :  </label>
    		<input placeholder="Enter name of the tour " type="text" value="<?php echo (!empty($details)) ? $details['tour_name'] : '' ; ?>" name="tour_name" required="true" />
    	</div>
  		<div id="to_be_repeated">
			<?php if(!empty($details)){ ?>
			<?php foreach( $details['steps'] as $key => &$steps ){ ?>
			<table>
		    <tr>
		    	<td width="4%">
					<input type="text" class="step" name="tour[<?php echo $key; ?>][step_no]" value="<?php echo $steps['step_no']; ?>" style="width:18px; border:none;" />
				</td>
		    	<td width="35%">
		    		<label>Message :  </label>
			    	<textarea placeholder="Enter message" name="tour[<?php echo $key; ?>][tour_message]" rows="5" cols="40" required="true"><?php echo $steps['tour_message']; ?></textarea>
			    </td>
			    <td  width="35%">
			    	<label>Selector : </label>
			     	<input type="text" name="tour[<?php echo $key; ?>][tour_selector]" placeholder="Enter selector" value="<?php echo $steps['tour_selector']; ?>" />
			    </td>
			    <td width="30%">
					<label>Allow user interaction : </label>
			    	<label>Yes</label>
					<input type="radio" name="tour[<?php echo $key; ?>][user_interaction]" value="1" <?php echo ($steps['user_interaction'] == 1) ? 'checked="checked"' : '' ; ?> onchange="userinteraction(this,<?php echo $key+1; ?>);" />
			    	<label>No</label> 
					<input type="radio" <?php echo ($steps['user_interaction'] != 1) ? 'checked="checked"' : '' ; ?> name="tour[<?php echo $key; ?>][user_interaction]" value="0" onchange="userinteraction(this,<?php echo $key+1; ?>);" />
			    </td>
				<td></td>
			</tr>
     		<tr>
				<td></td>
				<td id="event_<?php echo $key+1; ?>">
					<input type="radio" name="tour[<?php echo $key; ?>][interaction_type]" value="event" <?php echo ($steps['interaction_type'] == 'event') ? 'checked="checked"' : '' ; ?> />
					<label>Event :  </label>
					<select name="tour[<?php echo $key; ?>][step_trigger]">
						<option <?php echo ($steps['step_trigger'] == 'click') ? 'selected="selected"' : '' ; ?> value="click">Click</option>
						<option <?php echo ($steps['step_trigger'] == 'mouseenter') ? 'selected="selected"' : '' ; ?> value="mouseenter">Hover</option>
					</select>
				</td>
				<td>
			   		<input id="timeout_<?php echo $key+1; ?>"  type="radio" name="tour[<?php echo $key; ?>][interaction_type]" <?php echo ($steps['interaction_type'] == 'timeout') ? 'checked="checked"' : '' ; ?> value="timeout" />
					<label>Timeout : </label>
					<input placeholder="Enter timeout (In Seconds)" type="text" name="tour[<?php echo $key; ?>][tour_timeout]" value="<?php echo $steps['tour_timeout']; ?>" />
				</td>
	    		<td>
	    			<input type="radio" name="tour[<?php echo $key; ?>][interaction_type]" value="next" <?php echo ($steps['interaction_type'] == 'next') ? 'checked="checked"' : '' ; ?> />
					<label >Next button </label>
			    </td>
		    	<td><td onclick="removediv(this)" style="color:#ff853d;cursor:pointer;"><b>[Delete]</b></td></td>
			</tr>
			</table>
			<?php } $key++; unset($steps); ?>
			<?php } else { $key = 0; ?> 
			<table>
		    <tr>
		    	<td width="3%">
					<input type="text" class="step" name="tour[<?php echo $key; ?>][step_no]" value="<?php echo $key+1; ?>" style="width:10px; border:none;"  />
				</td>
		    	<td width="35%">
		    		<label>Message :  </label>
			    	<textarea placeholder="Enter message" name="tour[<?php echo $key; ?>][tour_message]" rows="5" cols="40" required="true"></textarea>
			    </td>
			    <td  width="35%">
			    	<label>Selector : </label>
			     	<input type="text" name="tour[<?php echo $key; ?>][tour_selector]" placeholder="Enter selector" />
			    </td>
			    <td width="30%">
					<label>Allow user interaction : </label>
			    	<label>Yes</label>
					<input type="radio" name="tour[<?php echo $key; ?>][user_interaction]" value="1" checked="true" onchange="userinteraction(this,<?php echo $key+1; ?>);" />
			    	<label>No</label> 
					<input type="radio" name="tour[<?php echo $key; ?>][user_interaction]" value="0" onchange="userinteraction(this,<?php echo $key+1; ?>);" />
			    </td>
				<td></td>
			</tr>
     		<tr>
				<td></td>
				<td id="event_<?php echo $key+1; ?>">
					<input type="radio" name="tour[<?php echo $key; ?>][interaction_type]" value="event" checked="true" />
					<label>Event :  </label>
					<select name="tour[<?php echo $key; ?>][step_trigger]">
						<option value="click">Click</option>
						<option value="mouseenter">Hover</option>
					</select>
				</td>
				<td>
			   		<input id="timeout_<?php echo $key+1; ?>" type="radio" name="tour[<?php echo $key; ?>][interaction_type]" value="timeout" />
					<label>Timeout : </label>
					<input placeholder="Enter timeout" type="text" name="tour[<?php echo $key; ?>][tour_timeout]" />
				</td>
	    		<td>
	    			<input type="radio" name="tour[<?php echo $key; ?>][interaction_type]" value="next" />
					<label >Next button </label>
			    </td>
		    	<td><!-- <td onclick="removediv(this)" style="color:#ff853d;cursor:pointer;"><b>[Delete]</b></td> --></td>
			</tr>
			</table>
			<?php } ?>
		</div>
		<div  class="reset">
		    <input type="button" rel="<?php echo $key; ?>" name="add" id="add" value="Add New Step">
		    <!-- <input type="reset" name="reset" /> -->
		    <input type="submit" name="submit" />
		</div>
    </form>	
</div>
<script type="text/javascript">


$("#add").click(function(e){
	var y = $('#add').attr('rel');
	var key = parseInt(y,10) + parseInt(1,10);
	 $('#add').attr('rel',key);
	var x = $('.step').last().val(); x++;
    	y = key; //text box name increment

    $("#to_be_repeated").append('<table>'+
		'<tr>'+
			'<td width="3%"><input type="text" class="step" name="tour['+y+'][step_no]" value="'+x+'" style="width:10px; border:none;"></td>'+
			'<td width="35%">'+
				'<label>Message :  </label>'+
				'<textarea name="tour['+y+'][tour_message]" placeholder="Enter message" rows="5" cols="40" required="true"></textarea>'+
			'</td>'+
			'<td  width="35%">'+
				'<label>Selector : </label>'+
				'<input placeholder="Enter selector" type="text" name="tour['+y+'][tour_selector]" />'+
			'</td>'+
			'<td width="30%">'+
				 '<label>Allow user interaction : </label>'+
				'<label>Yes</label><input type="radio" name="tour['+y+'][user_interaction]" value="1" checked="true" onchange="userinteraction(this,'+x+')">'+
				'<label>No</label> <input type="radio" name="tour['+y+'][user_interaction]" value="0"  onchange="userinteraction(this,'+x+')" >'+
			'</td>'+
			'<td></td>'+
		'</tr>'+
		'<tr>'+
			'<td ></td>'+
			'<td id="event_'+x+'">'+
			'<input type="radio" name="tour['+y+'][interaction_type]" value="event"  checked="true" />'+
			'<label>Event :  </label>'+
			'<select name="tour['+y+'][step_trigger]">'+
				'<option value="click">Click</option>'+
				'<option value="mouseenter">Hover</option>'+
			'</select>'+
			'</td>'+
			'<td>'+
				'<input id="timeout_'+x+'" type="radio" name="tour['+y+'][interaction_type]" value="timeout" />'+
				'<label>Timeout : </label>'+
				 '<input placeholder="Enter timeout" type="text" name="tour['+y+'][tour_timeout]" />'+
			'</td>'+

			'<td>'+
				'<input type="radio" name="tour['+y+'][interaction_type]" value="next" />'+
				 '<label>Next button </label>'+
			'</td>'+
			'<td onclick="removediv(this)" style="color:#ff853d;cursor:pointer;"><b>[Delete]</b></td>'+
		'</tr>'+'</table>'); //add input box

});
function removediv(td){
	if (confirm("Are you sure?")) {
	var tablee= $(td).parent('tr');
	var tr = tablee.siblings();
	var parent_tbl = tablee.parent('table');
	 tr.remove();	
	tablee.remove();
	parent_tbl.remove();
}
return false;

	//ttbl.parent('table').prev('hr').remove();
	//ttbl.parent('table').remove();
}
function userinteraction(interact,id){
	if(interact.value == 0)	{ // no selected
		$("#event_"+id).hide();
		$("#timeout_"+id).attr('checked', 'checked');
	} else {
		$("#event_"+id).show();
	}
}
</script>
</body>
</html>