ALTER TABLE `courses_resource_links` ADD `academic_year` VARCHAR(32) NULL DEFAULT NULL AFTER `description`, ADD `academic_term` VARCHAR(32) NULL DEFAULT NULL AFTER `academic_year`;
