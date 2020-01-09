$(document).ready(function() {
	$('a').click(function() {
		var a = $(this);
		if (a.data('target') != '#leadmodal') return;
		$('#leadcoursename').html(a.data('course-name'));
		$('#leadcourseid').val(a.data('course-id'));
	});

	$('button#leadsubmit').click(function() {
		var name=$('form#leadform input#leadname').val();
		var email=$('form#leadform input#leademail').val();
		var phone=$('form#leadform input#leadphone').val();

		var flag=true;

		if (!validatePhone_Lead(phone)) {
			$('#leadform-alert-phone').html('Enter a valid phone number.');
			flag=false;
		}
		else $('#leadform-alert-phone').html('');

		if (!email.includes('@')) {
			$('#leadform-alert-email').html('Enter a valid email ID.');
			flag=false;
		}
		else if (!email.includes('.')) {
			$('#leadform-alert-email').html('Enter a valid email ID.');
			flag=false;
		}
		else $('#leadform-alert-email').html('');

		if (name.length<3) {
			$('#leadform-alert-name').html('Enter your full name.');
			flag=false;
		}
		else $('#leadform-alert-name').html('');

		if (flag) {

				var ad_url = window.location.href;
				var course_id = $('#leadcourseid').val();
			  $.post("https://www.jigsawacademy.com/jaws/webapi/iot/leadform", { phone: phone, name: name, email: email , course_id: course_id, url: ad_url}, function (data) {
                        $('#leadmodal').modal("hide");
                  });
		}
	});

	function validatePhone_Lead(txtPhone) {
        var filter = /([0-9]{10})|(\([0-9]{3}\)\s+[0-9]{3}\-[0-9]{4})/;
        if (filter.test(txtPhone)) {
            return true;
        }
        else {
            return false;
        }
    }

});