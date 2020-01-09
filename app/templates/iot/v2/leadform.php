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

    JIGSAW ACADEMY WORKFLOW SYSTEM v2
    ---------------------------------
*/

	// Prevent exclusive access
    	if (!defined("JAWS")) {
        		header('Location: https://www.jigsawacademy.com');
        		die();
    	}

?>

	<script async type="text/javascript" src="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL; ?>/app/templates/iot/v2/js/leadform.js"></script>

<!-- Lead modal start-->
	<div class="modal fade leadmodal text-center" id="leadmodal" role="dialog">
	  <div class="modal-dialog" style='max-width:640px;'>
	    <!-- Modal content-->
	    <div class="modal-content model-margin" style='margin-top: 0px;'>
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	      </div>
	      <div class="modal-body">
	        <div class="sign-in-sec-in"> <span class="sign-in-sec-head"> Provide us your contact details. </span>
	          <p style="font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 18px;">We will notify you when <span style='font-weight:bold;' id='leadcoursename'>this course</span> becomes available.</p>
	          <div class="sign-in-sec-button" style='padding: 5% 10% 0% 10%;'> 

	          <!--<input type="text" class="" placeholder="Full Name" name="name" required/>	          
	          <input type="email" class="" placeholder="Email" name="name" required/>
	          <input type="tel" class="" placeholder="Contact Number" name="phone" required/>-->

	          <form id='leadform'>
	                <div class="form-with-field font-lato" style='width:100%; text-align:left;'>
	                        <input type="hidden" name="verify_otp" value="false">
	                        <input type="hidden" name="course_id" id='leadcourseid' value="">
	                        <div class="field-main">
	                                <label class="font-lato" for="name">FULL NAME</label>
	                                <input type="text" name="name" id="leadname" required value="<?php echo $_SESSION["user"]["name"] ?? '';?>" class="field">
	                                <div style='color:red;' id="leadform-alert-name"></div>
	                        </div>	                        
	                        <div class="field-main">
	                              <label for="email">EMAIL</label>
	                              <input type="email" class="field" id="leademail" required value="<?php echo $_SESSION["user"]["email"] ?? '';?>">
	                              <div style='color:red;' id="leadform-alert-email"></div>
	                        </div>
	                        <div class="field-main phone_num">
	                              <label for="phone">PHONE NUMBER</label>
	                              <input type="text" name="phone" id="leadphone" class="field mobilenumber" minlength="6" maxlength="10" required value="<?php echo $_SESSION["user"]["phone"] ?? '';?>">
	                              <div style='color:red;' id="leadform-alert-phone"></div>
	                        </div>
	                        <br/>
	                </div>

                </form>

                  <div class="step-footer clearfix">
                    <button id='leadsubmit' class="send-button" style='float:right;position: relative; right: -7.5%;'>SUBMIT<img alt="img" src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/send-arrow.png"> </button>
                   </div>

	          </div>
	          <!-- sign-in-sec-button ends here--> 
	        </div>
	      </div>
	    </div>
	  </div>
	</div>

