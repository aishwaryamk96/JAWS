<?php
if(auth_session_is_logged() && !empty($_SESSION['user']['phone']) && isset($_GET['return_url_2'])){
    ob_end_clean();
    header('Location: '.urldecode($_GET['return_url_2']));
    die();
}
?>

<!-- Modal -->
<div id="profile-popup1" class="modal fade" role="dialog">
      <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                  <!--div class="modal-header" -->
                        <!--  <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                      <!--   <h3 class="modal-title"></h3> -->
                  <!-- /div -->
                  <div class="modal-body">
                        <div class="popupbox-inner">
                              <div class="box-with-border no-border">
                                  <h3>ACCOUNT INFORMATION </h3>
                                  <div class="clearfix information-form-account">
                                          <form id="iot_profile_form" method="post" class="jigsawforms"  >
                                              <div class="appform-left-box clearfix">
                                                    <div class="left-image-info-form">
                                                        <div class="image-main">
                                                              <img src="<?php echo $_SESSION["user"]["photo_url"]; //socialImage($userid );?>" id="profilejigsaw">
                                                              <!-- <span class="profile-edit-icon"><img src="<?php //echo get_image('profile-pic-edit-icon.jpg');?>?v=0.1"><input type="file" name="fileupload" id="fileupload" accept="image/*"></span> -->
                                                        </div>
                                                          <!--  <a href="<?php //print wp_nonce_url(site_url('social-login?logout=true&ru='.urlencode(home_url(add_query_arg(array(),$wp->request)))), 'signoutjigsaw', 'jigsaw_signout');?>" class="pull-left" style="padding-top: 10px;">
                                                          <img src="<?php //echo get_image("logo_logout.png"); ?>" alt="Sign out" title="Sign Out" style="padding-right: 5px;"/>Sign Out</a> -->
                                                      </div>
                                                    <div class="form-with-field font-lato">
                                                            <input type="hidden" name="return_url_2" value="<?php echo ((isset($_GET['return_url_2']) && !empty($_GET['return_url_2']))? $_GET['return_url_2'] : ''); ?>">
                                                            <input type="hidden" name="verify_otp" value="<?php echo ((isset($_GET['verify']) && !empty($_GET['verify']))? $_GET['verify'] : ''); ?>">
                                                            <div class="field-main">
                                                                    <label class="font-lato" for="display_name">NAME</label>
                                                                    <input type="text" name="display_name" placeholder="Name" id="display_name" value="<?php echo $_SESSION["user"]["name"];//$user_info->display_name;?>" class="field" disabled >
                                                            </div>
                                                            <div class="field-main">
                                                                  <label for="email"> EMAIL </label>
                                                                  <input type="text" class="field" id="email" value="<?php echo $_SESSION["user"]["email"];//$user_info->user_email;?>" disabled>
                                                                  <!-- EDIT !!!!!!!!!!!!!!!!!! -->
                                                                  <span id='p5EmailId' style='display:none;'><?php echo $_SESSION["user"]["email"]; ?></span>
                                                                  <!-- EDIT End !!!!!!!!!!!!!!!!!-->
                                                            </div>
                                                            <div class="field-main phone_num">
                                                                  <label for="mobile_number"> PHONE NUMBER </label>
                                                                  <input type="text" name="mobile_number" id="mobile_number" class="field mobilenumber" minlength="6" maxlength="10" value="<?php echo $_SESSION["user"]["phone"];//@$metadata['mobile_number']; ?>"  required >
                                                            </div>
                                                             <div class="alert"></div>
                                                    </div>
                                              </div>

                                              <!-- </div> -->

                                                        <div class="step-footer clearfix">
                                                              <?php /*<span class="delete-account-link"><a href="<?php print wp_nonce_url(site_url('social-login?delete=true'), 'deleteaccount', 'jigsaw_delete');?>" id="delete_user">Delete account</a></span>*/?>
                                                              <button class="send-button update-account-form-button">SUBMIT <img alt="img" src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/send-arrow.png"> </button>
                                                        </div>

                                              <!--step-footer ends here-->
                                        </form>
                                  </div>
                                  <!--box-with-border ends here-->
                                  <?php /* Section for force phone number for first time logged in user */ //if (!empty($_GET["profile"]) && $_GET["profile"] === "open") { ?>
                                  <!-- <input type="hidden" name="forcelogin" id="forcelogin" value="yes" /> -->
                                  <?php //} ?>
                              </div>
                        </div>
                        <!-- modal body ends here -->
                  </div>
                 <!--  <div class="modal-footer"> -->
                      <!--  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->
                 <!--  </div> -->
          </div>
    </div>
</div>

<?php
	if(isset($_GET["return_url_2"]))
	{
		if(strlen($_SESSION["user"]["phone"]) == 0)
		{ ?>
			<script>
				$(document).ready(function(){
					$('#profile-popup1').modal({backdrop: "static"});
				});
			</script>
		<?php }
	}
?>

 <script type="text/javascript">
 $(document).ready(function(){
        var return_url_2 = $("#iot_profile_form input[name=return_url_2]").val();
        var verify_otp = $("#iot_profile_form input[name=verify_otp]").val();
        var img = '<img alt="img" src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/send-arrow.png">';
        if(return_url_2!='')
        {
            $('button.send-button').html('Start Your Free Course'+img);
        }
         $(".send-button").click(function(event) {
                event.preventDefault();
                var phone = $("#iot_profile_form input[name=mobile_number]").val();
                var name = $("#iot_profile_form input[name=display_name]").val();
                var email = $("#iot_profile_form input[id=email]").val();
                 if ($.trim(phone).length == 0) {
                     $(".alert").html("Please enter phone number");
                       e.preventDefault();
                      return false;
                  }
                  else if(!validatePhone(phone)){
                     $(".alert").html("Please enter valid phone number");
                       e.preventDefault();
                     return false;
                  }
                  $.post("<?php echo JAWS_PATH_WEB ?>/webapi/wp/user.set", { phone: phone, name: name, email: email }, function (data) {
                          response = $.parseJSON(data);
                           if(return_url_2!='')
                          {
                             window.location.href= return_url_2;
                          }
                          $(".alert").html("");
                           $('#profile-popup1').modal("hide");
                  });
            });
    });

 function validatePhone(txtPhone) {
        var filter = /([0-9]{10})|(\([0-9]{3}\)\s+[0-9]{3}\-[0-9]{4})/;
        if (filter.test(txtPhone)) {
            return true;
        }
        else {
            return false;
        }
    }
 </script>
