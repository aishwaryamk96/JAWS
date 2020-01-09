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

var courses = new Array();

$(document).ready(function() {

	$("div.course").click(function() {

		if ($(this).hasClass('active')) $(this).removeClass('active').find("div.mode").removeClass('active');
		else $(this).addClass('active').find("div.mode:not(.disabled)").first().addClass('active');		

		updatePrice();	

	});

	$("div.mode:not(.disabled)").click(function(event) {
		event.stopPropagation();

		if ($(this).hasClass("active")) $(this).removeClass('active').closest('div.course').removeClass('active');
		else {
			if ($(this).closest('div.course').hasClass('active')) $(this).addClass('active').siblings('div.mode').removeClass('active');
			else $(this).addClass('active').closest('div.course').addClass('active');
		}

		updatePrice();	
	});

	$("div.course > div.text > div.name > a").click(function(event) {
		event.stopPropagation();
	})

	$("a#btn-wns").click(function() {

		//Price check
		var tol = updatePrice();
		var dis = Math.ceil(tol * 0.25);
		var tax = Math.ceil((tol  - dis) * 0.145);
		var nett = tol - dis + tax;

		if (tol == 0) {
			alert("Please select the courses you want to enroll in.");
			return;
		}

		//Prep
		var name = $("input#txt-name").val();
		var phone = $("input#txt-phone").val();
		var email = ($("input#txt-email").val()).toLowerCase();
		var city = $("input#txt-city").val();
		var country = $("input#txt-country").val();
		var empid = $("input#txt-emp-id").val();

		// Validate
		var proceed = true;

		if ((name.length < 3) || (name.indexOf(' ') == -1)) {
			$("label.error[for='txt-name']").html("Enter your full name.");
			$("input#txt-name").addClass('error');
			proceed = false;
		}
		else {
			$("label.error[for='txt-name']").html("");
			$("input#txt-name").removeClass('error');
		}	

		if ((phone.length < 6) || (phone.length > 15)) {
			$("label.error[for='txt-phone']").html("Enter a valid phone number.");
			$("input#txt-phone").addClass('error');
			proceed = false;
		}
		else {
			$("label.error[for='txt-phone']").html("");
			$("input#txt-phone").removeClass('error');
		}	

		if (city.length < 3) {
			$("label.error[for='txt-city']").html("Enter your city.");
			$("input#txt-city").addClass('error');
			proceed = false;
		}
		else {
			$("label.error[for='txt-city']").html("");
			$("input#txt-city").removeClass('error');
		}	

		if (country.length < 3) {
			$("label.error[for='txt-country']").html("Enter your country.");
			$("input#txt-country").addClass('error');
			proceed = false;
		}
		else {
			$("label.error[for='txt-country']").html("");
			$("input#txt-country").removeClass('error');
		}	

		if ((email.length < 9) || (email.indexOf('@wns.com') == -1)) {
			$("label.error[for='txt-email']").html("Enter your valid WNS email ID.");
			$("input#txt-email").addClass('error');
			proceed = false;
		}
		else {
			$("label.error[for='txt-email']").html("");
			$("input#txt-email").removeClass('error');
		}	

		if (empid.length < 1) {
			$("label.error[for='txt-emp-id']").html("Enter your WNS employee ID.");
			$("input#txt-emp-id").addClass('error');
			proceed = false;
		}
		else {
			$("label.error[for='txt-emp-id']").html("");
			$("input#txt-emp-id").removeClass('error');
		}	

		// Proceed
		if (proceed == false) {
			alert("There are some errors in your personal details!");
			return;
		}

		// tnc Check
		if (($('input#ichk-tc').is(':checked')) == false) {
			alert("You must accept the Terms & Conditions.");
			return;
		}

		if (($('input#ichk-pp').is(':checked')) == false) {
			alert("You must accept the Privacy Policy.");
			return;
		}

		var soc = "n";
		if ($('input#ichk-soc').is(':checked') == true) soc = "y";

		// Get API Location
		var jaws_webapi_url = $("#txt-jaws-url").val() + "/webapi/wns/paylink.request";

		// Validate / Submit
		$.ajax({
  			type: "POST",
  			url: jaws_webapi_url,
  			data: {
				name: name,
				phone: phone,
				email: email,				
				city: city,
				country: country,				
				empid: empid,
				soc: soc,
				courses: courses,
				sum_total: tol,
				nett_total: nett
  			},

  			success: function(data) {
        		var ret = jQuery.parseJSON(data);		
				if (ret["status"]) $(location).attr('href', 'https://www.jigsawacademy.com/jaws/wns/success');
				else alert("There was a problem :(\n" + ret["msg"]);
  			},
  
  			error:   function(jqXHR, textStatus, errorThrown) {
        		$(location).attr('href', 'https://www.jigsawacademy.com/jaws/wns/success');
        	}        
  		});	

		setTimeout(function(){ $(location).attr('href', 'https://www.jigsawacademy.com/jaws/wns/success'); }, 2000);

	});

	$('input').change(function() {
		$(this).removeClass('error');
		$("label.error[for='" + $(this).attr('id') + "']").html("");
	});

	function updatePrice() {
		var tol = 0;
		courses = new Array();
		
		$("div.course.active div.mode.active").each(function() {
			tol += parseInt($(this).data("price"));
			
			var name = $(this).closest("div.course.active").data("name");
			var mode = $(this).hasClass("sp") ? "Regular" : "Premium";
			courses.push(new Array(name, mode));
		});

		var dis = Math.ceil(tol * 0.25);
		var tax = Math.ceil((tol  - dis) * 0.145);
		var nett = tol - dis + tax;

		$("span#total").html(tol);
		$("span#dis").html(dis);
		$("span#tax").html(tax);
		$("span#nett").html(nett);	

		return tol;	
	}

	// Attach Scroll Handler dynamically
	if (document.addEventListener) {
    		document.addEventListener("mousewheel", scrollHandler(), false);
    		document.addEventListener("DOMMouseScroll", scrollHandler(), false);
    		document.addEventListener("scroll", scrollHandler(), false);
	} else {
    		sq.attachEvent("onmousewheel", scrollHandler());
	}

	function scrollHandler() {
		return function (e) {
			var height = $(window).height();
			var width = $(window).width();
			var scrtop = $("body").scrollTop();
	
			var target_Y = scrtop;
			var offset = $("div#course-selector").offset();
	
			if (target_Y < offset.top - 50) {
				$("div#pricing").removeClass("sticky");
				$("div#pricing").removeClass("sticky2");
			}
			else if (target_Y > offset.top + $("div#course-selector").height() - $("div#pricing").height() - 50) {
				$("div#pricing").removeClass("sticky");
				$("div#pricing").addClass("sticky2");
			}
			else {
				$("div#pricing").addClass("sticky");
				$("div#pricing").removeClass("sticky2");
			}
	
			return false;
		}
	}

});