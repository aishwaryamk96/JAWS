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

	 // Prevent exclusive access
	if (!defined("JAWS")) {
		header('Location: http://www.jigsawacademy.com');
		die();
	}

	load_module("ui");
	load_module("user");
	load_module("subs");
	load_module("refer");

	// Init Session
	auth_session_init();

	//Prep
	$return_url = JAWS_PATH_WEB."/referralslist";

	//Login Check
	if (!auth_session_is_logged()) {
		ui_render_login_front(array(
			"mode" => "login",
			"return_url" => $return_url,
			"text" => "Please login to access this page."
			));
		exit();
	}

	if (!auth_session_is_allowed("jlc.referral")) {
		ui_render_msg_front(array(
			"type" => "error",
			"title" => "Jigsaw Academy",
			"header" => "No Tresspassing",
			"text" => "Sorry, but you do not have permission to access this page.<br/>Contact Jigsaw Support team for assistance."
			));
		exit();
	}


	//echo "SEND NOTIFICATION CODE LEFT"; die;
	function camel_case($string) {
		return ucwords(str_replace("_", " ", $string));
	}

	$total_enrolled = 0;
	$reg = 0;
	$enrolled = 0;
	$awaiting_voucher = 0;
	$voucher_awarded = 0;
	$can_claim = 0;
	$invite_expired = 0;

	$export_list = "Referrer Email,Referrer Name,Email,Name,Phone,Courses\r\n";

	$courses = [];
	$res_courses = db_query("SELECT course_id, name FROM course;");
	foreach ($res_courses as $course) {
		$courses[$course["course_id"]] = $course["name"];
	}

	$bundles = [];
	$res_bundles = db_query("SELECT bundle_id, name FROM course_bundle;");
	foreach ($res_bundles as $bundle) {
		$bundles[$bundle["bundle_id"]] = $bundle["name"];
	}

	/** refer data **/
	$ref_array = array();
	$refer = db_query("
			SELECT
				refer.*,
				referred.user_id AS referred_id,
				referrer.email AS referrer_email, referrer.name AS referrer_name,
				act.content AS sys_content,
				subs.start_date AS enrolled_date,
				user_meta.reg_date AS registered_date
			FROM
				refer
			LEFT JOIN
				user AS referrer ON referrer.user_id = refer.referrer_id AND refer.referrer_type = 'user'
			LEFT JOIN
				user AS referred ON referred.email = refer.email
			LEFT JOIN
				system_activity AS act ON act.act_id = refer.referrer_id AND refer.referrer_type = 'system_activity'
			LEFT JOIN
				user_meta ON user_meta.user_id = refer.referral_id AND refer.referral_id IS NOT NULL
			LEFT JOIN
				subs ON subs.user_id = refer.referral_id AND refer.referral_id IS NOT NULL AND (subs.status = 'active' OR subs.status = 'pending')
			ORDER BY
			create_date DESC;
		");

	$i = 0;
	$status_dates = '';
	foreach ($refer as $ref) {

		$ref['status_dates'] = $status_dates;  //dates displayed with status in last column
		if ($ref['status'] == 'no_action') {

			$no_action++;
			$ref["color"] = "#F0F0F0";
			if ($ref["referrer_type"] == "system_activity") {

				$content = json_decode($ref["sys_content"]);
				$ref["referrer_email"] = $content->email;
				$ref["referrer_name"] = $content->name;

			}

			$export_list .= $ref["referrer_email"].",".$ref["referrer_name"].",".$ref["email"].",".$ref["name"].",";
			if (!empty($ref["courses"])) {

				$ref_courses = explode(";", $ref["courses"]);
				$courses_str = [];
				foreach ($ref_courses as $course) {
					$courses_str[] = $courses[$course];
				}
				$export_list .= implode(" + ", $courses_str).",";

			}
			if (!empty($ref["course_bundles"])) {

				$ref_bundles = explode(";", $ref["course_bundles"]);
				$bundles_str = [];
				foreach ($ref_bundles as $bundle) {
					$bundles_str[] = $bundles[$bundle];
				}
				$export_list .= implode(" + ", $bundles_str).",";

			}
			$export_list .= "\r\n";

			if (!empty($ref["referred_id"])) {

				$ref["status"] = "registered";
				$ref["referral_id"] = $ref["referred_id"];

			}
			else {

				$create_date = date_create_from_format("Y-m-d H:i:s", $ref["create_date"]);
				if (date_diff(new DateTime, $create_date, true) > 30) {
					$ref["status"] = "invite_expired";
				}

			}

		}
		if ($ref['status'] == 'registered') {

			$registered++;
			$ref["color"] = "#FAA257";
			if ($ref['registered_date']) {
				$ref['status_dates'] = "(".date('F d Y', strtotime($ref['registered_date'])).")";
			}
			if (!empty($ref["start_date"])) {
				$ref["status"] = "enrolled";
			}

		}
		if ($ref['status'] == 'enrolled') {

			$total_enrolled++;
			$enrolled++;
			$ref["color"] = "#9CFF88";
			$ref['status'] = "In Fulfillment Period";
			$ref['status_dates'] = "(".date('F d Y', strtotime($ref['enrolled_date'])).")";
			$pay_referred = db_query("SELECT status FROM payment_instl WHERE user_id = ".$ref["referral_id"]);
			$paid = true;
			foreach ($pay_referred as $pay) {

				if ($pay["status"] != "paid") {
					$paid = false;
				}

			}
			if ($ref["referrer_type"] == "user") {

				$pay_referrer = db_query("SELECT status FROM payment_instl WHERE user_id = ".$ref["referrer_id"]);
				foreach ($pay_referrer as $pay) {

					if ($pay["status"] != "paid") {
						$paid = false;
					}

				}

			}
			if ($paid) {
				$ref["status"] = "claim_reward";
			}

		}
		if ($ref['status'] == 'claim_reward') {

			$can_claim++;
			$total_enrolled++;
			$ref["color"] = "#49FFFF";

		}
		if ($ref['status'] == 'awaiting_approval') {

			$awaiting_voucher++;
			$total_enrolled++;
			$ref["color"] = "#FFFF00";
			if ($ref['claim_date']!='') {
				$ref['status_dates'] = "(".date('F d Y', strtotime($ref['claim_date'])).")";
			}

		}
		if ($ref['status'] == 'voucher_awarded') {

			$voucher_awarded++;
			$total_enrolled++;
			$ref["color"] = "#75DB1B";
			$ref['status_dates'] = "(".date('F d Y', strtotime($ref['voucher_awarded_date'])).")";

		}
		if ($ref['status'] == 'invite_expired') {

			$invite_expired++;
			$ref["color"] = "#ececec";

		}

		if ($ref["referrer_type"] == "user") {
			$user = user_get_by_id($ref["referrer_id"]); //print_r($user);  //die;
		}
		else {

			$res_act = db_query("SELECT * FROM system_activity WHERE act_id=".$ref["referrer_id"]);
			$user = json_decode($res_act[0]["content"], true);
			$user["user_id"] = $res_act[0]["act_id"];
			$user_src = "system_activity";

		}

		$ref_array[$i]['referrer_id']  = $user['user_id'];
		$ref_array[$i]['referrer_name'] = $user['name'];
		$ref_array[$i]['referrer_email'] = $user['email'];
		$ref_array[$i]['encoded_email'] = urlencode($user['email']);
		$ref_array[$i]['name'] = $ref['name'];
		$ref_array[$i]['email'] = $ref['email'];
		if ($ref['status'] != 'no_action' && $ref['status'] != 'invite_expired') {
			$ref_array[$i]['encoded_e'] = urlencode($ref['email']);
		}
		$ref_array[$i]['phone'] = $ref['phone'];
		$ref_array[$i]['date'] = date('F d Y', strtotime($ref['create_date']));
		$ref_array[$i]['status'] = camel_case($ref['status']);
		$ref_array[$i]['color'] = $ref['color'];
		$ref_array[$i]['status_dates'] = $ref['status_dates'];
		$ref_array[$i]['enrolled_date'] = ($ref['enrolled_date'] !='') ? date('F d Y', strtotime($ref['enrolled_date'])) : 'NA';
		$ref_array[$i]['claim_date'] = ($ref['claim_date'] !='') ? date('F d Y', strtotime($ref['claim_date'])) : 'NA';
		$ref_array[$i]['registered_date'] = ($ref['registered_date'] !='') ? date('F d Y', strtotime($ref['registered_date'])) : 'NA';
		$ref_array[$i]['voucher_awarded_date'] = ($ref['voucher_awarded_date'] !='') ? date('F d Y', strtotime($ref['voucher_awarded_date'])) : 'NA';
		$i++;

	}

	/** to export the data **/
	if (isset($_REQUEST["export"])) {

		$filename = "external/temp/no_action_referrals.csv";
		$file = fopen($filename, "w");
		fwrite($file, $export_list);
		fclose($file);

		header('Pragma: public');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control: private', false);
		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename="'. basename($filename) . '"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . filesize($filename));
		readfile($filename);
		exit();

	}

?>
<html ng-app="referApp">
<head>
    <title><?php echo substr($domain_name, 0, -3) ?>Referral List</title>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.17/angular.min.js"></script>

    <style>
    table td {
      background-color: rgba(0, 0, 0, 0.075);
      text-align:center;
      font-size: 13px;
    }
    table th {
      background-color: #63beeb;
      text-align:center;
      font-size: 13px;
      color:#fff;
    }
    table th a{
      color:#fff;
      text-decoration: none;
      font-size: 13px;
    }
    td.sno a{
      color:#000;
      font-size: 13px;
    }
    a {
      font-size: 75%;
    }

    .header {
      position: fixed;
      width: 100%;
      background: white;
      top: 0px;
      padding-top: 10px;
    }
    tr.expanded-class td{ background: #fff; text-align: left; cursor: pointer;}
    input{
          height: 30px;
          border: 1px solid #909090;
         /* border-radius: 3px;*/
          padding:3px;
    }
    form {
      margin-bottom: 0px;
    }
  </style>

  </head>
  <!-- Controller name goes here -->
  <body ng-controller="referController" style="font-family: sans-serif; font-size: 90%;">
    <div class="header">
      <div>
        <div style="display:inline-block; width:35%; vertical-align:middle;"><img src="<?php echo $_SERVER["server_name"]."/".JAWS_PATH_LOCAL.'/media/jaws/backend/jigsaw_horizontal_logo.png'; ?>">
        </div>
        <div  style="display:inline-block; width:35%; vertical-align:middle;">
          <span  style="font-weight:bold;font-size:16px;">Search: </span>
          <span >
          <input style="width:80%;" type="text" placeholder="Type your search text here..." ng-model="searchKeyword"/>
          </span>
        </div>

        <div style="display:inline-block;vertical-align:middle;width: 27%;text-align: right;"> <b style="font-size:18px;"><?php echo $_SESSION["user"]["name"]; ?></b> <br/>
        <a href="<?php echo JAWS_PATH_WEB."/logout"; ?>">Logout</a>
                <?php if (isset($msg)) echo "<br/>".$msg; ?></div>
      </div>
      <hr/>
          <center>

            <!-- <hr> -->
            <table border="0" cellpadding="10" cellspacing="2" style="font-size: 95%;">
          <thead>
            <tr>
		<th>Total Enrolled / Total Referred</TH>
		<th>In Fulfillment Period</th>
		<th>Awaiting Voucher</th>
		<th>Voucher Awarded</th>
		<th>Can Claim</th>
		<th>No Action</th>
		<th>Registered</th>
		<th>Invite Expired</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><b style="background: #1c9624;border-radius: 14px;padding: 7px;color: #fff;"><?php echo $total_enrolled ."</b>&nbsp;<b>/&nbsp;".$i ?></b></td>

              <td style="background:#9CFF88;"><?php echo $enrolled ?></td>
              <td style="background:#FFFF00;"><?php echo $awaiting_voucher ?></td>
              <td style="background:#75DB1B;"><?php echo $voucher_awarded ?></td>
              <td style="background:#49ffff;"><?php echo $can_claim ?></td>
              <td>
                <form method="POST" action="<?php echo JAWS_PATH_WEB ?>/referralslist">
                  <input style="cursor: pointer;" type="submit" name="export" value="<?php echo $no_action ?>" />
                </form>
              </td>
              <td style="background:#FAA257;"><?php echo $registered ?></td>
              <td><?php echo $invite_expired ?></td>
            </tr>
          </tbody>
        </table>
        <hr>
        <!-- <div style="padding:20px 0px 0px 0px;">
          <span  style="font-weight:bold">Search: </span>
          <span >
          <input type="text" placeholder="Type your search text here..." ng-model="searchKeyword"/>
          </span>
        </div> -->
      </center>
    </div>
    <center>
    <div style=" margin-top:15%;">
      <!-- <div style="text-align:left; font-weight:bold;width:90%;">NOTE: Click on the table header to sort</div> -->
      <table border="0" cellpadding="10" cellspacing="2" >
        <thead>
          <tr>
            <th>#</th>
            <th><a href="" ng-click="orderByField='referrer_name'; reverseSort = !reverseSort">Referrer Name</a></th>
            <th><a href="" ng-click="orderByField='referrer_email'; reverseSort = !reverseSort">Referrer Email</a></th>
            <th><a href="" ng-click="orderByField='name'; reverseSort = !reverseSort">Referral Name</a></th>
            <th><a href="" ng-click="orderByField='email'; reverseSort = !reverseSort">Referral Email</a></th>
            <th><a href="" ng-click="orderByField='phone'; reverseSort = !reverseSort">Referral Phone</a></th>
            <th><a href="" ng-click="orderByField='date'; reverseSort = !reverseSort">Referral Date</a></th>
            <th><a href="" ng-click="orderByField='status'; reverseSort = !reverseSort">Status</a></th>
          </tr>
        </thead>
        	<tbody  ng-repeat="referral in referrals  | filter: searchKeyword | orderBy:orderByField:reverseSort">
          <tr ng-click="collapsed=!collapsed">
            <td>{{$index + 1}}</td>
            <td class="sno"><a href="<?php echo JAWS_PATH_WEB ?>/search?search_text={{referral.encoded_email}}&criterion=email" target="_blank">{{referral.referrer_name}}</a>
            </td>

            <td>{{referral.referrer_email}}</td>

            <td >{{referral.name}}
              <br/><a ng-if="referral.encoded_e" href="<?php echo JAWS_PATH_WEB ?>/search?criterion=email&search_text={{referral.encoded_e}}" target="_blank">Search</a>
            </td>

            <td>{{referral.email}}</td>
            <td>{{referral.phone}}</td>
            <td>{{referral.date}}</td>
            <td style="background:{{referral.color}}">{{referral.status}} &nbsp;{{referral.status_dates}}
            <br/><a style="cursor:pointer; color:#009cd9;" ng-if="referral.status=='Awaiting Approval' " confirmed-click="voucher_notification(referral.name,referral.email,referral.referrer_name,referral.referrer_email)" ng-confirm-click="Are you sure you want to send notification for voucher awarded?" >Send Notification</a>
            </td>
          </tr>
          <tr ng-show="collapsed" class="expanded-class">
          	<td colspan="2" >
	          	<b>Registered: </b>{{referral.registered_date}}
	         </td>
	         <td colspan="2">
	          	<b>Enrolled: </b>{{referral.enrolled_date}}
	         </td>
	         <td colspan="2">
	          	<b>Claimed: </b>{{referral.claim_date}}
	         </td>
	         <td colspan="2">
	          	<b>Voucher Awarded: </b>{{referral.voucher_awarded_date}}
          	</td>
      	</tr>
      </table>
    </div>
    </center>
    <script type="text/javascript">var referrer =<?php echo json_encode($ref_array); ?>;</script>

    <script src="common/refer/controller.js"></script>
  </body>
</html>