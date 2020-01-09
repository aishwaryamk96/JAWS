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
        "title" => "GENPACT Nomination",
        "scripts" => array(1 => "app/templates/jaws/frontend/genpact.js"),
        "styles" => array(1 => "app/templates/jaws/frontend/modal.css",
                          2 => "app/templates/jaws/frontend/wns.css"  
            )
    ));

?>
        <div id="bkg-img"> </div>
        <div id="bkg-overlay"> </div>

        <div id="wns-form" class="border" ng-app=""> 
            <div class="header">Jigsaw Academy</div>
            <div class="sub-header">GENPACT - Foundation Course in Analytics</div>
            <div class="intro">
                Enroll for 'Foundation Course in Analytics' with us and avail a discount if you are a GENPACT Employee.<br/>Fill the form below to enroll yourself.
            </div>     

<br/>
            <div class="title"><span>Personal Info</span></div>
            <div id="details" class="border">

                <div class="panel">
                    <label for="txt-name">Full Name</label>
                    <input type="text" id="txt-name" name="name" class="field" value="" placeholder="Firstname Lastname" tabindex="1"/>
                    <label class="error" for="txt-name"></label>

                    <label for="txt-email" title="Email ID must be under the domain Genpact.com"><i class="fa fa-info-circle fa-lg" title="Email ID must be under the domain genpact.com"></i>GENPACT Email</label>
                    <input type="email" id="txt-email" name="email" class="field" value="" placeholder="email@genpact.com" tabindex="3"/>
                    <label class="error" for="txt-email"></label>

                     <label for="txt-emp-id">OHR ID</label>
                    <input type="text" id="txt-emp-id" name="emp-id" class="field" value="" tabindex="5" />
                    <label class="error" for="txt-emp-id"></label>

                    <label for="txt-country">Country</label>
                    <input type="text" id="txt-country" name="country" class="field" value="" tabindex="7"/>
                    <label class="error" for="txt-country"></label>

                </div>

                <div class="panel">
                    <label for="txt-phone">Phone</label>
                    <input type="tel" id="txt-phone" name="phone" class="field" value="" tabindex="2" />
                    <label class="error" for="txt-phone"></label>

                    <label for="txt-email-alt">Alternate Email</label>
                    <input type="email-alt" id="txt-email-alt" name="email" class="field" value="" placeholder="email@example.com" tabindex="4"/>
                    <label class="error" for="txt-email-alt"></label>
    
                    <label for="txt-office">Genpact Location</label>
                    <input type="text" id="txt-office" name="office" class="field" value="" tabindex="6" />
                    <label class="error" for="txt-office"></label>

                    <label for="txt-emp-city">City</label>
                    <input type="text" id="txt-city" name="city" class="field" value="" tabindex="8" />
                    <label class="error" for="txt-city"></label>
                </div>

            <div class="title" style="display:none;"><span>Select your Mode of Payment</span></div>
            <div id="paymode" class="border" style="display:none;">
                    <br/>
                    <form>   
                        <!--<input type="radio" name="opt-mode" value="inr" id="iopt-inr"/><label for="iopt-inr" selected><span><i class="fa fa-fw fa-lg fa-check"></i>   </span>Indian Rupees (34,500 INR Incl. of all taxes)</label><br/>                -->
                        <input type="radio" name="opt-mode" value="usd" id="iopt-usd" checked="checked"/><label for="iopt-usd"><span><i class="fa fa-fw fa-lg fa-check"></i>   </span>US Dollars (750 USD)</label><br/>
            </form>
            </div>

            <br/><br/>
                <div class="title"><span>Terms &amp; Conditions</span></div><br/>
                <form>   
                    <input type="checkbox" name="chk-tc" value="tc" id="ichk-tc"/><label for="ichk-tc"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;I agree to Jigsaw Academy's <a href="https://www.jigsawacademy.com/terms-conditions/" target="_blank" tabindex="7">Terms &amp; Conditions</a>.</label><br/>
            
                    <input type="checkbox" name="chk-pp" value="pp" id="ichk-pp"/><label for="ichk-pp"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;I agree to Jigsaw Academy's <a href="https://www.jigsawacademy.com/privacy-policy/" target="_blank" tabindex="8">Privacy Policy</a>.</label><br/>
                </form>

            </div>

            <center>
            <a class="button skewed" id="btn-genpact" style="margin-bottom: -7px;" tabindex="9">
                <span class="button-main-text">Submit</span>
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