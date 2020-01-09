<?php

	$user;

	$headers = getallheaders();
	if (isset($headers["Authorization"])) {

		if ($headers["Authorization"] != "Bearer 7F21611ED3FB647EA6101A7F5E07AFF1C330396BB53033D1B420F24F18E687E1") {

			header("HTTP/1.1 401");
			die();

		}

		load_module("user");

		$user = user_get_by_email($_POST["email"]);
		if ($user === false) {
			die(json_encode(["token" => false]));
		}
		$subs = db_query("SELECT * FROM subs WHERE status NOT IN ('inactive', 'pending', 'blocked') AND user_id = ".$user["user_id"]);
		if (empty($subs)) {
			die(json_encode(["token" => false]));
		}

		if (empty($user["photo_url"])) {
			db_exec("UPDATE user SET photo_url=".db_sanitize($_POST["photo_url"])." WHERE user_id = ".$user["user_id"].";");
		}

		if (!empty($_POST["preview"])) {
			die(json_encode(["token" => true]));
		}

		die(json_encode(["token" => /*psk_generate("user", $user["user_id"], "jlc.profile", "", "1", "days", true)*/$user["web_id"]]));

	}
	else if (isset($_GET["token"])) {

		$user = db_query("SELECT * FROM user WHERE web_id = ".db_sanitize($_GET["token"]));
		if (empty($user)) {

			$user = db_query("SELECT user.*, meta.city, meta.state, meta.country, DATE_FORMAT(meta.survey_date, '%e %b, %Y') AS survey_date FROM user INNER JOIN system_psk AS psk ON psk.entity_id = user.user_id INNER JOIN user_meta AS meta ON meta.user_id = user.user_id WHERE psk.action = 'jlc.profile' AND psk.token = ".db_sanitize($_GET["token"]).";");
			if (empty($user)) {

				header("HTTP/1.1 401");
				die();

			}

		}
		$user = $user[0];
		// psk_expire("user", $user["user_id"], "jlc.profile");
		db_query("DELETE FROM system_psk WHERE expire_date < CURRENT_TIMESTAMP;");

	}

	if (empty($user)) {

		header("HTTP/1.1 401");
		die();

	}

	if (empty($user["lms_soc"])) {

		if (!empty($user["soc_gp"])) {
			$user["lms_soc"] = "gp";
		}
		else if (!empty($user["soc_fb"])) {
			$user["lms_soc"] = "fb";
		}
		else if (!empty($user["soc_li"])) {
			$user["lms_soc"] = "li";
		}

	}

	$user_addr = [];
	if (!empty($user["city"])) {
		$user_addr[] = $user["city"];
	}
	if (!empty($user["state"])) {
		$user_addr[] = $user["state"];
	}
	if (!empty($user["country"])) {
		$user_addr[] = $user["country"];
	}
	$user_addr = implode(", ", $user_addr);

	$subs = [];
	$course_codes = [];

	$hide_dates = false;
	$hide_dates_offset = date_create_from_format("Y-m-d H:i:s", "2017-05-25 00:00:00");

	// $report_token = psk_generate("user", $user["user_id"], "jlc.profile.report", "1", "days", true);

	$res_subs = db_query(
			"SELECT
				subs.subs_id,
				subs.pay_id,
				subs.combo_free,
				IF (access.id IS NOT NULL,
					IF (MIN(DATE(access.start_date)) = CURDATE(), 'Today', DATE_FORMAT(MIN(access.start_date), '%e %b, %Y')),
					IF (DATE(subs.start_date) = CURDATE(), 'Today', DATE_FORMAT(subs.start_date, '%e %b, %Y'))
				) AS start_date,
				IF (access.id IS NOT NULL,
					MIN(access.start_date),
					subs.start_date
				) AS start_date_obj,
				IF (subs.status != 'inactive',
					IF (access.id IS NOT NULL,
						IF (MAX(DATE(access.end_date)) = CURDATE(), 'Today', DATE_FORMAT(MAX(access.end_date), '%e %b, %Y')),
						IF (subs.end_date_ext IS NOT NULL,
							IF (DATE(subs.end_date_ext) = CURDATE(), 'Today', DATE_FORMAT(subs.end_date_ext, '%e %b, %Y')),
							IF (DATE(subs.end_date) = CURDATE(), 'Today', DATE_FORMAT(subs.end_date, '%e %b, %Y'))
						)
					),
					NULL
				) AS end_date,
				IF (subs.status != 'inactive',
					IF (access.id IS NOT NULL,
						CEIL(DATEDIFF(MAX(DATE(access.end_date)), MIN(DATE(access.start_date))) / 30),
						IF (subs.end_date_ext IS NOT NULL,
							CEIL(DATEDIFF(DATE(subs.end_date_ext), DATE(subs.start_date)) / 30),
							CEIL(DATEDIFF(DATE(subs.end_date), DATE(subs.start_date)) / 30))
						),
					NULL
				) AS duration,
				IF (bundle.name IS NOT NULL, bundle.name, 'Custom Combo') AS bundle,
				pay.pay_id,
				pay.sum_total,
				pay.currency,
				pay.instl_total,
				GROUP_CONCAT(CONCAT(DATE_FORMAT(access.start_date, '%e %b, %Y'), '=', DATE_FORMAT(access.end_date, '%e %b, %Y'), '=', access.is_free) SEPARATOR '+') AS durations
			FROM
				subs
			LEFT JOIN
				subs_meta AS meta
				ON meta.subs_id = subs.subs_id
			LEFT JOIN
				course_bundle AS bundle
				ON bundle.bundle_id = meta.bundle_id
			LEFT JOIN
				payment AS pay
				ON pay.pay_id = subs.pay_id
			LEFT JOIN
				access_duration AS access
				ON access.subs_id = subs.subs_id
			WHERE
				subs.status = 'active'
				AND
				subs.user_id = ".$user["user_id"]."
			GROUP BY
				subs.subs_id
			ORDER BY
				subs.start_date DESC;"
		);

	foreach ($res_subs as $sub) {

		/*$res_access = db_query(
				"SELECT
					start_date AS start_date_obj,
					end_date AS end_date_obj,
					IF (DATE(start_date) = CURDATE(), 'Today', DATE_FORMAT(start_date, '%e %b, %Y')) AS start_date,
					IF (DATE(end_date) = CURDATE(), 'Today', DATE_FORMAT(end_date, '%e %b, %Y')) AS end_date
				FROM
					access_duration
				WHERE
					subs_id = ".$sub["subs_id"]."
				ORDER BY
					id ASC;"
			);

		if (!empty($res_access)) {

			$start_date = date_create_from_format("Y-m-d H:i:s", $res_access[0]["start_date_obj"]);
			$end_date = date_create_from_format("Y-m-d H:i:s", $res_access[count($res_access) - 1]["end_date_obj"]);

			$sub["duration"] = ceil(($end_date->diff($start_date))->days / 30);

			$sub["start_date"] = $res_access[0]["start_date"];
			$sub["end_date"] = $res_access[count($res_access) - 1]["end_date"];

		}*/

		$durations = explode("+", $sub["durations"]);
		$sub["durations"] = [];
		// ################## UNCOMMENT THIS PART WHENEVER ACCESS EXTNS HAVE TO BE ENABLED #######################################
		/*if (count($durations) > 1) {

			foreach ($durations as $duration) {
				$sub["durations"][] = explode("=", $duration);
			}

		}*/

		$start_date_obj = date_create_from_format("Y-m-d H:i:s", $sub["start_date_obj"]);
		if (!empty($start_date_obj)) {

			if ($start_date_obj < $hide_dates_offset) {
				$hide_dates = true;
			}

		}

		if (!empty($sub["pay_id"])) {

			$res_pay = db_query(
					"SELECT
						instl.sum,
						instl.currency,
						IF (DATE(instl.due_date) = CURDATE(), 'Today', DATE_FORMAT(instl.due_date, '%e %b, %Y')) AS due_date,
						IF (DATE(instl.pay_date) = CURDATE(), 'Today', DATE_FORMAT(instl.pay_date, '%e %b, %Y')) AS pay_date,
						instl.status,
						link.web_id AS pay_link
					FROM
						payment_instl AS instl
					LEFT JOIN
						payment_link AS link
						ON link.instl_id = instl.instl_id
					WHERE
						instl.pay_id = ".$sub["pay_id"]."
					ORDER BY
						instl.instl_count ASC;"
				);

			if (!empty($res_pay)) {
				$sub["pay"] = $res_pay;
			}

		}

		$res_enr = db_query(
				"SELECT
					enr.sis_id,
					enr.course_id,
					course.sis_id AS course_code,
					course.name,
					section.start_date,
					CONCAT(DATE_FORMAT(section.start_date, '%M %y'), ' - ', IF (enr.learn_mode = 'ml', 'Catalyst', IF (enr.learn_mode = 'sp', 'Regular', 'Premium'))) AS section_name,
					section.learn_mode,
					IF (lab.dir IS NOT NULL, lab.dir, enr.lab_ip) AS lab_ip,
					enr.lab_user
				FROM
					user_enrollment AS enr
				INNER JOIN
					course
					ON course.course_id = enr.course_id
				INNER JOIN
					course_section AS section
					ON section.id = enr.section_id
				LEFT JOIN
					course_lab AS lab
					ON lab.course_id = enr.course_id AND lab.status = 1
				WHERE
					enr.status = 'active'
					AND
					enr.subs_id = ".$sub["subs_id"]."
				ORDER BY
					enr.enr_id ASC;"
			);

		if (!empty($res_enr)) {

			$sub["enr"] = $res_enr;
			$user["sis_id"] = $res_enr[0]["sis_id"];

			$res_welcome_email = db_query("SELECT DATE_FORMAT(email_sent_at, '%e %b, %Y') AS email_sent_at FROM user_enr_meta WHERE subs_id = ".$sub["subs_id"].";");
			if (!empty($res_welcome_email)) {
				$sub["welcome_email_at"] = $res_welcome_email[0]["email_sent_at"];
			}

			foreach ($res_enr as $each) {
				$course_codes[] = $each["course_code"];
			}

		}

		$subs[] = $sub;

	}

	$progress = [];
	if (!empty($course_codes)) {

		$data["jig_id"] = $user["sis_id"];
		$data["course_codes"] = implode(";", $course_codes);

		$opts = array('http' => array(
				'method'  => 'POST',
				'header'  => 'Content-type: application/x-www-form-urlencoded',
				'content' => http_build_query($data)
			)
		);

		$context  = stream_context_create($opts);
		// die(file_get_contents("https://jigsawacademy.net/app/getcoursestopics.php", false, $context));
		$response = json_decode(file_get_contents("https://jigsawacademy.net/app/getcourseprogress.php", false, $context), true);
		foreach ($response["p"] as $course_code => $percentage) {

			$progress[$course_code]["p"] = floatval($percentage["p"]);
			$progress[$course_code]["c"] = (!empty($percentage["c"]) ? date_create_from_format("Y-m-d H:i:s.u", $percentage[c])->format("d M Y g:i A") : "0");

		}

	}


	function elipsiseText($text, $length) {

		if (strlen($text) > $length) {
			return substr($text, 0, $length)."...";
		}

		return $text;

	}

	function currenciseNumber($number, $rupee = true) {

		$number = strrev($number);
		if ($rupee) {

			$number = substr($number, 0, 3).",".substr($number, 3);
			$index = 4;
			$len = 2;

		}
		else {

			$index = 0;
			$len = 3;

		}
		while (strlen(substr($number, $index)) > $len) {

			$number = substr($number, 0, $index + $len).",".substr($number, $index + $len);
			$index += $len + 1;

		}

		return strrev($number);

	}

?>
<html>
<head>
	<script src="https://use.fontawesome.com/fc49ce4973.js"></script>
	<link rel="stylesheet" href="<?php echo JAWS_PATH_WEB ?>/app/apis/lmsapi/profile.css?ver=<?= time() ?>">
	<script>
		function showReport() {
			document.getElementById("modalOverlay").classList.remove("hidden");
		}
		function showHelp() {
			document.getElementById("modalOverlayHelp").classList.remove("hidden");
		}
		function modalClick() {
			var t = window.event.target.id;
			if (t == "btn-close" || t == "btn-cancel" || t == "modalOverlay") {
				document.getElementById("modalOverlay").classList.add("hidden");
			}
		}
		function modalClickHelp() {
			var t = window.event.target.id;
			if (t == "modal-close" || t == "modalOverlayHelp") {
				document.getElementById("modalOverlayHelp").classList.add("hidden");
			}
		}
		function report() {
			window.event.preventDefault();
			x = new XMLHttpRequest();
			var p = function(u, h, d, c) {
				x.onreadystatechange = function() {
					if (this.readyState < 4) {
						return;
					}
					c({status: this.status, data: this.responseText});
				}
				x.open("POST", u, true);
				for (var k in h) {
					x.setRequestHeader(k, h[k]);
				}
				var da = [];
				for (var k in d) {
					da.push(k+"="+d[k]);
				}
				x.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				x.send(da.join("&"));
			}
			var token = document.getElementById("report-token").value;
			var issueId = document.getElementById("reportCategory").value;
			var issueDesc = document.getElementById("reportDesc").value;
			if (issueId == -1) {
				alert("Please select an issue to continue...");
				return;
			}
			else if (issueId == 0 && issueDesc == "") {
				alert("Please describe your issue to continue...");
				return;
			}
			p("https://www.jigsawacademy.com/jaws/lmsapi/report", {}, {t: token, i: issueId, d: encodeURIComponent(issueDesc)}, function(r) {
				if (r.status == 200) {
					alert(r.data);
				}
				else {
					alert("Something went wrong...");
				}
				document.getElementById("modalOverlay").classList.add("hidden");
			});
		}
	</script>
</head>
<body>
	<div class="profile">
		<div class="profile-container">
			<div class="left-pane">
				<div class="profile-pic">
					<img src="<?php echo (!empty($user["photo_url"]) ? $user["photo_url"] : ""); ?>" width="200" height="200">
				</div>
				<?php if (!empty($user_addr)) { ?>
					<div class="location regular-font" title="<?php echo $user_addr ?>">
						<center>
							<i class="fa fa-map-marker addr-icon" aria-hidden="true"></i> <span><?php echo elipsiseText($user_addr, 27) ?></span>
						</center>
					</div>
				<?php }
				if (!empty($user["survey_date"])) { ?>
					<div class="survey-date regular-font" title="Access setup on <?php echo $user["survey_date"] ?>">
						<center>
							<i class="fa fa-universal-access" aria-hidden="true"></i> <?php echo $user["survey_date"] ?>
						</center>
					</div>
				<?php } ?>
			</div>
			<div class="main-pane">
				<div class="primary-info">
					<div class="first-half">
						<span id="user-name" class="regular-font"><?php echo $user["name"] ?></span>
						<?php if (!empty($user["sis_id"])) { ?>
							<span id="jig-id" class="regular-font"><?php echo $user["sis_id"] ?></span>
						<?php } ?>
					</div>
					<div class="second-half regular-font font-90pc">
						<span class="report">
							<label class="label-link" onclick="showHelp()">How to read this page?</label>
						</span>
					</div>
				</div>
				<div class="comm">
					<div class="first-half">
						<span class="comm-info comm-info-phone regular-font" title="Phone Number">
							<i class="fa fa-phone" aria-hidden="true"></i>
							<span id="comm-phone" class="comm-info-text"><?php echo (!empty($user["phone"]) ? $user["phone"] : "N/A") ?></span>
						</span>
						<span class="comm-info comm-info-email regular-font" title="Comunication Email ID">
							<i class="fa fa-envelope" aria-hidden="true"></i>
							<span id="comm-email" class="comm-info-text"><?php echo $user["email"] ?></span>
						</span>
					</div>
					<div class="second-half regular-font font-90pc">
						<span class="report no-padding">
							<label>Find some discrepancy?</label>
							<!-- <label class="label-link left-padding" onclick="showReport()">Report an issue</label> -->
							<label class="label-link left-padding">Raise a ticket</label>
						</span>
					</div>
				</div>
				<div class="divider"></div>
				<?php if (!empty($user["lms_soc"]) && $user["lms_soc"] != "corp") { ?>
					<div class="socials">
						<div class="<?php echo $user["lms_soc"] ?> regular-font social-email" title="JLC Social Login">
							<?php if ($user["lms_soc"] == "fb") { ?>
								<span class="fb-icon-container"><i class="fa fa-facebook-official fb-icon" aria-hidden="true"></i></span>
							<?php }
							else if ($user["lms_soc"] == "gp") { ?>
								<span class="gp-icon-container"><i class="fa fa-google-plus gp-icon" aria-hidden="true"></i></span>
							<?php }
							else { ?>
								<span class="li-icon-container"><i class="fa fa-linkedin-square li-icon" aria-hidden="true"></i></span>
								<?php } ?>
							<span id="<?php echo $user["lms_soc"] ?>-email"><?php echo $user["soc_".$user["lms_soc"]] ?></span>
							<span class="jlc-login"><img src="https://www.jigsawacademy.com/jaws/media/jaws/frontend/images/favicon.png" height="16" width="16"></span>
						</div>
					</div>
					<div class="divider"></div>
				<?php } ?>
				<div class="subs-container regular-font">
					<?php if (!empty($subs)) {
						foreach ($subs as $each_subs) { ?>
							<table class="subs-each" cellspacing="0">
								<thead>
									<tr>
										<th class="subs-bundle" title="<?php echo $each_subs["bundle"] ?>"><?php echo elipsiseText($each_subs["bundle"], 50) ?></th>
										<th class="subs-duration">
											<?php if (!$hide_dates) { ?>
												<span class="subs-duration-text">
													<?php echo $each_subs["start_date"]." to ".$each_subs["end_date"] ?><!-- <br> -->
													<span class="subs-duration-len"> (<?php echo $each_subs["duration"] ?> months)</span>
													<?php if (!empty($each_subs["durations"])) { ?>
														<i class="fa fa-info-circle durations-dropdown"></i>
														<div id="sd-<?= $each_subs["subs_id"] ?>" class="regular-font subs-durations">
															<ul>
																<li style="font-weight: bold">Access Extensions</li>
																<?php foreach ($each_subs["durations"] as $duration) { ?>
																	<li>
																		<span><?= $duration[0] ?> to <?= $duration[1] ?></span>
																		<?php if ($duration[2] == 0) { ?>
																			<span style="margin-left: 5px; color: green;"><i class="fa fa-money" title="Paid extension"></i></span>
																		<?php } ?>
																	</li>
																<?php } ?>
															</ul>
														</div>
													<?php } ?>
												</span>
											<?php } ?>
										</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="subs-enrs" colspan="2">
											<table class="subs-enr-each" cellspacing="0">
												<?php if (!empty($each_subs["enr"])) {

													foreach ($each_subs["enr"] as $enr) { ?>
														<tr>
															<td class="enr-course-name text-capitalize">
																<?php echo strtolower($enr["name"]);
																if (in_array($enr["course_id"], $each_subs["combo_free"])) { ?><span class="complimentary"><br>(Complimentary)</span><?php } ?>
															</td>
															<?php
															$notStarted = "";
															$progressStr = "";
															if (!empty($progress[$enr["course_code"]])) {
																if ($progress[$enr["course_code"]]["p"] == 0) {
																	$progressStr = "Not started";
																	$notStarted = "red";
																}
																else {
																	$progressStr = $progress[$enr["course_code"]]["p"]." %";
																}
															}
															else {
																$progressStr = "N/A";
															} ?>
															<td class="enr-section <?= $notStarted ?>">
																<?= $progressStr ?>
																<?php if (!empty($progress[$enr["course_code"]]["c"])) { ?>
																	<span class="enr-certificate">
																		<i class="fa fa-certificate" title="Generated on <?= $progress[$enr["course_code"]]["c"] ?>"></i>
																	</span>
																<?php } ?>
															</td>
															<td class="lab-details">
																<?php if (!empty($enr["lab_ip"])) { ?>
																	<span>
																		<span title="Click to copy" onclick="textCopy(enr.lab_ip)" style="cursor: pointer"><?php echo $enr["lab_ip"] ?></span><br><span class="lab-login"><?php echo $enr["lab_user"] ?></span>
																	</span>
																<?php } ?>
															</td>
														</tr>
													<?php }
													// Foreach end
												} ?>
											</table>
										</td>
									</tr>
									<?php if (!empty($each_subs["pay"])) { ?>
										<tr>
											<td class="subs-pay" colspan="2">
												<table class="subs-pay-tbl" cellspacing="0">
													<thead>
														<tr>
															<th class="pay-info-hdr">
																<span>Total Amount Payable</span>
																<?php echo ($each_subs["currency"] =='inr' ? "&#8377;" : "$"); echo currenciseNumber($each_subs["sum_total"], $each_subs["currency"] =='inr' ? true : false); ?>
															</th>
														</tr>
													</thead>
													<tbody style="color: #999">
														<tr>
															<td class="instl-hdr" style="font-weight: bold">Your Payment Details:</td>
														</tr>
														<tr>
															<td class="instl-container">
																<?php $i = 1; $due = false;
																foreach ($each_subs["pay"] as $instl) { ?>
																	<table class="pay-instl-each" cellspacing="0">
																		<tr class="<?php echo ((empty($instl["pay_date"]) && $instl["status"] != "paid") ? "instl-due" : "") ?>">
																			<td class="instl-idx"><?php echo $i++; ?>.</td>
																			<td><?php echo ($instl["currency"] == "inr" ? "&#8377;" : "$"); echo currenciseNumber($instl["sum"], $instl["currency"] =='inr' ? true : false); ?></td>
																			<td class="instl-pay-date">
																				<?php if (empty($instl["pay_date"]) && $instl["status"] != "paid") { ?>
																					<?php if (!empty($instl["due_date"])) { ?>
																						<span title="Due on <?php echo $instl["due_date"] ?>"><i class="fa fa-calendar" aria-hidden="true"></i> <?php echo $instl["due_date"] ?></span>
																						<?php if (!$due) { ?>
																							<a class="pay-link" title="Pay now" href="<?php echo JAWS_PATH_WEB."/pay?pay=".$instl["pay_link"] ?>">(Pay now <i class="fa fa-external-link" aria-hidden="true"></i>)</a>
																						<?php $due = true;
																						}
																					}
																				}
																				else { ?>
																					<span title="Paid on <?php $instl["pay_date"] ?>"><i class="fa fa-money" aria-hidden="true"></i> <?php echo $instl["pay_date"] ?></span>
																				<?php } ?>
																			</td>
																		</tr>
																	</table>
																<?php } ?>
															</td>
														</tr>
													</tbody>
												</table>
											</td>
										</tr>
									<?php }
									if (!empty($each_subs["welcome_email_at"])) { ?>
										<tr>
											<td colspan="2" class="welcome-email-info"><span class="welcome-email-title">Welcome email:</span> <?php echo $each_subs["welcome_email_at"] ?></td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						<?php }
						// Foreach complete
					} ?>
				</div>
			</div>
		</div>
	</div>
	<div id="modalOverlay" class="modal-overlay hidden" onclick="modalClick()">
		<div class="modal regular-font">
			<div class="dialog-header">
				<span class="dialog-title">Report an Issue</span>
				<i id="btn-close" class="fa fa-times btn-close" aria-hidden="true"></i>
			</div>
			<div class="dialog-body">
				<label class="section-header-first">Category</label>
				<div class="first-container layout-row">
					<select id="reportCategory" style="width: 100%">
						<option value="-1">Select</option>
						<option value="1">Enrollment information not available</option>
						<option value="2">Enrolment information incomplete</option>
						<option value="4">Communication information incorrect</option>
						<option value="5">Login information incorrect</option>
						<option value="6">Payment information missing</option>
						<option value="7">Payment information incorrect</option>
						<option value="8">Lab login information missing</option>
						<option value="9">Lab login information incorrect</option>
						<option value="0">Other issue</option>
					</select>
				</div>
				<label class="section-header-next">Description</label>
				<div class="next-container">
					<textarea placeholder="Please describe the problem here..." id="reportDesc" style="max-width: 100%; min-width: 100%;"></textarea>
				</div>
				<div class="next-container" style="flex-direction: row-reverse; font-size: 85%; margin-bottom: 0px;">
					<span class="italics">Allow us 7 working days to resolve your issue.</span>
				</div>
			</div>
			<div class="dialog-ctrl">
				<input type="hidden" name="subs_id" id="report-token" value="<?= $user["web_id"] ?>">
				<span id="btn-cancel" class="dialog-ctrl-btn btn-cancel">Cancel</span>
				<span id="btn-save" class="dialog-ctrl-btn btn-ok enabled" onclick="report()">Save</span>
			</div>
		</div>
	</div>
	<div id="modalOverlayHelp" class="modal-overlay hidden" onclick="modalClickHelp()">
		<div class="modal modal-help regular-font">
			<div class="dialog-header">
				<span class="dialog-title">Visual guide</span>
				<i id="modal-close" class="fa fa-times btn-close" aria-hidden="true"></i>
			</div>
			<img id="img-help" src="https://www.jigsawacademy.com/jaws/media/jaws/frontend/images/help_w<?= $hide_dates ? "o" : "" ?>_dates.png" width="1000" height="448">
		</div>
	</div>
</body>
</html>