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
   	load_module('ui');

	// Init Session
	auth_session_init();

	// Prep
	$login_params["return_url"] = JAWS_PATH_WEB."/shuriken/tagmgr";

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

	$container_all = shuriken_tagmgr_container_get_all();
	?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Shuriken - Tag Manager - Containers</title>
		<meta name="author" content="Nikita">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<link rel="icon" type="image/png" href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL.'/media/jaws/frontend/images/favicon.png'; ?>">
		<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL.'/common/fa/css/font-awesome.css'; ?>">
		<link rel="stylesheet" type="text/css" href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL."/app/templates/jaws/backend/temp/shuriken.tagmgr.css"; ?>" />
		<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	</head>
	<body>
		<div class="wrapper">
			<div id="main-container">
				<div id="header"><img src="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL.'/media/jaws/backend/jigsaw_horizontal_logo.png'; ?>">
				<div class="header-buttons">
				<a href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL."/shuriken/tagmgr"; ?>">Add New Container </a>
				<a href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL."/shuriken/tagmgr/tags"; ?>">Add New Tag </a>
			
				</div>
				</div>
				<div class="wrapper">
					<!-- ==============category-container listing here============== --> 
					<div id="category-list">
					<div class="cont title" style="text-align:center;  color:#4174e0;font-size: 1.2em;background: #ededed;padding: 7px 0;">Container list</div>
					
						<ul>
						<?php foreach($container_all as $key=>$value) : ?>
							<li  id="cont-id-<?php echo $value['container_id']; ?>"  onclick="displaydata(<?php echo $value['container_id']; ?>);" <?php echo ($value['status']=='enabled') ? 'class="list active"' : 'class="list"' ?>  >
								<div class="icon"><i class="fa fa-cube fa-fw" aria-hidden="true"></i></div>
								<div id="info">
									<div class="cat-info">
										<div class="category-name"><?php echo $value['category']; ?></div>
										<div class="container-name"><?php echo $value['name']; ?></div>
									</div>
									<div class="url-info"><?php echo $value['template_str']; ?></div>
								</div>
							</li>
						<?php endforeach; ?>
							
						</ul>
					</div>

					<!-- =========ADD/EDIT CONTAINER FORM========== -->
					<div id="container-form">
						<div class="new-container" align="center">
							<div class="cont container-heading" align="center">Add new  container form</div>
							<form id="add-container" name="add-container" action="" method="post" >
								<!--<div class="cont">
									<div class="cont-name">
									<label class="form-label">Status : </label>
										<select class="form-input" name="status" id="status">
											<option value="disabled">Disabled</option>
											<option value="enabled">Enabled</option>
										</select>
									</div>
									<div class="cont-cat"></div>
								</div>-->

								<div class="cont">
									<div class="cont-name">
										<label class="form-label">Name : </label>
										<input class="form-input" type="text" name="container_name" required="true" />
									</div>
									<div class="cont-cat">
										<label class="form-label">Category : </label>
										<input class="form-input" type="text" name="container_category" required="true" />
									</div>
								</div>

								<div class="cont">
									<label class="form-label">Url : </label>
									<input type="text" name="container_url" id="container_url" required="true"  />
								</div>

								<div class="cont">
									<label  class="form-label"></label>
									<div style='margin-bottom: 15px;'>Use the following URL templating rules</div>
									<ul class="cont-note">
										<li>Rule {} - In path : Segment MUST be present with ANY value, In query string : param MUST be present with ANY value.</li>
										<li>Rule {:} - In path : Segment MIGHT be present with ANY value, In query string : not applicable.</li>
										<li>Rule {!} - In path : Segment CANNOT be present, In query string : param CANNOT be present.</li>
										<li>Rule else/exact - In path : not applicable, In query string : param MUST be present with EXACT value.</li>
									</ul>
								</div>

								<div class="cont">
									<label class="form-label">Parsed Url : </label>
									<input type="text" id="parsed_url" name="parsed_url" readonly="readonly" />
								</div>

								<div class="cont add">
									<input type="submit" name="submit" id="add_button" value="submit" disabled="true" />
								</div>
							</form>
						</div>

						<!--  =============Edit container form =========  - -->
						<div id="container-edit" align="center">
							<div class="cont container-heading" align="center">Edit container form</div>
							<form id="edit-container" name="edit-container" action="" method="post" >
							<input type="hidden" name="container_id" id="container_id" />
							<input type="hidden" name="url_template" id="url_template_id" />
								<div class="cont">
									<div class="cont-name">
									<label class="form-label">Status : </label>
										<select class="form-input" name="edit_status" id="edit_status">
											<option value="disabled">Offline</option>
											<option value="enabled">Online</option>
										</select>
									</div>
									<div class="cont-cat"></div>
								</div>
								<div class="cont">
									<div class="cont-name">
										<label class="form-label">Name : </label>
										<input class="form-input" type="text" name="edit_container_name" id="edit_container_name" required="true" value="" />
									</div>
									<div class="cont-cat">
										<label class="form-label">Category : </label>
										<input class="form-input" type="text" name="edit_container_category" id="edit_container_category" required="true" value=""  />
									</div>
								</div>

								<div class="cont">
									<label class="form-label">Url : </label>
									<input type="text" name="edit_container_url" id="edit_container_url" required="true" value=""   />
								</div>

								<div class="cont">
									<label  class="form-label"></label>
									<div style='margin-bottom: 15px;'>Use the following URL templating rules</div>
									<ul class="cont-note">
										<li>Rule {} - In path : Segment MUST be present with ANY value, In query string : param MUST be present with ANY value.</li>
										<li>Rule {:} - In path : Segment MIGHT be present with ANY value, In query string : not applicable.</li>
										<li>Rule {!} - In path : Segment CANNOT be present, In query string : param CANNOT be present.</li>
										<li>Rule else/exact - In path : not applicable, In query string : param MUST be present with EXACT value.</li>
									</ul>
								</div>

								<div class="cont">
									<label class="form-label">Parsed Url : </label>
									<input type="text" id="edit_parsed_url" name="edit_parsed_url" readonly="readonly" />
								</div>

								<div id="tags-container">
									<ul id="sortable"></ul>
								</div>

								<div class="cont add">
									<input type="button" name="add_tag" id="add_tag" value="Add Tag" />
									<input type="submit" name="update" id="edit_button" value="Update" disabled="true" />
								</div>
							</form>
						</div>

						<!-- ==============Edit container form ends ====== -->
					</div>
					<!-- ===========Tags listing here========== -->

				</div>
			</div>
			
		</div><?php require 'add_tag.php'; ?>	


	<script>
	var selected_tags_array = [];
	$(document).ready(function() {
		
		// ========================== ADD CONTAINER CODE STARTS HERE ====================== //
		var url = "<?php echo JAWS_PATH_WEB; ?>/shuriken/tagmgr/shuriken.tagmgr.container.create";
		// ==========process the form========== //
			$('#add-container').submit(function(event) {
				var formData = $('form').serialize();
				//  =======process the form========//
				$.ajax({
					type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
					url         :    url, // the url where we want to POST
					data        : formData, // our data object

					success: function(data) {
						data = JSON.parse(data);
						var active = '';
						$("#add-container").trigger('reset'); // reset the form fields
						$("#category-list ul li").removeClass('selected');
						if(data.status == 'enabled')
						{
							active = 'active';
						}
						// ======append the added container to the list in left hand side menu [ category list ]===== //

						/** NOTE  : sort the li on category basis **/
						$("#category-list ul").prepend('<li class="selected '+active+' ">'+
									'<div class="icon"><i class="fa fa-cube fa-fw" aria-hidden="true"></i></div>'+
									'<div id="info">'+
										'<div class="cat-info">'+
											'<div class="category-name">'+data.category+'</div>'+
											'<div class="container-name">'+data.name+'</div>'+
										'</div>'+
										'<div class="url-info">'+data.template_str+'</div>'+
									'</div>'+
								'</li>');
						
						displaydata(data.container_id);
					},

					error:   function(jqXHR, textStatus, errorThrown) {
						console.log(errorThrown);
						}        
				})
				// =====stop the form from submitting the normal way and refreshing the page==== //
			   event.preventDefault();
			});

		// =======url error detection for add container====== //
			$("#container_url").bind('blur keyup',function(e) {  
					if (e.type == 'blur' || e.keyCode == '13')  
					{
						if(this.value!='') 
				{
					 $.ajax({
									type: "POST",
									url: "<?php echo JAWS_PATH_WEB; ?>/shuriken/tagmgr/shuriken.tagmgr.url.validate",
									data: {url_value : this.value},
									cache: false,
									success: function(dataNew)
									{
											dataNew = JSON.parse(dataNew);
											if(dataNew.status!=true || dataNew.status!=1)
											{	
												$("#add_button").attr("disabled", true);
												$(".cont-note").html('');
												if(dataNew.error.params)
												{
													$(".cont-note").append('<li class="error">'+dataNew.error.params+'</li>');
												}
												if(dataNew.error.fragment)
												{
													$(".cont-note").append('<li class="error">'+dataNew.error.fragment+'</li>');	
												}
											}
											else{
												$(".cont-note").html('<li><span>Note : </span> Enter the url following the rules .</li>'+
													'<li>Lorem Ipsum is a  dummy text. Enter the url following the rules .</li>'+
															'<li>Lorem Ipsum is a  dummy text.</li>');

												$("#add_button").attr("disabled", false);
											}
											$("#parsed_url").val(dataNew.parsed_url);
									}
								});
				}
				// IF THE INPUT WAS JUST FOCUSED AND NOTHING WAS TYPED ALSO, IF AFTER BACKSPACING THE VALUE IS ERASED
				else{
					$("#add_button").attr("disabled", true);
					$(".cont-note").html('<li><span>Note : </span> Enter the url following the rules .</li>'+
												'<li>Lorem Ipsum is a  dummy text. Enter the url following the rules .</li>'+
								'<li>Lorem Ipsum is a  dummy text.</li>');
					$("#parsed_url").val('');
				}
					}
			});  

			// ========================== ADD CONTAINER CODE ENDS HERE====================== //

			// ========================== EDIT CONTAINER CODE STARTS HERE====================== //

			var urledit = "<?php echo JAWS_PATH_WEB; ?>/shuriken/tagmgr/shuriken.tagmgr.container.edit";
		// ==========process the form========== //
			$('#edit-container').submit(function(event) {
				 var form_elements = {
						'container_id' : $("#container_id").val(),
						'edit_container_name' : $("#edit_container_name").val(),
						'edit_container_category' : $("#edit_container_category").val(),
						'edit_container_url' : $("#edit_container_url").val(),
						'edit_status' : $("#edit_status").val(),
						'url_template': $("#url_template_id").val(),
				 };
				  var tag_ids = [];
			
			$("#tags-container ul li").each(function(i, ele) {
				var obj = {};
                               	obj[$(this).data('rel')] = $(this).data('state');
                               	tag_ids.push(obj);
				selected_tags_array.push($(this).data('rel'));
			});
				//  =======process the form========//
				$.ajax({
					type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
					url         :    urledit, // the url where we want to POST
					data        : { form_ele : form_elements , tag_ids : tag_ids}, // our data object

					success: function(dataNew) {
						alert('Container updated successfully');
					},

					error:   function(jqXHR, textStatus, errorThrown) {
						console.log(errorThrown);
						}        
				})
				// =====stop the form from submitting the normal way and refreshing the page==== //
			   event.preventDefault();
			});


			// =========url error detection for edit container======= //
			$("#edit_container_url").bind('blur keyup',function(e) 
			{   
					if (e.type == 'blur' || e.keyCode == '13')  
					{
						if(this.value!='') 
				{
					 $.ajax({
										type: "POST",
										url: "<?php echo JAWS_PATH_WEB; ?>/shuriken/tagmgr/shuriken.tagmgr.url.validate",
										data: {url_value : this.value},
										cache: false,
										success: function(dataNew)
										{
												dataNew = JSON.parse(dataNew);
												if(dataNew.status!=true || dataNew.status!=1)
												{	
													$("#edit_button").attr("disabled", true);
													$(".cont-note").html('');
													if(dataNew.error.params)
													{
														$(".cont-note").append('<li class="error">'+dataNew.error.params+'</li>');
													}
													if(dataNew.error.fragment)
													{
														$(".cont-note").append('<li class="error">'+dataNew.error.fragment+'</li>');	
													}
												}
												else{
													$(".cont-note").html('<li><span>Note : </span> Enter the url following the rules .</li>'+
														'<li>Lorem Ipsum is a  dummy text. Enter the url following the rules .</li>'+
																'<li>Lorem Ipsum is a  dummy text.</li>');

													$("#edit_button").attr("disabled", false);
												}
												$("#edit_parsed_url").val(dataNew.parsed_url);
										}
										});
				}
				// IF THE INPUT WAS JUST FOCUSED AND NOTHING WAS TYPED ALSO, IF AFTER BACKSPACING THE VALUE IS ERASED
				else
				{
					$("#edit_button").attr("disabled", true);
					$(".cont-note").html('<li><span>Note : </span> Enter the url following the rules .</li>'+
													'<li>Lorem Ipsum is a  dummy text. Enter the url following the rules .</li>'+
															'<li>Lorem Ipsum is a  dummy text.</li>');
					$("#edit_parsed_url").val('');
				}
				   }
			}); 


			$("div#tag-modal div.overlay.close").click(function(){
		 $("body > div.wrapper").removeClass('blur');
		 $(this).closest("div#tag-modal").removeClass("active");
		});
	
		$("div#tag-modal div.close > i.fa").click(function() {
		 $("body > div.wrapper").removeClass('blur');
		 $(this).closest("div#tag-modal").removeClass("active");
		});

		// ===========tags modal================= //
		$("#add_tag").click(function() {
			 $.ajax({
								type: "POST",
								url: "<?php echo JAWS_PATH_WEB; ?>/shuriken/tagmgr/shuriken.tagmgr.tag.get.all.by.parent",
								data: {container_id :$("#container_id").val() },
								cache: false,
								success: function(dataNew)
								{
									$("#tags-list ul").html('');
									var html = '';
									 var li_class; var icon; var selector; var disabled_icon; var checked ;
									dataNew = JSON.parse(dataNew);
									for(i = 0; i < dataNew.length; i++)
									{		
										if(dataNew[i]['status']=='enabled')
										{
											li_class = ' class="list active" ';
										}
										else
										{
											li_class = ' class="list" ';
										}
										if(dataNew[i]['status'] == 'disabled')
										{
											disabled_icon = '<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>';
										}
										else {disabled_icon = '';}
										 if(dataNew[i]['parent_container']!='') 
										 { 
								icon = '<i class="fa fa-folder-open" aria-hidden="true"></i>';
							} 
							else 
							{ 
								icon =  '<i class="fa fa-globe" aria-hidden="true"></i>';
							} 
							if(dataNew[i]['type'] == 'html')
							{  selector = dataNew[i]['selector']; }
							else
							{ selector =  '...'; }
							if(jQuery.inArray(dataNew[i]["tag_id"], selected_tags_array) !== -1){ 
								 checked = ' <i  style="color:green;" class="fa fa-check" aria-hidden="true"></i> ';
								 var css_prop = 'style="pointer-events:none;"';
							 }
							 else
							 {
								checked = '';
								var css_prop = '';
							 }

						html += '<li data-state="'+dataNew[i]["status"]+'" '+css_prop+'data-rel="'+dataNew[i]["tag_id"]+'" '+li_class+ '>'+
							'<div class="icon">'+icon+'</div>'+
							'<div id="info">'+
								'<div class="tag-info">'+
									'<div class="tag-type">'+dataNew[i]['type']+'</div>'+
									'<div class="tag-name">'+dataNew[i]['name']+'</div>'+disabled_icon+checked+
								'</div>'+
								'<div class="selector-info">'+selector+'</div>'+
							'</div>'+
						'</li>';
					}
					$("#tags-list ul").html(html);
									$("#tag-modal").addClass("active");
									$("body > div.wrapper").addClass('blur');
									$( "#tags-list ul li" ).bind( "click", function() {
						$(this).toggleClass( "selected" );
					});
								}
								});
		});

	});

	// ======== common function ============//
		function displaydata(c_id)
		{
			selected_tags_array.length = 0;
			$(".list").removeClass('selected');
			var url_displaydata ="<?php echo JAWS_PATH_WEB; ?>/shuriken/tagmgr/shuriken.tagmgr.container.get";
			 $.ajax({
				type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
				url         :    url_displaydata, // the url where we want to POST
				data        : { container_id : c_id }, // our data object

				success: function(data) {
					data = JSON.parse(data);
					$("#cont-id-"+c_id).addClass('selected');
					$(".new-container").css("display","none");
					$("#container-edit").css("display","block");
					$("#edit_container_name").val(data[0].name);
					$("#edit_container_category").val(data[0].category);
					$("#edit_container_url").val(data[0].template_str);
					$("#container_id").val(data[0].container_id);
					$("#url_template_id").val(data[0].url_template);
					$("#edit_status").val(data[0].status);
					$("#edit_parsed_url").val('');
					if(data.length > 0)
					{	
						var html_li = '';
						var classname = '';
						var str = '';
						var j = 0;
						$("#tags-container ul").html('');
						for(var i =1; i<data.length;i++)
						{
							selected_tags_array[j] = data[i]["tag_id"];
							j=j+1;
							if(data[i]['assoc_status'] == 'enabled')
							{ classname = 'class="list active ui-state-default"'; }
							else
							{ classname = 'class="list ui-state-default"'; }

							 if(data[i]["parent_container"] !="") 
							 {   str = '<i class="fa fa-folder-open" aria-hidden="true"></i>'; }
							else 
							{  str = '<i class="fa fa-globe" aria-hidden="true"></i>' ; }

							 if(data[i]['type'] == "html" )
							 { type = data[i]['selector']; }
					 else { type = '...'; }
					
					if(data[i]['tag_status'] == 'disabled')
									{
										var exclaim = '<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>';
									}
									else {  var exclaim = '';}				
							html_li += '<li data-state="'+data[i]['assoc_status']+'" data-rel="'+data[i]["tag_id"]+'" '+classname+' >'+
								'<div class="icon">'+str+'</div>'+
								'<div id="info">'+
									'<div class="tag-info">'+
										'<div class="tag-type">'+data[i]['type']+'</div>'+
										'<div class="tag-name">'+data[i]['name']+'</div>'+exclaim+
										'<i class="fa fa-times" onclick="remove_tag(this)" data-tag-id="'+data[i]["tag_id"]+'" aria-hidden="true"></i>'+
										'<input value="'+data[i]['assoc_status']+'" type="button" name="state" data-state="'+data[i]['assoc_status']+'" onclick="tag_status(this)" />'+
									'</div>'+
									'<div class="selector-info">'+type+'</div>'+
								'</div>'+
							'</li>';
						}
						$("#tags-container ul").append(html_li); 
					}
				},

				error:   function(jqXHR, textStatus, errorThrown) {
					console.log(errorThrown);
					}        
			});
		}


		// ===== add value of  selected tags from modal ===== //
		function add_to_container()
		{	
			var count = 0;
			$("#tags-list ul li").each(function() 
			{
			if( $(this).hasClass("selected") )
			{	
				count = count + 1;
				$(this).clone().appendTo( "#tags-container ul" );
				$("body > div.wrapper").removeClass('blur');
				$(this).closest("div#tag-modal").removeClass("active");
			}
		});
		if(count == 0)
		{
			alert('Select atleast one tag to be added to  container');
		}
		}

	$( function() {
		$( "#sortable" ).sortable();
		$( "#sortable" ).disableSelection();
	});

	/** remove associated tag **/
	function remove_tag(tag){
		var tagid =  $(tag).attr('data-tag-id');
		if( confirm('Are you sure you want to  remove tag from this container?'))
		{
			 $("ul#sortable li[data-rel="+tagid+"]").remove();
			 $("#tags-list ul li[data-rel="+tagid+"] div#info div.tag-info i.fa-check").remove();
		}
		else
		{}
		
	}

	function tag_status(data)
	{
		if(data.value == 'enabled')
		{
			$(data).val('disabled');
			$(data).parent().closest('li').attr('data-state', 'disabled');
			$(data).parent().closest('li').removeClass('active');
		}
		else
		{
			$(data).val('enabled');	
			$(data).parent().closest('li').attr('data-state', 'enabled');
			$(data).parent().closest('li').addClass('active');
		}
	}
	
	</script>
</body>
</html>