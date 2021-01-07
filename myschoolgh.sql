-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 07, 2021 at 05:07 PM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.3.18

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
-- Table structure for table `announcements`
--

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

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `item_id`, `client_id`, `user_id`, `user_type`, `section`, `recipient_group`, `persistent`, `priority`, `modal_function`, `subject`, `message`, `content`, `seen_by`, `start_date`, `end_date`, `status`, `replies_count`, `date_created`, `last_updated_by`, `last_updated_date`) VALUES
(1, 'aL1vqY0mMxndUecfZkFGlijV8yIwugrt', 'LKJAFD94R', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'insurance_company', 'dashboard,index', 'broker', '0', 'high', 'ajaxNotice_aL1vqY0mMxndUe', 'Test announcement sharing', '<div><!--block-->This is a test announcement that is been shared with all insurance brokers. I perceive it will be great enough to have their view on the new changes that have come up during the last few days with respect to the new normal and the fact that employees have had their work schedules changed over time.</div>', '\r\n        <div class=\"modal announcementModal_aL1vqY0mMxndUecfZkFGlijV8yIwugrt fade\" data-backdrop=\"static\" data-keyboard=\"false\" id=\"announcementModal_aL1vqY0mMxndUecfZkFGlijV8yIwugrt\" data-announcement-id=\"aL1vqY0mMxndUecfZkFGlijV8yIwugrt\" tabindex=\"-1\" role=\"dialog\" aria-hidden=\"true\">\r\n            <div class=\"modal-dialog\" role=\"document\" style=\"min-width:300px; bottom: 0px\">\r\n                <div class=\"modal-content\">\r\n                    <div class=\"modal-header\">\r\n                        <h4><small class=\"tx-11\"><i class=\"fas fa-info\"></i></small> Test announcement sharing</h4>\r\n                    </div>\r\n                    <div class=\"modal-body\">\r\n                        <div class=\"notice\">\r\n                            <div><!--block-->This is a test announcement that is been shared with all insurance brokers. I perceive it will be great enough to have their view on the new changes that have come up during the last few days with respect to the new normal and the fact that employees have had their work schedules changed over time.</div>\r\n                        </div>\r\n                    </div>\r\n                    <div class=\"modal-footer text-right\">\r\n                        <button data-announcement-id=\"aL1vqY0mMxndUecfZkFGlijV8yIwugrt\" type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Close</button>\r\n                    </div>\r\n                </div>\r\n            </div>\r\n        </div>', '[\"NULL\",\"BZ7ScjiWnKD0YbwONqleTIUvy3PksGzQ\"]', '2020-10-16 00:00:00', '2020-10-19 23:59:00', '1', 0, '2020-10-13 06:45:21', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-14 09:27:34'),
(5, 'EN2SQ5tMZvAzqg4aFiPhX8cWHseGuyjw', 'LKJAFD94R', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'insurance_company', 'dashboard,index', 'agent', '0', 'medium', 'ajaxNotice_EN2SQ5tMZvAzqg', 'Another test annoucement', '<div><!--block-->This is a test announcement that is been shared with all insurance brokers. I perceive it will be great enough to have their view on the new changes that have come up during the last few days with respect to the new normal and the fact that employees have had their work schedules changed over time.</div>', '<div data-backdrop=\"static\" data-keyboard=\"false\" class=\"modal announcementModal_EN2SQ5tMZvAzqg4aFiPhX8cWHseGuyjw fade\" id=\"announcementModal_EN2SQ5tMZvAzqg4aFiPhX8cWHseGuyjw\" data-announcement-id=\"EN2SQ5tMZvAzqg4aFiPhX8cWHseGuyjw\" tabindex=\"-1\" role=\"dialog\" aria-hidden=\"true\">\r\n                <div class=\"modal-dialog\" role=\"document\" style=\"min-width:300px; bottom: 0px\">\r\n                    <div class=\"modal-content\">\r\n                    <div class=\"modal-body\">\r\n                        \r\n                        <div class=\"icon\">\r\n                            </div>\r\n                            <div class=\"notice\">\r\n                            <h4><small class=\"tx-11\"><i class=\"fas fa-info\"></i></small> Another test annoucement</h4>\r\n                            <div><!--block-->This is a test announcement that is been shared with all insurance brokers. I perceive it will be great enough to have their view on the new changes that have come up during the last few days with respect to the new normal and the fact that employees have had their work schedules changed over time.</div>\r\n                        </div>\r\n                    </div>\r\n                    <div class=\"modal-footer text-right\">\r\n                        <button data-announcement-id=\"EN2SQ5tMZvAzqg4aFiPhX8cWHseGuyjw\" type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Close</button>\r\n                    </div>\r\n                    </div>\r\n                </div>\r\n            </div>', '[\"NULL\",\"sgHvi29tuJakdfzmp71nowNlWr40BKDV\"]', '2020-10-13 06:00:00', '2020-10-19 11:59:00', '1', 0, '2020-10-13 10:58:52', NULL, '2020-10-14 09:12:16'),
(9, 'HU72tZ3lysP0Ag9Y5ehRqO4uCzMmvocp', 'LKJAFD94R', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'insurance_company', 'dashboard,index', 'client', '0', 'medium', 'ajaxNotice_HU72tZ3lysP0Ag9Y5ehRqO4uCzMmvocp', 'Test cron job activity', '<div><!--block-->This is the activity that i am sending the message</div>', '<div class=\"modal announcementModal_HU72tZ3lysP0Ag9Y5ehRqO4uCzMmvocp fade\" data-backdrop=\"static\" data-keyboard=\"false\" id=\"announcementModal_HU72tZ3lysP0Ag9Y5ehRqO4uCzMmvocp\" data-announcement-id=\"HU72tZ3lysP0Ag9Y5ehRqO4uCzMmvocp\" tabindex=\"-1\" role=\"dialog\" aria-hidden=\"true\">\r\n                <div class=\"modal-dialog\" role=\"document\" style=\"min-width:300px; bottom: 0px\">\r\n                    <div class=\"modal-content\">\r\n                        <div class=\"modal-header\">\r\n                            <h4><small class=\"tx-11\"><i class=\"fas fa-info\"></i></small> Test cron job activity</h4>\r\n                        </div>\r\n                        <div class=\"modal-body\">\r\n                            <div class=\"notice\">\r\n                                <div><!--block-->This is the activity that i am sending the message</div>\r\n                            </div>\r\n                        </div>\r\n                        <div class=\"modal-footer text-right\">\r\n                            <button data-announcement-id=\"HU72tZ3lysP0Ag9Y5ehRqO4uCzMmvocp\" type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Close</button>\r\n                        </div>\r\n                    </div>\r\n                </div>\r\n            </div>', '[\"NULL\",\"F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ\"]', '2020-10-13 06:00:00', '2020-10-19 11:59:00', '1', 0, '2020-10-13 15:24:37', NULL, '2020-10-14 09:12:16');

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` int(11) NOT NULL,
  `client_id` varchar(32) CHARACTER SET latin1 DEFAULT '1',
  `type` enum('Test','Assignment','Quiz','Exam','Group') COLLATE utf8_unicode_ci DEFAULT 'Assignment',
  `assignment_type` enum('file_attachment','multiple_choice') COLLATE utf8_unicode_ci DEFAULT 'file_attachment',
  `assigned_to` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `assigned_to_list` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `item_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `course_tutor` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `course_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `class_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `grading` int(11) DEFAULT 0,
  `assignment_title` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `assignment_description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_created` datetime DEFAULT current_timestamp(),
  `created_by` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `due_time` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` enum('Draft','Pending','Graded','Cancelled','Closed') COLLATE utf8_unicode_ci DEFAULT 'Pending',
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

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`id`, `client_id`, `type`, `assignment_type`, `assigned_to`, `assigned_to_list`, `item_id`, `course_tutor`, `course_id`, `class_id`, `grading`, `assignment_title`, `assignment_description`, `date_created`, `created_by`, `due_date`, `due_time`, `state`, `date_closed`, `date_updated`, `date_published`, `status`, `deleted`, `academic_year`, `academic_term`, `replies_count`, `comments_count`) VALUES
(1, 'LKJAFD94R', 'Assignment', 'file_attachment', 'selected_students', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo,ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v', 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '2', '2', 30, 'Test Assignment - The Art of Infusion', '&lt;div&gt;&lt;!--block--&gt;YOMAI-OUTREACH seeks to achieve this goal by first reconciling the youth to Christ, bringing great Ministers of God on board to Disciple, Mentor and Train the youth as ways of equipping them for The Kingdom Business and its Advancement. We wish to bridge the gap between the youth and older Christian generation by creating an environment where there would constant and consistent interactions through diverse mediums. By so doing, the Ministers would raise young ministers who are accountable, responsible, vibrant, anointed and God-fearing ministers of God now and in near the future&nbsp;&lt;/div&gt;', '2020-12-21 08:22:49', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-25', '09:00', 'Closed', '2020-12-22 15:57:58', '2020-12-22 15:50:31', NULL, '1', '0', '2019/2020', '1st', '0', '5'),
(2, 'LKJAFD94R', 'Assignment', 'multiple_choice', 'all_students', NULL, 'yGBuEJwioD2PZKanN7rzLpI6C3SR9TQF', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '2', '2', 20, 'A Quiz Like Assignment', '&lt;div&gt;&lt;!--block--&gt;We have audited the accompanying statements of financial position of Most Rev. Prof. Kwasi A Dickson Memorial Methodist Church as at 30thNovember, 2020 and the related statements of activities, for the period. These financial statements are the responsibility of the steward in charge of Finance. Our responsibility is to express an opinion on these financial statements based on our audit.&nbsp;&lt;/div&gt;', '2020-12-23 08:13:39', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '2020-12-30', '07:00', 'Pending', NULL, '2020-12-23 22:24:05', '2020-12-23 23:09:06', '1', '0', '2019/2020', '1st', '0', '1');

-- --------------------------------------------------------

--
-- Table structure for table `assignments_answers`
--

CREATE TABLE `assignments_answers` (
  `id` int(11) NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `assignment_id` varchar(32) DEFAULT NULL,
  `student_id` varchar(32) DEFAULT NULL,
  `answers` text DEFAULT NULL,
  `scores` varchar(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `assignments_answers`
--

INSERT INTO `assignments_answers` (`id`, `client_id`, `assignment_id`, `student_id`, `answers`, `scores`) VALUES
(1, 'LKJAFD94R', 'yGBuEJwioD2PZKanN7rzLpI6C3SR9TQF', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', '[{\"question_id\":\"kFvep4YJV7syUGQONBWn0cLPhXbH2jf8\",\"answer\":\"option_b\",\"assigned_mark\":\"5\",\"data_answered\":\"2020-12-28 01:01PM\",\"status\":\"correct\"},{\"question_id\":\"qh7EokbU25F0sT9pBPnQVH6LJAIZitDX\",\"answer\":\"option_b\",\"assigned_mark\":\"5\",\"data_answered\":\"2020-12-28 01:01PM\",\"status\":\"wrong\"},{\"question_id\":\"bDTclPoE12dXKvh7Nkx3IrHGLmta8Jnu\",\"answer\":\"option_b\",\"assigned_mark\":\"5\",\"data_answered\":\"2020-12-28 01:01PM\",\"status\":\"correct\"},{\"question_id\":\"V0BE5Ai2gGxrHXDf7qwncyRb4oNFa9CI\",\"answer\":\"option_d\",\"assigned_mark\":\"5\",\"data_answered\":\"2020-12-28 12:05PM\",\"status\":\"wrong\"}]', '10');

-- --------------------------------------------------------

--
-- Table structure for table `assignments_questions`
--

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

--
-- Dumping data for table `assignments_questions`
--

INSERT INTO `assignments_questions` (`id`, `client_id`, `item_id`, `assignment_id`, `question`, `difficulty`, `option_a`, `option_b`, `option_c`, `option_d`, `option_e`, `answer_type`, `created_by`, `correct_answer`, `marks`, `correct_answer_description`, `attempted_by`, `current_state`, `date_created`, `deleted`) VALUES
(1, 'LKJAFD94R', 'kFvep4YJV7syUGQONBWn0cLPhXbH2jf8', 'yGBuEJwioD2PZKanN7rzLpI6C3SR9TQF', 'This is the first test question that i am posting under this assignment', 'easy', 'First Answer', 'The next answer', 'This is also another answer', 'Great work there', 'Awesome work here', 'option', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'option_b', '5', NULL, NULL, 'Published', '2020-12-23 15:45:07', '0'),
(2, 'LKJAFD94R', 'qh7EokbU25F0sT9pBPnQVH6LJAIZitDX', 'yGBuEJwioD2PZKanN7rzLpI6C3SR9TQF', 'New Next Question Addition updated information here and there and new one here', 'easy', 'Fist item set', 'Second item', 'third item here', 'fourth item here', 'final item here', 'option', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'option_c', '5', NULL, NULL, 'Published', '2020-12-23 16:10:12', '0'),
(3, 'LKJAFD94R', 'bDTclPoE12dXKvh7Nkx3IrHGLmta8Jnu', 'yGBuEJwioD2PZKanN7rzLpI6C3SR9TQF', 'Final Test of Adding a question updated information here and there. Another update to this question', 'medium', 'This is necessary', 'that is another answer', 'third answer to add', 'another answer to send', 'final answer to push', 'option', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'option_b', '5', NULL, NULL, 'Published', '2020-12-23 16:18:06', '0'),
(4, 'LKJAFD94R', 'V0BE5Ai2gGxrHXDf7qwncyRb4oNFa9CI', 'yGBuEJwioD2PZKanN7rzLpI6C3SR9TQF', 'We conducted the audit in accordance with generally accepted auditing standards as well as standards accepted by the Methodist Church Ghana. Great updated information', 'easy', 'These standards require that', 'Our audit also includes assessing', 'if the accounting principles applied', 'in the financial statements is in conformity', 'to those prescribed by the Methodist Church Ghana.', 'option', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'option_a', '5', NULL, NULL, 'Published', '2020-12-23 20:36:39', '0');

-- --------------------------------------------------------

--
-- Table structure for table `assignments_submitted`
--

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

--
-- Dumping data for table `assignments_submitted`
--

INSERT INTO `assignments_submitted` (`id`, `client_id`, `assignment_id`, `student_id`, `score`, `graded`, `handed_in`, `date_submitted`, `date_graded`) VALUES
(1, 'LKJAFD94R', 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', '26', '1', 'Submitted', '2020-12-21 16:01:54', '2020-12-22 06:18:19'),
(2, 'LKJAFD94R', 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl', 'ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v', '26', '1', 'Pending', '2020-12-21 16:01:54', '2020-12-22 06:18:19'),
(7, 'LKJAFD94R', 'yGBuEJwioD2PZKanN7rzLpI6C3SR9TQF', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', '10', '1', 'Submitted', '2020-12-28 13:01:34', '2020-12-28 13:01:34');

-- --------------------------------------------------------

--
-- Table structure for table `blood_groups`
--

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

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `item_id`, `client_id`, `code`, `isbn`, `title`, `book_image`, `description`, `author`, `quantity`, `rack_no`, `row_no`, `category_id`, `desc`, `department_id`, `deleted_at`, `status`, `deleted`, `class_id`, `created_by`, `date_added`) VALUES
(1, '12223444443', 'LKJAFD94R', '01201', 'FAGSR45454', 'The products of JavaScript Info', 'assets/img/library/0RohHDm6af__social-media-analytics.png', 'This is the book for the day. I have', 'Henry Asmah', 100, '12', 'FDD', '1', 'This is the book for the day. I have', '1', NULL, '1', '0', '2', 'uIkajsw123456789064hxk1fc3efmnva', '2019-11-07'),
(2, 'afdafdafd343434', 'LKJAFD94R', '01203', 'FAGSR45454DDG', 'Principles of OOP', 'assets/img/library/U6KqIluVsw__executive-dashboard-100768427-large.jpg', 'This is the way to go', 'Emmanuel Obeng', 102, 'EKAL10', '13', '2', 'This is the way to go', '2', NULL, '1', '0', '2', 'uIkajsw123456789064hxk1fc3efmnva', '2019-11-07'),
(4, 'afdghhghghg', 'LKJAFD94R', NULL, 'HAI012152102', 'Update this book for me', 'assets/img/library/I0vGNaxPKM__image_720 (1).png', 'This is the book that i want to insert into the database system.', 'Cecilia Boateng', 58, 'A120', 'ADF', '1', 'This is the book that i want to insert into the database system.', '3', NULL, '1', '0', '2', 'uIkajsw123456789064hxk1fc3efmnva', '2019-11-16'),
(5, 'lkjlajfdk454545k', 'LKJAFD94R', NULL, 'IBSLAFKLDF343', 'book title', 'assets/img/library/4U7NdbZSx5__best-online-hospital-management-system-500x500.jpg', 'this is a test insertion of a book into the database', 'hello world and here', 43, '884ddD', '45', '2', NULL, '1', NULL, '1', '0', '2', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-02');

-- --------------------------------------------------------

--
-- Table structure for table `books_borrowed`
--

CREATE TABLE `books_borrowed` (
  `id` int(12) UNSIGNED NOT NULL,
  `client_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `the_type` enum('issued','requested') COLLATE utf8_unicode_ci DEFAULT 'issued',
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

--
-- Dumping data for table `books_borrowed`
--

INSERT INTO `books_borrowed` (`id`, `client_id`, `the_type`, `item_id`, `user_id`, `user_role`, `books_id`, `issued_date`, `return_date`, `fine`, `actual_paid`, `fine_paid`, `status`, `created_at`, `issued_by`, `actual_date_returned`, `updated_at`, `deleted`) VALUES
(1, 'LKJAFD94R', 'issued', 'iaym012xVA7YSHq8FgOr6DszpnQkWhlo', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 'student', '[\"afdghhghghg\",12223444443]', '2021-01-05', '2021-01-12', '34.00', '0.00', '0', 'Issued', '2021-01-05 19:00:27', 'uIkajsw123456789064hxk1fc', NULL, '2021-01-05 19:00:27', '0'),
(2, 'LKJAFD94R', 'issued', '0QJ4OYwMPFGfED7laTdAZxzUjCR6v29u', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'teacher', '[\"lkjlajfdk454545k\",\"afdafdafd343434\",\"afdghhghghg\",12223444443]', '2021-01-05', '2021-01-12', '45.00', '0.00', '0', 'Issued', '2021-01-05 19:04:07', 'uIkajsw123456789064hxk1fc', NULL, '2021-01-05 19:04:07', '0'),
(4, 'LKJAFD94R', 'requested', 'jJnVM2SimOsZDFla4UAkfHg0cyRwuQ9x', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'teacher', '[\"afdghhghghg\",12223444443]', NULL, '2021-01-12', '35', '0.00', '0', 'Approved', '2021-01-05 22:28:39', 'a6ImKRhGstOi8vMW0zQ2A57nq', NULL, '2021-01-05 22:28:39', '0');

-- --------------------------------------------------------

--
-- Table structure for table `books_borrowed_details`
--

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

--
-- Dumping data for table `books_borrowed_details`
--

INSERT INTO `books_borrowed_details` (`id`, `borrowed_id`, `book_id`, `date_borrowed`, `return_date`, `quantity`, `fine`, `actual_paid`, `fine_paid`, `issued_by`, `received_by`, `actual_date_returned`, `status`, `deleted`) VALUES
(1, 'iaym012xVA7YSHq8FgOr6DszpnQkWhlo', 'afdghhghghg', '2021-01-05 19:00:27', '2021-01-12', 1, '17.00', '0.00', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', NULL, 'Borrowed', '0'),
(2, 'iaym012xVA7YSHq8FgOr6DszpnQkWhlo', '12223444443', '2021-01-05 19:00:27', '2021-01-12', 1, '17.00', '0.00', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', NULL, 'Borrowed', '0'),
(3, '0QJ4OYwMPFGfED7laTdAZxzUjCR6v29u', 'lkjlajfdk454545k', '2021-01-05 19:04:07', '2021-01-12', 4, '11.25', '0.00', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', NULL, 'Borrowed', '0'),
(4, '0QJ4OYwMPFGfED7laTdAZxzUjCR6v29u', 'afdafdafd343434', '2021-01-05 19:04:07', '2021-01-12', 3, '11.25', '0.00', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', NULL, 'Borrowed', '0'),
(5, '0QJ4OYwMPFGfED7laTdAZxzUjCR6v29u', 'afdghhghghg', '2021-01-05 19:04:07', '2021-01-12', 3, '11.25', '0.00', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', NULL, 'Borrowed', '0'),
(6, '0QJ4OYwMPFGfED7laTdAZxzUjCR6v29u', '12223444443', '2021-01-05 19:04:07', '2021-01-12', 3, '11.25', '0.00', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', NULL, 'Borrowed', '0'),
(9, 'jJnVM2SimOsZDFla4UAkfHg0cyRwuQ9x', 'afdghhghghg', '2021-01-05 22:28:39', '2021-01-12', 4, '0.00', '0.00', '0', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', NULL, 'Borrowed', '0'),
(10, 'jJnVM2SimOsZDFla4UAkfHg0cyRwuQ9x', '12223444443', '2021-01-05 22:28:39', '2021-01-12', 3, '0.00', '0.00', '0', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', NULL, 'Borrowed', '0');

-- --------------------------------------------------------

--
-- Table structure for table `books_stock`
--

CREATE TABLE `books_stock` (
  `id` int(11) NOT NULL,
  `books_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `quantity` int(10) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `books_stock`
--

INSERT INTO `books_stock` (`id`, `books_id`, `quantity`) VALUES
(1, '12223444443', 93),
(2, 'afdafdafd343434', 99),
(3, 'afdghhghghg', 46),
(4, 'lkjlajfdk454545k', 44);

-- --------------------------------------------------------

--
-- Table structure for table `books_type`
--

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

--
-- Dumping data for table `books_type`
--

INSERT INTO `books_type` (`id`, `item_id`, `client_id`, `department_id`, `name`, `description`, `status`, `created_by`, `date_created`) VALUES
(1, '45df45454gfg', 'LKJAFD94R', '2', 'Academic', NULL, '1', NULL, '2021-01-03 22:05:17'),
(2, 'afdafdafd3434', 'LKJAFD94R', '3', 'Story', 'The Society has put in place a Covid 19 team comprising health professionals and some leaders to ensure that all the necessary safety protocols are in place to guarantee the safety of members who attend church service.', '1', NULL, '2021-01-03 22:05:17'),
(4, 'fgfg56565afd', 'LKJAFD94R', NULL, 'Other Hello Name', NULL, '1', NULL, '2021-01-03 22:05:17'),
(5, 'gfsgfytrytadfd', 'LKJAFD94R', NULL, 'Computer Programming', NULL, '1', NULL, '2021-01-03 22:05:17'),
(6, 'afdfdfrtrtr5656', 'LKJAFD94R', '7', 'ICT For Schools', NULL, '1', NULL, '2021-01-03 22:05:17'),
(9, 'afdad676734', 'LKJAFD94R', '8', 'Adding new book', NULL, '1', NULL, '2021-01-03 22:05:17');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `client_id` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `class_code` varchar(32) DEFAULT NULL,
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

INSERT INTO `classes` (`id`, `item_id`, `client_id`, `name`, `class_code`, `department_id`, `academic_year`, `academic_term`, `class_teacher`, `class_assistant`, `status`, `created_by`, `description`, `date_created`, `date_updated`) VALUES
(1, 'fadaf', 'LKJAFD94R', 'GENERAL ARTS 1', 'CL00020', '2', '', NULL, 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', '1', NULL, NULL, '2020-11-27 21:49:50', '2020-11-30 11:54:53'),
(2, 'faflkdjaflkdjafd', 'LKJAFD94R', 'GENERAL ARTS 2', 'GFCKJH', '2', '', NULL, 'null', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', '1', NULL, NULL, '2020-11-27 21:49:50', '2020-12-11 22:06:14'),
(3, 'erere454545', 'LKJAFD94R', 'SCIENCE 3', NULL, '1', '', NULL, NULL, NULL, '0', NULL, NULL, '2020-11-27 21:49:50', '2020-11-28 09:33:29'),
(4, 'afdaflkjdflajkfd', 'LKJAFD94R', 'VISUAL ARTS D', NULL, '3', '', NULL, NULL, NULL, '1', NULL, NULL, '2020-11-27 21:49:50', '2020-11-28 09:33:29'),
(5, 'afdfdf45454', 'LKJAFD94R', '2 HC 2', 'TESTCODE', '2', '', NULL, 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', '1', NULL, 'this is the class information that i am updating here... indeed it will be great', '2020-11-27 21:49:50', '2020-11-28 09:39:16'),
(7, NULL, 'LKJAFD94R', 'CLASS 1', NULL, '0', '', NULL, '', NULL, '1', NULL, NULL, '2020-11-27 21:49:50', '2020-11-28 09:33:29'),
(8, NULL, 'LKJAFD94R', 'CLASS 2', 'CL00008', '5', '', NULL, 'null', 'null', '1', NULL, NULL, '2020-11-27 21:49:50', '2020-11-30 11:56:47'),
(9, NULL, 'LKJAFD94R', 'CLASS 3', NULL, '0', '', NULL, '', NULL, '1', NULL, NULL, '2020-11-27 21:49:50', '2020-11-28 09:33:29'),
(10, NULL, 'LKJAFD94R', 'CLASS 4', NULL, '0', '', NULL, '', NULL, '1', NULL, NULL, '2020-11-27 21:49:50', '2020-11-28 09:33:29'),
(11, NULL, 'LKJAFD94R', 'CLASS 5', 'CL00020', 'null', '', NULL, 'null', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', '1', NULL, NULL, '2020-11-27 21:49:50', '2020-11-30 11:55:12'),
(12, 'fdfdfadad545454', 'LKJAFD94R', 'CLASS 6', NULL, '0', '', NULL, '', NULL, '1', NULL, NULL, '2020-11-27 21:49:50', '2020-11-28 09:33:29'),
(13, 'afdfadfdjhj78787', 'LKJAFD94R', 'JHS 1', 'CL00013', 'null', '', NULL, 'null', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', '1', NULL, NULL, '2020-11-27 21:49:50', '2020-11-30 11:57:00'),
(14, 'fafdf7878787', 'LKJAFD94R', 'JHS 2', NULL, '0', '', NULL, '', NULL, '1', NULL, NULL, '2020-11-27 21:49:50', '2020-11-28 09:33:29'),
(15, 'afdfd2323232', 'LKJAFD94R', 'JHS 3', NULL, '0', '', NULL, '', NULL, '1', NULL, NULL, '2020-11-27 21:49:50', '2020-11-28 09:33:29'),
(16, 'aaaadd444545454', 'LKJAFD94R', 'CLASS 6', NULL, '0', '', NULL, 'OY550107772', NULL, '1', NULL, NULL, '2020-11-27 21:49:50', '2020-11-28 09:33:29'),
(17, 'fdadafda454545', 'LKJAFD94R', 'General Arts 4 (History Option)', NULL, '2', '', NULL, 'OY550107772', NULL, '1', NULL, NULL, '2020-11-27 21:49:50', '2020-11-28 09:33:29'),
(18, 't66554fsdsfd', 'LKJAFD94R', 'Management in Business Models 3', NULL, '5', '', NULL, 'null', 'MSG9862354', '1', NULL, NULL, '2020-11-27 21:49:50', '2020-11-28 09:33:29'),
(19, 'aassghhhklll', 'LKJAFD94R', '1 Home Economics 5', NULL, '4', '', NULL, 'OY550107772', NULL, '1', NULL, NULL, '2020-11-27 21:49:50', '2020-11-28 09:33:29'),
(21, 'afghhhjgfdfdfd', 'LKJAFD94R', 'hello test class insertion', 'CL00019', '2', NULL, '1', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', '1', 'uIkajsw123456789064hxk1fc3efmnva', 'This is the the reason why i want to add a new information here', '2020-11-28 00:40:55', '2020-11-28 09:33:29');

-- --------------------------------------------------------

--
-- Table structure for table `clients_accounts`
--

CREATE TABLE `clients_accounts` (
  `id` int(11) NOT NULL,
  `client_id` varchar(255) DEFAULT NULL,
  `client_name` varchar(255) DEFAULT NULL,
  `client_contact` varchar(255) DEFAULT NULL,
  `client_preferences` varchar(5000) DEFAULT NULL,
  `client_status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `clients_accounts`
--

INSERT INTO `clients_accounts` (`id`, `client_id`, `client_name`, `client_contact`, `client_preferences`, `client_status`) VALUES
(1, 'LKJAFD94R', 'Test Client Account', '0550107770', '{\"labels\":{\"student_label\":\"AGL\",\"class_label\":\"CL\",\"department_label\":\"DEP\",\"section_label\":\"SEC\",\"course_label\":\"COR\",\"staff_label\":\"STF\",\"book\":\"BK\"},\"academics\":{\"academic_year\":\"2019/2020\",\"academic_term\":\"1st\"}}', '1');

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

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

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `client_id` varchar(32) DEFAULT 'LKJAFD94R',
  `course_code` varchar(255) DEFAULT NULL,
  `credit_hours` varchar(25) DEFAULT NULL,
  `academic_term` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
  `academic_year` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
  `programme_id` varchar(32) DEFAULT NULL,
  `class_id` varchar(32) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `course_tutor` varchar(500) DEFAULT NULL COMMENT 'THIS  IS WHERE THE ID OF THE TEACHER OR WHOEVER INSERTED IT WILL APPEAR',
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

INSERT INTO `courses` (`id`, `item_id`, `client_id`, `course_code`, `credit_hours`, `academic_term`, `academic_year`, `programme_id`, `class_id`, `name`, `slug`, `course_tutor`, `description`, `date_created`, `created_by`, `date_updated`, `status`, `deleted`) VALUES
(2, 'afdafdafdafd', 'LKJAFD94R', 'COR00002', '4', '1st', '2019/2020', '3', '2', 'Principles of Arts', 'principles-of-arts', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Updating the course information is really awesome... i am loving this informaton style', NULL, NULL, '2020-11-28 11:06:18', '1', '0'),
(29, '8u20GszACM7TyFNJctYEDgX9rwp6Oe1a', 'LKJAFD94R', 'COR00029', '3', '1st', '2019/2020', NULL, '1', 'The Concept of Reproduction', 'the-concept-of-reproduction', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'This is the course for the concept of reproduction that we want to upload into the system.', '2020-12-22', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-22 09:56:04', '1', '0');

-- --------------------------------------------------------

--
-- Table structure for table `courses_plan`
--

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

--
-- Dumping data for table `courses_plan`
--

INSERT INTO `courses_plan` (`id`, `item_id`, `client_id`, `course_id`, `unit_id`, `name`, `plan_type`, `academic_term`, `academic_year`, `description`, `start_date`, `end_date`, `created_by`, `date_created`, `date_updated`, `status`) VALUES
(1, 'S1fJgDrE6xRFunzhI4BlWLdcTbXmQ3Y2', 'LKJAFD94R', '2', NULL, 'Internet', 'unit', NULL, NULL, '&lt;div&gt;&lt;!--block--&gt;Note: You may not want to share the lesson plan with students and parents until you\'ve added and edited all the necessary information for the plan. In this case, you can edit the lesson plan later and select to share it with parents and students.&lt;br&gt;&lt;br&gt;Note: You may not want to share the lesson plan with students and parents until you\'ve added and edited all the necessary information for the plan. In this case, you can edit the lesson plan later and select to share it with parents and students.&lt;br&gt;&lt;br&gt;Note: You may not want to share the lesson plan with students and parents until you\'ve added and edited all the necessary information for the plan. In this case, you can edit the lesson plan later and select to share it with parents and students.&lt;br&gt;&lt;br&gt;Note: You may not want to share the lesson plan with students and parents until you\'ve added and edited all the necessary information for the plan. In this case, you can edit the lesson plan later and select to share it with parents and students.&lt;/div&gt;', '2020-11-01', '2020-11-28', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-28 23:01:32', '2020-11-30 12:41:31', '1'),
(2, 'iof6CAx2jWwGVgSNqO8mZhvulb1UpHYI', 'LKJAFD94R', '2', NULL, 'Word Processing Applications', 'unit', NULL, NULL, NULL, '2020-11-28', '2020-11-28', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-28 23:01:51', '2020-11-28 23:01:51', '1'),
(3, '7yTwVHjMEQpcungN1lA20KXdB9rixbOf', 'LKJAFD94R', '2', NULL, 'Emails', 'unit', NULL, NULL, NULL, '2020-11-28', '2020-11-28', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-28 23:03:00', '2020-11-28 23:03:00', '1'),
(4, 'yVJ8zdvEFchunDL0Ha5NsmjKT96C1gXq', 'LKJAFD94R', '2', NULL, 'Spreadsheet Applications', 'unit', NULL, NULL, NULL, '2020-11-28', '2020-11-28', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-28 23:03:19', '2020-11-28 23:03:19', '1'),
(5, 'megrn6o2U1HfVhlZRTaMq3xIibjWYd4w', 'LKJAFD94R', '2', '4', 'Definition', 'lesson', NULL, NULL, NULL, '2020-11-28', '2020-11-28', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-28 23:03:45', '2020-11-28 23:03:45', '1'),
(6, 'YlfiBetjG9qg6cQ3XVAbJF2KkOEh8UPC', 'LKJAFD94R', '2', '4', 'Advantages of Spreadsheet Applications', 'lesson', NULL, NULL, NULL, '2020-11-28', '2020-11-28', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-28 23:05:21', '2020-11-28 23:05:21', '1'),
(7, 'SemKVwY89CaHFGnsDfyth1NbOJ2AgxZ4', 'LKJAFD94R', '2', '4', 'Features of a Spreadsheet Application', 'lesson', NULL, NULL, NULL, '2020-11-28', '2020-11-28', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-28 23:05:51', '2020-11-28 23:05:51', '1'),
(8, 'J2XardCMAcmsN8Fku5KpDLTSPyf4RIeb', 'LKJAFD94R', '2', '3', 'Concept of Emails', 'lesson', NULL, NULL, NULL, '2020-11-28', '2020-11-28', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-28 23:06:20', '2020-11-28 23:06:20', '1'),
(9, 'DbdzmTyG2M5vNo0FlWY6HKZ7kXRspJLi', 'LKJAFD94R', '2', '3', 'Importance of Emails', 'lesson', NULL, NULL, NULL, '2020-11-28', '2020-11-28', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-28 23:06:43', '2020-11-28 23:06:43', '1'),
(10, 'kcmlLYTv16BInFHQwW9hejNaUG05pE2y', 'LKJAFD94R', '2', '3', 'Features of an Email Address', 'lesson', NULL, NULL, NULL, '2020-11-28', '2020-11-28', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-28 23:07:00', '2020-11-28 23:07:00', '1'),
(11, 'q87n2QJb9SPuCwyvmMGsrDTNxBfWX43l', 'LKJAFD94R', '2', '2', 'Word processing & a Word Processor', 'lesson', NULL, NULL, NULL, '2020-11-28', '2020-11-28', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-28 23:07:37', '2020-11-28 23:07:37', '1'),
(12, 'ngeWixaBPDj56sS9VUOTXfGLydAcqJtp', 'LKJAFD94R', '2', '2', 'Examples of Word Processors', 'lesson', NULL, NULL, NULL, '2020-11-28', '2020-11-28', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-28 23:07:57', '2020-11-28 23:07:57', '1'),
(13, 'OTpMXhej3k59ZtARSCfqgVFbomxYsdcw', 'LKJAFD94R', '2', '1', 'The World Wide Web', 'lesson', NULL, NULL, '&lt;div&gt;&lt;!--block--&gt;What is faith? Faith, according to Longmans Dictionary of Contemporary English, is having strong feeling of trust or confidence in someone or something. In religious parlance faith is belief and trust in God.&nbsp;&lt;br&gt;&lt;br&gt;&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;Hebrews 11:1 says, &quot;Faith is the confidence that what we hope for will actually happen, it gives us assurance about things we cannot see&quot;&lt;br&gt;&lt;br&gt;&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;According to a persons faith will be his peace, his hope, his strength, his courage, his decision and his victory over the world.&nbsp;&lt;br&gt;&lt;br&gt;&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;&lt;strong&gt;MAIN &lt;/strong&gt;&lt;br&gt;&lt;br&gt;&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;Driven by faith a Gentile woman, a Syrian Phoenician whose daughter was possessed by an evil spirit came and begged Jesus to cast out the demon from her child. Have mercy on me, oh Lord, son of David. Jesus replied &quot;It isnt right to take food from the children and throw it to the dogs&quot; (Matt.15:26 or Mark 7:27)&lt;br&gt;&lt;br&gt;&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;Jesus harsh statement should have dampened the spirit of the Gentile woman.&nbsp; At first, it seemed entirely unnoticed. Jesus &quot;answered her no word&quot; Yet she prayed on, Jesus statement sounded discouraging. &quot;I am not sent but unto the lost sheep of the house of Israel&quot; Yet she prayed on! Lord, help me&quot;&lt;br&gt;&lt;br&gt;&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;The second ulterance of our Lord was even less encouraging than the first: &quot;It is not meet to take the childrens bread and cast it to dogs&quot; Yet hope deferred&quot; did not make her heart sick&quot;&nbsp; (Prov. 13:12) Again she did not keep silent but pleaded for some &quot;crumbs&quot; of mercy to be granted (to) her. Her persistence finally earned her a gracious reward. &quot;O woman, great is thy faith, be it unto thee even as thou wilt&quot;&nbsp;&lt;br&gt;&lt;br&gt;&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;If it were you what will be your reaction? With anger you will leave the scene or say some bitter words against Jesus. But the Gentile woman persisted.&nbsp;&lt;/div&gt;', '2020-11-28', '2020-11-28', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-28 23:08:09', '2020-11-30 12:26:30', '1'),
(14, '6L3gRaQrTWhJ1mC7U50vjopAbtiFIM4O', 'LKJAFD94R', '2', '1', 'Hyperlinks', 'lesson', NULL, NULL, NULL, '2020-11-28', '2020-11-28', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-28 23:08:18', '2020-11-28 23:38:42', '1'),
(15, '1pyzaRZ396jsPkGiu8dLUlqDCFn0OcTt', 'LKJAFD94R', '2', '1', 'Benefits of the Internet and World Wide Web', 'lesson', NULL, NULL, NULL, '2020-11-28', '2020-11-28', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-28 23:08:28', '2020-11-28 23:08:28', '1'),
(16, '1p0MHqbCOcl7vaNni5PYeTELfGWyRksm', 'LKJAFD94R', '2', '1', 'Disadvantages of the internet', 'lesson', NULL, NULL, NULL, '2020-11-28', '2020-11-28', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-28 23:08:38', '2020-11-28 23:08:38', '1'),
(17, 'xEKHo8DfUzhPv5Wl2dyTG0BcCZwiptnL', 'LKJAFD94R', '2', '2', 'Features of MS Office Word', 'lesson', NULL, NULL, NULL, '2020-11-28', '2020-11-28', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-28 23:16:31', '2020-11-28 23:16:31', '1'),
(18, '0BRad9cX2PzI3Hh17QUeJqmFGVbtOi6j', 'LKJAFD94R', '29', NULL, 'The Theory of Conception', 'unit', '1st', '2019/2020', '&lt;div&gt;&lt;!--block--&gt;This is the theory behind the concept of conception. To date, you have provided &lt;strong&gt;all&lt;/strong&gt; the instruments that the band would need.&nbsp;&lt;/div&gt;', '2020-12-28', '2021-01-01', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-22 10:28:32', '2020-12-22 10:28:32', '1'),
(19, 'C3PZo1TNXvyK75RGHxDY8FStiwebqugQ', 'LKJAFD94R', '29', '18', 'Introduction', 'lesson', '1st', '2019/2020', '&lt;div&gt;&lt;!--block--&gt;From the humble beginnings of this great Society, your immeasurable support for this Church in general and the Brigade specifically has been remarkable.&nbsp;&lt;/div&gt;', '2020-12-28', '2021-01-01', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-22 10:37:26', '2020-12-22 10:37:26', '1');

-- --------------------------------------------------------

--
-- Table structure for table `courses_resource_links`
--

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

--
-- Dumping data for table `courses_resource_links`
--

INSERT INTO `courses_resource_links` (`id`, `item_id`, `client_id`, `course_id`, `lesson_id`, `description`, `resource_type`, `link_url`, `link_name`, `created_by`, `date_created`, `status`) VALUES
(1, 'SKYuXN2mFkCyGTbrZjz6MB4IOcoHL0nE', 'LKJAFD94R', 'afdafdafdafd', '[\"S1fJgDrE6xRFunzhI4BlWLdcTbXmQ3Y2\",\"7yTwVHjMEQpcungN1lA20KXdB9rixbOf\",\"yVJ8zdvEFchunDL0Ha5NsmjKT96C1gXq\",\"megrn6o2U1HfVhlZRTaMq3xIibjWYd4w\"]', 'This is the description of the link that is been uploaded. Updated', 'link', 'http://localhost/analitica_innovare/medics/dashboard', 'Test Link', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-30 18:19:14', '1'),
(2, 'hoqjn4PDZzAkpdRuEvtef3WrwVYI87g9', 'LKJAFD94R', 'afdafdafdafd', '[\"S1fJgDrE6xRFunzhI4BlWLdcTbXmQ3Y2\",\"yVJ8zdvEFchunDL0Ha5NsmjKT96C1gXq\",\"megrn6o2U1HfVhlZRTaMq3xIibjWYd4w\",\"J2XardCMAcmsN8Fku5KpDLTSPyf4RIeb\",\"DbdzmTyG2M5vNo0FlWY6HKZ7kXRspJLi\",\"q87n2QJb9SPuCwyvmMGsrDTNxBfWX43l\",\"ngeWixaBPDj56sS9VUOTXfGLydAcqJtp\",\"6L3gRaQrTWhJ1mC7U50vjopAbtiFIM4O\",\"1pyzaRZ396jsPkGiu8dLUlqDCFn0OcTt\"]', 'Updating this link for the user to make good use of the resource that is available at the endpoint.', 'link', 'http://localhost/analitica_innovare/followin/v2/api/hashtags/list?hashtag_id=HT473195642887', 'This is the Name of the Link', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-30 18:35:21', '1'),
(6, 'szeVW4RJYC7yqQO9pEFIMcGb2iXLrkTH', 'LKJAFD94R', 'afdafdafdafd', '[\"megrn6o2U1HfVhlZRTaMq3xIibjWYd4w\",\"YlfiBetjG9qg6cQ3XVAbJF2KkOEh8UPC\",\"SemKVwY89CaHFGnsDfyth1NbOJ2AgxZ4\",\"J2XardCMAcmsN8Fku5KpDLTSPyf4RIeb\",\"DbdzmTyG2M5vNo0FlWY6HKZ7kXRspJLi\",\"kcmlLYTv16BInFHQwW9hejNaUG05pE2y\"]', 'Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi vulputate fringilla. Donec lacinia congue felis in faucibus. Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi vulputate fringilla. Donec lacinia congue felis in faucibus.', 'link', 'http://localhost/azikaway/services-details/building-construction-management', 'Test New Link', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-02 01:58:46', '1');

-- --------------------------------------------------------

--
-- Table structure for table `cron_scheduler`
--

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
(2, 'SELECT item_id AS user_id, name FROM users WHERE (user_type=\"user\" OR user_type=\"business\") AND deleted=\"0\" AND status=\"1\"', 'HU72tZ3lysP0Ag9Y5ehRqO4uCzMmvocp', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '12', 'Announcement', 'notification', '1', '2020-10-13 06:00:00', '2020-10-13 15:24:37', '2020-10-13 15:44:44'),
(3, '[{\"user_id\":\"F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ\",\"email\":\"priscilla_appiah@obeng.com\",\"name\":\"Priscilla Appiah\"},{\"user_id\":\"sgHvi29tuJakdfzmp71nowNlWr40BKDV\",\"email\":\"revsolo@mail.com\",\"name\":\"Solomon Kwarteng\"}]', '2EMKgph0O93bjkQRLmnqUAIylBJD1arT', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '5', 'Email Message', 'email', '1', '2020-10-13 16:02:14', '2020-10-13 16:02:14', '2020-10-13 16:15:12'),
(4, '[{\"user_id\":\"sgHvi29tuJakdfzmp71nowNlWr40BKDV\",\"email\":\"revsolo@mail.com\",\"fullname\":\"Solomon Kwarteng\"},{\"user_id\":\"uIkajswRCXEVr58mg64hxk1fc3efmnva\",\"email\":\"frankamoako@gmail.com\",\"fullname\":\"National Insurance Commission\"}]', '7xTeIEKY1bGjZX0RAs8iypdL45z6BtVh', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '5', 'Email Message', 'email', '0', '2020-10-20 20:39:07', '2020-10-20 20:39:07', NULL),
(5, '[{\"user_id\":\"sgHvi29tuJakdfzmp71nowNlWr40BKDV\",\"email\":\"revsolo@mail.com\",\"fullname\":\"Solomon Kwarteng\"}]', 'u0cnFdkDIH825p6V3b4yENZjAhPJiomY', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '5', 'Email Message', 'email', '0', '2020-10-22 10:36:26', '2020-10-22 10:36:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `client_id` varchar(32) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `department_code` varchar(32) DEFAULT NULL,
  `name` varchar(25) DEFAULT NULL,
  `image` varchar(255) DEFAULT 'assets/img/placeholder.jpg',
  `description` text DEFAULT NULL,
  `department_head` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `created_by` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `client_id`, `department_code`, `name`, `image`, `description`, `department_head`, `status`, `created_by`, `date_created`, `date_updated`) VALUES
(1, 'LKJAFD94R', NULL, 'Blue', 'assets/img/placeholder.jpg', NULL, NULL, '1', NULL, '2020-11-28 09:34:20', '2020-11-28 09:34:20'),
(2, 'LKJAFD94R', NULL, 'First Department Name', 'assets/img/placeholder.jpg', NULL, NULL, '1', NULL, '2020-11-28 09:34:20', '2020-11-28 09:34:20'),
(3, 'LKJAFD94R', NULL, 'Green', 'assets/img/placeholder.jpg', NULL, NULL, '1', NULL, '2020-11-28 09:34:20', '2020-11-28 09:34:20'),
(4, 'LKJAFD94R', NULL, 'Yellow', 'assets/img/placeholder.jpg', NULL, NULL, '1', NULL, '2020-11-28 09:34:20', '2020-11-28 09:34:20'),
(5, 'LKJAFD94R', 'DEPA', 'Pink', 'assets/img/placeholder.jpg', 'this is the department update processing', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '1', NULL, '2020-11-28 09:34:20', '2020-11-28 09:50:46'),
(6, 'LKJAFD94R', NULL, 'O- Edited', 'assets/img/placeholder.jpg', NULL, NULL, '1', NULL, '2020-11-28 09:34:20', '2020-11-28 09:34:20'),
(7, 'LKJAFD94R', NULL, 'Test Section Modified', 'assets/img/placeholder.jpg', NULL, NULL, '1', NULL, '2020-11-28 09:34:20', '2020-11-28 09:34:20'),
(8, 'LKJAFD94R', NULL, 'Final test section', 'assets/img/placeholder.jpg', NULL, NULL, '1', NULL, '2020-11-28 09:34:20', '2020-11-28 09:34:20'),
(9, 'LKJAFD94R', NULL, 'Last Step', 'assets/img/placeholder.jpg', NULL, NULL, '0', NULL, '2020-11-28 09:34:20', '2020-11-28 09:34:20'),
(10, 'LKJAFD94R', NULL, 'Adding a new department', 'assets/img/placeholder.jpg', NULL, NULL, '1', NULL, '2020-11-28 09:34:20', '2020-11-28 09:34:20'),
(11, 'LKJAFD94R', 'ANANOA', 'this is the department na', 'assets/img/placeholder.jpg', 'this is what i am inserting for this department', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '1', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-28 09:34:20', '2020-11-28 09:34:20');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

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
(1, 'LKJAFD94R', 'z2JfSOdGTI4Pbxh8yV3UKY1LcMN7rgl6', 'Vacation Starts', '&lt;div&gt;&lt;!--block--&gt;This is the vacation starting point&lt;/div&gt;', '2020-12-31', '2021-01-05', NULL, 'all', 'PI9tVxCQrLsWJ8HT3wuMigBy0Ro5ZcDf', 'on', NULL, NULL, '0', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-30 10:14:50', '0', '0', 'Pending', '1'),
(2, 'LKJAFD94R', 'ht0vuLKSAJpB6i1FzOlIrdPXEqnxw2Ne', 'Another Test Event', '&lt;div&gt;&lt;!--block--&gt;We conducted the audit in accordance with generally accepted auditing standards as well as standards accepted by the Methodist Church Ghana. These standards require that we plan and perform the audit to obtain reasonable assurance about whether the financial statement is free of material misstatement. Our audit includes examining, on a test basis, evidence supporting the amounts and disclosures in the financial statements. Our audit also includes assessing if the accounting principles applied in the financial statements is in conformity to those prescribed by the Methodist Church Ghana.&nbsp;&lt;/div&gt;', '2020-12-01', '2020-12-03', NULL, 'student', 'miEKCQ5BwD20ovknlpUxXz4g78bAdRFj', 'not', NULL, NULL, '0', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-30 12:05:22', '0', '0', 'Pending', '1'),
(4, 'LKJAFD94R', 'cLnFJiO1RyGUEH6I3Ngr8qw54WxVXPjm', 'Fast Forward Test', '&lt;div&gt;&lt;!--block--&gt;This is an event for the teaching staff for two days. An updated information on the event&lt;/div&gt;', '2021-01-20', '2021-01-22', 'assets/img/events/W6DhwKX0GucmJ2zOligdsTZLBav9eb1j.png', 'teacher', 'WABbq4NLFRwStaOeXDQvsxrGZMCzf0gV', 'not', NULL, NULL, '0', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-01 16:20:05', '0', '2', 'Held', '1'),
(5, 'LKJAFD94R', 'YWgvhIO4ojzGlM7mR9J1eKXFyAp3NBtn', 'Another Test Image', '&lt;div&gt;&lt;!--block--&gt;This is an event for the teaching staff for two days.&lt;/div&gt;', '2021-01-18', '2021-01-22', 'assets/img/events/3PrZOCX5g9MeBAUbsiH8W7lpNLcTKV1J.png', 'teacher', 'WABbq4NLFRwStaOeXDQvsxrGZMCzf0gV', 'not', NULL, NULL, '0', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-01 16:21:34', '0', '0', 'Pending', '1'),
(6, 'LKJAFD94R', 'A3XpN8dZ1T0xLFCqvItM5eSURQ4u6azG', 'Test', '&lt;div&gt;&lt;!--block--&gt;This is here&lt;/div&gt;', '2021-01-25', '2021-01-25', 'assets/img/events/QxDM7P5cjF__image.png', 'student', 'kTnKvCEymV3JcgjadBAWe0x6iqPpOH18', 'not', NULL, NULL, '0', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-01 16:33:22', '0', '0', 'Pending', '1'),
(10, 'LKJAFD94R', 'JGEV3BmRPtC7Uy05AagTLw4oe2dik1pI', 'Test', '&lt;div&gt;&lt;!--block--&gt;Test another item&lt;/div&gt;', '2021-01-09', '2021-01-09', 'assets/img/events/AO34Yct0TIWPgRC9jEUFmx57fNVS6alM.png', 'all', 'WABbq4NLFRwStaOeXDQvsxrGZMCzf0gV', 'not', NULL, NULL, '0', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-01 17:22:34', '0', '0', 'Pending', '1'),
(11, 'LKJAFD94R', 'kqmcSI0sLpiH5K7njlEUPWRZ4bYfDOAd', 'Finaly', '&lt;div&gt;&lt;!--block--&gt;This is a test and a redirection to the next page&lt;/div&gt;', '2021-01-28', '2021-01-28', 'assets/img/events/gKZTamqsh4GYCb2efDBiAlRwkzxVW8yQ.png', 'teacher', 'gIKkUqhAQtyWXcPCd6z5m0JYnMoaFxDu', 'on', NULL, NULL, '0', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-02 00:22:01', '0', '0', 'Pending', '1'),
(12, 'LKJAFD94R', 'KkZRYGUXNPnT6FzIapOLHmwe1yvtd4xB', 'Vals Day Bash', '&lt;div&gt;&lt;!--block--&gt;This is gonna be great&lt;/div&gt;', '2021-02-14', '2021-02-14', NULL, 'all', 'gIKkUqhAQtyWXcPCd6z5m0JYnMoaFxDu', 'on', NULL, NULL, '0', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-02 00:24:00', '0', '0', 'Pending', '1'),
(13, 'LKJAFD94R', 'VXyqaZPKNzu6ekUgSxOD8CBEjTFpAw4n', 'Resuming and Excursion', '&lt;div&gt;&lt;!--block--&gt;This is an excursion&lt;/div&gt;', '2021-02-17', '2021-02-17', NULL, 'student', 'miEKCQ5BwD20ovknlpUxXz4g78bAdRFj', 'on', NULL, NULL, '0', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-02 00:28:29', '0', '0', 'Pending', '1');

-- --------------------------------------------------------

--
-- Table structure for table `events_types`
--

CREATE TABLE `events_types` (
  `id` int(11) NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `name` varchar(244) DEFAULT NULL,
  `description` text NOT NULL,
  `color_code` varchar(10) DEFAULT '#6777ef',
  `icon` varchar(244) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `events_types`
--

INSERT INTO `events_types` (`id`, `client_id`, `item_id`, `name`, `description`, `color_code`, `icon`, `status`) VALUES
(1, 'LKJAFD94R', 'gIKkUqhAQtyWXcPCd6z5m0JYnMoaFxDu', 'First Type Updated', 'This is the first type of event that i am creating and insert. Great test', '#398b13', NULL, '1'),
(2, 'LKJAFD94R', 'WABbq4NLFRwStaOeXDQvsxrGZMCzf0gV', 'Second Event Test', 'This is the second event type. The next trial will be to list and update the event types that has already.', '#b48608', NULL, '1'),
(4, 'LKJAFD94R', 'miEKCQ5BwD20ovknlpUxXz4g78bAdRFj', 'Another one', 'This is the final test for the event type insertion', '#131e72', NULL, '1'),
(5, 'LKJAFD94R', 'PI9tVxCQrLsWJ8HT3wuMigBy0Ro5ZcDf', 'Append to Dropdown', 'This is an update the append to dropdown list query.', '#e62828', NULL, '1'),
(6, 'LKJAFD94R', 'kTnKvCEymV3JcgjadBAWe0x6iqPpOH18', 'New Append to Dropdown', '', '#6777ef', NULL, '1');

-- --------------------------------------------------------

--
-- Table structure for table `files_attachment`
--

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

--
-- Dumping data for table `files_attachment`
--

INSERT INTO `files_attachment` (`id`, `resource`, `description`, `attachment_size`, `record_id`, `resource_id`, `created_by`, `date_created`) VALUES
(1, 'library_book', '{\"files\":[{\"unique_id\":\"YCRWThpuiABfLzHIoJjeEVkNgMdP5bt4csF60xrq8D1U97vaQyZOmS3\",\"name\":\"hospital-information-system-features.jpg\",\"path\":\"assets\\/uploads\\/uIkajsw123456789064hxk1fc3efmnva\\/docs\\/ebook_12223444443\\/hospital-information-system-features.jpg\",\"type\":\"jpg\",\"size\":\"35.96KB\",\"size_raw\":\"35.96\",\"is_deleted\":0,\"record_id\":\"12223444443\",\"datetime\":\"Monday, 4th January 2021 at 07:16:07PM\",\"favicon\":\"fa fa-file-image fa-1x\",\"color\":\"danger\",\"uploaded_by\":\"test_admin\",\"uploaded_by_id\":\"uIkajsw123456789064hxk1fc3efmnva\"},{\"unique_id\":\"r3aSyCVRiJ6ZA8Tek0vqcsofw7HzUxNELgnmh1D5FQYX9OKdIbW2PGj\",\"name\":\"online-poll-result.png\",\"path\":\"assets\\/uploads\\/uIkajsw123456789064hxk1fc3efmnva\\/docs\\/ebook_12223444443\\/online-poll-result.png\",\"type\":\"png\",\"size\":\"105.5KB\",\"size_raw\":\"105.5\",\"is_deleted\":0,\"record_id\":\"12223444443\",\"datetime\":\"Monday, 4th January 2021 at 07:16:07PM\",\"favicon\":\"fa fa-file-image fa-1x\",\"color\":\"danger\",\"uploaded_by\":\"test_admin\",\"uploaded_by_id\":\"uIkajsw123456789064hxk1fc3efmnva\"}],\"files_count\":2,\"raw_size_mb\":0.14,\"files_size\":\"0.14MB\"}', '0.14', '12223444443', '12223444443', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-04 19:16:07');

-- --------------------------------------------------------

--
-- Table structure for table `guardian_relation`
--

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

--
-- Dumping data for table `incidents`
--

INSERT INTO `incidents` (`id`, `client_id`, `item_id`, `incident_id`, `user_id`, `assigned_to`, `created_by`, `reported_by`, `incident_type`, `subject`, `description`, `incident_date`, `location`, `date_created`, `date_updated`, `deleted`, `status`) VALUES
(1, 'LKJAFD94R', '8zjP2lLqMIJZA6s1KynmCXaOwNb53kDd', NULL, 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 'null', 'uIkajsw123456789064hxk1fc3efmnva', 'Emmanuel Obeng, 0550107770', 'incident', 'The need to turn this around', '&lt;div&gt;&lt;!--block--&gt;this is the incident that occurred some moments ago when they wanted to know who was in charge of the items sent to the people and those around were not also around as at the time we wanted them to be around all this while.&lt;/div&gt;', '2020-11-29', 'The school premises', '2020-11-29 16:43:07', '2020-11-30 11:19:47', '0', 'Solved'),
(5, 'LKJAFD94R', 'KPNgS8LVXwWYqM9FBtpfIisb3ekCZuR5', '8zjP2lLqMIJZA6s1KynmCXaOwNb53kDd', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', NULL, 'uIkajsw123456789064hxk1fc3efmnva', NULL, 'followup', NULL, 'This is the first followup message to this incident', NULL, NULL, '2020-11-30 04:44:49', '2020-11-30 04:44:49', '0', 'Pending'),
(6, 'LKJAFD94R', 'LfcjMobNHnaCREYwQ3qxImF4r7OzTepd', '8zjP2lLqMIJZA6s1KynmCXaOwNb53kDd', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', NULL, 'uIkajsw123456789064hxk1fc3efmnva', NULL, 'followup', NULL, 'Its becoming interesting to note that, there is a huge difference between myself and the kind of information been put accross. Its quiet facinating to note that we are not on the same track.', NULL, NULL, '2020-11-30 05:16:26', '2020-11-30 05:16:26', '0', 'Pending'),
(7, 'LKJAFD94R', 'FeZjKzHo7dsRO4w0UpuLg1VPxIG5ytTS', NULL, 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 'null', 'uIkajsw123456789064hxk1fc3efmnva', 'The children in the class', 'incident', 'New Incident Log', '&lt;div&gt;&lt;!--block--&gt;They were in the class and noticed there was a problem with the students. All rushed there to ascertain the nature of the incident. That led to more students rushing to the scene. This is indeed a problem that needs to be looked at.&lt;/div&gt;', '2020-11-19', 'The class room', '2020-11-30 11:26:29', '2020-11-30 19:01:54', '0', 'Cancelled'),
(8, 'LKJAFD94R', 'NKDcP2kgSMaLolp9TX4ZeuIwG8UJrB5F', 'FeZjKzHo7dsRO4w0UpuLg1VPxIG5ytTS', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', NULL, 'uIkajsw123456789064hxk1fc3efmnva', NULL, 'followup', NULL, 'Realizing that the pupil who were involved also was a problem. He made an attempt to consult the administrator in order to shelve the evidence and make it appear as if there was no issue at at. This was in regard to the fact that, we have an issue that needs to be looked into as soon as possible.', NULL, NULL, '2020-11-30 11:27:55', '2020-11-30 11:27:55', '0', 'Pending'),
(9, 'LKJAFD94R', 'Q83w7Ivj25KuiqAfRdgGWLnoahxUDcOV', 'FeZjKzHo7dsRO4w0UpuLg1VPxIG5ytTS', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', NULL, 'uIkajsw123456789064hxk1fc3efmnva', NULL, 'followup', NULL, 'Who else wanted us to believe in the comments of the student when we were at the classroom. Please bear in mind that this is a test application and we are poised at making it be the best in terms of application and development. I am making this application one of the best choice for all schools.', NULL, NULL, '2020-11-30 11:28:57', '2020-11-30 11:28:57', '0', 'Pending'),
(10, 'LKJAFD94R', 'WmfgXVeatN7JIHhoALDK683vdcqkU4TO', 'FeZjKzHo7dsRO4w0UpuLg1VPxIG5ytTS', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', NULL, 'uIkajsw123456789064hxk1fc3efmnva', NULL, 'followup', NULL, '1. Reduce the length of homepage sliders to that of the banner images on the other pages 2. Remove the get started and read more buttons on the homepage slider images 3. Remove the ACHIEVEMNTS section from the home page 4. Remove the UPCOMING EVENTS,EXPERIINCED STAFFS AND LATEST NEWS SECTIONS 5. Remove the FOOTER section containing the recent posts, out sitemap and newsletter and leave just', NULL, NULL, '2020-11-30 11:30:05', '2020-11-30 11:30:05', '0', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `name` varchar(25) DEFAULT NULL,
  `section_code` varchar(32) DEFAULT NULL,
  `image` varchar(255) DEFAULT 'assets/img/placeholder.jpg',
  `description` text DEFAULT NULL,
  `section_leader` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
  `created_by` varchar(32) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `date_created` datetime DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `client_id`, `name`, `section_code`, `image`, `description`, `section_leader`, `created_by`, `status`, `date_created`, `date_updated`) VALUES
(1, 'LKJAFD94R', 'Blue', NULL, 'assets/img/placeholder.jpg', NULL, NULL, NULL, '1', '2020-11-28 00:34:43', '2020-11-28 09:34:32'),
(2, 'LKJAFD94R', 'Red', 'SECTIONCODED', 'assets/img/placeholder.jpg', 'update the section information here', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', NULL, '1', '2020-11-28 00:34:43', '2020-11-28 09:46:06'),
(3, 'LKJAFD94R', 'Green', NULL, 'assets/img/placeholder.jpg', NULL, NULL, NULL, '1', '2020-11-28 00:34:43', '2020-11-28 09:34:32'),
(4, 'LKJAFD94R', 'Yellow', NULL, 'assets/img/placeholder.jpg', NULL, NULL, NULL, '1', '2020-11-28 00:34:43', '2020-11-28 09:34:32'),
(5, 'LKJAFD94R', 'Pink Section', 'SECTIONC', 'assets/img/placeholder.jpg', 'this is the final updates to the sections list', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', NULL, '1', '2020-11-28 00:34:43', '2020-11-28 09:48:02'),
(6, 'LKJAFD94R', 'O- Edited', NULL, 'assets/img/placeholder.jpg', NULL, NULL, NULL, '1', '2020-11-28 00:34:43', '2020-11-28 09:34:32'),
(7, 'LKJAFD94R', 'Test Section Modified', NULL, 'assets/img/placeholder.jpg', NULL, NULL, NULL, '1', '2020-11-28 00:34:43', '2020-11-28 09:34:32'),
(8, 'LKJAFD94R', 'Final test section', NULL, 'assets/img/placeholder.jpg', NULL, NULL, NULL, '1', '2020-11-28 00:34:43', '2020-11-28 09:34:32'),
(9, 'LKJAFD94R', 'Last Step', NULL, 'assets/img/placeholder.jpg', NULL, NULL, NULL, '0', '2020-11-28 00:34:43', '2020-11-28 09:34:32'),
(10, 'LKJAFD94R', 'this is the section name', 'ADD A SECTION', 'assets/img/placeholder.jpg', 'this is the description for the section also', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 'uIkajsw123456789064hxk1fc3efmnva', '1', '2020-11-28 00:44:02', '2020-11-28 09:34:32');

-- --------------------------------------------------------

--
-- Table structure for table `table_indexes`
--

CREATE TABLE `table_indexes` (
  `id` int(11) NOT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `table` varchar(32) DEFAULT NULL,
  `page` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
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
  `guardian_id` varchar(255) DEFAULT NULL,
  `country` int(11) UNSIGNED DEFAULT NULL,
  `verify_token` varchar(120) DEFAULT NULL,
  `verified_date` datetime DEFAULT NULL,
  `token_expiry` varchar(32) DEFAULT NULL,
  `changed_password` enum('0','1') DEFAULT '1',
  `city` varchar(255) DEFAULT NULL,
  `date_created` datetime DEFAULT current_timestamp(),
  `last_updated` datetime DEFAULT NULL,
  `created_by` varchar(32) DEFAULT NULL,
  `image` varchar(255) DEFAULT 'assets/img/user.png',
  `previous_school` varchar(500) DEFAULT NULL,
  `previous_school_qualification` varchar(500) DEFAULT NULL,
  `previous_school_remarks` text DEFAULT NULL,
  `user_status` enum('Transferred','Active','Graduated','Dismissed') NOT NULL DEFAULT 'Active',
  `perma_image` varchar(255) DEFAULT 'assets/img/user.png',
  `user_type` enum('teacher','employee','parent','admin','student','accountant') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `item_id`, `unique_id`, `client_id`, `firstname`, `lastname`, `othername`, `name`, `academic_year`, `academic_term`, `enrollment_date`, `gender`, `email`, `username`, `password`, `access_level`, `preferences`, `status`, `deleted`, `verified_email`, `last_login`, `phone_number`, `phone_number_2`, `description`, `position`, `address`, `online`, `chat_status`, `last_seen`, `nation_ids`, `date_of_birth`, `class_id`, `blood_group`, `religion`, `section`, `programme`, `department`, `nationality`, `occupation`, `postal_code`, `disabled`, `residence`, `employer`, `guardian_id`, `country`, `verify_token`, `verified_date`, `token_expiry`, `changed_password`, `city`, `date_created`, `last_updated`, `created_by`, `image`, `previous_school`, `previous_school_qualification`, `previous_school_remarks`, `user_status`, `perma_image`, `user_type`) VALUES
(29, 'uIkajsw123456789064hxk1fc3efmnva', 'kajflkdkfafd', 'LKJAFD94R', 'Admin', 'User', 'Account', 'Admin Account', NULL, NULL, NULL, 'Male', 'test_admin@gmail.com', 'test_admin', '$2y$10$aQYMdSMFK.bxxjMLklXFTuD7.i84mep11Crrg4hAjAfv6cm1sQv9S', 8, '{\"theme_color\":\"\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"10\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-11-09 08:31:15\",\"idb_next_init\":\"2020-11-12 08:31:15\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"quotes\":\"on\",\"policies\":\"on\",\"adverts\":\"on\"},\"auto_close_modal\":\"dont\",\"text_editor\":\"trix\",\"messages\":{\"enter_to_send\":\"1\"}}', '0', '0', 'Y', '2021-01-07 14:48:24', '+233240889023', '(+233) 550-107-770', 'The description updated.', 'Chief Technical Officer, Analitica Innovare', 'Accra Ghana', '1', NULL, '2021-01-07 16:06:42', NULL, '1991-11-21', NULL, NULL, NULL, NULL, NULL, NULL, 'Ghananaian', 'Software Developer', NULL, '0', 'Accra', 'Analitica Innovare', NULL, 10, NULL, NULL, NULL, '0', 'Accra', '2020-06-27 03:36:47', '2020-09-24 13:33:54', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'assets/img/user.png', NULL, NULL, NULL, 'Active', 'assets/img/user.png', 'admin'),
(33, 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'FTA000012020', 'LKJAFD94R', 'Test', 'Teacher', 'Account', 'Teacher Account', NULL, NULL, '2020-11-01', 'Female', 'emmallob14@gmail.com', 'test_teacher', '$2y$10$aQYMdSMFK.bxxjMLklXFTuD7.i84mep11Crrg4hAjAfv6cm1sQv9S', 1, '{\"theme_color\":\"\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"10\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-11-09 08:31:15\",\"idb_next_init\":\"2020-11-12 08:31:15\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"quotes\":\"on\",\"policies\":\"on\",\"adverts\":\"on\"},\"auto_close_modal\":\"dont\",\"text_editor\":\"trix\",\"messages\":{\"enter_to_send\":\"1\"}}', '0', '0', 'N', '2021-01-07 14:48:29', '0550107770', '0203317732', 'This is the same information for the user information here and there.', NULL, NULL, '1', NULL, '2021-01-07 16:06:51', NULL, '1992-03-22', '2', '3', NULL, '3', NULL, '3', NULL, NULL, NULL, '0', 'Accra', NULL, NULL, 84, 'McqwNLnt96KzeWD1lER4Zt8sX3usfrE0LgAFiDHPzyO6hgado5X0pJT5wSj9IexbA7', NULL, '1606460040', '0', NULL, '2020-11-27 00:54:00', '2020-11-27 19:12:43', 'uIkajsw123456789064hxk1fc3efmnva', 'assets/img/users/WgAzcUqmSK__Methodist.jpg', NULL, NULL, NULL, 'Active', 'assets/images/profiles/avatar.png', 'teacher'),
(34, 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 'AGL000012020', 'LKJAFD94R', 'Solomon', 'Obeng', 'Darko', 'Solomon Obeng Darko', NULL, NULL, '2020-11-08', 'Male', 'themailhereisthere@mail.com', 'test_student_2', '$2y$10$aQYMdSMFK.bxxjMLklXFTuD7.i84mep11Crrg4hAjAfv6cm1sQv9S', 1, '{\"theme_color\":\"\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"10\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-11-09 08:31:15\",\"idb_next_init\":\"2020-11-12 08:31:15\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"quotes\":\"on\",\"policies\":\"on\",\"adverts\":\"on\"},\"auto_close_modal\":\"dont\",\"text_editor\":\"trix\",\"messages\":{\"enter_to_send\":\"1\"}}', '0', '0', 'N', '2020-12-21 16:37:08', '00930993093', '0039930039930', NULL, NULL, NULL, '0', NULL, '2020-12-21 16:45:08', NULL, '2000-10-15', '1', '3', NULL, '3', NULL, '3', NULL, NULL, NULL, '0', 'that location', NULL, '54693872', 234, '8wJ0RQVHF9LtETipowqsV8RdN39J3e6PT1i1rU4vDgzAPBUIxCGWusFm5S2jSXWYnvMcbbQf', NULL, '1606460673', '0', NULL, '2020-11-27 01:04:33', '2020-12-17 23:00:24', 'uIkajsw123456789064hxk1fc3efmnva', 'assets/img/users/jns7h1WK2G__appimg-88bdf4ad97eeb380c2f931b768b0ad14.png', NULL, NULL, NULL, 'Active', 'assets/images/profiles/avatar.png', 'student'),
(35, 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'AGL000022020', 'LKJAFD94R', 'Grace', 'Obeng-Yeboah', 'Afia', 'Grace Obeng-Yeboah', NULL, NULL, '2020-11-08', 'Male', 'graciellaob@gmail.com', 'test_student', '$2y$10$aQYMdSMFK.bxxjMLklXFTuD7.i84mep11Crrg4hAjAfv6cm1sQv9S', 1, '{\"theme_color\":\"\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"10\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-11-09 08:31:15\",\"idb_next_init\":\"2020-11-12 08:31:15\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"quotes\":\"on\",\"policies\":\"on\",\"adverts\":\"on\"},\"auto_close_modal\":\"dont\",\"text_editor\":\"trix\",\"messages\":{\"enter_to_send\":\"1\"}}', '0', '0', 'N', '2020-12-28 15:35:00', '00930993093', '0039930039930', 'This is the basic information about my self... I am confident that the information is valid and useful', NULL, NULL, '0', NULL, '2020-12-28 16:38:32', NULL, '2000-12-20', '2', '3', NULL, '3', NULL, '3', NULL, NULL, NULL, '0', 'Shiashie', NULL, '48356217,30462664355', 234, 'mKyIShrzNJYIwdnB0qUUW2DihvFSAs5b539ZOpRGlnTo74dcakOAQswfLoe0LVV', NULL, '1606460780', '0', NULL, '2020-11-27 01:06:20', '2020-11-27 12:44:38', 'uIkajsw123456789064hxk1fc3efmnva', 'assets/img/user.png', NULL, NULL, NULL, 'Active', 'assets/images/profiles/avatar.png', 'student'),
(40, 'SZM14dtqDkbfn5cBl0ARgPCj287hym36', 'AGL000032020', 'LKJAFD94R', 'Emmanuella', 'Darko', 'Sarfowaa', 'Emmanuella Darko Sarfowaa', NULL, NULL, '2019-06-04', 'Female', 'jauntygirl@gmail.com', 'test_student_3', '$2y$10$aQYMdSMFK.bxxjMLklXFTuD7.i84mep11Crrg4hAjAfv6cm1sQv9S', 1, '{\"theme_color\":\"\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"10\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-11-09 08:31:15\",\"idb_next_init\":\"2020-11-12 08:31:15\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"quotes\":\"on\",\"policies\":\"on\",\"adverts\":\"on\"},\"auto_close_modal\":\"dont\",\"text_editor\":\"trix\",\"messages\":{\"enter_to_send\":\"1\"}}', '0', '0', 'N', NULL, '0247685521', NULL, NULL, NULL, NULL, '0', NULL, '2020-12-17 22:48:33', NULL, '2001-09-04', '1', '4', NULL, '2', NULL, '3', NULL, NULL, NULL, '0', 'Agblezaa, Off Spintex Road', NULL, '', 84, 'cuOes44nI6yE6QLU9jVPXX0SwAwp1hfljMqd3gaYpin2rbTxcsqJkAdS3ivoQDWzIGoDV1G', NULL, '1608266913', '0', NULL, '2020-12-17 22:48:33', NULL, 'uIkajsw123456789064hxk1fc3efmnva', 'assets/img/user.png', NULL, NULL, NULL, 'Active', 'assets/img/user.png', 'student'),
(41, 'ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v', 'AGL000042020', 'LKJAFD94R', 'Frank', 'Amponsah', 'Amoah', 'Frank Amponsah Amoah', NULL, NULL, '2019-10-21', 'Male', 'frankamoah@gmail.com', 'test_student_4', '$2y$10$aQYMdSMFK.bxxjMLklXFTuD7.i84mep11Crrg4hAjAfv6cm1sQv9S', 1, '{\"theme_color\":\"\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"10\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-11-09 08:31:15\",\"idb_next_init\":\"2020-11-12 08:31:15\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"quotes\":\"on\",\"policies\":\"on\",\"adverts\":\"on\"},\"auto_close_modal\":\"dont\",\"text_editor\":\"trix\",\"messages\":{\"enter_to_send\":\"1\"}}', '0', '0', 'N', NULL, NULL, NULL, 'This is the description of the student', NULL, NULL, '0', NULL, '2020-12-17 22:59:40', NULL, '1990-12-12', '2', '5', NULL, '5', NULL, '5', NULL, NULL, NULL, '0', 'Port Harcourt', NULL, '', 32, 'ISif1mdadb3LEq7rxO04znYjHFLYXM1PbtKo9GGhzZOkWucgjUXs6weQaBm8P2TAcETvsFW', NULL, '1608267580', '0', NULL, '2020-12-17 22:59:40', NULL, 'uIkajsw123456789064hxk1fc3efmnva', 'assets/img/user.png', NULL, NULL, NULL, 'Active', 'assets/img/user.png', 'student'),
(42, 'SZM14dtqcccfn5cBl0ARgPCj287hym36', 'AGL000052020', 'LKJAFD94R', 'Cecilia', 'Boateng', '', 'Cecilia Boateng', NULL, NULL, '2019-06-04', 'Female', 'jauntygirl@gmail.com', 'test_student_3', '$2y$10$aQYMdSMFK.bxxjMLklXFTuD7.i84mep11Crrg4hAjAfv6cm1sQv9S', 1, '{\"theme_color\":\"\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"10\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-11-09 08:31:15\",\"idb_next_init\":\"2020-11-12 08:31:15\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"quotes\":\"on\",\"policies\":\"on\",\"adverts\":\"on\"},\"auto_close_modal\":\"dont\",\"text_editor\":\"trix\",\"messages\":{\"enter_to_send\":\"1\"}}', '0', '0', 'N', NULL, '0247685521', NULL, NULL, NULL, NULL, '0', NULL, '2020-12-17 22:48:33', NULL, '2001-07-10', '1', '4', NULL, '2', NULL, '3', NULL, NULL, NULL, '0', 'Agblezaa, Off Spintex Road', NULL, '48356217', 84, 'cuOes44nI6yE6QLU9jVPXX0SwAwp1hfljMqd3gaYpin2rbTxcsqJkAdS3ivoQDWzIGoDV1G', NULL, '1608266913', '0', NULL, '2020-12-17 22:48:33', NULL, 'uIkajsw123456789064hxk1fc3efmnva', 'assets/img/user.png', NULL, NULL, NULL, 'Active', 'assets/img/user.png', 'student'),
(44, 'SZMsssqcccfn5cBl0ARgPCj287hym36', 'AGL000062020', 'LKJAFD94R', 'Maureen', 'Anim', '', 'Maureen Anim', NULL, NULL, '2019-06-04', 'Female', 'jauntygirl@gmail.com', 'test_student_3', '$2y$10$aQYMdSMFK.bxxjMLklXFTuD7.i84mep11Crrg4hAjAfv6cm1sQv9S', 1, '{\"theme_color\":\"\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"10\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-11-09 08:31:15\",\"idb_next_init\":\"2020-11-12 08:31:15\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"quotes\":\"on\",\"policies\":\"on\",\"adverts\":\"on\"},\"auto_close_modal\":\"dont\",\"text_editor\":\"trix\",\"messages\":{\"enter_to_send\":\"1\"}}', '0', '0', 'N', NULL, '0247685521', NULL, NULL, NULL, NULL, '0', NULL, '2020-12-17 22:48:33', NULL, '2001-11-14', '2', '4', NULL, '2', NULL, '3', NULL, NULL, NULL, '0', 'Agblezaa, Off Spintex Road', NULL, '', 84, 'cuOes44nI6yE6QLU9jVPXX0SwAwp1hfljMqd3gaYpin2rbTxcsqJkAdS3ivoQDWzIGoDV1G', NULL, '1608266913', '0', NULL, '2020-12-17 22:48:33', NULL, 'uIkajsw123456789064hxk1fc3efmnva', 'assets/img/user.png', NULL, NULL, NULL, 'Active', 'assets/img/user.png', 'student'),
(46, 'SZMsssqcccfn5cBl0aaaPCj287hym36', 'AGL000072020', 'LKJAFD94R', 'Felicia', 'Amponsah', '', 'Felicia Amponsah', NULL, NULL, '2019-06-04', 'Female', 'jauntygirl@gmail.com', 'test_student_3', '$2y$10$aQYMdSMFK.bxxjMLklXFTuD7.i84mep11Crrg4hAjAfv6cm1sQv9S', 1, '{\"theme_color\":\"\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"10\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-11-09 08:31:15\",\"idb_next_init\":\"2020-11-12 08:31:15\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"quotes\":\"on\",\"policies\":\"on\",\"adverts\":\"on\"},\"auto_close_modal\":\"dont\",\"text_editor\":\"trix\",\"messages\":{\"enter_to_send\":\"1\"}}', '0', '0', 'N', NULL, '0247685521', NULL, NULL, NULL, NULL, '0', NULL, '2020-12-17 22:48:33', NULL, '2001-06-14', '1', '4', NULL, '2', NULL, '3', NULL, NULL, NULL, '0', 'Agblezaa, Off Spintex Road', NULL, '48356217', 84, 'cuOes44nI6yE6QLU9jVPXX0SwAwp1hfljMqd3gaYpin2rbTxcsqJkAdS3ivoQDWzIGoDV1G', NULL, '1608266913', '0', NULL, '2020-12-17 22:48:33', NULL, 'uIkajsw123456789064hxk1fc3efmnva', 'assets/img/user.png', NULL, NULL, NULL, 'Active', 'assets/img/user.png', 'student');

-- --------------------------------------------------------

--
-- Table structure for table `users_access_attempt`
--

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
(1, '::1', 'emmallob14@gmail.com', '1', 'reset', 1, '2020-09-19 11:27:43'),
(2, '::1', 'priscilla_appiah@obeng.com', '0', 'login', 2, '2020-09-19 11:40:53'),
(3, '::1', 'emmallob14', '1', 'login', 3, '2020-09-19 16:14:59'),
(4, '::1', 'test@login.com', '0', 'login', 3, '2020-09-22 13:20:12'),
(5, '::1', 'frankamoako@gmail.com', '1', 'reset', 1, '2020-09-22 22:49:56'),
(6, '::1', 'test_admin', '1', 'login', 0, '2021-01-07 14:48:24'),
(7, '::1', 'revsolo', '1', 'login', 0, '2020-09-28 22:09:15'),
(8, '::1', 'testaccount', '0', 'login', 1, '2020-10-02 23:58:50'),
(9, '::1', 'test_broker', '1', 'login', 0, '2020-11-10 09:40:30'),
(10, '::1', 'priscilla_appiah', '0', 'login', 4, '2020-10-05 08:42:33'),
(11, '::1', 'test_user', '0', 'login', 0, '2020-11-11 19:49:50'),
(12, '::1', 'emmallob', '0', 'login', 4, '2020-10-10 09:19:24'),
(13, '::1', 'test_ic', '1', 'login', 4, '2020-11-13 12:31:55'),
(14, '::1', 'testadmin@mail.com', '0', 'login', 1, '2020-10-22 16:31:31'),
(15, '::1', 'admin@mail.com', '0', 'login', 6, '2020-11-07 14:23:09'),
(16, '::1', 'test_nic', '0', 'login', 1, '2020-12-02 01:06:33');

-- --------------------------------------------------------

--
-- Table structure for table `users_activity_logs`
--

CREATE TABLE `users_activity_logs` (
  `id` int(11) UNSIGNED NOT NULL,
  `item_id` varchar(72) DEFAULT NULL,
  `user_id` varchar(32) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `previous_record` text DEFAULT NULL,
  `date_recorded` datetime NOT NULL DEFAULT current_timestamp(),
  `user_agent` varchar(255) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users_activity_logs`
--

INSERT INTO `users_activity_logs` (`id`, `item_id`, `user_id`, `subject`, `source`, `previous_record`, `date_recorded`, `user_agent`, `description`) VALUES
(1, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl_xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'uIkajsw123456789064hxk1fc3efmnva', 'assignment-grade', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-21 16:01:54', 'Windows 10 | Chrome | ::1', 'Admin User Account graded the student: 26'),
(2, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl_ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v', 'uIkajsw123456789064hxk1fc3efmnva', 'assignment-grade', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-21 16:01:54', 'Windows 10 | Chrome | ::1', 'Admin User Account graded the student: 25'),
(3, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl_ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v', 'uIkajsw123456789064hxk1fc3efmnva', 'assignment-grade', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-21 16:06:32', 'Windows 10 | Chrome | ::1', 'Admin User Account graded the student: 27'),
(4, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl_xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'uIkajsw123456789064hxk1fc3efmnva', 'assignment-grade', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-21 16:16:52', 'Windows 10 | Chrome | ::1', 'Admin User Account graded the student: 22'),
(5, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl_ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v', 'uIkajsw123456789064hxk1fc3efmnva', 'assignment-grade', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-21 16:16:52', 'Windows 10 | Chrome | ::1', 'Admin User Account graded the student: 23'),
(6, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl_xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'uIkajsw123456789064hxk1fc3efmnva', 'assignment-grade', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-21 16:26:55', 'Windows 10 | Chrome | ::1', 'Admin User Account graded the student: 26'),
(7, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl_ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v', 'uIkajsw123456789064hxk1fc3efmnva', 'assignment-grade', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-21 16:26:55', 'Windows 10 | Chrome | ::1', 'Admin User Account graded the student: 28'),
(8, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl_xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'uIkajsw123456789064hxk1fc3efmnva', 'assignment-grade', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-21 16:30:42', 'Windows 10 | Chrome | ::1', 'Admin User Account graded the student: 25'),
(9, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl_ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v', 'uIkajsw123456789064hxk1fc3efmnva', 'assignment-grade', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-21 16:30:43', 'Windows 10 | Chrome | ::1', 'Admin User Account graded the student: 25'),
(10, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl_xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'uIkajsw123456789064hxk1fc3efmnva', 'assignment-grade', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-21 16:31:05', 'Windows 10 | Chrome | ::1', 'Admin User Account graded the student: 27'),
(11, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl_ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v', 'uIkajsw123456789064hxk1fc3efmnva', 'assignment-grade', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-21 16:31:05', 'Windows 10 | Chrome | ::1', 'Admin User Account graded the student: 29'),
(12, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl_xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'uIkajsw123456789064hxk1fc3efmnva', 'assignment-grade', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-21 16:33:13', 'Windows 10 | Chrome | ::1', 'Admin User Account graded the student: 24'),
(13, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl_ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v', 'uIkajsw123456789064hxk1fc3efmnva', 'assignment-grade', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-21 16:33:13', 'Windows 10 | Chrome | ::1', 'Admin User Account graded the student: 25'),
(14, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl_xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'uIkajsw123456789064hxk1fc3efmnva', 'assignment-grade', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-21 16:34:46', 'Windows 10 | Chrome | ::1', 'Admin User Account graded the student: 26'),
(15, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl_ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v', 'uIkajsw123456789064hxk1fc3efmnva', 'assignment-grade', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-21 16:34:46', 'Windows 10 | Chrome | ::1', 'Admin User Account graded the student: 26'),
(16, '', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-22 06:26:29', 'Windows 10 | Chrome | ::1', '<strong>Grace Obeng-Yeboah</strong> added a new endpoint: <strong>assignments/handin</strong> to the resource: <strong>assignments</strong>.'),
(17, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl_xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'assignment-doc', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-22 07:49:03', 'Windows 10 | Chrome | ::1', 'Grace Obeng-Yeboah handed in the assignment for grading.'),
(18, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl_xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'assignment-doc', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-22 08:09:21', 'Windows 10 | Chrome | ::1', 'Grace Obeng-Yeboah handed in the assignment for grading.'),
(19, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl_xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'assignment-doc', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-22 08:13:49', 'Windows 10 | Chrome | ::1', 'Grace Obeng-Yeboah handed in the assignment for grading.'),
(20, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl_xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'assignment-doc', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-22 08:15:36', 'Windows 10 | Chrome | ::1', 'Grace Obeng-Yeboah handed in the assignment for grading.'),
(21, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl_xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'assignment-doc', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-22 08:17:42', 'Windows 10 | Chrome | ::1', 'Grace Obeng-Yeboah handed in the assignment for grading.'),
(22, '', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-22 09:27:40', 'Windows 10 | Chrome | ::1', '<strong>Grace Obeng-Yeboah</strong> added a new endpoint: <strong>assignments/close</strong> to the resource: <strong>assignments</strong>.'),
(23, '29', 'uIkajsw123456789064hxk1fc3efmnva', 'courses', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-22 09:56:04', 'Windows 10 | Chrome | ::1', 'Admin User Account created a new Course: The Concept of Reproduction'),
(24, '18', 'uIkajsw123456789064hxk1fc3efmnva', 'courses_plan', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-22 10:28:32', 'Windows 10 | Chrome | ::1', 'Admin User Account created a new Course Unit: The Theory of Conception'),
(25, '19', 'uIkajsw123456789064hxk1fc3efmnva', 'courses_plan', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-22 10:37:26', 'Windows 10 | Chrome | ::1', 'Admin User Account created a new Course Unit: Introduction'),
(26, '', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-22 14:59:52', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> added a new endpoint: <strong>assignments/update</strong> to the resource: <strong>assignments_add</strong>.'),
(27, 'qpenjlpgilhwjkyszofuhub3ocwmtebr', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"106\",\"item_id\":\"qpenjlpgilhwjkyszofuhub3ocwmtebr\",\"version\":\"v1\",\"resource\":\"assignments_add\",\"endpoint\":\"assignments\\/update\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"assignment_type\\\":\\\"required - The type of assignment type to upload (multiple_choice or upload_file_attachment)\\\",\\\"assignment_title\\\":\\\"required - The title of the assignment\\\",\\\"description\\\":\\\"Any additional instructions added to the assignment\\\",\\\"grade\\\":\\\"required - The grade for this assignment\\\",\\\"date_due\\\":\\\"required - The date on which the assignment is due.\\\",\\\"time_due\\\":\\\"The time for submission\\\",\\\"assigned_to\\\":\\\"This determines whether to assign the assignment to all students in the class or to specific students\\\",\\\"assigned_to_list\\\":\\\"This is required when you decide to assign the assignment to specific students.\\\",\\\"class_id\\\":\\\"required - The id of the class to assign the assignment\\\",\\\"course_id\\\":\\\"required - The unique id of the course to link this assignment.\\\",\\\"assignment_id\\\":\\\"required - The unique id of the assignment to update the record.\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-12-22 14:59:52\",\"last_updated\":\"2020-12-22 14:59:52\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":null}', '2020-12-22 15:21:49', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> updated the endpoint.'),
(28, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl', 'uIkajsw123456789064hxk1fc3efmnva', 'assignments', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-22 15:32:06', 'Windows 10 | Chrome | ::1', 'Admin User Account updated the assignment details'),
(29, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl', 'uIkajsw123456789064hxk1fc3efmnva', 'assignments', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-22 15:40:56', 'Windows 10 | Chrome | ::1', 'Admin User Account updated the assignment details'),
(30, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl', 'uIkajsw123456789064hxk1fc3efmnva', 'assignments', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-22 15:40:56', 'Windows 10 | Chrome | ::1', 'Due Date has been changed.'),
(31, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl', 'uIkajsw123456789064hxk1fc3efmnva', 'assignments', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-22 15:40:57', 'Windows 10 | Chrome | ::1', 'Due Time has been changed.'),
(32, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl', 'uIkajsw123456789064hxk1fc3efmnva', 'assignments', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-22 15:40:57', 'Windows 10 | Chrome | ::1', 'Assignment Grade has been changed.'),
(33, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl', 'uIkajsw123456789064hxk1fc3efmnva', 'assignments', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-22 15:40:57', 'Windows 10 | Chrome | ::1', 'Assignment description has been changed.'),
(34, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl', 'uIkajsw123456789064hxk1fc3efmnva', 'assignments', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-22 15:45:10', 'Windows 10 | Chrome | ::1', 'Admin User Account updated the assignment details'),
(35, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl', 'uIkajsw123456789064hxk1fc3efmnva', 'assignments', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-22 15:50:31', 'Windows 10 | Chrome | ::1', 'Admin User Account updated the assignment details'),
(36, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl', 'uIkajsw123456789064hxk1fc3efmnva', 'assignments', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '&lt;div&gt;&lt;!--block--&gt;This is a test assignment&lt;/div&gt;', '2020-12-22 15:50:32', 'Windows 10 | Chrome | ::1', 'Assignment description has been changed.'),
(37, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl', 'uIkajsw123456789064hxk1fc3efmnva', 'Comment', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-22 16:22:12', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> left a comment on this.'),
(38, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl', 'uIkajsw123456789064hxk1fc3efmnva', 'Comments Count', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-22 16:22:12', 'Windows 10 | Chrome | ::1', 'Number of comments is set to 1.'),
(39, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Comment', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-22 17:24:00', 'Windows 10 | Chrome | ::1', '<strong>Grace Obeng-Yeboah</strong> left a comment on this.'),
(40, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Comments Count', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-22 17:24:00', 'Windows 10 | Chrome | ::1', 'Number of comments is set to 2.'),
(41, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl', 'uIkajsw123456789064hxk1fc3efmnva', 'Comment', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-22 17:25:28', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> left a comment on this.'),
(42, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl', 'uIkajsw123456789064hxk1fc3efmnva', 'Comments Count', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-22 17:25:28', 'Windows 10 | Chrome | ::1', 'Number of comments is set to 3.'),
(43, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Comment', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-22 17:26:05', 'Windows 10 | Chrome | ::1', '<strong>Grace Obeng-Yeboah</strong> left a comment on this.'),
(44, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Comments Count', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-22 17:26:05', 'Windows 10 | Chrome | ::1', 'Number of comments is set to 4.'),
(45, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Comment', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-22 17:30:24', 'Windows 10 | Chrome | ::1', '<strong>Obeng Emmanuel Hyde</strong> left a comment on this.'),
(46, 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Comments Count', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-22 17:30:24', 'Windows 10 | Chrome | ::1', 'Number of comments is set to 5.'),
(47, '', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-23 07:53:07', 'Windows 10 | Chrome | ::1', '<strong>Obeng Emmanuel Hyde</strong> added a new endpoint: <strong>assignments/reopen</strong> to the resource: <strong>assignments</strong>.'),
(48, 'cnl54rkmwfpovdqqojdzwh3rauig7enz', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"100\",\"item_id\":\"cnl54rkmwfpovdqqojdzwh3rauig7enz\",\"version\":\"v1\",\"resource\":\"assignments\",\"endpoint\":\"assignments\\/add\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"assignment_type\\\":\\\"required - The type of assignment type to upload (multiple_choice or upload_file_attachment)\\\",\\\"assignment_title\\\":\\\"required - The title of the assignment\\\",\\\"description\\\":\\\"Any additional instructions added to the assignment\\\",\\\"grade\\\":\\\"required - The grade for this assignment\\\",\\\"date_due\\\":\\\"required - The date on which the assignment is due.\\\",\\\"time_due\\\":\\\"The time for submission\\\",\\\"assigned_to\\\":\\\"This determines whether to assign the assignment to all students in the class or to specific students\\\",\\\"assigned_to_list\\\":\\\"This is required when you decide to assign the assignment to specific students.\\\",\\\"class_id\\\":\\\"required - The id of the class to assign the assignment\\\",\\\"course_id\\\":\\\"required - The unique id of the course to link this assignment.\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-12-21 07:53:09\",\"last_updated\":\"2020-12-21 08:51:52\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2020-12-23 07:57:22', 'Windows 10 | Chrome | ::1', '<strong>Obeng Emmanuel Hyde</strong> updated the endpoint.'),
(49, 'qpenjlpgilhwjkyszofuhub3ocwmtebr', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"106\",\"item_id\":\"qpenjlpgilhwjkyszofuhub3ocwmtebr\",\"version\":\"v1\",\"resource\":\"assignments\",\"endpoint\":\"assignments\\/update\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"assignment_type\\\":\\\"required - The type of assignment type to upload (multiple_choice or upload_file_attachment)\\\",\\\"assignment_title\\\":\\\"required - The title of the assignment\\\",\\\"description\\\":\\\"Any additional instructions added to the assignment\\\",\\\"grade\\\":\\\"required - The grade for this assignment\\\",\\\"date_due\\\":\\\"required - The date on which the assignment is due.\\\",\\\"time_due\\\":\\\"The time for submission\\\",\\\"assigned_to\\\":\\\"This determines whether to assign the assignment to all students in the class or to specific students\\\",\\\"assigned_to_list\\\":\\\"This is required when you decide to assign the assignment to specific students.\\\",\\\"class_id\\\":\\\"required - The id of the class to assign the assignment\\\",\\\"course_id\\\":\\\"required - The unique id of the course to link this assignment.\\\",\\\"assignment_id\\\":\\\"required - The unique id of the assignment to update the record.\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-12-22 14:59:52\",\"last_updated\":\"2020-12-22 15:21:49\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2020-12-23 07:57:36', 'Windows 10 | Chrome | ::1', '<strong>Obeng Emmanuel Hyde</strong> updated the endpoint.'),
(50, 'qpenjlpgilhwjkyszofuhub3ocwmtebr', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"106\",\"item_id\":\"qpenjlpgilhwjkyszofuhub3ocwmtebr\",\"version\":\"v1\",\"resource\":\"assignments\",\"endpoint\":\"assignments\\/update\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"assignment_type\\\":\\\"required - The type of assignment type to upload (multiple_choice or file_attachment)\\\",\\\"assignment_title\\\":\\\"required - The title of the assignment\\\",\\\"description\\\":\\\"Any additional instructions added to the assignment\\\",\\\"grade\\\":\\\"required - The grade for this assignment\\\",\\\"date_due\\\":\\\"required - The date on which the assignment is due.\\\",\\\"time_due\\\":\\\"The time for submission\\\",\\\"assigned_to\\\":\\\"This determines whether to assign the assignment to all students in the class or to specific students\\\",\\\"assigned_to_list\\\":\\\"This is required when you decide to assign the assignment to specific students.\\\",\\\"class_id\\\":\\\"required - The id of the class to assign the assignment\\\",\\\"course_id\\\":\\\"required - The unique id of the course to link this assignment.\\\",\\\"assignment_id\\\":\\\"required - The unique id of the assignment to update the record.\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-12-22 14:59:52\",\"last_updated\":\"2020-12-23 07:57:36\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\"}', '2020-12-23 08:09:48', 'Windows 10 | Chrome | ::1', '<strong>Obeng Emmanuel Hyde</strong> updated the endpoint.'),
(51, 'cnl54rkmwfpovdqqojdzwh3rauig7enz', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"100\",\"item_id\":\"cnl54rkmwfpovdqqojdzwh3rauig7enz\",\"version\":\"v1\",\"resource\":\"assignments\",\"endpoint\":\"assignments\\/add\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"assignment_type\\\":\\\"required - The type of assignment type to upload (multiple_choice or file_attachment)\\\",\\\"assignment_title\\\":\\\"required - The title of the assignment\\\",\\\"description\\\":\\\"Any additional instructions added to the assignment\\\",\\\"grade\\\":\\\"required - The grade for this assignment\\\",\\\"date_due\\\":\\\"required - The date on which the assignment is due.\\\",\\\"time_due\\\":\\\"The time for submission\\\",\\\"assigned_to\\\":\\\"This determines whether to assign the assignment to all students in the class or to specific students\\\",\\\"assigned_to_list\\\":\\\"This is required when you decide to assign the assignment to specific students.\\\",\\\"class_id\\\":\\\"required - The id of the class to assign the assignment\\\",\\\"course_id\\\":\\\"required - The unique id of the course to link this assignment.\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-12-21 07:53:09\",\"last_updated\":\"2020-12-23 07:57:22\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\"}', '2020-12-23 08:13:32', 'Windows 10 | Chrome | ::1', '<strong>Obeng Emmanuel Hyde</strong> updated the endpoint.'),
(52, 'yGBuEJwioD2PZKanN7rzLpI6C3SR9TQF', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'assignments', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-23 08:13:39', 'Windows 10 | Chrome | ::1', 'Obeng Emmanuel Hyde created a new Assignment: A Quiz Like Assignment'),
(53, '', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-23 15:10:23', 'Windows 10 | Chrome | ::1', '<strong>Teacher Account</strong> added a new endpoint: <strong>assignments/add_question</strong> to the resource: <strong>assignments</strong>.'),
(54, 'fsf9xl0ykqcje1zmhiadbxnctwgg2dn3', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"108\",\"item_id\":\"fsf9xl0ykqcje1zmhiadbxnctwgg2dn3\",\"version\":\"v1\",\"resource\":\"assignments\",\"endpoint\":\"assignments\\/add_question\",\"method\":\"POST\",\"description\":\"This endpoint is used to both add and update a question under a specific assignment\",\"parameter\":\"{\\\"option_a\\\":\\\"The value for Option A\\\",\\\"option_b\\\":\\\"The value for Option B\\\",\\\"option_c\\\":\\\"The value for Option C\\\",\\\"option_d\\\":\\\"The value for Option D\\\",\\\"option_e\\\":\\\"The value for Option E\\\",\\\"question\\\":\\\"The question detail\\\",\\\"answer_type\\\":\\\"The type of the answer to process\\\",\\\"question_id\\\":\\\"The unique id of the question\\\",\\\"assignment_id\\\":\\\"required - The assignment id\\\",\\\"difficulty\\\":\\\"The difficulty level of the question\\\",\\\"answers\\\":\\\"An array of selected options\\\",\\\"numeric_answer\\\":\\\"If the answer is numeric this should show\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-12-23 15:10:23\",\"last_updated\":\"2020-12-23 15:10:23\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"updated_by\":null}', '2020-12-23 15:26:48', 'Windows 10 | Chrome | ::1', '<strong>Teacher Account</strong> updated the endpoint.'),
(55, 'fsf9xl0ykqcje1zmhiadbxnctwgg2dn3', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"108\",\"item_id\":\"fsf9xl0ykqcje1zmhiadbxnctwgg2dn3\",\"version\":\"v1\",\"resource\":\"assignments\",\"endpoint\":\"assignments\\/add_question\",\"method\":\"POST\",\"description\":\"This endpoint is used to both add and update a question under a specific assignment\",\"parameter\":\"{\\\"option_a\\\":\\\"The value for Option A\\\",\\\"option_b\\\":\\\"The value for Option B\\\",\\\"option_c\\\":\\\"The value for Option C\\\",\\\"option_d\\\":\\\"The value for Option D\\\",\\\"option_e\\\":\\\"The value for Option E\\\",\\\"question\\\":\\\"required - The question detail\\\",\\\"answer_type\\\":\\\"The type of the answer to process\\\",\\\"question_id\\\":\\\"The unique id of the question\\\",\\\"assignment_id\\\":\\\"required - The assignment id\\\",\\\"difficulty\\\":\\\"The difficulty level of the question\\\",\\\"answers\\\":\\\"An array of selected options\\\",\\\"numeric_answer\\\":\\\"If the answer is numeric this should show\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-12-23 15:10:23\",\"last_updated\":\"2020-12-23 15:26:48\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"updated_by\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\"}', '2020-12-23 15:27:10', 'Windows 10 | Chrome | ::1', '<strong>Teacher Account</strong> updated the endpoint.'),
(56, '', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-23 21:19:12', 'Windows 10 | Chrome | ::1', '<strong>Teacher Account</strong> added a new endpoint: <strong>assignments/review_question</strong> to the resource: <strong>assignments</strong>.'),
(57, 'yGBuEJwioD2PZKanN7rzLpI6C3SR9TQF', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'assignments', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-23 22:03:52', 'Windows 10 | Chrome | ::1', 'Teacher Account updated the assignment details'),
(58, 'yGBuEJwioD2PZKanN7rzLpI6C3SR9TQF', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'assignments', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-23 22:14:48', 'Windows 10 | Chrome | ::1', 'Teacher Account updated the assignment details'),
(59, 'yGBuEJwioD2PZKanN7rzLpI6C3SR9TQF', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'assignments', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-23 22:23:52', 'Windows 10 | Chrome | ::1', 'Teacher Account updated the assignment details'),
(60, 'yGBuEJwioD2PZKanN7rzLpI6C3SR9TQF', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'assignments', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-23 22:24:05', 'Windows 10 | Chrome | ::1', 'Teacher Account updated the assignment details'),
(61, '', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-23 23:01:59', 'Windows 10 | Chrome | ::1', '<strong>Teacher Account</strong> added a new endpoint: <strong>assignments/publish</strong> to the resource: <strong>assignments</strong>.'),
(62, 'fsf9xl0ykqcje1zmhiadbxnctwgg2dn3', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"108\",\"item_id\":\"fsf9xl0ykqcje1zmhiadbxnctwgg2dn3\",\"version\":\"v1\",\"resource\":\"assignments\",\"endpoint\":\"assignments\\/add_question\",\"method\":\"POST\",\"description\":\"This endpoint is used to both add and update a question under a specific assignment\",\"parameter\":\"{\\\"option_a\\\":\\\"required - The value for Option A\\\",\\\"option_b\\\":\\\"required - The value for Option B\\\",\\\"option_c\\\":\\\"required - The value for Option C\\\",\\\"option_d\\\":\\\"The value for Option D\\\",\\\"option_e\\\":\\\"The value for Option E\\\",\\\"question\\\":\\\"required - The question detail\\\",\\\"answer_type\\\":\\\"The type of the answer to process\\\",\\\"question_id\\\":\\\"The unique id of the question\\\",\\\"assignment_id\\\":\\\"required - The assignment id\\\",\\\"difficulty\\\":\\\"The difficulty level of the question\\\",\\\"answers\\\":\\\"An array of selected options\\\",\\\"numeric_answer\\\":\\\"If the answer is numeric this should show\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-12-23 15:10:23\",\"last_updated\":\"2020-12-23 15:27:10\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"updated_by\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\"}', '2020-12-24 22:10:36', 'Windows 10 | Chrome | ::1', '<strong>Teacher Account</strong> updated the endpoint.'),
(63, '', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-25 23:05:34', 'Windows 10 | Chrome | ::1', '<strong>Grace Obeng-Yeboah</strong> added a new endpoint: <strong>assignments/save_answer</strong> to the resource: <strong>assignments</strong>.'),
(64, '9ojxbvfyqeocke3jrt0hbpgucpkztlsr', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"111\",\"item_id\":\"9ojxbvfyqeocke3jrt0hbpgucpkztlsr\",\"version\":\"v1\",\"resource\":\"assignments\",\"endpoint\":\"assignments\\/save_answer\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"question_id\\\":\\\"required - This is the unique id of the question to load\\\",\\\"answers\\\":\\\"This is the array of answers selected\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-12-25 23:05:34\",\"last_updated\":\"2020-12-25 23:05:34\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"updated_by\":null}', '2020-12-26 07:58:06', 'Windows 10 | Chrome | ::1', '<strong>Grace Obeng-Yeboah</strong> updated the endpoint.'),
(65, '', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-28 11:41:46', 'Windows 10 | Chrome | ::1', '<strong>Grace Obeng-Yeboah</strong> added a new endpoint: <strong>assignments/review_answers</strong> to the resource: <strong>assignments</strong>.'),
(66, 'yGBuEJwioD2PZKanN7rzLpI6C3SR9TQF_xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'assignment_doc', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-28 12:21:47', 'Windows 10 | Chrome | ::1', 'Grace Obeng-Yeboah handed in the assignment for auto grading by the system.'),
(67, 'yGBuEJwioD2PZKanN7rzLpI6C3SR9TQF_xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'assignment_doc', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-28 12:22:27', 'Windows 10 | Chrome | ::1', 'Grace Obeng-Yeboah handed in the assignment for auto grading by the system.'),
(68, 'yGBuEJwioD2PZKanN7rzLpI6C3SR9TQF_xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'assignment_doc', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-28 12:23:11', 'Windows 10 | Chrome | ::1', 'Grace Obeng-Yeboah handed in the assignment for auto grading by the system.'),
(69, 'yGBuEJwioD2PZKanN7rzLpI6C3SR9TQF_xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'assignment_doc', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-28 13:01:34', 'Windows 10 | Chrome | ::1', 'Grace Obeng-Yeboah handed in the assignment for auto grading by the system.'),
(70, 'yGBuEJwioD2PZKanN7rzLpI6C3SR9TQF', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Comment', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-28 13:03:15', 'Windows 10 | Chrome | ::1', '<strong>Grace Obeng-Yeboah</strong> left a comment on this.'),
(71, 'yGBuEJwioD2PZKanN7rzLpI6C3SR9TQF', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Comments Count', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-28 13:03:15', 'Windows 10 | Chrome | ::1', 'Number of comments is set to 1.'),
(72, '', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-28 20:30:47', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> added a new endpoint: <strong>attendance/log</strong> to the resource: <strong>attendance</strong>.'),
(73, '0eyocpqa958qstkvxolmn6p4bbjsxu1l', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"113\",\"item_id\":\"0eyocpqa958qstkvxolmn6p4bbjsxu1l\",\"version\":\"v1\",\"resource\":\"attendance\",\"endpoint\":\"attendance\\/log\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"date\\\":\\\"required - The date to log the attendance\\\",\\\"attendance\\\":\\\"required - This is an array of user_ids and their status\\\",\\\"user_type\\\":\\\"required - This denotes the user type to query.\\\",\\\"class_id\\\":\\\"The class id is required if the user type is student.\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-12-28 20:30:47\",\"last_updated\":\"2020-12-28 20:30:47\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":null}', '2020-12-28 22:45:20', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(74, '0eyocpqa958qstkvxolmn6p4bbjsxu1l', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"113\",\"item_id\":\"0eyocpqa958qstkvxolmn6p4bbjsxu1l\",\"version\":\"v1\",\"resource\":\"attendance\",\"endpoint\":\"attendance\\/log\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"date\\\":\\\"required - The date to log the attendance\\\",\\\"attendance\\\":\\\"required - This is an array of user_ids and their status\\\",\\\"user_type\\\":\\\"required - This denotes the user type to query.\\\",\\\"class_id\\\":\\\"The class id is required if the user type is student.\\\",\\\"finalize\\\":\\\"This parameter is set when there is the need to finalize the log\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-12-28 20:30:47\",\"last_updated\":\"2020-12-28 22:45:20\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2020-12-28 22:45:54', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(75, '0eyocpqa958qstkvxolmn6p4bbjsxu1l', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"113\",\"item_id\":\"0eyocpqa958qstkvxolmn6p4bbjsxu1l\",\"version\":\"v1\",\"resource\":\"attendance\",\"endpoint\":\"attendance\\/log\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"date\\\":\\\"required - The date to log the attendance\\\",\\\"attendance\\\":\\\"This is an array of user_ids and their status\\\",\\\"user_type\\\":\\\"This denotes the user type to query.\\\",\\\"class_id\\\":\\\"The class id is required if the user type is student.\\\",\\\"finalize\\\":\\\"This parameter is set when there is the need to finalize the log\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-12-28 20:30:47\",\"last_updated\":\"2020-12-28 22:45:54\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2020-12-29 09:16:20', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(76, '0eyocpqa958qstkvxolmn6p4bbjsxu1l', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"113\",\"item_id\":\"0eyocpqa958qstkvxolmn6p4bbjsxu1l\",\"version\":\"v1\",\"resource\":\"attendance\",\"endpoint\":\"attendance\\/log\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"date\\\":\\\"required - The date to log the attendance\\\",\\\"attendance\\\":\\\"This is an array of user_ids and their status\\\",\\\"user_type\\\":\\\"This denotes the user type to query.\\\",\\\"class_id\\\":\\\"The class id is required if the user type is student.\\\",\\\"finalize\\\":\\\"This parameter is set when there is the need to finalize the log\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-12-28 20:30:47\",\"last_updated\":\"2020-12-29 09:16:20\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2020-12-29 09:16:46', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(77, '', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-29 19:35:20', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> added a new endpoint: <strong>events/add_type</strong> to the resource: <strong>events</strong>.'),
(78, '', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-29 19:35:47', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> added a new endpoint: <strong>events/update_type</strong> to the resource: <strong>events</strong>.'),
(79, 'GECo1lL7FIP2UKy0iYMDus4w358ZRVaz', 'uIkajsw123456789064hxk1fc3efmnva', 'events_type', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-29 19:54:04', 'Windows 10 | Chrome | ::1', 'Admin Account created a new Event Type: Test Type'),
(80, 'v4yzw52dmfvsbjmfhsajoipqye63ollx', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"114\",\"item_id\":\"v4yzw52dmfvsbjmfhsajoipqye63ollx\",\"version\":\"v1\",\"resource\":\"events\",\"endpoint\":\"events\\/add_type\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"name\\\":\\\"required - The name of the type\\\",\\\"description\\\":\\\"Any additional description of the type\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-12-29 19:35:20\",\"last_updated\":\"2020-12-29 19:35:20\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":null}', '2020-12-29 19:55:47', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(81, '6d8roxdljhw2vnkwzb1et0yiahqqy4rl', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"115\",\"item_id\":\"6d8roxdljhw2vnkwzb1et0yiahqqy4rl\",\"version\":\"v1\",\"resource\":\"events\",\"endpoint\":\"events\\/update_type\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"name\\\":\\\"required - The name of the type\\\",\\\"description\\\":\\\"Any additional description of the type\\\",\\\"type_id\\\":\\\"required - The unique id of the type\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-12-29 19:35:47\",\"last_updated\":\"2020-12-29 19:35:47\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":null}', '2020-12-29 19:55:52', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(82, 'jz53lBGX46QWLxEIAMfb9ZHR7wqY1opa', 'uIkajsw123456789064hxk1fc3efmnva', 'events_type', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-29 19:56:40', 'Windows 10 | Chrome | ::1', 'Admin Account created a new Event Type: Another Test'),
(83, 'L0fdGMveJhUmruA2ljNE73pVSCqbX1iI', 'uIkajsw123456789064hxk1fc3efmnva', 'events_type', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-29 19:57:36', 'Windows 10 | Chrome | ::1', 'Admin Account created a new Event Type: Another Test'),
(84, 'gIKkUqhAQtyWXcPCd6z5m0JYnMoaFxDu', 'uIkajsw123456789064hxk1fc3efmnva', 'events_type', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-29 19:58:09', 'Windows 10 | Chrome | ::1', 'Admin Account created a new Event Type: First Type'),
(85, 'WABbq4NLFRwStaOeXDQvsxrGZMCzf0gV', 'uIkajsw123456789064hxk1fc3efmnva', 'events_type', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-29 19:58:43', 'Windows 10 | Chrome | ::1', 'Admin Account created a new Event Type: Second Event Test'),
(86, 'gIKkUqhAQtyWXcPCd6z5m0JYnMoaFxDu', 'uIkajsw123456789064hxk1fc3efmnva', 'events_type', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"1\",\"client_id\":\"LKJAFD94R\",\"item_id\":\"gIKkUqhAQtyWXcPCd6z5m0JYnMoaFxDu\",\"name\":\"First Type\",\"description\":\"This is the first type of event that i am creating and insert\",\"icon\":null,\"status\":\"1\"}', '2020-12-29 21:01:47', 'Windows 10 | Chrome | ::1', 'Admin Account successfully updated the event type: First Type Updated'),
(87, 'lbFCqo8ajNIDZhASz6if5BWpr1dYwt2e', 'uIkajsw123456789064hxk1fc3efmnva', 'events_type', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-29 21:05:32', 'Windows 10 | Chrome | ::1', 'Admin Account created a new Event Type: Another Item'),
(88, 'miEKCQ5BwD20ovknlpUxXz4g78bAdRFj', 'uIkajsw123456789064hxk1fc3efmnva', 'events_type', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-29 21:06:12', 'Windows 10 | Chrome | ::1', 'Admin Account created a new Event Type: Another one'),
(89, 'WABbq4NLFRwStaOeXDQvsxrGZMCzf0gV', 'uIkajsw123456789064hxk1fc3efmnva', 'events_type', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"2\",\"client_id\":\"LKJAFD94R\",\"item_id\":\"WABbq4NLFRwStaOeXDQvsxrGZMCzf0gV\",\"name\":\"Second Event Test\",\"description\":\"This is the second event type. The next trial will be to list and update the event types that has already been created and inserted into the system.\",\"icon\":null,\"status\":\"1\"}', '2020-12-29 21:06:25', 'Windows 10 | Chrome | ::1', 'Admin Account successfully updated the event type: Second Event Test'),
(90, 'gIKkUqhAQtyWXcPCd6z5m0JYnMoaFxDu', 'uIkajsw123456789064hxk1fc3efmnva', 'events_type', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"1\",\"client_id\":\"LKJAFD94R\",\"item_id\":\"gIKkUqhAQtyWXcPCd6z5m0JYnMoaFxDu\",\"name\":\"First Type Updated\",\"description\":\"This is the first type of event that i am creating and insert\",\"icon\":null,\"status\":\"1\"}', '2020-12-29 21:06:40', 'Windows 10 | Chrome | ::1', 'Admin Account successfully updated the event type: First Type Updated'),
(91, 'fhfotj03yx9prjo6cndilzebq2scp8w5', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"96\",\"item_id\":\"fhfotj03yx9prjo6cndilzebq2scp8w5\",\"version\":\"v1\",\"resource\":\"users\",\"endpoint\":\"users\\/guardian_add\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"guardian_id\\\":\\\"required - The unique id of the user to update\\\",\\\"gender\\\":\\\"The gender of the user\\\",\\\"image\\\":\\\"The display picture of the guardian\\\",\\\"fullname\\\":\\\"required - The fullname of the guardian\\\",\\\"date_of_birth\\\":\\\"The date of birth of the guardian\\\",\\\"email\\\":\\\"The email address\\\",\\\"contact\\\":\\\"required - The primary contact of the user\\\",\\\"contact_2\\\":\\\"The secondary contact of the user \\\",\\\"address\\\":\\\"The postal address\\\",\\\"residence\\\":\\\"The place of residence\\\",\\\"country\\\":\\\"The country of the user\\\",\\\"employer\\\":\\\"The name of the employer (company name)\\\",\\\"occupation\\\":\\\"The profession of the user\\\",\\\"description\\\":\\\"Any additional information of the user\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-12-17 09:50:16\",\"last_updated\":\"2020-12-17 09:57:57\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2020-12-29 21:56:04', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(92, 'zbdo1unr04lrhlpdfyqkwhg9vymwnzxe', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"95\",\"item_id\":\"zbdo1unr04lrhlpdfyqkwhg9vymwnzxe\",\"version\":\"v1\",\"resource\":\"users\",\"endpoint\":\"users\\/guardian_update\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"guardian_id\\\":\\\"required - The unique id of the user to update\\\",\\\"gender\\\":\\\"The gender of the user\\\",\\\"image\\\":\\\"The display picture of the guardian\\\",\\\"fullname\\\":\\\"The fullname of the guardian\\\",\\\"date_of_birth\\\":\\\"The date of birth of the guardian\\\",\\\"email\\\":\\\"The email address\\\",\\\"contact\\\":\\\"The primary contact of the user\\\",\\\"contact_2\\\":\\\"The secondary contact of the user \\\",\\\"address\\\":\\\"The postal address\\\",\\\"residence\\\":\\\"The place of residence\\\",\\\"country\\\":\\\"The country of the user\\\",\\\"employer\\\":\\\"The name of the employer (company name)\\\",\\\"occupation\\\":\\\"The profession of the user\\\",\\\"description\\\":\\\"Any additional information of the user\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-12-17 09:49:34\",\"last_updated\":\"2020-12-17 09:57:47\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2020-12-29 21:56:10', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(93, '54693872', 'uIkajsw123456789064hxk1fc3efmnva', 'guardian', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-29 21:59:31', 'Windows 10 | Chrome | ::1', 'Admin Account updated the guardian record.'),
(94, '54693872', 'uIkajsw123456789064hxk1fc3efmnva', 'guardian', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-29 21:59:31', 'Windows 10 | Chrome | ::1', 'Guardian gender was changed from '),
(95, '54693872', 'uIkajsw123456789064hxk1fc3efmnva', 'guardian', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-29 22:00:15', 'Windows 10 | Chrome | ::1', 'Admin Account updated the guardian record.'),
(96, '54693872', 'uIkajsw123456789064hxk1fc3efmnva', 'guardian', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '', '2020-12-29 22:00:15', 'Windows 10 | Chrome | ::1', 'Guardian gender was changed from '),
(97, '54693872', 'uIkajsw123456789064hxk1fc3efmnva', 'guardian', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-29 22:00:44', 'Windows 10 | Chrome | ::1', 'Admin Account updated the guardian record.');
INSERT INTO `users_activity_logs` (`id`, `item_id`, `user_id`, `subject`, `source`, `previous_record`, `date_recorded`, `user_agent`, `description`) VALUES
(98, '54693872', 'uIkajsw123456789064hxk1fc3efmnva', 'guardian', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '', '2020-12-29 22:00:44', 'Windows 10 | Chrome | ::1', 'Guardian gender was changed from '),
(99, '54693872', 'uIkajsw123456789064hxk1fc3efmnva', 'guardian', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-29 22:00:44', 'Windows 10 | Chrome | ::1', 'Guardian employer was changed from '),
(100, '54693872', 'uIkajsw123456789064hxk1fc3efmnva', 'guardian', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-29 22:00:44', 'Windows 10 | Chrome | ::1', 'Guardian occupation was changed from '),
(101, '54693872', 'uIkajsw123456789064hxk1fc3efmnva', 'guardian', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-29 22:00:44', 'Windows 10 | Chrome | ::1', 'Guardian description was changed from '),
(102, 'g1s0ypnf6ywmsineoxcruivtl4w9auqe', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"15\",\"item_id\":\"g1s0ypnf6ywmsineoxcruivtl4w9auqe\",\"version\":\"v1\",\"resource\":\"users\",\"endpoint\":\"users\\/add\",\"method\":\"POST\",\"description\":\"Add a new user account\",\"parameter\":\"{\\\"firstname\\\":\\\"required - The firstname of the user\\\",\\\"client_id\\\":\\\"This is a Unique of the user that is been created.\\\",\\\"lastname\\\":\\\"required - The lastname of the user\\\",\\\"othername\\\":\\\"The othernames of the user\\\",\\\"gender\\\":\\\"The gender of the user\\\",\\\"date_of_birth\\\":\\\"The date of birth\\\",\\\"email\\\":\\\"The email address of the user\\\",\\\"phone\\\":\\\"Contact number of the user\\\",\\\"phone_2\\\":\\\"Secondary contact number\\\",\\\"address\\\":\\\"The address of the user\\\",\\\"residence\\\":\\\"The place of residence\\\",\\\"nationality\\\":\\\"The nationality of the user\\\",\\\"country\\\":\\\"The country id of the user\\\",\\\"description\\\":\\\"Any additional information of the user\\\",\\\"user_id\\\":\\\"The id of the user\\\",\\\"employer\\\":\\\"The name of the user employer\\\",\\\"occupation\\\":\\\"The occupation of the user\\\",\\\"position\\\":\\\"The position of the user\\\",\\\"access_level\\\":\\\"The access permission id of the user.\\\",\\\"department\\\":\\\"The department of the user\\\",\\\"unique_id\\\":\\\"The unique id of the user\\\",\\\"section\\\":\\\"The section of the user\\\",\\\"class_id\\\":\\\"The class id of the user\\\",\\\"blood_group\\\":\\\"The blood group of the user\\\",\\\"guardian_info\\\":\\\"An array of the guardian information\\\",\\\"enrollment_date\\\":\\\"The date on which the user was enrolled\\\",\\\"user_type\\\":\\\"required - The type of the user to add\\\",\\\"image\\\":\\\"Image of the user\\\",\\\"academic_year\\\":\\\"The academic year on which the student was enrolled\\\",\\\"academic_term\\\":\\\"The term within which the student was enrolled\\\",\\\"status\\\":\\\"The status of the user\\\",\\\"username\\\":\\\"The username of the user for login purposes.\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-09-19 07:17:49\",\"last_updated\":\"2020-12-10 16:54:39\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"tgxuwdwkdjr58mg64hxk1fc3efmnvata\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2020-12-29 22:05:22', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(103, 'js7u9uwbmlnhccmxtpya4nwqk5hvgkag', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"11\",\"item_id\":\"js7u9uwbmlnhccmxtpya4nwqk5hvgkag\",\"version\":\"v1\",\"resource\":\"users\",\"endpoint\":\"users\\/update\",\"method\":\"POST\",\"description\":\"\'This endpoint is used for updating the information of the user.\'\",\"parameter\":\"{\\\"firstname\\\":\\\"required - The firstname of the user\\\",\\\"lastname\\\":\\\"required - The lastname of the user\\\",\\\"othername\\\":\\\"The othernames of the user\\\",\\\"client_id\\\":\\\"This is a Unique of the user that is been created.\\\",\\\"gender\\\":\\\"The gender of the user\\\",\\\"date_of_birth\\\":\\\"The date of birth\\\",\\\"email\\\":\\\"The email address of the user\\\",\\\"phone\\\":\\\"Contact number of the user\\\",\\\"phone_2\\\":\\\"Secondary contact number\\\",\\\"address\\\":\\\"The address of the user\\\",\\\"residence\\\":\\\"The place of residence\\\",\\\"nationality\\\":\\\"The nationality of the user\\\",\\\"country\\\":\\\"The country id of the user\\\",\\\"description\\\":\\\"Any additional information of the user\\\",\\\"position\\\":\\\"The position of the user\\\",\\\"user_id\\\":\\\"The id of the user\\\",\\\"occupation\\\":\\\"The occupation of the user\\\",\\\"employer\\\":\\\"The name of the users employer\\\",\\\"access_level\\\":\\\"The access permission id of the user.\\\",\\\"department\\\":\\\"The department of the user\\\",\\\"unique_id\\\":\\\"The unique id of the user\\\",\\\"section\\\":\\\"The section of the user\\\",\\\"class_id\\\":\\\"The class id of the user\\\",\\\"blood_group\\\":\\\"The blood group of the user\\\",\\\"guardian_info\\\":\\\"An array of the guardian information\\\",\\\"enrollment_date\\\":\\\"The date on which the user was enrolled\\\",\\\"user_type\\\":\\\"The type of the user to add\\\",\\\"image\\\":\\\"Image of the user\\\",\\\"academic_year\\\":\\\"The academic year on which the student was enrolled\\\",\\\"academic_term\\\":\\\"The term within which the student was enrolled\\\",\\\"username\\\":\\\"The username of the user for login purposes.\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-09-18 09:40:19\",\"last_updated\":\"2020-12-10 16:54:58\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"tgxuwdwkdjr58mg64hxk1fc3efmnvata\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2020-12-29 22:05:37', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(104, 'js7u9uwbmlnhccmxtpya4nwqk5hvgkag', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"11\",\"item_id\":\"js7u9uwbmlnhccmxtpya4nwqk5hvgkag\",\"version\":\"v1\",\"resource\":\"users\",\"endpoint\":\"users\\/update\",\"method\":\"POST\",\"description\":\"\'This endpoint is used for updating the information of the user.\'\",\"parameter\":\"{\\\"firstname\\\":\\\"required - The firstname of the user\\\",\\\"lastname\\\":\\\"required - The lastname of the user\\\",\\\"othername\\\":\\\"The othernames of the user\\\",\\\"client_id\\\":\\\"This is a Unique of the user that is been created.\\\",\\\"gender\\\":\\\"The gender of the user\\\",\\\"date_of_birth\\\":\\\"The date of birth\\\",\\\"email\\\":\\\"The email address of the user\\\",\\\"phone\\\":\\\"Contact number of the user\\\",\\\"phone_2\\\":\\\"Secondary contact number\\\",\\\"address\\\":\\\"The address of the user\\\",\\\"residence\\\":\\\"The place of residence\\\",\\\"nationality\\\":\\\"The nationality of the user\\\",\\\"country\\\":\\\"The country id of the user\\\",\\\"description\\\":\\\"Any additional information of the user\\\",\\\"position\\\":\\\"The position of the user\\\",\\\"user_id\\\":\\\"The id of the user\\\",\\\"occupation\\\":\\\"The occupation of the user\\\",\\\"employer\\\":\\\"The name of the users employer\\\",\\\"access_level\\\":\\\"The access permission id of the user.\\\",\\\"department\\\":\\\"The department of the user\\\",\\\"unique_id\\\":\\\"The unique id of the user\\\",\\\"section\\\":\\\"The section of the user\\\",\\\"class_id\\\":\\\"The class id of the user\\\",\\\"blood_group\\\":\\\"The blood group of the user\\\",\\\"guardian_info\\\":\\\"An array of the guardian information\\\",\\\"enrollment_date\\\":\\\"The date on which the user was enrolled\\\",\\\"user_type\\\":\\\"The type of the user to add\\\",\\\"image\\\":\\\"Image of the user\\\",\\\"academic_year\\\":\\\"The academic year on which the student was enrolled\\\",\\\"academic_term\\\":\\\"The term within which the student was enrolled\\\",\\\"username\\\":\\\"The username of the user for login purposes.\\\",\\\"previous_school\\\":\\\"This is applicable for students only\\\",\\\"previous_school_qualification\\\":\\\"Applicable for students only\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-09-18 09:40:19\",\"last_updated\":\"2020-12-29 22:05:37\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"tgxuwdwkdjr58mg64hxk1fc3efmnvata\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2020-12-29 22:07:54', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(105, 'js7u9uwbmlnhccmxtpya4nwqk5hvgkag', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"11\",\"item_id\":\"js7u9uwbmlnhccmxtpya4nwqk5hvgkag\",\"version\":\"v1\",\"resource\":\"users\",\"endpoint\":\"users\\/update\",\"method\":\"POST\",\"description\":\"\'This endpoint is used for updating the information of the user.\'\",\"parameter\":\"{\\\"firstname\\\":\\\"required - The firstname of the user\\\",\\\"lastname\\\":\\\"required - The lastname of the user\\\",\\\"othername\\\":\\\"The othernames of the user\\\",\\\"client_id\\\":\\\"This is a Unique of the user that is been created.\\\",\\\"gender\\\":\\\"The gender of the user\\\",\\\"date_of_birth\\\":\\\"The date of birth\\\",\\\"email\\\":\\\"The email address of the user\\\",\\\"phone\\\":\\\"Contact number of the user\\\",\\\"phone_2\\\":\\\"Secondary contact number\\\",\\\"address\\\":\\\"The address of the user\\\",\\\"residence\\\":\\\"The place of residence\\\",\\\"nationality\\\":\\\"The nationality of the user\\\",\\\"country\\\":\\\"The country id of the user\\\",\\\"description\\\":\\\"Any additional information of the user\\\",\\\"position\\\":\\\"The position of the user\\\",\\\"user_id\\\":\\\"The id of the user\\\",\\\"occupation\\\":\\\"The occupation of the user\\\",\\\"employer\\\":\\\"The name of the users employer\\\",\\\"access_level\\\":\\\"The access permission id of the user.\\\",\\\"department\\\":\\\"The department of the user\\\",\\\"unique_id\\\":\\\"The unique id of the user\\\",\\\"section\\\":\\\"The section of the user\\\",\\\"class_id\\\":\\\"The class id of the user\\\",\\\"blood_group\\\":\\\"The blood group of the user\\\",\\\"guardian_info\\\":\\\"An array of the guardian information\\\",\\\"enrollment_date\\\":\\\"The date on which the user was enrolled\\\",\\\"user_type\\\":\\\"The type of the user to add\\\",\\\"image\\\":\\\"Image of the user\\\",\\\"academic_year\\\":\\\"The academic year on which the student was enrolled\\\",\\\"academic_term\\\":\\\"The term within which the student was enrolled\\\",\\\"username\\\":\\\"The username of the user for login purposes.\\\",\\\"previous_school\\\":\\\"This is applicable for students only\\\",\\\"previous_school_qualification\\\":\\\"Applicable for students only\\\",\\\"previous_school_remarks\\\":\\\"Any remarks supplied by previous school from which student is coming from\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-09-18 09:40:19\",\"last_updated\":\"2020-12-29 22:07:54\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"tgxuwdwkdjr58mg64hxk1fc3efmnvata\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2020-12-29 22:07:56', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(106, 'g1s0ypnf6ywmsineoxcruivtl4w9auqe', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"15\",\"item_id\":\"g1s0ypnf6ywmsineoxcruivtl4w9auqe\",\"version\":\"v1\",\"resource\":\"users\",\"endpoint\":\"users\\/add\",\"method\":\"POST\",\"description\":\"Add a new user account\",\"parameter\":\"{\\\"firstname\\\":\\\"required - The firstname of the user\\\",\\\"client_id\\\":\\\"This is a Unique of the user that is been created.\\\",\\\"lastname\\\":\\\"required - The lastname of the user\\\",\\\"othername\\\":\\\"The othernames of the user\\\",\\\"gender\\\":\\\"The gender of the user\\\",\\\"date_of_birth\\\":\\\"The date of birth\\\",\\\"email\\\":\\\"The email address of the user\\\",\\\"phone\\\":\\\"Contact number of the user\\\",\\\"phone_2\\\":\\\"Secondary contact number\\\",\\\"address\\\":\\\"The address of the user\\\",\\\"residence\\\":\\\"The place of residence\\\",\\\"nationality\\\":\\\"The nationality of the user\\\",\\\"country\\\":\\\"The country id of the user\\\",\\\"description\\\":\\\"Any additional information of the user\\\",\\\"user_id\\\":\\\"The id of the user\\\",\\\"employer\\\":\\\"The name of the user employer\\\",\\\"occupation\\\":\\\"The occupation of the user\\\",\\\"position\\\":\\\"The position of the user\\\",\\\"access_level\\\":\\\"The access permission id of the user.\\\",\\\"department\\\":\\\"The department of the user\\\",\\\"unique_id\\\":\\\"The unique id of the user\\\",\\\"section\\\":\\\"The section of the user\\\",\\\"class_id\\\":\\\"The class id of the user\\\",\\\"blood_group\\\":\\\"The blood group of the user\\\",\\\"guardian_info\\\":\\\"An array of the guardian information\\\",\\\"enrollment_date\\\":\\\"The date on which the user was enrolled\\\",\\\"user_type\\\":\\\"required - The type of the user to add\\\",\\\"image\\\":\\\"Image of the user\\\",\\\"academic_year\\\":\\\"The academic year on which the student was enrolled\\\",\\\"academic_term\\\":\\\"The term within which the student was enrolled\\\",\\\"status\\\":\\\"The status of the user\\\",\\\"username\\\":\\\"The username of the user for login purposes.\\\",\\\"previous_school\\\":\\\"This is applicable for students only\\\",\\\"previous_school_qualification\\\":\\\"Applicable for students only\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-09-19 07:17:49\",\"last_updated\":\"2020-12-29 22:05:22\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"tgxuwdwkdjr58mg64hxk1fc3efmnvata\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2020-12-29 22:08:02', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(107, '', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-29 23:19:41', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> added a new endpoint: <strong>users/modify_wardguardian</strong> to the resource: <strong>users</strong>.'),
(108, '', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-30 09:22:11', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> added a new endpoint: <strong>events/add</strong> to the resource: <strong>events</strong>.'),
(109, '', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-30 09:23:01', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> added a new endpoint: <strong>events/update</strong> to the resource: <strong>events</strong>.'),
(110, 'PI9tVxCQrLsWJ8HT3wuMigBy0Ro5ZcDf', 'uIkajsw123456789064hxk1fc3efmnva', 'events_type', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-30 09:27:34', 'Windows 10 | Chrome | ::1', 'Admin Account created a new Event Type: Append to Dropdown'),
(111, 'kTnKvCEymV3JcgjadBAWe0x6iqPpOH18', 'uIkajsw123456789064hxk1fc3efmnva', 'events_type', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-30 09:28:31', 'Windows 10 | Chrome | ::1', 'Admin Account created a new Event Type: New Append to Dropdown'),
(112, 'z2JfSOdGTI4Pbxh8yV3UKY1LcMN7rgl6', 'uIkajsw123456789064hxk1fc3efmnva', 'events', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-30 10:14:50', 'Windows 10 | Chrome | ::1', 'Admin Account created a new Event with title <strong>Vacation Starts</strong> to be held on 2020-12-30.'),
(113, '', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-30 10:17:54', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> added a new endpoint: <strong>events/list</strong> to the resource: <strong>events</strong>.'),
(114, '', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-30 10:18:51', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> added a new endpoint: <strong>events/types_list</strong> to the resource: <strong>events</strong>.'),
(115, 'ibxuohpuwjz4b96jzalqs0wepmnngctg', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"120\",\"item_id\":\"ibxuohpuwjz4b96jzalqs0wepmnngctg\",\"version\":\"v1\",\"resource\":\"events\",\"endpoint\":\"events\\/types_list\",\"method\":\"GET\",\"description\":\"\",\"parameter\":\"{\\\"limit\\\":\\\"The number of rows to limit the result\\\",\\\"type_id\\\":\\\"The unique id of the event type\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-12-30 10:18:51\",\"last_updated\":\"2020-12-30 10:18:51\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":null}', '2020-12-30 10:19:22', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(116, 'ibxuohpuwjz4b96jzalqs0wepmnngctg', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"120\",\"item_id\":\"ibxuohpuwjz4b96jzalqs0wepmnngctg\",\"version\":\"v1\",\"resource\":\"events\",\"endpoint\":\"events\\/types_list\",\"method\":\"GET\",\"description\":\"\",\"parameter\":\"{\\\"limit\\\":\\\"The number of rows to limit the result\\\",\\\"type_id\\\":\\\"The unique id of the event type\\\",\\\"show_events\\\":\\\"When parsed it will also list all events found under the type\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-12-30 10:18:51\",\"last_updated\":\"2020-12-30 10:19:22\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2020-12-30 10:19:27', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(117, 'o5pmu71qxrjp8moyj6gzgawdtbb2kysv', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"119\",\"item_id\":\"o5pmu71qxrjp8moyj6gzgawdtbb2kysv\",\"version\":\"v1\",\"resource\":\"events\",\"endpoint\":\"events\\/list\",\"method\":\"GET\",\"description\":\"\",\"parameter\":\"{\\\"limit\\\":\\\"The number of rows to limit the result\\\",\\\"event_id\\\":\\\"The unique id of the event\\\",\\\"event_date\\\":\\\"The date on which the event will be held\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-12-30 10:17:54\",\"last_updated\":\"2020-12-30 10:17:54\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":null}', '2020-12-30 10:20:46', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(118, '6z8t4vx5rhnhkmorjcwezxbdsq237g9b', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"117\",\"item_id\":\"6z8t4vx5rhnhkmorjcwezxbdsq237g9b\",\"version\":\"v1\",\"resource\":\"events\",\"endpoint\":\"events\\/add\",\"method\":\"POST\",\"description\":\"This endpoint adds a new event into the system.\",\"parameter\":\"{\\\"title\\\":\\\"required - The title of the event\\\",\\\"type\\\":\\\"required - The type of event to add\\\",\\\"audience\\\":\\\"required - The audience of the event\\\",\\\"date\\\":\\\"required - The date of the event\\\",\\\"holiday\\\":\\\"To ascertain whether the event is a holiday or not\\\",\\\"event_image\\\":\\\"Any image to attach to this event\\\",\\\"description\\\":\\\"Any additional information to be added to this event.\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-12-30 09:22:11\",\"last_updated\":\"2020-12-30 09:22:11\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":null}', '2020-12-30 10:23:28', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(119, 'g7bieqmavu2ynzzlosfw1cfsyh3ivtwp', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"118\",\"item_id\":\"g7bieqmavu2ynzzlosfw1cfsyh3ivtwp\",\"version\":\"v1\",\"resource\":\"events\",\"endpoint\":\"events\\/update\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"title\\\":\\\"required - The title of the event\\\",\\\"type\\\":\\\"The type of event to add\\\",\\\"audience\\\":\\\"The audience of the event\\\",\\\"date\\\":\\\"The date of the event\\\",\\\"holiday\\\":\\\"To ascertain whether the event is a holiday or not\\\",\\\"event_image\\\":\\\"Any image to attach to this event\\\",\\\"description\\\":\\\"Any additional information to be added to this event.\\\",\\\"event_id\\\":\\\"required - The unique id of the event\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-12-30 09:23:01\",\"last_updated\":\"2020-12-30 09:23:01\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":null}', '2020-12-30 10:23:34', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(120, 'g7bieqmavu2ynzzlosfw1cfsyh3ivtwp', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"118\",\"item_id\":\"g7bieqmavu2ynzzlosfw1cfsyh3ivtwp\",\"version\":\"v1\",\"resource\":\"events\",\"endpoint\":\"events\\/update\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"title\\\":\\\"required - The title of the event\\\",\\\"type\\\":\\\"The type of event to add\\\",\\\"audience\\\":\\\"The audience of the event\\\",\\\"date\\\":\\\"The date of the event\\\",\\\"holiday\\\":\\\"To ascertain whether the event is a holiday or not\\\",\\\"event_image\\\":\\\"Any image to attach to this event\\\",\\\"description\\\":\\\"Any additional information to be added to this event.\\\",\\\"event_id\\\":\\\"required - The unique id of the event\\\",\\\"is_mailable\\\":\\\"Specify whether this event can be emailed to the users list specified\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-12-30 09:23:01\",\"last_updated\":\"2020-12-30 10:23:34\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2020-12-30 10:24:33', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(121, 'g7bieqmavu2ynzzlosfw1cfsyh3ivtwp', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"118\",\"item_id\":\"g7bieqmavu2ynzzlosfw1cfsyh3ivtwp\",\"version\":\"v1\",\"resource\":\"events\",\"endpoint\":\"events\\/update\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"title\\\":\\\"required - The title of the event\\\",\\\"type\\\":\\\"The type of event to add\\\",\\\"audience\\\":\\\"The audience of the event\\\",\\\"date\\\":\\\"required - The date of the event\\\",\\\"holiday\\\":\\\"To ascertain whether the event is a holiday or not\\\",\\\"event_image\\\":\\\"Any image to attach to this event\\\",\\\"description\\\":\\\"Any additional information to be added to this event.\\\",\\\"event_id\\\":\\\"required - The unique id of the event\\\",\\\"is_mailable\\\":\\\"Specify whether this event can be emailed to the users list specified\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-12-30 09:23:01\",\"last_updated\":\"2020-12-30 10:24:33\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2020-12-30 10:30:46', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(122, 'ht0vuLKSAJpB6i1FzOlIrdPXEqnxw2Ne', 'uIkajsw123456789064hxk1fc3efmnva', 'events', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2020-12-30 12:05:22', 'Windows 10 | Chrome | ::1', 'Admin Account created a new Event with title <strong>Another Test Event</strong> to be held on 2020-12-01.'),
(124, 'cLnFJiO1RyGUEH6I3Ngr8qw54WxVXPjm', 'uIkajsw123456789064hxk1fc3efmnva', 'events', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-01 16:20:05', 'Windows 10 | Chrome | ::1', 'Admin Account created a new Event with title <strong>Fast Forward Test</strong> to be held on 2021-01-20.'),
(125, 'YWgvhIO4ojzGlM7mR9J1eKXFyAp3NBtn', 'uIkajsw123456789064hxk1fc3efmnva', 'events', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-01 16:21:34', 'Windows 10 | Chrome | ::1', 'Admin Account created a new Event with title <strong>Fast Forward Test</strong> to be held on 2021-01-20.'),
(126, 'A3XpN8dZ1T0xLFCqvItM5eSURQ4u6azG', 'uIkajsw123456789064hxk1fc3efmnva', 'events', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-01 16:33:22', 'Windows 10 | Chrome | ::1', 'Admin Account created a new Event with title <strong>Test</strong> to be held on 2021-01-25.'),
(127, '6d8roxdljhw2vnkwzb1et0yiahqqy4rl', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"115\",\"item_id\":\"6d8roxdljhw2vnkwzb1et0yiahqqy4rl\",\"version\":\"v1\",\"resource\":\"events\",\"endpoint\":\"events\\/update_type\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"name\\\":\\\"required - The name of the type\\\",\\\"description\\\":\\\"Any additional description of the type\\\",\\\"type_id\\\":\\\"required - The unique id of the type\\\",\\\"icon\\\":\\\"The icon to be used to represent events that falls under this category\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-12-29 19:35:47\",\"last_updated\":\"2020-12-29 19:55:52\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2021-01-01 16:37:47', 'Windows 10 | Chrome | ::1', '<strong>Teacher Account</strong> updated the endpoint.'),
(128, '6d8roxdljhw2vnkwzb1et0yiahqqy4rl', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"115\",\"item_id\":\"6d8roxdljhw2vnkwzb1et0yiahqqy4rl\",\"version\":\"v1\",\"resource\":\"events\",\"endpoint\":\"events\\/update_type\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"name\\\":\\\"required - The name of the type\\\",\\\"description\\\":\\\"Any additional description of the type\\\",\\\"type_id\\\":\\\"required - The unique id of the type\\\",\\\"icon\\\":\\\"The icon to be used to represent events that falls under this category\\\",\\\"color_code\\\":\\\"The color code for the event type\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-12-29 19:35:47\",\"last_updated\":\"2021-01-01 16:37:47\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\"}', '2021-01-01 16:37:47', 'Windows 10 | Chrome | ::1', '<strong>Teacher Account</strong> updated the endpoint.'),
(129, 'v4yzw52dmfvsbjmfhsajoipqye63ollx', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"114\",\"item_id\":\"v4yzw52dmfvsbjmfhsajoipqye63ollx\",\"version\":\"v1\",\"resource\":\"events\",\"endpoint\":\"events\\/add_type\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"name\\\":\\\"required - The name of the type\\\",\\\"description\\\":\\\"Any additional description of the type\\\",\\\"icon\\\":\\\"The icon to be used to represent events that falls under this category\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-12-29 19:35:20\",\"last_updated\":\"2020-12-29 19:55:47\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2021-01-01 16:37:51', 'Windows 10 | Chrome | ::1', '<strong>Teacher Account</strong> updated the endpoint.'),
(130, 'v4yzw52dmfvsbjmfhsajoipqye63ollx', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"114\",\"item_id\":\"v4yzw52dmfvsbjmfhsajoipqye63ollx\",\"version\":\"v1\",\"resource\":\"events\",\"endpoint\":\"events\\/add_type\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"name\\\":\\\"required - The name of the type\\\",\\\"description\\\":\\\"Any additional description of the type\\\",\\\"icon\\\":\\\"The icon to be used to represent events that falls under this category\\\",\\\"color_code\\\":\\\"The color code for the event type\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-12-29 19:35:20\",\"last_updated\":\"2021-01-01 16:37:51\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\"}', '2021-01-01 16:37:51', 'Windows 10 | Chrome | ::1', '<strong>Teacher Account</strong> updated the endpoint.'),
(131, 'PI9tVxCQrLsWJ8HT3wuMigBy0Ro5ZcDf', 'uIkajsw123456789064hxk1fc3efmnva', 'events_type', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"5\",\"client_id\":\"LKJAFD94R\",\"item_id\":\"PI9tVxCQrLsWJ8HT3wuMigBy0Ro5ZcDf\",\"name\":\"Append to Dropdown\",\"description\":\"\",\"color_code\":\"#6777ef\",\"icon\":null,\"status\":\"1\"}', '2021-01-01 16:45:00', 'Windows 10 | Chrome | ::1', 'Admin Account successfully updated the event type: Append to Dropdown'),
(132, 'gIKkUqhAQtyWXcPCd6z5m0JYnMoaFxDu', 'uIkajsw123456789064hxk1fc3efmnva', 'events_type', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"1\",\"client_id\":\"LKJAFD94R\",\"item_id\":\"gIKkUqhAQtyWXcPCd6z5m0JYnMoaFxDu\",\"name\":\"First Type Updated\",\"description\":\"This is the first type of event that i am creating and insert. Great test\",\"color_code\":\"#6777ef\",\"icon\":null,\"status\":\"1\"}', '2021-01-01 16:46:23', 'Windows 10 | Chrome | ::1', 'Admin Account successfully updated the event type: First Type Updated'),
(133, 'miEKCQ5BwD20ovknlpUxXz4g78bAdRFj', 'uIkajsw123456789064hxk1fc3efmnva', 'events_type', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"4\",\"client_id\":\"LKJAFD94R\",\"item_id\":\"miEKCQ5BwD20ovknlpUxXz4g78bAdRFj\",\"name\":\"Another one\",\"description\":\"This is the final test for the event type insertion\",\"color_code\":\"#6777ef\",\"icon\":null,\"status\":\"1\"}', '2021-01-01 16:51:56', 'Windows 10 | Chrome | ::1', 'Admin Account successfully updated the event type: Another one'),
(134, 'PI9tVxCQrLsWJ8HT3wuMigBy0Ro5ZcDf', 'uIkajsw123456789064hxk1fc3efmnva', 'events_type', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"5\",\"client_id\":\"LKJAFD94R\",\"item_id\":\"PI9tVxCQrLsWJ8HT3wuMigBy0Ro5ZcDf\",\"name\":\"Append to Dropdown\",\"description\":\"\",\"color_code\":\"#6777ef\",\"icon\":null,\"status\":\"1\"}', '2021-01-01 16:56:28', 'Windows 10 | Chrome | ::1', 'Admin Account successfully updated the event type: Append to Dropdown'),
(135, 'W27qhyLTPoZXCB14aUpIsrQkRwg38GMl', 'uIkajsw123456789064hxk1fc3efmnva', 'events', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-01 17:11:07', 'Windows 10 | Chrome | ::1', 'Admin Account created a new Event with title <strong>Test</strong> to be held on 2021-01-09.'),
(136, '9dzpOCe1FB4S7aGtPENZYsw52LMhrUXv', 'uIkajsw123456789064hxk1fc3efmnva', 'events', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-01 17:14:17', 'Windows 10 | Chrome | ::1', 'Admin Account created a new Event with title <strong>Test</strong> to be held on 2021-01-09.'),
(137, 'y2TuCfDZzedUn76pbLaAo4VYS1QksPqK', 'uIkajsw123456789064hxk1fc3efmnva', 'events', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-01 17:20:44', 'Windows 10 | Chrome | ::1', 'Admin Account created a new Event with title <strong>Test</strong> to be held on 2021-01-09.'),
(138, 'JGEV3BmRPtC7Uy05AagTLw4oe2dik1pI', 'uIkajsw123456789064hxk1fc3efmnva', 'events', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-01 17:22:34', 'Windows 10 | Chrome | ::1', 'Admin Account created a new Event with title <strong>Test</strong> to be held on 2021-01-09.'),
(139, 'gIKkUqhAQtyWXcPCd6z5m0JYnMoaFxDu', 'uIkajsw123456789064hxk1fc3efmnva', 'events_type', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"1\",\"client_id\":\"LKJAFD94R\",\"item_id\":\"gIKkUqhAQtyWXcPCd6z5m0JYnMoaFxDu\",\"name\":\"First Type Updated\",\"description\":\"This is the first type of event that i am creating and insert. Great test\",\"color_code\":\"#398b13\",\"icon\":null,\"status\":\"1\"}', '2021-01-01 17:57:07', 'Windows 10 | Chrome | ::1', 'Admin Account successfully updated the event type: First Type Updated'),
(140, 'WABbq4NLFRwStaOeXDQvsxrGZMCzf0gV', 'uIkajsw123456789064hxk1fc3efmnva', 'events_type', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"2\",\"client_id\":\"LKJAFD94R\",\"item_id\":\"WABbq4NLFRwStaOeXDQvsxrGZMCzf0gV\",\"name\":\"Second Event Test\",\"description\":\"This is the second event type. The next trial will be to list and update the event types that has already.\",\"color_code\":\"#6777ef\",\"icon\":null,\"status\":\"1\"}', '2021-01-01 18:00:07', 'Windows 10 | Chrome | ::1', 'Admin Account successfully updated the event type: Second Event Test'),
(141, 'WABbq4NLFRwStaOeXDQvsxrGZMCzf0gV', 'uIkajsw123456789064hxk1fc3efmnva', 'events_type', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"2\",\"client_id\":\"LKJAFD94R\",\"item_id\":\"WABbq4NLFRwStaOeXDQvsxrGZMCzf0gV\",\"name\":\"Second Event Test\",\"description\":\"This is the second event type. The next trial will be to list and update the event types that has already.\",\"color_code\":\"#6777ef\",\"icon\":null,\"status\":\"1\"}', '2021-01-01 18:01:13', 'Windows 10 | Chrome | ::1', 'Admin Account successfully updated the event type: Second Event Test'),
(142, 'PI9tVxCQrLsWJ8HT3wuMigBy0Ro5ZcDf', 'uIkajsw123456789064hxk1fc3efmnva', 'events_type', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"5\",\"client_id\":\"LKJAFD94R\",\"item_id\":\"PI9tVxCQrLsWJ8HT3wuMigBy0Ro5ZcDf\",\"name\":\"Append to Dropdown\",\"description\":\"This is an update the append to dropdown list query.\",\"color_code\":\"#ac39a3\",\"icon\":null,\"status\":\"1\"}', '2021-01-01 20:47:59', 'Windows 10 | Chrome | ::1', 'Admin Account successfully updated the event type: Append to Dropdown'),
(143, 'PI9tVxCQrLsWJ8HT3wuMigBy0Ro5ZcDf', 'uIkajsw123456789064hxk1fc3efmnva', 'events_type', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"5\",\"client_id\":\"LKJAFD94R\",\"item_id\":\"PI9tVxCQrLsWJ8HT3wuMigBy0Ro5ZcDf\",\"name\":\"Append to Dropdown\",\"description\":\"This is an update the append to dropdown list query.\",\"color_code\":\"#ac39a3\",\"icon\":null,\"status\":\"1\"}', '2021-01-01 20:48:26', 'Windows 10 | Chrome | ::1', 'Admin Account successfully updated the event type: Append to Dropdown'),
(144, '', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-01 21:06:58', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> added a new endpoint: <strong>records/remove</strong> to the resource: <strong>records</strong>.'),
(145, 'kTnKvCEymV3JcgjadBAWe0x6iqPpOH18', 'uIkajsw123456789064hxk1fc3efmnva', 'event_type', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-01 21:07:08', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> deleted this record from the system.'),
(146, 'miEKCQ5BwD20ovknlpUxXz4g78bAdRFj', 'uIkajsw123456789064hxk1fc3efmnva', 'event_type', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-01 21:07:19', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> deleted this record from the system.'),
(147, 'SZMsssqcccfn5cBl0ARgPCj287hym36', 'uIkajsw123456789064hxk1fc3efmnva', 'user', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-01 21:36:39', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> deleted this record from the system.'),
(148, '10', 'uIkajsw123456789064hxk1fc3efmnva', 'department', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-01 21:39:43', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> deleted this record from the system.'),
(149, 'kTnKvCEymV3JcgjadBAWe0x6iqPpOH18', 'uIkajsw123456789064hxk1fc3efmnva', 'events_type', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"6\",\"client_id\":\"LKJAFD94R\",\"item_id\":\"kTnKvCEymV3JcgjadBAWe0x6iqPpOH18\",\"name\":\"New Append to Dropdown\",\"description\":\"\",\"color_code\":\"#6777ef\",\"icon\":null,\"status\":\"1\"}', '2021-01-01 21:43:56', 'Windows 10 | Chrome | ::1', 'Admin Account successfully updated the event type: New Append to Dropdown'),
(150, 'WABbq4NLFRwStaOeXDQvsxrGZMCzf0gV', 'uIkajsw123456789064hxk1fc3efmnva', 'events_type', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"2\",\"client_id\":\"LKJAFD94R\",\"item_id\":\"WABbq4NLFRwStaOeXDQvsxrGZMCzf0gV\",\"name\":\"Second Event Test\",\"description\":\"This is the second event type. The next trial will be to list and update the event types that has already.\",\"color_code\":\"#b48608\",\"icon\":null,\"status\":\"1\"}', '2021-01-01 21:45:15', 'Windows 10 | Chrome | ::1', 'Admin Account successfully updated the event type: Second Event Test'),
(151, 'g7bieqmavu2ynzzlosfw1cfsyh3ivtwp', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"118\",\"item_id\":\"g7bieqmavu2ynzzlosfw1cfsyh3ivtwp\",\"version\":\"v1\",\"resource\":\"events\",\"endpoint\":\"events\\/update\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"title\\\":\\\"required - The title of the event\\\",\\\"type\\\":\\\"The type of event to add\\\",\\\"audience\\\":\\\"The audience of the event\\\",\\\"date\\\":\\\"required - The date of the event\\\",\\\"holiday\\\":\\\"To ascertain whether the event is a holiday or not\\\",\\\"event_image\\\":\\\"Any image to attach to this event\\\",\\\"description\\\":\\\"Any additional information to be added to this event.\\\",\\\"event_id\\\":\\\"required - The unique id of the event\\\",\\\"is_mailable\\\":\\\"Specify whether this event can be emailed to the users list specified\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-12-30 09:23:01\",\"last_updated\":\"2020-12-30 10:30:46\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2021-01-01 23:33:48', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(152, 'cLnFJiO1RyGUEH6I3Ngr8qw54WxVXPjm', 'uIkajsw123456789064hxk1fc3efmnva', 'Comment', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-01 23:54:30', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> left a comment on this.'),
(153, 'cLnFJiO1RyGUEH6I3Ngr8qw54WxVXPjm', 'uIkajsw123456789064hxk1fc3efmnva', 'Comments Count', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-01 23:54:30', 'Windows 10 | Chrome | ::1', 'Number of comments is set to 1.'),
(154, 'cLnFJiO1RyGUEH6I3Ngr8qw54WxVXPjm', 'uIkajsw123456789064hxk1fc3efmnva', 'events', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-02 00:16:33', 'Windows 10 | Chrome | ::1', 'Admin Account updated the event details.'),
(155, 'cLnFJiO1RyGUEH6I3Ngr8qw54WxVXPjm', 'uIkajsw123456789064hxk1fc3efmnva', 'events', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-02 00:18:51', 'Windows 10 | Chrome | ::1', 'Admin Account updated the event details.'),
(156, '6z8t4vx5rhnhkmorjcwezxbdsq237g9b', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"117\",\"item_id\":\"6z8t4vx5rhnhkmorjcwezxbdsq237g9b\",\"version\":\"v1\",\"resource\":\"events\",\"endpoint\":\"events\\/add\",\"method\":\"POST\",\"description\":\"This endpoint adds a new event into the system.\",\"parameter\":\"{\\\"title\\\":\\\"required - The title of the event\\\",\\\"type\\\":\\\"required - The type of event to add\\\",\\\"audience\\\":\\\"required - The audience of the event\\\",\\\"date\\\":\\\"required - The date of the event\\\",\\\"holiday\\\":\\\"To ascertain whether the event is a holiday or not\\\",\\\"event_image\\\":\\\"Any image to attach to this event\\\",\\\"description\\\":\\\"Any additional information to be added to this event.\\\",\\\"is_mailable\\\":\\\"Specify whether this event can be emailed to the users list specified\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-12-30 09:22:11\",\"last_updated\":\"2020-12-30 10:23:28\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2021-01-02 00:21:33', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(157, 'kqmcSI0sLpiH5K7njlEUPWRZ4bYfDOAd', 'uIkajsw123456789064hxk1fc3efmnva', 'events', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-02 00:22:02', 'Windows 10 | Chrome | ::1', 'Admin Account created a new Event with title <strong>Finaly</strong> to be held on 2021-01-28.'),
(158, 'kqmcSI0sLpiH5K7njlEUPWRZ4bYfDOAd', 'uIkajsw123456789064hxk1fc3efmnva', 'events', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-02 00:22:44', 'Windows 10 | Chrome | ::1', 'Admin Account updated the event details.'),
(159, 'KkZRYGUXNPnT6FzIapOLHmwe1yvtd4xB', 'uIkajsw123456789064hxk1fc3efmnva', 'events', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-02 00:24:00', 'Windows 10 | Chrome | ::1', 'Admin Account created a new Event with title <strong>Vals Day Bash</strong> to be held on 2021-02-14.'),
(160, 'VXyqaZPKNzu6ekUgSxOD8CBEjTFpAw4n', 'uIkajsw123456789064hxk1fc3efmnva', 'events', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-02 00:28:29', 'Windows 10 | Chrome | ::1', 'Admin Account created a new Event with title <strong>Resuming and Excursion</strong> to be held on 2021-02-17.'),
(161, 'cLnFJiO1RyGUEH6I3Ngr8qw54WxVXPjm', 'uIkajsw123456789064hxk1fc3efmnva', 'events', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-02 07:27:33', 'Windows 10 | Chrome | ::1', 'Admin Account updated the event details.'),
(162, 'cLnFJiO1RyGUEH6I3Ngr8qw54WxVXPjm', 'uIkajsw123456789064hxk1fc3efmnva', 'events', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-02 07:32:15', 'Windows 10 | Chrome | ::1', 'Admin Account updated the event details.'),
(163, 'cLnFJiO1RyGUEH6I3Ngr8qw54WxVXPjm', 'uIkajsw123456789064hxk1fc3efmnva', 'events', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', 'Ongoing', '2021-01-02 07:32:15', 'Windows 10 | Chrome | ::1', 'Event Status was changed from Ongoing'),
(164, 'YWgvhIO4ojzGlM7mR9J1eKXFyAp3NBtn', 'uIkajsw123456789064hxk1fc3efmnva', 'events', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-02 07:42:10', 'Windows 10 | Chrome | ::1', 'Admin Account updated the event details.'),
(165, '19', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'attendance_log', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-02 08:13:50', 'Windows 10 | Chrome | ::1', 'Teacher Account logged attendance for <strong>GENERAL ARTS 1</strong> on 2021-01-02.'),
(166, '20', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'attendance_log', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-02 08:14:48', 'Windows 10 | Chrome | ::1', 'Teacher Account logged attendance for <strong>GENERAL ARTS 2</strong> on 2021-01-02.'),
(167, '20', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'attendance_log', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\\\",\\\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\\\",\\\"unique_id\\\":\\\"AGL000022020\\\",\\\"name\\\":\\\"Grace Obeng-Yeboah\\\",\\\"email\\\":\\\"graciellaob@gmail.com\\\",\\\"phone_number\\\":\\\"00930993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\\\",\\\"unique_id\\\":\\\"AGL000042020\\\",\\\"name\\\":\\\"Frank Amponsah Amoah\\\",\\\"email\\\":\\\"frankamoah@gmail.com\\\",\\\"phone_number\\\":null,\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"SZMsssqcccfn5cBl0ARgPCj287hym36\\\",\\\"unique_id\\\":\\\"AGL000052020\\\",\\\"name\\\":\\\"Maureen Anim\\\",\\\"email\\\":\\\"jauntygirl@gmail.com\\\",\\\"phone_number\\\":\\\"0247685521\\\",\\\"state\\\":\\\"absent\\\"}]\",\"finalize\":\"0\"}', '2021-01-02 08:14:51', 'Windows 10 | Chrome | ::1', 'Teacher Account updated logged attendance for <strong>GENERAL ARTS 2</strong> on 2021-01-02.'),
(168, '19', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'attendance_log', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\\\",\\\"SZMsssqcccfn5cBl0aaaPCj287hym36\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\\\",\\\"unique_id\\\":\\\"AGL000012020\\\",\\\"name\\\":\\\"Solomon Obeng Darko\\\",\\\"email\\\":\\\"themailhereisthere@mail.com\\\",\\\"phone_number\\\":\\\"00930993093\\\",\\\"state\\\":\\\"absent\\\"},{\\\"item_id\\\":\\\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\\\",\\\"unique_id\\\":\\\"AGL000032020\\\",\\\"name\\\":\\\"Emmanuella Darko Sarfowaa\\\",\\\"email\\\":\\\"jauntygirl@gmail.com\\\",\\\"phone_number\\\":\\\"0247685521\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"SZM14dtqcccfn5cBl0ARgPCj287hym36\\\",\\\"unique_id\\\":\\\"AGL000042020\\\",\\\"name\\\":\\\"Cecilia Boateng\\\",\\\"email\\\":\\\"jauntygirl@gmail.com\\\",\\\"phone_number\\\":\\\"0247685521\\\",\\\"state\\\":\\\"absent\\\"},{\\\"item_id\\\":\\\"SZMsssqcccfn5cBl0aaaPCj287hym36\\\",\\\"unique_id\\\":\\\"AGL000052020\\\",\\\"name\\\":\\\"Felicia Amponsah\\\",\\\"email\\\":\\\"jauntygirl@gmail.com\\\",\\\"phone_number\\\":\\\"0247685521\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-01-02 08:14:57', 'Windows 10 | Chrome | ::1', 'Teacher Account updated logged attendance for <strong>GENERAL ARTS 1</strong> on 2021-01-02.');
INSERT INTO `users_activity_logs` (`id`, `item_id`, `user_id`, `subject`, `source`, `previous_record`, `date_recorded`, `user_agent`, `description`) VALUES
(169, '21', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'attendance_log', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-02 08:15:11', 'Windows 10 | Chrome | ::1', 'Teacher Account logged attendance for <strong>GENERAL ARTS 2</strong> on 2021-01-01.'),
(170, '21', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'attendance_log', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\\\",\\\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\\\",\\\"SZMsssqcccfn5cBl0ARgPCj287hym36\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\\\",\\\"unique_id\\\":\\\"AGL000022020\\\",\\\"name\\\":\\\"Grace Obeng-Yeboah\\\",\\\"email\\\":\\\"graciellaob@gmail.com\\\",\\\"phone_number\\\":\\\"00930993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\\\",\\\"unique_id\\\":\\\"AGL000042020\\\",\\\"name\\\":\\\"Frank Amponsah Amoah\\\",\\\"email\\\":\\\"frankamoah@gmail.com\\\",\\\"phone_number\\\":null,\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"SZMsssqcccfn5cBl0ARgPCj287hym36\\\",\\\"unique_id\\\":\\\"AGL000052020\\\",\\\"name\\\":\\\"Maureen Anim\\\",\\\"email\\\":\\\"jauntygirl@gmail.com\\\",\\\"phone_number\\\":\\\"0247685521\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-01-02 08:15:15', 'Windows 10 | Chrome | ::1', 'Teacher Account updated logged attendance for <strong>GENERAL ARTS 2</strong> on 2021-01-01.'),
(171, '22', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'attendance_log', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-02 08:15:24', 'Windows 10 | Chrome | ::1', 'Teacher Account logged attendance for <strong>GENERAL ARTS 1</strong> on 2021-01-01.'),
(172, '22', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'attendance_log', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\\\",\\\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\\\",\\\"SZM14dtqcccfn5cBl0ARgPCj287hym36\\\",\\\"SZMsssqcccfn5cBl0aaaPCj287hym36\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\\\",\\\"unique_id\\\":\\\"AGL000012020\\\",\\\"name\\\":\\\"Solomon Obeng Darko\\\",\\\"email\\\":\\\"themailhereisthere@mail.com\\\",\\\"phone_number\\\":\\\"00930993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\\\",\\\"unique_id\\\":\\\"AGL000032020\\\",\\\"name\\\":\\\"Emmanuella Darko Sarfowaa\\\",\\\"email\\\":\\\"jauntygirl@gmail.com\\\",\\\"phone_number\\\":\\\"0247685521\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"SZM14dtqcccfn5cBl0ARgPCj287hym36\\\",\\\"unique_id\\\":\\\"AGL000042020\\\",\\\"name\\\":\\\"Cecilia Boateng\\\",\\\"email\\\":\\\"jauntygirl@gmail.com\\\",\\\"phone_number\\\":\\\"0247685521\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"SZMsssqcccfn5cBl0aaaPCj287hym36\\\",\\\"unique_id\\\":\\\"AGL000052020\\\",\\\"name\\\":\\\"Felicia Amponsah\\\",\\\"email\\\":\\\"jauntygirl@gmail.com\\\",\\\"phone_number\\\":\\\"0247685521\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-01-02 08:15:28', 'Windows 10 | Chrome | ::1', 'Teacher Account updated logged attendance for <strong>GENERAL ARTS 1</strong> on 2021-01-01.'),
(173, '23', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'attendance_log', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-02 08:15:44', 'Windows 10 | Chrome | ::1', 'Teacher Account logged attendance for <strong></strong> on 2021-01-02.'),
(174, NULL, 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'attendance_log', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\\\",\\\"unique_id\\\":\\\"FTA000012020\\\",\\\"name\\\":\\\"Teacher Account\\\",\\\"email\\\":\\\"emmallob14@gmail.com\\\",\\\"phone_number\\\":\\\"0550107770\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-01-02 08:15:48', 'Windows 10 | Chrome | ::1', 'Teacher Account updated logged attendance for <strong></strong> on 2021-01-02.'),
(175, '23', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'attendance_log', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\\\",\\\"unique_id\\\":\\\"FTA000012020\\\",\\\"name\\\":\\\"Teacher Account\\\",\\\"email\\\":\\\"emmallob14@gmail.com\\\",\\\"phone_number\\\":\\\"0550107770\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-01-02 08:16:08', 'Windows 10 | Chrome | ::1', 'Teacher Account updated logged attendance for <strong></strong> on 2021-01-02.'),
(176, '24', 'uIkajsw123456789064hxk1fc3efmnva', 'attendance_log', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-02 08:17:44', 'Windows 10 | Chrome | ::1', 'Admin Account logged attendance for <strong></strong> on 2021-01-01.'),
(177, '24', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'attendance_log', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\\\",\\\"unique_id\\\":\\\"FTA000012020\\\",\\\"name\\\":\\\"Teacher Account\\\",\\\"email\\\":\\\"emmallob14@gmail.com\\\",\\\"phone_number\\\":\\\"0550107770\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-01-02 08:18:06', 'Windows 10 | Chrome | ::1', 'Teacher Account updated logged attendance for <strong></strong> on 2021-01-01.'),
(178, '25', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'attendance_log', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-02 08:20:34', 'Windows 10 | Chrome | ::1', 'Teacher Account logged attendance for <strong></strong> on 2021-01-02.'),
(179, '25', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'attendance_log', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"uIkajsw123456789064hxk1fc3efmnva\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"uIkajsw123456789064hxk1fc3efmnva\\\",\\\"unique_id\\\":\\\"kajflkdkfafd\\\",\\\"name\\\":\\\"Admin Account\\\",\\\"email\\\":\\\"test_admin@gmail.com\\\",\\\"phone_number\\\":\\\"+233240889023\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-01-02 08:21:49', 'Windows 10 | Chrome | ::1', 'Teacher Account updated logged attendance for <strong>admin</strong> on 2021-01-02.'),
(180, '26', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'attendance_log', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-02 08:22:07', 'Windows 10 | Chrome | ::1', 'Teacher Account logged attendance for <strong>admin</strong> on 2021-01-01.'),
(181, '26', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'attendance_log', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"uIkajsw123456789064hxk1fc3efmnva\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"uIkajsw123456789064hxk1fc3efmnva\\\",\\\"unique_id\\\":\\\"kajflkdkfafd\\\",\\\"name\\\":\\\"Admin Account\\\",\\\"email\\\":\\\"test_admin@gmail.com\\\",\\\"phone_number\\\":\\\"+233240889023\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-01-02 08:22:11', 'Windows 10 | Chrome | ::1', 'Teacher Account updated logged attendance for <strong>admin</strong> on 2021-01-01.'),
(182, 'cLnFJiO1RyGUEH6I3Ngr8qw54WxVXPjm', 'uIkajsw123456789064hxk1fc3efmnva', 'Comment', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-02 11:32:42', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> left a comment on this.'),
(183, 'cLnFJiO1RyGUEH6I3Ngr8qw54WxVXPjm', 'uIkajsw123456789064hxk1fc3efmnva', 'Comments Count', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-02 11:32:42', 'Windows 10 | Chrome | ::1', 'Number of comments is set to 2.'),
(184, '', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-02 12:53:28', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> added a new endpoint: <strong>library/add_book</strong> to the resource: <strong>library</strong>.'),
(185, '', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-02 12:54:09', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> added a new endpoint: <strong>library/update_book</strong> to the resource: <strong>library</strong>.'),
(186, '', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-02 12:55:28', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> added a new endpoint: <strong>library/list</strong> to the resource: <strong>library</strong>.'),
(187, 'yggtpdlj6m4feqczo1jkavsm83wihu95', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"123\",\"item_id\":\"yggtpdlj6m4feqczo1jkavsm83wihu95\",\"version\":\"v1\",\"resource\":\"library\",\"endpoint\":\"library\\/update_book\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"title\\\":\\\"required - The title of the book\\\",\\\"isbn\\\":\\\"The unique identification code for the book\\\",\\\"author\\\":\\\"The author of the book\\\",\\\"rack_no\\\":\\\"The rack on which the book could be located\\\",\\\"row_no\\\":\\\"The row on the rack number to locate the book\\\",\\\"quantity\\\":\\\"The quantity of the books available in stock\\\",\\\"department_id\\\":\\\"The unique id of the department\\\",\\\"class_id\\\":\\\"The unique id of the class\\\",\\\"category_id\\\":\\\"The category under which this book falls\\\",\\\"description\\\":\\\"The summary description of the book\\\",\\\"book_id\\\":\\\"required - The unique id of the book to update\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-01-02 12:54:08\",\"last_updated\":\"2021-01-02 12:54:08\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":null}', '2021-01-02 13:08:53', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(188, 'lwzcv1xwjnshkpipxtqr3mhlkfn7bg4d', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"122\",\"item_id\":\"lwzcv1xwjnshkpipxtqr3mhlkfn7bg4d\",\"version\":\"v1\",\"resource\":\"library\",\"endpoint\":\"library\\/add_book\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"title\\\":\\\"required - The title of the book\\\",\\\"isbn\\\":\\\"The unique identification code for the book\\\",\\\"author\\\":\\\"The author of the book\\\",\\\"rack_no\\\":\\\"The rack on which the book could be located\\\",\\\"row_no\\\":\\\"The row on the rack number to locate the book\\\",\\\"quantity\\\":\\\"The quantity of the books available in stock\\\",\\\"department_id\\\":\\\"The unique id of the department\\\",\\\"class_id\\\":\\\"The unique id of the class\\\",\\\"category_id\\\":\\\"The category under which this book falls\\\",\\\"description\\\":\\\"The summary description of the book\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-01-02 12:53:28\",\"last_updated\":\"2021-01-02 12:53:28\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":null}', '2021-01-02 13:08:59', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(189, 'lwzcv1xwjnshkpipxtqr3mhlkfn7bg4d', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"122\",\"item_id\":\"lwzcv1xwjnshkpipxtqr3mhlkfn7bg4d\",\"version\":\"v1\",\"resource\":\"library\",\"endpoint\":\"library\\/add_book\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"title\\\":\\\"required - The title of the book\\\",\\\"isbn\\\":\\\"required - The unique identification code for the book\\\",\\\"author\\\":\\\"The author of the book\\\",\\\"rack_no\\\":\\\"The rack on which the book could be located\\\",\\\"row_no\\\":\\\"The row on the rack number to locate the book\\\",\\\"quantity\\\":\\\"The quantity of the books available in stock\\\",\\\"department_id\\\":\\\"The unique id of the department\\\",\\\"class_id\\\":\\\"The unique id of the class\\\",\\\"category_id\\\":\\\"The category under which this book falls\\\",\\\"description\\\":\\\"The summary description of the book\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-01-02 12:53:28\",\"last_updated\":\"2021-01-02 13:08:58\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2021-01-02 13:09:11', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(190, 'yggtpdlj6m4feqczo1jkavsm83wihu95', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"123\",\"item_id\":\"yggtpdlj6m4feqczo1jkavsm83wihu95\",\"version\":\"v1\",\"resource\":\"library\",\"endpoint\":\"library\\/update_book\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"title\\\":\\\"required - The title of the book\\\",\\\"isbn\\\":\\\"required - The unique identification code for the book\\\",\\\"author\\\":\\\"The author of the book\\\",\\\"rack_no\\\":\\\"The rack on which the book could be located\\\",\\\"row_no\\\":\\\"The row on the rack number to locate the book\\\",\\\"quantity\\\":\\\"The quantity of the books available in stock\\\",\\\"department_id\\\":\\\"The unique id of the department\\\",\\\"class_id\\\":\\\"The unique id of the class\\\",\\\"category_id\\\":\\\"The category under which this book falls\\\",\\\"description\\\":\\\"The summary description of the book\\\",\\\"book_id\\\":\\\"required - The unique id of the book to update\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-01-02 12:54:08\",\"last_updated\":\"2021-01-02 13:08:53\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2021-01-02 13:09:16', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(191, 'lwzcv1xwjnshkpipxtqr3mhlkfn7bg4d', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"122\",\"item_id\":\"lwzcv1xwjnshkpipxtqr3mhlkfn7bg4d\",\"version\":\"v1\",\"resource\":\"library\",\"endpoint\":\"library\\/add_book\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"title\\\":\\\"required - The title of the book\\\",\\\"isbn\\\":\\\"required - The unique identification code for the book\\\",\\\"author\\\":\\\"required - The author of the book\\\",\\\"rack_no\\\":\\\"The rack on which the book could be located\\\",\\\"row_no\\\":\\\"The row on the rack number to locate the book\\\",\\\"quantity\\\":\\\"The quantity of the books available in stock\\\",\\\"department_id\\\":\\\"The unique id of the department\\\",\\\"class_id\\\":\\\"The unique id of the class\\\",\\\"category_id\\\":\\\"The category under which this book falls\\\",\\\"description\\\":\\\"The summary description of the book\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-01-02 12:53:28\",\"last_updated\":\"2021-01-02 13:09:11\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2021-01-02 13:09:28', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(192, '4', 'uIkajsw123456789064hxk1fc3efmnva', 'library_book', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-02 13:14:26', 'Windows 10 | Chrome | ::1', 'Admin Account added the Book: book title'),
(193, 'lwzcv1xwjnshkpipxtqr3mhlkfn7bg4d', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"122\",\"item_id\":\"lwzcv1xwjnshkpipxtqr3mhlkfn7bg4d\",\"version\":\"v1\",\"resource\":\"library\",\"endpoint\":\"library\\/add_book\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"title\\\":\\\"required - The title of the book\\\",\\\"isbn\\\":\\\"required - The unique identification code for the book\\\",\\\"author\\\":\\\"required - The author of the book\\\",\\\"rack_no\\\":\\\"The rack on which the book could be located\\\",\\\"row_no\\\":\\\"The row on the rack number to locate the book\\\",\\\"quantity\\\":\\\"required - The quantity of the books available in stock\\\",\\\"department_id\\\":\\\"The unique id of the department\\\",\\\"class_id\\\":\\\"The unique id of the class\\\",\\\"category_id\\\":\\\"The category under which this book falls\\\",\\\"description\\\":\\\"The summary description of the book\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-01-02 12:53:28\",\"last_updated\":\"2021-01-02 13:09:28\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2021-01-02 13:20:21', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(194, 'yggtpdlj6m4feqczo1jkavsm83wihu95', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"123\",\"item_id\":\"yggtpdlj6m4feqczo1jkavsm83wihu95\",\"version\":\"v1\",\"resource\":\"library\",\"endpoint\":\"library\\/update_book\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"title\\\":\\\"required - The title of the book\\\",\\\"isbn\\\":\\\"required - The unique identification code for the book\\\",\\\"author\\\":\\\"required - The author of the book\\\",\\\"rack_no\\\":\\\"The rack on which the book could be located\\\",\\\"row_no\\\":\\\"The row on the rack number to locate the book\\\",\\\"quantity\\\":\\\"The quantity of the books available in stock\\\",\\\"department_id\\\":\\\"The unique id of the department\\\",\\\"class_id\\\":\\\"The unique id of the class\\\",\\\"category_id\\\":\\\"The category under which this book falls\\\",\\\"description\\\":\\\"The summary description of the book\\\",\\\"book_id\\\":\\\"required - The unique id of the book to update\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-01-02 12:54:08\",\"last_updated\":\"2021-01-02 13:09:16\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2021-01-02 13:20:33', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(195, '2', 'uIkajsw123456789064hxk1fc3efmnva', 'book', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-02 13:23:29', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> deleted this record from the system.'),
(196, 'mfxzhx0vt3oo5jnbtekv8waefzrusdwl', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"124\",\"item_id\":\"mfxzhx0vt3oo5jnbtekv8waefzrusdwl\",\"version\":\"v1\",\"resource\":\"library\",\"endpoint\":\"library\\/list\",\"method\":\"GET\",\"description\":\"\",\"parameter\":\"{\\\"limit\\\":\\\"The number of rows to limit the result\\\",\\\"department_id\\\":\\\"The unique id of the department\\\",\\\"class_id\\\":\\\"The unique id of the class\\\",\\\"category_id\\\":\\\"The category under which this book falls\\\",\\\"description\\\":\\\"The summary description of the book\\\",\\\"book_id\\\":\\\"required - The unique id of the book to update\\\",\\\"isbn\\\":\\\"The unique identification code for the book\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-01-02 12:55:28\",\"last_updated\":\"2021-01-02 12:55:28\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":null}', '2021-01-02 15:36:21', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(197, '1', 'uIkajsw123456789064hxk1fc3efmnva', 'library_book', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-02 15:51:45', 'Windows 10 | Chrome | ::1', 'Admin Account updated the Book: The products of JavaScript infco'),
(198, '1', 'uIkajsw123456789064hxk1fc3efmnva', 'library_book', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-02 15:52:26', 'Windows 10 | Chrome | ::1', 'Admin Account updated the Book: The products of JavaScript infco'),
(199, '1', 'uIkajsw123456789064hxk1fc3efmnva', 'library_book', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-02 15:52:40', 'Windows 10 | Chrome | ::1', 'Admin Account updated the Book: The products of JavaScript Info'),
(200, '2', 'uIkajsw123456789064hxk1fc3efmnva', 'library_book', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-02 15:58:55', 'Windows 10 | Chrome | ::1', 'Admin Account updated the Book: Principles of OOP'),
(201, '', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-02 21:24:24', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> added a new endpoint: <strong>library/upload_resource</strong> to the resource: <strong>library</strong>.'),
(202, '4', 'uIkajsw123456789064hxk1fc3efmnva', 'library_book', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-02 21:30:51', 'Windows 10 | Chrome | ::1', 'Admin Account updated the Book: Update this book for me'),
(203, 'afdghhghghg', 'uIkajsw123456789064hxk1fc3efmnva', 'library_book', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-02 21:33:54', 'Windows 10 | Chrome | ::1', 'Admin Account updated the Book: Update this book for me'),
(204, 'afdghhghghg', 'uIkajsw123456789064hxk1fc3efmnva', 'Comment', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-03 21:34:28', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> left a comment on this.'),
(205, 'afdghhghghg', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Comment', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-03 21:35:14', 'Windows 10 | Chrome | ::1', '<strong>Teacher Account</strong> left a comment on this.'),
(206, 'afdghhghghg', 'uIkajsw123456789064hxk1fc3efmnva', 'Comment', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-03 21:35:37', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> left a comment on this.'),
(207, 'g1s0ypnf6ywmsineoxcruivtl4w9auqe', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"15\",\"item_id\":\"g1s0ypnf6ywmsineoxcruivtl4w9auqe\",\"version\":\"v1\",\"resource\":\"users\",\"endpoint\":\"users\\/add\",\"method\":\"POST\",\"description\":\"Add a new user account\",\"parameter\":\"{\\\"firstname\\\":\\\"required - The firstname of the user\\\",\\\"client_id\\\":\\\"This is a Unique of the user that is been created.\\\",\\\"lastname\\\":\\\"required - The lastname of the user\\\",\\\"othername\\\":\\\"The othernames of the user\\\",\\\"gender\\\":\\\"The gender of the user\\\",\\\"date_of_birth\\\":\\\"The date of birth\\\",\\\"email\\\":\\\"The email address of the user\\\",\\\"phone\\\":\\\"Contact number of the user\\\",\\\"phone_2\\\":\\\"Secondary contact number\\\",\\\"address\\\":\\\"The address of the user\\\",\\\"residence\\\":\\\"The place of residence\\\",\\\"nationality\\\":\\\"The nationality of the user\\\",\\\"country\\\":\\\"The country id of the user\\\",\\\"description\\\":\\\"Any additional information of the user\\\",\\\"user_id\\\":\\\"The id of the user\\\",\\\"employer\\\":\\\"The name of the user employer\\\",\\\"occupation\\\":\\\"The occupation of the user\\\",\\\"position\\\":\\\"The position of the user\\\",\\\"access_level\\\":\\\"The access permission id of the user.\\\",\\\"department\\\":\\\"The department of the user\\\",\\\"unique_id\\\":\\\"The unique id of the user\\\",\\\"section\\\":\\\"The section of the user\\\",\\\"class_id\\\":\\\"The class id of the user\\\",\\\"blood_group\\\":\\\"The blood group of the user\\\",\\\"guardian_info\\\":\\\"An array of the guardian information\\\",\\\"enrollment_date\\\":\\\"The date on which the user was enrolled\\\",\\\"user_type\\\":\\\"required - The type of the user to add\\\",\\\"image\\\":\\\"Image of the user\\\",\\\"academic_year\\\":\\\"The academic year on which the student was enrolled\\\",\\\"academic_term\\\":\\\"The term within which the student was enrolled\\\",\\\"status\\\":\\\"The status of the user\\\",\\\"username\\\":\\\"The username of the user for login purposes.\\\",\\\"previous_school\\\":\\\"This is applicable for students only\\\",\\\"previous_school_qualification\\\":\\\"Applicable for students only\\\",\\\"previous_school_remarks\\\":\\\"Any remarks supplied by previous school from which student is coming from\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-09-19 07:17:49\",\"last_updated\":\"2020-12-29 22:08:02\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"tgxuwdwkdjr58mg64hxk1fc3efmnvata\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2021-01-03 21:50:13', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(208, 'js7u9uwbmlnhccmxtpya4nwqk5hvgkag', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"11\",\"item_id\":\"js7u9uwbmlnhccmxtpya4nwqk5hvgkag\",\"version\":\"v1\",\"resource\":\"users\",\"endpoint\":\"users\\/update\",\"method\":\"POST\",\"description\":\"\'This endpoint is used for updating the information of the user.\'\",\"parameter\":\"{\\\"firstname\\\":\\\"required - The firstname of the user\\\",\\\"lastname\\\":\\\"required - The lastname of the user\\\",\\\"othername\\\":\\\"The othernames of the user\\\",\\\"client_id\\\":\\\"This is a Unique of the user that is been created.\\\",\\\"gender\\\":\\\"The gender of the user\\\",\\\"date_of_birth\\\":\\\"The date of birth\\\",\\\"email\\\":\\\"The email address of the user\\\",\\\"phone\\\":\\\"Contact number of the user\\\",\\\"phone_2\\\":\\\"Secondary contact number\\\",\\\"address\\\":\\\"The address of the user\\\",\\\"residence\\\":\\\"The place of residence\\\",\\\"nationality\\\":\\\"The nationality of the user\\\",\\\"country\\\":\\\"The country id of the user\\\",\\\"description\\\":\\\"Any additional information of the user\\\",\\\"position\\\":\\\"The position of the user\\\",\\\"user_id\\\":\\\"The id of the user\\\",\\\"occupation\\\":\\\"The occupation of the user\\\",\\\"employer\\\":\\\"The name of the users employer\\\",\\\"access_level\\\":\\\"The access permission id of the user.\\\",\\\"department\\\":\\\"The department of the user\\\",\\\"unique_id\\\":\\\"The unique id of the user\\\",\\\"section\\\":\\\"The section of the user\\\",\\\"class_id\\\":\\\"The class id of the user\\\",\\\"blood_group\\\":\\\"The blood group of the user\\\",\\\"guardian_info\\\":\\\"An array of the guardian information\\\",\\\"enrollment_date\\\":\\\"The date on which the user was enrolled\\\",\\\"user_type\\\":\\\"The type of the user to add\\\",\\\"image\\\":\\\"Image of the user\\\",\\\"academic_year\\\":\\\"The academic year on which the student was enrolled\\\",\\\"academic_term\\\":\\\"The term within which the student was enrolled\\\",\\\"username\\\":\\\"The username of the user for login purposes.\\\",\\\"previous_school\\\":\\\"This is applicable for students only\\\",\\\"previous_school_qualification\\\":\\\"Applicable for students only\\\",\\\"previous_school_remarks\\\":\\\"Any remarks supplied by previous school from which student is coming from\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-09-18 09:40:19\",\"last_updated\":\"2020-12-29 22:07:56\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"tgxuwdwkdjr58mg64hxk1fc3efmnvata\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2021-01-03 21:50:21', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(209, 'jkkllool45454', 'uIkajsw123456789064hxk1fc3efmnva', 'book_category', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-03 22:00:42', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> deleted this record from the system.'),
(210, '', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-03 22:46:04', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> added a new endpoint: <strong>library/update_category</strong> to the resource: <strong>library</strong>.'),
(211, '', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-03 22:46:37', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> added a new endpoint: <strong>library/add_category</strong> to the resource: <strong>library</strong>.'),
(212, 'afdafdafd3434', 'uIkajsw123456789064hxk1fc3efmnva', 'library_category', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-03 22:58:53', 'Windows 10 | Chrome | ::1', 'Admin Account updated the category.'),
(213, 'afdafdafd3434', 'uIkajsw123456789064hxk1fc3efmnva', 'library_category', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-03 22:59:31', 'Windows 10 | Chrome | ::1', 'Admin Account updated the category.'),
(214, 'jkkllool45454', 'uIkajsw123456789064hxk1fc3efmnva', 'book_category', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-03 23:03:44', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> deleted this record from the system.'),
(215, 'afdaferre455', 'uIkajsw123456789064hxk1fc3efmnva', 'book_category', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-03 23:03:47', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> deleted this record from the system.'),
(216, '6565fgfgfds34', 'uIkajsw123456789064hxk1fc3efmnva', 'book_category', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-03 23:03:50', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> deleted this record from the system.'),
(217, 'afdf6565jjd67', 'uIkajsw123456789064hxk1fc3efmnva', 'book_category', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-03 23:03:53', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> deleted this record from the system.'),
(218, 'fgfg56565afd', 'uIkajsw123456789064hxk1fc3efmnva', 'library_category', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-03 23:05:25', 'Windows 10 | Chrome | ::1', 'Admin Account updated the category.'),
(219, 'lwzcv1xwjnshkpipxtqr3mhlkfn7bg4d', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"122\",\"item_id\":\"lwzcv1xwjnshkpipxtqr3mhlkfn7bg4d\",\"version\":\"v1\",\"resource\":\"library\",\"endpoint\":\"library\\/add_book\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"title\\\":\\\"required - The title of the book\\\",\\\"isbn\\\":\\\"required - The unique identification code for the book\\\",\\\"author\\\":\\\"required - The author of the book\\\",\\\"rack_no\\\":\\\"The rack on which the book could be located\\\",\\\"row_no\\\":\\\"The row on the rack number to locate the book\\\",\\\"quantity\\\":\\\"required - The quantity of the books available in stock\\\",\\\"department_id\\\":\\\"The unique id of the department\\\",\\\"class_id\\\":\\\"The unique id of the class\\\",\\\"category_id\\\":\\\"The category under which this book falls\\\",\\\"description\\\":\\\"The summary description of the book\\\",\\\"code\\\":\\\"The unique code the item\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-01-02 12:53:28\",\"last_updated\":\"2021-01-02 13:20:21\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2021-01-04 19:04:23', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(220, 'yggtpdlj6m4feqczo1jkavsm83wihu95', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"123\",\"item_id\":\"yggtpdlj6m4feqczo1jkavsm83wihu95\",\"version\":\"v1\",\"resource\":\"library\",\"endpoint\":\"library\\/update_book\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"title\\\":\\\"required - The title of the book\\\",\\\"isbn\\\":\\\"required - The unique identification code for the book\\\",\\\"author\\\":\\\"required - The author of the book\\\",\\\"rack_no\\\":\\\"The rack on which the book could be located\\\",\\\"row_no\\\":\\\"The row on the rack number to locate the book\\\",\\\"quantity\\\":\\\"The quantity of the books available in stock\\\",\\\"department_id\\\":\\\"The unique id of the department\\\",\\\"class_id\\\":\\\"The unique id of the class\\\",\\\"category_id\\\":\\\"The category under which this book falls\\\",\\\"description\\\":\\\"The summary description of the book\\\",\\\"book_id\\\":\\\"required - The unique id of the book to update\\\",\\\"code\\\":\\\"The unique code the item\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-01-02 12:54:08\",\"last_updated\":\"2021-01-02 13:20:33\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2021-01-04 19:04:28', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(221, 'lkjlajfdk454545k', 'uIkajsw123456789064hxk1fc3efmnva', 'library_book', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-04 19:07:53', 'Windows 10 | Chrome | ::1', 'Admin Account updated the Book: book title'),
(222, 'afdghhghghg', 'uIkajsw123456789064hxk1fc3efmnva', 'library_book', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-04 19:13:52', 'Windows 10 | Chrome | ::1', 'Admin Account updated the Book: Update this book for me'),
(223, 'afdafdafd343434', 'uIkajsw123456789064hxk1fc3efmnva', 'library_book', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-04 19:14:05', 'Windows 10 | Chrome | ::1', 'Admin Account updated the Book: Principles of OOP'),
(224, '12223444443', 'uIkajsw123456789064hxk1fc3efmnva', 'library_book', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-04 19:14:19', 'Windows 10 | Chrome | ::1', 'Admin Account updated the Book: The products of JavaScript Info'),
(225, 'mfxzhx0vt3oo5jnbtekv8waefzrusdwl', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"124\",\"item_id\":\"mfxzhx0vt3oo5jnbtekv8waefzrusdwl\",\"version\":\"v1\",\"resource\":\"library\",\"endpoint\":\"library\\/list\",\"method\":\"GET\",\"description\":\"\",\"parameter\":\"{\\\"limit\\\":\\\"The number of rows to limit the result\\\",\\\"department_id\\\":\\\"The unique id of the department\\\",\\\"class_id\\\":\\\"The unique id of the class\\\",\\\"category_id\\\":\\\"The category under which this book falls\\\",\\\"description\\\":\\\"The summary description of the book\\\",\\\"book_id\\\":\\\"The unique id of the book to update\\\",\\\"isbn\\\":\\\"The unique identification code for the book\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-01-02 12:55:28\",\"last_updated\":\"2021-01-02 15:36:21\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2021-01-04 20:07:06', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(226, 'mfxzhx0vt3oo5jnbtekv8waefzrusdwl', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"124\",\"item_id\":\"mfxzhx0vt3oo5jnbtekv8waefzrusdwl\",\"version\":\"v1\",\"resource\":\"library\",\"endpoint\":\"library\\/list\",\"method\":\"GET\",\"description\":\"\",\"parameter\":\"{\\\"limit\\\":\\\"The number of rows to limit the result\\\",\\\"department_id\\\":\\\"The unique id of the department\\\",\\\"class_id\\\":\\\"The unique id of the class\\\",\\\"category_id\\\":\\\"The category under which this book falls\\\",\\\"description\\\":\\\"The summary description of the book\\\",\\\"book_id\\\":\\\"The unique id of the book to update\\\",\\\"isbn\\\":\\\"The unique identification code for the book\\\",\\\"show_in_list\\\":\\\"This is applicable if the user wants to ascertain whether the book has been added in a session to be issued out or requested.\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-01-02 12:55:28\",\"last_updated\":\"2021-01-04 20:07:06\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2021-01-04 20:09:35', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(227, '', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-04 21:05:19', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> added a new endpoint: <strong>library/issue_request_handler</strong> to the resource: <strong>library</strong>.'),
(228, 'sggcjcpqyzuphbuvw9ijdwxq7e5forto', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"128\",\"item_id\":\"sggcjcpqyzuphbuvw9ijdwxq7e5forto\",\"version\":\"v1\",\"resource\":\"library\",\"endpoint\":\"library\\/issue_request_handler\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"label\\\":\\\"An array that contains the request to perform. Parameters: todo - add, remove, request and issue \\/ book_id - Required if the todo is either add or remove.\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-01-04 21:05:19\",\"last_updated\":\"2021-01-04 21:05:19\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":null}', '2021-01-04 21:18:57', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(229, '1', NULL, 'books_borrowed', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-05 14:34:31', 'Windows 10 | Chrome | ::1', ' Issued Books out to a User.'),
(230, '1', NULL, 'books_borrowed', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-05 14:35:58', 'Windows 10 | Chrome | ::1', ' Issued Books out to a User.'),
(231, '2', 'uIkajsw123456789064hxk1fc3efmnva', 'books_borrowed', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-05 14:36:29', 'Windows 10 | Chrome | ::1', 'Admin Account Issued Books out to a User.'),
(232, '1', 'uIkajsw123456789064hxk1fc3efmnva', 'books_borrowed', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-05 14:37:19', 'Windows 10 | Chrome | ::1', 'Admin Account Issued Books out to a User.'),
(233, '2', 'uIkajsw123456789064hxk1fc3efmnva', 'books_borrowed', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-05 14:38:02', 'Windows 10 | Chrome | ::1', 'Admin Account Issued Books out to a User.'),
(234, '1', 'uIkajsw123456789064hxk1fc3efmnva', 'books_borrowed', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-05 14:40:08', 'Windows 10 | Chrome | ::1', 'Admin Account Issued Books out to a User.'),
(235, 'Ym3TxRhlwEnCF2szAGZgNvMferJDOq1a', 'uIkajsw123456789064hxk1fc3efmnva', 'books_borrowed', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-05 14:48:11', 'Windows 10 | Chrome | ::1', 'Admin Account Issued Books out to a User.'),
(236, 'AMXhgvCRJU3S6zubi15rm7xEQ20VNePT', 'uIkajsw123456789064hxk1fc3efmnva', 'books_borrowed', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-05 14:52:40', 'Windows 10 | Chrome | ::1', 'Admin Account Issued Books out to a User.'),
(237, '02spBfYMyJX4W8nlREC7TdOIFAxeq1w9', 'uIkajsw123456789064hxk1fc3efmnva', 'books_borrowed', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-05 14:55:43', 'Windows 10 | Chrome | ::1', 'Admin Account Issued Books out to a User.'),
(238, '5g2hPMbtcYHnsC4NLFDfz9jQpoORmIy7', 'uIkajsw123456789064hxk1fc3efmnva', 'books_borrowed', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-05 14:56:01', 'Windows 10 | Chrome | ::1', 'Admin Account Issued Books out to a User.'),
(239, 'iaym012xVA7YSHq8FgOr6DszpnQkWhlo', 'uIkajsw123456789064hxk1fc3efmnva', 'books_borrowed', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-05 19:00:27', 'Windows 10 | Chrome | ::1', 'Admin Account Issued Books out to a User.'),
(240, '0QJ4OYwMPFGfED7laTdAZxzUjCR6v29u', 'uIkajsw123456789064hxk1fc3efmnva', 'books_borrowed', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-05 19:04:07', 'Windows 10 | Chrome | ::1', 'Admin Account Issued Books out to a User.'),
(241, 'U0Hp9hKZJQaAokbi8Rgdeul76SP1NqGX', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'books_borrowed', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-05 22:26:40', 'Windows 10 | Chrome | ::1', 'Teacher Account Made a request for a list of Books..'),
(242, 'jJnVM2SimOsZDFla4UAkfHg0cyRwuQ9x', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'books_borrowed', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-05 22:28:39', 'Windows 10 | Chrome | ::1', 'Teacher Account Made a request for a list of Books..'),
(243, '', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-06 08:16:35', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> added a new endpoint: <strong>library/issued_request_list</strong> to the resource: <strong>library</strong>.'),
(244, 'jcz6d2qh85bfkpewit3zqcw1nab7m0jy', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"id\":\"129\",\"item_id\":\"jcz6d2qh85bfkpewit3zqcw1nab7m0jy\",\"version\":\"v1\",\"resource\":\"library\",\"endpoint\":\"library\\/issued_request_list\",\"method\":\"GET\",\"description\":\"\",\"parameter\":\"{\\\"limit\\\":\\\"The number of rows to limit the result\\\",\\\"borrowed_id\\\":\\\"The unique id of the borrowed id\\\",\\\"user_id\\\":\\\"The unique id of the user who requested for the books\\\",\\\"return_date\\\":\\\"Filter by the date on which books are to be returned\\\",\\\"issued_date\\\":\\\"Filter by the date on which the books were issued\\\",\\\"status\\\":\\\"Filter by the status of the request\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-01-06 08:16:35\",\"last_updated\":\"2021-01-06 08:16:35\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":null}', '2021-01-06 08:17:49', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> updated the endpoint.'),
(245, 'jJnVM2SimOsZDFla4UAkfHg0cyRwuQ9x', 'uIkajsw123456789064hxk1fc3efmnva', 'books_borrowed', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-07 12:32:03', 'Windows 10 | Chrome | ::1', 'Admin Account changed the Book Quantity from  to 7.'),
(246, 'jJnVM2SimOsZDFla4UAkfHg0cyRwuQ9x', 'uIkajsw123456789064hxk1fc3efmnva', 'books_borrowed', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-07 12:32:31', 'Windows 10 | Chrome | ::1', 'Admin Account changed the Book Quantity from 7 to 7.'),
(247, 'jJnVM2SimOsZDFla4UAkfHg0cyRwuQ9x', 'uIkajsw123456789064hxk1fc3efmnva', 'books_borrowed', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-07 12:39:19', 'Windows 10 | Chrome | ::1', 'Admin Account changed the Book Quantity from 7 to 3.'),
(248, 'jJnVM2SimOsZDFla4UAkfHg0cyRwuQ9x', 'uIkajsw123456789064hxk1fc3efmnva', 'books_borrowed', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-07 12:39:23', 'Windows 10 | Chrome | ::1', 'Admin Account changed the Book Quantity from 3 to 4.'),
(249, '27', 'uIkajsw123456789064hxk1fc3efmnva', 'attendance_log', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-07 12:50:32', 'Windows 10 | Chrome | ::1', 'Admin Account logged attendance for <strong>GENERAL ARTS 1</strong> on 2021-01-07.'),
(250, '27', 'uIkajsw123456789064hxk1fc3efmnva', 'attendance_log', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\\\",\\\"SZM14dtqcccfn5cBl0ARgPCj287hym36\\\",\\\"SZMsssqcccfn5cBl0aaaPCj287hym36\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\\\",\\\"unique_id\\\":\\\"AGL000012020\\\",\\\"name\\\":\\\"Solomon Obeng Darko\\\",\\\"email\\\":\\\"themailhereisthere@mail.com\\\",\\\"phone_number\\\":\\\"00930993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\\\",\\\"unique_id\\\":\\\"AGL000032020\\\",\\\"name\\\":\\\"Emmanuella Darko Sarfowaa\\\",\\\"email\\\":\\\"jauntygirl@gmail.com\\\",\\\"phone_number\\\":\\\"0247685521\\\",\\\"state\\\":\\\"absent\\\"},{\\\"item_id\\\":\\\"SZM14dtqcccfn5cBl0ARgPCj287hym36\\\",\\\"unique_id\\\":\\\"AGL000052020\\\",\\\"name\\\":\\\"Cecilia Boateng\\\",\\\"email\\\":\\\"jauntygirl@gmail.com\\\",\\\"phone_number\\\":\\\"0247685521\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"SZMsssqcccfn5cBl0aaaPCj287hym36\\\",\\\"unique_id\\\":\\\"AGL000072020\\\",\\\"name\\\":\\\"Felicia Amponsah\\\",\\\"email\\\":\\\"jauntygirl@gmail.com\\\",\\\"phone_number\\\":\\\"0247685521\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-01-07 12:50:36', 'Windows 10 | Chrome | ::1', 'Admin Account updated logged attendance for <strong>GENERAL ARTS 1</strong> on 2021-01-07.'),
(251, '28', 'uIkajsw123456789064hxk1fc3efmnva', 'attendance_log', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-07 12:50:45', 'Windows 10 | Chrome | ::1', 'Admin Account logged attendance for <strong>GENERAL ARTS 2</strong> on 2021-01-07.');
INSERT INTO `users_activity_logs` (`id`, `item_id`, `user_id`, `subject`, `source`, `previous_record`, `date_recorded`, `user_agent`, `description`) VALUES
(252, '29', 'uIkajsw123456789064hxk1fc3efmnva', 'attendance_log', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-07 12:50:58', 'Windows 10 | Chrome | ::1', 'Admin Account logged attendance for <strong>teacher</strong> on 2021-01-06.'),
(253, '30', 'uIkajsw123456789064hxk1fc3efmnva', 'attendance_log', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-07 12:51:13', 'Windows 10 | Chrome | ::1', 'Admin Account logged attendance for <strong>GENERAL ARTS 1</strong> on 2021-01-06.'),
(254, '30', 'uIkajsw123456789064hxk1fc3efmnva', 'attendance_log', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\\\",\\\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\\\",\\\"SZMsssqcccfn5cBl0aaaPCj287hym36\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\\\",\\\"unique_id\\\":\\\"AGL000012020\\\",\\\"name\\\":\\\"Solomon Obeng Darko\\\",\\\"email\\\":\\\"themailhereisthere@mail.com\\\",\\\"phone_number\\\":\\\"00930993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\\\",\\\"unique_id\\\":\\\"AGL000032020\\\",\\\"name\\\":\\\"Emmanuella Darko Sarfowaa\\\",\\\"email\\\":\\\"jauntygirl@gmail.com\\\",\\\"phone_number\\\":\\\"0247685521\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"SZM14dtqcccfn5cBl0ARgPCj287hym36\\\",\\\"unique_id\\\":\\\"AGL000052020\\\",\\\"name\\\":\\\"Cecilia Boateng\\\",\\\"email\\\":\\\"jauntygirl@gmail.com\\\",\\\"phone_number\\\":\\\"0247685521\\\",\\\"state\\\":\\\"absent\\\"},{\\\"item_id\\\":\\\"SZMsssqcccfn5cBl0aaaPCj287hym36\\\",\\\"unique_id\\\":\\\"AGL000072020\\\",\\\"name\\\":\\\"Felicia Amponsah\\\",\\\"email\\\":\\\"jauntygirl@gmail.com\\\",\\\"phone_number\\\":\\\"0247685521\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-01-07 12:51:15', 'Windows 10 | Chrome | ::1', 'Admin Account updated logged attendance for <strong>GENERAL ARTS 1</strong> on 2021-01-06.'),
(255, '29', 'uIkajsw123456789064hxk1fc3efmnva', 'attendance_log', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\\\",\\\"unique_id\\\":\\\"FTA000012020\\\",\\\"name\\\":\\\"Teacher Account\\\",\\\"email\\\":\\\"emmallob14@gmail.com\\\",\\\"phone_number\\\":\\\"0550107770\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-01-07 12:51:26', 'Windows 10 | Chrome | ::1', 'Admin Account updated logged attendance for <strong>teacher</strong> on 2021-01-06.'),
(256, '31', 'uIkajsw123456789064hxk1fc3efmnva', 'attendance_log', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-07 12:51:54', 'Windows 10 | Chrome | ::1', 'Admin Account logged attendance for <strong>teacher</strong> on 2021-01-07.'),
(257, '31', 'uIkajsw123456789064hxk1fc3efmnva', 'attendance_log', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\\\",\\\"unique_id\\\":\\\"FTA000012020\\\",\\\"name\\\":\\\"Teacher Account\\\",\\\"email\\\":\\\"emmallob14@gmail.com\\\",\\\"phone_number\\\":\\\"0550107770\\\",\\\"state\\\":\\\"present\\\"}]\",\"finalize\":\"0\"}', '2021-01-07 12:52:09', 'Windows 10 | Chrome | ::1', 'Admin Account updated logged attendance for <strong>teacher</strong> on 2021-01-07.'),
(258, 'jJnVM2SimOsZDFla4UAkfHg0cyRwuQ9x', 'uIkajsw123456789064hxk1fc3efmnva', 'books_borrowed', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-07 15:04:25', 'Windows 10 | Chrome | ::1', 'Admin Account changed the Request Fine from 0.00 to 20.'),
(259, 'jJnVM2SimOsZDFla4UAkfHg0cyRwuQ9x', 'uIkajsw123456789064hxk1fc3efmnva', 'books_borrowed', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-07 15:05:08', 'Windows 10 | Chrome | ::1', 'Admin Account changed the Request Fine from 20.00 to 30.'),
(260, 'jJnVM2SimOsZDFla4UAkfHg0cyRwuQ9x', 'uIkajsw123456789064hxk1fc3efmnva', 'books_borrowed', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-07 15:05:49', 'Windows 10 | Chrome | ::1', 'Admin Account changed the Request Fine from 30 to 35.'),
(261, 'jJnVM2SimOsZDFla4UAkfHg0cyRwuQ9x', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'books_borrowed', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-07 15:29:33', 'Windows 10 | Chrome | ::1', 'Teacher Account Cancelled the request for the books.'),
(262, 'jJnVM2SimOsZDFla4UAkfHg0cyRwuQ9x', 'uIkajsw123456789064hxk1fc3efmnva', 'books_borrowed', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-07 15:34:49', 'Windows 10 | Chrome | ::1', 'Admin Account changed the Request Status from Requested to Approved.'),
(263, 'jJnVM2SimOsZDFla4UAkfHg0cyRwuQ9x', 'uIkajsw123456789064hxk1fc3efmnva', 'Comment', 'MySchoolGH Management System Calculation<br>Property changed by an update from another property.', NULL, '2021-01-07 16:03:56', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> left a comment on this.');

-- --------------------------------------------------------

--
-- Table structure for table `users_api_endpoints`
--

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
(1, 'xwmcpvd8ezmqehjnt3g5ykpbalrk29is', 'v1', 'users', 'users/list', 'GET', 'This endpoint manages the user information', '{\"limit\":\"The number of rows to limit the result\",\"company_id\":\"List the results using the company id\",\"user_id\":\"The user id to load the information\",\"user_type\":\"The user type to fetch the record\",\"gender\":\"The gender of the user\",\"date_of_birth\":\"Search by date of birth\",\"q\":\"Searching for users using a string.\",\"status\":\"Load the result filtered by the status of the policy\",\"minified\":\"If the user requested for the minimal data\",\"lookup\":\"Query string\"}', 'active', 0, '2020-09-11 21:31:47', '2020-10-09 02:10:29', '0', '0', NULL, 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ'),
(2, 'zubdjqpic2reoykink1jhvq4pes7n9f8', 'v1', 'users', 'users/activities', 'GET', 'This endpoint loads the user activity logs updated', '{\"limit\":\"The number of rows to limit the result\",\"user_id\":\"The user id to load the information\"}', 'active', 0, '2020-09-11 21:31:47', '2020-09-15 21:29:05', '0', '0', NULL, NULL),
(6, 'cct4z26rwfkpv7xdkdbhxsruuapyfqt9', 'v1', 'endpoints', 'endpoints/list', 'GET', '', '{\"limit\":\"The number of rows to limit the results set.\",\"endpoint_id\":\"The id of the endpoint to load the content.\"}', 'active', 0, '2020-09-12 15:19:44', '2020-09-21 11:51:42', '0', '0', NULL, 'tgxuwdwkdjr58mg64hxk1fc3efmnvata'),
(10, 'pm3w6ofckjzguwyaoa1ytnh8fnllbs7z', 'v1', 'users', 'users/preference', 'POST', 'Initialize the user account. This accepts a value contained in the value parameter. init_idb will initiate the index db on the user\'s device, the any other parameter will update the user preferences.', '{\"label\":\"The item to update. It can be an array data and parsed to update the user preferences.\",\"the_user_id\":\"The user id to update the preference (optional)\"}', 'active', 0, '2020-09-17 15:09:43', '2020-11-11 20:11:51', '0', '0', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ'),
(11, 'js7u9uwbmlnhccmxtpya4nwqk5hvgkag', 'v1', 'users', 'users/update', 'POST', '\'This endpoint is used for updating the information of the user.\'', '{\"firstname\":\"required - The firstname of the user\",\"lastname\":\"required - The lastname of the user\",\"othername\":\"The othernames of the user\",\"client_id\":\"This is a Unique of the user that is been created.\",\"gender\":\"The gender of the user\",\"date_of_birth\":\"The date of birth\",\"email\":\"The email address of the user\",\"phone\":\"Contact number of the user\",\"phone_2\":\"Secondary contact number\",\"address\":\"The address of the user\",\"residence\":\"The place of residence\",\"nationality\":\"The nationality of the user\",\"country\":\"The country id of the user\",\"description\":\"Any additional information of the user\",\"position\":\"The position of the user\",\"user_id\":\"The id of the user\",\"occupation\":\"The occupation of the user\",\"employer\":\"The name of the users employer\",\"access_level\":\"The access permission id of the user.\",\"department\":\"The department of the user\",\"unique_id\":\"The unique id of the user\",\"section\":\"The section of the user\",\"class_id\":\"The class id of the user\",\"blood_group\":\"The blood group of the user\",\"guardian_info\":\"An array of the guardian information\",\"enrollment_date\":\"The date on which the user was enrolled\",\"user_type\":\"The type of the user to add\",\"image\":\"Image of the user\",\"academic_year\":\"The academic year on which the student was enrolled\",\"academic_term\":\"The term within which the student was enrolled\",\"username\":\"The username of the user for login purposes.\",\"previous_school\":\"This is applicable for students only\",\"previous_school_qualification\":\"Applicable for students only\",\"previous_school_remarks\":\"Any remarks supplied by previous school from which student is coming from\",\"religion\":\"The religion of the user\"}', 'active', 0, '2020-09-18 09:40:19', '2021-01-03 21:50:21', '0', '0', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajsw123456789064hxk1fc3efmnva'),
(12, 'ujeqvzg4c7ubshvlyp8jmffl2aykkoi5', 'v1', 'files', 'files/preview', 'POST', 'Use this endpoint to upload a file for preview.', '{\"file_upload\":\"required - The name of the file to upload\",\"module\":\"This will process any additional information added to this file upload.\"}', 'active', 0, '2020-09-18 14:04:04', '2020-09-19 17:52:22', '0', '0', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata'),
(13, 'pvfweosbq9yhyux2ugbndktj5m1vjsoc', 'v1', 'users', 'users/save_image', 'POST', 'This endpoint saves a users profile picture once it has been reviewed and accepted by the user.', '{\"user_id\":\"The id of the user to update the profile picture\"}', 'active', 0, '2020-09-18 14:23:38', '2020-09-18 14:23:38', '0', '0', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', NULL),
(15, 'g1s0ypnf6ywmsineoxcruivtl4w9auqe', 'v1', 'users', 'users/add', 'POST', 'Add a new user account', '{\"firstname\":\"required - The firstname of the user\",\"client_id\":\"This is a Unique of the user that is been created.\",\"lastname\":\"required - The lastname of the user\",\"othername\":\"The othernames of the user\",\"gender\":\"The gender of the user\",\"date_of_birth\":\"The date of birth\",\"email\":\"The email address of the user\",\"phone\":\"Contact number of the user\",\"phone_2\":\"Secondary contact number\",\"address\":\"The address of the user\",\"residence\":\"The place of residence\",\"nationality\":\"The nationality of the user\",\"country\":\"The country id of the user\",\"description\":\"Any additional information of the user\",\"user_id\":\"The id of the user\",\"employer\":\"The name of the user employer\",\"occupation\":\"The occupation of the user\",\"position\":\"The position of the user\",\"access_level\":\"The access permission id of the user.\",\"department\":\"The department of the user\",\"unique_id\":\"The unique id of the user\",\"section\":\"The section of the user\",\"class_id\":\"The class id of the user\",\"blood_group\":\"The blood group of the user\",\"guardian_info\":\"An array of the guardian information\",\"enrollment_date\":\"The date on which the user was enrolled\",\"user_type\":\"required - The type of the user to add\",\"image\":\"Image of the user\",\"academic_year\":\"The academic year on which the student was enrolled\",\"academic_term\":\"The term within which the student was enrolled\",\"status\":\"The status of the user\",\"username\":\"The username of the user for login purposes.\",\"previous_school\":\"This is applicable for students only\",\"previous_school_qualification\":\"Applicable for students only\",\"previous_school_remarks\":\"Any remarks supplied by previous school from which student is coming from\",\"religion\":\"The religion of the user\"}', 'active', 0, '2020-09-19 07:17:49', '2021-01-03 21:50:13', '0', '0', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajsw123456789064hxk1fc3efmnva'),
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
(72, '04bg2xphdmdqxvn5ow3pz9zamylgwke8', 'v1', 'classes', 'classes/add', 'POST', '', '{\"class_code\":\"The unique class code\",\"name\":\"required - The name of the class\",\"class_teacher\":\"The unique id of the class teacher\",\"class_assistant\":\"The unique id of the class assistant\",\"description\":\"The description of the class (optional)\",\"department_id\":\"The id of the department to which the class belongs\"}', 'active', 0, '2020-11-27 23:00:12', '2020-11-28 00:39:57', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(73, 'e05golyqpdpgnx1untuysrbbc37jws2f', 'v1', 'classes', 'classes/update', 'POST', '', '{\"class_code\":\"The unique class code\",\"name\":\"required - The name of the class\",\"class_teacher\":\"The unique id of the class teacher\",\"class_assistant\":\"The unique id of the class assistant\",\"description\":\"The description of the class (optional)\",\"class_id\":\"required - The unique of the class to update\",\"department_id\":\"The id of the department to which the class belongs\"}', 'active', 0, '2020-11-27 23:00:41', '2020-11-28 00:40:02', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(74, 'su5mijqfenc72vum40wokop9cvtfwbil', 'v1', 'classes', 'classes/list', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"q\":\"A search term for the class name\",\"class_teacher\":\"The unique id of the class teacher\",\"department_id\":\"The department id of the class to load\",\"class_id\":\"The unique id of the class\",\"class_assistant\":\"The unique id of the class assistant\",\"columns\":\"This lists only the requested columns\"}', 'active', 0, '2020-11-27 23:02:12', '2020-12-15 23:06:20', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(75, 'r58fkmqxb7rezo0euaj1umiqp9snywhd', 'v1', 'departments', 'departments/list', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"q\":\"A search term for the class name\",\"department_head\":\"The unique id of the department head\",\"department_id\":\"The unique id of the department\"}', 'active', 0, '2020-11-27 23:03:17', '2020-11-27 23:03:28', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(76, 'ljzganfuczysikqtrq1heodi3h6mw48v', 'v1', 'departments', 'departments/add', 'POST', '', '{\"department_code\":\"The department code\",\"name\":\"required - The name of the department\",\"image\":\"The department logo if any\",\"description\":\"A sample description of the department\",\"department_head\":\"The unique id of the department head\"}', 'active', 0, '2020-11-27 23:05:18', '2020-11-27 23:05:18', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(77, 'aw3d0vg12o6cszyfmiqvyt4eehx8fpwx', 'v1', 'departments', 'departments/update', 'POST', '', '{\"department_code\":\"The department code\",\"name\":\"required - The name of the department\",\"image\":\"The department logo if any\",\"description\":\"A sample description of the department\",\"department_head\":\"The unique id of the department head\",\"department_id\":\"required - The id of the department to update\"}', 'active', 0, '2020-11-27 23:05:57', '2020-11-27 23:05:57', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(78, '82uynfw7k90hpcwrkxqzrpylttmibajx', 'v1', 'sections', 'sections/update', 'POST', '', '{\"section_code\":\"The unique section code\",\"name\":\"required - The name of the section\",\"section_leader\":\"The unique id of the section leader\",\"description\":\"The description of the class (optional)\",\"section_id\":\"required - The unique of the section to update\"}', 'active', 0, '2020-11-27 23:07:30', '2020-11-27 23:07:30', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(79, 'yrlboe2l8atxuzbiz1kgcm6gntpvsu9d', 'v1', 'sections', 'sections/add', 'POST', '', '{\"section_code\":\"The unique section code\",\"name\":\"required - The name of the section\",\"section_leader\":\"The unique id of the section leader\",\"description\":\"The description of the class (optional)\"}', 'active', 0, '2020-11-27 23:07:55', '2020-11-27 23:07:55', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(80, 'ldvjfzjzkbaly13e5ksbxrf06a2p4qm8', 'v1', 'sections', 'sections/list', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"section_id\":\"The section id to load\",\"section_leader\":\"The unique id of the section leader\"}', 'active', 0, '2020-11-27 23:08:35', '2020-11-27 23:08:35', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(81, 'kfendr2by6aae08trixsxzu71odwpjov', 'v1', 'courses', 'courses/list', 'GET', '', '{\"limit\":\"The number of rows to return\",\"department_id\":\"The department id to fetch the courses offered\",\"course_tutor\":\"The unique id of the course tutor\",\"class_id\":\"The unique id of the class offering the course\",\"course_id\":\"The unique id of the course\",\"full_details\":\"A request for full information\",\"full_attachments\":\"This parameters loads all attachments for the course (all unit/lesson) attachments\"}', 'active', 0, '2020-11-28 10:12:44', '2020-11-28 23:40:17', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(82, 'yz7euod8acemowbhtxp3gr6t0mjblakx', 'v1', 'courses', 'courses/add', 'POST', '', '{\"name\":\"required - The title of the course\",\"course_code\":\"The unique code of the course\",\"credit_hours\":\"The number of credit hours for the course\",\"class_id\":\"The unique id of the class offering this course\",\"course_tutor\":\"The unique id of the course tutor\",\"description\":\"The description or course content\",\"academic_year\":\"The academic year for this course\",\"academic_term\":\"The academic term for this course\"}', 'active', 0, '2020-11-28 10:16:58', '2020-11-28 10:16:58', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(83, '7vljhpahe0bgyc2mprfnyjsf1gvb6lzt', 'v1', 'courses', 'courses/update', 'POST', '', '{\"name\":\"required - The title of the course\",\"course_code\":\"The unique code of the course\",\"credit_hours\":\"The number of credit hours for the course\",\"class_id\":\"The unique id of the class offering this course\",\"course_tutor\":\"The unique id of the course tutor\",\"description\":\"The description or course content\",\"academic_year\":\"The academic year for this course\",\"academic_term\":\"The academic term for this course\",\"course_id\":\"required - The id of the course to update\"}', 'active', 0, '2020-11-28 10:17:22', '2020-11-28 10:17:22', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(84, 'mza14e8o2txn0yeuyvdxqjpivaf5rsk9', 'v1', 'courses', 'courses/add_unit', 'POST', '', '{\"name\":\"required - The name of the unit\",\"start_date\":\"The start date for the unit\",\"end_date\":\"The end date of the unit\",\"description\":\"The description of the unit\",\"course_id\":\"The course id\"}', 'active', 0, '2020-11-28 12:46:24', '2020-11-28 12:48:54', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(85, '3cfqmdg5p1csezzrerip7v06ykahtvws', 'v1', 'courses', 'courses/add_lesson', 'POST', '', '{\"name\":\"required - The name of the unit\",\"start_date\":\"The start date for the unit\",\"end_date\":\"The end date of the unit\",\"description\":\"The description of the unit\",\"course_id\":\"The course id\",\"unit_id\":\"The id of the unit to add this lesson\"}', 'active', 0, '2020-11-28 12:46:55', '2020-11-28 14:06:37', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(86, 'exo0tiyhb6y1nrvhkjle59owipm3s42t', 'v1', 'courses', 'courses/update_lesson', 'POST', '', '{\"name\":\"required - The name of the unit\",\"start_date\":\"The start date for the unit\",\"end_date\":\"The end date of the unit\",\"description\":\"The description of the unit\",\"course_id\":\"The course id\",\"unit_id\":\"The id of the unit to add this lesson\",\"lesson_id\":\"The id of the lesson to add\"}', 'active', 0, '2020-11-28 12:47:42', '2020-11-28 12:49:17', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
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
(104, 'a2grs865f79romhwelfcjo4ziulghbxu', 'v1', 'assignments', 'assignments/handin', 'POST', '', '{\"assignment_id\":\"required - The unique assignment id to handin.\"}', 'active', 0, '2020-12-22 06:26:29', '2020-12-22 06:26:29', '0', '0', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', NULL),
(105, 'ikr5lyhutqvox42tdjamdzrscpy89wxf', 'v1', 'assignments', 'assignments/close', 'POST', '', '{\"assignment_id\":\"required - The unique assignment id to close.\"}', 'active', 0, '2020-12-22 09:27:40', '2020-12-22 09:27:40', '0', '0', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', NULL),
(106, 'qpenjlpgilhwjkyszofuhub3ocwmtebr', 'v1', 'assignments', 'assignments/update', 'POST', '', '{\"assignment_type\":\"required - The type of assignment type to upload (multiple_choice or file_attachment)\",\"assignment_title\":\"required - The title of the assignment\",\"description\":\"Any additional instructions added to the assignment\",\"grade\":\"required - The grade for this assignment\",\"date_due\":\"required - The date on which the assignment is due.\",\"time_due\":\"The time for submission\",\"assigned_to\":\"required - This determines whether to assign the assignment to all students in the class or to specific students\",\"assigned_to_list\":\"This is needed when you decide to assign the assignment to specific students.\",\"class_id\":\"required - The id of the class to assign the assignment\",\"course_id\":\"required - The unique id of the course to link this assignment.\",\"assignment_id\":\"required - The unique id of the assignment to update the record.\"}', 'active', 0, '2020-12-22 14:59:52', '2020-12-23 08:09:48', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe'),
(107, 'wpun52gxrmylbnuopkeoxe8qvtcj1bzc', 'v1', 'assignments', 'assignments/reopen', 'POST', '', '{\"assignment_id\":\"required - The unique id of the assignment to reopen\"}', 'active', 0, '2020-12-23 07:53:07', '2020-12-23 07:53:07', '0', '0', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', NULL),
(108, 'fsf9xl0ykqcje1zmhiadbxnctwgg2dn3', 'v1', 'assignments', 'assignments/add_question', 'POST', 'This endpoint is used to both add and update a question under a specific assignment', '{\"option_a\":\"required - The value for Option A\",\"option_b\":\"required - The value for Option B\",\"option_c\":\"required - The value for Option C\",\"option_d\":\"The value for Option D\",\"option_e\":\"The value for Option E\",\"question\":\"required - The question detail\",\"answer_type\":\"The type of the answer to process\",\"question_id\":\"The unique id of the question\",\"assignment_id\":\"required - The assignment id\",\"difficulty\":\"The difficulty level of the question\",\"answers\":\"An array of selected options\",\"numeric_answer\":\"If the answer is numeric this should show\",\"marks\":\"The marks for the question\"}', 'active', 0, '2020-12-23 15:10:23', '2020-12-24 22:10:36', '0', '0', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe'),
(109, 'm8hxel6jpwfbyu2iqc4s3jqaf1krdvkh', 'v1', 'assignments', 'assignments/review_question', 'GET', '', '{\"assignment_id\":\"required - The unique assignment id to review.\",\"question_id\":\"required - The unique id of the question\"}', 'active', 0, '2020-12-23 21:19:12', '2020-12-23 21:19:12', '0', '0', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', NULL),
(110, 'jj2fi9v8vwf0b1lrmxy4edaopnwhgeg7', 'v1', 'assignments', 'assignments/publish', 'POST', '', '{\"assignment_id\":\"required - The id of the assignment to publish.\"}', 'active', 0, '2020-12-23 23:01:59', '2020-12-23 23:01:59', '0', '0', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', NULL),
(111, '9ojxbvfyqeocke3jrt0hbpgucpkztlsr', 'v1', 'assignments', 'assignments/save_answer', 'POST', '', '{\"question_id\":\"required - This is the unique id of the question to load\",\"answers\":\"This is the array of answers selected\",\"previous_id\":\"This will determine the next question to load\"}', 'active', 0, '2020-12-25 23:05:34', '2020-12-26 07:58:06', '0', '0', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo'),
(112, 'ymdwiwoga59nchjqzsdslrt6fgfluxzo', 'v1', 'assignments', 'assignments/review_answers', 'GET', 'Load the answers selected by this user for the specified assignment', '{\"assignment_id\":\"required - The unique id of the assignment\"}', 'active', 0, '2020-12-28 11:41:46', '2020-12-28 11:41:46', '0', '0', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', NULL),
(113, '0eyocpqa958qstkvxolmn6p4bbjsxu1l', 'v1', 'attendance', 'attendance/log', 'POST', '', '{\"date\":\"required - The date to log the attendance\",\"attendance\":\"This is an array of user_ids and their status\",\"user_type\":\"This denotes the user type to query.\",\"class_id\":\"The class id is needed if the user type is student.\",\"finalize\":\"This parameter is set when there is the need to finalize the log\"}', 'active', 0, '2020-12-28 20:30:47', '2020-12-29 09:16:46', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(114, 'v4yzw52dmfvsbjmfhsajoipqye63ollx', 'v1', 'events', 'events/add_type', 'POST', '', '{\"name\":\"required - The name of the type\",\"description\":\"Any additional description of the type\",\"icon\":\"The icon to be used to represent events that falls under this category\",\"color_code\":\"The color code for the event type\"}', 'active', 0, '2020-12-29 19:35:20', '2021-01-01 16:37:51', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe'),
(115, '6d8roxdljhw2vnkwzb1et0yiahqqy4rl', 'v1', 'events', 'events/update_type', 'POST', '', '{\"name\":\"required - The name of the type\",\"description\":\"Any additional description of the type\",\"type_id\":\"required - The unique id of the type\",\"icon\":\"The icon to be used to represent events that falls under this category\",\"color_code\":\"The color code for the event type\"}', 'active', 0, '2020-12-29 19:35:47', '2021-01-01 16:37:47', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe'),
(116, 'y0ckjulcbkoqvdfyen2d9nuigzmhaeo6', 'v1', 'users', 'users/modify_wardguardian', 'POST', 'Modify the guardians list attached to a student', '{\"user_id\":\"required - The unique id for the guardian and the ward\",\"todo\":\"required - The activity to perform (append, remove).\"}', 'active', 0, '2020-12-29 23:19:41', '2020-12-29 23:19:41', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(117, '6z8t4vx5rhnhkmorjcwezxbdsq237g9b', 'v1', 'events', 'events/add', 'POST', 'This endpoint adds a new event into the system.', '{\"title\":\"required - The title of the event\",\"type\":\"required - The type of event to add\",\"audience\":\"required - The audience of the event\",\"date\":\"required - The date of the event\",\"holiday\":\"To ascertain whether the event is a holiday or not\",\"event_image\":\"Any image to attach to this event\",\"description\":\"Any additional information to be added to this event.\",\"is_mailable\":\"Specify whether this event can be emailed to the users list specified\",\"status\":\"The status of the event\"}', 'active', 0, '2020-12-30 09:22:11', '2021-01-02 00:21:33', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(118, 'g7bieqmavu2ynzzlosfw1cfsyh3ivtwp', 'v1', 'events', 'events/update', 'POST', '', '{\"title\":\"required - The title of the event\",\"type\":\"The type of event to add\",\"audience\":\"The audience of the event\",\"date\":\"required - The date of the event\",\"holiday\":\"To ascertain whether the event is a holiday or not\",\"event_image\":\"Any image to attach to this event\",\"description\":\"Any additional information to be added to this event.\",\"event_id\":\"required - The unique id of the event\",\"is_mailable\":\"Specify whether this event can be emailed to the users list specified\",\"status\":\"This is the status of the event\"}', 'active', 0, '2020-12-30 09:23:01', '2021-01-01 23:33:48', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(119, 'o5pmu71qxrjp8moyj6gzgawdtbb2kysv', 'v1', 'events', 'events/list', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"event_id\":\"The unique id of the event\",\"event_date\":\"The date on which the event will be held\",\"audience\":\"The audience to receive this event\"}', 'active', 0, '2020-12-30 10:17:54', '2020-12-30 10:20:46', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(120, 'ibxuohpuwjz4b96jzalqs0wepmnngctg', 'v1', 'events', 'events/types_list', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"type_id\":\"The unique id of the event type\",\"show_events\":\"When parsed it will also list all events found under the each type\"}', 'active', 0, '2020-12-30 10:18:51', '2020-12-30 10:19:27', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(121, 'newylzxjawo2vdtv7p6mbjusdqmtln4a', 'v1', 'records', 'records/remove', 'POST', '', '{\"resource\":\"required - This is the resource to delete\",\"record_id\":\"required - This is the unique id of the record to delete\"}', 'active', 0, '2021-01-01 21:06:58', '2021-01-01 21:06:58', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(122, 'lwzcv1xwjnshkpipxtqr3mhlkfn7bg4d', 'v1', 'library', 'library/add_book', 'POST', '', '{\"title\":\"required - The title of the book\",\"isbn\":\"required - The unique identification code for the book\",\"author\":\"required - The author of the book\",\"rack_no\":\"The rack on which the book could be located\",\"row_no\":\"The row on the rack number to locate the book\",\"quantity\":\"required - The quantity of the books available in stock\",\"department_id\":\"The unique id of the department\",\"class_id\":\"The unique id of the class\",\"category_id\":\"The category under which this book falls\",\"description\":\"The summary description of the book\",\"code\":\"The unique code the item\",\"book_image\":\"The cover image for the book\"}', 'active', 0, '2021-01-02 12:53:28', '2021-01-04 19:04:23', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(123, 'yggtpdlj6m4feqczo1jkavsm83wihu95', 'v1', 'library', 'library/update_book', 'POST', '', '{\"title\":\"required - The title of the book\",\"isbn\":\"required - The unique identification code for the book\",\"author\":\"required - The author of the book\",\"rack_no\":\"The rack on which the book could be located\",\"row_no\":\"The row on the rack number to locate the book\",\"quantity\":\"The quantity of the books available in stock\",\"department_id\":\"The unique id of the department\",\"class_id\":\"The unique id of the class\",\"category_id\":\"The category under which this book falls\",\"description\":\"The summary description of the book\",\"book_id\":\"required - The unique id of the book to update\",\"code\":\"The unique code the item\",\"book_image\":\"The cover image for the book\"}', 'active', 0, '2021-01-02 12:54:08', '2021-01-04 19:04:28', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(124, 'mfxzhx0vt3oo5jnbtekv8waefzrusdwl', 'v1', 'library', 'library/list', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"department_id\":\"The unique id of the department\",\"class_id\":\"The unique id of the class\",\"category_id\":\"The category under which this book falls\",\"description\":\"The summary description of the book\",\"book_id\":\"The unique id of the book to update\",\"isbn\":\"The unique identification code for the book\",\"show_in_list\":\"This is applicable if the user wants to ascertain whether the book has been added in a session to be issued out or requested.\",\"minified\":\"If parsed then the result will be simplified\"}', 'active', 0, '2021-01-02 12:55:28', '2021-01-04 20:09:35', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(125, 'jwn17zfsaecz43ih96ouqyfiela2hv5k', 'v1', 'library', 'library/upload_resource', 'POST', '', '{\"book_id\":\"required - The book id to upload the files to\"}', 'active', 0, '2021-01-02 21:24:24', '2021-01-02 21:24:24', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(126, 'duts60wylhukvkdfimwpbtcp9lhoszya', 'v1', 'library', 'library/update_category', 'POST', '', '{\"name\":\"required - The title of the category\",\"department_id\":\"The department of the book category\",\"description\":\"The description of the category\",\"category_id\":\"required - The unique id of the category to update.\"}', 'active', 0, '2021-01-03 22:46:04', '2021-01-03 22:46:04', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(127, 'eagq8tdpnik0yvrjvobwscbuz3z4r9d7', 'v1', 'library', 'library/add_category', 'POST', '', '{\"name\":\"required - The title of the category\",\"department_id\":\"The department of the book category\",\"description\":\"The description of the category\"}', 'active', 0, '2021-01-03 22:46:37', '2021-01-03 22:46:37', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(128, 'sggcjcpqyzuphbuvw9ijdwxq7e5forto', 'v1', 'library', 'library/issue_request_handler', 'POST', '', '{\"label\":\"required - An array that contains the request to perform. Parameters: todo - add, remove, request and issue / book_id - Required if the todo is either add or remove.\"}', 'active', 0, '2021-01-04 21:05:19', '2021-01-04 21:18:57', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva');
INSERT INTO `users_api_endpoints` (`id`, `item_id`, `version`, `resource`, `endpoint`, `method`, `description`, `parameter`, `status`, `counter`, `date_created`, `last_updated`, `deleted`, `deprecated`, `added_by`, `updated_by`) VALUES
(129, 'jcz6d2qh85bfkpewit3zqcw1nab7m0jy', 'v1', 'library', 'library/issued_request_list', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"borrowed_id\":\"The unique id of the borrowed id\",\"user_id\":\"The unique id of the user who requested for the books\",\"return_date\":\"Filter by the date on which books are to be returned\",\"issued_date\":\"Filter by the date on which the books were issued\",\"status\":\"Filter by the status of the request\",\"show_list\":\"This when appended while show the details of the book borrowed\"}', 'active', 0, '2021-01-06 08:16:35', '2021-01-06 08:17:49', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva');

-- --------------------------------------------------------

--
-- Table structure for table `users_api_keys`
--

CREATE TABLE `users_api_keys` (
  `id` int(11) UNSIGNED NOT NULL,
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

INSERT INTO `users_api_keys` (`id`, `user_id`, `username`, `access_token`, `access_key`, `access_type`, `expiry_date`, `expiry_timestamp`, `requests_limit`, `total_requests`, `permissions`, `date_generated`, `status`) VALUES
(6, 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'test_admin', '$2y$10$wTlBdjQuI6HAT1XqwyHPZOkWHL47L4IsqPHq7ey6wv0hYbdSOjrJC', 'p43FVPXvUi8DWNzklKBHjhQ1S4wktGcJ6maAYLG73MOCdsxzjeQdsMREtBfn20TI9Hli', 'temp', '2020-09-30', '2020-09-30 21:46:52', 5000, 0, NULL, '2020-09-30 21:46:52', '0'),
(7, 'uIkajsw123456789064hxk1fc3efmnva', 'test_admin', '$2y$10$dqBEsuNoYjhPdTscR6dq3u5V87.CHys0m.GA5U0kZqSzrYgK51qs6', '4jIRASkrjEOGXNCXWlBRlvyggCn34uQWmpqYtVLzHm5BPFDiUehzPH6Tdrf0yIcab9ap0t', 'temp', '2020-11-13', '2020-11-13 18:36:36', 5000, 0, NULL, '2020-11-13 18:36:36', '1');

-- --------------------------------------------------------

--
-- Table structure for table `users_api_queries`
--

CREATE TABLE `users_api_queries` (
  `id` int(11) UNSIGNED NOT NULL,
  `requests_count` int(11) UNSIGNED DEFAULT NULL,
  `request_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users_api_requests`
--

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
  `academic_year` varchar(32) DEFAULT NULL,
  `academic_term` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users_attendance_log`
--

INSERT INTO `users_attendance_log` (`id`, `client_id`, `user_type`, `class_id`, `users_list`, `users_data`, `log_date`, `created_by`, `date_created`, `status`, `finalize`, `date_finalized`, `academic_year`, `academic_term`) VALUES
(1, 'LKJAFD94R', 'student', '1', '[\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"SZM14dtqcccfn5cBl0ARgPCj287hym36\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000032020\",\"name\":\"Emmanuella Darko Sarfowaa\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000042020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"}]', '2020-12-02', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 08:28:29', '1', '1', '2020-12-29 08:29:09', '2019/2020', '1st'),
(2, 'LKJAFD94R', 'student', '1', '[\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"SZMsssqcccfn5cBl0aaaPCj287hym36\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000032020\",\"name\":\"Emmanuella Darko Sarfowaa\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000042020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2020-12-01', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 08:28:38', '1', '1', '2020-12-29 08:29:04', '2019/2020', '1st'),
(3, 'LKJAFD94R', 'student', '1', '[\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"SZM14dtqcccfn5cBl0ARgPCj287hym36\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"absent\"},{\"item_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000032020\",\"name\":\"Emmanuella Darko Sarfowaa\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000042020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"}]', '2020-11-30', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 08:28:50', '1', '1', '2020-12-29 08:28:56', '2019/2020', '1st'),
(4, 'LKJAFD94R', 'student', '1', '[\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"SZM14dtqcccfn5cBl0ARgPCj287hym36\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"absent\"},{\"item_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000032020\",\"name\":\"Emmanuella Darko Sarfowaa\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000042020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"holiday\"}]', '2020-12-03', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 08:29:17', '1', '1', '2020-12-29 08:29:21', '2019/2020', '1st'),
(5, 'LKJAFD94R', 'student', '2', '[\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\",\"SZMsssqcccfn5cBl0ARgPCj287hym36\"]', '[{\"item_id\":\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"unique_id\":\"AGL000022020\",\"name\":\"Grace Obeng-Yeboah\",\"email\":\"graciellaob@gmail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\",\"unique_id\":\"AGL000042020\",\"name\":\"Frank Amponsah Amoah\",\"email\":\"frankamoah@gmail.com\",\"phone_number\":null,\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Maureen Anim\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2020-12-03', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 08:30:15', '1', '1', '2020-12-29 08:30:18', '2019/2020', '1st'),
(6, 'LKJAFD94R', 'student', '2', '[\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\"]', '[{\"item_id\":\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"unique_id\":\"AGL000022020\",\"name\":\"Grace Obeng-Yeboah\",\"email\":\"graciellaob@gmail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\",\"unique_id\":\"AGL000042020\",\"name\":\"Frank Amponsah Amoah\",\"email\":\"frankamoah@gmail.com\",\"phone_number\":null,\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Maureen Anim\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"}]', '2020-12-02', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 08:30:27', '1', '1', '2020-12-29 08:30:30', '2019/2020', '1st'),
(7, 'LKJAFD94R', 'student', '2', '[\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\",\"SZMsssqcccfn5cBl0ARgPCj287hym36\"]', '[{\"item_id\":\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"unique_id\":\"AGL000022020\",\"name\":\"Grace Obeng-Yeboah\",\"email\":\"graciellaob@gmail.com\",\"phone_number\":\"00930993093\",\"state\":\"absent\"},{\"item_id\":\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\",\"unique_id\":\"AGL000042020\",\"name\":\"Frank Amponsah Amoah\",\"email\":\"frankamoah@gmail.com\",\"phone_number\":null,\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Maureen Anim\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2020-12-01', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 08:30:35', '1', '1', '2020-12-29 08:30:38', '2019/2020', '1st'),
(8, 'LKJAFD94R', 'student', '2', '[\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\"]', '[{\"item_id\":\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"unique_id\":\"AGL000022020\",\"name\":\"Grace Obeng-Yeboah\",\"email\":\"graciellaob@gmail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\",\"unique_id\":\"AGL000042020\",\"name\":\"Frank Amponsah Amoah\",\"email\":\"frankamoah@gmail.com\",\"phone_number\":null,\"state\":\"absent\"},{\"item_id\":\"SZMsssqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Maureen Anim\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"}]', '2020-12-04', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 08:30:54', '1', '1', '2020-12-29 08:30:58', '2019/2020', '1st'),
(9, 'LKJAFD94R', 'student', '1', '[\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"SZMsssqcccfn5cBl0aaaPCj287hym36\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000032020\",\"name\":\"Emmanuella Darko Sarfowaa\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"holiday\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000042020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"late\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2020-12-04', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 08:47:23', '1', '1', '2020-12-29 08:47:28', '2019/2020', '1st'),
(10, 'LKJAFD94R', 'student', '1', '[\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000032020\",\"name\":\"Emmanuella Darko Sarfowaa\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"late\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000042020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"late\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"}]', '2020-12-07', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 08:53:18', '1', '1', '2020-12-29 08:53:23', '2019/2020', '1st'),
(11, 'LKJAFD94R', 'admin', NULL, '[\"uIkajsw123456789064hxk1fc3efmnva\"]', '[{\"item_id\":\"uIkajsw123456789064hxk1fc3efmnva\",\"unique_id\":\"kajflkdkfafd\",\"name\":\"Admin Account\",\"email\":\"test_admin@gmail.com\",\"phone_number\":\"+233240889023\",\"state\":\"present\"}]', '2020-12-02', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 09:16:49', '1', '1', '2020-12-29 09:18:48', '2019/2020', '1st'),
(12, 'LKJAFD94R', 'admin', NULL, '[\"uIkajsw123456789064hxk1fc3efmnva\"]', '[{\"item_id\":\"uIkajsw123456789064hxk1fc3efmnva\",\"unique_id\":\"kajflkdkfafd\",\"name\":\"Admin Account\",\"email\":\"test_admin@gmail.com\",\"phone_number\":\"+233240889023\",\"state\":\"present\"}]', '2020-12-03', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 09:18:29', '1', '1', '2020-12-29 09:18:53', '2019/2020', '1st'),
(13, 'LKJAFD94R', 'admin', NULL, '[\"uIkajsw123456789064hxk1fc3efmnva\"]', '[{\"item_id\":\"uIkajsw123456789064hxk1fc3efmnva\",\"unique_id\":\"kajflkdkfafd\",\"name\":\"Admin Account\",\"email\":\"test_admin@gmail.com\",\"phone_number\":\"+233240889023\",\"state\":\"present\"}]', '2020-12-01', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 09:18:38', '1', '1', '2020-12-29 09:18:41', '2019/2020', '1st'),
(14, 'LKJAFD94R', 'teacher', NULL, '[\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\"]', '[{\"item_id\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000012020\",\"name\":\"Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"}]', '2020-12-03', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 09:19:00', '1', '1', '2020-12-29 09:19:27', '2019/2020', '1st'),
(15, 'LKJAFD94R', 'teacher', NULL, '[\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\"]', '[{\"item_id\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000012020\",\"name\":\"Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"}]', '2020-12-02', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 09:19:06', '1', '1', '2020-12-29 09:19:11', '2019/2020', '1st'),
(16, 'LKJAFD94R', 'teacher', NULL, '[\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\"]', '[{\"item_id\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000012020\",\"name\":\"Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"}]', '2020-12-01', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 09:19:15', '1', '1', '2020-12-29 09:19:18', '2019/2020', '1st'),
(17, 'LKJAFD94R', 'teacher', NULL, '[\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\"]', '[{\"item_id\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000012020\",\"name\":\"Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"}]', '2020-12-04', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 09:19:33', '1', '1', '2020-12-29 09:19:36', '2019/2020', '1st'),
(18, 'LKJAFD94R', 'student', '1', '[\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"SZM14dtqcccfn5cBl0ARgPCj287hym36\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000032020\",\"name\":\"Emmanuella Darko Sarfowaa\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000042020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"}]', '2020-12-08', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 09:29:20', '1', '1', '2020-12-29 09:29:23', '2019/2020', '1st'),
(19, 'LKJAFD94R', 'student', '1', '[\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"SZMsssqcccfn5cBl0aaaPCj287hym36\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"absent\"},{\"item_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000032020\",\"name\":\"Emmanuella Darko Sarfowaa\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000042020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2021-01-02', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '2021-01-02 08:13:50', '1', '1', '2021-01-02 08:14:57', '2019/2020', '1st'),
(20, 'LKJAFD94R', 'student', '2', '[\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\"]', '[{\"item_id\":\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"unique_id\":\"AGL000022020\",\"name\":\"Grace Obeng-Yeboah\",\"email\":\"graciellaob@gmail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\",\"unique_id\":\"AGL000042020\",\"name\":\"Frank Amponsah Amoah\",\"email\":\"frankamoah@gmail.com\",\"phone_number\":null,\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Maureen Anim\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"}]', '2021-01-02', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '2021-01-02 08:14:48', '1', '1', '2021-01-02 08:14:51', '2019/2020', '1st'),
(21, 'LKJAFD94R', 'student', '2', '[\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\",\"SZMsssqcccfn5cBl0ARgPCj287hym36\"]', '[{\"item_id\":\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"unique_id\":\"AGL000022020\",\"name\":\"Grace Obeng-Yeboah\",\"email\":\"graciellaob@gmail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\",\"unique_id\":\"AGL000042020\",\"name\":\"Frank Amponsah Amoah\",\"email\":\"frankamoah@gmail.com\",\"phone_number\":null,\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Maureen Anim\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2021-01-01', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '2021-01-02 08:15:11', '1', '1', '2021-01-02 08:15:15', '2019/2020', '1st'),
(22, 'LKJAFD94R', 'student', '1', '[\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"SZMsssqcccfn5cBl0aaaPCj287hym36\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000032020\",\"name\":\"Emmanuella Darko Sarfowaa\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000042020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2021-01-01', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '2021-01-02 08:15:24', '1', '1', '2021-01-02 08:15:28', '2019/2020', '1st'),
(23, 'LKJAFD94R', 'teacher', NULL, '[\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\"]', '[{\"item_id\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000012020\",\"name\":\"Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"}]', '2021-01-02', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '2021-01-02 08:15:44', '1', '1', '2021-01-02 08:16:08', '2019/2020', '1st'),
(24, 'LKJAFD94R', 'teacher', NULL, '[\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\"]', '[{\"item_id\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000012020\",\"name\":\"Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"}]', '2021-01-01', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-02 08:17:44', '1', '1', '2021-01-02 08:18:05', '2019/2020', '1st'),
(25, 'LKJAFD94R', 'admin', NULL, '[\"uIkajsw123456789064hxk1fc3efmnva\"]', '[{\"item_id\":\"uIkajsw123456789064hxk1fc3efmnva\",\"unique_id\":\"kajflkdkfafd\",\"name\":\"Admin Account\",\"email\":\"test_admin@gmail.com\",\"phone_number\":\"+233240889023\",\"state\":\"present\"}]', '2021-01-02', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '2021-01-02 08:20:33', '1', '1', '2021-01-02 08:21:49', '2019/2020', '1st'),
(26, 'LKJAFD94R', 'admin', NULL, '[\"uIkajsw123456789064hxk1fc3efmnva\"]', '[{\"item_id\":\"uIkajsw123456789064hxk1fc3efmnva\",\"unique_id\":\"kajflkdkfafd\",\"name\":\"Admin Account\",\"email\":\"test_admin@gmail.com\",\"phone_number\":\"+233240889023\",\"state\":\"present\"}]', '2021-01-01', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '2021-01-02 08:22:07', '1', '1', '2021-01-02 08:22:11', '2019/2020', '1st'),
(27, 'LKJAFD94R', 'student', '1', '[\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"SZMsssqcccfn5cBl0aaaPCj287hym36\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000032020\",\"name\":\"Emmanuella Darko Sarfowaa\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000072020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2021-01-07', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-07 12:50:32', '1', '1', '2021-01-07 12:50:36', '2019/2020', '1st'),
(28, 'LKJAFD94R', 'student', '2', '[\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\"]', '[{\"item_id\":\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"unique_id\":\"AGL000022020\",\"name\":\"Grace Obeng-Yeboah\",\"email\":\"graciellaob@gmail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\",\"unique_id\":\"AGL000042020\",\"name\":\"Frank Amponsah Amoah\",\"email\":\"frankamoah@gmail.com\",\"phone_number\":null,\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000062020\",\"name\":\"Maureen Anim\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"}]', '2021-01-07', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-07 12:50:45', '1', '0', NULL, '2019/2020', '1st'),
(29, 'LKJAFD94R', 'teacher', NULL, '[\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\"]', '[{\"item_id\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000012020\",\"name\":\"Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"}]', '2021-01-06', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-07 12:50:58', '1', '1', '2021-01-07 12:51:26', '2019/2020', '1st'),
(30, 'LKJAFD94R', 'student', '1', '[\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"SZMsssqcccfn5cBl0aaaPCj287hym36\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000032020\",\"name\":\"Emmanuella Darko Sarfowaa\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000072020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2021-01-06', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-07 12:51:12', '1', '1', '2021-01-07 12:51:15', '2019/2020', '1st'),
(31, 'LKJAFD94R', 'teacher', NULL, '[\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\"]', '[{\"item_id\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000012020\",\"name\":\"Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"}]', '2021-01-07', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-07 12:51:54', '1', '1', '2021-01-07 12:52:09', '2019/2020', '1st');

-- --------------------------------------------------------

--
-- Table structure for table `users_chat`
--

CREATE TABLE `users_chat` (
  `id` int(11) UNSIGNED NOT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `message_unique_id` varchar(32) DEFAULT NULL,
  `sender_id` varchar(32) DEFAULT NULL,
  `receiver_id` varchar(32) DEFAULT NULL,
  `message` varchar(1000) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `seen_status` enum('0','1') NOT NULL DEFAULT '0',
  `seen_date` datetime DEFAULT current_timestamp(),
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
(1, '8f0oBFWCqNV9eSnR26T7gM5XvdEkaUbs', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'Hello test, mailer', '2020-10-13 09:51:47', '1', '2020-10-13 09:13:46', '0', '0', '5', NULL, NULL),
(2, '1mvAgrz75BPIZafbeFR2NHhEQqJSXMOW', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'yeah whats going on?', '2020-10-13 00:29:02', '1', '2020-10-13 09:13:54', '0', '0', '5', NULL, NULL),
(3, 'Fe5yqaClLtgS6MRN2YwUEx7ViosZIKGJ', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'all is well here', '2020-10-13 00:29:10', '1', '2020-10-13 09:13:46', '0', '0', '5', NULL, NULL),
(4, 'anObdsCTlM1tyeW8LgvFiYKXq967EHVR', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'your end?', '2020-10-14 00:29:16', '1', '2020-10-14 09:13:46', '0', '0', '5', NULL, NULL),
(5, 'hk1cL3AjYf2J6RD8WblXHEvVGoUpNdet', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'same here... i am doing perfectly great', '2020-10-14 00:29:24', '1', '2020-10-14 09:13:54', '0', '0', '5', NULL, NULL),
(6, 'WR6QqlchAwUCpzxe5dj7PGk0oNfmXrHV', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'thats good to know', '2020-10-14 00:29:30', '1', '2020-10-14 09:13:46', '0', '0', '5', NULL, NULL),
(7, 'lRf6G80PMN2WVAuvicOJBHkLCzyrQdwb', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'yeah sure', '2020-10-14 00:29:34', '1', '2020-10-14 09:13:54', '0', '0', '5', NULL, NULL),
(8, 'FlOEWwg4nvDUzPKLfkCR3yVAH9SZmIpj', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'chat you later then', '2020-10-14 00:29:39', '1', '2020-10-14 09:13:46', '0', '0', '5', NULL, NULL),
(9, '7NEhfGPBgkpFdAo3ZSVxRqCvs9tjY0O1', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'alright, bye for now', '2020-10-14 00:29:44', '1', '2020-10-14 09:13:54', '0', '0', '5', NULL, NULL),
(10, 'qFi2ypjm8NUn9rXObsu4Y1cR6CoBGe5d', 'CTOSCUEPFW1N5JZGKD2HV9ZX', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'hello priscilla', '2020-10-15 00:32:19', '1', '2020-10-16 10:02:42', '0', '0', '5', NULL, NULL),
(11, 'NnxatZUpECvmH5gIWylFfP7YRKc2Sk06', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'hello mr', '2020-10-15 09:06:16', '1', '2020-10-15 09:13:54', '0', '0', '5', NULL, NULL),
(12, 'kRl3Q5UM4uHvoqIDYjBpbGywNVWft90K', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'yeah, whats up with you?', '2020-10-15 09:06:38', '1', '2020-10-15 09:13:46', '0', '0', '5', NULL, NULL),
(13, '1XuWYdA784j3Mgr2OqmCfnb5xLtlhiBQ', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'i am doing great now, and you?', '2020-10-15 09:06:55', '1', '2020-10-15 09:13:54', '0', '0', '5', NULL, NULL),
(14, 'hWwE37oR8ufzg0MQ1KD6cxZAyiUHYsSv', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'same here i am perfect', '2020-10-15 09:07:05', '1', '2020-10-15 09:13:46', '0', '0', '5', NULL, NULL),
(15, 'exFLaXfi1v0YdkrDEOy2WJmgAPwK9URG', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'thats good to know', '2020-10-16 09:13:46', '1', '2020-10-16 09:13:54', '0', '0', '5', NULL, NULL),
(16, 'XEQRJMfDCr2xaNLHFoUiBs0tludpqTwb', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'alright...', '2020-10-16 09:13:54', '1', '2020-10-16 09:16:21', '0', '0', '5', NULL, NULL),
(17, 'oexm5t6CaOArGcg2SJviR3fNE9juLsIM', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'so sup now', '2020-10-16 09:16:03', '1', '2020-10-16 09:16:21', '0', '0', '5', NULL, NULL),
(18, 'yiqTMOcSfmVW7o1Kal9vHZ65rtnCpYxe', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'do i even know whats going on', '2020-10-16 09:16:21', '1', '2020-10-16 09:23:20', '0', '0', '5', NULL, NULL),
(19, 'PIJM7VfLnHGCWTByaNUkdegmqF4rixA9', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'hiii', '2020-10-16 09:23:20', '1', '2020-10-16 09:34:55', '0', '0', '5', NULL, NULL),
(20, 'T14vdgQUGj0lCtJyz2O6Ir9knDZHqVNp', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'what are you doing now?', '2020-10-16 09:23:25', '1', '2020-10-16 09:34:55', '0', '0', '5', NULL, NULL),
(21, '9Iu8oYVtd7s4nElJ2vWN0hT3CygOPDU1', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'respond oooooooo toom', '2020-10-16 09:24:24', '1', '2020-10-16 09:34:55', '0', '0', '5', NULL, NULL),
(22, 'BEUCWDM0Xxtoqdjlp17FKhNfuV3G82zY', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'whatsup', '2020-10-16 10:00:09', '1', '2020-10-16 10:00:24', '0', '0', '5', NULL, NULL),
(23, 'KIhYJFZTVwQmidPUy5czvAMD0OokefnB', 'CTOSCUEPFW1N5JZGKD2HV9ZX', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'yeah', '2020-10-16 10:02:46', '1', '2020-10-16 10:03:09', '0', '0', '5', NULL, NULL),
(24, 'ctjY3spiVE8xoCu91vPQJWG5mUKXDHyl', 'CTOSCUEPFW1N5JZGKD2HV9ZX', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'what dey go on this morning?', '2020-10-16 10:02:56', '1', '2020-10-16 10:03:09', '0', '0', '5', NULL, NULL),
(25, 'IrjTHzg7OwdRvlUJ2F3bm4QGBksWyM1D', 'UEKROHNAYS5PNTZAZCGF72TJ', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'all is well here', '2020-10-16 10:03:15', '1', '2020-10-16 10:03:20', '0', '0', '5', NULL, NULL),
(26, '29Rc7lufPoEOnTBJIYyhUCiX34Mwq5LF', 'CTOSCUEPFW1N5JZGKD2HV9ZX', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'alright thats nice', '2020-10-16 10:03:20', '1', '2020-10-16 10:03:40', '0', '0', '5', NULL, NULL),
(27, 'OBjzYpsZ385Di01dMgrtxHunFTlbWc2q', 'UEKROHNAYS5PNTZAZCGF72TJ', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'kkkk good to know', '2020-10-16 10:03:40', '1', '2020-10-16 10:05:29', '0', '0', '5', NULL, NULL),
(28, '8CiI7tRPGXzujBLFeHKOcJNqDrfAl3yd', 'LO6DKN9BDS40FBOQPPTWGMKE', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'hello solo', '2020-10-16 10:06:05', '1', '2020-10-16 10:06:21', '0', '0', '5', NULL, NULL),
(29, '376ApcyGE4HrDnWXwg12odMjfT5uQBxb', 'LO6DKN9BDS40FBOQPPTWGMKE', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'how are you today', '2020-10-16 10:06:12', '1', '2020-10-16 10:06:21', '0', '0', '5', NULL, NULL),
(30, 'jth02QkFGWER5Ser4zda3bKITiqV1m9n', 'EP8IKCY96NFYUD5URHLJWKTH', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'i am doing great', '2020-10-16 10:06:29', '1', '2020-10-16 10:06:38', '0', '0', '5', NULL, NULL),
(31, 'BYzQ05VdfPMahWr3KJEA2LRqsHIjbmSF', 'EP8IKCY96NFYUD5URHLJWKTH', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'and you?', '2020-10-16 10:06:32', '1', '2020-10-16 10:06:38', '0', '0', '5', NULL, NULL),
(32, 'MFsHKmedQz9PbkZwJcG04vEYC278tjxO', 'LO6DKN9BDS40FBOQPPTWGMKE', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'i am perfect as well', '2020-10-16 10:06:38', '1', '2020-10-20 13:30:24', '0', '0', '5', NULL, NULL),
(33, 'sbL9dCHI7yNEra8DVcoOBi0Wn1uvPwUf', 'SZJBUDUMNRXBGVH1R8DJISY2', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'hello nic', '2020-10-16 10:06:54', '1', '2020-10-16 10:07:04', '0', '0', '5', NULL, NULL),
(34, 'oMyx2Hnz0hEkAaSpNL8RciIG4jgO6Cdr', 'SZJBUDUMNRXBGVH1R8DJISY2', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'how are you?', '2020-10-16 10:06:56', '1', '2020-10-16 10:07:04', '0', '0', '5', NULL, NULL),
(35, '8FiMhYagZudjWfRw4bOl0mCp1kDzrUBV', 'KMLOZHQTXRAVEBPBCYHXLGN4', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'i am great', '2020-10-16 10:07:08', '1', '2020-10-16 10:07:21', '0', '0', '5', NULL, NULL),
(36, '1I6Clm3HUfZpWStQbDAiaVd5ETXwshxq', 'KMLOZHQTXRAVEBPBCYHXLGN4', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'and you>', '2020-10-16 10:07:10', '1', '2020-10-16 10:07:21', '0', '0', '5', NULL, NULL),
(37, 'RUMQZ5TxDmrj7CHbLadk0s3SpJWXNuPI', 'SZJBUDUMNRXBGVH1R8DJISY2', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'i am perfect as well', '2020-10-16 10:07:21', '1', '2020-10-16 10:22:33', '0', '0', '5', NULL, NULL),
(38, 'QzhXkWHV36PCagwYoquLldNiEtAjT8pM', 'LO6DKN9BDS40FBOQPPTWGMKE', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'hello priscy', '2020-10-16 10:07:30', '1', '2020-10-16 10:07:46', '0', '0', '5', NULL, NULL),
(39, 'GLVC7JYxaZgd1hswkK0cDtNUpiBWzjRF', 'LO6DKN9BDS40FBOQPPTWGMKE', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'sup', '2020-10-16 10:07:32', '1', '2020-10-16 10:07:46', '0', '0', '5', NULL, NULL),
(40, 'Jq8YPTK47OU2z0IVdrcwxm1kaLDZ36R5', 'JK4GBSJEA3BFGAPNQRKLPO89', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'yeah whats up', '2020-10-16 10:07:53', '1', '2020-10-16 10:23:42', '0', '0', '5', NULL, NULL),
(41, 'tzY1Bi2QnxGy3EpmUAHkDdWOseZNRXo9', 'JK4GBSJEA3BFGAPNQRKLPO89', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'hello', '2020-10-16 10:24:07', '1', '2020-10-16 10:39:39', '0', '0', '5', NULL, NULL),
(42, 'QJjUiNgXV8AFZlECMhanmDRqWYru0t59', 'JK4GBSJEA3BFGAPNQRKLPO89', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'sup with you?', '2020-10-16 10:24:35', '1', '2020-10-16 10:39:39', '0', '0', '5', NULL, NULL),
(43, '4wfqrJvk7VLFM8nHOxpd9U603PRDstjo', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'hello sup here', '2020-10-16 10:24:47', '1', '2020-10-16 10:25:19', '0', '0', '5', NULL, NULL),
(44, 'XDHaKQ4rRx0ScWlmewhAIV613MNEtBiO', 'LO6DKN9BDS40FBOQPPTWGMKE', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'hello', '2020-10-16 10:39:55', '1', '2020-10-16 10:56:34', '0', '0', '5', NULL, NULL),
(45, 'aFMLbANy4n1WUHPKRtjXT6EkIic2CQv0', 'LO6DKN9BDS40FBOQPPTWGMKE', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'when and how here', '2020-10-16 10:39:59', '1', '2020-10-16 10:56:34', '0', '0', '5', NULL, NULL),
(46, 'OBMXnsYpIxcZmguqDhj9AJUT8iR6Vz4K', 'V9REJG2HK4ZONEDSCK5NBA0P', 'BZ7ScjiWnKD0YbwONqleTIUvy3PksGzQ', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'hello', '2020-10-16 10:40:32', '1', '2020-10-19 18:08:39', '0', '0', '5', NULL, NULL),
(47, 'YTIzplo5hvedtiKjBJZWOsEx6G9gQFm7', 'V9REJG2HK4ZONEDSCK5NBA0P', 'BZ7ScjiWnKD0YbwONqleTIUvy3PksGzQ', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'how are you here', '2020-10-16 10:40:35', '1', '2020-10-19 18:08:39', '0', '0', '5', NULL, NULL),
(48, 'cXFA8jbVIqYCJzhMg2mUB1EO9ox60ikr', 'V9REJG2HK4ZONEDSCK5NBA0P', 'BZ7ScjiWnKD0YbwONqleTIUvy3PksGzQ', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'great to hear that you are working very great now', '2020-10-16 10:40:44', '1', '2020-10-19 18:08:39', '0', '0', '5', NULL, NULL),
(50, 'OS1W0XlDtgHA5pEU7M8k23RiFqILY4hu', 'EN0UTFOYJWMWPB6YTQXJ9HR4', 'BZ7ScjiWnKD0YbwONqleTIUvy3PksGzQ', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'hello', '2020-10-16 10:45:52', '1', '2020-10-19 18:08:39', '0', '0', '5', NULL, NULL),
(51, 'PbLgTZtkEmRhpijyQOfCs3IX1KeJvHoA', 'V9REJG2HK4ZONEDSCK5NBA0P', 'BZ7ScjiWnKD0YbwONqleTIUvy3PksGzQ', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'whats up', '2020-10-16 10:54:51', '1', '2020-10-19 18:08:39', '0', '0', '5', NULL, NULL),
(52, 'M21p9JtesZyT7okKDA6Hrj8QEmIVzPL4', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'hellooooo', '2020-10-16 10:55:10', '1', '2020-10-16 10:56:26', '0', '0', '5', NULL, NULL),
(53, 'f7Ue0Q6NDzBldhGXOTZHnPsyrmajAEM9', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'what dey go on', '2020-10-16 10:55:14', '1', '2020-10-16 10:56:26', '0', '0', '5', NULL, NULL),
(54, 'r6bU5Tp02LlXAKjSYe78aOIuxBhkD1cJ', 'LO6DKN9BDS40FBOQPPTWGMKE', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'it is there and here', '2020-10-16 10:57:09', '1', '2020-10-19 18:14:14', '0', '0', '5', NULL, NULL),
(55, 'Ug35OydQLGTzNfvDISY9bolnjtJXc6Ex', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'hii', '2020-10-16 10:57:56', '1', '2020-10-19 18:12:13', '0', '0', '5', NULL, NULL),
(56, 'FKwEzpClcTbgyB1X4Mr83jJs9DxLoGmP', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'hello world', '2020-10-16 10:57:58', '1', '2020-10-19 18:12:13', '0', '0', '5', NULL, NULL),
(57, 'ze4yaBqRLj8FmbgHC5X7wohfOITMlZvk', 'V9REJG2HK4ZONEDSCK5NBA0P', 'BZ7ScjiWnKD0YbwONqleTIUvy3PksGzQ', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'hello world', '2020-10-16 10:58:22', '1', '2020-10-19 18:08:39', '0', '0', '5', NULL, NULL),
(58, 'fZAyNpmRHseTb50F9g7qcD6k4ovjhClw', 'GXH0FNAWT48B9TRSWZIRVPH1', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'helo', '2020-10-16 10:58:47', '1', '2020-10-19 18:07:22', '0', '0', '5', NULL, NULL),
(59, 'H8y5tTDXL3hY1NOKlCiodSFAvnj0zPea', '9EPGFHYHUBCICTSRLL6V3QEB', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'hello', '2020-10-16 10:58:49', '1', '2020-10-19 18:07:22', '0', '0', '5', NULL, NULL),
(60, 'Jwty8v3RgW1zjLfbQZ0HiDhBFo94adAc', '9QF1NEAXKLXKTGWT4MOLIEPH', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'whats up with you here', '2020-10-16 10:58:54', '1', '2020-10-19 18:07:22', '0', '0', '5', NULL, NULL),
(61, 'RpOhU4lFxEzeJsfT9v1iXa6WgVLwrcPb', 'LO6DKN9BDS40FBOQPPTWGMKE', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'hello Sir', '2020-10-19 18:15:04', '1', '2020-10-19 18:25:40', '0', '0', '5', NULL, NULL),
(62, 'AflryEN7JZgqvtRhmFGOSBpxoUTabiQI', 'LO6DKN9BDS40FBOQPPTWGMKE', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'yeah', '2020-10-19 18:25:47', '1', '2020-10-19 18:25:54', '0', '0', '5', NULL, NULL),
(63, '5E9ibYSTLU3RwIBoHQCPWAxyerpJ1XdK', 'LO6DKN9BDS40FBOQPPTWGMKE', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'how do you do?', '2020-10-19 18:25:51', '1', '2020-10-19 18:25:54', '0', '0', '5', NULL, NULL),
(64, 'yzjhxsMBk3eZlUDEwo7dO9f1m5Xu4tqH', 'LO6DKN9BDS40FBOQPPTWGMKE', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'all is well here', '2020-10-19 18:26:01', '1', '2020-10-19 18:28:47', '0', '0', '5', NULL, NULL),
(65, 'vDgqOcTWhMXaASBEJurpyoKHFd59b4wR', 'LO6DKN9BDS40FBOQPPTWGMKE', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'thats good to know', '2020-10-19 18:28:47', '1', '2020-10-19 18:38:51', '0', '0', '5', NULL, NULL),
(66, 'Z1X7fvAQSbtBFM5sUHGCcOkrx0nIEP2y', 'LO6DKN9BDS40FBOQPPTWGMKE', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'are you busy?', '2020-10-19 18:31:46', '1', '2020-10-19 18:38:51', '0', '0', '5', NULL, NULL),
(67, 'qWcULw1bIpZPC036fxMzXVel5HFhrB9g', 'LO6DKN9BDS40FBOQPPTWGMKE', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'no please', '2020-10-19 18:38:58', '1', '2020-10-19 18:39:58', '0', '0', '5', NULL, NULL),
(68, 'riW8vyuXhIQCmtaRgkFL20jYqSsJPo71', 'LO6DKN9BDS40FBOQPPTWGMKE', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'alright well noted', '2020-10-19 18:40:12', '1', '2020-10-19 18:47:27', '0', '0', '5', NULL, NULL),
(69, 'XOxkLGRDhc9l1FdeasVgjm4UuPIb3wKA', 'BSOZHM19JKFKDCSVIPGRH6UQ', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'G9fI4VlHtRPga5Ezq78S6wjNYcunFAs0', 'hello grace', '2020-10-19 18:40:52', '0', '2020-10-19 18:40:52', '0', '0', '5', NULL, NULL),
(70, '3LTPsIvb04hVUkXAmn2iRKEaq5gGODfJ', 'LO6DKN9BDS40FBOQPPTWGMKE', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'so whats the plans for today?', '2020-10-19 18:41:07', '1', '2020-10-19 18:47:27', '0', '0', '5', NULL, NULL),
(71, 'fTtgyqPVCloGKdR58zsrpMNUw2W3DB0x', 'LO6DKN9BDS40FBOQPPTWGMKE', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'why are you wasting so much time responding to my message?', '2020-10-19 18:43:07', '1', '2020-10-19 18:47:27', '0', '0', '5', NULL, NULL),
(72, 'QIpgOjDaCVzJuZ7WFA4tPNBkfq15oUrY', 'LO6DKN9BDS40FBOQPPTWGMKE', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'hiiii', '2020-10-19 18:45:27', '1', '2020-10-19 18:47:27', '0', '0', '5', NULL, NULL),
(73, 'BIFWSolRmteZYVf2Ci5TQOhnNPHrAGKp', 'CTITJFOLBCAYG6V0U37DLMKE', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '5U1hKwp4HZNSW80jRCXEVrIJOvmYTAyd', 'sammy', '2020-10-19 18:45:51', '0', '2020-10-19 18:45:51', '0', '0', '5', NULL, NULL),
(74, 'm7MhrCEd96Xyt0aqKl1k2DWf4LUN8sjG', 'LO6DKN9BDS40FBOQPPTWGMKE', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'priscy', '2020-10-19 18:46:01', '1', '2020-10-19 18:47:27', '0', '0', '5', NULL, NULL),
(75, 'QDyMozCBpSridjNs2KVvqPYwxX53UIAF', 'LO6DKN9BDS40FBOQPPTWGMKE', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'awww', '2020-10-19 18:47:32', '1', '2020-10-19 18:47:44', '0', '0', '5', NULL, NULL),
(76, 'H1RDoqce0W476bELGdCM2Fjy8tTiJuwp', 'LO6DKN9BDS40FBOQPPTWGMKE', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'am sorry please', '2020-10-19 18:47:35', '1', '2020-10-19 18:47:44', '0', '0', '5', NULL, NULL),
(77, 'hcfsvtXYLx3nCoZU2JNKRlkTBWj4aDed', 'LO6DKN9BDS40FBOQPPTWGMKE', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'alright no problem', '2020-10-19 18:47:50', '1', '2020-10-19 18:47:55', '0', '0', '5', NULL, NULL),
(78, 'anloiL3y7hfwrZPYjxESGJz6A9HTmU4g', 'LO6DKN9BDS40FBOQPPTWGMKE', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'kkk', '2020-10-19 18:47:55', '1', '2020-10-19 18:48:42', '0', '0', '5', NULL, NULL),
(79, 'TuixrWv82Y3o47ytapZzlQE0hXeVNCLG', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'what is going on at your end?', '2020-10-19 18:48:07', '1', '2020-10-20 13:31:43', '0', '0', '5', NULL, NULL),
(80, 'oAdcY5uLNpTen0MJfiwqI6b2Ka9USZ4D', 'LO6DKN9BDS40FBOQPPTWGMKE', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'can you please answer the questions i asked initially', '2020-10-19 18:48:31', '1', '2020-10-19 18:48:42', '0', '0', '5', NULL, NULL),
(81, 'MDwJ1clrIxEuOpPRV38bWNhXsLSZAiB0', 'LO6DKN9BDS40FBOQPPTWGMKE', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'did you ask any questions?', '2020-10-19 18:48:55', '1', '2020-10-19 18:49:02', '0', '0', '5', NULL, NULL),
(82, 'wdTI5AJPrOl8eKCUHLYh4Ro1vatyX963', 'LO6DKN9BDS40FBOQPPTWGMKE', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'sorry!', '2020-10-19 18:49:02', '1', '2020-10-20 08:44:43', '0', '0', '5', NULL, NULL),
(83, 'wa0KYDF4LTE9bBGVNPxlszHkdphMoyO1', 'MH9AAV7BTKJGGFUF2YLWIHE6', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'G9fI4VlHtRPga5Ezq78S6wjNYcunFAs0', 'yo', '2020-10-19 19:03:34', '0', '2020-10-19 19:03:34', '0', '0', '5', NULL, NULL),
(84, 'LpitUkK9JCDov8ScdRQMeVTyjswBul36', 'MH9AAV7BTKJGGFUF2YLWIHE6', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'G9fI4VlHtRPga5Ezq78S6wjNYcunFAs0', 'yo', '2020-10-19 19:04:13', '0', '2020-10-19 19:04:13', '0', '0', '5', NULL, NULL),
(85, 'wsP6Yy9Le82MOktXhVqWzSIAxpunrmvR', 'LO6DKN9BDS40FBOQPPTWGMKE', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'alright, no problem', '2020-10-19 19:07:05', '1', '2020-10-20 08:47:28', '0', '0', '5', NULL, NULL),
(86, 'w9RH4uiNMrsvI01aymZUgqVznYJQcXxF', 'LO6DKN9BDS40FBOQPPTWGMKE', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'sup', '2020-10-19 19:08:40', '1', '2020-10-20 08:47:28', '0', '0', '5', NULL, NULL),
(87, 'YF48xLMOfz9TBG1XQvDESrZVjKnsd3Uu', 'LO6DKN9BDS40FBOQPPTWGMKE', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'i guess, the nature of the items will depend on what to?', '2020-10-19 19:08:55', '1', '2020-10-19 19:13:45', '0', '0', '5', NULL, NULL),
(88, 'MVQ9RDutTnpsjIaWfbPcFdS0YBhgX4K2', 'LO6DKN9BDS40FBOQPPTWGMKE', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'eyo, good', '2020-10-19 19:13:41', '1', '2020-10-19 19:13:45', '0', '0', '5', NULL, NULL),
(89, '3CcPzmGSgsry5tukaIpXlK9xd4nD0Uj8', 'LO6DKN9BDS40FBOQPPTWGMKE', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'eeeish', '2020-10-19 19:13:57', '1', '2020-10-20 08:44:43', '0', '0', '5', NULL, NULL),
(90, 'SqFge2c3hMRtsfGmoTuQWkbJDwPxO8zH', 'MH9AAV7BTKJGGFUF2YLWIHE6', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'G9fI4VlHtRPga5Ezq78S6wjNYcunFAs0', 'yeap', '2020-10-19 20:57:49', '0', '2020-10-19 20:57:49', '0', '0', '5', NULL, NULL),
(91, 'St4KjfUuqMenl0pZR13PNX7v6ELFBCYx', 'LO6DKN9BDS40FBOQPPTWGMKE', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'eyo', '2020-10-19 20:57:56', '1', '2020-10-19 20:58:13', '0', '0', '5', NULL, NULL),
(92, 'AxmDkNtzv48Lil31fH0nScI5pKyWTYQP', 'LO6DKN9BDS40FBOQPPTWGMKE', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'i am not typing so please stop it', '2020-10-19 20:58:24', '1', '2020-10-20 08:47:28', '0', '0', '5', NULL, NULL),
(93, 'V5Jd4mbvaGcYRS6BnE31hi79tyArNuLD', 'BSOZHM19JKFKDCSVIPGRH6UQ', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'G9fI4VlHtRPga5Ezq78S6wjNYcunFAs0', 'hello', '2020-10-20 08:57:27', '0', '2020-10-20 08:57:27', '0', '0', '5', NULL, NULL),
(94, 'FjUsrL7VMcXQAN25E13azRPvkGw6hIgm', 'EXFBA9MQICGHROPQ2MF15UNE', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', '5U1hKwp4HZNSW80jRCXEVrIJOvmYTAyd', 'hello sir', '2020-10-20 08:57:32', '0', '2020-10-20 08:57:32', '0', '0', '5', NULL, NULL),
(95, 'NUPpb8e3KJIgsVxwk1WaLFA9m4YfytX5', 'IVY62SWM9KFO5BL1KH0XWGXJ', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', '5U1hKwp4HZNSW80jRCXEVrIJOvmYTAyd', 'how are you doing?', '2020-10-20 08:57:34', '0', '2020-10-20 08:57:34', '0', '0', '5', NULL, NULL),
(96, 'HpUY0Bk3QtnLREocDKMsVxdJhiqWwl7F', 'LO6DKN9BDS40FBOQPPTWGMKE', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Hello', '2020-10-20 08:58:20', '1', '2020-10-20 08:58:31', '0', '0', '5', NULL, NULL),
(97, 'bl7tGnLYAvB8aq1STRigXC42dIp693rO', 'LO6DKN9BDS40FBOQPPTWGMKE', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'yeah', '2020-10-20 08:58:33', '1', '2020-10-20 09:01:28', '0', '0', '5', NULL, NULL),
(98, 'xJ8epLBqsN1jZHhn59XEvlDgC24YrtO7', 'LO6DKN9BDS40FBOQPPTWGMKE', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'how are you doing?', '2020-10-20 08:58:39', '1', '2020-10-20 09:01:28', '0', '0', '5', NULL, NULL),
(99, 'F7qerYpTV8lLXcmA14sIGS309MzHK65o', 'EXFBA9MQICGHROPQ2MF15UNE', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', '5U1hKwp4HZNSW80jRCXEVrIJOvmYTAyd', 'eyo', '2020-10-20 09:00:19', '0', '2020-10-20 09:00:19', '0', '0', '5', NULL, NULL),
(100, 'ruaItq2C3NiYR4bwDlyfcSKpnWvoHMQ6', 'LO6DKN9BDS40FBOQPPTWGMKE', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'i believe all is well', '2020-10-20 09:00:26', '1', '2020-10-20 13:30:24', '0', '0', '5', NULL, NULL),
(101, 'rUFvoiB2mezaX1OhwuYbgWn0P7sDfCQ3', 'XZ7B28JFTWOGAPQABFKNVC9E', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'BZ7ScjiWnKD0YbwONqleTIUvy3PksGzQ', 'good to know here', '2020-10-20 09:00:33', '0', '2020-10-20 09:00:33', '0', '0', '5', NULL, NULL),
(102, 'zliXg8Hb0qVNIuMALd5SKmEWJhG1ROj4', 'EXFBA9MQICGHROPQ2MF15UNE', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', '5U1hKwp4HZNSW80jRCXEVrIJOvmYTAyd', 'thats nice', '2020-10-20 09:00:40', '0', '2020-10-20 09:00:40', '0', '0', '5', NULL, NULL),
(103, 'u0FyDp75vtaHiIkeUJ1oAPlsmMXzw89C', 'LO6DKN9BDS40FBOQPPTWGMKE', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'i am good here', '2020-10-20 09:01:33', '1', '2020-10-20 09:02:17', '0', '0', '5', NULL, NULL),
(104, 'GJ3avReOf1psd2y8Zbjn5wqcQli7NDYF', 'LO6DKN9BDS40FBOQPPTWGMKE', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', ' and you?', '2020-10-20 09:01:34', '1', '2020-10-20 09:02:17', '0', '0', '5', NULL, NULL),
(105, 'y95qQTjBUD2F7gnu0Eob3SNiKGHeXd1a', 'LO6DKN9BDS40FBOQPPTWGMKE', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'all is well here as well', '2020-10-20 09:02:28', '1', '2020-10-20 09:02:54', '0', '0', '5', NULL, NULL),
(106, 'Dz1GRBlaxWkOc3A8sSCqtVIpUEiF5X4Y', 'LO6DKN9BDS40FBOQPPTWGMKE', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'alright', '2020-10-20 09:02:57', '1', '2020-10-20 09:03:55', '0', '0', '5', NULL, NULL),
(107, 'S1f49BCaYHezMUDbjLd3IiAQJsWch5lq', 'LO6DKN9BDS40FBOQPPTWGMKE', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'good to know', '2020-10-20 09:03:00', '1', '2020-10-20 09:03:55', '0', '0', '5', NULL, NULL),
(108, 'nt4DgLeWRkC9AXrH3SMdmGolYUJzO6Tq', 'LO6DKN9BDS40FBOQPPTWGMKE', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'alright', '2020-10-20 09:04:00', '1', '2020-10-20 09:07:17', '0', '0', '5', NULL, NULL),
(109, '0IawU9T2HoqP7YkVd1J4xGEMmZnpiz8R', 'LO6DKN9BDS40FBOQPPTWGMKE', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'hello', '2020-10-20 13:32:01', '0', '2020-10-20 13:32:01', '0', '0', '5', NULL, NULL),
(110, 'ItbCmN6MEso1HZYAJ39iyaLQfw5gROj0', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'hello Frank', '2020-10-20 13:32:31', '1', '2020-10-20 13:32:53', '0', '0', '5', NULL, NULL),
(111, 'fxwOzZnDpk7JquthgIG9ELWQ4U3NRS1B', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'hello, how are you doing?', '2020-10-20 13:33:03', '1', '2020-10-20 13:33:26', '0', '0', '5', NULL, NULL),
(112, 'qMvJ6XVEfo9Ry4HhuPUBL5c2AIjab08N', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'hows your day going?', '2020-10-20 13:33:23', '1', '2020-10-20 13:33:26', '0', '0', '5', NULL, NULL),
(113, 'uzXaGfAmTrE2ht7HReCgKMOqbspwQiZB', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'its been great here', '2020-10-20 13:33:35', '1', '2020-10-20 13:35:56', '0', '0', '5', NULL, NULL),
(114, 'dlteShCEgA8xPYB0uNfJjwrk19a3QDFs', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'and you rend?', '2020-10-20 13:33:38', '1', '2020-10-20 13:35:56', '0', '0', '5', NULL, NULL),
(115, 'bYl9aOzGLnyrNHEdUpBIDQ56kCwtZX3J', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'its been perfect as well', '2020-10-20 13:37:38', '1', '2020-10-20 13:38:33', '0', '0', '5', NULL, NULL),
(116, '5MRWyEGjlFmwO9DHk1P6cf0SziCnvJoK', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'alright', '2020-10-20 13:38:42', '1', '2020-10-20 13:38:53', '0', '0', '5', NULL, NULL),
(117, 'jXgCHnKvNA9elsdcD3LiU7yEoO0xWM4G', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'kk', '2020-10-20 13:38:59', '1', '2020-10-20 13:39:23', '0', '0', '5', NULL, NULL),
(118, 'S4icrYCKzD78EA5JmuZMXRphndkFbPse', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'i would like to meet you somewhere next week', '2020-10-20 13:39:08', '1', '2020-10-20 13:39:23', '0', '0', '5', NULL, NULL),
(119, 'LDdHZfeaTrzmFECit3yvg5n0UB2klVRK', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'will that be possible', '2020-10-20 13:39:14', '1', '2020-10-20 13:39:23', '0', '0', '5', NULL, NULL),
(120, 'NV21Rvo9heLHb7fmWG53ZAtTwExOsBjD', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'yeah sure it will', '2020-10-20 13:39:23', '1', '2020-10-20 13:39:31', '0', '0', '5', NULL, NULL),
(121, 'hQAomteKu1VEbif3lT0JLOB8sjXazUqc', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'kkk', '2020-10-20 13:39:31', '1', '2020-10-20 13:41:52', '0', '0', '5', NULL, NULL),
(122, 'vNrpb3MdexZVchqnD9guEjzY4Ryt7H5B', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'come along with 2 of your friends', '2020-10-20 13:39:54', '1', '2020-10-20 13:41:52', '0', '0', '5', NULL, NULL),
(123, 'U2z8gs3lCdIhZWc1NBLJe0uYHRa6AO7r', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'i hope that wont be a problem', '2020-10-20 13:41:47', '1', '2020-10-20 13:41:52', '0', '0', '5', NULL, NULL),
(124, 'YSqXFlUjz3GhLniNIsA0ZTVd5m6P94Ob', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'not at all', '2020-10-20 13:41:55', '1', '2020-10-20 13:45:12', '0', '0', '5', NULL, NULL),
(125, 'YG9RsZT1wBtaecVLSlNUOvhzpAW5dgCQ', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'i am cool with that', '2020-10-20 13:41:59', '1', '2020-10-20 13:45:12', '0', '0', '5', NULL, NULL),
(126, '6Br7yVCHPfsMOD21QhSgG9NFivpuwLU0', 'SZJBUDUMNRXBGVH1R8DJISY2', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'how are you', '2020-10-20 13:42:19', '1', '2020-10-20 13:42:23', '0', '0', '5', NULL, NULL),
(127, 'XOzqjuEkpcDS3908B7x1wd5QeJUlHiYK', 'SZJBUDUMNRXBGVH1R8DJISY2', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'i am doing great', '2020-10-20 13:42:26', '1', '2020-10-20 13:42:39', '0', '0', '5', NULL, NULL),
(128, 'ZcySoMeuvABaxYEdIFlOLr21sKqgRQb0', 'SZJBUDUMNRXBGVH1R8DJISY2', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'and you please', '2020-10-20 13:42:30', '1', '2020-10-20 13:42:39', '0', '0', '5', NULL, NULL),
(129, 'agAJ5EFTDrPv0XyINpVeGlYWkcuqb6BO', 'SZJBUDUMNRXBGVH1R8DJISY2', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'same here', '2020-10-20 13:42:39', '1', '2020-10-20 13:42:51', '0', '0', '5', NULL, NULL),
(130, 'mJylv5gViKohAYM8szuSTOaEn64Hr9tC', 'SZJBUDUMNRXBGVH1R8DJISY2', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'i am great', '2020-10-20 13:42:41', '1', '2020-10-20 13:42:51', '0', '0', '5', NULL, NULL),
(131, 'WkRm7tDBOJnqbQvuiHxrTVc15LAEdy2o', 'SZJBUDUMNRXBGVH1R8DJISY2', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'thats good to know', '2020-10-20 13:42:51', '1', '2020-10-20 13:43:10', '0', '0', '5', NULL, NULL),
(132, '7D5mYSGhykPje0Cs1QaEzBH3dNTUKqpx', 'SZJBUDUMNRXBGVH1R8DJISY2', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'He opened discussion on the Sub-Committee Terms of Reference. Members discussed details of the Terms of Reference and came to an understanding on what the Committee was expected to do.. There was a detailed discussion of ideas', '2020-10-20 13:46:31', '1', '2020-10-20 13:49:54', '0', '0', '5', NULL, NULL),
(133, 'iXErUdl98Q3vGMza1JmxCDob75cwfPsq', 'XWQJGTTUSNARXWSBK9ELRYVP', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '7S89OC3soyemFhrj24qtP6ifvLzxQK1U', '\n', '2020-10-20 13:53:15', '0', '2020-10-20 13:53:15', '0', '0', '5', NULL, NULL),
(134, 'TXuGVAyK3Qxtrn9Okd0HRlcpviU4E7wD', 'XWQJGTTUSNARXWSBK9ELRYVP', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '7S89OC3soyemFhrj24qtP6ifvLzxQK1U', '\n', '2020-10-20 13:54:08', '0', '2020-10-20 13:54:08', '0', '0', '5', NULL, NULL),
(135, 'jey0sb1zAmf2TFNQUEMKuti6hPIX3rZa', 'LO6DKN9BDS40FBOQPPTWGMKE', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'hello, sup for now\n', '2020-10-20 13:54:51', '0', '2020-10-20 13:54:51', '0', '0', '5', NULL, NULL),
(136, 'GpyliOCPBA6I0fxvcMq49NXDh5H78ZJe', 'LO6DKN9BDS40FBOQPPTWGMKE', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'i believe all is well\nthat is why i wanted to come over last monday\n', '2020-10-20 13:55:51', '0', '2020-10-20 13:55:51', '0', '0', '5', NULL, NULL),
(137, '5M1Ao4fzRk7NKyVXlPIYhgZuHpdr8CBJ', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'yeah whats the deal now\n', '2020-10-20 13:56:12', '1', '2020-10-20 14:02:58', '0', '0', '5', NULL, NULL),
(138, '9wTkYLS2A1cVMuHaIbGCDjyomKRNrWns', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'i believe\n', '2020-10-20 13:56:16', '1', '2020-10-20 14:02:58', '0', '0', '5', NULL, NULL),
(139, 'GWHDzCnFZVOijmL6Boltk2R7bsEe0uyM', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'that\nthe deal is what we have been waiting for\n', '2020-10-20 13:56:29', '1', '2020-10-20 14:02:58', '0', '0', '5', NULL, NULL),
(140, 'erVJ1Px3DlZKp0CWSmywi5h6TOansXIM', 'S4JFNVOVL869PZ25U13BOC0I', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', NULL, 'aflkajlfd jalkfdja lkda jflkd jakjkajfafd\nlkjflafd\n', '2020-10-20 14:00:11', '0', '2020-10-20 14:00:11', '0', '0', '5', NULL, NULL),
(141, 'wvy8uYDeSJmqCpoxk3cgzF5Harlhf7A4', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'eyo\n', '2020-10-20 15:48:27', '1', '2020-10-20 15:48:36', '0', '0', '5', NULL, NULL),
(142, 'wCXYVERp7nIP1G6rfkqgisFuoKB4c5b3', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'whats up\n', '2020-10-20 15:48:45', '1', '2020-10-20 15:49:30', '0', '0', '5', NULL, NULL),
(143, 'wZD7eEpd8mCFIqjxairy0kOUXWfMGP6S', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'i am doing great\n', '2020-10-20 15:49:43', '1', '2020-10-20 15:49:48', '1', '0', '5', NULL, NULL),
(144, 'udoGrEVT89CmQzNyvg3fMSbJlKqaIiw4', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'and you?\n', '2020-10-20 15:49:48', '1', '2020-10-20 15:49:55', '0', '0', '5', NULL, NULL),
(145, 'qkn9XE1IJPA0ymZ4uO7edofLHwYl5KGB', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'same here\n', '2020-10-20 15:49:55', '1', '2020-10-20 15:50:01', '1', '0', '5', NULL, NULL),
(146, 'Y8Peifomz2vFqMDwdkpTK4xXCun9Has1', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'thats good to know\n', '2020-10-20 15:50:01', '1', '2020-10-20 15:59:41', '0', '0', '5', NULL, NULL),
(147, 'NsHW4aJVR2CZYQoA65eEI3Gp0BDF9XUL', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'hey\n', '2020-10-20 16:13:27', '1', '2020-10-20 16:13:40', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL),
(148, 'xy2SUp307AwHWfvBP1ROouNcV6nGE5lq', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'yep\n', '2020-10-20 16:13:49', '1', '2020-10-20 16:14:01', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36||Windows 10||Chrome||::1', NULL),
(149, '1RceYit9HTSynJsjKXBf8QwaCNOVp24d', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'wetin dey go on\n', '2020-10-20 16:14:01', '1', '2020-10-20 16:14:08', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL),
(150, 'IGkjmwzFMLaTDWO9B1qNA3X2i6RsvSpn', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'do i even have any idea\n', '2020-10-20 16:14:08', '1', '2020-10-20 16:14:10', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36||Windows 10||Chrome||::1', NULL),
(151, 'AhOCkWB3u9JfsQPFNoZgXaU5r2GzIDTR', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'alright\n', '2020-10-20 16:14:10', '1', '2020-10-20 16:14:24', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL),
(152, '5E3weMmgZ19QsvaHcpAu7hBWzLt2PJbO', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'lemme know when something comes to mind\n', '2020-10-20 16:14:18', '1', '2020-10-20 16:14:24', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL),
(153, 'f45bQEHha2A0DyRIsMrt63XjLBeNzk9J', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'alright, will do just that\n', '2020-10-20 16:14:24', '1', '2020-10-20 16:14:28', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36||Windows 10||Chrome||::1', NULL),
(154, 'o5ltRkcmxi9Aj43phVWeZ0dzBFrgU1wv', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'well noted\n', '2020-10-20 16:14:28', '1', '2020-10-20 16:14:32', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL),
(155, 'eK7ptZI9yCXr3cAW8wSLnUmEO10q6jhP', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'kkkk\n', '2020-10-20 16:14:32', '1', '2020-10-20 16:15:47', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36||Windows 10||Chrome||::1', NULL),
(156, 'gSTx62c8ubfhjzsQpO7DAMNoF4JKPqXd', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'i am really loving this still\n', '2020-10-20 16:14:46', '1', '2020-10-20 16:15:47', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36||Windows 10||Chrome||::1', NULL),
(157, 'mJ4SKy8pnsWVvM9fqTIQdcFLY6jgu0N7', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'sorry\n', '2020-10-20 16:14:53', '1', '2020-10-20 16:15:47', '1', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36||Windows 10||Chrome||::1', NULL),
(158, 'BRWk0OHDgMdswyLqSZbAECKn5zaXIG72', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'sorry\n*style*\n---', '2020-10-20 16:15:06', '1', '2020-10-20 16:15:47', '1', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36||Windows 10||Chrome||::1', NULL),
(159, 'OWtm9ySuRfzZs8kqFAdTlJEI5hCDU07p', 'SZJBUDUMNRXBGVH1R8DJISY2', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'kkk\n', '2020-10-20 16:17:31', '1', '2020-10-20 16:17:41', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0||Windows 10||Firefox||127.0.0.1', NULL),
(160, 'VaY5iEfr2R0HdXUSgntmILk1oF4uOMGA', 'SZJBUDUMNRXBGVH1R8DJISY2', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'i get you now\n', '2020-10-20 16:17:35', '1', '2020-10-20 16:17:41', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0||Windows 10||Firefox||127.0.0.1', NULL),
(161, 'mT2QYUsqcxAWoZnRadIXtVwLpDyFKNij', 'SZJBUDUMNRXBGVH1R8DJISY2', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'no problem\n', '2020-10-20 16:17:47', '1', '2020-10-20 16:26:06', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36||Windows 10||Chrome||::1', NULL),
(162, 'CsBplRG2aZMfiIE19bLtVdKyNA6zSYjD', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'hello frank\n', '2020-10-20 16:17:52', '1', '2020-10-20 16:18:11', '1', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36||Windows 10||Chrome||::1', NULL),
(163, 'vk9UZt8Wlxniz3QFpTMfIYXeBS2mbPKd', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'i believe we are on the same track\n', '2020-10-20 16:18:03', '1', '2020-10-20 16:18:11', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36||Windows 10||Chrome||::1', NULL),
(164, 'T9IM7C5a0VBefPjtpwnxD4WUyQcGZ6mk', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'yeah sure\n', '2020-10-20 16:18:11', '1', '2020-10-20 16:18:25', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL),
(165, 'QH7U3JKThb1rMlNpPgwSGsjEmaFdILWV', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'we are there\n', '2020-10-20 16:18:14', '1', '2020-10-20 16:18:25', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL),
(166, '4TIyZfBMu3SV2EdqX9nDgv7KslRUt0ph', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'what do you think?\n', '2020-10-20 16:18:18', '1', '2020-10-20 16:18:25', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL),
(167, 'kv0EHdD2X9Roribhuzy5ItqgJLwAP7Ge', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'do i even know what to think\n', '2020-10-20 16:18:25', '1', '2020-10-20 16:18:46', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36||Windows 10||Chrome||::1', NULL),
(168, 'kI5ihQENbPJDF941AozCOtVSWwK6HRxu', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'i am not sure what we should be considering\n', '2020-10-20 16:18:37', '1', '2020-10-20 16:18:46', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36||Windows 10||Chrome||::1', NULL),
(169, '6IgxGdLMXCQvwtcDB4EVYNZ52sKf1yFR', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'alright, lemme know when you do\n', '2020-10-20 16:18:46', '1', '2020-10-20 16:20:10', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL),
(170, 'wPeB1KhpZqXzQiTYUMOk4RdJsgo8fWcC', 'GXH0FNAWT48B9TRSWZIRVPH1', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'i think we owe you the best thing to do\n', '2020-10-20 16:19:47', '1', '2020-10-20 16:19:56', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0||Windows 10||Firefox||127.0.0.1', NULL),
(171, 'weyDzJKniZSxsTplU0oH3uQ8kmP7dLMF', 'GXH0FNAWT48B9TRSWZIRVPH1', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'oh saa\n', '2020-10-20 16:19:59', '1', '2020-10-20 16:20:22', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL),
(172, 'mqdcjMoAyIULzsfZ4liOWkKxat2JRuE9', 'GXH0FNAWT48B9TRSWZIRVPH1', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'what dey happen for your end/\n', '2020-10-20 16:20:05', '1', '2020-10-20 16:20:22', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL),
(173, 'pWsIiX6PqMYxkLQHuynb1S2EleNcK5BV', 'GXH0FNAWT48B9TRSWZIRVPH1', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', '?\n', '2020-10-20 16:20:06', '1', '2020-10-20 16:20:22', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL),
(174, 'NC7Voc41EXShQ5G9WTKz2puLk6FgRJfr', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'alright\n', '2020-10-20 16:20:13', '1', '2020-10-20 16:23:47', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36||Windows 10||Chrome||::1', NULL),
(175, 'U9TvVmRuScipnKANoPWFd4hqXbrDwQ60', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'i will\n', '2020-10-20 16:20:14', '1', '2020-10-20 16:23:47', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36||Windows 10||Chrome||::1', NULL),
(176, 'IgDCJfaeSUksH02lbrwnuiN6j1yOtLXP', 'GXH0FNAWT48B9TRSWZIRVPH1', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'nothing much\n', '2020-10-20 16:20:23', '1', '2020-10-20 16:20:46', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0||Windows 10||Firefox||127.0.0.1', NULL),
(177, 'zuytCAeDMTdQfPF07jpHUgaJo3wiRqkY', 'GXH0FNAWT48B9TRSWZIRVPH1', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'i am just working things out\n', '2020-10-20 16:20:35', '1', '2020-10-20 16:20:46', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0||Windows 10||Firefox||127.0.0.1', NULL),
(178, 'DI3NeJjry19HnGzZ5VgRipY8bA4Pok0x', 'GXH0FNAWT48B9TRSWZIRVPH1', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'alright, lemme know when all is successful\n', '2020-10-20 16:20:46', '1', '2020-10-20 16:20:51', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL),
(179, 'X0zIHfQLMnmdUxTGbFeurViyj51W6EKA', 'GXH0FNAWT48B9TRSWZIRVPH1', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'sure will do\n', '2020-10-20 16:20:51', '1', '2020-10-20 16:21:04', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0||Windows 10||Firefox||127.0.0.1', NULL),
(180, 'cq5opQDRMLOe4dZySzjFkVv9ExAUXIiY', 'GXH0FNAWT48B9TRSWZIRVPH1', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'catch you later\n', '2020-10-20 16:20:57', '1', '2020-10-20 16:21:04', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0||Windows 10||Firefox||127.0.0.1', NULL),
(181, 'KsljQ0fXITZ5utzgH2mCGAYV47oL619F', 'GXH0FNAWT48B9TRSWZIRVPH1', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'bye for now\n', '2020-10-20 16:20:59', '1', '2020-10-20 16:21:04', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0||Windows 10||Firefox||127.0.0.1', NULL),
(182, 'TsQlRfn49yKZqN5mB2ztYiOwU8CEF7k0', 'GXH0FNAWT48B9TRSWZIRVPH1', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'same here\n', '2020-10-20 16:21:04', '1', '2020-10-20 16:26:05', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL),
(183, 'q8hVaGc0boWtnDQFEUBf1AikgCwyNHve', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'hello  world\n', '2020-10-20 16:30:16', '1', '2020-10-20 16:40:59', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL),
(184, 'NXKovZfkyLA9WO3l06gbEz1CYhqFPB4c', 'SZJBUDUMNRXBGVH1R8DJISY2', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'hello\n', '2020-10-20 16:33:50', '1', '2020-10-20 16:33:59', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0||Windows 10||Firefox||127.0.0.1', NULL),
(185, 'Hrnvs3PfwaTyhC8WbxLNGlS6J0XR1eip', 'SZJBUDUMNRXBGVH1R8DJISY2', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'great work done here\n', '2020-10-20 16:34:11', '1', '2020-10-20 18:09:23', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36||Windows 10||Chrome||::1', NULL);
INSERT INTO `users_chat` (`id`, `item_id`, `message_unique_id`, `sender_id`, `receiver_id`, `message`, `date_created`, `seen_status`, `seen_date`, `sender_deleted`, `receiver_deleted`, `notice_type`, `user_agent`, `user_signature`) VALUES
(186, 'PQTRD18Guphj6gFUqtsXwv3ofBdCeWxm', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'kk\n', '2020-10-20 16:41:13', '1', '2020-10-20 16:44:27', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36||Windows 10||Chrome||::1', NULL),
(187, 'tZCDf2VAFg9pqP3Mz0INLbOK1H587U4E', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'whats the big deal now\n', '2020-10-20 16:42:41', '1', '2020-10-20 16:44:27', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36||Windows 10||Chrome||::1', NULL),
(188, 'wiJBRlfVTHZsKCuF7I0PGh5XW1tq34nr', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'please, inform the rest of the people to come as well\n', '2020-10-20 16:44:12', '1', '2020-10-20 16:44:27', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36||Windows 10||Chrome||::1', NULL),
(189, '7CqTR2ZcAWLtYvuM5raeKBGSh84HkU0d', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'i hope that is kk with you\n', '2020-10-20 16:44:24', '1', '2020-10-20 16:44:27', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36||Windows 10||Chrome||::1', NULL),
(190, 'WrQvjgER9zeyL3CwKUu0XYHSGFahBcd5', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'yeah sure\n', '2020-10-20 16:44:29', '1', '2020-10-20 16:44:53', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL),
(191, 'hWK9Y3LrIepsquj2X1UgwRQZ8dacSz5C', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'all is kk with me\n', '2020-10-20 16:44:32', '1', '2020-10-20 16:44:53', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL),
(192, 'pYaO6W07AHneFtoVXsBL8d5I13yPZgiD', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'i will let them know\n', '2020-10-20 16:44:49', '1', '2020-10-20 16:44:53', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL),
(193, 'hAOTwx6cSVXF9oYqrJNyEUIGkzj34Zp1', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'great\n', '2020-10-20 16:44:55', '1', '2020-10-20 16:45:05', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36||Windows 10||Chrome||::1', NULL),
(194, 'OLo9jK6A3PWCvDmtwTHZs5MF2XEz70ry', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'thanks\n', '2020-10-20 16:44:58', '1', '2020-10-20 16:45:05', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36||Windows 10||Chrome||::1', NULL),
(195, 'DslHuqUCgQoBf8pEhZN3TYieM56dtr0a', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'you are most welcomed\n', '2020-10-20 16:45:05', '1', '2020-10-20 18:11:19', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL),
(196, 'ual3bCkLiyBoTUNh8OfmAY2wF5VEHqRW', 'LO6DKN9BDS40FBOQPPTWGMKE', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Hey priscy\n', '2020-10-20 18:09:42', '0', '2020-10-20 18:09:42', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0||Windows 10||Firefox||127.0.0.1', NULL),
(197, 'DNRBw3YtJ2ySnj65oE1LvAlGI7iO8MuX', 'LO6DKN9BDS40FBOQPPTWGMKE', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', '/**\n	 * Confirm that the user is online by checking the difference between the last_seen and the current time\n	 * If the difference is 5 minutes or less then, the user is online if not then the user is offline\n	 */\n	public function user_is_online($last_seen) {\n		// online algorithm (user is online if last activity is at most 3 minutes ago)\n        return (bool) (raw_time_diff($last_seen) ', '2020-10-20 18:10:52', '0', '2020-10-20 18:10:52', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0||Windows 10||Firefox||127.0.0.1', NULL),
(198, 'l3wPTuovAGm8iKf5CDXk7IWNcZjLgVd4', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'hey bro\n', '2020-10-20 18:45:27', '1', '2020-10-20 18:47:49', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36||Windows 10||Chrome||::1', NULL),
(199, '3DSqGX6YIcaOFB90sjWTmVyMRwLC1d5t', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'i believe we are on the same page', '2020-10-20 18:45:46', '1', '2020-10-20 18:47:49', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36||Windows 10||Chrome||::1', NULL),
(200, '75GY3jVArXSf648hKkCJFRldwcoziPQa', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'hello world\n', '2020-10-20 19:19:10', '1', '2020-10-20 19:23:50', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL),
(201, '52IznrPNjmFpYcQ6ZJkLaCEUbfuGqShO', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'how has your day been so far\n', '2020-10-20 19:19:25', '1', '2020-10-20 19:23:50', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL),
(202, 'h6l3bTg9NBRySKUIvswm1qWZfHo84JdM', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'work has been marvelous, i perceive\nyou can see what is going on at the moment\n', '2020-10-20 19:20:06', '1', '2020-10-20 19:23:50', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL),
(203, 'ExW0spgMYbdDHqeXV8lvkLQjJTS7PUi5', 'GXH0FNAWT48B9TRSWZIRVPH1', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'frankies\n', '2020-10-20 19:20:48', '1', '2020-10-20 19:23:03', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0||Windows 10||Firefox||127.0.0.1', NULL),
(204, 'BH8zapEjkgrFusAnPVO0Q3IxCKUviqZm', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'i am doing my best here\n', '2020-10-20 19:22:23', '1', '2020-10-20 19:23:50', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL),
(205, 'NzJQKlf6mFYMWwctrqa1GHoxR2SA5D3L', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '// convert the seen and sent dates into ago state\n                $result->clean_date = date(&#34;l, F jS&#34;, strtotime($result->date_created));\n                $result->sent_time = date(&#34;h:i A&#34;, strtotime($result->date_created));\n                $result->seen_time = time_diff($result->seen_date);\n                $result->sent_ago = time_diff($result->date_created);', '2020-10-20 19:24:06', '1', '2020-10-20 19:24:15', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36||Windows 10||Chrome||::1', NULL),
(206, 'pDPQ7qbufsUJXo6mL1dC5zNiHBIlrG0Y', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'alert(&#34;this is a bug&#34;);', '2020-10-20 19:24:37', '1', '2020-10-20 19:30:22', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL),
(207, 'M32tHnUAC6zqaRbQ9PoGmprxJh8KBWXI', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'security is king\n', '2020-10-20 19:31:02', '1', '2020-10-20 19:31:54', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36||Windows 10||Chrome||::1', NULL),
(208, 'TqXpPgR49umywEivoxlzrH0A8VbQO3ad', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'interesting times ahead\n', '2020-10-20 19:31:54', '1', '2020-10-20 19:48:26', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL),
(209, 'IH1hStC3UdFjAw2PNzQopYb4OeyfsrGE', 'CTITJFOLBCAYG6V0U37DLMKE', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '5U1hKwp4HZNSW80jRCXEVrIJOvmYTAyd', 'send\n', '2020-10-20 19:33:22', '0', '2020-10-20 19:33:22', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL),
(210, '3fc9EUazm2joVsITLnvSGqe0NDu6WhwZ', 'GXH0FNAWT48B9TRSWZIRVPH1', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'hello\n', '2020-10-20 19:35:53', '1', '2020-10-20 19:51:18', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0||Windows 10||Firefox||127.0.0.1', NULL),
(211, '4QEXRKp2TU81B5YfPWx7oLJwOvi0jqyn', 'GXH0FNAWT48B9TRSWZIRVPH1', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'hii\n', '2020-10-20 19:36:46', '1', '2020-10-20 19:51:18', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0||Windows 10||Firefox||127.0.0.1', NULL),
(212, 'tgqZxQ78wsMORpSknJuUbVl3eNoIGFPi', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'this should work. i sence this is what i want great work\n', '2020-10-20 19:51:05', '1', '2020-10-20 19:51:10', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL),
(213, '0vC9aPlXf17FbQMTkJWwqetSdYjc4KLB', 'GXH0FNAWT48B9TRSWZIRVPH1', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'yeap\n', '2020-10-20 19:51:24', '1', '2020-10-20 19:51:47', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL),
(214, 'UvW1auBAI34cPSfjD7J8yCX2pHmhxTde', 'GXH0FNAWT48B9TRSWZIRVPH1', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'whats it please\n', '2020-10-20 19:51:42', '1', '2020-10-20 19:51:47', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL),
(215, 'WbOlePfoF16HA7hrvEqZIwMidBn4Tx2k', 'GXH0FNAWT48B9TRSWZIRVPH1', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'do i even know what it i\n', '2020-10-20 19:51:54', '1', '2020-10-20 19:52:17', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0||Windows 10||Firefox||127.0.0.1', NULL),
(216, '05ZJrwNtjQuKeW3DF8Cc2liI7O9P6qzh', 'GXH0FNAWT48B9TRSWZIRVPH1', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'do i know what it is\n', '2020-10-20 19:52:00', '1', '2020-10-20 19:52:17', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0||Windows 10||Firefox||127.0.0.1', NULL),
(217, 'Ajk0fGMuvZ6W7cnKF23r4RzH81s9NdOt', 'GXH0FNAWT48B9TRSWZIRVPH1', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'alirght', '2020-10-20 19:52:17', '1', '2020-10-20 19:57:44', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL),
(218, 'ocslWyzr5xGVb2ATSL3hM9wRf1K7BkJD', 'GXH0FNAWT48B9TRSWZIRVPH1', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'hey\n', '2020-10-20 19:52:47', '1', '2020-10-20 19:57:44', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL),
(219, 'MK8woxjuSEYb76RFk2qcJBXyLdDtvAhm', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'hey boy\n', '2020-10-20 19:55:51', '1', '2020-10-20 19:56:12', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL),
(220, 'xWdvtH0GgcsInNJk6Ari4jEB1lYXwu97', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'yeap\n', '2020-10-20 19:56:15', '1', '2020-10-20 19:57:03', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36||Windows 10||Chrome||::1', NULL),
(221, 'wpZdrmSQ3V6JlbK9kAtgqHy8iEjsP72R', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'hello\n', '2020-10-20 19:57:07', '1', '2020-10-20 19:58:28', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL),
(222, 'PSFL7GitxlaJwE5Ns2VeA6ugMBqTXOCY', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'hiii\n', '2020-10-20 19:58:55', '1', '2020-10-20 20:01:21', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36||Windows 10||Chrome||::1', NULL),
(223, 'rHFIuTZ3KGYXf4Q6P5LDe8nWRdzNwJko', '6DUJ2AKNIKTLBNXPY8SZEP9Q', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', 'yeah\n', '2020-10-20 20:01:23', '1', '2020-10-22 09:02:56', '0', '0', '5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36 Edg/85.0.564.68||Windows 10||Chrome||::1', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_complaints`
--

CREATE TABLE `users_complaints` (
  `id` int(11) UNSIGNED NOT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `user_id` varchar(32) DEFAULT NULL,
  `related_to` varchar(64) DEFAULT NULL,
  `related_to_id` varchar(32) DEFAULT NULL,
  `related_to_details` text DEFAULT NULL COMMENT 'Save some back information about the item for referencing',
  `complaint_id` varchar(32) DEFAULT NULL,
  `company_id` varchar(32) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `complaint_type` enum('complaint','reply') NOT NULL DEFAULT 'complaint',
  `user_type` enum('user','support') DEFAULT 'user',
  `date_created` datetime DEFAULT current_timestamp(),
  `last_updated` datetime DEFAULT current_timestamp(),
  `updated_by` varchar(32) DEFAULT NULL,
  `assigned_to` varchar(32) DEFAULT NULL,
  `seen` enum('0','1') DEFAULT '0',
  `seen_by` varchar(32) DEFAULT NULL,
  `seen_date` datetime DEFAULT NULL,
  `recipient` enum('user','admin','insurance_company','nic','bank','reinsurance') NOT NULL,
  `follow` enum('0','1') NOT NULL DEFAULT '1',
  `notify` enum('0','1') NOT NULL DEFAULT '1',
  `rating` varchar(2) DEFAULT NULL,
  `status` enum('Answered','Pending','Closed','Waiting','Reopen','Solved','Processing') NOT NULL DEFAULT 'Pending',
  `replies_count` int(12) UNSIGNED DEFAULT 0,
  `comments_count` int(12) UNSIGNED DEFAULT 0,
  `submit_status` enum('draft','save') DEFAULT 'save',
  `date_submitted` datetime DEFAULT NULL,
  `deleted` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users_complaints`
--

INSERT INTO `users_complaints` (`id`, `item_id`, `user_id`, `related_to`, `related_to_id`, `related_to_details`, `complaint_id`, `company_id`, `subject`, `message`, `complaint_type`, `user_type`, `date_created`, `last_updated`, `updated_by`, `assigned_to`, `seen`, `seen_by`, `seen_date`, `recipient`, `follow`, `notify`, `rating`, `status`, `replies_count`, `comments_count`, `submit_status`, `date_submitted`, `deleted`) VALUES
(1, '7dhIrzukYby1cegqZO6BHEJK934vxCji', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'general-service-delivery', 'null', NULL, NULL, 'ksus7opxcw3ayeqfdrtuv4jrmm61kfoi', 'First degugged complaint', '&lt;div&gt;&lt;!--block--&gt;There is no data here. I am good to go.&lt;/div&gt;', 'complaint', 'user', '2020-10-01 20:31:13', '2020-10-01 20:31:13', NULL, NULL, '0', NULL, NULL, 'admin', '1', '1', NULL, 'Pending', 0, 0, 'draft', NULL, '0'),
(4, 'P2YA0NJSDhEn36yIKUHRz4wsZbfCjeXl', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'company-insurance-policies', 'afdafd4454', '{\"item_id\":\"afdafd4454\",\"item_name\":\"Fidelity - Cash in Transit\",\"policy_name\":\"Fidelity - Cash in Transit\",\"company_id\":\"ksus7opxcw3ayeqfdrtuv4jrmm61kfoi\",\"policy_code\":\"CTI\",\"year_enrolled\":\"October 2019\",\"company_name\":\"Enterprise Insurance\",\"date_created\":\"2020-09-16 17:00:50\"}', NULL, 'ksus7opxcw3ayeqfdrtuv4jrmm61kfoi', 'Insurance Company Item List', '&lt;div&gt;&lt;!--block--&gt;Final test of the company insurance policy information log&lt;/div&gt;', 'complaint', 'user', '2020-10-01 20:41:48', '2020-10-03 13:37:27', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', NULL, '1', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-01 21:09:45', 'insurance_company', '1', '1', NULL, 'Solved', 0, 0, 'save', NULL, '0'),
(5, 'De5CE9RxJhUytzVPX1THdNq6faQg4AvK', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'previous-complaint', 'P2YA0NJSDhEn36yIKUHRz4wsZbfCjeXl', '{\"complaint_id\":\"P2YA0NJSDhEn36yIKUHRz4wsZbfCjeXl\",\"user_id\":\"F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ\",\"subject\":\"Insurance Company Item List\",\"related_to_details\":\"{\"item_id\":\"afdafd4454\",\"item_name\":\"Fidelity - Cash in Transit\",\"policy_name\":\"Fidelity - Cash in Transit\",\"company_id\":\"ksus7opxcw3ayeqfdrtuv4jrmm61kfoi\",\"policy_code\":\"CTI\",\"year_enrolled\":\"October 2019\",\"company_name\":\"Enterprise Insurance\",\"date_created\":\"2020-09-16 17:00:50\"}\",\"related_to\":\"company-insurance-policies\",\"date_created\":\"2020-10-01 20:41:48\",\"submit_status\":\"save\",\"status\":\"Pending\",\"related_to_name\":null}', NULL, 'ksus7opxcw3ayeqfdrtuv4jrmm61kfoi', 'Previous complaint test', '&lt;div&gt;&lt;!--block--&gt;This is related to a previous complaint made on the system.&lt;/div&gt;', 'complaint', 'user', '2020-10-01 20:48:58', '2020-10-01 20:48:58', NULL, NULL, '0', NULL, NULL, 'insurance_company', '1', '1', NULL, 'Pending', 0, 0, 'draft', NULL, '0'),
(6, 'qiV14ErtnzclWF9jYA8BNkuJp7Kho2Pf', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'system-bug', NULL, '{\"complaint_id\":\"De5CE9RxJhUytzVPX1THdNq6faQg4AvK\",\"user_id\":\"F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ\",\"subject\":\"Previous complaint test\",\"related_to_details\":\"{\"complaint_id\":\"P2YA0NJSDhEn36yIKUHRz4wsZbfCjeXl\",\"user_id\":\"F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ\",\"subject\":\"Insurance Company Item List\",\"related_to_details\":\"{\"item_id\":\"afdafd4454\",\"item_name\":\"Fidelity - Cash in Transit\",\"policy_name\":\"Fidelity - Cash in Transit\",\"company_id\":\"ksus7opxcw3ayeqfdrtuv4jrmm61kfoi\",\"policy_code\":\"CTI\",\"year_enrolled\":\"October 2019\",\"company_name\":\"Enterprise Insurance\",\"date_created\":\"2020-09-16 17:00:50\"}\",\"related_to\":\"company-insurance-policies\",\"date_created\":\"2020-10-01 20:41:48\",\"submit_status\":\"save\",\"status\":\"Pending\",\"related_to_name\":null}\",\"related_to\":\"previous-complaint\",\"date_created\":\"2020-10-01 20:48:58\",\"submit_status\":\"draft\",\"status\":\"Pending\",\"related_to_name\":null}', NULL, 'ksus7opxcw3ayeqfdrtuv4jrmm61kfoi', 'System bug data', '&lt;div&gt;&lt;!--block--&gt;This is a system bug and i need this needs to be seen by an admin&lt;/div&gt;', 'complaint', 'user', '2020-10-01 20:50:33', '2020-10-01 20:50:33', NULL, NULL, '1', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-11-10 10:22:19', 'admin', '1', '1', NULL, 'Pending', 0, 0, 'save', NULL, '0'),
(8, 'Z69JmVgQ2CFAEY3HsbfSaIMthcljL5Oo', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'client-policy', '9W3oQkX04NyFU8LMEVbtKISe5TChPD1f', '{\"item_id\":\"9W3oQkX04NyFU8LMEVbtKISe5TChPD1f\",\"policy_name\":\"Fidelity - Cash in Transit\",\"policy_id\":\"CTI000012020\",\"policy_type\":\"afdafd4454\",\"policy_type_details\":\"<div><!--block-->This is the policy description</div>\",\"policy_start_date\":\"2020-10-10\",\"premium\":\"0.00\",\"first_premium_due_date\":null,\"last_premium_payment\":null,\"next_repayment_date\":null,\"requirements\":null,\"payment_plan\":null,\"date_created\":\"2020-09-30 14:59:52\",\"policy_holder_id\":\"F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ\",\"policy_holder\":\"Priscilla Appiah\"}', NULL, 'ksus7opxcw3ayeqfdrtuv4jrmm61kfoi', 'Bug on my Policy', '&lt;div&gt;&lt;!--block--&gt;There is a bug on this policy&lt;/div&gt;', 'complaint', 'user', '2020-10-01 20:56:04', '2020-10-03 13:37:42', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '1', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-01 20:59:17', 'insurance_company', '1', '1', NULL, 'Solved', 0, 0, 'save', NULL, '0'),
(10, 'miZcHfKEWdINSu1jVPl4G8RJMpn3Yr5L', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'claims', 'sBAhYUIbjLc30PkpGgdVqtzrZEm8vH7N', '{\"item_id\":\"sBAhYUIbjLc30PkpGgdVqtzrZEm8vH7N\",\"company_id\":\"ksus7opxcw3ayeqfdrtuv4jrmm61kfoi\",\"user_id\":\"F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ\",\"policy_id\":\"CTI000012020\",\"amount_claimed\":\"23900.00\",\"date_created\":\"2020-09-30 21:57:45\",\"policy_type\":\"afdafd4454\",\"assigned_to\":\"sgHvi29tuJakdfzmp71nowNlWr40BKDV\",\"requested_by\":\"Priscilla Appiah\",\"seen_by_name\":null,\"company_name\":\"Enterprise Insurance\",\"assigned_to_name\":\"Solomon Kwarteng\",\"policy_name\":\"Fidelity - Cash in Transit\",\"claim_status\":\"Approved\",\"policy_enrolled_date\":\"2020-10-10\"}', NULL, 'ksus7opxcw3ayeqfdrtuv4jrmm61kfoi', 'Claims final test', '&lt;div&gt;&lt;!--block--&gt;This is a test on my claim&lt;/div&gt;', 'complaint', 'user', '2020-10-01 21:08:43', '2020-10-06 19:11:47', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '1', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-01 21:10:32', 'insurance_company', '1', '1', NULL, 'Answered', 2, 0, 'save', NULL, '0');

-- --------------------------------------------------------

--
-- Table structure for table `users_emails`
--

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

--
-- Dumping data for table `users_feedback`
--

INSERT INTO `users_feedback` (`id`, `item_id`, `resource`, `resource_id`, `feedback_type`, `user_id`, `user_type`, `subject`, `message`, `mentions`, `date_created`, `likes_count`, `comments_count`, `user_agent`, `deleted`) VALUES
(1, '8CMby5k69V4fSjwvI0ZedQO1PrDo3HNh', 'assignments', 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl', 'comment', 'uIkajsw123456789064hxk1fc3efmnva', 'admin', NULL, '&lt;div&gt;&lt;!--block--&gt;This is a test assignment comment&lt;/div&gt;', NULL, '2020-12-22 16:22:12', '0', '0', 'Windows 10 | Chrome | ::1', '0'),
(2, '6qHtg7a2PiSGlpOrBuzwRfc1ms9Dx5No', 'assignments', 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl', 'comment', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', '', NULL, '&lt;div&gt;&lt;!--block--&gt;This is also my comment that i am sharing on this assignment. Please ensure the right thing is done in all ways and aspects&lt;/div&gt;', NULL, '2020-12-22 17:24:00', '0', '0', 'Windows 10 | Chrome | ::1', '0'),
(3, 'IenC6ustgQEmR3pBizAywZoOXKvjHdq1', 'assignments', 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl', 'comment', 'uIkajsw123456789064hxk1fc3efmnva', 'admin', NULL, '&lt;div&gt;&lt;!--block--&gt;Alright Grace i will do as you have requested. Also, note that we are far behind schedule so we need to catch up as soon as possible on the subject as said earliar on. Thanks for partaking in this conversation.&lt;/div&gt;', NULL, '2020-12-22 17:25:28', '0', '0', 'Windows 10 | Chrome | ::1', '0'),
(4, 'pKGjAnU5CL38qlu6wRDe7FozPkW49E20', 'assignments', 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl', 'comment', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', '', NULL, '&lt;div&gt;&lt;!--block--&gt;Thats great. we are on track to success. i am confident that we will get there as soon as possible&lt;/div&gt;', NULL, '2020-12-22 17:26:05', '0', '0', 'Windows 10 | Chrome | ::1', '0'),
(5, 'iSTGqxvYsHhE43eky1V7ljonPWdLXf25', 'assignments', 'fWosa024FkTZ9cOH6zevXtPjx8JgbRhl', 'comment', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '', NULL, '&lt;div&gt;&lt;!--block--&gt;I am pleased to know each on of you is following the updates on this assignment.&lt;/div&gt;', NULL, '2020-12-22 17:30:24', '0', '0', 'Windows 10 | Chrome | ::1', '0'),
(6, 'eLcf7FONMj8sytVnmUKIGpaYWSRrlAv1', 'assignments', 'yGBuEJwioD2PZKanN7rzLpI6C3SR9TQF', 'comment', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', '', NULL, '&lt;div&gt;&lt;!--block--&gt;I am sharing a comment on this assignment for the teacher to see and make some recommendations.&lt;/div&gt;', NULL, '2020-12-28 13:03:15', '0', '0', 'Windows 10 | Chrome | ::1', '0'),
(7, 'iLV2ASDRrUnKQa86H1kWwXtxoGes7MFZ', 'events', 'cLnFJiO1RyGUEH6I3Ngr8qw54WxVXPjm', 'comment', 'uIkajsw123456789064hxk1fc3efmnva', 'admin', NULL, '&lt;div&gt;&lt;!--block--&gt;Upload of comments for this event is here and there&lt;/div&gt;', NULL, '2021-01-01 23:54:30', '0', '0', 'Windows 10 | Chrome | ::1', '0'),
(8, 'M7sNI9ZbX8viWypJGH3ElS4nOcQRxgtf', 'events', 'cLnFJiO1RyGUEH6I3Ngr8qw54WxVXPjm', 'comment', 'uIkajsw123456789064hxk1fc3efmnva', 'admin', NULL, '&lt;div&gt;&lt;!--block--&gt;1.&nbsp; Building of Web Applications that takes into account multi-user levels and permissions. Employing the best of various use cases to control the access users of user accounts.&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;2. Create website layout/user interface by using standard HTML5 / CSS practices&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;3. Gather and refine specifications and requirements based on technical needs&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;4. Create and maintain software documentation.&lt;/div&gt;', NULL, '2021-01-02 11:32:42', '0', '0', 'Windows 10 | Chrome | ::1', '0'),
(9, 'ynpPqBCu3WoKh2crE1fHvlN8iYaD5O9V', 'ebook', 'afdghhghghg', 'comment', 'uIkajsw123456789064hxk1fc3efmnva', 'admin', NULL, '&lt;div&gt;&lt;!--block--&gt;This is a first comment on this book&lt;/div&gt;', NULL, '2021-01-03 21:34:28', '0', '0', 'Windows 10 | Chrome | ::1', '0'),
(10, 'Vvmgnj7bdzDRYxryitSJ48fNoELTGQcq', 'ebook', 'afdghhghghg', 'comment', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '', NULL, '&lt;div&gt;&lt;!--block--&gt;This is my next comment that i am also posting on this book&lt;/div&gt;', NULL, '2021-01-03 21:35:14', '0', '0', 'Windows 10 | Chrome | ::1', '0'),
(11, 'J56IsbNphlT0kKMxWEYH2dBaZSfV4c8y', 'ebook', 'afdghhghghg', 'comment', 'uIkajsw123456789064hxk1fc3efmnva', 'admin', NULL, '&lt;div&gt;&lt;!--block--&gt;Interestingly its gonna be a nice time reading the comments here&lt;/div&gt;', NULL, '2021-01-03 21:35:37', '0', '0', 'Windows 10 | Chrome | ::1', '0'),
(12, 'QeDbfIu4pjRr1UWGKT7cq06Oiv9JPdMB', 'books_request', 'jJnVM2SimOsZDFla4UAkfHg0cyRwuQ9x', 'comment', 'uIkajsw123456789064hxk1fc3efmnva', 'admin', NULL, '&lt;div&gt;&lt;!--block--&gt;Reduce the length of homepage sliders to that of the banner images on the other pages. Reduce the length of homepage sliders to that of the banner images on the other pages. Reduce the length of homepage sliders to that of the banner images on the other pages.&nbsp;&lt;/div&gt;', NULL, '2021-01-07 16:03:56', '0', '0', 'Windows 10 | Chrome | ::1', '0');

-- --------------------------------------------------------

--
-- Table structure for table `users_gender`
--

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
-- Table structure for table `users_guardian`
--

CREATE TABLE `users_guardian` (
  `id` int(11) NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `user_id` varchar(32) DEFAULT NULL,
  `image` varchar(255) NOT NULL DEFAULT 'assets/img/user.png',
  `fullname` varchar(255) DEFAULT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `contact` varchar(255) DEFAULT NULL,
  `contact_2` varchar(32) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `blood_group` varchar(255) DEFAULT NULL,
  `employer` varchar(255) DEFAULT NULL,
  `relationship` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `residence` varchar(255) DEFAULT NULL,
  `country` varchar(32) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users_guardian`
--

INSERT INTO `users_guardian` (`id`, `client_id`, `user_id`, `image`, `fullname`, `gender`, `contact`, `contact_2`, `date_of_birth`, `email`, `occupation`, `blood_group`, `employer`, `relationship`, `address`, `residence`, `country`, `description`, `status`, `date_created`, `date_updated`) VALUES
(1, 'LKJAFD94R', '259786528933', 'assets/img/user.png', 'The name', 'Male', '0550107770', '0240553604', '1993-05-11', 'theguardianmail@mail.com', 'The occupation information', NULL, 'The Employer Details', 'Parent', 'The Postal Address', 'Accra', 'null', 'This is the description of the user', '1', '2020-12-17 10:03:48', '2020-12-17 10:34:57'),
(2, 'LKJAFD94R', '30462664355', 'assets/img/user.png', 'Simon Kweinoo', '', '004984849948', NULL, NULL, 'newemail@mailer.com', NULL, NULL, NULL, 'Parent', 'this is the email address that i am adding', NULL, 'null', NULL, '1', '2020-12-17 10:03:48', '2020-12-17 23:19:03'),
(3, 'LKJAFD94R', '54693872', 'assets/img/user.png', 'William Darko', '', '02090393992', NULL, NULL, 'williedarko22@gmail.com', 'that is the occupation', 'B+', 'my employer name is here', 'Parent', 'This is the address of the dad', 'this is the residence', '3', 'this is the description', '1', '2020-12-17 22:47:52', '2020-12-29 22:00:44'),
(4, 'LKJAFD94R', '873469387243', 'assets/img/user.png', 'Philip Asamoah', NULL, '02090393992', NULL, NULL, 'williedarko22@gmail.com', NULL, NULL, NULL, 'Parent', 'This is the address of the dad', NULL, NULL, NULL, '1', '2020-12-17 22:48:33', '2020-12-17 22:48:33'),
(5, 'LKJAFD94R', '48356217', 'assets/img/user.png', 'The parent Obeng', NULL, '009300399309', NULL, NULL, 'theemailaddress@mail.com', NULL, NULL, NULL, 'Uncle', 'This is the address of the guardian information that i am inserting now', NULL, NULL, NULL, '1', '2020-12-17 22:59:40', '2020-12-17 22:59:40');

-- --------------------------------------------------------

--
-- Table structure for table `users_login_history`
--

CREATE TABLE `users_login_history` (
  `id` int(11) UNSIGNED NOT NULL,
  `company_id` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_id` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastlogin` datetime DEFAULT current_timestamp(),
  `log_ipaddress` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `log_browser` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `log_platform` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_login_history`
--

INSERT INTO `users_login_history` (`id`, `company_id`, `client_id`, `username`, `lastlogin`, `log_ipaddress`, `log_browser`, `user_id`, `log_platform`) VALUES
(1, '', 'LKJAFD94R', 'test_admin', '2020-11-16 13:21:46', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.12'),
(2, NULL, 'LKJAFD94R', 'test_admin', '2020-11-16 13:22:33', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.12'),
(3, NULL, 'LKJAFD94R', 'test_admin', '2020-11-16 20:26:16', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.19'),
(4, NULL, 'LKJAFD94R', 'test_admin', '2020-11-26 22:10:22', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66'),
(5, NULL, 'LKJAFD94R', 'test_admin', '2020-11-27 07:20:46', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66'),
(6, NULL, 'LKJAFD94R', 'test_admin', '2020-11-27 12:25:41', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66'),
(7, NULL, 'LKJAFD94R', 'test_admin', '2020-11-27 17:58:29', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66'),
(8, NULL, 'LKJAFD94R', 'test_admin', '2020-11-28 09:26:40', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66'),
(9, NULL, 'LKJAFD94R', 'test_admin', '2020-11-28 21:04:03', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66'),
(10, NULL, 'LKJAFD94R', 'test_admin', '2020-11-29 10:10:07', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66'),
(11, NULL, 'LKJAFD94R', 'test_admin', '2020-11-29 11:40:12', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66'),
(12, NULL, 'LKJAFD94R', 'test_admin', '2020-11-29 14:01:13', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66'),
(13, NULL, 'LKJAFD94R', 'test_admin', '2020-11-29 16:34:20', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66'),
(14, NULL, 'LKJAFD94R', 'test_admin', '2020-11-29 20:56:29', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66'),
(15, NULL, 'LKJAFD94R', 'test_admin', '2020-11-30 04:26:10', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66'),
(16, NULL, 'LKJAFD94R', 'test_admin', '2020-11-30 09:31:39', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66'),
(17, NULL, 'LKJAFD94R', 'test_admin', '2020-11-30 11:11:46', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66'),
(18, NULL, 'LKJAFD94R', 'test_admin', '2020-11-30 15:08:01', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66'),
(19, NULL, 'LKJAFD94R', 'test_admin', '2020-11-30 22:00:38', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66'),
(20, NULL, 'LKJAFD94R', 'test_admin', '2020-12-01 08:14:31', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66'),
(21, NULL, 'LKJAFD94R', 'test_admin', '2020-12-01 08:20:27', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66'),
(22, NULL, 'LKJAFD94R', 'test_admin', '2020-12-02 01:06:37', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66'),
(23, NULL, 'LKJAFD94R', 'test_admin', '2020-12-02 21:56:33', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66'),
(24, NULL, 'LKJAFD94R', 'test_admin', '2020-12-10 16:39:57', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66'),
(25, NULL, 'LKJAFD94R', 'test_admin', '2020-12-10 16:57:50', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66'),
(26, NULL, 'LKJAFD94R', 'test_admin', '2020-12-10 22:13:42', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66'),
(27, NULL, 'LKJAFD94R', 'test_admin', '2020-12-11 13:04:16', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66'),
(28, NULL, 'LKJAFD94R', 'test_admin', '2020-12-11 17:26:14', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66'),
(29, NULL, 'LKJAFD94R', 'test_admin', '2020-12-11 21:42:48', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66'),
(30, NULL, 'LKJAFD94R', 'test_admin', '2020-12-12 08:27:45', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66'),
(31, NULL, 'LKJAFD94R', 'test_admin', '2020-12-12 18:42:53', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66'),
(32, NULL, 'LKJAFD94R', 'test_admin', '2020-12-15 19:56:28', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66'),
(33, NULL, 'LKJAFD94R', 'test_admin', '2020-12-15 22:25:44', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66'),
(34, NULL, 'LKJAFD94R', 'test_admin', '2020-12-16 05:58:10', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66'),
(35, NULL, 'LKJAFD94R', 'test_admin', '2020-12-16 16:04:14', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66'),
(36, NULL, 'LKJAFD94R', 'test_admin', '2020-12-17 08:05:04', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(37, NULL, 'LKJAFD94R', 'test_admin', '2020-12-17 13:22:41', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(38, NULL, 'LKJAFD94R', 'test_admin', '2020-12-17 22:36:35', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(39, NULL, 'LKJAFD94R', 'test_admin', '2020-12-18 06:51:17', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(40, NULL, 'LKJAFD94R', 'test_admin', '2020-12-18 12:00:04', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(41, NULL, 'LKJAFD94R', 'test_admin', '2020-12-18 14:47:10', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(42, NULL, 'LKJAFD94R', 'test_admin', '2020-12-18 18:47:47', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(43, NULL, 'LKJAFD94R', 'test_admin', '2020-12-19 07:18:59', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(44, NULL, 'LKJAFD94R', 'test_admin', '2020-12-19 16:16:43', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(45, NULL, 'LKJAFD94R', 'test_admin', '2020-12-19 16:19:57', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(46, NULL, 'LKJAFD94R', 'test_admin', '2020-12-19 16:29:03', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(47, NULL, 'LKJAFD94R', 'test_admin', '2020-12-19 16:30:47', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(48, NULL, 'LKJAFD94R', 'test_admin', '2020-12-21 06:43:57', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(49, NULL, 'LKJAFD94R', 'test_admin', '2020-12-21 11:19:22', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(50, NULL, 'LKJAFD94R', 'test_student', '2020-12-21 16:37:08', '::1', 'Chrome|Windows 10', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(51, NULL, 'LKJAFD94R', 'test_student', '2020-12-21 16:45:16', '::1', 'Chrome|Windows 10', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(52, NULL, 'LKJAFD94R', 'test_student', '2020-12-21 19:59:07', '::1', 'Chrome|Windows 10', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(53, NULL, 'LKJAFD94R', 'test_admin', '2020-12-21 20:06:47', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(54, NULL, 'LKJAFD94R', 'test_student', '2020-12-22 05:33:23', '::1', 'Chrome|Windows 10', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(55, NULL, 'LKJAFD94R', 'test_admin', '2020-12-22 05:46:42', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(56, NULL, 'LKJAFD94R', 'test_admin', '2020-12-22 08:30:25', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(57, NULL, 'LKJAFD94R', 'test_admin', '2020-12-22 14:37:17', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(58, NULL, 'LKJAFD94R', 'test_student', '2020-12-22 14:37:28', '::1', 'Chrome|Windows 10', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(59, NULL, 'LKJAFD94R', 'test_teacher', '2020-12-22 17:26:15', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(60, NULL, 'LKJAFD94R', 'test_admin', '2020-12-23 07:07:20', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(61, NULL, 'LKJAFD94R', 'test_teacher', '2020-12-23 07:07:27', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(62, NULL, 'LKJAFD94R', 'test_student', '2020-12-23 07:20:45', '::1', 'Chrome|Windows 10', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(63, NULL, 'LKJAFD94R', 'test_teacher', '2020-12-23 12:34:38', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(64, NULL, 'LKJAFD94R', 'test_student', '2020-12-23 12:34:39', '::1', 'Chrome|Windows 10', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(65, NULL, 'LKJAFD94R', 'test_student', '2020-12-23 15:00:13', '::1', 'Chrome|Windows 10', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(66, NULL, 'LKJAFD94R', 'test_student', '2020-12-23 16:29:31', '::1', 'Chrome|Windows 10', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(67, NULL, 'LKJAFD94R', 'test_teacher', '2020-12-23 20:29:53', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(68, NULL, 'LKJAFD94R', 'test_student', '2020-12-23 20:30:07', '::1', 'Chrome|Windows 10', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(69, NULL, 'LKJAFD94R', 'test_student', '2020-12-23 22:06:29', '::1', 'Chrome|Windows 10', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(70, NULL, 'LKJAFD94R', 'test_teacher', '2020-12-24 13:06:59', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(71, NULL, 'LKJAFD94R', 'test_student', '2020-12-24 13:07:06', '::1', 'Chrome|Windows 10', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(72, NULL, 'LKJAFD94R', 'test_teacher', '2020-12-24 22:06:27', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(73, NULL, 'LKJAFD94R', 'test_student', '2020-12-24 22:06:34', '::1', 'Chrome|Windows 10', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(74, NULL, 'LKJAFD94R', 'test_teacher', '2020-12-25 19:27:25', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(75, NULL, 'LKJAFD94R', 'test_teacher', '2020-12-25 19:29:17', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(76, NULL, 'LKJAFD94R', 'test_student', '2020-12-25 19:32:02', '::1', 'Chrome|Windows 10', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(77, NULL, 'LKJAFD94R', 'test_student', '2020-12-25 22:56:51', '::1', 'Chrome|Windows 10', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(78, NULL, 'LKJAFD94R', 'test_teacher', '2020-12-25 23:38:37', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(79, NULL, 'LKJAFD94R', 'test_student', '2020-12-26 07:25:05', '::1', 'Chrome|Windows 10', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(80, NULL, 'LKJAFD94R', 'test_teacher', '2020-12-26 07:25:23', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(81, NULL, 'LKJAFD94R', 'test_student', '2020-12-27 22:55:45', '::1', 'Chrome|Windows 10', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(82, NULL, 'LKJAFD94R', 'test_teacher', '2020-12-27 22:56:00', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(83, NULL, 'LKJAFD94R', 'test_student', '2020-12-28 11:32:05', '::1', 'Chrome|Windows 10', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(84, NULL, 'LKJAFD94R', 'test_teacher', '2020-12-28 12:34:52', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(85, NULL, 'LKJAFD94R', 'test_teacher', '2020-12-28 15:05:59', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(86, NULL, 'LKJAFD94R', 'test_student', '2020-12-28 15:35:00', '::1', 'Chrome|Windows 10', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(87, NULL, 'LKJAFD94R', 'test_teacher', '2020-12-28 17:32:37', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(88, NULL, 'LKJAFD94R', 'test_admin', '2020-12-28 17:32:55', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(89, NULL, 'LKJAFD94R', 'test_teacher', '2020-12-28 20:25:08', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(90, NULL, 'LKJAFD94R', 'test_admin', '2020-12-28 20:25:11', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(91, NULL, 'LKJAFD94R', 'test_teacher', '2020-12-28 23:07:05', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(92, NULL, 'LKJAFD94R', 'test_admin', '2020-12-29 08:27:47', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(93, NULL, 'LKJAFD94R', 'test_admin', '2020-12-29 18:12:08', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(94, NULL, 'LKJAFD94R', 'test_teacher', '2020-12-30 09:01:12', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(95, NULL, 'LKJAFD94R', 'test_admin', '2020-12-30 09:01:18', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(96, NULL, 'LKJAFD94R', 'test_admin', '2020-12-30 19:47:33', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(97, NULL, 'LKJAFD94R', 'test_admin', '2021-01-01 15:20:46', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(98, NULL, 'LKJAFD94R', 'test_teacher', '2021-01-01 16:37:17', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(99, NULL, 'LKJAFD94R', 'test_admin', '2021-01-01 20:45:54', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(100, NULL, 'LKJAFD94R', 'test_teacher', '2021-01-01 21:07:42', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(101, NULL, 'LKJAFD94R', 'test_teacher', '2021-01-02 00:22:58', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(102, NULL, 'LKJAFD94R', 'test_teacher', '2021-01-02 07:22:01', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(103, NULL, 'LKJAFD94R', 'test_admin', '2021-01-02 07:22:16', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(104, NULL, 'LKJAFD94R', 'test_teacher', '2021-01-02 11:18:08', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(105, NULL, 'LKJAFD94R', 'test_admin', '2021-01-02 11:26:40', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(106, NULL, 'LKJAFD94R', 'test_admin', '2021-01-02 15:32:21', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(107, NULL, 'LKJAFD94R', 'test_teacher', '2021-01-02 15:37:38', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(108, NULL, 'LKJAFD94R', 'test_admin', '2021-01-02 18:31:41', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(109, NULL, 'LKJAFD94R', 'test_admin', '2021-01-02 21:18:53', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(110, NULL, 'LKJAFD94R', 'test_admin', '2021-01-02 21:29:08', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(111, NULL, 'LKJAFD94R', 'test_teacher', '2021-01-02 21:53:00', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(112, NULL, 'LKJAFD94R', 'test_admin', '2021-01-02 21:53:49', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(113, NULL, 'LKJAFD94R', 'test_teacher', '2021-01-02 21:53:50', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(114, NULL, 'LKJAFD94R', 'test_admin', '2021-01-02 22:04:19', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(115, NULL, 'LKJAFD94R', 'test_admin', '2021-01-03 21:03:05', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(116, NULL, 'LKJAFD94R', 'test_teacher', '2021-01-03 21:34:40', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(117, NULL, 'LKJAFD94R', 'test_teacher', '2021-01-03 23:16:07', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(118, NULL, 'LKJAFD94R', 'test_admin', '2021-01-04 08:09:50', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(119, NULL, 'LKJAFD94R', 'test_teacher', '2021-01-04 09:42:55', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(120, NULL, 'LKJAFD94R', 'test_admin', '2021-01-04 18:35:03', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(121, NULL, 'LKJAFD94R', 'test_teacher', '2021-01-04 18:54:53', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(122, NULL, 'LKJAFD94R', 'test_admin', '2021-01-04 22:46:25', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(123, NULL, 'LKJAFD94R', 'test_admin', '2021-01-05 14:07:22', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(124, NULL, 'LKJAFD94R', 'test_admin', '2021-01-05 18:31:52', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(125, NULL, 'LKJAFD94R', 'test_teacher', '2021-01-05 18:33:34', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(126, NULL, 'LKJAFD94R', 'test_teacher', '2021-01-05 22:22:46', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(127, NULL, 'LKJAFD94R', 'test_admin', '2021-01-05 22:29:19', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(128, NULL, 'LKJAFD94R', 'test_admin', '2021-01-06 08:01:06', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(129, NULL, 'LKJAFD94R', 'test_teacher', '2021-01-06 08:21:23', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(130, NULL, 'LKJAFD94R', 'test_teacher', '2021-01-07 10:01:13', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(131, NULL, 'LKJAFD94R', 'test_admin', '2021-01-07 10:01:21', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(132, NULL, 'LKJAFD94R', 'test_teacher', '2021-01-07 12:42:28', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(133, NULL, 'LKJAFD94R', 'test_admin', '2021-01-07 14:48:24', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88'),
(134, NULL, 'LKJAFD94R', 'test_teacher', '2021-01-07 14:48:29', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88');

-- --------------------------------------------------------

--
-- Table structure for table `users_messaging_list`
--

CREATE TABLE `users_messaging_list` (
  `id` int(11) UNSIGNED NOT NULL,
  `item_id` varchar(32) DEFAULT NULL,
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

INSERT INTO `users_messaging_list` (`id`, `item_id`, `template_type`, `users_id`, `recipients_list`, `date_requested`, `schedule_type`, `schedule_date`, `message_medium`, `sent_status`, `subject`, `message`, `created_by`, `deleted`, `date_sent`) VALUES
(1, 'tyFQGmOPDR4LH0WC2M6VnAbUEdoTNxBr', 'account-verify', 'SZM14dtqDkbfn5cBl0ARgPCj287hym36', '{\"recipients_list\":[{\"fullname\":\"Emmanuella Darko\",\"email\":\"jauntygirl@gmail.com\",\"customer_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\"}]}', '2020-12-17 22:48:33', 'send_now', '2020-12-17 22:48:33', 'email', '0', '[MySchoolGH Management System] Account Verification', 'Hello Emmanuella,<a class=\"alert alert-success\" href=\"http://localhost/myschool_gh/verify?account&token=cuOes44nI6yE6QLU9jVPXX0SwAwp1hfljMqd3gaYpin2rbTxcsqJkAdS3ivoQDWzIGoDV1G\">Verify your account</a><br><br>If it does not work please copy this link and place it in your browser url.<br><br>http://localhost/myschool_gh/verify?account&token=cuOes44nI6yE6QLU9jVPXX0SwAwp1hfljMqd3gaYpin2rbTxcsqJkAdS3ivoQDWzIGoDV1G', 'uIkajsw123456789064hxk1fc3efmnva', '0', NULL),
(2, 'dct2TfPXHnoy8IspF7iCxjGJEmDzu0Ka', 'account-verify', 'ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v', '{\"recipients_list\":[{\"fullname\":\"Frank Amponsah\",\"email\":\"frankamoah@gmail.com\",\"customer_id\":\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\"}]}', '2020-12-17 22:59:40', 'send_now', '2020-12-17 22:59:40', 'email', '0', '[MySchoolGH Management System] Account Verification', 'Hello Frank,<a class=\"alert alert-success\" href=\"http://localhost/myschool_gh/verify?account&token=ISif1mdadb3LEq7rxO04znYjHFLYXM1PbtKo9GGhzZOkWucgjUXs6weQaBm8P2TAcETvsFW\">Verify your account</a><br><br>If it does not work please copy this link and place it in your browser url.<br><br>http://localhost/myschool_gh/verify?account&token=ISif1mdadb3LEq7rxO04znYjHFLYXM1PbtKo9GGhzZOkWucgjUXs6weQaBm8P2TAcETvsFW', 'uIkajsw123456789064hxk1fc3efmnva', '0', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_notification`
--

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
(1, 'Q9ntZBs2DbxlYSam6kcdHzpTUj57he0M', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'Password Reset', 'You have successfully changed your password.', NULL, 'system', '4', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-09-19 11:30:23', 'Unseen', NULL, '0', '1'),
(2, 'BtsPYHUMn1hCEOvrTcfp8ewdQj4iX5SG', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'Password Reset Request', 'A request was made by yourself to change your password.', NULL, 'system', '4', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-09-22 22:49:56', 'Unseen', NULL, '0', '1'),
(3, 'u7jWHTItP8iRv9rYeXFqk6CDZ0zdnmQp', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'Login Failures', 'An attempt count of 5 was made to access your Account. <br>We recommend that you change your password if this was not you. <a href=\"{{APPURL}}profile\">Visit your profile</a> to effect those changes.                               ', NULL, 'system', '3', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-09-22 23:04:46', 'Unseen', NULL, '0', '1'),
(5, 'MQd49HxOl0vsc86zCE5gw7D1yBIN3XUu', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'Status Change', 'Your Complaint on the subject have been submitted.', NULL, 'system', '10', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', '2020-09-23 16:52:29', 'Unseen', NULL, '0', '1'),
(6, '3xnpZt6Fha0SRu5XijrKdl8wMP7Hb1eC', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'Status Change', 'Your Complaint on the subject <strong>Draft With Attachments</strong> have been submitted.', NULL, 'system', '10', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', '2020-09-23 16:59:45', 'Unseen', NULL, '0', '1'),
(7, 'fs0ZRXzpAjPE2U51QyVW6knmIDcFuH49', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'Status Change', '<strong>Frank Amoako</strong> left a new reply on your <a title=\"Click to View Complaint\" href=\"{{APPURL}}{{RESOURCE_PAGE}}DW3nA52VUHqlraxCNSvdPEQGu9z0hLIo\">Complaint</a>.', NULL, 'system', '10', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-09-23 17:34:16', 'Unseen', NULL, '0', '1'),
(8, '0cLjb8kfNTEiyuQD9egA1VPG6zpoUF7H', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'Thread Reply', '<strong>Frank Amoako</strong> left a new reply on your <a title=\"Click to View Complaint\" href=\"{{APPURL}}{{RESOURCE_PAGE}}DW3nA52VUHqlraxCNSvdPEQGu9z0hLIo\">Complaint</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-09-23 17:36:57', 'Unseen', NULL, '0', '1'),
(9, 'KrsObuhzVQ68Ud5LFifEBCM4HmwAjqlJ', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'Thread Reply', '<strong>Frank Amoako</strong> left a new reply on your <a title=\"Click to View Complaint\" href=\"{{APPURL}}{{RESOURCE_PAGE}}DW3nA52VUHqlraxCNSvdPEQGu9z0hLIo\">Complaint</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-09-23 18:52:17', 'Unseen', NULL, '0', '1'),
(10, 'FjpyR6QV47aS1rdhgMEwYfBLlKWUNzAD', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'Thread Reply', '<strong>Frank Amoako</strong> left a new reply on your <a title=\"Click to View Complaint\" href=\"{{APPURL}}{{RESOURCE_PAGE}}DW3nA52VUHqlraxCNSvdPEQGu9z0hLIo\">Complaint</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-09-23 19:40:09', 'Unseen', NULL, '0', '1'),
(11, '1OBVhUiNED72uKJP85Cvp4oszaIR09A3', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'Complaint', '<strong>Frank Amoako</strong> assigned a complaint to you. <a title=\"Click to View\" href=\"{{APPURL}}complaints/zbr4ovRJ8KfaH16FxZdkA02DC3QeN7Bp\">Click to view</a>.', NULL, 'system', '11', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-09-23 20:03:46', 'Unseen', NULL, '0', '1'),
(14, 'VkfgOmL1Tuq5J2e4bxRW3UFPwEMvsh96', 'zbr4ovRJ8KfaH16FxZdkA02DC3QeN7Bp', 'Complaint', 'Your complaint with subject <strong>PREACHING SERMON ON 16/08/2020</strong> has been <strong>Solved</strong> by <strong>Frank Amoako</strong>.', NULL, 'system', '11', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-09-23 20:13:26', 'Unseen', NULL, '0', '1'),
(15, 'ruGtXQRYsz2gnZfFDJpU1ljhcyoE85Ma', 'BZ7ScjiWnKD0YbwONqleTIUvy3PksGzQ', 'Complaint', '<strong>Frank Amoako</strong> assigned a complaint to you. <a title=\"Click to View\" href=\"{{APPURL}}complaints/USdVHGI4N7zbgFscZixyTXlPwB1O8h6L\">Click to view</a>.', NULL, 'system', '11', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-09-23 20:14:21', 'Unseen', NULL, '0', '1'),
(16, 'QOLCEHx86uf2hSlbJRzMiwInWFjv5tks', 'USdVHGI4N7zbgFscZixyTXlPwB1O8h6L', 'Complaint', 'Your complaint with subject <strong>Problem Contacting Client</strong> has been <strong>Solved</strong> by <strong>Frank Amoako</strong>.', NULL, 'system', '11', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-09-23 20:14:21', 'Unseen', NULL, '0', '1'),
(17, 'MjPaAl2gH6CNqD8KQ7zvWVn0yYIZB3st', 'WYhHCdxN87fOtuJrmzEc5RlkS0I6y1ig', 'Complaint', 'Your complaint with subject <strong>I am testing the lodge complaint</strong> has been <strong>Solved</strong> by <strong>Frank Amoako</strong>.', NULL, 'system', '11', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-09-23 20:16:09', 'Unseen', NULL, '0', '1'),
(18, 'GUJnz4Lwc7BsODEv8eahbTA10VPfKQXk', 'DW3nA52VUHqlraxCNSvdPEQGu9z0hLIo', 'Complaint', 'Your complaint with subject <strong>Draft With Attachments</strong> has been <strong>Solved</strong> by <strong>Frank Amoako</strong>.', NULL, 'system', '11', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-09-23 20:50:31', 'Unseen', NULL, '0', '1'),
(19, 'DTJSHjVhf3Z8GcQCMWw2ElbKry9txYvU', 'BZ7ScjiWnKD0YbwONqleTIUvy3PksGzQ', 'Complaint', '<strong>Frank Amoako</strong> assigned a complaint to you. <a title=\"Click to View\" href=\"{{APPURL}}complaints-view/TodX7zmaCIUHGwb56MetlvgKunL2JNiZ\">Click to view</a>.', NULL, 'system', '11', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-09-28 16:06:42', 'Unseen', NULL, '0', '1'),
(20, '7AgMeNxQFmpTKaXfR2wh5n9iUkVJ6L3v', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'Thread Reply', '<strong>Frank Amoako</strong> left a new reply on your <a title=\"Click to View Complaint\" href=\"{{APPURL}}{{RESOURCE_PAGE}}TodX7zmaCIUHGwb56MetlvgKunL2JNiZ\">Complaint</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-09-28 16:09:52', 'Unseen', NULL, '0', '1'),
(21, 'TJp2uVfBnIPkshM1Zg5e9l36G4UaFNCX', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'Complaint', '<strong>Frank Amoako</strong> assigned a complaint to you. <a title=\"Click to View\" href=\"{{APPURL}}complaints-view/TodX7zmaCIUHGwb56MetlvgKunL2JNiZ\">Click to view</a>.', NULL, 'system', '11', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-09-28 16:12:40', 'Unseen', NULL, '0', '1'),
(22, 'hquFAIVNzjlnWdM5QeUKPYbvo6f1DpO8', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Status Change', 'Your Policy <strong>Health Insurance - Updated</strong> have been submitted.', NULL, 'system', '10', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', '2020-09-29 19:16:05', 'Unseen', NULL, '0', '1'),
(23, '6jGaKnSosR7Dx0UPJ8QIHC4V3cT1AqdO', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Reply', '<strong>Frank Amoako</strong> left a new reply on your <a title=\"Click to View Policy\" href=\"{{APPURL}}{{RESOURCE_PAGE}}Y5vzMiG9qHXAgSZE8xBcNVD2CulToKt6\">Policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-09-29 19:39:02', 'Unseen', NULL, '0', '1'),
(24, 'JxuEH3WyXcQOzjbKhtD0R5gFwBCMdIPn', 'BZ7ScjiWnKD0YbwONqleTIUvy3PksGzQ', 'Insurance Policy', '<strong>Frank Amoako</strong> assigned an Insurance to you. <a title=\"Click to View\" href=\"{{APPURL}}policies-view/\">Click to view</a>.', NULL, 'system', '6', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-09-29 20:38:36', 'Unseen', NULL, '0', '1'),
(25, 'hRdAkHy9KrFL1UsPWzice3vq6lmGt4j2', 'BZ7ScjiWnKD0YbwONqleTIUvy3PksGzQ', 'Account Update', '<strong>Frank Amoako</strong> updated your account information', NULL, 'system', '9', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-09-30 11:43:33', 'Unseen', NULL, '0', '1'),
(26, 'zG3JmtYfVgF4kaUo7DXM6er0Ex8nT2dv', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Status Change', 'Your Policy Claim <strong></strong> have been submitted.', NULL, 'system', '10', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', '2020-09-30 20:56:25', 'Unseen', NULL, '0', '1'),
(27, 'zYwpilMa3CrJ7fmWU1eAsXxI0tgThDGo', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Policy Claims', '<strong>Hello ,</strong> your claim request on the policy <a title=\"Click to View\" href=\"{{APPURL}}claims-view/vHJuEzR5bLf60deGMaV8yBorDwOQmcN1\"><strong>CTI000012020</strong></a> is Confirmed.', NULL, 'system', '7', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-09-30 21:05:48', 'Unseen', NULL, '0', '1'),
(28, '3N4YICtbgOLcopJwnZWUjHk9lEh0Re17', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Policy Claims', '<strong>Hello ,</strong> your claim request on the policy <a title=\"Click to View\" href=\"{{APPURL}}claims-view/sBAhYUIbjLc30PkpGgdVqtzrZEm8vH7N\"><strong>CTI000012020</strong></a> is Confirmed.', NULL, 'system', '7', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-09-30 21:58:03', 'Unseen', NULL, '0', '1'),
(29, 'APwjO3T6JpvQzStouaK0rRIWGBsli8Hd', 'BZ7ScjiWnKD0YbwONqleTIUvy3PksGzQ', 'Policy Claims', '<strong>Frank Amoako</strong> assigned an Insurance Claim request to you. <a title=\"Click to View\" href=\"{{APPURL}}claims-view/sBAhYUIbjLc30PkpGgdVqtzrZEm8vH7N\">Click to view</a>.', NULL, 'system', '7', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-09-30 22:00:58', 'Unseen', NULL, '0', '1'),
(30, 'p5TGqk7BbiQgWIetlVRXaSN4rjso1HAM', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'Policy Claims', '<strong>Frank Amoako</strong> assigned an Insurance Claim request to you. <a title=\"Click to View\" href=\"{{APPURL}}claims-view/sBAhYUIbjLc30PkpGgdVqtzrZEm8vH7N\">Click to view</a>.', NULL, 'system', '7', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-01 10:03:51', 'Unseen', NULL, '0', '1'),
(31, 'TIoK0dAEZzkVYXfcbCDWtBFmuaNwLgRr', NULL, 'Thread Reply', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view claim\" href=\"{{APPURL}}claims-view/\">claim</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-01 11:13:46', 'Unseen', NULL, '0', '1'),
(32, 'xaSI3jADKrzbUne1XNpEBWT0mOkCVvYt', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Reply', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view claim\" href=\"{{APPURL}}claims-view/sBAhYUIbjLc30PkpGgdVqtzrZEm8vH7N\">claim</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-01 11:17:48', 'Unseen', NULL, '0', '1'),
(33, 'B4VwHsiPpM1nadELfRy7ZJuC5ItK83h0', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Reply', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view claim\" href=\"{{APPURL}}claims-view/sBAhYUIbjLc30PkpGgdVqtzrZEm8vH7N\">claim</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-01 12:43:36', 'Unseen', NULL, '0', '1'),
(34, 'kU4H3I8P2VbGNeL9B6DtdoqRY5y0MQzp', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Reply', '<strong>Frank Amoako</strong> left a new reply on your <a title=\"Click to view Policy\" href=\"{{APPURL}}{{RESOURCE_PAGE}}/Y5vzMiG9qHXAgSZE8xBcNVD2CulToKt6\">Policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-01 13:18:06', 'Unseen', NULL, '0', '1'),
(35, 'yErukNxbUJz5Sv07jiLTgeA9RPXInZqO', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Reply', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view claim\" href=\"{{APPURL}}claims-view/sBAhYUIbjLc30PkpGgdVqtzrZEm8vH7N\">claim</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-01 15:04:45', 'Unseen', NULL, '0', '1'),
(36, '5An4xENfdo3CciXTjLy6wp29HsvF1kJ7', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Reply', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view claim\" href=\"{{APPURL}}claims-view/sBAhYUIbjLc30PkpGgdVqtzrZEm8vH7N\">claim</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-01 15:05:51', 'Unseen', NULL, '0', '1'),
(37, '6l7TUA3xMWyJksvqfuFrIt4OD2NgHd1m', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Reply', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view claim\" href=\"{{APPURL}}claims-view/sBAhYUIbjLc30PkpGgdVqtzrZEm8vH7N\">claim</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-01 15:07:30', 'Unseen', NULL, '0', '1'),
(38, 'JlEYLikw1artbNIOSXeBzZypQquF94T0', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Reply', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view claim\" href=\"{{APPURL}}claims-view/sBAhYUIbjLc30PkpGgdVqtzrZEm8vH7N\">claim</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-01 15:10:42', 'Unseen', NULL, '0', '1'),
(39, 'yiPZpzCtmwGDbTaWk5Y9Vlu6e21XfonR', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Reply', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view claim\" href=\"{{APPURL}}claims-view/sBAhYUIbjLc30PkpGgdVqtzrZEm8vH7N\">claim</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-01 15:10:58', 'Unseen', NULL, '0', '1'),
(40, 'EhIg1dqlXTpozH8c6CivZWnYu0KNV9Be', NULL, 'Thread Reply', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view claim\" href=\"{{APPURL}}claims-view/9W3oQkX04NyFU8LMEVbtKISe5TChPD1f\">claim</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-01 16:01:46', 'Unseen', NULL, '0', '1'),
(41, 'y8OP0gCDYeMz7u2XK6pvobhUdFGxaB4m', NULL, 'Thread Reply', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view claim\" href=\"{{APPURL}}claims-view/9W3oQkX04NyFU8LMEVbtKISe5TChPD1f\">claim</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-01 16:04:02', 'Unseen', NULL, '0', '1'),
(42, '2cTZI6lWa57vSfLeBKmRyPrUEoqDAiC4', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Reply', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view policy\" href=\"{{APPURL}}policies-view/9W3oQkX04NyFU8LMEVbtKISe5TChPD1f\">policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-01 16:10:05', 'Unseen', NULL, '0', '1'),
(43, 'g3z5tTUwPpDeNv70XjMyObZGqRxIkJKC', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Reply', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view claim\" href=\"{{APPURL}}claims-view/sBAhYUIbjLc30PkpGgdVqtzrZEm8vH7N\">claim</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-01 17:55:35', 'Unseen', NULL, '0', '1'),
(44, 'HU4eWlAvrTtCFQDYcNZ1KuX5b7VoIwpz', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Reply', '<strong>Frank Amoako</strong> left a new reply on your <a title=\"Click to view Policy Claim\" href=\"{{APPURL}}{{RESOURCE_PAGE}}/sBAhYUIbjLc30PkpGgdVqtzrZEm8vH7N\">Policy Claim</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-01 17:56:52', 'Unseen', NULL, '0', '1'),
(45, 'yU8CDjkw3bR2A9hZpgKnYLQPSBxrcEl7', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Policy Claims', '<strong>Hello Priscilla Appiah,</strong> your claim request on the policy <a title=\"Click to View\" href=\"{{APPURL}}claims-view/sBAhYUIbjLc30PkpGgdVqtzrZEm8vH7N\"><strong>CTI000012020</strong></a> is Approved.', NULL, 'system', '7', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-01 18:05:10', 'Unseen', NULL, '0', '1'),
(46, 'goWfXSecKpQhPnruzNZi5Ymkw0FvIysO', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Reply', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view policy\" href=\"{{APPURL}}policies-view/9W3oQkX04NyFU8LMEVbtKISe5TChPD1f\">policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-03 09:36:19', 'Unseen', NULL, '0', '1'),
(47, '3ENcP5qdL0pWuUojIeDMrFakt2GvxsXb', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Reply', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view policy\" href=\"{{APPURL}}policies-view/Y5vzMiG9qHXAgSZE8xBcNVD2CulToKt6\">policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-03 11:03:27', 'Unseen', NULL, '0', '1'),
(48, 'Q0pJZmKgyPxN6A1ajcLzFM8SoiVUT9C5', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Reply', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view policy\" href=\"{{APPURL}}policies-view/Y5vzMiG9qHXAgSZE8xBcNVD2CulToKt6\">policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-03 11:05:11', 'Unseen', NULL, '0', '1'),
(49, 'kGDLaMZYdlsiyg3z4S6FR5pIVo7Cmvx0', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Reply', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view policy\" href=\"{{APPURL}}policies-view/Y5vzMiG9qHXAgSZE8xBcNVD2CulToKt6\">policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-03 11:05:28', 'Unseen', NULL, '0', '1'),
(50, 'ISuRWOYcH02M8at47nUp6Niq3Fzr1VJm', '5U1hKwp4HZNSW80jRCXEVrIJOvmYTAyd', 'Thread Reply', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view policy\" href=\"{{APPURL}}policies-view/kajflkjakdajkdka8dkkd98kkdkKKkdj\">policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-03 11:06:13', 'Unseen', NULL, '0', '1'),
(51, 'L8kgeWVQzTNxXphqwFtfRdsC0jM5Palo', '5U1hKwp4HZNSW80jRCXEVrIJOvmYTAyd', 'Thread Reply', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view policy\" href=\"{{APPURL}}policies-view/kajflkjakdajkdka8dkkd98kkdkKKkdj\">policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-03 11:06:40', 'Unseen', NULL, '0', '1'),
(52, 'ky0NwWLvY1qKFiShdgt9aesuJf5IOMXU', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Reply', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view claim\" href=\"{{APPURL}}claims-view/sBAhYUIbjLc30PkpGgdVqtzrZEm8vH7N\">claim</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-03 11:07:43', 'Unseen', NULL, '0', '1'),
(53, '5v9g3PGTfKDIdoVHUEZL1qF4RNXOzuwb', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Reply', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view claim\" href=\"{{APPURL}}claims-view/sBAhYUIbjLc30PkpGgdVqtzrZEm8vH7N\">claim</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-03 11:09:18', 'Unseen', NULL, '0', '1'),
(54, 'p8YNRGve5hWmUwsJqTjikoDxQatzAbB7', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Complaint', 'Your complaint with subject <strong>Insurance Company Item List</strong> has been <strong>Solved</strong> by <strong>Frank Amoako</strong>.', NULL, 'system', '11', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-03 13:37:27', 'Unseen', NULL, '0', '1'),
(55, '5jhritHs0BZeckgvVRbEWlIPA4UJFpT1', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Complaint', 'Your complaint with subject <strong>Bug on my Policy</strong> has been <strong>Solved</strong> by <strong>Frank Amoako</strong>.', NULL, 'system', '11', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-03 13:37:42', 'Unseen', NULL, '0', '1'),
(56, 'ZcBIAuvndhSbL6HpgFxj5983OVi07eUR', '7S89OC3soyemFhrj24qtP6ifvLzxQK1U', 'Claims', '<strong>Test Insurance Broker</strong> made a claim on your policy <a title=\"Click to View\" href=\"{{APPURL}}claims-view/gUdRTkt2yQIGV6cMnDO7LqfYejrZ5bEx\">POLDL874892020</a>.', NULL, 'system', '11', 'BZ7ScjiWnKD0YbwONqleTIUvy3PksGzQ', '2020-10-03 16:48:19', 'Unseen', NULL, '0', '1'),
(57, 'WbpT7AVc4NuXDj10avx3tzHJZLsMoOwd', '7S89OC3soyemFhrj24qtP6ifvLzxQK1U', 'Policy Claims', '<strong>Hello Fredrick Amoah,</strong> your claim request on the policy <a title=\"Click to View\" href=\"{{APPURL}}claims-view/gUdRTkt2yQIGV6cMnDO7LqfYejrZ5bEx\"><strong>POLDL874892020</strong></a> is Confirmed.', NULL, 'system', '7', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-03 17:22:31', 'Unseen', NULL, '0', '1'),
(58, 'u7w0XP69BYRk2jdiHczFUDCvVIKsb534', '7S89OC3soyemFhrj24qtP6ifvLzxQK1U', 'Thread Comment', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view claim\" href=\"{{APPURL}}claims-view/gUdRTkt2yQIGV6cMnDO7LqfYejrZ5bEx\">claim</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-03 17:43:36', 'Unseen', NULL, '0', '1'),
(59, 'I4VrchLoHaDvOsGiqPBTzeCmNlnXy68R', 'BZ7ScjiWnKD0YbwONqleTIUvy3PksGzQ', 'Thread Comment', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view claim\" href=\"{{APPURL}}claims-view/gUdRTkt2yQIGV6cMnDO7LqfYejrZ5bEx\">claim</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-03 17:43:36', 'Unseen', NULL, '0', '1'),
(60, 'CJpFzgynNx4vdZEhO7wUHu1IkrAaGlcT', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Comment', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view policy\" href=\"{{APPURL}}policies-view/Y5vzMiG9qHXAgSZE8xBcNVD2CulToKt6\">policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-05 08:26:13', 'Unseen', NULL, '0', '1'),
(61, 'zV9gNioKJ86tZABIG4MPDS13fupTlekw', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Comment', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view policy\" href=\"{{APPURL}}policies-view/Y5vzMiG9qHXAgSZE8xBcNVD2CulToKt6\">policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-05 08:26:13', 'Unseen', NULL, '0', '1'),
(62, '5iCglNqxM9FRPvBtXfoGO4KrmHwsnQbh', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Comment', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view policy\" href=\"{{APPURL}}policies-view/Y5vzMiG9qHXAgSZE8xBcNVD2CulToKt6\">policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-05 08:29:16', 'Unseen', NULL, '0', '1'),
(63, 'iB5wOPXYJF1pz6emLIgs8UTxjD4QhC2t', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Comment', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view policy\" href=\"{{APPURL}}policies-view/Y5vzMiG9qHXAgSZE8xBcNVD2CulToKt6\">policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-05 08:29:16', 'Unseen', NULL, '0', '1'),
(64, 'NTR5ywe3LYSPAxzXZ4d07WhabjnM6qED', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Cancel Policy', 'Cancel policy request has successfully been lodged. You will be notified when the request is approved.', NULL, 'system', '6', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', '2020-10-06 05:22:25', 'Unseen', NULL, '0', '1'),
(65, 'TcyQRk0ZfaM7YpSoCEnN3s5x6gUJHhG4', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Insurance Policy', '<strong>Priscilla Appiah</strong> has made a request to cancel your policy with ID: <strong>CTI000012020</strong>. You will be notified once the request is approved.', NULL, 'system', '6', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', '2020-10-06 05:30:00', 'Unseen', NULL, '0', '1'),
(66, 'pUlSoRgLizvDHBGFqO3EX1NJ4ceWnVT2', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Cancel Policy', 'Your request to cancel the policy with ID: <strong>CTI000012020</strong> have been approved.', NULL, 'system', '6', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-06 05:30:13', 'Unseen', NULL, '0', '1'),
(67, 'iJRUku1DfOynmeM3bWrj2YIEP4sCT6Gx', '7S89OC3soyemFhrj24qtP6ifvLzxQK1U', 'Policy Claims', '<strong>Hello Fredrick Amoah,</strong> your claim request on the policy <a title=\"Click to View\" href=\"{{APPURL}}claims-view/gUdRTkt2yQIGV6cMnDO7LqfYejrZ5bEx\"><strong>POLDL874892020</strong></a> is Approved.', NULL, 'system', '7', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-06 05:44:10', 'Unseen', NULL, '0', '1'),
(68, 'czXFEGA8PlCkqrQNjM2KgVYUiJsSfODp', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'Insurance Policy', '<strong>Frank Amoako</strong> assigned an Insurance to you. <a title=\"Click to View\" href=\"{{APPURL}}policies-view/Y5vzMiG9qHXAgSZE8xBcNVD2CulToKt6\">Click to view</a>.', NULL, 'system', '6', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-06 06:02:47', 'Unseen', NULL, '0', '1'),
(69, 'v4BhfrVAHLRYsoTmdWPS86EMFagp1I9G', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Account Update', '<strong>Frank Amoako</strong> updated your account information', NULL, 'system', '9', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-06 06:04:16', 'Unseen', NULL, '0', '1'),
(70, 'AF3HI95z8rS7fqeuGRLw2x10QlmNi4oZ', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Account Update', '<strong>Frank Amoako</strong> updated your account information', NULL, 'system', '9', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-06 06:04:49', 'Unseen', NULL, '0', '1'),
(71, 'HYgOhrBcdkZCRiPfGsDmxMw8pqe6ES5W', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Account Update', '<strong>Frank Amoako</strong> updated your account information', NULL, 'system', '9', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-06 06:05:35', 'Unseen', NULL, '0', '1'),
(72, '5dmXBZ2vcogpuTUfEJG6V8Q4il0YqaP1', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Reply', '<strong>Frank Amoako</strong> left a new reply on your <a title=\"Click to view Policy\" href=\"{{APPURL}}{{RESOURCE_PAGE}}/Y5vzMiG9qHXAgSZE8xBcNVD2CulToKt6\">Policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-06 10:08:57', 'Unseen', NULL, '0', '1'),
(73, 'tILn4lG3Wz5eq72DUproPyfCBFTmNaiS', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Insurance Policy', '<strong>Priscilla Appiah</strong> has made a request to cancel your policy with ID: <strong>GAF00012020</strong>. You will be notified once the request is approved.', NULL, 'system', '6', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', '2020-10-06 10:14:44', 'Unseen', NULL, '0', '1'),
(74, 'SVF013kwuLta7vCnmxZdOAUzpNJoRWIb', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Cancel Policy', 'Your request to cancel the policy with ID: <strong>GAF00012020</strong> have been approved.', NULL, 'system', '6', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-06 10:17:27', 'Unseen', NULL, '0', '1'),
(75, 'aJ7ZndiWm6l38tsNhDFXBKAbovyGfLPY', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Comment', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view policy\" href=\"{{APPURL}}policies-view/0DsMt2GhbKOlVRvqfcmEkN1eUxPWuY8A\">policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-06 10:50:07', 'Unseen', NULL, '0', '1'),
(76, 'aKIjSfpRGU6E7rsnZWQOT13qFAPgeoiu', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Comment', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view policy\" href=\"{{APPURL}}policies-view/0DsMt2GhbKOlVRvqfcmEkN1eUxPWuY8A\">policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-06 10:50:07', 'Unseen', NULL, '0', '1'),
(77, 'dHZaSl9NBPenv1mFf5QJ26MiouXykwjr', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Comment', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view policy\" href=\"{{APPURL}}policies-view/0DsMt2GhbKOlVRvqfcmEkN1eUxPWuY8A\">policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-06 10:50:07', 'Unseen', NULL, '0', '1'),
(78, 'reFwSKtjhYMZcTy6uO1k2aDIJqfHoL4C', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Comment', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view policy\" href=\"{{APPURL}}policies-view/0DsMt2GhbKOlVRvqfcmEkN1eUxPWuY8A\">policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-06 10:50:07', 'Unseen', NULL, '0', '1'),
(79, 'viFGrQbP7nYhZg5WMR4LUjfINKxzE1s9', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Comment', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view policy\" href=\"{{APPURL}}policies-view/0DsMt2GhbKOlVRvqfcmEkN1eUxPWuY8A\">policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-06 10:50:07', 'Unseen', NULL, '0', '1'),
(80, 'yOeSczdKL7wtvmTxBHRJ3lPaUsAjQi9p', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Comment', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view policy\" href=\"{{APPURL}}policies-view/0DsMt2GhbKOlVRvqfcmEkN1eUxPWuY8A\">policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-06 10:50:07', 'Unseen', NULL, '0', '1'),
(81, 'dAZzpkEQeGoyiF8Nt62a1cl7SguMX0DY', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Comment', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view policy\" href=\"{{APPURL}}policies-view/0DsMt2GhbKOlVRvqfcmEkN1eUxPWuY8A\">policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-06 10:51:13', 'Unseen', NULL, '0', '1'),
(82, 'ILb1TzNS534DRntuWCrsBj0UAQ6kXZKh', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Comment', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view policy\" href=\"{{APPURL}}policies-view/0DsMt2GhbKOlVRvqfcmEkN1eUxPWuY8A\">policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-06 10:51:13', 'Unseen', NULL, '0', '1'),
(83, 'ad96JMi1BlHuTOybtnzwGm2Y35IWLVsU', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Policy Claims', '<strong>Hello Priscilla Appiah,</strong> your claim request on the policy <a title=\"Click to View\" href=\"{{APPURL}}claims-view/sBAhYUIbjLc30PkpGgdVqtzrZEm8vH7N\"><strong>CTI000012020</strong></a> is Confirmed.', NULL, 'system', '7', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-06 15:46:04', 'Unseen', NULL, '0', '1'),
(84, 'dr4keOKXNZEwFhb3TIHtYm2Q9lWDUcj7', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Policy Claims', '<strong>Hello Priscilla Appiah,</strong> your claim request on the policy <a title=\"Click to View\" href=\"{{APPURL}}claims-view/sBAhYUIbjLc30PkpGgdVqtzrZEm8vH7N\"><strong>CTI000012020</strong></a> is Approved.', NULL, 'system', '7', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-06 15:46:21', 'Unseen', NULL, '0', '1'),
(85, 'P1SAOUIY8T52yEFL9qnJcVGBXw7phmbC', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Reply', '<strong>Frank Amoako</strong> left a new reply on your <a title=\"Click to view Complaint\" href=\"{{APPURL}}{{RESOURCE_PAGE}}/miZcHfKEWdINSu1jVPl4G8RJMpn3Yr5L\">Complaint</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-06 19:12:54', 'Unseen', NULL, '0', '1'),
(86, 'Gqhf6Ioap48zVxUlON0DiknA27gWtTsr', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Reply', '<strong>Frank Amoako</strong> left a new reply on your <a title=\"Click to view Policy\" href=\"{{APPURL}}{{RESOURCE_PAGE}}/0DsMt2GhbKOlVRvqfcmEkN1eUxPWuY8A\">Policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-07 01:41:14', 'Unseen', NULL, '0', '1'),
(87, '0m18Q6pAqusPDdkoaNzOGXw4yeYrV3JT', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Comment', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view policy\" href=\"{{APPURL}}policies-view/0DsMt2GhbKOlVRvqfcmEkN1eUxPWuY8A\">policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-07 01:41:33', 'Unseen', NULL, '0', '1'),
(88, 'M79miQunGScqrW3LaxPfoTABZyRsO5XI', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Comment', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view policy\" href=\"{{APPURL}}policies-view/0DsMt2GhbKOlVRvqfcmEkN1eUxPWuY8A\">policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-07 01:41:33', 'Unseen', NULL, '0', '1'),
(89, '4iR6K1a2WNTMIsOXZdHpmyt9hzUuvJ5e', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Insurance Policy', '<strong>Priscilla Appiah</strong> has made a request to cancel your policy with ID: <strong>CTI000022020</strong>. You will be notified once the request is approved.', NULL, 'system', '6', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', '2020-10-07 01:42:14', 'Unseen', NULL, '0', '1'),
(90, 'qlwkisZjezM3DWd602NgTXuyb7QErxB1', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Comment', '<strong>Frank Amoako</strong> left a comment on your request to cancel the policy <a title=\"Click to view cancel\" href=\"{{APPURL}}policies-view/UIVev_0DsMt2GhbKOlVRvqfcmEkN1eUxPWuY8A\">CTI000022020</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-07 01:42:41', 'Unseen', NULL, '0', '1'),
(91, 'oE28Nuc4MQKabPSJ9RlTkI5BqitnYepG', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Comment', '<strong>Frank Amoako</strong> left a comment on your request to cancel the policy <a title=\"Click to view cancel\" href=\"{{APPURL}}policies-view/UIVev_0DsMt2GhbKOlVRvqfcmEkN1eUxPWuY8A\">CTI000022020</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-07 01:43:19', 'Unseen', NULL, '0', '1'),
(92, 'o3VRciTYP8yxrBqAzvulKIWX4pU0OnQ5', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Comment', '<strong>Frank Amoako</strong> left a comment on your request to cancel the policy <a title=\"Click to view cancel\" href=\"{{APPURL}}policies-view/UIVev_0DsMt2GhbKOlVRvqfcmEkN1eUxPWuY8A\">CTI000022020</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-07 01:44:07', 'Unseen', NULL, '0', '1'),
(93, 'EXZosu27ATSMI80wYrbgldWz5jvtRVHU', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Cancel Policy', 'Your request to cancel the policy with ID: <strong>CTI000022020</strong> have been approved.', NULL, 'system', '6', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-07 01:44:15', 'Unseen', NULL, '0', '1'),
(94, 'hnvxgUwmQVp4o35iqzGXfcK8erkB1IOC', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Reply', '<strong>Frank Amoako</strong> left a new reply on your <a title=\"Click to view Policy\" href=\"{{APPURL}}{{RESOURCE_PAGE}}/0DsMt2GhbKOlVRvqfcmEkN1eUxPWuY8A\">Policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-07 02:03:50', 'Unseen', NULL, '0', '1'),
(95, 'D9MkIyWpQGnxwZd2EoPaczlrNtU71q4v', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Comment', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view policy\" href=\"{{APPURL}}policies-view/0DsMt2GhbKOlVRvqfcmEkN1eUxPWuY8A\">policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-07 02:04:41', 'Unseen', NULL, '0', '1'),
(96, 'hfg9iLz1W3rRcExUAYmVB4HoZtaOjP7l', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Comment', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view policy\" href=\"{{APPURL}}policies-view/0DsMt2GhbKOlVRvqfcmEkN1eUxPWuY8A\">policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-07 02:04:41', 'Unseen', NULL, '0', '1'),
(97, '8mGd5LXhCan3bMkAJ0f9KWsODFvBHZxe', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Reply', '<strong>Frank Amoako</strong> left a new reply on your <a title=\"Click to view Policy\" href=\"{{APPURL}}{{RESOURCE_PAGE}}/0DsMt2GhbKOlVRvqfcmEkN1eUxPWuY8A\">Policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-07 02:05:08', 'Unseen', NULL, '0', '1'),
(98, 'IlPYspnADreqZ7bamV5yt2BUCFQ489vO', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Reply', '<strong>Frank Amoako</strong> left a new reply on your <a title=\"Click to view Policy\" href=\"{{APPURL}}{{RESOURCE_PAGE}}/0DsMt2GhbKOlVRvqfcmEkN1eUxPWuY8A\">Policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-07 02:05:34', 'Unseen', NULL, '0', '1'),
(99, 'NM4kgmqDoBSzel3vP8JycjQuZ6RtXwpL', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Reply', '<strong>Frank Amoako</strong> left a new reply on your <a title=\"Click to view Policy\" href=\"{{APPURL}}{{RESOURCE_PAGE}}/0DsMt2GhbKOlVRvqfcmEkN1eUxPWuY8A\">Policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-07 02:07:08', 'Unseen', NULL, '0', '1'),
(100, 'v3lRqf8c5mAOdkxP2T9YKpu6SbWNC4MQ', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Comment', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view policy\" href=\"{{APPURL}}policies-view/0DsMt2GhbKOlVRvqfcmEkN1eUxPWuY8A\">policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-07 02:07:28', 'Unseen', NULL, '0', '1'),
(101, '19jDlbSausezIQ2GdYq48fxKX73Vwtgc', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Comment', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view policy\" href=\"{{APPURL}}policies-view/0DsMt2GhbKOlVRvqfcmEkN1eUxPWuY8A\">policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-07 02:07:28', 'Unseen', NULL, '0', '1'),
(102, 'e1DoRrQx9YtZ4dJ6gj03KafBXkcUmMpi', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Comment', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view policy\" href=\"{{APPURL}}policies-view/0DsMt2GhbKOlVRvqfcmEkN1eUxPWuY8A\">policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-07 14:09:54', 'Unseen', NULL, '0', '1'),
(103, 'G9xDbFd64QliS58gCHAEnWvhqOyUfJrs', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Comment', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view policy\" href=\"{{APPURL}}policies-view/0DsMt2GhbKOlVRvqfcmEkN1eUxPWuY8A\">policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-07 14:09:54', 'Unseen', NULL, '0', '1'),
(104, '3bwfE8gsWrpuB0CONFdKVcHIM1AeGzm2', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Comment', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view policy\" href=\"{{APPURL}}policies-view/0DsMt2GhbKOlVRvqfcmEkN1eUxPWuY8A\">policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-07 14:10:37', 'Unseen', NULL, '0', '1'),
(105, 'VcFRrl9CP6jLkheuBaOgt8wbNWAS72ZG', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Comment', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view policy\" href=\"{{APPURL}}policies-view/0DsMt2GhbKOlVRvqfcmEkN1eUxPWuY8A\">policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-07 14:10:37', 'Unseen', NULL, '0', '1'),
(106, 'W29sXhgbVYmdPQUfLpDeSRFJjB1ZvTCA', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Reply', '<strong>Frank Amoako</strong> left a new reply on your <a title=\"Click to view Policy\" href=\"{{APPURL}}{{RESOURCE_PAGE}}/0DsMt2GhbKOlVRvqfcmEkN1eUxPWuY8A\">Policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-07 14:12:23', 'Unseen', NULL, '0', '1'),
(107, 'QsTqviH4dSyJzmBRwtuaA5Vo3C7KILhe', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'Account Update', '<strong>Frank Amoako</strong> updated your account information', NULL, 'system', '9', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-13 05:57:47', 'Unseen', NULL, '0', '1'),
(108, 'rpSXVC1gj0WvDfRd2UPuAzFN8Z9QOhcE', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'Account Update', '<strong>Frank Amoako</strong> updated your account information', NULL, 'system', '9', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-13 05:59:26', 'Unseen', NULL, '0', '1'),
(109, 'r3EiuQ9yT4Db2XwzM5q7kWNPhlvUVRHG', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'Account Update', '<strong>Frank Amoako</strong> updated your account information', NULL, 'system', '9', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-13 05:59:43', 'Unseen', NULL, '0', '1'),
(110, 'lcT5zrfsEtkFwUB9yxMb2dGZY3WAp0KL', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Announcement', '<a title=\"Click to View\" class=\"preview-announcement\" data-announcement_id=\"HU72tZ3lysP0Ag9Y5ehRqO4uCzMmvocp\" href=\"{{APPURL}}announcements\">Hello Priscilla Appiah, an announcement was posted for your review.</a>', NULL, 'system', '12', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-13 15:44:43', 'Unseen', NULL, '0', '1'),
(111, 'cNJj0zlO6oTFYdyvMECusib2UL53xZIA', 'KBCqU4A5zxR23PX9YlJ0LH8foDVuFrTm', 'Announcement', 'Hello Emmanuel Obeng Hyde, an announcement was posted for your review. <a title=\"Click to View\" class=\"preview-announcement\" data-announcement_id=\"HU72tZ3lysP0Ag9Y5ehRqO4uCzMmvocp\" href=\"{{APPURL}}announcements\">Click to view</a>', NULL, 'system', '12', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-13 15:44:44', 'Unseen', NULL, '0', '1'),
(112, 'chVFnqjCIk4zLWx5HwGuDrygPKl27fvX', '7S89OC3soyemFhrj24qtP6ifvLzxQK1U', 'Announcement', 'Hello Fredrick Amoah, an announcement was posted for your review. <a title=\"Click to View\" class=\"preview-announcement\" data-announcement_id=\"HU72tZ3lysP0Ag9Y5ehRqO4uCzMmvocp\" href=\"{{APPURL}}announcements\">Click to view</a>', NULL, 'system', '12', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-13 15:44:44', 'Unseen', NULL, '0', '1'),
(113, 'qBE5HAOeySXiusafmvQZPghMC1lzrcW8', 'G9fI4VlHtRPga5Ezq78S6wjNYcunFAs0', 'Announcement', 'Hello Grace Obeng, an announcement was posted for your review. <a title=\"Click to View\" class=\"preview-announcement\" data-announcement_id=\"HU72tZ3lysP0Ag9Y5ehRqO4uCzMmvocp\" href=\"{{APPURL}}announcements\">Click to view</a>', NULL, 'system', '12', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-13 15:44:44', 'Unseen', NULL, '0', '1'),
(114, 'Plq2nt8UIJAfk5FCmdpMZTwb4oYEVeDN', '5U1hKwp4HZNSW80jRCXEVrIJOvmYTAyd', 'Announcement', 'Hello Samuel Boateng, an announcement was posted for your review. <a title=\"Click to View\" class=\"preview-announcement\" data-announcement_id=\"HU72tZ3lysP0Ag9Y5ehRqO4uCzMmvocp\" href=\"{{APPURL}}announcements\">Click to view</a>', NULL, 'system', '12', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-13 15:44:44', 'Unseen', NULL, '0', '1'),
(115, 'w905UFAytf3Xu2BbmqHKlghecZJad8ps', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Email Message', '<a title=\"Click to View\" class=\"preview-email\" data-email_id=\"2EMKgph0O93bjkQRLmnqUAIylBJD1arT\" href=\"{{APPURL}}emails\">Hello Priscilla Appiah, you have been sent an email message.</a>', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-13 16:15:11', 'Unseen', NULL, '0', '1'),
(116, 'UD5Zy8QoBzASEjuMGdsLq4CPV3Kwmcih', 'sgHvi29tuJakdfzmp71nowNlWr40BKDV', 'Email Message', '<a title=\"Click to View\" class=\"preview-email\" data-email_id=\"2EMKgph0O93bjkQRLmnqUAIylBJD1arT\" href=\"{{APPURL}}emails\">Hello Solomon Kwarteng, you have been sent an email message.</a>', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-13 16:15:11', 'Unseen', NULL, '0', '1'),
(117, 'vs8JZwp0PBWDz2LCNoajeQhF34HMX79T', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'Status Change', 'Your  <strong></strong> have been submitted.', NULL, 'system', '10', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-15 13:44:50', 'Unseen', NULL, '0', '1'),
(118, 'XwAHUEbKWJLy6Oo2euYPn3ritMDfkxT9', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'Status Change', 'Your  <strong></strong> have been submitted.', NULL, 'system', '10', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-15 13:46:46', 'Unseen', NULL, '0', '1'),
(119, 'dFvxn6gVa7ktyRKiLq20TslOZcE8upJD', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Comment', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view policy\" href=\"{{APPURL}}policies-view/0DsMt2GhbKOlVRvqfcmEkN1eUxPWuY8A\">policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-15 14:10:59', 'Unseen', NULL, '0', '1'),
(120, 'OnWd3QjqyzJa0BXf8CsmVYb72UNp1Fle', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Comment', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view policy\" href=\"{{APPURL}}policies-view/0DsMt2GhbKOlVRvqfcmEkN1eUxPWuY8A\">policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-15 14:10:59', 'Unseen', NULL, '0', '1'),
(121, 'J3vBgxENRo7CPAe9TKuUZF2IX0w4Gchb', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Comment', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view policy\" href=\"{{APPURL}}policies-view/0DsMt2GhbKOlVRvqfcmEkN1eUxPWuY8A\">policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-15 14:11:16', 'Unseen', NULL, '0', '1'),
(122, 'cVLfATPWzM8ma7XRFxekBGoE3YuwSHtU', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Comment', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view policy\" href=\"{{APPURL}}policies-view/0DsMt2GhbKOlVRvqfcmEkN1eUxPWuY8A\">policy</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-15 14:11:16', 'Unseen', NULL, '0', '1'),
(123, 'wEslngB5CDpbKTuR7OPiqxy4Xe6tUA1H', '7S89OC3soyemFhrj24qtP6ifvLzxQK1U', 'Thread Comment', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view claim\" href=\"{{APPURL}}claims-view/gUdRTkt2yQIGV6cMnDO7LqfYejrZ5bEx\">claim</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-15 14:12:31', 'Unseen', NULL, '0', '1'),
(124, 'vgyDqti8BZRhcA3M6TUEkYLeolFmCPrJ', 'BZ7ScjiWnKD0YbwONqleTIUvy3PksGzQ', 'Thread Comment', '<strong>Frank Amoako</strong> left a comment on your <a title=\"Click to view claim\" href=\"{{APPURL}}claims-view/gUdRTkt2yQIGV6cMnDO7LqfYejrZ5bEx\">claim</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-10-15 14:12:31', 'Unseen', NULL, '0', '1'),
(125, 'KZUVOGpHFwiy4r9kSh2zIeEqNP5JoftQ', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'License Renewal Application', '<a title=\"Click to View\" href=\"{{APPURL}}licenses-view/ksus7opxcw3ayeqfdrtuv4jrmm61kfoi\"><strong>Hello Frank Amoako,</strong> your license renewal application is Processing.</a>', NULL, 'system', '7', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', '2020-10-15 15:21:55', 'Unseen', NULL, '0', '1'),
(126, 'EjiKFUWI65O7CSLs2XdhT1ABltnzkNqM', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'Thread Reply', '<strong>National Insurance Commission</strong> left a new reply on your <a title=\"Click to view License Application\" href=\"{{APPURL}}{{RESOURCE_PAGE}}/t2jirqwgnmltkiqjwgemoef0a697oypk\">License Application</a>.', NULL, 'system', '5', 'uIkajswRCXEVr58mg64hxk1fc3efmnva', '2020-10-22 11:23:52', 'Unseen', NULL, '0', '1'),
(127, 'RNjaP6E7c51hYTALOufeMGZ2SDWpwksq', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'Ad Campaign', '<strong>Test Insurance Company</strong> has made a request to cancel your Ad Campaign with ID: <strong>AD000004</strong>. You will be notified once the request is approved.', NULL, 'system', '6', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-11-03 08:49:52', 'Unseen', NULL, '0', '1'),
(128, 'YocLAhEIsHPDnUMKVZ745CtvJg93je2W', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'Cancel Policy', 'Cancel policy request has successfully been reversed.', NULL, 'system', '6', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-11-03 09:04:11', 'Unseen', NULL, '0', '1'),
(129, '6D2hHOX1IkAmYxUzd753tuCvNnwrVopG', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'Ad Campaign', '<strong>Test Insurance Company</strong> has made a request to cancel your Ad Campaign with ID: <strong>AD000004</strong>. You will be notified once the request is approved.', NULL, 'system', '6', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-11-03 09:10:18', 'Unseen', NULL, '0', '1'),
(130, 't7VOa45JUDyRnz2imkocGpjwQ3bKMSBY', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'Cancel Advert', 'Your request to cancel the Advert with ID: <strong>AD000004</strong> have been approved.', NULL, 'system', '6', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-03 09:18:26', 'Unseen', NULL, '0', '1'),
(131, 'st4ScXgnQiHdOY1N62ayZPh3fMReLjTr', 'F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ', 'Thread Reply', '<strong>Test Insurance Company</strong> left a new reply on your <a title=\"Click to view Complaint\" href=\"{{APPURL}}complaints-view/miZcHfKEWdINSu1jVPl4G8RJMpn3Yr5L\">Complaint</a>.', NULL, 'system', '5', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-11-10 09:41:43', 'Unseen', NULL, '0', '1'),
(132, '9lqK52dBIzL74rYQJ1o0aOAMScg8hnwT', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'Ad Campaign', '<strong>Test Insurance Company</strong> has made a request to cancel your Ad Campaign with ID: <strong>AD00005</strong>. You will be notified once the request is approved.', NULL, 'system', '6', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '2020-11-10 22:54:20', 'Unseen', NULL, '0', '1'),
(133, 'Lx5anYckJTG2emr7zFd1vP6BX8b9SRW3', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'Cancel Advert', 'Cancel Advert request has successfully been reversed.', NULL, 'system', '6', '5nI0fZ6wUWzVDEHtOScLiAvl9GKjYXs2', '2020-11-10 22:57:48', 'Unseen', NULL, '0', '1'),
(134, 'gvQCaRplFPce6HAZ58UYmdr9JIujOS7q', '7S89OC3soyemFhrj24qtP6ifvLzxQK1U', 'Policy Claims', '<strong>Hello Fredrick Amoah,</strong> your claim request on the policy <a title=\"Click to View\" href=\"{{APPURL}}claims-view/gUdRTkt2yQIGV6cMnDO7LqfYejrZ5bEx\"><strong>POLDL874892020</strong></a> is Confirmed.', NULL, 'system', '7', '5nI0fZ6wUWzVDEHtOScLiAvl9GKjYXs2', '2020-11-10 22:58:26', 'Unseen', NULL, '0', '1'),
(135, 'iaA79kwtpE4O0me58CbgNWlGSxUc1oMP', '7S89OC3soyemFhrj24qtP6ifvLzxQK1U', 'Policy Claims', '<strong>Hello Fredrick Amoah,</strong> your claim request on the policy <a title=\"Click to View\" href=\"{{APPURL}}claims-view/gUdRTkt2yQIGV6cMnDO7LqfYejrZ5bEx\"><strong>POLDL874892020</strong></a> is Approved.', NULL, 'system', '7', '5nI0fZ6wUWzVDEHtOScLiAvl9GKjYXs2', '2020-11-10 23:00:58', 'Unseen', NULL, '0', '1'),
(136, 'qc52OKxY9nIaNgkR03tJArlG4fijoSyC', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-27 11:07:44', 'Unseen', NULL, '0', '1'),
(137, 'PTGJiLbhoxK9qFcM6d5w1CrNfkzslUuH', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-27 12:25:52', 'Unseen', NULL, '0', '1'),
(138, 'Ecz8yB2D51ZTRrp9Ww7eYiqxO0KLhosN', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-27 12:28:33', 'Unseen', NULL, '0', '1'),
(139, 'I5bfVMezErkgchsi49LGunjt0FZ8QWYK', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-27 12:29:07', 'Unseen', NULL, '0', '1'),
(140, 'nT4mZeV9Y6ji5DG8QqlFWc1zaLyt0ohv', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-27 12:31:13', 'Unseen', NULL, '0', '1'),
(141, '65CexRrJUB0FPLV3vHdZGm7zltK42ojY', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-27 12:31:53', 'Unseen', NULL, '0', '1'),
(142, 'Wb0pXPtLdh2QjvYOUoGIgTfsr8xSacZF', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-27 12:33:53', 'Unseen', NULL, '0', '1'),
(143, 'RmM8YBLHiU53SvoCnPpA0qQhEJOj9TrG', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-27 12:34:19', 'Unseen', NULL, '0', '1'),
(144, 'aXTURLbD12cu5Bw6K9I3VdOPer78fhYN', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-27 12:40:41', 'Unseen', NULL, '0', '1');
INSERT INTO `users_notification` (`id`, `item_id`, `user_id`, `subject`, `message`, `resource_page`, `initiated_by`, `notice_type`, `created_by`, `date_created`, `seen_status`, `seen_date`, `confirmed`, `status`) VALUES
(145, 'tU5kiFfzqNJmOKwLlDaARhT1IPg72MVH', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-27 12:41:11', 'Unseen', NULL, '0', '1'),
(146, 'w9tpecXZ0Adru6yDzhgKIPYVSi2R3n1O', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-27 12:41:33', 'Unseen', NULL, '0', '1'),
(147, 'mGRYKqhadSeALE6PzxZifvBIXk0HD4MU', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-27 12:42:14', 'Unseen', NULL, '0', '1'),
(148, 'iOjJbB2wl1fC79tAkWxQFhLuz84I3doc', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-27 12:42:33', 'Unseen', NULL, '0', '1'),
(149, 'k9gXRp1VsCuP6vjlB2H0yaItFKATnwZN', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-27 12:42:48', 'Unseen', NULL, '0', '1'),
(150, 'ECbqNPmTLdH98zyhX6B0j7J1IQx5DZYo', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-27 12:44:38', 'Unseen', NULL, '0', '1'),
(151, 'JOhqwGud0rijx7VYPF61sUL3lX4ImSN2', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-27 18:56:21', 'Unseen', NULL, '0', '1'),
(152, 'zExC0PNiowl6kgtDQVAp8T4aBOU1IGfR', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-27 18:59:21', 'Unseen', NULL, '0', '1'),
(153, 'B0ZUuR8h6qDdv91AjMxH4CoizTkeYLmf', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-27 19:09:28', 'Unseen', NULL, '0', '1'),
(154, 'B7keImZuMcC83D4Ug09pboGLwQqfVYzR', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-27 19:09:36', 'Unseen', NULL, '0', '1'),
(155, 'H5veKCTBgfDY3trU81GpnSsF6i4RyEAz', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-27 19:10:53', 'Unseen', NULL, '0', '1'),
(156, 'AW7JbNGfYjSx48tO1n5ZsFmdeVHDB0T6', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-27 19:12:43', 'Unseen', NULL, '0', '1'),
(157, 'c0KoVmRYkA5qju4BGHMeiUvDE36JxtdW', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-30 11:48:25', 'Unseen', NULL, '0', '1'),
(158, 'LPqEu57NOyYp2xbvDj84BwCnfUHRe9Qd', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-11 13:06:31', 'Unseen', NULL, '0', '1'),
(159, 'd9KGx3NQoFIPuR241MeaTsEjYDhqmc8A', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-11 13:35:32', 'Unseen', NULL, '0', '1'),
(160, 'udxapBi4l1f8Vb9HDKZcRzLewW0vsgP3', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-11 13:36:09', 'Unseen', NULL, '0', '1'),
(161, '6B7ntNcrxUQE02FTRlCA3VXadyhLe1kq', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-11 17:45:36', 'Unseen', NULL, '0', '1'),
(162, 'e267aFX1KwUSHTWnpR3yDCth8fBsuqA9', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-11 17:48:21', 'Unseen', NULL, '0', '1'),
(163, 'h8TqKS3udHMPUDy0IO9evJRECxboaGFN', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-11 17:49:38', 'Unseen', NULL, '0', '1'),
(164, 'FJOv0Nm2ezsDbScAZaIpHUBTngjuEftG', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-11 17:49:47', 'Unseen', NULL, '0', '1'),
(165, 'bnsy92aojBHk4uMApLRWOtQSxI1eVgE6', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-11 17:51:50', 'Unseen', NULL, '0', '1'),
(166, 'TLbskvSJacIGBWnAU42MzZtQd9yNKHf1', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-11 17:51:55', 'Unseen', NULL, '0', '1'),
(167, 'VifkbSZIpJuTWMXsrmhF8NH02DwozO6d', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-11 17:55:13', 'Unseen', NULL, '0', '1'),
(168, 'gG32A85p0fNrM6RBXYZIcEF1bo4eKaCd', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-11 17:57:50', 'Unseen', NULL, '0', '1'),
(169, 'zygam2c97pnWfMx1HL6wSZNUsRdVDJub', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 'Account Update', '<strong>Admin User Account</strong> updated your account information', NULL, 'system', '9', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-17 23:00:24', 'Unseen', NULL, '0', '1');

-- --------------------------------------------------------

--
-- Table structure for table `users_notification_types`
--

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
(2, 'Policy Expiration', 'policy', 'Moderate', NULL, 'text-warning', '1'),
(3, 'Login Attempts', 'account', 'Very High', 'fa fa-lock', 'text-danger', '1'),
(4, 'Reset Password', 'password', 'High', 'fa fa-lock-open', 'text-danger', '1'),
(5, 'Message', 'message', 'Moderate', 'fa fa-envelope', NULL, '1'),
(6, 'Insurance Policy', 'policy', 'Moderate', 'fa fa-anchor', NULL, '1'),
(7, 'Claims Payment', 'payment', 'Moderate', 'fa fa-weight-hanging', NULL, '1'),
(8, 'Renew License', 'license', 'Moderate', NULL, NULL, '1'),
(9, 'Account Update', 'account', 'Moderate', 'fa fa-user', NULL, '1'),
(10, 'Status Change', 'status-change', 'Moderate', 'fa fa-random', 'text-primary', '1'),
(11, 'Complaint', 'complaint', 'Moderate', NULL, NULL, '1'),
(12, 'Announcement', 'announcement', NULL, 'fa fa-bell', NULL, '1');

-- --------------------------------------------------------

--
-- Table structure for table `users_payments`
--

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

--
-- Dumping data for table `users_payments`
--

INSERT INTO `users_payments` (`id`, `record_type`, `record_id`, `record_details`, `user_id`, `checkout_url`, `initiated_by`, `initiated_medium`, `created_date`, `amount`, `payment_status`, `payment_date`, `payment_option`, `payment_checkout_url`, `payment_info`, `momo_medium`, `transaction_id`, `replies_count`, `comments_count`) VALUES
(1, 'licenses', 'ksus7opxcw3ayeqfdrtuv4jrmm61kfoi', '{\"license_id\":\"LKIKD88993\",\"start_date\":\"2020-10-25\",\"expiry_date\":\"2022-11-05\",\"status\":\"Processing\",\"amount_payable\":\"2000.00\",\"payment_status\":\"Not Paid\",\"date_created\":\"2020-09-20 10:00:08\"}', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'xDZ4B5AbnoaSfOUMqwgkcdmPt18pRY6LWleyujGrQXTiC', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'user', '2020-11-09 08:45:56', 2000.00, 'Pending', NULL, 'payswitch', 'https://test.theteller.net/checkout/checkout/NnJ6eUg1K1pnNzRPeUc2RmJoUWJVUT09Olpqa3lZemxsWkdNNFpXSmlNRFV6TkdOa05qTTBZbVkxWWpReE1EUmhZMlE9', NULL, NULL, '100000000001', 0, 0),
(2, 'adverts', 'Jni74EtRLksw5oO2mPQrHfXvbWeGDhzc', '{\"advert_id\":\"AD00005\",\"advert_title\":\"New Ad Campaign Promotion\",\"image\":\"assets\\/uploads\\/adverts\\/l1cskT5a0jBGHWzbFeQyZrSP2RDM8vg4IdVptiJNoLwE3AfK796OYxm.png\",\"start_date\":\"2020-11-09\",\"end_date\":\"2020-11-27\",\"days\":\"19\",\"status\":\"Processing\",\"ad_objective\":\"POLICY_VIEWS\",\"amount_spent\":\"0.00\",\"date_created\":\"2020-11-03 09:23:05\"}', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'Lnzy3gawG1fck9EMrl4K7HjQRWPdTYoODAvsu0b5tN2VZ', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'user', '2020-11-09 08:46:04', 4.00, 'Paid', '2020-11-09 13:28:39', 'payswitch', 'https://test.theteller.net/checkout/checkout/ZlhGVWtWdXllSWhSZXZiU3QxSnNNQT09Olpqa3lZemxsWkdNNFpXSmlNRFV6TkdOa05qTTBZbVkxWWpReE1EUmhZMlE9', '{\"code\":\"000\",\"status\":\"approved\",\"reason\":\"Approved: Transaction successful!\",\"transaction_id\":\"100000000002\",\"r_switch\":\"MTN\",\"subscriber_number\":\"0550107770\",\"amount\":4,\"currency\":\"GHS\"}', 'MTN', '100000000002', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users_posts`
--

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

--
-- Dumping data for table `users_reset_request`
--

INSERT INTO `users_reset_request` (`id`, `item_id`, `username`, `user_id`, `user_agent`, `token_status`, `request_token`, `reset_date`, `reset_agent`, `expiry_time`) VALUES
(1, 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'emmallob14', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'Chrome Windows 10|::1', 'USED', NULL, '2020-09-19 11:30:23', 'Chrome Windows 10|::1', 1600511423),
(2, 'v8giEcx57KFeUH6zQfMSJlGfa', 'test_admin', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'Chrome Windows 10|::1', 'PENDING', 'v8giEcx57KFeUH6zQfMSJlGfaIhKyrBYwtWVDmuPgkP2VoLXqMNreaTFATRDROq', NULL, NULL, 1600818596);

-- --------------------------------------------------------

--
-- Table structure for table `users_roles`
--

CREATE TABLE `users_roles` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `client_id` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
  `permissions` varchar(5000) CHARACTER SET utf8mb4 DEFAULT NULL,
  `date_logged` datetime NOT NULL DEFAULT current_timestamp(),
  `last_updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_roles`
--

INSERT INTO `users_roles` (`id`, `user_id`, `client_id`, `permissions`, `date_logged`, `last_updated`) VALUES
(34, 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'LKJAFD94R', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"guardian\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"incident\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"department\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"section\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"class\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"course\":{\"view\":1,\"add\":1,\"update\":1,\"lesson\":1,\"delete\":1},\"library\":{\"view\":1,\"request\":1},\"permissions\":{\"view\":1,\"update\":1},\"assignments\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1,\"handin\":1,\"mark\":1}}}', '2020-11-27 00:54:00', NULL),
(35, 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 'LKJAFD94R', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"parent\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1}}}', '2020-11-27 01:04:33', NULL),
(36, 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'LKJAFD94R', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"parent\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1}}}', '2020-11-27 01:06:20', NULL),
(37, 'uIkajsw123456789064hxk1fc3efmnva', 'LKJAFD94R', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"guardian\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"incident\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"department\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"section\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"events\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"class\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"library\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1,\"issue\":1,\"return\":1},\"course\":{\"view\":1,\"add\":1,\"update\":1,\"lesson\":1,\"delete\":1},\"permissions\":{\"view\":1,\"update\":1},\"assignments\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1,\"handin\":1,\"mark\":1}}}', '2020-06-10 12:08:20', NULL),
(38, 'ljg52NfPEsRhvXV3y8aGqUJxtTCn9DwM', 'LKJAFD94R', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"parent\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1}}}', '2020-12-10 17:01:29', NULL),
(41, 'vtIzqjrxDAf5uyegcQ8M7w2dk43XoLpZ', 'LKJAFD94R', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"parent\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1}}}', '2020-12-17 22:47:52', NULL),
(42, 'SZM14dtqDkbfn5cBl0ARgPCj287hym36', 'LKJAFD94R', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"parent\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1}}}', '2020-12-17 22:48:33', NULL),
(43, 'ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v', 'LKJAFD94R', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"parent\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1}}}', '2020-12-17 22:59:40', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_temp_forms`
--

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

CREATE TABLE `users_types` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'GUEST',
  `description` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_permissions` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_types`
--

INSERT INTO `users_types` (`id`, `name`, `description`, `user_permissions`) VALUES
(1, 'STUDENT', 'student', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"parent\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1}}}'),
(2, 'TEACHER', 'teacher', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"parent\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1}}}'),
(3, 'PARENT', 'parent', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"parent\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1}}}'),
(4, 'EMPLOYEE', 'employee', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"staff\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"parent\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1}}}'),
(5, 'ADMIN', 'admin', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"guardian\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"incident\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"department\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"section\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"class\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"course\":{\"view\":1,\"add\":1,\"update\":1,\"lesson\":1,\"delete\":1}}}'),
(6, 'ACCOUNTANT', 'accountant', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"guardian\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"incident\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"department\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"section\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"class\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"course\":{\"view\":1,\"add\":1,\"update\":1,\"lesson\":1,\"delete\":1}}}');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_terms`
--
ALTER TABLE `academic_terms`
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
-- Indexes for table `clients_accounts`
--
ALTER TABLE `clients_accounts`
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
-- Indexes for table `files_attachment`
--
ALTER TABLE `files_attachment`
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
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `table_indexes`
--
ALTER TABLE `table_indexes`
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
-- Indexes for table `users_complaints`
--
ALTER TABLE `users_complaints`
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
-- Indexes for table `users_guardian`
--
ALTER TABLE `users_guardian`
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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_terms`
--
ALTER TABLE `academic_terms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `assignments_answers`
--
ALTER TABLE `assignments_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `assignments_questions`
--
ALTER TABLE `assignments_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `assignments_submitted`
--
ALTER TABLE `assignments_submitted`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `blood_groups`
--
ALTER TABLE `blood_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `books_borrowed`
--
ALTER TABLE `books_borrowed`
  MODIFY `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `books_borrowed_details`
--
ALTER TABLE `books_borrowed_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `books_stock`
--
ALTER TABLE `books_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `books_type`
--
ALTER TABLE `books_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `clients_accounts`
--
ALTER TABLE `clients_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `country`
--
ALTER TABLE `country`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=250;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `courses_plan`
--
ALTER TABLE `courses_plan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `courses_resource_links`
--
ALTER TABLE `courses_resource_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `cron_scheduler`
--
ALTER TABLE `cron_scheduler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `events_types`
--
ALTER TABLE `events_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `files_attachment`
--
ALTER TABLE `files_attachment`
  MODIFY `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `guardian_relation`
--
ALTER TABLE `guardian_relation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `incidents`
--
ALTER TABLE `incidents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `table_indexes`
--
ALTER TABLE `table_indexes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `users_access_attempt`
--
ALTER TABLE `users_access_attempt`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users_activity_logs`
--
ALTER TABLE `users_activity_logs`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=264;

--
-- AUTO_INCREMENT for table `users_api_endpoints`
--
ALTER TABLE `users_api_endpoints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `users_chat`
--
ALTER TABLE `users_chat`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=224;

--
-- AUTO_INCREMENT for table `users_complaints`
--
ALTER TABLE `users_complaints`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users_emails`
--
ALTER TABLE `users_emails`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_feedback`
--
ALTER TABLE `users_feedback`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users_gender`
--
ALTER TABLE `users_gender`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users_guardian`
--
ALTER TABLE `users_guardian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users_login_history`
--
ALTER TABLE `users_login_history`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;

--
-- AUTO_INCREMENT for table `users_messaging_list`
--
ALTER TABLE `users_messaging_list`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users_notification`
--
ALTER TABLE `users_notification`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=170;

--
-- AUTO_INCREMENT for table `users_notification_types`
--
ALTER TABLE `users_notification_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users_payments`
--
ALTER TABLE `users_payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users_posts`
--
ALTER TABLE `users_posts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_reset_request`
--
ALTER TABLE `users_reset_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users_roles`
--
ALTER TABLE `users_roles`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `users_temp_forms`
--
ALTER TABLE `users_temp_forms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_types`
--
ALTER TABLE `users_types`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
