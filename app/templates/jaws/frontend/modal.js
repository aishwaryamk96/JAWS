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

(function($) {
   	$.fn.changeElementType = function(newType) {
       	var attrs = {};
	
       	$.each(this[0].attributes, function(idx, attr) {
           	attrs[attr.nodeName] = attr.nodeValue;
       	});
	
       	this.replaceWith(function() {
           	return $("<" + newType + "/>", attrs).append($(this).contents());
       	});
   	}
})(jQuery);

$(document).ready(function(){

	$.ajaxSetup({ cache: false });

	var page_curr = 0;
	var page_total = $("div.page").length;
	var progress = 0;

	var btn_later = $("#btn-later");
	var btn_prev = $("#btn-prev");
	var btn_next = $("#btn-next");

	var allow_nav = true;
	var states_ele = $("#select-state").html();

	function refresh_nav_buttons() {

		if (page_curr == 0) {
			btn_later.addClass("active");
			btn_prev.removeClass("active");
			btn_next.removeClass("active");
		}
		else if (page_curr == (page_total - 2)) {
			btn_later.removeClass("active");
			btn_prev.addClass("active");
			btn_next.addClass("active").html("Submit");
		}
		else if (page_curr == (page_total - 1)) {
			btn_later.removeClass("active").css("display","none");
			btn_prev.removeClass("active");
			btn_next.removeClass("active");
			$("#btn-website").addClass("active");
		}
		else {
			btn_later.removeClass("active");
			btn_prev.addClass("active");
			btn_next.addClass("active").html("Next");
		}

	}

	function progress_update() {

		if (progress >= page_curr) return;
		progress = page_curr;

		var progress_px_step = $("div.modal").first().width() / page_total;
		var progress_px = ((page_total * (1 / 5)) * progress_px_step) + (((progress + 1) * (4 / 5))  * progress_px_step);
		
		if ((progress + 1) == page_total) $("div.progress").width(progress_px).addClass("ready");		
		else $("div.progress").width(progress_px);		

	}

	function validate(exec, callback) {

		// Create form data

		var data_phone = $("#txt-phone").val();
		var data_email = $("#txt-email").val();
		var data_age = $("#select-age").val();
		var data_gender = $("#select-gender").val();

		var data_city = $("#txt-city").val();
		var data_zipcode = $("#txt-zipcode").val();
		var data_country = $("#select-country").val();
		var data_state = $("#select-state").val();

		var data_qualification = $("#select-qualification").val();
		var data_experience = $("#select-experience").val();

		var data_why = "";
		$('input[name="chk-why"]:checked').each(function(index) { data_why += $(this).val() + ","; });

		var data_marketing = "";
		$('input[name="chk-marketing"]:checked').each(function(index) { data_marketing += $(this).val() + ","; });

		var data_enquiry = "";
		$('input[name="opt-enquiry"]:checked').each(function(index) { data_enquiry += $(this).val() + ","; });

		var data_sales = "";
		$('input[name="chk-sales"]:checked').each(function(index) { data_sales += $(this).val() + ","; });

		var data_soc = "";
		if ($("#soc-fb").hasClass("active")) data_soc = "fb";
		else if ($("#soc-gp").hasClass("active")) data_soc = "gp";
		else if ($("#soc-li").hasClass("active")) data_soc = "li";

		// Get API Location
		var jaws_webapi_url = $("#txt-jaws-url").val() + "/webapi/user.lms.setup";

		// Step
		var step = page_curr;
		if (exec) step = 0;

		// Validate / Submit
		$.post(jaws_webapi_url,
			{
				step: step,
				phone: data_phone,
				email: data_email,
				age: data_age,
				gender: data_gender,
				city: data_city,
				state: data_state,
				country: data_country,
				zipcode: data_zipcode,
				qualification: data_qualification,
				experience: data_experience,
				why: data_why,
				marketing: data_marketing,
				enquiry: data_enquiry,
				sales: data_sales,
				soc: data_soc
			},

			function(data, status) {
				callback(jQuery.parseJSON(data));				
			}
		);

	}

	function navigate_next_callback(Errs) {

		// Reset errors
		$(".field.error").removeClass("error");
		$("label.error").html("");
		
		// Validation failed !
		if (!Errs["is_valid"]) {
	
			// Show errors
			if ((Errs["phone"] !== undefined) && (Errs["phone"].length > 0)) {
				$("#txt-phone").addClass("error");
				$("label.error[for='phone'").html(Errs["phone"]);
			}

			if ((Errs["age"] !== undefined) && (Errs["age"].length > 0)) {
				$("#select-age").addClass("error");
				$("label.error[for='age'").html(Errs["age"]);
			}

			if ((Errs["email"] !== undefined) && (Errs["email"].length > 0)) {
				$("#txt-email").addClass("error");
				$("label.error[for='email'").html(Errs["email"]);
			}

			if ((Errs["gender"] !== undefined) && (Errs["gender"].length > 0)) {
				$("#select-gender").addClass("error");
				$("label.error[for='gender'").html(Errs["gender"]);
			}

			if ((Errs["city"] !== undefined) && (Errs["city"].length > 0)) {
				$("#txt-city").addClass("error");
				$("label.error[for='city'").html(Errs["city"]);
			}

			if ((Errs["state"] !== undefined) && (Errs["state"].length > 0)) {
				$("#select-state").addClass("error");
				$("label.error[for='state'").html(Errs["state"]);
			}

			if ((Errs["zipcode"] !== undefined) && (Errs["zipcode"].length > 0)) {
				$("#txt-zipcode").addClass("error");
				$("label.error[for='zipcode'").html(Errs["zipcode"]);
			}

			if ((Errs["country"] !== undefined) && (Errs["country"].length > 0)) {
				$("#select-country").addClass("error");
				$("label.error[for='country'").html(Errs["country"]);
			}

			if ((typeof Errs["qualification"] != 'undefined') && (Errs["qualification"].length > 0)) {
				$("#select-qualification").addClass("error");
				$("label.error[for='qualification'").html(Errs["qualification"]);
			}

			if ((typeof Errs["experience"] != 'undefined') && (Errs["experience"].length > 0)) {
				$("#select-experience").addClass("error");
				$("label.error[for='experience'").html(Errs["experience"]);
			}

			if ((typeof Errs["why"] != 'undefined') && (Errs["why"].length > 0)) $("label.error[for='why'").html(Errs["why"]);
			if ((typeof Errs["marketing"] != 'undefined') && (Errs["marketing"].length > 0)) $("label.error[for='marketing'").html(Errs["marketing"]);
			if ((typeof Errs["enquiry"] != 'undefined') && (Errs["enquiry"].length > 0)) $("label.error[for='enquiry'").html(Errs["enquiry"]);
			if ((typeof Errs["sales"] != 'undefined') && (Errs["sales"].length > 0)) $("label.error[for='sales'").html(Errs["sales"]);

			if ((typeof Errs["soc"] != 'undefined') && (Errs["soc"].length > 0)) $("label.error[for='soc'").html(Errs["soc"]);
	
			// Reset UI Nav transitions
			btn_next.removeClass("blocked");
			allow_nav = true;
		}
	
		// Valid ! Proceed with UI Nav
		else {	

			allow_nav = false;
			btn_next.addClass("blocked");	
	
			var page_curr_ele = $("div.page:nth-child(" + (page_curr + 1) + ")");
			var page_next_ele = $("div.page:nth-child(" + (page_curr + 2) + ")");
				
			page_curr_ele.animate({left: "-60vw"}, 650, function() { 
				page_curr_ele.removeClass("active").css("left","0"); 
				btn_next.removeClass("blocked");
				allow_nav = true;
			});
			page_next_ele.css("left","60vw").addClass("active").animate({left: "0"}, 650, function() {});
				
			page_curr ++;
			refresh_nav_buttons();
			progress_update();					
		}

	}

	function social_auth_popup(soc) {

		// Get the URL of the hybrid auth popup view
		var jaws_hybridauth_url = $("#txt-jaws-url").val() + "/socialauth" + "?soc=" + soc;

		// Prep
		var self = this;

		// Show popup
        self.popupWindow = window.open(
            jaws_hybridauth_url,
            "hybridauth_social_sing_on",
            "location=0,status=0,scrollbars=0,width=800,height=500"
        );

        // Check for popup close event
        var winTimer = setInterval(function ()
            {
                if (self.popupWindow.closed !== false)
                {
                    // Remove the timer
                    clearInterval(winTimer);

                    // Validate
                    validate(false, soc_validate_callback); 
                }
            }, 200);
	}

    function soc_validate_callback(Errs) {

    	// Reset errors
		$("label.error[for='soc'").html("");

    	// Get the active soc - validation callback is for this soc
    	var soc_ele = $(".social-radio.active");

    	// Check validation
    	if ((!Errs["is_valid"]) && (typeof Errs["soc"] != 'undefined') && (Errs["soc"].length > 0)) {
    		
    		// reset the UI and show the error
    		$(".social-radio").removeClass("inactive");
    		soc_ele.removeClass("active");
    		$("label.error[for='soc'").html(Errs["soc"]);
    	}

    	else {

    		// Update the UI with the given data - account was associated successfully
    		soc_ele.children("input.soc-info").first().val(Errs["soc_info"]);

    	}
	
	}	

	function navigate_next() {

		if (!allow_nav) return;
		if (page_curr >= (page_total - 1)) return;

		// Validate
		if (page_curr > 0) validate((page_curr == 8) ? true : false, navigate_next_callback);	

		// No validation needed...first page
		else {

			// Proceed with UI Nav
			allow_nav = false;
			btn_next.addClass("blocked");

			var page_curr_ele = $("div.page:nth-child(" + (page_curr + 1) + ")");
			var page_next_ele = $("div.page:nth-child(" + (page_curr + 2) + ")");
				
			page_curr_ele.animate({left: "-60vw"}, 650, function() { 
				page_curr_ele.removeClass("active").css("left","0"); 
				btn_next.removeClass("blocked");
				allow_nav = true;
			});
			page_next_ele.css("left","60vw").addClass("active").animate({left: "0"}, 650, function() {});
				
			page_curr ++;
			refresh_nav_buttons();
			progress_update();	

		}
			
	}

	function navigate_prev() {

		if (!allow_nav) return;
		if (page_curr == 0) return;
		allow_nav = false;
		btn_prev.addClass("blocked");

		var page_curr_ele = $("div.page:nth-child(" + (page_curr + 1) + ")");
		var page_prev_ele = $("div.page:nth-child(" + page_curr + ")");

		page_curr_ele.animate({left: "+60vw"}, 650, function() { 
			page_curr_ele.removeClass("active").css("left","0"); 			
			btn_prev.removeClass("blocked");			
			allow_nav = true;
		});
		page_prev_ele.css("left","-60vw").addClass("active").animate({left: "0"}, 650, function() {});

		page_curr --;
		refresh_nav_buttons();
		progress_update();

	}

	// Event Handlers

	btn_prev.click(function() {
		navigate_prev();
	});

	btn_next.click(function() {
		navigate_next();
	});

	$("#btn-begin").click(function() {
		navigate_next();
	});

	$(".social-radio").click(function() {

		// Set this to active
		$(".social-radio").removeClass("active").addClass("inactive");
        $(this).addClass("active");

        // Get the social ID if we dont have it already
        if (($(".social-radio.active input.soc-info").first().val()).length == 0) social_auth_popup(($(".social-radio.active").first().attr("id")).substr(4,2)); 
        else $("label.error[for='soc'").html("");
       
    });

    $('#select-country').on('change', function (e) {

    	if ($("#select-country").val() == "IND") {
    		$("#select-state").html("");
    		$("#select-state").changeElementType("select");
    		$("#select-state").html(states_ele);
    		/*$("label[for='state']").html("State");*/

    		//$("#txt-city").show();
    		$("#txt-zipcode").show();
    		$("#select-state").show();
    		//$("label[for='city']").show();
    		$("label[for='zipcode']").show();
    		$("label[for='state']").show();
    	}
    	else {
    		$("#select-state").html("");
    		$("#select-state").changeElementType("input");
    		/*$("label[for='state']").html("State / Region / City");*/

    		//$("#txt-city").val("").hide();
    		$("#txt-zipcode").hide().val("");
    		$("#select-state").val("").hide();
    		//$("label[for='city']").hide();
    		$("label[for='zipcode']").hide();
    		$("label[for='state']").hide();
    	}

    	$("label[for='state'].error").html("");
    });

    $("#btn-website").click(function() {
    	$(location).attr('href', 'https://www.jigsawacademy.com');
    });
    
    $("#btn-mind-website").click(function() {
    	$(location).attr('href', 'https://mind-global.com/');
    });
    
    $("#btn-iot").click(function() {
    	$(location).attr('href', 'https://www.jigsawacademy.com/iot/home');
    });

    $("#btn-later").click(function() {

    	if (!confirm("Setting up your access on the Learning Center is necessary before we can grant you access to your course materials. This process takes only a few minutes of your time. We recommend you finish this right away.\n\n'Ok/Yes' to continue the setup process.\n'Cancel/No' to do it later on (Not recommended).")) {

    		// Send email
    		var jaws_webapi_url = $("#txt-jaws-url").val() + "/webapi/user.lms.setup.defer";
			$.post(jaws_webapi_url,
				{
					dummy: "dummy"
				},
	
				function(data, status) {

					// Parse
					var res = jQuery.parseJSON(data);
					if (!res["is_notified"]) alert("Apologies, but due to some technical problems, we could not email you a link to resume this process.");

					// Redir
					else {
						alert("We have sent you an email to resume the setup process. Please finish the process as soon as you can!\n\nYou will now be redirected to our main website.");						
    					$(location).attr('href', 'https://www.jigsawacademy.com');			
    				}
				}
			);
    	}    	
    });

    // Init 
	refresh_nav_buttons();
	
	
});