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
    /*load_library("payment");
    load_module("user");
    load_module("course");
    load_module("subs");*/
    load_module("ui");

    // Init Session
    //auth_session_init();

    // Prep
    /*$user;
    $login_params["return_url"] = JAWS_PATH_WEB."/wns-nominations";

    // No one is logged in
    if (!auth_session_is_logged()) { 
        ui_render_login_front(array(
                        "mode" => "create",
                        "return_url" => $login_params["return_url"],
                        "text" => "Please sign-in or register for a new account.<br/>Select the social network account you want to use for this."
                    ));            
        exit();
    }

    // Someone is logged in
    else $user = user_get_by_id($_SESSION["user"]["user_id"]);

    */

    // Proceed with rendering the UI
    ui_render_head_front(array(
        "title" => "WNS Nominations",
        "scripts" => array(1 => "app/templates/jaws/frontend/wns.js"),
        "styles" => array(1 => "app/templates/jaws/frontend/modal.css",
                          2 => "app/templates/jaws/frontend/wns.css"  
            )
    ));

?>
        <div id="bkg-img"> </div>
        <div id="bkg-overlay"> </div>

        <div id="wns-form" class="border" ng-app=""> 
            <div class="header">Jigsaw Academy</div>
            <div class="sub-header">WNS Nomination</div>
            <div class="intro">
                Enroll with us and avail a flat -25% discount if you are a WNS Employee.<br/>Fill the form below to nominate yourself for an enrollment.
            </div>

            <div class="title"><span>Select Courses</span></div><br/>
            <div id="course-selector">

            <?php

            // Load course info
            $course_arr = db_query("SELECT * FROM course WHERE ((il_status_inr = TRUE) OR (sp_status_inr = TRUE)) AND status='enabled';");
            foreach($course_arr as $course) { 

                $res_meta = db_query("SELECT * FROM course_meta WHERE course_id=".$course["course_id"]." LIMIT 1;");
                $course_content = json_decode($res_meta[0]["content"], true);

                $allow_sp = false;
                $allow_il = false;
                if ((strcmp($course["sp_status_inr"], "1") == 0) && (isset($course["sp_price_inr"])) && (intval($course["sp_price_inr"]) > 0 )) $allow_sp = true;
                if ((strcmp($course["il_status_inr"], "1") == 0) && (isset($course["il_price_inr"])) && (intval($course["il_price_inr"]) > 0 )) $allow_il = true;

                ?>

                    <div class="course" data-name="<?php echo $course["name"];?>">    
                        <div class="text">
                        	<div class="name"><a href="<?php echo $course_content["url_web"];?>" target="_blank" title="Look up on jigsawacademy.com"><?php echo $course["name"];?></a></div>
                            <div class="desc"><?php echo substr($res_meta[0]["desc"], 0, 75)."..";?></div>
                        </div>
        
                        <div class="mode-selector">
                            <div class="mode il <?php if (!$allow_il) echo "disabled"; ?>" data-price="<?php echo $course["il_price_inr"]; ?>" title="Select Premium Mode">
                                <?php if ($allow_il) { ?>
                                    <div class="mode-name">Premium<div class="select"><i class="fa fa-fw fa-lg fa-check"></i></div></div>
                                    <div class="mode-price"><?php echo '&#8377;'.number_format(intval($course["il_price_inr"])); ?></div>
                                <?php } else { ?>
                                    <div class="mode-name">Premium<br/>Not Avail</div>
                                <?php } ?> 
                            </div>
                            <div class="mode sp <?php if (!$allow_sp) echo "disabled"; ?>" data-price="<?php echo $course["sp_price_inr"]; ?>" title="Select Regular Mode">
                                <?php if ($allow_sp) { ?>
                                    <div class="mode-name">Regular<div class="select"><i class="fa fa-fw fa-lg fa-check"></i></div></div>
                                    <div class="mode-price"><?php echo '&#8377;'.number_format(intval($course["sp_price_inr"])); ?></div>
                                    <div class="select"><i class="fa fa-fw fa-lg fa-check"></i></div>
                                <?php } else { ?>
                                    <div class="mode-name">Regular<br/>Not Avail</div>
                                <?php } ?> 
                            </div>
                        </div>
                    </div>

                <?php } ?>               

            </div>

            <!--<br/><br/>-->
            <div id="pricing" class="border">
            <div class="title"><span>Pricing</span></div><br/>            	
                <div class="row"><div class="left">Total Price</div><div class="right"><span class="sym"></span>&#8377;<span class="amt" id="total">0</span></div></div>
            	<div class="row"><div class="left">Discount (-25%)</div><div class="right"><span class="sym">&#8377;</span><span class="amt" id="dis">0</span></div></div>
            	<div class="row"><div class="left">Tax (+14.5%)</div><div class="right"><span class="sym">&#8377;</span><span class="amt" id="tax">0</span></div></div>
            	<div class="row"><div class="left">Nett Payable</div><div class="right"><span class="sym">&#8377;</span><span class="amt" id="nett">0</span></div></div>
            </div>

            <br/><br/><br/>
            <div class="title"><span>Personal Info</span></div>
            <div id="details" class="border">

                <div class="panel">
                    <label for="txt-name">Full Name</label>
                    <input type="text" id="txt-name" name="name" class="field" value="" placeholder="Firstname Lastname" tabindex="1"/>
                    <label class="error" for="txt-name"></label>

                    <label for="txt-email" title="Email ID must be under the domain WNS.com"><i class="fa fa-info-circle fa-lg" title="Email ID must be under the domain WNS.com"></i>WNS Email</label>
                    <input type="email" id="txt-email" name="email" class="field" value="" placeholder="email@wns.com" tabindex="3"/>
                    <label class="error" for="txt-email"></label>

                    <label for="txt-country">Country</label>
                    <input type="text" id="txt-country" name="country" class="field" value="" tabindex="5"/>
                    <label class="error" for="txt-country"></label>

                </div>

                <div class="panel">
                    <label for="txt-phone">Phone</label>
                    <input type="tel" id="txt-phone" name="phone" class="field" value="" tabindex="2" />
                    <label class="error" for="txt-phone"></label>
    
                    <label for="txt-emp-id">WNS Employee ID</label>
                    <input type="text" id="txt-emp-id" name="emp-id" class="field" value="" tabindex="4" />
                    <label class="error" for="txt-emp-id"></label>

                    <label for="txt-emp-city">City</label>
                    <input type="text" id="txt-city" name="city" class="field" value="" tabindex="6" />
                    <label class="error" for="txt-city"></label>
                </div>

                <div class="title"><span>Terms &amp; Conditions</span></div><br/>

                <form>
                    <input type="checkbox" name="chk-soc" value="soc" id="ichk-soc"/><label for="ichk-soc"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;I want to access my course materials using Social network login (Facebook / Google+ / LinkedIn)</label><br/>

                    <input type="checkbox" name="chk-tc" value="tc" id="ichk-tc"/><label for="ichk-tc"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;I agree to Jigsaw Academy's <a href="https://www.jigsawacademy.com/terms-conditions/" target="_blank" tabindex="7">Terms &amp; Conditions</a>.</label><br/>
            
                    <input type="checkbox" name="chk-pp" value="pp" id="ichk-pp"/><label for="ichk-pp"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;I agree to Jigsaw Academy's <a href="https://www.jigsawacademy.com/privacy-policy/" target="_blank" tabindex="8">Privacy Policy</a>.</label><br/>
                </form>

            </div>

            <center>
            <a class="button skewed" id="btn-wns" style="margin-bottom: -7px;" tabindex="9">
                <span class="button-main-text">Enroll Me</span>
                <span class="button-main-arrow-image">
                    <img class="image-icon" src="<?php echo JAWS_PATH_WEB.'/media/jaws/frontend/images/long-arrow-orange.png'; ?>">
                </span>
            </a>
            </center>

        </div>

        <div style="visibility: hidden; display: none;">
            <input type="hidden" value="<?php echo JAWS_PATH_WEB; ?>" id="txt-jaws-url" style="visibility: hidden; display: none;" />
        </div>
    
    </body>
</html>