ALTER TABLE `package` ADD `individual_course` VARCHAR(512) NULL DEFAULT NULL AFTER `combo_free`;
ALTER TABLE `subs` ADD `individual_course` VARCHAR(512) NULL DEFAULT NULL AFTER `combo_free`;
