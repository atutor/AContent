# set admin as an author
UPDATE `users` SET `is_author` = '1' WHERE `user_id` = '2';
# give admin access to tests and file manager
INSERT INTO `user_group_privilege` (`user_group_id` ,`privilege_id` ,`user_requirement`) VALUES ('1', '8', '0'), ('1', '9', '0');
# Updates for AContent templates

ALTER TABLE `content` ADD `layout` TEXT NOT NULL AFTER `content_type`; 
ALTER TABLE `content` ADD `optional` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `layout`; 
ALTER TABLE `content` ADD `structure` TEXT NOT NULL AFTER `optional`; 

INSERT INTO `config` (`name`, `value`) VALUES ('enable_template_structure','1');
INSERT INTO `config` (`name`, `value`) VALUES ('enable_template_layout','1');
INSERT INTO `config` (`name`, `value`) VALUES ('enable_template_page','1');

