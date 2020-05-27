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

  $qres = db_query('SELECT paylink_id, user_id FROM payment_link WHERE web_id="'.$GLOBALS["content"]["emailer"]["paylink_id"].'" LIMIT 1;');
  activity_create("ignore", "paylink.send", "sent", "paylink_id", $qres[0]["paylink_id"], "user_id", $qres[0]["user_id"], "Payment Link Sent", "logged");

?>

<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
    <head>
    <!-- NAME: 1 COLUMN -->
    <!--[if gte mso 15]>
      <xml>
         <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch>96</o:PixelsPerInch>
         </o:OfficeDocumentSettings>
      </xml>
      <![endif]-->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Thanks for purchasing course - Jigsaw Academy</title>
    </head>
    <body style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background-color: #d6d6d5; margin: 0; min-width: 100%; padding: 0; width: 100%;">
    <style type="text/css">
        @media screen and (max-width:699px) {
            .t4of12, .t5of12, .t6of12, .t10of12, .t12of12, .full { width: 100% !important; max-width: none !important }
            a[x-apple-data-detectors] { color: inherit !important; text-decoration: none !important; font-size: inherit !important; font-family: inherit !important; font-weight: inherit !important; line-height: inherit !important }
        }
        @media screen {
            @font-face {
                font-family: 'Montserrat-Thin';
                font-style: normal;
                font-weight: 300;
                src: local('Montserrat Light'), local('Montserrat-Light'), url(https://fonts.gstatic.com/s/montserrat/v10/IVeH6A3MiFyaSEiudUMXE8u2Q0OS-KeTAWjgkS85mDg.woff2) format('woff2');
                unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2212, U+2215;
            }
            @font-face {
                font-family: 'Montserrat-Thick';
                font-style: normal;
                font-weight: 500;
                src: local('Montserrat Medium'), local('Montserrat-Medium'), url(https://fonts.gstatic.com/s/montserrat/v10/BYPM-GE291ZjIXBWrtCwejOo-lJoxoMO4vrg2XwIHQk.woff2) format('woff2');
                unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2212, U+2215;
            }
        }
        .btn a:hover, * [lang=x-btn] a:hover { background-color: #270A9C !important; border-color: #270A9C !important }
        a[x-apple-data-detectors] { color: inherit !important; text-decoration: none !important; font-size: inherit !important; font-family: inherit !important; font-weight: inherit !important; line-height: inherit !important }
    </style>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color: #FFFFFF; border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;" bgcolor="#FFFFFF">
        <tr>
            <td align="center">
            <!--[if (gte mso 9)|(IE)]>
            <table width="700" align="center" cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td>
            <![endif]-->
            <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: none; border-collapse: collapse; border-spacing: 0; max-width: 700px; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;">
              <tr>
                <td style="background-color:#FFFFFF;" align="center">
                <!-- Header Start -->
                <table border="0" cellpadding="0" cellspacing="0" style="border: none; border-collapse: collapse; border-spacing: 0; max-width: 700px; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;">
                <tr>
                    <td bgcolor="#FFFFFF" valign="bottom" background="https://www.jigsawacademy.com/emailer/images/banner-mage.png" style="-webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover; background-image: url('https://www.jigsawacademy.com/emailer/images/banner-mage.png'); " align="center">
                        <div>
                            <table border="0" cellpadding="0" cellspacing="0" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;">
                                <tr>
                                    <td style="padding:0;margin:0;width:100%;" align="center">
                                        <img src="https://www.jigsawacademy.com/emailer/images/banner-mage.png" width="96" height="" border="0" style="-ms-interpolation-mode: bicubic; clear: both; display: block; outline: none; text-decoration: none; width: 100%;">
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
                </table>
                <!-- Header End -->
                <!-- Content Start -->
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: none; border-collapse: collapse; border-spacing: 0; margin: auto; max-width: 700px; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;" class="tron">
                <tr>
                    <td align="center">
                    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border: none; border-collapse: collapse; border-spacing: 0; margin: auto; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;" bgcolor="#ffffff" class="basetable">
                    <tr>
                        <td align="center">
                        <!--[if (gte mso 9)|(IE)]>
                        <table width="700" align="center" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                        <td align="center">
                        <![endif]-->
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="basetable" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;">
                        <tr>
                            <td align="center" style="background-color:#ffffff;">
                            <!-- open wrapper -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="basetable" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;">
                            <tr>
                                <td>
                                <!--[if (gte mso 9)|(IE)]>
                                <table width="684" align="center" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td>
                                <![endif]-->
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="basetable" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;">
                                <tr>
                                    <td>
                                    <!--[if (gte mso 9)|(IE)]>
                                    <table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td>
                                    <![endif]-->

                                    <!-- intro -->
                                    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;">
                                        <tr>
                                            <td class="outsidegutter" align="left" style="padding: 0 14px 0 14px;">
                                                <table border="0" cellpadding="0" cellspacing="0" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;"
                                                    class="">
                                                    <tr>
                                                        <td>
                                                            <!--[if (gte mso 9)|(IE)]>
                                                            <table width="672" align="left" cellpadding="0" cellspacing="0" border="0">
                                                            <tr>
                                                                <td>
                                                                    <![endif]-->
                                                            <table border="0" cellpadding="0" cellspacing="0" class="t10of12 basetable" align="left" style="border: none; border-collapse: collapse; border-spacing: 0; max-width: 672px; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;">
                                                                <tr>
                                                                    <td style="padding-left: 12px; padding-right: 12px;">
                                                                        <table border="0" cellpadding="0" cellspacing="0" class="basetable" width="100%" align="center" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; table-layout: fixed; width: 100%;">
                                                                            <tr>
                                                                                <td class="h1" style="color: #e65123; font-family: 'Montserrat-Thin', Helvetica, Arial, sans-serif; font-size: 20px; line-height: 20px; padding: 20px 0;" align="center">Hello <?php echo ucfirst(strtolower($GLOBALS["content"]["emailer"]["fname"])); ?>, </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="p1 p1-cta" style="color: #000000; font-family: 'Montserrat-Thin', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 20px; padding-bottom: 10px;" align="center">Thank you for signing up for the Creative Leader's Program at MIND.
                                                                                </td>
                                                                            </tr><!-- JA-54 -->
                                                                            <?php if(!empty($GLOBALS['content']['emailer']['bundle_details'])){ ?>
                                                                            <tr>
                                                                                <td class="p1 p1-cta" style="color: #000000; font-family: 'Montserrat-Thin', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 20px; padding-bottom: 10px;" align="center">You have enrolled in :
<!--<b><?php //echo $GLOBALS['content']['emailer']['bundle_details']['name']; ?></b>.-->
                                                                                    <!-- Course list -->
                                    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;">
                                        <tr>
                                            <td class="outsidegutter" align="left" style="padding: 0 14px 0 14px;">
                                                <table border="0" cellpadding="0" cellspacing="0" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;"
                                                    class="">
                                                    <tr>
                                                        <td>
                                                            <!--[if (gte mso 9)|(IE)]>
                                                            <table width="672" align="left" cellpadding="0" cellspacing="0" border="0">
                                                            <tr>
                                                                <td>
                                                                    <![endif]-->
                                                            <table border="0" cellpadding="0" cellspacing="0" class="t10of12 basetable" align="left" style="border: none; border-collapse: collapse; border-spacing: 0; max-width: 672px; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;">
                                                                <tr>
                                                                    <td style="padding-left: 12px; padding-right: 12px;">
                                                                        <table border="0" cellpadding="0" cellspacing="0" class="basetable" width="100%" align="center" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; table-layout: fixed; width: 100%;">
                                                                             <tr>
                                                                                <td class="h1" style="color: #e65123; font-family: 'Montserrat-Thin', Helvetica, Arial, sans-serif; font-size: 20px; line-height: 26px;text-transform: none!important;" align="center">
                                                                                    <span style="letter-spacing: 0.5px; text-decoration: none;">
                                                                                        <?php echo (($GLOBALS['content']['emailer']['bundle_details']['name'])); ?>
                                                                                    </span>
                                                                                  
                                                                                </td>
                                                                            </tr>
                                                                        <?php foreach($GLOBALS["content"]["emailer"]["free_course"] as $idx => $iCourse) { ?>
                                                                            <tr>
                                                                                <td class="h1" style="color: #e65123; font-family: 'Montserrat-Thin', Helvetica, Arial, sans-serif; font-size: 20px; line-height: 26px;text-transform: none!important;" align="center">
                                                                                    <span style="letter-spacing: 0.5px; text-decoration: none;">
                                                                                        <?php echo (($iCourse['course_name'])); ?>
                                                                                    </span>
                                                                                   <!--  <a href="<?php //echo $course["url"]; ?>" style="letter-spacing: 0.5px; text-decoration: none;">
                                                                                        <?php //echo $course["name"]; ?>
                                                                                    </a> -->
                                                                                </td>
                                                                            </tr>
                                                                        <?php } ?>
                                                                             <?php foreach($GLOBALS["content"]["emailer"]["individual_course"]  as $idx => $iCourse) { ?>
                                                                            <tr>
                                                                                <td class="h1" style="color: #e65123; font-family: 'Montserrat-Thin', Helvetica, Arial, sans-serif; font-size: 20px; line-height: 26px;text-transform: none!important;" align="center">
                                                                                    <span style="letter-spacing: 0.5px; text-decoration: none;">
                                                                                        <?php echo (($iCourse['course_name'])); ?>
                                                                                    </span>
                                                                                   <!--  <a href="<?php //echo $course["url"]; ?>" style="letter-spacing: 0.5px; text-decoration: none;">
                                                                                        <?php //echo $course["name"]; ?>
                                                                                    </a> -->
                                                                                </td>
                                                                            </tr>
                                                                        <?php } ?>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <!--[if (gte mso 9)|(IE)]>
                                                                </td>
                                                            </tr>
                                                            </table>
                                                            <![endif]-->
                                                            <!--[if mso]></td>
                                                            <td>
                                                            <![endif]-->
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- close Course list -->
                                                                                </td>
                                                                            </tr>
                                                                           
                                                                            
                                                                            <?php } else { ?>
                                                                            <tr>
                                                                                <td class="p1 p1-cta" style="color: #000000; font-family: 'Montserrat-Thin', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 20px;" align="center">THE COURSES CHOSEN BY YOU ARE:
                                                                                </td>
                                                                            </tr>
                                                                            <?php } ?>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            
                                                            <!--[if (gte mso 9)|(IE)]>
                                                                </td>
                                                            </tr>
                                                            </table>
                                                            <![endif]-->
                                                            <!--[if mso]></td>
                                                            <td>
                                                            <![endif]-->
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- close intro -->
                                    <?php if(empty($GLOBALS['content']['emailer']['bundle_details'])){
                                        // course listing wil be shown only for individual courses not for bundles ?>
                                    <!-- HR -->
                                    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;">
                                        <tr>
                                            <td class="outsidegutter" align="left" style="padding: 0 14px 0 14px;">
                                                <table border="0" cellpadding="0" cellspacing="0" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;"
                                                    class="">
                                                    <tr>
                                                        <td>
                                                            <!--[if (gte mso 9)|(IE)]>
                                                            <table width="672" align="left" cellpadding="0" cellspacing="0" border="0">
                                                            <tr>
                                                                <td>
                                                                    <![endif]-->
                                                            <table border="0" cellpadding="0" cellspacing="0" class="t12of12" align="left" style="border: none; border-collapse: collapse; border-spacing: 0; max-width: 672px; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;">
                                                                <tr>
                                                                    <td class="hr-h1" style="padding-bottom: 5px; padding-left: 12px; padding-right: 12px; padding-top: 5px;">
                                                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" align="left" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; table-layout: fixed; width: 100%;">
                                                                            <tr>
                                                                                <td height="1" class="tronhr" style="background: #00A8E1; font-size: 0px; line-height: 0px;">&amp;nbsp;</td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <!--[if (gte mso 9)|(IE)]>
                                                            </td>
                                                        </tr>
                                                        </table>
                                                        <![endif]-->
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- close HR -->

                                    <!-- Course list -->
                                    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;">
                                        <tr>
                                            <td class="outsidegutter" align="left" style="padding: 0 14px 0 14px;">
                                                <table border="0" cellpadding="0" cellspacing="0" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;"
                                                    class="">
                                                    <tr>
                                                        <td>
                                                            <!--[if (gte mso 9)|(IE)]>
                                                            <table width="672" align="left" cellpadding="0" cellspacing="0" border="0">
                                                            <tr>
                                                                <td>
                                                                    <![endif]-->
                                                            <table border="0" cellpadding="0" cellspacing="0" class="t10of12 basetable" align="left" style="border: none; border-collapse: collapse; border-spacing: 0; max-width: 672px; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;">
                                                                <tr>
                                                                    <td style="padding-left: 12px; padding-right: 12px;">
                                                                        <table border="0" cellpadding="0" cellspacing="0" class="basetable" width="100%" align="center" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; table-layout: fixed; width: 100%;">
                                                                        <?php foreach($GLOBALS["content"]["emailer"]["courses"] as $course) { ?>
                                                                            <tr>
                                                                                <td class="h1" style="color: #e65123; font-family: 'Montserrat-Thin', Helvetica, Arial, sans-serif; font-size: 20px; line-height: 26px;text-transform: none!important;" align="center">
                                                                                    <span style="letter-spacing: 0.5px; text-decoration: none;">
                                                                                        <?php echo (($course["name"])); ?>
                                                                                    </span>
                                                                                   <!--  <a href="<?php //echo $course["url"]; ?>" style="letter-spacing: 0.5px; text-decoration: none;">
                                                                                        <?php //echo $course["name"]; ?>
                                                                                    </a> -->
                                                                                </td>
                                                                            </tr>
                                                                        <?php } ?>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <!--[if (gte mso 9)|(IE)]>
                                                                </td>
                                                            </tr>
                                                            </table>
                                                            <![endif]-->
                                                            <!--[if mso]></td>
                                                            <td>
                                                            <![endif]-->
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- close Course list -->

                                    <!-- HR -->
                                    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;">
                                        <tr>
                                            <td class="outsidegutter" align="left" style="padding: 0 14px 0 14px;">
                                                <table border="0" cellpadding="0" cellspacing="0" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;"
                                                    class="">
                                                    <tr>
                                                        <td>
                                                            <!--[if (gte mso 9)|(IE)]>
                                                            <table width="672" align="left" cellpadding="0" cellspacing="0" border="0">
                                                            <tr>
                                                                <td>
                                                                    <![endif]-->
                                                            <table border="0" cellpadding="0" cellspacing="0" class="t12of12" align="left" style="border: none; border-collapse: collapse; border-spacing: 0; max-width: 672px; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;">
                                                                <tr>
                                                                    <td class="hr-h1" style="padding-bottom: 5px; padding-left: 12px; padding-right: 12px; padding-top: 5px;">
                                                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" align="left" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; table-layout: fixed; width: 100%;">
                                                                            <tr>
                                                                                <td height="1" class="tronhr" style="background: #00A8E1; font-size: 0px; line-height: 0px;">&amp;nbsp;</td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <!--[if (gte mso 9)|(IE)]>
                                                            </td>
                                                        </tr>
                                                        </table>
                                                        <![endif]-->
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- close HR -->
                                    <?php } ?>
                                    <!-- price -->
                                    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;">
                                        <tr>
                                            <td class="outsidegutter" align="left" style="padding: 0 14px 0 14px;">
                                                <table border="0" cellpadding="0" cellspacing="0" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;"
                                                    class="">
                                                    <tr>
                                                        <td>
                                                            <!--[if (gte mso 9)|(IE)]>
                                                            <table width="672" align="left" cellpadding="0" cellspacing="0" border="0">
                                                            <tr>
                                                                <td>
                                                                    <![endif]-->
                                                            <table border="0" cellpadding="0" cellspacing="0" class="t10of12 basetable" align="left" style="border: none; border-collapse: collapse; border-spacing: 0; max-width: 672px; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;">
                                                                <tr>
                                                                    <td style="padding-left: 12px; padding-right: 12px;">
                                                                        <table border="0" cellpadding="0" cellspacing="0" class="basetable" width="100%" align="left" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; table-layout: fixed; width: 100%;">
                                                                            
                                                                            <tr>
                                                                            	<?php if (intval($GLOBALS["content"]["emailer"]["payment"]["instl_total"]) == 1) { ?>
	                                                                                <td class="h1" style="color: #000000; font-family: 'Montserrat-Thin', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 20px; padding-bottom: 10px;" align="center">For this enrollment you will be charged a one-time sum of <b><?php echo ((strcmp(strtolower($GLOBALS["content"]["emailer"]["currency"]), "inr") == 0) ? '&#8377;' : '&#36;' ).number_format(intval($GLOBALS["content"]["emailer"]["sum"])); ?></b></td>
                                                                                <?php } else { ?>
                                                                                	 <td class="h1" style="color: #000000; font-family: 'Montserrat-Thin', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 20px; padding-bottom: 10px;" align="center">For this enrollment you will be charged a total sum of <b><?php echo ((strcmp(strtolower($GLOBALS["content"]["emailer"]["currency"]), "inr") == 0) ? '&#8377;' : '&#36;' ).number_format(intval($GLOBALS["content"]["emailer"]["sum_total"])); ?></b></td>
                                                                                <?php } ?>
                                                                            </tr>
                                                                            <?php if (intval($GLOBALS["content"]["emailer"]["payment"]["instl_total"]) > 1) { ?>
                                                                            <tr>
                                                                                <td class="h1" style="color: #000000; font-family: 'Montserrat-Thin', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 17px; padding-bottom: 5px;" align="center">Since you have opted to finance your enrollment in installments, please find your installment scheme as below. An email reminder with your  payment details  will be sent before each due date.</td>
                                                                            </tr>
                                                                            <?php } ?>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <!--[if (gte mso 9)|(IE)]>
                                                                </td>
                                                            </tr>
                                                            </table>
                                                            <![endif]-->
                                                            <!--[if mso]></td>
                                                            <td>
                                                            <![endif]-->
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- close price -->

                                    <?php if (intval($GLOBALS["content"]["emailer"]["payment"]["instl_total"]) > 1) {
                                            $count = 1;
                                            $due_days_tol = 0;
                                            $instl_count_text_arr = array(
                                                1 => '1<sup>st</sup> Installment',
                                                2 => '2<sup>nd</sup> Installment',
                                                3 => '3<sup>rd</sup> Installment',
                                                4 => '4<sup>th</sup> Installment',
                                                5 => '5<sup>th</sup> Installment',
                                                6 => '6<sup>th</sup> Installment',
                                                7 => '7<sup>th</sup> Installment',
                                                8 => '8<sup>th</sup> Installment',
                                                9 => '9<sup>th</sup> Installment',
                                            );
                                    ?>
                                    <!-- Instalment Box Start -->
                                    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;">
                                        <tr>
                                            <td class="outsidegutter" align="left" style="padding: 0 14px 0 14px;">
                                                <table border="0" cellpadding="0" cellspacing="0" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;"
                                                class="">
                                                <tr>
                                                    <td style="padding-bottom: 15px;">
                                                    <?php while($count <= intval($GLOBALS["content"]["emailer"]["payment"]["instl_total"])) {
                                                        $instl = $GLOBALS["content"]["emailer"]["payment"]["instl"][$count];
                                                        $due_date = date_create_from_format("Y-m-d H:i:s", $instl["due_date"] ?? "");
                                                        // Due Days Calc
                                                        if ($count > 1) {
                                                            $due_days_tol += intval($GLOBALS["content"]["emailer"]["payment"]["instl"][$count]["due_days"]);
                                                        }
                                                    ?>
                                                    <?php if ($count > 1) { ?>
                                                    <!-- installment block start -->
                                                    <!--[if (gte mso 9)|(IE)]>
                                                    <table width="224" align="left" cellpadding="0" cellspacing="0" border="0">
                                                        <tr>
                                                        <td>
                                                        <![endif]-->
                                                        <table border="0" cellpadding="0" cellspacing="0" class="t4of12" align="left" style="border: none; border-collapse: collapse; border-spacing: 0; max-width: 168px; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;">
                                                            <tr>
                                                            <td style="padding-left: 12px; padding-right: 12px;">
                                                                <table border="0" cellpadding="0" cellspacing="0" width="100%" align="left" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; table-layout: fixed; width: 100%;">
                                                                <tr>
                                                                    <td style="padding-top:15px;">
                                                                    <!--[if (gte mso 9)|(IE)]>
                                                                    <table width="224" align="left" cellpadding="0" cellspacing="0" border="0">
                                                                    <tr>
                                                                        <td>
                                                                        <![endif]-->
                                                                        <table border="1" cellpadding="5" cellspacing="5" width="100%" align="center" style="border: 1px solid #BCBEC0; border-collapse: collapse; border-spacing: 1; mso-table-lspace: 0; mso-table-rspace: 0; table-layout: fixed; width: 100%;">
                                                                        <tr>
                                                                            <td>
                                                                        <!--[if (gte mso 9)|(IE)]>
                                                                            <table width="224" align="center" cellpadding="0" cellspacing="0" border="0">
                                                                            <tr>
                                                                                <td>
                                                                            <![endif]-->
                                                                            <table border="0" cellpadding="0" cellspacing="0" class="t4of12" align="left" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;">
                                                                                <tr>
                                                                                    <td class="h1" style="color: #BCBEC0; font-family: 'Montserrat-Thin', Helvetica, Arial, sans-serif; font-size: 12px; line-height: 0px; padding: 5px;" align="center">
                                                                                        <?php echo $instl_count_text_arr[$count]; ?>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="h1" style="color: #BCBEC0; font-family: 'Montserrat-Thick', Helvetica, Arial, sans-serif; font-size: 20px; line-height: 15px; padding: 5px;" align="center">
                                                                                        <?php echo ((strcmp(strtolower($GLOBALS["content"]["emailer"]["currency"]), "inr") == 0) ? '&#8377;' : '&#36;' ).number_format(intval($GLOBALS["content"]["emailer"]["payment"]["instl"][$count]["sum"])); ?>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <?php if (empty($due_date)) { ?>
                                                                                        <td class="h1" style="color: #BCBEC0; font-family: 'Montserrat-Thick', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 15px; padding: 5px;" align="center">
                                                                                            <span style="font-size: 9px;display: block;">
                                                                                                Due in ( days from inital )
                                                                                            </span>
                                                                                            <?php echo strval($due_days_tol)." Days"; ?>
                                                                                        </td>
                                                                                    <?php } else { ?>
                                                                                        <td class="h1" style="color: #BCBEC0; font-family: 'Montserrat-Thick', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 15px; padding: 5px;" align="center">
                                                                                            <span style="font-size: 9px;display: block;">
                                                                                                Due on
                                                                                            </span>
                                                                                            <?= $due_date->format("M j<\s\up>S</\s\up>, Y"); ?>
                                                                                        </td>
                                                                                    <?php } ?>
                                                                                </tr>
                                                                            </table>
                                                                            <!--[if (gte mso 9)|(IE)]>
                                                                                </td>
                                                                            </tr>
                                                                            </table>
                                                                        <![endif]-->
                                                                            </td>
                                                                        </tr>
                                                                        </table>
                                                                        <!--[if (gte mso 9)|(IE)]>
                                                                        </td>
                                                                    </tr>
                                                                    </table>
                                                                    <![endif]-->
                                                                    <!--[if mso]></td>
                                                                        <td>
                                                                    <![endif]-->
                                                                    </td>
                                                                </tr>
                                                                </table>
                                                            </td>
                                                            </tr>
                                                        </table>
                                                        <!--[if (gte mso 9)|(IE)]>
                                                        </td>
                                                        </tr>
                                                    </table>
                                                    <![endif]-->
                                                    <!--[if mso]></td>
                                                    <td>
                                                    <![endif]-->
                                                    <!-- installment block end -->
                                                    <?php } else { ?>
                                                    <!-- linked installment block start -->
                                                    <a href="<?php echo JAWS_PATH_WEB.'/pay?pay='.$GLOBALS["content"]["emailer"]["paylink_id"]; ?>">
                                                    <!--[if (gte mso 9)|(IE)]>
                                                    <table width="224" align="left" cellpadding="0" cellspacing="0" border="0">
                                                    <tr>
                                                        <td>
                                                    <![endif]-->
                                                        <table border="0" cellpadding="0" cellspacing="0" class="t4of12" align="left" style="border: none; border-collapse: collapse; border-spacing: 0; max-width: 168px; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;">
                                                        <tr>
                                                            <td style="padding-left: 12px; padding-right: 12px;">
                                                                <table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; table-layout: fixed; width: 100%;">
                                                                <tr>
                                                                    <td style="padding-top:15px;">
                                                                    <!--[if (gte mso 9)|(IE)]>
                                                                    <table width="224" align="center" cellpadding="0" cellspacing="0" border="0">
                                                                    <tr>
                                                                        <td>
                                                                    <![endif]-->
                                                                        <table border="1" bgcolor="#E6FBFF" cellpadding="5" cellspacing="5" width="100%" align="center" style="background-color: #FFCC; border: 2px solid #e65123; border-collapse: collapse; border-spacing: 1; mso-table-lspace: 0; mso-table-rspace: 0; table-layout: fixed; width: 100%;">
                                                                        <tr>
                                                                            <td>
                                                                            <!--[if (gte mso 9)|(IE)]>
                                                                            <table width="224" align="center" cellpadding="0" cellspacing="0" border="0">
                                                                            <tr>
                                                                                <td>
                                                                            <![endif]-->
                                                                            <table border="0" cellpadding="0" cellspacing="0" class="t4of12" align="center" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;">
                                                                            <tr>
                                                                                <td class="h1" style="color: #9D9FA0; font-family: 'Montserrat-Thin', Helvetica, Arial, sans-serif; font-size: 12px; line-height: 0px; padding: 5px;" align="center">
                                                                                    <?php echo $instl_count_text_arr[$count]; ?>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="h1" style="color: #e65123; font-family: 'Montserrat-Thick', Helvetica, Arial, sans-serif; font-size: 20px; line-height: 15px; padding: 5px;" align="center">
                                                                                    <?php echo "Rs ".number_format(intval($GLOBALS["content"]["emailer"]["payment"]["instl"][$count]["sum"])); ?>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="h1" style="color: #717172; font-family: 'Montserrat-Thick', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 15px; padding: 5px;" align="center">
                                                                                    <span style="font-size: 9px;display: block;">&nbsp;</span>
                                                                                    Initial
                                                                                </td>
                                                                            </tr>
                                                                            </table>
                                                                            <!--[if (gte mso 9)|(IE)]>
                                                                                 </td>
                                                                            </tr>
                                                                            </table>
                                                                            <![endif]-->
                                                                            </td>
                                                                        </tr>
                                                                        </table>
                                                                    <!--[if (gte mso 9)|(IE)]>
                                                                        </td>
                                                                    </tr>
                                                                    </table>
                                                                    <![endif]-->
                                                                    <!--[if mso]></td>
                                                                        <td>
                                                                    <![endif]-->
                                                                    </td>
                                                                </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        </table>
                                                    <!--[if (gte mso 9)|(IE)]>
                                                        </td>
                                                    </tr>
                                                    </table>
                                                    <![endif]-->
                                                    <!--[if mso]></td>
                                                    <td>
                                                    <![endif]-->
                                                    </a>
                                                    <!-- linked installment block end -->
                                                    <?php } $count++; } ?>
                                                    </td>
                                                </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- Instalment Box End -->
                                    <?php } ?>

                                    <!-- payment link button -->
                                    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;">
                                        <tr>
                                            <td class="outsidegutter" align="left" style="padding: 0 14px 0 14px;">
                                                <table border="0" cellpadding="0" cellspacing="0" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;"
                                                    class="">
                                                    <tr>
                                                        <td>
                                                            <!--[if (gte mso 9)|(IE)]>
                                                            <table width="672" align="left" cellpadding="0" cellspacing="0" border="0">
                                                            <tr>
                                                                <td>
                                                                    <![endif]-->
                                                            <table border="0" cellpadding="0" cellspacing="0" class="t10of12 basetable" align="left" style="border: none; border-collapse: collapse; border-spacing: 0; max-width: 672px; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;">
                                                                <tr>
                                                                    <td style="padding-left: 12px; padding-right: 12px;">
                                                                        <table border="0" cellpadding="0" cellspacing="0" class="basetable" width="100%" align="left" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; table-layout: fixed; width: 100%;">
                                                                            <tr>
                                                                                <td class="p1 p1-cta" style="color: #000000; font-family: 'Montserrat-Thin', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 17px; padding-bottom: 20px;" align="center">If you would like to proceed with the payment, please click on the button below and login with your own social ID.
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <!--[if (gte mso 9)|(IE)]>
                                                                </td>
                                                            </tr>
                                                            </table>
                                                            <![endif]-->
                                                            <!--[if mso]></td>
                                                            <td>
                                                            <![endif]-->
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <!--[if (gte mso 9)|(IE)]>
                                                            <table width="336" align="center" cellpadding="0" cellspacing="0" border="0">
                                                            <tr>
                                                                <td>
                                                                    <![endif]-->
                                                            <table border="0" cellpadding="0" cellspacing="0" class="t6of12" align="center" style="border: none; border-collapse: collapse; border-spacing: 0; max-width: 336px; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;">
                                                                <tr>
                                                                    <td style="padding-left: 12px; padding-right: 12px;">
                                                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; table-layout: fixed; width: 100%;">
                                                                            <tr>
                                                                                <td align="center">
                                                                                    <!-- CTA -->
                                                                                    <div class="btn cta" lang="x-btn" style="font-family: 'Montserrat-Thick', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 15px; text-transform: uppercase;">
                                                                                        <a href="<?php echo JAWS_PATH_WEB.'/pay?pay='.$GLOBALS["content"]["emailer"]["paylink_id"]; ?>" style="background-color: #F7941D; border-color: #F7941D; border-radius: 0px; border-style: solid; border-width: 13px 16px; color: #ffffff; display: inline-block; letter-spacing: 0.5px; max-width: 300px; min-width: 110px; text-align: center; text-decoration: none; text-transform: uppercase; transition: all 0.2s ease-in;">
                                                                                            <span style="float:left;text-align:left;">proceed to make payment > </span>
                                                                                        </a>
                                                                                    </div>

                                                                                    <!-- END CTA -->
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <!--[if (gte mso 9)|(IE)]>
                                                                </td>
                                                            </tr>
                                                            </table>
                                                            <![endif]-->
                                                            <!--[if mso]></td>
                                                            <td>
                                                            <![endif]-->
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- close payment link button -->

                                    <!-- payment link copy and end -->
                                    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;">
                                        <tr>
                                            <td class="outsidegutter" align="left" style="padding: 0 14px 0 14px;">
                                                <table border="0" cellpadding="0" cellspacing="0" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;"
                                                    class="">
                                                    <tr>
                                                        <td>
                                                            <!--[if (gte mso 9)|(IE)]>
                                                            <table width="672" align="left" cellpadding="0" cellspacing="0" border="0">
                                                            <tr>
                                                                <td>
                                                                    <![endif]-->
                                                            <table border="0" cellpadding="0" cellspacing="0" class="t10of12 basetable" align="left" style="border: none; border-collapse: collapse; border-spacing: 0; max-width: 672px; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;">
                                                                <tr>
                                                                    <td style="padding-left: 12px; padding-right: 12px;">
                                                                        <table border="0" cellpadding="0" cellspacing="0" class="basetable" width="100%" align="left" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; table-layout: fixed; width: 100%;">
                                                                            <tr>
                                                                                <td class="h1" style="color: #000000; font-family: 'Montserrat-Thin', Helvetica, Arial, sans-serif; font-size: 12px; line-height: 17px; padding-top: 20px;" align="center">If you are unable to click on the button, please copy and paste the link below in your browser window: </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="p1 p1-cta" style="color: #00A8E1; font-family: 'Montserrat-Thin', Helvetica, Arial, sans-serif; font-size: 12px; line-height: 20px; padding-bottom: 10px;" align="center">
                                                                                    <?php echo JAWS_PATH_WEB.'/pay?pay='.$GLOBALS["content"]["emailer"]["paylink_id"]; ?>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="h1" style="color: #000000; font-family: 'Montserrat-Thin', Helvetica, Arial, sans-serif; font-size: 12px; line-height: 17px; padding-top: 20px;" align="center">Please ensure your banking transaction limit is above the payable amount.</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="p1 p1-cta" style="color: #000000; font-family: 'Montserrat-Thin', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 17px; padding-bottom: 15px;" align="center"> Please reach out to Jigsaw Payments team in case you have any queries or require any assistance.
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="h1" style="color: #e65123; font-family: 'Montserrat-Thin', Helvetica, Arial, sans-serif; font-size: 20px; line-height: 20px;padding-bottom: 20px;" align="center">Happy Learning!</td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <!--[if (gte mso 9)|(IE)]>
                                                                </td>
                                                            </tr>
                                                            </table>
                                                            <![endif]-->
                                                            <!--[if mso]></td>
                                                            <td>
                                                            <![endif]-->
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- close payment link copy and end -->

                                    <!--[if (gte mso 9)|(IE)]>
                                        </td>
                                    </tr>
                                    </table>
                                    <![endif]-->
                                    </td>
                                </tr>
                                    </table>
                                <!--[if (gte mso 9)|(IE)]>
                                    </td>
                                </tr>
                                </table>
                                <![endif]-->
                                </td>
                            </tr>
                            </table>
                            <!-- close wrapper -->
                            <!-- close wrapper -->
                            </td>
                        </tr>
                        </table>
                        <!--[if (gte mso 9)|(IE)]>
                        </td>
                        </tr>
                        </table>
                        <![endif]-->
                        </td>
                    </tr>
                    </table>
                </td>
                </tr>
                </table>
                <!-- Content End -->
                 <!-- Footer Start -->
                 <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;" >
                 <tr>
                     <td align="left" style="background-color:#414042;">
                         <table border="0" cellpadding="0" cellspacing="0" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;" >
                             <tr>
                                 <td align="center">
                                     <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: none; border-collapse: collapse; border-spacing: 0; max-width: 700px; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;" >
                                         <tr>
                                             <td style="padding: 0 26px;">
                                                 <!--[if (gte mso 9)|(IE)]>
                                                 <table width="648" align="center" cellpadding="0" cellspacing="0" border="0">
                                                 <tr>
                                                     <td>
                                                     <![endif]-->
                                                 <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: none; border-collapse: collapse; border-spacing: 0; max-width: 648px; mso-table-lspace: 0; mso-table-rspace: 0; table-layout: fixed; width: 100%;">
                                                     <tr>
                                                         <td>
                                                             <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;">
                                                                 <tr>
                                                                     <td align="left">
                                                                         <!--[if (gte mso 9)|(IE)]>
                                                                     <table width="648" align="left" cellpadding="0" cellspacing="0" border="0">
                                                                         <tr>
                                                                         <td>
                                                                             <![endif]-->
                                                                         <!--[if (gte mso 9)|(IE)]>
                                                                         <table width="216" align="left" cellpadding="0" cellspacing="0" border="0">
                                                                         <tr>
                                                                             <td>
                                                                             <![endif]-->
                                                                         <table border="0" cellpadding="0" cellspacing="0" class="basetable" align="left" style="border: none; border-collapse: collapse; border-spacing: 0; max-width: 200px; mso-table-lspace: 0; mso-table-rspace: 0; table-layout: fixed; width: 100%; text-align: center;">
                                                                             <tr>
                                                                                 <td style="padding-top:12px;">
                                                                                     <table border="0" cellpadding="0" cellspacing="0" class="basetable" width="100%" align="center" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; table-layout: fixed; width: 100%;">
                                                                                         <tr>
                                                                                             <td style="font-family: 'Montserrat-Thick', Helvetica, Arial, sans-serif; font-size: 14px; color: #FFFFFF;">
                                                                                                 <img src="https://www.jigsawacademy.com/emailer/images/phone-icon.jpg" height="18" width="25" />
                                                                                                 <?php echo $GLOBALS['content']['footer']['phone'] ?? '+91-90193-17000'; ?>
                                                                                             </td>
                                                                                         </tr>
                                                                                     </table>
                                                                                 </td>
                                                                             </tr>
                                                                         </table>
                                                                         <!--[if (gte mso 9)|(IE)]>
                                                                             </td>
                                                                         </tr>
                                                                         </table>
                                                                         <![endif]-->
                                                                         <!--[if mso]>
                                                                         </td>
                                                                         <td align="left">
                                                                             <![endif]-->
                                                                         <!--[if (gte mso 9)|(IE)]>
                                                                         <table width="216" align="right" cellpadding="0" cellspacing="0" border="0">
                                                                         <tr>
                                                                             <td>
                                                                             <![endif]-->
                                                                         <!-- support -->
                                                                         <table border="0" cellpadding="0" cellspacing="0" class="basetable" align="left" style="border: none; border-collapse: collapse; border-spacing: 0; max-width: 216px; mso-table-lspace: 0; mso-table-rspace: 0; table-layout: fixed; width: 100%;">
                                                                             <tr>
                                                                                 <td style="padding-top:10px;">
                                                                                     <table border="0" cellpadding="0" cellspacing="0" class="basetable" width="100%" align="left" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;">
                                                                                         <tr>
                                                                                         <td align="right" valign="bottom">
                                                                                             <img src="https://www.jigsawacademy.com/emailer/images/mail-icon.jpg" width="25" height="20" style="-ms-interpolation-mode: bicubic; border: none; clear: both; max-width: 100%; outline: none; text-decoration: none; width: auto;padding-top: 5px;" alt="" >
                                                                                         </td>
                                                                                         <td valign="center" style="font-family: 'Montserrat-Thick', Helvetica, Arial, sans-serif; font-size: 12px; color: #FFFFFF;"><a href="mailto:payments@jigsawacademy.com" style="color: #ffffff; text-decoration: none;">payments@jigsawacademy.com</a></td>
                                                                                         </tr>
                                                                                     </table>
                                                                                 </td>
                                                                             </tr>
                                                                         </table>
                                                                         <!-- END support -->
                                                                         <!--[if (gte mso 9)|(IE)]>
                                                                             </td>
                                                                         </tr>
                                                                         </table>
                                                                         <![endif]-->
                                                                         <!--[if mso]>
                                                                         </td>
                                                                         <td align="left">
                                                                             <![endif]-->
                                                                         <!--[if (gte mso 9)|(IE)]>
                                                                         <table width="216" align="right" cellpadding="0" cellspacing="0" border="0">
                                                                         <tr>
                                                                             <td>
                                                                             <![endif]-->
                                                                         <!-- support -->
                                                                         <table border="0" cellpadding="0" cellspacing="0" class="basetable" align="left" style="border: none; border-collapse: collapse; border-spacing: 0; max-width: 232px; mso-table-lspace: 0; mso-table-rspace: 0; table-layout: fixed; width: 100%;">
                                                                             <tr>
                                                                                 <td style="padding-top:10px;">
                                                                                     <table border="0" cellpadding="0" cellspacing="0" class="basetable" width="100%" align="left" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;">
                                                                                         <tr>
                                                                                         <td valign="bottom" style="font-family: 'Montserrat-Thick', Helvetica, Arial, sans-serif; font-size: 12px; color: #FFFFFF;  padding-top: 7px; text-align: center;">&copy; Manipal Innovation and Design.</td>
                                                                                         </tr>
                                                                                     </table>
                                                                                 </td>
                                                                             </tr>
                                                                         </table>
                                                                         <!-- END support -->
                                                                         <!--[if (gte mso 9)|(IE)]>
                                                                             </td>
                                                                         </tr>
                                                                         </table>
                                                                         <![endif]-->
                                                                         <!--[if (gte mso 9)|(IE)]>
                                                                             </td>
                                                                             </tr>
                                                                         </table>
                                                                         <![endif]-->
                                                                     </td>
                                                                 </tr>
                                                             </table>
                                                         </td>
                                                     </tr>
                                                     <tr>
                                                         <td style="padding:10px 0 0 0;">
                                                             <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;"
                                                                 >
                                                                 <!-- full break start-->
                                                                 <tr>
                                                                     <td height="1" style="font-size:0px;line-height:0px;background:#414042;">&nbsp;</td>
                                                                 </tr>
                                                                 <!-- full break end -->
                                                             </table>
                                                         </td>
                                                     </tr>
                                                 </table>
                                                 <!--[if (gte mso 9)|(IE)]>
                                                 </td>
                                                 </tr>
                                             </table>
                                             <![endif]-->
                                             </td>
                                         </tr>
                                     </table>
                                 </td>
                             </tr>
                         </table>
                     </td>
                 </tr>
                 <tr>
                     <td style="background-color:#FFFFFF;">
                         <table border="0" cellpadding="0" cellspacing="0" style="border: none; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;" >
                             <tr>
                                 <td align="center">
                                     <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: none; border-collapse: collapse; border-spacing: 0; max-width: 700px; mso-table-lspace: 0; mso-table-rspace: 0; width: 100%;" >
                                         <tr>
                                             <td class="h1" style="color: #717172; font-family: 'Montserrat-Thin', Helvetica, Arial, sans-serif; font-size: 8px; line-height: 10px;text-align: justify;" align="left">By proceeding you have accepted our <a href="https://mind-global.com/terms-and-conditions/">Terms and Conditions</a>.This e-mail and any attachments with it, are for the sole use of the intended recipient(s) and may contain confidential and privileged information. Unauthorized access to this e-mail (or attachments) and disclosure or copying of its contents or any action taken in reliance on this e-mail is strictly prohibited and may be unlawful. Unintended recipients must notify the sender immediately by e-mail/phone &amp; delete it from their system without making any copies or disclosing it to a third person. Before opening any attachments please check them for viruses and defects.</td>
                                         </tr>
                                     </table>
                                 </td>
                             </tr>
                         </table>
                     </td>
                 </tr>
             </table>
             <!-- Footer End -->
                </td>
              </tr>
            </table>
            <!--[if (gte mso 9)|(IE)]>
                </td>
              </tr>
            </table>
            <![endif]-->
            </td>
        </tr>
    </table>
    </body>
</html>