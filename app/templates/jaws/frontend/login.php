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
    header('Location: ../index.php');
    die();
}

?>

<div id="bkg-img"> </div>
<div id="bkg-overlay"> </div>

<div class="modal">

    <div class="page bkg active">
        <div class="header">Sign-In</div>
        <div class="sub-header">Jigsaw Academy</div>

        <div class="text" style="width: 100%; overflow: visible!important;">
            <?php echo (isset($GLOBALS["content"]["text"]) ? $GLOBALS["content"]["text"] : "Please sign-in to your account.<br />"); ?>
            <br /><br />

            <div class="login-soc">
                <div style="display: block; margin: 25px auto 50px auto; width: 70%; position: relative; left: 5%">
                    <div id="soc-fb" class="social-radio" title="Select Facebook"><i class="fa fa-facejbook-square fa-fw fa-4x"></i><input type="hidden" class="soc-info " style="display:none;" value="" /></div>
                    <div id="soc-gp" class="social-radio" title="Select Google+"><i class="fa fa-goojgle-plus-square fa-fw fa-4x"></i><input type="hidden" class="soc-   info" style="display:none;" value="" /></div>
                    <div id="soc-li" class="social-radio" title="Select linkedin"><i class="fa fa-linkjedin-square fa-fw fa-4x"></i><input type="hidden" class="soc-info " style="display:none;" value="" /></div>
                </div>

                <!--<div style="display: block; margin: 0 auto; text-align: center; position: relative;">
                    <span id="login-switch-corp">Corporate user? <b>Click here</b></span>
                </div>-->
            </div>

            <div class="panel login-corp" style="display: none; margin: -10px 0px 0px 0px;">

                <input type="text" id="txt-email" name="email" class="field" value="" placeholder="Email" style="" />
                <label class="error" for="email"></label>

                <a class="button skewed" id="btn-begin" style="margin-top: 38px; position: relative; top: -35px;">
                    <span class="button-main-text">LOGIN</span>
                    <span class="button-main-arrow-image">
                        <img class="image-icon" src="<?php echo JAWS_PATH_WEB . '/media/jaws/frontend/images/long-arrow-orange.png'; ?>">
                    </span>
                </a>

            </div>

            <div class="panel login-corp" style="display: none; margin: -10px 0px 0px 0px; position: relative; top: -15px;">

                <input type="password" id="txt-pass" name="pass" class="field" placeholder="Password" value="" />
                <label class="error" for="pass"></label>

                <div style="position: relative; top: 2px">
                    <span id="login-switch-passreset">Forgot password? <b>Reset</b></span><br />
                    <span id="login-switch-signup">New user? <b>Sign up</b></span><br />
                    <span id="login-switch-soc">Social login? <b>Click here</b></span>
                </div>

            </div>

            <div style="display: none;">

                <?php

                // Calculate the login processor URL based on mode
                if (!isset($GLOBALS["content"]["mode"])) $GLOBALS["content"]["mode"] = "create";
                $login_url = JAWS_PATH_WEB . "/do" . $GLOBALS["content"]["mode"];

                // Add the return URL as param
                if (!isset($GLOBALS["content"]["return_url"])) $GLOBALS["content"]["return_url"] = JAWS_PATH_WEB;
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
        <p style="font-size:11px;margin:0; color: rgba(0,0,0,0.75);font-family: 'Lato', 'Sans-serif'">By proceeding you are agreeing to Jigsaw's <a href="https://www.jigsawacademy.com/terms-conditions/">Terms and Conditions</a>. You are also entitled to Jigsaw's <a href="https://www.jigsawacademy.com/privacy-policy/">Privacy Policy</a>.</p>
    </div>

    <div class="nav">
        <div class="panel left">
            <div class="link-button active" id="btn-website">Back to website</div>
        </div>

        <div class="panel right">
            <div class="link-button active" style="visibility:visible; user-select:none; pointer-events: none; color:rgba(0,0,0,0.35);" id="btn-prev"><i class="fa fa-phone fa-fw fa-lg fa-2x"></i><span style="position:relative; top: -0.5vh; font-size: 110%;">&nbsp;+91 92435-22277</span></div>
        </div>
    </div>

</div>

<div style="visibility: hidden; display: none;">
    <input type="hidden" value="<?php echo JAWS_PATH_WEB; ?>" id="txt-jaws-url" style="visibility: hidden; display: none;" />
</div>