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

$(document).ready(function() {

	$("a#btn-dsb").click(function() {

		//Pay Mode
		var paymode = "";

		//Prep
		var name = $("input#txt-name").val();;
		var email = ($("input#txt-email").val()).toLowerCase();

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

		if (email.length < 9) {
			$("label.error[for='txt-email']").html("Enter your login email ID.");
			$("input#txt-email").addClass('error');
			proceed = false;
		}
		else {
			$("label.error[for='txt-email']").html("");
			$("input#txt-email").removeClass('error');
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

		// Prep Course name
		var course_code = $('#txt-course-code').val();

		// Submit by GET request
		window.location.href= $("#txt-jaws-url").val() + "/ipba/pay?" +
		 	"name=" + encodeURIComponent(name) + "&" +
		 	"email=" + encodeURIComponent(email) + "&" +
		 	"course_code=" + encodeURIComponent(course_code) + "&" +
			"paymode=" + encodeURIComponent(paymode);

	});

	$('input').change(function() {
		$(this).removeClass('error');
		$("label.error[for='" + $(this).attr('id') + "']").html("");
	});

});