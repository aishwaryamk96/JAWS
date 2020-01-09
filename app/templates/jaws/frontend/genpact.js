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

var gateway_inr = "https://secure.ebs.in/pg/ma/billing/cart/action/add/?accountId=089d24462fdf4565642728e609db8a7cMTA0NzQ=&prodName=GENPACT_ISAS_INR_2016&prodPrice=34500&prodQty=1&shopURL=aHR0cDovL2ppZ3Nhd2FjYWRlbXkuY29t&shippingUnits=1";

$(document).ready(function() {

	$("a#btn-genpact").click(function() {

		//Pay Mode
		var paymode = "";

		//Prep
		var name = $("input#txt-name").val();
		var phone = $("input#txt-phone").val();
		var email = ($("input#txt-email").val()).toLowerCase();
		var email_alt = ($("input#txt-email-alt").val()).toLowerCase();
		var empid = $("input#txt-emp-id").val();
		var city = $("input#txt-city").val();
		var country = $("input#txt-country").val();
		var office = $("input#txt-office").val();

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

		if ((email.length < 9) || ((email.indexOf('@genpact.com') == -1) && (email.indexOf('@centrica.com') == -1))) {
			$("label.error[for='txt-email']").html("Enter your valid GENPACT/CENTRICA email ID.");
			$("input#txt-email").addClass('error');
			proceed = false;
		}
		else {
			$("label.error[for='txt-email']").html("");
			$("input#txt-email").removeClass('error');
		}	

		if (email_alt.length < 9) {
			$("label.error[for='txt-email-alt']").html("Enter a valid communication email ID.");
			$("input#txt-email-alt").addClass('error');
			proceed = false;
		}
		else {
			$("label.error[for='txt-email-alt']").html("");
			$("input#txt-email-alt").removeClass('error');
		}	

		if (empid.length < 1) {
			$("label.error[for='txt-emp-id']").html("Enter your GENPACT OHR ID.");
			$("input#txt-emp-id").addClass('error');
			proceed = false;
		}
		else {
			$("label.error[for='txt-emp-id']").html("");
			$("input#txt-emp-id").removeClass('error');
		}	

		if (office.length < 1) {
			$("label.error[for='txt-office']").html("Enter your GENPACT office location.");
			$("input#txt-office").addClass('error');
			proceed = false;
		}
		else {
			$("label.error[for='txt-office']").html("");
			$("input#txt-office").removeClass('error');
		}	

		// Proceed
		if (proceed == false) {
			alert("There are some errors in your personal details!");
			return;
		}

		// Paymode check
		if ($('input#iopt-inr').is(':checked') == true) paymode = "inr";
		else if ($('input#iopt-usd').is(':checked') == true) paymode = "usd";
		else {
			alert("Please select the mode of payment.");
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

		// Get API Location
		var jaws_webapi_url = $("#txt-jaws-url").val() + "/webapi/genpact/paylink.external.create";

		// Validate / Submit
		$.ajax({
  			type: "POST",
  			url: jaws_webapi_url,
  			data: {
				name: name,
				phone: phone,
				email: email,
				email_alt: email_alt,				
				city: city,
				office: office,
				country: country,				
				empid: empid,
				paymode: paymode
  			},

  			success: function(data) {
        				var ret = jQuery.parseJSON(data);		
				if (ret["status"]) pay();
				else alert("There was a problem :(\n" + ret["msg"]);
  			},
  
  			error:   function(jqXHR, textStatus, errorThrown) {
        				pay();
        			}        
  		});	

		// Fallback - do not prevent payment!
		setTimeout(function(){ pay(); }, 2500);

	});

	$('input').change(function() {
		$(this).removeClass('error');
		$("label.error[for='" + $(this).attr('id') + "']").html("");
	});	

	function pay() {
		if ($('input#iopt-inr').is(':checked') == true) $(location).attr('href', gateway_inr);
		else {
			alert("Thanks for sharing your details. If you have not already made the payment, please use the payment link that has been shared with you in your email.");
			$(location).attr('href', "https://www.jigsawacademy.com/");
		}
	}

});