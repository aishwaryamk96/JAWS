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

    	// Check
    	if (!auth_session_is_logged()) {

?>

	<script async type="text/javascript" src="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL; ?>/app/templates/iot/v1/js/login.js"></script> 

<!-- Login modal start-->
	<div class="modal fade loginmodal text-center" id="loginmodal" role="dialog">
	  <div class="modal-dialog"> 
	    <!-- Modal content-->
	    <div class="modal-content model-margin">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	      </div>
	      <div class="modal-body">
	        <div class="sign-in-sec-in"> <span class="sign-in-sec-head"> Sign Up using any of the below options. </span>
	          <p>We will never spam you. It's a promise!</p>
	          <div class="sign-in-sec-button"> 
	          <a href="#" class="social-radio" id="soc-fb">
	            <div class="fb-button">
	              <button class="fb-button-in"> <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/fb-img.png" alt="f"> Sign Up with Facebook </button>
	            </div>
	            </a> 
	            <!-- fb-button ends here--> 
	            <a href="#" class="social-radio" id="soc-gp">
	            <div class="google-button">
	              <button class="google-button-in"> <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/google-img.png" alt="G"> Sign Up with Google </button>
	            </div>
	            </a> 
	            <!-- google-button ends here--> 
	            <a href="#" class="social-radio" id="soc-li">
	            <div class="in-button">
	              <button class="in-button-in"> <img src="<?php echo $_SERVER['server_name'].'/'.JAWS_PATH_LOCAL.'/common/iot/'; ?>images/link-in-img.png" alt="in"> Sign Up with LinkedIn </button>
	            </div>
	            </a> 
	            <!-- in-button ends here--> 
	          </div>
	          <!-- sign-in-sec-button ends here--> 
	        </div>
	      </div>
	    </div>
	  </div>
	</div>
	<!-- Login modal end--> 

	<div style="display: none;">

            <?php

                // Calculate the login processor URL based on mode
                if (!isset($GLOBALS["content"]["mode"])) $GLOBALS["content"]["mode"] = "create";
                $login_url = JAWS_PATH_WEB."/do".$GLOBALS["content"]["mode"];

                // Add the return URL as param
                if (!isset($GLOBALS["content"]["return_url"])) $GLOBALS["content"]["return_url"] = 'https://'. $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
                $return_url = urlencode($GLOBALS["content"]["return_url"]);

                // Add Re-Auth as param
                $reauth = "false";
                if (!isset($GLOBALS["content"]["reauth"])) $GLOBALS["content"]["reauth"] = false;
                if ($GLOBALS["content"]["reauth"]) $reauth = "true";
            ?>

                <input type="hidden" id="login_url" value="<?php echo $login_url; ?>" />
                <input type="hidden" id="return_url" value="<?php echo $return_url; ?>" />
                <input type="hidden" id="reauth" value="<?php echo $reauth; ?>" />
            </div>                                          

        </div>

        <?php } ?>