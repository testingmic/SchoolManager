-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 29, 2021 at 01:49 AM
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
  `year_group` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `academic_years`
--

INSERT INTO `academic_years` (`id`, `year_group`) VALUES
(4, '2018/2019'),
(5, '2019/2020'),
(6, '2020/2021'),
(7, '2021/2022');

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

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`id`, `client_id`, `type`, `assignment_type`, `assigned_to`, `assigned_to_list`, `item_id`, `course_tutor`, `department_id`, `course_id`, `class_id`, `grading`, `assignment_title`, `assignment_description`, `date_created`, `created_by`, `due_date`, `due_time`, `state`, `allowed_time`, `date_closed`, `date_updated`, `date_published`, `status`, `deleted`, `academic_year`, `academic_term`, `replies_count`, `comments_count`) VALUES
(1, 'LKJAFD94R', 'Assignment', 'multiple_choice', 'all_students', NULL, 'pPbhxKiCyArNcqvIeoEdV68YmJsL9uOZ', '[\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"0011989GstOi8vMW0zQ2A57nqLJZNkYe\"]', '2', '8u20GszACM7TyFNJctYEDgX9rwp6Oe1a', 'faflkdjaflkdjafd', 40, 'Multiple Test Questions - 26th January 2021', '&lt;div&gt;&lt;!--block--&gt;Greetings in the name of our Lord and Saviour Jesus Christ.&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;Please find attached a Standard Chartered Bank cheque of GHS100.00 as payment for the supply of 20 pieces of sentinel. Please find attached a Standard Chartered Bank cheque of GHS100.00 as payment for the supply of 20 pieces of sentinel. Please find attached a Standard Chartered Bank cheque of GHS100.00 as payment for the supply of 20 pieces of sentinel. Please find attached a Standard Chartered Bank cheque of GHS100.00 as payment for the supply of 20 pieces of sentinel.&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;Thank you&lt;br&gt;&lt;br&gt;&lt;/div&gt;', '2021-01-26 09:43:07', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '2021-02-01', '08:30', 'Answered', '30', NULL, '2021-01-26 10:48:35', '2021-01-26 10:56:10', '1', '0', '2019/2020', '1st', '0', '0'),
(2, 'LKJAFD94R', 'Assignment', 'file_attachment', 'all_students', '', 'jNngSCaicZtfoqDG7HMvKbEpmr6x1ue8', '[\"aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"0011989GstOi8vMW0zQ2A57nqLJZNkYe\",\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\"]', '2', 'BKj7Nv8MXTqsfEQaoZSebY2RCthn4LWG', 'faflkdjaflkdjafd', 30, 'Upload Assignment - 26th January 2021', '&lt;div&gt;&lt;!--block--&gt;Greetings in the name of our Lord and Saviour Jesus Christ.&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;Please find attached a Standard Chartered Bank cheque of GHS100.00 as payment for the supply of 20 pieces of sentinel. Please find attached a Standard Chartered Bank cheque of GHS100.00 as payment for the supply of 20 pieces of sentinel. Please find attached a Standard Chartered Bank cheque of GHS100.00 as payment for the supply of 20 pieces of sentinel. Please find attached a Standard Chartered Bank cheque of GHS100.00 as payment for the supply of 20 pieces of sentinel.&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;Thank you&lt;/div&gt;', '2021-01-26 14:54:23', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '2021-02-01', '09:00', 'Graded', '30', NULL, NULL, NULL, '1', '0', '2019/2020', '1st', '0', '0');

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

--
-- Dumping data for table `assignments_answers`
--

INSERT INTO `assignments_answers` (`id`, `client_id`, `assignment_id`, `student_id`, `answers`, `scores`) VALUES
(1, 'LKJAFD94R', 'pPbhxKiCyArNcqvIeoEdV68YmJsL9uOZ', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', '[{\"question_id\":\"f0gBJV3RP257HD6yKbq8aZXxML9wOdvp\",\"answer\":\"option_c\",\"assigned_mark\":\"4\",\"date_answered\":\"2021-01-26 11:21AM\",\"status\":\"wrong\"},{\"question_id\":\"DbSKqaT8PI3eYJCxyAZonk9Bcf0RsO75\",\"answer\":\"option_c\",\"assigned_mark\":\"3\",\"date_answered\":\"2021-01-26 11:21AM\",\"status\":\"correct\"},{\"question_id\":\"EP46sSqiDdx0XhQeVKbWu3Oav7yYocAL\",\"answer\":\"option_a\",\"assigned_mark\":\"5\",\"date_answered\":\"2021-01-26 11:21AM\",\"status\":\"wrong\"},{\"question_id\":\"fwlGgduQYEFJL7A3PvxyjtsRb0XiKzVO\",\"answer\":\"option_a\",\"assigned_mark\":\"3\",\"date_answered\":\"2021-01-26 11:21AM\",\"status\":\"wrong\"},{\"question_id\":\"cXKbe4Zn5Jy7gkazIvNS3j2rDstqoV8Q\",\"answer\":\"option_c\",\"assigned_mark\":\"4\",\"date_answered\":\"2021-01-26 11:21AM\",\"status\":\"wrong\"},{\"question_id\":\"B6MVdfx28gpsekwtWI41LHKnQi5uUDXC\",\"answer\":\"option_c\",\"assigned_mark\":\"4\",\"date_answered\":\"2021-01-26 11:21AM\",\"status\":\"correct\"},{\"question_id\":\"tQLa0hJ1BNunRz2MdIKDg6AjW4mxlowc\",\"answer\":\"option_b\",\"assigned_mark\":\"5\",\"date_answered\":\"2021-01-26 11:21AM\",\"status\":\"correct\"},{\"question_id\":\"z1pZIwG2J0e3fkbvAD8Ey9QWjVRNUrxu\",\"answer\":\"option_d\",\"assigned_mark\":\"7\",\"date_answered\":\"2021-01-26 11:21AM\",\"status\":\"correct\"},{\"question_id\":\"GARViYoJjPBngqISkX9b8Ea7FufvMemw\",\"answer\":\"option_b\",\"assigned_mark\":\"3\",\"date_answered\":\"2021-01-26 11:23AM\",\"status\":\"correct\"},{\"question_id\":\"dLPUeNguHfrszi3An8QMjCT675cVZXS0\",\"answer\":\"option_c\",\"assigned_mark\":\"2\",\"date_answered\":\"2021-01-26 11:47AM\",\"status\":\"wrong\"}]', '22');

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

--
-- Dumping data for table `assignments_questions`
--

INSERT INTO `assignments_questions` (`id`, `client_id`, `item_id`, `assignment_id`, `question`, `difficulty`, `option_a`, `option_b`, `option_c`, `option_d`, `option_e`, `option_f`, `answer_type`, `created_by`, `correct_answer`, `marks`, `correct_answer_description`, `attempted_by`, `current_state`, `date_created`, `deleted`) VALUES
(1, 'LKJAFD94R', 'f0gBJV3RP257HD6yKbq8aZXxML9wOdvp', 'pPbhxKiCyArNcqvIeoEdV68YmJsL9uOZ', 'The question was not added to the information here', 'easy', 'First Question', 'Another Here', 'Yes cool answer', 'Good to go', 'Thats perfect', NULL, 'option', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'option_b', '4', NULL, NULL, 'Published', '2021-01-26 09:48:23', '0'),
(2, 'LKJAFD94R', 'DbSKqaT8PI3eYJCxyAZonk9Bcf0RsO75', 'pPbhxKiCyArNcqvIeoEdV68YmJsL9uOZ', 'Integration of Service API’s into web applications (Payment Systems / SMS’s API’s). Development of Analytics tools using Facebook, Twitter, Instagram and LinkedIn Developer Api’s.', 'easy', 'Online Booking System', 'Managers where users can book for a', 'A web application suitable for Event', 'particular seat for a particular event', 'Voting Collation System', NULL, 'option', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'option_c', '3', NULL, NULL, 'Published', '2021-01-26 09:55:53', '0'),
(3, 'LKJAFD94R', 'EP46sSqiDdx0XhQeVKbWu3Oav7yYocAL', 'pPbhxKiCyArNcqvIeoEdV68YmJsL9uOZ', 'Used YouTube channels like (Adam Khoury, Traversy, Eli the Computer Guy) to learn my preferred programing languages was through', 'easy', 'been engaged in the development of', 'web applications and database', 'management applications over the years', NULL, NULL, NULL, 'option', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'option_b', '5', NULL, NULL, 'Published', '2021-01-26 09:57:17', '0'),
(4, 'LKJAFD94R', 'fwlGgduQYEFJL7A3PvxyjtsRb0XiKzVO', 'pPbhxKiCyArNcqvIeoEdV68YmJsL9uOZ', 'Write well designed, testable, efficient code by using best software development practices.', 'easy', 'Good answers', 'deserves', 'a good treat', 'henceforth do the right thing', 'as i also engage in the best thing', NULL, 'option', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'option_c', '3', NULL, NULL, 'Published', '2021-01-26 10:01:32', '0'),
(5, 'LKJAFD94R', 'cXKbe4Zn5Jy7gkazIvNS3j2rDstqoV8Q', 'pPbhxKiCyArNcqvIeoEdV68YmJsL9uOZ', 'Create website layout/user interface by using standard HTML5 / CSS practices', 'easy', 'This project', 'has indeed', 'thought me to be', 'very circumspective', 'before its good to persevere', NULL, 'option', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'option_d', '4', NULL, NULL, 'Published', '2021-01-26 10:04:09', '0'),
(6, 'LKJAFD94R', 'B6MVdfx28gpsekwtWI41LHKnQi5uUDXC', 'pPbhxKiCyArNcqvIeoEdV68YmJsL9uOZ', 'Adding of questions is quiet a difficult task to do', 'medium', 'As i said today', 'I am confident', 'to have the best', 'choice of programming techniques', NULL, NULL, 'option', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'option_c', '4', NULL, NULL, 'Published', '2021-01-26 10:07:59', '0'),
(7, 'LKJAFD94R', 'tQLa0hJ1BNunRz2MdIKDg6AjW4mxlowc', 'pPbhxKiCyArNcqvIeoEdV68YmJsL9uOZ', 'I am doing great.', 'medium', 'This is getting awesome', 'I am sure this will get really better soon', 'indeed programming', 'has never been easy', 'and i am sure we will surely get there', NULL, 'option', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'option_b', '5', NULL, NULL, 'Published', '2021-01-26 10:09:34', '0'),
(8, 'LKJAFD94R', 'z1pZIwG2J0e3fkbvAD8Ey9QWjVRNUrxu', 'pPbhxKiCyArNcqvIeoEdV68YmJsL9uOZ', 'Moving to the next step in computing', 'easy', 'I want to get the last marks ready', 'Testing the marking scheme', 'And ensure that we check for all errors in the page', 'Correct answer is this one.', NULL, NULL, 'option', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'option_d', '7', NULL, NULL, 'Published', '2021-01-26 10:40:44', '0'),
(9, 'LKJAFD94R', 'GARViYoJjPBngqISkX9b8Ea7FufvMemw', 'pPbhxKiCyArNcqvIeoEdV68YmJsL9uOZ', 'Last but one question to add to the database', 'medium', 'False', 'True', 'Both of them', 'None is true and false', NULL, NULL, 'option', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'option_b', '3', NULL, NULL, 'Published', '2021-01-26 10:41:28', '0'),
(10, 'LKJAFD94R', 'dLPUeNguHfrszi3An8QMjCT675cVZXS0', 'pPbhxKiCyArNcqvIeoEdV68YmJsL9uOZ', 'This is the last question. The last option is the answer', 'easy', 'I am enjoying what i am doing', 'because it is working', 'as i expect it to work', 'ensure the best of programming', 'and checking for efficiency.', NULL, 'option', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'option_e', '2', NULL, NULL, 'Published', '2021-01-26 10:43:17', '0');

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

--
-- Dumping data for table `assignments_submitted`
--

INSERT INTO `assignments_submitted` (`id`, `client_id`, `assignment_id`, `student_id`, `score`, `graded`, `handed_in`, `date_submitted`, `date_graded`) VALUES
(1, 'LKJAFD94R', 'pPbhxKiCyArNcqvIeoEdV68YmJsL9uOZ', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', '22', '1', 'Submitted', '2021-01-26 11:47:42', '2021-01-26 11:47:42'),
(2, 'LKJAFD94R', 'jNngSCaicZtfoqDG7HMvKbEpmr6x1ue8', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', '25', '1', 'Submitted', '2021-01-26 15:17:13', '2021-01-26 17:03:13');

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

--
-- Dumping data for table `books_borrowed`
--

INSERT INTO `books_borrowed` (`id`, `client_id`, `the_type`, `item_id`, `user_id`, `user_role`, `books_id`, `issued_date`, `return_date`, `fine`, `actual_paid`, `fine_paid`, `status`, `created_at`, `issued_by`, `actual_date_returned`, `updated_at`, `deleted`) VALUES
(1, 'LKJAFD94R', 'issued', 'iaym012xVA7YSHq8FgOr6DszpnQkWhlo', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 'student', '[\"afdghhghghg\",12223444443]', '2021-01-05', '2021-01-12', '34', '0.00', '0', 'Returned', '2021-01-05 19:00:27', 'uIkajsw123456789064hxk1fc', '2021-01-07 20:24:54', '2021-01-05 19:00:27', '0'),
(2, 'LKJAFD94R', 'issued', '0QJ4OYwMPFGfED7laTdAZxzUjCR6v29u', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'teacher', '[\"lkjlajfdk454545k\",\"afdafdafd343434\",\"afdghhghghg\",12223444443]', '2021-01-05', '2021-01-12', '45', '0.00', '0', 'Returned', '2021-01-05 19:04:07', 'uIkajsw123456789064hxk1fc', '2021-01-07 19:27:39', '2021-01-05 19:04:07', '0'),
(4, 'LKJAFD94R', 'requested', 'jJnVM2SimOsZDFla4UAkfHg0cyRwuQ9x', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'teacher', '[\"afdghhghghg\",12223444443]', '2021-01-07', '2021-01-12', '35', '0.00', '0', 'Returned', '2021-01-05 22:28:39', 'a6ImKRhGstOi8vMW0zQ2A57nq', '2021-01-07 20:26:23', '2021-01-05 22:28:39', '0'),
(5, 'LKJAFD94R', 'request', 'jvdLMH89PiWZhk2nuXOS74qCBRYoFg3a', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'student', '[\"lkjlajfdk454545k\",\"afdghhghghg\"]', '2021-01-22', '2021-01-29', '29', '0.00', '0', 'Returned', '2021-01-22 16:09:21', '5Jz7Lv8aZ1Gi43MxDXBlfqRn9', '2021-01-22 16:34:11', '2021-01-22 16:09:21', '0'),
(6, 'LKJAFD94R', 'request', 'cAEs9mkCL8o5ShItQMRfyaTPjWilYbwe', 'SZM14dtqDkbfn5cBl0ARgPCj287hym36', 'student', '[\"afdghhghghg\",12223444443]', NULL, '2021-01-29', '0', '0.00', '0', 'Cancelled', '2021-01-22 16:14:36', '5Jz7Lv8aZ1Gi43MxDXBlfqRn9', NULL, '2021-01-22 16:14:36', '0'),
(7, 'LKJAFD94R', 'request', 'sLpW0YEMuofNg1xJyPzHZ63kRiOjtCch', 'SZM14dtqDkbfn5cBl0ARgPCj287hym36', 'student', '[\"lkjlajfdk454545k\"]', '2021-01-22', '2021-01-29', '23', '0.00', '0', 'Approved', '2021-01-22 16:16:35', '5Jz7Lv8aZ1Gi43MxDXBlfqRn9', NULL, '2021-01-22 16:16:35', '0'),
(8, 'LKJAFD94R', 'request', 'Fe9orzmpIQJNWhDbRV0T7XB4kd3YE8Ax', 'SZM14dtqDkbfn5cBl0ARgPCj287hym36', 'student', '[\"afdafdafd343434\"]', NULL, '2021-01-21', '0', '0.00', '0', 'Requested', '2021-01-22 16:16:53', '5Jz7Lv8aZ1Gi43MxDXBlfqRn9', NULL, '2021-01-22 16:16:53', '0'),
(9, 'LKJAFD94R', 'request', 'dG7qrhKA2fS0oFJWtTQOmsEkVYXxU3zC', 'kXTrgfmRsnUY13VD0LAB9ZiGuKzP46EQ', 'student', '[\"afdghhghghg\"]', '2021-01-22', '2021-01-29', '0', '0.00', '0', 'Approved', '2021-01-22 17:38:19', '5Jz7Lv8aZ1Gi43MxDXBlfqRn9', NULL, '2021-01-22 17:38:19', '0');

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

--
-- Dumping data for table `books_borrowed_details`
--

INSERT INTO `books_borrowed_details` (`id`, `borrowed_id`, `book_id`, `date_borrowed`, `return_date`, `quantity`, `fine`, `actual_paid`, `fine_paid`, `issued_by`, `received_by`, `actual_date_returned`, `status`, `deleted`) VALUES
(1, 'iaym012xVA7YSHq8FgOr6DszpnQkWhlo', 'afdghhghghg', '2021-01-05 19:00:27', '2021-01-12', 1, '17.00', '0.00', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', '2021-01-07 20:24:54', 'Returned', '0'),
(2, 'iaym012xVA7YSHq8FgOr6DszpnQkWhlo', '12223444443', '2021-01-05 19:00:27', '2021-01-12', 1, '17.00', '0.00', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', '2021-01-07 20:24:54', 'Returned', '0'),
(3, '0QJ4OYwMPFGfED7laTdAZxzUjCR6v29u', 'lkjlajfdk454545k', '2021-01-05 19:04:07', '2021-01-12', 4, '11.25', '0.00', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '2021-01-07 19:27:39', 'Returned', '0'),
(4, '0QJ4OYwMPFGfED7laTdAZxzUjCR6v29u', 'afdafdafd343434', '2021-01-05 19:04:07', '2021-01-12', 3, '11.25', '0.00', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '2021-01-07 19:27:39', 'Returned', '0'),
(5, '0QJ4OYwMPFGfED7laTdAZxzUjCR6v29u', 'afdghhghghg', '2021-01-05 19:04:07', '2021-01-12', 3, '11.25', '0.00', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '2021-01-07 19:27:39', 'Returned', '0'),
(6, '0QJ4OYwMPFGfED7laTdAZxzUjCR6v29u', '12223444443', '2021-01-05 19:04:07', '2021-01-12', 3, '11.25', '0.00', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '2021-01-07 19:27:39', 'Returned', '0'),
(9, 'jJnVM2SimOsZDFla4UAkfHg0cyRwuQ9x', 'afdghhghghg', '2021-01-05 22:28:39', '2021-01-12', 4, '17.50', '0.00', '0', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '2021-01-07 20:26:23', 'Returned', '0'),
(10, 'jJnVM2SimOsZDFla4UAkfHg0cyRwuQ9x', '12223444443', '2021-01-05 22:28:39', '2021-01-12', 3, '17.50', '0.00', '0', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '2021-01-07 20:26:23', 'Returned', '0'),
(11, 'jvdLMH89PiWZhk2nuXOS74qCBRYoFg3a', 'lkjlajfdk454545k', '2021-01-22 16:09:21', '2021-01-29', 2, '14.50', '0.00', '0', '5Jz7Lv8aZ1Gi43MxDXBlfqRn9uYQjIEc', 'SZMsssqcccfn5cBl0aaaPCj287hym36', '2021-01-22 16:34:11', 'Returned', '0'),
(12, 'jvdLMH89PiWZhk2nuXOS74qCBRYoFg3a', 'afdghhghghg', '2021-01-22 16:09:21', '2021-01-29', 4, '14.50', '0.00', '0', '5Jz7Lv8aZ1Gi43MxDXBlfqRn9uYQjIEc', 'SZMsssqcccfn5cBl0aaaPCj287hym36', '2021-01-22 16:34:11', 'Returned', '0'),
(13, 'cAEs9mkCL8o5ShItQMRfyaTPjWilYbwe', 'afdghhghghg', '2021-01-22 16:14:36', '2021-01-29', 4, '0.00', '0.00', '0', '5Jz7Lv8aZ1Gi43MxDXBlfqRn9uYQjIEc', 'SZM14dtqDkbfn5cBl0ARgPCj287hym36', NULL, 'Borrowed', '0'),
(14, 'cAEs9mkCL8o5ShItQMRfyaTPjWilYbwe', '12223444443', '2021-01-22 16:14:36', '2021-01-29', 4, '0.00', '0.00', '0', '5Jz7Lv8aZ1Gi43MxDXBlfqRn9uYQjIEc', 'SZM14dtqDkbfn5cBl0ARgPCj287hym36', NULL, 'Borrowed', '0'),
(15, 'sLpW0YEMuofNg1xJyPzHZ63kRiOjtCch', 'lkjlajfdk454545k', '2021-01-22 16:16:35', '2021-01-29', 3, '23.00', '0.00', '0', '5Jz7Lv8aZ1Gi43MxDXBlfqRn9uYQjIEc', 'SZM14dtqDkbfn5cBl0ARgPCj287hym36', NULL, 'Borrowed', '0'),
(16, 'Fe9orzmpIQJNWhDbRV0T7XB4kd3YE8Ax', 'afdafdafd343434', '2021-01-22 16:16:53', '2021-01-21', 3, '0.00', '0.00', '0', '5Jz7Lv8aZ1Gi43MxDXBlfqRn9uYQjIEc', 'SZM14dtqDkbfn5cBl0ARgPCj287hym36', NULL, 'Borrowed', '0'),
(17, 'dG7qrhKA2fS0oFJWtTQOmsEkVYXxU3zC', 'afdghhghghg', '2021-01-22 17:38:19', '2021-01-29', 3, '0.00', '0.00', '0', '5Jz7Lv8aZ1Gi43MxDXBlfqRn9uYQjIEc', 'kXTrgfmRsnUY13VD0LAB9ZiGuKzP46EQ', NULL, 'Borrowed', '0');

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

--
-- Dumping data for table `books_stock`
--

INSERT INTO `books_stock` (`id`, `books_id`, `quantity`) VALUES
(1, '12223444443', 89),
(2, 'afdafdafd343434', 93),
(3, 'afdghhghghg', 31),
(4, 'lkjlajfdk454545k', 33);

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
(1, NULL, 'fadafafdadaf', 'LKJAFD94R', 'GENERAL ARTS 1', NULL, 'CL00020', NULL, '[\"8u20GszACM7TyFNJctYEDgX9rwp6Oe1a\",\"afdafdafdafd\",\"BKj7Nv8MXTqsfEQaoZSebY2RCthn4LWG\",\"fT6zyKmjYLHXVsSte5cpGuQIMNdOrxW1\",\"SqAtOldUajW5YGH7XyhRLP3Fi4f1QK8v\"]', '[\"IGFbSMJRBrfCqjp6PymQKkix2w8AsNOn\",\"FEBWu0eKzDihZGrxCYj29QJt6nUb3fvd\",\"lkajflajfakfaljfkaf\"]', NULL, '2', '', NULL, 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', '1', NULL, NULL, '2020-11-27 21:49:50', '2020-11-30 11:54:53'),
(2, NULL, 'faflkdjaflkdjafd', 'LKJAFD94R', 'GENERAL ARTS 2', NULL, 'GFCKJH', NULL, '[\"8u20GszACM7TyFNJctYEDgX9rwp6Oe1a\",\"afdafdafdafd\",\"SqAtOldUajW5YGH7XyhRLP3Fi4f1QK8v\",\"BKj7Nv8MXTqsfEQaoZSebY2RCthn4LWG\"]', '[\"FEBWu0eKzDihZGrxCYj29QJt6nUb3fvd\",\"lkajflajfakfaljfkaf\"]', NULL, '2', '', NULL, 'null', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', '1', NULL, NULL, '2020-11-27 21:49:50', '2020-12-11 22:06:14'),
(3, NULL, 'erere454545', 'LKJAFD94R', 'SCIENCE 3', NULL, NULL, NULL, '', NULL, NULL, '1', '', NULL, NULL, NULL, '0', NULL, NULL, '2020-11-27 21:49:50', '2020-11-28 09:33:29'),
(4, NULL, 'afdaflkjdflajkfd', 'LKJAFD94R', 'VISUAL ARTS D', NULL, 'CL00004', 26, '[\"afdafdafdafd\",\"fT6zyKmjYLHXVsSte5cpGuQIMNdOrxW1\"]', '[\"FEBWu0eKzDihZGrxCYj29QJt6nUb3fvd\",\"IGFbSMJRBrfCqjp6PymQKkix2w8AsNOn\"]', 5, '3', '2019/2020', '1st', NULL, NULL, '1', NULL, NULL, '2020-11-27 21:49:50', '2021-01-22 17:06:48'),
(5, NULL, 'aafafafafafafdaf', 'LKJAFD94R', '2 HC 2', NULL, 'TESTCODE', NULL, '[\"afdafdafdafd\"]', NULL, NULL, '2', '', NULL, 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', '1', NULL, 'this is the class information that i am updating here... indeed it will be great', '2020-11-27 21:49:50', '2020-11-28 09:39:16'),
(7, NULL, 'fdfafdfdafdafdfa', 'LKJAFD94R', 'CLASS 1', NULL, NULL, NULL, '', '[\"FEBWu0eKzDihZGrxCYj29QJt6nUb3fvd\"]', NULL, '0', '', NULL, '', NULL, '1', NULL, NULL, '2020-11-27 21:49:50', '2020-11-28 09:33:29'),
(8, NULL, 'fdfdfdfdfd', 'LKJAFD94R', 'CLASS 2', NULL, 'CL00008', 0, '[\"BKj7Nv8MXTqsfEQaoZSebY2RCthn4LWG\"]', '[\"lkajflajfakfaljfkaf\"]', 0, '5', '2019/2020', '1st', '', '', '1', NULL, 'This is a test description', '2020-11-27 21:49:50', '2021-01-22 14:12:08'),
(9, NULL, 'fdadadaffd', 'LKJAFD94R', 'CLASS 3', NULL, NULL, NULL, '[\"fT6zyKmjYLHXVsSte5cpGuQIMNdOrxW1\"]', '[\"IGFbSMJRBrfCqjp6PymQKkix2w8AsNOn\",\"FEBWu0eKzDihZGrxCYj29QJt6nUb3fvd\"]', NULL, '0', '', NULL, '', NULL, '1', NULL, NULL, '2020-11-27 21:49:50', '2020-11-28 09:33:29'),
(10, NULL, 'afdfdfd', 'LKJAFD94R', 'CLASS 4', NULL, NULL, NULL, '', NULL, NULL, '0', '', NULL, '', NULL, '1', NULL, NULL, '2020-11-27 21:49:50', '2020-11-28 09:33:29'),
(11, NULL, 'fdfdfaddfdfdad545454', 'LKJAFD94R', 'CLASS 5', NULL, 'CL00020', NULL, '', NULL, NULL, 'null', '', NULL, 'null', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', '1', NULL, NULL, '2020-11-27 21:49:50', '2020-11-30 11:55:12'),
(12, NULL, 'fdfdfadad545454', 'LKJAFD94R', 'CLASS 6', NULL, NULL, NULL, '[\"8u20GszACM7TyFNJctYEDgX9rwp6Oe1a\"]', NULL, NULL, '0', '', NULL, '', NULL, '1', NULL, NULL, '2020-11-27 21:49:50', '2020-11-28 09:33:29'),
(13, NULL, 'afdfadfdjhj78787', 'LKJAFD94R', 'JHS 1', NULL, 'CL00013', NULL, '[\"fT6zyKmjYLHXVsSte5cpGuQIMNdOrxW1\"]', NULL, NULL, 'null', '', NULL, 'null', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', '1', NULL, NULL, '2020-11-27 21:49:50', '2020-11-30 11:57:00'),
(14, NULL, 'fafdf7878787', 'LKJAFD94R', 'JHS 2', NULL, NULL, NULL, '', '[\"IGFbSMJRBrfCqjp6PymQKkix2w8AsNOn\",\"FEBWu0eKzDihZGrxCYj29QJt6nUb3fvd\"]', NULL, '0', '', NULL, '', NULL, '1', NULL, NULL, '2020-11-27 21:49:50', '2020-11-28 09:33:29'),
(15, NULL, 'afdfd2323232', 'LKJAFD94R', 'JHS 3', NULL, NULL, NULL, '[\"8u20GszACM7TyFNJctYEDgX9rwp6Oe1a\",\"SqAtOldUajW5YGH7XyhRLP3Fi4f1QK8v\"]', NULL, NULL, '0', '', NULL, '', NULL, '1', NULL, NULL, '2020-11-27 21:49:50', '2020-11-28 09:33:29'),
(16, NULL, 'aaaadd444545454', 'LKJAFD94R', 'CLASS 6', NULL, NULL, NULL, '[\"8u20GszACM7TyFNJctYEDgX9rwp6Oe1a\",\"SqAtOldUajW5YGH7XyhRLP3Fi4f1QK8v\"]', NULL, NULL, '0', '', NULL, 'OY550107772', NULL, '1', NULL, NULL, '2020-11-27 21:49:50', '2020-11-28 09:33:29');

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

--
-- Dumping data for table `classes_rooms`
--

INSERT INTO `classes_rooms` (`item_id`, `client_id`, `code`, `name`, `capacity`, `description`, `classes_list`, `status`) VALUES
('FEBWu0eKzDihZGrxCYj29QJt6nUb3fvd', 'LKJAFD94R', 'JAKS', 'Social Sciences Theater', '300', NULL, '[\"fadafafdadaf\",\"fdfafdfdafdafdfa\",\"fafdf7878787\",\"afdaflkjdflajkfd\"]', '1'),
('IGFbSMJRBrfCqjp6PymQKkix2w8AsNOn', 'LKJAFD94R', 'LKFD98', 'Science Main Lecture Theater', '230', NULL, '[\"fadafafdadaf\",\"afdaflkjdflajkfd\",\"fdadadaffd\",\"fafdf7878787\"]', '1'),
('lkajflajfakfaljfkaf', 'LKJAFD94R', 'JIALD', 'North East Class Room', '200', NULL, '[\"fadafafdadaf\",\"faflkdjaflkdjafd\",\"fdfdfdfdfd\"]', '1');

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
  `client_address` varchar(255) DEFAULT NULL,
  `client_email` varchar(255) DEFAULT NULL,
  `client_website` varchar(245) DEFAULT NULL,
  `client_logo` varchar(255) DEFAULT NULL,
  `client_location` varchar(255) DEFAULT NULL,
  `client_category` varchar(64) DEFAULT NULL,
  `client_preferences` varchar(5000) DEFAULT NULL,
  `client_status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `clients_accounts`
--

INSERT INTO `clients_accounts` (`id`, `client_id`, `client_name`, `client_contact`, `client_address`, `client_email`, `client_website`, `client_logo`, `client_location`, `client_category`, `client_preferences`, `client_status`) VALUES
(1, 'LKJAFD94R', 'Test Client Account Updated', '0550107770', 'East Cantonments Address, Accra', 'emmallob14@gmail.com', 'https://testwebsite.com', NULL, 'Dodowa', NULL, '{\"academics\":{\"academic_year\":\"2019\\/2020\",\"academic_term\":\"1st\",\"term_starts\":\"2021-01-24\",\"term_ends\":\"2021-01-24\"},\"labels\":{\"student_label\":\"AGL\",\"parent_label\":\"PA\",\"teacher_label\":\"tr\",\"staff_label\":\"STF\",\"course_label\":\"COR\",\"book_label\":\"bkl\",\"class_label\":\"CL\",\"department_label\":\"DEP\",\"section_label\":\"SEC\",\"receipt_label\":\"rec\",\"currency\":\"USD\"},\"opening_days\":[\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\",\"Friday\"]}', '1');

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

INSERT INTO `courses` (`id`, `upload_id`, `item_id`, `client_id`, `course_code`, `credit_hours`, `academic_term`, `academic_year`, `department_id`, `programme_id`, `weekly_meeting`, `class_id`, `name`, `slug`, `course_tutor`, `description`, `date_created`, `created_by`, `date_updated`, `status`, `deleted`) VALUES
(2, NULL, 'afdafdafdafd', 'LKJAFD94R', 'COR002', '4', '1st', '2019/2020', NULL, '3', 7, '[\"fadafafdadaf\",\"faflkdjaflkdjafd\",\"afdaflkjdflajkfd\",\"aafafafafafafdaf\"]', 'Principles of Arts', 'principles-of-arts', '[\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"9898989GstOi8vMW0zQ2A57nqLJZNkYe\"]', 'Updating the course information is really awesome... i am loving this informaton style', NULL, NULL, '2021-01-22 12:03:56', '1', '0'),
(29, NULL, '8u20GszACM7TyFNJctYEDgX9rwp6Oe1a', 'LKJAFD94R', 'COR029', '3', '1st', '2019/2020', NULL, NULL, 7, '[\"fadafafdadaf\",\"faflkdjaflkdjafd\",\"fdfdfadad545454\",\"afdfd2323232\",\"aaaadd444545454\"]', 'The Concept of Reproduction', 'the-concept-of-reproduction', '[\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"0011989GstOi8vMW0zQ2A57nqLJZNkYe\"]', 'This is the course for the concept of reproduction that we want to upload into the system.', '2020-12-22', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-22 11:48:23', '1', '0'),
(30, NULL, 'SqAtOldUajW5YGH7XyhRLP3Fi4f1QK8v', 'LKJAFD94R', 'JAIFD', '34', '1st', '2019/2020', NULL, NULL, 7, '[\"fadafafdadaf\",\"faflkdjaflkdjafd\",\"afdfd2323232\",\"aaaadd444545454\"]', 'Introduction to Machine Learning', 'introduction-to-machine-learning', '[\"aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"0011989GstOi8vMW0zQ2A57nqLJZNkYe\"]', 'This is an introduction to the jquery programming language.', '2021-01-22', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-23 11:59:01', '1', '0'),
(31, NULL, 'BKj7Nv8MXTqsfEQaoZSebY2RCthn4LWG', 'LKJAFD94R', 'ADPHP', '3', '1st', '2019/2020', NULL, NULL, 10, '[\"fadafafdadaf\",\"faflkdjaflkdjafd\",\"fdfdfdfdfd\"]', 'Advanced PHP Programming', 'advanced-php-programming', '[\"aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"0011989GstOi8vMW0zQ2A57nqLJZNkYe\"]', 'This is the Advanced PHP Programming Course', '2021-01-23', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-23 11:44:42', '1', '0'),
(32, NULL, 'fT6zyKmjYLHXVsSte5cpGuQIMNdOrxW1', 'LKJAFD94R', 'JSA90', '3', '1st', '2019/2020', NULL, NULL, 6, '[\"fadafafdadaf\",\"afdaflkjdflajkfd\",\"fdadadaffd\",\"afdfadfdjhj78787\"]', 'JavaScript & jQuery Programming', 'javascript-jquery-programming', '[\"aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"0011989GstOi8vMW0zQ2A57nqLJZNkYe\"]', 'This course deals with JavaScript and jQuery programming.', '2021-01-23', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-23 11:54:11', '1', '0');

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
(2, 'SELECT item_id AS user_id, name FROM users WHERE (user_type=\"user\" OR user_type=\"business\") AND deleted=\"0\" AND status=\"1\"', 'HU72tZ3lysP0Ag9Y5ehRqO4uCzMmvocp', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '12', 'Announcement', 'notification', '1', '2020-10-13 06:00:00', '2020-10-13 15:24:37', '2020-10-13 15:44:44'),
(3, '[{\"user_id\":\"F3lDCq0wHJ71smAKdbnOEphIeuo9tfRQ\",\"email\":\"priscilla_appiah@obeng.com\",\"name\":\"Priscilla Appiah\"},{\"user_id\":\"sgHvi29tuJakdfzmp71nowNlWr40BKDV\",\"email\":\"revsolo@mail.com\",\"name\":\"Solomon Kwarteng\"}]', '2EMKgph0O93bjkQRLmnqUAIylBJD1arT', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '5', 'Email Message', 'email', '1', '2020-10-13 16:02:14', '2020-10-13 16:02:14', '2020-10-13 16:15:12'),
(4, '[{\"user_id\":\"sgHvi29tuJakdfzmp71nowNlWr40BKDV\",\"email\":\"revsolo@mail.com\",\"fullname\":\"Solomon Kwarteng\"},{\"user_id\":\"uIkajswRCXEVr58mg64hxk1fc3efmnva\",\"email\":\"frankamoako@gmail.com\",\"fullname\":\"National Insurance Commission\"}]', '7xTeIEKY1bGjZX0RAs8iypdL45z6BtVh', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '5', 'Email Message', 'email', '0', '2020-10-20 20:39:07', '2020-10-20 20:39:07', NULL),
(5, '[{\"user_id\":\"sgHvi29tuJakdfzmp71nowNlWr40BKDV\",\"email\":\"revsolo@mail.com\",\"fullname\":\"Solomon Kwarteng\"}]', 'u0cnFdkDIH825p6V3b4yENZjAhPJiomY', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', '5', 'Email Message', 'email', '0', '2020-10-22 10:36:26', '2020-10-22 10:36:26', NULL);

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
(3, 'GBP'),
(4, 'SWF'),
(5, 'YEN');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
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

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `client_id`, `upload_id`, `department_code`, `name`, `slug`, `image`, `description`, `department_head`, `status`, `created_by`, `date_created`, `date_updated`) VALUES
(1, 'LKJAFD94R', NULL, NULL, 'Blue', NULL, 'assets/img/placeholder.jpg', NULL, NULL, '1', NULL, '2020-11-28 09:34:20', '2020-11-28 09:34:20'),
(2, 'LKJAFD94R', NULL, NULL, 'First Department Name', NULL, 'assets/img/placeholder.jpg', NULL, NULL, '1', NULL, '2020-11-28 09:34:20', '2020-11-28 09:34:20'),
(3, 'LKJAFD94R', NULL, NULL, 'Green', NULL, 'assets/img/placeholder.jpg', NULL, NULL, '1', NULL, '2020-11-28 09:34:20', '2020-11-28 09:34:20'),
(4, 'LKJAFD94R', NULL, NULL, 'Yellow', NULL, 'assets/img/placeholder.jpg', NULL, NULL, '1', NULL, '2020-11-28 09:34:20', '2020-11-28 09:34:20'),
(5, 'LKJAFD94R', NULL, 'DEPA', 'Pink', NULL, 'assets/img/placeholder.jpg', 'this is the department update processing', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '1', NULL, '2020-11-28 09:34:20', '2020-11-28 09:50:46'),
(6, 'LKJAFD94R', NULL, NULL, 'O- Edited', NULL, 'assets/img/placeholder.jpg', NULL, NULL, '1', NULL, '2020-11-28 09:34:20', '2020-11-28 09:34:20'),
(7, 'LKJAFD94R', NULL, NULL, 'Test Section Modified', NULL, 'assets/img/placeholder.jpg', NULL, NULL, '1', NULL, '2020-11-28 09:34:20', '2020-11-28 09:34:20'),
(8, 'LKJAFD94R', NULL, NULL, 'Final test section', NULL, 'assets/img/placeholder.jpg', NULL, NULL, '1', NULL, '2020-11-28 09:34:20', '2020-11-28 09:34:20'),
(9, 'LKJAFD94R', NULL, NULL, 'Last Step', NULL, 'assets/img/placeholder.jpg', NULL, NULL, '0', NULL, '2020-11-28 09:34:20', '2020-11-28 09:34:20'),
(10, 'LKJAFD94R', NULL, NULL, 'Adding a new department', NULL, 'assets/img/placeholder.jpg', NULL, NULL, '1', NULL, '2020-11-28 09:34:20', '2020-11-28 09:34:20'),
(11, 'LKJAFD94R', NULL, 'ANANOA', 'this is the department na', NULL, 'assets/img/placeholder.jpg', 'this is what i am inserting for this department', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '1', 'uIkajsw123456789064hxk1fc3efmnva', '2020-11-28 09:34:20', '2020-11-28 09:34:20');

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
(1, 'LKJAFD94R', 'z2JfSOdGTI4Pbxh8yV3UKY1LcMN7rgl6', 'Vacation Starts', '&lt;div&gt;&lt;!--block--&gt;This is the vacation starting point&lt;/div&gt;', '2020-12-31', '2021-01-05', NULL, 'all', 'PI9tVxCQrLsWJ8HT3wuMigBy0Ro5ZcDf', 'on', NULL, NULL, '0', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-30 10:14:50', '0', '1', 'Pending', '1'),
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

DROP TABLE IF EXISTS `events_types`;
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
(1, 'LKJAFD94R', NULL, 1, 3, '35.00', 'USD', '2019/2020', '1st', '1', '2021-01-08 17:12:27', 'uIkajsw123456789064hxk1fc3efmnva'),
(2, 'LKJAFD94R', NULL, 2, 2, '670.00', 'USD', '2019/2020', '1st', '1', '2021-01-08 17:15:20', 'uIkajsw123456789064hxk1fc3efmnva'),
(3, 'LKJAFD94R', NULL, 1, 1, '450.00', 'USD', '2019/2020', '1st', '1', '2021-01-08 17:16:28', 'uIkajsw123456789064hxk1fc3efmnva'),
(4, 'LKJAFD94R', NULL, 2, 1, '460.00', 'USD', '2019/2020', '1st', '1', '2021-01-11 02:26:30', 'uIkajsw123456789064hxk1fc3efmnva'),
(5, 'LKJAFD94R', NULL, 2, 3, '32.00', 'USD', '2019/2020', '1st', '1', '2021-01-11 02:27:09', 'uIkajsw123456789064hxk1fc3efmnva'),
(6, 'LKJAFD94R', NULL, 1, 2, '600.00', 'USD', '2019/2020', '1st', '1', '2021-01-25 21:35:04', 'uIkajsw123456789064hxk1fc3efmnva'),
(7, 'LKJAFD94R', NULL, 4, 1, '430.00', 'USD', '2019/2020', '1st', '1', '2021-01-25 21:35:50', 'uIkajsw123456789064hxk1fc3efmnva'),
(8, 'LKJAFD94R', NULL, 4, 2, '500.00', 'USD', '2019/2020', '1st', '1', '2021-01-25 21:36:02', 'uIkajsw123456789064hxk1fc3efmnva'),
(9, 'LKJAFD94R', NULL, 4, 3, '399.00', 'USD', '2019/2020', '1st', '1', '2021-01-25 21:36:11', 'uIkajsw123456789064hxk1fc3efmnva'),
(10, 'LKJAFD94R', NULL, 4, 4, '30.00', 'USD', '2019/2020', '1st', '1', '2021-01-25 21:36:19', 'uIkajsw123456789064hxk1fc3efmnva');

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
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `fees_category`
--

INSERT INTO `fees_category` (`id`, `client_id`, `name`, `amount`, `code`, `status`) VALUES
(1, 'LKJAFD94R', 'Tuition Fees', '430', NULL, '1'),
(2, 'LKJAFD94R', 'Hostel Fees', '600', NULL, '1'),
(3, 'LKJAFD94R', 'PTA Dues', '35', NULL, '1'),
(4, 'LKJAFD94R', 'Information Technology Dues', '40', NULL, '1');

-- --------------------------------------------------------

--
-- Table structure for table `fees_collection`
--

DROP TABLE IF EXISTS `fees_collection`;
CREATE TABLE `fees_collection` (
  `id` int(11) NOT NULL,
  `client_id` varchar(32) NOT NULL DEFAULT '1',
  `item_id` varchar(32) DEFAULT NULL,
  `student_id` varchar(32) DEFAULT NULL,
  `department_id` int(10) UNSIGNED DEFAULT NULL,
  `programme_id` int(11) UNSIGNED DEFAULT NULL,
  `class_id` int(11) UNSIGNED DEFAULT NULL,
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

INSERT INTO `fees_collection` (`id`, `client_id`, `item_id`, `student_id`, `department_id`, `programme_id`, `class_id`, `currency`, `category_id`, `amount`, `created_by`, `recorded_date`, `description`, `academic_year`, `academic_term`, `reversed`, `status`) VALUES
(3, 'LKJAFD94R', 'y3Geq0puosZAj6', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 3, NULL, 1, 'USD', 1, '400.00', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-01 19:58:33', 'This is the summary description of the payment been made by the student.', '2019/2020', '1st', '0', '1'),
(4, 'LKJAFD94R', '6DkYWCGSm1rn0q', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 3, NULL, 1, 'USD', 1, '50.00', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-10 21:39:14', 'Full payment of fees', '2019/2020', '1st', '0', '1'),
(5, 'LKJAFD94R', 'IxaVptPjnN1Ab9', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 3, NULL, 1, 'USD', 3, '35.00', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-22 21:53:48', 'null', '2019/2020', '1st', '0', '1'),
(6, 'LKJAFD94R', 'ljNfiw3IxSoYgL', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 3, NULL, 2, 'USD', 2, '670.00', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-11 03:30:45', 'This is full payment of the student tuition fees.', '2019/2020', '1st', '0', '1'),
(7, 'LKJAFD94R', 'MZ8EgaNR0v6H2P', 'ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v', 5, NULL, 2, 'USD', 2, '670.00', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-30 03:33:55', 'null', '2019/2020', '1st', '0', '1'),
(8, 'LKJAFD94R', 'Bf25WbSdkvcgO8hwCA3xH1ir9TPjUnGF', 'ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v', 5, NULL, 2, 'USD', 1, '400.00', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-11 08:56:21', 'Making part payment of the tuition fees.', '2019/2020', '1st', '0', '1'),
(9, 'LKJAFD94R', 'YS6IltQMAPiJ8fNEqaTe1FwgO4dxLcX3', 'ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v', 5, NULL, 2, 'USD', 1, '50.00', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-05 09:17:40', 'Make payment now', '2019/2020', '1st', '0', '1'),
(10, 'LKJAFD94R', '3z5qGgHVdYWkKUF4lx08rEBONp9X6aLo', 'SZMsssqcccfn5cBl0ARgPCj287hym36', 3, NULL, 2, 'USD', 2, '300.00', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-11 09:19:59', 'Making part payment of fees', '2019/2020', '1st', '0', '1'),
(11, 'LKJAFD94R', 'UD9wJGuznC54kKmpbTWMLcRQ7FjsE1PN', 'SZMsssqcccfn5cBl0ARgPCj287hym36', 3, NULL, 2, 'USD', 2, '170.00', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-13 09:20:13', '', '2019/2020', '1st', '0', '1'),
(12, 'LKJAFD94R', 'RpBnxETU5hr7tV82DGl6s34Q9ugXSJPH', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 3, NULL, 2, 'USD', 1, '250.00', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-14 09:20:40', '', '2019/2020', '1st', '0', '1'),
(13, 'LKJAFD94R', 'REWbIGmyzC9HNBJO68kFaAeKljLVU102', 'SZMsssqcccfn5cBl0ARgPCj287hym36', 3, NULL, 2, 'USD', 2, '200.00', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-16 09:21:04', '', '2019/2020', '1st', '0', '1'),
(14, 'LKJAFD94R', '6gFfQaPHZbp1OMz7NKWLV5yYljheD8Ac', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 3, NULL, 2, 'USD', 1, '210.00', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-17 09:21:46', '', '2019/2020', '1st', '0', '1'),
(15, 'LKJAFD94R', '4sVDL723Odt6ISNYiQmjxMnWGfkvKaAz', 'SZMsssqcccfn5cBl0ARgPCj287hym36', 3, NULL, 2, 'USD', 1, '460.00', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-19 09:22:37', '', '2019/2020', '1st', '0', '1'),
(16, 'LKJAFD94R', 'NFYIrTAGBvisuo3RWejzlVQPUS0Z674y', 'SZM14dtqDkbfn5cBl0ARgPCj287hym36', 3, NULL, 1, 'USD', 1, '250.00', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-18 19:15:31', '', '2019/2020', '1st', '0', '1'),
(17, 'LKJAFD94R', 'cB93JrnSbLGVqeO72my0ATogk4ZjfNIp', 'ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v', 5, NULL, 2, 'USD', 1, '10.00', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-18 19:15:46', '', '2019/2020', '1st', '0', '1'),
(18, 'LKJAFD94R', '5sbvWJI8QuC6r7LoiYThPjUHpqzcdnOe', 'SZM14dtqcccfn5cBl0ARgPCj287hym36', 3, NULL, 1, 'USD', 1, '450.00', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-18 23:25:21', '', '2019/2020', '1st', '0', '1'),
(19, 'LKJAFD94R', 'uPF2qtQZEvNChKkDBypjbszfVY8Wxomr', 'SZM14dtqcccfn5cBl0ARgPCj287hym36', 3, NULL, 1, 'USD', 3, '25.00', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-19 19:16:32', '', '2019/2020', '1st', '0', '1'),
(20, 'LKJAFD94R', '7cxSiajDqFmMzHB3VEyG91QWph5LgIn0', 'SZM14dtqDkbfn5cBl0ARgPCj287hym36', 3, NULL, 1, 'USD', 3, '15.00', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-19 23:25:24', '', '2019/2020', '1st', '0', '1'),
(21, 'LKJAFD94R', 'Rw8rLiKnW9jyNdFOksJ4MP0cV6IUuqxb', 'SZM14dtqDkbfn5cBl0ARgPCj287hym36', 3, NULL, 1, 'USD', 1, '100.00', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-19 19:17:18', '', '2019/2020', '1st', '0', '1'),
(22, 'LKJAFD94R', 'dP7fO1ERLoqFHiXAkUaejGDZzyIKvub9', 'SZMsssqcccfn5cBl0aaaPCj287hym36', 3, NULL, 1, 'USD', 3, '15.00', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-19 19:17:35', '', '2019/2020', '1st', '0', '1'),
(23, 'LKJAFD94R', 'XCia2ZOdR1lwk7ogDSszueWjKQBJxHLp', 'SZM14dtqcccfn5cBl0ARgPCj287hym36', 3, NULL, 1, 'USD', 3, '10.00', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-20 19:24:59', '', '2019/2020', '1st', '0', '1'),
(24, 'LKJAFD94R', 'pzmgTLtMc4iSKNBfejZa0JEIUV8QyXCO', 'SZM14dtqDkbfn5cBl0ARgPCj287hym36', 3, NULL, 1, 'USD', 1, '50.00', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-20 19:25:10', '', '2019/2020', '1st', '0', '1'),
(25, 'LKJAFD94R', 'X9OShmNLsBzJW3AKGCl8DHePUi4vgqoR', 'SZM14dtqDkbfn5cBl0ARgPCj287hym36', 3, NULL, 1, 'USD', 1, '50.00', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-20 19:25:22', '', '2019/2020', '1st', '0', '1'),
(26, 'LKJAFD94R', 'Rum4VznsopkGBSKwh7X6rZltQ0DqvIf5', 'SZMsssqcccfn5cBl0aaaPCj287hym36', 3, NULL, 1, 'USD', 1, '450.00', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-25 20:23:44', 'null', '2019/2020', '1st', '0', '1'),
(27, 'LKJAFD94R', 'mgZhsBj42cWfToPKl0CpFIJQAiG5t9xb', 'SZMsssqcccfn5cBl0aaaPCj287hym36', 3, NULL, 1, 'USD', 3, '20.00', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-25 20:23:56', 'null', '2019/2020', '1st', '0', '1'),
(28, 'LKJAFD94R', 'pPmGs5r9cAzL8exhJTu6CZwU0SlXRnfV', 'SZM14dtqDkbfn5cBl0ARgPCj287hym36', 3, NULL, 1, 'USD', 3, '5.00', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-25 21:10:04', 'This is another payment value', '2019/2020', '1st', '0', '1'),
(29, 'LKJAFD94R', 'yZqfLz2UwKVHJlohW6jONbTpuCS3DiRd', 'SZM14dtqDkbfn5cBl0ARgPCj287hym36', 3, NULL, 1, 'USD', 3, '5.00', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-25 21:15:20', 'null', '2019/2020', '1st', '0', '1'),
(30, 'LKJAFD94R', 'yXQ8nOYG0LgAhuTPoF1NHdm2lJqwWpS9', 'SZM14dtqDkbfn5cBl0ARgPCj287hym36', 3, NULL, 1, 'USD', 3, '3.00', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-25 21:16:21', 'null', '2019/2020', '1st', '0', '1'),
(31, 'LKJAFD94R', 'KmBd9wC5nzQTtrsVj78OLXyYHIUcFboa', 'SZM14dtqDkbfn5cBl0ARgPCj287hym36', 3, NULL, 1, 'USD', 3, '4.00', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-25 21:16:35', 'null', '2019/2020', '1st', '0', '1'),
(34, 'LKJAFD94R', 'W38tRPipmsrxwjKOdEnFIkBhGv6C5z12', 'SZM14dtqDkbfn5cBl0ARgPCj287hym36', 3, NULL, 1, 'USD', 3, '3.00', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-25 21:33:12', 'null', '2019/2020', '1st', '0', '1'),
(35, 'LKJAFD94R', 'uGrNOaMmYoFUHDQfkVj012Pc6x5ldECI', 'SZM14dtqDkbfn5cBl0ARgPCj287hym36', 3, NULL, 4, 'USD', 4, '30.00', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-25 22:32:03', 'null', '2019/2020', '1st', '0', '1'),
(36, 'LKJAFD94R', 'J6jBZtCrHVIqKxaX9gOTNfAoL5eyipnP', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 3, NULL, 1, 'USD', 2, '230.00', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-28 18:29:27', 'You will be left with a balance of 370 to pay to the user.', '2019/2020', '1st', '0', '1'),
(37, 'LKJAFD94R', 'ToUrsB75lpudfIF2PwC0HbJn8mxGWQ9e', 'SZM14dtqcccfn5cBl0ARgPCj287hym36', 3, NULL, 1, 'USD', 2, '300.00', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-28 18:32:55', 'This is a part payment of the school fees.', '2019/2020', '1st', '0', '1');

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
  `paid_status` enum('0','1') NOT NULL DEFAULT '0',
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
(1, 'LKJAFD94R', 'afdafdsgf45454fg', 'SZM14dtqDkbfn5cBl0ARgPCj287hym36', '1', 1, 'USD', '430.00', '450.00', '-20.00', '2019/2020', '1st', '0', '2021-01-08 17:11:33', '2021-01-18 19:25:22', 'X9OShmNLsBzJW3AKGCl8DHePUi4vgqoR', 'uIkajsw123456789064hxk1fc3efmnva', '1'),
(2, 'LKJAFD94R', 'afdfdfdfdfdffderer', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', '1', 3, 'USD', '35.00', '35.00', '0.00', '2019/2020', '1st', '1', '2021-01-08 17:12:27', '2021-01-09 21:53:48', 'IxaVptPjnN1Ab9', 'uIkajsw123456789064hxk1fc3efmnva', '1'),
(3, 'LKJAFD94R', 'dfdffgggfd35t5656', 'SZM14dtqDkbfn5cBl0ARgPCj287hym36', '1', 3, 'USD', '399.00', '35.00', '364.00', '2019/2020', '1st', '1', '2021-01-08 17:12:27', '2021-01-25 21:33:12', 'W38tRPipmsrxwjKOdEnFIkBhGv6C5z12', 'uIkajsw123456789064hxk1fc3efmnva', '1'),
(4, 'LKJAFD94R', 'afdere45faderrere', 'SZM14dtqcccfn5cBl0ARgPCj287hym36', '1', 3, 'USD', '35.00', '35.00', '0.00', '2019/2020', '1st', '0', '2021-01-08 17:12:27', '2021-01-18 19:24:59', 'XCia2ZOdR1lwk7ogDSszueWjKQBJxHLp', 'uIkajsw123456789064hxk1fc3efmnva', '1'),
(5, 'LKJAFD94R', 'a445454fggdfdf', 'SZMsssqcccfn5cBl0aaaPCj287hym36', '1', 3, 'USD', '35.00', '35.00', '0.00', '2019/2020', '1st', '0', '2021-01-08 17:12:27', '2021-01-25 20:23:56', 'mgZhsBj42cWfToPKl0CpFIJQAiG5t9xb', 'uIkajsw123456789064hxk1fc3efmnva', '1'),
(6, 'LKJAFD94R', 'afd545fdf5645556', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', '2', 2, 'USD', '670.00', '670.00', '0.00', '2019/2020', '1st', '1', '2021-01-08 17:15:20', '2021-01-11 03:30:45', 'ljNfiw3IxSoYgL', 'uIkajsw123456789064hxk1fc3efmnva', '1'),
(7, 'LKJAFD94R', 'sgfsgfjkkjk4545', 'ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v', '2', 2, 'USD', '670.00', '670.00', '0.00', '2019/2020', '1st', '1', '2021-01-08 17:15:20', '2021-01-11 03:33:55', 'MZ8EgaNR0v6H2P', 'uIkajsw123456789064hxk1fc3efmnva', '1'),
(8, 'LKJAFD94R', 'fgfgd4577gaetrere', 'SZMsssqcccfn5cBl0ARgPCj287hym36', '2', 2, 'USD', '670.00', '670.00', '0.00', '2019/2020', '1st', '0', '2021-01-08 17:15:20', '2021-01-19 09:21:04', 'REWbIGmyzC9HNBJO68kFaAeKljLVU102', 'uIkajsw123456789064hxk1fc3efmnva', '1'),
(9, 'LKJAFD94R', 'ffadfahhf464h656hg', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', '1', 1, 'USD', '450.00', '450.00', '0.00', '2019/2020', '1st', '1', '2021-01-08 17:16:28', '2021-01-09 21:39:14', '6DkYWCGSm1rn0q', 'uIkajsw123456789064hxk1fc3efmnva', '1'),
(10, 'LKJAFD94R', 'sgfsgfsgfr665ttrtr', 'SZM14dtqcccfn5cBl0ARgPCj287hym36', '1', 1, 'USD', '450.00', '450.00', '0.00', '2019/2020', '1st', '0', '2021-01-08 17:16:28', '2021-01-20 19:16:11', '5sbvWJI8QuC6r7LoiYThPjUHpqzcdnOe', 'uIkajsw123456789064hxk1fc3efmnva', '1'),
(11, 'LKJAFD94R', 'afdfhgjhjhkjlhggh', 'SZMsssqcccfn5cBl0aaaPCj287hym36', '1', 1, 'USD', '450.00', '450.00', '0.00', '2019/2020', '1st', '0', '2021-01-08 17:16:28', '2021-01-25 20:23:44', 'Rum4VznsopkGBSKwh7X6rZltQ0DqvIf5', 'uIkajsw123456789064hxk1fc3efmnva', '1'),
(27, 'LKJAFD94R', 'vaUGfPZ7Ln9KpTS4lyHc8gmd3YXRqk1I', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', '2', 1, 'USD', '460.00', '460.00', '0.00', '2019/2020', '1st', '0', '2021-01-11 03:30:11', '2021-01-19 09:21:46', '6gFfQaPHZbp1OMz7NKWLV5yYljheD8Ac', 'uIkajsw123456789064hxk1fc3efmnva', '1'),
(28, 'LKJAFD94R', 'z5VPHQZtAkhjRmxqalueEdW0INnD8YCS', 'ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v', '2', 1, 'USD', '460.00', '460.00', '0.00', '2019/2020', '1st', '0', '2021-01-11 03:30:11', '2021-01-20 19:15:46', 'cB93JrnSbLGVqeO72my0ATogk4ZjfNIp', 'uIkajsw123456789064hxk1fc3efmnva', '1'),
(29, 'LKJAFD94R', 'wla0cZULxsrHvFdkzhAnjCiG4p9NIbEO', 'SZMsssqcccfn5cBl0ARgPCj287hym36', '2', 1, 'USD', '460.00', '460.00', '0.00', '2019/2020', '1st', '0', '2021-01-11 03:30:11', '2021-01-19 09:22:37', '4sVDL723Odt6ISNYiQmjxMnWGfkvKaAz', 'uIkajsw123456789064hxk1fc3efmnva', '1'),
(30, 'LKJAFD94R', 'tLMgeV3b6soXaFylhB2rRzCA8QG0HimZ', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', '1', 2, 'USD', '600.00', '230.00', '370.00', '2019/2020', '1st', '0', '2021-01-25 21:35:04', '2021-01-28 18:29:27', 'J6jBZtCrHVIqKxaX9gOTNfAoL5eyipnP', 'uIkajsw123456789064hxk1fc3efmnva', '1'),
(31, 'LKJAFD94R', 'wRi0c3ZsyhD5MgFlSb7vEz8jmGCToXPn', 'SZM14dtqcccfn5cBl0ARgPCj287hym36', '1', 2, 'USD', '600.00', '300.00', '300.00', '2019/2020', '1st', '0', '2021-01-25 21:35:04', '2021-01-28 18:32:55', 'ToUrsB75lpudfIF2PwC0HbJn8mxGWQ9e', 'uIkajsw123456789064hxk1fc3efmnva', '1'),
(32, 'LKJAFD94R', '2SczhploItDJKVMxFNmRuv4Cjk96bX7A', 'SZMsssqcccfn5cBl0aaaPCj287hym36', '1', 2, 'USD', '600.00', '0.00', '600.00', '2019/2020', '1st', '0', '2021-01-25 21:35:04', NULL, NULL, 'uIkajsw123456789064hxk1fc3efmnva', '1'),
(33, 'LKJAFD94R', '2uzax6DJGcXMkEhSRWgq0Q3VvB7UTFKn', 'SZM14dtqDkbfn5cBl0ARgPCj287hym36', '4', 2, 'USD', '500.00', '0.00', '500.00', '2019/2020', '1st', '0', '2021-01-25 21:36:02', NULL, NULL, 'uIkajsw123456789064hxk1fc3efmnva', '1'),
(34, 'LKJAFD94R', 'VYUaHPsL8FMeDwWqhTpvjKi2bB56No7R', 'SZM14dtqDkbfn5cBl0ARgPCj287hym36', '4', 4, 'USD', '30.00', '30.00', '0.00', '2019/2020', '1st', '1', '2021-01-25 21:36:20', '2021-01-25 22:32:03', 'uGrNOaMmYoFUHDQfkVj012Pc6x5ldECI', 'uIkajsw123456789064hxk1fc3efmnva', '1');

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

--
-- Dumping data for table `files_attachment`
--

INSERT INTO `files_attachment` (`id`, `resource`, `description`, `attachment_size`, `record_id`, `resource_id`, `created_by`, `date_created`) VALUES
(1, 'library_book', '{\"files\":[{\"unique_id\":\"YCRWThpuiABfLzHIoJjeEVkNgMdP5bt4csF60xrq8D1U97vaQyZOmS3\",\"name\":\"hospital-information-system-features.jpg\",\"path\":\"assets\\/uploads\\/uIkajsw123456789064hxk1fc3efmnva\\/docs\\/ebook_12223444443\\/hospital-information-system-features.jpg\",\"type\":\"jpg\",\"size\":\"35.96KB\",\"size_raw\":\"35.96\",\"is_deleted\":0,\"record_id\":\"12223444443\",\"datetime\":\"Monday, 4th January 2021 at 07:16:07PM\",\"favicon\":\"fa fa-file-image fa-1x\",\"color\":\"danger\",\"uploaded_by\":\"test_admin\",\"uploaded_by_id\":\"uIkajsw123456789064hxk1fc3efmnva\"},{\"unique_id\":\"r3aSyCVRiJ6ZA8Tek0vqcsofw7HzUxNELgnmh1D5FQYX9OKdIbW2PGj\",\"name\":\"online-poll-result.png\",\"path\":\"assets\\/uploads\\/uIkajsw123456789064hxk1fc3efmnva\\/docs\\/ebook_12223444443\\/online-poll-result.png\",\"type\":\"png\",\"size\":\"105.5KB\",\"size_raw\":\"105.5\",\"is_deleted\":0,\"record_id\":\"12223444443\",\"datetime\":\"Monday, 4th January 2021 at 07:16:07PM\",\"favicon\":\"fa fa-file-image fa-1x\",\"color\":\"danger\",\"uploaded_by\":\"test_admin\",\"uploaded_by_id\":\"uIkajsw123456789064hxk1fc3efmnva\"}],\"files_count\":2,\"raw_size_mb\":0.14,\"files_size\":\"0.14MB\"}', '0.14', '12223444443', '12223444443', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-04 19:16:07'),
(2, 'events', '{\"files\":[{\"unique_id\":\"dzamSBFx4hXDwGnNfij8PWqTKp5Qb63lgs9CVYHotuIyv7eEURAZJrM\",\"name\":\"bank2.png\",\"path\":\"assets\\/uploads\\/uIkajsw123456789064hxk1fc3efmnva\\/docs\\/events_z2JfSOdGTI4Pbxh8yV3UKY1LcMN7rgl6\\/bank2.png\",\"type\":\"png\",\"size\":\"27.24KB\",\"size_raw\":\"27.24\",\"is_deleted\":0,\"record_id\":\"z2JfSOdGTI4Pbxh8yV3UKY1LcMN7rgl6\",\"datetime\":\"Sunday, 24th January 2021 at 01:21:18PM\",\"favicon\":\"fa fa-file-image fa-1x\",\"color\":\"danger\",\"uploaded_by\":\"test_admin\",\"uploaded_by_id\":\"uIkajsw123456789064hxk1fc3efmnva\"}],\"files_count\":1,\"raw_size_mb\":0.03,\"files_size\":\"0.03MB\"}', '0.03', 'z2JfSOdGTI4Pbxh8yV3UKY1LcMN7rgl6_3YJD9dtyNuzfEgIAMl4rkchsoCGVmpTn', 'z2JfSOdGTI4Pbxh8yV3UKY1LcMN7rgl6', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-24 13:21:18'),
(3, 'assignments', '{\"files\":[{\"unique_id\":\"Sm72QrOBYF50IjAGw8gq3XzphlsRaUbKkdu6TM1oEWLJV9nitfxyPec\",\"name\":\"2021-01-17_063539.csv\",\"path\":\"assets\\/uploads\\/uIkajsw123456789064hxk1fc3efmnva\\/docs\\/assignments\\/2021-01-17_063539.csv\",\"type\":\"csv\",\"size\":\"704B\",\"size_raw\":\"0.69\",\"is_deleted\":0,\"record_id\":\"Lzr4S8NTcp3RGFIk6VdlsnUP79ExAyt0\",\"datetime\":\"Tuesday, 26th January 2021 at 01:30:21AM\",\"favicon\":\"fa fa-file-csv fa-1x\",\"color\":\"success\",\"uploaded_by\":\"test_admin\",\"uploaded_by_id\":\"uIkajsw123456789064hxk1fc3efmnva\"}],\"files_count\":1,\"raw_size_mb\":0,\"files_size\":\"0MB\"}', '0', 'Lzr4S8NTcp3RGFIk6VdlsnUP79ExAyt0', 'Lzr4S8NTcp3RGFIk6VdlsnUP79ExAyt0', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-26 01:30:21'),
(4, 'assignments', '{\"files\":[{\"unique_id\":\"YiorACxkeBOp50K8JsGPg14D7nfSWlENLHUIy6d3FqwR2b9aVmXhtMj\",\"name\":\"2021 Circuit MYF Rededition Letters - Update.docx\",\"path\":\"assets\\/uploads\\/a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\\/docs\\/assignments\\/2021_ircuit__ededition_etters_-_pdate.docx\",\"type\":\"docx\",\"size\":\"128.1KB\",\"size_raw\":\"128.1\",\"is_deleted\":0,\"record_id\":\"jNngSCaicZtfoqDG7HMvKbEpmr6x1ue8\",\"datetime\":\"Tuesday, 26th January 2021 at 02:54:22PM\",\"favicon\":\"fa fa-file-word fa-1x\",\"color\":\"primary\",\"uploaded_by\":\"test_teacher\",\"uploaded_by_id\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\"}],\"files_count\":1,\"raw_size_mb\":0.13,\"files_size\":\"0.13MB\"}', '0.13', 'jNngSCaicZtfoqDG7HMvKbEpmr6x1ue8', 'jNngSCaicZtfoqDG7HMvKbEpmr6x1ue8', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '2021-01-26 14:54:22'),
(5, 'assignment_doc', '{\"files\":[{\"unique_id\":\"9Y7rSWLlRNEozctgM4ni2DuZmTFOX1Cq8hx5ks6avGPpAK0VwQHJ3eI\",\"name\":\"2021 Circuit MYF Rededition Letters - Update.docx\",\"path\":\"assets\\/uploads\\/xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\\/docs\\/assignments_handin_jNngSCaicZtfoqDG7HMvKbEpmr6x1ue8\\/2021_ircuit__ededition_etters_-_pdate.docx\",\"type\":\"docx\",\"size\":\"128.1KB\",\"size_raw\":\"128.1\",\"is_deleted\":0,\"record_id\":\"jNngSCaicZtfoqDG7HMvKbEpmr6x1ue8\",\"datetime\":\"Tuesday, 26th January 2021 at 03:17:13PM\",\"favicon\":\"fa fa-file-word fa-1x\",\"color\":\"primary\",\"uploaded_by\":\"test_student\",\"uploaded_by_id\":\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\"},{\"unique_id\":\"dwgnQVPmpskrGISDCtiEU9f4FeZcHRyAYqlaW8z7v6uh0B53K2JNM1O\",\"name\":\"M200206-2021-02_1512021_124039.pdf\",\"path\":\"assets\\/uploads\\/xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\\/docs\\/assignments_handin_jNngSCaicZtfoqDG7HMvKbEpmr6x1ue8\\/200206-2021-02_1512021_124039.pdf\",\"type\":\"pdf\",\"size\":\"168.34KB\",\"size_raw\":\"168.34\",\"is_deleted\":0,\"record_id\":\"jNngSCaicZtfoqDG7HMvKbEpmr6x1ue8\",\"datetime\":\"Tuesday, 26th January 2021 at 03:17:13PM\",\"favicon\":\"fa fa-file-pdf fa-1x\",\"color\":\"danger\",\"uploaded_by\":\"test_student\",\"uploaded_by_id\":\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\"},{\"unique_id\":\"2LPwVauDhM4me8AIBUZKptXnGOjd7cN30rYqJso1fbRC6xETglvy5kW\",\"name\":\"image (20).png\",\"path\":\"assets\\/uploads\\/xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\\/docs\\/assignments_handin_jNngSCaicZtfoqDG7HMvKbEpmr6x1ue8\\/image_20.png\",\"type\":\"png\",\"size\":\"233.18KB\",\"size_raw\":\"233.18\",\"is_deleted\":0,\"record_id\":\"jNngSCaicZtfoqDG7HMvKbEpmr6x1ue8\",\"datetime\":\"Tuesday, 26th January 2021 at 03:17:13PM\",\"favicon\":\"fa fa-file-image fa-1x\",\"color\":\"danger\",\"uploaded_by\":\"test_student\",\"uploaded_by_id\":\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\"},{\"unique_id\":\"LcUh3Yy8CZa4HKNvDTfS2uOJFPm9g7QWRA16GxXrMsniVktoBw5pbzI\",\"name\":\"image (18).png\",\"path\":\"assets\\/uploads\\/xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\\/docs\\/assignments_handin_jNngSCaicZtfoqDG7HMvKbEpmr6x1ue8\\/image_18.png\",\"type\":\"png\",\"size\":\"192.78KB\",\"size_raw\":\"192.78\",\"is_deleted\":0,\"record_id\":\"jNngSCaicZtfoqDG7HMvKbEpmr6x1ue8\",\"datetime\":\"Tuesday, 26th January 2021 at 03:17:13PM\",\"favicon\":\"fa fa-file-image fa-1x\",\"color\":\"danger\",\"uploaded_by\":\"test_student\",\"uploaded_by_id\":\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\"}],\"files_count\":4,\"raw_size_mb\":0.71,\"files_size\":\"0.71MB\"}', '0.71', 'jNngSCaicZtfoqDG7HMvKbEpmr6x1ue8', 'jNngSCaicZtfoqDG7HMvKbEpmr6x1ue8_xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', '2021-01-26 15:17:13');

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
-- Table structure for table `payslips`
--

DROP TABLE IF EXISTS `payslips`;
CREATE TABLE `payslips` (
  `id` int(11) NOT NULL,
  `client_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
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

--
-- Dumping data for table `payslips`
--

INSERT INTO `payslips` (`id`, `client_id`, `employee_id`, `basic_salary`, `total_allowance`, `total_deductions`, `gross_salary`, `net_salary`, `payslip_month`, `payslip_month_id`, `payslip_year`, `payment_mode`, `created_by`, `validated`, `validated_date`, `comments`, `date_log`, `status`, `deleted`) VALUES
(1, 'LKJAFD94R', 'uIkajsw123456789064hxk1fc3efmnva', 7800.00, 2200.00, 2000.00, 10000.00, 8000.00, 'January', '2021-01-31', '2021', 'Bank', 'uIkajsw123456789064hxk1fc3efmnva', '1', '2021-01-28 16:35:23', 'Paid into the account of the employee', '2021-01-27 22:04:51', '1', '0'),
(2, 'LKJAFD94R', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 3300.00, 100.00, 427.00, 3400.00, 2973.00, 'January', '2021-01-31', '2021', 'Bank', 'uIkajsw123456789064hxk1fc3efmnva', '1', '2021-01-28 16:32:56', '', '2021-01-28 09:22:04', '1', '0'),
(3, 'LKJAFD94R', 'aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe', 7800.00, 2200.00, 2000.00, 10000.00, 8000.00, 'December', '2021-01-31', '2020', 'null', 'uIkajsw123456789064hxk1fc3efmnva', '1', '2021-01-28 17:10:55', '', '2021-01-28 17:10:50', '1', '0');

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
  `default_amount` double(12,2) DEFAULT 0.00,
  `status` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `payslips_allowance_types`
--

INSERT INTO `payslips_allowance_types` (`id`, `client_id`, `name`, `description`, `type`, `default_amount`, `status`) VALUES
(1, 'LKJAFD94R', 'Tax', 'This is the taxt that will be deducted and sent to GRA', 'Deduction', 0.00, '1'),
(2, 'LKJAFD94R', 'SSNIT', 'This will go to SNNIT and serve a savings for the employee at a later date in future', 'Deduction', 0.00, '1'),
(3, 'LKJAFD94R', 'Transport', NULL, 'Allowance', 0.00, '1'),
(4, 'LKJAFD94R', 'Overtime', 'This will apply to overtime and all other works', 'Allowance', 0.00, '1'),
(5, 'LKJAFD94R', 'Math Books Edited', 'This is the allowance that i am adding to the list', 'Allowance', 0.00, '1'),
(6, 'LKJAFD94R', 'homepage', 'This is yet another deduction that will also be considered later on', 'Deduction', 0.00, '1');

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

--
-- Dumping data for table `payslips_details`
--

INSERT INTO `payslips_details` (`id`, `client_id`, `payslip_id`, `allowance_id`, `employee_id`, `detail_type`, `payslip_month`, `payslip_year`, `amount`, `date_created`) VALUES
(1, 'LKJAFD94R', '1', 3, 'uIkajsw123456789064hxk1fc3efmnva', 'Allowance', 'January', '2021', 1300.00, '2021-01-27 22:04:51'),
(2, 'LKJAFD94R', '1', 4, 'uIkajsw123456789064hxk1fc3efmnva', 'Allowance', 'January', '2021', 500.00, '2021-01-27 22:04:51'),
(3, 'LKJAFD94R', '1', 5, 'uIkajsw123456789064hxk1fc3efmnva', 'Allowance', 'January', '2021', 400.00, '2021-01-27 22:04:51'),
(4, 'LKJAFD94R', '1', 1, 'uIkajsw123456789064hxk1fc3efmnva', 'Deduction', 'January', '2021', 800.00, '2021-01-27 22:04:51'),
(5, 'LKJAFD94R', '1', 2, 'uIkajsw123456789064hxk1fc3efmnva', 'Deduction', 'January', '2021', 1200.00, '2021-01-27 22:04:51'),
(6, 'LKJAFD94R', '2', 3, 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Allowance', 'January', '2021', 100.00, '2021-01-28 09:22:04'),
(7, 'LKJAFD94R', '2', 1, 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Deduction', 'January', '2021', 198.00, '2021-01-28 09:22:04'),
(8, 'LKJAFD94R', '2', 2, 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Deduction', 'January', '2021', 229.00, '2021-01-28 09:22:04'),
(9, 'LKJAFD94R', '3', 3, 'aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Allowance', 'December', '2020', 1300.00, '2021-01-28 17:10:50'),
(10, 'LKJAFD94R', '3', 4, 'aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Allowance', 'December', '2020', 500.00, '2021-01-28 17:10:50'),
(11, 'LKJAFD94R', '3', 5, 'aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Allowance', 'December', '2020', 400.00, '2021-01-28 17:10:50'),
(12, 'LKJAFD94R', '3', 1, 'aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Deduction', 'December', '2020', 800.00, '2021-01-28 17:10:51'),
(13, 'LKJAFD94R', '3', 2, 'aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Deduction', 'December', '2020', 1200.00, '2021-01-28 17:10:51');

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

--
-- Dumping data for table `payslips_employees_allowances`
--

INSERT INTO `payslips_employees_allowances` (`id`, `client_id`, `allowance_id`, `employee_id`, `amount`, `type`, `date_created`) VALUES
(21, 'LKJAFD94R', 3, 'uIkajsw123456789064hxk1fc3efmnva', 1300.00, 'Allowance', '2021-01-27 15:06:42'),
(22, 'LKJAFD94R', 4, 'uIkajsw123456789064hxk1fc3efmnva', 500.00, 'Allowance', '2021-01-27 15:06:43'),
(23, 'LKJAFD94R', 5, 'uIkajsw123456789064hxk1fc3efmnva', 400.00, 'Allowance', '2021-01-27 15:06:43'),
(24, 'LKJAFD94R', 1, 'uIkajsw123456789064hxk1fc3efmnva', 800.00, 'Deduction', '2021-01-27 15:06:43'),
(25, 'LKJAFD94R', 2, 'uIkajsw123456789064hxk1fc3efmnva', 1200.00, 'Deduction', '2021-01-27 15:06:43'),
(28, 'LKJAFD94R', 4, 'aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe', 200.00, 'Allowance', '2021-01-27 15:21:37'),
(29, 'LKJAFD94R', 1, 'aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe', 200.00, 'Deduction', '2021-01-27 15:21:37'),
(30, 'LKJAFD94R', 2, 'aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe', 290.00, 'Deduction', '2021-01-27 15:21:37'),
(34, 'LKJAFD94R', 3, 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 100.00, 'Allowance', '2021-01-27 15:23:01'),
(35, 'LKJAFD94R', 1, 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 198.00, 'Deduction', '2021-01-27 15:23:01'),
(36, 'LKJAFD94R', 2, 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 229.00, 'Deduction', '2021-01-27 15:23:01'),
(37, 'LKJAFD94R', 3, '9898989GstOi8vMW0zQ2A57nqLJZNkYe', 230.00, 'Allowance', '2021-01-27 15:24:17'),
(38, 'LKJAFD94R', 4, '9898989GstOi8vMW0zQ2A57nqLJZNkYe', 220.00, 'Allowance', '2021-01-27 15:24:17'),
(39, 'LKJAFD94R', 5, '9898989GstOi8vMW0zQ2A57nqLJZNkYe', 400.00, 'Allowance', '2021-01-27 15:24:17'),
(40, 'LKJAFD94R', 1, '9898989GstOi8vMW0zQ2A57nqLJZNkYe', 300.00, 'Deduction', '2021-01-27 15:24:17'),
(41, 'LKJAFD94R', 2, '9898989GstOi8vMW0zQ2A57nqLJZNkYe', 450.00, 'Deduction', '2021-01-27 15:24:17');

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

--
-- Dumping data for table `payslips_employees_payroll`
--

INSERT INTO `payslips_employees_payroll` (`id`, `client_id`, `employee_id`, `basic_salary`, `allowances`, `deductions`, `net_allowance`, `gross_salary`, `net_salary`, `account_name`, `account_number`, `bank_name`, `bank_branch`, `ssnit_number`, `tin_number`) VALUES
(1, 'LKJAFD94R', 'uIkajsw123456789064hxk1fc3efmnva', 7800.00, 2200.00, 2000.00, 200.00, 10000.00, 8000.00, 'Emmanuel K. Obeng', '10122909200390', 'Stanbic Bank Ghan Limited', 'Adjiringanor Branch', 'FHA09309390', '200910920909'),
(2, 'LKJAFD94R', 'aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe', 3200.00, 200.00, 490.00, -290.00, 3400.00, 2910.00, 'Second Teacher Account', '9930939039930', 'United Bank of Africa', 'American House', 'FAB0993938839', 'ALD090039930'),
(3, 'LKJAFD94R', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 3300.00, 100.00, 427.00, -327.00, 3400.00, 2973.00, 'Test Teacher Account', '00939930', 'Accra Bank', 'Accra', 'FAKD8859958948', '994994994445'),
(4, 'LKJAFD94R', '9898989GstOi8vMW0zQ2A57nqLJZNkYe', 3209.00, 850.00, 750.00, 100.00, 4059.00, 3309.00, 'Third Teacher Account', '9900939930039', 'Standard Chartered Bank', 'Accra Main Branch', 'FB993883938398', 'LA99309300393');

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

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `client_id`, `name`, `slug`, `section_code`, `image`, `description`, `section_leader`, `created_by`, `status`, `date_created`, `date_updated`) VALUES
(1, 'LKJAFD94R', 'Blue', NULL, NULL, 'assets/img/placeholder.jpg', NULL, NULL, NULL, '1', '2020-11-28 00:34:43', '2020-11-28 09:34:32'),
(2, 'LKJAFD94R', 'Red', NULL, 'SECTIONCODED', 'assets/img/placeholder.jpg', 'update the section information here', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', NULL, '1', '2020-11-28 00:34:43', '2020-11-28 09:46:06'),
(3, 'LKJAFD94R', 'Green', NULL, NULL, 'assets/img/placeholder.jpg', NULL, NULL, NULL, '1', '2020-11-28 00:34:43', '2020-11-28 09:34:32'),
(4, 'LKJAFD94R', 'Yellow', NULL, NULL, 'assets/img/placeholder.jpg', NULL, NULL, NULL, '1', '2020-11-28 00:34:43', '2020-11-28 09:34:32'),
(5, 'LKJAFD94R', 'Pink Section', NULL, 'SECTIONC', 'assets/img/placeholder.jpg', 'this is the final updates to the sections list', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', NULL, '1', '2020-11-28 00:34:43', '2020-11-28 09:48:02'),
(6, 'LKJAFD94R', 'O- Edited', NULL, NULL, 'assets/img/placeholder.jpg', NULL, NULL, NULL, '1', '2020-11-28 00:34:43', '2020-11-28 09:34:32'),
(7, 'LKJAFD94R', 'Test Section Modified', NULL, NULL, 'assets/img/placeholder.jpg', NULL, NULL, NULL, '1', '2020-11-28 00:34:43', '2020-11-28 09:34:32'),
(8, 'LKJAFD94R', 'Final test section', NULL, NULL, 'assets/img/placeholder.jpg', NULL, NULL, NULL, '1', '2020-11-28 00:34:43', '2020-11-28 09:34:32'),
(9, 'LKJAFD94R', 'Last Step', NULL, NULL, 'assets/img/placeholder.jpg', NULL, NULL, NULL, '0', '2020-11-28 00:34:43', '2020-11-28 09:34:32'),
(10, 'LKJAFD94R', 'this is the section name', NULL, 'ADD A SECTION', 'assets/img/placeholder.jpg', 'this is the description for the section also', 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 'uIkajsw123456789064hxk1fc3efmnva', '1', '2020-11-28 00:44:02', '2020-11-28 09:34:32');

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

--
-- Dumping data for table `timetables`
--

INSERT INTO `timetables` (`item_id`, `client_id`, `name`, `days`, `slots`, `duration`, `class_id`, `start_hr`, `start_min`, `start_mer`, `start_time`, `allow_conflicts`, `frozen`, `academic_year`, `academic_term`, `disabled_inputs`, `status`, `published`, `date_created`, `last_updated`) VALUES
('AUPurC1x0IT6dwqXJD9pvmWKbg3QB8Yo', 'LKJAFD94R', 'Another template to create', 5, 8, 45, 'fadafafdadaf', '08', '30', 'AM', '08:30', 0, 0, '2019/2020', '1st', '[\"1_1\",\"5_1\"]', '1', '1', '2021-01-22 23:52:24', '2021-01-23 16:17:42'),
('qEhlngB36otLKapxvISUHWduMTY4Gi8c', 'LKJAFD94R', 'Test Timetable', 6, 9, 60, 'faflkdjaflkdjafd', '08', '30', 'AM', '08:00', 0, 0, '2019/2020', '1st', '[]', '1', '1', '2021-01-22 22:54:58', '2021-01-23 17:41:06');

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

--
-- Dumping data for table `timetables_slots_allocation`
--

INSERT INTO `timetables_slots_allocation` (`id`, `client_id`, `timetable_id`, `day`, `slot`, `day_slot`, `room_id`, `class_id`, `course_id`, `students_id`, `tutors_id`, `status`, `date_created`) VALUES
(117, 'LKJAFD94R', 'AUPurC1x0IT6dwqXJD9pvmWKbg3QB8Yo', '4', '4', '4_4', 'lkajflajfakfaljfkaf', 'fadafafdadaf', 'afdafdafdafd', NULL, NULL, '1', '2021-01-23 14:04:06'),
(118, 'LKJAFD94R', 'AUPurC1x0IT6dwqXJD9pvmWKbg3QB8Yo', '1', '4', '1_4', 'lkajflajfakfaljfkaf', 'fadafafdadaf', 'afdafdafdafd', NULL, NULL, '1', '2021-01-23 14:04:06'),
(119, 'LKJAFD94R', 'AUPurC1x0IT6dwqXJD9pvmWKbg3QB8Yo', '5', '7', '5_7', 'lkajflajfakfaljfkaf', 'fadafafdadaf', 'afdafdafdafd', NULL, NULL, '1', '2021-01-23 14:04:06'),
(120, 'LKJAFD94R', 'AUPurC1x0IT6dwqXJD9pvmWKbg3QB8Yo', '3', '7', '3_7', 'lkajflajfakfaljfkaf', 'fadafafdadaf', 'afdafdafdafd', NULL, NULL, '1', '2021-01-23 14:04:06'),
(121, 'LKJAFD94R', 'AUPurC1x0IT6dwqXJD9pvmWKbg3QB8Yo', '1', '7', '1_7', 'lkajflajfakfaljfkaf', 'fadafafdadaf', '8u20GszACM7TyFNJctYEDgX9rwp6Oe1a', NULL, NULL, '1', '2021-01-23 14:04:06'),
(122, 'LKJAFD94R', 'AUPurC1x0IT6dwqXJD9pvmWKbg3QB8Yo', '5', '3', '5_3', 'lkajflajfakfaljfkaf', 'fadafafdadaf', '8u20GszACM7TyFNJctYEDgX9rwp6Oe1a', NULL, NULL, '1', '2021-01-23 14:04:06'),
(123, 'LKJAFD94R', 'AUPurC1x0IT6dwqXJD9pvmWKbg3QB8Yo', '2', '6', '2_6', 'lkajflajfakfaljfkaf', 'fadafafdadaf', 'afdafdafdafd', NULL, NULL, '1', '2021-01-23 14:04:06'),
(124, 'LKJAFD94R', 'AUPurC1x0IT6dwqXJD9pvmWKbg3QB8Yo', '2', '3', '2_3', 'lkajflajfakfaljfkaf', 'fadafafdadaf', '8u20GszACM7TyFNJctYEDgX9rwp6Oe1a', NULL, NULL, '1', '2021-01-23 14:04:06'),
(125, 'LKJAFD94R', 'AUPurC1x0IT6dwqXJD9pvmWKbg3QB8Yo', '3', '4', '3_4', 'lkajflajfakfaljfkaf', 'fadafafdadaf', '8u20GszACM7TyFNJctYEDgX9rwp6Oe1a', NULL, NULL, '1', '2021-01-23 14:04:06'),
(126, 'LKJAFD94R', 'AUPurC1x0IT6dwqXJD9pvmWKbg3QB8Yo', '4', '6', '4_6', 'lkajflajfakfaljfkaf', 'fadafafdadaf', '8u20GszACM7TyFNJctYEDgX9rwp6Oe1a', NULL, NULL, '1', '2021-01-23 14:04:06'),
(127, 'LKJAFD94R', 'AUPurC1x0IT6dwqXJD9pvmWKbg3QB8Yo', '1', '2', '1_2', 'lkajflajfakfaljfkaf', 'fadafafdadaf', 'BKj7Nv8MXTqsfEQaoZSebY2RCthn4LWG', NULL, NULL, '1', '2021-01-23 14:04:07'),
(128, 'LKJAFD94R', 'AUPurC1x0IT6dwqXJD9pvmWKbg3QB8Yo', '3', '2', '3_2', 'lkajflajfakfaljfkaf', 'fadafafdadaf', 'BKj7Nv8MXTqsfEQaoZSebY2RCthn4LWG', NULL, NULL, '1', '2021-01-23 14:04:07'),
(129, 'LKJAFD94R', 'AUPurC1x0IT6dwqXJD9pvmWKbg3QB8Yo', '5', '2', '5_2', 'lkajflajfakfaljfkaf', 'fadafafdadaf', 'BKj7Nv8MXTqsfEQaoZSebY2RCthn4LWG', NULL, NULL, '1', '2021-01-23 14:04:07'),
(130, 'LKJAFD94R', 'AUPurC1x0IT6dwqXJD9pvmWKbg3QB8Yo', '2', '8', '2_8', 'lkajflajfakfaljfkaf', 'fadafafdadaf', 'BKj7Nv8MXTqsfEQaoZSebY2RCthn4LWG', NULL, NULL, '1', '2021-01-23 14:04:07'),
(131, 'LKJAFD94R', 'AUPurC1x0IT6dwqXJD9pvmWKbg3QB8Yo', '4', '8', '4_8', 'lkajflajfakfaljfkaf', 'fadafafdadaf', 'BKj7Nv8MXTqsfEQaoZSebY2RCthn4LWG', NULL, NULL, '1', '2021-01-23 14:04:07'),
(132, 'LKJAFD94R', 'AUPurC1x0IT6dwqXJD9pvmWKbg3QB8Yo', '1', '5', '1_5', 'lkajflajfakfaljfkaf', 'fadafafdadaf', 'fT6zyKmjYLHXVsSte5cpGuQIMNdOrxW1', NULL, NULL, '1', '2021-01-23 14:04:07'),
(133, 'LKJAFD94R', 'AUPurC1x0IT6dwqXJD9pvmWKbg3QB8Yo', '3', '5', '3_5', 'lkajflajfakfaljfkaf', 'fadafafdadaf', 'fT6zyKmjYLHXVsSte5cpGuQIMNdOrxW1', NULL, NULL, '1', '2021-01-23 14:04:07'),
(134, 'LKJAFD94R', 'AUPurC1x0IT6dwqXJD9pvmWKbg3QB8Yo', '5', '5', '5_5', 'lkajflajfakfaljfkaf', 'fadafafdadaf', 'fT6zyKmjYLHXVsSte5cpGuQIMNdOrxW1', NULL, NULL, '1', '2021-01-23 14:04:07'),
(135, 'LKJAFD94R', 'AUPurC1x0IT6dwqXJD9pvmWKbg3QB8Yo', '3', '4', '3_4', 'lkajflajfakfaljfkaf', 'fadafafdadaf', '8u20GszACM7TyFNJctYEDgX9rwp6Oe1a', NULL, NULL, '1', '2021-01-23 14:04:07'),
(149, 'LKJAFD94R', 'qEhlngB36otLKapxvISUHWduMTY4Gi8c', '2', '4', '2_4', 'lkajflajfakfaljfkaf', 'faflkdjaflkdjafd', 'afdafdafdafd', NULL, NULL, '1', '2021-01-23 17:41:05'),
(150, 'LKJAFD94R', 'qEhlngB36otLKapxvISUHWduMTY4Gi8c', '1', '6', '1_6', 'lkajflajfakfaljfkaf', 'faflkdjaflkdjafd', 'afdafdafdafd', NULL, NULL, '1', '2021-01-23 17:41:05'),
(151, 'LKJAFD94R', 'qEhlngB36otLKapxvISUHWduMTY4Gi8c', '6', '1', '6_1', 'lkajflajfakfaljfkaf', 'faflkdjaflkdjafd', 'afdafdafdafd', NULL, NULL, '1', '2021-01-23 17:41:05'),
(152, 'LKJAFD94R', 'qEhlngB36otLKapxvISUHWduMTY4Gi8c', '6', '4', '6_4', 'lkajflajfakfaljfkaf', 'faflkdjaflkdjafd', '8u20GszACM7TyFNJctYEDgX9rwp6Oe1a', NULL, NULL, '1', '2021-01-23 17:41:05'),
(153, 'LKJAFD94R', 'qEhlngB36otLKapxvISUHWduMTY4Gi8c', '6', '8', '6_8', 'lkajflajfakfaljfkaf', 'faflkdjaflkdjafd', '8u20GszACM7TyFNJctYEDgX9rwp6Oe1a', NULL, NULL, '1', '2021-01-23 17:41:05'),
(154, 'LKJAFD94R', 'qEhlngB36otLKapxvISUHWduMTY4Gi8c', '1', '9', '1_9', 'lkajflajfakfaljfkaf', 'faflkdjaflkdjafd', 'SqAtOldUajW5YGH7XyhRLP3Fi4f1QK8v', NULL, NULL, '1', '2021-01-23 17:41:05'),
(155, 'LKJAFD94R', 'qEhlngB36otLKapxvISUHWduMTY4Gi8c', '5', '9', '5_9', 'lkajflajfakfaljfkaf', 'faflkdjaflkdjafd', 'SqAtOldUajW5YGH7XyhRLP3Fi4f1QK8v', NULL, NULL, '1', '2021-01-23 17:41:05'),
(156, 'LKJAFD94R', 'qEhlngB36otLKapxvISUHWduMTY4Gi8c', '3', '5', '3_5', 'lkajflajfakfaljfkaf', 'faflkdjaflkdjafd', 'SqAtOldUajW5YGH7XyhRLP3Fi4f1QK8v', NULL, NULL, '1', '2021-01-23 17:41:05'),
(157, 'LKJAFD94R', 'qEhlngB36otLKapxvISUHWduMTY4Gi8c', '4', '3', '4_3', 'lkajflajfakfaljfkaf', 'faflkdjaflkdjafd', 'BKj7Nv8MXTqsfEQaoZSebY2RCthn4LWG', NULL, NULL, '1', '2021-01-23 17:41:05'),
(158, 'LKJAFD94R', 'qEhlngB36otLKapxvISUHWduMTY4Gi8c', '5', '6', '5_6', 'lkajflajfakfaljfkaf', 'faflkdjaflkdjafd', 'BKj7Nv8MXTqsfEQaoZSebY2RCthn4LWG', NULL, NULL, '1', '2021-01-23 17:41:05'),
(159, 'LKJAFD94R', 'qEhlngB36otLKapxvISUHWduMTY4Gi8c', '1', '1', '1_1', 'lkajflajfakfaljfkaf', 'faflkdjaflkdjafd', 'BKj7Nv8MXTqsfEQaoZSebY2RCthn4LWG', NULL, NULL, '1', '2021-01-23 17:41:05'),
(160, 'LKJAFD94R', 'qEhlngB36otLKapxvISUHWduMTY4Gi8c', '3', '2', '3_2', 'lkajflajfakfaljfkaf', 'faflkdjaflkdjafd', 'SqAtOldUajW5YGH7XyhRLP3Fi4f1QK8v', NULL, NULL, '1', '2021-01-23 17:41:05'),
(161, 'LKJAFD94R', 'qEhlngB36otLKapxvISUHWduMTY4Gi8c', '1', '4', '1_4', 'lkajflajfakfaljfkaf', 'faflkdjaflkdjafd', '8u20GszACM7TyFNJctYEDgX9rwp6Oe1a', NULL, NULL, '1', '2021-01-23 17:41:05'),
(162, 'LKJAFD94R', 'qEhlngB36otLKapxvISUHWduMTY4Gi8c', '3', '1', '3_1', 'lkajflajfakfaljfkaf', 'faflkdjaflkdjafd', '8u20GszACM7TyFNJctYEDgX9rwp6Oe1a', NULL, NULL, '1', '2021-01-23 17:41:06'),
(163, 'LKJAFD94R', 'qEhlngB36otLKapxvISUHWduMTY4Gi8c', '3', '7', '3_7', 'lkajflajfakfaljfkaf', 'faflkdjaflkdjafd', 'BKj7Nv8MXTqsfEQaoZSebY2RCthn4LWG', NULL, NULL, '1', '2021-01-23 17:41:06'),
(164, 'LKJAFD94R', 'qEhlngB36otLKapxvISUHWduMTY4Gi8c', '3', '9', '3_9', 'lkajflajfakfaljfkaf', 'faflkdjaflkdjafd', 'afdafdafdafd', NULL, NULL, '1', '2021-01-23 17:41:06'),
(165, 'LKJAFD94R', 'qEhlngB36otLKapxvISUHWduMTY4Gi8c', '5', '3', '5_3', 'lkajflajfakfaljfkaf', 'faflkdjaflkdjafd', 'afdafdafdafd', NULL, NULL, '0', '2021-01-23 17:41:06');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
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
  `guardian_id` varchar(255) DEFAULT NULL,
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
  `user_status` enum('Transferred','Active','Graduated','Dismissed') NOT NULL DEFAULT 'Active',
  `perma_image` varchar(255) DEFAULT 'assets/img/user.png',
  `user_type` enum('teacher','employee','parent','admin','student','accountant') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `upload_id`, `item_id`, `unique_id`, `client_id`, `firstname`, `lastname`, `othername`, `name`, `academic_year`, `academic_term`, `enrollment_date`, `gender`, `email`, `username`, `password`, `access_level`, `preferences`, `status`, `deleted`, `verified_email`, `last_login`, `phone_number`, `phone_number_2`, `description`, `position`, `address`, `online`, `chat_status`, `last_seen`, `nation_ids`, `date_of_birth`, `class_id`, `course_ids`, `class_ids`, `blood_group`, `religion`, `section`, `programme`, `department`, `nationality`, `occupation`, `postal_code`, `disabled`, `residence`, `employer`, `guardian_id`, `last_timetable_id`, `country`, `verify_token`, `verified_date`, `token_expiry`, `changed_password`, `account_balance`, `city`, `relationship`, `date_created`, `last_updated`, `created_by`, `image`, `previous_school`, `previous_school_qualification`, `previous_school_remarks`, `user_status`, `perma_image`, `user_type`) VALUES
(29, NULL, 'uIkajsw123456789064hxk1fc3efmnva', 'kajflkdkfafd', 'LKJAFD94R', 'Admin', 'User', 'Account', 'Admin User Account', '2019/2020', '1st', NULL, 'Male', 'test_admin@gmail.com', 'test_admin', '$2y$10$aQYMdSMFK.bxxjMLklXFTuD7.i84mep11Crrg4hAjAfv6cm1sQv9S', 8, '{\"theme_color\":\"\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"10\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-11-09 08:31:15\",\"idb_next_init\":\"2020-11-12 08:31:15\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"quotes\":\"on\",\"policies\":\"on\",\"adverts\":\"on\"},\"auto_close_modal\":\"dont\",\"text_editor\":\"trix\",\"messages\":{\"enter_to_send\":\"1\"}}', '1', '0', 'Y', '2021-01-28 16:31:22', '+233240889023', '(+233) 550-107-770', '&lt;div&gt;&lt;!--block--&gt;&lt;strong&gt;Name:&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/strong&gt;Emmanuel Obeng&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;&lt;strong&gt;Age:&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/strong&gt;28years&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;&lt;strong&gt;Programming Languages: &lt;/strong&gt;PHP, jQuery/JavaScript, SQL, HTML 5&lt;strong&gt;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&lt;/strong&gt;&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;&lt;strong&gt;Learning:&lt;/strong&gt;&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;I used YouTube channels like (Adam Khoury, Traversy, Eli the Computer Guy) to learn my preferred programing languages was through&nbsp;&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;&lt;strong&gt;Web &amp; Database Applications Development&lt;/strong&gt;&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;I have been engaged in the development of web applications and database management applications over the years. Have an in-depth knowledge in Database Management Application Development.&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;&lt;strong&gt;Skills:&lt;/strong&gt;&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;Write well designed, testable, efficient code by using best software development practices.&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;ü&nbsp; Integration of Service API’s into web applications (Payment Systems / SMS’s API’s). Development of Analytics tools using Facebook, Twitter, Instagram and LinkedIn Developer Api’s.&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;Building of Web Applications that takes into account multi-user levels and permissions. Employing the best of various use cases to control the access users of user accounts.&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;ü&nbsp; Create website layout/user interface by using standard HTML5 / CSS practices&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;ü&nbsp; Gather and refine specifications and requirements based on technical needs&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;ü&nbsp; Create and maintain software documentation.&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;ü&nbsp; Stay plugged into emerging technologies/industry trends and apply them into operations and activities.&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;&lt;strong&gt;Lists of recent applications developed&lt;/strong&gt;&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;ü Online Booking System&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; - A web application suitable for Event Managers where users can book for a particular seat for a particular event&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;ü Voting Collation System&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;- A web application for managing votes (built using the&nbsp; election structure of the Ghana EC)&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;ü Argon Point of Sale&nbsp; &nbsp; &nbsp; &nbsp;- Web based POS application with online payment&nbsp;&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;&lt;em&gt;(integrated PaySwitch Payment Aggregator)&lt;/em&gt;&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;ü PHP FileManager&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;- A web application built to aid users / groups to manage and share files.&lt;/div&gt;', 'Chief Technical Officer, Analitica Innovare', 'Accra Ghana', '1', NULL, '2021-01-28 21:37:16', NULL, '1991-11-21', '1', '[]', NULL, '4', NULL, NULL, NULL, NULL, 'Ghananaian', 'Software Developer', NULL, '0', 'Accra', 'Analitica Innovare', NULL, 'AUPurC1x0IT6dwqXJD9pvmWKbg3QB8Yo', 10, NULL, NULL, NULL, '0', '0', 'Accra', NULL, '2020-06-27 03:36:47', '2021-01-27 00:51:02', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'assets/img/user.png', NULL, NULL, NULL, 'Active', 'assets/img/user.png', 'admin'),
(33, NULL, 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'FTA000012020', 'LKJAFD94R', 'Test', 'Teacher', 'Account', 'Test Teacher Account', '2019/2020', '1st', '2020-11-01', 'Female', 'emmallob14@gmail.com', 'test_teacher', '$2y$10$aQYMdSMFK.bxxjMLklXFTuD7.i84mep11Crrg4hAjAfv6cm1sQv9S', 1, '{\"theme_color\":\"\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"10\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-11-09 08:31:15\",\"idb_next_init\":\"2020-11-12 08:31:15\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"quotes\":\"on\",\"policies\":\"on\",\"adverts\":\"on\"},\"auto_close_modal\":\"dont\",\"text_editor\":\"trix\",\"messages\":{\"enter_to_send\":\"1\"}}', '1', '0', 'N', '2021-01-28 18:59:47', '0550107770', '0203317732', 'This is the same information for the user information here and there.', '', NULL, '1', NULL, '2021-01-28 20:04:43', NULL, '1992-03-22', '2', '[\"2\",\"29\",\"30\",\"31\",\"32\"]', NULL, '3', NULL, '3', NULL, '3', NULL, NULL, NULL, '0', 'Accra', NULL, NULL, 'qEhlngB36otLKapxvISUHWduMTY4Gi8c', 84, 'McqwNLnt96KzeWD1lER4Zt8sX3usfrE0LgAFiDHPzyO6hgado5X0pJT5wSj9IexbA7', NULL, '1606460040', '0', '0', NULL, NULL, '2020-11-27 00:54:00', '2021-01-22 10:09:20', 'uIkajsw123456789064hxk1fc3efmnva', 'assets/img/users/WgAzcUqmSK__Methodist.jpg', NULL, NULL, NULL, 'Active', 'assets/images/profiles/avatar.png', 'teacher'),
(34, NULL, 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 'AGL000012020', 'LKJAFD94R', 'Solomon', 'Obeng', 'Darko', 'Solomon Obeng Darko', '2019/2020', '1st', '2020-11-08', 'Male', 'themailhereisthere@mail.com', 'test_student_2', '$2y$10$aQYMdSMFK.bxxjMLklXFTuD7.i84mep11Crrg4hAjAfv6cm1sQv9S', 1, '{\"theme_color\":\"\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"10\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-11-09 08:31:15\",\"idb_next_init\":\"2020-11-12 08:31:15\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"quotes\":\"on\",\"policies\":\"on\",\"adverts\":\"on\"},\"auto_close_modal\":\"dont\",\"text_editor\":\"trix\",\"messages\":{\"enter_to_send\":\"1\"}}', '1', '0', 'N', '2020-12-21 16:37:08', '00930993093', '0039930039930', NULL, NULL, NULL, '0', NULL, '2020-12-21 16:45:08', NULL, '2000-10-15', '1', NULL, NULL, '3', NULL, '3', NULL, '3', NULL, NULL, NULL, '0', 'that location', NULL, 'PA000032021', NULL, 234, '8wJ0RQVHF9LtETipowqsV8RdN39J3e6PT1i1rU4vDgzAPBUIxCGWusFm5S2jSXWYnvMcbbQf', NULL, '1606460673', '0', '0', NULL, NULL, '2020-11-27 01:04:33', '2020-12-17 23:00:24', 'uIkajsw123456789064hxk1fc3efmnva', 'assets/img/users/jns7h1WK2G__appimg-88bdf4ad97eeb380c2f931b768b0ad14.png', NULL, NULL, NULL, 'Active', 'assets/images/profiles/avatar.png', 'student'),
(35, NULL, 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'AGL000022020', 'LKJAFD94R', 'Grace', 'Obeng-Yeboah', 'Afia', 'Grace Obeng-Yeboah', '2019/2020', '1st', '2020-11-08', 'Male', 'graciellaob@gmail.com', 'test_student', '$2y$10$aQYMdSMFK.bxxjMLklXFTuD7.i84mep11Crrg4hAjAfv6cm1sQv9S', 1, '{\"theme_color\":\"\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"10\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-11-09 08:31:15\",\"idb_next_init\":\"2020-11-12 08:31:15\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"quotes\":\"on\",\"policies\":\"on\",\"adverts\":\"on\"},\"auto_close_modal\":\"dont\",\"text_editor\":\"trix\",\"messages\":{\"enter_to_send\":\"1\"}}', '1', '0', 'N', '2021-01-26 17:30:30', '00930993093', '0039930039930', 'This is the basic information about my self... I am confident that the information is valid and useful', NULL, NULL, '0', NULL, '2021-01-26 17:45:10', NULL, '2000-12-20', '2', NULL, NULL, '3', NULL, '3', NULL, '3', NULL, NULL, NULL, '0', 'Shiashie', NULL, 'PA000032021', 'qEhlngB36otLKapxvISUHWduMTY4Gi8c', 234, 'mKyIShrzNJYIwdnB0qUUW2DihvFSAs5b539ZOpRGlnTo74dcakOAQswfLoe0LVV', NULL, '1606460780', '0', '0', NULL, NULL, '2020-11-27 01:06:20', '2020-11-27 12:44:38', 'uIkajsw123456789064hxk1fc3efmnva', 'assets/img/user.png', NULL, NULL, NULL, 'Active', 'assets/images/profiles/avatar.png', 'student'),
(40, NULL, 'SZM14dtqDkbfn5cBl0ARgPCj287hym36', 'AGL000032020', 'LKJAFD94R', 'Emmanuella', 'Darko', 'Sarfowaa', 'Emmanuella Darko Sarfowaa', '2019/2020', '1st', '2019-06-04', 'Female', 'jauntygirl@gmail.com', 'test_student_3', '$2y$10$aQYMdSMFK.bxxjMLklXFTuD7.i84mep11Crrg4hAjAfv6cm1sQv9S', 1, '{\"theme_color\":\"\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"10\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-11-09 08:31:15\",\"idb_next_init\":\"2020-11-12 08:31:15\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"quotes\":\"on\",\"policies\":\"on\",\"adverts\":\"on\"},\"auto_close_modal\":\"dont\",\"text_editor\":\"trix\",\"messages\":{\"enter_to_send\":\"1\"}}', '1', '0', 'N', '2021-01-11 02:25:47', '0247685521', '', '', NULL, NULL, '0', NULL, '2020-12-17 22:48:33', NULL, '2001-09-04', '4', NULL, NULL, '4', '', '2', NULL, '3', NULL, NULL, NULL, '0', 'Agblezaa, Off Spintex Road', NULL, 'PA000022021,PA000012021', NULL, 84, 'cuOes44nI6yE6QLU9jVPXX0SwAwp1hfljMqd3gaYpin2rbTxcsqJkAdS3ivoQDWzIGoDV1G', NULL, '1608266913', '0', '0', NULL, NULL, '2020-12-17 22:48:33', '2021-01-19 11:10:07', 'uIkajsw123456789064hxk1fc3efmnva', 'assets/img/user.png', '', '', '', 'Active', 'assets/img/user.png', 'student'),
(41, NULL, 'ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v', 'AGL000042020', 'LKJAFD94R', 'Frank', 'Amponsah', 'Amoah', 'Frank Amponsah Amoah', '2019/2020', '1st', '2019-10-21', 'Male', 'frankamoah@gmail.com', 'test_student_4', '$2y$10$aQYMdSMFK.bxxjMLklXFTuD7.i84mep11Crrg4hAjAfv6cm1sQv9S', 1, '{\"theme_color\":\"\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"10\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-11-09 08:31:15\",\"idb_next_init\":\"2020-11-12 08:31:15\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"quotes\":\"on\",\"policies\":\"on\",\"adverts\":\"on\"},\"auto_close_modal\":\"dont\",\"text_editor\":\"trix\",\"messages\":{\"enter_to_send\":\"1\"}}', '1', '0', 'N', '2021-01-11 02:25:47', '090399309', '03993094049', 'This is the description of the student', NULL, NULL, '0', NULL, '2020-12-17 22:59:40', NULL, '1990-12-12', '2', NULL, NULL, '5', 'christian', '5', NULL, '5', NULL, NULL, NULL, '0', 'Port Harcourt', NULL, 'PA000032021', NULL, 32, 'ISif1mdadb3LEq7rxO04znYjHFLYXM1PbtKo9GGhzZOkWucgjUXs6weQaBm8P2TAcETvsFW', NULL, '1608267580', '0', '0', NULL, NULL, '2020-12-17 22:59:40', '2021-01-19 11:20:25', 'uIkajsw123456789064hxk1fc3efmnva', 'assets/img/user.png', 'test', 'test', 'test', 'Active', 'assets/img/user.png', 'student'),
(42, NULL, 'SZM14dtqcccfn5cBl0ARgPCj287hym36', 'AGL000052020', 'LKJAFD94R', 'Cecilia', 'Boateng', '', 'Cecilia Boateng', '2019/2020', '1st', '2019-06-04', 'Male', 'jauntygirl@gmail.com', 'test_student_3', '$2y$10$aQYMdSMFK.bxxjMLklXFTuD7.i84mep11Crrg4hAjAfv6cm1sQv9S', 1, '{\"theme_color\":\"\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"10\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-11-09 08:31:15\",\"idb_next_init\":\"2020-11-12 08:31:15\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"quotes\":\"on\",\"policies\":\"on\",\"adverts\":\"on\"},\"auto_close_modal\":\"dont\",\"text_editor\":\"trix\",\"messages\":{\"enter_to_send\":\"1\"}}', '1', '0', 'N', '2021-01-11 02:25:47', '0247685521', NULL, NULL, NULL, NULL, '0', NULL, '2020-12-17 22:48:33', NULL, '2001-07-10', '1', NULL, NULL, '4', NULL, '2', NULL, '3', NULL, NULL, NULL, '0', 'Agblezaa, Off Spintex Road', NULL, 'PA000032021', NULL, 84, 'cuOes44nI6yE6QLU9jVPXX0SwAwp1hfljMqd3gaYpin2rbTxcsqJkAdS3ivoQDWzIGoDV1G', NULL, '1608266913', '0', '0', NULL, NULL, '2020-12-17 22:48:33', NULL, 'uIkajsw123456789064hxk1fc3efmnva', 'assets/img/user.png', NULL, NULL, NULL, 'Active', 'assets/img/user.png', 'student'),
(44, NULL, 'SZMsssqcccfn5cBl0ARgPCj287hym36', 'AGL000062020', 'LKJAFD94R', 'Maureen', 'Anim', '', 'Maureen Anim', '2019/2020', '1st', '2019-06-04', 'Female', 'jauntygirl@gmail.com', 'test_student_3', '$2y$10$aQYMdSMFK.bxxjMLklXFTuD7.i84mep11Crrg4hAjAfv6cm1sQv9S', 1, '{\"theme_color\":\"\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"10\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-11-09 08:31:15\",\"idb_next_init\":\"2020-11-12 08:31:15\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"quotes\":\"on\",\"policies\":\"on\",\"adverts\":\"on\"},\"auto_close_modal\":\"dont\",\"text_editor\":\"trix\",\"messages\":{\"enter_to_send\":\"1\"}}', '1', '0', 'N', '2021-01-11 02:25:47', '0247685521', NULL, NULL, NULL, NULL, '0', NULL, '2020-12-17 22:48:33', NULL, '2001-11-14', '2', NULL, NULL, '4', NULL, '2', NULL, '3', NULL, NULL, NULL, '0', 'Agblezaa, Off Spintex Road', NULL, 'PA000032021', NULL, 84, 'cuOes44nI6yE6QLU9jVPXX0SwAwp1hfljMqd3gaYpin2rbTxcsqJkAdS3ivoQDWzIGoDV1G', NULL, '1608266913', '0', '0', NULL, NULL, '2020-12-17 22:48:33', NULL, 'uIkajsw123456789064hxk1fc3efmnva', 'assets/img/user.png', NULL, NULL, NULL, 'Active', 'assets/img/user.png', 'student'),
(46, NULL, 'SZMsssqcccfn5cBl0aaaPCj287hym36', 'AGL000072020', 'LKJAFD94R', 'Felicia', 'Amponsah', '', 'Felicia Amponsah', '2019/2020', '1st', '2019-06-04', 'Female', 'jauntygirl@gmail.com', 'test_student_3', '$2y$10$aQYMdSMFK.bxxjMLklXFTuD7.i84mep11Crrg4hAjAfv6cm1sQv9S', 1, '{\"theme_color\":\"\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"10\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-11-09 08:31:15\",\"idb_next_init\":\"2020-11-12 08:31:15\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"quotes\":\"on\",\"policies\":\"on\",\"adverts\":\"on\"},\"auto_close_modal\":\"dont\",\"text_editor\":\"trix\",\"messages\":{\"enter_to_send\":\"1\"}}', '1', '0', 'N', '2021-01-11 02:25:47', '0247685521', NULL, NULL, NULL, NULL, '0', NULL, '2020-12-17 22:48:33', NULL, '2001-06-14', '1', NULL, NULL, '4', NULL, '2', NULL, '3', NULL, NULL, NULL, '0', 'Agblezaa, Off Spintex Road', NULL, 'PA000022021', NULL, 84, 'cuOes44nI6yE6QLU9jVPXX0SwAwp1hfljMqd3gaYpin2rbTxcsqJkAdS3ivoQDWzIGoDV1G', NULL, '1608266913', '0', '0', NULL, NULL, '2020-12-17 22:48:33', NULL, 'uIkajsw123456789064hxk1fc3efmnva', 'assets/img/user.png', NULL, NULL, NULL, 'Active', 'assets/img/user.png', 'student'),
(47, NULL, '4aOdhDg36xX5Hr9zKwRYkPQfCLNT2Zne', 'PA000012021', 'LKJAFD94R', 'test', 'guardian', NULL, 'test guardian ', '2019/2020', '1st', NULL, 'Male', 'testemailguardian@mail.com', 'testemailguardian', '$2y$10$aQYMdSMFK.bxxjMLklXFTuD7.i84mep11Crrg4hAjAfv6cm1sQv9S', 3, '{\"payments\":{},\"default_payment\":\"mobile_money\",\"theme_color\":\"sidebar-light\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"200\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-09-18\",\"idb_next_init\":\"2020-09-21\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"policies\":\"on\",\"proposals\":\"on\"}}', '1', '0', 'N', NULL, '09098830039', '09098830039', 'the description', NULL, 'accra', '0', NULL, '2021-01-18 21:46:27', NULL, '1900-12-20', '2', NULL, NULL, 'A-', NULL, NULL, NULL, NULL, NULL, 'the occupation', NULL, '0', 'accra', 'employer', NULL, NULL, 3, '9lOePw7bfgBhKrvXVzQG2DMuG8sMzOQV3oEtY0kmC8dJAIk5H61buy7FLNplsoKREiTjL', NULL, '1611027987', '0', '0', NULL, 'Uncle', '2021-01-18 21:46:27', '2021-01-18 22:48:17', 'uIkajsw123456789064hxk1fc3efmnva', 'assets/img/user.png', NULL, NULL, NULL, 'Active', 'assets/img/user.png', 'parent'),
(50, NULL, 'kXTrgfmRsnUY13VD0LAB9ZiGuKzP46EQ', 'AGL000082021', 'LKJAFD94R', 'test', 'student', 'name', 'test student name', '2019/2020', '1st', '2021-01-14', 'Male', 'teststudent@mail.com', 'teststudent', '$2y$10$aQYMdSMFK.bxxjMLklXFTuD7.i84mep11Crrg4hAjAfv6cm1sQv9S', 1, '{\"payments\":{},\"default_payment\":\"mobile_money\",\"theme_color\":\"sidebar-light\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"200\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-09-18\",\"idb_next_init\":\"2020-09-21\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"policies\":\"on\",\"proposals\":\"on\"}}', '1', '0', 'N', NULL, '09930039930', '0039930039', 'test description', NULL, NULL, '0', NULL, '2021-01-18 23:21:28', NULL, '2004-12-01', '7', NULL, NULL, '1', 'test religion', '2', NULL, '2', NULL, NULL, NULL, '0', 'test residence', NULL, 'PA000022021', NULL, 2, 'h3fzsDixMFWtYHXC3e5Iq5v1j6TpgILEErdiQsSAZgcvl46dDVz8uN0UUnNOWb4ywH9', NULL, '1611033688', '0', '0', NULL, NULL, '2021-01-18 23:21:28', '2021-01-19 11:14:01', NULL, 'assets/img/user.png', 'test previous', 'test qualification', 'test remarks here', 'Active', 'assets/img/user.png', 'student'),
(54, NULL, '5Jz7Lv8aZ1Gi43MxDXBlfqRn9uYQjIEc', 'PA000022021', 'LKJAFD94R', 'parent', NULL, NULL, 'parent  ', NULL, NULL, NULL, 'Male', 'test parent', 'test_parent', '$2y$10$aQYMdSMFK.bxxjMLklXFTuD7.i84mep11Crrg4hAjAfv6cm1sQv9S', 6, '{\"payments\":{},\"default_payment\":\"mobile_money\",\"theme_color\":\"sidebar-light\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"200\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-09-18\",\"idb_next_init\":\"2020-09-21\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"policies\":\"on\",\"proposals\":\"on\"}}', '1', '0', 'N', '2021-01-26 17:46:37', '9930039', NULL, NULL, NULL, 'this is the test insertion', '0', NULL, '2021-01-24 13:43:51', NULL, NULL, '2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, 'AUPurC1x0IT6dwqXJD9pvmWKbg3QB8Yo', NULL, NULL, NULL, NULL, '1', '0', NULL, 'Parent', '2021-01-19 11:20:25', NULL, NULL, 'assets/img/user.png', NULL, NULL, NULL, 'Active', 'assets/img/user.png', 'parent'),
(55, NULL, 'aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe', 'FTA000022020', 'LKJAFD94R', 'Second', 'Teacher', 'Account', 'Second Teacher Account', '2019/2020', '1st', '2020-11-01', 'Male', 'emmallob14@gmail.com', 'test_teacher_2', '$2y$10$aQYMdSMFK.bxxjMLklXFTuD7.i84mep11Crrg4hAjAfv6cm1sQv9S', 1, '{\"theme_color\":\"\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"10\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-11-09 08:31:15\",\"idb_next_init\":\"2020-11-12 08:31:15\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"quotes\":\"on\",\"policies\":\"on\",\"adverts\":\"on\"},\"auto_close_modal\":\"dont\",\"text_editor\":\"trix\",\"messages\":{\"enter_to_send\":\"1\"}}', '1', '0', 'N', '2021-01-21 09:37:47', '0550107770', '0203317732', 'This is the same information for the user information here and there.', '', NULL, '1', NULL, '2021-01-21 10:21:18', NULL, '1992-03-22', '2', '[\"2\",\"SqAtOldUajW5YGH7XyhRLP3Fi4f1QK8v\",\"30\",\"BKj7Nv8MXTqsfEQaoZSebY2RCthn4LWG\",\"fT6zyKmjYLHXVsSte5cpGuQIMNdOrxW1\",\"32\"]', NULL, '3', NULL, '3', NULL, '3', NULL, NULL, NULL, '0', 'Accra', NULL, NULL, NULL, 84, 'McqwNLnt96KzeWD1lER4Zt8sX3usfrE0LgAFiDHPzyO6hgado5X0pJT5wSj9IexbA7', NULL, '1606460040', '0', '0', NULL, NULL, '2020-11-27 00:54:00', '2021-01-22 10:40:03', 'uIkajsw123456789064hxk1fc3efmnva', 'assets/img/users/WgAzcUqmSK__Methodist.jpg', NULL, NULL, NULL, 'Active', 'assets/images/profiles/avatar.png', 'teacher'),
(57, NULL, 'aaav8aZ1Gi43MxDXBlfqRn9uYQjIEc', 'PA000032021', 'LKJAFD94R', 'Parent ', '', 'Second', 'Parent Second', NULL, NULL, NULL, 'Female', 'test parent', 'test_parent_2', '$2y$10$aQYMdSMFK.bxxjMLklXFTuD7.i84mep11Crrg4hAjAfv6cm1sQv9S', 6, '{\"payments\":{},\"default_payment\":\"mobile_money\",\"theme_color\":\"sidebar-light\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"200\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-09-18\",\"idb_next_init\":\"2020-09-21\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"policies\":\"on\",\"proposals\":\"on\"}}', '1', '0', 'N', '2021-01-21 10:08:24', '9930039', NULL, NULL, NULL, 'this is the test insertion', '1', NULL, '2021-01-21 11:11:24', NULL, NULL, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', '0', NULL, 'Parent', '2021-01-19 11:20:25', NULL, NULL, 'assets/img/user.png', NULL, NULL, NULL, 'Active', 'assets/img/user.png', 'parent'),
(58, NULL, '9898989GstOi8vMW0zQ2A57nqLJZNkYe', 'FTA000032020', 'LKJAFD94R', 'Third', 'Teacher', 'Account', 'Third Teacher Account', '2019/2020', '1st', '2020-11-01', 'Male', 'emmallob14@gmail.com', 'test_teacher_3', '$2y$10$aQYMdSMFK.bxxjMLklXFTuD7.i84mep11Crrg4hAjAfv6cm1sQv9S', 1, '{\"theme_color\":\"\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"10\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-11-09 08:31:15\",\"idb_next_init\":\"2020-11-12 08:31:15\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"quotes\":\"on\",\"policies\":\"on\",\"adverts\":\"on\"},\"auto_close_modal\":\"dont\",\"text_editor\":\"trix\",\"messages\":{\"enter_to_send\":\"1\"}}', '1', '0', 'N', '2021-01-21 09:37:47', '0550107770', '0203317732', 'This is the same information for the user information here and there.', '', NULL, '1', NULL, '2021-01-21 10:21:18', NULL, '1992-03-22', '2', '[\"2\"]', NULL, '3', NULL, '3', NULL, '3', NULL, NULL, NULL, '0', 'Accra', NULL, NULL, NULL, 84, 'McqwNLnt96KzeWD1lER4Zt8sX3usfrE0LgAFiDHPzyO6hgado5X0pJT5wSj9IexbA7', NULL, '1606460040', '0', '0', NULL, NULL, '2020-11-27 00:54:00', '2021-01-22 10:40:03', 'uIkajsw123456789064hxk1fc3efmnva', 'assets/img/users/WgAzcUqmSK__Methodist.jpg', NULL, NULL, NULL, 'Active', 'assets/images/profiles/avatar.png', 'teacher'),
(59, NULL, '0011989GstOi8vMW0zQ2A57nqLJZNkYe', 'FTA000042020', 'LKJAFD94R', 'Forth', 'Teacher', 'Account', 'Forth Teacher Account', '2019/2020', '1st', '2020-11-01', 'Male', 'emmallob14@gmail.com', 'test_teacher_3', '$2y$10$aQYMdSMFK.bxxjMLklXFTuD7.i84mep11Crrg4hAjAfv6cm1sQv9S', 1, '{\"theme_color\":\"\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"previous_policies\":{},\"list_count\":\"10\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-11-09 08:31:15\",\"idb_next_init\":\"2020-11-12 08:31:15\"},\"sidebar_nav\":\"sidebar-opened\",\"new_policy_notification\":\"notify\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\",\"quotes\":\"on\",\"policies\":\"on\",\"adverts\":\"on\"},\"auto_close_modal\":\"dont\",\"text_editor\":\"trix\",\"messages\":{\"enter_to_send\":\"1\"}}', '1', '0', 'N', '2021-01-21 09:37:47', '0550107770', '0203317732', 'This is the same information for the user information here and there.', '', NULL, '1', NULL, '2021-01-21 10:21:18', NULL, '1992-03-22', '2', '[\"2\",\"29\",\"BKj7Nv8MXTqsfEQaoZSebY2RCthn4LWG\",\"fT6zyKmjYLHXVsSte5cpGuQIMNdOrxW1\",\"32\",\"30\"]', NULL, '3', NULL, '3', NULL, '3', NULL, NULL, NULL, '0', 'Accra', NULL, NULL, NULL, 84, 'McqwNLnt96KzeWD1lER4Zt8sX3usfrE0LgAFiDHPzyO6hgado5X0pJT5wSj9IexbA7', NULL, '1606460040', '0', '0', NULL, NULL, '2020-11-27 00:54:00', '2021-01-22 10:40:03', 'uIkajsw123456789064hxk1fc3efmnva', 'assets/img/users/WgAzcUqmSK__Methodist.jpg', NULL, NULL, NULL, 'Active', 'assets/images/profiles/avatar.png', 'teacher');

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
(1, '::1', 'emmallob14@gmail.com', '1', 'reset', 1, '2020-09-19 11:27:43'),
(2, '::1', 'priscilla_appiah@obeng.com', '0', 'login', 2, '2020-09-19 11:40:53'),
(3, '::1', 'emmallob14', '1', 'login', 3, '2020-09-19 16:14:59'),
(4, '::1', 'test@login.com', '0', 'login', 3, '2020-09-22 13:20:12'),
(5, '::1', 'frankamoako@gmail.com', '1', 'reset', 1, '2020-09-22 22:49:56'),
(6, '::1', 'test_admin', '1', 'login', 0, '2021-01-28 16:31:22'),
(7, '::1', 'revsolo', '1', 'login', 0, '2020-09-28 22:09:15'),
(8, '::1', 'testaccount', '0', 'login', 1, '2020-10-02 23:58:50'),
(9, '::1', 'test_broker', '1', 'login', 0, '2020-11-10 09:40:30'),
(10, '::1', 'priscilla_appiah', '0', 'login', 4, '2020-10-05 08:42:33'),
(11, '::1', 'test_user', '0', 'login', 0, '2020-11-11 19:49:50'),
(12, '::1', 'emmallob', '0', 'login', 4, '2020-10-10 09:19:24'),
(13, '::1', 'test_ic', '1', 'login', 4, '2020-11-13 12:31:55'),
(14, '::1', 'testadmin@mail.com', '0', 'login', 1, '2020-10-22 16:31:31'),
(15, '::1', 'admin@mail.com', '0', 'login', 6, '2020-11-07 14:23:09'),
(16, '::1', 'test_nic', '0', 'login', 1, '2020-12-02 01:06:33'),
(17, '::1', 'test_agent', '0', 'login', 1, '2021-01-13 00:21:34'),
(18, '::1', 'test_parent', '0', 'login', 0, '2021-01-26 17:46:37'),
(19, '::1', 'test_teacher', '1', 'login', 0, '2021-01-28 18:59:47'),
(20, '::1', 'test_stu', '0', 'login', 1, '2021-01-20 09:00:43'),
(21, '::1', 'admin', '0', 'login', 1, '2021-01-27 11:32:00'),
(22, '::1', 'admintest_', '0', 'login', 1, '2021-01-28 09:01:11');

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
(1, 'LKJAFD94R', 'WCNldYZX2k5mvjtbOKB7HQVP1F6SUsRy', 'uIkajsw123456789064hxk1fc3efmnva', 'assignments', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-26 02:05:04', 'Windows 10 | Chrome | ::1', 'Admin Account created a new Assignment: test', '1'),
(2, 'LKJAFD94R', 'pPbhxKiCyArNcqvIeoEdV68YmJsL9uOZ', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'assignments', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-26 09:43:07', 'Windows 10 | Chrome | ::1', 'Test Teacher Account created a new Assignment: Multiple Test Questions - 26th January 2021', '1'),
(3, 'LKJAFD94R', 'pPbhxKiCyArNcqvIeoEdV68YmJsL9uOZ', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'assignments', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-26 10:48:36', 'Windows 10 | Chrome | ::1', 'Test Teacher Account updated the assignment details', '1'),
(4, 'LKJAFD94R', 'pPbhxKiCyArNcqvIeoEdV68YmJsL9uOZ', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'assignments', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-26 10:48:36', 'Windows 10 | Chrome | ::1', 'Assignment description has been changed.', '1'),
(5, 'LKJAFD94R', 'ymdwiwoga59nchjqzsdslrt6fgfluxzo', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-26 11:24:22', 'Windows 10 | Chrome | ::1', '<strong>Grace Obeng-Yeboah</strong> updated the endpoint.', '1'),
(6, 'LKJAFD94R', 'ymdwiwoga59nchjqzsdslrt6fgfluxzo', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-26 11:29:13', 'Windows 10 | Chrome | ::1', '<strong>Grace Obeng-Yeboah</strong> updated the endpoint.', '1'),
(7, 'LKJAFD94R', 'ymdwiwoga59nchjqzsdslrt6fgfluxzo', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-26 11:35:49', 'Windows 10 | Chrome | ::1', '<strong>Grace Obeng-Yeboah</strong> updated the endpoint.', '1'),
(8, 'LKJAFD94R', 'ymdwiwoga59nchjqzsdslrt6fgfluxzo', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-26 11:37:19', 'Windows 10 | Chrome | ::1', '<strong>Grace Obeng-Yeboah</strong> updated the endpoint.', '1'),
(9, 'LKJAFD94R', 'a2grs865f79romhwelfcjo4ziulghbxu', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-26 11:47:14', 'Windows 10 | Chrome | ::1', '<strong>Grace Obeng-Yeboah</strong> updated the endpoint.', '1'),
(10, 'LKJAFD94R', 'pPbhxKiCyArNcqvIeoEdV68YmJsL9uOZ_xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'assignment_doc', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-26 11:47:42', 'Windows 10 | Chrome | ::1', 'Grace Obeng-Yeboah handed in the assignment for auto grading by the system.', '1'),
(11, 'LKJAFD94R', 'jNngSCaicZtfoqDG7HMvKbEpmr6x1ue8', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'assignments', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-26 14:54:23', 'Windows 10 | Chrome | ::1', 'Test Teacher Account created a new Assignment: Upload Assignment - 26th January 2021', '1'),
(12, 'LKJAFD94R', 'jNngSCaicZtfoqDG7HMvKbEpmr6x1ue8_xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'assignment_doc', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-26 15:17:13', 'Windows 10 | Chrome | ::1', 'Grace Obeng-Yeboah handed in the assignment for grading.', '1'),
(13, 'LKJAFD94R', 'jNngSCaicZtfoqDG7HMvKbEpmr6x1ue8_xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'assignment-grade', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-26 17:03:13', 'Windows 10 | Chrome | ::1', 'Test Teacher Account graded the student: 25', '1'),
(14, 'LKJAFD94R', 'ymdwiwoga59nchjqzsdslrt6fgfluxzo', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-26 17:19:20', 'Windows 10 | Chrome | ::1', '<strong>Grace Obeng-Yeboah</strong> updated the endpoint.', '1'),
(15, 'LKJAFD94R', '', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-26 18:14:25', 'Windows 10 | Chrome | ::1', '<strong>Admin Account</strong> added a new endpoint: <strong>account/download_temp</strong> to the resource: <strong>account</strong>.', '1'),
(16, 'LKJAFD94R', '82', 'uIkajsw123456789064hxk1fc3efmnva', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-26 22:13:45', 'Windows 10 | Chrome | ::1', 'Admin Account logged attendance for <strong>GENERAL ARTS 1</strong> on 2021-01-25.', '1'),
(17, 'LKJAFD94R', '82', 'uIkajsw123456789064hxk1fc3efmnva', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-26 22:13:49', 'Windows 10 | Chrome | ::1', 'Admin Account updated logged attendance for <strong>GENERAL ARTS 1</strong> on 2021-01-25.', '1'),
(18, 'LKJAFD94R', '83', 'uIkajsw123456789064hxk1fc3efmnva', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-26 22:18:22', 'Windows 10 | Chrome | ::1', 'Admin Account logged attendance for <strong>GENERAL ARTS 2</strong> on 2021-01-26.', '1'),
(19, 'LKJAFD94R', '83', 'uIkajsw123456789064hxk1fc3efmnva', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-26 22:18:25', 'Windows 10 | Chrome | ::1', 'Admin Account updated logged attendance for <strong>GENERAL ARTS 2</strong> on 2021-01-26. The record was finalized and cannot be changed again.', '1'),
(20, 'LKJAFD94R', '84', 'uIkajsw123456789064hxk1fc3efmnva', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-26 22:20:14', 'Windows 10 | Chrome | ::1', 'Admin Account logged attendance for <strong>GENERAL ARTS 2</strong> on 2021-01-25.', '1'),
(21, 'LKJAFD94R', '84', 'uIkajsw123456789064hxk1fc3efmnva', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-26 22:20:17', 'Windows 10 | Chrome | ::1', 'Admin Account updated logged attendance for <strong>GENERAL ARTS 2</strong> on 2021-01-25. The record was finalized and cannot be changed again.', '1'),
(22, 'LKJAFD94R', '85', 'uIkajsw123456789064hxk1fc3efmnva', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-26 22:20:33', 'Windows 10 | Chrome | ::1', 'Admin Account logged attendance for <strong>GENERAL ARTS 1</strong> on 2021-01-26.', '1'),
(23, 'LKJAFD94R', '85', 'uIkajsw123456789064hxk1fc3efmnva', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-26 22:20:38', 'Windows 10 | Chrome | ::1', 'Admin Account updated logged attendance for <strong>GENERAL ARTS 1</strong> on 2021-01-26. The record was finalized and cannot be changed again.', '1'),
(24, 'LKJAFD94R', '86', 'uIkajsw123456789064hxk1fc3efmnva', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-26 22:20:52', 'Windows 10 | Chrome | ::1', 'Admin Account logged attendance for <strong>VISUAL ARTS D</strong> on 2021-01-22.', '1'),
(25, 'LKJAFD94R', '87', 'uIkajsw123456789064hxk1fc3efmnva', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-26 22:20:58', 'Windows 10 | Chrome | ::1', 'Admin Account logged attendance for <strong>VISUAL ARTS D</strong> on 2021-01-25.', '1'),
(26, 'LKJAFD94R', '88', 'uIkajsw123456789064hxk1fc3efmnva', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-26 22:21:07', 'Windows 10 | Chrome | ::1', 'Admin Account logged attendance for <strong>VISUAL ARTS D</strong> on 2021-01-26.', '1'),
(27, 'LKJAFD94R', '88', 'uIkajsw123456789064hxk1fc3efmnva', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-26 22:21:10', 'Windows 10 | Chrome | ::1', 'Admin Account updated logged attendance for <strong>VISUAL ARTS D</strong> on 2021-01-26. The record was finalized and cannot be changed again.', '1'),
(28, 'LKJAFD94R', '87', 'uIkajsw123456789064hxk1fc3efmnva', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-26 22:21:17', 'Windows 10 | Chrome | ::1', 'Admin Account updated logged attendance for <strong>VISUAL ARTS D</strong> on 2021-01-25. The record was finalized and cannot be changed again.', '1'),
(29, 'LKJAFD94R', 'PA000012021', 'uIkajsw123456789064hxk1fc3efmnva', 'guardian_ward', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-26 22:27:25', 'Windows 10 | Chrome | ::1', 'Admin Account appended <strong>Emmanuella Darko Sarfowaa</strong> as a ward to <strong>test guardian </strong>.', '1'),
(30, 'LKJAFD94R', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva', 'admin_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-27 00:38:58', 'Windows 10 | Chrome | ::1', 'Name was changed from Admin Account', '1'),
(31, 'LKJAFD94R', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva', 'admin_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-27 00:38:58', 'Windows 10 | Chrome | ::1', 'Primary Contact was been changed from (+233) 550-107-770', '1'),
(32, 'LKJAFD94R', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva', 'admin_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-27 00:38:58', 'Windows 10 | Chrome | ::1', 'You updated your account information', '1'),
(33, 'LKJAFD94R', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva', 'admin_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-27 00:43:26', 'Windows 10 | Chrome | ::1', 'Primary Contact was been changed from (+233) 550-107-770', '1'),
(34, 'LKJAFD94R', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva', 'admin_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-27 00:43:26', 'Windows 10 | Chrome | ::1', 'You updated your account information', '1'),
(35, 'LKJAFD94R', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva', 'admin_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-27 00:47:58', 'Windows 10 | Chrome | ::1', 'Primary Contact was been changed from (+233) 550-107-770', '1'),
(36, 'LKJAFD94R', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva', 'admin_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-27 00:47:58', 'Windows 10 | Chrome | ::1', 'You updated your account information', '1'),
(37, 'LKJAFD94R', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva', 'admin_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-27 00:48:54', 'Windows 10 | Chrome | ::1', 'Primary Contact was been changed from (+233) 550-107-770', '1'),
(38, 'LKJAFD94R', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva', 'admin_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-27 00:48:54', 'Windows 10 | Chrome | ::1', 'You updated your account information', '1'),
(39, 'LKJAFD94R', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva', 'admin_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-27 00:51:02', 'Windows 10 | Chrome | ::1', 'User description was altered.', '1'),
(40, 'LKJAFD94R', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva', 'admin_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-27 00:51:02', 'Windows 10 | Chrome | ::1', 'Primary Contact was been changed from (+233) 550-107-770', '1'),
(41, 'LKJAFD94R', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva', 'admin_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-27 00:51:02', 'Windows 10 | Chrome | ::1', 'You updated your account information', '1'),
(42, 'LKJAFD94R', '', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-27 13:10:11', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> added a new endpoint: <strong>payroll/allowances</strong> to the resource: <strong>payroll</strong>.', '1'),
(43, 'LKJAFD94R', 'yrmmok7pj0jes3bcywphiwt9a1tfxnvx', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"149\",\"item_id\":\"yrmmok7pj0jes3bcywphiwt9a1tfxnvx\",\"version\":\"v1\",\"resource\":\"payroll\",\"endpoint\":\"payroll\\/allowances\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"employee_id\\\":\\\"required - The unique id of the employee\\\",\\\"basic_salary\\\":\\\"The basic salary of the employee\\\",\\\"allowances\\\":\\\"An array of allowances receivable by the employee\\\",\\\"deductions\\\":\\\"An array of deductions to be made from the gross salary\\\",\\\"account_name\\\":\\\"The Bank Account name\\\",\\\"bank_name\\\":\\\"The name of the bank\\\",\\\"bank_branch\\\":\\\"The bank account branch\\\",\\\"ssnit\\\":\\\"The SSNIT number of the employee\\\",\\\"tin_number\\\":\\\"The Tax Identification Number of the employee\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-01-27 13:10:11\",\"last_updated\":\"2021-01-27 13:10:11\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":null}', '2021-01-27 13:10:27', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> updated the endpoint.', '1'),
(44, 'LKJAFD94R', 'yrmmok7pj0jes3bcywphiwt9a1tfxnvx', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"149\",\"item_id\":\"yrmmok7pj0jes3bcywphiwt9a1tfxnvx\",\"version\":\"v1\",\"resource\":\"payroll\",\"endpoint\":\"payroll\\/paymentdetails\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"employee_id\\\":\\\"required - The unique id of the employee\\\",\\\"basic_salary\\\":\\\"The basic salary of the employee\\\",\\\"allowances\\\":\\\"An array of allowances receivable by the employee\\\",\\\"deductions\\\":\\\"An array of deductions to be made from the gross salary\\\",\\\"account_name\\\":\\\"The Bank Account name\\\",\\\"bank_name\\\":\\\"The name of the bank\\\",\\\"bank_branch\\\":\\\"The bank account branch\\\",\\\"ssnit\\\":\\\"The SSNIT number of the employee\\\",\\\"tin_number\\\":\\\"The Tax Identification Number of the employee\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-01-27 13:10:11\",\"last_updated\":\"2021-01-27 13:10:27\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2021-01-27 13:11:04', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> updated the endpoint.', '1'),
(45, 'LKJAFD94R', 'yrmmok7pj0jes3bcywphiwt9a1tfxnvx', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"149\",\"item_id\":\"yrmmok7pj0jes3bcywphiwt9a1tfxnvx\",\"version\":\"v1\",\"resource\":\"payroll\",\"endpoint\":\"payroll\\/paymentdetails\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"employee_id\\\":\\\"required - The unique id of the employee\\\",\\\"basic_salary\\\":\\\"The basic salary of the employee\\\",\\\"allowances\\\":\\\"An array of allowances receivable by the employee\\\",\\\"deductions\\\":\\\"An array of deductions to be made from the gross salary\\\",\\\"account_name\\\":\\\"The Bank Account name\\\",\\\"account_number\\\":\\\"The account number of the employee\\\",\\\"bank_name\\\":\\\"The name of the bank\\\",\\\"bank_branch\\\":\\\"The bank account branch\\\",\\\"ssnit\\\":\\\"The SSNIT number of the employee\\\",\\\"tin_number\\\":\\\"The Tax Identification Number of the employee\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-01-27 13:10:11\",\"last_updated\":\"2021-01-27 13:11:03\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2021-01-27 13:11:21', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> updated the endpoint.', '1'),
(46, 'LKJAFD94R', 'yrmmok7pj0jes3bcywphiwt9a1tfxnvx', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"149\",\"item_id\":\"yrmmok7pj0jes3bcywphiwt9a1tfxnvx\",\"version\":\"v1\",\"resource\":\"payroll\",\"endpoint\":\"payroll\\/paymentdetails\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"employee_id\\\":\\\"required - The unique id of the employee\\\",\\\"basic_salary\\\":\\\"The basic salary of the employee\\\",\\\"allowances\\\":\\\"An array of allowances receivable by the employee\\\",\\\"deductions\\\":\\\"An array of deductions to be made from the gross salary\\\",\\\"account_name\\\":\\\"The Bank Account name\\\",\\\"account_number\\\":\\\"The account number of the employee\\\",\\\"bank_name\\\":\\\"The name of the bank\\\",\\\"bank_branch\\\":\\\"The bank account branch\\\",\\\"ssnit_number\\\":\\\"The SSNIT number of the employee\\\",\\\"tin_number\\\":\\\"The Tax Identification Number of the employee\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-01-27 13:10:11\",\"last_updated\":\"2021-01-27 13:11:20\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2021-01-27 13:41:57', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> updated the endpoint.', '1'),
(47, 'LKJAFD94R', 'yrmmok7pj0jes3bcywphiwt9a1tfxnvx', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"149\",\"item_id\":\"yrmmok7pj0jes3bcywphiwt9a1tfxnvx\",\"version\":\"v1\",\"resource\":\"payroll\",\"endpoint\":\"payroll\\/paymentdetails\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"employee_id\\\":\\\"required - The unique id of the employee\\\",\\\"gross_salary\\\":\\\"The gross salary of the employee\\\",\\\"allowances\\\":\\\"An array of allowances receivable by the employee\\\",\\\"deductions\\\":\\\"An array of deductions to be made from the gross salary\\\",\\\"account_name\\\":\\\"The Bank Account name\\\",\\\"account_number\\\":\\\"The account number of the employee\\\",\\\"bank_name\\\":\\\"The name of the bank\\\",\\\"bank_branch\\\":\\\"The bank account branch\\\",\\\"ssnit_number\\\":\\\"The SSNIT number of the employee\\\",\\\"tin_number\\\":\\\"The Tax Identification Number of the employee\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-01-27 13:10:11\",\"last_updated\":\"2021-01-27 13:41:57\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2021-01-27 13:54:41', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> updated the endpoint.', '1'),
(48, 'LKJAFD94R', 'yrmmok7pj0jes3bcywphiwt9a1tfxnvx', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"149\",\"item_id\":\"yrmmok7pj0jes3bcywphiwt9a1tfxnvx\",\"version\":\"v1\",\"resource\":\"payroll\",\"endpoint\":\"payroll\\/paymentdetails\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"employee_id\\\":\\\"required - The unique id of the employee\\\",\\\"basic_salary\\\":\\\"The gross salary of the employee\\\",\\\"allowances\\\":\\\"An array of allowances receivable by the employee\\\",\\\"deductions\\\":\\\"An array of deductions to be made from the gross salary\\\",\\\"account_name\\\":\\\"The Bank Account name\\\",\\\"account_number\\\":\\\"The account number of the employee\\\",\\\"bank_name\\\":\\\"The name of the bank\\\",\\\"bank_branch\\\":\\\"The bank account branch\\\",\\\"ssnit_number\\\":\\\"The SSNIT number of the employee\\\",\\\"tin_number\\\":\\\"The Tax Identification Number of the employee\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-01-27 13:10:11\",\"last_updated\":\"2021-01-27 13:54:41\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2021-01-27 13:54:41', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> updated the endpoint.', '1'),
(49, 'LKJAFD94R', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva', 'salary_allowances', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '\r\n                <p><strong>Gross Salary:</strong> 7500</p>\r\n                <p><strong>Total Allowances:</strong> 1700</p>\r\n                <p><strong>Total Deductions:</strong> 2000</p>\r\n                <p><strong>Total Allowances:</strong> -300</p>\r\n                <p><strong>Basic Salary:</strong> 7200</p>', '2021-01-27 14:14:19', 'Windows 10 | Chrome | ::1', 'Admin User Account inserted the Salary Allowances of: Admin User Account', '1'),
(50, 'LKJAFD94R', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva', 'salary_allowances', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '\r\n                <p class=\'mb-0 pb-0\'><strong>Gross Salary:</strong> 7500.00 => 7800.00</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Total Allowances:</strong> 1700.00 => 2200</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Gross Salary:</strong> 9500.00 => 9800</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Total Deductions:</strong> 2000.00 => 2000</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Total Allowances:</strong> -300.00 => 200</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Basic Salary:</strong> 7200.00 => 8000</p>', '2021-01-27 14:47:03', 'Windows 10 | Chrome | ::1', 'Admin User Account updated the Salary Allowances of: Admin User Account', '1'),
(51, 'LKJAFD94R', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva', 'bank_details', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '\r\n                <p class=\'mb-0 pb-0\'><strong>Account Name:</strong> Emmanuel Obeng => Emmanuel K. Obeng</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Account Number:</strong> 10122909200390 => 10122909200390</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Bank Name:</strong> Stanbic Bank GH. Limited => Stanbic Bank Ghan Limited</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Branch:</strong> Adjiringanor Branch => Adjiringanor Branch</p>\r\n                <p class=\'mb-0 pb-0\'><strong>SSNIT No.:</strong>  => </p>\r\n                <p class=\'mb-0 pb-0\'><strong>Tin No.:</strong> 20091092002993093 => 200910920909</p>', '2021-01-27 15:03:20', 'Windows 10 | Chrome | ::1', 'Admin User Account updated the Bank Details of: Admin User Account', '1'),
(52, 'LKJAFD94R', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva', 'bank_details', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '\r\n                <p class=\'mb-0 pb-0\'><strong>Account Name:</strong> Emmanuel K. Obeng => Emmanuel K. Obeng</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Account Number:</strong> 10122909200390 => 10122909200390</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Bank Name:</strong> Stanbic Bank Ghan Limited => Stanbic Bank Ghan Limited</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Branch:</strong> Adjiringanor Branch => Adjiringanor Branch</p>\r\n                <p class=\'mb-0 pb-0\'><strong>SSNIT No.:</strong> FHA09309390 => FHA09309390</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Tin No.:</strong> 200910920909 => 200910920909</p>', '2021-01-27 15:03:49', 'Windows 10 | Chrome | ::1', 'Admin User Account updated the Bank Details of: Admin User Account', '1'),
(53, 'LKJAFD94R', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva', 'salary_allowances', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '\r\n                <p class=\'mb-0 pb-0\'><strong>Gross Salary:</strong> 7800.00 => 7800.00</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Total Allowances:</strong> 2200.00 => 2200</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Gross Salary:</strong> 9800.00 => 10000</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Total Deductions:</strong> 2000.00 => 2000</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Total Allowances:</strong> 200.00 => 200</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Basic Salary:</strong> 8000.00 => 8000</p>', '2021-01-27 15:06:43', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> updated the Salary Allowances of: <strong>Admin User Account</strong>', '1'),
(54, 'LKJAFD94R', 'aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe', 'uIkajsw123456789064hxk1fc3efmnva', 'bank_details', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-27 15:13:30', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> inserted the Bank Details of: <strong>Second Teacher Account</strong>', '1'),
(55, 'LKJAFD94R', 'aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe', 'uIkajsw123456789064hxk1fc3efmnva', 'salary_allowances', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '\r\n                <p class=\'mb-0 pb-0\'><strong>Gross Salary:</strong> 0.00 => 3200</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Total Allowances:</strong> 0.00 => 0</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Gross Salary:</strong> 0.00 => 3200</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Total Deductions:</strong> 0.00 => 0</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Total Allowances:</strong> 0.00 => 0</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Basic Salary:</strong> 0.00 => 3200</p>', '2021-01-27 15:20:04', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> updated the Salary Allowances of: <strong>Second Teacher Account</strong>', '1'),
(56, 'LKJAFD94R', 'aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe', 'uIkajsw123456789064hxk1fc3efmnva', 'salary_allowances', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '\r\n                <p class=\'mb-0 pb-0\'><strong>Gross Salary:</strong> 3200.00 => 3200</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Total Allowances:</strong> 0.00 => 0</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Gross Salary:</strong> 3200.00 => 3200</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Total Deductions:</strong> 0.00 => 0</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Total Allowances:</strong> 0.00 => 0</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Basic Salary:</strong> 3200.00 => 3200</p>', '2021-01-27 15:21:19', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> updated the Salary Allowances of: <strong>Second Teacher Account</strong>', '1'),
(57, 'LKJAFD94R', 'aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe', 'uIkajsw123456789064hxk1fc3efmnva', 'salary_allowances', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '\r\n                <p class=\'mb-0 pb-0\'><strong>Gross Salary:</strong> 3200.00 => 3200.00</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Total Allowances:</strong> 0.00 => 200</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Gross Salary:</strong> 3200.00 => 3400</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Total Deductions:</strong> 0.00 => 490</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Total Allowances:</strong> 0.00 => -290</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Basic Salary:</strong> 3200.00 => 2910</p>', '2021-01-27 15:21:37', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> updated the Salary Allowances of: <strong>Second Teacher Account</strong>', '1'),
(58, 'LKJAFD94R', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'uIkajsw123456789064hxk1fc3efmnva', 'salary_allowances', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-27 15:22:23', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> inserted the Salary Allowances of: <strong>Test Teacher Account</strong>', '1'),
(59, 'LKJAFD94R', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'uIkajsw123456789064hxk1fc3efmnva', 'bank_details', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '\r\n                <p class=\'mb-0 pb-0\'><strong>Account Name:</strong>  => Test Teacher Account</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Account Number:</strong>  => 00939930</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Bank Name:</strong>  => Accra Bank</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Branch:</strong>  => Accra</p>\r\n                <p class=\'mb-0 pb-0\'><strong>SSNIT No.:</strong>  => FAKD8859958948</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Tin No.:</strong>  => 994994994445</p>', '2021-01-27 15:22:49', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> updated the Bank Details of: <strong>Test Teacher Account</strong>', '1'),
(60, 'LKJAFD94R', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'uIkajsw123456789064hxk1fc3efmnva', 'salary_allowances', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '\r\n                <p class=\'mb-0 pb-0\'><strong>Gross Salary:</strong> 2300.00 => 3300.00</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Total Allowances:</strong> 100.00 => 100</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Gross Salary:</strong> 2400.00 => 3400</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Total Deductions:</strong> 427.00 => 427</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Total Allowances:</strong> -327.00 => -327</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Basic Salary:</strong> 1973.00 => 2973</p>', '2021-01-27 15:23:01', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> updated the Salary Allowances of: <strong>Test Teacher Account</strong>', '1'),
(61, 'LKJAFD94R', '9898989GstOi8vMW0zQ2A57nqLJZNkYe', 'uIkajsw123456789064hxk1fc3efmnva', 'bank_details', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-27 15:23:45', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> inserted the Bank Details of: <strong>Third Teacher Account</strong>', '1'),
(62, 'LKJAFD94R', '9898989GstOi8vMW0zQ2A57nqLJZNkYe', 'uIkajsw123456789064hxk1fc3efmnva', 'salary_allowances', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '\r\n                <p class=\'mb-0 pb-0\'><strong>Gross Salary:</strong> 0.00 => 3209</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Total Allowances:</strong> 0.00 => 850</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Gross Salary:</strong> 0.00 => 4059</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Total Deductions:</strong> 0.00 => 750</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Total Allowances:</strong> 0.00 => 100</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Basic Salary:</strong> 0.00 => 3309</p>', '2021-01-27 15:24:18', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> updated the Salary Allowances of: <strong>Third Teacher Account</strong>', '1'),
(63, 'LKJAFD94R', '', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-27 17:25:04', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> added a new endpoint: <strong>payroll/payslipdetails</strong> to the resource: <strong>payroll</strong>.', '1'),
(64, 'LKJAFD94R', '', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-27 19:50:08', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> added a new endpoint: <strong>payroll/generatepayslip</strong> to the resource: <strong>payroll</strong>.', '1'),
(65, 'LKJAFD94R', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '\r\n            <p class=\'mb-0 pb-0\'><strong>Basic Salary:</strong> 7800.00 => 7800</p>\r\n            <p class=\'mb-0 pb-0\'><strong>Total Allowances:</strong> 2200.00 => 2200</p>\r\n            <p class=\'mb-0 pb-0\'><strong>Gross Salary:</strong> 10000.00 => 10000</p>\r\n            <p class=\'mb-0 pb-0\'><strong>Total Deductions:</strong> 2000.00 => 2000</p>\r\n            <p class=\'mb-0 pb-0\'><strong>Net Salary:</strong> 8000.00 => 8000</p>', '2021-01-27 21:56:11', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> updated the payslip for: <strong></strong> for the month: <strong>January 2021</strong>', '1'),
(66, 'LKJAFD94R', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '\r\n            <p class=\'mb-0 pb-0\'><strong>Basic Salary:</strong> 7800.00 => 7800</p>\r\n            <p class=\'mb-0 pb-0\'><strong>Total Allowances:</strong> 2200.00 => 2200</p>\r\n            <p class=\'mb-0 pb-0\'><strong>Gross Salary:</strong> 10000.00 => 10000</p>\r\n            <p class=\'mb-0 pb-0\'><strong>Total Deductions:</strong> 2000.00 => 2000</p>\r\n            <p class=\'mb-0 pb-0\'><strong>Net Salary:</strong> 8000.00 => 8000</p>', '2021-01-27 21:56:42', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> updated the payslip for: <strong>Admin User Account</strong> for the month: <strong>January 2021</strong>', '1'),
(67, 'LKJAFD94R', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '\r\n            <p class=\'mb-0 pb-0\'><strong>Basic Salary:</strong> 7800.00 => 7800</p>\r\n            <p class=\'mb-0 pb-0\'><strong>Total Allowances:</strong> 2200.00 => 2200</p>\r\n            <p class=\'mb-0 pb-0\'><strong>Gross Salary:</strong> 10000.00 => 10000</p>\r\n            <p class=\'mb-0 pb-0\'><strong>Total Deductions:</strong> 2000.00 => 2000</p>\r\n            <p class=\'mb-0 pb-0\'><strong>Net Salary:</strong> 8000.00 => 8000</p>', '2021-01-27 21:59:13', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> updated the payslip for: <strong>Admin User Account</strong> for the month: <strong>January 2021</strong>', '1'),
(68, 'LKJAFD94R', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-27 22:03:42', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> generated a payslip for: <strong></strong> for the month: <strong>January 2021</strong>', '1'),
(69, 'LKJAFD94R', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-27 22:04:51', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> generated a payslip for: <strong>Admin User Account</strong> for the month: <strong>January 2021</strong>', '1'),
(70, 'LKJAFD94R', '', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-28 09:17:52', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> added a new endpoint: <strong>paysliplist</strong> to the resource: <strong>payroll</strong>.', '1'),
(71, 'LKJAFD94R', 'cvbxjrq7wrg3cmbakpd4jogohyeql9st', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"152\",\"item_id\":\"cvbxjrq7wrg3cmbakpd4jogohyeql9st\",\"version\":\"v1\",\"resource\":\"payroll\",\"endpoint\":\"paysliplist\",\"method\":\"GET\",\"description\":\"\",\"parameter\":\"{\\\"employee_id\\\":\\\"The unique id of the employee\\\",\\\"month_id\\\":\\\"The month to load\\\",\\\"year_id\\\":\\\"The year of the payslip\\\",\\\"created_by\\\":\\\"The unique id of the one who created the payslip\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-01-28 09:17:52\",\"last_updated\":\"2021-01-28 09:17:52\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":null}', '2021-01-28 09:18:12', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> updated the endpoint.', '1'),
(72, 'LKJAFD94R', 'cvbxjrq7wrg3cmbakpd4jogohyeql9st', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"152\",\"item_id\":\"cvbxjrq7wrg3cmbakpd4jogohyeql9st\",\"version\":\"v1\",\"resource\":\"payroll\",\"endpoint\":\"payroll\\/paysliplist\",\"method\":\"GET\",\"description\":\"\",\"parameter\":\"{\\\"employee_id\\\":\\\"The unique id of the employee\\\",\\\"month_id\\\":\\\"The month to load\\\",\\\"year_id\\\":\\\"The year of the payslip\\\",\\\"created_by\\\":\\\"The unique id of the one who created the payslip\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-01-28 09:17:52\",\"last_updated\":\"2021-01-28 09:18:11\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2021-01-28 09:18:38', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> updated the endpoint.', '1'),
(73, 'LKJAFD94R', 'cvbxjrq7wrg3cmbakpd4jogohyeql9st', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"152\",\"item_id\":\"cvbxjrq7wrg3cmbakpd4jogohyeql9st\",\"version\":\"v1\",\"resource\":\"payroll\",\"endpoint\":\"paysliplist\",\"method\":\"GET\",\"description\":\"\",\"parameter\":\"{\\\"employee_id\\\":\\\"The unique id of the employee\\\",\\\"month_id\\\":\\\"The month to load\\\",\\\"year_id\\\":\\\"The year of the payslip\\\",\\\"created_by\\\":\\\"The unique id of the one who created the payslip\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-01-28 09:17:52\",\"last_updated\":\"2021-01-28 09:18:38\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2021-01-28 09:18:48', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> updated the endpoint.', '1'),
(74, 'LKJAFD94R', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'uIkajsw123456789064hxk1fc3efmnva', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-28 09:22:04', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> generated a payslip for: <strong>Test Teacher Account</strong> for the month: <strong>January 2021</strong>', '1'),
(75, 'LKJAFD94R', '', 'uIkajsw123456789064hxk1fc3efmnva', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-28 09:54:16', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> added a new endpoint: <strong>records/validate</strong> to the resource: <strong>records</strong>.', '1'),
(76, 'LKJAFD94R', '2', 'uIkajsw123456789064hxk1fc3efmnva', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-28 16:31:30', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> validated the payslip: <strong></strong> for the month: <strong> </strong>', '1'),
(77, 'LKJAFD94R', '2', 'uIkajsw123456789064hxk1fc3efmnva', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-28 16:32:56', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> validated the payslip: <strong>Test Teacher Account</strong> for the month: <strong>January 2021</strong>', '1'),
(78, 'LKJAFD94R', '1', 'uIkajsw123456789064hxk1fc3efmnva', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-28 16:35:23', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> validated the payslip: <strong>Admin User Account</strong> for the month: <strong>January 2021</strong>', '1'),
(79, 'LKJAFD94R', 'aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe', 'uIkajsw123456789064hxk1fc3efmnva', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-28 17:10:50', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> generated a payslip for: <strong>Second Teacher Account</strong> for the month: <strong>December 2020</strong>', '1'),
(80, 'LKJAFD94R', '3', 'uIkajsw123456789064hxk1fc3efmnva', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-28 17:10:55', 'Windows 10 | Chrome | ::1', '<strong>Admin User Account</strong> validated the payslip: <strong>Second Teacher Account</strong> for the month: <strong>December 2020</strong>', '1'),
(81, 'LKJAFD94R', 'tLMgeV3b6soXaFylhB2rRzCA8QG0HimZ', 'uIkajsw123456789064hxk1fc3efmnva', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-28 18:29:27', 'Windows 10 | Chrome | ::1', 'Admin User Account received an amount of \r\n                <strong>230</strong> as Payment for <strong>Hostel Fees</strong> from <strong>Solomon Obeng Darko</strong>. \r\n                Outstanding Balance is <strong>370</strong>', '1'),
(82, 'LKJAFD94R', 'wRi0c3ZsyhD5MgFlSb7vEz8jmGCToXPn', 'uIkajsw123456789064hxk1fc3efmnva', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-28 18:32:55', 'Windows 10 | Chrome | ::1', 'Admin User Account received an amount of \r\n                <strong>300</strong> as Payment for <strong>Hostel Fees</strong> from <strong>Cecilia Boateng</strong>. \r\n                Outstanding Balance is <strong>300</strong>', '1'),
(83, 'LKJAFD94R', '89', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-28 19:10:38', 'Windows 10 | Chrome | ::1', 'Test Teacher Account logged attendance for <strong>GENERAL ARTS 1</strong> on 2021-01-28.', '1'),
(84, 'LKJAFD94R', '89', 'uIkajsw123456789064hxk1fc3efmnva', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\\\",\\\"SZM14dtqcccfn5cBl0ARgPCj287hym36\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\\\",\\\"unique_id\\\":\\\"AGL000012020\\\",\\\"name\\\":\\\"Solomon Obeng Darko\\\",\\\"email\\\":\\\"themailhereisthere@mail.com\\\",\\\"phone_number\\\":\\\"00930993093\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"SZM14dtqcccfn5cBl0ARgPCj287hym36\\\",\\\"unique_id\\\":\\\"AGL000052020\\\",\\\"name\\\":\\\"Cecilia Boateng\\\",\\\"email\\\":\\\"jauntygirl@gmail.com\\\",\\\"phone_number\\\":\\\"0247685521\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"SZMsssqcccfn5cBl0aaaPCj287hym36\\\",\\\"unique_id\\\":\\\"AGL000072020\\\",\\\"name\\\":\\\"Felicia Amponsah\\\",\\\"email\\\":\\\"jauntygirl@gmail.com\\\",\\\"phone_number\\\":\\\"0247685521\\\",\\\"state\\\":\\\"absent\\\"}]\",\"finalize\":\"0\"}', '2021-01-28 20:05:30', 'Windows 10 | Chrome | ::1', 'Admin User Account updated logged attendance for <strong>GENERAL ARTS 1</strong> on 2021-01-28. The record was finalized and cannot be changed again.', '1'),
(85, 'LKJAFD94R', '90', 'uIkajsw123456789064hxk1fc3efmnva', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-01-28 20:13:09', 'Windows 10 | Chrome | ::1', 'Admin User Account logged attendance for <strong>teacher</strong> on 2021-01-26.', '1'),
(86, 'LKJAFD94R', '90', 'uIkajsw123456789064hxk1fc3efmnva', 'attendance_log', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"users_list\":\"[\\\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\\\",\\\"aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe\\\",\\\"9898989GstOi8vMW0zQ2A57nqLJZNkYe\\\"]\",\"users_data\":\"[{\\\"item_id\\\":\\\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\\\",\\\"unique_id\\\":\\\"FTA000012020\\\",\\\"name\\\":\\\"Test Teacher Account\\\",\\\"email\\\":\\\"emmallob14@gmail.com\\\",\\\"phone_number\\\":\\\"0550107770\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe\\\",\\\"unique_id\\\":\\\"FTA000022020\\\",\\\"name\\\":\\\"Second Teacher Account\\\",\\\"email\\\":\\\"emmallob14@gmail.com\\\",\\\"phone_number\\\":\\\"0550107770\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"9898989GstOi8vMW0zQ2A57nqLJZNkYe\\\",\\\"unique_id\\\":\\\"FTA000032020\\\",\\\"name\\\":\\\"Third Teacher Account\\\",\\\"email\\\":\\\"emmallob14@gmail.com\\\",\\\"phone_number\\\":\\\"0550107770\\\",\\\"state\\\":\\\"present\\\"},{\\\"item_id\\\":\\\"0011989GstOi8vMW0zQ2A57nqLJZNkYe\\\",\\\"unique_id\\\":\\\"FTA000042020\\\",\\\"name\\\":\\\"Forth Teacher Account\\\",\\\"email\\\":\\\"emmallob14@gmail.com\\\",\\\"phone_number\\\":\\\"0550107770\\\",\\\"state\\\":\\\"absent\\\"}]\",\"finalize\":\"0\"}', '2021-01-28 20:13:14', 'Windows 10 | Chrome | ::1', 'Admin User Account updated logged attendance for <strong>teacher</strong> on 2021-01-26. The record was finalized and cannot be changed again.', '1');

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
(74, 'su5mijqfenc72vum40wokop9cvtfwbil', 'v1', 'classes', 'classes/list', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"q\":\"A search term for the class name\",\"class_teacher\":\"The unique id of the class teacher\",\"department_id\":\"The department id of the class to load\",\"class_id\":\"The unique id of the class\",\"class_assistant\":\"The unique id of the class assistant\",\"columns\":\"This lists only the requested columns\",\"load_courses\":\"Optionally use to load the courses for this class\",\"load_rooms\":\"Optionally use to load the classrooms for this class\"}', 'active', 0, '2020-11-27 23:02:12', '2021-01-22 15:15:57', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(75, 'r58fkmqxb7rezo0euaj1umiqp9snywhd', 'v1', 'departments', 'departments/list', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"q\":\"A search term for the class name\",\"department_head\":\"The unique id of the department head\",\"department_id\":\"The unique id of the department\"}', 'active', 0, '2020-11-27 23:03:17', '2020-11-27 23:03:28', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(76, 'ljzganfuczysikqtrq1heodi3h6mw48v', 'v1', 'departments', 'departments/add', 'POST', '', '{\"department_code\":\"The department code\",\"name\":\"required - The name of the department\",\"image\":\"The department logo if any\",\"description\":\"A sample description of the department\",\"department_head\":\"The unique id of the department head\"}', 'active', 0, '2020-11-27 23:05:18', '2020-11-27 23:05:18', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(77, 'aw3d0vg12o6cszyfmiqvyt4eehx8fpwx', 'v1', 'departments', 'departments/update', 'POST', '', '{\"department_code\":\"The department code\",\"name\":\"required - The name of the department\",\"image\":\"The department logo if any\",\"description\":\"A sample description of the department\",\"department_head\":\"The unique id of the department head\",\"department_id\":\"required - The id of the department to update\"}', 'active', 0, '2020-11-27 23:05:57', '2020-11-27 23:05:57', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(78, '82uynfw7k90hpcwrkxqzrpylttmibajx', 'v1', 'sections', 'sections/update', 'POST', '', '{\"section_code\":\"The unique section code\",\"name\":\"required - The name of the section\",\"section_leader\":\"The unique id of the section leader\",\"description\":\"The description of the class (optional)\",\"section_id\":\"required - The unique of the section to update\"}', 'active', 0, '2020-11-27 23:07:30', '2020-11-27 23:07:30', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(79, 'yrlboe2l8atxuzbiz1kgcm6gntpvsu9d', 'v1', 'sections', 'sections/add', 'POST', '', '{\"section_code\":\"The unique section code\",\"name\":\"required - The name of the section\",\"section_leader\":\"The unique id of the section leader\",\"description\":\"The description of the class (optional)\"}', 'active', 0, '2020-11-27 23:07:55', '2020-11-27 23:07:55', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(80, 'ldvjfzjzkbaly13e5ksbxrf06a2p4qm8', 'v1', 'sections', 'sections/list', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"section_id\":\"The section id to load\",\"section_leader\":\"The unique id of the section leader\"}', 'active', 0, '2020-11-27 23:08:35', '2020-11-27 23:08:35', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(81, 'kfendr2by6aae08trixsxzu71odwpjov', 'v1', 'courses', 'courses/list', 'GET', '', '{\"limit\":\"The number of rows to return\",\"department_id\":\"The department id to fetch the courses offered\",\"course_tutor\":\"The unique id of the course tutor\",\"class_id\":\"The unique id of the class offering the course\",\"course_id\":\"The unique id of the course\",\"full_details\":\"A request for full information\",\"full_attachments\":\"This parameters loads all attachments for the course (all unit/lesson) attachments\",\"minified\":\"Just run a small set of query.\"}', 'active', 0, '2020-11-28 10:12:44', '2021-01-20 19:45:16', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(82, 'yz7euod8acemowbhtxp3gr6t0mjblakx', 'v1', 'courses', 'courses/add', 'POST', '', '{\"name\":\"required - The title of the course\",\"course_code\":\"required - The unique code of the course\",\"credit_hours\":\"The number of credit hours for the course\",\"class_id\":\"The unique id of the class offering this course\",\"course_tutor\":\"The unique id of the course tutor\",\"description\":\"The description or course content\",\"academic_year\":\"The academic year for this course\",\"academic_term\":\"The academic term for this course\",\"course_id\":\"Optional\",\"weekly_meeting\":\"The number of times this course is held in a week\"}', 'active', 0, '2020-11-28 10:16:58', '2021-01-22 22:04:09', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(83, '7vljhpahe0bgyc2mprfnyjsf1gvb6lzt', 'v1', 'courses', 'courses/update', 'POST', '', '{\"name\":\"required - The title of the course\",\"course_code\":\"required - The unique code of the course\",\"credit_hours\":\"The number of credit hours for the course\",\"class_id\":\"The unique id of the class offering this course\",\"course_tutor\":\"The unique id of the course tutor\",\"description\":\"The description or course content\",\"academic_year\":\"The academic year for this course\",\"academic_term\":\"The academic term for this course\",\"course_id\":\"required - The id of the course to update\",\"weekly_meeting\":\"The number of times this course is held in a week\"}', 'active', 0, '2020-11-28 10:17:22', '2021-01-22 22:04:13', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
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
(124, 'mfxzhx0vt3oo5jnbtekv8waefzrusdwl', 'v1', 'library', 'library/list', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"department_id\":\"The unique id of the department\",\"class_id\":\"The unique id of the class\",\"category_id\":\"The category under which this book falls\",\"description\":\"The summary description of the book\",\"book_id\":\"The unique id of the book to update\",\"isbn\":\"The unique identification code for the book\",\"show_in_list\":\"This is applicable if the user wants to ascertain whether the book has been added in a session to be issued out or requested.\",\"minified\":\"If parsed then the result will be simplified\"}', 'active', 0, '2021-01-02 12:55:28', '2021-01-04 20:09:35', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(125, 'jwn17zfsaecz43ih96ouqyfiela2hv5k', 'v1', 'library', 'library/upload_resource', 'POST', '', '{\"book_id\":\"required - The book id to upload the files to\"}', 'active', 0, '2021-01-02 21:24:24', '2021-01-02 21:24:24', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL);
INSERT INTO `users_api_endpoints` (`id`, `item_id`, `version`, `resource`, `endpoint`, `method`, `description`, `parameter`, `status`, `counter`, `date_created`, `last_updated`, `deleted`, `deprecated`, `added_by`, `updated_by`) VALUES
(126, 'duts60wylhukvkdfimwpbtcp9lhoszya', 'v1', 'library', 'library/update_category', 'POST', '', '{\"name\":\"required - The title of the category\",\"department_id\":\"The department of the book category\",\"description\":\"The description of the category\",\"category_id\":\"required - The unique id of the category to update.\"}', 'active', 0, '2021-01-03 22:46:04', '2021-01-03 22:46:04', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(127, 'eagq8tdpnik0yvrjvobwscbuz3z4r9d7', 'v1', 'library', 'library/add_category', 'POST', '', '{\"name\":\"required - The title of the category\",\"department_id\":\"The department of the book category\",\"description\":\"The description of the category\"}', 'active', 0, '2021-01-03 22:46:37', '2021-01-03 22:46:37', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(128, 'sggcjcpqyzuphbuvw9ijdwxq7e5forto', 'v1', 'library', 'library/issue_request_handler', 'POST', '', '{\"label\":\"required - An array that contains the request to perform. Parameters: todo - add, remove, request and issue / book_id - Required if the todo is either add or remove.\"}', 'active', 0, '2021-01-04 21:05:19', '2021-01-04 21:18:57', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(129, 'jcz6d2qh85bfkpewit3zqcw1nab7m0jy', 'v1', 'library', 'library/issued_request_list', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"borrowed_id\":\"The unique id of the borrowed id\",\"user_id\":\"The unique id of the user who requested for the books\",\"return_date\":\"Filter by the date on which books are to be returned\",\"issued_date\":\"Filter by the date on which the books were issued\",\"status\":\"Filter by the status of the request\",\"show_list\":\"This when appended while show the details of the book borrowed\"}', 'active', 0, '2021-01-06 08:16:35', '2021-01-06 08:17:49', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(130, 'aeqtpxgzy5ho3v8cldtsiprb47zcu0fq', 'v1', 'fees', 'fees/list', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"student_id\":\"The unique id of the student to load the record\",\"class_id\":\"The unique id of the class to load the record\",\"academic_year\":\"The academic year to load the information\",\"academic_term\":\"The academic term to load the information\"}', 'active', 0, '2021-01-08 07:43:05', '2021-01-08 07:43:05', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(131, 'fcgwydlvdkss1tivtx2uro9pbma5zqyk', 'v1', 'fees', 'fees/payment_form', 'GET', '', '{\"department_id\":\"This is the unique id of the department\",\"class_id\":\"This is the unique id of the class of the student\",\"student_id\":\"The unique id of the student\",\"category_id\":\"The fees category type to load\",\"show_history\":\"When submitted in the query, the result will contain the payment history of the student (if supplied)\"}', 'active', 0, '2021-01-08 11:43:35', '2021-01-08 11:43:35', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(132, 'd6kwitjdx81rnocfabuefoqywtrvsb59', 'v1', 'fees', 'fees/allocate_fees', 'POST', '', '{\"allocate_to\":\"required - This specifies whether to allot the fees to the class or student\",\"amount\":\"required - This is the amount.\",\"category_id\":\"required - This is the category id of the fees type\",\"student_id\":\"This is only needed if the allocate_to is equal to student.\",\"class_id\":\"This is required for insertion. If not specified, the said fees will be allotted to all active classes in the database.\"}', 'active', 0, '2021-01-08 16:19:16', '2021-01-08 16:20:11', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(133, '5gnulblaojzrmyke1chz3yivchsxp6gu', 'v1', 'fees', 'fees/allocate_fees_amount', 'GET', 'Get the fees allotted a class or student', '{\"allocate_to\":\"required - This specifies whether to allot the fees to the class or student\",\"category_id\":\"required - This is the category id of the fees type\",\"student_id\":\"This is only needed if the allocate_to is equal to student.\",\"class_id\":\"This is required for insertion. If not specified, the said fees will be allotted to all active classes in the database.\"}', 'active', 0, '2021-01-08 21:12:22', '2021-01-08 21:25:46', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(134, 'blgc8rfdehuqmcq6iiohygn0trv7uevt', 'v1', 'fees', 'fees/make_payment', 'POST', '', '{\"checkout_url\":\"required - This is the checkout url for making payments\",\"payment_mode\":\"The mode for making the payment\",\"amount\":\"required - This is the amount to be made.\",\"description\":\"The description for the payment (optional)\"}', 'active', 0, '2021-01-09 19:22:02', '2021-01-09 19:22:44', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
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
(153, 'vdljdyuqcwtgmhnibxzgja69x7r0e8kz', 'v1', 'records', 'records/validate', 'POST', '', '{\"label\":\"required - An array of actions to perform.\"}', 'active', 0, '2021-01-28 09:54:16', '2021-01-28 09:54:16', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_api_keys`
--

DROP TABLE IF EXISTS `users_api_keys`;
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
(1, 'LKJAFD94R', 'student', '1', '[\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"SZM14dtqcccfn5cBl0ARgPCj287hym36\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000032020\",\"name\":\"Emmanuella Darko Sarfowaa\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000042020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"}]', '2020-12-02', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 08:28:29', '1', '1', '2020-12-29 08:29:09', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(2, 'LKJAFD94R', 'student', '1', '[\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"SZMsssqcccfn5cBl0aaaPCj287hym36\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000032020\",\"name\":\"Emmanuella Darko Sarfowaa\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000042020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2020-12-01', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 08:28:38', '1', '1', '2020-12-29 08:29:04', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(3, 'LKJAFD94R', 'student', '1', '[\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"SZM14dtqcccfn5cBl0ARgPCj287hym36\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"absent\"},{\"item_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000032020\",\"name\":\"Emmanuella Darko Sarfowaa\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000042020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"}]', '2020-11-30', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 08:28:50', '1', '1', '2020-12-29 08:28:56', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(4, 'LKJAFD94R', 'student', '1', '[\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"SZM14dtqcccfn5cBl0ARgPCj287hym36\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"absent\"},{\"item_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000032020\",\"name\":\"Emmanuella Darko Sarfowaa\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000042020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"holiday\"}]', '2020-12-03', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 08:29:17', '1', '1', '2020-12-29 08:29:21', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(5, 'LKJAFD94R', 'student', '2', '[\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\",\"SZMsssqcccfn5cBl0ARgPCj287hym36\"]', '[{\"item_id\":\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"unique_id\":\"AGL000022020\",\"name\":\"Grace Obeng-Yeboah\",\"email\":\"graciellaob@gmail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\",\"unique_id\":\"AGL000042020\",\"name\":\"Frank Amponsah Amoah\",\"email\":\"frankamoah@gmail.com\",\"phone_number\":null,\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Maureen Anim\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2020-12-03', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 08:30:15', '1', '1', '2020-12-29 08:30:18', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(6, 'LKJAFD94R', 'student', '2', '[\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\"]', '[{\"item_id\":\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"unique_id\":\"AGL000022020\",\"name\":\"Grace Obeng-Yeboah\",\"email\":\"graciellaob@gmail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\",\"unique_id\":\"AGL000042020\",\"name\":\"Frank Amponsah Amoah\",\"email\":\"frankamoah@gmail.com\",\"phone_number\":null,\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Maureen Anim\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"}]', '2020-12-02', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 08:30:27', '1', '1', '2020-12-29 08:30:30', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(7, 'LKJAFD94R', 'student', '2', '[\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\",\"SZMsssqcccfn5cBl0ARgPCj287hym36\"]', '[{\"item_id\":\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"unique_id\":\"AGL000022020\",\"name\":\"Grace Obeng-Yeboah\",\"email\":\"graciellaob@gmail.com\",\"phone_number\":\"00930993093\",\"state\":\"absent\"},{\"item_id\":\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\",\"unique_id\":\"AGL000042020\",\"name\":\"Frank Amponsah Amoah\",\"email\":\"frankamoah@gmail.com\",\"phone_number\":null,\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Maureen Anim\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2020-12-01', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 08:30:35', '1', '1', '2020-12-29 08:30:38', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(8, 'LKJAFD94R', 'student', '2', '[\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\"]', '[{\"item_id\":\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"unique_id\":\"AGL000022020\",\"name\":\"Grace Obeng-Yeboah\",\"email\":\"graciellaob@gmail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\",\"unique_id\":\"AGL000042020\",\"name\":\"Frank Amponsah Amoah\",\"email\":\"frankamoah@gmail.com\",\"phone_number\":null,\"state\":\"absent\"},{\"item_id\":\"SZMsssqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Maureen Anim\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"}]', '2020-12-04', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 08:30:54', '1', '1', '2020-12-29 08:30:58', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(9, 'LKJAFD94R', 'student', '1', '[\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"SZMsssqcccfn5cBl0aaaPCj287hym36\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000032020\",\"name\":\"Emmanuella Darko Sarfowaa\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"holiday\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000042020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"late\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2020-12-04', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 08:47:23', '1', '1', '2020-12-29 08:47:28', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(10, 'LKJAFD94R', 'student', '1', '[\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000032020\",\"name\":\"Emmanuella Darko Sarfowaa\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"late\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000042020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"late\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"}]', '2020-12-07', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 08:53:18', '1', '1', '2020-12-29 08:53:23', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(11, 'LKJAFD94R', 'admin', NULL, '[\"uIkajsw123456789064hxk1fc3efmnva\"]', '[{\"item_id\":\"uIkajsw123456789064hxk1fc3efmnva\",\"unique_id\":\"kajflkdkfafd\",\"name\":\"Admin Account\",\"email\":\"test_admin@gmail.com\",\"phone_number\":\"+233240889023\",\"state\":\"present\"}]', '2020-12-02', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 09:16:49', '1', '1', '2020-12-29 09:18:48', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(12, 'LKJAFD94R', 'admin', NULL, '[\"uIkajsw123456789064hxk1fc3efmnva\"]', '[{\"item_id\":\"uIkajsw123456789064hxk1fc3efmnva\",\"unique_id\":\"kajflkdkfafd\",\"name\":\"Admin Account\",\"email\":\"test_admin@gmail.com\",\"phone_number\":\"+233240889023\",\"state\":\"present\"}]', '2020-12-03', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 09:18:29', '1', '1', '2020-12-29 09:18:53', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(13, 'LKJAFD94R', 'admin', NULL, '[\"uIkajsw123456789064hxk1fc3efmnva\"]', '[{\"item_id\":\"uIkajsw123456789064hxk1fc3efmnva\",\"unique_id\":\"kajflkdkfafd\",\"name\":\"Admin Account\",\"email\":\"test_admin@gmail.com\",\"phone_number\":\"+233240889023\",\"state\":\"present\"}]', '2020-12-01', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 09:18:38', '1', '1', '2020-12-29 09:18:41', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(14, 'LKJAFD94R', 'teacher', NULL, '[\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\"]', '[{\"item_id\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000012020\",\"name\":\"Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"}]', '2020-12-03', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 09:19:00', '1', '1', '2020-12-29 09:19:27', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(15, 'LKJAFD94R', 'teacher', NULL, '[\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\"]', '[{\"item_id\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000012020\",\"name\":\"Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"}]', '2020-12-02', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 09:19:06', '1', '1', '2020-12-29 09:19:11', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(16, 'LKJAFD94R', 'teacher', NULL, '[\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\"]', '[{\"item_id\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000012020\",\"name\":\"Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"}]', '2020-12-01', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 09:19:15', '1', '1', '2020-12-29 09:19:18', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(17, 'LKJAFD94R', 'teacher', NULL, '[\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\"]', '[{\"item_id\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000012020\",\"name\":\"Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"}]', '2020-12-04', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 09:19:33', '1', '1', '2020-12-29 09:19:36', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(18, 'LKJAFD94R', 'student', '1', '[\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"SZM14dtqcccfn5cBl0ARgPCj287hym36\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000032020\",\"name\":\"Emmanuella Darko Sarfowaa\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000042020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"}]', '2020-12-08', 'uIkajsw123456789064hxk1fc3efmnva', '2020-12-29 09:29:20', '1', '1', '2020-12-29 09:29:23', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(19, 'LKJAFD94R', 'student', '1', '[\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"SZMsssqcccfn5cBl0aaaPCj287hym36\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"absent\"},{\"item_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000032020\",\"name\":\"Emmanuella Darko Sarfowaa\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000042020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2021-01-02', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '2021-01-02 08:13:50', '1', '1', '2021-01-02 08:14:57', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(20, 'LKJAFD94R', 'student', '2', '[\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\"]', '[{\"item_id\":\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"unique_id\":\"AGL000022020\",\"name\":\"Grace Obeng-Yeboah\",\"email\":\"graciellaob@gmail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\",\"unique_id\":\"AGL000042020\",\"name\":\"Frank Amponsah Amoah\",\"email\":\"frankamoah@gmail.com\",\"phone_number\":null,\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Maureen Anim\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"}]', '2021-01-02', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '2021-01-02 08:14:48', '1', '1', '2021-01-02 08:14:51', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(21, 'LKJAFD94R', 'student', '2', '[\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\",\"SZMsssqcccfn5cBl0ARgPCj287hym36\"]', '[{\"item_id\":\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"unique_id\":\"AGL000022020\",\"name\":\"Grace Obeng-Yeboah\",\"email\":\"graciellaob@gmail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\",\"unique_id\":\"AGL000042020\",\"name\":\"Frank Amponsah Amoah\",\"email\":\"frankamoah@gmail.com\",\"phone_number\":null,\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Maureen Anim\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2021-01-01', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '2021-01-02 08:15:11', '1', '1', '2021-01-02 08:15:15', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(22, 'LKJAFD94R', 'student', '1', '[\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"SZMsssqcccfn5cBl0aaaPCj287hym36\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000032020\",\"name\":\"Emmanuella Darko Sarfowaa\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000042020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2021-01-01', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '2021-01-02 08:15:24', '1', '1', '2021-01-02 08:15:28', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(23, 'LKJAFD94R', 'teacher', NULL, '[\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\"]', '[{\"item_id\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000012020\",\"name\":\"Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"}]', '2021-01-02', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '2021-01-02 08:15:44', '1', '1', '2021-01-02 08:16:08', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(24, 'LKJAFD94R', 'teacher', NULL, '[\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\"]', '[{\"item_id\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000012020\",\"name\":\"Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"}]', '2021-01-01', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-02 08:17:44', '1', '1', '2021-01-02 08:18:05', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(25, 'LKJAFD94R', 'admin', NULL, '[\"uIkajsw123456789064hxk1fc3efmnva\"]', '[{\"item_id\":\"uIkajsw123456789064hxk1fc3efmnva\",\"unique_id\":\"kajflkdkfafd\",\"name\":\"Admin Account\",\"email\":\"test_admin@gmail.com\",\"phone_number\":\"+233240889023\",\"state\":\"present\"}]', '2021-01-02', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '2021-01-02 08:20:33', '1', '1', '2021-01-02 08:21:49', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(26, 'LKJAFD94R', 'admin', NULL, '[\"uIkajsw123456789064hxk1fc3efmnva\"]', '[{\"item_id\":\"uIkajsw123456789064hxk1fc3efmnva\",\"unique_id\":\"kajflkdkfafd\",\"name\":\"Admin Account\",\"email\":\"test_admin@gmail.com\",\"phone_number\":\"+233240889023\",\"state\":\"present\"}]', '2021-01-01', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '2021-01-02 08:22:07', '1', '1', '2021-01-02 08:22:11', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(27, 'LKJAFD94R', 'student', '1', '[\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"SZMsssqcccfn5cBl0aaaPCj287hym36\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000032020\",\"name\":\"Emmanuella Darko Sarfowaa\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000072020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2021-01-07', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-07 12:50:32', '1', '1', '2021-01-07 12:50:36', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(28, 'LKJAFD94R', 'student', '2', '[\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\"]', '[{\"item_id\":\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"unique_id\":\"AGL000022020\",\"name\":\"Grace Obeng-Yeboah\",\"email\":\"graciellaob@gmail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\",\"unique_id\":\"AGL000042020\",\"name\":\"Frank Amponsah Amoah\",\"email\":\"frankamoah@gmail.com\",\"phone_number\":null,\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000062020\",\"name\":\"Maureen Anim\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"}]', '2021-01-07', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-07 12:50:45', '1', '1', NULL, 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(29, 'LKJAFD94R', 'teacher', NULL, '[\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\"]', '[{\"item_id\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000012020\",\"name\":\"Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"}]', '2021-01-06', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-07 12:50:58', '1', '1', '2021-01-07 12:51:26', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(30, 'LKJAFD94R', 'student', '1', '[\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"SZMsssqcccfn5cBl0aaaPCj287hym36\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000032020\",\"name\":\"Emmanuella Darko Sarfowaa\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000072020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2021-01-06', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-07 12:51:12', '1', '1', '2021-01-07 12:51:15', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(31, 'LKJAFD94R', 'teacher', NULL, '[\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\"]', '[{\"item_id\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000012020\",\"name\":\"Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"}]', '2021-01-07', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-07 12:51:54', '1', '1', '2021-01-07 12:52:09', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(32, 'LKJAFD94R', 'student', '1', '[\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"SZMsssqcccfn5cBl0aaaPCj287hym36\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000072020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2021-01-21', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 14:54:35', '1', '1', '2021-01-21 15:04:35', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(33, 'LKJAFD94R', 'student', '1', '[\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"SZMsssqcccfn5cBl0aaaPCj287hym36\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"absent\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000072020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2021-01-20', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 15:04:57', '1', '1', '2021-01-21 15:05:00', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(34, 'LKJAFD94R', 'student', '1', '[\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000072020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"}]', '2021-01-19', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 15:05:11', '1', '1', '2021-01-21 15:05:14', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(35, 'LKJAFD94R', 'student', '1', '[\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"SZMsssqcccfn5cBl0aaaPCj287hym36\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000072020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2021-01-18', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 15:05:24', '1', '1', '2021-01-21 15:05:28', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(36, 'LKJAFD94R', 'teacher', '', '[\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe\"]', '[{\"item_id\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000012020\",\"name\":\"Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"},{\"item_id\":\"aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000022020\",\"name\":\"Second Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"}]', '2021-01-18', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 15:10:02', '1', '1', '2021-01-21 15:15:10', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(37, 'LKJAFD94R', 'teacher', NULL, '[\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe\"]', '[{\"item_id\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000012020\",\"name\":\"Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"},{\"item_id\":\"aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000022020\",\"name\":\"Second Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"}]', '2021-01-19', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 15:17:21', '1', '1', '2021-01-21 15:17:27', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(38, 'LKJAFD94R', 'teacher', NULL, '[\"aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe\"]', '[{\"item_id\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000012020\",\"name\":\"Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"absent\"},{\"item_id\":\"aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000022020\",\"name\":\"Second Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"}]', '2021-01-20', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 15:17:34', '1', '1', '2021-01-21 15:17:41', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(39, 'LKJAFD94R', 'teacher', NULL, '[\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe\"]', '[{\"item_id\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000012020\",\"name\":\"Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"},{\"item_id\":\"aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000022020\",\"name\":\"Second Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"}]', '2021-01-21', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 15:17:50', '1', '1', '2021-01-21 15:17:53', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(40, 'LKJAFD94R', 'student', '2', '[\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\",\"SZMsssqcccfn5cBl0ARgPCj287hym36\"]', '[{\"item_id\":\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"unique_id\":\"AGL000022020\",\"name\":\"Grace Obeng-Yeboah\",\"email\":\"graciellaob@gmail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\",\"unique_id\":\"AGL000042020\",\"name\":\"Frank Amponsah Amoah\",\"email\":\"frankamoah@gmail.com\",\"phone_number\":\"090399309\",\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000062020\",\"name\":\"Maureen Anim\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2021-01-21', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 15:18:06', '1', '1', '2021-01-21 15:18:14', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(41, 'LKJAFD94R', 'student', '2', '[\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\",\"SZMsssqcccfn5cBl0ARgPCj287hym36\"]', '[{\"item_id\":\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"unique_id\":\"AGL000022020\",\"name\":\"Grace Obeng-Yeboah\",\"email\":\"graciellaob@gmail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\",\"unique_id\":\"AGL000042020\",\"name\":\"Frank Amponsah Amoah\",\"email\":\"frankamoah@gmail.com\",\"phone_number\":\"090399309\",\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000062020\",\"name\":\"Maureen Anim\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2021-01-20', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 15:18:21', '1', '1', '2021-01-21 15:18:24', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(42, 'LKJAFD94R', 'student', '4', '[\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\"]', '[{\"item_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000032020\",\"name\":\"Emmanuella Darko Sarfowaa\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2021-01-20', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 15:18:29', '1', '1', '2021-01-21 15:18:34', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(43, 'LKJAFD94R', 'student', '4', '[\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\"]', '[{\"item_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000032020\",\"name\":\"Emmanuella Darko Sarfowaa\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2021-01-19', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 15:18:38', '1', '1', '2021-01-21 15:18:41', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(44, 'LKJAFD94R', 'admin', NULL, '[\"uIkajsw123456789064hxk1fc3efmnva\"]', '[{\"item_id\":\"uIkajsw123456789064hxk1fc3efmnva\",\"unique_id\":\"kajflkdkfafd\",\"name\":\"Admin Account\",\"email\":\"test_admin@gmail.com\",\"phone_number\":\"+233240889023\",\"state\":\"present\"}]', '2021-01-19', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 15:18:52', '1', '1', '2021-01-21 15:19:22', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(45, 'LKJAFD94R', 'admin', NULL, '[\"uIkajsw123456789064hxk1fc3efmnva\"]', '[{\"item_id\":\"uIkajsw123456789064hxk1fc3efmnva\",\"unique_id\":\"kajflkdkfafd\",\"name\":\"Admin Account\",\"email\":\"test_admin@gmail.com\",\"phone_number\":\"+233240889023\",\"state\":\"present\"}]', '2021-01-21', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 15:19:02', '1', '1', '2021-01-21 15:19:06', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(46, 'LKJAFD94R', 'admin', NULL, '[\"uIkajsw123456789064hxk1fc3efmnva\"]', '[{\"item_id\":\"uIkajsw123456789064hxk1fc3efmnva\",\"unique_id\":\"kajflkdkfafd\",\"name\":\"Admin Account\",\"email\":\"test_admin@gmail.com\",\"phone_number\":\"+233240889023\",\"state\":\"present\"}]', '2021-01-20', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 15:19:13', '1', '1', '2021-01-21 15:19:16', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(47, 'LKJAFD94R', 'admin', NULL, '[\"uIkajsw123456789064hxk1fc3efmnva\"]', '[{\"item_id\":\"uIkajsw123456789064hxk1fc3efmnva\",\"unique_id\":\"kajflkdkfafd\",\"name\":\"Admin Account\",\"email\":\"test_admin@gmail.com\",\"phone_number\":\"+233240889023\",\"state\":\"present\"}]', '2021-01-18', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 15:19:28', '1', '1', '2021-01-21 15:19:31', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(48, 'LKJAFD94R', 'student', '4', '[\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\"]', '[{\"item_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000032020\",\"name\":\"Emmanuella Darko Sarfowaa\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2021-01-21', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 16:49:56', '1', '1', '2021-01-21 16:49:59', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(49, 'LKJAFD94R', 'student', '1', '[\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"SZMsssqcccfn5cBl0aaaPCj287hym36\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000072020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2021-01-11', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 16:50:32', '1', '1', '2021-01-21 16:50:35', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(50, 'LKJAFD94R', 'student', '1', '[\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"SZMsssqcccfn5cBl0aaaPCj287hym36\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000072020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2021-01-12', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 16:50:45', '1', '1', '2021-01-21 16:50:47', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(51, 'LKJAFD94R', 'student', '1', '[\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"SZMsssqcccfn5cBl0aaaPCj287hym36\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000072020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2021-01-13', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 16:50:55', '1', '1', '2021-01-21 16:50:58', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(52, 'LKJAFD94R', 'student', '1', '[\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"SZMsssqcccfn5cBl0aaaPCj287hym36\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000072020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2021-01-14', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 16:51:05', '1', '1', '2021-01-21 16:51:09', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(53, 'LKJAFD94R', 'student', '1', '[\"SZM14dtqcccfn5cBl0ARgPCj287hym36\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"absent\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000072020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"}]', '2021-01-15', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 16:51:17', '1', '1', '2021-01-21 16:51:20', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(54, 'LKJAFD94R', 'student', '7', '[\"kXTrgfmRsnUY13VD0LAB9ZiGuKzP46EQ\"]', '[{\"item_id\":\"kXTrgfmRsnUY13VD0LAB9ZiGuKzP46EQ\",\"unique_id\":\"AGL000082021\",\"name\":\"test student name\",\"email\":\"teststudent@mail.com\",\"phone_number\":\"09930039930\",\"state\":\"present\"}]', '2021-01-21', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 16:57:00', '1', '1', '2021-01-21 16:57:02', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(55, 'LKJAFD94R', 'student', '7', '[\"kXTrgfmRsnUY13VD0LAB9ZiGuKzP46EQ\"]', '[{\"item_id\":\"kXTrgfmRsnUY13VD0LAB9ZiGuKzP46EQ\",\"unique_id\":\"AGL000082021\",\"name\":\"test student name\",\"email\":\"teststudent@mail.com\",\"phone_number\":\"09930039930\",\"state\":\"present\"}]', '2021-01-20', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 16:57:10', '1', '1', '2021-01-21 16:57:12', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(56, 'LKJAFD94R', 'student', '7', '[]', '[{\"item_id\":\"kXTrgfmRsnUY13VD0LAB9ZiGuKzP46EQ\",\"unique_id\":\"AGL000082021\",\"name\":\"test student name\",\"email\":\"teststudent@mail.com\",\"phone_number\":\"09930039930\",\"state\":\"absent\"}]', '2021-01-19', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 16:57:17', '1', '1', '2021-01-21 16:57:20', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(57, 'LKJAFD94R', 'student', '7', '[]', '[{\"item_id\":\"kXTrgfmRsnUY13VD0LAB9ZiGuKzP46EQ\",\"unique_id\":\"AGL000082021\",\"name\":\"test student name\",\"email\":\"teststudent@mail.com\",\"phone_number\":\"09930039930\",\"state\":\"absent\"}]', '2021-01-18', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 16:57:25', '1', '1', '2021-01-21 16:57:28', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(58, 'LKJAFD94R', 'student', '2', '[\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"SZMsssqcccfn5cBl0ARgPCj287hym36\"]', '[{\"item_id\":\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"unique_id\":\"AGL000022020\",\"name\":\"Grace Obeng-Yeboah\",\"email\":\"graciellaob@gmail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\",\"unique_id\":\"AGL000042020\",\"name\":\"Frank Amponsah Amoah\",\"email\":\"frankamoah@gmail.com\",\"phone_number\":\"090399309\",\"state\":\"absent\"},{\"item_id\":\"SZMsssqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000062020\",\"name\":\"Maureen Anim\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2021-01-18', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 17:02:19', '1', '1', '2021-01-21 17:02:24', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(59, 'LKJAFD94R', 'teacher', NULL, '[\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe\"]', '[{\"item_id\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000012020\",\"name\":\"Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"},{\"item_id\":\"aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000022020\",\"name\":\"Second Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"}]', '2021-01-04', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 18:30:00', '1', '1', '2021-01-21 18:30:03', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(60, 'LKJAFD94R', 'teacher', NULL, '[\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe\"]', '[{\"item_id\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000012020\",\"name\":\"Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"},{\"item_id\":\"aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000022020\",\"name\":\"Second Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"}]', '2021-01-08', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 18:30:29', '1', '1', '2021-01-21 18:30:32', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(61, 'LKJAFD94R', 'teacher', NULL, '[]', '[{\"item_id\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000012020\",\"name\":\"Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"absent\"},{\"item_id\":\"aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000022020\",\"name\":\"Second Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"absent\"}]', '2021-01-05', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 18:30:39', '1', '1', '2021-01-21 18:30:42', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(62, 'LKJAFD94R', 'student', '1', '[\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"SZMsssqcccfn5cBl0aaaPCj287hym36\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000072020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2021-01-05', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 18:31:07', '1', '1', '2021-01-21 18:31:10', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(63, 'LKJAFD94R', 'student', '2', '[\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"SZMsssqcccfn5cBl0ARgPCj287hym36\"]', '[{\"item_id\":\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"unique_id\":\"AGL000022020\",\"name\":\"Grace Obeng-Yeboah\",\"email\":\"graciellaob@gmail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\",\"unique_id\":\"AGL000042020\",\"name\":\"Frank Amponsah Amoah\",\"email\":\"frankamoah@gmail.com\",\"phone_number\":\"090399309\",\"state\":\"absent\"},{\"item_id\":\"SZMsssqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000062020\",\"name\":\"Maureen Anim\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2021-01-05', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 18:31:21', '1', '1', '2021-01-21 18:31:23', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(64, 'LKJAFD94R', 'student', '2', '[\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\",\"SZMsssqcccfn5cBl0ARgPCj287hym36\"]', '[{\"item_id\":\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"unique_id\":\"AGL000022020\",\"name\":\"Grace Obeng-Yeboah\",\"email\":\"graciellaob@gmail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\",\"unique_id\":\"AGL000042020\",\"name\":\"Frank Amponsah Amoah\",\"email\":\"frankamoah@gmail.com\",\"phone_number\":\"090399309\",\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000062020\",\"name\":\"Maureen Anim\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2021-01-06', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 18:31:33', '1', '1', '2021-01-21 18:31:36', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(65, 'LKJAFD94R', 'student', '4', '[]', '[{\"item_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000032020\",\"name\":\"Emmanuella Darko Sarfowaa\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"}]', '2021-01-06', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 18:31:42', '1', '1', '2021-01-21 18:31:44', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(66, 'LKJAFD94R', 'student', '4', '[]', '[{\"item_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000032020\",\"name\":\"Emmanuella Darko Sarfowaa\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"}]', '2021-01-05', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 18:31:51', '1', '1', '2021-01-21 18:31:53', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(67, 'LKJAFD94R', 'student', '4', '[\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\"]', '[{\"item_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000032020\",\"name\":\"Emmanuella Darko Sarfowaa\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2021-01-11', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 18:32:01', '1', '1', '2021-01-21 18:32:04', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(68, 'LKJAFD94R', 'student', '4', '[\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\"]', '[{\"item_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000032020\",\"name\":\"Emmanuella Darko Sarfowaa\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2021-01-12', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 18:32:10', '1', '1', '2021-01-21 18:32:13', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(69, 'LKJAFD94R', 'student', '4', '[\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\"]', '[{\"item_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000032020\",\"name\":\"Emmanuella Darko Sarfowaa\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2021-01-01', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 18:32:36', '1', '1', '2021-01-21 18:32:39', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st');
INSERT INTO `users_attendance_log` (`id`, `client_id`, `user_type`, `class_id`, `users_list`, `users_data`, `log_date`, `created_by`, `date_created`, `status`, `finalize`, `date_finalized`, `finalized_by`, `academic_year`, `academic_term`) VALUES
(70, 'LKJAFD94R', 'teacher', NULL, '[\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe\"]', '[{\"item_id\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000012020\",\"name\":\"Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"},{\"item_id\":\"aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000022020\",\"name\":\"Second Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"}]', '2021-01-11', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 18:46:03', '1', '1', '2021-01-21 18:46:06', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(71, 'LKJAFD94R', 'admin', NULL, '[\"uIkajsw123456789064hxk1fc3efmnva\"]', '[{\"item_id\":\"uIkajsw123456789064hxk1fc3efmnva\",\"unique_id\":\"kajflkdkfafd\",\"name\":\"Admin Account\",\"email\":\"test_admin@gmail.com\",\"phone_number\":\"+233240889023\",\"state\":\"present\"}]', '2021-01-11', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 18:46:18', '1', '1', '2021-01-21 18:47:52', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(72, 'LKJAFD94R', 'admin', NULL, '[\"uIkajsw123456789064hxk1fc3efmnva\"]', '[{\"item_id\":\"uIkajsw123456789064hxk1fc3efmnva\",\"unique_id\":\"kajflkdkfafd\",\"name\":\"Admin Account\",\"email\":\"test_admin@gmail.com\",\"phone_number\":\"+233240889023\",\"state\":\"present\"}]', '2021-01-12', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 18:46:24', '1', '1', '2021-01-21 18:47:57', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(73, 'LKJAFD94R', 'admin', NULL, '[\"uIkajsw123456789064hxk1fc3efmnva\"]', '[{\"item_id\":\"uIkajsw123456789064hxk1fc3efmnva\",\"unique_id\":\"kajflkdkfafd\",\"name\":\"Admin Account\",\"email\":\"test_admin@gmail.com\",\"phone_number\":\"+233240889023\",\"state\":\"present\"}]', '2021-01-13', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 18:46:29', '1', '1', '2021-01-21 18:48:02', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(74, 'LKJAFD94R', 'admin', NULL, '[\"uIkajsw123456789064hxk1fc3efmnva\"]', '[{\"item_id\":\"uIkajsw123456789064hxk1fc3efmnva\",\"unique_id\":\"kajflkdkfafd\",\"name\":\"Admin Account\",\"email\":\"test_admin@gmail.com\",\"phone_number\":\"+233240889023\",\"state\":\"present\"}]', '2021-01-14', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 18:46:35', '1', '1', '2021-01-21 18:48:07', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(75, 'LKJAFD94R', 'admin', NULL, '[\"uIkajsw123456789064hxk1fc3efmnva\"]', '[{\"item_id\":\"uIkajsw123456789064hxk1fc3efmnva\",\"unique_id\":\"kajflkdkfafd\",\"name\":\"Admin Account\",\"email\":\"test_admin@gmail.com\",\"phone_number\":\"+233240889023\",\"state\":\"present\"}]', '2021-01-15', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 18:46:42', '1', '1', '2021-01-21 18:48:18', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(76, 'LKJAFD94R', 'teacher', NULL, '[\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\"]', '[{\"item_id\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000012020\",\"name\":\"Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"},{\"item_id\":\"aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000022020\",\"name\":\"Second Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"absent\"}]', '2021-01-15', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 18:46:48', '1', '1', '2021-01-21 18:46:54', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(77, 'LKJAFD94R', 'teacher', NULL, '[\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe\"]', '[{\"item_id\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000012020\",\"name\":\"Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"},{\"item_id\":\"aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000022020\",\"name\":\"Second Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"}]', '2021-01-12', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 18:47:04', '1', '1', '2021-01-21 18:47:07', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(78, 'LKJAFD94R', 'teacher', NULL, '[]', '[{\"item_id\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000012020\",\"name\":\"Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"absent\"},{\"item_id\":\"aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000022020\",\"name\":\"Second Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"absent\"}]', '2021-01-13', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 18:47:15', '1', '1', '2021-01-21 18:47:18', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(79, 'LKJAFD94R', 'teacher', NULL, '[]', '[{\"item_id\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000012020\",\"name\":\"Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"absent\"},{\"item_id\":\"aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000022020\",\"name\":\"Second Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"absent\"}]', '2021-01-14', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-21 18:47:25', '1', '1', '2021-01-21 18:47:29', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(80, 'LKJAFD94R', 'student', '1', '[]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"absent\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000072020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"}]', '2021-01-22', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-22 00:17:12', '1', '1', '2021-01-22 00:17:22', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(81, 'LKJAFD94R', 'student', '2', '[\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\"]', '[{\"item_id\":\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"unique_id\":\"AGL000022020\",\"name\":\"Grace Obeng-Yeboah\",\"email\":\"graciellaob@gmail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\",\"unique_id\":\"AGL000042020\",\"name\":\"Frank Amponsah Amoah\",\"email\":\"frankamoah@gmail.com\",\"phone_number\":\"090399309\",\"state\":\"absent\"},{\"item_id\":\"SZMsssqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000062020\",\"name\":\"Maureen Anim\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"}]', '2021-01-22', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-22 00:18:00', '1', '1', '2021-01-22 00:18:04', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(82, 'LKJAFD94R', 'student', '1', '[\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"SZMsssqcccfn5cBl0aaaPCj287hym36\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000072020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2021-01-25', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-26 22:13:45', '1', '1', '2021-01-26 22:13:49', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(83, 'LKJAFD94R', 'student', '2', '[\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\",\"SZMsssqcccfn5cBl0ARgPCj287hym36\"]', '[{\"item_id\":\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"unique_id\":\"AGL000022020\",\"name\":\"Grace Obeng-Yeboah\",\"email\":\"graciellaob@gmail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\",\"unique_id\":\"AGL000042020\",\"name\":\"Frank Amponsah Amoah\",\"email\":\"frankamoah@gmail.com\",\"phone_number\":\"090399309\",\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000062020\",\"name\":\"Maureen Anim\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2021-01-26', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-26 22:18:22', '1', '1', '2021-01-26 22:18:25', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(84, 'LKJAFD94R', 'student', '2', '[\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\"]', '[{\"item_id\":\"xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo\",\"unique_id\":\"AGL000022020\",\"name\":\"Grace Obeng-Yeboah\",\"email\":\"graciellaob@gmail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\",\"unique_id\":\"AGL000042020\",\"name\":\"Frank Amponsah Amoah\",\"email\":\"frankamoah@gmail.com\",\"phone_number\":\"090399309\",\"state\":\"absent\"},{\"item_id\":\"SZMsssqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000062020\",\"name\":\"Maureen Anim\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"}]', '2021-01-25', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-26 22:20:13', '1', '1', '2021-01-26 22:20:17', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(85, 'LKJAFD94R', 'student', '1', '[\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"SZMsssqcccfn5cBl0aaaPCj287hym36\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000072020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2021-01-26', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-26 22:20:33', '1', '1', '2021-01-26 22:20:38', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(86, 'LKJAFD94R', 'student', '4', '[\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\"]', '[{\"item_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000032020\",\"name\":\"Emmanuella Darko Sarfowaa\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"}]', '2021-01-22', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-26 22:20:52', '1', '0', NULL, NULL, '2019/2020', '1st'),
(87, 'LKJAFD94R', 'student', '4', '[]', '[{\"item_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000032020\",\"name\":\"Emmanuella Darko Sarfowaa\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"}]', '2021-01-25', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-26 22:20:58', '1', '1', '2021-01-26 22:21:17', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(88, 'LKJAFD94R', 'student', '4', '[]', '[{\"item_id\":\"SZM14dtqDkbfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000032020\",\"name\":\"Emmanuella Darko Sarfowaa\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"}]', '2021-01-26', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-26 22:21:07', '1', '1', '2021-01-26 22:21:10', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(89, 'LKJAFD94R', 'student', '1', '[\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"SZM14dtqcccfn5cBl0ARgPCj287hym36\"]', '[{\"item_id\":\"Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn\",\"unique_id\":\"AGL000012020\",\"name\":\"Solomon Obeng Darko\",\"email\":\"themailhereisthere@mail.com\",\"phone_number\":\"00930993093\",\"state\":\"present\"},{\"item_id\":\"SZM14dtqcccfn5cBl0ARgPCj287hym36\",\"unique_id\":\"AGL000052020\",\"name\":\"Cecilia Boateng\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"present\"},{\"item_id\":\"SZMsssqcccfn5cBl0aaaPCj287hym36\",\"unique_id\":\"AGL000072020\",\"name\":\"Felicia Amponsah\",\"email\":\"jauntygirl@gmail.com\",\"phone_number\":\"0247685521\",\"state\":\"absent\"}]', '2021-01-28', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '2021-01-28 19:10:38', '1', '1', '2021-01-28 20:05:30', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st'),
(90, 'LKJAFD94R', 'teacher', NULL, '[\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"9898989GstOi8vMW0zQ2A57nqLJZNkYe\"]', '[{\"item_id\":\"a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000012020\",\"name\":\"Test Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"},{\"item_id\":\"aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000022020\",\"name\":\"Second Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"},{\"item_id\":\"9898989GstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000032020\",\"name\":\"Third Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"present\"},{\"item_id\":\"0011989GstOi8vMW0zQ2A57nqLJZNkYe\",\"unique_id\":\"FTA000042020\",\"name\":\"Forth Teacher Account\",\"email\":\"emmallob14@gmail.com\",\"phone_number\":\"0550107770\",\"state\":\"absent\"}]', '2021-01-26', 'uIkajsw123456789064hxk1fc3efmnva', '2021-01-28 20:13:09', '1', '1', '2021-01-28 20:13:14', 'uIkajsw123456789064hxk1fc3efmnva', '2019/2020', '1st');

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
  `seen_date` datetime DEFAULT current_timestamp(),
  `sender_deleted` enum('0','1') NOT NULL DEFAULT '0',
  `receiver_deleted` enum('0','1') NOT NULL DEFAULT '0',
  `notice_type` varchar(12) NOT NULL DEFAULT '5',
  `user_agent` varchar(500) DEFAULT NULL,
  `user_signature` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users_complaints`
--

DROP TABLE IF EXISTS `users_complaints`;
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
(12, 'QeDbfIu4pjRr1UWGKT7cq06Oiv9JPdMB', 'books_request', 'jJnVM2SimOsZDFla4UAkfHg0cyRwuQ9x', 'comment', 'uIkajsw123456789064hxk1fc3efmnva', 'admin', NULL, '&lt;div&gt;&lt;!--block--&gt;Reduce the length of homepage sliders to that of the banner images on the other pages. Reduce the length of homepage sliders to that of the banner images on the other pages. Reduce the length of homepage sliders to that of the banner images on the other pages.&nbsp;&lt;/div&gt;', NULL, '2021-01-07 16:03:56', '0', '0', 'Windows 10 | Chrome | ::1', '0'),
(13, '3YJD9dtyNuzfEgIAMl4rkchsoCGVmpTn', 'events', 'z2JfSOdGTI4Pbxh8yV3UKY1LcMN7rgl6', 'comment', 'uIkajsw123456789064hxk1fc3efmnva', 'admin', NULL, '&lt;div&gt;&lt;!--block--&gt;This is a test&lt;/div&gt;', NULL, '2021-01-24 13:21:18', '0', '0', 'Windows 10 | Chrome | ::1', '0');

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
(1, 'LKJAFD94R', 'test_student', '2021-01-26 09:28:28', '::1', 'Chrome|Windows 10', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.14'),
(2, 'LKJAFD94R', 'test_teacher', '2021-01-26 09:28:49', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.14'),
(3, 'LKJAFD94R', 'test_student', '2021-01-26 10:57:46', '::1', 'Chrome|Windows 10', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.14'),
(4, 'LKJAFD94R', 'test_student', '2021-01-26 11:21:13', '::1', 'Chrome|Windows 10', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.14'),
(5, 'LKJAFD94R', 'test_student', '2021-01-26 14:21:26', '::1', 'Chrome|Windows 10', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.14'),
(6, 'LKJAFD94R', 'test_teacher', '2021-01-26 14:21:41', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.14'),
(7, 'LKJAFD94R', 'test_teacher', '2021-01-26 17:01:28', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.14'),
(8, 'LKJAFD94R', 'test_student', '2021-01-26 17:01:34', '::1', 'Chrome|Windows 10', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.14'),
(9, 'LKJAFD94R', 'test_parent', '2021-01-26 17:30:08', '::1', 'Chrome|Windows 10', '5Jz7Lv8aZ1Gi43MxDXBlfqRn9uYQjIEc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.14'),
(10, 'LKJAFD94R', 'test_student', '2021-01-26 17:30:30', '::1', 'Chrome|Windows 10', 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.14'),
(11, 'LKJAFD94R', 'test_parent', '2021-01-26 17:46:37', '::1', 'Chrome|Windows 10', '5Jz7Lv8aZ1Gi43MxDXBlfqRn9uYQjIEc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.14'),
(12, 'LKJAFD94R', 'test_admin', '2021-01-26 17:51:20', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.14'),
(13, 'LKJAFD94R', 'test_admin', '2021-01-27 09:50:48', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.14'),
(14, 'LKJAFD94R', 'test_admin', '2021-01-27 11:32:05', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.14'),
(15, 'LKJAFD94R', 'test_admin', '2021-01-28 09:01:20', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.14'),
(16, 'LKJAFD94R', 'test_admin', '2021-01-28 15:01:09', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.14'),
(17, 'LKJAFD94R', 'test_admin', '2021-01-28 16:31:22', '::1', 'Chrome|Windows 10', 'uIkajsw123456789064hxk1fc3efmnva', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.14'),
(18, 'LKJAFD94R', 'test_teacher', '2021-01-28 16:35:38', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.14'),
(19, 'LKJAFD94R', 'test_teacher', '2021-01-28 18:59:47', '::1', 'Chrome|Windows 10', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.14');

-- --------------------------------------------------------

--
-- Table structure for table `users_messaging_list`
--

DROP TABLE IF EXISTS `users_messaging_list`;
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
(2, 'dct2TfPXHnoy8IspF7iCxjGJEmDzu0Ka', 'account-verify', 'ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v', '{\"recipients_list\":[{\"fullname\":\"Frank Amponsah\",\"email\":\"frankamoah@gmail.com\",\"customer_id\":\"ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v\"}]}', '2020-12-17 22:59:40', 'send_now', '2020-12-17 22:59:40', 'email', '0', '[MySchoolGH Management System] Account Verification', 'Hello Frank,<a class=\"alert alert-success\" href=\"http://localhost/myschool_gh/verify?account&token=ISif1mdadb3LEq7rxO04znYjHFLYXM1PbtKo9GGhzZOkWucgjUXs6weQaBm8P2TAcETvsFW\">Verify your account</a><br><br>If it does not work please copy this link and place it in your browser url.<br><br>http://localhost/myschool_gh/verify?account&token=ISif1mdadb3LEq7rxO04znYjHFLYXM1PbtKo9GGhzZOkWucgjUXs6weQaBm8P2TAcETvsFW', 'uIkajsw123456789064hxk1fc3efmnva', '0', NULL),
(3, 'tC4TvKlVsgnh1R8QfzwWm6k0IZGobPUE', 'account-verify', '4aOdhDg36xX5Hr9zKwRYkPQfCLNT2Zne', '{\"recipients_list\":[{\"fullname\":\"test guardian \",\"email\":\"testemailguardian@mail.com\",\"customer_id\":\"4aOdhDg36xX5Hr9zKwRYkPQfCLNT2Zne\"}]}', '2021-01-18 21:46:27', 'send_now', '2021-01-18 21:46:27', 'email', '0', '[MySchoolGH Management System] Account Verification', 'Hello test,<a class=\"alert alert-success\" href=\"http://localhost/myschool_gh/verify?account&token=9lOePw7bfgBhKrvXVzQG2DMuG8sMzOQV3oEtY0kmC8dJAIk5H61buy7FLNplsoKREiTjL\">Verify your account</a><br><br>If it does not work please copy this link and place it in your browser url.<br><br>http://localhost/myschool_gh/verify?account&token=9lOePw7bfgBhKrvXVzQG2DMuG8sMzOQV3oEtY0kmC8dJAIk5H61buy7FLNplsoKREiTjL', 'uIkajsw123456789064hxk1fc3efmnva', '0', NULL),
(4, 'dhGLfe4B7abuzY0gmFinqU68cSOjJZkD', 'account-verify', 'kXTrgfmRsnUY13VD0LAB9ZiGuKzP46EQ', '{\"recipients_list\":[{\"fullname\":\"test student name\",\"email\":\"teststudent@mail.com\",\"customer_id\":\"kXTrgfmRsnUY13VD0LAB9ZiGuKzP46EQ\"}]}', '2021-01-18 23:21:28', 'send_now', '2021-01-18 23:21:28', 'email', '0', '[MySchoolGH Management System] Account Verification', 'Hello test,<a class=\"alert alert-success\" href=\"http://localhost/myschool_gh/verify?account&token=h3fzsDixMFWtYHXC3e5Iq5v1j6TpgILEErdiQsSAZgcvl46dDVz8uN0UUnNOWb4ywH9\">Verify your account</a><br><br>If it does not work please copy this link and place it in your browser url.<br><br>http://localhost/myschool_gh/verify?account&token=h3fzsDixMFWtYHXC3e5Iq5v1j6TpgILEErdiQsSAZgcvl46dDVz8uN0UUnNOWb4ywH9', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', '0', NULL);

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
(34, 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe', 'LKJAFD94R', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"guardian\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"incident\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"department\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"section\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"class\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"course\":{\"view\":1,\"add\":1,\"update\":1,\"lesson\":1,\"delete\":1},\"library\":{\"view\":1,\"request\":1},\"permissions\":{\"view\":1,\"update\":1},\"assignments\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1,\"handin\":1,\"mark\":1}}}', '2020-11-27 00:54:00', NULL),
(35, 'Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn', 'LKJAFD94R', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"parent\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1}}}', '2020-11-27 01:04:33', NULL),
(36, 'xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo', 'LKJAFD94R', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"parent\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1}}}', '2020-11-27 01:06:20', NULL),
(37, 'uIkajsw123456789064hxk1fc3efmnva', 'LKJAFD94R', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"guardian\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"incident\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"department\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"section\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"events\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"class\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"attendance\":{\"log\":1,\"finalize\":1},\"library\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1,\"issue\":1,\"return\":1},\"fees\":{\"view\":1,\"update\":1,\"receive\":1,\"allocation\":1,\"view_allocation\":1},\"fees_category\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"course\":{\"view\":1,\"add\":1,\"update\":1,\"lesson\":1,\"delete\":1},\"permissions\":{\"view\":1,\"update\":1},\"assignments\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1,\"handin\":1,\"mark\":1},\"timetable\":{\"manage\":1,\"allocate\":1},\"settings\":{\"filters\":1,\"manage\":1,\"activities\":1,\"login_history\":1},\"payslip\":{\"view\":1,\"modify_payroll\":1,\"generate\":1,\"validate\":1,\"manage_expense\":1}}}', '2020-06-10 12:08:20', NULL),
(38, 'ljg52NfPEsRhvXV3y8aGqUJxtTCn9DwM', 'LKJAFD94R', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"parent\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1}}}', '2020-12-10 17:01:29', NULL),
(41, 'vtIzqjrxDAf5uyegcQ8M7w2dk43XoLpZ', 'LKJAFD94R', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"parent\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1}}}', '2020-12-17 22:47:52', NULL),
(42, 'SZM14dtqDkbfn5cBl0ARgPCj287hym36', 'LKJAFD94R', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"parent\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1}}}', '2020-12-17 22:48:33', NULL),
(43, 'ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v', 'LKJAFD94R', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"parent\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1}}}', '2020-12-17 22:59:40', NULL),
(44, '4aOdhDg36xX5Hr9zKwRYkPQfCLNT2Zne', 'LKJAFD94R', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"parent\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1}}}', '2021-01-18 21:46:27', NULL),
(47, 'kXTrgfmRsnUY13VD0LAB9ZiGuKzP46EQ', 'LKJAFD94R', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"parent\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1}}}', '2021-01-18 23:21:28', NULL),
(48, 'aaaaaRhGstOi8vMW0zQ2A57nqLJZNkYe', 'LKJAFD94R', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"parent\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1}}}', '2020-12-17 22:59:40', NULL),
(49, '5Jz7Lv8aZ1Gi43MxDXBlfqRn9uYQjIEc', 'LKJAFD94R', '{\"permissions\":{\"student\":{\"view\":1},\"teacher\":{\"view\":1},\"parent\":{\"view\":1},\"employee\":{\"view\":1},\"library\":{\"request\":1}}}', '2021-01-18 21:46:27', NULL);

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
(1, 'STUDENT', 'student', '{\"permissions\":{\"student\":{\"view\":1},\"teacher\":{\"view\":1},\"parent\":{\"view\":1},\"employee\":{\"view\":1}}}'),
(2, 'TEACHER', 'teacher', '{\"permissions\":{\"student\":{\"view\":1},\"teacher\":{\"view\":1},\"parent\":{\"view\":1},\"employee\":{\"view\":1},\"attendance\":{\"log\":1},\"library\":{\"request\":1},\"course\":{\"update\":1,\"lesson\":1}}}'),
(3, 'PARENT', 'parent', '{\"permissions\":{\"student\":{\"view\":1},\"teacher\":{\"view\":1},\"parent\":{\"view\":1},\"employee\":{\"view\":1},\"library\":{\"request\":1},\"fees\":{\"view\":1,\"view_allocation\":1}}}'),
(4, 'EMPLOYEE', 'employee', '{\"permissions\":{\"student\":{\"view\":1},\"teacher\":{\"view\":1},\"parent\":{\"view\":1},\"employee\":{\"view\":1},\"library\":{\"request\":1}}}'),
(5, 'ADMIN', 'admin', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"guardian\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"incident\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"department\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"section\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"events\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"class\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"attendance\":{\"log\":1,\"finalize\":1},\"library\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1,\"issue\":1,\"return\":1},\"fees\":{\"view\":1,\"update\":1,\"receive\":1,\"allocation\":1,\"view_allocation\":1},\"fees_category\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"course\":{\"view\":1,\"add\":1,\"update\":1,\"lesson\":1,\"delete\":1},\"permissions\":{\"view\":1,\"update\":1},\"assignments\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1,\"handin\":1,\"mark\":1},\"timetable\":{\"manage\":1,\"allocate\":1},\"settings\":{\"filters\":1,\"manage\":1,\"activities\":1,\"login_history\":1},\"payslip\":{\"view\":1,\"modify_payroll\":1,\"generate\":1,\"validate\":1,\"manage_expense\":1}}}'),
(6, 'ACCOUNTANT', 'accountant', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"guardian\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"incident\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"department\":{\"view\":1},\"section\":{\"view\":1},\"events\":{\"view\":1},\"class\":{\"view\":1},\"attendance\":{\"log\":1},\"library\":{\"view\":1,\"return\":1},\"fees\":{\"view\":1,\"update\":1,\"receive\":1,\"allocation\":1,\"view_allocation\":1},\"fees_category\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"course\":{\"view\":1,\"lesson\":1},\"settings\":{\"filters\":1},\"payslip\":{\"view\":1,\"modify_payroll\":1,\"generate\":1,\"validate\":1,\"manage_expense\":1}}}');

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
-- AUTO_INCREMENT for table `academic_years`
--
ALTER TABLE `academic_years`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `assignments_submitted`
--
ALTER TABLE `assignments_submitted`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `books_borrowed_details`
--
ALTER TABLE `books_borrowed_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

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
-- AUTO_INCREMENT for table `currency`
--
ALTER TABLE `currency`
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
-- AUTO_INCREMENT for table `fees_allocations`
--
ALTER TABLE `fees_allocations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `fees_category`
--
ALTER TABLE `fees_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `fees_collection`
--
ALTER TABLE `fees_collection`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `fees_payments`
--
ALTER TABLE `fees_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `files_attachment`
--
ALTER TABLE `files_attachment`
  MODIFY `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
-- AUTO_INCREMENT for table `payslips`
--
ALTER TABLE `payslips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payslips_allowance_types`
--
ALTER TABLE `payslips_allowance_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payslips_details`
--
ALTER TABLE `payslips_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `payslips_employees_allowances`
--
ALTER TABLE `payslips_employees_allowances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `payslips_employees_payroll`
--
ALTER TABLE `payslips_employees_payroll`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `periods`
--
ALTER TABLE `periods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `timetables_slots_allocation`
--
ALTER TABLE `timetables_slots_allocation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=166;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `users_access_attempt`
--
ALTER TABLE `users_access_attempt`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `users_activity_logs`
--
ALTER TABLE `users_activity_logs`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `users_api_endpoints`
--
ALTER TABLE `users_api_endpoints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `users_chat`
--
ALTER TABLE `users_chat`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users_gender`
--
ALTER TABLE `users_gender`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users_login_history`
--
ALTER TABLE `users_login_history`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users_messaging_list`
--
ALTER TABLE `users_messaging_list`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users_notification`
--
ALTER TABLE `users_notification`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

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
