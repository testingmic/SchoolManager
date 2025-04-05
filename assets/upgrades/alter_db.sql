ALTER TABLE `grading_terminal_scores` ADD `distinct_record` VARCHAR(32) NULL DEFAULT NULL AFTER `report_id`, ADD UNIQUE (`distinct_record`);
