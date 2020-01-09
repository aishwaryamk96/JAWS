<!doctype html>
<html>
<head>
    <title>Your Webinar Registration is Complete</title>
</head>
<body>
    <div style="width: 600px; margin: 0 auto; font-size: 14px; font-family: arial, verdana;">
        <table style="width: 100%; max-width: 600px;">
            <tr>
                <th align="left">
                    <img src="https://www.jigsawacademy.com/jaws/media/jaws/frontend/images/jigsaw-logotype.png" alt="Jigsaw Academy" width="200" height="40" />
                </th>
                <th align="right">
                    <img src="<?php echo $GLOBALS['content']['emailer']['data']['image']; ?>" alt="MISB Bocconi" width="200" height="40" />
                </th>
            </tr>
            <tr>
                <td colspan="2">
                    <img src="https://www.jigsawacademy.com/jaws/media/jaws/frontend/images/webinar-registration-emailer.jpg" alt="Grad Hats" />
                </td>
            </tr>
            <tr>
                <td colspan="2">
                   <table>
                       <tr>
                           <td style="padding: 10px 40px;">Hi Name!</td>
                       </tr>
                       <tr>
                           <td style="padding: 10px 40px;">
                               <?php echo $GLOBALS['content']['emailer']['data']['description']; ?>
                           </td>
                       </tr>
                       <tr>
                           <td style="padding-bottom: 15px;">
                               <table align="center" style="border: 1px solid #F6F6F6;padding: 10px;">
                                   <tr>
                                       <td align="center" style="padding-bottom: 15px;color:#003A6C">
                                           <b><?php echo $GLOBALS['content']['emailer']['data']['title']; ?></b>
                                       </td>
                                   </tr>
                                   <tr>
                                       <td align="center">
                                           with
                                       </td>
                                   </tr>
                                   <tr>
                                       <td align="center">
                                           <b><?php echo $GLOBALS['content']['emailer']['data']['by']; ?></b>
                                       </td>
                                   </tr>
                                   <tr>
                                       <td align="center">
                                           <i><?php echo $GLOBALS['content']['emailer']['data']['position']; ?></i>
                                       </td>
                                   </tr>
                                   <tr>
                                       <td align="center">
                                           on
                                       </td>
                                   </tr>
                                   <tr>
                                       <td align="center" style="padding-bottom:10px;">
                                           <b><?php echo $GLOBALS['content']['emailer']['data']['name']; ?></b>
                                       </td>
                                   </tr>
                                   <tr>
                                       <td align="center" style="color:#42B4E4;">
                                           <?php echo date("l, jS F Y", strtotime($GLOBALS['content']['emailer']['data']['date'])); ?>
                                       </td>
                                   </tr>
                                   <tr>
                                       <td align="center" style="color:#42B4E4;">
                                           <?php echo $GLOBALS['content']['emailer']['data']['time'].' '.$GLOBALS['content']['emailer']['data']['ampm'] ?> to <?php $timestamp = strtotime($GLOBALS['content']['emailer']['data']['time'].' '.$GLOBALS['content']['emailer']['data']['ampm']) + 60*60; $time = date('H:i', $timestamp); echo $time; ?>
                                       </td>
                                   </tr>
                               </table>
                           </td>
                       </tr>
                       <tr>
                           <td bgcolor="#F1F1F2" >
                               <table style="width: 100%; max-width: 600px; padding: 15px;">
                                   <tr>
                                       <td colspan="3" align="center" bgcolor="#F1F1F2">ATTEND THE WEBINAR IN 3 STEPS</td>
                                   </tr>
                                   <tr>
                                       <td align="center" width="198">
                                           <img src="https://www.jigsawacademy.com/jaws/media/jaws/frontend/images/login-mail.jpg" alt="Join" />
                                           <br>
                                           Go to login page - 
                                           <br>
                                           <a href="<?php echo $GLOBALS['content']['emailer']['data']['url']; ?>" style="color:#42B4E4;text-decoration:none;"><?php echo $GLOBALS['content']['emailer']['data']['url']; ?></a>
                                        </td>
                                        <td align="center" width="198">
                                            <img src="https://www.jigsawacademy.com/jaws/media/jaws/frontend/images/listdetails-mail.jpg" alt="Details" />
                                            <br>
                                            Enter your name and<br/> email address
                                        </td>
                                        <td align="center" width="198">
                                            <img src="https://www.jigsawacademy.com/jaws/media/jaws/frontend/images/click-mail.jpg" alt="Link" />
                                           <br>
                                           Click Join Now
                                       </td>
                                   </tr>
                               </table>
                           </td>
                       </tr>
                       <tr>
                           <td align="center" style="padding: 10px 0;">
                            If you have any trouble logging in or any further queries, please call: <b style="color:#42B4E4;"><?php echo $GLOBALS['content']['emailer']['data']['phone']; ?></b> or write to us at: <b style="color:#42B4E4;"><?php echo $GLOBALS['content']['emailer']['data']['email']; ?></b>
                           </td>
                       </tr>
                       <tr>
                           <td align="center" style="padding-top:30px;padding-bottom:10px; color:#42B4E4; text-decoration:none;">
                               <?php $e = array(
                                   0 => array(
                                       'date_start' => $GLOBALS['content']['emailer']['data']['date'],
                                       'date_end' => $GLOBALS['content']['emailer']['data']['date'],
                                       'timezone' => 'Asia%2FKolkata',
                                       'title'  => $GLOBALS['content']['emailer']['data']['title'],
                                       'description' => '',
                                       'location' => 'online',
                                       'organizer' => 'Jigsaw Academy',
                                       'organizer_email' => $GLOBALS['content']['emailer']['data']['email'],
                                       'privacy' => 'public'
                                   )
                               ); $e = http_build_query($e); ?>
                               <b style="color: #333333";>ADD TO CALENDAR:</b> <a href="https://addtocalendar.com/atc/ical?f=m&<?php echo $e; ?>">iCaldendar</a> . <a href="https://addtocalendar.com/atc/google?f=m&<?php echo $e; ?>">Google</a> . <a href="https://addtocalendar.com/atc/outlook?f=m&<?php echo $e; ?>">Outlook</a> . <a href="https://addtocalendar.com/atc/outlookonline?f=m&<?php echo $e; ?>">Outlook Online</a> . <a href="https://addtocalendar.com/atc/yahoo?f=m&<?php echo $e; ?>">Yahoo!</a>
                           </td>
                       </tr>
                   </table>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table bgcolor="#42B4E4" style="width: 100%; max-width: 600px;">
                        <tr>
                            <td align="left">
                                <table style="width: 100%; max-width: 150px;">
                                    <tr>
                                        <td align="center" style="color: #FFFFFF; font-size: 12px;"><b>CONTACT</b></td>
                                    </tr>
                                    <tr>
                                        <td align="center" style="color: #FFFFFF; font-size: 10px;"><?php echo $GLOBALS['content']['emailer']['data']['phone']; ?></td>
                                    </tr>
                                    <tr>
                                        <td align="center" style="color: #FFFFFF; font-size: 10px;"><i>(10AM - 6PM, Mon-Sat)</i></td>
                                    </tr>
                                </table>
                            </td>
                            <td align="left">
                                <table style="width: 100%; max-width: 150px;">
                                    <tr>
                                        <td align="center" style="color: #FFFFFF; font-size: 12px;"><b>EMAIL</b></td>
                                    </tr>
                                    <tr>
                                        <td align="center" style="color: #FFFFFF; font-size: 10px;"><?php echo $GLOBALS['content']['emailer']['data']['email']; ?></td>
                                    </tr>
                                </table>
                            </td>
                            <td align="left">
                                <table style="width: 100%; max-width: 150px;">
                                    <tr>
                                        <td align="center" style="color: #FFFFFF; font-size: 12px;"><b>WEBSITE</b></td>
                                    </tr>
                                    <tr>
                                        <td align="center" style="color: #FFFFFF; font-size: 10px;"><a><?php echo $GLOBALS['content']['emailer']['data']['link']; ?></a></td>
                                    </tr>
                                </table>
                            </td>
                            <td align="left">
                                <table style="width: 100%; max-width: 150px;">
                                    <tr>
                                        <td align="center" style="color: #FFFFFF; font-size: 12px;"><b>BLOG</b></td>
                                    </tr>
                                    <tr>
                                        <td align="center" style="color: #FFFFFF; font-size: 10px;"><a>https://analyticstraining.com</a></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>