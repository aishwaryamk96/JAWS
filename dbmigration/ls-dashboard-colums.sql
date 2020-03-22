ALTER TABLE `user_leads_basic` ADD `status` SMALLINT NOT NULL DEFAULT '0' COMMENT '0 = new data, 1 - processed , 9 = old data' AFTER `user_id`;
ALTER TABLE `user_leads_basic_compiled` ADD `status` SMALLINT NOT NULL DEFAULT '1' COMMENT '1 => new, 2 = API request, 3 =success, 4 = failure , 9 = old data' AFTER `lead_id`;
