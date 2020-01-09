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
		header('Location: http://www.jigsawacademy.com');
		die();
	}

	// Load stuff
   	load_plugin("shuriken");
	load_module('ui');

	// Init Session
	auth_session_init();

	// Prep
	$login_params["return_url"] = JAWS_PATH_WEB."/shuriken/tagmgr/tags";

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
	if (!auth_session_is_allowed("shuriken")) {
		ui_render_msg_front(array(
				"type" => "error",
				"title" => "Jigsaw Academy",
				"header" => "No Tresspassing",
				"text" => "Sorry, but you do not have permission to access this page.<br/>Contact Jigsaw Support team for assistance."
				));

		exit();
	} 

	$tags = shuriken_tagmgr_tag_get_all();
	$container = shuriken_tagmgr_container_get_all();
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Shuriken - Tag Manager - Tags</title>
		<meta name="author" content="dashboard">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<link rel="icon" type="image/png" href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL.'/media/jaws/frontend/images/favicon.png'; ?>">
		<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL.'/common/fa/css/font-awesome.css'; ?>">
		<link rel="stylesheet" type="text/css" href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL."/app/templates/jaws/backend/temp/shuriken.tagmgr.css"; ?>" />
		<script   src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	</head>
	<body>
		<div class="wrapper">
			<div id="main-container">
				<div id="header"><img src="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL.'/media/jaws/backend/jigsaw_horizontal_logo.png'; ?>">
				<div class="header-buttons">
				<a href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL."/shuriken/tagmgr"; ?>">Add New Container </a>
				<a href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL."/shuriken/tagmgr/tags"; ?>">Add New Tag </a>
					<!-- <input onclick="display_add_container();" type="button" name="add" value="Add New Container" />
					<input onclick="display_add_tag();" type="button" name="add_script" value="Add New Tag" /> -->
				</div>
				</div>
				<div class="wrapper">
					<!-- ==============tags listing here============== --> 
					<div id="category-list">
					<div class="cont title" style="text-align:center;  color:#4174e0;font-size: 1.2em;background: #ededed;padding: 7px 0;">Tag List</div>
						<ul>
							<?php foreach($tags as $key=>$value) : ?>
							<li id="tag-id-<?php echo $value['tag_id']; ?>" <?php echo ($value['status']=='enabled') ? 'class="list active"' : 'class="list"' ?>  onclick="displaydata(<?php echo $value['tag_id']; ?>);">
								<div class="icon">
									 <?php if($value['parent_container']!='') 
													 { 
										 ?><i class="fa fa-folder-open" aria-hidden="true"></i> <?php
											 } 
									 else 
									{ 
									?><i class="fa fa-globe" aria-hidden="true"></i><?php
									} ?>
								</div>
								<div id="info">
									<div class="tag-info">
										<div class="tag-type"><?php echo $value['type']; ?></div>
										<div class="tag-name"><?php echo $value['name']; ?></div>
									</div>
									<div class="selector-info">
										 <?php if($value['type'] == 'html')
										{  echo $value['selector']; }
										?>
									</div>
								</div>
							</li>
							<?php endforeach; ?>
						</ul>
					</div>

					<!-- =========ADD/EDIT TAG FORM========== -->
					<div id="tag-form">
						<div class="new-tag" align="center">
							<div class="cont container-heading" align="center">Add new tag form</div>
							<form id="add-tag" name="add-tag" action="" method="post" >
								
								<div class="cont">
									<div class="cont-name">
										<label class="form-label">Status : </label>
										<select class="form-input" name="status" id="status">
											<option value="enabled">Enabled</option>
											<option value="disabled">Disabled</option>
										</select>
									</div>
									<div class="cont-cat">
										<label class="form-label">Tag Scope : </label>
										<select class="form-input" name="parent_container" id="parent_container">
											<option value="">Global</option>
											<?php foreach($container as $key=>$value){ ?>
											<option value="<?php echo $value['container_id']?>"><?php echo $value['name']; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>

								<div class="cont">
									<label class="form-label">Name : </label>
									<input type="text" name="tag_name" id="tag_name" required="true"  />
								</div>
								
								<div class="cont">
									<label class="form-label">Code : </label>
									<textarea name="tag_code" id="tag_code" required="true" cols="77" rows="5"></textarea> 
								</div>
								
								<div class="cont">
									<label class="form-label">Tag type : </label>
									<input type="text" name="tag_type" id="tag_type"  />
								</div>

								<div class="cont" id="selector">
									<div class="cont-name">
										<label class="form-label">Selector type : </label>
										<select class="form-input" name="selector_type" id="selector_type">
											<option value="">Select</option>
											<option value="TagName">Tag Name</option>
											<option value="ClassName">Class Name</option>
											<option value="Id">Id</option>
										</select>
									</div>
									
									<div class="cont-cat">
										<label class="form-label">Selector Name : </label>
										<input class="form-input" type="text" name="selector_name"  />
									</div>
								</div>

								<div class="cont add">
									<input type="submit" name="submit" id="add_button" value="submit" />
								</div>
							</form>
						</div>

						<!--  =============Edit tag form =========  - -->
						<div id="tag-edit" align="center">
							<div class="cont container-heading" align="center">Edit Tag form</div>
							<form id="edit-tag" name="edit-tag" action="" method="post" >
							<input type="hidden" name="tag_id" id="tag_id">
								<div class="cont">
									<div class="cont-name">
										<label class="form-label">Status : </label>
										<select class="form-input" name="edit_status" id="edit_status">
											<option value="enabled">Enabled</option>
											<option value="disabled">Disabled</option>
										</select>
									</div>
									<div class="cont-cat" id="tag-scope">
										<label class="form-label">Tag Scope : </label>
										<select class="form-input" name="edit_parent_container" id="edit_parent_container">
											<option value="">Global</option>
											<?php foreach($container as $key=>$value){ ?>
											<option value="<?php echo $value['container_id']?>"><?php echo $value['name']; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>

								<div class="cont">
									<label class="form-label">Name : </label>
									<input type="text" name="edit_tag_name" id="edit_tag_name" required="true"  />
								</div>
								<div class="cont">
									<label class="form-label">Code : </label>
									<textarea name="edit_tag_code" id="edit_tag_code" required="true" cols="77" rows="5"></textarea> 
								</div>

								<div class="cont">
									<label class="form-label">Tag type : </label>
									<input type="text" name="edit_tag_type" id="edit_tag_type"  />
								</div>

								<div class="cont" id="edit-selector">
									<div class="cont-name">
										<label class="form-label">Selector type : </label>
										<select class="form-input" name="edit_selector_type" id="edit_selector_type">
											<option value="">Select</option>
											<option value="TagName">Tag Name</option>
											<option value="ClassName">Class Name</option>
											<option value="Id">Id</option>
										</select>
									</div>
									<div class="cont-cat">
										<label class="form-label">Selector Name : </label>
										<input class="form-input" type="text" name="edit_selector_name" id="edit_selector_name" />
									</div>
								</div>

								<div class="cont add">
									<input type="submit" name="submit" id="edit_button" value="submit" />
								</div>
							</form>
						</div>
						<!-- ==============Edit tag form ends ====== -->
					</div>
				</div>
			</div>
			
		</div>

<script>
	var container = <?php echo json_encode($container); ?>;
	$(document).ready(function(){
		// ========================== ADD TAG CODE STARTS HERE ====================== //
		// ==========process the form========== //
			$('#add-tag').submit(function(event) {
				var formData = $('#add-tag').serialize();
				//  =======process the form========//
				$.ajax({
					type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
					url         :   "<?php echo JAWS_PATH_WEB; ?>/shuriken/tagmgr/shuriken.tagmgr.tag.create", // the url where we want to POST
					data        : formData, // our data object

					success: function(dataNew) {
						dataNew = JSON.parse(dataNew);
						$("#add-tag").trigger('reset'); // reset the form fields
						$("#category-list ul li").removeClass('selected');

						// ======append the added tag to the list in left hand side menu [ tag list ]===== //
						if(dataNew.parent_container!= null) 
							 { 
					icon = '<i class="fa fa-folder-open" aria-hidden="true"></i>';
				} 
				else 
				{ 
					icon =  '<i class="fa fa-globe" aria-hidden="true"></i>';
				}
				if(dataNew.status=='enabled')
							{
								li_class = 'list active';
							}
							else
							{
								li_class = 'list';
							}
						/** NOTE  : sort the li on tag basis **/
						$("#category-list ul").prepend('<li id="tag-id-'+dataNew.tag_id+'" onclick="displaydata('+dataNew.tag_id+');" class="selected '+li_class+'" data-rel="'+dataNew.tag_id+'" >'+
										'<div class="icon">'+icon+'</div>'+
										'<div id="info">'+
											'<div class="tag-info">'+
												'<div class="tag-type">'+dataNew.type+'</div>'+
												'<div class="tag-name">'+dataNew.name+'</div>'+
											'</div>'+
											'<div class="selector-info">'+dataNew.selector+'</div>'+
										'</div>'+
									'</li>');
						/** ==== calling function to fetch and display details of particular tag ====== **/
						displaydata(dataNew.tag_id); 
					},

					error:   function(jqXHR, textStatus, errorThrown) {
						console.log(errorThrown);
						}        
				})
				// =====stop the form from submitting the normal way and refreshing the page==== //
			   event.preventDefault();
			});

				$("#tag_code").bind('blur keyup',function(e) {  
					if (e.type == 'blur' || e.keyCode == '13')  
					{
						if(this.value!='') 
					{
						 $.ajax({
										type: "POST",
										url: "<?php echo JAWS_PATH_WEB; ?>/shuriken/tagmgr/shuriken.tagmgr.tag.type",
										data: {tag_code : this.value},
										cache: false,
										success: function(dataNew)
										{
										  dataNew = JSON.parse(dataNew);
										  $("#tag_type").val(dataNew);
										  if(dataNew == 'html')
										  {
											$('#selector').show();
										  }
										  else
										  {
											$('#selector').hide();
										  }
										}
									});
					}
					// IF THE INPUT WAS JUST FOCUSED AND NOTHING WAS TYPED ALSO, IF AFTER BACKSPACING THE VALUE IS ERASED
					else{}
					}
			  });

		 // ================Edit tag code starts here =============//
		// ==========process the form========== //
			$('#edit-tag').submit(function(event) {
			var form_ele = $("#edit-tag").serialize();
					//  =======process the form========//
				$.ajax({
					type        :  'POST', // define the type of HTTP verb we want to use (POST for our form)
					url         :    "<?php echo JAWS_PATH_WEB; ?>/shuriken/tagmgr/shuriken.tagmgr.tag.edit", //the url where we want to POST
					data        : form_ele, // our data object

					success: function(dataNew) {
						alert('Tags information updated successfully');
					},

					error:   function(jqXHR, textStatus, errorThrown) {
						console.log(errorThrown);
						}        
				})
				// =====stop the form from submitting the normal way and refreshing the page==== //
			   event.preventDefault();
			});

		 // =============== Edit tag Code ends here ==============//
	});

	// ======== common function ============//
		function displaydata(tag_id)
		{
						
			var html = '';
			$(".list").removeClass('selected');
			var url_displaydata = "<?php echo JAWS_PATH_WEB; ?>/shuriken/tagmgr/shuriken.tagmgr.tag.get";
			 $.ajax({
				type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
				url         :    url_displaydata, // the url where we want to POST
				data        : { tag_id : tag_id }, // our data object

				success: function(data) {
					data = JSON.parse(data);
					$("#tag-id-"+tag_id).addClass('selected');
					$(".new-tag").css("display","none");
					$("#tag-edit").css("display","block");
					$("#tag_id").val(data.tag_id);
					$("#edit_tag_name").val(data.name);
					$("#edit_tag_type").val(data.type);

					if ((data.file === null) || (data.file === undefined)) {
						$("#edit_tag_code").val(data.code);
						$('#edit_button').show();
					}
					else {
						$("#edit_tag_code").val('[PROTECTED TAG - VIEW CODE ACCESS RESTRICTED TO DEVELOPERS ONLY]');
						$('#edit_button').hide();
					}

					$("#edit_status").val(data.status);
					$("#edit_parent_container").val(data.parent_container);
					
					if(data.parent_container==null)
					{
						$("#edit_parent_container").html('<option value="">Global</option>');
					}
					else
					{
						var html = '<option value="">Global</option>';
						for(var i =0; i<container.length; i++)
						{
							html += '<option value="'+container[i]['container_id']+'">'+container[i]['name']+'</option>';
						}
						$("#edit_parent_container").html(html);
						$("#edit_parent_container").val(data.parent_container);
					}
					if(data.type == 'html')
					{
						$("#edit-selector").show();
						$("#edit_selector_type").val(data.selector_type);
						$("#edit_selector_name").val(data.selector);
					}
					else
					{
						$("#edit-selector").hide();
					}

				},

				error:   function(jqXHR, textStatus, errorThrown) {
					console.log(errorThrown);
					}        
			});
		}
</script>
</body>
</html>