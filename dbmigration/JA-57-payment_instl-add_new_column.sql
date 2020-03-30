ALTER TABLE `payment_instl` ADD `instl_edited` TINYINT NOT NULL DEFAULT '0' COMMENT '0 = not edited , 1= edited' AFTER `instl_id`;
ALTER TABLE `payment_instl` ADD `updated_at` DATETIME on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `receipt`, ADD `updated_by` INT NOT NULL AFTER `updated_at`;
ALTER TABLE `payment_link` ADD `updated_at` DATETIME on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `status`, ADD `updated_by` INT NOT NULL AFTER `updated_at`;
ALTER TABLE `payment_link` CHANGE `updated_by` `updated_by` INT(11) NULL;
ALTER TABLE `payment_instl` CHANGE `updated_by` `updated_by` INT(11) NULL;
