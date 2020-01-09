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
    $login_params["return_url"] = JAWS_PATH_WEB."/tourian/url";

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
		
	// handle form post data
	if(!empty($_POST)){
		$_SESSION['message'] = $tourian->createURL($_POST);
	}

	// get details for edit action
	if(!empty($_GET['content_id'])){
		$details = $tourian->getDetails('tourian.url',$_GET['content_id']);
	}
	
	// get details for delete action
	if( !empty($_GET['c']) && !empty($_GET['t']) && $_GET['t'] === 'r' ){
		$tourian->remove('tourian.url',base64_decode($_GET['c']));
		header('Location: list');
		exit;
	}
    
	//Render Tourian's Form
	$tour_list = $tourian->findTourList();

?>
<!DOCTYPE html>
<html>
<head>
<title>Tourian Connecting Form</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style> html { font-size: 100%; } body { font-family: Lato, sans-serif; font-weight: 400; line-height: 1.5; vertical-align: baseline; } h1{ font-weight: bold; color:#ff853d; } hr { border-style: dotted; color:#a2a2a2; } label { font-weight:bold; color: #333; font-size:14px; } textarea { display:inline-block; vertical-align: middle; } input[type="text" i] { width:190px; height: 24px; } select { width:200px; height:24px;margin-left:20px; } th  , #view , #view tr td { border: 1px solid #a4a4a4; text-align: left; color: #000; font-weight: normal; font-size: 14px;  } th{ padding:5px; } table { border-top:1px dotted #a4a4a4; margin:10px; } table tr td { text-align: left; padding: 10px; } table tr td a { color:#ff853d; } b{ font-size:12px; } .clear_div { clear:both; } .main{ margin:10px; border: 2px solid #a4a4a4; box-shadow: 3px 1px 9px #a4a4a4; height:auto; text-align: center; } .sub_divs { text-align: left; padding:10px; } .reset { text-align: center; padding:10px; } .reset input , .addButton{    background-color: #ff853d; padding: 5px; color: #000; font-weight: bold; border: none; margin-right:10px;} .add2 { text-decoration: none; font-weight: inherit; } </style>
<script type="text/javascript" src="../common/jquery-1.12.2.min.js"></script>
</head>
<body>
<div class="main">
    <h1>Connect Your Tours Here!</h1>
	<div class="sub_divs">
		<a class="addButton add2" href="<?php echo JAWS_PATH_WEB.'/tourian/url'; ?>">Add New URL</a>
		<a class="addButton add2" href="<?php echo JAWS_PATH_WEB.'/tourian/add'; ?>">Add New Tour</a>
		<a class="addButton add2" href="<?php echo JAWS_PATH_WEB.'/tourian/list'; ?>">List URL</a>
		<a class="addButton add2" href="<?php echo JAWS_PATH_WEB.'/tourian/list?get=tours'; ?>">List Tour</a>
	</div>
    <?php if(!empty($_SESSION['message'])){ ?><h3><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></h3><?php } ?>
    <form id="tour_url" method="post" onsubmit="return validate();" >
	<input type="hidden" name="content_id" value="<?php echo (!empty($_GET['content_id'])) ? $_GET['content_id'] : '' ;?>" />
    	<div class="sub_divs">
			<label>Page to  be Connected : </label>
			<input placeholder="Enter page url" type="text" name="page_url" value="<?php echo (!empty($details)) ? $details['content'] : '' ; ?>" required="true">
    	</div>
    	<div class="sub_divs">
			<label>Select tours to be connected to this page :</label>
		</div>
		<div class="sub_divs">
			<?php if(!empty($tour_list)){ ?>			
			<?php foreach($tour_list as &$t){ ?>
			<input type="checkbox" name="tours[]" id="context<?php echo $t['content_id']; ?>" value="<?php echo $t['content_id']; ?>" <?php echo ( !empty($details) && in_array($t['content_id'],$details['tours']) ) ? 'checked="checked"' : '' ; ?> />
			<label for="context<?php echo $t['content_id']; ?>"> <?php echo $t['tour_name']; ?> </label>
			<?php } unset($t); ?>
			<?php } else { ?>
			<p>Please create a tour first to make the association.</p>
			<?php } ?>
		</div>
		<div  class="reset">
			<!-- <input type="reset" name="reset"> -->
			<input type="submit" name="submit" />
		</div>
    </form>	
</div>
<script type="text/javascript">
function validate(){
	if ($("#tour_url input:checkbox:checked").length > 0){
		return true;
	}else{
	    alert('Please select any tour!');
	    return false;
	}
}
</script>
</body>
</html>