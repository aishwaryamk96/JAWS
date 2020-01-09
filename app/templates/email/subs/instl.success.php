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
                    <td>
                        <table border="0" cellpadding="30" cellspacing="0" style="width: 100%; border-color: transparent;" bgcolor="#0c8dc9" color="#ffffff">
                            <tr>
                                <td style="text-align: center;">
                                    <span style="color: white; font-size: 150%;">Payment Successful</span><br /><br />
                                    <span style="color: #DAEEF7; font-size: 85%;">Hi <?php echo $GLOBALS["content"]["emailer"]["fname"]; ?>! We have received your payment of <?php echo ((strcmp(strtolower($GLOBALS["content"]["emailer"]["currency"]), "inr") == 0) ? '&#8377;' : '&#36;' ).number_format(intval($GLOBALS["content"]["emailer"]["sum"])); ?> for your <?php

                                            $instl_count_text_arr = array(
                                                1 => "first",
                                                2 => "second",
                                                3 => "third",
                                                4 => "fourth",
                                                5 => "fifth",
                                                6 => "sixth",
                                                7 => "seventh",
                                                8 => "eigth",
                                                9 => "ninth"
                                            );

                                            echo $instl_count_text_arr[intval($GLOBALS["content"]["emailer"]["instl_count"])];

                                        ?> installment.</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- PAYMENT SCHEME /////////////////////////////////////////////////////////////////////// -->

                <tr>
                    <td>
                        <table border="0" cellpadding="30" cellspacing="0" style="width: 100%; border-color: transparent;" bgcolor="#f4f6f6" color="#000000">
                            <tr>
                                <td style="text-align: center;">
                                    <span style="color: #252525; font-size: 85%;">You had opted to finance your enrollment in installments. The following is a summary of the status of your installment scheme. We will email you a reminder of your payment details a week before each due date if any installment is pending.</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- INSTL //////////////////////////////////////////////////////////////////////////////// -->

                <tr>
                    <td width="800" bgcolor="#f4f6f6">
                        <center>

                            <table border="0" cellpadding="0" cellspacing="15" bgcolor="#f4f6f6" color="#000000" style="border-color: #e2e4e4; border-style: solid; border-width: 0 0 1px 0;">
                                <tr height="180" bgcolor="#f4f6f6">

                                    <?php

                                    $count = 1;
                                    $instl_count_text_arr = array(
                                        1 => '1<sup style="font-size: 70%;">st</sup> Installment',
                                        2 => '2<sup style="font-size: 70%;">nd</sup> Installment',
                                        3 => '3<sup style="font-size: 70%;">rd</sup> Installment',
                                        4 => '4<sup style="font-size: 70%;">th</sup> Installment',
                                        5 => '5<sup style="font-size: 70%;">th</sup> Installment',
                                        6 => '6<sup style="font-size: 70%;">th</sup> Installment',
                                        7 => '7<sup style="font-size: 70%;">th</sup> Installment',
                                        8 => '8<sup style="font-size: 70%;">th</sup> Installment',
                                        9 => '9<sup style="font-size: 70%;">th</sup> Installment',
                                    );

                                    while($count <= intval($GLOBALS["content"]["emailer"]["payment"]["instl_total"])) {

                                        if (((($count - 1) % 3) == 0) && ($count > 1)) {
                                        ?>
                                            </tr>
                                            <tr height="180" bgcolor="#f4f6f6">
                                        <?php
                                        }

                                        // Format the due date
                                        $due_date = $GLOBALS["content"]["emailer"]["payment"]["instl"][$count]["due_date"];
                                        $due_date = strtoupper(strval(date('M j-S, Y', strtotime($due_date))));
                                        $due_date_arr = explode("-", $due_date);
                                        $due_date_arr_sub = explode(",", $due_date_arr[1]);
                                        $due_date_arr_sub[0] = '<sup style="font-size: 60%; text-transform: none;">'.strtolower($due_date_arr_sub[0]).'</sup>';
                                        $due_date = $due_date_arr[0].$due_date_arr_sub[0].",".$due_date_arr_sub[1];

                                        // Is paid ?
                                        if (strcmp($GLOBALS["content"]["emailer"]["payment"]["instl"][$count]["status"], 'paid') == 0) {
                                            ?>

                                            <td bgcolor="#fafdfd" width="245">
                                                <table style="border: 1px solid #e0e1e1;" border="0" cellpadding="0" cellspacing="0">
                                                    <tr height="100">
                                                        <td width="245" style="text-align: center;">
                                                            <span style="font-size: 80%; color: #e1e4e4;"><?php echo $instl_count_text_arr[$count]; ?></span><br />
                                                    <span style="font-size: 200%; color: #e1e4e4;"><?php echo ((strcmp(strtolower($GLOBALS["content"]["emailer"]["currency"]), "inr") == 0) ? '&#8377;' : '&#36;' ); ?></span>&nbsp;<span style="font-size: 275%; color: #bcbebe;"><?php echo number_format(intval($GLOBALS["content"]["emailer"]["payment"]["instl"][$count]["sum"])); ?></span>
                                                        </td>
                                                    </tr>
                                                    <tr height="80">
                                                        <td width="245">
                                                            <table border="0" cellpadding="10" cellspacing="0" style="border-color: #e0e1e1; border-style: solid; border-width: 1px 0 0 0;">
                                                                <tr>
                                                                    <td width="245" style="text-align: center;">
                                                                        <span style="font-size: 150%; color: #bce3be; text-transform: capitalize; line-height: 200%;">&#x2714;&nbsp;Paid</span><br />
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>

                                    <?php
                                        }

                                        else {
                                    ?>

                                            <td bgcolor="#ffffff" width="245" style="">
                                                <table style="border: 1px solid #e0e1e1;" border="0" cellpadding="0" cellspacing="0">
                                                    <tr height="100">
                                                        <td width="245" style="text-align: center;">
                                                            <span style="font-size: 80%; color: #808080;"><?php echo $instl_count_text_arr[$count]; ?></span><br />
                                                    <span style="font-size: 200%; color: #808080;">&#8377;</span>&nbsp;<span style="font-size: 275%; color: black"><?php echo number_format(intval($GLOBALS["content"]["emailer"]["payment"]["instl"][$count]["sum"])); ?></span>
                                                        </td>
                                                    </tr>
                                                    <tr height="80">
                                                        <td width="245">
                                                            <table border="0" cellpadding="10" cellspacing="0" style="border-color: #e0e1e1; border-style: solid; border-width: 1px 0 0 0;">
                                                                <tr>
                                                                    <td width="245" style="text-align: center;">
                                                                        <span style="font-size: 80%; color: #808080; text-transform: capitalize; line-height: 145%;">Due date</span><br />
                                                                        <span style="font-size: 125%; color: black; line-height: 145%; text-transform: uppercase;"><?php echo $due_date; ?></span>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>

                                    <?php

                                        }

                                        $count ++;

                                    }

                                    ?>

                                </tr>
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
