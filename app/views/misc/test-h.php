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
		header('Location: ../index.php');
		die();
	}

	session_start();

	error_reporting(E_ALL);
	// ----- CUSTOM CODE ---------------------------------------------------------
	// --------------------------------------------------------------------------------

	// $could_not_find = [];
	// $no_pay = [];
	// $instl1_mismatch = [];
	// $instl1_bad_status = [];
	// $instlx_mismatch = [];
	// $instlx_missing = [];

	// $records_processed = 0;
	// $instls_processed = 0;

	// $data = file("external/temp/payments.csv");
	// // $data[] = "barshneya.talukdar@hotmail.com,70990,9016,3/30/2016,30987,9/7/2016,30987";
	// foreach ($data as $line) {

	// 	$line = trim($line);

	// 	$components = explode(",", $line);
	// 	// Search the user
	// 	$user = user_get_by_email(trim($components[0]));
	// 	if ($user === false) {

	// 		$could_not_find[] = $components[0];
	// 		continue;

	// 	}

	// 	// Get payment master info
	// 	$pay = db_query("SELECT pay_id FROM payment WHERE status='paid' AND sum_total=".trim($components[1])." AND user_id=".$user["user_id"].";");
	// 	if ($pay === false || empty($pay)) {

	// 		$no_pay[] = $components[0];
	// 		continue;

	// 	}

	// 	$pay = $pay[0];
	// 	// Get first instl info
	// 	$instl = db_query("SELECT instl_id, status FROM payment_instl WHERE instl_count=1 AND pay_id=".$pay["pay_id"]." AND sum=".trim($components[4]).";");
	// 	if ($instl === false || empty($instl)) {

	// 		$instl1_mismatch[] = $components[0];
	// 		continue;

	// 	}

	// 	$instl = $instl[0];
	// 	// If the 1st instl is marked 'disabled', mark it paid
	// 	if ($instl["status"] != "paid" && $instl["status"] != "disabled") {

	// 		$instl1_bad_status[] = $components[0];
	// 		continue;

	// 	}
	// 	// if ($instl["status"] == "disabled") {

	// 	// 	// echo "First instalment, ".$instl["instl_id"].", is 'disabled'. It will be marked as 'paid'<br>";
	// 	// 	// db_exec("UPDATE payment_instl SET status='paid' WHERE instl_id=".$instl["instl_id"]);

	// 	// }

	// 	$offset = 2;
	// 	$i = 2;
	// 	while (!empty($components[$offset * $i + 2]) && strlen(trim($components[$offset * $i + 2])) != 0) {

	// 		if (empty($components[$offset * $i + 2]) || strlen(trim($components[$offset * $i + 2])) == 0) {
	// 			break;
	// 		}
	// 		$instlx = db_query("SELECT instl_id, status, pay_date FROM payment_instl WHERE instl_count=".$i." AND pay_id=".$pay["pay_id"].";");
	// 		if ($instlx === false || empty($instlx)) {

	// 			$instlx_mismatch[] = ["i" => $i, "e" => $components[0]];
	// 			$i++;
	// 			continue;

	// 		}

	// 		$instlx = $instlx[0];
	// 		if (empty($instlx["instl_id"])) {

	// 			$instlx_missing[] = ["i" => $i, "e" => $components[0]];
	// 			$i++;
	// 			continue;

	// 		}
	// 		if ($instlx["status"] != "paid") {

	// 			$date = date_create_from_format("m/d/Y", $components[$offset * $i + 1]);
	// 			echo $instlx["instl_id"]." instalment is '".$instlx["status"]."'. It will be marked as 'paid'<br>";
	// 			db_exec("UPDATE payment_instl SET status='paid', pay_date=".db_sanitize($date->format("Y-m-d H:i:s"))." WHERE instl_id=".$instlx["instl_id"].";");
	// 			db_exec("UPDATE payment_link SET status='used' WHERE instl_id=".$instlx["instl_id"].";");

	// 			$instls_processed++;

	// 		}
	// 		else {

	// 			if (empty($instlx["pay_date"])) {

	// 				$date = date_create_from_format("m/d/Y", $components[$offset * $i + 1]);
	// 				echo $instlx["instl_id"]." instalment's pay_date is empty. It will be updated.<br>";
	// 				db_exec("UPDATE payment_instl SET pay_date=".db_sanitize($date->format("Y-m-d H:i:s"))." WHERE instl_id=".$instlx["instl_id"].";");
	// 				db_exec("UPDATE payment_link SET status='used' WHERE instl_id=".$instlx["instl_id"].";");

	// 				$instls_processed++;

	// 			}

	// 		}
	// 		$i++;

	// 	}

	// 	$records_processed++;

	// }

	// echo $records_processed." records were processed properly.<br><br>";
	// echo $instls_processed." instalments were modified.<br><br>";

	// if (!empty($could_not_find)) {
	// 	echo "Below users were not found in the system:<br>".implode(",", $could_not_find)."<br><br>";
	// }
	// if (!empty($no_pay)) {
	// 	echo "Appropriate payment info was not found for the below users:<br>".implode(",", $no_pay)."<br><br>";
	// }
	// if (!empty($instl1_mismatch)) {
	// 	echo "First instalment info did not match for the following users:<br>".implode(",", $instl1_mismatch)."<br><br>";
	// }
	// if (!empty($instl1_bad_status)) {
	// 	echo "First instalment status is neither 'paid' nor 'disabled' for following users:<br>".implode(",", $instl1_bad_status)."<br><br>";
	// }
	// if (!empty($instlx_missing)) {

	// 	echo "Further instalments not found for the following users:<br>";
	// 	foreach ($instlx_missing as $record) {
	// 		echo $record["i"]." - ".$record["e"]."<br>";
	// 	}

	// }
	// if (!empty($instlx_mismatch)) {

	// 	echo "Further instalments' info did not match for the following users:<br>";
	// 	foreach ($instlx_mismatch as $record) {
	// 		echo $record["i"]." - ".$record["e"]."<br>";
	// 	}

	// }

	// $course_id = 109;
	// $GLOBALS["ml_effective"] = true;
	// $enrs = db_query("SELECT enr.enr_id, section.start_date FROM user_enrollment AS enr INNER JOIN subs ON subs.subs_id = enr.subs_id INNER JOIN course_section AS section ON section.id = enr.section_id WHERE enr.course_id = 5 AND DATE(subs.start_date) > '2017-11-16';");
	// foreach ($enrs as $enr) {

	// 	$section = section_get_for_date_create($course_id, $enr["start_date"], 3);
	// 	db_exec("UPDATE user_enrollment SET sis_status = 'na', course_id = ".$course_id.", section_id = ".$section["id"]." WHERE enr_id = ".$enr["enr_id"]);

	// }

	// $data = file("external/temp/old_dsr.csv");
	// foreach ($data as $line) {

	// 	$info = explode(",", $line);
	// 	$enr = db_query("SELECT enr.enr_id, subs.subs_id, subs.combo FROM user_enrollment AS enr INNER JOIN subs ON subs.subs_id = enr.subs_id WHERE enr.course_id = 5 AND enr.sis_id = ".db_sanitize($info[0]).";");
	// 	if (empty($enr)) {

	// 		echo $info[0]." is not enrolled in course ID 5<br><br>";
	// 		continue;

	// 	}
	// 	$enr = $enr[0];

		// echo "Processing ".$info[0]." => Combo = ".$enr["combo"]."<br>";

	// 	$writeback = false;
	// 	$combo_writeback = [];
	// 	$combo = explode(";", $enr["combo"]);
	// 	foreach ($combo as $course) {

	// 		$id = explode(",", $course);
	// 		if ($id[0] == 5) {

	// 			$writeback = true;
	// 			$id[0] = 109;

	// 		}

	// 		$combo_writeback[] = $id[0].",".$id[1];

	// 	}
	// 	if ($writeback) {

	// 		// echo "UPDATE subs SET combo = ".db_sanitize(implode(";", $combo_writeback))." WHERE subs_id = ".$enr["subs_id"].";<br>";
	// 		db_exec("UPDATE subs SET combo = ".db_sanitize(implode(";", $combo_writeback))." WHERE subs_id = ".$enr["subs_id"].";");

	// 	}

	// 	$info[1] = trim($info[1]);
	// 	$section_date_str = "01 ".substr($info[1], 3, 3)." ".substr($info[1], 8);
	// 	$section_date = date_create_from_format("d M y", $section_date_str);
	// 	$section = section_get_for_date_create(109, $section_date, 3);
	// 	// echo "UPDATE user_enrollment SET course_id = 109, section_id = ".$section["id"]." WHERE enr_id = ".$enr["enr_id"].";<br><br>";
	// 	db_exec("UPDATE user_enrollment SET course_id = 109, section_id = ".$section["id"]." WHERE enr_id = ".$enr["enr_id"].";");

	// }

	// load_module("user");

	// $skipped = [];
	// $processed = 0;

	// $emails = ["dom.rosenberg@hotmail.com","dom.rosenberg@hotmail.com","sandhya.saranathan@gmail.com","sandhya.saranathan@gmail.com","lavan.117@gmail.com","lavan.117@gmail.com","adisingh75@gmail.com","anindyadas2431@gmail.com","anindyadas2431@gmail.com","sonythomas4@gmail.com","sonythomas4@gmail.com","mushtaq.kanjipani@gmail.com","arjunl.lokesh@gmail.com","arjunl.lokesh@gmail.com","Surabhi.srivastava13@gmail.com","amith.v.nayak@gmail.com","devanshigandhi16@gmail.com","abdulquddus.md@gmail.com","jaiswalrht.777@gmail.com","17.moses@gmail.com","17.moses@gmail.com","vinayakjoshi25@gmail.com","deepeshsharma9@gmail.com","supriyaroyipt@gmail.com","supriyaroyipt@gmail.com","callmejeet@gmail.com","mailsharmilamathan@gmail.com","mailsharmilamathan@gmail.com","mailmeam27@gmail.com","mailsharmilamathan@gmail.com","mailsharmilamathan@gmail.com","johntekdek@gmail.com"];
	// foreach ($emails as $email) {

	// 	$pays = db_query(
	// 		"SELECT
	// 			link.paylink_id
	// 		FROM
	// 			payment AS pay
	// 		INNER JOIN
	// 			payment_link AS link
	// 			ON link.pay_id = pay.pay_id
	// 		INNER JOIN
	// 			user ON user.user_id = pay.user_id
	// 		WHERE
	// 			pay.status != 'paid'
	// 			AND
	// 			(
	// 				user.email = ".db_sanitize($email)."
	// 				OR
	// 				user.soc_fb = ".db_sanitize($email)."
	// 				OR
	// 				user.soc_gp = ".db_sanitize($email)."
	// 				OR
	// 				user.soc_li = ".db_sanitize($email)."
	// 			);"
	// 	);

	// 	foreach ($pays as $pay) {

	// 		db_exec("UPDATE payment_link SET status = 'disabled' WHERE paylink_id = ".$pay["paylink_id"]);
	// 		$processed++;

	// 	}

	// }

	// echo count($skipped)." emails were skipped.<br>";
	// if (count($skipped) > 0) {
	// 	echo implode(", ", $skipped)."<br><br>";
	// }

	// echo $processed." payment links disabled.";

	// if (isset($_POST["jig_id"])) {

	// 	$sis_ids = [];
	// 	$courses = db_query("SELECT course.sis_id FROM course INNER JOIN user_enrollment AS ue ON ue.course_id = course.course_id WHERE ue.sis_id = ".db_sanitize($_POST["jig_id"]));
	// 	foreach ($courses as $course) {
	// 		$sis_ids[] = $course["sis_id"];
	// 	}

	// 	$_POST["course_codes"] = implode(";", $sis_ids);

	// 	$opts = array('http' => array(
	// 				'method'  => 'POST',
	// 				'header'  => 'Content-type: application/x-www-form-urlencoded',
	// 				'content' => http_build_query($_POST)
	// 			)
	// 		);
	// 	$context  = stream_context_create($opts);
	// 	// die(file_get_contents("https://jigsawacademy.net/app/getcoursestopics.php", false, $context));
	// 	var_dump(file_get_contents("https://jigsawacademy.net/app/getcourseprogress.php", false, $context), true);

	// }

	// load_module("refer");

	// $res = refer_get_statistics();
	// foreach ($res as $ref) {
	// 	var_dump($ref); echo "<br>";
	// }

	// var_dump($_SESSION);

	// load_plugin("aws_lab");
	// $aws = AwsLab::killInstances();

	echo json_encode(openssl_x509_parse("-----BEGIN CERTIFICATE-----
MIIDnDCCAoSgAwIBAgIGAWADuHSMMA0GCSqGSIb3DQEBBQUAMIGOMQswCQYDVQQGEwJVUzETMBEG
A1UECAwKQ2FsaWZvcm5pYTEWMBQGA1UEBwwNU2FuIEZyYW5jaXNjbzENMAsGA1UECgwET2t0YTEU
MBIGA1UECwwLU1NPUHJvdmlkZXIxDzANBgNVBAMMBmVkY2FzdDEcMBoGCSqGSIb3DQEJARYNaW5m
b0Bva3RhLmNvbTAeFw0xNzExMjgxNzQwNDhaFw0yNzExMjgxNzQxNDhaMIGOMQswCQYDVQQGEwJV
UzETMBEGA1UECAwKQ2FsaWZvcm5pYTEWMBQGA1UEBwwNU2FuIEZyYW5jaXNjbzENMAsGA1UECgwE
T2t0YTEUMBIGA1UECwwLU1NPUHJvdmlkZXIxDzANBgNVBAMMBmVkY2FzdDEcMBoGCSqGSIb3DQEJ
ARYNaW5mb0Bva3RhLmNvbTCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBAJk86IVKHE1b
dPi2+xSJDT2jtWnRLROdlAlF7Nwj6QtQ4Si6vbNu82w7fXTNuuX0l4uLKCizaQLSRjKYp1w0HgRF
tqAGdffpkJKUjEvvtQzuPzFSuc4A67dcPL1R1W+Zw2jIFbh8qlnxxUHb6Lun17HWV0TNI3UUBWut
rZX5coUXnmgduUzbmnXD9o1QYmJNcu15ySvMjYZ8V13T1Jmh5eOhQvVhuyqKWh7oBeRMzMEnDjsy
59v29pyRpqGkIy0JiXN/ZIxdTVhxFrrIQd5n+RyHsQ6wxw72CDCpNCkOlp1KVCN3uJ+N+s4ueeMc
fDN/I5xDX4mrg2ulsd0DkHxUNJ8CAwEAATANBgkqhkiG9w0BAQUFAAOCAQEATa2njR5Pt10+UpLo
slTPjKMoIufq5nELH7vezanNBLkgmq154WrHdCbBId3ABQ7gYqjTURzv9ZvlArxF/GFG/WsJXCC8
sQgAf4P5rNBMhb0SmvINKXMC2e6a6cpkohrBCBC2LL0YUc1lJhpdeYRphsBbwBGnPzhQkQOSxPz6
TQ7I2rmF/oOTcsbuKhsDVLXxWMp9+UE4yH/PZlhjhTc0qhb68dV43ztUegy8T2i9D/DZbGtnS4Ve
4T0SWmCkBnM+TD2bzRvP3pqiOROuOVzqZCnaAchxMOHLBheMAwah/bR+p2Csqb23aLRghS+bLYYU
bKggUg+wsYnZT8hT3kXQmA==
-----END CERTIFICATE-----", false));
	die("Yaya");

?>
<html>
<head>
	<script type="text/javascript">
		function iframeLoad() {
			var iframe = document.getElementById("frame");
			iframe.height = "";
			iframe.height = iframe.contentWindow.document.body.scrollHeight + "px";
		}
	</script>
</head>
<body>
	<iframe id="frame" src="https://www.jigsawacademy.com" onload="iframeLoad()"></iframe>
	<svg>
	</svg>
</body>
</html>