ALTER TABLE `user_leads_basic_compiled` ADD `basic_lead_id` INT(11) NULL DEFAULT NULL AFTER `lead_id`;
ALTER TABLE `user_leads_basic_compiled` ADD `ls_request` TEXT NULL DEFAULT NULL AFTER `__tr`, ADD `ls_response` TEXT NULL DEFAULT NULL AFTER `ls_request`;
