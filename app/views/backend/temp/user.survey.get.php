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
    load_plugin("phpexcel");
    load_module("ui");

    // Init Session
    auth_session_init();

    // Prep
    $login_params["return_url"] = JAWS_PATH_WEB."/qform";

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
    if (!auth_session_is_allowed("user.survey.get")) {
        ui_render_msg_front(array(
                "type" => "error",
                "title" => "Jigsaw Academy",
                "header" => "No Tresspassing",
                "text" => "Sorry, but you do not have permission to access this page.<br/>Contact Jigsaw Support team for assistance."
                ));
        exit();
    }

    if (!empty($_POST["from"])) {

        $from = db_sanitize($_POST["from"]);
        $to = db_sanitize($_POST["to"]);

        // Download Survet Data
        $lqueryres_arr = array(
            0 => array(
                "title" => "Users Details",

                "data" => db_query("SELECT
                                                    main.name AS Name,
                                                    main.phone AS Phone,
                                                    main.email AS Email,
                                                    meta.city AS City,
                                                    meta.state AS State,
                                                    meta.country AS Country,
                                                    meta.age AS Age,
                                                    meta.gender AS Gender,
                                                    meta.qualification AS Qualification,
                                                    meta.experience AS Experience,
                                                    main.soc_fb AS 'Facebook ID',
                                                    main.soc_gp AS 'Google+ ID',
                                                    main.soc_li AS 'LinkedIn ID',
                                                    meta.reg_date AS 'Account Created',
                                                    meta.survey_date AS 'Survey Date'
                                                FROM
                                                    user AS main
                                                INNER JOIN user_meta AS meta
                                                    ON main.user_id = meta.user_id
                                                WHERE
                                                    (`status`='active') AND
                                                    (DATE(meta.survey_date) > $from) AND
                                                    (DATE(meta.survey_date) < $to)
                                                ORDER BY
                                                    meta.survey_date ASC;
                                                ")
                ),

            1 => array(
                "title" => "Survey Data",

                "data" => db_query("SELECT
                                                    main.name AS Name,
                                                    main.phone AS Phone,
                                                    main.email AS Email,
                                                    common_schema.extract_json_value(meta.survey_data,'/why') AS 'Why did you choose a course in Analytics?',
                                                    meta.leads_media_src AS 'How did you get to know about Jigsaw?',
                                                    common_schema.extract_json_value(meta.survey_data,'/enquiry') AS 'How did you get in touch with us?',
                                                    common_schema.extract_json_value(meta.survey_data,'/sales') AS 'Which of these helped you make up your mind to enroll?'
                                                FROM
                                                    user AS main
                                                INNER JOIN user_meta AS meta
                                                    ON main.user_id = meta.user_id
                                                WHERE
                                                    (`status`='active') AND
                                                    (DATE(meta.survey_date) > $from) AND
                                                    (DATE(meta.survey_date) < $to)
                                                ORDER BY
                                                    meta.survey_date ASC;")
                )
        );

        $prop = array(
            "title" => "Students Survey (".date("F j, Y").")",
            "category" => "survey feedback experience engagement review analysis"
            );

        phpexcel_write($lqueryres_arr, $prop, "Students Survey (".date("F j, Y").").xls");
        exit();

    }

?>
<html>
<head>
    <title>Q form</title>
</head>
<body>
    <div style="display: flex">
        <form method="post" style="margin: auto">
            From: <input type="date" name="from"><br>
            From: <input type="date" name="to"><br>
            <input type="submit" value="Get me the data">
        </form>
    </div>
</body>
</html>