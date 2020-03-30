ALTER TABLE `course` ADD `payment_gateway` TINYINT NULL DEFAULT '0' COMMENT '1= New Razorpay account, 0 = Old gateway options' AFTER `platform_id`;
ALTER TABLE `course_bundle` ADD `payment_gateway` TINYINT NULL DEFAULT '0' COMMENT '1= New Razorpay account, 0 = Old gateway options' AFTER `platform_id`;
ALTER TABLE `course` CHANGE `payment_gateway` `rpay_acc_flag` TINYINT(4) NULL DEFAULT '0' COMMENT '1= New Razorpay account, 0 = Old gateway options';
