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

?> 

<tr> 
    <td bgcolor="#f4f6f6" color="#FFFFFF">      
        <table border="0" cellpadding="0" cellspacing="0" style="width: 100%; border: none;">
            <tr height="200">
                <td width="40">&nbsp;</td>
                <td width="350"><img align="left" style="display: block; margin: 0;" src="<?php echo JAWS_PATH_WEB.'/media/jaws/frontend/images/jigsaw-logotype.png'; ?>" alt="" width="350" height="63"/></td>
                <td width="300"><img align="right" style="display: block; margin: 0;" src="<?php echo JAWS_PATH_WEB.'/media/jaws/frontend/images/gradhat.png'; ?>" alt="" width="280" height="150"/></td>
            </tr>
        </table>
    </td>   
</tr>