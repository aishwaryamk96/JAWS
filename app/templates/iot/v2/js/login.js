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

$(document).ready(function(){	

	$(".social-radio").click(function() {

		// Set this to active
		$(".social-radio").removeClass("active").addClass("inactive");
		$(this).addClass("active");

		// Get the URLs
		var login_url = $("#login_url").val();
		var return_url = $("#return_url").val();
		var reauth = $("#reauth").val();
		var wplogin_url = $("#wplogin_url").val();
		var return_url_2 = $(this).data('ru');
		var verify = $(this).data('verify');

		// Form redirect URL
		//var redir = login_url + '?soc=' + ($(".social-radio.active").first().attr("id")).substr(4,2) + "&return_url=" + return_url + encodeURIComponent("?return_url_2=" + encodeURIComponent(encodeURIComponent(return_url_2)) + "&verify=" + verify) + "&reauth=" + reauth;

		var redir = login_url + '?soc=' + ($(".social-radio.active").first().attr("id")).substr(4,2) + "&return_url=" + wplogin_url + "?redir=" + encodeURIComponent(return_url + encodeURIComponent(encodeURIComponent("?return_url_2=" + encodeURIComponent(return_url_2 + "&soc=" + ($(".social-radio.active").first().attr("id")).substr(4,2)) + "&verify=" + verify) + "&pre=soc_to_session&pre_param=" + ($(".social-radio.active").first().attr("id")).substr(4,2))) + "&reauth=" + reauth;

		// Redirect to return URL
		$(location).attr('href', redir);
			 
	});

	$('a').click(function() {
		if (!$(this).data('ru')) return;
		$(".social-radio").data('ru', $(this).data('ru'));
		$(".social-radio").data('verify', $(this).data('verify'));
		$('button.update-account-form-button').data('ru', $(this).data('ru'));
	});
	
});