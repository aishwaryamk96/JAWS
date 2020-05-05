ALTER TABLE `course` ADD `seller` TINYINT NOT NULL DEFAULT '1' COMMENT '0 = Common courses , 1 = Jigsaw, 2= Manipal , 3 = MINDSchool' AFTER `name`;
ALTER TABLE `course_bundle` ADD `seller` TINYINT NOT NULL DEFAULT '1' COMMENT '0 = Common courses , 1 = Jigsaw, 2= Manipal , 3 = MINDSchool' AFTER `bundle_type`;
