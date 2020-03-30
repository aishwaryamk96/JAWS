ALTER TABLE `course` ADD `rpay_acc_flag` TINYINT NULL DEFAULT '0' COMMENT '1= New Razorpay account, 0 = Old gateway options' AFTER `platform_id`;
ALTER TABLE `course_bundle` ADD `rpay_acc_flag` TINYINT NULL DEFAULT '0' COMMENT '1= New Razorpay account, 0 = Old gateway options' AFTER `platform_id`;
