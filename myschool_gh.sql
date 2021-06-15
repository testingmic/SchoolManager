-- MariaDB dump 10.18  Distrib 10.4.17-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: myschool_gh
-- ------------------------------------------------------
-- Server version	10.4.17-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `_blood_groups`
--

DROP TABLE IF EXISTS `_blood_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_blood_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_blood_groups`
--

LOCK TABLES `_blood_groups` WRITE;
/*!40000 ALTER TABLE `_blood_groups` DISABLE KEYS */;
INSERT INTO `_blood_groups` VALUES (1,'A+'),(2,'A-'),(3,'B+'),(4,'B-'),(5,'O+'),(6,'O-');
/*!40000 ALTER TABLE `_blood_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_books`
--

DROP TABLE IF EXISTS `_books`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_books` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL DEFAULT 1,
  `code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `isbn` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `author` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `quantity` int(10) DEFAULT NULL,
  `rackNo` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rowNo` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `category_id` int(10) DEFAULT NULL,
  `desc` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `department_id` int(10) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `deleted` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `programme_id` int(11) DEFAULT NULL,
  `added_by` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` date DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `books_code_unique` (`code`),
  KEY `books_department_id_foreign` (`department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_books`
--

LOCK TABLES `_books` WRITE;
/*!40000 ALTER TABLE `_books` DISABLE KEYS */;
INSERT INTO `_books` VALUES (1,1,'01201','FAGSR45454','The products of JavaScript infco','This is the book for the day. I have','Henry Asmah',100,'12','',1,'This is the book for the day. I have',1,NULL,'1','0',2,NULL,'2019-11-07'),(2,1,'01203','FAGSR45454DDG','Principles of OOP','This is the way to go','Emmanuel Obeng',102,'10','',2,'This is the way to go',2,NULL,'1','0',2,NULL,'2019-11-07'),(4,1,NULL,'HAI012152102','Update this book for me','This is the book that i want to insert into the database system.','Cecilia Boateng',58,'A120','',6,'This is the book that i want to insert into the database system.',0,NULL,'1','0',2,NULL,'2019-11-16');
/*!40000 ALTER TABLE `_books` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_books_borrowed`
--

DROP TABLE IF EXISTS `_books_borrowed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_books_borrowed` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `school_id` int(11) DEFAULT 1,
  `student_id` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `books_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `issueDate` date NOT NULL,
  `returnDate` date NOT NULL,
  `fine` decimal(18,2) NOT NULL DEFAULT 0.00,
  `actual_paid` decimal(12,2) NOT NULL DEFAULT 0.00,
  `fine_paid` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `status` enum('Borrowed','Returned') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Borrowed',
  `created_at` datetime DEFAULT current_timestamp(),
  `issued_by` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `actual_date_returned` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `borrow_books_books_id_foreign` (`books_id`),
  KEY `borrow_books_students_id_foreign` (`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_books_borrowed`
--

LOCK TABLES `_books_borrowed` WRITE;
/*!40000 ALTER TABLE `_books_borrowed` DISABLE KEYS */;
INSERT INTO `_books_borrowed` VALUES (1,1,'MSG1468397','1||2','2019-10-28','2019-10-31',15.00,15.00,'1','Returned','2019-10-28 19:10:16','OY550107771','2019-11-10 19:43:13','2019-10-28 19:10:16',NULL),(2,1,'VI550107774','2','2019-10-28','2019-11-10',0.00,0.00,'1','Returned','2019-10-28 19:10:16','OY550107771','2019-11-10 19:43:13','2019-10-28 19:10:16',NULL),(6,1,'VI550107774','1||2','2019-11-13','2019-11-15',15.00,0.00,'1','Returned','2019-11-13 23:44:37','MYG54087571','2020-01-17 12:13:04','2019-11-13 23:44:37',NULL),(7,1,'MSG9862354','1||2','2019-11-14','2019-11-27',6.00,6.00,'1','Returned','2019-11-14 00:03:34','MYG54087571','2020-01-17 12:12:36','2019-11-14 00:03:34',NULL),(8,1,'VI550107774','1','2019-11-14','2019-11-21',0.00,0.00,'1','Returned','2019-11-14 07:06:50','MYG54087571','2020-01-17 12:12:51','2019-11-14 07:06:50',NULL),(9,1,'VI550107774','4||2','2020-01-17','2020-01-17',0.00,0.00,'1','Returned','2020-01-17 12:09:40','OY550107770','2020-01-17 12:12:56','2020-01-17 12:09:40',NULL),(10,1,'VI550107774','2','2021-01-05','2021-01-13',100.00,0.00,'0','Borrowed','2021-01-05 22:36:15','OY550107770',NULL,'2021-01-05 22:36:15',NULL),(11,1,'MSG9862354','4||2||1','2021-01-03','2021-01-10',20.00,0.00,'0','Borrowed','2021-01-05 22:37:36','OY550107770',NULL,'2021-01-05 22:37:36',NULL);
/*!40000 ALTER TABLE `_books_borrowed` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_books_borrowed_details`
--

DROP TABLE IF EXISTS `_books_borrowed_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_books_borrowed_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `borrowed_id` int(11) DEFAULT NULL,
  `book_id` int(11) DEFAULT NULL,
  `date_borrowed` datetime DEFAULT current_timestamp(),
  `return_date` date DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `fine` decimal(10,2) NOT NULL DEFAULT 0.00,
  `actual_paid` decimal(12,2) NOT NULL DEFAULT 0.00,
  `fine_paid` enum('0','1') NOT NULL DEFAULT '0',
  `issued_by` varchar(255) DEFAULT NULL,
  `received_by` varchar(255) DEFAULT NULL,
  `actual_date_returned` datetime DEFAULT NULL,
  `status` enum('Returned','Borrowed') NOT NULL DEFAULT 'Borrowed',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_books_borrowed_details`
--

LOCK TABLES `_books_borrowed_details` WRITE;
/*!40000 ALTER TABLE `_books_borrowed_details` DISABLE KEYS */;
INSERT INTO `_books_borrowed_details` VALUES (1,1,1,'2019-10-28 22:50:17','2019-10-31',1,5.00,5.00,'1','OY550107771','MSG1468397','2019-11-10 19:42:19','Returned'),(2,1,2,'2019-10-28 22:50:17','2019-10-31',1,10.00,10.00,'1','OY550107771','MSG1468397','2019-11-10 19:42:20','Returned'),(3,2,2,'2019-10-28 22:50:17','2019-11-10',1,0.00,0.00,'1','OY550107771','MSG1468397','2019-11-10 19:52:36','Returned'),(10,6,1,'2019-11-13 23:44:37','2019-11-15',3,7.50,0.00,'0','MYG54087571','VI550107774','2020-01-17 12:13:04','Returned'),(11,6,2,'2019-11-13 23:44:37','2019-11-15',1,7.50,0.00,'0','MYG54087571','VI550107774','2020-01-17 12:13:04','Returned'),(12,7,1,'2019-11-14 00:03:34','2019-11-27',3,3.00,3.00,'1','MYG54087571','MSG9862354','2020-01-17 12:12:36','Returned'),(13,7,2,'2019-11-14 00:03:34','2019-11-27',2,3.00,3.00,'1','MYG54087571','MSG9862354','2020-01-17 12:12:36','Returned'),(14,8,1,'2019-11-14 07:06:50','2019-11-21',2,0.00,0.00,'0','MYG54087571','VI550107774','2020-01-17 12:12:51','Returned'),(15,9,4,'2020-01-17 12:09:40','2020-01-17',1,0.00,0.00,'0','OY550107770','VI550107774','2020-01-17 12:12:56','Returned'),(16,9,2,'2020-01-17 12:09:40','2020-01-17',2,0.00,0.00,'1','OY550107770','VI550107774','2020-01-17 12:12:56','Returned'),(17,10,2,'2021-01-05 22:36:15','2021-01-13',1,100.00,0.00,'0','OY550107770','VI550107774',NULL,'Borrowed'),(18,11,4,'2021-01-05 22:37:36','2021-01-14',2,6.67,0.00,'0','OY550107770','MSG9862354','2021-01-07 11:08:21','Borrowed'),(19,11,2,'2021-01-05 22:37:36','2021-01-14',3,6.67,0.00,'0','OY550107770','MSG9862354','2021-01-07 11:08:21','Borrowed'),(20,11,1,'2021-01-05 22:37:36','2021-01-14',3,6.67,0.00,'0','OY550107770','MSG9862354','2021-01-07 11:08:21','Borrowed');
/*!40000 ALTER TABLE `_books_borrowed_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_books_stock`
--

DROP TABLE IF EXISTS `_books_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_books_stock` (
  `books_id` int(10) unsigned NOT NULL,
  `quantity` int(10) unsigned NOT NULL,
  KEY `stock_books_books_id_foreign` (`books_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_books_stock`
--

LOCK TABLES `_books_stock` WRITE;
/*!40000 ALTER TABLE `_books_stock` DISABLE KEYS */;
/*!40000 ALTER TABLE `_books_stock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_books_type`
--

DROP TABLE IF EXISTS `_books_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_books_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL DEFAULT 1,
  `department_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_books_type`
--

LOCK TABLES `_books_type` WRITE;
/*!40000 ALTER TABLE `_books_type` DISABLE KEYS */;
INSERT INTO `_books_type` VALUES (1,1,2,'Academic','1'),(2,1,3,'Story','1'),(3,1,4,'Magazine','1'),(4,1,NULL,'Other','0'),(5,1,NULL,'Computer Programming','0'),(6,1,7,'ICT For Schools','1'),(7,1,NULL,'Main Books','0'),(8,1,NULL,'Last Refresh','0'),(9,1,8,'Adding new book','1'),(10,1,NULL,'Refresh Page','0');
/*!40000 ALTER TABLE `_books_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_classes`
--

DROP TABLE IF EXISTS `_classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `programme` varchar(255) DEFAULT NULL,
  `programme_id` int(11) DEFAULT NULL,
  `academic_year` int(11) DEFAULT NULL,
  `academic_term` int(5) DEFAULT 1,
  `form_id` int(11) DEFAULT NULL,
  `class_teacher` varchar(35) DEFAULT NULL,
  `class_prefect` varchar(25) DEFAULT NULL,
  `no_on_roll` int(11) NOT NULL DEFAULT 0,
  `status` enum('0','1') DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_classes`
--

LOCK TABLES `_classes` WRITE;
/*!40000 ALTER TABLE `_classes` DISABLE KEYS */;
INSERT INTO `_classes` VALUES (1,1,'GENERAL ARTS 1',NULL,'GENERAL ARTS',2,4,1,1,NULL,NULL,0,'1'),(2,1,'GENERAL ARTS 2',NULL,'GENERAL ARTS',2,4,1,1,'',NULL,0,'1'),(3,1,'SCIENCE 3',NULL,'GENERAL SCIENCE',1,4,1,1,NULL,NULL,0,'0'),(4,1,'VISUAL ARTS D',NULL,'VISUAL ARTS',3,4,1,1,NULL,NULL,0,'1'),(5,1,'2 HC 2',NULL,'HOME ECONOMICS',4,4,1,2,NULL,NULL,0,'1'),(7,4,'CLASS 1',NULL,'',0,NULL,1,0,'',NULL,0,'1'),(8,4,'CLASS 2','class-2','',0,NULL,1,0,'',NULL,0,'1'),(9,4,'CLASS 3','class-3','',0,NULL,1,0,'',NULL,0,'1'),(10,4,'CLASS 4','class-4','',0,NULL,1,0,'',NULL,0,'1'),(11,4,'CLASS 5','class-5','',0,NULL,1,0,'',NULL,0,'1'),(12,4,'CLASS 6','class-6','',0,NULL,1,0,'',NULL,0,'1'),(13,4,'JHS 1','jhs-1','',0,NULL,1,0,'',NULL,0,'1'),(14,4,'JHS 2','jhs-2','',0,NULL,1,0,'',NULL,0,'1'),(15,4,'JHS 3','jhs-3','',0,NULL,1,0,'',NULL,0,'1'),(16,3,'CLASS 6','class-6','',0,NULL,1,0,'OY550107772',NULL,0,'1'),(17,1,'General Arts 4 (History Option)','general-arts-4-history-option_VTxqs',NULL,2,4,1,NULL,'OY550107772',NULL,0,'1'),(18,1,'Management in Business Models 3','management-in-business-models-3_Rc5Aq',NULL,5,4,1,NULL,'null','MSG9862354',0,'1'),(19,1,'1 Home Economics 5','1-home-economics-5_1Jx5Z',NULL,4,4,1,NULL,'OY550107772',NULL,0,'1');
/*!40000 ALTER TABLE `_classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_courses`
--

DROP TABLE IF EXISTS `_courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) DEFAULT NULL,
  `course_code` varchar(255) DEFAULT NULL,
  `credit_hours` varchar(25) DEFAULT NULL,
  `academic_term` varchar(11) DEFAULT NULL,
  `academic_year` varchar(11) DEFAULT NULL,
  `unique_id` varchar(35) DEFAULT NULL,
  `programme_id` int(11) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `course_teacher` varchar(500) DEFAULT NULL COMMENT 'THIS  IS WHERE THE ID OF THE TEACHER OR WHOEVER INSERTED IT WILL APPEAR',
  `content` text DEFAULT NULL,
  `date_added` date DEFAULT NULL,
  `modified_by` varchar(35) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp(),
  `questions_count` int(11) DEFAULT 0,
  `status` enum('0','1') DEFAULT '1',
  `deleted` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_courses`
--

LOCK TABLES `_courses` WRITE;
/*!40000 ALTER TABLE `_courses` DISABLE KEYS */;
INSERT INTO `_courses` VALUES (28,1,NULL,'4','1st','2019/2020',NULL,3,4,'Principles of Arts',NULL,'OY550107772',NULL,NULL,NULL,'2019-10-23 21:18:10',0,'1','0');
/*!40000 ALTER TABLE `_courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_departments`
--

DROP TABLE IF EXISTS `_departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL DEFAULT 1,
  `name` varchar(25) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_departments`
--

LOCK TABLES `_departments` WRITE;
/*!40000 ALTER TABLE `_departments` DISABLE KEYS */;
INSERT INTO `_departments` VALUES (1,1,'Blue','1'),(2,1,'First Department Name','1'),(3,1,'Green','1'),(4,1,'Yellow','0'),(5,1,'Pink','1'),(6,1,'O- Edited','0'),(7,1,'Test Section Modified','1'),(8,1,'Final test section','1'),(9,1,'Last Step','0'),(10,1,'Adding a new department','1');
/*!40000 ALTER TABLE `_departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_expenses_type`
--

DROP TABLE IF EXISTS `_expenses_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_expenses_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL DEFAULT 1,
  `expenses_type` varchar(255) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_expenses_type`
--

LOCK TABLES `_expenses_type` WRITE;
/*!40000 ALTER TABLE `_expenses_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `_expenses_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_fees_allocations`
--

DROP TABLE IF EXISTS `_fees_allocations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_fees_allocations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL DEFAULT 1,
  `programme_id` int(11) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `fees_type` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `academic_year` varchar(25) NOT NULL DEFAULT '2019/2020',
  `academic_term` varchar(30) NOT NULL DEFAULT '1st',
  `status` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_fees_allocations`
--

LOCK TABLES `_fees_allocations` WRITE;
/*!40000 ALTER TABLE `_fees_allocations` DISABLE KEYS */;
INSERT INTO `_fees_allocations` VALUES (1,1,2,1,1,450.00,'2019/2020','1st','1'),(2,1,2,2,1,450.00,'2019/2020','1st','1'),(3,1,2,17,1,450.00,'2019/2020','1st','1'),(4,1,2,1,3,20.00,'2019/2020','1st','1'),(5,1,2,2,3,20.00,'2019/2020','1st','1'),(6,1,2,17,3,20.00,'2019/2020','1st','1'),(7,1,2,1,4,45.00,'2019/2020','1st','1'),(8,1,2,2,4,45.00,'2019/2020','1st','1'),(9,1,2,17,4,45.00,'2019/2020','1st','1'),(13,1,2,1,2,700.00,'2019/2020','1st','1'),(14,1,2,2,2,700.00,'2019/2020','1st','1'),(15,1,2,17,2,700.00,'2019/2020','1st','1');
/*!40000 ALTER TABLE `_fees_allocations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_fees_collection`
--

DROP TABLE IF EXISTS `_fees_collection`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_fees_collection` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL DEFAULT 1,
  `unique_id` varchar(25) DEFAULT NULL,
  `student_id` varchar(25) DEFAULT NULL,
  `programme_id` int(11) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `fees_type` int(11) DEFAULT NULL,
  `amount` decimal(25,2) DEFAULT 0.00,
  `recorded_by` varchar(25) DEFAULT NULL,
  `recorded_date` datetime NOT NULL DEFAULT current_timestamp(),
  `description` text DEFAULT NULL,
  `academic_year` varchar(25) DEFAULT '2019/2020',
  `academic_term` varchar(25) DEFAULT '1st',
  `status` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_id` (`unique_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_fees_collection`
--

LOCK TABLES `_fees_collection` WRITE;
/*!40000 ALTER TABLE `_fees_collection` DISABLE KEYS */;
INSERT INTO `_fees_collection` VALUES (1,1,'nro2ZESaxh1l60','VI550107774',2,1,1,150.00,'OY550107770','2020-01-18 18:33:35','','2019/2020','1st','1'),(2,1,'XTRlaBKLMNwOQk','VI550107774',2,1,1,320.00,'OY550107770','2020-01-18 18:34:26','','2019/2020','1st','1'),(3,1,'rLVTwOzS5B7mUj','VI550107774',2,1,1,10.00,'OY550107770','2020-01-18 18:34:59','','2019/2020','1st','1'),(4,1,'CPQRrS2LjFVZew','MSG9862354',2,2,1,400.00,'OY550107770','2021-01-08 11:02:02','','2019/2020','1st','1'),(5,1,'qNfaR5EJokmT4p','MSG9862354',2,2,1,50.00,'OY550107770','2021-01-08 11:02:48','','2019/2020','1st','1'),(6,1,'l3poUCxdyiTJgG','MSG1468397',2,1,1,450.00,'OY550107770','2021-01-08 11:16:10','','2019/2020','1st','1');
/*!40000 ALTER TABLE `_fees_collection` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_fees_payments`
--

DROP TABLE IF EXISTS `_fees_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_fees_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL DEFAULT 1,
  `student_id` varchar(25) DEFAULT NULL,
  `fees_type` int(11) DEFAULT NULL,
  `amount_due` decimal(10,2) NOT NULL DEFAULT 0.00,
  `amount_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `academic_year` varchar(25) DEFAULT '2019/2020',
  `academic_term` varchar(25) DEFAULT '1st',
  `paid_status` enum('0','1') NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_fees_payments`
--

LOCK TABLES `_fees_payments` WRITE;
/*!40000 ALTER TABLE `_fees_payments` DISABLE KEYS */;
INSERT INTO `_fees_payments` VALUES (1,1,'VI550107774',1,450.00,480.00,0.00,'2019/2020','1st','1','2021-01-08 11:15:59'),(2,1,'MSG9862354',1,450.00,450.00,0.00,'2019/2020','1st','1','2021-01-08 11:15:59'),(3,1,'MSG1468397',1,450.00,450.00,0.00,'2019/2020','1st','1','2021-01-08 11:15:59'),(4,1,'MSG92854631',1,450.00,0.00,450.00,'2019/2020','1st','0','2021-01-08 11:21:01'),(5,1,'MSG9862354',2,700.00,0.00,700.00,'2019/2020','1st','0','2021-01-08 11:21:20'),(6,1,'MSG92854631',2,700.00,0.00,700.00,'2019/2020','1st','0','2021-01-08 11:21:21'),(7,1,'MSG1468397',2,700.00,0.00,700.00,'2019/2020','1st','0','2021-01-08 11:21:21'),(8,1,'OY550107773',1,450.00,0.00,450.00,'2019/2020','1st','0','2021-01-08 11:21:52'),(9,1,'NE550107775',1,450.00,0.00,450.00,'2019/2020','1st','0','2021-01-08 11:21:52'),(10,1,'MSG71235894',1,450.00,0.00,450.00,'2019/2020','1st','0','2021-01-08 11:21:53');
/*!40000 ALTER TABLE `_fees_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_fees_type`
--

DROP TABLE IF EXISTS `_fees_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_fees_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL DEFAULT 1,
  `fees_type` varchar(255) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_fees_type`
--

LOCK TABLES `_fees_type` WRITE;
/*!40000 ALTER TABLE `_fees_type` DISABLE KEYS */;
INSERT INTO `_fees_type` VALUES (1,1,'Tuition Fees','1'),(2,1,'Hostel Fees','1'),(3,1,'PTA Dues','1'),(4,1,'Information Technology Dues','1');
/*!40000 ALTER TABLE `_fees_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_gender`
--

DROP TABLE IF EXISTS `_gender`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_gender` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) DEFAULT 1,
  `gender` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_gender`
--

LOCK TABLES `_gender` WRITE;
/*!40000 ALTER TABLE `_gender` DISABLE KEYS */;
INSERT INTO `_gender` VALUES (1,1,'Male'),(2,1,'Female'),(3,1,'Bi-Sexual'),(4,1,'Heterosexual');
/*!40000 ALTER TABLE `_gender` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_history_reason`
--

DROP TABLE IF EXISTS `_history_reason`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_history_reason` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reason` varchar(255) NOT NULL,
  `description` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_history_reason`
--

LOCK TABLES `_history_reason` WRITE;
/*!40000 ALTER TABLE `_history_reason` DISABLE KEYS */;
INSERT INTO `_history_reason` VALUES (1,'Sick',''),(2,'Permission to Absent','');
/*!40000 ALTER TABLE `_history_reason` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_hostel_list`
--

DROP TABLE IF EXISTS `_hostel_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_hostel_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL DEFAULT 1,
  `hostel_name` varchar(255) DEFAULT NULL,
  `hostel_description` text DEFAULT NULL,
  `hostel_manager` varchar(255) DEFAULT NULL,
  `hostel_tutor` varchar(255) DEFAULT NULL,
  `hostel_porter` varchar(255) DEFAULT NULL,
  `hostel_prefect` varchar(255) DEFAULT NULL,
  `hostel_code` varchar(255) DEFAULT NULL,
  `hostel_rates` decimal(10,2) NOT NULL DEFAULT 0.00,
  `hostel_rooms_capacity` int(11) DEFAULT 0,
  `hostel_current_occupancy` int(11) NOT NULL DEFAULT 0,
  `hostel_state` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_hostel_list`
--

LOCK TABLES `_hostel_list` WRITE;
/*!40000 ALTER TABLE `_hostel_list` DISABLE KEYS */;
INSERT INTO `_hostel_list` VALUES (2,1,'This is the first hostel','','null','null','null','null','HC DEO023',0.00,400,0,'1'),(3,1,'This is the first hostel','','null','null','OY550107771','null','HC DEO023',432.00,250,0,'1'),(4,1,'Second Room Information','This is the best hostel that you can ever reside in.','OY550107773','OY550107771','OY550107772','MSG9862354','UIDFKDI',500.00,120,0,'1');
/*!40000 ALTER TABLE `_hostel_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_hostel_room`
--

DROP TABLE IF EXISTS `_hostel_room`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_hostel_room` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_code` varchar(255) DEFAULT NULL,
  `room_name` varchar(255) DEFAULT NULL,
  `room_capacity` int(11) DEFAULT 0,
  `room_notes` text DEFAULT NULL,
  `room_status` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_hostel_room`
--

LOCK TABLES `_hostel_room` WRITE;
/*!40000 ALTER TABLE `_hostel_room` DISABLE KEYS */;
/*!40000 ALTER TABLE `_hostel_room` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_hostel_room_allocation`
--

DROP TABLE IF EXISTS `_hostel_room_allocation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_hostel_room_allocation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_code` varchar(255) DEFAULT NULL,
  `room_bed` varchar(25) DEFAULT NULL,
  `student_id` varchar(25) DEFAULT NULL,
  `assigned_by` varchar(255) DEFAULT NULL,
  `date_assigned` datetime DEFAULT current_timestamp(),
  `current_state` enum('Active','Inactive','Vacated') NOT NULL DEFAULT 'Active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_hostel_room_allocation`
--

LOCK TABLES `_hostel_room_allocation` WRITE;
/*!40000 ALTER TABLE `_hostel_room_allocation` DISABLE KEYS */;
/*!40000 ALTER TABLE `_hostel_room_allocation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_notices`
--

DROP TABLE IF EXISTS `_notices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_notices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) DEFAULT 1,
  `title` varchar(255) DEFAULT NULL,
  `comment` text NOT NULL,
  `posted_by` varchar(255) DEFAULT NULL,
  `user_group` varchar(255) DEFAULT NULL,
  `current_state` enum('PENDING','COMPLETED','IN PROGRESS','CANCELLED') NOT NULL DEFAULT 'PENDING',
  `urgency` varchar(15) DEFAULT NULL,
  `date_posted` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('0','1') DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_notices`
--

LOCK TABLES `_notices` WRITE;
/*!40000 ALTER TABLE `_notices` DISABLE KEYS */;
INSERT INTO `_notices` VALUES (1,1,'Great School manag mene esom tus eleifend lectus sed maximus mi faucibusnting.','',NULL,NULL,'PENDING','pink','2019-10-19 07:57:24','1'),(2,1,'Great School manag printing.','',NULL,NULL,'PENDING','skyblue','2019-10-19 07:57:24','1');
/*!40000 ALTER TABLE `_notices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_programmes`
--

DROP TABLE IF EXISTS `_programmes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_programmes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) DEFAULT NULL,
  `programme_name` varchar(255) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_programmes`
--

LOCK TABLES `_programmes` WRITE;
/*!40000 ALTER TABLE `_programmes` DISABLE KEYS */;
INSERT INTO `_programmes` VALUES (1,1,'GENERAL SCIENCE',2,'1'),(2,1,'GENERAL ARTS',3,'1'),(3,1,'VISUAL ARTS',3,'1'),(4,1,'HOME ECONOMICS',1,'1'),(5,1,'BUSINESS ARTS',5,'1'),(6,1,'Business Integration',6,'1');
/*!40000 ALTER TABLE `_programmes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_region`
--

DROP TABLE IF EXISTS `_region`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_region` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_region`
--

LOCK TABLES `_region` WRITE;
/*!40000 ALTER TABLE `_region` DISABLE KEYS */;
INSERT INTO `_region` VALUES (1,'Christian'),(2,'Islam'),(3,'Hindu'),(4,'Buddish');
/*!40000 ALTER TABLE `_region` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_religion`
--

DROP TABLE IF EXISTS `_religion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_religion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_religion`
--

LOCK TABLES `_religion` WRITE;
/*!40000 ALTER TABLE `_religion` DISABLE KEYS */;
INSERT INTO `_religion` VALUES (1,'Christian'),(2,'Islam'),(3,'Hindu'),(4,'Buddish');
/*!40000 ALTER TABLE `_religion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_schools`
--

DROP TABLE IF EXISTS `_schools`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_schools` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(1000) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `school_type` enum('MONT','PRIMARY','JHS_SHS','TERTIARY') DEFAULT NULL,
  `contact` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` varchar(1000) DEFAULT NULL,
  `location` varchar(1000) DEFAULT NULL,
  `google_maps_location` varchar(1000) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `motto` varchar(1000) DEFAULT NULL,
  `receipt_note` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_schools`
--

LOCK TABLES `_schools` WRITE;
/*!40000 ALTER TABLE `_schools` DISABLE KEYS */;
INSERT INTO `_schools` VALUES (1,'Emmallen Networks Inc','assets/img/logo2.png','TERTIARY','0550107770','emmallob14@gmail.com','P. O. Box AF 2582, Adentan','Dodowa, Near Dangme West Hospital',NULL,'This is an IT Solutions School','We the best!',NULL);
/*!40000 ALTER TABLE `_schools` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_schools_academics`
--

DROP TABLE IF EXISTS `_schools_academics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_schools_academics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) DEFAULT NULL,
  `academic_term` varchar(255) DEFAULT NULL,
  `academic_year` varchar(255) DEFAULT NULL,
  `term_begins` date DEFAULT NULL,
  `term_ends` date DEFAULT NULL,
  `nextterm_begins` date DEFAULT NULL,
  `nextterm_ends` date DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_schools_academics`
--

LOCK TABLES `_schools_academics` WRITE;
/*!40000 ALTER TABLE `_schools_academics` DISABLE KEYS */;
INSERT INTO `_schools_academics` VALUES (1,1,'1st','2019/2020','2019-09-01','2019-12-26','2020-01-13','2020-04-01','1');
/*!40000 ALTER TABLE `_schools_academics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_sections`
--

DROP TABLE IF EXISTS `_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL DEFAULT 1,
  `name` varchar(25) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_sections`
--

LOCK TABLES `_sections` WRITE;
/*!40000 ALTER TABLE `_sections` DISABLE KEYS */;
INSERT INTO `_sections` VALUES (1,1,'Blue','1'),(2,1,'Red','1'),(3,1,'Green','1'),(4,1,'Yellow','1'),(5,1,'Pink Section','1'),(6,1,'O- Edited','1'),(7,1,'Test Section Modified','1'),(8,1,'Final test section','1'),(9,1,'Last Step','0');
/*!40000 ALTER TABLE `_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_titles`
--

DROP TABLE IF EXISTS `_titles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_titles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_titles`
--

LOCK TABLES `_titles` WRITE;
/*!40000 ALTER TABLE `_titles` DISABLE KEYS */;
INSERT INTO `_titles` VALUES (1,'Mr'),(2,'Mrs'),(3,'Miss'),(4,'Madam'),(5,'Hon.'),(6,'Dr.'),(7,'Prof.');
/*!40000 ALTER TABLE `_titles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_users`
--

DROP TABLE IF EXISTS `_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entry_year` varchar(255) DEFAULT '2019/2020',
  `entry_term` varchar(255) DEFAULT '1st',
  `member_type` varchar(25) DEFAULT NULL,
  `unique_id` varchar(35) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL,
  `programme` int(11) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `section` int(11) DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `student_status` enum('DAY','BOARDING') NOT NULL DEFAULT 'DAY',
  `title` varchar(50) DEFAULT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `othernames` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `blood_group` varchar(25) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `user_image` varchar(255) DEFAULT 'assets/img/figure/student12.png',
  `user_type` enum('SUPER','ADMIN','STUDENT','TEACHER','PARENT','STAFF') DEFAULT 'STUDENT',
  `religion` varchar(50) DEFAULT NULL,
  `gender` varchar(25) DEFAULT NULL,
  `relation` varchar(255) DEFAULT NULL,
  `parent_id` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `mobile` varchar(25) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `changed` enum('0','1') DEFAULT '0',
  `group_array` varchar(500) DEFAULT NULL,
  `account_balance` varchar(25) DEFAULT NULL,
  `recorded_by` varchar(255) DEFAULT NULL,
  `recorded_date` datetime DEFAULT current_timestamp(),
  `modified_by` varchar(25) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL,
  `last_seen` int(11) DEFAULT NULL,
  `last_ipaddress` varchar(255) DEFAULT NULL,
  `browser_details` varchar(255) DEFAULT NULL,
  `log_session` varchar(255) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `blocked` enum('0','1') NOT NULL DEFAULT '0',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_users`
--

LOCK TABLES `_users` WRITE;
/*!40000 ALTER TABLE `_users` DISABLE KEYS */;
INSERT INTO `_users` VALUES (1,'2019/2020','1st',NULL,'OY550107770',1,NULL,NULL,0,NULL,'DAY',NULL,'Emmanuel','Hyde','Obeng','Emmanuel Hyde Obeng',NULL,'1992-03-20','assets/img/figure/student2.png','SUPER',NULL,'Male',NULL,NULL,NULL,NULL,NULL,'','$2y$10$W7GplOvBnIF3x76m9Mmel.RlRn8iXx/GEtUoYBVJ3gI7HHqo8x6J6','0',NULL,NULL,'emmallob14','2018-09-17 05:13:11',NULL,'0000-00-00 00:00:00','2019-10-01 11:24:05',1569929552,'127.0.0.1','Windows 8.1 | Chrome 77','07P1DlR5s4ArfoypI6XpgToeh6V6NDZ7PIkSfCc0gZaptpArv72','1','0','0'),(2,'2019/2020','1st',NULL,'OY550107771',1,NULL,NULL,0,NULL,'DAY',NULL,'Walter','Kwame','Drah','Walter Kwame Drah',NULL,'1990-09-22','assets/img/figure/student2.png','STAFF',NULL,'Male',NULL,NULL,NULL,NULL,NULL,'This account is for walter to enable him manage the content of all content he uploads','$2y$10$W7GplOvBnIF3x76m9Mmel.RlRn8iXx/GEtUoYBVJ3gI7HHqo8x6J6','0',NULL,NULL,'','0000-00-00 00:00:00',NULL,'0000-00-00 00:00:00','2019-10-01 11:31:23',1569929528,'127.0.0.1','Windows 8.1 | Chrome 76','lGj6fvN0e5E9Eo7cc5y20DoxkDHpxqQNcRLSUsUWP7f3Fv4vnibdQx3faQLk92','1','0','0'),(3,'2019/2020','1st','2','OY550107772',1,NULL,NULL,0,'This is my address that i want to be reached on','DAY','Prof.','Frank','OSEI','Asibu','Prof. Frank Asibu',NULL,'2000-09-24','assets/img/figure/student2.png','TEACHER',NULL,'Male',NULL,NULL,'0550107770',NULL,'emmallob1212@gmail.com','This is my account','$2y$10$AjHh88yNkAiu/WChvYcuCOUc7NssfAoW4SWgHux6cpd5Ky.NGfx7i','0',NULL,NULL,'','0000-00-00 00:00:00','MYG54087571','2019-10-23 23:56:22','2018-09-24 22:13:48',0,'::1','Windows 8.1 | Chrome 69',NULL,'1','0','0'),(4,'2019/2020','1st',NULL,'OY550107773',1,2,1,1,NULL,'DAY',NULL,'NICHOLAS','KOJO','BOADI','NICHOLAS KOJO BOADI',NULL,'1988-04-10','assets/img/figure/student2.png','ADMIN',NULL,'Male',NULL,NULL,NULL,NULL,NULL,'','$2y$10$W7GplOvBnIF3x76m9Mmel.RlRn8iXx/GEtUoYBVJ3gI7HHqo8x6J6','0',NULL,NULL,'','0000-00-00 00:00:00',NULL,'0000-00-00 00:00:00','2018-09-28 06:02:32',0,'::1','Windows 8.1 | Chrome 69','ySxJdU1oA0MbppJVQNRQwI87VIizXN3BGT5m8e6h8uMmxgQ099','1','0','0'),(5,'2019/2020','1st',NULL,'VI550107774',1,2,1,1,'P. O. Box AF 2582, Adentan','DAY',NULL,'SAMUEL','','ODURO','SAMUEL ODURO','B-','2018-09-27','assets/img/figure/student2.png','STUDENT','Hindu','Male',NULL,'MSG92854631','0546140378',NULL,'analiticainnovare@gmail.com','This is a short bio data about  me here.','$2y$10$rduN9T3oqxLZSO3VWflfTeeqgKGxYMbvNxquTzMEdOfGSCL02VU8K','0',NULL,'30','','0000-00-00 00:00:00','MYG54087571','2019-10-19 22:23:35',NULL,NULL,NULL,NULL,NULL,'1','0','0'),(6,'2019/2020','1st',NULL,'NE550107775',1,2,1,2,'','DAY',NULL,'EMMANUEL','HYDE','OBENG','EMMANUEL OBENG',NULL,'1992-03-22','assets/img/figure/student2.png','PARENT',NULL,'Male',NULL,NULL,'0215210212','0212012012','thisismyemail@gmail.com','This account is for the Super Administrator who will be responsible for managing the entire student database and information as presented by the teachers.','$2y$10$HaH3HqQOZAcSLNVIKDAseOkVdOGiS7GIbmDT6dqa8TrmkB5eENSgm','0',NULL,NULL,NULL,'2019-01-15 16:41:16','MYG54087571','2019-10-20 16:16:27','2019-01-15 16:46:35',1547599595,'127.0.0.1','Windows 8.1 | Chrome 58','3Dz7yt2nxBNLv659pAASI8yLnkGbUicYeCPtQnMwH82','1','0','0'),(12,'2019/2020','1st',NULL,'MSG9862354',1,2,2,2,'This is my address','DAY',NULL,'Gloria',NULL,'Manu','Gloria Manu','B+','2010-05-11','assets/img/figure/student3.png','STUDENT','Hindu','Female',NULL,'','233550107770',NULL,'','thank you for the bio data',NULL,'0',NULL,NULL,'MYG54087571','2019-10-19 16:33:01',NULL,'2019-10-19 16:33:01',NULL,NULL,NULL,NULL,NULL,'1','0','0'),(19,'2019/2020','1st',NULL,'MSG71235894',1,2,1,1,'Plt No F/69 Com 18','DAY',NULL,'Linda',NULL,'Kunwe','Linda Kunwe',NULL,NULL,'assets/img/figure/student3.png','PARENT',NULL,'Female',NULL,NULL,'+233550107770','','analiticainnovare@gmail.com','This is the bio that i want to add for this user',NULL,'0',NULL,NULL,'MYG54087571','2019-10-19 23:44:29','MYG54087571','2019-10-21 23:50:52',NULL,NULL,NULL,NULL,NULL,'1','0','0'),(24,'2019/2020','1st',NULL,'MSG92854631',1,2,2,2,'This is my address','DAY',NULL,'Test',NULL,'Parent','Test Parent',NULL,NULL,'assets/img/figure/student1.png','PARENT',NULL,'Male',NULL,NULL,'0212002120','0550107770','testparent@myschool.com','this is the bio data of this parent user.',NULL,'0',NULL,NULL,'MYG54087571','2019-10-20 00:06:36','MYG54087571','2019-10-20 07:04:48',NULL,NULL,NULL,NULL,NULL,'1','0','0'),(25,'2019/2020','1st',NULL,'MSG1468397',1,2,2,2,'','DAY',NULL,'Monica',NULL,'Duodu','Monica Duodu','B+','1999-06-10','assets/img/figure/student3.png','STUDENT','Christian','Female',NULL,'null','',NULL,'','',NULL,'0',NULL,NULL,'MYG54087571','2019-10-21 21:34:38','OY550107770','2019-12-08 19:38:56',NULL,NULL,NULL,NULL,NULL,'1','0','0');
/*!40000 ALTER TABLE `_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_users_activity`
--

DROP TABLE IF EXISTS `_users_activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_users_activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) DEFAULT 1,
  `user_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `affected_item_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `item_data_set` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ip_address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_recorded` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=202 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='This table will log all activities that are carried out by the user on the system.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_users_activity`
--

LOCK TABLES `_users_activity` WRITE;
/*!40000 ALTER TABLE `_users_activity` DISABLE KEYS */;
INSERT INTO `_users_activity` VALUES (1,1,'MYG54087571','VI550107774','student','Updated the student record set in the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-19 22:23:05'),(2,1,'MYG54087571','VI550107774','student','Updated the student record set in the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-19 22:23:35'),(3,1,'MYG54087571','MSG7413825','student','Added a new student record into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-19 23:24:03'),(4,1,'MYG54087571','MSG85692317','parent','Added a new parent record into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-19 23:28:36'),(5,1,'MYG54087571','MSG69738512','parent','Added a new parent record into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-19 23:29:31'),(6,1,'MYG54087571','MSG41756923','parent','Added a new parent record into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-19 23:31:20'),(7,1,'MYG54087571','MSG82459617','parent','Added a new parent record into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-19 23:33:36'),(8,1,'MYG54087571','MSG47821953','parent','Added a new parent record into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-19 23:43:24'),(9,1,'MYG54087571','MSG71235894','parent','Added a new parent record into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-19 23:44:29'),(10,1,'MYG54087571','MSG28569173','parent','Added a new parent record into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-19 23:53:52'),(11,1,'MYG54087571','MSG24568931','parent','Added a new parent record into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-19 23:56:11'),(12,1,'MYG54087571','MSG58364912','parent','Added a new parent record into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-19 23:57:28'),(13,1,'MYG54087571','MSG23479186','parent','Added a new parent record into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-19 23:58:40'),(14,1,'MYG54087571','MSG92854631','parent','Added a new parent record into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-20 00:06:36'),(15,1,'MYG54087571','3','gender','Added a new gender record into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-20 05:49:04'),(16,1,'MYG54087571','MSG71235894','parent','Updated the parent record set in the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-20 07:00:54'),(17,1,'MYG54087571','MSG71235894','parent','Updated the parent record set in the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-20 07:01:06'),(18,1,'MYG54087571','MSG71235894','parent','Updated the parent record set in the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-20 07:02:21'),(19,1,'MYG54087571','MSG71235894','parent','Updated the parent record set in the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-20 07:02:24'),(20,1,'MYG54087571','MSG92854631','parent','Updated the parent record set in the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-20 07:04:49'),(21,1,'MYG54087571','3','gender','Updated the gender record set in the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-20 16:11:04'),(22,1,'MYG54087571','4','gender','Added a new gender record into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-20 16:15:12'),(23,1,'MYG54087571','MSG71235894','parent','Updated the parent record set in the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-20 16:15:51'),(24,1,'MYG54087571','NE550107775','parent','Updated the parent record set in the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-20 16:16:27'),(25,1,'MYG54087571','5','programme','Added a new programme record into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-20 16:37:19'),(26,1,'MYG54087571','5','programme','Updated the programme record set in the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-20 20:15:27'),(27,1,'MYG54087571','6','programme','Added a new programme record into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-20 20:16:11'),(28,1,'MYG54087571','5','programme','Updated the programme record set in the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-20 20:24:13'),(29,1,'MYG54087571','3','programme','Updated the programme record set in the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-20 20:24:24'),(30,1,'MYG54087571','MSG1468397','student','Added a new student record into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-21 21:34:39'),(31,1,'MYG54087571','17','class','Added a new class record into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-21 22:31:31'),(32,1,'MYG54087571','18','class','Added a new class record into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-21 22:34:00'),(33,1,'MYG54087571','19','class','Added a new class record into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-21 22:41:13'),(34,1,'MYG54087571','4','class','Updated the class record set in the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-21 23:23:12'),(35,1,'MYG54087571','18','class','Updated the class record set in the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-21 23:23:26'),(36,1,'MYG54087571','18','class','Updated the class record set in the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-21 23:23:51'),(37,1,'MYG54087571','18','class','Updated the class record set in the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-21 23:27:45'),(38,1,'MYG54087571','MSG71235894','parent','Updated the parent record set in the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-21 23:50:52'),(39,1,'MYG54087571','27','subject','Added a new Course record into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-23 21:16:48'),(40,1,'MYG54087571','28','subject','Added a new Course record into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-23 21:18:10'),(41,1,'MYG54087571','28','subject','Updated the Course record set in the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-23 21:35:57'),(42,1,'MYG54087571','OY550107772','teacher','Updated the  record set in the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-23 22:46:55'),(43,1,'MYG54087571','OY550107772','teacher','Updated the Lecturer record set in the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-23 22:47:35'),(44,1,'MYG54087571','OY550107772','teacher','Updated the Lecturer record set in the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-23 22:47:54'),(45,1,'MYG54087571','OY550107772','teacher','Updated the Lecturer record set in the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-23 22:48:22'),(46,1,'MYG54087571','OY550107772','teacher','Updated the Teacher record set in the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-23 23:54:11'),(47,1,'MYG54087571','OY550107772','teacher','Updated the Teacher record set in the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-23 23:55:19'),(48,1,'MYG54087571','OY550107772','teacher','Updated the Teacher record set in the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-23 23:55:56'),(49,1,'MYG54087571','OY550107772','teacher','Updated the Teacher record set in the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-23 23:56:22'),(50,1,'MYG54087571',NULL,'history','Added a new user history into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-24 21:37:59'),(51,1,'MYG54087571',NULL,'history','Added a new user history into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-24 21:40:33'),(52,1,'MYG54087571','2','history','Added a new user history into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-24 22:14:46'),(53,1,'MYG54087571','8','subject','Deleted a subject from the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-25 23:40:08'),(54,1,'MYG54087571','8','subject','Deleted a subject from the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-25 23:40:58'),(55,1,'MYG54087571','28','subject','Deleted a subject from the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-25 23:42:10'),(56,1,'MYG54087571','9','subject','Deleted a subject from the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-25 23:42:31'),(57,1,'MYG54087571','3','class','Deleted a class from the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-25 23:45:45'),(58,1,'MYG54087571','3','class','Deleted a class from the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-25 23:46:07'),(59,1,'MYG54087571','3','class','Deleted a class from the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-25 23:47:08'),(60,1,'MYG54087571','OY550107772','teacher','Deleted a teacher from the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-25 23:52:37'),(61,1,'MYG54087571','MSG92854631','parent','Deleted a parent from the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-25 23:53:58'),(62,1,'MYG54087571','MSG1468397','student','Deleted a student from the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-25 23:55:53'),(63,1,'MYG54087571','7','history','Added a new user history into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-26 00:16:09'),(64,1,'MYG54087571','8','history','Added a new user history into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-26 00:17:41'),(65,1,'MYG54087571','9','history','Added a new user history into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-26 00:19:00'),(66,1,'MYG54087571','9','section','Deleted a section from the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-26 00:27:05'),(67,1,'MYG54087571','5','history','Added a new user history into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-28 22:21:32'),(68,1,'MYG54087571','6','history','Added a new user history into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-28 22:24:25'),(69,1,'MYG54087571','7','history','Added a new user history into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-28 22:24:54'),(70,1,'MYG54087571','8','history','Added a new user history into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-28 22:25:17'),(71,1,'MYG54087571','9','history','Added a new user history into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-28 22:25:33'),(72,1,'MYG54087571','10','history','Added a new user history into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-28 22:26:01'),(73,1,'MYG54087571','10','history','Added a new user history into the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-28 23:04:20'),(74,1,'MYG54087571','1','programme','Updated the Course record set in the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-28 23:10:40'),(75,1,'MYG54087571','5','department','Deleted a department from the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-28 23:12:58'),(76,1,'MYG54087571','5','department','Deleted a department from the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-28 23:13:46'),(77,1,'MYG54087571','6','department','Deleted a department from the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-28 23:14:02'),(78,1,'MYG54087571','4','department','Deleted a department from the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-28 23:14:47'),(79,1,'MYG54087571','6','department','Deleted a department from the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-28 23:15:03'),(80,1,'MYG54087571','4','books_category','Deleted a books_category from the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-28 23:19:43'),(81,1,'MYG54087571','4','books_category','Deleted a books_category from the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-28 23:20:30'),(82,1,'MYG54087571','4','books_category','Deleted a books_category from the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-28 23:20:39'),(83,1,'MYG54087571','4','books_category','Deleted a books_category from the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-28 23:21:05'),(84,1,'MYG54087571','4','books-category','Deleted a books-category from the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-28 23:27:30'),(85,1,'MYG54087571','8','books-category','Deleted a books-category from the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-28 23:38:56'),(86,1,'MYG54087571','10','books-category','Deleted a books-category from the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-28 23:39:05'),(87,1,'MYG54087571','7','books-category','Deleted a books-category from the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-29 16:05:15'),(88,1,'MYG54087571','5','books-category','Deleted a books-category from the system.','Chrome | Windows 8.1','127.0.0.1','2019-10-29 16:05:21'),(89,1,'MYG54087571','VI550107774','student-history-map','Was assigned to {{guardian::MSG92854631}} as Guardian.','Chrome | Windows 8.1','::1','2019-11-10 22:23:35'),(93,1,'MYG54087571','6','books-borrowed','Issued Books out to a Student.','Chrome | Windows 8.1','::1','2019-11-13 23:44:37'),(94,1,'MYG54087571','7','books-borrowed','Issued Books out to a Student.','Chrome | Windows 8.1','::1','2019-11-14 00:03:34'),(95,1,'MYG54087571','8','books-borrowed','Issued Books out to a Student.','Chrome | Windows 8.1','::1','2019-11-14 07:06:50'),(96,1,'MYG54087571','3','book-details','Inserted the details of the Book.','Chrome | Windows 8.1','::1','2019-11-16 22:46:54'),(97,1,'MYG54087571','4','history','Added a new user history into the system.','Chrome | Windows 8.1','::1','2019-11-16 22:48:23'),(98,1,'MYG54087571','4','book-details','Updated the details of the Book.','Chrome | Windows 8.1','::1','2019-11-16 22:53:49'),(99,1,'MYG54087571','4','book-details','Updated the details of the Book.','Chrome | Windows 8.1','::1','2019-11-16 22:54:21'),(100,1,'MYG54087571','4','book-details','Updated the details of the Book.','Chrome | Windows 8.1','::1','2019-11-16 22:54:53'),(101,1,'MYG54087571','1','history','Added a new user history into the system.','Chrome | Windows 8.1','::1','2019-12-06 17:27:33'),(102,1,'MYG54087571','1','fees-category','Deleted a fees-category from the system.','Chrome | Windows 8.1','::1','2019-12-06 17:40:47'),(103,1,'MYG54087571','2','history','Added a new user history into the system.','Chrome | Windows 8.1','::1','2019-12-06 18:07:49'),(104,1,'MYG54087571','3','history','Added a new user history into the system.','Chrome | Windows 8.1','::1','2019-12-06 18:07:59'),(105,1,'MYG54087571','4','history','Added a new user history into the system.','Chrome | Windows 8.1','::1','2019-12-06 18:08:06'),(106,1,'MYG54087571',NULL,'fees-category','Updated the fees category record on the system','Chrome | Windows 8.1','::1','2019-12-06 18:14:36'),(107,1,'MYG54087571','4','fees-category','Updated the fees category record on the system','Chrome | Windows 8.1','::1','2019-12-06 18:16:56'),(108,1,'MYG54087571','1','fees-allocation','Allocated fees to a class.','Chrome | Windows 8.1','::1','2019-12-06 20:54:23'),(109,1,'MYG54087571','2','fees-allocation','Allocated fees to a class.','Chrome | Windows 8.1','::1','2019-12-06 20:57:15'),(110,1,'MYG54087571','3','fees-allocation','Allocated fees to a class.','Chrome | Windows 8.1','::1','2019-12-06 20:57:31'),(111,1,'MYG54087571','','fees-allocation','Updated the fees allocated to a class','Chrome | Windows 8.1','::1','2019-12-06 21:09:32'),(112,1,'MYG54087571','','fees-allocation','Updated the fees allocated to a class','Chrome | Windows 8.1','::1','2019-12-06 21:10:41'),(113,1,'MYG54087571','','fees-allocation','Updated the fees allocated to a class','Chrome | Windows 8.1','::1','2019-12-06 21:11:17'),(114,1,'MYG54087571','','fees-allocation','Updated the fees allocated to a class','Chrome | Windows 8.1','::1','2019-12-06 21:13:08'),(115,1,'MYG54087571','','fees-allocation','Updated the fees allocated to a class','Chrome | Windows 8.1','::1','2019-12-06 21:28:08'),(116,1,'MYG54087571','','fees-allocation','Updated the fees allocated to a class','Chrome | Windows 8.1','::1','2019-12-06 21:29:20'),(117,1,'MYG54087571','','fees-allocation','Updated the fees allocated to a class','Chrome | Windows 8.1','::1','2019-12-06 21:29:27'),(118,1,'MYG54087571','','fees-allocation','Updated the fees allocated to a class','Chrome | Windows 8.1','::1','2019-12-06 21:30:33'),(119,1,'MYG54087571','','fees-allocation','Updated the fees allocated to a class','Chrome | Windows 8.1','::1','2019-12-06 21:32:07'),(120,1,'MYG54087571','','fees-allocation','Updated the fees allocated to a class','Chrome | Windows 8.1','::1','2019-12-06 21:32:07'),(121,1,'MYG54087571','','fees-allocation','Updated the fees allocated to a class','Chrome | Windows 8.1','::1','2019-12-06 21:32:15'),(122,1,'MYG54087571','2','fees-allocation','Updated the fees allocated to a class','Chrome | Windows 8.1','::1','2019-12-06 21:32:49'),(123,1,'MYG54087571','2','fees-allocation','Updated the fees allocated to a class','Chrome | Windows 8.1','::1','2019-12-06 21:32:49'),(124,1,'MYG54087571','3','fees-allocation','Updated the fees allocated to a class','Chrome | Windows 8.1','::1','2019-12-06 21:33:00'),(125,1,'MYG54087571','3','fees-allocation','Updated the fees allocated to a class','Chrome | Windows 8.1','::1','2019-12-06 21:33:05'),(126,1,'OY550107770','ou0EyCtZHKBPpS','fees-collection','An amount of 565 was received from <strong>student-id::VI550107774</strong> for the payment of <strong>fees-type::1</strong>. The Outstanding balance is <strong>235</strong>.','Chrome | Windows 8.1','::1','2019-12-08 06:26:15'),(127,1,'OY550107770','YCA4DqotrJ23ZR','fees-collection','An amount of 200 was received from <strong>student-id::VI550107774</strong> for the payment of <strong>fees-type::1</strong>. The Outstanding balance is <strong>35</strong>.','Chrome | Windows 8.1','::1','2019-12-08 08:27:20'),(128,1,'OY550107770','EVRUsJp7mX5x01','fees-collection','An amount of 10 was received from <strong>student-id::VI550107774</strong> for the payment of <strong>fees-type::1</strong>. The Outstanding balance is <strong>25</strong>.','Chrome | Windows 8.1','::1','2019-12-08 08:27:52'),(129,1,'OY550107770','EWXuHO7pnPReKF','fees-collection','An amount of 45 was received from <strong>student-id::VI550107774</strong> for the payment of <strong>fees-type::1</strong>. The Outstanding balance is <strong>0</strong>.','Chrome | Windows 8.1','::1','2019-12-08 18:50:30'),(130,1,'OY550107770','1','fees-allocation','Allocated fees to a class.','Chrome | Windows 8.1','::1','2019-12-08 19:12:13'),(131,1,'OY550107770','2','fees-allocation','Allocated fees to a class.','Chrome | Windows 8.1','::1','2019-12-08 19:12:13'),(132,1,'OY550107770','3','fees-allocation','Allocated fees to a class.','Chrome | Windows 8.1','::1','2019-12-08 19:12:14'),(133,1,'OY550107770','4','fees-allocation','Allocated fees to a class.','Chrome | Windows 8.1','::1','2019-12-08 19:12:55'),(134,1,'OY550107770','5','fees-allocation','Allocated fees to a class.','Chrome | Windows 8.1','::1','2019-12-08 19:12:56'),(135,1,'OY550107770','6','fees-allocation','Allocated fees to a class.','Chrome | Windows 8.1','::1','2019-12-08 19:12:56'),(136,1,'OY550107770','7','fees-allocation','Allocated fees to a class.','Chrome | Windows 8.1','::1','2019-12-08 19:13:11'),(137,1,'OY550107770','8','fees-allocation','Allocated fees to a class.','Chrome | Windows 8.1','::1','2019-12-08 19:13:11'),(138,1,'OY550107770','9','fees-allocation','Allocated fees to a class.','Chrome | Windows 8.1','::1','2019-12-08 19:13:11'),(139,1,'OY550107770','10','fees-allocation','Allocated fees to a class.','Chrome | Windows 8.1','::1','2019-12-08 19:13:11'),(140,1,'OY550107770','11','fees-allocation','Allocated fees to a class.','Chrome | Windows 8.1','::1','2019-12-08 19:13:12'),(141,1,'OY550107770','12','fees-allocation','Allocated fees to a class.','Chrome | Windows 8.1','::1','2019-12-08 19:13:12'),(142,1,'OY550107770','13','fees-allocation','Allocated fees to a class.','Chrome | Windows 8.1','::1','2019-12-08 19:25:37'),(143,1,'OY550107770','14','fees-allocation','Allocated fees to a class.','Chrome | Windows 8.1','::1','2019-12-08 19:25:37'),(144,1,'OY550107770','15','fees-allocation','Allocated fees to a class.','Chrome | Windows 8.1','::1','2019-12-08 19:25:37'),(145,1,'OY550107770','MSG1468397','student','Updated the student record set in the system.','Chrome | Windows 8.1','::1','2019-12-08 19:38:56'),(150,1,'OY550107770','1','student-fees-allocation','Allocated fees-type::1 to SAMUEL ODURO for the <strong>1st Term</strong> of <strong>2019/2020</strong> Academic Year.','Chrome | Windows 8.1','::1','2019-12-08 23:20:54'),(151,1,'OY550107770','2','student-fees-allocation','Allocated fees-type::1 to Monica Duodu for the <strong>1st Term</strong> of <strong>2019/2020</strong> Academic Year.','Chrome | Windows 8.1','::1','2019-12-08 23:20:54'),(152,1,'OY550107770','3','student-fees-allocation','Allocated fees-type::1 to SAMUEL ODURO for the <strong>1st Term</strong> of <strong>2019/2020</strong> Academic Year.','Chrome | Windows 8.1','::1','2019-12-08 23:21:13'),(153,1,'OY550107770','4','student-fees-allocation','Allocated fees-type::1 to Monica Duodu for the <strong>1st Term</strong> of <strong>2019/2020</strong> Academic Year.','Chrome | Windows 8.1','::1','2019-12-08 23:21:13'),(154,1,'OY550107770','5','student-fees-allocation','Allocated fees-type::3 to SAMUEL ODURO for the <strong>1st Term</strong> of <strong>2019/2020</strong> Academic Year.','Chrome | Windows 8.1','::1','2019-12-08 23:36:14'),(155,1,'OY550107770','6','student-fees-allocation','Allocated fees-type::3 to Monica Duodu for the <strong>1st Term</strong> of <strong>2019/2020</strong> Academic Year.','Chrome | Windows 8.1','::1','2019-12-08 23:36:14'),(156,1,'OY550107770','7','student-fees-allocation','Allocated fees-type::4 to SAMUEL ODURO for the <strong>1st Term</strong> of <strong>2019/2020</strong> Academic Year.','Chrome | Windows 8.1','::1','2019-12-08 23:36:32'),(157,1,'OY550107770','8','student-fees-allocation','Allocated fees-type::4 to Monica Duodu for the <strong>1st Term</strong> of <strong>2019/2020</strong> Academic Year.','Chrome | Windows 8.1','::1','2019-12-08 23:36:32'),(158,1,'OY550107770','XDRazjcHb1Zyv3','fees-collection','An amount of 300 was received from <strong>student-id::MSG1468397</strong> for the payment of <strong>fees-type::1</strong>. The Outstanding balance is <strong>150</strong>.','Chrome | Windows 8.1','::1','2019-12-08 23:39:49'),(159,1,'OY550107770','XAVirYToIMn73S','fees-collection','An amount of 250 was received from <strong>student-id::VI550107774</strong> for the payment of <strong>fees-type::1</strong>. The Outstanding balance is <strong>200</strong>.','Chrome | Windows 8.1','::1','2019-12-08 23:41:55'),(160,1,'OY550107770','yLClIUf1YJmtua','fees-collection','An amount of 200 was received from <strong>student-id::VI550107774</strong> for the payment of <strong>fees-type::1</strong>. The Outstanding balance is <strong>0</strong>.','Chrome | Windows 8.1','::1','2019-12-09 05:29:25'),(161,1,'OY550107770','wy18dEActi29KY','fees-collection','An amount of 150 was received from <strong>student-id::MSG1468397</strong> for the payment of <strong>fees-type::1</strong>. The Outstanding balance is <strong>0</strong>.','Chrome | Windows 8.1','::1','2020-01-17 12:03:56'),(162,1,'OY550107770','4','book-details','Updated the details of the Book.','Chrome | Windows 8.1','::1','2020-01-17 12:07:01'),(163,1,'OY550107770',NULL,'history','Updated a record on the system.','Chrome | Windows 8.1','::1','2020-01-17 12:07:01'),(164,1,'OY550107770','1','book-details','Updated the details of the Book.','Chrome | Windows 8.1','::1','2020-01-17 12:07:05'),(165,1,'OY550107770',NULL,'history','Updated a record on the system.','Chrome | Windows 8.1','::1','2020-01-17 12:07:05'),(166,1,'OY550107770','9','books-borrowed','Issued Books out to a Student.','Chrome | Windows 8.1','::1','2020-01-17 12:09:40'),(167,1,'OY550107770','1','book-details','Updated the details of the Book.','Chrome | Windows 8.1','::1','2020-01-17 12:18:59'),(168,1,'OY550107770',NULL,'history','Updated a record on the system.','Chrome | Windows 8.1','::1','2020-01-17 12:19:00'),(169,1,'OY550107770','2','book-details','Updated the details of the Book.','Chrome | Windows 8.1','::1','2020-01-17 12:19:20'),(170,1,'OY550107770',NULL,'history','Updated a record on the system.','Chrome | Windows 8.1','::1','2020-01-17 12:19:20'),(171,1,'OY550107770','1','book-details','Updated the details of the Book.','Chrome | Windows 8.1','::1','2020-01-17 12:48:46'),(172,1,'OY550107770',NULL,'history','Updated a record on the system.','Chrome | Windows 8.1','::1','2020-01-17 12:48:46'),(173,1,'OY550107770','4','hostel-room','Deleted a hostel-room from the system.','Chrome | Windows 8.1','::1','2020-01-18 07:27:41'),(174,1,'OY550107770','2','hostel-room','Deleted a hostel-room from the system.','Chrome | Windows 8.1','::1','2020-01-18 15:46:43'),(175,1,'OY550107770','YnE2VzsdyZewmA','fees-collection','An amount of 20 was received from <strong>student-id::VI550107774</strong> for the payment of <strong>fees-type::3</strong>. The Outstanding balance is <strong>0</strong>.','Chrome | Windows 8.1','::1','2020-01-18 18:25:19'),(176,1,'OY550107770','dH6FzI2WE8gvhi','fees-collection','An amount of 20 was received from <strong>student-id::VI550107774</strong> for the payment of <strong>fees-type::3</strong>. The Outstanding balance is <strong>0</strong>.','Chrome | Windows 8.1','::1','2020-01-18 18:27:04'),(177,1,'OY550107770','i5BCXjwA9h1Won','fees-collection','An amount of 20 was received from <strong>student-id::VI550107774</strong> for the payment of <strong>fees-type::3</strong>. The Outstanding balance is <strong>0</strong>.','Chrome | Windows 8.1','::1','2020-01-18 18:28:18'),(178,1,'OY550107770','eY3jpkTuygMb90','fees-collection','An amount of 20 was received from <strong>student-id::VI550107774</strong> for the payment of <strong>fees-type::3</strong>. The Outstanding balance is <strong>0</strong>.','Chrome | Windows 8.1','::1','2020-01-18 18:28:33'),(179,1,'OY550107770','i0mwrhgCG8RZJz','fees-collection','An amount of 20 was received from <strong>student-id::VI550107774</strong> for the payment of <strong>fees-type::3</strong>. The Outstanding balance is <strong>0</strong>.','Chrome | Windows 8.1','::1','2020-01-18 18:28:44'),(180,1,'OY550107770','1','student-fees-allocation','Allocated fees-type::1 to student-id::VI550107774 for the <strong>1st Term</strong> of <strong>2019/2020</strong> Academic Year.','Chrome | Windows 8.1','::1','2020-01-18 18:33:14'),(181,1,'OY550107770','nro2ZESaxh1l60','fees-collection','An amount of 150 was received from <strong>student-id::VI550107774</strong> for the payment of <strong>fees-type::1</strong>. The Outstanding balance is <strong>300</strong>.','Chrome | Windows 8.1','::1','2020-01-18 18:33:35'),(182,1,'OY550107770','XTRlaBKLMNwOQk','fees-collection','An amount of 320 was received from <strong>student-id::VI550107774</strong> for the payment of <strong>fees-type::1</strong>. The Outstanding balance is <strong>0</strong>.','Chrome | Windows 8.1','::1','2020-01-18 18:34:26'),(183,1,'OY550107770','rLVTwOzS5B7mUj','fees-collection','An amount of 10 was received from <strong>student-id::VI550107774</strong> for the payment of <strong>fees-type::1</strong>. The Outstanding balance is <strong>0</strong>.','Chrome | Windows 8.1','::1','2020-01-18 18:35:00'),(184,1,'OY550107770','2','student-fees-allocation','Allocated fees-type::1 to student-id::MSG9862354 for the <strong>1st Term</strong> of <strong>2019/2020</strong> Academic Year.','Chrome | Windows 8.1','::1','2020-01-18 18:36:29'),(185,1,'OY550107770','3','student-fees-allocation','Allocated fees-type::1 to student-id::MSG1468397 for the <strong>1st Term</strong> of <strong>2019/2020</strong> Academic Year.','Chrome | Windows 8.1','::1','2020-01-18 18:36:46'),(186,1,'OY550107770','1','book-details','Updated the details of the Book.','Chrome | Windows 10','::1','2020-09-11 21:06:38'),(187,1,'OY550107770',NULL,'history','Updated a record on the system.','Chrome | Windows 10','::1','2020-09-11 21:06:38'),(188,1,'OY550107770','4','book-details','Updated the details of the Book.','Chrome | Windows 10','::1','2020-11-13 17:40:12'),(189,1,'OY550107770',NULL,'history','Updated a record on the system.','Chrome | Windows 10','::1','2020-11-13 17:40:12'),(190,1,'OY550107770','10','books-borrowed','Issued Books out to a Student.','Chrome | Windows 10','::1','2021-01-05 22:36:15'),(191,1,'OY550107770','11','books-borrowed','Issued Books out to a Student.','Chrome | Windows 10','::1','2021-01-05 22:37:36'),(192,1,'OY550107770','CPQRrS2LjFVZew','fees-collection','An amount of 400 was received from <strong>student-id::MSG9862354</strong> for the payment of <strong>fees-type::1</strong>. The Outstanding balance is <strong>50</strong>.','Chrome | Windows 10','::1','2021-01-08 11:02:02'),(193,1,'OY550107770','qNfaR5EJokmT4p','fees-collection','An amount of 50 was received from <strong>student-id::MSG9862354</strong> for the payment of <strong>fees-type::1</strong>. The Outstanding balance is <strong>0</strong>.','Chrome | Windows 10','::1','2021-01-08 11:02:48'),(194,1,'OY550107770','l3poUCxdyiTJgG','fees-collection','An amount of 450 was received from <strong>student-id::MSG1468397</strong> for the payment of <strong>fees-type::1</strong>. The Outstanding balance is <strong>0</strong>.','Chrome | Windows 10','::1','2021-01-08 11:16:10'),(195,1,'OY550107770','4','student-fees-allocation','Allocated fees-type::1 to Test Parent for the <strong>1st Term</strong> of <strong>2019/2020</strong> Academic Year.','Chrome | Windows 10','::1','2021-01-08 11:21:01'),(196,1,'OY550107770','5','student-fees-allocation','Allocated fees-type::2 to Gloria Manu for the <strong>1st Term</strong> of <strong>2019/2020</strong> Academic Year.','Chrome | Windows 10','::1','2021-01-08 11:21:20'),(197,1,'OY550107770','6','student-fees-allocation','Allocated fees-type::2 to Test Parent for the <strong>1st Term</strong> of <strong>2019/2020</strong> Academic Year.','Chrome | Windows 10','::1','2021-01-08 11:21:21'),(198,1,'OY550107770','7','student-fees-allocation','Allocated fees-type::2 to Monica Duodu for the <strong>1st Term</strong> of <strong>2019/2020</strong> Academic Year.','Chrome | Windows 10','::1','2021-01-08 11:21:21'),(199,1,'OY550107770','8','student-fees-allocation','Allocated fees-type::1 to NICHOLAS KOJO BOADI for the <strong>1st Term</strong> of <strong>2019/2020</strong> Academic Year.','Chrome | Windows 10','::1','2021-01-08 11:21:52'),(200,1,'OY550107770','9','student-fees-allocation','Allocated fees-type::1 to EMMANUEL OBENG for the <strong>1st Term</strong> of <strong>2019/2020</strong> Academic Year.','Chrome | Windows 10','::1','2021-01-08 11:21:53'),(201,1,'OY550107770','10','student-fees-allocation','Allocated fees-type::1 to Linda Kunwe for the <strong>1st Term</strong> of <strong>2019/2020</strong> Academic Year.','Chrome | Windows 10','::1','2021-01-08 11:21:53');
/*!40000 ALTER TABLE `_users_activity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_users_history`
--

DROP TABLE IF EXISTS `_users_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_users_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) DEFAULT NULL,
  `title` varchar(500) DEFAULT NULL,
  `user_type` varchar(255) DEFAULT NULL,
  `requested_by` varchar(255) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `date_requested` date DEFAULT NULL,
  `details` text DEFAULT NULL,
  `color` varchar(25) DEFAULT NULL,
  `recorded_by` varchar(25) DEFAULT NULL,
  `date_logged` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_users_history`
--

LOCK TABLES `_users_history` WRITE;
/*!40000 ALTER TABLE `_users_history` DISABLE KEYS */;
INSERT INTO `_users_history` VALUES (1,1,'This is the test','STUDENT','VI550107774','1','2019-10-10','He wants to go to the cinema to enjoy some movies. I hope his request will be approved the next time','#bdb909','MYG54087571','2019-10-24 21:40:33','0'),(2,1,'This is the next item','TEACHER','OY550107772','2','2019-10-24','He wants to go and check up on some few things in town. He will be back pretty soon.','#431c95','MYG54087571','2019-10-24 22:14:45','0');
/*!40000 ALTER TABLE `_users_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_users_type`
--

DROP TABLE IF EXISTS `_users_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_users_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) DEFAULT 1,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_users_type`
--

LOCK TABLES `_users_type` WRITE;
/*!40000 ALTER TABLE `_users_type` DISABLE KEYS */;
INSERT INTO `_users_type` VALUES (1,1,'Part Time'),(2,1,'Full Time'),(3,1,'Attachee'),(4,1,'Cognate Members');
/*!40000 ALTER TABLE `_users_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fees_payments`
--

DROP TABLE IF EXISTS `fees_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fees_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL DEFAULT 1,
  `student_id` varchar(25) DEFAULT NULL,
  `fees_type` int(11) DEFAULT NULL,
  `amount_due` decimal(10,2) NOT NULL DEFAULT 0.00,
  `amount_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `academic_year` varchar(25) DEFAULT '2019/2020',
  `academic_term` varchar(25) DEFAULT '1st',
  `paid_status` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fees_payments`
--

LOCK TABLES `fees_payments` WRITE;
/*!40000 ALTER TABLE `fees_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `fees_payments` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-06-14  7:29:04
