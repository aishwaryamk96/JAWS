<?php

db_exec("UPDATE course SET course_code='JBDA' WHERE course_id=6;");

$to = "manish.b@jigsawacademy.com, moses.kola@jigsawacademy.com";
$subject = "Jigsaw Big Data Course Code Change Done";

$message = "<html><body>Hi Manish garu and Moses garu,<br /><br />I have updated the Big data course code to JBDA in JAWS.<br /><br />Cheers,<br />Himanshu Malpande!</body></html>";

$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= 'From: <no-reply@jigsawacademy.com>' . "\r\n";
mail($to, $subject, $message, $headers);

?>
