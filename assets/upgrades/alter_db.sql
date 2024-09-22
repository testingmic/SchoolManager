ALTER TABLE `clients_packages` CHANGE `payment_module` `payment_module` ENUM('0','not_activated','activated') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'activated';
ALTER TABLE `users` ADD `scholarship_status` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `user_status`;
ALTER TABLE `clients_packages` ADD `status` CHAR(12) NOT NULL DEFAULT 'active' AFTER `pricing`;