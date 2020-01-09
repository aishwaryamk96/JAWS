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

    JIGSAW ACADEMY WORKFLOW SYSTEM v2
    ---------------------------------
*/
	//activity_debug_start();
	//activity_debug_log(json_encode($_REQUEST)."-");

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header('Location: https://www.jigsawacademy.com');
		die();
	}

	// Check Auth
	if (!auth_api("subs.create")) die("You do not have the required priviledges to use this feature.");

	// Basic Check
	if (!isset($_POST["name"]) || !isset($_POST["email"]) || !isset($_POST["phone"]) || !isset($_POST["city"])) {
		activity_create("critical","uc.paylink.create","fail","","","","","Missing Basic Field in API.");
		die(json_encode(['error'=>'Missing Basic Fields']));
	}

	// Extra Check
	/* if (!isset($_POST["file_cover"]) || !isset($_POST["degree_1_type"]) || !isset($_POST["degree_1_instt"]) || !isset($_POST["degree_1_gpa"]) || !isset($_POST["degree_1_year"]) || !isset($_POST["file_degree_1_marksheet"]) || !isset($_POST["file_work_cv"]) || !isset($_POST["address"]) || !isset($_POST["dob"])) {
		activity_create("critical","uc.paylink.create","fail","","","","","Missing Extra Field in API.");
		die(json_encode(['error'=>'Missing Extra Fields']));
	} */
	if ( !isset($_POST["file_work_cv"]) ) {
		activity_create("critical","uc.paylink.create","fail","","","","","Missing Extra Field in API.");
		die(json_encode(['error'=>'Missing Extra Fields']));
	}

	// Prep Basic
	$data['name'] = $_POST['name'];
	$data['email'] = $_POST['email'];
	$data['phone'] = $_POST['phone'];
	$data['dob'] = $_POST['dob'] ?? 'NA';
	$data['city'] = $_POST['city'];
	$data['address'] = $_POST['address'] ?? 'NA';

	$data['degree_1_type'] = $_POST['degree_1_type'] ?? 'NA';
	$data['degree_1_instt'] = $_POST['degree_1_instt'] ?? 'NA';
	$data['degree_1_gpa'] = $_POST['degree_1_gpa'] ?? 'NA';
	$data['degree_1_year'] = $_POST['degree_1_year'] ?? 'NA';
	$data['file_degree_1_marksheet'] = $_POST['file_degree_1_marksheet'] ?? 'NA';
	$data['degree_2_type'] = $_POST['degree_2_type'] ?? 'Not Specified';
	$data['file_degree_2_marksheet'] = $_POST['file_degree_2_marksheet'] ?? 'Not Specified';
	$data['exam'] = $_POST['exam'] ?? 'Not Specified';
	$data['exam_score'] = $_POST['exam_score'] ?? 'Not Specified';

	$data['work_org'] = $_POST['work_org'] ?? 'Not Specified';
	$data['work_designation'] = $_POST['work_designation'] ?? 'Not Specified';
	$data['work_ctc'] = $_POST['work_ctc'] ?? 'Not Specified';
	$data['file_work_ctc'] = $_POST['file_work_ctc'] ?? 'Not Specified';
	$data['work_years'] = $_POST['work_years'] ?? 'Not Specified';
	$data['file_work_years'] = $_POST['file_work_years'] ?? 'Not Specified';

	$data['file_cover'] = $_POST['file_cover'] ?? 'NA';
	$data['file_work_cv'] = $_POST['file_work_cv'] ?? 'NA';
	$data['file_recommendation'] = $_POST['file_recommendation'] ?? 'None';

	$data['scholarship'] = $_POST['scholarship'] ?? 'No';
	$data['fam_income'] = $_POST['fam_income'] ?? 'Not Specified';

	// Payment
	$pay_mode = "INR";
	$pay_value = 499;
	if (!empty($_POST["pay_mode"])) {
		if (strtoupper($_POST["pay_mode"]) == "USD") {
			$pay_mode = "USD";
			$pay_value = 8;
		}
	}
	$data['sum'] = $pay_value;
	$data['paymode'] = $pay_mode;

	// Create Activity Record for Payment
	$act_id = activity_create('high','uc.paylink','use','','','','',json_encode($data),'pending');
	echo json_encode(["link" => 'https://www.jigsawacademy.com/jaws/uc/pay?pay='.$act_id]);


	// Create Leads record
	/*db_exec("INSERT INTO user_leads_basic (
		user_id,
		name,
		email,
		phone,
		referer,
		ip,
		ad_lp,
		ad_url,
		create_date,
		capture_trigger,
		capture_type
	) VALUES (".
		($_POST["user_id"] ?? 'NULL').", ".
		db_sanitize($_POST['name']).", ".
		db_sanitize($_POST['email']).", ".
		db_sanitize($_POST['phone']).", ".
		db_sanitize($_SERVER['HTTP_REFERER']).", ".
		db_sanitize($_SERVER['REMOTE_ADDR']).", ".
		"'www.jigsawacademy.com', ".
		db_sanitize($_SERVER['REQUEST_URI']).", ".
		db_sanitize(strval(date("Y-m-d H:i:s"))).", ".
		"'ws-uc-gateway'".", ".
		"'cookie'".
	");");*/

	// Done
	exit();

?>
