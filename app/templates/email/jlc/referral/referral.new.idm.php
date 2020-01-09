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

	<html>
    <head>

        <style>
            a.btn:hover { background-color: #FF9326!important; }
            a.btn:active { background-color: #BE4600!important; }
        </style>

    </head>
    <body>

        <center>
            <table width="800" border="0" cellpadding="0" cellspacing="0" style="font-family: verdana; font-size:100%; margin: 5px auto; max-width: 800px; padding: 0; width: 100vw; border: none;">

                <!-- HEADER AND LOGO //////////////////////////////////////////////////////////////////////// -->

                <?php load_template("email","common/front/header"); ?>

                <!-- SUBJECT AND INTRO ////////////////////////////////////////////////////////////////////// -->

                <tr>
                    <td style="text-align: center; padding: 12px" bgcolor="#009cd9">
                        <span style="color: white; font-size: 100%;"><?php echo $GLOBALS["content"]["emailer"]["referrer"]["name"]; ?> wants to boost your career</span>
                    </td>
                </tr>

                <!-- SUBS ////////////////////////////////////////////////////////////////////////////////// -->

                <tr>
                    <td width="800" bgcolor="#f4f6f6">
                        <center>

                            <table border="0" cellpadding="0" cellspacing="15" bgcolor="#f4f6f6" color="#000000" style="border-color: #e2e4e4; border-style: solid; border-width: 0 0 1px 0;">
                                <tr height="20" bgcolor="#f4f6f6">
                                    <td>Dear <?php echo $GLOBALS["content"]["emailer"]["referred"]["name"]; ?>,</td>
                                </tr>
                                <tr height="20" bgcolor="#f4f6f6">
                                    <td colspan="2">Your friend <?php echo $GLOBALS["content"]["emailer"]["referrer"]["name"]; ?>, is learning analytics with us and recommends a great course to give your career a boost. <?php echo $GLOBALS["content"]["emailer"]["referrer"]["fname"]; ?> believes that the <a target="_blank" href="https://www.jigsawacademy.com/idm/">Integrated Program in Data Science and Machine Learning (IDM)</a> by the University of Chicago Graham School and Jigsaw Academy is ideal for you!</td>
                                </tr>

                                <tr height="20" bgcolor="#f4f6f6">
                                    <td colspan="2">Enclosed is the brochure and schedule for your reference.</td>
                                </tr>

                                <tr height="20" bgcolor="#f4f6f6">
                                    <td colspan="2">The IDM focuses on advanced data science and machine learning with R, Big Data with Spark, as well as project management, visualization and the storytelling with data. The program is a hybrid course (in-person and online) with 96 hours (12 days) on in-person classes in hotel Hilton Bangalore, 26 online sessions as well as over 100 hours of pre-recorded content during a period of 9 months. The faculty will be a mix of lecturers from University of Chicago, industry experts and Jigsaw faculty.</td>
                                </tr>

                                <tr height="20" bgcolor="#f4f6f6">
                                    <td colspan="2"><b>Enrol within June 2017 and use coupon code <?php echo $GLOBALS["content"]["emailer"]['coupon_code']; ?> to get a 10% discount on the course fees.</b></td>
                                </tr>

                                <tr height="10" bgcolor="#f4f6f6">
                                    <td>Best wishes,</td>
                                </tr>
                                <tr height="10" bgcolor="#f4f6f6">
                                    <td>IDM Admissions</td>
                                </tr>
                                <tr height="10" bgcolor="#f4f6f6">
                                    <td>Website: <a href="https://www.jigsawacademy.com/idm">https://www.jigsawacademy.com/idm</a></td>
                                </tr>
                                <tr height="20" bgcolor="#f4f6f6">
                                    <td>Contact: +91 90199 87000 (10AM - 6PM, Monday to Saturday)</td>
                                </tr>
                                <!-- <tr height="245" bgcolor="#f4f6f6">  -->

                               <!--  <?php
                                    $count = 1;
                                   // foreach($GLOBALS["content"]["emailer"]["courses"] as $course) {

                                    //if (((($count - 1) % 3) == 0)  && ($count > 1)) {
                                        ?>
                                            </tr>
                                            <tr height="245" bgcolor="#f4f6f6">
                                        <?php
                                    //}

                                ?>        -->

                                   <!--  <td bgcolor="" width="50%" style="">
                                        <center>
                                            <table style="border: 1px solid #d8d9d9;" border="0" cellpadding="0" cellspacing="0">
                                                <tr height="122">
                                                    <td width="245">
                                                        <a href="<?php echo $course["url"]; ?>" class="img" style="text-decoration: none;"><img src="<?php echo $course["img"]; ?>" alt="" height="122" width="246" /></a>
                                                    </td>
                                                </tr>
                                                <tr height="123">
                                                    <td width="245" bgcolor="#ffffff">
                                                        <table style="" border="0" cellpadding="10" cellspacing="0">
                                                            <tr>
                                                                <td>
                                                                    <a href="<?php echo $course["url"]; ?>" class="crs" style="text-decoration: none;"><span style="font-size: 90%; color: #0c8dc9; text-transform: uppercase; line-height: 145%;"><?php echo $course["name"]; ?></span></a><br />
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <span style="font-size: 75%; color: #787878;"><?php echo $course["desc"]; ?></span>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </center>
                                    </td> -->

                                    <?php
                                           // $count ++;

                                        //}
                                    ?>

                                <!-- </tr> -->

                            </table>
                        </center>

                    </td>
                </tr>

                <!-- OUTRO ///////////////////////////////////////////////////////////////////////////////// -->

                <?php load_template("email", "common/front/footer"); ?>

            </table>
        </center>

    </body>
</html>
