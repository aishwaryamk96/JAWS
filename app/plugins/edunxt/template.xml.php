<?xml version="1.0" encoding="utf-8"?>
<edunxt>
	<persons>
		<person role="Student">
			<login_name><?= $email; ?></login_name>
			<password><?= $password; ?></password>
			<first_name><?= $first_name ?></first_name>
			<?php if (!empty($middle_name)) { ?>
<middle_name><?= $middle_name; ?></middle_name>
			<?php }
			else { ?>
<middle_name/>
			<?php } ?>
			<last_name><?= $last_name ?></last_name>
			<date_of_birth></date_of_birth>
			<address>
				<street_address></street_address>
				<city></city>
				<state></state>
				<country></country>
			</address>
			<primary_contact_number><?= $phone ?></primary_contact_number>
			<secondary_contact_number></secondary_contact_number>
			<primary_email_id><?= $email; ?></primary_email_id>
			<secondary_email_id></secondary_email_id>
			<application_id><?= $sis_id; ?></application_id>
			<application_status>1</application_status>
			<domain_name>JigsawAcademy</domain_name>
			<lc_code>10000</lc_code>
			<roll_no><?= $email; ?></roll_no>
			<joining_drive><?= $start_date->format("Ym"); ?></joining_drive>
			<current_drive><?= $start_date->format("Ym"); ?></current_drive>
			<login_expire_in_months/>
			<slcm_person_id><?= $sis_id; ?></slcm_person_id>
			<created_ts>now</created_ts>
			<updated_ts>now</updated_ts>
			<programs>
				<program validity_date="<?= $start_date->format('Y-m-d'); ?>" updated_ts="<?= $start_date->format('Y-m-d H:i:s'); ?>" status="ADM" name="<?= $bundle_code; ?>" current_semester="01" current_drive="<?= $today->format('Ym'); ?>" created_ts="<?= $today->format('Y-m-d H:i:s'); ?>"/>
			</programs>
			<courses>
				<?php foreach ($courses as $course) { ?>
<course updated_ts="<?= $today->format('Y-m-d H:i:s'); ?>" syllabus_applicable="01" status="01" semester="1" program="<?= $bundle_code; ?>" name="<?= $course["code"]; ?>" mode="<?= $course["mode"]; ?>" lc_code="" current_drive="<?= $today->format('Ym') ?>" created_ts="<?= $today->format('Y-m-d H:i:s'); ?>" course_instance_name=""/>
				<?php } ?>
			</courses>
		</person>
	</persons>
</edunxt>