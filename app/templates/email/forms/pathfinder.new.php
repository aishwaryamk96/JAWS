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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html data-editor-version="2" class="sg-campaigns" xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>The right program for you with the Jigsaw Path Selector - Jigsaw Academy</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1" /><!--[if !mso]><!-->
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" /><!--<![endif]-->
    <!--[if (gte mso 9)|(IE)]>
    <xml>
    <o:OfficeDocumentSettings>
    <o:AllowPNG/>
    <o:PixelsPerInch>96</o:PixelsPerInch>
    </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->
    <!--[if (gte mso 9)|(IE)]>
    <style type="text/css">
      body {width: 700px;margin: 0 auto;}
      table {border-collapse: collapse;}
      table, td {mso-table-lspace: 0pt;mso-table-rspace: 0pt;}
      img {-ms-interpolation-mode: bicubic;}
    </style>
    <![endif]-->

    <style type="text/css">
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
        .mont-thin{
            font-family: 'Montserrat-Thin',Helvetica,Arial,sans-serif;
        }
        .mont-thick{
            font-family: 'Montserrat-Thick',Helvetica,Arial,sans-serif;
        }
      body, p, div {
        font-family: 'Montserrat-Thin',Helvetica,Arial,sans-serif;
        font-size: 14px;
      }
      body {
        color: #000000;
      }
      body a {
        color: #1188E6;
        text-decoration: none;
      }
      p { margin: 0; padding: 0; }
      table.wrapper {
        width:100% !important;
        table-layout: fixed;
        -webkit-font-smoothing: antialiased;
        -webkit-text-size-adjust: 100%;
        -moz-text-size-adjust: 100%;
        -ms-text-size-adjust: 100%;
      }
      table.wrapper2 {
        width:50% !important;
        table-layout: fixed;
        -webkit-font-smoothing: antialiased;
        -webkit-text-size-adjust: 50%;
        -moz-text-size-adjust: 50%;
        -ms-text-size-adjust: 50%;
        margin: 15px 0;
        float: left;
      }
      img.max-width {
        max-width: 100% !important;
      }
      .column.of-2 {
        width: 50%;
      }
      .column.of-3 {
        width: 33.333%;
      }
      .column.of-4 {
        width: 25%;
      }
      .column.of-5 {
        width: 20%;
      }
      .cta {
          padding: 10px 10px;
          display: inline-block;
          text-align: center;
          text-align: -webkit-center;
          background: #f7941e;
          color: #ffffff;
          margin: 10px;
          text-transform: uppercase;
        }
        .box{
            margin: 5px auto;
            padding: 30px 10px;
            text-align: center;
            text-align: -webkit-center;
            font-size: 30px;
            background: #ffffff;
            color:<?php echo $GLOBALS['content']['emailer']['mail_data']['bgcolor']; ?>;
            max-width: 160px;
        }
        .pathfinder{
            background-color: <?php echo $GLOBALS['content']['emailer']['mail_data']['bgcolor']; ?>;
            color: #ffffff;
            background-image: url('https://www.jigsawacademy.com/wp-content/themes/jigsaw/images/new-design/PathFinder_pattern_web.png');
            margin-top: 10px;
            padding: 10px 0;
        }
      @media screen and (max-width:480px) {
        .preheader .rightColumnContent,
        .footer .rightColumnContent {
            text-align: left !important;
        }
        .preheader .rightColumnContent div,
        .preheader .rightColumnContent span,
        .footer .rightColumnContent div,
        .footer .rightColumnContent span {
          text-align: left !important;
        }
        .preheader .rightColumnContent,
        .preheader .leftColumnContent {
          font-size: 80% !important;
          padding: 5px 0;
        }
        table.wrapper-mobile {
          width: 100% !important;
          table-layout: fixed;
        }
        img.max-width {
          height: auto !important;
          max-width: 480px !important;
        }
        a.bulletproof-button {
          display: block !important;
          width: auto !important;
          font-size: 80%;
          padding-left: 0 !important;
          padding-right: 0 !important;
        }
        .columns {
          width: 100% !important;
        }
        .column {
          display: block !important;
          width: 100% !important;
          padding-left: 0 !important;
          padding-right: 0 !important;
          margin-left: 0 !important;
          margin-right: 0 !important;
        }
        .mob-width {
            width: 360px !important;
        }
      }
    </style>
    <!--user entered Head Start-->
    
     <!--End Head user entered-->
  </head>
  <body>
    <center class="wrapper" data-link-color="#1188E6" data-body-style="font-size: 14px; font-family: 'Montserrat-Thin',Helvetica,Arial,sans-serif; color: #000000; background-color: #ffffff;">
      <div class="webkit">
        <table cellpadding="0" cellspacing="0" border="0" width="100%" class="wrapper" bgcolor="#ffffff">
          <tr>
            <td valign="top" bgcolor="#ffffff" width="100%">
              <table width="100%" role="content-container" class="outer" align="center" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td width="100%">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                      <tr>
                        <td>
                          <!--[if mso]>
                          <center>
                          <table><tr><td width="700">
                          <![endif]-->
                          <table width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%; max-width:700px;" align="center">
                            <tr>
                              <td role="modules-container" style="padding: 0px 0px 0px 0px; color: #000000; text-align: left;" bgcolor="#ffffff" width="100%" align="left">
    <!-- content start -->

    <table class="module preheader preheader-hide" role="module" data-type="preheader" border="0" cellpadding="0" cellspacing="0" width="100%"
           style="display: none !important; mso-hide: all; visibility: hidden; opacity: 0; color: transparent; height: 0; width: 0;">
      <tr>
        <td role="module-content">
          <p></p>
        </td>
      </tr>
    </table>
    
    <!-- logo section -->
    <table class="wrapper" role="module" data-type="image" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;">
      <tr>
        <td style="font-size:6px;line-height:10px;padding:0px 0px 0px 0px;" valign="top" align="center">
          <img class="max-width" border="0" style="display:block;max-width:100% !important;width:100%;height:auto !important;" src="https://www.jigsawacademy.com/emailer/images/jigsaw-logo-header.jpg" alt="" width="700">
        </td>
      </tr>
    </table>
    <!-- logo section -->
    <!-- welcome section -->
    <table class="module" role="module" data-type="code" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;">
      <tr>
        <td height="100%" valign="top">
            <div class="mont-thick" style="text-align:center;color:#00a8e1;font-size: 20px;line-height: 20px;padding-bottom: 20px;font-weight: 700;font-family: 'Montserrat-Thick',Helvetica,Arial,sans-serif;">
                Welcome to Jigsaw Family
            </div>
            <div style="text-align:center;padding-bottom: 20px; font-family: 'Montserrat-Thin',Helvetica,Arial,sans-serif;">
                Hello <?php echo ucfirst(strtolower($GLOBALS["content"]["emailer"]["fname"])); ?>,
                <span class="mont-thin" style="color: rgb(0, 0, 0); font-weight: 400; text-align: -webkit-center;font-family: 'Montserrat-Thin',Helvetica,Arial,sans-serif;"><?php echo $GLOBALS["content"]["emailer"]["sub-header"]; ?></span>
            </div>
        </td>
      </tr>
    </table>
    <!-- welcome section -->
    <!-- divider section -->
    <table class="module"
           role="module"
           data-type="divider"
           border="0"
           cellpadding="0"
           cellspacing="0"
           width="100%"
           style="table-layout: fixed;">
      <tr>
        <td style="padding:0px 0px 0px 0px;"
            role="module-content"
            height="100%"
            valign="top"
            bgcolor="">
          <table border="0"
                 cellpadding="0"
                 cellspacing="0"
                 align="center"
                 width="100%"
                 height="2px"
                 style="line-height:2px; font-size:2px;">
            <tr>
              <td
                style="padding: 0px 0px 2px 0px;"
                bgcolor="#000"></td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
    <!-- divider section -->
    <!-- pathfinder section -->
    <div class="pathfinder" style="background-color: <?php echo $GLOBALS['content']['emailer']['mail_data']['bgcolor']; ?>;color: #ffffff;background-image: url('https://www.jigsawacademy.com/emailer/images/pathfinder_pattern.png');margin: 10px 0;padding: 10px 0;">
        <!-- path header -->
        <table class="module" role="module" data-type="text" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;">
        <tr>
            <td style="padding:18px 0px 18px 0px;line-height:22px;text-align:inherit;"
                height="100%"
                valign="top"
                bgcolor="">
                <?php if( $GLOBALS['content']['emailer']['mail_data']['post_id'] == '49900'){ ?>
                <table>
                    <tr>
                        <td style="width: 150px;text-align: right;">
                            <img src="https://www.jigsawacademy.com/emailer/images/iim-indore-logo.jpg" alt="IIM Indore" style="width: 50px;background: white;padding: 5px;margin: 0 15px 0 0;">
                        </td>
                        <td style="text-align:left;font-size: 25px;line-height: 30px;color:#ffffff;">
                            <?php echo $GLOBALS['content']['emailer']['mail_data']['name']; ?>
                        </td>
                    </tr>
                </table>
                <?php } else { ?>
                    <div style="text-align:center;font-size: 25px;line-height: 30px;color:#ffffff;">
                        <?php echo $GLOBALS['content']['emailer']['mail_data']['name']; ?>
                    </div>
                <?php } ?>
                    <div>&nbsp;</div>
                    <?php if(!empty($GLOBALS['content']['emailer']['mail_data']['opportunity_text'])){ ?>
                        <div style="text-align:center;color:#ffffff;"><?php echo $GLOBALS['content']['emailer']['mail_data']['opportunity_text']; ?></div>
                    <?php } ?>
            </td>
        </tr>
        </table>
        <!-- path header -->
        <?php if(!empty($GLOBALS['content']['emailer']['mail_data']['opportunity_text'])){ ?>
        <!-- path points 1 -->
        <table border="0" cellpadding="0" cellspacing="0" align="center" width="100%" role="module" data-type="columns"
            data-version="5" style="padding:0px 0px 0px 0px;" bgcolor="">
            <tr role='module-content'>
                <td height="100%" valign="top">
                    <!--[if (gte mso 9)|(IE)]>
                    <center>
                        <table cellpadding="0" cellspacing="0" border="0" width="100%" style="border-spacing:0;border-collapse:collapse;table-layout: fixed;" >
                        <tr>
                    <![endif]-->

                    <?php foreach( $GLOBALS['content']['emailer']['mail_data']['opportunity_icons'] as $icons ){ ?>
                    <!--[if (gte mso 9)|(IE)]>
                    <td width="140.000px" valign="top" style="padding: 0px 0px 0px 0px;border-collapse: collapse;" >
                    <![endif]-->
        
                    <table width="140.000" style="width:140.000px;border-spacing:0;border-collapse:collapse;margin:0px 0px 0px 0px;"
                        cellpadding="0" cellspacing="0" align="left" border="0" bgcolor="" class="column column-0 of-5 empty">
                        <tr>
                            <td class="mob-width" style="padding:0px;margin:0px;border-spacing:0;">
                                
                                <table class="wrapper" role="module" data-type="image" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;">
                                    <tr>
                                        <td style="font-size:6px;line-height:10px;padding:0px 0px 0px 0px;" valign="top" align="center">
                                            <img class="max-width" border="0" style="display:block;" src="<?php echo $icons['thumbnail']; ?>" alt="<?php echo $icons['oppr_title']; ?>" width="40">
                                        </td>
                                    </tr>
                                </table>
                                <table class="module" role="module" data-type="text" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;">
                                    <tr>
                                        <td style="padding:18px 0px 18px 0px;line-height:10px;text-align:inherit;" height="100%" valign="top" bgcolor="">
                                            <div style="text-align:center;font-size: 10px;font-family:'Montserrat-Thin',Helvetica,Arial,sans-serif;padding:0 5px;color:#ffffff;" class="mont-thin"><?php echo $icons['oppr_title']; ?></div>
                                        </td>
                                    </tr>
                                </table>

                            </td>
                        </tr>
                    </table>
        
                    <!--[if (gte mso 9)|(IE)]>
                    </td>
                    <![endif]-->
                    <?php } ?>

                    <!--[if (gte mso 9)|(IE)]>
                        <tr>
                        </table>
                    </center>
                    <![endif]-->
                </td>
            </tr>
        </table>
        <!-- path points 1 -->
        <?php } ?>
        <!-- path points 2 -->
        <table  border="0"
                cellpadding="0"
                cellspacing="0"
                align="center"
                width="100%"
                role="module"
                data-type="columns"
                data-version="2"
                style="padding:0px 0px 0px 0px;"
                bgcolor="">
            <tr role='module-content'>
                <td height="100%" valign="top">
                    <!--[if (gte mso 9)|(IE)]>
                    <center>
                    <table cellpadding="0" cellspacing="0" border="0" width="100%" style="border-spacing:0;border-collapse:collapse;table-layout: fixed;" >
                    <tr>
                    <![endif]-->
                    <!--[if (gte mso 9)|(IE)]>
                    <td width="350.000px" valign="top" style="padding: 0px 0px 0px 0px;border-collapse: collapse;">
                    <![endif]-->
                    <table width="350.000" style="width:350.000px;border-spacing:0;border-collapse:collapse;margin:0px 0px 0px 0px;"
                        cellpadding="0" cellspacing="0" align="left" border="0" bgcolor="" class="column column-1 of-2 empty">
                        <tr>
                            <td style="padding:0px;margin:0px;border-spacing:0;text-transform: uppercase;padding: 10px;text-align: center; font-weight:bold;color:#ffffff;">
                                <?php echo $GLOBALS['content']['emailer']['mail_data']['earn_text']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td height="100%" valign="top">
                                <!--[if (gte mso 9)|(IE)]>
                                <center>
                                <table cellpadding="0" cellspacing="0" border="0" width="100%" style="border-spacing:0;border-collapse:collapse;table-layout: fixed;" >
                                <tr>
                                <![endif]-->
                                
                                <!--[if (gte mso 9)|(IE)]>
                                <td width="175.000px" valign="top" style="padding: 0px 0px 0px 0px;border-collapse: collapse;" >
                                <![endif]-->
                                
                                <table width="175.000" style="width:175.000px;border-spacing:0;border-collapse:collapse;margin:0px 0px 0px 0px;" cellpadding="0" cellspacing="0" align="left" border="0" bgcolor="" class="column column-0 of-2 empty">
                                    <tr>
                                        <td style="padding:0px;margin:0px;border-spacing:0;">
                                
                                            <table class="wrapper" role="module" data-type="image" border="0" cellpadding="0" cellspacing="0" width="100%"
                                                style="table-layout: fixed;">
                                                <tr>
                                                    <td style="font-size:6px;line-height:10px;padding:0px 0px 0px 0px;" valign="top" align="center">
                                                        <img class="max-width" border="0" style="display:block;max-width:100% !important;height:auto !important;" src="https://www.jigsawacademy.com/emailer/images/pathfinder-img-inr.png" alt="INR" width="55">
                                                    </td>
                                                </tr>
                                            </table>
                                
                                            <table class="module" role="module" data-type="text" border="0" cellpadding="0" cellspacing="0" width="100%"
                                                style="table-layout: fixed;">
                                                <tr>
                                                    <td style="padding:18px 0px 18px 0px;line-height:22px;text-align:inherit;" height="100%" valign="top" bgcolor="">
                                                        <div style="text-align:center;color:#ffffff;"><?php echo $GLOBALS['content']['emailer']['mail_data']['earn_description']; ?></div>
                                                        <div style="text-align:center;color:#ffffff;"><?php echo $GLOBALS['content']['emailer']['mail_data']['salary_text_indian']; ?></div>
                                                    </td>
                                                </tr>
                                            </table>
                                
                                        </td>
                                    </tr>
                                </table>
                                
                                <!--[if (gte mso 9)|(IE)]>
                                </td>
                                <![endif]-->
                                <!--[if (gte mso 9)|(IE)]>
                                <td width="175.000px" valign="top" style="padding: 0px 0px 0px 0px;border-collapse: collapse;" >
                                <![endif]-->
                                
                                <table width="175.000" style="width:175.000px;border-spacing:0;border-collapse:collapse;margin:0px 0px 0px 0px;" cellpadding="0" cellspacing="0" align="left" border="0" bgcolor="" class="column column-1 of-2 empty">
                                    <tr>
                                        <td style="padding:0px;margin:0px;border-spacing:0;">
                                
                                            <table class="wrapper" role="module" data-type="image" border="0" cellpadding="0" cellspacing="0" width="100%"
                                                style="table-layout: fixed;">
                                                <tr>
                                                    <td style="font-size:6px;line-height:10px;padding:0px 0px 0px 0px;" valign="top" align="center">
                                                        <img class="max-width" border="0" style="display:block;max-width:100% !important;height:auto !important;" src="https://www.jigsawacademy.com/emailer/images/pathfinder-img-usd.png" alt="USD" width="55">
                                                    </td>
                                                </tr>
                                            </table>
                                
                                            <table class="module" role="module" data-type="text" border="0" cellpadding="0" cellspacing="0" width="100%"
                                                style="table-layout: fixed;">
                                                <tr>
                                                    <td style="padding:18px 0px 18px 0px;line-height:22px;text-align:inherit;" height="100%" valign="top"
                                                        bgcolor="">
                                                        <div style="text-align:center;color:#ffffff;"><?php echo $GLOBALS['content']['emailer']['mail_data']['earn_description_usd']; ?></div>
                                
                                                        <div style="text-align:center;color:#ffffff;"><?php echo $GLOBALS['content']['emailer']['mail_data']['salary_text_us']; ?></div>
                                                    </td>
                                                </tr>
                                            </table>
                                
                                        </td>
                                    </tr>
                                </table>
                                
                                <!--[if (gte mso 9)|(IE)]>
                                </td>
                                <![endif]-->
                                <!--[if (gte mso 9)|(IE)]>
                                <tr>
                                </table>
                                </center>
                                <![endif]-->
                            </td>
                        </tr>
                    </table>

                    <!--[if (gte mso 9)|(IE)]>
                    </td>
                    <![endif]-->
                    <!--[if (gte mso 9)|(IE)]>
                    <td width="350.000px" valign="top" style="padding: 0px 0px 0px 0px;border-collapse: collapse;">
                    <![endif]-->
                    <table width="350.000" style="width:350.000px;border-spacing:0;border-collapse:collapse;margin:0px 0px 0px 0px;"
                        cellpadding="0" cellspacing="0" align="left" border="0" bgcolor="" class="column column-1 of-2 empty">
                        <tr>
                            <td style="padding:0px;margin:0px;border-spacing:0;text-transform: uppercase;padding: 10px;text-align: center;color:<?php echo $GLOBALS['content']['emailer']['mail_data']['bgcolor']; ?>; font-weight:bold;" bgcolor="#ffffff">
                                <?php echo $GLOBALS['content']['emailer']['mail_data']['work_text']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td height="100%" valign="top" bgcolor="#ffffff">
                                <?php foreach($GLOBALS['content']['emailer']['mail_data']['work_description'] as $work){ ?>
                                    <table class="wrapper2" role="module" data-type="image" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;">
                                        <tr>
                                            <td style="font-size:6px;line-height:10px;padding:0px 0px 0px 0px;" valign="top" align="center">
                                                <img class="max-width" border="0" style="display:block;max-width:100% !important;height:auto !important;" src="<?php echo $work['thumbnail']; ?>" alt="" width="55">
                                            </td>
                                        </tr>
                                    </table>
                                <?php } ?>
                            </td>
                        </tr>
                    </table>
                    
                    <!--[if (gte mso 9)|(IE)]>
                    </td>
                    <![endif]-->
                    <!--[if (gte mso 9)|(IE)]>
                    <tr>
                    </table>
                    </center>
                    <![endif]-->
                </td>
            </tr>
        </table>
        <!-- path points 2 -->
        <?php if(!empty($GLOBALS['content']['emailer']['mail_data']['university_text'])){ ?>
        <!-- path header 2 -->
        <table class="module" role="module" data-type="text" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;">
            <tr>
                <td style="padding:18px 0px 18px 0px;line-height:22px;text-align:inherit;" height="100%" valign="top" bgcolor="">
                    <div style="text-align:center;color:#ffffff;"><?php echo $GLOBALS['content']['emailer']['mail_data']['university_text']; ?></div>
                </td>
            </tr>
        </table>
        <!-- path header 2 -->
        <!-- path points 3 -->
        <table border="0" cellpadding="0" cellspacing="0" align="center" width="100%" role="module" data-type="columns"
            data-version="5" style="padding:0px 0px 0px 0px;" bgcolor="">
            <tr role='module-content'>
                <td height="100%" valign="top">
                    <!--[if (gte mso 9)|(IE)]>
                    <center>
                        <table cellpadding="0" cellspacing="0" border="0" width="100%" style="border-spacing:0;border-collapse:collapse;table-layout: fixed;" >
                        <tr>
                    <![endif]-->

                    <?php foreach( $GLOBALS['content']['emailer']['mail_data']['university_icons'] as $icons ){ ?>
                    <!--[if (gte mso 9)|(IE)]>
                    <td width="175.000px" valign="top" style="padding: 0px 0px 0px 0px;border-collapse: collapse;" >
                    <![endif]-->
        
                    <table width="175.000" style="width:175.000px;border-spacing:0;border-collapse:collapse;margin:0px 0px 0px 0px;"
                        cellpadding="0" cellspacing="0" align="left" border="0" bgcolor="" class="column column-0 of-5 empty">
                        <tr>
                            <td class="mob-width" style="padding:0px;margin:0px;border-spacing:0;">
        
                                <table class="wrapper" role="module" data-type="image" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;">
                                    <tr>
                                        <td style="font-size:6px;line-height:10px;padding:0px 0px 0px 0px;" valign="top" align="center">
                                            <img class="max-width" border="0" style="display:block;" src="<?php echo $icons['thumbnail']; ?>"
                                                alt="<?php echo $icons['oppr_title']; ?>" width="65">
                                        </td>
                                    </tr>
                                </table>
                                <table class="module" role="module" data-type="text" border="0" cellpadding="0" cellspacing="0"
                                    width="100%" style="table-layout: fixed;">
                                    <tr>
                                        <td style="padding:18px 0px 18px 0px;line-height:10px;text-align:inherit;" height="100%"
                                            valign="top" bgcolor="">
                                            <div style="text-align:center;font-size: 10px;font-family:'Montserrat-Thin',Helvetica,Arial,sans-serif;padding:0 5px;max-width: 140px;margin: 0 auto;color:#ffffff;" class="mont-thin"><?php echo $icons['oppr_title']; ?></div>
                                        </td>
                                    </tr>
                                </table>
        
                            </td>
                        </tr>
                    </table>
        
                    <!--[if (gte mso 9)|(IE)]>
                    </td>
                    <![endif]-->
                    <?php  } ?>

                    <!--[if (gte mso 9)|(IE)]>
                    <tr>
                    </table>
                    </center>
                    <![endif]-->
                </td>
            </tr>
        </table>
        <!-- path points 3 -->
        <?php } ?>
        <!-- path points 4 -->
        <table  border="0"
                cellpadding="0"
                cellspacing="0"
                align="center"
                width="100%"
                role="module"
                data-type="columns"
                data-version="3"
                style="padding:0px 0px 0px 0px;"
                bgcolor="">
            <tr role='module-content'>
                <td height="100%" valign="top">
                    <!--[if (gte mso 9)|(IE)]>
                    <center>
                        <table cellpadding="0" cellspacing="0" border="0" width="100%" style="border-spacing:0;border-collapse:collapse;table-layout: fixed;" >
                        <tr>
                    <![endif]-->
            
                        <!--[if (gte mso 9)|(IE)]>
                        <td width="175.000px" valign="top" style="padding: 0px 0px 0px 0px;border-collapse: collapse;" >
                        <![endif]-->

                            <table  width="175.000"
                                    style="width:175.000px;border-spacing:0;border-collapse:collapse;margin:0px 0px 0px 0px;"
                                    cellpadding="0"
                                    cellspacing="0"
                                    align="left"
                                    border="0"
                                    bgcolor=""
                                    class="column column-0 of-4 empty" >
                            <tr>
                                <td class="mob-width" style="padding:0px;margin:0px;border-spacing:0;">
                                    <div class="box mont-thick" style="margin: 5px auto;padding: 30px 10px;text-align: center;text-align: -webkit-center;font-size: 30px;background: #ffffff;color:<?php echo $GLOBALS['content']['emailer']['mail_data']['bgcolor']; ?>;font-family: 'Montserrat-Thick',Helvetica,Arial,sans-serif;max-width: 160px;">
                                        <?php echo $GLOBALS['content']['emailer']['mail_data']['footer_text']; ?>
                                    </div>
                                </td>
                            </tr>
                            </table>

                        <!--[if (gte mso 9)|(IE)]>
                        </td>
                        <![endif]-->
                        <!--[if (gte mso 9)|(IE)]>
                        <td width="350.000px" valign="top" style="padding: 0px 0px 0px 0px;border-collapse: collapse;" >
                        <![endif]-->

                            <table  width="350.000"
                                    style="width:350.000px;border-spacing:0;border-collapse:collapse;margin:0px 0px 0px 0px;"
                                    cellpadding="0"
                                    cellspacing="0"
                                    align="left"
                                    border="0"
                                    bgcolor=""
                                    class="column column-1 of-2 empty" >
                            <tr>
                                <td class="mont-thin" style="padding:0px;margin:0px;border-spacing:0;font-family:'Montserrat-Thin',Helvetica,Arial,sans-serif;text-align: center;text-align: -webkit-center;font-size: 12px; padding: 10px;color:#ffffff;">
                                    <?php echo $GLOBALS['content']['emailer']['mail_data']['footer_description']; ?>
                                </td>
                            </tr>
                            </table>

                        <!--[if (gte mso 9)|(IE)]>
                        </td>
                        <![endif]-->
                        <!--[if (gte mso 9)|(IE)]>
                        <td width="175.000px" valign="top" style="padding: 0px 0px 0px 0px;border-collapse: collapse;" >
                        <![endif]-->

                            <table  width="175.000"
                                    style="width:175.000px;border-spacing:0;border-collapse:collapse;margin:0px 0px 0px 0px;"
                                    cellpadding="0"
                                    cellspacing="0"
                                    align="left"
                                    border="0"
                                    bgcolor=""
                                    class="column column-1 of-4 empty" >
                            <tr>
                                <td class="mob-width" style="padding:0px;margin:0px;border-spacing:0;">
                                    <div style="margin: 0 auto;text-align: center;text-align: -webkit-center;">
                                        <a class="mont-thick cta" href="<?php echo $GLOBALS['content']['emailer']['mail_data']['post_url']; ?>" style="padding: 10px 10px;display: inline-block;text-align: center;text-align: -webkit-center;background: #f7941e;color: #ffffff;margin: 25px 0px;text-transform: uppercase;font-family: 'Montserrat-Thick',Helvetica,Arial,sans-serif;text-decoration:none;">View Course Details</a>
                                    </div>
                                </td>
                            </tr>
                            </table>

                        <!--[if (gte mso 9)|(IE)]>
                        </td>
                        <![endif]-->
                    <!--[if (gte mso 9)|(IE)]>
                        <tr>
                        </table>
                    </center>
                    <![endif]-->
                </td>
            </tr>
        </table>
        <!-- path points 4 -->
    </div>
    <!-- pathfinder section -->
    <!-- divider section -->
    <table class="module" role="module" data-type="divider" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;">
        <tr>
            <td style="padding:0px 0px 0px 0px;" role="module-content" height="100%" valign="top" bgcolor="">
                <table border="0" cellpadding="0" cellspacing="0" align="center" width="100%" height="2px" style="line-height:2px; font-size:2px;">
                    <tr>
                        <td style="padding: 0px 0px 2px 0px;" bgcolor="#000"></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <!-- divider section -->
    <!-- Footer section -->
    <table class="module" role="module" data-type="code" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;">
        <tr>
            <td height="100%" valign="top">
                <div class="mont-thick" style="text-align:center;color:#00a8e1;font-size: 20px;line-height: 20px;padding: 20px 0;font-weight: 700;font-family: 'Montserrat-Thick',Helvetica,Arial,sans-serif;">
                    Happy Learning!
                </div>
            </td>
        </tr>
    </table>
    <!-- Footer section -->
    <!-- end section -->
    <table border="0" cellpadding="0" cellspacing="0" align="center" width="100%" role="module" data-type="columns"
        data-version="3" style="padding:0px 0px 0px 0px;" bgcolor="">
        <tr role='module-content'>
            <td height="100%" valign="top">
                <!--[if (gte mso 9)|(IE)]>
                <center>
                <table cellpadding="0" cellspacing="0" border="0" width="100%" style="border-spacing:0;border-collapse:collapse;table-layout: fixed;" >
                <tr>
                <![endif]-->
    
                <!--[if (gte mso 9)|(IE)]>
                <td width="233.333px" valign="top" style="height:51px;padding: 0px 0px 0px 0px;border-collapse: collapse;" >
                <![endif]-->
    
                <table width="233.333" style="height:51px;width:233.333px;border-spacing:0;border-collapse:collapse;margin:0px 0px 0px 0px;"
                    cellpadding="0" cellspacing="0" align="left" border="0" bgcolor="#414143" class="column column-0 of-3 empty">
                    <tr>
                        <td class="mob-width" style="padding:12px;margin:0px;border-spacing:0;text-align: center;">
                            <a href="tel:+919019217000" style="color:#ffffff;display: inline-block;">
                                <img align="left" src="https://www.jigsawacademy.com/emailer/images/phone-icon.jpg" width="25" alt="Phone">
                                <span style="float:left;margin-top:2px;font-size:13px" >+91 90192-17000</span>
                            </a>
                        </td>
                    </tr>
                </table>
    
                <!--[if (gte mso 9)|(IE)]>
                </td>
                <![endif]-->
                <!--[if (gte mso 9)|(IE)]>
                <td width="233.333px" valign="top" style="height:51px;padding: 0px 0px 0px 0px;border-collapse: collapse;" >
                <![endif]-->
    
                <table width="233.333" style="height:51px;width:233.333px;border-spacing:0;border-collapse:collapse;margin:0px 0px 0px 0px;"
                    cellpadding="0" cellspacing="0" align="left" border="0" bgcolor="#414143" class="column column-0 of-3 empty">
                    <tr>
                        <td class="mob-width" style="padding:13px;margin:0px;border-spacing:0;text-align: center;">
                            <a href="mailto:info@jigsawacademy.com" style="color:#ffffff;display: inline-block;height:18px;">
                                <img align="left" style="margin-top: 3px;" src="https://www.jigsawacademy.com/emailer/images/mail-icon.jpg" width="25" alt="Phone">
                                <span style="float:left;margin-top:2px;font-size:13px">info@jigsawacademy.com</span>
                            </a>
                        </td>
                    </tr>
                </table>
    
                <!--[if (gte mso 9)|(IE)]>
                </td>
                <![endif]-->
                <!--[if (gte mso 9)|(IE)]>
                <td width="233.333px" valign="top" style="height:51px;padding: 0px 0px 0px 0px;border-collapse: collapse;" >
                <![endif]-->
    
                <table width="233.333" style="height:51px;width:233.333px;border-spacing:0;border-collapse:collapse;margin:0px 0px 0px 0px;"
                    cellpadding="0" cellspacing="0" align="left" border="0" bgcolor="#414143" class="column column-0 of-3 empty">
                    <tr>
                        <td class="mob-width" style="padding:15px 0;margin:0px;border-spacing:0;color:#ffffff;font-size: 9px;height: 21px;text-align: center;">
                            &copy; Jigsaw Academy Education Pvt. Ltd.
                        </td>
                    </tr>
                </table>
    
                <!--[if (gte mso 9)|(IE)]>
                </td>
                <![endif]-->
                <!--[if (gte mso 9)|(IE)]>
                <tr>
                </table>
                </center>
                <![endif]-->
            </td>
        </tr>
    </table>
    <!-- end section -->
    <!-- content end -->
                              </td>
                            </tr>
                            <tr>
                                <td style="font-size: 8px;color: #717172;text-align: justify;line-height: 10px;font-family: 'Montserrat-Thin',Helvetica,Arial,sans-serif;">
                                    This e-mail and any attachments with it, are for the sole use of the intended recipient(s) and may contain confidential and privileged information. Unauthorized access to this e-mail (or attachments) and disclosure or copying of its contents or any action taken in reliance on this e-mail is strictly prohibited and may be unlawful. Unintended recipients must notify the sender immediately by e-mail/phone & delete it from their system without making any copies or disclosing it to a third person. Before opening any attachments please check them for viruses and defects.
                                </td>
                            </tr>
                          </table>
                          <!--[if mso]>
                          </td></tr></table>
                          </center>
                          <![endif]-->
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </div>
    </center>
  </body>
</html>