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
    <td>
        <table border="0" cellpadding="30" cellspacing="0" style="width: 100%; border-color: transparent;" bgcolor="#0c8dc9" color="#ffffff">
            <tr>
                <td style="text-align: center;">
                    <span style="font-size: 70%; color: #A9D7EC; line-height: 200%; text-transform: uppercase;">Contact</span><br />
                    <span style="font-size: 80%; color: #F3F9FC;"><?php echo $GLOBALS['content']['footer']['phone'] ?? '+91-90192-17000'; ?></span>
                </td>
                <td style="text-align: center;">
                    <span style="font-size: 70%; color: #A9D7EC; line-height: 200%; text-transform: uppercase;">Email</span><br />
                    <a style="text-decoration: none;" href="mailto:support@jigsawacademy.com?Subject=Regarding%20My%20Enrollment" target="_top"><span style="font-size: 80%; color: #F3F9FC;">support@jigsawacademy.com</span></a>
                </td>
                <td style="text-align: center;">
                    <span style="font-size: 70%; color: #A9D7EC; line-height: 200%; text-transform: uppercase;">Facebook</span><br />
                    <a style="text-decoration: none;" href="https://www.facebook.com/jigsawacademy"><span style="font-size: 80%; color: #F3F9FC;">fb.com/jigsawacademy</span></a>
                </td>
            </tr>
        </table>
    </td>
</tr>

<!-- FOOTER /////////////////////////////////////////////////////////////////////////////// -->

<tr>
    <td>
        <table border="0" cellpadding="10" cellspacing="0" style="width: 100%; border-color: transparent;" bgcolor="#ffffff" color="#000000">
            <tr>
                <td style="text-align: left;">
                    <span style="color: #989898; font-size: 55%;">This email is confidential and is intended to be read by the recipient only.</span>
                </td>
                <td style="text-align: right;">
                    <span style="color: #989898; font-size: 55%;">&copy;&nbsp;Jigsaw Academy Education Pvt. Ltd.</span>
                </td>
            </tr>
        </table>
    </td>
</tr>