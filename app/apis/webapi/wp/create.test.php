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

// Check Auth
if (!auth_api("email.custom")) die("You do not have the required priviledges to use this feature.");


$email = trim($_POST['email']);
$course_code = trim($_POST['course_code']);
$datetime = new DateTime();
$start_date = $datetime->modify('+1 hour')->format(DateTime::ATOM); // Updated ISO8601
$end_date = $datetime->modify('+3 day')->format(DateTime::ATOM);

$db_out = db_query("SELECT * FROM exam_links WHERE email =" . db_sanitize($email) . " AND course_code =" . db_sanitize($course_code) . " AND status='pending' ORDER BY id DESC;");

if(!empty($db_out)) {
    die($db_out[0]['url']);
    exit;
}

$slug = 'jdpw8'; // test slug

$url = 'https://api.doselect.com/platform/v1/test/' . $slug . '/candidates';

$headers = array(
    'Content-Type: application/json',
    'DoSelect-Api-Key: c46fb081a8254a308fbf670c2edecda6',
    'DoSelect-Api-Secret: d7c4d2c6404622c014ec81de5377cedd06cf3b7ef14aeaecd28b75c7c35357bc'
);

$post = array(
    'email' => $email,
    'start_time' => $start_date,
    'expiry'  =>  $end_date
);

$url = rtrim($url, '/ ') . '?suppress_email=True';

$request = curl_init($url);
curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($request, CURLOPT_POST, true);
curl_setopt($request, CURLOPT_POSTFIELDS, json_encode($post));

$response = (string) curl_exec($request);
if (curl_errno($request)) {
    echo "Error: " . curl_error($request);
    exit();
}

$result = json_decode($response, true);
curl_close($request);

db_exec("INSERT INTO exam_links ( email, start_at, end_at, status, url, course_code, meta ) VALUES ( " . db_sanitize($email) . ", " . db_sanitize($start_date) . ", " . db_sanitize($end_date) . ", " . db_sanitize($result['status']) .", " . db_sanitize($result['candidate_access_url']) . ", " . db_sanitize($course_code) . ", " . db_sanitize(json_encode($result)) . " );");

echo $result['candidate_access_url'];

exit;

?>