-- OMR Scanner Database Setup
-- This script creates the necessary tables for the OMR (Optical Mark Recognition) system

-- Table: omr_answer_keys
-- Stores the correct answer keys for exams
CREATE TABLE IF NOT EXISTS `omr_answer_keys` (
  `key_id` int(11) NOT NULL AUTO_INCREMENT,
  `exam_id` varchar(50) NOT NULL,
  `answer_key` TEXT NOT NULL COMMENT 'JSON format of correct answers',
  `total_questions` int(11) DEFAULT 0,
  `subject` varchar(100) DEFAULT NULL,
  `created_by` varchar(50) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive') DEFAULT 'active',
  `client_id` varchar(50) NOT NULL,
  PRIMARY KEY (`key_id`),
  KEY `idx_exam_id` (`exam_id`),
  KEY `idx_client_id` (`client_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores answer keys for OMR exams';

-- Table: omr_results
-- Stores the overall results of OMR scans
CREATE TABLE IF NOT EXISTS `omr_results` (
  `result_id` varchar(50) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `student_name` varchar(255) DEFAULT NULL COMMENT 'Extracted from OCR',
  `exam_id` varchar(50) NOT NULL,
  `class_id` varchar(50) DEFAULT NULL,
  `detected_answers` TEXT COMMENT 'JSON format of detected answers',
  `score` int(11) DEFAULT 0,
  `total_questions` int(11) DEFAULT 0,
  `percentage` decimal(5,2) DEFAULT 0.00,
  `scan_image_path` varchar(500) DEFAULT NULL,
  `scan_timestamp` timestamp DEFAULT CURRENT_TIMESTAMP,
  `processed_by` varchar(50) NOT NULL,
  `processing_time` decimal(5,2) DEFAULT NULL COMMENT 'Time in seconds',
  `confidence_score` decimal(5,2) DEFAULT NULL COMMENT 'OCR/Detection confidence',
  `status` enum('pending','processed','verified','rejected') DEFAULT 'processed',
  `notes` TEXT DEFAULT NULL,
  `client_id` varchar(50) NOT NULL,
  PRIMARY KEY (`result_id`),
  KEY `idx_student_id` (`student_id`),
  KEY `idx_exam_id` (`exam_id`),
  KEY `idx_class_id` (`class_id`),
  KEY `idx_scan_timestamp` (`scan_timestamp`),
  KEY `idx_client_id` (`client_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores OMR scan results';

-- Table: omr_answer_details
-- Stores detailed answer information for each question
CREATE TABLE IF NOT EXISTS `omr_answer_details` (
  `detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `result_id` varchar(50) NOT NULL,
  `question_number` int(11) NOT NULL,
  `selected_answer` char(1) DEFAULT NULL COMMENT 'A, B, C, D or null if not detected',
  `correct_answer` char(1) DEFAULT NULL COMMENT 'A, B, C, D',
  `is_correct` tinyint(1) DEFAULT 0,
  `confidence_level` decimal(5,2) DEFAULT NULL COMMENT 'Detection confidence for this answer',
  `bubble_fill_ratio` decimal(5,2) DEFAULT NULL COMMENT 'Percentage of bubble filled',
  PRIMARY KEY (`detail_id`),
  KEY `idx_result_id` (`result_id`),
  KEY `idx_question_number` (`question_number`),
  KEY `idx_is_correct` (`is_correct`),
  FOREIGN KEY (`result_id`) REFERENCES `omr_results`(`result_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Detailed answer information for each question';

-- Table: omr_scan_sessions
-- Tracks scanning sessions for batch processing
CREATE TABLE IF NOT EXISTS `omr_scan_sessions` (
  `session_id` varchar(50) NOT NULL,
  `exam_id` varchar(50) NOT NULL,
  `class_id` varchar(50) DEFAULT NULL,
  `session_name` varchar(255) NOT NULL,
  `total_sheets` int(11) DEFAULT 0,
  `processed_sheets` int(11) DEFAULT 0,
  `failed_sheets` int(11) DEFAULT 0,
  `started_by` varchar(50) NOT NULL,
  `started_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `completed_at` timestamp NULL DEFAULT NULL,
  `status` enum('active','completed','cancelled') DEFAULT 'active',
  `notes` TEXT DEFAULT NULL,
  `client_id` varchar(50) NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `idx_exam_id` (`exam_id`),
  KEY `idx_class_id` (`class_id`),
  KEY `idx_started_at` (`started_at`),
  KEY `idx_client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tracks OMR scanning sessions';

-- Table: omr_templates
-- Stores OMR sheet templates for different formats
CREATE TABLE IF NOT EXISTS `omr_templates` (
  `template_id` varchar(50) NOT NULL,
  `template_name` varchar(255) NOT NULL,
  `total_questions` int(11) NOT NULL,
  `questions_per_column` int(11) DEFAULT 30,
  `number_of_columns` int(11) DEFAULT 2,
  `options_per_question` int(11) DEFAULT 4 COMMENT 'Usually A,B,C,D = 4 options',
  `template_config` TEXT COMMENT 'JSON configuration for bubble positions',
  `sample_image_path` varchar(500) DEFAULT NULL,
  `created_by` varchar(50) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `is_default` tinyint(1) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `client_id` varchar(50) NOT NULL,
  PRIMARY KEY (`template_id`),
  KEY `idx_client_id` (`client_id`),
  KEY `idx_is_default` (`is_default`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='OMR sheet templates and configurations';

-- Insert default template
INSERT INTO `omr_templates` 
(`template_id`, `template_name`, `total_questions`, `questions_per_column`, `number_of_columns`, 
 `options_per_question`, `template_config`, `created_by`, `is_default`, `client_id`) 
VALUES 
('OMR_TEMPLATE_001', 'Standard 60 Questions (2 Columns)', 60, 30, 2, 4,
 '{"name_region": {"x": 0, "y": 0, "width": 1, "height": 0.15}, 
   "question_columns": [
     {"start_x": 0.1, "start_y": 0.2, "end_y": 0.9}, 
     {"start_x": 0.5, "start_y": 0.2, "end_y": 0.9}
   ],
   "bubble_size": {"width": 0.03, "height": 0.04},
   "option_spacing": 0.08}',
 'system', 1, 'SYSTEM');

-- Create indexes for better performance
CREATE INDEX idx_omr_results_performance ON omr_results(client_id, exam_id, scan_timestamp DESC);
CREATE INDEX idx_omr_answers_analysis ON omr_answer_details(result_id, is_correct, question_number);
CREATE INDEX idx_omr_keys_lookup ON omr_answer_keys(client_id, exam_id, status);

-- Add sample answer key for testing
INSERT INTO `omr_answer_keys` 
(`exam_id`, `answer_key`, `total_questions`, `subject`, `created_by`, `client_id`)
VALUES 
('TEST_EXAM_001', 
 '{"1":"A","2":"B","3":"C","4":"D","5":"A","6":"B","7":"C","8":"D","9":"A","10":"B","11":"C","12":"D","13":"A","14":"B","15":"C","16":"D","17":"A","18":"B","19":"C","20":"D","21":"A","22":"B","23":"C","24":"D","25":"A","26":"B","27":"C","28":"D","29":"A","30":"B","31":"C","32":"D","33":"A","34":"B","35":"C","36":"D","37":"A","38":"B","39":"C","40":"D","41":"A","42":"B","43":"C","44":"D","45":"A","46":"B","47":"C","48":"D","49":"A","50":"B","51":"C","52":"D","53":"A","54":"B","55":"C","56":"D","57":"A","58":"B","59":"C","60":"D"}',
 60, 'Mathematics', 'system', 'SYSTEM');