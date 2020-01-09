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
    load_library("payment");
    load_module("user");
    load_module("course");
    load_module("subs");
    load_module("ui");

    // Init Session
    auth_session_init();

    // Prep
    $login_params["return_url"] = JAWS_PATH_WEB."/kformalt";

    // Login Check
    if (!auth_session_is_logged()) {
        ui_render_login_front(array(
                    "mode" => "login",
                    "return_url" => $login_params["return_url"],
                    "text" => "Please login to access this page."
                ));
        exit();
    }

    // Priviledge Check
    if (!auth_session_is_allowed("paylink.create")) {
        ui_render_msg_front(array(
                "type" => "error",
                "title" => "Jigsaw Academy",
                "header" => "No Tresspassing",
                "text" => "Sorry, but you do not have permission to access this page.<br/>Contact Jigsaw Support team for assistance."
                ));

        exit();
    }

    //Render Krishna's Form

?>

    <!DOCTYPE html>
    <html lang="en">
        <head>
        
            <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
            <meta name="description" content="The Online School of Analytics">
            <meta name="author" content="BadGuppy">
            <title>Jigsaw Academy - KForm (Alternate) for the KMan!</title>
            <link rel="icon" type="image/png" href="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL.'/media/jaws/frontend/images/favicon.png'; ?>">
            
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>

            <script>
                $(document).ready(function() {

                    function rebuild_combo_str() {
                        var combovals = $('input.combosel:checked').map(function() { return this.value; }).get();
                        var combostr = "";

                        for (var i=0; i < combovals.length; i++) {
                            if (i > 0) combostr += ";";
                            combostr += combovals[i];
                            combostr += "," + $('input[name=combo-' +  combovals[i] + ']:checked').val();
                        }

                        $("#val-combostr").val(combostr);
                    }

                    $("input.combosel").change(function() {
    
                        var mode_regular = $("#combo-" + $(this).val() + "-regular");
                        var mode_premium = $("#combo-" + $(this).val() + "-premium");
    
                        if ($(this).is(':checked')) {                            
                            mode_premium.prop("checked", true).removeAttr("disabled");
                            mode_regular.prop("checked", true).removeAttr("disabled");
                        }
                        else {                            
                            mode_premium.attr("disabled", "disabled").prop("checked", false);
                            mode_regular.attr("disabled", "disabled").prop("checked", false);
                        }

                        rebuild_combo_str();
    
                    });

                    $("input.combomodesel").change(function() {

                        rebuild_combo_str();

                    });

                    $("select#instl-total").change(function() {
                        var tol = $(this).val();

                        for (var i=2; i <= parseInt(tol); i++) {
                            $("#instl-"+ i.toString() +"-sum").removeAttr("disabled").val("");
                            $("#instl-"+ i.toString() +"-due").removeAttr("disabled").val("");
                        }

                        for (var i=i; i <= 5; i++) {
                            $("#instl-"+ i.toString() +"-sum").attr("disabled","disabled").val("");
                            $("#instl-"+ i.toString() +"-due").attr("disabled","disabled").val("");
                        }
                    });

                    $("#email").focusout(function () {

                        var jaws_webapi_url = $("#txt-jaws-url").val() + "/webapi/temp/user.get.name";
                        var ret = $.post(jaws_webapi_url,
                                    {                                        
                                        email: $(this).val()
                                    },
                        
                                    function(data, status) {
                                        var ret = jQuery.parseJSON(data);
                                        if (ret["status"]) $("#name").val(ret["name"]);            
                                        else $("#name").val("");   
                                    }
                                );

                    });

                    $('#email').bind("enterKey",function(e) {
                        var jaws_webapi_url = $("#txt-jaws-url").val() + "/webapi/temp/user.get.name";
                        $.post(jaws_webapi_url,
                                    {                                        
                                        email: $("#email").val()
                                    },
                        
                                    function(data, status) {
                                        var ret = jQuery.parseJSON(data);
                                        if (ret["status"]) $("#name").val(ret["name"]);            
                                        else $("#name").val("");   
                                    }
                                );
                    });

                    $('#email').keyup(function(e) {
                        if(e.keyCode == 13)
                        {
                            $(this).trigger("enterKey");
                        }
                    });

                    $("#commitbtn").click(function() {

                        if (!confirm("Are you sure you want to generate this link?")) return;

                        var instl_arr = new Array();
                        var instl_total = parseInt($("select#instl-total").val());
                        instl_arr.push(new Array("Start from index 1 pls",""));
                        
                        for (var count=1; count <= instl_total; count++) {
                            var instl = new Array(parseInt($("#instl-"+ count.toString() +"-sum").val()), (count == 1) ? 0 : parseInt($("#instl-"+ count.toString() +"-due").val()));
                            instl_arr.push(instl);
                        }

                        var jaws_webapi_url = $("#txt-jaws-url").val() + "/webapi/temp/paylink.create.wsubs";
                        var corp = "n";
                        if ($('input#ichk-corp').is(':checked') == true) corp = "y";

                        $.post(jaws_webapi_url,
                                    {        
                                        email: $("#email").val(),
                                        name: $("#name").val(),
                                        combo: $("#val-combostr").val(),
                                        access_duration: $("#access-duration").val(),
                                        instl_total: instl_total,
                                        instl: instl_arr,
                                        paymode: $("select#pay-mode").val(),
                                        corp: corp
                                    },
                        
                                    function(data, status) {
                                        var ret = jQuery.parseJSON(data);
                                        
                                        if (ret) {
                                            alert("Link Succesfully Emailed");
                                            window.location.replace($("#txt-jaws-url").val() + "/kformalt");
                                        }
                                        else alert("Custom Link Generation FAILED!");

                                    }
                        );

                    });

                });
            </script> 
    
        </head>
        <body>
    
            <div>
                <center>
                K-Form (Alternate) for the K-Man! (You are logged in as: <?php echo $_SESSION["user"]["name"]; ?> <a href="<?php echo JAWS_PATH_WEB."/logout"; ?>">Logout</a>)
                <?php if (isset($msg)) echo "<br/>".$msg; ?>
                </center>
            </div>         
            <hr>
    
            <center>
            <table border="0" cellpadding="10" cellspacing="2" bgcolor="#f4f6f6">
                <?php
    
                $course_arr = db_query("SELECT * FROM course WHERE ((il_status_inr = TRUE) OR (sp_status_inr = TRUE)) AND (status='enabled' OR status='hidden');");

                foreach($course_arr as $course) { ?>

                    <tr>
                        <td width="250"><input type="checkbox" name="combo" class="combosel" value="<?php echo $course["course_id"]; ?>"><?php echo $course["name"]; ?></input></td>
                        <td width="250">
                            <?php if ((strcmp($course["sp_status_inr"], "1") == 0) && (isset($course["sp_price_inr"])) && (intval($course["sp_price_inr"]) > 0 )) { ?><input type="radio" name="combo-<?php echo $course["course_id"]; ?>" id="combo-<?php echo $course["course_id"]; ?>-regular" class="combomodesel" value="2" disabled="disabled">Regular (Web Price: <?php echo $course["sp_price_inr"]; ?> INR) 
                            <?php } else { ?>
                                -
                            <?php } ?>
                        </td>
                        <td width="250">
                            <?php if ((strcmp($course["il_status_inr"], "1") == 0) && (isset($course["il_price_inr"])) && (intval($course["il_price_inr"]) > 0 )) { ?><input type="radio" name="combo-<?php echo $course["course_id"]; ?>" id="combo-<?php echo $course["course_id"]; ?>-premium" class="combomodesel" value="1" disabled="disabled">Premium (Web Price: <?php echo $course["il_price_inr"]; ?> INR) 
                            <?php } else { ?>
                                -
                            <?php } ?>
                        </td>
                    </tr>

                <?php } ?>

            </table>
            </center>
            <hr>
            <br/>

           <div>
                <center>
                Access Duration: 
                <select name='access_duration' id='access-duration'>
                    <option value='0' selected>Normal (Default)</option>
                    <option value='3'>3-Month Subscription</option>
                    <option value='6'>6-Month Subscription</option>
                    <option value='9'>9-Month Subscription</option>
                    <option value='12'>12-Month Subscription</option>
                </select>
                <br/><br/>
                </center>
            </div>         
            <hr>
            <br/>

            <div><center>
            Note: Extra charges (tax, instl fee) will <b>NOT</b> be added on each entered installment amount! All prices must be <b>FINAL</b>!<br/><br/>

                <select name="instl_total" id="instl-total">
                    <option value="1" selected>Full Payment</option>
                    <option value="2">2 Installments</option>
                    <option value="3">3 Installments</option>
                    <option value="4">4 Installments</option>
                    <option value="5">5 Installments</option>
                </select>
                <br/>

                <table border="0" cellpadding="10" cellspacing="2" bgcolor="#f4f6f6">
                    <tr>
                        <td width="300"><input style="width: 100%;" type="number" placeholder="Amount (INR)" id="instl-1-sum" required></input></td>
                        <td width="300"></td>
                    </tr>
                     <tr>
                        <td width="300"><input style="width: 100%;" type="number" placeholder="Amount (INR)" id="instl-2-sum" disabled="disabled"></input></td>
                        <td width="300"><input style="width: 100%;" type="number" min="8" placeholder="Due (Days from previous installment due date)" id="instl-2-due" disabled="disabled"></input></td>
                    </tr>
                     <tr>
                        <td width="300"><input style="width: 100%;" type="number" placeholder="Amount (INR)" id="instl-3-sum" disabled="disabled"></input></td>
                        <td width="300"><input style="width: 100%;" type="number" min="8" placeholder="Due (Days from previous installment due date)" id="instl-3-due" disabled="disabled"></input></td>
                    </tr>
                     <tr>
                        <td width="300"><input style="width: 100%;" type="number" placeholder="Amount (INR)" id="instl-4-sum" disabled="disabled"></input></td>
                        <td width="300"><input style="width: 100%;" type="number" min="8" placeholder="Due (Days from previous installment due date)" id="instl-4-due" disabled="disabled"></input></td>
                    </tr>
                     <tr>
                        <td width="300"><input style="width: 100%;" type="number" placeholder="Amount (INR)" id="instl-5-sum" disabled="disabled"></input></td>
                        <td width="300"><input style="width: 100%;" type="number" min="8" placeholder="Due (Days from previous installment due date)" id="instl-5-due" disabled="disabled"></input></td>
                    </tr>
                </table>
                </center> 

            </div>
            <hr>
            <br/>

            <div>
            <center>
                Note: Only 'online' payment type will create a payment link for first installment! <br/>
                WARNING! Payment types other than 'online' means payment has already been made for first installment! <br/><br/>

                <input type="email" placeholder="Student's Email" required id="email"></input>&nbsp;&nbsp;<input type="text" placeholder="Student's Name" required id="name"></input><br/><br/>

                <input type="checkbox" disabled value="corp" name="corp" id="ichk-corp"> Corporate Student (No social login)
                <br/><br/>

                Payment Mode : <select name="Payment Type" id="pay-mode">
                    <option value="online" selected>Online (via Payment Gateway)</option>
                    <option value="cash">Cash (Already Paid)</option>
                    <option value="cheque">Cheque (Already Paid)</option>
                    <option value="dd">DD (Already Paid)</option>
                    <option value="other">Other (Already Paid)</option>
                </select>

            </center>
            </div>
            <hr>
            <br/>

            <input type="hidden" id="txt-jaws-url" value="<?php echo JAWS_PATH_WEB; ?>" />
            <input type="hidden" id="val-combostr" value="" />

            <center>
                <input type="submit" value="Commit" id="commitbtn"/>
            </center>
    
        </body>
    </html>

