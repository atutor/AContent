// set admin as an author
UPDATE `users` SET `is_author` = '1' WHERE `user_id` = '2;
// give admin access to tests and file manager
INSERT INTO `user_group_privilege` (`user_group_id` ,`privilege_id` ,`user_requirement`) VALUES ('1', '8', '0'), ('1', '9', '0');
