-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 12, 2021 at 05:35 AM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 7.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `myschoolgh`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_terms`
--

DROP TABLE IF EXISTS `academic_terms`;
CREATE TABLE `academic_terms` (
  `id` int(11) NOT NULL,
  `client_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `academic_terms`
--

INSERT INTO `academic_terms` (`id`, `client_id`, `name`, `description`) VALUES
(1, 'LKJAFD94R', '1st', '1st Semester'),
(2, 'LKJAFD94R', '2nd', '2nd Semester'),
(3, 'LKJAFD94R', '3rd', '3rd Semester');

-- --------------------------------------------------------

--
-- Table structure for table `academic_years`
--

DROP TABLE IF EXISTS `academic_years`;
CREATE TABLE `academic_years` (
  `id` int(11) NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `year_group` varchar(255) DEFAULT NULL,
  `academic_term` varchar(32) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
CREATE TABLE `announcements` (
  `id` int(11) UNSIGNED NOT NULL,
  `item_id` varchar(32) NOT NULL DEFAULT 'NULL',
  `client_id` varchar(32) DEFAULT NULL,
  `user_id` varchar(32) DEFAULT NULL,
  `user_type` varchar(32) DEFAULT NULL,
  `section` varchar(255) DEFAULT 'dashboard,index',
  `recipient_group` varchar(1000) NOT NULL DEFAULT 'all',
  `persistent` enum('0','1') DEFAULT '0',
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `modal_function` varchar(84) DEFAULT 'generalNoticeHandler',
  `subject` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `content` text DEFAULT NULL,
  `seen_by` text DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `replies_count` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `last_updated_by` varchar(32) DEFAULT NULL,
  `last_updated_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

DROP TABLE IF EXISTS `assignments`;
CREATE TABLE `assignments` (
  `id` int(11) NOT NULL,
  `client_id` varchar(32) CHARACTER SET latin1 DEFAULT '1',
  `type` enum('Test','Assignment','Quiz','Exam','Group') COLLATE utf8_unicode_ci DEFAULT 'Assignment',
  `assignment_type` enum('file_attachment','multiple_choice') COLLATE utf8_unicode_ci DEFAULT 'file_attachment',
  `assigned_to` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `assigned_to_list` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `item_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `course_tutor` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `department_id` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  `course_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `class_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `grading` int(11) DEFAULT 0,
  `assignment_title` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `assignment_description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_created` datetime DEFAULT current_timestamp(),
  `created_by` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `due_time` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` enum('Draft','Pending','Graded','Cancelled','Closed','Answered') COLLATE utf8_unicode_ci DEFAULT 'Pending',
  `allowed_time` varchar(4) COLLATE utf8_unicode_ci DEFAULT '30',
  `date_closed` datetime DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  `date_published` datetime DEFAULT NULL,
  `status` enum('0','1') COLLATE utf8_unicode_ci DEFAULT '1',
  `deleted` enum('0','1') COLLATE utf8_unicode_ci DEFAULT '0',
  `academic_year` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '2019/2020',
  `academic_term` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1st',
  `replies_count` varchar(14) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `comments_count` varchar(14) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assignments_answers`
--

DROP TABLE IF EXISTS `assignments_answers`;
CREATE TABLE `assignments_answers` (
  `id` int(11) NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `assignment_id` varchar(32) DEFAULT NULL,
  `student_id` varchar(32) DEFAULT NULL,
  `answers` text DEFAULT NULL,
  `scores` varchar(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `assignments_questions`
--

DROP TABLE IF EXISTS `assignments_questions`;
CREATE TABLE `assignments_questions` (
  `id` int(11) NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `assignment_id` varchar(32) DEFAULT NULL,
  `question` text DEFAULT NULL,
  `difficulty` enum('easy','medium','advanced') NOT NULL DEFAULT 'medium',
  `option_a` varchar(255) DEFAULT NULL,
  `option_b` varchar(255) DEFAULT NULL,
  `option_c` varchar(255) DEFAULT NULL,
  `option_d` varchar(255) DEFAULT NULL,
  `option_e` varchar(255) DEFAULT NULL,
  `option_f` varchar(255) DEFAULT NULL,
  `answer_type` enum('option','multiple','numeric','input') NOT NULL DEFAULT 'option',
  `created_by` varchar(32) DEFAULT NULL,
  `correct_answer` varchar(255) DEFAULT NULL,
  `marks` varchar(12) DEFAULT '1',
  `correct_answer_description` text DEFAULT NULL,
  `attempted_by` text DEFAULT NULL,
  `current_state` enum('Published','Draft') NOT NULL DEFAULT 'Published',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted` enum('0','1') DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `assignments_submitted`
--

DROP TABLE IF EXISTS `assignments_submitted`;
CREATE TABLE `assignments_submitted` (
  `id` int(11) NOT NULL,
  `client_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `assignment_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `score` varchar(12) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `graded` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `handed_in` enum('Pending','Submitted') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Pending',
  `date_submitted` datetime NOT NULL DEFAULT current_timestamp(),
  `date_graded` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blood_groups`
--

DROP TABLE IF EXISTS `blood_groups`;
CREATE TABLE `blood_groups` (
  `id` int(11) NOT NULL,
  `name` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `blood_groups`
--

INSERT INTO `blood_groups` (`id`, `name`) VALUES
(1, 'A+'),
(2, 'A-'),
(3, 'B+'),
(4, 'B-'),
(5, 'O+'),
(6, 'O-');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
CREATE TABLE `books` (
  `id` int(10) UNSIGNED NOT NULL,
  `item_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `client_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `isbn` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `book_image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `author` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `quantity` int(10) DEFAULT NULL,
  `rack_no` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `row_no` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `category_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `desc` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `department_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `deleted` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `class_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_by` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` date DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `books_borrowed`
--

DROP TABLE IF EXISTS `books_borrowed`;
CREATE TABLE `books_borrowed` (
  `id` int(12) UNSIGNED NOT NULL,
  `client_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `the_type` enum('issued','requested','request') COLLATE utf8_unicode_ci DEFAULT 'issued',
  `item_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_role` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `books_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `issued_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `fine` varchar(21) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0.00',
  `actual_paid` varchar(21) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0.00',
  `fine_paid` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `status` enum('Issued','Requested','Returned','Cancelled','Approved') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Issued',
  `created_at` datetime DEFAULT current_timestamp(),
  `issued_by` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `actual_date_returned` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp(),
  `deleted` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `books_borrowed_details`
--

DROP TABLE IF EXISTS `books_borrowed_details`;
CREATE TABLE `books_borrowed_details` (
  `id` int(11) NOT NULL,
  `borrowed_id` varchar(32) DEFAULT NULL,
  `book_id` varchar(32) DEFAULT NULL,
  `date_borrowed` datetime DEFAULT current_timestamp(),
  `return_date` date DEFAULT NULL,
  `quantity` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `fine` decimal(10,2) NOT NULL DEFAULT 0.00,
  `actual_paid` decimal(12,2) NOT NULL DEFAULT 0.00,
  `fine_paid` enum('0','1') NOT NULL DEFAULT '0',
  `issued_by` varchar(32) DEFAULT NULL,
  `received_by` varchar(32) DEFAULT NULL,
  `actual_date_returned` datetime DEFAULT NULL,
  `status` enum('Returned','Borrowed') NOT NULL DEFAULT 'Borrowed',
  `deleted` enum('0','1') DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `books_stock`
--

DROP TABLE IF EXISTS `books_stock`;
CREATE TABLE `books_stock` (
  `id` int(11) NOT NULL,
  `books_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `quantity` int(10) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `books_type`
--

DROP TABLE IF EXISTS `books_type`;
CREATE TABLE `books_type` (
  `id` int(11) NOT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `department_id` varchar(32) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `created_by` varchar(32) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `upload_id` varchar(12) DEFAULT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `client_id` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `class_code` varchar(32) DEFAULT NULL,
  `class_size` int(12) UNSIGNED DEFAULT NULL,
  `courses_list` varchar(2000) DEFAULT NULL,
  `rooms_list` varchar(2000) DEFAULT NULL,
  `weekly_meeting` int(12) UNSIGNED DEFAULT NULL,
  `department_id` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
  `academic_year` varchar(32) DEFAULT NULL,
  `academic_term` varchar(32) DEFAULT '1',
  `class_teacher` varchar(35) CHARACTER SET utf8mb4 DEFAULT NULL,
  `class_assistant` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
  `status` enum('0','1') CHARACTER SET utf8mb4 DEFAULT '1',
  `created_by` varchar(32) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `upload_id`, `item_id`, `client_id`, `name`, `slug`, `class_code`, `class_size`, `courses_list`, `rooms_list`, `weekly_meeting`, `department_id`, `academic_year`, `academic_term`, `class_teacher`, `class_assistant`, `status`, `created_by`, `description`, `date_created`, `date_updated`) VALUES
(1, NULL, 'B0wVXoJq8cLnaeOWC4EvkRzmhiHSlMry', 'TLIS0000001', 'JHS 1', 'jhs-1', 'CKA', 32, '[\"5sLO0GR2qYThuQK39CzNc1gy4xlHv8PJ\"]', '[]', NULL, NULL, '2020/2021', '1st', 'tnThYo5wKHG2XxgPdSVkErb7zLlqum1A', '1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', '1', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', NULL, '2021-02-24 06:10:35', '2021-02-24 06:10:35'),
(2, NULL, 'EDMOiv6CQ2nLrP08d7XFygGSkIjqhcxA', 'TLIS0000001', 'JHS 2', 'jhs-2', 'LAK', 35, NULL, '[]', NULL, NULL, '2020/2021', '1st', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', '1', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', NULL, '2021-02-24 06:10:53', '2021-02-24 06:10:53');

-- --------------------------------------------------------

--
-- Table structure for table `classes_rooms`
--

DROP TABLE IF EXISTS `classes_rooms`;
CREATE TABLE `classes_rooms` (
  `item_id` varchar(255) NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `capacity` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `classes_list` varchar(2000) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `clients_accounts`
--

DROP TABLE IF EXISTS `clients_accounts`;
CREATE TABLE `clients_accounts` (
  `id` int(11) NOT NULL,
  `client_id` varchar(255) DEFAULT NULL,
  `client_name` varchar(255) DEFAULT NULL,
  `client_contact` varchar(255) DEFAULT NULL,
  `client_secondary_contact` varchar(32) DEFAULT NULL,
  `client_address` varchar(255) DEFAULT NULL,
  `client_email` varchar(255) DEFAULT NULL,
  `client_website` varchar(245) DEFAULT NULL,
  `client_logo` varchar(255) DEFAULT NULL,
  `client_location` varchar(255) DEFAULT NULL,
  `client_category` varchar(64) DEFAULT NULL,
  `client_preferences` varchar(5000) DEFAULT NULL,
  `client_status` enum('0','1') NOT NULL DEFAULT '1',
  `client_state` enum('Expired','Pending','Activated','Suspended','Active') NOT NULL DEFAULT 'Pending',
  `ip_address` varchar(32) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `clients_accounts`
--

INSERT INTO `clients_accounts` (`id`, `client_id`, `client_name`, `client_contact`, `client_secondary_contact`, `client_address`, `client_email`, `client_website`, `client_logo`, `client_location`, `client_category`, `client_preferences`, `client_status`, `client_state`, `ip_address`, `date_created`) VALUES
(1, 'TLIS0000001', 'True Love International School', '0550107770', '0240553604', 'P. O. Box AF 2582, Adentan Accra', 'emmallob14@gmail.com', 'https://www.trueloveinternational.com', 'assets/img/accounts/oEbGi1JVC0BqZp3gwtHKmFQYRAUWjcvl.png', 'Dodowa', NULL, '{\"academics\":{\"academic_year\":\"2020\\/2021\",\"academic_term\":\"1st\",\"term_starts\":\"2021-01-18\",\"term_ends\":\"2021-05-07\",\"next_academic_year\":\"2020\\/2021\",\"next_academic_term\":\"2nd\",\"next_term_starts\":\"2021-05-24\",\"next_term_ends\":\"2021-07-30\"},\"labels\":{\"student_label\":\"sl\",\"parent_label\":\"gl\",\"teacher_label\":\"tl\",\"staff_label\":\"stl\",\"course_label\":\"cl\",\"book_label\":\"bkl\",\"class_label\":\"cl\",\"department_label\":\"dp\",\"section_label\":\"sl\",\"receipt_label\":\"rel\",\"currency\":\"GHS\"},\"opening_days\":[\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\",\"Friday\"]}', '1', 'Active', NULL, '2021-02-24 04:46:36'),
(2, 'MSIS0000002', 'Morning Star International School', '0550107770', NULL, 'Accra, Cantanments', 'morningstar@gmail.com', 'https://www.morningstar.com', NULL, '', NULL, '{\"academics\":{\"academic_year\":\"2020/2021\",\"academic_term\":\"1st\",\"term_starts\":\"2021-01-18\",\"term_ends\":\"2021-03-31\",\"next_academic_year\":\"\",\"next_academic_term\":\"\"},\"labels\":{\"student_label\":\"sl\",\"parent_label\":\"gl\",\"teacher_label\":\"tl\",\"staff_label\":\"stl\",\"course_label\":\"crl\",\"book_label\":\"bkl\",\"class_label\":\"cl\",\"department_label\":\"dl\",\"section_label\":\"sl\",\"receipt_label\":\"rl\",\"currency\":\"GHS\"},\"opening_days\":[\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\",\"Friday\"],\"setup_upload\":{\"staff\":true}}', '1', 'Active', NULL, '2021-02-24 04:46:36'),
(3, 'GIS00003', 'Galaxy International School', '233550107770', '233240553604', 'P. O. Box DT 2582, Accra', 'info@gallaxyinternationalschool.com', 'https://www.gallaxyinternationalschool.com', 'assets/img/accounts/wsxCG7avKgQRZ8OH5MBhN9z61mefjYcL.png', 'Accra', NULL, '{\"academics\":{\"academic_year\":\"2021\\/2022\",\"academic_term\":\"1st\",\"term_starts\":\"2021-01-12\",\"term_ends\":\"2021-04-01\",\"next_academic_year\":\"2021\\/2022\",\"next_academic_term\":\"2nd\",\"next_term_starts\":\"2021-04-30\",\"next_term_ends\":\"2021-06-30\"},\"labels\":{\"student_label\":\"st\",\"parent_label\":\"gu\",\"teacher_label\":\"tl\",\"staff_label\":\"sl\",\"course_label\":\"cl\",\"book_label\":\"bk\",\"class_label\":\"ctl\",\"department_label\":\"dp\",\"section_label\":\"st\",\"receipt_label\":\"rl\",\"currency\":\"GHS\"},\"opening_days\":[\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\",\"Friday\"]}', '1', 'Activated', '::1', '2021-03-17 10:20:28'),
(4, 'MSGH000004', 'Test Sample School', '233550107770', '233500021983', 'Test School Address', 'testsampleschool@mail.com', 'https://sampleschoolgh.com', NULL, 'Accra', NULL, '{\"academics\":{\"academic_year\":\"2019\\/2020\",\"academic_term\":\"1st\",\"term_starts\":\"2021-03-01\",\"term_ends\":\"2021-05-29\",\"next_academic_year\":\"2019\\/2020\",\"next_academic_term\":\"2nd\",\"next_term_starts\":\"2021-06-15\",\"next_term_ends\":\"2021-08-31\"},\"labels\":{\"student_label\":\"ST\",\"parent_label\":\"GL\",\"teacher_label\":\"TL\",\"staff_label\":\"SL\",\"course_label\":\"CL\",\"book_label\":\"BK\",\"class_label\":\"\",\"department_label\":\"\",\"section_label\":\"\",\"receipt_label\":\"\",\"currency\":\"GHS\"},\"opening_days\":[\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\",\"Friday\"],\"account\":{\"verified_date\":\"2021-05-06 11:49PM\",\"expiry\":\"2021-06-06 11:49PM\",\"activation_code\":\"K1lwMeg7hxHL4DWI89jNZQRSOtXp0bCAdJoT3FPrn5G6cEifkqaYyU\"}}', '1', 'Activated', '::1', '2021-05-06 22:59:24');

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

DROP TABLE IF EXISTS `contact`;
CREATE TABLE `contact` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `user_agent` varchar(255) DEFAULT NULL,
  `ip_address` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `contact`
--

INSERT INTO `contact` (`id`, `name`, `email`, `subject`, `message`, `date_created`, `user_agent`, `ip_address`) VALUES
(1, 'afdafldjlk', 'lkjlkajflkdkaf@lkajflk.com', 'lafkldl', 'lkjlakjflafdafd', '2021-02-22 01:16:58', NULL, '::1'),
(2, 'afaljdlafjdl', 'lkjlakfjdlk@ljaf.com', 'ljaflkdlkajfdklajf kdjfl', 'lkjlakfjkdja kjfafd afdfd', '2021-02-22 01:07:07', NULL, '::1'),
(3, 'lkjfalkjk', 'lkjlkjfkajfdkj2@kljAlf.co', 'lkajfldjafkd jlfjakflk', 'jlkjfa kjdalkfd afd afdl kjflkdafdafdfd', '2021-02-22 01:20:31', NULL, '::1'),
(4, 'alkfjalfdjkj', 'lkjlkajfdksfj@lkfj.com', 'ljalkfdlkaf djl', 'lkjlkfakfd jkafj klajfkjffaddfsdf', '2021-02-22 01:20:56', NULL, '::1');

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

DROP TABLE IF EXISTS `country`;
CREATE TABLE `country` (
  `id` int(11) NOT NULL,
  `country_name` varchar(255) NOT NULL,
  `country_code` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `country`
--

INSERT INTO `country` (`id`, `country_name`, `country_code`) VALUES
(1, 'Afghanistan', 'AF'),
(2, 'Aland Islands', 'AX'),
(3, 'Albania', 'AL'),
(4, 'Algeria', 'DZ'),
(5, 'American Samoa', 'AS'),
(6, 'Andorra', 'AD'),
(7, 'Angola', 'AO'),
(8, 'Anguilla', 'AI'),
(9, 'Antarctica', 'AQ'),
(10, 'Antigua and Barbuda', 'AG'),
(11, 'Argentina', 'AR'),
(12, 'Armenia', 'AM'),
(13, 'Aruba', 'AW'),
(14, 'Australia', 'AU'),
(15, 'Austria', 'AT'),
(16, 'Azerbaijan', 'AZ'),
(17, 'Bahamas', 'BS'),
(18, 'Bahrain', 'BH'),
(19, 'Bangladesh', 'BD'),
(20, 'Barbados', 'BB'),
(21, 'Belarus', 'BY'),
(22, 'Belgium', 'BE'),
(23, 'Belize', 'BZ'),
(24, 'Benin', 'BJ'),
(25, 'Bermuda', 'BM'),
(26, 'Bhutan', 'BT'),
(27, 'Bolivia, Plurinational State of', 'BO'),
(28, 'Bonaire, Sint Eustatius and Saba', 'BQ'),
(29, 'Bosnia and Herzegovina', 'BA'),
(30, 'Botswana', 'BW'),
(31, 'Bouvet Island', 'BV'),
(32, 'Brazil', 'BR'),
(33, 'British Indian Ocean Territory', 'IO'),
(34, 'Brunei Darussalam', 'BN'),
(35, 'Bulgaria', 'BG'),
(36, 'Burkina Faso', 'BF'),
(37, 'Burundi', 'BI'),
(38, 'Cambodia', 'KH'),
(39, 'Cameroon', 'CM'),
(40, 'Canada', 'CA'),
(41, 'Cape Verde', 'CV'),
(42, 'Cayman Islands', 'KY'),
(43, 'Central African Republic', 'CF'),
(44, 'Chad', 'TD'),
(45, 'Chile', 'CL'),
(46, 'China', 'CN'),
(47, 'Christmas Island', 'CX'),
(48, 'Cocos (Keeling) Islands', 'CC'),
(49, 'Colombia', 'CO'),
(50, 'Comoros', 'KM'),
(51, 'Congo', 'CG'),
(52, 'Congo, the Democratic Republic of the', 'CD'),
(53, 'Cook Islands', 'CK'),
(54, 'Costa Rica', 'CR'),
(55, 'Cote d\'Ivoire', 'CI'),
(56, 'Croatia', 'HR'),
(57, 'Cuba', 'CU'),
(58, 'Curacao', 'CW'),
(59, 'Cyprus', 'CY'),
(60, 'Czech Republic', 'CZ'),
(61, 'Denmark', 'DK'),
(62, 'Djibouti', 'DJ'),
(63, 'Dominica', 'DM'),
(64, 'Dominican Republic', 'DO'),
(65, 'Ecuador', 'EC'),
(66, 'Egypt', 'EG'),
(67, 'El Salvador', 'SV'),
(68, 'Equatorial Guinea', 'GQ'),
(69, 'Eritrea', 'ER'),
(70, 'Estonia', 'EE'),
(71, 'Ethiopia', 'ET'),
(72, 'Falkland Islands (Malvinas)', 'FK'),
(73, 'Faroe Islands', 'FO'),
(74, 'Fiji', 'FJ'),
(75, 'Finland', 'FI'),
(76, 'France', 'FR'),
(77, 'French Guiana', 'GF'),
(78, 'French Polynesia', 'PF'),
(79, 'French Southern Territories', 'TF'),
(80, 'Gabon', 'GA'),
(81, 'Gambia', 'GM'),
(82, 'Georgia', 'GE'),
(83, 'Germany', 'DE'),
(84, 'Ghana', 'GH'),
(85, 'Gibraltar', 'GI'),
(86, 'Greece', 'GR'),
(87, 'Greenland', 'GL'),
(88, 'Grenada', 'GD'),
(89, 'Guadeloupe', 'GP'),
(90, 'Guam', 'GU'),
(91, 'Guatemala', 'GT'),
(92, 'Guernsey', 'GG'),
(93, 'Guinea', 'GN'),
(94, 'Guinea-Bissau', 'GW'),
(95, 'Guyana', 'GY'),
(96, 'Haiti', 'HT'),
(97, 'Heard Island and McDonald Islands', 'HM'),
(98, 'Holy See (Vatican City State)', 'VA'),
(99, 'Honduras', 'HN'),
(100, 'Hong Kong', 'HK'),
(101, 'Hungary', 'HU'),
(102, 'Iceland', 'IS'),
(103, 'India', 'IN'),
(104, 'Indonesia', 'ID'),
(105, 'Iran, Islamic Republic of', 'IR'),
(106, 'Iraq', 'IQ'),
(107, 'Ireland', 'IE'),
(108, 'Isle of Man', 'IM'),
(109, 'Israel', 'IL'),
(110, 'Italy', 'IT'),
(111, 'Jamaica', 'JM'),
(112, 'Japan', 'JP'),
(113, 'Jersey', 'JE'),
(114, 'Jordan', 'JO'),
(115, 'Kazakhstan', 'KZ'),
(116, 'Kenya', 'KE'),
(117, 'Kiribati', 'KI'),
(118, 'Korea, Democratic People\'s Republic of', 'KP'),
(119, 'Korea, Republic of', 'KR'),
(120, 'Kuwait', 'KW'),
(121, 'Kyrgyzstan', 'KG'),
(122, 'Lao People\'s Democratic Republic', 'LA'),
(123, 'Latvia', 'LV'),
(124, 'Lebanon', 'LB'),
(125, 'Lesotho', 'LS'),
(126, 'Liberia', 'LR'),
(127, 'Libya', 'LY'),
(128, 'Liechtenstein', 'LI'),
(129, 'Lithuania', 'LT'),
(130, 'Luxembourg', 'LU'),
(131, 'Macao', 'MO'),
(132, 'Macedonia, the Former Yugoslav Republic of', 'MK'),
(133, 'Madagascar', 'MG'),
(134, 'Malawi', 'MW'),
(135, 'Malaysia', 'MY'),
(136, 'Maldives', 'MV'),
(137, 'Mali', 'ML'),
(138, 'Malta', 'MT'),
(139, 'Marshall Islands', 'MH'),
(140, 'Martinique', 'MQ'),
(141, 'Mauritania', 'MR'),
(142, 'Mauritius', 'MU'),
(143, 'Mayotte', 'YT'),
(144, 'Mexico', 'MX'),
(145, 'Micronesia, Federated States of', 'FM'),
(146, 'Moldova, Republic of', 'MD'),
(147, 'Monaco', 'MC'),
(148, 'Mongolia', 'MN'),
(149, 'Montenegro', 'ME'),
(150, 'Montserrat', 'MS'),
(151, 'Morocco', 'MA'),
(152, 'Mozambique', 'MZ'),
(153, 'Myanmar', 'MM'),
(154, 'Namibia', 'NA'),
(155, 'Nauru', 'NR'),
(156, 'Nepal', 'NP'),
(157, 'Netherlands', 'NL'),
(158, 'New Caledonia', 'NC'),
(159, 'New Zealand', 'NZ'),
(160, 'Nicaragua', 'NI'),
(161, 'Niger', 'NE'),
(162, 'Nigeria', 'NG'),
(163, 'Niue', 'NU'),
(164, 'Norfolk Island', 'NF'),
(165, 'Northern Mariana Islands', 'MP'),
(166, 'Norway', 'NO'),
(167, 'Oman', 'OM'),
(168, 'Pakistan', 'PK'),
(169, 'Palau', 'PW'),
(170, 'Palestine, State of', 'PS'),
(171, 'Panama', 'PA'),
(172, 'Papua New Guinea', 'PG'),
(173, 'Paraguay', 'PY'),
(174, 'Peru', 'PE'),
(175, 'Philippines', 'PH'),
(176, 'Pitcairn', 'PN'),
(177, 'Poland', 'PL'),
(178, 'Portugal', 'PT'),
(179, 'Puerto Rico', 'PR'),
(180, 'Qatar', 'QA'),
(181, 'Reunion', 'RE'),
(182, 'Romania', 'RO'),
(183, 'Russian Federation', 'RU'),
(184, 'Rwanda', 'RW'),
(185, 'Saint Barthelemy', 'BL'),
(186, 'Saint Helena, Ascension and Tristan da Cunha', 'SH'),
(187, 'Saint Kitts and Nevis', 'KN'),
(188, 'Saint Lucia', 'LC'),
(189, 'Saint Martin (French part)', 'MF'),
(190, 'Saint Pierre and Miquelon', 'PM'),
(191, 'Saint Vincent and the Grenadines', 'VC'),
(192, 'Samoa', 'WS'),
(193, 'San Marino', 'SM'),
(194, 'Sao Tome and Principe', 'ST'),
(195, 'Saudi Arabia', 'SA'),
(196, 'Senegal', 'SN'),
(197, 'Serbia', 'RS'),
(198, 'Seychelles', 'SC'),
(199, 'Sierra Leone', 'SL'),
(200, 'Singapore', 'SG'),
(201, 'Sint Maarten (Dutch part)', 'SX'),
(202, 'Slovakia', 'SK'),
(203, 'Slovenia', 'SI'),
(204, 'Solomon Islands', 'SB'),
(205, 'Somalia', 'SO'),
(206, 'South Africa', 'ZA'),
(207, 'South Georgia and the South Sandwich Islands', 'GS'),
(208, 'South Sudan', 'SS'),
(209, 'Spain', 'ES'),
(210, 'Sri Lanka', 'LK'),
(211, 'Sudan', 'SD'),
(212, 'Suriname', 'SR'),
(213, 'Svalbard and Jan Mayen', 'SJ'),
(214, 'Swaziland', 'SZ'),
(215, 'Sweden', 'SE'),
(216, 'Switzerland', 'CH'),
(217, 'Syrian Arab Republic', 'SY'),
(218, 'Taiwan, Province of China', 'TW'),
(219, 'Tajikistan', 'TJ'),
(220, 'Tanzania, United Republic of', 'TZ'),
(221, 'Thailand', 'TH'),
(222, 'Timor-Leste', 'TL'),
(223, 'Togo', 'TG'),
(224, 'Tokelau', 'TK'),
(225, 'Tonga', 'TO'),
(226, 'Trinidad and Tobago', 'TT'),
(227, 'Tunisia', 'TN'),
(228, 'Turkey', 'TR'),
(229, 'Turkmenistan', 'TM'),
(230, 'Turks and Caicos Islands', 'TC'),
(231, 'Tuvalu', 'TV'),
(232, 'Uganda', 'UG'),
(233, 'Ukraine', 'UA'),
(234, 'United Arab Emirates', 'AE'),
(235, 'United Kingdom', 'GB'),
(236, 'United States', 'US'),
(237, 'United States Minor Outlying Islands', 'UM'),
(238, 'Uruguay', 'UY'),
(239, 'Uzbekistan', 'UZ'),
(240, 'Vanuatu', 'VU'),
(241, 'Venezuela, Bolivarian Republic of', 'VE'),
(242, 'Viet Nam', 'VN'),
(243, 'Virgin Islands, British', 'VG'),
(244, 'Virgin Islands, U.S.', 'VI'),
(245, 'Wallis and Futuna', 'WF'),
(246, 'Western Sahara', 'EH'),
(247, 'Yemen', 'YE'),
(248, 'Zambia', 'ZM'),
(249, 'Zimbabwe', 'ZW');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `upload_id` varchar(12) DEFAULT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `client_id` varchar(32) DEFAULT 'LKJAFD94R',
  `course_code` varchar(255) DEFAULT NULL,
  `credit_hours` varchar(25) DEFAULT NULL,
  `academic_term` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
  `academic_year` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
  `department_id` varchar(32) DEFAULT NULL,
  `programme_id` varchar(32) DEFAULT NULL,
  `weekly_meeting` int(12) UNSIGNED DEFAULT NULL,
  `class_id` varchar(2000) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `units_count` varchar(12) DEFAULT '0',
  `lessons_count` varchar(12) DEFAULT '0',
  `course_tutor` varchar(2000) DEFAULT NULL COMMENT 'THIS  IS WHERE THE ID OF THE TEACHER OR WHOEVER INSERTED IT WILL APPEAR',
  `description` text DEFAULT NULL,
  `date_created` date DEFAULT current_timestamp(),
  `created_by` varchar(35) DEFAULT NULL,
  `date_updated` datetime DEFAULT current_timestamp(),
  `status` enum('0','1') DEFAULT '1',
  `deleted` enum('0','1') DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `upload_id`, `item_id`, `client_id`, `course_code`, `credit_hours`, `academic_term`, `academic_year`, `department_id`, `programme_id`, `weekly_meeting`, `class_id`, `name`, `slug`, `units_count`, `lessons_count`, `course_tutor`, `description`, `date_created`, `created_by`, `date_updated`, `status`, `deleted`) VALUES
(1, 'gWPeAo9kBm5c', 'BAybmg6is1Ff7ruWveIxhdNYXESJH38U', 'TLIS0000001', 'c001', '3', '1st', '2020/2021', NULL, NULL, 10, '[\"B0wVXoJq8cLnaeOWC4EvkRzmhiHSlMry\",\"eXOHZA8cmSqKnJ06CPaI7EhfvDBs2LxM\"]', 'introduction to jquery', 'introduction-to-jquery', '0', '0', '[\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\",\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\"]', 'this is the introduction to jquery', '2021-02-20', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-02-23 09:32:04', '1', '0'),
(2, 'gWPeAo9kBm5c', 'XUuG1qdL6SVFyMfCQkP2s5vcpxmgtTHj', 'TLIS0000001', 'c002', '4', '1st', '2020/2021', NULL, NULL, 8, '[\"B0wVXoJq8cLnaeOWC4EvkRzmhiHSlMry\",\"eXOHZA8cmSqKnJ06CPaI7EhfvDBs2LxM\"]', 'basics of programming', 'basics-of-programming', '0', '0', '[\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\"]', 'this is the programming techniques', '2021-02-20', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-02-23 09:32:19', '1', '0'),
(3, 'gWPeAo9kBm5c', '5sLO0GR2qYThuQK39CzNc1gy4xlHv8PJ', 'TLIS0000001', 'c003', '4', '1st', '2020/2021', NULL, NULL, 12, '[\"B0wVXoJq8cLnaeOWC4EvkRzmhiHSlMry\"]', 'object oriented programming', 'object-oriented-programming', '0', '0', '[\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\"]', 'this is for object oriented programming', '2021-02-20', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-04-17 14:26:17', '1', '0');

-- --------------------------------------------------------

--
-- Table structure for table `courses_plan`
--

DROP TABLE IF EXISTS `courses_plan`;
CREATE TABLE `courses_plan` (
  `id` int(11) NOT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `course_id` varchar(32) DEFAULT NULL,
  `unit_id` varchar(32) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `plan_type` enum('unit','lesson') NOT NULL DEFAULT 'unit',
  `academic_term` varchar(32) DEFAULT NULL,
  `academic_year` varchar(32) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_by` varchar(32) DEFAULT NULL,
  `date_created` datetime DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT current_timestamp(),
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `courses_resource_links`
--

DROP TABLE IF EXISTS `courses_resource_links`;
CREATE TABLE `courses_resource_links` (
  `id` int(11) NOT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `course_id` varchar(32) DEFAULT NULL,
  `lesson_id` varchar(2000) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `resource_type` enum('link','file') NOT NULL DEFAULT 'link',
  `link_url` varchar(500) DEFAULT NULL,
  `link_name` varchar(500) DEFAULT NULL,
  `created_by` varchar(32) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `cron_scheduler`
--

DROP TABLE IF EXISTS `cron_scheduler`;
CREATE TABLE `cron_scheduler` (
  `id` int(11) NOT NULL,
  `query` text DEFAULT NULL,
  `item_id` varchar(255) DEFAULT NULL,
  `user_id` varchar(32) DEFAULT NULL,
  `notice_code` varchar(12) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `cron_type` varchar(32) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0',
  `active_date` datetime DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_processed` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cron_scheduler`
--

INSERT INTO `cron_scheduler` (`id`, `query`, `item_id`, `user_id`, `notice_code`, `subject`, `cron_type`, `status`, `active_date`, `date_created`, `date_processed`) VALUES
(1, NULL, '7pcUdSFJ2au8', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', NULL, NULL, 'terminal_report', '1', '2021-03-17 11:39:07', '2021-03-17 11:36:57', '2021-03-17 11:47:22'),
(2, NULL, '7pcUdSFJ2au8', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', NULL, NULL, 'terminal_report', '1', '2021-03-17 11:39:07', '2021-03-17 11:37:17', '2021-03-17 11:47:22'),
(3, NULL, '7pcUdSFJ2au8', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', NULL, NULL, 'terminal_report', '1', '2021-03-17 11:39:07', '2021-03-17 11:38:06', '2021-03-17 11:47:22'),
(4, NULL, '7pcUdSFJ2au8', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', NULL, NULL, 'terminal_report', '1', '2021-03-17 11:39:07', '2021-03-17 11:39:07', '2021-03-17 11:47:23'),
(5, NULL, 'ZGJ3HPCDQYINMPQ9', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', NULL, NULL, 'terminal_report', '1', '2021-04-17 14:30:28', '2021-04-17 14:30:28', '2021-04-17 14:36:57'),
(6, NULL, '7AC2NXYLFPRTBO31', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', NULL, NULL, 'terminal_report', '1', '2021-04-17 14:33:56', '2021-04-17 14:33:56', '2021-04-17 14:36:57');

-- --------------------------------------------------------

--
-- Table structure for table `currency`
--

DROP TABLE IF EXISTS `currency`;
CREATE TABLE `currency` (
  `id` int(11) NOT NULL,
  `currency` varchar(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `currency`
--

INSERT INTO `currency` (`id`, `currency`) VALUES
(1, 'GHS'),
(2, 'USD'),
(3, 'GBP');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `client_id` varchar(32) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `upload_id` varchar(32) DEFAULT NULL,
  `department_code` varchar(32) DEFAULT NULL,
  `name` varchar(25) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT 'assets/img/placeholder.jpg',
  `description` text DEFAULT NULL,
  `department_head` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `created_by` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `event_image` varchar(255) DEFAULT NULL,
  `audience` varchar(32) DEFAULT NULL,
  `event_type` varchar(32) DEFAULT NULL,
  `is_holiday` varchar(32) DEFAULT NULL,
  `is_mailable` varchar(32) DEFAULT NULL,
  `date_emailed` datetime DEFAULT NULL,
  `emailed_state` enum('0','1') NOT NULL DEFAULT '0',
  `created_by` varchar(32) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `replies_count` varchar(13) NOT NULL DEFAULT '0',
  `comments_count` varchar(13) NOT NULL DEFAULT '0',
  `state` enum('Pending','Cancelled','Held','Ongoing') NOT NULL DEFAULT 'Pending',
  `status` enum('0','1') DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `client_id`, `item_id`, `title`, `description`, `start_date`, `end_date`, `event_image`, `audience`, `event_type`, `is_holiday`, `is_mailable`, `date_emailed`, `emailed_state`, `created_by`, `date_created`, `replies_count`, `comments_count`, `state`, `status`) VALUES
(1, 'TLIS0000001', 'bacOBo6KPX0imGHJ7ZE8Y2Tr1kDSVsNW', 'May Day', NULL, '2021-05-03', '2021-05-03', NULL, 'all', 'SYed3LuwXgKkh60P7Uzc2W41qNQHGyDr', 'on', NULL, NULL, '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-03 20:18:45', '0', '0', 'Held', '1'),
(2, 'TLIS0000001', 'C9nNU06YfeJFOp1Hv2LSE5ubGaw3Mqzt', 'Good Friday', NULL, '2021-04-02', '2021-04-02', NULL, 'all', 'SYed3LuwXgKkh60P7Uzc2W41qNQHGyDr', 'on', NULL, NULL, '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-03 20:23:15', '0', '0', 'Held', '1'),
(3, 'TLIS0000001', 'hUyM50dWA1OwETareXCbLB3RvKz2mqYV', 'Easter Monday', NULL, '2021-04-05', '2021-04-05', NULL, 'all', 'SYed3LuwXgKkh60P7Uzc2W41qNQHGyDr', 'on', NULL, NULL, '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-03 20:23:39', '0', '0', 'Held', '1'),
(4, 'TLIS0000001', 'wGCaT9IlgcyhxFeQP0rinRj2S6mfLpJ1', 'Independence Day', NULL, '2021-03-06', '2021-03-06', NULL, 'all', 'SYed3LuwXgKkh60P7Uzc2W41qNQHGyDr', 'on', NULL, NULL, '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-03 20:24:36', '0', '0', 'Held', '1'),
(5, 'TLIS0000001', '01c5f8GzLxVQjvSeCtOwXBYZ7W29HPFo', 'Independence Day Observed', NULL, '2021-03-08', '2021-03-08', NULL, 'all', 'SYed3LuwXgKkh60P7Uzc2W41qNQHGyDr', 'on', NULL, NULL, '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-03 20:24:57', '0', '0', 'Held', '1'),
(6, 'TLIS0000001', 'UXLgat5ZscWOAQKS7qwzfVMv8Gkrj4mp', 'Constitutional Day', NULL, '2021-01-07', '2021-01-07', NULL, 'all', 'SYed3LuwXgKkh60P7Uzc2W41qNQHGyDr', 'on', NULL, NULL, '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-03 20:26:35', '0', '0', 'Held', '1');

-- --------------------------------------------------------

--
-- Table structure for table `events_types`
--

DROP TABLE IF EXISTS `events_types`;
CREATE TABLE `events_types` (
  `id` int(11) NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `name` varchar(244) DEFAULT 'Public Holiday',
  `slug` varchar(64) DEFAULT 'public-holiday',
  `description` varchar(5000) NOT NULL DEFAULT 'This is the general category for all public holidays',
  `color_code` varchar(10) DEFAULT '#6777ef',
  `icon` varchar(244) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `events_types`
--

INSERT INTO `events_types` (`id`, `client_id`, `item_id`, `name`, `slug`, `description`, `color_code`, `icon`, `status`) VALUES
(1, 'TLIS0000001', 'SYed3LuwXgKkh60P7Uzc2W41qNQHGyDr', 'Public Holiday', 'public-holiday', 'This is the general category for all public holidays', '#9c27b0', NULL, '1'),
(2, 'MSGH000004', 'nwAShbU71vWTmpu0t453EQxaGYH6RlZO', 'Public Holiday', 'public-holiday', 'This is the general category for all public holidays', '#6777ef', NULL, '1');

-- --------------------------------------------------------

--
-- Table structure for table `e_learning`
--

DROP TABLE IF EXISTS `e_learning`;
CREATE TABLE `e_learning` (
  `id` int(11) NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `class_id` varchar(32) DEFAULT NULL,
  `allow_comments` enum('allow','disallow') NOT NULL DEFAULT 'allow',
  `allow_downloads` enum('0','1') NOT NULL DEFAULT '1',
  `course_id` varchar(32) DEFAULT NULL,
  `course_tutors` varchar(2000) DEFAULT NULL,
  `unit_id` varchar(32) DEFAULT NULL,
  `lesson_id` varchar(32) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `cover_image` varchar(255) DEFAULT NULL,
  `state` enum('Published','Draft') DEFAULT 'Published',
  `replies_count` varchar(12) NOT NULL DEFAULT '0',
  `comments_count` varchar(12) NOT NULL DEFAULT '0',
  `academic_year` varchar(32) DEFAULT NULL,
  `academic_term` varchar(32) DEFAULT NULL,
  `created_by` varchar(32) DEFAULT NULL,
  `updated_by` varchar(32) DEFAULT NULL,
  `updated_date` datetime NOT NULL DEFAULT current_timestamp(),
  `date_created` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `e_learning_comments`
--

DROP TABLE IF EXISTS `e_learning_comments`;
CREATE TABLE `e_learning_comments` (
  `id` int(11) NOT NULL,
  `type` enum('comment','reply') DEFAULT 'comment',
  `comment_id` varchar(5) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `record_id` varchar(120) DEFAULT NULL,
  `user_id` varchar(32) DEFAULT NULL,
  `ipaddress` varchar(244) DEFAULT NULL,
  `user_agent` varchar(244) DEFAULT NULL,
  `status` enum('0','1') DEFAULT '1',
  `likes` varchar(12) DEFAULT '0',
  `dislikes` varchar(12) NOT NULL DEFAULT '0',
  `date_created` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `e_learning_timer`
--

DROP TABLE IF EXISTS `e_learning_timer`;
CREATE TABLE `e_learning_timer` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` varchar(32) DEFAULT NULL,
  `video_id` varchar(120) DEFAULT NULL,
  `timer` varchar(12) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `e_learning_views`
--

DROP TABLE IF EXISTS `e_learning_views`;
CREATE TABLE `e_learning_views` (
  `id` int(11) UNSIGNED NOT NULL,
  `video_id` varchar(255) DEFAULT NULL,
  `views` varchar(15) DEFAULT '0',
  `views_array` text DEFAULT NULL,
  `comments` varchar(12) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `fees_allocations`
--

DROP TABLE IF EXISTS `fees_allocations`;
CREATE TABLE `fees_allocations` (
  `id` int(11) NOT NULL,
  `client_id` varchar(32) NOT NULL DEFAULT '1',
  `programme_id` int(11) UNSIGNED DEFAULT NULL,
  `class_id` int(11) UNSIGNED DEFAULT NULL,
  `category_id` int(11) UNSIGNED DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(32) DEFAULT NULL,
  `academic_year` varchar(25) NOT NULL DEFAULT '2019/2020',
  `academic_term` varchar(30) NOT NULL DEFAULT '1st',
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `fees_allocations`
--

INSERT INTO `fees_allocations` (`id`, `client_id`, `programme_id`, `class_id`, `category_id`, `amount`, `currency`, `academic_year`, `academic_term`, `status`, `date_created`, `created_by`) VALUES
(1, 'TLIS0000001', NULL, 1, 1, '650.00', 'GHS', '2020/2021', '1st', '1', '2021-05-01 17:05:13', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(2, 'TLIS0000001', NULL, 1, 2, '25.00', 'GHS', '2020/2021', '1st', '1', '2021-05-01 17:35:41', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(3, 'TLIS0000001', NULL, 1, 3, '30.00', 'GHS', '2020/2021', '1st', '1', '2021-05-01 17:35:48', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(4, 'TLIS0000001', NULL, 1, 4, '50.00', 'GHS', '2020/2021', '1st', '1', '2021-05-01 17:35:52', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(5, 'TLIS0000001', NULL, 1, 5, '250.00', 'GHS', '2020/2021', '1st', '1', '2021-05-01 17:35:56', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(6, 'TLIS0000001', NULL, 2, 1, '650.00', 'GHS', '2020/2021', '1st', '1', '2021-05-03 12:30:43', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(7, 'TLIS0000001', NULL, 2, 2, '25.00', 'GHS', '2020/2021', '1st', '1', '2021-05-03 12:30:49', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(8, 'TLIS0000001', NULL, 2, 3, '30.00', 'GHS', '2020/2021', '1st', '1', '2021-05-03 12:30:52', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(9, 'TLIS0000001', NULL, 2, 4, '50.00', 'GHS', '2020/2021', '1st', '1', '2021-05-03 12:30:56', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(10, 'TLIS0000001', NULL, 2, 5, '250.00', 'GHS', '2020/2021', '1st', '1', '2021-05-03 12:31:00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM');

-- --------------------------------------------------------

--
-- Table structure for table `fees_category`
--

DROP TABLE IF EXISTS `fees_category`;
CREATE TABLE `fees_category` (
  `id` int(11) NOT NULL,
  `client_id` varchar(32) NOT NULL DEFAULT '1',
  `name` varchar(255) DEFAULT NULL,
  `amount` varchar(32) DEFAULT NULL,
  `code` varchar(32) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `fees_category`
--

INSERT INTO `fees_category` (`id`, `client_id`, `name`, `amount`, `code`, `description`, `status`) VALUES
(1, 'TLIS0000001', 'Tuition Fees', '650', 'TUI', NULL, '1'),
(2, 'TLIS0000001', 'ICT Dues', '25', 'IT', NULL, '1'),
(3, 'TLIS0000001', 'Library Fees', '30', 'LIB', NULL, '1'),
(4, 'TLIS0000001', 'Project Fees', '50', 'PRO', NULL, '1'),
(5, 'TLIS0000001', 'Feeding Fees', '250', 'FF', NULL, '1');

-- --------------------------------------------------------

--
-- Table structure for table `fees_collection`
--

DROP TABLE IF EXISTS `fees_collection`;
CREATE TABLE `fees_collection` (
  `id` int(11) NOT NULL,
  `client_id` varchar(32) NOT NULL DEFAULT '1',
  `item_id` varchar(32) DEFAULT NULL,
  `receipt_id` varchar(32) DEFAULT NULL,
  `payment_id` varchar(32) DEFAULT NULL,
  `student_id` varchar(32) DEFAULT NULL,
  `department_id` int(10) UNSIGNED DEFAULT NULL,
  `programme_id` int(11) UNSIGNED DEFAULT NULL,
  `class_id` int(11) UNSIGNED DEFAULT NULL,
  `payment_method` enum('Cash','Cheque') NOT NULL DEFAULT 'Cash',
  `cheque_bank` varchar(255) DEFAULT NULL,
  `cheque_number` varchar(64) DEFAULT NULL,
  `cheque_security` varchar(64) DEFAULT NULL,
  `paidin_by` varchar(64) DEFAULT NULL,
  `paidin_contact` varchar(32) DEFAULT NULL,
  `currency` varchar(20) DEFAULT NULL,
  `category_id` int(11) UNSIGNED DEFAULT NULL,
  `amount` decimal(25,2) DEFAULT 0.00,
  `created_by` varchar(32) DEFAULT NULL,
  `recorded_date` datetime NOT NULL DEFAULT current_timestamp(),
  `description` text DEFAULT NULL,
  `academic_year` varchar(25) DEFAULT '2019/2020',
  `academic_term` varchar(25) DEFAULT '1st',
  `reversed` enum('0','1') DEFAULT '0',
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `fees_collection`
--

INSERT INTO `fees_collection` (`id`, `client_id`, `item_id`, `receipt_id`, `payment_id`, `student_id`, `department_id`, `programme_id`, `class_id`, `payment_method`, `cheque_bank`, `cheque_number`, `cheque_security`, `paidin_by`, `paidin_contact`, `currency`, `category_id`, `amount`, `created_by`, `recorded_date`, `description`, `academic_year`, `academic_term`, `reversed`, `status`) VALUES
(1, 'TLIS0000001', 'UAweEh3mnDVLp70icYt4oOxXkSF6qPzJ', NULL, NULL, 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 0, NULL, 1, 'Cash', NULL, NULL, NULL, NULL, NULL, 'GHS', 2, '25.00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-01 17:37:49', NULL, '2020/2021', '1st', '0', '1'),
(2, 'TLIS0000001', 'qjCsSpkN8mxKTDw4aYG52Zzb30OFLrhM', NULL, NULL, 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 0, NULL, 1, 'Cash', NULL, NULL, NULL, NULL, NULL, 'GHS', 1, '600.00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-01 17:38:17', NULL, '2020/2021', '1st', '0', '1'),
(3, 'TLIS0000001', '1wz9deBc2ponRXaNAQ7Fy5KDW8rm3Yhg', NULL, NULL, 'qkrNWbzA3EoZLaSleY5T4291ICQsmvdD', 0, NULL, 1, 'Cash', NULL, NULL, NULL, NULL, NULL, 'GHS', 1, '650.00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-01 17:40:54', NULL, '2020/2021', '1st', '0', '1'),
(4, 'TLIS0000001', 'DcexTLqhI0ObSyfAPM3wWBFo6dk1p5ji', NULL, NULL, 'qkrNWbzA3EoZLaSleY5T4291ICQsmvdD', 0, NULL, 1, 'Cash', NULL, NULL, NULL, NULL, NULL, 'GHS', 2, '25.00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-01 17:46:04', NULL, '2020/2021', '1st', '0', '1'),
(5, 'TLIS0000001', '3ZaUqz9JfIBNCtWDHvnYVAsgGwyKku6x', NULL, NULL, 'qkrNWbzA3EoZLaSleY5T4291ICQsmvdD', 0, NULL, 1, 'Cash', NULL, NULL, NULL, NULL, NULL, 'GHS', 3, '30.00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-01 17:46:46', NULL, '2020/2021', '1st', '0', '1'),
(6, 'TLIS0000001', 'YrCgtPJTBUM0GsoXS9Qyxfju5daO7ZD2', NULL, NULL, 'qkrNWbzA3EoZLaSleY5T4291ICQsmvdD', 0, NULL, 1, 'Cash', NULL, NULL, NULL, NULL, NULL, 'GHS', 4, '50.00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-01 17:47:42', NULL, '2020/2021', '1st', '0', '1'),
(7, 'TLIS0000001', 'kHSNId0tBL3poP6vA17OuFJTyli5Qgaq', NULL, NULL, 'qkrNWbzA3EoZLaSleY5T4291ICQsmvdD', 0, NULL, 1, 'Cash', NULL, NULL, NULL, NULL, NULL, 'GHS', 5, '250.00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-01 17:48:10', NULL, '2020/2021', '1st', '0', '1'),
(8, 'TLIS0000001', 'FzTLvXAqHxRuJo7ZwP0nQdr1pDK4OU5f', NULL, NULL, '1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', 0, NULL, 1, 'Cash', NULL, NULL, NULL, NULL, NULL, 'GHS', 5, '250.00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-01 17:48:31', NULL, '2020/2021', '1st', '0', '1'),
(9, 'TLIS0000001', 'TVnHDzhoSgIrcdQx7Fjv0Z1MKAEbq3UP', NULL, NULL, '1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', 0, NULL, 1, 'Cash', NULL, NULL, NULL, NULL, NULL, 'GHS', 3, '20.00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-01 17:48:45', NULL, '2020/2021', '1st', '0', '1'),
(10, 'TLIS0000001', 'Fgo8jPQIRiWNVmcha974C15d32E6TGUL', NULL, NULL, '1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', 0, NULL, 1, 'Cash', NULL, NULL, NULL, NULL, NULL, 'GHS', 1, '650.00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-01 17:53:22', NULL, '2020/2021', '1st', '0', '1'),
(11, 'TLIS0000001', '2L0aJrZNRXDI6P1yQ7qKbCWvce4dMYwS', NULL, NULL, '1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', 0, NULL, 1, 'Cash', NULL, NULL, NULL, NULL, NULL, 'GHS', 4, '20.00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-01 17:53:42', NULL, '2020/2021', '1st', '0', '1'),
(12, 'TLIS0000001', 'xHXUYzLREdQlAhDGkuZ1JFBO68NM9r0S', NULL, NULL, '1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', 0, NULL, 1, 'Cash', NULL, NULL, NULL, NULL, NULL, 'GHS', 2, '25.00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-01 17:53:57', NULL, '2020/2021', '1st', '0', '1'),
(13, 'TLIS0000001', 'uZEqtcS1ijDOeJdk54Ys7F9l0H82vhNz', NULL, NULL, 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 0, NULL, 1, 'Cash', NULL, NULL, NULL, NULL, NULL, 'GHS', 1, '50.00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-01 18:53:49', NULL, '2020/2021', '1st', '0', '1'),
(14, 'TLIS0000001', 'tR315zfY80DeuFWElwAqrnbHmdIk9Vys', NULL, NULL, 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 0, NULL, 1, 'Cash', NULL, NULL, NULL, NULL, NULL, 'GHS', 3, '30.00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-01 18:54:03', NULL, '2020/2021', '1st', '0', '1'),
(15, 'TLIS0000001', 'Uc1IeT6HvqrO4Dytx2XLkWaENZVGuC7w', NULL, NULL, 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 0, NULL, 1, 'Cash', NULL, NULL, NULL, NULL, NULL, 'GHS', 4, '50.00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-01 18:54:12', NULL, '2020/2021', '1st', '0', '1'),
(16, 'TLIS0000001', 'PlCAM4q5Fj3G6utVKy7Qve0Hrg2mUnBh', '00016', NULL, 'aSzAlD7uPObpda2jKtE0qhWUV8igTo9a', 0, NULL, 2, 'Cash', NULL, NULL, NULL, NULL, NULL, 'GHS', 1, '350.00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-03 12:31:29', NULL, '2020/2021', '1st', '0', '1'),
(17, 'TLIS0000001', '7peCHbf4v21jSqsMWgdrnT86OZLPhKNV', 'REL00017', NULL, 'aSzAlD7uPObpda2jKtE0qhWUV8igTo9a', 0, NULL, 2, 'Cash', NULL, NULL, NULL, NULL, NULL, 'GHS', 1, '300.00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-03 12:43:36', NULL, '2020/2021', '1st', '0', '1'),
(18, 'TLIS0000001', 'T1j8wmEvSPfBV2ueZQrFNC0GXMAYO6Iy', 'REL00018', NULL, 'aSzAlD7uPObpda2jKtE0qhWUV8igTo9a', 0, NULL, 2, 'Cash', NULL, NULL, NULL, NULL, NULL, 'GHS', 2, '25.00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-03 12:44:05', 'This is the full payment for the student ict dues', '2020/2021', '1st', '0', '1'),
(19, 'TLIS0000001', 'WervgpBHOqPkRFwUd1VJ6NhisTl43EYy', 'REL00019', NULL, 'T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK', 0, NULL, 1, 'Cheque', 'Fidelity Bank Ghana Limited::10', '0092021', NULL, NULL, NULL, 'GHS', 1, '300.00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-03 17:03:02', NULL, '2020/2021', '1st', '0', '1'),
(20, 'TLIS0000001', 'lU1qMQ5JVva2yhPHngKtDTLmxbCo8IEF', 'REL00020', NULL, 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9a', 0, NULL, 2, 'Cash', NULL, NULL, NULL, NULL, NULL, 'GHS', 1, '500.00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-03 22:13:47', NULL, '2020/2021', '1st', '0', '1'),
(21, 'TLIS0000001', 'cMKzHlQWirkLVb9OahRsSPTw6DyetJdG', 'REL00021', NULL, 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9a', 0, NULL, 2, 'Cash', NULL, NULL, NULL, NULL, NULL, 'GHS', 1, '50.00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-03 22:20:01', NULL, '2020/2021', '1st', '0', '1'),
(22, 'TLIS0000001', 'mrqNO8AbfnYSkZJVDwQ1oicj9WXCh4TF', 'REL00022', NULL, 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9a', 0, NULL, 2, 'Cash', NULL, NULL, NULL, NULL, NULL, 'GHS', 1, '50.00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-03 22:24:34', 'This is another payment check', '2020/2021', '1st', '0', '1'),
(23, 'TLIS0000001', 'cxQmr0Z1eJY2D8vBubtsS4HaL9V6pzif', 'REL00023', NULL, 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9a', 0, NULL, 2, 'Cash', NULL, NULL, NULL, NULL, NULL, 'GHS', 2, '25.00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-03 23:15:34', NULL, '2020/2021', '1st', '0', '1'),
(24, 'TLIS0000001', '2pfchrozeNGQ4jwVBR7xkYEubOP3Im8U', 'REL00024', NULL, 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9a', 0, NULL, 2, 'Cheque', 'Absa Bank Ghana Limited::2', '0092839', NULL, NULL, NULL, 'GHS', 3, '30.00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-03 23:18:30', NULL, '2020/2021', '1st', '0', '1'),
(25, 'TLIS0000001', '5FmSoD1rT9ZGc3OV8NR02wKtYipj6gbE', 'REL00025', NULL, 'aSzAlD7uPObpda2jKtE0qhWUV8igTo9a', 0, NULL, 2, 'Cheque', 'Agricultural Development Bank Limited::4', '000293', NULL, NULL, NULL, 'GHS', 3, '30.00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-03 23:21:38', NULL, '2020/2021', '1st', '0', '1'),
(26, 'TLIS0000001', 'pXgH0eWLxAZvGmNRMrPSCyJc6as8QVnD', 'REL00026', NULL, 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9a', 0, NULL, 2, 'Cash', NULL, NULL, NULL, NULL, NULL, 'GHS', 4, '50.00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-03 23:23:31', NULL, '2020/2021', '1st', '0', '1'),
(27, 'TLIS0000001', 'XJpPenYTzOQiaHy5bVrALqSjwC9kZgd1', 'REL00027', NULL, 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9a', 0, NULL, 2, 'Cash', NULL, NULL, NULL, NULL, NULL, 'GHS', 1, '50.00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-03 23:24:46', NULL, '2020/2021', '1st', '0', '1'),
(28, 'TLIS0000001', 'Mpil76KgEkxPYBJdLrTSaQfce4DbHn3h', 'REL00028', NULL, 'T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK', 0, NULL, 1, 'Cheque', 'Standard Chartered Bank (Ghana) Limited::21', '655142', '', NULL, NULL, 'GHS', 2, '25.00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-11 11:38:06', 'This is the full paid for the ICT Dues Owed by the User', '2020/2021', '1st', '0', '1'),
(29, 'TLIS0000001', 'EXZwAaJcM7r5iqO', 'REL00029', NULL, 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9a', 0, NULL, 2, 'Cash', NULL, NULL, NULL, NULL, NULL, 'GHS', 5, '10.00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-12 05:07:40', 'This is the test payment', '2020/2021', '1st', '0', '1'),
(30, 'TLIS0000001', 'dGD9NlZcBf4syWt', 'REL00030', NULL, 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9a', 0, NULL, 2, 'Cash', NULL, NULL, NULL, NULL, NULL, 'GHS', 5, '70.00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-12 05:19:26', 'This is the next payment test that i am parsing into the database.', '2020/2021', '1st', '0', '1'),
(31, 'TLIS0000001', 'kiQM4X9EVmPA0e8', 'REL00031', NULL, 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9a', 0, NULL, 2, 'Cash', NULL, NULL, NULL, NULL, NULL, 'GHS', 5, '80.00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-12 05:21:27', 'Final Test to ensure i have the best result as expected', '2020/2021', '1st', '0', '1'),
(32, 'TLIS0000001', 'ilQ3x0yrdYh6tTH', 'REL00032', NULL, 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9a', 0, NULL, 2, 'Cheque', 'Agricultural Development Bank Limited::4', '009029', '', NULL, NULL, 'GHS', 5, '90.00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-12 05:28:20', 'This is the final payment for the student fees', '2020/2021', '1st', '0', '1');

-- --------------------------------------------------------

--
-- Table structure for table `fees_collection_banks`
--

DROP TABLE IF EXISTS `fees_collection_banks`;
CREATE TABLE `fees_collection_banks` (
  `id` int(11) NOT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `fees_collection_banks`
--

INSERT INTO `fees_collection_banks` (`id`, `bank_name`, `address`, `phone_number`, `website`, `email`, `logo`) VALUES
(2, 'Absa Bank Ghana Limited', 'Absa House J.E. Atta-Mills High Street Accra P.O. Box GP2949, Accra', '233-302-429150', 'https://www.absa.com.gh', 'service.excellence@absa.africa', NULL),
(3, 'Access Bank (Ghana) Plc', 'Starlets’ 91 Road, Opposite Accra Sports Stadium, Osu P. O. Box GP 353, Accra, Ghana', '233-302-661769, 233-302-684858, 233-302-661613', 'http://www.ghana.accessbankplc.com', 'info@ghana.accessbankplc.com', NULL),
(4, 'Agricultural Development Bank Limited', 'Accra Financial Centre, 3rd Ambassadorial Development Area, Ridge-Accra, P.O. Box 4191, Accra-Ghana', '233-302-770403, 233-302-762104, 233-302-783123,233-302-784394,233-28-9255880,233-28-9225881', 'http://www.agricbank.com', 'customercare@agricbank.com', NULL),
(5, 'Bank of Africa Ghana Limited', 'Head Office,1st Floor, Block A&B,The Octagon, Independence Avenue P.O. Box C1541 Cantonments, Accra – Ghana', '233-302-249690,233-302-249679/83/98,233-302-249683,233-302-249683,233-302-249698', 'http://www.boaghana.com', 'complaints@boaghana.com', NULL),
(6, 'CalBank PLC', 'Head Office 45 Independence Ave P.O. Box 14596 Accra – Ghana', '233-302-680061, 233-302-680069', 'http://www.calbank.net', 'customercare@calbank.net', NULL),
(7, 'Consolidated Bank Ghana Limited', 'First Floor, Manet Tower 3, Airport City, Accra PMB CT363 ,Cantonments, Accra', '233 302-634330, 233 302-634359, 233 302-216000', 'http://www.cbg.com.gh', 'info@cbg.com.gh', NULL),
(8, 'Ecobank Ghana Limited', '2 Morocco Lane, Off Independence Avenue, P.O. Box: P.O. Box AN 16746, Accra North – Ghana', '233 302-681146/8, 233 302-213999', 'http://www.ecobank.com', 'ecobankenquiries@ecobank.com', NULL),
(9, 'FBNBank (Ghana) Limited', 'Head Office,Plot No. 678, Liberation Road, Airport, Accra, PMB No. 16, Accra North, Ghana', '233-302-236136/235684, 233-302-238510, 233-302-235684, 233-302-236136, 233-302-235819', 'http://www.fbnbankghana.com', 'fbn@fbnbankghana.com', NULL),
(10, 'Fidelity Bank Ghana Limited', 'Ridge Towers – Ridge, Accra. PMB 43, Cantonments, Accra, Ghana.', '233-302-214490', 'http://www.fidelitybank.com.gh', 'wecare@myfidelitybank.net', NULL),
(11, 'First Atlantic Bank Limited', '233 -302-68 2203, 233-302-68 0825', '233 -302-68 2203, 233-302-68 0825', 'http://www.firstatlanticbank.com.gh', 'Info@firstatlanticbank.com.gh', NULL),
(12, 'First National Bank (Ghana) Limited', 'Head Office, 6th Floor, Accra Financial Centre, Cnr. Independence Ave./Liberation Road, P.O. Box TU 23, Accra-Ghana', '233-302-242435050', 'http://www.firstnationalbank.com.gh', 'info@firstnationalbank.com.gh', NULL),
(13, 'GCB Bank Limited', 'Head Office, High Street , 2 Thorpe Road, P.O. Box 134, Accra', '233-302-672852, 233-302-664918, 233-302-663964, 233-302-672852-4, 233-302-672859, 233-302-672865, 233-302-663480, 233-302-664910', 'http://www.gcbbank.com.gh', 'corporateaffairs@gcb.com.gh', NULL),
(14, 'Guaranty Trust Bank (Ghana) Limited', '25A, Castle Road, Ambassadorial Enclave, Ambassadorial Enclave, Ridge, PM.B CT 416, Accra – Ghana', '233-302 - 680668, 233-302 - 676462, 233-302 - 687751, 233-302 - 680662, 233-302 - 680746, 233-302 - 676681, 233-302 - 201027, 233-303 - 201048, 233-302 - 816621-3', 'http://www.gtbghana.com', 'gh.corporateaffairs@gtbank.com', NULL),
(15, 'National Investment Bank Limited', 'Head Office, 37 Kwame Nkrumah Avenue, P.O. Box 3726, Accra, Ghana', '233-302-661701', 'http://www.nib-ghana.com', 'info@nib-ghana.com', NULL),
(16, 'OmniBSIC Bank Ghana Limited', 'C9/14 Dzorwulu, Olusegun Way, Opposite Allied Oil Filling Station, P.O. Box KN 5569,  Kaneshie, Accra', '233-307-086000', 'http://www.omnibank.com.gh', 'info@omnibank.com.gh', NULL),
(17, 'Prudential Bank Limited', 'Head Office, Ring Road Central, PMB - General Post Office, Accra – Ghana', '233-302-781200-7', 'http://www.prudentialbank.com.gh', 'headoffice@prudentialbank.com.gh', NULL),
(18, 'Republic Bank (Ghana) PLC', 'Head Office, Ebankese No. 35, Sixth Avenue, North Ridge, Accra – Ghana, P.O Box CT 4603, Cantonments,Accra – Ghana', '233-302-242090-2, 233-302-242090-4', 'http://www.republicghana.com', 'email@republicghana.com', NULL),
(19, 'Societe General (Ghana) Limited', 'Head Office, P. O. Box 13119, Ring Road Central Accra, Accra - Ghana', '233-302-202001, 233-302-248920, 233-577606464', 'http://www.societegenerale.com.gh', 'sgghana.info@socgen.com', NULL),
(20, 'Stanbic Bank Ghana Limited', 'Head Office, Stanbic Heights, 25 Liberation Link, Airport City, P.O. Box CT 2344, Cantonments, Accra-Ghana', '233-302-687670-8, 233-302-687671, 233-302-687672, 233-302-687673-9', 'http://www.stanbicbank.com.gh', 'customercare@stanbic.com.gh', NULL),
(21, 'Standard Chartered Bank (Ghana) Limited', 'Head Office, No. 87 Independence Avenue, P.O. Box 768, Accra', '233-302-664591-8, 233-302-740100', 'http://www.sc.com/gh', 'feedback.ghana@sc.com', NULL),
(22, 'United Bank for Africa (Ghana) Limited', 'PMB 29, Ministries, Heritage Towers, Ambassadorial Enclave, Off Liberia Road, Ridge, Accra-Ghana', '233-302- 674085, 233-302-674089, 233-302-674056', 'http://www.ubagroup.com', '', NULL),
(23, 'Universal Merchant Bank Limited', 'Airport City, SSNIT Emporium, P.O. Box 401, North Ridge, Accra-Ghana', '233-302- 666331/6', 'http://www.myumbbank.com', 'info@myumbbank.com', NULL),
(24, 'Zenith Bank (Ghana) Limited', 'Head Office, Zenith Heights, No. 31 Independence Avenue, PMB CT 393, Cantonments, Accra', '233-302-660075, 233-302-611500, 233-302-660079, 233-302-660091, 233-302-660093, 233-302-660095', 'http://www.zenithbank.com.gh', 'info@zenithbank.com.gh', NULL),
(25, 'Bank of Ghana', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `fees_payments`
--

DROP TABLE IF EXISTS `fees_payments`;
CREATE TABLE `fees_payments` (
  `id` int(11) NOT NULL,
  `client_id` varchar(32) NOT NULL DEFAULT '1',
  `checkout_url` varchar(32) DEFAULT NULL,
  `student_id` varchar(32) DEFAULT NULL,
  `class_id` varchar(32) DEFAULT NULL,
  `category_id` int(11) UNSIGNED DEFAULT NULL,
  `currency` varchar(32) DEFAULT NULL,
  `amount_due` decimal(10,2) NOT NULL DEFAULT 0.00,
  `amount_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `academic_year` varchar(25) DEFAULT '2019/2020',
  `academic_term` varchar(25) DEFAULT '1st',
  `paid_status` enum('0','1','2') NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `last_payment_date` datetime DEFAULT NULL,
  `last_payment_id` varchar(32) DEFAULT NULL,
  `created_by` varchar(32) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `fees_payments`
--

INSERT INTO `fees_payments` (`id`, `client_id`, `checkout_url`, `student_id`, `class_id`, `category_id`, `currency`, `amount_due`, `amount_paid`, `balance`, `academic_year`, `academic_term`, `paid_status`, `date_created`, `last_payment_date`, `last_payment_id`, `created_by`, `status`) VALUES
(1, 'TLIS0000001', 'JNMB5b8OaUdEAT0W9Hiy7hre4z2gSPcl', '1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', '1', 1, 'GHS', '650.00', '650.00', '0.00', '2020/2021', '1st', '1', '2021-05-01 17:05:13', '2021-05-01 17:53:22', 'Fgo8jPQIRiWNVmcha974C15d32E6TGUL', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '1'),
(2, 'TLIS0000001', '1NKO2F4b7iakzMJVsGmThQvxS0B6PRjl', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', '1', 1, 'GHS', '650.00', '650.00', '0.00', '2020/2021', '1st', '1', '2021-05-01 17:05:13', '2021-05-01 18:53:49', 'uZEqtcS1ijDOeJdk54Ys7F9l0H82vhNz', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '1'),
(3, 'TLIS0000001', 'ANVs0r7WtK8k5IqUGhifL312bwTMSa4z', 'T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK', '1', 1, 'GHS', '650.00', '300.00', '350.00', '2020/2021', '1st', '2', '2021-05-01 17:05:13', '2021-05-03 17:03:02', 'WervgpBHOqPkRFwUd1VJ6NhisTl43EYy', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '1'),
(4, 'TLIS0000001', 'poFK7ZXTM9xyS4V2riWUE1BLtHe8df0h', 'qkrNWbzA3EoZLaSleY5T4291ICQsmvdD', '1', 1, 'GHS', '650.00', '650.00', '0.00', '2020/2021', '1st', '1', '2021-05-01 17:05:13', '2021-05-01 17:40:54', '1wz9deBc2ponRXaNAQ7Fy5KDW8rm3Yhg', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '1'),
(5, 'TLIS0000001', '8KPG0gBRAa2fjuLk1S5UZNrhHqbxez3v', '1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', '1', 2, 'GHS', '25.00', '25.00', '0.00', '2020/2021', '1st', '1', '2021-05-01 17:35:42', '2021-05-01 17:53:57', 'xHXUYzLREdQlAhDGkuZ1JFBO68NM9r0S', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '1'),
(6, 'TLIS0000001', '4xmnf8kpQvz5jSNF6IyCWilh1r2udbH7', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', '1', 2, 'GHS', '25.00', '25.00', '0.00', '2020/2021', '1st', '1', '2021-05-01 17:35:42', '2021-05-01 17:37:49', 'UAweEh3mnDVLp70icYt4oOxXkSF6qPzJ', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '1'),
(7, 'TLIS0000001', '3aj8WoYlCXApyfJLRs1UiPDZ6kIMuHvz', 'T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK', '1', 2, 'GHS', '25.00', '25.00', '0.00', '2020/2021', '1st', '1', '2021-05-01 17:35:42', '2021-05-11 11:38:07', 'Mpil76KgEkxPYBJdLrTSaQfce4DbHn3h', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '1'),
(8, 'TLIS0000001', 'KMNkXjTSYZbG584wAD0CQxqhvUuJrstn', 'qkrNWbzA3EoZLaSleY5T4291ICQsmvdD', '1', 2, 'GHS', '25.00', '25.00', '0.00', '2020/2021', '1st', '1', '2021-05-01 17:35:42', '2021-05-01 17:46:04', 'DcexTLqhI0ObSyfAPM3wWBFo6dk1p5ji', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '1'),
(9, 'TLIS0000001', 'xOgoTPUGqM1WiXYk8hcfs7dN2aFv9Dyt', '1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', '1', 3, 'GHS', '30.00', '20.00', '10.00', '2020/2021', '1st', '2', '2021-05-01 17:35:48', '2021-05-01 17:48:45', 'TVnHDzhoSgIrcdQx7Fjv0Z1MKAEbq3UP', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '1'),
(10, 'TLIS0000001', 'fhxabjoyIKD1C3LgX2wUdnJV0BT9RmWt', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', '1', 3, 'GHS', '30.00', '30.00', '0.00', '2020/2021', '1st', '1', '2021-05-01 17:35:48', '2021-05-01 18:54:03', 'tR315zfY80DeuFWElwAqrnbHmdIk9Vys', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '1'),
(11, 'TLIS0000001', '0fTKcBM2d8notm7L6zekp4iFCjHuUaRq', 'T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK', '1', 3, 'GHS', '30.00', '0.00', '30.00', '2020/2021', '1st', '0', '2021-05-01 17:35:48', NULL, NULL, 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '1'),
(12, 'TLIS0000001', 'r7SPIRThcMXi6L0wVK93QNGxYCq8vkyH', 'qkrNWbzA3EoZLaSleY5T4291ICQsmvdD', '1', 3, 'GHS', '30.00', '30.00', '0.00', '2020/2021', '1st', '1', '2021-05-01 17:35:48', '2021-05-01 17:46:46', '3ZaUqz9JfIBNCtWDHvnYVAsgGwyKku6x', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '1'),
(13, 'TLIS0000001', 'Q9MsTXLY3mWOlnD27CAZHar5x6RGbfvd', '1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', '1', 4, 'GHS', '50.00', '20.00', '30.00', '2020/2021', '1st', '1', '2021-05-01 17:35:52', '2021-05-01 17:53:42', '2L0aJrZNRXDI6P1yQ7qKbCWvce4dMYwS', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '1'),
(14, 'TLIS0000001', '2KVsRjaYE6FNyufQp4ZHnq53hTMlokiW', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', '1', 4, 'GHS', '50.00', '50.00', '0.00', '2020/2021', '1st', '1', '2021-05-01 17:35:52', '2021-05-01 18:54:12', 'Uc1IeT6HvqrO4Dytx2XLkWaENZVGuC7w', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '1'),
(15, 'TLIS0000001', 'agd9pfh7XWcUkr32MAomNuKjJF0vqiRw', 'T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK', '1', 4, 'GHS', '50.00', '0.00', '50.00', '2020/2021', '1st', '0', '2021-05-01 17:35:52', NULL, NULL, 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '1'),
(16, 'TLIS0000001', 'QCc3yn6eNutSsZ1LFBUXAx94MVHIEphk', 'qkrNWbzA3EoZLaSleY5T4291ICQsmvdD', '1', 4, 'GHS', '50.00', '50.00', '0.00', '2020/2021', '1st', '1', '2021-05-01 17:35:52', '2021-05-01 17:47:42', 'YrCgtPJTBUM0GsoXS9Qyxfju5daO7ZD2', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '1'),
(17, 'TLIS0000001', 'BzrDqvUMC0hHfsuY4cgZnwFXk7xWtl56', '1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', '1', 5, 'GHS', '250.00', '250.00', '0.00', '2020/2021', '1st', '1', '2021-05-01 17:35:56', '2021-05-01 17:48:31', 'FzTLvXAqHxRuJo7ZwP0nQdr1pDK4OU5f', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '1'),
(18, 'TLIS0000001', 'eErG2a3tXD0HzhgF8PySsJx9KjdVTWkc', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', '1', 5, 'GHS', '250.00', '0.00', '250.00', '2020/2021', '1st', '0', '2021-05-01 17:35:56', NULL, NULL, 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '1'),
(19, 'TLIS0000001', 'ypQCGAUmuefgBh31ojiO56VlJaxRtkZM', 'T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK', '1', 5, 'GHS', '250.00', '0.00', '250.00', '2020/2021', '1st', '0', '2021-05-01 17:35:56', NULL, NULL, 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '1'),
(20, 'TLIS0000001', 'EZbRWGF2jde8QM9tB4a70HfJqwun6zTV', 'qkrNWbzA3EoZLaSleY5T4291ICQsmvdD', '1', 5, 'GHS', '250.00', '250.00', '0.00', '2020/2021', '1st', '1', '2021-05-01 17:35:56', '2021-05-01 17:48:10', 'kHSNId0tBL3poP6vA17OuFJTyli5Qgaq', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '1'),
(21, 'TLIS0000001', 'xXiZUrDslmP0vF7fkny9hbVICLJqtKjd', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9a', '2', 1, 'GHS', '650.00', '650.00', '0.00', '2020/2021', '1st', '1', '2021-05-03 12:30:44', '2021-05-03 23:24:46', 'XJpPenYTzOQiaHy5bVrALqSjwC9kZgd1', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '1'),
(22, 'TLIS0000001', 'hYqFrSR5LHzde0fVK3WilgaPjICuAZ81', 'aSzAlD7uPObpda2jKtE0qhWUV8igTo9a', '2', 1, 'GHS', '650.00', '650.00', '0.00', '2020/2021', '1st', '1', '2021-05-03 12:30:44', '2021-05-03 12:43:36', '7peCHbf4v21jSqsMWgdrnT86OZLPhKNV', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '1'),
(23, 'TLIS0000001', 'yZCNG8zuIYxVfKoPr71Hg5DEbt62edAp', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9a', '2', 2, 'GHS', '25.00', '25.00', '0.00', '2020/2021', '1st', '1', '2021-05-03 12:30:49', '2021-05-03 23:15:34', 'cxQmr0Z1eJY2D8vBubtsS4HaL9V6pzif', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '1'),
(24, 'TLIS0000001', 'X836ySvCMnfKoYPd2Q59DmIup0eghtzA', 'aSzAlD7uPObpda2jKtE0qhWUV8igTo9a', '2', 2, 'GHS', '25.00', '25.00', '0.00', '2020/2021', '1st', '1', '2021-05-03 12:30:49', '2021-05-03 12:44:05', 'T1j8wmEvSPfBV2ueZQrFNC0GXMAYO6Iy', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '1'),
(25, 'TLIS0000001', 'iFy7AaqCnmTk3fIRxb8zvSpJhXule2Bt', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9a', '2', 3, 'GHS', '30.00', '30.00', '0.00', '2020/2021', '1st', '1', '2021-05-03 12:30:52', '2021-05-03 23:18:30', '2pfchrozeNGQ4jwVBR7xkYEubOP3Im8U', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '1'),
(26, 'TLIS0000001', 'tEfWagUKR2x4ownVHjQ5dFIcsBG6XA71', 'aSzAlD7uPObpda2jKtE0qhWUV8igTo9a', '2', 3, 'GHS', '30.00', '30.00', '0.00', '2020/2021', '1st', '1', '2021-05-03 12:30:52', '2021-05-03 23:21:38', '5FmSoD1rT9ZGc3OV8NR02wKtYipj6gbE', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '1'),
(27, 'TLIS0000001', '6kOWRdgPJsA9Yepb81cNhMFt3C5QwiaH', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9a', '2', 4, 'GHS', '50.00', '50.00', '0.00', '2020/2021', '1st', '1', '2021-05-03 12:30:56', '2021-05-03 23:23:31', 'pXgH0eWLxAZvGmNRMrPSCyJc6as8QVnD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '1'),
(28, 'TLIS0000001', 'r9TYkC5zxwUVIbFPSg7dGc8pN1JyKOqH', 'aSzAlD7uPObpda2jKtE0qhWUV8igTo9a', '2', 4, 'GHS', '50.00', '0.00', '50.00', '2020/2021', '1st', '0', '2021-05-03 12:30:56', NULL, NULL, 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '1'),
(29, 'TLIS0000001', 'bfoHtlVKavzrXAnp3G1INZ6sFDOPgkj2', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9a', '2', 5, 'GHS', '250.00', '250.00', '0.00', '2020/2021', '1st', '1', '2021-05-03 12:31:00', '2021-05-12 05:28:20', 'ilQ3x0yrdYh6tTH', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '1'),
(30, 'TLIS0000001', 'aY6485iXZQWsEOtdPqn3ypRkrIfgJBCV', 'aSzAlD7uPObpda2jKtE0qhWUV8igTo9a', '2', 5, 'GHS', '250.00', '0.00', '250.00', '2020/2021', '1st', '0', '2021-05-03 12:31:00', NULL, NULL, 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '1');

-- --------------------------------------------------------

--
-- Table structure for table `files_attachment`
--

DROP TABLE IF EXISTS `files_attachment`;
CREATE TABLE `files_attachment` (
  `id` int(12) UNSIGNED NOT NULL,
  `resource` varchar(32) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `attachment_size` varchar(16) DEFAULT NULL,
  `record_id` varchar(80) DEFAULT NULL,
  `resource_id` varchar(66) DEFAULT NULL,
  `created_by` varchar(32) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `grading_system`
--

DROP TABLE IF EXISTS `grading_system`;
CREATE TABLE `grading_system` (
  `id` int(11) NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `grading` text DEFAULT NULL,
  `structure` text DEFAULT NULL,
  `show_position` varchar(8) DEFAULT 'true',
  `allow_submission` varchar(12) NOT NULL DEFAULT '''true''',
  `show_teacher_name` varchar(8) NOT NULL DEFAULT 'false'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `grading_system`
--

INSERT INTO `grading_system` (`id`, `client_id`, `grading`, `structure`, `show_position`, `allow_submission`, `show_teacher_name`) VALUES
(1, 'TLIS0000001', '{\"1\":{\"start\":\"90\",\"end\":\"100\",\"interpretation\":\"Excellent\\/Outstanding\"},\"2\":{\"start\":\"80\",\"end\":\"89\",\"interpretation\":\"Very Good\"},\"3\":{\"start\":\"70\",\"end\":\"79\",\"interpretation\":\"Good\"},\"4\":{\"start\":\"60\",\"end\":\"69\",\"interpretation\":\"Fairly Good\\/Pass\"},\"5\":{\"start\":\"50\",\"end\":\"59\",\"interpretation\":\"Needs Improvement\"},\"6\":{\"start\":\"0\",\"end\":\"49\",\"interpretation\":\"Needs Booster Support\"}}', '{\"course_title\":\"true\",\"columns\":{\"Class Score\":\"30\",\"Exams Score\":\"70\",\"Total Score\":\"100\"},\"average_score\":\"true\",\"show_position\":\"true\",\"show_teacher_name\":\"true\",\"allow_submission\":\"true\",\"teacher_comments\":\"true\"}', 'true', 'true', 'true'),
(2, 'MSIS0000002', '{\"1\":{\"start\":\"90\",\"end\":\"100\",\"interpretation\":\"Excellent\"},\"2\":{\"start\":\"80\",\"end\":\"89\",\"interpretation\":\"Very Good\"}}', '{\"course_title\":\"true\",\"columns\":{\"Class Score\":\"30\",\"Exams Score\":\"70\",\"Total Grades\":\"100\"},\"average_score\":\"true\",\"show_position\":\"true\",\"show_teacher_name\":\"true\",\"allow_submission\":\"true\",\"teacher_comments\":\"true\"}', 'true', 'true', 'true');

-- --------------------------------------------------------

--
-- Table structure for table `grading_terminal_logs`
--

DROP TABLE IF EXISTS `grading_terminal_logs`;
CREATE TABLE `grading_terminal_logs` (
  `id` int(11) NOT NULL,
  `report_id` varchar(32) DEFAULT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `class_id` varchar(32) DEFAULT NULL,
  `course_id` varchar(32) DEFAULT NULL,
  `course_name` varchar(255) DEFAULT NULL,
  `course_code` varchar(32) DEFAULT NULL,
  `class_name` varchar(255) DEFAULT NULL,
  `academic_year` varchar(32) DEFAULT NULL,
  `academic_term` varchar(32) DEFAULT NULL,
  `teacher_ids` varchar(2000) DEFAULT NULL,
  `teachers_name` varchar(2000) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_modified` datetime DEFAULT current_timestamp(),
  `created_by` varchar(32) DEFAULT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `grading_terminal_logs`
--

INSERT INTO `grading_terminal_logs` (`id`, `report_id`, `client_id`, `class_id`, `course_id`, `course_name`, `course_code`, `class_name`, `academic_year`, `academic_term`, `teacher_ids`, `teachers_name`, `date_created`, `date_modified`, `created_by`, `status`) VALUES
(1, '7pcUdSFJ2au8', 'TLIS0000001', 'B0wVXoJq8cLnaeOWC4EvkRzmhiHSlMry', 'XUuG1qdL6SVFyMfCQkP2s5vcpxmgtTHj', 'Basics Of Programming', 'C002', 'JHS 1', '2020/2021', '1st', 'P00001', 'fredrick amponsah badu', '2021-02-24 06:12:15', '2021-03-17 11:39:07', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Approved'),
(3, '1DTUACe7nhq0', 'TLIS0000001', 'B0wVXoJq8cLnaeOWC4EvkRzmhiHSlMry', 'BAybmg6is1Ff7ruWveIxhdNYXESJH38U', 'Introduction To Jquery', 'C001', 'JHS 1', '2020/2021', '1st', 'P00001', 'fredrick amponsah badu', '2021-02-24 12:15:36', '2021-03-17 11:34:14', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Approved'),
(6, '7AC2NXYLFPRTBO31', 'TLIS0000001', 'B0wVXoJq8cLnaeOWC4EvkRzmhiHSlMry', '5sLO0GR2qYThuQK39CzNc1gy4xlHv8PJ', 'Object Oriented Programming', 'C003', 'JHS 1', '2020/2021', '1st', NULL, NULL, '2021-04-17 14:33:56', '2021-04-17 14:33:56', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Approved');

-- --------------------------------------------------------

--
-- Table structure for table `grading_terminal_scores`
--

DROP TABLE IF EXISTS `grading_terminal_scores`;
CREATE TABLE `grading_terminal_scores` (
  `id` int(11) NOT NULL,
  `report_id` varchar(20) DEFAULT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `student_unique_id` varchar(32) DEFAULT NULL,
  `student_item_id` varchar(32) DEFAULT NULL,
  `student_name` varchar(255) DEFAULT NULL,
  `course_id` varchar(32) DEFAULT NULL,
  `course_name` varchar(255) DEFAULT NULL,
  `course_code` varchar(32) DEFAULT NULL,
  `class_id` varchar(32) DEFAULT NULL,
  `class_name` varchar(255) DEFAULT NULL,
  `scores` text DEFAULT NULL,
  `total_score` int(5) UNSIGNED DEFAULT NULL,
  `average_score` varchar(32) DEFAULT NULL,
  `class_position` varchar(32) DEFAULT NULL,
  `teacher_ids` varchar(500) DEFAULT NULL,
  `teachers_name` text DEFAULT NULL,
  `class_teacher_remarks` varchar(500) DEFAULT NULL,
  `status` enum('Saved','Cancelled','Submitted','Approved','Rejected') DEFAULT 'Saved',
  `created_by` varchar(32) DEFAULT NULL,
  `academic_year` varchar(32) DEFAULT NULL,
  `academic_term` varchar(32) DEFAULT NULL,
  `date_submitted` datetime DEFAULT NULL,
  `date_approved` datetime DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_modified` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `grading_terminal_scores`
--

INSERT INTO `grading_terminal_scores` (`id`, `report_id`, `client_id`, `student_unique_id`, `student_item_id`, `student_name`, `course_id`, `course_name`, `course_code`, `class_id`, `class_name`, `scores`, `total_score`, `average_score`, `class_position`, `teacher_ids`, `teachers_name`, `class_teacher_remarks`, `status`, `created_by`, `academic_year`, `academic_term`, `date_submitted`, `date_approved`, `date_created`, `date_modified`) VALUES
(1, '7pcUdSFJ2au8', 'TLIS0000001', 'LJKDFLAA3', '1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', 'Ebenezer Franklin Hyde', 'XUuG1qdL6SVFyMfCQkP2s5vcpxmgtTHj', 'Basics Of Programming', 'C002', 'B0wVXoJq8cLnaeOWC4EvkRzmhiHSlMry', 'JHS 1', '[{\"item\":\"class_score\",\"score\":\"28\"},{\"item\":\"exams_score\",\"score\":\"55\"}]', 83, '86', NULL, 'P00001', 'fredrick amponsah badu', 'A good student', 'Approved', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st', '2021-03-20 21:46:57', '2021-03-20 21:48:20', '2021-02-24 06:12:15', '2021-03-20 21:50:45'),
(2, '7pcUdSFJ2au8', 'TLIS0000001', 'IURIEKJFD', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'julian asamoah dadzie', 'XUuG1qdL6SVFyMfCQkP2s5vcpxmgtTHj', 'Basics Of Programming', 'C002', 'B0wVXoJq8cLnaeOWC4EvkRzmhiHSlMry', 'JHS 1', '[{\"item\":\"class_score\",\"score\":\"28\"},{\"item\":\"exams_score\",\"score\":\"60\"}]', 88, '86', NULL, 'P00001', 'fredrick amponsah badu', 'Good', 'Approved', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st', '2021-03-20 21:46:57', '2021-03-20 21:48:20', '2021-02-24 06:12:15', '2021-03-20 21:50:45'),
(3, '7pcUdSFJ2au8', 'TLIS0000001', 'ALJKDFLAA3', 'T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK', 'George Anderson Hyde', 'XUuG1qdL6SVFyMfCQkP2s5vcpxmgtTHj', 'Basics Of Programming', 'C002', 'B0wVXoJq8cLnaeOWC4EvkRzmhiHSlMry', 'JHS 1', '[{\"item\":\"class_score\",\"score\":\"28\"},{\"item\":\"exams_score\",\"score\":\"60\"}]', 88, '86', NULL, 'P00001', 'fredrick amponsah badu', 'Excellent', 'Approved', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st', '2021-03-20 21:46:57', '2021-03-20 21:48:20', '2021-02-24 06:12:15', '2021-03-20 21:50:45'),
(4, '7pcUdSFJ2au8', 'TLIS0000001', 'AIURIEKJFD', 'qkrNWbzA3EoZLaSleY5T4291ICQsmvdD', 'Philip Anthony dadzie', 'XUuG1qdL6SVFyMfCQkP2s5vcpxmgtTHj', 'Basics Of Programming', 'C002', 'B0wVXoJq8cLnaeOWC4EvkRzmhiHSlMry', 'JHS 1', '[{\"item\":\"class_score\",\"score\":\"30\"},{\"item\":\"exams_score\",\"score\":\"55\"}]', 85, '86', NULL, 'P00001', 'fredrick amponsah badu', 'Improving on performance', 'Approved', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st', '2021-03-20 21:46:57', '2021-03-20 21:48:20', '2021-02-24 06:12:15', '2021-03-20 21:50:45'),
(5, '1DTUACe7nhq0', 'TLIS0000001', 'LJKDFLAA3', '1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', 'Ebenezer Franklin Hyde', 'BAybmg6is1Ff7ruWveIxhdNYXESJH38U', 'Introduction To Jquery', 'C001', 'B0wVXoJq8cLnaeOWC4EvkRzmhiHSlMry', 'JHS 1', '[{\"item\":\"class_score\",\"score\":\"27\"},{\"item\":\"exams_score\",\"score\":\"63\"}]', 90, '85', NULL, 'P00001', 'fredrick amponsah badu', 'A good student', 'Approved', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st', '2021-03-17 23:05:23', '2021-03-20 21:31:09', '2021-02-24 12:15:35', '2021-03-20 09:42:48'),
(6, '1DTUACe7nhq0', 'TLIS0000001', 'IURIEKJFD', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'julian asamoah dadzie', 'BAybmg6is1Ff7ruWveIxhdNYXESJH38U', 'Introduction To Jquery', 'C001', 'B0wVXoJq8cLnaeOWC4EvkRzmhiHSlMry', 'JHS 1', '[{\"item\":\"class_score\",\"score\":\"26\"},{\"item\":\"exams_score\",\"score\":\"36\"}]', 62, '76.75', NULL, 'P00001', 'fredrick amponsah badu', 'Needs extra tuition', 'Approved', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st', '2021-03-17 23:05:23', '2021-03-19 07:03:29', '2021-02-24 12:15:35', '2021-03-17 11:26:05'),
(7, '1DTUACe7nhq0', 'TLIS0000001', 'ALJKDFLAA3', 'T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK', 'George Anderson Hyde', 'BAybmg6is1Ff7ruWveIxhdNYXESJH38U', 'Introduction To Jquery', 'C001', 'B0wVXoJq8cLnaeOWC4EvkRzmhiHSlMry', 'JHS 1', '[{\"item\":\"class_score\",\"score\":\"27\"},{\"item\":\"exams_score\",\"score\":\"63\"}]', 90, '85', NULL, 'P00001', 'fredrick amponsah badu', 'Could do better', 'Approved', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st', '2021-03-17 23:05:23', '2021-03-20 21:31:09', '2021-02-24 12:15:35', '2021-03-20 09:42:48'),
(8, '1DTUACe7nhq0', 'TLIS0000001', 'AIURIEKJFD', 'qkrNWbzA3EoZLaSleY5T4291ICQsmvdD', 'Philip Anthony dadzie', 'BAybmg6is1Ff7ruWveIxhdNYXESJH38U', 'Introduction To Jquery', 'C001', 'B0wVXoJq8cLnaeOWC4EvkRzmhiHSlMry', 'JHS 1', '[{\"item\":\"class_score\",\"score\":\"27\"},{\"item\":\"exams_score\",\"score\":\"48\"}]', 75, '85', NULL, 'P00001', 'fredrick amponsah badu', 'Improving on performance', 'Approved', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st', '2021-03-17 23:05:23', '2021-03-20 21:31:09', '2021-02-24 12:15:35', '2021-03-20 09:42:48'),
(14, '7AC2NXYLFPRTBO31', 'TLIS0000001', 'LJKDFLAA3', '1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', 'Ebenezer Franklin Hyde', '5sLO0GR2qYThuQK39CzNc1gy4xlHv8PJ', 'Object Oriented Programming', 'C003', 'B0wVXoJq8cLnaeOWC4EvkRzmhiHSlMry', 'JHS 1', '[{\"item\":\"class_score\",\"score\":\"27\"},{\"item\":\"exams_score\",\"score\":\"55\"}]', 82, '84', NULL, 'P00001', 'fredrick amponsah badu', 'Good student needs more improvement', 'Approved', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st', '2021-04-17 14:37:59', '2021-04-17 14:38:44', '2021-04-17 14:33:56', '2021-04-17 14:38:43'),
(15, '7AC2NXYLFPRTBO31', 'TLIS0000001', 'IURIEKJFD', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'julian asamoah dadzie', '5sLO0GR2qYThuQK39CzNc1gy4xlHv8PJ', 'Object Oriented Programming', 'C003', 'B0wVXoJq8cLnaeOWC4EvkRzmhiHSlMry', 'JHS 1', '[{\"item\":\"class_score\",\"score\":\"10\"},{\"item\":\"exams_score\",\"score\":\"63\"}]', 73, '82.75', NULL, 'P00001', 'fredrick amponsah badu', 'Improving steadily.', 'Approved', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st', '2021-04-17 14:37:59', '2021-04-17 14:38:10', '2021-04-17 14:33:56', '2021-04-17 14:37:22'),
(16, '7AC2NXYLFPRTBO31', 'TLIS0000001', 'ALJKDFLAA3', 'T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK', 'George Anderson Hyde', '5sLO0GR2qYThuQK39CzNc1gy4xlHv8PJ', 'Object Oriented Programming', 'C003', 'B0wVXoJq8cLnaeOWC4EvkRzmhiHSlMry', 'JHS 1', '[{\"item\":\"class_score\",\"score\":\"29\"},{\"item\":\"exams_score\",\"score\":\"57\"}]', 86, '84', NULL, 'P00001', 'fredrick amponsah badu', 'Can do better', 'Approved', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st', '2021-04-17 14:37:59', '2021-04-17 14:38:44', '2021-04-17 14:33:56', '2021-04-17 14:38:44'),
(17, '7AC2NXYLFPRTBO31', 'TLIS0000001', 'AIURIEKJFD', 'qkrNWbzA3EoZLaSleY5T4291ICQsmvdD', 'Philip Anthony dadzie', '5sLO0GR2qYThuQK39CzNc1gy4xlHv8PJ', 'Object Oriented Programming', 'C003', 'B0wVXoJq8cLnaeOWC4EvkRzmhiHSlMry', 'JHS 1', '[{\"item\":\"class_score\",\"score\":\"26\"},{\"item\":\"exams_score\",\"score\":\"64\"}]', 90, '82.75', NULL, 'P00001', 'fredrick amponsah badu', 'Excellent performance', 'Approved', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st', '2021-04-17 14:37:59', '2021-04-17 14:38:17', '2021-04-17 14:33:56', '2021-04-17 14:37:23');

-- --------------------------------------------------------

--
-- Table structure for table `guardian_relation`
--

DROP TABLE IF EXISTS `guardian_relation`;
CREATE TABLE `guardian_relation` (
  `id` int(11) NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `guardian_relation`
--

INSERT INTO `guardian_relation` (`id`, `client_id`, `name`, `status`) VALUES
(1, 'LKJAFD94R', 'Parent', '1'),
(2, 'LKJAFD94R', 'Uncle', '1');

-- --------------------------------------------------------

--
-- Table structure for table `incidents`
--

DROP TABLE IF EXISTS `incidents`;
CREATE TABLE `incidents` (
  `id` int(11) NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `incident_id` varchar(32) DEFAULT NULL,
  `user_id` varchar(32) DEFAULT NULL,
  `assigned_to` varchar(32) DEFAULT NULL,
  `created_by` varchar(32) DEFAULT NULL,
  `reported_by` varchar(255) DEFAULT NULL,
  `incident_type` enum('incident','followup') DEFAULT 'incident',
  `subject` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `incident_date` date DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  `status` enum('Pending','Processing','Solved','Cancelled') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `payslips`
--

DROP TABLE IF EXISTS `payslips`;
CREATE TABLE `payslips` (
  `id` int(11) NOT NULL,
  `client_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `item_id` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `employee_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `basic_salary` double(12,2) NOT NULL DEFAULT 0.00,
  `total_allowance` double(12,2) NOT NULL DEFAULT 0.00,
  `total_deductions` double(12,2) NOT NULL DEFAULT 0.00,
  `gross_salary` double(12,2) NOT NULL DEFAULT 0.00,
  `net_salary` double(12,2) NOT NULL DEFAULT 0.00,
  `payslip_month` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payslip_month_id` date DEFAULT NULL,
  `payslip_year` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_mode` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_by` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `validated` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `validated_date` datetime DEFAULT NULL,
  `comments` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_log` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('0','1') COLLATE utf8_unicode_ci DEFAULT '0',
  `deleted` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payslips_allowance_types`
--

DROP TABLE IF EXISTS `payslips_allowance_types`;
CREATE TABLE `payslips_allowance_types` (
  `id` int(11) NOT NULL,
  `client_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `default_amount` varchar(12) COLLATE utf8_unicode_ci DEFAULT '0.00',
  `status` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payslips_details`
--

DROP TABLE IF EXISTS `payslips_details`;
CREATE TABLE `payslips_details` (
  `id` int(11) NOT NULL,
  `client_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payslip_id` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `allowance_id` int(11) DEFAULT NULL,
  `employee_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `detail_type` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payslip_month` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payslip_year` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount` double(12,2) NOT NULL DEFAULT 0.00,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payslips_employees_allowances`
--

DROP TABLE IF EXISTS `payslips_employees_allowances`;
CREATE TABLE `payslips_employees_allowances` (
  `id` int(11) NOT NULL,
  `client_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `allowance_id` int(11) DEFAULT NULL,
  `employee_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount` double(12,2) NOT NULL DEFAULT 0.00,
  `type` enum('Allowance','Deduction') COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payslips_employees_payroll`
--

DROP TABLE IF EXISTS `payslips_employees_payroll`;
CREATE TABLE `payslips_employees_payroll` (
  `id` int(11) NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `employee_id` varchar(32) DEFAULT NULL,
  `basic_salary` double(12,2) DEFAULT 0.00,
  `allowances` double(12,2) DEFAULT 0.00,
  `deductions` double(12,2) DEFAULT 0.00,
  `net_allowance` double(12,2) DEFAULT 0.00,
  `gross_salary` double(12,2) DEFAULT 0.00,
  `net_salary` double(12,2) DEFAULT 0.00,
  `account_name` varchar(255) DEFAULT NULL,
  `account_number` varchar(32) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `bank_branch` varchar(255) DEFAULT NULL,
  `ssnit_number` varchar(255) DEFAULT NULL,
  `tin_number` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `periods`
--

DROP TABLE IF EXISTS `periods`;
CREATE TABLE `periods` (
  `id` int(11) NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `date` varchar(64) DEFAULT NULL,
  `period_start` varchar(64) DEFAULT NULL,
  `period_end` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `name` varchar(25) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `section_code` varchar(32) DEFAULT NULL,
  `image` varchar(255) DEFAULT 'assets/img/placeholder.jpg',
  `description` text DEFAULT NULL,
  `section_leader` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
  `created_by` varchar(32) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `date_created` datetime DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `timetables`
--

DROP TABLE IF EXISTS `timetables`;
CREATE TABLE `timetables` (
  `item_id` varchar(32) NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `days` int(11) NOT NULL DEFAULT 5,
  `slots` int(11) NOT NULL DEFAULT 0,
  `duration` int(11) NOT NULL DEFAULT 90,
  `class_id` varchar(1000) DEFAULT NULL,
  `start_hr` char(2) NOT NULL DEFAULT '08',
  `start_min` char(2) NOT NULL DEFAULT '30',
  `start_mer` enum('AM','PM') NOT NULL DEFAULT 'AM',
  `start_time` varchar(22) DEFAULT NULL,
  `allow_conflicts` tinyint(1) NOT NULL DEFAULT 0,
  `frozen` tinyint(1) NOT NULL DEFAULT 0,
  `academic_year` varchar(32) DEFAULT NULL,
  `academic_term` varchar(32) DEFAULT NULL,
  `disabled_inputs` varchar(2000) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `published` enum('0','1') NOT NULL DEFAULT '1',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `last_updated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `timetables_slots_allocation`
--

DROP TABLE IF EXISTS `timetables_slots_allocation`;
CREATE TABLE `timetables_slots_allocation` (
  `id` int(11) NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `timetable_id` varchar(32) NOT NULL,
  `day` varchar(12) DEFAULT NULL,
  `slot` varchar(12) DEFAULT NULL,
  `day_slot` varchar(12) DEFAULT NULL,
  `room_id` varchar(32) DEFAULT NULL,
  `class_id` varchar(32) DEFAULT NULL,
  `course_id` varchar(32) DEFAULT NULL,
  `students_id` varchar(5000) DEFAULT NULL,
  `tutors_id` varchar(500) DEFAULT NULL,
  `status` enum('0','1') DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `conflicting` varchar(32) NOT NULL,
  `upload_id` varchar(12) DEFAULT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `unique_id` varchar(32) DEFAULT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `othername` varchar(255) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `academic_year` varchar(32) DEFAULT NULL,
  `academic_term` varchar(32) DEFAULT NULL,
  `enrollment_date` date DEFAULT NULL,
  `gender` varchar(32) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `username` varchar(25) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `access_level` int(11) UNSIGNED NOT NULL DEFAULT 6,
  `preferences` varchar(2000) DEFAULT '{"payments":{},"default_payment":"mobile_money","theme_color":"sidebar-light","sidebar":"sidebar-opened","font-size":"12px","previous_policies":{},"list_count":"200","idb_init":{"init":0,"idb_last_init":"2020-09-18","idb_next_init":"2020-09-21"},"sidebar_nav":"sidebar-opened","new_policy_notification":"notify","quick_links":{"chat":"on","calendar":"on","policies":"on","proposals":"on"}}',
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  `verified_email` enum('Y','N') DEFAULT 'N',
  `last_login` datetime DEFAULT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `phone_number_2` varchar(64) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `online` enum('0','1') NOT NULL DEFAULT '0',
  `chat_status` varchar(255) DEFAULT NULL,
  `last_seen` datetime DEFAULT current_timestamp(),
  `nation_ids` varchar(1000) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `class_id` varchar(32) DEFAULT NULL,
  `course_ids` varchar(1000) DEFAULT NULL,
  `class_ids` varchar(2000) DEFAULT NULL,
  `blood_group` varchar(32) DEFAULT NULL,
  `religion` varchar(32) DEFAULT NULL,
  `section` varchar(32) DEFAULT NULL,
  `programme` varchar(32) DEFAULT NULL,
  `department` varchar(32) DEFAULT NULL,
  `nationality` varchar(255) DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `postal_code` varchar(32) DEFAULT NULL,
  `disabled` enum('0','1') NOT NULL DEFAULT '0',
  `residence` varchar(255) DEFAULT NULL,
  `employer` varchar(255) DEFAULT NULL,
  `guardian_id` varchar(1000) DEFAULT 'NULL',
  `last_timetable_id` varchar(32) DEFAULT NULL,
  `country` int(11) UNSIGNED DEFAULT NULL,
  `verify_token` varchar(120) DEFAULT NULL,
  `verified_date` datetime DEFAULT NULL,
  `token_expiry` varchar(32) DEFAULT NULL,
  `changed_password` enum('0','1') DEFAULT '1',
  `account_balance` varchar(32) DEFAULT '0',
  `city` varchar(255) DEFAULT NULL,
  `relationship` varchar(64) DEFAULT NULL,
  `date_created` datetime DEFAULT current_timestamp(),
  `last_updated` datetime DEFAULT NULL,
  `created_by` varchar(32) DEFAULT NULL,
  `image` varchar(255) DEFAULT 'assets/img/user.png',
  `previous_school` varchar(500) DEFAULT NULL,
  `previous_school_qualification` varchar(500) DEFAULT NULL,
  `previous_school_remarks` text DEFAULT NULL,
  `user_status` enum('Pending','Transferred','Active','Graduated','Dismissed') NOT NULL DEFAULT 'Active',
  `perma_image` varchar(255) DEFAULT 'assets/img/user.png',
  `user_type` enum('teacher','employee','parent','admin','student','accountant') DEFAULT NULL,
  `last_visited_page` varchar(255) DEFAULT '{{APPURL}}dashboard'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `conflicting`, `upload_id`, `item_id`, `unique_id`, `client_id`, `firstname`, `lastname`, `othername`, `name`, `academic_year`, `academic_term`, `enrollment_date`, `gender`, `email`, `username`, `password`, `access_level`, `preferences`, `status`, `deleted`, `verified_email`, `last_login`, `phone_number`, `phone_number_2`, `description`, `position`, `address`, `online`, `chat_status`, `last_seen`, `nation_ids`, `date_of_birth`, `class_id`, `course_ids`, `class_ids`, `blood_group`, `religion`, `section`, `programme`, `department`, `nationality`, `occupation`, `postal_code`, `disabled`, `residence`, `employer`, `guardian_id`, `last_timetable_id`, `country`, `verify_token`, `verified_date`, `token_expiry`, `changed_password`, `account_balance`, `city`, `relationship`, `date_created`, `last_updated`, `created_by`, `image`, `previous_school`, `previous_school_qualification`, `previous_school_remarks`, `user_status`, `perma_image`, `user_type`, `last_visited_page`) VALUES
(1, '', NULL, 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'TLISU0000001', 'TLIS0000001', 'Emmanuel', 'Obeng', 'Hyde', 'Emmanuel Obeng Hyde', NULL, NULL, NULL, 'Male', 'emmallob14@gmail.com', 'emmallob14', '$2y$10$h1s4UbIhqcDH0ZHqFr.fIeNMAhGONrpCEBX7IidpU8dah90apf98K', 6, '{\"payments\":{},\"default_payment\":\"mobile_money\",\"theme_color\":\"sidebar-light\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"200\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2021-05-05 16:03:06\",\"idb_next_init\":\"2021-05-07 16:03:06\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"policies\":\"on\",\"proposals\":\"on\"}}', '1', '0', 'Y', '2021-05-02 22:22:50', '0550107770', NULL, '&lt;div&gt;&lt;!--block--&gt;This is my simple profile to add to the database.&lt;/div&gt;', 'Software Developer', 'P. O. Box AF 2582, Adentan Accra', '1', NULL, '2021-05-12 05:28:34', NULL, '1995-03-20', NULL, '[]', NULL, 'B+', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'Dodowa', NULL, NULL, NULL, 84, NULL, '2021-02-19 21:48:26', NULL, '1', '0', NULL, NULL, '2021-02-19 20:43:24', '2021-02-19 23:50:26', NULL, 'assets/img/user.png', NULL, NULL, NULL, 'Active', 'assets/img/user.png', 'admin', '{{APPURL}}fees-payment'),
(2, '', 'dOC2tAcXh4Jf', '1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', 'LJKDFLAA3', 'TLIS0000001', 'Ebenezer', 'Franklin', 'Hyde', 'Ebenezer Franklin Hyde', '2020/2021', '1st', '1970-01-01', 'Male', 'emmallob@mail.com', 'emmallob', '$2y$10$h1s4UbIhqcDH0ZHqFr.fIeNMAhGONrpCEBX7IidpU8dah90apf98K', 6, '{\"payments\":{},\"default_payment\":\"mobile_money\",\"theme_color\":\"sidebar-light\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"200\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2021-05-01 07:21:46\",\"idb_next_init\":\"2021-05-03 07:21:46\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"policies\":\"on\",\"proposals\":\"on\"}}', '1', '0', 'N', '2021-05-01 07:21:42', '983983983', NULL, 'Emmanuel is a good student', NULL, NULL, '0', NULL, '2021-05-01 07:21:49', NULL, '1992-03-20', '1', '[]', NULL, 'A+', 'christian', 'null', NULL, '', NULL, NULL, NULL, '0', 'accra', NULL, 'ZYibOC4wzLWBXUAa5skuhNS2KxQ7nr1f', NULL, 0, NULL, NULL, NULL, '1', '0', 'accra', NULL, '2021-02-20 08:37:02', '2021-02-23 09:32:38', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'assets/img/user.png', 'knust', 'bsc. Real estate', 'he was a good student', 'Active', 'assets/img/user.png', 'student', '{{APPURL}}dashboard'),
(3, '', 'dOC2tAcXh4Jf', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'IURIEKJFD', 'TLIS0000001', 'julian', 'asamoah', 'dadzie', 'julian asamoah dadzie', '2020/2021', '1st', '2020-01-15', 'Male', 'julian@mail.com', 'julian', '$2y$10$h1s4UbIhqcDH0ZHqFr.fIeNMAhGONrpCEBX7IidpU8dah90apf98K', 6, '{\"payments\":{},\"default_payment\":\"mobile_money\",\"theme_color\":\"sidebar-light\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"200\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2021-05-03 08:13:20\",\"idb_next_init\":\"2021-05-05 08:13:20\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"policies\":\"on\",\"proposals\":\"on\"}}', '1', '0', 'N', '2021-05-02 23:05:40', '9090993093', NULL, 'Julian is a great student', NULL, NULL, '1', NULL, '2021-05-03 20:32:53', NULL, '1993-05-12', '1', '[]', NULL, 'B+', 'christian', 'null', NULL, '', NULL, NULL, NULL, '0', 'santa maria', NULL, NULL, NULL, 0, NULL, NULL, NULL, '1', '0', 'accra', NULL, '2021-02-20 08:37:02', '2021-02-23 09:32:59', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'assets/img/user.png', 'pentecost university', 'bsc. Human resource management', 'was a great student', 'Active', 'assets/img/user.png', 'student', '{{APPURL}}dashboard'),
(4, '', 'QBxIsCoZmuhv', 'tnThYo5wKHG2XxgPdSVkErb7zLlqum1A', 'P00001', 'TLIS0000001', 'fredrick', 'amponsah', 'badu', 'fredrick amponsah badu', '2020/2021', '1st', '2020-03-11', 'Male', 'fredamponsah@gmail.com', 'fredamponsah', '$2y$10$h1s4UbIhqcDH0ZHqFr.fIeNMAhGONrpCEBX7IidpU8dah90apf98K', 6, '{\"payments\":{},\"default_payment\":\"mobile_money\",\"theme_color\":\"sidebar-light\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"200\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2021-02-20 22:30:19\",\"idb_next_init\":\"2021-02-22 22:30:19\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"policies\":\"on\",\"proposals\":\"on\"}}', '1', '0', 'N', '2021-02-20 22:30:16', '490993093', NULL, 'He is a good teacher', 'manager', NULL, '1', NULL, '2021-02-20 22:35:53', NULL, '1990-02-03', NULL, '[\"1\",\"2\",\"3\"]', NULL, 'A+', 'christian', 'null', NULL, '', NULL, 'teacher', NULL, '0', 'accra', 'the school', NULL, NULL, 3, NULL, NULL, NULL, '1', '0', 'accra', NULL, '2021-02-20 09:07:18', '2021-02-20 09:55:35', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'assets/img/user.png', NULL, NULL, NULL, 'Active', 'assets/img/user.png', 'teacher', '{{APPURL}}dashboard'),
(5, '', 'QBxIsCoZmuhv', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'P00002', 'TLIS0000001', 'henry', 'asmah', '', 'henry asmah ', '2020/2021', '1st', '2020-03-11', 'Male', 'asmahhenry@gmail.com', 'asmahhenry', '$2y$10$h1s4UbIhqcDH0ZHqFr.fIeNMAhGONrpCEBX7IidpU8dah90apf98K', 6, '{\"payments\":{},\"default_payment\":\"mobile_money\",\"theme_color\":\"sidebar-light\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"200\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2021-02-20 20:50:56\",\"idb_next_init\":\"2021-02-22 20:50:56\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"policies\":\"on\",\"proposals\":\"on\"}}', '1', '0', 'N', '2021-02-20 20:50:52', '9093009192', NULL, 'he is a great teache', 'administrative assistant', NULL, '1', NULL, '2021-02-20 23:24:37', NULL, '1990-02-03', NULL, '[\"1\"]', NULL, 'B', 'christian', '', NULL, '', NULL, 'teacher', NULL, '0', 'accra', 'the school', NULL, NULL, NULL, NULL, NULL, NULL, '1', '0', 'accra', NULL, '2021-02-20 09:07:18', NULL, 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'assets/img/user.png', NULL, NULL, NULL, 'Active', 'assets/img/user.png', 'teacher', '{{APPURL}}dashboard'),
(6, '', NULL, 'vmVn9KIyMRx4ASTDCY0qLF83lWgXZpPa', 'MSISU0000001', 'MSIS0000002', 'Sani', 'Adul', 'Jabar', 'Sani Adul Jabar', NULL, NULL, NULL, 'Male', 'morningstar@gmail.com', 'morningstar', '$2y$10$EU53IfrCN0HSH9O3eh1eYuwPuTiz46qUmgfgouyVPNpPj9luseEVW', 6, '{\"payments\":{},\"default_payment\":\"mobile_money\",\"theme_color\":\"sidebar-light\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"200\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2021-02-22 14:16:58\",\"idb_next_init\":\"2021-02-24 14:16:58\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"policies\":\"on\",\"proposals\":\"on\"}}', '1', '0', 'Y', '2021-02-23 09:25:44', '024033177332', NULL, NULL, 'Developer', 'Accra, Cantanments', '1', NULL, '2021-02-23 09:41:41', NULL, '1999-07-01', NULL, '[]', NULL, 'A-', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'Dzen Ayoor', NULL, NULL, NULL, 84, NULL, '2021-02-22 14:15:36', NULL, '1', '0', NULL, NULL, '2021-02-22 14:13:22', '2021-02-22 16:35:57', NULL, 'assets/img/user.png', NULL, NULL, NULL, 'Active', 'assets/img/user.png', 'admin', '{{APPURL}}dashboard'),
(7, '', 'CQcFuBWHgnjq', 'T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK', 'ALJKDFLAA3', 'TLIS0000001', 'George', 'Anderson', 'Hyde', 'George Anderson Hyde', '2020/2021', '1st', '1970-01-01', 'Male', 'emmallob@mail.com', 'emmallob', NULL, 6, '{\"payments\":{},\"default_payment\":\"mobile_money\",\"theme_color\":\"sidebar-light\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"200\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-09-18\",\"idb_next_init\":\"2020-09-21\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"policies\":\"on\",\"proposals\":\"on\"}}', '1', '0', 'N', NULL, '983983983', NULL, 'Emmanuel is a good student', NULL, NULL, '0', NULL, '2021-02-23 09:27:24', NULL, '1992-06-30', '1', NULL, NULL, 'A+', 'christian', '', NULL, '', NULL, NULL, NULL, '0', 'accra', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', '0', 'accra', NULL, '2021-02-23 09:27:24', NULL, 'vmVn9KIyMRx4ASTDCY0qLF83lWgXZpPa', 'assets/img/user.png', 'knust', 'bsc. Real estate', 'he was a good student', 'Active', 'assets/img/user.png', 'student', '{{APPURL}}dashboard'),
(8, '', 'CQcFuBWHgnjq', 'qkrNWbzA3EoZLaSleY5T4291ICQsmvdD', 'AIURIEKJFD', 'TLIS0000001', 'Philip', 'Anthony', 'dadzie', 'Philip Anthony dadzie', '2020/2021', '1st', '2020-01-15', 'Male', 'julian@mail.com', 'julian', NULL, 6, '{\"payments\":{},\"default_payment\":\"mobile_money\",\"theme_color\":\"sidebar-light\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"200\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-09-18\",\"idb_next_init\":\"2020-09-21\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"policies\":\"on\",\"proposals\":\"on\"}}', '1', '0', 'N', NULL, '9090993093', NULL, 'Julian is a great student', NULL, NULL, '0', NULL, '2021-02-23 09:27:24', NULL, '1999-03-04', '1', NULL, NULL, 'B+', 'christian', '', NULL, '', NULL, NULL, NULL, '0', 'santa maria', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', '0', 'accra', NULL, '2021-02-23 09:27:24', NULL, 'vmVn9KIyMRx4ASTDCY0qLF83lWgXZpPa', 'assets/img/user.png', 'pentecost university', 'bsc. Human resource management', 'was a great student', 'Active', 'assets/img/user.png', 'student', '{{APPURL}}dashboard'),
(9, '', 'MlOgsDr9hwYT', '8hMv9C2qmL1ZH04WwyKfnrRAPusbVGx6', 'P00001', 'MSIS0000002', 'fredrick', 'amponsah', 'badu', 'fredrick amponsah badu', '2020/2021', '1st', '2020-03-11', 'Male', 'fredamponsah@gmail.com', NULL, NULL, 6, '{\"payments\":{},\"default_payment\":\"mobile_money\",\"theme_color\":\"sidebar-light\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"200\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-09-18\",\"idb_next_init\":\"2020-09-21\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"policies\":\"on\",\"proposals\":\"on\"}}', '1', '0', 'N', NULL, '490993093', NULL, 'He is a good teacher', 'manager', NULL, '0', NULL, '2021-02-23 09:27:36', NULL, '1997-12-03', NULL, '', NULL, 'A+', 'christian', '', NULL, '', NULL, 'teacher', NULL, '0', 'accra', 'the school', NULL, NULL, NULL, NULL, NULL, NULL, '1', '0', 'accra', NULL, '2021-02-23 09:27:36', NULL, 'vmVn9KIyMRx4ASTDCY0qLF83lWgXZpPa', 'assets/img/user.png', NULL, NULL, NULL, 'Active', 'assets/img/user.png', 'teacher', '{{APPURL}}dashboard'),
(10, '', 'MlOgsDr9hwYT', 'ZYibOC4wzLWBXUAa5skuhNS2KxQ7nr1f', 'P00002', 'TLIS0000001', 'henry', 'asmah', '', 'henry asmah', '2020/2021', '1st', '2020-03-11', 'Male', 'asmahhenry@gmail.com', NULL, NULL, 6, '{\"payments\":{},\"default_payment\":\"mobile_money\",\"theme_color\":\"sidebar-light\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"200\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-09-18\",\"idb_next_init\":\"2020-09-21\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"policies\":\"on\",\"proposals\":\"on\"}}', '1', '0', 'N', NULL, '9093009192', NULL, 'he is a great teache', 'administrative assistant', NULL, '0', NULL, '2021-02-23 09:27:36', NULL, '1990-10-19', NULL, '', NULL, 'B', 'christian', '', NULL, '', NULL, 'teacher', NULL, '0', 'accra', 'the school', NULL, NULL, NULL, NULL, NULL, NULL, '1', '0', 'accra', NULL, '2021-02-23 09:27:36', NULL, 'vmVn9KIyMRx4ASTDCY0qLF83lWgXZpPa', 'assets/img/user.png', NULL, NULL, NULL, 'Active', 'assets/img/user.png', 'parent', '{{APPURL}}dashboard'),
(11, '', NULL, '9HOBYRpyMxJr60foS5FdQXGDUCsviNeh', 'GISU000001', 'GIS00003', 'Philip', 'Adusei Bampoh', NULL, 'Philip Adusei Bampoh ', NULL, NULL, NULL, 'Male', 'info@gallaxyinternationalschool.com', 'info', '$2y$10$JbOOG34xvPmOQYA5De7QlubFC12SJZrDe6Ct0q7WL9WwGyxJi07/2', 6, '{\"payments\":{},\"default_payment\":\"mobile_money\",\"theme_color\":\"sidebar-light\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"200\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2021-03-17 10:31:37\",\"idb_next_init\":\"2021-03-19 10:31:37\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"policies\":\"on\",\"proposals\":\"on\"}}', '1', '0', 'Y', '2021-03-17 10:33:13', '233550107770', NULL, NULL, 'Administrator', 'P. O. Box DT 2582, Accra', '0', NULL, '2021-03-17 10:42:05', NULL, '1995-07-20', NULL, '[]', NULL, 'A-', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'Accra', NULL, NULL, NULL, 0, NULL, '2021-03-17 10:31:22', NULL, '1', '0', NULL, NULL, '2021-03-17 10:20:28', '2021-03-17 10:42:05', NULL, 'assets/img/user.png', NULL, NULL, NULL, 'Active', 'assets/img/user.png', 'admin', '{{APPURL}}dashboard'),
(12, '', 'dOC2tAcXh4Jf', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9a', 'IURIEKJFD', 'TLIS0000001', 'Test', 'Class', 'Student', 'Test Class Student', '2020/2021', '1st', '2020-01-15', 'Male', 'julian@mail.com', 'julian', '$2y$10$h1s4UbIhqcDH0ZHqFr.fIeNMAhGONrpCEBX7IidpU8dah90apf98K', 6, '{\"payments\":{},\"default_payment\":\"mobile_money\",\"theme_color\":\"sidebar-light\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"200\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2021-05-03 08:13:20\",\"idb_next_init\":\"2021-05-05 08:13:20\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"policies\":\"on\",\"proposals\":\"on\"}}', '1', '0', 'N', '2021-05-02 23:05:40', '9090993093', NULL, 'Julian is a great student', NULL, NULL, '1', NULL, '2021-05-03 12:13:54', NULL, '1993-05-12', '2', '[]', NULL, 'B+', 'christian', 'null', NULL, '', NULL, NULL, NULL, '0', 'santa maria', NULL, NULL, NULL, 0, NULL, NULL, NULL, '1', '0', 'accra', NULL, '2021-02-20 08:37:02', '2021-02-23 09:32:59', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'assets/img/user.png', 'pentecost university', 'bsc. Human resource management', 'was a great student', 'Active', 'assets/img/user.png', 'student', '{{APPURL}}dashboard'),
(13, '', 'dOC2tAcXh4Jf', 'aSzAlD7uPObpda2jKtE0qhWUV8igTo9a', 'IURIEKJFD', 'TLIS0000001', 'Another Test', 'Class', 'Student', 'Another Test Class Student', '2020/2021', '1st', '2020-01-15', 'Male', 'julian@mail.com', 'julian', '$2y$10$h1s4UbIhqcDH0ZHqFr.fIeNMAhGONrpCEBX7IidpU8dah90apf98K', 6, '{\"payments\":{},\"default_payment\":\"mobile_money\",\"theme_color\":\"sidebar-light\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"200\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2021-05-03 08:13:20\",\"idb_next_init\":\"2021-05-05 08:13:20\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"policies\":\"on\",\"proposals\":\"on\"}}', '1', '0', 'N', '2021-05-02 23:05:40', '9090993093', NULL, 'Julian is a great student', NULL, NULL, '1', NULL, '2021-05-03 12:13:54', NULL, '1993-05-12', '2', '[]', NULL, 'B+', 'christian', 'null', NULL, '', NULL, NULL, NULL, '0', 'santa maria', NULL, NULL, NULL, 0, NULL, NULL, NULL, '1', '0', 'accra', NULL, '2021-02-20 08:37:02', '2021-02-23 09:32:59', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'assets/img/user.png', 'pentecost university', 'bsc. Human resource management', 'was a great student', 'Active', 'assets/img/user.png', 'student', '{{APPURL}}dashboard'),
(14, '', NULL, 'Z3qWQN8stLBUSg2FArpOIKEDMJvdafYk', 'TSSU000001', 'MSGH000004', 'Frank', 'Asamoah', 'Mensah', 'Frank Asamoah Mensah', NULL, NULL, NULL, 'Male', 'testsampleschool@mail.com', 'testsampleschool', '$2y$10$T/JZzqXxRCXe3fRQuR9QpeQNj5Rh1PSRVz9IcC08G6pnwfWfGCemW', 6, '{\"payments\":{},\"default_payment\":\"mobile_money\",\"theme_color\":\"sidebar-light\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"200\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2021-05-06 23:33:27\",\"idb_next_init\":\"2021-05-08 23:33:27\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"policies\":\"on\",\"proposals\":\"on\"}}', '1', '0', 'Y', NULL, '233550107770', NULL, NULL, 'Administrator', 'Test School Address', '0', NULL, '2021-05-06 23:44:50', NULL, '1995-07-20', NULL, '[]', NULL, 'A-', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'Island', NULL, 'NULL', NULL, 2, NULL, '2021-05-06 23:49:35', NULL, '1', '0', NULL, NULL, '2021-05-06 22:59:24', '2021-05-06 23:33:51', NULL, 'assets/img/user.png', NULL, NULL, NULL, 'Active', 'assets/img/user.png', 'admin', '{{APPURL}}dashboard');

-- --------------------------------------------------------

--
-- Table structure for table `users_access_attempt`
--

DROP TABLE IF EXISTS `users_access_attempt`;
CREATE TABLE `users_access_attempt` (
  `id` int(11) NOT NULL,
  `ipaddress` varchar(50) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `username_found` enum('0','1') DEFAULT '0',
  `attempt_type` enum('login','reset') DEFAULT 'login',
  `attempts` int(11) DEFAULT 0,
  `lastattempt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users_access_attempt`
--

INSERT INTO `users_access_attempt` (`id`, `ipaddress`, `username`, `username_found`, `attempt_type`, `attempts`, `lastattempt`) VALUES
(1, '::1', 'test_teacher', '0', 'login', 1, '2021-02-19 15:17:07'),
(2, '::1', 'test_admin', '0', 'login', 1, '2021-02-19 15:17:09'),
(3, '::1', 'asmahhenry@gmail.com', '0', 'login', 1, '2021-02-20 20:50:31'),
(4, '::1', 'emmallob14@gmail.com', '0', 'login', 0, '2021-03-21 08:39:27'),
(5, '::1', 'api@dripps.com', '0', 'login', 2, '2021-03-17 10:03:56'),
(6, '::1', 'info@gallaxyinternationalschool.com', '0', 'login', 0, '2021-03-17 10:33:13');

-- --------------------------------------------------------

--
-- Table structure for table `users_activity_logs`
--

DROP TABLE IF EXISTS `users_activity_logs`;
CREATE TABLE `users_activity_logs` (
  `id` int(11) UNSIGNED NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `item_id` varchar(72) DEFAULT NULL,
  `user_id` varchar(32) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `previous_record` text DEFAULT NULL,
  `date_recorded` datetime NOT NULL DEFAULT current_timestamp(),
  `user_agent` varchar(255) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `status` enum('0','1') DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users_activity_logs`
--

INSERT INTO `users_activity_logs` (`id`, `client_id`, `item_id`, `user_id`, `subject`, `source`, `previous_record`, `date_recorded`, `user_agent`, `description`, `status`) VALUES
(1, 'TLIS0000001', 'LJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"27\"},{\"item\":\"exams_score\",\"score\":\"54\"}],\"remarks\":\"A good and promising student\",\"total_score\":81}', '2021-02-24 05:17:55', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: LJKDFLAA3', '1'),
(2, 'TLIS0000001', 'IURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"23\"},{\"item\":\"exams_score\",\"score\":\"24\"}],\"remarks\":\"Needs extra tuition\",\"total_score\":47}', '2021-02-24 05:17:56', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: IURIEKJFD', '1'),
(3, 'TLIS0000001', 'ALJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"25\"},{\"item\":\"exams_score\",\"score\":\"50\"}],\"remarks\":\"Could do better\",\"total_score\":75}', '2021-02-24 05:17:56', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: ALJKDFLAA3', '1'),
(4, 'TLIS0000001', 'AIURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"23\"},{\"item\":\"exams_score\",\"score\":\"44\"}],\"remarks\":\"Improving on performance\",\"total_score\":67}', '2021-02-24 05:17:56', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: AIURIEKJFD', '1'),
(5, 'TLIS0000001', 'LJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"27\"},{\"item\":\"exams_score\",\"score\":\"54\"}],\"remarks\":\"A good and promising student\",\"total_score\":81}', '2021-02-24 05:47:28', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: LJKDFLAA3', '1'),
(6, 'TLIS0000001', 'IURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"23\"},{\"item\":\"exams_score\",\"score\":\"24\"}],\"remarks\":\"Needs extra tuition\",\"total_score\":47}', '2021-02-24 05:47:28', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: IURIEKJFD', '1'),
(7, 'TLIS0000001', 'ALJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"25\"},{\"item\":\"exams_score\",\"score\":\"50\"}],\"remarks\":\"Could do better\",\"total_score\":75}', '2021-02-24 05:47:28', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: ALJKDFLAA3', '1'),
(8, 'TLIS0000001', 'AIURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"23\"},{\"item\":\"exams_score\",\"score\":\"44\"}],\"remarks\":\"Improving on performance\",\"total_score\":67}', '2021-02-24 05:47:28', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: AIURIEKJFD', '1'),
(9, 'TLIS0000001', 'LJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"27\"},{\"item\":\"exams_score\",\"score\":\"54\"}],\"remarks\":\"A good and promising student\",\"total_score\":81}', '2021-02-24 05:48:31', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: LJKDFLAA3', '1'),
(10, 'TLIS0000001', 'IURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"23\"},{\"item\":\"exams_score\",\"score\":\"24\"}],\"remarks\":\"Needs extra tuition\",\"total_score\":47}', '2021-02-24 05:48:32', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: IURIEKJFD', '1'),
(11, 'TLIS0000001', 'ALJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"25\"},{\"item\":\"exams_score\",\"score\":\"50\"}],\"remarks\":\"Could do better\",\"total_score\":75}', '2021-02-24 05:48:32', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: ALJKDFLAA3', '1'),
(12, 'TLIS0000001', 'AIURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"23\"},{\"item\":\"exams_score\",\"score\":\"44\"}],\"remarks\":\"Improving on performance\",\"total_score\":67}', '2021-02-24 05:48:32', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: AIURIEKJFD', '1'),
(13, 'TLIS0000001', 'LJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"27\"},{\"item\":\"exams_score\",\"score\":\"54\"}],\"remarks\":\"A good and promising student\",\"total_score\":81}', '2021-02-24 05:49:01', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: LJKDFLAA3', '1'),
(14, 'TLIS0000001', 'IURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"23\"},{\"item\":\"exams_score\",\"score\":\"24\"}],\"remarks\":\"Needs extra tuition\",\"total_score\":47}', '2021-02-24 05:49:01', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: IURIEKJFD', '1'),
(15, 'TLIS0000001', 'ALJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"25\"},{\"item\":\"exams_score\",\"score\":\"50\"}],\"remarks\":\"Could do better\",\"total_score\":75}', '2021-02-24 05:49:01', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: ALJKDFLAA3', '1'),
(16, 'TLIS0000001', 'AIURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"23\"},{\"item\":\"exams_score\",\"score\":\"44\"}],\"remarks\":\"Improving on performance\",\"total_score\":67}', '2021-02-24 05:49:01', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: AIURIEKJFD', '1'),
(17, 'TLIS0000001', 'LJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"27\"},{\"item\":\"exams_score\",\"score\":\"54\"}],\"remarks\":\"A good and promising student\",\"total_score\":81}', '2021-02-24 05:49:19', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: LJKDFLAA3', '1'),
(18, 'TLIS0000001', 'IURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"23\"},{\"item\":\"exams_score\",\"score\":\"24\"}],\"remarks\":\"Needs extra tuition\",\"total_score\":47}', '2021-02-24 05:49:19', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: IURIEKJFD', '1'),
(19, 'TLIS0000001', 'ALJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"25\"},{\"item\":\"exams_score\",\"score\":\"50\"}],\"remarks\":\"Could do better\",\"total_score\":75}', '2021-02-24 05:49:19', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: ALJKDFLAA3', '1'),
(20, 'TLIS0000001', 'AIURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"23\"},{\"item\":\"exams_score\",\"score\":\"44\"}],\"remarks\":\"Improving on performance\",\"total_score\":67}', '2021-02-24 05:49:19', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: AIURIEKJFD', '1'),
(21, 'TLIS0000001', 'LJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"27\"},{\"item\":\"exams_score\",\"score\":\"54\"}],\"remarks\":\"A good and promising student\",\"total_score\":81}', '2021-02-24 05:49:57', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: LJKDFLAA3', '1'),
(22, 'TLIS0000001', 'IURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"23\"},{\"item\":\"exams_score\",\"score\":\"24\"}],\"remarks\":\"Needs extra tuition\",\"total_score\":47}', '2021-02-24 05:49:57', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: IURIEKJFD', '1'),
(23, 'TLIS0000001', 'ALJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"25\"},{\"item\":\"exams_score\",\"score\":\"50\"}],\"remarks\":\"Could do better\",\"total_score\":75}', '2021-02-24 05:49:57', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: ALJKDFLAA3', '1'),
(24, 'TLIS0000001', 'AIURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"23\"},{\"item\":\"exams_score\",\"score\":\"44\"}],\"remarks\":\"Improving on performance\",\"total_score\":67}', '2021-02-24 05:49:57', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: AIURIEKJFD', '1'),
(25, 'TLIS0000001', 'LJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"27\"},{\"item\":\"exams_score\",\"score\":\"54\"}],\"remarks\":\"A good and promising student\",\"total_score\":81}', '2021-02-24 05:50:43', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: LJKDFLAA3', '1'),
(26, 'TLIS0000001', 'IURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"23\"},{\"item\":\"exams_score\",\"score\":\"24\"}],\"remarks\":\"Needs extra tuition\",\"total_score\":47}', '2021-02-24 05:50:43', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: IURIEKJFD', '1'),
(27, 'TLIS0000001', 'ALJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"25\"},{\"item\":\"exams_score\",\"score\":\"50\"}],\"remarks\":\"Could do better\",\"total_score\":75}', '2021-02-24 05:50:43', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: ALJKDFLAA3', '1'),
(28, 'TLIS0000001', 'AIURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"23\"},{\"item\":\"exams_score\",\"score\":\"44\"}],\"remarks\":\"Improving on performance\",\"total_score\":67}', '2021-02-24 05:50:43', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: AIURIEKJFD', '1'),
(29, 'TLIS0000001', 'LJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"27\"},{\"item\":\"exams_score\",\"score\":\"54\"}],\"remarks\":\"A good student\",\"total_score\":81}', '2021-02-24 05:54:09', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: LJKDFLAA3', '1'),
(30, 'TLIS0000001', 'IURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"23\"},{\"item\":\"exams_score\",\"score\":\"24\"}],\"remarks\":\"Needs extra tuition\",\"total_score\":47}', '2021-02-24 05:54:09', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: IURIEKJFD', '1'),
(31, 'TLIS0000001', 'ALJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"25\"},{\"item\":\"exams_score\",\"score\":\"50\"}],\"remarks\":\"Could do better\",\"total_score\":75}', '2021-02-24 05:54:09', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: ALJKDFLAA3', '1'),
(32, 'TLIS0000001', 'AIURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"23\"},{\"item\":\"exams_score\",\"score\":\"44\"}],\"remarks\":\"Improving on performance\",\"total_score\":67}', '2021-02-24 05:54:09', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: AIURIEKJFD', '1'),
(33, 'TLIS0000001', 'LJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"27\"},{\"item\":\"exams_score\",\"score\":\"54\"}],\"remarks\":\"A good student\",\"total_score\":81}', '2021-02-24 06:04:26', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: LJKDFLAA3', '1'),
(34, 'TLIS0000001', 'IURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"23\"},{\"item\":\"exams_score\",\"score\":\"24\"}],\"remarks\":\"Needs extra tuition\",\"total_score\":47}', '2021-02-24 06:04:26', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: IURIEKJFD', '1'),
(35, 'TLIS0000001', 'ALJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"25\"},{\"item\":\"exams_score\",\"score\":\"50\"}],\"remarks\":\"Could do better\",\"total_score\":75}', '2021-02-24 06:04:26', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: ALJKDFLAA3', '1'),
(36, 'TLIS0000001', 'AIURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"23\"},{\"item\":\"exams_score\",\"score\":\"44\"}],\"remarks\":\"Improving on performance\",\"total_score\":67}', '2021-02-24 06:04:27', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: AIURIEKJFD', '1'),
(37, 'TLIS0000001', 'LJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"27\"},{\"item\":\"exams_score\",\"score\":\"54\"}],\"remarks\":\"A good student\",\"total_score\":81}', '2021-02-24 06:07:40', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: LJKDFLAA3', '1'),
(38, 'TLIS0000001', 'IURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"23\"},{\"item\":\"exams_score\",\"score\":\"24\"}],\"remarks\":\"Needs extra tuition\",\"total_score\":47}', '2021-02-24 06:07:40', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: IURIEKJFD', '1'),
(39, 'TLIS0000001', 'ALJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"25\"},{\"item\":\"exams_score\",\"score\":\"50\"}],\"remarks\":\"Could do better\",\"total_score\":75}', '2021-02-24 06:07:41', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: ALJKDFLAA3', '1'),
(40, 'TLIS0000001', 'AIURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"23\"},{\"item\":\"exams_score\",\"score\":\"44\"}],\"remarks\":\"Improving on performance\",\"total_score\":67}', '2021-02-24 06:07:41', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: AIURIEKJFD', '1'),
(41, 'TLIS0000001', 'LJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"27\"},{\"item\":\"exams_score\",\"score\":\"54\"}],\"remarks\":\"A good student\",\"total_score\":81}', '2021-02-24 06:08:08', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: LJKDFLAA3', '1'),
(42, 'TLIS0000001', 'IURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"23\"},{\"item\":\"exams_score\",\"score\":\"24\"}],\"remarks\":\"Needs extra tuition\",\"total_score\":47}', '2021-02-24 06:08:08', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: IURIEKJFD', '1'),
(43, 'TLIS0000001', 'ALJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"25\"},{\"item\":\"exams_score\",\"score\":\"50\"}],\"remarks\":\"Could do better\",\"total_score\":75}', '2021-02-24 06:08:08', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: ALJKDFLAA3', '1'),
(44, 'TLIS0000001', 'AIURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"23\"},{\"item\":\"exams_score\",\"score\":\"44\"}],\"remarks\":\"Improving on performance\",\"total_score\":67}', '2021-02-24 06:08:08', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: AIURIEKJFD', '1'),
(45, 'TLIS0000001', 'z0x46Nk9O31MTDYhyUJLdgBuas8S5I2i', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'classes', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-02-24 06:10:35', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde created a new Class: JHS 1', '1'),
(46, 'TLIS0000001', 'EDMOiv6CQ2nLrP08d7XFygGSkIjqhcxA', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'classes', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-02-24 06:10:53', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde created a new Class: JHS 2', '1'),
(47, 'TLIS0000001', 'LJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"27\"},{\"item\":\"exams_score\",\"score\":\"54\"}],\"remarks\":\"A good student\",\"total_score\":81}', '2021-02-24 06:12:15', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: LJKDFLAA3', '1'),
(48, 'TLIS0000001', 'IURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"23\"},{\"item\":\"exams_score\",\"score\":\"24\"}],\"remarks\":\"Needs extra tuition\",\"total_score\":47}', '2021-02-24 06:12:15', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: IURIEKJFD', '1'),
(49, 'TLIS0000001', 'ALJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"25\"},{\"item\":\"exams_score\",\"score\":\"50\"}],\"remarks\":\"Could do better\",\"total_score\":75}', '2021-02-24 06:12:15', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: ALJKDFLAA3', '1'),
(50, 'TLIS0000001', 'AIURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"23\"},{\"item\":\"exams_score\",\"score\":\"44\"}],\"remarks\":\"Improving on performance\",\"total_score\":67}', '2021-02-24 06:12:15', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: AIURIEKJFD', '1'),
(51, 'TLIS0000001', 'LJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"scores\":\"[{\\\"item\\\":\\\"class_score\\\",\\\"score\\\":\\\"27\\\"},{\\\"item\\\":\\\"exams_score\\\",\\\"score\\\":\\\"54\\\"}]\",\"total_score\":\"81\",\"average_score\":\"67.5\",\"upload_id\":\"7pcUdSFJ2au8\",\"class_position\":null,\"student_name\":\"Ebenezer Franklin Hyde\",\"status\":\"Submitted\"}', '2021-02-24 06:16:24', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Ebenezer Franklin Hyde</strong> With ID: LJKDFLAA3', '1'),
(52, 'TLIS0000001', 'IURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"scores\":\"[{\\\"item\\\":\\\"class_score\\\",\\\"score\\\":\\\"23\\\"},{\\\"item\\\":\\\"exams_score\\\",\\\"score\\\":\\\"24\\\"}]\",\"total_score\":\"47\",\"average_score\":\"67.5\",\"upload_id\":\"7pcUdSFJ2au8\",\"class_position\":null,\"student_name\":\"julian asamoah dadzie\",\"status\":\"Submitted\"}', '2021-02-24 06:16:24', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>julian asamoah dadzie</strong> With ID: IURIEKJFD', '1'),
(53, 'TLIS0000001', 'ALJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"scores\":\"[{\\\"item\\\":\\\"class_score\\\",\\\"score\\\":\\\"25\\\"},{\\\"item\\\":\\\"exams_score\\\",\\\"score\\\":\\\"50\\\"}]\",\"total_score\":\"75\",\"average_score\":\"67.5\",\"upload_id\":\"7pcUdSFJ2au8\",\"class_position\":null,\"student_name\":\"George Anderson Hyde\",\"status\":\"Submitted\"}', '2021-02-24 06:16:24', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>George Anderson Hyde</strong> With ID: ALJKDFLAA3', '1'),
(54, 'TLIS0000001', 'AIURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"scores\":\"[{\\\"item\\\":\\\"class_score\\\",\\\"score\\\":\\\"23\\\"},{\\\"item\\\":\\\"exams_score\\\",\\\"score\\\":\\\"44\\\"}]\",\"total_score\":\"67\",\"average_score\":\"67.5\",\"upload_id\":\"7pcUdSFJ2au8\",\"class_position\":null,\"student_name\":\"Philip Anthony dadzie\",\"status\":\"Submitted\"}', '2021-02-24 06:16:24', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Philip Anthony dadzie</strong> With ID: AIURIEKJFD', '1'),
(55, 'TLIS0000001', '', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-02-24 11:52:17', 'Windows 10 | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> added a new endpoint: <strong>terminal_reports/check_existence</strong> to the resource: <strong>terminal_reports</strong>.', '1'),
(56, 'TLIS0000001', 'LJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"27\"},{\"item\":\"exams_score\",\"score\":\"58\"}],\"remarks\":\"A good student\",\"total_score\":85}', '2021-02-24 12:15:35', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: LJKDFLAA3', '1'),
(57, 'TLIS0000001', 'IURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"26\"},{\"item\":\"exams_score\",\"score\":\"36\"}],\"remarks\":\"Needs extra tuition\",\"total_score\":62}', '2021-02-24 12:15:35', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: IURIEKJFD', '1'),
(58, 'TLIS0000001', 'ALJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"27\"},{\"item\":\"exams_score\",\"score\":\"62\"}],\"remarks\":\"Could do better\",\"total_score\":89}', '2021-02-24 12:15:35', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: ALJKDFLAA3', '1'),
(59, 'TLIS0000001', 'AIURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"27\"},{\"item\":\"exams_score\",\"score\":\"44\"}],\"remarks\":\"Improving on performance\",\"total_score\":71}', '2021-02-24 12:15:35', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: AIURIEKJFD', '1'),
(60, 'GIS00003', '9HOBYRpyMxJr60foS5FdQXGDUCsviNeh', '9HOBYRpyMxJr60foS5FdQXGDUCsviNeh', 'verify_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-17 10:20:28', 'Windows 10 | Chrome | ::1', 'Galaxy International School created a new Account pending Verification.', '1'),
(61, 'GIS00003', '9HOBYRpyMxJr60foS5FdQXGDUCsviNeh', '9HOBYRpyMxJr60foS5FdQXGDUCsviNeh', 'verify_account', 'Account was manually activated using the Activation link.', NULL, '2021-03-17 10:31:22', 'Windows 10 | Chrome | ::1', 'info\'s - account was successfully activated.', '1'),
(62, 'GIS00003', 'GIS00003', '9HOBYRpyMxJr60foS5FdQXGDUCsviNeh', 'account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"3\",\"client_id\":\"GIS00003\",\"client_name\":\"Galaxy International School\",\"client_contact\":\"233550107770\",\"client_secondary_contact\":\"233240553604\",\"client_address\":\"P. O. Box DT 2582, Accra\",\"client_email\":\"info@gallaxyinternationalschool.com\",\"client_website\":null,\"client_logo\":null,\"client_location\":null,\"client_category\":null,\"client_preferences\":{\"labels\":{\"staff\":\"GIS\",\"student\":\"GIS\",\"parent\":\"GIS\"},\"academics\":{\"academic_year\":\"\",\"academic_term\":\"\",\"next_academic_year\":\"\",\"next_academic_term\":\"\"},\"account\":{\"type\":\"basic\",\"activation_code\":\"r0GnHJ3smfZQziSRNC8Ie65VPBOYuMLvEpjwyAtq2klWXxKTodhDcg\",\"date_created\":\"2021-03-17 10:20AM\",\"expiry\":\"2021-04-17 10:31AM\",\"verified_date\":\"2021-03-17 10:31AM\"}},\"client_status\":\"1\",\"client_state\":\"Activated\",\"ip_address\":\"::1\",\"date_created\":\"2021-03-17 10:20:28\",\"grading_system\":null,\"grading_structure\":null,\"show_position\":null,\"show_teacher_name\":null,\"allow_submission\":null}', '2021-03-17 10:36:17', 'Windows 10 | Chrome | ::1', ' updated the Account Information', '1'),
(63, 'GIS00003', 'GIS00003', '9HOBYRpyMxJr60foS5FdQXGDUCsviNeh', 'account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"3\",\"client_id\":\"GIS00003\",\"client_name\":\"Galaxy International School\",\"client_contact\":\"233550107770\",\"client_secondary_contact\":\"233240553604\",\"client_address\":\"P. O. Box DT 2582, Accra\",\"client_email\":\"info@gallaxyinternationalschool.com\",\"client_website\":\"https:\\/\\/www.gallaxyinternationalschool.com\",\"client_logo\":null,\"client_location\":\"Accra\",\"client_category\":null,\"client_preferences\":{\"academics\":{\"academic_year\":\"2021\\/2022\",\"academic_term\":\"1st\",\"term_starts\":\"2021-01-12\",\"term_ends\":\"2021-04-01\",\"next_academic_year\":\"2021\\/2022\",\"next_academic_term\":\"2nd\",\"next_term_starts\":\"2021-04-30\",\"next_term_ends\":\"2021-06-30\"},\"labels\":{\"student_label\":\"st\",\"parent_label\":\"gu\",\"teacher_label\":\"tl\",\"staff_label\":\"sl\",\"course_label\":\"cl\",\"book_label\":\"bk\",\"class_label\":\"ctl\",\"department_label\":\"dp\",\"section_label\":\"st\",\"receipt_label\":\"rl\",\"currency\":\"GHS\"},\"opening_days\":[\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\",\"Friday\"]},\"client_status\":\"1\",\"client_state\":\"Activated\",\"ip_address\":\"::1\",\"date_created\":\"2021-03-17 10:20:28\",\"grading_system\":null,\"grading_structure\":null,\"show_position\":null,\"show_teacher_name\":null,\"allow_submission\":null}', '2021-03-17 10:37:37', 'Windows 10 | Chrome | ::1', ' updated the Account Information', '1'),
(64, 'GIS00003', '9HOBYRpyMxJr60foS5FdQXGDUCsviNeh', '9HOBYRpyMxJr60foS5FdQXGDUCsviNeh', 'admin_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-17 10:41:16', 'Windows 10 | Chrome | ::1', 'Name was changed from ', '1'),
(65, 'GIS00003', '9HOBYRpyMxJr60foS5FdQXGDUCsviNeh', '9HOBYRpyMxJr60foS5FdQXGDUCsviNeh', 'admin_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-17 10:41:16', 'Windows 10 | Chrome | ::1', 'Date of Birth has been changed to 1995-07-20', '1'),
(66, 'GIS00003', '9HOBYRpyMxJr60foS5FdQXGDUCsviNeh', '9HOBYRpyMxJr60foS5FdQXGDUCsviNeh', 'admin_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-17 10:41:16', 'Windows 10 | Chrome | ::1', 'Position has been altered.  => Administrator', '1'),
(67, 'GIS00003', '9HOBYRpyMxJr60foS5FdQXGDUCsviNeh', '9HOBYRpyMxJr60foS5FdQXGDUCsviNeh', 'admin_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-17 10:41:16', 'Windows 10 | Chrome | ::1', 'You updated your account information', '1'),
(68, 'GIS00003', '9HOBYRpyMxJr60foS5FdQXGDUCsviNeh', '9HOBYRpyMxJr60foS5FdQXGDUCsviNeh', 'admin_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-17 10:42:05', 'Windows 10 | Chrome | ::1', 'You updated your account information', '1'),
(69, 'TLIS0000001', '', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"25\"},{\"item\":\"exams_score\",\"score\":\"56\"},{\"item\":\"class_score\",\"score\":\"26\"},{\"item\":\"exams_score\",\"score\":\"57\"},{\"item\":\"class_score\",\"score\":\"27\"},{\"item\":\"exams_score\",\"score\":\"58\"},{\"item\":\"class_score\",\"score\":\"28\"},{\"item\":\"exams_score\",\"score\":\"59\"}],\"remarks\":\"Great Work\",\"total_score\":336}', '2021-03-17 11:19:40', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: ', '1'),
(70, 'TLIS0000001', 'LJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"scores\":\"[{\\\"item\\\":\\\"class_score\\\",\\\"score\\\":\\\"27\\\"},{\\\"item\\\":\\\"exams_score\\\",\\\"score\\\":\\\"54\\\"}]\",\"total_score\":\"81\",\"average_score\":\"67.5\",\"upload_id\":\"7pcUdSFJ2au8\",\"class_position\":null,\"student_name\":\"Ebenezer Franklin Hyde\",\"status\":\"Saved\"}', '2021-03-17 11:25:05', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Ebenezer Franklin Hyde</strong> With ID: LJKDFLAA3', '1'),
(71, 'TLIS0000001', 'IURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"scores\":\"[{\\\"item\\\":\\\"class_score\\\",\\\"score\\\":\\\"23\\\"},{\\\"item\\\":\\\"exams_score\\\",\\\"score\\\":\\\"24\\\"}]\",\"total_score\":\"47\",\"average_score\":\"67.5\",\"upload_id\":\"7pcUdSFJ2au8\",\"class_position\":null,\"student_name\":\"julian asamoah dadzie\",\"status\":\"Saved\"}', '2021-03-17 11:25:05', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>julian asamoah dadzie</strong> With ID: IURIEKJFD', '1'),
(72, 'TLIS0000001', 'ALJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"scores\":\"[{\\\"item\\\":\\\"class_score\\\",\\\"score\\\":\\\"25\\\"},{\\\"item\\\":\\\"exams_score\\\",\\\"score\\\":\\\"50\\\"}]\",\"total_score\":\"75\",\"average_score\":\"67.5\",\"upload_id\":\"7pcUdSFJ2au8\",\"class_position\":null,\"student_name\":\"George Anderson Hyde\",\"status\":\"Saved\"}', '2021-03-17 11:25:05', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>George Anderson Hyde</strong> With ID: ALJKDFLAA3', '1'),
(73, 'TLIS0000001', 'AIURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"scores\":\"[{\\\"item\\\":\\\"class_score\\\",\\\"score\\\":\\\"23\\\"},{\\\"item\\\":\\\"exams_score\\\",\\\"score\\\":\\\"44\\\"}]\",\"total_score\":\"67\",\"average_score\":\"67.5\",\"upload_id\":\"7pcUdSFJ2au8\",\"class_position\":null,\"student_name\":\"Philip Anthony dadzie\",\"status\":\"Saved\"}', '2021-03-17 11:25:05', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Philip Anthony dadzie</strong> With ID: AIURIEKJFD', '1'),
(74, 'TLIS0000001', 'LJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"scores\":\"[{\\\"item\\\":\\\"class_score\\\",\\\"score\\\":\\\"25\\\"},{\\\"item\\\":\\\"exams_score\\\",\\\"score\\\":\\\"56\\\"}]\",\"total_score\":\"81\",\"average_score\":\"84\",\"upload_id\":\"7pcUdSFJ2au8\",\"class_position\":null,\"student_name\":\"Ebenezer Franklin Hyde\",\"status\":\"Saved\"}', '2021-03-17 11:36:57', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Ebenezer Franklin Hyde</strong> With ID: LJKDFLAA3', '1'),
(75, 'TLIS0000001', 'IURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"scores\":\"[{\\\"item\\\":\\\"class_score\\\",\\\"score\\\":\\\"26\\\"},{\\\"item\\\":\\\"exams_score\\\",\\\"score\\\":\\\"57\\\"}]\",\"total_score\":\"83\",\"average_score\":\"84\",\"upload_id\":\"7pcUdSFJ2au8\",\"class_position\":null,\"student_name\":\"julian asamoah dadzie\",\"status\":\"Saved\"}', '2021-03-17 11:36:57', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>julian asamoah dadzie</strong> With ID: IURIEKJFD', '1'),
(76, 'TLIS0000001', 'ALJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"scores\":\"[{\\\"item\\\":\\\"class_score\\\",\\\"score\\\":\\\"27\\\"},{\\\"item\\\":\\\"exams_score\\\",\\\"score\\\":\\\"58\\\"}]\",\"total_score\":\"85\",\"average_score\":\"84\",\"upload_id\":\"7pcUdSFJ2au8\",\"class_position\":null,\"student_name\":\"George Anderson Hyde\",\"status\":\"Saved\"}', '2021-03-17 11:36:57', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>George Anderson Hyde</strong> With ID: ALJKDFLAA3', '1'),
(77, 'TLIS0000001', 'AIURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"scores\":\"[{\\\"item\\\":\\\"class_score\\\",\\\"score\\\":\\\"28\\\"},{\\\"item\\\":\\\"exams_score\\\",\\\"score\\\":\\\"59\\\"}]\",\"total_score\":\"87\",\"average_score\":\"84\",\"upload_id\":\"7pcUdSFJ2au8\",\"class_position\":null,\"student_name\":\"Philip Anthony dadzie\",\"status\":\"Saved\"}', '2021-03-17 11:36:57', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Philip Anthony dadzie</strong> With ID: AIURIEKJFD', '1'),
(78, 'TLIS0000001', 'LJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"scores\":\"[{\\\"item\\\":\\\"class_score\\\",\\\"score\\\":\\\"25\\\"},{\\\"item\\\":\\\"exams_score\\\",\\\"score\\\":\\\"56\\\"}]\",\"total_score\":\"81\",\"average_score\":\"84\",\"upload_id\":\"7pcUdSFJ2au8\",\"class_position\":null,\"student_name\":\"Ebenezer Franklin Hyde\",\"status\":\"Saved\"}', '2021-03-17 11:37:17', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Ebenezer Franklin Hyde</strong> With ID: LJKDFLAA3', '1'),
(79, 'TLIS0000001', 'IURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"scores\":\"[{\\\"item\\\":\\\"class_score\\\",\\\"score\\\":\\\"26\\\"},{\\\"item\\\":\\\"exams_score\\\",\\\"score\\\":\\\"57\\\"}]\",\"total_score\":\"83\",\"average_score\":\"84\",\"upload_id\":\"7pcUdSFJ2au8\",\"class_position\":null,\"student_name\":\"julian asamoah dadzie\",\"status\":\"Saved\"}', '2021-03-17 11:37:17', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>julian asamoah dadzie</strong> With ID: IURIEKJFD', '1'),
(80, 'TLIS0000001', 'ALJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"scores\":\"[{\\\"item\\\":\\\"class_score\\\",\\\"score\\\":\\\"27\\\"},{\\\"item\\\":\\\"exams_score\\\",\\\"score\\\":\\\"58\\\"}]\",\"total_score\":\"85\",\"average_score\":\"84\",\"upload_id\":\"7pcUdSFJ2au8\",\"class_position\":null,\"student_name\":\"George Anderson Hyde\",\"status\":\"Saved\"}', '2021-03-17 11:37:17', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>George Anderson Hyde</strong> With ID: ALJKDFLAA3', '1'),
(81, 'TLIS0000001', 'AIURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"scores\":\"[{\\\"item\\\":\\\"class_score\\\",\\\"score\\\":\\\"28\\\"},{\\\"item\\\":\\\"exams_score\\\",\\\"score\\\":\\\"59\\\"}]\",\"total_score\":\"87\",\"average_score\":\"84\",\"upload_id\":\"7pcUdSFJ2au8\",\"class_position\":null,\"student_name\":\"Philip Anthony dadzie\",\"status\":\"Saved\"}', '2021-03-17 11:37:17', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Philip Anthony dadzie</strong> With ID: AIURIEKJFD', '1'),
(82, 'TLIS0000001', 'LJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"scores\":\"[{\\\"item\\\":\\\"class_score\\\",\\\"score\\\":\\\"25\\\"},{\\\"item\\\":\\\"exams_score\\\",\\\"score\\\":\\\"56\\\"}]\",\"total_score\":\"81\",\"average_score\":\"84\",\"upload_id\":\"7pcUdSFJ2au8\",\"class_position\":null,\"student_name\":\"Ebenezer Franklin Hyde\",\"status\":\"Saved\"}', '2021-03-17 11:38:06', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Ebenezer Franklin Hyde</strong> With ID: LJKDFLAA3', '1'),
(83, 'TLIS0000001', 'IURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"scores\":\"[{\\\"item\\\":\\\"class_score\\\",\\\"score\\\":\\\"26\\\"},{\\\"item\\\":\\\"exams_score\\\",\\\"score\\\":\\\"57\\\"}]\",\"total_score\":\"83\",\"average_score\":\"84\",\"upload_id\":\"7pcUdSFJ2au8\",\"class_position\":null,\"student_name\":\"julian asamoah dadzie\",\"status\":\"Saved\"}', '2021-03-17 11:38:06', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>julian asamoah dadzie</strong> With ID: IURIEKJFD', '1'),
(84, 'TLIS0000001', 'ALJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"scores\":\"[{\\\"item\\\":\\\"class_score\\\",\\\"score\\\":\\\"27\\\"},{\\\"item\\\":\\\"exams_score\\\",\\\"score\\\":\\\"58\\\"}]\",\"total_score\":\"85\",\"average_score\":\"84\",\"upload_id\":\"7pcUdSFJ2au8\",\"class_position\":null,\"student_name\":\"George Anderson Hyde\",\"status\":\"Saved\"}', '2021-03-17 11:38:06', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>George Anderson Hyde</strong> With ID: ALJKDFLAA3', '1'),
(85, 'TLIS0000001', 'AIURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"scores\":\"[{\\\"item\\\":\\\"class_score\\\",\\\"score\\\":\\\"28\\\"},{\\\"item\\\":\\\"exams_score\\\",\\\"score\\\":\\\"59\\\"}]\",\"total_score\":\"87\",\"average_score\":\"84\",\"upload_id\":\"7pcUdSFJ2au8\",\"class_position\":null,\"student_name\":\"Philip Anthony dadzie\",\"status\":\"Saved\"}', '2021-03-17 11:38:06', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Philip Anthony dadzie</strong> With ID: AIURIEKJFD', '1'),
(86, 'TLIS0000001', 'LJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"scores\":\"[{\\\"item\\\":\\\"class_score\\\",\\\"score\\\":\\\"25\\\"},{\\\"item\\\":\\\"exams_score\\\",\\\"score\\\":\\\"56\\\"}]\",\"total_score\":\"81\",\"average_score\":\"84\",\"upload_id\":\"7pcUdSFJ2au8\",\"class_position\":null,\"student_name\":\"Ebenezer Franklin Hyde\",\"status\":\"Saved\"}', '2021-03-17 11:39:07', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Ebenezer Franklin Hyde</strong> With ID: LJKDFLAA3', '1'),
(87, 'TLIS0000001', 'IURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"scores\":\"[{\\\"item\\\":\\\"class_score\\\",\\\"score\\\":\\\"26\\\"},{\\\"item\\\":\\\"exams_score\\\",\\\"score\\\":\\\"57\\\"}]\",\"total_score\":\"83\",\"average_score\":\"84\",\"upload_id\":\"7pcUdSFJ2au8\",\"class_position\":null,\"student_name\":\"julian asamoah dadzie\",\"status\":\"Saved\"}', '2021-03-17 11:39:07', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>julian asamoah dadzie</strong> With ID: IURIEKJFD', '1'),
(88, 'TLIS0000001', 'ALJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"scores\":\"[{\\\"item\\\":\\\"class_score\\\",\\\"score\\\":\\\"27\\\"},{\\\"item\\\":\\\"exams_score\\\",\\\"score\\\":\\\"58\\\"}]\",\"total_score\":\"85\",\"average_score\":\"84\",\"upload_id\":\"7pcUdSFJ2au8\",\"class_position\":null,\"student_name\":\"George Anderson Hyde\",\"status\":\"Saved\"}', '2021-03-17 11:39:07', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>George Anderson Hyde</strong> With ID: ALJKDFLAA3', '1'),
(89, 'TLIS0000001', 'AIURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"scores\":\"[{\\\"item\\\":\\\"class_score\\\",\\\"score\\\":\\\"28\\\"},{\\\"item\\\":\\\"exams_score\\\",\\\"score\\\":\\\"59\\\"}]\",\"total_score\":\"87\",\"average_score\":\"84\",\"upload_id\":\"7pcUdSFJ2au8\",\"class_position\":null,\"student_name\":\"Philip Anthony dadzie\",\"status\":\"Saved\"}', '2021-03-17 11:39:07', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Philip Anthony dadzie</strong> With ID: AIURIEKJFD', '1'),
(90, 'TLIS0000001', '1DTUACe7nhq0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'report_result', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-17 23:05:23', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde submitted the results of <strong>Introduction To Jquery (C001)</strong> for <strong>JHS 1</strong>', '1'),
(91, 'TLIS0000001', '1DTUACe7nhq0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'report_result', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-18 22:33:54', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde approved the results of <strong>Introduction To Jquery (C001)</strong> for <strong>JHS 1</strong>', '1');
INSERT INTO `users_activity_logs` (`id`, `client_id`, `item_id`, `user_id`, `subject`, `source`, `previous_record`, `date_recorded`, `user_agent`, `description`, `status`) VALUES
(92, 'TLIS0000001', '1DTUACe7nhq0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'report_result', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-19 06:54:38', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde approved the results of <strong>Introduction To Jquery (C001)</strong> for <strong>JHS 1</strong>', '1'),
(93, 'TLIS0000001', '1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-20 08:29:28', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong></strong> With ID: 1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', '1'),
(94, 'TLIS0000001', '1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-20 08:30:25', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Ebenezer Franklin Hyde</strong> With ID: 1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', '1'),
(95, 'TLIS0000001', 'qkrNWbzA3EoZLaSleY5T4291ICQsmvdD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-20 09:20:19', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Philip Anthony dadzie</strong> With ID: qkrNWbzA3EoZLaSleY5T4291ICQsmvdD', '1'),
(96, 'TLIS0000001', '1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-20 09:40:16', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Introduction To Jquery JHS 1</strong> With ID: 1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', '1'),
(97, 'TLIS0000001', 'T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-20 09:40:16', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Introduction To Jquery JHS 1</strong> With ID: T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK', '1'),
(98, 'TLIS0000001', 'qkrNWbzA3EoZLaSleY5T4291ICQsmvdD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-20 09:40:16', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Introduction To Jquery JHS 1</strong> With ID: qkrNWbzA3EoZLaSleY5T4291ICQsmvdD', '1'),
(99, 'TLIS0000001', '1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-20 09:40:55', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Introduction To Jquery JHS 1</strong> With ID: 1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', '1'),
(100, 'TLIS0000001', 'T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-20 09:40:55', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Introduction To Jquery JHS 1</strong> With ID: T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK', '1'),
(101, 'TLIS0000001', 'qkrNWbzA3EoZLaSleY5T4291ICQsmvdD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-20 09:40:56', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Introduction To Jquery JHS 1</strong> With ID: qkrNWbzA3EoZLaSleY5T4291ICQsmvdD', '1'),
(102, 'TLIS0000001', '1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-20 09:42:40', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Introduction To Jquery JHS 1</strong> With ID: 1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', '1'),
(103, 'TLIS0000001', 'T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-20 09:42:40', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Introduction To Jquery JHS 1</strong> With ID: T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK', '1'),
(104, 'TLIS0000001', 'qkrNWbzA3EoZLaSleY5T4291ICQsmvdD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-20 09:42:40', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Introduction To Jquery JHS 1</strong> With ID: qkrNWbzA3EoZLaSleY5T4291ICQsmvdD', '1'),
(105, 'TLIS0000001', '1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-20 09:42:48', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Introduction To Jquery JHS 1</strong> With ID: 1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', '1'),
(106, 'TLIS0000001', 'T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-20 09:42:48', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Introduction To Jquery JHS 1</strong> With ID: T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK', '1'),
(107, 'TLIS0000001', 'qkrNWbzA3EoZLaSleY5T4291ICQsmvdD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-20 09:42:48', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Introduction To Jquery JHS 1</strong> With ID: qkrNWbzA3EoZLaSleY5T4291ICQsmvdD', '1'),
(108, 'TLIS0000001', '1DTUACe7nhq0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'report_result', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-20 21:31:09', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde approved the results of <strong>Introduction To Jquery (C001)</strong> for <strong>JHS 1</strong>', '1'),
(109, 'TLIS0000001', '7pcUdSFJ2au8', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'report_result', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-20 21:46:57', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde submitted the results of <strong>Basics Of Programming (C002)</strong> for <strong>JHS 1</strong>', '1'),
(110, 'TLIS0000001', '1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-20 21:48:20', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Basics Of Programming JHS 1</strong> With ID: 1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', '1'),
(111, 'TLIS0000001', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-20 21:48:20', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Basics Of Programming JHS 1</strong> With ID: GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', '1'),
(112, 'TLIS0000001', 'T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-20 21:48:20', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Basics Of Programming JHS 1</strong> With ID: T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK', '1'),
(113, 'TLIS0000001', 'qkrNWbzA3EoZLaSleY5T4291ICQsmvdD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-20 21:48:20', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Basics Of Programming JHS 1</strong> With ID: qkrNWbzA3EoZLaSleY5T4291ICQsmvdD', '1'),
(114, 'TLIS0000001', '1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-20 21:50:45', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Basics Of Programming JHS 1</strong> With ID: 1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', '1'),
(115, 'TLIS0000001', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-20 21:50:45', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Basics Of Programming JHS 1</strong> With ID: GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', '1'),
(116, 'TLIS0000001', 'T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-20 21:50:45', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Basics Of Programming JHS 1</strong> With ID: T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK', '1'),
(117, 'TLIS0000001', 'qkrNWbzA3EoZLaSleY5T4291ICQsmvdD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-03-20 21:50:45', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Basics Of Programming JHS 1</strong> With ID: qkrNWbzA3EoZLaSleY5T4291ICQsmvdD', '1'),
(118, 'TLIS0000001', '5sLO0GR2qYThuQK39CzNc1gy4xlHv8PJ', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'courses', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"3\",\"upload_id\":\"gWPeAo9kBm5c\",\"item_id\":\"5sLO0GR2qYThuQK39CzNc1gy4xlHv8PJ\",\"client_id\":\"TLIS0000001\",\"course_code\":\"c003\",\"credit_hours\":\"4\",\"academic_term\":\"1st\",\"academic_year\":\"2020\\/2021\",\"department_id\":null,\"programme_id\":null,\"weekly_meeting\":\"12\",\"class_id\":null,\"name\":\"object oriented programming\",\"slug\":null,\"units_count\":\"0\",\"lessons_count\":\"0\",\"course_tutor\":null,\"description\":\"this is for object oriented programming\",\"date_created\":\"2021-02-20\",\"created_by\":\"JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM\",\"date_updated\":\"2021-02-20 09:15:39\",\"status\":\"1\",\"deleted\":\"0\"}', '2021-04-17 14:26:11', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the Course: object oriented programming', '1'),
(119, 'TLIS0000001', '5sLO0GR2qYThuQK39CzNc1gy4xlHv8PJ', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'courses', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"3\",\"upload_id\":\"gWPeAo9kBm5c\",\"item_id\":\"5sLO0GR2qYThuQK39CzNc1gy4xlHv8PJ\",\"client_id\":\"TLIS0000001\",\"course_code\":\"c003\",\"credit_hours\":\"4\",\"academic_term\":\"1st\",\"academic_year\":\"2020\\/2021\",\"department_id\":null,\"programme_id\":null,\"weekly_meeting\":\"12\",\"class_id\":\"[\\\"B0wVXoJq8cLnaeOWC4EvkRzmhiHSlMry\\\"]\",\"name\":\"object oriented programming\",\"slug\":\"object-oriented-programming\",\"units_count\":\"0\",\"lessons_count\":\"0\",\"course_tutor\":\"[]\",\"description\":\"this is for object oriented programming\",\"date_created\":\"2021-02-20\",\"created_by\":\"JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM\",\"date_updated\":\"2021-04-17 14:26:11\",\"status\":\"1\",\"deleted\":\"0\"}', '2021-04-17 14:26:17', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the Course: object oriented programming', '1'),
(120, 'TLIS0000001', 'LJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"18\"},{\"item\":\"exams_score\",\"score\":\"55\"}],\"remarks\":\"Good student needs more improvement\",\"total_score\":73}', '2021-04-17 14:30:27', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: LJKDFLAA3', '1'),
(121, 'TLIS0000001', 'IURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"24\"},{\"item\":\"exams_score\",\"score\":\"63\"}],\"remarks\":\"Improving steadily.\",\"total_score\":87}', '2021-04-17 14:30:27', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: IURIEKJFD', '1'),
(122, 'TLIS0000001', 'ALJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"25\"},{\"item\":\"exams_score\",\"score\":\"57\"}],\"remarks\":\"Can do better\",\"total_score\":82}', '2021-04-17 14:30:27', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: ALJKDFLAA3', '1'),
(123, 'TLIS0000001', 'AIURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"20\"},{\"item\":\"exams_score\",\"score\":\"64\"}],\"remarks\":\"Excellent performance\",\"total_score\":84}', '2021-04-17 14:30:28', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: AIURIEKJFD', '1'),
(124, 'TLIS0000001', 'LJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"27\"},{\"item\":\"exams_score\",\"score\":\"55\"}],\"remarks\":\"Good student needs more improvement\",\"total_score\":82}', '2021-04-17 14:33:56', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: LJKDFLAA3', '1'),
(125, 'TLIS0000001', 'IURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"10\"},{\"item\":\"exams_score\",\"score\":\"63\"}],\"remarks\":\"Improving steadily.\",\"total_score\":73}', '2021-04-17 14:33:56', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: IURIEKJFD', '1'),
(126, 'TLIS0000001', 'ALJKDFLAA3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"29\"},{\"item\":\"exams_score\",\"score\":\"57\"}],\"remarks\":\"Can do better\",\"total_score\":86}', '2021-04-17 14:33:56', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: ALJKDFLAA3', '1'),
(127, 'TLIS0000001', 'AIURIEKJFD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"marks\":[{\"item\":\"class_score\",\"score\":\"26\"},{\"item\":\"exams_score\",\"score\":\"64\"}],\"remarks\":\"Excellent performance\",\"total_score\":90}', '2021-04-17 14:33:56', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde uploaded the terminal report for 1st 2020/2021 Academic Year of Student With ID: AIURIEKJFD', '1'),
(128, 'TLIS0000001', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-17 14:34:13', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Object Oriented Programming JHS 1</strong> With ID: 0', '1'),
(129, 'TLIS0000001', '1', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-17 14:34:13', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Object Oriented Programming JHS 1</strong> With ID: 1', '1'),
(130, 'TLIS0000001', '2', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-17 14:34:13', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Object Oriented Programming JHS 1</strong> With ID: 2', '1'),
(131, 'TLIS0000001', '1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-17 14:37:22', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Object Oriented Programming JHS 1</strong> With ID: 1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', '1'),
(132, 'TLIS0000001', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-17 14:37:22', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Object Oriented Programming JHS 1</strong> With ID: GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', '1'),
(133, 'TLIS0000001', 'T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-17 14:37:23', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Object Oriented Programming JHS 1</strong> With ID: T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK', '1'),
(134, 'TLIS0000001', 'qkrNWbzA3EoZLaSleY5T4291ICQsmvdD', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-17 14:37:23', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Object Oriented Programming JHS 1</strong> With ID: qkrNWbzA3EoZLaSleY5T4291ICQsmvdD', '1'),
(135, 'TLIS0000001', '7AC2NXYLFPRTBO31', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'report_result', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-17 14:37:59', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde submitted the results of <strong>Object Oriented Programming (C003)</strong> for <strong>JHS 1</strong>', '1'),
(136, 'TLIS0000001', '7AC2NXYLFPRTBO31', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'report_result', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-17 14:38:10', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde approved the results of <strong>Object Oriented Programming (C003)</strong> for <strong>JHS 1</strong>', '1'),
(137, 'TLIS0000001', '7AC2NXYLFPRTBO31', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'report_result', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-17 14:38:17', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde approved the results of <strong>Object Oriented Programming (C003)</strong> for <strong>JHS 1</strong>', '1'),
(138, 'TLIS0000001', '1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-17 14:38:44', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Object Oriented Programming JHS 1</strong> With ID: 1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', '1'),
(139, 'TLIS0000001', 'T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'terminal_report', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-17 14:38:44', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the terminal report for 1st 2020/2021 Academic Year of <strong>Object Oriented Programming JHS 1</strong> With ID: T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK', '1'),
(140, 'TLIS0000001', 'TLIS0000001', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"1\",\"client_id\":\"TLIS0000001\",\"client_name\":\"True Love International School\",\"client_contact\":\"0550107770\",\"client_secondary_contact\":\"0240553604\",\"client_address\":\"P. O. Box AF 2582, Adentan Accra\",\"client_email\":\"emmallob14@gmail.com\",\"client_website\":\"https:\\/\\/www.trueloveinternational.com\",\"client_logo\":\"assets\\/img\\/accounts\\/oEbGi1JVC0BqZp3gwtHKmFQYRAUWjcvl.png\",\"client_location\":\"Dodowa\",\"client_category\":null,\"client_preferences\":{\"academics\":{\"academic_year\":\"2020\\/2021\",\"academic_term\":\"1st\",\"term_starts\":\"2021-01-18\",\"term_ends\":\"2021-03-12\",\"next_academic_year\":\"2021\\/2022\",\"next_academic_term\":\"1st\",\"next_term_starts\":\"2021-03-28\",\"next_term_ends\":\"2021-05-21\"},\"labels\":{\"student_label\":\"sl\",\"parent_label\":\"gl\",\"teacher_label\":\"tl\",\"staff_label\":\"stl\",\"course_label\":\"cl\",\"book_label\":\"bkl\",\"class_label\":\"cl\",\"department_label\":\"dp\",\"section_label\":\"sl\",\"receipt_label\":\"rel\",\"currency\":\"GHS\"},\"opening_days\":[\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\",\"Friday\"]},\"client_status\":\"1\",\"client_state\":\"Active\",\"ip_address\":null,\"date_created\":\"2021-02-24 04:46:36\",\"grading_system\":\"{\\\"1\\\":{\\\"start\\\":\\\"90\\\",\\\"end\\\":\\\"100\\\",\\\"interpretation\\\":\\\"Excellent\\\\\\/Outstanding\\\"},\\\"2\\\":{\\\"start\\\":\\\"80\\\",\\\"end\\\":\\\"89\\\",\\\"interpretation\\\":\\\"Very Good\\\"},\\\"3\\\":{\\\"start\\\":\\\"70\\\",\\\"end\\\":\\\"79\\\",\\\"interpretation\\\":\\\"Good\\\"},\\\"4\\\":{\\\"start\\\":\\\"60\\\",\\\"end\\\":\\\"69\\\",\\\"interpretation\\\":\\\"Fairly Good\\\\\\/Pass\\\"},\\\"5\\\":{\\\"start\\\":\\\"50\\\",\\\"end\\\":\\\"59\\\",\\\"interpretation\\\":\\\"Needs Improvement\\\"},\\\"6\\\":{\\\"start\\\":\\\"0\\\",\\\"end\\\":\\\"49\\\",\\\"interpretation\\\":\\\"Needs Booster Support\\\"}}\",\"grading_structure\":\"{\\\"course_title\\\":\\\"true\\\",\\\"columns\\\":{\\\"Class Score\\\":\\\"30\\\",\\\"Exams Score\\\":\\\"70\\\",\\\"Total Score\\\":\\\"100\\\"},\\\"average_score\\\":\\\"true\\\",\\\"show_position\\\":\\\"true\\\",\\\"show_teacher_name\\\":\\\"true\\\",\\\"teacher_comments\\\":\\\"true\\\"}\",\"show_position\":\"true\",\"show_teacher_name\":\"true\",\"allow_submission\":\"true\"}', '2021-04-17 14:44:24', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the Account Information', '1'),
(141, 'TLIS0000001', 'TLIS0000001', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"1\",\"client_id\":\"TLIS0000001\",\"client_name\":\"True Love International School\",\"client_contact\":\"0550107770\",\"client_secondary_contact\":\"0240553604\",\"client_address\":\"P. O. Box AF 2582, Adentan Accra\",\"client_email\":\"emmallob14@gmail.com\",\"client_website\":\"https:\\/\\/www.trueloveinternational.com\",\"client_logo\":\"assets\\/img\\/accounts\\/oEbGi1JVC0BqZp3gwtHKmFQYRAUWjcvl.png\",\"client_location\":\"Dodowa\",\"client_category\":null,\"client_preferences\":{\"academics\":{\"academic_year\":\"2018\\/2019\",\"academic_term\":\"1st\",\"term_starts\":\"2021-01-18\",\"term_ends\":\"2021-03-12\",\"next_academic_year\":\"2021\\/2022\",\"next_academic_term\":\"1st\",\"next_term_starts\":\"2021-03-28\",\"next_term_ends\":\"2021-05-21\"},\"labels\":{\"student_label\":\"sl\",\"parent_label\":\"gl\",\"teacher_label\":\"tl\",\"staff_label\":\"stl\",\"course_label\":\"cl\",\"book_label\":\"bkl\",\"class_label\":\"cl\",\"department_label\":\"dp\",\"section_label\":\"sl\",\"receipt_label\":\"rel\",\"currency\":\"GHS\"},\"opening_days\":[\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\",\"Friday\"]},\"client_status\":\"1\",\"client_state\":\"Active\",\"ip_address\":null,\"date_created\":\"2021-02-24 04:46:36\",\"grading_system\":\"{\\\"1\\\":{\\\"start\\\":\\\"90\\\",\\\"end\\\":\\\"100\\\",\\\"interpretation\\\":\\\"Excellent\\\\\\/Outstanding\\\"},\\\"2\\\":{\\\"start\\\":\\\"80\\\",\\\"end\\\":\\\"89\\\",\\\"interpretation\\\":\\\"Very Good\\\"},\\\"3\\\":{\\\"start\\\":\\\"70\\\",\\\"end\\\":\\\"79\\\",\\\"interpretation\\\":\\\"Good\\\"},\\\"4\\\":{\\\"start\\\":\\\"60\\\",\\\"end\\\":\\\"69\\\",\\\"interpretation\\\":\\\"Fairly Good\\\\\\/Pass\\\"},\\\"5\\\":{\\\"start\\\":\\\"50\\\",\\\"end\\\":\\\"59\\\",\\\"interpretation\\\":\\\"Needs Improvement\\\"},\\\"6\\\":{\\\"start\\\":\\\"0\\\",\\\"end\\\":\\\"49\\\",\\\"interpretation\\\":\\\"Needs Booster Support\\\"}}\",\"grading_structure\":\"{\\\"course_title\\\":\\\"true\\\",\\\"columns\\\":{\\\"Class Score\\\":\\\"30\\\",\\\"Exams Score\\\":\\\"70\\\",\\\"Total Score\\\":\\\"100\\\"},\\\"average_score\\\":\\\"true\\\",\\\"show_position\\\":\\\"true\\\",\\\"show_teacher_name\\\":\\\"true\\\",\\\"teacher_comments\\\":\\\"true\\\"}\",\"show_position\":\"true\",\"show_teacher_name\":\"true\",\"allow_submission\":\"true\"}', '2021-04-17 14:50:03', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the Account Information', '1'),
(142, 'TLIS0000001', '', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-17 20:41:55', 'Windows 10 | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> added a new endpoint: <strong>terminal_reports/generate</strong> to the resource: <strong>terminal_reports</strong>.', '1'),
(143, 'TLIS0000001', 'hcbopnbvlef9rf8yw61rsmdj4gujpzhx', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"177\",\"item_id\":\"hcbopnbvlef9rf8yw61rsmdj4gujpzhx\",\"version\":\"v1\",\"resource\":\"terminal_reports\",\"endpoint\":\"terminal_reports\\/generate\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"academic_year\\\":\\\"The academic year to generate the report. Default will be the current academic year\\\",\\\"academic_term\\\":\\\"The academic term to generate the report. Default will be the current academic term.\\\",\\\"class_id\\\":\\\"required - This is the class to generate the terminal report cards\\\",\\\"student_id\\\":\\\"This is optional. When set then the report card only this student is generated.\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-04-17 20:41:55\",\"last_updated\":\"2021-04-17 20:41:55\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM\",\"updated_by\":null}', '2021-04-17 22:33:22', 'Windows 10 | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> updated the endpoint.', '1'),
(144, 'TLIS0000001', '1', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-20 12:59:39', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde logged attendance for <strong>JHS 1</strong> on 2021-04-01.', '1'),
(145, 'TLIS0000001', NULL, 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"unique_id\\\":\\\"LJKDFLAA3\\\",\\\"name\\\":\\\"Ebenezer Franklin Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"unique_id\\\":\\\"IURIEKJFD\\\",\\\"name\\\":\\\"julian asamoah dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\",\\\"unique_id\\\":\\\"ALJKDFLAA3\\\",\\\"name\\\":\\\"George Anderson Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"absent\\\"},{\\\"item_id\\\":\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\",\\\"unique_id\\\":\\\"AIURIEKJFD\\\",\\\"name\\\":\\\"Philip Anthony dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-04-20 12:59:43', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>JHS 1</strong> on 2021-04-01. ', '1'),
(146, 'TLIS0000001', NULL, 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"unique_id\\\":\\\"LJKDFLAA3\\\",\\\"name\\\":\\\"Ebenezer Franklin Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"unique_id\\\":\\\"IURIEKJFD\\\",\\\"name\\\":\\\"julian asamoah dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\",\\\"unique_id\\\":\\\"ALJKDFLAA3\\\",\\\"name\\\":\\\"George Anderson Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"absent\\\"},{\\\"item_id\\\":\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\",\\\"unique_id\\\":\\\"AIURIEKJFD\\\",\\\"name\\\":\\\"Philip Anthony dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-04-20 13:00:36', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>JHS 1</strong> on 2021-04-01. ', '1'),
(147, 'TLIS0000001', NULL, 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"unique_id\\\":\\\"LJKDFLAA3\\\",\\\"name\\\":\\\"Ebenezer Franklin Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"unique_id\\\":\\\"IURIEKJFD\\\",\\\"name\\\":\\\"julian asamoah dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\",\\\"unique_id\\\":\\\"ALJKDFLAA3\\\",\\\"name\\\":\\\"George Anderson Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"absent\\\"},{\\\"item_id\\\":\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\",\\\"unique_id\\\":\\\"AIURIEKJFD\\\",\\\"name\\\":\\\"Philip Anthony dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-04-20 13:01:05', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>JHS 1</strong> on 2021-04-01. ', '1'),
(148, 'TLIS0000001', '1', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"unique_id\\\":\\\"LJKDFLAA3\\\",\\\"name\\\":\\\"Ebenezer Franklin Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"unique_id\\\":\\\"IURIEKJFD\\\",\\\"name\\\":\\\"julian asamoah dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\",\\\"unique_id\\\":\\\"ALJKDFLAA3\\\",\\\"name\\\":\\\"George Anderson Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"absent\\\"},{\\\"item_id\\\":\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\",\\\"unique_id\\\":\\\"AIURIEKJFD\\\",\\\"name\\\":\\\"Philip Anthony dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-04-20 13:01:08', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>JHS 1</strong> on 2021-04-01. The record was finalized and cannot be changed again.', '1'),
(149, 'TLIS0000001', '2', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-20 13:01:17', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde logged attendance for <strong>JHS 1</strong> on 2021-04-02.', '1'),
(150, 'TLIS0000001', '2', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\",\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"unique_id\\\":\\\"LJKDFLAA3\\\",\\\"name\\\":\\\"Ebenezer Franklin Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"unique_id\\\":\\\"IURIEKJFD\\\",\\\"name\\\":\\\"julian asamoah dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\",\\\"unique_id\\\":\\\"ALJKDFLAA3\\\",\\\"name\\\":\\\"George Anderson Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\",\\\"unique_id\\\":\\\"AIURIEKJFD\\\",\\\"name\\\":\\\"Philip Anthony dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-04-20 13:01:21', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>JHS 1</strong> on 2021-04-02. The record was finalized and cannot be changed again.', '1'),
(151, 'TLIS0000001', 'TLIS0000001', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"1\",\"client_id\":\"TLIS0000001\",\"client_name\":\"True Love International School\",\"client_contact\":\"0550107770\",\"client_secondary_contact\":\"0240553604\",\"client_address\":\"P. O. Box AF 2582, Adentan Accra\",\"client_email\":\"emmallob14@gmail.com\",\"client_website\":\"https:\\/\\/www.trueloveinternational.com\",\"client_logo\":\"assets\\/img\\/accounts\\/oEbGi1JVC0BqZp3gwtHKmFQYRAUWjcvl.png\",\"client_location\":\"Dodowa\",\"client_category\":null,\"client_preferences\":{\"academics\":{\"academic_year\":\"2020\\/2021\",\"academic_term\":\"1st\",\"term_starts\":\"2021-01-18\",\"term_ends\":\"2021-03-12\",\"next_academic_year\":\"2021\\/2022\",\"next_academic_term\":\"1st\",\"next_term_starts\":\"2021-03-28\",\"next_term_ends\":\"2021-05-21\"},\"labels\":{\"student_label\":\"sl\",\"parent_label\":\"gl\",\"teacher_label\":\"tl\",\"staff_label\":\"stl\",\"course_label\":\"cl\",\"book_label\":\"bkl\",\"class_label\":\"cl\",\"department_label\":\"dp\",\"section_label\":\"sl\",\"receipt_label\":\"rel\",\"currency\":\"GHS\"},\"opening_days\":[\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\",\"Friday\"]},\"client_status\":\"1\",\"client_state\":\"Active\",\"ip_address\":null,\"date_created\":\"2021-02-24 04:46:36\",\"grading_system\":{\"1\":{\"start\":\"90\",\"end\":\"100\",\"interpretation\":\"Excellent\\/Outstanding\"},\"2\":{\"start\":\"80\",\"end\":\"89\",\"interpretation\":\"Very Good\"},\"3\":{\"start\":\"70\",\"end\":\"79\",\"interpretation\":\"Good\"},\"4\":{\"start\":\"60\",\"end\":\"69\",\"interpretation\":\"Fairly Good\\/Pass\"},\"5\":{\"start\":\"50\",\"end\":\"59\",\"interpretation\":\"Needs Improvement\"},\"6\":{\"start\":\"0\",\"end\":\"49\",\"interpretation\":\"Needs Booster Support\"}},\"grading_structure\":{\"course_title\":\"true\",\"columns\":{\"Class Score\":\"30\",\"Exams Score\":\"70\",\"Total Score\":\"100\"},\"average_score\":\"true\",\"show_position\":\"true\",\"show_teacher_name\":\"true\",\"allow_submission\":\"true\",\"teacher_comments\":\"true\"},\"show_position\":\"true\",\"show_teacher_name\":\"true\",\"allow_submission\":\"true\"}', '2021-04-20 13:02:08', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the Account Information', '1'),
(152, 'TLIS0000001', 'TLIS0000001', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"1\",\"client_id\":\"TLIS0000001\",\"client_name\":\"True Love International School\",\"client_contact\":\"0550107770\",\"client_secondary_contact\":\"0240553604\",\"client_address\":\"P. O. Box AF 2582, Adentan Accra\",\"client_email\":\"emmallob14@gmail.com\",\"client_website\":\"https:\\/\\/www.trueloveinternational.com\",\"client_logo\":\"assets\\/img\\/accounts\\/oEbGi1JVC0BqZp3gwtHKmFQYRAUWjcvl.png\",\"client_location\":\"Dodowa\",\"client_category\":null,\"client_preferences\":{\"academics\":{\"academic_year\":\"2020\\/2021\",\"academic_term\":\"1st\",\"term_starts\":\"2021-01-18\",\"term_ends\":\"2021-03-31\",\"next_academic_year\":\"2021\\/2022\",\"next_academic_term\":\"1st\",\"next_term_starts\":\"2021-03-28\",\"next_term_ends\":\"2021-05-21\"},\"labels\":{\"student_label\":\"sl\",\"parent_label\":\"gl\",\"teacher_label\":\"tl\",\"staff_label\":\"stl\",\"course_label\":\"cl\",\"book_label\":\"bkl\",\"class_label\":\"cl\",\"department_label\":\"dp\",\"section_label\":\"sl\",\"receipt_label\":\"rel\",\"currency\":\"GHS\"},\"opening_days\":[\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\",\"Friday\"]},\"client_status\":\"1\",\"client_state\":\"Active\",\"ip_address\":null,\"date_created\":\"2021-02-24 04:46:36\",\"grading_system\":{\"1\":{\"start\":\"90\",\"end\":\"100\",\"interpretation\":\"Excellent\\/Outstanding\"},\"2\":{\"start\":\"80\",\"end\":\"89\",\"interpretation\":\"Very Good\"},\"3\":{\"start\":\"70\",\"end\":\"79\",\"interpretation\":\"Good\"},\"4\":{\"start\":\"60\",\"end\":\"69\",\"interpretation\":\"Fairly Good\\/Pass\"},\"5\":{\"start\":\"50\",\"end\":\"59\",\"interpretation\":\"Needs Improvement\"},\"6\":{\"start\":\"0\",\"end\":\"49\",\"interpretation\":\"Needs Booster Support\"}},\"grading_structure\":{\"course_title\":\"true\",\"columns\":{\"Class Score\":\"30\",\"Exams Score\":\"70\",\"Total Score\":\"100\"},\"average_score\":\"true\",\"show_position\":\"true\",\"show_teacher_name\":\"true\",\"allow_submission\":\"true\",\"teacher_comments\":\"true\"},\"show_position\":\"true\",\"show_teacher_name\":\"true\",\"allow_submission\":\"true\"}', '2021-04-20 13:15:47', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the Account Information', '1'),
(153, 'TLIS0000001', 'TLIS0000001', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"1\",\"client_id\":\"TLIS0000001\",\"client_name\":\"True Love International School\",\"client_contact\":\"0550107770\",\"client_secondary_contact\":\"0240553604\",\"client_address\":\"P. O. Box AF 2582, Adentan Accra\",\"client_email\":\"emmallob14@gmail.com\",\"client_website\":\"https:\\/\\/www.trueloveinternational.com\",\"client_logo\":\"assets\\/img\\/accounts\\/oEbGi1JVC0BqZp3gwtHKmFQYRAUWjcvl.png\",\"client_location\":\"Dodowa\",\"client_category\":null,\"client_preferences\":{\"academics\":{\"academic_year\":\"2020\\/2021\",\"academic_term\":\"1st\",\"term_starts\":\"2021-01-18\",\"term_ends\":\"2021-04-16\",\"next_academic_year\":\"2021\\/2022\",\"next_academic_term\":\"1st\",\"next_term_starts\":\"2021-03-28\",\"next_term_ends\":\"2021-05-21\"},\"labels\":{\"student_label\":\"sl\",\"parent_label\":\"gl\",\"teacher_label\":\"tl\",\"staff_label\":\"stl\",\"course_label\":\"cl\",\"book_label\":\"bkl\",\"class_label\":\"cl\",\"department_label\":\"dp\",\"section_label\":\"sl\",\"receipt_label\":\"rel\",\"currency\":\"GHS\"},\"opening_days\":[\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\",\"Friday\"]},\"client_status\":\"1\",\"client_state\":\"Active\",\"ip_address\":null,\"date_created\":\"2021-02-24 04:46:36\",\"grading_system\":{\"1\":{\"start\":\"90\",\"end\":\"100\",\"interpretation\":\"Excellent\\/Outstanding\"},\"2\":{\"start\":\"80\",\"end\":\"89\",\"interpretation\":\"Very Good\"},\"3\":{\"start\":\"70\",\"end\":\"79\",\"interpretation\":\"Good\"},\"4\":{\"start\":\"60\",\"end\":\"69\",\"interpretation\":\"Fairly Good\\/Pass\"},\"5\":{\"start\":\"50\",\"end\":\"59\",\"interpretation\":\"Needs Improvement\"},\"6\":{\"start\":\"0\",\"end\":\"49\",\"interpretation\":\"Needs Booster Support\"}},\"grading_structure\":{\"course_title\":\"true\",\"columns\":{\"Class Score\":\"30\",\"Exams Score\":\"70\",\"Total Score\":\"100\"},\"average_score\":\"true\",\"show_position\":\"true\",\"show_teacher_name\":\"true\",\"allow_submission\":\"true\",\"teacher_comments\":\"true\"},\"show_position\":\"true\",\"show_teacher_name\":\"true\",\"allow_submission\":\"true\"}', '2021-04-20 13:22:47', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the Account Information', '1'),
(154, 'TLIS0000001', '3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-20 13:23:21', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde logged attendance for <strong>JHS 1</strong> on 2021-04-05.', '1'),
(155, 'TLIS0000001', '3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\",\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"unique_id\\\":\\\"LJKDFLAA3\\\",\\\"name\\\":\\\"Ebenezer Franklin Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"unique_id\\\":\\\"IURIEKJFD\\\",\\\"name\\\":\\\"julian asamoah dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"absent\\\"},{\\\"item_id\\\":\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\",\\\"unique_id\\\":\\\"ALJKDFLAA3\\\",\\\"name\\\":\\\"George Anderson Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\",\\\"unique_id\\\":\\\"AIURIEKJFD\\\",\\\"name\\\":\\\"Philip Anthony dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-04-20 13:23:24', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>JHS 1</strong> on 2021-04-05. The record was finalized and cannot be changed again.', '1'),
(156, 'TLIS0000001', '4', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-20 13:23:32', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde logged attendance for <strong>JHS 1</strong> on 2021-04-06.', '1'),
(157, 'TLIS0000001', '4', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\",\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"unique_id\\\":\\\"LJKDFLAA3\\\",\\\"name\\\":\\\"Ebenezer Franklin Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"unique_id\\\":\\\"IURIEKJFD\\\",\\\"name\\\":\\\"julian asamoah dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\",\\\"unique_id\\\":\\\"ALJKDFLAA3\\\",\\\"name\\\":\\\"George Anderson Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\",\\\"unique_id\\\":\\\"AIURIEKJFD\\\",\\\"name\\\":\\\"Philip Anthony dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-04-20 13:23:35', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>JHS 1</strong> on 2021-04-06. The record was finalized and cannot be changed again.', '1'),
(158, 'TLIS0000001', '5', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-20 13:23:42', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde logged attendance for <strong>JHS 1</strong> on 2021-04-07.', '1'),
(159, 'TLIS0000001', '5', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\",\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"unique_id\\\":\\\"LJKDFLAA3\\\",\\\"name\\\":\\\"Ebenezer Franklin Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"unique_id\\\":\\\"IURIEKJFD\\\",\\\"name\\\":\\\"julian asamoah dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\",\\\"unique_id\\\":\\\"ALJKDFLAA3\\\",\\\"name\\\":\\\"George Anderson Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\",\\\"unique_id\\\":\\\"AIURIEKJFD\\\",\\\"name\\\":\\\"Philip Anthony dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-04-20 13:23:45', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>JHS 1</strong> on 2021-04-07. The record was finalized and cannot be changed again.', '1');
INSERT INTO `users_activity_logs` (`id`, `client_id`, `item_id`, `user_id`, `subject`, `source`, `previous_record`, `date_recorded`, `user_agent`, `description`, `status`) VALUES
(160, 'TLIS0000001', '6', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-20 13:23:52', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde logged attendance for <strong>JHS 1</strong> on 2021-04-08.', '1'),
(161, 'TLIS0000001', '6', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"unique_id\\\":\\\"LJKDFLAA3\\\",\\\"name\\\":\\\"Ebenezer Franklin Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"unique_id\\\":\\\"IURIEKJFD\\\",\\\"name\\\":\\\"julian asamoah dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\",\\\"unique_id\\\":\\\"ALJKDFLAA3\\\",\\\"name\\\":\\\"George Anderson Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\",\\\"unique_id\\\":\\\"AIURIEKJFD\\\",\\\"name\\\":\\\"Philip Anthony dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"absent\\\"}]\",\"finalize\":\"0\"}', '2021-04-20 13:23:56', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>JHS 1</strong> on 2021-04-08. The record was finalized and cannot be changed again.', '1'),
(162, 'TLIS0000001', '7', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-20 13:24:27', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde logged attendance for <strong>teacher</strong> on 2021-04-01.', '1'),
(163, 'TLIS0000001', '7', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\\\",\\\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\\\",\\\"unique_id\\\":\\\"P00001\\\",\\\"name\\\":\\\"fredrick amponsah badu\\\",\\\"email\\\":\\\"fredamponsah@gmail.com\\\",\\\"phone_number\\\":\\\"490993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\\\",\\\"unique_id\\\":\\\"P00002\\\",\\\"name\\\":\\\"henry asmah \\\",\\\"email\\\":\\\"asmahhenry@gmail.com\\\",\\\"phone_number\\\":\\\"9093009192\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-04-20 13:24:30', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>teacher</strong> on 2021-04-01. The record was finalized and cannot be changed again.', '1'),
(164, 'TLIS0000001', '8', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-20 13:24:40', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde logged attendance for <strong>teacher</strong> on 2021-04-05.', '1'),
(165, 'TLIS0000001', '8', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\\\",\\\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\\\",\\\"unique_id\\\":\\\"P00001\\\",\\\"name\\\":\\\"fredrick amponsah badu\\\",\\\"email\\\":\\\"fredamponsah@gmail.com\\\",\\\"phone_number\\\":\\\"490993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\\\",\\\"unique_id\\\":\\\"P00002\\\",\\\"name\\\":\\\"henry asmah \\\",\\\"email\\\":\\\"asmahhenry@gmail.com\\\",\\\"phone_number\\\":\\\"9093009192\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-04-20 13:24:44', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>teacher</strong> on 2021-04-05. The record was finalized and cannot be changed again.', '1'),
(166, 'TLIS0000001', '9', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-20 13:24:50', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde logged attendance for <strong>teacher</strong> on 2021-04-06.', '1'),
(167, 'TLIS0000001', '9', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\\\",\\\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\\\",\\\"unique_id\\\":\\\"P00001\\\",\\\"name\\\":\\\"fredrick amponsah badu\\\",\\\"email\\\":\\\"fredamponsah@gmail.com\\\",\\\"phone_number\\\":\\\"490993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\\\",\\\"unique_id\\\":\\\"P00002\\\",\\\"name\\\":\\\"henry asmah \\\",\\\"email\\\":\\\"asmahhenry@gmail.com\\\",\\\"phone_number\\\":\\\"9093009192\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-04-20 13:24:53', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>teacher</strong> on 2021-04-06. The record was finalized and cannot be changed again.', '1'),
(168, 'TLIS0000001', '10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-20 13:24:59', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde logged attendance for <strong>teacher</strong> on 2021-04-07.', '1'),
(169, 'TLIS0000001', '10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\\\",\\\"unique_id\\\":\\\"P00001\\\",\\\"name\\\":\\\"fredrick amponsah badu\\\",\\\"email\\\":\\\"fredamponsah@gmail.com\\\",\\\"phone_number\\\":\\\"490993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\\\",\\\"unique_id\\\":\\\"P00002\\\",\\\"name\\\":\\\"henry asmah \\\",\\\"email\\\":\\\"asmahhenry@gmail.com\\\",\\\"phone_number\\\":\\\"9093009192\\\",\\\"state\\\":\\\"absent\\\"}]\",\"finalize\":\"0\"}', '2021-04-20 13:25:02', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>teacher</strong> on 2021-04-07. The record was finalized and cannot be changed again.', '1'),
(170, 'TLIS0000001', '11', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-20 13:25:10', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde logged attendance for <strong>teacher</strong> on 2021-04-08.', '1'),
(171, 'TLIS0000001', '11', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\\\",\\\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\\\",\\\"unique_id\\\":\\\"P00001\\\",\\\"name\\\":\\\"fredrick amponsah badu\\\",\\\"email\\\":\\\"fredamponsah@gmail.com\\\",\\\"phone_number\\\":\\\"490993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\\\",\\\"unique_id\\\":\\\"P00002\\\",\\\"name\\\":\\\"henry asmah \\\",\\\"email\\\":\\\"asmahhenry@gmail.com\\\",\\\"phone_number\\\":\\\"9093009192\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-04-20 13:25:13', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>teacher</strong> on 2021-04-08. The record was finalized and cannot be changed again.', '1'),
(172, 'TLIS0000001', '12', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-20 13:25:27', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde logged attendance for <strong>JHS 1</strong> on 2021-04-09.', '1'),
(173, 'TLIS0000001', '12', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\",\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"unique_id\\\":\\\"LJKDFLAA3\\\",\\\"name\\\":\\\"Ebenezer Franklin Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"unique_id\\\":\\\"IURIEKJFD\\\",\\\"name\\\":\\\"julian asamoah dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\",\\\"unique_id\\\":\\\"ALJKDFLAA3\\\",\\\"name\\\":\\\"George Anderson Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\",\\\"unique_id\\\":\\\"AIURIEKJFD\\\",\\\"name\\\":\\\"Philip Anthony dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-04-20 13:25:30', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>JHS 1</strong> on 2021-04-09. The record was finalized and cannot be changed again.', '1'),
(174, 'TLIS0000001', '13', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-20 13:25:40', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde logged attendance for <strong>JHS 1</strong> on 2021-04-12.', '1'),
(175, 'TLIS0000001', '13', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"unique_id\\\":\\\"LJKDFLAA3\\\",\\\"name\\\":\\\"Ebenezer Franklin Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"unique_id\\\":\\\"IURIEKJFD\\\",\\\"name\\\":\\\"julian asamoah dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"absent\\\"},{\\\"item_id\\\":\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\",\\\"unique_id\\\":\\\"ALJKDFLAA3\\\",\\\"name\\\":\\\"George Anderson Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\",\\\"unique_id\\\":\\\"AIURIEKJFD\\\",\\\"name\\\":\\\"Philip Anthony dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"absent\\\"}]\",\"finalize\":\"0\"}', '2021-04-20 13:25:42', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>JHS 1</strong> on 2021-04-12. The record was finalized and cannot be changed again.', '1'),
(176, 'TLIS0000001', '14', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-20 13:25:51', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde logged attendance for <strong>JHS 1</strong> on 2021-04-13.', '1'),
(177, 'TLIS0000001', '14', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"unique_id\\\":\\\"LJKDFLAA3\\\",\\\"name\\\":\\\"Ebenezer Franklin Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"unique_id\\\":\\\"IURIEKJFD\\\",\\\"name\\\":\\\"julian asamoah dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"absent\\\"},{\\\"item_id\\\":\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\",\\\"unique_id\\\":\\\"ALJKDFLAA3\\\",\\\"name\\\":\\\"George Anderson Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\",\\\"unique_id\\\":\\\"AIURIEKJFD\\\",\\\"name\\\":\\\"Philip Anthony dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"absent\\\"}]\",\"finalize\":\"0\"}', '2021-04-20 13:25:53', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>JHS 1</strong> on 2021-04-13. The record was finalized and cannot be changed again.', '1'),
(178, 'TLIS0000001', '15', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-20 13:26:00', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde logged attendance for <strong>JHS 1</strong> on 2021-04-14.', '1'),
(179, 'TLIS0000001', '15', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\",\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"unique_id\\\":\\\"LJKDFLAA3\\\",\\\"name\\\":\\\"Ebenezer Franklin Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"unique_id\\\":\\\"IURIEKJFD\\\",\\\"name\\\":\\\"julian asamoah dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\",\\\"unique_id\\\":\\\"ALJKDFLAA3\\\",\\\"name\\\":\\\"George Anderson Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\",\\\"unique_id\\\":\\\"AIURIEKJFD\\\",\\\"name\\\":\\\"Philip Anthony dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-04-20 13:26:03', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>JHS 1</strong> on 2021-04-14. The record was finalized and cannot be changed again.', '1'),
(180, 'TLIS0000001', '16', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-20 13:26:58', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde logged attendance for <strong>JHS 1</strong> on 2021-04-20.', '1'),
(181, 'TLIS0000001', '16', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"unique_id\\\":\\\"LJKDFLAA3\\\",\\\"name\\\":\\\"Ebenezer Franklin Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"unique_id\\\":\\\"IURIEKJFD\\\",\\\"name\\\":\\\"julian asamoah dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\",\\\"unique_id\\\":\\\"ALJKDFLAA3\\\",\\\"name\\\":\\\"George Anderson Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\",\\\"unique_id\\\":\\\"AIURIEKJFD\\\",\\\"name\\\":\\\"Philip Anthony dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"absent\\\"}]\",\"finalize\":\"0\"}', '2021-04-20 13:27:00', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>JHS 1</strong> on 2021-04-20. The record was finalized and cannot be changed again.', '1'),
(182, 'TLIS0000001', '17', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-20 13:27:09', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde logged attendance for <strong>JHS 1</strong> on 2021-04-19.', '1'),
(183, 'TLIS0000001', '17', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\",\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"unique_id\\\":\\\"LJKDFLAA3\\\",\\\"name\\\":\\\"Ebenezer Franklin Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"unique_id\\\":\\\"IURIEKJFD\\\",\\\"name\\\":\\\"julian asamoah dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\",\\\"unique_id\\\":\\\"ALJKDFLAA3\\\",\\\"name\\\":\\\"George Anderson Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\",\\\"unique_id\\\":\\\"AIURIEKJFD\\\",\\\"name\\\":\\\"Philip Anthony dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-04-20 13:27:11', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>JHS 1</strong> on 2021-04-19. The record was finalized and cannot be changed again.', '1'),
(184, 'TLIS0000001', '18', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-20 13:27:17', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde logged attendance for <strong>teacher</strong> on 2021-04-19.', '1'),
(185, 'TLIS0000001', '18', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\\\",\\\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\\\",\\\"unique_id\\\":\\\"P00001\\\",\\\"name\\\":\\\"fredrick amponsah badu\\\",\\\"email\\\":\\\"fredamponsah@gmail.com\\\",\\\"phone_number\\\":\\\"490993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\\\",\\\"unique_id\\\":\\\"P00002\\\",\\\"name\\\":\\\"henry asmah \\\",\\\"email\\\":\\\"asmahhenry@gmail.com\\\",\\\"phone_number\\\":\\\"9093009192\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-04-20 13:27:20', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>teacher</strong> on 2021-04-19. The record was finalized and cannot be changed again.', '1'),
(186, 'TLIS0000001', '19', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-20 13:27:26', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde logged attendance for <strong>teacher</strong> on 2021-04-20.', '1'),
(187, 'TLIS0000001', '19', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\\\",\\\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\\\",\\\"unique_id\\\":\\\"P00001\\\",\\\"name\\\":\\\"fredrick amponsah badu\\\",\\\"email\\\":\\\"fredamponsah@gmail.com\\\",\\\"phone_number\\\":\\\"490993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\\\",\\\"unique_id\\\":\\\"P00002\\\",\\\"name\\\":\\\"henry asmah \\\",\\\"email\\\":\\\"asmahhenry@gmail.com\\\",\\\"phone_number\\\":\\\"9093009192\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-04-20 13:27:28', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>teacher</strong> on 2021-04-20. The record was finalized and cannot be changed again.', '1'),
(188, 'TLIS0000001', '20', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-20 13:27:56', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde logged attendance for <strong>teacher</strong> on 2021-04-16.', '1'),
(189, 'TLIS0000001', '20', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\\\",\\\"unique_id\\\":\\\"P00001\\\",\\\"name\\\":\\\"fredrick amponsah badu\\\",\\\"email\\\":\\\"fredamponsah@gmail.com\\\",\\\"phone_number\\\":\\\"490993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\\\",\\\"unique_id\\\":\\\"P00002\\\",\\\"name\\\":\\\"henry asmah \\\",\\\"email\\\":\\\"asmahhenry@gmail.com\\\",\\\"phone_number\\\":\\\"9093009192\\\",\\\"state\\\":\\\"absent\\\"}]\",\"finalize\":\"0\"}', '2021-04-20 13:27:58', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>teacher</strong> on 2021-04-16. The record was finalized and cannot be changed again.', '1'),
(190, 'TLIS0000001', '21', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-20 13:28:05', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde logged attendance for <strong>teacher</strong> on 2021-04-15.', '1'),
(191, 'TLIS0000001', '21', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\\\",\\\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\\\",\\\"unique_id\\\":\\\"P00001\\\",\\\"name\\\":\\\"fredrick amponsah badu\\\",\\\"email\\\":\\\"fredamponsah@gmail.com\\\",\\\"phone_number\\\":\\\"490993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\\\",\\\"unique_id\\\":\\\"P00002\\\",\\\"name\\\":\\\"henry asmah \\\",\\\"email\\\":\\\"asmahhenry@gmail.com\\\",\\\"phone_number\\\":\\\"9093009192\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-04-20 13:28:07', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>teacher</strong> on 2021-04-15. The record was finalized and cannot be changed again.', '1'),
(192, 'TLIS0000001', '22', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-20 13:28:15', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde logged attendance for <strong>teacher</strong> on 2021-04-14.', '1'),
(193, 'TLIS0000001', '22', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\\\",\\\"unique_id\\\":\\\"P00001\\\",\\\"name\\\":\\\"fredrick amponsah badu\\\",\\\"email\\\":\\\"fredamponsah@gmail.com\\\",\\\"phone_number\\\":\\\"490993093\\\",\\\"state\\\":\\\"absent\\\"},{\\\"item_id\\\":\\\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\\\",\\\"unique_id\\\":\\\"P00002\\\",\\\"name\\\":\\\"henry asmah \\\",\\\"email\\\":\\\"asmahhenry@gmail.com\\\",\\\"phone_number\\\":\\\"9093009192\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-04-20 13:28:17', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>teacher</strong> on 2021-04-14. The record was finalized and cannot be changed again.', '1'),
(194, 'TLIS0000001', '23', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-20 13:28:33', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde logged attendance for <strong>JHS 1</strong> on 2021-04-16.', '1'),
(195, 'TLIS0000001', '23', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"unique_id\\\":\\\"LJKDFLAA3\\\",\\\"name\\\":\\\"Ebenezer Franklin Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"unique_id\\\":\\\"IURIEKJFD\\\",\\\"name\\\":\\\"julian asamoah dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\",\\\"unique_id\\\":\\\"ALJKDFLAA3\\\",\\\"name\\\":\\\"George Anderson Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\",\\\"unique_id\\\":\\\"AIURIEKJFD\\\",\\\"name\\\":\\\"Philip Anthony dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"absent\\\"}]\",\"finalize\":\"0\"}', '2021-04-20 13:28:36', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>JHS 1</strong> on 2021-04-16. The record was finalized and cannot be changed again.', '1'),
(196, 'TLIS0000001', '24', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-20 13:28:44', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde logged attendance for <strong>JHS 1</strong> on 2021-04-15.', '1'),
(197, 'TLIS0000001', '24', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"unique_id\\\":\\\"LJKDFLAA3\\\",\\\"name\\\":\\\"Ebenezer Franklin Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"unique_id\\\":\\\"IURIEKJFD\\\",\\\"name\\\":\\\"julian asamoah dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\",\\\"unique_id\\\":\\\"ALJKDFLAA3\\\",\\\"name\\\":\\\"George Anderson Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"absent\\\"},{\\\"item_id\\\":\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\",\\\"unique_id\\\":\\\"AIURIEKJFD\\\",\\\"name\\\":\\\"Philip Anthony dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-04-20 13:28:46', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>JHS 1</strong> on 2021-04-15. The record was finalized and cannot be changed again.', '1'),
(198, 'TLIS0000001', '25', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-04-20 13:29:28', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde logged attendance for <strong>teacher</strong> on 2021-04-12.', '1'),
(199, 'TLIS0000001', '25', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\\\",\\\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\\\",\\\"unique_id\\\":\\\"P00001\\\",\\\"name\\\":\\\"fredrick amponsah badu\\\",\\\"email\\\":\\\"fredamponsah@gmail.com\\\",\\\"phone_number\\\":\\\"490993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\\\",\\\"unique_id\\\":\\\"P00002\\\",\\\"name\\\":\\\"henry asmah \\\",\\\"email\\\":\\\"asmahhenry@gmail.com\\\",\\\"phone_number\\\":\\\"9093009192\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-04-20 13:29:31', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>teacher</strong> on 2021-04-12. The record was finalized and cannot be changed again.', '1'),
(200, 'TLIS0000001', 'TLIS0000001', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"1\",\"client_id\":\"TLIS0000001\",\"client_name\":\"True Love International School\",\"client_contact\":\"0550107770\",\"client_secondary_contact\":\"0240553604\",\"client_address\":\"P. O. Box AF 2582, Adentan Accra\",\"client_email\":\"emmallob14@gmail.com\",\"client_website\":\"https:\\/\\/www.trueloveinternational.com\",\"client_logo\":\"assets\\/img\\/accounts\\/oEbGi1JVC0BqZp3gwtHKmFQYRAUWjcvl.png\",\"client_location\":\"Dodowa\",\"client_category\":null,\"client_preferences\":{\"academics\":{\"academic_year\":\"2020\\/2021\",\"academic_term\":\"1st\",\"term_starts\":\"2021-01-18\",\"term_ends\":\"2021-04-23\",\"next_academic_year\":\"2021\\/2022\",\"next_academic_term\":\"1st\",\"next_term_starts\":\"2021-03-28\",\"next_term_ends\":\"2021-05-21\"},\"labels\":{\"student_label\":\"sl\",\"parent_label\":\"gl\",\"teacher_label\":\"tl\",\"staff_label\":\"stl\",\"course_label\":\"cl\",\"book_label\":\"bkl\",\"class_label\":\"cl\",\"department_label\":\"dp\",\"section_label\":\"sl\",\"receipt_label\":\"rel\",\"currency\":\"GHS\"},\"opening_days\":[\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\",\"Friday\"]},\"client_status\":\"1\",\"client_state\":\"Active\",\"ip_address\":null,\"date_created\":\"2021-02-24 04:46:36\",\"grading_system\":{\"1\":{\"start\":\"90\",\"end\":\"100\",\"interpretation\":\"Excellent\\/Outstanding\"},\"2\":{\"start\":\"80\",\"end\":\"89\",\"interpretation\":\"Very Good\"},\"3\":{\"start\":\"70\",\"end\":\"79\",\"interpretation\":\"Good\"},\"4\":{\"start\":\"60\",\"end\":\"69\",\"interpretation\":\"Fairly Good\\/Pass\"},\"5\":{\"start\":\"50\",\"end\":\"59\",\"interpretation\":\"Needs Improvement\"},\"6\":{\"start\":\"0\",\"end\":\"49\",\"interpretation\":\"Needs Booster Support\"}},\"grading_structure\":{\"course_title\":\"true\",\"columns\":{\"Class Score\":\"30\",\"Exams Score\":\"70\",\"Total Score\":\"100\"},\"average_score\":\"true\",\"show_position\":\"true\",\"show_teacher_name\":\"true\",\"allow_submission\":\"true\",\"teacher_comments\":\"true\"},\"show_position\":\"true\",\"show_teacher_name\":\"true\",\"allow_submission\":\"true\"}', '2021-05-01 07:17:01', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the Account Information', '1'),
(201, 'TLIS0000001', '26', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 07:17:55', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde logged attendance for <strong>JHS 1</strong> on 2021-04-21.', '1'),
(202, 'TLIS0000001', '26', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\",\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"unique_id\\\":\\\"LJKDFLAA3\\\",\\\"name\\\":\\\"Ebenezer Franklin Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"unique_id\\\":\\\"IURIEKJFD\\\",\\\"name\\\":\\\"julian asamoah dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\",\\\"unique_id\\\":\\\"ALJKDFLAA3\\\",\\\"name\\\":\\\"George Anderson Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\",\\\"unique_id\\\":\\\"AIURIEKJFD\\\",\\\"name\\\":\\\"Philip Anthony dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-05-01 07:18:08', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>JHS 1</strong> on 2021-04-21. The record was finalized and cannot be changed again.', '1'),
(203, 'TLIS0000001', '27', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 07:18:19', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde logged attendance for <strong>JHS 1</strong> on 2021-04-22.', '1'),
(204, 'TLIS0000001', '27', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"unique_id\\\":\\\"LJKDFLAA3\\\",\\\"name\\\":\\\"Ebenezer Franklin Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"unique_id\\\":\\\"IURIEKJFD\\\",\\\"name\\\":\\\"julian asamoah dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"absent\\\"},{\\\"item_id\\\":\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\",\\\"unique_id\\\":\\\"ALJKDFLAA3\\\",\\\"name\\\":\\\"George Anderson Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\",\\\"unique_id\\\":\\\"AIURIEKJFD\\\",\\\"name\\\":\\\"Philip Anthony dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"absent\\\"}]\",\"finalize\":\"0\"}', '2021-05-01 07:18:21', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>JHS 1</strong> on 2021-04-22. The record was finalized and cannot be changed again.', '1'),
(205, 'TLIS0000001', '28', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 07:18:31', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde logged attendance for <strong>JHS 1</strong> on 2021-04-23.', '1'),
(206, 'TLIS0000001', '28', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"unique_id\\\":\\\"LJKDFLAA3\\\",\\\"name\\\":\\\"Ebenezer Franklin Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"unique_id\\\":\\\"IURIEKJFD\\\",\\\"name\\\":\\\"julian asamoah dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\",\\\"unique_id\\\":\\\"ALJKDFLAA3\\\",\\\"name\\\":\\\"George Anderson Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"absent\\\"},{\\\"item_id\\\":\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\",\\\"unique_id\\\":\\\"AIURIEKJFD\\\",\\\"name\\\":\\\"Philip Anthony dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-05-01 07:18:34', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>JHS 1</strong> on 2021-04-23. The record was finalized and cannot be changed again.', '1'),
(207, 'TLIS0000001', '29', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 07:18:51', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde logged attendance for <strong>JHS 1</strong> on 2021-04-26.', '1'),
(208, 'TLIS0000001', '29', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"unique_id\\\":\\\"LJKDFLAA3\\\",\\\"name\\\":\\\"Ebenezer Franklin Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"unique_id\\\":\\\"IURIEKJFD\\\",\\\"name\\\":\\\"julian asamoah dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\",\\\"unique_id\\\":\\\"ALJKDFLAA3\\\",\\\"name\\\":\\\"George Anderson Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"absent\\\"},{\\\"item_id\\\":\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\",\\\"unique_id\\\":\\\"AIURIEKJFD\\\",\\\"name\\\":\\\"Philip Anthony dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-05-01 07:18:53', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>JHS 1</strong> on 2021-04-26. The record was finalized and cannot be changed again.', '1'),
(209, 'TLIS0000001', '30', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 07:19:10', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde logged attendance for <strong>JHS 1</strong> on 2021-04-27.', '1'),
(210, 'TLIS0000001', '30', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"unique_id\\\":\\\"LJKDFLAA3\\\",\\\"name\\\":\\\"Ebenezer Franklin Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"unique_id\\\":\\\"IURIEKJFD\\\",\\\"name\\\":\\\"julian asamoah dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"absent\\\"},{\\\"item_id\\\":\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\",\\\"unique_id\\\":\\\"ALJKDFLAA3\\\",\\\"name\\\":\\\"George Anderson Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"absent\\\"},{\\\"item_id\\\":\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\",\\\"unique_id\\\":\\\"AIURIEKJFD\\\",\\\"name\\\":\\\"Philip Anthony dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-05-01 07:19:13', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>JHS 1</strong> on 2021-04-27. The record was finalized and cannot be changed again.', '1'),
(211, 'TLIS0000001', '31', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 07:19:26', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde logged attendance for <strong>JHS 1</strong> on 2021-04-28.', '1'),
(212, 'TLIS0000001', '31', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\",\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"unique_id\\\":\\\"LJKDFLAA3\\\",\\\"name\\\":\\\"Ebenezer Franklin Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"unique_id\\\":\\\"IURIEKJFD\\\",\\\"name\\\":\\\"julian asamoah dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"absent\\\"},{\\\"item_id\\\":\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\",\\\"unique_id\\\":\\\"ALJKDFLAA3\\\",\\\"name\\\":\\\"George Anderson Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\",\\\"unique_id\\\":\\\"AIURIEKJFD\\\",\\\"name\\\":\\\"Philip Anthony dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-05-01 07:19:28', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>JHS 1</strong> on 2021-04-28. The record was finalized and cannot be changed again.', '1'),
(213, 'TLIS0000001', '32', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 07:19:37', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde logged attendance for <strong>JHS 1</strong> on 2021-04-29.', '1'),
(214, 'TLIS0000001', '32', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\\\",\\\"unique_id\\\":\\\"LJKDFLAA3\\\",\\\"name\\\":\\\"Ebenezer Franklin Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\\\",\\\"unique_id\\\":\\\"IURIEKJFD\\\",\\\"name\\\":\\\"julian asamoah dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\\\",\\\"unique_id\\\":\\\"ALJKDFLAA3\\\",\\\"name\\\":\\\"George Anderson Hyde\\\",\\\"email\\\":\\\"emmallob@mail.com\\\",\\\"phone_number\\\":\\\"983983983\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\\\",\\\"unique_id\\\":\\\"AIURIEKJFD\\\",\\\"name\\\":\\\"Philip Anthony dadzie\\\",\\\"email\\\":\\\"julian@mail.com\\\",\\\"phone_number\\\":\\\"9090993093\\\",\\\"state\\\":\\\"absent\\\"}]\",\"finalize\":\"0\"}', '2021-05-01 07:19:40', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated logged attendance for <strong>JHS 1</strong> on 2021-04-29. The record was finalized and cannot be changed again.', '1'),
(215, 'TLIS0000001', '1', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_category', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 16:52:32', 'Windows 10 | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> added a new category with name: Tuition Fees', '1'),
(216, 'TLIS0000001', '2', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_category', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 16:52:53', 'Windows 10 | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> added a new category with name: ICT Dues', '1'),
(217, 'TLIS0000001', '1', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_category', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 16:53:01', 'Windows 10 | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> updated the category: Tuition Fees', '1'),
(218, 'TLIS0000001', '3', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_category', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 16:53:24', 'Windows 10 | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> added a new category with name: Library Fees', '1'),
(219, 'TLIS0000001', '4', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_category', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 16:53:44', 'Windows 10 | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> added a new category with name: Project Fees', '1'),
(220, 'TLIS0000001', '1', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_category', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 16:53:55', 'Windows 10 | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> updated the category: Tuition Fees', '1'),
(221, 'TLIS0000001', '5', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_category', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 16:54:07', 'Windows 10 | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> added a new category with name: Feeding Fees', '1'),
(222, 'TLIS0000001', '5', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_category', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 16:54:17', 'Windows 10 | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> updated the category: Feeding Fees', '1'),
(223, 'TLIS0000001', '1', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_allocation', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 17:05:13', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde added the fee allocation for <strong>Tuition Fees</strong> of: <strong>GHS 650</strong>', '1'),
(224, 'TLIS0000001', '1', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_allocation', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 17:35:41', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde added the fee allocation for <strong>ICT Dues</strong> of: <strong>GHS 25</strong>', '1'),
(225, 'TLIS0000001', '1', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_allocation', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 17:35:48', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde added the fee allocation for <strong>Library Fees</strong> of: <strong>GHS 30</strong>', '1');
INSERT INTO `users_activity_logs` (`id`, `client_id`, `item_id`, `user_id`, `subject`, `source`, `previous_record`, `date_recorded`, `user_agent`, `description`, `status`) VALUES
(226, 'TLIS0000001', '1', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_allocation', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 17:35:52', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde added the fee allocation for <strong>Project Fees</strong> of: <strong>GHS 50</strong>', '1'),
(227, 'TLIS0000001', '1', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_allocation', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 17:35:56', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde added the fee allocation for <strong>Feeding Fees</strong> of: <strong>GHS 250</strong>', '1'),
(228, 'TLIS0000001', '4xmnf8kpQvz5jSNF6IyCWilh1r2udbH7', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 17:37:49', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of \r\n                <strong>25</strong> as Payment for <strong>ICT Dues</strong> from <strong>julian asamoah dadzie</strong>. \r\n                Outstanding Balance is <strong>0</strong>', '1'),
(229, 'TLIS0000001', '1NKO2F4b7iakzMJVsGmThQvxS0B6PRjl', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 17:38:17', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of \r\n                <strong>600</strong> as Payment for <strong>Tuition Fees</strong> from <strong>julian asamoah dadzie</strong>. \r\n                Outstanding Balance is <strong>50</strong>', '1'),
(230, 'TLIS0000001', 'poFK7ZXTM9xyS4V2riWUE1BLtHe8df0h', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 17:40:54', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of \r\n                <strong>650</strong> as Payment for <strong>Tuition Fees</strong> from <strong>Philip Anthony dadzie</strong>. \r\n                Outstanding Balance is <strong>0</strong>', '1'),
(231, 'TLIS0000001', 'KMNkXjTSYZbG584wAD0CQxqhvUuJrstn', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 17:46:04', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of \r\n                <strong>25</strong> as Payment for <strong>ICT Dues</strong> from <strong>Philip Anthony dadzie</strong>. \r\n                Outstanding Balance is <strong>0</strong>', '1'),
(232, 'TLIS0000001', 'r7SPIRThcMXi6L0wVK93QNGxYCq8vkyH', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 17:46:46', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of \r\n                <strong>30</strong> as Payment for <strong>Library Fees</strong> from <strong>Philip Anthony dadzie</strong>. \r\n                Outstanding Balance is <strong>0</strong>', '1'),
(233, 'TLIS0000001', 'QCc3yn6eNutSsZ1LFBUXAx94MVHIEphk', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 17:47:42', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of \r\n                <strong>50</strong> as Payment for <strong>Project Fees</strong> from <strong>Philip Anthony dadzie</strong>. \r\n                Outstanding Balance is <strong>0</strong>', '1'),
(234, 'TLIS0000001', 'EZbRWGF2jde8QM9tB4a70HfJqwun6zTV', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 17:48:10', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of \r\n                <strong>250</strong> as Payment for <strong>Feeding Fees</strong> from <strong>Philip Anthony dadzie</strong>. \r\n                Outstanding Balance is <strong>0</strong>', '1'),
(235, 'TLIS0000001', 'BzrDqvUMC0hHfsuY4cgZnwFXk7xWtl56', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 17:48:31', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of \r\n                <strong>250</strong> as Payment for <strong>Feeding Fees</strong> from <strong>Ebenezer Franklin Hyde</strong>. \r\n                Outstanding Balance is <strong>0</strong>', '1'),
(236, 'TLIS0000001', 'xOgoTPUGqM1WiXYk8hcfs7dN2aFv9Dyt', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 17:48:45', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of \r\n                <strong>20</strong> as Payment for <strong>Library Fees</strong> from <strong>Ebenezer Franklin Hyde</strong>. \r\n                Outstanding Balance is <strong>10</strong>', '1'),
(237, 'TLIS0000001', 'JNMB5b8OaUdEAT0W9Hiy7hre4z2gSPcl', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 17:53:22', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of \r\n                <strong>650</strong> as Payment for <strong>Tuition Fees</strong> from <strong>Ebenezer Franklin Hyde</strong>. \r\n                Outstanding Balance is <strong>0</strong>', '1'),
(238, 'TLIS0000001', 'Q9MsTXLY3mWOlnD27CAZHar5x6RGbfvd', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 17:53:42', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of \r\n                <strong>20</strong> as Payment for <strong>Project Fees</strong> from <strong>Ebenezer Franklin Hyde</strong>. \r\n                Outstanding Balance is <strong>30</strong>', '1'),
(239, 'TLIS0000001', '8KPG0gBRAa2fjuLk1S5UZNrhHqbxez3v', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 17:53:57', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of \r\n                <strong>25</strong> as Payment for <strong>ICT Dues</strong> from <strong>Ebenezer Franklin Hyde</strong>. \r\n                Outstanding Balance is <strong>0</strong>', '1'),
(240, 'TLIS0000001', '1NKO2F4b7iakzMJVsGmThQvxS0B6PRjl', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 18:53:49', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of \r\n                <strong>50</strong> as Payment for <strong>Tuition Fees</strong> from <strong>julian asamoah dadzie</strong>. \r\n                Outstanding Balance is <strong>0</strong>', '1'),
(241, 'TLIS0000001', 'fhxabjoyIKD1C3LgX2wUdnJV0BT9RmWt', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 18:54:03', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of \r\n                <strong>30</strong> as Payment for <strong>Library Fees</strong> from <strong>julian asamoah dadzie</strong>. \r\n                Outstanding Balance is <strong>0</strong>', '1'),
(242, 'TLIS0000001', '2KVsRjaYE6FNyufQp4ZHnq53hTMlokiW', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-01 18:54:12', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of \r\n                <strong>50</strong> as Payment for <strong>Project Fees</strong> from <strong>julian asamoah dadzie</strong>. \r\n                Outstanding Balance is <strong>0</strong>', '1'),
(243, 'TLIS0000001', 'ZYibOC4wzLWBXUAa5skuhNS2KxQ7nr1f', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'guardian_ward', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-02 20:46:59', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde appended <strong>Ebenezer Franklin Hyde</strong> as a ward to <strong>henry asmah</strong>.', '1'),
(244, 'TLIS0000001', '2', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_allocation', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-03 12:30:43', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde added the fee allocation for <strong>Tuition Fees</strong> of: <strong>GHS 650</strong>', '1'),
(245, 'TLIS0000001', '2', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_allocation', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-03 12:30:49', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde added the fee allocation for <strong>ICT Dues</strong> of: <strong>GHS 25</strong>', '1'),
(246, 'TLIS0000001', '2', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_allocation', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-03 12:30:52', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde added the fee allocation for <strong>Library Fees</strong> of: <strong>GHS 30</strong>', '1'),
(247, 'TLIS0000001', '2', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_allocation', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-03 12:30:56', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde added the fee allocation for <strong>Project Fees</strong> of: <strong>GHS 50</strong>', '1'),
(248, 'TLIS0000001', '2', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_allocation', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-03 12:31:00', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde added the fee allocation for <strong>Feeding Fees</strong> of: <strong>GHS 250</strong>', '1'),
(249, 'TLIS0000001', 'hYqFrSR5LHzde0fVK3WilgaPjICuAZ81', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-03 12:31:29', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of \r\n                <strong>350</strong> as Payment for <strong>Tuition Fees</strong> from <strong>Another Test Class Student</strong>. \r\n                Outstanding Balance is <strong>300</strong>', '1'),
(250, 'TLIS0000001', 'hYqFrSR5LHzde0fVK3WilgaPjICuAZ81', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-03 12:43:36', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of \r\n                <strong>300</strong> as Payment for <strong>Tuition Fees</strong> from <strong>Another Test Class Student</strong>. \r\n                Outstanding Balance is <strong>0</strong>', '1'),
(251, 'TLIS0000001', 'X836ySvCMnfKoYPd2Q59DmIup0eghtzA', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-03 12:44:05', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of \r\n                <strong>25</strong> as Payment for <strong>ICT Dues</strong> from <strong>Another Test Class Student</strong>. \r\n                Outstanding Balance is <strong>0</strong>', '1'),
(252, 'TLIS0000001', 'blgc8rfdehuqmcq6iiohygn0trv7uevt', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"134\",\"item_id\":\"blgc8rfdehuqmcq6iiohygn0trv7uevt\",\"version\":\"v1\",\"resource\":\"fees\",\"endpoint\":\"fees\\/make_payment\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"checkout_url\\\":\\\"required - This is the checkout url for making payments\\\",\\\"payment_mode\\\":\\\"The mode for making the payment\\\",\\\"amount\\\":\\\"required - This is the amount to be made.\\\",\\\"description\\\":\\\"The description for the payment (optional)\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-01-09 19:22:02\",\"last_updated\":\"2021-01-09 19:22:44\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2021-05-03 16:46:57', 'Windows 10 | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> updated the endpoint.', '1'),
(253, 'TLIS0000001', 'blgc8rfdehuqmcq6iiohygn0trv7uevt', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"134\",\"item_id\":\"blgc8rfdehuqmcq6iiohygn0trv7uevt\",\"version\":\"v1\",\"resource\":\"fees\",\"endpoint\":\"fees\\/make_payment\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"checkout_url\\\":\\\"required - This is the checkout url for making payments\\\",\\\"payment_mode\\\":\\\"required - The mode for making the payment\\\",\\\"amount\\\":\\\"required - This is the amount to be made.\\\",\\\"description\\\":\\\"The description for the payment (optional)\\\",\\\"bank_name\\\":\\\"The name of the bank if a cheque is used to make payment\\\",\\\"cheque_number\\\":\\\"The unique number of the cheque if payment is being made using a cheque.\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-01-09 19:22:02\",\"last_updated\":\"2021-05-03 16:46:57\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM\"}', '2021-05-03 16:57:19', 'Windows 10 | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> updated the endpoint.', '1'),
(254, 'TLIS0000001', 'blgc8rfdehuqmcq6iiohygn0trv7uevt', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"134\",\"item_id\":\"blgc8rfdehuqmcq6iiohygn0trv7uevt\",\"version\":\"v1\",\"resource\":\"fees\",\"endpoint\":\"fees\\/make_payment\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"checkout_url\\\":\\\"required - This is the checkout url for making payments\\\",\\\"payment_method\\\":\\\"required - The mode for making the payment\\\",\\\"amount\\\":\\\"required - This is the amount to be made.\\\",\\\"description\\\":\\\"The description for the payment (optional)\\\",\\\"bank_name\\\":\\\"The name of the bank if a cheque is used to make payment\\\",\\\"cheque_number\\\":\\\"The unique number of the cheque if payment is being made using a cheque.\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-01-09 19:22:02\",\"last_updated\":\"2021-05-03 16:57:19\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM\"}', '2021-05-03 17:00:15', 'Windows 10 | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> updated the endpoint.', '1'),
(255, 'TLIS0000001', 'ANVs0r7WtK8k5IqUGhifL312bwTMSa4z', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-03 17:03:02', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of \r\n                <strong>300</strong> as Payment for <strong>Tuition Fees</strong> from <strong>George Anderson Hyde</strong>. \r\n                Outstanding Balance is <strong>350</strong>', '1'),
(256, 'TLIS0000001', 'SYed3LuwXgKkh60P7Uzc2W41qNQHGyDr', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'events_type', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-03 19:29:42', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde created a new Event Type: Public Holiday', '1'),
(257, 'TLIS0000001', 'SYed3LuwXgKkh60P7Uzc2W41qNQHGyDr', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'events_type', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"1\",\"client_id\":\"TLIS0000001\",\"item_id\":\"SYed3LuwXgKkh60P7Uzc2W41qNQHGyDr\",\"name\":\"Public Holiday\",\"slug\":\"public-holiday\",\"description\":\"This is for all Public Holidays\",\"color_code\":\"#000000\",\"icon\":null,\"status\":\"1\"}', '2021-05-03 19:30:29', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde successfully updated the event type: Public Holiday', '1'),
(258, 'TLIS0000001', 'bacOBo6KPX0imGHJ7ZE8Y2Tr1kDSVsNW', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'events', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-03 20:18:45', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde created a new Event with title <strong>May Day</strong> to be held on 2021-05-03.', '1'),
(259, 'TLIS0000001', 'C9nNU06YfeJFOp1Hv2LSE5ubGaw3Mqzt', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'events', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-03 20:23:15', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde created a new Event with title <strong>Good Friday</strong> to be held on 2021-04-02.', '1'),
(260, 'TLIS0000001', 'hUyM50dWA1OwETareXCbLB3RvKz2mqYV', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'events', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-03 20:23:39', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde created a new Event with title <strong>Easter Monday</strong> to be held on 2021-04-05.', '1'),
(261, 'TLIS0000001', 'wGCaT9IlgcyhxFeQP0rinRj2S6mfLpJ1', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'events', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-03 20:24:36', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde created a new Event with title <strong>Independence Day</strong> to be held on 2021-03-06.', '1'),
(262, 'TLIS0000001', '01c5f8GzLxVQjvSeCtOwXBYZ7W29HPFo', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'events', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-03 20:24:58', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde created a new Event with title <strong>Independence Day Observed</strong> to be held on 2021-03-08.', '1'),
(263, 'TLIS0000001', 'wGCaT9IlgcyhxFeQP0rinRj2S6mfLpJ1', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'events', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-03 20:25:26', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde updated the event details.', '1'),
(264, 'TLIS0000001', 'wGCaT9IlgcyhxFeQP0rinRj2S6mfLpJ1', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'events', 'MySchoolGH Calculation<br>Property changed by an update from another property.', 'Pending', '2021-05-03 20:25:26', 'Windows 10 | Chrome | ::1', 'Event Status was changed from Pending', '1'),
(265, 'TLIS0000001', 'UXLgat5ZscWOAQKS7qwzfVMv8Gkrj4mp', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'events', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-03 20:26:35', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde created a new Event with title <strong>Constitutional Day</strong> to be held on 2021-01-07.', '1'),
(266, 'TLIS0000001', 'xXiZUrDslmP0vF7fkny9hbVICLJqtKjd', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-03 22:13:47', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of \r\n                <strong>500</strong> as Payment for <strong>Tuition Fees</strong> from <strong>Test Class Student</strong>. \r\n                Outstanding Balance is <strong>150</strong>', '1'),
(267, 'TLIS0000001', 'xXiZUrDslmP0vF7fkny9hbVICLJqtKjd', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-03 22:20:01', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of \r\n                <strong>50</strong> as Payment for <strong>Tuition Fees</strong> from <strong>Test Class Student</strong>. \r\n                Outstanding Balance is <strong>100</strong>', '1'),
(268, 'TLIS0000001', 'xXiZUrDslmP0vF7fkny9hbVICLJqtKjd', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-03 22:24:34', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of \r\n                <strong>50</strong> as Payment for <strong>Tuition Fees</strong> from <strong>Test Class Student</strong>. \r\n                Outstanding Balance is <strong>50</strong>', '1'),
(269, 'TLIS0000001', 'yZCNG8zuIYxVfKoPr71Hg5DEbt62edAp', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-03 23:15:34', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of \r\n                <strong>25</strong> as Payment for <strong>ICT Dues</strong> from <strong>Test Class Student</strong>. \r\n                Outstanding Balance is <strong>0</strong>', '1'),
(270, 'TLIS0000001', 'iFy7AaqCnmTk3fIRxb8zvSpJhXule2Bt', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-03 23:18:30', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of \r\n                <strong>30</strong> as Payment for <strong>Library Fees</strong> from <strong>Test Class Student</strong>. \r\n                Outstanding Balance is <strong>0</strong>', '1'),
(271, 'TLIS0000001', 'tEfWagUKR2x4ownVHjQ5dFIcsBG6XA71', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-03 23:21:38', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of \r\n                <strong>30</strong> as Payment for <strong>Library Fees</strong> from <strong>Another Test Class Student</strong>. \r\n                Outstanding Balance is <strong>0</strong>', '1'),
(272, 'TLIS0000001', '6kOWRdgPJsA9Yepb81cNhMFt3C5QwiaH', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-03 23:23:31', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of \r\n                <strong>50</strong> as Payment for <strong>Project Fees</strong> from <strong>Test Class Student</strong>. \r\n                Outstanding Balance is <strong>0</strong>', '1'),
(273, 'TLIS0000001', 'xXiZUrDslmP0vF7fkny9hbVICLJqtKjd', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-03 23:24:46', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of \r\n                <strong>50</strong> as Payment for <strong>Tuition Fees</strong> from <strong>Test Class Student</strong>. \r\n                Outstanding Balance is <strong>0</strong>', '1'),
(274, 'TLIS0000001', '', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-05 16:05:27', 'Windows 10 | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> added a new endpoint: <strong>pwa/idb</strong> to the resource: <strong>pwa</strong>.', '1'),
(275, 'TLIS0000001', 'blgc8rfdehuqmcq6iiohygn0trv7uevt', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"134\",\"item_id\":\"blgc8rfdehuqmcq6iiohygn0trv7uevt\",\"version\":\"v1\",\"resource\":\"fees\",\"endpoint\":\"fees\\/make_payment\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"checkout_url\\\":\\\"required - This is the checkout url for making payments\\\",\\\"payment_method\\\":\\\"required - The mode for making the payment\\\",\\\"amount\\\":\\\"required - This is the amount to be made.\\\",\\\"description\\\":\\\"The description for the payment (optional)\\\",\\\"bank_id\\\":\\\"The unique id of the bank if a cheque is used to make payment\\\",\\\"cheque_number\\\":\\\"The unique number of the cheque if payment is being made using a cheque.\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-01-09 19:22:02\",\"last_updated\":\"2021-05-03 17:00:15\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM\"}', '2021-05-06 14:50:43', 'Windows 10 | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> updated the endpoint.', '1'),
(276, 'MSGH000004', 'Z3qWQN8stLBUSg2FArpOIKEDMJvdafYk', 'Z3qWQN8stLBUSg2FArpOIKEDMJvdafYk', 'verify_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-06 22:59:25', 'Windows 10 | Chrome | ::1', 'Test Sample School created a new Account pending Verification.', '1'),
(277, 'MSGH000004', 'Z3qWQN8stLBUSg2FArpOIKEDMJvdafYk', 'Z3qWQN8stLBUSg2FArpOIKEDMJvdafYk', 'admin_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-06 23:33:21', 'Windows 10 | Chrome | ::1', 'Name was changed from ', '1'),
(278, 'MSGH000004', 'Z3qWQN8stLBUSg2FArpOIKEDMJvdafYk', 'Z3qWQN8stLBUSg2FArpOIKEDMJvdafYk', 'admin_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-06 23:33:21', 'Windows 10 | Chrome | ::1', 'Position has been altered.  => Administrator', '1'),
(279, 'MSGH000004', 'Z3qWQN8stLBUSg2FArpOIKEDMJvdafYk', 'Z3qWQN8stLBUSg2FArpOIKEDMJvdafYk', 'admin_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-06 23:33:21', 'Windows 10 | Chrome | ::1', 'You updated your account information', '1'),
(280, 'MSGH000004', 'Z3qWQN8stLBUSg2FArpOIKEDMJvdafYk', 'Z3qWQN8stLBUSg2FArpOIKEDMJvdafYk', 'admin_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-06 23:33:51', 'Windows 10 | Chrome | ::1', 'Date of Birth has been changed to 1995-07-20', '1'),
(281, 'MSGH000004', 'Z3qWQN8stLBUSg2FArpOIKEDMJvdafYk', 'Z3qWQN8stLBUSg2FArpOIKEDMJvdafYk', 'admin_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-06 23:33:51', 'Windows 10 | Chrome | ::1', 'You updated your account information', '1'),
(282, 'MSGH000004', 'MSGH000004', 'Z3qWQN8stLBUSg2FArpOIKEDMJvdafYk', 'account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"4\",\"client_id\":\"MSGH000004\",\"client_name\":\"Test Sample School\",\"client_contact\":\"233550107770\",\"client_secondary_contact\":\"233500021983\",\"client_address\":\"Test School Address\",\"client_email\":\"testsampleschool@mail.com\",\"client_website\":null,\"client_logo\":null,\"client_location\":null,\"client_category\":null,\"client_preferences\":{\"labels\":{\"staff\":\"TSSU\",\"student\":\"TSS\",\"parent\":\"TSSP\",\"receipt\":\"RTSS\"},\"academics\":{\"academic_year\":\"2021\\/2020\",\"academic_term\":\"\",\"next_academic_year\":\"\",\"next_academic_term\":\"\"},\"account\":{\"type\":\"basic\",\"activation_code\":\"K1lwMeg7hxHL4DWI89jNZQRSOtXp0bCAdJoT3FPrn5G6cEifkqaYyU\",\"date_created\":\"2021-05-06 10:59PM\",\"expiry\":\"2021-06-06 10:59PM\"},\"opening_days\":[\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\",\"Friday\"]},\"client_status\":\"1\",\"client_state\":\"Pending\",\"ip_address\":\"::1\",\"date_created\":\"2021-05-06 22:59:24\",\"grading_system\":null,\"grading_structure\":null,\"show_position\":null,\"show_teacher_name\":null,\"allow_submission\":null,\"academic_year_logs\":[]}', '2021-05-06 23:35:07', 'Windows 10 | Chrome | ::1', 'Frank Asamoah Mensah updated the Account Information', '1'),
(283, 'MSGH000004', 'MSGH000004', 'Z3qWQN8stLBUSg2FArpOIKEDMJvdafYk', 'account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"4\",\"client_id\":\"MSGH000004\",\"client_name\":\"Test Sample School\",\"client_contact\":\"233550107770\",\"client_secondary_contact\":\"233500021983\",\"client_address\":\"Test School Address\",\"client_email\":\"testsampleschool@mail.com\",\"client_website\":\"https:\\/\\/sampleschoolgh.com\",\"client_logo\":null,\"client_location\":\"Accra\",\"client_category\":null,\"client_preferences\":{\"academics\":{\"academic_year\":\"2019\\/2020\",\"academic_term\":\"1st\",\"term_starts\":\"2021-03-01\",\"term_ends\":\"2021-05-29\",\"next_academic_year\":\"2019\\/2020\",\"next_academic_term\":\"2nd\",\"next_term_starts\":\"2021-06-15\",\"next_term_ends\":\"2021-08-31\"},\"labels\":{\"student_label\":\"\",\"parent_label\":\"\",\"teacher_label\":\"\",\"staff_label\":\"\",\"course_label\":\"\",\"book_label\":\"\",\"class_label\":\"\",\"department_label\":\"\",\"section_label\":\"\",\"receipt_label\":\"\",\"currency\":\"GHS\"},\"opening_days\":[\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\",\"Friday\"],\"account\":{}},\"client_status\":\"1\",\"client_state\":\"Pending\",\"ip_address\":\"::1\",\"date_created\":\"2021-05-06 22:59:24\",\"grading_system\":null,\"grading_structure\":null,\"show_position\":null,\"show_teacher_name\":null,\"allow_submission\":null,\"academic_year_logs\":[]}', '2021-05-06 23:44:50', 'Windows 10 | Chrome | ::1', 'Frank Asamoah Mensah updated the Account Information', '1'),
(284, 'MSGH000004', 'Z3qWQN8stLBUSg2FArpOIKEDMJvdafYk', 'Z3qWQN8stLBUSg2FArpOIKEDMJvdafYk', 'verify_account', 'Account was manually activated using the Activation link.', NULL, '2021-05-06 23:48:31', 'Windows 10 | Chrome | ::1', 'testsampleschool\'s - account was successfully activated.', '1'),
(285, 'MSGH000004', 'Z3qWQN8stLBUSg2FArpOIKEDMJvdafYk', 'Z3qWQN8stLBUSg2FArpOIKEDMJvdafYk', 'verify_account', 'Account was manually activated using the Activation link.', NULL, '2021-05-06 23:49:35', 'Windows 10 | Chrome | ::1', 'testsampleschool\'s - account was successfully activated.', '1'),
(286, 'TLIS0000001', '', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-08 19:03:28', 'Windows 10 | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> added a new endpoint: <strong>fees/search</strong> to the resource: <strong>fees</strong>.', '1'),
(287, 'TLIS0000001', 'k6fr3exchm5rniq24fcysvyzidazolkb', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"179\",\"item_id\":\"k6fr3exchm5rniq24fcysvyzidazolkb\",\"version\":\"v1\",\"resource\":\"fees\",\"endpoint\":\"fees\\/search\",\"method\":\"GET\",\"description\":\"\",\"parameter\":\"\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-05-08 19:03:28\",\"last_updated\":\"2021-05-08 19:03:28\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM\",\"updated_by\":null}', '2021-05-08 19:09:56', 'Windows 10 | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> updated the endpoint.', '1'),
(288, 'TLIS0000001', '3aj8WoYlCXApyfJLRs1UiPDZ6kIMuHvz', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-11 11:38:07', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of \r\n                <strong>25</strong> as Payment for <strong>ICT Dues</strong> from <strong>George Anderson Hyde</strong>. \r\n                Outstanding Balance is <strong>0</strong>', '1'),
(289, 'TLIS0000001', 'blgc8rfdehuqmcq6iiohygn0trv7uevt', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"134\",\"item_id\":\"blgc8rfdehuqmcq6iiohygn0trv7uevt\",\"version\":\"v1\",\"resource\":\"fees\",\"endpoint\":\"fees\\/make_payment\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"checkout_url\\\":\\\"required - This is the checkout url for making payments\\\",\\\"payment_method\\\":\\\"required - The mode for making the payment\\\",\\\"amount\\\":\\\"required - This is the amount to be made.\\\",\\\"description\\\":\\\"The description for the payment (optional)\\\",\\\"bank_id\\\":\\\"The unique id of the bank if a cheque is used to make payment\\\",\\\"cheque_number\\\":\\\"The unique number of the cheque if payment is being made using a cheque.\\\",\\\"cheque_security\\\":\\\"The security code on the cheque.\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-01-09 19:22:02\",\"last_updated\":\"2021-05-06 14:50:43\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM\"}', '2021-05-11 22:17:05', 'Windows 10 | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> updated the endpoint.', '1'),
(290, 'TLIS0000001', 'bfoHtlVKavzrXAnp3G1INZ6sFDOPgkj2', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-12 05:07:40', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of \r\n                            <strong>10</strong> as Payment for <strong>Feeding Fees</strong> from \r\n                            <strong>Test Class Student</strong>. Outstanding Balance is <strong>240</strong>', '1'),
(291, 'TLIS0000001', 'bfoHtlVKavzrXAnp3G1INZ6sFDOPgkj2', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-12 05:19:26', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of \r\n                            <strong>70</strong> as Payment for <strong>Feeding Fees</strong> from \r\n                            <strong>Test Class Student</strong>. Outstanding Balance is <strong>170</strong>', '1'),
(292, 'TLIS0000001', 'bfoHtlVKavzrXAnp3G1INZ6sFDOPgkj2', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-12 05:21:27', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of \r\n                            <strong>80</strong> as Payment for <strong>Feeding Fees</strong> from \r\n                            <strong>Test Class Student</strong>. Outstanding Balance is <strong>90</strong>', '1'),
(293, 'TLIS0000001', 'bfoHtlVKavzrXAnp3G1INZ6sFDOPgkj2', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-05-12 05:28:20', 'Windows 10 | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of \r\n                            <strong>90</strong> as Payment for <strong>Feeding Fees</strong> from \r\n                            <strong>Test Class Student</strong>. Outstanding Balance is <strong>0</strong>', '1');

-- --------------------------------------------------------

--
-- Table structure for table `users_api_endpoints`
--

DROP TABLE IF EXISTS `users_api_endpoints`;
CREATE TABLE `users_api_endpoints` (
  `id` int(11) NOT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `version` varchar(32) NOT NULL DEFAULT 'v1',
  `resource` varchar(64) DEFAULT NULL,
  `endpoint` varchar(255) DEFAULT NULL,
  `method` enum('GET','POST','PUT','DELETE') DEFAULT 'GET',
  `description` varchar(255) DEFAULT NULL,
  `parameter` text DEFAULT NULL,
  `status` enum('overloaded','active','dormant','inactive') NOT NULL DEFAULT 'active',
  `counter` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `last_updated` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  `deprecated` enum('0','1') NOT NULL DEFAULT '0',
  `added_by` varchar(32) DEFAULT NULL,
  `updated_by` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users_api_endpoints`
--

INSERT INTO `users_api_endpoints` (`id`, `item_id`, `version`, `resource`, `endpoint`, `method`, `description`, `parameter`, `status`, `counter`, `date_created`, `last_updated`, `deleted`, `deprecated`, `added_by`, `updated_by`) VALUES
(1, 'xwmcpvd8ezmqehjnt3g5ykpbalrk29is', 'v1', 'users', 'users/list', 'GET', 'This endpoint manages the user information', '{\"limit\":\"The number of rows to limit the result\",\"class_id\":\"List the results using the class_id\",\"user_id\":\"The user id to load the information\",\"user_type\":\"The user type to fetch the record\",\"gender\":\"The gender of the user\",\"date_of_birth\":\"Search by date of birth\",\"q\":\"Searching for users using a string.\",\"status\":\"Load the result filtered by the status of the policy\",\"minified\":\"If the user requested for the minimal data\",\"lookup\":\"Query string\",\"append_wards\":\"Append the wards list to the result\"}', 'active', 0, '2020-09-11 21:31:47', '2021-01-18 22:24:50', '0', '0', NULL, 'uIkajsw123456789064hxk1fc3efmnva'),
(2, 'zubdjqpic2reoykink1jhvq4pes7n9f8', 'v1', 'users', 'users/activities', 'GET', 'This endpoint loads the user activity logs updated', '{\"limit\":\"The number of rows to limit the result\",\"user_id\":\"The user id to load the information\"}', 'active', 0, '2020-09-11 21:31:47', '2020-09-15 21:29:05', '0', '0', NULL, NULL),
(6, 'cct4z26rwfkpv7xdkdbhxsruuapyfqt9', 'v1', 'endpoints', 'endpoints/list', 'GET', '', '{\"limit\":\"The number of rows to limit the results set.\",\"endpoint_id\":\"The id of the endpoint to load the content.\"}', 'active', 0, '2020-09-12 15:19:44', '2020-09-21 11:51:42', '0', '0', NULL, 'tgxuwdwkdjr58mg64hxk1fc3efmnvata'),
(10, 'pm3w6ofckjzguwyaoa1ytnh8fnllbs7z', 'v1', 'users', 'users/preference', 'POST', 'Initialize the user account. This accepts a value contained in the value parameter. init_idb will initiate the index db on the user\'s device, the any other parameter will update the user preferences.', '{\"label\":\"The item to update. It can be an array data and parsed to update the user preferences.\",\"the_user_id\":\"The user id to update the preference (optional)\"}', 'active', 0, '2020-09-17 15:09:43', '2020-11-11 20:11:51', '0', '0', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ'),
(11, 'js7u9uwbmlnhccmxtpya4nwqk5hvgkag', 'v1', 'users', 'users/update', 'POST', '\'This endpoint is used for updating the information of the user.\'', '{\"firstname\":\"required - The firstname of the user\",\"lastname\":\"required - The lastname of the user\",\"othername\":\"The othernames of the user\",\"client_id\":\"This is a Unique of the user that is been created.\",\"gender\":\"The gender of the user\",\"date_of_birth\":\"The date of birth\",\"email\":\"The email address of the user\",\"phone\":\"Contact number of the user\",\"phone_2\":\"Secondary contact number\",\"address\":\"The address of the user\",\"residence\":\"The place of residence\",\"nationality\":\"The nationality of the user\",\"country\":\"The country id of the user\",\"description\":\"Any additional information of the user\",\"position\":\"The position of the user\",\"user_id\":\"The id of the user\",\"occupation\":\"The occupation of the user\",\"employer\":\"The name of the users employer\",\"access_level\":\"The access permission id of the user.\",\"department_id\":\"The department of the user\",\"unique_id\":\"The unique id of the user\",\"section\":\"The section of the user\",\"class_id\":\"The class id of the user\",\"blood_group\":\"The blood group of the user\",\"guardian_info\":\"An array of the guardian information\",\"enrollment_date\":\"The date on which the user was enrolled\",\"user_type\":\"The type of the user to add\",\"image\":\"Image of the user\",\"academic_year\":\"The academic year on which the student was enrolled\",\"academic_term\":\"The term within which the student was enrolled\",\"username\":\"The username of the user for login purposes.\",\"previous_school\":\"This is applicable for students only\",\"previous_school_qualification\":\"Applicable for students only\",\"previous_school_remarks\":\"Any remarks supplied by previous school from which student is coming from\",\"religion\":\"The religion of the user\",\"relationship\":\"The relationship of the guardian to the student\",\"courses_ids\":\"This is the course id and is applicable to teachers only\",\"status\":\"The state of the current user to be set.\"}', 'active', 0, '2020-09-18 09:40:19', '2021-01-22 08:45:20', '0', '0', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajsw123456789064hxk1fc3efmnva'),
(12, 'ujeqvzg4c7ubshvlyp8jmffl2aykkoi5', 'v1', 'files', 'files/preview', 'POST', 'Use this endpoint to upload a file for preview.', '{\"file_upload\":\"required - The name of the file to upload\",\"module\":\"This will process any additional information added to this file upload.\"}', 'active', 0, '2020-09-18 14:04:04', '2020-09-19 17:52:22', '0', '0', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata'),
(13, 'pvfweosbq9yhyux2ugbndktj5m1vjsoc', 'v1', 'users', 'users/save_image', 'POST', 'This endpoint saves a users profile picture once it has been reviewed and accepted by the user.', '{\"user_id\":\"The id of the user to update the profile picture\"}', 'active', 0, '2020-09-18 14:23:38', '2020-09-18 14:23:38', '0', '0', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', NULL),
(15, 'g1s0ypnf6ywmsineoxcruivtl4w9auqe', 'v1', 'users', 'users/add', 'POST', 'Add a new user account', '{\"firstname\":\"required - The firstname of the user\",\"client_id\":\"This is a Unique of the user that is been created.\",\"lastname\":\"required - The lastname of the user\",\"othername\":\"The othernames of the user\",\"gender\":\"The gender of the user\",\"date_of_birth\":\"The date of birth\",\"email\":\"The email address of the user\",\"phone\":\"Contact number of the user\",\"phone_2\":\"Secondary contact number\",\"address\":\"The address of the user\",\"residence\":\"The place of residence\",\"nationality\":\"The nationality of the user\",\"country\":\"The country id of the user\",\"description\":\"Any additional information of the user\",\"user_id\":\"The id of the user\",\"employer\":\"The name of the user employer\",\"occupation\":\"The occupation of the user\",\"position\":\"The position of the user\",\"access_level\":\"The access permission id of the user.\",\"department_id\":\"The department of the user\",\"unique_id\":\"The unique id of the user\",\"section\":\"The section of the user\",\"class_id\":\"The class id of the user\",\"blood_group\":\"The blood group of the user\",\"guardian_info\":\"An array of the guardian information\",\"enrollment_date\":\"The date on which the user was enrolled\",\"user_type\":\"required - The type of the user to add\",\"image\":\"Image of the user\",\"academic_year\":\"The academic year on which the student was enrolled\",\"academic_term\":\"The term within which the student was enrolled\",\"status\":\"The status of the user\",\"username\":\"The username of the user for login purposes.\",\"previous_school\":\"This is applicable for students only\",\"previous_school_qualification\":\"Applicable for students only\",\"previous_school_remarks\":\"Any remarks supplied by previous school from which student is coming from\",\"religion\":\"The religion of the user\",\"relationship\":\"The relationship of the guardian to the student\",\"courses_ids\":\"This is the course id and is applicable to teachers only\",\"status\":\"The state of the current user to be set.\"}', 'active', 0, '2020-09-19 07:17:49', '2021-01-22 08:44:53', '0', '0', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajsw123456789064hxk1fc3efmnva'),
(16, '9wsuxpzg0u7365thvnayhoprlkxobywi', 'v1', 'files', 'files/attachments', 'POST', 'This endpoint will be used to attach documents to a specific resource. The same could be used to list the uploaded files', '{\"attachment_file_upload\":\"Document to upload, if any.\",\"module\":\"The module of documents to list.\",\"item_id\":\"This is the id of the item - This is needed in loading information\", \"label\":\"This will contain additional information\",\"comment_attachment_file_upload\":\"The file to attach\"}', 'active', 0, '2020-09-19 23:41:50', '2020-10-07 01:59:54', '0', '0', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata'),
(20, 'rjhod5nsgaqx9acfbepgc6hplkbqsyy0', 'v1', 'notification', 'notification/list', 'GET', '', '{\"notice_id\":\"The id of the notification\",\"status\":\"The status of the notification\",\"user_id\":\"The id of the user\",\"initiated_by\":\"The notification was initiated by.\"}', 'active', 0, '2020-09-20 19:24:10', '2020-09-20 19:24:10', '0', '0', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', NULL),
(21, 'ipyk5mexrhu80xwqdtvbojdgrwsqfbif', 'v1', 'replies', 'replies/add', 'POST', '', '{\"resource\":\"required - The resource endpoint to share this reply\",\"record_id\":\"required - This is the id of the parent id to post this reply\",\"message\":\"required - The message as a reply to the resource\"}', 'active', 0, '2020-09-21 16:33:18', '2020-09-21 19:23:57', '0', '0', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata'),
(22, 'vvbgi4chdmpslyrfnexuqjk7a8x1fwhw', 'v1', 'replies', 'replies/list', 'GET', 'This endpoint is used to load the replies', '{\"limit\":\"The number of rows to limit the result\",\"resource_id\":\"This is the name of the resource to load the replies\",\"resource\":\"This is a reference to the resource to fetch all replies\",\"user_id\":\"The id of the user who made the replies\",\"last_reply_id\":\"The very last comment loaded.\",\"feedback_type\":\"The type of feedback to load: comment or reply\"}', 'active', 0, '2020-09-21 16:34:32', '2020-10-01 17:59:36', '0', '0', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata'),
(23, 'gjnjxeyfxkacb523waopq7tf6stzsd4u', 'v1', 'records', 'records/threads', 'POST', 'The param will be formatted like: param[messages][record_id] = the_user_id to load the messages, param[notifications][record_id] = the_user_id, which is the user id to load the notifications, param[replies][record_id] which will be the resource id to load', '{\"date_range\":\"The date range to count the items\",\"date\":\"The maximum date to set as limit to count the records\",\"param\":\"required - The parameter to count the record. This will be an array to take a resource as key and the record_id as the value.\",\"record_id\":\"The record id to perform the query on\"}', 'active', 0, '2020-09-21 19:34:49', '2020-09-22 22:12:58', '0', '0', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata'),
(24, 'qv3hr24vw5koailshqzo9gbswg07yref', 'v1', 'records', 'records/save', 'POST', 'Use this endpoint to a record which is in its draft state.', '{\"resource\":\"required - The resource type to save\",\"resource_id\":\"required - The record itselft to save\"}', 'active', 0, '2020-09-23 16:15:56', '2020-09-23 16:19:45', '0', '0', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV'),
(25, 'izqed6k2tzyh0acqsin4xmfunpco3ora', 'v1', 'complaints', 'complaints/update', 'POST', '', '{\"status\":\"This is the status of the complaint\",\"assigned_to\":\"The user to whom this complaint is been assigned to.\",\"complaint_id\":\"required - The id of the complaint\",\"subject\":\"The subject of the complaint\",\"related_to\":\"The resource that this complaint is related to\",\"related_to_id\":\"The unique id of the relation\",\"message\":\"The content of the message.\"}', 'active', 0, '2020-09-23 18:46:20', '2020-09-24 00:20:28', '0', '0', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata'),
(34, 'qypz3gfiyw6onsvw8mnlhxuredkq9cdz', 'v1', 'replies', 'replies/comment', 'POST', '', '{\"item_id\":\"required - The id of the item to leave the comment\",\"comment\":\"required - The comment to post\",\"resource\":\"required - The resource to leave comment on: complaint, policy, claim\"}', 'active', 0, '2020-10-01 10:02:04', '2020-10-01 10:26:04', '0', '0', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata'),
(35, 'hyxa0jmfz9bfl3i82yxeketg654cqicv', 'v1', 'replies', 'replies/delete', 'POST', '', '{\"reply_id\":\"required - The id of the reply to delete.\"}', 'active', 0, '2020-10-01 18:39:38', '2020-10-01 18:39:38', '0', '0', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', NULL),
(38, 'dfskpdcvvcixjzn3jun6grtqt05lmrpo', 'v1', 'posts', 'posts/share', 'POST', '', '{\"post_content\":\"required - The post content to share\",\"post_user_id\":\"required - The id of the user making the post\",\"post_id\":\"This is the main post id to insert\",\"post_parent_id\":\"This is the parent post id if a comment is to be shared on a post.\"}', 'active', 0, '2020-10-07 09:49:50', '2020-10-07 10:08:20', '0', '0', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata'),
(39, 'gyla2jc6dnzb91tz0pypqmaruxmxnqwe', 'v1', 'posts', 'posts/list', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"last_post_id\":\"The id of the last post\",\"visibility\":\"The status of the post - default is Public\",\"resource_id\":\"The id of the user to load\",\"item_id\":\"The id of the resource to load\",\"post_id\":\"The id of the post to load\"}', 'active', 0, '2020-10-07 10:27:56', '2020-10-07 10:27:56', '0', '0', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', NULL),
(40, 'hgwsa3in7zl5u6xrafetqjtdov9dfpxu', 'v1', 'posts', 'posts/likes_count', 'POST', '', '{\"post_id\":\"required - The post to like.\"}', 'active', 0, '2020-10-07 13:07:02', '2020-10-07 13:07:02', '0', '0', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', NULL),
(41, 'i7atecpmnuhe6gxvhdifbfsg810rplqk', 'v1', 'emails', 'emails/action', 'POST', '', '{\"action\":\"required - This will contain all the variables to be used in processing all requests to the endpoint\"}', 'active', 0, '2020-10-07 17:15:18', '2020-10-07 17:15:18', '0', '0', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', NULL),
(42, 'hdfduhabxq42g5kivosfmy6wyr1ebjsz', 'v1', 'announcements', 'announcements/list', 'GET', '', '{\"limit\":\"The number of rows to list\",\"status\":\"The status of the annoucement.\"}', 'active', 0, '2020-10-12 14:06:26', '2020-10-12 14:06:26', '0', '0', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', NULL),
(43, 'ehzc0anlv7jyscd3xlijzr6k2fogu1gp', 'v1', 'announcements', 'announcements/post', 'POST', '', '{\"subject\":\"required - The subject of the announcement\",\"start_date\":\"required - The start date for the announcement\",\"end_date\":\"The date on which the announcement will end\",\"recipient_group\":\"required - The recipient of this announcement\",\"message\":\"required - The announcement message to share with the recipient group\",\"priority\":\"This can low, medium or high\"}', 'active', 0, '2020-10-13 05:39:13', '2020-10-13 14:01:08', '0', '0', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata'),
(44, 'e0zkxmnt7vjeuhg3u9c1vf2b4wlyykid', 'v1', 'announcements', 'announcements/update', 'POST', '', '{\"subject\":\"required - The subject of the announcement\",\"start_date\":\"required - The start date for the announcement\",\"end_date\":\"The date on which the announcement will end\",\"recipient_group\":\"required - The recipient of this announcement\",\"message\":\"required - The announcement message to share with the recipient group\",\"priority\":\"This can low, medium or high\",\"announcement_id\":\"required- The id of the announcement to update\"}', 'active', 0, '2020-10-14 08:54:23', '2020-10-14 08:54:23', '0', '0', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', NULL),
(45, '5zno3bxqf0khmwg284gcxe6lsouavkrv', 'v1', 'announcements', 'announcements/viewed', 'POST', '', '{\"announcement_id\":\"required- The id of the announcement viewed\"}', 'active', 0, '2020-10-14 14:24:23', '2020-10-14 14:24:23', '0', '0', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', NULL),
(49, 'mb6tzlxni2no9xiol0sc7ahmqyvkzrgs', 'v1', 'chats', 'chats/list', 'POST', '', '{\"user_id\":\"required - The user to load messages shared with\",\"limit\":\"The number of messages to load\"}', 'active', 0, '2020-10-15 20:30:32', '2020-10-15 20:30:32', '0', '0', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', NULL),
(50, 'rs6mwxgjntkie50rwp2yv7movgdufhnq', 'v1', 'chats', 'chats/delete', 'POST', '', '{\"msg_id\":\"required - The id of the message to delete\",\"action\":\"required - The item to delete either message/conversation.\"}', 'active', 0, '2020-10-20 14:54:16', '2020-10-20 14:56:48', '0', '0', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata'),
(64, 'irmvzaxwy1ifatnp8lgnrd2coxj7h5pq', 'v1', 'payments', 'payments/list', 'GET', '', '{\"checkout_url\":\"The checkout url to load the information\",\"initiated_by\":\"The id of the user who initialed this payment\",\"user_id\":\"The id of the user for whom this payment request relates to\",\"record_type\":\"The type of the payment request\",\"record_id\":\"The id of the record for which the payment was made\",\"status\":\"The status of the payment request\"}', 'active', 0, '2020-11-04 05:18:01', '2020-11-27 22:56:53', '0', '1', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajsw123456789064hxk1fc3efmnva'),
(65, 'sb926dyp8cuavrhwkb0wjkrmlogqefu3', 'v1', 'payments', 'payments/checkout', 'POST', '', '{\"payment_mode\":\"required - This is the payment method to be used\",\"payment_info\":\"This is the payment information used in the query.\"}', 'active', 0, '2020-11-09 09:18:20', '2020-11-27 22:56:43', '1', '1', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajsw123456789064hxk1fc3efmnva'),
(66, 'ibxllgojguzeynrnjoxq70pb4hcavwdm', 'v1', 'payments', 'payments/verify', 'GET', '', '{\"payment_module\":\"required - The payment module used\",\"transaction_id\":\"required - The unique id of the transaction\"}', 'active', 0, '2020-11-09 11:32:30', '2020-11-27 22:56:55', '0', '1', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(67, 'ai6mpxag2hutb5y3dcvzjrkujgko19ml', 'v1', 'users', 'users/load_permissions', 'POST', '', '{\"user_id\":\"required - The id of the user to load the permissions.\"}', 'active', 0, '2020-11-10 15:26:11', '2020-11-10 15:26:11', '0', '0', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', NULL),
(68, 'p6jth58usr2wbqjtyx3nufvmzk0okpq9', 'v1', 'users', 'users/save_permissions', 'POST', '', '{\"user_id\":\"required - The id of the user to update the permissions\",\"access_level\":\"required - The current permission of the user\",\"permissions_list\":\"The array of the user permissions.\"}', 'active', 0, '2020-11-10 19:32:42', '2020-11-10 19:32:42', '0', '0', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', NULL),
(70, 'yyswpqfeompnwkvjg6qacgb312ozhcti', 'v1', 'reports', 'reports/generate', 'GET', '', '{\"period\":\"The period for the data collection (Default: this_week)\",\"label\":\"This will be an array of information to generate the report\"}', 'active', 0, '2020-11-12 15:48:07', '2020-11-12 15:48:34', '0', '0', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata'),
(71, '8nonkzspjlup3vdkyhfhremdw5xz7clw', 'v1', 'forms', 'forms/load', 'POST', NULL, '{\"module\":\"required - This will be an array variable to contain all the necessary data for processing.\"}', 'active', 0, '2020-11-27 18:49:30', '2020-11-27 18:49:30', '0', '0', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata'),
(72, '04bg2xphdmdqxvn5ow3pz9zamylgwke8', 'v1', 'classes', 'classes/add', 'POST', '', '{\"class_code\":\"The unique class code\",\"name\":\"required - The name of the class\",\"class_teacher\":\"The unique id of the class teacher\",\"class_assistant\":\"The unique id of the class assistant\",\"description\":\"The description of the class (optional)\",\"department_id\":\"The id of the department to which the class belongs\",\"weekly_meeting\":\"The number of times this class meets in a week\",\"class_size\":\"The number of students in the class\",\"room_id\":\"An array of rooms that this class can be held\"}', 'active', 0, '2020-11-27 23:00:12', '2021-01-22 13:45:22', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(73, 'e05golyqpdpgnx1untuysrbbc37jws2f', 'v1', 'classes', 'classes/update', 'POST', '', '{\"class_code\":\"The unique class code\",\"name\":\"required - The name of the class\",\"class_teacher\":\"The unique id of the class teacher\",\"class_assistant\":\"The unique id of the class assistant\",\"description\":\"The description of the class (optional)\",\"class_id\":\"required - The unique of the class to update\",\"department_id\":\"The id of the department to which the class belongs\",\"weekly_meeting\":\"The number of times this class meets in a week\",\"class_size\":\"The number of students in the class\",\"room_id\":\"An array of rooms that this class can be held\"}', 'active', 0, '2020-11-27 23:00:41', '2021-01-22 13:45:26', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(74, 'su5mijqfenc72vum40wokop9cvtfwbil', 'v1', 'classes', 'classes/list', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"q\":\"A search term for the class name\",\"class_teacher\":\"The unique id of the class teacher\",\"department_id\":\"The department id of the class to load\",\"class_id\":\"The unique id of the class\",\"class_assistant\":\"The unique id of the class assistant\",\"columns\":\"This lists only the requested columns\",\"load_courses\":\"Optionally use to load the courses for this class\",\"load_rooms\":\"Optionally use to load the classrooms for this class\",\"filter\":\"Additional filter to use\"}', 'active', 0, '2020-11-27 23:02:12', '2021-01-29 09:49:24', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe'),
(75, 'r58fkmqxb7rezo0euaj1umiqp9snywhd', 'v1', 'departments', 'departments/list', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"q\":\"A search term for the class name\",\"department_head\":\"The unique id of the department head\",\"department_id\":\"The unique id of the department\"}', 'active', 0, '2020-11-27 23:03:17', '2020-11-27 23:03:28', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(76, 'ljzganfuczysikqtrq1heodi3h6mw48v', 'v1', 'departments', 'departments/add', 'POST', '', '{\"department_code\":\"The department code\",\"name\":\"required - The name of the department\",\"image\":\"The department logo if any\",\"description\":\"A sample description of the department\",\"department_head\":\"The unique id of the department head\"}', 'active', 0, '2020-11-27 23:05:18', '2020-11-27 23:05:18', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(77, 'aw3d0vg12o6cszyfmiqvyt4eehx8fpwx', 'v1', 'departments', 'departments/update', 'POST', '', '{\"department_code\":\"The department code\",\"name\":\"required - The name of the department\",\"image\":\"The department logo if any\",\"description\":\"A sample description of the department\",\"department_head\":\"The unique id of the department head\",\"department_id\":\"required - The id of the department to update\"}', 'active', 0, '2020-11-27 23:05:57', '2020-11-27 23:05:57', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(78, '82uynfw7k90hpcwrkxqzrpylttmibajx', 'v1', 'sections', 'sections/update', 'POST', '', '{\"section_code\":\"The unique section code\",\"name\":\"required - The name of the section\",\"section_leader\":\"The unique id of the section leader\",\"description\":\"The description of the class (optional)\",\"section_id\":\"required - The unique of the section to update\"}', 'active', 0, '2020-11-27 23:07:30', '2020-11-27 23:07:30', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(79, 'yrlboe2l8atxuzbiz1kgcm6gntpvsu9d', 'v1', 'sections', 'sections/add', 'POST', '', '{\"section_code\":\"The unique section code\",\"name\":\"required - The name of the section\",\"section_leader\":\"The unique id of the section leader\",\"description\":\"The description of the class (optional)\"}', 'active', 0, '2020-11-27 23:07:55', '2020-11-27 23:07:55', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(80, 'ldvjfzjzkbaly13e5ksbxrf06a2p4qm8', 'v1', 'sections', 'sections/list', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"section_id\":\"The section id to load\",\"section_leader\":\"The unique id of the section leader\"}', 'active', 0, '2020-11-27 23:08:35', '2020-11-27 23:08:35', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(81, 'kfendr2by6aae08trixsxzu71odwpjov', 'v1', 'courses', 'courses/list', 'GET', '', '{\"limit\":\"The number of rows to return\",\"department_id\":\"The department id to fetch the courses offered\",\"course_tutor\":\"The unique id of the course tutor\",\"class_id\":\"The unique id of the class offering the course\",\"course_id\":\"The unique id of the course\",\"full_details\":\"A request for full information\",\"full_attachments\":\"This parameters loads all attachments for the course (all unit/lesson) attachments\",\"minified\":\"Just run a small set of query.\"}', 'active', 0, '2020-11-28 10:12:44', '2021-01-20 19:45:16', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(82, 'yz7euod8acemowbhtxp3gr6t0mjblakx', 'v1', 'courses', 'courses/add', 'POST', '', '{\"name\":\"required - The title of the course\",\"course_code\":\"required - The unique code of the course\",\"credit_hours\":\"The number of credit hours for the course\",\"class_id\":\"The unique id of the class offering this course\",\"course_tutor\":\"The unique id of the course tutor\",\"description\":\"The description or course content\",\"academic_year\":\"The academic year for this course\",\"academic_term\":\"The academic term for this course\",\"course_id\":\"Optional\",\"weekly_meeting\":\"The number of times this course is held in a week\"}', 'active', 0, '2020-11-28 10:16:58', '2021-01-22 22:04:09', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(83, '7vljhpahe0bgyc2mprfnyjsf1gvb6lzt', 'v1', 'courses', 'courses/update', 'POST', '', '{\"name\":\"required - The title of the course\",\"course_code\":\"The unique code of the course\",\"credit_hours\":\"The number of credit hours for the course\",\"class_id\":\"The unique id of the class offering this course\",\"course_tutor\":\"The unique id of the course tutor\",\"description\":\"The description or course content\",\"academic_year\":\"The academic year for this course\",\"academic_term\":\"The academic term for this course\",\"course_id\":\"required - The id of the course to update\",\"weekly_meeting\":\"The number of times this course is held in a week\"}', 'active', 0, '2020-11-28 10:17:22', '2021-02-19 14:29:01', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe'),
(84, 'mza14e8o2txn0yeuyvdxqjpivaf5rsk9', 'v1', 'courses', 'courses/add_unit', 'POST', '', '{\"name\":\"required - The name of the unit\",\"start_date\":\"The start date for the unit\",\"end_date\":\"The end date of the unit\",\"description\":\"The description of the unit\",\"course_id\":\"The course id\"}', 'active', 0, '2020-11-28 12:46:24', '2020-11-28 12:48:54', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(85, '3cfqmdg5p1csezzrerip7v06ykahtvws', 'v1', 'courses', 'courses/add_lesson', 'POST', '', '{\"name\":\"required - The name of the unit\",\"start_date\":\"The start date for the unit\",\"end_date\":\"The end date of the unit\",\"description\":\"The description of the unit\",\"course_id\":\"The course id\",\"unit_id\":\"The id of the unit to add this lesson\",\"lesson_id\":\"The id of the lesson to add\"}', 'active', 0, '2020-11-28 12:46:55', '2021-02-03 23:31:29', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(86, 'exo0tiyhb6y1nrvhkjle59owipm3s42t', 'v1', 'courses', 'courses/update_lesson', 'POST', '', '{\"name\":\"required - The name of the unit\",\"start_date\":\"The start date for the unit\",\"end_date\":\"The end date of the unit\",\"description\":\"The description of the unit\",\"course_id\":\"The course id\",\"unit_id\":\"The id of the unit to add this lesson\",\"lesson_id\":\"The id of the lesson to add\",\"attachment_file_upload\":\"\"}', 'active', 0, '2020-11-28 12:47:42', '2021-02-03 23:23:35', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(87, 'n7xaq9yywrmowk3n4v2a6utbqosplthz', 'v1', 'courses', 'courses/update_unit', 'POST', '', '{\"name\":\"required - The name of the unit\",\"start_date\":\"The start date for the unit\",\"end_date\":\"The end date of the unit\",\"description\":\"The description of the unit\",\"course_id\":\"The course id\",\"unit_id\":\"The id of the unit to update\"}', 'active', 0, '2020-11-28 12:48:18', '2020-11-28 12:48:33', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(88, 'izgpt5lncaqlicuxfmavpu1b3vtso6ke', 'v1', 'incidents', 'incidents/list', 'GET', '', '{\"incident_type\":\"The type of the incident (incident, followup)\",\"user_id\":\"The unique id of the user\",\"incident_date\":\"The date for the incident\",\"created_by\":\"The unique id of the user who created / replied to an incident\",\"incident_id\":\"The unique id of the incident\",\"full_details\":\"This includes the followup messages\"}', 'active', 0, '2020-11-29 14:59:17', '2020-11-29 17:18:55', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(89, 'ektkvnfcislrb8qpyal2vmfdzunc9x3w', 'v1', 'incidents', 'incidents/add', 'POST', '', '{\"subject\":\"required - The subject of the incident\",\"incident_date\":\"required - The date on which the incident occured.\",\"assigned_to\":\"The person to whom the incident has been assigned to handle\",\"location\":\"The location of the incident\",\"user_id\":\"required - The unique id of the user who this incident relates to.\",\"description\":\"Full description of the incident in question.\",\"reported_by\":\"The name/contact of the person who reported the incident\"}', 'active', 0, '2020-11-29 16:32:12', '2020-11-29 17:03:13', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(90, 'j29y0pdvqgzfwr4qtmmv6lhcpdacbj35', 'v1', 'incidents', 'incidents/update', 'POST', '', '{\"subject\":\"required - The subject of the incident\",\"incident_date\":\"required - The date on which the incident occured.\",\"assigned_to\":\"The person to whom the incident has been assigned to handle\",\"location\":\"The location of the incident\",\"user_id\":\"required - The unique id of the user who this incident relates to.\",\"description\":\"Full description of the incident in question.\",\"reported_by\":\"The name/contact of the person who reported the incident\",\"incident_id\":\"required - The unique id of the incident\",\"status\":\"The status of the incident.\"}', 'active', 0, '2020-11-29 16:32:49', '2020-11-29 21:44:51', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(91, 'le0gdwiayi4pg7lmudhsqnjqf6bestp5', 'v1', 'incidents', 'incidents/add_followup', 'POST', '', '{\"incident_id\":\"required - The unique id of the incident\",\"user_id\":\"required - The unique id of the user to whom this incident relates\",\"comment\":\"required - The comment to add to this followup\"}', 'active', 0, '2020-11-29 23:27:56', '2020-11-29 23:28:15', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(92, 'md6ksfvdozctbync2xweloqmirgujzlh', 'v1', 'resources', 'resources/upload_4courses', 'POST', '', '{\"upload\":\"required - An array of items for upload\",\"the_file\":\"The file to upload\"}', 'active', 0, '2020-11-30 17:17:14', '2020-11-30 18:52:23', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(93, '0e3yxwdoqj2hulznchvfskudpkxly84j', 'v1', 'users', 'users/guardian_list', 'GET', '', '{\"client_id\":\"The unique id of the client to load the results\",\"append_wards\":\"Append the wards of the user\"}', 'active', 0, '2020-12-11 18:04:47', '2020-12-11 18:25:21', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(94, 'jyv34gkf0obuxqzhykricz12wvptmwmo', 'v1', 'attendance', 'attendance/display_attendance', 'GET', '', '{\"class_id\":\"This loads the class attendance for the specified date range.\",\"date_range\":\"This is the date range to load the attendance log.\",\"user_type\":\"The type of users to search for\"}', 'active', 0, '2020-12-16 06:02:55', '2020-12-16 06:32:23', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(95, 'zbdo1unr04lrhlpdfyqkwhg9vymwnzxe', 'v1', 'users', 'users/guardian_update', 'POST', '', '{\"guardian_id\":\"required - The unique id of the user to update\",\"gender\":\"The gender of the user\",\"image\":\"The display picture of the guardian\",\"fullname\":\"The fullname of the guardian\",\"date_of_birth\":\"The date of birth of the guardian\",\"email\":\"The email address\",\"contact\":\"The primary contact of the user\",\"contact_2\":\"The secondary contact of the user \",\"address\":\"The postal address\",\"residence\":\"The place of residence\",\"country\":\"The country of the user\",\"employer\":\"The name of the employer (company name)\",\"occupation\":\"The profession of the user\",\"description\":\"Any additional information of the user\",\"blood_group\":\"The blood group of the guardian\"}', 'active', 0, '2020-12-17 09:49:34', '2020-12-29 21:56:10', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(96, 'fhfotj03yx9prjo6cndilzebq2scp8w5', 'v1', 'users', 'users/guardian_add', 'POST', '', '{\"guardian_id\":\"required - The unique id of the user to update\",\"gender\":\"The gender of the user\",\"image\":\"The display picture of the guardian\",\"fullname\":\"required - The fullname of the guardian\",\"date_of_birth\":\"The date of birth of the guardian\",\"email\":\"The email address\",\"contact\":\"required - The primary contact of the user\",\"contact_2\":\"The secondary contact of the user \",\"address\":\"The postal address\",\"residence\":\"The place of residence\",\"country\":\"The country of the user\",\"employer\":\"The name of the employer (company name)\",\"occupation\":\"The profession of the user\",\"description\":\"Any additional information of the user\",\"blood_group\":\"The blood group of the guardian\"}', 'active', 0, '2020-12-17 09:50:16', '2020-12-29 21:56:04', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(97, 'ecykfrdjb4gvzqyg2milv0ntofacui5e', 'v1', 'users', 'users/modify_guardianward', 'POST', '', '{\"user_id\":\"required - The unique id for the guardian and the ward\",\"todo\":\"required - The activity to perform (append, remove).\"}', 'active', 0, '2020-12-18 07:00:45', '2020-12-18 07:00:45', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(98, 'ydhcdkmtco4onq02isbu1ylztisr8jpv', 'v1', 'users', 'users/save_permission', 'POST', '', '{\"access_level\":\"required - An array string containing the access permissions of the user\",\"user_id\":\"required - The user id to update the access permission.\"}', 'active', 0, '2020-12-18 18:53:15', '2020-12-18 18:53:15', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(99, '7lhlrvuwmhj4jtbmsgqdfsapiquxcgfb', 'v1', 'assignments', 'assignments/load_course_students', 'GET', 'This endpoint in assignments loads both the course and students list using the class id as a filter.', '{\"class_id\":\"required - The class id to filter the results list.\"}', 'active', 0, '2020-12-21 06:58:32', '2020-12-21 06:58:32', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(100, 'cnl54rkmwfpovdqqojdzwh3rauig7enz', 'v1', 'assignments', 'assignments/add', 'POST', '', '{\"assignment_type\":\"required - The type of assignment type to upload (multiple_choice or file_attachment)\",\"assignment_title\":\"required - The title of the assignment\",\"description\":\"Any additional instructions added to the assignment\",\"grade\":\"required - The grade for this assignment\",\"date_due\":\"required - The date on which the assignment is due.\",\"time_due\":\"The time for submission\",\"assigned_to\":\"required - This determines whether to assign the assignment to all students in the class or to specific students\",\"assigned_to_list\":\"This is needed when you decide to assign the assignment to specific students.\",\"class_id\":\"required - The id of the class to assign the assignment\",\"course_id\":\"required - The unique id of the course to link this assignment.\"}', 'active', 0, '2020-12-21 07:53:09', '2020-12-23 08:13:32', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe'),
(101, 'vupvk8ffjdwit3ysduqrsqjbxhn02goe', 'v1', 'assignments', 'assignments/list', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"assignment_id\":\"The unique id of the assignment to laod the data\"}', 'active', 0, '2020-12-21 08:26:54', '2020-12-21 08:26:54', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(102, '2k91razy8emcegbhsmj3iq0bgkujpfzp', 'v1', 'assignments', 'assignments/student_info', 'GET', '', '{\"student_id\":\"required - The unique id of the student\",\"assignment_id\":\"required - The unique id of the assignment\",\"preview\":\"Boolean value of either 0 or 1\"}', 'active', 0, '2020-12-21 14:10:08', '2020-12-21 14:13:07', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(103, 'lfrowhwxiu1emhszarjqfinbdac2jyp7', 'v1', 'assignments', 'assignments/award_marks', 'POST', '', '{\"student_list\":\"required - An array of the students list\",\"assignment_id\":\"required - The unique id of the assignment\"}', 'active', 0, '2020-12-21 15:29:58', '2020-12-21 15:42:49', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(104, 'a2grs865f79romhwelfcjo4ziulghbxu', 'v1', 'assignments', 'assignments/handin', 'POST', '', '{\"assignment_id\":\"required - The unique assignment id to handin.\",\"answers\":\"The answer to the question\",\"question_id\":\"The unique id of the current question\"}', 'active', 0, '2020-12-22 06:26:29', '2021-01-26 11:47:14', '0', '0', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo'),
(105, 'ikr5lyhutqvox42tdjamdzrscpy89wxf', 'v1', 'assignments', 'assignments/close', 'POST', '', '{\"assignment_id\":\"required - The unique assignment id to close.\"}', 'active', 0, '2020-12-22 09:27:40', '2020-12-22 09:27:40', '0', '0', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', NULL),
(106, 'qpenjlpgilhwjkyszofuhub3ocwmtebr', 'v1', 'assignments', 'assignments/update', 'POST', '', '{\"assignment_type\":\"required - The type of assignment type to upload (multiple_choice or file_attachment)\",\"assignment_title\":\"required - The title of the assignment\",\"description\":\"Any additional instructions added to the assignment\",\"grade\":\"required - The grade for this assignment\",\"date_due\":\"required - The date on which the assignment is due.\",\"time_due\":\"The time for submission\",\"assigned_to\":\"required - This determines whether to assign the assignment to all students in the class or to specific students\",\"assigned_to_list\":\"This is needed when you decide to assign the assignment to specific students.\",\"class_id\":\"required - The id of the class to assign the assignment\",\"course_id\":\"required - The unique id of the course to link this assignment.\",\"assignment_id\":\"required - The unique id of the assignment to update the record.\"}', 'active', 0, '2020-12-22 14:59:52', '2020-12-23 08:09:48', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe'),
(107, 'wpun52gxrmylbnuopkeoxe8qvtcj1bzc', 'v1', 'assignments', 'assignments/reopen', 'POST', '', '{\"assignment_id\":\"required - The unique id of the assignment to reopen\"}', 'active', 0, '2020-12-23 07:53:07', '2020-12-23 07:53:07', '0', '0', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', NULL),
(108, 'fsf9xl0ykqcje1zmhiadbxnctwgg2dn3', 'v1', 'assignments', 'assignments/add_question', 'POST', 'This endpoint is used to both add and update a question under a specific assignment', '{\"option_a\":\"required - The value for Option A\",\"option_b\":\"required - The value for Option B\",\"option_c\":\"required - The value for Option C\",\"option_d\":\"The value for Option D\",\"option_e\":\"The value for Option E\",\"question\":\"required - The question detail\",\"answer_type\":\"The type of the answer to process\",\"question_id\":\"The unique id of the question\",\"assignment_id\":\"required - The assignment id\",\"difficulty\":\"The difficulty level of the question\",\"answers\":\"An array of selected options\",\"numeric_answer\":\"If the answer is numeric this should show\",\"marks\":\"The marks for the question\"}', 'active', 0, '2020-12-23 15:10:23', '2020-12-24 22:10:36', '0', '0', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe'),
(109, 'm8hxel6jpwfbyu2iqc4s3jqaf1krdvkh', 'v1', 'assignments', 'assignments/review_question', 'GET', '', '{\"assignment_id\":\"required - The unique assignment id to review.\",\"question_id\":\"required - The unique id of the question\"}', 'active', 0, '2020-12-23 21:19:12', '2020-12-23 21:19:12', '0', '0', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', NULL),
(110, 'jj2fi9v8vwf0b1lrmxy4edaopnwhgeg7', 'v1', 'assignments', 'assignments/publish', 'POST', '', '{\"assignment_id\":\"required - The id of the assignment to publish.\"}', 'active', 0, '2020-12-23 23:01:59', '2020-12-23 23:01:59', '0', '0', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', NULL),
(111, '9ojxbvfyqeocke3jrt0hbpgucpkztlsr', 'v1', 'assignments', 'assignments/save_answer', 'POST', '', '{\"question_id\":\"required - This is the unique id of the question to load\",\"answers\":\"This is the array of answers selected\",\"previous_id\":\"This will determine the next question to load\"}', 'active', 0, '2020-12-25 23:05:34', '2020-12-26 07:58:06', '0', '0', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo'),
(112, 'ymdwiwoga59nchjqzsdslrt6fgfluxzo', 'v1', 'assignments', 'assignments/review_answers', 'POST', 'Load the answers selected by this user for the specified assignment', '{\"assignment_id\":\"required - The unique id of the assignment\",\"review_answers\":\"An array of the last question answers\",\"answers\":\"The answer to the question\",\"question_id\":\"The unique id of the current question\",\"student_id\":\"The student id to load\",\"show_answer\":\"Set when the answers to the questions is to be loaded.\"}', 'active', 0, '2020-12-28 11:41:46', '2021-01-26 17:19:20', '0', '0', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo'),
(113, '0eyocpqa958qstkvxolmn6p4bbjsxu1l', 'v1', 'attendance', 'attendance/log', 'POST', '', '{\"date\":\"required - The date to log the attendance\",\"attendance\":\"This is an array of user_ids and their status\",\"user_type\":\"This denotes the user type to query.\",\"class_id\":\"The class id is needed if the user type is student.\",\"finalize\":\"This parameter is set when there is the need to finalize the log\"}', 'active', 0, '2020-12-28 20:30:47', '2020-12-29 09:16:46', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(114, 'v4yzw52dmfvsbjmfhsajoipqye63ollx', 'v1', 'events', 'events/add_type', 'POST', '', '{\"name\":\"required - The name of the type\",\"description\":\"Any additional description of the type\",\"icon\":\"The icon to be used to represent events that falls under this category\",\"color_code\":\"The color code for the event type\"}', 'active', 0, '2020-12-29 19:35:20', '2021-01-01 16:37:51', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe'),
(115, '6d8roxdljhw2vnkwzb1et0yiahqqy4rl', 'v1', 'events', 'events/update_type', 'POST', '', '{\"name\":\"required - The name of the type\",\"description\":\"Any additional description of the type\",\"type_id\":\"required - The unique id of the type\",\"icon\":\"The icon to be used to represent events that falls under this category\",\"color_code\":\"The color code for the event type\"}', 'active', 0, '2020-12-29 19:35:47', '2021-01-01 16:37:47', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe'),
(116, 'y0ckjulcbkoqvdfyen2d9nuigzmhaeo6', 'v1', 'users', 'users/modify_wardguardian', 'POST', 'Modify the guardians list attached to a student', '{\"user_id\":\"required - The unique id for the guardian and the ward\",\"todo\":\"required - The activity to perform (append, remove).\"}', 'active', 0, '2020-12-29 23:19:41', '2020-12-29 23:19:41', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(117, '6z8t4vx5rhnhkmorjcwezxbdsq237g9b', 'v1', 'events', 'events/add', 'POST', 'This endpoint adds a new event into the system.', '{\"title\":\"required - The title of the event\",\"type\":\"required - The type of event to add\",\"audience\":\"required - The audience of the event\",\"date\":\"required - The date of the event\",\"holiday\":\"To ascertain whether the event is a holiday or not\",\"event_image\":\"Any image to attach to this event\",\"description\":\"Any additional information to be added to this event.\",\"is_mailable\":\"Specify whether this event can be emailed to the users list specified\",\"status\":\"The status of the event\"}', 'active', 0, '2020-12-30 09:22:11', '2021-01-02 00:21:33', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(118, 'g7bieqmavu2ynzzlosfw1cfsyh3ivtwp', 'v1', 'events', 'events/update', 'POST', '', '{\"title\":\"required - The title of the event\",\"type\":\"The type of event to add\",\"audience\":\"The audience of the event\",\"date\":\"required - The date of the event\",\"holiday\":\"To ascertain whether the event is a holiday or not\",\"event_image\":\"Any image to attach to this event\",\"description\":\"Any additional information to be added to this event.\",\"event_id\":\"required - The unique id of the event\",\"is_mailable\":\"Specify whether this event can be emailed to the users list specified\",\"status\":\"This is the status of the event\"}', 'active', 0, '2020-12-30 09:23:01', '2021-01-01 23:33:48', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(119, 'o5pmu71qxrjp8moyj6gzgawdtbb2kysv', 'v1', 'events', 'events/list', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"event_id\":\"The unique id of the event\",\"event_date\":\"The date on which the event will be held\",\"audience\":\"The audience to receive this event\"}', 'active', 0, '2020-12-30 10:17:54', '2020-12-30 10:20:46', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(120, 'ibxuohpuwjz4b96jzalqs0wepmnngctg', 'v1', 'events', 'events/types_list', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"type_id\":\"The unique id of the event type\",\"show_events\":\"When parsed it will also list all events found under the each type\"}', 'active', 0, '2020-12-30 10:18:51', '2020-12-30 10:19:27', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(121, 'newylzxjawo2vdtv7p6mbjusdqmtln4a', 'v1', 'records', 'records/remove', 'POST', '', '{\"resource\":\"required - This is the resource to delete\",\"record_id\":\"required - This is the unique id of the record to delete\"}', 'active', 0, '2021-01-01 21:06:58', '2021-01-01 21:06:58', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(122, 'lwzcv1xwjnshkpipxtqr3mhlkfn7bg4d', 'v1', 'library', 'library/add_book', 'POST', '', '{\"title\":\"required - The title of the book\",\"isbn\":\"required - The unique identification code for the book\",\"author\":\"required - The author of the book\",\"rack_no\":\"The rack on which the book could be located\",\"row_no\":\"The row on the rack number to locate the book\",\"quantity\":\"required - The quantity of the books available in stock\",\"department_id\":\"The unique id of the department\",\"class_id\":\"The unique id of the class\",\"category_id\":\"required - The category under which this book falls\",\"description\":\"The summary description of the book\",\"code\":\"The unique code the item\",\"book_image\":\"The cover image for the book\"}', 'active', 0, '2021-01-02 12:53:28', '2021-01-21 08:53:01', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(123, 'yggtpdlj6m4feqczo1jkavsm83wihu95', 'v1', 'library', 'library/update_book', 'POST', '', '{\"title\":\"required - The title of the book\",\"isbn\":\"required - The unique identification code for the book\",\"author\":\"required - The author of the book\",\"rack_no\":\"The rack on which the book could be located\",\"row_no\":\"The row on the rack number to locate the book\",\"quantity\":\"The quantity of the books available in stock\",\"department_id\":\"The unique id of the department\",\"class_id\":\"The unique id of the class\",\"category_id\":\"required - The category under which this book falls\",\"description\":\"The summary description of the book\",\"book_id\":\"required - The unique id of the book to update\",\"code\":\"The unique code the item\",\"book_image\":\"The cover image for the book\"}', 'active', 0, '2021-01-02 12:54:08', '2021-01-21 08:53:13', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(124, 'mfxzhx0vt3oo5jnbtekv8waefzrusdwl', 'v1', 'library', 'library/list', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"department_id\":\"The unique id of the department\",\"class_id\":\"The unique id of the class\",\"category_id\":\"The category under which this book falls\",\"description\":\"The summary description of the book\",\"book_id\":\"The unique id of the book to update\",\"isbn\":\"The unique identification code for the book\",\"show_in_list\":\"This is applicable if the user wants to ascertain whether the book has been added in a session to be issued out or requested.\",\"minified\":\"If parsed then the result will be simplified\"}', 'active', 0, '2021-01-02 12:55:28', '2021-01-04 20:09:35', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva');
INSERT INTO `users_api_endpoints` (`id`, `item_id`, `version`, `resource`, `endpoint`, `method`, `description`, `parameter`, `status`, `counter`, `date_created`, `last_updated`, `deleted`, `deprecated`, `added_by`, `updated_by`) VALUES
(125, 'jwn17zfsaecz43ih96ouqyfiela2hv5k', 'v1', 'library', 'library/upload_resource', 'POST', '', '{\"book_id\":\"required - The book id to upload the files to\"}', 'active', 0, '2021-01-02 21:24:24', '2021-01-02 21:24:24', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(126, 'duts60wylhukvkdfimwpbtcp9lhoszya', 'v1', 'library', 'library/update_category', 'POST', '', '{\"name\":\"required - The title of the category\",\"department_id\":\"The department of the book category\",\"description\":\"The description of the category\",\"category_id\":\"required - The unique id of the category to update.\"}', 'active', 0, '2021-01-03 22:46:04', '2021-01-03 22:46:04', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(127, 'eagq8tdpnik0yvrjvobwscbuz3z4r9d7', 'v1', 'library', 'library/add_category', 'POST', '', '{\"name\":\"required - The title of the category\",\"department_id\":\"The department of the book category\",\"description\":\"The description of the category\"}', 'active', 0, '2021-01-03 22:46:37', '2021-01-03 22:46:37', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(128, 'sggcjcpqyzuphbuvw9ijdwxq7e5forto', 'v1', 'library', 'library/issue_request_handler', 'POST', '', '{\"label\":\"required - An array that contains the request to perform. Parameters: todo - add, remove, request and issue / book_id - Required if the todo is either add or remove.\"}', 'active', 0, '2021-01-04 21:05:19', '2021-01-04 21:18:57', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(129, 'jcz6d2qh85bfkpewit3zqcw1nab7m0jy', 'v1', 'library', 'library/issued_request_list', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"borrowed_id\":\"The unique id of the borrowed id\",\"user_id\":\"The unique id of the user who requested for the books\",\"return_date\":\"Filter by the date on which books are to be returned\",\"issued_date\":\"Filter by the date on which the books were issued\",\"status\":\"Filter by the status of the request\",\"show_list\":\"This when appended while show the details of the book borrowed\"}', 'active', 0, '2021-01-06 08:16:35', '2021-01-06 08:17:49', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(130, 'aeqtpxgzy5ho3v8cldtsiprb47zcu0fq', 'v1', 'fees', 'fees/list', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"student_id\":\"The unique id of the student to load the record\",\"class_id\":\"The unique id of the class to load the record\",\"academic_year\":\"The academic year to load the information\",\"academic_term\":\"The academic term to load the information\"}', 'active', 0, '2021-01-08 07:43:05', '2021-01-08 07:43:05', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(131, 'fcgwydlvdkss1tivtx2uro9pbma5zqyk', 'v1', 'fees', 'fees/payment_form', 'GET', '', '{\"department_id\":\"This is the unique id of the department\",\"class_id\":\"This is the unique id of the class of the student\",\"student_id\":\"The unique id of the student\",\"category_id\":\"The fees category type to load\",\"show_history\":\"When submitted in the query, the result will contain the payment history of the student (if supplied)\"}', 'active', 0, '2021-01-08 11:43:35', '2021-01-08 11:43:35', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(132, 'd6kwitjdx81rnocfabuefoqywtrvsb59', 'v1', 'fees', 'fees/allocate_fees', 'POST', '', '{\"allocate_to\":\"required - This specifies whether to allot the fees to the class or student\",\"amount\":\"required - This is the amount.\",\"category_id\":\"required - This is the category id of the fees type\",\"student_id\":\"This is only needed if the allocate_to is equal to student.\",\"class_id\":\"This is required for insertion. If not specified, the said fees will be allotted to all active classes in the database.\"}', 'active', 0, '2021-01-08 16:19:16', '2021-01-08 16:20:11', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(133, '5gnulblaojzrmyke1chz3yivchsxp6gu', 'v1', 'fees', 'fees/allocate_fees_amount', 'GET', 'Get the fees allotted a class or student', '{\"allocate_to\":\"required - This specifies whether to allot the fees to the class or student\",\"category_id\":\"required - This is the category id of the fees type\",\"student_id\":\"This is only needed if the allocate_to is equal to student.\",\"class_id\":\"This is required for insertion. If not specified, the said fees will be allotted to all active classes in the database.\"}', 'active', 0, '2021-01-08 21:12:22', '2021-01-08 21:25:46', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(134, 'blgc8rfdehuqmcq6iiohygn0trv7uevt', 'v1', 'fees', 'fees/make_payment', 'POST', '', '{\"checkout_url\":\"required - This is the checkout url for making payments\",\"payment_method\":\"required - The mode for making the payment\",\"amount\":\"required - This is the amount to be made.\",\"description\":\"The description for the payment (optional)\",\"bank_id\":\"The unique id of the bank if a cheque is used to make payment\",\"cheque_number\":\"The unique number of the cheque if payment is being made using a cheque.\",\"cheque_security\":\"The security code on the cheque.\",\"student_id\":\"The student id to receive payment.\"}', 'active', 0, '2021-01-09 19:22:02', '2021-05-11 22:17:05', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(135, '01gwzvszytiwhn3fkfulasxciee6yv72', 'v1', 'analitics', 'analitics/generate', 'GET', '', '{\"period\":\"The period for the data collection (Default: this_week)\",\"label\":\"This will be an array of information to generate the report\"}', 'active', 0, '2021-01-15 22:26:22', '2021-01-15 22:26:22', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(136, 'cv1z6jdijok4lbireaxv25hyzsonfrc3', 'v1', 'users', 'users/set_default_student', 'POST', '', '{\"student_id\":\"required - The student id to set as the default id.\"}', 'active', 0, '2021-01-21 13:11:48', '2021-01-21 13:11:48', '0', '0', '5Jz7Lv8aZ1Gi43MxDXBlfqRn9uYQjIEc', NULL),
(137, 'o1y0arsbxfjclzahgzx2wswitjuy4m9l', 'v1', 'rooms', 'rooms/list', 'GET', '', '{\"limit\":\"The number of rows to list\",\"code\":\"The room unique code\"}', 'active', 0, '2021-01-22 14:57:22', '2021-01-22 14:57:22', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(138, 'ncehg1fp96hkrzzsl7fuuy5my8dekxdt', 'v1', 'rooms', 'rooms/add_classroom', 'POST', '', '{\"code\":\"The room code\",\"name\":\"required - The name of the classroom\",\"capacity\":\"The number of students to occupy this room\",\"class_id\":\"The unique id of the class to attach to this room\",\"description\":\"A sample description of the classroom\"}', 'active', 0, '2021-01-22 14:59:13', '2021-01-22 15:12:36', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(139, 'anzngkpiomhq7ulc30ry1fve4djxsv6t', 'v1', 'rooms', 'rooms/update_classroom', 'POST', '', '{\"code\":\"The room code\",\"name\":\"required - The name of the classroom\",\"capacity\":\"The number of students to occupy this room\",\"class_id\":\"The unique id of the class to attach to this room\",\"class_room_id\":\"required - The unique id of the classroom to update\",\"description\":\"A sample description of the classroom\"}', 'active', 0, '2021-01-22 14:59:55', '2021-01-22 15:12:41', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(140, 'hnuy7ps0wh1zk5ybmcsd2rmlugedfkan', 'v1', 'timetable', 'timetable/save', 'POST', '', '{\"slots\":\"required - The number of slots\",\"days\":\"required - The number of days for the slot\",\"duration\":\"required - The duration for each session\",\"start_time\":\"required - The start time for each day\",\"disabled_inputs\":\"Any input fields that have been disabled\",\"timetable_id\":\"The unique id if the user wants to update an existing timetable record.\",\"name\":\"The name of the timetable\",\"class_id\":\"The class id for this timetable\"}', 'active', 0, '2021-01-22 20:54:11', '2021-01-22 22:43:35', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(141, 'ptujidskhagnvoxokm70f6rhesp8bvyw', 'v1', 'timetable', 'timetable/set_timetable_id', 'POST', '', '{\"timetable_id\":\"required - Set the current default timetable_id to work on.\"}', 'active', 0, '2021-01-23 07:45:03', '2021-01-23 07:45:03', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(142, 'tky3jv0pjfqoxbwhf6wqmzcdtkys7zea', 'v1', 'timetable', 'timetable/allocate', 'POST', '', '{\"data\":\"required - This will contain an array of data set to process.\"}', 'active', 0, '2021-01-23 08:16:28', '2021-01-23 08:16:28', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(143, 'smdetrywgqof3ndyqshl1he2blf4t7m8', 'v1', 'timetable', 'timetable/list', 'GET', '', '{\"full_detail\":\"optional parameter to load full information\",\"timetable_id\":\"The unique id of the timetable record\",\"class_id\":\"Specify the class_id to load specifics\"}', 'active', 0, '2021-01-23 11:28:53', '2021-01-23 11:34:31', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(144, 'jw90y6upfh3sxmigwivqbrbxgzrtzdef', 'v1', 'timetable', 'timetable/draw', 'GET', '', '{\"timetable_id\":\"required - This returns a table of the timetable record.\",\"load\":\"Accepted values are: yesterday, today or tomorrow\"}', 'active', 0, '2021-01-23 17:15:39', '2021-01-20 07:43:38', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(145, 'fsk3jgtu9coh2vd5peyby6pqabtelzcu', 'v1', 'account', 'account/update', 'POST', '', '{\"general\":\"An array of data to update\",\"import\":\"This is an array of data to import\",\"logo\":\"The school logo\"}', 'active', 0, '2021-01-24 15:16:28', '2021-01-24 15:36:18', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(146, '12ocyvjbqc0bmani465rlgs3hdefttez', 'v1', 'account', 'account/upload_csv', 'POST', '', '{\"csv_file\":\"required - This is the csv file to import\",\"column\":\"required - This is the data type to upload\"}', 'active', 0, '2021-01-24 18:31:54', '2021-01-24 18:31:54', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(147, 'f3nnmlxxjjubwg1iu8ice5r0oaky4tqa', 'v1', 'account', 'account/import', 'POST', '', '{\"csv_values\":\"required - An array of values\",\"csv_keys\":\"required - An array of column headers\",\"column\":\"required - The dataset to upload\"}', 'active', 0, '2021-01-24 20:29:53', '2021-01-24 20:29:53', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(148, 'uopknqyzpeb8fgise1dwinfhquvcmlda', 'v1', 'account', 'account/download_temp', 'GET', 'Use this endpoint to download a temporary file for upload', '{\"file\":\"required - This is the file to download.\"}', 'active', 0, '2021-01-26 18:14:25', '2021-01-26 18:14:25', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(149, 'yrmmok7pj0jes3bcywphiwt9a1tfxnvx', 'v1', 'payroll', 'payroll/paymentdetails', 'POST', '', '{\"employee_id\":\"required - The unique id of the employee\",\"basic_salary\":\"The gross salary of the employee\",\"allowances\":\"An array of allowances receivable by the employee\",\"deductions\":\"An array of deductions to be made from the gross salary\",\"account_name\":\"The Bank Account name\",\"account_number\":\"The account number of the employee\",\"bank_name\":\"The name of the bank\",\"bank_branch\":\"The bank account branch\",\"ssnit_number\":\"The SSNIT number of the employee\",\"tin_number\":\"The Tax Identification Number of the employee\"}', 'active', 0, '2021-01-27 13:10:11', '2021-01-27 13:54:41', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(150, 'cjdgaxsck37tmuoybg14eblyqfsnj6ra', 'v1', 'payroll', 'payroll/payslipdetails', 'GET', '', '{\"employee_id\":\"required - The unique id of the employee\",\"month_id\":\"required - The month to load\",\"year_id\":\"required - The year to fetch the record\"}', 'active', 0, '2021-01-27 17:25:04', '2021-01-27 17:25:04', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(151, 'vp3kq6zq7puln1mcjyfxrt8rfohcwn4i', 'v1', 'payroll', 'payroll/generatepayslip', 'POST', '', '{\"allowances\":\"An array of allowances to receive\",\"deductions\":\"An array of deductions to be made.\",\"basic_salary\":\"required - The basic salary of the employee\",\"comments\":\"Any comments to share.\",\"payment_mode\":\"The mode of payment of the salary\",\"payment_status\":\"The payment status\",\"month_id\":\"required - The month id to generate the payslip\",\"year_id\":\"required - The year id to generate the payslip\",\"employee_id\":\"required - The employee id to generate the payslip for\"}', 'active', 0, '2021-01-27 19:50:08', '2021-01-27 19:50:08', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(152, 'cvbxjrq7wrg3cmbakpd4jogohyeql9st', 'v1', 'payroll', 'payroll/paysliplist', 'GET', '', '{\"employee_id\":\"The unique id of the employee\",\"month_id\":\"The month to load\",\"year_id\":\"The year of the payslip\",\"created_by\":\"The unique id of the one who created the payslip\"}', 'active', 0, '2021-01-28 09:17:52', '2021-01-28 09:18:48', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(153, 'vdljdyuqcwtgmhnibxzgja69x7r0e8kz', 'v1', 'records', 'records/validate', 'POST', '', '{\"label\":\"required - An array of actions to perform.\"}', 'active', 0, '2021-01-28 09:54:16', '2021-01-28 09:54:16', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(154, 'ldefbjonqm51xsvm8ohahgkw9vidplus', 'v1', 'payroll', 'payroll/saveallowance', 'POST', '', '{\"name\":\"required - The name of the allowance type\",\"type\":\"required - The type of record: Allowance or Deduction\",\"description\":\"The full description of the item\",\"allowance_id\":\"When specified the query will update the existing record. If not then a new record will be inserted.\",\"default_amount\":\"This is either the default amount or percentage attached to this allowance\"}', 'active', 0, '2021-01-29 15:17:43', '2021-01-29 15:34:59', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(155, 'auxx3zrevvlih4m8pnwzhquo6yft2lq9', 'v1', 'resources', 'resources/e_courses', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"class_id\":\"The unique class id to filter the results\",\"course_id\":\"The unique id of the course to load the resources\",\"unit_id\":\"This is the unique unit id to load the resources\",\"lesson_id\":\"This is the unique lesson id to load the resources uploaded\",\"rq\":\"A search term to be used while searching for the record set from the resources table.\",\"start\":\"The point to start the query from.\"}', 'active', 0, '2021-02-03 23:33:08', '2021-02-04 23:18:58', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(156, '96x1byedgwphzk7ja4imlhkcty3rvq8r', 'v1', 'resources', 'resources/e_resources', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"class_id\":\"The unique class id to filter the results\",\"course_id\":\"The unique id of the course to load the resources\",\"unit_id\":\"This is the unique unit id to load the resources\",\"lesson_id\":\"This is the unique lesson id to load the resources uploaded\",\"rq\":\"A search term to be used while searching for the record set from the resources table.\",\"start\":\"The point to start the query from.\",\"resource_id\":\"If a unique resource is to be loaded.\"}', 'active', 0, '2021-02-04 23:18:33', '2021-02-05 13:34:02', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(157, 'nqhlzhvput3vkre1rzwsk4mgyat5cfxg', 'v1', 'courses', 'courses/course_unit_lessons_list', 'GET', '', '{\"course_id\":\"The course to load the lessons and units\",\"minified\":\"If parsed a simple data will be loaded\",\"unit_id\":\"The unit id of the data to load.\",\"type\":\"The type can either be unit or lesson\"}', 'active', 0, '2021-02-05 07:59:16', '2021-02-05 07:59:16', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(158, 'w3y5ky0mzvvtxphouxsehgjiarqbjwg4', 'v1', 'resources', 'resources/upload_4elearning', 'POST', '', '{\"class_id\":\"required - The class to upload this material for\",\"course_id\":\"required - The course to associate this material with\",\"unit_id\":\"The course unit id to assign this material\",\"title\":\"required - The title of material\",\"description\":\"This is a summary description of the e-learning material\",\"allow_comment\":\"This option determines whether to allow comments or not. Accepts: allow/disallow\",\"state\":\"This is the state of the material. Default is Published or Draft\"}', 'active', 0, '2021-02-05 08:11:58', '2021-02-05 08:14:34', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(159, '1ps6qtcwtxiovfrrh5zeypub82jnalnk', 'v1', 'resources', 'resources/update_4elearning', 'POST', '', '{\"resource_id\":\"required - This is the unique id of the resource to upload\",\"class_id\":\"required - The class to upload this material for\",\"course_id\":\"required - The course to associate this material with\",\"unit_id\":\"The course unit id to assign this material\",\"title\":\"required - The title of material\",\"description\":\"This is a summary description of the e-learning material\",\"allow_comment\":\"This option determines whether to allow comments or not. Accepts: allow/disallow\",\"state\":\"This is the state of the material. Default is Published or Draft\"}', 'active', 0, '2021-02-05 08:12:51', '2021-02-05 08:14:14', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(160, '6lvcn4nfizjp2fmwclkydosv5xu1r7qt', 'v1', 'replies', 'replies/share', 'POST', '', '{\"comment\":\"required - The comment to share\",\"record_id\":\"This is the unique id of the video to share.\",\"comment_id\":\"This is applicable when its a reply to a comment\",\"video_time\":\"Save the current video time while sharing the comment.\"}', 'active', 0, '2021-02-05 20:44:14', '2021-02-05 23:02:16', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(161, 'chi3ix9fqzgl4hpwss6t5tzjnv2yndbr', 'v1', 'resources', 'resources/comments_list', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"record_id\":\"required - This is the record to load the comments list\",\"last_comment_id\":\"The last comment id\",\"type\":\"This can either be reply or comment\"}', 'active', 0, '2021-02-05 21:35:21', '2021-02-05 23:01:55', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(162, '4ctszybl3gfxjiswfvn2yxj6b81hnqza', 'v1', 'dictionary', 'dictionary/search', 'GET', '', '{\"term\":\"required - This is the term to lookup for.\",\"deep_search\":\"This takes a boolean value. When set to true it will return a lot of data\"}', 'active', 0, '2021-02-07 20:30:41', '2021-02-07 21:14:17', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(163, 'etz1upuvbni5gpky0omxyqqcl9axwvjt', 'v1', 'fees', 'fees/savecategory', 'POST', '', '{\"name\":\"required - This is the name of the fee category\",\"code\":\"This is a unique code to denote the category\",\"amount\":\"required - This is the amount attached to this fee category\",\"description\":\"This is the full description of the category\",\"category_id\":\"This is the category id if it needs to be updated\"}', 'active', 0, '2021-02-18 16:22:40', '2021-02-18 16:22:40', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(164, 'zm2tepl9xqgadjmgwcn6vfcwssyiq83f', 'v1', 'account', 'account/complete_setup', 'POST', '', '', 'active', 0, '2021-02-20 09:31:50', '2021-02-20 09:32:07', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(165, '5gmgebyuf6ao1luzlrpsnjkvsx39c8tp', 'v1', 'chats', 'chats/search_user', 'GET', '', '{\"q\":\"required - The user to search for.\"}', 'active', 0, '2021-02-20 15:06:59', '2021-02-20 15:06:59', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', NULL),
(166, '5zno3bxqf0khmwg284gcxe6lsouavkrv', 'v1', 'chats', 'chats/send', 'POST', '', '{\"receiver_id\":\"required - The unique id of the user to receive the message\",\"message_id\":\"The unique id of the message interaction between the two users\",\"sender_id\":\"This is the unique id of the send of the message\",\"message\":\"required - The message to be sent.\"}', 'active', 0, '2020-10-14 14:24:23', '2020-10-14 14:24:23', '0', '0', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', NULL),
(167, 'e0zkxmnt7vjeuhg3u9c1vf2b4wlyykid', 'v1', 'chats', 'chats/list', 'POST', '', '{\"user_id\":\"required - The user to load messages shared with\",\"limit\":\"The number of messages to load\"}', 'active', 0, '2020-10-14 08:54:23', '2020-10-14 08:54:23', '0', '0', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', NULL),
(168, 'xwmcpvd8ezmqehjnt3g5ykpbalrk29is', 'v1', 'chats', 'chats/delete', 'POST', '', '{\"msg_id\":\"required - The id of the message to delete\",\"action\":\"required - The item to delete either message/conversation.\"}', 'active', 0, '2020-09-11 21:31:47', '2021-01-18 22:24:50', '0', '0', NULL, 'uIkajsw123456789064hxk1fc3efmnva'),
(169, 'xwmcpvd8ezmqehjnt3g5ykpbalrk29is', 'v1', 'chats', 'chats/alerts', 'POST', '', '', 'active', 0, '2020-09-11 21:31:47', '2021-01-18 22:24:50', '0', '0', NULL, 'uIkajsw123456789064hxk1fc3efmnva'),
(170, 'rzwvr2eg3i98gapafuephlzbt01qkykm', 'v1', 'account', 'account/update_grading', 'POST', '', '{\"grading_values\":\"required - This is an array of the grading values\",\"report_columns\":\"This is an array of the report columns\"}', 'active', 0, '2021-02-22 21:36:36', '2021-02-22 23:48:22', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(171, 'jm5vlovnazpskrsqiry1k0gfbnejuda7', 'v1', 'terminal_reports', 'terminal_reports/download_csv', 'GET', '', '{\"class_id\":\"required - This is the class id to download\",\"course_id\":\"required - This is the course id to get the information\"}', 'active', 0, '2021-02-23 10:06:39', '2021-02-23 10:06:39', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', NULL),
(172, 'kveidclsmah8ilqruwbj2mtwuvj5ofxd', 'v1', 'terminal_reports', 'terminal_reports/upload_csv', 'POST', '', '{\"report_file\":\"This is the CSV file to upload\",\"class_id\":\"This is the class id for populating the data\",\"course_id\":\"This is the unique id of the course to load the results\"}', 'active', 0, '2021-02-23 12:31:40', '2021-02-23 12:32:19', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(173, 'lfdvoasl5a62cvcgi1nmm7krrt3wzphe', 'v1', 'terminal_reports', 'terminal_reports/save_report', 'POST', '', '{\"report_sheet\":\"required - An array of the data to process\"}', 'active', 0, '2021-02-23 22:40:16', '2021-02-23 22:40:16', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', NULL),
(174, 'exghes10atnr7qdmkyphiwibq4o92tuc', 'v1', 'terminal_reports', 'terminal_reports/check_existence', 'GET', '', '{\"course_id\":\"required - The course id\",\"class_id\":\"required - The unique class id\"}', 'active', 0, '2021-02-24 11:52:17', '2021-02-24 11:52:17', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', NULL),
(175, 'exghes10atnr7qdmkyphiwibq4o92tuc', 'v1', 'terminal_reports', 'terminal_reports/modify', 'POST', 'This endpoint is used to modify the status of a terminal report that has been uploaded', '{\"label\":\"required - This is an array of actions to perform\"}', 'active', 0, '2021-02-24 11:52:17', '2021-02-24 11:52:17', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', NULL),
(176, 'exghes10atnr7qdmkyphiwibq4o92tuc', 'v1', 'terminal_reports', 'terminal_reports/update_report', 'POST', 'This endpoint is used to modify the status of a terminal report that has been uploaded', '{\"label\":\"required - This is an array of actions to perform\"}', 'active', 0, '2021-02-24 11:52:17', '2021-02-24 11:52:17', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', NULL),
(177, 'hcbopnbvlef9rf8yw61rsmdj4gujpzhx', 'v1', 'terminal_reports', 'terminal_reports/generate', 'GET', '', '{\"academic_year\":\"The academic year to generate the report. Default will be the current academic year\",\"academic_term\":\"The academic term to generate the report. Default will be the current academic term.\",\"class_id\":\"required - This is the class to generate the terminal report cards\",\"student_id\":\"This is optional. When set then the report card only this student is generated.\"}', 'active', 0, '2021-04-17 20:41:55', '2021-04-17 22:33:22', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(178, 'cjs47zlobduqn1rbikyngap5uzt0m2mj', 'v1', 'pwa', 'pwa/idb', 'GET', '', '', 'active', 0, '2021-05-05 16:05:27', '2021-05-05 16:05:27', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', NULL),
(179, 'k6fr3exchm5rniq24fcysvyzidazolkb', 'v1', 'fees', 'fees/search', 'GET', '', '{\"term\":\"required - This is the search term to lookup for.\"}', 'active', 0, '2021-05-08 19:03:28', '2021-05-08 19:09:56', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM');

-- --------------------------------------------------------

--
-- Table structure for table `users_api_keys`
--

DROP TABLE IF EXISTS `users_api_keys`;
CREATE TABLE `users_api_keys` (
  `id` int(11) UNSIGNED NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `user_id` varchar(255) NOT NULL,
  `username` varchar(55) DEFAULT NULL,
  `access_token` varchar(1000) DEFAULT NULL,
  `access_key` varchar(255) DEFAULT NULL,
  `access_type` enum('temp','permanent') DEFAULT 'permanent',
  `expiry_date` date DEFAULT NULL,
  `expiry_timestamp` datetime NOT NULL DEFAULT current_timestamp(),
  `requests_limit` int(11) UNSIGNED DEFAULT 1000000,
  `total_requests` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `permissions` longtext DEFAULT NULL CHECK (json_valid(`permissions`)),
  `date_generated` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users_api_keys`
--

INSERT INTO `users_api_keys` (`id`, `client_id`, `user_id`, `username`, `access_token`, `access_key`, `access_type`, `expiry_date`, `expiry_timestamp`, `requests_limit`, `total_requests`, `permissions`, `date_generated`, `status`) VALUES
(6, NULL, 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'test_admin', '$2y$10$wTlBdjQuI6HAT1XqwyHPZOkWHL47L4IsqPHq7ey6wv0hYbdSOjrJC', 'p43FVPXvUi8DWNzklKBHjhQ1S4wktGcJ6maAYLG73MOCdsxzjeQdsMREtBfn20TI9Hli', 'temp', '2021-12-31', '2021-12-31 21:46:52', 5000, 0, NULL, '2020-09-30 21:46:52', '0'),
(7, NULL, 'uIkajsw123456789064hxk1fc3efmnva', 'test_admin', '$2y$10$dqBEsuNoYjhPdTscR6dq3u5V87.CHys0m.GA5U0kZqSzrYgK51qs6', '4jIRASkrjEOGXNCXWlBRlvyggCn34uQWmpqYtVLzHm5BPFDiUehzPH6Tdrf0yIcab9ap0t', 'temp', '2021-12-31', '2021-12-31 21:46:52', 5000, 0, NULL, '2020-11-13 18:36:36', '1');

-- --------------------------------------------------------

--
-- Table structure for table `users_api_queries`
--

DROP TABLE IF EXISTS `users_api_queries`;
CREATE TABLE `users_api_queries` (
  `id` int(11) UNSIGNED NOT NULL,
  `requests_count` int(11) UNSIGNED DEFAULT NULL,
  `request_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users_api_requests`
--

DROP TABLE IF EXISTS `users_api_requests`;
CREATE TABLE `users_api_requests` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` varchar(64) DEFAULT NULL,
  `request_uri` varchar(1000) DEFAULT NULL,
  `request_payload` text DEFAULT NULL,
  `request_method` varchar(10) DEFAULT NULL,
  `response_code` int(11) UNSIGNED DEFAULT NULL,
  `user_ipaddress` varchar(255) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `date_created` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users_attendance_log`
--

DROP TABLE IF EXISTS `users_attendance_log`;
CREATE TABLE `users_attendance_log` (
  `id` int(11) NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `user_type` varchar(32) DEFAULT NULL,
  `class_id` varchar(32) DEFAULT NULL,
  `users_list` text DEFAULT NULL,
  `users_data` text DEFAULT NULL,
  `log_date` date DEFAULT NULL,
  `created_by` varchar(32) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `finalize` enum('0','1') NOT NULL DEFAULT '0',
  `date_finalized` datetime DEFAULT NULL,
  `finalized_by` varchar(32) DEFAULT NULL,
  `academic_year` varchar(32) DEFAULT NULL,
  `academic_term` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users_attendance_log`
--

INSERT INTO `users_attendance_log` (`id`, `client_id`, `user_type`, `class_id`, `users_list`, `users_data`, `log_date`, `created_by`, `date_created`, `status`, `finalize`, `date_finalized`, `finalized_by`, `academic_year`, `academic_term`) VALUES
(1, 'TLIS0000001', 'student', '1', '[\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\"]', '[{\"item_id\":\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"unique_id\":\"LJKDFLAA3\",\"name\":\"Ebenezer Franklin Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"unique_id\":\"IURIEKJFD\",\"name\":\"julian asamoah dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"present\"},{\"item_id\":\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\",\"unique_id\":\"ALJKDFLAA3\",\"name\":\"George Anderson Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"absent\"},{\"item_id\":\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\",\"unique_id\":\"AIURIEKJFD\",\"name\":\"Philip Anthony dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"present\"}]', '2021-04-01', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-04-20 12:59:39', '1', '1', '2021-04-20 13:01:08', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st'),
(2, 'TLIS0000001', 'student', '1', '[\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\",\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\"]', '[{\"item_id\":\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"unique_id\":\"LJKDFLAA3\",\"name\":\"Ebenezer Franklin Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"unique_id\":\"IURIEKJFD\",\"name\":\"julian asamoah dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"present\"},{\"item_id\":\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\",\"unique_id\":\"ALJKDFLAA3\",\"name\":\"George Anderson Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\",\"unique_id\":\"AIURIEKJFD\",\"name\":\"Philip Anthony dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"present\"}]', '2021-04-02', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-04-20 13:01:17', '1', '1', '2021-04-20 13:01:21', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st'),
(3, 'TLIS0000001', 'student', '1', '[\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\",\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\"]', '[{\"item_id\":\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"unique_id\":\"LJKDFLAA3\",\"name\":\"Ebenezer Franklin Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"unique_id\":\"IURIEKJFD\",\"name\":\"julian asamoah dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"absent\"},{\"item_id\":\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\",\"unique_id\":\"ALJKDFLAA3\",\"name\":\"George Anderson Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\",\"unique_id\":\"AIURIEKJFD\",\"name\":\"Philip Anthony dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"present\"}]', '2021-04-05', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-04-20 13:23:20', '1', '1', '2021-04-20 13:23:24', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st'),
(4, 'TLIS0000001', 'student', '1', '[\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\",\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\"]', '[{\"item_id\":\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"unique_id\":\"LJKDFLAA3\",\"name\":\"Ebenezer Franklin Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"unique_id\":\"IURIEKJFD\",\"name\":\"julian asamoah dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"present\"},{\"item_id\":\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\",\"unique_id\":\"ALJKDFLAA3\",\"name\":\"George Anderson Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\",\"unique_id\":\"AIURIEKJFD\",\"name\":\"Philip Anthony dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"present\"}]', '2021-04-06', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-04-20 13:23:31', '1', '1', '2021-04-20 13:23:35', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st'),
(5, 'TLIS0000001', 'student', '1', '[\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\",\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\"]', '[{\"item_id\":\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"unique_id\":\"LJKDFLAA3\",\"name\":\"Ebenezer Franklin Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"unique_id\":\"IURIEKJFD\",\"name\":\"julian asamoah dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"present\"},{\"item_id\":\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\",\"unique_id\":\"ALJKDFLAA3\",\"name\":\"George Anderson Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\",\"unique_id\":\"AIURIEKJFD\",\"name\":\"Philip Anthony dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"present\"}]', '2021-04-07', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-04-20 13:23:42', '1', '1', '2021-04-20 13:23:45', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st'),
(6, 'TLIS0000001', 'student', '1', '[\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\"]', '[{\"item_id\":\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"unique_id\":\"LJKDFLAA3\",\"name\":\"Ebenezer Franklin Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"unique_id\":\"IURIEKJFD\",\"name\":\"julian asamoah dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"present\"},{\"item_id\":\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\",\"unique_id\":\"ALJKDFLAA3\",\"name\":\"George Anderson Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\",\"unique_id\":\"AIURIEKJFD\",\"name\":\"Philip Anthony dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"absent\"}]', '2021-04-08', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-04-20 13:23:52', '1', '1', '2021-04-20 13:23:56', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st'),
(7, 'TLIS0000001', 'teacher', NULL, '[\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\",\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\"]', '[{\"item_id\":\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\",\"unique_id\":\"P00001\",\"name\":\"fredrick amponsah badu\",\"email\":\"fredamponsah@gmail.com\",\"phone_number\":\"490993093\",\"state\":\"present\"},{\"item_id\":\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\",\"unique_id\":\"P00002\",\"name\":\"henry asmah \",\"email\":\"asmahhenry@gmail.com\",\"phone_number\":\"9093009192\",\"state\":\"present\"}]', '2021-04-01', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-04-20 13:24:27', '1', '1', '2021-04-20 13:24:30', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st'),
(8, 'TLIS0000001', 'teacher', NULL, '[\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\",\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\"]', '[{\"item_id\":\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\",\"unique_id\":\"P00001\",\"name\":\"fredrick amponsah badu\",\"email\":\"fredamponsah@gmail.com\",\"phone_number\":\"490993093\",\"state\":\"present\"},{\"item_id\":\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\",\"unique_id\":\"P00002\",\"name\":\"henry asmah \",\"email\":\"asmahhenry@gmail.com\",\"phone_number\":\"9093009192\",\"state\":\"present\"}]', '2021-04-05', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-04-20 13:24:40', '1', '1', '2021-04-20 13:24:44', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st'),
(9, 'TLIS0000001', 'teacher', NULL, '[\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\",\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\"]', '[{\"item_id\":\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\",\"unique_id\":\"P00001\",\"name\":\"fredrick amponsah badu\",\"email\":\"fredamponsah@gmail.com\",\"phone_number\":\"490993093\",\"state\":\"present\"},{\"item_id\":\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\",\"unique_id\":\"P00002\",\"name\":\"henry asmah \",\"email\":\"asmahhenry@gmail.com\",\"phone_number\":\"9093009192\",\"state\":\"present\"}]', '2021-04-06', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-04-20 13:24:50', '1', '1', '2021-04-20 13:24:53', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st'),
(10, 'TLIS0000001', 'teacher', NULL, '[\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\"]', '[{\"item_id\":\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\",\"unique_id\":\"P00001\",\"name\":\"fredrick amponsah badu\",\"email\":\"fredamponsah@gmail.com\",\"phone_number\":\"490993093\",\"state\":\"present\"},{\"item_id\":\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\",\"unique_id\":\"P00002\",\"name\":\"henry asmah \",\"email\":\"asmahhenry@gmail.com\",\"phone_number\":\"9093009192\",\"state\":\"absent\"}]', '2021-04-07', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-04-20 13:24:59', '1', '1', '2021-04-20 13:25:02', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st'),
(11, 'TLIS0000001', 'teacher', NULL, '[\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\",\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\"]', '[{\"item_id\":\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\",\"unique_id\":\"P00001\",\"name\":\"fredrick amponsah badu\",\"email\":\"fredamponsah@gmail.com\",\"phone_number\":\"490993093\",\"state\":\"present\"},{\"item_id\":\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\",\"unique_id\":\"P00002\",\"name\":\"henry asmah \",\"email\":\"asmahhenry@gmail.com\",\"phone_number\":\"9093009192\",\"state\":\"present\"}]', '2021-04-08', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-04-20 13:25:09', '1', '1', '2021-04-20 13:25:13', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st'),
(12, 'TLIS0000001', 'student', '1', '[\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\",\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\"]', '[{\"item_id\":\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"unique_id\":\"LJKDFLAA3\",\"name\":\"Ebenezer Franklin Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"unique_id\":\"IURIEKJFD\",\"name\":\"julian asamoah dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"present\"},{\"item_id\":\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\",\"unique_id\":\"ALJKDFLAA3\",\"name\":\"George Anderson Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\",\"unique_id\":\"AIURIEKJFD\",\"name\":\"Philip Anthony dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"present\"}]', '2021-04-09', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-04-20 13:25:27', '1', '1', '2021-04-20 13:25:30', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st'),
(13, 'TLIS0000001', 'student', '1', '[\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\"]', '[{\"item_id\":\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"unique_id\":\"LJKDFLAA3\",\"name\":\"Ebenezer Franklin Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"unique_id\":\"IURIEKJFD\",\"name\":\"julian asamoah dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"absent\"},{\"item_id\":\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\",\"unique_id\":\"ALJKDFLAA3\",\"name\":\"George Anderson Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\",\"unique_id\":\"AIURIEKJFD\",\"name\":\"Philip Anthony dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"absent\"}]', '2021-04-12', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-04-20 13:25:40', '1', '1', '2021-04-20 13:25:42', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st'),
(14, 'TLIS0000001', 'student', '1', '[\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\"]', '[{\"item_id\":\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"unique_id\":\"LJKDFLAA3\",\"name\":\"Ebenezer Franklin Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"unique_id\":\"IURIEKJFD\",\"name\":\"julian asamoah dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"absent\"},{\"item_id\":\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\",\"unique_id\":\"ALJKDFLAA3\",\"name\":\"George Anderson Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\",\"unique_id\":\"AIURIEKJFD\",\"name\":\"Philip Anthony dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"absent\"}]', '2021-04-13', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-04-20 13:25:51', '1', '1', '2021-04-20 13:25:53', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st'),
(15, 'TLIS0000001', 'student', '1', '[\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\",\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\"]', '[{\"item_id\":\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"unique_id\":\"LJKDFLAA3\",\"name\":\"Ebenezer Franklin Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"unique_id\":\"IURIEKJFD\",\"name\":\"julian asamoah dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"present\"},{\"item_id\":\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\",\"unique_id\":\"ALJKDFLAA3\",\"name\":\"George Anderson Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\",\"unique_id\":\"AIURIEKJFD\",\"name\":\"Philip Anthony dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"present\"}]', '2021-04-14', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-04-20 13:26:00', '1', '1', '2021-04-20 13:26:03', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st'),
(16, 'TLIS0000001', 'student', '1', '[\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\"]', '[{\"item_id\":\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"unique_id\":\"LJKDFLAA3\",\"name\":\"Ebenezer Franklin Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"unique_id\":\"IURIEKJFD\",\"name\":\"julian asamoah dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"present\"},{\"item_id\":\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\",\"unique_id\":\"ALJKDFLAA3\",\"name\":\"George Anderson Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\",\"unique_id\":\"AIURIEKJFD\",\"name\":\"Philip Anthony dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"absent\"}]', '2021-04-20', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-04-20 13:26:58', '1', '1', '2021-04-20 13:27:00', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st'),
(17, 'TLIS0000001', 'student', '1', '[\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\",\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\"]', '[{\"item_id\":\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"unique_id\":\"LJKDFLAA3\",\"name\":\"Ebenezer Franklin Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"unique_id\":\"IURIEKJFD\",\"name\":\"julian asamoah dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"present\"},{\"item_id\":\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\",\"unique_id\":\"ALJKDFLAA3\",\"name\":\"George Anderson Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\",\"unique_id\":\"AIURIEKJFD\",\"name\":\"Philip Anthony dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"present\"}]', '2021-04-19', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-04-20 13:27:09', '1', '1', '2021-04-20 13:27:11', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st'),
(18, 'TLIS0000001', 'teacher', NULL, '[\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\",\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\"]', '[{\"item_id\":\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\",\"unique_id\":\"P00001\",\"name\":\"fredrick amponsah badu\",\"email\":\"fredamponsah@gmail.com\",\"phone_number\":\"490993093\",\"state\":\"present\"},{\"item_id\":\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\",\"unique_id\":\"P00002\",\"name\":\"henry asmah \",\"email\":\"asmahhenry@gmail.com\",\"phone_number\":\"9093009192\",\"state\":\"present\"}]', '2021-04-19', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-04-20 13:27:17', '1', '1', '2021-04-20 13:27:20', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st'),
(19, 'TLIS0000001', 'teacher', NULL, '[\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\",\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\"]', '[{\"item_id\":\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\",\"unique_id\":\"P00001\",\"name\":\"fredrick amponsah badu\",\"email\":\"fredamponsah@gmail.com\",\"phone_number\":\"490993093\",\"state\":\"present\"},{\"item_id\":\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\",\"unique_id\":\"P00002\",\"name\":\"henry asmah \",\"email\":\"asmahhenry@gmail.com\",\"phone_number\":\"9093009192\",\"state\":\"present\"}]', '2021-04-20', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-04-20 13:27:26', '1', '1', '2021-04-20 13:27:28', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st'),
(20, 'TLIS0000001', 'teacher', NULL, '[\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\"]', '[{\"item_id\":\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\",\"unique_id\":\"P00001\",\"name\":\"fredrick amponsah badu\",\"email\":\"fredamponsah@gmail.com\",\"phone_number\":\"490993093\",\"state\":\"present\"},{\"item_id\":\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\",\"unique_id\":\"P00002\",\"name\":\"henry asmah \",\"email\":\"asmahhenry@gmail.com\",\"phone_number\":\"9093009192\",\"state\":\"absent\"}]', '2021-04-16', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-04-20 13:27:56', '1', '1', '2021-04-20 13:27:58', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st'),
(21, 'TLIS0000001', 'teacher', NULL, '[\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\",\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\"]', '[{\"item_id\":\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\",\"unique_id\":\"P00001\",\"name\":\"fredrick amponsah badu\",\"email\":\"fredamponsah@gmail.com\",\"phone_number\":\"490993093\",\"state\":\"present\"},{\"item_id\":\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\",\"unique_id\":\"P00002\",\"name\":\"henry asmah \",\"email\":\"asmahhenry@gmail.com\",\"phone_number\":\"9093009192\",\"state\":\"present\"}]', '2021-04-15', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-04-20 13:28:04', '1', '1', '2021-04-20 13:28:07', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st'),
(22, 'TLIS0000001', 'teacher', NULL, '[\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\"]', '[{\"item_id\":\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\",\"unique_id\":\"P00001\",\"name\":\"fredrick amponsah badu\",\"email\":\"fredamponsah@gmail.com\",\"phone_number\":\"490993093\",\"state\":\"absent\"},{\"item_id\":\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\",\"unique_id\":\"P00002\",\"name\":\"henry asmah \",\"email\":\"asmahhenry@gmail.com\",\"phone_number\":\"9093009192\",\"state\":\"present\"}]', '2021-04-14', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-04-20 13:28:15', '1', '1', '2021-04-20 13:28:17', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st'),
(23, 'TLIS0000001', 'student', '1', '[\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\"]', '[{\"item_id\":\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"unique_id\":\"LJKDFLAA3\",\"name\":\"Ebenezer Franklin Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"unique_id\":\"IURIEKJFD\",\"name\":\"julian asamoah dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"present\"},{\"item_id\":\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\",\"unique_id\":\"ALJKDFLAA3\",\"name\":\"George Anderson Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\",\"unique_id\":\"AIURIEKJFD\",\"name\":\"Philip Anthony dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"absent\"}]', '2021-04-16', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-04-20 13:28:33', '1', '1', '2021-04-20 13:28:36', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st'),
(24, 'TLIS0000001', 'student', '1', '[\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\"]', '[{\"item_id\":\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"unique_id\":\"LJKDFLAA3\",\"name\":\"Ebenezer Franklin Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"unique_id\":\"IURIEKJFD\",\"name\":\"julian asamoah dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"present\"},{\"item_id\":\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\",\"unique_id\":\"ALJKDFLAA3\",\"name\":\"George Anderson Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"absent\"},{\"item_id\":\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\",\"unique_id\":\"AIURIEKJFD\",\"name\":\"Philip Anthony dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"present\"}]', '2021-04-15', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-04-20 13:28:44', '1', '1', '2021-04-20 13:28:46', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st'),
(25, 'TLIS0000001', 'teacher', NULL, '[\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\",\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\"]', '[{\"item_id\":\"tnThYo5wKHG2XxgPdSVkErb7zLlqum1A\",\"unique_id\":\"P00001\",\"name\":\"fredrick amponsah badu\",\"email\":\"fredamponsah@gmail.com\",\"phone_number\":\"490993093\",\"state\":\"present\"},{\"item_id\":\"NgBS03aI1zLOq5osPf4VlCnYktbETMpQ\",\"unique_id\":\"P00002\",\"name\":\"henry asmah \",\"email\":\"asmahhenry@gmail.com\",\"phone_number\":\"9093009192\",\"state\":\"present\"}]', '2021-04-12', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-04-20 13:29:28', '1', '1', '2021-04-20 13:29:31', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st'),
(26, 'TLIS0000001', 'student', '1', '[\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\",\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\"]', '[{\"item_id\":\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"unique_id\":\"LJKDFLAA3\",\"name\":\"Ebenezer Franklin Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"unique_id\":\"IURIEKJFD\",\"name\":\"julian asamoah dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"present\"},{\"item_id\":\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\",\"unique_id\":\"ALJKDFLAA3\",\"name\":\"George Anderson Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\",\"unique_id\":\"AIURIEKJFD\",\"name\":\"Philip Anthony dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"present\"}]', '2021-04-21', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-01 07:17:55', '1', '1', '2021-05-01 07:18:08', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st'),
(27, 'TLIS0000001', 'student', '1', '[\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\"]', '[{\"item_id\":\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"unique_id\":\"LJKDFLAA3\",\"name\":\"Ebenezer Franklin Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"unique_id\":\"IURIEKJFD\",\"name\":\"julian asamoah dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"absent\"},{\"item_id\":\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\",\"unique_id\":\"ALJKDFLAA3\",\"name\":\"George Anderson Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\",\"unique_id\":\"AIURIEKJFD\",\"name\":\"Philip Anthony dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"absent\"}]', '2021-04-22', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-01 07:18:19', '1', '1', '2021-05-01 07:18:21', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st'),
(28, 'TLIS0000001', 'student', '1', '[\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\"]', '[{\"item_id\":\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"unique_id\":\"LJKDFLAA3\",\"name\":\"Ebenezer Franklin Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"unique_id\":\"IURIEKJFD\",\"name\":\"julian asamoah dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"present\"},{\"item_id\":\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\",\"unique_id\":\"ALJKDFLAA3\",\"name\":\"George Anderson Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"absent\"},{\"item_id\":\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\",\"unique_id\":\"AIURIEKJFD\",\"name\":\"Philip Anthony dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"present\"}]', '2021-04-23', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-01 07:18:31', '1', '1', '2021-05-01 07:18:34', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st'),
(29, 'TLIS0000001', 'student', '1', '[\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\"]', '[{\"item_id\":\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"unique_id\":\"LJKDFLAA3\",\"name\":\"Ebenezer Franklin Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"unique_id\":\"IURIEKJFD\",\"name\":\"julian asamoah dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"present\"},{\"item_id\":\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\",\"unique_id\":\"ALJKDFLAA3\",\"name\":\"George Anderson Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"absent\"},{\"item_id\":\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\",\"unique_id\":\"AIURIEKJFD\",\"name\":\"Philip Anthony dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"present\"}]', '2021-04-26', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-01 07:18:50', '1', '1', '2021-05-01 07:18:53', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st'),
(30, 'TLIS0000001', 'student', '1', '[\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\"]', '[{\"item_id\":\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"unique_id\":\"LJKDFLAA3\",\"name\":\"Ebenezer Franklin Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"unique_id\":\"IURIEKJFD\",\"name\":\"julian asamoah dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"absent\"},{\"item_id\":\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\",\"unique_id\":\"ALJKDFLAA3\",\"name\":\"George Anderson Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"absent\"},{\"item_id\":\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\",\"unique_id\":\"AIURIEKJFD\",\"name\":\"Philip Anthony dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"present\"}]', '2021-04-27', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-01 07:19:10', '1', '1', '2021-05-01 07:19:13', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st'),
(31, 'TLIS0000001', 'student', '1', '[\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\",\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\"]', '[{\"item_id\":\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"unique_id\":\"LJKDFLAA3\",\"name\":\"Ebenezer Franklin Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"unique_id\":\"IURIEKJFD\",\"name\":\"julian asamoah dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"absent\"},{\"item_id\":\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\",\"unique_id\":\"ALJKDFLAA3\",\"name\":\"George Anderson Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\",\"unique_id\":\"AIURIEKJFD\",\"name\":\"Philip Anthony dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"present\"}]', '2021-04-28', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-01 07:19:26', '1', '1', '2021-05-01 07:19:28', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st'),
(32, 'TLIS0000001', 'student', '1', '[\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\"]', '[{\"item_id\":\"1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB\",\"unique_id\":\"LJKDFLAA3\",\"name\":\"Ebenezer Franklin Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"GSzAlD7uPObpda2jKtE0qhWUV8igTo9f\",\"unique_id\":\"IURIEKJFD\",\"name\":\"julian asamoah dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"present\"},{\"item_id\":\"T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK\",\"unique_id\":\"ALJKDFLAA3\",\"name\":\"George Anderson Hyde\",\"email\":\"emmallob@mail.com\",\"phone_number\":\"983983983\",\"state\":\"present\"},{\"item_id\":\"qkrNWbzA3EoZLaSleY5T4291ICQsmvdD\",\"unique_id\":\"AIURIEKJFD\",\"name\":\"Philip Anthony dadzie\",\"email\":\"julian@mail.com\",\"phone_number\":\"9090993093\",\"state\":\"absent\"}]', '2021-04-29', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-05-01 07:19:37', '1', '1', '2021-05-01 07:19:40', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2020/2021', '1st');

-- --------------------------------------------------------

--
-- Table structure for table `users_chat`
--

DROP TABLE IF EXISTS `users_chat`;
CREATE TABLE `users_chat` (
  `id` int(11) UNSIGNED NOT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `message_unique_id` varchar(32) DEFAULT NULL,
  `sender_id` varchar(32) DEFAULT NULL,
  `receiver_id` varchar(32) DEFAULT NULL,
  `message` varchar(1000) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `seen_status` enum('0','1') NOT NULL DEFAULT '0',
  `seen_date` datetime DEFAULT NULL,
  `sender_deleted` enum('0','1') NOT NULL DEFAULT '0',
  `receiver_deleted` enum('0','1') NOT NULL DEFAULT '0',
  `notice_type` varchar(12) NOT NULL DEFAULT '5',
  `user_agent` varchar(500) DEFAULT NULL,
  `user_signature` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users_chat`
--

INSERT INTO `users_chat` (`id`, `item_id`, `message_unique_id`, `sender_id`, `receiver_id`, `message`, `date_created`, `seen_status`, `seen_date`, `sender_deleted`, `receiver_deleted`, `notice_type`, `user_agent`, `user_signature`) VALUES
(1, NULL, 'XALRB0TPMRK34TE9', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'tnThYo5wKHG2XxgPdSVkErb7zLlqum1A', 'Hello, how are you doing?', '2021-02-20 17:19:37', '1', '2021-02-20 22:31:17', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(2, NULL, 'XALRB0TPMRK34TE9', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'tnThYo5wKHG2XxgPdSVkErb7zLlqum1A', 'Whats the plans for the day? I believe we will be meeting today for the discussions', '2021-02-20 17:20:49', '1', '2021-02-20 22:31:17', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(3, NULL, '2FWJDFM6IRCUVPW4', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'Hello julian how are you doing today?', '2021-02-20 17:24:05', '1', '2021-02-20 22:31:22', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(4, NULL, '2FWJDFM6IRCUVPW4', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'i believe all is going on as planned', '2021-02-20 17:24:11', '1', '2021-02-20 22:31:22', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(5, NULL, 'Z0XQVLMCYJ54SHHB', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'hello henry', '2021-02-20 17:36:07', '1', '2021-02-20 21:10:07', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(6, NULL, 'Z0XQVLMCYJ54SHHB', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'whats the plans and schedules for today?', '2021-02-20 17:36:15', '1', '2021-02-20 21:10:07', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(7, NULL, 'Z0XQVLMCYJ54SHHB', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Alright, cool.. i had a great day', '2021-02-20 21:11:24', '1', '2021-02-20 21:11:27', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36 Edg/88.0.705.68', NULL),
(8, NULL, 'Z0XQVLMCYJ54SHHB', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'thats nice and good to know', '2021-02-20 21:11:34', '1', '2021-02-20 21:38:07', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(9, NULL, 'Z0XQVLMCYJ54SHHB', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'alright', '2021-02-20 22:00:13', '1', '2021-02-20 22:24:04', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36 Edg/88.0.705.68', NULL),
(10, NULL, 'Z0XQVLMCYJ54SHHB', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'sup for today?', '2021-02-20 22:25:52', '1', '2021-02-20 22:26:25', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(11, NULL, 'Z0XQVLMCYJ54SHHB', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'all is well here', '2021-02-20 22:26:31', '1', '2021-02-20 22:26:47', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36 Edg/88.0.705.68', NULL),
(12, NULL, 'Z0XQVLMCYJ54SHHB', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'whats the big deal at your end as well?', '2021-02-20 22:26:41', '1', '2021-02-20 22:26:47', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36 Edg/88.0.705.68', NULL),
(13, NULL, 'Z0XQVLMCYJ54SHHB', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'nothing much ooo', '2021-02-20 22:26:52', '1', '2021-02-20 22:27:03', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(14, NULL, 'Z0XQVLMCYJ54SHHB', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'do you have anything for me?', '2021-02-20 22:26:59', '1', '2021-02-20 22:27:03', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(15, NULL, 'Z0XQVLMCYJ54SHHB', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'sure there is a problem', '2021-02-20 22:27:10', '1', '2021-02-20 22:27:17', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36 Edg/88.0.705.68', NULL),
(16, NULL, 'Z0XQVLMCYJ54SHHB', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'that i want us to resolve', '2021-02-20 22:27:15', '1', '2021-02-20 22:27:17', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36 Edg/88.0.705.68', NULL),
(17, NULL, 'Z0XQVLMCYJ54SHHB', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'oh saaa', '2021-02-20 22:27:20', '1', '2021-02-20 22:27:27', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(18, NULL, 'Z0XQVLMCYJ54SHHB', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'what is it', '2021-02-20 22:27:23', '1', '2021-02-20 22:27:27', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(19, NULL, 'Z0XQVLMCYJ54SHHB', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'do i even know', '2021-02-20 22:28:40', '1', '2021-02-20 22:28:43', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36 Edg/88.0.705.68', NULL),
(20, NULL, 'Z0XQVLMCYJ54SHHB', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'why not', '2021-02-20 22:28:45', '1', '2021-02-20 22:28:52', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(21, NULL, 'Z0XQVLMCYJ54SHHB', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'you should', '2021-02-20 22:28:47', '1', '2021-02-20 22:28:52', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(22, NULL, 'Z0XQVLMCYJ54SHHB', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'will try and see if i know what the problem is', '2021-02-20 22:29:01', '1', '2021-02-20 22:29:06', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36 Edg/88.0.705.68', NULL),
(23, NULL, '2FWJDFM6IRCUVPW4', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'hello julian', '2021-02-20 22:31:12', '1', '2021-02-20 22:31:22', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(24, NULL, '2FWJDFM6IRCUVPW4', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'yes boss', '2021-02-20 22:31:26', '1', '2021-02-20 22:31:31', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36 Edg/88.0.705.68', NULL),
(25, NULL, '2FWJDFM6IRCUVPW4', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'how are you doing today?', '2021-02-20 22:31:37', '1', '2021-02-20 22:31:56', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(26, NULL, '2FWJDFM6IRCUVPW4', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'i am doing great', '2021-02-20 22:32:00', '1', '2021-02-20 22:33:28', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36 Edg/88.0.705.68', NULL),
(27, NULL, '2FWJDFM6IRCUVPW4', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'and you?', '2021-02-20 22:32:02', '1', '2021-02-20 22:33:28', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36 Edg/88.0.705.68', NULL),
(28, NULL, 'XALRB0TPMRK34TE9', 'tnThYo5wKHG2XxgPdSVkErb7zLlqum1A', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'yeah yeah', '2021-02-20 22:32:08', '1', '2021-02-20 22:34:00', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(29, NULL, 'XALRB0TPMRK34TE9', 'tnThYo5wKHG2XxgPdSVkErb7zLlqum1A', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'sup for today', '2021-02-20 22:32:11', '1', '2021-02-20 22:34:00', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(30, NULL, '2FWJDFM6IRCUVPW4', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'all is well here', '2021-02-20 22:33:33', '1', '2021-02-20 22:34:57', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(31, NULL, 'L1JXFOE3UKCH47KB', 'tnThYo5wKHG2XxgPdSVkErb7zLlqum1A', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'hello julian', '2021-02-20 22:33:46', '1', '2021-05-01 14:21:40', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(32, NULL, 'L1JXFOE3UKCH47KB', 'tnThYo5wKHG2XxgPdSVkErb7zLlqum1A', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'how are you doing?', '2021-02-20 22:33:52', '1', '2021-05-01 14:21:40', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(33, NULL, 'XALRB0TPMRK34TE9', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'tnThYo5wKHG2XxgPdSVkErb7zLlqum1A', 'all is good', '2021-02-20 22:34:05', '1', '2021-02-20 22:34:10', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(34, NULL, 'XALRB0TPMRK34TE9', 'tnThYo5wKHG2XxgPdSVkErb7zLlqum1A', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'alright thats fine', '2021-02-20 22:34:15', '1', '2021-02-20 22:34:19', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(35, NULL, 'XALRB0TPMRK34TE9', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'tnThYo5wKHG2XxgPdSVkErb7zLlqum1A', 'kkk', '2021-02-20 22:34:23', '1', '2021-02-20 22:34:33', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(36, NULL, 'XALRB0TPMRK34TE9', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'tnThYo5wKHG2XxgPdSVkErb7zLlqum1A', 'great news', '2021-02-20 22:34:27', '1', '2021-02-20 22:34:33', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(37, NULL, 'XALRB0TPMRK34TE9', 'tnThYo5wKHG2XxgPdSVkErb7zLlqum1A', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'i wish to know', '2021-02-20 22:34:41', '1', '2021-02-20 22:35:11', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(38, NULL, '2FWJDFM6IRCUVPW4', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'kkk', '2021-02-20 22:35:00', '1', '2021-02-20 22:35:06', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36 Edg/88.0.705.68', NULL),
(39, NULL, '2FWJDFM6IRCUVPW4', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'bye for now', '2021-02-20 22:35:02', '1', '2021-02-20 22:35:06', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36 Edg/88.0.705.68', NULL),
(40, NULL, '2FWJDFM6IRCUVPW4', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'alright bye', '2021-02-20 22:35:10', '1', '2021-02-20 22:35:18', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(41, NULL, '5WC64QGHIX0GOWCO', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'tnThYo5wKHG2XxgPdSVkErb7zLlqum1A', 'hello fred', '2021-02-20 22:41:03', '0', '2021-02-20 22:41:03', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36 Edg/88.0.705.68', NULL),
(42, NULL, 'Z0XQVLMCYJ54SHHB', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'hey boss', '2021-02-20 22:41:29', '1', '2021-02-20 22:41:43', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(43, NULL, 'Z0XQVLMCYJ54SHHB', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'whats up now', '2021-02-20 22:41:37', '1', '2021-02-20 22:41:43', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(44, NULL, 'Z0XQVLMCYJ54SHHB', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'do i even know', '2021-02-20 22:41:47', '1', '2021-02-20 22:45:19', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36 Edg/88.0.705.68', NULL),
(45, NULL, 'Z0XQVLMCYJ54SHHB', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'whats the big deal at your end?', '2021-02-20 22:42:05', '1', '2021-02-20 22:45:19', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36 Edg/88.0.705.68', NULL),
(46, NULL, 'Z0XQVLMCYJ54SHHB', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'nothing for now', '2021-02-20 22:45:29', '1', '2021-02-20 22:45:41', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(47, NULL, 'Z0XQVLMCYJ54SHHB', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'just working on the chat system', '2021-02-20 22:45:34', '1', '2021-02-20 22:45:41', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(48, NULL, 'Z0XQVLMCYJ54SHHB', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'alright cool', '2021-02-20 22:45:44', '1', '2021-02-20 22:49:48', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36 Edg/88.0.705.68', NULL),
(49, NULL, 'Z0XQVLMCYJ54SHHB', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'hope you good too', '2021-02-20 22:47:25', '1', '2021-02-20 22:49:48', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36 Edg/88.0.705.68', NULL),
(50, NULL, 'Z0XQVLMCYJ54SHHB', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'hope you good too', '2021-02-20 22:47:59', '1', '2021-02-20 22:49:48', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36 Edg/88.0.705.68', NULL),
(51, NULL, 'Z0XQVLMCYJ54SHHB', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'hey boss', '2021-02-20 22:50:05', '1', '2021-02-20 22:53:16', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36 Edg/88.0.705.68', NULL),
(52, NULL, 'Z0XQVLMCYJ54SHHB', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'please i hope you are doing grea', '2021-02-20 22:50:14', '1', '2021-02-20 22:53:16', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36 Edg/88.0.705.68', NULL),
(53, NULL, 'Z0XQVLMCYJ54SHHB', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'whats this thing again', '2021-02-20 22:50:25', '1', '2021-02-20 22:53:16', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36 Edg/88.0.705.68', NULL),
(54, NULL, 'Z0XQVLMCYJ54SHHB', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'hey', '2021-02-20 22:59:56', '1', '2021-02-20 23:00:04', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(55, NULL, 'Z0XQVLMCYJ54SHHB', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'why the loads of messages?', '2021-02-20 23:00:02', '1', '2021-02-20 23:00:04', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(56, NULL, 'Z0XQVLMCYJ54SHHB', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'do i even know', '2021-02-20 23:00:10', '1', '2021-02-20 23:00:15', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36 Edg/88.0.705.68', NULL),
(57, NULL, 'Z0XQVLMCYJ54SHHB', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'did you say you dont know?', '2021-02-20 23:00:22', '1', '2021-02-20 23:00:34', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36', NULL),
(58, NULL, 'Z0XQVLMCYJ54SHHB', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'yeah i dont', '2021-02-20 23:00:39', '1', '2021-05-01 15:08:33', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36 Edg/88.0.705.68', NULL),
(59, NULL, 'Z0XQVLMCYJ54SHHB', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'tell me more about it that i do not know', '2021-02-20 23:00:50', '1', '2021-05-01 15:08:33', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36 Edg/88.0.705.68', NULL),
(60, NULL, '2FWJDFM6IRCUVPW4', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'hello', '2021-05-01 15:08:45', '1', '2021-05-01 15:08:49', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36 Edg/90.0.818.49', NULL),
(61, NULL, '2FWJDFM6IRCUVPW4', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'how are you doing?', '2021-05-01 15:08:53', '1', '2021-05-01 15:08:58', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36', NULL),
(62, NULL, '2FWJDFM6IRCUVPW4', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'i am doing great and you?', '2021-05-01 15:09:05', '1', '2021-05-01 15:11:21', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36 Edg/90.0.818.49', NULL),
(63, NULL, '2FWJDFM6IRCUVPW4', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'hiii', '2021-05-01 15:11:38', '1', '2021-05-01 16:37:05', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36 Edg/90.0.818.49', NULL),
(64, NULL, '2FWJDFM6IRCUVPW4', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'hello world', '2021-05-01 16:24:55', '1', '2021-05-01 16:37:05', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36 Edg/90.0.818.49', NULL),
(65, NULL, '2FWJDFM6IRCUVPW4', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'why arent you replying to my messages?', '2021-05-01 16:32:54', '1', '2021-05-01 16:37:05', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36 Edg/90.0.818.49', NULL),
(66, NULL, '2FWJDFM6IRCUVPW4', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'am sorry was busy', '2021-05-01 16:37:50', '1', '2021-05-01 16:37:56', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36', NULL),
(67, NULL, '2FWJDFM6IRCUVPW4', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'whats the big deal please?', '2021-05-01 16:38:03', '1', '2021-05-01 16:38:05', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36', NULL),
(68, NULL, '2FWJDFM6IRCUVPW4', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'alright no problem.', '2021-05-01 16:38:10', '0', NULL, '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36 Edg/90.0.818.49', NULL),
(69, NULL, '2FWJDFM6IRCUVPW4', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'i had wanted you to come over tonight so we discuss the business deal', '2021-05-01 16:38:24', '0', NULL, '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36 Edg/90.0.818.49', NULL),
(70, NULL, '2FWJDFM6IRCUVPW4', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'your mind dey?', '2021-05-01 16:38:27', '0', NULL, '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36 Edg/90.0.818.49', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_emails`
--

DROP TABLE IF EXISTS `users_emails`;
CREATE TABLE `users_emails` (
  `id` int(10) UNSIGNED NOT NULL,
  `thread_id` varchar(32) DEFAULT NULL,
  `company_id` varchar(32) DEFAULT NULL,
  `user_id` varchar(32) DEFAULT NULL,
  `subject` varchar(1000) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `sender_details` varchar(2000) DEFAULT NULL,
  `recipient_details` text DEFAULT NULL,
  `recipient_list` text DEFAULT NULL,
  `copy_recipients` text DEFAULT NULL,
  `copy_recipients_list` text DEFAULT NULL,
  `read_list` text DEFAULT NULL,
  `favorite_list` text DEFAULT NULL,
  `important_list` text DEFAULT NULL,
  `trash_list` text DEFAULT NULL,
  `deleted_list` text DEFAULT NULL,
  `archive_list` text DEFAULT NULL,
  `label` enum('draft','inbox','trash','important','sent','archive') NOT NULL DEFAULT 'inbox',
  `mode` varchar(12) DEFAULT 'inbox',
  `schedule_send` enum('true','false') NOT NULL DEFAULT 'false',
  `schedule_date` datetime DEFAULT current_timestamp(),
  `sent_status` enum('0','1') NOT NULL DEFAULT '0',
  `sent_date` datetime DEFAULT NULL,
  `attachment_size` varchar(12) NOT NULL DEFAULT '0',
  `date_created` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users_feedback`
--

DROP TABLE IF EXISTS `users_feedback`;
CREATE TABLE `users_feedback` (
  `id` int(11) UNSIGNED NOT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `resource` varchar(40) DEFAULT NULL,
  `resource_id` varchar(40) DEFAULT NULL,
  `feedback_type` enum('reply','comment') NOT NULL DEFAULT 'reply',
  `user_id` varchar(32) DEFAULT NULL,
  `user_type` enum('business','user','bancassurance','broker','agent','nic','reinsurance','admin','nic','insurance_company') DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `mentions` varchar(2000) DEFAULT NULL,
  `date_created` datetime DEFAULT current_timestamp(),
  `likes_count` varchar(12) NOT NULL DEFAULT '0',
  `comments_count` varchar(12) NOT NULL DEFAULT '0',
  `user_agent` varchar(255) DEFAULT NULL,
  `deleted` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users_gender`
--

DROP TABLE IF EXISTS `users_gender`;
CREATE TABLE `users_gender` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_gender`
--

INSERT INTO `users_gender` (`id`, `name`) VALUES
(1, 'Male'),
(2, 'Female'),
(3, 'Other');

-- --------------------------------------------------------

--
-- Table structure for table `users_login_history`
--

DROP TABLE IF EXISTS `users_login_history`;
CREATE TABLE `users_login_history` (
  `id` int(11) UNSIGNED NOT NULL,
  `client_id` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastlogin` datetime DEFAULT current_timestamp(),
  `log_ipaddress` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `log_browser` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `log_platform` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_login_history`
--

INSERT INTO `users_login_history` (`id`, `client_id`, `username`, `lastlogin`, `log_ipaddress`, `log_browser`, `user_id`, `log_platform`) VALUES
(1, 'TLIS0000001', 'emmallob14', '2021-02-19 21:49:46', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.18'),
(2, 'TLIS0000001', 'emmallob14', '2021-02-20 08:10:41', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.18'),
(3, 'TLIS0000001', 'emmallob14', '2021-02-20 14:05:00', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.18'),
(4, 'TLIS0000001', 'emmallob14', '2021-02-20 16:37:10', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.18'),
(5, 'TLIS0000001', 'emmallob14', '2021-02-20 20:31:44', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.18'),
(6, 'TLIS0000001', 'asmahhenry', '2021-02-20 20:50:52', '::1', 'Chrome|Windows 10', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.15'),
(7, 'TLIS0000001', 'fredamponsah', '2021-02-20 22:30:16', '::1', 'Chrome|Windows 10', 'tnThYo5wKHG2XxgPdSVkErb7zLlqum1A', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.18'),
(8, 'TLIS0000001', 'julian', '2021-02-20 22:30:26', '::1', 'Chrome|Windows 10', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.15'),
(9, 'TLIS0000001', 'emmallob14', '2021-02-21 14:28:22', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.18'),
(10, 'TLIS0000001', 'emmallob14', '2021-02-21 23:16:43', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.18'),
(11, 'TLIS0000001', 'emmallob14', '2021-02-22 13:54:34', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.18'),
(12, 'MSIS0000002', 'morningstar', '2021-02-22 14:16:55', '::1', 'Chrome|Windows 10', 'vmVn9KIyMRx4ASTDCY0qLF83lWgXZpPa', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.18'),
(13, 'MSIS0000002', 'morningstar', '2021-02-22 16:14:18', '::1', 'Chrome|Windows 10', 'vmVn9KIyMRx4ASTDCY0qLF83lWgXZpPa', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.18'),
(14, 'TLIS0000001', 'emmallob14', '2021-02-22 16:39:53', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.18'),
(15, 'MSIS0000002', 'morningstar', '2021-02-22 16:48:23', '::1', 'Chrome|Windows 10', 'vmVn9KIyMRx4ASTDCY0qLF83lWgXZpPa', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.18'),
(16, 'TLIS0000001', 'emmallob14', '2021-02-22 19:18:47', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.18'),
(17, 'MSIS0000002', 'morningstar', '2021-02-22 19:35:25', '::1', 'Chrome|Windows 10', 'vmVn9KIyMRx4ASTDCY0qLF83lWgXZpPa', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.15'),
(18, 'MSIS0000002', 'morningstar', '2021-02-23 00:41:36', '::1', 'Chrome|Windows 10', 'vmVn9KIyMRx4ASTDCY0qLF83lWgXZpPa', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.15'),
(19, 'TLIS0000001', 'emmallob14', '2021-02-23 08:10:58', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.18'),
(20, 'MSIS0000002', 'morningstar', '2021-02-23 09:25:44', '::1', 'Chrome|Windows 10', 'vmVn9KIyMRx4ASTDCY0qLF83lWgXZpPa', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.15'),
(21, 'TLIS0000001', 'emmallob14', '2021-02-23 12:22:53', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.18'),
(22, 'TLIS0000001', 'emmallob14', '2021-02-23 15:14:22', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.18'),
(23, 'TLIS0000001', 'emmallob14', '2021-02-23 16:51:19', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.18'),
(24, 'TLIS0000001', 'emmallob14', '2021-02-23 22:23:26', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.18'),
(25, 'TLIS0000001', 'emmallob14', '2021-02-24 03:28:23', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.18'),
(26, 'TLIS0000001', 'emmallob14', '2021-02-24 10:45:14', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.18'),
(27, 'GIS00003', 'info@gallaxyinternationalschool.com', '2021-03-17 10:31:32', '::1', 'Chrome|Windows 10', '9HOBYRpyMxJr60foS5FdQXGDUCsviNeh', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.19'),
(28, 'GIS00003', 'info@gallaxyinternationalschool.com', '2021-03-17 10:32:36', '::1', 'Chrome|Windows 10', '9HOBYRpyMxJr60foS5FdQXGDUCsviNeh', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.19'),
(29, 'GIS00003', 'info@gallaxyinternationalschool.com', '2021-03-17 10:33:13', '::1', 'Chrome|Windows 10', '9HOBYRpyMxJr60foS5FdQXGDUCsviNeh', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.19'),
(30, 'TLIS0000001', 'emmallob14@gmail.com', '2021-03-17 10:43:09', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.19'),
(31, 'TLIS0000001', 'emmallob14@gmail.com', '2021-03-17 22:26:06', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.19'),
(32, 'TLIS0000001', 'emmallob14@gmail.com', '2021-03-18 09:26:56', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.19'),
(33, 'TLIS0000001', 'emmallob14@gmail.com', '2021-03-18 17:22:20', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.19'),
(34, 'TLIS0000001', 'emmallob14@gmail.com', '2021-03-18 17:26:20', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.19'),
(35, 'TLIS0000001', 'emmallob14@gmail.com', '2021-03-18 17:26:32', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.19'),
(36, 'TLIS0000001', 'emmallob14@gmail.com', '2021-03-18 20:58:42', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.19'),
(37, 'TLIS0000001', 'emmallob14@gmail.com', '2021-03-19 06:48:16', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.90'),
(38, 'TLIS0000001', 'emmallob14@gmail.com', '2021-03-19 12:15:22', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.90'),
(39, 'TLIS0000001', 'emmallob14@gmail.com', '2021-03-19 21:49:58', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.90'),
(40, 'TLIS0000001', 'emmallob14@gmail.com', '2021-03-20 08:18:20', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.90'),
(41, 'TLIS0000001', 'emmallob14@gmail.com', '2021-03-20 10:48:19', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.90'),
(42, 'TLIS0000001', 'emmallob14@gmail.com', '2021-03-20 21:29:10', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.90'),
(43, 'TLIS0000001', 'emmallob14@gmail.com', '2021-03-21 08:39:27', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.90'),
(44, 'TLIS0000001', 'emmallob14', '2021-03-23 11:37:24', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.90'),
(45, 'TLIS0000001', 'emmallob14', '2021-04-16 13:15:06', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.12'),
(46, 'TLIS0000001', 'emmallob14', '2021-04-17 14:21:03', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.12'),
(47, 'TLIS0000001', 'emmallob14', '2021-04-17 20:20:39', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.12'),
(48, 'TLIS0000001', 'emmallob14', '2021-04-17 22:32:31', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.12'),
(49, 'TLIS0000001', 'emmallob14', '2021-04-18 05:48:12', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.12'),
(50, 'TLIS0000001', 'emmallob14', '2021-04-19 13:20:29', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.12'),
(51, 'TLIS0000001', 'emmallob14', '2021-04-20 12:34:21', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.12'),
(52, 'TLIS0000001', 'emmallob14', '2021-04-20 13:01:52', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.12'),
(53, 'TLIS0000001', 'emmallob14', '2021-04-20 13:41:27', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.12'),
(54, 'TLIS0000001', 'emmallob', '2021-04-20 13:43:02', '::1', 'Chrome|Windows 10', '1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.12'),
(55, 'TLIS0000001', 'emmallob14', '2021-04-20 21:49:49', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.12'),
(56, 'TLIS0000001', 'emmallob14', '2021-04-21 05:07:12', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.12'),
(57, 'TLIS0000001', 'emmallob14', '2021-04-21 12:36:49', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.12'),
(58, 'TLIS0000001', 'emmallob14', '2021-04-29 12:00:54', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(59, 'TLIS0000001', 'emmallob14', '2021-04-30 11:29:58', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(60, 'TLIS0000001', 'emmallob14', '2021-05-01 07:05:40', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(61, 'TLIS0000001', 'emmallob', '2021-05-01 07:21:42', '::1', 'Chrome|Windows 10', '1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(62, 'TLIS0000001', 'julian', '2021-05-01 07:22:51', '::1', 'Chrome|Windows 10', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(63, 'TLIS0000001', 'emmallob14', '2021-05-01 10:53:03', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(64, 'TLIS0000001', 'julian', '2021-05-01 10:53:58', '::1', 'Chrome|Windows 10', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(65, 'TLIS0000001', 'julian', '2021-05-01 11:55:27', '::1', 'Chrome|Windows 10', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(66, 'TLIS0000001', 'emmallob14', '2021-05-01 11:55:40', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(67, 'TLIS0000001', 'julian', '2021-05-01 18:50:09', '::1', 'Chrome|Windows 10', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(68, 'TLIS0000001', 'emmallob14', '2021-05-02 11:39:48', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(69, 'TLIS0000001', 'emmallob14', '2021-05-02 16:58:52', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(70, 'TLIS0000001', 'emmallob14', '2021-05-02 18:36:56', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(71, 'TLIS0000001', 'emmallob14', '2021-05-02 19:37:49', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(72, 'TLIS0000001', 'emmallob14', '2021-05-02 20:43:36', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(73, 'TLIS0000001', 'emmallob14', '2021-05-02 22:22:50', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(74, 'TLIS0000001', 'julian', '2021-05-02 23:05:40', '::1', 'Chrome|Windows 10', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(75, 'TLIS0000001', 'emmallob14', '2021-05-03 08:13:08', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(76, 'TLIS0000001', 'julian', '2021-05-03 08:13:17', '::1', 'Chrome|Windows 10', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(77, 'TLIS0000001', 'julian', '2021-05-03 09:21:19', '::1', 'Chrome|Windows 10', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(78, 'TLIS0000001', 'emmallob14', '2021-05-03 12:13:48', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(79, 'TLIS0000001', 'julian', '2021-05-03 12:13:51', '::1', 'Chrome|Windows 10', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(80, 'TLIS0000001', 'emmallob14', '2021-05-03 16:29:46', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(81, 'TLIS0000001', 'julian', '2021-05-03 16:29:52', '::1', 'Chrome|Windows 10', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(82, 'TLIS0000001', 'emmallob14', '2021-05-03 19:26:42', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(83, 'TLIS0000001', 'julian', '2021-05-03 20:32:24', '::1', 'Chrome|Windows 10', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(84, 'TLIS0000001', 'emmallob14', '2021-05-03 22:10:52', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(85, 'TLIS0000001', 'emmallob14', '2021-05-05 16:03:04', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(86, 'TLIS0000001', 'emmallob14', '2021-05-06 08:46:30', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(87, 'TLIS0000001', 'emmallob14', '2021-05-06 09:14:42', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(88, 'TLIS0000001', 'emmallob14', '2021-05-06 09:14:58', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(89, 'TLIS0000001', 'emmallob14', '2021-05-06 14:49:20', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(90, 'TLIS0000001', 'emmallob14', '2021-05-06 23:11:12', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(91, 'MSGH000004', 'testsampleschool', '2021-05-06 23:13:40', '::1', 'Chrome|Windows 10', 'Z3qWQN8stLBUSg2FArpOIKEDMJvdafYk', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(92, 'MSGH000004', 'testsampleschool', '2021-05-06 23:13:48', '::1', 'Chrome|Windows 10', 'Z3qWQN8stLBUSg2FArpOIKEDMJvdafYk', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(93, 'TLIS0000001', 'emmallob14', '2021-05-06 23:51:33', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85'),
(94, 'TLIS0000001', 'emmallob14', '2021-05-07 20:05:54', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93'),
(95, 'TLIS0000001', 'emmallob14', '2021-05-08 17:47:58', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93'),
(96, 'TLIS0000001', 'emmallob14', '2021-05-09 11:12:01', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93'),
(97, 'TLIS0000001', 'emmallob14', '2021-05-11 11:24:31', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93'),
(98, 'TLIS0000001', 'emmallob14', '2021-05-11 15:37:32', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93'),
(99, 'TLIS0000001', 'emmallob14', '2021-05-11 17:07:31', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93'),
(100, 'TLIS0000001', 'emmallob14', '2021-05-11 20:57:09', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93'),
(101, 'TLIS0000001', 'emmallob14', '2021-05-12 04:05:00', '::1', 'Chrome|Windows 10', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93');

-- --------------------------------------------------------

--
-- Table structure for table `users_messaging_list`
--

DROP TABLE IF EXISTS `users_messaging_list`;
CREATE TABLE `users_messaging_list` (
  `id` int(11) UNSIGNED NOT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `template_type` varchar(32) DEFAULT NULL,
  `users_id` varchar(1000) DEFAULT NULL,
  `recipients_list` text DEFAULT NULL,
  `date_requested` datetime DEFAULT current_timestamp(),
  `schedule_type` enum('send_now','send_later') NOT NULL DEFAULT 'send_now',
  `schedule_date` datetime NOT NULL DEFAULT current_timestamp(),
  `message_medium` enum('email','sms') NOT NULL DEFAULT 'email',
  `sent_status` enum('0','1') DEFAULT '0',
  `subject` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_by` varchar(32) DEFAULT NULL,
  `deleted` enum('0','1') DEFAULT '0',
  `date_sent` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users_messaging_list`
--

INSERT INTO `users_messaging_list` (`id`, `item_id`, `client_id`, `template_type`, `users_id`, `recipients_list`, `date_requested`, `schedule_type`, `schedule_date`, `message_medium`, `sent_status`, `subject`, `message`, `created_by`, `deleted`, `date_sent`) VALUES
(1, 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'TLIS0000001', 'verify_account', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '{\"recipients_list\":[{\"fullname\":\"True Love International School\",\"email\":\"emmallob14@gmail.com\",\"customer_id\":\"JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM\"}]}', '2021-02-19 20:43:24', 'send_now', '2021-02-19 20:43:24', 'email', '0', '[MySchoolGH] Account Verification', 'Thank you for registering your School: <strong>True Love International School</strong> with MySchoolGH.\r\n                        We are pleased to have you join and benefit from our platform.\r\n\r\nOne of our personnel will get in touch shortly to assist you with additional setup processes that is required to aid you quick start the usage of the application.\r\n\r\n<a href=\'http://localhost/myschool_gh/verify?account=true&token=Cp5UM0edDfJT6ocFbkrXRsw3HzN9Px8LSh1my7qujntO4KIWYZQlBG\'><strong>Click Here</strong></a> to verify your Email Address and also to activate the account.\r\n\r\n', 'TLISU0000001', '0', NULL),
(2, 'vmVn9KIyMRx4ASTDCY0qLF83lWgXZpPa', 'MSIS0000002', 'verify_account', 'vmVn9KIyMRx4ASTDCY0qLF83lWgXZpPa', '{\"recipients_list\":[{\"fullname\":\"Morning Star International School\",\"email\":\"morningstar@gmail.com\",\"customer_id\":\"vmVn9KIyMRx4ASTDCY0qLF83lWgXZpPa\"}]}', '2021-02-22 14:13:22', 'send_now', '2021-02-22 14:13:22', 'email', '0', '[MySchoolGH] Account Verification', 'Thank you for registering your School: <strong>Morning Star International School</strong> with MySchoolGH.\r\n                        We are pleased to have you join and benefit from our platform.<br><br>\r\n                        Your can login with your <strong>Email Address:</strong> morningstar@gmail.com or <strong>Username:</strong> morningstar\r\n                        and the password that was provided during signup.<br><br>One of our personnel will get in touch shortly to assist you with additional setup processes that is required to aid you quick start the usage of the application.<br></br><a href=\'http://localhost/myschool_gh/verify?dw=account&token=syCEZr2Iakd8K67xtB5HupRNwnm9J0FcbUg3QeLhX1TvVSG4DAMPYo\'><strong>Click Here</strong></a> to verify your Email Address and also to activate the account.<br><br>', 'MSISU0000001', '0', NULL),
(3, '9HOBYRpyMxJr60foS5FdQXGDUCsviNeh', 'GIS00003', 'verify_account', '9HOBYRpyMxJr60foS5FdQXGDUCsviNeh', '{\"recipients_list\":[{\"fullname\":\"Galaxy International School\",\"email\":\"info@gallaxyinternationalschool.com\",\"customer_id\":\"9HOBYRpyMxJr60foS5FdQXGDUCsviNeh\"}]}', '2021-03-17 10:20:28', 'send_now', '2021-03-17 10:20:28', 'email', '0', '[MySchoolGH] Account Verification', 'Thank you for registering your School: <strong>Galaxy International School</strong> with MySchoolGH.\r\n                        We are pleased to have you join and benefit from our platform.<br><br>\r\n                        Your can login with your <strong>Email Address:</strong> info@gallaxyinternationalschool.com or <strong>Username:</strong> info\r\n                        and the password that was provided during signup.<br><br>One of our personnel will get in touch shortly to assist you with additional setup processes that is required to aid you quick start the usage of the application.<br></br><a href=\'http://localhost/myschool_gh/verify?dw=account&token=r0GnHJ3smfZQziSRNC8Ie65VPBOYuMLvEpjwyAtq2klWXxKTodhDcg\'><strong>Click Here</strong></a> to verify your Email Address and also to activate the account.<br><br>', 'GISU000001', '0', NULL),
(4, 'Z3qWQN8stLBUSg2FArpOIKEDMJvdafYk', 'MSGH000004', 'verify_account', 'Z3qWQN8stLBUSg2FArpOIKEDMJvdafYk', '{\"recipients_list\":[{\"fullname\":\"Test Sample School\",\"email\":\"testsampleschool@mail.com\",\"customer_id\":\"Z3qWQN8stLBUSg2FArpOIKEDMJvdafYk\"}]}', '2021-05-06 22:59:25', 'send_now', '2021-05-06 22:59:25', 'email', '0', '[MySchoolGH] Account Verification', 'Thank you for registering your School: <strong>Test Sample School</strong> with MySchoolGH.\r\n                        We are pleased to have you join and benefit from our platform.<br><br>\r\n                        Your can login with your <strong>Email Address:</strong> testsampleschool@mail.com or <strong>Username:</strong> testsampleschool\r\n                        and the password that was provided during signup.<br><br>One of our personnel will get in touch shortly to assist you with additional setup processes that is required to aid you quick start the usage of the application.<br></br><a href=\'http://localhost/myschool_gh/verify?dw=account&token=K1lwMeg7hxHL4DWI89jNZQRSOtXp0bCAdJoT3FPrn5G6cEifkqaYyU\'><strong>Click Here</strong></a> to verify your Email Address and also to activate the account.<br><br>', 'TSSU000001', '0', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_notification`
--

DROP TABLE IF EXISTS `users_notification`;
CREATE TABLE `users_notification` (
  `id` int(11) UNSIGNED NOT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `user_id` varchar(32) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `resource_page` varchar(64) DEFAULT NULL,
  `initiated_by` enum('user','system') DEFAULT 'user',
  `notice_type` varchar(32) DEFAULT NULL,
  `created_by` varchar(32) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `seen_status` enum('Seen','Unseen') NOT NULL DEFAULT 'Unseen',
  `seen_date` datetime DEFAULT NULL,
  `confirmed` enum('0','1') DEFAULT '0',
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users_notification`
--

INSERT INTO `users_notification` (`id`, `item_id`, `user_id`, `subject`, `message`, `resource_page`, `initiated_by`, `notice_type`, `created_by`, `date_created`, `seen_status`, `seen_date`, `confirmed`, `status`) VALUES
(1, 'Rbn16gCIx0B2TeoL3tp5ZAlX8dVMujOY', 'tnThYo5wKHG2XxgPdSVkErb7zLlqum1A', 'Account Update', '<strong>Emmanuel Obeng Hyde</strong> updated your account information', NULL, 'system', '9', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-02-20 09:53:29', 'Unseen', NULL, '0', '1'),
(2, 'T0JMfaKsRGLQrNCDmB1kIcA4pxP9l6v5', 'tnThYo5wKHG2XxgPdSVkErb7zLlqum1A', 'Account Update', '<strong>Emmanuel Obeng Hyde</strong> updated your account information', NULL, 'system', '9', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-02-20 09:53:40', 'Unseen', NULL, '0', '1'),
(3, 'U7QyjvlfLAhTteSYgaXCN4Bcq6zR9nGP', 'tnThYo5wKHG2XxgPdSVkErb7zLlqum1A', 'Account Update', '<strong>Emmanuel Obeng Hyde</strong> updated your account information', NULL, 'system', '9', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-02-20 09:55:35', 'Unseen', NULL, '0', '1'),
(4, 'y0TF6WzI9cGgdj3Mo4uabKHEBJ2Z1VNp', '1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', 'Account Update', '<strong>Emmanuel Obeng Hyde</strong> updated your account information', NULL, 'system', '9', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-02-23 09:32:38', 'Unseen', NULL, '0', '1'),
(5, 'dPFQbmAwlXgU8ztZ4xiuOKGp6sHWk3CR', 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'Account Update', '<strong>Emmanuel Obeng Hyde</strong> updated your account information', NULL, 'system', '9', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', '2021-02-23 09:32:59', 'Unseen', NULL, '0', '1');

-- --------------------------------------------------------

--
-- Table structure for table `users_notification_types`
--

DROP TABLE IF EXISTS `users_notification_types`;
CREATE TABLE `users_notification_types` (
  `id` int(11) NOT NULL,
  `name` varchar(62) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alias` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `priority` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `favicon` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `favicon_color` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_notification_types`
--

INSERT INTO `users_notification_types` (`id`, `name`, `alias`, `priority`, `favicon`, `favicon_color`, `status`) VALUES
(1, 'Renew Policy', 'policy', 'Moderate', NULL, NULL, '1'),
(3, 'Login Attempts', 'account', 'Very High', 'fa fa-lock', 'text-danger', '1'),
(4, 'Reset Password', 'password', 'High', 'fa fa-lock-open', 'text-danger', '1'),
(5, 'Message', 'message', 'Moderate', 'fa fa-envelope', NULL, '1'),
(8, 'Renew License', 'license', 'Moderate', NULL, NULL, '1'),
(9, 'Account Update', 'account', 'Moderate', 'fa fa-user', NULL, '1'),
(10, 'Status Change', 'status-change', 'Moderate', 'fa fa-random', 'text-primary', '1'),
(12, 'Announcement', 'announcement', NULL, 'fa fa-bell', NULL, '1');

-- --------------------------------------------------------

--
-- Table structure for table `users_payments`
--

DROP TABLE IF EXISTS `users_payments`;
CREATE TABLE `users_payments` (
  `id` int(10) UNSIGNED NOT NULL,
  `record_type` enum('licenses','policy','adverts') DEFAULT NULL,
  `record_id` varchar(32) DEFAULT NULL,
  `record_details` text DEFAULT NULL,
  `user_id` varchar(32) DEFAULT NULL,
  `checkout_url` varchar(255) DEFAULT NULL,
  `initiated_by` varchar(32) DEFAULT NULL,
  `initiated_medium` enum('user','system') NOT NULL DEFAULT 'system',
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `amount` double(12,2) DEFAULT 0.00,
  `payment_status` enum('Pending','Paid','Cancelled','Failed') DEFAULT 'Pending',
  `payment_date` datetime DEFAULT NULL,
  `payment_option` enum('expresspay','slydepay','payswitch') DEFAULT NULL,
  `payment_checkout_url` varchar(500) DEFAULT NULL,
  `payment_info` varchar(500) DEFAULT NULL,
  `momo_medium` varchar(32) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `replies_count` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `comments_count` int(12) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users_posts`
--

DROP TABLE IF EXISTS `users_posts`;
CREATE TABLE `users_posts` (
  `id` int(11) UNSIGNED NOT NULL,
  `resource_id` varchar(32) DEFAULT NULL,
  `shared_by` varchar(32) DEFAULT NULL,
  `user_type` varchar(32) DEFAULT NULL,
  `post_id` varchar(65) DEFAULT NULL,
  `post_parent_id` varchar(32) DEFAULT '0',
  `post_content` text DEFAULT NULL,
  `post_mentions` varchar(1000) DEFAULT NULL,
  `post_user_agent` varchar(255) DEFAULT NULL,
  `post_user_device` varchar(255) DEFAULT NULL,
  `date_created` datetime DEFAULT current_timestamp(),
  `views_count` smallint(12) UNSIGNED NOT NULL DEFAULT 0,
  `likes_list` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `likes_count` smallint(12) UNSIGNED DEFAULT 0,
  `comments_count` smallint(12) UNSIGNED DEFAULT 0,
  `shares_count` varchar(12) NOT NULL DEFAULT '0',
  `visibility` enum('Public','Private','Clients') NOT NULL DEFAULT 'Public',
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users_reset_request`
--

DROP TABLE IF EXISTS `users_reset_request`;
CREATE TABLE `users_reset_request` (
  `id` int(11) NOT NULL,
  `item_id` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `user_id` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `user_agent` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token_status` enum('USED','EXPIRED','PENDING','ANNULED') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDING',
  `request_token` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_date` datetime DEFAULT NULL,
  `reset_agent` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expiry_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_roles`
--

DROP TABLE IF EXISTS `users_roles`;
CREATE TABLE `users_roles` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `client_id` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
  `permissions` text CHARACTER SET utf8mb4 DEFAULT NULL,
  `date_logged` datetime NOT NULL DEFAULT current_timestamp(),
  `last_updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_roles`
--

INSERT INTO `users_roles` (`id`, `user_id`, `client_id`, `permissions`, `date_logged`, `last_updated`) VALUES
(1, 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'TLIS0000001', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"guardian\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"incident\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"department\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"section\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"events\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"class\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"attendance\":{\"log\":1,\"finalize\":1},\"library\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1,\"issue\":1,\"return\":1},\"fees\":{\"view\":1,\"update\":1,\"receive\":1,\"allocation\":1,\"view_allocation\":1,\"reports\":1},\"fees_category\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"results\":{\"upload\":1,\"modify\":1,\"approve\":1,\"generate\":1},\"course\":{\"view\":1,\"add\":1,\"update\":1,\"lesson\":1,\"delete\":1},\"permissions\":{\"view\":1,\"update\":1},\"assignments\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1,\"mark\":1},\"timetable\":{\"manage\":1,\"allocate\":1},\"settings\":{\"filters\":1,\"manage\":1,\"activities\":1,\"login_history\":1},\"payslip\":{\"view\":1,\"modify_payroll\":1,\"generate\":1,\"validate\":1,\"manage_expense\":1}}}', '2021-02-19 20:43:24', NULL),
(2, '1v9Gqy2ATRlCzMjESgiY4U05O3mpWLNB', 'TLIS0000001', '{\"permissions\":{\"student\":{\"view\":1},\"teacher\":{\"view\":1},\"parent\":{\"view\":1},\"employee\":{\"view\":1},\"assignments\":{\"view\":1,\"handin\":1}}}', '2021-02-20 08:37:02', '2021-02-20 08:37:02'),
(3, 'GSzAlD7uPObpda2jKtE0qhWUV8igTo9f', 'TLIS0000001', '{\"permissions\":{\"student\":{\"view\":1},\"teacher\":{\"view\":1},\"parent\":{\"view\":1},\"employee\":{\"view\":1},\"assignments\":{\"view\":1,\"handin\":1}}}', '2021-02-20 08:37:02', '2021-02-20 08:37:02'),
(4, 'tnThYo5wKHG2XxgPdSVkErb7zLlqum1A', 'TLIS0000001', '{\"permissions\":{\"student\":{\"view\":1},\"teacher\":{\"view\":1},\"parent\":{\"view\":1},\"employee\":{\"view\":1},\"attendance\":{\"log\":1},\"library\":{\"request\":1},\"course\":{\"update\":1,\"lesson\":1},\"assignments\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1,\"mark\":1}}}', '2021-02-20 09:07:18', '2021-02-20 09:07:18'),
(5, 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', 'TLIS0000001', '{\"permissions\":{\"student\":{\"view\":1},\"teacher\":{\"view\":1},\"parent\":{\"view\":1},\"employee\":{\"view\":1},\"attendance\":{\"log\":1},\"library\":{\"request\":1},\"course\":{\"update\":1,\"lesson\":1},\"assignments\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1,\"mark\":1}}}', '2021-02-20 09:07:18', '2021-02-20 09:07:18'),
(6, 'vmVn9KIyMRx4ASTDCY0qLF83lWgXZpPa', 'MSIS0000002', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"guardian\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"incident\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"department\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"section\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"events\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"class\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"attendance\":{\"log\":1,\"finalize\":1},\"library\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1,\"issue\":1,\"return\":1},\"fees\":{\"view\":1,\"update\":1,\"receive\":1,\"allocation\":1,\"view_allocation\":1},\"fees_category\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"course\":{\"view\":1,\"add\":1,\"update\":1,\"lesson\":1,\"delete\":1},\"permissions\":{\"view\":1,\"update\":1},\"assignments\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1,\"mark\":1},\"timetable\":{\"manage\":1,\"allocate\":1},\"settings\":{\"filters\":1,\"manage\":1,\"activities\":1,\"login_history\":1},\"payslip\":{\"view\":1,\"modify_payroll\":1,\"generate\":1,\"validate\":1,\"manage_expense\":1}}}', '2021-02-22 14:13:22', NULL),
(7, 'T18sVwiSd4HumlNkXUfh3ngjCEP0M7LK', 'MSIS0000002', '{\"permissions\":{\"student\":{\"view\":1},\"teacher\":{\"view\":1},\"parent\":{\"view\":1},\"employee\":{\"view\":1},\"assignments\":{\"view\":1,\"handin\":1}}}', '2021-02-23 09:27:24', '2021-02-23 09:27:24'),
(8, 'qkrNWbzA3EoZLaSleY5T4291ICQsmvdD', 'MSIS0000002', '{\"permissions\":{\"student\":{\"view\":1},\"teacher\":{\"view\":1},\"parent\":{\"view\":1},\"employee\":{\"view\":1},\"assignments\":{\"view\":1,\"handin\":1}}}', '2021-02-23 09:27:24', '2021-02-23 09:27:24'),
(9, '8hMv9C2qmL1ZH04WwyKfnrRAPusbVGx6', 'MSIS0000002', '{\"permissions\":{\"student\":{\"view\":1},\"teacher\":{\"view\":1},\"parent\":{\"view\":1},\"employee\":{\"view\":1},\"attendance\":{\"log\":1},\"library\":{\"request\":1},\"course\":{\"update\":1,\"lesson\":1},\"assignments\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1,\"mark\":1}}}', '2021-02-23 09:27:36', '2021-02-23 09:27:36'),
(10, 'ZYibOC4wzLWBXUAa5skuhNS2KxQ7nr1f', 'MSIS0000002', '{\"permissions\":{\"student\":{\"view\":1},\"teacher\":{\"view\":1},\"parent\":{\"view\":1},\"employee\":{\"view\":1},\"attendance\":{\"log\":1},\"library\":{\"request\":1},\"course\":{\"update\":1,\"lesson\":1},\"assignments\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1,\"mark\":1}}}', '2021-02-23 09:27:36', '2021-02-23 09:27:36'),
(11, '9HOBYRpyMxJr60foS5FdQXGDUCsviNeh', 'GIS00003', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"guardian\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"incident\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"department\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"section\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"events\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"class\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"attendance\":{\"log\":1,\"finalize\":1},\"library\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1,\"issue\":1,\"return\":1},\"fees\":{\"view\":1,\"update\":1,\"receive\":1,\"allocation\":1,\"view_allocation\":1},\"fees_category\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"course\":{\"view\":1,\"add\":1,\"update\":1,\"lesson\":1,\"delete\":1},\"permissions\":{\"view\":1,\"update\":1},\"assignments\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1,\"mark\":1},\"timetable\":{\"manage\":1,\"allocate\":1},\"settings\":{\"filters\":1,\"manage\":1,\"activities\":1,\"login_history\":1},\"payslip\":{\"view\":1,\"modify_payroll\":1,\"generate\":1,\"validate\":1,\"manage_expense\":1}}}', '2021-03-17 10:20:28', NULL),
(12, 'Z3qWQN8stLBUSg2FArpOIKEDMJvdafYk', 'MSGH000004', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"guardian\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"incident\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"department\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"section\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"events\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"class\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"attendance\":{\"log\":1,\"finalize\":1},\"library\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1,\"issue\":1,\"return\":1},\"fees\":{\"view\":1,\"update\":1,\"receive\":1,\"allocation\":1,\"view_allocation\":1},\"fees_category\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"results\":{\"upload\":1,\"modify\":1,\"approve\":1,\"generate\":1},\"course\":{\"view\":1,\"add\":1,\"update\":1,\"lesson\":1,\"delete\":1},\"permissions\":{\"view\":1,\"update\":1},\"assignments\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1,\"mark\":1},\"timetable\":{\"manage\":1,\"allocate\":1},\"settings\":{\"filters\":1,\"manage\":1,\"activities\":1,\"login_history\":1},\"payslip\":{\"view\":1,\"modify_payroll\":1,\"generate\":1,\"validate\":1,\"manage_expense\":1}}}', '2021-05-06 22:59:24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_temp_forms`
--

DROP TABLE IF EXISTS `users_temp_forms`;
CREATE TABLE `users_temp_forms` (
  `id` int(11) NOT NULL,
  `user_id` varchar(32) DEFAULT NULL,
  `form_modules` varchar(64) DEFAULT NULL,
  `form_content` text DEFAULT NULL,
  `expiry_time` datetime DEFAULT NULL,
  `status` enum('0','1') DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users_types`
--

DROP TABLE IF EXISTS `users_types`;
CREATE TABLE `users_types` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'GUEST',
  `description` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_permissions` text COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_types`
--

INSERT INTO `users_types` (`id`, `name`, `description`, `user_permissions`) VALUES
(1, 'STUDENT', 'student', '{\"permissions\":{\"student\":{\"view\":1},\"teacher\":{\"view\":1},\"parent\":{\"view\":1},\"employee\":{\"view\":1},\"assignments\":{\"view\":1,\"handin\":1}}}'),
(2, 'TEACHER', 'teacher', '{\"permissions\":{\"student\":{\"view\":1},\"teacher\":{\"view\":1},\"parent\":{\"view\":1},\"employee\":{\"view\":1},\"attendance\":{\"log\":1},\"library\":{\"request\":1},\"course\":{\"update\":1,\"lesson\":1},\"assignments\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1,\"mark\":1},\"results\":{\"upload\":1,\"modify\":1}}}'),
(3, 'PARENT', 'parent', '{\"permissions\":{\"student\":{\"view\":1},\"teacher\":{\"view\":1},\"parent\":{\"view\":1},\"employee\":{\"view\":1},\"library\":{\"request\":1},\"fees\":{\"view\":1,\"view_allocation\":1},\"assignments\":{\"view\":1,\"handin\":1}}}'),
(4, 'EMPLOYEE', 'employee', '{\"permissions\":{\"student\":{\"view\":1},\"teacher\":{\"view\":1},\"parent\":{\"view\":1},\"employee\":{\"view\":1},\"library\":{\"request\":1}}}'),
(5, 'ACCOUNTANT', 'accountant', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"guardian\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"incident\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"department\":{\"view\":1},\"section\":{\"view\":1},\"events\":{\"view\":1},\"class\":{\"view\":1},\"attendance\":{\"log\":1},\"library\":{\"view\":1,\"return\":1},\"fees\":{\"view\":1,\"update\":1,\"receive\":1,\"allocation\":1,\"view_allocation\":1,\"reports\":1},\"fees_category\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"course\":{\"view\":1,\"lesson\":1},\"settings\":{\"filters\":1},\"payslip\":{\"view\":1,\"modify_payroll\":1,\"generate\":1,\"validate\":1,\"manage_expense\":1}}}'),
(6, 'ADMIN', 'admin', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"guardian\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"incident\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"department\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"section\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"events\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"class\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"attendance\":{\"log\":1,\"finalize\":1},\"library\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1,\"issue\":1,\"return\":1},\"fees\":{\"view\":1,\"update\":1,\"receive\":1,\"allocation\":1,\"view_allocation\":1,\"reports\":1},\"fees_category\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"results\":{\"upload\":1,\"modify\":1,\"approve\":1,\"generate\":1},\"course\":{\"view\":1,\"add\":1,\"update\":1,\"lesson\":1,\"delete\":1},\"permissions\":{\"view\":1,\"update\":1},\"assignments\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1,\"mark\":1},\"timetable\":{\"manage\":1,\"allocate\":1},\"settings\":{\"filters\":1,\"manage\":1,\"activities\":1,\"login_history\":1},\"payslip\":{\"view\":1,\"modify_payroll\":1,\"generate\":1,\"validate\":1,\"manage_expense\":1}}}');

-- --------------------------------------------------------

--
-- Table structure for table `wn_antonym`
--

DROP TABLE IF EXISTS `wn_antonym`;
CREATE TABLE `wn_antonym` (
  `synset_id_1` decimal(10,0) DEFAULT NULL,
  `wnum_1` decimal(10,0) DEFAULT NULL,
  `synset_id_2` decimal(10,0) DEFAULT NULL,
  `wnum_2` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `wn_attr_adj_noun`
--

DROP TABLE IF EXISTS `wn_attr_adj_noun`;
CREATE TABLE `wn_attr_adj_noun` (
  `synset_id_1` decimal(10,0) DEFAULT NULL,
  `synset_id_2` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `wn_cause`
--

DROP TABLE IF EXISTS `wn_cause`;
CREATE TABLE `wn_cause` (
  `synset_id_1` decimal(10,0) DEFAULT NULL,
  `synset_id_2` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `wn_class_member`
--

DROP TABLE IF EXISTS `wn_class_member`;
CREATE TABLE `wn_class_member` (
  `synset_id_1` decimal(10,0) DEFAULT NULL,
  `synset_id_2` decimal(10,0) DEFAULT NULL,
  `class_type` char(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `wn_derived`
--

DROP TABLE IF EXISTS `wn_derived`;
CREATE TABLE `wn_derived` (
  `synset_id_1` decimal(10,0) DEFAULT NULL,
  `wnum_1` decimal(10,0) DEFAULT NULL,
  `synset_id_2` decimal(10,0) DEFAULT NULL,
  `wnum_2` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `wn_entails`
--

DROP TABLE IF EXISTS `wn_entails`;
CREATE TABLE `wn_entails` (
  `synset_id_1` decimal(10,0) DEFAULT NULL,
  `synset_id_2` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `wn_gloss`
--

DROP TABLE IF EXISTS `wn_gloss`;
CREATE TABLE `wn_gloss` (
  `synset_id` decimal(10,0) NOT NULL DEFAULT 0,
  `gloss` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `wn_hypernym`
--

DROP TABLE IF EXISTS `wn_hypernym`;
CREATE TABLE `wn_hypernym` (
  `synset_id_1` decimal(10,0) DEFAULT NULL,
  `synset_id_2` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `wn_hyponym`
--

DROP TABLE IF EXISTS `wn_hyponym`;
CREATE TABLE `wn_hyponym` (
  `synset_id_1` decimal(10,0) DEFAULT NULL,
  `synset_id_2` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `wn_mbr_meronym`
--

DROP TABLE IF EXISTS `wn_mbr_meronym`;
CREATE TABLE `wn_mbr_meronym` (
  `synset_id_1` decimal(10,0) DEFAULT NULL,
  `synset_id_2` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `wn_participle`
--

DROP TABLE IF EXISTS `wn_participle`;
CREATE TABLE `wn_participle` (
  `synset_id_1` decimal(10,0) DEFAULT NULL,
  `wnum_1` decimal(10,0) DEFAULT NULL,
  `synset_id_2` decimal(10,0) DEFAULT NULL,
  `wnum_2` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `wn_part_meronym`
--

DROP TABLE IF EXISTS `wn_part_meronym`;
CREATE TABLE `wn_part_meronym` (
  `synset_id_1` decimal(10,0) DEFAULT NULL,
  `synset_id_2` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `wn_pertainym`
--

DROP TABLE IF EXISTS `wn_pertainym`;
CREATE TABLE `wn_pertainym` (
  `synset_id_1` decimal(10,0) DEFAULT NULL,
  `wnum_1` decimal(10,0) DEFAULT NULL,
  `synset_id_2` decimal(10,0) DEFAULT NULL,
  `wnum_2` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `wn_see_also`
--

DROP TABLE IF EXISTS `wn_see_also`;
CREATE TABLE `wn_see_also` (
  `synset_id_1` decimal(10,0) DEFAULT NULL,
  `wnum_1` decimal(10,0) DEFAULT NULL,
  `synset_id_2` decimal(10,0) DEFAULT NULL,
  `wnum_2` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `wn_similar`
--

DROP TABLE IF EXISTS `wn_similar`;
CREATE TABLE `wn_similar` (
  `synset_id_1` decimal(10,0) DEFAULT NULL,
  `synset_id_2` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `wn_subst_meronym`
--

DROP TABLE IF EXISTS `wn_subst_meronym`;
CREATE TABLE `wn_subst_meronym` (
  `synset_id_1` decimal(10,0) DEFAULT NULL,
  `synset_id_2` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `wn_synset`
--

DROP TABLE IF EXISTS `wn_synset`;
CREATE TABLE `wn_synset` (
  `synset_id` decimal(10,0) NOT NULL DEFAULT 0,
  `w_num` decimal(10,0) NOT NULL DEFAULT 0,
  `word` varchar(50) DEFAULT NULL,
  `ss_type` char(2) DEFAULT NULL,
  `sense_number` decimal(10,0) NOT NULL DEFAULT 0,
  `tag_count` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `wn_verb_frame`
--

DROP TABLE IF EXISTS `wn_verb_frame`;
CREATE TABLE `wn_verb_frame` (
  `synset_id_1` decimal(10,0) DEFAULT NULL,
  `f_num` decimal(10,0) DEFAULT NULL,
  `w_num` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `wn_verb_group`
--

DROP TABLE IF EXISTS `wn_verb_group`;
CREATE TABLE `wn_verb_group` (
  `synset_id_1` decimal(10,0) DEFAULT NULL,
  `synset_id_2` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_terms`
--
ALTER TABLE `academic_terms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `academic_years`
--
ALTER TABLE `academic_years`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `assignment_id` (`item_id`);

--
-- Indexes for table `assignments_answers`
--
ALTER TABLE `assignments_answers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `assignments_questions`
--
ALTER TABLE `assignments_questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `assignments_submitted`
--
ALTER TABLE `assignments_submitted`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blood_groups`
--
ALTER TABLE `blood_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `books_borrowed`
--
ALTER TABLE `books_borrowed`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `books_borrowed_details`
--
ALTER TABLE `books_borrowed_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `books_stock`
--
ALTER TABLE `books_stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_books_books_id_foreign` (`books_id`);

--
-- Indexes for table `books_type`
--
ALTER TABLE `books_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `classes_rooms`
--
ALTER TABLE `classes_rooms`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `clients_accounts`
--
ALTER TABLE `clients_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `country`
--
ALTER TABLE `country`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses_plan`
--
ALTER TABLE `courses_plan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses_resource_links`
--
ALTER TABLE `courses_resource_links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cron_scheduler`
--
ALTER TABLE `cron_scheduler`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `currency`
--
ALTER TABLE `currency`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events_types`
--
ALTER TABLE `events_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `e_learning`
--
ALTER TABLE `e_learning`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `e_learning_comments`
--
ALTER TABLE `e_learning_comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `e_learning_timer`
--
ALTER TABLE `e_learning_timer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `e_learning_views`
--
ALTER TABLE `e_learning_views`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fees_allocations`
--
ALTER TABLE `fees_allocations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fees_category`
--
ALTER TABLE `fees_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fees_collection`
--
ALTER TABLE `fees_collection`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fees_collection_banks`
--
ALTER TABLE `fees_collection_banks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fees_payments`
--
ALTER TABLE `fees_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `files_attachment`
--
ALTER TABLE `files_attachment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `grading_system`
--
ALTER TABLE `grading_system`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `grading_terminal_logs`
--
ALTER TABLE `grading_terminal_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `grading_terminal_scores`
--
ALTER TABLE `grading_terminal_scores`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guardian_relation`
--
ALTER TABLE `guardian_relation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `incidents`
--
ALTER TABLE `incidents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payslips`
--
ALTER TABLE `payslips`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payslips_allowance_types`
--
ALTER TABLE `payslips_allowance_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payslips_details`
--
ALTER TABLE `payslips_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payslips_employees_allowances`
--
ALTER TABLE `payslips_employees_allowances`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payslips_employees_payroll`
--
ALTER TABLE `payslips_employees_payroll`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `periods`
--
ALTER TABLE `periods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `timetables`
--
ALTER TABLE `timetables`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `timetables_slots_allocation`
--
ALTER TABLE `timetables_slots_allocation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `item_id` (`item_id`) USING BTREE;

--
-- Indexes for table `users_access_attempt`
--
ALTER TABLE `users_access_attempt`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_activity_logs`
--
ALTER TABLE `users_activity_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_api_endpoints`
--
ALTER TABLE `users_api_endpoints`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_api_keys`
--
ALTER TABLE `users_api_keys`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_api_queries`
--
ALTER TABLE `users_api_queries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_api_requests`
--
ALTER TABLE `users_api_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_attendance_log`
--
ALTER TABLE `users_attendance_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_chat`
--
ALTER TABLE `users_chat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_emails`
--
ALTER TABLE `users_emails`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_feedback`
--
ALTER TABLE `users_feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_gender`
--
ALTER TABLE `users_gender`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_login_history`
--
ALTER TABLE `users_login_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_messaging_list`
--
ALTER TABLE `users_messaging_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_notification`
--
ALTER TABLE `users_notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_notification_types`
--
ALTER TABLE `users_notification_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_payments`
--
ALTER TABLE `users_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_posts`
--
ALTER TABLE `users_posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_reset_request`
--
ALTER TABLE `users_reset_request`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_roles`
--
ALTER TABLE `users_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_temp_forms`
--
ALTER TABLE `users_temp_forms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_types`
--
ALTER TABLE `users_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wn_antonym`
--
ALTER TABLE `wn_antonym`
  ADD KEY `synset_id_1` (`synset_id_1`),
  ADD KEY `synset_id_2` (`synset_id_2`),
  ADD KEY `wnum_1` (`wnum_1`),
  ADD KEY `wnum_2` (`wnum_2`);

--
-- Indexes for table `wn_attr_adj_noun`
--
ALTER TABLE `wn_attr_adj_noun`
  ADD KEY `synset_id_1` (`synset_id_1`),
  ADD KEY `synset_id_2` (`synset_id_2`);

--
-- Indexes for table `wn_cause`
--
ALTER TABLE `wn_cause`
  ADD KEY `synset_id_1` (`synset_id_1`),
  ADD KEY `synset_id_2` (`synset_id_2`);

--
-- Indexes for table `wn_class_member`
--
ALTER TABLE `wn_class_member`
  ADD KEY `synset_id_1` (`synset_id_1`),
  ADD KEY `synset_id_2` (`synset_id_2`);

--
-- Indexes for table `wn_derived`
--
ALTER TABLE `wn_derived`
  ADD KEY `synset_id_1` (`synset_id_1`),
  ADD KEY `synset_id_2` (`synset_id_2`),
  ADD KEY `wnum_1` (`wnum_1`),
  ADD KEY `wnum_2` (`wnum_2`);

--
-- Indexes for table `wn_entails`
--
ALTER TABLE `wn_entails`
  ADD KEY `synset_id_1` (`synset_id_1`),
  ADD KEY `synset_id_2` (`synset_id_2`);

--
-- Indexes for table `wn_gloss`
--
ALTER TABLE `wn_gloss`
  ADD PRIMARY KEY (`synset_id`);
ALTER TABLE `wn_gloss` ADD FULLTEXT KEY `gloss` (`gloss`);

--
-- Indexes for table `wn_hypernym`
--
ALTER TABLE `wn_hypernym`
  ADD KEY `synset_id_1` (`synset_id_1`),
  ADD KEY `synset_id_2` (`synset_id_2`);

--
-- Indexes for table `wn_hyponym`
--
ALTER TABLE `wn_hyponym`
  ADD KEY `synset_id_1` (`synset_id_1`),
  ADD KEY `synset_id_2` (`synset_id_2`);

--
-- Indexes for table `wn_mbr_meronym`
--
ALTER TABLE `wn_mbr_meronym`
  ADD KEY `synset_id_1` (`synset_id_1`),
  ADD KEY `synset_id_2` (`synset_id_2`);

--
-- Indexes for table `wn_participle`
--
ALTER TABLE `wn_participle`
  ADD KEY `synset_id_1` (`synset_id_1`),
  ADD KEY `synset_id_2` (`synset_id_2`),
  ADD KEY `wnum_1` (`wnum_1`),
  ADD KEY `wnum_2` (`wnum_2`);

--
-- Indexes for table `wn_part_meronym`
--
ALTER TABLE `wn_part_meronym`
  ADD KEY `synset_id_1` (`synset_id_1`),
  ADD KEY `synset_id_2` (`synset_id_2`);

--
-- Indexes for table `wn_pertainym`
--
ALTER TABLE `wn_pertainym`
  ADD KEY `synset_id_1` (`synset_id_1`),
  ADD KEY `synset_id_2` (`synset_id_2`),
  ADD KEY `wnum_1` (`wnum_1`),
  ADD KEY `wnum_2` (`wnum_2`);

--
-- Indexes for table `wn_see_also`
--
ALTER TABLE `wn_see_also`
  ADD KEY `synset_id_1` (`synset_id_1`),
  ADD KEY `synset_id_2` (`synset_id_2`),
  ADD KEY `wnum_1` (`wnum_1`),
  ADD KEY `wnum_2` (`wnum_2`);

--
-- Indexes for table `wn_similar`
--
ALTER TABLE `wn_similar`
  ADD KEY `synset_id_1` (`synset_id_1`),
  ADD KEY `synset_id_2` (`synset_id_2`);

--
-- Indexes for table `wn_subst_meronym`
--
ALTER TABLE `wn_subst_meronym`
  ADD KEY `synset_id_1` (`synset_id_1`),
  ADD KEY `synset_id_2` (`synset_id_2`);

--
-- Indexes for table `wn_synset`
--
ALTER TABLE `wn_synset`
  ADD PRIMARY KEY (`synset_id`,`w_num`),
  ADD KEY `synset_id` (`synset_id`),
  ADD KEY `w_num` (`w_num`),
  ADD KEY `word` (`word`);

--
-- Indexes for table `wn_verb_frame`
--
ALTER TABLE `wn_verb_frame`
  ADD KEY `synset_id_1` (`synset_id_1`),
  ADD KEY `f_num` (`f_num`),
  ADD KEY `w_num` (`w_num`);

--
-- Indexes for table `wn_verb_group`
--
ALTER TABLE `wn_verb_group`
  ADD KEY `synset_id_1` (`synset_id_1`),
  ADD KEY `synset_id_2` (`synset_id_2`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_terms`
--
ALTER TABLE `academic_terms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `academic_years`
--
ALTER TABLE `academic_years`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assignments_answers`
--
ALTER TABLE `assignments_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assignments_questions`
--
ALTER TABLE `assignments_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assignments_submitted`
--
ALTER TABLE `assignments_submitted`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blood_groups`
--
ALTER TABLE `blood_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `books_borrowed`
--
ALTER TABLE `books_borrowed`
  MODIFY `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `books_borrowed_details`
--
ALTER TABLE `books_borrowed_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `books_stock`
--
ALTER TABLE `books_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `books_type`
--
ALTER TABLE `books_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `clients_accounts`
--
ALTER TABLE `clients_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `country`
--
ALTER TABLE `country`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=250;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `courses_plan`
--
ALTER TABLE `courses_plan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courses_resource_links`
--
ALTER TABLE `courses_resource_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cron_scheduler`
--
ALTER TABLE `cron_scheduler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `currency`
--
ALTER TABLE `currency`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `events_types`
--
ALTER TABLE `events_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `e_learning`
--
ALTER TABLE `e_learning`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `e_learning_comments`
--
ALTER TABLE `e_learning_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `e_learning_timer`
--
ALTER TABLE `e_learning_timer`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `e_learning_views`
--
ALTER TABLE `e_learning_views`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fees_allocations`
--
ALTER TABLE `fees_allocations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `fees_category`
--
ALTER TABLE `fees_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `fees_collection`
--
ALTER TABLE `fees_collection`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `fees_collection_banks`
--
ALTER TABLE `fees_collection_banks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `fees_payments`
--
ALTER TABLE `fees_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `files_attachment`
--
ALTER TABLE `files_attachment`
  MODIFY `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grading_system`
--
ALTER TABLE `grading_system`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `grading_terminal_logs`
--
ALTER TABLE `grading_terminal_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `grading_terminal_scores`
--
ALTER TABLE `grading_terminal_scores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `guardian_relation`
--
ALTER TABLE `guardian_relation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `incidents`
--
ALTER TABLE `incidents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payslips`
--
ALTER TABLE `payslips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payslips_allowance_types`
--
ALTER TABLE `payslips_allowance_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payslips_details`
--
ALTER TABLE `payslips_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payslips_employees_allowances`
--
ALTER TABLE `payslips_employees_allowances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payslips_employees_payroll`
--
ALTER TABLE `payslips_employees_payroll`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `periods`
--
ALTER TABLE `periods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `timetables_slots_allocation`
--
ALTER TABLE `timetables_slots_allocation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users_access_attempt`
--
ALTER TABLE `users_access_attempt`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users_activity_logs`
--
ALTER TABLE `users_activity_logs`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=294;

--
-- AUTO_INCREMENT for table `users_api_endpoints`
--
ALTER TABLE `users_api_endpoints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=180;

--
-- AUTO_INCREMENT for table `users_api_keys`
--
ALTER TABLE `users_api_keys`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users_api_queries`
--
ALTER TABLE `users_api_queries`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_api_requests`
--
ALTER TABLE `users_api_requests`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_attendance_log`
--
ALTER TABLE `users_attendance_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `users_chat`
--
ALTER TABLE `users_chat`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `users_emails`
--
ALTER TABLE `users_emails`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_feedback`
--
ALTER TABLE `users_feedback`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_gender`
--
ALTER TABLE `users_gender`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users_login_history`
--
ALTER TABLE `users_login_history`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `users_messaging_list`
--
ALTER TABLE `users_messaging_list`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users_notification`
--
ALTER TABLE `users_notification`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users_notification_types`
--
ALTER TABLE `users_notification_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users_payments`
--
ALTER TABLE `users_payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_posts`
--
ALTER TABLE `users_posts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_reset_request`
--
ALTER TABLE `users_reset_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_roles`
--
ALTER TABLE `users_roles`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users_temp_forms`
--
ALTER TABLE `users_temp_forms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_types`
--
ALTER TABLE `users_types`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
