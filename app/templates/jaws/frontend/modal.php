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

    <div class="page <?php echo ((strcmp($GLOBALS["content"]["type"], "error") != 0) ? "bkg" : ""); ?> active">               
        <div class="header"><?php echo $GLOBALS["content"]["header"]; ?></div>
        <?php if (isset($GLOBALS["content"]["title"])) { ?><div class="sub-header"><?php echo $GLOBALS["content"]["title"]; ?></div><?php } ?>
        <div class="text">
            <br/>
            <?php echo $GLOBALS["content"]["text"]; ?>
            <?php if (isset($GLOBALS["content"]["btn"])) { ?>
            <br/>
            <a class="button skewed" id="btn-begin" href="<?php echo $GLOBALS["content"]["btn"]["url"]; ?>">
                <span class="button-main-text"><?php echo $GLOBALS["content"]["btn"]["text"]; ?></span>
                <span class="button-main-arrow-image">
                    <img class="image-icon" src="<?php echo JAWS_PATH_WEB.'/media/jaws/frontend/images/long-arrow-orange.png'; ?>">
                </span>
            </a>
            <?php } ?>
        </div>
    </div>

    <div class="nav">
        <div class="panel left">
            <div class="link-button active" id="btn-<?php echo $GLOBALS['content']['footer']['website'] ?? 'website'; ?>">Back to website</div>
        </div>  

        <div class="panel right" >
        	<div class="link-button active" style="visibility:visible; user-select:none; pointer-events: none; color:rgba(0,0,0,0.35);" id="btn-prev"><i class="fa fa-phone fa-fw fa-lg fa-2x"></i><span style="position:relative; top: -0.5vh; font-size: 110%;">&nbsp;<?php echo $GLOBALS['content']['footer']['phone'] ?? '+91-90192-17000'; ?></span></div>
        </div>
    </div>           

</div>


