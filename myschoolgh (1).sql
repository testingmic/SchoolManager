-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 11, 2021 at 04:01 AM
-- Server version: 8.0.26-0ubuntu0.20.04.2
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
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
  `id` int NOT NULL,
  `client_id` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `academic_terms`
--

INSERT INTO `academic_terms` (`id`, `client_id`, `name`, `description`) VALUES
(1, 'MSGH000001', '1st', '1st Semester'),
(2, 'MSGH000001', '2nd', '2nd Semester'),
(3, 'MSGH000001', '3rd', '3rd Semester');

-- --------------------------------------------------------

--
-- Table structure for table `academic_years`
--

DROP TABLE IF EXISTS `academic_years`;
CREATE TABLE `academic_years` (
  `id` int NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `year_group` varchar(255) DEFAULT NULL,
  `academic_term` varchar(32) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

DROP TABLE IF EXISTS `accounts`;
CREATE TABLE `accounts` (
  `id` int UNSIGNED NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `currency` varchar(32) NOT NULL DEFAULT 'GHS',
  `account_name` varchar(255) DEFAULT NULL,
  `account_bank` varchar(255) DEFAULT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `opening_balance` varchar(14) DEFAULT '0',
  `total_credit` double(13,2) NOT NULL DEFAULT '0.00',
  `total_debit` double(13,2) NOT NULL DEFAULT '0.00',
  `balance` double(13,2) NOT NULL DEFAULT '0.00',
  `default_account` enum('0','1') DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` varchar(32) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `client_id`, `item_id`, `currency`, `account_name`, `account_bank`, `account_number`, `description`, `opening_balance`, `total_credit`, `total_debit`, `balance`, `default_account`, `date_created`, `created_by`, `status`) VALUES
(1, 'MSGH000001', 'nFCjT14PyzISdho', 'GHS', 'Morning Star International School', 'Standard Chartered Bank (Ghana) Limited', '00100102335100', 'This is the main account of the school.', '1500', 16100.00, 22620.00, -6520.00, '1', '2021-07-30 11:03:09', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1'),
(2, 'MSGH000001', 'nqe1tB4agLxm0NI', 'GHS', 'Morning Star International School', 'United Bank for Africa (Ghana) Limited', '002019909000090', 'This is another account for the payment of salary', '3000', 3000.00, 0.00, 3000.00, '0', '2021-07-30 11:04:02', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1');

-- --------------------------------------------------------

--
-- Table structure for table `accounts_transaction`
--

DROP TABLE IF EXISTS `accounts_transaction`;
CREATE TABLE `accounts_transaction` (
  `id` int UNSIGNED NOT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `account_id` varchar(32) DEFAULT NULL,
  `account_type` varchar(32) DEFAULT NULL,
  `item_type` enum('Deposit','Expense') DEFAULT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `amount` double(13,2) DEFAULT '0.00',
  `balance` double(13,2) DEFAULT '0.00',
  `record_date` date DEFAULT NULL,
  `payment_medium` varchar(32) DEFAULT NULL,
  `description` text,
  `academic_year` varchar(32) DEFAULT NULL,
  `academic_term` varchar(32) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` varchar(32) DEFAULT NULL,
  `state` enum('Pending','Approved') NOT NULL DEFAULT 'Pending',
  `validated_date` datetime DEFAULT NULL,
  `validated_by` varchar(32) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `accounts_transaction`
--

INSERT INTO `accounts_transaction` (`id`, `item_id`, `client_id`, `account_id`, `account_type`, `item_type`, `reference`, `amount`, `balance`, `record_date`, `payment_medium`, `description`, `academic_year`, `academic_term`, `date_created`, `created_by`, `state`, `validated_date`, `validated_by`, `status`) VALUES
(1, 'RL00001', 'MSGH000001', 'nFCjT14PyzISdho', 'fees', 'Deposit', NULL, 500.00, 2000.00, '2021-07-30', 'Cash', 'Fees Payment - for <strong>Sani Abdul Jabal</strong>', '2020/2021', '1st', '2021-07-30 11:09:53', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Approved', '2021-07-31 09:14:37', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1'),
(2, 'Nzg7Bo0h2CYm8rt', 'MSGH000001', 'nFCjT14PyzISdho', 'fees', 'Deposit', NULL, 500.00, 2500.00, '2021-07-30', 'Cash', 'Fees Payment - for <strong>Sani Abdul Jabal </strong>', '2020/2021', '1st', '2021-07-30 11:10:27', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Approved', '2021-07-31 09:14:45', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1'),
(3, 'Y3DMseEwgqaTnov', 'MSGH000001', 'nFCjT14PyzISdho', 'fees', 'Deposit', NULL, 750.00, 3250.00, '2021-07-30', 'Cash', 'Fees Payment - for <strong>Frederick Asamoah</strong>', '2020/2021', '1st', '2021-07-30 11:19:47', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Approved', '2021-07-31 09:14:33', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1'),
(4, 'Y3DMseEwgqaTnov', 'MSGH000001', 'nFCjT14PyzISdho', 'fees', 'Deposit', NULL, 100.00, 3350.00, '2021-07-30', 'Cash', 'Fees Payment - for <strong>Frederick Asamoah</strong>', '2020/2021', '1st', '2021-07-30 11:19:47', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Approved', '2021-07-31 09:14:33', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1'),
(5, 'Y3DMseEwgqaTnov', 'MSGH000001', 'nFCjT14PyzISdho', 'fees', 'Deposit', NULL, 150.00, 3500.00, '2021-07-30', 'Cash', 'Fees Payment - for <strong>Frederick Asamoah</strong>', '2020/2021', '1st', '2021-07-30 11:19:47', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Approved', '2021-07-31 09:14:33', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1'),
(6, 'f4hYVUz2jAJqK0C', 'MSGH000001', 'nFCjT14PyzISdho', 'fees', 'Deposit', NULL, 100.00, 3600.00, '2021-07-30', 'Cash', 'Fees Payment - for <strong>Frederick Asamoah</strong>', '2020/2021', '1st', '2021-07-30 11:37:09', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Approved', '2021-07-31 09:14:41', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1'),
(7, 'f4hYVUz2jAJqK0C', 'MSGH000001', 'nFCjT14PyzISdho', 'fees', 'Deposit', NULL, 200.00, 3800.00, '2021-07-30', 'Cash', 'Fees Payment - for <strong>Frederick Asamoah</strong>', '2020/2021', '1st', '2021-07-30 11:37:09', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Approved', '2021-07-31 09:14:41', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1'),
(8, 'f4hYVUz2jAJqK0C', 'MSGH000001', 'nFCjT14PyzISdho', 'fees', 'Deposit', NULL, 100.00, 3900.00, '2021-07-30', 'Cash', 'Fees Payment - for <strong>Frederick Asamoah</strong>', '2020/2021', '1st', '2021-07-30 11:37:09', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Approved', '2021-07-31 09:14:41', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1'),
(9, 'DM2FuzCt7g9jwTc', 'MSGH000001', 'nFCjT14PyzISdho', 'fees', 'Deposit', NULL, 100.00, 3800.00, '2021-07-30', 'Cash', 'Fees Payment - for <strong>Sani Abdul Jabal </strong>', '2020/2021', '1st', '2021-07-30 21:08:14', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Approved', '2021-07-30 21:08:14', NULL, '1'),
(10, 'DM2FuzCt7g9jwTc', 'MSGH000001', 'nFCjT14PyzISdho', 'fees', 'Deposit', NULL, 200.00, 3700.00, '2021-07-30', 'Cash', 'Fees Payment - for <strong>Sani Abdul Jabal </strong>', '2020/2021', '1st', '2021-07-30 21:08:14', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Approved', '2021-07-30 21:08:14', NULL, '1'),
(11, 'DM2FuzCt7g9jwTc', 'MSGH000001', 'nFCjT14PyzISdho', 'fees', 'Deposit', NULL, 100.00, 3800.00, '2021-07-30', 'Cash', 'Fees Payment - for <strong>Sani Abdul Jabal </strong>', '2020/2021', '1st', '2021-07-30 21:08:14', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Approved', '2021-07-30 21:08:14', NULL, '1'),
(12, 'wBX46syTzIv0CRVGOpa21refdgLt8huq', 'MSGH000001', 'nFCjT14PyzISdho', 'payroll', 'Expense', NULL, 1885.00, 2415.00, '2021-07-30', 'bank', 'Auto Generation of PaySlip - July 2021 for <strong>Emmanuel Obeng Hyde</strong>', '2020/2021', '1st', '2021-07-30 21:19:04', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Approved', '2021-07-30 21:19:10', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1'),
(13, 'tvX70QgkfziTreOhHBqdsWy8uIFSGUYV', 'MSGH000001', 'nFCjT14PyzISdho', 'payroll', 'Expense', NULL, 1885.00, 530.00, '2021-07-30', 'bank', 'Auto Generation of PaySlip - June 2021 for <strong>Emmanuel Obeng Hyde</strong>', '2020/2021', '1st', '2021-07-30 21:20:20', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Approved', '2021-07-30 21:20:51', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1'),
(14, 'qsTZe6kB2UbgNcv0pDwuVWhJj7OiMI9d', 'MSGH000001', 'nFCjT14PyzISdho', 'payroll', 'Expense', NULL, 1885.00, -1355.00, '2021-07-30', 'bank', 'Auto Generation of PaySlip - May 2021 for <strong>Emmanuel Obeng Hyde</strong>', '2020/2021', '1st', '2021-07-30 21:20:32', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Approved', '2021-07-30 21:20:49', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1'),
(15, 'WQByR0GwYIOZjNU3n4DreVmt6FSXqgkd', 'MSGH000001', 'nFCjT14PyzISdho', 'payroll', 'Expense', NULL, 1885.00, -3240.00, '2021-07-30', 'bank', 'Auto Generation of PaySlip - April 2021 for <strong>Emmanuel Obeng Hyde</strong>', '2020/2021', '1st', '2021-07-30 21:20:43', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Approved', '2021-07-30 21:20:47', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1'),
(16, 'fkcA4ghlJUQHv0YV7EsdoDnCzLBep19w', 'MSGH000001', 'nFCjT14PyzISdho', 'payroll', 'Expense', NULL, 1885.00, -5125.00, '2021-07-30', 'bank', 'Auto Generation of PaySlip - March 2021 for <strong>Emmanuel Obeng Hyde</strong>', '2020/2021', '1st', '2021-07-30 21:30:13', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Approved', '2021-07-30 21:30:16', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1'),
(17, 'jBmQV1zGN8A3e6M', 'MSGH000001', 'nFCjT14PyzISdho', 'O5dMNfr3u8Stzph', 'Deposit', NULL, 6500.00, 1375.00, '2021-06-30', 'cash', NULL, '2020/2021', '1st', '2021-07-31 09:30:13', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Pending', NULL, NULL, '1'),
(18, 'JbxwjX3B7ec5n89', 'MSGH000001', 'nFCjT14PyzISdho', 'cFE8LdrTgAxoZIS', 'Deposit', NULL, 5300.00, 6675.00, '2021-07-31', 'cash', NULL, '2020/2021', '1st', '2021-07-31 09:31:21', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Pending', NULL, NULL, '1'),
(19, '9S57ykNTZYcKMh2fbzqwRIgJFlEojtxP', 'MSGH000001', 'nFCjT14PyzISdho', 'payroll', 'Expense', NULL, 1885.00, 4790.00, '2021-07-31', 'bank', 'Auto Generation of PaySlip - January 2021 for <strong>Emmanuel Obeng Hyde</strong>', '2020/2021', '1st', '2021-07-31 15:43:50', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Pending', NULL, NULL, '1'),
(20, 'nRle3zcbyou6JHECYSMmZvXUiNqTOrfW', 'MSGH000001', 'nFCjT14PyzISdho', 'payroll', 'Expense', NULL, 1885.00, 2905.00, '2021-07-31', 'bank', 'Auto Generation of PaySlip - January 2021 for <strong>Emmanuel Obeng Hyde</strong>', '2020/2021', '1st', '2021-07-31 15:45:16', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Pending', NULL, NULL, '1'),
(21, 'UyfzjD4EbBR37g0Txq6m9HMenLka1dVN', 'MSGH000001', 'nFCjT14PyzISdho', 'payroll', 'Expense', NULL, 1885.00, 1020.00, '2021-07-31', 'bank', 'Auto Generation of PaySlip - January 2021 for <strong>Emmanuel Obeng Hyde</strong>', '2020/2021', '1st', '2021-07-31 15:46:27', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Pending', NULL, NULL, '1'),
(22, 'KloMVYkpOmfcvAd46Jrjq5Z0wDFgiPeQ', 'MSGH000001', 'nFCjT14PyzISdho', 'payroll', 'Expense', NULL, 1885.00, -865.00, '2021-07-31', 'bank', 'Auto Generation of PaySlip - January 2021 for <strong>Emmanuel Obeng Hyde</strong>', '2020/2021', '1st', '2021-07-31 15:52:14', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Pending', NULL, NULL, '1'),
(24, 'Nk1HRvQEy7faUI9tAOTBiSLnJrhuMYb8', 'MSGH000001', 'nFCjT14PyzISdho', 'payroll', 'Expense', NULL, 1885.00, -2750.00, '2021-07-31', 'bank', 'Auto Generation of PaySlip - January 2021 for <strong>Emmanuel Obeng Hyde</strong>', '2020/2021', '1st', '2021-07-31 15:55:32', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Approved', '2021-07-31 16:09:11', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1'),
(25, 'o0W58OxEnIVylw9qb1DdgeA4CN2sFPML', 'MSGH000001', 'nFCjT14PyzISdho', 'payroll', 'Expense', NULL, 1885.00, -4635.00, '2021-07-31', 'bank', 'Auto Generation of PaySlip - February 2021 for <strong>Emmanuel Obeng Hyde</strong>', '2020/2021', '1st', '2021-07-31 15:57:06', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Approved', '2021-07-31 16:09:05', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1'),
(26, 'hfPom0C5EkWX7vMa3w1dsZyVKJpQ9c6j', 'MSGH000001', 'nFCjT14PyzISdho', 'payroll', 'Expense', NULL, 1885.00, -6520.00, '2021-07-31', 'bank', 'Auto Generation of PaySlip - March 2021 for <strong>Emmanuel Obeng Hyde</strong>', '2020/2021', '1st', '2021-07-31 15:59:08', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Approved', '2021-07-31 16:09:08', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1');

-- --------------------------------------------------------

--
-- Table structure for table `accounts_type_head`
--

DROP TABLE IF EXISTS `accounts_type_head`;
CREATE TABLE `accounts_type_head` (
  `id` int NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `description` text,
  `academic_year` varchar(32) DEFAULT NULL,
  `academic_term` varchar(32) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` varchar(32) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `accounts_type_head`
--

INSERT INTO `accounts_type_head` (`id`, `client_id`, `item_id`, `name`, `type`, `description`, `academic_year`, `academic_term`, `date_created`, `created_by`, `status`) VALUES
(1, 'MSGH000001', 'cFE8LdrTgAxoZIS', 'Bus Fees / Transport', 'Income', NULL, '2020/2021', '1st', '2021-07-31 09:25:53', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1'),
(2, 'MSGH000001', 'O5dMNfr3u8Stzph', 'Feeding Fees', 'Income', NULL, '2020/2021', '1st', '2021-07-31 09:26:02', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
CREATE TABLE `announcements` (
  `id` int UNSIGNED NOT NULL,
  `item_id` varchar(32) NOT NULL DEFAULT 'NULL',
  `client_id` varchar(32) DEFAULT NULL,
  `user_id` varchar(32) DEFAULT NULL,
  `user_type` varchar(32) DEFAULT NULL,
  `recipient_group` varchar(1000) NOT NULL DEFAULT 'all',
  `persistent` enum('0','1') DEFAULT '0',
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `subject` varchar(255) DEFAULT NULL,
  `message` text,
  `content` text,
  `seen_by` text,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `replies_count` int UNSIGNED NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_updated_by` varchar(32) DEFAULT NULL,
  `last_updated_date` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

DROP TABLE IF EXISTS `assignments`;
CREATE TABLE `assignments` (
  `id` int NOT NULL,
  `client_id` varchar(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT '1',
  `assignment_group` enum('Test','Assignment','Quiz','Exam','Group Work') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'Assignment',
  `assignment_type` enum('file_attachment','multiple_choice') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'file_attachment',
  `assigned_to` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'all_students',
  `assigned_to_list` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `item_id` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `course_tutor` varchar(1000) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `department_id` varchar(14) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `course_id` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `class_id` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `grading` int DEFAULT '0',
  `assignment_title` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `assignment_description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `insertion_mode` enum('Auto','Manual') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Auto',
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `due_time` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` enum('Draft','Pending','Graded','Cancelled','Closed','Answered') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'Pending',
  `allowed_time` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '30',
  `date_closed` datetime DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  `date_published` datetime DEFAULT NULL,
  `status` enum('0','1') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '1',
  `deleted` enum('0','1') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '0',
  `academic_year` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '2019/2020',
  `academic_term` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '1st',
  `replies_count` varchar(14) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `comments_count` varchar(14) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`id`, `client_id`, `assignment_group`, `assignment_type`, `assigned_to`, `assigned_to_list`, `item_id`, `course_tutor`, `department_id`, `course_id`, `class_id`, `grading`, `assignment_title`, `assignment_description`, `insertion_mode`, `date_created`, `created_by`, `due_date`, `due_time`, `state`, `allowed_time`, `date_closed`, `date_updated`, `date_published`, `status`, `deleted`, `academic_year`, `academic_term`, `replies_count`, `comments_count`) VALUES
(1, 'MSGH000001', 'Test', 'file_attachment', 'selected_students', '[\"KDtbYhedUAgTC8sG1caxV6LfEklMjvFn\",\"aaabYhedUAgTC8sG1caxV6LfEklMjvFn\"]', 'kcnYl4hx3gzZU2o6rTbMGaORwVI0BEP8', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', NULL, 'JBKXbQYAU4hMimldGuEZNVpr6H3fL1y7', 'DKm7eAGouxcHIfiw52OljBX6zk3W19pT', 30, 'Test Assessment', NULL, 'Manual', '2021-07-24 17:56:35', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-07-24', '', 'Closed', '30', NULL, NULL, '2021-07-24 17:56:35', '1', '0', '2020/2021', '1st', '0', '0'),
(2, 'MSGH000001', 'Quiz', 'file_attachment', 'selected_students', '[\"KDtbYhedUAgTC8sG1caxV6LfEklMjvFn\",\"aaabYhedUAgTC8sG1caxV6LfEklMjvFn\"]', 'Rf3oQbCmVITqjXltHJ9niE0WZFvs8Pxk', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', NULL, 'FxJjgQ7bB14Khr869iGCpTSNEwMfynZu', 'DKm7eAGouxcHIfiw52OljBX6zk3W19pT', 25, 'Sample Test Information', NULL, 'Manual', '2021-07-26 07:26:39', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-07-26', '08:30', 'Closed', '30', NULL, NULL, '2021-07-26 07:26:39', '1', '0', '2020/2021', '1st', '0', '0'),
(3, 'MSGH000001', 'Group Work', 'file_attachment', 'selected_students', '[\"KDtbYhedUAgTC8sG1caxV6LfEklMjvFn\",\"aaabYhedUAgTC8sG1caxV6LfEklMjvFn\",\"1526BaoMyt8Inh3c0zugePkbKfsGHNmX\"]', 'uqPftbpeQUFRWm5NhCX9cHYA2a8gLKzn', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', NULL, 'jPNrKhLWRAp72Q1dsk06bfacXvOCF3UE', 'DKm7eAGouxcHIfiw52OljBX6zk3W19pT', 30, 'New Test Assessment with Log and Save Later', 'This assessment test was given as a group to the class. They were grouped into 5 members each and made to submit it in 3 days interval.', 'Manual', '2021-07-26 17:37:58', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-07-26', '', 'Closed', '30', '2021-07-26 17:41:32', NULL, '2021-07-26 17:37:58', '1', '0', '2020/2021', '1st', '0', '0'),
(4, 'MSGH000001', 'Assignment', 'file_attachment', 'selected_students', '[\"KDtbYhedUAgTC8sG1caxV6LfEklMjvFn\",\"aaabYhedUAgTC8sG1caxV6LfEklMjvFn\"]', 'UZjpe3Q2dXJTFKPbCWswml1fhDaVrnox', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', NULL, 'hdqeCQ73NDkcFvPBrJa8OX4TERpxiVuM', 'DKm7eAGouxcHIfiw52OljBX6zk3W19pT', 30, 'Another assignment', '', 'Manual', '2021-07-30 20:11:15', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-07-30', '', 'Closed', '30', NULL, NULL, '2021-07-30 20:11:15', '1', '0', '2020/2021', '1st', '0', '1'),
(5, 'MSGH000001', 'Quiz', 'file_attachment', 'selected_students', '[\"KDtbYhedUAgTC8sG1caxV6LfEklMjvFn\",\"aaabYhedUAgTC8sG1caxV6LfEklMjvFn\"]', '2aGb5wX4ZYU7BOmLioT6WeNAVujKh8n3', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', NULL, 'jPNrKhLWRAp72Q1dsk06bfacXvOCF3UE', 'DKm7eAGouxcHIfiw52OljBX6zk3W19pT', 25, 'Test Quiz', '', 'Manual', '2021-07-30 20:12:02', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-07-30', '', 'Closed', '30', NULL, NULL, '2021-07-30 20:12:02', '1', '0', '2020/2021', '1st', '0', '0');

-- --------------------------------------------------------

--
-- Table structure for table `assignments_answers`
--

DROP TABLE IF EXISTS `assignments_answers`;
CREATE TABLE `assignments_answers` (
  `id` int NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `assignment_id` varchar(32) DEFAULT NULL,
  `student_id` varchar(32) DEFAULT NULL,
  `answers` text,
  `scores` varchar(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assignments_questions`
--

DROP TABLE IF EXISTS `assignments_questions`;
CREATE TABLE `assignments_questions` (
  `id` int NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `assignment_id` varchar(32) DEFAULT NULL,
  `question` text,
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
  `correct_answer_description` text,
  `attempted_by` text,
  `current_state` enum('Published','Draft') NOT NULL DEFAULT 'Published',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted` enum('0','1') DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assignments_submitted`
--

DROP TABLE IF EXISTS `assignments_submitted`;
CREATE TABLE `assignments_submitted` (
  `id` int NOT NULL,
  `client_id` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `assignment_id` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_id` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `score` varchar(12) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `graded` enum('0','1') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `handed_in` enum('Pending','Submitted') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Pending',
  `date_submitted` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_graded` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `assignments_submitted`
--

INSERT INTO `assignments_submitted` (`id`, `client_id`, `assignment_id`, `student_id`, `score`, `graded`, `handed_in`, `date_submitted`, `date_graded`) VALUES
(1, 'MSGH000001', 'kcnYl4hx3gzZU2o6rTbMGaORwVI0BEP8', 'KDtbYhedUAgTC8sG1caxV6LfEklMjvFn', '25', '1', 'Submitted', '2021-07-24 17:56:36', '2021-07-24 17:56:36'),
(2, 'MSGH000001', 'kcnYl4hx3gzZU2o6rTbMGaORwVI0BEP8', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn', '28', '1', 'Submitted', '2021-07-24 17:56:36', '2021-07-24 17:56:36'),
(3, 'MSGH000001', 'Rf3oQbCmVITqjXltHJ9niE0WZFvs8Pxk', 'KDtbYhedUAgTC8sG1caxV6LfEklMjvFn', '23', '1', 'Submitted', '2021-07-26 07:26:39', '2021-07-26 07:26:39'),
(4, 'MSGH000001', 'Rf3oQbCmVITqjXltHJ9niE0WZFvs8Pxk', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn', '21', '1', 'Submitted', '2021-07-26 07:26:40', '2021-07-26 07:26:40'),
(5, 'MSGH000001', 'uqPftbpeQUFRWm5NhCX9cHYA2a8gLKzn', 'KDtbYhedUAgTC8sG1caxV6LfEklMjvFn', '26', '1', 'Submitted', '2021-07-26 17:37:58', '2021-07-26 17:41:26'),
(6, 'MSGH000001', 'uqPftbpeQUFRWm5NhCX9cHYA2a8gLKzn', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn', '28', '1', 'Submitted', '2021-07-26 17:37:58', '2021-07-26 17:41:26'),
(7, 'MSGH000001', 'UZjpe3Q2dXJTFKPbCWswml1fhDaVrnox', 'KDtbYhedUAgTC8sG1caxV6LfEklMjvFn', '24', '1', 'Submitted', '2021-07-30 20:11:15', '2021-07-30 20:11:15'),
(8, 'MSGH000001', 'UZjpe3Q2dXJTFKPbCWswml1fhDaVrnox', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn', '19', '1', 'Submitted', '2021-07-30 20:11:16', '2021-07-30 20:11:16'),
(9, 'MSGH000001', '2aGb5wX4ZYU7BOmLioT6WeNAVujKh8n3', 'KDtbYhedUAgTC8sG1caxV6LfEklMjvFn', '20', '1', 'Submitted', '2021-07-30 20:12:02', '2021-07-30 20:12:02'),
(10, 'MSGH000001', '2aGb5wX4ZYU7BOmLioT6WeNAVujKh8n3', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn', '22', '1', 'Submitted', '2021-07-30 20:12:02', '2021-07-30 20:12:02');

-- --------------------------------------------------------

--
-- Table structure for table `banks_list`
--

DROP TABLE IF EXISTS `banks_list`;
CREATE TABLE `banks_list` (
  `id` int NOT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `banks_list`
--

INSERT INTO `banks_list` (`id`, `bank_name`, `address`, `phone_number`, `website`, `email`, `logo`) VALUES
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
-- Table structure for table `blood_groups`
--

DROP TABLE IF EXISTS `blood_groups`;
CREATE TABLE `blood_groups` (
  `id` int NOT NULL,
  `name` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
CREATE TABLE `books` (
  `id` int UNSIGNED NOT NULL,
  `item_id` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `client_id` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `code` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `isbn` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `book_image` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `author` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `rack_no` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `row_no` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `category_id` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `desc` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `department_id` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` enum('0','1') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `deleted` enum('0','1') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `class_id` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_by` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `books_borrowed`
--

DROP TABLE IF EXISTS `books_borrowed`;
CREATE TABLE `books_borrowed` (
  `id` int UNSIGNED NOT NULL,
  `client_id` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `the_type` enum('issued','requested','request') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'issued',
  `item_id` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_id` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_role` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `books_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `issued_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `fine` varchar(21) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0.00',
  `actual_paid` varchar(21) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0.00',
  `fine_paid` enum('0','1') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `status` enum('Issued','Requested','Returned','Cancelled','Approved') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Issued',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `issued_by` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `actual_date_returned` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `deleted` enum('0','1') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `books_borrowed_details`
--

DROP TABLE IF EXISTS `books_borrowed_details`;
CREATE TABLE `books_borrowed_details` (
  `id` int NOT NULL,
  `borrowed_id` varchar(32) DEFAULT NULL,
  `book_id` varchar(32) DEFAULT NULL,
  `date_borrowed` datetime DEFAULT CURRENT_TIMESTAMP,
  `return_date` date DEFAULT NULL,
  `quantity` int UNSIGNED NOT NULL DEFAULT '0',
  `fine` decimal(10,2) NOT NULL DEFAULT '0.00',
  `actual_paid` decimal(12,2) NOT NULL DEFAULT '0.00',
  `fine_paid` enum('0','1') NOT NULL DEFAULT '0',
  `issued_by` varchar(32) DEFAULT NULL,
  `received_by` varchar(32) DEFAULT NULL,
  `actual_date_returned` datetime DEFAULT NULL,
  `status` enum('Returned','Borrowed') NOT NULL DEFAULT 'Borrowed',
  `deleted` enum('0','1') DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `books_stock`
--

DROP TABLE IF EXISTS `books_stock`;
CREATE TABLE `books_stock` (
  `id` int NOT NULL,
  `books_id` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `quantity` int UNSIGNED DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `books_type`
--

DROP TABLE IF EXISTS `books_type`;
CREATE TABLE `books_type` (
  `id` int NOT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `department_id` varchar(32) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `created_by` varchar(32) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `church_bible_classes`
--

DROP TABLE IF EXISTS `church_bible_classes`;
CREATE TABLE `church_bible_classes` (
  `id` int NOT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `language` varchar(32) DEFAULT NULL,
  `class_leader` varchar(32) DEFAULT NULL,
  `assistant_class_leader` varchar(32) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `church_bible_classes`
--

INSERT INTO `church_bible_classes` (`id`, `item_id`, `client_id`, `name`, `slug`, `language`, `class_leader`, `assistant_class_leader`, `status`) VALUES
(1, 'aB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'MSGCH001', 'Bro. Isaac Awuku Appiah\'s Class', NULL, 'Twi', NULL, NULL, '1'),
(2, 'ghjKJldldldVRHF1X6UmWdtxkDpNTbM', 'MSGCH001', 'Bro. Enoch Osafo\'s Class', NULL, 'Twi', NULL, NULL, '1');

-- --------------------------------------------------------

--
-- Table structure for table `church_booking_log`
--

DROP TABLE IF EXISTS `church_booking_log`;
CREATE TABLE `church_booking_log` (
  `id` int NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `log_date` varchar(32) DEFAULT NULL,
  `members_list` text,
  `members_ids` varchar(1000) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` varchar(32) DEFAULT NULL,
  `state` enum('Logged','Closed') NOT NULL DEFAULT 'Logged'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `church_booking_log`
--

INSERT INTO `church_booking_log` (`id`, `client_id`, `item_id`, `log_date`, `members_list`, `members_ids`, `date_created`, `created_by`, `state`) VALUES
(1, 'MSGCH001', 'nHqkxIeroOB60U2', '2021-06-28', '{\"1\":{\"fullname\":\"Emmanuel Obeng\",\"contact\":\"0550107770\",\"residence\":\"Dodowa\",\"temperature\":\"35.6\",\"item_id\":\"pNhrLzPaGgKVdYynSf\",\"gender\":\"Male\"}}', '[\"pNhrLzPaGgKVdYynSf\"]', '2021-06-28 20:07:32', 'aB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Logged'),
(2, 'MSGCH001', '5xbkm1MpUtYJ9ie', '2021-07-05', '{\"1\":{\"fullname\":\"Franka Akpey\",\"contact\":\"0203900903\",\"residence\":\"Adjiringanor\",\"gender\":\"Female\",\"temperature\":\"36.8\",\"item_id\":\"MwkVSv6m8ITKcQhY9p\"},\"2\":{\"fullname\":\"Philomena Mensah\",\"contact\":\"0204904900\",\"residence\":\"Adjiringanor, East Legon\",\"gender\":\"Female\",\"temperature\":\"35.7\",\"item_id\":\"nq6vbuY3NxA0IHTD5f\"},\"3\":{\"fullname\":\"Patience Mensah\",\"contact\":\"0245687930\",\"residence\":\"Adjiringanor\",\"gender\":\"Female\",\"temperature\":\"36.7\",\"item_id\":\"a6suOlIo4PUD5mH8yK\"}}', '[\"MwkVSv6m8ITKcQhY9p\",\"nq6vbuY3NxA0IHTD5f\",\"a6suOlIo4PUD5mH8yK\"]', '2021-06-28 20:11:01', 'aB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Logged'),
(3, 'MSGCH001', 'yA5WsHGuXeV2ho1', '2021-07-06', '{\"1\":{\"fullname\":\"Clifford Owusu-Acheampong\",\"contact\":\"0240553604\",\"residence\":\"Nana Krom\",\"gender\":\"Male\",\"temperature\":\"34.9\",\"item_id\":\"j0OXFIhuaDG9y2ErNf\"},\"2\":{\"fullname\":\"Henry Owusu-Acheampong\",\"contact\":\"0204904900\",\"residence\":\"Nana Krom\",\"gender\":\"Male\",\"temperature\":\"36.7\",\"item_id\":\"iTlPKERAhe8Hrztxp7\"}}', '[\"j0OXFIhuaDG9y2ErNf\",\"iTlPKERAhe8Hrztxp7\"]', '2021-06-29 19:49:47', 'aB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Logged'),
(4, 'MSGCH001', 'b0fx5uenKdF1saJ', '2021-07-04', '{\"1\":{\"fullname\":\"Samuel Oduro\",\"contact\":\"03200200020\",\"residence\":\"\",\"gender\":\"Male\",\"temperature\":\"35.7\",\"item_id\":\"CSlgetAuWrTkQvy5fj\"},\"2\":{\"fullname\":\"Emmanuel Obeng\",\"contact\":\"0550107770\",\"residence\":\"Dodowa\",\"gender\":\"Male\",\"temperature\":\"36.7\",\"item_id\":\"pNhrLzPaGgKVdYynSf\"},\"3\":{\"fullname\":\"Samuel Essilfie\",\"contact\":\"098377388377\",\"residence\":\"Tesano\",\"gender\":\"Male\",\"temperature\":\"36.5\",\"item_id\":\"xbQM7wkihESZeYI8Ry\"},\"4\":{\"fullname\":\"Frederick Amponsah\",\"contact\":\"0388377398\",\"residence\":\"Accra\",\"gender\":\"Male\",\"temperature\":\"36.7\",\"item_id\":\"z07QBo6ishWVCgurDT\"},\"5\":{\"fullname\":\"Patience Sakyi\",\"contact\":\"0240909039\",\"residence\":\"NTHC Estates\",\"gender\":\"Female\",\"temperature\":\"36.4\",\"item_id\":\"EnNugPbYIxl0jHLCzK\"}}', '[\"CSlgetAuWrTkQvy5fj\",\"pNhrLzPaGgKVdYynSf\",\"xbQM7wkihESZeYI8Ry\",\"z07QBo6ishWVCgurDT\",\"EnNugPbYIxl0jHLCzK\"]', '2021-07-06 08:33:11', 'aB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'Logged');

-- --------------------------------------------------------

--
-- Table structure for table `church_members`
--

DROP TABLE IF EXISTS `church_members`;
CREATE TABLE `church_members` (
  `id` int NOT NULL,
  `client_id` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `item_id` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `unique_id` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `group_id` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `fullname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `contact` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `contact_2` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `profession` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `gender` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `residence` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `organization` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `bible_class` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `state` enum('Active','Visitor','Catcumen','Full','Cease to Meet') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `church_members`
--

INSERT INTO `church_members` (`id`, `client_id`, `item_id`, `unique_id`, `group_id`, `image`, `fullname`, `contact`, `contact_2`, `profession`, `date_of_birth`, `email`, `gender`, `residence`, `organization`, `bible_class`, `state`, `date_created`) VALUES
(1, 'MSGCH001', 'MwkVSv6m8ITKcQhY9p', NULL, NULL, NULL, 'Franka Akpey', '0203900903', NULL, NULL, NULL, NULL, 'Female', 'Adjiringanor', NULL, NULL, NULL, '2021-06-28 23:15:38'),
(2, 'MSGCH001', 'nq6vbuY3NxA0IHTD5f', NULL, NULL, NULL, 'Philomena Mensah', '0204904900', NULL, NULL, NULL, NULL, 'Female', 'Adjiringanor, East Legon', NULL, NULL, NULL, '2021-06-28 23:15:38'),
(3, 'MSGCH001', 'a6suOlIo4PUD5mH8yK', NULL, NULL, NULL, 'Patience Mensah', '0245687930', NULL, NULL, NULL, NULL, 'Female', 'Adjiringanor', NULL, NULL, NULL, '2021-06-28 23:15:39'),
(4, 'MSGCH001', 'pNhrLzPaGgKVdYynSf', NULL, NULL, NULL, 'Emmanuel Obeng', '0550107770', NULL, NULL, NULL, NULL, 'Male', 'Dodowa', NULL, NULL, NULL, '2021-06-28 23:16:34'),
(5, 'MSGCH001', 'j0OXFIhuaDG9y2ErNf', NULL, NULL, NULL, 'Clifford Owusu-Acheampong', '0240553604', NULL, NULL, NULL, NULL, 'Male', 'Nana Krom', NULL, NULL, NULL, '2021-06-29 19:49:47'),
(6, 'MSGCH001', 'iTlPKERAhe8Hrztxp7', NULL, NULL, NULL, 'Henry Owusu-Acheampong', '0204904900', NULL, NULL, NULL, NULL, 'Male', 'Nana Krom', NULL, NULL, NULL, '2021-06-29 19:49:47'),
(7, 'MSGCH001', 'CSlgetAuWrTkQvy5fj', NULL, NULL, NULL, 'Samuel Oduro', '03200200020', NULL, NULL, NULL, NULL, 'Male', '', NULL, NULL, NULL, '2021-07-06 08:33:10'),
(8, 'MSGCH001', 'xbQM7wkihESZeYI8Ry', NULL, NULL, NULL, 'Samuel Essilfie', '098377388377', NULL, NULL, NULL, NULL, 'Male', 'Tesano', NULL, NULL, NULL, '2021-07-06 08:33:10'),
(9, 'MSGCH001', 'z07QBo6ishWVCgurDT', NULL, NULL, NULL, 'Frederick Amponsah', '0388377398', NULL, NULL, NULL, NULL, 'Male', 'Accra', NULL, NULL, NULL, '2021-07-06 08:33:11'),
(10, 'MSGCH001', 'EnNugPbYIxl0jHLCzK', NULL, NULL, NULL, 'Patience Sakyi', '0240909039', NULL, NULL, NULL, NULL, 'Female', 'NTHC Estates', NULL, NULL, NULL, '2021-07-06 08:33:11'),
(13, 'MSGCH001', 'lyN65uYaI1xrDtP3', NULL, NULL, NULL, 'Clifford Owusu Acheampong', '0093993883898', '994884984984', 'Student', NULL, 'clifford@gmail.com', 'Male', 'Nana Krom', '[\"ghjKJldldldVRHF1X6UmWdtxkafdf\",\"klLakSnzVRHF1X6UmWdtxkD8gpNTbM\"]', 'aB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', NULL, '2021-07-06 17:41:42');

-- --------------------------------------------------------

--
-- Table structure for table `church_organizations`
--

DROP TABLE IF EXISTS `church_organizations`;
CREATE TABLE `church_organizations` (
  `id` int NOT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `description` text,
  `executives` text,
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `church_organizations`
--

INSERT INTO `church_organizations` (`id`, `item_id`, `client_id`, `name`, `slug`, `logo`, `description`, `executives`, `status`) VALUES
(1, 'ghjKJldldldVRHF1X6UmWdtxkafdf', 'MSGCH001', 'Brigade', 'brigade', NULL, NULL, NULL, '1'),
(2, 'klLakSnzVRHF1X6UmWdtxkD8gpNTbM', 'MSGCH001', 'Girls Fellowship', 'girls-fellowship', NULL, NULL, NULL, '1');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
CREATE TABLE `classes` (
  `id` int NOT NULL,
  `upload_id` varchar(12) DEFAULT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `client_id` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `class_code` varchar(32) DEFAULT NULL,
  `class_size` int UNSIGNED DEFAULT NULL,
  `courses_list` varchar(2000) DEFAULT NULL,
  `rooms_list` varchar(2000) DEFAULT NULL,
  `weekly_meeting` int UNSIGNED DEFAULT NULL,
  `department_id` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `academic_year` varchar(32) DEFAULT NULL,
  `academic_term` varchar(32) DEFAULT '1',
  `class_teacher` varchar(35) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `class_assistant` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `status` enum('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT '1',
  `created_by` varchar(32) DEFAULT NULL,
  `description` text,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `upload_id`, `item_id`, `client_id`, `name`, `slug`, `class_code`, `class_size`, `courses_list`, `rooms_list`, `weekly_meeting`, `department_id`, `academic_year`, `academic_term`, `class_teacher`, `class_assistant`, `status`, `created_by`, `description`, `date_created`, `date_updated`) VALUES
(1, NULL, 'YoGuMxE8vS5d1iz3Aa42WKUbjNqBygmD', 'MSGH000001', 'Class 6', 'class-6', 'JE1', 30, '[\"pQ9vbrynG43wHDKuz760mdMCsFoZJVXA\",\"JBKXbQYAU4hMimldGuEZNVpr6H3fL1y7\",\"GJmTXfDihU6LW8OBaEQFzqnjANKvuwbl\",\"FxJjgQ7bB14Khr869iGCpTSNEwMfynZu\",\"jPNrKhLWRAp72Q1dsk06bfacXvOCF3UE\"]', '[]', NULL, NULL, '2020/2021', '1st', NULL, NULL, '1', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', NULL, '2021-07-23 10:57:10', '2021-07-23 10:57:10'),
(2, NULL, 'iHYaK8jk0FRsGorylLNf4h7pTgbWXnB9', 'MSGH000001', 'JHS 1', 'jhs-1', 'CL00002', 40, '[\"pQ9vbrynG43wHDKuz760mdMCsFoZJVXA\",\"JBKXbQYAU4hMimldGuEZNVpr6H3fL1y7\",\"GJmTXfDihU6LW8OBaEQFzqnjANKvuwbl\",\"FxJjgQ7bB14Khr869iGCpTSNEwMfynZu\",\"hdqeCQ73NDkcFvPBrJa8OX4TERpxiVuM\",\"jPNrKhLWRAp72Q1dsk06bfacXvOCF3UE\"]', '[]', NULL, NULL, '2020/2021', '1st', NULL, NULL, '1', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', NULL, '2021-07-23 10:57:48', '2021-07-23 10:57:48'),
(3, NULL, 'AMz7oODFB6NnPidcj9uZtIEaV1x0sG83', 'MSGH000001', 'JHS 2', 'jhs-2', 'CL03', 45, '[\"pQ9vbrynG43wHDKuz760mdMCsFoZJVXA\",\"JBKXbQYAU4hMimldGuEZNVpr6H3fL1y7\",\"GJmTXfDihU6LW8OBaEQFzqnjANKvuwbl\",\"FxJjgQ7bB14Khr869iGCpTSNEwMfynZu\",\"hdqeCQ73NDkcFvPBrJa8OX4TERpxiVuM\",\"jPNrKhLWRAp72Q1dsk06bfacXvOCF3UE\"]', '[]', NULL, NULL, '2020/2021', '1st', NULL, NULL, '1', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', NULL, '2021-07-23 10:58:39', '2021-07-23 10:59:04'),
(4, NULL, 'DKm7eAGouxcHIfiw52OljBX6zk3W19pT', 'MSGH000001', 'JHS 3', 'jhs-3', 'CL04', 32, '[\"pQ9vbrynG43wHDKuz760mdMCsFoZJVXA\",\"JBKXbQYAU4hMimldGuEZNVpr6H3fL1y7\",\"GJmTXfDihU6LW8OBaEQFzqnjANKvuwbl\",\"FxJjgQ7bB14Khr869iGCpTSNEwMfynZu\",\"hdqeCQ73NDkcFvPBrJa8OX4TERpxiVuM\",\"jPNrKhLWRAp72Q1dsk06bfacXvOCF3UE\"]', '[]', NULL, NULL, '2020/2021', '1st', NULL, NULL, '1', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', NULL, '2021-07-23 10:58:51', '2021-07-23 10:58:51');

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
  `description` text,
  `classes_list` varchar(2000) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clients_accounts`
--

DROP TABLE IF EXISTS `clients_accounts`;
CREATE TABLE `clients_accounts` (
  `id` int UNSIGNED NOT NULL,
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
  `client_state` enum('Expired','Pending','Activated','Suspended','Active','Propagation','Complete') NOT NULL DEFAULT 'Pending',
  `client_account` varchar(32) DEFAULT NULL,
  `setup` enum('School','Church','Booking') NOT NULL DEFAULT 'School',
  `sms_sender` varchar(32) DEFAULT NULL,
  `ip_address` varchar(32) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `clients_accounts`
--

INSERT INTO `clients_accounts` (`id`, `client_id`, `client_name`, `client_contact`, `client_secondary_contact`, `client_address`, `client_email`, `client_website`, `client_logo`, `client_location`, `client_category`, `client_preferences`, `client_status`, `client_state`, `client_account`, `setup`, `sms_sender`, `ip_address`, `date_created`) VALUES
(1, 'MSGH000001', 'Morning Star International School', '233550107770', '233247685521', 'PMB 2582, Accra Main Post Office', 'emmallob14@gmail.com', 'https://www.morningstarschool.com', NULL, 'Cantonments, Accra', NULL, '{\"academics\":{\"academic_year\":\"2020\\/2021\",\"academic_term\":\"1st\",\"term_starts\":\"2021-07-11\",\"term_ends\":\"2021-09-30\",\"next_academic_year\":\"2020\\/2021\",\"next_academic_term\":\"2nd\",\"next_term_starts\":\"2021-10-18\",\"next_term_ends\":\"2021-12-22\"},\"labels\":{\"student_label\":\"st\",\"parent_label\":\"gl\",\"teacher_label\":\"tl\",\"staff_label\":\"sl\",\"course_label\":\"cl\",\"book_label\":\"bkl\",\"class_label\":\"cl\",\"department_label\":\"dl\",\"section_label\":\"sl\",\"receipt_label\":\"rl\",\"currency\":\"GHS\",\"print_receipt\":\"1\"},\"opening_days\":[\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\",\"Friday\"],\"account\":{\"type\":\"basic\",\"activation_code\":\"7iHJXMS3aWFUVkP6A814T5vBLRdZlYNwoQcejKzCuxygG92bEOrIhq\",\"date_created\":\"2021-07-22 09:20PM\",\"expiry\":\"2021-10-23 11:53AM\",\"verified_date\":\"2021-07-23 11:53AM\"}}', '1', 'Active', NULL, 'School', NULL, '::1', '2021-07-22 21:20:27');

-- --------------------------------------------------------

--
-- Table structure for table `clients_terminal_log`
--

DROP TABLE IF EXISTS `clients_terminal_log`;
CREATE TABLE `clients_terminal_log` (
  `id` int UNSIGNED NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `student_id` varchar(32) DEFAULT NULL,
  `log_type` enum('student','school') NOT NULL DEFAULT 'student',
  `academic_year` varchar(12) DEFAULT NULL,
  `academic_term` varchar(12) DEFAULT NULL,
  `fees_log` varchar(3000) DEFAULT NULL,
  `arrears_log` varchar(3000) DEFAULT NULL,
  `fees_category_log` varchar(2000) DEFAULT NULL,
  `statistics_logs` text,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

DROP TABLE IF EXISTS `contact`;
CREATE TABLE `contact` (
  `id` int NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_agent` varchar(255) DEFAULT NULL,
  `ip_address` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

DROP TABLE IF EXISTS `country`;
CREATE TABLE `country` (
  `id` int NOT NULL,
  `country_name` varchar(255) NOT NULL,
  `country_code` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `id` int UNSIGNED NOT NULL,
  `upload_id` varchar(12) DEFAULT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `client_id` varchar(32) DEFAULT 'LKJAFD94R',
  `course_code` varchar(255) DEFAULT NULL,
  `credit_hours` varchar(25) DEFAULT NULL,
  `academic_year` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `academic_term` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `department_id` varchar(32) DEFAULT NULL,
  `programme_id` varchar(32) DEFAULT NULL,
  `weekly_meeting` int UNSIGNED DEFAULT NULL,
  `class_id` varchar(2000) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `units_count` varchar(12) DEFAULT '0',
  `lessons_count` varchar(12) DEFAULT '0',
  `course_tutor` varchar(2000) DEFAULT NULL COMMENT 'THIS  IS WHERE THE ID OF THE TEACHER OR WHOEVER INSERTED IT WILL APPEAR',
  `description` text,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` varchar(35) DEFAULT NULL,
  `date_updated` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('0','1') DEFAULT '1',
  `deleted` enum('0','1') DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `upload_id`, `item_id`, `client_id`, `course_code`, `credit_hours`, `academic_year`, `academic_term`, `department_id`, `programme_id`, `weekly_meeting`, `class_id`, `name`, `slug`, `units_count`, `lessons_count`, `course_tutor`, `description`, `date_created`, `created_by`, `date_updated`, `status`, `deleted`) VALUES
(1, NULL, 'pQ9vbrynG43wHDKuz760mdMCsFoZJVXA', 'MSGH000001', 'COA', '4', '2020/2021', '1st', NULL, NULL, 6, '[\"YoGuMxE8vS5d1iz3Aa42WKUbjNqBygmD\",\"iHYaK8jk0FRsGorylLNf4h7pTgbWXnB9\",\"AMz7oODFB6NnPidcj9uZtIEaV1x0sG83\",\"DKm7eAGouxcHIfiw52OljBX6zk3W19pT\"]', 'Integrated Science', 'integrated-science', '0', '0', '[]', NULL, '2021-07-23 10:59:58', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-07-23 10:59:58', '1', '0'),
(2, NULL, 'JBKXbQYAU4hMimldGuEZNVpr6H3fL1y7', 'MSGH000001', 'ICT', '3', '2020/2021', '1st', NULL, NULL, 5, '[\"YoGuMxE8vS5d1iz3Aa42WKUbjNqBygmD\",\"iHYaK8jk0FRsGorylLNf4h7pTgbWXnB9\",\"AMz7oODFB6NnPidcj9uZtIEaV1x0sG83\",\"DKm7eAGouxcHIfiw52OljBX6zk3W19pT\"]', 'Information, Communication & Technologo', 'information-communication-technologo', '1', '1', '[]', NULL, '2021-07-23 11:00:58', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-08-09 06:28:04', '1', '0'),
(3, NULL, 'GJmTXfDihU6LW8OBaEQFzqnjANKvuwbl', 'MSGH000001', 'RME', '4', '2020/2021', '1st', NULL, NULL, 6, '[\"YoGuMxE8vS5d1iz3Aa42WKUbjNqBygmD\",\"iHYaK8jk0FRsGorylLNf4h7pTgbWXnB9\",\"AMz7oODFB6NnPidcj9uZtIEaV1x0sG83\",\"DKm7eAGouxcHIfiw52OljBX6zk3W19pT\"]', 'Religious & Moral Education', 'religious-moral-education', '0', '0', '[]', NULL, '2021-07-23 11:01:27', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-07-23 11:01:27', '1', '0'),
(4, NULL, 'FxJjgQ7bB14Khr869iGCpTSNEwMfynZu', 'MSGH000001', 'BDT', '2', '2020/2021', '1st', NULL, NULL, 7, '[\"YoGuMxE8vS5d1iz3Aa42WKUbjNqBygmD\",\"iHYaK8jk0FRsGorylLNf4h7pTgbWXnB9\",\"AMz7oODFB6NnPidcj9uZtIEaV1x0sG83\",\"DKm7eAGouxcHIfiw52OljBX6zk3W19pT\"]', 'Basic, Design and Technology', 'basic-design-and-technology', '0', '0', '[]', NULL, '2021-07-23 11:01:58', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-07-23 11:01:58', '1', '0'),
(5, NULL, 'hdqeCQ73NDkcFvPBrJa8OX4TERpxiVuM', 'MSGH000001', 'SC', '3', '2020/2021', '1st', NULL, NULL, 5, '[\"iHYaK8jk0FRsGorylLNf4h7pTgbWXnB9\",\"AMz7oODFB6NnPidcj9uZtIEaV1x0sG83\",\"DKm7eAGouxcHIfiw52OljBX6zk3W19pT\"]', 'Social Studies', 'social-studies', '0', '0', '[]', NULL, '2021-07-23 11:03:13', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-07-23 11:03:13', '1', '0'),
(6, NULL, 'jPNrKhLWRAp72Q1dsk06bfacXvOCF3UE', 'MSGH000001', 'MATH', '3', '2020/2021', '1st', NULL, NULL, 6, '[\"YoGuMxE8vS5d1iz3Aa42WKUbjNqBygmD\",\"iHYaK8jk0FRsGorylLNf4h7pTgbWXnB9\",\"AMz7oODFB6NnPidcj9uZtIEaV1x0sG83\",\"DKm7eAGouxcHIfiw52OljBX6zk3W19pT\"]', 'Mathematics', 'mathematics', '0', '0', '[]', NULL, '2021-07-23 11:05:34', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-07-23 11:05:34', '1', '0');

-- --------------------------------------------------------

--
-- Table structure for table `courses_plan`
--

DROP TABLE IF EXISTS `courses_plan`;
CREATE TABLE `courses_plan` (
  `id` int NOT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `course_id` varchar(32) DEFAULT NULL,
  `unit_id` varchar(32) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `plan_type` enum('unit','lesson') NOT NULL DEFAULT 'unit',
  `academic_term` varchar(32) DEFAULT NULL,
  `academic_year` varchar(32) DEFAULT NULL,
  `description` text,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_by` varchar(32) DEFAULT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `courses_plan`
--

INSERT INTO `courses_plan` (`id`, `item_id`, `client_id`, `course_id`, `unit_id`, `name`, `plan_type`, `academic_term`, `academic_year`, `description`, `start_date`, `end_date`, `created_by`, `date_created`, `date_updated`, `status`) VALUES
(1, 'Yg2APBDjcmaN5CKVyZ9qL8hE4OS0FQIG', 'MSGH000001', '2', NULL, 'Introduction to Computing', 'unit', '1st', '2020/2021', '&lt;div&gt;&lt;!--block--&gt;This agreement is made this day &lt;strong&gt;11th July, 2021, &lt;/strong&gt;Between &lt;strong&gt;WILLIAM ADJEI ABANKWA &lt;/strong&gt;of House No. BT 128, Sekyere Begoro in the Fanteakwa North District in the Eastern Region of the Republic of Ghana (Hereinafter called the Lessors); &lt;strong&gt;MR. DANIEL KENNETH BANSAH, MR. PETER KERSI AND MR. WILLIAM KWAO ANSAH &lt;/strong&gt;(Hereinafter called the Lessee).&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;&lt;br&gt;&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;That no member of my family has the right of ownership of the said farm land again after the total payment have been made.&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;&lt;br&gt;&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;That in consideration, of Sixty Thousand Ghana Cedis (GHS60,000.00) as part payment for the total amount of One Hundred and Twenty Thousand Ghana Cedis Only (GHS120,000.00) has been made for the farmland.&lt;/div&gt;', '2021-08-09', '2021-08-09', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-08-09 06:20:54', '2021-08-09 06:20:54', '1'),
(2, 'QSmGlUMeCy1ujpcihozqH2avLI8An59J', 'MSGH000001', '2', '1', 'Introduction', 'lesson', '1st', '2020/2021', '&lt;div&gt;&lt;!--block--&gt;This agreement is made this day &lt;strong&gt;11th July, 2021, &lt;/strong&gt;Between &lt;strong&gt;WILLIAM ADJEI ABANKWA &lt;/strong&gt;of House No. BT 128, Sekyere Begoro in the Fanteakwa North District in the Eastern Region of the Republic of Ghana (Hereinafter called the Lessors); &lt;strong&gt;MR. DANIEL KENNETH BANSAH, MR. PETER KERSI AND MR. WILLIAM KWAO ANSAH &lt;/strong&gt;(Hereinafter called the Lessee).&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;&lt;br&gt;&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;That no member of my family has the right of ownership of the said farm land again after the total payment have been made.&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;&lt;br&gt;&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;That in consideration, of Sixty Thousand Ghana Cedis (GHS60,000.00) as part payment for the total amount of One Hundred and Twenty Thousand Ghana Cedis Only (GHS120,000.00) has been made for the farmland.&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;&lt;br&gt;&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;&lt;strong&gt;SIGNED / MARKED &lt;/strong&gt;and delivered by the said&nbsp;&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;&lt;strong&gt;MR. WILLIAM ADJEI ABANKWA &lt;/strong&gt;after the&nbsp;&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;contents have been read and explained to them in ………………………………….&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;Akan Language where they seemed to understand &lt;strong&gt;Lessors&lt;/strong&gt;&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;before signing their signature / mark&lt;/div&gt;&lt;div&gt;&lt;!--block--&gt;thumbprint hereto in the presence of: -&lt;/div&gt;', '2021-08-09', '2021-08-11', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-08-09 06:28:04', '2021-08-09 06:28:04', '1');

-- --------------------------------------------------------

--
-- Table structure for table `courses_resource_links`
--

DROP TABLE IF EXISTS `courses_resource_links`;
CREATE TABLE `courses_resource_links` (
  `id` int NOT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `course_id` varchar(32) DEFAULT NULL,
  `lesson_id` varchar(2000) DEFAULT NULL,
  `description` text,
  `academic_year` varchar(32) DEFAULT NULL,
  `academic_term` varchar(32) DEFAULT NULL,
  `resource_type` enum('link','file') NOT NULL DEFAULT 'link',
  `link_url` varchar(500) DEFAULT NULL,
  `link_name` varchar(500) DEFAULT NULL,
  `created_by` varchar(32) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cron_scheduler`
--

DROP TABLE IF EXISTS `cron_scheduler`;
CREATE TABLE `cron_scheduler` (
  `id` int NOT NULL,
  `query` text,
  `item_id` varchar(255) DEFAULT NULL,
  `user_id` varchar(32) DEFAULT NULL,
  `notice_code` varchar(12) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `cron_type` varchar(32) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0',
  `active_date` datetime DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_processed` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `currency`
--

DROP TABLE IF EXISTS `currency`;
CREATE TABLE `currency` (
  `id` int NOT NULL,
  `currency` varchar(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `id` int NOT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `client_id` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `upload_id` varchar(32) DEFAULT NULL,
  `department_code` varchar(32) DEFAULT NULL,
  `name` varchar(25) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT 'assets/img/placeholder.jpg',
  `description` text,
  `department_head` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `created_by` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `id` int NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
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
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `replies_count` varchar(13) NOT NULL DEFAULT '0',
  `comments_count` varchar(13) NOT NULL DEFAULT '0',
  `state` enum('Pending','Cancelled','Held','Ongoing') NOT NULL DEFAULT 'Pending',
  `status` enum('0','1') DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events_types`
--

DROP TABLE IF EXISTS `events_types`;
CREATE TABLE `events_types` (
  `id` int NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `name` varchar(244) DEFAULT 'Public Holiday',
  `slug` varchar(64) DEFAULT 'public-holiday',
  `description` varchar(5000) NOT NULL DEFAULT 'This is the general category for all public holidays',
  `color_code` varchar(10) DEFAULT '#6777ef',
  `icon` varchar(244) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `events_types`
--

INSERT INTO `events_types` (`id`, `client_id`, `item_id`, `name`, `slug`, `description`, `color_code`, `icon`, `status`) VALUES
(1, 'MSGH000001', 'I5XHxeni3P0R6wmszgTC9hYSqWyrD7Ef', 'Public Holiday', 'public-holiday', 'This is the general category for all public holidays', '#6777ef', NULL, '1');

-- --------------------------------------------------------

--
-- Table structure for table `e_learning`
--

DROP TABLE IF EXISTS `e_learning`;
CREATE TABLE `e_learning` (
  `id` int NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `description` text,
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
  `updated_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `e_learning_comments`
--

DROP TABLE IF EXISTS `e_learning_comments`;
CREATE TABLE `e_learning_comments` (
  `id` int NOT NULL,
  `type` enum('comment','reply') DEFAULT 'comment',
  `comment_id` varchar(5) DEFAULT NULL,
  `comment` text,
  `record_id` varchar(120) DEFAULT NULL,
  `user_id` varchar(32) DEFAULT NULL,
  `ipaddress` varchar(244) DEFAULT NULL,
  `user_agent` varchar(244) DEFAULT NULL,
  `status` enum('0','1') DEFAULT '1',
  `likes` varchar(12) DEFAULT '0',
  `dislikes` varchar(12) NOT NULL DEFAULT '0',
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `e_learning_timer`
--

DROP TABLE IF EXISTS `e_learning_timer`;
CREATE TABLE `e_learning_timer` (
  `id` int UNSIGNED NOT NULL,
  `user_id` varchar(32) DEFAULT NULL,
  `video_id` varchar(120) DEFAULT NULL,
  `timer` varchar(12) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `e_learning_views`
--

DROP TABLE IF EXISTS `e_learning_views`;
CREATE TABLE `e_learning_views` (
  `id` int UNSIGNED NOT NULL,
  `video_id` varchar(255) DEFAULT NULL,
  `views` varchar(15) DEFAULT '0',
  `views_array` text,
  `comments` varchar(12) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fees_allocations`
--

DROP TABLE IF EXISTS `fees_allocations`;
CREATE TABLE `fees_allocations` (
  `id` int UNSIGNED NOT NULL,
  `client_id` varchar(32) NOT NULL DEFAULT '1',
  `programme_id` varchar(32) DEFAULT NULL,
  `class_id` varchar(32) DEFAULT NULL,
  `category_id` varchar(32) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(32) DEFAULT NULL,
  `academic_year` varchar(25) NOT NULL DEFAULT '2019/2020',
  `academic_term` varchar(30) NOT NULL DEFAULT '1st',
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `fees_allocations`
--

INSERT INTO `fees_allocations` (`id`, `client_id`, `programme_id`, `class_id`, `category_id`, `amount`, `currency`, `academic_year`, `academic_term`, `status`, `date_created`, `created_by`) VALUES
(1, 'MSGH000001', NULL, '4', '1', '750.00', 'GHS', '2020/2021', '1st', '1', '2021-07-30 11:08:47', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL'),
(2, 'MSGH000001', NULL, '4', '2', '100.00', 'GHS', '2020/2021', '1st', '1', '2021-07-30 11:08:52', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL'),
(3, 'MSGH000001', NULL, '4', '3', '150.00', 'GHS', '2020/2021', '1st', '1', '2021-07-30 11:08:55', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL'),
(4, 'MSGH000001', NULL, '4', '4', '100.00', 'GHS', '2020/2021', '1st', '1', '2021-07-30 11:08:59', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL'),
(5, 'MSGH000001', NULL, '4', '5', '200.00', 'GHS', '2020/2021', '1st', '1', '2021-07-30 11:09:03', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL'),
(6, 'MSGH000001', NULL, '4', '6', '100.00', 'GHS', '2020/2021', '1st', '1', '2021-07-30 11:09:06', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL');

-- --------------------------------------------------------

--
-- Table structure for table `fees_category`
--

DROP TABLE IF EXISTS `fees_category`;
CREATE TABLE `fees_category` (
  `id` int UNSIGNED NOT NULL,
  `client_id` varchar(32) NOT NULL DEFAULT '1',
  `name` varchar(255) DEFAULT NULL,
  `amount` varchar(32) DEFAULT NULL,
  `code` varchar(32) DEFAULT NULL,
  `description` text,
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `fees_category`
--

INSERT INTO `fees_category` (`id`, `client_id`, `name`, `amount`, `code`, `description`, `status`) VALUES
(1, 'MSGH000001', 'Tuition Fees', '750', 'TUI', NULL, '1'),
(2, 'MSGH000001', 'Project Fees', '100', 'PJ', NULL, '1'),
(3, 'MSGH000001', 'Library Dues', '150', 'LD', NULL, '1'),
(4, 'MSGH000001', 'ICT', '100', 'ICT', NULL, '1'),
(5, 'MSGH000001', 'Sports & Recreational', '200', 'SR', NULL, '1'),
(6, 'MSGH000001', 'Examination Fees', '100', NULL, NULL, '1');

-- --------------------------------------------------------

--
-- Table structure for table `fees_collection`
--

DROP TABLE IF EXISTS `fees_collection`;
CREATE TABLE `fees_collection` (
  `id` int UNSIGNED NOT NULL,
  `client_id` varchar(32) NOT NULL DEFAULT '1',
  `item_id` varchar(32) DEFAULT NULL,
  `receipt_id` varchar(32) DEFAULT NULL,
  `payment_id` varchar(32) DEFAULT NULL,
  `student_id` varchar(32) DEFAULT NULL,
  `department_id` int UNSIGNED DEFAULT NULL,
  `programme_id` int UNSIGNED DEFAULT NULL,
  `class_id` int UNSIGNED DEFAULT NULL,
  `payment_method` enum('Cash','Cheque','MoMo_Card') NOT NULL DEFAULT 'Cash',
  `cheque_bank` varchar(255) DEFAULT NULL,
  `cheque_number` varchar(64) DEFAULT NULL,
  `cheque_security` varchar(64) DEFAULT NULL,
  `paidin_by` varchar(64) DEFAULT NULL,
  `paidin_contact` varchar(32) DEFAULT NULL,
  `currency` varchar(20) DEFAULT NULL,
  `category_id` int UNSIGNED DEFAULT NULL,
  `amount` decimal(25,2) DEFAULT '0.00',
  `email_address` varchar(64) DEFAULT NULL,
  `contact_number` varchar(64) DEFAULT NULL,
  `created_by` varchar(32) DEFAULT NULL,
  `recorded_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `description` text,
  `aggregator` varchar(1000) DEFAULT NULL,
  `academic_year` varchar(25) DEFAULT '2019/2020',
  `academic_term` varchar(25) DEFAULT '1st',
  `reversed` enum('0','1') DEFAULT '0',
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `fees_collection`
--

INSERT INTO `fees_collection` (`id`, `client_id`, `item_id`, `receipt_id`, `payment_id`, `student_id`, `department_id`, `programme_id`, `class_id`, `payment_method`, `cheque_bank`, `cheque_number`, `cheque_security`, `paidin_by`, `paidin_contact`, `currency`, `category_id`, `amount`, `email_address`, `contact_number`, `created_by`, `recorded_date`, `description`, `aggregator`, `academic_year`, `academic_term`, `reversed`, `status`) VALUES
(1, 'MSGH000001', 'GmaIVf6t7gJxX3Y', 'RL00001', 'RL00001', 'KDtbYhedUAgTC8sG1caxV6LfEklMjvFn', NULL, NULL, 4, 'Cash', NULL, NULL, NULL, 'testmail@gmail.com', '0550107770', 'GHS', 1, '500.00', NULL, NULL, 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-07-30 11:09:53', NULL, NULL, '2020/2021', '1st', '0', '1'),
(2, 'MSGH000001', 'XVSFxWz0sYaET2K', 'RL00002', 'Nzg7Bo0h2CYm8rt', 'KDtbYhedUAgTC8sG1caxV6LfEklMjvFn', NULL, NULL, 4, 'Cash', NULL, NULL, NULL, '', '', 'GHS', 1, '250.00', NULL, NULL, 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-07-30 11:10:27', NULL, NULL, '2020/2021', '1st', '0', '1'),
(3, 'MSGH000001', 'khNyi5fSZgDYtRO', 'RL00003', 'Nzg7Bo0h2CYm8rt', 'KDtbYhedUAgTC8sG1caxV6LfEklMjvFn', NULL, NULL, 4, 'Cash', NULL, NULL, NULL, '', '', 'GHS', 2, '100.00', NULL, NULL, 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-07-30 11:10:27', NULL, NULL, '2020/2021', '1st', '0', '1'),
(4, 'MSGH000001', 'oT3Zkq0H4CaJzYi', 'RL00004', 'Nzg7Bo0h2CYm8rt', 'KDtbYhedUAgTC8sG1caxV6LfEklMjvFn', NULL, NULL, 4, 'Cash', NULL, NULL, NULL, '', '', 'GHS', 3, '150.00', NULL, NULL, 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-07-30 11:10:27', NULL, NULL, '2020/2021', '1st', '0', '1'),
(5, 'MSGH000001', 'LMY0Vh3a9y15Ebi', 'RL00005', 'Y3DMseEwgqaTnov', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn', NULL, NULL, 4, 'Cash', NULL, NULL, NULL, 'frederick@gmail.com', '0204399404', 'GHS', 1, '750.00', NULL, NULL, 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-07-30 11:19:47', NULL, NULL, '2020/2021', '1st', '0', '1'),
(6, 'MSGH000001', 'ryJXf6W3jZhLTzQ', 'RL00006', 'Y3DMseEwgqaTnov', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn', NULL, NULL, 4, 'Cash', NULL, NULL, NULL, 'frederick@gmail.com', '0204399404', 'GHS', 2, '100.00', NULL, NULL, 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-07-30 11:19:47', NULL, NULL, '2020/2021', '1st', '0', '1'),
(7, 'MSGH000001', 'fknpr2oBXiFPSdW', 'RL00007', 'Y3DMseEwgqaTnov', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn', NULL, NULL, 4, 'Cash', NULL, NULL, NULL, 'frederick@gmail.com', '0204399404', 'GHS', 3, '150.00', NULL, NULL, 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-07-30 11:19:47', NULL, NULL, '2020/2021', '1st', '0', '1'),
(8, 'MSGH000001', 'lKkBjAvEIuH6Sfn', 'RL00008', 'f4hYVUz2jAJqK0C', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn', NULL, NULL, 4, 'Cash', NULL, NULL, NULL, '', '', 'GHS', 4, '100.00', NULL, NULL, 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-07-30 11:37:09', NULL, NULL, '2020/2021', '1st', '0', '1'),
(9, 'MSGH000001', '061FbK3pck5Vqr4', 'RL00009', 'f4hYVUz2jAJqK0C', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn', NULL, NULL, 4, 'Cash', NULL, NULL, NULL, '', '', 'GHS', 5, '200.00', NULL, NULL, 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-07-30 11:37:09', NULL, NULL, '2020/2021', '1st', '0', '1'),
(10, 'MSGH000001', 'iLJ3r4eXOQEuAbj', 'RL00010', 'f4hYVUz2jAJqK0C', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn', NULL, NULL, 4, 'Cash', NULL, NULL, NULL, '', '', 'GHS', 6, '100.00', NULL, NULL, 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-07-30 11:37:09', NULL, NULL, '2020/2021', '1st', '0', '1'),
(11, 'MSGH000001', '5GWt9Lzmn2bSOgk', 'RL00011', 'DM2FuzCt7g9jwTc', 'KDtbYhedUAgTC8sG1caxV6LfEklMjvFn', NULL, NULL, 4, 'Cash', NULL, NULL, NULL, '', '', 'GHS', 4, '100.00', NULL, NULL, 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-07-30 21:08:14', NULL, NULL, '2020/2021', '1st', '0', '1'),
(12, 'MSGH000001', '6Wo2XnkgJ1CQcmj', 'RL00012', 'DM2FuzCt7g9jwTc', 'KDtbYhedUAgTC8sG1caxV6LfEklMjvFn', NULL, NULL, 4, 'Cash', NULL, NULL, NULL, '', '', 'GHS', 5, '200.00', NULL, NULL, 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-07-30 21:08:14', NULL, NULL, '2020/2021', '1st', '0', '1'),
(13, 'MSGH000001', '7fIj3NobZYw8AMF', 'RL00013', 'DM2FuzCt7g9jwTc', 'KDtbYhedUAgTC8sG1caxV6LfEklMjvFn', NULL, NULL, 4, 'Cash', NULL, NULL, NULL, '', '', 'GHS', 6, '100.00', NULL, NULL, 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-07-30 21:08:14', NULL, NULL, '2020/2021', '1st', '0', '1');

-- --------------------------------------------------------

--
-- Table structure for table `fees_payments`
--

DROP TABLE IF EXISTS `fees_payments`;
CREATE TABLE `fees_payments` (
  `id` int UNSIGNED NOT NULL,
  `client_id` varchar(32) NOT NULL DEFAULT '1',
  `checkout_url` varchar(32) DEFAULT NULL,
  `student_id` varchar(32) DEFAULT NULL,
  `class_id` varchar(32) DEFAULT NULL,
  `category_id` int UNSIGNED DEFAULT NULL,
  `currency` varchar(32) DEFAULT NULL,
  `amount_due` decimal(10,2) NOT NULL DEFAULT '0.00',
  `amount_paid` decimal(10,2) NOT NULL DEFAULT '0.00',
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `academic_year` varchar(25) DEFAULT '2019/2020',
  `academic_term` varchar(25) DEFAULT '1st',
  `editable` enum('0','1') NOT NULL DEFAULT '1',
  `paid_status` enum('0','1','2') NOT NULL DEFAULT '0' COMMENT '0=not paid at all, 1=full paid, 2=part payment',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_payment_date` datetime DEFAULT NULL,
  `last_payment_id` varchar(32) DEFAULT NULL,
  `created_by` varchar(32) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `fees_payments`
--

INSERT INTO `fees_payments` (`id`, `client_id`, `checkout_url`, `student_id`, `class_id`, `category_id`, `currency`, `amount_due`, `amount_paid`, `balance`, `academic_year`, `academic_term`, `editable`, `paid_status`, `date_created`, `last_payment_date`, `last_payment_id`, `created_by`, `status`) VALUES
(1, 'MSGH000001', 'HNYqlXMGadstjxB90F6nJTi1EpDKuVwm', 'KDtbYhedUAgTC8sG1caxV6LfEklMjvFn', '4', 1, 'GHS', '750.00', '750.00', '0.00', '2020/2021', '1st', '1', '1', '2021-07-30 11:08:47', '2021-07-30 11:10:27', 'XVSFxWz0sYaET2K', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1'),
(2, 'MSGH000001', 'xTlyF7HOcSQLg8DjnPAIervsXwR1Bh9E', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn', '4', 1, 'GHS', '750.00', '750.00', '0.00', '2020/2021', '1st', '1', '1', '2021-07-30 11:08:47', '2021-07-30 11:19:47', 'LMY0Vh3a9y15Ebi', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1'),
(3, 'MSGH000001', 'qYoEBVs1NgapQiIrDFckXn7R3ZLmzwGP', 'KDtbYhedUAgTC8sG1caxV6LfEklMjvFn', '4', 2, 'GHS', '100.00', '100.00', '0.00', '2020/2021', '1st', '1', '1', '2021-07-30 11:08:52', '2021-07-30 11:10:27', 'khNyi5fSZgDYtRO', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1'),
(4, 'MSGH000001', 'UcYJZBCGPRloQjSMht2a5ifzdWkbunrX', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn', '4', 2, 'GHS', '100.00', '100.00', '0.00', '2020/2021', '1st', '1', '1', '2021-07-30 11:08:52', '2021-07-30 11:19:47', 'ryJXf6W3jZhLTzQ', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1'),
(5, 'MSGH000001', 'WG6vdRk9zQE14Vfus5nNq8KO7iby3gxo', 'KDtbYhedUAgTC8sG1caxV6LfEklMjvFn', '4', 3, 'GHS', '150.00', '150.00', '0.00', '2020/2021', '1st', '1', '1', '2021-07-30 11:08:55', '2021-07-30 11:10:27', 'oT3Zkq0H4CaJzYi', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1'),
(6, 'MSGH000001', 'VFUTfh5goj2H4ZYEvCKBiAOxtn13LpeX', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn', '4', 3, 'GHS', '150.00', '150.00', '0.00', '2020/2021', '1st', '1', '1', '2021-07-30 11:08:55', '2021-07-30 11:19:47', 'fknpr2oBXiFPSdW', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1'),
(7, 'MSGH000001', 'Tugdh9CsXmH8DElWRjUJyOYNtarqIfQK', 'KDtbYhedUAgTC8sG1caxV6LfEklMjvFn', '4', 4, 'GHS', '100.00', '100.00', '0.00', '2020/2021', '1st', '1', '1', '2021-07-30 11:08:59', '2021-07-30 21:08:14', '5GWt9Lzmn2bSOgk', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1'),
(8, 'MSGH000001', '6k0VXQBgv3WUpFjtmr1n9DdlcY4uGz5i', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn', '4', 4, 'GHS', '100.00', '100.00', '0.00', '2020/2021', '1st', '1', '1', '2021-07-30 11:08:59', '2021-07-30 11:37:09', 'lKkBjAvEIuH6Sfn', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1'),
(9, 'MSGH000001', 'fCiDKFdU5mJuaAgSZ9xEtoyPkcWeT8NB', 'KDtbYhedUAgTC8sG1caxV6LfEklMjvFn', '4', 5, 'GHS', '200.00', '200.00', '0.00', '2020/2021', '1st', '1', '1', '2021-07-30 11:09:03', '2021-07-30 21:08:14', '6Wo2XnkgJ1CQcmj', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1'),
(10, 'MSGH000001', 'KirUmoZGBVkbsCtSuYWw143HxRhe6d9T', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn', '4', 5, 'GHS', '200.00', '200.00', '0.00', '2020/2021', '1st', '1', '1', '2021-07-30 11:09:03', '2021-07-30 11:37:09', '061FbK3pck5Vqr4', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1'),
(11, 'MSGH000001', 'nH3eOt6NklwVBFaLoXKmDup8jcyqWxA4', 'KDtbYhedUAgTC8sG1caxV6LfEklMjvFn', '4', 6, 'GHS', '100.00', '100.00', '0.00', '2020/2021', '1st', '1', '1', '2021-07-30 11:09:06', '2021-07-30 21:08:14', '7fIj3NobZYw8AMF', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1'),
(12, 'MSGH000001', '4gbea5wzMU2FlTKRdCy6fA8W0YmoGEpX', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn', '4', 6, 'GHS', '100.00', '100.00', '0.00', '2020/2021', '1st', '1', '1', '2021-07-30 11:09:06', '2021-07-30 11:37:09', 'iLJ3r4eXOQEuAbj', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1');

-- --------------------------------------------------------

--
-- Table structure for table `files_attachment`
--

DROP TABLE IF EXISTS `files_attachment`;
CREATE TABLE `files_attachment` (
  `id` int UNSIGNED NOT NULL,
  `resource` varchar(32) DEFAULT NULL,
  `description` text,
  `attachment_size` varchar(16) DEFAULT NULL,
  `record_id` varchar(80) DEFAULT NULL,
  `resource_id` varchar(66) DEFAULT NULL,
  `created_by` varchar(32) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `files_attachment`
--

INSERT INTO `files_attachment` (`id`, `resource`, `description`, `attachment_size`, `record_id`, `resource_id`, `created_by`, `date_created`) VALUES
(1, 'accounts_transaction', '{\"files\":[],\"files_count\":0,\"raw_size_mb\":0,\"files_size\":\"0MB\"}', '0', 'jBmQV1zGN8A3e6M', 'jBmQV1zGN8A3e6M', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-07-31 09:30:14'),
(2, 'accounts_transaction', '{\"files\":[],\"files_count\":0,\"raw_size_mb\":0,\"files_size\":\"0MB\"}', '0', 'JbxwjX3B7ec5n89', 'JbxwjX3B7ec5n89', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-07-31 09:31:21'),
(3, 'courses_plan', '{\"files\":[{\"unique_id\":\"qROdU3AKyD2W5loGehZ7SQMt9gcX8nEYwCraubmi1PfF0BL6vHjkJVN\",\"name\":\"Business Plan.docx\",\"path\":\"assets\\/uploads\\/HjwXoJQNresMBFznDW7qAGaTCOY0c6kL\\/docs\\/course_lesson_1\\/usiness_lan.docx\",\"type\":\"docx\",\"size\":\"11.44KB\",\"size_raw\":\"11.44\",\"is_deleted\":0,\"record_id\":\"QSmGlUMeCy1ujpcihozqH2avLI8An59J\",\"datetime\":\"Monday, 9th August 2021 06:28:04AM\",\"favicon\":\"fa fa-file-word fa-1x\",\"color\":\"primary\",\"uploaded_by\":\"Emmanuel Obeng Hyde &bull; 9th Aug 2021\",\"uploaded_by_id\":\"HjwXoJQNresMBFznDW7qAGaTCOY0c6kL\"},{\"unique_id\":\"1ZaIV8ruFjXfdRvkzg2O6pGmTN9Pe4xJWKMBDQnSos7ihH0CctEUAqb\",\"name\":\"Commissioning Service.docx\",\"path\":\"assets\\/uploads\\/HjwXoJQNresMBFznDW7qAGaTCOY0c6kL\\/docs\\/course_lesson_1\\/ommissioning_ervice.docx\",\"type\":\"docx\",\"size\":\"388.8KB\",\"size_raw\":\"388.8\",\"is_deleted\":0,\"record_id\":\"QSmGlUMeCy1ujpcihozqH2avLI8An59J\",\"datetime\":\"Monday, 9th August 2021 06:28:04AM\",\"favicon\":\"fa fa-file-word fa-1x\",\"color\":\"primary\",\"uploaded_by\":\"Emmanuel Obeng Hyde &bull; 9th Aug 2021\",\"uploaded_by_id\":\"HjwXoJQNresMBFznDW7qAGaTCOY0c6kL\"},{\"unique_id\":\"xWBFGt2ab4MuLhvfE76mHSe8DNpzjTgskUyR3V95nIqroAlYiwQCOK1\",\"name\":\"Deed of Transfer.pdf\",\"path\":\"assets\\/uploads\\/HjwXoJQNresMBFznDW7qAGaTCOY0c6kL\\/docs\\/course_lesson_1\\/eed_of_ransfer.pdf\",\"type\":\"pdf\",\"size\":\"30.59KB\",\"size_raw\":\"30.59\",\"is_deleted\":0,\"record_id\":\"QSmGlUMeCy1ujpcihozqH2avLI8An59J\",\"datetime\":\"Monday, 9th August 2021 06:28:04AM\",\"favicon\":\"fa fa-file-pdf fa-1x\",\"color\":\"danger\",\"uploaded_by\":\"Emmanuel Obeng Hyde &bull; 9th Aug 2021\",\"uploaded_by_id\":\"HjwXoJQNresMBFznDW7qAGaTCOY0c6kL\"}],\"files_count\":3,\"raw_size_mb\":0.42,\"files_size\":\"0.42MB\"}', '0.42', 'QSmGlUMeCy1ujpcihozqH2avLI8An59J', '2', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-08-09 06:28:05');

-- --------------------------------------------------------

--
-- Table structure for table `grading_system`
--

DROP TABLE IF EXISTS `grading_system`;
CREATE TABLE `grading_system` (
  `id` int NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `grading` text,
  `structure` text,
  `show_position` varchar(8) DEFAULT 'true',
  `allow_submission` varchar(12) NOT NULL DEFAULT '''true''',
  `show_teacher_name` varchar(8) NOT NULL DEFAULT 'false',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `grading_system`
--

INSERT INTO `grading_system` (`id`, `client_id`, `grading`, `structure`, `show_position`, `allow_submission`, `show_teacher_name`, `date_created`) VALUES
(1, 'MSGH000001', '{\"1\":{\"start\":\"0\",\"end\":\"30\",\"interpretation\":\"Fail\"},\"2\":{\"start\":\"31\",\"end\":\"40\",\"interpretation\":\"Pass\"},\"3\":{\"start\":\"41\",\"end\":\"50\",\"interpretation\":\"Credit\"},\"4\":{\"start\":\"51\",\"end\":\"60\",\"interpretation\":\"Good\"},\"5\":{\"start\":\"61\",\"end\":\"70\",\"interpretation\":\"Very Good\"},\"6\":{\"start\":\"71\",\"end\":\"80\",\"interpretation\":\"Excellent\"},\"7\":{\"start\":\"81\",\"end\":\"100\",\"interpretation\":\"Distinction\"}}', '{\"course_title\":\"true\",\"average_score\":\"true\",\"show_position\":\"true\",\"show_teacher_name\":\"true\",\"allow_submission\":\"false\",\"teacher_comments\":\"true\"}', 'true', 'false', 'true', '2021-07-23 05:47:58');

-- --------------------------------------------------------

--
-- Table structure for table `grading_terminal_logs`
--

DROP TABLE IF EXISTS `grading_terminal_logs`;
CREATE TABLE `grading_terminal_logs` (
  `id` int NOT NULL,
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
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` varchar(32) DEFAULT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grading_terminal_scores`
--

DROP TABLE IF EXISTS `grading_terminal_scores`;
CREATE TABLE `grading_terminal_scores` (
  `id` int NOT NULL,
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
  `scores` text,
  `total_score` int UNSIGNED DEFAULT NULL,
  `average_score` varchar(32) DEFAULT NULL,
  `class_position` varchar(32) DEFAULT NULL,
  `teacher_ids` varchar(500) DEFAULT NULL,
  `teachers_name` text,
  `class_teacher_remarks` varchar(500) DEFAULT NULL,
  `status` enum('Saved','Cancelled','Submitted','Approved','Rejected') DEFAULT 'Saved',
  `created_by` varchar(32) DEFAULT NULL,
  `academic_year` varchar(32) DEFAULT NULL,
  `academic_term` varchar(32) DEFAULT NULL,
  `date_submitted` datetime DEFAULT NULL,
  `date_approved` datetime DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guardian_relation`
--

DROP TABLE IF EXISTS `guardian_relation`;
CREATE TABLE `guardian_relation` (
  `id` int NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `guardian_relation`
--

INSERT INTO `guardian_relation` (`id`, `name`, `status`) VALUES
(1, 'Parent', '1');

-- --------------------------------------------------------

--
-- Table structure for table `incidents`
--

DROP TABLE IF EXISTS `incidents`;
CREATE TABLE `incidents` (
  `id` int NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `incident_id` varchar(32) DEFAULT NULL,
  `user_id` varchar(32) DEFAULT NULL,
  `user_role` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `assigned_to` varchar(32) DEFAULT NULL,
  `created_by` varchar(32) DEFAULT NULL,
  `reported_by` varchar(255) DEFAULT NULL,
  `incident_type` enum('incident','followup') DEFAULT 'incident',
  `subject` varchar(255) DEFAULT NULL,
  `description` text,
  `incident_date` date DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  `status` enum('Pending','Processing','Solved','Cancelled') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `knowledge_base`
--

DROP TABLE IF EXISTS `knowledge_base`;
CREATE TABLE `knowledge_base` (
  `id` int NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `parent_id` int NOT NULL DEFAULT '0',
  `subject` varchar(32) DEFAULT NULL,
  `section` varchar(64) DEFAULT NULL,
  `department` varchar(32) DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `user_type` enum('user','support') NOT NULL DEFAULT 'user',
  `rating` int NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime DEFAULT NULL,
  `status` enum('Pending','Answered','Closed','Waiting','Reopened') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'Pending',
  `user_id` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `knowledge_base`
--

INSERT INTO `knowledge_base` (`id`, `client_id`, `parent_id`, `subject`, `section`, `department`, `content`, `user_type`, `rating`, `date_created`, `date_updated`, `status`, `user_id`) VALUES
(1, 'MSGH000001', 0, 'Paid for SMS Article', 'Payments', 'Sales_Billing', '<!-- wp:paragraph -->\r\n<p>A viral video of a lady opening giving some hot slaps to a man adjudged to be her boyfriend has caused an uproar on social media.<br>In the video circulating, a lady is heard asking his man about another lady called “Abigail” and when the man failed to answer who Abigail is, he started chopping slaps of his life.</p>\r\n<!-- /wp:paragraph -->\r\n\r\n<!-- wp:paragraph -->\r\n<p>Probably, our man has been caught cheating and that could be the reason for his inability to answer that simple “Abigail” question. If you were the man in question, what would’ve been your reaction?<br>Watch the video below:</p>\r\n<!-- /wp:paragraph -->\r\n\r\n<!-- wp:paragraph -->\r\n<p>A viral video of a lady opening giving some hot slaps to a man adjudged to be her boyfriend has caused an uproar on social media.<br>In the video circulating, a lady is heard asking his man about another lady called “Abigail” and when the man failed to answer who Abigail is, he started chopping slaps of his life.</p>\r\n<!-- /wp:paragraph -->\r\n\r\n<!-- wp:paragraph -->\r\n<p>Probably, our man has been caught cheating and that could be the reason for his inability to answer that simple “Abigail” question. If you were the man in question, what would’ve been your reaction?<br>Watch the video below:</p>\r\n<!-- /wp:paragraph -->', 'user', 0, '2021-08-07 12:59:56', '2021-08-08 13:51:45', 'Waiting', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn'),
(2, 'MSGH000001', 0, 'Problem on Page', 'Courses_Lesson_Planner', 'Usage_Problem', '<!-- wp:heading --><h2>Who we are</h2><!-- /wp:heading --><!-- wp:paragraph --><p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>Our website address is: http://localhost/wordpress.</p><!-- /wp:paragraph --><!-- wp:heading --><h2>Comments</h2><!-- /wp:heading --><!-- wp:paragraph --><p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>When visitors leave comments on the site we collect the data shown in the comments form, and also the visitor&#8217;s IP address and browser user agent string to help spam detection.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>An anonymized string created from your email address (also called a hash) may be provided to the Gravatar service to see if you are using it. The Gravatar service privacy policy is available here: https://automattic.com/privacy/. After approval of your comment, your profile picture is visible to the public in the context of your comment.</p><!-- /wp:paragraph --><!-- wp:heading --><h2>Media</h2><!-- /wp:heading --><!-- wp:paragraph --><p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>If you upload images to the website, you should avoid uploading images with embedded location data (EXIF GPS) included. Visitors to the website can download and extract any location data from images on the website.</p><!-- /wp:paragraph --><!-- wp:heading --><h2>Cookies</h2><!-- /wp:heading --><!-- wp:paragraph --><p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>If you leave a comment on our site you may opt-in to saving your name, email address and website in cookies. These are for your convenience so that you do not have to fill in your details again when you leave another comment. These cookies will last for one year.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>If you visit our login page, we will set a temporary cookie to determine if your browser accepts cookies. This cookie contains no personal data and is discarded when you close your browser.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>When you log in, we will also set up several cookies to save your login information and your screen display choices. Login cookies last for two days, and screen options cookies last for a year. If you select &quot;Remember Me&quot;, your login will persist for two weeks. If you log out of your account, the login cookies will be removed.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>If you edit or publish an article, an additional cookie will be saved in your browser. This cookie includes no personal data and simply indicates the post ID of the article you just edited. It expires after 1 day.</p><!-- /wp:paragraph --><!-- wp:heading --><h2>Embedded content from other websites</h2><!-- /wp:heading --><!-- wp:paragraph --><p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>Articles on this site may include embedded content (e.g. videos, images, articles, etc.). Embedded content from other websites behaves in the exact same way as if the visitor has visited the other website.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>These websites may collect data about you, use cookies, embed additional third-party tracking, and monitor your interaction with that embedded content, including tracking your interaction with the embedded content if you have an account and are logged in to that website.</p><!-- /wp:paragraph --><!-- wp:heading --><h2>Who we share your data with</h2><!-- /wp:heading --><!-- wp:paragraph --><p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>If you request a password reset, your IP address will be included in the reset email.</p><!-- /wp:paragraph --><!-- wp:heading --><h2>How long we retain your data</h2><!-- /wp:heading --><!-- wp:paragraph --><p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>If you leave a comment, the comment and its metadata are retained indefinitely. This is so we can recognize and approve any follow-up comments automatically instead of holding them in a moderation queue.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>For users that register on our website (if any), we also store the personal information they provide in their user profile. All users can see, edit, or delete their personal information at any time (except they cannot change their username). Website administrators can also see and edit that information.</p><!-- /wp:paragraph --><!-- wp:heading --><h2>What rights you have over your data</h2><!-- /wp:heading --><!-- wp:paragraph --><p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>If you have an account on this site, or have left comments, you can request to receive an exported file of the personal data we hold about you, including any data you have provided to us. You can also request that we erase any personal data we hold about you. This does not include any data we are obliged to keep for administrative, legal, or security purposes.</p><!-- /wp:paragraph --><!-- wp:heading --><h2>Where we send your data</h2><!-- /wp:heading --><!-- wp:paragraph --><p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>Visitor comments may be checked through an automated spam detection service.</p><!-- /wp:paragraph -->', 'user', 0, '2021-08-07 13:03:58', '2021-08-08 13:50:28', 'Waiting', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn'),
(11, 'MSGH000001', 1, NULL, NULL, NULL, 'Alright Thanks for the reply. I really do appreaciate it.', 'user', 0, '2021-08-07 14:53:46', NULL, 'Pending', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn'),
(15, 'MSGH000001', 1, NULL, NULL, NULL, 'Test reply to this item. I am only testing to see if it works as expected', 'user', 0, '2021-08-08 13:35:35', NULL, 'Pending', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL'),
(16, 'MSGH000001', 2, NULL, NULL, NULL, 'This is a thread message sent to to this article', 'user', 0, '2021-08-08 13:40:08', NULL, 'Pending', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL'),
(17, 'MSGH000001', 1, NULL, NULL, NULL, 'This is a test item parsed here', 'user', 0, '2021-08-08 13:40:46', NULL, 'Pending', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL'),
(18, 'MSGH000001', 2, NULL, NULL, NULL, 'This is a test reply', 'user', 0, '2021-08-08 13:49:38', NULL, 'Pending', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL'),
(19, 'MSGH000001', 2, NULL, NULL, NULL, 'Another test reply here... fix redirection bug', 'user', 0, '2021-08-08 13:50:28', NULL, 'Pending', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL'),
(20, 'MSGH000001', 1, NULL, NULL, NULL, 'Send a comment and change the reply information', 'user', 0, '2021-08-08 13:51:45', NULL, 'Pending', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL');

-- --------------------------------------------------------

--
-- Table structure for table `payment_urls`
--

DROP TABLE IF EXISTS `payment_urls`;
CREATE TABLE `payment_urls` (
  `id` int NOT NULL,
  `short_url` varchar(32) NOT NULL,
  `checkout_url` varchar(600) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payslips`
--

DROP TABLE IF EXISTS `payslips`;
CREATE TABLE `payslips` (
  `id` int UNSIGNED NOT NULL,
  `client_id` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `item_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `employee_id` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `basic_salary` double(12,2) NOT NULL DEFAULT '0.00',
  `total_allowance` double(12,2) NOT NULL DEFAULT '0.00',
  `total_deductions` double(12,2) NOT NULL DEFAULT '0.00',
  `gross_salary` double(12,2) NOT NULL DEFAULT '0.00',
  `net_salary` double(12,2) NOT NULL DEFAULT '0.00',
  `payslip_month` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `payslip_month_id` date DEFAULT NULL,
  `payslip_year` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_mode` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_by` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `validated` enum('0','1') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `validated_date` datetime DEFAULT NULL,
  `comments` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `date_log` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('0','1') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '0',
  `deleted` enum('0','1') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `payslips`
--

INSERT INTO `payslips` (`id`, `client_id`, `item_id`, `employee_id`, `basic_salary`, `total_allowance`, `total_deductions`, `gross_salary`, `net_salary`, `payslip_month`, `payslip_month_id`, `payslip_year`, `payment_mode`, `created_by`, `validated`, `validated_date`, `comments`, `date_log`, `status`, `deleted`) VALUES
(2, 'MSGH000001', 'Nk1HRvQEy7faUI9tAOTBiSLnJrhuMYb8', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 2000.00, 320.00, 435.00, 2320.00, 1885.00, 'January', '2021-01-31', '2021', 'Bank', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1', '2021-07-31 16:09:11', NULL, '2021-07-31 15:55:32', '1', '0'),
(3, 'MSGH000001', 'o0W58OxEnIVylw9qb1DdgeA4CN2sFPML', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 2000.00, 320.00, 435.00, 2320.00, 1885.00, 'February', '2021-02-28', '2021', 'Bank', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1', '2021-07-31 16:09:05', NULL, '2021-07-31 15:57:06', '1', '0'),
(4, 'MSGH000001', 'hfPom0C5EkWX7vMa3w1dsZyVKJpQ9c6j', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 2000.00, 320.00, 435.00, 2320.00, 1885.00, 'March', '2021-03-31', '2021', 'Bank', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1', '2021-07-31 16:09:08', NULL, '2021-07-31 15:59:08', '1', '0');

-- --------------------------------------------------------

--
-- Table structure for table `payslips_allowance_types`
--

DROP TABLE IF EXISTS `payslips_allowance_types`;
CREATE TABLE `payslips_allowance_types` (
  `id` int NOT NULL,
  `client_id` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(1000) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `default_amount` varchar(12) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '0.00',
  `status` enum('0','1') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `payslips_allowance_types`
--

INSERT INTO `payslips_allowance_types` (`id`, `client_id`, `name`, `description`, `type`, `default_amount`, `status`) VALUES
(1, 'MSGH000001', 'Transport', NULL, 'Allowance', NULL, '1'),
(2, 'MSGH000001', 'Overtime', NULL, 'Allowance', NULL, '1'),
(3, 'MSGH000001', 'SSNIT', NULL, 'Deduction', NULL, '1'),
(4, 'MSGH000001', 'Income Tax', NULL, 'Deduction', NULL, '1');

-- --------------------------------------------------------

--
-- Table structure for table `payslips_details`
--

DROP TABLE IF EXISTS `payslips_details`;
CREATE TABLE `payslips_details` (
  `id` int UNSIGNED NOT NULL,
  `client_id` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `payslip_id` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `allowance_id` int DEFAULT NULL,
  `employee_id` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `detail_type` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `payslip_month` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `payslip_year` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount` double(12,2) NOT NULL DEFAULT '0.00',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `payslips_details`
--

INSERT INTO `payslips_details` (`id`, `client_id`, `payslip_id`, `allowance_id`, `employee_id`, `detail_type`, `payslip_month`, `payslip_year`, `amount`, `date_created`) VALUES
(13, 'MSGH000001', '2', 1, 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Allowance', 'January', '2021', 200.00, '2021-07-31 15:55:32'),
(14, 'MSGH000001', '2', 2, 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Allowance', 'January', '2021', 120.00, '2021-07-31 15:55:32'),
(15, 'MSGH000001', '2', 3, 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Deduction', 'January', '2021', 250.00, '2021-07-31 15:55:32'),
(16, 'MSGH000001', '2', 4, 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Deduction', 'January', '2021', 185.00, '2021-07-31 15:55:32'),
(17, 'MSGH000001', '3', 1, 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Allowance', 'February', '2021', 200.00, '2021-07-31 15:57:06'),
(18, 'MSGH000001', '3', 2, 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Allowance', 'February', '2021', 120.00, '2021-07-31 15:57:06'),
(19, 'MSGH000001', '3', 3, 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Deduction', 'February', '2021', 250.00, '2021-07-31 15:57:06'),
(20, 'MSGH000001', '3', 4, 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Deduction', 'February', '2021', 185.00, '2021-07-31 15:57:06'),
(21, 'MSGH000001', '4', 1, 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Allowance', 'March', '2021', 200.00, '2021-07-31 15:59:08'),
(22, 'MSGH000001', '4', 2, 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Allowance', 'March', '2021', 120.00, '2021-07-31 15:59:08'),
(23, 'MSGH000001', '4', 3, 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Deduction', 'March', '2021', 250.00, '2021-07-31 15:59:08'),
(24, 'MSGH000001', '4', 4, 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Deduction', 'March', '2021', 185.00, '2021-07-31 15:59:08');

-- --------------------------------------------------------

--
-- Table structure for table `payslips_employees_allowances`
--

DROP TABLE IF EXISTS `payslips_employees_allowances`;
CREATE TABLE `payslips_employees_allowances` (
  `id` int NOT NULL,
  `client_id` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `allowance_id` int DEFAULT NULL,
  `employee_id` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount` double(12,2) NOT NULL DEFAULT '0.00',
  `type` enum('Allowance','Deduction') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `payslips_employees_allowances`
--

INSERT INTO `payslips_employees_allowances` (`id`, `client_id`, `allowance_id`, `employee_id`, `amount`, `type`, `date_created`) VALUES
(1, 'MSGH000001', 1, 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 200.00, 'Allowance', '2021-07-30 21:13:26'),
(2, 'MSGH000001', 2, 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 120.00, 'Allowance', '2021-07-30 21:13:26'),
(3, 'MSGH000001', 3, 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 250.00, 'Deduction', '2021-07-30 21:13:26'),
(4, 'MSGH000001', 4, 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 185.00, 'Deduction', '2021-07-30 21:13:26');

-- --------------------------------------------------------

--
-- Table structure for table `payslips_employees_payroll`
--

DROP TABLE IF EXISTS `payslips_employees_payroll`;
CREATE TABLE `payslips_employees_payroll` (
  `id` int NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `employee_id` varchar(32) DEFAULT NULL,
  `basic_salary` double(12,2) DEFAULT '0.00',
  `allowances` double(12,2) DEFAULT '0.00',
  `deductions` double(12,2) DEFAULT '0.00',
  `net_allowance` double(12,2) DEFAULT '0.00',
  `gross_salary` double(12,2) DEFAULT '0.00',
  `net_salary` double(12,2) DEFAULT '0.00',
  `account_name` varchar(255) DEFAULT NULL,
  `account_number` varchar(32) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `bank_branch` varchar(255) DEFAULT NULL,
  `ssnit_number` varchar(255) DEFAULT NULL,
  `tin_number` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payslips_employees_payroll`
--

INSERT INTO `payslips_employees_payroll` (`id`, `client_id`, `employee_id`, `basic_salary`, `allowances`, `deductions`, `net_allowance`, `gross_salary`, `net_salary`, `account_name`, `account_number`, `bank_name`, `bank_branch`, `ssnit_number`, `tin_number`) VALUES
(1, 'MSGH000001', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 2000.00, 320.00, 435.00, -115.00, 2320.00, 1885.00, 'Emmanuel Obeng', '201909200290', '10', 'Accra', 'fFd93988300393', '0329930039303');

-- --------------------------------------------------------

--
-- Table structure for table `periods`
--

DROP TABLE IF EXISTS `periods`;
CREATE TABLE `periods` (
  `id` int NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `date` varchar(64) DEFAULT NULL,
  `period_start` varchar(64) DEFAULT NULL,
  `period_end` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `promotions_history`
--

DROP TABLE IF EXISTS `promotions_history`;
CREATE TABLE `promotions_history` (
  `id` int UNSIGNED NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `history_log_id` varchar(32) DEFAULT NULL,
  `academic_year` varchar(32) DEFAULT NULL,
  `academic_term` varchar(32) DEFAULT NULL,
  `promote_from` varchar(32) DEFAULT NULL,
  `promote_to` varchar(32) DEFAULT NULL,
  `logged_by` varchar(32) DEFAULT NULL,
  `status` enum('Pending','Processed','Cancelled') NOT NULL DEFAULT 'Pending',
  `date_log` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `promotions_log`
--

DROP TABLE IF EXISTS `promotions_log`;
CREATE TABLE `promotions_log` (
  `id` int NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `student_id` varchar(32) DEFAULT NULL,
  `history_log_id` varchar(32) DEFAULT NULL,
  `academic_year` varchar(32) DEFAULT NULL,
  `academic_term` varchar(32) DEFAULT NULL,
  `promote_from` varchar(32) DEFAULT NULL,
  `promote_to` varchar(32) DEFAULT NULL,
  `is_promoted` enum('0','1','2','3') NOT NULL DEFAULT '0' COMMENT '0=not promoted, 1=promoted, 2=on hold and 3 = cancelled',
  `promoted_by` varchar(32) DEFAULT NULL,
  `date_log` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
CREATE TABLE `sections` (
  `id` int NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `name` varchar(25) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `section_code` varchar(32) DEFAULT NULL,
  `image` varchar(255) DEFAULT 'assets/img/placeholder.jpg',
  `description` text,
  `section_leader` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `created_by` varchar(32) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `smsemail_balance`
--

DROP TABLE IF EXISTS `smsemail_balance`;
CREATE TABLE `smsemail_balance` (
  `id` int UNSIGNED NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `sms_sent` varchar(12) DEFAULT '0',
  `sms_balance` varchar(12) DEFAULT '0',
  `email_sent` varchar(12) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `smsemail_balance`
--

INSERT INTO `smsemail_balance` (`id`, `client_id`, `sms_sent`, `sms_balance`, `email_sent`) VALUES
(1, 'MSGH000001', '0', '20', '0');

-- --------------------------------------------------------

--
-- Table structure for table `smsemail_send_list`
--

DROP TABLE IF EXISTS `smsemail_send_list`;
CREATE TABLE `smsemail_send_list` (
  `id` int UNSIGNED NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `type` enum('sms','email') DEFAULT 'sms',
  `campaign_name` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text,
  `recipient_group` varchar(32) DEFAULT NULL,
  `recipient_list` text,
  `recipient_ids` text,
  `recieved_count` varchar(21) NOT NULL DEFAULT '0',
  `units_used` varchar(12) DEFAULT '0',
  `schedule_time` datetime DEFAULT NULL,
  `sent_status` enum('Pending','Delivered') NOT NULL DEFAULT 'Pending',
  `sent_time` datetime DEFAULT NULL,
  `created_by` varchar(32) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `smsemail_templates`
--

DROP TABLE IF EXISTS `smsemail_templates`;
CREATE TABLE `smsemail_templates` (
  `id` int UNSIGNED NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `type` varchar(32) DEFAULT NULL,
  `module` enum('FeesPayment') DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `message` text,
  `academic_year` varchar(32) DEFAULT NULL,
  `academic_term` varchar(32) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` varchar(32) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sms_packages`
--

DROP TABLE IF EXISTS `sms_packages`;
CREATE TABLE `sms_packages` (
  `id` int UNSIGNED NOT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `units` varchar(32) DEFAULT NULL,
  `amount` varchar(32) DEFAULT NULL,
  `amount_purchased` varchar(12) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sms_packages`
--

INSERT INTO `sms_packages` (`id`, `item_id`, `units`, `amount`, `amount_purchased`) VALUES
(1, 'erGNnx02z8OpX', '250', '10', '10'),
(2, 'IVTct3Oy0Mzlj', '510', '20', '20'),
(3, 'EndiQ8RrWAfP9', '1388', '50', '50'),
(4, 'NyHIzwc1qrusS', '3125', '100', '0'),
(5, 'VoaFAZCG5pUxk', '6650', '200', '0'),
(6, 'U9PT3NRSBIXuL', '17300', '500', '0');

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

DROP TABLE IF EXISTS `support_tickets`;
CREATE TABLE `support_tickets` (
  `id` int NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `parent_id` int NOT NULL DEFAULT '0',
  `subject` varchar(32) DEFAULT NULL,
  `section` varchar(64) DEFAULT NULL,
  `department` varchar(32) DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `user_type` enum('user','support') NOT NULL DEFAULT 'user',
  `rating` int NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime DEFAULT NULL,
  `status` enum('Pending','Answered','Closed','Waiting','Reopened') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'Pending',
  `user_id` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `support_tickets`
--

INSERT INTO `support_tickets` (`id`, `client_id`, `parent_id`, `subject`, `section`, `department`, `content`, `user_type`, `rating`, `date_created`, `date_updated`, `status`, `user_id`) VALUES
(1, 'MSGH000001', 0, 'Paid for SMS', 'Payments', 'Sales_Billing', 'Hello, Please i have made payment for an sms but it has not yet reflected on my account. Please help me out with this. Thank you.', 'user', 0, '2021-08-07 12:59:56', '2021-08-07 14:53:46', 'Closed', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn'),
(2, 'MSGH000001', 0, 'Problem on Page', 'Courses_Lesson_Planner', 'Usage_Problem', 'Hello, Please i am finding challenges with some of the use case most especially on the Students Fees Payment page. Please what steps can i follow to reverse a fees payment made on the system. This is very urgent. Help me out please.', 'user', 0, '2021-08-07 13:03:58', '2021-08-07 15:04:23', 'Closed', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn'),
(6, 'MSGH000001', 2, NULL, NULL, NULL, 'Please i have waited and waited and waited but no reponse. Please help me with this. Thank you.', 'user', 0, '2021-08-07 14:13:36', NULL, 'Pending', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn'),
(7, 'MSGH000001', 2, NULL, NULL, NULL, 'Oh please why are you delaying replying me on this issue... The message was sent about 20 minutes ago but no reply as at now.', 'user', 0, '2021-08-07 14:19:01', NULL, 'Pending', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn'),
(8, 'MSGH000001', 1, NULL, NULL, NULL, 'Whats the situation on this one too?', 'user', 0, '2021-08-07 14:19:17', NULL, 'Pending', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn'),
(9, 'MSGH000001', 1, NULL, NULL, NULL, 'What sought of Customer service are you running at your end?', 'user', 0, '2021-08-07 14:27:08', NULL, 'Pending', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn'),
(10, 'MSGH000001', 1, NULL, NULL, NULL, 'Thanks for reaching out to us. Please kindly note that your account has been credited with the amount. Thank you.', 'support', 0, '2021-08-07 14:48:35', NULL, 'Pending', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL'),
(11, 'MSGH000001', 1, NULL, NULL, NULL, 'Alright Thanks for the reply. I really do appreaciate it.', 'user', 0, '2021-08-07 14:53:46', NULL, 'Pending', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn'),
(12, 'MSGH000001', 2, NULL, NULL, NULL, 'Kindly send us the Receipt ID of the payment to reverse. Thank you.', 'support', 0, '2021-08-07 15:03:01', NULL, 'Pending', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL'),
(13, 'MSGH000001', 2, NULL, NULL, NULL, 'It is RE190293 Thanks for the support.', 'user', 0, '2021-08-07 15:03:39', NULL, 'Pending', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn'),
(14, 'MSGH000001', 2, NULL, NULL, NULL, 'We have successfully reversed the payment. Please for contacting support.', 'support', 0, '2021-08-07 15:04:23', NULL, 'Pending', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL');

-- --------------------------------------------------------

--
-- Table structure for table `timetables`
--

DROP TABLE IF EXISTS `timetables`;
CREATE TABLE `timetables` (
  `item_id` varchar(32) NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `days` int UNSIGNED NOT NULL DEFAULT '5',
  `slots` int UNSIGNED NOT NULL DEFAULT '0',
  `duration` int UNSIGNED NOT NULL DEFAULT '90',
  `class_id` varchar(1000) DEFAULT NULL,
  `department_id` varchar(32) DEFAULT NULL,
  `start_hr` char(2) NOT NULL DEFAULT '08',
  `start_min` char(2) NOT NULL DEFAULT '30',
  `start_mer` enum('AM','PM') NOT NULL DEFAULT 'AM',
  `start_time` varchar(22) DEFAULT NULL,
  `allow_conflicts` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `frozen` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `academic_year` varchar(32) DEFAULT NULL,
  `academic_term` varchar(32) DEFAULT NULL,
  `disabled_inputs` varchar(2000) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `published` enum('0','1') NOT NULL DEFAULT '1',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `timetables`
--

INSERT INTO `timetables` (`item_id`, `client_id`, `name`, `days`, `slots`, `duration`, `class_id`, `department_id`, `start_hr`, `start_min`, `start_mer`, `start_time`, `allow_conflicts`, `frozen`, `academic_year`, `academic_term`, `disabled_inputs`, `status`, `published`, `date_created`, `last_updated`) VALUES
('th4qpA2Wcy6eMJslnoixXSgEUrzPK8I1', 'MSGH000001', 'Class 6 Timetable Calendar', 5, 8, 60, 'YoGuMxE8vS5d1iz3Aa42WKUbjNqBygmD', NULL, '08', '30', 'AM', '08:00', 0, 0, '2020/2021', '1st', '[]', '1', '1', '2021-08-03 05:01:05', '2021-08-09 06:34:43');

-- --------------------------------------------------------

--
-- Table structure for table `timetables_slots_allocation`
--

DROP TABLE IF EXISTS `timetables_slots_allocation`;
CREATE TABLE `timetables_slots_allocation` (
  `id` int UNSIGNED NOT NULL,
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
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `timetables_slots_allocation`
--

INSERT INTO `timetables_slots_allocation` (`id`, `client_id`, `timetable_id`, `day`, `slot`, `day_slot`, `room_id`, `class_id`, `course_id`, `students_id`, `tutors_id`, `status`, `date_created`) VALUES
(1, 'MSGH000001', 'th4qpA2Wcy6eMJslnoixXSgEUrzPK8I1', '1', '2', '1_2', '', 'YoGuMxE8vS5d1iz3Aa42WKUbjNqBygmD', 'FxJjgQ7bB14Khr869iGCpTSNEwMfynZu', NULL, NULL, '1', '2021-08-09 06:34:41'),
(2, 'MSGH000001', 'th4qpA2Wcy6eMJslnoixXSgEUrzPK8I1', '2', '4', '2_4', '', 'YoGuMxE8vS5d1iz3Aa42WKUbjNqBygmD', 'JBKXbQYAU4hMimldGuEZNVpr6H3fL1y7', NULL, NULL, '1', '2021-08-09 06:34:41'),
(3, 'MSGH000001', 'th4qpA2Wcy6eMJslnoixXSgEUrzPK8I1', '3', '6', '3_6', '', 'YoGuMxE8vS5d1iz3Aa42WKUbjNqBygmD', 'pQ9vbrynG43wHDKuz760mdMCsFoZJVXA', NULL, NULL, '1', '2021-08-09 06:34:41'),
(4, 'MSGH000001', 'th4qpA2Wcy6eMJslnoixXSgEUrzPK8I1', '4', '7', '4_7', '', 'YoGuMxE8vS5d1iz3Aa42WKUbjNqBygmD', 'jPNrKhLWRAp72Q1dsk06bfacXvOCF3UE', NULL, NULL, '1', '2021-08-09 06:34:41'),
(5, 'MSGH000001', 'th4qpA2Wcy6eMJslnoixXSgEUrzPK8I1', '5', '8', '5_8', '', 'YoGuMxE8vS5d1iz3Aa42WKUbjNqBygmD', 'GJmTXfDihU6LW8OBaEQFzqnjANKvuwbl', NULL, NULL, '1', '2021-08-09 06:34:42'),
(6, 'MSGH000001', 'th4qpA2Wcy6eMJslnoixXSgEUrzPK8I1', '3', '2', '3_2', '', 'YoGuMxE8vS5d1iz3Aa42WKUbjNqBygmD', 'FxJjgQ7bB14Khr869iGCpTSNEwMfynZu', NULL, NULL, '1', '2021-08-09 06:34:42'),
(7, 'MSGH000001', 'th4qpA2Wcy6eMJslnoixXSgEUrzPK8I1', '5', '2', '5_2', '', 'YoGuMxE8vS5d1iz3Aa42WKUbjNqBygmD', 'FxJjgQ7bB14Khr869iGCpTSNEwMfynZu', NULL, NULL, '1', '2021-08-09 06:34:42'),
(8, 'MSGH000001', 'th4qpA2Wcy6eMJslnoixXSgEUrzPK8I1', '1', '6', '1_6', '', 'YoGuMxE8vS5d1iz3Aa42WKUbjNqBygmD', 'JBKXbQYAU4hMimldGuEZNVpr6H3fL1y7', NULL, NULL, '1', '2021-08-09 06:34:42'),
(9, 'MSGH000001', 'th4qpA2Wcy6eMJslnoixXSgEUrzPK8I1', '4', '5', '4_5', '', 'YoGuMxE8vS5d1iz3Aa42WKUbjNqBygmD', 'JBKXbQYAU4hMimldGuEZNVpr6H3fL1y7', NULL, NULL, '1', '2021-08-09 06:34:42'),
(10, 'MSGH000001', 'th4qpA2Wcy6eMJslnoixXSgEUrzPK8I1', '5', '6', '5_6', '', 'YoGuMxE8vS5d1iz3Aa42WKUbjNqBygmD', 'JBKXbQYAU4hMimldGuEZNVpr6H3fL1y7', NULL, NULL, '1', '2021-08-09 06:34:42'),
(11, 'MSGH000001', 'th4qpA2Wcy6eMJslnoixXSgEUrzPK8I1', '1', '8', '1_8', '', 'YoGuMxE8vS5d1iz3Aa42WKUbjNqBygmD', 'pQ9vbrynG43wHDKuz760mdMCsFoZJVXA', NULL, NULL, '1', '2021-08-09 06:34:42'),
(12, 'MSGH000001', 'th4qpA2Wcy6eMJslnoixXSgEUrzPK8I1', '2', '3', '2_3', '', 'YoGuMxE8vS5d1iz3Aa42WKUbjNqBygmD', 'pQ9vbrynG43wHDKuz760mdMCsFoZJVXA', NULL, NULL, '1', '2021-08-09 06:34:42'),
(13, 'MSGH000001', 'th4qpA2Wcy6eMJslnoixXSgEUrzPK8I1', '4', '3', '4_3', '', 'YoGuMxE8vS5d1iz3Aa42WKUbjNqBygmD', 'pQ9vbrynG43wHDKuz760mdMCsFoZJVXA', NULL, NULL, '1', '2021-08-09 06:34:42'),
(14, 'MSGH000001', 'th4qpA2Wcy6eMJslnoixXSgEUrzPK8I1', '5', '4', '5_4', '', 'YoGuMxE8vS5d1iz3Aa42WKUbjNqBygmD', 'pQ9vbrynG43wHDKuz760mdMCsFoZJVXA', NULL, NULL, '1', '2021-08-09 06:34:43'),
(15, 'MSGH000001', 'th4qpA2Wcy6eMJslnoixXSgEUrzPK8I1', '1', '4', '1_4', '', 'YoGuMxE8vS5d1iz3Aa42WKUbjNqBygmD', 'jPNrKhLWRAp72Q1dsk06bfacXvOCF3UE', NULL, NULL, '1', '2021-08-09 06:34:43'),
(16, 'MSGH000001', 'th4qpA2Wcy6eMJslnoixXSgEUrzPK8I1', '2', '6', '2_6', '', 'YoGuMxE8vS5d1iz3Aa42WKUbjNqBygmD', 'jPNrKhLWRAp72Q1dsk06bfacXvOCF3UE', NULL, NULL, '1', '2021-08-09 06:34:43'),
(17, 'MSGH000001', 'th4qpA2Wcy6eMJslnoixXSgEUrzPK8I1', '3', '4', '3_4', '', 'YoGuMxE8vS5d1iz3Aa42WKUbjNqBygmD', 'jPNrKhLWRAp72Q1dsk06bfacXvOCF3UE', NULL, NULL, '1', '2021-08-09 06:34:43'),
(18, 'MSGH000001', 'th4qpA2Wcy6eMJslnoixXSgEUrzPK8I1', '5', '3', '5_3', '', 'YoGuMxE8vS5d1iz3Aa42WKUbjNqBygmD', 'jPNrKhLWRAp72Q1dsk06bfacXvOCF3UE', NULL, NULL, '1', '2021-08-09 06:34:43'),
(19, 'MSGH000001', 'th4qpA2Wcy6eMJslnoixXSgEUrzPK8I1', '2', '8', '2_8', '', 'YoGuMxE8vS5d1iz3Aa42WKUbjNqBygmD', 'GJmTXfDihU6LW8OBaEQFzqnjANKvuwbl', NULL, NULL, '1', '2021-08-09 06:34:43'),
(20, 'MSGH000001', 'th4qpA2Wcy6eMJslnoixXSgEUrzPK8I1', '3', '5', '3_5', '', 'YoGuMxE8vS5d1iz3Aa42WKUbjNqBygmD', 'GJmTXfDihU6LW8OBaEQFzqnjANKvuwbl', NULL, NULL, '1', '2021-08-09 06:34:43'),
(21, 'MSGH000001', 'th4qpA2Wcy6eMJslnoixXSgEUrzPK8I1', '4', '2', '4_2', '', 'YoGuMxE8vS5d1iz3Aa42WKUbjNqBygmD', 'GJmTXfDihU6LW8OBaEQFzqnjANKvuwbl', NULL, NULL, '1', '2021-08-09 06:34:43'),
(22, 'MSGH000001', 'th4qpA2Wcy6eMJslnoixXSgEUrzPK8I1', '1', '3', '1_3', '', 'YoGuMxE8vS5d1iz3Aa42WKUbjNqBygmD', 'GJmTXfDihU6LW8OBaEQFzqnjANKvuwbl', NULL, NULL, '1', '2021-08-09 06:34:43');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_logs`
--

DROP TABLE IF EXISTS `transaction_logs`;
CREATE TABLE `transaction_logs` (
  `id` int UNSIGNED NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `transaction_id` varchar(32) DEFAULT NULL,
  `endpoint` varchar(32) DEFAULT NULL,
  `reference_id` varchar(32) DEFAULT NULL,
  `transaction_data` text,
  `metadata` varchar(1000) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `amount` varchar(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `conflicting` varchar(32) DEFAULT NULL,
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
  `access_level` int UNSIGNED NOT NULL DEFAULT '6',
  `preferences` varchar(2000) DEFAULT '{"payments":{},"default_payment":"mobile_money","theme_color":"sidebar-light","sidebar":"sidebar-opened","font-size":"12px","list_count":"200","idb_init":{"init":0,"idb_last_init":"2020-09-18","idb_next_init":"2020-09-21"},"sidebar_nav":"sidebar-opened","quick_links":{"chat":"on","calendar":"on"}}',
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  `verified_email` enum('Y','N') DEFAULT 'N',
  `last_login` datetime DEFAULT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `phone_number_2` varchar(64) DEFAULT NULL,
  `description` text,
  `position` varchar(255) DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `online` enum('0','1') NOT NULL DEFAULT '0',
  `chat_status` varchar(255) DEFAULT NULL,
  `last_seen` datetime DEFAULT CURRENT_TIMESTAMP,
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
  `last_password_change` datetime DEFAULT NULL,
  `country` int UNSIGNED DEFAULT NULL,
  `verify_token` varchar(120) DEFAULT NULL,
  `verified_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `token_expiry` varchar(32) DEFAULT NULL,
  `changed_password` enum('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT '0',
  `account_balance` varchar(32) DEFAULT '0',
  `city` varchar(255) DEFAULT NULL,
  `relationship` varchar(64) DEFAULT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `last_updated` datetime DEFAULT NULL,
  `created_by` varchar(32) DEFAULT NULL,
  `image` varchar(255) DEFAULT 'assets/img/user.png',
  `previous_school` varchar(500) DEFAULT NULL,
  `previous_school_qualification` varchar(500) DEFAULT NULL,
  `previous_school_remarks` text,
  `user_status` enum('Pending','Transferred','Active','Graduated','Dismissed') NOT NULL DEFAULT 'Active',
  `perma_image` varchar(255) DEFAULT 'assets/img/user.png',
  `user_type` enum('teacher','employee','parent','admin','student','accountant') DEFAULT NULL,
  `last_visited_page` varchar(255) DEFAULT '{{APPURL}}dashboard'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `conflicting`, `upload_id`, `item_id`, `unique_id`, `client_id`, `firstname`, `lastname`, `othername`, `name`, `academic_year`, `academic_term`, `enrollment_date`, `gender`, `email`, `username`, `password`, `access_level`, `preferences`, `status`, `deleted`, `verified_email`, `last_login`, `phone_number`, `phone_number_2`, `description`, `position`, `address`, `online`, `chat_status`, `last_seen`, `nation_ids`, `date_of_birth`, `class_id`, `course_ids`, `class_ids`, `blood_group`, `religion`, `section`, `programme`, `department`, `nationality`, `occupation`, `postal_code`, `disabled`, `residence`, `employer`, `guardian_id`, `last_timetable_id`, `last_password_change`, `country`, `verify_token`, `verified_date`, `token_expiry`, `changed_password`, `account_balance`, `city`, `relationship`, `date_created`, `last_updated`, `created_by`, `image`, `previous_school`, `previous_school_qualification`, `previous_school_remarks`, `user_status`, `perma_image`, `user_type`, `last_visited_page`) VALUES
(1, NULL, NULL, 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'MSISU000001', 'MSGH000001', 'Emmanuel', 'Obeng', 'Hyde', 'Emmanuel Obeng Hyde', NULL, NULL, NULL, 'Male', 'emmallob14@gmail.com', 'emmallob14', '$2y$10$OJq3npPhTIONczuyEz1wdeQvfGiK5yU6AUjE0.MzK8aIgITnL2a/K', 6, '{\"payments\":{},\"default_payment\":\"mobile_money\",\"theme_color\":\"sidebar-light\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"list_count\":\"200\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-09-18\",\"idb_next_init\":\"2020-09-21\"},\"sidebar_nav\":\"sidebar-opened\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\"}}', '1', '0', 'N', '2021-08-10 08:20:01', '233550107770', NULL, '&lt;div&gt;&lt;!--block--&gt;This is a short description about myself.&lt;/div&gt;', 'Developer', 'PMB 2582, Accra Main Post Office', '1', NULL, '2021-08-10 08:20:26', NULL, '1992-03-22', NULL, '[]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'Dodowa', NULL, 'NULL', 'th4qpA2Wcy6eMJslnoixXSgEUrzPK8I1', NULL, 84, '7iHJXMS3aWFUVkP6A814T5vBLRdZlYNwoQcejKzCuxygG92bEOrIhq', '2021-07-22 21:20:27', NULL, '1', '0', NULL, NULL, '2021-07-22 21:20:27', '2021-07-22 21:45:36', NULL, 'assets/img/user.png', NULL, NULL, NULL, 'Active', 'assets/img/user.png', 'admin', '{{APPURL}}dashboard'),
(2, NULL, NULL, 'KDtbYhedUAgTC8sG1caxV6LfEklMjvFn', 'ST000012021', 'MSGH000001', 'Sani', 'Abdul Jabal', NULL, 'Sani Abdul Jabal ', '2020/2021', '1st', '2021-07-23', 'Male', 'sanijabal@gmail.com', 'sanijabal', '$2y$10$m4kYmlg3NGXSnspgEg7/h.ac1c6GPjDZtKj/HT6FhQCwEXKdt1bZC', 1, '{\"payments\":{},\"default_payment\":\"mobile_money\",\"theme_color\":\"sidebar-light\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"list_count\":\"200\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-09-18\",\"idb_next_init\":\"2020-09-21\"},\"sidebar_nav\":\"sidebar-opened\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\"}}', '1', '0', 'Y', NULL, NULL, NULL, 'A good student by all standard.', NULL, NULL, '0', NULL, '2021-07-23 11:09:45', NULL, '2000-02-23', '4', NULL, NULL, '', 'Muslim', 'null', NULL, NULL, NULL, NULL, NULL, '0', 'Dzen Ayoor', NULL, 'uUxhoywY8e6drCSF5nQMZT3sRck9EGmD', NULL, '2021-08-06 06:21:15', 84, NULL, '2021-07-23 11:23:26', NULL, '0', '0', NULL, NULL, '2021-07-23 11:09:45', NULL, NULL, 'assets/img/user.png', NULL, NULL, NULL, 'Active', 'assets/img/user.png', 'student', '{{APPURL}}dashboard'),
(3, NULL, NULL, 'uUxhoywY8e6drCSF5nQMZT3sRck9EGmD', 'GL000012021', 'MSGH000001', 'Solomon', 'Jabal', '', 'Solomon  Jabal', NULL, NULL, NULL, 'Male', 'solomonabdul@gmail.com', 'solomonabdul@gmail.com', '$2y$10$8gGxAOJMwFC55glaDKprJucyNCG.k8ejU9iQlswy9Nkl2Cq7rt0lu', 6, '{\"payments\":{},\"default_payment\":\"mobile_money\",\"theme_color\":\"sidebar-light\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"list_count\":\"200\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-09-18\",\"idb_next_init\":\"2020-09-21\"},\"sidebar_nav\":\"sidebar-opened\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\"}}', '1', '0', 'Y', NULL, '0249090930', NULL, NULL, NULL, '', '0', NULL, '2021-07-23 11:09:45', NULL, NULL, NULL, '[]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'Akuapim Mountains', NULL, 'NULL', NULL, NULL, 84, NULL, '2021-07-23 12:54:55', NULL, '0', '0', NULL, 'Parent', '2021-07-23 11:09:45', '2021-08-07 19:11:33', NULL, 'assets/img/user.png', NULL, NULL, NULL, 'Active', 'assets/img/user.png', 'parent', '{{APPURL}}dashboard'),
(4, NULL, NULL, 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn', 'ST000022021', 'MSGH000001', 'Fredrick', 'Asamoah', NULL, 'Frederick Asamoah', '2020/2021', '1st', '2021-07-23', 'Male', 'fredrickasamoah@gmail.com', 'fredrickasamoah', '$2y$10$OOnYVaigrpwQxo5ALAp2BOJxEuBLh/24M6fYTyCpK8PVBVDmsaROy', 1, '{\"payments\":{},\"default_payment\":\"mobile_money\",\"theme_color\":\"sidebar-light\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"list_count\":\"200\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-09-18\",\"idb_next_init\":\"2020-09-21\"},\"sidebar_nav\":\"sidebar-opened\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\"}}', '1', '0', 'Y', NULL, NULL, NULL, 'A good student by all standard.', NULL, NULL, '0', NULL, '2021-07-23 11:09:45', NULL, '2000-02-23', '4', NULL, NULL, '', 'Muslim', 'null', NULL, NULL, NULL, NULL, NULL, '0', 'Dzen Ayoor', NULL, '', NULL, NULL, 84, NULL, '2021-07-23 11:23:26', NULL, '0', '0', NULL, NULL, '2021-07-23 11:09:45', NULL, NULL, 'assets/img/user.png', NULL, NULL, NULL, 'Active', 'assets/img/user.png', 'student', '{{APPURL}}dashboard'),
(5, NULL, NULL, '1526BaoMyt8Inh3c0zugePkbKfsGHNmX', 'ST000032021', 'MSGH000001', 'Emmanuella', 'Darko', 'Sarfowaa', 'Emmanuella Darko Sarfowaa', '2020/2021', '1st', '2021-08-07', 'Female', NULL, 'ST000032021', '$2y$10$jZSNvC64Ep37tg/QRLBbXu4rtkbB1js5JATeVhFGYG8niHw/eEe0.', 1, '{\"payments\":{},\"default_payment\":\"mobile_money\",\"theme_color\":\"sidebar-light\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"list_count\":\"200\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-09-18\",\"idb_next_init\":\"2020-09-21\"},\"sidebar_nav\":\"sidebar-opened\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\"}}', '1', '0', 'N', '2021-08-07 18:09:42', '0247685521', NULL, NULL, NULL, NULL, '0', NULL, '2021-08-07 18:21:02', NULL, '2001-09-04', '4', NULL, NULL, NULL, 'Christian', 'null', NULL, NULL, NULL, NULL, NULL, '0', 'Blue Kiosk', NULL, 'NULL', 'th4qpA2Wcy6eMJslnoixXSgEUrzPK8I1', '2021-08-07 17:22:19', 84, 'iECnGVyw56racRgqZfXxu9Fr0SlgtMdoWb0K37RtO8PZs4DfJYplL1WNOjICmbvzJkxqX4U', '2021-08-07 17:14:33', '1628378073', '0', '0', NULL, NULL, '2021-08-07 17:14:33', NULL, NULL, 'assets/img/user.png', NULL, NULL, NULL, 'Active', 'assets/img/user.png', 'student', '{{APPURL}}dashboard'),
(6, NULL, NULL, '3wczSvKX16d92VYiIuBGaZ5r4fCmnFpe', 'ST000042021', 'MSGH000001', 'Priscilla', 'Boadi', 'Akosua Ampomaa', 'Priscilla Boadi Akosua Ampomaa', '2020/2021', '1st', '2021-08-07', 'Female', NULL, 'ST000042021', '$2y$10$eEUUl.cuGQOM389x2p.nkeOU2qQ.WANlvMZlN2Yvd.ezgQVBw5mce', 1, '{\"payments\":{},\"default_payment\":\"mobile_money\",\"theme_color\":\"sidebar-light\",\"sidebar\":\"sidebar-opened\",\"font-size\":\"12px\",\"list_count\":\"200\",\"idb_init\":{\"init\":0,\"idb_last_init\":\"2020-09-18\",\"idb_next_init\":\"2020-09-21\"},\"sidebar_nav\":\"sidebar-opened\",\"quick_links\":{\"chat\":\"on\",\"calendar\":\"on\"}}', '1', '0', 'N', NULL, '0240500390', NULL, NULL, NULL, NULL, '0', NULL, '2021-08-07 19:02:24', NULL, '2000-08-10', '2', '[]', NULL, NULL, 'Christian', 'null', NULL, NULL, NULL, NULL, NULL, '0', 'Akuapim, East Region', NULL, 'uUxhoywY8e6drCSF5nQMZT3sRck9EGmD', NULL, NULL, 84, 'txCZjrA5BlIRRHuZYqcDOw029vAMzP3Nz9rDKg0y7nhdQUNkQfjGOUF4dambKseoa57J', '2021-08-07 19:02:24', '1628384544', '0', '0', NULL, NULL, '2021-08-07 19:02:24', '2021-08-07 19:13:48', NULL, 'assets/img/user.png', 'Prince of Peace International School', NULL, 'She was a good student and paid attention in class', 'Active', 'assets/img/user.png', 'student', '{{APPURL}}dashboard');

-- --------------------------------------------------------

--
-- Table structure for table `users_access_attempt`
--

DROP TABLE IF EXISTS `users_access_attempt`;
CREATE TABLE `users_access_attempt` (
  `id` int NOT NULL,
  `ipaddress` varchar(50) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `username_found` enum('0','1') DEFAULT '0',
  `attempt_type` enum('login','reset') DEFAULT 'login',
  `attempts` int DEFAULT '0',
  `lastattempt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users_access_attempt`
--

INSERT INTO `users_access_attempt` (`id`, `ipaddress`, `username`, `username_found`, `attempt_type`, `attempts`, `lastattempt`) VALUES
(1, '127.0.0.1', 'fredrickasamoah@gmail.com', '1', 'reset', 1, '2021-08-03 05:39:55'),
(2, '::1', 'ST000032021', '1', 'login', 0, '2021-08-07 17:23:32'),
(3, '127.0.0.1', 'ST000032021', '1', 'login', 0, '2021-08-07 18:09:42');

-- --------------------------------------------------------

--
-- Table structure for table `users_activity_logs`
--

DROP TABLE IF EXISTS `users_activity_logs`;
CREATE TABLE `users_activity_logs` (
  `id` int UNSIGNED NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `item_id` varchar(72) DEFAULT NULL,
  `user_id` varchar(32) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `previous_record` text,
  `date_recorded` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_agent` varchar(255) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `status` enum('0','1') DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users_activity_logs`
--

INSERT INTO `users_activity_logs` (`id`, `client_id`, `item_id`, `user_id`, `subject`, `source`, `previous_record`, `date_recorded`, `user_agent`, `description`, `status`) VALUES
(1, 'MSGH000001', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'verify_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-22 21:20:28', 'Linux | Chrome | ::1', 'Morning Star International School created a new Account pending Verification.', '1'),
(2, 'MSGH000001', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'admin_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-22 21:44:14', 'Linux | Chrome | ::1', 'Name was changed from ', '1'),
(3, 'MSGH000001', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'admin_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-22 21:44:14', 'Linux | Chrome | ::1', 'Date of Birth has been changed to 1992-03-22', '1'),
(4, 'MSGH000001', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'admin_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-22 21:44:14', 'Linux | Chrome | ::1', 'User description was altered.', '1'),
(5, 'MSGH000001', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'admin_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-22 21:44:14', 'Linux | Chrome | ::1', 'Position has been altered.  => Developer', '1'),
(6, 'MSGH000001', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'admin_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-22 21:44:14', 'Linux | Chrome | ::1', 'You updated your account information', '1'),
(7, 'MSGH000001', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'admin_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '<div><!--block-->This is a short description about myself.</div>', '2021-07-22 21:45:36', 'Linux | Chrome | ::1', 'User description was altered.', '1'),
(8, 'MSGH000001', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'admin_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-22 21:45:36', 'Linux | Chrome | ::1', 'You updated your account information', '1'),
(9, 'MSGH000001', 'MSGH000001', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"1\",\"client_id\":\"MSGH000001\",\"client_name\":\"Morning Star International School\",\"client_contact\":\"233550107770\",\"client_secondary_contact\":\"233247685521\",\"client_address\":\"PMB 2582, Accra Main Post Office\",\"client_email\":\"emmallob14@gmail.com\",\"client_website\":null,\"client_logo\":null,\"client_location\":null,\"client_category\":null,\"client_preferences\":{\"labels\":{\"staff\":\"MSISU\",\"student\":\"MSIS\",\"parent\":\"MSISP\",\"receipt\":\"RMSIS\"},\"academics\":{\"academic_year\":\"2021\\/2020\",\"academic_term\":\"\",\"term_starts\":\"\",\"term_ends\":\"\",\"next_academic_year\":\"\",\"next_academic_term\":\"\"},\"account\":{\"type\":\"basic\",\"activation_code\":\"7iHJXMS3aWFUVkP6A814T5vBLRdZlYNwoQcejKzCuxygG92bEOrIhq\",\"date_created\":\"2021-07-22 09:20PM\",\"expiry\":\"2021-08-22 09:20PM\"},\"opening_days\":[\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\",\"Friday\"]},\"client_status\":\"1\",\"client_state\":\"Pending\",\"client_account\":null,\"setup\":\"School\",\"sms_sender\":null,\"ip_address\":\"::1\",\"date_created\":\"2021-07-22 21:20:27\",\"grading_system\":null,\"grading_structure\":null,\"show_position\":null,\"show_teacher_name\":null,\"allow_submission\":null,\"academic_year_logs\":[]}', '2021-07-22 21:52:25', 'Linux | Chrome | ::1', ' updated the Account Information', '1'),
(10, 'MSGH000001', 'MSGH000001', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"1\",\"client_id\":\"MSGH000001\",\"client_name\":\"Morning Star International School\",\"client_contact\":\"233550107770\",\"client_secondary_contact\":\"233247685521\",\"client_address\":\"PMB 2582, Accra Main Post Office\",\"client_email\":\"emmallob14@gmail.com\",\"client_website\":\"https:\\/\\/www.morningstarschool.com\",\"client_logo\":null,\"client_location\":\"Cantonments, Accra\",\"client_category\":null,\"client_preferences\":{\"academics\":{\"academic_year\":\"2020\\/2021\",\"academic_term\":\"1st\",\"term_starts\":\"2021-07-11\",\"term_ends\":\"2021-09-30\",\"next_academic_year\":\"2020\\/2021\",\"next_academic_term\":\"2nd\",\"next_term_starts\":\"2021-10-18\",\"next_term_ends\":\"2021-12-22\"},\"labels\":{\"student_label\":\"st\",\"parent_label\":\"gl\",\"teacher_label\":\"tl\",\"staff_label\":\"sl\",\"course_label\":\"cl\",\"book_label\":\"bkl\",\"class_label\":\"cl\",\"department_label\":\"dl\",\"section_label\":\"sl\",\"receipt_label\":\"rl\",\"currency\":\"GHS\",\"print_receipt\":\"1\"},\"opening_days\":[\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\",\"Friday\"],\"account\":{\"type\":\"basic\",\"activation_code\":\"7iHJXMS3aWFUVkP6A814T5vBLRdZlYNwoQcejKzCuxygG92bEOrIhq\",\"date_created\":\"2021-07-22 09:20PM\",\"expiry\":\"2021-08-22 09:20PM\"}},\"client_status\":\"1\",\"client_state\":\"Pending\",\"client_account\":null,\"setup\":\"School\",\"sms_sender\":null,\"ip_address\":\"::1\",\"date_created\":\"2021-07-22 21:20:27\",\"grading_system\":null,\"grading_structure\":null,\"show_position\":null,\"show_teacher_name\":null,\"allow_submission\":null,\"academic_year_logs\":[]}', '2021-07-22 21:56:43', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde updated the Account Information', '1'),
(11, 'MSGH000001', '', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-23 10:43:31', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> added a new endpoint: <strong>assignments/prepare_assessment</strong> to the resource: <strong>assignments</strong>.', '1'),
(12, 'MSGH000001', 'dxhh4ov3wac5fwiem0zlgk8s2z9typng', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"219\",\"item_id\":\"dxhh4ov3wac5fwiem0zlgk8s2z9typng\",\"version\":\"v1\",\"resource\":\"assignments\",\"endpoint\":\"assignments\\/prepare_assessment\",\"method\":\"GET\",\"description\":\"This endpoint is meant for preparing the data to save.\",\"parameter\":\"{\\\"assessment_title\\\":\\\"required - The title of the assessment\\\",\\\"assessment_type\\\":\\\"required - The assignment type to save.\\\",\\\"class_id\\\":\\\"required - The unique class id for the log.\\\",\\\"course_id\\\":\\\"required - The unique course\\/subject id to save the record\\\",\\\"date_due\\\":\\\"required - The date on which the assignment was given\\\",\\\"time_due\\\":\\\"The time for the submission of the assignment\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-07-23 10:43:31\",\"last_updated\":\"2021-07-23 10:43:31\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"HjwXoJQNresMBFznDW7qAGaTCOY0c6kL\",\"updated_by\":null}', '2021-07-23 10:44:06', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> updated the endpoint.', '1'),
(13, 'MSGH000001', 'dxhh4ov3wac5fwiem0zlgk8s2z9typng', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"219\",\"item_id\":\"dxhh4ov3wac5fwiem0zlgk8s2z9typng\",\"version\":\"v1\",\"resource\":\"assignments\",\"endpoint\":\"assignments\\/prepare_assessment\",\"method\":\"POST\",\"description\":\"This endpoint is meant for preparing the data to save.\",\"parameter\":\"{\\\"assessment_title\\\":\\\"required - The title of the assessment\\\",\\\"assessment_type\\\":\\\"required - The assignment type to save.\\\",\\\"class_id\\\":\\\"required - The unique class id for the log.\\\",\\\"course_id\\\":\\\"required - The unique course\\/subject id to save the record\\\",\\\"date_due\\\":\\\"required - The date on which the assignment was given\\\",\\\"time_due\\\":\\\"The time for the submission of the assignment\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-07-23 10:43:31\",\"last_updated\":\"2021-07-23 10:44:06\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"HjwXoJQNresMBFznDW7qAGaTCOY0c6kL\",\"updated_by\":\"HjwXoJQNresMBFznDW7qAGaTCOY0c6kL\"}', '2021-07-23 10:44:12', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> updated the endpoint.', '1'),
(14, 'MSGH000001', 'dxhh4ov3wac5fwiem0zlgk8s2z9typng', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"219\",\"item_id\":\"dxhh4ov3wac5fwiem0zlgk8s2z9typng\",\"version\":\"v1\",\"resource\":\"assignments\",\"endpoint\":\"assignments\\/prepare_assessment\",\"method\":\"GET\",\"description\":\"This endpoint is meant for preparing the data to save.\",\"parameter\":\"{\\\"assessment_title\\\":\\\"required - The title of the assessment\\\",\\\"assessment_type\\\":\\\"required - The assignment type to save.\\\",\\\"class_id\\\":\\\"required - The unique class id for the log.\\\",\\\"course_id\\\":\\\"required - The unique course\\/subject id to save the record\\\",\\\"date_due\\\":\\\"required - The date on which the assignment was given\\\",\\\"time_due\\\":\\\"The time for the submission of the assignment\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-07-23 10:43:31\",\"last_updated\":\"2021-07-23 10:44:12\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"HjwXoJQNresMBFznDW7qAGaTCOY0c6kL\",\"updated_by\":\"HjwXoJQNresMBFznDW7qAGaTCOY0c6kL\"}', '2021-07-23 10:54:30', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> updated the endpoint.', '1'),
(15, 'MSGH000001', 'YoGuMxE8vS5d1iz3Aa42WKUbjNqBygmD', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'classes', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-23 10:57:10', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde created a new Class: Class 6', '1'),
(16, 'MSGH000001', 'iHYaK8jk0FRsGorylLNf4h7pTgbWXnB9', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'classes', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-23 10:57:48', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde created a new Class: JHS 1', '1'),
(17, 'MSGH000001', 'AMz7oODFB6NnPidcj9uZtIEaV1x0sG83', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'classes', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-23 10:58:39', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde created a new Class: JHS 2', '1'),
(18, 'MSGH000001', 'DKm7eAGouxcHIfiw52OljBX6zk3W19pT', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'classes', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-23 10:58:51', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde created a new Class: JHS 3', '1'),
(19, 'MSGH000001', '3', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'classes', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"3\",\"upload_id\":null,\"item_id\":\"AMz7oODFB6NnPidcj9uZtIEaV1x0sG83\",\"client_id\":\"MSGH000001\",\"name\":\"JHS 2\",\"slug\":\"jhs-2\",\"class_code\":\"CL03\",\"class_size\":null,\"courses_list\":null,\"rooms_list\":\"[]\",\"weekly_meeting\":null,\"department_id\":null,\"academic_year\":\"2020\\/2021\",\"academic_term\":\"1st\",\"class_teacher\":null,\"class_assistant\":null,\"status\":\"1\",\"created_by\":\"HjwXoJQNresMBFznDW7qAGaTCOY0c6kL\",\"description\":null,\"date_created\":\"2021-07-23 10:58:39\",\"date_updated\":\"2021-07-23 10:58:39\"}', '2021-07-23 10:59:04', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde updated the Class: JHS 2', '1'),
(20, 'MSGH000001', 'pQ9vbrynG43wHDKuz760mdMCsFoZJVXA', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'courses', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-23 10:59:58', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde created a new Course: Integrated Science', '1'),
(21, 'MSGH000001', 'JBKXbQYAU4hMimldGuEZNVpr6H3fL1y7', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'courses', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-23 11:00:58', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde created a new Course: Information, Communication & Technologo', '1'),
(22, 'MSGH000001', 'GJmTXfDihU6LW8OBaEQFzqnjANKvuwbl', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'courses', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-23 11:01:27', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde created a new Course: Religious & Moral Education', '1'),
(23, 'MSGH000001', 'FxJjgQ7bB14Khr869iGCpTSNEwMfynZu', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'courses', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-23 11:01:58', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde created a new Course: Basic, Design and Technology', '1'),
(24, 'MSGH000001', 'hdqeCQ73NDkcFvPBrJa8OX4TERpxiVuM', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'courses', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-23 11:03:13', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde created a new Course: Social Studies', '1'),
(25, 'MSGH000001', 'jPNrKhLWRAp72Q1dsk06bfacXvOCF3UE', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'courses', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-23 11:05:34', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde created a new Course: Mathematics', '1'),
(26, 'MSGH000001', 'KDtbYhedUAgTC8sG1caxV6LfEklMjvFn', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'account-verify', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-23 11:09:45', 'Linux | Chrome | ::1', 'Sani Abdul Jabal  - verify account by clicking on the link sent to the provided email address.', '1'),
(27, 'MSGH000001', 'KDtbYhedUAgTC8sG1caxV6LfEklMjvFn', 'KDtbYhedUAgTC8sG1caxV6LfEklMjvFn', 'verify_account', 'Account was manually activated using the Activation link.', NULL, '2021-07-23 11:23:26', 'Linux | Firefox | 127.0.0.1', 'sanijabal\'s - account was successfully activated.', '1'),
(30, 'MSGH000001', 'uUxhoywY8e6drCSF5nQMZT3sRck9EGmD', 'uUxhoywY8e6drCSF5nQMZT3sRck9EGmD', 'verify_account', 'Account was manually activated using the Activation link.', NULL, '2021-07-23 12:54:55', 'Linux | Firefox | 127.0.0.1', 'solomonabdul\'s - account was successfully activated.', '1'),
(31, NULL, 'uUxhoywY8e6drCSF5nQMZT3sRck9EGmD', 'uUxhoywY8e6drCSF5nQMZT3sRck9EGmD', 'password-recovery', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-23 12:56:15', 'Linux | Firefox | 127.0.0.1', 'You successfully changed your password.', '1'),
(32, 'MSGH000001', 'uUxhoywY8e6drCSF5nQMZT3sRck9EGmD', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'guardian_ward', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-23 13:00:01', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde appended <strong>Sani Abdul Jabal </strong> as a ward to <strong>Solomon Jabal Abdul</strong>.', '1'),
(33, 'MSGH000001', 'dxhh4ov3wac5fwiem0zlgk8s2z9typng', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"219\",\"item_id\":\"dxhh4ov3wac5fwiem0zlgk8s2z9typng\",\"version\":\"v1\",\"resource\":\"assignments\",\"endpoint\":\"assignments\\/prepare_assessment\",\"method\":\"POST\",\"description\":\"This endpoint is meant for preparing the data to save.\",\"parameter\":\"{\\\"assessment_title\\\":\\\"required - The title of the assessment\\\",\\\"assessment_type\\\":\\\"required - The assignment type to save.\\\",\\\"class_id\\\":\\\"required - The unique class id for the log.\\\",\\\"course_id\\\":\\\"required - The unique course\\/subject id to save the record\\\",\\\"date_due\\\":\\\"required - The date on which the assignment was given\\\",\\\"time_due\\\":\\\"The time for the submission of the assignment\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-07-23 10:43:31\",\"last_updated\":\"2021-07-23 10:54:30\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"HjwXoJQNresMBFznDW7qAGaTCOY0c6kL\",\"updated_by\":\"HjwXoJQNresMBFznDW7qAGaTCOY0c6kL\"}', '2021-07-23 13:05:13', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> updated the endpoint.', '1'),
(34, 'MSGH000001', 'dxhh4ov3wac5fwiem0zlgk8s2z9typng', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"219\",\"item_id\":\"dxhh4ov3wac5fwiem0zlgk8s2z9typng\",\"version\":\"v1\",\"resource\":\"assignments\",\"endpoint\":\"assignments\\/prepare_assessment\",\"method\":\"POST\",\"description\":\"This endpoint is meant for preparing the data to save.\",\"parameter\":\"{\\\"assessment_title\\\":\\\"required - The title of the assessment\\\",\\\"assessment_type\\\":\\\"required - The assignment type to save.\\\",\\\"class_id\\\":\\\"required - The unique class id for the log.\\\",\\\"course_id\\\":\\\"required - The unique course\\/subject id to save the record\\\",\\\"date_due\\\":\\\"required - The date on which the assignment was given\\\",\\\"time_due\\\":\\\"The time for the submission of the assignment\\\",\\\"request\\\":\\\"This is the request parsed\\\",\\\"awarded_marks\\\":\\\"This is an array of awarded marks\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-07-23 10:43:31\",\"last_updated\":\"2021-07-23 13:05:13\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"HjwXoJQNresMBFznDW7qAGaTCOY0c6kL\",\"updated_by\":\"HjwXoJQNresMBFznDW7qAGaTCOY0c6kL\"}', '2021-07-23 13:05:27', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> updated the endpoint.', '1'),
(35, 'MSGH000001', 'dxhh4ov3wac5fwiem0zlgk8s2z9typng', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"219\",\"item_id\":\"dxhh4ov3wac5fwiem0zlgk8s2z9typng\",\"version\":\"v1\",\"resource\":\"assignments\",\"endpoint\":\"assignments\\/log_assessment\",\"method\":\"POST\",\"description\":\"This endpoint is meant for preparing the data to save.\",\"parameter\":\"{\\\"assessment_title\\\":\\\"required - The title of the assessment\\\",\\\"assessment_type\\\":\\\"required - The assignment type to save.\\\",\\\"class_id\\\":\\\"required - The unique class id for the log.\\\",\\\"course_id\\\":\\\"required - The unique course\\/subject id to save the record\\\",\\\"date_due\\\":\\\"required - The date on which the assignment was given\\\",\\\"time_due\\\":\\\"The time for the submission of the assignment\\\",\\\"request\\\":\\\"This is the request parsed\\\",\\\"awarded_marks\\\":\\\"This is an array of awarded marks\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-07-23 10:43:31\",\"last_updated\":\"2021-07-23 13:05:27\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"HjwXoJQNresMBFznDW7qAGaTCOY0c6kL\",\"updated_by\":\"HjwXoJQNresMBFznDW7qAGaTCOY0c6kL\"}', '2021-07-23 14:06:54', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> updated the endpoint.', '1'),
(36, 'MSGH000001', '', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-24 17:19:19', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> added a new endpoint: <strong>assignments/save_assessment</strong> to the resource: <strong>assignments</strong>.', '1'),
(37, 'MSGH000001', 'dxhh4ov3wac5fwiem0zlgk8s2z9typng', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"219\",\"item_id\":\"dxhh4ov3wac5fwiem0zlgk8s2z9typng\",\"version\":\"v1\",\"resource\":\"assignments\",\"endpoint\":\"assignments\\/log_assessment\",\"method\":\"POST\",\"description\":\"This endpoint is meant for preparing the data to save.\",\"parameter\":\"{\\\"assessment_title\\\":\\\"required - The title of the assessment\\\",\\\"assessment_type\\\":\\\"required - The assignment type to save.\\\",\\\"class_id\\\":\\\"required - The unique class id for the log.\\\",\\\"course_id\\\":\\\"required - The unique course\\/subject id to save the record\\\",\\\"date_due\\\":\\\"required - The date on which the assignment was given\\\",\\\"time_due\\\":\\\"The time for the submission of the assignment\\\",\\\"request\\\":\\\"This is the request parsed\\\",\\\"awarded_marks\\\":\\\"This is an array of awarded marks\\\",\\\"overall_score\\\":\\\"required - The total marks for the assignment\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-07-23 10:43:31\",\"last_updated\":\"2021-07-23 14:06:54\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"HjwXoJQNresMBFznDW7qAGaTCOY0c6kL\",\"updated_by\":\"HjwXoJQNresMBFznDW7qAGaTCOY0c6kL\"}', '2021-07-26 08:27:49', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> updated the endpoint.', '1'),
(38, 'MSGH000001', 'uqPftbpeQUFRWm5NhCX9cHYA2a8gLKzn_KDtbYhedUAgTC8sG1caxV6LfEklMjvFn', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'assignments', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-26 17:41:26', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde graded the student: 26', '1'),
(39, 'MSGH000001', 'uqPftbpeQUFRWm5NhCX9cHYA2a8gLKzn_aaabYhedUAgTC8sG1caxV6LfEklMjvFn', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'assignments', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-26 17:41:26', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde graded the student: 28', '1'),
(40, 'MSGH000001', 'uqPftbpeQUFRWm5NhCX9cHYA2a8gLKzn', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'assignment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-26 17:41:32', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde closed the assignment thus prohibiting grading.', '1'),
(41, 'MSGH000001', 'nFCjT14PyzISdho', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'accounts', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 11:03:09', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde added a new account type head', '1'),
(42, 'MSGH000001', 'nqe1tB4agLxm0NI', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'accounts', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 11:04:02', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde added a new account type head', '1'),
(43, 'MSGH000001', '1', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'fees_category', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 11:05:04', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> added a new category with name: Tuition Fees', '1'),
(44, 'MSGH000001', '2', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'fees_category', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 11:05:23', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> added a new category with name: Project Fees', '1'),
(45, 'MSGH000001', '1', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'fees_category', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 11:05:34', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> updated the category: Tuition Fees', '1'),
(46, 'MSGH000001', '3', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'fees_category', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 11:06:10', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> added a new category with name: Library Dues', '1'),
(47, 'MSGH000001', '4', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'fees_category', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 11:07:43', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> added a new category with name: ICT', '1'),
(48, 'MSGH000001', '5', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'fees_category', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 11:08:11', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> added a new category with name: Sports & Recreational', '1'),
(49, 'MSGH000001', '6', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'fees_category', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 11:08:28', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> added a new category with name: Examination Fees', '1'),
(50, 'MSGH000001', '4', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'fees_allocation', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 11:08:47', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde added the fee allocation for <strong>Tuition Fees</strong> of: <strong>GHS 750</strong>', '1'),
(51, 'MSGH000001', '4', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'fees_allocation', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 11:08:52', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde added the fee allocation for <strong>Project Fees</strong> of: <strong>GHS 100</strong>', '1'),
(52, 'MSGH000001', '4', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'fees_allocation', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 11:08:55', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde added the fee allocation for <strong>Library Dues</strong> of: <strong>GHS 150</strong>', '1'),
(53, 'MSGH000001', '4', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'fees_allocation', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 11:08:59', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde added the fee allocation for <strong>ICT</strong> of: <strong>GHS 100</strong>', '1'),
(54, 'MSGH000001', '4', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'fees_allocation', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 11:09:03', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde added the fee allocation for <strong>Sports & Recreational</strong> of: <strong>GHS 200</strong>', '1'),
(55, 'MSGH000001', '4', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'fees_allocation', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 11:09:06', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde added the fee allocation for <strong>Examination Fees</strong> of: <strong>GHS 100</strong>', '1'),
(56, 'MSGH000001', 'HNYqlXMGadstjxB90F6nJTi1EpDKuVwm', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 11:09:53', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of <strong>500</strong> as Payment for <strong>Tuition Fees</strong> from <strong>Sani Abdul Jabal</strong>. Outstanding Balance is <strong>250</strong>', '1'),
(57, 'MSGH000001', 'HNYqlXMGadstjxB90F6nJTi1EpDKuVwm', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 11:10:27', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of <strong>250.00</strong> as Payment for <strong>Tuition Fees</strong> from <strong>Sani Abdul Jabal </strong>. Outstanding Balance is <strong>0</strong>', '1'),
(58, 'MSGH000001', 'qYoEBVs1NgapQiIrDFckXn7R3ZLmzwGP', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 11:10:27', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of <strong>100.00</strong> as Payment for <strong>Project Fees</strong> from <strong>Sani Abdul Jabal </strong>. Outstanding Balance is <strong>0</strong>', '1'),
(59, 'MSGH000001', 'WG6vdRk9zQE14Vfus5nNq8KO7iby3gxo', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 11:10:27', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of <strong>150.00</strong> as Payment for <strong>Library Dues</strong> from <strong>Sani Abdul Jabal </strong>. Outstanding Balance is <strong>0</strong>', '1'),
(60, 'MSGH000001', 'xTlyF7HOcSQLg8DjnPAIervsXwR1Bh9E', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 11:19:47', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of <strong>750.00</strong> as Payment for <strong>Tuition Fees</strong> from <strong>Frederick Asamoah</strong>. Outstanding Balance is <strong>0</strong>', '1'),
(61, 'MSGH000001', 'UcYJZBCGPRloQjSMht2a5ifzdWkbunrX', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 11:19:47', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of <strong>100.00</strong> as Payment for <strong>Project Fees</strong> from <strong>Frederick Asamoah</strong>. Outstanding Balance is <strong>0</strong>', '1'),
(62, 'MSGH000001', 'VFUTfh5goj2H4ZYEvCKBiAOxtn13LpeX', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 11:19:47', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of <strong>150.00</strong> as Payment for <strong>Library Dues</strong> from <strong>Frederick Asamoah</strong>. Outstanding Balance is <strong>0</strong>', '1'),
(63, 'MSGH000001', '6k0VXQBgv3WUpFjtmr1n9DdlcY4uGz5i', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 11:37:09', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of <strong>100.00</strong> as Payment for <strong>ICT</strong> from <strong>Frederick Asamoah</strong>. Outstanding Balance is <strong>0</strong>', '1'),
(64, 'MSGH000001', 'KirUmoZGBVkbsCtSuYWw143HxRhe6d9T', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 11:37:09', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of <strong>200.00</strong> as Payment for <strong>Sports & Recreational</strong> from <strong>Frederick Asamoah</strong>. Outstanding Balance is <strong>0</strong>', '1'),
(65, 'MSGH000001', '4gbea5wzMU2FlTKRdCy6fA8W0YmoGEpX', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 11:37:09', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of <strong>100.00</strong> as Payment for <strong>Examination Fees</strong> from <strong>Frederick Asamoah</strong>. Outstanding Balance is <strong>0</strong>', '1'),
(66, 'MSGH000001', 'Tugdh9CsXmH8DElWRjUJyOYNtarqIfQK', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 21:08:14', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of <strong>100.00</strong> as Payment for <strong>ICT</strong> from <strong>Sani Abdul Jabal </strong>. Outstanding Balance is <strong>0</strong>', '1'),
(67, 'MSGH000001', 'fCiDKFdU5mJuaAgSZ9xEtoyPkcWeT8NB', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 21:08:14', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of <strong>200.00</strong> as Payment for <strong>Sports & Recreational</strong> from <strong>Sani Abdul Jabal </strong>. Outstanding Balance is <strong>0</strong>', '1'),
(68, 'MSGH000001', 'nH3eOt6NklwVBFaLoXKmDup8jcyqWxA4', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'fees_payment', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 21:08:14', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde received an amount of <strong>100.00</strong> as Payment for <strong>Examination Fees</strong> from <strong>Sani Abdul Jabal </strong>. Outstanding Balance is <strong>0</strong>', '1'),
(69, 'MSGH000001', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'bank_details', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 21:11:15', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> inserted the Bank Details of: <strong>Emmanuel Obeng Hyde</strong>', '1'),
(70, 'MSGH000001', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'salary_allowances', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '\r\n                <p class=\'mb-0 pb-0\'><strong>Basic Salary:</strong> 0.00 => 2000</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Total Allowances:</strong> 0.00 => 0</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Gross Salary:</strong> 0.00 => 2000</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Total Deductions:</strong> 0.00 => 0</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Net Allowances:</strong> 0.00 => 0</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Net Salary:</strong> 0.00 => 2000</p>', '2021-07-30 21:11:26', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> updated the Salary Allowances of: <strong>Emmanuel Obeng Hyde</strong>', '1'),
(71, 'MSGH000001', '1', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 21:12:15', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> added a new Allowance record under the payroll section', '1'),
(72, 'MSGH000001', '2', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 21:12:29', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> added a new Allowance record under the payroll section', '1'),
(73, 'MSGH000001', '3', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 21:12:40', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> added a new Deduction record under the payroll section', '1'),
(74, 'MSGH000001', '4', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 21:12:50', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> added a new Deduction record under the payroll section', '1'),
(75, 'MSGH000001', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'salary_allowances', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '\r\n                <p class=\'mb-0 pb-0\'><strong>Basic Salary:</strong> 2000.00 => 2000.00</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Total Allowances:</strong> 0.00 => 320</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Gross Salary:</strong> 2000.00 => 2320</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Total Deductions:</strong> 0.00 => 435</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Net Allowances:</strong> 0.00 => -115</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Net Salary:</strong> 2000.00 => 1885</p>', '2021-07-30 21:13:26', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> updated the Salary Allowances of: <strong>Emmanuel Obeng Hyde</strong>', '1'),
(76, 'MSGH000001', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 21:19:04', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> generated a payslip for: <strong>Emmanuel Obeng Hyde</strong> for the month: <strong>July 2021</strong>', '1'),
(77, 'MSGH000001', 'wBX46syTzIv0CRVGOpa21refdgLt8huq', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 21:19:10', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> validated the payslip: <strong>Emmanuel Obeng Hyde</strong> for the month: <strong>July 2021</strong>', '1'),
(78, 'MSGH000001', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 21:20:20', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> generated a payslip for: <strong>Emmanuel Obeng Hyde</strong> for the month: <strong>June 2021</strong>', '1'),
(79, 'MSGH000001', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 21:20:32', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> generated a payslip for: <strong>Emmanuel Obeng Hyde</strong> for the month: <strong>May 2021</strong>', '1'),
(80, 'MSGH000001', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 21:20:43', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> generated a payslip for: <strong>Emmanuel Obeng Hyde</strong> for the month: <strong>April 2021</strong>', '1'),
(81, 'MSGH000001', 'WQByR0GwYIOZjNU3n4DreVmt6FSXqgkd', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 21:20:47', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> validated the payslip: <strong>Emmanuel Obeng Hyde</strong> for the month: <strong>April 2021</strong>', '1'),
(82, 'MSGH000001', 'qsTZe6kB2UbgNcv0pDwuVWhJj7OiMI9d', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 21:20:50', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> validated the payslip: <strong>Emmanuel Obeng Hyde</strong> for the month: <strong>May 2021</strong>', '1'),
(83, 'MSGH000001', 'tvX70QgkfziTreOhHBqdsWy8uIFSGUYV', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 21:20:52', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> validated the payslip: <strong>Emmanuel Obeng Hyde</strong> for the month: <strong>June 2021</strong>', '1'),
(84, 'MSGH000001', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 21:30:13', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> generated a payslip for: <strong>Emmanuel Obeng Hyde</strong> for the month: <strong>March 2021</strong>', '1'),
(85, 'MSGH000001', 'fkcA4ghlJUQHv0YV7EsdoDnCzLBep19w', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-30 21:30:16', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> validated the payslip: <strong>Emmanuel Obeng Hyde</strong> for the month: <strong>March 2021</strong>', '1'),
(86, 'MSGH000001', 'Y3DMseEwgqaTnov', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'accounts_transaction', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-31 09:14:33', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> validated the transaction.', '1'),
(87, 'MSGH000001', 'RL00001', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'accounts_transaction', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-31 09:14:37', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> validated the transaction.', '1'),
(88, 'MSGH000001', 'f4hYVUz2jAJqK0C', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'accounts_transaction', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-31 09:14:41', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> validated the transaction.', '1'),
(89, 'MSGH000001', 'Nzg7Bo0h2CYm8rt', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'accounts_transaction', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-31 09:14:45', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> validated the transaction.', '1'),
(90, 'MSGH000001', 'cFE8LdrTgAxoZIS', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'accounts_typehead', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-31 09:25:53', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde added a new account type head', '1'),
(91, 'MSGH000001', 'O5dMNfr3u8Stzph', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'accounts_typehead', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-31 09:26:02', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde added a new account type head', '1'),
(92, 'MSGH000001', 'jBmQV1zGN8A3e6M', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'accounts_transaction', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-31 09:30:14', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde added a new deposit', '1'),
(93, 'MSGH000001', 'JbxwjX3B7ec5n89', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'accounts_transaction', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-31 09:31:22', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde added a new deposit', '1'),
(94, 'MSGH000001', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-31 15:43:50', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> generated a payslip for: <strong>Emmanuel Obeng Hyde</strong> for the month: <strong>January 2021</strong>', '1'),
(95, 'MSGH000001', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '\r\n                <p class=\'mb-0 pb-0\'><strong>Basic Salary:</strong> 2000.00 => 2000</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Total Allowances:</strong> 320.00 => 320</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Gross Salary:</strong> 2320.00 => 2320</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Total Deductions:</strong> 435.00 => 435</p>\r\n                <p class=\'mb-0 pb-0\'><strong>Net Salary:</strong> 1885.00 => 1885</p>', '2021-07-31 15:44:44', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> updated the payslip for: <strong>Emmanuel Obeng Hyde</strong> for the month: <strong>January 2021</strong>', '1'),
(96, 'MSGH000001', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-31 15:45:16', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> generated a payslip for: <strong>Emmanuel Obeng Hyde</strong> for the month: <strong>January 2021</strong>', '1'),
(97, 'MSGH000001', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-31 15:46:27', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> generated a payslip for: <strong>Emmanuel Obeng Hyde</strong> for the month: <strong>January 2021</strong>', '1'),
(98, 'MSGH000001', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-31 15:52:14', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> generated a payslip for: <strong>Emmanuel Obeng Hyde</strong> for the month: <strong>January 2021</strong>', '1'),
(103, 'MSGH000001', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-31 15:55:32', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> generated a payslip for: <strong>Emmanuel Obeng Hyde</strong> for the month: <strong>January 2021</strong>', '1'),
(104, 'MSGH000001', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-31 15:57:06', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> generated a payslip for: <strong>Emmanuel Obeng Hyde</strong> for the month: <strong>February 2021</strong>', '1'),
(105, 'MSGH000001', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-31 15:59:08', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> generated a payslip for: <strong>Emmanuel Obeng Hyde</strong> for the month: <strong>March 2021</strong>', '1'),
(106, 'MSGH000001', 'o0W58OxEnIVylw9qb1DdgeA4CN2sFPML', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-31 16:09:05', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> validated the payslip: <strong>Emmanuel Obeng Hyde</strong> for the month: <strong>February 2021</strong>', '1'),
(107, 'MSGH000001', 'hfPom0C5EkWX7vMa3w1dsZyVKJpQ9c6j', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-31 16:09:08', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> validated the payslip: <strong>Emmanuel Obeng Hyde</strong> for the month: <strong>March 2021</strong>', '1'),
(108, 'MSGH000001', 'Nk1HRvQEy7faUI9tAOTBiSLnJrhuMYb8', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'payslip', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-07-31 16:09:11', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> validated the payslip: <strong>Emmanuel Obeng Hyde</strong> for the month: <strong>January 2021</strong>', '1'),
(109, 'MSGH000001', 'vupvk8ffjdwit3ysduqrsqjbxhn02goe', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"101\",\"item_id\":\"vupvk8ffjdwit3ysduqrsqjbxhn02goe\",\"version\":\"v1\",\"resource\":\"assignments\",\"endpoint\":\"assignments\\/list\",\"method\":\"GET\",\"description\":\"\",\"parameter\":\"{\\\"limit\\\":\\\"The number of rows to limit the result\\\",\\\"assignment_id\\\":\\\"The unique id of the assignment to laod the data\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-12-21 08:26:54\",\"last_updated\":\"2020-12-21 08:26:54\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":null}', '2021-07-31 16:27:59', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> updated the endpoint.', '1'),
(110, 'MSGH000001', 'vupvk8ffjdwit3ysduqrsqjbxhn02goe', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"101\",\"item_id\":\"vupvk8ffjdwit3ysduqrsqjbxhn02goe\",\"version\":\"v1\",\"resource\":\"assignments\",\"endpoint\":\"assignments\\/list\",\"method\":\"GET\",\"description\":\"\",\"parameter\":\"{\\\"limit\\\":\\\"The number of rows to limit the result\\\",\\\"assignment_id\\\":\\\"The unique id of the assignment to laod the data\\\",\\\"show_marks\\\":\\\"When parsed, the list will include the marks obtained by each student.\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-12-21 08:26:54\",\"last_updated\":\"2021-07-31 16:27:59\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"HjwXoJQNresMBFznDW7qAGaTCOY0c6kL\"}', '2021-07-31 16:28:15', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> updated the endpoint.', '1');
INSERT INTO `users_activity_logs` (`id`, `client_id`, `item_id`, `user_id`, `subject`, `source`, `previous_record`, `date_recorded`, `user_agent`, `description`, `status`) VALUES
(111, 'MSGH000001', 'vupvk8ffjdwit3ysduqrsqjbxhn02goe', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"101\",\"item_id\":\"vupvk8ffjdwit3ysduqrsqjbxhn02goe\",\"version\":\"v1\",\"resource\":\"assignments\",\"endpoint\":\"assignments\\/list\",\"method\":\"GET\",\"description\":\"\",\"parameter\":\"{\\\"limit\\\":\\\"The number of rows to limit the result\\\",\\\"assignment_id\\\":\\\"The unique id of the assignment to laod the data\\\",\\\"show_marks\\\":\\\"When parsed, the list will include the marks obtained by each student.\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-12-21 08:26:54\",\"last_updated\":\"2021-07-31 16:28:15\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"uIkajsw123456789064hxk1fc3efmnva\",\"updated_by\":\"HjwXoJQNresMBFznDW7qAGaTCOY0c6kL\"}', '2021-07-31 16:29:13', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> updated the endpoint.', '1'),
(112, NULL, 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn', 'password-recovery', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-08-03 05:39:55', 'Linux | Firefox | 127.0.0.1', 'Frederick Asamoah requested for a password reset code.', '1'),
(113, 'MSGH000001', '', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-08-03 05:53:41', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> added a new endpoint: <strong>auth/password_manager</strong> to the resource: <strong>auth</strong>.', '1'),
(114, 'MSGH000001', '', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-08-04 08:07:35', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> added a new endpoint: <strong>users/quick_search</strong> to the resource: <strong>users</strong>.', '1'),
(115, 'MSGH000001', '', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-08-07 12:03:10', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> added a new endpoint: <strong>support/list</strong> to the resource: <strong>support</strong>.', '1'),
(116, 'MSGH000001', '', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-08-07 12:31:23', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> added a new endpoint: <strong>support/create</strong> to the resource: <strong>support</strong>.', '1'),
(117, 'MSGH000001', '', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-08-07 12:31:59', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> added a new endpoint: <strong>support/reply</strong> to the resource: <strong>support</strong>.', '1'),
(118, 'MSGH000001', '', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-08-07 12:51:29', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> added a new endpoint: <strong>support/close</strong> to the resource: <strong>support</strong>.', '1'),
(119, 'MSGH000001', '1526BaoMyt8Inh3c0zugePkbKfsGHNmX', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'account-verify', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-08-07 17:14:33', 'Linux | Chrome | ::1', 'Emmanuella Darko Sarfowaa - verify account by clicking on the link sent to the provided email address.', '1'),
(120, 'MSGH000001', 'g1s0ypnf6ywmsineoxcruivtl4w9auqe', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"15\",\"item_id\":\"g1s0ypnf6ywmsineoxcruivtl4w9auqe\",\"version\":\"v1\",\"resource\":\"users\",\"endpoint\":\"users\\/add\",\"method\":\"POST\",\"description\":\"Add a new user account\",\"parameter\":\"{\\\"firstname\\\":\\\"required - The firstname of the user\\\",\\\"client_id\\\":\\\"This is a Unique of the user that is been created.\\\",\\\"lastname\\\":\\\"required - The lastname of the user\\\",\\\"othername\\\":\\\"The othernames of the user\\\",\\\"gender\\\":\\\"The gender of the user\\\",\\\"date_of_birth\\\":\\\"The date of birth\\\",\\\"email\\\":\\\"The email address of the user\\\",\\\"phone\\\":\\\"Contact number of the user\\\",\\\"phone_2\\\":\\\"Secondary contact number\\\",\\\"address\\\":\\\"The address of the user\\\",\\\"residence\\\":\\\"The place of residence\\\",\\\"nationality\\\":\\\"The nationality of the user\\\",\\\"country\\\":\\\"The country id of the user\\\",\\\"description\\\":\\\"Any additional information of the user\\\",\\\"user_id\\\":\\\"The id of the user\\\",\\\"employer\\\":\\\"The name of the user employer\\\",\\\"occupation\\\":\\\"The occupation of the user\\\",\\\"position\\\":\\\"The position of the user\\\",\\\"access_level\\\":\\\"The access permission id of the user.\\\",\\\"department_id\\\":\\\"The department of the user\\\",\\\"unique_id\\\":\\\"The unique id of the user\\\",\\\"section\\\":\\\"The section of the user\\\",\\\"class_id\\\":\\\"The class id of the user\\\",\\\"blood_group\\\":\\\"The blood group of the user\\\",\\\"guardian_info\\\":\\\"An array of the guardian information\\\",\\\"enrollment_date\\\":\\\"The date on which the user was enrolled\\\",\\\"user_type\\\":\\\"required - The type of the user to add\\\",\\\"image\\\":\\\"Image of the user\\\",\\\"academic_year\\\":\\\"The academic year on which the student was enrolled\\\",\\\"academic_term\\\":\\\"The term within which the student was enrolled\\\",\\\"status\\\":\\\"The status of the user\\\",\\\"username\\\":\\\"The username of the user for login purposes.\\\",\\\"previous_school\\\":\\\"This is applicable for students only\\\",\\\"previous_school_qualification\\\":\\\"Applicable for students only\\\",\\\"previous_school_remarks\\\":\\\"Any remarks supplied by previous school from which student is coming from\\\",\\\"religion\\\":\\\"The religion of the user\\\",\\\"relationship\\\":\\\"The relationship of the guardian to the student\\\",\\\"courses_ids\\\":\\\"This is the course id and is applicable to teachers only\\\",\\\"status\\\":\\\"The state of the current user to be set.\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2020-09-19 07:17:49\",\"last_updated\":\"2021-01-22 08:44:53\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"tgxuwdwkdjr58mg64hxk1fc3efmnvata\",\"updated_by\":\"uIkajsw123456789064hxk1fc3efmnva\"}', '2021-08-07 18:52:59', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> updated the endpoint.', '1'),
(121, 'MSGH000001', '3wczSvKX16d92VYiIuBGaZ5r4fCmnFpe', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'account-verify', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-08-07 19:02:24', 'Linux | Chrome | ::1', 'Priscilla Boadi Akosua Ampomaa - verify account by clicking on the link sent to the provided email address.', '1'),
(122, 'MSGH000001', '3wczSvKX16d92VYiIuBGaZ5r4fCmnFpe', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'student_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-08-07 19:08:37', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> updated the account information of <strong>Priscilla Boadi Akosua Ampomaa</strong>', '1'),
(123, 'MSGH000001', 'uUxhoywY8e6drCSF5nQMZT3sRck9EGmD', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'parent_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', 'Solomon Abdul Jabal', '2021-08-07 19:11:28', 'Linux | Chrome | ::1', 'Name was changed from Solomon Abdul Jabal', '1'),
(124, 'MSGH000001', 'uUxhoywY8e6drCSF5nQMZT3sRck9EGmD', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'parent_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-08-07 19:11:28', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> updated the account information of <strong>Solomon Abdul Jabal</strong>', '1'),
(125, 'MSGH000001', 'uUxhoywY8e6drCSF5nQMZT3sRck9EGmD', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'parent_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-08-07 19:11:33', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> updated the account information of <strong>Solomon Jabal </strong>', '1'),
(126, 'MSGH000001', '3wczSvKX16d92VYiIuBGaZ5r4fCmnFpe', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'student_account', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-08-07 19:13:48', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> updated the account information of <strong>Priscilla Boadi Akosua Ampomaa</strong>', '1'),
(127, 'MSGH000001', 'uUxhoywY8e6drCSF5nQMZT3sRck9EGmD', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'guardian_ward', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-08-07 19:18:42', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde appended <strong>Frederick Asamoah</strong> as a ward to <strong>Solomon  Jabal</strong>.', '1'),
(128, 'MSGH000001', 'uUxhoywY8e6drCSF5nQMZT3sRck9EGmD', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'guardian_ward', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-08-07 19:18:52', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde removed <strong>Frederick Asamoah</strong> as the ward of <strong>Solomon  Jabal</strong>.', '1'),
(129, 'MSGH000001', 'di7bp5awvu0trmrgqbmnoze1gccxuqsy', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"224\",\"item_id\":\"di7bp5awvu0trmrgqbmnoze1gccxuqsy\",\"version\":\"v1\",\"resource\":\"support\",\"endpoint\":\"support\\/create\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"department\\\":\\\"required - The department to handle the issue\\\",\\\"subject\\\":\\\"This is the subject of the message\\\",\\\"content\\\":\\\"required - This is the content of the message\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-08-07 12:31:23\",\"last_updated\":\"2021-08-07 12:31:23\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"HjwXoJQNresMBFznDW7qAGaTCOY0c6kL\",\"updated_by\":null}', '2021-08-08 12:30:23', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> updated the endpoint.', '1'),
(130, 'MSGH000001', '', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-08-08 13:02:55', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> added a new endpoint: <strong>support/reopen</strong> to the resource: <strong>support</strong>.', '1'),
(131, 'MSGH000001', 'fci7keuat4xogsfo2ictlrh6bpqqvr0g', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"225\",\"item_id\":\"fci7keuat4xogsfo2ictlrh6bpqqvr0g\",\"version\":\"v1\",\"resource\":\"support\",\"endpoint\":\"support\\/reply\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"ticket_id\\\":\\\"required - The unique id of the ticket to reply\\\",\\\"content\\\":\\\"required - This is the content of the message\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-08-07 12:31:59\",\"last_updated\":\"2021-08-07 12:31:59\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"HjwXoJQNresMBFznDW7qAGaTCOY0c6kL\",\"updated_by\":null}', '2021-08-08 13:28:36', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> updated the endpoint.', '1'),
(132, 'MSGH000001', 'fci7keuat4xogsfo2ictlrh6bpqqvr0g', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'endpoints', 'MySchoolGH Calculation<br>Property changed by an update from another property.', '{\"id\":\"225\",\"item_id\":\"fci7keuat4xogsfo2ictlrh6bpqqvr0g\",\"version\":\"v1\",\"resource\":\"support\",\"endpoint\":\"support\\/reply\",\"method\":\"POST\",\"description\":\"\",\"parameter\":\"{\\\"ticket_id\\\":\\\"required - The unique id of the ticket to reply\\\",\\\"content\\\":\\\"required - This is the content of the message\\\",\\\"section\\\":\\\"This is the section of item to reply to\\\"}\",\"status\":\"active\",\"counter\":\"0\",\"date_created\":\"2021-08-07 12:31:59\",\"last_updated\":\"2021-08-08 13:28:35\",\"deleted\":\"0\",\"deprecated\":\"0\",\"added_by\":\"HjwXoJQNresMBFznDW7qAGaTCOY0c6kL\",\"updated_by\":\"HjwXoJQNresMBFznDW7qAGaTCOY0c6kL\"}', '2021-08-08 13:34:41', 'Linux | Chrome | ::1', '<strong>Emmanuel Obeng Hyde</strong> updated the endpoint.', '1'),
(133, 'MSGH000001', '1', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'courses_plan', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-08-09 06:20:55', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde created a new Course Unit: Introduction to Computing', '1'),
(134, 'MSGH000001', '2', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'courses_plan', 'MySchoolGH Calculation<br>Property changed by an update from another property.', NULL, '2021-08-09 06:28:04', 'Linux | Chrome | ::1', 'Emmanuel Obeng Hyde created a new Course Unit: Introduction', '1');

-- --------------------------------------------------------

--
-- Table structure for table `users_api_endpoints`
--

DROP TABLE IF EXISTS `users_api_endpoints`;
CREATE TABLE `users_api_endpoints` (
  `id` int UNSIGNED NOT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `version` varchar(32) NOT NULL DEFAULT 'v1',
  `resource` varchar(64) DEFAULT NULL,
  `endpoint` varchar(255) DEFAULT NULL,
  `method` enum('GET','POST','PUT','DELETE') DEFAULT 'GET',
  `description` varchar(255) DEFAULT NULL,
  `parameter` text,
  `status` enum('overloaded','active','dormant','inactive') NOT NULL DEFAULT 'active',
  `counter` int UNSIGNED NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  `deprecated` enum('0','1') NOT NULL DEFAULT '0',
  `added_by` varchar(32) DEFAULT NULL,
  `updated_by` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(15, 'g1s0ypnf6ywmsineoxcruivtl4w9auqe', 'v1', 'users', 'users/add', 'POST', 'Add a new user account', '{\"firstname\":\"required - The firstname of the user\",\"client_id\":\"This is a Unique of the user that is been created.\",\"lastname\":\"required - The lastname of the user\",\"othername\":\"The othernames of the user\",\"gender\":\"The gender of the user\",\"date_of_birth\":\"The date of birth\",\"email\":\"The email address of the user\",\"phone\":\"Contact number of the user\",\"phone_2\":\"Secondary contact number\",\"address\":\"The address of the user\",\"residence\":\"The place of residence\",\"nationality\":\"The nationality of the user\",\"country\":\"The country id of the user\",\"description\":\"Any additional information of the user\",\"user_id\":\"The id of the user\",\"employer\":\"The name of the user employer\",\"occupation\":\"The occupation of the user\",\"position\":\"The position of the user\",\"access_level\":\"The access permission id of the user.\",\"department_id\":\"The department of the user\",\"unique_id\":\"The unique id of the user\",\"section\":\"The section of the user\",\"class_id\":\"The class id of the user\",\"blood_group\":\"The blood group of the user\",\"guardian_info\":\"An array of the guardian information\",\"guardian_id\":\"The unique id of the selected guardian\",\"enrollment_date\":\"The date on which the user was enrolled\",\"user_type\":\"required - The type of the user to add\",\"image\":\"Image of the user\",\"academic_year\":\"The academic year on which the student was enrolled\",\"academic_term\":\"The term within which the student was enrolled\",\"status\":\"The status of the user\",\"username\":\"The username of the user for login purposes.\",\"previous_school\":\"This is applicable for students only\",\"previous_school_qualification\":\"Applicable for students only\",\"previous_school_remarks\":\"Any remarks supplied by previous school from which student is coming from\",\"religion\":\"The religion of the user\",\"relationship\":\"The relationship of the guardian to the student\",\"courses_ids\":\"This is the course id and is applicable to teachers only\",\"status\":\"The state of the current user to be set.\"}', 'active', 0, '2020-09-19 07:17:49', '2021-08-07 18:52:59', '0', '0', 'tgxuwdwkdjr58mg64hxk1fc3efmnvata', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL'),
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
(94, 'jyv34gkf0obuxqzhykricz12wvptmwmo', 'v1', 'attendance', 'attendance/display_attendance', 'GET', '', '{\"class_id\":\"This loads the class attendance for the specified date range.\",\"date_range\":\"This is the date range to load the attendance log.\",\"user_type\":\"The type of users to search for\",\"no_list\":\"This indicates whether to display the users details list or not.\"}', 'active', 0, '2020-12-16 06:02:55', '2021-06-04 22:28:11', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(95, 'zbdo1unr04lrhlpdfyqkwhg9vymwnzxe', 'v1', 'users', 'users/guardian_update', 'POST', '', '{\"guardian_id\":\"required - The unique id of the user to update\",\"gender\":\"The gender of the user\",\"image\":\"The display picture of the guardian\",\"fullname\":\"The fullname of the guardian\",\"date_of_birth\":\"The date of birth of the guardian\",\"email\":\"The email address\",\"contact\":\"The primary contact of the user\",\"contact_2\":\"The secondary contact of the user \",\"address\":\"The postal address\",\"residence\":\"The place of residence\",\"country\":\"The country of the user\",\"employer\":\"The name of the employer (company name)\",\"occupation\":\"The profession of the user\",\"description\":\"Any additional information of the user\",\"blood_group\":\"The blood group of the guardian\"}', 'active', 0, '2020-12-17 09:49:34', '2020-12-29 21:56:10', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(96, 'fhfotj03yx9prjo6cndilzebq2scp8w5', 'v1', 'users', 'users/guardian_add', 'POST', '', '{\"guardian_id\":\"required - The unique id of the user to update\",\"gender\":\"The gender of the user\",\"image\":\"The display picture of the guardian\",\"fullname\":\"required - The fullname of the guardian\",\"date_of_birth\":\"The date of birth of the guardian\",\"email\":\"The email address\",\"contact\":\"required - The primary contact of the user\",\"contact_2\":\"The secondary contact of the user \",\"address\":\"The postal address\",\"residence\":\"The place of residence\",\"country\":\"The country of the user\",\"employer\":\"The name of the employer (company name)\",\"occupation\":\"The profession of the user\",\"description\":\"Any additional information of the user\",\"blood_group\":\"The blood group of the guardian\"}', 'active', 0, '2020-12-17 09:50:16', '2020-12-29 21:56:04', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(97, 'ecykfrdjb4gvzqyg2milv0ntofacui5e', 'v1', 'users', 'users/modify_guardianward', 'POST', '', '{\"user_id\":\"required - The unique id for the guardian and the ward\",\"todo\":\"required - The activity to perform (append, remove).\"}', 'active', 0, '2020-12-18 07:00:45', '2020-12-18 07:00:45', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(98, 'ydhcdkmtco4onq02isbu1ylztisr8jpv', 'v1', 'users', 'users/save_permission', 'POST', '', '{\"access_level\":\"required - An array string containing the access permissions of the user\",\"user_id\":\"required - The user id to update the access permission.\"}', 'active', 0, '2020-12-18 18:53:15', '2020-12-18 18:53:15', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(99, '7lhlrvuwmhj4jtbmsgqdfsapiquxcgfb', 'v1', 'assignments', 'assignments/load_course_students', 'GET', 'This endpoint in assignments loads both the course and students list using the class id as a filter.', '{\"class_id\":\"required - The class id to filter the results list.\"}', 'active', 0, '2020-12-21 06:58:32', '2020-12-21 06:58:32', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(100, 'cnl54rkmwfpovdqqojdzwh3rauig7enz', 'v1', 'assignments', 'assignments/add', 'POST', '', '{\"assignment_type\":\"required - The type of assignment type to upload (multiple_choice or file_attachment)\",\"assignment_title\":\"required - The title of the assignment\",\"description\":\"Any additional instructions added to the assignment\",\"grade\":\"required - The grade for this assignment\",\"date_due\":\"required - The date on which the assignment is due.\",\"time_due\":\"The time for submission\",\"assigned_to\":\"required - This determines whether to assign the assignment to all students in the class or to specific students\",\"assigned_to_list\":\"This is needed when you decide to assign the assignment to specific students.\",\"class_id\":\"required - The id of the class to assign the assignment\",\"course_id\":\"required - The unique id of the course to link this assignment.\"}', 'active', 0, '2020-12-21 07:53:09', '2020-12-23 08:13:32', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe'),
(101, 'vupvk8ffjdwit3ysduqrsqjbxhn02goe', 'v1', 'assignments', 'assignments/list', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"assignment_id\":\"The unique id of the assignment to laod the data\",\"show_marks\":\"When parsed, the list will include the marks obtained by each student.\"}', 'active', 0, '2020-12-21 08:26:54', '2021-07-31 16:29:13', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL'),
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
(122, 'lwzcv1xwjnshkpipxtqr3mhlkfn7bg4d', 'v1', 'library', 'library/add_book', 'POST', '', '{\"title\":\"required - The title of the book\",\"isbn\":\"required - The unique identification code for the book\",\"author\":\"required - The author of the book\",\"rack_no\":\"The rack on which the book could be located\",\"row_no\":\"The row on the rack number to locate the book\",\"quantity\":\"required - The quantity of the books available in stock\",\"department_id\":\"The unique id of the department\",\"class_id\":\"The unique id of the class\",\"category_id\":\"required - The category under which this book falls\",\"description\":\"The summary description of the book\",\"code\":\"The unique code the item\",\"book_image\":\"The cover image for the book\"}', 'active', 0, '2021-01-02 12:53:28', '2021-07-11 15:11:13', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(123, 'yggtpdlj6m4feqczo1jkavsm83wihu95', 'v1', 'library', 'library/update_book', 'POST', '', '{\"title\":\"required - The title of the book\",\"isbn\":\"required - The unique identification code for the book\",\"author\":\"required - The author of the book\",\"rack_no\":\"The rack on which the book could be located\",\"row_no\":\"The row on the rack number to locate the book\",\"quantity\":\"The quantity of the books available in stock\",\"department_id\":\"The unique id of the department\",\"class_id\":\"The unique id of the class\",\"category_id\":\"required - The category under which this book falls\",\"description\":\"The summary description of the book\",\"book_id\":\"required - The unique id of the book to update\",\"code\":\"The unique code the item\",\"book_image\":\"The cover image for the book\"}', 'active', 0, '2021-01-02 12:54:08', '2021-01-21 08:53:13', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva');
INSERT INTO `users_api_endpoints` (`id`, `item_id`, `version`, `resource`, `endpoint`, `method`, `description`, `parameter`, `status`, `counter`, `date_created`, `last_updated`, `deleted`, `deprecated`, `added_by`, `updated_by`) VALUES
(124, 'mfxzhx0vt3oo5jnbtekv8waefzrusdwl', 'v1', 'library', 'library/list', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"department_id\":\"The unique id of the department\",\"class_id\":\"The unique id of the class\",\"category_id\":\"The category under which this book falls\",\"description\":\"The summary description of the book\",\"book_id\":\"The unique id of the book to update\",\"isbn\":\"The unique identification code for the book\",\"show_in_list\":\"This is applicable if the user wants to ascertain whether the book has been added in a session to be issued out or requested.\",\"minified\":\"If parsed then the result will be simplified\"}', 'active', 0, '2021-01-02 12:55:28', '2021-01-04 20:09:35', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(125, 'jwn17zfsaecz43ih96ouqyfiela2hv5k', 'v1', 'library', 'library/upload_resource', 'POST', '', '{\"book_id\":\"required - The book id to upload the files to\"}', 'active', 0, '2021-01-02 21:24:24', '2021-01-02 21:24:24', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(126, 'duts60wylhukvkdfimwpbtcp9lhoszya', 'v1', 'library', 'library/update_category', 'POST', '', '{\"name\":\"required - The title of the category\",\"department_id\":\"The department of the book category\",\"description\":\"The description of the category\",\"category_id\":\"required - The unique id of the category to update.\"}', 'active', 0, '2021-01-03 22:46:04', '2021-01-03 22:46:04', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(127, 'eagq8tdpnik0yvrjvobwscbuz3z4r9d7', 'v1', 'library', 'library/add_category', 'POST', '', '{\"name\":\"required - The title of the category\",\"department_id\":\"The department of the book category\",\"description\":\"The description of the category\"}', 'active', 0, '2021-01-03 22:46:37', '2021-01-03 22:46:37', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(128, 'sggcjcpqyzuphbuvw9ijdwxq7e5forto', 'v1', 'library', 'library/issue_request_handler', 'POST', '', '{\"label\":\"required - An array that contains the request to perform. Parameters: todo - add, remove, request and issue / book_id - Required if the todo is either add or remove.\"}', 'active', 0, '2021-01-04 21:05:19', '2021-01-04 21:18:57', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(129, 'jcz6d2qh85bfkpewit3zqcw1nab7m0jy', 'v1', 'library', 'library/issued_request_list', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"borrowed_id\":\"The unique id of the borrowed id\",\"user_id\":\"The unique id of the user who requested for the books\",\"return_date\":\"Filter by the date on which books are to be returned\",\"issued_date\":\"Filter by the date on which the books were issued\",\"status\":\"Filter by the status of the request\",\"show_list\":\"This when appended while show the details of the book borrowed\"}', 'active', 0, '2021-01-06 08:16:35', '2021-01-06 08:17:49', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(130, 'aeqtpxgzy5ho3v8cldtsiprb47zcu0fq', 'v1', 'fees', 'fees/list', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"student_id\":\"The unique id of the student to load the record\",\"class_id\":\"The unique id of the class to load the record\",\"academic_year\":\"The academic year to load the information\",\"academic_term\":\"The academic term to load the information\"}', 'active', 0, '2021-01-08 07:43:05', '2021-01-08 07:43:05', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(131, 'fcgwydlvdkss1tivtx2uro9pbma5zqyk', 'v1', 'fees', 'fees/payment_form', 'GET', '', '{\"department_id\":\"This is the unique id of the department\",\"class_id\":\"This is the unique id of the class of the student\",\"student_id\":\"The unique id of the student\",\"category_id\":\"The fees category type to load\",\"show_history\":\"When submitted in the query, the result will contain the payment history of the student (if supplied)\"}', 'active', 0, '2021-01-08 11:43:35', '2021-01-08 11:43:35', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', NULL),
(132, 'd6kwitjdx81rnocfabuefoqywtrvsb59', 'v1', 'fees', 'fees/allocate_fees', 'POST', '', '{\"allocate_to\":\"required - This specifies whether to allot the fees to the class or student\",\"amount\":\"required - This is the amount.\",\"category_id\":\"required - This is the category id of the fees type\",\"student_id\":\"This is only needed if the allocate_to is equal to student.\",\"class_id\":\"This is required for insertion. If not specified, the said fees will be allotted to all active classes in the database.\"}', 'active', 0, '2021-01-08 16:19:16', '2021-01-08 16:20:11', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(133, '5gnulblaojzrmyke1chz3yivchsxp6gu', 'v1', 'fees', 'fees/allocate_fees_amount', 'GET', 'Get the fees allotted a class or student', '{\"allocate_to\":\"required - This specifies whether to allot the fees to the class or student\",\"category_id\":\"required - This is the category id of the fees type\",\"student_id\":\"This is only needed if the allocate_to is equal to student.\",\"class_id\":\"This is required for insertion. If not specified, the said fees will be allotted to all active classes in the database.\"}', 'active', 0, '2021-01-08 21:12:22', '2021-01-08 21:25:46', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'uIkajsw123456789064hxk1fc3efmnva'),
(134, 'blgc8rfdehuqmcq6iiohygn0trv7uevt', 'v1', 'fees', 'fees/make_payment', 'POST', '', '{\"checkout_url\":\"required - This is the checkout url for making payments\",\"payment_method\":\"required - The mode for making the payment\",\"amount\":\"required - This is the amount to be made.\",\"description\":\"The description for the payment (optional)\",\"bank_id\":\"The unique id of the bank if a cheque is used to make payment\",\"cheque_number\":\"The unique number of the cheque if payment is being made using a cheque.\",\"cheque_security\":\"The security code on the cheque.\",\"student_id\":\"The student id to receive payment.\",\"email_address\":\"The email address of the payee\",\"contact_number\":\"The contact number of the payee.\",\"category_id\":\"The category id for the payment\"}', 'active', 0, '2021-01-09 19:22:02', '2021-06-12 14:35:21', '0', '0', 'uIkajsw123456789064hxk1fc3efmnva', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
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
(179, 'k6fr3exchm5rniq24fcysvyzidazolkb', 'v1', 'fees', 'fees/search', 'GET', '', '{\"term\":\"required - This is the search term to lookup for.\"}', 'active', 0, '2021-05-08 19:03:28', '2021-05-08 19:09:56', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(180, 'criggde6xxfby5stuz0stio84jmklap9', 'v1', 'promotion', 'promotion/log', 'GET', '', '{\"history_id\":\"The unique id of the history log\",\"student_id\":\"The unique id of the student\",\"promote_from\":\"The class to promote the student from\",\"promote_to\":\"The class to promote the student to.\",\"academic_year\":\"The academic year that the promotion applies\",\"academic_term\":\"The academic term that the promotion applies.\"}', 'active', 0, '2021-05-12 21:29:49', '2021-05-13 18:28:11', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(181, 'g6zmpvqslurid7eeci5uxbhw0jtxyokj', 'v1', 'promotion', 'promotion/students', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"department_id\":\"The department from which to filter the students list\",\"class_id\":\"required - The class to load the students from\",\"gender\":\"For easy listing of students using the gender to load\"}', 'active', 0, '2021-05-13 09:49:35', '2021-05-13 12:04:11', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(182, 'tgquj7fpexwcrlxbywzh2szympn9onrk', 'v1', 'promotion', 'promotion/promote', 'POST', '', '{\"promote_from\":\"required - The class to promote the students from\",\"promote_to\":\"required - The class to promote the students to\",\"students_list\":\"The list of students id (This must be the unique id of the students) - Can be a comma separated string or an array.\"}', 'active', 0, '2021-05-13 13:46:52', '2021-05-13 13:46:52', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', NULL),
(183, 'ljquezyafhhsqreaw1ib9oyto08kx7xf', 'v1', 'promotion', 'promotion/history', 'GET', '', '{\"history_id\":\"The unique id of the history log\",\"promote_from\":\"The class to promote the student from\",\"promote_to\":\"The class to promote the student to.\",\"academic_year\":\"The academic year that the promotion applies\",\"academic_term\":\"The academic term that the promotion applies.\",\"append_log\":\"This when parsed will also include the students that were promoted.\"}', 'active', 0, '2021-05-13 18:24:34', '2021-05-13 18:26:04', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(184, 'fe5cboyi0yjhtj4ub68ztsoslxpaemgc', 'v1', 'promotion', 'promotion/validate', 'POST', '', '{\"history_id\":\"required - This is the unique id of the promotion history log to validate\"}', 'active', 0, '2021-05-13 21:21:43', '2021-05-13 21:21:43', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', NULL),
(185, '683lcxznuvuq0ctoae4ya7lpzbt5vk1x', 'v1', 'promotion', 'promotion/cancel', 'POST', '', '{\"history_id\":\"required - This is the unique id of the promotion history log to cancel\"}', 'active', 0, '2021-05-13 21:21:58', '2021-05-13 21:21:58', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', NULL),
(186, 'tilz2uhxiltd1y3e8rbv79mugajaxzko', 'v1', 'account', 'account/endacademicterm', 'POST', '', '', 'active', 0, '2021-05-19 05:44:12', '2021-05-19 05:44:12', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', NULL),
(187, 'mnx3croyjulnssv9vaefzqprbi8dpejh', 'v1', 'accounting', 'accounting/add_accounttype', 'POST', '', '{\"name\":\"required - This is the name of the account type\",\"account_type\":\"required - This is the account type (income or expense)\",\"description\":\"The full description of this account type head\"}', 'active', 0, '2021-05-29 16:54:05', '2021-05-31 05:57:57', '0', '0', 'EtHgJPGLnUw83X95xCAFcQOazMqIu1Zd', 'EtHgJPGLnUw83X95xCAFcQOazMqIu1Zd'),
(188, '17ytcaqlwlvnaf69xupcztwyxuzoshbd', 'v1', 'accounting', 'accounting/update_accounttype', 'POST', '', '{\"name\":\"required - This is the name of the account type\",\"account_type\":\"required - This is the account type (income or expense)\",\"description\":\"The full description of this account type head\",\"type_id\":\"This is the account type.\"}', 'active', 0, '2021-05-29 16:55:07', '2021-05-31 05:57:54', '0', '0', 'EtHgJPGLnUw83X95xCAFcQOazMqIu1Zd', 'EtHgJPGLnUw83X95xCAFcQOazMqIu1Zd'),
(189, 'ojdhxq98bvgtiovyt4eickdz6frlpbka', 'v1', 'accounting', 'accounting/add_deposit', 'POST', '', '{\"account_id\":\"required - The name of the account to make this deposit\",\"account_type\":\"required - The account head type\",\"reference\":\"The reference to this deposit\",\"amount\":\"required - The amount to be deposited\",\"date\":\"required - The date on which the deposit was made\",\"payment_medium\":\"The medium of payment\",\"description\":\"The full description of the deposit\"}', 'active', 0, '2021-05-29 18:26:39', '2021-06-02 16:58:53', '0', '0', 'EtHgJPGLnUw83X95xCAFcQOazMqIu1Zd', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(190, 'jhsgwpjbzudnmgztuhdl3rpq89cvkolm', 'v1', 'accounting', 'accounting/update_deposit', 'POST', '', '{\"account_id\":\"required - The name of the account to make this deposit\",\"account_type\":\"required - The account head type\",\"reference\":\"The reference to this deposit\",\"amount\":\"required - The amount to be deposited\",\"date\":\"required - The date on which the deposit was made\",\"payment_medium\":\"The medium of payment\",\"description\":\"The full description of the deposit\",\"transaction_id\":\"required - The unique id of the deposit made\"}', 'active', 0, '2021-05-29 18:27:12', '2021-06-02 16:59:45', '0', '0', 'EtHgJPGLnUw83X95xCAFcQOazMqIu1Zd', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(191, 'vz7rm0mejxzeysicwpafibj6argy5h9s', 'v1', 'accounting', 'accounting/add_expenditure', 'POST', '', '{\"account_id\":\"required - The name of the account to make this expenditure\",\"account_type\":\"required - The account head type\",\"reference\":\"The reference to this deposit\",\"amount\":\"required - The amount to be expended\",\"date\":\"required - The date on which the deposit was made\",\"payment_medium\":\"The medium of payment\",\"description\":\"The full description of the expenditure\"}', 'active', 0, '2021-05-29 18:30:12', '2021-06-02 16:59:05', '0', '0', 'EtHgJPGLnUw83X95xCAFcQOazMqIu1Zd', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(192, 'dv8zamg3jiyffa5pcqilyln2oe1jkpdt', 'v1', 'accounting', 'accounting/update_expenditure', 'POST', '', '{\"account_id\":\"required - The name of the account to make this expenditure\",\"account_type\":\"required - The account head type\",\"reference\":\"The reference to this deposit\",\"amount\":\"required - The amount to be expended\",\"date\":\"required - The date on which the deposit was made\",\"payment_medium\":\"The medium of payment\",\"description\":\"The full description of the expenditure\",\"transaction_id\":\"required - This is the unique id of the expenditure record\"}', 'active', 0, '2021-05-29 18:30:45', '2021-06-02 16:59:41', '0', '0', 'EtHgJPGLnUw83X95xCAFcQOazMqIu1Zd', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(193, 'dba8slklxjieov4n3uzirm6vywb5hz0o', 'v1', 'accounting', 'accounting/list_accounttype', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"type\":\"The type of head to list\",\"type_id\":\"The unique id of the account type head to list\",\"q\":\"Search by name\"}', 'active', 0, '2021-05-31 06:12:58', '2021-05-31 06:30:26', '0', '0', 'EtHgJPGLnUw83X95xCAFcQOazMqIu1Zd', 'EtHgJPGLnUw83X95xCAFcQOazMqIu1Zd'),
(194, '17jcys9gmnnaqye8hihdpjdx4ilfuvz5', 'v1', 'accounting', 'accounting/add_account', 'POST', '', '{\"account_name\":\"required - The name of the account\",\"account_number\":\"required - This is the account number\",\"description\":\"The full description of the account\",\"opening_balance\":\"The opening balance for this account.\",\"account_bank\":\"The name of the bank for this account\"}', 'active', 0, '2021-05-31 06:20:08', '2021-07-09 06:57:56', '0', '0', 'EtHgJPGLnUw83X95xCAFcQOazMqIu1Zd', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(195, 'zrgnpxabvxr3blolcueeiodwsva2j47n', 'v1', 'accounting', 'accounting/update_account', 'POST', '', '{\"account_name\":\"required - The name of the account\",\"account_number\":\"required - This is the account number\",\"description\":\"The full description of the account\",\"opening_balance\":\"The opening balance for this account.\",\"account_bank\":\"The name of the bank for this account\",\"account_id\":\"required - This is the unique id of the account record\"}', 'active', 0, '2021-05-31 06:21:12', '2021-07-09 06:58:02', '0', '0', 'EtHgJPGLnUw83X95xCAFcQOazMqIu1Zd', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(196, 'bsdxwyrptuvxw9ympq5r8ni7gzkaeq3l', 'v1', 'accounting', 'accounting/list_accounts', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"type\":\"The type of head to list\",\"account_id\":\"The unique id of the account type head to list\",\"q\":\"Search by name\"}', 'active', 0, '2021-05-31 06:31:32', '2021-05-31 06:31:32', '0', '0', 'EtHgJPGLnUw83X95xCAFcQOazMqIu1Zd', NULL),
(197, 'abxwbywsup4icrvd9d2aqnvnjr5izhgt', 'v1', 'accounting', 'accounting/list_transactions', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"item_type\":\"The type of transaction record to show (Expense/Deposit)\",\"account_id\":\"The unique id of the account type head to list\",\"q\":\"Search by name\",\"account_type\":\"Filter by the account type head.\",\"transaction_id\":\"This is the unique transaction id to load\",\"academic_year\":\"Filter the results by the academic year\",\"academic_term\":\"Filter the results by the academic term\",\"date_range\":\"This is a date range to load the record\",\"date\":\"This is the date for the record.\"}', 'active', 0, '2021-05-31 07:10:48', '2021-05-31 07:15:12', '0', '0', 'EtHgJPGLnUw83X95xCAFcQOazMqIu1Zd', 'EtHgJPGLnUw83X95xCAFcQOazMqIu1Zd'),
(198, 'cdpblsr2vucjhghb7mkpi9q5kjumd6e1', 'v1', 'attendance', 'attendance/attendance_report', 'GET', '', '{\"class_id\":\"The unique id of the class to load the details\",\"month_year\":\"required - The month and year to load the record\",\"user_type\":\"required - The type of students to load the record.\"}', 'active', 0, '2021-06-05 06:25:31', '2021-06-05 06:44:38', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(199, 'qwnims843gwvfry9ygun57cf2zteorjm', 'v1', 'communication', 'communication/add_template', 'POST', '', '{\"name\":\"required - This is the name of the template\",\"message\":\"required - This is the message for the template\",\"type\":\"required - This is the type of the template: sms/email\",\"module\":\"The module to use in sending the message\"}', 'active', 0, '2021-06-05 16:33:15', '2021-07-15 05:31:00', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(200, 'sxde6mefyr0m1nphiv7goro5a2utqvaq', 'v1', 'communication', 'communication/update_template', 'POST', '', '{\"name\":\"required - This is the name of the template\",\"message\":\"required - This is the message for the template\",\"module\":\"The module to use in sending the message\",\"type\":\"required - This is the type of the template: sms/email\",\"template_id\":\"required - This is the unique template id to be updated.\"}', 'active', 0, '2021-06-05 16:34:11', '2021-07-15 05:31:09', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(201, 'rtopy2oubgzvqemxqd8clbzgk1yis47u', 'v1', 'communication', 'communication/list_templates', 'GET', '', '{\"q\":\"The term to search for\",\"type\":\"The type of template to load\",\"template_id\":\"The template id to load\"}', 'active', 0, '2021-06-05 16:49:15', '2021-06-05 16:49:25', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(202, 'vdwocayu7bvzt9s38gifrelyr5wnsbcq', 'v1', 'communication', 'communication/send_smsemail', 'POST', '', '{\"campaign_name\":\"required - The name of the campaign\",\"template_id\":\"The unique id of the template to load the content\",\"message\":\"required - This is the message to send.\",\"recipient_type\":\"required - The receipient category of the message\",\"role_group\":\"The user role group\",\"role_id\":\"The id of the user role\",\"class_id\":\"The class id of the receipient\",\"send_later\":\"This indicates whether to send the message now or later\",\"schedule_date\":\"The date on which to send the message\",\"schedule_time\":\"This is the time to send the message\",\"type\":\"This is the type of message to send.\",\"recipients\":\"This is an array of the list of individual recipient of the message.\"}', 'active', 0, '2021-06-07 06:13:27', '2021-06-07 06:13:27', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', NULL),
(203, 'rtopy2oubgzvqemxqd8clbzgk1yis47u', 'v1', 'communication', 'communication/list_messages', 'GET', '', '{\"q\":\"The campaign nameto search for.\",\"type\":\"The type of template to load\",\"message_id\":\"The template id to load\",\"status\":\"The status of the message\"}', 'active', 0, '2021-06-05 16:49:15', '2021-06-05 16:49:25', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(204, 'eyvwnxkz6vbzt137realcgp9olysgdmi', 'v1', 'payment', 'payment/init', 'POST', '', '{\"email\":\"required - The email address of the customer.\" ,\"amount\":\"required - the amount to be paid.\"}', 'active', 0, '2021-06-12 07:55:21', '2021-06-12 09:24:44', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(205, 'dwtejdn8qlrgxekscnvchxbvoiymu7fz', 'v1', 'payment', 'payment/get', 'GET', '', '{\"route\":\"required - The route to check for the payment information.\",\"reference\":\"The transaction id to use for the search\"}', 'active', 0, '2021-06-12 08:03:56', '2021-06-12 08:03:56', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', NULL),
(206, '9dxqp2l0ovqcskzin4f1l8cfe7ejrzi6', 'v1', 'communication', 'communication/verify_and_update', 'POST', '', '{\"package_id\":\"required - The package id to pay for\",\"reference_id\":\"required - The reference id of the transaction\",\"transaction_id\":\"required - The unique id of the transaction\"}', 'active', 0, '2021-06-12 09:37:45', '2021-06-12 09:40:54', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(207, '0o6rluiwsahmfd4jz7xf9tbpuoivnqag', 'v1', 'fees', 'fees/momocard_payment', 'POST', '', '{\"checkout_url\":\"required - This is the checkout url for making payments\",\"amount\":\"required - This is the amount to be made.\",\"description\":\"The description for the payment (optional)\",\"student_id\":\"The student id to receive payment.\",\"email_address\":\"The email address of the payee\",\"contact_number\":\"The contact number of the payee.\",\"category_id\":\"The category id for the payment\",\"reference_id\":\"The unique reference id.\",\"transaction_id\":\"The unique transaction id.\"}', 'active', 0, '2021-06-12 18:27:29', '2021-06-12 18:31:25', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(208, 'qlhkw6oseqcu2vfai01rjimpxhbtzvmn', 'v1', 'booking', 'booking/log', 'POST', 'Book User Attendance', '{\"log_date\":\"required - The date to log the attendance\",\"fullname\":\"required - The fullname of the member\",\"contact\":\"The contact number of the member\",\"residence\":\"The residential address of the member\",\"temperature\":\"required - The temperature of the member\",\"booking_id\":\"The unique id of the booking log\",\"item_id\":\"The member id to log\",\"gender\":\"The gender of the users\"}', 'active', 0, '2021-06-28 08:31:55', '2021-07-05 13:43:51', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'aB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(209, 'kuvjca7ib3eycwrvzsdrtbz6xml2nglx', 'v1', 'booking', 'booking/list', 'GET', '', '{\"limit\":\"The number of rows to limit the result\",\"booking_id\":\"The unique id of the record\",\"log_date\":\"The date for the record\",\"created_by\":\"The unique id of the user who created the log.\"}', 'active', 0, '2021-06-28 18:47:03', '2021-06-28 18:48:47', '0', '0', 'aB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'aB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(210, 'uzmkobx84ax1hk3sjnwelq7upemz5qvl', 'v1', 'booking', 'booking/members', 'POST', '', '{\"data\":\"required - This is an array of the variable requested.\"}', 'active', 0, '2021-07-05 14:59:05', '2021-07-05 15:36:36', '0', '0', 'aB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'aB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(211, '2wxzafwhgmy3l7ucovkomb96zr5npxjf', 'v1', 'booking', 'booking/analitics', 'GET', '', '', 'active', 0, '2021-07-06 07:58:20', '2021-07-06 07:58:20', '0', '0', 'aB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', NULL),
(212, 'uzmkobx84ax1hk3sjnwelq7upemz5qvl', 'v1', 'booking', 'booking/members', 'GET', '', '{\"data\":\"required - This is an array of the variable requested.\"}', 'active', 0, '2021-07-05 14:59:05', '2021-07-05 15:36:36', '0', '0', 'aB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', 'aB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM'),
(213, 'yyq9gjnrexsvv72mzwlufjbi6dzex4ch', 'v1', 'auth', 'auth/change_password', 'POST', '', '{\"password\":\"required - The original password of the user\",\"password_1\":\"required - The new password\",\"password_2\":\"required - Confirmation of the new password\",\"user_id\":\"required - The unique user id\"}', 'active', 0, '2021-07-07 00:38:57', '2021-07-07 00:47:43', '0', '0', 'tnThYo5wKHG2XxgPdSVkErb7zLlqum1A', 'tnThYo5wKHG2XxgPdSVkErb7zLlqum1A'),
(214, 'uhpbmzkn46ydq8o70jtv3v5fiidwqhas', 'v1', 'notification', 'notification/mark_as_read', 'POST', '', '{\"notification_id\":\"required - The unique id of the notification to mark as read.\"}', 'active', 0, '2021-07-09 21:56:34', '2021-07-09 21:56:34', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', NULL),
(215, 'zl9yqxhkn0pnsuizmgsbd2tebvylofr7', 'v1', 'terminal_reports', 'terminal_reports/results_list', 'GET', '', '', 'active', 0, '2021-07-10 06:15:52', '2021-07-10 06:15:52', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', NULL),
(216, 'pd9ok34iwlceevytvpgr0fhnjxo68rfh', 'v1', 'payment', 'payment/pay', 'POST', '', '{\"email\":\"required - The email address of the user\",\"contact\":\"The contact number of the user to make payment\",\"amount\":\"required - The amount to pay.\",\"param\":\"Additional Parameters to parse.\"}', 'active', 0, '2021-07-13 22:13:04', '2021-07-13 22:13:04', '0', '0', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', NULL),
(217, 'pd9ok34iwlceevytvpgr0fhnjxo68rfh', 'v1', 'payment', 'payment/verify', 'POST', '', '{\"reference_id\":\"required - The unique reference id for this transaction\",\"transaction_id\":\"required - The unique transaction id generated by the payment aggregator\",\"param\":\"Additional Parameters to parse.\"}', 'active', 0, '2021-07-13 22:13:04', '2021-07-13 22:13:04', '0', '0', 'NgBS03aI1zLOq5osPf4VlCnYktbETMpQ', NULL),
(218, 'h8z6oocevtwjgkaghlibqclznbyfkxmq', 'v1', 'accounting', 'accounting/set_primary_account', 'POST', '', '{\"account_id\":\"required - The unique id of the account to set as default.\"}', 'active', 0, '2021-07-15 21:55:22', '2021-07-15 21:55:22', '0', '0', 'JB7iLyOZnzVRHF1X6UmWdtxkD8gpNTbM', NULL),
(219, 'dxhh4ov3wac5fwiem0zlgk8s2z9typng', 'v1', 'assignments', 'assignments/prepare_assessment', 'POST', 'This endpoint is meant for preparing the data to save.', '{\"assessment_title\":\"required - The title of the assessment\",\"assessment_type\":\"required - The assignment type to save.\",\"class_id\":\"required - The unique class id for the log.\",\"course_id\":\"required - The unique course/subject id to save the record\",\"date_due\":\"required - The date on which the assignment was given\",\"time_due\":\"The time for the submission of the assignment\",\"request\":\"This is the request parsed\",\"awarded_marks\":\"This is an array of awarded marks\",\"overall_score\":\"required - The total marks for the assignment\",\"assessment_description\":\"This is an optional description of the assessment log\"}', 'active', 0, '2021-07-23 10:43:31', '2021-07-26 08:27:49', '0', '0', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL'),
(220, 'gfylzd6ujn5c0mjtebkaxzrsgdeayp43', 'v1', 'assignments', 'assignments/save_assessment', 'POST', '', '{\"data\":\"required - This is an array of data for the assignment to be saved.\",\"students_list\":\"required - An array of the list of students with their score.\"}', 'active', 0, '2021-07-24 17:19:18', '2021-07-24 17:19:18', '0', '0', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', NULL),
(221, '3fgd7wsmc19ogyuj5zqksnjwdhk4b8tx', 'v1', 'auth', 'auth/password_manager', 'POST', '', '{\"data\":\"required - An array of data to process\"}', 'active', 0, '2021-08-03 05:53:40', '2021-08-03 05:53:40', '0', '0', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', NULL),
(222, 'pbk7izhzlx1dgvu60xurccfspjb2gjhr', 'v1', 'users', 'users/quick_search', 'GET', '', '{\"lookup\":\"required - This is the query term to search for\",\"user_type\":\"The type of users to search for.\"}', 'active', 0, '2021-08-04 08:07:35', '2021-08-04 08:07:35', '0', '0', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', NULL),
(223, 'dd7eatvu0jarxtxk9yvibzwjqg62ybrp', 'v1', 'support', 'support/list', 'GET', '', '{\"parent_id\":\"This is the parent id of the ticket to display\",\"ticket_id\":\"This is the ticket id to show information\",\"show_all\":\"If parsed, the comments to the tickets will be attached\",\"q\":\"This is used to search for a subject that matches the search term.\"}', 'active', 0, '2021-08-07 12:03:10', '2021-08-07 12:03:10', '0', '0', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', NULL),
(224, 'di7bp5awvu0trmrgqbmnoze1gccxuqsy', 'v1', 'support', 'support/create', 'POST', '', '{\"department\":\"required - The department to handle the issue\",\"section\":\"This is the section of the application that the user requires support\",\"subject\":\"This is the subject of the message\",\"content\":\"required - This is the content of the message\"}', 'active', 0, '2021-08-07 12:31:23', '2021-08-08 12:30:22', '0', '0', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL'),
(225, 'fci7keuat4xogsfo2ictlrh6bpqqvr0g', 'v1', 'support', 'support/reply', 'POST', '', '{\"ticket_id\":\"required - The unique id of the ticket to reply\",\"content\":\"required - This is the content of the message\",\"section\":\"required - This is the section of item to reply to\"}', 'active', 0, '2021-08-07 12:31:59', '2021-08-08 13:34:41', '0', '0', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL'),
(226, 'zlwpnyapilq1mr2e3bmc5rvgxko6cka4', 'v1', 'support', 'support/close', 'POST', '', '{\"ticket_id\":\"required - The unique id of the ticket to close\"}', 'active', 0, '2021-08-07 12:51:29', '2021-08-07 12:51:29', '0', '0', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', NULL),
(227, 'vw4gikpzu7hjxvn32f9o6dko1patleyc', 'v1', 'support', 'support/reopen', 'POST', '', '{\"ticket_id\":\"required - The unique id of the ticket to reopen\"}', 'active', 0, '2021-08-08 13:02:55', '2021-08-08 13:02:55', '0', '0', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_api_keys`
--

DROP TABLE IF EXISTS `users_api_keys`;
CREATE TABLE `users_api_keys` (
  `id` int UNSIGNED NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `user_id` varchar(255) NOT NULL,
  `username` varchar(55) DEFAULT NULL,
  `access_token` varchar(1000) DEFAULT NULL,
  `access_key` varchar(255) DEFAULT NULL,
  `access_type` enum('temp','permanent') DEFAULT 'permanent',
  `expiry_date` date DEFAULT NULL,
  `expiry_timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `requests_limit` int UNSIGNED DEFAULT '1000000',
  `total_requests` int UNSIGNED NOT NULL DEFAULT '0',
  `permissions` longtext,
  `date_generated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ;

-- --------------------------------------------------------

--
-- Table structure for table `users_api_queries`
--

DROP TABLE IF EXISTS `users_api_queries`;
CREATE TABLE `users_api_queries` (
  `id` int UNSIGNED NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `requests_count` int UNSIGNED DEFAULT NULL,
  `request_date` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_api_requests`
--

DROP TABLE IF EXISTS `users_api_requests`;
CREATE TABLE `users_api_requests` (
  `id` int UNSIGNED NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `user_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `request_uri` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `request_payload` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `request_method` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `response_code` int UNSIGNED DEFAULT NULL,
  `user_ipaddress` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `user_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_attendance_log`
--

DROP TABLE IF EXISTS `users_attendance_log`;
CREATE TABLE `users_attendance_log` (
  `id` int UNSIGNED NOT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `user_type` varchar(32) DEFAULT NULL,
  `class_id` varchar(32) DEFAULT NULL,
  `users_list` text,
  `users_data` text,
  `log_date` date DEFAULT NULL,
  `created_by` varchar(32) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `finalize` enum('0','1') NOT NULL DEFAULT '0',
  `date_finalized` datetime DEFAULT NULL,
  `finalized_by` varchar(32) DEFAULT NULL,
  `academic_year` varchar(32) DEFAULT NULL,
  `academic_term` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_chat`
--

DROP TABLE IF EXISTS `users_chat`;
CREATE TABLE `users_chat` (
  `id` int UNSIGNED NOT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `message_unique_id` varchar(32) DEFAULT NULL,
  `sender_id` varchar(32) DEFAULT NULL,
  `receiver_id` varchar(32) DEFAULT NULL,
  `message` varchar(1000) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `seen_status` enum('0','1') NOT NULL DEFAULT '0',
  `seen_date` datetime DEFAULT NULL,
  `sender_deleted` enum('0','1') NOT NULL DEFAULT '0',
  `receiver_deleted` enum('0','1') NOT NULL DEFAULT '0',
  `notice_type` varchar(12) NOT NULL DEFAULT '5',
  `user_agent` varchar(500) DEFAULT NULL,
  `user_signature` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users_chat`
--

INSERT INTO `users_chat` (`id`, `item_id`, `message_unique_id`, `sender_id`, `receiver_id`, `message`, `date_created`, `seen_status`, `seen_date`, `sender_deleted`, `receiver_deleted`, `notice_type`, `user_agent`, `user_signature`) VALUES
(1, NULL, 'QBKY6LPU0FVMDWJO', '1526BaoMyt8Inh3c0zugePkbKfsGHNmX', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'hello sir', '2021-08-07 17:51:24', '1', '2021-08-07 18:08:21', '0', '0', '5', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36', NULL),
(2, NULL, 'QBKY6LPU0FVMDWJO', '1526BaoMyt8Inh3c0zugePkbKfsGHNmX', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'How are you doing today', '2021-08-07 17:51:28', '1', '2021-08-07 18:08:21', '0', '0', '5', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36', NULL),
(3, NULL, 'QBKY6LPU0FVMDWJO', '1526BaoMyt8Inh3c0zugePkbKfsGHNmX', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'i hope all is well with you?', '2021-08-07 17:51:37', '1', '2021-08-07 18:08:21', '0', '0', '5', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36', NULL),
(4, NULL, 'QBKY6LPU0FVMDWJO', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1526BaoMyt8Inh3c0zugePkbKfsGHNmX', 'Hello, i am doing great here', '2021-08-07 18:08:36', '1', '2021-08-07 18:10:09', '0', '0', '5', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36', NULL),
(5, NULL, 'QBKY6LPU0FVMDWJO', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '1526BaoMyt8Inh3c0zugePkbKfsGHNmX', 'sup at your end too?', '2021-08-07 18:08:41', '1', '2021-08-07 18:10:09', '0', '0', '5', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36', NULL),
(6, NULL, 'QBKY6LPU0FVMDWJO', '1526BaoMyt8Inh3c0zugePkbKfsGHNmX', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'all is well here', '2021-08-07 18:10:20', '1', '2021-08-07 18:10:43', '0', '0', '5', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:90.0) Gecko/20100101 Firefox/90.0', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_emails`
--

DROP TABLE IF EXISTS `users_emails`;
CREATE TABLE `users_emails` (
  `id` int UNSIGNED NOT NULL,
  `thread_id` varchar(32) DEFAULT NULL,
  `company_id` varchar(32) DEFAULT NULL,
  `user_id` varchar(32) DEFAULT NULL,
  `subject` varchar(1000) DEFAULT NULL,
  `message` text,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `sender_details` varchar(2000) DEFAULT NULL,
  `recipient_details` text,
  `recipient_list` text,
  `copy_recipients` text,
  `copy_recipients_list` text,
  `read_list` text,
  `favorite_list` text,
  `important_list` text,
  `trash_list` text,
  `deleted_list` text,
  `archive_list` text,
  `label` enum('draft','inbox','trash','important','sent','archive') NOT NULL DEFAULT 'inbox',
  `mode` varchar(12) DEFAULT 'inbox',
  `schedule_send` enum('true','false') NOT NULL DEFAULT 'false',
  `schedule_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `sent_status` enum('0','1') NOT NULL DEFAULT '0',
  `sent_date` datetime DEFAULT NULL,
  `attachment_size` varchar(12) NOT NULL DEFAULT '0',
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_feedback`
--

DROP TABLE IF EXISTS `users_feedback`;
CREATE TABLE `users_feedback` (
  `id` int UNSIGNED NOT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `resource` varchar(40) DEFAULT NULL,
  `resource_id` varchar(40) DEFAULT NULL,
  `feedback_type` enum('reply','comment') NOT NULL DEFAULT 'reply',
  `user_id` varchar(32) DEFAULT NULL,
  `user_type` enum('business','user','bancassurance','broker','agent','nic','reinsurance','admin','nic','insurance_company') DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text,
  `mentions` varchar(2000) DEFAULT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `likes_count` varchar(12) NOT NULL DEFAULT '0',
  `comments_count` varchar(12) NOT NULL DEFAULT '0',
  `user_agent` varchar(255) DEFAULT NULL,
  `deleted` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users_feedback`
--

INSERT INTO `users_feedback` (`id`, `item_id`, `resource`, `resource_id`, `feedback_type`, `user_id`, `user_type`, `subject`, `message`, `mentions`, `date_created`, `likes_count`, `comments_count`, `user_agent`, `deleted`) VALUES
(1, 'pd1eJMm4TjP3WgGyhXxAlSLofbiqNI6B', 'assignments', 'UZjpe3Q2dXJTFKPbCWswml1fhDaVrnox', 'comment', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'admin', NULL, '&lt;div&gt;&lt;!--block--&gt;This is a test comment on this assignment&lt;/div&gt;', NULL, '2021-07-30 20:11:30', '0', '0', 'Linux | Chrome | ::1', '0');

-- --------------------------------------------------------

--
-- Table structure for table `users_gender`
--

DROP TABLE IF EXISTS `users_gender`;
CREATE TABLE `users_gender` (
  `id` int NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_gender`
--

INSERT INTO `users_gender` (`id`, `name`) VALUES
(1, 'Male'),
(2, 'Female');

-- --------------------------------------------------------

--
-- Table structure for table `users_login_history`
--

DROP TABLE IF EXISTS `users_login_history`;
CREATE TABLE `users_login_history` (
  `id` int UNSIGNED NOT NULL,
  `client_id` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastlogin` datetime DEFAULT CURRENT_TIMESTAMP,
  `log_ipaddress` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `log_browser` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `log_platform` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_login_history`
--

INSERT INTO `users_login_history` (`id`, `client_id`, `username`, `lastlogin`, `log_ipaddress`, `log_browser`, `user_id`, `log_platform`) VALUES
(1, 'MSGH000001', 'emmallob14', '2021-07-23 05:40:55', '::1', 'Chrome|Linux', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.164 Safari/537.36'),
(2, 'MSGH000001', 'emmallob14', '2021-07-23 10:30:05', '::1', 'Chrome|Linux', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.164 Safari/537.36'),
(3, 'MSGH000001', 'emmallob14', '2021-07-23 11:53:42', '::1', 'Chrome|Linux', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.164 Safari/537.36'),
(4, 'MSGH000001', 'emmallob14', '2021-07-24 11:35:29', '::1', 'Chrome|Linux', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.164 Safari/537.36'),
(5, 'MSGH000001', 'emmallob14', '2021-07-24 17:18:06', '::1', 'Chrome|Linux', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.164 Safari/537.36'),
(6, 'MSGH000001', 'emmallob14', '2021-07-26 07:22:56', '::1', 'Chrome|Linux', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.164 Safari/537.36'),
(7, 'MSGH000001', 'emmallob14', '2021-07-26 17:35:58', '::1', 'Chrome|Linux', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.164 Safari/537.36'),
(8, 'MSGH000001', 'emmallob14', '2021-07-28 04:38:13', '::1', 'Chrome|Linux', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.164 Safari/537.36'),
(9, 'MSGH000001', 'emmallob14', '2021-07-30 07:49:28', '::1', 'Chrome|Linux', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36'),
(10, 'MSGH000001', 'emmallob14@gmail.com', '2021-07-30 10:57:57', '::1', 'Chrome|Linux', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36'),
(11, 'MSGH000001', 'emmallob14@gmail.com', '2021-07-30 20:10:38', '::1', 'Chrome|Linux', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36'),
(12, 'MSGH000001', 'emmallob14', '2021-07-31 09:05:48', '::1', 'Chrome|Linux', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36'),
(13, 'MSGH000001', 'emmallob14', '2021-07-31 15:35:36', '::1', 'Chrome|Linux', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36'),
(14, 'MSGH000001', 'emmallob14', '2021-08-01 05:25:26', '::1', 'Chrome|Linux', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36'),
(15, 'MSGH000001', 'emmallob14', '2021-08-01 20:30:06', '::1', 'Chrome|Linux', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36'),
(16, 'MSGH000001', 'emmallob14', '2021-08-02 20:28:38', '::1', 'Chrome|Linux', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36'),
(17, 'MSGH000001', 'emmallob14', '2021-08-03 04:31:30', '::1', 'Chrome|Linux', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36'),
(18, 'MSGH000001', 'emmallob14', '2021-08-03 20:36:28', '::1', 'Chrome|Linux', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36'),
(19, 'MSGH000001', 'emmallob14', '2021-08-04 07:02:53', '::1', 'Chrome|Linux', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36'),
(20, 'MSGH000001', 'emmallob14', '2021-08-04 12:47:48', '::1', 'Chrome|Linux', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36'),
(21, 'MSGH000001', 'emmallob14', '2021-08-04 15:06:08', '::1', 'Chrome|Linux', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36'),
(22, 'MSGH000001', 'emmallob14', '2021-08-06 05:21:45', '::1', 'Chrome|Linux', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36'),
(23, 'MSGH000001', 'emmallob14', '2021-08-07 09:17:36', '::1', 'Chrome|Linux', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36'),
(24, 'MSGH000001', 'emmallob14', '2021-08-07 11:40:00', '::1', 'Chrome|Linux', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36'),
(25, 'MSGH000001', 'emmallob14', '2021-08-07 17:07:50', '::1', 'Chrome|Linux', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36'),
(26, 'MSGH000001', 'ST000032021', '2021-08-07 17:23:32', '::1', 'Chrome|Linux', '1526BaoMyt8Inh3c0zugePkbKfsGHNmX', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36'),
(27, 'MSGH000001', 'emmallob14', '2021-08-07 18:00:45', '::1', 'Chrome|Linux', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36'),
(28, 'MSGH000001', 'ST000032021', '2021-08-07 18:09:42', '127.0.0.1', 'Firefox|Linux', '1526BaoMyt8Inh3c0zugePkbKfsGHNmX', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:90.0) Gecko/20100101 Firefox/90.0'),
(29, 'MSGH000001', 'emmallob14', '2021-08-08 12:29:17', '::1', 'Chrome|Linux', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36'),
(30, 'MSGH000001', 'emmallob14', '2021-08-09 05:27:52', '::1', 'Chrome|Linux', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36'),
(31, 'MSGH000001', 'emmallob14', '2021-08-10 08:20:01', '127.0.0.1', 'Firefox|Linux', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:90.0) Gecko/20100101 Firefox/90.0');

-- --------------------------------------------------------

--
-- Table structure for table `users_messaging_list`
--

DROP TABLE IF EXISTS `users_messaging_list`;
CREATE TABLE `users_messaging_list` (
  `id` int UNSIGNED NOT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `template_type` varchar(32) DEFAULT NULL,
  `users_id` varchar(1000) DEFAULT NULL,
  `recipients_list` text,
  `date_requested` datetime DEFAULT CURRENT_TIMESTAMP,
  `schedule_type` enum('send_now','send_later') NOT NULL DEFAULT 'send_now',
  `schedule_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `message_medium` enum('email','sms') NOT NULL DEFAULT 'email',
  `sent_status` enum('0','1') DEFAULT '0',
  `subject` varchar(255) DEFAULT NULL,
  `message` text,
  `created_by` varchar(32) DEFAULT NULL,
  `deleted` enum('0','1') DEFAULT '0',
  `date_sent` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users_messaging_list`
--

INSERT INTO `users_messaging_list` (`id`, `item_id`, `client_id`, `template_type`, `users_id`, `recipients_list`, `date_requested`, `schedule_type`, `schedule_date`, `message_medium`, `sent_status`, `subject`, `message`, `created_by`, `deleted`, `date_sent`) VALUES
(1, 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'MSGH000001', 'verify_account', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '{\"recipients_list\":[{\"fullname\":\"Morning Star International School\",\"email\":\"emmallob14@gmail.com\",\"customer_id\":\"HjwXoJQNresMBFznDW7qAGaTCOY0c6kL\"}]}', '2021-07-22 21:20:28', 'send_now', '2021-07-22 21:20:28', 'email', '0', '[MySchoolGH] Account Verification', 'Thank you for registering your School: <strong>Morning Star International School</strong> with MySchoolGH.\r\n                        We are pleased to have you join and benefit from our platform.<br><br>\r\n                        Your can login with your <strong>Email Address:</strong> emmallob14@gmail.com or <strong>Username:</strong> emmallob14\r\n                        and the password that was provided during signup.<br><br>One of our personnel will get in touch shortly to assist you with additional setup processes that is required to aid you quick start the usage of the application.<br></br><a href=\'http://localhost/myschool_gh/verify?dw=account&token=7iHJXMS3aWFUVkP6A814T5vBLRdZlYNwoQcejKzCuxygG92bEOrIhq\'><strong>Click Here</strong></a> to verify your Email Address and also to activate the account.<br><br>', 'MSISU000001', '0', NULL),
(2, '74LEGzRCwYZJvpQTUecaoSAngrHPWqy8', NULL, 'account-verify', 'KDtbYhedUAgTC8sG1caxV6LfEklMjvFn', '{\"recipients_list\":[{\"fullname\":\"Sani Abdul Jabal \",\"email\":\"sanijabal@gmail.com\",\"customer_id\":\"KDtbYhedUAgTC8sG1caxV6LfEklMjvFn\"}]}', '2021-07-23 11:09:45', 'send_now', '2021-07-23 11:09:45', 'email', '0', '[MySchoolGH] Account Verification', 'Hello Sani,<a class=\"alert alert-success\" href=\"http://localhost/myschool_gh/verify?dw=user&token=6iqSa6TmKaICfdV4qPwH8QA7GssyvEu3H1ATzbZWJjhD3dcV0vo5UMixF2W9JyMk\">Verify your account</a><br><br>If it does not work please copy this link and place it in your browser url.<br><br>http://localhost/myschool_gh/verify?dw=user&token=6iqSa6TmKaICfdV4qPwH8QA7GssyvEu3H1ATzbZWJjhD3dcV0vo5UMixF2W9JyMk', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '0', NULL),
(3, 'uUxhoywY8e6drCSF5nQMZT3sRck9EGmD', NULL, 'password-recovery', 'uUxhoywY8e6drCSF5nQMZT3sRck9EGmD', '{\"recipients_list\":[{\"fullname\":\"Solomon Jabal Abdul\",\"email\":\"solomonabdul@gmail.com\",\"customer_id\":\"uUxhoywY8e6drCSF5nQMZT3sRck9EGmD\"}]}', '2021-07-23 12:56:15', 'send_now', '2021-07-23 12:56:15', 'email', '0', '[MySchoolGH] Change Password', 'Hi Solomon Jabal Abdul<br>You have successfully changed your password at MySchoolGH<br><br>Ignore this message if your rightfully effected this change.<br><br>If not,<a class=\"alert alert-success\" href=\"http://localhost/myschool_gh/forgot-password\">Click Here</a> if you did not perform this act.', 'uUxhoywY8e6drCSF5nQMZT3sRck9EGmD', '0', NULL),
(4, 'OhjRcwWJTNMZezBCI0uXKbtyoF5EkxgV', 'MSGH000001', 'password-recovery', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn', '{\"recipients_list\":[{\"fullname\":\"Frederick Asamoah\",\"email\":\"fredrickasamoah@gmail.com\",\"customer_id\":\"aaabYhedUAgTC8sG1caxV6LfEklMjvFn\"}]}', '2021-08-03 05:39:55', 'send_now', '2021-08-03 05:39:55', 'email', '0', '[MySchoolGH] Change Password', 'Hi Frederick Asamoah<br>You have requested to reset your password at MySchoolGH<br><br>Before you can reset your password please follow this link. The reset link expires after 2 hours<br><br><a class=\"alert alert-success\" href=\"http://localhost/myschool_gh/verify?dw=password&token=Lj9WHsQBwguOY3nvkqaFyxNecpJ7Ab2ZRd8SiEX15PotlTUmhC64D0fIGVMzr\">Click Here to Reset Password</a><br><br>If it does not work please copy this link and place it in your browser url.<br><br>http://localhost/myschool_gh/verify?dw=password&token=Lj9WHsQBwguOY3nvkqaFyxNecpJ7Ab2ZRd8SiEX15PotlTUmhC64D0fIGVMzr', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn', '0', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_notification`
--

DROP TABLE IF EXISTS `users_notification`;
CREATE TABLE `users_notification` (
  `id` int UNSIGNED NOT NULL,
  `item_id` varchar(32) DEFAULT NULL,
  `client_id` varchar(32) DEFAULT NULL,
  `user_id` varchar(32) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text,
  `resource_page` varchar(64) DEFAULT NULL,
  `initiated_by` enum('user','system') DEFAULT 'user',
  `notice_type` varchar(32) DEFAULT NULL,
  `created_by` varchar(32) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `seen_status` enum('Seen','Unseen') NOT NULL DEFAULT 'Unseen',
  `seen_date` datetime DEFAULT NULL,
  `confirmed` enum('0','1') DEFAULT '0',
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users_notification`
--

INSERT INTO `users_notification` (`id`, `item_id`, `client_id`, `user_id`, `subject`, `message`, `resource_page`, `initiated_by`, `notice_type`, `created_by`, `date_created`, `seen_status`, `seen_date`, `confirmed`, `status`) VALUES
(1, 'hfPom0C5EkWX7vMa3w1dsZyVKJpQ9c6j', 'MSGH000001', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'PaySlip for: March 2021', 'Your Payslip for <strong>March 2021</strong> has been generated successfully. CVisit the payslips page to view it.', NULL, 'system', '12', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-07-31 15:59:08', 'Seen', '2021-07-31 16:08:23', '0', '1'),
(2, 'OhjRcwWJTNMZezBCI0uXKbtyoF5EkxgV', NULL, 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn', 'Password Reset Request', 'A request was made by yourself to change your password.', NULL, 'system', '4', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn', '2021-08-03 05:39:55', 'Unseen', NULL, '0', '1'),
(3, 'ATP4LEtCI5Zkn1GgvjDiFdzh63c7mblS', 'MSGH000001', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn', 'Password Change', 'Your password was successfully changed by <strong>Emmanuel Obeng Hyde.', NULL, 'system', '4', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-08-03 06:39:43', 'Unseen', NULL, '0', '1'),
(4, 'yNAIREjBGmoZ6d9K8Y5zc1s0Vw2OeqHi', NULL, '3wczSvKX16d92VYiIuBGaZ5r4fCmnFpe', 'Account Update', '<strong>Emmanuel Obeng Hyde</strong> updated your account information', NULL, 'system', '9', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-08-07 19:08:37', 'Unseen', NULL, '0', '1'),
(5, 'VdKJyRgxDIzE2PiqUeYFu0rfkLtmpaZ5', NULL, 'uUxhoywY8e6drCSF5nQMZT3sRck9EGmD', 'Account Update', '<strong>Emmanuel Obeng Hyde</strong> updated your account information', NULL, 'system', '9', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-08-07 19:11:28', 'Unseen', NULL, '0', '1'),
(6, 'Vi4Bhu9wRqMcI612ND8OES7dsoaQz3xv', NULL, 'uUxhoywY8e6drCSF5nQMZT3sRck9EGmD', 'Account Update', '<strong>Emmanuel Obeng Hyde</strong> updated your account information', NULL, 'system', '9', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-08-07 19:11:34', 'Unseen', NULL, '0', '1'),
(7, 'QcnBAaFOSG8dlg0jRPhNCKvJEXkZLYfw', NULL, '3wczSvKX16d92VYiIuBGaZ5r4fCmnFpe', 'Account Update', '<strong>Emmanuel Obeng Hyde</strong> updated your account information', NULL, 'system', '9', 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', '2021-08-07 19:13:49', 'Unseen', NULL, '0', '1');

-- --------------------------------------------------------

--
-- Table structure for table `users_notification_types`
--

DROP TABLE IF EXISTS `users_notification_types`;
CREATE TABLE `users_notification_types` (
  `id` int NOT NULL,
  `name` varchar(62) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alias` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `priority` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `favicon` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `favicon_color` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_notification_types`
--

INSERT INTO `users_notification_types` (`id`, `name`, `alias`, `priority`, `favicon`, `favicon_color`, `status`) VALUES
(3, 'Login Attempts', 'account', 'Very High', 'fa fa-lock', 'bg-danger', '1'),
(4, 'Reset Password', 'password', 'High', 'fa fa-lock-open', 'bg-warning', '1'),
(5, 'Message', 'message', 'Moderate', 'fa fa-envelope', 'bg-primary', '1'),
(9, 'Account Update', 'account', 'Moderate', 'fa fa-user', 'bg-success', '1'),
(10, 'Status Change', 'status-change', 'Moderate', 'fa fa-random', 'bg-primary', '1'),
(12, 'Announcement', 'announcement', NULL, 'fa fa-bell', 'bg-primary', '1');

-- --------------------------------------------------------

--
-- Table structure for table `users_payments`
--

DROP TABLE IF EXISTS `users_payments`;
CREATE TABLE `users_payments` (
  `id` int UNSIGNED NOT NULL,
  `record_type` enum('licenses','policy','adverts') DEFAULT NULL,
  `record_id` varchar(32) DEFAULT NULL,
  `record_details` text,
  `user_id` varchar(32) DEFAULT NULL,
  `checkout_url` varchar(255) DEFAULT NULL,
  `initiated_by` varchar(32) DEFAULT NULL,
  `initiated_medium` enum('user','system') NOT NULL DEFAULT 'system',
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `amount` double(12,2) DEFAULT '0.00',
  `payment_status` enum('Pending','Paid','Cancelled','Failed') DEFAULT 'Pending',
  `payment_date` datetime DEFAULT NULL,
  `payment_option` enum('expresspay','slydepay','payswitch') DEFAULT NULL,
  `payment_checkout_url` varchar(500) DEFAULT NULL,
  `payment_info` varchar(500) DEFAULT NULL,
  `momo_medium` varchar(32) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `replies_count` int UNSIGNED NOT NULL DEFAULT '0',
  `comments_count` int UNSIGNED DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_posts`
--

DROP TABLE IF EXISTS `users_posts`;
CREATE TABLE `users_posts` (
  `id` int UNSIGNED NOT NULL,
  `resource_id` varchar(32) DEFAULT NULL,
  `shared_by` varchar(32) DEFAULT NULL,
  `user_type` varchar(32) DEFAULT NULL,
  `post_id` varchar(65) DEFAULT NULL,
  `post_parent_id` varchar(32) DEFAULT '0',
  `post_content` text,
  `post_mentions` varchar(1000) DEFAULT NULL,
  `post_user_agent` varchar(255) DEFAULT NULL,
  `post_user_device` varchar(255) DEFAULT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `views_count` smallint UNSIGNED NOT NULL DEFAULT '0',
  `likes_list` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `likes_count` smallint UNSIGNED DEFAULT '0',
  `comments_count` smallint UNSIGNED DEFAULT '0',
  `shares_count` varchar(12) NOT NULL DEFAULT '0',
  `visibility` enum('Public','Private','Clients') NOT NULL DEFAULT 'Public',
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_reset_request`
--

DROP TABLE IF EXISTS `users_reset_request`;
CREATE TABLE `users_reset_request` (
  `id` int NOT NULL,
  `item_id` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `client_id` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `user_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `user_agent` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `token_status` enum('USED','EXPIRED','PENDING','ANNULED') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'PENDING',
  `changed_by` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `request_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `request_token` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `reset_date` datetime DEFAULT NULL,
  `reset_agent` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `expiry_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users_reset_request`
--

INSERT INTO `users_reset_request` (`id`, `item_id`, `client_id`, `username`, `user_id`, `user_agent`, `token_status`, `changed_by`, `request_date`, `request_token`, `reset_date`, `reset_agent`, `expiry_time`) VALUES
(1, 'rS9jAdMcCa6s1f3E2YvoleyqX0n5DNxQ', 'MSGH000001', 'solomonabdul', 'uUxhoywY8e6drCSF5nQMZT3sRck9EGmD', 'Firefox Linux|127.0.0.1', 'USED', NULL, '2021-08-01 05:32:21', NULL, '2021-07-23 12:56:15', 'Firefox Linux|127.0.0.1', 1627044975),
(2, 'OhjRcwWJTNMZezBCI0uXKbtyoF5EkxgV', 'MSGH000001', 'fredrickasamoah', 'aaabYhedUAgTC8sG1caxV6LfEklMjvFn', 'Firefox Linux|127.0.0.1', 'USED', NULL, '2021-08-03 05:39:55', NULL, '2021-08-03 06:39:43', 'Chrome Linux|::1', 1627972783);

-- --------------------------------------------------------

--
-- Table structure for table `users_roles`
--

DROP TABLE IF EXISTS `users_roles`;
CREATE TABLE `users_roles` (
  `id` int UNSIGNED NOT NULL,
  `user_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `client_id` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `permissions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `date_logged` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_roles`
--

INSERT INTO `users_roles` (`id`, `user_id`, `client_id`, `permissions`, `date_logged`, `last_updated`) VALUES
(1, 'HjwXoJQNresMBFznDW7qAGaTCOY0c6kL', 'MSGH000001', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"accountant\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"guardian\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"incident\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"department\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"section\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"events\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"class\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"attendance\":{\"log\":1,\"finalize\":1},\"library\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1,\"issue\":1,\"return\":1},\"fees\":{\"view\":1,\"update\":1,\"receive\":1,\"allocation\":1,\"view_allocation\":1,\"reports\":1},\"fees_category\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"accounting\":{\"accounts\":1,\"account_type_head\":1,\"deposits\":1,\"expenditure\":1,\"validate\":1,\"modify\":1},\"promotion\":{\"promote\":1,\"approve\":1},\"results\":{\"upload\":1,\"modify\":1,\"approve\":1,\"generate\":1},\"course\":{\"view\":1,\"add\":1,\"update\":1,\"lesson\":1,\"delete\":1},\"permissions\":{\"view\":1,\"update\":1,\"change_password\":1},\"assignments\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1,\"mark\":1},\"timetable\":{\"manage\":1,\"allocate\":1},\"communication\":{\"manage\":1,\"send\":1,\"templates\":1},\"payslip\":{\"view\":1,\"modify_payroll\":1,\"generate\":1,\"validate\":1,\"manage_expense\":1},\"settings\":{\"filters\":1,\"activities\":1,\"login_history\":1,\"manage\":1,\"close\":1,\"support\":1}}}', '2021-07-22 21:20:27', NULL),
(2, 'KDtbYhedUAgTC8sG1caxV6LfEklMjvFn', 'MSGH000001', '{\"permissions\":{\"student\":{\"view\":1},\"teacher\":{\"view\":1},\"parent\":{\"view\":1},\"results\":{\"generate\":1},\"employee\":{\"view\":1},\"assignments\":{\"view\":1,\"handin\":1}}}', '2021-07-23 11:09:45', NULL),
(3, '1526BaoMyt8Inh3c0zugePkbKfsGHNmX', 'MSGH000001', '{\"permissions\":{\"student\":{\"view\":1},\"teacher\":{\"view\":1},\"parent\":{\"view\":1},\"results\":{\"generate\":1},\"employee\":{\"view\":1},\"assignments\":{\"view\":1,\"handin\":1},\"library\":{\"request\":1}}}', '2021-08-07 17:14:33', NULL),
(4, '3wczSvKX16d92VYiIuBGaZ5r4fCmnFpe', 'MSGH000001', '{\"permissions\":{\"student\":{\"view\":1},\"teacher\":{\"view\":1},\"parent\":{\"view\":1},\"results\":{\"generate\":1},\"employee\":{\"view\":1},\"assignments\":{\"view\":1,\"handin\":1},\"library\":{\"request\":1}}}', '2021-08-07 19:02:24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_temp_forms`
--

DROP TABLE IF EXISTS `users_temp_forms`;
CREATE TABLE `users_temp_forms` (
  `id` int NOT NULL,
  `user_id` varchar(32) DEFAULT NULL,
  `form_modules` varchar(64) DEFAULT NULL,
  `form_content` text,
  `expiry_time` datetime DEFAULT NULL,
  `status` enum('0','1') DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_types`
--

DROP TABLE IF EXISTS `users_types`;
CREATE TABLE `users_types` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'GUEST',
  `description` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_permissions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_types`
--

INSERT INTO `users_types` (`id`, `name`, `description`, `user_permissions`) VALUES
(1, 'STUDENT', 'student', '{\"permissions\":{\"student\":{\"view\":1},\"teacher\":{\"view\":1},\"parent\":{\"view\":1},\"results\":{\"generate\":1},\"employee\":{\"view\":1},\"assignments\":{\"view\":1,\"handin\":1},\"library\":{\"request\":1}}}'),
(2, 'TEACHER', 'teacher', '{\"permissions\":{\"student\":{\"view\":1},\"teacher\":{\"view\":1},\"parent\":{\"view\":1},\"employee\":{\"view\":1},\"attendance\":{\"log\":1},\"library\":{\"request\":1},\"course\":{\"update\":1,\"lesson\":1},\"assignments\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1,\"mark\":1},\"results\":{\"upload\":1,\"modify\":1},\"promotion\":{\"promote\":1}}}'),
(3, 'PARENT', 'parent', '{\"permissions\":{\"student\":{\"view\":1},\"teacher\":{\"view\":1},\"parent\":{\"view\":1},\"employee\":{\"view\":1},\"library\":{\"request\":1},\"results\":{\"generate\":1},\"fees\":{\"view\":1,\"view_allocation\":1},\"assignments\":{\"view\":1,\"handin\":1}}}'),
(4, 'EMPLOYEE', 'employee', '{\"permissions\":{\"student\":{\"view\":1},\"teacher\":{\"view\":1},\"parent\":{\"view\":1},\"employee\":{\"view\":1},\"library\":{\"request\":1}}}'),
(5, 'ACCOUNTANT', 'accountant', '{\"permissions\":{\"student\":{\"view\":1},\"teacher\":{\"view\":1},\"employee\":{\"view\":1},\"incident\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"department\":{\"view\":1},\"events\":{\"view\":1},\"class\":{\"view\":1},\"library\":{\"view\":1,\"return\":1,\"request\":1},\"fees\":{\"view\":1,\"update\":1,\"receive\":1,\"allocation\":1,\"view_allocation\":1,\"reports\":1},\"fees_category\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"accounting\":{\"accounts\":1,\"account_type_head\":1,\"deposits\":1,\"expenditure\":1,\"validate\":1},\"course\":{\"view\":1,\"lesson\":1},\"communication\":{\"manage\":1,\"send\":1,\"templates\":1},\"settings\":{\"filters\":1},\"payslip\":{\"view\":1,\"modify_payroll\":1,\"generate\":1,\"validate\":1,\"manage_expense\":1}}}'),
(6, 'ADMIN', 'admin', '{\"permissions\":{\"student\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"teacher\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"employee\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"accountant\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"guardian\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"incident\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"department\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"section\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"events\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"class\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"attendance\":{\"log\":1,\"finalize\":1},\"library\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1,\"issue\":1,\"return\":1},\"fees\":{\"view\":1,\"update\":1,\"receive\":1,\"allocation\":1,\"view_allocation\":1,\"reports\":1},\"fees_category\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1},\"accounting\":{\"accounts\":1,\"account_type_head\":1,\"deposits\":1,\"expenditure\":1,\"validate\":1,\"modify\":1},\"promotion\":{\"promote\":1,\"approve\":1},\"results\":{\"upload\":1,\"modify\":1,\"approve\":1,\"generate\":1},\"course\":{\"view\":1,\"add\":1,\"update\":1,\"lesson\":1,\"delete\":1},\"permissions\":{\"view\":1,\"update\":1,\"change_password\":1},\"assignments\":{\"view\":1,\"add\":1,\"update\":1,\"delete\":1,\"mark\":1},\"timetable\":{\"manage\":1,\"allocate\":1},\"communication\":{\"manage\":1,\"send\":1,\"templates\":1},\"payslip\":{\"view\":1,\"modify_payroll\":1,\"generate\":1,\"validate\":1,\"manage_expense\":1},\"settings\":{\"filters\":1,\"activities\":1,\"login_history\":1,\"manage\":1,\"close\":1,\"support\":1}}}');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wn_attr_adj_noun`
--

DROP TABLE IF EXISTS `wn_attr_adj_noun`;
CREATE TABLE `wn_attr_adj_noun` (
  `synset_id_1` decimal(10,0) DEFAULT NULL,
  `synset_id_2` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wn_cause`
--

DROP TABLE IF EXISTS `wn_cause`;
CREATE TABLE `wn_cause` (
  `synset_id_1` decimal(10,0) DEFAULT NULL,
  `synset_id_2` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wn_class_member`
--

DROP TABLE IF EXISTS `wn_class_member`;
CREATE TABLE `wn_class_member` (
  `synset_id_1` decimal(10,0) DEFAULT NULL,
  `synset_id_2` decimal(10,0) DEFAULT NULL,
  `class_type` char(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wn_entails`
--

DROP TABLE IF EXISTS `wn_entails`;
CREATE TABLE `wn_entails` (
  `synset_id_1` decimal(10,0) DEFAULT NULL,
  `synset_id_2` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wn_gloss`
--

DROP TABLE IF EXISTS `wn_gloss`;
CREATE TABLE `wn_gloss` (
  `synset_id` decimal(10,0) UNSIGNED NOT NULL DEFAULT '0',
  `gloss` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wn_hypernym`
--

DROP TABLE IF EXISTS `wn_hypernym`;
CREATE TABLE `wn_hypernym` (
  `synset_id_1` decimal(10,0) DEFAULT NULL,
  `synset_id_2` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wn_hyponym`
--

DROP TABLE IF EXISTS `wn_hyponym`;
CREATE TABLE `wn_hyponym` (
  `synset_id_1` decimal(10,0) DEFAULT NULL,
  `synset_id_2` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wn_mbr_meronym`
--

DROP TABLE IF EXISTS `wn_mbr_meronym`;
CREATE TABLE `wn_mbr_meronym` (
  `synset_id_1` decimal(10,0) DEFAULT NULL,
  `synset_id_2` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wn_part_meronym`
--

DROP TABLE IF EXISTS `wn_part_meronym`;
CREATE TABLE `wn_part_meronym` (
  `synset_id_1` decimal(10,0) DEFAULT NULL,
  `synset_id_2` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wn_similar`
--

DROP TABLE IF EXISTS `wn_similar`;
CREATE TABLE `wn_similar` (
  `synset_id_1` decimal(10,0) DEFAULT NULL,
  `synset_id_2` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wn_subst_meronym`
--

DROP TABLE IF EXISTS `wn_subst_meronym`;
CREATE TABLE `wn_subst_meronym` (
  `synset_id_1` decimal(10,0) DEFAULT NULL,
  `synset_id_2` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wn_synset`
--

DROP TABLE IF EXISTS `wn_synset`;
CREATE TABLE `wn_synset` (
  `synset_id` decimal(10,0) NOT NULL DEFAULT '0',
  `w_num` decimal(10,0) NOT NULL DEFAULT '0',
  `word` varchar(50) DEFAULT NULL,
  `ss_type` char(2) DEFAULT NULL,
  `sense_number` decimal(10,0) NOT NULL DEFAULT '0',
  `tag_count` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wn_verb_frame`
--

DROP TABLE IF EXISTS `wn_verb_frame`;
CREATE TABLE `wn_verb_frame` (
  `synset_id_1` decimal(10,0) DEFAULT NULL,
  `f_num` decimal(10,0) DEFAULT NULL,
  `w_num` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wn_verb_group`
--

DROP TABLE IF EXISTS `wn_verb_group`;
CREATE TABLE `wn_verb_group` (
  `synset_id_1` decimal(10,0) DEFAULT NULL,
  `synset_id_2` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `accounts_transaction`
--
ALTER TABLE `accounts_transaction`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `accounts_type_head`
--
ALTER TABLE `accounts_type_head`
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
-- Indexes for table `banks_list`
--
ALTER TABLE `banks_list`
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
-- Indexes for table `church_bible_classes`
--
ALTER TABLE `church_bible_classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `church_booking_log`
--
ALTER TABLE `church_booking_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `church_members`
--
ALTER TABLE `church_members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `church_organizations`
--
ALTER TABLE `church_organizations`
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
-- Indexes for table `clients_terminal_log`
--
ALTER TABLE `clients_terminal_log`
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
-- Indexes for table `knowledge_base`
--
ALTER TABLE `knowledge_base`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_urls`
--
ALTER TABLE `payment_urls`
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
-- Indexes for table `promotions_history`
--
ALTER TABLE `promotions_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `promotions_log`
--
ALTER TABLE `promotions_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smsemail_balance`
--
ALTER TABLE `smsemail_balance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smsemail_send_list`
--
ALTER TABLE `smsemail_send_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smsemail_templates`
--
ALTER TABLE `smsemail_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sms_packages`
--
ALTER TABLE `sms_packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
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
-- Indexes for table `transaction_logs`
--
ALTER TABLE `transaction_logs`
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `academic_years`
--
ALTER TABLE `academic_years`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `accounts_transaction`
--
ALTER TABLE `accounts_transaction`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `accounts_type_head`
--
ALTER TABLE `accounts_type_head`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `assignments_answers`
--
ALTER TABLE `assignments_answers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assignments_questions`
--
ALTER TABLE `assignments_questions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assignments_submitted`
--
ALTER TABLE `assignments_submitted`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `banks_list`
--
ALTER TABLE `banks_list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `blood_groups`
--
ALTER TABLE `blood_groups`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `books_borrowed`
--
ALTER TABLE `books_borrowed`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `books_borrowed_details`
--
ALTER TABLE `books_borrowed_details`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `books_stock`
--
ALTER TABLE `books_stock`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `books_type`
--
ALTER TABLE `books_type`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `church_bible_classes`
--
ALTER TABLE `church_bible_classes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `church_booking_log`
--
ALTER TABLE `church_booking_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `church_members`
--
ALTER TABLE `church_members`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `church_organizations`
--
ALTER TABLE `church_organizations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `clients_accounts`
--
ALTER TABLE `clients_accounts`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `clients_terminal_log`
--
ALTER TABLE `clients_terminal_log`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `country`
--
ALTER TABLE `country`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=250;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `courses_plan`
--
ALTER TABLE `courses_plan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `courses_resource_links`
--
ALTER TABLE `courses_resource_links`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cron_scheduler`
--
ALTER TABLE `cron_scheduler`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `currency`
--
ALTER TABLE `currency`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events_types`
--
ALTER TABLE `events_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `e_learning`
--
ALTER TABLE `e_learning`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `e_learning_comments`
--
ALTER TABLE `e_learning_comments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `e_learning_timer`
--
ALTER TABLE `e_learning_timer`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `e_learning_views`
--
ALTER TABLE `e_learning_views`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fees_allocations`
--
ALTER TABLE `fees_allocations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `fees_category`
--
ALTER TABLE `fees_category`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `fees_collection`
--
ALTER TABLE `fees_collection`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `fees_payments`
--
ALTER TABLE `fees_payments`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `files_attachment`
--
ALTER TABLE `files_attachment`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `grading_system`
--
ALTER TABLE `grading_system`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `grading_terminal_logs`
--
ALTER TABLE `grading_terminal_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grading_terminal_scores`
--
ALTER TABLE `grading_terminal_scores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guardian_relation`
--
ALTER TABLE `guardian_relation`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `incidents`
--
ALTER TABLE `incidents`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `knowledge_base`
--
ALTER TABLE `knowledge_base`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `payment_urls`
--
ALTER TABLE `payment_urls`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payslips`
--
ALTER TABLE `payslips`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payslips_allowance_types`
--
ALTER TABLE `payslips_allowance_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payslips_details`
--
ALTER TABLE `payslips_details`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `payslips_employees_allowances`
--
ALTER TABLE `payslips_employees_allowances`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payslips_employees_payroll`
--
ALTER TABLE `payslips_employees_payroll`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `periods`
--
ALTER TABLE `periods`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `promotions_history`
--
ALTER TABLE `promotions_history`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `promotions_log`
--
ALTER TABLE `promotions_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `smsemail_balance`
--
ALTER TABLE `smsemail_balance`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `smsemail_send_list`
--
ALTER TABLE `smsemail_send_list`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `smsemail_templates`
--
ALTER TABLE `smsemail_templates`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sms_packages`
--
ALTER TABLE `sms_packages`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `timetables_slots_allocation`
--
ALTER TABLE `timetables_slots_allocation`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `transaction_logs`
--
ALTER TABLE `transaction_logs`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users_access_attempt`
--
ALTER TABLE `users_access_attempt`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users_activity_logs`
--
ALTER TABLE `users_activity_logs`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;

--
-- AUTO_INCREMENT for table `users_api_endpoints`
--
ALTER TABLE `users_api_endpoints`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=228;

--
-- AUTO_INCREMENT for table `users_api_keys`
--
ALTER TABLE `users_api_keys`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_api_queries`
--
ALTER TABLE `users_api_queries`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_api_requests`
--
ALTER TABLE `users_api_requests`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_attendance_log`
--
ALTER TABLE `users_attendance_log`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_chat`
--
ALTER TABLE `users_chat`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users_emails`
--
ALTER TABLE `users_emails`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_feedback`
--
ALTER TABLE `users_feedback`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users_gender`
--
ALTER TABLE `users_gender`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users_login_history`
--
ALTER TABLE `users_login_history`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `users_messaging_list`
--
ALTER TABLE `users_messaging_list`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users_notification`
--
ALTER TABLE `users_notification`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users_notification_types`
--
ALTER TABLE `users_notification_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users_payments`
--
ALTER TABLE `users_payments`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_posts`
--
ALTER TABLE `users_posts`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_reset_request`
--
ALTER TABLE `users_reset_request`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users_roles`
--
ALTER TABLE `users_roles`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users_temp_forms`
--
ALTER TABLE `users_temp_forms`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_types`
--
ALTER TABLE `users_types`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
